using UnityEngine;
using System.Collections;

[AddComponentMenu("NGUI/Interaction/Button Message With Parameter")]
public class UIButtonRedirector : MonoBehaviour
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

    public GameObject redirectorControllerObject;
    public int index = 0;
    
    public Trigger trigger = Trigger.OnClick;
    public bool includeChildren = false;

    bool mStarted = false;
    bool mHighlighted = false;

    void Start() { mStarted = true; }

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
        if (redirectorControllerObject == null) redirectorControllerObject = gameObject;

        var functionName = "Redirect";

        if (includeChildren)
        {
            Transform[] transforms = redirectorControllerObject.GetComponentsInChildren<Transform>();

            for (int i = 0, imax = transforms.Length; i < imax; ++i)
            {
                Transform t = transforms[i];
                t.gameObject.SendMessage(functionName, index, SendMessageOptions.DontRequireReceiver);
            }
        }
        else
        {
            redirectorControllerObject.SendMessage(functionName, index, SendMessageOptions.DontRequireReceiver);
        }
    }
}
