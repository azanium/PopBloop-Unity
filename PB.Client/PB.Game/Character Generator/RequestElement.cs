using UnityEngine;

using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Client;

namespace PB.Game
{

    public class RequestElement
    {
        private string _name;
        public string Name
        {
            get { return _name; }
            set
            {
                if (_name != value)
                {
                    //AssetsManager.RemoveAssetBundle(System.IO.Path.Combine(path, _name));

                    _name = value;
                    request = null;
                }

            }
        }


        public AssetBundleRequest request = null;

        private string path;

        public RequestElement(string urlPath, string elementName)
        {
            path = urlPath;
            Reset(elementName);
        }

        public virtual void Reset(string elementName)
        {
            this.request = null;
            this.Name = elementName;
        }

        public virtual WWW ElementWWW
        {
            get
            {
                //Debug.LogWarning("==>" + this.Name);
                return AssetsManager.DownloadAssetBundle(System.IO.Path.Combine(path, this.Name));
            }
        }

        public virtual void Clear()
        {
            AssetsManager.RemoveAssetBundle(System.IO.Path.Combine(path, this.Name));
        }

        public virtual bool IsLoaded(Type elementType)
        {
            if (ElementWWW.isDone == false) return false;

            if (request == null)
            {
                request = ElementWWW.assetBundle.LoadAsync(this.Name, elementType);
            }

            if (request.isDone == false)
            {
                return false;
            }

            return true;
        }

    }

}