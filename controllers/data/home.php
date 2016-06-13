<?php

namespace controllers\data;
use \models as models;

class home extends _ {
	function __construct() {
		parent::__construct();

	}


	
	function stats_live() {
		$settings = $this->user['settings']['sites'];
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] :"";
		$search = (isset($_REQUEST['search'])) ? $_REQUEST['search'] : $settings['filter']['search'];
		$result = array();
		
		
		
		
		
		
		
		
		
		$where = "dateTo >= CURRENT_DATE() AND dateFrom <= CURRENT_DATE()";
		
		
		$campaignO = models\campaign::getInstance();
		
		
		$campaigns = $campaignO->getAll($where, "dateTo DESC");
		$campaignStats = array(
			"campaigns"=>count($campaigns),
				"adverts"=>0,
				"sites"=>0,
				"used"=>0,
				"budget"=>0,
				"percent_usage"=>0,
				"impressions"=>0,
				"clicks"=>0,
				"domain_adverts"=>0,
				"domain_active"=>0,
		);
		$c=array();
		$sites=array();
		
		
		foreach ($campaigns as $campaign){
			$campaignID = $campaign['ID'];
			$account = new models\account();
			$account = $account->get($campaign['accountID']);
			
			$campaign['dateFrom_display'] = date("d F Y",strtotime($campaign['dateFrom']));
			$campaign['dateTo_display'] = date("d F Y",strtotime($campaign['dateTo']));
			
			$adverts = models\advert::getInstance()->getAll("campaignID = '$campaignID'");
			$campaignStats['adverts'] = $campaignStats['adverts'] + count($adverts);
			
			$a = array();
			$budget = 0;
			$used = 0;
			$invoicedAmount = 0;
			$state = $campaign['active'];
			foreach ($adverts as $advert){
				$domainDetails = models\advert::getInstance()->advertDomainDetails("as_adverts_domains.aID='{$advert['ID']}'","percentused DESC");
				//test_array($domainDetails); 
				foreach ($domainDetails as $domain){
					$invoicedAmount = $invoicedAmount +$domain['invoiceAmount'];
					$budget = $budget + $domain['budget'];
					$used = $used + $domain['used'];
					if ($domain['active']!='1') {
						$state = 0;
					} else {
						$campaignStats['domain_active'] = $campaignStats['domain_active'] + 1;
					}
					$campaignStats['domain_adverts'] = $campaignStats['domain_adverts'] + 1;
					$sites["site-".$domain['dID']] = $sites["site-".$domain['dID']] + 1;
					$campaignStats['impressions'] = $campaignStats['impressions'] + $domain['impressions'];
					$campaignStats['clicks'] = $campaignStats['clicks'] + $domain['clicks'];
				}
				$advert['domains'] = $domainDetails;
				$advert['widthpopover'] = $advert['width'] + 30;
				$a[] = $advert;
			}
			
			$stateVal = "";
			if ($campaign['active']=='1'){
				$stateVal = 'green';
			}
			if ($campaign['future']=='1'){
				$stateVal = 'blue';
			}
			if ($state!='1' && $stateVal!=""){
				$stateVal = 'red';
			}
			
			
			
			$campaign['state'] = $stateVal;
			$campaign['account'] = $account['account'];
			$campaign['budget'] = currency($budget);
			$campaign['used'] = currency($used);
			
			$campaign['adverts'] = $a;
			
			$campaignStats['used'] = $campaignStats['used'] + $used;
			$campaignStats['budget'] = $campaignStats['budget'] + $budget;
			$campaignStats['invoiceAmount'] = $campaignStats['invoiceAmount'] + $invoicedAmount;
			
			
			$c[] = $campaign;
		}
		
		$campaignStats['sites'] = count($sites);
		$campaignStats['used_currency'] = currency($campaignStats['used']);
		$campaignStats['budget_currency'] = currency($campaignStats['budget']);
		$campaignStats['invoiceAmount'] = currency($campaignStats['invoiceAmount']);
		
