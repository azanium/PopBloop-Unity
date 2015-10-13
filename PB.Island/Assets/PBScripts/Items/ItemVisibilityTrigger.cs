using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class ItemVisibilityTrigger : MonoBehaviour
{
    #region MemVars & Props

    public enum VisibilityMode : byte
    {
        All,
        Parent,
        Children,
    };

    public AnimationClip actionAnimation;
    public VisibilityMode mode = VisibilityMode.All;

    protected List<string> _animations;
    protected Renderer _parentRenderer;
    protected List<Renderer> _childRenderers;

    #endregion


    #region Mono Methods

    private void Start()
    {
        Collider collider = GetComponent<Collider>();
        if (collider == null)
        {
            Debug.LogError("No Collider attached to ItemVisibility");
        }
        else
        {
            collider.isTrigger = true;
        }

        if (actionAnimation != null)
        {
            Animation animationComponent = GetComponent<Animation>();
            if (animationComponent == null)
            {
                Debug.LogError("No Animation component attached to ItemVisibility");
            }
            else
            {
                foreach (AnimationState state in this.gameObject.animation)
                {
                    _animations.Add(state.clip.name);
                }

                if (_animations.Contains(actionAnimation.name) == false)
                {
                    this.gameObject.animation.AddClip(actionAnimation, actionAnimation.name);
                }
            }
        }

        if (this.gameObject.GetComponent<Renderer>() != null)
        {
            _parentRenderer = this.gameObject.renderer;

            Renderer[] renderer = GetComponentsInChildren<Renderer>();

            _childRenderers = new List<Renderer>();

            foreach (Renderer childRender in renderer)
            {
                _childRenderers.Add(childRender);
            }
        }

        IsVisible = false;
    }

    protected void PlayActionAnimation(bool isPlay)
    {
        if (actionAnimation != null && GetComponent<Animation>() != null)
        {
            this.gameObject.animation[actionAnimation.name].wrapMode = WrapMode.Loop;

            if (isPlay)
            {
                this.gameObject.animation.CrossFade(actionAnimation.name);
            }
            else
            {
                this.gameObject.animation.Stop(actionAnimation.name);
            }
        }
    }

    private void OnTriggerEnter(Collider collision)
    {
        PlayActionAnimation(true);
        IsVisible = true;
    }

    private void OnTriggerExit(Collider collision)
    {
        PlayActionAnimation(false);
        IsVisible = false;
    }

    protected bool _isVisible = false;
    protected bool IsVisible
    {
        get { return _isVisible; }
        set
        {
            _isVisible = value;

            switch (mode)
            {
                case VisibilityMode.All:
                    {
                        _parentRenderer.enabled = value;
                        foreach (Renderer renderer in _childRenderers)
                        {
                            renderer.enabled = value;
                        }
                    }
                    break;

                case VisibilityMode.Parent:
                    {
                        _parentRenderer.enabled = value;
                    }
                    break;

                case VisibilityMode.Children:
                    {
                        foreach (Renderer renderer in _childRenderers)
                        {
                            renderer.enabled = value;
                        }
                    }
                    break;
            }
        }
    }

    #endregion
}
