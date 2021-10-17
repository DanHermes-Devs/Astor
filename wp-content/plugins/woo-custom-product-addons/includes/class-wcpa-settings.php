<?php

if (!defined('ABSPATH'))
    exit;

class WCPA_Settings
{

    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    public function __construct()
    {
        $this->_version = WCPA_VERSION;
        $this->_token = WCPA_TOKEN;
        $this->file = WCPA_FILE;
        $this->dir = dirname($this->file);
        add_action('admin_menu', array($this, 'register_options_page'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('init', array($this, 'check_migration'));
        $plugin = plugin_basename($this->file);
        add_filter("plugin_action_links_$plugin", array($this, 'add_settings_link'));
    }

    /**
     *
     *
     * Ensures only one instance of CPO is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main CPO instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

    public function check_migration()
    {
        $migration = new WCPA_Migration();
        $migration->check();
    }

    public function admin_notices()
    {

    }

    public function add_settings_link($links)
    {
        $settings = '<a href="' . admin_url('options-general.php?page=wcpa_settings') . '">' . __('Settings', 'woo-custom-product-addons') . '</a>';
        $products = '<a href="' . admin_url('edit.php?post_type=' . WCPA_POST_TYPE) . '">' . __('Create Forms', 'woo-custom-product-addons') . '</a>';
        $pro_link = '<a href="https://acowebs.com/woo-custom-product-addons/?ref=free-wcpa" target="_blank">' . __('Premium version', 'woo-custom-product-addons') . '</a>';

        array_unshift($links, $products, $settings, $pro_link);

        return $links;
    }

    function register_options_page()
    {
        add_options_page('Custom Product Addons', 'Custom Product Addons', 'manage_options', 'wcpa_settings', array($this, 'options_page'));
    }

    public function options_page()
    {


        if (array_key_exists('wcpa_save_settings', $_POST)) {
            $this->save_settings();
        }
        if (array_key_exists('action', $_GET)) {
            if ($_GET['action'] == 'migrate') {
                if (isset($_GET['wcpa_nonce']) && wp_verify_nonce($_GET['wcpa_nonce'], 'wcpa_migration')) {
                    $migration = new WCPA_Migration();
                    $response = $migration->version_migration();
                    WCPA_Backend::view('settings-migration', ['response' => $response]);
                }
            }
        } else if (array_key_exists('view', $_GET)) {
            if ($_GET['view'] == 'migration') {
                WCPA_Backend::view('settings-migration', []);
            }
        } else {
            WCPA_Backend::view('settings-main', []);
        }
    }

    public function save_settings()
    {
        if (isset($_POST['wcpa_save_settings']) && wp_verify_nonce($_POST['wcpa_nonce'], 'wcpa_save_settings')) {
            $settings = get_option(WCPA_SETTINGS_KEY);

//            if (isset($_POST['options_total_label'])) {
//                $settings['options_total_label'] = sanitize_text_field($_POST['options_total_label']);
//            }
//            if (isset($_POST['options_product_label'])) {
//                $settings['options_product_label'] = sanitize_text_field($_POST['options_product_label']);
//            }
//            if (isset($_POST['total_label'])) {
//                $settings['total_label'] = sanitize_text_field($_POST['total_label']);
//            }

            if (isset($_POST['wcpa_show_meta_in_cart'])) {
                $settings['show_meta_in_cart'] = true;
            } else {
                $settings['show_meta_in_cart'] = false;
            }
            if (isset($_POST['add_to_cart_text'])) {
                $settings['add_to_cart_text'] = sanitize_text_field($_POST['add_to_cart_text']);
            }

            if (isset($_POST['wcpa_show_meta_in_checkout'])) {
                $settings['show_meta_in_checkout'] = true;
            } else {
                $settings['show_meta_in_checkout'] = false;
            }
            if (isset($_POST['wcpa_show_meta_in_order'])) {
                $settings['show_meta_in_order'] = true;
            } else {
                $settings['show_meta_in_order'] = false;
            }
            if (isset($_POST['form_loading_order_by_date'])) {
                $settings['form_loading_order_by_date'] = true;
            } else {
                $settings['form_loading_order_by_date'] = false;
            }
            if (isset($_POST['hide_empty_data'])) {
                $settings['hide_empty_data'] = true;
            } else {
                $settings['hide_empty_data'] = false;
            }


            update_option(WCPA_SETTINGS_KEY, $settings);
            $ml = new WCPA_Ml();
            if ($ml->is_active()) {
                $ml->settings_to_wpml();
            }
        }
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

}
