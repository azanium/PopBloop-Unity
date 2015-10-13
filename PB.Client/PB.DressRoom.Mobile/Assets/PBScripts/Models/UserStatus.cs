using UnityEngine;
using System.Collections;
using PB.Client;

public class UserStatus : JsonModel<UserStatus>
{
    public string status { get; set; }

    public static string GetStatusApi(string userid)
    {
        return string.Format("{0}api/unity/user/status/{1}?rand={2}", PopBloopSettings.WebServerUrl, userid, UnityEngine.Time.deltaTime);
    }

    public static string SetStatusApi(string userid, string newStatus)
    {
        string encodedStatus = WWW.EscapeURL(newStatus);
        return string.Format("{0}api/unity/user/setstatus/{1}/{2}?rand={3}", PopBloopSettings.WebServerUrl, userid, encodedStatus, UnityEngine.Time.deltaTime);
    }
}
