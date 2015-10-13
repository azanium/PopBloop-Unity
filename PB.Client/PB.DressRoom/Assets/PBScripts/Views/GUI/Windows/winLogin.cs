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

    public winLogin(string title, Action<winLogin, string, string> onLogin)
        : base(title, new GUIContent(title))
    {
        IsDraggable = false;

        TweenDirection = WindowTweenDirection.FromTop;

        MakeCenter(250, 170);

        OnLogin = onLogin;

        _styleFailed = new GUIStyle();
        _styleFailed.normal.textColor = Color.yellow;
    }

    public override void Draw(int id)
    {
        base.Draw(id);

        GUI.SetNextControlName("");
        Rect area = new Rect(10, 30, WindowRect.width - 10, WindowRect.height - 10);
        
        GUI.Label(new Rect(area.x, area.y, 100, 25), "Username");

        GUI.SetNextControlName(ctrlUsername);
        username = GUI.TextField(new Rect(area.x + 70, area.y, 150, 25), username);

        float offset = 30;
        GUI.SetNextControlName("");
        GUI.Label(new Rect(area.x, area.y + offset, 100, 25), "Password");

        GUI.SetNextControlName(ctrlPassword);
        password = GUI.PasswordField(new Rect(area.x + 70, area.y + offset, 150, 25), password, '*');

        GUI.Label(new Rect(area.x + 70, area.y + offset + offset, 150, 25), InfoText, _styleFailed);

        if (GUI.Button(new Rect(area.x + 70, area.y + offset * 3, 100, 25), "Login"))
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
