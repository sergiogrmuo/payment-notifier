<?php
namespace App\Application;

use App\Domain\Entity\Payment;
use App\Domain\Port\NotifierInterface;

class SendPaymentNotification
{
    public function __construct(
        private NotifierInterface $notifier
    ) {}

    public function execute(Payment $payment): void
    {
        if (!$this->notifier->notify($payment)) {
            throw new \RuntimeException("No se pudo enviar la notificación.");
        }
    }
}