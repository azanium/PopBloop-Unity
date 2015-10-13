/**********************************************/
/*
/* Unity Project 
/*
/* (C) 2011, 2012 Suhendra Ahmad
/*
/**********************************************/

using UnityEngine;
using System;
using System.Collections;
using System.Collections.Generic;
using PB.Common;
using PB.Client;

[RequireComponent(typeof(AvatarController))]
[RequireComponent(typeof(Actor))]
public class ActorAnimator : MonoBehaviour 
{
	#region MemVars & Props

    static public List<ActorAnimator> actorAnimations;

    public class AnimationData
    {
        /// <summary>
        /// Current Animation Name 
        /// </summary>
        public string Animation = "";

        /// <summary>
        /// Current Animation Wrap Mode 
        /// </summary>
        public WrapMode Wrap = WrapMode.Loop;

        /// <summary>
        /// Current Animation Layer
        /// </summary>
        public int Layer = 0;

        /// <summary>
        /// Current Animation Action
        /// </summary>
        public AnimationAction Action = AnimationAction.Play;

        /// <summary>
        /// Current Animation Speed
        /// </summary>
        public float Speed = 1f;

        public bool IsPlaying = true;

        public AnimationData(int layer)
        {
            this.Layer = layer;
        }
    }

    protected AvatarController _avatarController;
    protected Actor _actor;

    protected Dictionary<int, AnimationData> currentAnimations = new Dictionary<int, AnimationData>()
    {
        { 0, new AnimationData(0) }, // Current Animation Data for Layer 0
        { 1, new AnimationData(1) }  // Current Animation Data for Layer 1
        // Add more if needed here
    };

	#endregion


    #region Mono Methods

    protected virtual void Awake()
    {
        if (actorAnimations == null)
        {
            actorAnimations = new List<ActorAnimator>();
        }
        actorAnimations.Add(this);
    }

    protected virtual void Start() 
	{
        _avatarController = GetComponent<AvatarController>();
        
        if (_avatarController == null)
        {
            Debug.LogWarning("No AvatarController found on the Foreign Character, please attach it");
        }

        _actor = GetComponent<Actor>();
        if (_actor == null)
        {
            Debug.LogWarning("No Actor attached to the Foreign Character, please attach it");
        }
	}

    protected virtual void Update() 
	{
        foreach (int key in currentAnimations.Keys)
        {
            if (currentAnimations[key].IsPlaying)
            {
                if (_avatarController.IsAnimationPlaying(currentAnimations[key].Animation) == false)
                {
                    BroadcastMessage("PlayerAnimationStopped", currentAnimations[key], SendMessageOptions.DontRequireReceiver);
                    currentAnimations[key].IsPlaying = false;
                }
            }
        }
	}
	
	#endregion
	
	
	#region Custom Methods

    public virtual void Animate(string animation, AnimationAction action, WrapMode animationWrap, float animationSpeed, int layer)
    {
        if (layer > 1)
        {
            Debug.LogWarning("Animation Layer > 1 is not support for now");
            return;
        }

        if (animation == "" || animation == null)
        {
            return;
        }

        if (_avatarController != null)
        {
            if (_avatarController.AnimationClips == null)
            {
                return;
            }

            if (_avatarController.AnimationClips.Contains(animation))
            {
                if (_avatarController.IsAnimationPlaying(animation) == false)
                {
                    PlayAnimation(animation, action, animationWrap, animationSpeed, layer);
                }
                else
                {
                    if (currentAnimations[layer].Animation != animation || currentAnimations[layer].Wrap != animationWrap || currentAnimations[layer].Layer != layer ||
                        currentAnimations[layer].Speed != animationSpeed || currentAnimations[layer].Action != action)
                    {
                        PlayAnimation(animation, action, animationWrap, animationSpeed, layer);
                    }
                }
            }
        }
    }

    private void PlayAnimation(string anim, AnimationAction action, WrapMode wrap, float speed, int layerIndex)
    {
        currentAnimations[layerIndex].Animation = anim;
        currentAnimations[layerIndex].Action = action;
        currentAnimations[layerIndex].Wrap = wrap;
        currentAnimations[layerIndex].Speed = speed;
        currentAnimations[layerIndex].Layer = layerIndex;
        currentAnimations[layerIndex].IsPlaying = true;

        _avatarController.Animate(anim, action, wrap, speed, layerIndex);
    }

    static public void Animate(Item item, string animation, AnimationAction action, WrapMode animationWrap, float animationSpeed, int layer)
    {
        foreach (ActorAnimator actorAnim in actorAnimations)
        {
            if (actorAnim == null)
            {
                continue;
            }

            if (actorAnim._actor == null)
            {
                continue;
            }

            if (actorAnim._actor.Item == item)
            {
                actorAnim.Animate(animation, action, animationWrap, animationSpeed, layer);

                // There's only 1 item for 1 actor, so it's unique, get out after we found it.
                break;
            }
        }
    }
    
	#endregion
}
