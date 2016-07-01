<?php

namespace controllers\data;
use \models as models;

class ward_map extends _ {
	function __construct() {
		parent::__construct();
		
		$this->user = $this->f3->get("user");
		
	}
	function data() {
	
		}
	function clicked(){
		models\log::_do("2");
		$result = array();
		$key = isset($_GET['key'])?$_GET['key']:"";
		$vs = "";
		
		if ($key){
			$split_key = explode(",",$key);
			$lat = $split_key[1];
			$lng = $split_key[0];
			
			
			$vs = \controllers\data\iec::getInstance()->votingStation($lat,$lng);
			
			
		}
		$point = \controllers\data\lookup::getInstance()->point("{$key}");
		
		$result['code']=404;
		if (isset($point['Ward'])){
			if (isset($point['Ward']['codes']['MDB'])&&$point['Ward']['codes']['MDB']){
				
				$ward = \controllers\data\lookup::getInstance()->ward($point['Ward']['codes']['MDB']);
				$result = $point['Ward'];
				$result['councilors'] = models\councilor::getInstance()->getAll("wardID='{$point['Ward']['codes']['MDB']}'","fullname ASC");
				
				$result['code']=200;
				$result['geojson']=($ward['data']);
				
				$result['VotingStation']=$vs;
				
			}
		}
		
		
		
		

		return $GLOBALS["output"]['data'] = $result;
	}
	



}
