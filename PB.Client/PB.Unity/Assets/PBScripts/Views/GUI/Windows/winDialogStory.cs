using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Common;
using PB.Client;
using PB.Game;
using System;

public class winDialogStory : WindowBase
{
	#region MemVars & Props
	
	/// <summary>
	/// State Action delegate
	/// </summary>
	public delegate void StateAction(float gap, float buttonHeight);
	
	/// <summary>
	/// The Dialog Story that we're dealing with
	/// </summary>
    public DialogStory Story { get; private set; }
	
	/// <summary>
	/// Check is the Dialog is on Quest mode or not
	/// </summary>
	protected bool dlgIsQuest = false;
	
	/// <summary>
	/// The Quest state type
	/// </summary>
	public enum QuestStateType
	{
		None,
		Normal,
		Active, 
		Done
	}
	/// <summary>
	/// Current Quest State
	/// </summary>
	public QuestStateType QuestState = QuestStateType.None;
	
	/// <summary>
	/// Current Active Quest ID
	/// </summary>
	protected int _activeQuestID = -1;
	
	/// <summary>
	/// Actions dictionary
	/// </summary>
	protected Dictionary<QuestStateType, StateAction> _actions = new Dictionary<QuestStateType, StateAction>();
	
	/// <summary>
	/// Quest icon on the left of the quest option button
	/// </summary>
	protected GUIStyle _questHintStyle;

    private GUIStyle _questButtonStyle;

    private Action<winDialogStory> closeCallback;
	
	#endregion


    #region Ctor

    public winDialogStory(string name, string caption, DialogStory story, Action<winDialogStory> OnCloseCallback)
        : base(name, new GUIContent(caption))
    {
		_questHintStyle = new GUIStyle();
        _questHintStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/NPC/exclamation");

        _questButtonStyle = new GUIStyle();
        _questButtonStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/Skins/button_quest");
        _questButtonStyle.hover.background = ResourceManager.Instance.LoadTexture2D("GUI/Skins/button_quest_hover");
        _questButtonStyle.active.background = ResourceManager.Instance.LoadTexture2D("GUI/Skins/button_quest_active");
        _questButtonStyle.font = ResourceManager.Instance.LoadFont(ResourceManager.FontComicSerif);
        _questButtonStyle.fontSize = 15;
        _questButtonStyle.normal.textColor = new Color(0.73f, 0.96f, 1f, 1f);
        _questButtonStyle.hover.textColor = Color.white;
        _questButtonStyle.alignment = TextAnchor.MiddleCenter;

        Story = story;
		
		IsDraggable = true;
        TweenDirection = WindowTweenDirection.FromTop;

        Initalize(name, caption, story, OnCloseCallback);
		
		Margin = new Rect(Margin.x, 30, Margin.width, Margin.height);
		
		MakeCenter(400, 300);
		
		_actions.Clear();
		_actions.Add(QuestStateType.None, DisplayQuestPassiveState);
		_actions.Add(QuestStateType.Normal, DisplayQuestNormalState);
		_actions.Add(QuestStateType.Active, DisplayQuestActiveState);
		_actions.Add(QuestStateType.Done, DisplayQuestDoneState);
    }

    #endregion


    #region Methods

    public void Initalize(string name, string caption, DialogStory story, Action<winDialogStory> OnCloseCallback)
    {
        Name = name;
        Caption = new GUIContent(caption);
        Story = story;
		Story.CurrentDialog = 0;

        closeCallback = OnCloseCallback;
    }

