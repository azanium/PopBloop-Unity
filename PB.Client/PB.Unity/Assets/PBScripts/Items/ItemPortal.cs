/// <summary>
/// 
/// Item Pickup Controller
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using System.Collections;
using System.Timers;

using PB.Common;
using PB.Client;
using PB.Game;

[AddComponentMenu("PopBloop Scripts/Items/Portal Item")]
public class ItemPortal : ItemBase
{
    #region MemVars & Props

    public string WorldName = "";
    public string QuestionText = "Do you want to travel?";
    public string YesAnswer = "Yes";
    public string NoAnswer = "No";
    public string SpawnGroupName = "";

    public override ItemBaseType ItemType
    {
        get { return ItemBaseType.Portal; }
    }

    public override ItemActionType ActionType
    {
        get { return ItemActionType.Portal; }
    }

    #endregion


    #region Ctor

    public ItemPortal()
    {
    }

    #endregion


    #region Methods

    protected override void Update()
    {
        base.Update();
    }

    protected override void OnGUI()
    {
        base.OnGUI();
    }

    public override void OnAction(GameControllerBase game)
    {
        base.OnAction(game);

        float currentDistance = CalcDistance(gameObject.transform.position, game.Avatar.transform.position);

        _readyToUse = false;
        string contentText = QuestionText.Replace("{WorldName}", WorldName); 
        if (currentDistance <= Distance)
        {
            if (WorldName != "")
            {
                WindowManager.CreateDialog("dlgPortalLoading", hintDescription, contentText, new string[] { YesAnswer, NoAnswer }, (dlg, choice) =>
                {
                    if (choice == 0)
                    {
                        // Store the spawn group name of this portal, so the game load will pickup once the game is reloaded
                        var profile = PBDefaults.GetProfile(PBConstants.PROFILE_INGAME);
                        profile.SetString(PBConstants.PREF_SPAWNGROUP, this.SpawnGroupName);
                        
                        Messenger<string>.Broadcast(Messages.LEVEL_CHANGE, WorldName);
                    }
                    else
                    {
                        _readyToUse = true;
                    }
                    
                    dlg.Hide();
                }).MakeCenter(300, 200);
            }
        }
    }

    #endregion
}
