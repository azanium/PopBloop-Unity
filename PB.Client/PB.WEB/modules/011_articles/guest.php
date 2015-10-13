<?php
include_once('libraries/Template.php');

include_once('modules/001_user_management/user.php');
include_once('modules/004_friends/user.php');
include_once('modules/005_messaging/guest.php');

include_once('libraries/LiloMongo.php'); 

// menampilkan page sesuai parameter
// $template_file: whatis -> $actual_template_file: modules\011_articles\templates_popbloopdark\article_guest_whatis.php
function article_guest_page($template_file = NULL){
	if(!isset($template_file)){
		$template_file = func_arg(0);
	}
	$template_file = 'modules/011_articles/templates_popbloopdark/article_guest_'.$template_file.'.php';
//	die($actual_template_file);

	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;

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


function article_guest_read(){
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	
//	$lilo_mongo = new LiloMongo();
//	$lilo_mongo->selectDB('Assets');
//	$lilo_mongo->selectCollection('Avatar');

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

	$alias = func_arg(0);
//	$template_file = "modules/011_articles/templates_popbloopdark/article_guest_read_".$article_id.".php";
	// testing

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Articles');
	$lilo_mongo->selectCollection('Article');

  $article = $lilo_mongo->findOne(array('alias' => $alias));
  $template->title = $article['title'];
  $template->text = $article['text'];
  $template->alias = $article['alias'];


	$template_file = "modules/011_articles/templates_popbloopdark/article_guest_get.php";
	
	$template->middle = $template->render($template_file);

	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
}

// sama dengan read, tapi dari db
function article_guest_get($alias = NULL){
  if(!isset($alias)){
    $alias = func_arg(0);
  }
  
	$basepath = $_SESSION['basepath'];
	$template = new Template();
	$logged_in = user_user_loggedin();

	$template->logged_in = $logged_in;
	$template->basepath = $basepath;
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Articles');
	$lilo_mongo->selectCollection('Article');

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

//	$article_id = func_arg(0);
//	$template_file = "modules/011_articles/templates_popbloopdark/article_guest_read_".$article_id.".php";
	
//	$template->middle = $template->render($template_file);
  $article = $lilo_mongo->findOne(array('alias' => $alias));
  $template->middle = $article['text'];


	$return = $template->render("modules/000_user_interface/templates/ui_user_default.php");
	return $return;
  
  
}


// ambil data dari table Articles.Article
//		(lilo_id, title, text, alias)
// return array
function article_guest_getarticles(){
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Article');

	$array_parameter = array();
	$limit = 0;
	$sort = array('title' => 1);

	$article_cursor = $lilo_mongo->find($array_parameter, $limit, $sort);
	
	$article_array = array();
	while($curr = $article_cursor->getNext()){
		$article_array[] = array(	'lilo_id' => $curr['lilo_id'],
															'title' => $curr['title'],
															'alias' => $curr['alias'],
															'text' => $curr['text'],
															'text_short' => substr($curr['text'], 0, 100)
		);
	}

	return json_encode($article_array);
}

function article_guest_getonearticle($id = NULL){
	if(!isset($id)){
		$id = $_POST['id'];
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Article');
	
	$article = $lilo_mongo->findOne(array('lilo_id' => $id));
	return json_encode($article);
}


function article_guest_getoneslide($id = NULL){
	if(!isset($id)){
		$id = $_POST['id'];
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Slideshow');
	
	$article = $lilo_mongo->findOne(array('lilo_id' => $id));
	return json_encode($article);
}

function article_guest_getslides(){
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Slideshow');

	$array_parameter = array();
	$limit = 0;
	$sort = array('no' => 1);

	$article_cursor = $lilo_mongo->find($array_parameter, $limit, $sort);
	
	$article_array = array();
	while($curr = $article_cursor->getNext()){
		$article_array[] = array(	'lilo_id' => $curr['lilo_id'],
															'no' => $curr['no'],
															'title' => $curr['title'],
															'image' => $curr['image'],
															'description' => $curr['description'],
															'link' => $curr['link']
		);
	}

	return json_encode($article_array);	
}

