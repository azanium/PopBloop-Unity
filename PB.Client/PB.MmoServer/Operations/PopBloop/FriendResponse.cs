using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Common;
using Photon.SocketServer.Rpc;

namespace PB.MmoServer.Operations
{
    public class FriendResponse
    {
        /// <summary>
        /// Friend Request Operation Code
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.FriendOpCode)]
        public byte OpCode { get; set; }
    }
}
