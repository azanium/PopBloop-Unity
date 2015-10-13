using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class StreamController : UIViewController
{
    #region MemVars & Props

    public GameObject itemPrefab;
    public GameObject commentPrefab;
    public UIGrid itemGrid;
    public UITable commentTable;

    #region Items

    public enum StreamDataCategory
    {
        Hat = 0,
        Head,
        Body,
        Foot
    }

    public class StreamDataItem
    {
        public StreamDataCategory Category;
        public string Description;
        public string Image;

        public StreamDataItem(StreamDataCategory category, string image, string description)
        {
            Category = category;
            Description = description;
            Image = image;
        }
    }

    public List<StreamDataItem> itemsData = new List<StreamDataItem>()
    {
        new StreamDataItem(StreamDataCategory.Hat, "topi koki merah", "Topi Koki Merah"),
        new StreamDataItem(StreamDataCategory.Hat, "fedore brown", "Fedora Brown"),
        new StreamDataItem(StreamDataCategory.Hat, "kuplux", "Kuplux"),
        new StreamDataItem(StreamDataCategory.Head, "helm full face", "Helm Full Face"),
        new StreamDataItem(StreamDataCategory.Head, "helm half face", "Helm Half Face"),
        new StreamDataItem(StreamDataCategory.Hat, "baret", "Baret")
    };

    private Dictionary<GameObject, StreamDataItem> itemsMap = new Dictionary<GameObject, StreamDataItem>();

    #endregion

    #region Comments

    public class StreamDataComment
    {
        public string Description;
        public string Image;

        public StreamDataComment(string image, string description)
        {
            Description = description;
            Image = image;
        }
    }

    public List<StreamDataComment> commentsData = new List<StreamDataComment>()
    {
        new StreamDataComment("", "[0000FF]Wolverine: [-]loves your MIX"),
        new StreamDataComment("", "[0000FF]Nadia: [-]mentioned you in LA SUMMER JEANS"),
        new StreamDataComment("", "[0000FF]Hani: [-]mentioned you in PRADA GOLDEN AXE"),
        new StreamDataComment("", "[0000FF]QQ: [-]Where do you got this?"),
        new StreamDataComment("", "[0000FF]John McClaine: [-]It's nice"),
        new StreamDataComment("", "[0000FF]John McClaine: [-]Want One"),
        new StreamDataComment("", "[0000FF]Hani: [-]Will try it.."),
        new StreamDataComment("", "[0000FF]Hani: [-]I've tried it.."),
    };

    private Dictionary<GameObject, StreamDataComment> commentsMap = new Dictionary<GameObject, StreamDataComment>();

    #endregion

    #endregion


    #region Mono Methods

    public override void Start()
    {
        SetItemsSource(itemsData);
        SetCommentsSource(commentsData);
    }

    public override void OnEnable()
    {
        DressRoom.HideCharacter();
        TabMenuController.SetKamarPasState(TabMenuController.KamarPasState.Stream);
    }

    #endregion


    #region Internal & Public Methods

    private IEnumerator UpdateItemsGrid()
    {
        itemGrid.Reposition();
        yield return new WaitForEndOfFrame();
    }

    public void SetItemsSource(IEnumerable<StreamDataItem> items)
    {
        if (itemGrid == null || itemPrefab == null)
        {
            return;
        }

        itemsMap.Clear();

        foreach (StreamDataItem item in items)
        {
            GameObject obj = (GameObject)Instantiate(itemPrefab, Vector3.zero, Quaternion.identity);

            obj.transform.parent = this.itemGrid.transform;
            obj.transform.localScale = Vector3.one;
            obj.transform.localPosition = new Vector3(0, 0, 2);

            var sprite = obj.GetComponentInChildren<UISprite>();
            if (sprite != null)
            {
                sprite.spriteName = item.Image;
            }
            var label = obj.GetComponentInChildren<UILabel>();
            if (label != null)
            {
                label.text = item.Description;
            }
            UIButtonMessage target = obj.GetComponentInChildren<UIButtonMessage>();
            if (target != null)
            {
                target.target = this.gameObject;
            }

            itemsMap.Add(obj, item);
        }

        StartCoroutine(UpdateItemsGrid());
    }

    private IEnumerator UpdateComentsTable()
    {
        commentTable.Reposition();
        yield return new WaitForEndOfFrame();
    }

    public void SetCommentsSource(IEnumerable<StreamDataComment> items)
    {
        if (commentTable == null || commentPrefab == null)
        {
            return;
        }

        commentsMap.Clear();
        foreach (StreamDataComment item in items)
        {
            GameObject obj = (GameObject)Instantiate(commentPrefab, Vector3.zero, Quaternion.identity);

            obj.transform.parent = this.commentTable.transform;
            obj.transform.localScale = new Vector3(30, 30, 1);
            obj.transform.localPosition = new Vector3(0, 0, 2);

            //var sprite = obj.GetComponentInChildren<UISprite>();
            var label = obj.GetComponentInChildren<UILabel>();
            if (label != null)
            {
                label.text = item.Description;
            }

            UIButtonMessage target = obj.GetComponentInChildren<UIButtonMessage>();
            if (target != null)
            {
                target.target = this.gameObject;
            }

            commentsMap.Add(obj, item);
        }

        StartCoroutine(UpdateComentsTable());
    }

    #endregion


    #region Events

    public void OnMore(GameObject sender)
    {
        UINavigationController.PushController(typeof(StoreController));
    }

    public void OnItemClick(GameObject sender)
    {
        if (itemsMap.ContainsKey(sender))
        {
            var item = itemsMap[sender];
            UINavigationController.PushController(typeof(AvatarPreviewController), (c) => 
            {
                string heading = "";
                switch (item.Category)
                {
                    case StreamDataCategory.Head:
                    case StreamDataCategory.Hat:
                        heading = "Headwear";
                        break;

                    case StreamDataCategory.Body:
                        heading = "Cloth";
                        break;

                    case StreamDataCategory.Foot:
                        heading = "Footwear"; 
                        break;
                }
                /*
                AvatarPreviewPageController controller = (AvatarPreviewPageController)c;
                if (controller != null)
                {
                    controller.SetHeading(heading);
                    controller.SetDetail(item.Description);
                }*/
            }, null);
        }
    }

    public void OnCommentClick(GameObject sender)
    {
        Debug.LogWarning("comment clicked");
    }

    #endregion
}
