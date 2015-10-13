using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using UnityEngine;

using PB.Client;

/// <summary>
/// AssetBundle Download Manager
/// </summary>
public class AssetsManager
{
    #region MemVars & Props

    private static Dictionary<string, WWW> _textureCache = new Dictionary<string, WWW>();
    public static Dictionary<string, WWW> Textures
    {
        get { return _textureCache; }
    }

    private static Dictionary<string, WWW> _bundleCache = new Dictionary<string, WWW>();
    /// <summary>
    /// Asset Bundles Cache
    /// </summary>
    public static Dictionary<string, WWW> Bundles
    {
        get { return _bundleCache; }
    }

    private static Dictionary<string, WWW> _soundCache = new Dictionary<string, WWW>();
    public static Dictionary<string, WWW> Sounds
    {
        get { return _soundCache; }
    }

    private static List<GameObject> _gameObjects = new List<GameObject>();
    public static List<GameObject> GameObjects
    {
        get { return _gameObjects; }
    }

    private static Dictionary<int, Texture2D> _skinTextures = new Dictionary<int, Texture2D>();
    public static Dictionary<int, Texture2D> SkinColors
    {
        get { return _skinTextures; }
    }

    private static Dictionary<string, AnimationClip> _animationCache = new Dictionary<string, AnimationClip>();
    public static Dictionary<string, AnimationClip> Animations
    {
        get { return _animationCache; }
    }

    private static Dictionary<string, WWW> _stringCache = new Dictionary<string, WWW>();
    public static Dictionary<string, WWW> Strings
    {
        get { return _stringCache; }
    }

    #endregion


    #region Public Static Methods

    public static void Initialize()
    {
        _skinTextures.Clear();
        for (int i = 1; i <= 19; i++)
        {
            Texture2D tex = (Texture2D)Resources.Load("3D/Skins/" + i.ToString());
            _skinTextures.Add(i, tex);
        }
    }

    public static AnimationClip GetAnimationFromBundle(string bundleName)
    {
        string animationPath = SanitizeUrl(bundleName + ".unity3d");

        if (_animationCache.ContainsKey(animationPath))
        {
            return _animationCache[animationPath];
        }
        else
        {
            AnimationClip clip = null;
            try
            {
                WWW www = DownloadAssetBundle(bundleName);
                AssetBundle bundle = www.assetBundle;
                if (bundle == null)
                {
                    Debug.LogError("Bundle for " + bundleName + " is null, and IsDone = " + (www.isDone).ToString()+ " error : '" + www.error+"'");
                }
                clip = (AnimationClip)bundle.mainAsset;
                if (clip == null)
                {
                    Debug.LogError("GetAnimationFromBundle: AnimationClip is null");
                }
                bundle.Unload(false);

                _animationCache.Add(animationPath, clip);
            }
            catch (System.Exception ex)
            {
                Debug.LogWarning("Animation: " + bundleName + " doesnt have a valid assetBundle. => " + ex.ToString());
            }
            return clip;
        }
    }

    /// <summary>
    /// Download a bundle from the server, user must provide http:// address on the bundleName,
    /// Use GameSettings to fetch the http:// address
    /// </summary>
    /// <param name="bundleName">The bundle name without .unity3d</param>
    /// <returns>WWW</returns>
    public static WWW DownloadAssetBundle(string bundleName)
    {
        return DownloadAssetBundleAbsolute(bundleName + ".unity3d");
    }

    /// <summary>
    /// Download a bundle from the server, user must provide http:// address on the bundleName with .unity3d name along with it
    /// </summary>
    /// <param name="bundleName">The bundle name with .unity3d</param>
    /// <returns>WWW</returns>
    public static WWW DownloadAssetBundleAbsolute(string bundleName)
    {
        bundleName = SanitizeUrl(bundleName);

        if (_bundleCache.ContainsKey(bundleName) == false)
        {
            if (PopBloopSettings.useLogs)
            {
                Debug.Log("Downloading " + bundleName);
            }
            try
            {
                _bundleCache.Add(bundleName, WWW.LoadFromCacheOrDownload(bundleName, 1));
            }
            catch (Exception ex)
            {
                Debug.LogError("Exception at: AssetsManager.DownloadAssetBundleAbsolute: \"" + bundleName + "\" => " + ex.ToString());
                Debug.Log("Retry downloading " + bundleName);

                WWW old = _bundleCache[bundleName];
                if (old != null)
                {
                    old.Dispose();
                }

                _bundleCache[bundleName] = new WWW(bundleName);
            }
        }

        return _bundleCache[bundleName];
    }

    public static WWW RetryDownloadAssetBundle(string bundleName)
    {
        return RetryDownloadAssetBundleAbsolute(bundleName + ".unity3d");
    }

