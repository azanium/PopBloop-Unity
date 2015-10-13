using UnityEngine;
using System.Collections;
using System;
using PB.Common;
using System.Collections.Generic;
using PB.Client;
using ExitGames.Client.Photon;

public class PBLoadingState : PBGameState
{
    #region MemVars & Props

    static public readonly PBGameState Instance = new PBLoadingState();

    protected AudioClip _backgroundSound = null;
    private float _progress = 0;
    private int _counter = 1;
    private bool _isDownload = false;
    //private float loadingProgress = 0;

    enum DownloadStateType
    {
        Level,
        GameData
    }

    private DownloadStateType _downloadState = DownloadStateType.Level;
    //private string _statusText = PBConstants.LOADINGBAR_LOADING;

    #endregion


    #region Ctor

    public PBLoadingState()
        : base(GameStateType.Loading)
    {
    }

    #endregion


    #region Mono Methods

    public override void Update(GameControllerBase mainGame)
    {
        base.Update(mainGame);

        if (_isDownload)
        {
            switch (_downloadState)
            {
                case DownloadStateType.Level:
                    {
                        if (DownloadWorldLevel(mainGame))
                        {
                            _downloadState = DownloadStateType.GameData;
                        }
                    }
                    break;

                case DownloadStateType.GameData:
                    {
                        //_statusText = GameDataLoader.GetCurrentStatusText;
                        //loadingProgress = GameDataLoader.CurrentProgress;
                        MainController.SetLoadingText(GameDataLoader.GetCurrentStatusText);
                        MainController.SetLoadingProgess(GameDataLoader.CurrentProgress);
                        if (GameDataLoader.Download())
                        {
                            _isDownload = false;
                            OnWorldLoaded(mainGame);
                        }
                    }
                    break;

            }
        }
    }

    public override void OnGUI(GameControllerBase mainGame)
    {
        base.OnGUI(mainGame);

        // Display Loading Progress
        //UIProgressBar.Instance.Update(_statusText, this.loadingProgress, PBConstants.LOADINGBAR_FONTSIZE);
    }

    #endregion


    #region Instantiations

    protected void OnInstantiateAudio(GameControllerBase mainGame, UnityEngine.Object obj)
    {
        _backgroundSound = (AudioClip)obj;
        mainGame.SetupBackgroundSound(_backgroundSound);
    }

    protected void OnInstantiateSkybox(GameControllerBase mainGame, UnityEngine.Object obj)
    {
        RenderSettings.skybox = (Material)obj;

        try
        {
            // Setup the fog
            Fog fog = mainGame.Game.WorldData.Level.Fog;
            RenderSettings.fog = fog.active;
            RenderSettings.fogColor = new Color(fog.color.x, fog.color.y, fog.color.z, fog.color.w);
            RenderSettings.fogDensity = fog.density;
            RenderSettings.fogStartDistance = fog.startDistance;
            RenderSettings.fogEndDistance = fog.endDistance;
            RenderSettings.fogMode = (FogMode)Enum.Parse(typeof(FogMode), fog.fogMode);
        }
        catch (Exception ex)
        {
            Debug.LogError("IslandGame.OnInstantiateSkybox.Fog: " + ex.ToString());
        }

    }

