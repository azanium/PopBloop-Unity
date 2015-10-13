<?php
/**
 * TODO: advance PHP authentication
 * list of function:
 *    - login_form:  generate form for login
 *    - 
 */
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/user.php');
include_once('modules/001_user_management/user.php');
include_once('modules/007_statistic_reporting_service/admin.php');

//require_once "Mail.php";

function user_guest_add_user($args = NULL){  // username, password, email
  if(isset($args) && is_array($args) && count($args) > 0){
    extract($args);

		$birthday_expl = explode('/', $birthday);
		$birthday_dd = $birthday_expl[0];
		$birthday_mm = $birthday_expl[1];
		$birthday_yy = $birthday_expl[2];
  } else {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
		
    $avatarname = $_POST['avatarname'];
		
    $handphone = $_POST['handphone'];
    $twitter = $_POST['twitter'];
		
		// 'sex':new_sex,'birthday':new_birthday,'location':new_location,
		$sex = $_POST['sex'];
		$birthday = $_POST['birthday'];
		
		$birthday_expl = explode('/', $birthday);
		$birthday_dd = $birthday_expl[0];
		$birthday_mm = $birthday_expl[1];
		$birthday_yy = $birthday_expl[2];
		
		$location = $_POST['location'];
  }
  
	if(!isset($via_fb)){
		require_once('libraries/recaptcha/recaptchalib.php');
		$privatekey = "6Lc4rc0SAAAAAPcmFERN1OCwB05q72wvPipQS5zX";
		$resp = recaptcha_check_answer(	$privatekey,
																		$_SERVER["REMOTE_ADDR"],
																		$_POST["recaptcha_challenge_field"],
																		$_POST["recaptcha_response_field"]);
		
		if (!$resp->is_valid) {
			// What happens when the CAPTCHA was entered incorrectly
			return "ERROR - The reCAPTCHA wasn't entered correctly. Try it again.";	//  [" . $resp->error . "]
			// $_SESSION['pop_error_msg'][] = "The reCAPTCHA wasn't entered correctly. Try it again. [" . $resp->error . "]";
			// header("Location: " . $_SESSION['basepath']);
			// exit;
		}
	}
	
  $config = $_SESSION['config'];
  
  // validasi semua input...
	
	// only alpha numeric allowed for username
	// Revisi 04072012: username sama dengan email
//	if(!(ctype_alnum($username) && ctype_alnum($password))){
//		return "ERROR - Use only letters and digits for username and password";
//	}
//  
//  if(strlen(trim($username)) < 4){
//    return "ERROR - Use at least 4 character for username";
//  }
  
  if(strlen(trim($password)) < 6){
    return "ERROR - Use at least 6 character for password";
  }
  
  // TODO: email validation
  if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
    return "ERROR - Invalid email";
  }
	
	// Revisi 04072012: username sama dengan email
	// jadi yg dicek redundan: avatar name
  //$username_exists = user_user_redundancy_check('username', $username);
  //if($username_exists){
  //  return "ERROR - Username $username already taken. Use another username.";
  //}
  
  $avatarname_exists = user_guest_property_redundancy_check('avatarname', $avatarname);
  if($avatarname_exists){
    return "ERROR - Avatar name $avatarname already taken. Use another avatar name.";
  }

  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Users');
  $lilo_mongo->selectCollection('Account');
  
  $email_exists = user_user_redundancy_check('email', $email);
  if($email_exists){
		if(isset($via_fb)){
			// koneksikan kedua account
			// user dapat login dengan username & password PB yg sudah dia daftarkan sebelumnya
			//	dan dia bisa juga login dengan facebook
			//	username: sama dengan email
			//	password: ada 2, password dan fb_password
			//		password adalah password lama
			//		fb_password adalah password yg di-set saat register via facebook (saat ini sama dengan fb id)
			//	jadi, cukup melakukan update di record user dgn email tsb dengan menambahkan field fb_password
			//		saat login, pengecekan password dilakukan pada kedua field (password & fb_password)
			
			//	yg bisa di update di account setting hanya 'password'
			//	'fb_password' tidak dapat diupdate.
			
			$criteria = array('email' => $email);
			$newobj = array('fb_password' => md5($fb_id),  'fb_id' => $fb_id);
			$options = array("multiple" => false);
			
			$lilo_mongo->update_set($criteria, $newobj, $options);
			
			$user_data = $lilo_mongo->findOne($criteria);
			
			// perlu langsung login atau tidak?
			$_SESSION['user_id'] = $user_data['lilo_id'];
			$_SESSION['username'] = $user_data['username'];
			$_SESSION['fullname'] = $user_data['$fullname'];
			
			user_user_login($username, $fb_id);
			
			return 'OK';
			
		} else {
			return "ERROR - Email already used. Use another email.";		// STRING JANGAN DIUBAH, KARENA DIGUNAKAN DI index.php saat registrasi via Facebook
		}
  }
  
  $join_date = time();
	
	$act_key = $username . $join_date . rand(0, 9999);
	$activation_key = md5($act_key);
  
  // masukkan ke DB
  
  $user_data = array('email' => htmlspecialchars($email), 'password' => md5($password), 'username' => htmlspecialchars($username), 'join_date' => $join_date, 'activation_key' => $activation_key, 'fb_id' => $fb_id);
  $lilo_id = $lilo_mongo->insert($user_data);
  $lilo_mongo->update($user_data, array_merge($user_data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false) );
  
  // masukkan fullname ke table Properties
	// update 28 Juni 2012: tambahkan field handphone & twitter
  $lilo_mongo->selectCollection('Properties');
  $lilo_mongo->insert(array(
														'lilo_id' => (string)$lilo_id,
														'fullname' => htmlspecialchars($fullname),
														'avatarname' => htmlspecialchars($avatarname),
														'handphone' => htmlspecialchars($handphone),
														'twitter' => htmlspecialchars($twitter),
														
														'sex' => htmlspecialchars($sex),
														'birthday' => htmlspecialchars($birthday),
														'birthday_dd' => htmlspecialchars($birthday_dd),
														'birthday_mm' => htmlspecialchars($birthday_mm),
														'birthday_yy' => htmlspecialchars($birthday_yy),
														'location' => htmlspecialchars($location)
														
														)
											);
  
  
  // daftarkan user ini ke group 'user'
  // sampe seneee...
  $lilo_mongo->selectCollection('Group');
  // dapatkan lilo_id untuk name: 'user'
  $group_ = $lilo_mongo->findOne(array('name' => 'user'));
  $group_id = $group_['lilo_id'];
  
  
  $lilo_mongo->selectCollection('GroupMember');
  $lilo_mongo->insert(array('user_id' => (string)$lilo_id, 'group_id' => $group_id));
  
  $lilo_mongo->close();
  
  
  if((isset($_POST['automate_login']) && $_POST['automate_login'] == '1') || (isset($automate_login) && $automate_login == '1')){
    
    $_SESSION['user_id'] = (string)$lilo_id;
    $_SESSION['username'] = $username;
    $_SESSION['fullname'] = $fullname;
    
    user_user_login($username, $password);
  }
  
  // semua OK? return 'OK'
  return "OK";
  
}

