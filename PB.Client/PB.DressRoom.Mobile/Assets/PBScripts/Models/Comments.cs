using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using PB.Client;

public class Comments : JsonModel<Comments>
{
    public class Comment
    {
        public string id { get; set; }
        public string username { get; set; }
        public string picture { get; set; }
        public string sex { get; set; }
        public string userid { get; set; }
        public string datetime { get; set; }
        public string comment { get; set; }
    }

    public int count;
    public List<Comment> data;

    public static string ListCommentsAvatarItemApi(string avatarItemId, int start, int limit)
    {
        return string.Format("{0}api/mobile/social/listcommentavataritem/{1}/{2}/{3}/", PopBloopSettings.WebServerUrl, avatarItemId, start, limit);
    }

    public static string ListCommentsAvatarMixApi(string mixId, int start, int limit)
    {
        return string.Format("{0}api/mobile/social/listcommentavatarmix/{1}/{2}/{3}/", PopBloopSettings.WebServerUrl, mixId, start, limit);
    }
}

public class CommentsCount : JsonModel<CommentsCount>
{
    public int count;

    public static string GetCountCommentsAvatarMixApi(string mixId)
    {
        return string.Format("{0}api/mobile/social/countcommentavatarmix/{1}", PopBloopSettings.WebServerUrl, mixId);
    }

    public static string GetCountCommentsAvatarItemApi(string avatarItemId)
    {
        return string.Format("{0}api/mobile/social/countcommentavataritem/{1}", PopBloopSettings.WebServerUrl, avatarItemId);
    }

    public static string CreateCommentAvatarItemApi(string email, string avatarItemId)
    {
        return string.Format("{0}api/mobile/social/addcommentavataritem/{1}/{2}", PopBloopSettings.WebServerUrl, email, avatarItemId);
    }

    public static string CreateCommentAvatarMixApi(string email, string mixId)
    {
        return string.Format("{0}api/mobile/social/addcommentavatarmix/{1}/{2}", PopBloopSettings.WebServerUrl, email, mixId);
    }
}
