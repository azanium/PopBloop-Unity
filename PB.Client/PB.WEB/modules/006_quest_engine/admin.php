<?php

include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/user.php');
include_once('modules/000_user_interface/admin.php');

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
		case 'exist_id':
			$data = $_POST;
			
			$exists = $lilo_mongo->findOne(array('ID' => $data['ID']));
			if(count($exists)){
				return "Data Quest dengan ID = " . $data['ID'] . " sudah ada di database!";
			} else {
				return '';
			}
			
			break;
		
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
			$ID = isset($id) ? $id : func_arg(1);

//			$result = $lilo_mongo->findOne(array('lilo_id' => $lilo_id));
			$result = $lilo_mongo->findOne(array('ID' => "$ID"));	// ID harus string, bukan integer

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
function quest_admin_dialogstorydetail1($name = NULL){
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
	
	$data = $dialog_story['dialogs'];
	$return['Name'] = $dialog_story['name'];
	$return['Dialogs'] = array();
	foreach($data as $key => $val){
                unset($dtoption);
                $dtoption=array();
                foreach($val['options'] as $nilai)
                {                    
                    $dtoption[]=array(
                        'Tipe'=>$nilai['option_type'],
                        'Content'=>$nilai['description'],
                        'Next'=>$nilai['next_id'],
                    );
                }
                $return['Dialogs'][]=array(
                    'ID'=>$val['id'],
                    'Description'=>$val['description'],
                    'Options'=>$dtoption
                );	
	}
	
	
	$return = json_encode($return);
	return $return;
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
	
	$return['Description'] = $dialog_story['description'];
	$return['LILO_ID'] = $dialog_story['lilo_id'];
	
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
					
					if($return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Tipe'] == 1){
						$quest_description = quest_desc_by_id($data['option_content_quest_' . $key_expl[1] . '_' . $optidx]);
						// write_log(array('log_text' => "ID: " . $data['option_content_quest_' . $key_expl[1] . '_' . $optidx] . "\n\r" . print_r($quest_description, true)));
						
						$return['Dialogs'][$dialog_idx]['Options'][$option_idx]['Content'] = $data['option_content_quest_' . $key_expl[1] . '_' . $optidx] . ' - ' . $quest_description;
					}
					
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


function quest_desc_by_id($ID = NULL){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	$lilo_mongo->selectCollection('Quest');
	
	$ret = $lilo_mongo->findOne(array('ID' => "$ID"));
	return $ret['Description'];
}


function quest_admin_editdialogstory($lilo_id = NULL){

	if(!isset($lilo_id)){
		$lilo_id = func_arg(0);
	}
	
	if(!isset($lilo_id)){
		return false;
	}
	
	$dialog_story_detail = quest_admin_dialogstorydetailbyid($lilo_id);
	
	$template = new Template();
	$logged_in = user_user_loggedin();
		
	// Deteksi User Agent
		
	$template->logged_in = $logged_in;
	$template->basepath = $_SESSION['basepath'];
	$template->lilo_id = $lilo_id;
	$template->dialog_story_detail = $dialog_story_detail; //json_encode($dialog_story_detail);
		
	$return = $template->render("modules/006_quest_engine/templates/quest_admin_editdialogstory.php");
	return $return;

}

function quest_admin_editdialogstorysubmit(){
  $params = array();
  parse_str($_REQUEST['serialized_data'], $params);

	// die("Array yg dihasilkan: \n\r" . print_r($params, true));
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	$lilo_mongo->selectCollection('DialogStory');

	$lilo_id = $params['lilo_id'];
//	$dialog_story_name = $params['dialog_story_name'];
//	$dialog_story_description = $params['dialog_story_description'];
	
	unset($params['lilo_id']);
//	unset($params['dialog_story_name']);
//	unset($params['dialog_story_description']);
	
	foreach($params as $key => $val){//die("params key " . $params[$key]);
		if(strpos($key, 'option_content_quest_') !== false){
		  $params[$key] = intval($val);
		}
	}
	// die("Array yg dihasilkan: \n\r" . print_r($params, true));
	
	$criteria = array('lilo_id' => $lilo_id);
	$update_data = array(	'lilo_id' => $lilo_id,
												'description' => $params['dialog_story_description'],
												'all_variable_2' => $params
											 );
	
	$lilo_mongo->update_set($criteria, $update_data);
	
	return 'OK';
}







function quest_admin_wssearchplayer($keyword = NULL){
	if(!isset($keyword)){
		$keyword = func_arg(0);
	}

	$keyword = trim($keyword);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Users');
	$lilo_mongo->selectCollection('Account');
	
	$search = new MongoRegex("/".$keyword."/i");
	$player_cursor = $lilo_mongo->find(array("username" => $search));
	
	while($player = $player_cursor->getNext()){
//		$players[] = $player;
// write_log(array('log_text' => print_r($player, true)));
    unset($player['password']);
    unset($player['_id']);
		$players[] = array_merge(user_user_property(array('lilo_id' => $player['lilo_id']), 'array'), $player);

	}
	
	$players = json_encode($players);
	return $players;
}


function quest_admin_wssearchquest($keyword = NULL){
  // menampilkan daftar quest berdasar keyword yg diberikan
	// pencarian berdasar Game.Quest.ID & Game.Quest.Description
	
	if(!isset($keyword)){
		$keyword = func_arg(0);
	}

	$keyword = trim($keyword);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	$lilo_mongo->selectCollection('Quest');
	
	$search = new MongoRegex("/".$keyword."/i");
	$quest_cursor = $lilo_mongo->find(array("Description" => $search));
	
	while($quest = $quest_cursor->getNext()){
		$quests[] = $quest;
	}
	
//	return json_encode($dialog_stories);
	
	$quests = json_encode($quests);
	return $quests;
}


function quest_admin_wsquesttoplayer($type = NULL, $questid = NULL, $sort_by = NULL, $asc_desc = NULL){
  // input: quest id
	// return: semua player yg selesaikan quest ini
	// Game.QuestJournal
	
	if(!isset($type)){
		$type = func_arg(0);
	}
	
	if(!isset($questid)){
		$questid = func_arg(1);
	}
	
	$questid = intval($questid);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	
	if(trim(strtolower($type)) == 'completed' || trim(strtolower($type)) == 'csv_completed'){
		$lilo_mongo->selectCollection('QuestJournal');
		
		$criteria = array('questid' => $questid);
		$journal_cursor = $lilo_mongo->find($criteria);
	
		$result = array();
		while($journal = $journal_cursor->getNext()){
			// dari $journal['userid'], dapatkan properti player
			$player_property = user_user_property(array('lilo_id' => $journal['userid']));
			
			// dari $journal['start_date'] & $journal['end_date'] dapatkan durasi
			if(trim($journal['start_date']) != '' && trim($journal['end_date']) != ''){
				$duration = unitytounixtime(trim($journal['end_date'])) - unitytounixtime(trim($journal['start_date']));
				// $duration = date("Y-m-d H:i:s", $duration);
				$journal['duration'] = sec2hms($duration);
			}
			
			$result[] = array_merge((array)json_decode($player_property), $journal);
		}
		
		// die('<pre>' . print_r($result, true) . '</pre>');
		
		if(trim(strtolower($type)) == 'csv_completed'){
      // dari $result, generate csv
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=Quest.'.$questid.'.completed.player.'.time().'.csv');
			header('Pragma: no-cache');
		  
			//# Titlte of the CSV
			//$Content = "Name,Address,Age,Phone \n";
			//# fill data in the CSV
			//$Content .= "\"John Doe\",\"New York, USA\",15,65465464 \n";
			//
			//echo $Content;
			
			// User Name	Full Name	Handphone	Email	Twitter	Start Date	End Date	Duration
			$csv_string = "User Name, Avatar Name, Full Name, Handphone, Email, Twitter, Start Date, End Date, Duration \n";
			
			foreach($result as $res){
				$csv_string .= $res['username'] . ", " . $res['avatarname'] . ", " . $res['fullname'] . ", " . $res['handphone'] . ", " . $res['email'] . ", " . $res['twitter'] . ", " . $res['start_date'] . ", " . $res['end_date'] . ", " . $res['duration'] . " \n";
			}
			
			echo $csv_string;
			exit;
		}
		
		return json_encode($result);
	} else {
		$lilo_mongo->selectCollection('QuestActive');
		
		$criteria = array('questid' => $questid);
		$journal_cursor = $lilo_mongo->find($criteria);
	
		$result = array();
		while($journal = $journal_cursor->getNext()){
			// dari $journal['userid'], dapatkan properti player
			// SAMPE SENEEE...
			$player_property = user_user_property(array('lilo_id' => $journal['userid']));
			$result[] = array_merge((array)json_decode($player_property), $journal);
		}
		
		// die('<pre>' . print_r($result, true) . '</pre>');
		return json_encode($result);
	}
}

function quest_admin_exp(){
  return unitytounixtime('6/29/2012 7:32:09 PM') . " - " . unitytounixtime('6/29/2012 7:08:51 PM') . " - " . unitytounixtime('7/1/2012 10:47:15 AM') . " - " . unitytounixtime('7/1/2012 10:47 PM');
}

function quest_admin_wsplayertoquest($type = NULL, $userid = NULL, $sort_by = NULL, $asc_desc = NULL){
  // input: type (completed, current), player userid / username
	// return: semua quest yg diselesaikan player ini
	// Game.QuestJournal
	
	if(!isset($type)){
		$type = func_arg(0);
	}
	
	if(!isset($userid)){
		$userid = func_arg(1);
	}
	
	$userid = trim($userid);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest
	
	if(trim(strtolower($type)) == 'completed'){
		$lilo_mongo->selectCollection('QuestJournal');
		
		$criteria = array('userid' => $userid);
		$journal_cursor = $lilo_mongo->find($criteria);
	
		$result = array();
		while($journal = $journal_cursor->getNext()){
			// dari $journal['questid'], dapatkan properti quest
			$quest_property = quest_admin_ws_quest('detail', $journal['questid']);
			
			// dari $journal['start_date'] & $journal['end_date'] dapatkan durasi
			if(trim($journal['start_date']) != '' && trim($journal['end_date']) != ''){
				$duration = unitytounixtime(trim($journal['end_date'])) - unitytounixtime(trim($journal['start_date']));
				// $duration = date("Y-m-d H:i:s", $duration);
				$journal['duration'] = sec2hms($duration);
			}
			
			$result[] = array_merge((array)json_decode($quest_property), $journal);
		}
		
		// die('<pre>' . print_r($result, true) . '</pre>');
		return json_encode($result);
	} else {
		$lilo_mongo->selectCollection('QuestActive');
		
		$criteria = array('userid' => $userid);
		$journal_cursor = $lilo_mongo->find($criteria);
	
		$result = array();
		while($journal = $journal_cursor->getNext()){
			// dari $journal['questid'], dapatkan properti quest
			// SAMPE SENEEE...
			$quest_property = quest_admin_ws_quest('detail', $journal['questid']);
			$result[] = array_merge((array)json_decode($quest_property), $journal);
		}
		
		// die('<pre>' . print_r($result, true) . '</pre>');
		return json_encode($result);
	}
	
  
}



function quest_admin_questjournal($quest_id = NULL){
	// menampilkan daftar player yg sudah menyelesaikan + player yg masih memainkan sebuah quest
	// data diperoleh dari quest_admin_wsquesttoplayer (AJAX)
	
  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  

  $html = $template->render("modules/006_quest_engine/templates/quest_admin_questjournal.php");
  $html = ui_admin_default(NULL, $html);

  return $html;
}


function quest_admin_undoneplayerquest($questid = NULL, $userid = NULL){
  // hapus data dari QuestJournal
  $questid = isset($questid) ? $questid : func_arg(0);
  $userid = isset($userid) ? $userid : func_arg(1);


	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest

  $lilo_mongo->selectCollection('QuestJournal');
	
	$criteria = array('questid' => intval($questid), 'userid' => $userid);
	// die(print_r($criteria, true));
	$lilo_mongo->remove($criteria);
	
	return "OK";
//	die("questid: $questid, userid: $userid");
}


function quest_admin_playerjournal($user_id = NULL){
  // menampilkan daftar quest yg sudah diselesaikan + sedang dimainkan seorang player
	// data diperoleh dari quest_admin_wsplayertoquest (AJAX)
}

function quest_admin_dropplayerinventory(){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');	// Game: DialogStory, Dialog, DialogOption, Quest

  $lilo_mongo->selectCollection('PlayerInventory');
	
	$lilo_mongo->remove(array());

  return 'OK';
}

?>