    public override void Draw(int id)
    {
        if (Story == null)
        {
            return;
        }

        GUI.BringWindowToFront(id);

        base.Draw(id);
		
		Dialog dlg = Story.Dialogs[(int)Story.CurrentDialog];
				
		float gap = 5;
		float buttonHeight = 30;
		float startOffset = WindowRect.height - gap - buttonHeight - Margin.height;
		
		if (QuestState != winDialogStory.QuestStateType.None)
		{
            bool isReturnable = QuestEngine.Instance.IsQuestReturnable(_activeQuestID);
            bool isQuestActive = QuestEngine.Instance.IsQuestActive(_activeQuestID);

            // Check if the dialog is on Quest Mode or Dialog mode
            //dlgIsQuest = isQuestActive || isReturnable ? true : false;
            if (isQuestActive)
            {
                dlgIsQuest = true;
            }
		}

        if (dlgIsQuest == false)
        {
            DisplayStandardDialog(dlg, startOffset, gap, buttonHeight);
        }
        else /// Dialog is now in Quest mode
        {
            QuestStateType state = QuestState;

            switch (state)
            {
                case QuestStateType.None:
                case QuestStateType.Done:
                case QuestStateType.Normal:
                    {
                        if (QuestEngine.Instance.IsQuestDateTimeRangeValid(_activeQuestID, PBGameMaster.GameTime))
                        {
                            _actions[state](gap, buttonHeight);
                        }
                        else
                        {
                            DisplayStandardDialog(dlg, startOffset, gap, buttonHeight);
                        }
                    } break;

                case QuestStateType.Active:
                    {
                        // Now if the quest is in active state, check for available item requirements
                        //Dictionary<string, int> equipments, inventories;
                        QuestReward reward;

                        // Check our quest item requirements
                        QuestEngine.Instance.GetQuestItemRequirements(_activeQuestID, out reward);//equipments, out inventories);

                        bool isValid = InventoryEngine.Instance.HasEquipments(reward.Equipments) && InventoryEngine.Instance.HasItems(reward.Inventories);

                        if (isValid)
                        {
                            _actions[QuestStateType.Active](gap, buttonHeight);
                        }
                        else
                        {
                            _actions[QuestStateType.Normal](gap, buttonHeight);
                        }

                    } break;

            }
        }
		
    }

    #endregion


    #region Helpers

    /// <summary>
    /// Render the current Dialog
    /// </summary>
    /// <param name="dlg">The Dialog we want to render</param>
    /// <param name="startOffset">Y start offset</param>
    /// <param name="gap">gap between buttons</param>
    /// <param name="buttonHeight">the button height</param>
    private void DisplayStandardDialog(Dialog dlg, float startOffset, float gap, float buttonHeight)
    {
        #region Standard Dialog Tree

        // Display the description of the dialog
        DisplayDescription(dlg.Description, gap, buttonHeight);

        for (int i = dlg.Options.Count - 1; i >= 0; i--)
        {
            DialogOption opt = dlg.Options[i];

            // Preliminary Quest option checking
            if (opt.Tipe == 1)
            {
                Quest quest = QuestEngine.Instance.GetQuest(opt.Next);

                // If no quest is found, then do not draw the option button, skip
                if (quest == null)
                {
                    continue;
                }

                // If the quest is not returnable to the NPC and the quest state is active, 
                if (quest.IsReturn == false && QuestState == QuestStateType.Active)
                {
                    continue;
                }

                // Skip, if the quest requirement is invalid
                if (QuestEngine.Instance.IsQuestRequirementValid(opt.Next) == false)
                {
                    continue;
                }

                // Skip, if the quest is already done
                if (quest.IsDone)
                {
                    continue;
                }
            }

            bool result = MakeButton(new Rect(Margin.x * 2, startOffset, WindowRect.width - Margin.width * 4, buttonHeight),
                                     opt);

            startOffset -= buttonHeight + gap;

            if (result)
            {
                OnSelected(opt.Tipe, opt.Next);
            }
        }

        #endregion
    }

    /// <summary>
    /// Detect the State of the Quest 
    /// </summary>
    /// <param name="questID">Quest ID</param>
    /// <returns>Quest State</returns>
    public QuestStateType GetState(int questID)
    {
        Quest quest = QuestEngine.Instance.GetQuest(questID);

        if (quest != null)
        {
            if (quest.IsDone)
            {
                return QuestStateType.Done;
            }

            if (quest.IsActive)
            {
                return QuestStateType.Active;
            }

            return QuestStateType.None;
        }

        return QuestStateType.None;
    }
	
    /// <summary>
    /// Close this Dialog Story
    /// </summary>
	public void Close()
	{
        if (closeCallback != null)
        {
            closeCallback(this);
        }

		Story.CurrentDialog = 0;
		Hide();
	}
	
	protected void DisplayDescription(string text, float gap, float buttonHeight) 
	{
        float height = WindowRect.height - gap - buttonHeight - Margin.height * 2;
		GUI.Label(new Rect(Margin.x, Margin.y, WindowRect.width - Margin.width - Margin.x, height), text);
	}

    protected void DisplayDescriptionWithRedeem(string text, Dictionary<string, int> redeems, float gap, float buttonHeight)
    {
        float height = WindowRect.height - gap - buttonHeight - Margin.height * 2 - 40f;
        GUI.Label(new Rect(Margin.x, Margin.y, WindowRect.width - Margin.width - Margin.x, height), text);
        
        string redeem = "We will send your Redeem Code(s) to your email!";
        /*foreach (string r in redeems.Keys)
        {
            redeem += r + " ";
        }*/
        GUI.Label(new Rect(Margin.x, Margin.y + height, WindowRect.width - Margin.width - Margin.x, 40), redeem);
    }

