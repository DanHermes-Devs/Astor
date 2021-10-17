<?php
/*
 * Plugin Name: WooCommerce Custom Product Addons (Free)
 * Version: 2.3.10
 * Plugin URI: https://acowebs.com
 * Description: WooCommerce Product add-on plugin. Add custom fields to your WooCommerce product page. With an easy-to-use Custom Form Builder, now you can add extra product options quickly.
 * Author URI: https://acowebs.com
 * Author: Acowebs
 * Requires at least: 4.0
 * Tested up to: 5.3.2
 * Requires PHP: 5.6
 * Text Domain: woo-custom-product-addons
 * WC requires at least: 3.3.0
 * WC tested up to: 3.9.1
 */

if (!defined('ABSPATH'))
    exit;

define('WCPA_POST_TYPE', 'wcpa_pt_forms');
define('WCPA_LIST_PAGE_HOOCK', 'wcpa_manage');
define('WCPA_CART_ITEM_KEY', 'wcpa_data');

if(!defined('WCPA_UPLOAD_DIR')){
    define('WCPA_UPLOAD_DIR', 'wcpa_uploads');
}
define('WCPA_PRODUCT_META_KEY', '_wcpa_product_meta');
define('WCPA_PRODUCT_META_FIELD', 'wcpa_product_meta');
define('WCPA_ORDER_META_KEY', '_WCPA_order_meta_data');
define('WCPA_TEXT_DOMAIN', 'woo-custom-product-addons');
define('WCPA_FORM_META_KEY', '_wcpa_fb-editor-data');
define('WCPA_SETTINGS_KEY', 'wcpa_settings_key');
define('WCPA_PRODUCTS_TRANSIENT_KEY', 'wcpa_products_transient');
define('WCPA_TOKEN', 'wcpa');
define('WCPA_VERSION', '2.3.10');
define('WCPA_FILE', __FILE__);
define('WCPA_PLUGIN_NAME', 'WooCommerce Custom Product Addons (Free)');


require_once(realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes/helpers.php');

function wcpa_init() {
    $plugin_rel_path = basename(dirname(__FILE__)) . '/languages'; /* Relative to WP_PLUGIN_DIR */

    load_plugin_textdomain('woo-custom-product-addons', false, $plugin_rel_path);
}

add_action('plugins_loaded', 'wcpa_init');
spl_autoload_register('wcpa_autoloader');

function wcpa_autoloader($class_name) {
    if (0 === strpos($class_name, 'WCPA')) {
        $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
        $class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
        require_once $classes_dir . $class_file;
    }
}

if (!function_exists('WCPA')) {

    function WCPA() {
        $instance = WCPA_Backend::instance(__FILE__, WCPA_VERSION);
        return $instance;
    }

}

if (is_admin()) {
    WCPA();
}
new WCPA_Front_End(__FILE__, WCPA_VERSION);
