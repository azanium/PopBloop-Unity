// --------------------------------------------------------------------------------------------------------------------
// <copyright file="MmoPeer.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   This <see cref="Peer" /> subclass is the client's <see cref="Peer.CurrentOperationHandler">operation handler</see> immediately after connecting.
//   It does also keep references to the optional <see cref="MmoRadar" /> and <see cref="CounterPublisher" /> subscription.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.MmoServer
{
    using System;

    using PB.Common;
    using PB.MmoServer.Operations;
    using Photon.SocketServer;
    using Photon.SocketServer.Diagnostics;
    using Photon.SocketServer.Mmo;
    using Photon.SocketServer.Mmo.Messages;
    using Photon.SocketServer.Rpc;

    using PhotonHostRuntimeInterfaces;
    using PB.Server.Db;
    using ExitGames.Logging;
    using System.IO;

    /// <summary>
    ///   This <see cref = "Peer" /> subclass is the client's <see cref = "Peer.CurrentOperationHandler">operation handler</see> immediately after connecting.
    ///   It does also keep references to the optional <see cref = "MmoRadar" /> and <see cref = "CounterPublisher" /> subscription.
    /// </summary>
    public class MmoPeer : Peer, IOperationHandler
    {
        #region Constructors and Destructors

        /// <summary>
        ///   Initializes a new instance of the <see cref = "MmoPeer" /> class.
        /// </summary>
        /// <param name = "rpcProtocol">
        ///   The rpc protocol.
        /// </param>
        /// <param name = "nativePeer">
        ///   The native peer.
        /// </param>
        [CLSCompliant(false)]
        public MmoPeer(IRpcProtocol rpcProtocol, IPhotonPeer nativePeer)
            : base(rpcProtocol, nativePeer)
        {
            // this is the operation handler before entering a world
            this.SetCurrentOperationHandler(this);

            #region PopBloop

            string configFile = Path.Combine(DbSettings.BinaryPath, "PopBloopServer.config");
            if (File.Exists(configFile))
            {
                using (StreamReader reader = new StreamReader(configFile))
                {
                    string line = reader.ReadLine();

                    if (line.Length > 1)
                    {
                        string[] config = line.Split('@');
                        string address = config[1];
                        string username = config[0];
                        if (address.Trim().Length > 1 && address.Contains(":"))
                        {
                            DbSettings.DBServerAddress = address;
                            log.Info("DBServerAddress: " + address);
                        }

                        if (username.Trim().Length > 1 && username.Contains(":"))
                        {
                            string[] credentials = username.Split(':');
                            if (credentials.Length == 2)
                            {
                                DbSettings.DBServerUsername = credentials[0];
                                DbSettings.DBServerPassword = credentials[1];
                                log.Info("DBUserName: " + credentials[0] + ", DBPassword: " + credentials[1]);
                            }
                            else
                            {
                                log.Error("No credentials found on PopBloopServer.config");
                            }
                        }
                        else
                        {
                            log.Info("Credentials Invalid: '" + username +"'");
                        }
                    }
                }
            }

            #endregion
        }

        #endregion

        #region Properties

        /// <summary>
        ///   Gets or sets the counter subscription.
        ///   Counters are subscribed with operation <see cref = "SubscribeCounter" /> and unsubscribed with <see cref = "OperationCode.UnsubscribeCounter" />.
        /// </summary>
        public IDisposable CounterSubscription { get; set; }

        /// <summary>
        ///   Gets or sets the MMO radar subscription.
        ///   The radar is subscribed with operation <see cref = "RadarSubscribe" />.
        /// </summary>
        public IDisposable MmoRadarSubscription { get; set; }

        
        private static readonly ILogger log = LogManager.GetCurrentClassLogger();

        #region PopBloop MemVars & Props

        private string defaultId = "";
        private string defaultRoom = "";
        private DateTime startVisit = DateTime.Now;

        #endregion


        #endregion

        #region Public Methods

        /// <summary>
        ///   Handles all operations that are not allowed before operation <see cref = "EnterWorld" /> is called.
        /// </summary>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with <see cref = "ReturnCode.InvalidOperation" />.
        /// </returns>
        public static OperationResponse InvalidOperation(OperationRequest request)
        {
            return new OperationResponse(request.OperationCode)
                {
                   ReturnCode = (int)ReturnCode.InvalidOperation, DebugMessage = "InvalidOperation: " + (OperationCode)request.OperationCode 
                };
        }

        /// <summary>
        ///   Expects operation <see cref = "RadarSubscribe" /> and subscribes the <paramref name = "peer" /> to the <see cref = "MmoRadar" />.
        ///   Publishes an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" /> if successful.
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
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.WorldNotFound" />.
        /// </returns>
        public static OperationResponse OperationRadarSubscribe(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var mmoPeer = (MmoPeer)peer;
            var operation = new RadarSubscribe(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            if (mmoPeer.MmoRadarSubscription != null)
            {
                mmoPeer.MmoRadarSubscription.Dispose();
                mmoPeer.MmoRadarSubscription = null;
            }

            MmoWorld world;
            if (MmoWorldCache.Instance.TryGet(operation.WorldName, out world) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.WorldNotFound, "WorldNotFound");
            }

            mmoPeer.MmoRadarSubscription = world.Radar.Channel.Subscribe(mmoPeer.RequestFiber, m => RadarChannel_OnItemEventMessage(peer, m));

            // set return values
            var responseObject = new RadarSubscribeResponse
                {
                    BottomRightCorner = world.Area.Max.ToFloatArray(2), 
                    TopLeftCorner = world.Area.Min.ToFloatArray(2), 
                    TileDimensions = world.TileDimensions.ToFloatArray(2), 
                    WorldName = world.Name
                };

            // send response before sending radar content
            var response = new OperationResponse(request.OperationCode, responseObject);
            peer.SendOperationResponse(response, sendParameters);

            // send complete radar content to client
            world.Radar.SendContentToPeer(mmoPeer);

            // response already sent
            return null;
        }

        /// <summary>
        ///   Expects operation <see cref = "CreateWorld" /> and adds a new <see cref = "MmoWorld" /> to the <see cref = "MmoWorldCache" />.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "request">
        ///   The request.
        /// </param>
        /// <returns>
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" /> or <see cref = "ReturnCode.WorldAlreadyExists" />.
        /// </returns>
        public OperationResponse OperationCreateWorld(PeerBase peer, OperationRequest request)
        {
            log.Info("Create World");
            var operation = new CreateWorld(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            MmoWorld world;
            MethodReturnValue result = MmoWorldCache.Instance.TryCreate(
                operation.WorldName, operation.TopLeftCorner.ToVector(), operation.BottomRightCorner.ToVector(), operation.TileDimensions.ToVector(), out world)
                                           ? MethodReturnValue.Ok
                                           : MethodReturnValue.Fail((int)ReturnCode.WorldAlreadyExists, "WorldAlreadyExists");

            return operation.GetOperationResponse(result);
        }

        /// <summary>
        ///   Expects operation <see cref = "EnterWorld" /> and creates a new <see cref = "MmoActor" /> with a new <see cref = "MmoItem" /> as avatar and a new <see cref = "MmoClientInterestArea" />. 
        ///   The <see cref = "MmoActor" /> becomes the new <see cref = "Peer.CurrentOperationHandler">operation handler</see>.
        ///   If another <see cref = "MmoActor" /> with the same name exists he is disconnected.
        ///   An <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.Ok" /> is published on success.
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
        ///   Null or an <see cref = "OperationResponse" /> with error code <see cref = "ReturnCode.WorldNotFound" />.
        /// </returns>
        public OperationResponse OperationEnterWorld(PeerBase peer, OperationRequest request, SendParameters sendParameters)
        {
            var operation = new EnterWorld(peer.Protocol, request);
            if (!operation.IsValid)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            MmoWorld world;
            if (MmoWorldCache.Instance.TryGet(operation.WorldName, out world) == false)
            {
                return operation.GetOperationResponse((int)ReturnCode.WorldNotFound, "WorldNotFound");
            }

            var interestArea = new MmoClientInterestArea(peer, operation.InterestAreaId, world)
                {
                   ViewDistanceEnter = operation.ViewDistanceEnter.ToVector(), ViewDistanceExit = operation.ViewDistanceExit.ToVector() 
                };

            var actor = new MmoActor(peer, world, interestArea);
            var avatar = new MmoItem(world, operation.Position, operation.Rotation, operation.Properties, actor, operation.Username, (byte)ItemType.Avatar);

            while (world.ItemCache.AddItem(avatar) == false)
            {
                Item otherAvatarItem;
                if (world.ItemCache.TryGetItem(avatar.Type, avatar.Id, out otherAvatarItem))
                {
                    avatar.Dispose();
                    actor.Dispose();
                    interestArea.Dispose();

                    ((Peer)((MmoItem)otherAvatarItem).Owner.Peer).DisconnectByOtherPeer(this, request, sendParameters);

                    // request continued later, no response here
                    return null;
                }
            }

            #region PopBloop

            avatar.AvatarName = operation.AvatarName;
            log.InfoFormat("View Distance {0}, {1}", operation.ViewDistanceEnter[0], operation.ViewDistanceEnter[1]);

            DbManager.Instance.RecordPlayerStats(avatar.Id, operation.WorldName);
            DbManager.Instance.RecordRoomStats(avatar.Id, operation.WorldName);
            DbManager.Instance.SetConcurrent(avatar.Id, operation.WorldName, false);

            #endregion

            // init avatar
            actor.AddItem(avatar);
            actor.Avatar = avatar;

            ((Peer)peer).SetCurrentOperationHandler(actor);

            // set return values
            var responseObject = new EnterWorldResponse
                {
                    BottomRightCorner = world.Area.Max.ToFloatArray(2), 
                    TopLeftCorner = world.Area.Min.ToFloatArray(2), 
                    TileDimensions = world.TileDimensions.ToFloatArray(2), 
                    WorldName = world.Name
                };

            // send response; use item channel to ensure that this event arrives before any move or subscribe events
            var response = new OperationResponse(request.OperationCode, responseObject);
            sendParameters.ChannelId = Settings.ItemEventChannel;
            peer.SendOperationResponse(response, sendParameters);

            lock (interestArea.SyncRoot)
            {
                interestArea.AttachToItem(avatar);
                interestArea.UpdateInterestManagement();
            }

            avatar.Spawn(operation.Position);
            world.Radar.AddItem(avatar, operation.Position);

            PhotonCounter.SessionCount.Increment();

            // response already sent
            return null;
        }

        #endregion

        #region Implemented Interfaces

        #region IOperationHandler

        /// <summary>
        ///   <see cref = "IOperationHandler" /> implementation.
        ///   Stops any further operation handling and disposes the peer's resources.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        public void OnDisconnect(PeerBase peer)
        {
            DbManager.Instance.SetConcurrent(defaultId, defaultRoom, true);
            DbManager.Instance.SetPlayerVisit(defaultId, defaultRoom, startVisit, true);

            this.SetCurrentOperationHandler(null);
            this.Dispose();
        }

        /// <summary>
        ///   <see cref = "IOperationHandler" /> implementation.
        ///   Disconnects the peer.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        public void OnDisconnectByOtherPeer(PeerBase peer)
        {
            DbManager.Instance.SetConcurrent(defaultId, defaultRoom, true);
            DbManager.Instance.SetPlayerVisit(defaultId, defaultRoom, startVisit, true);

            // disconnect after any queued events are sent
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
                case OperationCode.CreateWorld:
                    {
                        return this.OperationCreateWorld(peer, operationRequest);
                    }

                case OperationCode.EnterWorld:
                    {
                        return this.OperationEnterWorld(peer, operationRequest, sendParameters);
                    }

                case OperationCode.RadarSubscribe:
                    {
                        return OperationRadarSubscribe(peer, operationRequest, sendParameters);
                    }

                case OperationCode.SubscribeCounter:
                    {
                        return CounterOperations.SubscribeCounter(peer, operationRequest);
                    }

                case OperationCode.UnsubscribeCounter:
                    {
                        return CounterOperations.UnsubscribeCounter(peer, operationRequest);
                    }

                case OperationCode.AddInterestArea:
                case OperationCode.AttachInterestArea:
                case OperationCode.DestroyItem:
                case OperationCode.DetachInterestArea:
                case OperationCode.ExitWorld:
                case OperationCode.GetProperties:
                case OperationCode.Move:
                case OperationCode.MoveInterestArea:
                case OperationCode.RaiseGenericEvent:
                case OperationCode.RemoveInterestArea:
                case OperationCode.SetProperties:
                case OperationCode.SetViewDistance:
                case OperationCode.SpawnItem:
                case OperationCode.SubscribeItem:
                case OperationCode.UnsubscribeItem:
                    {
                        return InvalidOperation(operationRequest);
                    }


                #region PopBloop

                case OperationCode.Login:
                    {
                        return this.OperationLogin(peer, operationRequest);
                    }
               
                case OperationCode.Authenticate:
                    {
                        return this.OperationAuthenticate(peer, operationRequest);
                    }

                case OperationCode.LoadWorld:
                    {
                        return this.OperationLoadWorld(peer, operationRequest);
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
        ///   Diposes the <see cref = "MmoRadarSubscription" /> and the <see cref = "CounterSubscription" />.
        /// </summary>
        /// <param name = "disposing">
        ///   The disposing.
        /// </param>
        protected override void Dispose(bool disposing)
        {
            if (disposing)
            {
                if (this.MmoRadarSubscription != null)
                {
                    this.MmoRadarSubscription.Dispose();
                    this.MmoRadarSubscription = null;
                }

                if (this.CounterSubscription != null)
                {
                    this.CounterSubscription.Dispose();
                    this.CounterSubscription = null;
                }
            }

            base.Dispose(disposing);
        }

        /// <summary>
        ///   Sends the event to the client.
        /// </summary>
        /// <param name = "peer">
        ///   The client peer.
        /// </param>
        /// <param name = "message">
        ///   The message.
        /// </param>
        private static void RadarChannel_OnItemEventMessage(PeerBase peer, ItemEventMessage message)
        {
            // already in right fiber, we would use peer.SendEvent otherwise
            peer.SendEvent(message.EventData, message.SendParameters);
        }

        #endregion

        #region PopBloop Operations

        protected AuthenticateResponse GetAuthResponse(string username, string userId, string avatarName)
        {
            // Get the inventories
            Inventories inventories = DbManager.Instance.GetInventories(userId);

            // Get the equipments
            Equipments equipments = DbManager.Instance.FetchEquipments(userId);

            // Get the quest history journal
            int[] questJournal = DbManager.Instance.GetQuestJournal(userId);

            // Get the quest active history
            int[] questActive = DbManager.Instance.GetQuestActive(userId);
            byte[] eqBytes = Equipments.Serialize(equipments);

            AuthenticateResponse response = new AuthenticateResponse()
            {
                PlayerId = userId,
                PlayerUsername = username,
                AvatarName = avatarName,
                Inventories = Inventories.Serialize(inventories),
                Equipments = Equipments.Serialize(equipments),
                QuestJournal = questJournal,
                QuestActive = questActive
            };

            return response;
        }

        public OperationResponse OperationLogin(PeerBase peer, OperationRequest request)
        {
            var operation = new Login(peer.Protocol, request);
            
            log.Info("Logging In: Username:" + operation.Username +", Password: " + operation.Password);

            if (operation.IsValid)
            {
                try
                {
                    var currentSession = DbManager.Instance.UserValid(operation.Username, operation.Password);

                    if (currentSession == null)
                    {
                        log.Info("Auth: User: " + operation.Username + " with password: " + operation.Password + "("+DbManager.Instance.GetMd5Hash(operation.Password) + ") does not exists");
                        return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "InvalidToken" };
                    }

                    startVisit = DateTime.Now;
                    defaultRoom = "Loading";
                    defaultId = currentSession.UserId.ToString();
                    DbManager.Instance.SetConcurrent(defaultId, defaultRoom, false);

                    AuthenticateResponse response = GetAuthResponse(currentSession.Username, currentSession.UserId.ToString(), currentSession.Avatarname);

                    if (response.PlayerId == null || response.PlayerId == "")
                    {
                        return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "InvalidToken" };
                    }
                    else
                    {
                        return new OperationResponse(request.OperationCode, response) { ReturnCode = (int)ReturnCode.Ok, DebugMessage = "Authenticated" };
                    }
                }
                catch (Exception ex)
                {
                    log.Info("Auth Exception: " + ex.ToString());
                    return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidToken, DebugMessage = operation.GetErrorMessage() };
                }
            }
            else
            {
                log.Info("Auth: Invalid Operation");
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = operation.GetErrorMessage() };
            }
        }

        public OperationResponse OperationAuthenticate(PeerBase peer, OperationRequest request)
        {
            var operation = new Authenticate(peer.Protocol, request);
            
            log.Info("Authenticating...: " + operation.PlayerToken);
            
            if (operation.IsValid)
            {
                try
                {
                    var currentSession = DbManager.Instance.GetUserBySession(operation.PlayerToken);

                    startVisit = DateTime.Now;
                    defaultId = currentSession.UserId.ToString();
                    defaultRoom = "Loading";
                    DbManager.Instance.SetConcurrent(defaultId, defaultRoom, false);

                    AuthenticateResponse response = GetAuthResponse(currentSession.Username, currentSession.UserId.ToString(), currentSession.Avatarname);
                    
                    if (response.PlayerId == null || response.PlayerId == "")
                    {
                        return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = "InvalidToken" };
                    }
                    else
                    {
                        return new OperationResponse(request.OperationCode, response) { ReturnCode = (int)ReturnCode.Ok, DebugMessage = "Authenticated" };
                    }
                }
                catch (Exception ex)
                {
                    log.Info("Auth Exception: " + ex.ToString());
                    return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidToken, DebugMessage = operation.GetErrorMessage() };
                }
            }
            else
            {
                log.Info("Auth: Invalid Operation");
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = operation.GetErrorMessage() };
            }
        }

        /// <summary>
        /// Expect operation <see cref="LoadWorld"/> and build Level from database, serialize it as Protobuf/JSON then send it back to client
        /// </summary>
        /// <param name="peer">The mmo peer</param>
        /// <param name="request">The request</param>
        /// <returns>Operation Response</returns>
        public OperationResponse OperationLoadWorld(PeerBase peer, OperationRequest request)
        {
            var operation = new LoadWorld(peer.Protocol, request);

            if (operation.IsValid == false)
            {
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.InvalidOperationParameter, DebugMessage = operation.GetErrorMessage() };
            }

            try
            {
                string worldName = operation.PrivateRoom ? "PrivateRoom" : operation.WorldName;
                
                Level level = DbManager.Instance.GetLevel(worldName);
                
                byte[] buffer = LevelBuilder.Serialize(level);

                log.InfoFormat("Level {0}: Size ({1}, {2}), Tile ({3}, {4}), Entities Count: {5}, Audio: {6}", worldName, level.WorldSize[0], level.WorldSize[1], level.InterestArea[0], level.InterestArea[1], level.Entities.Count,
                    level.Audio);
                var response = new LoadWorldResponse()
                {
                    WorldLevel = buffer
                };

                return new OperationResponse(request.OperationCode, response) { ReturnCode = (int)ReturnCode.Ok, DebugMessage = "OperationLoadWorld" };
            }
            catch (Exception ex)
            {
                log.Info("LoadWorld Exception: " + ex.ToString());
                return new OperationResponse(request.OperationCode) { ReturnCode = (int)ReturnCode.Fatal, DebugMessage = operation.GetErrorMessage() };
            }
        }

        #endregion
    }
}