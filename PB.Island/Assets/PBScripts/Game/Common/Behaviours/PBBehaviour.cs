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
/// Use this Behaviour version if the class is only handle 1 object, like Player, etc.
/// </summary>
public class PBBehaviour : PBBehaviourBase 
{
	#region MemVars & Props

    static public PBBehaviour instance = null;

	#endregion

	
	#region Mono Methods

    protected virtual void Awake()
    {
        if (instance == null)
        {
            instance = this;
        }
    }
	
	#endregion
	
	
	#region Custom Methods
	
	#endregion
}
