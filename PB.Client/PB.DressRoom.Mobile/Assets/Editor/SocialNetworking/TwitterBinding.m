//
//  TwitterBinding.m
//  SocialNetworking
//
//  Created by Mike on 9/18/10.
//  Copyright 2010 Prime31 Studios. All rights reserved.
//

#import "TwitterManager.h"


// Converts NSString to C style string by way of copy (Mono will free it)
#define MakeStringCopy( _x_ ) ( _x_ != NULL && [_x_ isKindOfClass:[NSString class]] ) ? strdup( [_x_ UTF8String] ) : NULL

// Converts C style string to NSString
#define GetStringParam( _x_ ) ( _x_ != NULL ) ? [NSString stringWithUTF8String:_x_] : [NSString stringWithUTF8String:""]


void _twitterInit( const char * consumerKey, const char * consumerSecret )
{
	[TwitterManager sharedManager].consumerKey = GetStringParam( consumerKey );
	[TwitterManager sharedManager].consumerSecret = GetStringParam( consumerSecret );
}


bool _twitterIsLoggedIn()
{
	return [[TwitterManager sharedManager] isLoggedIn];
}


const char * _twitterLoggedInUsername()
{
	NSString *username = [[TwitterManager sharedManager] loggedInUsername];
	return MakeStringCopy( username );
}


void _twitterLogin( const char * username, const char * password )
{
	[[TwitterManager sharedManager] xAuthLoginWithUsername:GetStringParam( username ) password:GetStringParam( password )];
}


void _twitterLogout()
{
	[[TwitterManager sharedManager] logout];
}


void _twitterPostStatusUpdate( const char * status )
{
	[[TwitterManager sharedManager] postStatusUpdate:GetStringParam( status )];
}


void _twitterGetHomeTimeline()
{
	[[TwitterManager sharedManager] getHomeTimeline];
}
