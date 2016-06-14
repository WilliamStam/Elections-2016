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
		
		$point = \controllers\data\lookup::getInstance()->point("{$key}");
		
		if ($point['code']==200){
			$result['code']=404;
			//test_array($point); 
			if (isset($point['Ward'])){
				if (isset($point['Ward']['codes']['MDB'])&&$point['Ward']['codes']['MDB']){
					
					$ward = \controllers\data\lookup::getInstance()->ward($point['Ward']['codes']['MDB'],true);
					$result = $point['Ward'];
					$result['code']=200;
					$result['geojson']=($ward['data']);
				}
			}
		} else {
			$result['code']=404;
		}
		

		return $GLOBALS["output"]['data'] = $result;
	}
	



}
