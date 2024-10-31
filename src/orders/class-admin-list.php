<?php
namespace Ecomerciar\Pickit\Orders;

use Ecomerciar\Pickit\Helper\Helper;

defined('ABSPATH') || exit;

/**
 * Main Onboarding Class
 */
class AdminList {

    public static function add_extra_button_tablenav($post){
        global $post_type_object;
        if ($post_type_object->name === 'shop_order') {
            helper::get_template_part('adminlist', 'gotopickit',  ['href'=> admin_url( 'admin.php?page=pickit-panel&sameTarget=1' ) , 'label' => __('Ir al panel pickit', 'wc-pickit')] );
        }
    }

}