using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class UINotificationCenter : GUIBase
{
    #region MemVars & Props

    public class NotificationItem
    {
        public Rect area;
        public Vector2 newPosition;
        public GUIContent notification;
        public float time;
        public float duration;
    }

    public Rect defaultArea;
    public float notificationSpace = 5;
    public float notificationDuration = 15000f;
    public int maxNotification = 7;

    private static UINotificationCenter notificationCenter;
    private List<NotificationItem> notificationItems;
    private List<NotificationItem> itemsToRemove = new List<NotificationItem>();
    private GUIStyle notificationStyle;


    #endregion


    #region MonoBehavior's Methods

    public override void Awake()
    {
        notificationCenter = this;
        notificationItems = new List<NotificationItem>();
        
        notificationStyle = new GUIStyle();
        notificationStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Notification/notification_bg");
        notificationStyle.font = ResourceManager.Instance.LoadFont(ResourceManager.FontABeeZeeRegular);
        notificationStyle.fontSize = 12;
        notificationStyle.alignment = TextAnchor.MiddleCenter;
        notificationStyle.normal.textColor = Color.white;

        notificationStyle.padding = new RectOffset(5, 5, 5, 5);

        defaultArea = new Rect(10, 50, 100, 30);
    }

    public override void OnDestroy()
    {
    }

    public override void Start() 
    {
	}

    public override void Update() 
    {
    }

    public override void OnGUI()
    {
        if (PBGameMaster.GameState != GameStateType.WorldEntered)
        {
            return;
        }

        if (notificationItems.Count > 0)
        {
            itemsToRemove.Clear();
            
            int count = 0;

            foreach (NotificationItem item in notificationItems)
            {
                count++;
                item.time += Time.deltaTime;
                if (item.time >= item.duration * 0.001f || count > 7)
                {
                    itemsToRemove.Add(item);
                }
                else
                {
                    item.area.x = Mathf.Lerp(item.area.x, item.newPosition.x, Time.deltaTime * 3f);
                    item.area.y = Mathf.Lerp(item.area.y, item.newPosition.y, Time.deltaTime * 6f);
                    GUI.Box(item.area, item.notification, notificationStyle);
                }
            }

            foreach (NotificationItem item in itemsToRemove)
            {
                notificationItems.Remove(item);
            }
        }
    }

    public override void OnEnable()
    {
    }

    public override void OnDisable()
    {
    }

    #endregion


    #region Public Methods

    public static void Notify(string text)
    {
        if (notificationCenter == null)
        {
            return;
        }

        NotificationItem item = new NotificationItem();
        item.notification = new GUIContent(text);
        item.area = notificationCenter.defaultArea;
        item.newPosition = new Vector2(item.area.x, item.area.y);
        item.time = 0;
        item.duration = notificationCenter.notificationDuration;

        Vector2 size = notificationCenter.notificationStyle.CalcSize(item.notification);
        item.area.width = size.x + 20;
        item.area.x -= item.area.width + 5;
        item.area.height = notificationCenter.notificationStyle.normal.background.height;

        notificationCenter.notificationItems.Insert(0, item);
        notificationCenter.ReArrangeItems();
    }

    #endregion


    #region Private Methods

    private void ReArrangeItems()
    {
        if (notificationItems.Count > 1)
        {
            NotificationItem firstItem = notificationItems[0];
            Vector2 firstSize = notificationStyle.CalcSize(firstItem.notification);

            for (int i = 1; i < notificationItems.Count; i++)
            {
                NotificationItem item = notificationItems[i];
                item.newPosition = new Vector2(item.newPosition.x, item.newPosition.y + firstSize.y + notificationSpace);
            }
        }
    }

    #endregion
}
