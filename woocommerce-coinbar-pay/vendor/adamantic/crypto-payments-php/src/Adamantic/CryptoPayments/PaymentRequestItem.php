<?php
/*
 * Copyright (c) 2022 ADAMANTIC S.r.l (https://www.adamantic.io).
 *
 * This software is produced by ADAMANTIC and provided under the MIT License.
 * If you have not received a copy of the said license, please refer to
 * https://opensource.org/licenses/MIT
 */

namespace Adamantic\CryptoPayments;

use Brick\Math\BigDecimal;

/**
 * Represents a single item in a payment request
 */
interface PaymentRequestItem {

    /**
     * @return string type of item (e.g. 'service')
     */
    function getType(): string;

    /**
     * @return string id of the item (often the SKU of the merchant)
     */
    function getId(): string;

    /**
     * @return int the number of items of this product to purchase
     */
    function getUnits(): int;

    /**
     * @return string a string description of the current payment item
     */
    function getDescription(): string;

    /**
     * @return BigDecimal the amount requested for this item. It is expected for
     *                    the payment gateway to treat it with the request currency's
     *                    specified precision.
     */
    function getAmount(): BigDecimal;

}