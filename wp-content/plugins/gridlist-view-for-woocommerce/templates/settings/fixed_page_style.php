<th><?php _e( 'Fixed style for page', 'BeRocket_LGV_domain' ); ?></th>
<td>
    <select class="br_grid_list_page_select">
    <?php
    $woo_pages = array(
        'home'      => '[SITE HOME]',
        'shop'      => '[WOO SHOP]',
        'category'  => '[WOO CATEGORIES]',
        'product'   => '[WOO PRODUCTS]',
    );
    $styles = array(
        'grid'  => __('Grid', 'BeRocket_LGV_domain'),
        'list'  => __('List', 'BeRocket_LGV_domain'),
    );
    $devices = array(
        'pages'  => __('Mobile & Desktop', 'BeRocket_LGV_domain'),
        'mobile_pages'  => __('Mobile', 'BeRocket_LGV_domain'),
        'desktop_pages'  => __('Desktop', 'BeRocket_LGV_domain'),
    );
    $pages = get_pages();
    $default_language = apply_filters( 'wpml_default_language', NULL );
    foreach($woo_pages as $woo_page_id => $woo_page_name) {
        echo '<option value="' . $woo_page_id . '">' . $woo_page_name . '</option>';
    }
    foreach ( $pages as $page ) {
        $page_id = apply_filters( 'wpml_object_id', $page->ID, 'page', true, $default_language );
        echo '<option value="'.$page_id.'">'.$page->post_title.'</option>';
    }
    ?>
    </select>
    <select class="br_grid_list_style_select">
        <?php 
        foreach($styles as $style_val => $style_name) {
            echo "<option value='{$style_val}'>{$style_name}</option>";
        }
        ?>
    </select>
    <select class="br_grid_list_device_select">
        <?php 
        foreach($devices as $device_val => $device_name) {
            echo "<option value='{$device_val}'>{$device_name}</option>";
        }
        ?>
    </select>
    <button type="button" class="button br_grid_list_page_add" name="<?php echo $settings_name; ?>[buttons_page]"><?php _e('Add page', 'BeRocket_LGV_domain'); ?></button>
    <ul class="br_grid_list_pages">
        <?php
        foreach($devices as $device_val => $device_name) {
            if( isset($options[$device_val]) && is_array($options[$device_val]) ) {
                foreach($options[$device_val] as $page_id => $style) {
                    $text = '';
                    $text .= '<span class="br_grid_list_style">'.$styles[$style].'</span>';
                    $text .= '<span class="br_grid_list_device">'.$device_name.'</span>';
                    if( array_key_exists($page_id, $woo_pages) ) {
                        $text .= $woo_pages[$page_id];
                    } else {
                        $current_language = apply_filters( 'wpml_current_language', NULL );
                        $cpage_id = apply_filters( 'wpml_object_id', $page_id, 'page', true, $current_language );
                        $page = get_post($cpage_id);
                        $text .= berocket_isset($page, 'post_title', '==PAGE NOT EXIST==');
                    }
                    echo '<li class="br_grid_list_page_id id_'.$page_id.' id_'.$device_val.'_'.$page_id.'">
                    <input type="hidden" name="'.$settings_name.'[buttons_page]['.$device_val.']['.$page_id.']" value="'.$style.'">
                    <button type="button" class="button br_grid_list_page_remove">'.$text.'</button>
                    </li>';
                }
            }
        }
        ?>
    </ul>
</td>
