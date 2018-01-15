<?php

require __DIR__ . '/vendor/autoload.php';

try{
    $pdo = new PDO('sqlite:'.dirname(__FILE__).'/data/pci.sqlite');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT    
} catch(Exception $e) {
    echo "Cannot access to sqlite database : ".$e->getMessage();
    die();
}

$pheanstalk = new \Pheanstalk\Pheanstalk('127.0.0.1');

if ($pheanstalk->getConnection()->isServiceListening()) {
  $job = $pheanstalk
    ->watch('build')
    ->ignore('default')
    ->reserve();
    
  $data = unserialize($job->getData());    
  
  $projectId = $data['projectId'];
  $raw = $data['raw'];
  $payload = json_decode($raw, true);      

  // Run build
  $project = 'https://github.com/'.$payload['repository']['full_name'].'.git';
  $sha = $payload['head_commit']['id'];
  $command = '/bin/sh '.__DIR__.'/build.sh ' . $project . ' ' . $sha; 
  
  set_time_limit(0);
  exec($command, $output, $return_var);
  
  $output = file_get_contents(__DIR__. '/workspace/'.$sha.'.log'); 
  $status = 0;  
  if ((int) $return_var === 0) {    
    $status = 1;
  } else {
    $status = 2;
  }
  
  // Save build
   try {
    $stmt = $pdo->prepare("INSERT INTO build VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
      null,
      $payload['head_commit']['id'],
      $projectId,
      $raw,
      $output,
      $status,
      49,
      date("Y-m-d H:i:s")
    ]);
    
    $pheanstalk->delete($job);        
  } catch(\Exception $e) {
    print_r($e);
  }
}
