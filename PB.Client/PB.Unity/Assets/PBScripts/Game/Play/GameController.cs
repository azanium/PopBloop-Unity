using System;
using System.Collections;
using System.Collections.Generic;

using UnityEngine;

using PB.Client;
using PB.Common;
using PB.Game;
using LitJson;

public class GameController : GameControllerBase
{
    #region MemVars & Props

    static public GameController gameController;

    #endregion


    #region Ctor

    public GameController()
        : base()
    {
    }

    #endregion


    #region Game Methods

    protected void Awake()
    {
        gameController = this;
    }

    protected override void Start()
    {
        base.Start();

        if (GetComponent<UIBanner>() == false)
        {
            gameObject.AddComponent<UIBanner>().isVisible = false;
        }
    }

    #region Game Messages

    protected void OnEnable()
    {
        Messenger<bool>.AddListener(Messages.INVENTORY_INVOKE, OnInventoryInvoke);
        Messenger<bool>.AddListener(Messages.QUEST_JOURNAL_INVOKE, OnQuestJournalInvoke);
        Messenger<string>.AddListener(Messages.CHAT, OnShout);
        Messenger<string>.AddListener(Messages.LEVEL_CHANGE, OnLevelChange);
        Messenger<string>.AddListener(Messages.BANNER_CHANGE, OnBannerChange);
        Messenger<bool>.AddListener(Messages.BANNER_SETVISIBILITY, OnBannerSetVisibility);
        Messenger<GameItem>.AddListener(Messages.INVENTORY_REMOVE, OnInventoryRemove);
        Messenger<Vector3>.AddListener(Messages.PLAYER_BEGINMOVE, OnPlayerBeginMove);
        Messenger<string>.AddListener(Messages.REDEEM_SEND_EMAIL, OnSendRedeemEmail);
        Messenger<string>.AddListener(Messages.REDEEM_AVATAR_GENERATE, OnRedeemAvatarGenerate);

        PBThirdPersonController.OnPlayerMovementStopped += new Action(OnPlayerMoved);
    }

    protected void OnDisable()
    {
        Messenger<bool>.RemoveListener(Messages.INVENTORY_INVOKE, OnInventoryInvoke);
        Messenger<bool>.RemoveListener(Messages.QUEST_JOURNAL_INVOKE, OnQuestJournalInvoke);
        Messenger<string>.RemoveListener(Messages.CHAT, OnShout);
        Messenger<string>.RemoveListener(Messages.LEVEL_CHANGE, OnLevelChange);
        Messenger<string>.RemoveListener(Messages.BANNER_CHANGE, OnBannerChange);
        Messenger<bool>.RemoveListener(Messages.BANNER_SETVISIBILITY, OnBannerSetVisibility);
        Messenger<GameItem>.RemoveListener(Messages.INVENTORY_REMOVE, OnInventoryRemove);
        Messenger<Vector3>.RemoveListener(Messages.PLAYER_BEGINMOVE, OnPlayerBeginMove);
        Messenger<string>.RemoveListener(Messages.REDEEM_SEND_EMAIL, OnSendRedeemEmail);
        Messenger<string>.RemoveListener(Messages.REDEEM_AVATAR_GENERATE, OnRedeemAvatarGenerate);

        PBThirdPersonController.OnPlayerMovementStopped -= new Action(OnPlayerMoved);
    }

    protected void OnRedeemAvatarGenerate(string avatarItemId)
    {
        StartCoroutine(GenerateRedeemAvatar(avatarItemId));
    }

    protected IEnumerator GenerateRedeemAvatar(string avatarItemId)
    {
        WWW asset = AssetsManager.DownloadString(PopBloopSettings.GenerateRedeemAvatarUrl(avatarItemId) + Time.frameCount);

        yield return asset;

        if (asset.error != null)
        {
            Debug.LogWarning("Failed to request Reward Redeem, retrying now...");
            StartCoroutine(GenerateRedeemAvatar(avatarItemId));
        }
        else
        {
            string json = asset.text.Trim();
            List<Dictionary<string, string>> returnMap = null;
            try
            {
                returnMap = JsonMapper.ToObject<List<Dictionary<string, string>>>(json);
            }
            catch (Exception ex)
            {
                Debug.LogWarning("Failed to decode RedeemAvatar... => " + ex.ToString());
            }

            if (returnMap != null)
            {
                string redeemCodes = "";
                foreach (Dictionary<string, string> map in returnMap)
                {
                    if (map.ContainsKey("code"))
                    {
                        if (redeemCodes != "")
                        {
                            redeemCodes += ".";
                        }
                        string code = map["code"];
                        redeemCodes += code;
                    }
                }

                if (string.IsNullOrEmpty(redeemCodes) == false)
                {
                    Debug.Log("Sending RedeemCodes to user's email: " + redeemCodes);
                    StartCoroutine(SendRedeemEmail(redeemCodes));
                }
            }
        }
    }

    protected void OnSendRedeemEmail(string redeem)
    {
        StartCoroutine(SendRedeemEmail(redeem));
    }

