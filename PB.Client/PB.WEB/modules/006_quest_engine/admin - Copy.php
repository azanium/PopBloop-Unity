<?php

include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/user.php');

// quest editor for admin
// CRUD quest
function quest_admin_default(){
		$template = new Template();
		$logged_in = user_user_loggedin();

		// Deteksi User Agent

		$template->logged_in = $logged_in;
		$template->basepath = $_SESSION['basepath'];

		$return = $template->render("modules/006_quest_engine/templates/quest_admin_default.php");
		return $return;

}

// dialog editor for admin
// CRUD dialog_story -> dialog -> option / quest
function quest_admin_dialog(){
		$template = new Template();
		$logged_in = user_user_loggedin();

		// Deteksi User Agent

		$template->logged_in = $logged_in;
		$template->basepath = $_SESSION['basepath'];

		$return = $template->render("modules/006_quest_engine/templates/quest_admin_dialog.php");
		return $return;

}

function quest_admin_test_submit(){
	print("<pre>" . print_r($_REQUEST, true) . "</pre>");
	print("<pre>" . print_r($_POST, true) . "</pre>");
	
	exit;
}

function quest_admin_ws_quest($op = NULL, $id = NULL, $data = NULL){
	if(!isset($op)){
		$op = func_arg(0) != '' ? func_arg(0) : 'read';
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	$lilo_mongo->selectCollection('Quest');

	switch($op){
		case 'create':
			$data = $_POST;
			// return "<pre>" . print_r($data, true) . "<pre>";

			// cek dulu apakah ID yg dimasukkan sudah ada di db
			$exists = $lilo_mongo->findOne(array('ID' => $data['ID']));
			if(count($exists)){
				return "Data Quest dengan ID = " . $data['ID'] . " sudah ada di database!";
			}
			
			$lilo_id = $lilo_mongo->insert($data);
			$lilo_mongo->update($data, array_merge($data, array('lilo_id' => (string)$lilo_id)), array("multiple" => false));

			return "1";

			break;
		case 'update':
			$data = $_POST;
			$exists_ = $lilo_mongo->findOne(array('ID' => $data['ID']));
			$exist = ($exists_['lilo_id'] != $data['lilo_id']);
			if($exists){
				return "Data Quest dengan ID = " . $data['ID'] . " sudah ada di database! Gunakan ID lain";
			}
			
			$lilo_mongo->update_set(array('lilo_id' => $data['lilo_id']), $data);
			
			return "1";
			
			break;
		case 'delete':
			$lilo_id = func_arg(1);
			
			$lilo_mongo->remove(array('lilo_id' => $lilo_id));
			
			return "1";
			
			break;
		
		// detail == getone == findONe
		// digunakan unity web player
		case 'detail':
			$ID = func_arg(1);
			
//			$result = $lilo_mongo->findOne(array('lilo_id' => $lilo_id));
			$result = $lilo_mongo->findOne(array('ID' => $ID));

			$result['IsActive'] = strtolower($result['IsActive']) == 'true' ? true : false;
			$result['IsDone'] = strtolower($result['IsDone']) == 'true' ? true : false;
			$result['IsReturn'] = strtolower($result['IsReturn']) == 'true' ? true : false;

			if(count($result)){
//				return json_encode($result);
				unset($result['_id']);
				unset($result['_id']);
				unset($result['lilo_id']);
				unset($result['Energy']);
				unset($result['Item']);
				$result['ID'] = intval($result['ID']);
				$result['RequiredEnergy'] = intval($result['RequiredEnergy']);
				$result['Requirement'] = intval($result['Requirement']);
				$result = json_encode($result);
				return $result;
//				return str_replace('"', "'", $result);

			} else {
				return '';
			}

//			return json_encode($result);
			
			break;

		// digunakan oleh quest editor
		case 'detail_by_lilo_id':
			$lilo_id = func_arg(1);
			
			$result = $lilo_mongo->findOne(array('lilo_id' => $lilo_id));

			if(count($result)){
//				return json_encode($result);
				
				$result = json_encode($result);
				return $result;
//				return str_replace('"', "'", $result);

				
			} else {
				return '';
			}
			break;

	}
	
	// default: read
	// read == getall == find
	$quests = array();
	
	$quests_cursor = $lilo_mongo->find();
	
	while($quest = $quests_cursor->getNext()){
		$quests[] = $quest;
	}
	
//	return json_encode($quests);

	$quests = json_encode($quests);
	return $quests;
//	return str_replace('"', "'", $quests);

}

// show all dialog story and its 'children'
function quest_admin_ws_dialogstory($op = NULL, $id = NULL, $data = NULL){
	// quest/admin/ws_dialogstory							=> list all dialog_story
	// quest/admin/ws_dialogstory/read				=> -- idem --
	// quest/admin/ws_dialogstory/create			=> menerima $_POST untuk dimasukkan ke db
	// quest/admin/ws_dialogstory/update/$id	=> menerima $_POST untuk update dialog_story dgn id = $id
	// quest/admin/ws_dialogstory/delete/$id	=> menghapus dialog_story dgn id = $id
	if(!isset($op)){
		$op = func_arg(0) != '' ? func_arg(0) : 'read';
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	$lilo_mongo->selectCollection('DialogStory');
	
	switch($op){
		case 'create':	// sementara baru data di Game.DialogStory dulu belum sampe ke anak2nya...
			if(!isset($data)){	// data = array(name, description);
				$data = $_POST;
			}
			
//			return $data['all_variable'];
			
			if(!isset($data['name'])){
				return false;
			}
			
//			$data['all_variable'] = parse_str($data['all_variable']);
			$a = explode('&', $data['all_variable']);
			$i = 0;
			while ($i < count($a)) {
				$b = split('=', $a[$i]);
//					echo 'Value for parameter ', htmlspecialchars(urldecode($b[0])),
//							 ' is ', htmlspecialchars(urldecode($b[1])), "<br />\n";
				$i++;
				$key = urldecode($b[0]);
				$val = urldecode($b[1]);
				
				// option_content_quest_
				if(substr($key, 0, 21) == 'option_content_quest_'){
					$val_expl = explode('-', $val);
					$val = trim($val_expl[0]);
				}
				
				$data['all_variable_2'][$key] = $val;
			}

			
			$dialog_story_id = $lilo_mongo->insert($data);
			$lilo_mongo->update($data, array_merge($data, array('lilo_id' => (string)$dialog_story_id)), array("multiple" => false));
			
		
			return "1";//$dialog_story_id;
			
			break;
			
		case 'update':	// sementara baru data di Game.DialogStory dulu belum sampe ke anak2nya...
			if(!isset($id)){
				$id = func_arg(1);
			}
			
			if(!isset($data)){	// data = array(name, description);
				$data = $_POST;
			}
			
			$lilo_mongo->update(array('lilo_id' => $id), $data, array("multiple" => false));
			
			return "1";
			
			break;
			
		case 'delete':
			if(!isset($id)){
				$id = func_arg(1);
			}

			$lilo_mongo->delete(array('lilo_id' => $id));

			return "1";

			break;
	}


	// op == 'read'
	
	$dialog_stories = array();
	
	$dialog_stories_cursor = $lilo_mongo->find();
	
	while($dialog_story = $dialog_stories_cursor->getNext()){
		$dialog_stories[] = $dialog_story;
	}
	
//	return json_encode($dialog_stories);
	
	$dialog_stories = json_encode($dialog_stories);
	return $dialog_stories;
//	return str_replace('"', "'", $dialog_stories);

}

function quest_admin_dialogstorydetail($name = NULL){
	if(!isset($name)){
		$name = func_arg(0);
	}
	
	if(!isset($name)){
		return false;
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	$lilo_mongo->selectCollection('DialogStory');

	$dialog_story = $lilo_mongo->findOne(array('name' => $name));
	
	$return = array();
	
	$data = $dialog_story['all_variable_2'];
	$return['Name'] = $data['dialog_story_name'];
	$return['Dialogs'] = array();
	
	// hitung jumlah dialog berdasar dialogid_x [x: 1, 2]
	$dialog_idx = 0;
	foreach($data as $key => $val){
		$pos = strpos($key, 'dialogid_');
		if($pos !== false){
			$key_expl = explode('_', $key);
			$return['Dialogs'][$dialog_idx]['ID'] = (int)$val;
			$return['Dialogs'][$dialog_idx]['Description'] = $data['description_' . $key_expl[1]];
			
			$return['Dialogs'][$dialog_idx]['Options'] = array();
			$option_idx = 0;
			foreach($data as $key_ => $val_){	// dapatkan select_options_ $key_expl[1] _ xxx
				$pos_ = strpos($key_, 'select_options_' . $key_expl[1]);
				if($pos_ !== false){
					$key_expl_ = explode('_', $key_);
					$optidx = $key_expl_[3];
					$return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Tipe'] = (int)$val_;
					$return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Content'] = $data['option_content_choice_' . $key_expl[1] . '_' . $optidx];
					$return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Next'] = (int)$data['nextidoption_content_choice_' . $key_expl[1] . '_' . $optidx];
					
					
					$option_idx++;
				}
			}
			
			$dialog_idx++;
		}
	}
	
	
	$return = json_encode($return);
	return $return;
//	return str_replace('"', "'", $return);
}

			/*
        "all_variable_2" / "data" : {
                "dialog_story_name" : "First",
                "dialog_story_description" : "",


                "dialogid_1" : "0",
                "description_1" : "Hello, my name AZA!",
								
                "select_options_1_1" : "0",
                "option_content_choice_1_1" : "OK, continue",
                "nextidoption_content_choice_1_1" : "1",
								
                "select_options_1_2" : "0",
                "option_content_choice_1_2" : "Nope, thanks, I'm out",
                "nextidoption_content_choice_1_2" : "-1",


								"dialogid_2" : "1",
                "description_2" : "AZA Very handsome!",
								
                "select_options_2_3" : "0",
                "option_content_choice_2_3" : "You're right!",
                "nextidoption_content_choice_2_3" : "0"
        },
			*/

function quest_admin_dialogstorydetailbyid($lilo_id = NULL){
	if(!isset($lilo_id)){
		$lilo_id = func_arg(0);
	}
	
	if(!isset($lilo_id)){
		return false;
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	$lilo_mongo->selectCollection('DialogStory');

	$dialog_story = $lilo_mongo->findOne(array('lilo_id' => $lilo_id));
	
	$return = array();
	
	$data = $dialog_story['all_variable_2'];
	$return['Name'] = $data['dialog_story_name'];
	$return['Dialogs'] = array();
	
	// hitung jumlah dialog berdasar dialogid_x [x: 1, 2]
	$dialog_idx = 0;
	foreach($data as $key => $val){
		$pos = strpos($key, 'dialogid_');
		if($pos !== false){
			$key_expl = explode('_', $key);
			$return['Dialogs'][$dialog_idx]['ID'] = (int)$val;
			$return['Dialogs'][$dialog_idx]['Description'] = $data['description_' . $key_expl[1]];
			
			$return['Dialogs'][$dialog_idx]['Options'] = array();
			$option_idx = 0;
			foreach($data as $key_ => $val_){	// dapatkan select_options_ $key_expl[1] _ xxx
				$pos_ = strpos($key_, 'select_options_' . $key_expl[1]);
				if($pos_ !== false){
					$key_expl_ = explode('_', $key_);
					$optidx = $key_expl_[3];
					$return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Tipe'] = (int)$val_;
					$return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Content'] = $data['option_content_choice_' . $key_expl[1] . '_' . $optidx];
					$return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Next'] = (int)$data['nextidoption_content_choice_' . $key_expl[1] . '_' . $optidx];
					
					
					$option_idx++;
				}
			}
			
			$dialog_idx++;
		}
	}
	
	
//	$return = json_encode($return);
	return $return;
//	return str_replace('"', "'", $return);
}


function quest_admin_editdialogstory($lilo_id = NULL){
	if(!isset($lilo_id)){
		$lilo_id = func_arg(0);
	}
	
	if(!isset($lilo_id)){
		return false;
	}
	
	$dialog_story_detail = quest_admin_dialogstorydetailbyid($lilo_id);
}

?>