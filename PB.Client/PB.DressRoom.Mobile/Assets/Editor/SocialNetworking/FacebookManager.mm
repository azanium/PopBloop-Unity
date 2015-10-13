//
//  FacebookManager.m
//  Facebook
//
//  Created by Mike on 9/13/10.
//  Copyright 2010 Prime31 Studios. All rights reserved.
//

#import "FacebookManager.h"


void UnitySendMessage( const char * className, const char * methodName, const char * param );

void UnityPause( bool pause );


@implementation FacebookManager

@synthesize appId = _appId;

///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark NSObject

+ (FacebookManager*)sharedManager
{
	static FacebookManager *sharedSingleton;
	
	if( !sharedSingleton )
		sharedSingleton = [[FacebookManager alloc] init];
	
	return sharedSingleton;
}


- (id)init
{
	if( ( self = [super init] ) )
	{
		// get a handle on the facebook master plan
		_facebook = [[Facebook alloc] init];
	}
	return self;
}


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark Public

- (BOOL)isLoggedIn
{
	return [_facebook isSessionValid];
}


- (void)login
{
	if( [self isLoggedIn] )
	{
		UnitySendMessage( "SocialNetworkingManager", "facebookLoginSucceeded", "" );
		return;
	}
	
	UnityPause( true );
	[_facebook authorize:_appId permissions:[NSArray arrayWithObject:@"publish_stream"] delegate:self];
}


- (void)logout
{
	[_facebook logout:self];
}


- (void)getLoggedInUsername
{
	[_facebook requestWithGraphPath:@"me" andDelegate:self];
}


- (void)postMessage:(NSString*)message
{
	[self postMessage:message link:nil linkName:nil];
}


- (void)postMessage:(NSString*)message link:(NSString*)link linkName:(NSString*)linkName
{
	[self postMessage:message link:link linkName:linkName linkToImage:nil caption:nil];
}
	 
	 
 // Allowed post params: message, picture (link), link, name (of link), caption, description (of link)
 - (void)postMessage:(NSString*)message link:(NSString*)link linkName:(NSString*)linkName linkToImage:(NSString*)linkToImage caption:(NSString*)caption
{
	NSMutableDictionary *params = [NSMutableDictionary dictionaryWithObjectsAndKeys:message, @"message", nil];
	
	if( link )
		[params setObject:link forKey:@"link"];
	
	if( linkName )
		[params setObject:linkName forKey:@"name"];
	
	if( linkToImage )
		[params setObject:linkToImage forKey:@"picture"];
	
	if( caption )
		[params setObject:caption forKey:@"caption"];
	
	[_facebook requestWithGraphPath:@"me/feed" andParams:params andHttpMethod:@"POST" andDelegate:self];
}


- (void)postPhoto:(NSString*)path caption:(NSString*)caption
{
	if( ![[NSFileManager defaultManager] fileExistsAtPath:path] )
	{
		NSLog( @"image does not exist: %@", path );
		return;
	}
	
	NSURL *url  = [NSURL fileURLWithPath:path];
	NSData *data = [NSData dataWithContentsOfURL:url];
	UIImage *img  = [[UIImage alloc] initWithData:data];
	
	NSMutableDictionary *params = [NSMutableDictionary dictionaryWithObjectsAndKeys:
									img, @"picture",
								   caption, @"message",
									nil];
	[_facebook requestWithGraphPath:@"me/photos" andParams:params andHttpMethod:@"POST" andDelegate:self];
	[img release];  
}


- (void)showPostMessageDialog
{
	[self showPostMessageDialogWithMessage:nil];
}


- (void)showPostMessageDialogWithMessage:(NSString*)message
{
	NSMutableDictionary *params = [NSMutableDictionary dictionaryWithObjectsAndKeys:_appId, @"api_key", nil];
	
	if( message )
		[params setObject:message forKey:@"message"];
	
    [_facebook dialog:@"stream.publish" andParams:params andDelegate:self];
}


- (void)postMessageDialog:(NSString*)message link:(NSString*)link linkName:(NSString*)linkName linkToImage:(NSString*)linkToImage caption:(NSString*)caption
{
	NSMutableDictionary *params = [NSMutableDictionary dictionaryWithObjectsAndKeys:_appId, @"api_key", nil];
	
	if( message )
		[params setObject:message forKey:@"message"];
	
	if( link )
		[params setObject:link forKey:@"link"];
	
	if( linkName )
		[params setObject:linkName forKey:@"name"];
	
	if( linkToImage )
		[params setObject:linkToImage forKey:@"picture"];
	
	if( caption )
		[params setObject:caption forKey:@"caption"];
	
	[_facebook dialog:@"stream.publish" andParams:params andDelegate:self];
	
}


