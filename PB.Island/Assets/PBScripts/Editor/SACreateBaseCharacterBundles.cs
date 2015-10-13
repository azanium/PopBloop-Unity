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
			
			GenerateBaseCharacter(go, name, BuildTarget.WebPlayer);
		}
	}

    [MenuItem("PopBloop/Assets/Android/1. Create Base Character Bundles")]
    static void ExecuteAndroid()
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

            GenerateBaseCharacter(go, name, BuildTarget.Android);
            Debug.Log("Base character " + name + " was created");

        }
    }

    [MenuItem("PopBloop/Assets/iOS/1. Create Base Character Bundles")]
    static void ExecuteiOS()
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

            GenerateBaseCharacter(go, name, BuildTarget.iPhone);
        }
    }

	static void GenerateBaseCharacter(GameObject obj, string name, BuildTarget target)
	{
        name += "_base";
        //string path = EditorHelpers.ElementsAssetBundlePath + name + "_base.unity3d";
        /*if (target == BuildTarget.iPhone)
        {
            name += "_ios";
        }
        else if (target == BuildTarget.Android)
        {
            name += "_android";
        }*/

        string path = EditorUtility.SaveFilePanel("Save Character Bundle as", "", name, "unity3d");
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

        EditorUtility.DisplayDialog(PBConstants.APP_TITLE, "Character Base generation completed\n" + path, "OK");
	}

}
