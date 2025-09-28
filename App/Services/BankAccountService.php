<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use Aws\Sns\SnsClient;
use App\Events\WithdrawalMade;
use App\Events\DepositMade;
use Exception;

class BankAccountService {
    private AccountRepository $repository;
    private SnsClient $sns;
    private string $topicArn;

    public function __construct(AccountRepository $repository, SnsClient $sns, string $topicArn) {
        $this->repository = $repository;
        $this->sns = $sns;
        $this->topicArn = $topicArn;
    }

    public function withdraw(int $accountId, float $amount): bool {
        $account = $this->repository->findById($accountId);
        if (!$account) throw new Exception("Account not found");
        if ($account->balance < $amount) throw new Exception("Insufficient funds");

        $newBalance = $account->balance - $amount;
        $success = $this->repository->updateBalance($accountId, $newBalance);

        if ($success) {
            $event = new WithdrawalMade($accountId, $amount);
            $this->publishEvent($event);
        }

        return $success;
    }

    public function deposit(int $accountId, float $amount): bool {
        $account = $this->repository->findById($accountId);
        if (!$account) throw new Exception("Account not found");

        $newBalance = $account->balance + $amount;
        $success = $this->repository->updateBalance($accountId, $newBalance);

        if ($success) {
            $event = new DepositMade($accountId, $amount);
            $this->publishEvent($event);
        }

        return $success;
    }

    public function getBalance(int $accountId): ?float {
        $account = $this->repository->findById($accountId);
        return $account ? $account->balance : null;
    }

    private function publishEvent($event): void {
        $payload = json_encode([
            'event' => get_class($event),
            'data'  => [
                'accountId' => $event->accountId,
                'amount'    => $event->amount,
                'timestamp' => date('c')
            ]
        ]);

        $this->sns->publish([
            'TopicArn' => $this->topicArn,
            'Message'  => $payload,
        ]);
    }
}
