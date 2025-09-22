<?php

// Assuming using some framework or raw PHP; using PDO for DB, AWS SNS PHP SDK for SNS, etc.

require 'vendor/autoload.php';

use Aws\Sns\SnsClient;

class WithdrawalEvent {
    private $amount;
    private $accountId;
    private $status;

    public function __construct($amount, $accountId, $status) {
        $this->amount = $amount;
        $this->accountId = $accountId;
        $this->status = $status;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getAccountId() {
        return $this->accountId;
    }

    public function getStatus() {
        return $this->status;
    }

    public function toJson() {
        return sprintf(
            '{"amount":"%s","accountId":%d,"status":"%s"}',
            (string)$this->amount,
            $this->accountId,
            $this->status
        );
    }
}

class BankAccountController {

    private $pdo;
    private $snsClient;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;

        $this->snsClient = new SnsClient([
            'version' => 'latest',
            'region'  => 'YOUR_REGION', // Specify your region
            // Credentials etc. configuration as needed
        ]);
    }

    public function withdraw($accountId, $amount) {
        // Check current balance
        $sql = "SELECT balance FROM accounts WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$accountId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $currentBalance = $row ? $row['balance'] : null;

        if ($currentBalance !== null && bccomp($currentBalance, $amount, 2) >= 0) {
            // Update balance
            $sql = "UPDATE accounts SET balance = balance - ? WHERE id = ?";
            $stmtUpdate = $this->pdo->prepare($sql);
            $rowsAffected = $stmtUpdate->execute([$amount, $accountId]) ? $stmtUpdate->rowCount() : 0;

            if ($rowsAffected > 0) {
                return "Withdrawal successful";
            } else {
                // In case the update fails for reasons other than a balance check
                return "Withdrawal failed";
            }
        } else {
            // Insufficient funds
            return "Insufficient funds for withdrawal";
        }

        // After a successful withdrawal, publish a withdrawal event to SNS
        $event = new WithdrawalEvent($amount, $accountId, "SUCCESSFUL");
        $eventJson = $event->toJson();

        // Convert event to JSON string
        $snsTopicArn = "arn:aws:sns:YOUR_REGION:YOUR_ACCOUNT_ID:YOUR_TOPIC_NAME";

        $result = $this->snsClient->publish([
            'Message'  => $eventJson,
            'TopicArn' => $snsTopicArn,
        ]);

        return "Withdrawal successful";
    }
}

// Example usage (not part of port, for context)
$dsn = "mysql:host=your_db_host;dbname=your_db_name;charset=utf8mb4";
$pdo = new PDO($dsn, "username", "password");
$controller = new BankAccountController($pdo);

// Suppose receiving HTTP request params
$accountId = isset($_GET['accountId']) ? intval($_GET['accountId']) : null;
$amount = isset($_GET['amount']) ? $_GET['amount'] : null;  // might want to ensure BigDecimal-like correctness

if ($accountId !== null && $amount !== null) {
    echo $controller->withdraw($accountId, $amount);
} else {
    echo "Missing accountId or amount";
}
