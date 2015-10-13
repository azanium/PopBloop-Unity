using UnityEngine;
using System.Collections;
using PB.Common;

public class PBConnectedState : PBGameState
{
    static public readonly PBGameState Instance = new PBConnectedState();

    private PBDefaults _loaderPref;

    public PBConnectedState()
        : base(GameStateType.Connected)
    {
        _loaderPref = PBDefaults.GetProfile(PBConstants.PROFILE_PREF);
    }

    public override void OnGUI(GameControllerBase mainGame)
    {
        base.OnGUI(mainGame);

        //UIProgressBar.Instance.Update(PBConstants.LOADINGBAR_CONNECTING, 0, PBConstants.LOADINGBAR_FONTSIZE);
    }

    public override void OnConnect(GameControllerBase mainGame)
    {
        base.OnConnect(mainGame);

        Debug.Log("MainGame: Connected");

        // Setup our Inventory and Quest Engine
        InventoryEngine.Instance.Initialize(mainGame);
        QuestEngine.Instance.Initialize(mainGame);
        DialogEngine.Instance.Initialize(mainGame);

        PBGameMaster.GameState = GameStateType.Connected;

        if (Application.platform == RuntimePlatform.WindowsEditor)
        {
            mainGame.Game.Avatar.Token = "b184145cd5405aa0cadd91c724198613";//"f8caa53b14f9d5d35fdc84756c5ff8ba";
        }
        else if (Application.platform == RuntimePlatform.WindowsPlayer)
        {
            mainGame.Game.Avatar.Token = "f61297bd64ce510052c3864b2fe22d02";
        }

        if (Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor ||
            Application.platform == RuntimePlatform.WindowsPlayer || Application.platform == RuntimePlatform.OSXPlayer)
        {
            if (_loaderPref.GetInt(PBConstants.PREF_LOGGEDIN) == 1)
            {
                PBGameMaster.GameState = GameStateType.Authenticating;

                string username = _loaderPref.GetString(PBConstants.PREF_USERNAME);
                string password = _loaderPref.GetString(PBConstants.PREF_PASSWORD);
                
                Debug.Log("MainGame: Logging in with Username: " + username + ", Password: " + password);

                mainGame.Game.Avatar.Login(username, password);
            }
            else
            {
                mainGame.ShowLoginWindow();
            }
        }

        if (Application.platform == RuntimePlatform.WindowsWebPlayer || Application.platform == RuntimePlatform.OSXWebPlayer)
        {
            PBGameMaster.GameState = GameStateType.Authenticating;

            Debug.Log("MainGame: Authenticating... with Token: " + mainGame.Game.Avatar.Token);

            mainGame.Game.Avatar.Authenticate(mainGame.Game.Avatar.Token);
        }
    }
}
