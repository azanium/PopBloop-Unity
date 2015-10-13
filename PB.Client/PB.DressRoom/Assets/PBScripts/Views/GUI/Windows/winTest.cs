using UnityEngine;
using System.Collections;

public class winTest : WindowBase {

	public winTest(string name) : base(name, new GUIContent("Test"))
	{
		IsVisible = true;
		WindowRect = new Rect(100, 100, 300, 300);
		IsResizable = true;
	}
	
	public override void Draw(int id)
	{
		base.Draw(id);
		
		if (GUI.Button(new Rect(0, 0, 50, 30), "Test"))
		{
			Debug.Log("www");
		}
		
		if (Input.GetMouseButtonDown(0) == true)
		{
			Vector2 mousePos = GetMousePosition();
			Debug.Log(mousePos);
		}
	}
}
