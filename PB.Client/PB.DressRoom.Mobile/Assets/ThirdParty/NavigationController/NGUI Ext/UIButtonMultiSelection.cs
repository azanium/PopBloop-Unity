using UnityEngine;
using System.Collections;
using System.Collections.Generic;

[AddComponentMenu("NGUI/Interaction/Button Multi Selection")]
public class UIButtonMultiSelection : MonoBehaviour
{

    public Transform tweenTarget;
    public Vector3 hover = new Vector3(1.1f, 1.1f, 1.1f);
    public Vector3 pressed = new Vector3(1.05f, 1.05f, 1.05f);
    public float duration = 0.2f;
    public bool selected = false;
    public GameObject invokeObject;
    public string method;
    public int index = 0;

    Vector3 mScale;
    bool mInitDone = false;

    void Start()
    {
        if (enabled && selected)
        {
            if (!mInitDone) Init();
            TweenScale.Begin(tweenTarget.gameObject, duration, Vector3.Scale(mScale, hover)).method = UITweener.Method.EaseInOut;
        }
    }

    void OnDisable()
    {
        /*if (tweenTarget != null)
        {
            TweenScale tc = tweenTarget.GetComponent<TweenScale>();

            if (tc != null)
            {
                tc.scale = mScale;
                tc.enabled = false;
            }
        }*/
    }

    void Init()
    {
        mInitDone = true;
        if (tweenTarget == null) tweenTarget = transform;
        mScale = tweenTarget.localScale;
    }

    void OnPress(bool isPressed)
    {
        if (enabled)
        {
            if (!mInitDone) Init();
            TweenScale.Begin(tweenTarget.gameObject, duration, isPressed ? Vector3.Scale(mScale, pressed) :
                (UICamera.IsHighlighted(gameObject) || selected ? Vector3.Scale(mScale, hover) : mScale)).method = UITweener.Method.EaseInOut;
        }
    }

    void OnClick()
    {
        if (!mInitDone) Init();

        SetSelected();

        if (invokeObject != null && string.IsNullOrEmpty(method) == false)
        {
            invokeObject.SendMessage(method, SendMessageOptions.DontRequireReceiver);
        }
    }
     
    public void SetSelected()
    {
        Transform parent = transform.parent;
        if (parent != null)
        {
            UIButtonMultiSelection[] buttons = GetButtons(parent);
            int thresIndex = 0;
            foreach (UIButtonMultiSelection btn in buttons)
            {
                if (btn == this)
                {
                    break;
                }
                thresIndex++;
            }

            for (int i = 0; i < buttons.Length; i++)
            {
                var button = buttons[i];
                button.selected = false;
                TweenScale.Begin(button.gameObject, duration, Vector3.one).method = UITweener.Method.EaseInOut;
            }

            for (int i = 0; i <= thresIndex; i++)
            {
                var button = buttons[i];
                button.selected = true;
                TweenScale.Begin(button.gameObject, duration, hover).method = UITweener.Method.EaseInOut;
            }
        }
    }
	
	public void SetMultiSelected()
	{
        this.selected = true;
		TweenScale.Begin(gameObject, duration, hover).method = UITweener.Method.EaseInOut;
	}

    protected UIButtonMultiSelection[] GetButtons(Transform rootParent)
    {
        UIButtonMultiSelection[] buttons = rootParent.GetComponentsInChildren<UIButtonMultiSelection>(true);
        List<UIButtonMultiSelection> sortedList = new List<UIButtonMultiSelection>();

        foreach (UIButtonMultiSelection btn in buttons)
        {
            int idx = btn.index;

            int insertIndex = 0;
            foreach (UIButtonMultiSelection sel in sortedList)
            {
                if (sel.index >= idx)
                {
                    break;
                }
                insertIndex++;
            }
                
            sortedList.Insert(insertIndex, btn);
        }

        return sortedList.ToArray();
    }
}
