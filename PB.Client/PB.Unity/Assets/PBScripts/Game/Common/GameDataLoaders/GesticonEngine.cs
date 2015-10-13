using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Client;
using LitJson;

public class GesticonEngine : LoaderBase
{
    #region MemVars & Props

    static public readonly GesticonEngine Instance = new GesticonEngine();

    public override string StatusText
    {
        get
        {
            return PBConstants.LOADINGBAR_GAME_GESTICONS;
        }
    }

    private string _url = string.Format("{0}?dc={1}", PopBloopSettings.GesticonUrl, Time.frameCount);
    private string _json = "[]";

    /// <summary>
    /// Animation that can be triggered on Shout
    /// </summary>
    private Dictionary<string, string> _gesticons = new Dictionary<string, string>()
    {
        { "bye", "bye" },
        { "happy", "happy" },
        { "sit", "@sitground" },
        { "iwak", "@iwak_peyek" },
        { "dance", "@dance" },
        { "swim", "@swim" },
        { "swimidle", "@swimidle" },
        { "gangnam", "@gangnam" },
        { "victory", "victory" },
        { "you", "you" },
        { "metal", "metal" },
        { "handsome", "handsome" },
        { "pretty", "pretty" },
        { "chibi", "chibi" },
    };

    #endregion


    #region Methods

    public string GetGesticonByCommand(string command)
    {
        if (_gesticons.ContainsKey(command))
        {
            return _gesticons[command];
        }

        return "";
    }

    public override void PrepareDownload()
    {
        base.PrepareDownload();

        _json = "[]";
    }

    public override bool IsReady()
    {
        base.IsReady();

        _progress = 0;

        WWW asset = AssetsManager.DownloadString(_url);

        if (asset.isDone == false)
        {
            return false;
        }

        if (asset.error != null)
        {
            Debug.LogWarning("GesticonEngine: Retrying error when downloading " + _url + " => " + asset.error);
            asset = AssetsManager.RetryDownloadString(_url);
            return false;
        }

        string gestJson = asset.text.Trim();
        if (_json != gestJson)
        {
            _json = gestJson;
            List<Dictionary<string, string>> result = null;
            try
            {
                result = JsonMapper.ToObject<List<Dictionary<string, string>>>(_json);
            }
            catch (System.Exception ex)
            {
                Debug.LogWarning(string.Format("Failed to convert Gesticon data => {0}", ex.ToString()));
            }

            if (result != null)
            {
                //_gesticons.Clear();
                foreach (Dictionary<string, string> map in result)
                {
                    string command = map["command"];
                    if (_gesticons.ContainsKey(command) == false)
                    {
                        _gesticons.Add(command, map["animation"]);
                    }
                }
            }
        }

        _progress = 1;

        return true;
    }

    public override void Clear()
    {
        base.Clear();
    }

    #endregion
}
