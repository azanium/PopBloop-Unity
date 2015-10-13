using UnityEngine;
using System.Collections;
using PB.Client;

public class Gizmonizer : MonoBehaviour
{
    public float gizmoSize = 1.0f;

    private GameObject gizmoObj;
    private Gizmo gizmo;
    private GizmoHandle.GizmoType gizmoType = GizmoHandle.GizmoType.Position;

    void Start()
    {
        ResetGizmo();
    }

    void Update()
    {
        if (Input.GetKeyDown(KeyCode.Escape))
        {
            RemoveGizmo();
        }

        if (gizmo)
        {
            if (Input.GetKeyDown(KeyCode.Alpha1))
            {
                gizmoType = GizmoHandle.GizmoType.Position;
                gizmo.setType(gizmoType);
            }
            if (Input.GetKeyDown(KeyCode.Alpha2))
            {
                gizmoType = GizmoHandle.GizmoType.Rotation;
                gizmo.setType(gizmoType);
            }
            if (Input.GetKeyDown(KeyCode.Alpha3))
            {
                gizmoType = GizmoHandle.GizmoType.Scale;
                gizmo.setType(gizmoType);
            }
            if (gizmo.needUpdate)
            {
                ResetGizmo();
            }
        }
    }


    void OnMouseDown()
    {
        if (!gizmoObj)
        {
            ResetGizmo();
        }
    }

    public void RemoveGizmo()
    {
        if (gizmoObj)
        {
            gameObject.layer = 0;
            foreach (Transform child in transform)
            {
                child.gameObject.layer = 0;
            }
            Destroy(gizmoObj);
            Destroy(gizmo);
        }
    }

    public void ResetGizmo()
    {
        RemoveGizmo();
        gameObject.layer = 2;
        foreach (Transform child in transform)
        {
            child.gameObject.layer = 2;
        }
        gizmoObj = AssetsManager.Instantiate(Resources.Load("3D/Gizmo/GizmoAxis"), transform.position, transform.rotation);
        gizmoObj.transform.localScale *= gizmoSize;
        gizmo = gizmoObj.GetComponent<Gizmo>();
        gizmo.setParent(transform);
        gizmo.setType(gizmoType);
    }


}