    protected IEnumerator SendRedeemEmail(string redeem)
    {
        WWW www = new WWW(PopBloopSettings.RedeemCodeSendMail(_game.Avatar.Token, redeem));

        Debug.Log(string.Format("Sending Redeem Code Email to session: {0}, with Redeem: {1}", _game.Avatar.Token, redeem));

        yield return www;

        if (www.error != null)
        {
            Debug.LogError("Failed to send Redeem Code to user's email");
        }
        else
        {
            string json = www.text.Trim();
            Dictionary<string, System.Object> result = JsonMapper.ToObject<Dictionary<string, System.Object>>(json);

            bool success = (bool)result["success"];
            if (success)
            {
                Debug.Log("Sending Redeem Code to user success");
            }
            else
            {
                Debug.Log("Failed sending Redeem Code to user with message: " + result["message"]);
            }

        }
    }

    protected void OnPlayerBeginMove(Vector3 position)
    {
        _wayPoint.transform.position = position;
        _wayPoint.renderer.enabled = true;
    }

    protected void OnPlayerMoved()
    {
        _wayPoint.renderer.enabled = false;
    }

    protected void OnShout(string message)
    {
        if (Game != null)
        {
            if (message.Length > 0 && message[0] == '/')
            {
                string shout = message.Substring(1, message.Length - 1).Trim();

                int startParamIndex = shout.IndexOf(" ");

                string command = "";
                string param = "";

                if (startParamIndex > -1)
                {
                    command = shout.Substring(0, startParamIndex).Trim();
                    param = shout.Substring(startParamIndex, shout.Length - startParamIndex).Trim();
                }
                else
                {
                    command = shout.Trim();
                }

                Debug.LogWarning(string.Format("Command: '{0}', Param: '{1}'", command, param));

                Processor.ExecuteCommand(this, command, param);

                return;
            }

            Debug.Log("MainGame: Sending Shout: " + message);

            string[] tokens = Processor.GetTokens(message);
            if (tokens.Length > 0)
            {
                foreach (string token in tokens)
                {
                    if (token.Length <= 1)
                    {
                        continue;
                    }

                    if (token[0] == '#')
                    {
                        string animShout = token.Substring(1, token.Length - 1);

                        string animationName = GesticonEngine.Instance.GetGesticonByCommand(animShout); //Processor.InShoutAnimationCommands[token.ToLower()];
                        if (animationName == "")
                        {
                            Debug.Log("no animation key");
                            continue;
                        }

                        bool isLooping = false;
                        if (animationName.Contains("@"))
                        {
                            isLooping = true;
                            int index = animationName.IndexOf("@") + 1;
                            animationName = animationName.Substring(index, animationName.Length - index);
                        }

                        PlayerAnimator.playerAnimation.Animate(animationName, AnimationAction.Play, isLooping ? WrapMode.Loop : WrapMode.Once, 1f, 0);
                        break;
                    }
                }
            }

            // Get @user DM pattern from the text
            string[] addresses = Processor.GetAddressName(message);

            // Send Chat Operation to the Game Server
            Operations.Chat(Game, Game.Avatar.Id, Game.Avatar.Type, addresses, message);

            Application.ExternalCall("shoutPost", message);
        }
    }

    protected void OnInventoryInvoke(bool enable)
    {
        _inventoryVisible = !_inventoryVisible;

        winInventory win = WindowManager.CreateInventoryWindow("inventory", "", Screen.width - 195, Screen.height - 320, 3, 5,
            (d) => { _inventoryVisible = d.IsVisible; });

        if (!enable)
        {
            win.Hide();
        }
        else
        {
            win.Show();
        }
    }

    protected void OnQuestJournalInvoke(bool enable)
    {
        _questJournalVisible = !_questJournalVisible;

        winQuest win = WindowManager.CreateQuestWindow("questWindow", "", Screen.width - 345, Screen.height - 320,
            (d) => { _questJournalVisible = d.IsVisible; });

        if (enable)
        {
            win.Show();
        }
        else
        {
            win.Hide();
        }
    }

    protected void OnLevelChange(string levelName)
    {
        Debug.Log("MainGame: Get Level Info and Try to change Level to: " + levelName);

        winDialog dialog = WindowManager.CreateDialog("dlgItemPortal", PBConstants.APP_TITLE, Lang.Localized("Loading, please wait..."), new string[] { "OK" }, (dlg, choice) =>
        {
            dlg.Hide();
        });

        dialog.TweenDirection = WindowBase.WindowTweenDirection.None;
        dialog.MakeCenter(200, 130);

        Operations.GetLevelInfo(this.Game, levelName);
    }

    protected void OnBannerChange(string banner)
    {
        GetComponent<UIBanner>().background = ResourceManager.Instance.LoadTexture2D(banner);
    }

    protected void OnBannerSetVisibility(bool visible)
    {
        if (GetComponent<UIBanner>() != null)
        {
            GetComponent<UIBanner>().isVisible = visible;
        }
    }

    protected void OnInventoryRemove(GameItem item)
    {
        WindowManager.CreateDialog("dlgInventoryRemove", "Warning!", "Do you wish to delete item " + item.Code, new string[] { "Yes", "No" }, (dlg, choice) =>
        {
            if (choice == 0)
            {
                bool result = InventoryEngine.Instance.RemoveItem(item);
                if (PopBloopSettings.useLogs)
                {
                    Debug.Log("Remove Inventory Item: " + item.Code + " => Result: " + result.ToString());
                }
            }
            dlg.Hide();
        }).MakeCenter(300, 200);
    }

    #endregion

    #endregion
}