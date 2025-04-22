<?php
namespace App\Domain\Port;

use App\Domain\Entity\Payment;

interface NotifierInterface
{
    public function notify(Payment $payment): bool;
}