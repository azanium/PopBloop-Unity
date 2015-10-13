using UnityEngine;
using System.Collections;

public class WindowBase 
{	
	#region MemVars & Props
	
	protected Rect _windowRect = new Rect(0, 0, 100, 100);
	protected Rect _margin = new Rect(20, 20, 20, 20);
	
	public virtual Rect WindowRect
	{
		get { return _windowRect; }
		set { _windowRect = value; }
	}
	
	public virtual Rect Margin
	{
		get { return _margin; }
		set { _margin = value; }
	}
	
	protected GUIStyle _captionStyle = new GUIStyle();
	protected float _captionSize = 30;
	
	protected GUIContent _caption;
	public GUIContent Caption 
	{ 
		get { return _caption; }
		set 
		{
			if (_caption != value)
			{
				_caption = value;
				_captionSize = _captionStyle.CalcHeight(_caption, WindowRect.width);
			}
		}
	}

    private bool _bisVisible = true;
	public virtual bool IsVisible 
    {
        get { return _bisVisible; }
        protected set
        {
            _bisVisible = value;

            PrepareWindowRectForAnimation();
        }
    }
	
	protected static int WINID = 0;
	public int ID { get; set; }

    public bool IsDraggable = false;
    public bool IsAlwaysOnScreen = false;
    public bool IsResizable = false;

    public string Name = "";
    public GUIStyle WindowStyle { get; set; }

    public float smoothShow = 5.0f;
    protected Vector2 _desiredInPosition;
    protected Vector2 _desiredOutPosition;
    protected bool _drawSmoothHide = false;

    public float angleRotation = 0f;

    public enum WindowTweenDirection
    {
        None = 0,
        FromLeft,
        FromRight,
        FromBottom,
        FromTop,
        FromTopLeft,
        FromTopRight,
        FromBottomLeft,
        FromBottomRight,
    }

    public WindowTweenDirection TweenDirection = WindowTweenDirection.FromRight;
	
	#endregion


    #region Ctor

    public WindowBase(string name, GUIContent caption)
	{
		Name = name;
		Caption = caption;
		IsVisible = true;
		ID = WINID++;
		IsDraggable = true;
        WindowStyle = null;

		_captionStyle = new GUIStyle();
	}

    #endregion


    #region Methods

    public virtual void SetWindowRect(float x, float y, float width, float height)
    {
        _desiredInPosition = new Vector2(x, y);
        WindowRect = new Rect(x, y, width, height);

        // Reset positioning based on animation
        PrepareWindowRectForAnimation();
    }

    public virtual void Show()
	{
		IsVisible = true;
	}

	public virtual void Hide()
	{
        //_drawSmoothHide = true;
		IsVisible = false;
	}
	
	public virtual void Destroy()
	{
		WindowManager.Destroy(this.Name);
	}
	
	public Vector2 GetMousePosition()
	{
		Vector2 pos = Input.mousePosition;
		return new Vector2(pos.x - WindowRect.x, pos.y);
	}
	
	public void DrawGUI(GUISkin skin)
	{
        if (IsVisible)
        {
            GUI.skin = skin;

            if (IsAlwaysOnScreen)
            {
                float x = WindowRect.x;
                float y = WindowRect.y;
                if (x + WindowRect.width > Screen.width)
                {
                    x = Screen.width - WindowRect.width;
                }
                if (x < 0)
                {
                    x = 0;
                }
                if (y + WindowRect.height > Screen.height)
                {
                    y = Screen.height - WindowRect.height;
                }
                if (y < 0)
                {
                    y = 0;
                }
                WindowRect = new Rect(x, y, WindowRect.width, WindowRect.height);
            }

            DrawWindow();

            DrawUI();
            
            UpdateTween();
        }
	}

    private void DrawWindow()
    {
        if (WindowStyle != null)
        {
            WindowRect = GUI.Window(ID, WindowRect, Draw, Caption, WindowStyle);
        }
        else
        {
            WindowRect = GUI.Window(ID, WindowRect, Draw, Caption);
        }
    }

