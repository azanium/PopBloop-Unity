/// <summary>
/// 
/// Item Base Type
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Client;
using PB.Common;

/// <summary>
/// All In Game interaction item derived from this class
/// </summary>
public class ItemBase : MonoBehaviour
{
    #region MemVars & Props

    public AnimationClip animationIdle;
    public WrapMode animationIdleWrap = WrapMode.Loop;
    public AnimationClip animationOpen;
    public WrapMode animationOpenWrap = WrapMode.Loop;
    public AnimationClip animationClose;
    public WrapMode animationCloseWrap = WrapMode.Once;
    
    public bool useAnimation = true;
    public bool isSparkle = false;
    public bool isVisible = true;
    public float Distance = 1.5f;
    public string hintDescription = "No Hint, Please set the Hint Description!!!";
    public bool drawGizmo = true;

    public virtual ItemBaseType ItemType
    {
        get { return ItemBaseType.Invalid; }
    }

    public virtual ItemActionType ActionType
    {
        get { return ItemActionType.Pickup; }
    }

    public float actionAngle = 45f;

    protected bool _isHovered = false;
    protected GameObject _sparkle;
    protected GameObject _dim;
    protected bool _isProximityBreached = false;
    private bool _isProximityCalled = false;
    //private bool _drawHintText = false;
    //private bool _inProximity = false;
    private GUIStyle _hintStyle;
    protected Dictionary<string, AnimationClip> _animations = new Dictionary<string, AnimationClip>();
    protected string _currentAnimationPlaying = "";

    protected bool _cameraIsFacingMe = false;
    protected bool _rendererVisible = true;
    protected bool _inProximity = false;
    protected bool _readyToUse = true;

    #endregion


    #region MMO Sync MemVars & Props

    public bool syncTransforms = false;
    public bool syncAnimations = false;
    public int syncInterval = 200;

    protected Vector3 _lastMovePosition;
    protected Vector3 _lastMoveRotation;
    protected float _nextMoveTime;
    protected bool _pendingUpdatePosition = false;
    protected Vector3 _newPosition;
    protected Vector3 _newRotation;
    protected bool _updateNewPosition;


    #endregion


    #region Mono Methods

    protected virtual void Start()
    {
        _animations.Clear();
        if (gameObject.GetComponent<Animation>() != null)
        {
            foreach (AnimationState state in gameObject.animation)
            {
                if (_animations.ContainsKey(state.clip.name) == false)
                {
                    _animations.Add(state.clip.name, state.clip);
                }
            }

            Animate(animationOpen, animationOpenWrap, false);
            Animate(animationClose, animationCloseWrap, false);
        }

        _hintStyle = new GUIStyle();
        _hintStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/hintBox");
        _hintStyle.font = ResourceManager.Instance.LoadFont("GUI/Font/DroidSans");
        _hintStyle.normal.textColor = Color.white;
        _hintStyle.fontStyle = FontStyle.Bold;
        _hintStyle.alignment = TextAnchor.MiddleCenter;
    }

