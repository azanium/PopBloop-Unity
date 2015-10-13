using UnityEngine;

using System.Collections;
using System.Collections.Generic;

public class BodyTypeController : UIViewController
{
    #region MemVars & Props

    public UIButtonColorRadioSelect skinnyBodyButton;
    public UIButtonColorRadioSelect averageBodyButton;
    public UIButtonColorRadioSelect chubbyBodyButton;
    private Gender currentGender;

    #endregion


    #region Mono Methods

    
    #endregion


    #region UIViewController's Methods

    public override void OnAppeared()
    {
        base.OnAppear();

        DressRoom.ShowCharacter();
        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Center);

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);

        var param = this.controllerParameters;
        if (param.Count == 0 || param.ContainsKey("f") == false)
        {
            return;
        }
        var func = param["f"];

        if (string.IsNullOrEmpty(func) == false)
        {
            if (func == "skinny")
            {
                skinnyBodyButton.SetEnabled();
            }
            else if (func == "average")
            {
                averageBodyButton.SetEnabled();
            }
            else if (func == "chubby")
            {
                chubbyBodyButton.SetEnabled();
            }

            Debug.LogWarning(Gender.GetGenderApi(email));
            NetworkController.DownloadFromUrl(Gender.GetGenderApi(email), OnGetGenderDownloaded, null);
        }
    }

    public override void OnDissapear()
    {
        base.OnDissapear();

        DressRoom.HideCharacter();
        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Default);
    }

    #endregion


    #region Internal Methods

    private string _playerBodyType = "";

    private void OnGetGenderDownloaded(WWW www)
    {
        string text = www.text;
        
        var genderObj = Gender.CreateObject(text);
        if (genderObj != null)
        {
            currentGender = genderObj;

            var btype = genderObj.GetBodyType();
            switch (btype)
            {
                case Gender.BodyType.Thin:
                    skinnyBodyButton.SetEnabled();
                    _playerBodyType = "small";
                    break;

                case Gender.BodyType.Medium:
                    averageBodyButton.SetEnabled();
                    _playerBodyType = "medium";
                    break;

                case Gender.BodyType.Fat:
                    chubbyBodyButton.SetEnabled();
                    _playerBodyType = "big";
                    break;
            }
        }
    }

    #endregion


    #region Public Methods
    #endregion


    #region Events

    public void OnBodySkinny(GameObject sender)
    {
        if (currentGender == null)
        {
            return;
        }

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);

        _playerBodyType = "thin";
        NetworkController.DownloadFromUrl(AvatarConfig.GetDefaultAvatarConfigByBodyTypeApi(email, _playerBodyType), (www) =>
        {
            var genderSetObj = AvatarConfig.CreateObject(www.text);
            if (genderSetObj != null)
            {
                DressRoom.ChangePlayerCharacter(genderSetObj.configuration);
            }
        }, null);
    }

    public void OnBodyAverage(GameObject sender)
    {
        if (currentGender == null)
        {
            return;
        }

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);

        _playerBodyType = "medium";
        NetworkController.DownloadFromUrl(AvatarConfig.GetDefaultAvatarConfigByBodyTypeApi(email, _playerBodyType), (www) =>
        {
            var genderSetObj = AvatarConfig.CreateObject(www.text);
            if (genderSetObj != null)
            {
                DressRoom.ChangePlayerCharacter(genderSetObj.configuration);
            }
        }, null);
    }

    public void OnBodyChubby(GameObject sender)
    {
        if (currentGender == null)
        {
            return;
        }

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);

        _playerBodyType = "fat";
        NetworkController.DownloadFromUrl(AvatarConfig.GetDefaultAvatarConfigByBodyTypeApi(email, _playerBodyType), (www) =>
        {
            var genderSetObj = AvatarConfig.CreateObject(www.text);
            
            if (genderSetObj != null)
            {
                DressRoom.ChangePlayerCharacter(genderSetObj.configuration);
            }
        }, null);
    }

    public void OnConfirm(GameObject sender)
    {
        if (currentGender == null)
        {
            return;
        }

        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = defaultProfile.GetString(KPConstants.KPEmail);

        NetworkController.DownloadFromUrl(GenderSet.GetSetBodyTypeApi(email, _playerBodyType), (www) =>
        {
            UINavigationController.DismissController();
        }, null);
    }

    public void OnCancel(GameObject sender)
    {
        UINavigationController.DismissController();
    }

    #endregion
}
