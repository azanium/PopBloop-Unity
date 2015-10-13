using UnityEngine;
using System.Collections;

using PB.Common;
using PB.Client;
using PB.Game;

/// <summary>
/// Class to detect Game Objects and send OnAction event when that object is interactive
/// </summary>
public class Detector 
{	
	#region MemVars & Props
	
	public GameControllerBase Game { get; set; }
	
	#endregion
	
	
	#region Ctor
	
	public Detector(GameControllerBase game)
	{
		this.Game = game;
	}

    public void Dispose()
    {
        PBThirdPersonController.OnPlayerCollided += new System.Action<ControllerColliderHit>(PBThirdPersonController_OnPlayerCollided);
    }

	
	#endregion
	
	
	#region Methods
	
	public void DetectObjects()
	{
		if (Game.Game.State != GameState.WorldEntered)
		{
			return;
		}
        /*
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
		}*/
	}

    private void ProcessClickInteraction(RaycastHit hit)
    {
        switch (hit.collider.tag)
        {
            case LevelConstants.TagNPC:
                {
                    ItemNPC npc = hit.collider.GetComponent<ItemNPC>();
                    npc.OnAction(Game);
                }
                break;

            case LevelConstants.TagItem:
                {
                    ItemBase item = hit.collider.GetComponent<ItemBase>();

                    if (item != null)
                    {
                        item.OnAction(Game);
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

    void PBThirdPersonController_OnPlayerCollided(ControllerColliderHit obj)
    {
        Debug.LogWarning("Collided: " + obj.collider.name);
    }

	#endregion
}
