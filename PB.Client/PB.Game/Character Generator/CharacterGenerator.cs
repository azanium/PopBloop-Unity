using UnityEngine;

using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using LitJson;

using PB.Client;

namespace PB.Game
{

    /// <summary>
    /// Character Generator
    /// -SA
    /// </summary>
    public class CharacterGenerator
    {
        #region MemVars & Props

        public enum UndoType
        {
            Character = 0,
            Element = 1,
            Facial = 2,
            SkinColor
        }

        protected class UndoData
        {
            public UndoType undoType;
            public string configuration;

            public UndoData(UndoType tipe, string config)
            {
                undoType = tipe;
                configuration = config;
            }
        }

        public CharacterConfig currentCharacterConfig = null;

        protected Stack<UndoData> undoStacks = new Stack<UndoData>();

        public enum BodyPartChangeType
        {
            Top,
            Middle,
            Bottom
        };
        public static event Action<BodyPartChangeType> OnBodyPartChanging = null;

        private GameObject _root;
        private Dictionary<CharacterConstants.BodyPart, CharacterElement> _elements = new Dictionary<CharacterConstants.BodyPart, CharacterElement>();
        private Dictionary<string, AnimationElement> _animations = new Dictionary<string, AnimationElement>();

        private string characterBase = "";
        private CharacterConstants.Gender currentGender = CharacterConstants.Gender.Male;

        /// <summary>
        /// Flag to know if this character is using hat
        /// </summary>
        private bool IsUseHat
        {
            get;
            set;
        }

        private bool IsCharacterReady()
        {
            if (CharacterBaseWWW == null)
            {
                return false;
            }

            // First we load the character base (this can have animations or not at all)
            if (CharacterBaseWWW.isDone == false)
            {
                return false;
            }

            // Second, we load the character base's animations (this can be emote, or any standard animations)
            foreach (AnimationElement animElement in _animations.Values)
            {
                if (!animElement.IsLoaded)
                {
                    return false;
                }
            }

            // Last, we load the character element
            foreach (CharacterElement element in _elements.Values)
            {
                if (!element.IsLoaded)
                {
                    return false;
                }
            }

            return true;
        }

        /// <summary>
        /// Check if the Character Generator is ready to generate the character
        /// </summary>
        public bool IsReady
        {
            get
            {
                return IsCharacterReady();
            }
        }

        protected List<string> _animationClips = new List<string>();
        public List<string> AnimationClips
        {
            get { return _animationClips; }
        }

        /// <summary>
        /// Download progress of the Character being generated
        /// </summary>
        public int Progress
        {
            get
            {
                int count = _elements.Values.Count + _animations.Values.Count + 1;

                float progress = 0;
                if (CharacterBaseWWW != null)
                {
                    if (CharacterBaseWWW.isDone)
                    {
                        progress++;
                    }
                }

                // Second, we load the character base's animations (this can be emote, or any standard animations)
                foreach (AnimationElement animElement in _animations.Values)
                {
                    if (animElement.IsLoaded)
                    {
                        progress++;
                    }
                }

                foreach (CharacterElement element in _elements.Values)
                {
                    if (element.IsLoaded)
                    {
                        progress++;
                    }
                }

                return (int)((progress / count) * 100);
            }
        }

        private float _fatLevel = 0.01f;
        /// <summary>
        /// Fat level of the character
        /// </summary>
        public float FatLevel
        {
            get { return _fatLevel; }
            set
            {
                if (_fatLevel != value)
                {
                    _fatLevel = value;
                }
            }
        }

        private int _skinColorIndex = 1;
        /// <summary>
        /// Skin Color Index of the generated character
        /// </summary>
        public int SkinColorIndex
        {
            get { return _skinColorIndex; }
            set
            {
                if (_skinColorIndex != value)
                {
                    UndoData data = new UndoData(UndoType.SkinColor, CharacterConfig.SkinColorToJson(_skinColorIndex));
                    undoStacks.Push(data);

                    _skinColorIndex = Math.Max(1, value);

                   // Generate(_root);

                    /* FOR MULTI MATERIALS
                    //Color color = CharacterConstants.GetColorFromIndex(value);
                    foreach (CharacterElement element in _elements.Values)
                    {
                        //element.ChangeSkinColor(color);
                        Debug.Log(element.ElementName);
                        element.ChangeSkinTexture(AssetsManager.SkinColors[value]);
                    }*/
                }
            }
        }

