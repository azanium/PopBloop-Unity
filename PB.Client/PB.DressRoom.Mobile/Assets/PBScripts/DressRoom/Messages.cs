using UnityEngine;
using System.Collections;

public class Messages {
	
    // ANIMATIONS
    public static string ANIMATE_AVATAR = "msgAnimBye";

    // BANNER
    public static string BANNER_CHANGE = "msgBannerChange";
    public static string BANNER_SETVISIBILITY = "msgBannerSetViibility";

    // Gallery
    public static string GUI_ENABLE = "msgGUIEnable";
    public static string GUI_UPLOAD_PHOTO = "msgGUIUploadPhoto";
    public static string GUI_ENABLE_SNAPSHOT = "msgGUIEnableSnapshot";

    // Level
    public static string LEVEL_CHANGE = "msgChangeLevel";

    // Quest and Inventory
	public static string INVENTORY_INVOKE = "msgInvokeInventory";
    public static string INVENTORY_REMOVE = "msgInventoryRemove";
    public static string QUEST_JOURNAL_INVOKE = "msgInvokeQuestJournal";

    /// <summary>
    /// Chat Messages
    /// </summary>
    public static string CHAT = "msgChat";
    public static string CHAT_LOG = "msgChatLog";
    public static string BUBBLE_ADD = "msgAddBubble";
    public static string BUBBLE_REMOVE = "msgRemoveBubble";
    public static string BUBBLE_POSITION = "msgBubblePosition";

	/// Energy Level
	public static string ENERGY_LEVEL = "msgEnergyLevel";
	public static string ENERGY_LEVEL_INC = "msgEnergyLevelInc";
	public static string ENERGY_LEVEL_DEC = "msgEnergyLevelDec";
    public static string COIN_LEVEL = "msgCoinLevel";

    /// Player Controller
    public static string PLAYER_MOVETO = "msgPlayerMoveTo";
    public static string PLAYER_BEGINMOVE = "msgPlayerBeginMove";
    public static string PLAYER_MOVED = "msgPlayerMoved";
}
