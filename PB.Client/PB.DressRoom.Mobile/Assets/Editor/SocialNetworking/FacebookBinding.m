//
//  FacebookBinding.m
//  Facebook
//
//  Created by Mike on 9/13/10.
//  Copyright 2010 Prime31 Studios. All rights reserved.
//

#import "FacebookManager.h"
#import "JSON.h"


// Converts NSString to C style string by way of copy (Mono will free it)
#define MakeStringCopy( _x_ ) ( _x_ != NULL && [_x_ isKindOfClass:[NSString class]] ) ? strdup( [_x_ UTF8String] ) : NULL

// Converts C style string to NSString
#define GetStringParam( _x_ ) ( _x_ != NULL ) ? [NSString stringWithUTF8String:_x_] : [NSString stringWithUTF8String:""]

// Converts C style string to NSString as long as it isnt empty
#define GetStringParamOrNil( _x_ ) ( _x_ != NULL && strlen( _x_ ) ) ? [NSString stringWithUTF8String:_x_] : nil


void _facebookInit( const char * appId )
{
	[FacebookManager sharedManager].appId = GetStringParam( appId );
}


bool _facebookIsLoggedIn()
{
	return [[FacebookManager sharedManager] isLoggedIn];
}


void _facebookLogin()
{
	[[FacebookManager sharedManager] login];
}


void _facebookLogout()
{
	[[FacebookManager sharedManager] logout];
}


void _facebookGetLoggedinUsersName()
{
	[[FacebookManager sharedManager] getLoggedInUsername];
}


void _facebookPostMessage( const char * message )
{
	[[FacebookManager sharedManager] postMessage:GetStringParam( message )];
}


void _facebookPostMessageWithLink( const char * message, const char * link, const char * linkName )
{
	[[FacebookManager sharedManager] postMessage:GetStringParam( message ) link:GetStringParam( link ) linkName:GetStringParam( linkName )];
}


void _facebookPostMessageWithLinkAndLinkToImage( const char * message, const char * link, const char * linkName, const char * linkToImage, const char * caption )
{
	[[FacebookManager sharedManager] postMessage:GetStringParam( message ) link:GetStringParam( link ) linkName:GetStringParam( linkName ) linkToImage:GetStringParam( linkToImage ) caption:GetStringParam( caption )];
}


void _facebookPostImage( const char * pathToImage, const char * caption )
{
	[[FacebookManager sharedManager] postPhoto:GetStringParam( pathToImage ) caption:GetStringParam( caption )];
}


void _facebookGetFriends()
{
	[[FacebookManager sharedManager] getFriends];
}


void _facebookShowPostMessageDialog()
{
	[[FacebookManager sharedManager] showPostMessageDialog];
}


void _facebookShowPostMessageDialogWithMessage( const char * message )
{
	[[FacebookManager sharedManager] showPostMessageDialogWithMessage:GetStringParam( message )];
}


void _facebookShowPostMessageDialogWithOptions( const char * message, const char * link, const char * linkName, const char * linkToImage, const char * caption )
{
	[[FacebookManager sharedManager] postMessageDialog:GetStringParamOrNil( message )
												  link:GetStringParamOrNil( link )
											  linkName:GetStringParamOrNil( linkName )
										   linkToImage:GetStringParamOrNil( linkToImage )
											   caption:GetStringParamOrNil( caption )];
}


void _facebookGraphRequest( const char * graphPath, const char * httpMethod, const char * jsonDict )
{
	// make sure we have a legit dictionary
	NSString *jsonString = GetStringParam ( jsonDict );
	NSMutableDictionary *dict = [jsonString JSONValue];
	
	if( ![dict isKindOfClass:[NSMutableDictionary class]] )
		return;
	
	[[FacebookManager sharedManager] requestWithGraphPath:GetStringParam( graphPath )
											   httpMethod:GetStringParam( httpMethod )
												   params:dict];
}

