//----------------------------------------------
//            NGUI: Next-Gen UI kit
// Copyright © 2011-2013 Tasharen Entertainment
//----------------------------------------------

using UnityEngine;

/// <summary>
/// Sends a message to the remote object when something happens.
/// </summary>

[AddComponentMenu("NGUI/Interaction/Button Dismiss Controller")]
public class UIButtonDismissController : MonoBehaviour
{
    public enum Trigger
    {
        OnClick,
        OnMouseOver,
        OnMouseOut,
        OnPress,
        OnRelease,
        OnDoubleClick,
    }

    /// <summary>
    /// Leave it blank if you already set the controller somewhere, it will auto detect
    /// </summary>
    public UINavigationController navigationController;
    public Trigger trigger = Trigger.OnClick;
    public bool dismissToFirstController = false;

    bool mStarted = false;
    bool mHighlighted = false;

    void Start()
    {
        mStarted = true;
        if (navigationController == null)
        {
            navigationController = UINavigationController.navigationController;
        }
    }

    void OnEnable() { if (mStarted && mHighlighted) OnHover(UICamera.IsHighlighted(gameObject)); }

    void OnHover(bool isOver)
    {
        if (enabled)
        {
            if (((isOver && trigger == Trigger.OnMouseOver) ||
                (!isOver && trigger == Trigger.OnMouseOut))) Send();
            mHighlighted = isOver;
        }
    }

    void OnPress(bool isPressed)
    {
        if (enabled)
        {
            if (((isPressed && trigger == Trigger.OnPress) ||
                (!isPressed && trigger == Trigger.OnRelease))) Send();
        }
    }

    void OnClick() { if (enabled && trigger == Trigger.OnClick) Send(); }

    void OnDoubleClick() { if (enabled && trigger == Trigger.OnDoubleClick) Send(); }

    void Send()
    {
        if (navigationController != null)
        {
            if (dismissToFirstController)
            {
                navigationController.dismissToFirstController();
            }
            else
            {
                navigationController.dismissController();
            }
        }
    }
}