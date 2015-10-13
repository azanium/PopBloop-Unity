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
    public class FetchInventory : Operation
    {
        public FetchInventory(IRpcProtocol protocol, OperationRequest request)
            : base(protocol, request)
        {
        }

        [DataMember(Code = (byte)ParameterCode.PlayerId)]
        public string UserId { get; set; }

        /// <summary>
        /// Gets the operation response.
        /// </summary>
        /// <param name="errorCode">
        /// The error code.
        /// </param>
        /// <param name="debugMessage">
        /// The debug message.
        /// </param>
        /// <returns>
        /// A new operation response.
        /// </returns>
        public OperationResponse GetOperationResponse(short errorCode, string debugMessage)
        {
            var responseObjcet = new FetchInventoryResponse();

            return new OperationResponse(this.OperationRequest.OperationCode, responseObjcet) { ReturnCode = errorCode, DebugMessage = debugMessage };
        }

        /// <summary>
        /// Gets the operation response.
        /// </summary>
        /// <param name="returnValue">
        /// The return value.
        /// </param>
        /// <returns>
        /// A new operation response.
        /// </returns>
        public OperationResponse GetOperationResponse(MethodReturnValue returnValue)
        {
            return GetOperationResponse(returnValue.Error, returnValue.Debug);
        }
    }
}
