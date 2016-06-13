<?php

namespace controllers\data;
use \models as models;

class lookup extends _ {
	private static $instance;
	function __construct() {
		parent::__construct();

	}
	public static function getInstance(){
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	function ward($key="",$check=false){
		if ($key) {
		} else {
			$key = $this->f3->get("PARAMS['key']");
		}
		$check = (isset($_GET['check'])&&$_GET['check']=='true')||$check==true?true:false;
		$data = array();
		$lookup_type = "area";
		$lookup_key = "";
		//test_array($key); 
		
		$data = models\ward::getInstance()->get($key);
		$lookup_key = "MDB:".$key;
		$url = "http://mapit.code4sa.org/{$lookup_type}/{$lookup_key}.geojson?simplify_tolerance=0.0001";
		//	test_array($return); 
		
		//test_array($check); 
		
		
		if ($check){
			$n = new \Web();
			$response = $n->request($url);
			$return = ($response['body']);
			$r = json_decode($return);
			$r = json_decode(json_encode($r), true);
			//test_array($r); 
			if (isset($r['code'])&&$r['code']==404){
				$data['code'] = 404;
			} else {
				$data['code'] = 200;
			}
			$data['data']=$return;
			
		} 
		
		if ($data['data']=="" && $check==false){
			//test_array($url); 
			$n = new \Web();
			$response = $n->request($url);
			$return = ($response['body']);
			$r = json_decode($return);
			$r = json_decode(json_encode($r), true);
			//	test_array($return); 
			
				if ($r && $r['code'] !='404'){
					$values = array(
							"wardID"=>$key,
							"data"=>($return)
					);
					
					$ID = models\ward::_save($data['ID'],$values);
					$data = models\ward::getInstance()->get($ID);
					$data['code'] = 200;
				} else {
					$data['code'] = 404;
				}
			
			
		}
		
		$data = models\ward::format($data);
		
		//test_array($data);
		return $GLOBALS["output"]['data'] = $data;
	}
	function point($key){
		if ($key) {
		} else {
			$key = $this->f3->get("PARAMS['key']");
		}
		//http://mapit.code4sa.org/point/4326/29.910106658935547,-23.042962580313343
		$url = "http://mapit.code4sa.org/point/4326/$key";
		$n = new \Web();
		$response = $n->request($url);
		$return = ($response['body']);
		$r = json_decode($return);
		$r = json_decode(json_encode($r), true);
		//test_array($r); 
		$data  = array();
		
		if (count($r)){
			$data['code']=200;
		} else {
			$data['code']=404;
		}
		
		
		
		foreach ($r as $item){
			$data[$item['type_name']] = $item;
		}
		
		return $GLOBALS["output"]['data'] = $data;
	}
	


}
