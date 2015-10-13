<?php

/**
 * halaman default
 * write your docs here dude...
 */
include_once('libraries/Template.php');

include_once('modules/001_user_management/user.php');

function ui_user_default($template_file = NULL, $html_string = NULL){
	$basepath = $_SESSION['basepath'];

	$template = new Template();

	$logged_in = user_user_loggedin();
	$template->logged_in = $logged_in;
	
	// Deteksi User Agent

	$template->basepath = $basepath;

	if($logged_in){
		
		// definisikan content untuk header, footer, left, center dan right
		if(isset($template_file) && is_file($template_file)){
			$middle = $template->render($template_file);	
		} else if (isset($html_string)){
			$middle = $html_string;
		} else {
			// defaultnya tampilin apa bro?
			$middle = $template->render("modules/000_user_interface/templates/ui_user_logged_in.php");	
		}
		
	} else {
		// definisikan content untuk header, footer, left, center dan right
//		$middle = file_get_contents($basepath . "user/user/login_form");
		$middle = $template->render("modules/000_user_interface/templates/ui_user_not_logged_in.php");
	}

	$template->middle = $middle;
	$template->username = $_SESSION['username'];

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;

	// komponen2 template lain
	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	
	return $return;
}

function ui_user_avatar_editor_old(){
	$ajax = func_arg(0);
	
//	$template_file = "modules/000_user_interface/templates/ui_user_avatar_editor.php";
	$template_file = "modules/000_user_interface/templates/ui_user_avatar_editor_tabbed.php";
	
	if($ajax == 'ajax'){
		$basepath = $_SESSION['basepath'];
		$template = new Template();
		$logged_in = user_user_loggedin();

		// Deteksi User Agent

		$template->logged_in = $logged_in;
		$template->basepath = $basepath;

		$return = $template->render($template_file);
		return $return;

	} else {
		return ui_user_default($template_file);
	}
}

function ui_user_avatar_editor(){
	$ajax = func_arg(0);
	
//	$template_file = "modules/000_user_interface/templates/ui_user_avatar_editor.php";
	$template_file = "modules/000_user_interface/templates/ui_user_avatar_editor_new.php";
//	$template_file = "modules/000_user_interface/templates/ui_user_avatar_editor_categorized.php";
	
	
//	if($ajax == 'ajax'){
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	// Deteksi User Agent

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	
	// dapatkan semua bagian avatar berdasar tipe
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');

	$avatar_array = array();
	$tipe_array = array('face', 'face_part_eye_brows', 'face_part_eyes', 'face_part_lip', 'hair', 'pants', 'shoes', 'body', 'top', 'hand', 'hat');

	foreach($tipe_array as $tipe_){
		$var_name = $tipe_ . "_array";
		${$var_name} = array();
//		$avatar_cursor = $lilo_mongo->find(array('tipe' => $tipe_));
		
    $avatar_cursor = $lilo_mongo->find(array('tipe' => $tipe_), 0, array('gender' => 1, 'name' => 1, 'category' => 1));

		
		while($curr = $avatar_cursor->getNext()){
			${$var_name}[] = array(	'id' => $curr['lilo_id'], 
															'gender' => $curr['gender'], 
															'size' => $curr['size'], 
															'tipe' => $curr['tipe'], 
															'name' => $curr['name'], 
															'element' => $curr['element'],
															'material' => $curr['material'],
															'element2' => $curr['element_2'],
															'material2' => $curr['material_2'],
															'preview_image' => $curr['preview_image']);
		}
	
		$avatar_array[$tipe_] = ${$var_name};
	}
	
	$template->avatar_array = $avatar_array;

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

//	$return = $template->render($template_file);
//	return $return;

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$template->middle = $template->render($template_file);

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;



//	} else {
//		return ui_user_default($template_file);
//	}
}

