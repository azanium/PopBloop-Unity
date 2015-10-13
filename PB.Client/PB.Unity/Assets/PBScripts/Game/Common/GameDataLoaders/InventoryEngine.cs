/// <summary>
/// 
/// Inventory System
/// 
/// Suhendra Ahmad
/// 
/// </summary>

using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using System;

using PB.Common;
using PB.Client;
using LitJson;

public class InventoryEngine : LoaderBase
{
    #region Inventory MemVars & Props

    public static readonly InventoryEngine Instance = new InventoryEngine();

    private List<GameItem> _inventories = new List<GameItem>();
    public List<GameItem> Items
    {
        get { return _inventories; }
    }

    #endregion


    #region Equipments MemVars & Props

    private Dictionary<string, int> _equipments = new Dictionary<string, int>();
    public Dictionary<string, int> Equipments
    {
        get { return _equipments; }
    }

    private GameControllerBase _mainGame = null;

    public override string StatusText
    {
        get { return PBConstants.LOADINGBAR_INVENTORIES; }
    }

    protected class InventoryIconData
    {
        public string name { get; set; }
        public string file { get; set; }
    }

    public class InventoryMetadata
    {
        public string name { get; set; }
        public string file { get; set; }
    }

    protected string _inventoryUrl = "";
    protected bool _isInventoryMetadataDownloaded = false;
    protected List<InventoryMetadata> _inventoryMetadatas = new List<InventoryMetadata>();

    protected static Dictionary<string, Texture2D> _normalTextureCaches = new Dictionary<string, Texture2D>();
    /*{
        { "energy", (Texture2D)Resources.Load("GUI/Inventory/battery") },
        { "coin", (Texture2D)Resources.Load("GUI/Inventory/coin") },
        { "candy", (Texture2D)Resources.Load("GUI/Inventory/battery") }
    };*/
    protected static Dictionary<string, string> _reverseTextureToKeyCaches = new Dictionary<string, string>();
    protected static Dictionary<string, Texture2D> _hoverTextureCaches = new Dictionary<string, Texture2D>();
    protected static Dictionary<string, GUIStyle> _styleCaches = new Dictionary<string, GUIStyle>();

    #endregion


    #region Inventory Methods

    public int ItemCount(string itemCode)
    {
        int count = 0;
        foreach (GameItem item in _inventories)
        {
            if (item.Code.ToLower() == itemCode.ToLower())
            {
                count++;
            }
        }

        return count;
    }

    public bool HasItems(IDictionary<string, int> items)
    {
        int count = 0;
        foreach (string item in items.Keys)
        {
            if (HasItems(item, items[item]))
            {
                count++;
            }
        }

        return count == items.Keys.Count;
    }

    public bool HasItems(string itemCode, int itemCount)
    {
        int count = 0;
        foreach (GameItem item in _inventories)
        {
            if (item.Code.ToLower() == itemCode.ToLower())
            {
                count++;
            }
        }

        return count >= itemCount;
    }

    public void StoreItem(string code, string name, float weight, string description)
    {
        GameItem gameItem = new GameItem("", code, name, weight, description);
        _inventories.Add(gameItem);

        SyncItemAdd(gameItem);
    }

    public bool StealItem(string code)
    {
        int index = -1;
        GameItem gameItem = null;
        foreach (GameItem item in _inventories)
        {
            //Debug.LogWarning(string.Format("item.Code: {0} == code: {1}", item.Code.ToLower(), code.ToLower()));
            if (item.Code.ToLower() == code.ToLower())
            {
                index = _inventories.IndexOf(item);
                gameItem = item;
            }
        }

        if (index > -1 && gameItem != null)
        {
            _inventories.RemoveAt(index);
            SyncItemRemove(gameItem);
            //Debug.Log("Removed Item: " + gameItem.Code);
            return true;
        }

        return false;
    }

    public void StealItems(IDictionary<string, int> items)
    {
        foreach (string item in items.Keys)
        {
            for (int index = 0; index < items[item]; index++)
            {
                StealItem(item);
            }
        }
    }

    public bool RemoveItem(GameItem item)
    {
        _inventories.Remove(item);
        SyncItemRemove(item);

        return true;
    }

    /// <summary>
    /// Download Inventories from the server
    /// </summary>
    /// <returns>
    /// true if succesful, false otherwise
    /// </returns>
    public bool FetchInventories()
    {
        return true;
    }

    public void Initialize(GameControllerBase mainGame)
    {
        _mainGame = mainGame;
    }

    public void SetInventories(Inventories inventories)
    {
        if (inventories != null)
        {
            _inventories.Clear();
            _inventories.AddRange(inventories.Items);
        }
    }

    public bool SyncItemAdd(GameItem item)
    {
        if (_mainGame == null)
        {
            return false;
        }

        _mainGame.Game.Avatar.SetInventoryAdd(item.Code, item.Name, item.Weight, item.Description);

        return true;
    }

