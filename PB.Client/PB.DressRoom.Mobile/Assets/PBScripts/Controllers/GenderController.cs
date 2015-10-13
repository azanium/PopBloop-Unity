using UnityEngine;

using System.Collections;
using System.Collections.Generic;

public class GenderController : UIViewController
{
    #region MemVars & Props

    public UIButtonColorRadioSelect maleRadioButton;
    public UIButtonColorRadioSelect femaleRadioButton;
    private Gender currentGender;


    #endregion


    #region Mono Methods

    public override void OnAppear()
    {
        base.OnAppear();

        DressRoom.ShowCharacter();
        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Center);

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);

        NetworkController.DownloadFromUrl(Gender.GetGenderApi(email), OnGetGenderDownloaded);
    }

    public override void OnDissapear()
    {
        base.OnDissapear();

        DressRoom.HideCharacter();
        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Default);
    }

    #endregion


    #region Internal Methods

    private void OnGetGenderDownloaded(WWW www)
    {
        string text = www.text;

        var genderObj = Gender.CreateObject(text);
        if (genderObj != null)
        {
            currentGender = genderObj;
            _playerGender = genderObj.gender;
            
            var gender = genderObj.GetGender();
            switch (gender)
            {
                case Gender.GenderType.Male:
                    maleRadioButton.SetEnabled();
                    break;

                case Gender.GenderType.Female:
                    femaleRadioButton.SetEnabled();
                    break;
            }
        }
    }


    #endregion


    #region Public Methods
    #endregion


    #region Events

    private string _playerGender = "male";

    public void OnGenderMale()
    {
        if (currentGender == null)
        {
            return;
        }

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);
        
        _playerGender = "male";
        NetworkController.DownloadFromUrl(AvatarConfig.GetDefaultAvatarConfigByGenderApi(email, "male"), (www) =>
        {
            var genderSetObj = AvatarConfig.CreateObject(www.text);
            if (genderSetObj != null)
            {
                DressRoom.ChangePlayerCharacter(genderSetObj.configuration);
            }
        });
    }

    public void OnGenderFemale()
    {
        if (currentGender == null)
        {
            return;
        }

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);

        _playerGender = "female";
        NetworkController.DownloadFromUrl(AvatarConfig.GetDefaultAvatarConfigByGenderApi(email, "female"), (www) =>
        {
            var genderSetObj = AvatarConfig.CreateObject(www.text);
            if (genderSetObj != null)
            {
                DressRoom.ChangePlayerCharacter(genderSetObj.configuration);
            }
        });
    }

    public void OnCancel()
    {
        UINavigationController.DismissController();
    }

    public void OnConfirm()
    {
        if (currentGender == null)
        {
            return;
        }

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);
        
        NetworkController.DownloadFromUrl(GenderSet.GetSetGenderApi(email, _playerGender), (www) =>
        {
            UINavigationController.DismissController();
        });

    }

    #endregion
}
