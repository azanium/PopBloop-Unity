using UnityEngine;
using System.Collections;
using UnityEditor;
using PB.Common;
using System.Collections.Generic;

public class SAItemInteraction : EditorWindow
{
    private static SAItemInteraction window;

    private List<ItemInteraction.ItemInteractionType> interactionTargets = new List<ItemInteraction.ItemInteractionType>();
    private string descriptionHint = "";
    private float interactionDistance = 1.5f;
    private Vector2 scrollPosition;

    [MenuItem("PopBloop/Items/Interaction Item")]
    static void ExecuteInteractionItem()
    {
        GameObject[] objs = SAItemsMenu.GetSelections();
        if (objs == null)
        {
            return;
        }

        window = EditorWindow.GetWindow<SAItemInteraction>("PopBloop");
        window.interactionTargets.Add(ItemInteraction.ItemInteractionType.Sit);
    }

    void OnGUI()
    {
        GUILayout.Label("Item Interaction", EditorStyles.boldLabel);

        descriptionHint = EditorGUILayout.TextField("Description Hint", descriptionHint);
        interactionDistance = EditorGUILayout.FloatField("Interaction Distance", interactionDistance);
        GUILayout.BeginHorizontal();
        if (GUILayout.Button("Add"))
        {
            interactionTargets.Add(ItemInteraction.ItemInteractionType.Sit);
        }

        if (GUILayout.Button("Remove"))
        {
            interactionTargets.RemoveAt(interactionTargets.Count - 1);
        }
        GUILayout.EndHorizontal();

        scrollPosition = GUILayout.BeginScrollView(scrollPosition);

        for (int i = 0; i < interactionTargets.Count; i++)
        {
            ItemInteraction.ItemInteractionType interactionType = interactionTargets[i];
            interactionType = (ItemInteraction.ItemInteractionType)EditorGUILayout.EnumPopup("Interaction Target " + (i + 1).ToString(), interactionType);
            interactionTargets[i] = interactionType;
        }

        GUILayout.EndScrollView();

        if (GUILayout.Button("Create"))
        {
            CreateObjects();
            window.Close();
        }


    }

    void CreateObjects()
    {
        GameObject[] objs = SAItemsMenu.GetSelections();
        if (objs == null)
        {
            return;
        }
        
        foreach (GameObject go in objs)
        {
            if (SAItemsMenu.TagObject(go, LevelConstants.TagItem) == false)
            {
                break;
            }

            if (go.GetComponent<ItemInteraction>() != null)
            {
                // Remove everything
                ItemInteractionTarget[] targets = go.GetComponentsInChildren<ItemInteractionTarget>();

                foreach (ItemInteractionTarget target in targets)
                {
                    target.transform.parent = null;
                    DestroyImmediate(target.gameObject);
                }
            }

            AttachObjects(go);
        }
    }

    void AttachObjects(GameObject go)
    {
        ItemInteraction interaction = go.GetComponent<ItemInteraction>();
        if (interaction == null)
        {
            interaction = go.AddComponent<ItemInteraction>();
        }
        
        interaction.Distance = interactionDistance;
        interaction.hintDescription = descriptionHint;

        List<Transform> targets = new List<Transform>();

        int index = 1;
        foreach (ItemInteraction.ItemInteractionType interactionType in interactionTargets)
        {
            GameObject child = new GameObject((index++).ToString() + " - " + interactionType.ToString());
            child.transform.parent = go.transform;
            child.AddComponent<ItemInteractionTarget>().interactionType = interactionType;

            targets.Add(child.transform);
        }
        interaction.SetTargets(targets);
    }

}
