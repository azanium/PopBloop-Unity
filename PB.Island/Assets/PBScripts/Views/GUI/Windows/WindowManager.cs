using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using System;

using PB.Common;

public class WindowManager : MonoBehaviour
{
    #region MemVars & Props

    public GUISkin skin;

    #endregion


    #region Mono Methods

    protected void OnGUI()
    {
        GUI.depth = 0;

        if (skin != null)
        {
            GUI.skin = skin;
        }

        Draw(skin);
    }

    #endregion


    #region Manager

    public static Dictionary<string, WindowBase> Windows = new Dictionary<string, WindowBase>();
	
	public static void Draw(GUISkin skin)
	{
		if (IsVisible)
		{
			foreach (WindowBase window in Windows.Values)
			{
                if (window.angleRotation != 0f)
                {
                    Rect rect = window.WindowRect;
                    GUIUtility.RotateAroundPivot(window.angleRotation, new Vector2(rect.x + rect.width / 2, rect.y + rect.height / 2));
                }
				window.DrawGUI(skin);
			}
		}
	}
	
	public static void Destroy(string name)
	{
		if (Windows.ContainsKey(name))
		{
            Windows[name].Hide();

			Windows.Remove(name);
		}
	}

    public static void Clear()
    {
        foreach (WindowBase window in Windows.Values)
        {
            window.Hide();
        }
        Windows.Clear();
    }
	
    private static bool _isVisible = true;
	public static bool IsVisible
	{
		get { return _isVisible; }
		set { _isVisible = value; }
	}

    public static bool IsPointOutsideGUI(Vector3 pos)
    {
        bool isOutside = true;
        foreach (WindowBase win in Windows.Values)
        {
            if (win.WindowRect.Contains(pos) && win.IsVisible)
            {
                isOutside = false;
                break;
            }
        }

        return isOutside;
    }

	#endregion
	
	
	#region Factory
	
	public static WindowBase CreateWindow(string name)
	{
		WindowBase win = null;
		if (Windows.ContainsKey(name))
		{
			win = Windows[name];
		}
		else
		{
			win = new winTest(name);
		
			Windows.Add(name, win);
		}
		
		return win;
	}
	
	public static winDialog CreateDialog(string name, string caption, string content, string[] choices, Action<winDialog, int> callback)
	{
		winDialog dlg = null;
		if (Windows.ContainsKey(name))
		{
			dlg = (winDialog)Windows[name];
			dlg.Initialize(caption, content, choices, callback);
			dlg.Show();
		}
		else
		{
			dlg = new winDialog(name, caption, content, choices, callback);
			
			Windows.Add(name, dlg);
		}
		
		return dlg;
	}

    public static winDialogStory CreateDialogStory(string name, string caption, DialogStory story, Action<winDialogStory> OnCloseCallback)
    {
        winDialogStory dlg = null;
        if (Windows.ContainsKey(name))
        {
            dlg = (winDialogStory)Windows[name];
            dlg.Initalize(name, caption, story, OnCloseCallback);
            dlg.Show();
        }
        else
        {
            dlg = new winDialogStory(name, caption, story, OnCloseCallback);

            Windows.Add(name, dlg);
        }

        return dlg;
    }
	
	public static winInventory CreateInventoryWindow(string name, string caption, float posX, float posY, int sizeX, int sizeY, Action<winInventory> onClose)
	{
		winInventory inventory = null;
		if (Windows.ContainsKey(name))
		{
			inventory = (winInventory)Windows[name];
			inventory.Initialize(caption);
			inventory.Show();
		}
		else
		{
			inventory = new winInventory(name, caption, posX, posY, sizeX, sizeY, onClose);
			
			Windows.Add(name, inventory);
		}
		
		return inventory;
	}

    public static winQuest CreateQuestWindow(string name, string caption, float posX, float posY, Action<winQuest> onClose)
    {
        winQuest quest = null;
        if (Windows.ContainsKey(name))
        {
            quest = (winQuest)Windows[name];
            quest.Initialize(caption);
            quest.Show();
        }
        else
        {
            quest = new winQuest(name, caption, posX, posY, onClose);

            Windows.Add(name, quest);
        }

        return quest;
    }

    public static winLogin CreateLoginWindow(string title, Action<winLogin, string, string> onLogin)
    {
        winLogin login = null;
        if (Windows.ContainsKey(title))
        {
            login = (winLogin)Windows[title];
            login.OnLogin = onLogin;
            login.Show();
        }
        else
        {
            login = new winLogin(title, onLogin);

            Windows.Add(title, login);
        }

        return login;
    }

    public static winPhotoGallery CreatePhotoGallery(string title, Texture2D texture)
    {
        winPhotoGallery gallery = null;
        if (Windows.ContainsKey(title))
        {
            gallery = (winPhotoGallery)Windows[title];
            gallery.Initialize(texture);
            gallery.Show();
        }
        else
        {
            gallery = new winPhotoGallery(title, title, texture);

            Windows.Add(title, gallery);
        }

        return gallery;
    }

	#endregion

}
