using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Common;
using Photon.SocketServer.Rpc;
using Photon.SocketServer;

namespace PB.MmoServer.Operations
{
    public class AnimateResponse
    {
        public AnimateResponse()
        {
        }

        /// <summary>
        /// Gets or sets the selected <see cref="Item"/> Id.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemId)]
        public string ItemId { get; set; }

        /// <summary>
        /// Gets or sets the selected <see cref="Item"/> type.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemType)]
        public byte? ItemType { get; set; }

        /// <summary>
        /// Gets or sets the selected <see cref="Item"/> animation
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.Animation)]
        public string Animation { get; set; }

        /// <summary>
        /// Gets or sets the animation action
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationAction)]
        public byte AnimationAction { get; set; }

        /// <summary>
        /// Gets or sets the selected <see cref="Item"/> animation wrap
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationWrap)]
        public byte AnimationWrap { get; set; }

        /// <summary>
        /// Gets or sets the selected <see cref="Item"/> animation speed
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationSpeed)]
        public float AnimationSpeed { get; set; }

        /// <summary>
        /// Gets or sets the animation layer
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.AnimationLayer)]
        public int AnimationLayer { get; set; }
    }
}