    protected void OnInstantiateLightmaps(GameControllerBase mainGame, PB.Common.LightmapInfo lightmap)
    {
        if (lightmap.lightmapsMode != "" && lightmap.lightmapsMode != null && lightmap.lightmaps.Count > 0)
        {
            LightmapsMode mode = (LightmapsMode)Enum.Parse(typeof(LightmapsMode), lightmap.lightmapsMode);
            LightmapSettings.lightmapsMode = mode;

            List<LightmapData> maps = new List<LightmapData>();
            Level level = mainGame.Game.WorldData.Level;
            foreach (LightmapDataInfo lmData in level.Lightmap.lightmaps)
            {
                LightmapData data = new LightmapData();

                string nearKey = string.Format("{0}{1}/{2}.unity3d", PopBloopSettings.LevelAssetsUrl, level.Path, lmData.nearLightmap);
                string farKey = string.Format("{0}{1}/{2}.unity3d", PopBloopSettings.LevelAssetsUrl, level.Path, lmData.farLightmap);
     
                if (lmData.nearLightmap != "" && AssetsManager.Bundles.ContainsKey(nearKey))
                {
                    WWW www = AssetsManager.Bundles[nearKey];

                    if (www.assetBundle != null)
                    {
                        data.lightmapNear = (Texture2D)www.assetBundle.mainAsset;
                    }
                    else
                    {
                        Debug.LogWarning("OnInstantiateLightmaps.NearLightMaps: AssetBundle not found on " + nearKey);
                    }
                }

                // Setup Far Lightmap if there is
                if (lmData.farLightmap != "" && AssetsManager.Bundles.ContainsKey(farKey))
                {
                    WWW www = AssetsManager.Bundles[farKey];

                    if (www.assetBundle != null)
                    {
                        data.lightmapFar = (Texture2D)www.assetBundle.mainAsset;
                    }
                    else
                    {
                        Debug.LogWarning("OnInstantiateLightmaps.FarLightMaps: AssetBundle not found on " + farKey);
                    }
                }

                maps.Add(data);
            }

            if (maps.Count > 0)
            {
                LightmapSettings.lightmaps = maps.ToArray();
            }
        }
    }

    protected void OnInstantiateLevelEntity(GameControllerBase mainGame, UnityEngine.Object obj, Vector3 position, Vector3 rotation, string tag, int lightmapIndex, Vector4 lightmapTilingOffset)
    {
        if (PopBloopSettings.useLogs)
        {
            Debug.Log(string.Format("MainGame: Instantiating object '{0}', Lightmap Index: {1}, LightmapTilingOffset: {2},{3},{4},{5}", obj.name, lightmapIndex, lightmapTilingOffset.x, lightmapTilingOffset.y, lightmapTilingOffset.z, lightmapTilingOffset.w));
        }

        GameObject go = AssetsManager.Instantiate(obj);

        if (go != null)
        {
            go.transform.position = position;
            go.transform.rotation = Quaternion.Euler(rotation);
            go.transform.tag = tag;

            if (PopBloopSettings.useLogs)
            {
                //Debug.Log("Lightmap Object: " + go.name + " => Index: " + lightmapIndex.ToString());
            }

            if (go.renderer != null && mainGame.Game.WorldData.Level.Lightmap.lightmaps.Count > 0)
            {
                go.renderer.lightmapIndex = lightmapIndex;
                go.renderer.lightmapTilingOffset = lightmapTilingOffset;
            }

            if (go.tag == LevelConstants.TagItem)
            {
                ItemBase item = go.GetComponent<ItemBase>();
                if (item != null)
                {
                    item.drawGizmo = false;
                }
            }

            if (go.tag == LevelConstants.TagNPC)
            {
                ItemNPC npc = go.GetComponent<ItemNPC>();
                npc.drawGizmo = false;
            }

            if (go.tag == LevelConstants.TagSpawnPoints)
            {
                PBGameMaster.SpawnPoints.Add(go.transform);
            }

            if (PBGameMaster.Objects.ContainsKey(go.name) == false)
            {
                PBGameMaster.Objects.Add(go.name, go);
            }
        }
        else
        {
            Debug.Log("MainGame: Instantiating " + obj.name + " failed");
        }
    }

    #endregion


    #region World Loading

    protected void OnWorldLoaded(GameControllerBase mainGame)
    {
        //loadingProgress = 0;
        MainController.SetLoadingProgess(0);

        // Setup the Inventories and Quests
        SetupInventoriesAndQuests(mainGame.Game);

        // Enter World only when we are not in the private room
        mainGame.Game.Avatar.EnterWorld();

        PBGameMaster.GameState = GameStateType.EnterWorld;
    }

    private void SetupInventoriesAndQuests(Game game)
    {
        string inv = "";
        foreach (GameItem item in game.Avatar.Inventories.Items)
        {
            inv += item.Name + "=>" + item.Code + ", ";
        }
        Debug.LogWarning(inv);
        InventoryEngine.Instance.SetInventories(game.Avatar.Inventories);
        InventoryEngine.Instance.SetEquipments(game.Avatar.Equipments);

        QuestEngine.Instance.SetQuestJournal(game.Avatar.QuestJournals);
        QuestEngine.Instance.SetQuestActiveJournal(game.Avatar.QuestActiveJournals);
    }

