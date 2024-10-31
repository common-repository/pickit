<?php 
namespace Ecomerciar\Pickit\ShippingMethod;

use Ecomerciar\Pickit\Helper\Helper;
/**
 * Settings for PedidosYa rate shipping.
 *
 */

defined('ABSPATH') || exit;

$addr_labels = Helper::get_country_address_labels(Helper::get_option('api-country'));
$settings = array(
  'title' => array(
      'title' => __('Nombre Método Envío', 'wc-pickit') ,
      'type' => 'text',
      'description' => __('Nombre con el que aparecerá el tipo de envío en tu tienda.', 'wc-pickit') ,
      'default' => __('Pickit', 'wc-pickit') ,
      'desc_tip' => true,
  ),
  'wc-pickit-service-type' => array(
    'title' => __('Servicio', 'wc-pickit') ,
    'type' => 'select',
    'options' => [
      'PP' => __('Envío a punto' , 'wc-pickit'),
      'ST' => __('Envío a domicilio', 'wc-pickit'),
      'BO' => __('Ambos', 'wc-pickit'),
    ],
    'default' => __('Pickit', 'wc-pickit') ,
    'desc_tip' => true,
  ),

  // Sección Tienda
  'wc-pickit_store_section' => array(
  'title' =>  __('Tienda', 'wc-pickit'),
  'description' =>  __('Crea tu punto de despacho para que podamos retirar tus paquetes <strong>pickit</strong>.', 'wc-pickit') ,
  'type' => 'title',
  ) ,

    'wc-pickit-forwarding-agent-name'=> array(
      'title' => __('Nombre de quién prepara los envíos', 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),
      'desc_tip' => false
    ) ,
    'wc-pickit-forwarding-agent-last-name'=> array(
      'title' => __('Apellido de quién prepara los envíos', 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),
      'desc_tip' => false
    ) ,
    'wc-pickit-pickup-phone'=> array(
      'title' => __('Teléfono', 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required'),
      'desc_tip' => true,
      //'sanitize_callback' => array($this, "sanitize_phone"),
      'description' => __('El formato del teléfono debe ser:<br/>un prefijo opcional con el símbolo +<br/>solo números, no pueden tener letras<br/>el número debe comenzar con un número entre 1 y 9 y luego de 5 a 14 dígitos (0 a 9).', 'wc-pickit') ,
    ) ,
    'wc-pickit-pickup-email'=> array(
      'title' => __('Correo electrónico', 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required'),
      'desc_tip' => true,
      //'sanitize_callback' => array($this, "sanitize_phone"),
    ) ,
    'wc-pickit-pickup-address' => array(
      'title' => __( $addr_labels['address'] , 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),
    ) ,
    'wc-pickit-pickup-address-nbr' => array(
      'title' => __( $addr_labels['address-nbr'] , 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),
    ) ,
    'wc-pickit-pickup-city' => array(
      'title' => __( $addr_labels['address-city'] , 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),            
    ) ,
    'wc-pickit-pickup-state' => array(
      'title' => __( $addr_labels['address-state'] , 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),            
    ) ,
    'wc-pickit-pickup-postalcode' => array(
      'title' => __( $addr_labels['address-postalcode'], 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),            
    ) ,
    'wc-pickit-pickup-notes' => array(
      'title' => __('Notas Adicionales', 'wc-pickit') ,
      'type' => 'text',
      'custom_attributes' => array( 'required' => 'required' ),            
    ) ,
  // Sección Tarifa
  'wc-pickit_price_section' => array(
    'title' =>  __('Tarifa', 'wc-pickit'),
    'description' =>  __('Defina el tipo de tarifa que desea aplicar:', 'wc-pickit') ,
    'type' => 'title',
    ) ,
    'wc-pickit-price' => array(
      'title' => __('Tipo', 'wc-pickit') ,
      'type' => 'select',
      'custom_attributes' => array( 'required' => 'required' ),
      'options' => [
        'DINAMIC' => __('Tarifa dinámica de pickit', 'wc-pickit'),
        'FIXED' => __('Tarifa fija', 'wc-pickit'),
        'ADJUST' => __('Tarifa dinámica ajustada', 'wc-pickit'),
      ]            
    ) ,
    'wc-pickit-price-fixed' => array(
      'title' => __('Costo de envío de $', 'wc-pickit') ,
      'type' => 'number',
      'custom_attributes' => array( 'required' => 'required' ),        
    ) ,
    'wc-pickit-price-adj-type' => array(
      'title' => __('Quiero aplicar', 'wc-pickit') ,
      'type' => 'select',
      'custom_attributes' => array( 'required' => 'required' ),   
      'options' => [
        'CHARGE' => 'Recargo',
        'DISCOUNT' => 'Descuento'
      ]     
    ) ,
    'wc-pickit-price-adj-pct' => array(
      'title' => __('del %', 'wc-pickit') ,
      'type' => 'number',
      'custom_attributes' => array( 'required' => 'required' ),      
    ) ,
  // Sección Envío Gratuito
  'wc-pickit_free_delivey' => array(
  'title' =>  __('Envíos Gratis', 'wc-pickit'),
  'description' =>  __('Puedes ofrecer envíos gratis a partir del monto que desees. El costo correrá por tu cuenta.', 'wc-pickit') ,
  'type' => 'title',
  ) ,
    'wc-pickit-free-delivery' =>     array(
      'title' => __('Envíos Gratis', 'wc-pickit'),
      'label' =>  __('Activar', 'wc-pickit'),
      'type'  => 'checkbox',
      'default' => 'no',
    ) ,
    'wc-pickit-free-delivery-from'=> array(
      'title' => __('Monto Mínimo de Compra', 'wc-pickit') ,
      'type' => 'number',
      'default' => 0,
      'custom_attributes' => array( 'required' => 'required'),
      'desc_tip' => true,
      //'sanitize_callback' => array($this, "sanitize_cost"),
      'description' => __('El envío será gratuito para el comprador siempre que el pedido supere el monto indicado en este campo.', 'wc-pickit') ,
    ) ,  
  );

return $settings;