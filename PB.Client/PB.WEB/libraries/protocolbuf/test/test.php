<?php
// first include pb_message
require_once('../message/pb_message.php');
 /*
// now include the generated files
require_once('./pb_proto_StoryRequest.php');
require_once('./pb_proto_StoryResponse.php');
 
//parse the StoryRequest
$received_story_request = new StoryRequest();
$received_story_request->parseFromString($HTTP_RAW_POST_DATA);
 
//the info we want to gather
$name1 = $received_story_request->character1();
$name2 = $received_story_request->character2();
$ammo = $received_story_request->secret_weapon();
if(empty($ammo))
{
	$ammo = "baseball";
}
 
//build the StoryResponse
$story_response = new StoryResponse();
if(empty($name1) || empty($name2) )
{
	$story_response->set_status(FAILED);
	$story_response->set_story("Oops. Both characters need to be named!");
}
else
{
	$story_response->set_status(SUCCESS);
	$story_choice = rand(0, 2);
	$the_story = "";
	if($story_choice == 0)
	{
		$the_story .= 'Once upon a time, ' . $name1 . ' and ' . $name2 . ' went to slay a dragon that started fires everywhere. ';
		$the_story .= 'When they arrived, the dragon spat fire at them. ';
		$the_story .= 'They were backed into a corner. They were both scared. ';
		$the_story .= 'And then suddenly, ' . $name2 . ' had an idea.';
		$the_story .= '\n\n';
		$the_story .= '\"I know, \" said ' . $name2 . '. \"Why don\'t we throw a ' . $ammo . ' at it?\"\n';
		$the_story .= '\"Good idea!\" agreed ' . $name1 . ', so they did.';
		$the_story .= '\n\n';
		$the_story .= 'And it worked! The dragon was defeated and agreed to stop being such a fire hazard. ';
		$the_story .= 'They agreed that they all should get along and be friends. They lived happily ever after.';
	}
	else if($story_choice == 1)
	{
		$the_story .= 'One day, ' . $name1 . ' and ' . $name2 . ' were playing basketball. ';
		$the_story .= 'Unfortunately, the ball got caught in the tree. ';
		$the_story .= 'And then suddenly, ' . $name2 . ' had an idea.';
		$the_story .= '\n\n';
		$the_story .= '\"I know, \" said ' . $name2 . '. \"Why don\'t we throw a ' . $ammo . ' at it?\"\n';
		$the_story .= '\"Good idea!\" agreed ' . $name1 . ', so they did.';
		$the_story .= '\n\n';
		$the_story .= 'And it worked! They got the ball back. ';
		$the_story .= 'They agreed that this was too much fun and decided to play basketball happily ever after.';
	}
	else if($story_choice == 2)
	{
		$the_story .= 'Long ago, in a galaxy far far away, ' . $name1 . ' and ' . $name2 . ' were looking for a ship. ';
		$the_story .= 'A salesman told them that he got a piece of junk they could sell, but they would need to fix it first. ';
		$the_story .= 'They were told that there was a piece missing from the engine and the ship wouldn\'t fly until they found a replacement. ';
		$the_story .= 'And then suddenly, ' . $name2 . ' had an idea.';
		$the_story .= '\n\n';
		$the_story .= '\"I know, \" said ' . $name2 . '. \"Why don\'t we just jam this ' . $ammo . ' in it?\"\n';
		$the_story .= '\"Good idea!\" agreed ' . $name1 . ', so they did.';
		$the_story .= '\n\n';
		$the_story .= 'And it worked! The ship was good as new. ';
		$the_story .= 'They agreed that the ship was awesome and decided to cruise around in it happily ever after.';
	}
	$story_response->set_story($the_story);
}
 
//respond to client
$serialized_string = $story_response->SerializeToString();
print($serialized_string);
 */
 
require_once('./pb_proto_Kroto.php');

$quest01 = new Quest();
$quest01->set_questname('wadehell...');

$kroto01 = new Kroto();
$kroto01->set_id(1);
$kroto01->set_name('Ngarso');
$kroto01->set_quest($quest01);

$serialized_string = $kroto01->SerializeToString();
$text = print_r($serialized_string, true);


$filename = 'test.txt';
$somecontent = $text;

// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {

    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'a')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }

    echo "Success, wrote ($somecontent) to file ($filename)";

    fclose($handle);

} else {
    echo "The file $filename is not writable";
}


?>