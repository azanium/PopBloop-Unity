using System.IO;
using UnityEngine;
using UnityEditor;
using System.Collections;

using PB.Client;
using PB.Common;
using Ionic.Zip;

public class SALevelExporter {

    [MenuItem("PopBloop/World/Create Level bundles on Scene")]
    static void ExportScene()
    {

        string path = EditorHelpers.TempPath;
        string targetPath = EditorUtility.SaveFilePanel("Save Level As", "", "Level.zip", "*.zip");
        string targetFile = Path.GetFileNameWithoutExtension(Path.GetFileName(targetPath));

        if (targetPath == "" || targetPath == null)
        {
            return; 
        }

        string targetDir = Path.GetDirectoryName(targetPath);

        string zipTarget = Path.Combine(targetDir, Path.ChangeExtension(targetFile, ".zip"));
        string skyboxTarget = Path.Combine(targetDir, Path.ChangeExtension(targetFile + "_skybox", ".unity3d"));

        Debug.Log("zipTarget: " + zipTarget + " => targetFile: " + targetFile + " => targetDir: " + targetDir + " => skybox: " + skyboxTarget);


        // Export skybox
        if (RenderSettings.skybox != null)
        {
            string skyboxName = skyboxTarget.ToLower();

            Debug.Log("Create Skybox bundle: " + skyboxName);

            string assetPath = AssetDatabase.GetAssetPath(RenderSettings.skybox);

            Object asset = AssetDatabase.LoadAssetAtPath(assetPath, typeof(Material));

            BuildPipeline.BuildAssetBundle(asset, null, skyboxTarget);
        }

        if (path.Length != 0)
        {
            //path += "/";

            GameObject[] objs = (GameObject[])GameObject.FindObjectsOfType(typeof(GameObject));//Selection.gameObjects;
            Debug.Log("Object count to serialize " + objs.Length);
            Level level = new Level();

            foreach (GameObject obj in objs)
            {
                if (obj.transform.parent != null) continue;

                Vector3 pos = obj.transform.position;
                Vector3 rot = obj.transform.rotation.eulerAngles;

                string name = obj.name.ToLower().Replace(" ", "_");

                Debug.Log("Creating bundle for: " + name);

                Entity entity = new Entity();
                entity.ObjectName = name;
                entity.Position = new float3(pos.x, pos.y, pos.z);
                entity.Rotation = new float3(rot.x, rot.y, rot.z);
                entity.Tag = obj.tag;
                entity.LightmapIndex = obj.renderer != null ? obj.renderer.lightmapIndex : -1;
                entity.LightmapTilingOffset = obj.renderer != null ? new float4(obj.renderer.lightmapTilingOffset.x, obj.renderer.lightmapTilingOffset.y, obj.renderer.lightmapTilingOffset.z, obj.renderer.lightmapTilingOffset.w) : new float4(0f, 0f, 0f, 0f);
                level.Entities.Add(entity);

                if (obj.tag == LevelConstants.TagItem)
                {
                    ItemBase item = obj.GetComponent<ItemBase>();
                    item.drawGizmo = false;
                }

                if (obj.tag == LevelConstants.TagNPC)
                {
                    ItemNPC npc = obj.GetComponent<ItemNPC>();
                    npc.drawGizmo = false;
                }

                if (obj.tag == LevelConstants.TagNPC)
                {
                    GameObject root = PrefabUtility.FindPrefabRoot(obj);

                    Object prefab = EditorHelpers.GetPrefab(root, root.name.ToLower());

                    BuildPipeline.BuildAssetBundle(prefab, null, path + root.name.ToLower() + ".unity3d", BuildAssetBundleOptions.CollectDependencies);

                    AssetDatabase.DeleteAsset(AssetDatabase.GetAssetPath(prefab));

                }
                else if (obj.GetType() != typeof(Camera))
                {

                    Object prefab = EditorHelpers.GetPrefab(obj, name);

                    BuildPipeline.BuildAssetBundle(prefab, null, path + name + ".unity3d", BuildAssetBundleOptions.CollectDependencies | BuildAssetBundleOptions.CompleteAssets);

                    AssetDatabase.DeleteAsset(AssetDatabase.GetAssetPath(prefab));
                }
            }

            // Encode RenderSettings to the level
            level.Fog.active = RenderSettings.fog;
            level.Fog.color = new float4(RenderSettings.fogColor.r, RenderSettings.fogColor.g, RenderSettings.fogColor.b, RenderSettings.fogColor.a);
            level.Fog.density = RenderSettings.fogDensity;
            level.Fog.startDistance = RenderSettings.fogStartDistance;
            level.Fog.endDistance = RenderSettings.fogEndDistance;
            level.Fog.fogMode = RenderSettings.fogMode.ToString();

            // Encode LightmapSettings to the level
            level.Lightmap.lightmapsMode = LightmapSettings.lightmapsMode.ToString();
            int counter = 0;
            foreach (LightmapData lmData in LightmapSettings.lightmaps)
            {
                string near = lmData.lightmapNear != null ? lmData.lightmapNear.name.ToLower() : "";
                string far = lmData.lightmapFar != null ? lmData.lightmapFar.name.ToLower() : "";

                LightmapDataInfo lmInfo = new LightmapDataInfo(near, far);
                level.Lightmap.lightmaps.Add(lmInfo);

                if (near != "")
                {
                    Debug.Log("Exporting Near Lightmap " + counter.ToString() + " : '" + near + "'");
                    string assetPath = AssetDatabase.GetAssetPath(lmData.lightmapNear);
                    Object asset = AssetDatabase.LoadAssetAtPath(assetPath, typeof(Texture2D));
                    BuildPipeline.BuildAssetBundle(asset, null, path + near + ".unity3d", BuildAssetBundleOptions.CollectDependencies | BuildAssetBundleOptions.CompleteAssets);
                }

                if (far != "")
                {
                    Debug.Log("Exporting Far Lightmap " + counter.ToString() + " : '" + far + "'"); 
                    string assetPath = AssetDatabase.GetAssetPath(lmData.lightmapFar);
                    Object asset = AssetDatabase.LoadAssetAtPath(assetPath, typeof(Texture2D));
                    BuildPipeline.BuildAssetBundle(asset, null, path + far + ".unity3d", BuildAssetBundleOptions.CollectDependencies | BuildAssetBundleOptions.CompleteAssets);
                }

                counter++;
            }

            string levelName = path + "level.ini";

            if (File.Exists(levelName))
            {
                File.Delete(levelName);
            }

            Debug.Log("Serializing Level : " + levelName);
            LevelBuilder.SaveLevelAsINI(levelName, level);

            string npcName = path + "npc.ini";
            if (File.Exists(npcName))
            {
                File.Delete(npcName);
            }
            Debug.Log("Serializing NPC : " + npcName);
            LevelBuilder.SaveNPCInfoAsINI(npcName, level);

            string[] files = Directory.GetFiles(path);

            // Zip our bundles
            using (ZipFile zip = new ZipFile())
            {
                foreach (string file in files)
                {
                    zip.AddFile(file, "");
                }

                // Save our zip file
                zip.Save(zipTarget);
            }

            // Remove temporary bundles
            foreach (string file in files)
            {
                File.Delete(file);
            }


            Debug.Log("Creating bundle done");

            string skyInfo = "";
            if (RenderSettings.skybox != null)
            {
                skyInfo = "\n" + skyboxTarget;
            }

            EditorUtility.DisplayDialog(PBConstants.APP_TITLE, "Level creation complete\n" + zipTarget + skyInfo, "OK");
            Debug.Log("Finish create Level: " + zipTarget);
        }
    }
}
