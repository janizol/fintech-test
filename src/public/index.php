<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../controllers/BankAccountController.php";

// Create DB connection
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$pdo = new PDO($dsn, DB_USER, DB_PASS);

// Create controller
$bankAccountModel = new BankAccount($pdo);
$controller = new BankAccountController($bankAccountModel);

// Handle request
$accountId = $_GET['accountId'] ?? null;
$amount = $_GET['amount'] ?? null;

if ($accountId && $amount) {
    echo $controller->withdraw((int)$accountId, $amount);
} else {
    echo "Missing accountId or amount";
}
