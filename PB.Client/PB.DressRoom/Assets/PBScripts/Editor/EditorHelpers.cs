using UnityEngine;
using UnityEditor;
using System.IO;
using System.Collections;
using System.Collections.Generic;

class EditorHelpers {
	
	public static Object GetResourcesPrefab(GameObject go, string name)
	{
		Object tmpPrefab = PrefabUtility.CreateEmptyPrefab(PrefabsPath + name + ".prefab");
		tmpPrefab = PrefabUtility.ReplacePrefab(go, tmpPrefab);
		
		Object.DestroyImmediate(go);
		
		return tmpPrefab;
	}
	
	public static Object GetPrefab(GameObject go, string name)
	{
		Object tmpPrefab = PrefabUtility.CreateEmptyPrefab(TempPath + name + ".prefab");
        tmpPrefab = PrefabUtility.ReplacePrefab(go, tmpPrefab);
		
		//Object.DestroyImmediate(go);
		
		return tmpPrefab;
	}
	
	public static string TempPath
	{
		get
		{
			string path = "Assets/Resources/Temp/";
			
			if (Directory.Exists(path) == false)
			{
				Directory.CreateDirectory(path);
			}
			
			return path;
		}
	}
	
	public static string PrefabsPath
	{
		get
		{
			string path = "Assets/Resources/Prefabs/" + Path.DirectorySeparatorChar;
			if (Directory.Exists(path) == false)
			{
				Directory.CreateDirectory(path);
			}
			
			return path;
		}
	}
	
	public static string AssetBundlePath
	{
		get
		{
			string path = "bundles" + Path.DirectorySeparatorChar;
			if (Directory.Exists(path) == false)
			{
				Directory.CreateDirectory(path);
			}
			
			return path;
		}
	}
	
	public static string ElementsAssetBundlePath
	{
		get
		{
			string path = AssetBundlePath + "characters/";
			if (Directory.Exists(path) == false)
			{
				Directory.CreateDirectory(path);
			}
			
			return  path;
		}
	}
	
	public static string AnimationsAssetBundlePath
	{
		get 
		{
			string path = AssetBundlePath + "animations/";
			if (Directory.Exists(path) == false)
			{
				Directory.CreateDirectory(path);
			}
			
			return path;
		}
	}
	
	public static string MaterialsAssetBundlePath
	{
		get
		{
			string path = AssetBundlePath + "materials/";
			if (Directory.Exists(path) == false)
			{
				Directory.CreateDirectory(path);
			}
			
			return path;
		}
	}
	
	public static List<T> GetAllAssets<T>(string path) where T : Object
	{
		List<T> assets = new List<T>();
		
		string[] files = Directory.GetFiles(path);
		
		foreach (string file in files)
		{
			if (file.Contains(".meta")) 
			{
				continue;
			}
			
			T asset = (T)AssetDatabase.LoadAssetAtPath(file, typeof(T));
			
			if (asset == null)
			{
				Debug.LogError("Asset is not " + typeof(T) + " type");
			}
			else
			{
				assets.Add(asset);
			}
		}
	
		return assets;
	}
}
