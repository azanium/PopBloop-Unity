<?php
// just create from the proto file a pb_prot[NAME].php file
require_once('../parser/pb_parser.php');
 
$test = new PBParser();
//$test->parse('./StoryRequest.proto');
//$test = new PBParser();
//$test->parse('./StoryResponse.proto');
// $test->parse('./Kroto.proto');

$proto_file = $_REQUEST['proto'];
if($proto_file){
	$test->parse($proto_file);
	var_dump('File parsing done!');
} else {
	echo 'Stupid';
}

?>