using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Client;
using PB.Game;

public class UIBubbleView : GUIBase
{
    #region MemVars & Props

    #region Helper Class

    public class BubbleElement
    {
        public Vector2 Size = Vector2.zero;
        public Vector3 Offset = Vector3.zero;
        public Transform Transform;
        public GUIContent Content = new GUIContent("");

        public BubbleElement(GUIContent content, Transform transform, Vector2 size, Vector3 offset)
        {
            this.Content = content;
            this.Size = size;
            this.Offset = offset;
            this.Transform = transform;
        }
    }

    #endregion

    public Font font;
    public int fontSize = 12;

    private GUIStyle bubbleStyle0;
    private GUIStyle pointStyle;

    protected static List<BubbleElement> _contents = new List<BubbleElement>();
    protected static Dictionary<GUIContent, BubbleElement> _contentMap = new Dictionary<GUIContent, BubbleElement>();
    private List<int> _bubbleToRemove = new List<int>();
    private Texture2D _pointTexture;
    private Rect bubbleRect;


    #endregion


    #region MonoBehavior's Methods

    public override void Start() 
    {
        // Bubble Style 0 for when the bubble is at the left of the center screen
        bubbleStyle0 = new GUIStyle();
        bubbleStyle0.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Chat/shout_bg");
        bubbleStyle0.font = ResourceManager.Instance.LoadFont(ResourceManager.FontABeeZeeRegular);
        bubbleStyle0.fontSize = fontSize;
        bubbleStyle0.fontStyle = FontStyle.Italic;
        bubbleStyle0.normal.textColor = Color.white;
        
        //bubbleStyle0.border.top = 15;
        //bubbleStyle0.border.bottom = 15;
        //bubbleStyle0.border.left = 50;
        
        bubbleStyle0.padding.top = 5;
        bubbleStyle0.padding.bottom = 5;
        bubbleStyle0.padding.left = 5;
        bubbleStyle0.padding.right = 5;
        
        bubbleStyle0.alignment = TextAnchor.MiddleCenter;

        bubbleStyle0.wordWrap = true;

        _pointTexture = ResourceManager.Instance.LoadTexture2D("GUI/Chat/shout_bg_point");
        bubbleRect = new Rect();
	}

    public override void OnGUI()
    {
        if (PBGameMaster.GameState != GameStateType.WorldEntered || _contents.Count < 1)
        {
            return;
        }

        // Offset of ordered stack of bubbles, the last added will stay at 0
        float offset = 0;
        int index = 0;

        _bubbleToRemove.Clear();

        for (int i = 0; i < _contents.Count; i++)
        {
            var element = _contents[i];

            if (element.Transform == null)
            {
                _bubbleToRemove.Add(i);    
                continue;
            }

            // Transform the bubble position to screen point
            Vector3 screenPoint = CalcBubblePosition(element.Transform.position + element.Offset);

            // Put the position at the center of the object
            float posX = screenPoint.x - element.Size.x / 2;

            // position Y = object screen point - bubble size Y - total offset calculate from 0 index
            float posY = Screen.height - screenPoint.y - element.Size.y - offset;
            
            // If the bubble position is visible to user, then draw it
            if (screenPoint.z > 0)
            {
                GUIStyle style = bubbleStyle0;

                bubbleRect.y = posY;
                bubbleRect.x = posX;
                bubbleRect.width = element.Size.x;
                bubbleRect.height = element.Size.y;

                // Draw the bubble
                GUI.Label(bubbleRect, element.Content, style);

                GUI.DrawTexture(new Rect(posX + element.Size.x * 0.5f - _pointTexture.width * 0.5f, posY + element.Size.y - 2, _pointTexture.width, _pointTexture.height), _pointTexture);
            }

            offset += element.Size.y + _pointTexture.height;
            index++;
        }

        foreach (int bubbleIndex in _bubbleToRemove)
        {
            if (bubbleIndex > -1 && bubbleIndex < _contents.Count)
            {
                _contents.RemoveAt(bubbleIndex);
            }
        }
    }

    #endregion


    #region Messages Methods

    public override void OnEnable()
    {
        Messenger<GUIContent, Transform, Vector3>.AddListener(Messages.BUBBLE_ADD, OnBubbleAdd);
        Messenger<GUIContent>.AddListener(Messages.BUBBLE_REMOVE, OnBubbleRemove);
        
    }

    public override void OnDisable()
    {
        Messenger<GUIContent, Transform, Vector3>.RemoveListener(Messages.BUBBLE_ADD, OnBubbleAdd);
        Messenger<GUIContent>.RemoveListener(Messages.BUBBLE_REMOVE, OnBubbleRemove);
   
    }

    void OnBubbleAdd(GUIContent content, Transform transform, Vector3 offset)
    {
        float width, height;
        CalcGUIContentSize(content, bubbleStyle0, out width, out height);

        if (_contentMap.ContainsKey(content) == false)
        {
            BubbleElement element = new BubbleElement(content, transform, new Vector2(width, height), offset);

            _contentMap.Add(content, element);
            _contents.Insert(0, element);
        }
    }

    void OnBubbleRemove(GUIContent content)
    {
        if (_contentMap.ContainsKey(content))
        {
            BubbleElement element = _contentMap[content];

            _contents.Remove(element);
            _contentMap.Remove(content);
        }
    }

    public void CalcGUIContentSize(GUIContent content, GUIStyle style, out float width, out float height)
    {
        float minWidth;
        float maxWidth;

        //GetPadding();

        style.CalcMinMaxWidth(content, out minWidth, out maxWidth);

        float threshold = 250;

        if (maxWidth < threshold)
        {
            style.wordWrap = false;
            Vector2 size = style.CalcSize(content);
            style.wordWrap = true;
            maxWidth = size.x;   
        }

        width = Mathf.Clamp(maxWidth, 0, threshold);
        height = Mathf.Clamp(style.CalcHeight(content, width), 21, 150);
        //Debug.LogWarning(string.Format("min: {0}, max: {1} => w: {2}, isHeightDependentonwidht: {3}", minWidth, maxWidth, width, style));

        //SetPadding(l, t, r, b);
    }

    private Vector3 CalcBubblePosition(Vector3 position)
    {
        return Camera.main.WorldToScreenPoint(position);
    }

    public void Clear()
    {
        _contentMap.Clear();
        _contents.Clear();
    }

    #endregion
}
