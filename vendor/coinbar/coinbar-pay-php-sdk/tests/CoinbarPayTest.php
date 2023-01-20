<?php declare(strict_types=1);

use Adamantic\CryptoPayments\Currency;
use Adamantic\CryptoPayments\PaymentGateway;
use Adamantic\CryptoPayments\PaymentRequest;
use Adamantic\CryptoPayments\PaymentRequestSimpleItem;
use Adamantic\CryptoPayments\PaymentStatus;
use Adamantic\CryptoPayments\PaymentStatusListener;
use Adamantic\CryptoPayments\PaymentStatusUpdate;
use Brick\Math\BigDecimal;
use CoinbarPay\Sdk\CoinbarCypher;
use CoinbarPay\Sdk\CoinbarPaymentGateway;
use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig;
use CoinbarPay\Sdk\CoinbarPaymentGatewayEnvConfig;
use CoinbarPay\Sdk\PaymentRequestToken;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

Dotenv::createImmutable(__DIR__)->safeLoad();

class PaymentStatusUpdateCounter implements PaymentStatusListener {
    public int     $statusUpdateCount = 0;
    public ?string $lastStatus = null;
    function onPaymentStatusUpdate(PaymentStatusUpdate $update)
    {
        $this->statusUpdateCount++;
        $this->lastStatus = $update->getStatus();
    }
}


final class CoinbarPayTest extends TestCase
{

    public function testCreateGateway() {
        $gw = self::createGateway();
        self::assertInstanceOf(CoinbarPaymentGateway::class, $gw);
    }

    public function testCreateRequest() {
        $rq = PaymentRequest::createNew();
        self::assertGreaterThan(0, $rq->getTimestampMs());
        self::assertNotEmpty($rq->getUuid());
    }

    public function testCreateRequestToken() {
        $cf = new CoinbarPaymentGatewayEnvConfig();
        $rq = PaymentRequest::createNew()
            ->setCurrency(new Currency('EUR', 2))
            ->setUserId('1158375')
            ->setUserEmail('m.rossi@example.com')
            ->addItem((new PaymentRequestSimpleItem())
                ->setType('service')
                ->setDescription('Service Description')
                ->setId('1552498')
                ->setUnits(1)
                ->setAmount(BigDecimal::of('25.55'))
            );
        $tk = PaymentRequestToken::from($rq, $cf);
        $encoded = $tk->encode();
        self::assertNotEmpty($encoded);
        $decoded = json_decode((new CoinbarCypher($cf))->decode($encoded));
        self::assertEquals('Service Description', $decoded->products[0]->product_name);
    }

    public function testCreatePaymentRequest() {
        $cf = new CoinbarPaymentGatewayEnvConfig();
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
        self::assertStringContainsString($cf->get($cf::CBPAY_SERVICE_CLIENT_ID), $rs->getFrontendRedirectUrl());

        echo $rs->getFrontendRedirectUrl();
    }

