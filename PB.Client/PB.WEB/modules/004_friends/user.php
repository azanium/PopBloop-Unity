<?php
/**
 * Analisa
 
 * Circles: lilo_id, user_id, circle_array 
 * direvisi menjadi...
 * Circles: lilo_id, user_id, circle_name
 
 
 * FriendInvitation: lilo_id, inviter_user_id, invitee_user_id, circle_array, invite_time
 
 * Friends: lilo_id, user_id, friend_user_id, circle_array *, approving_time
 *		(for inviter, circle_array adalah FriendInvitation.circle_array)
 *		(for invitee, circle_array adalah circle_array yg diset saat approval)
 * Friends mempunyai 2 record (2 arah) untuk setiap connection
 *
 * Process: 
 *	0. user A search in all users data for user B
 *	1. user A invite user B as her friend. user A already set circle_array for user B
 *	2. user B notified of the invitation
 *	3. user B	approve the invitation => user B set circle_array for user A, process friend_user_add
 *						ignore the invitation => do nothing
 *
 */

// ALL FUNCTIONS WITH 'WS_' PREFIX SHOULD NOT RETURN ANY GUI, DATA ONLY! 
// What data format will we use? JSON?

include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/user.php');

// GUI: terdiri dari 2 tab
function friend_user_default_old(){	// ajax only
//	$ret .= "&bull;&nbsp;Search people to be added as friends<br />";
//	$ret .= "&bull;&nbsp;List of friends by groups/circles<br />";

	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	// Deteksi User Agent

	$template_file = "modules/004_friends/templates/friend_user_default.php";

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;

	$template->circles = (array)friend_user_ws_circle($_SESSION['user_id'], 'array');

	$return = $template->render($template_file);
	return $return;

}

function friend_user_people(){
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	$template->session_id = user_user_sessionid();

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

//	$return = $template->render($template_file);
//	return $return;

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

//	$template->middle = $template->render("modules/000_user_interface/templates/ui_user_play.php");
	$template->middle = $template->render("modules/004_friends/templates/friend_user_people.php");

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
	
}

function friend_user_list(){
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

	$template->circles = (array)friend_user_ws_circle($_SESSION['user_id'], 'array');


//	$template_file = "modules/005_messaging/templates_popbloopdark/message_user_status.php";
	$template_file = "modules/004_friends/templates/friend_user_list.php";
	
	$template->middle = $template->render($template_file);

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;

}

function friend_user_default(){
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

	$template->circles = (array)friend_user_ws_circle($_SESSION['user_id'], 'array');


//	$template_file = "modules/005_messaging/templates_popbloopdark/message_user_status.php";
	$template_file = "modules/004_friends/templates/friend_user_default.php";
	
	$template->middle = $template->render($template_file);

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
	
}


