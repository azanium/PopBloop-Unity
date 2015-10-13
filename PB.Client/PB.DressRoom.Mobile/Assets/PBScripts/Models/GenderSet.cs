using UnityEngine;

using System.Collections;
using System.Collections.Generic;
using PB.Client;

public class GenderSet : JsonModel<GenderSet>
{
    public string id { get; set; }
    public bool success { get; set; }
    public string bodyType { get; set; }
    public string configuration { get; set; }

    public static string GetSetGenderApi(string email, string gender)
    {
        return string.Format("{0}api/mobile/avatar/setgender/{1}/{2}?rand={3}", PopBloopSettings.WebServerUrl, email, gender, UnityEngine.Time.deltaTime);
    }

    public static string GetSetBodyTypeApi(string email, string bodysize)
    {
        return string.Format("{0}api/mobile/avatar/setbodytype/{1}/{2}?rand={3}", PopBloopSettings.WebServerUrl, email, bodysize, UnityEngine.Time.deltaTime);
    }
}
