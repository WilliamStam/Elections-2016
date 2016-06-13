<?php
namespace controllers;
use \timer as timer;
use \models as models;
class admin extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "admin",
			"sub_section"=> "home",
			"template"   => "admin",
			"meta"       => array(
				"title"=> "Elections 2016 | Admin",
			),
		);
		$tmpl->output();
	}
	
	
	
}
