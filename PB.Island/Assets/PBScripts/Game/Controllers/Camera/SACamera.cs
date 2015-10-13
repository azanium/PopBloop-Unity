using UnityEngine;
using System.Collections;
using PB.Common;

public class SACamera : MonoBehaviour
{
    public Transform TargetLookAt;

    public float Distance = 10f;
    public float DistanceMin = 3f;
    public float DistanceMax = 25;
    public float DistanceSmooth = 0.05f;
    public float MouseXSensitivity = 5f;
    public float MouseYSensitivity = 5f;
    public float MouseWheelSensitivity = 5f;
    public float YAngleMinLimit = -40f;
    public float YAngleMaxLimit = 80f;
    public float XSmooth = 0.05f;
    public float YSmooth = 0.1f;
    public float DistanceOcclusionStep = 0.1f;
    public int MaxOcclusionCheck = 10;
    public float DistanceResumeSmooth = 1f;
    public float autoRotateCameraSpeed = 3f;

    private float _mouseX = 0f;
    private float _mouseY = 0f;
    private float _desiredMouseX = 0f;
    private float _desiredMouseY = 0f;
    private float _startDistance = 5f;
    private float _desiredDistance = 0f;
    private float _velDistance = 0f;
    private Vector3 _position = Vector3.zero;
    private Vector3 _desiredPosition = Vector3.zero;
    private float _velX = 0f;
    private float _velY = 0f;
    private float _velZ = 0f;
    private Camera _camera;
    private float _distanceSmooth = 0f;
    private float _preOccludedDistance = 0f;

    private bool _autoRotateCameraBehindTarget = false;

    void Start()
    {
        if (TargetLookAt == null)
        {
            TargetLookAt = transform;
        }

        _camera = Camera.main;

        if (_camera == null)
        {
            Debug.LogError("SACamera: No Main camera exist");
        }

        _startDistance = Distance;

        Reset();
    }

    void LateUpdate()
    {
        if (_camera == null)
        {
            return;
        }

        HandleInput();

        int count = 0;
        do
        {
            CalculateDesiredPosition();
            count++;

        } while (IsCameraOccluded(count));

        UpdatePosition();
    }

    public void Reset()
    {
        _desiredMouseX = _mouseX = TargetLookAt.rotation.eulerAngles.y;
        _desiredMouseY = _mouseY = 0f;
        Distance = _startDistance;
        _desiredDistance = Distance;
        _preOccludedDistance = Distance;
        _autoRotateCameraBehindTarget = false;
    }

    private void HandleInput()
    {
        float wheelDeadZone = 0.01f;
        float wheel = Input.GetAxis("Mouse ScrollWheel");
        if (wheel < -wheelDeadZone || wheel > wheelDeadZone)
        {
            _desiredDistance = Mathf.Clamp(Distance - wheel * MouseWheelSensitivity, DistanceMin, DistanceMax);
            _preOccludedDistance = _desiredDistance;
            _distanceSmooth = DistanceSmooth;
        }

        if ((Input.GetMouseButton(1) && WindowManager.IsPointOutsideGUI(Input.mousePosition)) ||
            (Input.GetMouseButton(0) && (Input.GetKey(KeyCode.LeftControl) || Input.GetKey(KeyCode.RightControl))))
        {
            _mouseX += Input.GetAxis("Mouse X") * MouseXSensitivity;
            _mouseY -= Input.GetAxis("Mouse Y") * MouseYSensitivity;
            _autoRotateCameraBehindTarget = false;
        }

        if (Input.GetKey(KeyCode.R))
        {
            _desiredMouseX = TargetLookAt.rotation.eulerAngles.y;
            _desiredMouseY = 0f;
            _autoRotateCameraBehindTarget = true;
        }

        _mouseY = SAHelper.ClampAngle(_mouseY, YAngleMinLimit, YAngleMaxLimit);
    }

    private Vector3 CalculatePosition(float rotateX, float rotateY, float distance)
    {
        Vector3 direction = new Vector3(0f, 0f, -distance);
        Quaternion rotation = Quaternion.Euler(rotateX, rotateY, 0f);

        return TargetLookAt.position + (rotation * direction);
    }

