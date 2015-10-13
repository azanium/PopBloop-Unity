namespace PB.MmoServer.Operations
{
    using PB.Common;
    using Photon.SocketServer;
    using Photon.SocketServer.Mmo;
    using Photon.SocketServer.Rpc;

    public class ChatResponse
    {
        public ChatResponse()
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
    }
}
