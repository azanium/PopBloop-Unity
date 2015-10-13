using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;

public class Rotator : MonoBehaviour {

    public float angleSpeed = 0.05f;

    public enum eRotationDirection
    {
        Left = 0,
        Right = 1
    };
    public eRotationDirection RotationDirection = eRotationDirection.Left;

    private bool _isHovered = false;
    private DressRoom _dressRoom;
    private bool _isRotating = false;
    private bool _enableGUI = true;

	void Start() 
    {
        _dressRoom = GameObject.Find("_DressRoom").GetComponent<DressRoom>();// transform.root.gameObject.GetComponent<DressRoom>();

        if (_dressRoom == null)
        {
            Debug.LogError("No DressRoom script is found, please check the scene");
        }
	}
	
	void Update() 
    {
        if (!_enableGUI)
        {
            return;
        }


        if (Input.GetMouseButtonDown(0))
        {
            if (_isHovered)
            {
                _isRotating = true;
            }
            else
            {
            }
        }

        if (Input.GetMouseButtonUp(0))
        {
            if (_isHovered)
            {
                _isRotating = false;
            }
            else
            {
            }
        }

        if (_dressRoom != null && _isRotating)
        {
            if (_dressRoom.Character != null)
            {
                //_dressRoom.Character.transform.Rotate(Vector3.up, (RotationDirection == eRotationDirection.Left ? angleSpeed : -angleSpeed) * Time.deltaTime);
            }
        }
         
	}

    /*void OnMouseEnter()
    {
        _isHovered = true;
    }

    void OnMouseExit()
    {
        _isHovered = false;
        _isRotating = false;
    }*/

    void OnEnable()
    {
        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGuiEnable);
    }

    void OnDisable()
    {
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGuiEnable);
    }

    void OnGuiEnable(bool enabled)
    {
        _enableGUI = enabled;

        renderer.enabled = enabled;
    }
}
