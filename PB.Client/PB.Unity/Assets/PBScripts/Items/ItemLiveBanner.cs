using UnityEngine;
using System.Collections;
using System.Collections.Generic;

using PB.Client;
using System.Xml.Serialization;
using System;

public class ItemLiveBanner : MonoBehaviour
{
    #region MemVars & Props

    [Serializable]
    public class BannerItem
    {
        public string code = "";

        public string version = "";

        [NonSerialized]
        public Texture2D texture = null;
    }

    [Serializable]
    public class Banner
    {
        public List<BannerItem> Items = new List<BannerItem>();
        public Banner()
        {
        }
    }

    public static void SerializeBanner(Banner banner)
    {
        //XmlSerializer serializer = new XmlSerializer(typeof(Banner));
        
    }

    public string code = "default";
    public int checkIntervalInSeconds = 10;

    private System.Timers.Timer _timer;
    private Renderer _renderer;

    #endregion


    #region Mono Methods

    private void Start()
    {
        _timer = new System.Timers.Timer(checkIntervalInSeconds * 1000);
        _timer.Elapsed += new System.Timers.ElapsedEventHandler(_timer_Elapsed);
        _timer.Start();

        _renderer = GetComponent<Renderer>();
        if (_renderer != null)
        {

        }
    }

    private void Update()
    {
    }

    #endregion


    #region Methods

    private void _timer_Elapsed(object sender, System.Timers.ElapsedEventArgs e)
    {
        
    }
    
    #endregion
}
