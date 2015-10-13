<?php
error_reporting(E_ERROR);
$allowed_ip = array(
	'202.154.40.235',
	'124.66.160.25',
	'118.136.216.37',
	'::1',
	'127.0.0.1'
);
$ip         = $_SERVER['REMOTE_ADDR'];
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
if (!in_array($ip, $allowed_ip)) {
	//		header('Location: ' . $basepath . 'oops.php');
	//		exit;
}
$_SESSION['basepath']                  = $basepath;
$basefile                              = realpath('index.php');
$basedir                               = dirname($basefile);

$_SESSION['basedir']                   = $basedir;
$_SESSION['element_dir']               = $basepath . "bundles/characters/"; // jangan lupa / di akhir
$_SESSION['material_dir']              = $basepath . "bundles/materials/"; // jangan lupa / di akhir
$_SESSION['preview_dir']               = $basepath . "bundles/preview_images/"; // jangan lupa / di akhir
$_SESSION['animation_dir']             = $basepath . "bundles/animations/"; // jangan lupa / di akhir
$_SESSION['animation_preview_dir']     = $basepath . "bundles/animations/preview/"; // jangan lupa / di akhir
// relative path, untuk kepentingan upload admin
$_SESSION['element_rel_dir']           = "bundles/characters/"; // jangan lupa / di akhir
$_SESSION['material_rel_dir']          = "bundles/materials/"; // jangan lupa / di akhir
$_SESSION['preview_rel_dir']           = "bundles/preview_images/"; // jangan lupa / di akhir
$_SESSION['animation_rel_dir']         = "bundles/animations/"; // jangan lupa / di akhir
$_SESSION['animation_preview_rel_dir'] = "bundles/animations/preview/"; // jangan lupa / di akhir

include_once('config/modules.php');
include_once('libraries/baselibs.php');
include_once('libraries/Permission.php');
include_once('libraries/UserAgent.php');
include_once('libraries/Alias.php');
include_once('config/themes.php');
include_once('config/connection.php');
include_once('config/security.php');
include_once('config/mail.php');
include_once('libraries/fb/facebook.php');
write_log(array(
	'log_text' => "\n\n=======================\n\nMULAI LOG\n\n=======================\n\nAll Request:\n" . print_r($_REQUEST, true) . "\n\n=====\n\n"
));
$_SESSION['config'] = $config;
$_REQUEST['q']      = replace_alias($_REQUEST['q']);
$skipped_q          = array(
	'article/guest/getslides',
	'message/user/loadmessages',
	'avatar/user/set_configuration',
	'avatar/user/avatar_category',
	'avatar/user/get_size',
	'avatar/user/get_gender',
	'avatar/user/get_configuration'
);

// $skipped_q untuk pengecekan facebook session

