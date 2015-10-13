using UnityEngine;

using System.Collections;
using System.Collections.Generic;



public class NetworkController : MonoBehaviour
{
    #region MemVars & Props

    public class WWWData
    {
        public string url;
        public WWW www;
        internal DownloadCallback onFinished;
        internal DownloadCallback onProgress;

        public WWWData(string url, WWW www, DownloadCallback onFinished, DownloadCallback onProgress)
        {
            this.www = www;
            this.url = url;
            this.onFinished = onFinished;
            this.onProgress = onProgress;
        }
    }

    public delegate void DownloadCallback(WWW www);

    private static NetworkController networkController;
    private bool _ready = false;

    private Dictionary<string, WWWData> _downloadList = new Dictionary<string, WWWData>();
    private List<string> _downloadCompleted = new List<string>();

    #endregion


    #region Mono Methods

    protected void Awake()
    {
        networkController = this;
    }

    protected void Start()
    {
        _ready = true;
    }

    protected void Update()
    {
        //ProcessDownloads();
        //CleanupDownloads();
    }

    #endregion


    #region Internal Methods

    private void ProcessDownloads()
    {
        foreach (var url in _downloadList.Keys)
        {
            var data = _downloadList[url];
            
            if (data.www.isDone)
            {
                if (data.onFinished != null)
                {
                    data.onFinished(data.www);
                }
                _downloadCompleted.Add(url);
            }
            else
            {
                if (data.onProgress != null)
                {
                    data.onProgress(data.www);
                }
            }
        }
    }

    private void CleanupDownloads()
    {
        foreach (var completed in _downloadCompleted)
        {
            if (_downloadList.ContainsKey(completed))
            {
                var www = _downloadList[completed];
                www = null;
                _downloadList.Remove(completed);

            }
        }
        _downloadCompleted.Clear();
    }

    private WWWData downloadFromUrl(string path, DownloadCallback onFinished, DownloadCallback onProgress)
    {
        if (_ready == false || onFinished == null)
        {
            return null;
        }

        StartCoroutine(_downloadFromUrl(path, onFinished));

        return null;
    }

    private void downloadFromUrl(string path, Dictionary<string, object> postData, DownloadCallback onFinished)
    {
        if (!_ready || onFinished == null)
        {
            return;
        }

        StartCoroutine(_downloadFromUrlWithPOST(path, postData, onFinished));
    }

    private IEnumerator _downloadFromUrlWithPOST(string path, Dictionary<string, object> postData, DownloadCallback onFinished)
    {
        WWWForm wwwform = new WWWForm();
        
        if (postData != null)
        {
            foreach (var postKey in postData.Keys)
            {
                var postValue = postData[postKey];
                if (postValue is string)
                {
                    wwwform.AddField(postKey, (string)postValue);
                }
                if (postValue is byte[])
                {
                    string filename = Md5Sum("screenshot-" + System.DateTime.Now.ToString());
                    wwwform.AddBinaryData(postKey, (byte[])postValue, filename + ".png");
                }
            }
        }

        WWW www = new WWW(path, wwwform);

        yield return www;

        if (www.error == null)
        {
            if (onFinished != null && www.isDone)
            {
                onFinished(www);
            }
        }
        else
        {
            Debug.LogWarning("NetworkController retrying download with POST: " + path + ". With error: " + www.error);
        }
    }

    private IEnumerator _downloadFromUrl(string path, DownloadCallback callback)
    {
        WWW www = new WWW(path);

        yield return www;

        if (www.error == null)
        {
            if (callback != null && www.isDone)
            {
                callback(www);
            }
        }
        else
        {
            Debug.LogWarning("NetworkController retrying download: " + path + ". With error: " + www.error);
            //StartCoroutine(_downloadFromUrl(path, callback));
        }
    }


    private void downloadImageFromUrl(string path, DownloadCallback onFinished)
    {
        if (_ready == false)
        {
            return;
        }

        string filename = System.IO.Path.GetFileName(path);

        string persistentFile = GetPersistentImagePath(filename);
        string localUrl = string.Format("file:///{0}", persistentFile);

        if (System.IO.File.Exists(persistentFile))
        {
            StartCoroutine(_downloadFromUrl(localUrl, onFinished));
        }
        else
        {
            StartCoroutine(_downloadFromUrl(path, (www) => {
                System.IO.File.WriteAllBytes(persistentFile, www.bytes);
                if (onFinished != null)
                {
                    onFinished(www);
                }
            }));
        }

    }

    private IEnumerator _downloadImageFromUrl(string path, DownloadCallback onFinished)
    {
        
        yield return null;
    }

    #endregion


    #region Public Methods

    public static string Md5Sum(string strToEncrypt)
    {
        System.Text.UTF8Encoding ue = new System.Text.UTF8Encoding();
        byte[] bytes = ue.GetBytes(strToEncrypt);

        // encrypt bytes
        System.Security.Cryptography.MD5CryptoServiceProvider md5 = new System.Security.Cryptography.MD5CryptoServiceProvider();
        byte[] hashBytes = md5.ComputeHash(bytes);

        // Convert the encrypted bytes back to a string (base 16)
        string hashString = "";

        for (int i = 0; i < hashBytes.Length; i++)
        {
            hashString += System.Convert.ToString(hashBytes[i], 16).PadLeft(2, '0');
        }

        return hashString.PadLeft(32, '0');
    }

    public static void DownloadFromUrl(string path, Dictionary<string, object> postData, DownloadCallback onFinished)
    {
        if (networkController != null)
        {
            networkController.downloadFromUrl(path, postData, onFinished);
        }
    }

    public static void DownloadImageFromUrl(string path, DownloadCallback onFinished)
    {
        if (networkController != null)
        {
            networkController.downloadImageFromUrl(path, onFinished);
        }
    }

    public static WWWData DownloadFromUrl(string path, DownloadCallback onFinished, DownloadCallback onProgress)
    {
        if (networkController != null)
        {
            return networkController.downloadFromUrl(path, onFinished, onProgress);
        }

        return null;
    }

    public static WWWData DownloadFromUrl(string path, DownloadCallback onFinished)
    {
        Debug.LogWarning(path);
        if (networkController != null)
        {
            return networkController.downloadFromUrl(path, onFinished, null);
        }

        return null;
    }

    public static string GetPersistentImagePath(string imageFile)
    {
        string dir = string.Format("{0}/images/", Application.persistentDataPath);
        if (System.IO.Directory.Exists(dir) == false)
        {
            System.IO.Directory.CreateDirectory(dir);
        }
        return string.Format("{0}{1}", dir, imageFile);
    }

    #endregion
}
