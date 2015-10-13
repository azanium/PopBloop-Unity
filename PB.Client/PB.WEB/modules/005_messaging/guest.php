<?php
include_once('libraries/Template.php');

include_once('modules/001_user_management/user.php');

function message_guest_default(){
	return "Halaman default modul shout";
}

/*
twitter-like
shout: defaultnya public, ada config utk jadi private (hanya bisa dilihat follower)

atau...

google-like
shout: bisa di-set group-of-friends (circle) mana yg bisa baca
*/
function message_guest_shout($session_id = NULL, $shout = NULL, $circle = ''){
	// hanya menerima $_POST
	// variable yg diterima: 
	//	- session_id: diproses hanya jika session_id masih valid & aktif, meskipun nanti yg disimpan di db adalah user_id
	//	- circle: '', 'public', [circle_name_1], [circle_name_2]
	//	- shout = text status

	if(!isset($session_id) || trim($session_id) == ''){
		// dapatkan $session_id dari $_POST
		$session_id = $_POST['session_id'];
	}

	if(!isset($session_id) || trim($session_id) == ''){
		$session_id = $_SESSION['session_id'];
	}
	
	if(!isset($shout) || trim($shout) == ''){
		$shout = $_POST['shout'];
	}
	
	if(!isset($circle) || trim($circle) == ''){
		$circle = $_POST['circle'];
	}
	
	
	
	// cek apakah $session_id masih berlaku: Users.Session.time_end == '' || strtotime() - strtotime(Users.Session.time_end) < 3 * 60;
	$user_id = message_guest_sessionvalidation($session_id);
	
	if(!$user_id || trim($user_id) == ''){
		write_log(array('log_text' => "Invalid session_id: $session_id"));
		return "ERROR - Validation failed.";
	}
	
	$shout = trim($shout);
	
	if($shout == ''){
		return "ERROR - Shout should not be empty.";
	}
	
	$mentioned_friends = message_guest_mentionedfriends($shout);
	
	if(strlen($shout) > 500) {
		$shout = substr($shout, 0, 500);
	}
	
	$shout = htmlspecialchars($shout);
	$shout = message_guest_shoutlinktomentioneduser($shout);
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Social');
  $lilo_mongo->selectCollection('Shout');
	
	$shout_data = array('user_id' => $user_id, 'shout_time' => date("Y-m-d - H:i:s"), 'shout' => $shout, 'circle' => $circle, 'mentioned_friends' => $mentioned_friends, 'mentioned_friends_notified' => 0);
	
	// notify mentioned friends dengan CRON
	
	$lilo_id = $lilo_mongo->insert($shout_data);
	$lilo_mongo->update($shout_data, array_merge($shout_data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
	
	return "OK";
}

function message_guest_sessionvalidation($session_id = NULL){
	if(!isset($session_id) || trim($session_id) == ''){
		$session_id = func_arg(0);
	}
	
	if(!isset($session_id) || trim($session_id) == ''){
		return false;
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Users');
  $lilo_mongo->selectCollection('Session');

	$session_data = $lilo_mongo->findOne(array('session_id' => $session_id));
	// return "<pre>" . print_r($session_data, true) . "<pre>" . time();
	// cek apakah $session_id masih berlaku: Users.Session.time_end == '' || strtotime() - strtotime(Users.Session.time_end) < 3 * 60;
	if(!count($session_data)){
		return false;
	}
	
//	$valid_session = (trim($session_data['time_end']) == '') || (time() - strtotime($session_data['time_end']) < 3 * 60);
	$valid_session = (time() - strtotime($session_data['time_end'])) < (3 * 60);
	
	if(!$valid_session){
		return false;
	}
	
  $lilo_mongo->selectDB('Users');
  $lilo_mongo->selectCollection('Account');
	
	$account_data = $lilo_mongo->findOne(array('username' => $session_data['username']));
	// return "<pre>" . print_r($session_data, true) . "<pre>" . "<pre>" . print_r($account_data, true) . "<pre>" . time();
	if(!count($account_data)){
		return false;
	}
	
	return $account_data['lilo_id'];
}

function message_guest_mentionedfriends($shout){
	preg_match_all('/@([A-Za-z0-9_]+)/', $shout, $usernames);
//	return $usernames[1];
	$mentionedfriends = array();
	$allusernames = message_guest_allusernames();
	foreach($usernames[1] as $u){
		if(in_array($u, $allusernames)){
			$mentionedfriends[] = $u;
		}
	}
	
	return $mentionedfriends;
}

function message_guest_allusernames(){
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Users');
  $lilo_mongo->selectCollection('Account');
	
	$usernames = $lilo_mongo->command_values(array("distinct" => "Account", "key" => "username", "query" => array()));

	//return print_r($usernames, true);
	return $usernames;
}

function message_guest_testusernameregex($string = NULL){
	if(!isset($string)) $string = func_arg(0);
	if(!isset($string)) $string = 'RT @username: lorem ipsum @cjoudrey etc...';
	preg_match_all('/@([A-Za-z0-9_]+)/', $string, $usernames);
	print_r($usernames[1]);
}

function message_guest_shoutlinktomentioneduser($shout){
	return preg_replace_callback('/@([A-Za-z0-9_]+)/', 'addlink', $shout);
}

function addlink($matches){
	$basepath = $_SESSION['basepath'];
	$allusernames = message_guest_allusernames();
	if(in_array($matches[1], $allusernames)){
		return "<a href='".$basepath."profile/".$matches[1]."'>" . "@".$matches[1] . "</a>";
	} else {
		return "@".$matches[1];
	}
}


function message_guest_shoutcomment(){
	// hanya menerima $_POST
	// variable yg diterima: 
	//	- session_id: diproses hanya jika session_id masih valid & aktif
	//	- shout_id = id shout yg di-comment
	//	- comment_text = text comment
	
}

function message_guest_shouttest(){
	$basepath = $_SESSION['basepath'];

	$template = new Template();

	$logged_in = user_user_loggedin();
	$template->logged_in = $logged_in;
	
	// Deteksi User Agent

	$template->basepath = $basepath;

	$middle = $template->render("modules/005_messaging/templates/message_guest_shouttest.php");

	$template->middle = $middle;
	$template->username = $_SESSION['username'];

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;

	// komponen2 template lain
	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
}


?>