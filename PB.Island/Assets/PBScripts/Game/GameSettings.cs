using UnityEngine;
using System.Collections;
using PB.Client;

public class GameSettings : MonoBehaviour
{
    #region MemVars & Props

    static private GameSettings gameSettings = null;

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
    /// Development Server address
    /// </summary>
    public string serverDevelopment = PopBloopSettings.serverDevelopment;

    /// <summary>
    /// Web Server Staging
    /// </summary>
    public string serverStaging = PopBloopSettings.serverStaging;

    /// <summary>
    /// Web Server Production
    /// </summary>
    public string serverProduction = PopBloopSettings.serverProduction;

    /// <summary>
    /// First background image for 788w
    /// </summary>
    public Texture2D background;

    /// <summary>
    /// Second background image for 940w
    /// </summary>
    public Texture2D backgroundHi;

    /// <summary>
    /// GUI Skin for the Game
    /// </summary>
    public GUISkin defaultGuiSkin;

    #endregion


    #region Mono Methods

    protected void Awake()
    {
        gameSettings = this;

        PopBloopSettings.serverLocal = serverLocal;
        PopBloopSettings.serverStaging = serverStaging;
        PopBloopSettings.serverProduction = serverProduction;
        PopBloopSettings.serverDevelopment = serverDevelopment;
        PopBloopSettings.DevelopmentMode = DevelopmentMode;
        PopBloopSettings.isSecure = isSecure;

        PBGameState.Register(PBDisconnectedState.Instance);
        PBGameState.Register(PBConnectedState.Instance);
        PBGameState.Register(PBAuthenticatedState.Instance);
        PBGameState.Register(PBLoadingState.Instance);
        PBGameState.Register(PBEnterWorldState.Instance);
    }

    protected void OnDestroy()
    {
        background = null;
        backgroundHi = null;
    }

    #endregion


    #region Static Methods

    static public Texture2D Background()
    {
        if (Screen.width == 788)
        {
            return gameSettings.background;
        }
        else
        {
            return gameSettings.backgroundHi;
        }
    }

    static public GUISkin GuiSkin()
    {
        return gameSettings.defaultGuiSkin;
    }

    static public string DefaultLevel()
    {
        return gameSettings.defaultLevel;
    }

    #endregion

}
