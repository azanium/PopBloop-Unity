
namespace PB.Server.Db
{
    public static class DbConstants
    {
        #region Databases Name

        public const string DbNameAssets = "Assets";
        public const string DbNameUsers = "Users";
        public const string DbNameGame = "Game";

        #endregion


        #region Collections Name

        public const string CollectionNameAccount = "Account";
        public const string CollectionNameLevel = "Level";
        public const string CollectionNameSession = "Session";
        public const string CollectionNameInventory = "PlayerInventory";
        public const string CollectionNameQuestJournal = "QuestJournal";
        public const string CollectionNameQuestActive = "QuestActive";
        public const string CollectionNameRooms = "Rooms";
        public const string CollectionNameEquipments = "Equipments";
        public const string CollectionNameRoomStats = "RoomStats";
        public const string CollectionNamePlayerStats = "PlayerStats";
        public const string CollectionNameProperties = "Properties";
        public const string CollectionNameConcurrent = "Concurrent";
        public const string CollectionNamePlayerVisit = "PlayerVisit";

        #endregion


        #region Collection Fields

        #region Level Collection Fields

        // Object meta
        public const string LevelFieldName = "name";
        public const string LevelFieldAssets = "assets";
        public const string LevelFieldObjectName = "objectName";
        public const string LevelFieldMaxCCUPerChannel = "max_ccu_per_channel";

        // Asset file and tag
        public const string LevelFieldAssetFile = "asset_file";
        public const string LevelFieldTag = "tag";
        public const string LevelFieldAssetPath = "directory";

        // Transformations
        public const string LevelFieldPositionX = "position_x";
        public const string LevelFieldPositionY = "position_y";
        public const string LevelFieldPositionZ = "position_z";
        public const string LevelFieldRotationX = "rotation_x";
        public const string LevelFieldRotationY = "rotation_y";
        public const string LevelFieldRotationZ = "rotation_z";
        
        // Entity Lightmap
        public const string LevelFieldEntityLightmapIndex = "lightmapIndex";
        public const string LevelFieldEntityLightmapsTilingOffsetX = "lightmapTilingOffset_x";
        public const string LevelFieldEntityLightmapsTilingOffsetY = "lightmapTilingOffset_y";
        public const string LevelFieldEntityLightmapsTilingOffsetZ = "lightmapTilingOffset_z";
        public const string LevelFieldEntityLightmapsTilingOffsetW = "lightmapTilingOffset_w";

        // Interest Regions
        public const string LevelFieldWorldSizeX = "world_size_x";
        public const string LevelFieldWorldSizeY = "world_size_Y";
        public const string LevelFieldInterestAreaX = "interest_area_x";
        public const string LevelFieldInterestAreaY = "interest_area_y";

        // Skybox and Audio
        public const string LevelFieldSkybox = "skybox_file";
        public const string LevelFieldAudioFile = "audio_file";

        // Fog
        public const string LevelFieldFogActive = "fogActive";
        public const string LevelFieldFogColorR = "fogColor_r";
        public const string LevelFieldFogColorG = "fogColor_g";
        public const string LevelFieldFogColorB = "fogColor_b";
        public const string LevelFieldFogColorA = "fogColor_a";
        public const string LevelFieldFogDensity = "fogDensity";
        public const string LevelFieldFogStartDistance = "fogStartDistance";
        public const string LevelFieldFogEndDistance = "fogEndDistance";
        public const string LevelFieldFogMode = "fogMode";

        // Lightmap
        public const string LevelFieldLightmapsMode = "lightmapsMode";
        public const string LevelFieldLightmapsCount = "lightmapsCount";
        public const string LevelFieldLightmapsNear = "near_";
        public const string LevelFieldLightmapsFar = "far_";


        #endregion

        #region Session Collection Fields

        public const string SessionFieldSessionId = "session_id";
        public const string SessionFieldUserId = "user_id";
        public const string SessionFieldUsername = "username";
        public const string SessionFieldAvatarName = "avatarname";
        public const string SessionFieldPassword = "password";

        #endregion

        #region Properties Collection Field

        public const string PropertiesFieldUserId = "lilo_id";
        public const string PropertiesFieldAvatarname = "avatarname";

        #endregion


        #region Concurrent Collection Fields

        public const string ConcurrentFieldUserId = "user_id";
        public const string ConcurrentFieldRoom = "room";
        public const string ConcurrentFieldDateTime = "datetime";

        #endregion


        #region PlayerVisit Collection Fields

        public const string PlayerVisitFieldUserId = "user_id";
        public const string PlayerVisitFieldRoom = "room";
        public const string PlayerVisitFieldDateTime = "datetime";
        public const string PlayerVisitFieldDuration = "duration";
        public const string PlayerVisitFieldBounce = "bounce";
        public const string PlayerVisitFieldDate = "date";

        #endregion

        #endregion
    }
}
