<?php

	$allowed_ip = array('202.154.40.235', '124.66.160.25', '118.136.216.37', '::1', '127.0.0.1');
	$ip = $_SERVER['REMOTE_ADDR'];

	/*
	q: module/role/function/func_arg1/func_arg2/func_arg3...
	*/
	ob_start();
	session_start();

	
	// apakah chat bar di bottom.php akan ditampilkan?
	$_SESSION['showchat'] = false;
	
	// production
	// ini_set('display_errors', 0);

	$basepath = "//" . $_SERVER['SERVER_NAME'] . "/";	// jangan lupa / di akhir
//	$basepath = "http://" . $_SERVER['SERVER_NAME'] . "/";	// jangan lupa / di akhir
//	$basepath = "http://" . $_SERVER['SERVER_NAME'] . "/popbloop/";	// jangan lupa / di akhir
//	$basepath = "http://" . $_SERVER['SERVER_NAME'] . "/lilo.beta/";	// jangan lupa / di akhir
	if(!in_array($ip, $allowed_ip)){
//		header('Location: ' . $basepath . 'oops.php');
//		exit;
	}

	$_SESSION['basepath'] = $basepath;

	$basefile = realpath('index.php');
	$basedir = dirname($basefile);
	$_SESSION['basedir'] = $basedir;

	$_SESSION['element_dir'] = $basepath . "bundles/characters/";	// jangan lupa / di akhir
	$_SESSION['material_dir'] = $basepath . "bundles/materials/";	// jangan lupa / di akhir
	$_SESSION['preview_dir'] = $basepath . "bundles/preview_images/";	// jangan lupa / di akhir
	$_SESSION['animation_dir'] = $basepath . "bundles/animations/";	// jangan lupa / di akhir
	$_SESSION['animation_preview_dir'] = $basepath . "bundles/animations/preview/";	// jangan lupa / di akhir

	// relative path, untuk kepentingan upload admin
	$_SESSION['element_rel_dir'] = "bundles/characters/";	// jangan lupa / di akhir
	$_SESSION['material_rel_dir'] = "bundles/materials/";	// jangan lupa / di akhir
	$_SESSION['preview_rel_dir'] = "bundles/preview_images/";	// jangan lupa / di akhir
	$_SESSION['animation_rel_dir'] = "bundles/animations/";	// jangan lupa / di akhir
	$_SESSION['animation_preview_rel_dir'] = "bundles/animations/preview/";	// jangan lupa / di akhir

	include_once('config/modules.php');
	include_once('libraries/baselibs.php');
	include_once('libraries/Permission.php');
	include_once('libraries/UserAgent.php');
	include_once('libraries/Alias.php');
	include_once('libraries/FacebookPB.php');
	include_once('config/themes.php');
	include_once('config/connection.php');
	include_once('config/security.php');
	include_once('config/mail.php');

	include_once('libraries/fb/facebook.php');

	
	
	$_SESSION['config'] = $config;
	
	// die("<pre>" . print_r($_REQUEST, true) . "</pre>");
	
	
	
//	$_REQUEST['q'] = alias($_REQUEST['q']);
	$_REQUEST['q'] = replace_alias($_REQUEST['q']);

write_log(array('log_text' => time() . ": q: " . print_r($_REQUEST['q'], true)));	
	
	// skip semua fungsi heartbeat
	$skipped_q = array('message/user/status', 'article/guest/getslides', 'message/user/loadmessages', 'LoadJS.ashx');
	if(!in_array($_REQUEST['q'], $skipped_q)){
		fb_check();
	}
	
	
	visitor_count();

//	print_r($_REQUEST);exit;
	
	
	// TODO: THEME!!!

	$module = strtolower(trim(arg(0)));
	if(strlen($module) == 0){
		$module = 'ui';
	}
	
	$role = strtolower(trim(arg(1)));
	if(strlen($role) == 0){
		$role = 'guest';
//		$role = 'user';
	}
	
	$_SESSION['url_role'] = $role;
	
	// sementara, utk admin gunakan theme lama
	if($role == 'admin'){
		define("LILO_THEME_ACTIVE", '');
	}
	
	// detect user_agent
//	$mobile = ua_is_mobile();	
//	$_SESSION['lilo_mobile'] = $mobile;
//
//	$browser = get_browser(null, true);
//
//	$js_enabled = $browser['javascript'];
//
//	if($mobile){
//		die("JS: <pre>" . print_r($browser, true) . "</pre>");
//	} else {
//		die("JS: <pre>" . print_r($browser, true) . "</pre>");
//	}
	
