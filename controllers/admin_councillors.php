<?php
namespace controllers;
use \timer as timer;
use \models as models;
class admin_councillors extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		$parties = models\party::getInstance()->getAll("","party ASC");
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "admin",
			"sub_section"=> "councillors",
			"template"   => "admin_councillors",
			"meta"       => array(
				"title"=> "Elections 2016 | Admin | councillors",
			),
			"js"=>array("/vendor/ckeditor/ckeditor/ckeditor.js")	
		);
		$tmpl->parties = $parties;
		$tmpl->output();
	}
	
	
	
}
