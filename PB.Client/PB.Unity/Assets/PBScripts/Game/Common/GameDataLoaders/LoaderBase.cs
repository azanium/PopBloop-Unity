using UnityEngine;
using System.Collections;

public class LoaderBase
{
    #region MemVars & Props

    public virtual string StatusText
    {
        get { return PBConstants.LOADINGBAR_LOADING; }
    }

    protected float _progress = 0;
    public float Progress
    {
        get { return _progress; }
    }

    protected bool _isRetrying = false;
    public bool IsRetrying
    {
        get { return _isRetrying; }
    }

    protected int _count = 0;
    public int Count
    {
        get { return _count; }
    }

    public GameControllerBase GameController;

    #endregion


    #region Methods

    public virtual void Initialize(GameControllerBase gameController)
    {
        this.GameController = gameController;
    }

    public virtual void PrepareDownload()
    {
        _progress = 0;
        _isRetrying = false;
        _count = 0;
    }

    public virtual bool IsReady()
    {
        return true;
    }

    public void Register()
    {
        GameDataLoader.Register(this);
    }

    public virtual void Clear()
    {
    }

    #endregion
}