    public static WWW RetryDownloadAssetBundleAbsolute(string bundleName)
    {
        bundleName = SanitizeUrl(bundleName);

        if (PopBloopSettings.useLogs)
        {
            Debug.Log("Retry Download " + bundleName);
        }

        if (_bundleCache.ContainsKey(bundleName))
        {
            WWW oldWWW = _bundleCache[bundleName];
            oldWWW.Dispose();

            _bundleCache[bundleName] = new WWW(bundleName);
        }
        else
        {
            _bundleCache.Add(bundleName, new WWW(bundleName));
        }

        return _bundleCache[bundleName];
    }

    /// <summary>
    /// Download a bundle from the server, user must provide http:// address on the bundleName with .unity3d name along with it
    /// </summary>
    /// <param name="bundleName">The bundle name with .unity3d</param>
    /// <returns>WWW</returns>
    public static WWW DownloadString(string url)
    {
        url = SanitizeUrl(url);

        if (_stringCache.ContainsKey(url) == false)
        {
            if (PopBloopSettings.useLogs)
            {
                Debug.Log("Downloading " + url);
            }
            try
            {
                _stringCache.Add(url, new WWW(url));
            }
            catch (Exception ex)
            {
                Debug.LogError("Exception at: AssetsManager.DownloadAssetBundleAbsolute: \"" + url + "\" => " + ex.ToString());
                Debug.Log("Retry downloading " + url);

                WWW old = _stringCache[url];
                if (old != null)
                {
                    old.Dispose();
                }

                _stringCache[url] = new WWW(url);
            }
        }

        return _stringCache[url];
    }

    public static WWW RetryDownloadString(string url)
    {
        url = SanitizeUrl(url);

        if (PopBloopSettings.useLogs)
        {
            Debug.Log("Retry Download " + url);
        }

        if (_stringCache.ContainsKey(url))
        {
            WWW oldWWW = _stringCache[url];
            oldWWW.Dispose();

            _stringCache[url] = new WWW(url);
        }
        else
        {
            _stringCache.Add(url, new WWW(url));
        }

        return _stringCache[url];
    }

    public static WWW DownloadTexture(string bundleName)
    {
        return DownloadTextureAbsolute(bundleName + ".unity3d");
    }

    public static WWW DownloadTextureAbsolute(string bundleName)
    {
        bundleName = SanitizeUrl(bundleName);

        if (_textureCache.ContainsKey(bundleName) == false)
        {
            if (PopBloopSettings.useLogs)
            {
                Debug.Log("Downloading Texture '" + bundleName + "'");
            }

            try
            {
                _textureCache.Add(bundleName, new WWW(bundleName));
            }
            catch (Exception ex)
            {
                Debug.LogError("Exception at: AssetsManager.DownloadTextureAbsolute: \"" + bundleName + "\" => " + ex.ToString());
            }
        }

        return _textureCache[bundleName];
    }

    /// <summary>
    /// Retrying the failed texture download with absolute url name
    /// </summary>
    /// <param name="textureName">texture url</param>
    /// <returns>WWW</returns>
    public static WWW RetryDownloadTextureAbsolute(string textureName)
    {
        textureName = SanitizeUrl(textureName);

        if (PopBloopSettings.useLogs)
        {
            Debug.Log("Retry Download " + textureName);
        }

        if (_textureCache.ContainsKey(textureName))
        {
            WWW oldWWW = _textureCache[textureName];
            oldWWW.Dispose();

            _textureCache[textureName] = new WWW(textureName);
        }
        else
        {
            _textureCache.Add(textureName, new WWW(textureName));
        }
         
        return _textureCache[textureName];
    }

    public static string SanitizeUrl(string url)
    {
        string sanitizedUrl = url.Replace(" ", "%20");
        //sanitizedUrl = url.Replace("@", "_at");
        if (PopBloopSettings.useLocalAssets)
        {
            if (Application.platform == RuntimePlatform.Android)
            {
                Debug.Log("===>" + Application.streamingAssetsPath);
                return "jar:file://" + Application.dataPath + "!/assets/" + sanitizedUrl;
            }
            else
            {
                Debug.Log("sanitize: "+"file://" + Application.dataPath + "/" + sanitizedUrl);
                return "file://" + Application.dataPath + "/" + sanitizedUrl;
            }
        }
        else
        {
            return sanitizedUrl;
        }
    }

    /// <summary>
    /// Remove AssetBundle from the cache
    /// </summary>
    /// <param name="bundleName">Asset Bundle name</param>
    public static void RemoveAssetBundle(string bundleName)
    {
        if (_bundleCache.ContainsKey(bundleName))
        {
            WWW www = _bundleCache[bundleName];
            if (www == null)
            {
                Debug.LogError("AssetsManager.RemoveAssetBundle: WWW for \"" + bundleName + "\" is null");
            }
            else if (www.isDone && www.error == null)
            {
                if (www.assetBundle != null)
                {
                    www.assetBundle.Unload(false);
                }

                www.Dispose();
                www = null;

                _bundleCache.Remove(bundleName);
            }
        }
    }

