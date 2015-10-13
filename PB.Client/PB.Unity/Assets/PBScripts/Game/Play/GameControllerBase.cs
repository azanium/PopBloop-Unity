using System;
using System.Collections;
using System.Collections.Generic;

using UnityEngine;

using PB.Client;
using PB.Common;
using PB.Game;
using LitJson;

public class GameControllerBase : MonoBehaviour, IGameListener
{
    #region MemVars & Props

    public GUISkin loadingGUISkin;

    /// <summary>
    /// Loading Banner for 940 pixel wide
    /// </summary>
    public Texture2D loadingBanner940;

    /// <summary>
    /// Loading Banner for 788 pixel wide
    /// </summary>
    public Texture2D loadingBanner788;

    /// <summary>
    /// Set if we want to use TCP to connect to game server, default is not
    /// </summary>
    private bool _useTcp = false;

    /// <summary>
    /// Map from Item to Actor object
    /// </summary>
    private Dictionary<Item, Actor> _actors = new Dictionary<Item, Actor>();

    protected Game _game = null;
    /// <summary>
    /// MMO Game
    /// </summary>
    public Game Game
    {
        get
        {
            return _game;
        }
    }

    protected GameObject _avatarObj;
    /// <summary>
    /// Our Avatar Game Object
    /// </summary>
    public GameObject Avatar
    {
        get
        {
            return _avatarObj;
        }
    }

    /// <summary>
    /// In Game Game Object detector
    /// </summary>
    protected Detector _detector;

    public winLogin loginWindow = null;

    protected GameObject _wayPoint;
    protected PBDefaults _loaderPref;
    protected PBDefaults _inGamePref;

    protected bool _inventoryVisible = false;
    protected bool _questJournalVisible = false;

    #endregion


    #region MonoBehaviour Event

    protected virtual void Start()
    {
        // Initialize the Assets Manager
        AssetsManager.Initialize();

        _wayPoint = (GameObject)AssetsManager.Instantiate(Resources.Load("3D/Waypoint/waypoint"));
        _wayPoint.animation.wrapMode = WrapMode.Loop;
        _wayPoint.animation.Play();
        _wayPoint.renderer.enabled = false;

        // Remove any visible window
        WindowManager.IsVisible = false;

        // Set banner visibility to false
        Messenger<bool>.Broadcast(Messages.BANNER_SETVISIBILITY, false);

        // Load the Preferences Defaults
        _loaderPref = PBDefaults.GetProfile(PBConstants.PROFILE_PREF);
        _inGamePref = PBDefaults.GetProfile(PBConstants.PROFILE_INGAME);

        /*
        // Check if IslandGame is called from Loader scene, otherwise, put warning and switch back to Loader for Preps
        if (_loaderPref.GetInt(PBConstants.PREF_LOADER) != 99)
		{
			Debug.LogWarning("MainGame: Island scene must be loaded from Loader, switching to loader now");
			Application.LoadLevel("Loader");
			return;
		}*/

        // Create Game Object Detector
        _detector = new Detector(this);

        try
        {
            if (Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.WindowsPlayer ||
                Application.platform == RuntimePlatform.OSXEditor || Application.platform == RuntimePlatform.OSXPlayer)
            {
                ConnectToGameServer(null);
            }

            // Call external get_session_id, so web player can get token from the website
            Application.ExternalCall("get_session_id");
        }
        catch (System.Exception ex)
        {
            Debug.Log(ex.ToString());
        }

        PBGameState.ProcessEvent((c) => c.Start(this));
    }

    protected void Update()
    {
        // If we have MMO game, then do update and detect objects
        if (_game != null)
        {
            _game.Update();

            _detector.DetectObjects();
        }

        // Debug purposes only, subject to removal
        if (Input.GetKey("z"))
        {
            Debug.Log("Increasing view distance");
            IncreaseViewDistance();
        }
        else if (Input.GetKey("x"))
        {
            Debug.Log("Decreasing view distance");
            DecreaseViewDistance();
        }

        if (Input.GetKeyDown(KeyCode.M))
        {
            PlayOrPauseBackgroundSound();
        }

        PBGameState.ProcessEvent((c) => c.Update(this));

        if (PBGameMaster.GameState == GameStateType.WorldEntered)
        {
            this.OnRadarUpdate(_game.Avatar.Id, _game.Avatar.Type, _game.Avatar.Position);
        }
    }

