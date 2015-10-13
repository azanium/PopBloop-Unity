using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Common;
using Photon.SocketServer.Rpc;
using Photon.SocketServer;

namespace PB.MmoServer.Operations
{
    public class LevelInfoResponse
    {
        public LevelInfoResponse()
        {
        }

        [DataMember(Code = (byte)ParameterCode.WorldName)]
        public string WorldName { get; set; }

        [DataMember(Code = (byte)ParameterCode.CurrentCCU)]
        public int CurrentCCU { get; set; }

        [DataMember(Code = (byte)ParameterCode.MaxCCU)]
        public int MaxCCU { get; set; }
    }
}
