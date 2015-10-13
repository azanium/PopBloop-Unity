using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using UnityEngine;

using LitJson;

using PB.Common;
using PB.Client;

public class QuestEngine : LoaderBase
{
    #region MemVars & Props

    public static readonly QuestEngine Instance = new QuestEngine();

    private GameControllerBase _mainGame = null;

    protected List<int> _activeQuests = new List<int>();
    public List<int> ActiveQuests
    {
        get { return _activeQuests; }
    }

    protected List<int> _history = new List<int>();
    public List<int> History
    {
        get { return _history; }
    }

    protected Dictionary<int, Quest> _quests = new Dictionary<int, Quest>();
    public Dictionary<int, Quest> Quests
    {
        get { return _quests; }
    }

    protected Dictionary<int, WWW> _questRequests = new Dictionary<int, WWW>();

    protected class QuestData
    {
        public bool IsDownloaded = false;
        public int QuestId = -1;
        public Quest Quest { get; set; }
        public string Url = "";

        public QuestData(string url)
        {
            Url = url;
        }
    }
    protected Dictionary<int, QuestData> _questDownloads = new Dictionary<int, QuestData>();

    public override string StatusText
    {
        get { return PBConstants.LOADINGBAR_GAME_QUESTS; }
    }

    #endregion


    #region Methods

    public void Initialize(GameControllerBase mainGame)
    {
        _mainGame = mainGame;
    }

    public void RegisterQuest(int questId)
    {
        if (_questDownloads.ContainsKey(questId) == false)
        {
            _questDownloads.Add(questId, new QuestData(PopBloopSettings.GetQuestStoryURL(questId.ToString()) + "/" + Time.frameCount));
        }
    }

    public override void PrepareDownload()
    {
        base.PrepareDownload();
    }

    public override bool IsReady()
    {
        base.IsReady();

        WWW asset = null;

        int count = 0;

        foreach (int questId in _questDownloads.Keys)
        {
            count++;

            QuestData quest = _questDownloads[questId];
            
            if (quest.IsDownloaded)
            {
                continue;
            }

            string url = quest.Url;

            asset = AssetsManager.DownloadString(url);

            if (asset.isDone == false)
            {
                return false;
            }

            if (asset.error != null)
            {
                Debug.LogWarning("LoadWorld: Retrying error when downloading " + url + " => " + asset.error);
                asset = AssetsManager.RetryDownloadString(url);
                return false;
            }

            // Mark this dialog data as downloaded
            quest.IsDownloaded = true;

            string json = asset.text.Trim();

            if (string.IsNullOrEmpty(json) == false)
            {
                DecodeJsonToQuest(questId, json);
            }

            // Calculate the progress
            _progress = (float)count / (float)_questDownloads.Count;
        }

        return true;
    }

    public Quest DecodeJsonToQuest(int questId, string json)
    {
        if (_quests.ContainsKey(questId) == false)
        {
            Quest result = null;

            if (string.IsNullOrEmpty(json) == false)
            {
                result = QuestEngine.ParseFromJSON(json);
            }

            if (result != null)
            {
                if (PopBloopSettings.useLogs)
                {
                    Debug.Log(string.Format("Quest {0} decoded succefully, Json: {1}", questId, json));
                }

                if (_activeQuests.Contains(result.ID))
                {
                    result.IsActive = true;
                }

                if (result.ID != -1)
                {
                    _quests.Add(questId, result);
                }
                else
                {
                    result = null;
                }

                return result;
            }
            else
            {
                return result;
            }
        }
        else
        {
            // Check if this quest is exist on our quest active journal,
            // if it is, then mark this quest as active
            if (_activeQuests.Contains(_quests[questId].ID))
            {
                _quests[questId].IsActive = true;
            }

            return _quests[questId];
        }
    }

    public override void Clear()
    {
        base.Clear();

        _questDownloads.Clear();
    }