    protected void LateUpdate()
    {
        PBGameState.ProcessEvent((c) => c.LateUpdate(this));
    }

    protected void FixedUpdate()
    {
        PBGameState.ProcessEvent((c) => c.FixedUpdate(this));
    }

    protected void OnGUI()
    {
        GUI.skin = loadingGUISkin;

        if (PBGameMaster.GameState == GameStateType.WorldEntered &&
            (Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor))
        {
            if (_game.Settings.IsPrivateRoom == false)
            {
                if (GUI.Button(new Rect(Screen.width - 150, 10, 100, 25), "Private Island"))
                {
                    Messenger<string>.Broadcast(Messages.LEVEL_CHANGE, "PrivateRoom");
                }
            }
            else
            {
                if (GUI.Button(new Rect(Screen.width - 150, 10, 100, 25), "Lobby Island"))
                {
                    Messenger<string>.Broadcast(Messages.LEVEL_CHANGE, "Island1");
                }
            }
        }

        if (Input.GetKeyDown(KeyCode.C) == true)
        {
            StartCoroutine(Capture());
        }

        PBGameState.ProcessEvent((c) => c.OnGUI(this));
    }

    protected IEnumerator Capture()
    {
        yield return new WaitForEndOfFrame();

        // Create a texture the size of the screen, RGB24 format
        var width = Screen.width;
        var height = Screen.height;
        var tex = new Texture2D(width, height, TextureFormat.RGB24, false);
        // Read screen contents into the texture
        tex.ReadPixels(new Rect(0, 0, width, height), 0, 0);
        tex.Apply();

        // Encode texture into PNG
        var bytes = tex.EncodeToPNG();
        Destroy(tex);

        // For testing purposes, also write to a file in the project folder
        //System.IO.File.WriteAllBytes(Application.dataPath + "/../SavedScreen.png", bytes);

        // Create a Web Form
        var form = new WWWForm();

        Debug.Log("Upload capture with session: " + Game.Avatar.Token);

        form.AddField("session_id", Game.Avatar.Token);
        form.AddBinaryData("picture", bytes, "screenshot.png", "image/png");

        WWW www = new WWW(PopBloopSettings.PhotoUploadUrl, form);
        yield return www;

        if (www.error != null)
        {
            Debug.LogError(www.error);
        }
        else
        {
            if (www.text.Contains("ERROR"))
            {
                Debug.LogWarning("Capture failed!");
            }
            else
            {
                // Now we get our latest uploaded filename

                Debug.Log("Get Uploaded capture filename on Server: " + PopBloopSettings.PhotoUploadLatestFilenameUrl);

                www = new WWW(PopBloopSettings.PhotoUploadLatestFilenameUrl);

                yield return www;

                if (www.error != null)
                {
                    Debug.LogError(www.error);
                }
                else
                {
                    string latestFilenameOnServer = www.text;
                    Debug.LogWarning(latestFilenameOnServer);
                }
            }
        }
    }

    public void Clear(bool clearPrefs)
    {
        Debug.Log("MainGame: Clearing Assets");

        StopBackgroundSound();

        // Delete all Loader settings
        if (clearPrefs)
        {
            _loaderPref.Clear();
        }

        PBGameMaster.Clear();

        // For now, do not cache anything when we change level
        AssetsManager.Clear();

        // Clear the Game Data Loaders
        GameDataLoader.Clear();
    }

    public void Clear()
    {
        Clear(true);
    }

    protected void OnDestroy()
    {
        Debug.LogWarning("MainGame: Destroyed");

        // Clear our objects
        Clear(false);
    }

