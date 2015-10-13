<?php

// currently, there are only 2 user groups: admin & user
// when another group added, just create a new function 'permission_' . $groupname

// group initial data:
// use Users;
// db.Group.insert({'name':'admin'});
// db.Group.insert({'name':'user'});
//		add lilo_id for each record

// groupmember, sample data
// db.GroupMember.insert({'user_id':'4dfb2bb2c1b4bac41c000000', 'group_id':'4e7a538def12cb9802911317'});	-> mukhtar - admin
// db.GroupMember.insert({'user_id':'4dfb2bb2c1b4bac41c000000', 'group_id':'4e7a5394ef12cb9802911318'});	-> mukhtar - user
// db.GroupMember.insert({'user_id':'4df6e7192cbfd4e6c000fd9b', 'group_id':'4e7a5394ef12cb9802911318'});	-> rully - user

include_once('libraries/LiloMongo.php'); 

//return true if current user is member of admin group
function permission($role = NULL, $user_id = NULL){
	$user_id = isset($user_id) ? $user_id : $_SESSION['user_id'];

	if($role == 'guest'){
		return true;
	}
	
	if($role != 'guest' && trim($user_id) == ''){
		return false;
	}

	

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Group');

	$group_id_ = $lilo_mongo->findOne(array('name' => $role));
	$group_id = $group_id_['lilo_id'];

	$lilo_mongo->selectCollection('GroupMember');
	$exists = $lilo_mongo->findOne(array('user_id' => $user_id, 'group_id' => $group_id));
	// die("userid: $user_id, groupid: $group_id");
	if(count($exists)){
		return true;
	}

	return false;
}

// memastikan session_id sama dengan yg tersimpan di Users.Session.session_id
function check_session(){
//	return true;
	
	$session_id = $_SESSION['session_id'];
	$user_id = $_SESSION['user_id'];

	if(trim($user_id) != '' && trim($session_id) != ''){
		$lilo_mongo = new LiloMongo();
		$lilo_mongo->selectDB('Users');
		$lilo_mongo->selectCollection('Session');
		
		$exist = $lilo_mongo->findOne(array('session_id' => $session_id, 'user_id' => $user_id));
		
		if(!is_array($exist) || !count($exist)){
			return false;
		}
		
		$cur_date = date("Y-m-d H:i:s");
		$lilo_mongo->update_set(array('session_id' => $session_id, 'user_id' => $user_id), array('time_end' => $cur_date));
		
	}
	
	return true;
}


?>