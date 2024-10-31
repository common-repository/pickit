<?php
namespace Ecomerciar\Pickit\Onboarding;

use Ecomerciar\Pickit\Helper\Helper;
use Ecomerciar\Pickit\SDK\PickitSdk;
defined('ABSPATH') || exit;

/**
 * Main Onboarding Class
 */
class Main {

  /**
  * Register Onboarding Page
  */
  public static function register_onboarding_page(){
    add_options_page('Onboarding - pickit', 'Onboarding - pickit', 'manage_options', 'wc-pickit-onboarding', ['\Ecomerciar\Pickit\Onboarding\Main', 'content'] );
  }

  /**
  * Get content
  */
   public static function content(){
    $data = [];
    $data['register'] = 'NONE';
    if(isset($_POST['option_page']) && 'wc-pickit-settings-onboarding' === $_POST['option_page'] ){
      if(isset($_POST['submit'])){
        Helper::set_option('webhook-payload-url', get_site_url());      
        Helper::set_option('api-key', filter_var($_POST['wc-pickit-api-key'], FILTER_SANITIZE_STRING));      
        Helper::set_option('api-secret', filter_var($_POST['wc-pickit-api-secret'], FILTER_SANITIZE_STRING));      
        Helper::set_option('api-country', filter_var($_POST['wc-pickit-api-country'], FILTER_SANITIZE_STRING));      

        $sdk = new PickitSdk();       
        if($sdk->register()){
          $data['register'] = 'OK';
        } else {
          $data['register'] = 'NOK';
        }
      }
    }  
  
    helper::get_template_part('page', 'onboarding',  $data );
    wp_enqueue_style('wc-pickit-onboarding');
   } 
}