    protected void OnApplicationQuit()
    {
        try
        {
            Debug.Log("MainGame: Quit");

            // Clear our objects
            Clear();

            if (_game != null)
            {
                // Disconnect from the server
                _game.Disconnect();
            }
        }
        catch (System.Exception ex)
        {
            Debug.Log(ex.Message);
        }
    }

    #endregion


    #region IGameListener implementation

    #region Logs

    public bool IsDebugLogEnabled
    {
        get { return false; }
    }

    public void LogDebug(Game game, string message)
    {
        Debug.Log(message);
    }

    public void LogError(Game game, string message)
    {
        Debug.LogError(message);
    }

    public void LogError(Game game, System.Exception exception)
    {
        if (exception != null)
        {
            //Debug.LogError(exception.Message);
        }
    }

    public void LogInfo(Game game, string message)
    {
        Debug.LogError(message);
    }

    #endregion


    #region Connections

    public void ConnectToGameServer(string token)
    {
        // Generate default settings
        PopBloopSettings setting = PopBloopSettings.GetDefaultSettings();

        bool isPrivateRoom = _loaderPref.GetInt(PBConstants.PREF_PRIVATEROOM) == 1;

        // Changed the settings' server address and the worldname 
        setting.ServerAddress = _loaderPref.GetString(PBConstants.PREF_SERVER);
        setting.WorldName = _loaderPref.GetString(PBConstants.PREF_WORLD);
        setting.IsPrivateRoom = isPrivateRoom;

        // Check if we are in Private Room
        if (isPrivateRoom == false)
        {
            Debug.Log("MainGame: Entering World: " + setting.WorldName);
        }
        else
        {
            Debug.Log("MainGame: Entering Private Room");
        }

        // Create our MMO game
        _game = new Game(this, setting, "Player");

        // Set our game instance
        PBGameMaster.Game = _game;

        if (token == null)
        {
            // Set the avatar's token
            _game.Avatar.Token = _loaderPref.GetString(PBConstants.PREF_TOKEN);
        }
        else
        {
            _game.Avatar.Token = token;
        }

        // Create our Photon Peer
        PhotonPeer peer = new PhotonPeer(_game, _useTcp);

        MainController.SetLoadingText(PBConstants.LOADINGBAR_CONNECTING);

        Debug.Log("MainGame: Connecting with IP Address : " + setting.ServerAddress);

        // Connect to our MMO server
        _game.Initialize(peer);

        PBGameMaster.GameState = GameStateType.Connecting;
    }

    public void OnConnect(Game game)
    {
        PBGameMaster.GameState = GameStateType.Connected;

        PBGameState.ProcessEvent((c) => c.OnConnect(this));
    }

    public void OnDisconnect(Game game, ExitGames.Client.Photon.StatusCode returnCode)
    {
        PBGameMaster.GameState = GameStateType.Disconnected;

        PBDefaults profile = PBDefaults.GetProfile(PBConstants.PROFILE_INGAME);
        profile.SetString(PBConstants.PREF_LEVELNAME, game.WorldData.Name);

        PBGameState.ProcessEvent((c) => c.OnDisconnect(this, returnCode));

        //PBGameMaster.Clear();
        //AssetsManager.Clear();
        Clear(false);

        MainController.SwitchScene("Game");
    }

    #endregion


    #region Authentication

    public void OnAuthenticated(Game game, bool isAuth)
    {
        PBGameMaster.GameState = GameStateType.Authenticated;

        PBGameState.ProcessEvent((c) => c.OnAuthenticated(this, isAuth));
    }

    #endregion


    #region World Level

    public void OnGetLevelInfo(Game game, string worldName, int currentCCU, int maxCCU)
    {
        WindowManager.Clear();

        if (currentCCU < maxCCU || maxCCU == 0)
        {
            Debug.Log("Try to changing World to: " + worldName + ", current CCU: " + currentCCU + ", max CCU: " + maxCCU);

            ReloadLevel(worldName, true);
        }
        else if (currentCCU >= maxCCU && maxCCU > 0)
        {
            WindowManager.CreateDialog("levelChangeFailed", PBConstants.APP_TITLE, Lang.Localized("Anda belum dapat masuk!\nPlayer saat ini sudah mencapai maximum ") + maxCCU.ToString() + Lang.Localized(" orang!\nCobalah beberapa saat lagi!"), new string[] { "OK" }, (sender, selection) =>
            {
                sender.Hide();
            });
        }
    }

