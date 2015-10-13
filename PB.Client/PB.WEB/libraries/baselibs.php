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

function write_log($args){	// filename, log_text
	extract($args);
	
	if(!isset($filename)){
		$filename = 'logs/dev.log';
	}
	
	$time_ = date("Y/m/d H:i:s");
	
	$log_text = "\n\nLog Time: $time_ \n\n" . $log_text . "\n\n-----------------------------------------------------\n\n";
	
	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {
	
		// In our example we're opening $filename in append mode.
		// The file pointer is at the bottom of the file hence
		// that's where $log_text will go when we fwrite() it.
		if (!$handle = fopen($filename, 'a')) {
//		 echo "Cannot open file ($filename)";
		 return false;
		}
		
		// Write $log_text to our opened file.
		if (fwrite($handle, $log_text) === FALSE) {
//			echo "Cannot write to file ($filename)";
			return false;
		}
		
//		echo "Success, wrote ($log_text) to file ($filename)";
		
		fclose($handle);
		return true;
	} else {
//		echo "The file $filename is not writable";
	}

	return false;
}


function visitor_count(){
	require_once('libraries/LiloMongo.php');

	$today = date("Y-m-d");

	if(!isset($_SESSION['pop_visitor_logged']) || $_SESSION['pop_visitor_logged'] != $today){
		
		$lilo_mongo = new LiloMongo();
		$lilo_mongo->selectDB('Logs');
		
		$lilo_mongo->selectCollection('Visitor');
		$visitor_data = array('date' => $today, 'time' => date("H:i:s"), 'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'], 'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'], '_SERVER' => $_SERVER);
		$lilo_mongo->insert($visitor_data);

		$lilo_mongo->selectCollection('VisitorCounter');	// date, count
		$criteria = array('date' => $today);
		$today_visitor_data = $lilo_mongo->findOne($criteria);
		
		$count = 0;
		if($today_visitor_data){
			$count = intval($today_visitor_data['count']);
		}
		
		$count++;
		
		$lilo_mongo->update_set(array('date' => $today), array('count' => $count), array("multiple" => true, 'upsert' => true));

		$_SESSION['pop_visitor_logged'] = $today;
	}
	
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


// mengubah unity time dgn format "6/29/2012 10:24:38 PM" ke integer (php time function)
// int mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
function unitytounixtime($unitytime = NULL){
	if(!isset($unitytime)){
		return time();
	}
	
	// 6/29/2012 10:24:38 PM = month/day/year hour:minute:second am/pm
	$ut_expl = explode(' ', $unitytime);
	
	$date_ = $ut_expl[0];
	$time_ = $ut_expl[1];
	$ampm_ = $ut_expl[2];
	
	$date_expl = explode('/', $date_);
	$time_expl = explode(':', $time_);
	$ampm_ = strtoupper(trim($ampm_));	// kalo PM => Hour = Hour + 12
	
	$time_expl[0] = ($ampm_ == 'PM') ? $time_expl[0] + 12 : $time_expl[0];
	
	return mktime(intval($time_expl[0]), intval($time_expl[1]), intval($time_expl[2]), intval($date_expl[0]), intval($date_expl[1]), intval($date_expl[2]));
}


?>