        public void ChangeSkinColor(int index)
        {
            if (_skinColorIndex != index)
            {
                _skinColorIndex = Math.Max(1, index);
            }
        }

        public int _layer = 1;
        public int Layer
        {
            get { return _layer; }
            set
            {
                if (_layer != value)
                {
                    _layer = Math.Max(1, value);

                    foreach (CharacterElement element in _elements.Values)
                    {
                        element.ChangeLayer(_layer);
                    }
                }
            }
        }

        #endregion


        #region Ctor & Initialization

        public CharacterGenerator()
        {
            IsUseHat = false;
        }

        #endregion


        #region Private Methods


        /// <summary>
        /// Build Elements from a given Character Configuration
        /// </summary>
        /// <param name="config">Configuration</param>
        private void BuildElementsFromConfig(CharacterConfig config)
        {
            //ChangeElement(CharacterConstants.BodyPart.Hat, config.hat, config.hatMaterial);
            ChangeFaceElement(config.face, config.eyeBrows, config.eyes, config.lip);
            //Debug.LogError("CG: hat: " + config.hat + ", mat: " + config.hatMaterial);
            //ChangeElement(CharacterConstants.BodyPart.Hat, config.hat, config.hatMaterial);
            //ChangeElement(CharacterConstants.BodyPart.Hair, config.hair, config.hairMaterial);
            ChangeHairElement(config.hair, config.hairMaterial, config.hat, config.hatMaterial);
            ChangeElement(CharacterConstants.BodyPart.Body, config.body, config.bodyMaterial);
            ChangeElement(CharacterConstants.BodyPart.Hand, config.hand, config.handMaterial);
            ChangeElement(CharacterConstants.BodyPart.Pants, config.pants, config.pantsMaterial);
            ChangeElement(CharacterConstants.BodyPart.Shoes, config.shoes, config.shoesMaterial);
        }

        #endregion


        #region Public Methods

        /// <summary>
        /// Load a Character Configuration to generate character with
        /// </summary>
        /// <param name="config"></param>
        public void LoadConfig(CharacterConfig config)
        {
            if (config == null)
            {
                Debug.Log("CharacterGenerator: Can not load null character configuration");
                return;
            }

            if (currentGender != config.gender)
            {
                ChangeCharacter(config);
            }
            else
            {
                BuildElementsFromConfig(config);
            }
        }

        /// <summary>
        /// Get the WWW of the CharacterBase that contains Bones and Animations
        /// </summary>
        public WWW CharacterBaseWWW
        {
            get
            {
                if (characterBase.Trim().Length == 0)
                {
                    return null;
                }

                return AssetsManager.DownloadAssetBundle(PopBloopSettings.CharactersBundleBaseURL + characterBase);
            }
        }

        public string Gender
        {
            get
            {
                string gender = "male";
                if (characterBase.ToLower() == "male_base")
                {
                    gender = "male";
                }
                else if (characterBase.ToLower() == "female_base")
                {
                    gender = "female";
                }
                return gender;
            }
        }

        /// <summary>
        /// Clear all Character's cached AssetBundles
        /// </summary>
        public void Clear()
        {
            if (_root != null)
            {
                UnityEngine.Object.Destroy(_root.gameObject);
            }
            AssetsManager.RemoveAssetBundle(PopBloopSettings.CharactersBundleBaseURL + characterBase);

            foreach (CharacterElement cel in _elements.Values)
            {
                cel.Clear();
            }
            _elements.Clear();
            ClearAnimations();

            Resources.UnloadUnusedAssets();
        }

