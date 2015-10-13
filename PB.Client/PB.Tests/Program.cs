using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using MongoDB.Driver;
using MongoDB.Bson;
using PB.Common;
using MongoDB.Driver.Builders;

namespace PB.Tests
{
    public class Session
    {
        public string Id { get; set; }
        public DateTime TimeStart { get; set; }
        public DateTime TimeEnd { get; set; }
        public ObjectId UserId { get; set; }
        public string Username { get; set; }
    }

    class Program
    {

        static void Main(string[] args)
        {
            //SetInventoryRemove("4e2fe1e4c1b4ba4444000014", "energy", "energy", 0, "");

            //Equipments eq = FetchEquipments("4e2fe1e4c1b4ba4444000014");

            //byte[] bytes = Equipments.Serialize(eq);

            UpdateEquipment("4e2fe1e4c1b4ba4444000014", "energy", 10);

            Console.ReadLine();
        }

        public static bool UpdateEquipment(string userid, string equipmentCode, int count)
        {
            MongoServer server = MongoServer.Create("mongodb://127.0.0.1:27017");
            MongoDatabase db = server.GetDatabase("Game", Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection("Equipments");

            var query = Query.And(
                Query.EQ("userid", userid),
                Query.EQ("code", equipmentCode));

            var find = col.FindOne(query);

            bool success = false;

            if (find == null)
            {
                BsonDocument doc = new BsonDocument();
                doc.Add("userid", userid);
                doc.Add("code", equipmentCode);
                doc.Add("count", count);

                success = col.Insert(doc).ErrorMessage == null;
            }
            else
            {
                var update = Update.Set("count", count);

                success = col.FindAndModify(query, SortBy.Descending("priority"), update, true, true).ErrorMessage == null;
            }

            server.Disconnect();

            return success;
        }

        public static Equipments FetchEquipments(string userid)
        {
            Equipments equipments = new Equipments();

            try
            {
                MongoServer server = MongoServer.Create("mongodb://127.0.0.1:27017");
                MongoDatabase db = server.GetDatabase("Game", Credentials, new SafeMode(true));
                MongoCollection<BsonDocument> coll = db.GetCollection("Equipments");

                QueryDocument query = new QueryDocument("userid", userid);

                var cursor = coll.Find(query);

                foreach (BsonDocument item in cursor)
                {
                    string code = item["code"].AsString;
                    int count = item["count"].AsInt32;

                    equipments.Items.Add(code, count);
                }

                server.Disconnect();
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine(ex.ToString());
            }

            return equipments;
        }

        public static bool SetInventoryRemove(string userid, string code, string name, float weight, string description)
        {
            MongoServer server = MongoServer.Create("mongodb://127.0.0.1:27017");

            MongoDatabase db = server.GetDatabase("Game", Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection("PlayerInventory");

            var query = Query.And(
                Query.EQ("userid", userid),
                Query.EQ("code", code),
                Query.EQ("name", name),
                Query.EQ("weight", weight),
                Query.EQ("description", description));


            var find = col.FindOne(query);

            SafeModeResult result = null;
            if (find != null)
            {
                result = col.Remove(query);
            }
            server.Disconnect();

            return result != null ? result.LastErrorMessage == null : false;
        }

        public static MongoCredentials Credentials
        {
            get
            {
                return new MongoCredentials("admin", "manglayang2010", true);
            }
        
        }
       
    }
}
