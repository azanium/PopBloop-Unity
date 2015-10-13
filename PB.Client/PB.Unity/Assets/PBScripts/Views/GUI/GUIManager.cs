using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;
using System;
using System.Collections.Generic;

public class GUIManager : MonoBehaviour
{
    #region MemVars & Props

    static protected GUIManager guiManager;

    public GUISkin skin;
    public Vector2 cursorSize = new Vector2(32f, 32f);
    protected Texture2D _cursor;

    protected bool _enableGUI = true;

    public static bool ShowCursor
    {
        get { return Screen.showCursor; }
        set
        {
            Screen.showCursor = value;
        }
    }

    protected enum DebugType
    {
        None,
        Fps,
        Debug,
        Ccu
    }

    protected DebugType _debugType = DebugType.None;
    protected int _frame = 0;
    protected int _fps = 0;
    protected float _oldTime = 0;

    protected Rect _debugRect = new Rect(10, 50, Screen.width, Screen.height);
    private List<GUIBase> guiBehaviors;
    private GUIStyle _debugStyle;

    #endregion


    #region Mono Methods

    protected void Awake()
    {
        guiManager = this;

        guiBehaviors = new List<GUIBase>();
        guiBehaviors.Add(new UIShout());
        guiBehaviors.Add(new UIHUDCommands());
        guiBehaviors.Add(new UIBubbleView());
        guiBehaviors.Add(new UIEnergy());
        guiBehaviors.Add(new UIActionView());
        guiBehaviors.Add(new UINotificationCenter());
        
        guiBehaviors.ForEach((gui) => gui.Awake());
    }

    protected void OnDestroy()
    {
        guiBehaviors.ForEach((gui) => gui.OnDestroy());
        guiBehaviors.Clear();
    }

    protected void Start() 
    {
        //Screen.showCursor = false;
        /*_cursor = ResourceManager.Instance.LoadTexture2D("GUI/Cursor/cursor");
        Cursor.SetCursor(_cursor, Vector2.zero, CursorMode.Auto);*/
        _oldTime = Time.time;

        guiBehaviors.ForEach((gui) => gui.Start());

        _debugStyle = new GUIStyle();
        _debugStyle.font = ResourceManager.Instance.LoadFont(ResourceManager.FontABeeZeeRegular);
        _debugStyle.normal.textColor = Color.white;
	}

    protected void Update()
    {
        guiBehaviors.ForEach((gui) => gui.Update());

        ++_frame;
        if (Time.time - _oldTime >= 1)
        {
            _fps = _frame;
            _frame = 0;
            _oldTime = Time.time;
        }
    }

    protected void OnGUI()
	{
        if (_enableGUI)
        {
            // Controls have the second priority
            GUI.depth = 1;
            
            switch (_debugType)
            {
                case DebugType.Debug:
                    {
                        GUILayout.BeginArea(_debugRect);
                        GUILayout.Label("All " + Resources.FindObjectsOfTypeAll(typeof(UnityEngine.Object)).Length, _debugStyle);
                        GUILayout.Label("Textures " + Resources.FindObjectsOfTypeAll(typeof(Texture)).Length, _debugStyle);
                        GUILayout.Label("AudioClips " + Resources.FindObjectsOfTypeAll(typeof(AudioClip)).Length, _debugStyle);
                        GUILayout.Label("Meshes " + Resources.FindObjectsOfTypeAll(typeof(Mesh)).Length, _debugStyle);
                        GUILayout.Label("Materials " + Resources.FindObjectsOfTypeAll(typeof(Material)).Length, _debugStyle);
                        GUILayout.Label("GameObjects " + Resources.FindObjectsOfTypeAll(typeof(GameObject)).Length, _debugStyle);
                        GUILayout.Label("Components " + Resources.FindObjectsOfTypeAll(typeof(Component)).Length, _debugStyle);
                        GUILayout.Label("Graphics Memory " + SystemInfo.graphicsMemorySize.ToString() + " MB", _debugStyle);
                        GUILayout.Label("System Memory " + SystemInfo.systemMemorySize.ToString() + " MB", _debugStyle);
                        GUILayout.EndArea();
                    }
                    break;

                case DebugType.Fps:
                    {
                        GUILayout.BeginArea(_debugRect);
                        GUILayout.Label("FPS " + _fps.ToString(), _debugStyle);
                        GUILayout.EndArea();
                    }
                    break;

                case DebugType.Ccu:
                    {
                        GUILayout.BeginArea(_debugRect);
                        GUILayout.Label("Online Player: " + PBGameMaster.ItemPositions.Count.ToString(), _debugStyle);
                        GUILayout.EndArea();
                    }
                    break;
            }

            guiBehaviors.ForEach((gui) => gui.OnGUI());
        }

        /*
        if (ShowCursor)
        {
            GUI.depth = 0;
            
            Rect pos = new Rect(mousePos.x, Screen.height - mousePos.y, cursorSize.x, cursorSize.y);

            GUI.DrawTexture(pos, _cursor);
        }*/

	}

