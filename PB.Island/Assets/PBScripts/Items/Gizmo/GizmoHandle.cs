using UnityEngine;
using System.Collections;

public class GizmoHandle : MonoBehaviour
{
    public GameObject positionEnd;
    public GameObject rotationEnd;
    public GameObject scaleEnd;
    public float moveSensitivity = 2f;
    public float rotationSensitivity = 64f;
    public bool needUpdate = false;

    public enum GizmoControl
    {
        Horizontal = 0,
        Vertical = 1,
        Both = 2
    }

    public enum GizmoType
    {
        Position = 0,
        Rotation = 1,
        Scale = 2
    }

    public enum GizmoAxis
    {
        X = 0,
        Y = 1,
        Z = 2
    }

    public GizmoType gizmoType = GizmoType.Position;
    public GizmoControl control = GizmoControl.Both;
    public GizmoAxis axis = GizmoAxis.X;

    private bool mouseDown = false;
    private Transform otherTrans;

    void Awake()
    {
        otherTrans = transform.parent;
    }

    void Update()
    {

    }

    public void setParent(Transform other)
    {
        otherTrans = other;
    }

    public void setType(GizmoType gizmoType)
    {
        this.gizmoType = gizmoType;

        positionEnd.SetActive(gizmoType == GizmoType.Position);
        rotationEnd.SetActive(gizmoType == GizmoType.Rotation);
        scaleEnd.SetActive(gizmoType == GizmoType.Scale);
    }

    void OnMouseDown()
    {
        mouseDown = true;
        Debug.Log("down========");
    }

    void OnMouseUp()
    {
        mouseDown = false;
        needUpdate = true;
    }


    void OnMouseDrag()
    {
        float delta = 0;

        if (mouseDown)
        {
            switch (control)
            {
                case GizmoControl.Horizontal:
                    {
                        delta = Input.GetAxis("Mouse X") * Time.deltaTime;
                    }
                    break;

                case GizmoControl.Vertical:
                    {
                        delta = Input.GetAxis("Mouse Y") * Time.deltaTime;
                    }
                    break;

                case GizmoControl.Both:
                    {
                        delta = (Input.GetAxis("Mouse X") + Input.GetAxis("Mouse Y")) * Time.deltaTime;
                    }
                    break;
            }


            switch (gizmoType)
            {
                case GizmoType.Position:
                    {
                        delta *= moveSensitivity;
                        if (axis == GizmoAxis.X)
                        {
                            otherTrans.Translate(Vector3.right * delta);
                        }
                        else if (axis == GizmoAxis.Y)
                        {
                            otherTrans.Translate(Vector3.up * delta);
                        }
                        else if (axis == GizmoAxis.Z)
                        {
                            otherTrans.Translate(Vector3.forward * delta);
                        }
                    } break;


                case GizmoType.Scale:
                    {
                        delta *= moveSensitivity;

                        if (axis == GizmoAxis.X)
                        {
                            otherTrans.localScale = new Vector3(otherTrans.localScale.x + delta, otherTrans.localScale.y, otherTrans.localScale.z);
                        }
                        else if (axis == GizmoAxis.Y)
                        {
                            otherTrans.localScale = new Vector3(otherTrans.localScale.x, otherTrans.localScale.y + delta, otherTrans.localScale.z);
                        }
                        else if (axis == GizmoAxis.Z)
                        {
                            otherTrans.localScale = new Vector3(otherTrans.localScale.x, otherTrans.localScale.y, otherTrans.localScale.z + delta);
                        }
                    } break;


                case GizmoType.Rotation:
                    {
                        delta *= rotationSensitivity;
                        if (axis == GizmoAxis.X)
                        {
                            otherTrans.Rotate(Vector3.right * delta);
                        }
                        else if (axis == GizmoAxis.Y)
                        {
                            otherTrans.Rotate(Vector3.up * delta);
                        }
                        else if (axis == GizmoAxis.Z)
                        {
                            otherTrans.Rotate(Vector3.forward * delta);
                        }

                    } break;
            }

        }
    }

}
