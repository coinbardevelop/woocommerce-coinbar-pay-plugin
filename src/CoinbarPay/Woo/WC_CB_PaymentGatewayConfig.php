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

use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig;
use \WC_Settings_API;


/**
 * Manages the Coinbar Pay configuration through the WordPress
 * settings API exposed from the WooCommerce settings forms.
 */
class WC_CB_PaymentGatewayConfig extends CoinbarPaymentGatewayConfig {

	private WC_Settings_API $settings;

	private array $config = [
		self::CBPAY_GW_URL => '',
		self::CBPAY_SERVICE_CLIENT_ID => '',
		self::CBPAY_BACKEND_CALLBACK_URL => '',
		self::CBPAY_FRONTEND_CALLBACK_URL => '',
		self::CBPAY_TOKEN_API_KEY => '',
		self::CBPAY_TOKEN_SECRET_KEY => ''
	];

	public function __construct(WC_Settings_API $settings) {
		$this->settings = $settings;
		$this->loadConfig();
	}


	public function getFormFieldDescriptors(): array {
		return array(
			self::CBPAY_GW_URL => array(
				'title'       => __('Coinbar Pay Gateway URL', 'woocommerce-coinbar-pay'),
				'type'        => 'text',
				'description' => __('The URL to the Coinbar Pay Gateway', 'woocommerce-coinbar-pay'),
				'default'     => $GLOBALS['CBPAY_GATEWAY_URL'],
				'desc_tip'    => true,
				'custom_attributes' => ($GLOBALS['CBPAY_GATEWAY_URL_READONLY'] ? array('readonly' => NULL) : NULL)
			),
			self::CBPAY_BACKEND_CALLBACK_URL => array(
				'title'       => __('Back-end Callback URL <br/><i>(LEAVE AS SUGGESTED)</i>', 'woocommerce-coinbar-pay'),
				'type'        => 'text',
				'description' => __('The URL that will be called by the gateway to update the payment status. Only modify this if you know exactly what you are doing.', 'woocommerce-coinbar-pay'),
				'default'     => get_site_url() . '/wc-api/cbpay-webhook',
				'desc_tip'    => true,
			),
			self::CBPAY_FRONTEND_CALLBACK_URL => array(
				'title'       => __('Front-end Redirect URL <br/><i>(LEAVE AS SUGGESTED)</i>', 'woocommerce-coinbar-pay'),
				'type'        => 'text',
				'description' => __('The front-end page onto which the gateway will redirect the user after completion.
				Requires Permalinks to be enabled on this Wordpress installation.  Only modify this if you know exactly what you are doing.', 'woocommerce-coinbar-pay'),
				'default'     => get_site_url() . '/wc-api/cbpay-fe-callback',
				'desc_tip'    => true,
			),
			self::CBPAY_SERVICE_CLIENT_ID => array(
				'title'       => __('Service Client ID', 'woocommerce-coinbar-pay'),
				'type'        => 'text',
				'description' => __('Your Coinbar Pay ID', 'woocommerce-coinbar-pay'),
				'default'     => '',
				'desc_tip'    => true,
			),
			self::CBPAY_TOKEN_API_KEY => array(
				'title'       => __('API Key', 'woocommerce-coinbar-pay'),
				'type'        => 'text',
				'description' => __('Your Coinbar Pay API Key', 'woocommerce-coinbar-pay'),
				'default'     => '',
				'desc_tip'    => true,
			),
			self::CBPAY_TOKEN_SECRET_KEY => array(
				'title'       => __('Secret Key', 'woocommerce-coinbar-pay'),
				'type'        => 'text',
				'description' => __('Your Coinbar Pay Secret Key', 'woocommerce-coinbar-pay'),
				'default'     => '',
				'desc_tip'    => true,
			),
		);
	}

	public function validateFields(): ValidationResult {
		$ret = new ValidationResult();
		foreach ($this->config as $key => $value) {
			if (! $value) {
				$ret->addError('Required configuration property not set: ' . $key);
			}
		}
		return $ret;
	}

	/**
	 * @inheritDoc
	 */
	public function get($key): string {
		return $this->config[$key];
	}

	/**
	 * @inheritDoc
	 */
	function loadConfig() {
		foreach ($this->config as $key => $value) {
			$this->config[$key] = $this->settings->get_option($key);
		}

		/*
		* Fetching the 'hardcoded' configuration first,
		* then applying reasonable defaults.
		*/
		@include(__DIR__ . '/../../../config/globals.php');
		if ( !isset($GLOBALS['CBPAY_GATEWAY_URL']) ) {
			$GLOBALS['CBPAY_GATEWAY_URL'] = 'https://pay.coinbar.io';
		}
		if ( !isset($GLOBALS['CBPAY_GATEWAY_URL_READONLY']) ) {
			$GLOBALS['CBPAY_GATEWAY_URL_READONLY'] = false;
		}

	}

}
