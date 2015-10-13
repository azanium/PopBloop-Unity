using UnityEngine;
using System.Collections;
using PB.Common;
using PB.Client;

public class PBAuthenticatedState : PBGameState
{
    static public readonly PBGameState Instance = new PBAuthenticatedState();

    private PBDefaults _loaderPref;

    public PBAuthenticatedState()
        : base(GameStateType.Authenticated)
    {
        _loaderPref = PBDefaults.GetProfile(PBConstants.PROFILE_PREF);
    }

    public override void OnGUI(GameControllerBase mainGame)
    {
        base.OnGUI(mainGame);

        //UIProgressBar.Instance.Update(PBConstants.LOADINGBAR_AUTHENTICATING, 0, PBConstants.LOADINGBAR_FONTSIZE);
    }

    public override void OnAuthenticated(GameControllerBase mainGame, bool isAuth)
    {
        base.OnAuthenticated(mainGame, isAuth);

        if (isAuth == false)	// not authenticated
		{
            if (mainGame.loginWindow == null)
            {
                mainGame.Game.Avatar.Disconnect();
            }
            else
            {
                PBGameMaster.GameState = GameStateType.Disconnected;
                mainGame.loginWindow.InfoText = "Failed...";

                _loaderPref.SetInt(PBConstants.PREF_LOGGEDIN, 0);

                mainGame.ShowLoginWindow();
            }
		}
		else
		{
            if (mainGame.loginWindow != null)
            {
                mainGame.loginWindow.Hide();
            }

            _loaderPref.SetInt(PBConstants.PREF_LOGGEDIN, 1);
            _loaderPref.SetString(PBConstants.PREF_USERNAME, mainGame.Game.Avatar.Username);
            _loaderPref.SetString(PBConstants.PREF_PASSWORD, mainGame.Game.Avatar.Password);

			mainGame.Game.Avatar.LoadWorld(mainGame.Game.Settings.IsPrivateRoom);

            PBGameMaster.GameState = GameStateType.Loading;
		}
    }
}
