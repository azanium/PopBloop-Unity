// --------------------------------------------------------------------------------------------------------------------
// <copyright file="WorldEntered.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   The dispatcher world entered.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.Client.GameStateStrategies
{
    using System;
    using System.Collections;
    using System.Collections.Generic;

    using ExitGames.Client.Photon;

    using PB.Common;

    /// <summary>
    /// The dispatcher world entered.
    /// </summary>
    [CLSCompliant(false)]
    public class WorldEntered : IGameLogicStrategy
    {
        /// <summary>
        /// The instance.
        /// </summary>
        public static readonly IGameLogicStrategy Instance = new WorldEntered();

        /// <summary>
        /// Gets State.
        /// </summary>
        public GameState State
        {
            get
            {
                return GameState.WorldEntered;
            }
        }

        #region Implemented Interfaces

        #region IGameLogicStrategy

        /// <summary>
        /// The on event receive.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        public void OnEventReceive(Game game, EventData eventData)
        {
            switch ((EventCode)eventData.Code)
            {
                case EventCode.RadarUpdate:
                    {
                        HandleEventRadarUpdate(eventData.Parameters, game);
                        return;
                    }

                case EventCode.ItemMoved:
                    {
                        HandleEventItemMoved(game, eventData.Parameters);
                        return;
                    }

                case EventCode.ItemDestroyed:
                    {
                        HandleEventItemDestroyed(game, eventData.Parameters);
                        return;
                    }

                case EventCode.ItemProperties:
                    {
                        HandleEventItemProperties(game, eventData.Parameters);
                        return;
                    }

                case EventCode.ItemPropertiesSet:
                    {
                        HandleEventItemPropertiesSet(game, eventData.Parameters);
                        return;
                    }

                case EventCode.ItemSubscribed:
                    {
                        HandleEventItemSubscribed(game, eventData.Parameters);
                        return;
                    }

                case EventCode.ItemUnsubscribed:
                    {
                        HandleEventItemUnsubscribed(game, eventData.Parameters);
                        return;
                    }

                case EventCode.WorldExited:
                    {
                        game.SetConnected();
                        return;
                    }

                case EventCode.ItemAnimate:
                    {
                        HandleEventItemAnimate(game, eventData.Parameters);
                        return;
                    }

                case EventCode.ItemChat:
                    {
                        HandleEventItemChat(game, eventData.Parameters);
                        return;
                    }

                case EventCode.GameItemMoved:
                    {
                        HandleEventGameItemMoved(game, eventData.Parameters);
                        return;
                    }

                case EventCode.GameItemAnimate:
                    {
                        HandleEventGameItemAnimate(game, eventData.Parameters);
                        return;
                    }
            }

            game.OnUnexpectedEventReceive(eventData);
        }

        /// <summary>
        /// The on operation return.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="response">
        /// The operation response.
        /// </param>
        public void OnOperationReturn(Game game, OperationResponse response)
        {
            if (response.ReturnCode == 0)
            {
                switch ((OperationCode)response.OperationCode)
                {
                    case OperationCode.RemoveInterestArea:
                    case OperationCode.AddInterestArea:
                        {
                            return;
                        }

                    case OperationCode.AttachInterestArea:
                        {
                            HandleEventInterestAreaAttached(game, response.Parameters);
                            return;
                        }

                    case OperationCode.DetachInterestArea:
                        {
                            HandleEventInterestAreaDetached(game);
                            return;
                        }

                    case OperationCode.SpawnItem:
                        {
                            HandleEventItemSpawned(game, response.Parameters);
                            return;
                        }

                    case OperationCode.RadarSubscribe:
                        {
                            return;
                        }

                    #region PopBloop Codes
    
                    case OperationCode.QuestJournal:
                        {
                            return;
                        }

                    case OperationCode.UpdateInventory:
                        {
                            // Every time we update the inventory, we should refetch the new one
                            Operations.FetchInventory(game);
                            return;
                        }

                    case OperationCode.FetchInventory:
                        {
                            HandleEventFetchInventory(game, response.Parameters);
                            return;
                        }

                    case OperationCode.UpdateEquipment:
                        {
                            Operations.FetchEquipments(game);
                            return;
                        }

                    case OperationCode.FetchEquipments:
                        {
                            HandleEventFetchEquipments(game, response.Parameters);
                            return;
                        }

                    case OperationCode.LevelInfo:
                        {
                            HandleGetLevelInfo(game, response.Parameters);
                            return;
                        }

                    #endregion
                }
            }

            game.Listener.LogDebug(game, "WorldEntered: Error with return code " + response.ReturnCode + " opcode = " + response.OperationCode);

            game.OnUnexpectedOperationError(response);
        }

        /// <summary>
        /// The on peer status callback.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="returnCode">
        /// The return code.
        /// </param>
        public void OnPeerStatusCallback(Game game, StatusCode returnCode)
        {
            switch (returnCode)
            {
                case StatusCode.Disconnect:
                case StatusCode.DisconnectByServer:
                case StatusCode.DisconnectByServerLogic:
                case StatusCode.DisconnectByServerUserLimit:
                case StatusCode.TimeoutDisconnect:
                    {
                        game.SetDisconnected(returnCode);
                        break;
                    }

                default:
                    {
                        game.DebugReturn(DebugLevel.ERROR, returnCode.ToString());
                        break;
                    }
            }
        }

        /// <summary>
        /// The on update.
        /// </summary>
        /// <param name="game">
        /// The game logic.
        /// </param>
        public void OnUpdate(Game game)
        {
            game.Peer.Service();
        }

        /// <summary>
        /// The send operation.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="operationCode">
        /// The operation code.
        /// </param>
        /// <param name="parameter">
        /// The parameter.
        /// </param>
        /// <param name="sendReliable">
        /// The send reliable.
        /// </param>
        /// <param name="channelId">
        /// The channel Id.
        /// </param>
        public void SendOperation(Game game, OperationCode operationCode, Dictionary<byte, object> parameter, bool sendReliable, byte channelId)
        {
            game.Peer.OpCustom((byte)operationCode, parameter, sendReliable, channelId);
        }

        #endregion

        #endregion

        /// <summary>
        /// The handle event item moved.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventGameItemMoved(Game game, IDictionary eventData)
        {
            var gameItemId = (string)eventData[(byte)ParameterCode.GameItemId];
            var position = (float[])eventData[(byte)ParameterCode.Position];
            float[] rotation = eventData.Contains((byte)ParameterCode.Rotation) ? (float[])eventData[(byte)ParameterCode.Rotation] : null;

            game.Listener.OnGameItemMoved(game, gameItemId, position, rotation);
        }

        /// <summary>
        /// The handle event item animate.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventGameItemAnimate(Game game, IDictionary eventData)
        {
            var gameItemId = (string)eventData[(byte)ParameterCode.GameItemId];
            string animation = (string)eventData[(byte)ParameterCode.Animation];
            byte wrap = (byte)eventData[(byte)ParameterCode.AnimationWrap];
            float speed = (float)eventData[(byte)ParameterCode.AnimationSpeed];
            byte action = (byte)eventData[(byte)ParameterCode.AnimationAction];

            AnimationAction animationAction = (AnimationAction)action;

            game.Listener.OnGameItemAnimate(game, gameItemId, animation, animationAction, wrap, speed);
        }

        /// <summary>
        /// The handle event interest area attached.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventInterestAreaAttached(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];

            game.OnCameraAttached(itemId, itemType);
        }

        /// <summary>
        /// The handle event interest area detached.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        private static void HandleEventInterestAreaDetached(Game game)
        {
            game.OnCameraDetached();
        }

        /// <summary>
        /// The handle event item destroyed.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventItemDestroyed(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];

            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                item.IsDestroyed = game.RemoveItem(item);
            }
        }

        /// <summary>
        /// The handle event item moved.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventItemMoved(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];
            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                if (item.IsMine == false)
                {
                    var position = (float[])eventData[(byte)ParameterCode.Position];
                    var oldPosition = (float[])eventData[(byte)ParameterCode.OldPosition];
                    float[] rotation = eventData.Contains((byte)ParameterCode.Rotation) ? (float[])eventData[(byte)ParameterCode.Rotation] : null;
                    float[] oldRotation = eventData.Contains((byte)ParameterCode.OldRotation) ? (float[])eventData[(byte)ParameterCode.OldRotation] : null;
                    item.SetPositions(position, oldPosition, rotation, oldRotation);
                }
            }
        }

        /// <summary>
        /// The handle event item animate
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="eventData">The event data</param>
        private static void HandleEventItemAnimate(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];
            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                if (item.IsMine == false)
                {
                    string animation = (string)eventData[(byte)ParameterCode.Animation];
                    byte wrap = (byte)eventData[(byte)ParameterCode.AnimationWrap];
                    float speed = (float)eventData[(byte)ParameterCode.AnimationSpeed];
                    int layer = (int)eventData[(byte)ParameterCode.AnimationLayer];
                    byte action = (byte)eventData[(byte)ParameterCode.AnimationAction];

                    AnimationAction animationAction = (AnimationAction)action;

                    item.SetAnimation(animation, animationAction, wrap, speed);
                }
            }
        }

        /// <summary>
        /// The handle event item chat
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="eventData">The event data</param>
        private static void HandleEventItemChat(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];
            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                if (item.IsMine == false)
                {
                    // Chat Group, not used for now
                    string[] group = (string[])eventData[(byte)ParameterCode.ChatGroup];
                    string message = (string)eventData[(byte)ParameterCode.ChatMessage];

                    game.Listener.OnReceivedChatMessage(game, item, group, message);
                }
            }
        }

        /// <summary>
        /// The handle event item properties.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventItemProperties(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];

            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                item.PropertyRevision = (int)eventData[(byte)ParameterCode.PropertiesRevision];

                if (item.IsMine == false)
                {
                    var propertiesSet = (Hashtable)eventData[(byte)ParameterCode.PropertiesSet];

                    item.SetServerAddress((string)propertiesSet[Item.PropertyKeyServerAddress]);
                    item.SetColor((int)propertiesSet[Item.PropertyKeyColor]);
                    item.SetText((string)propertiesSet[Item.PropertyKeyText]);
                    item.SetInterestAreaAttached((bool)propertiesSet[Item.PropertyKeyInterestAreaAttached]);
                    item.SetInterestAreaViewDistance(
                        (float[])propertiesSet[Item.PropertyKeyViewDistanceEnter], (float[])propertiesSet[Item.PropertyKeyViewDistanceExit]);

                    item.MakeVisibleToSubscribedInterestAreas();
                }
            }
        }

        /// <summary>
        /// The handle event item properties set.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventItemPropertiesSet(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];
            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                item.PropertyRevision = (int)eventData[(byte)ParameterCode.PropertiesRevision];

                if (item.IsMine == false)
                {
                    var propertiesSet = (Hashtable)eventData[(byte)ParameterCode.PropertiesSet];

                    if (propertiesSet.ContainsKey(Item.PropertyKeyColor))
                    {
                        item.SetColor((int)propertiesSet[Item.PropertyKeyColor]);
                    }

                    if (propertiesSet.ContainsKey(Item.PropertyKeyText))
                    {
                        item.SetText((string)propertiesSet[Item.PropertyKeyText]);
                    }

                    if (propertiesSet.ContainsKey(Item.PropertyKeyViewDistanceEnter))
                    {
                        var viewDistanceEnter = (float[])propertiesSet[Item.PropertyKeyViewDistanceEnter];
                        item.SetInterestAreaViewDistance(viewDistanceEnter, (float[])propertiesSet[Item.PropertyKeyViewDistanceExit]);
                    }

                    if (propertiesSet.ContainsKey(Item.PropertyKeyInterestAreaAttached))
                    {
                        item.SetInterestAreaAttached((bool)propertiesSet[Item.PropertyKeyInterestAreaAttached]);
                    }

                    if (propertiesSet.ContainsKey(Item.PropertyKeyServerAddress))
                    {
                        item.SetServerAddress((string)propertiesSet[Item.PropertyKeyServerAddress]);
                    }

                    if (propertiesSet.ContainsKey(Item.PropertyKeyAnimation))
                    {
                        string animation = (string)propertiesSet[Item.PropertyKeyAnimation];
                        byte wrap = (byte)propertiesSet[Item.PropertyKeyAnimationWrap];
                        float speed = (float)propertiesSet[Item.PropertyKeyAnimationSpeed];
                        int layer = (int)propertiesSet[Item.PropertyKeyAnimationLayer];
                        byte action = (byte)propertiesSet[Item.PropertyKeyAnimationAction];

                        item.SetAnimation(animation, (AnimationAction)action, wrap, speed);

                        game.Listener.OnItemAnimate(game, item, animation, (AnimationAction)action, wrap, speed, layer);
                    }
                }
            }
        }

        /// <summary>
        /// The handle event item spawned.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventItemSpawned(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];

            game.OnItemSpawned(itemType, itemId);
        }

        /// <summary>
        /// The handle event item subscribed.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventItemSubscribed(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];
            var position = (float[])eventData[(byte)ParameterCode.Position];
            var avatarName = (string)eventData[(byte)ParameterCode.AvatarName];
            var cameraId = (byte)eventData[(byte)ParameterCode.InterestAreaId];
            float[] rotation = eventData.Contains((byte)ParameterCode.Rotation) ? (float[])eventData[(byte)ParameterCode.Rotation] : null;
            var animation = (string)eventData[(byte)ParameterCode.Animation];
            var animationWrap = (byte)eventData[(byte)ParameterCode.AnimationWrap];
            var animationSpeed = (float)eventData[(byte)ParameterCode.AnimationSpeed];
            
            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                item.SetAvatarName(avatarName);
                
                if (item.IsMine)
                {
                    item.AddSubscribedInterestArea(cameraId);
                    item.AddVisibleInterestArea(cameraId);
                }
                else
                {
                    var revision = (int)eventData[(byte)ParameterCode.PropertiesRevision];
                    if (revision == item.PropertyRevision)
                    {
                        item.AddSubscribedInterestArea(cameraId);
                        item.AddVisibleInterestArea(cameraId);
                    }
                    else
                    {
                        item.AddSubscribedInterestArea(cameraId);
                        item.GetProperties();
                    }

                    item.SetPositions(position, position, rotation, rotation);
                }
            }
            else
            {
                item = new ForeignItem(itemId, itemType, game);
                item.SetPositions(position, position, rotation, rotation);
                item.SetAvatarName(avatarName);
                item.SetAnimation(animation, AnimationAction.Play, animationWrap, animationSpeed);

                game.AddItem(item);

                item.AddSubscribedInterestArea(cameraId);
                item.GetProperties();
            }
        }

        /// <summary>
        /// The handle event item unsubscribed.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        private static void HandleEventItemUnsubscribed(Game game, IDictionary eventData)
        {
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];
            var cameraId = (byte)eventData[(byte)ParameterCode.InterestAreaId];

            Item item;
            if (game.TryGetItem(itemType, itemId, out item))
            {
                if (item.RemoveSubscribedInterestArea(cameraId))
                {
                    item.RemoveVisibleInterestArea(cameraId);
                }
            }
        }

        /// <summary>
        /// The handle event radar update.
        /// </summary>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        private static void HandleEventRadarUpdate(IDictionary eventData, Game game)
        {
            var itemId = (string)eventData[(byte)ParameterCode.ItemId];
            var itemType = (byte)eventData[(byte)ParameterCode.ItemType];
            var position = (float[])eventData[(byte)ParameterCode.Position];
            game.Listener.OnRadarUpdate(itemId, itemType, position);
        }


        #region PopBloop Codes

        private static void HandleEventFetchInventory(Game game, IDictionary eventData)
        {
            byte[] inventoryBytes = (byte[])eventData[(byte)ParameterCode.Inventories];
            
            Inventories inventories = null;
            try
            {
                inventories = Inventories.Deserialize(inventoryBytes);
            }
            catch (Exception ex)
            {
                game.Listener.LogError(game, ex);
            }

            if (inventories != null)
            {
                game.OnInventoryFetched(inventories);
            }
        }

        private static void HandleEventFetchEquipments(Game game, IDictionary eventData)
        {
            byte[] equipmentsBytes = (byte[])eventData[(byte)ParameterCode.Equipments];

            Equipments equipments = null;
            try
            {
                equipments = Equipments.Deserialize(equipmentsBytes);
            }
            catch (Exception ex)
            {
                game.Listener.LogError(game, ex);
            }

            if (equipments != null)
            {
                game.OnEquipmentsFetched(equipments);
            }
        }

        private static void HandleGetLevelInfo(Game game, IDictionary eventData)
        {
            string worldName = (string)eventData[(byte)ParameterCode.WorldName];
            int maxCCU = (int)eventData[(byte)ParameterCode.MaxCCU];
            int currentCCU = (int)eventData[(byte)ParameterCode.CurrentCCU];

            game.OnGetLevelInfo(worldName, currentCCU, maxCCU);
        }

        #endregion
    }
}