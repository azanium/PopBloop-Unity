using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using UnityEngine;
using PB.Client;

/// <summary>
/// Server as the Master Data of the Game as singleton
/// </summary>
public class PBGameMaster
{
    #region MemVars & Props

    /// <summary>
    /// Player position
    /// </summary>
    public static Vector3 PlayerPosition { get; set; }

    /// <summary>
    /// Player direction
    /// </summary>
    public static Vector3 PlayerDirection { get; set; }

    /// <summary>
    /// The Game Instance
    /// </summary>
    public static Game Game { get; set; }

    /// <summary>
    /// All positions of MMO items
    /// </summary>
    public static readonly Dictionary<string, Vector3> ItemPositions = new Dictionary<string, Vector3>();

    /// <summary>
    /// The Game Time
    /// </summary>
    public static DateTime GameTime = DateTime.Now;

    private static GameStateType _gameState = GameStateType.Disconnected;
    /// <summary>
    /// Game States
    /// </summary>
    public static GameStateType GameState
    {
        get { return _gameState; }
        set
        {
            _gameState = value;
            switch (_gameState)
            {
                case GameStateType.Connecting:
                case GameStateType.Connected:
                    PBGameState.ForwardEvent(GameStateType.Connected);
                    break;

                case GameStateType.Authenticating:
                case GameStateType.Authenticated:
                    PBGameState.ForwardEvent(GameStateType.Authenticated);
                    break;

                case GameStateType.Loading:
                    PBGameState.ForwardEvent(GameStateType.Loading);
                    break;

                case GameStateType.EnterWorld:
                case GameStateType.WorldEntered:
                    PBGameState.ForwardEvent(GameStateType.EnterWorld);
                    break;

                case GameStateType.Disconnected:
                    PBGameState.ForwardEvent(GameStateType.Disconnected);
                    break;
            }
        }
    }

    public static List<Transform> SpawnPoints = new List<Transform>();
    public static Transform GetRandomSpawnPoint
    {
        get
        {
            if (SpawnPoints.Count == 0)
            {
                return null;
            }

            int index = UnityEngine.Random.Range(0, SpawnPoints.Count - 1);

            return SpawnPoints[index];
        }
    }

    private static Dictionary<string, GameObject> _objects = new Dictionary<string, GameObject>();
    public static Dictionary<string, GameObject> Objects
    {
        get { return _objects; }
    }

    public static void Clear()
    {
        _objects.Clear();
    }

    #endregion

}

