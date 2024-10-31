<?php
namespace Ecomerciar\Pickit\ShippingMethod;

use Ecomerciar\Pickit\SDK\PickitSdk;
use Ecomerciar\Pickit\Helper\Helper;
use WC_Shipping_Method;

defined('ABSPATH') || class_exists('\WC_Shipping_Method') || exit;

/**
 * Our main payment method class
 */
class WC_Pickit extends \WC_Shipping_Method {
    /**
     * Default constructor
     *
     * @param int $instance_id Shipping Method Instance from Order
     * @return void
     */
    public function __construct($instance_id = 0) {
        $this->id = 'pickit';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('pickit', 'wc-pickit');
        $this->method_description = __('Permite a tus clientes calcular el costo del envío por Pickit.', 'wc-pickit');
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        );
        $this->init();
    }

    /**
     * Init user set variables.
     *
     * @return void
     */
    public function init() {
        $this->instance_form_fields = include 'settings-pickit.php';
        $this->title = $this->get_option('title');

        // Custom Settings
        $this->forwarding_agent_name = $this->get_option('wc-pickit-forwarding-agent-name');
        $this->forwarding_agent_last_name = $this->get_option('wc-pickit-forwarding-agent-last-name');
        $this->pickup_phone = $this->get_option('wc-pickit-pickup-phone');
        $this->pickup_email = $this->get_option('wc-pickit-pickup-email');
        $this->pickup_address = $this->get_option('wc-pickit-pickup-address');
        $this->pickup_address_nbr = $this->get_option('wc-pickit-pickup-address-nbr');
        $this->pickup_city = $this->get_option('wc-pickit-pickup-city');
        $this->pickup_state = $this->get_option('wc-pickit-pickup-state');
        $this->pickup_postalcode = $this->get_option('wc-pickit-pickup-postalcode');
        $this->pickup_notes = $this->get_option('wc-pickit-pickup-notes');
        $this->price = $this->get_option('wc-pickit-price');
        $this->price_fixed = $this->get_option('wc-pickit-price-fixed');
        $this->price_adj_type = $this->get_option('wc-pickit-price-adj-type');
        $this->price_adj_pct = $this->get_option('wc-pickit-price-adj-pct');
        $this->free_delivery = $this->get_option('wc-pickit-free-delivery');
        $this->free_delivery_from = $this->get_option('wc-pickit-free-delivery-from');
        $this->service_type = $this->get_option('wc-pickit-service-type');

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, array(
            $this,
            'process_admin_options'
        ));
        add_action( 'admin_footer', ['Ecomerciar\Pickit\ShippingMethod\WC_Pickit', 'enqueue_admin_js'], 10 ); // Priority needs to be higher than wc_print_js (25).
        add_action( 'admin_footer', ['Ecomerciar\Pickit\ShippingMethod\WC_Pickit', 'enqueue_admin_style'], 10 ); // Priority needs to be higher than wc_print_js (25).
    }

   /**
     * Sanitize the cost field.
     *
     * @return string
     */
    public function sanitize_cost($value) {
        $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return $value;
    }

    /**
     * Calculate the shipping costs.
     *
     * @param array $package Package of items from cart.
     * @return void
     */
    public function calculate_shipping($package = array()) {
        $rateDefaults = array(
            'label' => $this->get_option('title') , // Label for the rate
            'cost' => '0', // Amount for shipping or an array of costs (for per item shipping)
            'taxes' => '', // Pass an array of taxes, or pass nothing to have it calculated for you, or pass 'false' to calculate no tax for this method
            'calc_tax' => 'per_order', // Calc tax per_order or per_item. Per item needs an array of costs passed via 'cost'
            'package' => $package,
            'term' => '',
        );
       
        $has_costs = false;

        $items = Helper::get_items_from_cart(WC()->cart);
        if ($items === false) {
            return;
        }
       
        $sdk = new PickitSdk();
        Helper::log("SDK->");

        if(empty($package['destination']['address_1']) || empty($package['destination']['city']) || empty($package['destination']['country']) || empty($package['destination']['state']) ){
            //avoid estimation if not defines Country, State, City, Address_1
            return;
        }    

        $res = $sdk->get_price($items,
                $package['destination'], 
                "ST",
                [  
                    'address1' => $this->pickup_address . ' ' . $this->pickup_address_nbr,              
                    'postalCode'  => $this->pickup_postalcode ,
                    'city'  => $this->pickup_city ,
                    'province'  => $this->pickup_state ,
                ] 
            );

        $defaultCost = $res['price'];
        //Calc Standad shipping (Customer Address)
        if("BO"===$this->service_type || "ST" === $this->service_type){            
            $resCost = $res['price'];
            if ($resCost){

                $has_costs = true; 
                
                switch ($this->price) {
                    case 'DINAMIC':
                        $cost = floatval($resCost);
                        break;
                    case 'FIXED':
                        $cost = floatval($this->price_fixed);
                        break;
                    case 'ADJUST':
                        $adjust = ("DISCOUNT"===$this->price_adj_type)?  (100 - floatval($this->price_adj_pct)) /100 : (100 + floatval($this->price_adj_pct)) / 100;
                        $cost = floatval($resCost) * $adjust ;
                        break;
                    default:
                        $cost = floatval($resCost);
                        break;
                }
            }

            $freeLabel = "";
            if( $has_costs && 'yes' === $this->free_delivery && floatval($this->free_delivery_from ) <= floatval($package['contents_cost']) ){
                $cost = 0;
                $freeLabel = __(' - ¡Gratis!', 'wc-pickit');
            } 

            if( $has_costs ){
               
                $rateST = $rateDefaults;
                $rateST['id'] =  $this->get_rate_id() . '_' . '2';
                $rateST['label'] = $rateST['label'] . __(' - Envío a domicilio', 'wc-pickit') . $freeLabel;
                $rateST['cost'] =  floatval($cost);
                $rateST['meta_data'] = [
                    'Service Type' => 'ST',  
                    'Pickit UUID' => $res['uuid'],
                    'lightbox' => $res['lightbox']                 
                ];
                $this->add_rate($rateST);
            }
        }

        $has_costs = false;
        //Calc Point shipping (Pickit Point)
        if("BO"===$this->service_type || "PP" === $this->service_type){
            //GET DEFAULT
            $base_points = Helper::get_points();
            $point_obj = Helper::get_point_for_state($package['destination']['country'], $package['destination']['state']);
            $point = 0;
            if(!empty($point_obj)){
                $point = $point_obj['idService'];
            } else {
                if (count($base_points)>0){
                    $point = $base_points[0]['idService'];
                } 
            }            
            $isDefaultPoint = true;
            $point_selected = WC()->session->get( 'pickit_point_selected');
            $point_name_selected = WC()->session->get( 'pickit_point_name_selected');
            Helper::log($point . " - " . $point_selected );
            if(!empty($point_selected)){
                $point = $point_selected;
                $isDefaultPoint = false;
                $point_name_selected_bse = (isset($base_points[$point]))? $base_points[$point]['name']: '';
                $point_name_selected = empty($point_name_selected)? $point_name_selected_bse : $point_name_selected;
            }
            $res = $sdk->get_price($items,
                $package['destination'], 
                "PP",
                [  
                    'address1' => $this->pickup_address . ' ' . $this->pickup_address_nbr,              
                    'postalCode'  => $this->pickup_postalcode ,
                    'city'  => $this->pickup_city ,
                    'province'  => $this->pickup_state ,
                ] , $point
            );

            $resCost = empty($res['price'])? $defaultCost : $res['price'];
            if ($resCost){

                $has_costs = true; 
                
                switch ($this->price) {
                    case 'DINAMIC':
                        $cost = floatval($resCost);
                        break;
                    case 'FIXED':
                        $cost = floatval($this->price_fixed);
                        break;
                    case 'ADJUST':
                        $adjust = ("DISCOUNT"===$this->price_adj_type)?  (100 - floatval($this->price_adj_pct)) /100 : (100 + floatval($this->price_adj_pct)) / 100;
                        $cost = floatval($resCost) * $adjust ;
                        break;
                    default:
                        $cost = floatval($resCost);
                        break;
                }
            }
            $estimatedLabel = ($isDefaultPoint)? __(" - Valor Aproximado", 'wc-pickit') : "";
            $freeLabel = "";
            if( $has_costs && 'yes' === $this->free_delivery && floatval($this->free_delivery_from ) <= floatval($package['contents_cost']) ){
                $cost = 0;
                $freeLabel = __(' - ¡Gratis!', 'wc-pickit');
                $estimatedLabel= "";
            } 

            if( $has_costs ){
                

                $ratePP = $rateDefaults;
                $rateST['id'] =  $this->get_rate_id() . '_' . '1';
                $ratePP['label'] = $ratePP['label'] . __(" - Envío a punto", 'wc-pickit') . $freeLabel . $estimatedLabel;
                $ratePP['cost'] =  floatval($cost); 
                if($isDefaultPoint){
                    $ratePP['meta_data'] = [
                        'Service Type' => 'PP',
                        'Pickit UUID' => $res['uuid'],
                        'lightbox' => $res['lightbox'],
                        'Punto Default' => $point                   
                    ];                               
                } else {
                    $ratePP['meta_data'] = [
                        'Service Type' => 'PP',
                        'Pickit UUID' => $res['uuid'],
                        'lightbox' => $res['lightbox'],
                        'Punto' => $point,
                        'Descripcion Punto' => (isset(Helper::get_points()[$point]))? Helper::get_points()[$point]['name']: '',            
                    ];                    
                }
                $this->add_rate($ratePP);
            }
        }                                
    }

    public static function package_rates($rates){
        /*Helper::log("package_rates");        
        $point_selected = WC()->session->get( 'pickit_point_selected');
        Helper::log($point_selected);
        $shipping_type = "pickit";        
        foreach ($rates AS $id => $data) {            
            if ($shipping_type === $data->method_id) {   
                if($data->meta_data['Service Type'] == "PP"){
                    $data->label =  $data->label ."-". $point_selected;
                }             
               */ 
               /* $sdk = new PickitSdk();
                Helper::log("SDK->");
        
                $res = $sdk->get_budget($data->meta_data['Pickit UUID']);
                write_log($res);
                */
                /*if($data->meta_data['Service Type'] == "PP"){
                    $data->label = "PP";
                }*/
               
          /*  }
        }*/
        return $rates;
    }

    public static function enqueue_admin_js() {
		wc_enqueue_js(
			"jQuery( function( $ ) {

                $( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
					if ( 'wc-modal-shipping-method-settings' === target ) {
                        $('#woocommerce_pickit_wc-pickit-forwarding-agent-name').closest('table').addClass('pickit-two-columns');
					}
				} );
               

				function wcPickitPriceType( el ) {
					var form = $( el ).closest( 'form' );
					var priceFixed = $( '#woocommerce_pickit_wc-pickit-price-fixed', form ).closest( 'tr' );
					var priceAdjType = $( '#woocommerce_pickit_wc-pickit-price-adj-type', form ).closest( 'tr' );
                    var priceAdjPct = $( '#woocommerce_pickit_wc-pickit-price-adj-pct', form ).closest( 'tr' );
					if ( 'DINAMIC' === $( el ).val() || '' === $( el ).val() ) {
						priceFixed.hide();
						priceAdjType.hide();
                        priceAdjPct.hide();
					} else if ( 'FIXED' === $( el ).val() ){
						priceFixed.show();
						priceAdjType.hide();
                        priceAdjPct.hide();
					} else {
                        priceFixed.hide();
						priceAdjType.show();
                        priceAdjPct.show();
                    }                  
				}

				$( document.body ).on( 'change', '#woocommerce_pickit_wc-pickit-price', function() {
					wcPickitPriceType( this );
				});

				// Change while load.
				$( '#woocommerce_pickit_wc-pickit-price' ).trigger( 'change' );
				$( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
					if ( 'wc-modal-shipping-method-settings' === target ) {
						wcPickitPriceType( $( '#wc-backbone-modal-dialog #woocommerce_pickit_wc-pickit-price', evt.currentTarget ) );
					}
				} );

                function wcPickitFree( el ) {
                    var form = $( el ).closest( 'form' );
                    var freeDeliveryFrom = $( '#woocommerce_pickit_wc-pickit-free-delivery-from', form ).closest( 'tr' );            
                    if ( $( el ).prop('checked') ) {
                      freeDeliveryFrom.show();             
                    } else {         
                      freeDeliveryFrom.hide();
                    }                  
                  }            
                  $( document.body ).on( 'change', '#woocommerce_pickit_wc-pickit-free-delivery', function() {
                    wcPickitFree( this );
                  });  
                  // Change while load.
                  $( '#woocommerce_pickit_wc-pickit-free-delivery' ).trigger( 'change' );
                  $( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
                    if ( 'wc-modal-shipping-method-settings' === target ) {
                        wcPickitFree( $( '#wc-backbone-modal-dialog #woocommerce_pickit_wc-pickit-free-delivery', evt.currentTarget ) );
                    }
                  } );


			});"
		);
	}

    public static function enqueue_admin_style(){
        ?>
        <style>        
            .wc-modal-shipping-method-settings form .form-table.pickit-two-columns tbody{
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;               
            }
            .wc-modal-shipping-method-settings form .form-table.pickit-two-columns th, .wc-modal-shipping-method-settings form .form-table.pickit-two-columns td{
                width: 50%;
            }
        </style>
        <?php
    }

}