    /// <summary>
    /// Fetch the quest from the server
    /// </summary>
    /// <param name="questID"></param>
    public Quest FetchQuest(int questID)
    {
        // Invalid quest
        if (questID == -1)
        {
            return null;
        }

        if (_quests.ContainsKey(questID) == false)
        {
            Quest result = null;

            WWW questWWW = null;

            // Check if our request to the WWW is already cached
            if (_questRequests.ContainsKey(questID))
            {
                questWWW = _questRequests[questID];
            }
            else
            {
                // No cache, then we request the quest the web webservice
                string url = PopBloopSettings.GetQuestStoryURL(questID.ToString()) + "/" + Time.frameCount;

                try
                {
                    Debug.Log("QuestEngine: Requesting Quest with ID " + questID + " => " + url);

                    questWWW = new WWW(url);
                    _questRequests.Add(questID, questWWW);
                }
                catch (Exception ex)
                {
                    Debug.LogError(ex.ToString());
                }
            }

            // If we have valid quest response, then parse it
            if (questWWW.isDone && questWWW.error == null)
            {
                string json = questWWW.text.Trim();

                //Debug.Log("QuestEngine: Got Quest JSON => " + json);

                if (json != "null" && json.Length != 0)
                {
                    result = QuestEngine.ParseFromJSON(json);
                }
            }

            if (result != null)
            {
                if (_activeQuests.Contains(result.ID))
                {
                    result.IsActive = true;
                }

                if (result.ID != -1)
                {
                    _quests.Add(questID, result);
                }
                else
                {
                    result = null;
                }

                return result;
            }
            else
            {
                return result;
            }
        }
        else
        {
            // Check if this quest is exist on our quest active journal,
            // if it is, then mark this quest as active
            if (_activeQuests.Contains(_quests[questID].ID))
            {
                _quests[questID].IsActive = true;
            }

            return _quests[questID];
        }
    }

    /// <summary>
    /// Create a dummy quest
    /// </summary>
    /// <param name="questID">The Quest ID</param>
    /// <returns>Always not null</returns>
    public Quest CreateDummyQuest(int questID)
    {
        Quest quest = new Quest(questID, "Quest Dummy", "Tolong carikan saya sebuah energi", "Apakah anda sudah mendapatkan energi?", "Sebagai rasa terima kasih saya berikan 10 energi", -1, 0, Quest.EncodeItem("", 1), Quest.EncodeItem("", 10), false, false, true);

        return quest;
    }

    /// <summary>
    /// Get the Quest from the quest id, if none found, we fetch them from the server
    /// </summary>
    /// <param name="questID">The quest ID</param>
    /// <returns>null if none found, true if otherwise</returns>
    public Quest GetQuest(int questID)
    {
        if (_quests.ContainsKey(questID))
        {
            if (_activeQuests.Contains(questID))
            {
                _quests[questID].IsActive = true;
            }
            return _quests[questID];
        }

        return null;//FetchQuest(questID);
    }

    /// <summary>
    /// Check if a requirement of certain quest ID is met
    /// </summary>
    /// <param name="questID">The Quest ID</param>
    /// <returns>true if requirement met, false otherwise</returns>
    public bool IsQuestRequirementValid(int questID)
    {
        if (History.Contains(questID))
        {
            return false;
        }

        // First, we query the quest, if none is found, then GetQuest will download it
        Quest quest = GetQuest(questID);

        // We found no quest of the id of questID, return false
        if (quest == null)
        {
            return false;
        }

        if (quest.Requirement == -1)
        {
            return true;
        }

        // Check in the history journal, see if our quest is finished, if it is, then we met the requirement
        if (_history.Contains(quest.Requirement))
        {
            return true;
        }

        // No requirement met, bug out
        return false;
    }

    public void SetQuestActive(int questID, bool isActive)
    {
        Quest quest = GetQuest(questID);

        if (quest != null)
        {
            quest.IsActive = isActive;

            if (isActive)
            {
                AddJournal(questID, true);
            }
        }
    }

    public bool IsQuestActive(int questID)
    {
        Quest quest = GetQuest(questID);

        return quest != null ? quest.IsActive : false;
    }

