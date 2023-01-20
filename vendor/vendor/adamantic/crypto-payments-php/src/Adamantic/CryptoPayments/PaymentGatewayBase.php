<?php
/*
 * Copyright (c) 2022 ADAMANTIC S.r.l (https://www.adamantic.io).
 *
 * This software is produced by ADAMANTIC and provided under the MIT License.
 * If you have not received a copy of the said license, please refer to
 * https://opensource.org/licenses/MIT
 */

namespace Adamantic\CryptoPayments;

abstract class PaymentGatewayBase implements PaymentGateway
{
    /**
     * @var PaymentStatusListener[]
     */
    private static array $paymentStatusListeners = array();

    /**
     * @inheritDoc
     */
    public function addPaymentStatusListener(PaymentStatusListener $listener): void
    {
        if (in_array($listener, self::$paymentStatusListeners, true)) {
            return;
        }
        self::$paymentStatusListeners[] = $listener;
    }

    /**
     * @inheritDoc
     */
    public function onIncomingPaymentStatusUpdate(PaymentStatusUpdate $update): void
    {
        $this->notifyPaymentStatusUpdate($update);
    }


    protected function notifyPaymentStatusUpdate(PaymentStatusUpdate $update) {
        foreach (self::$paymentStatusListeners as $listener) {
            $listener->onPaymentStatusUpdate($update);
        }
    }
}