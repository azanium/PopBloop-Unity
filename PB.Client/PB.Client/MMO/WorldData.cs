// --------------------------------------------------------------------------------------------------------------------
// <copyright file="WorldData.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   The world data.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

using PB.Common;
namespace PB.Client
{
    /// <summary>
    /// The world data.
    /// </summary>
    public class WorldData
    {
        /// <summary>
        /// Gets or sets BottomRightCorner.
        /// </summary>
        public float[] BottomRightCorner { get; set; }

        /// <summary>
        /// Gets Height.
        /// </summary>
        public float Height
        {
            get
            {
                return this.BottomRightCorner[1] - this.TopLeftCorner[1];
            }
        }

        /// <summary>
        /// Gets or sets Name.
        /// </summary>
        public string Name { get; set; }

        /// <summary>
        /// Gets or sets TileDimensions.
        /// </summary>
        public float[] TileDimensions { get; set; }

        /// <summary>
        /// Gets or sets TopLeftCorner.
        /// </summary>
        public float[] TopLeftCorner { get; set; }

        /// <summary>
        /// Gets Width.
        /// </summary>
        public float Width
        {
            get
            {
                return this.BottomRightCorner[0] - this.TopLeftCorner[0];
            }
        }

        /// <summary>
        /// The Level associated with this World
        /// </summary>
        public Level Level
        {
            get;
            set;
        }
    }
}