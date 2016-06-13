<?php

namespace controllers\data;
use \models as models;

class admin_parties extends _ {
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
			$where .= " AND (party LIKE '%$search%')";
		}
		
		
		$recordsO = models\party::getInstance();
		
		//test_string($where);
		
		$records = $recordsO->getAll($where);
		
		$limits = array();;
		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] :"";
		if (!$selectedpage) $selectedpage = 1;
		
		
		$limit = 15;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages(count($records), $limit,$selectedpage, 7);
		$limits = $pagination['limit'];
		
		$records = $recordsO->getAll($where, "party ASC", $limits);
		
		
		
		
		
		
		
		$result['details'] = models\party::getInstance()->format(models\party::getInstance()->get($ID));
		$result['records'] = models\party::getInstance()->format($records);
		$result['pagination'] = $pagination;
		$result['search'] = $search;


		return $GLOBALS["output"]['data'] = $result;
	}
	



}
