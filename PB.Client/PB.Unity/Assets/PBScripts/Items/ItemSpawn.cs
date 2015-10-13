using UnityEngine;
using System.Collections;

public class ItemSpawn : MonoBehaviour
{
    #region MemVars & Props

    public string spawnId;
    public string groupName;

    #endregion


    #region MonoBehavior's Methods

    void OnDrawGizmos()
    {
        Gizmos.DrawIcon(transform.position, "spawn_gizmo.png");
    }

    #endregion


    #region Public Methods

    #endregion


    #region Private Methods

    #endregion
}
