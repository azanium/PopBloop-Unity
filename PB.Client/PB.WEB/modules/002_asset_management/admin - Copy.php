<?php
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/admin.php');

function asset_admin_default(){
	return "asset admin default";
}

function asset_admin_avatar(){
	// CRUD for AVATAR
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');

	
	$add_new_avatar = $_POST['add_new_avatar'];
	
	if($add_new_avatar){
		extract($_REQUEST);

		$element = '';	//$_FILES['avatar_add_element'];
		$material = '';	//$_FILES['avatar_add_material'];
		$preview_image = '';	//$_FILES['avatar_add_preview_image'];

		if(isset($avatar_add_tipe) && isset($avatar_add_name)){
			// tangani file upload
			
			// element
			if(isset($_FILES['avatar_add_element'])){
				$uploaddir = 'bundles/characters/';
				if(!is_dir($uploaddir)){
					mkdir($uploaddir);
				}
				
				$uploadfile = $uploaddir . basename($_FILES['avatar_add_element']['name']);
				
				if (move_uploaded_file($_FILES['avatar_add_element']['tmp_name'], $uploadfile)) {
					$element = $_FILES['avatar_add_element']['name'];
				}
			}
			
			// material
			if(isset($_FILES['avatar_add_material'])){
				$uploaddir = 'bundles/materials/';
				if(!is_dir($uploaddir)){
					mkdir($uploaddir);
				}
				
				$uploadfile = $uploaddir . basename($_FILES['avatar_add_material']['name']);
				
				if (move_uploaded_file($_FILES['avatar_add_material']['tmp_name'], $uploadfile)) {
					$material = $_FILES['avatar_add_material']['name'];
				}
			}
			
			// preview_image
			// baiknya langsung generate thumbnail: 100x100
			if(isset($_FILES['avatar_add_preview_image'])){
				$uploaddir = 'bundles/preview_images/';
				if(!is_dir($uploaddir)){
					mkdir($uploaddir);
				}
				
				$uploadfile = $uploaddir . basename($_FILES['avatar_add_preview_image']['name']);
				
				if (move_uploaded_file($_FILES['avatar_add_preview_image']['tmp_name'], $uploadfile)) {
					$preview_image = $_FILES['avatar_add_preview_image']['name'];
				}
			}
			
			// ------ REVISI 100511 ------
			// tambah table Assets.AvatarCategories
			// tambah field 'categories' di table Assets.Avatar
			
			$avatar_data = array('tipe'						=> $avatar_add_tipe, 
													 'name'						=> $avatar_add_name, 
													 'element'				=> $element, 
													 'material'				=> $material, 
													 'preview_image'	=> $preview_image,
													 'gender'					=> $gender);

			// penanganan khusus untuk tipe == 'hair'
			if(strtolower(trim($avatar_add_tipe)) == 'hair'){
				// element
				if(isset($_FILES['avatar_add_element_2'])){
					$uploaddir = 'bundles/characters/';
					if(!is_dir($uploaddir)){
						mkdir($uploaddir);
					}
					
					$uploadfile = $uploaddir . basename($_FILES['avatar_add_element_2']['name']);
					
					if (move_uploaded_file($_FILES['avatar_add_element_2']['tmp_name'], $uploadfile)) {
						$element_2 = $_FILES['avatar_add_element_2']['name'];
					}
				}
				
				// material
				if(isset($_FILES['avatar_add_material_2'])){
					$uploaddir = 'bundles/materials/';
					if(!is_dir($uploaddir)){
						mkdir($uploaddir);
					}
					
					$uploadfile = $uploaddir . basename($_FILES['avatar_add_material_2']['name']);
					
					if (move_uploaded_file($_FILES['avatar_add_material_2']['tmp_name'], $uploadfile)) {
						$material_2 = $_FILES['avatar_add_material_2']['name'];
					}
				}
				
				
				$avatar_data_2 = array('element_2'				=> $element_2, 
															 'material_2'				=> $material_2);
				
				$avatar_data = array_merge((array)$avatar_data, (array)$avatar_data_2);

			}
			
			// simpan di db
			// avatar_add_tipe, avatar_add_name
			$avatar_id = $lilo_mongo->insert($avatar_data);
			
			$lilo_mongo->update($avatar_data, array_merge($avatar_data, array('lilo_id' => (string)$avatar_id)), array("multiple" => false) );
			
		}

		header("Location: " . $_SESSION['basepath'] . "asset/admin/avatar");
		exit;

	}
	
	// dapatkan semua level dari Assets.Level

	// sampe senee....
	// Assets.Avatar: lilo_id, tipe, name, element, material, preview_image
	// 		tipe: head, face_part_[eye_brows, eyes, lip], hair, pants, shoes, top_body

	// dapatkan semua bagian avatar berdasar tipe
	$avatar_array = array();
	$tipe_array = array('face', 'face_part_eye_brows', 'face_part_eyes', 'face_part_lip', 'hair', 'pants', 'shoes', 'body', 'top', 'hand');

	foreach($tipe_array as $tipe_){
		$var_name = $tipe_ . "_array";
		${$var_name} = array();
		$avatar_cursor = $lilo_mongo->find(array('tipe' => $tipe_));
		
		while($curr = $avatar_cursor->getNext()){
			${$var_name}[] = array(	'id' => $curr['lilo_id'], 
															'gender' => $curr['gender'], 
															'tipe' => $curr['tipe'], 
															'name' => $curr['name'], 
															'element' => $curr['element'],
															'material' => $curr['material'],
															'preview_image' => $curr['preview_image']);
		}
	
		$avatar_array[$tipe_] = ${$var_name};
	}

	$html = '';
	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->avatar_array = $avatar_array;
	
	$template->element_dir = $_SESSION['element_dir'];
	$template->material_dir = $_SESSION['material_dir'];
	$template->preview_dir = $_SESSION['preview_dir'];

	$html = $template->render("modules/002_asset_management/templates/asset_admin_avatar.php");

	$html = ui_admin_default(NULL, $html);

	return $html;

}

