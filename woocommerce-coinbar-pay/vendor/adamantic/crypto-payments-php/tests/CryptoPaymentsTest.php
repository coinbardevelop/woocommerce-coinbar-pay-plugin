<?php declare(strict_types=1);

use Adamantic\CryptoPayments\Currency;
use Adamantic\CryptoPayments\PaymentGateway;
use Adamantic\CryptoPayments\PaymentRequest;
use Adamantic\CryptoPayments\PaymentRequestSimpleItem;
use Adamantic\CryptoPayments\PaymentStatus;
use Adamantic\CryptoPayments\PaymentStatusListener;
use Adamantic\CryptoPayments\PaymentStatusUpdate;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\TestCase;

class NoOpGateway extends \Adamantic\CryptoPayments\PaymentGatewayBase {
    public function requestPayment(PaymentRequest $request): PaymentStatusUpdate
    {
        return (new PaymentStatusUpdate())
            ->setStatus(PaymentStatus::REQUESTED)
            ->setRequestId($request->getUuid());
    }

}

class NoOpConfig extends \Adamantic\CryptoPayments\PaymentGatewayConfig
{
    function createGateway(): PaymentGateway
    {
        return new NoOpGateway();
    }

    function get($key): string
    {
        return $key;
    }
}

class PaymentStatusUpdateCounter implements PaymentStatusListener {
    public int     $statusUpdateCount = 0;
    public ?string $lastStatus = null;
    function onPaymentStatusUpdate(PaymentStatusUpdate $update)
    {
        $this->statusUpdateCount++;
        $this->lastStatus = $update->getStatus();
    }
}


final class CryptoPaymentsTest extends TestCase
{

    public function testCreateGateway() {
        $gw = self::createGateway();
        self::assertInstanceOf(PaymentGateway::class, $gw);
    }

    public function testCreateRequest() {
        $rq = PaymentRequest::createNew();
        self::assertGreaterThan(0, $rq->getTimestampMs());
        self::assertNotEmpty($rq->getUuid());
    }


    public function testCreatePaymentRequest() {
        $cf = new NoOpConfig();
        $rq = PaymentRequest::createNew()
            ->setCurrency(new Currency('EUR', 2))
            ->setUserId('1158375')
            ->setUserEmail('m.rossi@example.com')
            ->addItem((new PaymentRequestSimpleItem())
                ->setType('service')
                ->setDescription('Service Description')
                ->setId('1552498')
                ->setUnits(1)
                ->setAmount(BigDecimal::of('0.01'))
            )
            ->addItem((new PaymentRequestSimpleItem())
                ->setType('product')
                ->setDescription('Product Description')
                ->setId('1155151')
                ->setUnits(1)
                ->setAmount(BigDecimal::of('0.02'))
            );

        $gw = $cf->createGateway();
        $rs = $gw->requestPayment($rq);
        self::assertEquals(PaymentStatus::REQUESTED, $rs->getStatus());

    }


    public function testStatusUpdateListens() {
        $cnt = new PaymentStatusUpdateCounter();
        self::createGateway()->addPaymentStatusListener($cnt);
        self::createGateway()->onIncomingPaymentStatusUpdate(
            (new PaymentStatusUpdate())
                ->setRequestId('123')
                ->setStatus(PaymentStatus::AUTHORIZED));
        self::assertEquals(1, $cnt->statusUpdateCount);
        self::assertEquals(PaymentStatus::AUTHORIZED, $cnt->lastStatus);
    }

    public function testStatusUpdateListensAtMostOnce() {
        $cnt = new PaymentStatusUpdateCounter();

        $gw = self::createGateway();
        $gw->addPaymentStatusListener($cnt);
        $gw->addPaymentStatusListener($cnt);

        self::createGateway()->onIncomingPaymentStatusUpdate(
            (new PaymentStatusUpdate())
                ->setRequestId('123')
                ->setStatus(PaymentStatus::AUTHORIZED));
        self::assertEquals(1, $cnt->statusUpdateCount);
    }

    private static function createGateway(): PaymentGateway {
        $cfg = new NoOpConfig();
        return $cfg->createGateway();
    }
}
