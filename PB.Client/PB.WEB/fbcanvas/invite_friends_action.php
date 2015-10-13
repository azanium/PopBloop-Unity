<?php
	require_once 'config.php';

	$selected_friends = $_POST['selected_friends'];
//	print_r($selected_friends);exit;

//	Array
//	(
//	    [0] => 2302957433431_1023302095
//	    [1] => 1784940923342_752423432
//	)

	$return_array = array();
	$invited_friends = array();
	foreach($selected_friends as $sf){
		$sf_expl = explode('_', $sf);
		$friend_id = $sf_expl[1];
		if(!in_array($friend_id, $invited_friends)){
			$post_id = $facebook->api('/' . $friend_id . '/feed', 'POST', array('message' => 'Gabung ke PopBloop yuk...'));
			if(isset($post_id)){
				$return_array[] = $sf;
				$invited_friends[] = $friend_id;
				
				// simpan ke db...
				
			}
		}
	}
	
	print(json_encode($return_array));
	exit;

?>