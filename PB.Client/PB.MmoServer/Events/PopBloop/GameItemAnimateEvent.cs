using PB.Common;
using Photon.SocketServer.Rpc;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.MmoServer.Events
{
    public class GameItemAnimateEvent
    {
        /// <summary>
        /// Gets or sets the source <see cref="Item"/> Id.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemId)]
        public string ItemId { get; set; }

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
    }
}
