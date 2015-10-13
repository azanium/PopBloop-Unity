<?php
//

require_once("modules/003_avatar_editor/user.php");


function avatar_guest_get_configuration($user_id = NULL){
//  return "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_a_01_01','lip':'lips_a_01_01'},{'tipe':'Hair','element':'male_hair_04','material':'male_hair_04_1'},{'tipe':'Body','element':'male_hoodie_medium','material':'male_hoodie_01_1'},{'tipe':'Pants','element':'male_pants_medium','material':'male_pants_4'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_2'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'}, {'tipe':'Skin','color':'1'}]";
//  return "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_a_01_01','lip':'lips_a_01_01'},{'tipe':'Hair','element':'male_hair_04','material':'male_hair_04_1'},{'tipe':'Body','element':'male_hoodie_medium','material':'male_hoodie_01_1'},{'tipe':'Pants','element':'female_pants_medium','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_2'},{'tipe':'Hand','element':'female_body_hand','material':'female_body'}, {'tipe':'Skin','color':'1'}]";

	if(!isset($user_id)){
		$user_id = func_arg(0);
	}
	
	// if(!isset($user_id)){
		// $user_id = $_SESSION['user_id'];
	// }
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');

  if(!isset($user_id )){
	// jika dipanggil dari unity, parameter yg digunakan adalah session_id
	$session_id = func_arg(0);
	if(isset($session_id) && trim($session_id) != ''){
		$lilo_mongo->selectCollection('Session');
		$session_data = $lilo_mongo->findOne(array('session_id' => $session_id));
		$user_id = $session_data['user_id'];
	}
  }

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

// ini code sebelum copy dari user.php
// function avatar_guest_get_configuration($user_id = NULL){	// dipanggil unity player
// //  return "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_a_01_01','lip':'lips_a_01_01'},{'tipe':'Hair','element':'male_hair_04','material':'male_hair_04_1'},{'tipe':'Body','element':'male_hoodie_medium','material':'male_hoodie_01_1'},{'tipe':'Pants','element':'male_pants_medium','material':'male_pants_4'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_2'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'}, {'tipe':'Skin','color':'1'}]";
// //  return "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows_01_01','eyes':'eyes_a_01_01','lip':'lips_a_01_01'},{'tipe':'Hair','element':'male_hair_04','material':'male_hair_04_1'},{'tipe':'Body','element':'male_hoodie_medium','material':'male_hoodie_01_1'},{'tipe':'Pants','element':'female_pants_medium','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_2'},{'tipe':'Hand','element':'female_body_hand','material':'female_body'}, {'tipe':'Skin','color':'1'}]";

	// if(!isset($user_id)){
		// $user_id = func_arg(0);
	// }
	
	// if(!isset($user_id)){
		// $user_id = $_SESSION['user_id'];
	// }
	
	// $lilo_mongo = new LiloMongo();
	// $lilo_mongo->selectDB('Users');

	// $lilo_mongo->selectCollection('Avatar');

	// $conf = $lilo_mongo->findOne(array('user_id' => $user_id));

	// if(!$conf){
		// // berikan konfigurasi default
		// $sex = avatar_user_get_gender();
		
		// $lilo_mongo->selectDB('Assets');
		// $lilo_mongo->selectCollection('DefaultAvatar');
		
		// $default_avatar = $lilo_mongo->findOne(array('gender' => $sex));
		
		// return $default_avatar['configuration'];
		
	// }

	// $config = str_replace("'", '"', $conf['configuration']);
	
	// $config_array = json_decode($config);
	
	// for($idx = 0; $idx < count($config_array); $idx++){
		// if($config_array[$idx]->element2 == 'undefined'){
			// $config_array[$idx]->element2 = '';
		// }
		// if($config_array[$idx]->material2 == 'undefined'){
			// $config_array[$idx]->material2 = '';
		// }
	// }

	// return str_replace('"', "'", json_encode($config_array));

// }
//function avatar_guest_get_configuration($user_id = NULL){	// GA KEPAKE
////	if(!isset($user_id)){
////		$user_id = func_arg(0);
////	}
////	
////	if(!isset($user_id)){
////		$user_id = $_SESSION['user_id'];
////	}
////	
//	$lilo_mongo = new LiloMongo();
//	$lilo_mongo->selectDB('Users');
//
//	// jika dipanggil dari unity, parameter yg digunakan adalah session_id
////	$session_id = func_arg(0);
////	if(isset($session_id) && trim($session_id) != ''){
////		$lilo_mongo->selectCollection('Session');
////		$session_data = $lilo_mongo->findOne(array('session_id' => $session_id));
////		$user_id = $session_data['user_id'];
////	}
//
//	if(!isset($user_id)){
//		$user_id = func_arg(0);
//	}
//
//	$lilo_mongo->selectCollection('Avatar');
//
//	$conf = $lilo_mongo->findOne(array('user_id' => $user_id));
//
//	if(!$conf){
//		// berikan konfigurasi default
//		$sex = avatar_guest_get_gender();
//		if($sex == "female"){
//			return "[{'tipe':'gender','element':'female_base'},
//							{'tipe':'Face','element':'female_head','material':'','eye_brows':'female_head_broweyes_01','eyes':'female_head_eyes_01','lip':'female_head_lips_01'},
//							{'tipe':'Hair','element':'female_hair2','material':'female_hair_02_2','element2':'','material2':''},
//							{'tipe':'Body','element':'female_t-shirt_medium','material':'female_t-shirt_03'},{'tipe':'Pants','element':'female_pants_medium','material':'female_pants_01'},
//							{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_4'},
//							{'tipe':'Hand','element':'female_body_hand','material':'female_body'},
//							{'tipe':'Skin','color':'1'}]";
//		} else {
//			return "[{'tipe':'gender','element':'male_base'},
//							{'tipe':'Face','element':'male_head','material':'','eye_brows':'male_head_broweyes_01','eyes':'male_head_eyes_01','lip':'male_head_lips_01'},
//							{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02','element2':'','material2':''},
//							{'tipe':'Body','element':'male_t-shirt_medium','material':'male_t-shirt_1'},{'tipe':'Pants','element':'male_pants_medium','material':'male_pants_2'},
//							{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_3'},
//							{'tipe':'Hand','element':'male_body_hand','material':'male_body'},
//							{'tipe':'Skin','color':'1'}]";
//		}
//		
//
////		return "[{'tipe':'gender','element':'male_base'},
////						{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},
////						{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},
////						{'tipe':'Body','element':'male_body_fat','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_fat','material':'male_short_pant'},
////						{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},
////						{'tipe':'Hand','element':'male_body_hand','material':'male_body'},
////						{'tipe':'Skin','color':'1'}]";
////
//		
//		//return "[{'tipe':'gender','element':'male_base'},
//		//				{'tipe':'Face','element':'male_face-1','material':'male_face-1','eye_brows':'brows','eyes':'eyes','lip':'lip'},
//		//				{'tipe':'Hair','element':'male_hair-2','material':'male_hair-2_blond','element2':'male_hair_1_bottom','material2':''},
//		//				{'tipe':'Body','element':'male_top-2','material':'male_top-2_green'},
//		//				{'tipe':'Hand','element':'male_body_hand','material':''},
//		//				{'tipe':'Pants','element':'male_pants-1','material':'male_pants-1_green'},
//		//				{'tipe':'Shoes','element':'male_shoes-2','material':'male_shoes-2_red'}]";
//	}
//
//	$config = str_replace("'", '"', $conf['configuration']);
//	
//	$config_array = json_decode($config);
//	
////	return print_r($config_array, true);
//	for($idx = 0; $idx < count($config_array); $idx++){
//		if($config_array[$idx]->element2 == 'undefined'){
//			$config_array[$idx]->element2 = '';
//		}
//		if($config_array[$idx]->material2 == 'undefined'){
//			$config_array[$idx]->material2 = '';
//		}
//	}
//
//	return str_replace('"', "'", json_encode($config_array));
//
//
////	return print_r($conf['configuration'], true);
//}

function avatar_guest_get_editor_animation($gender = NULL) {
	if(!isset($gender)){
		$gender = func_arg(0);
	}
	
	// dapatkan konfigurasi animation utk current user
	$lilo_mongo = new LiloMongo();
  
    // dapatkan semua di Assets.Animation yg permission-nya 'default'
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Animation');
	
    $anim_ = $lilo_mongo->command_values(array("distinct" => "Animation", "key" => "animation_file", "query" => array("gender" => $gender, "permission" => "default")));

	$default_anims = array();
	foreach($anim_ as $a){
		$a = str_replace(".unity3d", "", $a);
			$config[] = $a;
		
	}
  
	return json_encode($config);

//	return print_r($conf['configuration'], true);
}


/* ORIGINAL MUKHTAR's CODES
function avatar_guest_get_editor_animation($gender = NULL){
//	if(!isset($user_id)){
//		$user_id = func_arg(1);
//	}
//	
//	if(!isset($user_id)){
//		$session_id = $_SESSION['session_id'];
//		$user_id = avatar_guest_session_to_user_id($session_id);
//	}

	if(!isset($gender)){
		$gender = func_arg(0);
	}
	
//	if(!isset($gender)){
//		// berdasar $user_id, dapatkan gender. defaultnya 'male'
//		$gender = avatar_guest_get_gender($user_id);
//	}
	

	$config = array($gender.'@bye', $gender.'@idle1');
	return json_encode($config);
}*/


function avatar_guest_get_animation($user_id = NULL, $option = NULL){
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
		$user_id = avatar_guest_session_to_user_id($session_id);
	}
	
	// berdasar $user_id, dapatkan gender. defaultnya 'male'
	$gender = avatar_guest_get_gender($user_id);
	
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
	
	} else {
		$config = array($gender.'@bye', $gender.'@happy', $gender.'@idle1', $gender.'@idle2', $gender.'@jump', $gender.'@pickup', $gender.'@run', $gender.'@walk');
	}
  
  // dapatkan semua di Assets.Animation yg permission-nya 'default'
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Animation');
	
  $anim_ = $lilo_mongo->command_values(array("distinct" => "Animation", "key" => "animation_file", "query" => array("gender" => $gender, "permission" => "default")));

  $default_anims = array();
  foreach($anim_ as $a){
    $a = str_replace(".unity3d", "", $a);
    if(!in_array($a, $config)){
      $config[] = $a;
    }

  }
  
  return json_encode($config);
	
//	if($option == 'json'){
		//return json_encode($conf['configuration']);
//	}
	
//	return print_r($conf['configuration'], true);
}


/////////////////////
// HELPER FUNCTION //
/////////////////////
function avatar_guest_session_to_user_id($session_id = NULL){
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


function avatar_guest_get_gender($user_id = NULL){
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
	
	$gender = 'male';
	if(is_array($conf_)){
		foreach($conf_ as $c){
			if($c->tipe == 'gender'){
				$gender = $c->element;
			}
		}
	}
	
	return str_replace("_base", "", $gender);
}
