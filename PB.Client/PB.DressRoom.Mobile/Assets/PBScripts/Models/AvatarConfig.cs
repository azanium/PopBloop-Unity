using UnityEngine;

using System.Collections;
using System.Collections.Generic;
using PB.Client;

public class AvatarConfig : JsonModel<AvatarConfig>
{
    public string id { get; set; }
    public bool success { get; set; }
    public string bodyType { get; set; }
    public string configuration { get; set; }

    public static string GetDefaultAvatarConfigByGenderApi(string email, string gender)
    {
        return string.Format("{0}api/mobile/avatar/defaultavatar/{1}/{2}", PopBloopSettings.WebServerUrl, email, gender);
    }

    public static string GetDefaultAvatarConfigByBodyTypeApi(string email, string bodySize)
    {
        return string.Format("{0}api/mobile/avatar/defaultbodytype/{1}/{2}", PopBloopSettings.WebServerUrl, email, bodySize);
    }

    public static string SetAvatarConfigApi(string email)
    {
        return string.Format("{0}api/mobile/avatar/setconfig/{1}?rand={2}", PopBloopSettings.WebServerUrl, email, UnityEngine.Time.deltaTime);
    }
}
