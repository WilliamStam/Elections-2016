<?php
namespace models;
use \timer as timer;

class councilor extends _ {
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
		$where = "councillors.ID = '$ID'";
		


		$result = $this->f3->get("DB")->exec("
			SELECT *, councillors.ID as ID
			FROM councillors INNER JOIN parties ON councillors.partyID = parties.ID
			WHERE $where;
		"
		);


		if (count($result)) {
			$return = $result[0];
			
			$return['wards'] = $this->f3->get("DB")->exec("SELECT * FROM councillors_wards WHERE cID = '{$return['ID']}'");
		} else {
			$return = parent::dbStructure("councillors",array("wards"=>array()));
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
			 SELECT DISTINCT councillors.*, parties.party, parties.party_logo, GROUP_CONCAT(DISTINCT wID SEPARATOR ', ') as wards
			FROM (councillors INNER JOIN parties ON councillors.partyID = parties.ID) LEFT JOIN councillors_wards ON councillors.ID = councillors_wards.cID
			$where
			GROUP BY councillors.ID
			
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
		


		$a = new \DB\SQL\Mapper($f3->get("DB"), "councillors");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$a->$key = $value;
			}

		}

		$a->save();
		$ID = ($a->ID) ? $a->ID : $a->_id;
		
		if (isset($values['wards'])){
			$b = new \DB\SQL\Mapper($f3->get("DB"), "councillors_wards");
			foreach ($values['wards'] as $item){
				$b->load("ID='{$item['ID']}'");
				
				if ($item['wID']==""){
					$b->erase();
				} else {
					$b->cID = $ID;
					$b->wID = $item['wID'];
					$b->save();
				}
				
				$b->reset();
				
				
				
			}
			
			
		}
		
		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $ID;
	}

	

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"councillors");
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
			$idnum = $item['IDNumber'];
			if ($idnum){
				$bday = str_split(substr($idnum,0,6),2);
				
				$bday[0] = "19".$bday[0];
				$bday = implode("-",$bday);
				
				$date = new \DateTime($bday);
				$now = new \DateTime();
				$interval = $now->diff($date);
				$item['age'] =  $interval->y;
				
				
				$item['birth_day'] = $bday;
			}
			
			
			
			$n[] = $item;
		}
		
		if ($single) $n = $n[0];
		
		
		$records = $n;
		
		
		
		//test_array($n); 
		
		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $n;
	}
	
	
}
