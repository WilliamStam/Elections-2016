<?php
namespace controllers;
use \timer as timer;
use \models as models;
class home extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		models\log::_do("1");
		
		$timeline = models\timeline::getInstance()->getAll("","item_date ASC");
		
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "home",
			"sub_section"=> "home",
			"template"   => "home",
			"meta"       => array(
				"title"=> "Elections 2016",
			),
		);
		$tmpl->timeline = $timeline;
		$tmpl->output();
	}
	
	
	
}
