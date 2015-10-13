<?php

/**
 Halaman admin untuk melihat report achievement
 data ditampilkan secara real-time
 */
 
include_once('modules/001_user_management/user.php');

function report_admin_achievement($op = NULL, $rettype = NULL, $achtype = NULL){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('Achievement');
  
  $op = isset($op) ? $op : func_arg(0);
  $op = strtolower(trim($op));  // read, add, edit, delete

	switch($op){
		case 'get':
		case 'read':	//	report/admin/achievement/get/[rettype]/[achtype]
									//	report/admin/achievement/get/json/all
									//	report/admin/achievement/get/array/online_time
		  $rettype = isset($rettype) ? $rettype : func_arg(1);	$rettype = strtolower(trim($rettype));
		  $achtype = isset($achtype) ? $achtype : func_arg(2);	$achtype = strtolower(trim($achtype));
			
			$criteria = array();
			if($achtype != '' && $achtype != 'all'){
				$criteria = array('tipe' => $achtype);
			}
			
			$ach_cursor = $lilo_mongo->find($criteria);
			
			$ach_array = array();
			while($curr = $ach_cursor->getNext()){
				$tipe = $curr['tipe'];
				
				// dari $curr['user_id'] dapatkan username
				$user_detail = user_user_property(array('lilo_id' => $curr['user_id']), 'array');
				
				$ach_array[$tipe][] = array('lilo_id'	=> $curr['lilo_id'], 
																		'user_id'	=> $curr['user_id'], 
																		'tipe'		=> $curr['tipe'], 
																		'value'		=> $curr['value'],
																		
																		'username'	=> $user_detail['username'],
																		'fullname'	=> $user_detail['fullname'],
																		'email'	=> $user_detail['email'],
																		);
			}
			
			if(strtolower(trim($rettype)) == 'json'){
				$ach_array = json_encode($ach_array);
			}
			
			return $ach_array;
		
		break;
		
		case 'edit':
		
		break;
		
		case 'delete':
		
		break;
	}


  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  
  $html = $template->render("modules/007_statistic_reporting_service/templates/report_admin_achievement.php");

  $html = ui_admin_default(NULL, $html);

  return $html;

}

/**
> db.SessionLog.find();
{ "_id" : ObjectId("4ed4b255c1b4ba6c09000005"), "session_id" : "19aebff52b0cada12ee5f1efbd5583d8", "time_end" : "2011-12-02 08:48:35", "time_start" : "2011-11-29 17:22:13", "user_id" : "4df6e7192cbfd4e6c000fd9b", "username" : "rully" }
{ "_id" : ObjectId("4ed83050c1b4bac009000000"), "session_id" : "1902b9a2a85bf983f4afa98e14bf4c94", "time_end" : "2011-12-02 08:56:32", "time_start" : "2011-12-02 08:56:32", "user_id" : "4df6e7192cbfd4e6c000fd9b", "username" : "rully" }
 */
function report_admin_onlinetime($user_id = NULL){
//	$time_int = strtotime("2011-12-02 08:48:35");
//	
//	$time_int_reformatted = date("Y-m-d H:i:s", $time_int);
//	
//	print("String: " . "2011-12-02 08:48:35" . "<br />");
//	print("Timestamp: " . $time_int . "<br />");
//	print("Timestamp Reformatted: " . $time_int_reformatted . "<br />");
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('SessionLog');

	$user_id = isset($user_id) ? $user_id : func_arg(0);
	$criteria = array('user_id' => $user_id);
	
	$sess_cursor = $lilo_mongo->find($criteria);
	
	$total_time = 0;
	while($curr = $sess_cursor->getNext()){
		$time_start = strtotime($curr['time_start']);
		$time_end = strtotime($curr['time_end']);
		
		$total_time += $time_end - $time_start;
	}

//	return $total_time;
	return sec2hms($total_time, true);

}


/**
 kapan fungsi ini dipanggil? saat user login?
 */
