/// <summary>
/// 
/// Item Loot Controller
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Common;

[AddComponentMenu("PopBloop Scripts/Items/Loot Item")]
public class ItemLoot : ItemBase {
	
	#region MemVars & Props
	
	public string[] items;
	
	public override ItemBaseType ItemType 
	{
		get  { return ItemBaseType.Loot; }
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
	protected override void Start () {
        base.Start();

		InitSparkle(false);
	}
	
	// Update is called once per frame
	protected override void Update () {
        base.Update();
	}
	
	#endregion
	
	
	#region Internal Methods
	
	public string Take(int index)
	{
		if (items == null)
		{
			return "";
		}
		
		if (index > 0 && index < items.Length)
		{
			List<string> itemList = new List<string>(items);
			
			string item = items[index];
			
			itemList.RemoveAt(index);
			
			items = itemList.ToArray();
			itemList = null;
			
			return item;
		}

        return ""; ;
	}
	
	#endregion
}
