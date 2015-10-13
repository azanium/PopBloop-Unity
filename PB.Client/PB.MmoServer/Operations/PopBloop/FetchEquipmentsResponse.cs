using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Photon.SocketServer.Rpc;
using Photon.SocketServer;

using PB.Common;
using PB.MmoServer;

namespace PB.MmoServer.Operations
{
    public class FetchEquipmentsResponse : Operation
    {
        public FetchEquipmentsResponse()
        {
        }

        [DataMember(Code = (byte)ParameterCode.Equipments)]
        public byte[] Equipments { get; set; }
    }
}
