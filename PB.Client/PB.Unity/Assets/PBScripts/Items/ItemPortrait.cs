using UnityEngine;
using System.Collections;
using PB.Client;

public class ItemPortrait : MonoBehaviour 
{
    public string textureName;

    private Material material;

	void Start() 
    {
        if (renderer != null)
        {
            material = renderer.material;
            StartCoroutine(loadProtrait());
        }
	}

    IEnumerator loadProtrait()
    {
        WWW www = new WWW(PopBloopSettings.MaterialsBundleBaseURL + "logo.png");

        yield return www;

        if (www.error == null)
        {
            material.mainTexture = www.texture;
        }
    }
	
	void Update() 
    {
	
	}
}
