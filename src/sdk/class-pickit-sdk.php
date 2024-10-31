<?php
namespace Ecomerciar\Pickit\Sdk;

use Ecomerciar\Pickit\Api\PickitApi;
use Ecomerciar\Pickit\Helper\Helper;
use Ecomerciar\Pickit\ShippingMethod\WC_Pickit;
/**
 * PedidosYa SDK Main Class
 */
class PickitSdk {

    /**
     * Constructor Method
     *
     * @return Void
     */
    public function __construct($settings = []) {
        if (empty($settings)){
            $settings = Helper::get_setup_from_settings();            
        }
        $this->settings = $settings;
        $this->api = new PickitApi($this->settings);
    }
    
    public function register(bool $isTest = false){
        $endpoint = "/register-store";

        $uuid36 = wp_generate_uuid4();             
        $uuid32 = str_replace( '-', '', $uuid36 ); 

        //Add StoreName validation.
        $store = "";
        if(empty(get_bloginfo( 'name' ))){
            $protocols = array('http://', 'http://www.', 'www.', 'https://', 'https://www.');
            $store = str_replace($protocols, '', get_bloginfo('wpurl'));            
        } else {
            $store = get_bloginfo( 'name' );
        }
        if( empty($store) ){
            $store = "WOOSTORE";
        }
        
        $body = [];
        $body['store'] =  $store;
        $body['url'] = get_site_url();
        $body['webhook_payload'] = $this->settings['webhook-payload-url'];
        $body['webhook_appkey'] = $uuid32;
        $body['key'] = $this->settings['api-key'];
        $body['secret'] = $this->settings['api-secret'];
        $body['country'] = Helper::remove_unwanted_chars(strtolower(Helper::get_countries()[$this->settings['api-country']]));
        if ($isTest){
            $body['key'] = $this->settings['api-key-sandbox'];
            $body['secret'] = $this->settings['api-secret-sandbox'];
        }    
        $body['test'] = $isTest;

        $headers = [];
        $headers['Content-Type'] = "application/json";      
  
        $res = $this->api->post($endpoint, $body, $headers);
        $res = $this->handle_response($res, __FUNCTION__);

        if(isset($res['token'])){            
            if ($isTest){
                Helper::set_option('token-sandbox', [
                    'api-key' => $this->settings['api-key'],
                    'api-secret' => $this->settings['api-secret'],
                    'token' => $res['token'],
                    'webhook-token' => $uuid32,
                ]);
            } else {
                Helper::set_option('token', [
                    'api-key' => $this->settings['api-key'],
                    'api-secret' => $this->settings['api-secret'],
                    'token' => $res['token'],
                    'webhook-token' => $uuid32,
                ]);
            }
            return true;
        }
        if ($isTest){
            Helper::set_option('token-sandbox', [
                'api-key' => '',
                'api-secret' => '',
                'token' => '',
                'webhook-token' => '',
            ]);
        } else {
            Helper::set_option('token', [
                'api-key' => '',
                'api-secret' => '',
                'token' => '',
                'webhook-token' => '',
            ]);
        }

        return false;
    }

    public function register_sandbox(){
       return $this->register(true);
    }

    public function check_credentials(){
        return false;
    }

    public function check_credentials_sandbox(){
        return false;
    }

    private function get_bearer_header(){
        return (Helper::is_sandbox())? "Bearer " . $this->settings['token-sandbox']['token'] : "Bearer " . $this->settings['token']['token'];
    }

    public function get_connect_token(){
        $endpoint = "/connect/token";

        $body = [];       
                
        $headers = [];
        $headers['Content-Type'] = "application/json";     
        $headers['Authorization'] = $this->get_bearer_header();   

        $res = $this->api->post($endpoint, $body, $headers);
        return $this->handle_response($res, __FUNCTION__);        
    }
    
