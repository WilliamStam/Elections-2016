<?php
namespace controllers;
use \timer as timer;
use \models as models;
class admin_wards extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "admin",
			"sub_section"=> "wards",
			"template"   => "admin_wards",
			"meta"       => array(
				"title"=> "Elections 2016 | Admin | Wards",
			),
		);
		$tmpl->output();
	}
	
	
	
}