		if ($campaignStats['budget']>0 && $campaignStats['used']>0){
			$campaignStats['percent_usage'] =  ($campaignStats['used'] / $campaignStats['budget'])*100;
		}
		if ($campaignStats['domain_adverts']>0 && $campaignStats['domain_active']>0){
			$campaignStats['percent_state'] =  ($campaignStats['domain_active'] / $campaignStats['domain_adverts'])*100;
		}
		
		
		
		$result['campaigns'] = $campaignStats;
		
		
		//test_array($result); 
		
		$result['active'] = array();
		
		
		
		$delimarray = array(
			"minute"=>array(
				"db"=>"%Y-%m-%d %H:%i:s",
				"php"=>"1 minute",
				"phpF" => "Y-m-d H:i:s",
				"phpFlabel"=>"i"
			),
			"second"=>array(
				"db"=>"%Y-%m-%d %H:%i:%s",
				"php"=>"1 second",
				"phpF" => "Y-m-d H:i:s",
				"phpFlabel"=>"i"
			),
			
		);
		
		$delim = "second";
		$periodDe = "1 minute";
		//$periodDe = "1 hour";
		
		$domainsList = models\domain::getInstance()->getAll();
		$domainArray = array();
		foreach ($domainsList as $item){
			$domainArray[$item['ID']] = $item;
		}
		
		
		
		
		$DataImpressions = models\records_data::getInstance()->getAll("datein > DATE_SUB( NOW( ) , INTERVAL $periodDe )","as_record_visits","ID DESC");
		$DataClicks = models\records_data::getInstance()->getAll("datein > DATE_SUB( NOW( ) , INTERVAL $periodDe )","as_record_clicks","ID DESC");
		
		
		
		$result['countries'] = array();
		$countries = array();
		$sites = array();
		$pages = array();
		
		foreach ($DataImpressions as $item){
			if ($item['country']){
				if (!isset($countries[$item['country']]))$countries[$item['country']]=0;
				$countries[$item['country']] = $countries[$item['country']] + 1;
			}
			if ($item['dID']){
				if (!isset($sites[$item['dID']]))$sites[$item['dID']]=0;
				$sites[$item['dID']] = $sites[$item['dID']] + 1;
			}
			if ($item['page']&&$item['dID']){
				if (!isset($pages[$item['dID']][$item['page']]))$pages[$item['dID']][$item['page']]=0;
				$pages[$item['dID']][$item['page']] = $pages[$item['dID']][$item['page']] + 1;
			}
			
		}
		foreach ($DataClicks as $item){
			if ($item['country']) {
				if (!isset($countries[$item['country']])) $countries[$item['country']] = 0;
				$countries[$item['country']] = $countries[$item['country']] + 1;
			}
			if ($item['dID']){
				if (!isset($sites[$item['dID']]))$sites[$item['dID']]=0;
				$sites[$item['dID']] = $sites[$item['dID']] + 1;
			}
			if ($item['page']&&$item['dID']){
				if (!isset($pages[$item['dID']][$item['page']]))$pages[$item['dID']][$item['page']]=0;
				$pages[$item['dID']][$item['page']] = $pages[$item['dID']][$item['page']] + 1;
			}
		}
		
		
		
		$result['activeSites'] = array();
		
		$sitesData = array();
		
		foreach ($sites as $key=>$val){
			
			$item = $domainArray[$key];
			$item['value'] = $val;
			
			$p = array();
			foreach ($pages[$key] as $pkey=>$pitem){
				$p[] = array(
					"page"=>$pkey,
					"value"=>$pitem	
				);
			}
			
			usort($p, function($b, $a) {
				return $a['value'] <=> $b['value'];
			});
			$item['pages'] = $p;
			
			$sitesData[] = $item;
		}
		
		usort($sitesData, function($b, $a) {
			return $a['value'] <=> $b['value'];
		});
		
		$result['activeSites'] = $sitesData;
		
		
		//test_array(array($sitesData)); 
		
		
		
		
		
		
		
		
		
		
		/*
		
		if (isLocal()){
			$countries['Germany'] = rand(1,10);
			$countries['United States'] = rand(1,10);
			$countries['Brazil'] = rand(1,10);
			$countries['Canada'] = rand(1,10);
			$countries['France'] = rand(1,10);
		}
		*/
		
		
		
		$c = array();
		foreach ($countries as $k=>$v){
			$c[] = array($k,1);
		}
		
		
		
