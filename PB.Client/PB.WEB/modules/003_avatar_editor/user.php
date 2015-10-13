<?php
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');
include_once('modules/001_user_management/user.php');

function avatar_user_default(){
	return "What?";
}

function avatar_user_get_size($user_id = NULL){
	if(!isset($user_id)){
		$user_id = func_arg(0);
	}
	
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	
	$lilo_mongo->selectCollection('Avatar');

	$conf = $lilo_mongo->findOne(array('user_id' => $user_id));

	$size = $conf['size'];
	
	if(!isset($size) || trim($size) == ''){
		$conf = avatar_user_get_configuration();
		$conf = str_replace("'", '"', $conf);
		$conf = json_decode($conf);
		// var_dump($conf);
		//die("<pre>" . print_r($conf, true) . "</pre>");
		$size_ = '';
		foreach($conf as $c){
			if($c->tipe == 'Body'){
				$size_ = $c->element;
			}
		}
		
		if(strpos($size_, '_small') !== false){
			$size = 'small';
		}
		if(strpos($size_, '_medium') !== false){
			$size = 'medium';
		}
		if(strpos($size_, '_big') !== false){
			$size = 'big';
		}
		
//		$size = 'big';
	}
	
	return trim($size) != '' ? $size : 'medium';
}

function avatar_user_get_configuration($user_id = NULL){
//  return "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_a_01_01','lip':'lips_a_01_01'},{'tipe':'Hair','element':'male_hair_04','material':'male_hair_04_1'},{'tipe':'Body','element':'male_hoodie_medium','material':'male_hoodie_01_1'},{'tipe':'Pants','element':'male_pants_medium','material':'male_pants_4'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_2'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'}, {'tipe':'Skin','color':'1'}]";
//  return "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_a_01_01','lip':'lips_a_01_01'},{'tipe':'Hair','element':'male_hair_04','material':'male_hair_04_1'},{'tipe':'Body','element':'male_hoodie_medium','material':'male_hoodie_01_1'},{'tipe':'Pants','element':'female_pants_medium','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_2'},{'tipe':'Hand','element':'female_body_hand','material':'female_body'}, {'tipe':'Skin','color':'1'}]";

	if(!isset($user_id)){
		$user_id = func_arg(0);
	}
	
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	// jika dipanggil dari unity, parameter yg digunakan adalah session_id
//	$session_id = func_arg(0);
//	if(isset($session_id) && trim($session_id) != ''){
//		$lilo_mongo->selectCollection('Session');
//		$session_data = $lilo_mongo->findOne(array('session_id' => $session_id));
//		$user_id = $session_data['user_id'];
//	}

	$lilo_mongo->selectCollection('Avatar');

	$conf = $lilo_mongo->findOne(array('user_id' => $user_id));
	// write_log(array('log_text' => "UserID: $user_id \n\n" . print_r($conf, true)));
	if(!$conf || !is_array($conf) || !count($conf)){
		// berikan konfigurasi default
		$sex = avatar_user_get_gender();
		
		$lilo_mongo->selectDB('Assets');
		$lilo_mongo->selectCollection('DefaultAvatar');
		
		$default_avatar = $lilo_mongo->findOne(array('gender' => $sex, 'size' => 'medium'));
		
		return $default_avatar['configuration'];
		
		
	}

	$config = str_replace("'", '"', $conf['configuration']);
	
	$config_array = json_decode($config);
	
//	return print_r($config_array, true);
	for($idx = 0; $idx < count($config_array); $idx++){
		if($config_array[$idx]->element2 == 'undefined'){
			$config_array[$idx]->element2 = '';
		}
		if($config_array[$idx]->material2 == 'undefined'){
			$config_array[$idx]->material2 = '';
		}
	}

	return str_replace('"', "'", json_encode($config_array));


//	return print_r($conf['configuration'], true);
}

