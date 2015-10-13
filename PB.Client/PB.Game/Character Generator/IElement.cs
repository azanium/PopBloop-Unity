using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.Game
{

    public interface IElement
    {
        void Clear();
        bool IsLoaded { get; }
    }

}