using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Client;
using PB.Common;
using System;

[AddComponentMenu("PopBloop Scripts/Items/NPC")]
public class ItemNPC : ItemBase
{
    #region MemVars & Props

    public override ItemActionType ActionType
    {
        get { return ItemActionType.Talk; }
    }

    public override ItemBaseType ItemType
    {
        get
        {
            return ItemBaseType.NPC;
        }
    }

    public AnimationClip animationQuest;
    public WrapMode animationQuestWrap = WrapMode.Loop;
    public AnimationClip animationTalk;
    public WrapMode animationTalkWrap = WrapMode.Loop; 

    public string npcName = "Dummy";
    public string dialogStoryName = "";
    public float interactionDistance = 2f;
    public float hintHeight = 2.2f;
    public float questIconVisibleDistance = 20f;
    public float itemTextHeight = 0.3f;
    public int shoutBubbleFontSize = 11;
    //public bool drawGizmo = true;
    public string TooFarText = "Jarak anda terlalu jauh untuk berbicara!";
    public float shadowVisibleDistance = 15f;
    public bool autoRotateWhenTalk = true;
    public bool isDistanceInXZ = true;

    private DialogStory _story;
    private GameObject _questIcon = null;
    private Rect _hintRect;
    //private List<string> _animations = new List<string>();
    private Quaternion _rotation;
    private Quaternion _desiredRotation;

    private UIChatBubble _shoutBubble;
    private Vector3 _avatarNamePosition;
    private Projector _blobProjector;
    private Renderer _renderer;

    private UIHoverPlane _hoverIcon;

    #endregion


    #region MonoBehavior Events

    protected override void Start()
    {
        base.Start();

        _desiredRotation = _rotation = transform.rotation;

        PrepareGUI();

        float textOffset = hintHeight;
        if (this.collider != null)
        {
            // Get the character collider bounds
            Bounds bounds = this.collider.bounds;

            // Set the target position just above the collider bound size
            textOffset = bounds.size.y + 0.3f;
        }

        
        // Prepare our NPC Name Text
        Vector3 textPos = new Vector3(transform.position.x, transform.position.y + textOffset, transform.position.z);

        _questIcon = (GameObject)AssetsManager.Instantiate(Resources.Load("Prefabs/QuestIcon"), textPos, transform.rotation);
        _questIcon.transform.parent = transform;
        _questIcon.AddComponent<UIBillboard>();

        // Prepare NPC Quest Icon
        _hoverIcon = this.GetComponentInChildren<UIHoverPlane>();
        if (_hoverIcon == null)
        {
            Debug.LogWarning("ItemNPC: An NPC must have a hover icon attached to the NPCName, please attach it!");
        }

        // Perpare NPC shout bubble

        _shoutBubble = this.gameObject.GetComponent<UIChatBubble>();
        if (_shoutBubble == null)
        {
            _shoutBubble = this.gameObject.AddComponent<UIChatBubble>();
        }
        _shoutBubble.bubbleHeight = textOffset + 0.2f;

        if (this.gameObject.animation != null)
        {
            foreach (AnimationState state in this.gameObject.animation)
            {
                if (_animations.ContainsKey(state.clip.name) == false)
                {
                    _animations.Add(state.clip.name, state.clip);
                }
            }

            Animate(animationIdle, WrapMode.Loop, true);
            Animate(animationQuest, WrapMode.Loop, false);
            Animate(animationTalk, WrapMode.Loop, false);

        }
        else
        {
            //Debug.LogWarning("ItemNPC: " + npcName + " doesn't have animations");
        }

        _blobProjector = GetComponentInChildren<Projector>();
        _renderer = GetComponentInChildren<Renderer>();

        DialogEngine.Instance.RegisterDialogStory(dialogStoryName);
    }

