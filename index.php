<?php
date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_ALL, 'en_ZA.UTF8');
$errorPath = dirname(ini_get('error_log'));
$errorFile = $errorPath . DIRECTORY_SEPARATOR . basename(__DIR__) . "-errors.log";
ini_set("error_log", $errorFile);

if (session_id() == "") {
	$SID = @session_start();
} else $SID = session_id();
if (!$SID) {
	session_start();
	$SID = session_id();
}
$GLOBALS["output"] = array();
$GLOBALS["models"] = array();
require_once('vendor/autoload.php');

$f3 = \base::instance();
require('inc/timer.php');
require('inc/template.php');
require('inc/functions.php');
require('inc/pagination.php');
$GLOBALS['page_execute_timer'] = new timer(true);
$cfg = array();
require_once('config.default.inc.php');
if (file_exists("config.inc.php")) {
	require_once('config.inc.php');
}

$f3->set('AUTOLOAD', './|lib/|controllers/|inc/|/modules/');
$f3->set('PLUGINS', 'vendor/bcosca/fatfree/lib/');
$f3->set('CACHE', true);

$f3->set('DB', new DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '', $cfg['DB']['username'], $cfg['DB']['password']));
$f3->set('cfg', $cfg);
$f3->set('DEBUG', 3);


$f3->set('UI', 'app/|media/');
$f3->set('MEDIA', './media/|' . $cfg['media']);
$f3->set('TZ', 'Africa/Johannesburg');

$f3->set('TAGS', 'p,br,b,strong,i,italics,em,h1,h2,h3,h4,h5,h6,div,span,blockquote,pre,cite,ol,li,ul');

$f3->set("menu", array());
$f3->set("company", array());

//$f3->set('ERRORFILE', $errorFile);
//$f3->set('ONERROR', 'Error::handler');
$f3->set('ONERRORd', function ($f3) {
	// recursively clear existing output buffers:
	while (ob_get_level()) ob_end_clean();
	// your fresh page here:
	echo $f3->get('ERROR.text');
	print_r($f3->get('ERROR.stack'));
});

$version = date("YmdH");
if (file_exists("./.git/refs/heads/" . $cfg['git']['branch'])) {
	$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	$version = substr(base_convert(md5($version), 16, 10), -10);
}

$minVersion = preg_replace("/[^0-9]/", "", $version);
$f3->set('_version', $version);
$f3->set('_v', $minVersion);


$uID = isset($_SESSION['uID']) ? base64_decode($_SESSION['uID']) : "";


$userO = new \models\user();
$user = $userO->get($uID);
if (isset($_GET['auID']) && $user['su'] == '1') {
	$_SESSION['uID'] = $_GET['auID'];
	$user = $userO->get($_GET['auID']);
}
//test_array($uID); 

$f3->set('user', $user);

if ($user['ID']) {
	models\user::lastActivity($user);
}


//$f3->set('user', $user);
$f3->set('session', $SID);


$f3->route('GET|POST /', 'controllers\home->page');
$f3->route('GET|POST /where', 'controllers\where->page');
$f3->route('GET|POST /map', 'controllers\ward_map->page');


$f3->route('GET|POST /login', 'controllers\login->page');
$f3->route('GET|POST /login/do', 'controllers\login->login');


$f3->route('GET|POST /logout', function ($f3, $params) use ($user) {
	session_start();
	session_unset();
	session_destroy();
	session_write_close();
	setcookie(session_name(), '', 0, '/');
	//session_regenerate_id(true);
	
	//session_destroy();
	$f3->reroute("/login");
});


$f3->route('GET /lookup/@method/@key', function ($f3, $params) {
	$f3->call("controllers\\data\\lookup->" . $params['method']);
	
});


$f3->route('GET /temp', function ($f3) {
	$tmpl = new \template("template.twig");
	$tmpl->page = array(
			
			"template" => "temp",
			"meta" => array(
					"title" => "Ad-Server | Temp",
			),
			"css" => "",
			"js" => "",
	);
	$tmpl->output();
	
});


$f3->route('GET /t', function ($f3) {
	$time_start1 = microtime(true);
	$time_start2 = microtime(true);
	test_array(array(
			$time_start1,
			$time_start2
	));
	
});


$f3->route('GET /login', 'controllers\login->page');
$f3->route('POST /login', 'controllers\login->login');