        /// <summary>
        /// Change Character Configuration using JSON string Array<Dictionary>
        /// </summary>
        /// <param name="content">JSON array of dictionary</param>
        /// <returns>true if valid, false otherwise</returns>
        public bool ChangeCharacterFromJSON(string content)
        {
            if (content == "")
            {
                return false;
            }

            string json = content;

            List<Dictionary<string, string>> jsonValues = null;
            try
            {
                jsonValues = JsonMapper.ToObject<List<Dictionary<string, string>>>(json);
                //TODOCHECK
                //jsonValues = JsonConvert.DeserializeObject<List<Dictionary<string, string>>>(json);
            }
            catch (Exception ex)
            {
                Debug.LogError("Error on JsonConvert Array: " + ex.Message);
                return false;
            }

            bool result = false;

            if (jsonValues != null)
            {
                CharacterConfig config = new CharacterConfig();

                ///*********** SAVE UNDO HERE ************************/

                if (currentCharacterConfig != null)
                {
                    var undo = currentCharacterConfig.CharacterConfigToJson(SkinColorIndex, IsUseHat);
                    Debug.LogWarning("saving undo: " + undo);
                    UndoData data = new UndoData(UndoType.Character, undo);
                    undoStacks.Push(data);
                }

                currentCharacterConfig = config;

                ///***************************************************/

                foreach (Dictionary<string, string> map in jsonValues)
                {
                    if (map.ContainsKey("tipe") == false)
                    {
                        continue;
                    }

                    string tipe = map["tipe"];

                    if (tipe.ToLower() == "gender")
                    {
                        config.characterBase = map["element"];
                    }
                    else if (tipe.ToLower() == "skin")
                    {
                        if (map.ContainsKey("color"))
                        {
                            string color = map["color"];
                            if (string.IsNullOrEmpty(color) || color.ToLower() == "nan")
                            {
                                color = "1";
                            }
                            
                            _skinColorIndex = Int32.Parse(color);
                        }
                    }
                    else
                    {
                        CharacterConstants.BodyPart bodyPart = (CharacterConstants.BodyPart)Enum.Parse(typeof(CharacterConstants.BodyPart), tipe, true);
                        string element = map["element"];

                        if (bodyPart == CharacterConstants.BodyPart.Face)
                        {
                            // Only for face
                            string eyeBrows = map["eye_brows"];
                            string eyes = map["eyes"];
                            string lip = map["lip"];

                            config.face = element;
                            config.eyeBrows = eyeBrows;
                            config.eyes = eyes;
                            config.lip = lip;
                        }
                        else
                        {
                            string material = map["material"];

                            switch (bodyPart)
                            {
                                case CharacterConstants.BodyPart.Hat:
                                    config.hat = element;
                                    config.hatMaterial = material;
                                    break;

                                case CharacterConstants.BodyPart.Hair:
                                    config.hair = element;
                                    config.hairMaterial = material;
                                    break;

                                case CharacterConstants.BodyPart.Face:
                                    config.face = element;
                                    config.faceMaterial = material;
                                    break;

                                case CharacterConstants.BodyPart.Body:
                                    config.body = element;
                                    config.bodyMaterial = material;
                                    break;

                                case CharacterConstants.BodyPart.Pants:
                                    config.pants = element;
                                    config.pantsMaterial = material;
                                    break;

                                case CharacterConstants.BodyPart.Shoes:
                                    config.shoes = element;
                                    config.shoesMaterial = material;
                                    break;

                                case CharacterConstants.BodyPart.Hand:
                                    config.hand = element;
                                    config.handMaterial = material;
                                    break;

                                case CharacterConstants.BodyPart.HairBottom:
                                    config.hairBottom = element;
                                    config.hairBottomMaterial = material;
                                    break;
                            }
                        }
                    }
                }

                ChangeCharacter(config);
                result = true;
                
                //Debug.LogWarning(config.CharacterConfigToJson(SkinColorIndex, IsUseHat));
            }

            return result;
        }

        /// <summary>
        /// Change character configuarion based on CharacterConfig
        /// </summary>
        /// <param name="config">CharacterConfiguration</param>
        public void ChangeCharacter(CharacterConfig config)
        {
            Clear();

            currentGender = config.gender;
            characterBase = config.characterBase;

            if (PopBloopSettings.useLogs)
            {
                Debug.Log("CharacterGenerator: Loading Character : " + characterBase);
            }

            BuildElementsFromConfig(config);
        }

