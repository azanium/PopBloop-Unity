using UnityEngine;

using System.Collections;
using System.Collections.Generic;
using PB.Client;

public class Gender : JsonModel<Gender>
{
    public enum GenderType
    {
        None = 0,
        Male,
        Female
    }

    public enum BodyType
    {
        None = 0,
        Thin,
        Medium,
        Fat
    }

    public string id { get; set; }
    public bool success { get; set; }
    public string gender { get; set; }
    public string bodyType { get; set; }
    public string status { get; set; }

    public GenderType GetGender()
    {
        GenderType gen = GenderType.None;

        if (gender.ToLower() == "male")
        {
            return GenderType.Male;
        }
        else if (gender.ToLower() == "female")
        {
            return GenderType.Female;
        }

        return gen;
    }

    public BodyType GetBodyType()
    {
        BodyType btype = BodyType.None;

        if (bodyType.ToLower() == "thin" || bodyType.ToLower() == "small")
        {
            return BodyType.Thin;
        }
        else if (bodyType.ToLower() == "medium")
        {
            return BodyType.Medium;
        }
        else if (bodyType.ToLower() == "fat" || bodyType.ToLower() == "big")
        {
            return BodyType.Fat;
        }
        else
        {
            return BodyType.Medium;
        }

        return btype;
    }

    public static string GetGenderApi(string email)
    {
        return string.Format("{0}api/mobile/avatar/getgender/{1}", PopBloopSettings.WebServerUrl, email);
    }

}
