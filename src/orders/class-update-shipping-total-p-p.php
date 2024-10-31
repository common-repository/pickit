<?php
namespace Ecomerciar\Pickit\Orders;

use Ecomerciar\Pickit\Helper\Helper;
use Ecomerciar\Pickit\SDK\PickitSdk;

/**
 * Orders Base Action Class
 */
abstract class UpdateShippingTotalPP {    

    /**
     * Run Action
     *
     * @param \WC_Order $order
     * @param string $deliveryTime
     *
     * @return bool
     */
    public static function run( ){
        $available_methods = WC()->shipping->get_packages();
        return  $available_methods;
    }

    public static function ajax_callback_wp(){
        if (!wp_verify_nonce($_POST['nonce'], 'wc-pickit')) {
            wp_send_json_error();
        }    
        
        //Save selected point to session
        WC()->session->set( 'pickit_point_selected', isset($_POST['pickit_point_selected'])? $_POST['pickit_point_selected'] : null );
        WC()->session->set( 'pickit_point_name_selected', isset($_POST['pickit_point_name_selected'])? $_POST['pickit_point_name_selected'] : null );

        //Force to Recalculate Shippings
        $packages = WC()->cart->get_shipping_packages();
        foreach ($packages as $package_key => $package ) {
            WC()->session->set( 'shipping_for_package_' . $package_key, false ); 
        }

        wp_send_json_success();      
    }

}