function friend_user_ws_search($keyword = NULL){
	if(!isset($keyword)){
		$keyword = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$search = new MongoRegex("/".$keyword."/i");
	$users = $lilo_mongo->find(array("username" => $search));

	while($user = $users->getNext()){
		if($user['lilo_id'] != $_SESSION['user_id']){
			unset($user['password']);
			$users_array[] = $user;
		}
		
	}


	for($idx = 0; $idx < count($users_array); $idx++){
		$lilo_mongo->selectDB('Social');
		$lilo_mongo->selectCollection('FriendInvitations');
		
		// cek apakah $user sudah ada di table Social.FriendInvitations, cek berdasar inviter_user_id, invitee_user_id
		$invitation_exists = $lilo_mongo->count(array('inviter_user_id' => $_SESSION['user_id'], 'invitee_user_id' => $users_array[$idx]['lilo_id']));
		$users_array[$idx]['invitation_exists'] = $invitation_exists;
		
		// cek apakah $user sudah meng-invite kita
		$invitation2_exists = $lilo_mongo->count(array('inviter_user_id' => $users_array[$idx]['lilo_id'], 'invitee_user_id' => $_SESSION['user_id']));
		$users_array[$idx]['invitation2_exists'] = $invitation2_exists;
		
		$lilo_mongo->selectDB('Users');
		$lilo_mongo->selectCollection('Properties');

		$property_array = $lilo_mongo->findOne(array('lilo_id' => $users_array[$idx]['lilo_id']));
		
		$property_array['profile_picture'] = trim($property_array['profile_picture']) != '' ? $property_array['profile_picture'] : 'default.png';
		
		$users_array[$idx] = array_merge((array)$users_array[$idx], (array)$property_array);
	}
	
//	return "<pre>" . print_r($users_array, true) . "</pre>";
	return json_encode($users_array);
}


function friend_user_addtocircle($friend_id = NULL, $circle_name = NULL){
	if(!isset($friend_id)){
		$friend_id = $_REQUEST['friend_id'];
	}
	if(!isset($circle_name)){
		$circle_name = $_REQUEST['circle_name'];
	}
	
	$user_id = $_SESSION['user_id'];
	
	// desain awal: perlu approval utk add friend
	// desain sekarang: langsung masuk table friend
	
	// die("Anda memasukkan friend $friend_id ke circle $circle_name");
	
	// cek apakah friend_id ini sudah ada di circle terpilih
	// jika belum, masukkan
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Friends');
	
	$criteria = array('user_id' => $user_id, 'friend_id' => $friend_id);
	$friend_data = $lilo_mongo->findOne($criteria);
	
	//die(print_r($friend_data, true));
	
	if(count($friend_data)){
		// die(print_r($friend_data['circle_array'], true));
		if(in_array($circle_name, $friend_data['circle_array'])){
			// die($circle_name . " ada di " . print_r($friend_data['circle_array'], true));
		} else {
			// die($circle_name . " ga ada di " . print_r($friend_data['circle_array'], true));
			$friend_data['circle_array'][] = $circle_name;
			// update table Social.Friends
			$lilo_mongo->update_set($criteria, array('circle_array' => $friend_data['circle_array']));
		}
	} else {
		// masukkan ke table Social.Friends
		$data = array('user_id' => $user_id, 'friend_id' => $friend_id, 'circle_array' => array($circle_name));
		$lilo_mongo->insert($data);
	}
	
	/*
	Array
	(
			[_id] => MongoId Object
					(
							[$id] => 4f682ba4c1b4ba3809000003
					)
	
			[approval_time] => 1332226980
			[circle_array] => Array
					(
					)
	
			[friend_id] => 4dfb2bb2c1b4bac41c000000
			[lilo_id] => 4f682ba4c1b4ba3809000003
			[user_id] => 4df6e7192cbfd4e6c000fd9b
	)
	*/
	// return: jumlah friend di circle ini
	
	$count_criteria = array('user_id' => $user_id, 'circle_array' => array('$all' => array($circle_name)));
	$count = $lilo_mongo->count($count_criteria);
	die(print_r($count, true));
	die("Count: $count");
	
}

function friend_user_circlemembercount($circle_name = NULL){
	if(!isset($circle_name)){
		$circle_name = $_REQUEST['circle_name'];
	}
	
	$user_id = $_SESSION['user_id'];

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Friends');
	
	$count_criteria = array('user_id' => $user_id, 'circle_array' => array('$all' => array($circle_name)));
	$count = $lilo_mongo->count($count_criteria);

	return $count;	
}


function friend_user_cleanfriends(){
	// menghapus semua Social.Friends yg sudah tidak ada di table Users.Account
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Friends');
	
	
	$friend_cursor = $lilo_mongo->find();
	while($friend = $friend_cursor->getNext()){
		
		// cek apakah $friend[friend_id]; ada di Users.Account
		
		$lilo_mongo->selectDB('Users');
		$lilo_mongo->selectCollection('Account');
		
		$criteria = array('lilo_id' => $friend['friend_id']);
		$user_data = $lilo_mongo->findOne($criteria);
		
		if(count($user_data) < 1){
			// hapus dari Social.Friends
			$lilo_mongo->selectDB('Social');
			$lilo_mongo->selectCollection('Friends');
			
			$criteria = array('friend_id' => $friend['friend_id']);
			$lilo_mongo->delete($criteria);
			
			print("<pre>");
			print_r($criteria);
			print("</pre><br />");

		}
		
		
	}
	
}

function friend_user_ws_circle($user_id = NULL, $return_type = NULL){
	// friend/user/ws_circle/
	// friend/user/ws_circle/read
	// friend/user/ws_circle/create/[xxx]
	// friend/user/ws_circle/update/[xxx]/[yyy]
	// friend/user/ws_circle/delete/[xxx]
	
	// CRUD for table Circles: lilo_id, user_id, circle_array
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}

	if(!isset($user_id)){
		return false;
	}
	
	$op = func_arg(0);	// op: create, read (default), update, delete

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Circles');

	// dapatkan dulu semua circle yg ada sekarang...
	$circles = $lilo_mongo->findOne(array('user_id' => $user_id));
	// format: array('lilo_id' => xxx, user_id => yyy, circle_array => array('friend', 'family', 'acquaintance'));

	switch(strtolower($op)){
		case 'create':	// add new circle. example, to create circle 'enemy': friend/user/ws_circle/create/enemy
										// revisi 25 may 2012: tambah variable circle_color => friend/user/ws_circle/create/enemy/rgb(77, 77, 77)
			$new_circle = func_arg(1);
			$new_circle_color = func_arg(2);
			// die("new_circle: $new_circle, new_circle_color: $new_circle_color");
			$circles['circle_array'] = array_merge((array)$circles['circle_array'], array($new_circle));
			
			if(!isset($circles['circle_color_array'])){
				for($idx = 0; $idx < count($circles['circle_array']) - 1; $idx++){
					$circles['circle_color_array'][] = 'rgb(126, 126, 126)';
				}
				$circles['circle_color_array'][] = $new_circle_color;
			} else {
				$circles['circle_color_array'] = array_merge((array)$circles['circle_color_array'], array($new_circle_color));
			}
			
			break;
		case 'update':	// change circle name. example, to change circle 'enemy' to 'bastard': friend/user/ws_circle/update/enemy/bastard
										// revisi 25 may 2012: tambah variable circle_color => friend/user/ws_circle/update/enemy/bastard/rgb(77, 77, 77)
			$old_circle_name = func_arg(1);
			$new_circle_name = func_arg(2);
			$new_circle_color = func_arg(3);
			// die("Data yg anda kirim: $old_circle_name, $new_circle_name, $new_circle_color");
			$old_circles = $circles['circle_array'];
			$old_circle_colors = $circles['circle_color_array'];
			$new_circles = array();
			$new_circle_colors = array();
			
			for($idx = 0; $idx < count($old_circles); $idx++){
				if(strtolower($old_circles[$idx]) != strtolower($old_circle_name)){
					$new_circles[] = $old_circles[$idx];
					$new_circle_colors[] = (isset($old_circle_colors[$idx])) ? $old_circle_colors[$idx] : 'rgb(126, 126, 126)';
				} else {
					$new_circles[] = $new_circle_name;
					$new_circle_colors[] = $new_circle_color;
				}
			}
			
			// update semua di table Social.Friends untuk user_id = $user_id
			
			$lilo_mongo->selectCollection('Friends');
			
			$update_criteria = array('user_id' => $user_id, 'circle_array' => array('$all' => array($old_circle_name)));
			// db.Friends.find({"user_id" : "4dfb2bb2c1b4bac41c000000", "circle_array" : {'$all' : ['Developer']}});
			$friend_cursor = $lilo_mongo->find($update_criteria);
			while($cur_friend = $friend_cursor->getNext()){
				// die(print_r($cur_friend, true));
				
				$cur_friend_array = array();
				// update $cur_friend[circle_array]
				for($idx = 0; $idx < count($cur_friend['circle_array']); $idx++){
					if(strtolower($cur_friend['circle_array'][$idx]) == strtolower($old_circle_name)){
						$cur_friend_array[] = $new_circle_name;
					} else {
						$cur_friend_array[] = $cur_friend['circle_array'][$idx];
					}
				}
				
				$criteria_ = array('user_id' => $user_id, 'friend_id' => $cur_friend['friend_id']);
				$lilo_mongo->update_set($criteria_, array('circle_array' => $cur_friend_array));
				
			}
			
			
			$circles['circle_array'] = $new_circles;
			$circles['circle_color_array'] = $new_circle_colors;
			break;
		case 'delete':
			$deleted_circle = func_arg(1);
			$old_circles = $circles['circle_array'];
			
			$new_circles = array();
			$new_circle_colors = array();
			
			for($idx = 0; $idx < count($old_circles); $idx++){
				//if(strtolower($old_circles[$idx]) == strtolower($deleted_circle)){
				//	unset($circles['circle_array'][$idx]);
				//	unset($circles['circle_color_array'][$idx]);
				//}
				if(strtolower($old_circles[$idx]) != strtolower($deleted_circle)){
					$new_circles[] = $circles['circle_array'][$idx];
					$new_circle_colors[] = $circles['circle_color_array'][$idx];
				}
			}
			
			
			$circles['circle_array'] = $new_circles;
			$circles['circle_color_array'] = $new_circle_colors;
			
			// die("Circles: " . print_r($circles, true));
			// TODO
			// hapus juga yg ada di Social.Friends
			//		dapatkan semua field circle_array, jika sama dengan $deleted_circle maka unset
			//		update set

			$lilo_mongo->selectCollection('Friends');
			
			$delete_criteria = array('user_id' => $user_id, 'circle_array' => array('$all' => array($deleted_circle)));
			// db.Friends.find({"user_id" : "4dfb2bb2c1b4bac41c000000", "circle_array" : {'$all' : ['Developer']}});
			$friend_cursor = $lilo_mongo->find($delete_criteria);
			while($cur_friend = $friend_cursor->getNext()){
				$cur_friend_array = array();
				// update $cur_friend[circle_array]
				for($idx = 0; $idx < count($cur_friend['circle_array']); $idx++){
					if(strtolower($cur_friend['circle_array'][$idx]) != strtolower($deleted_circle)){
						$cur_friend_array[] = $cur_friend['circle_array'][$idx];
					}
				}
				
				$criteria_ = array('user_id' => $user_id, 'friend_id' => $cur_friend['friend_id']);
				$lilo_mongo->update_set($criteria_, array('circle_array' => $cur_friend_array));
				
			}
			
			break;
	}
	
	$lilo_mongo->selectCollection('Circles');
	
	if(isset($op) && strtolower($op) != 'read'){
		// update_set($array_criteria, $array_newobj, $array_options = array("multiple" => true))
		$lilo_mongo->update_set(array('user_id' => $user_id), array('circle_array' => $circles['circle_array'], 'circle_color_array' => $circles['circle_color_array']), array("multiple" => true, 'upsert' => true));
	}
	
	if($return_type == 'array'){
		return $circles['circle_array'];
	}
	
	return json_encode($circles['circle_array']);
}

