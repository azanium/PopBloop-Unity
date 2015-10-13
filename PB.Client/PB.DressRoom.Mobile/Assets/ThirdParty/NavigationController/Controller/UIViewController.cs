using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class UIViewController : MonoBehaviour
{
    #region MemVars & Props

    public virtual bool StackPushable
    {
        get { return true; }
    }

    public AnimationClip showForwardAnimation;
    public AnimationClip hideForwardAnimation;
    public AnimationClip showBackAnimation;
    public AnimationClip hideBackAnimation;

    public Dictionary<string, string> controllerParameters = new Dictionary<string, string>();

    public string controllerPath;

    #endregion


    #region Virtual Methods

    public virtual void Awake()
    {
    }

    public virtual void Start()
    {
    }

    public virtual void Update()
    {
    }

    public virtual void FixedUpdate()
    {
    }

    public virtual void OnEnable()
    {
    }

    public virtual void OnDisable()
    {
    }

    public virtual void OnAppear()
    {
    }

    /*public virtual void OnControllerParams(Dictionary<string, string> param)
    {
    }*/

    public virtual void OnAppeared()
    {
    }

    public virtual void OnDissapear()
    {
    }

    public virtual void OnDisappeared()
    {
    }

    #endregion


    #region Public Methods

    #endregion


    #region Internal Methods

    #endregion
}