    public static void RemoveTexture(string bundleName)
    {
        if (_textureCache.ContainsKey(bundleName))
        {
            WWW www = _textureCache[bundleName];
            if (www == null)
            {
            }
            else if (www.isDone && www.error == null)
            {
                if (www.texture != null)
                {
                    UnityEngine.Object.Destroy(www.texture);
                }
                www.Dispose();
                www = null;

                _textureCache.Remove(bundleName);
            }
        }
    }

    #region Sounds

    /// <summary>
    /// Download a sound from the server, user must provide http:// address on the bundleName,
    /// Use GameSettings to fetch the http:// address
    /// </summary>
    /// <param name="bundleName">The bundle name without .unity3d</param>
    /// <returns>WWW</returns>
    public static WWW DownloadSound(string bundleName)
    {
        if (_soundCache.ContainsKey(bundleName) == false)
        {
            if (PopBloopSettings.useLogs)
            {
                Debug.Log("Downloading " + bundleName + ".unity3d");
            }
            try
            {
                _soundCache.Add(bundleName, new WWW(bundleName + ".unity3d"));
            }
            catch (Exception ex)
            {
                Debug.LogError("Exception at: AssetsManager.DownloadAssetBundle: \"" + bundleName + "\" => " + ex.ToString());
            }
        }

        return _soundCache[bundleName];
    }

    /// <summary>
    /// Download a sound from the server, user must provide http:// address on the bundleName with .unity3d name along with it
    /// </summary>
    /// <param name="bundleName">The bundle name with .unity3d</param>
    /// <returns>WWW</returns>
    public static WWW DownloadSoundAbsolute(string bundleName)
    {
        if (_soundCache.ContainsKey(bundleName) == false)
        {
            if (PopBloopSettings.useLogs)
            {
                Debug.Log("Downloading sound " + bundleName);
            }
            try
            {
                _soundCache.Add(bundleName, new WWW(bundleName));
            }
            catch (Exception ex)
            {
                Debug.LogError("Exception at: AssetsManager.DownloadAssetBundleAbsolute: \"" + bundleName + "\" => " + ex.ToString());
            }
        }

        return _soundCache[bundleName];
    }

    #endregion


    /// <summary>
    /// Clear all of the bundles
    /// </summary>
    public static void Clear()
    {
        foreach (GameObject go in _gameObjects)
        {
            GameObject.Destroy(go);
        }

        _gameObjects.Clear();

        foreach (string bundle in _bundleCache.Keys)
        {
            WWW www = _bundleCache[bundle];

            /*if (PopBloopSettings.useLogs)
            {
                Debug.Log("Clearing " + bundle);
            }*/

            if (www == null)
            {
                continue;
            }

            if (www.isDone && www.error == null)
            {
                try
                {
                    if (www.assetBundle != null)
                    {
                        www.assetBundle.Unload(true);
                    }
                }
                catch (Exception ex)
                {
                    if (PopBloopSettings.useLogs)
                    {
                        Debug.LogWarning(string.Format("AssetsManager.Clear: bundle \"{0}\" doesn't have assetBundle, exception: {1}", bundle, ex.ToString()));
                    }
                }

                www.Dispose();
            }
        }
        _bundleCache.Clear();

        foreach (string texture in _textureCache.Keys)
        {
            WWW www = _textureCache[texture];

            if (www == null) continue;

            if (www.isDone && www.error == null)
            {
                if (www.texture != null)
                {
                    UnityEngine.Object.Destroy(www.texture);
                }
                www.Dispose();
            }
        }
        _textureCache.Clear();

        foreach (string sound in _soundCache.Keys)
        {
            WWW www = _soundCache[sound];

            if (www == null) continue;
            if (www.isDone && www.error == null)
            {
                if (www.audioClip != null)
                {
                    UnityEngine.Object.Destroy(www.audioClip);
                }
                www.Dispose();
            }
        }
        _soundCache.Clear();

        foreach (string bundle in _stringCache.Keys)
        {
            WWW www = _stringCache[bundle];

            if (www == null)
            {
                continue;
            }

            if (www.isDone && www.error == null)
            {
                www.Dispose();
                www = null;
            }
        }
        _stringCache.Clear();
    }

    /// <summary>
    /// Instantiate Object and cache it
    /// </summary>
    /// <param name="obj">The object to instantiate</param>
    /// <returns>The Game Object</returns>
    public static GameObject Instantiate(UnityEngine.Object obj)
    {
        GameObject go = (GameObject)GameObject.Instantiate(obj);

        _gameObjects.Add(go);

        return go;
    }

    /// <summary>
    /// Instantiate Object and cache it
    /// </summary>
    /// <param name="obj">The object</param>
    /// <param name="position">Object position</param>
    /// <param name="rotation">Object rotation</param>
    /// <returns>The Game Object</returns>
    public static GameObject Instantiate(UnityEngine.Object obj, Vector3 position, Quaternion rotation)
    {
        GameObject go = (GameObject)GameObject.Instantiate(obj, position, rotation);

        _gameObjects.Add(go);

        return go;
    }

    #endregion
}