    /// <summary>
    /// Search Dialog by its ID, this is to easily find the next dialog to render
    /// </summary>
    /// <param name="nextId">The next ID</param>
    /// <returns>Dialog index</returns>
	protected int SearchDialogIndexById(int nextId)
	{
		for (int i = 0; i < Story.Dialogs.Count; i++)
		{
			Dialog dlg = Story.Dialogs[i];
			if (dlg.ID == nextId)
			{
				return i;
			}
		}
		return -1;
	}

	/// <summary>
	/// Selection test
	/// </summary>
	/// <param name="tipe">The type of the option, 0 - choice, 1 - quest</param>
	/// <param name="nextId"></param>
	private void OnSelected(int tipe, int nextId)
	{
        // If nextId == -1, then close this dialog
		if (nextId == -1)
		{
			Close();
		}
		
        // If user choose a choice, then search for next dialog and render it
		if (tipe == 0)
		{
			int nextIndex = SearchDialogIndexById(nextId);

            // Found a valid next index?
			if (nextIndex > -1)
			{
				Story.CurrentDialog = nextIndex;
			}
			else
			{
				Close();
			}
		}
	}

    /// <summary>
    /// Decoded an embedded urls inside string
    /// </summary>
    /// <param name="input"></param>
    /// <param name="outputText"></param>
    /// <returns></returns>
    private List<string> DecodeEmbeddedUrl(string input, out string outputText)
    {
        outputText = "";
        List<string> url = new List<string>();

        string currentUrl = "";
        bool beginUrlParse = false;
        for (int i = 0; i < input.Length; i++)
        {
            char currentChar = input[i];
            if (currentChar != '{' && currentChar != '}' && !beginUrlParse)
            {
                outputText += currentChar;
            }

            if (beginUrlParse && currentChar != '}')
            {
                currentUrl += currentChar;
            }

            if (currentChar == '{' && beginUrlParse == false)
            {
                beginUrlParse = true;
            }
            if (currentChar == '}' && beginUrlParse)
            {
                beginUrlParse = false;
                url.Add(currentUrl);
                currentUrl = "";
            }

        }

        return url;
    }

    /// <summary>
    /// Generate button based on the dialog option
    /// </summary>
    /// <param name="rect"></param>
    /// <param name="opt"></param>
    /// <returns></returns>
	private bool MakeButton(Rect rect, DialogOption opt)
	{
		bool result = false;

        List<string> urls = new List<string>();

		switch (opt.Tipe)
		{
		case DialogEngine.DIALOG_OPTION_CHOICE:	// Plain Choice
			{
				string content;
                urls = DecodeEmbeddedUrl(opt.Content, out content);
                
                float hintWidth = rect.height;
                
				result = GUI.Button(new Rect(rect.x + hintWidth, rect.y, rect.width - hintWidth * 2, rect.height), content);

                if (result)
                {
                    foreach (string url in urls)
                    {
                        if (Application.isWebPlayer)
                        {
                            Application.ExternalEval("window.open('" + url + "', '_blank')"); 
                        }
                        else
                        {
                            Application.OpenURL(url);
                        }
                    }
                }
			}
			break;
			
		case DialogEngine.DIALOG_OPTION_QUEST: // Quest button
			{
                // Get the quest
                Quest quest = QuestEngine.Instance.GetQuest(opt.Next);

                // Check if Quest required for this quest is valid
                bool isQuestValid = QuestEngine.Instance.IsQuestRequirementValid(opt.Next);

                // Check for date range
                bool isQuestDateTimeRangeValid = QuestEngine.Instance.IsQuestDateTimeRangeValid(opt.Next, PBGameMaster.GameTime);

                // Proceed only when requirements and date range valid
                if (quest != null && isQuestValid && isQuestDateTimeRangeValid)
				{
					float hintWidth = rect.height;
					
                    GUI.Box(new Rect(rect.x, rect.y, hintWidth-2, rect.height), "", _questHintStyle);

                    string desc;
                    urls = DecodeEmbeddedUrl(opt.Content, out desc);//(quest.Description, out desc);

                    // Render the button
                    result = GUI.Button(new Rect(rect.x + hintWidth, rect.y, rect.width - hintWidth * 2, rect.height), desc, _questButtonStyle);

					if (result)
					{
						dlgIsQuest = true;
						_activeQuestID = quest.ID;
                        QuestState = GetState(_activeQuestID);

                        // Open the URLs
                        if (result)
                        {
                            foreach (string url in urls)
                            {
                                Application.OpenURL(url);
                            }
                        }
					}
				
				}
			}
			break;

        case DialogEngine.DIALOG_OPTION_QUIZ:
            {
                string content;
                urls = DecodeEmbeddedUrl(opt.Content, out content);

                float hintWidth = rect.height;

                result = GUI.Button(new Rect(rect.x + hintWidth, rect.y, rect.width - hintWidth * 2, rect.height), content, _questButtonStyle);
            }
            break;
		}

		return result;
	}
	