$f3->route('GET|POST /admin', 'controllers\admin->page');
$f3->route('GET|POST /admin/wards', 'controllers\admin_wards->page');
$f3->route('GET|POST /admin/users', 'controllers\admin_users->page');
$f3->route('GET|POST /admin/councilors', 'controllers\admin_councilors->page');
$f3->route('GET|POST /admin/parties', 'controllers\admin_parties->page');





$f3->route("GET /iec/voter/@ID", function ($f3, $params) {
	
	
	$post_data="grant_type=password&username=IECWebAPIMediaZN&password=53412d349f394c3aa5de5593961d2059";
	$url="https://api.elections.org.za/token";
	
	$options = array(
			"method"=>"POST",
			"content"=>$post_data
	);
	
	$web = new \Web();
	$request = $web->request($url,$options);
	$request = (json_decode($request['body']));
	$token = $request->access_token;
	
	
	$api_options = array(
			"header"=>"Authorization: Bearer ".$token
	);
	$api_url = "https://api.elections.org.za/api/v1/Voters/GetVoterAllDetails?ID={$params['ID']}";
	
	//test_array($api_options); 
	$api = (array)  $web->request($api_url,$api_options);
	
	$api['options'] = array(
			"url"=>$api_url,
			"options"=>$api_options
	);
	
	$response = json_decode($api['body']);
	
	
	test_array($response); 
	
	
	
	
	
});





$f3->route("GET /image/@width/@height/*", function ($f3, $params) {
	$path = $_SERVER['REQUEST_URI'];
	
	
	$crop = false;
	$enlarge = false;
	$width = $params['width'];
	$height = $params['height'];
	
	
	$img_path = str_replace("/image/{$width}/{$height}/", "", $path);
	
	
	$cfg = $f3->get("cfg");
	$folder = $cfg['media'];
	
	$img_path_full = $folder . $img_path;
	
	$img_path = $f3->fixslashes($img_path);
	$img_path_full = $f3->fixslashes($img_path_full);
	
	//test_array(file_exists($img_path)); 
	$fileexisits = false;
	$fileType = "";
	if (file_exists($img_path_full)) {
		$fileexisits = true;
		
		$fileT = new \Web();
		$fileType = $fileT->mime($img_path);
		
		//test_array($fileType); 
		
		
		header('Content-Type: ' . $fileType);
		header('Accept-Ranges: bytes');
		header('Cache-control: max-age=' . (60 * 60 * 24 * 365));
		header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));
		header('Last-Modified: ' . gmdate(DATE_RFC1123, filemtime($img_path_full)));
		
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			header('HTTP/1.1 304 Not Modified');
			die();
		}
		
		//test_array($img_path); 
		
		
		$img = new \Image($img_path);
		//$img->load();
		$img->resize($width, $height, $crop, $enlarge);
		
		$img->render(str_replace("image/", "", $fileType));
		exit();
		
	} else {
		
		$f3->error("404");
	}
	
	
});


$f3->route('GET|POST /admin/upload', function ($f3, $params) {
	//test_array("woof");
	$return = array();
	$cfg = $f3->get("cfg");
	
	$folder_ext = isset($_REQUEST["folder"]) ? $_REQUEST["folder"] : "";
	$folder = $cfg['media'];
	$folder = $folder . "/" . $folder_ext . "/";
	
	$folder = $f3->fixslashes($folder);
	$folder = str_replace("//", "/", $folder);
	
	//test_array($folder); 
	
	
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
	header('Cache-Control: no-cache');
	header('Access-Control-Max-Age: 1000');
	header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
	
	
	/* 
	// Support CORS
	header("Access-Control-Allow-Origin: *");
	// other CORS headers if any...
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		exit; // finish preflight CORS requests here
	}
	*/

// 5 minutes execution time
	@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
	$targetDir = $folder;
//$targetDir = 'uploads';
	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
	if (!file_exists($targetDir)) {
		@mkdir($targetDir, 01777, true);
	}

// Get a file name
	if (isset($_REQUEST["name"])) {
		$fileName = $_REQUEST["name"];
	} elseif (!empty($_FILES)) {
		$fileName = $_FILES["file"]["name"];
	} else {
		$fileName = uniqid("file_");
	}
	
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// Remove old temp files	
	if ($cleanupTargetDir) {
		if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		}
		
		while (($file = readdir($dir)) !== false) {
			$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
			
			// If temp file is current file proceed to the next
			if ($tmpfilePath == "{$filePath}.part") {
				continue;
			}
			
			// Remove temp file if it is older than the max age and is not the current file
			if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
				@unlink($tmpfilePath);
			}
		}
		closedir($dir);
	}


