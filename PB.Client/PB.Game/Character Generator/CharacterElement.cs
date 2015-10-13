using UnityEngine;

using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Client;

namespace PB.Game
{

    /// <summary>
    /// Character Element that used to build a character
    /// </summary>
    public class CharacterElement
    {
        #region MemVars & Props

        protected CharacterGenerator characterGenerator;

        private string _materialName;
        /// <summary> 
        /// Material Asset Bundle Name
        /// </summary>
        public string MaterialName
        {
            get
            {
                return _materialName;
            }
            set
            {
                _useMaterial = false;

                if (value != null && value != "")
                {
                    //AssetsManager.RemoveAssetBundle(PopBloopSettings.MaterialsBundleBaseURL + _materialName);

                    _materialName = value;
                    _materialRequest = null;
                    _useMaterial = true;
                }
            }
        }

        protected bool _bEnabled = false;
        private string _elementName;
        /// <summary>
        /// Element AssetBundle Name
        /// </summary>
        public string ElementName
        {
            get
            {
                return _elementName;
            }
            set
            {
                _bEnabled = false;
                if (value != null && value != "")
                {
                    //AssetsManager.RemoveAssetBundle(PopBloopSettings.CharactersBundleBaseURL + _elementName);

                    _elementName = value;
                    _meshRequest = null;
                    _boneNamesRequest = null;
                    _bEnabled = true;
                }
            }
        }

        protected bool _useMaterial;
        protected AssetBundleRequest _meshRequest;
        protected AssetBundleRequest _materialRequest;
        protected AssetBundleRequest _boneNamesRequest;

        #endregion


        #region Ctor

        public CharacterElement(CharacterGenerator generator, string elementName)
        {
            ElementName = elementName;
            characterGenerator = generator;
        }

        public CharacterElement(CharacterGenerator generator, string elementName, string materialName)
        {
            ElementName = elementName;
            MaterialName = materialName;
            characterGenerator = generator;

            if (PopBloopSettings.useLogs)
            {
                //Debug.Log("CharacterElement: Element: '" + ElementName + "', Material: " + materialName);
            }
        }

        #endregion


        #region Public Methods

        /// <summary>
        /// Clear Cached AssetBundles
        /// </summary>
        public virtual void Clear()
        {
            AssetsManager.RemoveAssetBundle(PopBloopSettings.CharactersBundleBaseURL + ElementName);
            AssetsManager.RemoveAssetBundle(PopBloopSettings.MaterialsBundleBaseURL + MaterialName);
        }

        /// <summary>
        /// The WWW of the element
        /// </summary>
        public WWW ElementWWW
        {
            get
            {
                return AssetsManager.DownloadAssetBundle(PopBloopSettings.CharactersBundleBaseURL + ElementName);
            }
        }

        /// <summary>
        /// The WWW of the material
        /// </summary>
        public WWW MaterialWWW
        {
            get
            {
                return AssetsManager.DownloadAssetBundle(PopBloopSettings.MaterialsBundleBaseURL + MaterialName);
            }
        }

        /// <summary>
        /// Check if element and material already loaded
        /// </summary>
        public virtual bool IsLoaded
        {
            get
            {
                try
                {
                    if (!ElementWWW.isDone)
                    {
                        return false;
                    }

                    if (_useMaterial)
                    {
                        if (!MaterialWWW.isDone)
                        {
                            return false;
                        }
                    }

                    if (_meshRequest == null)
                    {
                        _meshRequest = ElementWWW.assetBundle.LoadAsync("mesh", typeof(GameObject));
                    }

                    if (_boneNamesRequest == null)
                    {
                        _boneNamesRequest = ElementWWW.assetBundle.LoadAsync("bonenames", typeof(StringHolder));
                    }

                    if (_useMaterial)
                    {
                        if (_materialRequest == null)
                        {
                            _materialRequest = MaterialWWW.assetBundle.LoadAsync(_materialName, typeof(Material));
                        }
                    }

                    if (!_meshRequest.isDone) return false;
                    if (!_boneNamesRequest.isDone) return false;


                    if (_useMaterial && _materialRequest != null)
                    {
                        if (!_materialRequest.isDone) return false;
                    }
                }
                catch (System.Exception ex)
                {
                    Debug.LogError("CharacterElement.IsLoaded: Element: " + ElementName + ", Material: " + MaterialName +
                        " is throw exception: " + ex.ToString());
                }

                return true;
            }
        }

        private Material _activeMaterial = null;
        public virtual Material ActiveMaterial
        {
            get { return _activeMaterial; }
        }

        private SkinnedMeshRenderer _activeSkinnedMeshRenderer = null;
        public virtual SkinnedMeshRenderer ActiveSkinnedMeshRenderer
        {
            get
            {
                return _activeSkinnedMeshRenderer;
            }
        }

