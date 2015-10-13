using UnityEngine;
using System.Collections;

using PB.Common;
using PB.Client;

/// <summary>
/// Actor for Local Player's Avatar
/// Moving based on transform
/// </summary>
[RequireComponent(typeof(PBThirdPersonController))]
[RequireComponent(typeof(AudioSource))]
public class PlayerActor : Actor 
{	
	#region MemVars & Properties

    static public PlayerActor playerActor;

	private Vector3 _lastMovePosition;
	private Vector3 _lastMoveRotation;
	private float _nextMoveTime;
	private PBThirdPersonController motionController;
	
	#endregion
	
	
	#region MonoBehavior Methods

    protected override void Awake()
    {
        base.Awake();

        if (playerActor == null)
        {
            playerActor = this;
        }
    }
	
	protected override void Start() 
    {
        base.Start();

        motionController = GetComponent<PBThirdPersonController>();

        if (motionController == false)
        {
            Debug.LogError("Can't find PBThirdPersonController on PlayerActor.gameObject, please attach it!");
        }
	}
	
	// Update is called once per frame
	protected override void Update() 
    {
        base.Update();

        try
        {
            this.UpdatePositions();
        }
        catch (System.Exception ex)
        {
            Debug.LogWarning(ex.ToString());
        }

    }
		
	#endregion
	
	
	#region PlayerActor Methods

    public override void Initialize(Game mmoGame, Item mmoItem)
    {
        base.Initialize(mmoGame, mmoItem);

        
        motionController = GetComponent<PBThirdPersonController>();
        if (motionController != null)
        {
            PBGameMaster.PlayerDirection = motionController.MoveDirection = gameObject.transform.TransformDirection(Vector3.forward);
        }
    }

    public void MovePlayer(Vector3 position)
    {
        transform.position = position;
    }
		
	private void UpdatePositions()
	{
        // Update Player Position on GameMaster
        PBGameMaster.PlayerPosition = transform.position;
        if (motionController != null)
        {
            PBGameMaster.PlayerDirection = motionController.MoveDirection;
        }

		float time = Time.time;
		if (time >= _nextMoveTime)
		{
			Vector3 rotation = this.transform.rotation.eulerAngles;
            Vector3 position = this.transform.position;
			if (_lastMovePosition != position || _lastMoveRotation != rotation)			    
			{
                if (Vector3.Distance(position, _lastMovePosition) > 0.1f)
                {
                    _game.Avatar.MoveAbsolute(GetPosition(position), GetRotation(rotation));
                }
				
				_lastMovePosition = position;
				_lastMoveRotation = rotation;
			}

            int interval = 200;
            //if (_game != null)
            //{
            //    interval = _game.Settings.SendInterval;
            //}
			
			_nextMoveTime = time + (interval * 0.001f);
		}
    }

	#endregion
	
		
	#region Static Methods
		
	public static float[] GetPosition(Vector3 position)
	{
		float[] result = new float[3]
		{
			position.x,
			position.z,
			position.y
		};	
		
		return result;
	}

    public static Vector3 GetVectorPosition(float[] position)
    {
        return new Vector3(position[0], position[2], position[1]);
    }
	
	public static float[] GetRotation(Vector3 rotation)
	{
		float[] result = new float[3]
		{
			rotation.x,
			rotation.y,
			rotation.z
		};
		
		return result;	
	}

    public static Vector3 GetVectorRotation(float[] rotation)
    {
        return new Vector3(rotation[0], rotation[1], rotation[2]);
    }
	
	#endregion


    #region Game Messages

    public void PlayerStartSwimming(Vector3 waterPosition)
    {
        audio.loop = true;
        audio.Play();
    }

    public void PlayerEndSwimming()
    {
        audio.Stop();
    }

    #endregion
}
