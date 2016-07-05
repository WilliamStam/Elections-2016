<?php

namespace controllers\save;
use \models as models;

class admin_councillors extends _ {
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
			"fullname" => $this->post("fullname",true),
			"IDNumber" => $this->post("IDNumber",true),
			"partyID" => $this->post("partyID"),
			"photo" => $this->post("photo"),
			"bio" => $this->post("bio"),
		);
		if ($values['partyID']==""){
			$this->errors['partyID']="Please select a party for this councilor";
		}
		$war = array();
		foreach ($_POST as $key=>$value){
			if (substr($key,0,6)=="wardID"){
				$wID = str_replace("wardID-","",$key);
				if (substr($wID,0,4)=="new-"){
					$wID = "";
				}
				
				if ($wID=="" && $value==""){
					
				} else {
					$war[] = array("ID"=>$wID,"wID"=>$value);
					$ward = \controllers\data\lookup::getInstance()->ward($value);
				}
				
				
				
				
			//	test_array($key); 
			}
		}
		$values['wards']=$war;
		
			
		
		//test_array(array($ID,$values,$this->errors)); 
	
		
		
		if (count($this->errors)==0){
			$ID = models\councilor::_save($ID,$values);
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
		
		$result = models\councilor::_delete($ID);
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
