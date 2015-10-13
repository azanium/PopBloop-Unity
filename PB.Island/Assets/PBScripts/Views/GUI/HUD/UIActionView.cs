using UnityEngine;
using System.Collections;
using PB.Client;
using PB.Common;
using PB.Game;

public class UIActionView : GUIBase
{
    #region MemVars & Props

    static private ItemBase activeItem;

    private Texture2D talkIcon;
    private Texture2D useIcon;
    private Texture2D pickupIcon;
    private Texture2D portalIcon;
    private Texture2D actionIcon;
    private Texture2D actionShadowIcon;

    private Rect commandPositionRect;
    private Rect commandTextPositionRect;
    private Rect hintPositionRect;
    private Rect hintTextPositionRect;
    private Rect actionShadowRect;

    private GUIStyle commandStyle;
    private GUIStyle hintStyle;
    private GUIContent hintContent;

    #endregion


    #region MonoBehavior's Methods

    public override void Awake()
    {
        base.Awake();

        activeItem = null;

        talkIcon = ResourceManager.Instance.LoadTexture2D("GUI/HUD/talk_icon");
        useIcon = ResourceManager.Instance.LoadTexture2D("GUI/HUD/use_icon");
        pickupIcon = ResourceManager.Instance.LoadTexture2D("GUI/HUD/pickup_icon");
        portalIcon = ResourceManager.Instance.LoadTexture2D("GUI/HUD/portal_icon");
        actionIcon = ResourceManager.Instance.LoadTexture2D("GUI/HUD/action_icon");
        actionShadowIcon = ResourceManager.Instance.LoadTexture2D("GUI/HUD/action_shadow");

        commandStyle = new GUIStyle();
        commandStyle.font = (Font)ResourceManager.Instance.LoadFont(ResourceManager.FontComicSerif);
        commandStyle.alignment = TextAnchor.MiddleCenter;
        commandStyle.fontSize = 20;
        commandStyle.normal.textColor = new Color(0.2f, 0.2f, 0.2f);

        hintStyle = new GUIStyle();
        hintStyle.font = (Font)ResourceManager.Instance.LoadFont(ResourceManager.FontDroidSansBold);
        hintStyle.alignment = TextAnchor.MiddleLeft;
        hintStyle.normal.textColor = Color.white;
        hintStyle.fontSize = 14;

        hintContent = new GUIContent();

        // Create our Rects here to avoid garbage collection

        commandPositionRect = new Rect(Screen.width * 0.5f - actionIcon.width * 0.5f, Screen.height - actionIcon.height - 5, actionIcon.width, actionIcon.height);
        commandTextPositionRect = new Rect(commandPositionRect.x + 36, commandPositionRect.y, commandPositionRect.width - 50, commandPositionRect.height);

        hintPositionRect = new Rect(0, 0, talkIcon.width, talkIcon.height);
        hintTextPositionRect = new Rect(0, 0, 100, 50);
        actionShadowRect = new Rect(0, 0, 100, 50);

    }

    public override void OnDestroy()
    {
    }

    public override void Start()
    {
        base.Start();
    }

    public override void Update()
    {
        base.Update();

        if (GameController.gameController == null)
        {
            return;
        }

        if (PBGameMaster.GameState != GameStateType.WorldEntered)
        {
            return;
        }

        if (activeItem != null)
        {
            if (activeItem.IsReadyToUse())
            {
                if (Input.GetKey(KeyCode.F))
                {
                    activeItem.OnAction(GameController.gameController);
                }
            }
        }

        DetectObjects();
    }

