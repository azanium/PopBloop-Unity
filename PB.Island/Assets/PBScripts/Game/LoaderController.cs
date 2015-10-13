using UnityEngine;
using System.Collections;
using PB.Client;

public class LoaderController : MonoBehaviour
{
    #region MemVars & Props

    private bool retryConnect = false;
    private string worldName = "";

    #endregion


    #region MonoBehavior's Methods

    protected void Awake()
    {
    }

	protected void Start() 
    {
        StartGame("");
	}
	
	protected void Update() 
    {
        if (retryConnect)
        {
            Debug.Log("LoaderController: Trying to reconnect...");
            retryConnect = false;

            StartCoroutine(Connect(false, worldName));
        }
    }

    protected void OnGUI()
    {
    }

    #endregion


    #region Public Methods

    #endregion


    #region Private Methods

    public void StartGame(string worldName)
    {
        Debug.Log("Start Game...");

        StartCoroutine(Connect(false, worldName));
    }

    private IEnumerator Connect(bool useToken, string worldName)
    {
        string url = PopBloopSettings.LobbyInfo + "/" + Time.frameCount.ToString();
        if (useToken)
        {
            url = PopBloopSettings.LobbyInfo + "?token=" + MainController.WebSession + "/" + Time.frameCount.ToString();
        }

        Debug.Log("Loader: Getting Lobby => " + url);
        WWW www = new WWW(url);

        yield return www;
        if (www.error != null)
        {
            Debug.LogError("Loader: Error getting lobby from => " + url);
            retryConnect = true;

            yield return 0;
        }
        else
        {
            string config = www.text;

            UIBanner banner = this.GetComponent<UIBanner>();
            if (banner != null)
            {
                banner.isVisible = false;
            }

            if (config.Length > 0)
            {
                try
                {
                    string[] lobbyConfig = new string[] { config.Trim(), "" };
                    if (config.Contains(","))
                    {
                        // Lobby config is in the format: ipaddress:port,lobbyname", ex: 192.16.1.3:5055,Island
                        lobbyConfig = config.Split(new char[] { ',' });
                    }

                    PBDefaults pref = PBDefaults.GetProfile(PBConstants.PROFILE_PREF);

                    string server =  lobbyConfig[0];
                    string world = worldName != "" ? worldName : lobbyConfig[1];

                    if (GameSettings.DefaultLevel().Trim() != "")
                    {
                        world = GameSettings.DefaultLevel();
                        Debug.LogWarning("Using default Level: " + world);
                    }

                    pref.SetInt(PBConstants.PREF_LOADER, 99);
                    pref.SetString(PBConstants.PREF_TOKEN, MainController.WebSession);
                    pref.SetString(PBConstants.PREF_SERVER, server);
                    pref.SetString(PBConstants.PREF_WORLD, world);

                    Debug.Log("Loader: Start with Lobby Info: " + config.Trim() + ", => World: " + world);

                    if (world.ToLower() == "privateroom" || world.Trim().Length == 0)
                    {
                        pref.SetInt(PBConstants.PREF_PRIVATEROOM, 1);
                    }

                    // We are ready to launch our game
                    MainController.SetLoaderReady();

                    MainController.SwitchScene("Game");
                }
                catch (System.Exception ex)
                {
                    Debug.LogWarning(ex.ToString());

                    retryConnect = true;
                }
            }
        }
    }

    #endregion
}