    private bool IsCameraOccluded(int count)
    {
        bool isOccluded = false;

        float nearestHitDistance = CalculateCameraPoints(TargetLookAt.position, _desiredPosition);

        if (nearestHitDistance != -1)
        {
            if (count < MaxOcclusionCheck)
            {
                isOccluded = true;
                Distance -= DistanceOcclusionStep;
                if (Distance < 0.26f)
                {
                    Distance = 0.26f;
                }
            }
            else
            {
                Distance = nearestHitDistance - _camera.nearClipPlane;
                _distanceSmooth = DistanceResumeSmooth;
            }
            _desiredDistance = Distance;
        }

        return isOccluded;
    }

    private float CalculateCameraPoints(Vector3 from, Vector3 to)
    {
        float nearestDistance = -1f;

        SAHelper.NearClipPoints clipPoints = SAHelper.GetCameraNearClipPoints(to);

        Debug.DrawLine(from, to + _camera.transform.forward * -_camera.nearClipPlane, Color.red);
        Debug.DrawLine(from, clipPoints.LowerLeft, Color.blue);
        Debug.DrawLine(from, clipPoints.LowerRight, Color.blue);
        Debug.DrawLine(from, clipPoints.UpperLeft, Color.blue);
        Debug.DrawLine(from, clipPoints.UpperRight, Color.blue);

        int layerMask = ~(1 << LayerMask.NameToLayer("IgnoreCamera"));
        RaycastHit hit;
        if (Physics.Linecast(from, clipPoints.LowerLeft, out hit, layerMask) && hit.collider.tag != LevelConstants.TagPlayer)
        {
            nearestDistance = hit.distance;
        }

        if (Physics.Linecast(from, clipPoints.LowerRight, out hit, layerMask) && hit.collider.tag != LevelConstants.TagPlayer)
        {
            if (nearestDistance > hit.distance || nearestDistance == -1)
            {
                nearestDistance = hit.distance;
            }
        }

        if (Physics.Linecast(from, clipPoints.UpperLeft, out hit, layerMask) && hit.collider.tag != LevelConstants.TagPlayer)
        {
            if (nearestDistance > hit.distance || nearestDistance == -1)
            {
                nearestDistance = hit.distance;
            }
        }

        if (Physics.Linecast(from, clipPoints.UpperRight, out hit, layerMask) && hit.collider.tag != LevelConstants.TagPlayer)
        {
            if (nearestDistance > hit.distance || nearestDistance == -1)
            {
                nearestDistance = hit.distance;
            }
        }

        if (Physics.Linecast(from, to + _camera.transform.forward * -_camera.nearClipPlane, out hit, layerMask) && hit.collider.tag != LevelConstants.TagPlayer)
        {
            if (nearestDistance > hit.distance || nearestDistance == -1)
            {
                nearestDistance = hit.distance;
            }
        }

        return nearestDistance;
    }

    private void CalculateDesiredPosition()
    {
        ResetDistance();

        Distance = Mathf.SmoothDamp(Distance, _desiredDistance, ref _velDistance, _distanceSmooth);

        if (_autoRotateCameraBehindTarget)
        {
            _mouseX = Mathf.LerpAngle(_mouseX, _desiredMouseX, autoRotateCameraSpeed * Time.deltaTime);
            _mouseY = Mathf.LerpAngle(_mouseY, _desiredMouseY, autoRotateCameraSpeed * Time.deltaTime);
        }

        _desiredPosition = CalculatePosition(_mouseY, _mouseX, Distance);
    }

    private void ResetDistance()
    {
        if (_desiredDistance < _preOccludedDistance)
        {
            Vector3 pos = CalculatePosition(_mouseY, _mouseX, _preOccludedDistance);

            // Check if the original distance is no longer occluded
            float nearestDistance = CalculateCameraPoints(TargetLookAt.position, pos);

            if (nearestDistance == -1 || nearestDistance > _preOccludedDistance)
            {
                _desiredDistance = _preOccludedDistance;
            }
        }
    }

    private void UpdatePosition()
    {
        float x = Mathf.SmoothDamp(_position.x, _desiredPosition.x, ref _velX, XSmooth);
        float y = Mathf.SmoothDamp(_position.y, _desiredPosition.y, ref _velY, YSmooth);
        float z = Mathf.SmoothDamp(_position.z, _desiredPosition.z, ref _velZ, XSmooth);

        _position = new Vector3(x, y, z);

        Camera.main.transform.position = _position;
        Camera.main.transform.LookAt(TargetLookAt);
    }


}
