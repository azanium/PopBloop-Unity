<?php
/**
 * TODO: advance PHP authentication
 * list of function:
 *		- login_form:	generate form for login
 *		- 
 */
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/user.php');

function user_user_add_user($args = NULL){	// username, password, email
	if(isset($args) && is_array($args) && count($args) > 0){
		extract($args);
	} else {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];
	}
	
	// validasi semua input...
	if(strlen(trim($username)) < 4){
		return "ERROR - Use at least 4 character for username";
	}
	
	if(strlen(trim($password)) < 6){
		return "ERROR - Use at least 6 character for password";
	}
	
	// TODO: email validation
	if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
		return "ERROR - Invalid email";
	}
	
	$username_exists = user_user_redundancy_check('username', $username);
	if($username_exists){
		return "ERROR - Username $username already taken. Use another username.";
	}
	
	$email_exists = user_user_redundancy_check('email', $email);
	if($email_exists){
		return "ERROR - Email already used. Use another email.";
	}
	
	// masukkan ke DB
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$user_data = array('email' => $email, 'password' => $password, 'username' => $username);
	$lilo_id = $lilo_mongo->insert($user_data);
	$lilo_mongo->update($user_data, array_merge($user_data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
	
	
	$lilo_mongo->close();
	
	
	if(isset($_POST['automate_login']) && $_POST['automate_login'] == '1'){
		user_user_login($username, $password);
	}
	
	// semua OK? return 'OK'
	return "OK";
	
}

/**
 * return property dari user ini
 */
function user_user_property($args = NULL, $rettype = NULL){
		if(!user_user_loggedin()){
			return "0";
		}
		// write_log(array('log_text' => print_r($args, true)));
		$lilo_mongo = new LiloMongo();
		$lilo_mongo->selectDB('Users');
		$lilo_mongo->selectCollection('Account');
		
		$array_criteria = array('username' => $_SESSION['username']);

		if(isset($args)){
			$array_criteria = $args;
		}

		$myproperty = $lilo_mongo->findOne($array_criteria);
		unset($myproperty['password']);
		unset($myproperty['_id']);
		// write_log(array('log_text' => print_r($myproperty, true)));
		$lilo_mongo->selectCollection('Properties');
		
		$array_criteria = array('lilo_id' => $myproperty['lilo_id']);
		$myproperty2 = $lilo_mongo->findOne($array_criteria);
		
		if(trim($myproperty2['profile_picture']) == ''){
			$myproperty2['profile_picture'] = 'default.png';
		}
		
		unset($myproperty2['_id']);
		unset($myproperty2['lilo_id']);
		
		//	count_unread_msg
		//	jumlah pesan dengan 'receiver' = $user_id dan 'read' != '1'
		$lilo_mongo->selectDB('Social');
		$lilo_mongo->selectCollection('Messages');
		$count_unread_msg = $lilo_mongo->count(array('receiver' => $_SESSION['user_id'], 'read' => array('$not' => '1')));
		$myproperty2['count_unread_msg'] = intval($count_unread_msg);
		
		//	power
		$lilo_mongo->selectDB('Game');
		$lilo_mongo->selectCollection('Achievement');
		$power_array = $lilo_mongo->findOne(array('userid' => $_SESSION['user_id'], 'tipe' => 'energy'));
		
		$myproperty2['power'] = $power_array['value'];
		
		$myproperty = array_merge((array)$myproperty, (array)$myproperty2);
		// write_log(array('log_text' => print_r($myproperty, true)));

		if($rettype == 'array'){
			return $myproperty;
		}
		
		return json_encode($myproperty);
		
}

/**
 * return true bila current user sudah log-in
 */
function user_user_loggedin(){
	$session_id = $_SESSION['session_id'];
	$username = $_SESSION['username'];

	if(isset($session_id) && isset($username)){
		return "1";
	}
	
	return "0";
}


/**
 * return session_id jika user sudah login
 */
function user_user_sessionid(){
	$session_id = $_SESSION['session_id'];
	if(isset($session_id)){
		return $session_id;
	}
	
	return "0";
}

/**
 * return user_id jika user sudah login
 */
function user_user_userid(){
	$user_id = $_SESSION['user_id'];
	if(isset($user_id)){
		return $user_id;
	}
	
	return "0";
}



function user_user_login_form(){
	$basepath = $_SESSION['basepath'];

	$template = new Template();
	$template->users_array = $users_array;
	$template->basepath = $_SESSION['basepath'];

	$html = $template->render("modules/001_user_management/templates/user_user_login_form.php");
	return $html;

}

function user_user_fb_pb_connected($fb_id = NULL){
	// cek apakah $fb_id ada di Users.Account
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Account');
	
	$criteria = array('fb_id' => $fb_id);
	$exists = $lilo_mongo->findOne($criteria);
	
	if(is_array($exists) && count($exists)){
		return true;
	}
	
	return false;
}

function user_user_loginfb($fb_id){	// sama dengan user_user_login, tapi utk pengguna FB: password tidak ada / sama dengan email / sama dengan id
	include_once('libraries/fb/facebook.php');
	$config = array();
  $config['appId'] = '353789864649141';
  $config['secret'] = '9e066419bed7d9ff07f4475f26318aa8';
  $config['fileUpload'] = false; // optional

  $facebook = new Facebook($config);
	$me = $facebook->api('/me');
	// die("uname: $uname<br /><pre>" . print_r($me, true) . "</pre>");
write_log(array('log_text' => time() . ": user_user_loginfb: fb_id: " . print_r($fb_id, true)));
write_log(array('log_text' => time() . ": user_user_loginfb: " . print_r($me, true)));

// dapatkan uname dari db

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Account');
	$user_data = $lilo_mongo->findOne(array('fb_id' => $fb_id));

	$logged_in = user_user_login($user_data['username'], $me[id]);
	if($logged_in != '0'){
		$_SESSION['fb_id'] = $fb_id;
	}
	
	return $logged_in;
}

function user_user_login($uname = NULL, $passwd = NULL){
	// bisa diakses lewat web dgn path: /user/user/login/[username/password]
	// atau dipanggil langsung dgn fungsi user_user_login('username','password')
	$username = isset($uname) ? $uname : func_arg(0);
	$password = isset($passwd) ? $passwd : func_arg(1);
	if(strlen(trim($username)) == 0 || strlen(trim($password)) == 0){
		unset($_SESSION['session_id']);
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		return "0";
	}

	// connect to mongodb using default setting
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Account');
	$user_exists = $lilo_mongo->findOne(array('username' => $username, 'password' => md5($password)));

	if(!$user_exists || count($user_exists) == 0){
		$user_exists = $lilo_mongo->findOne(array('username' => $username, 'fb_password' => md5($password)));
	}
	
	$session_id = $_SESSION['session_id'];
	$session_username = $_SESSION['username'];
	
	// bagaimana jika $session_username berbeda dengan $username?
	
	$cur_date = date("Y-m-d H:i:s");
	if(is_array($user_exists) && count($user_exists)){
//		die("<pre>".print_r($user_exists, true)."</pre>");
		if(trim($session_id) == '' || trim($session_username) == ''){
			$user_id = (string)$user_exists['_id'] . '';
			// generate session_id
			$session_id = md5($cur_date . " - " . $username);
			$_SESSION['session_id'] = $session_id;
			$_SESSION['username'] = $username;
			$_SESSION['user_id'] = $user_id;
			
			// simpan di table session
			//		array(session_id, username, time_start, time_end)
			$lilo_mongo->selectCollection('Session');
			$lilo_mongo->insert(
				array(
					'session_id'	=> $session_id,
					'user_id'			=> $user_id,
					'username'		=> $username,
					'time_start'	=> $cur_date,
					'time_end'		=> ''
				)
			);
			
			return $session_id;
		} else {
			if($session_username == $username){
				// mencoba login, padahal sudah terdaftar...
				// biarkan saja, sepertinya orang ini ga ada kerjaan
				return $session_id;
			} else {
				// sudah terdaftar, kemudian mencoba login dengan username dan password yg lain
				// logout dulu, baru login dengan username yg baru

				//		update($array_criteria, $array_newobj, $array_options)
				$array_criteria = array('session_id' => $session_id);
				$array_newobj = array('$set' => array('time_end' => $cur_date));
				$array_options = array('upsert' => true);
				$lilo_mongo->selectCollection('Session');
				$lilo_mongo->update($array_criteria, $array_newobj, $array_options);
				
				$session_id = md5($cur_date . " - " . $username);
				$_SESSION['session_id'] = $session_id;
				$_SESSION['username'] = $username;

				// simpan di table session
				//		array(session_id, username, time_start, time_end)
				$lilo_mongo->selectCollection('Session');
				$lilo_mongo->insert(
					array(
						'session_id'	=> $session_id,
						'username'		=> $username,
						'time_start'	=> $cur_date,
						'time_end'		=> ''
					)
				);
				
				return $session_id;

			}
		}
	} else {
		unset($_SESSION['session_id']);
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		return "0";
	}
	
}

/**

	jika user tutup browser tanpa login, maka time_end yg tersimpan adl time last request
	
	saat user login, cek apakah ada data di table session utk user_id dia
	jika ada, pindahkan dulu datanya ke table SessionLog (user_id, session_id, time_start, time_end), baru kemudian data di Session dihapus
	
	table SessionLog ini yg digunakan utk menghitung achievement 'online_time'

 */
function user_user_logout(){
	// hapus session terkait login
	// mengeset field time_end di table Session
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	
	if(isset($_SESSION['session_id'])){
		$session_id = $_SESSION['session_id'];
		$lilo_mongo->selectCollection('Session');
	
		$array_criteria = array('session_id' => $session_id);
		
		// sementara hapus dulu data di Session
		// informasi tentang time_start dan time_end nantinya disimpan di table lain
		
		$session_data = $lilo_mongo->findOne($array_criteria);
		
		
//		$cur_date = date("Y-m-d H:i:s");
//		$array_newobj = array('$set' => array('time_end' => $cur_date));
//		$array_options = array('upsert' => true);
//	
//		$lilo_mongo->update($array_criteria, $array_newobj, $array_options);
		$array_criteria = array('user_id' => $_SESSION['user_id']);
		$lilo_mongo->delete($array_criteria);
		
		$lilo_mongo->selectCollection('SessionLog');
		$lilo_mongo->insert($session_data);
	}
	// die("<pre>" . print_r($_SESSION, true) . "<pre>");
	unset($_SESSION['session_id']);
	unset($_SESSION['username']);
	unset($_SESSION['user_id']);
	
	$redirect_to_fb_logout = 0;
	if(isset($_SESSION['fb_id'])){
		$redirect_to_fb_logout = 1;
	}
	
	unset($_SESSION['fb_me']);
	unset($_SESSION['fb_id']);
	unset($_SESSION['signed_request']);
	
	$basepath = $_SESSION['basepath'];
	
	$config = array();
  $config['appId'] = '353789864649141';
  $config['secret'] = '9e066419bed7d9ff07f4475f26318aa8';
  $config['fileUpload'] = false; // optional
  $facebook = new Facebook($config);
	$fb_logoutUrl = $facebook->getLogoutUrl();
	$facebook->destroySession();
	session_start();
	session_unset();
	session_destroy();
	session_write_close();
	setcookie(session_name(),'',0,'/');
	session_regenerate_id(true);
	
//	ob_start();
//	session_start();
	/*$_SESSION['just_logout'] = 1;*/
	
//	die("<pre>" . print_r($_SESSION, true) . "</pre>");
	if($redirect_to_fb_logout){
//		header("Location: " . $_SESSION['fb_logoutUrl']);
write_log(array('log_text' => time() . ": Redirect ke logouturl: " . $fb_logoutUrl));

		header("Location: " . $fb_logoutUrl);
//		header("Location: " . $_SESSION['fb_logoutUrl']);
		exit;
	}
	
//	header("Location: " . $_SESSION['basepath']);
	header("Location: " . $basepath);
	exit;

	$html = "<script>
		var oauth_url = '".$basepath."';
		window.top.location = oauth_url;
		
	</script>";
write_log(array('log_text' => time() . ": Redirect dgn html: " . $html ));
	print($html); exit;


//	return "1";
}

// return 1 jika redundan
function user_user_redundancy_check($check = NULL, $value = NULL){
	if(!isset($check)){
		$check = func_arg(0);
	}
	if(!isset($value)){
		$value = func_arg(1);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$criteria = array();
	switch($check){
		case 'email':
			$criteria = array('email' => $value);
			break;
		case 'username':
			$criteria = array('username' => $value);
			break;
	}
	
	$exists = $lilo_mongo->findOne($criteria);
	
	if($exists){
		return "1";
	}
	
	return "0";
}

function user_user_session_to_user_id($session_id = NULL){
	if(!isset($session_id)){
		$session_id = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Session');
	
	$array_criteria = array('session_id' => $session_id);
	$result_array = $lilo_mongo->findOne($array_criteria);
	
	return $result_array['user_id'];
}

// ajax? NO!!!
function user_user_properties(){
	// editor user properties untuk current user
	// untuk menambahkan field baru, langsung tambahkan saja di form di file template fungsi ini. gunakan nama sesuai table dimana field tsb akan disimpan...[account atau properties]
	$user_id = $_SESSION['user_id'];
	$username = $_SESSION['username'];
	
	if(!isset($user_id)){
		return false;
	}
	
	// dapatkan data current user dari table Users.Account dan Users.Properties
	// Users.Account: _id, email, password, username, lilo_id
	// Users.Properties: _id, user_id (Users.Account.lilo_id), fullname, picture_profile, ...
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	$account_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
//	return print_r($account_data, true);
	$lilo_mongo->selectCollection('Properties');
	$property_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
	
	// pecah $property_data['birthday'] menjadi dd, mm dan yy
	// separator bisa '/' bisa '-'
	
	if(strpos($property_data['birthday'], '-') !== false){
		$bd_expl = explode('-', $property_data['birthday']);
	} else if(strpos($property_data['birthday'], '/') !== false){
		$bd_expl = explode('/', $property_data['birthday']);
	}
	
	$property_data['birthday_dd'] = $bd_expl[0];
	$property_data['birthday_mm'] = $bd_expl[1];
	$property_data['birthday_yy'] = $bd_expl[2];
	
	$template = new Template();
	$template->username = $username;
	$template->user_id = $user_id;
	$template->account_data = $account_data;
	$template->property_data = $property_data;
	$template->basepath = $_SESSION['basepath'];

	$html = $template->render("modules/001_user_management/templates/user_user_properties.php");
	return ui_user_default(NULL, $html);
//	return $html;
}

// TODO: hapus variable2 yg tidak terpakai
function user_user_properties_email(){
	// editor user properties -> email setting untuk current user
	// untuk menambahkan field baru, langsung tambahkan saja di form di file template fungsi ini. gunakan nama sesuai table dimana field tsb akan disimpan...[account atau properties]
	$user_id = $_SESSION['user_id'];
	$username = $_SESSION['username'];
	
	if(!isset($user_id)){
		return false;
	}
	
	// dapatkan data current user dari table Users.Account dan Users.Properties
	// Users.Account: _id, email, password, username, lilo_id
	// Users.Properties: _id, user_id (Users.Account.lilo_id), fullname, picture_profile, ...
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	$account_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
//	return print_r($account_data, true);
	$lilo_mongo->selectCollection('Properties');
	$property_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
	
	$template = new Template();
	$template->username = $username;
	$template->user_id = $user_id;
	$template->account_data = $account_data;
	$template->property_data = $property_data;
	$template->basepath = $_SESSION['basepath'];

	$html = $template->render("modules/001_user_management/templates/user_user_properties_email.php");
	return ui_user_default(NULL, $html);
	
}


// TODO: hapus variable2 yg tidak terpakai
function user_user_properties_password(){
	// editor user properties -> email setting untuk current user
	// untuk menambahkan field baru, langsung tambahkan saja di form di file template fungsi ini. gunakan nama sesuai table dimana field tsb akan disimpan...[account atau properties]
	$user_id = $_SESSION['user_id'];
	$username = $_SESSION['username'];
	
	if(!isset($user_id)){
		return false;
	}
	
	// dapatkan data current user dari table Users.Account dan Users.Properties
	// Users.Account: _id, email, password, username, lilo_id
	// Users.Properties: _id, user_id (Users.Account.lilo_id), fullname, picture_profile, ...
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	$account_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
//	return print_r($account_data, true);
	$lilo_mongo->selectCollection('Properties');
	$property_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
	
	$template = new Template();
	$template->username = $username;
	$template->user_id = $user_id;
	$template->account_data = $account_data;
	$template->property_data = $property_data;
	$template->basepath = $_SESSION['basepath'];

	$html = $template->render("modules/001_user_management/templates/user_user_properties_password.php");
	return ui_user_default(NULL, $html);
	
}


// TODO: hapus variable2 yg tidak terpakai
function user_user_properties_deactivate(){
	// editor user properties -> email setting untuk current user
	// untuk menambahkan field baru, langsung tambahkan saja di form di file template fungsi ini. gunakan nama sesuai table dimana field tsb akan disimpan...[account atau properties]
	$user_id = $_SESSION['user_id'];
	$username = $_SESSION['username'];
	
	if(!isset($user_id)){
		return false;
	}
	
	// dapatkan data current user dari table Users.Account dan Users.Properties
	// Users.Account: _id, email, password, username, lilo_id
	// Users.Properties: _id, user_id (Users.Account.lilo_id), fullname, picture_profile, ...
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	$account_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
//	return print_r($account_data, true);
	$lilo_mongo->selectCollection('Properties');
	$property_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));
	
	$template = new Template();
	$template->username = $username;
	$template->user_id = $user_id;
	$template->account_data = $account_data;
	$template->property_data = $property_data;
	$template->basepath = $_SESSION['basepath'];

	$html = $template->render("modules/001_user_management/templates/user_user_properties_deactivate.php");
	return ui_user_default(NULL, $html);
	
}


function user_user_properties_image_upload(){
	require_once("libraries/js/valums-file-uploader/server/php.php");
	usleep(100000);
	//write_log(array('log_text' => print_r($_REQUEST, true) . "\n\r========\n\r" . print_r($_FILES, true) . "\n\r========\n\r"));	// filename, log_text
	$fileName;
	$fileSize;
	
	if (isset($_GET['qqfile'])){
		$fileName = $_GET['qqfile'];
	
		// xhr request
		$headers = apache_request_headers();
		$fileSize = (int)$headers['Content-Length'];
	} elseif (isset($_FILES['qqfile'])){
		$fileName = basename($_FILES['qqfile']['name']);
		$fileSize = $_FILES['qqfile']['size'];
	} else {
		die ('{error: "server-error file not passed"}');
	}
	
	if (count($_GET)){	
		array_merge($_GET, array('fileName'=>$fileName));
	
		$response = array_merge($_GET, array('success'=>true, 'fileName'=>$fileName));
	
		// to pass data through iframe you will need to encode all html tags		
		echo htmlspecialchars(json_encode($response), ENT_NOQUOTES);	
	} else {
		die ('{error: "server-error  query params not passed"}');
	}

	$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
	$file_dir = 'user_generated_data/profile_picture/';

	// max file size in bytes
	$sizeLimit = 10 * 1024 * 1024;
	
	$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	$result = $uploader->handleUpload($file_dir /*. $_GET['lilo_id']*/);

	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Properties');
	
	$properties = array('profile_picture' => $fileName);
	$user_id = $_SESSION['user_id'];

	$lilo_mongo->update_set(array('lilo_id' => $user_id), $properties, array('multiple' => false, 'upsert' => true, 'safe' => true));

	
	die('{"success":true}');
}

function user_user_get_profile_picture(){
	$user_id = $_SESSION['user_id'];

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Properties');

	$property = $lilo_mongo->findOne(array('lilo_id' => $user_id));
	return $property['profile_picture'];
}

function user_user_deactivate_account(){
	// data apa saja yang 'dipindahkan'?
	// Users.Account, Users.Properties, Users.GroupMember
	// Social.Friends (user_id dan friend_id)
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	
	$user_id = $_SESSION['user_id'];

	
	// 1. Users.Account
	$lilo_mongo->selectCollection('Account');
	$criteria = array('lilo_id' => $user_id);
	$backup_account = $lilo_mongo->findOne($criteria);
	$lilo_mongo->remove($criteria);
	
	// 2. Users.Properties
	$lilo_mongo->selectCollection('Properties');
	$criteria = array('lilo_id' => $user_id);
	$backup_properties = $lilo_mongo->findOne($criteria);
	$lilo_mongo->remove($criteria);
	
	// 3. Users.GroupMember
	$lilo_mongo->selectCollection('GroupMember');
	$criteria = array('user_id' => $user_id);
	$backup_groupmember = $lilo_mongo->findOne($criteria);
	$lilo_mongo->remove($criteria);
	

	$lilo_mongo->selectDB('Social');
	// 4. Social.Friends
	$lilo_mongo->selectCollection('Friends');
	$criteria = array('user_id' => $user_id);
	$backup_friends = $lilo_mongo->findOne($criteria);
	$lilo_mongo->remove($criteria);

	
	// masukkan ke table Users.DeletedUsers
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('DeletedUsers');
	$deletedusers = array('account' => $backup_account, 'properties' => $backup_properties, 'groupmember' => $backup_groupmember, 'friends' => $backup_friends);
	$lilo_mongo->insert($deletedusers);

	
	// Logout current user
	user_user_logout();
	header("Location: " . $_SESSION['basepath']);
	exit;
}


function user_user_properties_update2_email(){
	extract($_REQUEST);
	
	$user_id = $_SESSION['user_id'];

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Properties');
	
	// dapatkan password yg tersimpan di db
	$criteria = array('lilo_id' => $user_id);

	$account = array('email_notification_faved' => $email_notification_faved, 'email_notification_dm' => $email_notification_dm, 'email_notification_mention' => $email_notification_mention);
	
	$lilo_mongo->update_set(array('lilo_id' => $user_id), $account, array('multiple' => false, 'upsert' => true, 'safe' => true));

	$_SESSION['pop_status_msg'][] = "Your email settings updated successfully.";

	header("Location: " . $_SESSION['basepath'] . 'myprofile-email');
	exit;
}

function user_user_properties_update2_password(){
	extract($_REQUEST);	// current_password, new_password, new_password_confirm
	
	$user_id = $_SESSION['user_id'];

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Account');
	
	// dapatkan password yg tersimpan di db
	$criteria = array('lilo_id' => $user_id);
	$account = $lilo_mongo->findOne($criteria);
//	die("<pre>" . print_r($account, true) . "</pre>");

	if(md5($current_password) != $account['password']){
		$_SESSION['pop_error_msg'][] = "The password you entered is wrong.";
		header("Location: " . $_SESSION['basepath'] . 'myprofile-password');
		exit;
	}
	
	if($new_password != $new_password_confirm || trim($new_password == '')){
		$_SESSION['pop_error_msg'][] = "The password verification doesn't match.";
		header("Location: " . $_SESSION['basepath'] . 'myprofile-password');
		exit;
	}

	$account = array('password' => md5($new_password));
	
	$lilo_mongo->update_set(array('lilo_id' => $user_id), $account, array('multiple' => false, 'upsert' => true, 'safe' => true));

	$_SESSION['pop_status_msg'][] = "Your password updated successfully.";

	header("Location: " . $_SESSION['basepath'] . 'myprofile');
	exit;

}

function user_user_properties_update2(){
//	die("<pre>" . print_r($_REQUEST, true) . "</pre>");
	extract($_REQUEST);
	
	//if($account_x_password != confirm_password || $account_x_password == ''){
	//	unset($account_x_password);
	//	unset($_REQUEST['account_x_password']);
	//}
	
	$user_id = $_SESSION['user_id'];
	
	$account = array();
	$properties = array();
	
	foreach($_REQUEST as $key => $val){
		$processed_variable = strpos($key, '_x_');
		if($processed_variable !== false){
			$key_explode = explode('_x_', $key);
			${$key_explode[0]}[$key_explode[1]] = $val;
		}
		
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	// function update_set($array_criteria, $array_newobj, $array_options = array("multiple" => true)){
	// update table Users.Account dengan variable $account
	$lilo_mongo->selectCollection('Account');
	if(count($account)){
		
		if(isset($account['email'])){
			if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $account['email'])){
				$_SESSION['pop_error_msg'][] = "Email yang Anda masukkan tidak valid";
				header("Location: " . $_SESSION['basepath'] . 'myprofile');
				exit;
			}
		}
		
		$lilo_mongo->update_set(array('lilo_id' => $user_id), $account, array('multiple' => false, 'upsert' => true, 'safe' => true));
	}
	
	// update table Users.Properties dengan variable $properties
	$lilo_mongo->selectCollection('Properties');
	if(count($properties)){
		$properties['birthday'] = $properties['birthday_dd'] . "-" . $properties['birthday_mm'] . "-" . $properties['birthday_yy'];
		if(isset($properties['fullname']) && strlen($properties['fullname']) > 40){
			$properties['fullname'] = substr($properties['fullname'], 0, 40);
		}
		$lilo_mongo->update_set(array('lilo_id' => $user_id), $properties, array('multiple' => false, 'upsert' => true, 'safe' => true));
	}

	// tangani file upload
	// TODO: buat thumbnail ukuran kecil, sedang, besar & orig file
	//if(isset($_FILES['properties_x_profile_picture'])){
	//	$uploaddir = 'user_generated_data/profile_picture/';
	//	if(!is_dir($uploaddir)){
	//		mkdir($uploaddir);
	//	}
	//	
	//	$uploadfile = $uploaddir . basename($_FILES['properties_x_profile_picture']['name']);
	//	
	//	if (move_uploaded_file($_FILES['properties_x_profile_picture']['tmp_name'], $uploadfile)) {
	//		$profile_picture = $_FILES['properties_x_profile_picture']['name'];
	//		$lilo_mongo->update_set(array('lilo_id' => $user_id), array('profile_picture' => $profile_picture), array('multiple' => false, 'upsert' => true, 'safe' => true));
	//	}
	//}

	$_SESSION['pop_status_msg'][] = "Your profile updated successfully.";

//	return print_r($account, true);
//	header("Location: " . $_SESSION['basepath'] . 'user/user/properties');
	header("Location: " . $_SESSION['basepath'] . 'myprofile');
	exit;

}