	#endregion
	

	#region Quest Related
	
	private void SetQuestStateByQuestID(int questID)
	{
        Quest quest = QuestEngine.Instance.GetQuest(_activeQuestID);
		if (quest != null)
		{
			if (quest.IsDone)
			{
				QuestState = winDialogStory.QuestStateType.Done;
			}
			else
			{
				QuestState = quest.IsActive ? QuestStateType.Active : QuestStateType.Normal;
			}
			return;
		}
		QuestState = winDialogStory.QuestStateType.None;
	}
	
	private void DisplayQuestPassiveState(float gap, float buttonHeight)
	{
        int energy = InventoryEngine.Instance.GetEquipmentCount(Equipments.EquipmentType.Energy);

        int energyReq = QuestEngine.Instance.GetEnergyRequirement(_activeQuestID);
        
        float startOffset = WindowRect.height - gap - buttonHeight - Margin.height;

        if (energy < energyReq)
        {
            DisplayDescription(Lang.Localized("Untuk menjalankan quest ini kamu butuh Energi ") + energyReq.ToString(), gap, buttonHeight);

            if (GUI.Button(new Rect(Margin.x * 2, startOffset, WindowRect.width - Margin.width * 4, buttonHeight),
                           Lang.Localized("OK")))
            {
                dlgIsQuest = false;
                Close();
            }
        }
        else
        {
            DisplayDescription(Lang.Localized("Mau terima Quest ini?"), gap, buttonHeight);

            if (GUI.Button(new Rect(Margin.x * 2, startOffset, WindowRect.width - Margin.width * 4, buttonHeight),
                           Lang.Localized("No")))
            {
                dlgIsQuest = false;
            }

            startOffset -= buttonHeight + gap;

            if (GUI.Button(new Rect(Margin.x * 2, startOffset, WindowRect.width - Margin.width * 4, buttonHeight),
                           Lang.Localized("Yes")))
            {
                QuestState = winDialogStory.QuestStateType.Normal;

                if (energyReq > 0)
                {
                    InventoryEngine.Instance.SyncEquipment("energy", energy - energyReq);
                }
            }
        }
	}
	
	private void DisplayQuestNormalState(float gap, float buttonHeight)
	{
        Quest quest = QuestEngine.Instance.GetQuest(_activeQuestID);
		
		if (quest == null) return;
		
		string desc = quest.DescriptionNormal;
		DisplayDescription(desc, gap, buttonHeight);
		
		float startOffset = WindowRect.height - gap - buttonHeight - Margin.height;
		
		if (GUI.Button(new Rect(Margin.x * 2, startOffset, WindowRect.width - Margin.width * 4, buttonHeight), 
		               Lang.Localized("OK")))
		{
			dlgIsQuest = false;
            QuestEngine.Instance.SetQuestActive(_activeQuestID, true);
			SetQuestStateByQuestID(_activeQuestID);
			
			if (quest.IsReturn == false)
			{
                Debug.Log("quest done");
                RewardPlayer(_activeQuestID);
                QuestEngine.Instance.SetQuestDone(_activeQuestID, true);
                QuestState = QuestStateType.None;
			}
			
			Close();
		}
	}
	
	private void DisplayQuestActiveState(float gap, float buttonHeight)
	{
        Quest quest = QuestEngine.Instance.GetQuest(_activeQuestID);
		
		if (quest == null) return;
		
        /// Deskripsi perintah untuk player untuk mencarikan sesuatu
		DisplayDescription(quest.DescriptionActive, gap, buttonHeight);
		
		float startOfffest = WindowRect.height - gap - buttonHeight - Margin.height;

        if (GUI.Button(new Rect(Margin.x * 2, startOfffest, WindowRect.width - Margin.width * 4, buttonHeight),
            Lang.Localized("Tidak")))
        {
            Close();
        }

        startOfffest -= buttonHeight + gap;

		if (GUI.Button(new Rect(Margin.x * 2, startOfffest, WindowRect.width - Margin.width * 4, buttonHeight),
            Lang.Localized("Ya")))
		{
			QuestState = winDialogStory.QuestStateType.Done;
		}

	}

