using System.Collections;
using System;
using System.IO;
using System.Collections.Generic;
using System.Runtime.Serialization.Formatters.Binary;

using ProtoBuf;
using System.Globalization;

namespace PB.Common
{
    [ProtoContract]
    public class float3
    {
        [ProtoMember(1)]
        public float x { get; set; }

        [ProtoMember(2)]
        public float y { get; set; }

        [ProtoMember(3)]
        public float z { get; set; }

        public float3()
        {
        }

        public float3(float x, float y, float z)
        {
            this.x = x;
            this.y = y;
            this.z = z;
        }
    }

    [ProtoContract]
    public class float4
    {
        [ProtoMember(1)]
        public float x { get; set; }

        [ProtoMember(2)]
        public float y { get; set; }

        [ProtoMember(3)]
        public float z { get; set; }

        [ProtoMember(4)]
        public float w { get; set; }

        public float4()
        {}

        public float4(float x, float y, float z, float w)
        {
            this.x = x;
            this.y = y;
            this.z = z;
            this.w = w;
        }
    }

    [ProtoContract]
    public class Fog
    {
        [ProtoMember(1)]
        public bool active { get; set; }

        [ProtoMember(2)]
        public float4 color { get; set; }

        [ProtoMember(3)]
        public float density { get; set; }

        [ProtoMember(4)]
        public float startDistance { get; set; }

        [ProtoMember(5)]
        public float endDistance { get; set; }

        [ProtoMember(6)]
        public string fogMode { get; set; }

        public Fog()
        {
            color = new float4();
        }
    }

    [ProtoContract]
    public class LightmapDataInfo
    {
        [ProtoMember(1)]
        public string nearLightmap { get; set; }

        [ProtoMember(2)]
        public string farLightmap { get; set; }

        public LightmapDataInfo()
        {
        }

        public LightmapDataInfo(string nearMap, string farMap)
        {
            nearLightmap = nearMap;
            farLightmap = farMap;
        }
    }

    [ProtoContract]
    public class LightmapInfo
    {
        [ProtoMember(1)]
        public List<LightmapDataInfo> lightmaps { get; set; }

        [ProtoMember(2)]
        public string lightmapsMode { get; set; }

        public LightmapInfo()
        {
            lightmaps = new List<LightmapDataInfo>();
        }
    }

    [ProtoContract]
    public class Entity
    {
        [ProtoMember(1)]
        public string ObjectName { get; set; }

        [ProtoMember(2)]
        public float3 Position { get; set; }

        [ProtoMember(3)]
        public float3 Rotation { get; set; }

        [ProtoMember(4)]
        public float3 Scale { get; set; }

        [ProtoMember(5)]
        public string FilePath { get; set; }

        [ProtoMember(6)]
        public string Tag { get; set; }

        [ProtoMember(7)]
        public int LightmapIndex { get; set; }

        [ProtoMember(8)]
        public float4 LightmapTilingOffset { get; set; }
    }

    [ProtoContract]
    public class Level
    {
        [ProtoMember(1)]
        public List<Entity> Entities { get; set; }

        [ProtoMember(2)]
        public float[] WorldSize { get; set; }

        [ProtoMember(3)]
        public float[] InterestArea { get; set; }

        [ProtoMember(4)]
        public string Skybox { get; set; }

        [ProtoMember(5)]
        public string Audio { get; set; }

        [ProtoMember(6)]
        public Fog Fog { get; set; }

        [ProtoMember(7)]
        public LightmapInfo Lightmap { get; set; }

        [ProtoMember(8)]
        public string StartDateTime { get; set; }

        [ProtoMember(9)]
        public string Path { get; set; }

        public static void Serialize(string filepath, Level level)
        {
            using (var file = File.Create(filepath))
            {
                Serializer.Serialize(file, level);
                file.Flush();
            }
        }

        public static Level Deserialize(string filepath)
        {
            using (var file = File.Open(filepath, FileMode.Open))
            {
                Level level = Serializer.Deserialize<Level>(file);

                return level;
            }
        }

        public static Level Deserialize(byte[] buffer)
        {
            MemoryStream stream = new MemoryStream(buffer);
            stream.Seek(0, SeekOrigin.Begin);

            Level level = null;
            try
            {
                level = Serializer.Deserialize<Level>(stream);
            }
            catch (Exception ex)
            {
                throw ex;
            }

            return level;
        }
        
        public Level()
        {
            Entities = new List<Entity>();
            Fog = new Fog();
            Lightmap = new LightmapInfo();
            StartDateTime = DateTime.Now.ToString("d", new CultureInfo("en-US"));
        }
    }

}