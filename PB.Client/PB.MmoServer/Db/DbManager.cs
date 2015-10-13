using System;
using System.Collections.Generic;
using System.Linq;
using PB.Common;
using PB.Server.Db.Model;
using MongoDB.Bson;
using MongoDB.Driver;
using MongoDB.Driver.Builders;
using ExitGames.Logging;
using System.Security.Cryptography;
using System.Text;
using System.Globalization;

namespace PB.Server.Db
{
    public class DbManager
    {
        #region MemVars & Props

        private static DbManager _instance = null;
        private static MongoServer _server = null;

        private static readonly ILogger log = LogManager.GetCurrentClassLogger();

        public static DbManager Instance
        {
            get
            {
                if (_instance == null)
                {
                    _instance = new DbManager();
                }

                return _instance;
            }
        }

        [CLSCompliant(false)]
        public MongoCredentials Credentials
        {
            get
            {
                return new MongoCredentials(DbSettings.DBServerUsername, DbSettings.DBServerPassword, true);
            }
        }

        #endregion


        #region Ctor

        private DbManager()
        {
            _server = MongoServer.Create(DbSettings.ConnectionString);
        }

        #endregion


        #region Inventories

        public Equipments FetchEquipments(string userid)
        {
            Equipments equipments = new Equipments();

            try
            {
                MongoServer server = MongoServer.Create(DbSettings.ConnectionString);
                MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
                MongoCollection<BsonDocument> coll = db.GetCollection(DbConstants.CollectionNameEquipments);

                QueryDocument query = new QueryDocument("userid", userid);

                var cursor = coll.Find(query);

                foreach (BsonDocument item in cursor)
                {
                    string code = item["code"].AsString;
                    int count = item["count"].IsString ? Int32.Parse(item["count"].AsString) : item["count"].AsInt32;

                    equipments.Items.Add(code, count);
                }

                server.Disconnect();
            }
            catch (Exception ex)
            {
                log.Error("FetchEquipments: " + ex.ToString());
            }

            return equipments;
        }

