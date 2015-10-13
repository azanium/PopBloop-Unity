using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

public class Lang
{
    private static Dictionary<string, string> _langDictionary = new Dictionary<string, string>();

    /// <summary>
    /// Load the language
    /// </summary>
    /// <param name="code">Language Code</param>
    /// <returns>true if succesfull, false otherwise</returns>
    public static bool LoadLanguage(string code)
    {
        _langDictionary.Clear();

        return true;
    }

    /// <summary>
    /// Return a localized text
    /// </summary>
    /// <param name="text">the text which we want to be localized</param>
    /// <returns>the localized text</returns>
    public static string Localized(string text)
    {
        if (_langDictionary.ContainsKey(text))
        {
            return _langDictionary[text];
        }

        return text;
    }
}

