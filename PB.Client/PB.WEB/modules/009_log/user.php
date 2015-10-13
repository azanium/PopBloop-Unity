<?php
include_once('libraries/LiloMongo.php'); 

// LOG: Error Log, Access Log

function log_user_view(){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Logs');
	$lilo_mongo->selectCollection('Error');
	
	
	
}

function log_user_default(){
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
?>