<?php
namespace Ecomerciar\Pickit\Api;

use Ecomerciar\Pickit\Helper\Helper;

/**
 * Pickit API Class
 */
class PickitApi extends ApiConnector implements ApiInterface {
	// const API_BASE_URL = 'https://pickit-woo-stage-api.conexa.ai/api';
	const API_BASE_URL = 'https://pickit-woocommerce-api.conexa.ai/api';

	const APPLICATION_JSON = 'application/json';

	/**
	 * Class Constructor
	 *
	 * @return Void
	 */
	public function __construct( array $settings = array() ) {
		$this->api_key            = $settings['api-key'];
		$this->api_secret         = $settings['api-secret'];
		$this->operation_mode     = $settings['operation-mode'];
		$this->api_key_sandbox    = $settings['api-key-sandbox'];
		$this->api_secret_sandbox = $settings['api-secret-sandbox'];

		$this->token_access           = '';
		$this->token_last_access_dttm = '';
		$this->token_error            = array();
	}

	/**
	 * Get Base API Url
	 *
	 * @return string
	 */
	public function get_base_url() {
		return self::API_BASE_URL;
	}

}
