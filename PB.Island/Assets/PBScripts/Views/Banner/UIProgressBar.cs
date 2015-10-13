using UnityEngine;
using System.Collections;

public class UIProgressBar 
{

    public static readonly UIProgressBar Instance = new UIProgressBar();
    private GUIStyle _styleLoadingBar;
    private GUIStyle _styleLoadingFrame;
    private GUIStyle _styleLoadingText;
    private GUIStyle _styleProgressText;

    public UIProgressBar()
    {
        _styleLoadingBar = new GUIStyle();
        _styleLoadingBar.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Banner/loading_fill");
        
        _styleLoadingFrame = new GUIStyle();
        _styleLoadingFrame.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Banner/loading_bg");
        
        _styleLoadingText = new GUIStyle();
        _styleLoadingText.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        _styleLoadingText.fontSize = 16;
        _styleLoadingText.normal.textColor = new Color(0.6f, 0.6f, 0.6f);
        _styleLoadingText.fontStyle = FontStyle.Bold;

        _styleProgressText = new GUIStyle();
        _styleProgressText.font = _styleLoadingText.font;
        _styleProgressText.fontSize = 15;
        _styleProgressText.normal.textColor = Color.white;
        _styleProgressText.fontStyle = FontStyle.Bold;
        _styleProgressText.alignment = TextAnchor.MiddleRight;
	}

    public void Update(string title, string metaInfo, int progress, int fontSize)
    {
        DrawProgress(Screen.width / 2 - 290 / 2, 292, 290, 33, title, metaInfo, progress, fontSize);
    }
	
	public void Update(string title, int progress, int fontSize) 
    {
        DrawProgress(Screen.width / 2 - 290 / 2, 292, 290, 33, title, "", progress, fontSize);
	}

    void DrawProgress(float x, float y, float width, float height, string title, string metaInfo, int progress, int fontSize)
    {
        Rect rect = new Rect(x, y, _styleLoadingFrame.normal.background.width, _styleLoadingFrame.normal.background.height);

        _styleLoadingText.fontSize = fontSize;

        // Draw the Title
        GUI.Label(new Rect(rect.x + 15, rect.y, rect.width, rect.height), title, _styleLoadingText);

        Vector2 offset = _styleLoadingText.CalcSize(new GUIContent(title));

        // Draw the Loading Frame
        GUI.Box(new Rect(rect.x, rect.y + offset.y + 2, rect.width, rect.height), "", _styleLoadingFrame);

        Rect loadingRect = new Rect(rect.x, rect.y + offset.y + 2, ((float)progress / 100) * rect.width, rect.height);

        // Draw the Loading Bar
        GUI.BeginGroup(loadingRect);
        GUI.DrawTexture(new Rect(0, 0, rect.width, rect.height), _styleLoadingBar.normal.background);

        // Fix: issue when using Unity 4.0, so this code with if here is the work around
        if (loadingRect.width > 35)
        {
            float textWidth = loadingRect.width - 25;
            GUI.Label(new Rect(20, 0, textWidth, rect.height), string.Format("{0}{1}%", metaInfo, progress), _styleProgressText);
        }
        GUI.EndGroup();
    }
}
