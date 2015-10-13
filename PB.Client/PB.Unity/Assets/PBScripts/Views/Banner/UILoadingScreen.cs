using UnityEngine;

public class UILoadingScreen : MonoBehaviour
{
	private Texture2D _loadingScreenTexture = null;
	
	void Start()
	{
		LoadTexture();
	}
	
	void OnGUI()
	{
		if (_loadingScreenTexture != null)
		{
			
		}
	}
	
	private void LoadTexture()
	{
		_loadingScreenTexture = ResourceManager.Instance.LoadTexture2D("Materials/LoadingScreen");
	}
	
	private void DisposeTexture()
	{
		if (_loadingScreenTexture != null)
		{
			Object.DestroyImmediate(_loadingScreenTexture);
		}
	}
}
