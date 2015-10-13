using UnityEngine;
using System.Collections;

public class UIBillboard : MonoBehaviour
{
    #region MemVars & Props

    #endregion


    #region MonoBehavior's Methods

    protected void Awake()
    {
    }

	protected void Start() 
    {
	}
	
	protected void Update() 
    {
        if (Camera.mainCamera != null)
        {
            transform.LookAt(transform.position - Camera.mainCamera.transform.rotation * Vector3.back, Camera.mainCamera.transform.rotation * Vector3.up);
        }
        
    }
    private Vector3 CalcDirection(Vector3 from, Vector3 to)
    {
        return new Vector3(to.x - from.x, 0f, to.z - from.z);
    }


    protected void OnEnable()
    {
    }

    protected void OnDisable()
    {
    }

    #endregion


    #region Public Methods

    #endregion


    #region Private Methods

    #endregion
}
