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
		
		
		$point = \controllers\data\lookup::getInstance()->point("{$key}");
		
		$result['code']=404;
		if (isset($point['Ward'])){
			if (isset($point['Ward']['codes']['MDB'])&&$point['Ward']['codes']['MDB']){
				
				$ward = \controllers\data\lookup::getInstance()->ward($point['Ward']['codes']['MDB']);
				$result = $point['Ward'];
				$result['councillors'] = models\councilor::format(models\councilor::getInstance()->getAll("wID='{$point['Ward']['codes']['MDB']}'","party ASC"));
				
				$result['code']=200;
				$result['geojson']=($ward['data']);
				
				
			}
		}
		
		
		
		

		return $GLOBALS["output"]['data'] = $result;
	}
	



}
