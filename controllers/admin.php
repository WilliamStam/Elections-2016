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
		
		$stats = array();
		
		$sections = $this->f3->get("DB")->exec("SELECT typeID, count(ID) as c FROM stats GROUP BY typeID");
		$s = array();
		foreach ($sections as $item){
			$s[$item['typeID']] = $item['c'];
		}
		$pages = $this->f3->get("DB")->exec("SELECT page, count(ID) as c FROM stats WHERE typeID='1' GROUP BY `page` ");
		
		$users = $this->f3->get("DB")->exec("SELECT userKey, count(ID) as c FROM stats GROUP BY `userKey` ");
		$stats['unique_users'] = count($users);
		
		$stats['types'] = $s;
		$stats['pages'] = $pages;
		
		
		if (isset($_GET['d'])) test_array($stats); 
		
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "admin",
			"sub_section"=> "home",
			"template"   => "admin",
			"meta"       => array(
				"title"=> "Elections 2016 | Admin",
			),
		);
		$tmpl->stats = $stats;
		$tmpl->output();
	}
	
	
	
}