if (!in_array($_REQUEST['q'], $skipped_q)) {
	$config               = array();
	$config['appId']      = '353789864649141';
	$config['secret']     = '9e066419bed7d9ff07f4475f26318aa8';
	$config['fileUpload'] = false; // optional
	$facebook             = new Facebook($config);
	$signed_request       = $facebook->getSignedRequest(); // signed request ada bila: akses via fb canvas, baru logout, baru login dari fb, register via fb
	if (isset($signed_request)) {
		write_log(array(
			'log_text' => "SIGNED REQUEST: \n\n" . print_r($signed_request, true)
		));
		// cek jika diakses via page tab
		// http://developers.facebook.com/docs/appsonfacebook/pagetabs/
		if (isset($signed_request['page'])) {
			// $signed_request['page']['id'] dapat diketahui brand -> utk tentukan theme/level yg akan di-load
			$_SESSION['brand_page_id'] = $signed_request['page']['id'];
			// $signed_request['page']['liked'] : apakah page sudah di-like oleh user? jika belum => tampilkan banner utk like
			if (!$signed_request['page']['liked']) {
				// tandai bahwa user ini sebelumnya belum 'like' page di session
				// nanti jika $signed_request['page']['liked'] terdefinisi, tapi variable ini masih ada => tampilkan fb.after.like.jpg
				$_SESSION['before_like'] = 1;
				// string 'airbotol' nanti diganti dengan fb page id
				$html                    = "<div style='background:url(" . $basepath . "images/fb.pages/airbotol/fb.like.jpg) center top no-repeat; height:393px;'>&nbsp;</div>";
				die($html);
			} else if (trim($signed_request['page']['liked']) != '' && isset($_SESSION['before_like'])) {
				$html = '<div id="after_like_banner" style="text-align:center; width:817px; margin-left:auto; margin-right:auto;">';
				$html .= '<img id="page_liked" src="' . $basepath . 'images/fb.pages/airbotol/fb.after.like.jpg" usemap="#page_liked" border="0" width="817" height="393" alt="" />';
				$html .= '<map id="_page_liked" name="page_liked">';
				$html .= '<area id="hide_banner" shape="rect" coords="657,312,800,366" href="' . $basepath . '" alt="Enter Popbloop now" title="Enter Popbloop now"    />';
				$html .= '</map>';
				$html .= '</div>';
				$html_ .= '<script type="text/javascript">
										$(document).ready(function(){
											$(".withjs").hide();
											$("#hide_banner").live("click", function(){
												$("#after_like_banner").hide();
												$(".withjs").show();
												location.href="' . $basepath . '";
											});
										});
										</script>
										';
				$_SESSION['after_like_banner'] = $html;
			}
		}
		// cek jika $signed_request ini utk registrasi
		if (isset($signed_request['registration'])) { // print("<pre>" . print_r($signed_request['registration'], true) . "</pre>");exit;
			// masukkan ke db
			$me = $facebook->api('/me');
			require_once('modules/001_user_management/guest.php');
			// fullname, username, password, email, avatarname, handphone, twitter, sex, birthday, location
			$reg_data   = array(
				'fullname' => $signed_request['registration']['name'],
				'username' => $signed_request['registration']['email'],
				'password' => $me['id'],
				'email' => $signed_request['registration']['email'],
				'avatarname' => $signed_request['registration']['name'] . time(), // ngawur :)
				'handphone' => $signed_request['registration']['handphone'], // blm ada
				'twitter' => $signed_request['registration']['twitter'], // blm ada
				'sex' => $signed_request['registration']['gender'],
				'birthday' => $signed_request['registration']['birthday'],
				'location' => $signed_request['registration']['location']['name'],
				'via_fb' => 1,
				'fb_id' => $me['id'],
				'$automate_login' => 1
			);
			$registered = user_guest_add_user($reg_data);
			$via_fb_app = $signed_request['registration']['via_fb_app']; // digunakan untuk membedakan antara register lewat facebook app dan register lewat web
			if ($registered == 'OK') {
				user_user_loginfb($me[id] /*$signed_request['registration']['email']*/ );
				unset($_SESSION['signed_request']);
				unset($signed_request);
				if (isset($via_fb_app)) {
					header("Location: https://apps.facebook.com/popbloop");
					exit;
				}
				header("Location: " . $basepath);
				exit;
			} else if ($registered == 'ERROR - Email already used. Use another email.') {
				header("Location: " . $basepath);
				exit;
			}
		}
		if (isset($signed_request['oauth_token'])) {
			$_SESSION['signed_request'] = $signed_request;
		}
		$app_authorized = isset($signed_request['user_id']) && isset($signed_request['oauth_token']);
		if (!isset($signed_request['user_id']) && !isset($signed_request['oauth_token']) && !isset($_SESSION['fb_id']) /*&& !isset($_SESSION['just_logout'])*/ ) { // Redirect the user to the OAuth Dialog
			// die("YOu are here...");
			$redirect_uri = 'https://apps.facebook.com/popbloop/';
			if (isset($_SESSION['brand_page_id'])) {
				$redirect_uri = "https://www.facebook.com/pages/Airbotol/" . $_SESSION['brand_page_id'] . "?sk=app_353789864649141";
			}
			$html = "<script>
					var oauth_url = 'https://www.facebook.com/dialog/oauth/';
					oauth_url += '?client_id=' + " . $config['appId'] . ";
					oauth_url += '&redirect_uri=' + encodeURIComponent('" . $redirect_uri . "');
					oauth_url += '&scope=offline_access,email,user_birthday,status_update,publish_stream,read_friendlists';	// user_photos,user_videos,
					// document.write(oauth_url);
					window.top.location = oauth_url;
					
				</script>";
			print($html);
			exit;
		}
	}
	// CEK LAGI APAKAH BLOCK CODE INI BERGUNA / TIDAK
	// START
	// operasi2 yg dilakukan bila user akses via fb tab app
	// $_SESSION['in_fb_tab_app'] = $signed_request;
	write_log(array(
		'log_text' => "\nIN FB TAB APP:\n" . print_r($_SESSION['in_fb_tab_app'], true) . "\n"
	));
	if (isset($_SESSION['in_fb_tab_app']) && !isset($_SESSION['user_id'])) {
		$session = $facebook->getUser();
		$me      = null;
		if ($session) {
			write_log(array(
				'log_text' => "\nFB getUser terdefinisi:\n" . print_r($session, true) . "\n"
			));
			try {
				$me = $facebook->api('/me');
				// cek apakah current user sudah connected?
				require_once('modules/001_user_management/user.php');
				$fb_pb_connected = user_user_fb_pb_connected($me[id]);
				if ($fb_pb_connected) {
					$logged = user_user_loginfb($me[id]); // ternyata $me tidak selalu punya email. solusi: pake $me[id]	?
				} else {
					header('Location: ' . $basepath . 'fb.app.signup.php');
					exit;
				}
			}
			catch (Exception $e) {
			}
		} else {
			write_log(array(
				'log_text' => "\nFB getUser tidak terdefinisi:\n" . print_r($session, true) . "\n"
			));
		}
	}
	// CEK LAGI APAKAH BLOCK CODE INI BERGUNA / TIDAK
	// END
	// operasi2 yg dilakukan bila user akses via FB
	if (isset($_SESSION['signed_request'])) {
		$session = $facebook->getUser();
		$me      = null;
		if ($session) {
			try {
				$me = $facebook->api('/me');
				// cek apakah current user sudah connected?
				require_once('modules/001_user_management/user.php');
				$fb_pb_connected = user_user_fb_pb_connected($me[id]);
				if ($fb_pb_connected) {
					$logged = user_user_loginfb($me[id]); // ternyata $me tidak selalu punya email. solusi: pake $me[id]	?
				} else {
					header('Location: ' . $basepath . 'fb.app.signup.php');
					exit;
				}
			}
			catch (Exception $e) {
			}
		}
	}
	// operasi2 yg dilakukan bila user akses via WEB
	// belum selesai
	if (!isset($_SESSION['signed_request']) && !isset($_SESSION['user_id'])) {
		$facebook = new Facebook($config);
		$user     = $facebook->getUser();
		$me       = null;
		if ($user) {
			try {
				Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
				Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;
				// $uid = $facebook->getUser();
				$me                                          = $facebook->api('/me');
				// die("<pre>" . print_r($me, true) . "</pre>");
				// cek apakah sudah terdaftar di popbloop. terdaftar: $me ada di Users.account (id, email => fb_id, fb_email)
				// jika belum: redirect ke halaman signup
				// jika sudah: langsung panggil fungsi user_user_login (buat baru khusus utk user FB)
				require_once('modules/001_user_management/user.php');
				$fb_pb_connected = user_user_fb_pb_connected($me[id]);
				if ($fb_pb_connected) {
					// langsung login...
					$logged = user_user_loginfb($me[id]); // ternyata $me tidak selalu punya email. solusi: pake $me[id]	?
				} else {
					header('Location: ' . $basepath . 'fb.signup.php');
					exit;
				}
			}
			catch (FacebookApiException $e) {
				error_log($e);
				$me = null;
			}
		}
		$params                   = array(
			'next' => $basepath
		);
		$logoutUrl                = $facebook->getLogoutUrl($params);
		$loginUrl                 = $facebook->getLoginUrl();
		$_SESSION['fb_loginUrl']  = $loginUrl;
		$_SESSION['fb_logoutUrl'] = $logoutUrl;
	}
}
// check fb end
visitor_count();
//	print_r($_REQUEST);exit;
// TODO: THEME!!!

