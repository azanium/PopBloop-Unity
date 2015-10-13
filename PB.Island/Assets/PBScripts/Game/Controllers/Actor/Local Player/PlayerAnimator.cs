/**********************************************/
/*
/* Unity Project 
/*
/* (C) 2011, 2012 Suhendra Ahmad
/*
/**********************************************/

using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Client;
using PB.Common;

[RequireComponent(typeof(PBThirdPersonController))]
public class PlayerAnimator : ActorAnimator 
{
	#region MemVars & Props

    static public PlayerAnimator playerAnimation;
    protected Game _mmoGame;
    protected PBThirdPersonController _motionController;

	#endregion

	
	#region Mono Methods

    protected override void Awake()
    {
        base.Awake();

        if (playerAnimation == null)
        {
            playerAnimation = this;
        }
    }

    protected override void Start()
    {
        base.Start();

        _motionController = GetComponent<PBThirdPersonController>();

        if (_motionController == null)
        {
            Debug.LogError("Can't find motion controller on the Player prefab, please attach it");
        }
    }

    protected override void Update()
    {
        base.Update();

        float speed = 1.0f;

        if (IsMoving() && IsJumping() == false)
        {
            if (_motionController.IsSwimming)
            {
                Animate(PBConstants.ANIM_SWIM, AnimationAction.Play, WrapMode.Loop, speed, 0);
            }
            else
            {
                if (IsRunning() || IsThrotting())
                {
                   Animate(PBConstants.ANIM_RUN, AnimationAction.Play, WrapMode.Loop, speed, 0);
                }
                else
                {
                    Animate(PBConstants.ANIM_WALK, AnimationAction.Play, WrapMode.Loop, speed, 0);
                }
            }
        }
        else if (!IsMoving() && !IsJumping())
        {
            if (_motionController.IsSwimming)
            {
                Animate(PBConstants.ANIM_SWIM_IDLE, AnimationAction.Play, WrapMode.Loop, speed, 0);
            }
            else
            {
                // If no animation is being player by character, then we no choice but to play the idle animation
                if (_avatarController.IsAnimationPlayingAnything == false)
                {
                    PlayIdle();
                }
            }
        }
    }

	#endregion
	
	
	#region Custom Methods

    public void Initialize(Game mmoGame)
    {
        _mmoGame = mmoGame;
    }

    public void Animate(string animation, WrapMode wrapMode)
    {
        float speed = _motionController != null ? _motionController.GetAnimationSpeed() : 1f;

        Animate(animation, AnimationAction.Play, wrapMode, speed, 0);
    }

    public override void Animate(string animation, AnimationAction action, WrapMode wrapMode, float animationSpeed, int layer)
    {
        base.Animate(animation, action, wrapMode, animationSpeed, layer);

        _mmoGame.Avatar.Animate(animation, action, (byte)wrapMode, animationSpeed, layer);

        if (_avatarController == null)
        {
            Debug.Log("PlayerActor.Animate: character not found, please check the AvatarController being attached");
        }
    }

    public void PlayIdle()
    {
        Animate(PBConstants.ANIM_IDLE1, AnimationAction.Play, WrapMode.Loop, 1f, 0);
    }

    #region Motion Controller State Checks

    public bool IsMoving()
    {
        if (_motionController != null)
        {
            return _motionController.IsMoving();
        }

        return false;
    }

    public bool IsWalking()
    {
        if (_motionController != null)
        {
            return _motionController.IsWalking();
        }

        return false;
    }

    public bool IsJumping()
    {
        if (_motionController != null)
        {
            return _motionController.IsJumping();
        }

        return false;
    }

    public bool IsJumpReachedApex()
    {
        if (_motionController != null)
        {
            return _motionController.HasJumpReachedApex();
        }

        return false;
    }

    public float JumpSpeed
    {
        get
        {
            if (_motionController != null)
            {
                return _motionController.jumpAnimationSpeed;
            }
            return 1.0f;
        }
    }

    public float JumpLandSpeed
    {
        get
        {
            if (_motionController != null)
            {
                return _motionController.landAnimationSpeed;
            }
            return 1.0f;
        }
    }

    public bool IsRunning()
    {
        if (_motionController != null)
        {
            return _motionController.IsRunning();
        }

        return false;
    }

    public bool IsThrotting()
    {
        return _motionController != null ? _motionController.IsThrotting() : false;
    }

    #endregion

    #endregion


    #region Message's Events

    public void PlayerHasStopped()
    {
        PlayIdle();
    }

    public void DidJump()
    {    
        Animate(PBConstants.ANIM_JUMP, AnimationAction.Play, WrapMode.ClampForever, JumpSpeed, 0);
    }

    public void DidJumpReachApex()
    {
        Animate(PBConstants.ANIM_JUMP, AnimationAction.Play, WrapMode.ClampForever, -JumpLandSpeed, 0);
    }

    public void DidLand()
    {
        if (IsMoving() == false)
        {
            PlayIdle();
        }
    }

    #endregion
}
