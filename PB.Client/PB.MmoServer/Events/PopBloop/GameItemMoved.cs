using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using PB.Common;
using Photon.SocketServer.Rpc;

namespace PB.MmoServer.Events
{
    public class GameItemMoved
    {
        /// <summary>
        /// Gets or sets the source <see cref="Item"/> Id.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemId)]
        public string ItemId { get; set; }

        /// <summary>
        /// Gets or sets the Game Item id
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.GameItemId)]
        public string GameItemId { get; set; }

        /// <summary>
        /// Gets or sets the source <see cref="Item"/>'s new position.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.Position)]
        public float[] Position { get; set; }

        /// <summary>
        /// Gets or sets the source <see cref="Item"/>'s new rotation.
        /// This parameter is optional.
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.Rotation, IsOptional = true)]
        public float[] Rotation { get; set; }
    }
}
