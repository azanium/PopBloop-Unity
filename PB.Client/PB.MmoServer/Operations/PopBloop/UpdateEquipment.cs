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
    public class UpdateEquipment : Operation
    {
        public UpdateEquipment(IRpcProtocol protocol, OperationRequest request)
            : base(protocol, request)
        {
        }

        [DataMember(Code = (byte)ParameterCode.PlayerId)]
        public string UserId { get; set; }

        [DataMember(Code = (byte)ParameterCode.EquipmentCode)]
        public string EquipmentCode { get; set; }

        [DataMember(Code = (byte)ParameterCode.EquipmentCount)]
        public int EquipmentCount { get; set; }

        
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
            return new OperationResponse(this.OperationRequest.OperationCode) { ReturnCode = errorCode, DebugMessage = debugMessage };
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
