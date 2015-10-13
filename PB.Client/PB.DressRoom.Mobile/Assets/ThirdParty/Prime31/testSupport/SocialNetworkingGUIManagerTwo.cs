using UnityEngine;
using System.Collections;
using System;
using System.IO;


public class SocialNetworkingGUIManagerTwo : MonoBehaviour
{
	void Start()
	{
		// Dump friends list to log
		SocialNetworkingManager.facebookReceivedFriends += delegate( ArrayList result )
		{
			ResultLogger.logArraylist( result );
		};
		
		// Dump custom data to log
		SocialNetworkingManager.facebookReceivedCustomRequest += delegate( object result )
		{
			ResultLogger.logObject( result );
		};
	}
	

	void OnGUI()
	{
		float yPos = 25.0f;
		float xPos = 20.0f;
		float width = 160.0f;
		float buttonHeight = 35.0f;
		float heightPlus = buttonHeight + 5.0f;
		
		
		if( GUI.Button( new Rect( xPos, yPos, width, buttonHeight ), "Post Message" ) )
		{
			FacebookBinding.postMessage( "im posting this from Unity: " + Time.deltaTime );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Post Message & More" ) )
		{
			FacebookBinding.postMessageWithLinkAndLinkToImage( "link post from Unity: " + Time.deltaTime, "http://prime31.com", "Prime31 Studios", "http://prime31.com/assets/images/prime31logo.png", "Prime31 Logo" );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Get Friends" ) )
		{
			FacebookBinding.getFriends();
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Post Message Dialog" ) )
		{
			FacebookBinding.showPostMessageDialogWithMessage( "Hi, this game rules!" );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Dialog With Options" ) )
		{
			FacebookBinding.showPostMessageDialogWithOptions( "This will be the message", "http://prime31.com", "Prime31 Studios", string.Empty, string.Empty );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus * 2, width, buttonHeight ), "Back" ) )
		{
			Application.LoadLevel( "SocialNetworkingtestScene" );
		}
		
		
		yPos = 25.0f;
		xPos = width + 90.0f;
		
		// Twitter
		if( GUI.Button( new Rect( xPos, yPos, width, buttonHeight ), "Graph Request (me)" ) )
		{
			FacebookBinding.graphRequest( "me", "GET", new Hashtable() );
		}
		

		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Custom Graph Request" ) )
		{
			FacebookBinding.graphRequest( "platform/posts", "GET", new Hashtable() );
		}	

	}

}
