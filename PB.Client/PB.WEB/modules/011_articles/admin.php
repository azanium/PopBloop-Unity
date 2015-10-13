<?php
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/admin.php');

function article_admin_default(){
	// menampilkan semua item slideshow
	// table: Articles.Slideshow
	// struktur data: lilo_id, no, title, image, description, link

  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  
	// dari $_SESSION['pop_last_article_tab'], set default selected tab
	// setelah digunakan, langsung unset
	
	$tabs = array('article' => 0, 'slideshow' => 1);
	$template->active_tabs = intval($tabs[$_SESSION['pop_last_article_tab']]);
	
  $html = $template->render("modules/011_articles/templates/article_admin_default.php");

  $html = ui_admin_default(NULL, $html);

  return $html;

}

// ambil data dari table Articles.Article
//		(lilo_id, title, text, alias)
// return array
function article_admin_getarticles(){
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

function article_admin_getonearticle($id = NULL){
	if(!isset($id)){
		$id = $_POST['id'];
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Article');
	
	$article = $lilo_mongo->findOne(array('lilo_id' => $id));
	return json_encode($article);
}

function article_admin_addarticle($title = NULL, $text = NULL, $alias = NULL){
	if(!isset($title) || !isset($text)){
		$title = $_POST['title'];
		$text = $_POST['text'];
		$alias = $_POST['alias'];
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Article');

	$article = array('title' => $title, 'text' => $text, 'alias' => $alias);
	$lilo_id = $lilo_mongo->insert($article);
	$lilo_mongo->update($article, array_merge($article, array('lilo_id' => (string)$lilo_id)), array('multiple' => false));

	return (string)$lilo_id;
}

function article_admin_deletearticle($lilo_id = NULL){
  if(!isset($lilo_id)){
    $lilo_id = $_POST['lilo_id'];
  }
  
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Article');

  // remove($array_criteria, $array_options = array())
  $array_criteria = array('lilo_id' => $lilo_id);
  
  $lilo_mongo->remove($array_criteria);
  
  return "OK";
}

function article_admin_updatearticle($lilo_id = NULL, $alias = NULL, $title = NULL, $text = NULL){
  if(!isset($lilo_id) || !isset($alias) || !isset($title) || !isset($text)){
    $lilo_id = $_POST['lilo_id'];
    $alias = $_POST['alias'];
    $title = $_POST['title'];
    $text = $_POST['text'];
  }
  
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Article');

  // update_set($array_criteria, $array_newobj, $array_options = array("multiple" => true))
  $array_criteria = array('lilo_id' => $lilo_id);
  $array_newobj = array('alias' => $alias, 'title' => $title, 'text' => $text);
  
  $lilo_mongo->update_set($array_criteria, $array_newobj);
  
  return "OK";
}


function article_admin_updateslide($lilo_id = NULL, $no = NULL, $title = NULL, $description = NULL, $image = NULL, $link = NULL){
  if(!isset($lilo_id) || !isset($no) || !isset($title) || !isset($description) || !isset($image)){
    $lilo_id = $_POST['lilo_id'];
    $no = $_POST['no'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $link = $_POST['link'];
  }
  
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Slideshow');

  // update_set($array_criteria, $array_newobj, $array_options = array("multiple" => true))
  $array_criteria = array('lilo_id' => $lilo_id);
  $array_newobj = array('no' => $no, 'title' => $title, 'description' => $description, 'image' => $image, 'link' => $link);
  
  $lilo_mongo->update_set($array_criteria, $array_newobj);
  
  return "OK";
}

function article_admin_deleteslide($lilo_id = NULL){
  if(!isset($lilo_id)){
    $lilo_id = $_POST['lilo_id'];
  }
  
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Slideshow');

  // remove($array_criteria, $array_options = array())
  $array_criteria = array('lilo_id' => $lilo_id);
  
  $lilo_mongo->remove($array_criteria);
  
  return "OK";
}

function article_admin_addslide($no = NULL, $title = NULL, $image = NULL, $description = NULL, $link = NULL){
	if(!isset($no) || !isset($title) || !isset($image) || !isset($description) || !isset($link)){
		$no = $_POST['no'];
		$title = $_POST['title'];
		$image = $_POST['image'];
		$description = $_POST['description'];
		$link = $_POST['link'];
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Slideshow');

	$slide = array('no' => $no, 'title' => $title, 'image' => $image, 'description' => $description, 'link' => $link);
	$lilo_id = $lilo_mongo->insert($slide);
	$lilo_mongo->update($slide, array_merge($slide, array('lilo_id' => (string)$lilo_id)), array('multiple' => false));

	return (string)$lilo_id;
}

function article_admin_getoneslide($id = NULL){
	if(!isset($id)){
		$id = $_POST['id'];
	}
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Articles');
  $lilo_mongo->selectCollection('Slideshow');
	
	$article = $lilo_mongo->findOne(array('lilo_id' => $id));
	return json_encode($article);
}

function article_admin_getslides(){
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
