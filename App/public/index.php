<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php';

use App\Controllers\BankAccountController;
use App\Repositories\AccountRepository;
use App\Services\BankAccountService;
use Aws\Sns\SnsClient;

// Setup DB connection
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
$pdo = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Setup AWS SNS client
$snsClient = new SnsClient([
    'region'  => AWS_REGION,
    'version' => 'latest'
]);

// Wire dependencies
$repo = new AccountRepository($pdo);
$service = new BankAccountService($repo, $snsClient, SNS_TOPIC_ARN);
$controller = new BankAccountController($service);

// Example usage (for testing)
$controller->deposit(1, 100);
$controller->withdraw(1, 50);
$controller->balance(1);
