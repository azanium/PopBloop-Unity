using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Photon.SocketServer.Rpc;

using PB.Common;

namespace PB.MmoServer.Events
{
    /// <summary>
    /// Chat between items
    /// </summary>
    public class ItemChat
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
        /// Gets or sets the chat group
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ChatGroup, IsOptional = false)]
        public string[] Group { get; set; }

        /// <summary>
        /// Gets or sets the chat messages
        /// </summary>
        [DataMember(Code = (byte)ParameterCode.ChatMessage, IsOptional = false)]
        public string Message { get; set; }
    }
}
