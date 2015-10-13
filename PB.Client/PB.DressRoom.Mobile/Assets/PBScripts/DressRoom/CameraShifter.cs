using UnityEngine;
using System.Collections;

public class CameraShifter : MonoBehaviour
{
    #region MemVars & Props

    public Transform headPosition;
    public Transform footPosition;
    public Transform defaultPosition;
    public Transform bodyPosition;
    public Transform centerPosition;

    public enum ZoomTargetArea
    {
        Default = 0,
        Head = 1,
        Foot = 2,
        Body = 3,
        Center = 4
    };

    private bool _isZooming = false;
    private Transform _targetTransform;

    #endregion


    #region MonoBehaviors

    void Start() 
    {
	}
	
	void Update() 
    {
        if (_isZooming)
        {
            if (Mathf.Abs(Vector3.Distance(transform.position, _targetTransform.position)) > 0.001f)
            {
                transform.position = Vector3.Lerp(transform.position, _targetTransform.position, 10f * Time.deltaTime);
            }
            else
            {
                _isZooming = false;
            }
        }
    }

    #endregion


    #region Methods

    public void ZoomTo(ZoomTargetArea zoomTarget)
    {
        switch (zoomTarget)
        {
            case ZoomTargetArea.Head:
                _targetTransform = headPosition;
                break;

            case ZoomTargetArea.Foot:
                _targetTransform = footPosition;
                break;

            case ZoomTargetArea.Default:
                _targetTransform = defaultPosition;
                break;

            case ZoomTargetArea.Body:
                _targetTransform = bodyPosition;
                break;

            case ZoomTargetArea.Center:
                _targetTransform = centerPosition;
                break;
        }
        _isZooming = true;
    }

    #endregion
}
