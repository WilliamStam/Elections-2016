<?php
namespace controllers;
use \timer as timer;
use \models as models;
class admin_councilors extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		$parties = models\party::getInstance()->getAll();
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "admin",
			"sub_section"=> "councilors",
			"template"   => "admin_councilors",
			"meta"       => array(
				"title"=> "Elections 2016 | Admin | Councilors",
			),
			"js"=>array("/vendor/ckeditor/ckeditor/ckeditor.js")	
		);
		$tmpl->parties = $parties;
		$tmpl->output();
	}
	
	
	
}
