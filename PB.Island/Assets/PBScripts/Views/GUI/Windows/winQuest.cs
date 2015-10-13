using UnityEngine;
using System.Collections.Generic;
using PB.Client;
using System;
using PB.Common;

public class winQuest : WindowBase
{
    #region MemVars & Props

    public int Selection = -1;
    
    protected Action<winQuest> _onCloseCallback = null;
    protected float _sizeX = 3;
    protected float _sizeY = 3;
    protected float _posX = 0;
    protected float _posY = 0;
    
    protected GUIStyle _textStyle;
    protected GUIStyle _closeStyle;
    private GUIStyle _menuSelectedStyle;
    private GUIStyle _menuUnselectedStyle;
    private GUIStyle _menuHoverStyle;
    private GUIStyle _contentHeadlineStyle;
    private GUIStyle _contentDescriptionStyle;

//    private Texture2D _separatorTexture;

    //private Rect _separatorRect;
    private Rect _contentHeadlineRect;
    private Rect _contentRect;
    private Rect _menuRect;

    private int _selected = 0;
    private Vector2 _scrollPosition;

    private Dictionary<int, Quest> _activeQuests = new Dictionary<int, Quest>();
    private GUISkin skin;

    #endregion


    #region Ctor

    public winQuest(string name, string caption, float posX, float posY, Action<winQuest> onClose)
        : base(name, new GUIContent(caption))
    {
        IsDraggable = false;

        _posX = posX;
        _posY = posY;
        _onCloseCallback = onClose;

        Margin = new Rect(14, 26, 34, 43);

        _textStyle = new GUIStyle();
        _textStyle.normal.textColor = Color.white;
        _textStyle.alignment = TextAnchor.MiddleCenter;

        skin = (GUISkin)Resources.Load("GUI/Skins/GUI_Quest");

        _closeStyle = new GUIStyle();
        _closeStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/close");

        _menuSelectedStyle = new GUIStyle();
        _menuSelectedStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/quest_bar");
        _menuSelectedStyle.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        _menuSelectedStyle.fontSize = 11;
        _menuSelectedStyle.normal.textColor = Color.white;
        _menuSelectedStyle.padding = new RectOffset(3, 3, 3, 3);
        _menuSelectedStyle.wordWrap = true;
        _menuSelectedStyle.hover = new GUIStyleState()
        {
            textColor = Color.red
        };

        GUIStyleState menuHoverStyle = new GUIStyleState();
        menuHoverStyle.textColor = Color.white;
        
        _menuUnselectedStyle = new GUIStyle();
        _menuUnselectedStyle.normal.textColor = new Color(0.15f, 0.2f, 0.2f);
        _menuUnselectedStyle.font = _menuSelectedStyle.font;
        _menuUnselectedStyle.fontSize = 11;
        _menuUnselectedStyle.padding = new RectOffset(3, 3, 3, 3);
        _menuUnselectedStyle.wordWrap = true;
        _menuUnselectedStyle.hover = menuHoverStyle;

        _contentHeadlineStyle = new GUIStyle();
        _contentHeadlineStyle.normal.textColor = new Color(0.2f, 0.44f, 0.85f);
        _contentHeadlineStyle.fontSize = 13;
        _contentHeadlineStyle.font = ResourceManager.Instance.LoadFont(ResourceManager.FontChunkfive);

        _contentDescriptionStyle = new GUIStyle();
        _contentDescriptionStyle.fontSize = 11;
        _contentDescriptionStyle.normal.textColor = new Color(0.2f, 0.2f, 0.2f);
        _contentDescriptionStyle.font = _menuSelectedStyle.font;
        _contentDescriptionStyle.wordWrap = true;

        WindowStyle = new GUIStyle();
        WindowStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/quest_window");

        TweenDirection = WindowTweenDirection.FromBottom;
        
        SetWindowRect(_posX, _posY, WindowStyle.normal.background.width, WindowStyle.normal.background.height);

        float startOffset = 10f;

        //_separatorTexture = ResourceManager.Instance.LoadTexture2D("GUI/HUD/quest_separator");

        //_separatorRect = new Rect(140, startOffset, 1, _separatorTexture.height);
        _menuRect = new Rect(7, startOffset, 130, 162 + 30);
        _contentRect = new Rect(150, startOffset, 170, 162);
       
        Initialize(caption);
    }

    #endregion


    #region Methods

    public void Initialize(string caption)
    {
        Caption = new GUIContent(caption);
    }

    public override void Draw(int id)
    {
        base.Draw(id);
        GUI.skin = skin;

        UpdateActiveQuests();

        //GUI.DrawTexture(_separatorRect, _separatorTexture);

        string content = "";

        #region Menu Area
        
        GUILayout.BeginArea(_menuRect);

        _scrollPosition = GUILayout.BeginScrollView(_scrollPosition, GUILayout.Width(_menuRect.width), GUILayout.Height(_menuRect.height));

        int index = 0;
        foreach (Quest quest in _activeQuests.Values)
        {
            
            GUIStyle style = _selected == index ? _menuSelectedStyle : _menuUnselectedStyle;

            if (_selected == index)
            {
                GUILayout.Label(quest.Description, style);
                content = quest.DescriptionNormal;
            }
            else
            {
                if (GUILayout.Button(quest.Description, style))
                {
                    _selected = index;
                }
            }

            index++;
        }
        /*
        //TESTCODES
        for (int i = 0; i < 15; i++)
        {
            GUIStyle style = _menuUnselectedStyle;
            GUILayout.Button("Demo Quest " + i.ToString(), style);
        }*/

        GUILayout.EndScrollView();

        GUILayout.EndArea();

        #endregion


        #region Content Area

        GUILayout.BeginArea(_contentRect);
        GUILayout.BeginVertical();

        string objective = _selected > -1 ?  (_activeQuests.Values.Count > 0 ? "Objective" : "") : "";
        GUILayout.Label(objective, _contentHeadlineStyle, GUILayout.Height(23));
        
        GUILayout.Label(content, _contentDescriptionStyle);

        GUILayout.EndVertical();
        GUILayout.EndArea();

        #endregion
    }

    protected void UpdateActiveQuests()
    {
        List<int> activeQuests = QuestEngine.Instance.ActiveQuests;

        _activeQuests.Clear();

        foreach (int qIdx in activeQuests)
        {
            Quest quest = QuestEngine.Instance.GetQuest(qIdx);
            if (quest != null && QuestEngine.Instance.IsQuestDone(qIdx) == false)
            {
                if (_activeQuests.ContainsKey(qIdx) == false)
                {
                    _activeQuests.Add(qIdx, quest);
                }
            }
        }
    }

    public override void Hide()
    {
        base.Hide();

        Selection = -1;
    }

    #endregion
}
