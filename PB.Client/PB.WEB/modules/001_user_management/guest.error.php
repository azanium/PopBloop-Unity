<?php
/**
 * TODO: advance PHP authentication
 * list of function:
 *		- login_form:	generate form for login
 *		- 
 */
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/user.php');
include_once('modules/001_user_management/user.php');

function user_guest_add_user($args = NULL){	// username, password, email
	if(isset($args) && is_array($args) && count($args) > 0){
		extract($args);
	} else {
		$fullname = $_POST['fullname'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];
	}
	
	// validasi semua input...
	if(strlen(trim($username)) < 4){
		return "ERROR - Use at least 4 character for username";
	}
	
	if(strlen(trim($password)) < 6){
		return "ERROR - Use at least 6 character for password";
	}
	
	// TODO: email validation
	if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
		return "ERROR - Invalid email";
	}
	
	$username_exists = user_user_redundancy_check('username', $username);
	if($username_exists){
		return "ERROR - Username $username already taken. Use another username.";
	}
	
	$email_exists = user_user_redundancy_check('email', $email);
	if($email_exists){
		return "ERROR - Email already used. Use another email.";
	}
	
	$join_date = time();
	
	// masukkan ke DB
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$user_data = array('email' => $email, 'password' => $password, 'username' => $username, 'join_date' => $join_date);
	$lilo_id = $lilo_mongo->insert($user_data);
	$lilo_mongo->update($user_data, array_merge($user_data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
	
	// masukkan fullname ke table Properties
	$lilo_mongo->selectCollection('Properties');
	$lilo_mongo->insert(array('lilo_id' => $lilo_id, 'fullname' => $fullname));
	
	
	// daftarkan user ini ke group 'user'
	// sampe seneee...
	$lilo_mongo->selectCollection('Group');
	// dapatkan lilo_id untuk name: 'user'
	$group_ = $lilo_mongo->findOne(array('name' => 'user'));
	$group_id = $group_['lilo_id'];
	
	
	$lilo_mongo->selectCollection('GroupMember');
	$lilo_mongo->insert(array('user_id' => (string)$lilo_id, 'group_id' => $group_id));
	
	$lilo_mongo->close();
	
	
	if(isset($_POST['automate_login']) && $_POST['automate_login'] == '1'){
		$_SESSION['user_id'] = (string)$lilo_id
		user_user_login($username, $password);
	}
	
	// semua OK? return 'OK'
	return "OK";
	
}


function user_guest_login($uname = NULL, $passwd = NULL){
	// bisa diakses lewat web dgn path: /user/user/login/[username/password]
	// atau dipanggil langsung dgn fungsi user_guest_login('username','password')
	$username = isset($uname) ? $uname : func_arg(0);
	$password = isset($passwd) ? $passwd : func_arg(1);
	if(strlen(trim($username)) == 0 || strlen(trim($password)) == 0){
		unset($_SESSION['session_id']);
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		return "0";
	}

	// connect to mongodb using default setting
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Account');
	$user_exists = $lilo_mongo->findOne(array('username' => $username, 'password' => $password));

	$session_id = $_SESSION['session_id'];
	$session_username = $_SESSION['username'];
	
	// bagaimana jika $session_username berbeda dengan $username?
	
	$cur_date = date("Y-m-d H:i:s");
	if(is_array($user_exists) && count($user_exists)){
//		die("<pre>".print_r($user_exists, true)."</pre>");
		if(trim($session_id) == '' || trim($session_username) == ''){
			$user_id = (string)$user_exists['_id'] . '';
			// generate session_id
			$session_id = md5($cur_date . " - " . $username);
			$_SESSION['session_id'] = $session_id;
			$_SESSION['username'] = $username;
			$_SESSION['user_id'] = $user_id;
			
			// simpan di table session
			//		array(session_id, username, time_start, time_end)
			$lilo_mongo->selectCollection('Session');
			$lilo_mongo->insert(
				array(
					'session_id'	=> $session_id,
					'user_id'			=> $user_id,
					'username'		=> $username,
					'time_start'	=> $cur_date,
					'time_end'		=> ''
				)
			);
			
			return $session_id;
		} else {
			if($session_username == $username){
				// mencoba login, padahal sudah terdaftar...
				// biarkan saja, sepertinya orang ini ga ada kerjaan
				return $session_id;
			} else {
				// sudah terdaftar, kemudian mencoba login dengan username dan password yg lain
				// logout dulu, baru login dengan username yg baru

				//		update($array_criteria, $array_newobj, $array_options)
				$array_criteria = array('session_id' => $session_id);
				$array_newobj = array('$set' => array('time_end' => $cur_date));
				$array_options = array('upsert' => true);
				$lilo_mongo->selectCollection('Session');
				$lilo_mongo->update($array_criteria, $array_newobj, $array_options);
				
				$session_id = md5($cur_date . " - " . $username);
				$_SESSION['session_id'] = $session_id;
				$_SESSION['username'] = $username;

				// simpan di table session
				//		array(session_id, username, time_start, time_end)
				$lilo_mongo->selectCollection('Session');
				$lilo_mongo->insert(
					array(
						'session_id'	=> $session_id,
						'username'		=> $username,
						'time_start'	=> $cur_date,
						'time_end'		=> ''
					)
				);
				
				return $session_id;

			}
		}
	} else {
		unset($_SESSION['session_id']);
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		return "0";
	}
	
}
