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

class PaymentRequestSimpleItem implements PaymentRequestItem
{
    private string     $type;
    private string     $id;
    private string     $description;
    private int        $units;
    private BigDecimal $amount;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return PaymentRequestSimpleItem
     */
    public function setType(string $type): PaymentRequestSimpleItem
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return PaymentRequestSimpleItem
     */
    public function setId(string $id): PaymentRequestSimpleItem
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return PaymentRequestSimpleItem
     */
    public function setDescription(string $description): PaymentRequestSimpleItem
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnits(): int
    {
        return $this->units;
    }

    /**
     * @param int $units
     * @return PaymentRequestSimpleItem
     */
    public function setUnits(int $units): PaymentRequestSimpleItem
    {
        $this->units = $units;
        return $this;
    }

    /**
     * @return BigDecimal
     */
    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    /**
     * @param BigDecimal $amount
     * @return PaymentRequestSimpleItem
     */
    public function setAmount(BigDecimal $amount): PaymentRequestSimpleItem
    {
        $this->amount = $amount;
        return $this;
    }



}