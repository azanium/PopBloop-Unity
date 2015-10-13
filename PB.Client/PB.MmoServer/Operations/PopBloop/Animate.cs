using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Common;
using Photon.SocketServer.Rpc;
using Photon.SocketServer;

namespace PB.MmoServer.Operations
{
    public class Animate : Operation
    {
        public Animate(IRpcProtocol protocol, OperationRequest request)
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
        /// Get or sets the animation
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.Animation, IsOptional = false)]
        public string Animation { get; set; }

        /// <summary>
        /// The action of the animation
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationAction, IsOptional = false)]
        public byte AnimationAction { get; set; }

        /// <summary>
        /// Get or sets the animation wrap
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationWrap, IsOptional = false)]
        public byte AnimationWrap { get; set; }

        /// <summary>
        /// Gets or sets the animation speed
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationSpeed, IsOptional = false)]
        public float AnimationSpeed { get; set; }


        /// <summary>
        /// Gets or sets the animation layer
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationLayer, IsOptional = false)]
        public int AnimationLayer { get; set; }

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
            var responseObjcet = new AnimateResponse { ItemId = this.ItemId, ItemType = this.ItemType };

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