    protected virtual void Update()
    {
        DetectProximity();

        UpdatePositions();

        Camera mainCam = Camera.mainCamera;
        if (mainCam != null)
        {
            if (_inProximity)//CalcDistance(transform.position, PBGameMaster.PlayerPosition) <= Distance)
            {
                #region Item Raycast
                float x = Screen.width * 0.5f;
                float y = Screen.height * 0.5f;

                float rayOffset = 70f;
                Ray rayCenter = mainCam.ScreenPointToRay(new Vector3(x, y));
                Ray rayRight = mainCam.ScreenPointToRay(new Vector3(x + rayOffset, y));
                Ray rayLeft = mainCam.ScreenPointToRay(new Vector3(x - rayOffset, y));
                Ray rayUp = mainCam.ScreenPointToRay(new Vector3(x, y + rayOffset));
                Ray rayDown = mainCam.ScreenPointToRay(new Vector3(x, y - rayOffset));

                Ray rayUpRight = mainCam.ScreenPointToRay(new Vector3(x + rayOffset, y + rayOffset));
                Ray rayUpLeft = mainCam.ScreenPointToRay(new Vector3(x - rayOffset, y + rayOffset));
                Ray rayDownLeft = mainCam.ScreenPointToRay(new Vector3(x - rayOffset, y - rayOffset));
                Ray rayDownRight = mainCam.ScreenPointToRay(new Vector3(x + rayOffset, y - rayOffset));

                float d = Vector3.Distance(gameObject.transform.position, mainCam.transform.position);

                int layermask = ~(1 << LayerMask.NameToLayer("Player"));
                
                _cameraIsFacingMe = false;
                if (Raycast(this.gameObject, rayCenter, d * 2, layermask))
                {
                    _cameraIsFacingMe = true;
                }

                if (Raycast(this.gameObject, rayLeft, d * 2, layermask))
                {
                    _cameraIsFacingMe = true;
                }

                if (Raycast(this.gameObject, rayRight, d * 2, layermask))
                {
                    _cameraIsFacingMe = true;
                }

                if (Raycast(this.gameObject, rayUp, d * 2, layermask))
                {
                    _cameraIsFacingMe = true;
                }

                if (Raycast(this.gameObject, rayDown, d * 2, layermask) || 
                    Raycast(this.gameObject, rayUpLeft, d * 2, layermask) ||
                    Raycast(this.gameObject, rayUpRight, d * 2, layermask) ||
                    Raycast(this.gameObject, rayDownLeft, d * 2, layermask) ||
                    Raycast(this.gameObject, rayDownRight, d * 2, layermask))
                {
                    _cameraIsFacingMe = true;
                }
                #endregion

                if (_cameraIsFacingMe)
                {
                    UIActionView.EnableItem(this);
                }
                else
                {
                    UIActionView.DisableItem(this);
                }
            }
            else
            {
                UIActionView.DisableItem(this);
            }
        }

        // Play Idle Animations
        if (gameObject.animation != null)
        {
            if (gameObject.animation.isPlaying == false)
            {
                Animate(animationIdle, animationIdleWrap, true);
            }
        }
    }

    private bool Raycast(GameObject target, Ray ray, float length, int layermask)
    {
        RaycastHit hitInfo;
        if (Physics.Raycast(ray, out hitInfo, length, layermask))
        {
            if (hitInfo.collider.gameObject == target)
            {
                return true;
            }
        }

        return false;
    }

    protected virtual void OnGUI()
    {
        /*if (_drawHintText && !string.IsNullOrEmpty(hintDescription))
        {
            Vector2 pos = Event.current.mousePosition;

            GUIContent content = new GUIContent(hintDescription);

            float minWidth, maxWidth;
            _hintStyle.CalcMinMaxWidth(content, out minWidth, out maxWidth);
            float height = _hintStyle.CalcHeight(content, maxWidth);

            GUI.Box(new Rect(pos.x, pos.y - height, maxWidth + 30, height), content, _hintStyle);
        }*/
    }

    protected void DetectProximity()
    {
        Vector3 player = new Vector3(PBGameMaster.PlayerPosition.x, 0, PBGameMaster.PlayerPosition.z);
        Vector3 item = new Vector3(gameObject.transform.position.x, 0, gameObject.transform.position.z);

        float proximity = Mathf.Abs(Vector3.Distance(player, item));

        if (proximity <= Distance)
        {
            if (!_isProximityCalled)
            {
                OnProximityIn();
                _isProximityCalled = true;
            }
        }
        else
        {
            if (_isProximityCalled)
            {
                OnProximityOut();
                _isProximityCalled = false;
            }
        }
    } 

    protected void OnBecameVisible()
    {
        _rendererVisible = true;
    }

    protected void OnBecameInvisible()
    {
        _rendererVisible = false;
    }

    #endregion


    #region Internal Methods

    public void SetPosition(Vector3 position, Vector3 rotation)
    {
        _newPosition = position;
        _newRotation = rotation;
        _updateNewPosition = true;
        _pendingUpdatePosition = true;
    }

