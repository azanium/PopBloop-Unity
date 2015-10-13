using UnityEngine;
using UnityEditor;
using System.IO;

public class SaveFontTexture
{
    [MenuItem("PopBloop/GUI/Save Font Texture...")]
    public static void ExecuteSaveFontTexture()
    {
        Texture2D tex = Selection.activeObject as Texture2D;
        if (tex == null)
        {
            EditorUtility.DisplayDialog("No texture selected", "Please select a texture", "Cancel");
            return;
        }

        if (tex.format != TextureFormat.Alpha8)
        {
            EditorUtility.DisplayDialog("Wrong format", "Texture must be in uncompressed Alpha8 format", "Cancel");
            return;
        }

        // Convert Alpha8 texture to ARGB32 texture so it can be saved as a PNG
        var texPixels = tex.GetPixels();
        var tex2 = new Texture2D(tex.width, tex.height, TextureFormat.ARGB32, false);
        tex2.SetPixels(texPixels);

        // Save texture (WriteAllBytes is not used here in order to keep compatibility with Unity iPhone)
        var texBytes = tex2.EncodeToPNG();
        var fileName = EditorUtility.SaveFilePanel("Save font texture", "", "font Texture", "png");
        if (fileName.Length > 0)
        {
            FileStream f = new FileStream(fileName, FileMode.OpenOrCreate, FileAccess.Write);
            BinaryWriter b = new BinaryWriter(f);
            for (var i = 0; i < texBytes.Length; i++) b.Write(texBytes[i]);
            b.Close();
        }

        GameObject.DestroyImmediate(tex2);
    }
}