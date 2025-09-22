<?php

require_once __DIR__ . "/../models/BankAccount.php";
require_once __DIR__ . "/../models/WithdrawalEvent.php";

use Aws\Sns\SnsClient;

class BankAccountController {
    private $bankAccount;
    private $snsClient;

    public function __construct(BankAccount $bankAccount) {
        $this->bankAccount = $bankAccount;

        $this->snsClient = new SnsClient([
            'version' => 'latest',
            'region'  => AWS_REGION
            // credentials can be injected via environment/SDK
        ]);
    }

    public function withdraw($accountId, $amount) {
        $balance = $this->bankAccount->getBalance($accountId);

        if ($balance !== null && bccomp($balance, $amount, 2) >= 0) {
            $success = $this->bankAccount->updateBalance($accountId, $amount);

            if ($success) {
                $event = new WithdrawalEvent($amount, $accountId, "SUCCESSFUL");
                $this->publishEvent($event);
                return "Withdrawal successful";
            }
            return "Withdrawal failed";
        }

        return "Insufficient funds";
    }

    private function publishEvent(WithdrawalEvent $event) {
        $this->snsClient->publish([
            'Message'  => $event->toJson(),
            'TopicArn' => SNS_TOPIC_ARN
        ]);
    }
}
