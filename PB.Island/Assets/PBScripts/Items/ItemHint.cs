using UnityEngine;
using System.Collections;
using System.Timers;

[AddComponentMenu("PopBloop Scripts/GUI/Item Hint")]
public class ItemHint : MonoBehaviour 
{
    public string hintText = "";
    public float timeOut = 5000f;
    public bool useTimeout = true;

    protected GUIStyle _hintStyle;
    private bool _drawHint = false;
    protected Timer _timer;

    protected virtual void Start()
    {
        _hintStyle = new GUIStyle();
        _hintStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/hintBox");
        _hintStyle.font = ResourceManager.Instance.LoadFont("GUI/Font/DroidSans");
        _hintStyle.normal.textColor = Color.white;
        _hintStyle.fontStyle = FontStyle.Bold;
        _hintStyle.alignment = TextAnchor.MiddleCenter;

        _timer = new Timer(timeOut);
        _timer.Elapsed += new ElapsedEventHandler(timer_Elapsed);
        _timer.Start();
    }

    void timer_Elapsed(object sender, ElapsedEventArgs e)
    {
        _timer.Stop();
        _drawHint = false;
    }

    protected virtual void OnGUI()
    {
        if (!string.IsNullOrEmpty(hintText) && _drawHint)
        {
            Vector2 pos = Event.current.mousePosition;

            GUIContent content = new GUIContent(hintText);

            float minWidth, maxWidth;
            _hintStyle.CalcMinMaxWidth(content, out minWidth, out maxWidth);
            float height = _hintStyle.CalcHeight(content, maxWidth);

            GUI.Box(new Rect(pos.x, pos.y - height, maxWidth + 30, height), content, _hintStyle);
        }
    }

    void OnMouseEnter()
    {
        if (useTimeout)
        {
            _timer.Start();
        }
        _drawHint = true;
    }

    void OnMouseExit()
    {
        _timer.Stop();
        _drawHint = false;
    }
}