- (void)getFriends
{
	[_facebook requestWithGraphPath:@"me/friends" andDelegate:self];
}


- (void)requestWithGraphPath:(NSString*)path httpMethod:(NSString*)method params:(NSMutableDictionary*)params
{
	[_facebook requestWithGraphPath:path andParams:params andHttpMethod:method andDelegate:self];
}


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark FBSessionDelegate

// Called when the dialog successful log in the user
- (void)fbDidLogin
{
	UnityPause( false );
	UnitySendMessage( "SocialNetworkingManager", "facebookLoginSucceeded", "" );
}


// Called when the user dismiss the dialog without login
- (void)fbDidNotLogin:(BOOL)cancelled
{
	UnityPause( false );
	UnitySendMessage( "SocialNetworkingManager", "facebookLoginDidFail", "" );
}


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark FBRequestDelegate

// Called when an error prevents the request from completing successfully.
- (void)request:(FBRequest*)request didFailWithError:(NSError*)error
{
	// figure out what kind of post we are dealing with
	if( [request.url hasSuffix:@"me/friends"] ) // friend request
	{
		UnitySendMessage( "SocialNetworkingManager", "facebookFriendRequestDidFail", [[error localizedDescription] UTF8String] );
	}
	else if( [request.url hasSuffix:@"me"] ) // username request: special case can be both custom and username
	{
		UnitySendMessage( "SocialNetworkingManager", "facebookUsernameRequestDidFail", [[error localizedDescription] UTF8String] );
		UnitySendMessage( "SocialNetworkingManager", "facebookCustomRequestDidFail", [[error localizedDescription] UTF8String] );
	}
	else if( [request.url hasSuffix:@"me/feed"] ) // post to my wall request
	{
		UnitySendMessage( "SocialNetworkingManager", "facebookPostDidFail", [[error localizedDescription] UTF8String] );
	}
	else // custom request
	{
		UnitySendMessage( "SocialNetworkingManager", "facebookCustomRequestDidFail", [[error localizedDescription] UTF8String] );
	}
}


/**
 * Called when a request returns and its response has been parsed into an object.
 *
 * The resulting object may be a dictionary, an array, a string, or a number, depending
 * on thee format of the API response.
 */
- (void)request:(FBRequest*)request didLoad:(id)result
{
	// figure out what kind of post we are dealing with
	if( [request.url hasSuffix:@"me/friends"] ) // friend request
	{
		NSString *json = [[[NSString alloc] initWithData:request.responseText encoding:NSUTF8StringEncoding] autorelease];
		UnitySendMessage( "SocialNetworkingManager", "facebookDidReceiveFriends", [json UTF8String] );
	}
	else if( [request.url hasSuffix:@"me"] ) // username request: special case can be both custom and username
	{
		NSString *name = [result objectForKey:@"name"];
		UnitySendMessage( "SocialNetworkingManager", "facebookDidReceiveUsername", [name UTF8String] );

		NSString *json = [[[NSString alloc] initWithData:request.responseText encoding:NSUTF8StringEncoding] autorelease];
		UnitySendMessage( "SocialNetworkingManager", "facebookDidReceiveCustomRequest", [json UTF8String] );
	}
	else if( [request.url hasSuffix:@"me/feed"] ) // post to my wall request
	{
		UnitySendMessage( "SocialNetworkingManager", "facebookPostSucceeded", "" );
	}
	else // custom request
	{
		NSString *json = [[[NSString alloc] initWithData:request.responseText encoding:NSUTF8StringEncoding] autorelease];
		UnitySendMessage( "SocialNetworkingManager", "facebookDidReceiveCustomRequest", [json UTF8String] );
	}
}


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark FBDialogDelegate

/**
 * Called when the dialog succeeds and is about to be dismissed.
 */
- (void)dialogDidComplete:(FBDialog*)dialog
{
	UnitySendMessage( "SocialNetworkingManager", "facebookDialogDidComplete", "" );
}


/**
 * Called when the dialog is cancelled and is about to be dismissed.
 */
- (void)dialogDidNotComplete:(FBDialog*)dialog
{
	UnitySendMessage( "SocialNetworkingManager", "facebookDialogDidNotComplete", "" );
}


/**
 * Called when dialog failed to load due to an error.
 */
- (void)dialog:(FBDialog*)dialog didFailWithError:(NSError*)error
{
	UnitySendMessage( "SocialNetworkingManager", "facebookDialogDidFailWithError", [[error localizedDescription] UTF8String] );
}


@end