function ui_user_is_admin(){
	
	// user_id
	$user_id = $_SESSION['user_id'];
	// die($user_id);
	
	// dapatkan lilo_id utk Users.Group.name == 'admin'
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Group');
	
	$criteria = array('name' => 'admin');
	$group = $lilo_mongo->findOne($criteria);
	// die("Lilo_ID: " . $group['lilo_id']);
	
	$lilo_mongo->selectCollection('GroupMember');
	
	$criteria = array('user_id' => $user_id, 'group_id' => $group['lilo_id']);
	$member = $lilo_mongo->findOne($criteria);
	
	if(count($member) > 0){
		return 1;
	}
	
	return 0;
}

function ui_user_avatar_editor_categorized(){
	$ajax = func_arg(0);
	
	$template_file = "modules/000_user_interface/templates/ui_user_avatar_editor_categorized.php";
	
	
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	// Deteksi User Agent

	// cek apakah user ini admin
	
	$is_admin = ui_user_is_admin();
	
	$template->logged_in = $logged_in;
	$template->is_admin = $is_admin;
	$template->basepath = $basepath;
	
	// dapatkan semua bagian avatar berdasar tipe
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');

	$avatar_array = array();
	// $tipe_array = array('face', 'face_part_eye_brows', 'face_part_eyes', 'face_part_lip', 'hair', 'pants', 'shoes', 'body', 'top', 'hand', 'hat');
	// face, hand tdk perlu diproses, karena tidak ditampilkan ke user
	
	$color_palette = array('face_part_eye_brows', 'face_part_eyes', 'face_part_lip', 'hair');
	$texture = array('pants', 'shoes', 'body', 'top', 'hat');
	
	$type_array = array_merge($color_palette, $texture);
	
	foreach($type_array as $tipe_){
		$var_name = $tipe_ . "_array";
		${$var_name} = array();
		
		$lilo_mongo->selectCollection('Avatar');
    $avatar_ = $lilo_mongo->command_values(array("distinct" => "Avatar", "key" => "category", "query" => array('tipe' => $tipe_)));
		foreach($avatar_ as $a_){
			// dapatkan semua category + preview_picture + gender apa saja yg menggunakan category ini
			$gender_ = $lilo_mongo->command_values(array("distinct" => "Avatar", "key" => "gender", "query" => array('category' => $a_, 'tipe' => $tipe_)));
			foreach($gender_ as $g_){
				$size_ = $lilo_mongo->command_values(array("distinct" => "Avatar", "key" => "size", "query" => array('category' => $a_, 'tipe' => $tipe_, /*'gender' => array('in' => $gender_)*/ 'gender' => $g_)));
				
				foreach($size_ as $s_){
					$prev_img_ = $lilo_mongo->findOne(array('preview_image' => array('$nin' => array('', false, 0)), 'gender' => $g_, 'category' => $a_, 'tipe' => $tipe_, 'gender' => $g_), array('preview_image'));
					${$var_name}[] = array('category' => $a_, 'preview_image' => $prev_img_['preview_image'], 'tipe' => $tipe_, 'gender' => $g_, 'size' => $s_, 'name' => $a_);
				}
				
			}
		}
		
		$avatar_array[$tipe_] = ${$var_name};
	}
	
	
	// dapatkan default avatar configuration utk [male x female] x [big x medium x small]
	require_once('modules/003_avatar_editor/admin.php');
	$gender_array = array('male', 'female');
	$size_array = array('big', 'medium', 'small');
	
	foreach($gender_array as $gender){
		foreach($size_array as $size){
			${'avatar_config_' . $gender . '_' . $size} = avatar_admin_get_default_configuration($gender, $size);
			$template->{'avatar_config_' . $gender . '_' . $size} = ${'avatar_config_' . $gender . '_' . $size};
		}
	}

	$template->session_id = $_SESSION['session_id'];
	$template->avatar_array = $avatar_array;

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$template->middle = $template->render($template_file);

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
}

