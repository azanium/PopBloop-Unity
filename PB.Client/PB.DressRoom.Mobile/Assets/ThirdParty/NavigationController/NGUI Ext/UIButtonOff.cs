using UnityEngine;
using System.Collections;

[AddComponentMenu("NGUI/Interaction/Button Off")]
public class UIButtonOff : MonoBehaviour
{
    public GameObject[] targets;
    public GameObject monoObject;
    public string method;

    void OnClick()
    {
        UIButton thisButton = GetComponent<UIButton>();
        if (thisButton != null)
        {
            bool isActive = true;
            thisButton.isEnabled = isActive;
            if (monoObject == null)
            {
                monoObject = gameObject;
            }

            if (monoObject != null && string.IsNullOrEmpty(method) == false)
            {
                monoObject.SendMessage(method, thisButton, SendMessageOptions.DontRequireReceiver);
            }

            foreach (GameObject target in targets)
            {
                if (target != null)
                {
                    UIButton targetButton = target.GetComponent<UIButton>();
                    if (targetButton != null)
                    {
                        targetButton.isEnabled = !isActive;
                    }
                }
            }
        }
    }

}
