using UnityEngine;
using System.Collections;
using PB.Client;
using System;

using PB.Common;
using PB.Game;

public class winInventory : WindowBase 
{
	#region MemVars & Props
    
    public int Selection = -1;

	protected Action<winInventory> _onCloseCallback = null;
	protected float _sizeX = 3;
	protected float _sizeY = 3;
	protected float _posX = 0;
	protected float _posY = 0;
    protected GUIStyle _textStyle;
    protected GUIStyle _closeStyle;
    protected GUIStyle _tooltipStyle;
	
	#endregion
	
	
	#region Ctor
	
	public winInventory(string name, string caption, float posX, float posY, int sizeX, int sizeY, Action<winInventory> onClose) 
		: base(name, new GUIContent(caption))
	{
		IsDraggable = false;
		 
		_posX = posX;
		_posY = posY;
		_sizeX = sizeX;
		_sizeY = sizeY;
		_onCloseCallback = onClose;

        Margin = new Rect(11, 9, 34, 43);
		
        _textStyle = new GUIStyle();
        _textStyle.normal.textColor = Color.white;
        _textStyle.alignment = TextAnchor.MiddleCenter;

        _closeStyle = new GUIStyle();
        _closeStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/close");

        _tooltipStyle = new GUIStyle();
        _tooltipStyle.fontSize = 13;
        _tooltipStyle.font = ResourceManager.Instance.LoadFont("GUI/HUD/DroidSans");
        _tooltipStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/hintBox");
        _tooltipStyle.normal.textColor = Color.white;
        _tooltipStyle.padding = new RectOffset(3, 3, 3, 3);

        WindowStyle = new GUIStyle();
        WindowStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/items_window");
        //WindowRect = new Rect(_posX, _posY, 167, 150);
        
        TweenDirection = WindowTweenDirection.FromBottom;
        
        SetWindowRect(_posX, _posY, WindowStyle.normal.background.width, WindowStyle.normal.background.height);

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

        GUI.BringWindowToFront(id);

        float buttonWidth = 36;
        float buttonHeight = 36;
        float iconWidth = 34;
        float iconHeight = 34;
                   
        float gapX = 2;
        float gapY = 2;

        int item = 0;
		for (int y = 0; y < _sizeY; y++)
		{
			for (int x = 0; x < _sizeX; x++)
			{
				float posx = Margin.x + (x * buttonWidth) + (gapX * x);
				float posy = Margin.y + (y * buttonHeight) + (gapY * y);

                if (item < InventoryEngine.Instance.Items.Count)
                {
                    GameItem gameItem = InventoryEngine.Instance.Items[item];

                    //Debug.LogWarning(string.Format("winInventory: Code: {0}, File: {1}", gameItem.Code, gameItem.Name));

                    GUIStyle style = InventoryEngine.GetStyleForItem(gameItem.Code);

                    

                    // Item Icon
                    Rect rect = new Rect(posx + buttonWidth / 2 - iconWidth / 2, posy , iconWidth, iconHeight);

                    if (Selection == item)
                    {
                        GUI.Box(rect, new GUIContent("", gameItem.Code), style);
                        
                        // Close Button
                        Rect closeRect = new Rect(posx + buttonWidth * 0.5f + 3, posy, 16, 16);
                        if (GUI.Button(closeRect, new GUIContent("", "remove"), _closeStyle))
                        {
                            Messenger<GameItem>.Broadcast(Messages.INVENTORY_REMOVE, gameItem);
                            Selection = -1;
                        }
                    }
                    else 
                    {
                        if (GUI.Button(rect, new GUIContent("", gameItem.Code), style))
                        {
                            Selection = item;
                        }
                        //Debug.LogWarning("no sel");
                    }

                    if (string.IsNullOrEmpty(GUI.tooltip) == false)
                    {
                        Vector2 mousePosition = Event.current.mousePosition;
                        GUIContent tooltip = new GUIContent(GUI.tooltip);

                        Vector2 size = _tooltipStyle.CalcSize(tooltip);

                        GUI.Box(new Rect(mousePosition.x, mousePosition.y + 40, size.x + 3, size.y), GUI.tooltip, _tooltipStyle);
                    }
                }
                else
                {
                    GUI.Box(new Rect(posx + buttonWidth / 2 - iconWidth / 2, posy, iconWidth, iconHeight), "");
                }

                item++;
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
