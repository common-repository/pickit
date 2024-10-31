<?php

namespace  Ecomerciar\Pickit\Orders;

defined('ABSPATH') || EXIT;

use Ecomerciar\Pickit\SDK\PickitSdk;
use Ecomerciar\Pickit\Helper\Helper;
use \WC_Order;
use Ecomerciar\Pickit\ShippingMethod\WC_Pickit;
/*
* Main Plugin Process Class
*/
class Actions {

    public static function handle_final_budget($order) {
        $shipping_methods = $order->get_shipping_methods();
        if (empty($shipping_methods)) {
            return;
        }
        
        $shipping_method = array_shift($shipping_methods);

        if ("pickit" !== $shipping_method->get_method_id()){
            return;
        }

        $sdk = new PickitSdk();
        $shipping_method_pickit = new WC_Pickit($shipping_method->get_instance_id());
        $uuid = $sdk->get_final_budget($order, $shipping_method_pickit);            
        $shipping_method->update_meta_data('Pickit UUID', $uuid);
        $shipping_method->delete_meta_data('uuid');
        $shipping_method->save();
        $order->add_order_note(sprintf(__('Cotización final generada ( uuid: %s ).', 'wc-pickit') , $uuid ));
    }

    public static function handle_send_to_panel($order) {
        if( empty($order)){
            return;
         }
         $shipping_methods = $order->get_shipping_methods();
         if (empty($shipping_methods)) {
             return;
         }
         $shipping_method = array_shift($shipping_methods);
         if("pickit"!==$shipping_method->get_method_id()){
             return;
         }                 
         Helper::log("Create Order");        
         $sdk = new PickitSdk();
         $uuid = $shipping_method->get_meta('Pickit UUID');
         $sdk->create_order($order, $uuid );
         $order->add_order_note(sprintf(__('El pedido fue enviado al panel de pickit ( uuid: %s ).', 'wc-pickit') , $uuid ));
    }

    /**
     * Adds New Order Action -> Process Enviamelo Order
     *
     * @param arrray $actions
     * @return Array
     */
    public static function add_order_action($actions) {
        global $theorder;
        $shipping_methods = $theorder->get_shipping_methods();
        if (empty($shipping_methods)) {
            return $actions;
        }
        $shipping_method = array_shift($shipping_methods);
        /*Verify It's Enviamelo Shipping Methos*/
        if ('pickit' === $shipping_method->get_method_id() ) {
            $actions['wc_pickit_final_budget'] = __('Pickit - Generar Cotización Final', 'wc-pickit');
            $actions['wc_pickit_send_to_panel'] = __('Pickit - Enviar a Panel', 'wc-pickit');
        }
        return $actions;
    }

}