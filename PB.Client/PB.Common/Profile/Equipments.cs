using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

using ProtoBuf;

namespace PB.Common
{
    [ProtoContract]
    public class Equipments
    {
        [ProtoMember(1)]
        public Dictionary<string, int> Items { get; set; }

        public enum EquipmentType
        {
            Energy = 0,
            Coin = 1
        };

        public Equipments()
        {
            Items = new Dictionary<string, int>();
        }

        public static byte[] Serialize(Equipments equipments)
        {
            MemoryStream stream = new MemoryStream();

            Serializer.Serialize<Equipments>(stream, equipments);

            stream.Seek(0, SeekOrigin.Begin);

            return stream.ToArray();
        }

        public static Equipments Deserialize(byte[] buffer)
        {
            MemoryStream stream = new MemoryStream(buffer);
            stream.Seek(0, SeekOrigin.Begin);

            Equipments equipments = null;
            try
            {
                equipments = Serializer.Deserialize<Equipments>(stream);
            }
            catch (Exception ex)
            {
                throw ex;
            }

            return equipments;
        }
    }
}
