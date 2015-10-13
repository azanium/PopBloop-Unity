using UnityEngine;
using System.Collections;

[AddComponentMenu("NGUI/Interaction/Button Selection")]
public class UIButtonSelection : MonoBehaviour
{ 
    public Transform tweenTarget;
    public Vector3 hover = new Vector3(1.1f, 1.1f, 1.1f);
    public Vector3 pressed = new Vector3(1.05f, 1.05f, 1.05f);
    public float duration = 0.2f;
    public bool selected = false;
    public GameObject invokeObject;
    public string method;
    public string tagText;

    Vector3 mScale;
    bool mInitDone = false;
    bool mStarted = false;
    bool mHighlighted = false;

    void Start() 
    { 
        mStarted = true;

        if (selected)
        {
            if (!mInitDone) Init();
            TweenScale.Begin(tweenTarget.gameObject, duration, Vector3.Scale(mScale, hover)).method = UITweener.Method.EaseInOut;
        }
        
    }

    void OnEnable() 
    { 
        if (mStarted && mHighlighted) OnHover(UICamera.IsHighlighted(gameObject));
        if (selected)
        {
            if (!mInitDone) Init();
            TweenScale.Begin(tweenTarget.gameObject, duration, Vector3.Scale(mScale, hover)).method = UITweener.Method.EaseInOut;
        }
        else
        {
            if (!mInitDone) Init();
            TweenScale.Begin(tweenTarget.gameObject, duration, Vector3.one).method = UITweener.Method.EaseInOut;
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
                (UICamera.IsHighlighted(gameObject) || selected  ? Vector3.Scale(mScale, hover) : mScale)).method = UITweener.Method.EaseInOut;
        }
    }

    void OnHover(bool isOver)
    {
        if (enabled)
        {
            if (!mInitDone) Init();
            if (!selected)
            {
                TweenScale.Begin(tweenTarget.gameObject, duration, isOver ? Vector3.Scale(mScale, hover) : mScale).method = UITweener.Method.EaseInOut;
            }
            else
            {
                TweenScale.Begin(tweenTarget.gameObject, duration, Vector3.Scale(mScale, hover)).method = UITweener.Method.EaseInOut;
            }
            mHighlighted = isOver;
        }
    }

    void OnClick()
    {
        if (!mInitDone) Init();

        SetSelected();

        if (invokeObject != null && string.IsNullOrEmpty(method) == false)
        {
            invokeObject.SendMessage(method, gameObject, SendMessageOptions.DontRequireReceiver);
        }
    }

    public void SetSelected()
    {
        Transform parent = transform.parent;
        if (parent != null)
        {
            Transform rootParent = parent.parent;
            if (rootParent == null)
            {
                rootParent = parent;
            }

            if (rootParent != null)
            {
                UIButtonSelection[] buttons = rootParent.GetComponentsInChildren<UIButtonSelection>(true);
                foreach (var button in buttons)
                {
                    button.selected = false;

                    TweenScale.Begin(button.gameObject, duration, Vector3.one).method = UITweener.Method.EaseInOut;
                }
            }
        }

        this.selected = true;
        if (tweenTarget == null)
        {
            tweenTarget = transform;
        }
        TweenScale.Begin(tweenTarget.gameObject, duration, hover).method = UITweener.Method.EaseInOut;
    }

}