function ui_user_avatar_editor_branditems(){
	$ajax = func_arg(0);
	
	$template_file = "modules/000_user_interface/templates/ui_user_avatar_editor_branditems.php";
	
	
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	// Deteksi User Agent

	// cek apakah user ini admin
	
	$is_admin = ui_user_is_admin();
	
	$template->logged_in = $logged_in;
	$template->is_admin = $is_admin;
	$template->basepath = $basepath;
	
	// dapatkan semua bagian avatar berdasar tipe
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');

	$avatar_array = array();
	// $tipe_array = array('face', 'face_part_eye_brows', 'face_part_eyes', 'face_part_lip', 'hair', 'pants', 'shoes', 'body', 'top', 'hand', 'hat');
	// face, hand tdk perlu diproses, karena tidak ditampilkan ke user
	
	$color_palette = array('face_part_eye_brows', 'face_part_eyes', 'face_part_lip', 'hair');
	$texture = array('pants', 'shoes', 'body', 'top', 'hat');
	
	$type_array = array_merge($color_palette, $texture);
	
	foreach($type_array as $tipe_){
		$var_name = $tipe_ . "_array";
		${$var_name} = array();
		
		$lilo_mongo->selectCollection('Avatar');
    $avatar_ = $lilo_mongo->command_values(array("distinct" => "Avatar", "key" => "category", "query" => array('tipe' => $tipe_)));
		foreach($avatar_ as $a_){
			// dapatkan semua category + preview_picture + gender apa saja yg menggunakan category ini
			$gender_ = $lilo_mongo->command_values(array("distinct" => "Avatar", "key" => "gender", "query" => array('category' => $a_, 'tipe' => $tipe_)));
			foreach($gender_ as $g_){
				$size_ = $lilo_mongo->command_values(array("distinct" => "Avatar", "key" => "size", "query" => array('category' => $a_, 'tipe' => $tipe_, /*'gender' => array('in' => $gender_)*/ 'gender' => $g_)));
				
				foreach($size_ as $s_){
					$prev_img_ = $lilo_mongo->findOne(array('preview_image' => array('$nin' => array('', false, 0)), 'gender' => $g_, 'category' => $a_, 'tipe' => $tipe_, 'gender' => $g_), array('preview_image'));
					${$var_name}[] = array('category' => $a_, 'preview_image' => $prev_img_['preview_image'], 'tipe' => $tipe_, 'gender' => $g_, 'size' => $s_, 'name' => $a_);
				}
				
			}
		}
		
		$avatar_array[$tipe_] = ${$var_name};
	}
	
	
	// dapatkan default avatar configuration utk [male x female] x [big x medium x small]
	require_once('modules/003_avatar_editor/admin.php');
	$gender_array = array('male', 'female');
	$size_array = array('big', 'medium', 'small');
	
	foreach($gender_array as $gender){
		foreach($size_array as $size){
			${'avatar_config_' . $gender . '_' . $size} = avatar_admin_get_default_configuration($gender, $size);
			$template->{'avatar_config_' . $gender . '_' . $size} = ${'avatar_config_' . $gender . '_' . $size};
		}
	}

	$template->session_id = $_SESSION['session_id'];
	$template->avatar_array = $avatar_array;

	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$template->middle = $template->render($template_file);

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
}


function ui_user_user_agent($option = NULL){	// option: ...
	$u_agent =  $_SERVER['HTTP_USER_AGENT'];
	$browser = get_browser(null, true);

	$ret = "";

	switch($option){
		case "is_mozilla":
			$ret = preg_match('/Firefox/i',$u_agent) ? 1 : 0;
			break;
		case "is_ie":
			$ret = preg_match('/MSIE/i',$u_agent) ? 1 : 0;
			break;
		case "ismobiledevice":
			$ret = $browser['ismobiledevice'];
			break;
	}

	return $ret;
}

function ui_user_play(){
//	$ajax = func_arg(0);
//	if($ajax == 'ajax'){
//		$basepath = $_SESSION['basepath'];
//		$template = new Template();
//		$logged_in = user_user_loggedin();
//
//		// Deteksi User Agent
//
//		$template->logged_in = $logged_in;
//		$template->basepath = $basepath;
//		$template->session_id = user_user_sessionid();
//
//		$return = $template->render("modules/000_user_interface/templates/ui_user_play.php");
//		return $return;
//
//	} else {
//		$template_file = "modules/000_user_interface/templates/ui_user_play.php";
//		return ui_user_default($template_file);
//	}

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
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$template->middle = $template->render("modules/000_user_interface/templates/ui_user_play.php");

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;


}

