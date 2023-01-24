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

use Adamantic\CryptoPayments\Exceptions\UnknownStatusException;
use Adamantic\CryptoPayments\PaymentStatus;

/**
 * Performs mapping between the SDK statuses and the CoinbarPay ones
 * @see PaymentStatus for the SDK statuses
 */
abstract class CoinbarPaymentStatusMapper
{

    private static array $CB_TO_SDK = [
        "CREATED"  => PaymentStatus::REQUESTED,
        "PENDING"  => PaymentStatus::AUTHORIZED,
        "SUCCESS"  => PaymentStatus::COMPLETED,
        "FAILED"   => PaymentStatus::REFUSED,
        "CANCELED" => PaymentStatus::REVOKED,
    ];

    private static array $SDK_TO_CB = [
        PaymentStatus::REQUESTED  => "CREATED",
        PaymentStatus::AUTHORIZED => "PENDING",
        PaymentStatus::COMPLETED  => "SUCCESS",
        PaymentStatus::REFUSED    => "FAILED",
        PaymentStatus::REVOKED    => "CANCELED"
    ];


    /**
     * Returns the SDK status corresponding to the CoinbarPay status
     * @throws UnknownStatusException if the provided status is not known to the system
     */
    public static function coinbarToSdk(string $cbStatus): string {
        $cbStatus = strtoupper($cbStatus);
        if (!isset(self::$CB_TO_SDK[$cbStatus])) {
            throw new UnknownStatusException($cbStatus);
        }
        return self::$CB_TO_SDK[$cbStatus];
    }

    /**
     * Returns the CoinbarPay status corresponding to the SDK status
     * @throws UnknownStatusException if the provided status is not known to the system
     */
    public static function sdkToCoinbar(string $sdkStatus): string {
        $sdkStatus = strtoupper($sdkStatus);
        if (!isset(self::$SDK_TO_CB[$sdkStatus])) {
            throw new UnknownStatusException($sdkStatus);
        }
        return self::$SDK_TO_CB[$sdkStatus];
    }

}
