<?php

// php7.1 strict mode
declare(strict_types = 1);

// psr-4 autoloader
require __DIR__ . '/vendor/autoload.php';

$pheanstalk = new \Pheanstalk\Pheanstalk('127.0.0.1');



// 0) Init SQLITE pdo instance
try{
    global $pdo;
    $pdo = new PDO('sqlite:'.dirname(__FILE__).'/data/pci.sqlite');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT    
} catch(Exception $e) {
    echo "Cannot access to sqlite database : ".$e->getMessage();
    die();
}

// 1) Routes
$routes = [
  [
    'method' => 'POST', 
    'path' => '/github-webhook', 
    'callback' => 'build'
  ],
  [
    'method' => 'GET',
    'path' => '/output/:sha',
    'callback' => 'output'
  ],
  [
    'method' => 'GET',
    'path' => '/status/:sha',
    'callback' => 'status'
  ],
  [
    'method' => 'GET',
    'path' => '/',
    'callback' => 'home'
  ],
  [
    'method' => 'GET',
    'path' => '/projects/:owner/:project/builds/:id',
    'callback' => 'project'
  ],
  [
    'method' => 'DELETE',
    'path' => '/projects/:token',
    'callback' => 'delete_project'
  ]
];

// 2) Compose clean URI
$uri = $_SERVER['REQUEST_METHOD'] . ' ' . trim($_SERVER['REQUEST_URI'], '/');

// 3) Route matcher
$match = false;
foreach ($routes as $route) {
  $path = $route['method'] . ' ' . preg_replace('#:([\w]+)#', '([^/]+)', trim($route['path'], '/'));
  
  if(preg_match("#^$path$#i", $uri, $matches)) {
  
    array_shift($matches);
    
    call_user_func_array(
      $route['callback'], 
      $matches
    );
    
    $match = true;
    
    break;
  }
}

// 4) If not route matches
if (!$match) {  
  send(400, ['error' => sprintf('Unknown route: %s', $uri)]);
}

// Callables

/**
 * Run build and store svg status image
 * 
 * Payload:
 *  {
 *    "id": 4458399707,
 *    "sha": "1c844bac2dbb5814242be1cb1a45594e42180718",
 *    "name": "dridi-walid/micro-framework-skeleton"
 *  }
 *
 * @return void
 */
function build() {
  
  global $pheanstalk;    
  global $pdo;
       
  // HOST whitelist
  $whitelist_hostname = ['_', 'localhost', 'http://github.com'];
  
  if (!in_array($_SERVER['SERVER_NAME'], $whitelist_hostname)) {
    send(401, ['error' => $_SERVER['SERVER_NAME'] . ' is not supported yet!']);
    exit;
  }
  
  $raw = file_get_contents("php://input");
  
  $payload = json_decode($raw, true);   
  
  // Save project is it's not already configured
  $stmt = $pdo->prepare("SELECT COUNT(1) AS count, id, full_name, token FROM repository WHERE id = '" . $payload['repository']['id'] . "'");
  $stmt->execute();
  $result = $stmt->fetch();
  
  $projectId = null;
  $token = null;
  
  if ((int) $result['count']) {
    $projectId = $result['id'];
    $token = $result['token'];
  } else {
    try {
      $stmt = $pdo->prepare("INSERT INTO repository VALUES (?, ?, ?)");
      
      $token = uuid();
      
      $stmt->execute([
        $payload['repository']['id'],
        $payload['repository']['full_name'],
        $token
      ]);   
      
      $projectId = $payload['repository']['id']; 
      
      $pheanstalk
        ->useTube('build')
        ->put("job payload goes here\n");     
        
      send(200, ['token' => $token]);      
    } catch(\Exception $e) {
      send(500, ['error' => $e->getMessage()]);
    }
  }      
}

/**
 * Return build raw log
 *
 * @param $sha string Commit hash
 *
 * @return void
 */
function output($sha) {
  if (file_exists('workspace/'.$sha.'/output_console.txt')) {
    header('Content-Type: text/plain');
    readfile('workspace/'.$sha.'/output_console.txt');
  } else {   
    send(400, ['error' => 'Output log not found']);
  }
}

/**
 * Display build status by commit hash
 *
 * @return void
 */
function status($sha) {
  header('Content-Type: image/svg+xml;charset=utf-8');
  if (file_exists('workspace/'.$sha.'/status.svg')) {
      // status.svg exists
      readfile('workspace/'.$sha.'/status.svg');
  } else {
      // Not found
      echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="100" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h37v20H0z"/><path fill="#9f9f9f" d="M37 0h63v20H37z"/><path fill="url(#b)" d="M0 0h100v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="195" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="270">build</text><text x="195" y="140" transform="scale(.1)" textLength="270">build</text><text x="675" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="530">not found</text><text x="675" y="140" transform="scale(.1)" textLength="530">not found</text></g> </svg>';
  }
}

/**
 * Return http response
 *
 * @param $code int Http iso codes
 * @param message string {error: '...'}
 *
 * @return void
 */
function send($code, $message) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($message);
}

/**
 * Home page
 */
function home() {
  global $pdo;
  
  $stmt = $pdo->prepare("
    SELECT 	repository.* , build.id as build_id, build.sha, MAX(build.created_at)
    FROM repository
    INNER JOIN build	
	    ON	 build.repository_id = repository.id
    GROUP BY repository.id
  ");
  $stmt->execute();
  $repositories = $stmt->fetchAll();

  include __DIR__ . '/views/home.php';
}

/**
 * Project page
 */
function project($owner, $project, $build) {
  global $pdo;
  
  $stmt = $pdo->prepare("
    SELECT * 
    FROM build 
    WHERE sha = ?
  ");
  $stmt->execute([$build]);
  $build = $stmt->fetch();
  
  $build['payload'] = json_decode($build['payload'], true); 
  
  $stmt = $pdo->prepare("
    SELECT * 
    FROM build 
    WHERE repository_id = ?
    ORDER BY created_at DESC
  ");
  $stmt->execute([$build['repository_id']]);
  
  $history = $stmt->fetchAll(); 
  
  
  include __DIR__ . '/views/project.php';
}

/**
 * Delete project based on its token
 */
function delete_project($token) {
  send(501, ['error' => 'Not yet implemented']);
}

/**
 * Return uuid
 */
function uuid() {
  return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    // 32 bits for "time_low"
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    // 16 bits for "time_mid"
    mt_rand( 0, 0xffff ),
    // 16 bits for "time_hi_and_version",
    // four most significant bits holds version number 4
    mt_rand( 0, 0x0fff ) | 0x4000,
    // 16 bits, 8 bits for "clk_seq_hi_res",
    // 8 bits for "clk_seq_low",
    // two most significant bits holds zero and one for variant DCE1.1
    mt_rand( 0, 0x3fff ) | 0x8000,
    // 48 bits for "node"
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
  );
}
