using System;
using System.Collections.Generic;
using System.Text;

using ProtoBuf;

namespace PB.Common
{
    [ProtoContract]
    public class Quiz
    {
        [ProtoMember(1)]
        public int ID { get; set; }

        [ProtoMember(2)]
        public string Title { get; set; }

        [ProtoMember(3)]
        public string Description { get; set; }

        [ProtoMember(4)]
        public string brand_id { get; set; }

        [ProtoMember(5)]
        public string State { get; set; }

        [ProtoMember(6)]
        public string StartTime { get; set; }

        [ProtoMember(7)]
        public string EndTime { get; set; }

        [ProtoMember(8)]
        public bool isRandom { get; set; }

        [ProtoMember(9)]
        public int number { get; set; }

        [ProtoMember(10)]
        public int count { get; set; }

        [ProtoMember(3)]
        public string RequiredQuiz { get; set; }

        [ProtoMember(3)]
        public List<DialogOption> Options { get; set; }
    }
}
