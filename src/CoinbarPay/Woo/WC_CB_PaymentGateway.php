<?php
/*
 * Copyright (c) Coinbar Spa 2023.
 * This file is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the software.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace CoinbarPay\Woo;

use Adamantic\CryptoPayments\Currency;
use Adamantic\CryptoPayments\PaymentGatewayConfig;
use Adamantic\CryptoPayments\PaymentRequest;
use Adamantic\CryptoPayments\PaymentRequestSimpleItem;
use Adamantic\CryptoPayments\PaymentStatus;
use Brick\Math\BigDecimal;
use CoinbarPay\Sdk\CoinbarCypher;
use CoinbarPay\Sdk\CoinbarPaymentGateway;
use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig;
use CoinbarPay\Sdk\CoinbarPaymentStatusMapper;
use Exception;
use WC_Order;
use WC_Payment_Gateway;

class WC_CB_PaymentGateway extends WC_Payment_Gateway
{

    private string $order_status;
	private string $hide_text_box;
	private string $text_box_required;

    private WC_CB_PaymentGatewayConfig $gatewayConfig;
	private CoinbarPaymentGateway      $cbGateway;

	public function __construct()
    {
        $this->id = 'coinbarpay';
        $this->method_title = __('Coinbar Pay');
        $this->title = __('Coinbar Pay', 'coinbarpay-payment-gateway');
        $this->has_fields = true;
	    $this->gatewayConfig = new WC_CB_PaymentGatewayConfig($this);
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->hide_text_box = $this->get_option('hide_text_box');
        $this->text_box_required = $this->get_option('text_box_required');
        $this->order_status = $this->get_option('order_status');
        if ($this->enabled && $this->gatewayConfig->validateFields()) {
            $this->cbGateway = $this->gatewayConfig->createGateway();
        }
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action( 'woocommerce_api_cbpay-webhook', array( $this, 'webhook' ) );
        add_action( 'woocommerce_api_cbpay-webhook-dbg', array( $this, 'wh_debug_payload' ) );
	    add_action( 'woocommerce_api_cbpay-fe-callback', array( $this, 'fe_callback' ) );
    }

    public function init_form_fields()
    {
        $this->form_fields = array_merge(
                $this->gatewayConfig->getFormFieldDescriptors(),
                array(
                    'title' => array(
                        'title'                 => __('Method Title', 'coinbarpay-payment-gateway'),
                        'type'                  => 'text',
                        'description'   => __('This controls the title', 'coinbarpay-payment-gateway'),
                        'default'               => __('Coinbar Pay', 'coinbarpay-payment-gateway'),
                        'desc_tip'              => true,
                    ),
                    'description' => array(
                        'title' => __('Customer Message', 'coinbarpay-payment-gateway'),
                        'type' => 'textarea',
                        'css' => 'width:500px;',
                        'default' => 'Pay safely with crypto via our partner Coinbar Pay',
                        'description'   => __('The message you want to show to the customer in the checkout page.', 'coinbarpay-payment-gateway'),
                    ),
                    'text_box_required' => array(
                        'title'                 => __('Make the Text Box required', 'coinbarpay-payment-gateway'),
                        'type'                  => 'checkbox',
                        'label'                 => __('Make the text field required', 'coinbarpay-payment-gateway'),
                        'default'               => 'no'
                    ),
                    'hide_text_box' => array(
                        'title'                 => __('Hide The Text Box', 'coinbarpay-payment-gateway'),
                        'type'                  => 'checkbox',
                        'label'                 => __('Hide', 'coinbarpay-payment-gateway'),
                        'default'               => 'no',
                        'description'   => __('If you do not need to show the text box for customers at all, enable this option.', 'coinbarpay-payment-gateway'),
                    ),
                    'order_status' => array(
                        'title' => __('Order Status After The Checkout', 'coinbarpay-payment-gateway'),
                        'type' => 'select',
                        'options' => wc_get_order_statuses(),
                        'default' => 'wc-on-hold',
                        'description'   => __('The default order status after checkout if this gateway is used.', 'coinbarpay-payment-gateway'),
                    ),
                    'result_page_logo' => array(
                        'title'       => __('Result Page Logo', 'coinbarpay-payment-gateway'),
                        'type'        => 'text',
                        'description' => __('The Logo to display on the result page after the payment'),
                        'default'     => 'https://coinbar.io/static/media/logo-coinbar-hor.f21a28f5c91c26c050be7456c3fc6cb2.svg',
                        'desc_tip'    => true,
                    ),
                )
        );
    }
    /**
     * Admin Panel Options
     * - Options for bits like 'title' and availability on a country-by-country basis
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_options()
    {
?>
        <h3><?php _e('Coinbar Pay Settings', 'coinbarpay-payment-gateway'); ?></h3>
        <div id="poststuff">

        <?php @include(__DIR__ . '/../../../config/admin_info_box.php'); ?>

        <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <table class="form-table">
                        <?php $this->generate_settings_html(); ?>
                    </table>
                    <!--/.form-table-->
                </div>
            </div>
        </div>


        <div class="clear"></div>
    <?php
    }

    public function validate_fields()
    {
        $gwCfgValidation = $this->gatewayConfig->validateFields();
        foreach ($gwCfgValidation->getErrors() as $error) {
            wc_add_notice($error, 'error');
        }
        foreach ($gwCfgValidation->getWarnings() as $warning) {
            wc_add_notice($warning, 'notice');
        }
        if ($gwCfgValidation->isError()) {
            return false;
        }

        if ($this->text_box_required === 'no') {
            return true;
        }

        $textbox_value = (isset($_POST['other_payment-admin-note']))
            ? sanitize_text_field(trim($_POST['other_payment-admin-note']))
            : '';
        if ($textbox_value === '') {
            wc_add_notice(__('Please, complete the payment information.', 'coinbarpay-payment-gateway'), 'error');
            return false;
        }
        return true;
    }

    public function process_payment($order_id)
    {
        global $woocommerce;
        $order = new WC_Order($order_id);
        // Mark as on-hold (we're awaiting the payment completion)
        $order->update_status($this->order_status, __('Awaiting payment', 'coinbarpay-payment-gateway'));
        // // Reduce stock levels
        // wc_reduce_stock_levels($order_id);
        // if (isset($_POST[$this->id . '-admin-note']) && trim($_POST[$this->id . '-admin-note']) != '') {
        //     $order->add_order_note(esc_html($_POST[$this->id . '-admin-note']), 1);
        // }
        // // Remove cart
        // $woocommerce->cart->empty_cart();
        // // Return thankyou redirect

        $req = $this->createPaymentRequest($order);
//        wc_add_notice('Your payment has been completed, thank you');
        $rqu = $this->cbGateway->requestPayment($req);
        return array(
            'result' => 'success',
//            'redirect' => get_permalink( wc_get_page_id( 'shop' ) )
            'redirect' => $rqu->getFrontendRedirectUrl()
        );
    }

    public function payment_fields()
    {
    ?>
        <fieldset>
            <p class="form-row form-row-wide">
                <label for="<?php echo esc_attr($this->id); ?>-admin-note"><?php echo esc_html($this->description); ?> <?php if ($this->text_box_required === 'yes') : ?> <span class="required">*</span> <?php endif; ?></label>
                <?php if ($this->hide_text_box !== 'yes') { ?>
                    <textarea id="<?php echo esc_attr($this->id); ?>-admin-note" class="input-text" type="text" name="<?php echo esc_attr($this->id); ?>-admin-note"></textarea>
                <?php } ?>
            </p>
            <div class="clear"></div>
        </fieldset>
<?php
    }

    protected function createPaymentRequest(WC_Order $order): PaymentRequest {
        $preq = PaymentRequest::createNew();
        return $preq
            ->setUuid($order->get_order_number())
            ->setUserId($order->get_user_id())
            ->setUserEmail($order->get_billing_email())
            ->setCurrency(new Currency('EUR', 2))
            ->addItem((new PaymentRequestSimpleItem())
                ->setId($order->get_order_number())
                ->setUnits(1)
                ->setType('ecommerce')
                ->setDescription('Order #' . $order->get_order_number())
                ->setAmount(BigDecimal::of($order->get_total())));
    }


    public function webhook() {
        $scId = sanitize_text_field($_SERVER['HTTP_SERVICE_CLIENT_ID']);
        if (empty($scId) || $scId != $this->gatewayConfig->get(CoinbarPaymentGatewayConfig::CBPAY_SERVICE_CLIENT_ID)) {
            dieWithStatusAndPayload(400, 'Service Client ID does not match the configured value.', 'KO');
        }
        $entityBody = file_get_contents('php://input');
        if (!$entityBody) {
            dieWithStatusAndPayload(400, 'Empty body', 'KO');
        }
        $data = json_decode($entityBody);

        
        $cipher = new CoinbarCypher($this->gatewayConfig);
        $cbResponse = json_decode($cipher->decode($data->responseToken));

        try {
            $orderId = $cbResponse->payment_request_id_client;
            $cbStatus = CoinbarPaymentStatusMapper::coinbarToSdk($cbResponse->status);

            $order = wc_get_order($orderId);
            if (!$order) {
                dieWithStatusAndPayload(404, 'Order not found by ID: ' . $orderId, 'KO');
            }

            switch ($cbStatus) {

                case PaymentStatus::AUTHORIZED:
                    // we get this while the gateway is processing the payment confirmation,
                    // so nothing to do here at this stage
                    break;
                case PaymentStatus::REFUSED:
		            // The payment failed, but the user may be willing to retry so we just let it awaiting payment.
                    // TODO: add choice in plug-in configuration for cancel vs do nothing on REFUSED
                    break;
                case PaymentStatus::COMPLETED:
                    // the payment gateway confirmed the payment.
                    $order->payment_complete($cbResponse->payment_id_coinbar);
                    break;
                case PaymentStatus::REVOKED:
                    $order->update_status('cancelled', 'Vendor revoked the payment request');
                    break;
                default:
                    dieWithStatusAndPayload(400, 'Event type not handled: ' . $cbStatus, 'KO');
            }
            
            dieWithStatusAndPayload(200, 'Event captured');
        } catch (Exception $exc) {
            dieWithStatusAndPayload(500, $exc);
        }

    }

    function wh_debug_payload() {
        $scId = sanitize_text_field($_SERVER['HTTP_SERVICE_CLIENT_ID']);
        if (empty($scId) || $scId != $this->gatewayConfig->get(CoinbarPaymentGatewayConfig::CBPAY_SERVICE_CLIENT_ID)) {
            dieWithStatusAndPayload(400, 'Service Client ID does not match the configured value.', 'KO');
        }
        $entityBody = file_get_contents('php://input');
        if (!$entityBody) {
            dieWithStatusAndPayload(400, 'Empty body', 'KO');
        }
        $data = json_decode($entityBody);

        
        $cipher = new CoinbarCypher($this->gatewayConfig);
        $cbResponse = $cipher->decode($data->responseToken);
        header('Content-Type: application/json; charset=utf-8');
        die($cbResponse);
    }

    function fe_callback() {
        $payId  = sanitize_text_field($_GET['payment_id']);
        $status = sanitize_text_field($_GET['status']);
        try {
            $status = CoinbarPaymentStatusMapper::coinbarToSdk($status);
        } catch (\Throwable $t) {
            // nothing, we'll just acknowledge the failure
	    }
        ?>
            <html>
            <head>
                <style>
                    .center-screen {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        text-align: center;
                        min-height: 100vh;
                    }
                    .cb-logo {
                        width: 200px;
                    }
                </style>
            </head>
            <body>
            <div class="center-screen">

                <div>
                    <div>
                        <img src="<?php echo($this->get_option('result_page_logo')) ?>" class="cb-logo"/>
                    </div>

                    <?php if ($status === PaymentStatus::COMPLETED) { ?>
                        <h3><?php echo(__('Payment complete!', 'coinbarpay-payment-gateway')) ?></h3>
	                    <?php echo( __('Your payment is recorded, thank you!', 'coinbarpay-payment-gateway')) ?>
                    <?php } else { ?>
                        <h3><?php echo(__('Payment problem', 'coinbarpay-payment-gateway')) ?></h3>
	                    <?php echo( __('Something went wrong while processing your payment - status:', 'coinbarpay-payment-gateway')) ?>
                        <?php echo( esc_html($status) ?? __('UNKNOWN', 'coinbarpay-payment-gateway') ) ?>
                    <?php }

                    if ($payId) { ?>
                        <div><?php echo(__('Your Payment ID:', 'coinbarpay-payment-gateway')) ?>
                            <?php echo(esc_html($payId)) ?></div>
                    <?php }
                    ?>
                    <div>
                        <a href="<?php echo( get_permalink( wc_get_page_id( 'shop' ) ) ) ?>">
                            <?php echo( __('Return to the shop.', 'coinbarpay-payment-gateway')) ?>
                        </a>
                    </div>
                </div>
            </div>
            </body>
            </html>

        <?php
        dieWithHtml('');
    }
}

/**
 * Sends a response to the caller and dies immediately after, halting the execution of the script
 * @param int               $status  the HTTP status to return
 * @param string|\Exception $payload the payload to send back
 * @param string            $code    pass 'OK' or 'KO' - if $payload is an exception, 'KO' is forced
 */
function dieWithStatusAndPayload(int $status, mixed $payload, string $code = 'OK') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($status);
    if ($payload instanceof Exception) {
        $code = 'KO';
        $trace = $payload->getTraceAsString();
        $message = $payload->getMessage();
    } else {
        $trace = null;
        $message = $payload;
    }
    die(json_encode(array(
        'code'    => $code,
        'status'  => $status,
        'message' => $message,
        'trace'   => $trace
    )));
}

function dieWithHtml($html) {
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(200);
    die($html);
}
