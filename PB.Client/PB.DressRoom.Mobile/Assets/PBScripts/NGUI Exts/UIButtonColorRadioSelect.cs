//----------------------------------------------
//            NGUI: Next-Gen UI kit
// Copyright © 2011-2013 Tasharen Entertainment
//----------------------------------------------

using UnityEngine;

/// <summary>
/// Very basic script that will activate or deactivate an object (and all of its children) when clicked.
/// </summary>

[AddComponentMenu("NGUI/Interaction Exts/Button Color Radio Select")]
public class UIButtonColorRadioSelect : MonoBehaviour
{
    public Color enabledColor = Color.magenta;
    public Color normalColor = Color.black;

    public UIButtonColorRadioSelect[] siblings;

    public bool state = true;

    public enum Trigger
    {
        OnClick,
        OnMouseOver,
        OnMouseOut,
        OnPress,
        OnRelease,
        OnDoubleClick,
    }

    public GameObject target;
    public string functionName;
    public Trigger trigger = Trigger.OnClick;
    public bool includeChildren = false;

    void Start()
    {
        SetColor(state ? enabledColor : normalColor);

        siblings = transform.parent.GetComponentsInChildren<UIButtonColorRadioSelect>();
    }

    private void SetColor(Color color)
    {
        UILabel label = GetComponentInChildren<UILabel>();
        if (label != null)
        {
            label.color = color;
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
    
    public void SetEnabled()
    {
        if (siblings != null)
        {
            foreach (var button in siblings)
            {
                if (button.gameObject == gameObject) continue;
                button.SetColor(button.normalColor);
                button.state = false;
            }
        }

        if (!state)
        {
            state = true;
            SetColor(state ? enabledColor : normalColor);
        }
    }

    void Send()
    {
        if (siblings != null)
        {
            foreach (var button in siblings)
            {
                if (button.gameObject == gameObject) continue;
                button.SetColor(button.normalColor);
                button.state = false;
            }
        }
        if (!state)
        {
            state = true;
            SetColor(state ? enabledColor : normalColor);

            if (string.IsNullOrEmpty(functionName)) return;
            if (target == null) target = gameObject;

            if (includeChildren)
            {
                Transform[] transforms = target.GetComponentsInChildren<Transform>();

                for (int i = 0, imax = transforms.Length; i < imax; ++i)
                {
                    Transform t = transforms[i];
                    t.gameObject.SendMessage(functionName, gameObject, SendMessageOptions.DontRequireReceiver);
                }
            }
            else
            {
                target.SendMessage(functionName, gameObject, SendMessageOptions.DontRequireReceiver);
            }
        }

    }
}