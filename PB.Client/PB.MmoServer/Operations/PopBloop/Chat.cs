namespace PB.MmoServer.Operations
{
    using PB.Common;
    using Photon.SocketServer;
    using Photon.SocketServer.Mmo;
    using Photon.SocketServer.Rpc;

    public class Chat : Operation
    {
        public Chat(IRpcProtocol protocol, OperationRequest request)
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
        /// Chat Group, set it to "" if no group is associated
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ChatGroup)]
        public string[] Group { get; set; }

        /// <summary>
        /// Chat Message
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ChatMessage, IsOptional = false)]
        public string Message { get; set; }

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
            var responseObjcet = new ChatResponse { ItemId = this.ItemId, ItemType = this.ItemType };

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
