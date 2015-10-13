using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class QuizEngine : LoaderBase
{
    #region MemVars & Props

    static private QuizEngine quizEngine;

    protected Dictionary<int, WWW> _quizRequests = new Dictionary<int, WWW>();

    /*protected class QuizData
    {
        public bool IsDownloaded = false;
        public int QuestId = -1;
        public Quiz Quest { get; set; }
        public string Url = "";

        public QuestData(string url)
        {
            Url = url;
        }
    }
    protected Dictionary<int, QuestData> _questDownloads = new Dictionary<int, QuestData>();*/

    public override string StatusText
    {
        get { return PBConstants.LOADINGBAR_GAME_QUESTS; }
    }
    #endregion


    #region MonoBehavior's Methods

    protected void Awake()
    {
        quizEngine = this;
    }

    protected void OnDestroy()
    {
        quizEngine = null;
    }

	protected void Start() 
    {
	}
	
	protected void Update() 
    {
    }

    protected void OnEnable()
    {
    }

    protected void OnDisable()
    {
    }

    #endregion


    #region Public Methods

    public void RegisterQuiz(int quizId)
    {
    }
    
    public override void Initialize(GameControllerBase gameController)
    {
        base.Initialize(gameController);
    }

    public override void PrepareDownload()
    {
        base.PrepareDownload();
    }

    public override bool IsReady()
    {
        bool isReady = true;

        return isReady;
    }

    #endregion


    #region Private Methods

    #endregion
}
