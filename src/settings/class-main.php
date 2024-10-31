<?php
namespace Ecomerciar\Pickit\Settings;

use Ecomerciar\Pickit\Settings\Cron;
use Ecomerciar\Pickit\Settings\Section;
use Ecomerciar\Pickit\Helper\Helper;
use Ecomerciar\Pickit\SDK\PickitSdk;
defined('ABSPATH') || exit;

/**
 * A main class that holds all our settings logic
 */
class Main {
    /**
     * Add Pickit Setting Tab
     *
     * @param Array $settings_tab Shipping Methods
     * @return Array Shipping Methods
     */
    public static function add_tab_settings($settings_tab) {
        $settings_tab['pickit_shipping_options'] = __('pickit');
        return $settings_tab;
    }

    /**
     * Get Pickit Setting Tab
     *
     * @param Array $settings Shipping Methods
     * @param string $current_section Section which is beaing processing
     * @return Array Shipping Method Settings
     */
    public static function get_tab_settings($settings, $current_section) {
        if ('pickit_shipping_options' == $current_section) {
            add_action( 'admin_footer', ['Ecomerciar\Pickit\Settings\Main', 'enqueue_admin_js'], 10 ); // Priority needs to be higher than wc_print_js (25).
        
            return Section::get();
        }
        else {
            return $settings;
        }
    }

    /**
     * Get Pickit Settings
     *
     * @return Array Shipping Methods
     */
    public static function get_settings() {
        return apply_filters('wc_settings_pickit_shipping_options', Section::get());
    }

    /**
     * Update Pickit Settings
     *
     * @return Void
     */
    public static function update_settings() {
        woocommerce_update_options(self::get_settings());
    }


    public static function validate_credentials($sdk){        
        if (!$sdk->check_credentials()){
            add_action( 'admin_notices' , function(){
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . __( 'Las credenciales de <strong>pickit</strong> son incorrectas.' ,'wc-pickit') . '</p>';           
            echo '</div>';
            });
        }
    }

    public static function validate_credentials_sandbox($sdk){
        if (!$sdk->check_credentials_sandbox()){
            add_action( 'admin_notices' , function(){
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . __( 'Las credenciales de <strong>pickit</strong> Sandbox son incorrectas.' ,'wc-pickit') . '</p>';
            echo '</div>';
            });
        }
    }

    public static function register($sdk){
        if (!$sdk->register()){
            add_action( 'admin_notices' , function(){
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . __( 'No fue posible generar el Token  de <strong>pickit</strong>.' ,'wc-pickit') . '</p>';
            echo '</div>';
            });
        }
    }

    public static function register_sandbox($sdk){
        if (!$sdk->register_sandbox()){
            add_action( 'admin_notices' , function(){
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . __( 'No fue posible generar el Token  de <strong>pickit</strong> Sandbox.' ,'wc-pickit') . '</p>';
            echo '</div>';
            });
        }
    }

    /**
     * Save Settings - Execute validations
     *
     * @return Void
     */
    public static function save(){

        global $current_section;

        if ('pickit_shipping_options' == $current_section) {
            woocommerce_update_options(self::get_settings());
            Helper::set_option('webhook-payload-url', get_site_url( null, '/wc-api/wc-pickit-order-status' ) );
            $settings = Helper::get_setup_from_settings();
            $sdk = new PickitSdk($settings);                        
                                                
            if( Helper::is_sandbox()){
                if(empty($settings['token-sandbox']['token']) || 
                    $settings['token-sandbox']['api-key']!= $settings['api-key-sandbox'] ||
                    $settings['token-sandbox']['api-secret']!= $settings['api-secret-sandbox']){
                        self::register_sandbox($sdk);

                }
            }   else {
                if(empty($settings['token']['token']) || 
                    $settings['token']['api-key'] != $settings['api-key'] ||
                    $settings['token']['api-secret'] != $settings['api-secret']){
                        self::register($sdk);
                }

            }              
            Cron::run_cron();
        }
    }


    /**
     * enqueue_admin_js
     */
    public static function enqueue_admin_js(){
        wc_enqueue_js(
			"jQuery( function( $ ) {
                            
				function wcPickitSandbox( el ) {
					var form = $( el ).closest( 'form' );
                    var apiKey = $( '#wc-pickit-api-key', form ).closest( 'tr' );
                    var apiSecret = $( '#wc-pickit-api-secret', form ).closest( 'tr' );
					var apiKeySandbox = $( '#wc-pickit-api-key-sandbox', form ).closest( 'tr' );
					var apiSecretSandbox = $( '#wc-pickit-api-secret-sandbox', form ).closest( 'tr' );
					if ( 'sandbox' === $( el ).val() ) {
                        apiKey.hide();
                        apiSecret.hide();
                        apiKeySandbox.show();
                        apiSecretSandbox.show();
                    } else {
                        apiKey.show();
                        apiSecret.show();
                        apiKeySandbox.hide();
                        apiSecretSandbox.hide();
                    }                    
				}                

				$( document.body ).on( 'change', '#wc-pickit-operation-mode', function() {
					wcPickitSandbox( this );
				});

				// Change while load.
				$( '#wc-pickit-operation-mode' ).trigger( 'change' );

                function wcPickitFiscalField( el ){
                    var form = $( el ).closest( 'form' );
                    var label = $( '#wc-pickit-fiscal-id-label', form ).closest( 'tr' );
					var id = $( '#wc-pickit-fiscal-id-field', form ).closest( 'tr' );
                    if ( 'ADD' === $( el ).val() ) {
                        label.show();
                        id.hide();
                    }
                    if ( 'USE' === $( el ).val() ) {
                        label.hide();
                        id.show();
                    }
                    if ( 'NONE' === $( el ).val() ) {
                        label.hide();
                        id.hide();
                    }
                }

                $( document.body ).on( 'change', '#wc-pickit-fiscal-id-mode', function() {
					wcPickitFiscalField( this );
				});

				// Change while load.
				$( '#wc-pickit-fiscal-id-mode' ).trigger( 'change' );
			});"
		);
    }

}
