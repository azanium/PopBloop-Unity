using UnityEngine;
using System.Collections;

public class UIBanner : MonoBehaviour 
{	
	public Texture2D background;
	public bool isVisible = true;
	
	void OnGUI() 
    {
        GUI.depth = 1;

		if (background != null && isVisible)	
		{
			GUI.DrawTexture(new Rect(0, 0, Screen.width, Screen.height), background);
		}		
	}
}
