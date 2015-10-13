using UnityEngine;
using System.Collections;
using PB.Client;

public class MainController : MonoBehaviour
{
    #region MemVars & Props

    static private MainController mainController;

    private delegate void UpdateDelegate();
    private UpdateDelegate updateSceneDelegate;
    private UpdateDelegate updateGuiDelegate;

    private Component sceneController;

    private string currentScene = "";
    private string nextScene = "";

    private float loadingProgress = 0;
    private string loadingText = PBConstants.LOADINGBAR_INITIALIZING;
    private string webSession = "";
    private Texture2D background;
    private bool displayBackground = true;
    private bool loadingFinished = false;

    #endregion

    #region MonoBehavior's Methods

    protected void Awake()
    {
        mainController = this;
        currentScene = "";
        nextScene = "";
    }

    protected void OnDestroy()
    {
        mainController = null;
    }

	protected void Start() 
    {
        SwitchScene("Loader");
        updateSceneDelegate = UpdateSceneCleanup;
        updateGuiDelegate = UpdateGui;
	}
	
	protected void Update() 
    {
        if (updateSceneDelegate != null)
        {
            updateSceneDelegate();
        }
    }

    protected void OnGUI()
    {
        if (updateGuiDelegate != null && displayBackground)
        {
            if (GameSettings.GuiSkin() != null)
            {
                GUI.skin = GameSettings.GuiSkin();
            }
            updateGuiDelegate();
        }
    }

    #endregion


    #region Update Delegates

    private void UpdateGui()
    {
        GUI.DrawTexture(new Rect(0, 0, Screen.width, Screen.height), GameSettings.Background());
        
        UIProgressBar.Instance.Update(loadingText, (int)(this.loadingProgress * 100), PBConstants.LOADINGBAR_FONTSIZE);
    }

    private void UpdateSceneCleanup()
    {
        System.GC.Collect();
        updateSceneDelegate = UpdateSceneLoad;
        if (background == null)
        {
            background = GameSettings.Background();
        }
        displayBackground = true;
        loadingFinished = false;
    }

    private void UpdateSceneLoad()
    {
        sceneController = gameObject.AddComponent(nextScene + "Controller");
        if (sceneController == null)
        {
            Debug.LogWarning("Failed to add component: " + nextScene + "Controller");
        }
        currentScene = nextScene;
        updateSceneDelegate = UpdateSceneLoadingScreen;
        updateGuiDelegate = UpdateGui;
    }

    private void UpdateSceneLoadingScreen()
    {
        if (loadingFinished)//(loadingProgress >= 1)
        {
            displayBackground = false;
            loadingFinished = false;
            updateSceneDelegate = UpdateSceneRun;
            updateGuiDelegate = UpdateGui;
        }
    }

    private void UpdateSceneRun()
    {
        if (currentScene != nextScene)
        {
            updateSceneDelegate = UpdateSceneUnload;
            updateGuiDelegate = UpdateGui;
        }
    }

    private void UpdateSceneUnload()
    {
        if (sceneController != null)
        {
            Destroy(sceneController);
            sceneController = null;
        }
        currentScene = "";
        updateSceneDelegate = UpdateSceneCleanup;
    }

    #endregion


    #region Public Methods

    static public void SetLoadingFinished()
    {
        mainController.loadingFinished = true;
    }

    static public void SetLoadingText(string status)
    {
        mainController.loadingText = status;
    }

    static public void SetLoadingProgess(float progress)
    {
        mainController.loadingProgress = progress;
    }

    static public void SwitchScene(string newScene)
    {
        mainController.currentScene = "";
        mainController.nextScene = newScene;
    }

    static public string WebSession
    {
        get { return mainController.webSession; }
        set { mainController.webSession = value; }
    }

    static public void SetLoaderReady()
    {
        mainController.StartGame();
    }

    public void StartGame()
    {
        SwitchScene("Game");
        updateSceneDelegate = UpdateSceneUnload;
    }

    #endregion


    #region Private Methods

    /// <summary>
    /// Called from the Web
    /// </summary>
    /// <param name="session"></param>
    private void GetUserId(string session)
    {
        webSession = session;
    }

    #endregion
}
