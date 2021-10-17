<?php
if (!defined('ABSPATH')) {
    exit;
}

function ns_product_rating_woocommerce_loadStyles()
{
    wp_enqueue_style('ns-product-rating-woocommerce-style', plugins_url( 'asset/css/style.css', __FILE__ ));

}

add_action('wp_enqueue_scripts', 'ns_product_rating_woocommerce_loadStyles');

function ns_product_rating_woocommerce_dinamic_style()
{
    ?>
    <style>
        .ns-rating-woocom-fieldset > input:checked + label:hover, /* hover current star when changing rating */
        .ns-rating-woocom-fieldset > input:checked ~ label:hover,
        .ns-rating-woocom-fieldset > label:hover ~ input:checked ~ label, /* lighten current selection */
        .ns-rating-woocom-fieldset > input:checked ~ label:hover ~ label,
        .ns-rating-woocom-fieldset > input:checked ~ label, /* show gold star when clicked */
        .ns-rating-woocom-fieldset:not(:checked) > label:hover, /* hover current star */
        .ns-rating-woocom-fieldset:not(:checked) > label:hover ~ label{ color: <?php echo get_option('ns_product_rating_woocommerce_symbol_color'); ?> !important;  }

        .ns-rating-woocom-fieldset-read > input:checked ~ label, /* show gold star when clicked */
        .ns-rating-woocom-fieldset-read:not(:checked), /* hover previous stars in list */
        .ns-rating-woocom-fieldset-read > input:checked,
        .ns-rating-woocom-fieldset-read > input:checked ~ label, /* lighten current selection */
        .ns-rating-woocom-fieldset-read > input:checked ~ label { color: <?php echo get_option('ns_product_rating_woocommerce_symbol_color'); ?> !important;  }
    </style>
    <?php
}
add_action('wp_head', 'ns_product_rating_woocommerce_dinamic_style');


function ns_product_rating_woocommerce_enqueue_assets() {
    //wp_enqueue_style('wp-color-picker');
    wp_enqueue_script( 'ns_product_rating_woocommerce_custom', plugins_url( 'asset/js/custom.js' , __FILE__ ), array( 'jquery' ) );
    if(!is_shop()){
        wp_enqueue_script( 'ns_product_rating_woocommerce_custom_anim', plugins_url( 'asset/js/animation.js' , __FILE__ ), array( 'jquery' ) );
    }
    wp_localize_script( 'ns_product_rating_woocommerce_custom', 'ns_product_rating_woocommerce_vars', array(
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'ns_product_rating_woocommerce_rate' ))
    );
}
add_action( 'wp_enqueue_scripts', 'ns_product_rating_woocommerce_enqueue_assets' );


function ns_product_rating_color_picker_assets($hook_suffix)
{
    // $hook_suffix to apply a check for admin page.
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('my-script-handle', plugins_url('asset/js/custom-script.js', __FILE__), array('wp-color-picker'), false, true);
}

add_action('admin_enqueue_scripts', 'ns_product_rating_color_picker_assets');
?>