using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class UIHelper
{
	public static void ChangeSpriteColor(GameObject go, Color color)
    {
        var sprite = go.transform.FindChild("Background").GetComponent<UISlicedSprite>();
        if (sprite != null)
        {
            sprite.color = color;
        }
    } 
	
	public static UILabel GetLabel(GameObject go)
    {
        if (go == null)
        {
            return null;
        }

        return go.transform.FindChild("Label").GetComponent<UILabel>();
    }
	
	public static UISlicedSprite GetBackgroundSprite(GameObject go)
	{
		if (go == null)
		{
			return null;
		}
		
		return go.transform.FindChild("Background").GetComponent<UISlicedSprite>();
	}
	
    public static UISlicedSprite GetButtonSprite(UIButton button)
    {
        if (button == null)
        {
            return null;
        }

        return button.transform.FindChild("Background").GetComponent<UISlicedSprite>();
    }

    public static UISlicedSprite GetButtonSprite(UIButtonSelection button)
    {
        var btn = button.gameObject.GetComponent<UIButton>();

        return GetButtonSprite(btn);
    }

    public static UISlicedSprite GetButtonSprite(UIButtonMultiSelection button)
    {
        var btn = button.gameObject.GetComponent<UIButtonMultiSelection>();

        return GetButtonSprite(btn);
    }

    public static UILabel GetButtonLabel(UIButton button)
    {
        if (button == null)
        {
            return null;
        }

        return button.transform.FindChild("Label").GetComponent<UILabel>();
    }

    public static UILabel GetButtonLabel(UIButtonSelection button)
    {
        if (button == null)
        {
            return null;
        }

        return button.transform.FindChild("Label").GetComponent<UILabel>();
    }

    public static UIButtonSelection GetSelectedButtonSelection(GameObject parent)
    {
        UIButtonSelection[] buttons = parent.GetComponentsInChildren<UIButtonSelection>(true);
        UIButtonSelection selection = null;
        foreach (UIButtonSelection btn in buttons)
        {
            if (btn.selected)
            {
                selection = btn;
                break;
            }
        }

        return selection;
    }

    public static void ClearSelectedButtonSelection(GameObject parent)
    {
        UIButtonSelection[] buttons = parent.GetComponentsInChildren<UIButtonSelection>(true);
        //UIButtonSelection selection = null;
        foreach (UIButtonSelection btn in buttons)
        {
            btn.selected = false;
        }
    }

    public static UIButtonMultiSelection[] GetSelectedButtonMultiSelection(GameObject parent)
    {
        UIButtonMultiSelection[] buttons = parent.GetComponentsInChildren<UIButtonMultiSelection>(true);
        List<UIButtonMultiSelection> selections = new List<UIButtonMultiSelection>();
        foreach (UIButtonMultiSelection btn in buttons)
        {
            if (btn.selected)
            {
                selections.Add(btn);
            }
        }
        return selections.ToArray();
    }

    public static UIButtonSelection GetSelectedButton(GameObject parent)
    {
        var button = GetSelectedButtonSelection(parent);
        UIButtonSelection selected = null;
        if (button != null)
        {
            selected = button.gameObject.GetComponent<UIButtonSelection>();
        }

        return selected;
    }

    public static UISlicedSprite[] GetSpritesFromGrid(UIGrid grid, out GameObject centerObject)
    {
        UISlicedSprite[] sprites = grid.GetComponentsInChildren<UISlicedSprite>();
        
        centerObject = null;
        if (grid.gameObject.GetComponent<UICenterOnChild>() != null)
        {
            centerObject = grid.gameObject.GetComponent<UICenterOnChild>().centeredObject;
        }

        return sprites;
    }
}
