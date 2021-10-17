<?php
if (!defined('ABSPATH'))
    exit;

class WCPA_Product_Meta {

    private static $_instance = null;

    public function __construct() {
        add_filter('woocommerce_product_data_tabs', array($this, 'add_my_custom_product_data_tab'), 101, 1);
        add_action('woocommerce_product_data_panels', array($this, 'add_my_custom_product_data_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'woocommerce_process_product_meta_fields_save'));
    }

    public function add_my_custom_product_data_tab($product_data_tabs) {
        $product_data_tabs['wcpa_product-meta-tab'] = array(
            'label' => __('Custom Product Options', 'woo-custom-product-addons'),
            'target' => 'wcpa_product-meta-tab',
            'priority' => 90
        );
        return $product_data_tabs;
    }

    public function woocommerce_process_product_meta_fields_save($post_id) {
        // This is the case to save custom field data of checkbox. You have to do it as per your custom fields
        $meta_field = array();
        if (isset($_POST[WCPA_PRODUCT_META_FIELD])) {
            foreach ($_POST[WCPA_PRODUCT_META_FIELD] as $v) {
                $meta_field[] = (int) sanitize_text_field($v);
            }
        }
        update_post_meta($post_id, WCPA_PRODUCT_META_KEY, $meta_field);

        if (isset($_POST['wcpa_exclude_global_forms'])) {
            update_post_meta($post_id, 'wcpa_exclude_global_forms', true);
        } else {
            update_post_meta($post_id, 'wcpa_exclude_global_forms', false);
        }
    }

   public function add_my_custom_product_data_fields() {
        global $post;
        $ml = new WCPA_Ml();
        $meta_class = '';
        if ($ml->is_active() && !$ml->is_default_lan()) {
            $meta_class = 'wcpa_wpml_pro_meta';
        }
        ?>
        <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
        <div id="wcpa_product-meta-tab" class="panel woocommerce_options_panel <?php echo $meta_class; ?>">
            <?php
            if ($ml->is_active() && !$ml->is_default_lan()) {
                echo '<p class="wcpa_editor_message">' . sprintf(__('You can manage form fields from base language only.')) . '</p>';
            }
            ?>
            <h4> <?php _e('Select Form', 'wcpa-text-domain') ?></h4>
            <?php
            $meta_field = get_post_meta($post->ID, WCPA_PRODUCT_META_KEY, true);

            $forms = get_posts(array('post_type' => WCPA_POST_TYPE, 'posts_per_page' => -1));
            if ($ml->is_active()) {
                $forms = $ml->get_original_forms();
            }

            foreach ($forms as $v) {
                $checked = '';
                if ($meta_field && is_array($meta_field) && in_array($v->ID, $meta_field)) {
                    $checked = 'checked="checked"';
                }
                echo '<p><input type="checkbox" class="checkbox" ' . $checked . ' name="' . WCPA_PRODUCT_META_FIELD . '[]" id="wcpa_product_meta_' . $v->ID . '" value="' . $v->ID . '"">'
                . '<label for="wcpa_product_meta_' . $v->ID . '" class="description">' . $v->post_title . '(' . $v->ID . ')</label></p>';
            }
            ?>
            <h4> <?php _e('Configurations', 'wcpa-text-domain') ?></h4>
            <?php
            $checked = '';

            echo '<p><input type="checkbox" class="checkbox" ' . checked(get_post_meta($post->ID, 'wcpa_exclude_global_forms', true), true, false) . ' name="wcpa_exclude_global_forms" id="wcpa_exclude_global_forms" value="1">'
            . '<label for="wcpa_exclude_global_forms" class="description">Exclude/Override globally assigned form</label></p>';
            ?>
        </div>
        <?php
    }

    public static function instance($file = '', $version = '1.0.0') {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

}