function avatar_user_set_configuration($avatar_conf = NULL, $size = NULL){
	if(!isset($avatar_conf)){
		$avatar_conf = $_REQUEST['avatar_conf'];
	}
	
	if(!isset($size)){
		$size = $_REQUEST['size'];
	}
	
	// bagaimana format avatar_conf?
	/*
		$avatar_conf = array(
			array('tipe' => "gender", 'element' => "male_base"),
			array('tipe' => 'Face','element' => 'male_face-1','material' => 'male_face-1'),
			array('tipe' => 'Hair','element' => 'male_hair-2','material' => 'male_hair-2_blond'),
			array('tipe' => 'Body','element' => 'male_top-2','material' => 'male_top-2_green'),
			array('tipe' => 'Pants','element' => 'male_pants-1','material' => 'male_pants-1_green'),
			array('tipe' => 'Shoes','element' => 'male_shoes-2','material' => 'male_shoes-2_red'),
		);
		
		var message = 
	"[
		{'tipe':'gender','element':'male_base'},
		{'tipe':'Face','element':'male_face-1','material':'male_face-1'},
		{'tipe':'Hair','element':'male_hair-2','material':'male_hair-2_blond'},
		{'tipe':'Body','element':'male_top-2','material':'male_top-2_green'},
		{'tipe':'Pants','element':'male_pants-1','material':'male_pants-1_green'},
		{'tipe':'Shoes','element':'male_shoes-2','material':'male_shoes-2_red'}]";
	*/
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Avatar');
	
	$insert_id = $lilo_mongo->update_set(	array('user_id' => $_SESSION[user_id]), 
																				array('user_id' => $_SESSION[user_id], 'configuration' => $avatar_conf, 'size' => $size), 
																				array('upsert' => true));
	
	return "1";

}




function avatar_user_animation_new($op = NULL){
	// tab 1: daftar animation yg dimiliki current user
	// tab 2: daftar animation yg bisa digunakan/dibeli
	// tampilkan hanya animation dengan gender sesuai player gender

	// table: Assets.Animation dan Users.Animation

  $op = isset($op) ? $op : func_arg(0);
  $op = strtolower(trim($op));  // read, add, delete

  $lilo_mongo = new LiloMongo();
	
	$curr_gender = avatar_user_get_gender();

	switch($op){
		case 'read_all':	// avatar/user/animation/read_all/[json]
			// dapatkan semua animasi di table Assets.Animation
			$lilo_mongo->selectDB('Assets');
			$lilo_mongo->selectCollection('Animation');
			
			$anim_cursor = $lilo_mongo->find(array('gender' => $curr_gender));
			$anim_array = array();
			while($curr = $anim_cursor->getNext()){
				$anim_array[] = array('lilo_id' => $curr['lilo_id'], 
															'name' => $curr['name'], 
															'description' => $curr['description'], 
															'gender' => $curr['gender'], 
															'permission' => $curr['permission'], 
															'animation_file' => $curr['animation_file'], 
															'preview_file' => $curr['preview_file']);
			}
			
			$rettype = func_arg(1);
			if(trim(strtolower($rettype)) == 'json'){
				$anim_array = json_encode($anim_array);
			}
			
			return $anim_array;

			break;
		case 'read_mine':
			// dapatkan semua animasi di table Users.Animation
			$lilo_mongo->selectDB('Users');
			$lilo_mongo->selectCollection('Animation');
			
			$user_id = $_SESSION['user_id'];
			$anims = $lilo_mongo->findOne(array('user_id' => $user_id));
			
			$rettype = func_arg(1);
			$anim_config_ = $anims['configuration'];
			
			$anim_config = array();

			// pastikan gender sesuai $curr_gender
			foreach($anim_config_ as $ac){
				$ac_expl = explode('@', $ac);
				
				$ac_ = $curr_gender . '@' . $ac_expl[1];
				$anim_file = 'bundles/animations/' . $ac_ . '.unity3d';

				if(file_exists($anim_file)){
					$anim_config[] = $ac_;
				}
			}
			
			if(trim(strtolower($rettype)) == 'json'){
				$anim_config = json_encode($anim_config);
			}

			return $anim_config;
			break;
		case 'add':	// avatar/user/animation/add/[animation_file]
								// animation_file: bye, walk, male@walk, female@bye
			// mengupdate table Users.Animation
			$animation_file = func_arg(1);
			
			if(trim($animation_file) == ''){
				return '0';
			}

			$at_exists = strpos($animation_file, '@');
			
			if($at_exists){
				$animation_file_expl = explode('@', $animation_file);
				$anim = $animation_file_expl[1];
				$anim = str_replace('.unity3d', '', $anim);
			} else {
				$anim = $animation_file;
			}

//			$anim
			
			$lilo_mongo->selectDB('Users');
			$lilo_mongo->selectCollection('Animation');
			
			$curr_config = avatar_user_animation_new('read_mine');

			$anim_file = 'bundles/animations/' . $curr_gender . '@' . $anim . '.unity3d';

			if(file_exists($anim_file)){
				
			}

		
			break;
		case 'delete':
			break;
	}

}


