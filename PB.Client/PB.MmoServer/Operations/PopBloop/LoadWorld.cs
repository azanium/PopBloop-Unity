using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Photon.SocketServer.Rpc;
using Photon.SocketServer;

using PB.Common;

namespace PB.MmoServer
{
    /// <summary>
    /// Load World Operation
    /// </summary>
    public class LoadWorld : Operation
    {
        public LoadWorld(IRpcProtocol protocol, OperationRequest request)
            : base(protocol, request)
        {
        }

        [DataMember(Code = (byte)ParameterCode.WorldName, IsOptional = false)]
        public string WorldName { get; set; }

        [DataMember(Code = (byte)ParameterCode.PrivateRoom, IsOptional = false)]
        public bool PrivateRoom { get; set; }

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
            var response = new LoadWorldResponse();

            return new OperationResponse(this.OperationRequest.OperationCode, response) { ReturnCode = errorCode, DebugMessage = debugMessage };
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
            return this.GetOperationResponse(returnValue.Error, returnValue.Debug);
        }
    }
}
