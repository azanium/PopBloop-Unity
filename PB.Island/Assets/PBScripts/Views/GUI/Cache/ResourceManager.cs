using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class ResourceManager
{
    #region MemVars & Props

    #region Font Keys

    public static string FontDroidSans = "GUI/Fonts/DroidSans";
    public static string FontDroidSansBold = "GUI/Fonts/DroidSans-Bold";
    public static string FontChunkfive = "GUI/Fonts/Chunkfive";
    public static string FontSulphur = "GUI/Fonts/SULPHUR";
    public static string FontComicSerif = "GUI/Fonts/ComicSerif";
    public static string FontABeeZeeRegular = "ABeeZee-Regular";
    
    #endregion

    public static readonly ResourceManager Instance = new ResourceManager();

    private Dictionary<string, Font> _fontsCache = new Dictionary<string, Font>();
    private Dictionary<string, Texture2D> _textureCache = new Dictionary<string, Texture2D>();
    private List<Texture2D> _snapshots = new List<Texture2D>();

    #endregion


    #region Ctor

    #endregion


    #region Methods

    public Font LoadFont(string fontResource)
    {
        if (_fontsCache.ContainsKey(fontResource))
        {
            return _fontsCache[fontResource];
        }

        Font font = (Font)Resources.Load(fontResource);
        _fontsCache.Add(fontResource, font);

        return font;
    }

    public Texture2D LoadTexture2D(string textureResource)
    {
        if (_textureCache.ContainsKey(textureResource))
        {
            return _textureCache[textureResource];
        }

        Texture2D texture = (Texture2D)Resources.Load(textureResource);
        _textureCache.Add(textureResource, texture);

        return texture;
    }

    public Texture2D Snapshot()
    {
        // Create a texture the size of the screen, RGB24 format
        var width = Screen.width;
        var height = Screen.height;
        var tex = new Texture2D(width, height, TextureFormat.RGB24, false);

        // Read screen contents into the texture
        tex.ReadPixels(new Rect(0, 0, width, height), 0, 0);
        tex.Apply();

        return tex;
    }

    public void Clear()
    {
        foreach (string fontName in _fontsCache.Keys)
        {
            Font font = _fontsCache[fontName];
            if (font != null)
            {
                UnityEngine.Object.Destroy(font);
            }
        }
        _fontsCache.Clear();

        foreach (string textureName in _textureCache.Keys)
        {
            Texture2D texture = _textureCache[textureName];

            if (texture != null)
            {
                UnityEngine.Object.Destroy(texture);
            }
        }
        _textureCache.Clear();

        foreach (Texture2D snap in _snapshots)
        {
            UnityEngine.Object.Destroy(snap);
        }
        _snapshots.Clear();
    }

    #endregion

}
