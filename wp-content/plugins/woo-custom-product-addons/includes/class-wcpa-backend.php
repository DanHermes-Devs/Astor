<?php

if (!defined('ABSPATH'))
    exit;

class WCPA_Backend extends WCPA_Order_Meta
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

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.0.0')
    {
        $this->_version = $version;
        $this->_token = WCPA_TOKEN;
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        register_activation_hook($this->file, array($this, 'install'));
        add_action('upgrader_process_complete', array($this, 'upgrader_process_complete'), 10, 2);

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'), 10, 1);

        add_filter('woocommerce_order_item_display_meta_value', array($this, 'display_meta_value'), 10, 3);

        add_filter('post_row_actions', array($this, 'post_row_actions'), 10, 2);
        add_action('wp_ajax_wcpa_duplicate_form', array($this, 'duplicate_form'));

        add_action('woocommerce_before_order_itemmeta', array($this, 'order_item_line_item_html'), 10, 3);

        add_action('woocommerce_before_save_order_items', array($this, 'before_save_order_items'), 10, 2);

        add_action('woocommerce_order_item_get_formatted_meta_data', array($this, 'order_item_get_formatted_meta_data'), 10, 2);

        add_filter('manage_product_posts_columns', array($this, 'manage_products_columns'), 20, 1);
        add_action('manage_product_posts_custom_column', array($this, 'manage_products_column'), 10, 2);

        add_filter("manage_taxonomies_for_" . WCPA_POST_TYPE . "_columns", array($this, 'manage_taxonomies_for_list'), 10, 2);


        add_action('save_post', array($this, 'delete_transient'), 1);
        add_action('edited_term', array($this, 'delete_transient'));
        add_action('delete_term', array($this, 'delete_transient'));
        add_action('created_term', array($this, 'delete_transient'));


        WCPA_Form_Editor::instance();
        WCPA_Product_Meta::instance();
        WCPA_Settings::instance();
    }


    public function delete_transient($arg = false)
    {
        if ($arg) {
            in_array(get_post_type($arg), ['product', WCPA_POST_TYPE]) && delete_transient(WCPA_PRODUCTS_TRANSIENT_KEY);
        } else {
            delete_transient(WCPA_PRODUCTS_TRANSIENT_KEY);
        }

    }

    /**
     * Handling duplicate ajax action
     * @access  public
     * @since   4.3.3
     * @return  string
     */
    public function duplicate_form()
    {

        // Check the nonce
        check_ajax_referer('wcpa_duplicate_form', 'wcpa_nonce');

        // Get variables
        $original_id = sanitize_text_field($_POST['original_id']);


        global $wpdb;


        $_duplicate = get_post($original_id);

        if (!isset($_duplicate->post_type) || $_duplicate->post_type !== WCPA_POST_TYPE) {
            return false;
        }


        $duplicate['post_title'] = $_duplicate->post_title . ' ' . __('Copy', 'wcpa-text-domain');
        $duplicate['post_type'] = WCPA_POST_TYPE;

        $duplicate_id = wp_insert_post($duplicate);

        //  $custom_fields = get_post_custom($original_id);
        $settings = get_post_meta($original_id, WCPA_META_SETTINGS_KEY, true);
        update_post_meta($duplicate_id, WCPA_META_SETTINGS_KEY, $settings);

        $fb_meta = get_post_meta($original_id, WCPA_FORM_META_KEY, true);
        $fb_meta_obj = json_decode($fb_meta);
        $old_id = array();
        if ($fb_meta_obj && is_array($fb_meta_obj)) {
            foreach ($fb_meta_obj as $v) {
                if (isset($v->elementId)) {
                    $_tmp = $v->elementId;
                    $v->elementId = 'wcpa-' . sanitize_title($v->type) . '-' . uniqid();
                    $old_id[$_tmp] = $v->elementId;
                    //replace id in relation
                }
                if (isset($v->name)) {
                    $v->name = sanitize_title($v->type) . '-' . uniqid();
                }
            }
            foreach ($fb_meta_obj as $k => $v) {
                if (isset($v->relations) && is_array($v->relations)) {
                    foreach ($v->relations as $rel) {
                        if (isset($rel->rules) && is_array($rel->rules)) {
                            foreach ($rel->rules as $rul) {
                                if (isset($rul->rules->cl_field) && isset($old_id[$rul->rules->cl_field])) {
                                    $rul->rules->cl_field = $old_id[$rul->rules->cl_field];
                                }
                            }
                        }
                    }
                }
            }
        }

        update_post_meta($duplicate_id, WCPA_FORM_META_KEY, wp_slash(json_encode($fb_meta_obj)));


        echo $duplicate_id;

        wp_die();
    }

    /**
     * Add duplicate form link
     * @access  public
     * @since   4.3.3
     * @return  string
     */
    public function post_row_actions($actions, $post)
    {
        // Check for your post type.
        if ($post->post_type == WCPA_POST_TYPE) {
            $ml = new WCPA_Ml();
            if ($ml->is_active() && !$ml->is_default_lan()) {
                return $actions;
            }
            $label = __('Duplicate Form', 'wcpa-text-domain');

            // Create a nonce & add an action
            $nonce = wp_create_nonce('wcpa_duplicate_form');
            $actions['duplicate_wcpa'] = '<a class="wcpa_duplicate_form" data-nonce="' . $nonce . '" href="#" data-postid="' . $post->ID . '">' . $label . '</a>';
        }


        return $actions;
    }

    /**
     *    Create post type forms
     */
    public function register_type_forms()
    {

        $post_type = WCPA_POST_TYPE;
        $labels = array(
            'name' => 'Product Form',
            'singular_name' => 'Product Form',
            'name_admin_bar' => 'WCPA_Form',
            'add_new' => _x('Add New Form', $post_type, 'woo-custom-product-addons'),
            'add_new_item' => sprintf(__('Add New %s', 'woo-custom-product-addons'), 'Form'),
            'edit_item' => sprintf(__('Edit %s', 'woo-custom-product-addons'), 'Form'),
            'new_item' => sprintf(__('New %s', 'woo-custom-product-addons'), 'Form'),
            'all_items' => sprintf(__('Custom Product Addons', 'woo-custom-product-addons'), 'Form'),
            'view_item' => sprintf(__('View %s', 'woo-custom-product-addons'), 'Form'),
            'search_items' => sprintf(__('Search %s', 'woo-custom-product-addons'), 'Form'),
            'not_found' => sprintf(__('No %s Found', 'woo-custom-product-addons'), 'Form'),
            'not_found_in_trash' => sprintf(__('No %s Found In Trash', 'woo-custom-product-addons'), 'Form'),
            'parent_item_colon' => sprintf(__('Parent %s'), 'Form'),
            'menu_name' => 'Custome Product Options'
        );

        $args = array(
            'labels' => apply_filters($post_type . '_labels', $labels),
            'description' => '',
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=product',
            'show_in_nav_menus' => false,
            'query_var' => false,
            'can_export' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'rest_base' => $post_type,
            'hierarchical' => false,
            'show_in_rest' => false,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => array('title'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post',
            'taxonomies' => array('product_cat')
        );

        register_post_type($post_type, apply_filters($post_type . '_register_args', $args, $post_type));
    }

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles($hook = '')
    {
        wp_register_style($this->_token . '-admin', esc_url($this->assets_url) . 'css/admin.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-admin');
    }

// End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts($hook = '')
    {

        wp_register_script($this->_token . '-backend', esc_url($this->assets_url) . 'js/backend.js', array('jquery'), $this->_version);
        wp_register_script($this->_token . '-form-builder', esc_url($this->assets_url) . 'js/form-builder.min.js', array('jquery'), $this->_version);
        wp_enqueue_script('jquery');

        $screen = get_current_screen();
        if (isset($screen->id) && $screen->id === 'wcpa_pt_forms') {
            wp_enqueue_script($this->_token . '-form-builder');
        }
        wp_enqueue_script($this->_token . '-backend');
        require_once ('translations.php');
        wp_localize_script($this->_token . '-form-builder', 'form_builder_i18n', $translations);
    }

    public function manage_taxonomies_for_list($tax, $post_type)
    {
        $taxonomies = get_object_taxonomies($post_type, 'object');
        $taxonomies = wp_filter_object_list($taxonomies, array(), 'and', 'name');
        return $taxonomies;
    }

    public function manage_products_columns($columns)
    {

        $new = array_merge(array_slice($columns, 0, -2, true), ['wcpa_forms' => __('Product Forms', 'woo-custom-product-addons')], array_slice($columns, -2, null, true));

        return $new;
    }

    public function manage_products_column($column_name, $post_id)
    {
        if ($column_name == 'wcpa_forms') {
            $forms = get_post_meta($post_id, WCPA_PRODUCT_META_KEY, true);
            $link = '';
            if (is_array($forms)) {
                foreach ($forms as $v) {
                    $link .= '<a href="' . get_edit_post_link($v) . '">' . get_the_title($v) . '</a>, ';
                }
            }
            echo trim($link, ', ');
        }
    }

    public function upgrader_process_complete($upgrader_object, $options)
    {
        // The path to our plugin's main file
        $our_plugin = plugin_basename($this->file);
        // If an update has taken place and the updated type is plugins and the plugins element exists
        if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
            // Iterate through the plugins being updated and check if ours is there
            foreach ($options['plugins'] as $plugin) {
                if ($plugin == $our_plugin) {
                    $migration = new WCPA_Migration();
                    if ($migration->check_has_to_migrate()) {
                        $response = $migration->version_migration();
                    }
                }
            }
        }
    }

    public function forms_table_content($column_name, $post_id)
    {
        if ($column_name == 'category') {
            $categories = wp_get_post_terms($post_id, 'product_cat', array('fields' => 'names'));
            echo(implode(', ', $categories));
        }
    }

    static function view($view, $data = array())
    {
        extract($data);
        include(plugin_dir_path(__FILE__) . 'views/' . $view . '.php');
    }

    /**
     *
     *
     * Ensures only one instance of WCPA is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main WCPA instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
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

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install()
    {
        $this->_log_version_number();
        $this->_update_settings();
        $migration = new WCPA_Migration();
        if ($migration->check_has_to_migrate()) {
            $response = $migration->version_migration();
        }

        $this->_protect_upload_dir();
    }

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number()
    {
        update_option($this->_token . '_version', $this->_version);
    }

    private function _update_settings()
    {
        $settings = get_option(WCPA_SETTINGS_KEY, array());
        if (!isset($settings['show_meta_in_cart'])) {
            $settings['show_meta_in_cart'] = true;
        }
        if (!isset($settings['show_meta_in_checkout'])) {
            $settings['show_meta_in_checkout'] = true;
        }
        if (!isset($settings['show_meta_in_order'])) {
            $settings['show_meta_in_order'] = true;
        }
        if (!isset($settings['form_loading_order_by_date'])) {
            $count_posts = wp_count_posts(array('post_type' => WCPA_POST_TYPE));
            if ($count_posts > 1) {
                $settings['form_loading_order_by_date'] = false;
            } else {
                $settings['form_loading_order_by_date'] = true;
            }
        }

        if (!isset($settings['hide_empty_data'])) {
            $count_posts = wp_count_posts(array('post_type' => WCPA_POST_TYPE));
            if ($count_posts > 1) {
                $settings['hide_empty_data'] = false;
            } else {
                $settings['hide_empty_data'] = true;
            }
        }

        update_option(WCPA_SETTINGS_KEY, $settings);
    }

    private function _protect_upload_dir()
    {
        $upload_dir = wp_upload_dir();

        $files = array(
            array(
                'base' => $upload_dir['basedir'] . '/' . WCPA_UPLOAD_DIR,
                'file' => '.htaccess',
                'content' => 'Options -Indexes' . "\n"
                    . ''
            )
        ,
            array(
                'base' => $upload_dir['basedir'] . '/' . WCPA_UPLOAD_DIR,
                'file' => 'index.php',
                'content' => '<?php ' . "\n"
                    . '// Silence is golden.'
            )
        );

        foreach ($files as $file) {


            if ((wp_mkdir_p($file['base'])) && (!file_exists(trailingslashit($file['base']) . $file['file']))  // If file not exist
            ) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

}
