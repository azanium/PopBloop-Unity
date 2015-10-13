using UnityEngine;
using System.Collections;

public class ItemAnimateBase : ItemBase
{
    #region MemVars & Props

    public float respawnTime = 5000;
    public bool isAutoRespawn = true;
    protected bool useRotation = false;
    protected float rotationSpeed = 20.0f;

    protected bool _isRotating = false;
    protected float _oldTime = 0;
    protected bool _fadeOutRotation = false;

    #endregion


    #region Methods

    protected virtual void OnRespawn()
    {
    }

    protected override void Update()
    {
        base.Update();

        _oldTime += Time.deltaTime;

        if (_oldTime >= respawnTime * 0.001f && !isVisible)
        {
            _oldTime = 0;
            if (isAutoRespawn)
            {
                isVisible = true;

                this.gameObject.renderer.enabled = true;
                OnRespawn();
            }
        }

        if (_isRotating && useRotation)
        {
            transform.RotateAround(Vector3.up, rotationSpeed * Time.deltaTime);
        }

        if (!_isRotating && _fadeOutRotation && useRotation)
        {
            if (Quaternion.Angle(transform.rotation, Quaternion.identity) <= 0.001f)
            {
                _fadeOutRotation = false;
            }
            else
            {
                transform.rotation = Quaternion.Slerp(transform.rotation, Quaternion.identity, rotationSpeed * Time.deltaTime);
            }
        }
    }

    public virtual void ResetSpawn()
    {
        _oldTime = 0;
    }

    public override void OnProximityIn()
    {
        base.OnProximityIn();

        _isRotating = true;
    }

    public override void OnProximityOut()
    {
        base.OnProximityOut();

        _isRotating = false;
        _fadeOutRotation = true;
    }

    #endregion
}
