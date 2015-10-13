<?php
function avatar_admin_default(){
	// menampilkan daftar komponen avatar berdasar file2 yg ada di bundles/characters [dan bundles/materials]
	$chara_dir = 'bundles/characters';
	$mater_dir = 'bundles/materials';

	$chara_array = array();
	if ($handle = opendir($chara_dir)) {
		while (false !== ($file = readdir($handle))) {
			$file_expl = explode(".", $file);
			$is_unity3d = strtolower($file_expl[count($file_expl) - 1]) == 'unity3d';
			$file_replaced = str_replace('.unity3d', '', $file);
			if ($is_unity3d/*$file != "." && $file != ".."*/) {
//				echo "$file<br />\n";
				$chara_array[] = $file_replaced;
			}
		}
		
		closedir($handle);
	}
	
	$mater_array = array();
	if ($handle = opendir($mater_dir)) {
		while (false !== ($file = readdir($handle))) {
			$file_expl = explode(".", $file);
			$is_unity3d = strtolower($file_expl[count($file_expl) - 1]) == 'unity3d';
			$file_replaced = str_replace('.unity3d', '', $file);
			if ($is_unity3d/*$file != "." && $file != ".."*/) {
//				echo "$file<br />\n";
				$mater_array[] = $file_replaced;
			}
		}
		
		closedir($handle);
	}
	
//	print("Character:<br /><pre>" . print_r($chara_array, true) . "</pre><hr />");
//	print("Materials:<br /><pre>" . print_r($mater_array, true) . "</pre><hr />");

	// dapatkan semua materials untuk setiap character
	// male_eyes => male_eyes_blue, male_eyes_brown, male_eyes_green
	$chara_mater_array = array();
	for($idx = 0; $idx < count($chara_array); $idx++){
		$chara_mater_array[$idx]['character'] = $chara_array[$idx];
		foreach($mater_array as $ma){
//			if(){}
		}
	}

}


function avatar_admin_set_default_configuration($avatar_conf = NULL, $gender = NULL, $size = NULL, $name = NULL){
	if(!isset($avatar_conf)){
		$avatar_conf = $_REQUEST['avatar_conf'];
	}
	
	if(!isset($size)){
		$size = $_REQUEST['size'];
	}

	if(!isset($gender)){
		$gender = $_REQUEST['gender'];
	}

	if(!isset($name)){
		$name = $_REQUEST['name'];
	}
	
	$name = trim($name);

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('DefaultAvatar');
	
	$insert_id = $lilo_mongo->update_set(	array('gender' => $gender, 'size' => $size, 'name' => $name), 
																				array('gender' => $gender, 'size' => $size, 'name' => $name, 'configuration' => $avatar_conf), 
																				array('upsert' => true));
	
	return "1";

}

// http://localhost/avatar/admin/get_default_configuration&gender=male&size=big
function avatar_admin_get_default_configuration($gender = NULL, $size = NULL, $name = NULL){	// ambil data dari DefaultAvatar
	// pencarian dapat dilakukan berdasar [gender & size] maupun [name]
	if(!isset($gender)){
//		$gender = func_arg(0);
		$gender = $_REQUEST['gender'];
	}
	
	if(!isset($size)){
//		$size = func_arg(1);
		$size = $_REQUEST['size'];
	}

	if(!isset($name)){
//		$name = func_arg(2);
		$name = $_REQUEST['name'];
	}

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('DefaultAvatar');

	
	if(!isset($name)){
		if(isset($gender) && isset($size)){
			$name = "default " . trim(strtolower($gender)) . " " . trim(strtolower($size));
		}
		
	}
	
	$criteria = array();
	
	if(isset($gender)){
		$criteria = array_merge($criteria, array('gender' => $gender));
	}
	
	if(isset($name)){
		$criteria = array_merge($criteria, array('name' => $name));
	}
	
	if(isset($size)){
		$criteria = array_merge($criteria, array('size' => $size));
	}
	
	if(!count($criteria)){
		return json_encode(array());
	}
	
	$defaultAvatar = $lilo_mongo->findOne($criteria);
	
	return $defaultAvatar['configuration'];
	
}

function avatar_admin_preset($op = NULL){	// CRUD
	
	// data selain op dikirim via post
	
	if(!isset($op)){
		$op = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('DefaultAvatar');
	
	switch(strtolower(trim($op))){
		case 'getall':
			$preset_cursor = $lilo_mongo->find();
			$result = array();
			while($preset = $preset_cursor->getNext()){
				$key = $preset['name'];
				$result[$key] = $preset;
			}
			
			$result_['default male thin'] = $result['default male thin'];
			$result_['default male medium'] = $result['default male medium'];
			$result_['default male fat'] = $result['default male fat'];
			
			$result_['default female thin'] = $result['default female thin'];
			$result_['default female medium'] = $result['default female medium'];
			$result_['default female fat'] = $result['default female fat'];
			
			return "<pre>" . print_r($result_, true) . "</pre>";
			return json_encode($result_);
			break;
		
	}
	
	
}


?>