		$result['countries'] = $c;
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$activeData = models\records_data::getInstance()->getCountGrouped("(DATE_FORMAT(datein,'{$delimarray[$delim]['db']}')) as d, budgetCounted","datein > DATE_SUB( NOW( ) , INTERVAL $periodDe )","as_record_visits","ID ASC","DATE_FORMAT(datein,'{$delimarray[$delim]['db']}'), budgetCounted");
		
		
		$activeDataClicks = models\records_data::getInstance()->getCountGrouped("(DATE_FORMAT(datein,'{$delimarray[$delim]['db']}')) as d, budgetCounted","datein > DATE_SUB( NOW( ) , INTERVAL $periodDe )","as_record_clicks","ID ASC","DATE_FORMAT(datein,'{$delimarray[$delim]['db']}'), budgetCounted");
		
		
		//test_array($activeData); 
		$n = array();		
		$end = new \DateTime(date("Y-m-d H:i:s"));
		$begin = new \DateTime(date("Y-m-d H:i:s",strtotime('-'.$periodDe)));
		$end->modify('+1 second');
		//test_array(array($begin,$end));
		
		$interval = \DateInterval::createFromDateString($delimarray[$delim]['php']);
		$period = new \DatePeriod($begin, $interval, $end);
		
	//	test_array($period); 
		
		$labels = array();
		foreach ( $period as $dt ){
			$d = $dt->format( $delimarray[$delim]['phpF']);
			
			$n[$d] = 0;
			$labels[] = "";
//			$labels[] = $dt->format($delimarray[$delim]['phpFlabel']);
		}
		
		
		$budget = $n;
		$all = $n;
		
		$budgetClicks = $n;
		$allClicks = $n;
		
		
		
		foreach($activeData as $item){
			
			if ($item['budgetCounted']=="1"){
				$budget[$item['d']] = $item['c'];
			} 
			$all[$item['d']] = $all[$item['d']] + $item['c'];
			
		}
		foreach($activeDataClicks as $item){
			
			if ($item['budgetCounted']=="1"){
				$budgetClicks[$item['d']] = $item['c'];
			} 
			$allClicks[$item['d']] = $allClicks[$item['d']] + $item['c'];
			
		}
		
		$v = 5;
		$u = array();
		for ($x = 0; $x <= 60; $x+=$v) {
			$u[] = $x;
			
		}
		//test_array($u); 
		
		
	
		$n = array();
		foreach($all as $key=>$value){
			$secO = new \DateTime($key);
			$sec = $secO->format("s");
			$ns = "";
			
			foreach($u as $ss){
				if ($sec-($v-1)<=$ss){
					$ns = $ss;
					break;
				}
			}
			$ns = str_pad($ns, 2, "0", STR_PAD_LEFT);
			
			$k  = $secO->format("Y-m-d H:i:".$ns);
			if (!isset($n[$k]))	$n[$k] = 0;
			
			$n[$k] = $n[$k] + $value;
			
			
			//$n[] = $key . " | " . $sec . " | ". $ns;;
			
		}
		$labels = array();;
		$all = $n;
		foreach ($all as $item){
			$labels[] = "";
		}
		$n = array();
		foreach($budget as $key=>$value){
			$secO = new \DateTime($key);
			$sec = $secO->format("s");
			$ns = "";
			
			foreach($u as $ss){
				if ($sec-($v-1)<=$ss){
					$ns = $ss;
					break;
				}
			}
			$ns = str_pad($ns, 2, "0", STR_PAD_LEFT);
			
			$k  = $secO->format("Y-m-d H:i:".$ns);
			if (!isset($n[$k]))	$n[$k] = 0;
			
			$n[$k] = $n[$k] + $value;
			
			
			//$n[] = $key . " | " . $sec . " | ". $ns;;
			
		}
		
		
		$budget=$n;
		
		
		
