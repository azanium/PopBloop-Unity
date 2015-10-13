using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Common;
using PB.Client;

public class InventoryStyleCacheOld
{
    #region MemVars & Props

    protected static Dictionary<string, Texture2D> _normalTextureCaches = new Dictionary<string, Texture2D>();
    /*{
        { "energy", (Texture2D)Resources.Load("GUI/Inventory/battery") },
        { "coin", (Texture2D)Resources.Load("GUI/Inventory/coin") },
        { "candy", (Texture2D)Resources.Load("GUI/Inventory/battery") }
    };*/
    protected static Dictionary<string, string> _reverseTextureToKeyCaches = new Dictionary<string, string>();
    protected static Dictionary<string, Texture2D> _hoverTextureCaches = new Dictionary<string, Texture2D>();
    protected static Dictionary<string, GUIStyle> _styleCaches = new Dictionary<string, GUIStyle>();
    protected static Dictionary<string, WWW> _downloads = new Dictionary<string, WWW>();

    protected static string _contentManifest = "";
    public static string ContentManifest { get { return _contentManifest; } }

    #endregion


    #region Methods

    public static Dictionary<string, string> ImportContentManifest(string contentManifest)
    {
        // Do not update our cache if content manifest is the same
        if (_contentManifest == contentManifest)
        {
            return null;
        }

        Dictionary<string, string> contents = new Dictionary<string, string>();
        string[] lines = contentManifest.Split('\n');

        foreach (string line in lines)
        {
            string[] item = line.Split(',', ' ');
            if (item.Length == 2)
            {
                string code = item[0];
                string icon = item[1];
                
                contents.Add(code, icon);
            }
        }

        return contents;
    }

    public static void Clear()
    {
        foreach (string url in _downloads.Keys)
        {
            WWW www = _downloads[url];
            if (www.isDone && www.error == null)
            {
                Object.Destroy(www.texture);
            }
            www.Dispose();
        }

        _downloads.Clear();

        // Force the caches to download all the time
        _normalTextureCaches.Clear();
        
        _reverseTextureToKeyCaches.Clear();
    }

    public static IEnumerator DownloadContents(List<InventoryEngine.InventoryMetadata> contents)
    {
        foreach (InventoryEngine.InventoryMetadata data in contents)
        {
            if (_normalTextureCaches.ContainsKey(data.name))
            {
                continue;
            }
            else
            {
                // We already downloaded the texture?
                if (_reverseTextureToKeyCaches.ContainsKey(data.file))
                {
                    // Yes, do not redownload it
                    string cachedTexName = _reverseTextureToKeyCaches[data.file];
                    _normalTextureCaches[data.file] = _normalTextureCaches[cachedTexName];

                    if (PopBloopSettings.useLogs)
                    {
                        Debug.Log("Use the Inventory Item Icon cached texture: " + data.name + " => " + data.file);
                    }

                    yield return null;
                }
                else
                {

                    string url = PopBloopSettings.InventoryAssetsUrl + data.file + "?tick=" + Time.frameCount;
                    WWW content = new WWW(url);

                    if (_downloads.ContainsKey(url) == false)
                    {
                        _downloads.Add(url, content);
                    }

                    if (PopBloopSettings.useLogs)
                    {
                        Debug.Log("Downloading Inventory Icon: " + url);
                    }

                    yield return content;

                    if (content.error == null && content.isDone)
                    {
                        _normalTextureCaches[data.name] = content.texture;
                        _reverseTextureToKeyCaches[data.file] = data.name;
                    }
                    else
                    {
                        Debug.LogError("InventoryStyleCache: Failed to download inventory icon: " + url);
                    }
                }
            }

        }
    }

    public static GUIStyle GetStyleForItem(string itemCode)
    {
        if (_styleCaches.ContainsKey(itemCode))
        {
            return _styleCaches[itemCode];
        }
        else
        {
            GUIStyle style = new GUIStyle();

            if (_normalTextureCaches.ContainsKey(itemCode))
            {
                style.normal.background = _normalTextureCaches[itemCode];
            }

            if (_hoverTextureCaches.ContainsKey(itemCode))
            {
                style.hover.background = _hoverTextureCaches[itemCode];
            }

            _styleCaches.Add(itemCode, style);

            return style;
        }
    }

    public static void AddTexture(string code, Texture2D texture)
    {
        if (_normalTextureCaches.ContainsKey(code) == false)
        {
            _normalTextureCaches.Add(code, texture);
        }
        else
        {
            _normalTextureCaches[code] = texture;
        }
    }

    #endregion

}