    /**
     * get price estimation
     *
     * @param array $item
     * @param array $destination
     * @param int $shipping_method_id
     * @param int $shipping_method_instance
     * @return int
     */
    public function get_price($items, $destination, $service_type, $pick_up = Array('address1' => '', 'postalCode' => '', 'city'=> '', 'province'=>''), $point = 0){
        Helper::log("get_price--->");
        $endpoint = "/budget";
        $operationType = ("ST"==$service_type)? 2: 1;
        $body = [];  
        $body['serviceType']   =  'PP';
        $body['workflowTag']   =  "dispatch";
        $body['operationType']   =  $operationType;
        $body['retailer']   =  Array(
            'tokenId' => (Helper::is_sandbox())? $this->settings['api-secret-sandbox'] : $this->settings['api-secret'] ,
        );

        $body['products']   =  Array();
        $grouped_items = Helper::group_items($items);
        foreach ($grouped_items as $item) {
            $body['products'][] = Array(
                'name' => $item["description"],
                'weight' => Array(
                    'amount' => floatval($item["weight"]),
                    'unit' => "kg"
                ),
                'length' => Array(
                    'amount' => floatval($item["length"]),
                    'unit' => 'cm'
                ),
                'height' => Array(
                    'amount' =>  floatval($item["height"]),
                    'unit' => 'cm'
                ),
                'width' => Array(
                    'amount' =>  floatval($item["width"]),
                    'unit' => 'cm'
                ),
                'price' =>  floatval($item["price"]),
                'sku' => $item["sku"],
                'amount' => floatval($item['quantity'])
            );           
        }

        $body['retailerAlternativeAddress']   = Array(
            'postalCode' => $pick_up['postalCode'],
            'address' => $pick_up['address1'],
            'city' => $pick_up['city'],
            'province' => $pick_up['province']
        );
        $body['sla']   =  Array(
            'id' => 1
        );
        $body['customer']   =  Array(
            'name' => 'PRICING',
            'lastName' => 'PRICING',
            'pid' => '1',
            'email' => 'EMAIL@PRICING.COM',
            'phone' => '584121501269',
            'address' => Array(
                'postalCode' => $destination['postcode'] ,                
                'address' => trim($destination['address_1']) ,           
                'city' => $destination['city'] ,
                'province' => Helper::get_province_name($destination['country'], $destination['state'] )
            ),            
        );
        $body['pointId'] = intval($point);
    
        $headers = [];
        $headers['Content-Type'] = "application/json";     
        $headers['Authorization'] = $this->get_bearer_header();   
        $res = $this->api->post($endpoint, $body, $headers);        
        return $this->handle_response($res, __FUNCTION__);      
    }

    /**
     * get budget from UUID
     */
    public function get_budget($uuid){
        Helper::log("get_budget--->");
        $endpoint = "/budget/" . $uuid;
        $body = [];
        $headers = [];
        $headers['Content-Type'] = "application/json";     
        $headers['Authorization'] = $this->get_bearer_header();   
        $res = $this->api->get($endpoint, $body, $headers);
        return $this->handle_response($res, __FUNCTION__);
    }

    /**
     * get price estimation
     *
     * @param array $item
     * @param array $destination
     * @param int $shipping_method_id
     * @param int $shipping_method_instance
     * @return int
     */
    public function get_final_budget(\WC_Order $order){
        Helper::log("get_final_budget--->");
        $endpoint = "/budget";

        $customer = Helper::get_customer_from_order($order);

        $itemList = array();
        $items = Helper::get_items_from_order($order);       
       
        $order->get_shipping_methods();
        $shipping_methods = $order->get_shipping_methods();
        if (empty($shipping_methods)) {
            return;
        }
        $shipping_method = array_shift($shipping_methods);

        if ("pickit" !== $shipping_method->get_method_id()){
            return;
        }

        $shipping_method_pickit = new WC_Pickit($shipping_method->get_instance_id());
        $operationType = $shipping_method->get_meta('Service Type');
        $point = $shipping_method->get_meta('Punto');       

        $body = [];  
        $body['serviceType']   =  'PP';
        $body['workflowTag']   =  "dispatch";
        $body['operationType']   =  ("ST"===$operationType)? 2 : 1 ;
        $body['retailer']   =  Array(
            'tokenId' => (Helper::is_sandbox())? $this->settings['api-secret-sandbox'] : $this->settings['api-secret'] ,
        );

        $body['products']   =  Array();
        $grouped_items = Helper::group_items($items);
        foreach ($grouped_items as $item) {
            $body['products'][] = Array(
                'name' => $item["description"],
                'weight' => Array(
                    'amount' => floatval($item["weight"]),
                    'unit' => "kg"
                ),
                'length' => Array(
                    'amount' => floatval($item["length"]),
                    'unit' => 'cm'
                ),
                'height' => Array(
                    'amount' =>  floatval($item["height"]),
                    'unit' => 'cm'
                ),
                'width' => Array(
                    'amount' =>  floatval($item["width"]),
                    'unit' => 'cm'
                ),
                'price' =>  floatval($item["price"]),
                'sku' => $item["sku"],
                'amount' => floatval($item['quantity'])
            );           
        }

        $body['retailerAlternativeAddress']   = Array(
            'postalCode' => $shipping_method_pickit->pickup_postalcode,
            'address' => $shipping_method_pickit->pickup_address . " " . $shipping_method_pickit->pickup_address_nbr,
            'city' => $shipping_method_pickit->pickup_city,
            'province' => $shipping_method_pickit->pickup_state
        );
        $body['sla']   =  Array(
            'id' => 1
        );
        $body['customer']   =  Array(
            'name' =>  $customer['full_name'],
            'lastName' =>  $customer['full_name'],
            'pid' =>  strval($customer['pid']),
            'email' =>  $customer['email'],
            'phone' => $customer['phone'],
            'address' => Array(
               'postalCode' => $customer['cp'] ,
               'address' => trim($customer['address_1']) ,             
               'city' => $customer['locality'] ,
               'province' => $customer['province']
            ),            
        );
        
        $body['pointId'] = ("ST"===$operationType)? 0 : intval($point) ;
    
        $headers = [];
        $headers['Content-Type'] = "application/json";     
        $headers['Authorization'] = $this->get_bearer_header();   
        $res = $this->api->post($endpoint, $body, $headers);
        return $this->handle_response($res, __FUNCTION__);      
    }
    
