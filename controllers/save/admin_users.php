<?php

namespace controllers\save;
use \models as models;

class admin_users extends _ {
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
			"username" => $this->post("username_fld",true),
			//"password" => $this->post("password"),
			"fullname" => $this->post("fullname",true),
		);
		$password = $this->post("password_fld");
		if ($password==""&&($ID==""||$ID=="undefined") ){
			$this->errors['password_fld']="Required for a new user account";
		}
		if ($password){
			$values['password'] = $password;
		}
		
		//test_array(array($ID,$values,$this->errors)); 
	
		
		
		if (count($this->errors)==0){
			$ID = models\user::_save($ID,$values);
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
		if ($ID!=$this->user['ID']){
			$result = models\user::_delete($ID);
			if ($result!="done"){
				$error = "Cant delete this record";
			}
		} else {
			$error = "Cant delete your own record";
		}
		
		
		
		
		
		$return = array(
				"result"=>$result,
				"error" => $error
		);
		
		return $GLOBALS["output"]['data'] = $return;
	}
	
	

}
