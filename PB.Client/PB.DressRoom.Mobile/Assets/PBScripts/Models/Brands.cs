using PB.Client;
using System.Collections;
using System.Collections.Generic;

public class Brands : JsonModel<Brands>
{
    public class Brand
    {
        public string brand_id { get; set; }
        public string title { get; set; }
        public string website { get; set; }
        public string picture { get; set; }
        public string action { get; set; }
        public string value { get; set; }
    }

    public int count { get; set; }
    public List<Brand> data { get; set; }

    public static string GetBrandListApi(int start, int limit)
    {
        return string.Format("{0}api/mobile/store/brandlist/{1}/{2}", PopBloopSettings.WebServerUrl, start, limit);
    }
}
