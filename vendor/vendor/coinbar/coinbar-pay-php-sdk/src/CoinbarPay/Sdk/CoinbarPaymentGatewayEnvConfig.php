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

use Adamantic\CryptoPayments\Exceptions\ConfigurationException;

/**
 * Creates a payment gateway configuration for CoinbarPay
 * based on the following environment properties:
 * - `CBPAY_GW_URL`: The URL to reach the gateway
 * - `CBPAY_SERVICE_CLIENT_ID`: the service client ID 
 *                             (identifying the Coinbar client)
 * - `CBPAY_BACKEND_CALLBACK_URL`: the URL that the client will have
 *                                 to call back for payment events
 * - `CBPAY_TOKEN_API_KEY`: the client's API KEY
 * - `CBPAY_TOKEN_SECRET_KEY`: the client's SECRET KEY
 */
class CoinbarPaymentGatewayEnvConfig extends CoinbarPaymentGatewayConfig {

    private array $config = [
        self::CBPAY_GW_URL => '',
        self::CBPAY_SERVICE_CLIENT_ID => '',
        self::CBPAY_BACKEND_CALLBACK_URL => '',
        self::CBPAY_FRONTEND_CALLBACK_URL => '',
        self::CBPAY_TOKEN_API_KEY => '',
        self::CBPAY_TOKEN_SECRET_KEY => ''
    ];

    public function get($key): string {
        return $this->config[$key];
    }

    /**
     * @throws ConfigurationException if the required configuration is not found in the environment
     */
    public function loadConfig() {

        foreach( $this->config as $k => $v ) {
            $this->config[$k] = $this->requireEnv($k, true);
        }
    }

    /**
     * @throws ConfigurationException if the required configuration is not found in the environment
     */
    public function __construct() {
        $this->loadConfig();
    }

    private function requireEnv(string $key, bool $nonEmpty=false) : string {
        if (!isset($_ENV[$key])) {
            throw new ConfigurationException("Required environment variable '$key' is not set.");
        }
        $val = $_ENV[$key];
        if ($nonEmpty && empty($val)) {
            throw new ConfigurationException("Required environment variable '$key' has no value.");
        }
        return $val;
    }
 
}
