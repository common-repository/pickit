<?php
defined('ABSPATH') || exit;

// --- Shipment Method
add_filter( 'woocommerce_shipping_methods', ['PickitWoo', 'add_shipping_method']);

// --- Plugin Links
add_filter( 'plugin_action_links_' . plugin_basename(PickitWoo::MAIN_FILE) , ['PickitWoo', 'create_settings_link']);

// --- Settings
add_filter( 'woocommerce_get_sections_shipping', ['\Ecomerciar\Pickit\Settings\Main', 'add_tab_settings']);
add_filter( 'woocommerce_get_settings_shipping', ['\Ecomerciar\Pickit\Settings\Main', 'get_tab_settings'], 10, 2);
add_action( 'woocommerce_update_options_pickit_shipping_options', ['\Ecomerciar\Pickit\Settings\Main', 'update_settings']);

// Process/save the settings
add_action( 'woocommerce_settings_save_shipping', ['\Ecomerciar\Pickit\Settings\Main', 'save'] );

// --- Onboarding
add_action( 'admin_menu',  ['\Ecomerciar\Pickit\Onboarding\Main', 'register_onboarding_page']);

// --- Add Link to Pickit Middleman
add_action( 'manage_posts_extra_tablenav', ['\Ecomerciar\Pickit\Orders\AdminList', 'add_extra_button_tablenav'], 1,1);

// --- Go to Pickit Panel
add_action( 'admin_menu', ['\Ecomerciar\Pickit\Orders\PickitPanel', 'create_menu_option']);

// --- Process New Order - Create Final Budget
add_action( 'woocommerce_order_status_changed', ['\Ecomerciar\Pickit\Orders\ProcessNewOrder', 'handle_order_status'], 10, 4);
add_action( 'woocommerce_checkout_update_order_meta', ['\Ecomerciar\Pickit\Orders\ProcessNewOrder', 'process_new_checkout'], 30, 1 );

// --- Cron update Points
//add_action( 'wc_pickit_cron_update_points', ['\Ecomerciar\Pickit\Settings\Cron','run_cron']  );

// --- CheckOut Fields
add_action( 'woocommerce_checkout_update_order_meta', array( '\Ecomerciar\Pickit\CheckOut\PointField', 'checkout_update_order_meta' ) );
add_filter( 'woocommerce_order_details_before_order_table', array( '\Ecomerciar\Pickit\CheckOut\PointField', 'display_fields' ) );
add_action( 'woocommerce_after_shipping_rate', array( '\Ecomerciar\Pickit\CheckOut\PointField', 'shipping_point_field' ) );
add_filter( 'woocommerce_checkout_fields', array( '\Ecomerciar\Pickit\CheckOut\PointField', 'override_checkout_fields' ) );
add_action( 'woocommerce_after_checkout_validation', array( '\Ecomerciar\Pickit\CheckOut\PointField', 'checkout_validation' ) , 9999, 2);

add_filter('woocommerce_checkout_fields', ['\Ecomerciar\Pickit\CheckOut\FiscalIdField', 'override_checkout_fields']);
add_action('woocommerce_checkout_update_order_meta', ['\Ecomerciar\Pickit\CheckOut\FiscalIdField', 'checkout_update_order_meta']);
add_filter('woocommerce_admin_billing_fields', ['\Ecomerciar\Pickit\CheckOut\FiscalIdField', 'admin_billing_fields']);
add_filter('woocommerce_admin_shipping_fields', ['\Ecomerciar\Pickit\CheckOut\FiscalIdField', 'admin_shipping_fields']);
add_filter('woocommerce_billing_fields', ['\Ecomerciar\Pickit\CheckOut\FiscalIdField', 'admin_billing_fields']);
add_filter('woocommerce_shipping_fields', ['\Ecomerciar\Pickit\CheckOut\FiscalIdField', 'admin_shipping_fields']);

// --- Order Actions
add_action('woocommerce_order_actions', ['\Ecomerciar\Pickit\Orders\Actions', 'add_order_action']);
add_action('woocommerce_order_action_wc_pickit_final_budget', ['\Ecomerciar\Pickit\Orders\Actions', 'handle_final_budget']);
add_action('woocommerce_order_action_wc_pickit_send_to_panel', ['\Ecomerciar\Pickit\Orders\Actions', 'handle_send_to_panel']);

// --- Webhook
add_action('woocommerce_api_wc-pickit-order-status', ['\Ecomerciar\Pickit\Orders\Webhook', 'listener']);


add_filter('woocommerce_package_rates', ['\Ecomerciar\Pickit\Shippingmethod\WC_Pickit', 'package_rates']);

// --- Ajax for Checkout Select point
add_action( 'wp_ajax_pickit_action_update_shipping_total_pp', ['\Ecomerciar\Pickit\Orders\UpdateShippingTotalPP', 'ajax_callback_wp']);
add_action( 'wp_ajax_nopriv_pickit_action_update_shipping_total_pp', ['\Ecomerciar\Pickit\Orders\UpdateShippingTotalPP', 'ajax_callback_wp'] );

/*
function action_woocommerce_checkout_update_order_review($array, $int)
{
    WC()->cart->calculate_shipping();
    return;
}
add_action('woocommerce_checkout_update_order_review', 'action_woocommerce_checkout_update_order_review', 10, 2);*/