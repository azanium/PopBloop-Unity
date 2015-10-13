using System;
using System.Collections.Generic;
using System.Text;

using ProtoBuf;
using LitJson;

namespace Lilo.Common
{
    [ProtoContract]
    public class DialogStory
    {
        #region MemVars & Props

        [ProtoMember(1)]
        public string Name { get; set; }

        public int CurrentDialog { get; set; }

        [ProtoMember(2)]
        public List<Dialog> Dialogs { get; set; }

        #endregion


        #region Ctor

        public DialogStory()
        {
            CurrentDialog = 0;
            Name = "";
            Dialogs = new List<Dialog>();
        }

        #endregion


        #region Methods

        /// <summary>
        /// Get the list of the quests from the DialogStory
        /// </summary>
        /// <returns></returns>
        public List<int> GetQuests()
        {
            List<int> result = new List<int>();

            foreach (Dialog dlg in Dialogs)
            {
                foreach (DialogOption opt in dlg.Options)
                {
                    if (opt.Tipe == 1)  // 1 is Quest
                    {
                        result.Add(opt.Next);
                    }
                }
            }

            return result;
        }


        #region Serialization

        /// <summary>
        /// Parse DialogStory from JSON string
        /// </summary>
        /// <param name="json">JSON string</param>
        /// <returns>null if failed, not null if otherwise</returns>
        public static DialogStory ParseFromJSON(string json)
        {
            DialogStory story = null;
            try
            {
                story = JsonMapper.ToObject<DialogStory>(json);//JsonConvert.DeserializeObject<DialogStory>(json);
            }
            catch (Exception)
            {
                story = null;
            }

            return story;
        }

        /// <summary>
        /// Serialize a DialogStory into JSON string
        /// </summary>
        /// <param name="story">The Dialog Story</param>
        /// <returns>JSON string</returns>
        public static string SerializeToJSON(DialogStory story)
        {
            string result = null;
            try
            {
                result = JsonMapper.ToJson(story);
                //TODOCHECK
                //result = JsonConvert.SerializeObject(story);
            }
            catch (Exception)
            {
                result = null;
            }

            return result;
        }

        #endregion

        #endregion
    }
}
