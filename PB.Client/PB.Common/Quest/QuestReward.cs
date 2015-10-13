using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.Common
{
    public class QuestReward
    {
        #region MemVars & Props
        
        private Dictionary<string, int> _equipments = new Dictionary<string, int>();
        public Dictionary<string, int> Equipments
        {
            get { return _equipments; }
        }

        private Dictionary<string, int> _inventories = new Dictionary<string, int>();
        public Dictionary<string, int> Inventories
        {
            get { return _inventories; }
        }

        private Dictionary<string, int> _avatarRedeems = new Dictionary<string, int>();
        public Dictionary<string, int> AvatarRedeems
        {
            get { return _avatarRedeems; }
        }

        #endregion


        #region Ctor
        
        public QuestReward()
        {
        }

        #endregion


        public void AddEquipment(string code, int count)
        {
            if (_equipments.ContainsKey(code) == false)
            {
                _equipments.Add(code, count);
            }
        }

        public int GetEquipmentCount(string code)
        {
            if (_equipments.ContainsKey(code))
            {
                return _equipments[code];
            }
            return 0;
        }

        public int EquipmentCount()
        {
            return _equipments.Count;
        }

        public bool EquipmentExists(string code)
        {
            return GetEquipmentCount(code) > 0;
        }

        public void AddInventory(string code, int count)
        {
            if (_inventories.ContainsKey(code) == false)
            {
                _inventories.Add(code, count);
            }
        }

        public int GetInventoryCount(string code)
        {
            if (_inventories.ContainsKey(code))
            {
                return _inventories[code];
            }
            return 0;
        }

        public int InventoryCount()
        {
            return _inventories.Count;
        }

        public bool InventoryExists(string code)
        {
            return GetInventoryCount(code) > 0;
        }

        public void AddAvatarRedeem(string redeemCode, int count)
        {
            if (_avatarRedeems.ContainsKey(redeemCode) == false)
            {
                _avatarRedeems.Add(redeemCode, count);
            }
        }

        public int GetAvatarRedeemCount(string redeemCode)
        {
            if (_avatarRedeems.ContainsKey(redeemCode))
            {
                return _avatarRedeems[redeemCode];
            }
            return 0;
        }

        public int AvatarRedeemCount()
        {
            return _avatarRedeems.Count;
        }

        public bool AvatarRedeemExists(string redeemCode)
        {
            return GetAvatarRedeemCount(redeemCode) > 0;
        }
    }
}
