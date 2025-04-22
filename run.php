<?php
require __DIR__ . '/vendor/autoload.php';

use App\Domain\Entity\Payment;
use App\Infrastructure\Jwt\FirebaseJwtSigner;
use App\Infrastructure\Http\GuzzlePaymentNotifier;
use App\Application\SendPaymentNotification;

$config = require __DIR__ . '/config.php';

$payment = new Payment(
    amount: 120.75,
    status: 'completed',
    creditorAccount: 'ES1234567890',
    debtorAccount: 'ES0987654321'
);

$signer = new FirebaseJwtSigner($config['secret']);
$notifier = new GuzzlePaymentNotifier($config['endpoint'], $signer);

$useCase = new SendPaymentNotification($notifier);

try {
    $useCase->execute($payment);
    echo "✅ Notificación enviada correctamente.\n";
} catch (Throwable $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
