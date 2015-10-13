using System;
using System.Collections;
using System.Collections.Generic;

using UnityEngine;

using PB.Client;
using PB.Game;

public class DressRoom : MonoBehaviour
{
    #region MemVars & Props

    /// <summary>
    /// Set if we want to use custom GUISkin
    /// </summary>
    public GUISkin guiSkin;

    /// <summary>
    /// How sensitive the character rotation is when dragged with mouse
    /// </summary>
    public float characterRotationSensitity = 20.0f;

    /// <summary>
    /// How smooth the character rotation is when dragged with mouse
    /// </summary>
    public float characterRotationSmoothness = 5.0f;

    /// <summary>
    /// Is the request using HTTP or HTTPS
    /// </summary>
    public bool isSecure = false;

    /// <summary>
    /// Web Server Type 
    /// </summary>
    public PopBloopSettings.DevelopmentModeType DevelopmentMode = PopBloopSettings.DevelopmentModeType.LOCAL;

    /// <summary>
    /// Web Server Local
    /// </summary>
    public string serverLocal = PopBloopSettings.serverLocal;

    /// <summary>
    /// Web Server Staging
    /// </summary>
    public string serverStaging = PopBloopSettings.serverStaging;

    /// <summary>
    /// Web Server Production
    /// </summary>
    public string serverProduction = PopBloopSettings.serverProduction;

    /// <summary>
    /// Character Game Object
    /// </summary>
    public GameObject Character
    {
        get { return _character; }
    }

    private GameObject _oldCharacter;
    private GameObject _character;
    private CharacterGenerator generator;
    private bool _updateAvatar = false;
	private bool _changeNewCharacter = false;
    private bool _bAvatarConfigError = false;
    private bool _enableGUI = true;
    private float _mouseY = 0f;
    private bool _useSepia = false;
    private bool _useMotionBlur = false;
    private bool _useGlow = true;
    private CameraShifter _cameraZoom;
    private bool _isAvatarEditorReady = false;
    private CharacterGenerator.BodyPartChangeType _bodyPartToChange = CharacterGenerator.BodyPartChangeType.Middle;

    #endregion


    #region MonoBehavior Methods

    private void Awake()
	{
        SetupServers();

        Application.runInBackground = true;

		Application.ExternalCall("OnLiloLoaded");
        Application.ExternalCall("get_session_id");

        CharacterGenerator.OnBodyPartChanging += new Action<CharacterGenerator.BodyPartChangeType>(CharacterGenerator_OnBodyPartChanging);
	}
	
	private void Start() 
    {
        _cameraZoom = Camera.mainCamera.GetComponent<CameraShifter>();
        if (_cameraZoom == null)
        {
            Debug.LogWarning("No CameraShifter attached to the Main Camera, please attach it!");
        }

        AssetsManager.Initialize();

		generator = new CharacterGenerator();
				
		_updateAvatar = false; 
		_changeNewCharacter = false;

        #region TEST_CODES
        /*
        string gender = "male_base";

        string hair = "male_hair_1_top";
        string hairMat = "male_hair_1";

        string hairBottom = "male_hair_1_bottom";
        string hairBottomMat = "male_hair_1";

        string head = "male_head";

        string eyeBrows = "";
        string eyes = "";
        string lip = "";

        string body = "male_body_medium";
        string bodyMat = "";// "male_body";

        string hand = "male_body_hand";
        string handMat = "male_body";

        string pants = "male_pants_medium";
        string pantsMat = "male_pants";

        string shoes = "male_shoes_01";
        string shoesMat = "male_shoes_01";

        string gender = "male_base";

        string hair = "male_hair1_top";
        string hairMat = "male_hair1";

        string hairBottom = "male_hair1_bottom";
        string hairBottomMat = "male_hair1";

        string head = "male_head";

        string eyeBrows = "";
        string eyes = "";
        string lip = "";

        string body = "male_body_medium";
        string bodyMat = "male_body";// "male_body";

        string hand = "male_body_hand";
        string handMat = "male_body";

        string pants = "male_long_pants_medium";
        string pantsMat = "male_long_pants";

        string shoes = "male_shoes_01";
        string shoesMat = "male_shoes_01";*/

        //ChangeCharacterEvent("[{'tipe':'gender','element':'" + gender + "'},{'tipe':'face','element':'" + head + "','eye_brows':'" + eyeBrows + "','eyes':'" + eyes + "','lip':'" + lip + "'},{'tipe':'hair','element':'" + hair + "','material':'" + hairMat + "','element2':'" + hairBottom + "','material2':'" + hairBottomMat + "'},{'tipe':'body','element':'" + body + "','material':'" + bodyMat + "'},{'tipe':'pants','element':'" + pants + "','material':'" + pantsMat + "'},{'tipe':'shoes','element':'" + shoes + "','material':'" + shoesMat + "'},{'tipe':'Hand','element':'" + hand + "','material':'" + handMat + "'}]");		
        //ChangeCharacterEvent("[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'','eyes':'','lip':''},{'tipe':'Hair','element':'male_hair_1_top','material':'','element2':'male_hair_1_bottom','material2':''},{'tipe':'Body','element':'male_body_medium','material':''},{'tipe':'Pants','element':'male_pants_medium','material':''},{'tipe':'Shoes','element':'male_shoes_01','material':''},{'tipe':'Hand','element':'male_body_hand','material':''}]");
        #endregion

        if (Application.platform == RuntimePlatform.OSXEditor || Application.platform == RuntimePlatform.WindowsEditor)
        {
            ChangePlayerId("4e2fe1e4c1b4ba4444000014");
        }
	}

