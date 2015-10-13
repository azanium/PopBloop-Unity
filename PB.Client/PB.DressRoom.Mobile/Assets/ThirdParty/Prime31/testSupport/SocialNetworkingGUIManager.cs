using UnityEngine;
using System.Collections.Generic;
using System.Collections;
using System.Text;


public class SocialNetworkingGUIManager : MonoBehaviour
{
	void Start()
	{
		// Sample of how to get to the data available in the tweets
		SocialNetworkingManager.twitterHomeTimelineReceived += delegate( ArrayList result )
		{
			ResultLogger.logArraylist( result );
		};
		
		// Sample of how to get the results of a custom facebook graph request
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
		
		
		// Facebook
		if( GUI.Button( new Rect( xPos, yPos, width, buttonHeight ), "Initialize" ) )
		{
			FacebookBinding.init( "YOUR_APP_ID_HERE" );
			FacebookBinding.init( "140795189296924" );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Is Logged In?" ) )
		{
			bool isLoggedIn = FacebookBinding.isLoggedIn();
			Debug.Log( "Facebook is logged in: " + isLoggedIn );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Login" ) )
		{
			FacebookBinding.login();
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Logout" ) )
		{
			FacebookBinding.logout();
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Get User's Name" ) )
		{
			FacebookBinding.getLoggedinUsersName();
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus * 2, width, buttonHeight ), "More Facebook..." ) )
		{
			Application.LoadLevel( "SocialNetworkingtestSceneTwo" );
		}

		
		
		yPos = 25.0f;
		xPos = width + 90.0f;
		
		// Twitter
		if( GUI.Button( new Rect( xPos, yPos, width, buttonHeight ), "Initialize" ) )
		{
			TwitterBinding.init( "REPLACE_WITH_YOUR_INFO", "REPLACE_WITH_YOUR_INFO" );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Is Logged In?" ) )
		{
			bool isLoggedIn = TwitterBinding.isLoggedIn();
			Debug.Log( "Twitter is logged in: " + isLoggedIn );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Logged in Username" ) )
		{
			string username = TwitterBinding.loggedInUsername();
			Debug.Log( "Twitter username: " + username );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Login" ) )
		{
			TwitterBinding.login( "REPLACE_WITH_YOUR_INFO", "REPLACE_WITH_YOUR_INFO" );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Logout" ) )
		{
			TwitterBinding.logout();
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Post Status Update" ) )
		{
			TwitterBinding.postStatusUpdate( "im posting this from Unity: " + Time.deltaTime );
		}
		
		
		if( GUI.Button( new Rect( xPos, yPos += heightPlus, width, buttonHeight ), "Get Home Timeline" ) )
		{
			TwitterBinding.getHomeTimeline();
		}
	}


}