        public UndoType Undo()
        {
            UndoType retType = UndoType.Element;

            if (undoStacks.Count > 0)
            {

                var undo = undoStacks.Pop();

                if (undo == null)
                {
                    Debug.LogWarning("Undo is null");
                }
                Debug.LogWarning("Undoing: " + undo.configuration);

                switch (undo.undoType)
                {
                    case UndoType.Element:
                        ChangeElementFromJSON(undo.configuration);
                        retType = UndoType.Element;
                        break;
                    case UndoType.Facial:
                        ChangeFacePartFromJSON(undo.configuration);
                        retType = UndoType.Facial;
                        break;
                    case UndoType.Character:
                        ChangeCharacterFromJSON(undo.configuration);
                        retType = UndoType.Character;
                        break;
                    case UndoType.SkinColor:
                        int index = Int32.Parse(undo.configuration);
                        ChangeSkinColor(index);
                        retType = UndoType.SkinColor;
                        break;
                }
            }

            return retType;
        }

        public string GetCurrentCharacterConfig()
        {
            if (currentCharacterConfig != null)
            {
                return currentCharacterConfig.CharacterConfigToJson(SkinColorIndex, IsUseHat);
            }

            return "";
        }
 
        /// <summary>
        /// Change Character Element using JSON string: Dictionary
        /// </summary>
        /// <param name="content">JSON dictionary</param>
        /// <returns>true if valid, false otherwise</returns>
        public bool ChangeElementFromJSON(string content)
        {
            if (content == "")
            {
                return false;
            }

            Dictionary<string, string> jsonValues = null;
            try
            {
                jsonValues = JsonMapper.ToObject<Dictionary<string, string>>(content);
                //TODOCHECK
                //jsonValues = JsonConvert.DeserializeObject<Dictionary<string, string>>(content);
            }
            catch (Exception ex)
            {
                Debug.LogError("CharacterGenerator: Error On JsonConvert: " + ex.Message);
                return false;
            }

            bool result = false;


            if (jsonValues != null)
            {
                // If we don't find "tipe" key, then we are not dealing with correct json dictionary, bug out -SA
                if (jsonValues.ContainsKey("tipe") == false)
                {
                    return false;
                }

                IsUseHat = false;

                string tipe = jsonValues["tipe"];

                // Get list of BodyPart enums to compare to, we can change this into better codes
                string[] bodyParts = Enum.GetNames(typeof(CharacterConstants.BodyPart));

                foreach (string part in bodyParts)
                {
                    // If we have a match of "tipe" key, then we're good to go
                    if (tipe.ToLower() == part.ToLower())
                    {
                        // Get the part type
                        CharacterConstants.BodyPart bodyPart = (CharacterConstants.BodyPart)Enum.Parse(typeof(CharacterConstants.BodyPart), part, true);
                        
                        /* {'gender':'male','tipe':'face','element':'male_head','material':''} */
                        
                        string element = jsonValues["element"];
                        string material = jsonValues["material"];

                        if (bodyPart == CharacterConstants.BodyPart.Face)
                        {
                            ChangeFaceElement(element, jsonValues["eye_brows"], jsonValues["eyes"], jsonValues["lip"]);
                        }
                        else
                        {
                            string undoJson = currentCharacterConfig.BodyPartConfigToJson(bodyPart);
                            UndoData undo = new UndoData(UndoType.Element, undoJson);
                            undoStacks.Push(undo);

                            // Change the element and the material
                            ChangeElement(bodyPart, element, material);
                        }

                        result = true;

                        break;
                    }
                }

                // If this is not body part, check if this is a face part
                if (!result)
                {
                    result = ChangeFacePartFromJSON(content);
                }
            }

            return result;
        }

        /// <summary>
        /// Change Element 
        /// </summary>
        /// <param name="bodyPart">Element Part we want to change</param>
        /// <param name="elementName">The Element Bundle Name</param>
        /// <param name="materialName">The Material Bundle Name</param>
        public void ChangeElement(CharacterConstants.BodyPart bodyPart, string elementName, string materialName)
        {
            SetElement(bodyPart, elementName, materialName);
        }

        /// <summary>
        /// Change Hair Element
        /// </summary>
        /// <param name="topHairElement">The top hair element</param>
        /// <param name="topHairMat">The top hair material</param>
        /// <param name="bottomHairElement">The bottom hair material</param>
        /// <param name="bottomHairMat">The bottom hair material</param>
        public void ChangeHairElement(string hairElement, string hairMat, string hatElement, string hatMat)
        {
            string hairMaterial = hairMat == null ? null : hairMat.Trim().Length == 0 ? null : hairMat;
            string hatMaterial = hatMat == null ? null : hatMat.Trim().Length == 0 ? null : hatMat;

            // Set the top hair
            SetElement(CharacterConstants.BodyPart.Hair, hairElement, hairMaterial);

            IsUseHat = hatElement != null && hatElement != "";

            // Set the hat
            SetElement(CharacterConstants.BodyPart.Hat, hatElement, hatMaterial);
        }

