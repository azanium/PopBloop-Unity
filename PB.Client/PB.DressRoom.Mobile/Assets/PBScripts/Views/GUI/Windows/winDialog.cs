using UnityEngine;
using System;
using System.Collections;

public class winDialog : WindowBase {
	
	public Action<winDialog, int> OnSelection;
	
	public string Content { get; set; }
	public string[] Choices { get; set; }
	
	public winDialog(string name, string caption, string content, string[] choices, Action<winDialog, int> selection) : base(name, new GUIContent(caption))
	{
		IsDraggable = false;

        TweenDirection = WindowTweenDirection.FromTop;

		MakeCenter(500, 300);
		
		Initialize(caption, content, choices, selection);
	}
		
	public void Initialize(string caption, string content, string[] choices, Action<winDialog, int> selection)
	{
		Caption = new GUIContent(caption);
		Content = content;
		OnSelection = selection;
		Choices = choices;
		
		if (Choices	== null)
		{
			Choices = new string[]  { "OK" };
		}
	}
	
	public override void Draw(int id)
	{
		base.Draw(id);
		
		float buttonHeight = 20;
		float pad = 5;
		int count = Choices.Length;
		float buttonGap = (WindowRect.width / 3) / 2;
		float gapFromBottom = 20;
		
		for (int idx = 0; idx < count; idx ++)
		{
			string choice = Choices[idx];
			if (GUI.Button(new Rect(pad + buttonGap, WindowRect.height - (count - idx) * (buttonHeight + pad) - gapFromBottom, WindowRect.width - pad * 2 - buttonGap * 2, buttonHeight), choice))
			{
				if (OnSelection != null)
				{
					OnSelection(this, idx);
				}
			}
		}
		
		float contentHeight = WindowRect.height - count * (buttonHeight + pad) - pad * 2 - _captionSize;
		Rect contentRect = new Rect(20, 25, WindowRect.width - pad * 2 - 20, contentHeight);
		
		GUI.Label(contentRect, Content);
	}
}