    public function testDecodeResponse() {
        $responses = [
            'FAILED'   => 'ODU0UnRNV1g1a242WWhYTlNpaEtkd0ljbCszRXQxVWVHOTlUSnJvc3VuSlQ1OW42ZFl0ZUxSMjdBQWJmL3BsQXhucFRmS2FnM2owYjFpQmhMKzdaM3JCT3piU1A2VGJ4Q3VnbnIrUWZTUWZkODJoV1JBSTlZVktRVVFQT2tETmE5dmRielBKbTl4TFFYOHYwVlI4bUR0RzM2MXlwYk16SXZKSDVUOGt3bmlYVjh3dXZUWHVvVlhqMHhvb3ZaMFpOVHRNMkQ2Q1kvbkZiQXZ1TDE3ODN5cHFMWENGTU50UGdFcTFlNGR5RzNqY01WRng1Nnh4aktWamNTMi9rcmxPSlpDL2IzQlZkbXhEOTFhK1NlQlFhWWFsUkI5Y29pUzY2VHZsNmRHV05yOXhrclVlOFMxMGU0NGtLV3NhSytZYmJzZkdJZjlaOTN3MFVzQ1FNY3c3SWNleHFkaDl3YmlVYndCOHNrRjhFcStGT0pqcTlRdXoxeCtHNTcyUFNtdHQwYnNmOXZscWdNL0ZHbzRRaW4wMHhiVC9DSDUzOHMxQmhubUd1YmY1QzU4dXY3VFZ3cHlsQ3d2NVBKOUVjOE12VUI1TlBHaUJ4T3dKbGx1d1VISkFXOG1NZkNwTkxDanRRVkZVWUdnbUdnZExxN2NudVNBVHM5elBzTkFzOFUvd2xYeGMyd25ybjlaMCswT3N4NWltSS9FUklscGJxZVIzZytFWkRGNGFkS1ZJY2psV3hvNVdscU9wRFBmZ0FvZUNEN0J5Z2hoR3o2RlpnYUc0TkxNMklIcXg1d0RXNG9hNGRUb2FUM2pGT0Z4b0xGc2hsc0tFU09qMU9VQ2o5OGlBL0xINmxoZ1RnWk1UeDgzNnJJQmFqUFl3K2NLU3dqVXFtbFYxSUVWWFJ0UE5CTkxTQ25kRlljQjJONDFPUW94VUE5V3VQVGdZcUxJNWdCNDRaU2VES0UxVDMyYkFtcXN6YTIrUk5wdjZjVEVNNzJNVFUwZVhndU1IMUluNWovL2t1Y08rcGMxSGgvSVRTTXJsbVFpYnVFNGR3R3F2T0t2eEFqZXNlR1hQZWZNVHZOZ09iQ0VuQjcvdXBDUTNvK1NHMndvN2FLdXd2cmM4N1JBOUNDL2tmT1E9PQ==',
            'CANCELED' => 'ODU0UnRNV1g1a242WWhYTlNpaEtkd0ljbCszRXQxVWVHOTlUSnJvc3VuSjgvMzY0MHJ3WjlFL045Tm1RQTdvZGpaNW5KYXN4T0tCekFwYnBFRlFHcmhLd3ZDQmxwQ2JpYXlucTF6MEMrbUFtamlFM0FueEI5UFZsWTUyNElscWhndDRwTGpVRGZLSWlrSEp3Q1l6aERyVzZVWGV1d1FqVTFPbmhDRmsybjA4Z2g1d0FzRDJYSU1pWUtTZzdXcFZvQnVvTGFqWlBhTzI3ZDJJOUdmWjlEOGFKYVcwTGdlU1lORFc3ank4WWQxRVZHMTYxRFkxU0JoNWhndERsTFN0ZmszUFJpbUR3dFFSbU1JdDdlSEJPN01zMVhPS1h0cTNYRWcyU2YvOE9OTGRPVjU5SFA2alRGQkhzUVo4djFhTEg5ZWoxcVVEaFF0aVNpVXBQNmlTS0plTU1NM21YeVNhdkw2R0UydWk1SU1lK05ueTNWOSs2blhBd1VFb254MVdxdW5FZjY2SUVFVjFEV01KT3pxdVB6T1czMmdmQ3JUYzNCQnh0TkxDRHB5S1BkdzhzQ1UvTllLbERKbGozTEJ5aGFtVk8vdFNYcksxUk5TYmRubzR2UDE1dm1KZEcyMnNnY2RsTStVa3N6R01MYlBZalAxZUdTcS9kSXVCNFF4YjBiNzAza1MyODdMMjJCWHA5QXNubi9FL1BKTUhpeXZjNUNQZXFpUFM0cUJuODBjcU9CUEhISlR6ekVuTkd5U3l1TURUbHRnVUpmdTM5OUhHMTc4bTZNOGxCZjRRWlk5d1lGZk0wUURjN2FERjJWUXZJY2hPcVh6cENyd1cwRmx1b0NjemRsWG9tdkZBeVh5aXpZV1lsZzZjOEhJSVBNeU54RkkyMmVDSjU5djlaSFJHdTh5R1kvZUh1TUtoWFVMTGJydEpTVGlqSmRiZHozbSs2Njlycm1VU1U5QldVS3hSRyt1WFM2U3NUTVBXczAwZVdSSytlaW5naXpHRm5sdlcvdmx4WGlpTDlKTUcyZzdGekNOUk8vRnQxWmNDbzJpQ2w2RW1sTTZXYkxGQ1BRN0cyRWlqSXp6cW56R3RPbEZEL0dYd09qSHV6QzBTVnpObkE5TktnQ3JVMjNyOGVJOUdkbmc0RVEvaUNScC9kYUpMOFV0Y2RycUZuYXUrN1lGZitobk4rSXR3KzViQ0hadjI2SUw2NktXelFtTUZ3Y2tPWDRmbVZpTEJqVU5MN3YyQmhrTmpWcHZOOThlREljSStoSG1teENrTTBiNlA5WG5NWWVHSVQrQ2Q4MldDTG1qTkxxdy8xOE1GVUpuVT0=',
            'SUCCESS'  => 'ODU0UnRNV1g1a242WWhYTlNpaEtkd0ljbCszRXQxVWVHOTlUSnJvc3VuSW9tUTQ4ci9OcTV0OGtURU5ta1lTbm1NeWpoeTJzUXgwZ0Z1aUplbnkvS1IrS2k1MDl5RDJ0ZFg3T1R5QmMzelVvZ3dPVVFFdWlCc0UzS3M3L1BocmI2bWdBUnJuZTNkeCsvb1RhOHNmV1Z5RDQzRngzTXg5VFVFaURkeUhrelpHSTZldGdwL0RCdmFBUUtIY21telBKNlZnYkwyYXF6Zkl0Vmd0dThtbWQvQzFSQ0JFakd4dlRnMDBnTUVGalBqbU0vQlptNmhvWk1hOHozdFlybFFHOGJ1ZSt1MC9vRjNiRzd6ZW5KckpzQnVYV1M0V1NRRXRhQUNaTWcxLzB3UzBuSzRoQXVXcTBQU2M0MDloM0J0dzloWTNQWHEyNTlLVU1aTnlWeUJVSlRJaU03QXNYZFFkNWVvRU9lUGhXaUZVRVRxQXVLdGhYdVpWaHdySWQwamI2ZmQxQWVWOVBTS1FoZXlUTGxrR3FUR1lVeFRHZUFiNmlWTDkrTHBUbmRNbFRHb21KeVptMVRQWWJQVkR2SGozWDdsakJyanFnSzkyTHlTYWQzRHZqUHVSbE1PNlB3aTlGUVZVMktaU2h5T3Z6RC9FUGdhWmJYN3pGWUNpTTdpUG5OUlE5ZEM3cUhVb01oODlZTnk4Q0paVFg1QTRxeXIzbnd0ZjRoQVJaV3ZPVW1TblBZUDNlL01SL3RRaS94dmVDTDVOTWliTDcrQ2hmZWFWMzRSNkJjcGRXRElMcGY1SkFPWWd1eUR6SWR1RWJIS2VDa3ZPQTNCUkdXMWRJMG5UVTJpQ1lJbWIxaFd3eHhtU0pabEM0TUMzbjVUeHFReldiVHhRMmVOYlJ3Rk9zUklmKzFXL3ZLbndjWHBMUzV3WHl4SHcrM3ZSdS8vaGlZNDJza3FvNTR2dEhRNklKcm5Md28yVUtIYXJDbXUyaythMjUreWtUenhlMlJqc3U0S3lVTUMwdFNqa3hlZERhTytwNUJlYW1VenVKeGJhaldKTlJJVmJqMTBwLzJBSU1wL0gzRi9RSDA1akpxN1ZtbkhUZWNmNTYrZXk5RUpyeHRYNFZ3ZDhOQlVxU3dIcmROMHVYcHhPbXI5RDl1dUk9',
        ];

        $cipher = new CoinbarCypher(new CoinbarPaymentGatewayEnvConfig());
        foreach ($responses as $status => $response) {
            $decoded = $cipher->decode($response);
            $arr = json_decode($decoded, true);
            echo "\n" . $decoded . "\n";
            self::assertArrayHasKey('status', $arr);
            self::assertArrayHasKey('payment_detail', $arr);
//            self::assertArrayHasKey('service_client_id', $arr);
            self::assertEquals($status, $arr['status']);
            self::assertIsFloat($arr['payment_detail']['total_price']);
            if (isset($arr['service_client_id'])) {
                self::assertEquals($arr['service_client_id'],
                    (new CoinbarPaymentGatewayEnvConfig())
                        ->get(CoinbarPaymentGatewayConfig::CBPAY_SERVICE_CLIENT_ID));
            }
        }
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
        $cfg = new CoinbarPaymentGatewayEnvConfig();
        return $cfg->createGateway();
    }
}
