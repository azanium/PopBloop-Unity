using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.Client
{
    /// <summary>
    /// Global PopBloop Settings
    /// </summary>
    public class PopBloopSettings : Settings
    {
        #region Global Settings

        public static bool useLogs = true;

        #endregion


        #region Web Server Settings

        public static bool UseAssetsCache = false;

        /// <summary>
        /// Development Mode Type Enums
        /// </summary>
        public enum DevelopmentModeType
        {
            //EDITOR  = 0,    // Local Files no Server
            LOCAL = 0,          // Local Server
            DEVELOPMENT,
            STAGING,        // Staging Server
            PRODUCTION      // Production Server
        }

        public enum PlatformType
        {
            Default,
            Android,
            iOS
        }

        public static PlatformType platform = PlatformType.Default;

        /// <summary>
        /// Set this if we want to download the assetsbundle from the local filesystems.
        /// </summary>
        public static bool useLocalAssets = false;

        /// <summary>
        /// Set your development mode here, if you are testing on editor, choose editor, if not choose other than editor
        /// </summary>
        public static DevelopmentModeType DevelopmentMode = DevelopmentModeType.STAGING;

        /// <summary>
        /// Local File Path for testing on Unity Editor
        /// </summary>
        public static string serverEditor = "";//"file://" + Application.dataPath + "/../";

        /// <summary>
        /// Local Server address
        /// </summary>
        public static string serverLocal = "http://127.0.0.1/";

        /// <summary>
        /// Development Server address
        /// </summary>
        public static string serverDevelopment = "http://www.popbloop-dev.com/";

        /// <summary>
        /// Staging Server address
        /// </summary>
        public static string serverStaging = "http://dev.popbloop.com/";//"http://124.66.160.109/";

        /// <summary>
        /// Production Server address
        /// </summary>
        public static string serverProduction = "http://www.popbloop.com/";//"http://124.66.160.176/";

        /// <summary>
        /// Set if the Web Server using HTTP/HTTPS
        /// </summary>
        public static bool isSecure = false;

        private static string FormatHTTPS(string url)
        {
            if (isSecure)
            {
                if (url.Contains("HTTP:"))
                {
                    return url.Replace("HTTP:", "https:");
                }

                if (url.Contains("http:"))
                {
                    return url.Replace("http:", "https:");
                }
            }

            return url;
        }

        /// <summary>
        /// Development Server
        /// </summary>
        public static string WebServerUrl
        {
            get
            {
                string server = serverEditor;
                switch (DevelopmentMode)
                {
                    case DevelopmentModeType.LOCAL:
                        server = FormatHTTPS(serverLocal);
                        break;

                    case DevelopmentModeType.DEVELOPMENT:
                        server = FormatHTTPS(serverDevelopment);
                        break;

                    case DevelopmentModeType.PRODUCTION:
                        server = FormatHTTPS(serverProduction);
                        break;

                    case DevelopmentModeType.STAGING:
                        server = FormatHTTPS(serverStaging);
                        break;
                }

                return server;
            }
        }


        /// <summary>
        /// Return the root asset bundle url path
        /// </summary>
        public static string AssetsUrl
        {
            get
            {
                if (useLocalAssets)
                {
                    return "";
                }
                else
                {
                    return WebServerUrl;
                }
            }
        }

        /// <summary>
        /// Return local file asset bundle path
        /// </summary>
        public static string EditorAssetBundleBaseURL
        {
            get
            {
                return serverEditor;
            }
        }

        private static string MobileUrl
        {
            get
            {
                string mobilePath = "";
                if (platform == PlatformType.Android)
                {
                    mobilePath = "android/";
                }
                else if (platform == PlatformType.iOS)
                {
                    mobilePath = "ios/";
                }
                return mobilePath;
            }
        }

        public static string LevelAssetsUrl
        {
            get
            {

                return AssetsUrl + "bundles/level/";//"office/assets/level/";
            }
        }

        /// <summary>
        /// Return the character asset bundle url path
        /// </summary>
        public static string CharactersBundleBaseURL
        {
            get
            {
                return AssetsUrl + "bundles/characters/" + MobileUrl;//"office/assets/characters/";
            }
        }

        /// <summary>
        /// Return the Materials asset bundle url path
        /// </summary>
        public static string MaterialsBundleBaseURL
        {
            get
            {
                return AssetsUrl + "bundles/materials/" + MobileUrl;//"office/assets/materials/";
            }
        }

        /// <summary>
        /// Return the Animations asset bundle url path
        /// </summary>
        public static string AnimationsBundleBaseURL
        {
            get
            {
                return AssetsUrl + "bundles/animations/" + MobileUrl;//"office/assets/animations/";
            }
        }

        /// <summary>
        /// Return World Asset Bundle URL Path
        /// </summary>
        public static string WorldBundleBaseURL
        {
            get
            {
                return AssetsUrl + "worlds/";
            }
        }

        /// <summary>
        /// Return Shout URL path using POST
        /// </summary>
        public static string ShoutBaseURL
        {
            get
            {
                return WebServerUrl + "/message/guest/shout";
            }
        }

        /// <summary>
        /// Inventory API request URL
        /// </summary>
        public static string InventoryApiUrl
        {
            get
            {
                return AssetsUrl + "api/unity/query/inventory";//"office/api/inventory/";
            }
        }

        /// <summary>
        /// Inventory Assets Url is where the inventory icons reside
        /// </summary>
        public static string InventoryAssetsUrl
        {
            get
            {
                return AssetsUrl + "bundles/inventory/";//"office/assets/inventory/";
            }
        }

        /// <summary>
        /// Banner Url
        /// </summary>
        public static string BannerURL
        {
            get 
            { 
                return AssetsUrl + "bundles/banners/";//"office/assets/banners/"; 
            }
        }


        /// <summary>
        /// Return Avatar Configuratin URL path
        /// </summary>
        /// <param name="id">the user id</param>
        /// <returns>the url</returns>
        public static string GetAvatarConfigurationURL(string id)
        {
            return WebServerUrl + "api/unity/query/avatar_configuration/" + id;//"avatar/guest/get_configuration/" + id + ServerSecretKey;
        }

        public static string GetEditorAnimationsConfigurationURL(string gender)
        {
            return WebServerUrl + "api/unity/query/editor_animation/" + gender; //"avatar/guest/get_editor_animation/" + gender;
        }

        public static string GetAnimationsConfigurationURL(string id)
        {
            return WebServerUrl + "api/unity/query/avatar_animation/" + id; //"avatar/guest/get_animation/" + id + ServerSecretKey;
        }

        public static string GetDialogStoryURL(string id)
        {
            return WebServerUrl + "api/unity/query/dialogstory/" + id; //"quest/admin/dialogstorydetail/" + id + ServerSecretKey;
        }

        public static string GetQuestStoryURL(string id)
        {
            return WebServerUrl + "api/unity/query/quest/" + id; //"quest/admin/ws_quest/detail/" + id + ServerSecretKey;
        }

        /// <summary>
        /// Lobby Info Url
        /// </summary>
        public static string LobbyInfo
        {
            get { return WebServerUrl + "api/unity/query/lobby"; } //"asset/guest/get_lobby" + ServerSecretKey; }
        }

        /// <summary>
        /// Url to upload Photo via POST
        /// </summary>
        public static string PhotoUploadUrl
        {
            get { return WebServerUrl + "tester/fbaps/post_photo"; }//"user/guest/uploadprofilephoto"; }
        }

        /// <summary>
        /// Get the latest uploaded photo from the server
        /// </summary>
        public static string PhotoUploadLatestFilenameUrl
        {
            get { return WebServerUrl + "ui/user/photolastname"; }
        }

        /// <summary>
        /// Share the uploaded photo to the facebook
        /// POST parameter - nama_capturan_jpg_png
        /// </summary>
        public static string PhotoUploadShareToFacebookUrl(string filename)
        {
            return WebServerUrl + "ui/user/uploadphototofacebook/" + filename;
        }

        /// <summary>
        /// Share the latest uploaded photo album to facebook
        /// </summary>
        public static string PhotoUploadShareAlbumToFacebookUrl
        {
            get
            {
                return WebServerUrl + "ui/user/uploadphotoalbumfbapp";
            }
        }

        /// <summary>
        /// Send redeem code to a user email
        /// </summary>
        /// <param name="sessionid">session id of the current user</param>
        /// <param name="redeem">the redeem code</param>
        /// <returns></returns>
        public static string RedeemCodeSendMail(string sessionid, string redeem)
        {
            return WebServerUrl + string.Format("office/api/send_redeem_email/{0}/{1}/", sessionid, redeem);
        }

        /// <summary>
        /// HTTP/S request secret key
        /// </summary>
        public static string ServerSecretKey
        {
            get { return "?secret_key=e7d15fc033c5b70c109f2303d0021514"; }
        }

        /// <summary>
        /// Gesticons data
        /// </summary>
        public static string GesticonUrl
        {
            get { return AssetsUrl + "api/unity/query/gesticon"; }
        }

        /// <summary>
        /// Generate Redeem Avatar Url
        /// </summary>
        /// <param name="avatarItemId"></param>
        /// <param name="count"></param>
        /// <returns></returns>
        public static string GenerateRedeemAvatarUrl(string avatarItemId)
        {
            return string.Format("{0}api/unity/query/r_gen/{1}/", AssetsUrl, avatarItemId); 
        }

        #endregion


        #region KamarPas Related API


        public static string MobileAPIUrl
        {
            get { return string.Format("{0}api/mobile/", WebServerUrl); }
        }

        public static string GetMobileBrandList(int start, int dataLimit)
        {
            return string.Format("{0}brand/list/{1}/{2}?_rnd={3}", MobileAPIUrl, start, dataLimit, DateTime.Now.Ticks);    
        }

        public static string GetBrandImageUrl(string image)
        {
            return string.Format("{0}bundles/categori/{1}", WebServerUrl, image);
        }

        #endregion


        #region Photon Server Settings

        /// <summary>
        /// The get default settings.
        /// </summary>
        /// <returns>
        /// the settings
        /// </returns>
        public static PopBloopSettings GetDefaultSettings()
        {
            const string WorldName = "Island1";
            const int BoxesVertical = 1000;
            const int BoxesHorizontal = 1000;
            const int EdgeLengthVertical = 100;
            const int EdgeLengthHorizontal = 100;

            const int IntervalMove = 1000;

            const int Velocity = 1;
            const bool AutoMove = false;
            const bool SendReliable = true;

            const bool UseTcp = false;
            const string ServerAddress = "localhost:5055";
            const string ApplicationName = "PB.MmoServer";

            int[] tileDimensions = new int[2];
            tileDimensions[0] = EdgeLengthHorizontal;
            tileDimensions[1] = EdgeLengthVertical;

            int[] gridSize = new int[2];
            gridSize[0] = BoxesHorizontal * EdgeLengthHorizontal;
            gridSize[1] = BoxesVertical * EdgeLengthVertical;

            PopBloopSettings result = new PopBloopSettings();

            result.IsPrivateRoom = false;

            // photon
            result.ServerAddress = ServerAddress;
            result.UseTcp = UseTcp;
            result.ApplicationName = ApplicationName;

            // grid
            result.WorldName = WorldName;
            result.TileDimensions = tileDimensions;
            result.GridSize = gridSize;

            // game engine
            result.AutoMoveInterval = IntervalMove;

            result.SendReliable = SendReliable;
            result.AutoMoveVelocity = Velocity;
            result.AutoMove = AutoMove;

            return result;
        }

        /// <summary>
        /// The auto move.
        /// </summary>
        private bool autoMove;

        /// <summary>
        /// The auto move interval.
        /// </summary>
        private int autoMoveInterval;

        /// <summary>
        /// The auto move velocity.
        /// </summary>
        private int autoMoveVelocity;

        /////// <summary>
        /////// The send interval.
        /////// </summary>
        ////private int sendInterval;

        /// <summary>
        /// The use tcp.
        /// </summary>
        private bool useTcp;

        /// <summary>
        /// Gets or sets a value indicating whether AutoMove.
        /// </summary>
        public override bool AutoMove
        {
            get
            {
                return this.autoMove;
            }

            set
            {
                this.autoMove = value;
            }
        }

        /// <summary>
        /// Gets or sets IntervalMove.
        /// </summary>
        public override int AutoMoveInterval
        {
            get
            {
                return this.autoMoveInterval;
            }

            set
            {
                this.autoMoveInterval = value;
            }
        }

        /// <summary>
        /// Gets or sets Velocity.
        /// </summary>
        public override int AutoMoveVelocity
        {
            get
            {
                return this.autoMoveVelocity;
            }

            set
            {
                this.autoMoveVelocity = value;
            }
        }

        private int sendInterval = 100;

        /// <summary>
        /// Gets or sets IntervalSend.
        /// </summary>
        public override int SendInterval
        {
            get
            {
                return this.sendInterval;
            }

            set
            {
                this.sendInterval = value;
            }
        }

        /// <summary>
        /// Gets or sets a value indicating whether UseTcp.
        /// </summary>
        public bool UseTcp
        {
            get
            {
                return this.useTcp;
            }

            set
            {
                this.useTcp = value;
            }
        }

        #endregion


        #region Ctor

        public PopBloopSettings()
        {
        }
        
        #endregion 


        #region Methods



        #endregion
    }
}
