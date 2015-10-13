using UnityEngine;
using System.Collections;
using System;

public class winLogin : WindowBase
{
    private GUIStyle _styleFailed;
    public string InfoText = "";
    public Action<winLogin, string, string> OnLogin = null;
    private string username = "";
    private string password = "";
    private const string ctrlUsername = "ctrlUsername";
    private const string ctrlPassword = "ctrlPassword";
    private GUIStyle _style;

    public winLogin(string title, Action<winLogin, string, string> onLogin)
        : base(title, new GUIContent(title))
    {
        IsDraggable = false;

        TweenDirection = WindowTweenDirection.FromTop;

        MakeCenter(250, 200);

        OnLogin = onLogin;

        //_styleFailed = new GUIStyle();
        //_styleFailed.normal.textColor = Color.red;
    }

    public override void Draw(int id)
    {
        base.Draw(id);

        GUI.BringWindowToFront(id);

        GUI.SetNextControlName("");
        Rect area = new Rect(10, 30, WindowRect.width - 10, WindowRect.height - 10);

        GUILayout.BeginArea(area);
            GUILayout.BeginVertical();
                GUILayout.BeginHorizontal();
                    GUILayout.Label("Username", GUILayout.Width(80));
                    GUI.SetNextControlName(ctrlUsername);
                    username = GUILayout.TextField(username, GUILayout.Width(130));
                GUILayout.EndHorizontal();

                GUI.SetNextControlName("");
        
                GUILayout.BeginHorizontal();
                    GUILayout.Label("Password", GUILayout.Width(80));
                    GUI.SetNextControlName(ctrlPassword);
                    password = GUILayout.PasswordField(password, '*', GUILayout.Width(130));
                GUILayout.EndHorizontal();

                GUILayout.BeginHorizontal();
                    GUILayout.Label("", GUILayout.Width(80));
                    GUILayout.Label(InfoText, GUILayout.Width(130));
                GUILayout.EndHorizontal();

            GUILayout.EndVertical();
        GUILayout.EndArea();

        float offset = 30;
        

        if (GUI.Button(new Rect(area.x + 70, area.y + offset * 4, 100, 25), "Login"))
        {
            Login();
        }

        if (Event.current.character == '\n')
        {
            string control = GUI.GetNameOfFocusedControl();
            
            if (control == ctrlPassword)
            {
                Login();
            }
        }
    }

    private void Login()
    {
        if (OnLogin != null)
        {
            OnLogin(this, username, password);
        }
        username = "";
        password = "";
        GUI.FocusControl(ctrlUsername);
    }
	
}
