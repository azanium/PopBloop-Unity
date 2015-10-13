<?php
// simple aliasing
// more advanced functionalities should be made
// for example: profile/[username] can be redirected to user/user/profile/[username]

include_once('libraries/Permission.php');

function alias($shortUrl){
	$aliases = array(
		'logout'	=> array('guest'	=> 'ui/guest/default', 'user' => 'user/user/logout'),
		''	=> array('guest'	=> 'ui/guest/default', /*'user' => 'message/user/status'*/ 'user' => 'ui/user/avatar_editor_categorized' /*'ui/user/default'*/),
		'home'	=> array('guest'	=> 'ui/guest/default', 'user' => 'message/user/status' /*'ui/user/default'*/),
		
		'play'	=> array('guest'	=> 'article/guest/read/howto', 'user' => 'ui/user/play'),
		
//		'howtoplay' => array('guest'	=> 'article/guest/read/3', 'user'	=> 'article/guest/read/3'),
		'howtoplay' => array('guest'	=> 'article/guest/get/howtoplay', 'user'	=> 'article/guest/get/howtoplay'),
		
		'store'	=> array('guest' => 'ui/guest/default', 'user' => 'message/user/status'),
		'shop'	=> array('guest' => 'ui/guest/default', 'user' => ''),
		
		'myprofile'	=> array('guest' => 'ui/guest/default', 'user' => 'user/user/properties'),
		'myprofile-email'	=> array('user' => 'user/user/properties_email'),
		'myprofile-password'	=> array('user' => 'user/user/properties_password'),
		'myprofile-deactivate'	=> array('user' => 'user/user/properties_deactivate'),
		
		'myavatar'	=> array('guest' => 'ui/guest/default', 'user' => 'ui/user/avatar_editor_categorized'),

//		'social'	=> array('guest' => 'article/guest/read/4', 'user' => 'message/user/status'),
//		'howtosocial'	=> array('guest' => 'article/guest/read/4', 'user' => 'article/guest/read/4'),
		'social'	=> array('guest' => 'article/guest/get/howtosocial', 'user' => 'message/user/status'),
		'howtosocial'	=> array('guest' => 'article/guest/get/howtosocial', 'user' => 'article/guest/get/howtosocial'),
		
		'friends' => array('user' => 'friend/user/list'),
		
		'people' => array('user' => 'friend/user/people'),
		
//		'support' => array('guest' => 'article/guest/read/1', 'user' => 'article/guest/read/1'),
//		'faq' => array('guest' => 'article/guest/read/1', 'user' => 'article/guest/read/1'),
//		'support' => array('guest' => 'article/guest/get/faq', 'user' => 'article/guest/get/faq'),
//		'faq' => array('guest' => 'article/guest/get/faq', 'user' => 'article/guest/get/faq'),
		
//		article/guest/read/faq
		'support' => array('guest' => 'article/guest/read/faq', 'user' => 'article/guest/read/faq'),
		'faq' => array('guest' => 'article/guest/read/faq', 'user' => 'article/guest/read/faq'),
		'howto' => array('guest' => 'article/guest/read/howto', 'user' => 'article/guest/read/howto'),

		'toc' => array('guest' => 'article/guest/read/toc', 'user' => 'article/guest/read/toc'),

//		'troubleshooting' => array('guest' => 'article/guest/read/2', 'user' => 'article/guest/read/2'),
//		'troubleshooting' => array('guest' => 'article/guest/get/troubleshooting', 'user' => 'article/guest/get/troubleshooting'),
		'troubleshooting' => array('guest' => 'article/guest/read/troubleshooting', 'user' => 'article/guest/read/troubleshooting'),

		
//		'feature/player-of-the-month'	=> array('guest' => 'article/guest/read/4', 'user' => 'article/guest/read/4'),
//		'feature/featured-islands'	=> array('guest' => 'article/guest/read/5', 'user' => 'article/guest/read/5'), 
//		'feature/make-friends'	=> array('guest' => 'article/guest/read/6', 'user' => 'article/guest/read/6'), 
//		'feature/join-now'	=> array('guest' => 'article/guest/read/7', 'user' => 'article/guest/read/7'),
//		'feature/invite-friends'	=> array('guest' => 'article/guest/read/8', 'user' => 'article/guest/read/8'),
		
		'feature/player-of-the-month'	=> array('guest' => 'ui/guest/default', 'user' => 'ui/guest/default'),
		'feature/featured-islands'	=> array('guest' => 'ui/guest/default', 'user' => 'ui/guest/default'), 
		'feature/make-friends'	=> array('guest' => 'ui/guest/default', 'user' => 'ui/guest/default'), 
		'feature/join-now'	=> array('guest' => 'ui/guest/default', 'user' => 'ui/guest/default'),
		'feature/invite-friends'	=> array('guest' => 'ui/guest/default', 'user' => 'ui/guest/default'),
		
		'profile' => array('guest' => 'user/guest/property', 'user' => 'user/guest/property'),
		
		'whatis' => array('guest' => 'article/guest/page/whatis', 'user' => 'article/guest/page/whatis'),
		
		'microsite' => array('guest' => '', 'user' => 'ui/user/microsite'),
		
	);
	
	$roles = array('admin', 'editor', 'brand', 'user', 'guest');
	
		// permission($role, $_SESSION['user_id'])
	foreach($roles as $role){
		if(permission($role, $_SESSION['user_id'])){
			if(isset($aliases[$shortUrl][$role])){
				return $aliases[$shortUrl][$role];
			}
		}
	}

	// the shortUrl isn't on aliases index, maybe it's not an alias
	return $shortUrl;
}

/*
contoh: profile/mukhtar -> user/user/properties/mukhtar

aturan sementara: hanya terdiri dari 1 token [profile] dan 1 parameter [mukhtar]

*/
function replace_alias($shortUrl){
	$shortUrl_expl = explode('/', $shortUrl);
	$token = $shortUrl_expl[0];
	$parameter = $shortUrl_expl[1];
	
	if(alias($token) == $token){
		return alias($shortUrl);
	}
	
	return trim(alias($token) . '/' . $parameter, '/');
}
