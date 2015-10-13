//
//  FacebookManager.h
//  Facebook
//
//  Created by Mike on 9/13/10.
//  Copyright 2010 Prime31 Studios. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "FBConnect.h"


@interface FacebookManager : NSObject <FBSessionDelegate, FBRequestDelegate, FBDialogDelegate>
{
	Facebook *_facebook;
	NSString *_appId;
}
@property (nonatomic, copy) NSString *appId;


+ (FacebookManager*)sharedManager;


- (BOOL)isLoggedIn;

- (void)login;

- (void)logout;

- (void)getLoggedInUsername;

- (void)postMessage:(NSString*)message;

- (void)postMessage:(NSString*)message link:(NSString*)link linkName:(NSString*)linkName;

- (void)postMessage:(NSString*)message link:(NSString*)link linkName:(NSString*)linkName linkToImage:(NSString*)linkToImage caption:(NSString*)caption;

- (void)postPhoto:(NSString*)path caption:(NSString*)caption;

- (void)showPostMessageDialog;

- (void)showPostMessageDialogWithMessage:(NSString*)message;

- (void)postMessageDialog:(NSString*)message link:(NSString*)link linkName:(NSString*)linkName linkToImage:(NSString*)linkToImage caption:(NSString*)caption;

- (void)postPhoto:(NSString*)path caption:(NSString*)caption;

- (void)getFriends;

- (void)requestWithGraphPath:(NSString*)path httpMethod:(NSString*)method params:(NSMutableDictionary*)params;

@end
