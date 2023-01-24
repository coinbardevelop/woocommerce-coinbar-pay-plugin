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

use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig as Config;

/**
 * Utility class for encoding/decoding data according
 * to the CoinbarPay gateway specification
 */
class CoinbarCypher {

    const CYPHER_ALGO = 'AES-256-CBC';
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function encode(string $data): string {
        return base64_encode(
            openssl_encrypt(
                $data,
                self::CYPHER_ALGO,
                $this->config->get(Config::CBPAY_TOKEN_API_KEY),
                0,
                $this->config->get(Config::CBPAY_TOKEN_SECRET_KEY)
            )
        );
    }


    public function decode(string $data): string {
        return openssl_decrypt(
            base64_decode($data),
            self::CYPHER_ALGO,
            $this->config->get(Config::CBPAY_TOKEN_API_KEY),
            0,
            $this->config->get(Config::CBPAY_TOKEN_SECRET_KEY)
        );
    }

}
