<?php
require_once('config/connection.php');

class LiloMongo{

	var $connection;
	var $db;
	var $collection;

	function LiloMongo($args = NULL){	// $mongoserver, $dbname, $collectionname
		if(is_array($args)){
			extract($args);
		}

		if(!isset($mongoserver)){
			$mongoserver = trim($_SESSION['config']['mongod_ip']) != '' ? $_SESSION['config']['mongod_ip'] : '127.0.0.1';

			$username = $_SESSION['config']['mongod_username'];
			$password = $_SESSION['config']['mongod_password'];
			
			$mongoserver = "mongodb://".$username.":".$password."@" . $mongoserver;

			if(trim($_SESSION['config']['mongod_port']) != ''){
				$mongoserver = $mongoserver . ":" . $_SESSION['config']['mongod_port'];
			}

		}

		$this->connection = new Mongo($mongoserver);
		
		if(isset($dbname)){
			$this->db = $this->connection->selectDB($dbname);
		}
		
		if(isset($collectionname)){
			$this->collection = $this->db->selectCollection($collectionname);
		}
		
	}
	
	function selectDB($dbname){
		$this->db = $this->connection->selectDB($dbname);
	}
	
	function selectCollection($collectionname){
		$this->collection = $this->db->selectCollection($collectionname);
	}
	
	function findOne($array_parameter = array(), $fields = array()){
		// return an array
		return $this->collection->findOne($array_parameter, $fields);
	}
	
	function find($array_parameter = array(), $limit = 0, $sort = array()){
		// return a MongoCursor object
//		if($limit > 0){
			if(count($sort)){
				return $this->collection->find($array_parameter)->sort($sort)->limit($limit);
			}
			return $this->collection->find($array_parameter)->limit($limit);
//		}

//		the original version:
		return $this->collection->find($array_parameter);
	}
	
	function command($command_, $options_ = array()){
		// http://php.net/manual/en/mongodb.command.php
		return $this->db->command($command_, $options_);
	}
	
	function command_values($command_, $options_ = array()){
		$ret = $this->db->command($command_, $options_);
		return $ret['values'];
	}
	
	function count($array_parameter = array()){
		$cursor = $this->collection->find($array_parameter);
		return $cursor->count();
	}

	function insert($array_parameter){
		$this->collection->insert($array_parameter);
		return $array_parameter['_id'];
	}

	function update($array_criteria, $array_newobj, $array_options = array("multiple" => true)){
		$this->collection->update($array_criteria, $array_newobj, $array_options);
	}

	function update_set($array_criteria, $array_newobj, $array_options = array("multiple" => true)){
		$this->collection->update($array_criteria, array('$set' => $array_newobj), $array_options);
	}
	
	function remove($array_criteria, $array_options = array()){
		$this->collection->remove($array_criteria, $array_options);
	}

	// alias utk fungsi remove()
	function delete($array_criteria, $array_options = array()){
		$this->collection->remove($array_criteria, $array_options);
	}

	function close(){
		$this->connection->close();
	}

}

?>