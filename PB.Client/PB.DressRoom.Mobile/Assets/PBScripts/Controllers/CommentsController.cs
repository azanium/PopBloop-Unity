using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class CommentsController : UIViewController
{
    #region MemVars & Props

    public GameObject detailLablePrefab;
    public UIInput inputComment;
    public UITable tableComment;
    public UILabel commentsHeadingLabel;
    
    private string currentId, currentFunc;

    private List<GameObject> commentObjects = new List<GameObject>();

    #endregion


    #region Mono Methods

    #endregion


    #region UIViewController's Methods

    public override void OnAppear()
    {
        base.OnAppear();

        Clear();
    }

    public void Clear()
    {
        foreach (var obj in commentObjects)
        {
            Destroy(obj);
        }
        commentObjects.Clear();
    }

    public override void OnAppeared()
    {
        base.OnAppeared();

        var param = this.controllerParameters;
        
        if (param.Count == 0 || 
            !param.ContainsKey("f") ||
            !param.ContainsKey("v"))
        {
            return;
        }
        
        var func = param["f"];
        var val = param["v"];
        currentFunc = func;
        currentId = val;

        if (!string.IsNullOrEmpty(func))
        {
            if (func == "character")
            {
                NetworkController.DownloadFromUrl(Comments.ListCommentsAvatarMixApi(val, 0, 10), OnCommentsDownloadFinished);
                Debug.LogWarning(Comments.ListCommentsAvatarMixApi(val, 0, 10));
            }
            else if (func == "element")
            {
                NetworkController.DownloadFromUrl(Comments.ListCommentsAvatarItemApi(val, 0, 10), OnCommentsDownloadFinished);
                Debug.LogWarning(Comments.ListCommentsAvatarItemApi(val, 0, 10));
            }
        }
    }

    #endregion


    #region Internal & Public Methods

    private void OnCommentsDownloadFinished(WWW www)
    {
        string json = www.text;
        
        Debug.LogWarning("Comments JSON: " + json);

        var comments = Comments.CreateObject(json);
        if (comments != null)
        {
            commentsHeadingLabel.text = "Comments [FF0000]" + comments.count.ToString() + "[-]";
            Clear();

            StartCoroutine(PublishComment(comments));
        }
    }

    private IEnumerator PublishComment(Comments comments)
    {
        foreach (var com in comments.data)
        {
            if (tableComment != null)
            {
                GameObject obj = (GameObject)Instantiate(detailLablePrefab, Vector3.zero, Quaternion.identity);
                obj.transform.parent = tableComment.transform;
                obj.transform.localScale = new Vector3(30, 30, 1);
                obj.transform.position = Vector3.zero;
                
                var label = obj.GetComponent<UILabel>();
                if (label != null)
                {
                    label.text = string.Format("[0000FF]{0}: [-]{1}", com.username, com.comment);
                    inputComment.text = "";
                }

                commentObjects.Add(obj);
            }
        }

        yield return new WaitForEndOfFrame();

        tableComment.Reposition();
    }

    public void OnComment(GameObject sender)
    {
        var text = inputComment.text;
        if (text != "" && currentId != "")
        {
            var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
            var email = profile.GetString(KPConstants.KPEmail);

            Dictionary<string, object> post = new Dictionary<string, object>()
            {
                { "comment", text }
            };

            if (currentFunc == "character")
            {
                NetworkController.DownloadFromUrl(CommentsCount.CreateCommentAvatarMixApi(email, currentId), post, (www) =>
                {
                    NetworkController.DownloadFromUrl(Comments.ListCommentsAvatarMixApi(currentId, 0, 10), OnCommentsDownloadFinished);
                });
            }
            else if (currentFunc == "element")
            {
                NetworkController.DownloadFromUrl(CommentsCount.CreateCommentAvatarItemApi(email, currentId), post, (www) =>
                {
                    NetworkController.DownloadFromUrl(Comments.ListCommentsAvatarItemApi(currentId, 0, 10), OnCommentsDownloadFinished);
                });
            }

        }
    }

    #endregion
}
