

namespace PB.MmoServer.Operations
{
    using Photon.SocketServer.Rpc;
    using Photon.SocketServer;
    using PB.Common;

    /// <summary>
    /// This operation is run after user connected.
    /// </summary>
    public class Login : Operation
    {
        public Login(IRpcProtocol protocol, OperationRequest request)
            : base(protocol, request)
        {
        }

        [DataMember(Code = (byte)ParameterCode.Username, IsOptional = false)]
        public string Username { get; set; }

        [DataMember(Code = (byte)ParameterCode.Password, IsOptional = false)]
        public string Password { get; set; }

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
            var responseObjcet = new AuthenticateResponse();

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