// digunakan di /people
// mengembalikan daftar circle dan jumlah membernya
// revisi 28 May 2012: + warna box-nya
function friend_user_ws_circlecount($user_id = NULL, $return_type = NULL){
	
	// CRUD for table Circles: lilo_id, user_id, circle_array
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}

	if(!isset($user_id)){
		return false;
	}
	
	$op = func_arg(0);	// op: create, read (default), update, delete

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Circles');

	$circles = $lilo_mongo->findOne(array('user_id' => $user_id));
	// format: array('lilo_id' => xxx, user_id => yyy, circle_array => array('friend', 'family', 'acquaintance'));

	$circle_count = array();
	for($idx = 0; $idx < count($circles['circle_array']); $idx++){
		$circle_count[$idx] = friend_user_circlemembercount($circles['circle_array'][$idx]);
	}
	
	if(!isset($circles['circle_color_array'])){
		foreach((array)$circles['circle_array'] as $cca){
			$circles['circle_color_array'][] = 'rgb(126, 126, 126)';
		}
	}
	
	$circles['circle_array'] = array_merge((array)$circles['circle_array'], (array)$circle_count, (array)$circles['circle_color_array']);
	
	if($return_type == 'array'){
		return $circles['circle_array'];
	}
	
	return json_encode($circles['circle_array']);
}

