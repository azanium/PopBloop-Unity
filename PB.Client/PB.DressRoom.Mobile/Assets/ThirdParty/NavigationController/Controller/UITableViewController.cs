using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class UITableViewController<T> : UIViewController
{
    #region MemVars & Props
    
    public class ImageData
    {
        public T data;
        public string image = "";
        public Texture2D texture;
        public bool isLoaded = false;

        private UITableViewController<T> controller;

        public ImageData(UITableViewController<T> controller, T data)
        {
            this.controller = controller;
            this.data = data;
        }

        public ImageData(UITableViewController<T> controller, T data, string image, Texture2D texture, bool isLoaded)
        {
            this.data = data;
            this.image = image;
            this.texture = texture;
            this.isLoaded = isLoaded;
            this.controller = controller;
        }

        public void OnImageDownloaded(WWW www)
        {
            if (this.controller != null)
            {
                if (www.texture != null)
                {
                    this.texture = www.texture;
                    this.isLoaded = true;
                }
                this.controller.RenderData(data, this);
            }
        }
    }

    public List<T> tableData = new List<T>(); 
    public Dictionary<T, GameObject> mapDataToObj = new Dictionary<T, GameObject>();
    public Dictionary<GameObject, T> mapObjToData = new Dictionary<GameObject, T>();
    public Dictionary<T, ImageData> textureMap = new Dictionary<T, ImageData>();

    private bool isImagesReady = false;

    #endregion


    #region Mono Methods

    public override void Update()
    {
        base.Update();

        if (!isImagesReady)
        {
            bool done = true;
            foreach (var image in textureMap.Values)
            {
                if (image.isLoaded == false)
                {
                    done = false;
                }
            }

            if (done == true)
            {
                isImagesReady = true;
                RefreshTable();
            }
        }
    }

    #endregion


    #region Internal Methods

    #endregion


    #region Public Methods

    public virtual void ReloadData(IEnumerable<T> newData)
    {
        if (newData != null)
        {
            tableData.Clear();

            tableData.AddRange(newData);
        }
        else
        {
            Debug.LogWarning(string.Format("{0}::ReloadData: newData is null", this));
        }
    }

    public virtual void ReloadMap(System.Func<T, GameObject> mapCallback)
    {
        if (tableData == null || mapCallback == null)
        {
            return;
        }

        foreach (T data in tableData)
        {
            GameObject obj = mapCallback(data);

            if (obj != null)
            {
                if (mapDataToObj.ContainsKey(data) == false)
                {
                    mapDataToObj.Add(data, obj);
                }

                if (mapObjToData.ContainsKey(obj) == false)
                {
                    mapObjToData.Add(obj, data);
                }
            }
        }
    }

    public GameObject GetGameObjectByData(T data)
    {
        if (mapDataToObj.ContainsKey(data))
        {
            return mapDataToObj[data];
        }
        return null;
    }

    public T GetDataByGameObject(GameObject obj)
    {
        if (mapObjToData.ContainsKey(obj))
        {
            return mapObjToData[obj];

        }
        return default(T);
    }

    public virtual void ReloadImage(System.Func<T, string> OnLoadImage)
    {
        if (tableData == null || OnLoadImage == null)
        {
            return;
        }
        
        isImagesReady = false;

        foreach (var data in tableData)
        {
            string path = OnLoadImage(data);
            if (string.IsNullOrEmpty(path) == false)
            {
                ImageData image = new ImageData(this, data, path, null, false);
                NetworkController.DownloadImageFromUrl(path, image.OnImageDownloaded);

                if (textureMap.ContainsKey(data) == false)
                {
                    textureMap.Add(data, image);
                }
            }
        }

        RefreshTable();
    }

    public virtual void RefreshTable()
    {

    }

    public ImageData GetImageDataByData(T data)
    {
        if (textureMap.ContainsKey(data))
        {
            return textureMap[data];
        }
        return null;
    }

    public virtual void RenderData(T data, ImageData image)
    {
    }

    public virtual void Clear()
    {
        foreach (var go in mapObjToData.Keys)
        {
            //Debug.LogWarning("destroy : " + go.name);
            Destroy(go);
        }
        mapObjToData.Clear();
        mapDataToObj.Clear();
        tableData.Clear();
        foreach (var imageData in textureMap.Values)
        {
            if (imageData.texture != null)
            {
                Destroy(imageData.texture);
            }
        }
        textureMap.Clear();
    }

    #endregion
}