function user_user_properties_update(){
//	return "<pre>" . print_r($_REQUEST, true) . "\n\n" . print_r($_FILES) . "</pre>";

/*
Array ( [properties_x_profile_picture] => Array ( [name] => 05df4031edecf51406421e912bda5bc5.png [type] => image/png [tmp_name] => C:\Development\Web\xampp\tmp\php13B9.tmp [error] => 0 [size] => 8192 ) )

Array
(
    [q] => user/user/properties_update
    [account_x_fullname] => Rully Wijoyo
    [__utma] => 111872281.217400160.1312219046.1313694564.1314331379.15
    [__utmz] => 111872281.1312219046.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)
    [PHPSESSID] => r54l10rb1hl9bttocatt7hke53
)


1

Array
(
    [q] => user/user/properties_update
    [properties_x_fullname] => Rully Wijoyo
    [account_x_email] => rully@m-stars.net
    [properties_x_birthday] => 23-02-2012
    [properties_x_sex] => male
    [__utma] => 111872281.1470462402.1327380803.1327380803.1327380803.1
    [__utmz] => 111872281.1327380803.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)
    [PHPSESSID] => omsh3r2onegu6ommo5t3ch21p3
)

*/
//	die("<pre>" . print_r($_REQUEST, true) . "</pre>");
	extract($_REQUEST);
	
	if($account_x_password != $confirm_password || $account_x_password == ''){
		unset($account_x_password);
		unset($_REQUEST['account_x_password']);
	}
	
	$user_id = $_SESSION['user_id'];
	
	$account = array();
	$properties = array();
	
	foreach($_REQUEST as $key => $val){
		$processed_variable = strpos($key, '_x_');
		if($processed_variable !== false){
			$key_explode = explode('_x_', $key);
			${$key_explode[0]}[$key_explode[1]] = $val;
		}
		
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	// function update_set($array_criteria, $array_newobj, $array_options = array("multiple" => true)){
	// update table Users.Account dengan variable $account
	$lilo_mongo->selectCollection('Account');
	if(count($account)){
		
		if(isset($account['email'])){
			if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $account['email'])){
				$_SESSION['pop_error_msg'][] = "Email yang Anda masukkan tidak valid";
				header("Location: " . $_SESSION['basepath'] . 'profile');
				exit;
			}
		}
		
		$lilo_mongo->update_set(array('lilo_id' => $user_id), $account, array('multiple' => false, 'upsert' => true, 'safe' => true));
	}
	
	// update table Users.Properties dengan variable $properties
	$lilo_mongo->selectCollection('Properties');
	if(count($properties)){
		if(isset($properties['fullname']) && strlen($properties['fullname']) > 40){
			$properties['fullname'] = substr($properties['fullname'], 0, 40);
		}
		$lilo_mongo->update_set(array('lilo_id' => $user_id), $properties, array('multiple' => false, 'upsert' => true, 'safe' => true));
	}

	// tangani file upload
	// TODO: buat thumbnail ukuran kecil, sedang, besar & orig file
	if(isset($_FILES['properties_x_profile_picture'])){
		$uploaddir = 'user_generated_data/profile_picture/';
		if(!is_dir($uploaddir)){
			mkdir($uploaddir);
		}
		
		$uploadfile = $uploaddir . basename($_FILES['properties_x_profile_picture']['name']);
		
		if (move_uploaded_file($_FILES['properties_x_profile_picture']['tmp_name'], $uploadfile)) {
			$profile_picture = $_FILES['properties_x_profile_picture']['name'];
			$lilo_mongo->update_set(array('lilo_id' => $user_id), array('profile_picture' => $profile_picture), array('multiple' => false, 'upsert' => true, 'safe' => true));
		}
	}

	$_SESSION['pop_status_msg'][] = "Your profile updated successfully.";

//	return print_r($account, true);
//	header("Location: " . $_SESSION['basepath'] . 'user/user/properties');
	header("Location: " . $_SESSION['basepath'] . 'myprofile');
	exit;
}

function user_user_add_lilo_id_to_users(){
	// loop semua row di table users
	// tambahkan field lilo_id di tiap row tersebut
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');

	$users_cursor = $lilo_mongo->find(array());
	
	while($curr = $users_cursor->getNext()){
		if(trim($curr['lilo_id']) == ''){
			$ret .= "<br />" . print_r($curr, true) . "<br />";
			$lilo_id = (string)$curr['_id'];
			$lilo_mongo->update($curr, array_merge($curr, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
		}
	}

	return "beres..." . $ret;
	
}

function user_user_properties_by_uid($user_id = NULL){
	if(!isset($user_id) || trim($user_id) == ''){
		return array();
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Properties');
	
	$criteria = array('lilo_id' => $user_id);
	
	$user_prop = $lilo_mongo->findOne($criteria);
	
	if(!isset($user_prop['profile_picture']) || trim($user_prop['profile_picture']) == ''){
		$user_prop['profile_picture'] = "default.png";
	}
	
	return $user_prop;
	
}


?>
