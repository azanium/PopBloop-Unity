using UnityEngine;
using System;
using System.Collections.Generic;

using PB.Client;
using PB.Common;

public class Actor : MonoBehaviour
{
	#region MemVars & Properties

    static public List<Actor> actors;

	private AvatarName _avatarName = null;
    private Projector _projector = null;

	protected Item _item;
	/// <summary>
	/// The item 
	/// </summary>
	public Item Item { get { return _item; } }
	
	protected Game _game;
	/// <summary>
	/// The mmo game 
	/// </summary>
	public Game Game { get { return _game; } }
	
	//private float _lastMoveUpdateTime;
	
	protected Vector3 _lastMoveUpdate;
	protected AvatarController _character;
	
	protected bool _actorVisible = false;
	protected Vector3 _lastTargetPos;
	protected Transform _attachPoint;
	
	#endregion
	

	#region MonoBehaviour Events

    protected virtual void Awake()
    {
        if (actors == null)
        {
            actors = new List<Actor>();
        }
        actors.Add(this);
    }

	protected virtual void Start()
	{
        Application.runInBackground = true;

        _character = GetComponent<AvatarController>();

        if (_character == null)
        {
            Debug.LogWarning("No AvatarController found on the Foreign Character, please attach it");
        }

        // Find Blob Projector
        _projector = GetComponentInChildren<Projector>();

        _attachPoint = transform.FindChild("AttachPoint");

        _lastTargetPos = transform.position;
    }
	
	protected virtual void Update()
	{
	}
	
	#endregion

	
	#region Actor Methods
	
	public virtual void Initialize(Game mmoGame, Item mmoItem)		
	{
		_item = mmoItem;
		_game = mmoGame;
		
		this.name = _item.Id;
				
		transform.localScale = new Vector3(1f, 1f, 1f);
		transform.position = new Vector3(_item.Position[0], _item.Position[2], _item.Position[1]);
        transform.rotation = Quaternion.Euler(_item.Rotation[0], _item.Rotation[1], _item.Rotation[2]);

        _avatarName = GetComponentInChildren<AvatarName>();
        if (_avatarName != null)
        {
            _avatarName.Text = _item.AvatarName;
        }
	}
	
	public virtual void Destroy()
	{
		UnityEngine.Object.Destroy(gameObject);
		UnityEngine.Object.Destroy(this);
	}
		
	protected virtual bool ShowActor(bool show)
	{
		if (_actorVisible != show)
		{
			GetComponent<AvatarController>().SetActive(show);

            if (_avatarName != null)
            {
                _avatarName.gameObject.SetActive(show);
            }

            if (_projector != null)
            {
                _projector.gameObject.SetActive(show);
            }

			_actorVisible = show;
		}
			
		return show;
	}
	
	protected Vector3 GetPosition(float[] pos)
	{
		float x = pos[0];
		float z = pos[1];
		
		if (pos.Length == 2)
		{
			return new Vector3(x, 0, z);
		}
		
		return new Vector3(x, pos[2], z);
	}

    protected Quaternion GetRotation(float[] rotation)
	{
		Vector3 rot = new Vector3(rotation[0], rotation[1], rotation[2]);
		
		return Quaternion.Euler(rot);
	}


	#endregion
}