    /**
     * create order in middleman
     *
     *
     */
    public function create_order(\WC_Order $order, $uuid){
        $endpoint = "/orders";

        $customer = Helper::get_customer_from_order($order);

        $order->get_shipping_methods();
        $shipping_methods = $order->get_shipping_methods();
        if (empty($shipping_methods)) {
            return;
        }
        $shipping_method = array_shift($shipping_methods);

        if ("pickit" !== $shipping_method->get_method_id()){
            return;
        }

        $shipping_method_pickit = new WC_Pickit($shipping_method->get_instance_id());
        $operationType = $shipping_method->get_meta('Service Type');
        $point = $shipping_method->get_meta('Punto');      


        $body = [];  
        $body['uuid'] = $uuid;
        $body['store'] = get_bloginfo( 'name' );
        $body['serviceType'] = 'PP';
        $body['workflowTag'] = 'dispatch';
        $body['operationType'] = ("ST"===$operationType)? 2 : 1 ;
        $body['firstState'] = 1;
        $body['trakingInfo'] = [
            'order' => $order->get_ID(),
            'shipment' => '',//$order->get_ID()
        ];
        $body['deliveryTimeRange'] = [
            'start' =>  gmdate("Y-m-d\T00:00:00.000\Z", time()),
            'end' => gmdate("Y-m-d\T23:59:59.999\Z", time())
        ];
        $body['refoundProbableCause'] = "";
        $body['observations'] = trim($customer['address_2'] . " " . $customer['extra_info']);
        $body['date_shipping'] = gmdate("Y-m-d\TH:i:s.000\Z", time());
        $body['currier'] = "pickit";
        $body['packageAmount'] = 1;        

        $headers = [];
        $headers['Content-Type'] = "application/json";     
        $headers['Authorization'] = $this->get_bearer_header();   
        $res = $this->api->post($endpoint, $body, $headers);
        return $this->handle_response($res, __FUNCTION__);      
    }

    public function get_points(){
        $endpoint = "/points";
        $body = []; 
        $headers = [];
        $headers['Content-Type'] = "application/json";     
        $headers['Authorization'] = $this->get_bearer_header();   
        $res = $this->api->get($endpoint, $body, $headers);
        return $this->handle_response($res, __FUNCTION__);      
    }

    protected function handle_response($response, string $function_name)
    {
        if(!empty($response['errors'])){
            if("register"===$function_name || "register_sandbox" === $function_name){
                if(!empty($response['errors']['errors']['credentials'])){
                    return ['error' => __('Credenciales inválidas.' , 'wc-pickit')] ;
                }
                return ['error' => __('No fue posible registrar el cliente.' , 'wc-pickit')] ;
            }  
            if("get_connect_token" === $function_name){
                return "";
            } 
            if("get_price" === $function_name){
                return ['price'=> false, 'uuid' =>'', 'lightbox' => ''];
            } 
            if("get_budget" === $function_name){
                return ['price'=> false, 'uuid' =>'', 'lightbox' => ''];
            }
            if("get_final_budget" === $function_name){
                return false;
            } 
            if("get_points" === $function_name){
                return [];
            }                    
        }
        
        if(!is_array($response)){
            return $response;
        }
        
        if("register" === $function_name || "register_sandbox" === $function_name){
            return ['token' => $response['token'] ];
        }

        if("get_connect_token" === $function_name){
            return $response['storeKey'];
        }   

        if("get_price" === $function_name){
            return ['price'=> $response['totalPrice'], 'uuid' =>$response['uuid'], 'lightbox' => $response['urlMap']];
        }  

        if("get_final_budget" === $function_name){
            return $response['uuid'];
        }

        if("get_points" === $function_name){
            return $response['result'];
        }  
        return $response;        
    }    

}
