using UnityEngine;
using System.Collections;
using PB.Client;

public class AvatarMix : JsonModel<AvatarMix>
{
    public string success { get; set; }
    public string filename { get; set; }
    public string message { get; set; }

    public static string CreateMixApi(string email, string mixName)
    {
        return string.Format("{0}api/mobile/mix/create/{1}/{2}", PopBloopSettings.WebServerUrl, email, WWW.EscapeURL(mixName));
    }
}