function report_admin_countuseronlinetime($user_id = NULL){
//	hitung total time utk user $user_id di table SessionLog
//	tambahkan waktu yg diperoleh ke field online_time di table Game.Achievement (userid, tipe, value) untuk tipe: online_time
//	table SessionLog utk user $user_id diberi flag (counted = 1)
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('SessionLog');

	$user_id = isset($user_id) ? $user_id : func_arg(0);
	$criteria = array('user_id' => $user_id, 'counted' => array('$ne' => '1'));
	
	$sess_cursor = $lilo_mongo->find($criteria);
	
	$total_time = 0;
	while($curr = $sess_cursor->getNext()){
		$time_start = strtotime($curr['time_start']);
		$time_end = strtotime($curr['time_end']);
		
		$total_time += $time_end - $time_start;	// $total_time dalam satuan second
	}

	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('Achievement');
	$curr_ach = $lilo_mongo->findOne(array('userid' => $user_id, 'tipe' => 'online_time'));
	
	$curr_online_time = intval($curr_ach['value']);
	
	$total_time += $curr_online_time;
	
	$lilo_mongo->update_set(array('userid' => $user_id, 'tipe' => 'online_time'), array('value' => $total_time), array('upsert' => true));

	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('SessionLog');
	
	$lilo_mongo->update_set($criteria, array('counted' => '1'), array('multiple' => true, 'safe' => true));
	
//	return sec2hms($total_time, true);
	return 1;
}

function report_admin_wsusercountquest(){
	//Data Quest:
	//1a. Jumlah users yang mengaktifkan 1 Quest 
	//1b. Jumlah users yang menyelesaikan 1 Quest
	//
	//2a. Jumlah users yang mengaktifkan 2 Quest 
	//2b. Jumlah users yang menyelesaikan 2 Quest
	//
	//3a. Jumlah users yang mengaktifkan 3 Quest 
	//3b. Jumlah users yang menyelesaikan 3 Quest
	//
	//4a. Jumlah users yang mengaktifkan 4 Quest 
	//4b Jumlah users yang menyelesaikan 4 Quest

	// DB: Game.QuestJournal, Game.QuestActive
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	
	$lilo_mongo->selectCollection('QuestJournal');
	$all_users = $lilo_mongo->command_values(array('distinct' => "QuestJournal", 'key' => "userid"));

	$c_1 = array();
	$c_2 = array();
	$c_3 = array();
	$c_4 = array();
	
	foreach($all_users as $u){
		$criteria = array('userid' => $u);
		$count = $lilo_mongo->count($criteria);
		
		$user_acc_prop = report_admin_useraccountproperties($u);
		
		${"c_" . $count}[] = $user_acc_prop;
		
	}


	$lilo_mongo->selectCollection('QuestActive');
	$all_users = $lilo_mongo->command_values(array('distinct' => "QuestActive", 'key' => "userid"));

	$cur_1 = array();
	$cur_2 = array();
	$cur_3 = array();
	$cur_4 = array();
	
	foreach($all_users as $u){
		$criteria = array('userid' => $u);
		$count = $lilo_mongo->count($criteria);
		
		$user_acc_prop = report_admin_useraccountproperties($u);
		
		${"cur_" . $count}[] = $user_acc_prop;
		
	}
	
	$result = array('c_1' => $c_1, 'c_2' => $c_2, 'c_3' => $c_3, 'c_4' => $c_4, 'cur_1' => $cur_1, 'cur_2' => $cur_2, 'cur_3' => $cur_3, 'cur_4' => $cur_4);
	return json_encode($result);
}

function report_admin_useraccountproperties($lilo_id = null){
		
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	$account = $lilo_mongo->findOne(array('lilo_id' => $lilo_id));
	
	$lilo_mongo->selectCollection('Properties');
	$properties = $lilo_mongo->findOne(array('lilo_id' => $lilo_id));
	
	unset($account['password']);
	unset($account['_id']);
	unset($properties['_id']);
	
	$user_account_property = array_merge((array)$account, (array)$properties);

	return $user_account_property;
}
