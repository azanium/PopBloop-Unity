using UnityEngine;
using System.Collections;

public class PBConstants
{
    /// <summary>
    /// Is the UI can be hovered
    /// </summary>
    public static bool IsHoverShoutUI = false;

    #region App Title

    public const string APP_TITLE = "PopBloop";

    #endregion

    #region Loading Bar

    public const int LOADINGBAR_FONTSIZE = 14;
    public const string LOADINGBAR_CONNECTING = "Connecting";
    public const string LOADINGBAR_AUTHENTICATING = "Authenticating";
    public const string LOADINGBAR_LOADING = "Loading World...";
    public const string LOADINGBAR_INITIALIZING = "Initializing...";
    public const string LOADINGBAR_INVENTORIES = "Loading Inventories..";
    public const string LOADINGBAR_GAME_DIALOGS = "Loading Game Dialogs..";
    public const string LOADINGBAR_GAME_QUESTS = "Loading Game Quests..";
    public const string LOADINGBAR_GAME_GESTICONS = "Loading Gesticons.."; 

    #endregion

    #region Game Loader's Profile Defaults

    // Defaults profile
    public const string PROFILE_PREF = "profile_preferences";
    public const string PROFILE_INGAME = "profile_ingame";

    #endregion

    #region Animations Constants

    // Constants for In Game 
    public const string ANIM_IDLE1 = "idle1";
    public const string ANIM_WALK = "walk";
    public const string ANIM_RUN = "run";
    public const string ANIM_JUMP = "jump";
    public const string ANIM_PICKUP = "pickup";
    public const string ANIM_SWIM = "swim";
    public const string ANIM_SWIM_IDLE = "swimidle";
    public const string ANIM_SIT = "sit";
    public const string ANIM_LIE = "lie";

    #endregion


    #region Command Constants

    public const string COMMAND_TRAVEL = "TRAVEL";
    public const string COMMAND_PICKUP = "PICKUP";
    public const string COMMAND_TALK = "TALK";
    public const string COMMAND_USE = "USE";

    #endregion


    #region Loader's Profile Preferences

    // Loader related
    public const string PREF_LOADER = "loader";
    public const string PREF_LOGGEDIN = "loggedin";
    public const string PREF_PASSWORD = "password";
    public const string PREF_PRIVATEROOM = "privateroom";
    public const string PREF_SERVER = "server";
    public const string PREF_TOKEN = "token";
    public const string PREF_USERNAME = "username";
    public const string PREF_WORLD = "world";
    public const string PREF_LEVELNAME = "levelname";
    public const string PREF_SPAWNGROUP = "spawngroup";

    #endregion

    #region In Game Preferences

    // In Game related
    public const string INGAME_MUSICONOFF = "musiconoff";

    #endregion
}
