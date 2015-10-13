<?php
include_once('libraries/LiloMongo.php'); 

// LOG: Error Log, Access Log

function log_guest_view(){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Logs');
	$lilo_mongo->selectCollection('Error');
	
	
	
}

function log_guest_default(){
	$log_type		= $_REQUEST['log_type'];
	$log				= $_REQUEST['log'];
	$stacktrace	= $_REQUEST['stacktrace'];
	$ip_address	= $_SERVER['REMOTE_ADDR'];
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Logs');
	$lilo_mongo->selectCollection('Error');
	
	$log_array = array(	'log_type'		=> $log_type,
										 	'log'					=> $log,
										 	'stacktrace'	=> $stacktrace,
										 	'ip_address'	=> $ip_address
										 );
	
	$lilo_mongo->insert($log_array);
	
	return "1";
}

function log_guest_pageview($op, $var){
	// op: getall, var: ...
	// op: getbydate, var: 2012-03-28
	// op: inc, var: ...
	
	if(!isset($op)){
		$op = func_arg(0);
	}
	
	if(!isset($var)){
		$var = func_arg(1);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Logs');
	$lilo_mongo->selectCollection('PageView');	// 
	
	
	
	
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

?>