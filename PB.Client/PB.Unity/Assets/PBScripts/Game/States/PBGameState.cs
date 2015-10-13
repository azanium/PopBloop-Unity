using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Client;
using System;
using PB.Common;

public class PBGameState
{
    #region MemVars & Props

    static readonly public List<PBGameState> pbGameStates = new List<PBGameState>();

    static private GameStateType pbCurrentState = GameStateType.Disconnected;

    public virtual GameStateType State
    {
        get;
        set;
    }
 
    #endregion


    #region Ctor / Dtor

    public PBGameState(GameStateType state)
    {
        this.State = state;
    }

    public static void Register(PBGameState state)
    {
        if (pbGameStates.Contains(state) == false)
        {
            pbGameStates.Add(state);
        }   
    }


    #endregion


    #region Mono Methods

    public virtual void Start(GameControllerBase mainGame)
    {
    }

    public virtual void Update(GameControllerBase mainGame)
    {
    }

    public virtual void LateUpdate(GameControllerBase mainGame)
    {
    }

    public virtual void FixedUpdate(GameControllerBase mainGame)
    {
    }

    public virtual void OnGUI(GameControllerBase mainGame)
    {
    }

    #endregion


    #region Instance IGameListener Methods

    public virtual void OnAuthenticated(GameControllerBase mainGame, bool isAuth)
    {
    }

    public virtual void OnCameraAttached(string itemId, byte itemType)
    {
    }

    public virtual void OnCameraDetached()
    {
    }

    public virtual void OnConnect(GameControllerBase mainGame)
    {
    }

    public virtual void OnDisconnect(GameControllerBase mainGame, ExitGames.Client.Photon.StatusCode returnCode)
    {
    }

    public virtual void OnItemAdded(GameControllerBase mainGame, Item item)
    {
    }

    public virtual void OnItemAnimate(GameControllerBase mainGame, Item item, string animation, PB.Common.AnimationAction action, WrapMode wrapMode, float animationSpeed, int layer)
    {
    }

    public virtual void OnItemRemoved(GameControllerBase mainGame, Item item)
    {
    }

    public virtual void OnItemSpawned(GameControllerBase mainGame, byte itemType, string itemId)
    {
    }

    public virtual void OnRadarUpdate(GameControllerBase mainGame, string itemId, byte itemType, float[] position)
    {
    }

    public virtual void OnReceivedChatMessage(GameControllerBase mainGame, Item item, string[] group, string message)
    {
    }

    public virtual void OnWorldEntered(GameControllerBase mainGame)
    {
    }

    public virtual void OnWorldStartDownload(GameControllerBase mainGame)
    {
    }

    public virtual void OnInventoriesReceived(GameControllerBase mainGame, Inventories inventories)
    {
    }

    public virtual void OnEquipmentsReceived(GameControllerBase mainGame, Equipments equipments)
    {
    }

    #endregion


    #region Controller Methods

    public static void ForwardEvent(GameStateType state)
    {
        pbCurrentState = state;
    }

    public static void ProcessEvent(Action<PBGameState> callback)
    {
        foreach (PBGameState controller in pbGameStates)
        {
            if (controller.State == pbCurrentState)
            {
                if (callback != null)
                {
                    callback(controller);
                }
            }
        }
    }

    #endregion

}
