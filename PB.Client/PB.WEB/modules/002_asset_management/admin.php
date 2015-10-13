<?php
include_once('libraries/LiloMongo.php'); 
include_once('libraries/Template.php');

include_once('modules/000_user_interface/admin.php');

set_time_limit(300);

function asset_admin_default(){
  return "asset admin default";
}

function asset_admin_animation($op = NULL, $rettype = NULL){
  // CRUD for Animation
  // Animation: lilo_id, name, description, gender, animation_file, preview_file
  
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Animation');
  
  $op = isset($op) ? $op : func_arg(0);
  $op = strtolower(trim($op));  // read [default], add, edit, delete
  
  switch($op){
    case 'delete':
      $lilo_id = func_arg(1);
      if(trim($lilo_id) != ''){
        $lilo_mongo->remove(array('lilo_id' => $lilo_id));
      }
      
      return '1';
      
      break;

    case 'edit':
      extract($_REQUEST);
      
			$data = array('name' => $name, 'description' => $description, 'gender' => strtolower($gender), 'permission' => strtolower($permission)/*, 'animation_file' => $animation_file_, 'preview_file' => $preview_file_*/);
			
      if(trim($lilo_id) != ''){
        // upload animation_file dan preview_file
        $animation_file_ = '';
        if(isset($_FILES['animation_file'])){
          $uploaddir = 'bundles/animations';
          if(!is_dir($uploaddir)){
            mkdir($uploaddir);
          }
          
          $uploadfile = $uploaddir . basename($_FILES['animation_file']['name']);
          
          if (move_uploaded_file($_FILES['animation_file']['tmp_name'], $uploadfile)) {
            $animation_file_ = $_FILES['animation_file']['name'];
						$data = array_merge((array)$data, array('animation_file' => $animation_file_));
          }
        }
        
        $preview_file_ = '';
        if(isset($_FILES['preview_file'])){
          $uploaddir = 'bundles/animations/preview';
          if(!is_dir($uploaddir)){
            mkdir($uploaddir);
          }
          
          $uploadfile = $uploaddir . basename($_FILES['preview_file']['name']);
          
          if (move_uploaded_file($_FILES['preview_file']['tmp_name'], $uploadfile)) {
            $preview_file_ = $_FILES['preview_file']['name'];
						$data = array_merge((array)$data, array('preview_file' => $preview_file_));
          }
        }
        
        $criteria = array('lilo_id' => $lilo_id);
				
				
				
        $lilo_mongo->update_set($criteria, $data);
      }
			
			header("Location: " . $_SESSION['basepath'] . 'asset/admin/animation');
			exit;
//			return "1";

      break;
      
    case 'add':
      extract($_REQUEST);
      
      if(trim($name) == ''){
        return "Name should not be empty!";
      }
      
      $animation_file_ = '';
      if(isset($_FILES['animation_file'])){
        $uploaddir = 'bundles/animations/';
        if(!is_dir($uploaddir)){
          mkdir($uploaddir);
        }
        
        $uploadfile = $uploaddir . basename($_FILES['animation_file']['name']);
        
        if (move_uploaded_file($_FILES['animation_file']['tmp_name'], $uploadfile)) {
          $animation_file_ = $_FILES['animation_file']['name'];
        }
      }
      
      $preview_file_ = '';
      if(isset($_FILES['preview_file'])){
        $uploaddir = 'bundles/animations/preview/';
        if(!is_dir($uploaddir)){
          mkdir($uploaddir);
        }
        
        $uploadfile = $uploaddir . basename($_FILES['preview_file']['name']);
        
        if (move_uploaded_file($_FILES['preview_file']['tmp_name'], $uploadfile)) {
          $preview_file_ = $_FILES['preview_file']['name'];
        }
      }
      
      $data = array('name' => $name, 'description' => $description, 'gender' => strtolower($gender), 'permission' => strtolower($permission), 'animation_file' => $animation_file_, 'preview_file' => $preview_file_);
    	$anim_id = $lilo_mongo->insert($data);
			
			$lilo_mongo->update($data, array_merge($data, array('lilo_id' => (string)$anim_id)), array("multiple" => false) );

			
			header("Location: " . $_SESSION['basepath'] . 'asset/admin/animation');
			exit;
//			return "1";
		
      break;
    
		case 'detail':	// asset/admin/animation/detail/2394fdoiuo4iu5o23ifflgk4/json
      $lilo_id = func_arg(1);
			
			$retval = array();
      if(trim($lilo_id) != ''){
        $retval = $lilo_mongo->findOne(array('lilo_id' => $lilo_id));
      }
      
			$rettype = isset($rettype) ? $rettype : func_arg(2);
			if(strtolower(trim($rettype)) == 'json'){
				$retval = json_encode($retval);
			}
			
      return $retval;
      
      break;
			
		case 'getall':	// asset/admin/animation/getall/json
										// asset/admin/animation/getall
			$anim_cursor = $lilo_mongo->find();
			
			$anim_array = array();
			while($curr = $anim_cursor->getNext()){
				$anim_array[] = array('lilo_id' => $curr['lilo_id'], 
															'name' => $curr['name'], 
															'description' => $curr['description'], 
															'gender' => $curr['gender'], 
															'permission' => $curr['permission'], 
															'animation_file' => $curr['animation_file'], 
															'preview_file' => $curr['preview_file']);
			}
			
			$rettype = isset($rettype) ? $rettype : func_arg(1);	// asset/admin/animation/read/json
		
			if(strtolower(trim($rettype)) == 'json'){
				$anim_array = json_encode($anim_array);
			}
			
			return $anim_array;
			
			break;
  }
	
	
	// default: op == read
	
  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  
	
	$anim_array = asset_admin_animation('getall');
  $template->anim_array = $anim_array;
  
  $template->animation_dir = $_SESSION['animation_dir'];
  $template->animation_preview_dir = $_SESSION['animation_preview_dir'];

  $html = $template->render("modules/002_asset_management/templates/asset_admin_animation.php");

  $html = ui_admin_default(NULL, $html);

  return $html;

}

function asset_admin_avatar_set_size(){
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Avatar');

	$avatar_cursor = $lilo_mongo->find();
	
	$avatar_update = array();
	while($curr = $avatar_cursor->getNext()){
		//	male_pants_medium.unity3d			-> medium
		//	male_short_pant_fat.unity3d		-> fat
		//	male_body_legs_thin.unity3d		-> thin
		//	male_body_hand.unity3d				-> thin fat medium
		$element = $curr['element'];
		$size_ = '';
		if(stripos($element, '_thin')){
			$size_ = 'small';
		} else if(stripos($element, '_medium')){
			$size_ = 'medium';
		} else if(stripos($element, '_fat')){
			$size_ = 'big';
		} else {
			$size_ = 'small medium big';
		}
		
		$curr['size'] = $size_;
		$avatar_update[] = $curr;
	}

	$retval = '';
	foreach($avatar_update as $au){
		$criteria = array('lilo_id' => $au['lilo_id']);
//		$retval .= print_r($criteria, true) . '<br>' . print_r($au['size'], true) . '<hr>';
//		$lilo_mongo->update_set($criteria, array('size' => $au['size']));
//		$retval .= "&bull; ". $au['lilo_id'] ." terupdate<br>";
		if($au['tipe'] != 'shoes'){
			$retval .= "db.Avatar.update({lilo_id: '".$au['lilo_id']."'}, {'\$set':{size:'".$au['size']."'}});<br>";
		}
	}
	
	return $retval;
}

