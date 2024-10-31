<?php
/**
 * Class Webhooks
 *
 * @package  Ecomerciar\Pickit\Orders\Webhooks
 */

namespace Ecomerciar\Pickit\Orders;

use Ecomerciar\Pickit\Helper\Helper;

defined( 'ABSPATH' ) || exit();

/**
 * WebHook's base Class
 */
class Webhook {

	const OK    = 'HTTP/1.1 200 OK';
	const ERROR = 'HTTP/1.1 500 ERROR';

	/**
	 * Receives the webhook and check if it's valid to proceed
	 *
	 * @param string $data Webhook json Data for testing purpouses.
	 *
	 * @return bool
	 */
	public static function listener( string $data = null, string $data_header = null) {
       
		// Takes raw data from the request.
		if ( is_null( $data ) || empty( $data ) ) {
			$json = file_get_contents( 'php://input' );
		} else {
			$json = $data;
		}

		$appkey = '';
		if (is_null($data_header) || empty($data_header)){
			$headers = getallheaders();
			Helper::log($headers);
			$appkey = isset($headers['Appkey'])? $headers['Appkey'] : '';
		} else {
			$appkey = $data_header;
		}

		Helper::log_info( 'Webhook recibido' );	
		Helper::log(
			__FUNCTION__ .
				__( '- Webhook recibido de Pickit:', 'wc-pickit' ) .
				$json
		);
		Helper::log(__('AppKey Recibido: ', 'wc-pickit') . esc_html($appkey) );

		$process = self::process_webhook( $json , $appkey);
		
		if ( is_null( $data ) || empty( $data ) ) {
			// Real Webhook.
			if ( $process ) {
				header( self::OK );
			} else {
				header( self::ERROR );
				wp_die(
					__( 'WooCommerce Pickit Webhook no válido.', 'wc-pickit' ),
					'Pickit Webhook',
					array( 'response' => 500 )
				);
			}
		} else {
			// For testing purpouse.
			return $process;			
		}
	}


	/**
	 * Process Webhook
	 *
	 * @param json $json Webhook data for.
	 *
	 * @return bool
	 */
	public static function process_webhook( $json, $appkey ) {

		// Converts it into a PHP object.
		$data = json_decode( $json, true );

		if ( empty( $data ) || ! self::validate_input( $data ) || ! self::validate_appkey( $appkey ) ) {
			return false;
		}
		return self::handle_webhook( $data );		
	}
	
	private static function validate_appkey( string $appkey){
		if (Helper::is_sandbox()){
			$appKeyStored = isset(Helper::get_option('token-sandbox')['webhook-token'])? Helper::get_option('token-sandbox')['webhook-token'] : '';
		} else {
			$appKeyStored = isset(Helper::get_option('token')['webhook-token'])? Helper::get_option('token')['webhook-token']: '';
		}
		if ($appKeyStored !== $appkey) {
			Helper::log(
				__FUNCTION__ .
					__( '- AppKey no coincide.', 'wc-pickit' )
			);
			return false;
		}
		return true;
	}


	/**
	 * Validates the incoming webhook
	 *
	 * @param array $data Webhook data to be validated.
	 *
	 * @return bool
	 */
	private static function validate_input( array $data = [] ) {
		$return = true;
		if ( ! isset( $data['trakingOrder'] ) || empty( $data['trakingOrder'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook recibido sin id.', 'wc-pickit')
			);
			$return = false;
		}
		if ( ! isset( $data['status'] ) || empty( $data['status'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook recibido sin status.', 'wc-pickit' )
			);
			$return = false;
		}		
		return $return;
	}

	/**
	 * Handles and processes the webhook
	 *
	 * @param array $data webhook data to be processed.
	 *
	 * @return bool
	 */
	private static function handle_webhook( array $data = [] ) {

		$order = wc_get_order($data['trakingOrder']);
		if(empty($order)){
			Helper::log(
				__FUNCTION__ .
					__( '- No existe orden.', 'wc-pickit' )
			);
			return false;
		}
		$pickitStatus = Helper::get_status();
		if(!isset($pickitStatus[$data['status']])){
			Helper::log(
				__FUNCTION__ .
					__( '- Status no válido.', 'wc-pickit' )
			);
			return false;
		}

		$order->get_shipping_methods();
        $shipping_methods = $order->get_shipping_methods();
        if (empty($shipping_methods)) {
			Helper::log(
				__FUNCTION__ .
					__( '- Orden no tiene método de envío.', 'wc-pickit' )
			);
            return false;
        }
        $shipping_method = array_shift($shipping_methods);

        if ("pickit" !== $shipping_method->get_method_id()){
			Helper::log(
				__FUNCTION__ .
					__( '- Orden no tiene Pickit como método de envío.', 'wc-pickit' )
			);
            return false;
        }

		if('processing'===$order->get_status()){
			if('delivered'===$data['status']){
				$order->set_status('completed');
			}			
		}

		$order->add_order_note(sprintf(__('Pickit> ( "%s" %s).', 'wc-pickit') , $pickitStatus[$data['status']]['title'], $pickitStatus[$data['status']]['subtitle'] ));
		$order->save();

		return true;
	}
}
