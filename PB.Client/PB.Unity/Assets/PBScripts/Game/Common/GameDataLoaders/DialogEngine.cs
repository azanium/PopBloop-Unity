using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Common;
using PB.Client;
using System;

public class DialogEngine : LoaderBase
{
    #region MemVars & Props

    public const int DIALOG_OPTION_CHOICE = 0;
    public const int DIALOG_OPTION_QUEST = 1;
    public const int DIALOG_OPTION_QUIZ = 2;

    protected class DialogData
    {
        public bool IsDownloaded = false;
        public string Json = "";
        public DialogStory DialogStory { get; set; }
        public string Url = "";

        public DialogData(string url)
        {
            Url = url;
        }
    }

    static public readonly DialogEngine Instance = new DialogEngine();

    protected GameControllerBase _gameController = null;
    private Dictionary<string, DialogData> _dialogStories = new Dictionary<string, DialogData>();

    public override string StatusText
    {
        get { return PBConstants.LOADINGBAR_GAME_DIALOGS; }
    }

    #endregion


    #region Methods

    public void Initialize(GameControllerBase mainGame)
    {
        _gameController = mainGame;
    }

    public void RegisterDialogStory(string storyName)
    {
        if (_dialogStories.ContainsKey(storyName) == false)
        {
            _dialogStories.Add(storyName, new DialogData(PopBloopSettings.GetDialogStoryURL(storyName) + "/" + Time.frameCount.ToString()));
        }
    }

    public bool IsDialogStoryDownloaded(string storyName)
    {
        if (_dialogStories.ContainsKey(storyName))
        {
            return _dialogStories[storyName].IsDownloaded;
        }

        return true;
    }

    public bool IsDialogStoryValid(string storyName)
    {
        if (_dialogStories.ContainsKey(storyName))
        {
            DialogData dialog = _dialogStories[storyName];
            return dialog.IsDownloaded && dialog.DialogStory != null && dialog.DialogStory.Dialogs.Count > 0;
        }

        return false;
    }

    public DialogStory GetDialogStory(string storyName)
    {
        if (_dialogStories.ContainsKey(storyName))
        {
            return _dialogStories[storyName].DialogStory;
        }

        return null;
    }

    public override void PrepareDownload()
    {
        base.PrepareDownload();
    }

    public override bool IsReady()
    {
        base.IsReady();
        
        WWW asset = null;

        int count = 0;

        foreach (string storyName in _dialogStories.Keys)
        {
            count++;

            // Calculate the progress
            _progress = (float)count / (float)_dialogStories.Count;

            DialogData dialog = _dialogStories[storyName];
            
            if (dialog.IsDownloaded)
            {
                continue;
            }

            string url = dialog.Url;

            asset = AssetsManager.DownloadString(url);
            
            if (asset.isDone == false) 
            {
                return false;
            }

            if (asset.error != null)
            {
                Debug.LogWarning("DialogEngine: Retrying error when downloading " + url + " => " + asset.error);
                asset = AssetsManager.RetryDownloadString(url);
                return false;
            }

            // Mark this dialog data as downloaded
            dialog.IsDownloaded = true;

            string json = asset.text.Trim();

            if (string.IsNullOrEmpty(json) == false)
            {
                dialog.DialogStory = DecodeJsonToDialogStory(storyName, json);

                if (dialog.DialogStory != null)
                {
                    // Get embedded quests and quizes and set them up

                    IEnumerable<int> quests = GetEmbeddedQuests(dialog.DialogStory);

                    foreach (int questId in quests)
                    {
                        QuestEngine.Instance.RegisterQuest(questId);
                    }

                    IEnumerable<int> quizes = GetEmbeddedQuizes(dialog.DialogStory);

                    foreach (int quizId in quizes)
                    {
                    }
                }
            }
        }

        return true;
    }

    private DialogStory DecodeJsonToDialogStory(string storyName, string json)
    {
        DialogStory story = null;
        try
        {
            story = DialogStory.ParseFromJSON(json);
        }
        catch (Exception ex)
        {
            Debug.LogWarning(ex.ToString());
        }

        if (story != null)
        {
            if (story.Dialogs != null)
            {
                if (PopBloopSettings.useLogs && story.Dialogs.Count > 0)
                {
                    Debug.Log(string.Format("Dialog Story '{0}' decoded succesfully, Json: {1}", storyName, json));
                }
            }
            else
            {
                story = null;
                if (PopBloopSettings.useLogs)
                {
                    Debug.LogWarning("Dialog Story's '" + storyName + "' Dialogs is null");
                }
            }
        }
        else
        {
            if (PopBloopSettings.useLogs)
            {
                Debug.LogWarning("Dialog Story '" + storyName + "' JSON Convert Failed: JSON: \"" + json + "\"");
            }
        }

        return story;
    }

    public override void Clear()
    {
        base.Clear();

        _dialogStories.Clear();
    }

    public static IEnumerable<int> GetEmbeddedQuests(DialogStory story)
    {
        List<int> result = new List<int>();
        if (story.Dialogs.Count < 1)
        {
            return result;
        }

        foreach (Dialog dialog in story.Dialogs)
        {
            foreach (DialogOption option in dialog.Options)
            {
                if (option.Tipe == DIALOG_OPTION_QUEST && option.Next > -1)
                {
                    result.Add(option.Next);
                }
            }
        }

        return result;
    }

    public static IEnumerable<int> GetEmbeddedQuizes(DialogStory story)
    {
        List<int> result = new List<int>();

        if (story.Dialogs.Count < 1)
        {
            return result;
        }

        foreach (Dialog dialog in story.Dialogs)
        {
            foreach (DialogOption option in dialog.Options)
            {
                if (option.Tipe == DIALOG_OPTION_QUIZ && option.Next > -1)
                {
                    result.Add(option.Next);
                }
            }
        }

        return result;
    }

    #endregion
}
