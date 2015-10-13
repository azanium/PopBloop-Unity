using System;
using System.Collections.Generic;
using System.Text;

namespace PB.Common
{
    /// <summary>
    /// This enumeration contains known friend operation codes
    /// </summary>
    public enum FriendCode : byte
    {
        /// <summary>
        /// Request a friend
        /// </summary>
        Request = 1,

        /// <summary>
        /// Add friend
        /// </summary>
        Add = 2,

        /// <summary>
        /// Remove friend
        /// </summary>
        Remove = 3,

        /// <summary>
        /// Visit friend's room
        /// </summary>
        Visit = 4
    }
}
