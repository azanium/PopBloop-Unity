using UnityEngine;
using System.Collections;

//If you want to add new platforms to this enumeration, either put them at the end or give it a unique number
// NOTE: The "EditorPlatform" enum may also need to be updated as well to reflect changes made here
public enum Platform {iPhone=1, WebPlayer=2, iPad=3, iPhoneRetina=4, Standalone=5, Android=6, FlashPlayer=7, NaCl=8, iPadRetina=9, iPhone5=10, WP8=11, Windows8=12, iOS=13};

public class Platforms : MonoBehaviour {

	const float ratioTolerance = 0.03f; //Tolerance of calculated ratio to exact ratio
	
	static bool platformCalculated = false;
	static Platform calculatedPlatform;
	
	public const string editorPlatformOverrideKey = "editorPlatformOverride";
	
	public static Platform platform {
		get {
#if UNITY_EDITOR
			//If in editor and platformOverride is set, return override
			string platformString = UnityEditor.EditorPrefs.GetString(editorPlatformOverrideKey);
			platformCalculated = true;
			if(platformString == Platform.iPhone.ToString()) {
				calculatedPlatform = Platform.iPhone;
			} 
			else if(platformString == Platform.iPhoneRetina.ToString()) {
				calculatedPlatform = Platform.iPhoneRetina;
			} 
			else if(platformString == Platform.Android.ToString()) {
				calculatedPlatform = Platform.Android;
			} 
			else if(platformString == Platform.FlashPlayer.ToString()) {
				calculatedPlatform = Platform.FlashPlayer;
			}
			else if(platformString == Platform.NaCl.ToString()) {
				calculatedPlatform = Platform.NaCl;	
			} 
			else if(platformString == Platform.iPad.ToString()) {
				calculatedPlatform = Platform.iPad;
			}
			else if(platformString == Platform.iPadRetina.ToString()) {
				calculatedPlatform = Platform.iPadRetina;
			}
			else if(platformString == Platform.WebPlayer.ToString()) {
				calculatedPlatform = Platform.WebPlayer;
			} 
			else if(platformString == Platform.WP8.ToString()) {
				calculatedPlatform = Platform.WP8;
			}
			else if(platformString == Platform.Windows8.ToString()) {
				calculatedPlatform = Platform.Windows8;
			}
			else if(platformString == Platform.iPhone5.ToString()) {
				calculatedPlatform = Platform.iPhone5;
			}
			else if(platformString == Platform.iOS.ToString()) {
				calculatedPlatform = Platform.iOS;
			}
			else {
				calculatedPlatform = Platform.Standalone;
			}
#endif
			if(!platformCalculated) { //If platform wasn't calculated before, calculate now
				if(Application.platform == RuntimePlatform.IPhonePlayer) {
					#if UNITY_IPHONE
					int screenWidth = (int) Screen.width;
					int screenHeight = (int) Screen.height;
					
					if(screenWidth == 480 || screenWidth == 320) {
						calculatedPlatform = Platform.iPhone;
					} else if((screenWidth == 960 && screenHeight == 640) || (screenWidth == 640 && screenHeight == 960)) {
						calculatedPlatform = Platform.iPhoneRetina;
					} else if(screenWidth == 1024 || screenWidth == 768) {
						calculatedPlatform = Platform.iPad;
					} else if(screenWidth == 2048 || screenWidth == 1536) {
						calculatedPlatform = Platform.iPadRetina;
					} else if((screenWidth == 1136 && screenHeight == 640) || (screenWidth == 640 && screenHeight == 1136)) {
						calculatedPlatform = Platform.iPhone5;
					} else {
						calculatedPlatform = Platform.iPhone; //Default to iPhone
					}
					#endif
				} 
				else if(Application.platform == RuntimePlatform.Android) { 
					calculatedPlatform = Platform.Android; //exact screen size will be calculated per-Aspect Ratio
				} 
				else if(Application.platform == RuntimePlatform.FlashPlayer) { 
					calculatedPlatform = Platform.FlashPlayer; //exact screen size will be calculated per-Aspect Ratio
				} 
				else if(Application.platform == RuntimePlatform.NaCl) { 
					calculatedPlatform = Platform.NaCl; //exact screen size will be calculated per-Aspect Ratio
				} 
				else if(Application.platform == RuntimePlatform.WindowsWebPlayer || Application.platform == RuntimePlatform.OSXWebPlayer) {
					calculatedPlatform = Platform.WebPlayer; //exact screen size will be calculated per-Aspect Ratio 
				}
#if !UNITY_3_5 && !UNITY_4_0 && !UNITY_4_1
				else if(Application.platform == RuntimePlatform.MetroPlayerARM) {
					calculatedPlatform = Platform.WP8; //exact screen size will be calculated per-Aspect Ratio
				}
				else if(Application.platform == RuntimePlatform.MetroPlayerX86 || Application.platform == RuntimePlatform.MetroPlayerX64) {
					calculatedPlatform = Platform.WP8; //exact screen size will be calculated per-Aspect Ratio
				}
#endif
				else {
					calculatedPlatform = Platform.Standalone; //exact screen size will be calculated per-Aspect Ratio
				}
				platformCalculated = true;
				
			}
			return calculatedPlatform;
		}
	}
	
