<?php
/*
 * Copyright (c) Coinbar Spa 2023.
 *
 * This file is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Nome-Programma is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Nome-Programma.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace CoinbarPay\Sdk;

use Adamantic\CryptoPayments\PaymentGatewayBase;
use Adamantic\CryptoPayments\PaymentRequest;
use Adamantic\CryptoPayments\PaymentStatus;
use Adamantic\CryptoPayments\PaymentStatusUpdate;
use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig as C;

/**
 * Implementation of the CoinbarPay payment gateway.
 */
class CoinbarPaymentGateway extends PaymentGatewayBase {

    private CoinbarPaymentGatewayConfig $config;

    public function __construct(CoinbarPaymentGatewayConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function requestPayment(PaymentRequest $request): PaymentStatusUpdate
    {
        $tok = PaymentRequestToken::from($request, $this->config);
        $c = $this->config;
        return (new PaymentStatusUpdate())
            ->setRequestId($request->getUuid())
            ->setStatus(PaymentStatus::REQUESTED)
            ->setFrontendRedirectUrl(
                $c->get(C::CBPAY_GW_URL)
                . "/paymentgateway/pay?requestToken=" . $tok->encode()
                . "&serviceClientId=" . $c->get(C::CBPAY_SERVICE_CLIENT_ID)
                . "&timestamp=" . $request->getTimestampMs()
            );

    }

}
