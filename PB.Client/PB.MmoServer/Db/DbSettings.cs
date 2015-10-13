using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.Server.Db
{
    public class DbSettings
    {
        public static string DBServerAddress = "127.0.0.1:27017";
        public static string DBServerUsername = "admin";
        public static string DBServerPassword = "manglayang2010";

        public static string ConnectionString
        {
            get 
            {
                return string.Format("mongodb://{0}", DBServerAddress);
            }
            
        }

        public static string BinaryPath { get; set; }
    }
}
