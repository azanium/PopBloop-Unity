using UnityEngine;
using System.Collections;
using System.Runtime.InteropServices;


// All Objective-C exposed methods should be bound here
public class TwitterBinding
{
    [DllImport("__Internal")]
    private static extern void _twitterInit( string consumerKey, string consumerSecret );
 
	// Initializes the Twitter plugin and sets up the required oAuth information
    public static void init( string consumerKey, string consumerSecret )
    {
        if( Application.platform == RuntimePlatform.IPhonePlayer )
			_twitterInit( consumerKey, consumerSecret );
    }
	
	
    [DllImport("__Internal")]
    private static extern bool _twitterIsLoggedIn();
 
	// Checks to see if there is a currently logged in user
    public static bool isLoggedIn()
    {
        if( Application.platform == RuntimePlatform.IPhonePlayer )
			return _twitterIsLoggedIn();
		return false;
    }
	
	
    [DllImport("__Internal")]
    private static extern string _twitterLoggedInUsername();
 
	// Retuns the currently logged in user's username
    public static string loggedInUsername()
    {
        if( Application.platform == RuntimePlatform.IPhonePlayer )
			return _twitterLoggedInUsername();
		return string.Empty;
    }
	

    [DllImport("__Internal")]
    private static extern void _twitterLogin( string username, string password );
 
	// Logs in the user using xAuth
    public static void login( string username, string password )
    {
        if( Application.platform == RuntimePlatform.IPhonePlayer )
			_twitterLogin( username, password );
    }

	
    [DllImport("__Internal")]
    private static extern void _twitterLogout();
 
	// Logs out the current user
    public static void logout()
    {
        if( Application.platform == RuntimePlatform.IPhonePlayer )
			_twitterLogout();
    }


    [DllImport("__Internal")]
    private static extern void _twitterPostStatusUpdate( string status );
 
	// Posts the status text.  Be sure status text is less than 140 characters!
    public static void postStatusUpdate( string status )
    {
        if( Application.platform == RuntimePlatform.IPhonePlayer )
			_twitterPostStatusUpdate( status );
    }
    
    
    [DllImport("__Internal")]
    private static extern void _twitterGetHomeTimeline();
 
	// Receives tweets from the users home timeline
    public static void getHomeTimeline()
    {
        if( Application.platform == RuntimePlatform.IPhonePlayer )
			_twitterGetHomeTimeline();
    }

}
