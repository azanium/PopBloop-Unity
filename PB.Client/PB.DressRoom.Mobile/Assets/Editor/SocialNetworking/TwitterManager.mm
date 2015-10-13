//
//  P31Twitter.m
//  SocialNetworking
//
//  Created by Mike on 9/11/10.
//  Copyright 2010 Prime31 Studios. All rights reserved.
//

#import "TwitterManager.h"
#import "P31MutableOauthRequest.h"
#import "OARequestParameter.h"
#import "JSON.h"


void UnitySendMessage( const char * className, const char * methodName, const char * param );

NSString *const kLoggedInUser = @"kLoggedInUser";


@implementation TwitterManager

@synthesize consumerKey = _consumerKey, consumerSecret = _consumerSecret, payload = _payload;


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark NSObject

+ (TwitterManager*)sharedManager
{
	static TwitterManager *sharedSingleton;
	
	if( !sharedSingleton )
		sharedSingleton = [[TwitterManager alloc] init];
	
	return sharedSingleton;
}


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark Private

- (NSString*)extractUsernameFromHTTPBody:(NSString*)body
{
	if( !body )
		return nil;
	
	NSArray	*tuples = [body componentsSeparatedByString: @"&"];
	if( tuples.count < 1 )
		return nil;
	
	for( NSString *tuple in tuples )
	{
		NSArray *keyValueArray = [tuple componentsSeparatedByString: @"="];
		
		if( keyValueArray.count == 2 )
		{
			NSString *key = [keyValueArray objectAtIndex: 0];
			NSString *value = [keyValueArray objectAtIndex: 1];
			
			if( [key isEqualToString:@"screen_name"] )
				return value;
		}
	}
	
	return nil;
}


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark Public

- (BOOL)isLoggedIn
{
	NSString *tokenString = [[NSUserDefaults standardUserDefaults] objectForKey:kLoggedInUser];
	if( tokenString )
		return YES;
	return NO;
}


- (NSString*)loggedInUsername
{
	NSString *tokenString = [[NSUserDefaults standardUserDefaults] objectForKey:kLoggedInUser];
	if( !tokenString )
		return @"";
	return [self extractUsernameFromHTTPBody:tokenString];
}


- (void)xAuthLoginWithUsername:(NSString*)username password:(NSString*)password
{
	_requestType = TwitterRequestLogin;
	P31MutableOauthRequest *request = [[P31MutableOauthRequest alloc] initWithUrl:@"https://api.twitter.com/oauth/access_token"
																			  key:_consumerKey
																		   secret:_consumerSecret
																			token:nil];
	
	[request setHTTPMethod:@"POST"];


	[request setParameters:[NSArray arrayWithObjects:
							[OARequestParameter requestParameter:@"x_auth_mode" value:@"client_auth"],
							[OARequestParameter requestParameter:@"x_auth_username" value:username],
							[OARequestParameter requestParameter:@"x_auth_password" value:password],
							nil]];

	[request prepareRequest];
	
	NSURLConnection *connection = [[NSURLConnection alloc] initWithRequest:request delegate:self];	
	[request release];
	
    if( connection )
        _payload = [[NSMutableData alloc] init];
}


- (void)logout
{
	[[NSUserDefaults standardUserDefaults] setObject:nil forKey:kLoggedInUser];
	[[NSUserDefaults standardUserDefaults] synchronize];
}


- (void)postStatusUpdate:(NSString*)status
{
	NSString *tokenString = [[NSUserDefaults standardUserDefaults] objectForKey:kLoggedInUser];
	if( !tokenString )
	{
		UnitySendMessage( "SocialNetworkingManager", "twitterPostDidFail", "User is not logged in" );
		return;
	}
	
	OAToken *accessToken = [[OAToken alloc] initWithHTTPResponseBody:tokenString];
	[self postStatusUpdate:status withToken:accessToken];
}


- (void)postStatusUpdate:(NSString*)status withToken:(OAToken*)token
{
	_requestType = TwitterRequestUpdateStatus;
	P31MutableOauthRequest *request = [[P31MutableOauthRequest alloc] initWithUrl:@"http://api.twitter.com/1/statuses/update.json"
																			  key:_consumerKey
																		   secret:_consumerSecret
																			token:token];
	
	NSString *body = [NSString stringWithFormat:@"status=%@", [status encodedURLString]];
	[request setHTTPMethod:@"POST"];
	[request setHTTPBody:[body dataUsingEncoding:NSUTF8StringEncoding]];
	
	[request prepareRequest];
	
	NSURLConnection *connection = [[NSURLConnection alloc] initWithRequest:request delegate:self];	
	[request release];
	
    if( connection )
        _payload = [[NSMutableData alloc] init];
}


