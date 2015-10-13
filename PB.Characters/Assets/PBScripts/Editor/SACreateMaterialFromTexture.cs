using UnityEngine;
using UnityEditor;
using System.Collections;
using System.Collections.Generic;

public class SACreateMaterialFromTexture
{

    [MenuItem("PopBloop/Assets/5. Generate Material Assets From Textures")]
    static void Execute()
    {
        foreach (Object obj in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
        {
            if (obj.name.Contains("@")) continue;
            if (!(obj is Texture2D)) continue;

            Texture2D tex = (Texture2D)obj;

            if (tex.name.ToLower().Contains("normal")) continue;
            if (!tex.name.ToLower().Contains(obj.name.ToLower())) continue;

            string texturePath = AssetRootPath(tex) + tex.name.ToLower() + ".mat";
            
             
            if (System.IO.File.Exists(texturePath))
            {
                System.IO.File.Delete(texturePath);
            }

            Debug.Log("Generating Material Asset : " + texturePath);

            string shader = "Transparent/Cutout/Diffuse";
            Material material = new Material(Shader.Find(shader));
            material.SetTexture("_MainTex", tex);

            AssetDatabase.CreateAsset(material, texturePath); //"Assets/Characters/male/Materials/"+tex.name.ToLower()+".mat");

            //AssetDatabase.Refresh();

            Debug.Log("Generate Material Asset Done.");
        }
    }

    public static string AssetRootPath(Texture2D obj)
    {
        string path = System.IO.Path.GetDirectoryName(AssetDatabase.GetAssetPath(obj));
        path = path.Substring(0, path.LastIndexOf("/") + 1) + "materials/";
        
        if (System.IO.Directory.Exists(path) == false)
        {
            System.IO.Directory.CreateDirectory(path);
        }

        return path;
    }
}
