<?php
	require_once 'config.php';
	
	$decodedSignedRequest = $facebook->getSignedRequest();
	
	if($decodedSignedRequest['page']['liked'] == 1){
		echo "Like";
	} else {
		echo "Not Like Yet";
	}
?>