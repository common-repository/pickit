<?php
namespace Ecomerciar\Pickit\Settings;

use Ecomerciar\Pickit\Helper\Helper;
/**
 * Pickit Setting Section Main
 */
class Section {

    /**
     * Checks system requirements
     *
     * @return Array Fields Settings for Pickit
     */
    public static function get() {
       
        $wc_order_statuses = wc_get_order_statuses();
        $wc_order_statuses["0"] = __('Deshabilitar procesamiento automático','wc-pickit');


        $logo_url = Helper::get_assets_folder_url() . '/img/pickit_isologo.png';

        $settings = array(

            array(                
                'desc' => sprintf(__('<div style="margin-top:50px;background-color:#FFFFFF;padding: 40px 5px 20px 0px; border-radius: 0px 15px 0px 0px;"><img src="%s" ></div>', 'wc-pickit'), $logo_url),
                'type' => 'title',
                'id' => 'wc-pickit_shipping_options',
            ) , 
                array(
                    'name'      => __('Modo de Operación', 'wc-pickit'),
                    'id'        => 'wc-pickit-operation-mode',
                    'type'      => 'select',
                    'default'   => 'production',                   
                    'options'   => [
                        'production' => 'Producción',
                        'sandbox'    => 'Sandbox'                        
                    ]
                ) ,
                array(
                    'name' => __('API Key Sandbox','wc-pickit') ,
                    'type' => 'text',
                    'id' => 'wc-pickit-api-key-sandbox',                
                ) ,
                array(
                    'name' => __('Token Id Sandbox','wc-pickit') ,
                    'type' => 'password',
                    'id' => 'wc-pickit-api-secret-sandbox',                
                ) ,          
                array(
                    'name' => __('API Key','wc-pickit') ,
                    'type' => 'text',
                    'id' => 'wc-pickit-api-key',                
                ) ,
                array(
                    'name' => __('Token Id','wc-pickit') ,
                    'type' => 'password',
                    'id' => 'wc-pickit-api-secret',                
                ) ,
                array(
                    'name' => __('País','wc-pickit') ,
                    'type' => 'select',
                    'id' => 'wc-pickit-api-country',    
                    'options' => Helper::get_countries(),     
                ) ,           
            array(
                'type' => 'sectionend',
                'id' => 'wc-pickit_shipping_options'
            ) ,  
            array(
                'title' => __('Opciones Avanzadas',  'wc-pickit') ,
                'type'  => 'title',
                'id'    => 'wc-pickit_shipping_options_advance',
            ) ,
                array(
                    'name' => __('Estado a procesar', 'wc-pickit') ,
                    'type' => 'select',
                    'id' => 'wc-pickit-process-order-status',
                    'desc_tip' => true,
                    'default' => 'wc-processing',
                    'desc' => __('Cuando un pedido alcance este estado, se enviará la información a pickit.', 'wc-pickit') ,
                    'options' => $wc_order_statuses
                ) ,                
            array(
                    'type' => 'sectionend',
                    'id'   => 'wc-pickit_shipping_options_debug'
            ) ,  
            array(
                'title' => __('Tienda',  'wc-pickit') ,
                'type'  => 'title',
                'id'    => 'wc-pickit_shipping_options_shop',
            ) ,
                array(
                    'name' => __('Identificador Fiscal', 'wc-pickit') ,
                    'type' => 'select',
                    'id' => 'wc-pickit-fiscal-id-mode',
                    'desc_tip' => true,
                    'default' => 'wc-processing',
                    'desc' => __('Seleccione cómo desea utilizar el campo de Identifación Fiscal del comprador.', 'wc-pickit') ,
                    'options' => [
                        'ADD' => __('Crear nuevo campo', 'wc-pickit'),
                        'USE' => __('Utilizar campo existente', 'wc-pickit'),
                        'NONE' => __('Desactivado (no utilizar)', 'wc-pickit'),
                    ]                    
                ) ,
                array(
                    'name' => __('Nombre del campo mostrado en el Checkout','wc-pickit') ,
                    'type' => 'text',
                    'id' => 'wc-pickit-fiscal-id-label',                
                ) ,
                array(
                    'name' => __('ID del Identificador Fiscal','wc-pickit') ,
                    'type' => 'text',
                    'id' => 'wc-pickit-fiscal-id-field',                
                ) ,
            array(
                'type' => 'sectionend',
                'id'   => 'wc-pickit_shipping_options_shop'
            ) ,
            array(
                'title' => __('Debug',  'wc-pickit') ,
                'desc'  => sprintf(__('Puede habilitar el debug del plugin para realizar un seguimiento de la comunicación efectuada entre el plugin y la API de pickit. Podrá ver el registro desde el menú <a href="%s">WooCommerce > Estado > Registros</a>.', 'wc-pickit') , esc_url(get_admin_url(null, 'admin.php?page=wc-status&tab=logs')) ),
                'type'  => 'title',
                'id'    => 'wc-pickit_shipping_options_debug',
            ) ,
                array(
                    'name'      => '' ,
                    'id'        => 'wc-pickit-debug',
                    'type'      => 'checkbox',
                    'default'   => 'no',
                    'desc'  => __('Habilitar Debug', 'wc-pickit'),
                ) ,
            array(
                    'type' => 'sectionend',
                    'id'   => 'wc-pickit_shipping_options_debug'
            ) ,
        );

        return $settings;
    }

}
