using System.Collections.Generic;
using System.Linq;
using System.Text;
using UnityEngine;

namespace PB.Game
{

    /// <summary>
    /// Character Configuration 
    /// </summary>
    public class CharacterConfig
    {
        // Face Parts
        public string eyeBrows = "";
        public string eyes = "";
        public string lip = "";

        public static string DEFAULT_BUG_MARKER = "";

        // Body Parts
        public string hat = "";
        public string hatMaterial = "";

        public string hair = "";
        public string hairMaterial = "";

        public string hairBottom = "";//DEFAULT_BUG_MARKER;
        public string hairBottomMaterial = "";

        public string face = "";
        public string faceMaterial = "";

        public string body = "";
        public string bodyMaterial = "";

        public string pants = "";
        public string pantsMaterial = "";

        public string shoes = "";
        public string shoesMaterial = "";

        public string hand = "";
        public string handMaterial = "";

        public CharacterConstants.Gender gender = CharacterConstants.Gender.Male;
        public string characterBase = "male_base";

        public CharacterConfig()
        {
        }

        public void PrintCharacterConfig()
        {
            /*[{'tipe':'gender','element':'male_base'},
             * {'tipe':'Face','element':'male_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_a_01_01','lip':'lips_a_01_01'},
             * {'tipe':'Hair','element':'male_hair_02','material':'male_hair_02_1'},
             * {'tipe':'Body','element':'male_hoodie_medium','material':'male_hoodie_01_1'},
             * {'tipe':'Pants','element':'male_pants_medium','material':'male_pants_4'},
             * {'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_2'},
             * {'tipe':'Hand','element':'male_body_hand','material':'male_body'}, 
             * {'tipe':'Skin','color':'1'}]
             */
            string txt = "";
            txt += "Base: " + this.characterBase + "\n";
            txt += "Gender: " + this.gender + "\n";
            txt += "Face: " + this.face + ", eye_brows: " + this.eyeBrows + ", eyes: " + this.eyes + ", lip: " + this.lip + "\n";
            txt += "Hat: " + this.hat + " : " + this.hatMaterial + "\n";
            txt += "Hair: " + this.hair + " : " + this.hairMaterial + "\n";
            txt += "Body: " + this.body + " : " + this.bodyMaterial + "\n";
            txt += "Hand: " + this.hand + " : " + this.handMaterial + "\n";
            txt += "Pants: " + this.pants + " : " + this.pantsMaterial + "\n";
            txt += "Shoes: " + this.shoes + " : " + this.shoesMaterial + "\n";

            Debug.LogWarning(txt);
        }

        public string CharacterConfigToJson(int skinColorIndex, bool isUseHat)
        {
            string json = "";
            json += "[{'tipe':'gender','element':'" + this.characterBase + "'},";
            json += "{'tipe':'Face','element':'" + this.face + "','material':'" + this.faceMaterial + "','eye_brows':'" + this.eyeBrows + "','eyes':'" + this.eyes + "','lip':'" + this.lip + "'},";

            if (isUseHat)
            {
                json += "{'tipe':'Hat','element':'" + this.hat + "','material':'" + this.hatMaterial + "'},";
            }

            json += "{'tipe':'Hair','element':'" + this.hair + "','material':'" + this.hairMaterial + "'},";
            json += "{'tipe':'Body','element':'" + this.body + "','material':'" + this.bodyMaterial + "'},";
            json += "{'tipe':'Hand','element':'" + this.hand + "','material':'" + this.handMaterial + "'},";
            json += "{'tipe':'Pants','element':'" + this.pants + "','material':'" + this.pantsMaterial + "'},";
            json += "{'tipe':'Shoes','element':'" + this.shoes + "','material':'" + this.shoesMaterial + "'},";
            json += "{'tipe':'Skin','color':'" + skinColorIndex.ToString() + "'}]";

            return json;
        }

        public string BodyPartConfigToJson(CharacterConstants.BodyPart bodyPart)
        {
            string element = "";
            string material = "";
            switch (bodyPart)
            {
                case CharacterConstants.BodyPart.Body:
                    element = this.body;
                    material = this.bodyMaterial;
                    break;
                case CharacterConstants.BodyPart.Hair:
                    element = this.hair;
                    material = this.hairMaterial;
                    break;
                case CharacterConstants.BodyPart.Hand:
                    element = this.hand;
                    material = this.handMaterial;
                    break;
                case CharacterConstants.BodyPart.Hat:
                    element = this.hat;
                    material = this.hatMaterial;
                    break;
                case CharacterConstants.BodyPart.Pants:
                    element = this.pants;
                    material = this.pantsMaterial;
                    break;
                case CharacterConstants.BodyPart.Shoes:
                    element = this.shoes;
                    material = this.shoesMaterial;
                    break;
            }
            string json = "{'tipe':'"+ bodyPart.ToString() +"','element':'"+ element +"','material':'"+ material +"'}";

            return json;
        }

        public string FacePartConfigToJson(CharacterConstants.FacePart facePart)
        {
            string material = "";
            switch (facePart)
            {
                case CharacterConstants.FacePart.Eye_Brows:
                    material = this.eyeBrows;
                    break;
                case CharacterConstants.FacePart.Eyes:
                    material = this.eyes;
                    break;
                case CharacterConstants.FacePart.Lip:
                    material = this.lip;
                    break;
            }
            string json = "{'tipe':'"+ facePart.ToString() +"','element':'"+ material +"'}";
            return json;
        }

        public static string SkinColorToJson(int index)
        {
            return index.ToString();
        }
    }

}