    private Dictionary<string, int> rewardRedeems = null; 
	
	private void DisplayQuestDoneState(float gap, float buttonHeight)
	{
        Quest quest = QuestEngine.Instance.GetQuest(_activeQuestID);

        if (quest == null)
        {
            return;
        }

        bool isUsingRedeems = false;

        //Dictionary<string, int> rewardEquipments, rewardInventories;
        QuestReward reward;
        if (rewardRedeems == null)
        {            
            QuestEngine.Instance.GetRewards(_activeQuestID, out reward);//out rewardEquipments, out rewardInventories, out rewardRedeems);
            rewardRedeems = reward.AvatarRedeems;
        }
        
        if (rewardRedeems != null)
        {
            isUsingRedeems = rewardRedeems.Count > 0;
        }

        if (isUsingRedeems)
        {
            DisplayDescriptionWithRedeem(quest.DescriptionDone, rewardRedeems, gap, buttonHeight);
        }
        else
        {
            DisplayDescription(quest.DescriptionDone, gap, buttonHeight);
        }

		float startOfffest = WindowRect.height - gap - buttonHeight - Margin.height;
		
		if (GUI.Button(new Rect(Margin.x * 2, startOfffest, WindowRect.width - Margin.width * 4, buttonHeight),
		               Lang.Localized("OK")))
		{
            /***********************
             * VERSION 1 (OBSOLETE)
             ***********************
             
			// Steal the item from the inventory
            string code;
            int count;
            QuestEngine.GetQuestItemRequirementsv1(_activeQuestID, out code, out count);

            for (int i = 0; i < count; i++)
            {
                InventoryEngine.StealItem(code);
            }
            */

            //Dictionary<string, int> equipments, inventories;

            QuestEngine.Instance.GetQuestItemRequirements(_activeQuestID, out reward);//out equipments, out inventories);

            if (reward.InventoryCount() > 0) //inventories.Keys.Count > 0)
            {
                InventoryEngine.Instance.StealItems(reward.Inventories); 
            }

            if (reward.EquipmentCount() > 0)//equipments.Keys.Count > 0)
            {
                InventoryEngine.Instance.StealEquipments(reward.Equipments);
            }

            // Disable the dialog
			dlgIsQuest = false;

            QuestEngine.Instance.SetQuestDone(_activeQuestID, true);

			// Reset the state
			QuestState = winDialogStory.QuestStateType.None;

            RewardPlayer(_activeQuestID);

			// Clos the dialog
			Close();
		}
	}



    private void RewardPlayer(int questID)
    {
        //Dictionary<string, int> equipments, inventories;
        //List<string> redeems;
        QuestReward reward;

        if (QuestEngine.Instance.GetRewards(questID, out reward))//out equipments, out inventories, out redeems))
        {
            if (reward.AvatarRedeemCount() > 0)
            {
                var redeem = "";
                //for (int i = 0; i < reward.AvatarRedeems.Keys.Count; i++)
                foreach (string key in reward.AvatarRedeems.Keys)
                {
                    if (redeem != "")
                    {
                        redeem += ".";
                    }
                    redeem += key;
                }

                // Send the redeem code to player's email, multiple redeem codes separated by COLON
                Messenger<string>.Broadcast(Messages.REDEEM_AVATAR_GENERATE, redeem);
            }

            if (reward.InventoryCount() > 0)
            {
                // For Each inventory item code
                foreach (string item in reward.Inventories.Keys)
                {
                    // Increase N number of item
                    for (int count = 0; count < reward.Inventories[item]; count++)
                    {
                        // Max inventory can be piled is 9
                        if (InventoryEngine.Instance.Items.Count < 9)
                        {
                            //Debug.LogWarning("it: " + item + "" + inventories[item].ToString());
                            InventoryEngine.Instance.StoreItem(item, "", 0f, "");
                        }
                    }
                }
            }

            if (reward.EquipmentCount() > 0)
            {
                foreach (string eq in reward.Equipments.Keys)
                {
                    //Debug.LogWarning("eq: " + eq + " => " + equipments[eq].ToString());
                    int count = InventoryEngine.Instance.GetEquipmentCount(eq);
                    InventoryEngine.Instance.StoreEquipment(eq, reward.Equipments[eq] + count);
                }
            }
        }
    }

	#endregion
}
