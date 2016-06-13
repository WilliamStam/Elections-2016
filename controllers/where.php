<?php
namespace controllers;
use \timer as timer;
use \models as models;
class where extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		$timeline = models\timeline::getInstance()->getAll("","item_date ASC");
		
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "where",
			"sub_section"=> "where",
			"template"   => "where",
			"meta"       => array(
				"title"=> "Elections 2016 | Where do i vote?",
			),
		);
		$tmpl->timeline = $timeline;
		$tmpl->output();
	}
	
	
	
}
