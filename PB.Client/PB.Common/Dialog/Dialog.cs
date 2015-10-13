using System;
using System.Collections.Generic;
using System.Text;

using ProtoBuf;

namespace PB.Common
{
    [ProtoContract]
    public class Dialog
    {
        [ProtoMember(1)]
        public int ID { get; set; }

        [ProtoMember(2)]
        public string Description { get; set; }

        [ProtoMember(3)]
        public List<DialogOption> Options { get; set; }

        public Dialog()
        {
            Description = "";
            ID = -1;
            Options = new List<DialogOption>();
        }

        public Dialog(int id, string desc)
        {
            Description = desc;
            ID = id;
            Options = new List<DialogOption>();
        }
    }
}