        /// <summary>
        /// Change face element
        /// </summary>
        /// <param name="elementName">the element name</param>
        /// <param name="eyeBrows">eye brows</param>
        /// <param name="eyes">eyes</param>
        /// <param name="lip">lip</param>
        public void ChangeFaceElement(string elementName, string eyeBrows, string eyes, string lip)
        {
            if (PopBloopSettings.useLogs)
            {
                //Debug.Log("CharacterGenerator.ChangeFaceElement: brows: " + eyeBrows + ", eyes: " + eyes + ", lip: " + lip);
            }

            CharacterFaceElement face = null;
            if (_elements.ContainsKey(CharacterConstants.BodyPart.Face))
            {
                face = _elements[CharacterConstants.BodyPart.Face] as CharacterFaceElement;
                face.ElementName = elementName;
            }
            else
            {
                face = new CharacterFaceElement(this, elementName);
                _elements.Add(CharacterConstants.BodyPart.Face, face);
            }

            if (face != null)
            {
                face.ChangeFacePart(CharacterConstants.FacePart.Eye_Brows, eyeBrows);
                face.ChangeFacePart(CharacterConstants.FacePart.Eyes, eyes);
                face.ChangeFacePart(CharacterConstants.FacePart.Lip, lip);
            }
        }

        /// <summary>
        /// Change face part from json string
        /// </summary>
        /// <param name="content">json dictionary</param>
        /// <returns>true if succeed, false otherwise</returns>
        public bool ChangeFacePartFromJSON(string content)
        {
            if (content == "")
            {
                return false;
            }

            Dictionary<string, string> jsonValues = null;
            try
            {
                jsonValues = JsonMapper.ToObject<Dictionary<string, string>>(content);
            }
            catch (Exception ex)
            {
                Debug.LogError("Error On JsonConvert: " + ex.Message);
                return false;
            }

            bool result = false;

            if (jsonValues != null)
            {
                // If we don't find "tipe" key, then we are not dealing with correct json dictionary, bug out -SA
                if (jsonValues.ContainsKey("tipe") == false)
                {
                    return false;
                }


                string tipe = jsonValues["tipe"];

                // Get list of BodyPart enums to compare to, we can change this into better codes
                string[] parts = Enum.GetNames(typeof(CharacterConstants.FacePart));

                foreach (string part in parts)
                {
                    // If we have a match of "tipe" key, then we're good to go
                    if (tipe.ToLower() == part.ToLower())
                    {
                        // Get the part type
                        CharacterConstants.FacePart facePart = (CharacterConstants.FacePart)Enum.Parse(typeof(CharacterConstants.FacePart), part, true);

                        // Save the Undo
                        string undoJson = currentCharacterConfig.FacePartConfigToJson(facePart);
                        UndoData undo = new UndoData(UndoType.Facial, undoJson);
                        undoStacks.Push(undo);

                        ChangeFacePart(facePart, jsonValues["element"]);

                        result = true;

                        break;
                    }
                }
            }

            return result;
        }

        protected string GetElementName(CharacterConstants.BodyPart bodyPart)
        {
            if (_elements.ContainsKey(bodyPart))
            {
                return _elements[bodyPart].ElementName;
            }

            return null;
        }