    private void UpdateTween()
    {
        switch (TweenDirection)
        {
            case WindowTweenDirection.FromTop:
            case WindowTweenDirection.FromBottom:
                {
                    float targetY = _drawSmoothHide ? _desiredOutPosition.y : _desiredInPosition.y;
                    float winRectY = Mathf.Lerp(WindowRect.y, targetY, Time.deltaTime * smoothShow);

                    if (Mathf.Abs(WindowRect.y - winRectY) >= 0.01f)
                    {
                        WindowRect = new Rect(WindowRect.x, winRectY, WindowRect.width, WindowRect.height);
                    }
                    else
                    {
                        if (_drawSmoothHide)
                        {
                            _drawSmoothHide = false;
                            IsVisible = false;
                        }
                    }

                } break;

            case WindowTweenDirection.FromLeft:
            case WindowTweenDirection.FromRight:
                {
                    float winRectX = Mathf.Lerp(WindowRect.x, _desiredInPosition.x, Time.deltaTime * smoothShow);

                    if (Mathf.Abs(WindowRect.x - winRectX) >= 0.01f)
                    {
                        WindowRect = new Rect(winRectX, WindowRect.y, WindowRect.width, WindowRect.height);
                    }
                    else
                    {
                        if (_drawSmoothHide)
                        {
                            _drawSmoothHide = false;
                            IsVisible = false;
                        }
                    }

                } break;

            case WindowTweenDirection.FromTopLeft:
            case WindowTweenDirection.FromTopRight:
            case WindowTweenDirection.FromBottomLeft:
            case WindowTweenDirection.FromBottomRight:
                {
                    float winRectX = Mathf.Lerp(WindowRect.x, _desiredInPosition.x, Time.deltaTime * smoothShow);
                    float winRectY = Mathf.Lerp(WindowRect.y, _desiredInPosition.y, Time.deltaTime * smoothShow);

                    if (Mathf.Abs(WindowRect.y - winRectY) >= 0.01f)
                    {
                        WindowRect = new Rect(WindowRect.x, winRectY, WindowRect.width, WindowRect.height);
                    }

                    if (Mathf.Abs(WindowRect.x - winRectX) >= 0.01f)
                    {
                        WindowRect = new Rect(winRectX, WindowRect.y, WindowRect.width, WindowRect.height);
                    }

                } break;
        }
    }
	
	public virtual void Draw(int id)
	{
		if (IsDraggable)
		{			
			GUI.DragWindow(new Rect(0, 0, WindowRect.width, _captionSize));
		}
		
		if (IsResizable)
		{
			if (GUI.Button(new Rect(WindowRect.width - 30, WindowRect.height - 30, 30, 30), ".."))
			{
				Debug.Log("resize");
			}
		}
	}

    public virtual void DrawUI()
    {
    }

    public virtual WindowBase MakeCenter(float width, float height)
    {
        float x = (Screen.width / 2) - (width / 2);
        float y = (Screen.height / 2) - (height / 2);

        WindowRect = new Rect(x, y, width, height);
        _desiredInPosition = new Vector2(x, y);

        PrepareWindowRectForAnimation();

        return this;
    }

    protected virtual void PrepareWindowRectForAnimation()
    {
        switch (TweenDirection)
        {
            case WindowTweenDirection.FromLeft:
                {
                    WindowRect = new Rect(-WindowRect.width, WindowRect.y, WindowRect.width, WindowRect.height);
                } break;

            case WindowTweenDirection.FromRight:
                {
                    WindowRect = new Rect(Screen.width  + 1f, WindowRect.y, WindowRect.width, WindowRect.height);
                } break;

            case WindowTweenDirection.FromBottom:
                {
                    WindowRect = new Rect(WindowRect.x, Screen.height + 1f, WindowRect.width, WindowRect.height);
                } break;

            case WindowTweenDirection.FromTop:
                {
                    WindowRect = new Rect(WindowRect.x, -WindowRect.height - 1f, WindowRect.width, WindowRect.height);
                } break;

            case WindowTweenDirection.FromTopLeft:
                {
                    WindowRect = new Rect(-WindowRect.width - 1f, -WindowRect.height - 1f, WindowRect.width, WindowRect.height);
                } break;

            case WindowTweenDirection.FromTopRight:
                {
                    WindowRect = new Rect(Screen.width + 1f, -WindowRect.height - 1f, WindowRect.width, WindowRect.height);
                } break;

            case WindowTweenDirection.FromBottomLeft:
                {
                    WindowRect = new Rect(-WindowRect.width - 1f, Screen.height + 1f, WindowRect.width, WindowRect.height);
                } break;

            case WindowTweenDirection.FromBottomRight:
                {
                    WindowRect = new Rect(Screen.width + 1f, Screen.height + 1f, WindowRect.width, WindowRect.height);
                } break;
        }
    }

    #endregion
}
