/// <summary>
/// 
/// Items Menu on Unity Editor Main Menu
/// For use with Level Designer
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using UnityEditor;
using System.Collections;
using System;
using PB.Common;

public class SAItemsMenu 
{
    public static GameObject[] GetSelections()
    {
        return GetSelections(true);
    }

	public static GameObject[] GetSelections(bool displayError)
	{
		GameObject[] objs = Selection.gameObjects;
		
		if (objs.Length == 0 && displayError)
		{
			EditorUtility.DisplayDialog("Error", "Please select GameObject(s) on the hierarchy pane", "OK");
			return null;
		}
		
		return objs;
	}
	
	public static bool TagObject(GameObject go, string tag)
	{
		try
		{
			go.tag = tag;
		}
		catch (Exception ex)
		{
			EditorUtility.DisplayDialog("Error!", ex.Message, "OK");
			return false;
		}

		return true;
	}

    [MenuItem("PopBloop/Items/Pickup Item")]
	static void ExecutePickupItem()
	{
		GameObject[] objs = GetSelections();
		if (objs == null) return;
		
		foreach (GameObject go in objs)
		{
			if (TagObject(go, LevelConstants.TagItem) == false)
			{
				break;
			}
			
			if (go.GetComponent<ItemPickup>() == null)
			{
				go.AddComponent<ItemPickup>();
			}
			
			if (go.GetComponent<Collider>() != null)
			{
				go.GetComponent<Collider>().isTrigger = true;
			}
		}
	}

    [MenuItem("PopBloop/Items/Loot Item")]
    static void ExecuteLootItem()
    {
        GameObject[] objs = GetSelections();
        if (objs == null) return;

        foreach (GameObject go in objs)
        {
            if (TagObject(go, LevelConstants.TagItem) == false)
            {
                break;
            }

            if (go.GetComponent<ItemLoot>() == null)
            {
                go.AddComponent<ItemLoot>();
            }

            if (go.GetComponent<Collider>() != null)
            {
                go.GetComponent<Collider>().isTrigger = true;
            }

        }
    }

    [MenuItem("PopBloop/Items/NPC")]
	static void ExecuteNPCItem()
	{
		GameObject[] objs = GetSelections();
		if (objs == null) return;
		
		foreach (GameObject go in objs)
		{
			if (TagObject(go, LevelConstants.TagNPC) == false)
			{
				break;
			}
			
			if (go.GetComponent<ItemNPC>() == null)
			{
				go.AddComponent<ItemNPC>();
			}
		}
	}

    [MenuItem("PopBloop/Items/Equipment Pickup")]
    static void ExecutePickupEquipment()
    {
        GameObject[] objs = GetSelections();
        if (objs == null) return;

        foreach (GameObject go in objs)
        {
            if (TagObject(go, LevelConstants.TagItem) == false)
            {
                break;
            }

            if (go.GetComponent<ItemEquipmentPickup>() == null)
            {
                go.AddComponent<ItemEquipmentPickup>();
            }

            if (go.GetComponent<Collider>() != null)
            {
                go.GetComponent<Collider>().isTrigger = true;
            }
        }
    }

    [MenuItem("PopBloop/Items/Visibility Trigger Item")]
    static void ExecuteVisibilityTriggerItem()
    {
        GameObject[] objs = GetSelections();
        if (objs == null) return;

        foreach (GameObject go in objs)
        {
            if (TagObject(go, LevelConstants.TagItem) == false)
            {
                break;
            }

            if (go.GetComponent<ItemVisibilityTrigger>() == null)
            {
                go.AddComponent<ItemVisibilityTrigger>();
            }

            if (go.GetComponent<Collider>() != null)
            {
                go.GetComponent<Collider>().isTrigger = true;
            }
        }
    }

    [MenuItem("PopBloop/GUI/Hint")]
    static void ExecuteHint()
    {
        GameObject[] objs = GetSelections();
        if (objs == null) return;

        foreach (GameObject go in objs)
        {
            if (go.GetComponent<ItemHint>() == null)
            {
                go.AddComponent<ItemHint>();
            }
        }
    }
}
