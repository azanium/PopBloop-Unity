//----------------------------------------------
//            NGUI: Next-Gen UI kit
// Copyright © 2011-2013 Tasharen Entertainment
//----------------------------------------------

using UnityEngine;

/// <summary>
/// Very basic script that will activate or deactivate an object (and all of its children) when clicked.
/// </summary>

[AddComponentMenu("NGUI/Interaction Exts/Button Enabled")]
public class UIButtonEnabled : MonoBehaviour
{
    public GameObject target;
    public bool state = true;

    void OnClick() 
    {
        if (target != null)
        {
            UIButton btn = target.GetComponent<UIButton>();
            if (btn != null)
            {
                btn.isEnabled = state;
            }

            UIImageButton imageBtn = target.GetComponent<UIImageButton>();
            if (imageBtn != null)
            {
                imageBtn.isEnabled = state;
            }
            
        }
    }
}