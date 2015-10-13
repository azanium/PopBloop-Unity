<?php

/**
 * halaman default
 * write docs here dude...
 */
include_once('libraries/Template.php');

include_once('modules/001_user_management/admin.php');

function ui_admin_default($template_file = NULL, $html_string = NULL){
	$basepath = $_SESSION['basepath'];

	$template = new Template();

	$is_admin = user_admin_is_admin();
	
	// Deteksi User Agent

	$template->basepath = $basepath;

	if($is_admin){
		// definisikan content untuk header, footer, left, center dan right
		if(isset($template_file) && is_file($template_file)){
			$middle = $template->render($template_file);	
		} else if (isset($html_string)){
			$middle = $html_string;
		} else {
			// defaultnya tampilin apa bro?
			$middle = $template->render("modules/000_user_interface/templates/ui_admin_logged_in.php");	
		}
		
	} else {
		// definisikan content untuk header, footer, left, center dan right
//		$middle = file_get_contents($basepath . "user/user/login_form");
		$middle = $template->render("modules/000_user_interface/templates/ui_admin_not_logged_in.php");
	}

	$template->middle = $middle;
	
	$user_property = user_user_property();
	$template->user_property = json_decode($user_property);

	$template->heartBeatInterval = 60000;

	// komponen2 template lain
	$template->is_admin = $is_admin;
	$template->top = $template->render("modules/000_user_interface/templates/top.php");
	$template->left = $template->render("modules/000_user_interface/templates/left.php");
	$template->right = $template->render("modules/000_user_interface/templates/right.php");
	$template->bottom = $template->render("modules/000_user_interface/templates/bottom.php");

	$return = $template->render("modules/000_user_interface/templates/ui_admin_default.php");
	return $return;
}


function ui_admin_avatar_editor(){
	$ajax = func_arg(0);
	if($ajax == 'ajax'){
		$basepath = $_SESSION['basepath'];
		$template = new Template();
		$logged_in = user_user_loggedin();

		// Deteksi User Agent

		$template->logged_in = $logged_in;
		$template->basepath = $basepath;

		$return = $template->render("modules/000_user_interface/templates/ui_admin_avatar_editor.php");
		return $return;

	} else {
		$template_file = "modules/000_user_interface/templates/ui_admin_avatar_editor.php";
		return ui_admin_default($template_file);
	}

}

function ui_admin_user_agent($option = NULL){	// option: ...
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

function ui_admin_quest(){
	return "Quest...";	
}

function ui_admin_quiz(){
	return "Quiz...";	
}

function ui_admin_statistics(){
	return "Stats...";	
}

?>