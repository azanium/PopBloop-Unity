using PB.Common;
using Photon.SocketServer.Rpc;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.MmoServer.Operations
{
    public class GameItemMoveResponse
    {
        public GameItemMoveResponse()
        {
        }

        /// <summary>
        /// Game Item Id
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ItemId)]
        public string ItemId { get; set; }

        /// <summary>
        /// Game Item Position
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.Position)]
        public float[] Position { get; set; }

        /// <summary>
        /// Game Item Rotation
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.Rotation)]
        public float[] Rotation { get; set; }
    }
}
