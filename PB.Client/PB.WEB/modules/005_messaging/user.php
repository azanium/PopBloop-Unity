<?php

include_once('libraries/Template.php');

include_once('modules/001_user_management/user.php');
include_once('modules/004_friends/user.php');
include_once('modules/005_messaging/guest.php');

function message_user_default(){
	return "Halaman default modul shout";
}


function message_user_dmsend(){
	// mengirim direct message dari current user ke user lain
	$user_id = $_SESSION['user_id'];
	
	extract($_POST);	// friend_id, dm
	if(!isset($friend_id) || !isset($dm) || trim($friend_id) == '' || trim($dm) == ''){
		return "ERROR";
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Social');
  $lilo_mongo->selectCollection('DirectMessage');
	
	$dm_data = array('from' => $user_id, 'to' => $friend_id, 'datetime' => date("Y-m-d - H:i:s"), 'dm' => $dm, 'time' => time());
	
	$lilo_id = $lilo_mongo->insert($dm_data);
	$lilo_mongo->update($dm_data, array_merge($dm_data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
	
	return "OK";
	
}


/*
twitter-like
shout: defaultnya public, ada config utk jadi private (hanya bisa dilihat follower)

atau...

google-like
shout: bisa di-set group-of-friends (circle) mana yg bisa baca
*/
function message_user_shout(){
	// hanya menerima $_POST
	// variable yg diterima: 
	//	- session_id: diproses hanya jika session_id masih valid & aktif, meskipun nanti yg disimpan di db adalah user_id
	//	- circle: '', 'public', [circle_name_1], [circle_name_2]
	//	- shout_text = text status
	extract($_REQUEST);
	$session_id = $_SESSION['session_id'];

	$user_id = $_SESSION['user_id'];
	
	$shout = trim($shout);
	
	if($shout == ''){
		die("Your shout should not be empty.");
//		$_SESSION['pop_error_msg'][] = "ERROR - Shout should not be empty.";
//		header("Location: " . $_SESSION['basepath'] . "social");
//		exit;
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
	
	$shout_data = array('user_id' => $user_id, 'shout_time' => date("Y-m-d - H:i:s"), 'shout' => $shout, 'circle' => $circle, 'mentioned_friends' => $mentioned_friends, 'mentioned_friends_notified' => 0, 'time' => time(), 'ingame' => $ingame);
	// die(print_r($shout_data, true));
	// notify mentioned friends dengan CRON
	
	$lilo_id = $lilo_mongo->insert($shout_data);
	$lilo_mongo->update($shout_data, array_merge($shout_data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
	
	return "OK";
	
}


function message_user_home_messages(){
	return "Yooozzz";
}

function message_user_me_messages(){
	return "Yoooyyy";
}

function message_user_inbox_messages(){
	return "Yoooxxx";
}


function message_user_sessionvalidation($session_id = NULL){
	if(!isset($session_id) || trim($session_id) == ''){
		$session_id = arg(0);
	}
	
	if(!isset($session_id) || trim($session_id) == ''){
		return false;
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Users');
  $lilo_mongo->selectCollection('Session');

	$session_data = $lilo_mongo->findOne(array('session_id' => $session_id));
	return "<pre>" . print_r($session_data, true) . "<pre>";
	// cek apakah $session_id masih berlaku: Users.Session.time_end == '' || strtotime() - strtotime(Users.Session.time_end) < 3 * 60;
	
	
}

function message_user_inboxdialog(){
	return "Yadana yadana yadana dana...inboxdialog";
	
	// dapatkan semua list friend dari table 
	
}


function message_user_status(){
	// return print_r($_REQUEST, true);
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	
	// dapatkan semua bagian avatar berdasar tipe
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$template_file = "modules/005_messaging/templates_popbloopdark/message_user_status.php";
	
	$template->middle = $template->render($template_file);

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	
	return $return;

}

function message_user_shoutcomment(){
	// hanya menerima $_POST
	// variable yg diterima: 
	//	- session_id: diproses hanya jika session_id masih valid & aktif
	//	- shout_id = id shout yg di-comment
	//	- comment_text = text comment
	
}

function message_user_loadmessages(){
	// me-return 3 array: Home, Me, Inbox
	// - Home: status dari semua friends
	//		- dapatkan semua friendlist (friend_user_ws_friendlist)
	//		- 
//	$friendlist = friend_user_ws_friendlist();
//	return print_r($friendlist, true);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Friends');

	$user_id = $_SESSION['user_id'];

	$friends = $lilo_mongo->command_values(array("distinct" => "Friends", "key" => "friend_id", "query" => array('user_id' => $user_id)));
	
	$lilo_mongo->selectCollection('Shout');
	
	$array_parameter = array('user_id' => array('$in' => $friends));
	$limit = 100;
	$sort = array('time' => -1);
	
	$msg_home = array();

	$msg_home_cur = $lilo_mongo->find($array_parameter, $limit, $sort);
	
	while($curr = $msg_home_cur->getNext()){
		
		$user_properties = user_user_properties_by_uid($curr['user_id']);
		
		$msg_home[] = array("user_id" => $curr['user_id'],
												"shout_time" => $curr['shout_time'],
												"shout" => $curr['shout'],
												"circle" => $curr['circle'],
												"mentioned_friends" => $curr['mentioned_friends'],
												"mentioned_friends_notified" => $curr['mentioned_friends_notified'],
												"time" => $curr['time'],
												"lilo_id" => $curr['lilo_id'],
												
												"birthday" => $user_properties['birthday'],
												"fullname" => $user_properties['fullname'],
												"sender_id" => $user_properties['lilo_id'],
												"profile_picture" => $user_properties['profile_picture'],
												"sex" => $user_properties['sex'],
												"description" => $user_properties['description'],
												
												"time_word" => message_user_timetoword($curr['time'])
												
												);
	}

	// - Me: status gw sendiri + status orang yg mention gw
	$array_parameter = array('user_id' => $user_id);
	$limit = 100;
	$sort = array('time' => -1);
	
	$msg_me = array();

	$msg_me_cur = $lilo_mongo->find($array_parameter, $limit, $sort);
	
	while($curr = $msg_me_cur->getNext()){
		$user_properties = user_user_properties_by_uid($curr['user_id']);
		
		$msg_me[] = array("user_id" => $curr['user_id'],
											"shout_time" => $curr['shout_time'],
											"shout" => $curr['shout'],
											"circle" => $curr['circle'],
											"mentioned_friends" => $curr['mentioned_friends'],
											"mentioned_friends_notified" => $curr['mentioned_friends_notified'],
											"time" => $curr['time'],
											"lilo_id" => $curr['lilo_id'],
											
											"birthday" => $user_properties['birthday'],
											"fullname" => $user_properties['fullname'],
											"sender_id" => $user_properties['lilo_id'],
											"profile_picture" => $user_properties['profile_picture'],
											"sex" => $user_properties['sex'],
											"description" => $user_properties['description'],
											
											"time_word" => message_user_timetoword($curr['time'])
											
											);
	}

	// - Inbox: direct message, offline chat
	//		revisi: direct message hanya dari table DirectMessage, offline chat langsung masuk window chat saat user login
	$msg_inbox = array();
	
	$lilo_mongo->selectCollection('DirectMessage');

	$user_id = $_SESSION['user_id'];
	$array_parameter = array('to' => $user_id);
	$limit = 0;
	$sort = array('time' => -1);
	
	$msg_inbox_cur = $lilo_mongo->find($array_parameter, $limit, $sort);

	while($curr = $msg_inbox_cur->getNext()){
		$user_properties = user_user_properties_by_uid($curr['from']);
		
		$msg_inbox[] = array(	"from"	=> $curr['from'],
													"to"	=> $curr['to'],
													"datetime"	=> $curr['datetime'],
													"dm"	=> $curr['dm'],
													"time"	=> $curr['time'],
													"lilo_id"	=> $curr['lilo_id'],
													
													
													"birthday" => $user_properties['birthday'],
													"fullname" => $user_properties['fullname'],
													"sender_id" => $user_properties['lilo_id'],
													"profile_picture" => $user_properties['profile_picture'],
													"sex" => $user_properties['sex'],
													"description" => $user_properties['description'],
													
													"time_word" => message_user_timetoword($curr['time'])
											
											);
	}


	// all messages
	$all_messages = array('msg_home' => $msg_home,
												'msg_me' => $msg_me,
												'msg_inbox' => $msg_inbox);
	
	return json_encode($all_messages);
}

function message_user_loadusermessages($username = NULL){
	if(!isset($username)){
		$username = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$account = $lilo_mongo->findOne(array('username' => $username));
	
	$user_id = $account['lilo_id'];
	
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Shout');
	
	$array_parameter = array('user_id' => $user_id);
	$limit = 100;
	$sort = array('time' => -1);
	
	$msg_me = array();

	$msg_me_cur = $lilo_mongo->find($array_parameter, $limit, $sort);
	
	while($curr = $msg_me_cur->getNext()){
		$user_properties = user_user_properties_by_uid($curr['user_id']);
		
		$msg_me[] = array("user_id" => $curr['user_id'],
											"shout_time" => $curr['shout_time'],
											"shout" => $curr['shout'],
											"circle" => $curr['circle'],
											"mentioned_friends" => $curr['mentioned_friends'],
											"mentioned_friends_notified" => $curr['mentioned_friends_notified'],
											"time" => $curr['time'],
											"lilo_id" => $curr['lilo_id'],
											
											"birthday" => $user_properties['birthday'],
											"fullname" => $user_properties['fullname'],
											"sender_id" => $user_properties['lilo_id'],
											"profile_picture" => $user_properties['profile_picture'],
											"sex" => $user_properties['sex'],
											"description" => $user_properties['description'],
											
											"time_word" => message_user_timetoword($curr['time'])
											
											);
	}

	$all_messages = array('msg_me' => $msg_me);
	
	return json_encode($all_messages);
}

function message_user_timetoword($time = 0){
	$selisih = time() - $time;
	
	if($selisih < 60){
		return $selisih . " seconds ago";
	} else if($selisih < 3600){
		return floor($selisih / 60) . " minutes ago";
	} else if($selisih < (3600*24)){
		return floor($selisih / 3600) . " hours ago";
	} else if($selisih < (7*3600*24)){
		return floor($selisih / (3600*24)) . " days ago";
	} else {
		// tidak informatif bila yg ditampilkan adl x weeks ago, y months ago...
		// mending langsung tanggalnya saja
		return date("j F Y, H:i", $time);
	}
	
//	else if($selisih < (30*3600*24)){
//		return floor($selisih / (7*3600*24)) . " weeks ago";
//	} else if($selisih < (365*3600*24)){
//		return floor($selisih / (30*3600*24)) . " months ago";
//	} else {
//		return floor($selisih / (365*3600*24)) . " years ago";
//	}

}



// one can only delete his own shout
function message_user_shout_delete($lilo_id = NULL){
	if(!isset($lilo_id)){
		$lilo_id = func_arg(0);
	}
	
	$user_id = $_SESSION['user_id'];
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Shout');
	
	$criteria = array('lilo_id' => $lilo_id, 'user_id' => $user_id);
	//die(print_r($criteria, true));
	$lilo_mongo->remove($criteria);
	
	// delete all related comments
	
	print("1");
	exit;
}

// menerima variable post
// masuk ke table: Social.ShoutComment (lilo_id, shout_id, datetime, comment)
function message_user_shout_reply(){
	
/*
die(print_r($_REQUEST, true));

Array
(
    [q] => message/user/shout_reply
    [comment] => Reply to testsdfsdfsdfsdf
    [circle] => 
    [shout_id] => 4fa0897cc1b4ba341b000000
    [__utma] => 111872281.1470462402.1327380803.1336389708.1336418449.37
    [__utmz] => 111872281.1327380803.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)
    [MANTIS_STRING_COOKIE] => d51124df32701606e74f6e86a7da46f7bfb7835038e6ea4748643db7e4c2f0c0
    [POSTNUKESID] => d365816dbaef684d8522d130c239fc29
    [PHPSESSID] => ibi020pgc7hiesda9dmih8gip1
    [__utmc] => 111872281
    [__utmb] => 111872281.0.10.1336418449
)
*/
	extract($_REQUEST);
	if(!isset($shout_id)){
		die("Error");
	}
	
	if(trim($comment) == ''){
		die('Error: empty comment');
	}
	
	$user_id = $_SESSION['user_id'];
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('ShoutComment');
	
	$reply_data = array('shout_id' => $shout_id, 'datetime' => date("Y-m-d - H:i:s"), 'comment' => $comment, 'time' => time());
	
	$lilo_id = $lilo_mongo->insert($reply_data);
	$lilo_mongo->update($reply_data, array_merge($reply_data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
	
	// delete all related comments
	
	print("OK");
	exit;
	
	
}


?>