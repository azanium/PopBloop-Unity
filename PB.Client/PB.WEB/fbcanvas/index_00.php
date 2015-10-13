<?php
	require_once 'config.php';
	
	
//	die("Testing: <pre>" . print_r($facebook, true) . "</pre>");
	// get a valid session
	$uid = $facebook->getUser();
//	die("Testing: <pre>" . print_r($session, true) . "</pre>");
	$me = null;
	if($uid){
		// check if session is valid
		$me = $facebook->api('/me');
	}
	
	if($me){
		echo "User is logged in and has a valid session<br />";
		echo "<a href='" . $facebook->getLoginUrl() . "'>Login</a><br>";
		echo "<pre>" . print_r($me, true) . "</pre>";
//		echo "<script>top.location.href='".$facebook->getLoginUrl()."'</script>";
		
    $logoutUrl = $facebook->getLogoutUrl(array('next' => 'http://apps.facebook.com/popbloop/'));
    echo "<a href='#' onclick='top.location.href='".$logoutUrl."'; return false;'>Logout</a>";
//    echo "<a href='#' onclick='location.href='".$logoutUrl."'; return false;'>Logout</a>";
//    echo "<a href='".$logoutUrl."'>Logout</a>";
	} else {
		echo "Session expired or user has not logged in yet. Redirecting...";
    $loginUrl = $facebook->getLoginUrl(array('scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos'));
//		echo "<script>top.location.href='".$loginUrl."'</script>";
		echo "<script>location.href='".$loginUrl."'</script>";
//		header("Location: $loginUrl");
	}
	


?>

<div id="fb-root">

<a href="<?php echo $appBaseUrl ?>/about.php">About</a>
<br />
<a href="http://localhost/lilo3a" target="_top">External</a>

<form target="_top" method="post" action="<?php echo $localBaseUrl ?>/test_post.php">
<input type="text" name="car_name" id="car_name" /><br />
<input type="submit" value="Simpan" />
</form>

Your car name: <?php echo print_r($_SESSION['car_name'], true); ?>


</div>
<script>

	window.fbAsyncInit = function(){
		FB.Canvas.setAutoResize();
	};
	(function(){
		var e = document.createElement('script');
		e.async = true;
		e.src = document.location.protocol +
			'//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
	}());

</script>

