// --------------------------------------------------------------------------------------------------------------------
// <copyright file="Operations.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   The operations.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.Client
{
    using System;
    using System.Collections;
    using System.Collections.Generic;

    using PB.Common;

    /// <summary>
    /// The operations.
    /// </summary>
    [CLSCompliant(false)]
    public static class Operations
    {
        /// <summary>
        /// The add interest area.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="cameraId">
        /// The camera id.
        /// </param>
        /// <param name="position">
        /// The position.
        /// </param>
        /// <param name="viewDistanceEnter">
        /// The view distance enter.
        /// </param>
        /// <param name="viewDistanceExit">
        /// The view distance exit.
        /// </param>
        public static void AddInterestArea(Game game, byte cameraId, float[] position, float[] viewDistanceEnter, float[] viewDistanceExit)
        {
            var data = new Dictionary<byte, object>
                {
                    { (byte)ParameterCode.InterestAreaId, cameraId }, 
                    { (byte)ParameterCode.ViewDistanceEnter, viewDistanceEnter }, 
                    { (byte)ParameterCode.ViewDistanceExit, viewDistanceExit }, 
                    { (byte)ParameterCode.Position, position }
                };

            game.SendOperation(OperationCode.AddInterestArea, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The attach camera.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        public static void AttachInterestArea(Game game, string itemId, byte? itemType)
        {
            var data = new Dictionary<byte, object>();

            if (!string.IsNullOrEmpty(itemId))
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            game.SendOperation(OperationCode.AttachInterestArea, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The counter subscribe.
        /// </summary>
        /// <param name="peer">
        /// The photon peer.
        /// </param>
        /// <param name="receiveInterval">
        /// The receive interval.
        /// </param>
        public static void CounterSubscribe(PhotonPeer peer, int receiveInterval)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.CounterReceiveInterval, receiveInterval } };
            peer.OpCustom((byte)OperationCode.SubscribeCounter, data, true, Settings.DiagnosticsChannel);
        }

        /// <summary>
        /// The create world.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="worldName">
        /// The world name.
        /// </param>
        /// <param name="topLeftCorner">
        /// The top left corner.
        /// </param>
        /// <param name="bottomRightCorner">
        /// The bottom right corner.
        /// </param>
        /// <param name="tileDimensions">
        /// The tile dimensions.
        /// </param>
        public static void CreateWorld(Game game, string worldName, float[] topLeftCorner, float[] bottomRightCorner, float[] tileDimensions)
        {
            var data = new Dictionary<byte, object>
                {
                    { (byte)ParameterCode.WorldName, worldName }, 
                    { (byte)ParameterCode.TopLeftCorner, topLeftCorner }, 
                    { (byte)ParameterCode.BottomRightCorner, bottomRightCorner }, 
                    { (byte)ParameterCode.TileDimensions, tileDimensions }
                };
            game.SendOperation(OperationCode.CreateWorld, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// The destroy item.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        public static void DestroyItem(Game game, string itemId, byte itemType)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.ItemId, itemId }, { (byte)ParameterCode.ItemType, itemType } };
            game.SendOperation(OperationCode.DestroyItem, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The detach camera.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        public static void DetachInterestArea(Game game)
        {
            game.SendOperation(OperationCode.DetachInterestArea, new Dictionary<byte, object>(), true, Settings.ItemChannel);
        }

        /// <summary>
        /// The enter world.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="worldName">
        /// The world name.
        /// </param>
        /// <param name="username">
        /// The username.
        /// </param>
        /// <param name="properties">
        /// The properties.
        /// </param>
        /// <param name="position">
        /// The position.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <param name="viewDistanceEnter">
        /// The view Distance Enter.
        /// </param>
        /// <param name="viewDistanceExit">
        /// The view Distance Exit.
        /// </param>
        public static void EnterWorld(
            Game game, string worldName, string id, string avatarName, Hashtable properties, float[] position, float[] rotation, float[] viewDistanceEnter, float[] viewDistanceExit)
        {
            var data = new Dictionary<byte, object>
                {
                    { (byte)ParameterCode.WorldName, worldName }, 
                    { (byte)ParameterCode.Username, id }, 
                    { (byte)ParameterCode.AvatarName, avatarName },
                    { (byte)ParameterCode.Position, position }, 
                    { (byte)ParameterCode.ViewDistanceEnter, viewDistanceEnter }, 
                    { (byte)ParameterCode.ViewDistanceExit, viewDistanceExit }
                };

            if (properties != null)
            {
                data.Add((byte)ParameterCode.Properties, properties);
            }

            if (rotation != null)
            {
                data.Add((byte)ParameterCode.Rotation, rotation);
            }

            game.SendOperation(OperationCode.EnterWorld, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// The exit world.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        public static void ExitWorld(Game game)
        {
            game.SendOperation(OperationCode.ExitWorld, new Dictionary<byte, object>(), true, Settings.OperationChannel);
        }

        /// <summary>
        /// The get properties.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="knownRevision">
        /// The known revision.
        /// </param>
        public static void GetProperties(Game game, string itemId, byte itemType, int? knownRevision)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.ItemId, itemId }, { (byte)ParameterCode.ItemType, itemType } };
            if (knownRevision.HasValue)
            {
                data.Add((byte)ParameterCode.PropertiesRevision, knownRevision.Value);
            }

            game.SendOperation(OperationCode.GetProperties, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The move operation.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="position">
        /// The position.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <param name="sendReliable">
        /// The send Reliable.
        /// </param>
        public static void Move(Game game, string itemId, byte? itemType, float[] position, float[] rotation, bool sendReliable)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.Position, position } };
            if (itemId != null)
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            if (rotation != null)
            {
                data.Add((byte)ParameterCode.Rotation, rotation);
            }

            game.SendOperation(OperationCode.Move, data, sendReliable, Settings.ItemChannel);
        }

        /// <summary>
        /// The move camera.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="cameraId">
        /// The camera id.
        /// </param>
        /// <param name="position">
        /// The position.
        /// </param>
        public static void MoveInterestArea(Game game, byte cameraId, float[] position)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.InterestAreaId, cameraId }, { (byte)ParameterCode.Position, position } };

            game.SendOperation(OperationCode.MoveInterestArea, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The radar subscribe.
        /// </summary>
        /// <param name="peer">
        /// The photon peer.
        /// </param>
        /// <param name="worldName">
        /// The world Name.
        /// </param>
        public static void RadarSubscribe(PhotonPeer peer, string worldName)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.WorldName, worldName } };
            peer.OpCustom((byte)OperationCode.RadarSubscribe, data, true, Settings.RadarChannel);
        }

        /// <summary>
        /// The raise generic event.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="customEventCode">
        /// The custom event code.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        /// <param name="eventReliability">
        /// The event reliability.
        /// </param>
        /// <param name="eventReceiver">
        /// The event receiver.
        /// </param>
        public static void RaiseGenericEvent(
            Game game, string itemId, byte? itemType, byte customEventCode, object eventData, byte eventReliability, EventReceiver eventReceiver)
        {
            var data = new Dictionary<byte, object>
                {
                    { (byte)ParameterCode.CustomEventCode, customEventCode }, 
                    { (byte)ParameterCode.EventReliability, eventReliability }, 
                    { (byte)ParameterCode.EventReceiver, (byte)eventReceiver }
                };

            if (eventData != null)
            {
                data.Add((byte)ParameterCode.EventData, eventData);
            }

            if (itemId != null)
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            game.SendOperation(OperationCode.RaiseGenericEvent, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The remove interest area.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="cameraId">
        /// The camera id.
        /// </param>
        public static void RemoveInterestArea(Game game, byte cameraId)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.InterestAreaId, cameraId } };

            game.SendOperation(OperationCode.RemoveInterestArea, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The set properties.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="propertiesSet">
        /// The properties set.
        /// </param>
        /// <param name="propertiesUnset">
        /// The properties unset.
        /// </param>
        /// <param name="sendReliable">
        /// The send Reliable.
        /// </param>
        public static void SetProperties(Game game, string itemId, byte? itemType, Hashtable propertiesSet, ArrayList propertiesUnset, bool sendReliable)
        {
            var data = new Dictionary<byte, object>();
            if (propertiesSet != null)
            {
                data.Add((byte)ParameterCode.PropertiesSet, propertiesSet);
            }

            if (propertiesUnset != null)
            {
                data.Add((byte)ParameterCode.PropertiesUnset, propertiesUnset);
            }

            if (itemId != null)
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            game.SendOperation(OperationCode.SetProperties, data, sendReliable, Settings.ItemChannel);
        }

        /// <summary>
        /// The set view distance.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="viewDistanceEnter">
        /// The view Distance Enter.
        /// </param>
        /// <param name="viewDistanceExit">
        /// The view Distance Exit.
        /// </param>
        public static void SetViewDistance(Game game, float[] viewDistanceEnter, float[] viewDistanceExit)
        {
            var data = new Dictionary<byte, object>
                {
                    { (byte)ParameterCode.ViewDistanceEnter, viewDistanceEnter }, { (byte)ParameterCode.ViewDistanceExit, viewDistanceExit } 
                };
            game.SendOperation(OperationCode.SetViewDistance, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The spawn item.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="position">
        /// The position.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <param name="properties">
        /// The properties.
        /// </param>
        /// <param name="subscribe">
        /// The subscribe.
        /// </param>
        public static void SpawnItem(Game game, string itemId, byte itemType, float[] position, float[] rotation, Hashtable properties, bool subscribe)
        {
            var data = new Dictionary<byte, object>
                {
                    { (byte)ParameterCode.Position, position }, 
                    { (byte)ParameterCode.ItemId, itemId }, 
                    { (byte)ParameterCode.ItemType, itemType }, 
                    { (byte)ParameterCode.Subscribe, subscribe }
                };
            if (properties != null)
            {
                data.Add((byte)ParameterCode.Properties, properties);
            }

            if (rotation != null)
            {
                data.Add((byte)ParameterCode.Rotation, rotation);
            }

            game.SendOperation(OperationCode.SpawnItem, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The subscribe item.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="propertiesRevision">
        /// The properties revision.
        /// </param>
        public static void SubscribeItem(Game game, string itemId, byte itemType, int? propertiesRevision)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.ItemId, itemId }, { (byte)ParameterCode.ItemType, itemType } };
            if (propertiesRevision.HasValue)
            {
                data.Add((byte)ParameterCode.PropertiesRevision, propertiesRevision);
            }

            game.SendOperation(OperationCode.SubscribeItem, data, true, Settings.ItemChannel);
        }

        /// <summary>
        /// The unsubscribe item.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        public static void UnsubscribeItem(Game game, string itemId, byte itemType)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.ItemId, itemId }, { (byte)ParameterCode.ItemType, itemType } };

            game.SendOperation(OperationCode.UnsubscribeItem, data, true, Settings.ItemChannel);
        }

        #region PopBloop Operations

        /// <summary>
        /// Game Item Move
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="itemId">The item id</param>
        /// <param name="itemType">The item type</param>
        /// <param name="gameItemId">The animation name</param>
        /// <param name="position">Game item positio</param>
        /// <param name="rotation">Game item rotation</param>
        /// <param name="sendReliable">Reliable send?</param>
        public static void GameItemMove(Game game, string itemId, byte? itemType, string gameItemId, float[] position, float[] rotation, bool sendReliable)
        {
            var data = new Dictionary<byte, object> { { (byte)ParameterCode.Position, position }, { (byte)ParameterCode.GameItemId, gameItemId } };

            if (itemId != null)
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            if (rotation != null)
            {
                data.Add((byte)ParameterCode.Rotation, rotation);
            }

            game.SendOperation(OperationCode.GameItemMove, data, sendReliable, Settings.ItemChannel);
        }

        public static void GameItemAnimate(Game game, string itemId, byte? itemType, string gameItemId, string animation, byte animationAction, byte animationWrap, float animationSpeed, bool sendReliable)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.GameItemId, gameItemId },
                { (byte)ParameterCode.Animation, animation },
                { (byte)ParameterCode.AnimationAction, animationAction }, 
                { (byte)ParameterCode.AnimationWrap, animationWrap },
                { (byte)ParameterCode.AnimationSpeed, animationSpeed },
            };

            if (itemId != null)
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            game.SendOperation(OperationCode.GameItemAnimate, data, sendReliable, Settings.ItemChannel);
        }

        /// <summary>
        /// Animate the item
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="itemId">The item id</param>
        /// <param name="itemType">The item type</param>
        /// <param name="animation">The animation name</param>
        /// <param name="sendReliable">Reliable send?</param>
        public static void Animate(Game game, string itemId, byte? itemType, string animation, byte animationAction, byte animationWrap, float animationSpeed, int animationLayer, bool sendReliable)
        {
            var data = new Dictionary<byte, object>() 
            { 
                { (byte)ParameterCode.Animation, animation },
                { (byte)ParameterCode.AnimationAction, animationAction }, 
                { (byte)ParameterCode.AnimationWrap, animationWrap },
                { (byte)ParameterCode.AnimationSpeed, animationSpeed },
                { (byte)ParameterCode.AnimationLayer, animationLayer }
            };

            if (itemId != null)
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            game.SendOperation(OperationCode.Animate, data, sendReliable, Settings.ItemChannel);
        }

        /// <summary>
        /// Send Chat Message
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="itemId">The item id</param>
        /// <param name="itemType">The item type</param>
        /// <param name="group">The Chat Group</param>
        /// <param name="message">The Chat Message</param>
        public static void Chat(Game game, string itemId, byte? itemType, string[] group, string message)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.ChatGroup, group },
                { (byte)ParameterCode.ChatMessage, message }
            };

            if (itemId != null)
            {
                data.Add((byte)ParameterCode.ItemId, itemId);
            }

            if (itemType.HasValue)
            {
                data.Add((byte)ParameterCode.ItemType, itemType.Value);
            }

            game.SendOperation(OperationCode.Chat, data, true, Settings.ItemChannel);
        }        

        /// <summary>
        /// Load World Level Data
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="worldName">World Name</param>
        public static void LoadWorld(Game game, string worldName, bool isPrivateRoom)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.WorldName, worldName },
                { (byte)ParameterCode.PrivateRoom, isPrivateRoom }
            };

            game.SendOperation(OperationCode.LoadWorld, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// Authenticate player's token.
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="token">Player token</param>
        public static void Authenticate(Game game, string token)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.PlayerToken, token },
            };

            game.SendOperation(OperationCode.Authenticate, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// Login to the Game Server
        /// </summary>
        /// <param name="game"></param>
        /// <param name="username"></param>
        /// <param name="password"></param>
        public static void Login(Game game, string username, string password)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.Username, username },
                { (byte)ParameterCode.Password, password }
            };

            game.SendOperation(OperationCode.Login, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// Add the item to the inventory
        /// </summary>
        /// <param name="game">The game</param>
        /// <param name="code">The game item code</param>
        /// <param name="name">The game item name</param>
        /// <param name="weight">The game item weight</param>
        /// <param name="description">The game item description</param>
        public static void SetInventoryAdd(Game game, string code, string name, float weight, string description)
        {
            SetInventory(game, "", code, name, weight, description, true);    
        }

        /// <summary>
        /// Remove the item from the inventory
        /// </summary>
        /// <param name="game">The game</param>
        /// <param name="code">The game item code</param>
        /// <param name="name">The game item name</param>
        /// <param name="weight">The game item weight</param>
        /// <param name="description">The game item description</param>
        public static void SetInventoryRemove(Game game, string id, string code, string name, float weight, string description)
        {
            SetInventory(game, id, code, name, weight, description, false);
        }

        /// <summary>
        /// Set the inventory 
        /// </summary>
        /// <param name="game">The game</param>
        /// <param name="code">The game item code</param>
        /// <param name="name">The game item name</param>
        /// <param name="weight">The game item weight</param>
        /// <param name="description">The game item description</param>
        /// <param name="isAdding">Is the operation adding or removing</param>
        public static void SetInventory(Game game, string id, string code, string name, float weight, string description, bool isAdding)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.PlayerId, game.Avatar.Id.ToString() },
                { (byte)ParameterCode.InventoryItemIsAdding, isAdding },
                { (byte)ParameterCode.InventoryItemCode, code },
                { (byte)ParameterCode.InventoryItemName, name },
                { (byte)ParameterCode.InventoryItemWeight, weight },
                { (byte)ParameterCode.InventoryItemDescription, description}
            };

            if (isAdding == false)
            {
                data.Add((byte)ParameterCode.ItemId, id);
            }

            game.SendOperation(OperationCode.UpdateInventory, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// Fetch the Inventory
        /// </summary>
        /// <param name="game">The game</param>
        /// <param name="id">The User Id</param>
        public static void FetchInventory(Game game)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.PlayerId, game.Avatar.Id.ToString() }
            };

            game.SendOperation(OperationCode.FetchInventory, data, true, Settings.OperationChannel);
        }

        public static void FetchEquipments(Game game)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.PlayerId, game.Avatar.Id.ToString() }
            };

            game.SendOperation(OperationCode.FetchEquipments, data, true, Settings.OperationChannel);
        }

        public static void UpdateEquipment(Game game, string code, int count)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.PlayerId, game.Avatar.Id.ToString() },
                { (byte)ParameterCode.EquipmentCode, code },
                { (byte)ParameterCode.EquipmentCount, count }
            };

            game.SendOperation(OperationCode.UpdateEquipment, data, true, Settings.OperationChannel);
        }
        
        /// <summary>
        /// Add the Quest ID to the Quest Journal
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="questid">Quest ID</param>
        public static void SetQuestJournal(Game game, int questid, bool isActive)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.ItemId, game.Avatar.Id.ToString() },
                { (byte)ParameterCode.QuestID, questid },
                { (byte)ParameterCode.QuestActive, isActive } 
            };

            game.SendOperation(OperationCode.QuestJournal, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// Set Private Level to the Server
        /// </summary>
        /// <param name="game">The mmo game</param>
        public static void SetPrivateLevel(Game game)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.ItemId, game.Avatar.Id.ToString() },
                { (byte)ParameterCode.PrivateLevel, game.WorldData.Level }
            };

            game.SendOperation(OperationCode.PrivateLevel, data, true, Settings.OperationChannel);
        }

        /// <summary>
        /// Get Level Info
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="worldName">The World's name</param>
        public static void GetLevelInfo(Game game, string worldName)
        {
            var data = new Dictionary<byte, object>()
            {
                { (byte)ParameterCode.WorldName, worldName },
            };

            game.SendOperation(OperationCode.LevelInfo, data, true, Settings.OperationChannel);
        }

        #endregion
    }
}