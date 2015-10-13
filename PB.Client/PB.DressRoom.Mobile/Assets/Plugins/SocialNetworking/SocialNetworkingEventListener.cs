using UnityEngine;
using System.Collections;


public class SocialNetworkingEventListener : MonoBehaviour
{
	// Listens to all the events.  All event listeners MUST be removed before this object is disposed!
	void OnEnable()
	{
		// Twitter
		SocialNetworkingManager.twitterLogin += twitterLogin;
		SocialNetworkingManager.twitterLoginFailed += twitterLoginFailed;
		SocialNetworkingManager.twitterPost += twitterPost;
		SocialNetworkingManager.twitterPostFailed += twitterPostFailed;
		SocialNetworkingManager.twitterHomeTimelineReceived += twitterHomeTimelineReceived;
		SocialNetworkingManager.twitterHomeTimelineFailed += twitterHomeTimelineFailed;
		
		// Facebook
		SocialNetworkingManager.facebookLogin += facebookLogin;
		SocialNetworkingManager.facebookLoginFailed += facebookLoginFailed;
		SocialNetworkingManager.facebookReceivedUsername += facebookReceivedUsername;
		SocialNetworkingManager.facebookUsernameRequestFailed += facebookUsernameRequestFailed;
		SocialNetworkingManager.facebookPost += facebookPost;
		SocialNetworkingManager.facebookPostFailed += facebookPostFailed;
		
		SocialNetworkingManager.facebookReceivedFriends += facebookReceivedFriends;
		SocialNetworkingManager.facebookFriendRequestFailed += facebookFriendRequestFailed;
		SocialNetworkingManager.facebookDialogCompleted += facebokDialogCompleted;
		SocialNetworkingManager.facebookDialogDidntComplete += facebookDialogDidntComplete;
		SocialNetworkingManager.facebookDialogFailed += facebookDialogFailed;
		SocialNetworkingManager.facebookReceivedCustomRequest += facebookReceivedCustomRequest;
		SocialNetworkingManager.facebookCustomRequestFailed += facebookCustomRequestFailed;
	}

	
	void OnDisable()
	{
		// Remove all the event handlers
		// Twitter
		SocialNetworkingManager.twitterLogin -= twitterLogin;
		SocialNetworkingManager.twitterLoginFailed -= twitterLoginFailed;
		SocialNetworkingManager.twitterPost -= twitterPost;
		SocialNetworkingManager.twitterPostFailed -= twitterPostFailed;
		SocialNetworkingManager.twitterHomeTimelineReceived -= twitterHomeTimelineReceived;
		SocialNetworkingManager.twitterHomeTimelineFailed -= twitterHomeTimelineFailed;
		
		// Facebook
		SocialNetworkingManager.facebookLogin -= facebookLogin;
		SocialNetworkingManager.facebookLoginFailed -= facebookLoginFailed;
		SocialNetworkingManager.facebookReceivedUsername -= facebookReceivedUsername;
		SocialNetworkingManager.facebookUsernameRequestFailed -= facebookUsernameRequestFailed;
		SocialNetworkingManager.facebookPost -= facebookPost;
		SocialNetworkingManager.facebookPostFailed -= facebookPostFailed;
		
		SocialNetworkingManager.facebookReceivedFriends -= facebookReceivedFriends;
		SocialNetworkingManager.facebookFriendRequestFailed += facebookFriendRequestFailed;
		SocialNetworkingManager.facebookDialogCompleted -= facebokDialogCompleted;
		SocialNetworkingManager.facebookDialogDidntComplete -= facebookDialogDidntComplete;
		SocialNetworkingManager.facebookDialogFailed -= facebookDialogFailed;
		SocialNetworkingManager.facebookReceivedCustomRequest -= facebookReceivedCustomRequest;
		SocialNetworkingManager.facebookCustomRequestFailed -= facebookCustomRequestFailed;
	}

	
	// Twitter events
	void twitterLogin()
	{
		Debug.Log( "Successfully logged in to Twitter" );
	}
	
	
	void twitterLoginFailed( string error )
	{
		Debug.Log( "Twitter login failed: " + error );
	}
	

	void twitterPost()
	{
		Debug.Log( "Successfully posted to Twitter" );
	}
	

	void twitterPostFailed( string error )
	{
		Debug.Log( "Twitter post failed: " + error );
	}


	void twitterHomeTimelineFailed( string error )
	{
		Debug.Log( "Twitter HomeTimeline failed: " + error );
	}
	
	
	void twitterHomeTimelineReceived( ArrayList result )
	{
		Debug.Log( "received home timeline with tweet count: " + result.Count );
	}
	
	
	// Facebook events
	void facebookLogin()
	{
		Debug.Log( "Successfully logged in to Facebook" );
	}
	
	
	void facebookLoginFailed( string error )
	{
		Debug.Log( "Facebook login failed: " + error );
	}
	

	void facebookReceivedUsername( string username )
	{
		Debug.Log( "Facebook logged in users name: " + username );
	}
	
	
	void facebookUsernameRequestFailed( string error )
	{
		Debug.Log( "Facebook failed to receive username: " + error );
	}
	
	
	void facebookPost()
	{
		Debug.Log( "Successfully posted to Facebook" );
	}
	

	void facebookPostFailed( string error )
	{
		Debug.Log( "Facebook post failed: " + error );
	}


	void facebookReceivedFriends( ArrayList result )
	{
		Debug.Log( "received total friends: " + result.Count );
	}
	
	
	void facebookFriendRequestFailed( string error )
	{
		Debug.Log( "FfacebookFriendRequestFailed: " + error );
	}
	
	
	void facebokDialogCompleted()
	{
		Debug.Log( "facebokDialogCompleted" );
	}
	
	
	void facebookDialogDidntComplete()
	{
		Debug.Log( "facebookDialogDidntComplete" );
	}
	
	
	void facebookDialogFailed( string error )
	{
		Debug.Log( "facebookDialogFailed: " + error );
	}
	
	
	void facebookReceivedCustomRequest( object obj )
	{
		Debug.Log( "facebookReceivedCustomRequest" );
	}
	
	
	void facebookCustomRequestFailed( string error )
	{
		Debug.Log( "facebookCustomRequestFailed failed: " + error );
	}
	
}
