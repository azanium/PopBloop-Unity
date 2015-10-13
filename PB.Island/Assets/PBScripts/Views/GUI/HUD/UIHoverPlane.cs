using UnityEngine;
using System.Collections;

public class UIHoverPlane : MonoBehaviour
{
    #region MemVars & Props

    public enum eHoverType
    {
        None = 0,
        QuestionMark = 1,
        Exclamation = 2
    };

    public eHoverType HoverType = eHoverType.QuestionMark;

    #endregion


    #region MonoBehavior's Methods

    void Start()
    {
        SetupHoverType(HoverType);
    }

    void LateUpdate()
    {
        SetupHoverType(HoverType);
    }

    public void SetupHoverType(eHoverType hoverType)
    {
        switch (hoverType)
        {
            case eHoverType.None:
                {
                    this.gameObject.renderer.enabled = false;
                } break;

            case eHoverType.Exclamation:
                {
                    this.gameObject.renderer.material.SetTextureOffset("_MainTex", new Vector2(0f, 0.5f));
                    this.gameObject.renderer.enabled = true;
                } break;

            case eHoverType.QuestionMark:
                {
                    this.gameObject.renderer.enabled = true;
                    this.gameObject.renderer.material.SetTextureOffset("_MainTex", new Vector2(0f, 0.0f));
                } break;
        }
    }

    #endregion
}