/* q=module/role/function 
module = folder
role = auth
function = method
*/

$module = strtolower(trim(arg(0)));
if (strlen($module) == 0) {
	$module = 'ui';
}
$role = strtolower(trim(arg(1)));
if (strlen($role) == 0) {
	$role = 'guest';
	//		$role = 'user';
}
$_SESSION['url_role'] = $role;
// sementara, utk admin gunakan theme lama
if ($role == 'admin') {
	define("LILO_THEME_ACTIVE", '');
}
if (!permission($role, $_SESSION['user_id'])) {
	$secret_key = $_REQUEST['secret_key'];
	if ($secret_key == $config[$role . '_secret_key'] && trim($secret_key) != '') {
	} else if ($module != 'quest') {
		// return value berdasar $_SERVER['HTTP_USER_AGENT']
		$_SESSION['pop_error_msg'][] = "Access Denied! <span style='display:none'>" . $_REQUEST['q'] . "</span>";
		$html                        = "##ACCESSDENIED##	<html>
									<head>
										<title>PopBloop: Access Denied!</title>
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
if (!check_session()) {
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
$mimetype_nofile   = array(
	''
);
$mimetype_zip      = array(
	'application/x-compressed',
	'application/x-zip-compressed',
	'application/zip',
	'application/octet-stream',
	'multipart/x-zip',
	'application/x-gzip'
);
$mimetype_unity    = array(
	'application/vnd.unity'
);
$mimetype_image    = array(
	'image/png',
	'image/jpeg',
	'image/pjpeg',
	'image/bmp',
	'image/gif',
	'image/bmp',
	'image/x-windows-bmp'
);
$mimetype_audio    = array(
	'audio/ogg',
	'audio/mpeg'
);
$allowed_mimetypes = array_merge($mimetype_nofile, $mimetype_zip, $mimetype_unity, $mimetype_image, $mimetype_audio);
if (!permission('admin', $_SESSION['user_id'])) {
	$allowed_mimetypes = $mimetype_image;
}
if (isset($_FILES)) {
	foreach ($_FILES as $_file_) {
		if ($_file_['type'] != '' && !in_array($_file_['type'], $allowed_mimetypes)) {
			write_log(array(
				'log_text' => "File upload attack attempt: username: " . $_SESSION['username'] . ", user_id: " . $_SESSION['user_id'] . ", tipe file: " . $_file_['type'] . ", " . date("Y-m-d H:i:s") . "\n\r" . print_r($_FILES, true)
			)); // filename, log_text
			$_SESSION['pop_error_msg'][] = "File upload attack attempt!";
			header("Location: $basepath");
			exit;
		}
	}
}
$function = strtolower(trim(arg(2)));
if (strlen($function) == 0) {
	$function = 'default';
}
$module_alias = unserialize(LILO_MODULE_ALIAS);
if (!is_dir(LILO_MODULE_DIR . '/' . $module_alias[$module]) || !isset($module_alias[$module])) {
	/*header*/
	die("HTTP/1.0 404 Not Found");
	exit;
}
$module_file = LILO_MODULE_DIR . '/' . $module_alias[$module] . '/' . $role . '.php';
if (is_file($module_file)) {
	include_once($module_file);
} else {
	/*header*/
	die("HTTP/1.0 404 Not Found");
	exit;
}
$func_to_exec = $module . "_" . $role . "_" . $function;
if (!function_exists($func_to_exec)) {
	/*header*/
	die("HTTP/1.0 404 Not Found");
	exit;
}
print($func_to_exec());
?>
