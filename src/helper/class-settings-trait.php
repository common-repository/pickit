<?php
namespace Ecomerciar\Pickit\Helper;

/**
 * Settings Trait
 */
trait SettingsTrait {
				
    /**
     * Gets a plugin option
     *
     * @param string $key
     * @param boolean $default
     * @return mixed
     */
    public static function get_option(string $key, $default = false) {
                    return get_option('wc-pickit-' . $key, $default);
    }
    
    /**
     * Get settings
     *
     * @return array
     */
    public static function get_setup_from_settings() {
                    return ['api-key' => self::get_option('api-key', '') ,
                            'api-secret' => self::get_option('api-secret', '') ,
                            'api-country' => self::get_option('api-country', '') ,
                            'process-order-status' => self::get_option('process-order-status', '') ,
                            'operation-mode' => self::get_option('operation-mode', 'production') ,
                            'api-key-sandbox' => self::get_option('api-key-sandbox', '') ,
                            'api-secret-sandbox' => self::get_option('api-secret-sandbox', '') ,
                            'debug' => self::get_option('debug', 'no') ,
                            'webhook-payload-url' => self::get_option('webhook-payload-url', '') ,                            
                            'token' => self::get_option('token', ['api-key' =>'', 'api-secret' =>'', 'token' => '', 'webhook-token' => '']),
                            'token-sandbox' => self::get_option('token-sandbox', ['api-key' =>'', 'api-secret' =>'', 'token' => '', 'webhook-token' => '']),
                            'fiscal-id-mode' => self::get_option('fiscal-id-mode', 'NOUSE') ,
                            'fiscal-id-label' => self::get_option('fiscal-id-label', __('Identificador Fiscal', 'wc-pickit')) ,
                            'fiscal-id-field' => self::get_option('fiscal-id-field', 'NOUSE') ,
                    ];
    }

        /**
         * Set a plugin option
         *
         * @param string $key
         * @param boolean $default
         * @return mixed
         */
        public static function set_option(string $key, $default = false) {
                return update_option('wc-pickit-'  . $key, $default);
        }

        public static function is_sandbox(){
                return (self::get_option('operation-mode') == 'sandbox') ? true: false;
        }

        public static function has_current_token(){
                if( self::is_sandbox()){
                       return (isset(self::get_option('token-sandbox')['token']) && !empty(self::get_option('token-sandbox')['token']));
                } else {
                        return (isset(self::get_option('token')['token']) && !empty(self::get_option('token-sandbox')['token']));
                }
        }        

    }
