<?php

// php7.1 strict mode
declare(strict_types = 1);

// psr-4 autoloader
require __DIR__ . '/vendor/autoload.php';

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
    'path' => '/build', 
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
  error(400, sprintf('Unknown route: %s', $uri));
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
  $raw = file_get_contents("php://input");
        
  $payload = json_decode($raw, true); 
  $project = 'https://github.com/'.$payload['name'].'.git';
  $sha = $payload['sha'];
  $command = './build.sh ' . $project . ' ' . $sha;
  
  exec($command, $output, $return_var);
  
  if ((int) $return_var === 0) {        
    // Passing
    file_put_contents('workspace/'.$sha.'/status.svg', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="88" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="88" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h37v20H0z"/><path fill="#4c1" d="M37 0h51v20H37z"/><path fill="url(#b)" d="M0 0h88v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="195" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="270">build</text><text x="195" y="140" transform="scale(.1)" textLength="270">build</text><text x="615" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="410">passing</text><text x="615" y="140" transform="scale(.1)" textLength="410">passing</text></g> </svg>');
  } else {
    // Failure
    file_put_contents('workspace/'.$sha.'/status.svg', '
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="82" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="82" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h37v20H0z"/><path fill="#e05d44" d="M37 0h45v20H37z"/><path fill="url(#b)" d="M0 0h82v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="195" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="270">build</text><text x="195" y="140" transform="scale(.1)" textLength="270">build</text><text x="585" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="350">failure</text><text x="585" y="140" transform="scale(.1)" textLength="350">failure</text></g> </svg>');
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
    error(400, 'Output log not found');
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
function error($code, $message) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode(
      [
          'error' => $message
      ]
  );
}

/**
 * Home page
 */
function home() {
  global $pdo;
  
  $stmt = $pdo->prepare("SELECT * FROM project");
  $stmt->execute();
  $projects = $stmt->fetchAll();

  include __DIR__ . '/views/home.php';
}

/**
 * Project page
 */
function project($owner, $project, $build) {
  include __DIR__ . '/views/project.php';
}

/**
 * Delete project based on its token
 */
function delete_project($token) {
  error(501, 'Not yet implemented');
}