/**
 * Insert ke table FriendInvitation
 */
function friend_user_ws_invite($args = NULL){
	if(!isset($args)){
		$args = $_REQUEST;
	}
	
	// return print_r($_REQUEST, true);
	extract($args);

	if(!isset($inviter_user_id)){
		$inviter_user_id = $_SESSION['user_id'];
	}
	
	if(!isset($invitee_user_id)){
		return "0";
	}
	
//	$invitee_user_id_expl = explode("_", $invitee_user_id);

	// return "id yg anda undang: " . $invitee_user_id;

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');	// Social: Circles, Friends, FriendInvitations, PersonalMessages, Shout
	$lilo_mongo->selectCollection('FriendInvitations');	//	FriendInvitation: lilo_id, inviter_user_id, invitee_user_id, circle_array, invite_time

	$invitation_data = array('inviter_user_id' => $inviter_user_id, 
													 'invitee_user_id' => $invitee_user_id, 
													 'circle_array' => $circle_array, 
													 'invite_time' => time());

	$invitation_id = $lilo_mongo->insert($invitation_data);
	$lilo_mongo->update($invitation_data, array_merge($invitation_data, array('lilo_id' => (string)$invitation_id)), array("multiple" => false) );

	return "1";
}

function friend_user_ws_invitation_approval($args = NULL){
	if(!isset($args)){
		$args = $_REQUEST;
	}

	extract($args);	// $invitation_id, $circle_array
	// write_log(array('log_text' => print_r($args, true)));
	// masukkan ke tabel Friends
	// 2 record:
	//	1. inviter -> invitee: circle sesuai data di table FriendInvitations
	//	2. invitee -> inviter: circle sesuai $circle_array

	// dapatkan data dari FriendInvitations
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');	// Social: Circles, Friends, FriendInvitations, PersonalMessages, Shout
	$lilo_mongo->selectCollection('FriendInvitations');	//	FriendInvitation: lilo_id, inviter_user_id, invitee_user_id, circle_array, invite_time

	$invitation_detail = $lilo_mongo->findOne(array('lilo_id' => $invitation_id));
	// sampe seneee...

// return print_r($invitation_detail, true);
/*
Array
(
    [inviter_user_id] => 4e1df4c1c1b4bab417000000
    [invitee_user_id] => 4df6e7192cbfd4e6c000fd9b
    [circle_array] => Array
        (
            [0] => Acquaintance
        )

    [invite_time] => 1316761193
    [_id] => MongoId Object
        (
            [$id] => 4e7c2e69c1b4ba7409000002
        )

    [lilo_id] => 4e7c2e69c1b4ba7409000002
)
*/
	$lilo_mongo->selectCollection('Friends');	// Friends: user_id, friend_id, circle_array, approval_time
	
	$approval_time = time();
	// inviter -> invitee
	$inviter_to_invitee_data = array('user_id' => $invitation_detail['inviter_user_id'], 'friend_id' => $invitation_detail['invitee_user_id'], 'circle_array' => $invitation_detail['circle_array'], 'approval_time' => $approval_time);
	$friends_id = $lilo_mongo->insert($inviter_to_invitee_data);
	$lilo_mongo->update($inviter_to_invitee_data, array_merge($inviter_to_invitee_data, array('lilo_id' => (string)$friends_id)), array("multiple" => false) );

	// invitee -> inviter
	$invitee_to_inviter_data = array('user_id' => $invitation_detail['invitee_user_id'], 'friend_id' => $invitation_detail['inviter_user_id'], 'circle_array' => $circle_array, 'approval_time' => $approval_time);
	$friends_id = $lilo_mongo->insert($invitee_to_inviter_data);
	$lilo_mongo->update($invitee_to_inviter_data, array_merge($invitee_to_inviter_data, array('lilo_id' => (string)$friends_id)), array("multiple" => false) );

	// hapus dari table FriendInvitations
	$lilo_mongo->selectCollection('FriendInvitations');	//	FriendInvitation: lilo_id, inviter_user_id, invitee_user_id, circle_array, invite_time
	$lilo_mongo->remove(array('lilo_id' => $invitation_id));

	return '1';

}

