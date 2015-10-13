<?php
/**
 * Analisa
 * Circles: lilo_id, user_id, circle_array 
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
function friend_user_default(){	// ajax only
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
			$new_circle = func_arg(1);
			$circles['circle_array'] = array_merge((array)$circles['circle_array'], array($new_circle));
			break;
		case 'update':	// change circle name. example, to change circle 'enemy' to 'bastard': friend/user/ws_circle/update/enemy/bastard
			$old_circle_name = func_arg(1);
			$new_circle_name = func_arg(2);
			$old_circles = $circles['circle_array'];
			$new_circles = array();
			foreach($old_circles as $oc){
				if(strtolower($oc) != strtolower($new_circle_name)){
					$new_circles[] = $oc;
				} else {
					$new_circles[] = $new_circle_name;
				}
			}
			
			$circles['circle_array'] = $new_circles;
			break;
		case 'delete':
			$deleted_circle = func_arg(1);
			$old_circles = $circles['circle_array'];
			$new_circles = array();
			foreach($old_circles as $oc){
				if(strtolower($oc) != strtolower($deleted_circle)){
					$new_circles[] = $oc;
				}
			}
			
			$circles['circle_array'] = $new_circles;
			break;
	}
	
	if(isset($op) && strtolower($op) != 'read'){
		// update_set($array_criteria, $array_newobj, $array_options = array("multiple" => true))
		$lilo_mongo->update_set(array('user_id' => $user_id), array('circle_array' => $circles['circle_array']), array("multiple" => true, 'upsert' => true));
	}
	
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
		$requests_array[$idx]['profile_picture'] = $inviter_properties['profile_picture'];
		$requests_array[$idx]['sex'] = $inviter_properties['sex'];
	}
	
	return json_encode($requests_array);
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
			if(in_array($circle_data[$idx], $circle_array)){
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

//	write_log(array('filename' => NULL, 'somecontent' => print_r($outer_circle_friend_data, true) . "\n\n" . print_r($friend_list, true)));

	$circle_data[] = array_merge(array('Outer Circle'), (array)$outer_circle_friend_data);

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

?>