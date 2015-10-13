using UnityEngine;
using System.Collections;
using PB.Client;

[RequireComponent(typeof(ForeignActor))]
public class ForeignActorAnimator : ActorAnimator
{
    #region MemVars & Props

    protected ForeignActor _foreignActor;

    #endregion


    #region Mono Methods

    protected override void Start()
    {
        base.Start();

        _foreignActor = GetComponent<ForeignActor>();
        if (_foreignActor == null)
        {
            Debug.LogError("ForeignActorAnimator must have ForeignActor component attached");
        }
    }

    protected override void Update()
    {
        base.Update();

        if (_foreignActor != null)
        {
            Item item = _foreignActor.Item;
            if (item != null)
            {
                Animate(item.Animation, item.AnimationAction, (WrapMode)item.AnimationWrap, item.AnimationSpeed, 0);
            }
        }
    }
    
    #endregion


    #region Custom Methods

    #endregion
}