// diproses setelah invitation_approval
function friend_user_add($args = NULL){	// args: user_id, friend_user_id, circle_array, approving_time
	if(!isset($args)){
		$args = $_REQUEST;
	}
	
	extract($args);
	
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}
	
	if(!isset($friend_user_id)){
		return "0";
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');	// Social: Circles, Friends, FriendInvitations, PersonalMessages, Shout
	$lilo_mongo->selectCollection('Friends');

	$approving_time = time();
	$friend_data_1 = array('user_id' => $user_id, 'friend_user_id' => $friend_user_id, 'circle_array' => $circle_array, 'approving_time' => $approving_time);
	$lilo_mongo->insert($friend_data_1);
	
	
}

// menampilkan daftar invitation ke current user. dari table Social.FriendInvitations
function friend_user_ws_friendrequest($user_id = NULL){
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}

	if(!isset($user_id)){
		return false;
	}

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');	// Social: Circles, Friends, FriendInvitations, PersonalMessages, Shout
	$lilo_mongo->selectCollection('FriendInvitations');

	$requests = $lilo_mongo->find(array('invitee_user_id' => $user_id));
	
	$requests_array = array();
	while($request = $requests->getNext()){
		$requests_array[] = $request;
	}
	
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Properties');
	for($idx = 0; $idx < count($requests_array); $idx++){
		// dari $requests_array[$idx]['inviter_user_id'], dapatkan detail user: fullname, profile_picture, sex
		$inviter_properties = $lilo_mongo->findOne(array('lilo_id' => $requests_array[$idx]['inviter_user_id']));
		$requests_array[$idx]['fullname'] = $inviter_properties['fullname'];
		$requests_array[$idx]['profile_picture'] = trim($inviter_properties['profile_picture']) != '' ? $inviter_properties['profile_picture'] : 'default.png';
		$requests_array[$idx]['sex'] = $inviter_properties['sex'];
		
		
		// dapatkan mutual friend
		// $mutual_friends = friend_user_ws_mutual($user_id, $requests_array[$idx]['inviter_user_id']);
		// $requests_array[$idx]['mutual_friends_number'] = (int)$mutual_friends[0];
		// $requests_array[$idx]['mutual_friends_array'] = (array)$mutual_friends[1];
		
		$requests_array[$idx]['mutual_friends_number'] = 3;
		
		
	}
	
	return json_encode($requests_array);
}

