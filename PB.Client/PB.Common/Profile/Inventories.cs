using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

using ProtoBuf;


namespace PB.Common
{
    [ProtoContract]
    public class Inventories
    {
        [ProtoMember(1)]
        public List<GameItem> Items { get; set; }

        public Inventories()
        {
            Items = new List<GameItem>();
        }

        public static void Serialize(string filepath, Inventories inventory)
        {
            using (var file = File.Create(filepath))
            {
                Serializer.Serialize(file, inventory);
                file.Flush();
            }
        }

        public static Inventories Deserialize(string filepath)
        {
            using (var file = File.Open(filepath, FileMode.Open))
            {
                Inventories inventories = Serializer.Deserialize<Inventories>(file);

                return inventories;
            }
        }

        public static byte[] Serialize(Inventories inventories)
        {
            MemoryStream stream = new MemoryStream();

            Serializer.Serialize<Inventories>(stream, inventories);

            stream.Seek(0, SeekOrigin.Begin);

            return stream.ToArray();
        }

        public static Inventories Deserialize(byte[] buffer)
        {
            MemoryStream stream = new MemoryStream(buffer);
            stream.Seek(0, SeekOrigin.Begin);

            Inventories inventories = null;
            try
            {
                inventories = Serializer.Deserialize<Inventories>(stream);
            }
            catch (Exception ex)
            {
                throw ex;
            }

            return inventories;
        }
    }
}
