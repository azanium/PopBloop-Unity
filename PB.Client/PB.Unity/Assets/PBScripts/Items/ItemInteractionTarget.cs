using UnityEngine;
using System.Collections;

public class ItemInteractionTarget : MonoBehaviour
{
    public ItemInteraction.ItemInteractionType interactionType = ItemInteraction.ItemInteractionType.Sit;

    void OnDrawGizmos()
    {
        if (interactionType == ItemInteraction.ItemInteractionType.Sit)
        {
            Gizmos.DrawIcon(transform.position, "sit_gizmo.png", true);
        }
        else if (interactionType == ItemInteraction.ItemInteractionType.Sleep)
        {
            Gizmos.DrawIcon(transform.position, "sleep_gizmo.jpg", true);
        }
    }
}
