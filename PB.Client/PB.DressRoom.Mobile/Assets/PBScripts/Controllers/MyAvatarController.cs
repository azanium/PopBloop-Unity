using UnityEngine;
using System.Collections;

public class MyAvatarController : UIViewController
{
    #region MemVars & Props

    public UILabel avatarName;
    public UIInput avatarStatusInput;

    #endregion


    #region Mono Methods

    public override void OnAppear()
    {
        base.OnAppear();

        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Default);
    }

    public override void OnAppeared()
    {
        base.OnAppeared();

        var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var avatar = profile.GetString(KPConstants.KPAvatarname);

        NetworkController.DownloadFromUrl(Gender.GetGenderApi(profile.GetString(KPConstants.KPEmail)), (www) =>
        {
            var gender = Gender.CreateObject(www.text);
            if (gender != null)
            {
                var playerId = gender.id;
                var status = gender.status;

                profile.SetString(KPConstants.KPPlayerId, playerId);
                profile.SetString(KPConstants.KPUserStatus, status);

                DressRoom.ShowCharacter();
                DressRoom.ChangePlayerAvatar(playerId);
            }
            else
            {
                Debug.LogError("Invalid Player: " + www.error);
            }
        });
        
        avatarName.text = avatar;

        TabMenuController.ShowNavigator(true);

        TabMenuController.SetKamarPasState(TabMenuController.KamarPasState.MyAvatar);

        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Default);
    }

    public override void OnDissapear()
    {
        base.OnDissapear();

        DressRoom.HideCharacter();
    }


    #endregion


    #region Methods

    public void OnAbout(GameObject obj)
    {
        UIImageButton btn = obj.GetComponent<UIImageButton>();
        if (btn != null)
        {
            btn.isEnabled = false;
        }

        if (avatarStatusInput != null)
        {
            var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
            avatarStatusInput.text = profile.GetString(KPConstants.KPUserStatus);
        }
    }

    public void OnConfirmStatus(GameObject obj)
    {
        var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var oldStatus = profile.GetString(KPConstants.KPUserStatus);
        var newStatus = avatarStatusInput.text;

        Debug.LogWarning("status: '" + newStatus + "'");
        var api = UserStatus.SetStatusApi(profile.GetString(KPConstants.KPPlayerId), newStatus);
        Debug.LogWarning("API: " + api);
        NetworkController.DownloadFromUrl(api, (www) =>
        {
            obj.transform.parent.gameObject.SetActive(false);
            profile.SetString(KPConstants.KPUserStatus, newStatus);
        });
    }

    public void OnCapture(GameObject obj)
    {
        Debug.LogWarning("Captured");
        var fileName = string.Format("KamarPas-{0}.png", System.DateTime.Now.ToString());
        Application.CaptureScreenshot(fileName);
    }

    IEnumerator CaptureScreen()
    {
        yield return new WaitForEndOfFrame();
    }

    public void OnCreateMix(GameObject obj)
    {
        UINavigationController.PushController("/Capture?f=root");
    }

    private void EnablePanels(bool enabled)
    {
        var panels = GetComponentsInChildren<UIPanel>();
        foreach (var p in panels)
        {
            p.enabled = enabled;
        }
    }

    public void OnStore(GameObject obj)
    {
        UINavigationController.PushController("/Store?f=root");
    }

    public void OnFeature(GameObject obj)
    {
        UINavigationController.PushController("/Feature?f=root");
    }

    public void OnStyle(GameObject obj)
    {
        UINavigationController.PushController("/Style?f=root");
    }

    #endregion
}