    protected void UpdatePositions()
    {
        if (PBGameMaster.Game == null || syncTransforms == false || PBGameMaster.GameState != GameStateType.WorldEntered)
        {
            return;
        }

        Game game = PBGameMaster.Game;

        float time = Time.time;
        if (time >= _nextMoveTime && !_pendingUpdatePosition)
        {
            Vector3 position = this.transform.position;
            Vector3 rotation = this.transform.rotation.eulerAngles;

            if ((_lastMovePosition != position || _lastMoveRotation != rotation))
            {
                if (Vector3.Distance(position, _lastMovePosition) > 0.1f || (Mathf.Abs(Quaternion.Angle(Quaternion.Euler(_lastMoveRotation), Quaternion.Euler(rotation))) > 0.05f))
                {
                    game.Avatar.GameItemMove(this.gameObject.name, PlayerActor.GetPosition(position), PlayerActor.GetRotation(rotation));

                    _lastMovePosition = position;
                    _lastMoveRotation = rotation;
                }
            }

            _nextMoveTime = time + (syncInterval * 0.001f);
        }

        if (_updateNewPosition)
        {
            Vector3 position = this.transform.position;
            Vector3 rotation = this.transform.rotation.eulerAngles;

            // Smooth interpolate the current position into foreign new position
            bool positionUpdateDone = false;
            if (Mathf.Abs(Vector3.Distance(_newPosition, position)) > 0.05f)
            {
                transform.position = Vector3.Lerp(position, _newPosition, Time.deltaTime * 7.0f);
            }
            else
            {
                positionUpdateDone = true;
            }

            // Smooth interpolate the current rotation into foreign new rotation
            bool rotationUpdateDone = false;
            if (Mathf.Abs(Quaternion.Angle(Quaternion.Euler(rotation), Quaternion.Euler(_newRotation))) > 0.05f)
            {
                transform.rotation = Quaternion.Slerp(Quaternion.Euler(rotation), Quaternion.Euler(_newRotation), Time.deltaTime * 7.0f);
            }
            else
            {
                rotationUpdateDone = true;
            }

            // If all interpolation done, then reset 
            if (positionUpdateDone && rotationUpdateDone)
            {
                _updateNewPosition = false;
                _pendingUpdatePosition = false;
                _lastMovePosition = transform.position;
                _lastMoveRotation = transform.rotation.eulerAngles;
            }
        }
    }

    protected void OnDrawGizmos()
    {
        if (drawGizmo)
        {
            Gizmos.color = new Color(1f, 0f, 0f, 0.3f);
            Gizmos.DrawSphere(transform.position, Distance);
        }
    }

    protected void Animate(AnimationClip clip, WrapMode wrapMode, bool isPlay)
    {
        Animate(clip, wrapMode, isPlay, true);
    }

    protected void Animate(AnimationClip clip, WrapMode wrapMode, bool isPlay, bool syncAnimation)
    {
        if (clip == null)
        {
            return;
        }

        if (clip != null && this.gameObject.animation != null && _currentAnimationPlaying.ToLower() != clip.name.ToLower())
        {
            if (_animations.ContainsKey(clip.name) == false)
            {
                _animations.Add(clip.name, clip);

                this.gameObject.animation.AddClip(clip, clip.name);
            }

            this.gameObject.animation[clip.name].wrapMode = wrapMode;

            if (isPlay)
            {
                this.gameObject.animation.CrossFade(clip.name);
            }

            // Store the current animation playing
            _currentAnimationPlaying = clip.name;

            // Only sync when game state is world entered and syncAnimations enabled
            if (PBGameMaster.Game != null && syncAnimation && this.syncAnimations && PBGameMaster.GameState == GameStateType.WorldEntered)
            {
                PBGameMaster.Game.Avatar.GameItemAnimate(this.gameObject.name, clip.name, isPlay ? AnimationAction.Play : AnimationAction.Stop, (byte)wrapMode, 1f);
            }
        }
    }