    void OnMouseEnter()
    {
        Cursor.SetCursor(_cursor, Vector2.zero, CursorMode.Auto);
    }

    void OnMouseExit()
    {
        Cursor.SetCursor(null, Vector2.zero, CursorMode.Auto);
    }
    
    #endregion


    #region GUI Methods

    public void CaptureScreen()
    {
        _enableGUI = false;
        ShowCursor = false;

        StartCoroutine(CaptureScreenAsync());
    }

    private IEnumerator CaptureScreenAsync()
    {
        yield return new WaitForEndOfFrame();

        Texture2D texture = ResourceManager.Instance.Snapshot();

        ShowPhotoGallery(texture);
    }

    private void ShowPhotoGallery(Texture2D texture)
    {
        WindowManager.CreatePhotoGallery("Polaroid", texture);

        ShowCursor = true;
    }

    #endregion


    #region Static Methods

    static public void Capture()
    {
        if (guiManager != null)
        {
            guiManager.CaptureScreen();
        }
    }

    #endregion


    #region Messenger's Messages

    protected void OnEnable()
    {
        guiBehaviors.ForEach((gui) => gui.OnEnable());

        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGuiEnable);
        Messenger<winPhotoGallery, Texture2D>.AddListener(Messages.GUI_UPLOAD_PHOTO, OnGuiUploadPhoto);
        Messenger<bool>.AddListener(Messages.GUI_DEBUG_DIAG, OnDebugDiag);
        Messenger<bool>.AddListener(Messages.GUI_DEBUG_FPS, OnDebugFps);
        Messenger<bool>.AddListener(Messages.GUI_DEBUG_CCU, OnDebugCcu);
    }

    protected void OnDisable()
    {
        guiBehaviors.ForEach((gui) => gui.OnDisable());

        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGuiEnable);
        Messenger<winPhotoGallery, Texture2D>.RemoveListener(Messages.GUI_UPLOAD_PHOTO, OnGuiUploadPhoto);
        Messenger<bool>.RemoveListener(Messages.GUI_DEBUG_DIAG, OnDebugDiag);
        Messenger<bool>.RemoveListener(Messages.GUI_DEBUG_FPS, OnDebugFps);
        Messenger<bool>.RemoveListener(Messages.GUI_DEBUG_CCU, OnDebugCcu);
    }

    protected void OnDebugCcu(bool enabled)
    {
        _debugType = enabled ? DebugType.Ccu : DebugType.None;
    }

    protected void OnDebugFps(bool enabled)
    {
        _debugType = enabled ? DebugType.Fps : DebugType.None;
    }

    protected void OnDebugDiag(bool enabled)
    {
        _debugType = enabled ? DebugType.Debug : DebugType.None;
    }

    protected void OnGuiEnable(bool enabled)
    {
        _enableGUI = enabled;
    }

    protected void OnGuiUploadPhoto(winPhotoGallery gallery, Texture2D texture)
    {
        StartCoroutine(UploadPhoto(gallery, texture));
    }

    protected IEnumerator UploadPhoto(winPhotoGallery gallery, Texture2D texture)
    {
        // Encode texture into PNG
        var bytes = texture.EncodeToPNG();

        // Create a Web Form
        var form = new WWWForm();

        var sessionId = Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor ? "7946428e3ffe6c62549ab6642a42d6be" : PBGameMaster.Game.Avatar.Token;

        Debug.Log("Upload capture with session: " + sessionId);

        form.AddField("session_id", sessionId);
        form.AddBinaryData("picture", bytes, "screenshot.png", "image/png");

        WWW www = new WWW(PopBloopSettings.PhotoUploadUrl, form);
        yield return www;

        if (www.error != null)
        {
            Debug.LogError(www.error);
        }
        else
        {
            if (www.text.Contains("ERROR"))
            {
                Debug.LogWarning("Capture failed!");
            }
            else
            {
                string url = PopBloopSettings.PhotoUploadShareAlbumToFacebookUrl;
                url = url.Replace("www.", "");

                Debug.Log(string.Format("Share with Facebook: {0}", url));

                www = new WWW(url);

                yield return www;

                if (www.error != null)
                {
                    Debug.LogError("Share Upload photo facebook failed: " + www.error);
                }
                else
                {
                    Debug.Log("Uploaded photo shared to facebook with message: " + www.text);
                }
            }
        }

        gallery.FinishUpload();
    }

    #endregion
}
