using UnityEngine;
using System.Collections;
using PB.Client;
using PB.Common;

public class ItemEquipmentPickup : ItemAnimateBase
{
    #region MemVars & Props

    public override ItemActionType ActionType
    {
        get
        {
            return ItemActionType.Pickup;
        }
    }

    public override ItemBaseType ItemType
    {
        get
        {
            return ItemBaseType.Pickup;
        }
    }

    public Equipments.EquipmentType equipmentType = Equipments.EquipmentType.Coin;
    public int equipmentCount = 1;
    public int equipmentMax = 0;

    #endregion


    #region Methods

    protected void StoreEquipment()
    {
        int count = InventoryEngine.Instance.GetEquipmentCount(equipmentType);
        if (equipmentMax > 0)
        {
            if (count + equipmentCount <= equipmentMax)
            {
                InventoryEngine.Instance.SyncEquipment(equipmentType.ToString().ToLower(), count + equipmentCount);
            }
        }
        else
        {
            InventoryEngine.Instance.SyncEquipment(equipmentType.ToString().ToLower(), count + equipmentCount);
        }
    }

    public override void OnAction(GameControllerBase game)
    {
        base.OnAction(game);

        if (Vector3.Distance(transform.position, game.Avatar.transform.position) <= Distance)
        {
            this.gameObject.renderer.enabled = false;
            
            isVisible = false;

            ResetSpawn();

            StoreEquipment();
        }
        else
        {
            WindowManager.CreateDialog("dlgItem", PBConstants.APP_TITLE, Lang.Localized("Jarak anda terlalu jauh untuk mengambil item ini!"), new string[] { "OK" },
            (dlg, index) =>
            {
                ((winDialog)dlg).Hide();
            }).MakeCenter(300, 200);
        }
    }

    #endregion
}