    public void OnWorldStartDownload(Game game)
    {
        PBGameState.ProcessEvent((c) => c.OnWorldStartDownload(this));
    }

    public void OnWorldEntered(Game game)
    {
        PBGameState.ProcessEvent((c) => c.OnWorldEntered(this));
    }

    #endregion


    #region MMO Items

    public void OnItemAdded(Game game, Item item)
    {
        if (this.Game != null)
        {
            this.CreateActor(game, item);
            Debug.Log("MainGame: Item added: " + item.AvatarName);
        }
    }

    public void OnItemRemoved(Game game, Item item)
    {
        if (_actors.ContainsKey(item))
        {
            Actor actor = _actors[item];
            actor.Destroy();
            _actors.Remove(item);

            Resources.UnloadUnusedAssets();
        }
    }

    public void OnItemSpawned(byte itemType, string itemId)
    {
    }

    public void OnItemAnimate(Game game, Item item, string animation, AnimationAction action, byte animationWrap, float animationSpeed, int layer)
    {
        if (_actors.ContainsKey(item))
        {
            ActorAnimator.Animate(item, animation, action, (WrapMode)animationWrap, animationSpeed, layer);
        }
    }

    public void OnInventoriesReceived(Game game, Inventories inventories)
    {
        PBGameState.ProcessEvent((c) => c.OnInventoriesReceived(this, inventories));
    }

    public void OnEquipmentsReceived(Game game, Equipments equipments)
    {
        PBGameState.ProcessEvent((c) => c.OnEquipmentsReceived(this, equipments));
    }

    public void OnGameItemMoved(Game game, string gameItemId, float[] position, float[] rotation)
    {
        //Debug.LogWarning(string.Format("GameItemMoved: Pos: ({0}, {1}, {2}), Rot: ({3}, {4}, {5}) => {6}", position[0], position[2], position[1], rotation[0], rotation[1], rotation[2], gameItemId));
        GameObject go = GameObject.Find(gameItemId);
        if (go != null)
        {
            ItemBase item = go.GetComponent<ItemBase>();

            if (item != null)
            {
                item.SetPosition(PlayerActor.GetVectorPosition(position), PlayerActor.GetVectorRotation(rotation));
            }
            else
            {
                // If the item is not ItemBase type, then check if it's ItemNPC, since NPC is special case of item
                ItemNPC npc = go.GetComponent<ItemNPC>();

                if (npc != null)
                {
                    npc.SetPosition(PlayerActor.GetVectorPosition(position), PlayerActor.GetVectorRotation(rotation));
                }
            }
        }
    }

    public void OnGameItemAnimate(Game game, string gameItemId, string animation, AnimationAction action, byte animationWrap, float animationSpeed)
    {
        GameObject go = GameObject.Find(gameItemId);
        if (go != null)
        {
            ItemBase item = go.GetComponent<ItemBase>();

            if (item != null)
            {
                item.SetAnimation(animation, action, animationWrap, animationSpeed);
            }
        }
    }

    #endregion


    #region Miscs

    public void ReloadLevel(string worldName, bool disconnect)
    {
        /*if (gameObject.GetComponent<UIBubbleView>() != null)
        {
            gameObject.GetComponent<UIBubbleView>().Clear();
        }*/

        if (worldName == Game.Avatar.Id)
        {
            worldName = "PrivateRoom";
        }

        int isPrivateRoom = worldName == "PrivateRoom" ? 1 : 0;
        _loaderPref.SetInt(PBConstants.PREF_PRIVATEROOM, isPrivateRoom);
        _loaderPref.SetString(PBConstants.PREF_WORLD, worldName);

        if (disconnect)
        {
            _game.Disconnect();
        }

        //Application.LoadLevel("Game");

        MainController.SwitchScene("Game");
    }

