using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using System;

public class TabMenuController : MonoBehaviour
{
    #region MemVars & Props

    public enum KamarPasState
    {
        MyAvatar,
        KamarPas,
        Stream
    }

    public GameObject leftAnchorBar;
    public GameObject centerAnchorBar;
    public GameObject rightAnchorBar;

    public KamarPasState State = KamarPasState.KamarPas;
    private KamarPasState OldState = KamarPasState.KamarPas;

    private static TabMenuController navigatorController;

    public static void SetKamarPasState(KamarPasState state)
    {
        if (navigatorController != null)
        {
            navigatorController.State = state;
        }
    }

    public static KamarPasState GetKamarPasState()
    {
        if (navigatorController != null)
        {
            return navigatorController.State;
        }

        return KamarPasState.KamarPas;
    }

    #endregion


    #region Mono Methods

    private void Awake()
    {
        navigatorController = this;
    }

    private void Start()
    {
    }

    private void Update()
    {
        if (OldState != State)
        {
            OldState = State;
            UpdateState();
        }
    }

    #endregion


    #region Static & Internal Methods

    private void GetStateAnimation(out string showClip, out string hideClip)
    {
        int currentComp = State == KamarPasState.Stream ? 0 : State == KamarPasState.KamarPas ? 1 : 2;
        int oldComp = OldState == KamarPasState.Stream ? 0 : OldState == KamarPasState.KamarPas ? 1 : 2;

        showClip = KPConstants.KPAnimPushIn;
        hideClip = KPConstants.KPAnimPushOut;
        if (oldComp > currentComp)
        {
            showClip = KPConstants.KPAnimPopIn;
            hideClip = KPConstants.KPAnimPopOut;
        }
    }

    private void UpdateState()
    {
        switch (State)
        {
            case KamarPasState.Stream:
                SetNavState(leftAnchorBar, true);
                break;

            case KamarPasState.KamarPas:
                SetNavState(centerAnchorBar, true);
                break;

            case KamarPasState.MyAvatar:
                SetNavState(rightAnchorBar, true);
                break;
        }
    }

    private void showNavigator(bool state)
    {
        UIPanel panel = gameObject.GetComponent<UIPanel>();
        if (panel != null)
        {
            NGUITools.SetActive(panel.gameObject, state);
        }
    }

    public static void ShowNavigator(bool state)
    {
        if (navigatorController != null)
        {
            navigatorController.showNavigator(state);
        }
    }

    #endregion


    #region Events

    private void DisableButton(GameObject obj, bool state)
    {
        var button = obj.GetComponentInChildren<UIButton>();
        if (button != null)
        {
            button.isEnabled = state;
        }
    }

    private void SetNavState(GameObject anchor, bool state)
    {
        NGUITools.SetActive(leftAnchorBar, false);
        NGUITools.SetActive(centerAnchorBar, false);
        NGUITools.SetActive(rightAnchorBar, false);
        NGUITools.SetActive(anchor, state);
    }

    public void OnStore(GameObject sender)
    {
        State = KamarPasState.Stream;
        
        if (OldState != State)
        {
            UINavigationController.PushControllerAsFirst(typeof(StreamController));
        }
    }

    public void OnKamarPas(GameObject sender)
    {
        State = KamarPasState.KamarPas;
        
        if (OldState != State)
        {
            UINavigationController.PushControllerAsFirst(typeof(HomeController));
        }
    }

    public void OnMyAvatar(GameObject sender)
    {
        State = KamarPasState.MyAvatar;
        
        if (OldState != State)
        {
            UINavigationController.PushControllerAsFirst(typeof(MyAvatarController));
        }
    }

    #endregion
}