    // Update is called once per frame
    protected override void Update()
    {
        base.Update();
        //DetectProximity();

        float angle = Quaternion.Angle(_rotation, _desiredRotation);
        if (angle >= 0.01f)
        {
            _rotation = Quaternion.Slerp(_rotation, _desiredRotation, Time.deltaTime * 5.0f);
            transform.rotation = _rotation;
        }

        // Check for blob projector visibility
        if (_blobProjector != null)
        {
            bool isVisible = _renderer != null ? _renderer.isVisible : true;

            _blobProjector.enabled = isVisible ? CalcDistance(gameObject.transform.position, PBGameMaster.PlayerPosition) <= shadowVisibleDistance : false;
        }

        Quest quest;
        int questID;

        // Reset hover icon
        if (_hoverIcon != null)
        {
            _hoverIcon.HoverType = UIHoverPlane.eHoverType.None;
        }

        /// Check if our Dialog Story has valid quest in it, if it does then display quest icon
        if (HasValidQuest(this.dialogStoryName, out quest, out questID) && CalcDistance(gameObject.transform.position, PBGameMaster.PlayerPosition) <= questIconVisibleDistance)
        {
            if (quest == null)
            {
                return;
            }

            // If the quest is already done or the date time range not valid, then don't display icon
            if (quest.IsDone || QuestEngine.Instance.IsQuestDateTimeRangeValid(questID, PBGameMaster.GameTime) == false)
            {
                return;
            }

            //if (QuestEngine.IsItemRequirementsValidv1(questID)) // version 1 obsolete
            if (_hoverIcon != null)
            {
                if (QuestEngine.Instance.IsItemRequirementsValid(questID))
                {
                    _hoverIcon.HoverType = UIHoverPlane.eHoverType.QuestionMark;
                }
                else
                {
                    _hoverIcon.HoverType = UIHoverPlane.eHoverType.Exclamation;
                }
            }
        }

        //UpdatePositions();
    }

    /*void OnGUI()
    {
        Quest quest;
        int questID;

        /// Check if our Dialog Story has valid quest in it, if it does then display quest icon
        if (HasValidQuest(_story, out quest, out questID) && CalcDistance(gameObject.transform.position, GameMaster.Instance.PlayerPosition) <= QuestIconVisibleDistance)
        {
            if (quest == null) return;

            // If the quest is already done, then don\"t display icon
            if (quest.IsDone) return;

            if (QuestEngine.IsItemRequirementsValidv1(questID))
            {
                ShowIcon(transform.position, _questActiveStyle);
            }
            else
            {
                ShowIcon(transform.position, _questFoundStyle);
            }
        }
    }*/

    #endregion


    #region Internal Methods

    /*public void SetPosition(Vector3 position, Vector3 rotation)
    {
        _newPosition = position;
        _newRotation = rotation;
        _updateNewPosition = true;
        _pendingUpdatePosition = true;
    }

    protected void UpdatePositions()
    {
        if (PBGameMaster.Game == null || syncTransforms == false || PBGameMaster.GameState != ControllerEventState.WorldEntered)
        {
            return;
        }

        Game game = PBGameMaster.Game;

        float time = Time.time;
        if (time >= _nextMoveTime && !_pendingUpdatePosition)
        {
            Vector3 position = this.transform.position;
            Vector3 rotation = this.transform.rotation.eulerAngles;

            if (_lastMovePosition != position || _lastMoveRotation != rotation)
            {
                if (Mathf.Abs(Vector3.Distance(position, _lastMovePosition)) > 0.1f || (Mathf.Abs(Quaternion.Angle(Quaternion.Euler(_lastMoveRotation), Quaternion.Euler(rotation))) > 0.05f))
                {
                    game.Avatar.GameItemMoveAbsolute(this.gameObject.name, PlayerActor.GetPosition(position), PlayerActor.GetRotation(rotation));

                    _lastMovePosition = position;
                    _lastMoveRotation = rotation;
                }
            }

            _nextMoveTime = time + (syncInterval * 0.001f);
        }

        if (_updateNewPosition)
        {
            Vector3 position = this.transform.position;
            Vector3 rotation = this.transform.rotation.eulerAngles;
            //Debug.LogWarning(string.Format("new rotation: {0}, {1}, {2} => Current: {3}, {4}, {5}", _newRotation.x, _newRotation.y, _newRotation.z, rotation.x, rotation.y, rotation.z));
            //Debug.LogWarning(string.Format("new position: {0}, {1}, {2} => Current: {3}, {4}, {5}", _newPosition.x, _newPosition.y, _newPosition.z, position.x, position.y, position.z));

            // Smooth interpolate the current position into foreign new position
            bool positionUpdateDone = false;
            if (Mathf.Abs(Vector3.Distance(_newPosition, position)) > 0.05f)
            {
                transform.position = Vector3.Lerp(position, _newPosition, Time.deltaTime * 7.0f);
            }
            else
            {
                positionUpdateDone = true;
            }

            // Smooth interpolate the current rotation into foreign new rotation
            bool rotationUpdateDone = false;
            if (Mathf.Abs(Quaternion.Angle(Quaternion.Euler(rotation), Quaternion.Euler(_newRotation))) > 0.05f)
            {
                transform.rotation = Quaternion.Slerp(Quaternion.Euler(rotation), Quaternion.Euler(_newRotation), Time.deltaTime * 7.0f);
            }
            else
            {
                rotationUpdateDone = true;
            }

            // If all interpolation done, then reset 
            if (positionUpdateDone && rotationUpdateDone)
            {
                _updateNewPosition = false;
                _pendingUpdatePosition = false;
                _lastMovePosition = transform.position;
                _lastMoveRotation = transform.rotation.eulerAngles;
            }
        }
    }*/

