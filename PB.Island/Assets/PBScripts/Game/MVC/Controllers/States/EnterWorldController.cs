using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Common;
using PB.Game;
using PB.Client;

public class PBEnterWorldState : PBGameState
{
    static public readonly PBGameState Instance = new PBEnterWorldState();

    public PBEnterWorldState()
        : base(GameStateType.EnterWorld)
    {
    }

    public override void OnGUI(GameControllerBase mainGame)
    {
        base.OnGUI(mainGame);

        /*if (PBGameMaster.GameState == GameStateType.EnterWorld)
        {
            UIProgressBar.Instance.Update(PBConstants.LOADINGBAR_LOADING, 0, PBConstants.LOADINGBAR_FONTSIZE);

            return;
        }*/
    }

    public override void OnWorldEntered(GameControllerBase mainGame)
    {
        base.OnWorldEntered(mainGame);

        PBGameMaster.GameState = GameStateType.WorldEntered;

        WindowManager.IsVisible = true;

        mainGame.CreateAvatar(mainGame.Game);

        Operations.RadarSubscribe(mainGame.Game.Peer, mainGame.Game.WorldData.Name);

        // Unload unneeded assets
        Resources.UnloadUnusedAssets();

        // Finished and removed our loading background
        MainController.SetLoadingFinished();
    }

    public override void OnInventoriesReceived(GameControllerBase mainGame, Inventories inventories)
    {
        base.OnInventoriesReceived(mainGame, inventories);

        InventoryEngine.Instance.SetInventories(inventories);
    }

    public override void OnEquipmentsReceived(GameControllerBase mainGame, Equipments equipments)
    {
        base.OnEquipmentsReceived(mainGame, equipments);

        InventoryEngine.Instance.SetEquipments(equipments);
    }

    public override void OnReceivedChatMessage(GameControllerBase mainGame, PB.Client.Item item, string[] group, string message)
    {
        base.OnReceivedChatMessage(mainGame, item, group, message);
    }

    public override void OnRadarUpdate(GameControllerBase mainGame, string itemId, byte itemType, float[] position)
    {
        base.OnRadarUpdate(mainGame, itemId, itemType, position);

        itemId += itemType;

        if (position == null)
        {
            PBGameMaster.ItemPositions.Remove(itemId);
            return;
        }

        Vector3 pos = new Vector3(position[0], position[1], position.Length > 2 ? position[2] : 0f);

        if (PBGameMaster.ItemPositions.ContainsKey(itemId) == false)
        {
            PBGameMaster.ItemPositions.Add(itemId, pos);
            return;
        }

        PBGameMaster.ItemPositions[itemId] = pos;
    }
}
