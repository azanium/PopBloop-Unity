using UnityEngine;
using UnityEditor;
using System.Collections;
using System.Collections.Generic;

public class SACreateCharacterMaterialAssets {

    [MenuItem("PopBloop/Assets/0. Create Character Material Assets")]
	static void Execute()
	{
		foreach (Object obj in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
		{
			if (!(obj is GameObject)) continue;
			if (obj.name.Contains("@")) continue;
			
			GameObject go = (GameObject)obj;
			
			List<Texture2D> textures = EditorHelpers.GetAllAssets<Texture2D>(AssetRootPath(go) + "textures");
			
			foreach (SkinnedMeshRenderer smr in go.GetComponentsInChildren<SkinnedMeshRenderer>(true))
			{
				foreach (Texture2D tex in textures)
				{
					if (tex.name.ToLower().Contains("normal")) continue;
					if (!tex.name.ToLower().Contains(smr.name.ToLower())) continue;
					
					string materialsPath = MaterialsPath(go) + System.IO.Path.DirectorySeparatorChar + tex.name.ToLower() + ".mat";
					
					if (System.IO.File.Exists(materialsPath))
					{
						continue;
					}
					
					Debug.Log("Generating Material Asset : " + materialsPath);
					
					string shader = "Transparent/Cutout/Diffuse";
					Material material = new Material(Shader.Find(shader));
					material.SetTexture("_MainTex", tex);
					
					AssetDatabase.CreateAsset(material, materialsPath);
				}
			}
			AssetDatabase.Refresh();
		}
	}
	
	public static string AssetRootPath(GameObject obj)
	{
		string path = AssetDatabase.GetAssetPath(obj);
		return path.Substring(0, path.LastIndexOf("/") + 1);
	}
	
    public static string MaterialsPath(GameObject obj)
	{
		string matPath = AssetRootPath(obj) + "materials";
		if (System.IO.Directory.Exists(matPath) == false)
		{
			System.IO.Directory.CreateDirectory(matPath);
		}
		return matPath;
	}
}