/**
 * menampilkan daftar animation yg dapat dipilih current user
 */
function avatar_user_animation(){
	if(!isset($_SESSION['session_id'])){
		return false;
	}
	
	// update
	if(isset($_REQUEST['update']) && isset($_REQUEST['anim_conf'])){
		avatar_user_set_animation($_REQUEST['anim_conf']);
	}
	
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	$template->session_id = $_SESSION['session_id'];
	
	// get all files from animations directory
	// ...that match with current user gender
	
	// revisi 02 Nov 2011: ambil data dari DB [Assets.Animation], jangan langsung filesystem
	
	$gender = avatar_user_get_gender();
	
	$available_anim = array();
	$anim_dir = 'bundles/animations';
	$anim_array = array();
	if ($handle = opendir($anim_dir)) {
		while (false !== ($file = readdir($handle))) {
			$file_expl = explode(".", $file);
			$is_unity3d = strtolower($file_expl[count($file_expl) - 1]) == 'unity3d';
			$file_replaced = str_replace('.unity3d', '', $file);
			
			$file_expl = explode('@', $file);
			$gender_match = strtolower($gender) == strtolower($file_expl[0]);
			
			if ($is_unity3d && $gender_match) {
				$anim_array[] = $file_replaced;
			}
		}
		
		closedir($handle);
	}

	$anim_array = is_array($anim_array) ? $anim_array : json_decode($anim_array);
	$template->anim_array = $anim_array;
	
	// get animation configuration for current user
	$anim_conf = avatar_user_get_animation(NULL, 'json');
	$anim_conf = is_array($anim_conf) ? $anim_conf : json_decode($anim_conf);//json_decode(str_replace("'", '"', json_encode($anim_conf)));
	$template->anim_conf = $anim_conf;

	$return = $template->render("modules/003_avatar_editor/templates/avatar_user_animation.php");
	return $return;	

//	switch(json_last_error())
//	{
//		case JSON_ERROR_DEPTH:
//			$woi = ' - Maximum stack depth exceeded';
//			break;
//		case JSON_ERROR_CTRL_CHAR:
//			$woi = ' - Unexpected control character found';
//			break;
//		case JSON_ERROR_SYNTAX:
//			$woi = ' - Syntax error, malformed JSON';
//			break;
//		case JSON_ERROR_NONE:
//			$woi = ' - No errors';
//			break;
//	}
//
//	
//	return $return . "<br />woi: $woi";
}

