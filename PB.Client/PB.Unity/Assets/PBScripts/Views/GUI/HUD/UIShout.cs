using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Client;
using PB.Game;

public class UIShout : GUIBase 
{	
	public GUISkin skin;
    public int maxLogLines = 5;
    public bool enabledHints = false;

	private const int DEFAULT_Y_FROM_BOTTOM = 32;
		
	private GUIContent _shoutBoxContent;

    private GUIStyle _shoutBoxStyle;
	private GUIStyle _shoutTextStyle;
	private GUIStyle _shoutSendStyle;
	private GUIStyle _shoutLogButtonStyle;
    private GUIStyle _shoutBoxContentArea;
    private GUIStyle _shoutLogStyle;
    private GUIStyle _shoutLogTextStyle;
    private GUIStyle _tooltipStyle;

    private Rect _shoutBoxRect;
    private Rect _shoutLogRect;
    private Vector2 _shoutLogDesiredPos;
    private Vector2 _scrollPosition;
	private bool _isLogVisible = false;
    private float _smoothShow = 7f;

	private List<string> _logs = new List<string>();
    private bool _removeFocus = true;
    private Vector2 _shoutSendIconSize;

    public override void Awake()
    {
        base.Awake();

        skin = (GUISkin)Resources.Load("GUI/Skins/ShoutboxMetal");
    }

    public override void Start() 
    {
        _shoutBoxContentArea = new GUIStyle();
        _shoutBoxContentArea.border.top = 25;
        _shoutBoxContentArea.alignment = TextAnchor.MiddleCenter;

		_shoutBoxContent = new GUIContent();
		
		_shoutBoxStyle = new GUIStyle();
        _shoutBoxStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_bg");
		_shoutBoxStyle.alignment = TextAnchor.MiddleLeft;
		_shoutBoxStyle.border.left = 25;
		_shoutBoxStyle.border.right = 5;
        _shoutBoxStyle.border.top = 15;
		_shoutBoxRect = new Rect(6, Screen.height - DEFAULT_Y_FROM_BOTTOM - 12, 290, 39);
		
		_shoutTextStyle = new GUIStyle();
        _shoutTextStyle.margin.left = 10; 
        _shoutTextStyle.margin.right = 5;
        _shoutTextStyle.margin.top = 1;
        _shoutTextStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_log_bg");
		_shoutTextStyle.alignment = TextAnchor.MiddleLeft;
		
		// Shout Box Send Button
		_shoutSendStyle = new GUIStyle();
        _shoutSendStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_send_button_normal");
        _shoutSendStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_send_button_hover");
        _shoutSendStyle.active.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_send_button_active");
        //shoutSendStyle.margin.top = 1;
        _shoutSendStyle.alignment = TextAnchor.MiddleCenter;
        _shoutSendIconSize = new Vector2(_shoutSendStyle.normal.background.width, _shoutSendStyle.normal.background.height);

		// Shout Log Button
		_shoutLogButtonStyle = new GUIStyle();
        _shoutLogButtonStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_log_button_normal");
        _shoutLogButtonStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_log_button_hover");
        _shoutLogButtonStyle.active.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_log_button_active");
        _shoutLogButtonStyle.margin.top = 1;
		
		_shoutLogStyle = new GUIStyle();
        _shoutLogStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Shout/shout_log_bg");
        _shoutLogStyle.padding = new RectOffset(10, 5, 5, 5);
        _shoutLogStyle.wordWrap = true;
        _shoutLogStyle.font = ResourceManager.Instance.LoadFont(ResourceManager.FontDroidSans);
        _shoutLogStyle.fontSize = 11;

        _shoutLogTextStyle = new GUIStyle();
        _shoutLogTextStyle.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        _shoutLogTextStyle.fontSize = 11;
        _shoutLogTextStyle.wordWrap = true;

        _tooltipStyle = new GUIStyle();
        _tooltipStyle.fontSize = 13;
        _tooltipStyle.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        _tooltipStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/hintBox");
        _tooltipStyle.normal.textColor = Color.white;
        _tooltipStyle.padding = new RectOffset(3, 3, 3, 3);

        SetupShoutLogPosition();
	}

    private Vector2 _logScrollPosition;