function asset_admin_avatar_categorized(){
	// versi terkategorisasi dari asset_admin_avatar()
	
	
}

function asset_admin_avatar_delete($criteria = NULL){
	// criteria: 
	// face__female_female_head__
	// pants__female_female_longpants_medium__
	// top__female_female_longshirt_medium__
	if(!isset($criteria)){
		$criteria = func_arg(0);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');

	$criteria_expl = explode('__', $criteria);
	
	$tipe = $criteria_expl[0];
	$element = $criteria_expl[1] . '.unity3d';
	$material = $criteria_expl[2];

	if(trim($material) != ''){
		$criteria_array = array('tipe' => $tipe, 'element' => $element, 'material' => $material . '.unity3d');
	} else {
		$criteria_array = array('tipe' => $tipe, 'element' => $element);
	}
//	return print_r($criteria_array, true);
	$lilo_mongo->delete($criteria_array);
	
	return "1";

}

function asset_admin_avatar_detail($args = NULL){
	if(!isset($args)){
		$args = $_REQUEST;
	}
	
	extract($args);	// lilo_id, misal: 4e4f4fe0c1b4ba4409000003
									// atau
									// avatar_part, misal: hair__male_hair-1__male_hair-1_brown

	$criteria = array();
	if(trim($lilo_id) != ''){
		$criteria = array('lilo_id' => $lilo_id);
	} else if(trim($avatar_part) != ''){
		$avatar_part_expl = explode('__', $avatar_part);
		//	"element" : "male_hair-1.unity3d", "material" : "male_hair-1_blond.unity3d"
		$tipe = (string)$avatar_part_expl[0];
		$element = (string)$avatar_part_expl[1] . '.unity3d';
		$material = (string)$avatar_part_expl[2] . '.unity3d';
		$criteria = array('tipe' => $tipe, 'element' => $element, 'material' => $material);
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Avatar');
	
	$result = $lilo_mongo->findOne($criteria);
	
	return json_encode($result);
	
}

function asset_admin_npc(){
	// list of npc, CRUD
	$ajax = func_arg(0);
	
}

function asset_admin_dialog_tree(){
	
}

function asset_admin_level(){
	// CRUD for WORLD

	$ajax = func_arg(0);

	// dapatkan semua level dari Assets.Level
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');

	$level_array = $lilo_mongo->find();

	$html = '';
	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->level_array = $level_array;
	$template->add_level_form = asset_admin_level_add('ajax');

	$html = $template->render("modules/002_asset_management/templates/asset_admin_level.php");
	if(trim($ajax) == 'ajax'){
		
	} else {
		$html = ui_admin_default(NULL, $html);
	}

	return $html;
	
}

function asset_admin_level_exist($level_name = NULL){
	if(!isset($level_name)){
		$arg_0 = func_arg(0);
		$level_name = trim($arg_0) != '' ? trim($arg_0) : $_REQUEST['level_name'];
	}
	
	if(trim($level_name) == ''){
		return 'EMPTYNAME';
	}
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');
	
	$data_exist = $lilo_mongo->findOne(array('name' => $level_name));
	
	if(is_array($data_exist) && count($data_exist) > 0){
		return '1';
	}
	
	return '0';
}

function asset_admin_level_detail($level_to_show = NULL){
	$level_id = isset($level_to_show) ? $level_to_show : func_arg(0);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');

	$level_detail = $lilo_mongo->findOne(array('lilo_id' => $level_id));

	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->level_detail = $level_detail;

	return $template->render("modules/002_asset_management/templates/asset_admin_level_detail.php");
}

function asset_admin_level_delete($level_to_delete = NULL){
	$level_id = isset($level_to_delete) ? $level_to_delete : func_arg(0);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');
	
	$lilo_mongo->remove(array('lilo_id' => $level_id));

	// hapus semua Channel terkait
	$lilo_mongo->selectCollection('Channel');
	$lilo_mongo->remove(array('level_id' => $level_id));

	return TRUE;
}

function asset_admin_level_update(){
	$lilo_id 							= $_POST['lilo_id'];
	$name 								= $_POST['level_detail_name'];
	$server_ip 						= $_POST['server_ip'];
	$server_port 					= $_POST['server_port'];
	$channel_number 			= $_POST['channel_number'];
	$max_ccu_per_channel 	= $_POST['max_ccu_per_channel'];
	$world_size_x 				= $_POST['world_size_x'];
	$world_size_y 				= $_POST['world_size_y'];
	$interest_area_x 			= $_POST['interest_area_x'];
	$interest_area_y 			= $_POST['interest_area_y'];
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');
	
	asset_admin_level_update_channel($lilo_id, $name, $channel_number);

	$lilo_mongo->update_set(array('lilo_id' => $lilo_id),
												 array('name' => $name, 'lilo_id' => $lilo_id, 'server_ip' => $server_ip, 'server_port' => $server_port, 
															 'channel_number' => $channel_number, 'max_ccu_per_channel' => $max_ccu_per_channel, 
															 'world_size_x' => $world_size_x, 'world_size_y' => $world_size_y, 
															 'interest_area_x' => $interest_area_x, 'interest_area_y' => $interest_area_y)
												 );

	
	return "1";
	
}

function asset_admin_level_add($ajax = NULL){
	$submitted = $_POST['submitted'];
	$ajax = isset($ajax) ? $ajax : func_arg(0);
	
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');

	
	if($submitted){
		$level_name = trim($_POST['asset_admin_level_add_name']);
		$asset_admin_level_add_tag = trim($_POST['asset_admin_level_add_tag']);
		if($level_name == ''){
			die("Level Name should not be empty.");
		}
		
		// hapus dulu data level yg dengan nama yg sama, $level_name
		// asumsinya, semua yg masuk sini sudah melalui konfirmasi di klien tentang nama level yg sama
		$lilo_mongo->remove(array('name' => $level_name));
		
		// tangani file upload:
		// - upload ke directory user_generated_content/level
		$uploaddir = 'user_generated_data/level/' . $username . '/';
		if(!is_dir($uploaddir)){
			mkdir($uploaddir);
		}
		
		$time_marker = $_SERVER['REQUEST_TIME'];
		
		$uploadfile = $uploaddir . $time_marker . '...' . basename($_FILES['asset_admin_level_add_file_level']['name']);
		// string '...' adalah marker
		
		$extractdir = $uploaddir . '/' . $time_marker;
		if(!is_dir($extractdir)){
			mkdir($extractdir);
		}
		
		if (move_uploaded_file($_FILES['asset_admin_level_add_file_level']['tmp_name'], $uploadfile)) {
			// insert to database
		
			// - extract ke directory user_generated_content/level/[username]/<time_marker>...filename
			$zip = new ZipArchive;
			$res = $zip->open($uploadfile);
			if ($res === TRUE) {
				$zip->extractTo($extractdir);
				$zip->close();
				$_SESSION['status_msg'] = "File level berhasil diupload";
				
				// baca file hasil extract, masukkan informasinya ke DB
				if(file_exists($extractdir . '/level.ini')){
					require_once('libraries/protocolbuf/message/pb_message.php');
					require_once('user_generated_data/proto/pb_proto_Level.php');

					// sesuaikan variable2 di object ini dengan asset yg diupload
					$ini_array = parse_ini_file($extractdir . '/level.ini', true);
					if(is_array($ini_array) && count($ini_array)){
						foreach($ini_array as $key => $val){
							$asset_file = /*$_SESSION['basepath'] . */$extractdir . '/' . $ini_array[$key]['objectName'] . '.unity3d';
							$asset_file = str_replace('//', '/', $asset_file);
							$asset_file = str_replace('//', '/', $asset_file);
							$ini_array[$key]['asset_file'] = $asset_file;
							
							$position_xyz = $ini_array[$key]['position'];
							$rotation_xyz = $ini_array[$key]['rotation'];
							
							$position_xyz_expl = explode(',', $position_xyz);
							$position_x = $position_xyz_expl[0];
							$position_y = $position_xyz_expl[1];
							$position_z = $position_xyz_expl[2];
							
							$ini_array[$key]['position_x'] = $position_x;
							$ini_array[$key]['position_y'] = $position_y;
							$ini_array[$key]['position_z'] = $position_z;
							
							$rotation_xyz_expl = explode(',', $rotation_xyz);
							$rotation_x = $rotation_xyz_expl[0];
							$rotation_y = $rotation_xyz_expl[1];
							$rotation_z = $rotation_xyz_expl[2];
							
							$ini_array[$key]['rotation_x'] = $rotation_x;
							$ini_array[$key]['rotation_y'] = $rotation_y;
							$ini_array[$key]['rotation_z'] = $rotation_z;
							
						}
					}
					
					
					$preview_file = '';
					
					if(isset($_FILES['asset_admin_level_add_file_preview_level']) && trim($_FILES['asset_admin_level_add_file_preview_level']) != ''){
						$uploaddir_ = 'user_generated_data/level_preview/' . $username . '/';
						if(!is_dir($uploaddir_)){
							mkdir($uploaddir_);
						}
						
						// upload asset_admin_level_add_file_preview_level dan asset_admin_level_add_file_skybox
						$uploadfile_ = $uploaddir_ . $time_marker . '...' . basename($_FILES['asset_admin_level_add_file_preview_level']['name']);
						// string '...' adalah marker
						
						if (move_uploaded_file($_FILES['asset_admin_level_add_file_preview_level']['tmp_name'], $uploadfile_)) {
							$preview_file = $uploadfile_;
						}
					}
					
					$skybox_file = '';
					
					if(isset($_FILES['asset_admin_level_add_file_skybox']) && trim($_FILES['asset_admin_level_add_file_skybox']) != ''){
						$uploaddir_ = 'user_generated_data/level_skybox/' . $username . '/';
						if(!is_dir($uploaddir_)){
							mkdir($uploaddir_);
						}
						
						// upload asset_admin_level_add_file_preview_level dan asset_admin_level_add_file_skybox
						$uploadfile_ = $uploaddir_ . $time_marker . '...' . basename($_FILES['asset_admin_level_add_file_skybox']['name']);
						// string '...' adalah marker
						
						if (move_uploaded_file($_FILES['asset_admin_level_add_file_skybox']['tmp_name'], $uploadfile_)) {
							$skybox_file = $uploadfile_;
						}
					}
					


					
					// simpan object ini ke DB
					$lilo_mongo = new LiloMongo();
					$lilo_mongo->selectDB('Assets');
					$lilo_mongo->selectCollection('Level');	// name, uploader, upload_time, preview_image, object_serialized

					// 150811: 
					//	table Level, field baru:
					//		server_ip, server_port, channel_number, max_ccu_per_channel, world_size_[x, y], interest_area_[x, y]
					//	table Channel:
					//		level, name, current_ccu
					
					$server_ip 						= $_POST['asset_admin_level_add_server_ip'];
					$server_port 					= $_POST['asset_admin_level_add_server_port'];
					$channel_number 			= $_POST['asset_admin_level_add_channel_number'];
					$max_ccu_per_channel 	= $_POST['asset_admin_level_add_max_ccu_per_channel'];
					$world_size_x 				= $_POST['asset_admin_level_add_world_size_x'];
					$world_size_y 				= $_POST['asset_admin_level_add_world_size_y'];
					$interest_area_x 			= $_POST['asset_admin_level_add_interest_area_x'];
					$interest_area_y 			= $_POST['asset_admin_level_add_interest_area_y'];
					

					$level = array(	'name' 		=> $level_name, 
												 	'assets' 	=> $ini_array, 
													'tags' 		=> $asset_admin_level_add_tag,
													
													'server_ip'							=> $server_ip,
													'server_port'						=> $server_port,
													'channel_number'				=> $channel_number,
													'max_ccu_per_channel'		=> $max_ccu_per_channel,
													'world_size_x'					=> $world_size_x,
													'world_size_y'					=> $world_size_y,
													'interest_area_x'				=> $interest_area_x,
													'interest_area_y'				=> $interest_area_y,
													
													'preview_file'					=> $preview_file,
													'skybox_file'						=> $skybox_file
													);

					$level_id = $lilo_mongo->insert($level);
					$lilo_mongo->update($level, array_merge($level, array('lilo_id' => (string)$level_id)), array("multiple" => false) );

					// Channel...
					asset_admin_level_create_channel((string)$level_id, $level_name, $channel_number);

					
					unset($ini_array);
					
					// NPC Management
					if(file_exists($extractdir . '/npc.ini')){
						$ini_array = parse_ini_file($extractdir . '/npc.ini', true);
						if(is_array($ini_array) && count($ini_array)){
							foreach($ini_array as $key => $val){
								// masukkan data npc (sementara baru NPC name dan level name) ke db
								$lilo_mongo->selectCollection('NPC');
								$NPC = array('level' => $level_name, 'name' => $ini_array[$key]['name'], 'displayName' => $ini_array[$key]['displayName']);
								$npc_id = $lilo_mongo->insert($NPC);
								// function update($array_criteria, $array_newobj, $array_options = array("multiple" => true))
								$lilo_mongo->update($NPC, array_merge($NPC, array('lilo_id' => (string)$npc_id)), array("multiple" => false) );
							}
						}
					} else {
						$_SESSION['error_msg'] = "File npc.ini tidak ditemukan.";
					}

					$_SESSION['status_msg'] = "File level berhasil diupload";
//					header("Location: " . $_SESSION['basepath'] . 'asset/admin/level');

				} else {
					$_SESSION['error_msg'] = "File level.ini tidak ditemukan";
//					header("Location: " . $_SESSION['basepath'] . 'asset/admin/level');
				}
				
				
				// redirect ke...?
			} else {
				$_SESSION['status_msg'] = "File level gagal diupload";
				// redirect ke...?
//				header("Location: " . $_SESSION['basepath'] . 'asset/admin/level');
			}
		
		} else {
			die("Possible file upload attack!\n");
		}

		header("Location: " . $_SESSION['basepath'] . 'asset/admin/level');
		
	} // end if submitted
	
	// form upload
	$html = '';
	if(trim($ajax) == 'ajax'){
		$template = new Template();
		$template->basepath = $_SESSION['basepath'];
		$template->ajax = 'ajax';
		$html = $template->render("modules/002_asset_management/templates/asset_admin_level_add.php");
	} else {
		$template->ajax = '';
		$html = ui_admin_default("modules/002_asset_management/templates/asset_admin_level_add.php");
	}

	return $html;
}

function asset_admin_level_list(){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');

	$level_cursor = $lilo_mongo->find();

	$level_array = array();
	while($curr = $level_cursor->getNext()){
		$level_array[] = array('id' => $curr['lilo_id'], 'name' => $curr['name']);
	}

//	return print_r($level_array, true);
	$level_json = json_encode($level_array);
	$level_json = str_replace('"', "'", $level_json);
	return $level_json;
}

function asset_admin_channel_delete($channel_id = NULL){
	$channel_id_ = isset($channel_id) ? $channel_id : func_arg(0);
	
	// pastikan channel ini tidak ada yg sedang online
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Channel');
	
	$channel = $lilo_mongo->findOne(array('lilo_id' => $channel_id_));

	if(intval($channel['current_ccu']) == 0){
		$lilo_mongo->remove(array('lilo_id' => $channel_id_));
		
		// update Level.channel_number
		$level_id = $channel['level_id'];
		
		$lilo_mongo->selectCollection('Level');
		$level = $lilo_mongo->findOne(array('lilo_id' => $level_id));
		
		$channel_number = $level['channel_number'] - 1;
		if($channel_number < 0){
			$channel_number = 0;
		}
		
		$lilo_mongo->update_set(array('lilo_id' => $level_id), array('channel_number' => $channel_number));
		
		return "1";
	}

	return "Channel tidak dapat dihapus. Masih ada pemain yang menggunakan channel ini.";
}

function asset_admin_channel_editor($level_id = NULL){
	$level_id_ = isset($level_id) ? $level_id : func_arg(0);

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Channel');
	
	$channels_array = array();
	$channel_cursor = $lilo_mongo->find(array('level_id' => $level_id_));
	
	while($curr = $channel_cursor->getNext()){
		$channels_array[] = $curr;
	}
	
	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->channels_array = $channels_array;
	// sampe seneee....
	$html = $template->render("modules/002_asset_management/templates/asset_admin_channel_editor.php");
	return $html;
}

function asset_admin_channel_list($level_id = NULL){
	$level_id_ = isset($level_id) ? $level_id : func_arg(0);

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Channel');

	$channels_array = array();
	$channel_cursor = $lilo_mongo->find(array('level_id' => $level_id_));
	
	while($curr = $channel_cursor->getNext()){
		$channels_array[] = $curr;
	}
	
	$channels_array = json_encode($channels_array);
	return str_replace('"', "'", $channels_array);
}

function asset_admin_level_channel_list(){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Level');

	$level_cursor = $lilo_mongo->find();

	$level_array = array();
	while($curr = $level_cursor->getNext()){
		$level_array[] = $curr;
	}
	
	$lilo_mongo->selectCollection('Channel');

	for($idx = 0; $idx < count($level_array); $idx++){
		$channels_array = array();
		$channel_cursor = $lilo_mongo->find(array('level_id' => $level_array[$idx]['lilo_id']));
		
		while($curr = $channel_cursor->getNext()){
			$channels_array[] = $curr;
		}
		
		$level_array[$idx]['channels'] = $channels_array;
	}
	
	$level_json = json_encode($level_array);
	$level_json = str_replace('"', "'", $level_json);
	return $level_json;
//	return "<pre>" . print_r($level_array, true) . "</pre>";
}

//	asset_admin_level_update_channel($lilo_id, $name, $channel_number);
function asset_admin_level_update_channel($level_id = '', $level_name = '', $channel_number = 1){
	// dapatkan jumlah channel sebelumnya
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Channel');

	$channel_array = $lilo_mongo->find(array('level_id' => $level_id));
	
	$old_channel_number = count($channel_array);
	
	if($old_channel_number < $channel_number){
		// add
	} else if($old_channel_number > $channel_number){
		// delete
	}
	
}

function asset_admin_level_create_channel($level_id = '', $level_name = '', $channel_number = 1){
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Channel');

	if(intval($channel_number) == 0){
		$channel_number = 1;
	}
	
	for($idx = 1; $idx <= $channel_number; $idx++){
		$new_channel = array('level_id' => (string)$level_id, 'name' => $level_name . "_" . $idx, 'current_ccu' => 0);
		$new_channel_id = $lilo_mongo->insert($new_channel);
		$lilo_mongo->update($new_channel, array_merge($new_channel, array('lilo_id' => (string)$new_channel_id)), array("multiple" => false) );
	}

}

function asset_admin_proto_to_class($proto_file = NULL){
	require_once('libraries/protocolbuf/parser/pb_parser.php');
	
	if(!isset($proto_file)){
		$proto_file = func_arg(0);
	}
	
	$proto_file = "user_generated_data/proto/" . $proto_file;
	$test = new PBParser();

	if(file_exists($proto_file)){
		$test->parse($proto_file, 'user_generated_data/proto/');
		echo 'File parsing done!';
	} else {
		echo 'Stupid';
	}
}

function asset_admin_test_proto(){
	define("DIALOG", 1);
	
	require_once('libraries/protocolbuf/message/pb_message.php');
	
	require_once('modules/002_asset_management/protocol_buffer/pb_proto_DialogTree.php');
	
	$dialogOption01 = new DialogOption();
	$dialogOption01->set_nextDialog(2);
	$dialogOption01->set_text('Kambing');
	
	$dialogOption02 = new DialogOption();
	$dialogOption02->set_nextDialog(3);
	$dialogOption02->set_text('Ayam');
	
	$dialogOption03 = new DialogOption();
	$dialogOption03->set_nextDialog(0);
	$dialogOption03->set_text('OK. Guling2 aja semau lo...');
	
	$dialogOption04 = new DialogOption();
	$dialogOption04->set_nextDialog(0);
	$dialogOption04->set_text('OK deh, ayam jg ga apa2...');
	
	$dialogData01 = new DialogData();
	$dialogData01->set_id(1);
	$dialogData01->set_DialogType(DIALOG);
	$dialogData01->set_text("Anda siapa?");
	$dialogData01->set_OptionList(0, $dialogOption01);
	$dialogData01->set_OptionList(1, $dialogOption02);
//	$dialogData01->values["4"] = array($dialogOption01, $dialogOption02);
	
	$dialogData02 = new DialogData();
	$dialogData02->set_id(2);
	$dialogData02->set_DialogType(DIALOG);
	$dialogData02->set_text("Kambing Guling!!!");
	$dialogData02->set_OptionList(0, $dialogOption03);
	
	$dialogData03 = new DialogData();
	$dialogData03->set_id(3);
	$dialogData03->set_DialogType(DIALOG);
	$dialogData03->set_text("Ayam kampu** ?");
	$dialogData03->set_OptionList(0, $dialogOption04);
	
	$dialogTree01 = new DialogTree();
	$dialogTree01->set_Dialogs(0, $dialogData01);
	$dialogTree01->set_Dialogs(1, $dialogData02);
	$dialogTree01->set_Dialogs(2, $dialogData03);
//	$dialogTree01->set_currentDialog(1);

//	return print_r($dialogTree01);exit;

//	return "<pre>" . print_r($dialogTree01) . "<pre>";
	
	$serialized_string = $dialogTree01->SerializeToString();
	$text = print_r($serialized_string, true);
	
	return $text;	
}


function asset_admin_inventory(){
	// CRUD for Assets.Inventory
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Assets');
	$lilo_mongo->selectCollection('Inventory');
	
	// delete
	$delete = trim($_REQUEST['delete']);
	if($delete != ''){
		$lilo_mongo->delete(array('lilo_id' => $delete));
		return "1";
	}
	
	// detail
	$detail = trim($_REQUEST['detail']);
	if($detail != ''){
		$data = $lilo_mongo->findOne(array('lilo_id' => $detail));
		return json_encode($data);
	}
	
	// new_inventory
	$new_inventory = trim($_REQUEST['new_inventory']);
	if($new_inventory == "1"){
		extract($_POST);	// tipe, description, icon
		if(trim($tipe) != ''){
			$inventory_data = array('tipe' => $tipe, 'description' => $description, 'icon' => $icon);
			$lilo_id_ = $lilo_mongo->insert($inventory_data);
			
			$lilo_mongo->update($inventory_data, array_merge($inventory_data, array('lilo_id' => (string)$lilo_id_)), array("multiple" => false));
		}
	}

	// dapatkan data Assets.Inventory

	$inventory_array = $lilo_mongo->find();

	$html = '';
	$template = new Template();
	$template->basepath = $_SESSION['basepath'];
	
	$template->inventory_array = $inventory_array;

	$html = $template->render("modules/002_asset_management/templates/asset_admin_inventory.php");
	$html = ui_admin_default(NULL, $html);

	return $html;
	
}


?>