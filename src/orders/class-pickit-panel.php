<?php

namespace  Ecomerciar\Pickit\Orders;

defined('ABSPATH') || EXIT;

use Ecomerciar\Pickit\Sdk\PickitSdk;
use Ecomerciar\Pickit\Helper\Helper;
/*
* Main Plugin Process Class
*/
class PickitPanel {

  /**
   * Creates Process Page
   *
   * @return void
   */
  public static function create_menu_option()  {
      add_submenu_page(
        'woocommerce',
        __( 'Panel pickit', 'wc-pickit' ),
        __( 'Panel pickit', 'wc-pickit' ),
        'manage_woocommerce',
        'pickit-panel',
        [__CLASS__, 'page_content']
      );

  }

  /**
   * Displays process page
   *
   * @return void
   */
  public static function page_content()  {
    if (!is_admin() && !current_user_can('manage_options') && !current_user_can('manage_woocommerce')){
        die(__('what are you doing here?', 'wc-pickit' ));
      }        
      $sameTarget = (isset($_GET['sameTarget']))? true : false;          
      $sdk = new PickitSdk();
      $token = $sdk->get_connect_token();
      $has_token = empty($token)? false: true;
      $href = "https://pickit-woocommerce.conexa.ai/pickit/woo/loader?accessToken=" . $token;
      Helper::get_template_part('pickitpanel','init', ['has_token'=> $has_token, 'href' => $href, 'sameTarget' => $sameTarget]);
      wp_enqueue_style('wc-pickit-panel');
  }
}
