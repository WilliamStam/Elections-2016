<?php

namespace controllers\data;
use \models as models;

class admin_councillors extends _ {
	function __construct() {
		parent::__construct();
		
		$this->user = $this->f3->get("user");
		if ($this->user['ID']==""){
			$this->f3->error(403);
		}
	}


	function data() {
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] :"";
		$search = (isset($_REQUEST['search'])) ? $_REQUEST['search'] : "";
		$result = array();
		
		
		
		
		
		
		
		$where = "1";
		if ($search){
			$where .= " AND (fullname LIKE '%$search%')";
		}
		
		
		$recordsO = models\councilor::getInstance();
		
		//test_string($where);
		
		$records = $recordsO->getAll($where);
		
		$limits = array();;
		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] :"";
		if (!$selectedpage) $selectedpage = 1;
		
		
		$limit = 15;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages(count($records), $limit,$selectedpage, 7);
		$limits = $pagination['limit'];
		
		$records = $recordsO->getAll($where, "fullname ASC", $limits);
		
		
		
		
		
		
		
		$result['details'] = models\councilor::getInstance()->format(models\councilor::getInstance()->get($ID));
		$result['records'] = models\councilor::getInstance()->format($records);
		$result['pagination'] = $pagination;
		$result['search'] = $search;


		return $GLOBALS["output"]['data'] = $result;
	}
	



}
