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

use Adamantic\CryptoPayments\PaymentGateway;
use Adamantic\CryptoPayments\PaymentGatewayConfig;

/**
 * Abstract class delegating configuration to subclasses and templatizing construction
 * of a Coinbar Payment gateway
 */
abstract class CoinbarPaymentGatewayConfig extends PaymentGatewayConfig {

    const CBPAY_GW_URL = 'CBPAY_GW_URL';
    const CBPAY_SERVICE_CLIENT_ID = 'CBPAY_SERVICE_CLIENT_ID';
    const CBPAY_BACKEND_CALLBACK_URL = 'CBPAY_BACKEND_CALLBACK_URL';
    const CBPAY_FRONTEND_CALLBACK_URL = 'CBPAY_FRONTEND_CALLBACK_URL';
    const CBPAY_TOKEN_API_KEY = 'CBPAY_TOKEN_API_KEY';
    const CBPAY_TOKEN_SECRET_KEY = 'CBPAY_TOKEN_SECRET_KEY';

    public function createGateway(): PaymentGateway {
        $this->loadConfig();
        return new CoinbarPaymentGateway($this);
    }

    abstract function loadConfig();

}