    #endregion


    #region World Download

    public override void OnWorldStartDownload(GameControllerBase mainGame)
    {
        base.OnWorldStartDownload(mainGame);

        Level level = mainGame.Game.WorldData.Level;

        MainController.SetLoadingText(PBConstants.LOADINGBAR_LOADING);

        mainGame.Game.Listener.LogDebug(mainGame.Game, string.Format("Tile ({0}, {1}), Grid ({2}, {3})", level.InterestArea[0], level.InterestArea[1], level.WorldSize[0], level.WorldSize[1]));

        try
        {
            PBGameMaster.GameTime = DateTime.Parse(level.StartDateTime);
        }
        catch (System.Exception ex)
        {
            Debug.LogWarning("Invalid Game Time: " + level.StartDateTime + " => " + ex.ToString());
            PBGameMaster.GameTime = DateTime.Now;
        }

        //loadingProgress = 0;
        MainController.SetLoadingProgess(0);
        MainController.SetLoadingText(PBConstants.LOADINGBAR_LOADING);

        _progress = 0;
        _isDownload = true;
        _downloadState = DownloadStateType.Level;
        //_statusText = PBConstants.LOADINGBAR_LOADING;

        // Register Gesticon Engine to the game data loader
        GesticonEngine.Instance.Register();

        // Register the Inventory Engine into the game data loader
        InventoryEngine.Instance.Register();

        // Register dialog engine into the game data loader
        DialogEngine.Instance.Register();

        // Register our Quest engine into the game data loader
        QuestEngine.Instance.Register();

        // Prepare all the data loaders
        GameDataLoader.Prepare();
    }

    private int GetLevelTotalDownloads(GameControllerBase mainGame)
    {
        Level level = mainGame.Game.WorldData.Level;

        return level == null ? 0 : level.Lightmap.lightmaps.Count + level.Entities.Count + (level.Audio != "" ? 1 : 0) + (level.Skybox != "" ? 1 : 0) - 1;
    }

