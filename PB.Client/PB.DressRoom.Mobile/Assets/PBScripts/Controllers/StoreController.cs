using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Client;

public class StoreController : UITableViewController<Brands.Brand>
{
    #region MemVars & Props

    public GameObject rootMenu;
    public GameObject[] topMenus;
    
    public UIGrid gridView;
    public GameObject itemPrefab;

    public UILabel labelHeading;
    public UILabel labelDetail;

    private string currentFunc = "";

    #endregion


    #region Mono Methods

    public override void OnEnable()
    {
        DressRoom.HideCharacter();

        TabMenuController.SetKamarPasState(TabMenuController.KamarPasState.MyAvatar);
    }

    public override void OnAppeared()
    {
        base.OnAppeared();

        var param = this.controllerParameters;

        if (param.Count == 0 ||
            !param.ContainsKey("f"))
        {
            return;
        }

        var func = param["f"];

        if (func == "root")
        {
            rootMenu.gameObject.SetActive(true);
            labelDetail.gameObject.SetActive(false);

            Request(func);               
        }
        else if (func == "home")
        {
            rootMenu.gameObject.SetActive(false);
            labelDetail.gameObject.SetActive(true);
        }

        currentFunc = func;
    }

    public override void OnDissapear()
    {
        base.OnDissapear();

        Clear();
    }


    #endregion


    #region Internal Methods

    private void Request(string func)
    {
        Clear();

        Debug.LogWarning("API " + Brands.GetBrandListApi(0, 0));
        NetworkController.DownloadFromUrl(Brands.GetBrandListApi(0, 0), OnBrandDownloadFinished);
    }

    private void OnBrandDownloadFinished(WWW www)
    {
        string json = www.text;

        var brands = Brands.CreateObject(json);
        if (brands != null)
        {
            ReloadData(brands.data);
            ReloadMap(OnReloadMap);
            ReloadImage(OnReloadImage);
        }
        else
        {
            Debug.LogWarning("Store: Brands is invalid with Json: " + json);
        }
    }

    private string OnReloadImage(Brands.Brand data)
    {
        string path = string.IsNullOrEmpty(data.picture) ? "" : string.Format("{0}bundles/preview_images{1}", PopBloopSettings.WebServerUrl, data.picture);

        return path;
    }

    private GameObject OnReloadMap(Brands.Brand item)
    {
        GameObject obj = (GameObject)Instantiate(itemPrefab, Vector3.zero, Quaternion.identity);

        obj.transform.parent = this.gridView.transform;
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
            label.text = item.title;
        }
        return obj;
    }

    private IEnumerator UpdateTable()
    {
        yield return new WaitForEndOfFrame();

        if (gridView != null)
        {
            gridView.Reposition();
            var parent = NGUITools.FindInParents<UIDraggablePanel>(gridView.gameObject);
            if (parent != null)
            {
                parent.ResetPosition();
            }
        }
    }

    #endregion


    #region Public Methods

 
    public override void RenderData(Brands.Brand data, UITableViewController<Brands.Brand>.ImageData image)
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

    #endregion


    #region Menus Events

    public void OnClick(GameObject sender)
    {
        var data = this.GetDataByGameObject(sender);
        if (currentFunc != "root")
        {
            if (labelDetail != null)
            {
                labelDetail.text = data.title;
            }
        }

        Debug.Log(data.action);
        UINavigationController.PushController(data.action);
    }

    public void OnMenuAZClick(object sender)
    {
    }

    public void OnMenuNewestClick(object sender)
    {
    }

    public void OnMenuPopularClick(object sender)
    {
    }

    public void OnMenuAllClick(object sender)
    {
    }

    #endregion

}
