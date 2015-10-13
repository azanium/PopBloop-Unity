using UnityEngine;
using System.Collections;
using UnityEditor;
using PB.Common;
using System.Collections.Generic;

public class SAItemSpawn : EditorWindow
{
    private static SAItemSpawn window;

    private string spawnId = "Spawn";
    private string groupName = "Group";
    private bool autoNumberingName = true;
    private Vector2 scrollPosition;
    private GameObject[] existingObjects;
    private Dictionary<string, List<GameObject>> groupObjects;
    private bool useGroup = true;

    [MenuItem("PopBloop/Items/Spawn Point")]
    static void ExecuteInteractionItem()
    {
        window = EditorWindow.GetWindow<SAItemSpawn>("PopBloop");
        window.Initialize();
    }

    void Initialize()
    {
        groupObjects = new Dictionary<string, List<GameObject>>();

        existingObjects = GameObject.FindGameObjectsWithTag(LevelConstants.TagSpawnPoints);

        foreach (GameObject go in existingObjects)
        {
            ItemSpawn spawn = go.GetComponent<ItemSpawn>();
            if (groupObjects.ContainsKey(spawn.groupName) == false)
            {
                groupObjects.Add(spawn.groupName, new List<GameObject>() { go });
            }
            else
            {
                List<GameObject> groupChilds = groupObjects[spawn.groupName];
                groupChilds.Add(go);
            }
        }
    }

    void OnGUI()
    {
        GUILayout.Label("Spawn Point", EditorStyles.boldLabel);

        spawnId = EditorGUILayout.TextField("Spawn Id", spawnId);
        groupName = EditorGUILayout.TextField("Group Name", groupName);
        autoNumberingName = EditorGUILayout.Toggle("Auto Numbering Name", autoNumberingName);

        if (GUILayout.Button("Create"))
        {
            CreateObjects();
        }

        string description = useGroup ? "Existing Groups" : "Existing Spawn Ids";
        GUILayout.Label(description, EditorStyles.boldLabel);
        if (GUILayout.Button("Toggle display Group/Id"))
        {
            useGroup = !useGroup;
        }

        scrollPosition = GUILayout.BeginScrollView(scrollPosition);

        int index = 1;
        if (useGroup)
        {
            if (groupObjects == null)
            {
                Initialize();
            }

            foreach (string group in groupObjects.Keys)
            {
                if (GUILayout.Button(string.Format("{0}. {1}", index++, group)))
                {
                    List<GameObject> objects = groupObjects[group];
                    if (objects.Count > 0)
                    {
                        Selection.activeGameObject = objects[0];
                    }
                }
            }
        }
        else
        {
            if (existingObjects == null)
            {
                Initialize();
            }

            foreach (GameObject go in existingObjects)
            {
                if (GUILayout.Button(string.Format("{0}. {1}", index++, go.name)))
                {
                    Selection.activeGameObject = go;
                }
            }
        }

        GUILayout.EndScrollView();
    }

    void CreateObjects()
    {
        GameObject[] objs = SAItemsMenu.GetSelections();
        if (objs == null)
        {
            return;
        }

        GameObject[] objects = GameObject.FindGameObjectsWithTag(LevelConstants.TagSpawnPoints);

        int counter = 1;
        foreach (GameObject go in objs)
        {
            go.tag = LevelConstants.TagSpawnPoints;

            string currentName = spawnId + (autoNumberingName ? (counter ++).ToString() : "");

            if (objs.Length == 1)
            {
                currentName = spawnId;
            }

            if (IsNameExists(ref objects, currentName))
            {
                EditorUtility.DisplayDialog("Error!", string.Format("Spawn Id '{0}' is already exists, please change the name please", currentName), "OK");
                return;
            }
            else
            {
                go.name = currentName;
                ItemSpawn spawn = go.GetComponent<ItemSpawn>();
                if (spawn == null)
                {
                    spawn = go.AddComponent<ItemSpawn>();
                }

                spawn.spawnId = currentName;
                spawn.groupName = groupName;
            }
        }

        window.Close();
    }

    bool IsNameExists(ref GameObject[] objs, string name)
    {
        bool isExists = false;

        foreach (GameObject go in objs)
        {
            if (go.name == name)
            {
                isExists = true;
                break;
            }
        }

        return isExists;
    }

}
