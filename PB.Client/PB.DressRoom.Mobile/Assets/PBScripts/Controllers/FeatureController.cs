using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class FeatureController : UITableViewController<AvatarItems.AvatarItem>
{
    #region MemVars & Props

    public UIGrid gridView;
    public GameObject itemPrefab;

    public UILabel labelHeading;
    public UILabel labelDetail;

    protected class RequestData
    {
        public int start = 0;
        public int limit = 0;
        public string title = "";

        public RequestData(string title, int start, int limit)
        {
            this.title = title;
            this.start = start;
            this.limit = limit;
        }
    }

    private Dictionary<string, RequestData> titleMap = new Dictionary<string, RequestData>();

    #endregion


    #region Mono Methods

    public override void Start() 
    {
        if (gridView == null)
        {
            Debug.LogWarning("Need a grid data provider!");
        }
    }

    #endregion


    #region UIViewController Methods

    private void Request(string email, string func, int start, int limit)
    {
        Clear();

        string api = AvatarItems.GetFeaturesApi(email, func, start, limit);
        Debug.LogWarning("API : " + api);

        NetworkController.DownloadFromUrl(api, OnFeaturesDownloaded, null);
    }

    public override void OnAppeared()
    {
        var param = this.controllerParameters;
        
        if (param.Count == 0 || param.ContainsKey("f") == false)
        {
            return;
        }

        string func = param["f"]; // Get the function parameter
                 
        if (string.IsNullOrEmpty(func) == false)
        {
            var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
            var email = profile.GetString(KPConstants.KPEmail);
            
            int start = 0;
            int limit = 0;
            
            if (titleMap.ContainsKey(func))
            {
                var reqData = titleMap[func];
                start = reqData.start;
                limit = reqData.limit;
                labelDetail.text = reqData.title;
            }

            Request(email, func, start, limit);

            if (titleMap.ContainsKey(func) == false)
            {
                titleMap.Add(func, new RequestData(labelDetail.text, 0, 0));
            }
        }
    }

    public override void OnAppear()
    {
        base.OnAppear();

        DressRoom.HideCharacter();
    }

    public override void OnDissapear()
    {
        base.OnDissapear();

        Clear();
    }

    public override void OnDisappeared()
    {
        base.OnDisappeared();
    }

    private void OnFeaturesDownloaded(WWW www)
    {
        var features = AvatarItems.CreateObject(www.text);
        Debug.Log("FeaturesController: JSON: " + www.text);
        if (features != null)
        {
            ReloadData(features.data);
            ReloadMap(OnReloadMap);
            ReloadImage(OnReloadImage);

            RefreshTable();
        }
    }

    private string OnReloadImage(AvatarItems.AvatarItem data)
    {
        string path = string.IsNullOrEmpty(data.picture) ? "" : AvatarItems.GetImagePath(data.picture);

        return path;
    }

    private GameObject OnReloadMap(AvatarItems.AvatarItem item)
    {
        GameObject obj = (GameObject)Instantiate(itemPrefab, Vector3.zero, Quaternion.identity);
        
        obj.transform.parent = this.gridView.transform;
        obj.transform.localScale = Vector3.one;
        //obj.transform.localPosition = new Vector3(0, 0, -1);

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
            label.text = item.title;
        }
        return obj;
    }

    public override void RenderData(AvatarItems.AvatarItem data, UITableViewController<AvatarItems.AvatarItem>.ImageData image)
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

    public override void RefreshTable()
    {
        base.RefreshTable();

        StartCoroutine(UpdateTable());
    }

    private IEnumerator UpdateTable()
    {
        if (gridView != null)
        {
            gridView.Reposition();
        }

        yield return new WaitForEndOfFrame();

        var parent = NGUITools.FindInParents<UIDraggablePanel>(gridView.gameObject);
        if (parent != null)
        {
            parent.ResetPosition();
        }
    }

    private void DisplayLoading()
    {
        GameObject obj = (GameObject)Instantiate(itemPrefab, Vector3.zero, Quaternion.identity);

        obj.transform.parent = this.gridView.transform;
        obj.transform.localScale = Vector3.one;
        obj.transform.localPosition = new Vector3(0, 0, -1);

        var label = obj.GetComponentInChildren<UILabel>();
        var tex = obj.GetComponentInChildren<UITexture>();

        if (tex != null)
        {
            tex.mainTexture = (Texture2D)Resources.Load("Banners/loading");
        }
        if (label != null)
        {
            label.text = "Please wait...";
        }
    }

    #endregion


    #region Events

    public void OnClick(GameObject sender)
    {
        var data = this.GetDataByGameObject(sender);

        if (labelDetail != null)
        {
            labelDetail.text = data.title;
        }

        Debug.Log(data.action);
        UINavigationController.PushController(data.action);

    }

    #endregion
}
