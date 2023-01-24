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
 * Interface to implement for all payment gateway adapters
 */
interface PaymentGateway {

    /**
     * Creates a new payment request on the external payment service.
     * @param PaymentRequest $request the request to create
     * @return PaymentStatusUpdate the result of the payment request creation
     * @throws \Exception if anything wrong occurs during the creation of the payment
     */
    public function requestPayment(PaymentRequest $request): PaymentStatusUpdate;

    /**
     * Register a new listener for payment status updates
     * @param PaymentStatusListener $listener the listener to register
     * @return void
     */
    public function addPaymentStatusListener(PaymentStatusListener $listener): void;

    /**
     * Method designed for channel listeners to notify the gateway about an incoming
     * payment status update. The gateway is expected to perform bookkeeping and to
     * notify all registered listeners.
     * @return void
     */
    public function onIncomingPaymentStatusUpdate(PaymentStatusUpdate $update): void;
}