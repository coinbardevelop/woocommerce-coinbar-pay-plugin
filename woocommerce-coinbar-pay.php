<?php
/*
 * Plugin Name: CoinbarPay Payment Gateway
 * Plugin URI: https://github.com/adamantic-io/coinbar-pay-woocommerce-plg
 * Description: Pay with Cryptocurrencies using the CoinbarPay gateway
 * Author: Coinbar s.p.a., Adamantic Team
 * Author URI: https://www.coinbar.io
 * Requires PHP: 7.4
 * Version: 1.0.0
 */


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


register_activation_hook(__FILE__, 'plugin_hook_activate');
function plugin_hook_activate() {
    if (!class_exists('WooCommerce')) {
        die(__('Required plugin not found: WooCommerce'));
    }
}


add_filter('woocommerce_payment_gateways', 'add_cb_payment_gateway');
function add_cb_payment_gateway( $gateways ){
        $gateways[] = '\CoinbarPay\Woo\WC_CB_PaymentGateway';
        return $gateways;
}

add_action('plugins_loaded', 'init_cb_payment_gateway');
function init_cb_payment_gateway(){
        require __DIR__ . '/vendor/autoload.php';
}
