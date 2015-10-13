#define MOBILE

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
    /// Static instance for singleton
    /// </summary>
    private static DressRoom dressRoom = null;

    /// <summary>
    /// Set if we want to use custom GUISkin
    /// </summary>
    public GUISkin guiSkin;

    /// <summary>
    /// Setup the Platform which we are going to use to simulate with
    /// </summary>
    public PopBloopSettings.PlatformType platform = PopBloopSettings.PlatformType.Default;

    /// <summary>
    /// Set to use local assets instead of web assets
    /// </summary>
    public bool useLocalAssets = PopBloopSettings.useLocalAssets;

    /// <summary>
    /// Set if we want to dump the logs to the Unity Logs
    /// </summary>
    public bool useLogs = false;

    /// <summary>
    /// How sensitive the character rotation is when dragged with mouse
    /// </summary>
    public float characterRotationSensitity = 50.0f;

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
    public string serverDevelopment = PopBloopSettings.serverDevelopment;

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

    /// <summary>
    /// Previous character object to replace with the new one
    /// </summary>
    private GameObject _oldCharacter;

    /// <summary>
    /// Active Character Object
    /// </summary>
    private GameObject _character;

    /// <summary>
    /// Character Generator
    /// </summary>
    private CharacterGenerator generator;

    /// <summary>
    /// State for changing avatar element
    /// </summary>
    private bool _updateAvatar = false;

    /// <summary>
    /// State for changing new avatar
    /// </summary>
	private bool _changeNewCharacter = false;

    /// <summary>
    /// Error state for avatar configuration
    /// </summary>
    private bool _bAvatarConfigError = false;

    /// <summary>
    /// Enable GUI state
    /// </summary>
    private bool _enableGUI = true;

    /// <summary>
    /// Rotate Angle based on mouse rotation
    /// </summary>
    private float _mouseY = 0f;

    /// <summary>
    /// Use sephia camera fx
    /// </summary>
    private bool _useSepia = false;

    /// <summary>
    /// Use motion blur camera fx
    /// </summary>
    private bool _useMotionBlur = false;

    /// <summary>
    /// Use glow camera fx
    /// </summary>
    private bool _useGlow = true;

    /// <summary>
    /// Camera Zoom
    /// </summary>
    private CameraShifter _cameraZoom;

    /// <summary>
    /// State if avatar is ready to show
    /// </summary>
    private bool _isAvatarEditorReady = false;

    /// <summary>
    /// Body part to change
    /// </summary>
    private CharacterGenerator.BodyPartChangeType _bodyPartToChange = CharacterGenerator.BodyPartChangeType.Middle;

    /// <summary>
    /// State for avatar is loading
    /// </summary>
    private bool _isAvatarLoading = false;

    #endregion


    #region MonoBehavior Methods

    private void Awake()
	{
        dressRoom = this;

        SetupServers();

        Application.runInBackground = true;

        if (Application.platform == RuntimePlatform.WindowsWebPlayer || Application.platform == RuntimePlatform.OSXWebPlayer)
        {
            Application.ExternalCall("OnLiloLoaded");
            Application.ExternalCall("get_session_id");
        }
        
        CharacterGenerator.OnBodyPartChanging += new Action<CharacterGenerator.BodyPartChangeType>(CharacterGenerator_OnBodyPartChanging);
	}

    private void Start()
    {
        _cameraZoom = Camera.main.GetComponent<CameraShifter>();
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
        *
        if (Application.platform == RuntimePlatform.Android || Application.platform == RuntimePlatform.WindowsEditor)
        {
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
            string bodyMat = "male_body";// "male_body";

            string hand = "male_body_hand";
            string handMat = "male_body";

            string pants = "male_pants_medium";
            string pantsMat = "male_pants";

            string shoes = "male_shoes_01";
            string shoesMat = "male_shoes_01";

            ChangeCharacterEvent("[{'tipe':'gender','element':'" + gender + "'},{'tipe':'face','element':'" + head + "','eye_brows':'" + eyeBrows + "','eyes':'" + eyes + "','lip':'" + lip + "'},{'tipe':'hair','element':'" + hair + "','material':'" + hairMat + "','element2':'" + hairBottom + "','material2':'" + hairBottomMat + "'},{'tipe':'body','element':'" + body + "','material':'" + bodyMat + "'},{'tipe':'pants','element':'" + pants + "','material':'" + pantsMat + "'},{'tipe':'shoes','element':'" + shoes + "','material':'" + shoesMat + "'},{'tipe':'Hand','element':'" + hand + "','material':'" + handMat + "'}]");
        }
        //ChangeCharacterEvent("[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'','eyes':'','lip':''},{'tipe':'Hair','element':'male_hair_1_top','material':'','element2':'male_hair_1_bottom','material2':''},{'tipe':'Body','element':'male_body_medium','material':''},{'tipe':'Pants','element':'male_pants_medium','material':''},{'tipe':'Shoes','element':'male_shoes_01','material':''},{'tipe':'Hand','element':'male_body_hand','material':''}]");
        
        else
        {*/
        #endregion

//#if UNITY_ANDROID
//#else
        if (Application.platform == RuntimePlatform.OSXEditor || Application.platform == RuntimePlatform.WindowsEditor)
        {
           // ChangePlayerId("4e2fe1e4c1b4ba4444000014");
        }
//#endif
        //}
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

        GUI.skin = guiSkin;

        if (_isAvatarEditorReady == false)
        {
            if (generator.Progress < 100)
            {    
                UIProgressBarSmall.Instance.Update(0, 0, Screen.width, 6, "", generator.Progress, 13);
            }
        }

        GUI.skin = null;
        if (_bAvatarConfigError)
        {
             UIProgressBarSmall.Instance.UpdateError(0, 0, Screen.width, 6, 100);
        }

		if (Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor)
		{				
			/*if (GUI.Button(new Rect(10, 80, 100, 30), "Change Char To Female"))
			{

                //ChangeFacePartEvent("{'tipe':'eyeBrows','element':'female_head_broweyes_01'}");//"{'tipe':'eye_brows','element':'male_head_eyesbrow_01'}");
                //ChangeElementEvent("{'gender':'female','tipe':'hair','element':'female_hair1','material':'female_hair_01_3'}");//"{'tipe':'face','element':'male_head','material':''}");
                ChangeCharacterEvent("[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_b_02_02','lip':'lips_b_07_01'},{'tipe':'Hair','element':'male_hair_01','material':'male_hair_01_2'},{'tipe':'Body','element':'female_sackdres_thin','material':'female_sackdres_02'},{'tipe':'Pants','element':'female_skirt_thin','material':'female_skirt_02'},{'tipe':'Shoes','element':'female_shoes_03','material':'female_shoes_03_1'},{'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]");
            }
				
			if (GUI.Button(new Rect(10, 120, 100, 30), "Change Hair"))
			{
                ChangeElementEvent("{'gender':'female','tipe':'hair','element':'male_hair_03','material':'male_hair_03_2'}");
			}*/			
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
        if (Application.platform != RuntimePlatform.Android && Application.platform != RuntimePlatform.IPhonePlayer)
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

            // Smooth rotate by interpolating
            if (Input.GetMouseButton(0))
            {
                _mouseY += -Input.GetAxis("Mouse X") * characterRotationSensitity;
            }
        }
        else
        {
            if (Input.touchCount > 0)
            {
                var touch = Input.GetTouch(0);
                if (touch.phase == TouchPhase.Moved)
                {
                    _mouseY -= touch.deltaPosition.x * characterRotationSensitity * 0.02f;
                }
            }
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
                _character.animation.cullingType = AnimationCullingType.AlwaysAnimate;
                _character.animation.playAutomatically = false;
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
            NGUITools.SetActive(_character.gameObject, !_hideCharacter);
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

#if MOBILE
    private void UpdateSepiaEffect(bool useSepia)
    {
    }

    private void UpdateMotionBlurEffect(bool useMotionBlur)
    {
    }

    private void UpdateGlowEffect(bool useGlow)
    {
    }
#else
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
#endif

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
        PopBloopSettings.serverLocal = serverLocal;
        PopBloopSettings.serverStaging = serverStaging;
        PopBloopSettings.serverProduction = serverProduction;
        PopBloopSettings.serverDevelopment = serverDevelopment;
        PopBloopSettings.DevelopmentMode = DevelopmentMode;
        PopBloopSettings.isSecure = isSecure;
        PopBloopSettings.useLogs = this.useLogs;
        PopBloopSettings.useLocalAssets = this.useLocalAssets;

        // We force the platform automatically
        if (Application.platform == RuntimePlatform.Android)
        {
            this.platform = PopBloopSettings.PlatformType.Android;
        }
        else if (Application.platform == RuntimePlatform.IPhonePlayer)
        {
            this.platform = PopBloopSettings.PlatformType.iOS;
        }

        PopBloopSettings.platform = this.platform;
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

        _isAvatarLoading = true;

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
                _isAvatarLoading = false;
            }
            else
            {
                string animation = animWWW.text.Trim();

                Debug.Log(string.Format("DownloadPlayerAvatarConfiguration: GetEditorAnimationsConfigurationURL: {0} => Animation JSON: {1} ", animationURL, animation));

                generator.LoadAnimationFromJSON(animation, true);

                _changeNewCharacter = update;

                _isAvatarLoading = false;
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

		_updateAvatar = generator.ChangeElementFromJSON(content);
        Debug.Log("ChangeElementEvent: " + _updateAvatar + ", content: " + content);
	}
	
	public void ChangeFacePartEvent(string content)
	{
		if (generator == null)
		{
			Debug.LogWarning("CreatureGenerator can not be null");
			return;
		} 
		
		_updateAvatar = generator.ChangeFacePartFromJSON(content);
        Debug.Log("ChangeFacePartEvent: " + _updateAvatar + ", content: " + content);
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

        _updateAvatar = true;
    }

    public void ChangePlayerId(string id)
    {
        Debug.Log("ChangePlayerId: " + id);

        _hideCharacter = false;

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
    

    #region User Api & Static Methods

    public enum UserApiRequest
    {
        GetProfile = 0,
        UserCheck = 1,
        Login = 2,
        UpdateStatus = 3,
        UpdateProperties = 4,
        NewUser = 5
    };

    public static void CameraDolly(CameraShifter.ZoomTargetArea target)
    {
        if (dressRoom != null)
        {
            dressRoom._mouseY = 0;
            dressRoom._cameraZoom.ZoomTo(target);
        }
    }

    public void Undo()
    {
        if (_isAvatarEditorReady)
        {
            var undoType = generator.Undo();

            if (undoType == CharacterGenerator.UndoType.Character)
            {
                _changeNewCharacter = true;
            }
            else
            {
                _updateAvatar = true;
            }
        }
    }

    public static void UndoPlayer()
    {
        if (dressRoom != null)
        {
            dressRoom.Undo();
        }
    }

    public static string GetCurrentCharacterConfig()
    {
        if (dressRoom != null)
        {
            return dressRoom.generator.GetCurrentCharacterConfig();
        }

        return "";
    }

    public static void ChangePlayerAvatar(string playerId)
    {
        if (dressRoom != null)
        {
            if (dressRoom._character == null)
            {
                if (!dressRoom._isAvatarLoading)
                {
                    dressRoom.ChangePlayerId(playerId);
                }
            }
            else
            {
                ShowCharacter();
            }
        }
        else
        {
            Debug.LogWarning("DressRoom is null, can not change Player Avatar");
        }
    }

    public static void ChangePlayerCharacter(string json)
    {
        if (dressRoom != null)
        {
            dressRoom.ChangeCharacterEvent(json);
        }
    }

    public static void ChangePlayerElement(string json)
    {
        if (dressRoom != null)
        {
            dressRoom.ChangeElementEvent(json);
        }
    }

    public static void ChangePlayerSkin(int index)
    {
        if (dressRoom != null)
        {
            dressRoom.ChangeSkinColor(index);
        }
    }

    public static void ActivateCharacter(bool state)
    {
        if (dressRoom != null)
        {
            if (dressRoom._character != null)
            {
                NGUITools.SetActive(dressRoom._character.gameObject, state);
            }
        }
    }

    public static void ShowCharacter(string playerId)
    {
        if (dressRoom != null)
        {
            if (dressRoom._character != null)
            {
                //NGUITools.SetActive(dressRoom._character.gameObject, true);
                dressRoom._character.gameObject.SetActive(true);
            }
            else
            {
                if (string.IsNullOrEmpty(playerId) == false)
                {
                    ChangePlayerAvatar(playerId);
                }
            }
            dressRoom._hideCharacter = false;
        }
    }

    public static void ShowCharacter()
    {
        ShowCharacter("");
    }

    private bool _hideCharacter = false;

    public static void HideCharacter()
    {
        if (dressRoom != null)
        {
            if (dressRoom._character != null)
            {
                //NGUITools.SetActive(dressRoom._character.gameObject, false);
                dressRoom._character.gameObject.SetActive(false);
            }
            dressRoom._hideCharacter = true;
        }
    }

    private static string BuildUserRequest(UserApiRequest request, string parameter)
    {
        string ret = PopBloopSettings.WebServerUrl + KPConstants.KPUserApi;
        string req = "";
        switch (request)
        {
            case UserApiRequest.GetProfile:
                req = "profile/" + parameter;
                break;

            case UserApiRequest.Login:
                req = "login/" + parameter;
                break;

            case UserApiRequest.NewUser:
                req = "newuser/" + parameter;
                break;

            case UserApiRequest.UpdateProperties:
                req = "properties/" + parameter;
                break;

            case UserApiRequest.UpdateStatus:
                req = "status/" + parameter;
                break;

            case UserApiRequest.UserCheck:
                req = "check/" + parameter;
                break;
        }

        ret = string.Format("{0}{1}?token={2}", ret, req, DateTime.Now.Ticks);
        Debug.Log("Calling User Api: " + ret);

        return ret;
    }

    public static void CallUserApi(GameObject sender, UserApiRequest request, string parameter, Action<Dictionary<string, object>> callback)
    {
        if (dressRoom != null)
        {
            dressRoom.UserApi(sender, BuildUserRequest(request, parameter), callback);
        }
        else
        {
            Debug.LogWarning("Dress Room is not iniatialized");
        }
    }

    public static void CallUserApi(UserApiRequest request, string parameter, Action<Dictionary<string, object>> callback)
    {
        CallUserApi(null, request, parameter, callback);
    }

    private void UserApi(GameObject sender, string apiUrl, Action<Dictionary<string, object>> callback)
    {
        StartCoroutine(CallUserApi(sender, apiUrl, callback));
    }

    private IEnumerator CallUserApi(GameObject sender, string apiUrl, Action<Dictionary<string, object>> callBack)
    {
        WWW www = new WWW(apiUrl);

        if (sender != null)
        {
            sender.SetActive(false);
        }

        yield return www;

        if (www.error == null)
        {
            if (callBack != null)
            {
                var hash = Prime31.JsonExtensions.dictionaryFromJson(www.text);
                callBack(hash);
            }
        }
        else
        {
            StartCoroutine(CallUserApi(sender, apiUrl, callBack));
        }

        if (sender != null)
        {
            sender.SetActive(true);
        }
    }


    #endregion
}
