using UnityEngine;
using System;
using System.Collections.Generic;
using System.Collections;


// Any methods that Obj-C calls back using UnitySendMessage should be present here
public class SocialNetworkingManager : MonoBehaviour
{
	// Events and delegates
	public delegate void SocialNetworkingErrorEventHandler( string error );
	public delegate void SocialNetworkingStringEventHandler( string result );
	public delegate void SocialNetworkingArrayListEventHandler( ArrayList result );
	public delegate void SocialNetworkingObjectEventHandler( object result );
	public delegate void SocialNetworkingEventHandler();
	
	// Twitter
	// Fired after a successful login attempt was made
	public static event SocialNetworkingEventHandler twitterLogin;
	
	// Fired when an error occurs while logging in
	public static event SocialNetworkingErrorEventHandler twitterLoginFailed;
	
	// Fired after successfully sending a status update
	public static event SocialNetworkingEventHandler twitterPost;
	
	// Fired when a status update fails
	public static event SocialNetworkingErrorEventHandler twitterPostFailed;
	
	// Fired when the home timeline is received
	public static event SocialNetworkingArrayListEventHandler twitterHomeTimelineReceived;
	
	// Fired when a request for the home timeline fails
	public static event SocialNetworkingErrorEventHandler twitterHomeTimelineFailed;
	
	
	// Facebook
	// Fired after a successful login attempt was made
	public static event SocialNetworkingEventHandler facebookLogin;
	
	// Fired when an error occurs while logging in
	public static event SocialNetworkingErrorEventHandler facebookLoginFailed;
	
	// Fired after requesting the logged in users name
	public static event SocialNetworkingStringEventHandler facebookReceivedUsername;
	
	// Fired when failing to get a logged in users name
	public static event SocialNetworkingErrorEventHandler facebookUsernameRequestFailed;
	
	// Fired after successfully sending a status update
	public static event SocialNetworkingEventHandler facebookPost;
	
	// Fired when a status update fails
	public static event SocialNetworkingErrorEventHandler facebookPostFailed;
	
	// Fired when a friend request finishes
	public static event SocialNetworkingArrayListEventHandler facebookReceivedFriends;
	
	// Fired when a friend request fails
	public static event SocialNetworkingErrorEventHandler facebookFriendRequestFailed;
	
	// Fired when the post message dialog completes
	public static event SocialNetworkingEventHandler facebookDialogCompleted;
	
	// Fired when the post message dialog fails
	public static event SocialNetworkingErrorEventHandler facebookDialogFailed;
	
	// Fired when the post message dialog does not complete
	public static event SocialNetworkingEventHandler facebookDialogDidntComplete;
	
	// Fired when a custom graph request finishes
	public static event SocialNetworkingObjectEventHandler facebookReceivedCustomRequest;
	
	// Fired when a custom graph request fails
	public static event SocialNetworkingErrorEventHandler facebookCustomRequestFailed;
	
	

    void Awake()
    {
		// Set the GameObject name to the class name for easy access from Obj-C
		gameObject.name = this.GetType().ToString();
		DontDestroyOnLoad( this );
    }
	
	
	#region Twitter
	
	// Twitter
	public void twitterLoginSucceeded( string empty )
	{
		if( twitterLogin != null )
			twitterLogin();
	}
	
	
	public void twitterLoginDidFail( string error )
	{
		if( twitterLoginFailed != null )
			twitterLoginFailed( error );
	}
	
	
	public void twitterPostSucceeded( string empty )
	{
		if( twitterPost != null )
			twitterPost();
	}
	
	
	public void twitterPostDidFail( string error )
	{
		if( twitterPostFailed != null )
			twitterPostFailed( error );
	}
	
	
	public void twitterHomeTimelineDidFail( string error )
	{
		if( twitterHomeTimelineFailed != null )
			twitterHomeTimelineFailed( error );
	}
	
	
	public void twitterHomeTimelineDidFinish( string results )
	{
		if( twitterHomeTimelineReceived != null )
		{
			ArrayList resultList = (ArrayList)MiniJSON.JsonDecode( results );
			twitterHomeTimelineReceived( resultList );
		}
	}
	
	#endregion;
	
	
	#region Facebook

	// Facebook
	public void facebookLoginSucceeded( string empty )
	{
		if( facebookLogin != null )
			facebookLogin();
	}
	
	
	public void facebookLoginDidFail( string error )
	{
		if( facebookLoginFailed != null )
			facebookLoginFailed( error );
	}
	
	
	public void facebookDidReceiveUsername( string username )
	{
		if( facebookReceivedUsername != null )
			facebookReceivedUsername( username );
	}
	
	
	public void facebookUsernameRequestDidFail( string error )
	{
		if( facebookUsernameRequestFailed != null )
			facebookUsernameRequestFailed( error );
	}
	
	
	public void facebookPostSucceeded( string empty )
	{
		if( facebookPost != null )
			facebookPost();
	}
	
	
	public void facebookPostDidFail( string error )
	{
		if( facebookPostFailed != null )
			facebookPostFailed( error );
	}


	public void facebookDidReceiveFriends( string jsonResult )
	{
		if( facebookReceivedFriends != null )
		{
			Hashtable friendList = (Hashtable)MiniJSON.JsonDecode( jsonResult );
			
			if( friendList.Contains( "data" ) )
				facebookReceivedFriends( (ArrayList)friendList["data"] );
			else
				facebookReceivedFriends( new ArrayList() );
		}
	}
	
	
	public void facebookFriendRequestDidFail( string error )
	{
		if( facebookFriendRequestFailed != null )
			facebookFriendRequestFailed( error );
	}
	
	
	public void facebookDialogDidComplete( string empty )
	{
		if( facebookDialogCompleted != null )
			facebookDialogCompleted();
	}


	public void facebookDialogDidNotComplete( string empty )
	{
		if( facebookDialogDidntComplete != null )
			facebookDialogDidntComplete();
	}
	
	
	public void facebookDialogDidFailWithError( string error )
	{
		if( facebookDialogFailed != null )
			facebookDialogFailed( error );
	}


	public void facebookDidReceiveCustomRequest( string result )
	{
		if( facebookReceivedCustomRequest != null )
		{
			object obj = MiniJSON.JsonDecode( result );
			facebookReceivedCustomRequest( obj );
		}
	}
	
	
	public void facebookCustomRequestDidFail( string error )
	{
		if( facebookCustomRequestFailed != null )
			facebookCustomRequestFailed( error );
	}
	
	
	#endregion;

}