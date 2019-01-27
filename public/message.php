<?php

require __DIR__ . '/../vendor/autoload.php';

use rsavinkov\SmsSender\ApplicationRegistry;
use rsavinkov\SmsSender\Controller;

$controller = new Controller(ApplicationRegistry::instance());
$response = $controller->sendMessageAction();

http_response_code($response->getCode());
header('Content-Type:application/json');
echo json_encode($response->toArray());


