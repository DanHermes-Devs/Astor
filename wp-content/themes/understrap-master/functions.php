<?php
/**
 * UnderStrap functions and definitions
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$understrap_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/jetpack.php',                         // Load Jetpack compatibility file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567
	'/woocommerce.php',                     // Load WooCommerce functions.
	'/editor.php',                          // Load Editor functions.
	'/deprecated.php',                      // Load deprecated functions.
);

foreach ( $understrap_includes as $file ) {
	require_once get_template_directory() . '/inc' . $file;
}

function astor_menus(){
	register_nav_menus(array(
		'menu-footer'	=>	__('Menú Footer', 'understrap')
	));
}


add_action('init', 'astor_menus');

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);



function woocommerce_template_custom_title(){
	$titulo = "<h1 class='title-product'>" . get_the_title()  . "</h1>";
	echo $titulo;
}

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_custom_title', 1);

function woocommerce_template_custom_content(){
	$desc = the_content();
	global $product;

	$rating_count = $product->get_rating_count();
	$review_count = $product->get_review_count();
	$average      = $product->get_average_rating();

	//echo wc_get_rating_html($average, $rating_count);

	echo "<h1>" . $desc . "</h1>";
	echo '<h2 class="section-heading text-uppercase" style="font-size: 26px !important">TAMAÑO</h2> <p>1 caja (6 paquetes con 12 donas)</p>';
	echo '<h2 class="section-heading text-uppercase" style="font-size: 26px !important; margin-bottom: 0 !important;">CANTIDAD</h2>';

}

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_custom_content', 10);

add_action('woocommerce_after_shop_loop_item', 'add_star_rating' );


add_filter('woocommerce_loop_add_to_cart_link','change_simple_shop_add_to_cart',10,2);
function change_simple_shop_add_to_cart( $html, $product ){
    if( $product->is_type('simple')) {

        $html = sprintf( '<a rel="nofollow" href="%s" data-product_id="%s"  class="button-add-to-cart-person buttonflecha">%s</a>',
                esc_url( get_the_permalink() ),
                esc_attr( $product->get_id() ),
                esc_html(  __( 'COMPRAR', 'woocommerce' ) )
        );
    }
    return $html;
}


function astor_scripts() {
    wp_enqueue_style( 'script-name', get_template_directory_uri() . '/css/estilos.css', array(), '1.0.0');
}
add_action( 'wp_enqueue_scripts', 'astor_scripts' );



