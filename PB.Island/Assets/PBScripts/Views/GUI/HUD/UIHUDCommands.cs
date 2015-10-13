using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;

public class UIHUDCommands : GUIBase 
{
    public bool enabledHints = false;

	//private GUIStyle _inventoryActiveStyle;
    private GUIStyle _inventoryStyle;
	private Rect _inventoryRect;
	private bool _inventoryEnable = false;

    //private GUIStyle _questActiveStyle;
    private GUIStyle _questStyle;
	private Rect _questRect;
	private bool _questEnable = false;

    private GUIStyle _snapshotStyle;
    private GUIStyle _snapshotDisabledStyle;
    private GUIStyle _tooltipStyle;

    private bool _uploadingSnapshot = false;
    private bool _enableSnapshot = true;
    private bool _guiEnabled = true;
	
	// Use this for initialization
	public override void Start () 
    {
        _questStyle = new GUIStyle();
        _questStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/quest_normal");
        _questStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/quest_hover");
        
        _questRect = new Rect(Screen.width - 85, Screen.height - 80, _questStyle.normal.background.width, _questStyle.normal.background.height);
        

        //_questActiveStyle = new GUIStyle();
        //_questActiveStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/quest_active");

        _inventoryStyle = new GUIStyle();
        _inventoryStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/items_normal");
        _inventoryStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/items_hover");

		_inventoryRect = new Rect(Screen.width - 160, Screen.height - 80, _inventoryStyle.normal.background.width, _inventoryStyle.normal.background.height);
		
        

        //_inventoryActiveStyle = new GUIStyle();
        //_inventoryActiveStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/items_active");

        _snapshotStyle = new GUIStyle();
        _snapshotStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_normal");
        _snapshotStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_hover");
        _snapshotStyle.active.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_normal");

        _snapshotDisabledStyle = new GUIStyle();
        _snapshotDisabledStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/snapshot_disabled");

        _tooltipStyle = new GUIStyle();
        _tooltipStyle.fontSize = 13;
        _tooltipStyle.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        _tooltipStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/hintBox");
        _tooltipStyle.normal.textColor = Color.white;
        _tooltipStyle.padding = new RectOffset(3, 3, 3, 3);
	}

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

        if (_enableSnapshot)
        {
            GUIStyle snapStyle = _uploadingSnapshot ? _snapshotDisabledStyle : _snapshotStyle;

            if (GUI.Button(new Rect(Screen.width - 32, 0, 32, 32), new GUIContent("", "Polaroid"), snapStyle) && !_uploadingSnapshot)
            {
                _enableSnapshot = false;
                //StartCoroutine(Capture());
                GUIManager.Capture();
            }
        }
        else
        {
            GUI.DrawTexture(new Rect(Screen.width - 32, 0, 32, 32), _snapshotDisabledStyle.normal.background);
        }
        

        // Inventory Button
		if (GUI.Button(_inventoryRect, new GUIContent("", "Inventory"), _inventoryStyle))
		{
			_inventoryEnable = !_inventoryEnable;
            _questEnable = false;

			Messenger<bool>.Broadcast(Messages.INVENTORY_INVOKE, _inventoryEnable);
            Messenger<bool>.Broadcast(Messages.QUEST_JOURNAL_INVOKE, _questEnable);
		}
		
        // Quest Button
		if (GUI.Button(_questRect, new GUIContent("", "Quest"), _questStyle))
		{
			_questEnable = !_questEnable;
            _inventoryEnable = false;
			Messenger<bool>.Broadcast(Messages.QUEST_JOURNAL_INVOKE, _questEnable);
            Messenger<bool>.Broadcast(Messages.INVENTORY_INVOKE, _inventoryEnable);
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

    public override void OnEnable()
    {
        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGUIEnable);
        Messenger<bool>.AddListener(Messages.GUI_ENABLE_SNAPSHOT, OnEnableSnapshot);
    }

    public override void OnDisable()
    {
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGUIEnable);
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE_SNAPSHOT, OnEnableSnapshot);
    }

    void OnEnableSnapshot(bool enabled)
    {
        _enableSnapshot = enabled;
    }
    
    void OnGUIEnable(bool enabled)
    {
        _guiEnabled = enabled;
    }

}
