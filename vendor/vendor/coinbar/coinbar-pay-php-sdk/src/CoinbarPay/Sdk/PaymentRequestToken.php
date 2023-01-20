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

use Adamantic\CryptoPayments\PaymentRequest;
use Brick\Math\RoundingMode;
use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig as Config;

class PaymentRequestToken {
    
    private PaymentRequest $request;
    private Config $config;


    public function encode(): string {
        return (new CoinbarCypher($this->config))->encode($this->createJson());
    }


    public function createJson(): string {
        $c = $this->config;
        return json_encode([
            'service_client_id'  => $c->get(Config::CBPAY_SERVICE_CLIENT_ID),
            'public_key'         => $c->get(Config::CBPAY_TOKEN_API_KEY),
            'payment_request_id' => $this->request->getUuid(),
            // todo: populate name, surname
            'user_id'            => $this->request->getUserId(),
            'surname'            => '-',
            'name'               => '-',
            'email'              => $this->request->getUserEmail(),
            'products'           => $this->requestItemsAsArray(),
            'timestamp'          => floor(microtime(true) * 1000),
            'input_coin'         => $this->request->getCurrency()->getSymbol(),
            'urlcallback'        => $c->get(Config::CBPAY_FRONTEND_CALLBACK_URL)
        ]);
    }

    public static function from(
        PaymentRequest $request,
        Config $cfg): PaymentRequestToken
    {
        return new PaymentRequestToken($request, $cfg);
    }

    protected function __construct(
        PaymentRequest $request, ?Config $cfg)
    {
        $this->request = $request;
        $this->config = $cfg;
    }

    private function requestItemsAsArray(): array {
        $items = [];
        $curScale = $this->request->getCurrency()->getPrecision();
        foreach ($this->request->getItems() as $it) {
            $items[] = [
                'product_name'   => $it->getDescription(),
                'product_price'  => $it->getAmount()->toScale($curScale, RoundingMode::HALF_EVEN),
                'product_amount' => $it->getUnits(),
                'product_id'     => $it->getId(),
                'product_type'   => $it->getType()
            ];
        }
        return $items;
    }

}