    protected void Animate(string animation, WrapMode wrapMode, bool isPlay)
    {
        Animate(animation, wrapMode, isPlay, true);
    }

    protected void Animate(string animation, WrapMode wrapMode, bool isPlay, bool syncAnimation)
    {
        if (string.IsNullOrEmpty(animation))
        {
            return;
        }

        if (this.gameObject.animation != null && _currentAnimationPlaying.ToLower() != animation.ToLower() && isPlay)
        {
            this.gameObject.animation[animation].wrapMode = wrapMode;
            this.gameObject.animation.CrossFade(animation);

            // Store the current animation playing
            _currentAnimationPlaying = animation;

            // Only sync when game state is world entered and syncAnimations enabled
            if (PBGameMaster.Game != null && syncAnimation && this.syncAnimations && PBGameMaster.GameState == GameStateType.WorldEntered)
            {
                PBGameMaster.Game.Avatar.GameItemAnimate(this.gameObject.name, animation, isPlay ? AnimationAction.Play : AnimationAction.Stop, (byte)wrapMode, 1f);
            }
        }
    }



    protected void InitSparkle(bool enabled)
    {
        _sparkle = (GameObject)AssetsManager.Instantiate(Resources.Load("GUI/Interaction/Interact_Sparkle"));
        _sparkle.transform.parent = transform;
        _sparkle.transform.localPosition = Vector3.zero;
        _sparkle.transform.localRotation = Quaternion.identity;

        /*_dim = (GameObject)Instantiate(Resources.Load("GUI/Interaction/Interact_Dim"));
        _dim.transform.parent = transform;
        _dim.transform.localPosition = Vector3.zero;
        _dim.transform.localRotation = Quaternion.identity;
        */

        EnableSparkle(enabled);
    }

    protected float CalcDistance(Vector3 from, Vector3 to)
    {
        return Vector3.Distance(new Vector3(from.x, 0f, from.z), new Vector3(to.x, 0f, to.z));
    }

    protected Vector3 CalcDirection(Vector3 from, Vector3 to)
    {
        return new Vector3(to.x - from.x, 0f, to.z - from.z);
    }

    #endregion


    #region Public Methods

    public bool IsReadyToUse()
    {
        return _readyToUse;
    }

    public bool IsCameraFacingMe()
    {
        return _cameraIsFacingMe;
    }

    public void SetAnimation(string animationName, AnimationAction action, byte wrapMode, float speed)
    {
        if (_animations.ContainsKey(animationName))
        {
            AnimationClip clip = _animations[animationName];
            Animate(clip, (WrapMode)wrapMode, action == AnimationAction.Play, false);
        }
    }

    public virtual void EnableSparkle(bool enabled)
    {
        ParticleEmitter[] emitters = _sparkle.gameObject.GetComponentsInChildren<ParticleEmitter>();

        foreach (ParticleEmitter emitter in emitters)
        {
            emitter.emit = enabled;
        }

        /*emitters = _dim.gameObject.GetComponentsInChildren<ParticleEmitter>();
		
        foreach (ParticleEmitter emitter in emitters)
        {
            emitter.emit = enabled;
        }*/
    }

    public virtual void OnMouseEnter()
    {
        //_drawHintText = true;
    }

    public virtual void OnMouseExit()
    {
        //_drawHintText = false;
    }

    public virtual void OnAction(GameControllerBase game)
    {
    }

    public virtual void OnProximityIn()
    {
        _inProximity = true;

        if (isSparkle && isVisible)
        {
            EnableSparkle(true);
        }

        if (useAnimation)
        {
            Debug.Log("AnimOPen: " + animationOpen);
            Animate(animationOpen, animationOpenWrap, true);
        }
    } 

    public virtual void OnProximityOut()
    {
        _inProximity = false;

        if (isSparkle)
        {
            EnableSparkle(false);
        }

        if (useAnimation)
        {
            Animate(animationClose, animationCloseWrap, true);
        }
    }

    #endregion
}
