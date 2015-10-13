/**********************************************/
/*
/* Unity Project 
/*
/* (C) 2011, 2012 Suhendra Ahmad
/*
/**********************************************/

using UnityEngine;

using System.Collections;
using System.Collections.Generic;

public class PBDefaults
{
    private List<string> preferences = new List<string>();
    static private Dictionary<string, PBDefaults> profiles = new Dictionary<string, PBDefaults>();

    static public PBDefaults GetProfile(string profileName)
    {
        if (profiles.ContainsKey(profileName))
        {
            return profiles[profileName];
        }
        else
        {
            PBDefaults profile = new PBDefaults();

            profiles.Add(profileName, profile);

            return profile;
        }
    }

    public void SetFloat(string key, float value)
    {
        PlayerPrefs.SetFloat(key, value);
        SavePreference(key);
    }

    public float GetFloat(string key)
    {
        return PlayerPrefs.GetFloat(key);
    }

    public void SetInt(string key, int value)
    {
        PlayerPrefs.SetInt(key, value);
        SavePreference(key);
    }

    public int GetInt(string key)
    {
        return PlayerPrefs.GetInt(key);
    }

    public void SetBool(string key, bool value)
    {
        PlayerPrefs.SetInt(key, value ? 1 : -1);
        SavePreference(key);
    }

    public bool GetBool(string key)
    {
        bool result = PlayerPrefs.GetInt(key) == 1 ? true : false;

        return result;
    }

    public void SetString(string key, string value)
    {
        PlayerPrefs.SetString(key, value);
        SavePreference(key);
    }

    public string GetString(string key)
    {
        return PlayerPrefs.GetString(key);
    }

    private void SavePreference(string key)
    {
        if (preferences.Contains(key) == false)
        {
            preferences.Add(key);
        }
        PlayerPrefs.Save();
    }

    private void RemovePrefence(string key)
    {
        if (preferences.Contains(key))
        {
            preferences.Remove(key);
        }
        PlayerPrefs.DeleteKey(key);
    }

    public void Remove(string key)
    {
        RemovePrefence(key);
    }

    public void Clear()
    {
        foreach (string key in preferences)
        {
            if (PlayerPrefs.HasKey(key))
            {
                PlayerPrefs.DeleteKey(key);
            }
        }
        preferences.Clear();
    }

    public void Dispose()
    {
        Debug.LogError("Disposeee");
    }
}
