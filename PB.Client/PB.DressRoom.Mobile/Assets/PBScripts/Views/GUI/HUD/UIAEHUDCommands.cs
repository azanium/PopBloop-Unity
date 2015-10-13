using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;

public class UIAEHUDCommands : MonoBehaviour 
{
    private const string HUD_DOLLY_PATH = "GUI/HUD/Dolly/";

    private GUIStyle defaultStyle;
    private GUIStyle snapshotStyle;
    private GUIStyle snapshotDisabledStyle;

    private GUIStyle dollyHeadStyle;
    private GUIStyle dollyBodyStyle;
    private GUIStyle dollyFootStyle;
    private GUIStyle dollyResetStyle;
    private GUIStyle toolBarStyle;
    private GUIStyle _tooltipStyle;

    private bool enableSnapshot = true;
    private bool _guiEnabled = true;

    private CameraShifter _cameraZoom;

    // Use this for initialization
	public void Start () 
    {
        _cameraZoom = Camera.main.GetComponent<CameraShifter>();
        if (_cameraZoom == null)
        {
            Debug.LogWarning("No CameraShifter attached to the Main Camera, please attach it!");
        }

        defaultStyle = new GUIStyle();
        defaultStyle.margin = new RectOffset(5, 3, 3, 3);
        
        
        snapshotStyle = new GUIStyle(defaultStyle);
        snapshotStyle.normal.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "capture_normal");
        snapshotStyle.hover.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "capture_hover");
        snapshotStyle.active.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "capture_normal");

        snapshotDisabledStyle = new GUIStyle();
        snapshotDisabledStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_disabled");

        dollyHeadStyle = new GUIStyle(defaultStyle);
        dollyHeadStyle.normal.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_head_normal");
        dollyHeadStyle.hover.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_head_hover");
        dollyHeadStyle.active.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_head_active");

        dollyBodyStyle = new GUIStyle(defaultStyle);
        dollyBodyStyle.normal.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_body_normal");
        dollyBodyStyle.hover.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_body_hover");
        dollyBodyStyle.active.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_body_active");

        dollyResetStyle = new GUIStyle(defaultStyle);
        dollyResetStyle.normal.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_reset_normal");
        dollyResetStyle.hover.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_reset_hover");
        dollyResetStyle.active.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_reset_active");

        dollyFootStyle = new GUIStyle(defaultStyle);
        dollyFootStyle.normal.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_foot_normal");
        dollyFootStyle.hover.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_foot_hover");
        dollyFootStyle.active.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "dolly_foot_active");

        toolBarStyle = new GUIStyle();
        toolBarStyle.normal.background = ResourceManager.Instance.LoadTexture2D(HUD_DOLLY_PATH + "bg");

        _tooltipStyle = new GUIStyle();
        _tooltipStyle.fontSize = 13;
        _tooltipStyle.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        _tooltipStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/hintBox");
        _tooltipStyle.normal.textColor = Color.white;
        _tooltipStyle.padding = new RectOffset(3, 3, 3, 3);
	}
	
	public void OnGUI() 
    {
        if (!_guiEnabled || _cameraZoom == null)
        {
            return;
        }

        GUILayout.BeginArea(new Rect(0, 0, Screen.width, 38), toolBarStyle);

        GUILayout.BeginHorizontal();

        float width = 32;
        float height = 32;

        if (GUILayout.Button(new GUIContent("", "Reset Dolly"), dollyResetStyle, GUILayout.Height(height), GUILayout.Width(width)))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Default);
        }

        if (GUILayout.Button(new GUIContent("", "Dolly to Head"), dollyHeadStyle, GUILayout.Height(height), GUILayout.Width(width)))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Head);
        }

        if (GUILayout.Button(new GUIContent("", "Dolly to Body"), dollyBodyStyle, GUILayout.Height(height), GUILayout.Width(width)))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Body);
        }

        if (GUILayout.Button(new GUIContent("", "Dolly to Foot"), dollyFootStyle, GUILayout.Height(height), GUILayout.Width(width)))
        {
            _cameraZoom.ZoomTo(CameraShifter.ZoomTargetArea.Foot);
        }

        GUILayout.FlexibleSpace(); ;

        if (GUILayout.Button(new GUIContent("", "Take a Photo!"), snapshotStyle, GUILayout.Height(height), GUILayout.Width(width)) && enableSnapshot)
        {
            enableSnapshot = false;
            StartCoroutine(Capture());
        }
        
        GUILayout.EndHorizontal();

        GUILayout.EndArea();

        DrawTooltip();
	}

    void OnEnable()
    {
        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGUIEnable);
        Messenger<bool>.AddListener(Messages.GUI_ENABLE_SNAPSHOT, OnEnableSnapshot);
    }

    void OnDisable()
    {
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGUIEnable);
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE_SNAPSHOT, OnEnableSnapshot);
    }

    void OnEnableSnapshot(bool enabled)
    {
        enableSnapshot = enabled;
    }
    
    void OnGUIEnable(bool enabled)
    {
        _guiEnabled = enabled;
    }

    private IEnumerator Capture()
    {
        Messenger<bool>.Broadcast(Messages.GUI_ENABLE, false);

        yield return new WaitForEndOfFrame();

        Texture2D texture = ResourceManager.Instance.Snapshot();

        ShowPhotoGallery(texture);
    }

    private void ShowPhotoGallery(Texture2D texture)
    {
        WindowManager.CreatePhotoGallery("Photo Snapshot", texture);

        Messenger<bool>.Broadcast(Messages.GUI_ENABLE, true);
    }

    private void SwapGUIStyleState(GUIStyle style)
    {
        Texture2D tempTexture = style.normal.background;
        style.normal.background = style.active.background;
        style.active.background = tempTexture;
    }

    private void DrawTooltip()
    {
        if (string.IsNullOrEmpty(GUI.tooltip) == false)
        {
            Vector2 mousePosition = Event.current.mousePosition;
            GUIContent tooltip = new GUIContent(GUI.tooltip);

            Vector2 size = _tooltipStyle.CalcSize(tooltip);

            if (mousePosition.x + size.x + 3 > Screen.width)
            {
                mousePosition.x = Screen.width - size.x - 3;
            }

            GUI.Box(new Rect(mousePosition.x, mousePosition.y + 40, size.x + 3, size.y), GUI.tooltip, _tooltipStyle);
        }
    }
}