    public void OnRadarUpdate(string itemId, byte itemType, float[] position)
    {
        PBGameState.ProcessEvent((c) => c.OnRadarUpdate(this, itemId, itemType, position));
    }

    public void OnReceivedChatMessage(Game game, Item item, string[] group, string message)
    {
        PBGameState.ProcessEvent((c) => c.OnReceivedChatMessage(this, item, group, message));

        string addresses = "";

        bool displayChat = true;

        if (group != null && group.Length > 0)
        {
            displayChat = false;

            foreach (string address in group)
            {
                addresses += address + ", ";

                if (address.ToLower() == Game.Avatar.AvatarName.ToLower())
                {
                    displayChat = true;
                }
            }
        }

        Debug.Log("MainGame: Chat Message : " + message + " => Mentions: " + addresses);

        if (displayChat)
        {
            UIChatBubble chatBubble = GameObject.Find(item.Id).GetComponent<UIChatBubble>();
            if (chatBubble != null)
            {
                chatBubble.ShowMessage(message);

                Messenger<Item, string>.Broadcast(Messages.CHAT_LOG, item, message);
            }
        }
    }

    public void OnCameraAttached(string itemId, byte itemType)
    {
    }

    public void OnCameraDetached()
    {
    }

    #endregion

    #endregion


    #region Game Methods

    public bool ShowBanner
    {
        get
        {
            UIBanner banner = GetComponent<UIBanner>();

            return banner == null ? false : banner.isVisible;
        }
        set
        {
            UIBanner banner = GetComponent<UIBanner>();

            if (banner != null)
            {
                banner.isVisible = value;
            }
        }
    }

    public void SetupBackgroundSound(AudioClip soundClip)
    {
        if (soundClip == null)
        {
            Debug.Log("No Background Audio");
        }
        else
        {
            Debug.Log("Play Background Sound: '" + soundClip.name + "'");

            if (audio != null)
            {
                if (audio.clip != null)
                {
                    UnityEngine.Object.Destroy(audio.clip);
                }
                audio.clip = soundClip;
                audio.loop = true;

                PlayBackgroundSound();
            }
        }
    }

    public void PlayBackgroundSound()
    {
        bool on = _inGamePref.GetBool(PBConstants.INGAME_MUSICONOFF);
        if (audio.clip != null)
        {
            if (on)
            {
                audio.Play();
            }
            else
            {
                audio.Pause();
            }
        }
    }

    public void PlayOrPauseBackgroundSound()
    {
        bool on = _inGamePref.GetBool(PBConstants.INGAME_MUSICONOFF);
        if (audio == null)
        {
            return;
        }

        if (on)
        {
            audio.Pause();
        }
        else
        {
            if (audio.clip != null)
            {
                audio.Play();
            }
        }

        _inGamePref.SetBool(PBConstants.INGAME_MUSICONOFF, !on);
    }

    public void StopBackgroundSound()
    {
        if (audio != null)
        {
            if (audio.isPlaying)
            {
                audio.Stop();
            }
        }
    }

    public void ShowLoginWindow()
    {
        WindowManager.IsVisible = true;

        loginWindow = WindowManager.CreateLoginWindow("Login", (dlg, username, password) =>
        {
            dlg.InfoText = "Logging in...";
            if (_game != null)
            {
                Debug.Log("MainGame: Login with " + username + ":" + password);
                _game.Avatar.Login(username, password);
            }
        });
    }

    public void CreateAvatar(Game game)
    {
        _avatarObj = AssetsManager.Instantiate(Resources.Load("Prefabs/Player"));

        // Initialize the Player's Actor
        PlayerActor playerActor = _avatarObj.GetComponent<PlayerActor>();
        if (playerActor == null)
        {
            Debug.LogError("No PlayerActor script attached to Player's prefab, please attach it!");
            return;
        }

        playerActor.Initialize(game, game.Avatar);

        // Initialize the Actor Animation
        PlayerAnimator actorAnim = playerActor.GetComponent<PlayerAnimator>();
        if (actorAnim == null)
        {
            Debug.LogError("No ActorAnimation script attached on the Foreign Actor prefab");
            return;
        }
        actorAnim.Initialize(game);

        _actors.Add(game.Avatar, playerActor);

        Vector3 spawn = GetSpawnPosition();
        playerActor.MovePlayer(spawn);
    }

