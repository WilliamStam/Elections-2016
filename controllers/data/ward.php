<?php

namespace controllers\data;
use \models as models;

class ward extends _ {
	function __construct() {
		parent::__construct();
		
		$this->user = $this->f3->get("user");
		
	}
	function data() {
		models\log::_do("3");
		$result = array();
		$key = isset($_GET['ward'])?$_GET['ward']:"";
		
		$vs = "";
		
		if ($key){
			$split_key = explode(",",$key);
			$lat = $split_key[1];
			$lng = $split_key[0];
			
			
			$vs = \controllers\data\iec::getInstance()->votingStation($lat,$lng);
			
			
		}
		
		$point = \controllers\data\lookup::getInstance()->point("{$key}");
		
		
		
		
		if ($point['code']==200){
			$result['code']=404;
			//test_array($point); 
			if (isset($point['Ward'])){
				if (isset($point['Ward']['codes']['MDB'])&&$point['Ward']['codes']['MDB']){
					
					$ward = \controllers\data\lookup::getInstance()->ward($point['Ward']['codes']['MDB'],true);
					$result = $point['Ward'];
					$result['councillors'] = models\councilor::getInstance()->getAll("wardID='{$point['Ward']['codes']['MDB']}'","fullname ASC");
					$result['code']=200;
					$result['geojson']=($ward['data']);
				}
			}
			$result['VotingStation']=$vs;
			
		} else {
			$result['code']=404;
		}
		

		return $GLOBALS["output"]['data'] = $result;
	}
	



}
