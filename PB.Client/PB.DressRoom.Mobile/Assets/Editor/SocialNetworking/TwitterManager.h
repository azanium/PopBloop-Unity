//
//  P31Twitter.h
//  SocialNetworking
//
//  Created by Mike on 9/11/10.
//  Copyright 2010 Prime31 Studios. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef enum {
	TwitterRequestLogin,
	TwitterRequestUpdateStatus,
	TwitterRequestHomeTimeline
} TwitterRequest;


@class OAToken;

@interface TwitterManager : NSObject
{
	NSString *_consumerKey;
	NSString *_consumerSecret;
@private
	NSMutableData *_payload;
	TwitterRequest _requestType;
}
@property (nonatomic, copy) NSString *consumerKey;
@property (nonatomic, copy) NSString *consumerSecret;
@property (nonatomic, retain) NSMutableData *payload;



+ (TwitterManager*)sharedManager;

- (BOOL)isLoggedIn;

- (NSString*)loggedInUsername;

- (void)xAuthLoginWithUsername:(NSString*)username password:(NSString*)password;

- (void)logout;

- (void)postStatusUpdate:(NSString*)status;

- (void)postStatusUpdate:(NSString*)status withToken:(OAToken*)token;

- (void)getHomeTimeline;

@end
