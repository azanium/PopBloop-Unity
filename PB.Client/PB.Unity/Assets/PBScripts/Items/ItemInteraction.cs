/// <summary>
/// 
/// Item Interaction Controller
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;
using System.Collections.Generic;

[AddComponentMenu("PopBloop Scripts/Items/Interaction Item")]
public class ItemInteraction : ItemBase 
{	
	#region MemVars & Props

    public enum ItemInteractionType
    {
        Sit,
        Sleep
    }

    public override ItemBaseType ItemType
    {
        get { return ItemBaseType.Interaction; }
    }

    /// <summary>
    /// User can use this 
    /// </summary>
    public override ItemActionType ActionType
    {
        get { return ItemActionType.Use; }
    }

	public Transform[] targetPoints;
	public string animationName;
	
    private int takenPos = 0;
    
	#endregion
	
	
	#region MonoBehavior Methods

    protected void Awake()
    {
        PBThirdPersonController.OnPlayerMovementStopped += PBThirdPersonController_OnPlayerMovementStopped;
    }

    protected void OnDestroy()
    {
        PBThirdPersonController.OnPlayerMovementStopped -= PBThirdPersonController_OnPlayerMovementStopped;
    }

	protected override void Start() 
    {
        base.Start();
	}
	
	protected override void Update() 
    {
        base.Update();
	}

    void OnEnable()
    {
        Messenger<Vector3>.AddListener(Messages.PLAYER_MOVETO, OnPlayerBeingMoved);
    }

    void OnDisable()
    {
        Messenger<Vector3>.RemoveListener(Messages.PLAYER_MOVETO, OnPlayerBeingMoved);
    }

	#endregion
	
	
	#region Internal Methods

    public override void OnAction(GameControllerBase game)
    {
        base.OnAction(game);

        Transform position = GetAvailablePosition();

        // Do not turn on the interaction hint
        _readyToUse = false;

        ItemInteractionType interactionType = ItemInteractionType.Sit;
        var target = position.gameObject.GetComponent<ItemInteractionTarget>();
        if (target != null)
        {
            interactionType = target.interactionType;
        }
        
        if (position != null)
        {
            Messenger<ItemInteraction, Transform, ItemInteraction.ItemInteractionType>.Broadcast(Messages.PLAYER_INTERACTO, this, position, interactionType, MessengerMode.DONT_REQUIRE_LISTENER);
        } 
    }
	
	public Transform GetAvailablePosition()
	{
		if (takenPos < targetPoints.Length)
		{
			return targetPoints[takenPos++];
		}
	
		return null;
	}

    private void OnPlayerBeingMoved(Vector3 position)
    {
        if (PBThirdPersonController.GetMotionType() == PBThirdPersonController.MotionType.Interacting)
        {
            Reset();
        }
    }

    private void PBThirdPersonController_OnPlayerMovementStopped()
    {
        if (PBThirdPersonController.GetMotionType() == PBThirdPersonController.MotionType.Interacting)
        {
            Reset();
        }
    }

    private void Reset()
    {
        if (takenPos > 0)
        {
            takenPos--;
        }

        _readyToUse = true;
    }

	
	#endregion


    #region Public Methods

    public void SetTargets(IEnumerable<Transform> objs)
    {
        List<Transform> targets = new List<Transform>();
        foreach (var go in objs)
        {
            targets.Add(go);
        }

        targetPoints = targets.ToArray();
    }

    #endregion
}