// Ganti library-nya, dari PEAR ke PHPMailer
// Contek AVSI/modules/001_articles/guest.php fungsi article_guest_contact_form_submit()
//function user_guest_mailactivation(){
//	require_once "Mail.php";
//	require_once "config/mail.php";
//
//	set_time_limit(0);
//	ini_set('memory_limit','512M'); 
//	// setiap x jam, cari di table Users.Account yg belum mempunyai field 'activated'
//	// (field activated isinya: activation datetime)
//	// activation link: popbloop.com/user/guest/activate/[key]
//	// [key] = tersimpan di Users.Account.activation_key
//	// login di blok bila lewat waktu blm di activate
//	// setelah email dikirim, set Users.Account.activation_email_sent = '1' & activated = time()
//	
//	
//	// data di Users.Account yg blm mempunyai field 'activated'
//  $lilo_mongo = new LiloMongo();
//  $lilo_mongo->selectDB('Users');
//  $lilo_mongo->selectCollection('Account');
//
//	$criteria = array('activated' => array('$exists' => false));
//	
//	$non_activated_users = $lilo_mongo->find($criteria);
//
//	$users_array = array();
//	
//	// mail configuration
//	$host = $_SESSION['config']['mail_host'];
//	$port = $_SESSION['config']['mail_port'];
//	$username = $_SESSION['config']['mail_username'];
//	$password = $_SESSION['config']['mail_password'];
//	$from = $_SESSION['config']['mail_from'];
////	die("<pre>" . print_r($_SESSION['config'], true) . "</pre>");
//	$subject = "PopBloop Account Activation";
//	
//	$send_fail = array();
//	$send_succeed = array();
//	
//	while($curr = $non_activated_users->getNext()){
//		$users_array[] = $curr; // array('username' => $curr['username'], 'password' => $curr['password']);
//		
//		$to = $curr['email'];
//		
//		$headers = array(	'From' => $from,
//											'To' => $to,
//											'Subject' => $subject);
//
//		$body = "	Thank you for joining PopBloop.\n\r
//							To activate your account, click this link: " . $_SESSION['basepath'] . 'user/guest/activate/' . $curr['activation_key'];
//
//		
//		$smtp = Mail::factory('smtp',
//			array('host' => $host,
//						'port' => $port,
//						'auth' => true,
//						'username' => $username,
//						'password' => $password));
//
//		$mail = $smtp->send($to, $headers, $body);
//
//		if (PEAR::isError($mail)) {
//			write_log(array('log_text' => $mail->getMessage()));
////			echo("<p>" . $mail->getMessage() . "</p>");
//			$send_fail[] = $to;
//		} else {
////			echo("<p>Message successfully sent!</p>");
//			$send_succeed[] = $to;
//		}
//		
//	}
//
//
//	return "FAIL: <pre>" . print_r($send_fail, true) . "</pre>" . "SUCCEED: <pre>" . print_r($send_succeed, true) . "</pre>";
//}



