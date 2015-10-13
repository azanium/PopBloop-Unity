using UnityEngine;
using System.Collections;

public class Gizmo : MonoBehaviour
{
    public GizmoHandle axisX;
    public GizmoHandle axisY;
    public GizmoHandle axisZ;

    public GizmoHandle.GizmoType gizmoType;

    public bool needUpdate = false;

    void Awake()
    {
        axisX.axis = GizmoHandle.GizmoAxis.X;
        axisY.axis = GizmoHandle.GizmoAxis.Y;
        axisZ.axis = GizmoHandle.GizmoAxis.Z;

        setType(gizmoType);
    }


    void Update()
    {
        needUpdate = (axisX.needUpdate || axisY.needUpdate || axisZ.needUpdate);
    }

    public void setType(GizmoHandle.GizmoType gizmoType)
    {
        axisX.setType(gizmoType);
        axisY.setType(gizmoType);
        axisZ.setType(gizmoType);
    }

    public void setParent(Transform other)
    {
        transform.parent = other;
        axisX.setParent(other);
        axisY.setParent(other);
        axisZ.setParent(other);
    }
}
