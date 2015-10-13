<?php

/**
 * write your docs here dude...
 */
function arg($idx){
	$arg = $_REQUEST['q'];
	$arg_expl = explode("/", $arg);
	return $arg_expl[$idx];
}

/**
 * write your docs here dude...
 */
function arg_str(){
	return $_REQUEST['q'];
}

/**
 * write your docs here dude...
 */
function func_arg($idx){
	$arg = $_REQUEST['q'];
	$arg_expl = explode("/", $arg);
	$idx += 3;
	return $arg_expl[$idx];
}

function write_log($args){	// filename, somecontent
	extract($args);
	
	if(!isset($filename)){
		$filename = 'logs/dev.log';
	}
	
	$time_ = date("Y/m/d H:i:s");
	
	$somecontent = "\n\nLog Time: $time_ \n\n" . $somecontent . "\n\n-----------------------------------------------------\n\n";
	
	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {
	
		// In our example we're opening $filename in append mode.
		// The file pointer is at the bottom of the file hence
		// that's where $somecontent will go when we fwrite() it.
		if (!$handle = fopen($filename, 'a')) {
//		 echo "Cannot open file ($filename)";
		 return false;
		}
		
		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
//			echo "Cannot write to file ($filename)";
			return false;
		}
		
//		echo "Success, wrote ($somecontent) to file ($filename)";
		
		fclose($handle);
		return true;
	} else {
//		echo "The file $filename is not writable";
	}

	return false;
}


// helper function
function sec2hms ($sec, $padHours = false) 
{
	// credit: http://www.laughing-buddha.net/php/lib/sec2hms/

	// start with a blank string
	$hms = "";
	
	// do the hours first: there are 3600 seconds in an hour, so if we divide
	// the total number of seconds by 3600 and throw away the remainder, we're
	// left with the number of hours in those seconds
	$hours = intval(intval($sec) / 3600); 

	// add hours to $hms (with a leading 0 if asked for)
	$hms .= ($padHours) 
				? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
				: $hours. ":";
	
	// dividing the total seconds by 60 will give us the number of minutes
	// in total, but we're interested in *minutes past the hour* and to get
	// this, we have to divide by 60 again and then use the remainder
	$minutes = intval(($sec / 60) % 60); 

	// add minutes to $hms (with a leading 0 if needed)
	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

	// seconds past the minute are found by dividing the total number of seconds
	// by 60 and using the remainder
	$seconds = intval($sec % 60); 

	// add seconds to $hms (with a leading 0 if needed)
	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

	// done!
	return $hms;
	
}


?>
