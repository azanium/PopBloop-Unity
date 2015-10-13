// --------------------------------------------------------------------------------------------------------------------
// <copyright file="IGameListener.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   The i game logic listener.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.Client
{
    using System;

    using ExitGames.Client.Photon;
    
    using PB.Common;

    /// <summary>
    /// The i game logic listener.
    /// </summary>
    [CLSCompliant(false)]
    public interface IGameListener
    {
        /// <summary>
        /// Gets a value indicating whether IsDebugLogEnabled.
        /// </summary>
        bool IsDebugLogEnabled { get; }

        /// <summary>
        /// The log debug.
        /// </summary>
        /// <param name="game">
        /// The source game.
        /// </param>
        /// <param name="message">
        /// The message.
        /// </param>
        void LogDebug(Game game, string message);

        /// <summary>
        /// The log error.
        /// </summary>
        /// <param name="game">
        /// The source game.
        /// </param>
        /// <param name="message">
        /// The message.
        /// </param>
        void LogError(Game game, string message);

        /////// <summary>
        /////// The log error
        /////// </summary>
        /////// <param name="game">
        /////// The source game.
        /////// </param>
        /////// <param name="errorCode">
        /////// The error code.
        /////// </param>
        /////// <param name="debugMessage">
        /////// The debug message.
        /////// </param>
        /////// <param name="operationCode">
        /////// The operation code.
        /////// </param>
        ////void LogError(Game game, ReturnCode errorCode, string debugMessage, OperationCode operationCode);

        /// <summary>
        /// The log error.
        /// </summary>
        /// <param name="game">
        /// The source game.
        /// </param>
        /// <param name="exception">
        /// The exception.
        /// </param>
        void LogError(Game game, Exception exception);

        /// <summary>
        /// The log info.
        /// </summary>
        /// <param name="game">
        /// The source game.
        /// </param>
        /// <param name="message">
        /// The message.
        /// </param>
        void LogInfo(Game game, string message);

        /// <summary>
        /// The on camera attached.
        /// </summary>
        /// <param name="itemId">
        /// The item Id.
        /// </param>
        /// <param name="itemType">
        /// The item Type.
        /// </param>
        void OnCameraAttached(string itemId, byte itemType);

        /// <summary>
        /// The on camera detached.
        /// </summary>
        void OnCameraDetached();

        /// <summary>
        /// The on connect.
        /// </summary>
        /// <param name="game">
        /// The source game.
        /// </param>
        void OnConnect(Game game);

        /// <summary>
        /// The on disconnect.
        /// </summary>
        /// <param name="game">
        /// The source game.
        /// </param>
        /// <param name="returnCode">
        /// The return code.
        /// </param>
        void OnDisconnect(Game game, StatusCode returnCode);

        /// <summary>
        /// The on item added.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="item">
        /// The mmo item.
        /// </param>
        void OnItemAdded(Game game, Item item);

        /// <summary>
        /// The on item removed.
        /// </summary>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="item">
        /// The mmo item.
        /// </param>
        void OnItemRemoved(Game game, Item item);

        /// <summary>
        /// The on item spawned.
        /// </summary>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        void OnItemSpawned(byte itemType, string itemId);

        /// <summary>
        /// The on radar update.
        /// </summary>
        /// <param name="itemId">
        /// The item id.
        /// </param>
        /// <param name="itemType">
        /// The item type.
        /// </param>
        /// <param name="position">
        /// The position.
        /// </param>
        void OnRadarUpdate(string itemId, byte itemType, float[] position);

        /// <summary>
        /// The on world entered.
        /// </summary>
        /// <param name="game">
        /// The source game.
        /// </param>
        void OnWorldEntered(Game game);

        #region PopBloop Events

        /// <summary>
        /// The World is Start Downloading
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="level">The level</param>
        void OnWorldStartDownload(Game game);

        /// <summary>
        /// The on authenticated
        /// </summary>
        /// <param name="game"></param>
        void OnAuthenticated(Game game, bool isAuth);

        /// <summary>
        /// The On Received Chat Message
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="group">The chat group</param>
        /// <param name="message">The chat message</param>
        void OnReceivedChatMessage(Game game, Item item, string[] group, string message);

        /// <summary>
        /// The event when some player change their animation event
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="item">The mmo item</param>
        /// <param name="animation">The animation name</param>
        /// <param name="wrapMode">The animation wrap mode</param>
        /// <param name="animationSpeed">The animation speed</param>
        void OnItemAnimate(Game game, Item item, string animation, AnimationAction action, byte wrapMode, float animationSpeed, int layer);

        /// <summary>
        /// The event when Inventories data received
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="inventories">The Inventories data</param>
        void OnInventoriesReceived(Game game, Inventories inventories);

        /// <summary>
        /// The event when Equipments data received
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="equipments">The Equipments data</param>
        void OnEquipmentsReceived(Game game, Equipments equipments);

        /// <summary>
        /// The event when Level info received
        /// </summary>
        /// <param name="game"></param>
        /// <param name="worldName"></param>
        /// <param name="maxCCU"></param>
        void OnGetLevelInfo(Game game, string worldName, int currentCCU, int maxCCU);

        /// <summary>
        /// The event when game item is moved
        /// </summary>
        /// <param name="game">The mmo game</param>
        /// <param name="gameItemId">Game item id</param>
        /// <param name="position">Game item position</param>
        /// <param name="rotation">Game item rotation</param>
        void OnGameItemMoved(Game game, string gameItemId, float[] position, float[] rotation);

        /// <summary>
        /// The event when game item is animated
        /// </summary>
        /// <param name="game">Mmo game</param>
        /// <param name="gameItemId">game item id</param>
        /// <param name="animation">animation name</param>
        /// <param name="action">animation action</param>
        /// <param name="wrapMode">animation wrap mode</param>
        /// <param name="animationSpeed">animatin speed</param>
        void OnGameItemAnimate(Game game, string gameItemId, string animation, AnimationAction action, byte wrapMode, float animationSpeed);

        #endregion
    }
}