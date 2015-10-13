using UnityEngine;
using UnityEditor;
using System.Collections;
using System.Collections.Generic;
using System.IO;
using PB.Client;

class SACreateBaseCharacterBundles {

    [MenuItem("PopBloop/Assets/1. Create Base Character Bundles")]
	static void Execute()	
	{
        CreateBaseCharacter(BuildTarget.WebPlayer);
	}

    [MenuItem("PopBloop/Assets/Android/1. Create Base Character Bundles")]
    static void ExecuteAndroid()
    {
        CreateBaseCharacter(BuildTarget.Android);
    }

    [MenuItem("PopBloop/Assets/iOS/1. Create Base Character Bundles")]
    static void ExecuteiOS()
    {
        CreateBaseCharacter(BuildTarget.iPhone);
    }

    static void CreateBaseCharacter(BuildTarget target)
    {
        foreach (Object obj in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
        {
            // If the object is not GameObject, then skip this object
            if ((obj is GameObject) == false)
            {
                continue;
            }

            // If the object is of animation prefab type, then skip this object
            if (obj.name.Contains("@"))
            {
                continue;
            }

            if (obj is Texture2D) continue;

            string name = obj.name.ToLower();

            GameObject go = (GameObject)obj;

            GenerateBaseCharacter(go, name, target);
        }
    }
	
	static void GenerateBaseCharacter(GameObject obj, string name, BuildTarget target)
	{
        //string path = EditorHelpers.ElementsAssetBundlePath + name + "_base.unity3d";

        string path = EditorUtility.SaveFilePanel("Save Character Bundle as", "", name + "_base", "unity3d");
        if (path == "" || path == null)
        {
            return;
        }

		Debug.Log("Generate Base Character for "+name);
		
		GameObject clone = (GameObject)AssetsManager.Instantiate(obj);
			
		foreach (SkinnedMeshRenderer smr in clone.GetComponentsInChildren<SkinnedMeshRenderer>(true))
		{
			Object.DestroyImmediate(smr.gameObject);
		}

        // Get the list of the animation clips name
        List<string> animations = new List<string>(); 
        foreach (AnimationState animState in clone.animation)
        {
            animations.Add(animState.clip.name);
        }

        // Remove the animation's clips
        foreach (string clip in animations)
        {
            clone.animation.RemoveClip(clip);
        }
		
		//clone.AddComponent<SkinnedMeshRenderer>();
			
		Object prefab = EditorHelpers.GetPrefab(clone, "base");
		
		BuildPipeline.BuildAssetBundle(prefab, null, path, BuildAssetBundleOptions.CollectDependencies, target);
		
		//AssetDatabase.DeleteAsset(AssetDatabase.GetAssetPath(prefab));
		
		Object.DestroyImmediate(clone);

        EditorUtility.DisplayDialog("PopBloop", "Character Base generation completed\n" + path, "OK");
	}

}