function ui_user_quest(){
	return "Quest...";	
}

function ui_user_quiz(){
	return "Quiz...";	
}

function ui_user_statistics(){
	return "&nbsp;";	
}

// menerima dan menyimpan konfigurasi display di session
function ui_user_displayconfig($key = NULL, $val = NULL){
	$key = isset($key) ? $key : $_REQUEST['key'];
	$val = isset($val) ? $val : $_REQUEST['val'];
	
	$_SESSION['display_config_' . $key] = $val;
	
	return '1';// . "key: $key, val: $val";
}

function ui_user_getdisplayconfig($key = NULL){
	$key = isset($key) ? $key : $_REQUEST['key'];
	
	return $_SESSION['display_config_' . $key];
}

function ui_user_getalldisplayconfig(){
	$ret = '';
	foreach($_SESSION as $key => $val){
		if(substr($key, 0, 15) == 'display_config_'){
			$ret .= "key: $key, val: $val" . "<br />";
		}
	}
	return $ret;
}


// load template yg ada di modules\000_user_interface\templates_microsite
function ui_user_microsite($file_to_load = NULL){
	if(!isset($file_to_load)){
		$file_to_load = func_arg(0);
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
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;


	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$template->middle = $template->render("modules/000_user_interface/templates_microsite/" . $file_to_load . ".php");

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;

}

function ui_user_photoalbums(){
	// menampilkan semua image yg ada di user_generated_data\unity_profile_picture\[user_id]
	global $basepath;
	
	$user_id = $_SESSION['user_id'];
	$pic_folder = 'user_generated_data/unity_profile_picture/' . $user_id;
	
	if(!is_dir($pic_folder)){
		mkdir($pic_folder);
	}
	
	$html = "<table align='center' style='width:100%; border:0;'>";
	
	if ($handle = opendir($pic_folder)) {
//    echo "Directory handle: $handle\n";
//    echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..'){
				// echo "$entry<br />";
				$html .= "<tr>";
				$html .= "<td><img src='" . $_SESSION['basepath'] . $pic_folder . '/' . $entry . "' /></td>";
				$html .= "<td style='vertical-align:middle; text-align:center;'><a href='".$basepath."ui/user/uploadphototofacebook/".$entry."'>Upload to Facebook</a></td>";
				$html .= "</tr>";
			}
    }
		
    closedir($handle);
	}
	$html .= "</table>";
	die($html);
}

function ui_user_uploadphototofacebook($filename = NULL){
	global $facebook;
	global $basedir;
	global $basepath;
	if(!isset($filename)){
		$filename = func_arg(0);
	}
	
	$facebook->setFileUploadSupport(true);
	
//	die("<pre>" . print_r($basedir, true) . "</pre>" . "<br /><pre>" . print_r($facebook, true) . "</pre>");

	$user_id = $_SESSION['user_id'];
	$pic_file = 'user_generated_data/unity_profile_picture/' . $user_id . '/' . $filename;
	
	
	
	$args = array('message' => 'Hei, I am on PopBloop!');
	$args['image'] = '@' . realpath($pic_file);
//	die("<pre>" . print_r($args, true) . "</pre>");
	$data = $facebook->api('/me/photos', 'post', $args);

	header("Location: " . $basepath . 'ui/user/photoalbums');
	exit;
}


function ui_user_new_avatar_name(){
	$avatar_name = func_arg(0);
	
	$avatar_name = htmlspecialchars($avatar_name);
	
	// update properties avatarname utk lilo_id = $user_id
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Properties');

	$lilo_mongo->update_set(array('lilo_id' => $_SESSION['user_id']), array('avatarname' => $avatar_name));
	
	$_SESSION['avatarname'] = $avatar_name;
	
	header('Location: ' . $_SESSION['basepath']);
	exit;
}

?>