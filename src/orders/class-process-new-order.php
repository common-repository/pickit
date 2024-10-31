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
class ProcessNewOrder {


    public static function handle_order_status(int $order_id, string $status_from, string $status_to, \WC_Order $order) {
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
        if("wc-" . $order->get_status() != Helper::get_option('process-order-status', '')){
            return;
        }
        Helper::log("Create Order");        
        $sdk = new PickitSdk();
        $uuid = $shipping_method->get_meta('Pickit UUID');
        $sdk->create_order($order, $uuid );
        $order->add_order_note(sprintf(__('El pedido fue enviado al panel de pickit ( uuid: %s ).', 'wc-pickit') , $uuid ));
    }   

    public static function process_new_checkout($order_id){
        $order = wc_get_order($order_id);       
        if( empty($order)){
            return;
        }
        $shipping_methods = $order->get_shipping_methods();
        if (empty($shipping_methods)) {
            return;
        }
        $shipping_method = array_shift($shipping_methods);
        if("pickit"===$shipping_method->get_method_id()){
            $sdk = new PickitSdk();
            $shipping_method_pickit = new WC_Pickit($shipping_method->get_instance_id());
            $uuid = $sdk->get_final_budget($order, $shipping_method_pickit);            
            $shipping_method->update_meta_data('Pickit UUID', $uuid);
            $shipping_method->delete_meta_data('uuid');
      		$shipping_method->save();
            $order->add_order_note(sprintf(__('Cotización final generada ( uuid: %s ).', 'wc-pickit') , $uuid ));
        }
    }
}
