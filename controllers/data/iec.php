<?php
namespace controllers\data;
use \models as models;

class iec extends _ {
	private static $instance;
	
	function __construct() {
		parent::__construct();
		
		
		$post_data="grant_type=password&username=IECWebAPIMediaZN&password=53412d349f394c3aa5de5593961d2059";
		$url="https://api.elections.org.za/token";
		
		$options = array(
				"method"=>"POST",
				"content"=>$post_data
		);
		
		$this->web = new \Web();
		
	
		$request = $this->web->request($url,$options);
		$request = (json_decode($request['body']));
		$this->token = $request->access_token;
		
		
	}
	public static function getInstance(){
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	
	
	function voter() {
		$IDNumber = isset($_GET['IDNumber'])?$_GET['IDNumber']:"";
		//test_array($IDNumber); 
		$api_options = array(
				"header"=>"Authorization: Bearer ".$this->token
		);
		$api_url = "https://api.elections.org.za/api/v1/Voters/GetVoterAllDetails?ID={$IDNumber}";
		
	//	test_array($api_options); 
		$api = (array)  $this->web->request($api_url,$api_options);
		
		$api['options'] = array(
				"url"=>$api_url,
				"options"=>$api_options
		);
		
		$response = json_decode($api['body']);
		
		return $GLOBALS["output"]['data'] = $response;
	}
	function votingStation($lat="",$lng="") {
		if (!is_string($lat)){
			$lat = isset($_GET['lat'])?$_GET['lat']:"";
		}
		if (!is_string($lng)){
			$lng = isset($_GET['lng'])?$_GET['lng']:"";
		}
		
		//test_array($IDNumber); 
		$api_options = array(
				"header"=>"Authorization: Bearer ".$this->token
		);
		$api_url = "https://api.elections.org.za/api/v1/VotingStationDetails?Latitude={$lat}&Longitude={$lng}";
		
		//test_array($api_url); 
		$api = (array)  $this->web->request($api_url,$api_options);
		
		$api['options'] = array(
				"url"=>$api_url,
				"options"=>$api_options
		);
		
		$response = json_decode($api['body']);
		
		return $GLOBALS["output"]['data'] = $response;
	}
	function candidates() {
		
		
			$wardID = isset($_GET['wardID'])?$_GET['wardID']:"";
		
		
		//test_array($IDNumber); 
		$api_options = array(
				"header"=>"Authorization: Bearer ".$this->token
		);
		$api_url = "https://api.elections.org.za/api/v1/LGECandidates?ElectoralEventID=402&WardID={$wardID}";
		
		//test_array($api_url); 
		$api = (array)  $this->web->request($api_url,$api_options);
		
		$api['options'] = array(
				"url"=>$api_url,
				"options"=>$api_options
		);
		
		$response = json_decode($api['body']);
		
		return $GLOBALS["output"]['data'] = $response;
	}
	
	
}
