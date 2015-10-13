using System.Collections.Generic;
using System.Linq;
using System.Text;

using UnityEngine;

namespace PB.Game
{

    /// <summary>
    /// Character Constants
    /// </summary>
    public class CharacterConstants
    {
        public static readonly string TagCharacter = "Character";

        /// <summary>
        /// Gender
        /// </summary>
        public enum Gender
        {
            Male,
            Female
        }

        /// <summary>
        /// Body Parts
        /// </summary>
        public enum BodyPart
        {
            Hat,
            Hair,
            HairBottom,
            Face,
            Hand,
            Body,
            Pants,
            Shoes
        }

        /// <summary>
        /// Face Parts
        /// </summary>
        public enum FacePart
        {
            Eye_Brows,
            Eyes,
            Lip,
            Acnes,
        }


        /// <summary>
        /// Convert Color Index into Color
        /// </summary>
        /// <param name="index"></param>
        /// <returns></returns>
        public static Color GetColorFromIndex(int index)
        {
            switch (index)
            {
                case 0:
                    return new Color(0.996f, 0.882f, 0.725f);

                case 1:
                    return new Color(0.992f, 0.78f, 0.525f);

                case 2:
                    return new Color(0.749f, 0.592f, 0.396f);

                case 3:
                    return new Color(0.984f, 0.773f, 0.643f);

                case 4:
                    return new Color(0.969f, 0.596f, 0.416f);

                case 5:
                    return new Color(0.667f, 0.412f, 0.286f);

                case 6:
                    return new Color(1f, 0.965f, 0.878f);

                case 7:
                    return new Color(0.992f, 0.933f, 0.78f);

                case 8:
                    return new Color(0.984f, 0.871f, 0.612f);

                case 9:
                    return new Color(0.784f, 0.694f, 0.482f);

                case 10:
                    return new Color(0.984f, 0.741f, 0.518f);

                case 11:
                    return new Color(0.973f, 0.549f, 0.267f);

                case 12:
                    return new Color(0.604f, 0.341f, 0.165f);

                case 13:
                    return new Color(0.659f, 0.447f, 0.337f);

                case 14:
                    return new Color(0.435f, 0.196f, 0.114f);

                case 15:
                    return new Color(0.624f, 0.486f, 0.337f);

                case 16:
                    return new Color(0.388f, 0.235f, 0.114f);

                case 17:
                    return new Color(0.722f, 0.612f, 0.518f);

                case 18:
                    return new Color(0.518f, 0.373f, 0.267f);

                default:
                    return Color.blue;
            }

        }
    }

}