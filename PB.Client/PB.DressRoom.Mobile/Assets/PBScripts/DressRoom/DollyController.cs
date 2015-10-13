using UnityEngine;
using System.Collections;

public class DollyController : MonoBehaviour
{
    #region MemVars & Props

    public GameObject dollyReset;
    public GameObject dollyFeet;
    public GameObject dollyBody;
    public GameObject dollyHead;
    public GameObject capture;

    private CameraShifter cameraZoom;
    private bool isReady = false;

    #endregion


    #region Mono

    protected void Start()
    {
        if (dollyBody != null && dollyFeet != null && dollyHead != null &&
            dollyReset != null && capture != null)
        {
            isReady = true;
        }
        else
        {
            Debug.LogWarning("Dolly Buttons is net set on DollyController");
        }

        cameraZoom = Camera.main.GetComponent<CameraShifter>();
        if (cameraZoom == null)
        {
            Debug.LogWarning("No CameraShifter attached to the Main Camera, please attach it!");
        }
    }

    #endregion


    #region Private Methods

    private void DollyFeet()
    {
        if (isReady)
        {
            cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Foot);
        }
    }

    private void DollyBody()
    {
        if (isReady)
        {
            cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Body);
        }
    }

    private void DollyHead()
    {
        if (isReady)
        {
            cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Head);
        }
    }

    private void DollyReset()
    {
        if (isReady)
        {
            cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Default);
        }
    }

    private void Capture()
    {
        Debug.LogWarning("Capture");
    }

    #endregion
}
