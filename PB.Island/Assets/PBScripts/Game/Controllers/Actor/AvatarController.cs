using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Client;
using PB.Common;
using PB.Game;

public class AvatarController : MonoBehaviour
{
    #region MemVars & Props

    static public List<AvatarController> avatarControllers;

    private CharacterGenerator generator = new CharacterGenerator();
    public List<string> AnimationClips
    {
        get
        {
            if (generator != null)
            {
                return generator.AnimationClips;
            }

            return null;
        }
    }

	public GameObject characterObject = null;
    
    #endregion


    #region MonoBehavior

    private void Awake()
    {
        if (avatarControllers == null)
        {
            avatarControllers = new List<AvatarController>();
        }
        avatarControllers.Add(this);
    }

	private void Start() 
    {
        StartCoroutine(GenerateAvatar());
	}

    private bool _isGenerateAvatarError = false;

    private void Update()
    {
        if (_isGenerateAvatarError == true)
        {
            _isGenerateAvatarError = false;

            Debug.LogWarning("AvatarController: Retrying to generate Avatar");

            if (generator != null)
            {
                StartCoroutine(GenerateAvatar());
            }
        }
    }

    #endregion


    #region Methods

    private IEnumerator GenerateAvatar()
    {
        Actor actor = GetComponent<Actor>();

        if (actor == null)
        {
            Debug.LogError("AvatarController: Actor component is not found, please attach it!");
            yield return null;
        }

        string avatarConfigURL = PopBloopSettings.GetAvatarConfigurationURL(actor.Item.Id) + "/" + Time.frameCount.ToString();

        WWW avatarConfigWWW = new WWW(avatarConfigURL);

        yield return avatarConfigWWW;

        if (avatarConfigWWW.error != null)
        {
            Debug.LogError("AvatarController: Failed to get Avatar Configuration from Server: " + avatarConfigWWW.error);
            
            _isGenerateAvatarError = true;

            yield return null;
        }
        else
        {

            string avatarConfig = avatarConfigWWW.text.Trim();

            //if (PopBloopSettings.useLogs)
            {
                Debug.Log("AvatarController: Get Avatar Configuration: " + avatarConfigURL + " => Avatar JSON: " + avatarConfig);
            }

            //avatarConfigWWW.Dispose();

            generator.ChangeCharacterFromJSON(avatarConfig);
            //generator.ChangeCharacterFromJSON("[{'tipe':'gender','element':'male_base'},{'tipe':'face','element':'male_face-1','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'hair','element':'male_hair-2','material':'male_hair-2_blond'},{'tipe':'body','element':'male_top-2','material':'male_top-2_green'},{'tipe':'pants','element':'male_pants-1','material':'male_pants-1_green'},{'tipe':'shoes','element':'male_shoes-2','material':'male_shoes-2_red'}]");		

            string animationURL = PopBloopSettings.GetAnimationsConfigurationURL(actor.Item.Id) + "/" + Time.frameCount.ToString();
            WWW animationWWW = new WWW(animationURL);
            yield return animationWWW;

            if (animationWWW.error != null)
            {
                Debug.LogError("AvatarController: Failed to get Animation Configuration from Server: " + animationURL);
                 
                _isGenerateAvatarError = true;
                
                yield return null;
            }
            else
            {
                string animJSON = animationWWW.text.Trim();

                //if (PopBloopSettings.useLogs)
                {
                    Debug.Log(string.Format("AvatarController: GetAnimation Configuration: {0} => Animation JSON: {1} ", animationURL, animJSON));
                }

                generator.LoadAnimationFromJSON(animJSON, false);

                //animationWWW.Dispose();

                while (!generator.IsReady) yield return 0;

                GameObject go = generator.Generate();

                go.transform.parent = transform;
                go.transform.localPosition = Vector3.zero;
                go.transform.localRotation = Quaternion.identity;
                GetComponentInChildren<ParticleEmitter>().emit = false;

                characterObject = go;

                generator.Layer = 9; // 8 = Gizmo, 9 = NoShadow

                // Change the animation culling based on clip bounds
                characterObject.animation.cullingType = AnimationCullingType.BasedOnClipBounds;

                Animate(PBConstants.ANIM_IDLE1, 0, WrapMode.Loop, 1f, 0);
            }
        }
    }

    /// <summary>
    /// Check if the avatar is currently playing animation
    /// </summary>
    /// <param name="animation"></param>
    /// <returns></returns>
    public bool IsAnimationPlaying(string animation)
    {
        if (characterObject == null)
        {
            return false;
        }
        
        return characterObject.animation.IsPlaying(animation);
    }

    /// <summary>
    /// Check whether the character is currently playing any animation at all.
    /// </summary>
    public bool IsAnimationPlayingAnything
    {
        get
        {
            if (characterObject != null)
            {
                return characterObject.animation.isPlaying;
            }
            return false;
        }
    }

    /// <summary>
    /// Animate the Avatar
    /// </summary>
    /// <param name="animation">The Animation Name</param>
    /// <param name="action">0 - To play the animation, 1 - to stop the animation</param>
    /// <param name="wrapMode">Wrap Mode</param>
    /// <param name="animationSpeed">Animation Speed</param>
    /// <param name="layer">Animation Layer</param>
    public void Animate(string animation, AnimationAction action, WrapMode wrapMode, float animationSpeed, int layer)
	{
		if (characterObject	!= null && animation != "")
		{
            if (generator.AnimationClips.Contains(animation) && characterObject.animation != null)
			{
                if (action == AnimationAction.Play)
                {
                    characterObject.animation[animation].layer = layer;
                    characterObject.animation[animation].speed = animationSpeed;
                    characterObject.animation[animation].wrapMode = wrapMode;
                    characterObject.animation.CrossFade(animation);
                }
                else
                {
                    characterObject.animation[animation].layer = layer;
                    characterObject.animation.Stop(animation);
                }
			}
			else
			{
                string anims = "";
                foreach (AnimationState state in characterObject.animation)
                {
                    anims = anims + state.clip.name + ", ";
                }

                if (PopBloopSettings.useLogs)
                {
                    Debug.LogWarning("Animation " + animation + " not found on Character " + this.gameObject.name + " => Anims found : " + anims);
                }
			}
		}
	}

    public void SetActive(bool visible)	
	{
		SkinnedMeshRenderer[] renderers = gameObject.GetComponentsInChildren<SkinnedMeshRenderer>();
		foreach (SkinnedMeshRenderer renderer in renderers)
		{
			renderer.enabled = visible;
		}
	}
	
	public Transform GetHeadBone()
	{
		if (characterObject	== null)
		{
			return null;
		}
		
		return null;
    }

    #endregion
}