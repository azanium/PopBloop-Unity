<?php
require_once('libraries/baselibs.php');
if($_POST['car_name']){
	$_SESSION['car_name'] = $_POST['car_name'];
//	echo $_POST['car_name'];
}

// simpan di text
write_log(array('somecontent' => $_POST['car_name'] . "\n\r\n"));//{	// filename, somecontent

header('Location: http://apps.facebook.com/popbloop');
exit;

?>

<script>
//top.location.href='http://apps.facebook.com/popbloop';
</script>