    IEnumerator DownloadDialog()
    {
        string url = PopBloopSettings.GetDialogStoryURL(dialogStoryName) + "/" + Time.frameCount.ToString();

        if (PopBloopSettings.useLogs)
        {
            Debug.Log("ItemNPC: Downloading DialogStory => " + url);
        }

        WWW www = new WWW(url);

        yield return www;

        if (www.error == null)
        {
            // "{"Name":"demo99","Dialogs":[{"ID":"0","Description":"","Options":[{"Tipe":"1","Content":"Quest","Next":"99"},{"Tipe":"0","Content":"Exit","Next":"-1"}]}]}"
            // "{\"Name\":\"Dummy\",\"Dialogs\":[{\"ID\":0,\"Description\":\"Hello, Do you want to eat?\",\"Options\":[{\"Tipe\":1,\"Content\":null,\"Next\":1},{\"Tipe\":0,\"Content\":\"Yes, I'd love to\",\"Next\":1},{\"Tipe\":0,\"Content\":\"No Thanks, I'm full\",\"Next\":-1}]},{\"ID\":1,\"Description\":\"Here is your pizza\",\"Options\":[{\"Tipe\":0,\"Content\":\"Nice, thanks!\",\"Next\":-1}]}]}";//www.text;
            string json = www.text.Trim();

            try
            {
                _story = DialogStory.ParseFromJSON(json);
            }
            catch (Exception ex)
            {
                Debug.LogWarning(ex.ToString());
            }

            if (_story != null)
            {
                if (_story.Dialogs != null)
                {
                    if (_story.Dialogs.Count > 0)
                    {
                        Debug.Log("ItemNPC: Dialog Story '" + dialogStoryName + "' JSON Convert success: JSON: " + json);
                    }
                }
                else
                {
                    _story = null;
                    Debug.LogWarning("ItemNPC: Dialog Story's '" + dialogStoryName + "' Dialogs is null");
                }
            }
            else
            {
                Debug.LogWarning("ItemNPC: Dialog Story '" + dialogStoryName + "' JSON Convert Failed: JSON: " + json);
            }
        }

    }

    public void ShowDialog()
    {
        if (DialogEngine.Instance.IsDialogStoryValid(dialogStoryName))
        {
            _readyToUse = false;

            Animate(animationQuest, animationQuestWrap, true);

            string dialogName = string.IsNullOrEmpty(npcName) ? dialogStoryName : npcName;

            WindowManager.CreateDialogStory(dialogName, npcName, DialogEngine.Instance.GetDialogStory(dialogStoryName), (dlg) =>
            {
                _readyToUse = true;
            });
        }
    }