		$n = array();
		foreach($budgetClicks as $key=>$value){
			$secO = new \DateTime($key);
			$sec = $secO->format("s");
			$ns = "";
			
			foreach($u as $ss){
				if ($sec-($v-1)<=$ss){
					$ns = $ss;
					break;
				}
			}
			$ns = str_pad($ns, 2, "0", STR_PAD_LEFT);
			
			$k  = $secO->format("Y-m-d H:i:".$ns);
			if (!isset($n[$k]))	$n[$k] = 0;
			
			$n[$k] = $n[$k] + $value;
			
			
			//$n[] = $key . " | " . $sec . " | ". $ns;;
			
		}
		
		
		$budgetClicks=$n;
		$n = array();
		foreach($allClicks as $key=>$value){
			$secO = new \DateTime($key);
			$sec = $secO->format("s");
			$ns = "";
			
			foreach($u as $ss){
				if ($sec-($v-1)<=$ss){
					$ns = $ss;
					break;
				}
			}
			$ns = str_pad($ns, 2, "0", STR_PAD_LEFT);
			
			$k  = $secO->format("Y-m-d H:i:".$ns);
			if (!isset($n[$k]))	$n[$k] = 0;
			
			$n[$k] = $n[$k] + $value;
			
			
			//$n[] = $key . " | " . $sec . " | ". $ns;;
			
		}
		$allClicks = $n;
		
		//test_array($all)
		; 
		//test_array(array($budget,$all,$labels));
		
		
		$result['activeImpressions']['labels'] = $labels;
		
		$result['activeImpressions']['datasets'] = array(
				array(
						"label"=> "All",
						"fillColor"=> "rgba(220,220,220,0.2)",
						"strokeColor"=> "rgba(220,220,220,1)",
						"pointColor"=> "rgba(220,220,220,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(220,220,220,1)",
						"data"=> array_values($all)
				),
				array(
						"label"=> "Unique",
						"fillColor"=> "rgba(151,187,205,0.2)",
						"strokeColor"=> "rgba(151,187,205,1)",
						"pointColor"=> "rgba(151,187,205,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(151,187,205,1)",
						"data"=> array_values($budget)
				)
					
				
		);
		$result['activeClicks']['labels'] = $labels;
		
		$result['activeClicks']['datasets'] = array(
				array(
						"label"=> "All",
						"fillColor"=> "rgba(220,220,220,0.2)",
						"strokeColor"=> "rgba(220,220,220,1)",
						"pointColor"=> "rgba(220,220,220,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(220,220,220,1)",
						"data"=> array_values($allClicks)
				),
				array(
						"label"=> "Unique",
						"fillColor"=> "rgba(151,187,205,0.2)",
						"strokeColor"=> "rgba(151,187,205,1)",
						"pointColor"=> "rgba(151,187,205,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(151,187,205,1)",
						"data"=> array_values($budgetClicks)
				)
					
				
		);
		
		

