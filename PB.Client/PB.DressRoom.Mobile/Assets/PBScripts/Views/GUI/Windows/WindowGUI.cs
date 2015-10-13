using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;

public class WindowGUI : MonoBehaviour 
{
	public GUISkin skin;
    public Vector2 cursorSize = new Vector2(32f, 32f);
    private Texture2D _cursor;

    private bool _enableGUI = true;

    public string sessionId = "";

    private static bool _showCursor = true;
    public static bool showCursor
    {
        get { return _showCursor; }
        set
        {
            if (Application.platform != RuntimePlatform.WindowsEditor && Application.platform != RuntimePlatform.OSXEditor)
            {
                _showCursor = value;
            }
        }
    }	

	void Start() 
    {
        Screen.showCursor = false;
        _cursor = ResourceManager.Instance.LoadTexture2D("GUI/Cursor/cursor");
	}

	void OnGUI()
	{
        GUI.depth = 1;

        if (_enableGUI == false)
        {
            return;
        }

        if (showCursor)
        {
            GUI.depth = 0;
            Vector3 mousePos = Input.mousePosition;
            Rect pos = new Rect(mousePos.x, Screen.height - mousePos.y, cursorSize.x, cursorSize.y);

            GUI.DrawTexture(pos, _cursor);
        }

		WindowManager.Draw(skin);
	}

    void OnEnable()
    {
        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGuiEnable);
        Messenger<winPhotoGallery, Texture2D>.AddListener(Messages.GUI_UPLOAD_PHOTO, OnGuiUploadPhoto);
    }

    void OnDisable()
    {
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGuiEnable);
        Messenger<winPhotoGallery, Texture2D>.RemoveListener(Messages.GUI_UPLOAD_PHOTO, OnGuiUploadPhoto);
    }

    void OnGuiEnable(bool enabled)
    {
        _enableGUI = enabled;
    }

    void OnGuiUploadPhoto(winPhotoGallery gallery, Texture2D texture)
    {
        StartCoroutine(UploadPhoto(gallery, texture));
    }

    private IEnumerator UploadPhoto(winPhotoGallery gallery, Texture2D texture)
    {
        // Encode texture into PNG
        var bytes = texture.EncodeToPNG();

        // Create a Web Form
        var form = new WWWForm();

        var sessionId = Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor ? "7946428e3ffe6c62549ab6642a42d6be" : this.sessionId;

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

                /*// Now we get our latest uploaded filename
                Debug.Log("Get Uploaded capture filename on Server: " + PopBloopSettings.PhotoUploadLatestFilenameUrl);

                www = new WWW(PopBloopSettings.PhotoUploadLatestFilenameUrl);

                yield return www;

                if (www.error != null)
                {
                    Debug.LogError(www.error);
                }
                else
                {
                    string latestFilenameOnServer = www.text;

                    if (latestFilenameOnServer.Trim() != null)
                    {
                    }
                }*/
            }
        }

        gallery.FinishUpload();
        /*
        // Encode texture into PNG
        var bytes = texture.EncodeToPNG();

        // Create a Web Form
        var form = new WWWForm();

        var session = Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor ? "7946428e3ffe6c62549ab6642a42d6be" : this.sessionId;

        Debug.Log("Upload capture with session: " + session);

        form.AddField("session_id", session);
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
                Debug.Log("Captured");
            }
        }

        gallery.FinishUpload();*/
    }
}