function avatar_user_set_animation($animation_conf = NULL){
	if(!isset($animation_conf)){
		$animation_conf = $_REQUEST['animation_conf'];
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Animation');
	
	$insert_id = $lilo_mongo->update(	array('user_id' => $_SESSION[user_id]), 
																		array('user_id' => $_SESSION[user_id], 'configuration' => $animation_conf), 
																		array('upsert' => true));
	
	if(isset($_REQUEST['redirect_url'])){
		$_SESSION['pop_status_msg'][] = "Configuration saved.";
		header("Location: " . $_SESSION['basepath'] . $_REQUEST['redirect_url']);
		exit;
	}
	
	if($insert_id){
		return "1";
	}
	
	return 0;
}

function avatar_user_get_animation($user_id = NULL, $option = NULL){
	// dummy
//	$anim_array = array('item_pants', 'walkin', 'idle1', 'walk');
//	
//	if($option == 'json'){
//		return json_encode($anim_array);
//	}
//	
//	return str_replace('"', "'", json_encode($anim_array));
	// dummy
	
	// revisi 111011: animation disesuaikan dgn gender yg dipilih user di avatar editor
	//	atau...
	//	saat ubah gender di avatar editor, langsung ubah Users.Animation?
	
	if(!isset($user_id)){
		$user_id = func_arg(0);
	}
	
	if(!isset($user_id)){
		$session_id = $_SESSION['session_id'];
		$user_id = avatar_user_session_to_user_id($session_id);
	}
	
	// berdasar $user_id, dapatkan gender. defaultnya 'male'
	$gender = avatar_user_get_gender($user_id);
	
	// dapatkan konfigurasi animation utk current user
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Animation');

	$conf = $lilo_mongo->findOne(array('user_id' => $user_id));
	
	if(is_array($conf)){
		$config = array();
		$log = '';
		
		// cek dulu, apakah $conf['configuration'] sesuai gender di avatar dan sesuai asset di bundles/animations/
		for($idx = 0; $idx < count($conf['configuration']); $idx++){
			// format: [gender] [@] [animation_name]
			$anim_expl = explode('@', $conf['configuration'][$idx]);
			$conf['configuration'][$idx] = $gender . '@' . $anim_expl[1];
			
			$anim_file_exists = file_exists($_SESSION['basedir'] . '/bundles/animations/' . $conf['configuration'][$idx] . '.unity3d');
			$log .= '<br />' . $conf['configuration'][$idx] . '.unity3d' . ($anim_file_exists ? ' ada' : ' <b>ga</b> ada');
			
			if($anim_file_exists){
				$config[] = $conf['configuration'][$idx];
			}
		}
	
		return json_encode($config);
	} else {
		$config = array($gender.'@bye', $gender.'@happy', $gender.'@idle1', $gender.'@idle2', $gender.'@jump', $gender.'@pickup', $gender.'@run', $gender.'@walk');
		return json_encode($config);
	}
	
	
//	if($option == 'json'){
		//return json_encode($conf['configuration']);
//	}
	
//	return print_r($conf['configuration'], true);
}

function avatar_user_get_gender($user_id = NULL){
	if(!isset($user_id)){
		$user_id = $_SESSION['user_id'];
	}
	
	if(!isset($user_id)){
		$user_id = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

	$lilo_mongo->selectCollection('Avatar');

	$conf = $lilo_mongo->findOne(array('user_id' => $user_id));
	
	$conf_ = json_decode(str_replace("'", '"', $conf['configuration']));
	
	$gender = NULL;
	if(is_array($conf_)){
		foreach($conf_ as $c){
			if($c->tipe == 'gender'){
				$gender = $c->element;
			}
		}
	}
	
	if(!isset($gender)){
		// dapatkan dari Users.Properties.sex
		$lilo_mongo->selectCollection('Properties');
		
		$properties = $lilo_mongo->findOne(array('lilo_id' => $user_id));
		
		if(!isset($properties['sex'])){
			return 'male';
		}
		
		return $properties['sex'];
	}
	
	return str_replace("_base", "", $gender);
}


// menampilkan detail part avatar item
// digunakan di ui/user/avatar_editor
function avatar_user_avatar_category(){
	// die("<pre>" . print_r($_POST, true) . "</pre>");
	/*
	Array
	(
			[tipe] => body
			[gender] => male
			[category] => T-Shirt
			[size] => fat
	)
	*/
	
	extract($_POST);
	
	if(trim($tipe) == ''){
		die("Error - Incomplete argument");
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');


	$var_name = $tipe . "_array";
	$avatar_array = array();
//		$avatar_cursor = $lilo_mongo->find(array('tipe' => $tipe_));
	
	$avatar_cursor = $lilo_mongo->find(array('tipe' => $tipe, 'gender' => $gender, 'category' => $category, 'size' => $size), 0, array('gender' => 1, 'name' => 1, 'category' => 1));

	
	while($curr = $avatar_cursor->getNext()){
		$avatar_array[] = array('id' => $curr['lilo_id'], 
														'gender' => $curr['gender'], 
														'size' => $curr['size'], 
														'color' => $curr['color'], 
														'tipe' => $curr['tipe'], 
														'name' => $curr['name'], 
														'element' => $curr['element'],
														'material' => $curr['material'],
														'preview_image' => $curr['preview_image']);
	}


	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;

	$template->avatar_array = $avatar_array;

	$template->tipe = $tipe;

	$template->background_image = str_replace('"', "'", $background_image);

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

	$return = $template->render("modules/003_avatar_editor/templates/avatar_user_avatar_category.php");

	return $return;

}

/////////////////////
// HELPER FUNCTION //
/////////////////////
function avatar_user_session_to_user_id($session_id = NULL){
	if(!isset($session_id)){
		$session_id = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Session');
	
	$array_criteria = array('session_id' => $session_id);
	$result_array = $lilo_mongo->findOne($array_criteria);
	
	return $result_array['user_id'];
}

?>