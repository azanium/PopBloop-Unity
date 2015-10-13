// --------------------------------------------------------------------------------------------------------------------
// <copyright file="MmoActor.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   This <see cref="Actor" /> subclass is the peer's <see cref="Peer.CurrentOperationHandler">operation handler</see> after entering a world.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.MmoServer
{
    using System;
    using System.Collections;

    using PB.Common;
    using PB.MmoServer.Events;
    using PB.MmoServer.Operations;
    using Photon.SocketServer;
    using Photon.SocketServer.Mmo;
    using Photon.SocketServer.Mmo.Messages;
    using Photon.SocketServer.Rpc;
    using PB.MmmoServer.Operations;
    using PB.Server.Db;
    using ExitGames.Logging;
    using Photon.SocketServer.Diagnostics;

    /// <summary>
    ///   This <see cref = "Actor" /> subclass is the peer's <see cref = "Peer.CurrentOperationHandler">operation handler</see> after entering a world.
    /// </summary>
    public sealed class MmoActor : Actor, IOperationHandler
    {
        #region Constructors and Destructors

        private static readonly ILogger log = LogManager.GetCurrentClassLogger();

        #region PopBloop Properties & MemVars

        private DateTime startVisit = DateTime.Now;

        #endregion

        /// <summary>
        ///   Initializes a new instance of the <see cref = "MmoActor" /> class.
        /// </summary>
        /// <param name = "peer">
        ///   The peer State.
        /// </param>
        /// <param name = "world">
        ///   The world.
        /// </param>
        /// <param name = "interestArea">
        ///   The interestArea.
        /// </param>
        public MmoActor(PeerBase peer, IWorld world, InterestArea interestArea)
            : base(peer, world)
        {
            this.AddInterestArea(interestArea);
        }

        #endregion

        #region Public Methods

        /// <summary>
        ///   Handles operations <see cref = "CreateWorld" /> and <see cref = "EnterWorld" />.
        /// </summary>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.InvalidOperation" />.
        /// </returns>
        public static OperationResponse InvalidOperation(OperationRequest request)
        {
            string debugMessage = "InvalidOperation: " + (OperationCode)request.OperationCode;
            return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = debugMessage };
        }

        /// <summary>
        ///   This override hides the base method.
        ///   Use <see cref = "AddItem(MmoItem)" /> instead.
        /// </summary>
        /// <param name = "item">
        ///   The added item.
        /// </param>
        /// <exception cref = "ArgumentException">
        ///   Use <see cref = "AddItem(MmoItem)" /> instead.
        /// </exception>
        public new void AddItem(Item item)
        {
            throw new ArgumentException("add MmoItem instead of Item");
        }

        /// <summary>
        ///   Adds the <paramref name = "item" /> to the owned items.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <exception cref = "ArgumentException">
        ///   The item <see cref = "MmoItem.Owner" /> must be this actor.
        /// </exception>
        public void AddItem(MmoItem item)
        {
            if (item.Owner != this)
            {
                throw new ArgumentException("foreign owner forbidden");
            }

            base.AddItem(item);
        }

        /// <summary>
        ///   Handles operation <see cref = "AddInterestArea" />: Creates a new <see cref = "InterestArea" /> and optionally attaches it to an existing <see cref = "Item" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" /> or <see cref = "ReturnCode.InterestAreaAlreadyExists" />.
        ///   If the <see cref = "InterestArea" /> is supposed to be attached to an <see cref = "Item" /> error code <see cref = "ReturnCode.ItemNotFound" /> could be returned. 
        /// </returns>
        /// <remarks>
        ///   The <see cref = "InterestArea" /> is created even if error code <see cref = "ReturnCode.ItemNotFound" /> is returned.
        /// </remarks>
        public OperationResponse OperationAddInterestArea(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new AddInterestArea(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea))
            {
                return operation.GetOperationResponse((int)ReturnCode.InterestAreaAlreadyExists, "InterestAreaAlreadyExists");
            }

            interestArea = new MmoClientInterestArea(this.Peer, operation.InterestAreaId, this.World);
            this.AddInterestArea(interestArea);

            // attach interestArea to item
            Item item;
            if (operation.ItemType.HasValue && string.IsNullOrEmpty(operation.ItemId) == false)
            {
                IWorld world = this.World;

                bool actorItem = this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item);
                if (actorItem == false)
                {
                    if (world.ItemCache.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
                    {
                        return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
                    }
                }

                if (actorItem)
                {
                    // we are already in the item thread, invoke directly
                    return ItemOperationAddInterestArea(item, operation, interestArea);
                }

                // second parameter (peer) allows us to send an error event to the client (in case of an error)
                item.Fiber.Enqueue(() => this.ExecItemOperation(() => ItemOperationAddInterestArea(item, operation, interestArea), sendParameters));

                // send response later
                return null;
            }

            // free floating interestArea
            if (operation.Position != null)
            {
                lock (interestArea.SyncRoot)
                {
                    interestArea.Position = operation.Position.ToVector();
                    interestArea.ViewDistanceEnter = operation.ViewDistanceEnter.ToVector();
                    interestArea.ViewDistanceExit = operation.ViewDistanceExit.ToVector();
                    interestArea.UpdateInterestManagement();
                }
            }

            return operation.GetOperationResponse(MethodReturnValue.Ok);
        }

        /// <summary>
        ///   Handles operation <see cref = "AttachInterestArea" />: Attaches an existing <see cref = "InterestArea" /> to an existing <see cref = "Item" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" />, <see cref = "ReturnCode.InterestAreaNotFound" /> or <see cref = "ReturnCode.ItemNotFound" />.
        /// </returns>
        public OperationResponse OperationAttachInterestArea(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new AttachInterestArea(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
            }

            Item item;
            bool actorItem;
            if (false == operation.ItemType.HasValue || string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;
                actorItem = true;

                // set return vaues
                operation.ItemId = item.Id;
                operation.ItemType = item.Type;
            }
            else
            {
                IWorld world = this.World;
                actorItem = this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item);
                if (actorItem == false)
                {
                    if (world.ItemCache.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
                    {
                        return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
                    }
                }
            }

            if (actorItem)
            {
                // we are already in the item thread, invoke directly
                return this.ItemOperationAttachInterestArea(item, operation, interestArea, sendParameters);
            }

            // second parameter (peer) allows us to send an error event to the client (in case of an error)
            item.Fiber.Enqueue(
                () => this.ExecItemOperation(() => this.ItemOperationAttachInterestArea(item, operation, interestArea, sendParameters), sendParameters));

            // response is sent later
            return null;
        }

        /// <summary>
        ///   Handles operation <see cref = "DestroyItem" />: Destroys an existing <see cref = "MmoItem" />. 
        ///   The <see cref = "MmoItem.Owner" /> must be this actor instance.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" />, <see cref = "ReturnCode.ItemNotFound" /> or <see cref = "ReturnCode.ItemAccessDenied" />.
        /// </returns>
        public OperationResponse OperationDestroyItem(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new DestroyItem(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            Item item;
            bool actorItem = this.TryGetItem(operation.ItemType, operation.ItemId, out item);
            if (actorItem == false)
            {
                IWorld world = this.World;

                // search world cache just to see if item exists at all
                if (world.ItemCache.TryGetItem(operation.ItemType, operation.ItemId, out item) == false)
                {
                    return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
                }
            }

            if (actorItem)
            {
                // we are already in the item thread, invoke directly
                return this.ItemOperationDestroy(item, operation);
            }

            // second parameter (peer) allows us to send an error event to the client (in case of an error)
            // error ItemAccessDenied or ItemNotFound will be returned
            item.Fiber.Enqueue(() => this.ExecItemOperation(() => this.ItemOperationDestroy(item, operation), sendParameters));

            // operation is continued later
            return null;
        }

        /// <summary>
        ///   Handles operation <see cref = "DetachInterestArea" />: Detaches an existing <see cref = "InterestArea" /> from an <see cref = "Item" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" /> or <see cref = "ReturnCode.InterestAreaNotFound" />.
        /// </returns>
        public OperationResponse OperationDetachInterestArea(PeerBase peer, OperationRequest request)
        {
            var operation = new DetachInterestArea(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
            }

            lock (interestArea.SyncRoot)
            {
                interestArea.Detach();
            }

            return operation.GetOperationResponse(MethodReturnValue.Ok);
        }

        /// <summary>
        ///   Handles operation <see cref = "ExitWorld" />: Sends event <see cref = "WorldExited" /> to the client, disposes the actor and replaces the peer's <see cref = "Peer.CurrentOperationHandler" /> with the <see cref = "MmoPeer" /> itself.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   Always Null.
        /// </returns>
        public OperationResponse OperationExitWorld(PeerBase peer, OperationRequest request)
        {
            var operation = new Operation();
            operation.OnStart();

            this.ExitWorld();

            // don't send response
            operation.OnComplete();
            return null;
        }

        /// <summary>
        ///   Handles operation <see cref = "GetProperties" />: Sends event <see cref = "ItemProperties" /> to the client.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.ItemNotFound" />.
        /// </returns>
        public OperationResponse OperationGetProperties(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new GetProperties(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            IWorld world = this.World;
            Item item;
            bool actorItem = this.TryGetItem(operation.ItemType, operation.ItemId, out item);
            if (actorItem == false)
            {
                if (world.ItemCache.TryGetItem(operation.ItemType, operation.ItemId, out item) == false)
                {
                    return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
                }
            }

            if (actorItem)
            {
                // we are already in the item thread, invoke directly
                return this.ItemOperationGetProperties(item, operation);
            }

            // second parameter (peer) allows us to send an error event to the client (in case of an error)
            item.Fiber.Enqueue(() => this.ExecItemOperation(() => this.ItemOperationGetProperties(item, operation), sendParameters));

            // operation is continued later
            return null;
        }

        /// <summary>
        ///   Handles operation <see cref = "Move" />: Move the items and ultimately sends event <see cref = "ItemMoved" /> to other clients.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.ItemNotFound" />.
        /// </returns>
        public OperationResponse OperationMove(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new Move(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            Item item;
            if (false == operation.ItemType.HasValue || string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;

                // set return values
                operation.ItemId = item.Id;
                operation.ItemType = item.Type;
            }
            else if (this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            return this.ItemOperationMove((MmoItem)item, operation, sendParameters);
        }

        /// <summary>
        ///   Handles operation <see cref = "MoveInterestArea" />: Moves one of the actor's <see cref = "InterestArea">InterestAreas</see>.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.InterestAreaNotFound" />.
        /// </returns>
        public OperationResponse OperationMoveInterestArea(PeerBase peer, OperationRequest request)
        {
            var operation = new MoveInterestArea(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea))
            {
                lock (interestArea.SyncRoot)
                {
                    interestArea.Position = operation.Position.ToVector();
                    interestArea.UpdateInterestManagement();
                }

                // don't send response
                return null;
            }

            return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
        }

        /// <summary>
        ///   Handles operation <see cref = "RaiseGenericEvent" />: Sends event <see cref = "ItemGeneric" /> to an <see cref = "Item" /> owner or the subscribers of an <see cref = "Item" />'s <see cref = "Item.EventChannel" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.ItemNotFound" />.
        /// </returns>
        public OperationResponse OperationRaiseGenericEvent(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new RaiseGenericEvent(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            Item item;
            bool actorItem = true;
            if (false == operation.ItemType.HasValue || string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;

                // set return values
                operation.ItemType = item.Type;
                operation.ItemId = item.Id;
            }
            else if (this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
            {
                if (this.World.ItemCache.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
                {
                    return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
                }

                actorItem = false;
            }

            if (actorItem)
            {
                // we are already in the item thread, invoke directly
                return ItemOperationRaiseGenericEvent(item, operation, sendParameters);
            }

            // second parameter (peer) allows us to send an error event to the client (in case of an error)
            item.Fiber.Enqueue(() => this.ExecItemOperation(() => ItemOperationRaiseGenericEvent(item, operation, sendParameters), sendParameters));

            // operation continued later
            return null;
        }

        /// <summary>
        ///   Handles operation <see cref = "RemoveInterestArea" />: Removes one of the actor's <see cref = "InterestArea">InterestAreas</see>.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" /> or <see cref = "ReturnCode.InterestAreaNotFound" />.
        /// </returns>
        public OperationResponse OperationRemoveInterestArea(PeerBase peer, OperationRequest request)
        {
            var operation = new RemoveInterestArea(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea))
            {
                lock (interestArea.SyncRoot)
                {
                    interestArea.Detach();
                    interestArea.Dispose();
                }

                this.RemoveInterestArea(operation.InterestAreaId);
                return operation.GetOperationResponse(MethodReturnValue.Ok);
            }

            return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
        }

        /// <summary>
        ///   Handles operation <see cref = "SetProperties" />: Sets the <see cref = "Item.Properties" /> of an <see cref = "Item" /> and ultimately sends event <see cref = "ItemPropertiesSet" /> to other clients.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.ItemNotFound" />.
        /// </returns>
        public OperationResponse OperationSetProperties(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new SetProperties(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            Item item;
            if (false == operation.ItemType.HasValue || string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;

                // set return values
                operation.ItemId = item.Id;
                operation.ItemType = item.Type;
            }
            else if (this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            return this.ItemOperationSetProperties(item, operation, sendParameters);
        }

        /// <summary>
        ///   Handles operation <see cref = "SetViewDistance" />: Changes the subscribe and unsubscribe radius for an <see cref = "InterestArea" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.InterestAreaNotFound" />.
        /// </returns>
        public OperationResponse OperationSetViewDistance(PeerBase peer, OperationRequest request)
        {
            var operation = new SetViewDistance(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
            }

            lock (interestArea.SyncRoot)
            {
                interestArea.ViewDistanceEnter = operation.ViewDistanceEnter.ToVector();
                interestArea.ViewDistanceExit = operation.ViewDistanceExit.ToVector();
                interestArea.UpdateInterestManagement();
            }

            // don't send response
            return null;
        }

        /// <summary>
        ///   Handles operation <see cref = "SpawnItem" />: Creates a new <see cref = "Item" /> and optionally subscribes an <see cref = "InterestArea" /> to it.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" />, <see cref = "ReturnCode.ItemAlreadyExists" /> or <see cref = "ReturnCode.InterestAreaNotFound" />.
        /// </returns>
        public OperationResponse OperationSpawnItem(PeerBase peer, OperationRequest request)
        {
            var operation = new SpawnItem(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (operation.InterestAreaId.HasValue)
            {
                if (this.TryGetInterestArea(operation.InterestAreaId.Value, out interestArea) == false)
                {
                    return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
                }
            }
            else
            {
                interestArea = null;
            }

            IWorld world = this.World;
            var item = new MmoItem(world, operation.Position, operation.Rotation, operation.Properties, this, operation.ItemId, operation.ItemType);
            if (world.ItemCache.AddItem(item))
            {
                this.AddItem(item);
                return this.ItemOperationSpawn(item, operation, interestArea);
            }

            item.Dispose();
            return operation.GetOperationResponse((int)ReturnCode.ItemAlreadyExists, "ItemAlreadyExists");
        }

        /// <summary>
        ///   Handles operation <see cref = "SubscribeItem" />: Subscribes an existing <see cref = "InterestArea" /> to an existing <see cref = "Item" />.
        ///   The client receives event <see cref = "ItemSubscribed" /> on success.
        ///   If the submitted <see cref = "SubscribeItem.PropertiesRevision" /> is null or smaller than the item <see cref = "Item.PropertiesRevision" /> event <see cref = "ItemProperties" /> is sent to the client.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.InterestAreaNotFound" /> or <see cref = "ReturnCode.ItemNotFound" />.
        /// </returns>
        public OperationResponse OperationSubscribeItem(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new SubscribeItem(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            IWorld world = this.World;
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
            }

            Item item;
            bool actorItem = this.TryGetItem(operation.ItemType, operation.ItemId, out item);
            if (actorItem == false)
            {
                if (world.ItemCache.TryGetItem(operation.ItemType, operation.ItemId, out item) == false)
                {
                    return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
                }
            }

            if (actorItem)
            {
                // we are already in the item thread, invoke directly
                return this.ItemOperationSubscribeItem(item, operation, interestArea);
            }

            // second parameter (peer) allows us to send an error event to the client (in case of an error)
            item.Fiber.Enqueue(() => this.ExecItemOperation(() => this.ItemOperationSubscribeItem(item, operation, interestArea), sendParameters));

            // operation continues later
            return null;
        }

        /// <summary>
        ///   Handles operation <see cref = "UnsubscribeItem" />: Unsubscribes an existing <see cref = "InterestArea" /> from an existing <see cref = "Item" />.
        ///   The client receives event <see cref = "ItemUnsubscribed" /> on success.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.InterestAreaNotFound" /> or <see cref = "ReturnCode.ItemNotFound" />.
        /// </returns>
        public OperationResponse OperationUnsubscribeItem(PeerBase peer, OperationRequest request)
        {
            var operation = new UnsubscribeItem(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            InterestArea interestArea;
            if (this.TryGetInterestArea(operation.InterestAreaId, out interestArea) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.InterestAreaNotFound, "InterestAreaNotFound");
            }

            IWorld world = this.World;

            Item item;
            bool actorItem = this.TryGetItem(operation.ItemType, operation.ItemId, out item);
            if (actorItem == false)
            {
                if (world.ItemCache.TryGetItem(operation.ItemType, operation.ItemId, out item) == false)
                {
                    return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
                }
            }

            lock (interestArea.SyncRoot)
            {
                interestArea.UnsubscribeItem(item);
            }

            // don't send response
            return null;
        }

        #endregion

        #region PopBloop

        public void RemoveConcurrent()
        {
            MmoWorld world = (MmoWorld)this.World;

            string worldName = world.Name;
            foreach (Item item in this.GetOwnedItems((byte)ItemType.Avatar))
            {
                DbManager.Instance.SetConcurrent(item.Id, worldName, true);
                DbManager.Instance.SetPlayerVisit(item.Id, worldName, startVisit, false);
            }
        }

        #endregion

        #region Implemented Interfaces

        #region IOperationHandler

        /// <summary>
        ///   <see cref = "IOperationHandler" /> implementation.
        ///   Disposes the actor, stops any further operation handling and disposes the <see cref = "Peer" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        public void OnDisconnect(PeerBase peer)
        {
            PhotonCounter.SessionCount.Decrement();

            RemoveConcurrent();

            this.Dispose();
            ((Peer)peer).SetCurrentOperationHandler(null);
            peer.Dispose();
        }

        /// <summary>
        ///   <see cref = "IOperationHandler" /> implementation.
        ///   Kicks the actor from the world (event <see cref = "WorldExited" /> is sent to the client) and then disconnects the client.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        public void OnDisconnectByOtherPeer(PeerBase peer)
        {
            PhotonCounter.SessionCount.Decrement();

            RemoveConcurrent();
  
            this.ExitWorld();

            // disconnect peer after the exit world event is sent
            peer.RequestFiber.Enqueue(() => peer.RequestFiber.Enqueue(peer.Disconnect));
        }

        /// <summary>
        ///   <see cref = "IOperationHandler" /> implementation.
        ///   Dispatches the incoming <paramref name = "operationRequest" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "operationRequest">
        ///   The operation request.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> that is published with <see cref = "Peer.OnOperationRequest" /> or null.
        /// </returns>
        public OperationResponse OnOperationRequest(PeerBase peer, OperationRequest operationRequest, SendParameters sendParameters)
        {
            switch ((OperationCode)operationRequest.OperationCode)
            {
                case OperationCode.AddInterestArea:
                    {
                        return this.OperationAddInterestArea(peer, operationRequest, sendParameters);
                    }

                case OperationCode.AttachInterestArea:
                    {
                        return this.OperationAttachInterestArea(peer, operationRequest, sendParameters);
                    }

                case OperationCode.DestroyItem:
                    {
                        return this.OperationDestroyItem(peer, operationRequest, sendParameters);
                    }

                case OperationCode.DetachInterestArea:
                    {
                        return this.OperationDetachInterestArea(peer, operationRequest);
                    }

                case OperationCode.ExitWorld:
                    {
                        return this.OperationExitWorld(peer, operationRequest);
                    }

                case OperationCode.GetProperties:
                    {
                        return this.OperationGetProperties(peer, operationRequest, sendParameters);
                    }

                case OperationCode.Move:
                    {
                        return this.OperationMove(peer, operationRequest, sendParameters);
                    }

                case OperationCode.MoveInterestArea:
                    {
                        return this.OperationMoveInterestArea(peer, operationRequest);
                    }

                case OperationCode.RaiseGenericEvent:
                    {
                        return this.OperationRaiseGenericEvent(peer, operationRequest, sendParameters);
                    }

                case OperationCode.RemoveInterestArea:
                    {
                        return this.OperationRemoveInterestArea(peer, operationRequest);
                    }

                case OperationCode.SetProperties:
                    {
                        return this.OperationSetProperties(peer, operationRequest, sendParameters);
                    }

                case OperationCode.SetViewDistance:
                    {
                        return this.OperationSetViewDistance(peer, operationRequest);
                    }

                case OperationCode.SpawnItem:
                    {
                        return this.OperationSpawnItem(peer, operationRequest);
                    }

                case OperationCode.SubscribeItem:
                    {
                        return this.OperationSubscribeItem(peer, operationRequest, sendParameters);
                    }

                case OperationCode.UnsubscribeItem:
                    {
                        return this.OperationUnsubscribeItem(peer, operationRequest);
                    }

                case OperationCode.RadarSubscribe:
                    {
                        return MmoPeer.OperationRadarSubscribe(peer, operationRequest, sendParameters);
                    }

                case OperationCode.SubscribeCounter:
                    {
                        return CounterOperations.SubscribeCounter(peer, operationRequest);
                    }

                case OperationCode.UnsubscribeCounter:
                    {
                        return CounterOperations.SubscribeCounter(peer, operationRequest);
                    }

                case OperationCode.CreateWorld:
                case OperationCode.EnterWorld:
                    {
                        return InvalidOperation(operationRequest);
                    }

                #region PopBloop Operations

                case OperationCode.Animate:
                    {
                        return this.OperationAnimate(peer, operationRequest, sendParameters);
                    }

                case OperationCode.Chat:
                    {
                        return this.OperationChat(peer, operationRequest, sendParameters);
                    }

                case OperationCode.UpdateInventory:
                    {
                        return this.OperationUpdateInventory(peer, operationRequest);
                    }

                case OperationCode.FetchInventory:
                    {
                        return this.OperationFetchInventory(peer, operationRequest);
                    }

                case OperationCode.QuestJournal:
                    {
                        return this.OperationQuestJournal(peer, operationRequest);
                    }

                case OperationCode.FetchEquipments:
                    {
                        return this.OperationFetchEquipments(peer, operationRequest);
                    }

                case OperationCode.UpdateEquipment:
                    {
                        return this.OperationUpdateEquipment(peer, operationRequest);
                    }

                case OperationCode.LevelInfo:
                    {
                        return this.OperationGetLevelInfo(peer, operationRequest, sendParameters);
                    }

                case OperationCode.GameItemMove:
                    {
                        return this.OperationGameItemMove(peer, operationRequest, sendParameters);
                    }
                    
                case OperationCode.GameItemAnimate:
                    {
                        return this.OperationGameItemAnimate(peer, operationRequest, sendParameters);
                    }

                #endregion
            }

            return new OperationResponse(operationRequest.OperationCode)
                {
                   ReturnCode = (int)ReturnCode.OperationNotSupported, DebugMessage = "OperationNotSupported: " + operationRequest.OperationCode 
                };
        }

        #endregion

        #endregion

        #region Methods

        /// <summary>
        ///   The operation add interest area.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "interestArea">
        ///   The interestArea.
        /// </param>
        /// <returns>
        ///   Ok or ItemNotFound
        /// </returns>
        private static OperationResponse ItemOperationAddInterestArea(Item item, AddInterestArea operation, InterestArea interestArea)
        {
            if (item.Disposed)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            lock (interestArea.SyncRoot)
            {
                interestArea.AttachToItem(item);
                interestArea.ViewDistanceEnter = operation.ViewDistanceEnter.ToVector();
                interestArea.ViewDistanceExit = operation.ViewDistanceExit.ToVector();
                interestArea.UpdateInterestManagement();
            }

            operation.OnComplete();
            return operation.GetOperationResponse(MethodReturnValue.Ok);
        }

        /// <summary>
        ///   The operation raise generic event.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   Ok or ItemNotFound
        /// </returns>
        private static OperationResponse ItemOperationRaiseGenericEvent(Item item, RaiseGenericEvent operation, SendParameters sendParameters)
        {
            if (item.Disposed)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            var eventInstance = new ItemGeneric
                {
                   ItemId = item.Id, ItemType = item.Type, CustomEventCode = operation.CustomEventCode, EventData = operation.EventData 
                };

            var eventData = new EventData((byte)EventCode.ItemGeneric, eventInstance);
            sendParameters.Unreliable = (Reliability)operation.EventReliability == Reliability.Unreliable;
            sendParameters.ChannelId = Settings.ItemEventChannel;
            switch (operation.EventReceiver)
            {
                case (byte)EventReceiver.ItemOwner:
                    {
                        if (((IMmoItem)item).ReceiveEvent(eventData, sendParameters) == false)
                        {
                            string debugMessage = string.Format("Target item {0}/{1} could not receive event", item.Type, item.Id);
                            return operation.GetOperationResponse((int)ReturnCode.InvalidOperation, debugMessage);
                        }

                        break;
                    }

                case (byte)EventReceiver.ItemSubscriber:
                    {
                        var message = new ItemEventMessage(item, eventData, sendParameters);
                        item.EventChannel.Publish(message);
                        break;
                    }

                default:
                    {
                        return operation.GetOperationResponse((int)ReturnCode.ParameterOutOfRange, "Invalid EventReceiver " + operation.EventReceiver);
                    }
            }

            // no response
            operation.OnComplete();
            return null;
        }

        /// <summary>
        ///   The check access.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <returns>
        ///   ReturnCode.ItemNotFound, ReturnCode.ItemAccessDenied or Ok 
        /// </returns>
        private MethodReturnValue CheckAccess(Item item)
        {
            if (item.Disposed)
            {
                return MethodReturnValue.Fail((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            if (((IMmoItem)item).GrantWriteAccess(this))
            {
                return MethodReturnValue.Ok;
            }

            return MethodReturnValue.Fail((int)ReturnCode.ItemAccessDenied, "ItemAccessDenied");
        }

        /// <summary>
        ///   Executes an item operation and returns an error response in case an exception occurs.
        /// </summary>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        private void ExecItemOperation(Func<OperationResponse> operation, SendParameters sendParameters)
        {
            OperationResponse result = operation();
            if (result != null)
            {
                this.Peer.SendOperationResponse(result, sendParameters);
            }
        }

        /// <summary>
        ///   The exit world.
        /// </summary>
        private void ExitWorld()
        {
            var worldExited = new WorldExited { WorldName = ((MmoWorld)this.World).Name };
            this.Dispose();

            // set initial handler
            ((MmoPeer)this.Peer).SetCurrentOperationHandler((MmoPeer)this.Peer);

            var eventData = new EventData((byte)EventCode.WorldExited, worldExited);

            // use item channel to ensure that this event arrives in correct order with move/subscribe events
            this.Peer.SendEvent(eventData, new SendParameters { ChannelId = Settings.ItemEventChannel });
        }

        /// <summary>
        ///   The operation attach interest area.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "interestArea">
        ///   The interestArea.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   Ok or ItemNotFound
        /// </returns>
        private OperationResponse ItemOperationAttachInterestArea(
            Item item, AttachInterestArea operation, InterestArea interestArea, SendParameters sendParameters)
        {
            if (item.Disposed)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            lock (interestArea.SyncRoot)
            {
                interestArea.Detach();
                interestArea.AttachToItem(item);
                interestArea.UpdateInterestManagement();
            }

            // use item channel to ensure that this event arrives before any move or subscribe events
            OperationResponse response = operation.GetOperationResponse(MethodReturnValue.Ok);
            sendParameters.ChannelId = Settings.ItemEventChannel;
            this.Peer.SendOperationResponse(response, sendParameters);
            
            operation.OnComplete();
            return null;
        }

        /// <summary>
        ///   The destroy.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <returns>
        ///   error code ok
        /// </returns>
        private OperationResponse ItemOperationDestroy(Item item, DestroyItem operation)
        {
            MethodReturnValue result = this.CheckAccess(item);
            if (result)
            {
                item.Destroy();
                item.Dispose();
                this.RemoveItem(item);

                item.World.ItemCache.RemoveItem(item.Type, item.Id);
                var eventInstance = new ItemDestroyed { ItemId = item.Id, ItemType = item.Type };
                var eventData = new EventData((byte)EventCode.ItemDestroyed, eventInstance);
                this.Peer.SendEvent(eventData, new SendParameters { ChannelId = Settings.ItemEventChannel });

                // no response, event is sufficient
                operation.OnComplete();
                return null;
            }

            return operation.GetOperationResponse(result);
        }

        /// <summary>
        ///   The operation get properties.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <returns>
        ///   Ok or ItemNotFound
        /// </returns>
        private OperationResponse ItemOperationGetProperties(Item item, GetProperties operation)
        {
            if (item.Disposed)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            if (item.Properties != null)
            {
                if (operation.PropertiesRevision.HasValue == false || operation.PropertiesRevision.Value != item.PropertiesRevision)
                {
                    var properties = new ItemProperties
                        {
                           ItemId = item.Id, ItemType = item.Type, PropertiesRevision = item.PropertiesRevision, PropertiesSet = new Hashtable(item.Properties) 
                        };

                    var eventData = new EventData((byte)EventCode.ItemProperties, properties);
                    this.Peer.SendEvent(eventData, new SendParameters { ChannelId = Settings.ItemEventChannel });
                }
            }

            // no response sent
            operation.OnComplete();
            return null;
        }

        /// <summary>
        ///   The move operation.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   error code ok
        /// </returns>
        private OperationResponse ItemOperationMove(MmoItem item, Move operation, SendParameters sendParameters)
        {
            // should always be OK
            MethodReturnValue result = this.CheckAccess(item);
            if (result)
            {
                // save previous for event
                float[] oldPosition = item.Coordinate;
                float[] oldRotation = item.Rotation;

                // move
                item.Rotation = operation.Rotation;
                item.Move(operation.Position);

                // send event
                var eventInstance = new ItemMoved
                    {
                        ItemId = item.Id, 
                        ItemType = item.Type, 
                        OldPosition = oldPosition, 
                        Position = operation.Position, 
                        Rotation = operation.Rotation, 
                        OldRotation = oldRotation
                    };

                var eventData = new EventData((byte)EventCode.ItemMoved, eventInstance);
                sendParameters.ChannelId = Settings.ItemEventChannel;
                var message = new ItemEventMessage(item, eventData, sendParameters);
                item.EventChannel.Publish(message);

                // no response sent
                operation.OnComplete();
                return null;
            }

            return operation.GetOperationResponse(result);
        }

        /// <summary>
        ///   The set properties.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   error code ok
        /// </returns>
        private OperationResponse ItemOperationSetProperties(Item item, SetProperties operation, SendParameters sendParameters)
        {
            MethodReturnValue result = this.CheckAccess(item);
            if (result)
            {
                item.SetProperties(operation.PropertiesSet, operation.PropertiesUnset);
                var eventInstance = new ItemPropertiesSet
                    {
                        ItemId = item.Id, 
                        ItemType = item.Type, 
                        PropertiesRevision = item.PropertiesRevision, 
                        PropertiesSet = operation.PropertiesSet, 
                        PropertiesUnset = operation.PropertiesUnset
                    };

                var eventData = new EventData((byte)EventCode.ItemPropertiesSet, eventInstance);
                sendParameters.ChannelId = Settings.ItemEventChannel;
                var message = new ItemEventMessage(item, eventData, sendParameters);
                item.EventChannel.Publish(message);

                // no response sent
                operation.OnComplete();
                return null;
            }

            return operation.GetOperationResponse(result);
        }

        /// <summary>
        ///   Spawns an item.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "interestArea">
        ///   The interest Area.
        /// </param>
        /// <returns>
        ///   error code ok
        /// </returns>
        private OperationResponse ItemOperationSpawn(MmoItem item, SpawnItem operation, InterestArea interestArea)
        {
            // this should always return Ok
            MethodReturnValue result = this.CheckAccess(item);

            if (result)
            {
                item.Rotation = operation.Rotation;
                item.Spawn(operation.Position);
                ((MmoWorld)this.World).Radar.AddItem(item, operation.Position);

                if (interestArea != null)
                {
                    lock (interestArea.SyncRoot)
                    {
                        interestArea.SubscribeItem(item);
                    }
                }
            }

            operation.OnComplete();
            return operation.GetOperationResponse(result);
        }

        /// <summary>
        ///   Subscribes an item.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "interestArea">
        ///   The interestArea.
        /// </param>
        /// <returns>
        ///   Ok or ItemNotFound
        /// </returns>
        private OperationResponse ItemOperationSubscribeItem(Item item, SubscribeItem operation, InterestArea interestArea)
        {
            if (item.Disposed)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            lock (interestArea.SyncRoot)
            {
                interestArea.SubscribeItem(item);
            }

            if (operation.PropertiesRevision.HasValue == false || operation.PropertiesRevision.Value != item.PropertiesRevision)
            {
                var properties = new ItemPropertiesSet
                    {
                       ItemId = item.Id, ItemType = item.Type, PropertiesRevision = item.PropertiesRevision, PropertiesSet = new Hashtable(item.Properties) 
                    };
                var eventData = new EventData((byte)EventCode.ItemPropertiesSet, properties);
                this.Peer.SendEvent(eventData, new SendParameters { ChannelId = Settings.ItemEventChannel });
            }

            // don't send response
            operation.OnComplete();
            return null;
        }

        #endregion

        #region PopBloop OPERATIONS & EVENTS

        /// <summary>
        /// Operation Game Item Animate
        /// </summary>
        /// <param name="peer">The Mmo Peer</param>
        /// <param name="request">Operation Request</param>
        /// <param name="sendParameters">Parameters</param>
        /// <returns>Operation Response</returns>
        public OperationResponse OperationGameItemAnimate(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new GameItemAnimate(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            Item item;
            if (string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;

                // set return values
                operation.ItemId = item.Id;
            }
            else if (this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            return this.GameItemOperationAnimate((MmoItem)item, operation, sendParameters);
        }

        /// <summary>
        /// Operation Game Item Move
        /// </summary>
        /// <param name="peer">The Peer</param>
        /// <param name="request">Operation request</param>
        /// <param name="sendParameters">send parameters</param>
        /// <returns>Operation response</returns>
        public OperationResponse OperationGameItemMove(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new GameItemMove(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            operation.OnStart();
            Item item;
            if (string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;

                // set return values
                operation.ItemId = item.Id;
            }
            else if (this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            return this.GameItemOperationMove((MmoItem)item, operation, sendParameters);
        }

        /// <summary>
        /// Operation Get Level Info
        /// </summary>
        /// <param name="peer">Mmo Peer</param>
        /// <param name="request">Operation Request</param>
        /// <param name="sendParameters">Send Parameters</param>
        /// <returns>Operation Response</returns>
        public OperationResponse OperationGetLevelInfo(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new LevelInfo(peer.Protocol, request);

            if (operation.IsValid)
            {
                try
                {
                    int currentCCU, maxCCU;
                    DbManager.Instance.GetCCUPerChannel(operation.WorldName, out currentCCU, out maxCCU);

                    LevelInfoResponse response = new LevelInfoResponse()
                    {
                        WorldName = operation.WorldName,
                        CurrentCCU = currentCCU,
                        MaxCCU = maxCCU
                    };

                    return new OperationResponse(request.OperationCode, response) { ReturnCode = (int)ReturnCode.Ok, DebugMessage = "OK" };
                }
                catch (Exception ex)
                {
                    log.Fatal("OperationGetLevelInfo: " + ex.ToString());
                }

                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "Can't Get Level Info()" };
            }
            else
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = operation.GetErrorMessage() };
            }
        }

        /// <summary>
        /// Handles operation <see cref="Animate"/>: Animate the items and ultimately sends event <see cref="ItemAnimate"/> to other clients
        /// </summary>
        /// <param name="peer">The client peer</param>
        /// <param name="request">The request</param>
        /// <returns>Null or an <see cref="OperationResponse"/> with error code <see cref="ErrorCode.ItemNotFound"/></returns>
        public OperationResponse OperationAnimate(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new Animate(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            Item item;
            if (false == operation.ItemType.HasValue || string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;                
            }
            else if (this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.ItemNotFound, DebugMessage = "ItemNotFound" };
            }

            return this.ItemOperationAnimate((MmoItem)item, operation, operation.Animation, operation.AnimationAction, operation.AnimationWrap, operation.AnimationSpeed, operation.AnimationLayer, sendParameters);
        }

        /// <summary>
        /// Handles operation <see cref="Chat"/>: Chat from one item and send the chat message to another clients using event <see cref="ItemChat"/>
        /// </summary>
        /// <param name="peer">The client peer</param>
        /// <param name="request">The request</param>
        /// <returns>Null or an <see cref="OperationResponse"/>with error code <see cref="ErrorCode.ItemNotFound"/></returns>
        public OperationResponse OperationChat(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new Chat(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            Item item;
            if (false == operation.ItemType.HasValue || string.IsNullOrEmpty(operation.ItemId))
            {
                item = this.Avatar;

                operation.ItemId = item.Id;
                operation.ItemType = item.Type;
            }
            else if (this.TryGetItem(operation.ItemType.Value, operation.ItemId, out item) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.ItemNotFound, "ItemNotFound");
            }

            return this.ItemOperationChat((MmoItem)item, operation, sendParameters);
        }

        /// <summary>
        /// Fetch Inventories
        /// </summary>
        /// <param name="peer">Mmo Peer</param>
        /// <param name="request">Operation Request</param>
        /// <returns>Operation Response</returns>
        public OperationResponse OperationFetchInventory(PeerBase peer, OperationRequest request)
        {
            var operation = new FetchInventory(peer.Protocol, request);

            if (operation.IsValid)
            {
                try
                {
                    Inventories inventories = DbManager.Instance.GetInventories(operation.UserId);

                    FetchInventoryResponse response = new FetchInventoryResponse()
                    {
                        Inventories = Inventories.Serialize(inventories)
                    };

                    if (inventories != null)
                    {
                        return new OperationResponse(request.OperationCode, response) { ReturnCode = (int)ReturnCode.Ok, DebugMessage = "OK" };
                    }
                }
                catch (Exception ex)
                {
                    log.Fatal("FetchInventory: " + ex.ToString());
                }

                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "Can't FetchInventory()" };
            }
            else
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = operation.GetErrorMessage() };
            }
        }

        /// <summary>
        /// Update Inventory Item
        /// </summary>
        /// <param name="peer">The peer</param>
        /// <param name="request">The request</param>
        /// <returns>Operation Response</returns>
        public OperationResponse OperationUpdateInventory(PeerBase peer, OperationRequest request)
        {
            var operation = new UpdateInventory(peer.Protocol, request);

            if (operation.IsValid)
            {
                bool result = false;
                if (operation.GameItemIsAdding)
                {
                    log.Info("Adding Inventory: " + operation.GameItemCode + ", user: " + operation.UserId);
                    result = DbManager.Instance.SetInventoryAdd(operation.UserId, operation.GameItemCode, operation.GameItemName, operation.GameItemWeight, operation.GameItemDescription);
                }
                else
                {
                    log.Info("Removing Inventory: " + operation.GameItemCode + ", user: " + operation.UserId);
                    result = DbManager.Instance.SetInventoryRemove(operation.UserId, operation.Id, operation.GameItemCode, operation.GameItemName, operation.GameItemWeight, operation.GameItemDescription);
                }
                
                if (result)
                {
                    return operation.GetOperationResponse(MethodReturnValue.Ok);
                }
                else
                {
                    return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "Failed to Store Inventory Item" };
                }
            }
            else
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = operation.GetErrorMessage() };
            }
        }

        /// <summary>
        /// Fetch Equipments
        /// </summary>
        /// <param name="peer">Mmo Peer</param>
        /// <param name="request">Operation Request</param>
        /// <returns>Operation Response</returns>
        public OperationResponse OperationFetchEquipments(PeerBase peer, OperationRequest request)
        {
            var operation = new FetchInventory(peer.Protocol, request);

            if (operation.IsValid)
            {
                try
                {
                    Equipments equipments = DbManager.Instance.FetchEquipments(operation.UserId);

                    FetchEquipmentsResponse response = new FetchEquipmentsResponse()
                    {
                        Equipments = Equipments.Serialize(equipments)
                    };

                    if (equipments != null)
                    {
                        return new OperationResponse(request.OperationCode, response) { ReturnCode = (int)ReturnCode.Ok, DebugMessage = "OK" };
                    }
                }
                catch (Exception ex)
                {
                    log.Fatal("FetchInventory: " + ex.ToString());
                }

                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "Can't FetchInventory()" };
            }
            else
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = operation.GetErrorMessage() };
            }
        }


        /// <summary>
        /// Update Equipment data
        /// </summary>
        /// <param name="peer">Mmo Peer</param>
        /// <param name="request">Operation Request</param>
        /// <returns>Response</returns>
        public OperationResponse OperationUpdateEquipment(PeerBase peer, OperationRequest request)
        {
            var operation = new UpdateEquipment(peer.Protocol, request);

            if (operation.IsValid)
            {
                bool result = DbManager.Instance.UpdateEquipment(operation.UserId, operation.EquipmentCode, operation.EquipmentCount);

                log.InfoFormat("updateEq: userid: {0}, code: {1}, count: {2}", operation.UserId, operation.EquipmentCode, operation.EquipmentCount);

                if (result)
                {
                    return operation.GetOperationResponse(MethodReturnValue.Ok);
                }
                else
                {
                    return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "Failed to Update Equipment: " + operation.EquipmentCode };
                }
            }
            else
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = operation.GetErrorMessage() };
            }
        }

        /// <summary>
        /// Add Quest Journal 
        /// </summary>
        /// <param name="peer">The mmo peer</param>
        /// <param name="request">The request</param>
        /// <returns>Operation Response</returns>
        public OperationResponse OperationQuestJournal(PeerBase peer, OperationRequest request)
        {
            var operation = new QuestJournal(peer.Protocol, request);

            if (operation.IsValid)
            {
                DbManager.Instance.SetQuestJournal(operation.UserId, operation.QuestID, operation.IsActive);

                return operation.GetOperationResponse(MethodReturnValue.Ok);
            }
            else
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = operation.GetErrorMessage() };
            }
        }

        /// <summary>
        ///   The Game item animate operation.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   error code ok
        /// </returns>
        private OperationResponse GameItemOperationAnimate(MmoItem item, GameItemAnimate operation, SendParameters sendParameters)
        {
            // should always be OK
            MethodReturnValue result = this.CheckAccess(item);
            if (result)
            {
                // send event
                var eventInstance = new GameItemAnimateEvent
                {
                    ItemId = operation.ItemId,
                    GameItemId = operation.GameItemId,
                    Animation = operation.Animation,
                    AnimationAction = operation.AnimationAction,
                    AnimationSpeed = operation.AnimationSpeed,
                    AnimationWrap = operation.AnimationWrap
                };

                var eventData = new EventData((byte)EventCode.GameItemAnimate, eventInstance);
                sendParameters.ChannelId = Settings.ItemEventChannel;
                var message = new ItemEventMessage(item, eventData, sendParameters);
                item.EventChannel.Publish(message);

                // no response sent
                operation.OnComplete();
                return null;
            }

            return operation.GetOperationResponse(result);
        }

        /// <summary>
        ///   The Game item move operation.
        /// </summary>
        /// <param name = "item">
        ///   The mmo item.
        /// </param>
        /// <param name = "operation">
        ///   The operation.
        /// </param>
        /// <param name = "sendParameters">
        ///   The send Parameters.
        /// </param>
        /// <returns>
        ///   error code ok
        /// </returns>
        private OperationResponse GameItemOperationMove(MmoItem item, GameItemMove operation, SendParameters sendParameters)
        {
            // should always be OK
            MethodReturnValue result = this.CheckAccess(item);
            if (result)
            {
                // send event
                var eventInstance = new GameItemMoved
                {
                    ItemId = operation.ItemId,
                    GameItemId = operation.GameItemId,
                    Position = operation.Position,
                    Rotation = operation.Rotation
                };

                var eventData = new EventData((byte)EventCode.GameItemMoved, eventInstance);
                sendParameters.ChannelId = Settings.ItemEventChannel;
                var message = new ItemEventMessage(item, eventData, sendParameters);
                item.EventChannel.Publish(message);

                // no response sent
                operation.OnComplete();
                return null;
            }

            return operation.GetOperationResponse(result);
        }

        /// <summary>
        /// The Animate Operation
        /// </summary>
        /// <param name="item">The mmo item</param>
        /// <param name="operation">The operation</param>
        /// <returns>error code OK</returns>
        private OperationResponse ItemOperationAnimate(MmoItem item, Animate operation, string animation, byte action, byte animationWrap, float animationSpeed, int animationLayer, SendParameters sendParameters)
        {
            MethodReturnValue result = this.CheckAccess(item);
            if (result)
            {
                item.Animation = animation;
                item.AnimationWrap = animationWrap;
                item.AnimationSpeed = animationSpeed;

                var eventInstance = new ItemAnimate
                {
                    ItemId = item.Id,
                    ItemType = item.Type,
                    Animation = animation,
                    AnimationAction = action,
                    AnimationWrap = animationWrap,
                    AnimationSpeed = animationSpeed,
                    AnimationLayer = animationLayer
                };

                EventData eventData = new EventData((byte)EventCode.ItemAnimate, eventInstance);
                sendParameters.ChannelId = Settings.ItemEventChannel;
                var message = new ItemEventMessage(item, eventData, sendParameters);
                item.EventChannel.Publish(message);

                operation.OnComplete();

                return null;
            }

            return operation.GetOperationResponse(result);
        }

        /// <summary>
        /// The Chat Operation
        /// </summary>
        /// <param name="item">The mmo item</param>
        /// <param name="operation">The operation</param>
        /// <returns>error code OK</returns>
        private OperationResponse ItemOperationChat(MmoItem item, Chat operation, SendParameters sendParameters)
        {
            MethodReturnValue result = this.CheckAccess(item);
            if (result)
            {
                var eventInstance = new ItemChat
                {
                    ItemId = item.Id,
                    ItemType = item.Type,
                    Group = operation.Group,
                    Message = operation.Message
                };

                EventData eventData = new EventData((byte)EventCode.ItemChat, eventInstance);
                sendParameters.ChannelId = Settings.ItemEventChannel;
                var message = new ItemEventMessage(item, eventData, sendParameters);
                item.EventChannel.Publish(message);

                operation.OnComplete();

                return null;
            }

            return operation.GetOperationResponse(result);
        }

        #endregion
    }
}