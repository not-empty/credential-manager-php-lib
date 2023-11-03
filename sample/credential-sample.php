<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CredentialManager\Credential;

$redisConfig = [
    'host' => 'localhost',
    'port' => 6379,
];

$credential = new Credential($redisConfig);

$credential->setCredential(
    'originName',
    'serviceName',
    'CredentialSample'
);

$getCredential = $credential->getCredential('originName', 'serviceName');

print_r($getCredential);
echo PHP_EOL;
