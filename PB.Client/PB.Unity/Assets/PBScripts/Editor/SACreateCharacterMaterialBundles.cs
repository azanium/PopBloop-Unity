using UnityEngine;
using UnityEditor;
using System.IO;
using System.Collections;
using System.Collections.Generic;

public class SACreateCharacterMaterialBundles {

    [MenuItem("PopBloop/Assets/3. Create Character Material Bundles")]
	static void Execute()
	{
        string path = EditorUtility.SaveFolderPanel("Save Materials Bundles In", "", "");
        if (path == "" || path == null)
        {
            return;
        }
        path += "/";

        int count = 0;
		foreach (Object o in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
		{
			if (!(o is Material)) continue;
			
			string name = o.name.ToLower();
			
			Debug.Log("Create material bundle " + name);
			
			string assetPath = AssetDatabase.GetAssetPath(o);
			
			Object asset = AssetDatabase.LoadAssetAtPath(assetPath, typeof(Material));
			
			BuildPipeline.BuildAssetBundle(asset, null, Path.Combine(path, name + ".unity3d"));

            count++;
		}

        Debug.Log("Total material created is " + count.ToString());

        EditorUtility.DisplayDialog(PBConstants.APP_TITLE, "Material(s) creation completed\n" + path, "OK");
	}

    //[MenuItem("LiloCity/Assets/6. Create Texture Bundle")]
    //static void ExecuteSkybox()
    //{
    //    /*
    //    Dictionary<string, Texture2D> skyboxes = new Dictionary<string, Texture2D>();
    //    bool l = false;
    //    bool r = false;
    //    bool t = false;
    //    bool b = false;
    //    bool f = false;
    //    bool v = false;

    //    foreach (Object o in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
    //    {
    //        if (!(o is Texture2D)) continue;

    //        string name = o.name.ToLower();

    //        if (l == false && name.Contains("left"))
    //        {
    //            l = true;
    //            skyboxes.Add("left", o as Texture2D);
    //        }

    //        if (r == false && name.Contains("right"))
    //        {
    //            r = true;
    //            skyboxes.Add("right", o as Texture2D);
    //        }

    //        if (t == false && name.Contains("up"))
    //        {
    //            t = true;
    //            skyboxes.Add("up", o as Texture2D);
    //        }

    //        if (b == false && name.Contains("down"))
    //        {
    //            b = true;
    //            skyboxes.Add("down", o as Texture2D);
    //        }

    //        if (f == false && name.Contains("front"))
    //        {
    //            f = true;
    //            skyboxes.Add("front", o as Texture2D);
    //        }

    //        if (v == false && name.Contains("back"))
    //        {
    //            v = true;
    //            skyboxes.Add("back", o as Texture2D);
    //        }

    //    }

    //    if (skyboxes.Count < 6 || skyboxes.Count > 6)
    //    {
    //        EditorUtility.DisplayDialog("Warning", "Skyboxes count must be exactly 6 (contain name: front, back, left, right, up, down)", "OK");
    //        return;
    //    }

    //    */

    //    foreach (Object obj in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
    //    {
    //        if (obj.name.Contains("@")) continue;
    //        if (!(obj is Texture2D)) continue;

    //        Texture2D tex = (Texture2D)obj;

    //        if (tex.name.ToLower().Contains("normal")) continue;
    //        if (!tex.name.ToLower().Contains(obj.name.ToLower())) continue;

    //        string texturePath = AssetRootPath() + System.IO.Path.DirectorySeparatorChar + tex.name.ToLower() + ".mat";

    //        if (System.IO.File.Exists(texturePath))
    //        {
    //            System.IO.File.Delete(texturePath);
    //        }

    //        Debug.Log("Generating Material Asset : " + texturePath);

    //        string shader = "Transparent/Cutout/Diffuse";
    //        Material material = new Material(Shader.Find(shader));
    //        material.SetTexture("_MainTex", tex);

    //        AssetDatabase.CreateAsset(material, texturePath);

    //        AssetDatabase.Refresh();

    //        Debug.Log("Generate Material Asset Done.");
    //    }

    //    Material skyboxMat = new Material(Shader.Find("Diffuse"));//Shader.Find("RenderFX/Skybox"));
    //    skyboxMat.name = "Skybox";
    //    /*
    //    skyboxMat.SetTexture("_FrontTex", skyboxes["front"]);
    //    skyboxMat.SetTexture("_BackTex", skyboxes["back"]);
    //    skyboxMat.SetTexture("_LeftTex", skyboxes["left"]);
    //    skyboxMat.SetTexture("_RightTex", skyboxes["right"]);
    //    skyboxMat.SetTexture("_UpTex", skyboxes["up"]);
    //    skyboxMat.SetTexture("_DownTex", skyboxes["down"]);*/
    //    string texpath = AssetRootPath() + "/sky.mat";

    //    if (System.IO.File.Exists(texpath))
    //    {
    //        System.IO.File.Delete(texpath);
    //    }

    //    Debug.Log("Generating Material Asset : " + texpath);

    //    AssetDatabase.CreateAsset(skyboxMat, texpath);

    //    AssetDatabase.Refresh();
    //    /*
    //    foreach (KeyValuePair<string, Texture2D> texture in skyboxes)
    //    {
    //        string assetPath = AssetDatabase.GetAssetPath(texture.Value);

    //        Object asset = AssetDatabase.LoadAssetAtPath(assetPath, typeof(Texture2D));
    //        asset.name = texture.Key;
            
    //        objs.Add(asset);
    //    }

    //    string filename = EditorUtility.SaveFilePanel("Save Skybox Bundle As", EditorHelpers.MaterialsAssetBundlePath, "Skybox", "unity3d");

    //    BuildPipeline.BuildAssetBundle(null, objs.ToArray(), Path.Combine(EditorHelpers.MaterialsAssetBundlePath, filename));
    //    */


    //}

    //public static string AssetRootPath()
    //{
    //    string path = Application.dataPath;
    //    path = path.Substring(0, path.LastIndexOf("/") + 1) + "Materials";

    //    if (System.IO.Directory.Exists(path) == false)
    //    {
    //        System.IO.Directory.CreateDirectory(path);
    //    }

    //    return path;
    //}
}
