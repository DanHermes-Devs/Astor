<?php  
if( ! empty($options['value']) ) {
    $values = $options['value'];
} elseif( ! empty($test_values) ) {
    $values = $test_values;
}
$br_lgv_stat_products = '';
if ( ! empty($options['use']) ) {
    $product_count_per_page = br_lgv_get_cookie ( 1 );
    if( (int)$product_count_per_page ) {
        $br_lgv_stat_products = (int)$product_count_per_page;
    } elseif ( $product_count_per_page == 'all' ) {
        $br_lgv_stat_products = 'all';
    }
}
$exploder = (empty($options['explode']) ? '' : $options['explode']);
$exploder = preg_replace( '/\s+/','', $exploder );
if( ! empty($values) ) {
    ?><div class="br_lgv_product_count_block" <?php echo ' style="',( ( @ $position ) ? 'float:'.$position.';' : '' ), '"'; ?>><?php
    do_action( 'lgv_before_product_count_links' );
    $values = strtolower( @ $values );
    $values = preg_replace( '/\s+/', '', $values );
    $values = apply_filters( 'lgv_product_count_values', $values );
    $values = explode( ',', @ $values );
    echo '<span class="br_lgv_product_count text">'.@$options['text_before'].'</span>';
    $first = true;
    foreach( $values as $value ) {
        if( ! $first ) {
            ?><span class="br_lgv_product_count"><?php echo $exploder; ?></span><?php
        } else {
            $first = false;
        }
        if( $value == 'all' ) {
            ?><a href="#" data-type="<?php echo $value ?>" class="br_lgv_product_count_set <?php echo ( ( @ $custom_class ) ? @ $custom_class : 'br_lgv_product_count' ) ?> value_<?php echo $value; if ( $value == $br_lgv_stat_products ) echo ' selected' ?>"><?php _e( 'All', 'BeRocket_LGV_domain' ) ?></a><?php
        } elseif( ( (int)$value ) > 0 ) {
            ?><a href="#" data-type="<?php echo $value ?>" class="br_lgv_product_count_set <?php echo ( ( @ $custom_class ) ? @ $custom_class : 'br_lgv_product_count' ) ?> value_<?php echo (int)$value; if ( $value == $br_lgv_stat_products ) echo ' selected' ?>"><?php echo (int)$value ?></a><?php
        }
    }
    echo '<span class="br_lgv_product_count text">'.@$options['text_after'].'</span>';
    do_action( 'lgv_after_product_count_links' );
    ?></div><?php
}
?>
