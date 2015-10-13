using UnityEditor;
using UnityEngine;
using System.IO;
using System.Collections.Generic;
using System.Runtime.Serialization;
using System.Collections;

using PB.Client;
using PB.Common;
using Ionic.Zip;

class ResourceExporter
{
    [MenuItem("PopBloop/World/Build AssetBundle From Selection - Track dependencies")]
    static void ExportResource()
    {

        // Bring up save panel
        string path = EditorUtility.SaveFilePanel("Save Resource", "", "New Resource", "unity3d");
        if (path.Length != 0)
        {
            // Build the resource file from the active selection.
            Object[] selection = Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets);

            Debug.Log(selection.Length);
            Debug.Log(selection[0]);
            Debug.Log(selection[0].name);
            BuildPipeline.BuildAssetBundle(Selection.activeObject, selection, path, BuildAssetBundleOptions.CollectDependencies | BuildAssetBundleOptions.CompleteAssets);
            Selection.objects = selection;
        }
    }

    [MenuItem("PopBloop/World/Build AssetBundle From Selection - No dependency tracking")]
    static void ExportPrefabResource()
    {
        // Bring up save panel
        string path = EditorUtility.SaveFilePanel("Save Resource", "", "New Resource", "unity3d");
        if (path.Length != 0)
        {
            // Build the resource file from the active selection.
            BuildPipeline.BuildAssetBundle(Selection.activeObject, Selection.objects, path);
        }
    }

    [MenuItem("PopBloop/World/Build Prefab AssetBundle from selection folder - Track dependencies")]
    static void ExportTextureResource()
    {
        // Bring up save panel
        string path = EditorUtility.SaveFolderPanel("Save Resource", Application.dataPath + "/Bundle", "GameObject");
        if (path.Length != 0)
        {
            path += "/";
            // Build the resource file from the active selection.
            Object[] selection = Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets);
            Material tempStorage;
            foreach (Object obj in selection)
            {
                if (obj.GetType() == typeof(GameObject))
                {
                    Debug.Log("Creating bundle for: " + obj.name);

                    //buffer the material
                    tempStorage = null;
                    if (((GameObject)obj).transform.GetChild(0).renderer != null)
                    {
                        tempStorage = ((GameObject)obj).transform.GetChild(0).renderer.sharedMaterial;

                        //remove all material
                        ((GameObject)obj).transform.GetChild(0).renderer.materials = new Material[0];
                    }

                    //create asset bundle
                    BuildPipeline.BuildAssetBundle(obj, new Object[] { obj }, path + obj.name + ".unity3d", BuildAssetBundleOptions.CollectDependencies | BuildAssetBundleOptions.CompleteAssets);

                    //reset the material from buffer
                    if (tempStorage != null)
                        ((GameObject)obj).transform.GetChild(0).renderer.material = tempStorage;
                }
            }
            Selection.objects = selection;
            Debug.Log("Creating bundle done");
        }
    }

    [MenuItem("PopBloop/World/Build Texture AssetBundle from selection folder - Track dependencies")]
    static void ExportResourceFolder()
    {
        // Bring up save panel
        string path = EditorUtility.SaveFolderPanel("Save Resource", Application.dataPath + "/Bundle", "Texture");
        if (path.Length != 0)
        {
            path += "/";
            // Build the resource file from the active selection.
            Object[] selection = Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets);
            foreach (Object obj in selection)
            {
                if (obj.GetType() == typeof(Texture2D))
                {
                    Debug.Log("Creating bundle for: " + obj.name + "("+obj+")");
                    //create asset bundle
                    BuildPipeline.BuildAssetBundle(obj, new Object[] { obj }, path + obj.name + ".unity3d", BuildAssetBundleOptions.CollectDependencies | BuildAssetBundleOptions.CompleteAssets);
                }
            }
			
            Selection.objects = selection;
            Debug.Log("Creating bundle done");
        }
    }
	
	
}