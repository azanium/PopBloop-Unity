<?php

function fb_check(){
  

	// check fb start

	/*
	signed_request: 
	Array
	(
			[algorithm] => HMAC-SHA256
			[expires] => 1344492000
			[issued_at] => 1344485848
			[oauth_token] => AAAFBxR81IbUBACMTXSZBtudFAmNoffCxNi3fVpPIZBLolSSxvX3dGObQSVObnYx8l1fjWglPfr6ZAQfHNpaVslNioGJ3W4bObMZBww7LZCdXCGkZBS72wr
			[user] => Array
					(
							[country] => id
							[locale] => en_US
							[age] => Array
									(
											[min] => 21
									)
	
					)
	
			[user_id] => 100004158610028
	)
	
	jika user belum authorize app ini, maka variable $user_id dan $oauth_token tidak ada
	
	*/

	$config = array();
  $config['appId'] = '353789864649141';
  $config['secret'] = '9e066419bed7d9ff07f4475f26318aa8';
  $config['fileUpload'] = false; // optional

  $facebook = new Facebook($config);
	
	$signed_request = $facebook->getSignedRequest();
	// die("<pre>" . print_r($signed_request, true) . "</pre>");
write_log(array('log_text' => time() . ": signed_request: " . print_r($signed_request, true)));
	if(isset($signed_request)){
		// print("<pre>" . print_r($signed_request, true) . "</pre>");exit;
		
		// cek jika $signed_request ini utk registrasi
		if(isset($signed_request['registration'])){	// print("<pre>" . print_r($signed_request['registration'], true) . "</pre>");exit;
			// masukkan ke db
			$me = $facebook->api('/me');
			require_once('modules/001_user_management/guest.php');
			// fullname, username, password, email, avatarname, handphone, twitter, sex, birthday, location
			$reg_data = array('fullname' => $signed_request['registration']['name'],
												'username' => $signed_request['registration']['email'],
												'password' => $me['id'],
												'email' => $signed_request['registration']['email'],
												'avatarname' => $signed_request['registration']['name'] . time(),
												'handphone' => $signed_request['registration']['handphone'],	// blm ada
												'twitter' => $signed_request['registration']['twitter'],			// blm ada
												'sex' => $signed_request['registration']['gender'],
												'birthday' => $signed_request['registration']['birthday'],
												'location' => $signed_request['registration']['location']['name'],
												'via_fb'	=> 1,
												'fb_id'	=> $me['id'],
												'$automate_login' => 1);
			$registered = user_guest_add_user($reg_data);
			// die("Registered: $registered");
			if($registered == 'OK'){
				user_user_loginfb($me[id]/*$signed_request['registration']['email']*/);
				unset($_SESSION['signed_request']);
				unset($signed_request);
				header("Location: " . $basepath);
				exit;
			} else if($registered == 'ERROR - Email already used. Use another email.'){
				user_user_loginfb($me[id]/*$signed_request['registration']['email']*/);
				unset($_SESSION['signed_request']);
				unset($signed_request);
				//$html = "<script>
				//	alert('".$registered."');
				//	window.top.location = '".$basepath."';
				//</script>";
				//print($html); exit;
				header("Location: " . $basepath);
				exit;
			}
			
		}
write_log(array('log_text' => time() . ": 146 - signed_request: " . print_r($signed_request, true)));
		if(isset($signed_request['oauth_token'])){
			$_SESSION['signed_request'] = $signed_request;
		}
		
		$app_authorized = isset($signed_request['user_id']) && isset($signed_request['oauth_token']);
write_log(array('log_text' => time() . ": app_authorized: " . print_r($app_authorized, true)));
		if(!isset($signed_request['user_id']) && !isset($signed_request['oauth_token']) && !isset($_SESSION['fb_id']) /*&& !isset($_SESSION['just_logout'])*/){	// Redirect the user to the OAuth Dialog
			// die("YOu are here...");
			$html = "<script>
				var oauth_url = 'https://www.facebook.com/dialog/oauth/';
				oauth_url += '?client_id=' + " . $config['appId'] . ";
				oauth_url += '&redirect_uri=' + encodeURIComponent('https://apps.facebook.com/popbloop/');
				oauth_url += '&scope=email,user_birthday,status_update,publish_stream,read_friendlists';	// user_photos,user_videos,
				// document.write(oauth_url);
				window.top.location = oauth_url;
				
			</script>";
			print($html); exit;
		}
		
		/*unset($_SESSION['just_logout']);*/
		
	}

	// operasi2 yg dilakukan bila user akses via FB
	// BELUM SELESAI
	if(isset($_SESSION['signed_request'])){
write_log(array('log_text' => time() . ": akses via FB: " . print_r($_SESSION['signed_request'], true)));
		
		$session = $facebook->getUser();
		$me = null;
		if($session){
			try{
				$me = $facebook->api('/me');
			} catch (Exception $e){}
		}
		
		// die("<pre>" . print_r($me, true) . "</pre>");
		
		// cek apakah user ini sudah terdaftar sbg player popbloop
		// jika belum, redirect ke facebook registration plugin
		
	}

	// operasi2 yg dilakukan bila user akses via WEB
	// belum selesai
	if(!isset($_SESSION['signed_request'])){
write_log(array('log_text' => time() . ": akses via WEB: " . print_r($_SESSION['signed_request'], true)));
		$user = $facebook->getUser();
		// die("user: <pre>" . print_r($user, true) . "</pre>");
		$me = null;
		
		if($user){
			try{
				$uid = $facebook->getUser();
				$me = $facebook->api('/me');
				// die("<pre>" . print_r($me, true) . "</pre>");
				// cek apakah sudah terdaftar di popbloop. terdaftar: $me ada di Users.account (id, email => fb_id, fb_email)
				// jika belum: redirect ke halaman signup
				// jika sudah: langsung panggil fungsi user_user_login (buat baru khusus utk user FB)
				require_once('modules/001_user_management/user.php');
				$fb_pb_connected = user_user_fb_pb_connected($me[id]);
				if($fb_pb_connected){
					// langsung login...
					// SAMPE SENEE...
					$logged = user_user_loginfb($me[id]);	// ternyata $me tidak selalu punya email. solusi: pake $me[id]	?
write_log(array('log_text' => time() . ": FB PB Connected: " . print_r($logged, true)));
					// die("logged: $logged");
				} else {
write_log(array('log_text' => time() . ": FB PB NOT Connected: " . print_r($fb_pb_connected, true)));
					header('Location: ' . $basepath . 'fb.signup.php');
					exit;
				}
				
			} catch (FacebookApiException $e){
				error_log($e);
				
				$me = null;
			}
		}

	$params = array('next' => $basepath);

		$logoutUrl = $facebook->getLogoutUrl($params);
		$loginUrl = $facebook->getLoginUrl();
		// die("logout: $logoutUrl, login: $loginUrl");
//		$_SESSION['fb_me'] = $me;
		$_SESSION['fb_loginUrl'] = $loginUrl;
		$_SESSION['fb_logoutUrl'] = $logoutUrl;
		
	}
	
	// check fb end

}
