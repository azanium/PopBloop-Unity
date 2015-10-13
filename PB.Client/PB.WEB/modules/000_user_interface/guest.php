<?php

/**
 * halaman default
 * write your docs here dude...
 */
include_once('libraries/Template.php');

include_once('modules/001_user_management/user.php');

function ui_guest_default($template_file = NULL, $html_string = NULL){
	$basepath = $_SESSION['basepath'];

	$template = new Template();

	$logged_in = user_user_loggedin();
	$template->logged_in = $logged_in;
	
	// Deteksi User Agent

	$template->basepath = $basepath;

	if($logged_in){
		header('Location: ' . $basepath . 'ui/user/avatar_editor_categorized');
		exit;
		
		// definisikan content untuk header, footer, left, center dan right
		if(isset($template_file) && is_file($template_file)){
			$middle = $template->render($template_file);	
		} else if (isset($html_string)){
			$middle = $html_string;
		} else {
			// defaultnya tampilin apa bro?
//			$middle = $template->render("modules/000_user_interface/templates/ui_user_logged_in.php");	
			$middle = $template->render("modules/005_messaging/templates_popbloopdark/message_user_status.php");	
		}


    $template->middle = $middle;
    
    $user_property = user_user_property();
    $template->user_property = json_decode($user_property);

    $template->heartBeatInterval = 60000;

    // komponen2 template lain
    $template->top = $template->render("modules/000_user_interface/templates/top.php");
    $template->left = $template->render("modules/000_user_interface/templates/left.php");
    $template->right = $template->render("modules/000_user_interface/templates/right.php");
    $template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

    $return = $template->render("modules/000_user_interface/templates/ui_guest_default.php");
    return $return;

		
	} else {
		// definisikan content untuk header, footer, left, center dan right
//		$middle = file_get_contents($basepath . "user/user/login_form");
//		$middle = $template->render("modules/000_user_interface/templates/ui_guest_not_logged_in.php");
		$middle = $template->render("modules/000_user_interface/templates/ui_guest_landing.php");

    $template->top = NULL;
    $template->left = NULL;
    $template->right = NULL;
    $template->bottom = NULL;


    $template->middle = $middle;
    $return = $template->render("modules/000_user_interface/templates/ui_guest_default.php");
    return $return;
	}

}

function ui_guest_signupform_facebook(){
	
}

function ui_guest_signupform(){
  //die("<pre>" . print_r($_REQUEST, true) . "</pre>");
  
	$basepath = $_SESSION['basepath'];

	$template = new Template();

	$logged_in = user_user_loggedin();
	$template->logged_in = $logged_in;
	
	// Deteksi User Agent

	$template->basepath = $basepath;

  $template->signup_fullname = $_POST['signup_fullname'];
  $template->signup_email = $_POST['signup_email'];
  $template->signup_password = $_POST['signup_password'];

  $middle = $template->render("modules/000_user_interface/templates/ui_guest_signupform.php");

  $template->top = NULL;
  $template->left = NULL;
  $template->right = NULL;
  $template->bottom = NULL;

  $template->middle = $middle;
  $return = $template->render("modules/000_user_interface/templates/ui_guest_default.php");
  return $return;
  
}


function ui_guest_user_agent($option = NULL){	// option: ...
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


function ui_guest_microsite($file_to_load = NULL){
	if(!isset($file_to_load)){
		$file_to_load = func_arg(0);
	}
	
	$basepath = $_SESSION['basepath'];
	$template = new Template();
//	$logged_in = user_user_loggedin();

//	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
//	$template->session_id = user_user_sessionid();

//	$template->element_dir = $_SESSION['element_dir'];
//	$template->material_dir = $_SESSION['material_dir'];
//	$template->preview_dir = $_SESSION['preview_dir'];

//	$return = $template->render($template_file);
//	return $return;

//	$user_property = user_user_property();
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


?>