using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using UnityEngine;

namespace PB.Client
{
    public class InputManager
    {
        public static bool IsKeyClearForGameControl
        {
            get { return PBConstants.IsHoverShoutUI == false; }
        }

        public static bool GetKeyDown(KeyCode keyCode)
        {
            return Input.GetKeyDown(keyCode) && IsKeyClearForGameControl;
        }

        public static bool GetKeyUp(KeyCode keyCode)
        {
            return Input.GetKeyUp(keyCode) && IsKeyClearForGameControl;
        }

        public static bool GetButtonDown(string buttonName)
        {
            return Input.GetButtonDown(buttonName) && IsKeyClearForGameControl;
        }

        public static bool GetButtonUp(string buttonName)
        {
            return Input.GetButtonUp(buttonName) && IsKeyClearForGameControl;
        }

        public static bool GetButton(string buttonName)
        {
            return Input.GetButton(buttonName) && IsKeyClearForGameControl;
        }

        public static bool GetMouseButtonDown(int button)
        {
            return Input.GetMouseButton(button);
        }

        public static bool GetMouseButtonUp(int button)
        {
            return Input.GetMouseButtonUp(button);
        }
    }
}
