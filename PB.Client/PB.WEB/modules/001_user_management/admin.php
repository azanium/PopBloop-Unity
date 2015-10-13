<?php
/**
 * TODO:
 * list of function:
 *
 */
include_once('libraries/LiloMongo.php'); 
include_once('modules/001_user_management/user.php');
include_once('modules/000_user_interface/admin.php');
include_once('libraries/Template.php');

/**
 * CRUD data admin2
 */
function user_admin_admin(){
	$op = func_arg(0);
	
	switch(strtolower(trim($op))){
		case 'add':
			$username	= $_REQUEST['username'];
			$password	= $_REQUEST['password'];
			$email		= $_REQUEST['email'];
			
			$admin_created = create_new_admin($username, $password, $email);
			if($admin_created === TRUE){
				$_SESSION['pop_status_msg'][] = "Admin baru berhasil dibuat: <br>&bull;&nbsp;Username: $username<br>&bull;&nbsp;Password: $password<br>";
			} else {
				$_SESSION['pop_error_msg'][] = $admin_created;
			}
			
			break;
		
		case 'update':
			$username	= $_REQUEST['username'];
			$old_password	= $_REQUEST['old_password'];
			$new_password	= $_REQUEST['new_password'];
			$new_email		= $_REQUEST['new_email'];
			
			$admin_updated = update_admin($username, $old_password, $new_password, $email);
			if($admin_updated === TRUE){
				$_SESSION['pop_status_msg'][] = "Data admin berhasil diupdate";
			} else {
				$_SESSION['pop_error_msg'][] = $admin_updated;
			}
			
			break;
		
		case 'delete':	//	user/admin/admin/[delete]/[$username]
			$username = func_arg(1);
			$admin_deleted = delete_admin($username);
			
			if($admin_deleted === TRUE){
				$_SESSION['pop_status_msg'][] = "Data admin [$username] berhasil dihapus";
			} else {
				$_SESSION['pop_error_msg'][] = $admin_deleted;
			}
			
			break;
	}
	
	// default $op = view
	// dapatkan data semua admin	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Admin');

	$admin_data_cursor = $lilo_mongo->find();
	
	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->admin_data_cursor = $admin_data_cursor;

	// sampe senee...
	$html = $template->render("modules/001_user_management/templates/user_admin_admin.php");
	if(trim($ajax) == 'ajax'){
		
	} else {
		$html = ui_admin_default(NULL, $html);
	}

	return $html;

	
}

/**
 * mengecek apakah current_user [atau user dgn username yg diberikan] adalah administrator
 */
function user_admin_is_admin($uname = NULL){	// PERBAIKI, AMBIL DATA LANGSUNG DARI Users.Admin
	// sementara
	return true;
	// sementara
	$username = isset($uname) ? $uname : $_SESSION['username'];

	$is_admin = 0;
	if(user_user_loggedin()){
		$user_property = user_user_property(array('username' => $username));
		$user_property = json_decode($user_property);
		if(isset($user_property->user_group) && is_array($user_property->user_group)){
			foreach($user_property['user_group'] as $ug){
				if(strtolower($ug) == "admin"){
					$is_admin = 1;
				}
			}
		}
		
	}
	
	return $is_admin;
}

/**
 * CRUD for all users
 * return_type = html, array
 */
