<?php
namespace controllers;
use \timer as timer;
use \models as models;
class ward_map extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		$data = models\ward::getInstance()->getAll("","wardID ASC");
		$data = models\ward::format($data);
		
		//test_array($data); 
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "ward_map",
			"sub_section"=> "ward_map",
			"template"   => "ward_map",
			"meta"       => array(
				"title"=> "Elections 2016 | Wards",
			),
		);
		$tmpl->data = $data;
		$tmpl->output();
	}
	
	
	
}