    public bool SyncItemRemove(GameItem item)
    {
        if (_mainGame == null)
        {
            return false;
        }

        _mainGame.Game.Avatar.SetInventoryRemove(item.Code, item.Id, item.Name, item.Weight, item.Description);

        return true;
    }

    #endregion


    #region Equipments Methods

    public int GetEquipmentCount(Equipments.EquipmentType eqType)
    {
        string key = eqType.ToString().ToLower();

        if (_equipments.ContainsKey(key))
        {
            return _equipments[key];
        }

        return 0;
    }

    public int GetEquipmentCount(string eqType)
    {
        string key = eqType.ToLower();

        if (_equipments.ContainsKey(key))
        {
            return _equipments[key];
        }

        return 0;
    }

    public bool HasEquipments(IDictionary<string, int> equipments)
    {
        int count = 0;
        foreach (string equipment in equipments.Keys)
        {
            if (GetEquipmentCount(equipment) >= equipments[equipment])
            {
                count++;
            }
        }

        return count == equipments.Keys.Count;
    }

    public void StealEquipments(IDictionary<string, int> equipments)
    {
        foreach (string eq in equipments.Keys)
        {
            if (_equipments.ContainsKey(eq))
            {
                int count = Math.Max(0, _equipments[eq] - equipments[eq]);

                _equipments[eq] = count;

                SyncEquipment(eq, count);
            }
        }
    }

    public void StoreEquipment(string eq, int count)
    {
        if (_equipments.ContainsKey(eq))
        {
            _equipments[eq] = count;
        }
        else
        {
            _equipments.Add(eq, count);
        }

        SyncEquipment(eq, count);
    }

    public bool SyncEquipment(string code, int count)
    {
        if (_mainGame == null)
        {
            return false;
        }

        _mainGame.Game.Avatar.UpdateEquipment(code, count);

        return true;
    }

    public void SetEquipments(Equipments equipments)
    {
        if (equipments != null)
        {
            _equipments.Clear();
            foreach (KeyValuePair<string, int> pair in equipments.Items)
            {
                _equipments.Add(pair.Key, pair.Value);
            }
        }
    }

    #endregion


    #region Loader & Style Caches Methods

    public override void PrepareDownload()
    {
        base.PrepareDownload();

        _inventoryUrl = PopBloopSettings.InventoryApiUrl + "/" + Time.frameCount.ToString();
        _isInventoryMetadataDownloaded = false;

        _inventoryMetadatas = new List<InventoryMetadata>();

        Clear();
    }

    public override bool IsReady()
    {
        base.IsReady();
        
        WWW asset = null;

        // Download the meta info text from the server
        if (_isInventoryMetadataDownloaded == false)
        {
            asset = AssetsManager.DownloadString(_inventoryUrl);
            
            if (asset.isDone == false)
            {
                return false;
            }

            if (asset.error != null)
            {
                Debug.LogWarning(string.Format("InventoryEngine: Retrying error when downloading url: {0}, Message: {1}", _inventoryUrl, asset.error));
                asset = AssetsManager.RetryDownloadString(_inventoryUrl);
                return false;
            }

            // No more downloading the meta infos
            _isInventoryMetadataDownloaded = true;

            _inventoryMetadatas = JsonMapper.ToObject<List<InventoryMetadata>>(asset.text.Trim());
            foreach (InventoryMetadata meta in _inventoryMetadatas)
            {
                string url = string.Format("{0}{1}?tick={2}", PopBloopSettings.InventoryAssetsUrl, meta.file, Time.frameCount);
                meta.file = url;
            }
        }

        int count = 0;

        // Now download the inventory icons
        foreach (InventoryMetadata data in _inventoryMetadatas)
        {
            count++;

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
                        //Debug.Log("Using the Inventory Item Icon cached texture: " + data.name + " => " + data.file);
                    }

                    continue;
                }
                else
                {
                    asset = AssetsManager.DownloadTexture(data.file);

                    if (asset.isDone == false)
                    {
                        return false;
                    }

                    if (asset.error != null)
                    {
                        Debug.LogWarning(string.Format("InventoryEngine: Retrying error when downloading url: {0}, Message: {1}", data.file, asset.error));
                        asset = AssetsManager.RetryDownloadTextureAbsolute(_inventoryUrl);
                        return false;
                    }

                    if (asset.texture != null)
                    {
                        //if (PopBloopSettings.useLogs)
                        {
                           // Debug.LogWarning(string.Format("InventoryEngine: Succesfully downloaded texture: {0}, file: {1}", data.name, data.file));
                        }
                        _normalTextureCaches[data.name] = asset.texture;
                        _reverseTextureToKeyCaches[data.file] = data.name;
                    }
                }
            }

            // Calculate the progress
            _progress = (float)count / (float)_inventoryMetadatas.Count;
        }
        
        return true;
    }

    public override void Clear()
    {
        base.Clear();

        // Force the caches to download all the time
        //_normalTextureCaches.Clear();

        //_reverseTextureToKeyCaches.Clear();
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
