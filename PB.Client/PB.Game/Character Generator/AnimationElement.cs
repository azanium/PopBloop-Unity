using UnityEngine;

using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Client;

namespace PB.Game
{
    public class AnimationElement : IElement
    {
        #region MemVars & Props

        protected AssetBundleRequest _animationRequest = null;
        protected string animationURL = "";

        private string _animation;
        public string AnimationName
        {
            get { return _animation; }
            set
            {
                if (_animation != value)
                {
                    _animation = value;
                    _animationRequest = null;
                }
            }
        }

        public WWW AnimationWWW
        {
            get { return AssetsManager.DownloadAssetBundle(animationURL); }
        }

        public AnimationClip Clip
        {
            get
            {
                /*AssetBundle bundle = AnimationWWW.assetBundle;
                AnimationClip clip = (AnimationClip)bundle.mainAsset;
                if (clip == null)
                {
                    Debug.LogError("AnimationElement: AnimationClip is null");
                }
                bundle.Unload(false);*/

                return AssetsManager.GetAnimationFromBundle(animationURL);
            }
        }

        #endregion


        #region Ctor
        
        public AnimationElement(string animation)
        {
            animationURL = PopBloopSettings.AnimationsBundleBaseURL + animation;
            AnimationName = animation;
        }

        #endregion


        #region IElement Members

        public void Clear()
        {

        }

        public bool IsLoaded
        {
            get
            {
                if (!AnimationWWW.isDone)
                {
                    return false;
                }

                return true;
            }
        }

        #endregion
    }
}