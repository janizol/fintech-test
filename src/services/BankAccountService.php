<?php

class BankAccountService {
    private BankAccountRepository $repository;

    public function __construct(BankAccountRepository $repository) {
        $this->repository = $repository;
    }

    public function withdraw(int $accountId, float $amount): bool {
        $account = $this->repository->findById($accountId);
        if (!$account) {
            throw new Exception("Account not found");
        }

        if ($account->balance < $amount) {
            throw new Exception("Insufficient funds");
        }

        $newBalance = $account->balance - $amount;
        return $this->repository->updateBalance($accountId, $newBalance);
    }

    public function deposit(int $accountId, float $amount): bool {
        $account = $this->repository->findById($accountId);
        if (!$account) {
            throw new Exception("Account not found");
        }

        $newBalance = $account->balance + $amount;
        return $this->repository->updateBalance($accountId, $newBalance);
    }

    public function getBalance(int $accountId): ?float {
        $account = $this->repository->findById($accountId);
        return $account ? $account->balance : null;
    }
}