    private bool DownloadWorldLevel(GameControllerBase mainGame)
    {
        WWW asset = null;
        if (mainGame.Game.WorldData.Level != null)
        {
            Level level = mainGame.Game.WorldData.Level;

            string path = PopBloopSettings.LevelAssetsUrl + level.Path + "/";

            _counter = 0;

            int totalDownloads = GetLevelTotalDownloads(mainGame);

            // Download the skybox
            if (level.Skybox != "")
            {
                _counter++;
                ReportProgress(mainGame, _counter, totalDownloads);

                asset = AssetsManager.DownloadAssetBundleAbsolute(path + level.Skybox);
                if (asset.isDone == false)
                {
                    return false;
                }
                if (asset.error != null)
                {
                    Debug.LogError("LoadWorld: error downloading " + path + level.Skybox + "=> " + asset.error);
                    asset = AssetsManager.RetryDownloadAssetBundleAbsolute(path + level.Skybox);
                    return false;
                }
            }

            // Download the Lightmaps
            if (level.Lightmap.lightmaps.Count > 0)
            {
                foreach (LightmapDataInfo lmData in level.Lightmap.lightmaps)
                {
                    _counter++;
                    ReportProgress(mainGame, _counter, totalDownloads);

                    if (lmData.nearLightmap != "")
                    {
                        string nearLightmap = string.Format("{0}{1}", path, lmData.nearLightmap);

                        asset = AssetsManager.DownloadAssetBundle(nearLightmap);
                        if (asset.isDone == false)
                        {
                            return false;
                        }

                        if (asset.error != null)
                        {
                            Debug.LogError("LoadWorld: error downloading " + nearLightmap + "=> " + asset.error);
                            asset = AssetsManager.RetryDownloadAssetBundleAbsolute(nearLightmap);
                            return false;
                        }

                    }

                    if (lmData.farLightmap != "")
                    {
                        string farLightmap = string.Format("{0}{1}", path, lmData.farLightmap);
                        asset = AssetsManager.DownloadAssetBundle(farLightmap);
                        if (asset.isDone == false)
                        {
                            return false;
                        }

                        if (asset.error != null)
                        {
                            Debug.LogError("LoadWorld: error downloading " + farLightmap + "=> " + asset.error);
                            asset = AssetsManager.RetryDownloadAssetBundleAbsolute(farLightmap);
                            return false;
                        }
                    }
                }
            }

            // Download the audio
            if (level.Audio != "")
            {
                _counter++;
                ReportProgress(mainGame, _counter, totalDownloads);

                asset = AssetsManager.DownloadSoundAbsolute(path + level.Audio);

                if (asset.isDone == false)
                {
                    return false;
                }

                if (asset.error != null)
                {
                    Debug.LogError("LoadWorld: error downloading " + path + level.Skybox + "=> " + asset.error);
                    asset = AssetsManager.RetryDownloadAssetBundleAbsolute(path + level.Audio);

                    return false;
                }
            }

            // First, we download all the entities, don't instantiate it yet
            foreach (Entity entity in level.Entities)
            {
                _counter++;
                ReportProgress(mainGame, _counter, totalDownloads);

                string bundle = path + entity.FilePath;

                // return false if the download is not completed
                asset = AssetsManager.DownloadAssetBundleAbsolute(bundle);

                if (asset.isDone == false)
                {
                    return false;
                }

                if (asset.error != null)
                {
                    Debug.LogError("LoadWorld: error downloading " + path + level.Skybox + "=> " + asset.error);
                    asset = AssetsManager.RetryDownloadAssetBundleAbsolute(bundle);
                    return false;
                }
            }

            ReportProgress(mainGame, totalDownloads, totalDownloads);

            // Instantiate the skybox
            if (level.Skybox != "")
            {
                AssetBundle bundle = AssetsManager.Bundles[path + level.Skybox].assetBundle;
                UnityEngine.Object obj = bundle.mainAsset;
                OnInstantiateSkybox(mainGame, obj);
                bundle.Unload(false);
            }

            // Instantiate the Lightmaps
            if (level.Lightmap.lightmaps.Count > 0)
            {
                OnInstantiateLightmaps(mainGame, level.Lightmap);
            }

            // Insntatiate the audio
            if (level.Audio != "")
            {
                UnityEngine.Object obj = AssetsManager.Sounds[path + level.Audio].GetAudioClip(false);
                OnInstantiateAudio(mainGame, obj);
            }

            // Now, we have all the entities downloaded, instantiate them all
            foreach (Entity entity in level.Entities)
            {
                string bundlePath = path + entity.FilePath;

                AssetBundle bundle = AssetsManager.Bundles[bundlePath].assetBundle;
                UnityEngine.Object obj = bundle.mainAsset;

                // Set the entity position and rotation
                Vector3 pos = new Vector3(entity.Position.x, entity.Position.y, entity.Position.z);
                Vector3 rot = new Vector3(entity.Rotation.x, entity.Rotation.y, entity.Rotation.z);

                Vector4 tilingOffset = new Vector4(entity.LightmapTilingOffset.x, entity.LightmapTilingOffset.y, entity.LightmapTilingOffset.z, entity.LightmapTilingOffset.w);

                OnInstantiateLevelEntity(mainGame, obj, pos, rot, entity.Tag, entity.LightmapIndex, tilingOffset);

                bundle.Unload(false);
            }

            return true;
        }
        else
        {
            mainGame.Game.Listener.LogError(mainGame.Game, "Level " + mainGame.Game.WorldData.Name + "  is invalid");
            mainGame.Game.SetDisconnected(StatusCode.Exception);
        }

        return false;
    }

    private void ReportProgress(GameControllerBase mainGame, int counter, int totalDownloads)
    {
        if (counter > totalDownloads)
        {
            counter = totalDownloads;
        }
        
        float newProgress = (float)counter / (float)totalDownloads;

        // Calculate progress and inform listener about the progress
        
        if (_progress != newProgress)
        {
            //this.loadingProgress = _progress;
            //Debug.Log(string.Format("Counter: {0}, Total: {1}, Progress: {2}", counter, totalDownloads, newProgress));
            MainController.SetLoadingProgess(_progress);
        }
        _progress = newProgress;
    }

    #endregion
}
