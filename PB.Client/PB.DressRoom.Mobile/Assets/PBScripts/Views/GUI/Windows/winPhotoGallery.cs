using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;

public class winPhotoGallery : WindowBase
{
    private Texture2D _texture = null;
    private Rect _photoRect;
    public bool _isUploading = false;
    public bool _uploaded = false;

    private GUIStyle _closeStyle;
    private GUIStyle _uploadStyle;
    private GUIStyle _textStyle;
    private GUIStyle _tooltipStyle;
    private GUIStyle _frameStyle;

    private string _tooltip = "";

    public winPhotoGallery(string name, string caption, Texture2D texture)
        : base(name, new GUIContent(""))
    {
        //angleRotation = -2f;
        IsDraggable = false;
        TweenDirection = WindowTweenDirection.FromTop;

        WindowStyle = new GUIStyle();

        _frameStyle = new GUIStyle();
        Texture2D background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_frame");
        _frameStyle.normal.background = background;

        _closeStyle = new GUIStyle();
        _closeStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_recycle_normal");
        _closeStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_recycle_hover");
        _closeStyle.active.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_recycle_active");

        _uploadStyle = new GUIStyle();
        _uploadStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_upload_normal");
        _uploadStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_upload_hover");
        _uploadStyle.active.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_upload_active");

        _textStyle = new GUIStyle();
        _textStyle.font = ResourceManager.Instance.LoadFont(ResourceManager.FontDroidSans);
        _textStyle.normal.textColor = new Color(0, 0, 0);

        _tooltipStyle = new GUIStyle();
        _tooltipStyle.fontSize = 13;
        _tooltipStyle.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        _tooltipStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/hintBox");
        _tooltipStyle.normal.textColor = Color.white;
        _tooltipStyle.padding = new RectOffset(3, 3, 3, 3);

        float width = background.width;
        float height = background.height;
        MakeCenter(width, height);

        _texture = texture;
        _margin = new Rect(42, 41, 47, 78);
        _photoRect = new Rect(_margin.x, _margin.y, width - _margin.width - _margin.x, height - _margin.height - _margin.y);
    }

    public void Initialize(Texture2D texture)
    {
        _texture = texture;
    }

    public void FinishUpload()
    {
        _isUploading = false;
        _uploaded = true;
    }

    public override void DrawUI()
    {
        if (string.IsNullOrEmpty(_tooltip) == false)
        {
            Vector2 mousePosition = Event.current.mousePosition;
            GUIContent tooltip = new GUIContent(_tooltip);

            Vector2 size = _tooltipStyle.CalcSize(tooltip);

            if (mousePosition.x + size.x + 3 > Screen.width)
            {
                mousePosition.x = Screen.width - size.x - 3;
            }

            GUI.Box(new Rect(mousePosition.x, mousePosition.y + 40, size.x + 3, size.y), _tooltip, _tooltipStyle);
        }
    }

    public override void Draw(int id)
    {
        base.Draw(id);

        if (_texture != null)
        {
            Matrix4x4 mat = GUI.matrix;
            GUIUtility.RotateAroundPivot(-4f, new Vector2(WindowRect.width / 2, WindowRect.height / 2));
            GUI.DrawTexture(_photoRect, _texture);
            GUI.matrix = mat;

            GUI.DrawTexture(new Rect(0, 0, WindowRect.width, WindowRect.height), _frameStyle.normal.background);

            float top = _margin.y + WindowRect.height - _margin.height - 35;

            if (!_isUploading)
            {
                if (GUI.Button(new Rect(WindowRect.width - _margin.width - _margin.x + 32, top, _closeStyle.normal.background.width, _closeStyle.normal.background.height), 
                    new GUIContent("", "Close"), _closeStyle))
                {
                    UnityEngine.Object.Destroy(_texture);

                    _uploaded = false;
                    _isUploading = false;

                    Messenger<bool>.Broadcast(Messages.GUI_ENABLE_SNAPSHOT, true);

                    Hide();
                }

                if (_uploaded == false)
                {
                    if (GUI.Button(new Rect(WindowRect.width - _margin.width - _margin.x - _closeStyle.normal.background.width + 25, top, _uploadStyle.normal.background.width, _uploadStyle.normal.background.height), 
                        new GUIContent("", " Upload photo and share to facebook "), _uploadStyle))
                    {
                        _isUploading = true;
                        Messenger<winPhotoGallery, Texture2D>.Broadcast(Messages.GUI_UPLOAD_PHOTO, this, _texture);
                    }
                }
                else
                {
                    GUIUtility.RotateAroundPivot(-4f, new Vector2(WindowRect.width / 2, WindowRect.height / 2));
                    GUI.Label(new Rect(_margin.x, top + 15, 300, 25), "Photo uploaded, please close..", _textStyle);
                }
            }
            else
            {
                GUIUtility.RotateAroundPivot(-4f, new Vector2(WindowRect.width / 2, WindowRect.height / 2));
                GUI.Label(new Rect(_margin.x, top + 15, 300, 25), "Uploading...", _textStyle);
            }            
        }

        _tooltip = GUI.tooltip;
    }

}
