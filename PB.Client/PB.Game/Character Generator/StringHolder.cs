using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using UnityEngine;

namespace PB.Game
{
    /// <summary>
    /// Hold Array of Strings for serialization used by Assets
    /// </summary>
    public class StringHolder : ScriptableObject
    {
        /// <summary>
        /// The Array of String content
        /// </summary>
        public string[] content;
    }
}