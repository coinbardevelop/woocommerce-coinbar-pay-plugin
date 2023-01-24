<?php
/*
 * Copyright (c) 2022 ADAMANTIC S.r.l (https://www.adamantic.io).
 *
 * This software is produced by ADAMANTIC and provided under the MIT License.
 * If you have not received a copy of the said license, please refer to
 * https://opensource.org/licenses/MIT
 */

namespace Adamantic\CryptoPayments;

class PaymentStatusUpdate {

    private string $frontendRedirectUrl;
    private string $requestId;
    private string $status;
    private string $gwData;

    /**
     * @return string
     */
    public function getFrontendRedirectUrl(): string
    {
        return $this->frontendRedirectUrl;
    }

    /**
     * @param string $frontendRedirectUrl
     * @return PaymentStatusUpdate
     */
    public function setFrontendRedirectUrl(string $frontendRedirectUrl): PaymentStatusUpdate
    {
        $this->frontendRedirectUrl = $frontendRedirectUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     * @return PaymentStatusUpdate
     */
    public function setRequestId(string $requestId): PaymentStatusUpdate
    {
        $this->requestId = $requestId;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return PaymentStatusUpdate
     */
    public function setStatus(string $status): PaymentStatusUpdate
    {
        $this->status = $status;
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
     * @return PaymentStatusUpdate
     */
    public function setGwData(string $gwData): PaymentStatusUpdate
    {
        $this->gwData = $gwData;
        return $this;
    }

    
}