// Open temp file
	if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}
	
	if (!empty($_FILES)) {
		if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		}
		
		// Read binary input stream and append it to temp file
		if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	} else {
		if (!$in = @fopen("php://input", "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	}
	
	while ($buff = fread($in, 4096)) {
		fwrite($out, $buff);
	}
	
	@fclose($out);
	@fclose($in);

// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off 
		rename("{$filePath}.part", $filePath);
	}


// Return Success JSON-RPC response
	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	
	
});


$f3->route("GET|POST /save/@function", function ($app, $params) {
	$app->call("controllers\\save\\save->" . $params['function']);
});
$f3->route("GET|POST /save/@class/@function", function ($app, $params) {
	$app->call("controllers\\save\\" . $params['class'] . "->" . $params['function']);
});
$f3->route("GET|POST /save/@folder/@class/@function", function ($app, $params) {
	$app->call("controllers\\save\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
});
$f3->route("GET|POST /data/@function", function ($app, $params) {
	$app->call("controllers\\data\\data->" . $params['function']);
});
$f3->route("GET|POST /data/@class/@function", function ($app, $params) {
	//test_array($params); 
	$app->call("controllers\\data\\" . $params['class'] . "->" . $params['function']);
});
$f3->route("GET|POST /data/@folder/@class/@function", function ($app, $params) {
	$app->call("controllers\\data\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
});

$f3->route("GET|POST /internal/emails/@class/@function", function ($app, $params) {
	$app->call("controllers\\emails\\" . $params['class'] . "->" . $params['function']);
});


$f3->route("GET|POST /keepalive", function ($app, $params) {
	$user = $app->get("user");
	unset($user["password"]);
	unset($user["global_admin"]);
	test_array($user);
});







$f3->route("GET|POST /list", function ($f3, $params) {
	ini_set('max_execution_time', 300);
	
	
	$data = file_get_contents("list.txt");
	$lines = preg_split('/\R/', $data);;
	
	$parties = models\party::getInstance()->getAll();
	
	$p = array();
	foreach ($parties as $party){
		$p[$party['party']]=$party['ID'];
	}
	
	$a = new \DB\SQL\Mapper($f3->get("DB"), "councilors");
	//test_array($p); 
	
	$d = array();
	$n = array();
	foreach ($lines as $line) {
		
		$l = preg_split('/------/', $line);
		$n[] = $l;
		
		
		$party = trim($l[1]);
		
		if (isset($p[$party])){
			$partyID = $p[$party];
		} else {
			$pv = array(
					"party"=>$party
			);
			
			$partyID = models\party::_save("",$pv);
			$p[$party] = $partyID;
		}
		
		
		
			
			
		$wardID=trim($l[2]);
		if (strlen($wardID)>3){
			$values = array(
					"fullname" => trim($l[4])." ".trim($l[5]),
					"IDNumber" => trim($l[3]),
					"partyID" => $partyID,
					"wardID" => $wardID,
			);
			
			$a->load("IDNumber='{$values['IDNumber']}'");
			
			if ($a->dry()){
				foreach ($values as $key => $value) {
					if (isset($a->$key)) {
						$a->$key = $value;
					}
					
				}
				
				$a->save();
			}
			
			$a->reset();
			
			//test_array($values);
			$d[] = $values;
		}
		
		
	}
	
	
	test_array($d);
});


$f3->route('GET /php', function () {
	phpinfo();
	exit();
});

$f3->run();


$models = $GLOBALS['models'];

///test_array($models); 
$t = array();
foreach ($models as $model) {
	$c = array();
	foreach ($model['m'] as $method) {
		$c[] = $method;
	}
	$model['m'] = $c;
	$t[] = $model;
}

//test_array($t); 

$models = $t;
$pageTime = $GLOBALS['page_execute_timer']->stop("Page Execute");

$GLOBALS["output"]['timer'] = $GLOBALS['timer'];

$GLOBALS["output"]['models'] = $models;


$GLOBALS["output"]['page'] = array(
		"page" => $_SERVER['REQUEST_URI'],
		"time" => $pageTime
);

//test_array($tt); 

if ($f3->get("ERROR")) {
	exit();
}

if (($f3->get("AJAX") && ($f3->get("__runTemplate") == false) || $f3->get("__runJSON"))) {
	header("Content-Type: application/json");
	echo json_encode($GLOBALS["output"]);
} else {
	
	//if (strpos())
	if ($f3->get("NOTIMERS")) {
		exit();
	}
	
	
	echo '
					<script type="text/javascript">
				      updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
					</script>
					</body>
</html>';
	
}


?>
