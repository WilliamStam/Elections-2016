<?php
namespace models;
use \timer as timer;

class log extends _ {
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

	function get($ID,$options=array()) {
		$timer = new timer();
		$where = "ID = '$ID' OR wardID = '$ID'";
		


		$result = $this->f3->get("DB")->exec("
			SELECT *
			FROM stats
			WHERE $where;
		"
		);


		if (count($result)) {
			$return = $result[0];
			
		} else {
			$return = parent::dbStructure("stats");
		}
		
		//test_array($return);
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}

	public function getAll($where = "", $orderby = "", $limit = "", $options = array()) {
		$timer = new timer();
		$f3 = \Base::instance();

		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($limit) {
			$limit = " LIMIT " . $limit;
		}

		$args = "";
		if (isset($options['args'])) $args = $options['args'];

		$ttl = "";
		if (isset($options['ttl'])) $ttl = $options['ttl'];


		$result = $f3->get("DB")->exec("
			 SELECT DISTINCT *
			FROM stats
			$where
			$orderby
			$limit;
		", $args, $ttl
		);

		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}

	

	public static function _save($ID, $values = array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		$domain=$f3->get("domain");
		$domainID = $domain['ID'];
		
		if (isset($values['ID'])) unset($values['ID']);
		


		$a = new \DB\SQL\Mapper($f3->get("DB"), "stats");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$a->$key = $value;
			}

		}

		$a->save();
		$ID = ($a->ID) ? $a->ID : $a->_id;
		
		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $ID;
	}

	

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"stats");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return "done";

	}
	
	
	static function format($data) {
		$timer = new timer();
		$single = false;
		//	test_array($items); 
		if (isset($data['ID'])) {
			$single = true;
			$data = array($data);
		}
		//test_array($items);
		
		$i = 1;
		$n = array();
		foreach ($data as $item) {
			
			$n[] = $item;
		}
		
		if ($single) $n = $n[0];
		
		
		$records = $n;
		
		
		
		//test_array($n); 
		
		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $n;
	}
	
	static function _do($typeID) {
		$timer = new timer();
		
		$userKey = "";
		$page = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:"";
		$IP = $_SERVER['REMOTE_ADDR'];
		$proxyIP = "";
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$proxyIP = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$proxyIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		$userKey = md5($IP . "|".$proxyIP);
		
		$bot = is_bot();
		
		$values = array(
			"typeID"=>$typeID,
			"userKey"=>$userKey,
			"ip"=>$IP,
			"page"=>$page,
			"bot"=>$bot,
				
		);
		
		self::_save("",$values);
		//test_array($values); 
		
		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $userKey;
	}
	
	
}
