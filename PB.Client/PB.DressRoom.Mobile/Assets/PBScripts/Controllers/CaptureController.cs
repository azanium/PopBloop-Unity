using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class CaptureController : UIViewController
{
    #region MemVars & Props

    public GameObject detailPanel;
    
    public GameObject rootPanel;
    public GameObject submitPanel;

    public UILabel detailLabel;
    public UIInput mixNameInput;
    public UITexture previewTexture;

    protected Texture2D activeScreenshot;

    #endregion


    #region Mono Methods

    public override void Start()
    {
        base.Start();
    }

    public override void OnAppear()
    {
        base.OnAppear();

        TabMenuController.ShowNavigator(false);

        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Center);
        DressRoom.ShowCharacter();

        var param = this.controllerParameters;
        if (param.ContainsKey("f"))
        {
            var func = param["f"];

            if (func == "root")
            {
                rootPanel.gameObject.SetActive(true);
                submitPanel.gameObject.SetActive(false);
            }
            else if (func == "submit")
            {
                rootPanel.gameObject.SetActive(false);
                submitPanel.gameObject.SetActive(true);
            }
        }
    }

    public override void OnDissapear()
    {
        base.OnDissapear();

        TabMenuController.ShowNavigator(true);
    }

    #endregion


    #region UIViewController's Methods



    #endregion


    #region Internal Methods


    #endregion


    #region Public Methods

    protected IEnumerator Capture()
    {
        if (activeScreenshot != null)
        {
            Destroy(activeScreenshot);
        }

        rootPanel.gameObject.SetActive(false);

        yield return new WaitForEndOfFrame();

        activeScreenshot = new Texture2D(Screen.width, Screen.height);
        activeScreenshot.ReadPixels(new Rect(0, 0, Screen.width, Screen.height), 0, 0);
        activeScreenshot.Apply();

        previewTexture.mainTexture = activeScreenshot;

        UINavigationController.PushController("/Capture?f=submit");
    }

    #endregion


    #region Events

    public void OnCapture(GameObject sender)
    {
        StartCoroutine(Capture());
    }

    public void OnSubmit(GameObject sender)
    {
        if (activeScreenshot == null)
        {
            return;
        }

        var bytes = activeScreenshot.EncodeToPNG();
        Destroy(activeScreenshot);
        activeScreenshot = null;

        var mixName = mixNameInput.text;
        var config = DressRoom.GetCurrentCharacterConfig();

        var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = profile.GetString(KPConstants.KPEmail);

        Dictionary<string, object> post = new Dictionary<string, object>();
        post.Add("file", bytes);
        post.Add("configuration", config);

        var api = AvatarMix.CreateMixApi(email, mixName);
        Debug.LogWarning("API: " + api);

        NetworkController.DownloadFromUrl(api, post, (www) =>
        {
            UINavigationController.DismissController();
            Debug.Log("Uploaded with response: " + www.text);
        });
    }

    #endregion

}
