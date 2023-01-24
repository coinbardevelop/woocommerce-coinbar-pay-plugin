<?php
/*
 * Copyright (c) 2022 ADAMANTIC S.r.l (https://www.adamantic.io).
 *
 * This software is produced by ADAMANTIC and provided under the MIT License.
 * If you have not received a copy of the said license, please refer to
 * https://opensource.org/licenses/MIT
 */

namespace Adamantic\CryptoPayments;

class PaymentRequest {


    /**
     * @var PaymentRequestItem[] items of the payment request
     */
    private array    $items = [];
    private Currency $currency;
    private string   $gwData;
    private string   $uuid;
    private string   $userId;
    private string   $userEmail;
    private int      $timestampMs;

    public static function createNew(): PaymentRequest {
        return new PaymentRequest(true);
    }

    public static function createBlank(): PaymentRequest {
        return new PaymentRequest(false);
    }

    /**
     * @return string the unique id of this request
     */
    public function getUuid(): string {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return PaymentRequest
     */
    public function setUuid(string $uuid): PaymentRequest {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     * @return PaymentRequest
     */
    public function setUserId(string $userId): PaymentRequest
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    /**
     * @param string $userEmail
     * @return PaymentRequest
     */
    public function setUserEmail(string $userEmail): PaymentRequest
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency): PaymentRequest
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(PaymentRequestItem $item): PaymentRequest {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items): PaymentRequest
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return string
     */
    public function getGwData(): string
    {
        return $this->gwData;
    }

    /**
     * @param string $gwData
     */
    public function setGwData(string $gwData): PaymentRequest
    {
        $this->gwData = $gwData;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestampMs(): int
    {
        return $this->timestampMs;
    }

    /**
     * @param int $timestampMs
     */
    public function setTimestampMs(int $timestampMs): void
    {
        $this->timestampMs = $timestampMs;
    }


    protected function __construct(bool $init=false) {
        if ($init) {
            $this->timestampMs = floor(microtime(true) * 1000);
            $this->uuid = uniqid();
        }
    }

}
