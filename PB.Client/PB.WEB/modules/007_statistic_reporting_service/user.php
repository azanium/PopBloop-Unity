<?php
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');


/**
 * examples:
 * -to get online time for user rully, use: http://localhost/popbloop/report/user/achievement/4df6e7192cbfd4e6c000fd9b/online_time/get
 * -to increase online time for user rully by 200ms, use: http://localhost/popbloop/report/user/achievement/4df6e7192cbfd4e6c000fd9b/online_time/dec/200
 */

function report_user_achievement($user_id = NULL, $achievement_type = NULL, $op = NULL, $value = NULL){
	$user_id = isset($user_id) ? $user_id : $_SESSION['user_id'];
	
	if(!isset($user_id)){
		$user_id = func_arg(0);
	}
	
	if(!isset($achievement_type)){
		$achievement_type = func_arg(1);
	}
	
	if(!isset($op)){
		// inc, set, dec, * get
		// * = default
		$op = func_arg(2);
	}

	$op = strtolower(trim($op));

	if(!isset($value)){
		$value = func_arg(3);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('Achievement');
	
	// jika ada, dapatkan dulu current value
	$criteria = array('tipe' => $achievement_type, 'userid' => $user_id);
	$curr_ach = $lilo_mongo->findOne($criteria);
	
	if(!($op == '' || $op == 'get') && count($curr_ach)){
		$lilo_mongo->selectCollection('AchievementLog');
		// sebelum isi db diubah, backup dulu ke table AchievementLog
		$curr_ach_log = array_merge((array)$curr_ach, array('q' => $_REQUEST['q'], 'time' => time()));
		$lilo_mongo->insert($curr_ach_log);
	}
	
	$lilo_mongo->selectCollection('Achievement');

	switch($op){
		case '':
		case 'get':
			return $curr_ach['value'];
			break;
		case 'set':
			$lilo_mongo->update_set($criteria, array('value' => $value));
			break;
		case 'inc':
			$lilo_mongo->update_set($criteria, array('value' => (int)$curr_ach['value'] + (int)$value));
			break;
		case 'dec':
			$lilo_mongo->update_set($criteria, array('value' => (int)$curr_ach['value'] - (int)$value));
			break;
	}
	
	return "1";
}