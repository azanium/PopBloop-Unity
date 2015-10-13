<?php
	require_once 'facebook.php';
	$facebook = new Facebook(array(
		'appId'		=> '353789864649141',
		'secret'	=> '9e066419bed7d9ff07f4475f26318aa8',
		'cookie'	=> true
	));
	
//	die("<pre>" . print_r($facebook, true) . "<pre>");
	$appBaseUrl = 'http://apps.facebook.com/popbloop';
	$localBaseUrl = 'http://debugging.myfacebookapp.com/lilo.beta/fbcanvas';
?>