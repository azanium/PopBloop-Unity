using UnityEngine;
using System.Collections;
using PB.Client;
using PB.Common;

public class Loader : MonoBehaviour 
{
	#region MemVars & Props


    /// <summary>
    /// Loading Banner for 940 pixel wide
    /// </summary>
    public Texture2D loadingBanner940;

    /// <summary>
    /// Loading Banner for 788 pixel wide
    /// </summary>
    public Texture2D loadingBanner788;
    
    /// <summary>
    /// Is the request using HTTP or HTTPS
    /// </summary>
    public bool isSecure = false;

    /// <summary>
    /// Default Level to load
    /// Blank to set it to PrivateRoom 
    /// </summary>
    public string defaultLevel = "";

    /// <summary>
    /// Web Server Type 
    /// </summary>
    public PopBloopSettings.DevelopmentModeType DevelopmentMode = PopBloopSettings.DevelopmentMode;

    /// <summary>
    /// Web Server Local
    /// </summary>
    public string serverLocal = PopBloopSettings.serverLocal;

    /// <summary>
    /// Web Server Staging
    /// </summary>
    public string serverStaging = PopBloopSettings.serverStaging;

    /// <summary>
    /// Web Server Production
    /// </summary>
    public string serverProduction = PopBloopSettings.serverProduction;

    private bool _gameLoaded = false;
    private string _worldName = "";
    private string _token = "";
    private bool _retryConnect = false;

	#endregion
	

	#region MonoBehavior Methods
	
    private void Awake()
    {
        PopBloopSettings.serverLocal = serverLocal;
        PopBloopSettings.serverStaging = serverStaging;
        PopBloopSettings.serverProduction = serverProduction;
        PopBloopSettings.DevelopmentMode = DevelopmentMode;
        PopBloopSettings.isSecure = isSecure;

        // Startup our States
        PBGameState.Register(PBDisconnectedState.Instance);
        PBGameState.Register(PBConnectedState.Instance);
        PBGameState.Register(PBAuthenticatedState.Instance);
        PBGameState.Register(PBLoadingState.Instance);
        PBGameState.Register(PBEnterWorldState.Instance);
    }

    private void Update()
    {
        if (_retryConnect)
        {
            Debug.Log("Loader: Trying to reconnect...");
            _retryConnect = false;

            StartCoroutine(Connect(false, _worldName));
        }
    }

    private void Start()
    {
        // Call the Game Init on JavaScript if any
        Application.ExternalCall("OnGameInit");

        // Non Web player will play with default room
        if (Application.platform != RuntimePlatform.WindowsWebPlayer && Application.platform != RuntimePlatform.OSXWebPlayer)
        {
            StartGame("");
        }
    }

    private void OnGUI()
    {
        if (!_gameLoaded)
        {
            if (Screen.width == 788)
            {
                if (loadingBanner788 != null)
                {
                    GUI.DrawTexture(new Rect(0, 0, Screen.width, Screen.height), loadingBanner788);
                }
            }
            else
            {
                if (loadingBanner940 != null)
                {
                    GUI.DrawTexture(new Rect(0, 0, Screen.width, Screen.height), loadingBanner940);
                }
            }

            UIProgressBar.Instance.Update(PBConstants.LOADINGBAR_INITIALIZING, 0, PBConstants.LOADINGBAR_FONTSIZE);
        }
    }

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
            url = PopBloopSettings.LobbyInfo + "?token=" + _token + "/" + Time.frameCount.ToString();
        }

        Debug.Log("Loader: Getting Lobby => " + url);
		WWW www = new WWW(url);
		
		yield return www;
		if (www.error != null)
		{
			Debug.LogError("Loader: Error getting lobby from => " + url);
            _retryConnect = true;

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

                    string server = lobbyConfig[0];
                    string world = worldName != "" ? worldName : lobbyConfig[1];

                    if (defaultLevel.Trim() != "")
                    {
                        world = defaultLevel;
                    }

                    pref.SetInt(PBConstants.PREF_LOADER, 99);
                    pref.SetString(PBConstants.PREF_TOKEN, _token);
                    pref.SetString(PBConstants.PREF_SERVER, server);
                    pref.SetString(PBConstants.PREF_WORLD, world);

                    Debug.Log("Loader: Start with Lobby Info: " + config.Trim() +", => World: " + world);


                    if (world.ToLower() == "privateroom" || world.Trim().Length == 0)
                    {
                        pref.SetInt(PBConstants.PREF_PRIVATEROOM, 1);
                    }

                    _gameLoaded = true;

					Application.LoadLevel("Game");
				}
				catch (System.Exception ex)
				{
					Debug.LogWarning(ex.ToString());

                    _retryConnect = true;
				}
			}
		}
	}
	
	private void GetUserId(string token)
	{
		_token = token;

        Debug.Log("token");

        //StartCoroutine(Connect(true));
	}
	
	#endregion
}
