<?php
/**
 * Point Field Class
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
class PointField {
	/**
	 * Show Branch Select Field
	 *
	 * @param string $method Shipping Method Selected.
	 * @param array  $index Shipping Method Index.
	 */
	public static function shipping_point_field( $method, $index = 0 ) {
		if ( ! is_checkout() ) {
			return; // Only on checkout page.
		}

		$shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		$shipping_method  = $shipping_methods[0];

		// If shipping method is the choosen one.
		if ( $method->id === $shipping_method ) {
			// If shipping method choosen is from Cool Correo Argentino.
			if ( false !== strpos( $shipping_method, 'pickit' ) ) {
				$instance_id          = substr(
					$shipping_method,
					strpos( $shipping_method, ':' ) + 1
				);

                $meta_data = $method->get_meta_data();
                if(isset($meta_data['Service Type']) && 'PP'===$meta_data['Service Type'] ){
                    ?>

					<?php 
						$data = [
							//'points' => Helper::get_points(),
							'method-id' =>  $instance_id ,
							'uuid'	=> $meta_data['Pickit UUID'],
							'pickit_point' => isset($meta_data['Punto']) ? $meta_data['Punto'] : '',
							'pickit_point_name' => isset($meta_data['Descripcion Punto']) ? $meta_data['Descripcion Punto'] : '',
						] ;
						helper::get_template_part('checkout', 'point-map-selector',  $data );						
					?>

					<?php
                }                				
			}
		}
	}

	/**
	 * Show Point Select Field
	 *
	 * @param array $fields Checkout Fields.
	 */
	public static function override_checkout_fields( $fields ) {
		array_push(
			$fields['billing']['billing_state']['class'],
			'update_totals_on_change'
		);
		// check if it's setting up.
		array_push(
			$fields['shipping']['shipping_state']['class'],
			'update_totals_on_change'
		);	
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

		$pickit_point = filter_input(
			INPUT_POST,
			'pickit_point',
			FILTER_SANITIZE_STRING
		);
		Helper::log("checkout_update_order_meta------->" );
		Helper::log($_POST);
		Helper::log($pickit_point );
		if ( $pickit_point ) {
			wc_add_order_item_meta(
				$shipping_method->get_id(),
				'Punto',
				sanitize_text_field( $pickit_point )
			);
		}
	}

	/**
	 * Display New fields on Order
	 *
	 * @param WC_ORDER $order Woo Order Object.
	 */
	public static function display_fields( $order ) {
		
	}


	public static function checkout_validation( $fields, $errors ){
		// if any validation errors

		if (isset($_POST['pickit_point']) && ("none" === $_POST['pickit_point'] || empty($_POST['pickit_point']))){
			$errors->add( 'pickit_point_required', __( 'Debe seleccionar un punto de retiro.' , 'wc-pickit' ) );
		}
	}
}
