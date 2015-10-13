using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Photon.SocketServer.Rpc;
using Photon.SocketServer;
using PB.Common;

namespace PB.MmoServer.Operations
{
    public class Friend : Operation
    {
        public Friend(IRpcProtocol protocol, OperationRequest request)
            : base(protocol, request)
        {
        }

        /// <summary>
        /// Gets or sets the selected <see cref="Item"/> Id.
        /// This request parameter is optional. If not submitted the <see cref="Actor.Avatar"/> is selected.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemId)]
        public string ItemId { get; set; }

        /// <summary>
        /// Gets or sets the selected <see cref="Item"/> type.
        /// This request parameter is optional. If not submitted the <see cref="Actor.Avatar"/> is selected.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemType)]
        public byte? ItemType { get; set; }

        /// <summary>
        /// Friend Request operation code
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.FriendOpCode, IsOptional = false)]
        public byte OpCode { get; set; } 
    }
}
