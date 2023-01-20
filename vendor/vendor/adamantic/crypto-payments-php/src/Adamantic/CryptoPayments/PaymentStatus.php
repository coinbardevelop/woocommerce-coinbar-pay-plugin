<?php
/*
 * Copyright (c) 2022 ADAMANTIC S.r.l (https://www.adamantic.io).
 *
 * This software is produced by ADAMANTIC and provided under the MIT License.
 * If you have not received a copy of the said license, please refer to
 * https://opensource.org/licenses/MIT
 */

namespace Adamantic\CryptoPayments;

/**
 * All possible status for a payment
 * (not using an enum since many e-commerce sites are still on PHP 7.x)
 * TODO: migrate to PHP 8.1+ enums
 */
abstract class PaymentStatus {
    const REQUESTED  = 'REQUESTED';
    const AUTHORIZED = 'AUTHORIZED';
    const REFUSED    = 'REFUSED';
    const COMPLETED  = 'COMPLETED';
    const REFUNDED   = 'REFUNDED';
    const REVOKED    = 'REVOKED';
};

