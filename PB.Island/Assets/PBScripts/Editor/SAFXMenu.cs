using UnityEngine;
using System.Collections;
using UnityEditor;

public class SAFXMenu
{
    
    [MenuItem("PopBloop/Fx/UV Scroller")]
    static void Execute()
    {
        GameObject[] objs = SAItemsMenu.GetSelections();
        if (objs == null) return;

        foreach (GameObject go in objs)
        {
            if (go.GetComponent<UVScroller>() == null)
            {
                go.AddComponent<UVScroller>();
            }
        }
    }
}
