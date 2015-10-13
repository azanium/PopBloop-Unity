using UnityEngine;

using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Client;

namespace PB.Game
{
    public class CharacterFaceElement : CharacterElement
    {
        #region MemVars & Props

        protected Dictionary<CharacterConstants.FacePart, RequestElement> _mapFaceRequestElements = new Dictionary<CharacterConstants.FacePart, RequestElement>();

        #endregion


        #region Ctor

        public CharacterFaceElement(CharacterGenerator generator, string elementName)
            : base(generator, elementName)
        {
        }

        #endregion


        #region Public Methods

        public void ChangeFacePart(CharacterConstants.FacePart part, string materialName)
        {
            if (materialName != null && materialName != "")
            {
                if (_mapFaceRequestElements.ContainsKey(part) == false)
                {
                    RequestElement reqEl = new RequestElement(PopBloopSettings.MaterialsBundleBaseURL, materialName);
                    _mapFaceRequestElements.Add(part, reqEl);
                }
                else
                {
                    _mapFaceRequestElements[part].Reset(materialName);
                }
            }
        }

        public override void Clear()
        {
            base.Clear();

            foreach (RequestElement re in _mapFaceRequestElements.Values)
            {
                re.Clear();
            }
        }

        public override bool IsLoaded
        {
            get
            {
                if (!base.IsLoaded) return false;

                foreach (RequestElement re in _mapFaceRequestElements.Values)
                {
                    if (re.IsLoaded(typeof(Material)) == false)
                    {
                        return false;
                    }
                }

                return true;
            }
        }

        public override SkinnedMeshRenderer GetSkinnedMeshRenderer(bool combineTextureWithSkin)
        {
            SkinnedMeshRenderer renderer = base.GetSkinnedMeshRenderer(combineTextureWithSkin);

            if (renderer != null)
            {
                List<Material> materials = new List<Material>();

                foreach (RequestElement re in _mapFaceRequestElements.Values)
                {
                    materials.Add((Material)re.ElementWWW.assetBundle.mainAsset);//.request.asset);
                }

                Material combinedMaterial = CombineMaterials(materials);

                renderer.materials = new Material[] { combinedMaterial }; 
            }

            return renderer;
        }

        #endregion
    }
}