        /// <summary>
        /// Set the character element and material
        /// </summary>
        /// <param name="bodyPart">The body part</param>
        /// <param name="element">The body element</param>
        /// <param name="material">The body element's material</param>
        protected void SetElement(CharacterConstants.BodyPart bodyPart, string element, string material)
        {
            if (element == null || element.ToLower().Length == 0)
            {
                if (_elements.ContainsKey(bodyPart))
                {
                    _elements.Remove(bodyPart);
                }
                return;
            }

            if (_elements.ContainsKey(bodyPart))
            {
                _elements[bodyPart].ElementName = element;
                _elements[bodyPart].MaterialName = material;
            }
            else
            {
                _elements.Add(bodyPart, new CharacterElement(this, element, material));
            }

            string hatElement = GetElementName(CharacterConstants.BodyPart.Hat);

            IsUseHat = hatElement != null;

            if (OnBodyPartChanging != null)
            {
                BodyPartChangeType part = BodyPartChangeType.Middle;
                switch (bodyPart)
                {
                    case CharacterConstants.BodyPart.Face:
                    case CharacterConstants.BodyPart.Hair:
                    case CharacterConstants.BodyPart.HairBottom:
                    case CharacterConstants.BodyPart.Hat:
                        part = BodyPartChangeType.Top;
                        break;

                    case CharacterConstants.BodyPart.Pants:
                    case CharacterConstants.BodyPart.Shoes:
                        part = BodyPartChangeType.Bottom;
                        break;
                }

                OnBodyPartChanging(part);
            }

            /// Save to the character config
            switch (bodyPart)
            {
                case CharacterConstants.BodyPart.Body:
                    currentCharacterConfig.body = element;
                    currentCharacterConfig.bodyMaterial = material;
                    break;
                case CharacterConstants.BodyPart.Face:
                    currentCharacterConfig.face = element;
                    currentCharacterConfig.faceMaterial = material;
                    break;
                case CharacterConstants.BodyPart.Hair:
                    currentCharacterConfig.hair = element;
                    currentCharacterConfig.hairMaterial = material;
                    break;
                case CharacterConstants.BodyPart.HairBottom:
                    currentCharacterConfig.hairBottom = element;
                    currentCharacterConfig.hairBottomMaterial = material;
                    break;
                case CharacterConstants.BodyPart.Hat:
                    currentCharacterConfig.hat = element;
                    currentCharacterConfig.hatMaterial = material;
                    break;
                case CharacterConstants.BodyPart.Pants:
                    currentCharacterConfig.pants = element;
                    currentCharacterConfig.pantsMaterial = material;
                    break;
                case CharacterConstants.BodyPart.Shoes:
                    currentCharacterConfig.shoes = element;
                    currentCharacterConfig.shoesMaterial = material;
                    break;
            }

            //Debug.LogWarning(currentCharacterConfig.CharacterConfigToJson(SkinColorIndex, IsUseHat));

        }

        /// <summary>
        /// Change Face Part
        /// </summary>
        /// <param name="facePart">face part enum type</param>
        /// <param name="partName">part bundle name</param>
        public void ChangeFacePart(CharacterConstants.FacePart facePart, string partName)
        {
            if (_elements.ContainsKey(CharacterConstants.BodyPart.Face) != false)
            {
                CharacterFaceElement element = _elements[CharacterConstants.BodyPart.Face] as CharacterFaceElement;

                element.ChangeFacePart(facePart, partName);

                if (OnBodyPartChanging != null)
                {
                    OnBodyPartChanging(BodyPartChangeType.Top);
                }

                switch (facePart)
                {
                    case CharacterConstants.FacePart.Eye_Brows:
                        currentCharacterConfig.eyeBrows = partName;
                        break;
                    case CharacterConstants.FacePart.Eyes:
                        currentCharacterConfig.eyes = partName;
                        break;
                    case CharacterConstants.FacePart.Lip:
                        currentCharacterConfig.lip = partName;
                        break;
                }

                //Debug.LogWarning(currentCharacterConfig.CharacterConfigToJson(SkinColorIndex, IsUseHat));
            }
        }

        /// <summary>
        /// Add Animation Clips from JSON
        /// </summary>
        /// <param name="jsonArray">json array</param>
        /// <returns>true if succeed, false if otherwise</returns>
        public bool LoadAnimationFromJSON(string jsonArray, bool isAvatarEditor)
        {
            if (jsonArray == "")
            {
                return false;
            }

            List<string> jsonValues = null;
            try
            {
                jsonValues = JsonMapper.ToObject<List<string>>(jsonArray);
            }
            catch (Exception ex)
            {
                Debug.LogError("CharacterGenerator: Error On JsonConvert: " + ex.Message);
                return false;
            }

            bool result = false;

            if (jsonValues != null)
            {
                if (PopBloopSettings.useLogs)
                {
                    //Debug.Log("CharacterGenerator: JSON: " + jsonArray);
                }

                ClearAnimations();

                _animations.Clear();
                string anims = "";
                foreach (string anim in jsonValues)
                {
                    if (isAvatarEditor)
                    {
                        if (anim.Contains("@_") == false)
                        {
                            continue;
                        }
                    }
                    else
                    {
                        if (anim.Contains("@_"))
                        {
                            continue;
                        }
                    }
                    anims += anim + ", ";
                    _animations.Add(anim, new AnimationElement(anim));
                }

                if (PopBloopSettings.useLogs)
                {
                    Debug.Log("CharacterGenerator: LoadAnimationFromJSON: animations are: " + anims);
                }
            }

            return result;
        }

