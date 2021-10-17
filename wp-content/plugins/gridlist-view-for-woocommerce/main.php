<?php
define( "BeRocket_List_Grid_domain", 'BeRocket_LGV_domain'); 
define( "List_Grid_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain('BeRocket_LGV_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once(plugin_dir_path( __FILE__ ).'berocket/framework.php');
foreach (glob(__DIR__ . "/includes/*.php") as $filename)
{
    include_once($filename);
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class BeRocket_LGV extends BeRocket_Framework {
    public static $settings_name = 'br-list_grid-options';
    protected static $instance;
    public static $br_lgv_cookie_defaults = array('default', 'default');
    protected $check_init_array = array(
        array(
            'check' => 'woocommerce_version',
            'data' => array(
                'version' => '3.0',
                'operator' => '>=',
                'notice'   => 'Plugin Grid/List View for WooCommerce required WooCommerce version 3.0 or higher'
            )
        ),
        array(
            'check' => 'framework_version',
            'data' => array(
                'version' => '2.1',
                'operator' => '>=',
                'notice'   => 'Please update all BeRocket plugins to the most recent version. Grid/List View for WooCommerce is not working correctly with older versions.'
            )
        ),
    );
    function __construct () {
        $this->info = array(
            'id'          => 2,
            'lic_id'      => 47,
            'version'     => BeRocket_List_Grid_version,
            'plugin'      => '',
            'slug'        => '',
            'key'         => '',
            'name'        => '',
            'plugin_name' => 'list_grid',
            'full_name'   => __('Grid/List View for WooCommerce', 'BeRocket_LGV_domain'),
            'norm_name'   => __('Grid/List View', 'BeRocket_LGV_domain'),
            'price'       => '',
            'domain'      => 'BeRocket_LGV_domain',
            'templates'   => List_Grid_TEMPLATE_PATH,
            'plugin_file' => BeRocket_List_Grid_file,
            'plugin_dir'  => __DIR__,
        );
        $this->defaults = array(
            'buttons_page'    => array(
                'default_style'                 => 'grid',
                'mobile_default_style'          => 'grid',
                'max_mobile_width'              => '768',
                'disable_fa'                    => '',
                'fontawesome_frontend_disable'  => '',
                'fontawesome_frontend_version'  => '',
                'custom_class'                  => '',
                'above_order'                   => '',
                'under_order'                   => '1',
                'above_paging'                  => '',
                'position'                      => 'left',
                'padding'                       => array(
                    'top'                           => '5',
                    'bottom'                        => '5',
                    'left'                          => '0',
                    'right'                         => '0',
                ),
            ),
            'product_count'   => array(
                'use'                           => '1',
                'custom_class'                  => '',
                'products_per_page'             => '24',
                'value'                         => '12,24,48,all',
                'explode'                       => '/',
                'text_before'                   => '',
                'text_after'                    => '',
                'above_order'                   => array( 'is' => '', 'after' => '' ),
                'under_order'                   => array( 'is' => '', 'after' => '' ),
                'above_paging'                  => array( 'is' => '', 'after' => '' ),
                'before_grid_list'              => '1',
                'after_grid_list'               => '',
                'position'                      => 'left',
            ),
            'liststyle'         => array(),
            'custom_css'        => '',
            'javascript'        => array(
                'before_style_set'              => '',
                'after_style_set'               => '',
                'after_style_list'              => '',
                'after_style_grid'              => '',
                'before_get_cookie'             => '',
                'after_get_cookie'              => '',
                'before_buttons_reselect'       => '',
                'after_buttons_reselect'        => '',
                'before_product_reselect'       => '',
                'after_product_reselect'        => '',
                'before_page_reload'            => '',
                'before_ajax_product_reload'    => '',
                'after_ajax_product_reload'     => '',
            ),
            'fontawesome_frontend_disable'    => '',
            'fontawesome_frontend_version'    => '',
        );
        $this->values = array(
            'settings_name' => 'br-list_grid-options',
            'option_page'   => 'br-list_grid',
            'premium_slug'  => 'woocommerce-grid-list-view',
            'free_slug'     => 'gridlist-view-for-woocommerce',
        );
        $this->feature_list = array();
        $this->framework_data['fontawesome_frontend'] = true;
        parent::__construct( $this );
        if( $this->check_framework_version() ) {
            if ( $this->init_validation() ) {
                $options = $this->get_option();
                add_action ( "widgets_init", array( $this, 'widgets_init' ) );
                add_action ( 'woocommerce_after_shop_loop_item', array( $this, 'additional_product_data' ), 99999 );
                add_action ( 'br_before_preview_box', array( $this, 'remove_hooks' ) );
                add_action ( 'br_after_preview_box', array( $this, 'add_hooks' ) );
                add_filter ( 'post_class', array( $this, 'post_class' ), 9999, 3 );
                add_shortcode( 'br_grid_list', array( $this, 'shortcode' ) );
                add_action ( "wp", array( $this, 'wp' ) );
                add_action ( "admin_init", array( $this, 'wp' ) );
                //AJAX Compatibility
                add_filter('brfr_data_ajax_filters', array($this, 'data_ajax_filters'), 999);
                add_filter('brfr_get_option_ajax_filters', array($this, 'remove_product_per_page'), 999);
                //Load More Compatibility
                add_filter('brfr_data_BeRocket_LMP', array($this, 'data_BeRocket_LMP'), 999);
                add_filter('brfr_get_option_BeRocket_LMP', array($this, 'remove_product_per_page_load_more'), 999);
                // INIT COOKIES TO PREVENT ERRORS
                br_lgv_get_cookie ( 0, true );
            }
        } else {
            add_filter( 'berocket_display_additional_notices', array(
                $this,
                'old_framework_notice'
            ) );
        }
    }
    public function widgets_init() {
        register_widget("berocket_lgv_widget");
    }
    public function shortcode( $atts = array() ) {
        ob_start();
        the_widget( 'berocket_lgv_widget', $atts );
        $return = ob_get_clean();
        return $return;
    }
    function init_validation() {
        return parent::init_validation() && $this->check_framework_version();
    }
    function check_framework_version() {
        return ( ! empty(BeRocket_Framework::$framework_version) && version_compare(BeRocket_Framework::$framework_version, 2.1, '>=') );
    }
    function old_framework_notice($notices) {
        $notices[] = array(
            'start'         => 0,
            'end'           => 0,
            'name'          => $this->info[ 'plugin_name' ].'_old_framework',
            'html'          => __('<strong>Please update all BeRocket plugins to the most recent version. Grid/List View for WooCommerce is not working correctly with older versions.</strong>', 'BeRocket_LGV_domain'),
            'righthtml'     => '',
            'rightwidth'    => 0,
            'nothankswidth' => 0,
            'contentwidth'  => 1600,
            'subscribe'     => false,
            'priority'      => 10,
            'height'        => 50,
            'repeat'        => false,
            'repeatcount'   => 1,
            'image'         => array(
                'local'  => '',
                'width'  => 0,
                'height' => 0,
                'scale'  => 1,
            )
        );
        return $notices;
    }
    public function wp () {
        global $berocket_hide_grid_list_buttons, $wp_query;
        $berocket_hide_grid_list_buttons = false;
        $options        = $this->get_option();
        $lgv_options    = $options['buttons_page'];
        $lgv_js_options = $options['javascript'];
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $page_id = apply_filters( 'wpml_object_id', ( isset($wp_query->queried_object->ID) ? $wp_query->queried_object->ID : '' ), 'page', true, $default_language );
        $styles_on_page = array('pages' => false, 'mobile_pages' => false, 'desktop_pages' => false);
        foreach($styles_on_page as $style_on_device => $style_on_page) {
            if( isset($lgv_options[$style_on_device]) && is_array($lgv_options[$style_on_device]) && 
                (
                    ( isset($page_id) && array_key_exists($page_id, $lgv_options[$style_on_device]) ) ||
                    ( is_shop() && array_key_exists('shop', $lgv_options[$style_on_device]) ) ||
                    ( is_product_category() && array_key_exists('category', $lgv_options[$style_on_device]) ) ||
                    ( is_product() && array_key_exists('product', $lgv_options[$style_on_device]) ) ||
                    ( is_front_page() && array_key_exists('home', $lgv_options[$style_on_device]) )
                    
                )
            ) {
                if( $style_on_device == 'pages' ) {
                    $berocket_hide_grid_list_buttons = true;
                }
                $styles_on_page[$style_on_device] = 'grid';
                if( is_shop() ) {
                    $styles_on_page[$style_on_device] = $lgv_options[$style_on_device]['shop'];
                } elseif( is_product_category() ) {
                    $styles_on_page[$style_on_device] = $lgv_options[$style_on_device]['category'];
                } elseif( is_product() ) {
                    $styles_on_page[$style_on_device] = $lgv_options[$style_on_device]['product'];
                } elseif( is_front_page() ) {
                    $styles_on_page[$style_on_device] = $lgv_options[$style_on_device]['home'];
                } else {
                    $styles_on_page[$style_on_device] = $lgv_options[$style_on_device][$page_id];
                }
            }
        }
        wp_enqueue_script("jquery");
        wp_enqueue_script( 'berocket_jquery_cookie', plugins_url( 'js/jquery.cookie.js', __FILE__ ), array( 'jquery' ), BeRocket_List_Grid_version );
        wp_enqueue_script( 'berocket_lgv_grid_list', plugins_url( 'js/grid_view.js', __FILE__ ), array( 'jquery', 'berocket_jquery_cookie' ), BeRocket_List_Grid_version );
        wp_localize_script(
            'berocket_lgv_grid_list',
            'lgv_options',
            array( 
                'default_style' => $lgv_options['default_style'],
                'mobile_default_style' => $lgv_options['mobile_default_style'],
                'max_mobile_width' => $lgv_options['max_mobile_width'],
                'user_func' => apply_filters( 'berocket_lgv_user_func', $lgv_js_options ),
                'style_on_pages' => $styles_on_page,
            )
        );
    }
    public function init () {
        parent::init();
        wp_register_style( 'berocket_lgv_style', plugins_url( 'css/shop_lgv.css', __FILE__ ), "", BeRocket_List_Grid_version );
        wp_enqueue_style( 'berocket_lgv_style' );
        $options        = $this->get_option();
        $lgv_options    = $options['buttons_page'];
        br_lgv_get_cookie( 0, true );
        $lgv_options_pc = $options['product_count'];
        if( ! empty($lgv_options['above_order']) ) {
            add_action ( 'woocommerce_before_shop_loop', array($this, 'show_buttons_fix'), 3 );
        }
        if( ! empty($lgv_options['under_order']) ) {
            add_action ( 'woocommerce_before_shop_loop', array($this, 'show_buttons_fix'), 100 );
        }
        if( ! empty($lgv_options['above_paging']) ) {
            add_action ( 'woocommerce_after_shop_loop', array($this, 'show_buttons_fix'), 3 );
        }
        if( ! empty($lgv_options_pc['above_order']['is']) ) {
            add_action ( 'woocommerce_before_shop_loop', array($this, 'show_product_count_fix'), ( @ $lgv_options_pc['above_order']['after'] ? 4 : 2 ) );
        }
        if( ! empty($lgv_options_pc['under_order']['is']) ) {
            add_action ( 'woocommerce_before_shop_loop', array($this, 'show_product_count_fix'), ( @ $lgv_options_pc['under_order']['after'] ? 101 : 99 ) );
        }
        if( ! empty($lgv_options_pc['above_paging']['is']) ) {
            add_action ( 'woocommerce_after_shop_loop', array($this, 'show_product_count_fix'), ( @ $lgv_options_pc['above_paging']['after'] ? 4 : 2 ) );
        }
        if( ! empty($lgv_options_pc['before_grid_list']) ) {
            add_action ( 'br_lgv_before_list_grid_buttons', array($this, 'show_product_count'), 20 );
        }
        if( ! empty($lgv_options_pc['after_grid_list']) ) {
            add_action ( 'br_lgv_after_list_grid_buttons', array($this, 'show_product_count'), 20 );
        }
        if ( ! empty($lgv_options_pc['use']) || ! empty($lgv_options_pc['products_per_page']) ) {
            add_filter( 'loop_shop_per_page', array($this, 'set_products_per_page'), 9999999999 );
            add_action( 'pre_get_posts', array($this, 'set_pre_get_posts'), 9999999999 );
            add_action( 'woocommerce_shortcode_products_query', array($this, 'shortcode_products_query'), 9999999999 );
        }
    }

    public function set_products_per_page ($count) {
        $options        = $this->get_option();
        $lgv_options_pc = $options['product_count'];
        if ( ! empty($lgv_options_pc['use']) ) {
            $product_count_per_page = br_lgv_get_cookie( 1 );
            if( (int)$product_count_per_page ) {
                return $product_count_per_page;
            } elseif ( $product_count_per_page == 'all' ) {
                return apply_filters('berocket_grid_list_product_count_all', 400);
            } elseif ( ! empty($lgv_options_pc['products_per_page']) ) {
                return $lgv_options_pc['products_per_page'];
            }
        } elseif ( ! empty($lgv_options_pc['products_per_page']) ) {
            return $lgv_options_pc['products_per_page'];
        }
        return $count;
    }

    public function set_pre_get_posts($query) {
        if( ! is_admin() && $query->is_main_query() && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) && (is_post_type_archive( 'product' ) || is_product_category()) ) {
            $count = $this->set_products_per_page(false);
            if( $count !== false ) {
                $query->set('posts_per_page', $count);
            }
        }
    }
    public function shortcode_products_query($query_vars) {
        $count = $this->set_products_per_page(false);
        if( $count !== false ) {
            $query_vars['posts_per_page'] = $count;
        }
        return $query_vars;
    }
    /**
     * Function display Grid/List buttons
     *
     * @access public
     *
     * @return void
     */
    public function show_buttons_fix() {
        $options     = $this->get_option();
        $lgv_options = $options['buttons_page'];
        echo '<div style="clear:both;"></div>';
        set_query_var( 'title', '' );
        set_query_var( 'position', apply_filters( 'lgv_buttons_position', (empty($lgv_options['position']) ? '' : $lgv_options['position'] ) ) );
        set_query_var( 'padding', apply_filters( 'lgv_buttons_padding', (empty($lgv_options['padding']) ? '' : $lgv_options['padding'] ) ) );
        set_query_var( 'custom_class', apply_filters( 'lgv_buttons_custom_class', (empty($lgv_options['custom_class']) ? '' : $lgv_options['custom_class'] ) ) );
        $this->br_get_template_part( apply_filters( 'lgv_buttons_template', 'list-grid' ) );
        echo '<div style="clear:both;"></div>';
    }
    /**
     * Function display product count links
     *
     * @access public
     *
     * @return void
     */
    public function show_product_count() {
        $options        = $this->get_option();
        $lgv_options_pc = $options['product_count'];
        if ( ! empty($lgv_options_pc['use']) ) {
            set_query_var( 'options', apply_filters( 'lgv_product_count_lgv_options_pc', $lgv_options_pc ) );
            set_query_var( 'position', apply_filters( 'lgv_product_count_position', '' ) );
            set_query_var( 'custom_class', apply_filters( 'lgv_product_count_custom_class', (empty($lgv_options_pc['custom_class']) ? '' : $lgv_options_pc['custom_class'] ) ) );
            $this->br_get_template_part( apply_filters( 'lgv_product_count_template', 'product_count' ) );
        }
    }
    /**
     * Function display product count links with clear fix divs before and after
     *
     * @access public
     *
     * @return void
     */
    public function show_product_count_fix() {
        $options        = $this->get_option();
        $lgv_options_pc = $options['product_count'];
        if ( ! empty($lgv_options_pc['use']) ) {
            echo '<div style="clear:both;"></div>';
            set_query_var( 'position', apply_filters( 'lgv_product_count_position', (empty($lgv_options_pc['position']) ? '' : $lgv_options_pc['position'] ) ) );
            set_query_var( 'custom_class', apply_filters( 'lgv_product_count_custom_class', (empty($lgv_options_pc['custom_class']) ? '' : $lgv_options_pc['custom_class'] ) ) );
            set_query_var( 'options', $lgv_options_pc );
            $this->br_get_template_part( apply_filters( 'lgv_product_count_template', 'product_count' ) );
            echo '<div style="clear:both;"></div>';
        }
    }
    /**
     * Filter for add additional class to products in shop
     *
     * @param array $classes array with classes
     *
     * @return array
     */
    public function post_class ( $classes, $class, $post_id ) {
        if ( ! is_admin() || (defined('DOING_AJAX') && DOING_AJAX) ) {
            $post_type = get_post_type( $post_id );
            $is_add = true;
            if( is_product() ) {
                global $wp_query;
                $check_post = get_post($post_id);
                if( !empty($wp_query->queried_object_id) && $wp_query->queried_object_id == $post_id ) {
                    $is_add = false;
                }
            }
            if ( $post_type == 'product' && $is_add ) {
                $product_style = br_lgv_get_cookie ( 0 );
                if ( $product_style == 'list' ) {
                    $classes[] = 'berocket_lgv_list';
                } else {
                    $classes[] = 'berocket_lgv_grid';
                }
                $classes[] = 'berocket_lgv_list_grid';
                $classes = apply_filters( 'lgv_product_classes', $classes );
            }
        }
        return $classes;
    }
    /**
     * Function add inside product additional data
     *
     * @return void
     */
    public function additional_product_data() {
        if ( ! is_admin() || (defined('DOING_AJAX') && DOING_AJAX) ) {
            $post_id = get_the_ID();
            $post_type = get_post_type( $post_id );
            $is_add = true;
            if( is_product() ) {
                global $wp_query;
                $check_post = get_post($post_id);
                if( ! empty($wp_query->queried_object_id) && $wp_query->queried_object_id == $post_id ) {
                    $is_add = false;
                }
            }
            if ( $post_type == 'product' && $is_add ) {
                $this->remove_hooks();
                remove_action ( 'br_after_preview_box', array( $this, 'add_hooks' ) );
                $template = 'additional_product_data';
                $this->br_get_template_part( apply_filters( 'lgv_product_data_template', $template ) );
                $this->add_hooks();
                add_action ( 'br_after_preview_box', array( $this, 'add_hooks' ) );
            }
        }
    }
    public function remove_hooks() {
        remove_action ( 'woocommerce_after_shop_loop_item', array( $this, 'additional_product_data' ), 99999 );
    }
    
    public function add_hooks() {
        add_action ( 'woocommerce_after_shop_loop_item', array( $this, 'additional_product_data' ), 99999 );
    }
    public function admin_init () {
        parent::admin_init();
        if( ! empty($_GET['page']) && $_GET['page'] == $this->values[ 'option_page' ] ) {
            wp_enqueue_script("jquery");
            wp_enqueue_script( 'berocket_jquery_cookie', plugins_url( 'js/jquery.cookie.js', __FILE__ ), array( 'jquery' ), BeRocket_List_Grid_version );
            wp_enqueue_script( 'berocket_lgv_grid_list', plugins_url( 'js/grid_view.js', __FILE__ ), array( 'jquery', 'berocket_jquery_cookie' ), BeRocket_List_Grid_version );
            wp_register_style( 'berocket_lgv_style', plugins_url( 'css/shop_lgv.css', __FILE__ ), "", BeRocket_List_Grid_version );
            wp_enqueue_script( 'berocket_lgv_admin', plugins_url( 'js/admin_lgv.js', __FILE__ ), array( 'jquery' ), BeRocket_List_Grid_version );
            wp_register_style( 'berocket_lgv_admin_style', plugins_url( 'css/admin_lgv.css', __FILE__ ), "", BeRocket_List_Grid_version );
            wp_enqueue_style( 'berocket_lgv_admin_style' );
        }
        $this->update_from_not_framework();
    }
    public function update_from_not_framework() {
        $update_option = false;
        $options = $this->get_option();
        $settings_list = array('buttons_page', 'product_count', 'liststyle');
        foreach($settings_list as $setting_list) {
            $settings = get_option('br_lgv_'.$setting_list.'_option');
            if( ! empty($settings) && is_array($settings) ) {
                $update_option = true;
                $options[$setting_list] = $settings;
                delete_option('br_lgv_'.$setting_list.'_option');
            }
        }
        if($update_option) {
            $br_lgv_css_option = get_option('br_lgv_css_option');
            $options['custom_css'] = br_get_value_from_array($br_lgv_css_option, array('css_style'));
            $br_lgv_javascript_option = get_option('br_lgv_javascript_option');
            $options['javascript'] = br_get_value_from_array($br_lgv_javascript_option, array('script'), array());
            if( ! is_array($options['javascript']) ) {
                $options['javascript'] = array();
            }
            $options = $this->recursive_array_set( $this->defaults, $options );
            update_option($this->values[ 'settings_name' ], $options);
        }
    }
    public function set_styles () {
        $options = $this->get_option();
        $lgv_options = $options['buttons_page'];
        $lgv_pc_options = $options['product_count'];
        $lgv_ls_options = $options['liststyle'];
        $lgv_css_style = berocket_isset($options['custom_css']);
        $lgv_css_style = br_lgv_css_replace( $lgv_css_style );
        ?>
        <style>
            <?php echo $lgv_css_style; ?>
            <?php if ( empty($lgv_options['custom_class']) ) { 
                if( ! empty($lgv_options['button_style']['normal'])) {?>
                div.berocket_lgv_widget a.berocket_lgv_button{
                    <?php echo $lgv_options['button_style']['normal'] ?>
                }
                <?php } if( ! empty($lgv_options['button_style']['hover'])) { ?>
                div.berocket_lgv_widget a.berocket_lgv_button:hover{
                    <?php echo $lgv_options['button_style']['hover'] ?>
                }
                <?php } if( ! empty($lgv_options['button_style']['selected'])) { ?>
                div.berocket_lgv_widget a.berocket_lgv_button.selected{
                    <?php echo $lgv_options['button_style']['selected'] ?>
                }
            <?php }
            } if ( empty($lgv_pc_options['custom_class']) ) { 
                if( ! empty($lgv_pc_options['button_style']['normal'])) { ?>
                .br_lgv_product_count_block a.br_lgv_product_count{
                    <?php echo $lgv_pc_options['button_style']['normal'] ?>
                }
                <?php } if( ! empty($lgv_pc_options['button_style']['hover'])) { ?>
                .br_lgv_product_count_block a.br_lgv_product_count:hover{
                    <?php echo $lgv_pc_options['button_style']['hover'] ?>
                }
                <?php } if( ! empty($lgv_pc_options['button_style']['selected'])) { ?>
                .br_lgv_product_count_block a.br_lgv_product_count.selected{
                    <?php echo $lgv_pc_options['button_style']['selected'] ?>
                }
            <?php }
            } 
            if( ! empty($lgv_pc_options['button_style']['split'])) { ?>
            .br_lgv_product_count_block span.br_lgv_product_count{
                <?php echo $lgv_pc_options['button_style']['split'] ?>
            }
            <?php } if( ! empty($lgv_pc_options['button_style']['text'])) { ?>
            .br_lgv_product_count_block span.br_lgv_product_count.text{
                <?php echo $lgv_pc_options['button_style']['text'] ?>
            }
            <?php
            }
            $added = array();
            $remove = array();
            if ( ! empty($lgv_ls_options['button_style']) && is_array( $lgv_ls_options['button_style'] ) ) {
                foreach( $lgv_ls_options['button_style'] as $ls_style ) {
                    if ( ! empty($lgv_ls_options['button'][$ls_style['button']]['custom_class']) ) {
                        $remove[] = $ls_style['button'];
                    }
                }
            }
            if ( ! empty($lgv_ls_options['button_style']) && is_array( $lgv_ls_options['button_style'] ) ) {
                foreach( $lgv_ls_options['button_style'] as $ls_style ) {
                    if ( ! in_array( $ls_style['button'], $remove ) && ! in_array( $ls_style['button'].$ls_style['modifier'], $added ) ) {
                        echo '.woocommerce ul.products .berocket_lgv_additional_data .'.$ls_style['button'].$ls_style['modifier'].' ,div.berocket_lgv_additional_data .'.$ls_style['button'].$ls_style['modifier'].'{
                            '.$ls_style['style'].'
                        }';
                        $added[] = $ls_style['button'].$ls_style['modifier'];
                    }
                }
            }
            ?>
        </style>
        <?php
    }
    public function admin_settings( $tabs_info = array(), $data = array() ) {
        parent::admin_settings(
            array(
                'Buttons' => array(
                    'icon' => 'square',
                ),
                'Product Count' => array(
                    'icon' => 'list-ol',
                ),
                'Custom CSS/JavaScript' => array(
                    'icon' => 'css3'
                ),
                'License' => array(
                    'icon' => 'unlock-alt',
                    'link' => admin_url( 'admin.php?page=berocket_account' )
                ),
            ),
            array(
            'Buttons' => array(
                'default_style' => array(
                    "label"    => __( 'Default style', "BeRocket_LGV_domain" ),
                    "name"     => array("buttons_page", "default_style"),
                    "type"     => "selectbox",
                    "options"  => array(
                        array('value' => 'grid', 'text' => __('Grid', 'BeRocket_LGV_domain')),
                        array('value' => 'list', 'text' => __('List', 'BeRocket_LGV_domain')),
                    ),
                    "value"    => '',
                ),
                'mobile_default_style' => array(
                    "label"    => __( 'Mobile default style', "BeRocket_LGV_domain" ),
                    "name"     => array("buttons_page", "mobile_default_style"),
                    "type"     => "selectbox",
                    "options"  => array(
                        array('value' => 'grid', 'text' => __('Grid', 'BeRocket_LGV_domain')),
                        array('value' => 'list', 'text' => __('List', 'BeRocket_LGV_domain')),
                    ),
                    "value"    => '',
                ),
                'fixed_page_style' => array(
                    "label"     => "",
                    "section"   => 'fixed_page_style'
                ),
                'max_mobile_width' => array(
                    "label"     => __('Max mobile width', 'BeRocket_LGV_domain'),
                    "label_for" => ' '.__('px', 'BeRocket_LGV_domain'),
                    "type"      => "number",
                    "name"      => array("buttons_page", "max_mobile_width"),
                    "value"     => '',
                ),
                'custom_class' => array(
                    "label"     => __('Custom class', 'BeRocket_LGV_domain'),
                    "label_for" => ' '.__('If custom class seted options is not used', 'BeRocket_LGV_domain'),
                    "type"      => "text",
                    "name"      => array("buttons_page", "custom_class"),
                    "value"     => '',
                ),
                'buttons_display' => array(
                    "label"     => __('Buttons display', 'BeRocket_LGV_domain'),
                    "items"     => array(
                        array(
                            "label_for" => __('Above order by menu', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("buttons_page", "above_order"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('Under order by menu', 'BeRocket_LGV_domain'),
                            "label_be_for" => '<br>',
                            "type"      => "checkbox",
                            "name"      => array("buttons_page", "under_order"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('Above pagination', 'BeRocket_LGV_domain'),
                            "label_be_for" => '<br>',
                            "type"      => "checkbox",
                            "name"      => array("buttons_page", "above_paging"),
                            "value"     => '1',
                        ),
                    )
                ),
                'position' => array(
                    "label"    => __( 'Buttons position', "BeRocket_LGV_domain" ),
                    "label_for"=> __( 'Grid/List Buttons position on shop page', "BeRocket_LGV_domain" ),
                    "name"     => array("buttons_page", "position"),
                    "type"     => "selectbox",
                    "options"  => array(
                        array('value' => 'left', 'text' => __('Left', 'BeRocket_LGV_domain')),
                        array('value' => 'right', 'text' => __('Right', 'BeRocket_LGV_domain')),
                    ),
                    "value"    => '',
                ),
                'paddings' => array(
                    "label"     => __('Paddings', 'BeRocket_LGV_domain'),
                    "items"     => array(
                        array(
                            "label_for" => __('px', 'BeRocket_LGV_domain'),
                            "label_be_for" => __('Under buttons', 'BeRocket_LGV_domain'),
                            "type"      => "number",
                            "name"      => array("buttons_page", "padding", "top"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('px', 'BeRocket_LGV_domain'),
                            "label_be_for" => '<br>' . __('Above buttons', 'BeRocket_LGV_domain'),
                            "type"      => "number",
                            "name"      => array("buttons_page", "padding", "bottom"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('px', 'BeRocket_LGV_domain'),
                            "label_be_for" => '<br>' . __('Before buttons', 'BeRocket_LGV_domain'),
                            "type"      => "number",
                            "name"      => array("buttons_page", "padding", "left"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('px', 'BeRocket_LGV_domain'),
                            "label_be_for" => '<br>' . __('After buttons', 'BeRocket_LGV_domain'),
                            "type"      => "number",
                            "name"      => array("buttons_page", "padding", "right"),
                            "value"     => '1',
                        ),
                    )
                ),
                'shortcode' => array(
                    "label"     => "",
                    "section"   => 'shortcode'
                ),
                'button_styles' => array(
                    "label"     => "",
                    "tr_class"  => "brlgv_style_block",
                    "section"   => 'button_styles'
                ),
            ),
            'Product Count' => array(
                'use' => array(
                    "label"     => __('Use products count', 'BeRocket_LGV_domain'),
                    "type"      => "checkbox",
                    "name"      => array("product_count", "use"),
                    "value"     => '1',
                ),
                'custom_class' => array(
                    "label"     => __('Custom class for buttons', 'BeRocket_LGV_domain'),
                    "type"      => "text",
                    "name"      => array("product_count", "custom_class"),
                    "value"     => '',
                ),
                'products_per_page' => array(
                    "label"     => __('Default Products Per Page', 'BeRocket_LGV_domain'),
                    "type"      => "number",
                    "name"      => array("product_count", "products_per_page"),
                    "value"     => '',
                ),
                'value' => array(
                    "label"     => __('Product count value', 'BeRocket_LGV_domain'),
                    "label_for" => __('You can use digits and "all"(Example:"12,24,48,all")', 'BeRocket_LGV_domain'). '<br><p class="notice notice-error">' . 
                    __('Maximum products for "all" is 400. If you have more products "all" will be as 400', 'BeRocket_LGV_domain') . '</p>',
                    "type"      => "text",
                    "name"      => array("product_count", "value"),
                    "value"     => '',
                ),
                'explode' => array(
                    "label"     => __('Spliter value', 'BeRocket_LGV_domain'),
                    "class"     => "br_lgv_product_count_spliter",
                    "type"      => "text",
                    "name"      => array("product_count", "explode"),
                    "value"     => '',
                ),
                'paddings' => array(
                    "label"     => __('Text', 'BeRocket_LGV_domain'),
                    "items"     => array(
                        array(
                            "label_be_for" => __('Text before', 'BeRocket_LGV_domain'),
                            "type"      => "text",
                            "name"      => array("product_count", "text_before"),
                            "class"     => "text_before",
                            "value"     => '',
                        ),
                        array(
                            "label_be_for" => '<br>' . __('Text after', 'BeRocket_LGV_domain'),
                            "type"      => "text",
                            "name"      => array("product_count", "text_after"),
                            "class"     => "text_after",
                            "value"     => '',
                        ),
                    )
                ),
                'buttons_display' => array(
                    "label"     => __('Buttons display', 'BeRocket_LGV_domain'),
                    "items"     => array(
                        array(
                            "label_for" => __('Above order by menu', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "above_order", "is"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('Under Grid/List buttons', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "above_order", "after"),
                            "value"     => '1',
                        ),
                        array(
                            "label_be_for" => '<br>',
                            "label_for" => __('Under order by menu', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "under_order", "is"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('Under Grid/List buttons', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "under_order", "after"),
                            "value"     => '1',
                        ),
                        array(
                            "label_be_for" => '<br>',
                            "label_for" => __('Above pagination', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "above_paging", "is"),
                            "value"     => '1',
                        ),
                        array(
                            "label_for" => __('Under Grid/List buttons', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "above_paging", "after"),
                            "value"     => '1',
                        ),
                        array(
                            "label_be_for" => '<br>',
                            "label_for" => __('Before Grid/List buttons', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "before_grid_list"),
                            "value"     => '1',
                        ),
                        array(
                            "label_be_for" => '<br>',
                            "label_for" => __('After Grid/List buttons', 'BeRocket_LGV_domain'),
                            "type"      => "checkbox",
                            "name"      => array("product_count", "after_grid_list"),
                            "value"     => '1',
                        ),
                    )
                ),
                'position' => array(
                    "label"    => __( 'Product count position', "BeRocket_LGV_domain" ),
                    "label_for"=> __( 'Grid/List Product count position on shop page', "BeRocket_LGV_domain" ),
                    "name"     => array("product_count", "position"),
                    "type"     => "selectbox",
                    "options"  => array(
                        array('value' => 'left', 'text' => __('Left', 'BeRocket_LGV_domain')),
                        array('value' => 'right', 'text' => __('Right', 'BeRocket_LGV_domain')),
                    ),
                    "value"    => '',
                ),
            ),
            'Custom CSS/JavaScript' => array(
                'global_font_awesome_disable' => array(
                    "label"     => __( 'Disable Font Awesome', "BeRocket_LGV_domain" ),
                    "type"      => "checkbox",
                    "name"      => "fontawesome_frontend_disable",
                    "value"     => '1',
                    'label_for' => __('Don\'t load Font Awesome css files on site front end. Use it only if you don\'t use Font Awesome icons in widgets or your theme has Font Awesome.', 'BeRocket_LGV_domain'),
                ),
                'global_fontawesome_version' => array(
                    "label"    => __( 'Font Awesome Version', "BeRocket_LGV_domain" ),
                    "name"     => "fontawesome_frontend_version",
                    "type"     => "selectbox",
                    "options"  => array(
                        array('value' => '', 'text' => __('Font Awesome 4', 'BeRocket_LGV_domain')),
                        array('value' => 'fontawesome5', 'text' => __('Font Awesome 5', 'BeRocket_LGV_domain')),
                    ),
                    "value"    => '',
                    "label_for" => __('Version of Font Awesome that will be used on front end. Please select version that you have in your theme', 'BeRocket_LGV_domain'),
                ),
                array(
                    "label"   => __( "Custom CSS", "BeRocket_LGV_domain" ),
                    "name"    => "custom_css",
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'before_style_set' => array(
                    "label"   => __( "JavaScript before list or grid style set", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "before_style_set"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'after_style_set' => array(
                    "label"   => __( "JavaScript after list or grid style set", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "after_style_set"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'after_style_list' => array(
                    "label"   => __( "JavaScript after list style set", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "after_style_list"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'after_style_grid' => array(
                    "label"   => __( "JavaScript after grid style set", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "after_style_grid"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'before_get_cookie' => array(
                    "label"   => __( "JavaScript before cookies get", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "before_get_cookie"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'after_get_cookie' => array(
                    "label"   => __( "JavaScript after cookies get", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "after_get_cookie"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'before_buttons_reselect' => array(
                    "label"   => __( "JavaScript before selected buttons Grid/List changed", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "before_buttons_reselect"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'after_buttons_reselect' => array(
                    "label"   => __( "JavaScript after selected buttons Grid/List changed", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "after_buttons_reselect"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'before_product_reselect' => array(
                    "label"   => __( "JavaScript before selected product count links changed", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "before_product_reselect"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'after_product_reselect' => array(
                    "label"   => __( "JavaScript after selected product count links changed", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "after_product_reselect"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'before_page_reload' => array(
                    "label"   => __( "JavaScript before page reload on product count change", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "before_page_reload"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'before_ajax_product_reload' => array(
                    "label"   => __( "JavaScript before AJAX page reload on product count change", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "before_ajax_product_reload"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                'after_ajax_product_reload' => array(
                    "label"   => __( "JavaScript after AJAX page reload on product count change", "BeRocket_LGV_domain" ),
                    "label_for"=> __( "Works only if WooCommerce AJAX Products Filter installed", "BeRocket_LGV_domain" ),
                    "name"    => array("javascript", "after_ajax_product_reload"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Button Normal Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("buttons_page", "button_style", "normal"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_button_test_normal_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Button Hover Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("buttons_page", "button_style", "hover"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_button_test_hover_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Button Selected Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("buttons_page", "button_style", "selected"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_button_test_selected_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Product Count normal Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("product_count", "button_style", "normal"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_product_count_normal_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Product Count hover Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("product_count", "button_style", "hover"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_product_count_hover_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Product Count selected Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("product_count", "button_style", "selected"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_product_count_selected_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Product Count split Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("product_count", "button_style", "split"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_product_count_split_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
                array(
                    "label"   => __("Product Count text Styles", 'BeRocket_LGV_domain'),
                    "name"    => array("product_count", "button_style", "text"),
                    "type"    => "text",
                    "class"   => "berocket_lgv_product_count_text_styles",
                    "tr_class"=> "style_input_display_none",
                    "value"   => "",
                ),
            ),
        ) );
    }
    public function section_fixed_page_style($data, $options_global) {
        ob_start();
        $options = br_get_value_from_array($options_global, 'buttons_page');
        $settings_name = $this->values['settings_name'];
        include('templates/settings/fixed_page_style.php');
        return ob_get_clean();
    }
    public function section_shortcode($html) {
        $html = '<th>' . __( 'Shortcode', 'BeRocket_LGV_domain' ) . '</th>
        <td>
            <ul class="br_shortcode_info">
                <li>
                    <strong>[br_grid_list]</strong>
                    <ul>
                        <li><i>title</i> - ' . __( 'Title before buttons', 'BeRocket_LGV_domain' ) . '</li>
                        <li><i>position</i> - ' . __( '"", "left" or "right". Buttons position leftside or rightside', 'BeRocket_LGV_domain' ) . '</li>
                        <li><i>all_page</i> - ' . __( '1 or 0, display on any pages or only on shop and categories pages', 'BeRocket_LGV_domain' ) . '</li>
                    </ul>
                </li>
            </ul>
        </td>';
        return $html;
    }
    public function section_button_styles($data, $options_global) {
        ob_start();
        $options = br_get_value_from_array($options_global, 'buttons_page');
        $settings_name = $this->values['settings_name'];
        include('templates/settings/buttons_styles.php');
        return '<td colspan="2">' . ob_get_clean() . '</td>';
    }
    //Compatibility with other plugins
    function data_ajax_filters($data) {
        $data['General']['products_per_page']['extra'] = berocket_isset($data['General']['products_per_page']['extra']) . ' disabled';
        $data['General']['products_per_page']['label_for'] = __('<strong>Grid/List View</strong> override this settings, you can change it there ', 'BeRocket_LGV_domain')
        . '<a href="'.admin_url( 'admin.php?page=' . $this->values[ 'option_page' ] . '&tab=product-count' ).'">Grid/List View Settings</a>';
        return $data;
    }
    function remove_product_per_page($options) {
        if( ! empty($options['products_per_page']) ) {
            $options['products_per_page'] = '';
        }
        return $options;
    }
    function data_BeRocket_LMP($data) {
        $data['General']['general_products_per_page']['extra'] = berocket_isset($data['General']['general_products_per_page']['extra']) . ' disabled';
        $data['General']['general_products_per_page']['label_for'] = __('<strong>Grid/List View</strong> override this settings, you can change it there ', 'BeRocket_LGV_domain')
        . '<a href="'.admin_url( 'admin.php?page=' . $this->values[ 'option_page' ] . '&tab=product-count' ).'">Grid/List View Settings</a>';
        return $data;
    }
    function remove_product_per_page_load_more($options) {
        if( ! empty($options["br_lmp_general_settings"]["products_per_page"]) ) {
            $options["br_lmp_general_settings"]["products_per_page"] = '';
        }
        return $options;
    }
}

new BeRocket_LGV;
