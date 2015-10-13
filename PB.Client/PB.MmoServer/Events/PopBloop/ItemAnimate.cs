using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Photon.SocketServer.Rpc;

using PB.Common;

namespace PB.MmoServer.Events
{
    /// <summary>
    /// Animate Item 
    /// -SA
    /// </summary>
    public class ItemAnimate
    {
        /// <summary>
        /// Gets or sets the source <see cref="Item"/> Id.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemId)]
        public string ItemId { get; set; }

        /// <summary>
        /// Gets or sets the source <see cref="Item"/> type.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemType)]
        public byte ItemType { get; set; }

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
        /// Gets or sets the animation layer
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationLayer, IsOptional = false)]
        public int AnimationLayer { get; set; }
    }
}
