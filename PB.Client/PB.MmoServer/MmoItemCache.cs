// --------------------------------------------------------------------------------------------------------------------
// <copyright file="MmoItemCache.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   <see cref="ItemCache" /> subclass with a lock timeout configured at <see cref="Settings.MaxLockWaitTimeMilliseconds">Settings.MaxLockWaitTimeMilliseconds</see>.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.MmoServer
{
    using Photon.SocketServer.Mmo;

    /// <summary>
    /// <see cref="ItemCache"/> subclass with a lock timeout configured at <see cref="Settings.MaxLockWaitTimeMilliseconds">Settings.MaxLockWaitTimeMilliseconds</see>.
    /// </summary>
    public class MmoItemCache : ItemCache
    {
        /// <summary>
        /// Initializes a new instance of the <see cref="MmoItemCache"/> class.
        /// </summary>
        public MmoItemCache()
            : base(Settings.MaxLockWaitTimeMilliseconds)
        {
        }
    }
}