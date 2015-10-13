using PB.Common;
using Photon.SocketServer;
using Photon.SocketServer.Rpc;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.MmoServer.Operations
{
    public class GameItemAnimate : Operation
    {
        public GameItemAnimate(IRpcProtocol protocol, OperationRequest request)
            : base(protocol, request)
        {
        }

        /// <summary>
        /// Gets or sets the source <see cref="Item"/> Id.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemId)]
        public string ItemId { get; set; }

        /// <summary>
        /// Gets or sets the item type
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemType)]
        public byte? ItemType { get; set; }

        /// <summary>
        /// Gets or sets the game item id
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.GameItemId)]
        public string GameItemId { get; set; }

        /// <summary>
        /// Get or sets the animation name
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.Animation, IsOptional = false)]
        public string Animation { get; set; }

        /// <summary>
        /// Gets or sets the animation action
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationAction, IsOptional = false)]
        public byte AnimationAction { get; set; }

        /// <summary>
        /// Get or sets the animation wrap (loop, once)
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationWrap, IsOptional = false)]
        public byte AnimationWrap { get; set; }

        /// <summary>
        /// Gets or sets the animation speed 
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationSpeed, IsOptional = false)]
        public float AnimationSpeed { get; set; }

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
            var responseObjcet = new GameItemAnimateResponse { ItemId = this.ItemId, GameItemId = this.GameItemId };

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
