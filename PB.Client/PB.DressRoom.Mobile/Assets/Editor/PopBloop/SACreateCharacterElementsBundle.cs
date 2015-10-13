using System.IO;
using System.Collections;
using System.Collections.Generic;

using UnityEngine;
using UnityEditor;

using PB.Client;
using PB.Game;

class SACreateCharacterElementBundles
{
    [MenuItem("PopBloop/Assets/2. Create Character Elements Bundles")]
	static void Execute() 
    {
        CreateCharacterElementBundles(BuildTarget.WebPlayer);
	}

    [MenuItem("PopBloop/Assets/Android/2. Create Character Elements Bundles")]
    static void ExecuteAndroid()
    {
        CreateCharacterElementBundles(BuildTarget.Android);
    }

    [MenuItem("PopBloop/Assets/iOS/2. Create Character Elements Bundles")]
    static void ExecuteiOS()
    {
        CreateCharacterElementBundles(BuildTarget.iPhone);
    }

    static void CreateCharacterElementBundles(BuildTarget target)
    {
        string path = EditorUtility.SaveFolderPanel("Save Character Element Bundles In", "", "");
        if (path == "" || path == null)
        {
            return;
        }
        path += "/";

        foreach (Object obj in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
        {
            if (!(obj is GameObject))
            {
                continue;
            }

            if (obj.name.Contains("@"))
            {
                continue;
            }

            GameObject go = (GameObject)obj;
            string name = obj.name.ToLower();

            // Delete existing assetbundles for current character.
            /*string[] existingAssetbundles = Directory.GetFiles(path);//EditorHelpers.ElementsAssetBundlePath);
            foreach (string bundle in existingAssetbundles)
            {
                if (bundle.EndsWith(".unity3d") && bundle.Contains(name))
                    File.Delete(bundle);
            }*/

            List<Material> materials = EditorHelpers.GetAllAssets<Material>(SACreateCharacterMaterialAssets.MaterialsPath(go));

            foreach (SkinnedMeshRenderer smr in go.GetComponentsInChildren<SkinnedMeshRenderer>(true))
            {
                List<Object> assets = new List<Object>();

                // Create a clone of the skinned mesh renderer
                GameObject clone = (GameObject)PrefabUtility.InstantiatePrefab(smr.gameObject);

                // Destroy the parent, since we only need the skinned mesh renderer not the whole thing
                GameObject parent = clone.transform.parent.gameObject;
                clone.transform.parent = null;
                GameObject.DestroyImmediate(parent);

                // Generate prefab				
                Object prefab = EditorHelpers.GetPrefab(clone, "mesh");
                assets.Add(prefab);

                // Delete the clone
                GameObject.DestroyImmediate(clone);

                foreach (Material mat in materials)
                {
                    if (mat.name.ToLower().Contains(smr.name.ToLower()))
                    {
                        assets.Add(mat);
                    }
                }

                // Get the bone names
                List<string> bonenames = new List<string>();
                foreach (Transform bone in smr.bones)
                {
                    bonenames.Add(bone.name);
                }
                string boneNamesAssetPath = "Assets/bonenames.asset";

                // Create bone names asset then add it to the assets list
                StringHolder holder = ScriptableObject.CreateInstance<StringHolder>();
                holder.content = bonenames.ToArray();
                AssetDatabase.CreateAsset(holder, boneNamesAssetPath);
                assets.Add(AssetDatabase.LoadAssetAtPath(boneNamesAssetPath, typeof(StringHolder)));

                // the path format is : gender_elementname.unity3d
                string filepath = path + name + "_" + smr.name.ToLower() + ".unity3d";

                // Build the assets containing : mesh, bonenames
                BuildPipeline.BuildAssetBundle(null, assets.ToArray(), filepath, BuildAssetBundleOptions.CollectDependencies, target);

                Debug.Log("Element " + smr.name.ToLower() + " is created at " + filepath);

                Debug.Log("Saved " + name + " with " + (assets.Count - 2) + " materials");

                AssetDatabase.DeleteAsset(AssetDatabase.GetAssetPath(prefab));
                AssetDatabase.DeleteAsset(boneNamesAssetPath);
            }
        }

        EditorUtility.DisplayDialog("PopBloop", "Character's element(s) creation completed\n" + path, "OK");
    }
}
