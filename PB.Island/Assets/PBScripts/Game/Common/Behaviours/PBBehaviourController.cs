/**********************************************/
/*
/* Unity Project 
/*
/* (C) 2011, 2012 Suhendra Ahmad
/*
/**********************************************/

using UnityEngine;
using System.Collections;
using System.Collections.Generic;

/// <summary>
/// Use this Behaviour version if the class want to handle more than 1 object, like Enemies, Weapons, etc
/// </summary>
public class PBBehaviourController : PBBehaviourBase
{ 
	#region MemVars & Props

    static public List<PBBehaviourController> instances; 

	#endregion

	
	#region Mono Methods

    protected virtual void Awake()
    {
        if (instances == null)
        {
            instances = new List<PBBehaviourController>();
        }
        instances.Add(this);
    }
	
	#endregion
	
	
	#region Custom Methods
	
	#endregion
}