- (void)getHomeTimeline
{
	NSString *tokenString = [[NSUserDefaults standardUserDefaults] objectForKey:kLoggedInUser];
	if( !tokenString )
	{
		UnitySendMessage( "SocialNetworkingManager", "postFailed", "User is not logged in" );
		return;
	}
	
	OAToken *token = [[OAToken alloc] initWithHTTPResponseBody:tokenString];
	
	_requestType = TwitterRequestHomeTimeline;
	P31MutableOauthRequest *request = [[P31MutableOauthRequest alloc] initWithUrl:@"http://api.twitter.com/1/statuses/home_timeline.json"
																			  key:_consumerKey
																		   secret:_consumerSecret
																			token:token];
	
	[request setHTTPMethod:@"GET"];
	[request prepareRequest];
	
	NSURLConnection *connection = [[NSURLConnection alloc] initWithRequest:request delegate:self];	
	[request release];
	
    if( connection )
        _payload = [[NSMutableData alloc] init];
}


///////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark NSURLConnection Delegates

- (void)connection:(NSURLConnection*)conn didReceiveResponse:(NSURLResponse*)response
{
	[_payload setLength:0];
}


- (void)connection:(NSURLConnection*)conn didReceiveData:(NSData*)data
{
	[_payload appendData:data];
}


- (void)connectionDidFinishLoading:(NSURLConnection*)conn
{
	NSString *data = [[[NSString alloc] initWithData:_payload encoding:NSUTF8StringEncoding] autorelease];

	switch( _requestType )
	{
		case TwitterRequestLogin:
		{
			NSString *username = [self extractUsernameFromHTTPBody:data];
			if( !username )
			{
				UnitySendMessage( "SocialNetworkingManager", "twitterLoginDidFail", [data UTF8String] );
			}
			else
			{
				// save the token for posting
				[[NSUserDefaults standardUserDefaults] setObject:data forKey:kLoggedInUser];
				[[NSUserDefaults standardUserDefaults] synchronize];
				
				// send success message back to Unity
				UnitySendMessage( "SocialNetworkingManager", "twitterLoginSucceeded", "" );
			}

			break;
		}
		case TwitterRequestUpdateStatus:
		{
			// was this successful or not?
			if( [data rangeOfString:@"\"error\""].location != NSNotFound )
			{
				// try to extract a useful error message
				SBJSON *jsonParser = [[SBJSON new] autorelease];
				NSDictionary *dict = [jsonParser objectWithString:data];
				if( [dict isKindOfClass:[NSDictionary class]] && [[dict allKeys] containsObject:@"error"] )
				{
					NSString *error = [dict objectForKey:@"error"];
					UnitySendMessage( "SocialNetworkingManager", "twitterPostDidFail", [error UTF8String] );
				}
				else
				{
					UnitySendMessage( "SocialNetworkingManager", "twitterPostDidFail", [data UTF8String] );
				}
			}
			else
			{
				UnitySendMessage( "SocialNetworkingManager", "twitterPostSucceeded", "" );
			}

			break;
		}
		case TwitterRequestHomeTimeline:
		{
			// Return statuses to Unity
			UnitySendMessage( "SocialNetworkingManager", "twitterHomeTimelineDidFinish", [data UTF8String] );
			break;
		}
	}
	
	// clean up
	self.payload = nil;
	[conn release];
}


- (void)connection:(NSURLConnection*)conn didFailWithError:(NSError*)error
{
	if( _requestType == TwitterRequestLogin )
		UnitySendMessage( "SocialNetworkingManager", "twitterLoginDidFail", [[error localizedDescription] UTF8String] );
	else if( _requestType == TwitterRequestUpdateStatus )
		UnitySendMessage( "SocialNetworkingManager", "twitterPostDidFail", [[error localizedDescription] UTF8String] );
	else
		UnitySendMessage( "SocialNetworkingManager", "twitterHomeTimelineDidFail", [[error localizedDescription] UTF8String] );

	
	// clean up
	self.payload = nil;
}


@end