//	$browser = get_browser(null, true);
//	write_log(array('log_text' => print_r($_SERVER, true)));	//	write_log($args){	// filename, log_text
	
	// page yg bisa diakses non registered user?
	// sementara di-disabled dulu
	if(!permission($role, $_SESSION['user_id'])){
		$secret_key = $_REQUEST['secret_key'];
		if($secret_key == $config[$role . '_secret_key']){
			
		} else if($module != 'quest'){
			// return value berdasar $_SERVER['HTTP_USER_AGENT']
			$_SESSION['pop_error_msg'][] = "Access Denied! <span style='display:none'>".$_REQUEST['q']."</span>";
			$html = "##ACCESSDENIED##	<html>
									<head>
										<title>LiloCity: Access Denied!</title>
									</head>
									<body style='background-color:#F00'>
										<noscript>
											<font face='Palatino Linotype, Book Antiqua, Palatino, serif' size='+2'>
												Access Denied!<br />
											</font>
											Enable JavaScript in your browser to access this site properly.
										</noscript>
										<script language='JavaScript'>
											alert('Access Denied');
											window.location.replace('" . $_SESSION['basepath'] . "');
										</script>
									</body></html>";
			die($html);
//			die("Anda tidak berhak...");
		}
	}
	
	// cegah user untuk login di lebih dari satu browser
	// berfungsi juga sebagai penerima sinyal heartbeat
	if(!check_session()){
		unset($_SESSION['session_id']);
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		$_SESSION['pop_error_msg'][] = "Your session expired!";
		header("Location: " . $basepath);
		exit;
	}

	// cegah user untuk upload file dengan format selain yg diijinkan (application/vnd.unity, image/png, image/jpeg, image/bmp )
	
	/*
.zip 	application/x-compressed
.zip 	application/x-zip-compressed
.zip 	application/zip
.zip 	multipart/x-zip
	*/
	//	http://www.webmaster-toolkit.com/mime-types.shtml
//	$mimetype_nofile = array('');
//	$mimetype_zip = array('application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'application/octet-stream', 'multipart/x-zip');
//	$mimetype_unity = array('application/vnd.unity');
//	$mimetype_image = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/bmp', 'image/gif', 'image/bmp', 'image/x-windows-bmp', );
//	$mimetype_audio = array('audio/ogg', 'audio/mpeg');
//	
//	
////	$allowed_mimetypes = array('application/vnd.unity', 'image/png', 'image/jpeg', 'image/bmp', 'image/gif', 'application/zip');
//	$allowed_mimetypes = array_merge($mimetype_nofile, $mimetype_zip, $mimetype_unity, $mimetype_image, $mimetype_audio);
//	
//	if(!permission('admin', $_SESSION['user_id'])){
//		$allowed_mimetypes = $mimetype_image;
//	}
//	
//	if(isset($_FILES)){
//		foreach($_FILES as $_file_){
//			if($_file_['type'] != '' && !in_array($_file_['type'], $allowed_mimetypes)){
////				die($_file_['type'] . " ga boleh diupload");
//				write_log(array('log_text' => "File upload attack attempt: username: " . $_SESSION['username'] . ", user_id: ". $_SESSION['user_id'] . ", " . date("Y-m-d H:i:s") . "\n\r" . print_r($_FILES, true)));	// filename, log_text
//				$_SESSION['pop_error_msg'][] = "File upload attack attempt!";
//				header("Location: $basepath");
//				exit;
//			}
////			die($_file_['type'] . " boleh dong diupload...");
//		}
//	}
//	

	
	$function = strtolower(trim(arg(2)));
	if(strlen($function) == 0){
		$function = 'default';
	}

	$module_alias = unserialize(LILO_MODULE_ALIAS);
	
	if(!is_dir(LILO_MODULE_DIR . '/' . $module_alias[$module]) || !isset($module_alias[$module])){
		/*header*/die("HTTP/1.0 404 Not Found"); exit;
	}
	
	$module_file = LILO_MODULE_DIR . '/' . $module_alias[$module] . '/' . $role . '.php';
	if(is_file($module_file)){
		include_once($module_file);
	} else {
		/*header*/die("HTTP/1.0 404 Not Found"); exit;
	}
	
	$func_to_exec = $module . "_" . $role . "_" . $function;
	if(!function_exists($func_to_exec)){
		/*header*/die("HTTP/1.0 404 Not Found"); exit;
	}

	print($func_to_exec());

?>
