<?php

require __DIR__ . '/../vendor/autoload.php';

use rsavinkov\SmsSender\ApplicationRegistry;
use rsavinkov\SmsSender\Controller;
use rsavinkov\SmsSender\MessageService;
use MessageBird\Client;

$applicationRegistry = ApplicationRegistry::instance();
$messageBird = new Client($applicationRegistry->getMessageBirdApiKey());
$messageService = new MessageService($messageBird);

$controller = new Controller($messageService, $applicationRegistry->getEnvironment());
$response = $controller->sendMessageAction();

http_response_code($response->getCode());
header('Content-Type:application/json');
die(json_encode($response->toArray()));