// fungsi untuk memperoleh jumlah dan daftar mutual friend
function friend_user_ws_mutual($user1 = NULL, $user2 = NULL){
	if(!isset($user1)){
		$f0 = func_arg(0);
		$user1 = trim($f0) != '' ? $f0 : $_SESSION['user_id'];
	}
	
	if(!isset($user2)){
		$f0 = func_arg(1);
		$user2 = trim($f0) != '' ? $f0 : $_SESSION['user_id'];
	}
	
	if($user1 == $user2){
		return array(0, array());
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Friends');
	
	// dapatkan daftar friend $user1
	$user1_friend_array = array();
	$array_criteria = array('user_id' => $user1, 'friend_id' => array('$ne' => $user2));
	$user1_friend_cursor = $lilo_mongo->find($array_criteria);
	
	// dapatkan daftar friend $user2
	
}

// list friend, group berdasar circle
function friend_user_ws_friendlist($keyword = NULL){
	if(!isset($keyword)){
		$keyword = func_arg(0);
	}
	
	// dapatkan circle array
	// ...
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Circles');

	$circle_data_ = $lilo_mongo->findOne(array('user_id' => $_SESSION['user_id']));
	
	$circle_data = $circle_data_['circle_array'];

	// dapatkan semua data di table Friends dengan user_id = $_SESSION['user_id']
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Friends');
	$friend_cursor = $lilo_mongo->find(array('user_id' => $_SESSION['user_id']));

	$friend_data = array();
	while($friend = $friend_cursor->getNext()){
		unset($friend['_id']);
		
		// tambahkan detail $friend dari table Users.Account dan Users.Properties
		// dari $friend['user_id']
		
		$friend_account_detail = friend_user_detailbyuserid($friend['friend_id']);
		$friend_account_detail = json_decode($friend_account_detail);
		$friend = array_merge((array)$friend, (array)$friend_account_detail);

		
		$friend_data[] = $friend;
	}
	
	$outer_circle_friend_data = $friend_data;
	
	for($idx = 0; $idx < count($circle_data); $idx++){
		$friend_list = array();
		foreach($friend_data as $friend){
			$circle_array = $friend['circle_array'];
			if(in_array($circle_data[$idx], $circle_array) && trim($friend['fullname']) != ''){
//				// tambahkan detail $friend dari table Users.Account dan Users.Properties
//				// dari $friend['user_id']
//				
//				$friend_account_detail = friend_user_detailbyuserid($friend['friend_id']);
//				$friend_account_detail = json_decode($friend_account_detail);
//				$friend = array_merge((array)$friend, (array)$friend_account_detail);
				
				$friend_list[] = $friend;
			}
		}
		$circle_data[$idx] = array_merge((array)$circle_data[$idx], (array)$friend_list);
	}
	
	// dapatkan semua friend yg tdk masuk circle manapun...
//	$outer_circle_friend_data = array_diff($outer_circle_friend_data, $friend_list);
	for($idx = 0; $idx < count($outer_circle_friend_data); $idx++){
		if(count($outer_circle_friend_data[$idx]['circle_array'])){
			unset($outer_circle_friend_data[$idx]);
		}
	}

//	write_log(array('filename' => NULL, 'log_text' => print_r($outer_circle_friend_data, true) . "\n\n" . print_r($friend_list, true)));

//	BUGGY, SEMENTARA DI-COMMENT DULU
//	$circle_data[] = array_merge(array('Outer Circle'), (array)$outer_circle_friend_data);

	unset($friend_list);
	unset($friend_data);
	unset($circle_data_);
	$lilo_mongo->close();

	// dapatkan property setiap friend !

//	return print_r($circle_data, true);
//	return print_r($circle_data, true) . '-------------' . print_r($friend_data, true);
	return json_encode($circle_data);
}

/*
function friend_user_ws_search($keyword = NULL){
	if(!isset($keyword)){
		$keyword = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$search = new MongoRegex("/".$keyword."/i");
	$users = $lilo_mongo->find(array("username" => $search));

	while($user = $users->getNext()){
		if($user['lilo_id'] != $_SESSION['user_id']){
			$users_array[] = $user;
		}
		
	}


	for($idx = 0; $idx < count($users_array); $idx++){
		$lilo_mongo->selectDB('Social');
		$lilo_mongo->selectCollection('FriendInvitations');
		// cek apakah $user sudah ada di table Social.FriendInvitations, cek berdasar inviter_user_id, invitee_user_id
		$invitation_exists = $lilo_mongo->count(array('inviter_user_id' => $_SESSION['user_id'], 'invitee_user_id' => $users_array[$idx]['lilo_id']));
		$users_array[$idx]['invitation_exists'] = $invitation_exists;
		
		$lilo_mongo->selectDB('Users');
		$lilo_mongo->selectCollection('Properties');

		$property_array = $lilo_mongo->findOne(array('lilo_id' => $users_array[$idx]['lilo_id']));
		
		$users_array[$idx] = array_merge((array)$users_array[$idx], (array)$property_array);
	}
	
//	return "<pre>" . print_r($users_array, true) . "</pre>";
	return json_encode($users_array);
}

*/

function friend_user_delete(){
	
}

function friend_user_ws_deletecircle($args = NULL){
	if(!isset($args)){
		$args = $_REQUEST;
	}

	extract($args);
	
	if(!isset($circle_name)){
		return false;
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Circles');
	
	$user_id = $_SESSION['user_id'];
	
	$current_circles = $lilo_mongo->findOne(array('user_id' => $user_id));

//	$old_circles = $current_circles;

	if(count($current_circles) && count($current_circles['circle_array'])){
		$current_circles['circle_array'] = array_diff($current_circles['circle_array'], array($circle_name));
	}

//	return print_r($current_circles, true);

//	$lilo_mongo->update(array('user_id' => $user_id), (array)$current_circles);

	$lilo_mongo->update_set(array('user_id' => $user_id), array('circle_array' => $current_circles['circle_array']));

	// update semua di table Friends dengan user_id = $user_id, update bagian circle_array...hilangkan circle dengan nama $circle_name
	$lilo_mongo->selectCollection('Friends');
	$friends = $lilo_mongo->find(array("user_id" => $user_id));

	$friend_array = array();

	while($friend = $friends->getNext()){
		$friend['circle_array'] = array_diff((array)$friend['circle_array'], array($circle_name));
		
		$friend_array[] = $friend;
	}

	for($idx = 0; $idx < count($friend_array); $idx++){
		// update table Friends
		$criteria_array = array('user_id' => $user_id, 'friend_id' => $friend_array[$idx]['friend_id']);
		// buggy, no more :)
		// write_log(array('log_text' => 'criteria_array : ' . print_r($criteria_array, true)));
		// write_log(array('log_text' => 'friend_array idx : ' . print_r($friend_array[$idx], true)));
		$lilo_mongo->update_set($criteria_array, array('circle_array' => $friend_array[$idx]['circle_array']));
	}	

	return "1";
}

function friend_user_ws_deletefromcircle($args = NULL){
	if(!isset($args)){
		$args = $_REQUEST;
	}
	
	extract($args);
	
	if(!isset($friend_id) || !isset($circle_name)){
		return 'Data yang Anda kirimkan tidak lengkap';
	}
	
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}
	
	// hapus $friend_id dari circle $circle_name
	// di table Friends untuk user_id = $_SESSION['user_id']
	
	// dapatkan dulu detail dari Friends
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection('Friends');
	
	// jika User sudah ada di 'Outer Circle', hapus dari table Friends
	if(trim($circle_name) == 'Outer Circle'){
		$lilo_mongo->remove(array('user_id' => $user_id, 'friend_id' => $friend_id));
		return "1";
	}
	
	
	$friend_data = $lilo_mongo->findOne(array('user_id' => $user_id, 'friend_id' => $friend_id));
	$old_friend_data = $friend_data;
/*
$friend_data = Array
(
    [user_id] => 4df6e7192cbfd4e6c000fd9b
    [friend_id] => 4e38df26c1b4ba8c09000001
    [circle_array] => Array
        (
            [0] => M-Stars
            [1] => MU FC
        )

    [approval_time] => 1317191174
    [_id] => MongoId Object
        (
            [$id] => 4e82be06c1b4ba6c20000000
        )

    [lilo_id] => 4e82be06c1b4ba6c20000000
)
*/
	$friend_data['circle_array'] = array_diff((array)$friend_data['circle_array'], array($circle_name));
	
	$lilo_mongo->update_set(array('user_id' => $user_id, 'friend_id' => $friend_id), array('circle_array' => $friend_data['circle_array']));
	
	return '1';
	
//	return "SEMUA DATA, circle: $circle_name, friend_id: $friend_id" . print_r($friend_data, true) . print_r($old_friend_data, true);
	
//	return "User ID Anda: $user_id, Data yang Anda kirim: " . $friend_id . ", " . $circle_name;
}

/**
 * return: array, detail user dari table Users.Account dan Users.Properties
 */
function friend_user_detailbyuserid($user_id = NULL){
	if(!isset($user_id)){
		$user_id = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	$account_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));


	$lilo_mongo->selectCollection('Properties');
	$property_data = $lilo_mongo->findOne(array('lilo_id' => $user_id));

	if(trim($property_data['foto']) == ''){
		$property_data['foto'] = 'default.png';
	}
	
	$property_data['foto_url'] = $_SESSION['basepath'] . 'user_generated_data/profile_picture/' . $property_data['foto'];

	$result = array_merge((array)$account_data, (array)$property_data);
	
	unset($result['lilo_id']);
	unset($result['_id']);
	unset($result['password']);
	
	return json_encode($result);
}


