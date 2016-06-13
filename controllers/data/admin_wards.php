<?php

namespace controllers\data;
use \models as models;

class admin_wards extends _ {
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
			$where .= " AND (wardID LIKE '%$search%')";
		}
		
		
		$recordsO = models\ward::getInstance();
		
		//test_string($where);
		
		$records = $recordsO->getAll($where);
		
		$limits = array();;
		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] :"";
		if (!$selectedpage) $selectedpage = 1;
		
		
		$limit = 15;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages(count($records), $limit,$selectedpage, 7);
		$limits = $pagination['limit'];
		
		$records = $recordsO->getAll($where, "wardID ASC", $limits);
		
		
		
		
		
		
		
		$result['details'] = models\ward::getInstance()->format(models\ward::getInstance()->get($ID));
		$result['records'] = models\ward::getInstance()->format($records);
		$result['pagination'] = $pagination;
		$result['search'] = $search;


		return $GLOBALS["output"]['data'] = $result;
	}
	



}
