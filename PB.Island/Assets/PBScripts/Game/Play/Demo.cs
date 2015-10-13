using UnityEngine;
using System.Collections;

using PB.Common;
using PB.Client;

public class Demo : MonoBehaviour 
{
	
	private Level _level;
	
	// Use this for initialization
	IEnumerator Start () 
    {
		
		
		//string levelPath = PopBloopSettings.AssetBundleBaseURL + "levels/level";
        string levelPath = PopBloopSettings.LevelAssetsUrl;
		
		// Download the level data
		yield return AssetsManager.DownloadAssetBundle(levelPath);
		
		try
		{
			// Deserialize the level data
			Level level = Level.Deserialize(AssetsManager.Bundles[levelPath].bytes);
			
			if (level != null)
			{
				// Load the level
				StartCoroutine(LoadLevel(level));
			}
		}
		catch (System.Exception ex)
		{
			Debug.Log(ex.ToString());
		}
	}
	
	// Update is called once per frame
	void Update () {
	
	}
	
	
	
	IEnumerator LoadLevel(Level level)
	{
        string path = PopBloopSettings.LevelAssetsUrl + "levels/";
		
		// First, we download all the entities, don't instantiate it yet
		foreach (Entity entity in level.Entities)
		{
			string bundle = path + entity.ObjectName;
			yield return AssetsManager.DownloadAssetBundle(bundle);
		}
		
		// Now, we have all the entities downloaded, instantiate them all
		foreach (Entity entity in level.Entities)
		{
			string bundle = path + entity.ObjectName;
			GameObject go = (GameObject)AssetsManager.Instantiate(AssetsManager.Bundles[bundle].assetBundle.mainAsset);
			
			if (go != null)
			{
				float3 pos = entity.Position;
				float3 rot = entity.Rotation;
				go.transform.position = new Vector3(pos.x, pos.y, pos.z);
				go.transform.rotation = Quaternion.Euler(new Vector3(rot.x, rot.y, rot.z));
			}
			else
			{
				Debug.Log("Instantiating " + bundle + " failed");
			}
		}
	}
}
