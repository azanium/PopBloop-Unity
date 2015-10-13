using System;
using System.IO;
using System.Text;
using System.Collections.Generic;
using System.Collections;

using ProtoBuf;

namespace PB.Common
{
    public static class LevelBuilder
    {
        #region Methods

        public static bool SaveLevelAsINI(string pathName, Level level)
        {
            bool result = false;

            if (level.Entities.Count > 0)
            {
                using (StreamWriter writer = new StreamWriter(pathName))
                {
                    // Save the Render settings
                    writer.WriteLine("[rendersettings]");
                    writer.WriteLine(string.Format("{0}={1}", "fogActive", level.Fog.active));
                    writer.WriteLine(string.Format("{0}={1},{2},{3},{4}", "fogColor", level.Fog.color.x, level.Fog.color.y, level.Fog.color.z, level.Fog.color.w));
                    writer.WriteLine(string.Format("{0}={1}", "fogDensity", level.Fog.density));
                    writer.WriteLine(string.Format("{0}={1}", "fogStartDistance", level.Fog.startDistance));
                    writer.WriteLine(string.Format("{0}={1}", "fogEndDistance", level.Fog.endDistance));
                    writer.WriteLine(string.Format("{0}=\"{1}\"", "fogMode", level.Fog.fogMode));
                    writer.WriteLine();

                    writer.WriteLine("[lightmaps]");
                    writer.WriteLine(string.Format("lightmapsCount={0}", level.Lightmap.lightmaps.Count));
                    writer.WriteLine(string.Format("lightmapsMode=\"{0}\"", level.Lightmap.lightmapsMode));
                    int counter = 0;
                    foreach (LightmapDataInfo lmData in level.Lightmap.lightmaps)
                    {
                        writer.WriteLine(string.Format("near_{0}=\"{1}\"", counter, lmData.nearLightmap));
                        writer.WriteLine(string.Format("far_{0}=\"{1}\"", counter++, lmData.farLightmap));
                    }
                    writer.WriteLine();

                    // Save the entities
                    counter = 0;
                    foreach (Entity entity in level.Entities)
                    {
                        counter ++;
                        writer.WriteLine("[" + counter + "]");
                        writer.WriteLine("objectName=\"" + entity.ObjectName + "\"");
                        writer.WriteLine(string.Format("position={0},{1},{2}", entity.Position.x, entity.Position.y, entity.Position.z));
                        writer.WriteLine(string.Format("rotation={0},{1},{2}", entity.Rotation.x, entity.Rotation.y, entity.Rotation.z));
                        writer.WriteLine(string.Format("tag=\"{0}\"", entity.Tag));
                        writer.WriteLine(string.Format("lightmapIndex={0}", entity.LightmapIndex));
                        writer.WriteLine(string.Format("lightmapTilingOffset={0},{1},{2},{3}", entity.LightmapTilingOffset.x, entity.LightmapTilingOffset.y, entity.LightmapTilingOffset.z, 
                            entity.LightmapTilingOffset.w));
                        writer.WriteLine();
                    }

                    writer.Flush();
                    writer.Close();
                }
            }

            return result;
        }

        public static bool SaveNPCInfoAsINI(string pathName, Level level)
        {
            bool result = false;

            if (level.Entities.Count > 0)
            {
                using (StreamWriter writer = new StreamWriter(pathName))
                {
                    int counter = 0;
                    foreach (Entity entity in level.Entities)
                    {
                        if (entity.Tag == LevelConstants.TagNPC)
                        {
                            counter++;
                            writer.WriteLine("[" + counter + "]");

                            writer.WriteLine(string.Format("name={0}", entity.ObjectName));
                        }
                    }
                    writer.Flush();
                    writer.Close();
                }
            }

            return result;
        }

        public static byte[] Serialize(Level level)
        {
            MemoryStream stream = new MemoryStream();

            Serializer.Serialize<Level>(stream, level);
                
            stream.Seek(0, SeekOrigin.Begin);

            return stream.ToArray();
        }

        public static Level Deserialize(byte[] data)
        {
            MemoryStream stream = new MemoryStream(data);
            stream.Seek(0, SeekOrigin.Begin);

            Level level = null;
            try
            {
                level = Serializer.Deserialize<Level>(stream);
            }
            catch (Exception)
            {
                level = null;
            }

            return level;
        }

        #endregion
    }
}
