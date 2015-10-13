using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Common;
using PB.Game;


public class UIEnergy : GUIBase 
{	
	private int energyLevel = 50;
    private int coinLevel = 5000;

    public Vector2 Position = new Vector2(5, 10);

    private GUIStyle _styleFill;
    private GUIStyle _styleFrame;
    private GUIStyle _styleText;
    private GUIStyle _styleCoin;

    private Rect _frameRect;

    private Rect _fillRect;
    private Vector2 _offsetFill;

    private Vector2 _coinOffset;
    private Rect _coinRect;

    private Vector2 _coinLabelOffset;
    private Rect _coinLabelRect;

    private bool _enableGUI = true;
    private GUIContent _coinText;

    public override void Start() 
    {
        _styleCoin = new GUIStyle();
        _styleCoin.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Coins/coin");

        _styleFill = new GUIStyle();
        _styleFill.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Energy/energy_fill");
        _styleFrame = new GUIStyle();
        _styleFrame.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Energy/energy_frame");

        _coinText = new GUIContent();
        _styleText = new GUIStyle();
        _styleText.font = ResourceManager.Instance.LoadFont(ResourceManager.FontComicSerif);
        _styleText.fontSize = 18;
        _styleText.normal.textColor = new Color(0.2f, 0.2f, 0.2f);
        _styleText.alignment = TextAnchor.MiddleCenter;
        _styleText.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Coins/coin_bg");
		
		_frameRect = new Rect(Position.x, Position.y, _styleFrame.normal.background.width, _styleFrame.normal.background.height);
        _offsetFill = new Vector2(5, 11);
        _fillRect = new Rect(_frameRect.x + _offsetFill.x, _frameRect.y + _offsetFill.y, 139, 14);

        _coinOffset = new Vector2(10, 5);
        _coinRect = new Rect(_frameRect.x + _frameRect.width + _coinOffset.x, _frameRect.y + _coinOffset.y, _styleCoin.normal.background.width, _styleCoin.normal.background.height);

        _coinLabelOffset = new Vector2(-5, 1);
        _coinLabelRect = new Rect(_coinRect.x + _coinRect.width + _coinLabelOffset.x, _coinRect.y + _coinLabelOffset.y, 200, 51);
	}

    public override void OnGUI() 
    {
        if (PBGameMaster.GameState != GameStateType.WorldEntered)
		{
			return;
		}

        if (_enableGUI == false)
        {
            return;
        }

        energyLevel = InventoryEngine.Instance.GetEquipmentCount(Equipments.EquipmentType.Energy);
        coinLevel = InventoryEngine.Instance.GetEquipmentCount(Equipments.EquipmentType.Coin);

        _coinText.text = coinLevel.ToString();
        Vector2 coinTextSize = _styleText.CalcSize(_coinText);

        _coinLabelRect.width = coinTextSize.x + 10;
        _coinLabelRect.height = coinTextSize.y - 3;

        GUI.Label(_coinLabelRect, coinLevel.ToString(), _styleText);

        GUI.Box(_frameRect, "", _styleFrame);

        GUI.BeginGroup(new Rect(_fillRect.x, _fillRect.y, ((float)energyLevel / 100) * _fillRect.width, _fillRect.height));

            GUI.Box(new Rect(0, 0, _fillRect.width, _fillRect.height - 1), "", _styleFill);

        GUI.EndGroup();

        GUI.DrawTexture(_coinRect, _styleCoin.normal.background);

	}

    public override void OnEnable() 
	{
		Messenger<int>.AddListener(Messages.ENERGY_LEVEL, OnSetEnergyLevel);	
		Messenger<int>.AddListener(Messages.ENERGY_LEVEL_INC, OnIncreaseEnergyLevel);	
		Messenger<int>.AddListener(Messages.ENERGY_LEVEL_DEC, OnDecreaseEnergyLevel);
        Messenger<int>.AddListener(Messages.COIN_LEVEL, OnSetCoinLevel);
        Messenger<bool>.AddListener(Messages.GUI_ENABLE, OnGuiEnable);
	}

    public override void OnDisable()
	{
		Messenger<int>.RemoveListener(Messages.ENERGY_LEVEL, OnSetEnergyLevel);
		Messenger<int>.RemoveListener(Messages.ENERGY_LEVEL_INC, OnIncreaseEnergyLevel);	
		Messenger<int>.RemoveListener(Messages.ENERGY_LEVEL_DEC, OnDecreaseEnergyLevel);
        Messenger<int>.RemoveListener(Messages.COIN_LEVEL, OnSetCoinLevel);
        Messenger<bool>.RemoveListener(Messages.GUI_ENABLE, OnGuiEnable);
	}
	
	void OnSetEnergyLevel(int energy)
	{
		energyLevel = Mathf.Max(0, energy);
		energyLevel = Mathf.Min(100, energyLevel);
	}
	
	void OnIncreaseEnergyLevel(int increase)
	{
		energyLevel += increase;
		energyLevel = Mathf.Min(100, energyLevel);
	}
	
	void OnDecreaseEnergyLevel(int decrease)
	{
		energyLevel -= decrease;
		energyLevel = Mathf.Max(0, energyLevel);
	}

    void OnSetCoinLevel(int coin)
    {
        coinLevel = coin;
    }

    void OnGuiEnable(bool enabled)
    {
        _enableGUI = enabled;
    }

}
