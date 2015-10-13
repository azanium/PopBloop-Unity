// --------------------------------------------------------------------------------------------------------------------
// <copyright file="Connected.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   The dispatcher connected.
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
    /// The dispatcher connected.
    /// </summary>
    [CLSCompliant(false)]
    public class Connected : IGameLogicStrategy
    {
        #region MemVars & Props

        /// <summary>
        /// The instance.
        /// </summary>
        public static readonly IGameLogicStrategy Instance = new Connected();

        /// <summary>
        /// Gets State.
        /// </summary>
        public GameState State
        {
            get
            {
                return GameState.Connected;
            }
        }

        #endregion


        #region Implemented Interfaces

        #region IGameLogicStrategy

        /// <summary>
        /// The on event receive.
        /// </summary>
        /// <param name="game">
        /// The game logic.
        /// </param>
        /// <param name="eventData">
        /// The event data.
        /// </param>
        public void OnEventReceive(Game game, EventData eventData)
        {
            game.OnUnexpectedEventReceive(eventData);
        }

        /// <summary>
        /// The on operation return.
        /// </summary>
        /// <param name="game">
        /// The game logic.
        /// </param>
        /// <param name="response">
        /// The response.
        /// </param>
        public void OnOperationReturn(Game game, OperationResponse response)
        {
            // by default, a return of 0 is "successfully done"
            if (response.ReturnCode == 0)
            {
                switch ((OperationCode)response.OperationCode)
                {
                    case OperationCode.Login:
                    case OperationCode.Authenticate:
                        {
                            Authenticate(game, response);

                            return;
                        }

                    case OperationCode.LoadWorld:
                        {
                            LoadLevel(game, response);

                            return;
                        }

                    case OperationCode.CreateWorld:
                        {
                            if (game.Listener.IsDebugLogEnabled)
                            {
                                game.Listener.LogDebug(game, "OpCode: CreateWorld");
                            }

                            game.Avatar.EnterWorld();

                            return;
                        }

                    case OperationCode.EnterWorld:
                        {
                            var worldData = new WorldData
                            {
                                Name = (string)response.Parameters[(byte)ParameterCode.WorldName],
                                BottomRightCorner = (float[])response.Parameters[(byte)ParameterCode.BottomRightCorner],
                                TopLeftCorner = (float[])response.Parameters[(byte)ParameterCode.TopLeftCorner],
                                TileDimensions = (float[])response.Parameters[(byte)ParameterCode.TileDimensions],
                                Level = game.WorldData.Level
                            };

                            if (game.Listener.IsDebugLogEnabled)
                            {
                                game.Listener.LogDebug(game, "OpCode: EnterWorld");
                            }

                            game.Listener.LogDebug(game, "LoadWorld: Loading world: " + game.WorldData.Name + ", Tile Size(x, y) = (" + worldData.TileDimensions[0] + ", " + worldData.TileDimensions[1] + ")" +
                                      ", World Size = " + worldData.BottomRightCorner[0] + ", " + worldData.BottomRightCorner[1]);

                            game.SetStateWorldEntered(worldData);

                            return;
                        }

                    case OperationCode.GameItemMove:
                    case OperationCode.GameItemAnimate:
                        {
                            // DO NOTHING, THIS WILL BE HANDLED BY WORLDENTERED STATE
                            return;
                        }

                    #region ORIGINAL CODES

                    /*
                    case OperationCode.CreateWorld:
                        {
                            game.Avatar.EnterWorld();
                            return;
                        }

                    case OperationCode.EnterWorld:
                        {
                            var worldData = new WorldData
                                {
                                    Name = (string)response.Parameters[(byte)ParameterCode.WorldName],
                                    BottomRightCorner = (float[])response.Parameters[(byte)ParameterCode.BottomRightCorner],
                                    TopLeftCorner = (float[])response.Parameters[(byte)ParameterCode.TopLeftCorner],
                                    TileDimensions = (float[])response.Parameters[(byte)ParameterCode.TileDimensions]
                                };
                            game.SetStateWorldEntered(worldData);
                            return;
                        }

                     */

                    #endregion
                }
            }
            else
            {
                switch ((OperationCode)response.OperationCode)
                {
                    case OperationCode.LoadWorld:
                        {
                            // Do something when load world is failed
                            game.Listener.LogError(game, "LoadWorld failed: " + ((ReturnCode)response.ReturnCode).ToString() + " => " + response.DebugMessage);
                            
                            if (game.Listener.IsDebugLogEnabled)
                            {
                                game.Listener.LogError(game, "LoadWorld failed, Game disconnected, Return Code: " + response.ReturnCode + ", OpCode: " + response.OperationCode + ", " +
                                    response.DebugMessage);
                            }

                            game.Listener.OnAuthenticated(game, false);

                            return;
                        }

                    case OperationCode.Login:
                    case OperationCode.Authenticate:
                        {
                            game.Listener.LogError(game, "Auth failed: " + ((ReturnCode)response.ReturnCode).ToString() + " => " + response.DebugMessage);
                            if (game.Listener.IsDebugLogEnabled)
                            {
                                game.Listener.LogError(game, "Authentication failed, Game disconnected, Return Code: " + response.ReturnCode + ", OpCode: " + response.OperationCode + ", " +
                                    response.DebugMessage);
                            }

                            game.Listener.OnAuthenticated(game, false);

                            return;
                        }

                    case OperationCode.EnterWorld:
                        {
                            if (game.Listener.IsDebugLogEnabled)
                            {
                                game.Listener.LogDebug(game, "OpCode: Invalid EnterWorld");
                            }

                            Operations.CreateWorld(
                                game, game.WorldData.Name, game.WorldData.TopLeftCorner, game.WorldData.BottomRightCorner, game.WorldData.TileDimensions);
                            return;
                        }

                    #region ORIGINAL CODES
                    /*
                    case OperationCode.EnterWorld:
                        {
                            Operations.CreateWorld(
                                game, game.WorldData.Name, game.WorldData.TopLeftCorner, game.WorldData.BottomRightCorner, game.WorldData.TileDimensions);
                            return;
                        }
                         */
                    #endregion
                }
            }

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


        #region PopBloop CODES

        private void Authenticate(Game game, OperationResponse response)
        {
            string playerId = (string)response.Parameters[(byte)ParameterCode.PlayerId];
            string playerUsername = (string)response.Parameters[(byte)ParameterCode.PlayerUsername];
            string avatarName = (string)response.Parameters[(byte)ParameterCode.AvatarName];
            byte[] inventoryData = (byte[])response.Parameters[(byte)ParameterCode.Inventories];
            byte[] equipmentsData = (byte[])response.Parameters[(byte)ParameterCode.Equipments];
            int[] questJournal = (int[])response.Parameters[(byte)ParameterCode.QuestJournal];
            int[] questActive = (int[])response.Parameters[(byte)ParameterCode.QuestActive];

            if (playerId != null || playerId != "")
            {
                Inventories inventories = null;
                if (inventoryData != null)
                {
                    inventories = Inventories.Deserialize(inventoryData);
                }
                else
                {
                    game.Listener.LogError(game, "Invalid Inventories Data received from Game Server");
                }

                Equipments equipments = null;
                if (equipmentsData != null)
                {
                    equipments = Equipments.Deserialize(equipmentsData);
                }
                else
                {
                    game.Listener.LogError(game, "Invalid Equipments data received from Game Server");
                }

                game.Avatar.Inventories = inventories;
                game.Avatar.Equipments = equipments;
                game.Avatar.QuestJournals = questJournal;
                game.Avatar.QuestActiveJournals = questActive;
                game.Avatar.Id = playerId;
                game.Avatar.Username = playerUsername;
                game.Avatar.SetAvatarName(avatarName);
                
                game.Listener.OnAuthenticated(game, true);
            }
        }

        private void LoadLevel(Game game, OperationResponse response)
        {
            Level level = null;

            // Server harus mengembalikan Serialized Level protocol buffer 
            try
            {
                byte[] buffer = (byte[])response.Parameters[(byte)ParameterCode.WorldLevel];

                level = LevelBuilder.Deserialize(buffer);
            }
            catch (Exception ex)
            {
                game.Listener.LogError(game, ex.ToString());
            }

            // We've got level 
            if (level != null)
            {
                if (level.Entities.Count == 0)
                {
                    if (game.Listener.IsDebugLogEnabled)
                    {
                        game.Listener.LogError(game, "Level Entities is zero, meaning there's something wrong with the World[Name]");
                    }

                    game.SetDisconnected(StatusCode.Exception);

                    return;
                }

                
                game.WorldData.Level = level;
                game.WorldData.TileDimensions = level.InterestArea;
                game.WorldData.BottomRightCorner = level.WorldSize;

                game.Listener.OnWorldStartDownload(game);
            }
            else
            {
                if (game.Listener.IsDebugLogEnabled)
                {
                    game.Listener.LogError(game, "Game Disconnected, because the Level being Loaded is invalid");
                }
                game.SetDisconnected(StatusCode.Exception);
            }

        }

        #endregion
    }
}