/// <summary>
/// 
/// Item Pickup Controller
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using System.Collections;
using System.Timers;
using PB.Common;
using PB.Client;

[AddComponentMenu("PopBloop Scripts/Items/Pickup Item")]
public class ItemPickup : ItemAnimateBase 
{
	#region MemVars & Props
	
	public string itemCode = "coin";
    public string itemName = "";
    public string itemDescription = "";
    public float itemWeight = 0.0f;
	public int itemCount = 1;

    public bool isUnique = true;
	
	public override ItemBaseType ItemType 
	{
		get  { return ItemBaseType.Pickup; }
	}

    public override ItemActionType ActionType
    {
        get
        {
            return ItemActionType.Pickup;
        }
    }
	
	#endregion
	
	
	#region MonoBehavior Methods
	
	// Use this for initialization
    protected override void Start()
    {
        base.Start();

        if (_animations.Count > 0)
        {
            gameObject.animation.Stop();
        }

		InitSparkle(false);
	}
		
	#endregion
	
	
	#region Internal Methods
	
	private void StoreItem()
	{
        if (isUnique)
        {
            if (InventoryEngine.Instance.Items.Count < 9 && InventoryEngine.Instance.ItemCount(itemCode) == 0)
            {
                InventoryEngine.Instance.SyncItemAdd(new GameItem("", itemCode, itemName, itemWeight, itemDescription));
            }
        }
        else
        {
            if (itemCount > 0)
            {
                for (int i = 0; i < itemCount; i++)
                {
                    if (InventoryEngine.Instance.Items.Count < 9)
                    {
                        InventoryEngine.Instance.SyncItemAdd(new GameItem("", itemCode, itemName, itemWeight, itemDescription));
                    }
                }
            }
        }
	}

    public override void OnAction(GameControllerBase game)
	{
		base.OnAction(game);
		
		if (CalcDistance(transform.position, game.Avatar.transform.position) <= Distance && isVisible)
		{
			this.gameObject.renderer.enabled = false;
			isVisible = false;
            _readyToUse = false;

            ResetSpawn();

            PlayerAnimator.playerAnimation.Animate(PBConstants.ANIM_PICKUP, AnimationAction.Play, WrapMode.Once, 1f, 0);

			StoreItem();
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

    protected override void OnRespawn()
    {
        base.OnRespawn();

        _readyToUse = true;
    }

    private void PlayerAnimationStopped(ActorAnimator.AnimationData animation)
    {
    }
	
	#endregion
}