// input ke db doang
function friend_user_submitchat(){
	extract($_REQUEST);	// friend_id, text

//	$friend_id = isset($friend_id) ? $friend_id : func_arg(0);
	
	if(!isset($friend_id)){
		return '';
	}
	
	$curr_time = time();
	
	$user_id = $_SESSION['user_id'];
	
	if($user_id < $friend_id){
		$table_name = 'CHAT_' . $user_id . '_' . $friend_id;
	} else {
		$table_name = 'CHAT_' . $friend_id . '_' . $user_id;
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection($table_name);
	
	$text = htmlspecialchars(trim($text));
	
	if($text != ''){
		// masukkan ke DB
		$data = array('time' => $curr_time, 'speaker' => $user_id, 'text' => $text);
		$lilo_mongo->insert($data);
	}

	// CATATAN TAMBAHAN
	//saat rully kirim chat ke mukhtar, sekalian masukin data ke table ChatAlert (sender: rully, receiver: mukhtar, time: time() )
	//
	//mukhtar: saat loadFriendList, cek jika ada row di ChatAlert dengan (receiver:mukhtar), dapatkan semua sender-nya, 
	//
	//jika window chat utk sender blm ada: 
	//	show circlenya jika masih hidden, ubah warna sender jadi merah
	//	atau
	//	sekalian show windownya? [v]
	//
	//jika window chat utk sender sudah ada: do nothing
	
	$lilo_mongo->selectCollection("ChatAlert");
	$data = array('sender' => $user_id, 'receiver' => $friend_id, 'time' => $curr_time);
	$lilo_mongo->insert($data);

	return "1";
}

// mengembalikan array friend_id yang mengirim chat dan blm dibuka oleh current user
function friend_user_chatalert(){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection("ChatAlert");

	$user_id = $_SESSION['user_id'];
	
	$sender = $lilo_mongo->command_values(array("distinct" => "ChatAlert", "key" => "sender", "query" => array('receiver' => $user_id)));
	$count_sender = count($sender);
	
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection("Properties");
	
	for($idx = 0; $idx < $count_sender; $idx++){
		
		$fullname_idx = $count_sender + $idx;
		
		$sender_data = $lilo_mongo->findOne(array('lilo_id' => $sender[$idx]));
		
		$sender[$fullname_idx] = $sender_data['fullname'];

	}

	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection("ChatAlert");
	
	$lilo_mongo->remove(array('receiver' => $user_id));

	return json_encode($sender);
}


//	TODO: Filter variable 'text'
function friend_user_getchat(){
	// CHAT_ : start_time, time, text
	//	

	extract($_REQUEST);	// friend_id, text
	
	$friend_id = isset($friend_id) ? $friend_id : func_arg(0);
	
	if(!isset($friend_id)){
		return '';
	}
	
	$curr_time = time();
	
	$user_id = $_SESSION['user_id'];
	
	if($user_id < $friend_id){
		$table_name = 'CHAT_' . $user_id . '_' . $friend_id;
	} else {
		$table_name = 'CHAT_' . $friend_id . '_' . $user_id;
	}
	
	// dapetin nama lengkap dari user_id dan friend_id
	$user_detail = friend_user_detailbyuserid($user_id);
	$user_detail = json_decode($user_detail);
	$friend_detail = friend_user_detailbyuserid($friend_id);
	$friend_detail = json_decode($friend_detail);
	
	$arr_fullname[$user_id] = $user_detail->fullname;
	$arr_fullname[$friend_id] = $friend_detail->fullname;
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Social');
	$lilo_mongo->selectCollection($table_name);
	
	$limit = 50;
	
	// dapatkan last 50 conversation
	// find($array_parameter = array(), $limit = 0, $sort = array())
	
	$count = $lilo_mongo->count();

	if($count >= $limit){
		$conversation = $lilo_mongo->find(array(), $limit, array('time' => -1));
		
		$retval = '';
		$retval_array = array();
		$idx = $limit - 1;
		while($conv = $conversation->getNext()){
			$speaker = $conv['speaker'];
			$text = $conv['text'];
			
			$speaker_fullname = $arr_fullname[$speaker];
			
			$retval_array[$idx] = "<br><strong>$speaker_fullname</strong><br>" . $text;
			$idx--;
		}
		
		for($i = 0; $i < count($retval_array); $i++){
			if(isset($retval_array[$i]) && trim($retval_array[$i]) != ''){
				$retval .= $retval_array[$i];
			}
		}
		
		return $retval;
	} else {
		$conversation = $lilo_mongo->find(array(), $limit, array('time' => 1));
	
		$retval = '';
		$retval_array = array();
		while($conv = $conversation->getNext()){
			$speaker = $conv['speaker'];
			$text = $conv['text'];
			
			$speaker_fullname = $arr_fullname[$speaker];
			
			$retval .= "<br><strong>$speaker_fullname</strong><br>" . $text;
			
		}
		
		return $retval;
	}
	

}


// cek apakah user online
// online: current_time - Session.time_end < 5menit
function friend_user_isonline($user_id = null){
	$user_id = isset($user_id) ? $user_id : func_arg(0);
//	$user_id = func_arg(0);
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Session');
	
	$session_data = $lilo_mongo->findOne(array('user_id' => $user_id));
	
	$online = 0;
	if(count($session_data)){
		$idle_time = time() - strtotime($session_data['time_end']);
		$online = ($idle_time < 5*60) ? 1 : 0;
	}

	return $online;
	
}

function friend_user_facebook_invite(){
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	$template->session_id = user_user_sessionid();

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

//	$return = $template->render($template_file);
//	return $return;

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

//	$template->middle = $template->render("modules/000_user_interface/templates/ui_user_play.php");
//	$template->middle = $template->render("modules/004_friends/templates/friend_user_people.php");
	$template->middle = $template->render("modules/004_friends/templates/friend_user_facebook_invite.php");

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
}

?>