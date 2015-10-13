using System;
using System.Collections.Generic;
using ProtoBuf;

namespace PB.Common
{
    [ProtoContract]
    public class GameItem
    {
        [ProtoMember(1)]
        public string Id { get; set; }

        [ProtoMember(2)]
        public string Code { get; set; }

        [ProtoMember(3)]
        public string Name { get; set; }

        [ProtoMember(4)]
        public float Weight { get; set; }

        [ProtoMember(5)]
        public string Description { get; set; }

        public GameItem()
        {
        }

        public GameItem(string id, string code, string name, float weight, string description)
        {
            this.Id = id;
            this.Code = code;
            this.Name = name;
            this.Weight = weight;
            this.Description = description;
        }
    }
}
