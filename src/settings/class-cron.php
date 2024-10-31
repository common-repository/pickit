<?php
namespace Ecomerciar\Pickit\Settings;

use Ecomerciar\Pickit\Sdk\PickitSdk;
use Ecomerciar\Pickit\Helper\Helper;

defined('ABSPATH') || exit;

/**
 * Cron Processor's Main Class
 */
class Cron {


  /**
   * Run Cron Action
   *
   */
  public static function run_cron(){

    Helper::log(__("Ejecución Cron Settings Pickit", 'wc-pickit'));

    if(! Helper::has_current_token()){
        Helper::log(__("No Token", 'wc-pickit'));
        return;
    }

    $sdk = new PickitSdk();    
    Helper::set_option('points', $sdk->get_points());    
    Helper::log(__("Fin Cron Settings Pickit", 'wc-peya'));

  }

}
