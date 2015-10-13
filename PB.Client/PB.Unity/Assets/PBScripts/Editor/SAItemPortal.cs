using UnityEngine;
using System.Collections;
using UnityEditor;
using PB.Common;
using System.Collections.Generic;

public class SAItemPortal : EditorWindow
{
    private static SAItemPortal window;

    private string worldName = "";
    private string questionText = "Do you want to travel?";
    private string yesAnswer = "Yes";
    private string noAnswer = "No";
    private string groupSpawn = "";
    private float distance = 1.5f;

    [MenuItem("PopBloop/Items/Portal Item")]
    static void ExecuteInteractionItem()
    {
        GameObject[] objs = SAItemsMenu.GetSelections();
        if (objs == null)
        {
            return;
        }

        window = EditorWindow.GetWindow<SAItemPortal>("PopBloop");
        window.Initialize();
    }

    void Initialize()
    {
        GameObject[] objs = SAItemsMenu.GetSelections();
        if (objs == null)
        {
            return;
        }

        foreach (GameObject go in objs)
        {
            if (go.GetComponent<ItemPortal>() != null)
            {
                ItemPortal portal = go.GetComponent<ItemPortal>();
                worldName = portal.WorldName;
                questionText = portal.QuestionText;
                yesAnswer = portal.YesAnswer;
                noAnswer = portal.NoAnswer;
                distance = portal.Distance;
                groupSpawn = portal.SpawnGroupName;
                break;
            }
        }
    }

    void OnGUI()
    {
        GUILayout.Label("Portal Item", EditorStyles.boldLabel);

        worldName = EditorGUILayout.TextField(new GUIContent("World Name", "The Target Level Name"), worldName);
        questionText = EditorGUILayout.TextField(new GUIContent("Question Text", "The question in the dialog"), questionText);

        yesAnswer = EditorGUILayout.TextField("Yes Answer Text", yesAnswer);
        noAnswer = EditorGUILayout.TextField("No Answer Text", noAnswer);

        groupSpawn = EditorGUILayout.TextField(new GUIContent("Target Spawn Group", "Group name where the player will be spawned on target level"), groupSpawn);
        distance = EditorGUILayout.FloatField("Interaction Distance", distance);

        if (GUILayout.Button("Create"))
        {
            CreateObjects();
        }
    }

    void CreateObjects()
    {
        GameObject[] objs = SAItemsMenu.GetSelections();
        if (objs == null)
        {
            return;
        }

        if (worldName.Trim() == "")
        {
            EditorUtility.DisplayDialog("Error", "World Name must be set!!", "OK");
            return;
        }

        foreach (GameObject go in objs)
        {
            ItemPortal portal = go.GetComponent<ItemPortal>();
            if (portal == null)
            {
                portal = go.AddComponent<ItemPortal>();
            }
            portal.tag = LevelConstants.TagItem;

            portal.WorldName = worldName;
            portal.QuestionText = questionText;
            portal.YesAnswer = yesAnswer;
            portal.NoAnswer = noAnswer;
            portal.SpawnGroupName = groupSpawn;
            portal.Distance = distance;
        }
        
        window.Close();
    }
}
