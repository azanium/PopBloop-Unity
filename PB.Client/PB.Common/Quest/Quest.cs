using System;
using System.Collections.Generic;
using System.Text;
using ProtoBuf;

namespace PB.Common
{
    [ProtoContract]
    public class Quest
    {
        #region MemVars & Props

        [ProtoMember(1)]
        public int ID { get; set; }

        [ProtoMember(2)]
        public string Description { get; set; }

        [ProtoMember(3)]
        public string DescriptionNormal { get; set; }

        [ProtoMember(4)]
        public string DescriptionActive { get; set; }

        [ProtoMember(5)]
        public string DescriptionDone { get; set; }
        
        [ProtoMember(6)]
        public int Requirement { get; set; }

        [ProtoMember(7)]
        public string RequiredItem { get; set; }

        [ProtoMember(8)]
        public string Rewards { get; set; }

        [ProtoMember(9)]
        public bool IsDone { get; set; }

        [ProtoMember(10)]
        public bool IsActive { get; set; }

        [ProtoMember(11)]
        public bool IsReturn { get; set; }

        [ProtoMember(12)]
        public int RequiredEnergy { get; set; }

        [ProtoMember(13)]
        public string StartDate { get; set; }

        [ProtoMember(14)]
        public string EndDate { get; set; }

        #endregion


        #region Ctor

        public Quest()
        {
            Initialize(-1, "", "", "", "", 0, 0, EncodeItem("", 0), EncodeItem("", 0), false, false, true);
        }

        #endregion


        #region Methods

        public Quest(int id, string description, string normal, string active, string done, int requirement, int energy, string item, string rewards, bool isdone, bool isactive, bool isreturn)
        {
            Initialize(id, description, normal, active, done, requirement, energy, item, rewards, isdone, isactive, isreturn);
        }

        private void Initialize(int id, string description, string normal, string active, string done, int requirement, int energy, string item, string rewards, bool isdone, bool isactive, bool isreturn)
        {
            ID = id;
            Description = description;
            DescriptionNormal = normal;
            DescriptionActive = active;
            DescriptionDone = done;
            
            IsDone = isdone;
            IsActive = isactive;
            IsReturn = isreturn;

            Requirement = requirement;
            Rewards = rewards;
            RequiredItem = item;
            RequiredEnergy = energy;
        }

        public static string EncodeItem(string itemCode, int itemCount)
        {
            return string.Format("{0}|{1}", itemCode, itemCount);
        }

        public static void DecodeItemv1(string item, out string code, out int count)
        {
            string[] items = item.Split(new char[] {'|'});

            if (items.Length == 2)
            {
                code = items[0];
                count = Int32.Parse(items[1]);
            }
            else
            {
                code = null;
                count = 0;
            }   
        }

        public static void DecodeItem(string item, out QuestReward reward)// out Dictionary<string, int> equipments, out Dictionary<string, int> inventories, out Dictionary<string, int> redeems)
        {
            //equipments = new Dictionary<string, int>();
            //inventories = new Dictionary<string, int>();
            //redeems = new Dictionary<string, int>();
            reward = new QuestReward();


            // format: e:energy,20|e:coin,5|x:AVATAR_ID,COUNT
            string[] items = item.Split(new char[] { '|' } );

            foreach (string value in items)
            {
                string[] datas = value.Split(new char[] { ':' } );

                if (datas.Length < 2)
                {
                    continue;
                }

                string command = datas[0];
                string codedItem = datas[1];

                string[] pair = codedItem.Split(new char[] { ',' });

                if (pair.Length < 2)
                {
                    continue;
                }

                string code = pair[0];
                int count = Int32.Parse(pair[1]);

                if (command.Contains("e"))
                {
                    reward.AddEquipment(code, count);
                }
                else if (command.Contains("i"))
                {
                    reward.AddInventory(code, count);
                }
                else if (command.Contains("x"))
                {
                    reward.AddAvatarRedeem(code, count);
                }
            }
        }

        private DateTime DecodeDateTime(string dateTime)
        {
            DateTime date = DateTime.Parse(dateTime);

            return date;
        }

        private DateTime GetStartDate
        {
            get
            {
                return DecodeDateTime(StartDate);
            }
        }

        private DateTime GetEndDate
        {
            get
            {
                return DecodeDateTime(EndDate);
            }
        }

        public bool IsDateTimeRangeValid(DateTime now)
        {
            if (StartDate == null || StartDate == "" || EndDate == null || EndDate == "")
            {
                return true;
            }

            int startResult = DateTime.Compare(now, GetStartDate);
            int endResult = DateTime.Compare(now, GetEndDate);

            if ((startResult == 0 || startResult > 0) && (endResult == 0 || endResult < 0))
            {
                return true;
            }

            return false;
        }

        #endregion
    }
}