        public bool IsAnimationExist(string animationName)
        {
            return _animationClips.Contains(animationName);
        }

        public void ClearAnimations()
        {
            if (_root != null)
            {
                if (_root.animation == null) return;

                // Animation map is here to compare the actual animation contained in the root.animation and the one that saved on _animationClips cache.
                Dictionary<string, AnimationClip> _animationMap = new Dictionary<string, AnimationClip>();
                foreach (AnimationState state in _root.animation)
                {
                    if (state.clip != null) 
                    {
                        _animationMap.Add(state.clip.name, state.clip);
                    }
                }

                try
                {
                    foreach (string clip in _animationClips)
                    {
                        if (_animationMap.ContainsKey(clip))
                        {
                            _root.animation.RemoveClip(clip);
                        }
                    }
                }
                catch (Exception ex)
                {
                    Debug.LogWarning("ClearAnimations: " + ex.ToString());
                }

                _animationMap.Clear();
                _animationClips.Clear();
            }
        }

        /// <summary>
        /// Generate Character GameObject from current configuration
        /// </summary>
        /// <returns></returns>
        public GameObject Generate()
        {
            try
            {
                _root = (GameObject)UnityEngine.Object.Instantiate(CharacterBaseWWW.assetBundle.mainAsset);

                // Name the root object with the character gender
                _root.name = characterBase.Substring(0, characterBase.IndexOf('_'));

                // We cache the animation clips one time
                _animationClips.Clear();
                foreach (AnimationState state in _root.animation)
                {
                    _animationClips.Add(state.clip.name);
                }

                /// Add Animations to the character
                foreach (AnimationElement anim in _animations.Values)
                {
                    if (anim.Clip == null)
                    {
                        Debug.LogWarning(string.Format("Animation Clip's: {0} is null", anim.AnimationName));
                        continue;
                    }

                    // Only add a non existing animation clip
                    if (_animationClips.Contains(anim.Clip.name) == false)
                    {
                        _root.animation.AddClip(anim.Clip, anim.Clip.name);

                        // Register it to the animation clips list
                        _animationClips.Add(anim.Clip.name);
                    }
                }

                SkinnedMeshRenderer rootSmr = _root.GetComponent<SkinnedMeshRenderer>();
                if (rootSmr != null)
                {
                    rootSmr.updateWhenOffscreen = true;
                }

                return Generate(_root);
            }
            catch (Exception ex)
            {
                Debug.LogError("CharacterGenerator.Generate(): " + ex.ToString());
            }

            return null;
        }