    public void CreateActor(Game game, Item item)
    {
        if (item != game.Avatar)
        {
            GameObject newActorObj = AssetsManager.Instantiate(Resources.Load("Prefabs/ForeignActor"));

            // Initialize the Actor
            ForeignActor actor = newActorObj.GetComponent<ForeignActor>();
            if (actor == null)
            {
                Debug.LogError("No ForeignActor script attached on Foreign Actor prefab");
            }
            actor.Initialize(game, item);

            if (_actors.ContainsKey(item) == false)
            {
                _actors.Add(item, actor);
            }
        }
    }

    protected Vector3 GetSpawnPosition()
    {
        var profile = PBDefaults.GetProfile(PBConstants.PROFILE_INGAME);

        // Get the spawn group default, check ItemPortal.OnAction
        var spawnGroup = profile.GetString(PBConstants.PREF_SPAWNGROUP);

        List<GameObject> groups = new List<GameObject>();
        GameObject[] spawns = GameObject.FindGameObjectsWithTag(LevelConstants.TagSpawnPoints);

        // Null or empty spawn group lead to global group, which is any group will do
        if (string.IsNullOrEmpty(spawnGroup) == false)
        {
            if (spawns != null)
            {
                foreach (GameObject obj in spawns)
                {
                    var spawn = obj.GetComponent<ItemSpawn>();
                    if (spawn != null)
                    {
                        if (spawn.groupName == spawnGroup)
                        {
                            groups.Add(obj);
                        }
                    }
                }
            }
        }

        GameObject spawnObject; 
        if (groups.Count > 0)
        {
            int spawnIndex = UnityEngine.Random.Range(0, groups.Count - 1);
            spawnObject = groups[spawnIndex];
        }
        else
        {
            int spawnIndex = UnityEngine.Random.Range(0, spawns.Length - 1);
            spawnObject = spawns[spawnIndex];
        }

        Debug.LogWarning("Found Spawn Object ==> " + spawnObject.name);
        return spawnObject.transform.position;
    }

    protected void DecreaseViewDistance()
    {
        InterestArea cam;
        this.Game.TryGetCamera(0, out cam);
        float[] viewDistance = (float[])cam.ViewDistanceEnter.Clone();
        viewDistance[0] = System.Math.Max(0, viewDistance[0] - (this.Game.WorldData.TileDimensions[0] / 2));
        viewDistance[1] = System.Math.Max(0, viewDistance[1] - (this.Game.WorldData.TileDimensions[1] / 2));
        cam.SetViewDistance(viewDistance);
    }

    protected void IncreaseViewDistance()
    {
        InterestArea cam;
        this.Game.TryGetCamera(0, out cam);
        float[] viewDistance = (float[])cam.ViewDistanceEnter.Clone();
        viewDistance[0] = System.Math.Min(this.Game.WorldData.Width, viewDistance[0] + (this.Game.WorldData.TileDimensions[0] / 2));
        viewDistance[1] = System.Math.Min(this.Game.WorldData.Height, viewDistance[1] + (this.Game.WorldData.TileDimensions[1] / 2));
        Debug.Log("MainGame: ViewDistance[0]: " + viewDistance[0] + ", ViewDistance[1]: " + viewDistance[1]);
        cam.SetViewDistance(viewDistance);
    }

    protected void GetUserId(string token)
    {
        Debug.Log("IslandGame.GetUserId: Token: " + token);

        if (Application.platform == RuntimePlatform.WindowsWebPlayer || Application.platform == RuntimePlatform.OSXWebPlayer)
        {
            ConnectToGameServer(token);
        }
    }

    #endregion
}