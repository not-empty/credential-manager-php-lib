<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CredentialManager\Credential;

$credential = new Credential();

$credential->setCredential(
    'originName',
    'serviceName',
    'CredentialSample'
);

$getCredential = $credential->getCredential('originName', 'serviceName');

print_r($getCredential);
echo PHP_EOL;
