//----------------------------------------------
//            NGUI: Next-Gen UI kit
// Copyright © 2011-2013 Tasharen Entertainment
//----------------------------------------------

using System.Collections;
using UnityEngine;

/// <summary>
/// Very basic script that will activate or deactivate an object (and all of its children) when clicked.
/// </summary>

[AddComponentMenu("NGUI/Interaction Exts/Button Activate Timeout")]
public class UIButtonActivateTimeout : MonoBehaviour
{
    public GameObject target;
    public bool state = true;
    public float timeout = 2;
    public bool enableButtonAfterTimeout = false;

    void OnClick()
    {
        if (target != null)
        {
            StartCoroutine(ShowWithTimeout());
        }
    }

    private IEnumerator ShowWithTimeout()
    {
        if (target != null) NGUITools.SetActive(target, state);

        yield return new WaitForSeconds(timeout);

        if (target != null) NGUITools.SetActive(target, !state);

        if (enableButtonAfterTimeout)
        {
            UIButton btn = gameObject.GetComponent<UIButton>();
            if (btn != null)
            {
                btn.isEnabled = true;
            }

            UIImageButton imageBtn = gameObject.GetComponent<UIImageButton>();
            if (imageBtn != null)
            {
                imageBtn.isEnabled = true;
            }
        }
    }
}