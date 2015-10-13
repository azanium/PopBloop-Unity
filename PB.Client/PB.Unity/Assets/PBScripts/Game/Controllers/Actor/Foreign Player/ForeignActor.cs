using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Client;

/// <summary>
/// Foreign Player Actor
/// Moving based on _item (MmoItem)
/// </summary>
public class ForeignActor : Actor 
{
    static public List<ForeignActor> foreignActors;

    protected override void Awake()
    {
        base.Awake();

        if (foreignActors == null)
        {
            foreignActors = new List<ForeignActor>();
        }
        foreignActors.Add(this);
    }

    protected override void Update() 
    {
        base.Update();

		if (_item == null || _item.IsVisible == false)
		{
			ShowActor(false);
            return;
		}
				
		// Set the position
		Vector3 currentPos = new Vector3(_item.Position[0], transform.position.y, _item.Position[1]);
		
		if (_item.Position.Length == 3)
		{
			currentPos = new Vector3(_item.Position[0], _item.Position[2], _item.Position[1]);
		}
		
		if (currentPos != _lastMoveUpdate)
		{
			_lastMoveUpdate = currentPos;
			//_lastMoveUpdateTime = Time.time;
		}
		
		// Move smoothly
		float lerpT = Time.deltaTime * 7.0f;//(Time.time - _lastMoveUpdateTime) / 0.05f;
		
		// Use Lerp to smooth the position movements
		if(currentPos != transform.position)
		{
			transform.position = Vector3.Lerp(transform.position, currentPos, lerpT);
		}
		
		// Use Slerp to smooth the rotation movements
		if (_item.Rotation != null)
		{
			transform.rotation = Quaternion.Slerp(transform.rotation, this.GetRotation(_item.Rotation), lerpT);
		}

        // Make actor visible as the last, so character won't be jumpy
        ShowActor(true);
	}
	
}
