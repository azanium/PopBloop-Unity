using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Photon.SocketServer.Rpc;
using PB.Common;

namespace PB.MmoServer.Operations
{
    public class AuthenticateResponse
    {
        public AuthenticateResponse()
        { }

        [DataMember(Code = (byte)ParameterCode.PlayerId)]
        public string PlayerId { get; set; }

        [DataMember(Code = (byte)ParameterCode.PlayerUsername)]
        public string PlayerUsername { get; set; }

        [DataMember(Code = (byte)ParameterCode.AvatarName)]
        public string AvatarName { get; set; }

        [DataMember(Code = (byte)ParameterCode.Inventories)]
        public byte[] Inventories { get; set; }

        [DataMember(Code = (byte)ParameterCode.QuestJournal)]
        public int[] QuestJournal { get; set; }

        [DataMember(Code = (byte)ParameterCode.QuestActive)]
        public int[] QuestActive { get; set; }

        [DataMember(Code = (byte)ParameterCode.Equipments)]
        public byte[] Equipments { get; set; }
    }
}
