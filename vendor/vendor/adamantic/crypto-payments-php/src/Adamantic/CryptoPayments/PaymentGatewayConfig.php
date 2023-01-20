<?php declare(strict_types=1);
/*
 * Copyright (c) 2022 ADAMANTIC S.r.l (https://www.adamantic.io).
 *
 * This software is produced by ADAMANTIC and provided under the MIT License.
 * If you have not received a copy of the said license, please refer to
 * https://opensource.org/licenses/MIT
 */

namespace Adamantic\CryptoPayments;

/**
 * Instantiates a payment gateway configuration from an
 * implementation-dependent source (e.g. env properties,
 * or JSON configuration file).
 */
abstract class PaymentGatewayConfig {

    abstract function createGateway(): PaymentGateway;
    abstract function get($key): string;

}