function user_admin_user_list($return_type = "html"){

	$is_admin = user_admin_is_admin();
	if(!$is_admin){
//		return 0;
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$all_users = $lilo_mongo->find();
	$users_array = array();
	while($user = $all_users->getNext()){
		$users_array[] = $user;
	}

	if($return_type == "array"){
		return $users_array;
	}
	
	
	
	$template = new Template();
	$template->users_array = $users_array;
	$template->basepath = $_SESSION['basepath'];

	$html = $template->render("modules/001_user_management/templates/user_admin_user_list.php");
	return $html;
}

function user_admin_user_edit(){
	$username = func_arg(0);
	return "edit user $username";
}

function user_admin_user_delete(){
	$username = func_arg(0);
	return "delete user $username";
}

function user_admin_deletebyid($user_id = NULL){
	$user_id = isset($user_id) ? $user_id : func_arg(0);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$lilo_mongo->remove(array('lilo_id' => $user_id));
	
	$lilo_mongo->selectCollection('GroupMember');
	$lilo_mongo->remove(array('user_id' => $user_id));
	
	return "1";
}

function user_admin_detail($user_id = NULL){
	$user_id = isset($user_id) ? $user_id : func_arg(0);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	$result_1 = $lilo_mongo->findOne(array('lilo_id' => $user_id));

	$lilo_mongo->selectCollection('Properties');
	$result_2 = $lilo_mongo->findOne(array('lilo_id' => $user_id));
	
	$result = array_merge((array)$result_1, (array)$result_2);

	if(!$result){
		return '';
	} else {
		return json_encode($result);
	}

}

function user_admin_user_add(){
	$template = new Template();
	$template->users_array = $users_array;

	return $template->render("modules/001_user_management/templates/user_admin_user_add.php");
}

function user_admin_wscountcurrentplayer(){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('Concurrent');
	
	$cc_cursor = $lilo_mongo->count();
	
	return $cc_cursor;
}

function user_admin_wscurrentplayer(){
	// ambil data dari Game.Concurrent (user_id, room, datetime)
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('Concurrent');
	
	$cc_cursor = $lilo_mongo->find();
	
	$ccs = array();
	
	while($cc = $cc_cursor->getNext()){
		
		$user_property = user_user_property(array('lilo_id' => $cc['user_id']), 'array');
		$ccs[] = array_merge((array)$cc, (array)$user_property);
	}

	return json_encode($ccs);
}

function user_admin_current_player(){
  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  

  $html = $template->render("modules/001_user_management/templates/user_admin_current_player.php");
  $html = ui_admin_default(NULL, $html);

  return $html;
}


function user_admin_accountimport(){
	// ambil semua data di Users.Account x Users.Properties
	// output ke file CSV
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');

	$account_cursor = $lilo_mongo->find();
	
	$date = date("Y-m-d");

	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename=PopBloop.Data.User.'.$date.'..'.time().'.csv');
	header('Pragma: no-cache');

	$csv_string = "No, User ID, User Name, Full Name, Avatar Name, Email, Twitter, Handphone, Gender, Birthday, Join Date, Location\n \n \n";
	$no = 1;
	while($account = $account_cursor->getNext()){
		$account['join_date_dmy'] = date("d-m-Y", $account['join_date']);
		
		// dari account['lilo_id'] dapatkan properties['lilo_id']
		$lilo_mongo->selectCollection('Properties');
		$properties = $lilo_mongo->findOne(array('lilo_id' => $account['lilo_id']));
		
		$csv_string .= "$no, " . $account['lilo_id'] . ", " . $account['username'] . ", " . $properties['fullname'] . ", " . $properties['avatarname'] . ", " . $account['email'] . ", " . $properties['twitter'] . ", " . $properties['handphone'] . ", " . $properties['sex'] . ", " . $properties['birthday'] . ", " . $account['join_date_dmy'] . ", " . $properties['location'] . "\n";
		$no++;
	}

	$csv_string .= "\n \n ";

	echo $csv_string;
	exit;
}


function user_admin_sessionimport($date = NULL){
	// ambil semua data di Users.Session
	// output ke file CSV
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Session');

	if(!isset($date)){
		$date = func_arg(0);
	}
	
	if(trim($date) == ''){	// format: 2012-07-07
		// today
		$date = date("Y-m-d");
	}
	
	$search = new MongoRegex("/".$date."/i");
	$session_cursor = $lilo_mongo->find(array("time_end" => $search));

	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename=Session.Log.'.$date.'..'.time().'.csv');
	header('Pragma: no-cache');

	$csv_string = " , ACTIVE SESSION, DATE:, $date\n \n \n";
	$csv_string .= "No, User Name, User ID, IP, Time Start, Time End, Host, User Agent \n";
	$no = 1;
	while($session = $session_cursor->getNext()){
		$csv_string .= "$no, " . $session['username'] . ", " . $session['user_id'] . ", " . $session['REMOTE_ADDR'] . ", " . $session['time_start'] . ", " . $session['time_end'] . ", " . $session['REMOTE_HOST'] . ", " . $session['HTTP_USER_AGENT'] . "\n";
		$no++;
	}

	$csv_string .= "\n \n ";
	
	$lilo_mongo->selectCollection('SessionLog');
	
	$search = new MongoRegex("/".$date."/i");
	$session_cursor = $lilo_mongo->find(array("time_end" => $search));
	
	$csv_string .= " , NON-ACTIVE SESSION, DATE:, $date\n \n \n";
	$csv_string .= "No, User Name, User ID, IP, Time Start, Time End, Host, User Agent \n";
	$no = 1;
	while($session = $session_cursor->getNext()){
		$csv_string .= "$no, " . $session['username'] . ", " . $session['user_id'] . ", " . $session['REMOTE_ADDR'] . ", " . $session['time_start'] . ", " . $session['time_end'] . ", " . $session['REMOTE_HOST'] . ", " . $session['HTTP_USER_AGENT'] . "\n";
		$no++;
	}
	
	
	
	echo $csv_string;
	exit;
}

function user_admin_roomplayerstat(){
  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  

  $html = $template->render("modules/001_user_management/templates/user_admin_roomplayerstat.php");
  $html = ui_admin_default(NULL, $html);

  return $html;
}


function user_admin_wsroomstatrangedate($date_start = NULL, $date_end = NULL, $room = NULL){	// $date dalam format "n/j/Y", "7/9/2012"
	// dipanggil oleh user_admin_roomplayerstat
	// menampilkan data dari Game.RoomStats berdasar range tanggal yg dipilih user
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('RoomStats');

	
	if(!isset($date_start)){
		// $date_start = func_arg(0);
		// ga boleh lewat func_arg, soalnya: date ada tanda garis miring (/)
		$date_end = $_POST['date_start'];
	}
	
	if(!isset($date_end)){
		// $date_end = func_arg(1);
		// ga boleh lewat func_arg, soalnya: date ada tanda garis miring (/)
		$date_end = $_POST['date_end'];
	}
	
	if(trim($date_end) == ''){
		$date_end = date("n/j/Y");
	}
	
	if(trim($date_start) == ''){
		// jika date_start kosong, set date_start dengan date_end - 10
		// int mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
		$date_end_expl = explode('/', $date_end);
		
		$date_end_m = $date_end_expl[0];
		$date_end_d = $date_end_expl[1];
		$date_end_y = $date_end_expl[2];
		
		$date_end_mktime = mktime(0, 0, 0, $date_end_m, $date_end_d, $date_end_y);
		
		$ten_days = 10 * 24 * 3600;
		
		$date_end_mktime -= $ten_days; 
		
		$date_start = date("n/j/Y", $date_end_mktime);
	}

	if(!isset($room)){
		$room = func_arg(2);
	}

	if(trim($room) == ''){
		// dapatkan semua room di table Game.RoomStats
		$rooms = $lilo_mongo->command_values(array('distinct' => 'RoomStats', 'key' => 'room'));
	} else {
		$rooms = array(0 => $room);
	}
	
	// die("<pre>" . print_r($rooms, true) . "</pre>");
	
	$result = array();
	foreach($rooms as $r){
		// dapatkan data di Game.RoomStats untuk room = $r dan date mulai dari date_start s.d. date_end
		// date sebagai key
		
		$date_start_expl = explode('/', $date_start);
		
		$date_start_m = $date_start_expl[0];
		$date_start_d = $date_start_expl[1];
		$date_start_y = $date_start_expl[2];
		
		$date_start_mktime = mktime(0, 0, 0, $date_start_m, $date_start_d, $date_start_y);
		
		
		
		$date_end_expl = explode('/', $date_end);
		
		$date_end_m = $date_end_expl[0];
		$date_end_d = $date_end_expl[1];
		$date_end_y = $date_end_expl[2];
		
		$date_end_mktime = mktime(0, 0, 0, $date_end_m, $date_end_d, $date_end_y);
		
		
		$one_day = 24 * 3600;
		
		for($cur_date = $date_start_mktime; $cur_date <= $date_end_mktime; $cur_date += $one_day){
			$cur_date_ = date("n/j/Y", $cur_date);
			$cur_date__ = date("n_j_Y", $cur_date);
			$r__ = str_replace(' ', '__space__', $r);
			$result[$r__][$cur_date__] = $lilo_mongo->findOne(array('date' => $cur_date_, 'room' => $r));
		}
		
	}
	
	return json_encode($result);
	// die("<pre>" . print_r($result, true) . "</pre>");
}


function user_admin_wsroomstat($date = NULL){	// $date dalam format "n/j/Y", "7/9/2012"
	// dipanggil oleh user_admin_roomplayerstat
	// menampilkan data dari Game.RoomStats berdasar tanggal yg dipilih user
	
	if(!isset($date)){
		$date = func_arg(0);
	}
	
	if(trim($date) == ''){
		$date = date("n/j/Y");
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('RoomStats');	// date, room, unique_visit, visit
	
	$room_cursor = $lilo_mongo->find(array("date" => $date));

	$result = array();
	$no = 1;
	while($room = $room_cursor->getNext()){
		$result[] = array_merge(array('no' => $no), (array)$room);
		$no++;
	}
	
	return json_encode($result);
}


function user_admin_wsplayerstat($date = NULL){	// $date dalam format "n/j/Y", "7/9/2012"
	// dipanggil oleh user_admin_roomplayerstat
	// menampilkan data dari Game.PlayerStats berdasar tanggal yg dipilih user
	
	if(!isset($date)){
		$date = func_arg(0);
	}
	
	if(trim($date) == ''){
		$date = date("n/j/Y");
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('PlayerStats');	// date, userid, room, visit
	
	$room_cursor = $lilo_mongo->find(array("date" => $date));

	$result = array();
	$no = 1;
	while($room = $room_cursor->getNext()){
		
		// dari $room['userid'] dapatkan data di Users.Account dan Users.Properties
		$lilo_mongo = new LiloMongo();
		$lilo_mongo->selectDB('Users');
		
		$lilo_mongo->selectCollection('Account');	// email, username, join_date, lilo_id
		$criteria = array('lilo_id' => $room['userid']);
		$account = $lilo_mongo->findOne($criteria);
		
		$lilo_mongo->selectCollection('Properties');	// lilo_id, fullname, avatarname, handphone, twitter, sex, birthday, location
		$criteria = array('lilo_id' => $room['userid']);
		$properties = $lilo_mongo->findOne($criteria);
		
		$result[] = array_merge(array('no' => $no), (array)$room, (array)$account, (array)$properties);
		$no++;
	}
	
	return json_encode($result);
}

// fungsi untuk mengubah password yg sekarang plaintext ke md5
// cukup dieksekusi sekali saja
//function user_admin_md5password(){
//	$lilo_mongo = new LiloMongo();
//	$lilo_mongo->selectDB('Users');
//	$lilo_mongo->selectCollection('Account');
//	
//	// dapatkan semua user
//	$users_data = $lilo_mongo->find();
//	$users_array = array();
//	while($curr = $users_data->getNext()){
//		$users_array[] = array('username' => $curr['username'], 'password' => $curr['password']);
//	}
//	
//	foreach($users_array as $ua){
//		if($ua['username'] != 'mukhtar') $lilo_mongo->update_set(array('username' => $ua['username']), array('password' => md5($ua['password'])));
//		echo "username: $username, password: " . md5($ua['password']) . "<br />";
//	}
//
//}


//////////////////////
// HELPER FUNCTION	//
//////////////////////

function create_new_admin($username = NULL, $password = NULL, $email = NULL){
	if(trim($username) == '' || trim($password) == '' || trim($email) == ''){
		return FALSE;
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Admin');

	// cek apakah username / email sudah digunakan
	$admin_data = $lilo_mongo->find(array('username' => $username));
	while($curr = $admin_data->getNext()){
		return "Username $username sudah digunakan!";
	}

	$admin_data = $lilo_mongo->find(array('email' => $email));
	while($curr = $admin_data->getNext()){
		return "Email $email sudah digunakan!";
	}

	$new_admin = array('username' => $username, 'password' => $password, 'email' => $email);
	$lilo_mongo->insert($new_admin);
	
	return TRUE;

}

function update_admin($username, $old_password, $new_password, $email){
	// username tidak bisa diganti, hanya password dan email
	if(trim($username) == '' || trim($old_password) == '' || trim($new_password) == '' || trim($email) == ''){
		return FALSE;
	}

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Admin');

	$admin_data = $lilo_mongo->find(array('username' => $username, 'password' => md5($old_password)));
	$exist = FALSE;
	while($curr = $admin_data->getNext()){
		$exist = TRUE;
	}
	
	if(!exist){
		return "Data (Username / Password) yang Anda masukkan salah!";
	}
	
	$array_criteria = array('username' => $username, 'password' => md5($old_password));
	$array_newobj = array('password' => $new_password, 'email' => $email);
	
	$lilo_mongo->update_set($array_criteria, $array_newobj, NULL);
	
	return TRUE;
	
}

function delete_admin($username){
	// pastikan orang ini tidak melakukan suicide... :P
	if(trim($username) == trim($_SESSION['username'])){
		return "You stupid bastard.... You can't delete your own account!";
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Admin');

	$array_criteria = array('username' => $username);
	$lilo_mongo->remove($array_criteria);

	return TRUE;
}