function asset_admin_avatar(){
  // CRUD for AVATAR
  
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Avatar');

  
  $add_new_avatar = $_POST['add_new_avatar'];
  
  if($add_new_avatar){
    extract($_REQUEST);

    $element = '';  //$_FILES['avatar_add_element'];
    $material = '';  //$_FILES['avatar_add_material'];
    $preview_image = '';  //$_FILES['avatar_add_preview_image'];

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
      
      // ------ REVISI 100511 ------	????
      // tambah table Assets.AvatarCategories
      // tambah field 'categories' di table Assets.Avatar
      
      $avatar_data = array('tipe'            	=> $avatar_add_tipe, 
                           'name'		=> $avatar_add_name, 
                           'size'		=> $avatar_add_size,
                           'element'        	=> $element, 
                           'brand'        	=> $editor_div_brand, 
                           'category'           => $avatar_add_category,
                           'payment'        	=> $editor_div_payment, 
                           'material'        	=> $material, 
                           'preview_image'  	=> $preview_image,
                           'color'  		=> $avatar_add_color,
                           'gender'          	=> $gender
          );

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
        
        
        $avatar_data_2 = array('element_2'        => $element_2, 
                               'material_2'        => $material_2);
        
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
  //     tipe: head, face_part_[eye_brows, eyes, lip], hair, pants, shoes, top_body

  // dapatkan semua bagian avatar berdasar tipe
  $avatar_array = array();
  $tipe_array = array('face', 'hat', 'face_part_eye_brows', 'face_part_eyes', 'face_part_lip', 'hair', 'pants', 'shoes', 'body', 'top', 'hand');

  foreach($tipe_array as $tipe_){
    $var_name = $tipe_ . "_array";
    ${$var_name} = array();
    $avatar_cursor = $lilo_mongo->find(array('tipe' => $tipe_), 0, array('gender' => 1, 'name' => 1, 'category' => 1));
    
    while($curr = $avatar_cursor->getNext()){/*if($tipe_ == 'body'){print($curr['name'] . "<br />");}*/
      ${$var_name}[] = array(  'id' => $curr['lilo_id'], 
                              'gender' => $curr['gender'], 
                              'tipe' => $curr['tipe'], 
                              'size' => $curr['size'], 
                              'name' => $curr['name'], 
                              'element' => $curr['element'],
                              'material' => $curr['material'],
                              'preview_image' => $curr['preview_image']);
    }//if($tipe_ == 'body'){exit;}
  
    $avatar_array[$tipe_] = ${$var_name};
  }

  $html = '';
  $template = new Template();
  $lilo_mongo->selectCollection('Brand');
  $listbrand=$lilo_mongo->find();
  $lilo_mongo->selectCollection('Category');
  $listcategory=$lilo_mongo->find(array("tipe"=>"face"));
  $template->basepath = $_SESSION['basepath'];
  $template->brand=$listbrand;
  $template->category=$listcategory;
  $template->avatar_array = $avatar_array;  
  $template->element_dir = $_SESSION['element_dir'];
  $template->material_dir = $_SESSION['material_dir'];
  $template->preview_dir = $_SESSION['preview_dir'];
	
	// dari $_SESSION['pop_last_edited_avatar_element'], set default selected tab
	// setelah digunakan, langsung unset
	
	$tabs = array('face' => 0, 'hat' => 1, 'face_part_eye_brows' => 2, 'face_part_eyes' => 2, 'face_part_lip' => 2, 'hair' => 3, 'hand' => 4, 'pants' => 5, 'shoes' => 6, 'body' => 7);
	$template->active_tabs = intval($tabs[$_SESSION['pop_last_edited_avatar_element']]);
	
	$face_part_tabs = array('face_part_eye_brows' => 0, 'face_part_eyes' => 1, 'face_part_lip' => 2);
	$template->active_face_part_tabs = intval($face_part_tabs[$_SESSION['pop_last_edited_avatar_element']]);

  $html = $template->render("modules/002_asset_management/templates/asset_admin_avatar.php");

  $html = ui_admin_default(NULL, $html);

  return $html;

}

function asset_admin_avatar_categorized(){
  // versi terkategorisasi dari asset_admin_avatar()
    $op = isset($op) ? $op : func_arg(0);
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Category');
  $listcategory=$lilo_mongo->find(array("tipe"=>$op));
  foreach($listcategory as $dt)
  {
      echo "<option value='".$dt['name']."'>".$dt['name']."</option>";;
  }
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
  $element = $criteria_expl[1];
  $material = $criteria_expl[2];
// write_log(array('log_text' => "tipe: $tipe, element: $element, material: $material, criteria: $criteria"));
  if(trim($material) != '' && trim($element) != ''){
    $criteria_array = array('tipe' => $tipe, 'element' => $element . '.unity3d', 'material' => $material . '.unity3d');
  } else if(trim($material) != ''){
    $criteria_array = array('tipe' => $tipe, 'material' => $material . '.unity3d');
	} else {
    $criteria_array = array('tipe' => $tipe, 'element' => $element . '.unity3d');
  }
//  return print_r($criteria_array, true);
// write_log(array('log_text' => "criteria_array: " . print_r($criteria_array, true)));
  $lilo_mongo->delete($criteria_array);
  
  return "1";

}

function asset_admin_avatar_update(){
	
	if(trim($_REQUEST['editor_div_name']) == ''){
		$_SESSION['pop_error_msg'][] = "Field <i>Name</i> should not be empty!";
		header("Location: " . $_SESSION['basepath'] . 'asset/admin/avatar');
		exit;
	}
	
	// die("<font size='+10'>Underwear! Eh, under construction...</font>");
	// hanya menerima _POST
//	write_log(array('log_text' => print_r($_FILES, true)));
//	write_log(array('log_text' => print_r($_REQUEST, true)));
	
	// dapatkan property current avatar item. ga perlu :)
	// $_FILES: "edit_" . field_name
	
	$edit_element_dir = $edit_element_2_dir = $_SESSION['element_rel_dir'];	// $basepath . "bundles/characters/";	// jangan lupa / di akhir
	$edit_material_dir = $edit_material_2_dir = $_SESSION['material_rel_dir'];	// $basepath . "bundles/materials/";	// jangan lupa / di akhir
	$edit_preview_image_dir = $_SESSION['preview_rel_dir'];	// $basepath . "bundles/preview_images/";	// jangan lupa / di akhir

//	write_log(array('log_text' => "$edit_element_dir, $edit_element_2_dir, $edit_material_dir, $edit_material_2_dir, $edit_preview_image_dir"));

//	exit;

	$update_data = array();
	
	foreach($_FILES as $key => $file){
		$uploaddir = ${$key . "_dir"};
		$fieldname = str_replace('edit_', '', $key);
		if(trim($file['name']) != ''){
			$uploadfile = $uploaddir . basename($file['name']);

			if (move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile)) {
				$update_data = array_merge((array)$update_data, array($fieldname => $_FILES[$key]['name']));
			} else {
				write_log(array('log_text' => "KOPLAK\n\r" . print_r($_FILES[$key], true)));
				write_log(array('log_text' => "KOPLOK\n\r" . print_r($uploadfile, true)));
			}

		}
	}

	foreach($_REQUEST as $key => $val){
		if(strpos($key, 'editor_div_') !== false){
			$fieldname = str_replace('editor_div_', '', $key);
			$update_data = array_merge((array)$update_data, array($fieldname => $val));
		}
	}
	
	write_log(array('log_text' => print_r($update_data, true)));

	/*
	Array
	(
			[element] => sunny_skybox.unity3d
			[material] => sunny_skybox.unity3d
			[preview_image] => level.preview.png
	)
	
	*/

  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Avatar');

	$criteria = array('lilo_id' => $_REQUEST['lilo_id']);

	$lilo_mongo->update_set($criteria, $update_data);

	$_SESSION['pop_last_edited_avatar_element'] = $update_data['tipe'];

	$_SESSION['pop_status_msg'][] = "Data updated successfully";
	header("Location: " . $_SESSION['basepath'] . 'asset/admin/avatar');

	exit;


	
}

