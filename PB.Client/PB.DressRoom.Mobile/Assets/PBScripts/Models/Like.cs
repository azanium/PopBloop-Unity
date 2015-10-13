using UnityEngine;
using System.Collections;
using PB.Client;

public class Like : JsonModel<Like> 
{
    public bool isLike { get; set; }
    public int likeCount { get; set; }
    public string message { get; set; }

    public static string CheckLikeMixApi(string email, string mix_id)
    {
        return string.Format("{0}api/mobile/social/checklikeavatarmix/{1}/{2}", PopBloopSettings.WebServerUrl, email, mix_id);
    }

    public static string LikeMixApi(string email, string mix_id)
    {
        return string.Format("{0}api/mobile/social/likeavatarmix/{1}/{2}", PopBloopSettings.WebServerUrl, email, mix_id);
    }

    public static string CheckLikeAvatarApi(string email, string avatar_id)
    {
        return string.Format("{0}api/mobile/social/checklikeavataritem/{1}/{2}", PopBloopSettings.WebServerUrl, email, avatar_id);
    }

    public static string LikeAvatarApi(string email, string avatar_id)
    {
        return string.Format("{0}api/mobile/social/likeavataritem/{1}/{2}", PopBloopSettings.WebServerUrl, email, avatar_id);
    }
}
