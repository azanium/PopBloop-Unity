using UnityEngine;
using UnityEditor;
using System.IO;
using System.Collections;

public class SACreateAnimationBundles {

    [MenuItem("PopBloop/Assets/4. Create Animation Bundles")]
	public static void Execute()
	{
        //string path = EditorHelpers.AnimationsAssetBundlePath;		
        string path = EditorUtility.SaveFolderPanel("Save Animation Bundles In", "", "");
        if (path == "" || path == null)
        {
            return;
        }
        path += "/";

		foreach (Object o in Selection.GetFiltered(typeof(Object), SelectionMode.DeepAssets))
		{
			if (!(o is GameObject)) continue;
            if (o.name.ToLower().Contains("@") == true) continue;
			
			GameObject go = (GameObject)o;

			string name = o.name.ToLower();
			
			foreach (AnimationState state in go.animation)
			{
				string animPath = path + name + "@" + state.name.ToLower();
				string animFile = animPath + ".unity3d";
								
				if (File.Exists(animFile))
				{
					File.Delete(animFile);
				}
		
				Debug.Log("Create Animation Clip Bundle '" + state.name.ToLower() + "' at: " + animFile);

				BuildPipeline.BuildAssetBundle((Object)state.clip, null, animFile, BuildAssetBundleOptions.CollectDependencies);
			}
		}

        EditorUtility.DisplayDialog("PopBloop", "Animation bundles generation completed\n" + path, "OK");

	}
}
