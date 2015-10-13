using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Game;

public class Processor {

    /// <summary>
    /// Built in animations
    /// </summary>
    public static List<string> BuiltInAnimations = new List<string>()
    {
        "idle",
        "walk",
        "run",
        "jump",
        "bye",
        "happy"
    };

    /*
    /// <summary>
    /// Animation that can be triggered on Shout
    /// </summary>
    public static Dictionary<string, string> InShoutAnimationCommands = new Dictionary<string, string>()
    {
        { "#bye", "bye" },
        { "#happy", "happy" },
        { "#sit", "@sitground" },
        { "#iwak", "@iwak_peyek" },
        { "#dance", "@dance" },
        { "#swim", "@swim" },
        { "#swimidle", "@swimidle" },
        { "#gangnam", "@gangnam" },
        { "#victory", "victory" },
        { "#you", "you" },
        { "#metal", "metal" },
        { "#handsome", "handsome" },
        { "#pretty", "pretty" },
        { "#chibi", "chibi" },
    };*/

    public static Dictionary<string, System.Action<GameControllerBase, string>> ProcessorExecutionCommands = new Dictionary<string, System.Action<GameControllerBase, string>>()
    {
        { "ccu", (mainGame, param) => { Messenger<bool>.Broadcast(Messages.GUI_DEBUG_CCU, true); } },
        { "noccu", (mainGame, param) => { Messenger<bool>.Broadcast(Messages.GUI_DEBUG_CCU, false); } },
        { "debug", (mainGame, param) => { Messenger<bool>.Broadcast(Messages.GUI_DEBUG_DIAG, true); } }, 
        { "nodebug", (mainGame, param) => { Messenger<bool>.Broadcast(Messages.GUI_DEBUG_DIAG, false); } }, 
        { "fps", (mainGame, param) => { Messenger<bool>.Broadcast(Messages.GUI_DEBUG_FPS, true); } },
        { "nofps", (mainGame, param) => { Messenger<bool>.Broadcast(Messages.GUI_DEBUG_FPS, false); } },
        { "portal", (mainGame, param) => {
            Debug.LogWarning(string.Format("Param: '{0}'", param));
            if (param.Trim().Length > 0) { Messenger<string>.Broadcast(Messages.LEVEL_CHANGE, param); } } }
    };

    /// <summary>
    /// Get List of tokens from a given text
    /// </summary>
    /// <param name="text"></param>
    /// <returns></returns>
    public static string[] GetTokens(string text)
    {
        string[] tokens = text.Split(' ', '\t', '\t', '\n', '.', ',', ';', ':', '!', '?');

        return tokens;
    }

    /// <summary>
    /// Get @username address as mentions on a text
    /// </summary>
    /// <param name="text">The text</param>
    /// <returns>List of usernames without @</returns>
    public static string[] GetAddressName(string text)
    {
        string[] tokens = GetTokens(text);

        List<string> dms = new List<string>();

        foreach (string token in tokens)
        {
            if (token.Length > 1 && token[0] == '@')
            {
                dms.Add(token.Substring(1, token.Length - 1));
            }
        }

        return dms.ToArray();
    }

    public static void ExecuteCommand(GameControllerBase mainGame, string command, string param)
    {
        foreach (KeyValuePair<string, System.Action<GameControllerBase, string>> cmd in ProcessorExecutionCommands)
        {
            if (command == cmd.Key)
            {
                if (cmd.Value != null)
                {
                    cmd.Value(mainGame, param);
                }
            }
        }
    }

    #region Processor Internal Command Methods

    #endregion
}
