<?php
/**
 * Plugin Name: Pickit - WooCommerce
 * Description: Método de Envío de WooCommerce para Pickit.
 * Version: 3.0.1
 * Requires PHP: 7.0
 * Author: Conexa
 * Author URI: https://conexa.ai
 * Text Domain: wc-pickit
 * WC requires at least: 5.4.1
 * WC tested up to: 5.4
 */

defined('ABSPATH') || exit;

add_action( 'plugins_loaded', ['PickitWoo', 'init'] );
add_action( 'activated_plugin', ['PickitWoo', 'activation'] );
add_action( 'deactivated_plugin', ['PickitWoo', 'deactivation'] );
add_action( 'wp_enqueue_scripts', ['PickitWoo', 'register_scripts'] );
add_action( 'admin_enqueue_scripts', ['PickitWoo', 'register_scripts'] );

use Ecomerciar\Pickit\Helper\Helper;
 
/**
 * Plugin's base Class
 */
class PickitWoo {

    const VERSION = '3.1';
    const PLUGIN_NAME = 'Pickit';
    const MAIN_FILE = __FILE__;
    const MAIN_DIR = __DIR__;

    /**
     * Checks system requirements
     *
     * @return bool
     */
    public static function check_system() {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $system = self::check_components();

        if ($system['flag']) {
            deactivate_plugins(plugin_basename(__FILE__));
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . sprintf(__('<strong>%s/strong> Requiere al menos %s versión %s o superior.', 'wc-pickit') , self::PLUGIN_NAME, $system['flag'], $system['version']) . '</p>';
            echo '</div>';
            return false;
        }

        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . sprintf(__('WooCommerce debe estar activo antes de usar <strong>%s</strong>', 'wc-pickit') , self::PLUGIN_NAME) . '</p>';
            echo '</div>';
            return false;
        }
        return true;
    }

    /**
     * Check the components required for the plugin to work (PHP, WordPress and WooCommerce)
     *
     * @return array
     */
    private static function check_components() {
        global $wp_version;
        $flag = $version = false;

        if (version_compare(PHP_VERSION, '7.0', '<')) {
            $flag = 'PHP';
            $version = '7.0';
        }
        elseif (version_compare($wp_version, '5.4', '<')) {
            $flag = 'WordPress';
            $version = '5.4';
        }
        elseif (!defined('WC_VERSION') || version_compare(WC_VERSION, '4.3', '<')) {
            $flag = 'WooCommerce';
            $version = '4.3';
        }

        return ['flag' => $flag, 'version' => $version];
    }

    /**
     * Inits our plugin
     *
     * @return void
     */
    public static function init() {
        if (!self::check_system()) {
            return false;
        }

        spl_autoload_register(
			function ( $class ) {
				// Plugin base Namespace.
				if ( strpos( $class, 'Pickit' ) === false ||  strpos( $class, 'Ecomerciar' ) === false  ) {
					return;
				}
				$class     = str_replace( '\\', '/', $class );
				$parts     = explode( '/', $class );
				$classname = array_pop( $parts );

				$filename = $classname;
				$filename = str_replace( 'WooCommerce', 'Woocommerce', $filename );
				$filename = str_replace( 'WC_', 'Wc', $filename );
				$filename = str_replace( 'WC', 'Wc', $filename );
				$filename = preg_replace( '/([A-Z])/', '-$1', $filename );
				$filename = 'class' . $filename;
				$filename = strtolower( $filename );
				$folder   = strtolower( array_pop( $parts ) );
				if ( 'class-pickit-woo' === $filename ) {
					return;
				}
				require_once plugin_dir_path( __FILE__ ) . 'src/' . $folder . '/' . $filename . '.php';
			}
		);

        include_once __DIR__ . '/hooks.php';
        self::load_textdomain();
        Helper::init();
    }

    /**
     * Create a link to the settings page, in the plugins page
     *
     * @param array $links
     * @return array
     */
    public static function create_settings_link(array $links) {
        $link = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=shipping&section=pickit_shipping_options')) . '">' . __('Ajustes', 'wc-pickit') . '</a>';
        array_unshift($links, $link);
        $link = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=wc-pickit-onboarding')) . '">' . __('Onboarding', 'wc-pickit') . '</a>';
        array_unshift($links, $link);
        return $links;
    }

    /**
     * Adds our shipping method to WooCommerce
     *
     * @param array $shipping_methods
     * @return array
     */
    public static function add_shipping_method($shipping_methods) {
        $shipping_methods['pickit'] = '\Ecomerciar\Pickit\ShippingMethod\WC_Pickit';
        return $shipping_methods;       
    }

    /**
     * Loads the plugin text domain
     *
     * @return void
     */
    public static function load_textdomain() {
        load_plugin_textdomain('wc-pickit', false, basename(dirname(__FILE__)) . '/i18n/languages');
    }
    
    /**
     * Start Cron Schedule
     *
     * @return void
     */
    public static function start_schedule_cron(){
        if( ! wp_next_scheduled( 'wc_pickit_cron_update_points') ) {
          wp_schedule_event( current_time( 'timestamp' ), 'twicedaily', 'wc_pickit_cron_update_points');
        }
      }
  
      /**
       * Stop Cron Schedule
       *
       * @return void
      */
      public static function stop_schedule_cron(){
        if( wp_next_scheduled( 'wc_pickit_cron_update_points') ) {
          wp_clear_scheduled_hook( 'wc_pickit_cron_update_points' );
        }
      }

    /**
     * Activation Plugin Actions
     *
     * @return void
     */
    public static function activation($plugin){        
        if( $plugin == plugin_basename( self::MAIN_FILE ) ) {
            self::start_schedule_cron();
            exit( wp_redirect(  admin_url( 'admin.php?page=wc-pickit-onboarding' ) ));
          }
      }

      /**
     * DeActivation Plugin Actions
     *
     * @return void
     */
      public static function deactivation($plugin){
            self::stop_schedule_cron();
      }

    /**
     * Registers all scripts to be loaded laters
     *
     * @return void
     */
    public static function register_scripts()
    {
        wp_register_style('wc-pickit-onboarding', Helper::get_assets_folder_url() . '/css/onboarding.css');
        wp_register_style('wc-pickit-panel', Helper::get_assets_folder_url() . '/css/panel.css');
        wp_register_style('wc-pickit-checkout-point', Helper::get_assets_folder_url() . '/css/checkout-point.css');  

        if(is_checkout()){
            wp_enqueue_style('wc-pickit-checkout-point');
        }                
    }
}
?>
