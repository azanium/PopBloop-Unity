using UnityEngine;
using System.Collections;

public class ItemMoveable : ItemBase
{
    #region MemVars & Props

    public bool isMovingHorizontal = true;
    public bool isMovingVertical = true;
    public bool isRotatable = true;
    public bool isEditing = false;

    public override ItemBaseType ItemType
    {
        get
        {
            return ItemBaseType.Moveable;
        }
    }

    #endregion


    #region Ctor

    public ItemMoveable()
        : base()
    { }

    #endregion


    #region MonoBehavior Methods

    // Use this for initialization
    protected override void Start()
    {
        base.Start();
    }

    // Update is called once per frame
    protected override void Update()
    {
        base.Update();

        if (Input.GetMouseButtonDown(0))
        {
            Ray ray = Camera.main.ScreenPointToRay(Input.mousePosition);
            RaycastHit hitInfo;
            if (Physics.Raycast(ray, out hitInfo, 100, ~(1 << 8)))
            {
                Debug.Log(hitInfo.collider.tag);
            }
        }
    }

    #endregion


    #region Methods



    #endregion

}