    private void PutBox(Rect rect, Vector3 position, GUIStyle style)
    {
        Vector3 screenPoint = Camera.main.WorldToScreenPoint(position);

        if (screenPoint.z > 0)
        {
            Rect r = new Rect();
            r.x = screenPoint.x - rect.width / 2;
            r.y = Screen.height - screenPoint.y - r.height;
            r.width = rect.width;
            r.height = rect.height;

            GUI.Box(r, "", style);
        }
    }

    private void PrepareGUI()
    {
        _hintRect = new Rect(0, 0, 32, 32);
    }

    private void ShowIcon(Vector3 position, GUIStyle style)
    {
        PutBox(_hintRect, new Vector3(position.x, position.y + hintHeight, position.z), style);
    }

    private bool HasValidQuest(string dialogName, out Quest quest, out int questid)
    {
        if (DialogEngine.Instance.IsDialogStoryValid(dialogName))
        {
            DialogStory story = DialogEngine.Instance.GetDialogStory(dialogName);

            quest = null;
            questid = -1;
            if (story.Dialogs == null)
            {
                return false;
            }

            if (story.Dialogs.Count == 0)
            {
                return false;
            }

            Dialog dlg = story.Dialogs[story.CurrentDialog];

            int questID = -1;
            foreach (DialogOption opt in dlg.Options)
            {
                if (opt.Tipe == 1)
                {
                    questID = opt.Next;
                    break;
                }
            }

            if (questID > -1)
            {
                quest = QuestEngine.Instance.GetQuest(questID);
                questid = questID;

                return QuestEngine.Instance.IsQuestRequirementValid(questID);
            }

        }

        quest = null;
        questid = -1;

        return false;
    }

    /*protected float CalcDistance(Vector3 from, Vector3 to)
    {
        return Mathf.Abs(Vector3.Distance(new Vector3(from.x, 0f, from.z), new Vector3(to.x, 0f, to.z)));
    }


    protected void DetectProximity()
    {
        Vector3 player = isDistanceInXZ ? new Vector3(PBGameMaster.PlayerPosition.x, 0, PBGameMaster.PlayerPosition.z) : PBGameMaster.PlayerPosition;
        Vector3 item = isDistanceInXZ ? new Vector3(gameObject.transform.position.x, 0, gameObject.transform.position.z) : gameObject.transform.position;

        float proximity = Mathf.Abs(Vector3.Distance(player, item));

        if (proximity <= interactionDistance)
        {
            if (!_isProximityCalled)
            {
                OnProximityIn();
                _isProximityCalled = true;
            }
        }
        else
        {
            if (_isProximityCalled)
            {
                OnProximityOut();
                _isProximityCalled = false;
            }
        }
    }*/

    protected bool _isValidToInteract = false;

    public override void OnProximityIn()
    {
        base.OnProximityIn();

        _isValidToInteract = true;
    }

    public override void OnProximityOut()
    {
        base.OnProximityOut();

        _isValidToInteract = false;
        Animate(animationIdle, animationIdleWrap, true);
    }

    public override void OnAction(GameControllerBase game)
    {
        base.OnAction(game);

        if (_isValidToInteract == false)//(CalcDistance(transform.position, game.Avatar.transform.position) > interactionDistance)
        {
            if (_shoutBubble.IsShowing == false)
            {
                _shoutBubble.ShowMessage(TooFarText);
            }
        }
        else
        {
            // Calculate desired rotation that will face the NPC to the Player
            if (autoRotateWhenTalk)
            {
                _desiredRotation = Quaternion.LookRotation(CalcDirection(gameObject.transform.position, game.Avatar.transform.position));

                // Force to update to sync now
                _updateNewPosition = false;
            }

            Animate(animationTalk, animationTalkWrap, true);

            ShowDialog();
        }
    }

    private void Reset()
    {
        CapsuleCollider collider = this.gameObject.GetComponent<CapsuleCollider>();
        if (collider != null)
        {
            collider.center = new Vector3(0.0f, 1.0f, 0.0f);
            collider.radius = 0.2f;
            collider.height = 2.0f;
        }
        else
        {
            Debug.LogWarning("NPC must have at least capsule collider attached!");
        }
    }


    #endregion
}
