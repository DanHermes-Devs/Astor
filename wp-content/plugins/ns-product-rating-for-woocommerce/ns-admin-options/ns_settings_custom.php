<?php
if ( ! defined( 'ABSPATH' ) ) {
    wp_die( __( 'This file cannot be called directly!', 'ns' ) );
    exit;
}
//Amministrazione WP

//Genero la pagina
?>
<div class="wrap">
        <div class="icon32" id="icon-options-general"><br/></div>
        <!-- <h2>NS Product Rating Woocommerce</h2> -->
        
        <form method="post" action="options.php">
            <?php settings_fields('ns_product_rating_woocommerce_options_group'); ?>
            <div class="ns-admin-settings-pr4w"> 
		        <label for="ns_wcm_symbol_color"><?php _e('Symbol Color', $ns_text_domain) ?>:</label><br><br>
		                    
                <input type="text" id="ns_wcm_symbol_color" name="ns_product_rating_woocommerce_symbol_color"
                       value=" <?php echo get_option('ns_product_rating_woocommerce_symbol_color'); ?>"
                       class="color-field"/>
                <span class="description"></span><br>
		                   
                <p>
                    <input type="submit" class="button-primary" id="submit" name="submit" value="<?php _e('Save Changes', $ns_text_domain) ?>"/>
                </p>
		        
	         </div>
        </form>
	   
    </div>