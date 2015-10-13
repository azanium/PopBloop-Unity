using UnityEngine;
using System.Collections;

[AddComponentMenu("PopBloop Scripts/Fx/UV Scroller")]
public class UVScroller : MonoBehaviour 
{
    public float scrollSpeed = 0.1f;

	void Update() 
    {
        if (renderer == null || Camera.main == null)
        {
            return;
        }

        if (renderer.material.shader.isSupported)
        {
            Camera.main.depthTextureMode |= DepthTextureMode.Depth;
        }

        var offset = Time.time * scrollSpeed;

        renderer.material.SetTextureOffset("_BumpMap", new Vector2(offset / -7.0f, offset));
        renderer.material.SetTextureOffset("_MainTex", new Vector2(offset / 10.0f, offset));
	}
}