    public override void OnGUI() 
    {
    	if (PBGameMaster.GameState != GameStateType.WorldEntered)
		{
			return;
		}

        if (!_guiEnabled)
        {
            return;
        }

        if (_removeFocus)
        {
            GUIUtility.keyboardControl = 0;
            _removeFocus = false;
        }

		GUI.skin = skin;        

        GUILayout.BeginArea(_shoutBoxRect, _shoutBoxStyle);
        GUILayout.FlexibleSpace();
        GUILayout.BeginHorizontal();

        GUI.SetNextControlName("ShoutBoxText");

        GUILayout.Space(7);
        _shoutBoxContent.text = GUILayout.TextField(_shoutBoxContent.text, GUILayout.Width(193));//, _shoutTextStyle, GUILayout.MaxWidth(_shoutBoxRect.width - 80 -21), GUILayout.Height(26));
        
        // Shout Box Send Button
        if (GUILayout.Button(new GUIContent("", "Send Shout"), _shoutSendStyle, GUILayout.Width(_shoutSendIconSize.x), GUILayout.Height(_shoutSendIconSize.y)))
        {
            SendShout();
            GUIUtility.ExitGUI();
        }

        GUILayout.FlexibleSpace();

        // Shout Box Log Button
        if (GUILayout.Button(new GUIContent("", "Show Logs"), _shoutLogButtonStyle, GUILayout.Width(21), GUILayout.Height(25)))
        {
            _isLogVisible = !_isLogVisible;
            if (!_isLogVisible)
            {
                SetupShoutLogPosition();
            }
        }

        GUILayout.FlexibleSpace();
        GUILayout.EndHorizontal();
        GUILayout.FlexibleSpace();
        GUILayout.EndArea();

        // Shout Log
		if (_isLogVisible)
		{
            float shoutLogXPos = Mathf.Lerp(_shoutLogRect.x, _shoutLogDesiredPos.x, Time.deltaTime * _smoothShow);
            float shoutLogYPos = Mathf.Lerp(_shoutLogRect.y, _shoutLogDesiredPos.y, Time.deltaTime * _smoothShow);

            if (Mathf.Abs(shoutLogYPos - _shoutLogDesiredPos.y) >= 0.01f)
            {
                _shoutLogRect = new Rect(_shoutLogRect.x, shoutLogYPos, _shoutLogRect.width, _shoutLogRect.height);
            }

            if (Mathf.Abs(shoutLogXPos - _shoutLogDesiredPos.x) >= 0.01f)
            {
                _shoutLogRect = new Rect(shoutLogXPos, _shoutLogRect.y, _shoutLogRect.width, _shoutLogRect.height);
            }

            GUILayout.BeginArea(_shoutLogRect, "", _shoutLogStyle);

            _scrollPosition = GUILayout.BeginScrollView(_scrollPosition, GUILayout.Width(_shoutLogRect.width), GUILayout.Height(_shoutLogRect.height));
            
            for (int i = _logs.Count - 1; i >= 0; i--)
            {
                GUILayout.Label(_logs[i], _shoutLogTextStyle);  
            }

            GUILayout.EndScrollView();

            GUILayout.EndArea();
		}

        if (Event.current.type == EventType.keyDown)
        {
            if (Event.current.character == '\t' && GUI.GetNameOfFocusedControl() == "ShoutBoxText")
            {
                _removeFocus = true;
            }

            if (Event.current.character == '\n' && GUI.GetNameOfFocusedControl() == "ShoutBoxText")
            {
                SendShout();
                _removeFocus = true;
            }

        }
        
        if (string.IsNullOrEmpty(GUI.tooltip) == false && enabledHints)
        {
            Vector2 mousePosition = Event.current.mousePosition;
            GUIContent tooltip = new GUIContent(GUI.tooltip);

            Vector2 size = _tooltipStyle.CalcSize(tooltip);
            if (mousePosition.x + size.x + 3 > Screen.width)
            {
                mousePosition.x = Screen.width - size.x - 3;
            }

            if (mousePosition.y - 20 + size.y > Screen.height)
            {
                mousePosition.y = Screen.height - size.y + 20;
            }

            GUI.Box(new Rect(mousePosition.x, mousePosition.y - 20, size.x + 3, size.y), GUI.tooltip, _tooltipStyle);
        }
	}

    protected void SetupShoutLogPosition()
    {
        _shoutLogDesiredPos = new Vector2(_shoutBoxRect.x, _shoutBoxRect.y - 78 - 7);
        _shoutLogRect = new Rect(_shoutLogDesiredPos.x, Screen.height + 1f, 290, 83);
    }

	protected void SendShout() 
	{
		UIChatBubble bubble = GameObject.Find(PBGameMaster.Game.Avatar.Id).GetComponent<UIChatBubble>();
				
		string text = (string)_shoutBoxContent.text;
		
		if (bubble != null && text != "")
		{
            if (text.Length > 140)
            {
                text = text.Substring(0, 140);
            }
            AddLog(string.Format("me:{0}", text));
			
			bubble.ShowMessage(text);
			_shoutBoxContent.text = "";

            Messenger<string>.Broadcast(Messages.CHAT, text);
		}
    }

    private void AddLog(string log)
    {
        if (_logs.Count == maxLogLines)
        {
            _logs.RemoveAt(0);
        }
        _logs.Add(log);
    }

    #region Messenger's Messages

    public override void OnEnable()
    {
        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGUIEnable);
        Messenger<Item, string>.AddListener(Messages.CHAT_LOG, OnChatLog);
    }

    public override void OnDisable()
    {
        Messenger<Item, string>.AddListener(Messages.CHAT_LOG, OnChatLog);
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGUIEnable);
    }

    protected void OnChatLog(Item item, string message)
    {
        AddLog(string.Format("{0}:{1}", item.AvatarName, message));
    }

    private bool _guiEnabled = true;
    protected void OnGUIEnable(bool enabled)
    {
        _guiEnabled = enabled;
    }


    #endregion
}