	public static bool IsPlatformAspectBased(string plat) {
		return plat == Platform.Standalone.ToString() 
			|| plat == Platform.Android.ToString() 
			|| plat == Platform.FlashPlayer.ToString() 
			|| plat == Platform.WP8.ToString() 
			|| plat == Platform.Windows8.ToString() 
			|| plat == Platform.NaCl.ToString();
	}
	
	public static bool IsiOS {
		get {
			return (Platforms.platform == Platform.iOS || Platforms.platform == Platform.iPad || Platforms.platform == Platform.iPhone || Platforms.platform == Platform.iPadRetina || Platforms.platform == Platform.iPhoneRetina || Platforms.platform == Platform.iPhone5); 
		}
	}
	
	public static bool IsiOSPlatform(Platform platform) {
		return (platform == Platform.iOS || platform == Platform.iPad || platform == Platform.iPhone || platform == Platform.iPadRetina || platform == Platform.iPhoneRetina || platform == Platform.iPhone5); 
	}
	
	//detect if we're dealing with an iOS device thats NOT iPhone4S or iPad2 or iPad3 or iPhone 5
	//Since we can't guarantee that Unity has the most up to date list of device generations
	//we catch the old device generations
	public static bool IsiOSSlower {
		get { 
			if(IsiOS){
#if UNITY_IPHONE
	#if UNITY_3_5
	if(iPhoneSettings.generation == iPhoneGeneration.iPad1Gen 
	   || iPhoneSettings.generation == iPhoneGeneration.iPhone
	   || iPhoneSettings.generation == iPhoneGeneration.iPhone3G
	   || iPhoneSettings.generation == iPhoneGeneration.iPhone4
	   || iPhoneSettings.generation == iPhoneGeneration.iPhone3GS
	   || iPhoneSettings.generation == iPhoneGeneration.iPodTouch1Gen
	   || iPhoneSettings.generation == iPhoneGeneration.iPodTouch2Gen
	   || iPhoneSettings.generation == iPhoneGeneration.iPodTouch3Gen
	   || iPhoneSettings.generation == iPhoneGeneration.iPodTouch4Gen
	   )
		return true; //we found a comparatively slow iOS device 
	else
		return false; //not slow - its an iPad2 or iPhone4S or newer	
	#else
	if(iPhone.generation == iPhoneGeneration.iPad1Gen 
	   || iPhone.generation == iPhoneGeneration.iPhone
	   || iPhone.generation == iPhoneGeneration.iPhone3G
	   || iPhone.generation == iPhoneGeneration.iPhone4
	   || iPhone.generation == iPhoneGeneration.iPhone3GS
	   || iPhone.generation == iPhoneGeneration.iPodTouch1Gen
	   || iPhone.generation == iPhoneGeneration.iPodTouch2Gen
	   || iPhone.generation == iPhoneGeneration.iPodTouch3Gen
	   || iPhone.generation == iPhoneGeneration.iPodTouch4Gen
	)
		return true; //we found a comparatively slow iOS device 
	else
		return false; //not slow - its an iPad2 or iPhone4S or newer
	#endif
#else
				return false;
#endif
			} else { 
				return false; //not slow (or not iOS) 
			}
		} 
	}
}
