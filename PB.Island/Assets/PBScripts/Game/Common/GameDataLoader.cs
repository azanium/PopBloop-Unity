using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class GameDataLoader
{
    #region MemVars & Props

    protected static List<LoaderBase> _loaders = new List<LoaderBase>();

    #endregion


    #region Ctor

    public GameDataLoader()
    {
    }

    #endregion


    #region Methods

    static public void Register(LoaderBase loader)
    {
        if (_loaders.Contains(loader) == false)
        {
            _loaders.Add(loader);
        }
    }

    static public void Clear()
    {
        foreach (LoaderBase loader in _loaders)
        {
            loader.Clear();
        }
        _loaders.Clear();
    }

    static public bool LoaderExists(LoaderBase loader)
    {
        return _loaders.Contains(loader);
    }

    static public void Prepare()
    {
        foreach (LoaderBase loader in _loaders)
        {
            loader.PrepareDownload();
        }
    }

    static public bool Download()
    {
        foreach (LoaderBase loader in _loaders)
        {
            if (loader.IsReady() == false)
            {
                return false;
            }
        }

        return true;
    }

    static public string GetCurrentStatusText
    {
        get
        {
            string status = PBConstants.LOADINGBAR_LOADING;

            foreach (LoaderBase loader in _loaders)
            {
                if (loader.IsReady())
                {
                    continue;
                }
                status = loader.StatusText;
                break;
            }

            return status;
        }
    }

    static public float CurrentProgress
    {
        get
        {
            float progress = 0;
            foreach (LoaderBase loader in _loaders)
            {
                if (loader.IsReady())
                {
                    continue;
                }
                progress = loader.Progress;
                break;
            }

            return progress;
        }
    }
    
    #endregion

}