function asset_admin_avatar_importer(){
	// mengimport data dari file csv ke table Assets.Avatar
	// field di csv:
	//	1. tipe:
	//		- face
	//		- hat
	//		- face_part_eye_brows
	//		- face_part_eyes
	//		- face_part_lip
	//		- hair
	//		- hand
	//		- pants
	//		- shoes
	//		- body
	//	2. name: "Baju Kuning ukuran L"
	//	3. size: [ "", "small", "medium", "big" ]
	//	4. element: "female_hat.unity3d"
	//	5. material: "female_hat_02.unity3d"
	//	6. preview_image: "hat.png"
	//	7. gender : [ "female", "male", "unisex" ]
	
//	$csv_file = "exp/TODO.230212.List.Asset.Avatar.dos.csv";
//	$csv_file = "exp/Update.Face.Part.280212.csv";
//	$csv_file = "exp/Update.Female.T-Shirt.280212.csv";
	$csv_file = "exp/Update.Female.Baloon.Sack.Dress.290212.csv";
	$csv_data = array();
	if (($handle = fopen($csv_file, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if(strlen($data[0]) > 0){
				$csv_data[] = $data;
			}
		}
		fclose($handle);
	}
	
	
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Avatar');

	
	for($idx = 1; $idx < count($csv_data); $idx++){
		$avatar_data = array();
		
		for($id = 0; $id < count($csv_data[$idx]); $id++){
			$key = $csv_data[0][$id];
			$val = $csv_data[$idx][$id];
			
			$avatar_data[$key] = $val;
		}

		$avatar_id = $lilo_mongo->insert($avatar_data);
		
		$lilo_mongo->update($avatar_data, array_merge($avatar_data, array('lilo_id' => (string)$avatar_id)), array("multiple" => false) );

		$avatar_data['lilo_id'] = $avatar_id;
		
		print("<pre>" . print_r($avatar_data, true) . "</pre><hr />" );

	}

	
}


function asset_admin_avatar_detail($args = NULL){
  if(!isset($args)){
    $args = $_REQUEST;
  }
  
  extract($args);  // lilo_id, misal: 4e4f4fe0c1b4ba4409000003
                  // atau
                  // avatar_part, misal: hair__male_hair-1__male_hair-1_brown

  $criteria = array();
  if(trim($lilo_id) != ''){
    $criteria = array('lilo_id' => $lilo_id);
  } else if(trim($avatar_part) != ''){
    $avatar_part_expl = explode('__', $avatar_part);
    //  "element" : "male_hair-1.unity3d", "material" : "male_hair-1_blond.unity3d"
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
function asset_admin_level_sorting()
{
  $dataresult = func_arg(0);
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Level');
  $level_array = $lilo_mongo->find();
  if($dataresult!='')
  {
      $level_array = $lilo_mongo->find(array("brand_id"=>$dataresult));
  }
  $datarerun=array();
  foreach($level_array as $curr)
  {  
      $datarerun[]=array(
          '_id'=>$curr['_id'],
          'name'=>$curr['name'],
          'preview_file'=>$curr['preview_file'],
          'brand_id'=>$curr['brand_id'],
          'assets'=>$curr['assets'],
          'source_file'=>$curr['source_file'],
          'skybox_file'=>$curr['skybox_file'],
          'audio_file'=>$curr['audio_file'],
      );
  }
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  $template->listdata = $datarerun;
  $html = $template->render("modules/002_asset_management/templates/asset_admin_list_level.php");
  return $html;
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
  $lilo_mongo->selectCollection('Brand');
  $listbrand=$lilo_mongo->find();
  $template->brand = $listbrand;
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

	if(isset($level_detail['skybox_file'])){
		$file_name_expl = explode('/', $level_detail['skybox_file']);
		$c = count($file_name_expl) - 1;
		$level_detail['skybox_file_originalname'] = $file_name_expl[$c];
	}

	if(isset($level_detail['audio_file'])){
		$file_name_expl = explode('/', $level_detail['audio_file']);
		$c = count($file_name_expl) - 1;
		$level_detail['audio_file_originalname'] = $file_name_expl[$c];
	}
    
    
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  $lilo_mongo->selectCollection('Brand');
  $listbrand=$lilo_mongo->find();
  $template->brand = $listbrand;
  $template->level_detail = $level_detail;

  return $template->render("modules/002_asset_management/templates/asset_admin_level_detail.php");
}

function asset_admin_level_delete($level_to_delete = NULL){
  $level_id = isset($level_to_delete) ? $level_to_delete : func_arg(0);
  
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Level');
  
	$asset_data = $lilo_mongo->findOne(array('lilo_id' => $level_id));
	
  $lilo_mongo->remove(array('lilo_id' => $level_id));

  // hapus semua Channel terkait
  $lilo_mongo->selectCollection('Channel');
  $lilo_mongo->remove(array('level_id' => $level_id));

	$dir = trim($asset_data['directory']);
	if($dir != '' && is_dir($dir)){
		rename($dir, $dir . '_DELETED');
	}


  return TRUE;
}

function asset_admin_level_update(){
  $lilo_id               = $_POST['lilo_id'];
  $name                 = $_POST['level_detail_name'];
  $server_ip             = $_POST['server_ip'];
  $server_port           = $_POST['server_port'];
  $channel_number       = $_POST['channel_number'];
  $max_ccu_per_channel   = $_POST['max_ccu_per_channel'];
  $world_size_x         = $_POST['world_size_x'];
  $world_size_y         = $_POST['world_size_y'];
  $interest_area_x       = $_POST['interest_area_x'];
  $interest_area_y       = $_POST['interest_area_y'];
  $brand                    = $_POST['edit_brand'];
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Level');
  
  

  $lilo_mongo->update_set(array('lilo_id' => $lilo_id),
                         array('name' => $name, 'lilo_id' => $lilo_id,'brand_id' => $brand, 'server_ip' => $server_ip, 'server_port' => $server_port, 
                               'channel_number' => $channel_number, 'max_ccu_per_channel' => $max_ccu_per_channel, 
                               'world_size_x' => $world_size_x, 'world_size_y' => $world_size_y, 
                               'interest_area_x' => $interest_area_x, 'interest_area_y' => $interest_area_y)
                         );

  asset_admin_level_update_channel($lilo_id, $name, $channel_number);
  return "1";
  
}

function asset_admin_level_update_file(){
	require_once("libraries/js/valums-file-uploader/server/php.php");
	usleep(100000);
	
	$fileName;
	$fileSize;
	
	if (isset($_GET['qqfile'])){
		$fileName = $_GET['qqfile'];
	
		// xhr request
		$headers = apache_request_headers();
		$fileSize = (int)$headers['Content-Length'];
	} elseif (isset($_FILES['qqfile'])){
		$fileName = basename($_FILES['qqfile']['name']);
		$fileSize = $_FILES['qqfile']['size'];
	} else {
		die ('{error: "server-error file not passed"}');
	}
	
	if (count($_GET)){	
		array_merge($_GET, array('fileName'=>$fileName));
	
		$response = array_merge($_GET, array('success'=>true, 'fileName'=>$fileName));
	
		// to pass data through iframe you will need to encode all html tags		
		echo htmlspecialchars(json_encode($response), ENT_NOQUOTES);	
	} else {
		die ('{error: "server-error  query params not passed"}');
	}

	$file_type = $_GET['file_type'];	// 'skybox', 'audio'

  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Level');

	// list of valid extensions, ex. array("jpeg", "xml", "bmp")
	if(trim($file_type) == 'skybox'){
		$allowedExtensions = array('unity3d');
//		$file_dir = 'user_generated_data/level_skybox/';
		$lilo_data = $lilo_mongo->findOne(array('lilo_id' => $_GET['lilo_id']));
		$file_dir = $lilo_data['directory'] . '/';//'user_generated_data/level/' . ;
	} else if(trim($file_type) == 'audio'){
		$allowedExtensions = array('mp3', 'ogg', 'wav');
		$lilo_data = $lilo_mongo->findOne(array('lilo_id' => $_GET['lilo_id']));
		$file_dir = $lilo_data['directory'] . '/';//'user_generated_data/level/' . ;
	}
	
	// max file size in bytes
	$sizeLimit = 10 * 1024 * 1024;
	
	$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	$result = $uploader->handleUpload($file_dir /*. $_GET['lilo_id']*/);
	
	// to pass data through iframe you will need to encode all html tags
//	die(htmlspecialchars(json_encode($result), ENT_NOQUOTES));

//	die($result);

//die('{error: "'.$_GET['lilo_id'].'"}');
	// $_GET['lilo_id']
	$lilo_mongo->update_set(array('lilo_id' => $_GET['lilo_id']), array($file_type . '_file' => $file_dir . $fileName));
	

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
    $uploaddir = 'user_generated_data/level/' ;
    if(!is_dir($uploaddir)){
      mkdir($uploaddir);
    }
    
    $time_marker = $_SERVER['REQUEST_TIME'];
    
    $uploadfile = $uploaddir . $time_marker . '...' . basename($_FILES['asset_admin_level_add_file_level']['name']);
    // string '...' adalah marker
    
    $extractdir = $uploaddir . $time_marker;
    if(!is_dir($extractdir)){
      mkdir($extractdir);
    }
    
		$source_file = '';
		
    if (move_uploaded_file($_FILES['asset_admin_level_add_file_level']['tmp_name'], $uploadfile)) {
      // insert to database
    
      // - extract ke directory user_generated_content/level/[username]/<time_marker>...filename
      $zip = new ZipArchive;
      $res = $zip->open($uploadfile);
      if ($res === TRUE) {
        $zip->extractTo($extractdir);
        $zip->close();
        $_SESSION['pop_status_msg'][] = "File level berhasil diupload";
        
				// pindahkan file zip ke folder hasil ekstraksinya sendiri
				$source_file = $extractdir . '/' . basename($_FILES['asset_admin_level_add_file_level']['name']);
				rename($uploadfile, $source_file);
				
        // baca file hasil extract, masukkan informasinya ke DB
        if(file_exists($extractdir . '/level.ini')){
//          require_once('libraries/protocolbuf/message/pb_message.php');
 //         require_once('user_generated_data/proto/pb_proto_Level.php');

          // sesuaikan variable2 di object ini dengan asset yg diupload
          $ini_array = parse_ini_file($extractdir . '/level.ini', true);
					
					$lightmaps = NULL;
					
          if(is_array($ini_array) && count($ini_array)){
            foreach($ini_array as $key => $val){
							if($key == 'rendersettings'){
/*
fogActive=True
fogColor=0.5,0.5,0.5
fogDensity=0.05
fogStartDistance=0
fogEndDistance=300
fogMode="ExponentialSquared"
*/
								$fogActive = $ini_array[$key]['fogActive'];
								$fogColor = $ini_array[$key]['fogColor'];
								
								// pecah fogColor menjadi fogColor _ r/g/b/a
								$fogColor_expl = explode(',', $fogColor);
								$fogColor_r = $fogColor_expl[0];
								$fogColor_g = $fogColor_expl[1];
								$fogColor_b = $fogColor_expl[2];
								$fogColor_a = $fogColor_expl[3];
								
								$fogDensity = $ini_array[$key]['fogDensity'];
								$fogStartDistance = $ini_array[$key]['fogStartDistance'];
								$fogEndDistance = $ini_array[$key]['fogEndDistance'];
								$fogMode = $ini_array[$key]['fogMode'];
								
								unset($ini_array[$key]);

							} else if($key == 'lightmaps'){
								// write_log(array('log_text' => print_r($ini_array[$key], true)));
								
								foreach($ini_array[$key] as $key_ => $val_){
									if(substr($key_, 0, 5) == 'near_' || substr($key_, 0, 4) == 'far_'){
										if(trim($val_) != ''){
											$ini_array[$key][$key_] = $extractdir . '/' . $val_;
										}
									}
								}
								$lightmaps = $ini_array[$key];
								
								unset($ini_array[$key]);
							} else {
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
								
								$lightmapTilingOffset_xyzw = $ini_array[$key]['lightmapTilingOffset'];
								$lightmapTilingOffset_xyzw_expl = explode(',', $lightmapTilingOffset_xyzw);
								$lightmapTilingOffset_x = $lightmapTilingOffset_xyzw_expl[0];
								$lightmapTilingOffset_y = $lightmapTilingOffset_xyzw_expl[1];
								$lightmapTilingOffset_z = $lightmapTilingOffset_xyzw_expl[2];
								$lightmapTilingOffset_w = $lightmapTilingOffset_xyzw_expl[3];

								$ini_array[$key]['lightmapTilingOffset_x'] = $lightmapTilingOffset_x;
								$ini_array[$key]['lightmapTilingOffset_y'] = $lightmapTilingOffset_y;
								$ini_array[$key]['lightmapTilingOffset_z'] = $lightmapTilingOffset_z;
								$ini_array[$key]['lightmapTilingOffset_w'] = $lightmapTilingOffset_w;


							}
              
            }
          }
          
          
          $preview_file = '';
          
          if(isset($_FILES['asset_admin_level_add_file_preview_level'])/* && trim($_FILES['asset_admin_level_add_file_preview_level']) != ''*/){
//            $uploaddir_ = 'user_generated_data/level_preview/' ;
						$uploaddir_ = $extractdir;
            if(!is_dir($uploaddir_)){
              mkdir($uploaddir_);
            }
            
            // upload asset_admin_level_add_file_preview_level dan asset_admin_level_add_file_skybox
//            $uploadfile_ = $uploaddir_ . $time_marker . '...' . basename($_FILES['asset_admin_level_add_file_preview_level']['name']);
            $uploadfile_ = $uploaddir_ . '/' . basename($_FILES['asset_admin_level_add_file_preview_level']['name']);
            // string '...' adalah marker
            
            if (move_uploaded_file($_FILES['asset_admin_level_add_file_preview_level']['tmp_name'], $uploadfile_)) {
              $preview_file = $uploadfile_;
            }
          }
          
          $skybox_file = '';
//          write_log(array('log_text' => print_r($_FILES, true)));
          if(isset($_FILES['asset_admin_level_add_file_skybox'])/* && trim($_FILES['asset_admin_level_add_file_skybox']) != ''*/){
            // $uploaddir_ = 'user_generated_data/level_skybox/' ;
						$uploaddir_ = $extractdir;
            if(!is_dir($uploaddir_)){
              mkdir($uploaddir_);
            }
            
            // upload asset_admin_level_add_file_preview_level dan asset_admin_level_add_file_skybox
//            $uploadfile_ = $uploaddir_ . $time_marker . '...' . basename($_FILES['asset_admin_level_add_file_skybox']['name']);
            $uploadfile_ = $uploaddir_ . '/' . basename($_FILES['asset_admin_level_add_file_skybox']['name']);
            // string '...' adalah marker
            
            if (move_uploaded_file($_FILES['asset_admin_level_add_file_skybox']['tmp_name'], $uploadfile_)) {
              $skybox_file = $uploadfile_;
            }
          }
          
					$audio_file = '';
          if(isset($_FILES['asset_admin_level_add_file_audio'])){
						$uploaddir_ = $extractdir;
            if(!is_dir($uploaddir_)){
              mkdir($uploaddir_);
            }
            
            $uploadfile_ = $uploaddir_ . '/' . basename($_FILES['asset_admin_level_add_file_audio']['name']);
            
            if (move_uploaded_file($_FILES['asset_admin_level_add_file_audio']['tmp_name'], $uploadfile_)) {
              $audio_file = $uploadfile_;
            }
          }



          
          // simpan object ini ke DB
          $lilo_mongo = new LiloMongo();
          $lilo_mongo->selectDB('Assets');
          $lilo_mongo->selectCollection('Level');  // name, uploader, upload_time, preview_image, object_serialized

          // 150811: 
          //  table Level, field baru:
          //    server_ip, server_port, channel_number, max_ccu_per_channel, world_size_[x, y], interest_area_[x, y]
          //  table Channel:
          //    level, name, current_ccu
          
          $server_ip             = $_POST['asset_admin_level_add_server_ip'];
          $server_port           = $_POST['asset_admin_level_add_server_port'];
          $channel_number       = $_POST['asset_admin_level_add_channel_number'];
          $max_ccu_per_channel   = $_POST['asset_admin_level_add_max_ccu_per_channel'];
          $world_size_x         = $_POST['asset_admin_level_add_world_size_x'];
          $world_size_y         = $_POST['asset_admin_level_add_world_size_y'];
          $interest_area_x       = $_POST['asset_admin_level_add_interest_area_x'];
          $interest_area_y       = $_POST['asset_admin_level_add_interest_area_y'];
          $brand      = $_POST['level_add_brand'];

          $level = array(	'name'     => $level_name, 
                         	'assets'   => $ini_array, 
                          'tags'     => $asset_admin_level_add_tag,
													
													'lightmaps'	=> $lightmaps,
                          
                          'server_ip'              => $server_ip,
                          'server_port'            => $server_port,
                          'channel_number'        => $channel_number,
                          'max_ccu_per_channel'    => $max_ccu_per_channel,
                          'world_size_x'          => $world_size_x,
                          'world_size_y'          => $world_size_y,
                          'interest_area_x'        => $interest_area_x,
                          'interest_area_y'        => $interest_area_y,
                          'brand_id'        => $brand,
                          'preview_file'          => $preview_file,
                          'skybox_file'            => $skybox_file,

                          'audio_file'            => $audio_file,

													'source_file'					=> $source_file,
													'directory'						=> $extractdir,
													
													
													'fogActive'	=> (bool)$fogActive,
													'fogColor'	=> (string)$fogColor,

													'fogColor_r'	=> (float)$fogColor_r,
													'fogColor_g'	=> (float)$fogColor_g,
													'fogColor_b'	=> (float)$fogColor_b,
													'fogColor_a'	=> (float)$fogColor_a,

													
													'fogDensity'	=> (float)$fogDensity,
													'fogStartDistance'	=> (float)$fogStartDistance,
													'fogEndDistance'	=> (float)$fogEndDistance,
													'fogMode'	=> (string)$fogMode
													
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
            $_SESSION['pop_error_msg'][] = "File npc.ini tidak ditemukan.";
          }

          $_SESSION['pop_status_msg'][] = "File level berhasil diupload";
//          header("Location: " . $_SESSION['basepath'] . 'asset/admin/level');

        } else {
          $_SESSION['pop_error_msg'][] = "File level.ini tidak ditemukan";
//          header("Location: " . $_SESSION['basepath'] . 'asset/admin/level');
        }
        
        
        // redirect ke...?
      } else {
        $_SESSION['pop_status_msg'][] = "File level gagal diupload";
        // redirect ke...?
//        header("Location: " . $_SESSION['basepath'] . 'asset/admin/level');
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
    $lilo_mongo->selectCollection('Brand');
    $listbrand=$lilo_mongo->find();
    $template->brand = $listbrand;
    $html = $template->render("modules/002_asset_management/templates/asset_admin_level_add.php");
  } else {
    $template->ajax = '';
    $html = ui_admin_default("modules/002_asset_management/templates/asset_admin_level_add.php");
  }

  return $html;
}

//function asset_admin_hapuscumatestiniarray(){
//	//	1323906986 ok
//	//	1324290187 ga
//	$ini_array = parse_ini_file("user_generated_data/level/1323906986/level.ini");
//	return "<pre>" . print_r($ini_array, true) . "</pre>";
//}

function asset_admin_level_list($complete = null, $retval = null){
	$complete = isset($complete) ? $complete : func_arg(0);

  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Level');

  $level_cursor = $lilo_mongo->find();

  $level_array = array();
  while($curr = $level_cursor->getNext()){
		if(intval($complete)){
			$level_array[] = $curr;
		} else {
			$level_array[] = array('id' => $curr['lilo_id'], 'name' => $curr['name']);
		}
  }

	$retval = isset($retval) ? $retval : func_arg(1);
	if(trim(strtolower($retval)) == 'array'){
		return "<pre>" . print_r($level_array, true) . "</pre>";
	}

//  return print_r($level_array, true);
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
//  return "<pre>" . print_r($level_array, true) . "</pre>";
}

//  asset_admin_level_update_channel($lilo_id, $name, $channel_number);
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
//  $dialogData01->values["4"] = array($dialogOption01, $dialogOption02);
  
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
//  $dialogTree01->set_currentDialog(1);

//  return print_r($dialogTree01);exit;

//  return "<pre>" . print_r($dialogTree01) . "<pre>";
  
  $serialized_string = $dialogTree01->SerializeToString();
  $text = print_r($serialized_string, true);
  
  return $text;  
}

function asset_admin_inventory_exist(){  // tipe, [edit, lilo_id]
  // cek apakah tipe yg digunakan sudah digunakan inventory lain
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Inventory');

  extract($_REQUEST);
  if(trim($tipe) == ''){
    return 'EMPTYTYPE';
  }
  
  if(isset($edit) && isset($lilo_id)){
    $criteria = array('tipe' => $tipe, '$not' => array('lilo_id' => $lilo_id));
  } else {
    $criteria = array('tipe' => $tipe);
  }
  
  
  $exists = $lilo_mongo->findOne($criteria);
  
  return  count($exists) > 0 ? "1" : "0";
}

function asset_admin_inventory(){
  // CRUD for Assets.Inventory
  $lilo_mongo = new LiloMongo();
  $lilo_mongo->selectDB('Assets');
  $lilo_mongo->selectCollection('Inventory');
  
  // delete
  $delete = trim($_REQUEST['delete']);
  if($delete != ''){
    // next: rename icon menjadi 'deleted__' . original_name
    $deleted_data = $lilo_mongo->findOne(array('lilo_id' => $delete));
    
    if($deleted_data['icon'] != ''){
      unlink($deleted_data['icon']);
    }
    
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
    extract($_POST);  // tipe, description, icon
    if(trim($tipe) != ''){
      // delete dulu yg tipe-nya sama
      $lilo_mongo->delete(array('tipe' => $tipe));
      
      // upload icon
      $uploaddir = 'bundles/inventory/icons/';
      $uploadfile = $uploaddir . time() . '__' . basename($_FILES['icon']['name']);
      
      $icon = '';
      if (move_uploaded_file($_FILES['icon']['tmp_name'], $uploadfile)) {
//        echo "File is valid, and was successfully uploaded.\n";
        $icon = $uploadfile;
      } else {
//        echo "Possible file upload attack!\n";
      }
      
      $curtime = time();
      $inventory_data = array('tipe' => $tipe, 'description' => $description, 'icon' => $icon, 'upload_time' => $curtime, 'last_edit_time' => $curtime);
      $lilo_id_ = $lilo_mongo->insert($inventory_data);
      
      $lilo_mongo->update($inventory_data, array_merge($inventory_data, array('lilo_id' => (string)$lilo_id_)), array("multiple" => false));
    }
    
    header('Location:' . $_SESSION['basepath'] . 'asset/admin/inventory');
  }

  // edit_inventory
  $edit_inventory = trim($_REQUEST['edit_inventory']);
  if($edit_inventory != ''){

  //  Array (
  //    [q] => asset/admin/inventory
  //    [edit_inventory] => 4ea62afd89b38f280b000001
  //    [edit_tipe] => Test002
  //    [edit_description] => Testing Kedua
  //    [PHPSESSID] => 0g7pjm3pc256rerjfv01ae1f24 ) 
  
    extract($_REQUEST);  // edit_tipe, edit_description, edit_icon
    if(trim($edit_tipe) != ''){
      // upload icon
      $icon = '';
      if(trim($_FILES['edit_icon']['name']) != ''){
        $uploaddir = 'bundles/inventory/icons/';
        $uploadfile = $uploaddir . time() . '__' . basename($_FILES['edit_icon']['name']);
        
        if (move_uploaded_file($_FILES['edit_icon']['tmp_name'], $uploadfile)) {
          $icon = $uploadfile;
        } else {
          
        }
      }
      
      $curtime = time();
      if($icon != ''){
        $inventory_data = array('tipe' => $edit_tipe, 'description' => $edit_description, 'icon' => $icon, 'last_edit_time' => $curtime);
        
        // hapus file icon yg lama
        $deleted_data = $lilo_mongo->findOne(array('lilo_id' => $edit_inventory));
        
        if($deleted_data['icon'] != ''){
          unlink($deleted_data['icon']);
        }
        
      } else {
        $inventory_data = array('tipe' => $edit_tipe, 'description' => $edit_description, 'last_edit_time' => $curtime);
      }
      
      $criteria = array('lilo_id' => $edit_inventory);
      
      $lilo_mongo = new LiloMongo();
      $lilo_mongo->selectDB('Assets');
      $lilo_mongo->selectCollection('Inventory');
      $lilo_mongo->update_set($criteria, $inventory_data);
      
    }
    
    header('Location:' . $_SESSION['basepath'] . 'asset/admin/inventory');
  }

  // dapatkan data Assets.Inventory

  $inventory_array = $lilo_mongo->find();
  $inventory_array = $inventory_array->sort(array('upload_time' => -1));

  // digunakan utk list inventory di quest/admin/default
  $return_json = $_REQUEST['return_json'];
  if($return_json == "1"){
    $inv_array = array();
    while($curr = $inventory_array->getNext()){
      $inv_array[] = $curr;
    }
    
    return json_encode($inv_array);
  }

  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  
  $template->inventory_array = $inventory_array;

  $html = $template->render("modules/002_asset_management/templates/asset_admin_inventory.php");
  $html = ui_admin_default(NULL, $html);

  return $html;
  
}

function asset_admin_lobbysetting($op = null /* get | set */, $ip = null, $port = null, $ret_type = null /* array | json */, $room_history = null /* 0 | 1 */){
	if(!isset($op)){
		$op = func_arg(0);
	}
	
	if(!isset($ip)){
		$ip = func_arg(1);
	}
	
	if(!isset($port)){
		$port = func_arg(2);
	}
	
	if(!isset($ret_type)){
		$ret_type = func_arg(3);
	}
	
	if(!isset($room_history)){
		$room_history = func_arg(4);
	}
	// die("$op, $ip, $port");	// Data: set, 127.0.0.1, 12027
	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('LobbySetting');

	switch(trim(strtolower($op))){
		case 'get':
			$lobbysetting = $lilo_mongo->findOne();
			if($ret_type == 'array'){
				return $lobbysetting;
			} else {	// default: json
				return json_encode($lobbysetting);
			}
			break;
		case 'set':
			if(isset($ip) && isset($port)){
				$data = array('ip' => $ip, 'port' => $port, 'room_history' => $room_history);
				$lilo_mongo->update(array(), $data, array('upsert' => true));
				return 'OK';
			}
			break;
	}
}


function asset_admin_wsinventoryicons(){	// CRUD table Game.Items
	//1. Code field, string
	//2. version field, int
	//3. File field, file upload.		di-rename jadi lowercase no-space
	
	//	
	//Mekanismenya:
	//
	//1. Admin bisa buat inventory item baru dengan cara menset code, dan upload image. Version itu auto increment. 
	//		- File yang di upload harus .png atau .tga.
	//		- File yang di upload di taruh di bundles/icons
	//		- DB harus ada di Game.Items
	//2. Admin bisa mengedit existing inventory items.
	//		- Bisa mereset version number dengan click tombol aja, langsung di reset ke versi 1. 
	//		- Bisa mengubah file dan code field.
	
	// asset/admin/wsinventoryicons/[get|set]/[get=>code]

	$lilo_mongo = new LiloMongo();
	$lilo_mongo->selectDB('Game');
	$lilo_mongo->selectCollection('Items');
	
	$op = func_arg(0);	// add, get, update
	
	switch(strtolower(trim($op))){
		
		case 'resetversion':
			$code = func_arg(1);
			
			$criteria = array('code' => $code);
			$data = array('version' => 1);
			$lilo_mongo->update_set($criteria, $data);
			return 'OK';
			break;
		
		case 'exists':	// 1. cek apakah code sudah digunakan, 2. cek apakah format string code sudah benar (tanpa spasi)
			$code = func_arg(1);
			
			$code = trim($code);
			if($code == ''){
				return 'ERROR - Code should not be empty';
			}
			
			$space_exists = strpos($code, ' ');
			if($space_exists !== false){
				return "ERROR - Use no space for code.";
			}
			
			$criteria = array('code' => $code);
			
			$exists = $lilo_mongo->findOne($criteria);
			
			if(is_array($exists) && count($exists)){
				return "ERROR - Code already exists in the database. Use another code.";
			}
			
			return 'OK';
			
			break;
		
		
		// asset/admin/wsinventoryicons/delete/$code
		case 'delete':
			$code = func_arg(1);
			$criteria = array('code' => $code);
			
			$lilo_mongo->remove($criteria);
			
			return 'OK';
			
			break;
		
		// asset/admin/wsinventoryicons/get/$code
		case 'getall':
			$item_cursor = $lilo_mongo->find();
			
			while($curr = $item_cursor->getNext()){
				$result[] = array('code' => $curr['code'], 
													'file' => $curr['file'], 
													'version' => $curr['version']);
			}
			
			return json_encode($result);
			break;
		
		// asset/admin/wsinventoryicons/get/$code
		case 'get':
			// code & version
			$code = func_arg(1);
			
			$criteria = array('code' => $code);
			
			$result = $lilo_mongo->findOne($criteria);
			
			return json_encode($result);
			
			break;
		
		case 'update':
			// mh: yg di-set adalah image-nya, merujuk pada code. version: auto-increment
			
			$code = $_POST['code'];
			
			if(!isset($code) || trim($code) == ''){
				return 'ERROR - Code should not be empty.';
			}
			
			// dapatkan data yg lama, increment version-nya
			$criteria = array('code' => $code);
			$old_items = $lilo_mongo->findOne($criteria);
			
			$version = intval($old_items['version']) + 1;
			
			// tangani file upload
			$uploadfile = $old_items['file'];
			if(isset($_FILES['file'])){
				$uploaddir = 'bundles/icons/';
				if(!is_dir($uploaddir)){
					mkdir($uploaddir);
				}
				
				$uploadfile = $uploaddir . time() . '__' . basename($_FILES['file']['name']);
				
				if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
					return 'ERROR - Failed to upload file.';
				}
			}
			
			// update
			$data = array('code' => $code, 'file' => $uploadfile, 'version' => $version);
			$option = array('upsert' => true);
			$lilo_mongo->update_set($criteria, $data, $option);

			header("Location: " . $_SESSION['basepath'] . 'asset/admin/inventoryicons');
			exit;
			
			break;
		
		case 'add':
			
			// yg di-set: code + upload image, version: 1 jika code belum ada di db, auto-increment jika sudah ada di db
			$code = $_POST['code'];
			// jika $code sudah ada di DB, gagal
			$criteria = array('code' => $code);
			$exists = $lilo_mongo->findOne($criteria);
			
			if(is_array($exists) && count($exists)){
				return "Code sudah ada di DB. Gunakan code lain";
			}
			
			$version = 1;
			
			// tangani file upload
			$uploadfile = '';
			if(isset($_FILES['file'])){
				$uploaddir = 'bundles/icons/';
				if(!is_dir($uploaddir)){
					mkdir($uploaddir);
				}
				
				$uploadfile = $uploaddir . time() . '__' . basename($_FILES['file']['name']);
				
				if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
					return 'ERROR - Failed to upload file.';
				}
			}
			
			$data = array('code' => $code, 'file' => $uploadfile, 'version' => $version);
			$lilo_mongo->insert($data);
			
			header("Location: " . $_SESSION['basepath'] . 'asset/admin/inventoryicons');
			exit;
			
			break;
	}
	
}

function asset_admin_inventoryicons(){	// CRUD table Game.Items
  $html = '';
  $template = new Template();
  $template->basepath = $_SESSION['basepath'];
  
  $html = $template->render("modules/002_asset_management/templates/asset_admin_inventoryicons.php");
  $html = ui_admin_default(NULL, $html);

  return $html;
}
function asset_admin_brand()
{
    $lilo_mongo = new LiloMongo();
    $lilo_mongo->selectDB('Assets');
    $lilo_mongo->selectCollection('Brand');
    $op = isset($op) ? $op : func_arg(0);
    $op = strtolower(trim($op));
    switch($op)
    {
        case 'add':
              $data = array('name' => $_POST['name'],'brand_id' => $_POST['id_brand']);
              $anim_id = $lilo_mongo->insert($data);
              break;
        case 'delete':
            $lilo_mongo->remove(array('_id' => new MongoId($_POST['_id'])));
            break;
       case 'detail':
            $retval = $lilo_mongo->findOne(array('_id' => new MongoId($_POST['_id'])));
           $resultdetail=array(
               '_id'=>$_POST['_id'],
               'name'=>$retval['name'],
               'brand_id'=>$retval['brand_id'],
           );
            echo json_encode($resultdetail);
            exit;
            break;
       case 'edit':
           $lilo_mongo->update_set(array('_id'=>new MongoId($_POST['id'])), array('name'=>$_POST['name'],'brand_id' => $_POST['id_brand']));
            break;
    }
    $brand_data = $lilo_mongo->find();
    $returndt=array();
    foreach($brand_data as $result)
    {
        $returndt[]=array(
            '_id'=>$result['_id'],
            'name'=>$result['name'],
            'brand_id'=>$result['brand_id'],
        );
    }
    if(isset($_POST['json']))
    {
        $listtabel="";
        $listtabel .= "<table class='input_form' width='100%'>";
        $listtabel .="<tr>";
        $listtabel .="<th>Brand ID</th>";
        $listtabel .="<th>Name</th>";
        $listtabel .="<th>Action</th>";
        $listtabel .="</tr>";
        foreach($brand_data as $dt)
        {
          $listtabel .= "<tr>";
          $listtabel .= "<td>".$dt['brand_id']."</td>";
          $listtabel .= "<td>".$dt['name']."</td>";
          $listtabel .= "<td>";
          $listtabel .= "<button onclick='functionhapus(\"".$dt['_id']."\");'>Delete</button>";
          $listtabel .= "<button onclick='functiongetdetail(\"".$dt['_id']."\");'>Edit</button>";
          $listtabel .= "</td>";
          $listtabel .= "</tr>";
        }
        $listtabel .= "</table>";
        echo $listtabel;
        exit;
    }
    $html = '';
    $template = new Template();
    $template->basepath = $_SESSION['basepath'];
    $template->brand_array = $returndt;
    $html = $template->render("modules/002_asset_management/templates/asset_admin_brand.php");
    $html = ui_admin_default(NULL, $html);
    return $html;
}
function asset_admin_category()
{
    $lilo_mongo = new LiloMongo();
    $lilo_mongo->selectDB('Assets');
    $lilo_mongo->selectCollection('Category');
    $op = isset($op) ? $op : func_arg(0);
    $op = strtolower(trim($op));
    switch($op)
    {
        case 'add':
              $data = array('name' => $_POST['name'],'tipe' => $_POST['editor_div_type']);
              $anim_id = $lilo_mongo->insert($data);
              break;
        case 'delete':
            $lilo_mongo->remove(array('_id' => new MongoId($_POST['_id'])));
            break;
       case 'detail':
            $retval = $lilo_mongo->findOne(array('_id' => new MongoId($_POST['_id'])));
           $resultdetail=array(
               '_id'=>$_POST['_id'],
               'name'=>$retval['name'],
               'tipe'=>$retval['tipe'],
           );
            echo json_encode($resultdetail);
            exit;
            break;
       case 'edit':    
           $lilo_mongo->selectCollection('Category');
           $retval = $lilo_mongo->findOne(array('_id' => new MongoId($_POST['id'])));
           $lilo_mongo->selectCollection('Avatar');
           $lilo_mongo->update_set(array('category'=>$retval['name']), array('category'=>$_POST['name']));
           $lilo_mongo->selectCollection('Category');
           $lilo_mongo->update_set(array('_id'=>new MongoId($_POST['id'])), array('name'=>$_POST['name'],'tipe'=>$_POST['editor_div_type']));
            break;
    }
    $brand_data = $lilo_mongo->find(array(),0,array("tipe"=>1));
    if($_POST['showdt']!='')
    {
        $brand_data = $lilo_mongo->find(array("tipe"=>$_POST['showdt']),0,array("tipe"=>1));
    }
    $returndt=array();
    foreach($brand_data as $result)
    {
        $returndt[]=array(
            '_id'=>$result['_id'],
            'name'=>$result['name'],
            'tipe'=>$result['tipe'],
        );
    }
    if(isset($_POST['json']))
    {
        $listtabel="";
        $listtabel .= "<table class='input_form' width='100%'>";
        $listtabel .="<tr>";
        $listtabel .="<th>Name</th>";
        $listtabel .="<th>Avatar Body Part Type</th>";
        $listtabel .="<th>Action</th>";
        $listtabel .="</tr>";
        foreach($brand_data as $dt)
        {
          $listtabel .= "<tr>";
          $listtabel .= "<td>".$dt['name']."</td>";
          $listtabel .= "<td>".$dt['tipe']."</td>";
          $listtabel .= "<td>";
          $listtabel .= "<button onclick='functionhapus(\"".$dt['_id']."\");'>Delete</button>";
          $listtabel .= "<button onclick='functiongetdetail(\"".$dt['_id']."\");'>Edit</button>";
          $listtabel .= "</td>";
          $listtabel .= "</tr>";
        }
        $listtabel .= "</table>";
        echo $listtabel;
        exit;
    }
    $html = '';
    $template = new Template();
    $template->basepath = $_SESSION['basepath'];
    $lilo_mongo->selectCollection('AvatarBodyPart');
    $listtipe=$lilo_mongo->find();
    $template->tipe_array = $listtipe;
    $template->category_array = $returndt;
    $html = $template->render("modules/002_asset_management/templates/asset_admin_category.php");
    $html = ui_admin_default(NULL, $html);
    return $html;
}
?>