function user_guest_login($uname = NULL, $passwd = NULL){
  // bisa diakses lewat web dgn path: /user/user/login/[username/password]
  // atau dipanggil langsung dgn fungsi user_guest_login('username','password')
  $username = isset($uname) ? (string)$uname : (string)func_arg(0);
  $password = isset($passwd) ? (string)$passwd : (string)func_arg(1);
	
	if(!isset($username) || trim($username) == ''){
		$username = $_POST['username'];
	}
	
	if(!isset($password) || trim($password) == ''){
		$password = $_POST['password'];
	}
//	die("$username, $password");
  if(strlen(trim($username)) == 0 || strlen(trim($password)) == 0){
    unset($_SESSION['session_id']);
    unset($_SESSION['username']);
    unset($_SESSION['user_id']);
    return "0";
  }
	
	unset($_SESSION['pop_error_msg']);
	unset($_SESSION['pop_status_msg']);

  // connect to mongodb using default setting
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Users');

  $lilo_mongo->selectCollection('Account');
  $user_exists = $lilo_mongo->findOne(array('username' => $username, 'password' => md5($password)));

	// revisi 04072012: jika username tidak ditemukan, gunakan email
	if(!is_array($user_exists) || count($user_exists) == 0){
	  $user_exists = $lilo_mongo->findOne(array('email' => $username, 'password' => md5($password)));
		$username = $user_exists['username'];
	}
	
	
  $session_id = $_SESSION['session_id'];
  $session_username = $_SESSION['username'];
  
  // bagaimana jika $session_username berbeda dengan $username?
  
  $cur_date = date("Y-m-d H:i:s");
  if(is_array($user_exists) && count($user_exists)){
//    die("<pre>".print_r($user_exists, true)."</pre>");
    if(trim($session_id) == '' || trim($session_username) == ''){
      $user_id = (string)$user_exists['_id'] . '';
			
      // generate session_id
      $session_id = md5($cur_date . " - " . $username);
			
      $_SESSION['session_id'] = $session_id;
      $_SESSION['username'] = $username;
      $_SESSION['user_id'] = $user_id;
      
      // simpan di table session
      //    array(session_id, username, time_start, time_end)
			
			//	untuk mencegah user login di lebih dari satu browser, jika data session utk user_id sudah ada, maka lakukan hanya update
			//	lakukan insert hanya bila data session utk user_id belum ada
			
			$lilo_mongo->selectCollection('Session');
			$exist_user_id = $lilo_mongo->findOne(array('user_id' => $user_id));
			
			if(is_array($exist_user_id) && count($exist_user_id)){
				
//				$lilo_mongo->update_set(
//					array(
//						'user_id'     => $user_id,
//					),
//					array(
//						'session_id'  => $session_id,
////						'user_id'     => $user_id,
////						'username'    => $username,
////						'time_start'  => $cur_date,
////						'time_end'    => ''
//					)
//				);
				$lilo_mongo->selectCollection('SessionLog');
				$lilo_mongo->insert($exist_user_id);

				
			} 
//			else {
			$lilo_mongo->selectCollection('Session');
			$lilo_mongo->remove(array('user_id' => $user_id));
			$lilo_mongo->insert(
				array(
					'session_id'  => $session_id,
					'user_id'     => $user_id,
					'username'    => $username,
					'time_start'  => $cur_date,
					'time_end'    => '',
					'HTTP_USER_AGENT'	=> $_SERVER['HTTP_USER_AGENT'],
					'REMOTE_ADDR'	=> $_SERVER['REMOTE_ADDR'],
					'REMOTE_HOST'	=> $_SERVER['REMOTE_HOST'],
				)
			);
//			}

			report_admin_countuseronlinetime($user_id);
			
      
      return $session_id;
    } else {
			// sepertinya kondisi ini tidak akan terpenuhi... :P
			
      if($session_username == $username){
        // mencoba login, padahal sudah terdaftar...
        // biarkan saja, sepertinya orang ini ga ada kerjaan
        return $session_id;
      } else {
        // sudah terdaftar, kemudian mencoba login dengan username dan password yg lain
        // logout dulu, baru login dengan username yg baru

        //    update($array_criteria, $array_newobj, $array_options)
        $array_criteria = array('session_id' => $session_id);
        $array_newobj = array('$set' => array('time_end' => $cur_date));
        $array_options = array('upsert' => true);
        $lilo_mongo->selectCollection('Session');
        $lilo_mongo->update($array_criteria, $array_newobj, $array_options);
        
        $session_id = md5($cur_date . " - " . $username);
        $_SESSION['session_id'] = $session_id;
        $_SESSION['username'] = $username;

        // simpan di table session
        //    array(session_id, username, time_start, time_end)
        $lilo_mongo->selectCollection('Session');
        $lilo_mongo->insert(
          array(
            'session_id'  => $session_id,
            'username'    => $username,
            'time_start'  => $cur_date,
            'time_end'    => '',
						'HTTP_USER_AGENT'	=> $_SERVER['HTTP_USER_AGENT'],
						'REMOTE_ADDR'	=> $_SERVER['REMOTE_ADDR'],
						'REMOTE_HOST'	=> $_SERVER['REMOTE_HOST'],
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

// return 1 jika redundan
function user_guest_redundancy_check($check = NULL, $value = NULL){
	if(!isset($check)){
		$check = func_arg(0);
	}
	if(!isset($value)){
		$value = func_arg(1);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$criteria = array();
	switch($check){
		case 'email':
			$criteria = array('email' => $value);
			break;
		case 'username':
			$criteria = array('username' => $value);
			break;
	}
	
	$exists = $lilo_mongo->findOne($criteria);
	
	if($exists){
		return "1";
	}
	
	return "0";
}

// return 1 jika redundan
function user_guest_property_redundancy_check($check = NULL, $value = NULL){	// sama dengan /redundancy_check, tapi utk data di table Users.Properties: avatarname, twitter, handphone
	if(!isset($check)){
		$check = func_arg(0);
	}
	if(!isset($value)){
		$value = func_arg(1);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Properties');
	
	$criteria = array();
	switch($check){
		case 'avatarname':
			$criteria = array('avatarname' => $value);
			break;
		case 'twitter':
			$criteria = array('twitter' => $value);
			break;
		case 'handphone':
			$criteria = array('handphone' => $value);
			break;
	}
	
	$exists = $lilo_mongo->findOne($criteria);
	
	if($exists){
		return "1";
	}
	
	return "0";
}

// salah tempat, harusnya di user/user
// check session, jika belum terdaftar -> tendang
function user_guest_property(){
	$user_id = $_SESSION['user_id'];
	if(!isset($user_id)){
		header("Location: " . $_SESSION['basepath']);
		exit;
	}
	
	$username = func_arg(0);
	
	if(!isset($username)){
		$username = $_SESSION['username'];
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Users');

  $lilo_mongo->selectCollection('Account');

	$usernames = $lilo_mongo->command_values(array("distinct" => "Account", "key" => "username", "query" => array()));

	if(!in_array($username, $usernames)){
		return "HTTP/1.0 404 Not Found";
	}
	



	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	$template->session_id = user_user_sessionid();

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

//	$return = $template->render($template_file);
//	return $return;

	$user_property = user_user_property();
	$user_property = json_decode($user_property);
	
	// dapatkan umur dan gender
	$user_property->age = date("Y") - $user_property->birthday_yy;
	$user_property->sex = ucfirst($user_property->sex);
	
	$template->user_property = $user_property;

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	
	if($username == $_SESSION['username']){
		$template->middle = $template->render("modules/001_user_management/templates/user_guest_property.php");
	} else {
		
		$acc_prop = user_guest_accountproperties($username);
		$acc_prop['properties']['sex'] = ucfirst($acc_prop['properties']['sex']);
		$birthday_expl = explode('-', $acc_prop['properties']['birthday']);
		$acc_prop['properties']['age'] = date("Y") - (int)$birthday_expl[2];
		
		$template->account_properties = $acc_prop;
		$template->username = $username;
		$template->middle = $template->render("modules/001_user_management/templates/user_guest_property_other.php");
	}
	

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
}


function user_guest_accountproperties($username = NULL){
	// dari $username, dapatkan semua properti user ini di table: Users.Account, Users.Properties [Game.Achievement, Game.PlayerInventory]

	if(!isset($username)){
		$username = func_arg(0);
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Users');

  $lilo_mongo->selectCollection('Account');

	$criteria = array('username' => $username);
	$account = $lilo_mongo->findOne($criteria);
	
	if(!is_array($account) || !count($account)){
		return "ERROR";
	}
	
	unset($account['_id']);
	unset($account['password']);
	
  $lilo_mongo->selectCollection('Properties');

	$criteria = array('lilo_id' => $account['lilo_id']);
	$properties = $lilo_mongo->findOne($criteria);

	unset($properties['_id']);


  $lilo_mongo->selectDB('Game');
  $lilo_mongo->selectCollection('Achievement');
	
	$criteria = array('userid' => $account['lilo_id']);
	$achievement_ = $lilo_mongo->find($criteria);
	$achievement = array();
	while($curr = $achievement_->getNext()){
		$achievement[] = $curr;
	}

	$user_data = array('account' => $account, 'properties' => $properties, 'achievement' => $achievement);
	
	// dapatkan property lain: gender, age, last-status,
	// gender: dari Users.Avatar, field configuration, di tipe: gender dapatkan element-nya
	
	
	return $user_data;
	
	//return print_r($achievement, true);
	//return print_r($properties, true);
	//return print_r($account, true);
}


function user_guest_uploadprofilephoto(){
	// menerima post dan file dari unity
	// lanjutkan dengan proses cropping & resizing
	$session_id = $_SESSION['session_id'];
	if(!isset($session_id)){
		$session_id = $_POST['session_id'];
	}
	$user_id = user_user_session_to_user_id($session_id);
	
	if(trim($user_id) == ''){
		die("ERROR");
	}
	
	// tangani file upload
	if(isset($_FILES['picture'])){
		$uploaddir = 'user_generated_data/unity_profile_picture/' . $user_id . '/';
		if(!is_dir($uploaddir)){
			mkdir($uploaddir);
		}
		
		$uploadfile = $uploaddir . time() . '__' . basename($_FILES['picture']['name']);
		
		if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
			$profile_picture = time() . '__' . $_FILES['picture']['name'];
			return $profile_picture;
		}
	}

	return "ERROR";
}


// https://graph.facebook.com/USER_ID/photos

function user_guest_testupload(){
	$html = '<form method="post" enctype="multipart/form-data" action="'.$_SESSION['basepath'].'/user/guest/uploadprofilephoto">
					<input type="file" name="picture" id="picture" />
					<input type="submit" name="submit" value="Submit" />
					</form>';
	return $html;
}


//
//function user_guest_md5password(){
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
//		$lilo_mongo->update_set(array('username' => $ua['username']), array('password' => md5($ua['password'])));
//		echo "username: $username, password: " . md5($ua['password']) . "<br />";
//	}
//
//}
