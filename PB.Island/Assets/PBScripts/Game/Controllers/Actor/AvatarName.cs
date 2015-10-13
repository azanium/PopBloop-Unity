using UnityEngine;
using System.Collections;

[RequireComponent(typeof(TextMesh))]
public class AvatarName : MonoBehaviour 
{
    public string Text = "Noname";
    public int MaxTextLength = 25;

    private Camera _camera;
    private TextMesh _mesh;

	// Use this for initialization
	void Start () 
    {
        _camera = Camera.main;
        if (_camera == null)
        {
            Debug.LogError("AvatarName: No Main Camera defined!");
        }

        _mesh = GetComponent<TextMesh>();

        string displayText = Text;
        if (displayText.Length > MaxTextLength - 1)
        {
            displayText = displayText.Substring(0, MaxTextLength - 1) + "..";
        }
        _mesh.text = displayText;

        UIHoverPlane hover = GetComponentInChildren<UIHoverPlane>();
        if (hover != null)
        {
            hover.gameObject.transform.localScale = new Vector3(10, 10, 1);
        }
	}
	
	// Update is called once per frame
	void LateUpdate () 
    {
        if (_camera == null)
        {
            _camera = Camera.main;
        }

        if (_camera != null)
        {
            Vector3 direction = _camera.transform.position - transform.position;
            transform.LookAt(transform.position - direction);
        }
	}

    public void SetColor(Color color)
    {
        _mesh = gameObject.GetComponent<TextMesh>();
        if (_mesh != null)
        {            
            _mesh.renderer.sharedMaterial.color = color;
        }
    }

    public void SetName(string name)
    {
        _mesh.text = name;
    }
}