	private void Update() 
	{
        if (generator == null)
        {
            return;
        }

        _isAvatarEditorReady = generator.IsReady;
        if (_isAvatarEditorReady == false)
        {
            return;
        }

        ProcessInput();

        UpdateAvatar();
	}
	
	private void OnGUI()
	{
        if (generator == null || !_enableGUI)
        {
            return;
        }

		if (_isAvatarEditorReady == false) 
		{
            GUI.skin = guiSkin;
			
            UIProgressBarSmall.Instance.Update(10, Screen.height - 30, "Dressing up..", generator.Progress, 13);
		}

        GUI.skin = null;
        if (_bAvatarConfigError)
        {
            GUI.Button(new Rect(Screen.width - 130, 10, 120, 30), "Avatar Error");
        }

		if (Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor)
		{				
			if (GUI.Button(new Rect(10, 80, 100, 30), "Change Char To Female"))
			{

                //ChangeFacePartEvent("{'tipe':'eyeBrows','element':'female_head_broweyes_01'}");//"{'tipe':'eye_brows','element':'male_head_eyesbrow_01'}");
                //ChangeElementEvent("{'gender':'female','tipe':'hair','element':'female_hair1','material':'female_hair_01_3'}");//"{'tipe':'face','element':'male_head','material':''}");
                ChangeCharacterEvent("[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_b_02_02','lip':'lips_b_07_01'},{'tipe':'Hair','element':'male_hair_01','material':'male_hair_01_2'},{'tipe':'Body','element':'female_sackdres_thin','material':'female_sackdres_02'},{'tipe':'Pants','element':'female_skirt_thin','material':'female_skirt_02'},{'tipe':'Shoes','element':'female_shoes_03','material':'female_shoes_03_1'},{'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]");
            }
				
			if (GUI.Button(new Rect(10, 120, 100, 30), "Change Hair"))
			{
                ChangeElementEvent("{'gender':'female','tipe':'hair','element':'male_hair_03','material':'male_hair_03_2'}");
			}			
		}
	}

    #endregion


    #region Support Methods

    private void CharacterGenerator_OnBodyPartChanging(CharacterGenerator.BodyPartChangeType obj)
    {
        _bodyPartToChange = obj;
    }

