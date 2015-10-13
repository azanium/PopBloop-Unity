<?php
include_once('modules/000_user_interface/admin.php');

function server_admin_default(){
	// CRUD of registered server: name, port, ip, max CCU, current CCU* (*: updated realtime by game server on user connect/disconnect)
	// add server

	$ajax = func_arg(0);

	// dapatkan semua level dari Assets.Level
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Servers');
	$lilo_mongo->selectCollection('GameServer');

	$server_array = $lilo_mongo->find();

	$html = '';
	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->server_array = $server_array;

	$html = $template->render("modules/010_game_server_management/templates/server_admin_default.php");
	if(trim($ajax) == 'ajax'){
		
	} else {
		$html = ui_admin_default(NULL, $html);
	}

	return $html;
	
}

// menerima action add new server
function server_admin_add(){
	extract($_REQUEST);

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Servers');
	$lilo_mongo->selectCollection('GameServer');

	if($submitted == 1){
		$game_server = array('name' => $server_admin_add_name, 'ip' => $server_admin_add_ip, 'port' => $server_admin_add_port, 'max_ccu' => $server_admin_add_max_ccu);
		$game_server_id = $lilo_mongo->insert($game_server);

		$lilo_mongo->update($game_server, array_merge($game_server, array('lilo_id' => (string)$game_server_id)), array("multiple" => false) );

	}

	$_SESSION['pop_status_msg'][] = "New server added.";

	header("Location: " . $_SESSION['basepath'] . 'server/admin');
	exit;
}

function server_admin_detail($server_to_show = NULL){
	$server_id = isset($server_to_show) ? $server_to_show : func_arg(0);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Servers');
	$lilo_mongo->selectCollection('GameServer');

	$server_detail = $lilo_mongo->findOne(array('lilo_id' => $server_id));



	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->server_detail = $server_detail;

	return $template->render("modules/010_game_server_management/templates/server_admin_detail.php");
}

function server_admin_delete($server_to_delete = NULL){
	$server_id = isset($server_to_delete) ? $server_to_delete : func_arg(0);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Servers');
	$lilo_mongo->selectCollection('GameServer');
	
	$lilo_mongo->remove(array('lilo_id' => $server_id));
	
	return '1';
}



?>