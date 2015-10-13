using UnityEngine;

using System.Collections;
using System.Collections.Generic;
using PB.Client;

/*
 * API for Get List Banner
 * Methode: GET
 * Urutan Parameter
 * 1. Start Page from
 * 2. Limit Data
 * Example : [server host]/[server path]/api/mobile/banner/list/0/10/
 * Return : JSON
 */

public class Banners : JsonModel<Banners>
{
    public class Banner
    {
        public string ID { get; set; }
        public string name { get; set; }
        public string descriptions { get; set; }
        public string type { get; set; }
        public string urlPicture { get; set; }
        public string dataValue { get; set; }
        public string picture { get; set; }
    }

    public int count { get; set; }
    public List<Banner> data { get; set; }

    public static string GetBannersUrl(int startIndex, int limitData)
    {
        string api = string.Format("{0}api/mobile/banner/list/{1}/{2}", PopBloopSettings.WebServerUrl, startIndex, limitData);

        return api;
    }

    public static string GetBannersImagePath(string image)
    {
        return string.Format("{0}bundles/preview_images/{1}", PopBloopSettings.WebServerUrl, image);
    }

}