        protected Material CombineMaterials(IEnumerable<Material> materials)
        {
            int maxWidth = 32;
            int maxHeight = 32;
            foreach (Material mat in materials)
            {
                Texture2D texture = (Texture2D)mat.mainTexture;

                if (texture.width > maxWidth)
                {
                    maxWidth = texture.width;
                }

                if (texture.height > maxHeight)
                {
                    maxHeight = texture.height;
                }
            }

            Texture2D combinedTexture = new Texture2D(maxWidth, maxHeight, TextureFormat.ARGB32, false);

            // Set the background color as transparent
            for (int y = 0; y < combinedTexture.height; y++)
            {
                for (int x = 0; x < combinedTexture.width; x++)
                {
                    combinedTexture.SetPixel(x, y, new Color(0, 0, 0, 0));
                }
            }

            
            // Combine all the materials' textures into combinedTextures
            foreach (Material mat in materials)
            {
                //Debug.LogWarning("Combining Texture: " + mat.name);
                
                Texture2D mainTexture = (Texture2D)mat.mainTexture;

                for (int y = 0; y < mat.mainTexture.height; y++)
                {
                    for (int x = 0; x < mat.mainTexture.width; x++)
                    {
                        var pixel = mainTexture.GetPixel(x, y);

                        // If we have transparent pixel, do not draw it
                        if (pixel.a == 0)
                        {
                            continue;
                        }

                        combinedTexture.SetPixel(x, y, pixel);
                    }
                }
            }

            /*
             *  If the device does not support decal texture, might use these codes
             */ 
            Texture2D skinTexture = AssetsManager.SkinColors[characterGenerator.SkinColorIndex];
            Color skinColor = skinTexture.GetPixel(0, 0);

            // Extra phase for skin
            for (int y = 0; y < combinedTexture.height; y++)
            {
                for (int x = 0; x < combinedTexture.width; x++)
                {
                    var pixel = combinedTexture.GetPixel(x, y);

                    // Alpha blend between 2 pixels

                    float blendColorR = (float)((pixel.a * pixel.r) + ((1.0 - pixel.a) * skinColor.r));
                    float blendColorG = (float)((pixel.a * pixel.g) + ((1.0 - pixel.a) * skinColor.g));
                    float blendColorB = (float)((pixel.a * pixel.b) + ((1.0 - pixel.a) * skinColor.b));
                    
                    combinedTexture.SetPixel(x, y, new Color(blendColorR, blendColorG, blendColorB));
                }
            }

            // Apply the combo
            combinedTexture.Apply();

            Material newMat = new Material(Shader.Find("Diffuse"));
            //Texture2D skinTexture = AssetsManager.SkinColors[characterGenerator.SkinColorIndex];

            // Set the skin color texture as the base texture
            newMat.mainTexture = combinedTexture;
            
            /******************** END OF COMPAT CODES **************************************/

            /*
             * ORIGINAL CODE FOR WEB PLATFORM
             */
            /*
            // Apply the combo
            combinedTexture.Apply();

            Material newMat = new Material(Shader.Find("Decal"));
            Texture2D skinTexture = AssetsManager.SkinColors[characterGenerator.SkinColorIndex];

            // Set the skin color texture as the base texture
            newMat.mainTexture = skinTexture;

            // Set the facial as the decal texture
            newMat.SetTexture("_DecalTex", combinedTexture);
            */
            /******************** END OF ORIGINAL CODES ************************************/

            return newMat;
        }

        public SkinnedMeshRenderer GetSkinnedMeshRenderer()
        {
            return GetSkinnedMeshRenderer(true);
        }

        /// <summary>
        /// Get the skinned mesh renderer of the loaded element
        /// </summary>
        /// <returns>SkinnedMeshRenderer</returns>
        public virtual SkinnedMeshRenderer GetSkinnedMeshRenderer(bool combineTextureWithSkin)
        {
            if (_meshRequest.asset == null)
            {
                Debug.LogWarning("_meshrequest null: ElementName: " + ElementName + ", Material: " + MaterialName);
                return null;
            }

            GameObject go = (GameObject)UnityEngine.Object.Instantiate(_meshRequest.asset);

            Material material = _useMaterial ? (Material)_materialRequest.asset : null;

            if (material != null)
            {
                //TODO: if all materials is already set with read/write enabled then use this
                //Material combinedMaterial = CombineMaterials(new Material[] { material });
                go.renderer.material = combineTextureWithSkin ? CombineMaterials(new List<Material>() { material }) : material;
                _activeMaterial = material;
            }

            _activeSkinnedMeshRenderer = (SkinnedMeshRenderer)go.renderer;

            return _activeSkinnedMeshRenderer;
        }

        /// <summary>
        /// Get the Bone Names associated with this element
        /// </summary>
        /// <returns>Array of string of Bone Names</returns>
        public virtual string[] GetBoneNames()
        {
            var holder = (StringHolder)_boneNamesRequest.asset;

            return holder.content;
        }

        /// <summary>
        /// Change the skin color of the body's element
        /// </summary>
        /// <param name="color">The skin color</param>
        public virtual void ChangeSkinColor(Color color)
        {
            if (_activeSkinnedMeshRenderer != null)
            {
                Material[] materials = _activeSkinnedMeshRenderer.materials;

                foreach (Material material in materials)
                {
                    if (material.name.ToLower().Contains("skintone"))
                    {
                        material.color = color;
                        break;
                    }
                }
            }
        }

        public virtual void ChangeSkinTexture(Texture2D texture)
        {
            if (_activeSkinnedMeshRenderer != null && _activeMaterial != null)
            {
                Material[] materials = _activeSkinnedMeshRenderer.materials;

                CombineMaterials(materials);

                /* FOR MULTI MATERIALS
                foreach (Material material in materials)
                {
                    //GenerateMaterialWithSkin(material);
                    if (material.shader.name.ToLower() == "decal")
                    {
                        material.mainTexture = texture;
                        break;
                    } 

                    if (material.name.ToLower().Contains("skintone"))
                    {
                        material.mainTexture = texture;
                        break;
                    }
                }*/
            }
        }

        public virtual void ChangeLayer(int layer)
        {
            if (_activeSkinnedMeshRenderer != null)
            {
                _activeSkinnedMeshRenderer.gameObject.layer = layer;
            }
        }

        #endregion

    }
}