        public bool UpdateEquipment(string userid, string equipmentCode, int count)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);
            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameEquipments);

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

        public bool SetInventoryAdd(string userid, string code, string name, float weight, string description)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameInventory);

            /*var query = Query.And(
                Query.EQ("userid", userid),
                Query.EQ("tipe", tipe));
            
            var update = Update.Set("value", count);

            var result = col.FindAndModify(query, SortBy.Descending("priority"), update, true, true);*/

            BsonDocument doc = new BsonDocument(true);
            doc.Add("userid", userid);
            doc.Add("code", code);
            doc.Add("name", name);
            doc.Add("weight", weight);
            doc.Add("description", description);

            var result = col.Insert(doc);

            server.Disconnect();

            return result.LastErrorMessage == null;
        }

        public bool SetInventoryRemove(string userid, string itemId, string code, string name, float weight, string description)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameInventory);

            var query = Query.And(
                Query.EQ("_id", new ObjectId(itemId)),
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

        public Inventories GetInventories(string userid)
        {
            Inventories inventories = new Inventories();

            try
            {
                MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

                MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
                MongoCollection<BsonDocument> coll = db.GetCollection(DbConstants.CollectionNameInventory);
                QueryDocument query = new QueryDocument("userid", userid);
                var cursor = coll.Find(query);

                foreach (BsonDocument item in cursor)
                {
                    ObjectId id = item["_id"].AsObjectId;
                    var code = item["code"].AsString;
                    var name = item["name"].AsString;
                    float weight = item["weight"].IsDouble ? (float)item["weight"].AsDouble : float.Parse(item["weight"].AsString);
                    var description = item["description"].AsString;

                    GameItem gameItem = new GameItem(id.ToString(), code, name, weight, description);
                    inventories.Items.Add(gameItem);
                }

                server.Disconnect();
            }
            catch (Exception ex)
            {
                log.Error(ex.ToString());
            }

            return inventories;
        }

        #endregion


        #region Quests

        public int[] GetQuestActive(string userid)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameQuestActive);

            QueryDocument query = new QueryDocument("userid", userid);
            var cursor = col.Find(query);

            List<int> result = new List<int>();

            foreach (BsonDocument item in cursor)
            {
                var questid = item["questid"].IsInt32 ? item["questid"].AsInt32 : item["questid"].AsDouble;
                bool obsolete = item["obsolete"].AsBoolean;

                if (!obsolete)
                {
                    result.Add((int)questid);
                }
            }

            server.Disconnect();

            return result.ToArray();
        }

        /// <summary>
        /// Get Quest Journal
        /// </summary>
        /// <param name="userid">User Id</param>
        /// <returns>array of quest journal ids</returns>
        public int[] GetQuestJournal(string userid)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameQuestJournal);

            QueryDocument query = new QueryDocument("userid", userid);
            var cursor = col.Find(query);

            List<int> result = new List<int>();

            foreach (BsonDocument item in cursor)
            {
                var questid = item["questid"].IsInt32 ? item["questid"].AsInt32 : item["questid"].AsDouble;
                bool obsolete = item["obsolete"].AsBoolean;

                if (!obsolete)
                {
                    result.Add((int)questid);
                }
            }

            server.Disconnect();

            return result.ToArray();
        }

        public void SetQuestJournal(string userid, int questid, bool isActive)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));

            var query = Query.And(
                Query.EQ("userid", userid),
                Query.EQ("questid", questid));

            /// We have done quest
            if (isActive == false)
            {
                // Find the corresponding active quest
                MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameQuestActive);
                var findActive = col.FindOne(query);

                string startDateTime = "";

                if (findActive != null)
                {
                    if (findActive.Contains("datetime"))
                    {
                        startDateTime = findActive["datetime"].AsString;
                    }
                }

                col = db.GetCollection(DbConstants.CollectionNameQuestJournal);

                var find = col.FindOne(query);

                if (find == null)
                {
                    string now = DateTime.Now.ToString("d", new CultureInfo("en-US"));

                    BsonDocument doc = new BsonDocument();
                    doc.Add("userid", userid);
                    doc.Add("questid", questid);
                    doc.Add("start_date", startDateTime);
                    doc.Add("end_date", now);
                    doc.Add("obsolete", false);

                    col.Insert(doc);
                }

                // Now we modify the active quest journal
                col = db.GetCollection(DbConstants.CollectionNameQuestActive);

                var update = Update.Set("obsolete", true);

                var result = col.FindAndModify(query, SortBy.Descending("priority"), update, true, true);
            }
            else // We have active quest
            {
                MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameQuestActive);

                string now = DateTime.Now.ToString("d", new CultureInfo("en-US"));
                var update = Update.Set("obsolete", false).Set("datetime", now);

                var result = col.FindAndModify(query, SortBy.Descending("priority"), update, true, true);
            }
        
            server.Disconnect();
        }

        #endregion


        #region Level

        /// <summary>
        /// Set the player visit data
        /// </summary>
        /// <param name="userid"></param>
        /// <param name="roomName"></param>
        /// <param name="startTime"></param>
        /// <param name="isBounce"></param>
        public void SetPlayerVisit(string userid, string roomName, DateTime startTime, bool isBounce)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            var concurrentCollection = db.GetCollection(DbConstants.CollectionNamePlayerVisit);

            log.Info(string.Format("StartTime: {0}, Now: {1}", startTime, DateTime.Now));
            TimeSpan time = (DateTime.Now - startTime);
            double duration = time.TotalMinutes;
            string now = DateTime.Now.ToString("d", new CultureInfo("en-US"));

            var room = roomName == userid ? "PrivateRoom" : roomName;

            BsonDocument doc = new BsonDocument(true);
            doc.Add(DbConstants.PlayerVisitFieldUserId, userid);
            doc.Add(DbConstants.PlayerVisitFieldRoom, room);
            doc.Add(DbConstants.PlayerVisitFieldDateTime, startTime);
            doc.Add(DbConstants.PlayerVisitFieldDuration, duration);
            doc.Add(DbConstants.PlayerVisitFieldBounce, isBounce);
            doc.Add(DbConstants.PlayerVisitFieldDate, now);
            
            SafeModeResult result = concurrentCollection.Insert(doc);

            server.Disconnect();
        }

        /// <summary>
        /// Set the concurrent data
        /// </summary>
        /// <param name="userid"></param>
        /// <param name="roomName"></param>
        /// <param name="isRemove"></param>
        public void SetConcurrent(string userid, string roomName, bool isRemove)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            var concurrentCollection = db.GetCollection(DbConstants.CollectionNameConcurrent);

            var query = Query.EQ(DbConstants.ConcurrentFieldUserId, userid);

            var find = concurrentCollection.FindOne(query);

            SafeModeResult result = null;
            if (find != null)
            {
                // If it's already exist, remove it
                result = concurrentCollection.Remove(query);
            }

            if (isRemove == false)
            {
                var room = roomName == userid ? "PrivateRoom" : roomName;

                string now = DateTime.Now.ToString();//"d", new CultureInfo("en-US"));

                BsonDocument doc = new BsonDocument(true);
                doc.Add(DbConstants.ConcurrentFieldUserId, userid);
                doc.Add(DbConstants.ConcurrentFieldRoom, room);
                doc.Add(DbConstants.ConcurrentFieldDateTime, now);

                result = concurrentCollection.Insert(doc);
            }

            server.Disconnect();
        }

        public bool RecordRoomStats(string userid, string roomName)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);
            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            
            //////////////////////////////////////////////////////////////
            // Look up for Player count for this room and date is now
            //////////////////////////////////////////////////////////////
            MongoCollection<BsonDocument> playerCol = db.GetCollection(DbConstants.CollectionNamePlayerStats);

            string now = DateTime.Now.ToString("d", new CultureInfo("en-US"));

            var room = roomName == userid ? "PrivateRoom" : roomName;

            var query = Query.And(Query.EQ("date", now), 
                                  Query.EQ("room", room));

            int uniqueVisit = playerCol.Count(query);

            //////////////////////////////////////////////////////////////
            // Look up for Player END
            //////////////////////////////////////////////////////////////

            MongoCollection<BsonDocument> roomCol = db.GetCollection(DbConstants.CollectionNameRoomStats);
            var roomQuery = Query.And(Query.EQ("date", now),
                                      Query.EQ("room", room));
            var find = roomCol.FindOne(roomQuery);

            bool success = false;

            if (find == null)
            {
                BsonDocument doc = new BsonDocument(true);
                doc.Add("date", now);
                doc.Add("room", room);
                doc.Add("unique_visit", uniqueVisit);
                doc.Add("visit", 1);

                success = roomCol.Insert(doc).ErrorMessage == null;
            }
            else
            {
                int currentVisit = find["visit"].AsInt32;
                var update = Update.Set("visit", currentVisit + 1).Set("unique_visit", uniqueVisit);

                success = roomCol.FindAndModify(query, SortBy.Descending("priority"), update, true, true).ErrorMessage == null;
            }

            server.Disconnect();

            return success;
        }

        public bool RecordPlayerStats(string userid, string roomName)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNamePlayerStats);

            string now = DateTime.Now.ToString("d", new CultureInfo("en-US"));

            var room = roomName == userid ? "PrivateRoom" : roomName;

            var query = Query.And(Query.And(Query.EQ("userid", userid), Query.EQ("date", now),
                                  Query.EQ("room", room)));

            var find = col.FindOne(query);
            
            bool success = false;

            if (find == null)
            {
                BsonDocument doc = new BsonDocument(true);
                doc.Add("date", now);
                doc.Add("userid", userid);
                doc.Add("room", room);
                doc.Add("visit", 1);    // The player is first visiting this world

                success = col.Insert(doc).ErrorMessage == null;
            }
            else
            {
                int currentVisit = find["visit"].AsInt32;
                var update = Update.Set("visit", currentVisit + 1);

                success = col.FindAndModify(query, SortBy.Descending("priority"), update, true, true).ErrorMessage == null;
            }

            /*
             *var query = Query.And(
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
*/

            server.Disconnect();

            return success;
        }

        /// <summary>
        /// Get the level instance.
        /// </summary>
        /// <param name="levelName">The name of level.</param>
        /// <returns>Level instance.</returns>
        public Level GetLevel(string levelName)
        {
            var level = new Level();

            try
            {
                MongoServer server = MongoServer.Create(DbSettings.ConnectionString);
                var db = server.GetDatabase(DbConstants.DbNameAssets, Credentials, new SafeMode(true));
                MongoCollection<BsonDocument> mongColl = db.GetCollection(DbConstants.CollectionNameLevel);
                var query = new QueryDocument(DbConstants.LevelFieldName, levelName);
                var bsonDoc = mongColl.FindOne(query);

                level.Path = bsonDoc.Contains(DbConstants.LevelFieldAssetPath) ? bsonDoc[DbConstants.LevelFieldAssetPath].AsString : ""; 
                level.WorldSize = new float[2] { float.Parse(bsonDoc[DbConstants.LevelFieldWorldSizeX].AsString), float.Parse(bsonDoc[DbConstants.LevelFieldWorldSizeX].AsString) };
                level.InterestArea = new float[2] { float.Parse(bsonDoc[DbConstants.LevelFieldInterestAreaX].AsString), float.Parse(bsonDoc[DbConstants.LevelFieldInterestAreaY].AsString) };
                level.Entities = GetEntities(bsonDoc);

                level.Skybox = bsonDoc.Contains(DbConstants.LevelFieldSkybox) ? level.Skybox = bsonDoc[DbConstants.LevelFieldSkybox].AsString : "";
                level.Audio = bsonDoc.Contains(DbConstants.LevelFieldAudioFile) ? level.Audio = bsonDoc[DbConstants.LevelFieldAudioFile].AsString : "";

                // Load fog information
                level.Fog = new Fog();
                level.Fog.fogMode = bsonDoc.Contains(DbConstants.LevelFieldFogMode) ? bsonDoc[DbConstants.LevelFieldFogMode].AsString : "ExponentialSquared";
                level.Fog.active = bsonDoc.Contains(DbConstants.LevelFieldFogActive) ? bsonDoc[DbConstants.LevelFieldFogActive].AsBoolean : false;
                level.Fog.density = bsonDoc.Contains(DbConstants.LevelFieldFogDensity) ? (float)bsonDoc[DbConstants.LevelFieldFogDensity].AsDouble : 0.01f;
                level.Fog.startDistance = bsonDoc.Contains(DbConstants.LevelFieldFogStartDistance) ? (float)bsonDoc[DbConstants.LevelFieldFogStartDistance].AsDouble : 0f;
                level.Fog.endDistance = bsonDoc.Contains(DbConstants.LevelFieldFogEndDistance) ? (float)bsonDoc[DbConstants.LevelFieldFogEndDistance].AsDouble : 300f;
                float r = bsonDoc.Contains(DbConstants.LevelFieldFogColorR) ? r = (float)bsonDoc[DbConstants.LevelFieldFogColorR].AsDouble : 0.5f;
                float g = bsonDoc.Contains(DbConstants.LevelFieldFogColorG) ? g = (float)bsonDoc[DbConstants.LevelFieldFogColorG].AsDouble : 0.5f;
                float b = bsonDoc.Contains(DbConstants.LevelFieldFogColorB) ? b = (float)bsonDoc[DbConstants.LevelFieldFogColorB].AsDouble : 0.5f;
                float a = bsonDoc.Contains(DbConstants.LevelFieldFogColorA) ? a = (float)bsonDoc[DbConstants.LevelFieldFogColorA].AsDouble : 1f;
                level.Fog.color = new float4(r, g, b, a);

                BsonDocument lightmapsBson = bsonDoc.Contains("lightmaps") ? bsonDoc["lightmaps"].AsBsonDocument : null;

                level.Lightmap = GetLightmaps(lightmapsBson);

                server.Disconnect();
            }
            catch (Exception ex)
            {
                log.Error("DbManager.GetLevel(): " + ex.Message);
            }

            return level;
        }

        private LightmapInfo GetLightmaps(BsonDocument bsonDoc)
        {
            LightmapInfo lightmap = new LightmapInfo();

            if (bsonDoc != null)
            {
                // Load Lightmap Informations
                lightmap.lightmapsMode = bsonDoc.Contains(DbConstants.LevelFieldLightmapsMode) ? bsonDoc[DbConstants.LevelFieldLightmapsMode].AsString : "";
                int lightMapCount = bsonDoc.Contains(DbConstants.LevelFieldLightmapsCount) ? int.Parse(bsonDoc[DbConstants.LevelFieldLightmapsCount].AsString) : 0;

                if (lightMapCount > 0)
                {
                    for (int lcount = 0; lcount < lightMapCount; lcount++)
                    {
                        string nearField = DbConstants.LevelFieldLightmapsNear + lcount.ToString(); // This should be in the format: near_x
                        string farField = DbConstants.LevelFieldLightmapsFar + lcount.ToString();   // This should be in the format: far_x

                        string near = bsonDoc.Contains(nearField) ? bsonDoc[nearField].AsString : "";
                        string far = bsonDoc.Contains(farField) ? bsonDoc[farField].AsString : "";

                        LightmapDataInfo lmData = new LightmapDataInfo(near, far);
                        lightmap.lightmaps.Add(lmData);
                    }
                }
            }

            return lightmap;
        }

        /// <summary>
        /// Get the list of level entity.
        /// </summary>
        /// <param name="bsonDoc">The root level.</param>
        /// <returns>List of entities.</returns>
        [CLSCompliant(false)]
        public List<Entity> GetEntities(BsonDocument bsonDoc)
        {
            var entities = new List<Entity>();
            BsonDocument entityElements = bsonDoc["assets"].AsBsonDocument;
            Entity anEntity = null;

            for (int i = 0; i < entityElements.Count(); i++)
            {
                BsonDocument entityAttributes = entityElements[i].AsBsonDocument;
                anEntity = new Entity();

                if (entityAttributes.Contains("objectName"))
                {
                    anEntity.ObjectName = entityAttributes["objectName"].AsString;
                }

                if (entityAttributes.Contains("asset_file"))
                {
                    anEntity.FilePath = entityAttributes["asset_file"].AsString;
                }

                if (entityAttributes.Contains("position_x") &&
                    entityAttributes.Contains("position_y") &&
                    entityAttributes.Contains("position_z"))
                {
                    anEntity.Position = new float3(float.Parse(entityAttributes["position_x"].AsString),
                        float.Parse(entityAttributes["position_y"].AsString),
                        float.Parse(entityAttributes["position_z"].AsString));
                }

                if (entityAttributes.Contains("rotation_x") &&
                    entityAttributes.Contains("rotation_y") &&
                    entityAttributes.Contains("rotation_z"))
                {
                    anEntity.Rotation = new float3(float.Parse(entityAttributes["rotation_x"].AsString),
                        float.Parse(entityAttributes["rotation_y"].AsString),
                        float.Parse(entityAttributes["rotation_z"].AsString));
                }

                if (entityAttributes.Contains("tag"))
                {
                    anEntity.Tag = entityAttributes["tag"].AsString;
                }

                if (entityAttributes.Contains(DbConstants.LevelFieldEntityLightmapIndex))
                {
                    anEntity.LightmapIndex = int.Parse(entityAttributes[DbConstants.LevelFieldEntityLightmapIndex].AsString);
                }
                else
                {
                    anEntity.LightmapTilingOffset = new float4(0f, 0f, 0f, 0f);
                }

                float4 lightmapTileOffset = new float4(0f, 0f, 0f, 0f);
                if (entityAttributes.Contains(DbConstants.LevelFieldEntityLightmapsTilingOffsetX))
                {
                    lightmapTileOffset.x = float.Parse(entityAttributes[DbConstants.LevelFieldEntityLightmapsTilingOffsetX].AsString);
                }

                if (entityAttributes.Contains(DbConstants.LevelFieldEntityLightmapsTilingOffsetY))
                {
                    lightmapTileOffset.y = float.Parse(entityAttributes[DbConstants.LevelFieldEntityLightmapsTilingOffsetY].AsString);
                }

                if (entityAttributes.Contains(DbConstants.LevelFieldEntityLightmapsTilingOffsetZ))
                {
                    lightmapTileOffset.z = float.Parse(entityAttributes[DbConstants.LevelFieldEntityLightmapsTilingOffsetZ].AsString);
                }

                if (entityAttributes.Contains(DbConstants.LevelFieldEntityLightmapsTilingOffsetW))
                {
                    lightmapTileOffset.w = float.Parse(entityAttributes[DbConstants.LevelFieldEntityLightmapsTilingOffsetW].AsString);
                }
                anEntity.LightmapTilingOffset = lightmapTileOffset;

                entities.Add(anEntity);
            }

            return entities;
        }

        public int GetCCUOnLevelName(string levelName)
        {
            int ccu = 0;

            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameConcurrent);
            
            var query = Query.EQ(DbConstants.ConcurrentFieldRoom, levelName);

            var find = col.Find(query);

            if (find != null)
            {
                foreach (BsonDocument item in find)
                {
                    ccu++;
                }
            }

            server.Disconnect();

            return ccu;
        }

        public void GetCCUPerChannel(string levelName, out int currentCCU, out int maxCCU)
        {
            // Get the current CCU
            currentCCU = GetCCUOnLevelName(levelName);

            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameAssets, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameLevel);
            
            var query = Query.EQ(DbConstants.LevelFieldName, levelName);

            var find = col.FindOne(query);

            maxCCU = 0;

            if (find != null)
            {
                var value = find[DbConstants.LevelFieldMaxCCUPerChannel];

                if (value.IsString)
                {
                    string strCCU = value.AsString;
                    if (strCCU == null || strCCU == "")
                    {
                        maxCCU = 0;
                    }
                    else
                    {
                        maxCCU = int.Parse(strCCU);
                    }
                }
                else
                {
                    maxCCU = value.AsInt32;
                }
            }

            server.Disconnect();
        }

        public List<Entity> GetEntities(string levelName)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameAssets, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> mongColl = db.GetCollection(DbConstants.CollectionNameLevel);
            QueryDocument query = new QueryDocument(DbConstants.LevelFieldName, levelName);

            List<Entity> levelEntities = new List<Entity>();

            foreach (BsonDocument levelAssets in mongColl.Find(query))
            {
                //Console.WriteLine("Level Name: " + levelAssets["name"].AsString);
                BsonDocument entityElements = levelAssets[DbConstants.LevelFieldAssets].AsBsonDocument;
                Entity anEntity = null;

                for (int i = 0; i < entityElements.Count(); i++)
                {
                    BsonDocument entityAttributes = entityElements[i].AsBsonDocument;
                    anEntity = new Entity();

                    if (entityAttributes.Contains(DbConstants.LevelFieldObjectName))
                    {
                        anEntity.ObjectName = entityAttributes[DbConstants.LevelFieldObjectName].AsString;
                    }

                    if (entityAttributes.Contains(DbConstants.LevelFieldAssetFile))
                    {
                        anEntity.FilePath = entityAttributes[DbConstants.LevelFieldAssetFile].AsString;
                    }

                    if (entityAttributes.Contains(DbConstants.LevelFieldPositionX) &&
                        entityAttributes.Contains(DbConstants.LevelFieldPositionY) &&
                        entityAttributes.Contains(DbConstants.LevelFieldPositionZ))
                    {
                        anEntity.Position = new float3(float.Parse(entityAttributes[DbConstants.LevelFieldPositionX].AsString),
                            float.Parse(entityAttributes[DbConstants.LevelFieldPositionY].AsString),
                            float.Parse(entityAttributes[DbConstants.LevelFieldPositionZ].AsString));
                    }

                    if (entityAttributes.Contains(DbConstants.LevelFieldRotationX) &&
                        entityAttributes.Contains(DbConstants.LevelFieldRotationY) &&
                        entityAttributes.Contains(DbConstants.LevelFieldRotationZ))
                    {
                        anEntity.Rotation = new float3(float.Parse(entityAttributes[DbConstants.LevelFieldRotationX].AsString),
                            float.Parse(entityAttributes[DbConstants.LevelFieldRotationY].AsString),
                            float.Parse(entityAttributes[DbConstants.LevelFieldRotationZ].AsString));
                    }

                    if (entityAttributes.Contains(DbConstants.LevelFieldTag))
                    {
                        anEntity.Tag = entityAttributes[DbConstants.LevelFieldTag].AsString;
                    }

                    levelEntities.Add(anEntity);
                }
            }

            server.Disconnect();

            return levelEntities;
        }

        public Level LoadPrivateLevel(string levelName, string userid)
        {
            Level level = null;

            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameRooms);

            var query = Query.And(
                Query.EQ("userid", userid),
                Query.EQ("name", levelName));

            var bsonDoc = col.FindOne(query);

            if (bsonDoc != null)
            {
                level = new Level();

                List<Entity> entities = GetEntities(bsonDoc);
                level.Entities = entities;

                level.WorldSize = new float[2] { float.Parse(bsonDoc["world_size_x"].AsString), float.Parse(bsonDoc["world_size_y"].AsString) };
                level.InterestArea = new float[2] { float.Parse(bsonDoc["interest_area_x"].AsString), float.Parse(bsonDoc["interest_area_y"].AsString) };
            }

            server.Disconnect();

            return level;
        }

        public void SavePrivateLevel(string levelName, string userid, Level level)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);

            MongoDatabase db = server.GetDatabase(DbConstants.DbNameGame, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameRooms);

            var query = Query.And(
                Query.EQ("userid", userid),
                Query.EQ("name", levelName));

            var doc = col.FindOne(query);

            if (doc == null)
            {
                doc = new BsonDocument();
            }

            SerializeLevel(levelName, userid, level, ref doc);

            col.Insert(doc);

            server.Disconnect();
        }

        [CLSCompliant(false)]
        public void SerializeLevel(string levelName, string userid, Level level, ref BsonDocument bsonDoc)
        {
            bsonDoc.Add("userid", userid);
            bsonDoc.Add("name", levelName);
            bsonDoc.Add("world_size_x", level.WorldSize[0].ToString());
            bsonDoc.Add("world_size_y", level.WorldSize[1].ToString());
            bsonDoc.Add("interest_area_x", level.InterestArea[0].ToString());
            bsonDoc.Add("interest_area_y", level.InterestArea[1].ToString());

            int index = 1;

            BsonDocument entities = new BsonDocument();

            foreach (Entity entity in level.Entities)
            {
                BsonDocument obj = new BsonDocument();

                obj.Add("objectName", entity.ObjectName);
                obj.Add("asset_file", entity.FilePath);
                obj.Add("position_x", entity.Position.x.ToString());
                obj.Add("position_y", entity.Position.y.ToString());
                obj.Add("position_z", entity.Position.z.ToString());
                obj.Add("rotation_x", entity.Rotation.x.ToString());
                obj.Add("rotation_y", entity.Rotation.y.ToString());
                obj.Add("rotation_z", entity.Rotation.z.ToString());
                obj.Add("scale_x", entity.Scale.x.ToString());
                obj.Add("scale_y", entity.Scale.y.ToString());
                obj.Add("scale_z", entity.Scale.z.ToString());

                obj.Add("tag", entity.Tag);

                entities.Add(string.Format("{0}", index++), obj);
            }

            bsonDoc.Add("assets", entities);
        }

        #endregion


        #region Credentials

        /// <summary>
        /// Get User by session.
        /// </summary>
        /// <param name="token">Session Id.</param>
        /// <returns></returns>
        [CLSCompliant(false)]
        public Session GetUserBySession(string token)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);
            MongoDatabase db = server.GetDatabase(DbConstants.DbNameUsers, Credentials, new SafeMode(true));

            MongoCollection<BsonDocument> mongColl = db.GetCollection(DbConstants.CollectionNameSession);

            QueryDocument query = new QueryDocument(DbConstants.SessionFieldSessionId, token);
            BsonDocument bson = mongColl.FindOne(query);

            if (bson == null)
            {
                return null;
            }

            Session session = new Session();
            session.Id = bson[DbConstants.SessionFieldSessionId].AsString;
            string userId = bson[DbConstants.SessionFieldUserId].AsString;
            session.UserId = ObjectId.Parse(userId);

            session.Username = bson[DbConstants.SessionFieldUsername].AsString;

            // Look up for avatar's name
            MongoCollection<BsonDocument> colProp = db.GetCollection(DbConstants.CollectionNameProperties);
            var queryProp = Query.EQ(DbConstants.PropertiesFieldUserId, userId);

            session.Avatarname = session.Username;
            
            var resultAvatar = colProp.FindOne(queryProp);
            if (resultAvatar != null)
            {
                if (resultAvatar.Contains(DbConstants.PropertiesFieldAvatarname))
                {
                    session.Avatarname = resultAvatar[DbConstants.PropertiesFieldAvatarname].AsString;
                }
            }

            server.Disconnect();

            return session;
        }

        [CLSCompliant(false)]
        public Session UserValid(string username, string password)
        {
            MongoServer server = MongoServer.Create(DbSettings.ConnectionString);
            MongoDatabase db = server.GetDatabase(DbConstants.DbNameUsers, Credentials, new SafeMode(true));
            MongoCollection<BsonDocument> col = db.GetCollection(DbConstants.CollectionNameAccount);

            var query = Query.And(
                Query.EQ(DbConstants.SessionFieldUsername, username),
                Query.EQ(DbConstants.SessionFieldPassword, GetMd5Hash(password)));

            var bsonDoc = col.FindOne(query);

            if (bsonDoc == null)
            {
                return null;
            }

            Session session = new Session();
            session.Username = bsonDoc[DbConstants.SessionFieldUsername].AsString;
            string userId = bsonDoc[DbConstants.PropertiesFieldUserId].AsString;
            session.UserId = ObjectId.Parse(userId);

            // Lookup our avatar's name from the Properties
            MongoCollection<BsonDocument> colProp = db.GetCollection(DbConstants.CollectionNameProperties);
            var queryProp = Query.EQ(DbConstants.PropertiesFieldUserId, userId);

            session.Avatarname = session.Username;

            var resultAvatar = colProp.FindOne(queryProp);
            if (resultAvatar != null)
            {
                if (resultAvatar.Contains(DbConstants.PropertiesFieldAvatarname))
                {
                    session.Avatarname = resultAvatar[DbConstants.PropertiesFieldAvatarname].AsString;
                }
            }

            return session;
        }

        [CLSCompliant(false)]
        public string GetMd5Hash(string input)
        {
            string hash = "";
            using (MD5 md5Hash = MD5.Create())
            {
                // Convert the input string to a byte array and compute the hash.
                byte[] data = md5Hash.ComputeHash(Encoding.UTF8.GetBytes(input));

                // Create a new Stringbuilder to collect the bytes
                // and create a string.
                StringBuilder sBuilder = new StringBuilder();

                // Loop through each byte of the hashed data 
                // and format each one as a hexadecimal string.
                for (int i = 0; i < data.Length; i++)
                {
                    sBuilder.Append(data[i].ToString("x2"));
                }

                // Return the hexadecimal string.
                hash = sBuilder.ToString();
            }

            return hash;
        }

        #endregion
    }
}
