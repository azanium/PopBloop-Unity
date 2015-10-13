using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Photon.SocketServer.Rpc;

using PB.Common;

namespace PB.MmoServer.Operations
{
    public class FetchInventoryResponse
    {
        public FetchInventoryResponse()
        {
        }

        [DataMember(Code = (byte)ParameterCode.Inventories)]
        public byte[] Inventories { get; set; }
    }
}
