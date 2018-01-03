<?php
declare(strict_types = 1);

require __DIR__ . '/vendor/autoload.php';

header('content-type', 'application/json');

echo json_encode(['status' => 'fail']);
