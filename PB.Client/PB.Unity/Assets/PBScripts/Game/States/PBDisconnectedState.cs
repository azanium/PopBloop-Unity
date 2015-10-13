using UnityEngine;
using System.Collections;

using PB.Client;
using PB.Game;
using System.Timers;

public class PBDisconnectedState : PBGameState
{
    #region MemVars & Props

    static public readonly PBGameState Instance = new PBDisconnectedState();

    private Timer _countdownTimer;
    private int _counter = 10;
    private bool _isDisconnected = false;

    private Rect _retryButtonRect;
    private Rect _countDownTextRect;
    private Rect _takeMeNowTextRect;
    private Rect _screenRect;
    
    private GUIStyle _retryButtonStyle;
    private GUIStyle _countDownTextStyle;
    private GUIStyle _takeMeNowTextStyle;
    private GUIContent _tempContent;

    #endregion


    #region Ctor

    public PBDisconnectedState()
        : base(GameStateType.Disconnected)
    {
        _countdownTimer = new Timer(1000);
        _countdownTimer.Elapsed += new ElapsedEventHandler(_countdownTimer_Elapsed);

        float width = 181;
        float height = 59;
        float x = (Screen.width * 0.5f) - (width * 0.5f); //382;//
        float y = 308;//260;

        _retryButtonStyle = new GUIStyle();
        _retryButtonStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Banner/ditu_retry_normal");
        _retryButtonStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/Banner/ditu_retry_hover");
        _retryButtonStyle.active.background = ResourceManager.Instance.LoadTexture2D("GUI/Banner/ditu_retry_active");

        _retryButtonRect = new Rect(x, y, width, height);

        _countDownTextStyle = new GUIStyle();
        _countDownTextStyle.normal.textColor = Color.white;
        _countDownTextStyle.fontSize = 18;
        _countDownTextStyle.font = (Font)ResourceManager.Instance.LoadFont(ResourceManager.FontChunkfive);

        _takeMeNowTextStyle = new GUIStyle();
        _takeMeNowTextStyle.normal.textColor = Color.white;
        _takeMeNowTextStyle.fontSize = 30;
        _takeMeNowTextStyle.font = (Font)ResourceManager.Instance.LoadFont(ResourceManager.FontChunkfive);

        _countDownTextRect = new Rect(/*289, 142*/ 359, 238, Screen.width, Screen.height);
        _takeMeNowTextRect = new Rect(/*380, 222*/ 378, 273, Screen.width, Screen.height);
        _screenRect = new Rect(0, 0, Screen.width, Screen.height);

        _tempContent = new GUIContent();
    }

    #endregion


    #region Custom Methods

    private void _countdownTimer_Elapsed(object sender, ElapsedEventArgs e)
    {
        if (_counter > 0)
        {
            _counter--;
        }
        else
        {
            _countdownTimer.Stop();
        }
    }

    public override void OnGUI(GameControllerBase mainGame)
    {
        base.OnGUI(mainGame);

        if (_isDisconnected)
        {
            GUI.depth = 0;

            GUI.DrawTexture(_screenRect, GameSettings.Background());

            if (GUI.Button(_retryButtonRect, "", _retryButtonStyle))
            {
                _isDisconnected = false;

                ReloadLevel(mainGame);
            }

            string takeYouBackString = Lang.Localized("We'll take you back in ") + _counter.ToString() + Lang.Localized(" sec");
            _tempContent.text = takeYouBackString;
            Vector2 size = _countDownTextStyle.CalcSize(_tempContent);
            _countDownTextRect.x = Screen.width * 0.5f - size.x * 0.5f;

            GUI.Label(_countDownTextRect, takeYouBackString, _countDownTextStyle);

            string takeMeBackString = Lang.Localized("Take Me Back");
            _tempContent.text = takeMeBackString;
            size = _takeMeNowTextStyle.CalcSize(_tempContent);
            _takeMeNowTextRect.x = Screen.width * 0.5f - size.x * 0.5f;
            GUI.Label(_takeMeNowTextRect, takeMeBackString, _takeMeNowTextStyle);

            if (_counter == 0)
            {
                _isDisconnected = false;

                ReloadLevel(mainGame);
            }
        }
    }

    private void ReloadLevel(GameControllerBase mainGame)
    {
        PBDefaults pref = PBDefaults.GetProfile(PBConstants.PROFILE_INGAME);
        string worldName = pref.GetString(PBConstants.PREF_LEVELNAME);
        
        Debug.LogWarning("Retrying World: " + worldName);
        Messenger<bool>.Broadcast(Messages.BANNER_SETVISIBILITY, false);

        if (worldName != null && worldName != "")
        {
            //mainGame.ReloadLevel(worldName, false);
            MainController.SwitchScene("Loader");
        }
        else
        {
            //mainGame.ReloadLevel(worldName, false);
            MainController.SwitchScene("Loader");
        }
    }

    public override void OnDisconnect(GameControllerBase mainGame, ExitGames.Client.Photon.StatusCode returnCode)
    {
        base.OnDisconnect(mainGame, returnCode);

        _isDisconnected = true;
        _counter = 10;  // 10 seconds countdown

        Debug.Log("MainGame: Disconnected with returnCode " + returnCode);

        WindowManager.IsVisible = false;

        Messenger<string>.Broadcast(Messages.BANNER_CHANGE, "GUI/Banner/ditu_bg");
        Messenger<bool>.Broadcast(Messages.BANNER_SETVISIBILITY, true);

        PBGameMaster.GameState = GameStateType.Disconnected;

        //mainGame.Clear();

        _countdownTimer.Start();
    }

    #endregion
}

