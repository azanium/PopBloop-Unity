using UnityEngine;
using UnityEditor;
using System.Collections;
using System.Collections.Generic;
using System.IO;

public class SAGenerateSkybox
{

    [MenuItem("PopBloop/Assets/6. Generate Skybox")]
    static void Execute()
    {
        string filename = EditorUtility.SaveFilePanel("Save Skybox Bundle As", EditorHelpers.MaterialsAssetBundlePath, "Skybox", "unity3d");
        

        Dictionary<string, Texture2D> skyboxes = new Dictionary<string, Texture2D>();
        bool l = false;
        bool r = false;
        bool t = false;
        bool b = false;
        bool f = false;
        bool v = false;
        string matPath = "";
        foreach (Object o in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
        {
            if (o.name.Contains("@")) continue;
            if (!(o is Texture2D)) continue;

            Texture2D tex = (Texture2D)o;

            if (tex.name.ToLower().Contains("normal")) continue;
            if (!tex.name.ToLower().Contains(o.name.ToLower())) continue;

            matPath = AssetRootPath(tex) + "/" + Path.GetFileNameWithoutExtension(filename) + ".mat";

            string name = tex.name.ToLower();

            if (l == false && name.Contains("left"))
            {
                l = true;
                skyboxes.Add("left", o as Texture2D);
            }

            if (r == false && name.Contains("right"))
            {
                r = true;
                skyboxes.Add("right", o as Texture2D);
            }

            if (t == false && name.Contains("up"))
            {
                t = true;
                skyboxes.Add("up", o as Texture2D);
            }

            if (b == false && name.Contains("down"))
            {
                b = true;
                skyboxes.Add("down", o as Texture2D);
            }

            if (f == false && name.Contains("front"))
            {
                f = true;
                skyboxes.Add("front", o as Texture2D);
            }

            if (v == false && name.Contains("back"))
            {
                v = true;
                skyboxes.Add("back", o as Texture2D);
            }
        }

        if (skyboxes.Count < 6 || skyboxes.Count > 6)
        {
            EditorUtility.DisplayDialog("Warning", "Skyboxes count must be exactly 6 (contain name: front, back, left, right, up, down)", "OK");
            return;
        }


        if (System.IO.File.Exists(matPath))
        {
            System.IO.File.Delete(matPath);
        }
        
        Debug.Log("Generating Skybox Material Asset : " + matPath);

        string shader = "RenderFX/Skybox";
        Material skyboxMat = new Material(Shader.Find(shader));

        skyboxMat.SetTexture("_FrontTex", skyboxes["front"]);
        skyboxMat.SetTexture("_BackTex", skyboxes["back"]);
        skyboxMat.SetTexture("_LeftTex", skyboxes["left"]);
        skyboxMat.SetTexture("_RightTex", skyboxes["right"]);
        skyboxMat.SetTexture("_UpTex", skyboxes["up"]);
        skyboxMat.SetTexture("_DownTex", skyboxes["down"]);

        AssetDatabase.CreateAsset(skyboxMat, matPath);

        AssetDatabase.Refresh();        

        BuildPipeline.BuildAssetBundle(skyboxMat, null, filename);

        Debug.Log("Generate Skybox Done.");

    }

    public static string AssetRootPath(Texture2D obj)
    {
        string path = AssetDatabase.GetAssetPath(obj); 
        path = path.Substring(0, path.LastIndexOf("/") + 1);
     
        if (System.IO.Directory.Exists(path) == false)
        {
            System.IO.Directory.CreateDirectory(path);
        }

        return path;
    }
}
