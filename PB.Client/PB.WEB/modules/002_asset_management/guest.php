<?php

require_once('modules/001_user_management/user.php');
require_once('config/security.php');

function asset_guest_get_lobby(){
	// harusnya ini masuk di server management!!! pindahkan kesana!
	// dapatkan daftar Lobby dari table Assets.Level
	//$lilo_mongo = new LiloMongo();
	//$lilo_mongo->selectDB('Assets');
	//$lilo_mongo->selectCollection('Level');
	//
	//$lobby_cursor = $lilo_mongo->find(array('tags' => 'Lobby'));
	//
	//$lobby_name_list = '';
	//while($curr = $lobby_cursor->getNext()){
	//	$lobby_name_list .= $curr['name'];
	//}

  
	// dapatkan last room dari table Game.RoomHistory [userid, room]
	$userid = $_SESSION['user_id'];
  
  if(trim($userid) == ''){
    $session_id = $_REQUEST['token'];
    $userid = user_user_session_to_user_id($session_id);
  }
  

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('RoomHistory');
	
	$room_data = $lilo_mongo->findOne(array('userid' => $userid));
	

	// dapatkan IP dan Port dari table Game.LobbySetting
	$lilo_mongo->selectCollection('LobbySetting');
	$lobby_setting = $lilo_mongo->findOne(array());
	
  $room_data_ = '';
  
	if(is_array($lobby_setting) && count($lobby_setting)){
		$ip = $lobby_setting['ip'];
		$port = $lobby_setting['port'];
		$room_history = $lobby_setting['room_history'];
    
    if(intval($room_history) == 1){
      $room_data_ = $room_data['room'];
    }
    
	}
	
	return "$ip:$port," . $room_data_;


	// Contooh...
//	return "124.66.160.109:5055," . $room_data['room'];	// sementara di disable
	return "124.66.160.109:5055,";
  
  
	// Contooh...
//	return "124.66.160.109:5055," . $lobby_name_list;
//	return "192.168.1.4:5055," . $lobby_name_list;
//	return "192.168.1.6:5055," . $lobby_name_list;
//	return "192.168.1.3:5055," . $lobby_name_list;
//	return "192.168.1.106:5055," . $lobby_name_list;
}
