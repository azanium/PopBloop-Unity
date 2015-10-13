using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PB.Game.AssetsPath
{
    public class AssetsPath
    {
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
        public static string DevelopmentServer
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

        public string WebServer
        {
            get { return DevelopmentServer; }
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

        public static string LevelAssetsUrl
        {
            get
            {
                return WebURL + "bundles/level/";//"office/assets/level/";

            }
        }

        /// <summary>
        /// Return the root asset bundle url path
        /// </summary>
        public static string WebURL
        {
            get
            {
                return DevelopmentServer;
            }
        }

        /// <summary>
        /// Return the character asset bundle url path
        /// </summary>
        public static string CharactersBundleBaseURL
        {
            get
            {
                return DevelopmentServer + "bundles/characters/";//"office/assets/characters/";
            }
        }

        /// <summary>
        /// Return the Materials asset bundle url path
        /// </summary>
        public static string MaterialsBundleBaseURL
        {
            get
            {
                return WebURL + "bundles/materials/";//"office/assets/materials/";
            }
        }

        /// <summary>
        /// Return the Animations asset bundle url path
        /// </summary>
        public static string AnimationsBundleBaseURL
        {
            get
            {
                return WebURL + "bundles/animations/";//"office/assets/animations/";
            }
        }

        /// <summary>
        /// Return World Asset Bundle URL Path
        /// </summary>
        public static string WorldBundleBaseURL
        {
            get
            {
                return WorldBundleBaseURL + "worlds/";
            }
        }

        /// <summary>
        /// Return Shout URL path using POST
        /// </summary>
        public static string ShoutBaseURL
        {
            get
            {
                return WebURL + "/message/guest/shout";
            }
        }

        /// <summary>
        /// Inventory API request URL
        /// </summary>
        public static string InventoryApiUrl
        {
            get
            {
                return WebURL + "api/unity/query/inventory";//"office/api/inventory/";
            }
        }

        /// <summary>
        /// Inventory Assets Url is where the inventory icons reside
        /// </summary>
        public static string InventoryAssetsUrl
        {
            get
            {
                return WebURL + "bundles/inventory/";//"office/assets/inventory/";
            }
        }

        /// <summary>
        /// Banner Url
        /// </summary>
        public static string BannerURL
        {
            get
            {
                return WebURL + "bundles/banners/";//"office/assets/banners/"; 
            }
        }


        /// <summary>
        /// Return Avatar Configuratin URL path
        /// </summary>
        /// <param name="id">the user id</param>
        /// <returns>the url</returns>
        public static string GetAvatarConfigurationURL(string id)
        {
            return WebURL + "api/unity/query/avatar_configuration/" + id;//"avatar/guest/get_configuration/" + id + ServerSecretKey;
        }

        public static string GetEditorAnimationsConfigurationURL(string gender)
        {
            return WebURL + "api/unity/query/editor_animation/" + gender; //"avatar/guest/get_editor_animation/" + gender;
        }

        public static string GetAnimationsConfigurationURL(string id)
        {
            return WebURL + "api/unity/query/avatar_animation/" + id; //"avatar/guest/get_animation/" + id + ServerSecretKey;
        }

        public static string GetDialogStoryURL(string id)
        {
            return WebURL + "api/unity/query/dialogstory/" + id; //"quest/admin/dialogstorydetail/" + id + ServerSecretKey;
        }

        public static string GetQuestStoryURL(string id)
        {
            return WebURL + "api/unity/query/quest/" + id; //"quest/admin/ws_quest/detail/" + id + ServerSecretKey;
        }

        /// <summary>
        /// Lobby Info Url
        /// </summary>
        public static string LobbyInfo
        {
            get { return WebURL + "api/unity/query/lobby"; } //"asset/guest/get_lobby" + ServerSecretKey; }
        }

        /// <summary>
        /// Url to upload Photo via POST
        /// </summary>
        public static string PhotoUploadUrl
        {
            get { return WebURL + "tester/fbaps/post_photo"; }//"user/guest/uploadprofilephoto"; }
        }

        /// <summary>
        /// Get the latest uploaded photo from the server
        /// </summary>
        public static string PhotoUploadLatestFilenameUrl
        {
            get { return WebURL + "ui/user/photolastname"; }
        }

        /// <summary>
        /// Share the uploaded photo to the facebook
        /// POST parameter - nama_capturan_jpg_png
        /// </summary>
        public static string PhotoUploadShareToFacebookUrl(string filename)
        {
            return WebURL + "ui/user/uploadphototofacebook/" + filename;
        }

        /// <summary>
        /// Share the latest uploaded photo album to facebook
        /// </summary>
        public static string PhotoUploadShareAlbumToFacebookUrl
        {
            get
            {
                return WebURL + "ui/user/uploadphotoalbumfbapp";
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
            return WebURL + string.Format("office/api/send_redeem_email/{0}/{1}/", sessionid, redeem);
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
            get { return WebURL + "api/unity/query/gesticon"; }
        }

        /// <summary>
        /// Generate Redeem Avatar Url
        /// </summary>
        /// <param name="avatarItemId"></param>
        /// <param name="count"></param>
        /// <returns></returns>
        public static string GenerateRedeemAvatarUrl(string avatarItemId)
        {
            return string.Format("{0}api/unity/query/r_gen/{1}/", WebURL, avatarItemId);
        }

        #endregion
    }
}
