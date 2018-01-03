<?php
declare(strict_types = 1);

require __DIR__ . '/vendor/autoload.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents("php://input");
    
    $payload = json_decode($raw, true); 
    $project = 'https://github.com/'.$payload['name'].'.git';
    $sha = $payload['sha'];
    $command = './build.sh ' . $project . ' ' . $sha;
    
    exec($command, $output, $return_var);
    
    if ((int) $return_var === 0) {        
        file_put_contents('workspace/'.$sha.'/status.svg', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="88" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="88" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h37v20H0z"/><path fill="#4c1" d="M37 0h51v20H37z"/><path fill="url(#b)" d="M0 0h88v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="195" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="270">build</text><text x="195" y="140" transform="scale(.1)" textLength="270">build</text><text x="615" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="410">passing</text><text x="615" y="140" transform="scale(.1)" textLength="410">passing</text></g> </svg>');
    } else {
        file_put_contents('workspace/'.$sha.'/status.svg', '
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="82" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="82" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h37v20H0z"/><path fill="#e05d44" d="M37 0h45v20H37z"/><path fill="url(#b)" d="M0 0h82v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="195" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="270">build</text><text x="195" y="140" transform="scale(.1)" textLength="270">build</text><text x="585" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="350">failure</text><text x="585" y="140" transform="scale(.1)" textLength="350">failure</text></g> </svg>');
    }
} else {
    if (
        isset($_GET['status']) && 
        isset($_GET['sha']) && 
        is_dir('workspace/'.$_GET['sha']) && 
        file_exists('workspace/'.$_GET['sha'].'/status.svg')
    ) {
        header('Content-Type: image/svg+xml;charset=utf-8');
        readfile('workspace/'.$_GET['sha'].'/status.svg');
    } else if (
        isset($_GET['sha']) && 
        is_dir('workspace/'.$_GET['sha']) && 
        file_exists('workspace/'.$_GET['sha'].'/output_console.txt')
    ) {
        header('Content-Type: text/plain');
        readfile('workspace/'.$_GET['sha'].'/output_console.txt');
    } else {
         header('Content-Type: application/json');
         echo json_encode(
            [
                'error' => sprintf(
                    'Unknown route: %s %s', 
                    $_SERVER['REQUEST_METHOD'], 
                    $_SERVER['REQUEST_URI']
                )
            ]
        );
    }
}