    public void SetQuestDone(int questID, bool isDone)
    {
        Quest quest = GetQuest(questID);

        if (quest != null)
        {
            quest.IsDone = isDone;

            if (isDone)
            {
                AddJournal(questID, false);
            }
        }

        if (_activeQuests.Contains(questID))
        {
            _activeQuests.Remove(questID);
        }
    }

    public bool IsQuestDone(int questID)
    {
        Quest quest = GetQuest(questID);

        return quest != null ? quest.IsDone : false;
    }

    public bool IsQuestReturnable(int questID)
    {
        Quest quest = GetQuest(questID);

        return quest != null ? quest.IsReturn : false;
    }

    public string GetQuestCurrentDescription(int questID)
    {
        Quest quest = GetQuest(questID);

        if (quest != null)
        {
            if (quest.IsDone)
            {
                return quest.DescriptionDone;
            }
            else
            {
                return quest.IsActive ? quest.DescriptionActive : quest.DescriptionNormal;
            }
        }

        return null;
    }

    public bool GetQuestItemRequirementsv1(int questID, out string code, out int count)
    {
        Quest quest = GetQuest(questID);

        code = "";
        count = 0;

        if (quest != null)
        {
            Quest.DecodeItemv1(quest.RequiredItem, out code, out count);
            if (string.IsNullOrEmpty(code))
            {
                return false;
            }

            return true;
        }

        return false;
    }

    public bool GetQuestItemRequirements(int questID, out QuestReward reward)// out Dictionary<string, int> equipments, out Dictionary<string, int> inventories)
    {
        Quest quest = GetQuest(questID);

        reward = new QuestReward();

        if (quest != null)
        {
            Quest.DecodeItem(quest.RequiredItem, out reward);

            if (reward.EquipmentCount() == 0 && reward.InventoryCount() == 0)
            {
                return false;
            }

            return true;
        }

        return false;
    }

    /// <summary>
    /// Check if current time is valid against StartDate and EndDate of the quest
    /// </summary>
    /// <param name="questID">The Quest ID</param>
    /// <param name="now">Now time</param>
    /// <returns>true - valid, false - invalid</returns>
    public bool IsQuestDateTimeRangeValid(int questID, DateTime now)
    {
        Quest quest = GetQuest(questID);

        if (quest != null)
        {
            return quest.IsDateTimeRangeValid(now);
        }

        return false;
    }

    /// <summary>
    /// Get Energy Requirement of the Quest
    /// </summary>
    /// <param name="questID">The Quest ID</param>
    /// <returns>The number of energy needed</returns>
    public int GetEnergyRequirement(int questID)
    {
        Quest quest = GetQuest(questID);

        return quest != null ? quest.RequiredEnergy : -1;
    }

    
    /// <summary>
    /// Get Quest Rewards
    /// </summary>
    /// <param name="questID">Quest ID</param>
    /// <param name="reward">Quest Reward object</param>
    /// <returns>true if valid, false otherwise</returns>
    public bool GetRewards(int questID, out QuestReward reward)// out Dictionary<string, int> equipments, out Dictionary<string, int> inventories, out List<string> redeems)
    {
        Quest quest = GetQuest(questID);

        /*equipments = new Dictionary<string, int>();
        inventories = new Dictionary<string, int>();
        redeems = new List<string>();*/
        reward = new QuestReward();

        if (quest != null)
        {
            Quest.DecodeItem(quest.Rewards, out reward);//out equipments, out inventories, out redeems);

            return true;
        }

        return false;
    }

