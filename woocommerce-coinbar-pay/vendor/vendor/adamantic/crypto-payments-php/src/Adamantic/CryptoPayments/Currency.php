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
 * A class to represent a currency and its relative scale in payment systems.
 */
class Currency {

    private string $symbol;
    private int    $precision;

    /**
     * @param string $symbol
     * @param int $precision
     */
    public function __construct(string $symbol, int $precision)
    {
        $this->symbol = $symbol;
        $this->precision = $precision;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

}