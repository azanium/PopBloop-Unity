using PB.Common;
using Photon.SocketServer.Rpc;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.MmoServer.Operations
{
    public class GameItemAnimateResponse
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
    }
}