    /// <summary>
    /// Check if Quest Item's Requirements is valid, the criterias are:
    /// 1. Required Quest is valid
    /// 2. Item Type and Item Count is found on the Inventory with itemCount > 0
    /// 3. The Quest is active
    /// </summary>
    /// <param name="questID">the quest ID to checks</param>
    /// <returns>true if valid, false if otherwise</returns>
    public bool IsItemRequirementsValid(int questID)
    {
        if (IsQuestRequirementValid(questID) == false)
        {
            return false;
        }

        /*Dictionary<string, int> equipments;
        Dictionary<string, int> inventories;*/
        QuestReward reward;

        // Check our quest item requirements
        GetQuestItemRequirements(questID, out reward);// out equipments, out inventories);

        bool isValid = false;

        if (IsQuestActive(questID))
        {
            if (reward.InventoryCount() > 0)
            {
                int count = 0;
                foreach (string itemCode in reward.Inventories.Keys)
                {
                    // Check the item requirement in our Inventory Engine
                    if (InventoryEngine.Instance.HasItems(itemCode, reward.GetInventoryCount(itemCode)))
                    {
                        //isValid = true;
                        count++;
                    }
                }
                isValid = count == reward.InventoryCount();
            }

            if (reward.EquipmentCount() > 0)
            {
                int count = 0;
                foreach (string eqCode in reward.Equipments.Keys)
                {
                    if (InventoryEngine.Instance.GetEquipmentCount(eqCode) == reward.GetEquipmentCount(eqCode))
                    {
                        count++;
                    }
                }
                isValid = count == reward.EquipmentCount();
            }
        }

        return isValid;
    }

    /// <summary>
    /// Check if Quest Item's Requirements is valid, the criterias are:
    /// 1. Required Quest is valid
    /// 2. Item Type and Item Count is found on the Inventory with itemCount > 0
    /// 3. The Quest is active
    /// </summary>
    /// <param name="questID">the quest ID to checks</param>
    /// <returns>true if valid, false if otherwise</returns>
    public bool IsItemRequirementsValidv1(int questID)
    {
        if (IsQuestRequirementValid(questID) == false)
        {
            return false;
        }

        string code;
        int count;

        // Check our quest item requirements
        GetQuestItemRequirementsv1(questID, out code, out count);

        bool isValid = false;

        if (IsQuestActive(questID))
        {
            isValid = InventoryEngine.Instance.ItemCount(code) >= count;
        }

        return isValid;
    }

    public void SetQuestJournal(int[] journal)
    {
        if (journal != null)
        {
            _history.Clear();
            _history.AddRange(journal);
        }
    }

    public void SetQuestActiveJournal(int[] questactive)
    {
        if (questactive != null)
        {
            _activeQuests.Clear();
            _activeQuests.AddRange(questactive);
        }
    }

    public void AddJournal(int questid, bool isActive)
    {
        if (_history.Contains(questid) == false)
        {
            if (isActive)
            {
                _activeQuests.Add(questid);
            }
            else
            {
                _history.Add(questid);
            }

            if (_mainGame != null)
            {
                _mainGame.Game.Avatar.SetQuestJournal(questid, isActive);
            }
        }
    }

    public void RemoveJournal(int questid)
    {
        if (_history.Contains(questid))
        {
            _history.Remove(questid);

            if (_mainGame != null)
            {
                //Game.Avatar.SetQuestJournal(questid);
            }
        }
    }

    #endregion


    #region Serialization

    /// <summary>
    /// Parse DialogStory from JSON string
    /// </summary>
    /// <param name="json">JSON string</param>
    /// <returns>null if failed, not null if otherwise</returns>
    public static Quest ParseFromJSON(string json)
    {
        Quest quest = null;
        try
        {
            quest = JsonMapper.ToObject<Quest>(json);//JsonConvert.DeserializeObject<Quest>(json);
        }
        catch (Exception ex)
        {
            quest = null;
            Debug.LogError("QuestEngine.ParseFromJSON: " + ex.ToString());
        }

        return quest;
    }

    /// <summary>
    /// Serialize a DialogStory into JSON string
    /// </summary>
    /// <param name="story">The Dialog Story</param>
    /// <returns>JSON string</returns>
    public static string SerializeToJSON(Quest story)
    {
        string result = null;
        try
        {
            result = JsonMapper.ToJson(story);
            //TODOCHECK
            //result = JsonConvert.SerializeObject(story);
        }
        catch (Exception ex)
        {
            Debug.LogError(ex.ToString());

            result = null;
        }

        return result;
    }

    #endregion
}

