using UnityEngine;
using System.Collections;

public class AvatarPreviewController : UIViewController
{
    #region MemVars & Props

    public enum PreviewType
    {
        SkinColor = 0,
        Character,
        Element
    }

    public GameObject backToController;
    public UILabel labelHeading;
    public UILabel labelDetail;
    public GameObject detailPanel;
    public UILabel labelDetailInfo;

    public UIImageButton likeButton;
    public UIImageButton commentButton;

    public PreviewType currentPreview = PreviewType.SkinColor;

    protected bool isSaved = false;
    protected Like currentLike = null;
    protected string currentId = "";
     
    #endregion


    #region Mono Methods

    public override void OnDissapear()
    {
        base.OnDissapear();

        DressRoom.HideCharacter();
        DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Default);

        if (!isSaved)
        {
            DressRoom.UndoPlayer();
        }
    }

    public override void OnAppear()
    {
        var param = this.controllerParameters;

        if (param.Keys.Count == 0 || 
            param.ContainsKey("f") == false || 
            param.ContainsKey("v") == false)
        {
            return;
        }

        NGUITools.SetActive(likeButton.gameObject, true);
        NGUITools.SetActive(commentButton.gameObject, true);

        string func = param["f"]; // Get the function parameter
        string val = param["v"];
        string id = param.ContainsKey("id") ? param["id"] : "";
        currentId = id;
        
        var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = profile.GetString(KPConstants.KPEmail);

        if (string.IsNullOrEmpty(func) == false && string.IsNullOrEmpty(val) == false)
        {
            Debug.LogWarning("Func: " + func);
            if (func.ToLower() == "color")
            {
                NGUITools.SetActive(likeButton.gameObject, false);
                NGUITools.SetActive(commentButton.gameObject, false);

                currentPreview = PreviewType.SkinColor;

                DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Center);
                
                int index = int.Parse(val);
                DressRoom.ChangePlayerSkin(index);
            }
            else if (func.ToLower() == "character")
            {
                currentPreview = PreviewType.Character;

                DressRoom.ChangePlayerCharacter(val);
                DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Center);

                NetworkController.DownloadFromUrl(Like.CheckLikeMixApi(email, id), OnDownloadFinished);
                NetworkController.DownloadFromUrl(CommentsCount.GetCountCommentsAvatarMixApi(id), OnCommentsDownloadFinished);
            }
            else
            {
                NetworkController.DownloadFromUrl(Like.CheckLikeAvatarApi(email, id), OnDownloadFinished);
                NetworkController.DownloadFromUrl(CommentsCount.GetCountCommentsAvatarItemApi(id), OnCommentsDownloadFinished);

                currentPreview = PreviewType.Element;

                DressRoom.ChangePlayerElement(val);
                Debug.Log("Type: " + func + ", Value: " + val);

                if (func == "face_part_lip" || func == "face_part_eye_brows" || func == "face_part_eyes" || func == "hair")
                {
                    DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Head);
                }
                if (func == "body")
                {
                    DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Center);
                }
                if (func == "hat")
                {
                    DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Head);
                }
                if (func == "pants" || func == "shoes")
                {
                    DressRoom.CameraDolly(CameraShifter.ZoomTargetArea.Foot);
                }
            }
        }
    }

    private void OnDownloadFinished(WWW www)
    {
        string json = www.text;
        Debug.LogWarning("Mix JSON: " + json);
        var mix = Like.CreateObject(json);
        if (mix != null)
        {
            currentLike = mix;
            likeButton.SetSprite(!mix.isLike);
            var likeLabel = likeButton.GetComponentInChildren<UILabel>();
            if (likeLabel != null)
            {
                likeLabel.text = mix.likeCount.ToString();
            }
        }
    }

    private void OnCommentsDownloadFinished(WWW www)
    {
        string json = www.text;
        Debug.LogWarning("Comments Count JSON: " + json);
        var comCount = CommentsCount.CreateObject(json);
        if (comCount != null)
        {
            var commentLabel = commentButton.GetComponentInChildren<UILabel>();
            if (commentLabel != null)
            {
                commentLabel.text = comCount.count.ToString();
            }
        }
    }

    public override void OnAppeared()
    {
        base.OnAppeared();

        DressRoom.ShowCharacter();
    }

    public void SetHeading(string text)
    {
        if (labelHeading != null)
        {
            labelHeading.text = text;
        }
    }

    public void SetDetail(string text)
    {
        if (labelDetail != null)
        {
            labelDetail.text = text;
        }
    }

    public void ShowDetailInfo(bool state, string text)
    {
        if (detailPanel != null)
        {
            NGUITools.SetActive(detailPanel, state);

            if (labelDetailInfo != null)
            {
                labelDetailInfo.text = text;
            }
        }
    }

    #endregion


    #region Internal & Public Methods


    #endregion


    #region Messages

    private int loveCount = 0;
    private IEnumerator ShowLoveInfo()
    {
        ShowDetailInfo(true, "Sweet! You loved this.");
        yield return new WaitForSeconds(3);
        ShowDetailInfo(false, "");
    }

    public void OnLove(GameObject sender)
    {
        var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = profile.GetString(KPConstants.KPEmail);

        StartCoroutine(ShowLoveInfo());

        switch (currentPreview)
        {
            case PreviewType.Character:
                if (currentLike != null)
                {
                    sender.GetComponent<UIImageButton>().SetSprite(currentLike.isLike);
                    NetworkController.DownloadFromUrl(Like.LikeMixApi(email, currentId), OnDownloadFinished);
                }
                break;

            case PreviewType.Element:
                if (currentLike != null)
                {
                    sender.GetComponent<UIImageButton>().SetSprite(currentLike.isLike);
                    NetworkController.DownloadFromUrl(Like.LikeAvatarApi(email, currentId), OnDownloadFinished);
                }
                break;
        }

        var label = sender.GetComponentInChildren<UILabel>();
        
        if (label != null)
        {
                
            label.text = (++loveCount).ToString();
        }
    }

    public void OnComment(GameObject sender)
    {
        isSaved = true;
        string commentType = "character";
        if (currentPreview == PreviewType.Character)
        {
            commentType = "character";
        }
        else if (currentPreview == PreviewType.Element)
        {
            commentType = "element";
        }
        UINavigationController.PushController("/Comments?f="+ commentType + "&v=" +currentId);
    }

    private IEnumerator ShowCollectInfo()
    {
        ShowDetailInfo(true, "Yay! You've collected this item.\nYou can check it out later on your collection.");
        yield return new WaitForSeconds(3);
        ShowDetailInfo(false, "");
    }

    public void OnCollect(GameObject sender)
    {
        StartCoroutine(ShowCollectInfo());
    }

    public void OnWeb(GameObject sender)
    {
    }

    public void OnConfirm(GameObject sender)
    {
        var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var email = profile.GetString(KPConstants.KPEmail);

        isSaved = true;
        var config = DressRoom.GetCurrentCharacterConfig();

        if (config != "")
        {
            string url = AvatarConfig.SetAvatarConfigApi(email);
            NetworkController.DownloadFromUrl(url, new System.Collections.Generic.Dictionary<string, object>() { { "configuration", config } }, (www) =>
            {
                UINavigationController.DismissController();
            });
        }
    }

    public void OnCancel(GameObject sender)
    {
        isSaved = false;
        UINavigationController.DismissController();
    }

    #endregion
}
