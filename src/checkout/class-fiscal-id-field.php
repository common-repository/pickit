<?php
/**
 * Fiscal ID Field Class
 *
 * @package  Ecomerciar\Pickit\CheckOut
 */

namespace Ecomerciar\Pickit\CheckOut;

use Ecomerciar\Pickit\Helper\Helper;
use Ecomerciar\Pickit\ShippingMethod\WC_Pickit;

defined( 'ABSPATH' ) || exit();

/**
 * Checkout Class to mantain new custom fields for checkout.
 */
class FiscalIdField {

	/**
	 * Show Point Select Field
	 *
	 * @param array $fields Checkout Fields.
	 */
	public static function override_checkout_fields( $fields ) {
		if("ADD"===Helper::get_option('fiscal-id-mode' )){
            $fields['billing']['billing_pickit_fiscal_id'] = array(
                'label' => Helper::get_option('fiscal-id-label') ,
                'placeholder' => Helper::get_option('fiscal-id-label') ,
                'required' => true,
                'class' => array(
                    'form-row-wide'
                ) ,
                'clear' => true,
                'priority' => 22
            );
    
            $fields['shipping']['shipping_pickit_fiscal_id'] = array(
                'label' => Helper::get_option('fiscal-id-label') ,
                'placeholder' => Helper::get_option('fiscal-id-label') ,
                'required' => true,
                'class' => array(
                    'form-row-wide'
                ) ,
                'clear' => true,
                'priority' => 22
            );
        }
		return $fields;
	}

	/**
	 * Save checkout fields
	 *
	 * @param string $order_id Order ID.
	 * @param bool   $post_data Posted data.
	 */
	public static function checkout_update_order_meta( $order_id, $post_data = null ) {
		$order            = wc_get_order( $order_id );
		$shipping_methods = $order->get_shipping_methods();
		$shipping_method  = array_shift( $shipping_methods );

		$nonce_value    = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // phpcs:ignore
		if (
			empty( $nonce_value ) ||
			! wp_verify_nonce(
				$nonce_value,
				'woocommerce-process_checkout'
			)
		) {
			return;
		}

		$billing_pickit_fiscal_id = filter_input(
			INPUT_POST,
			'billing_pickit_fiscal_id',
			FILTER_SANITIZE_STRING
		);

        $shipping_pickit_fiscal_id = filter_input(
			INPUT_POST,
			'shipping_pickit_fiscal_id',
			FILTER_SANITIZE_STRING
		);
		Helper::log("checkout_update_order_meta------->" );

	    if (!empty($billing_pickit_fiscal_id)) {
            update_post_meta($order_id, '_billing_pickit_fiscal_id', sanitize_text_field($billing_pickit_fiscal_id));
        }
        if (!empty( $shipping_pickit_fiscal_id )) {
            update_post_meta($order_id, '_shipping_pickit_fiscal_id', sanitize_text_field( $shipping_pickit_fiscal_id ));
        }
	}

    /**
     * Show Admin Billing Fields
     *
     * @param array $fields
     * @return array
     */
    public static function admin_billing_fields($fields) {
        if("ADD" === Helper::get_option('fiscal-id-mode' )){
            if (is_admin()) {
                $fields['pickit_fiscal_id'] = array(
                    'label' => Helper::get_option('fiscal-id-label')  ,
                    'show' => true,
                    'style' => '',
                );
            }
        }

        return $fields;
    }

    /**
     * Show Admin Billing Fields
     *
     * @param array $fields
     * @return array
     */
    public static function admin_shipping_fields($fields) {
        if("ADD" === Helper::get_option('fiscal-id-mode' )){
            if (is_admin()) {
                $fields['pickit_fiscal_id'] = array(
                    'label' => Helper::get_option('fiscal-id-label')  ,
                    'show' => true,
                    'style' => '',
                );
            }
        }

        return $fields;
    }

}
