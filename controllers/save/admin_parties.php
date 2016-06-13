<?php

namespace controllers\save;
use \models as models;

class admin_parties extends _ {
	function __construct() {
		parent::__construct();
		$this->user = $this->f3->get("user");
		if ($this->user['ID']==""){
			$this->f3->error(403);
		}
	}


	
	function form() {
		$result = array();
		$ID = isset($_REQUEST['ID'])?$_REQUEST['ID']:"";
		
		$values = array(
				"party" => $this->post("party",true),
				"party_logo" => $this->post("party_logo"),
		);
		
		//test_array(array($ID,$values,$this->errors)); 
		
		
		
		if (count($this->errors)==0){
			$ID = models\party::_save($ID,$values);
		}
		$return = array(
				"ID" => $ID,
				"errors" => $this->errors
		);
		
		return $GLOBALS["output"]['data'] = $return;
	}
	function delete(){
		$result = array();
		$ID = isset($_REQUEST['ID'])?$_REQUEST['ID']:"";
		$error = "";
		
		$result = models\party::_delete($ID);
		if ($result!="done"){
			$error = "Cant delete this record";
		}
		
		
		$return = array(
			"result"=>$result,
			"error" => $error
		);
		
		return $GLOBALS["output"]['data'] = $return;
	}
	
	

}
