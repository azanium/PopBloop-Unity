<?php
	require_once 'config.php';
	
	//$post_id = '100003575731186_100508640078327';
	$post_id = $_POST['post_id'];die("post_id yg anda kirim: " . $post_id);
	$message = $_POST['message'];
	
	$comment_id = $facebook->api('/' . $post_id . '/comments', 'POST', array('message' => $message));
	
	print_r($comment_id); exit;
?>