/// <summary>
/// 
/// Item Base Type
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using System.Collections;

/// <summary>
/// All Interactive item type defined here
/// </summary>
public enum ItemBaseType
{
	Invalid = 0,
	Pickup = 1,
	Loot = 2,
	Interaction = 3,
    Moveable = 4,
    Portal = 5,
    NPC = 6
}

public enum ItemActionType
{
    Pickup = 0,
    Talk,
    Use,
    Portal
}