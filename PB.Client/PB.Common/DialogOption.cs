using ProtoBuf;

namespace Lilo.Common
{
    [ProtoContract]
    public class DialogOption
    {
        [ProtoMember(1)]
        public byte Tipe { get; set; }

        [ProtoMember(2)]
        public string Content { get; set; }

        [ProtoMember(3)]
        public int Next { get; set; }

        public DialogOption()
        {
            Tipe = 0;
            Content = "";
            Next = -1;
        }

        public DialogOption(byte tipe, string content, int nextId)
        {
            Tipe = tipe;
            Content = content;
            Next = nextId;
        }
    }
}
