<?php
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Payment;

class PaymentTest extends TestCase
{
    public function testPaymentToArrayIncludesAllFields()
    {
        $payment = new Payment(
            amount: 99.99,
            status: 'completed',
            creditorAccount: 'ES1111222233334444',
            debtorAccount: 'ES5555666677778888'
        );

        $array = $payment->toArray();

        $this->assertEquals(99.99, $array['amount']);
        $this->assertEquals('completed', $array['status']);
        $this->assertEquals('ES1111222233334444', $array['creditor_account']);
        $this->assertEquals('ES5555666677778888', $array['debtor_account']);
        $this->assertArrayHasKey('notification_id', $array);
    }
}