    private void ProcessInput()
    {
        if (Input.GetKey(KeyCode.Q))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Default);
        }

        if (Input.GetKey(KeyCode.W))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Head);
        }

        if (Input.GetKey(KeyCode.E))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Body);
        }

        if (Input.GetKey(KeyCode.R))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Foot);
        }

        if (Input.GetKeyDown(KeyCode.M))
        {
            _useMotionBlur = !_useMotionBlur;
            UpdateMotionBlurEffect(_useMotionBlur);
        }

        if (Input.GetKeyDown(KeyCode.S))
        {
            _useSepia = !_useSepia;
            UpdateSepiaEffect(_useSepia);
        }

        if (Input.GetKeyDown(KeyCode.G))
        {
            _useGlow = !_useGlow;
            UpdateGlowEffect(_useGlow);
        }

        // Smooth rotate by interpolating
        if (Input.GetMouseButton(0))
        {
            _mouseY += -Input.GetAxis("Mouse X") * characterRotationSensitity;
        }

        if (Character != null && _changeNewCharacter == false && _updateAvatar == false)
        {
            Character.transform.rotation = Quaternion.Slerp(Character.transform.rotation, Quaternion.Euler(0, _mouseY, 0), Time.deltaTime * characterRotationSmoothness);
        }
    }

    private void UpdateAvatar()
    {
        if (_changeNewCharacter)
        {
            if (_character != null)
            {
                Destroy(_character);
            }

            _character = generator.Generate();

            if (_character != null)
            {
                _character.animation.cullingType = AnimationCullingType.BasedOnClipBounds;
                _character.transform.parent = transform;
                _character.transform.localPosition = new Vector3(0f, 0.01f, 0f);
                _character.transform.localRotation = Quaternion.identity;

                _character.transform.rotation = Quaternion.Euler(_character.transform.rotation.eulerAngles.x, 0, _character.transform.rotation.eulerAngles.z);

                if (generator.IsAnimationExist(Constants.ANIM_IDLE))// .AnimationClips.Contains(Constants.ANIM_IDLE))
                {
                    _character.animation.Play(Constants.ANIM_IDLE);
                }
            }
        }
        else if (_updateAvatar)
        {
            _character = generator.Generate(_character);

            switch (_bodyPartToChange)
            {
                case CharacterGenerator.BodyPartChangeType.Middle:
                    {
                        CrossFadeAnimation(generator, _character, Constants.ANIM_CHECKBODY);
                    }
                    break;

                case CharacterGenerator.BodyPartChangeType.Top:
                    {
                        CrossFadeAnimation(generator, _character, Constants.ANIM_CHECKTOP);
                    }
                    break;

                case CharacterGenerator.BodyPartChangeType.Bottom:
                    {
                        CrossFadeAnimation(generator, _character, Constants.ANIM_CHECKBOTTOM);
                    }
                    break;
            }

        }

        if (_character != null)
        {
            if (_character.animation.isPlaying == false)
            {
                CrossFadeAnimation(generator, _character, Constants.ANIM_IDLE);
            }
        }

        _updateAvatar = false;
        _changeNewCharacter = false;
    }

    private void CrossFadeAnimation(CharacterGenerator generator, GameObject character, string animationName)
    {
        if (generator.IsAnimationExist(animationName) && _character != null)//generator.AnimationClips.Contains(animationName) && _character != null)
        {
            _character.animation[animationName].wrapMode = WrapMode.Once;
            _character.animation.CrossFade(animationName);
        }
    }

    private void UpdateSepiaEffect(bool useSepia)
    {
        SepiaToneEffect sepia = Camera.mainCamera.GetComponent<SepiaToneEffect>();
        if (sepia != null)
        {
            sepia.enabled = useSepia;
        }
    }

    private void UpdateMotionBlurEffect(bool useMotionBlur)
    {
        MotionBlur blur = Camera.mainCamera.GetComponent<MotionBlur>();
        if (blur != null)
        {
            blur.enabled = useMotionBlur;
        }
    }

    private void UpdateGlowEffect(bool useGlow)
    {
        GlowEffect glow = Camera.mainCamera.GetComponent<GlowEffect>();
        if (glow != null)
        {
            glow.enabled = useGlow;
        }
    }

    public void GetUserId(string session)
    {
        WindowGUI gui = GetComponent<WindowGUI>();
        if (gui != null)
        {
            gui.sessionId = session;
            Debug.Log("Got Session ID: " + session);
        }
        else
        {
            Debug.LogWarning("No SessionId being set on DressRoom");
        }
    }

    private void SetupServers()
    {
        Debug.Log("Start");
        PopBloopSettings.serverLocal = serverLocal;
        PopBloopSettings.serverStaging = serverStaging;
        PopBloopSettings.serverProduction = serverProduction;
        PopBloopSettings.DevelopmentMode = DevelopmentMode;
        PopBloopSettings.isSecure = isSecure;
    }

    private IEnumerator DownloadChangeCharacter(string avatarConfig)
    {
        bool update = generator.ChangeCharacterFromJSON(avatarConfig);

        string url = PopBloopSettings.GetEditorAnimationsConfigurationURL(generator.Gender);
        WWW animWWW = new WWW(url);
        Debug.Log("GetEditorAnimationsConfigurationURL: " + url);

        yield return animWWW;

        if (animWWW.error != null)
        {
            _bAvatarConfigError = true;
        }
        else
        {
            string animation = animWWW.text;
            Debug.Log(string.Format("DownloadChangeCharacter: GetEditorAnimationsConfigurationURL: {0} => Animation JSON: {1} ", url, animation));

            generator.LoadAnimationFromJSON(animation, true);

            _changeNewCharacter = update;
        }
    }

    private IEnumerator DownloadPlayerAvatarConfiguration(string id)
    {
        string avatarURL = PopBloopSettings.GetAvatarConfigurationURL(id);
        
        WWW avatarWWW = new WWW(avatarURL);
        Debug.Log("GetEditorAnimationsConfigurationURL: " + avatarURL);

        yield return avatarWWW;

        if (avatarWWW.error != null)
        {
            _bAvatarConfigError = true;
        }
        else
        {
            string json = avatarWWW.text.Trim();
            Debug.Log(string.Format("DownloadPlayerAvatarConfiguration: GetAvatarConfigurationUrl: {0} => Avatar JSON: {1}", avatarURL, json));

            bool update = generator.ChangeCharacterFromJSON(json);

            string animationURL = PopBloopSettings.GetEditorAnimationsConfigurationURL(generator.Gender);
            WWW animWWW = new WWW(animationURL);

            yield return animWWW;

            if (animWWW.error != null)
            {
                _bAvatarConfigError = true;
            }
            else
            {
                string animation = animWWW.text.Trim();

                Debug.Log(string.Format("DownloadPlayerAvatarConfiguration: GetEditorAnimationsConfigurationURL: {0} => Animation JSON: {1} ", animationURL, animation));

                generator.LoadAnimationFromJSON(animation, true);

                _changeNewCharacter = update;
            }
        }
    }

    #endregion


    #region JavaScript Events

    public void ChangeCharacterEvent(string content)
	{
		if (generator == null)
		{
			Debug.LogWarning("CreatureGenerator can not be null");
			return;
		}

        AssetsManager.Clear();

        Debug.Log("ChangeCharacterEvent: " + content);

		//_changeNewCharacter = generator.ChangeCharacterFromJSON(content);
        StartCoroutine(DownloadChangeCharacter(content));
	}
	
	public void ChangeElementEvent(string content)
	{
		if (generator == null)
		{
			Debug.LogWarning("CreatureGenerator can not be null");
			return;
		}

        Debug.Log("ChangeElementEvent: " + content);

		_updateAvatar = generator.ChangeElementFromJSON(content);
	}
	
	public void ChangeFacePartEvent(string content)
	{
		if (generator == null)
		{
			Debug.LogWarning("CreatureGenerator can not be null");
			return;
		}

        Debug.Log("ChangeFacePartEvent: " + content);
		
		_updateAvatar = generator.ChangeFacePartFromJSON(content);
	}

    public void ChangeSkinColor(int index)
    {
        if (generator == null)
        {
            Debug.LogWarning("ChangeSkinColor: CreatureGenerator can not be null");
            return;
        }

        Debug.Log("ChangeSkinColor: " + index);

        generator.SkinColorIndex = index;
    }

    public void ChangePlayerId(string id)
    {
        Debug.Log("ChangePlayerId: " + id);

        StartCoroutine(DownloadPlayerAvatarConfiguration(id));
    }

    #endregion


    #region Messenger's Messages

    private void OnEnable()
    {
        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGuiEnable);
    }

    private void OnDisable()
    {
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGuiEnable);
    }

    void OnGuiEnable(bool enabled)
    {
        _enableGUI = enabled;
    }

    #endregion
}
