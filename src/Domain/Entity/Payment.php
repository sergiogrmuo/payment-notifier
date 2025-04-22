<?php
namespace App\Domain\Entity;

class Payment
{
    public string $notificationId;

    public function __construct(
        public float $amount,
        public string $status,
        public string $creditorAccount,
        public string $debtorAccount
    ) {
        $this->notificationId = uniqid(); // Reemplazar con UUID si se requiere
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'status' => $this->status,
            'creditor_account' => $this->creditorAccount,
            'debtor_account' => $this->debtorAccount,
            'notification_id' => $this->notificationId,
        ];
    }
}