		return $GLOBALS["output"]['data'] = $result;
	}
	

	function stats(){
		
		$key = isset($_GET['key'])&&$_GET['key']?$_GET['key']:"";
		
		$result = array();
		$delimarray = array(
				"second"=>array(
						"db"=>"%Y-%m-%d %H:%i:%s",
						"dbLabel"=>"DATE_FORMAT(datein,'%s')",
						"groupBy"=>"1",
						"label"=>"s",
						"key"=>""
				),
				"minute"=>array(
						"db"=>"%Y-%m-%d %H:%i",
						"dbLabel"=>"DATE_FORMAT(datein,'%i')",
						"groupBy"=>"1",
						"label"=>"i",
						"key"=>"Y-m-d-H-i"
				),
				"hour"=>array(
						"db"=>"%Y-%m-%d %H",
						"dbLabel"=>"DATE_FORMAT(datein,'%H')",
						"groupBy"=>"1",
						"label"=>"H",
						"key"=>"Y-m-d-H"
								
				),
				"day"=>array(
						"db"=>"%Y-%m-%d",
						"dbLabel"=>"DATE_FORMAT(datein,'%d')",
						"groupBy"=>"1",
						"label"=>"d",
						"key"=>"Y-m-d"
				),
				"month"=>array(
						"db"=>"%Y-%m",
						"dbLabel"=>"DATE_FORMAT(datein,'%b %y')",
						"groupBy"=>"1",
						"label"=>"M",
						"key"=>"Y-m"
				),
				"year"=>array(
						"db"=>"%Y",
						"dbLabel"=>"DATE_FORMAT(datein,'%Y')",
						"groupBy"=>"1",
						"label"=>"Y"
				)
		);
		
	
		//$key = "2016-04-10-15-16";
	//	$key = "2016";
		$keyparts = explode("-",$key);
		$where = "";
		
		//test_array($key); 
		
		if ($key==""||$key=="prev") {
			$label = "Previous year";
			$delim = "month";
			$where = "";
			$d = strtotime("now");
			$its = array(
					new \DateTime(date("Y-m-01 00:00:00",strtotime("-11 month"))),
					new \DateTime(date("Y-m-t 23:59:59",$d))
			);
			$links = array(
					
					"year"=>array(
							"k"=>date("Y",($d)),
							"l"=>date("Y",($d)),
					),
					"month"=>array(
							"k"=>date("Y-m",($d)),
							"l"=>date("F",($d)),
					),
					"day"=>array(
							"k"=>date("Y-m-d",($d)),
							"l"=>date("d",($d)),
					),
					"hour"=>array(
							"k"=>date("Y-m-d-H",($d)),
							"l"=>date("Ha",($d)),
					),
					"minute"=>array(
							"k"=>date("Y-m-d-H",($d)),
							"l"=>date("i",($d)),
					),
			);
			
			//test_array($its); 
			
		} else {
			SWITCH (count($keyparts)){
				CASE 1: // year view
					$label = "Months - ". "{$keyparts[0]}";
					$delim = "month";
					$d = strtotime("{$keyparts[0]}-01-01");
					$links = array(
							"prev"=>date("Y",strtotime("-1 year",$d)),
							"next"=>date("Y",strtotime("+1 year",$d)),
							
					
					
					);
					$its = array(
							new \DateTime(date("Y-m-d H:i:s",mktime(0,0,0,1,1,$keyparts[0]))),
							new \DateTime(date("Y-m-d H:i:s",mktime(23,59,59,1,0,$keyparts[0]+1)))
					);
					break;
				CASE 2: // month view
					$label = "Days - ".date("F Y",strtotime("{$keyparts[0]}-{$keyparts[1]}-01"))."";
					$delim = "day";
					$d = strtotime("{$keyparts[0]}-{$keyparts[1]}-01");
					$links = array(
							"prev"=>date("Y-m",strtotime("-1 month",$d)),
							"next"=>date("Y-m",strtotime("+1 month",$d)),
							"up"=>date("Y",($d)),
							"year"=>array(
									"k"=>date("Y",($d)),
									"l"=>date("Y",($d)),
							)
					);
					$its = array(
							new \DateTime(date("Y-m-d H:i:s",mktime(0,0,0,$keyparts[1],1,$keyparts[0]))),
							new \DateTime(date("Y-m-d H:i:s",mktime(23,59,59,$keyparts[1]+1,0,$keyparts[0])))
					);
					break;
				CASE 3: // day view
					$label = "Hours - ".date("d F Y",strtotime("{$keyparts[0]}-{$keyparts[1]}-{$keyparts[2]}"))."";
					$delim = "hour";
					$d = strtotime("{$keyparts[0]}-{$keyparts[1]}-{$keyparts[2]}");
					$links = array(
							"prev"=>date("Y-m-d",strtotime("-1 day",$d)),
							"next"=>date("Y-m-d",strtotime("+1 day",$d)),
							"up"=>date("Y-m",($d)),
							"year"=>array(
									"k"=>date("Y",($d)),
									"l"=>date("Y",($d)),
							),
							"month"=>array(
									"k"=>date("Y-m",($d)),
									"l"=>date("F",($d)),
							),
					
					);
					$its = array(
							new \DateTime(date("Y-m-d H:i:s",mktime(0,0,0,$keyparts[1],$keyparts[2],$keyparts[0]))),
							new \DateTime(date("Y-m-d H:i:s",mktime(23,59,59,$keyparts[1],$keyparts[2],$keyparts[0])))
					);
					
					break;
				CASE 4: // hour view
					$label = "Minutes - " . date("d F Y   - Ha",strtotime("{$keyparts[0]}-{$keyparts[1]}-{$keyparts[2]} {$keyparts[3]}:00:00"));
					$delim = "minute";
					$d = strtotime("{$keyparts[0]}-{$keyparts[1]}-{$keyparts[2]} {$keyparts[3]}:00:00");
					$links = array(
							"prev"=>date("Y-m-d-H",strtotime("-1 hour",$d)),
							"next"=>date("Y-m-d-H",strtotime("+1 hour",$d)),
							"up"=>date("Y-m-d",($d)),
							"year"=>array(
									"k"=>date("Y",($d)),
									"l"=>date("Y",($d)),
							),
							"month"=>array(
									"k"=>date("Y-m",($d)),
									"l"=>date("F",($d)),
							),
							"day"=>array(
									"k"=>date("Y-m-d",($d)),
									"l"=>date("d",($d)),
							),
					);
					$its = array(
							new \DateTime(date("Y-m-d H:i:s",mktime($keyparts[3],0,0,$keyparts[1],$keyparts[2],$keyparts[0]))),
							new \DateTime(date("Y-m-d H:i:s",mktime($keyparts[3],59,59,$keyparts[1],$keyparts[2],$keyparts[0])))
					);
					break;
				CASE 5: // minute view
					$label = "Seconds - ".date("d F Y   - H:ia",strtotime("{$keyparts[0]}-{$keyparts[1]}-{$keyparts[2]} {$keyparts[3]}:{$keyparts[4]}:00"));
					$delim = "second";
					$d = strtotime("{$keyparts[0]}-{$keyparts[1]}-{$keyparts[2]} {$keyparts[3]}:{$keyparts[4]}:00");
					$links = array(
							"prev"=>date("Y-m-d-H-i",strtotime("-1 minute",$d)),
							"next"=>date("Y-m-d-H-i",strtotime("+1 minute",$d)),
							"up"=>date("Y-m-d-H",($d)),
							"year"=>array(
									"k"=>date("Y",($d)),
									"l"=>date("Y",($d)),
							),
							"month"=>array(
									"k"=>date("Y-m",($d)),
									"l"=>date("F",($d)),
							),
							"day"=>array(
									"k"=>date("Y-m-d",($d)),
									"l"=>date("d",($d)),
							),
							"hour"=>array(
									"k"=>date("Y-m-d-H",($d)),
									"l"=>date("Ha",($d)),
							),
					
					);
					$its = array(
							new \DateTime(date("Y-m-d H:i:s",mktime($keyparts[3],$keyparts[4],0,$keyparts[1],$keyparts[2],$keyparts[0]))),
							new \DateTime(date("Y-m-d H:i:s",mktime($keyparts[3],$keyparts[4],59,$keyparts[1],$keyparts[2],$keyparts[0])))
					);
					break;
				DEFAULT:
					$delim = "month";
					$where = "";
					$its = array(
							new \DateTime(date("Y-m-d H:i:s",strtotime("now"))),
							new \DateTime(date("Y-m-d H:i:s",strtotime("-1 year")))
					);
					break;
				
				
				
				
			}
		} 
		$where = "datein >= '{$its[0]->format("Y-m-d H:i:s")}' AND datein <= '{$its[1]->format("Y-m-d H:i:s")}'";
		
		//test_array($wher); 
		
		$Data = models\records_data::getInstance()->getCountGrouped("(DATE_FORMAT(datein,'{$delimarray[$delim]['db']}')) as arraykey, budgetCounted",$where,"as_record_visits","datein ASC","DATE_FORMAT(datein,'{$delimarray[$delim]['db']}'), budgetCounted");
		$DataClicks = models\records_data::getInstance()->getCountGrouped("(DATE_FORMAT(datein,'{$delimarray[$delim]['db']}')) as arraykey, budgetCounted",$where,"as_record_clicks","datein ASC","DATE_FORMAT(datein,'{$delimarray[$delim]['db']}'), budgetCounted");
		
	//	test_array($Data);
		
		
		
		
		
		
		$n = array();
		
		$begin = $its[0];
		$end = $its[1];
		//test_array(array($begin,$end));
		
		$interval = \DateInterval::createFromDateString("1 ".$delim);
		$period = new \DatePeriod($begin, $interval, $end);
		
		//test_array(str_replace("%","",$delimarray[$delim]['db'])); 
		
		$labels = array();
		foreach ( $period as $dt ){
			$d = $dt->format(str_replace("%","",$delimarray[$delim]['db']));
			
			$n[$d] = array(
					"label"=>$dt->format($delimarray[$delim]['label']),
					"c"=>0,
					"key"=>$dt->format($delimarray[$delim]['key']),
			);
//			$labels[] = $dt->format($delimarray[$delim]['phpFlabel']);
		}
		
		
		
		$budget = $n;
		$all = $n;
		
		foreach($Data as $item){
			if ($item['budgetCounted']=="1"){
				$budget[$item['arraykey']]['c'] = $item['c'];
			}
			$all[$item['arraykey']]['c'] = $all[$item['arraykey']]['c'] + $item['c'];
			
		}
		
		$budgetClicks = $n;
		$allClicks = $n;
		
		foreach($DataClicks as $item){
			if ($item['budgetCounted']=="1"){
				$budgetClicks[$item['arraykey']]['c'] = $item['c'];
			}
			$allClicks[$item['arraykey']]['c'] = $allClicks[$item['arraykey']]['c'] + $item['c'];
			
		}
		
		
		//test_array($all); 
		
		
		
		//test_array($n); 
		//test_array(array($delim,$where,$its)); 
		
		
		
		
		
		
		
		//test_array($data); 
		//test_array($Data); 
		
		$labels = array();
		$values = array(
			"all"=>array(),
			"unique"=>array()
		);
		$keys = array();
		
		foreach ($n as $key=>$item){
			$labels[] = $item['label'];
			$values[] = $item['c']*1;
			$keys[] = $item['key'];
		}
		foreach ($all as $key=>$item){
			$values['all'][] = $item['c']*1;
		}
		foreach ($budget as $key=>$item){
			$values['unique'][] = $item['c']*1;
		}
		
		
		
		
		$valuesClicks = array(
				"all"=>array(),
				"unique"=>array()
		);
		foreach ($allClicks as $key=>$item){
			$valuesClicks['all'][] = $item['c']*1;
		}
		foreach ($budgetClicks as $key=>$item){
			$valuesClicks['unique'][] = $item['c']*1;
		}
		
		//test_array($keys); 
		$result['controls']['label']="<h3>".$label."</h3>";
		$result['controls']['links']=$links;
		
		
		$result['impressions_chart']['labels'] = $labels;
		$result['impressions_chart']['datasets'] = array(
				array(
						"label"=> "All",
						"fillColor"=> "rgba(220,220,220,0.2)",
						"strokeColor"=> "rgba(220,220,220,1)",
						"pointColor"=> "rgba(220,220,220,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(220,220,220,1)",
						"data"=> $values['all'],
						"extra"=> $keys
				),
				array(
						"label"=> "Unique",
						"fillColor"=> "rgba(151,187,205,0.2)",
						"strokeColor"=> "rgba(151,187,205,1)",
						"pointColor"=> "rgba(151,187,205,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(151,187,205,1)",
						"data"=> $values['unique'],
						"extra"=> $keys
				)
		);
		//test_array($keys); 
		$result['clicks_chart']['labels'] = $labels;
		$result['clicks_chart']['datasets'] = array(
				array(
						"label"=> "All",
						"fillColor"=> "rgba(220,220,220,0.2)",
						"strokeColor"=> "rgba(220,220,220,1)",
						"pointColor"=> "rgba(220,220,220,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(220,220,220,1)",
						"data"=> $valuesClicks['all'],
						"extra"=> $keys
				),
				array(
						"label"=> "Unique",
						"fillColor"=> "rgba(151,187,205,0.2)",
						"strokeColor"=> "rgba(151,187,205,1)",
						"pointColor"=> "rgba(151,187,205,1)",
						"pointStrokeColor"=> "#fff",
						"pointHighlightFill"=> "#fff",
						"pointHighlightStroke"=> "rgba(151,187,205,1)",
						"data"=> $valuesClicks['unique'],
						"extra"=> $keys
				)
		);
		
		
		return $GLOBALS["output"]['data'] = $result;
	}
	function build($data,$interval,$budgetCounted){
		
	}


}
