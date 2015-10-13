using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class ProfileController : UIViewController
{
    #region MemVars & Props

    public GameObject gotoMyAvatar;
    public UIInput avatarName;
    public UIInput avatarDetails;

    public override bool StackPushable
    {
        get
        {
            return false;
        }
    }

    public override void OnAppear()
    {
        base.OnAppear();        
    }

    #endregion


    #region Mono Methods


    #endregion


    #region Methods

    private void UpdateProfile()
    {
        string avatar = avatarName.text;
        string details = avatarDetails.text;

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);
       
        DressRoom.CallUserApi(DressRoom.UserApiRequest.NewUser, string.Format("{0}/{1}/{2}/{3}/{4}", email, "12345", WWW.EscapeURL(avatar),
            "male", WWW.EscapeURL(details), ""), (dic) =>
            {
                Prime31.Utils.logObject(dic);
                bool isValid = (bool)dic["valid"];
                Debug.Log("UpdateProfile: " + isValid);

                ShowMainPage();
            });
    }

    public void ShowMainPage()
    {
        UINavigationController.PushController(typeof(MyAvatarController));
    }

    #endregion
}