        /// <summary>
        /// Generate elements from a Character Base's Root GameObject
        /// </summary>
        /// <param name="root"></param>
        /// <returns></returns>
        public GameObject Generate(GameObject root)
        {
            List<Material> materials = new List<Material>();
            List<Transform> bones = new List<Transform>();
            Transform[] transforms = root.GetComponentsInChildren<Transform>(true);
            SkinnedMeshRenderer[] renderes = root.GetComponentsInChildren<SkinnedMeshRenderer>(true);
            Dictionary<string, SkinnedMeshRenderer> bodyPartRenderers = new Dictionary<string, SkinnedMeshRenderer>();
            
            // Destroy existing SkinnedMeshRenderers
            for (int i = 0; i < renderes.Length; i++)
            {
                // Enlist all the body parts skinned mesh renderer to be reused
                if (!bodyPartRenderers.ContainsKey(renderes[i].name))
                {
                    bodyPartRenderers.Add(renderes[i].name, renderes[i]);
                }
            }

            foreach (KeyValuePair<CharacterConstants.BodyPart, CharacterElement> pairCel in _elements)
            {
                SkinnedMeshRenderer smr = pairCel.Value.GetSkinnedMeshRenderer(pairCel.Key != CharacterConstants.BodyPart.Hair);
                smr.updateWhenOffscreen = true;

                // Reattach Bones, so animation affects the meshes
                bones.Clear();
                foreach (string bone in pairCel.Value.GetBoneNames())
                {
                    foreach (Transform transform in transforms)
                    {
                        if (transform.name != bone) continue;

                        bones.Add(transform);
                    }
                }

                // Reconfigure Materials
                materials.Clear();

                // FOR DEBUGGING PURPOSE: SUBJECT TO ELIMINATION AFTER EXPORTER IS READY TO EXPORT USING TRANSPARENT CUTOUT DIFFUSE
                // We will force the SkinnedMeshRenderer's shader into Transparent Cutout Diffuse
                Shader transparentShader = Shader.Find("Transparent/Cutout/Diffuse");
                foreach (Material mat in smr.materials)
                {
                    // Never add a default material, it's useless
                    if (mat.name.ToLower().Contains("default") == true)
                    {
                        continue;
                    }

                    //mat.shader = transparentShader;
                    materials.Add(mat);

                    // Post process materials
                    switch (pairCel.Key)
                    {
                        case CharacterConstants.BodyPart.Hair:
                            {
                                float cutoff = IsUseHat ? 0.5f : 0.0f;
                                mat.SetFloat("_Cutoff", cutoff);
                            }
                            break;
                    }
                }

                string partName = pairCel.Key.ToString().ToLower();
            
                if (bodyPartRenderers.ContainsKey(partName))
                {
                    var oldSmr = bodyPartRenderers[partName];
                    //Debug.LogWarning("Destroying + " + partName);
                    //oldSmr.gameObject.SetActiveRecursively(false);
                    UnityEngine.Object.Destroy(oldSmr.gameObject);
                }

                // Reattach transform, bones 
                smr.transform.parent = _root.transform;

                smr.bones = bones.ToArray();
                smr.materials = materials.ToArray();
                smr.name = partName;

                // Force this skinned mesh renderer to activate itself so it will be affected by bone animation
                smr.gameObject.active = true;
            }

            // Extra treatment for Hat, since hat is optional, so we removed any hat if no hat is being used
            if (IsUseHat == false && bodyPartRenderers.ContainsKey("hat"))
            {
                var hatSmr = bodyPartRenderers["hat"];
                UnityEngine.Object.Destroy(hatSmr.gameObject);
            }

            Resources.UnloadUnusedAssets();

            return root;
        }

        /// <summary>
        /// Not yet correct, Leave this method alone for me  -SA
        /// </summary>
        /// <param name="root"></param>
        /// <returns></returns>
        public GameObject GenerateSingular(GameObject root)
        {
            List<CombineInstance> combineInstances = new List<CombineInstance>();
            List<Material> materials = new List<Material>();
            List<Transform> bones = new List<Transform>();
            Transform[] transforms = root.GetComponentsInChildren<Transform>();

            foreach (CharacterElement cel in _elements.Values)
            {
                SkinnedMeshRenderer smr = cel.GetSkinnedMeshRenderer();
                smr.updateWhenOffscreen = true;
                smr.enabled = true;

                materials.AddRange(smr.materials);

                for (int sub = 0; sub < smr.sharedMesh.subMeshCount; sub++)
                {
                    CombineInstance ci = new CombineInstance();

                    ci.mesh = smr.sharedMesh;
                    ci.subMeshIndex = sub;
                    combineInstances.Add(ci);
                }

                bones.Clear();
                foreach (string bone in cel.GetBoneNames())
                {
                    foreach (Transform transform in transforms)
                    {
                        if (transform.name != bone) continue;

                        bones.Add(transform);
                    }
                }

                smr.transform.parent = root.transform;
                smr.bones = bones.ToArray();

                UnityEngine.Object.Destroy(smr.gameObject);
            }

            //root.active = true;

            SkinnedMeshRenderer smrRoot = root.GetComponent<SkinnedMeshRenderer>();

            smrRoot.sharedMesh = new Mesh();
            smrRoot.sharedMesh.CombineMeshes(combineInstances.ToArray(), false, false);
            smrRoot.bones = bones.ToArray();
            smrRoot.materials = materials.ToArray();

            return root;
        }

        #endregion

    }
}