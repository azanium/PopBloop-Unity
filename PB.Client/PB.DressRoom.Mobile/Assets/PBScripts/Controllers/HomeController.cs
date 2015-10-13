using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Client;

public class HomeController : UITableViewController<Banners.Banner>
{
    #region MemVars & Props

    public UITable tableView;
    public GameObject itemPrefab;

    #endregion


    #region Mono Methods

    public override void Awake()
    {
    }

    public override void Start()
    {
    }

    public override void OnEnable()
    {
        TabMenuController.SetKamarPasState(TabMenuController.KamarPasState.KamarPas);
        DressRoom.HideCharacter();
    }

    public override void OnAppear()
    {
        base.OnAppear();

        NetworkController.DownloadFromUrl(Banners.GetBannersUrl(0, 0), this.OnBannerListDownloaded);

        StartCoroutine(UpdateTable());
    }

    public override void OnDisable()
    {
    }

    #endregion


    #region Intenal & Public Methods

    private void OnBannerListDownloaded(WWW www)
    {
        Clear();

        string json = www.text;
        Banners banners = Banners.CreateObject(json);

        if (banners != null)
        {
            ReloadData(banners.data);
            ReloadMap(OnReloadMap);
            ReloadImage(OnReloadImage);

            StartCoroutine(UpdateTable());
        }
        else
        {
            Debug.LogError("Banners creation is failed, maybe due to invalid json: " + json);
        }
    }

    private GameObject OnReloadMap(Banners.Banner banner)
    {
        GameObject obj = (GameObject)Instantiate(itemPrefab, Vector3.zero, Quaternion.identity);

        obj.transform.parent = this.tableView.transform;
        obj.transform.localScale = Vector3.one;
        obj.transform.localPosition = new Vector3(0, 0, -1);

        UIButtonMessage action = obj.GetComponentInChildren<UIButtonMessage>();
        if (action != null)
        {
            action.target = this.gameObject;
        }

        var label = obj.GetComponentInChildren<UILabel>();
        var tex = obj.GetComponentInChildren<UITexture>();

        if (tex != null)
        {
            tex.mainTexture = (Texture2D)Resources.Load("Banners/loading");
        }
        if (label != null)
        {
            label.text = banner.name;
        }
        return obj;
    }

    public string OnReloadImage(Banners.Banner data)
    {
        string path = string.IsNullOrEmpty(data.picture) ? "" : Banners.GetBannersImagePath(data.picture);
        Debug.LogWarning("Image : " + path);
        return path;
    }

    public override void RenderData(Banners.Banner data, ImageData image)
    {
        base.RenderData(data, image);

        GameObject obj = GetGameObjectByData(data);
        if (obj != null)
        {
            var tex = obj.GetComponentInChildren<UITexture>();
            if (tex != null)
            {
                tex.mainTexture = image.texture;
            }
        }
    }

    private IEnumerator UpdateTable()
    {
        yield return new WaitForEndOfFrame();

        if (tableView != null)
        {
            tableView.Reposition();
        }

        var parent = NGUITools.FindInParents<UIDraggablePanel>(tableView.gameObject);
        if (parent != null)
        {
            parent.ResetPosition();
        }
    }


    public void OnClick(GameObject sender)
    {
        var banner = this.GetDataByGameObject(sender);
        if (banner == null)
        {
            return;
        }

        Debug.LogWarning(banner.type);

        switch (banner.type)
        {
            case "url":
                Application.OpenURL(banner.dataValue);
                break;
            case "mix":
                UINavigationController.PushController(string.Format("/AvatarPreview?f=character&v={0}", banner.dataValue));
                break;
        }
    }

    #endregion
}