    public override void OnGUI()
    {
        base.OnGUI();

        if (PBGameMaster.GameState != GameStateType.WorldEntered)
        {
            return;
        }

        if (activeItem != null)
        {
            if (activeItem.IsReadyToUse() == false)
            {
                return;
            }

            Texture2D hintIcon = pickupIcon;
            string actionText = PBConstants.COMMAND_PICKUP;
            switch (activeItem.ActionType)
            {
                case ItemActionType.Talk:
                    hintIcon = talkIcon;
                    actionText = PBConstants.COMMAND_TALK;
                    break;

                case ItemActionType.Portal:
                    hintIcon = portalIcon;
                    actionText = PBConstants.COMMAND_TRAVEL;
                    break;

                case ItemActionType.Use:
                    hintIcon = useIcon;
                    actionText = PBConstants.COMMAND_USE;
                    break;
            }
            
            // Draw our command icon and text
            GUI.DrawTexture(commandPositionRect, actionIcon);
            GUI.Box(commandTextPositionRect, actionText, commandStyle);

            // First we calculate the target object bounding box and get its size
            Bounds offset;
            if (activeItem.gameObject.collider != null)
            {
                offset = activeItem.gameObject.collider.bounds;
            }
            else
            {
                offset = activeItem.gameObject.renderer.bounds;
            }

            // We add the position of the object with the bound box size
            Vector3 hintPos = activeItem.gameObject.transform.position;
            hintPos.y += offset.size.y;

            // Project our object hint 3D position to 2D 
            hintPos = CalcBubblePosition(hintPos);

            // Put the position at the center of the object
            hintPositionRect.x = hintPos.x - hintIcon.width * .5f;
            // position Y = object screen point - bubble size Y - total offset calculate from 0 index
            hintPositionRect.y = Screen.height - hintPos.y - (hintIcon.height * .5f) - 10;

            // Set the widht and height
            hintPositionRect.width = hintIcon.width;
            hintPositionRect.height = hintIcon.height;

            // If the position is visible to user, then draw it
            if (hintPos.z > 0)
            {                
                hintContent.text = activeItem.hintDescription;
                Vector2 hintTextSize = hintStyle.CalcSize(hintContent);

                hintTextPositionRect.x = hintPositionRect.x + hintIcon.width + 5;
                hintTextPositionRect.y = hintPositionRect.y;
                hintTextPositionRect.width = hintTextSize.x;
                hintTextPositionRect.height = hintTextSize.y + hintIcon.height * .5f;

                if (activeItem.hintDescription.Trim() != "")
                {
                    actionShadowRect.x = hintTextPositionRect.x - 5;
                    actionShadowRect.y = hintTextPositionRect.y + 8;// +hintTextPositionRect.height * .4f;
                    actionShadowRect.width = hintTextPositionRect.width + 10;
                    actionShadowRect.height = hintIcon.height - 10;

                    GUI.DrawTexture(actionShadowRect, actionShadowIcon);
                    GUI.Label(hintTextPositionRect, hintContent, hintStyle);
                }

                GUI.DrawTexture(hintPositionRect, hintIcon); 
            }

            
        }
    }
	
    #endregion


    #region Public Methods

    public static void EnableItem(ItemBase item)
    {
        if (activeItem != item)
        {
            activeItem = item;
        }
    }

    public static void DisableItem(ItemBase item)
    {
        if (activeItem == item)
        {
            activeItem = null;
        }
    }

    #endregion


    #region Private Methods

    public void DetectObjects()
    {
        if (Input.GetMouseButtonDown(0) == true && Input.GetKey(KeyCode.LeftControl) == false && Input.GetKey(KeyCode.RightControl) == false)
        {
            Ray ray = Camera.main.ScreenPointToRay(Input.mousePosition);

            RaycastHit hit;
            if (Physics.Raycast(ray, out hit))
            {
                Vector3 screenPos = Camera.main.WorldToScreenPoint(hit.point);
                if (WindowManager.IsPointOutsideGUI(screenPos) && GUIUtility.hotControl == 0)
                {
                    ProcessClickInteraction(hit);
                }
            }
        }
    }

    private void ProcessClickInteraction(RaycastHit hit)
    {
        GameControllerBase game = GameController.gameController;
        switch (hit.collider.tag)
        {
            case LevelConstants.TagNPC:
                {
                    ItemNPC npc = hit.collider.GetComponent<ItemNPC>();
                    npc.OnAction(game);
                }
                break;

            case LevelConstants.TagItem:
                {
                    ItemBase item = hit.collider.GetComponent<ItemBase>();

                    if (item != null)
                    {
                        if (item == activeItem)
                        {
                            if (item.IsReadyToUse())
                            {
                                item.OnAction(game);
                            }
                        }
                    }
                }
                break;

            case LevelConstants.TagTerrain:
                {
                    float height = hit.point.y;

                    if (Application.platform != RuntimePlatform.FlashPlayer)
                    {
                        if (Terrain.activeTerrain != null)
                        {
                            // If we are hitting the actual terrain, then get the terrain height to avoid floating waypoint on the tree
                            if (hit.collider.name.ToLower() == Terrain.activeTerrain.name.ToLower())
                            {
                                height = Terrain.activeTerrain.SampleHeight(hit.point);
                            }
                        }
                    }

                    Messenger<Vector3>.Broadcast(Messages.PLAYER_MOVETO, new Vector3(hit.point.x, height, hit.point.z));
                }
                break;
        }
    }

    private Vector3 CalcBubblePosition(Vector3 position)
    {
        if (Camera.mainCamera != null)
        {
            return Camera.mainCamera.WorldToScreenPoint(position);
        }

        return new Vector3(Screen.width + 10, Screen.height + 10); // Put nowhere to be seen
    }


    #endregion
}
