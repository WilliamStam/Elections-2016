<?php

namespace controllers\save;
use \models as models;

class admin_wards extends _ {
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
			"wardID" => $this->post("wardID",true),
			"type" => $this->post("password"),
			"coordinates" => $this->post("fullname"),
		);
		
		$ward = \controllers\data\lookup::getInstance()->ward($values['wardID'],true);
		
		// test_array($ward['code']); 
		if ($ward['code']=="404"){
			$this->errors['wardID']="Ward doesn't exist";
		} else {
			$values['data'] = json_encode($ward['data']);
		}
		//test_array(array($ID,$values,$this->errors)); 
	
		
		
		if (count($this->errors)==0){
			
			$ID = models\ward::_save($ID,$values);
			
			
			
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
		
		$result = models\ward::_delete($ID);
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
