using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Photon.SocketServer.Rpc;
using PB.Common;

namespace PB.MmoServer
{
    public class LoadWorldResponse
    {
        public LoadWorldResponse()
        {
        }

        [DataMember(Code = (byte)ParameterCode.WorldLevel, IsOptional = false)]
        public byte[] WorldLevel { get; set; }
    }
}
