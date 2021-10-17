<?php

if (!defined('ABSPATH'))
    exit;

class WCPA_Form {

    /**
     * Constructor function
     */
    private $cart_error = false;
    private $data = null;
    private $product = false;

    public function __construct() {

    }

    public function get_form($form_id = false) {
        $this->data = array();
        if ($form_id) {
            $json_string = get_post_meta($form_id, WCPA_FORM_META_KEY, true);

            $this->data = json_decode($json_string);
        } else {
            $post_ids = get_posts(array('post_type' => WCPA_POST_TYPE, 'fields' => 'ids', 'posts_per_page' => -1));
            foreach ($post_ids as $id) {
                $json_string = get_post_meta($id, WCPA_FORM_META_KEY, true);
                $json_encoded = json_decode($json_string);
                if ($json_encoded && is_array($json_encoded)) {
                    $this->data = array_merge($this->data, $json_encoded);
                }
            }
        }
    }

    public function set_product($product) {
        if (is_object($product)) {
            $this->product = $product;
        } else {
            $this->product = wc_get_product($product);
        }
    }

    public function get_wcpa_products($xcld_drct_prchsbl = false) {
        global $wpdb;

        if (false === ( $pro_ids = get_transient(WCPA_PRODUCTS_TRANSIENT_KEY) )) {

            if ($xcld_drct_prchsbl) {
                $post_ids = get_posts(
                        array(
                            'fields' => 'ids',
                            'post_type' => WCPA_POST_TYPE,
                            'posts_per_page' => -1,
                            'meta_query' => array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'wcpa_drct_prchsble',
                                    'value' => false,
                                    'type' => 'BOOLEAN',
                                ),
                                array(
                                    'key' => 'wcpa_drct_prchsble',
                                    'compare' => 'NOT EXISTS'
                                ),
                            )
                        )
                );
            } else {
                $post_ids = get_posts(
                        array(
                            'fields' => 'ids',
                            'post_type' => WCPA_POST_TYPE,
                            'posts_per_page' => -1
                        )
                );
            }



            if ($post_ids && count($post_ids)) {

                $query = "SELECT distinct object_id from $wpdb->term_relationships where term_taxonomy_id"
                        . " in (select tr.term_taxonomy_id from $wpdb->term_relationships as tr left join $wpdb->term_taxonomy as tt on(tt.term_taxonomy_id=tr.term_taxonomy_id) where tr.object_id in (" . implode(',', $post_ids) . ")"
                        . "and  tt.taxonomy = 'product_cat')";

                $pro_ids = $wpdb->get_col($query);
                if (count($pro_ids)) {
                    $pro_ids = get_posts(
                            array(
                                'fields' => 'ids',
                                'post_type' => 'product',
                                'posts_per_page' => -1,
                                'include' => $pro_ids,
                                'meta_query' => array(
                                    'relation' => 'OR',
                                    array(
                                        'key' => 'wcpa_exclude_global_forms',
                                        'value' => false,
                                        'type' => 'BOOLEAN',
                                    ),
                                    array(
                                        'key' => 'wcpa_exclude_global_forms',
                                        'compare' => 'NOT EXISTS'
                                    ),
                                )
                            )
                    );
                }
                $exluded_ids = get_posts(
                        array(
                            'fields' => 'ids',
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'meta_query' => array(
                                array(
                                    'key' => 'wcpa_exclude_global_forms',
                                    'value' => '1',
                                    'type' => 'BOOLEAN',
                                )
                            )
                        )
                );

                $pro_ids = array_diff($pro_ids, $exluded_ids);

                $temp = array_reduce($post_ids, function($a, $b) {
                    return $a . " `meta_value` LIKE '%:$b;%' OR";
                });
                $temp .= trim($temp, 'OR');
                $pro_ids2 = $wpdb->get_col("SELECT post_id  from $wpdb->postmeta WHERE meta_key = '" . WCPA_PRODUCT_META_KEY . "' and ($temp)");

                if ($pro_ids2) {
                    $pro_ids = array_unique(array_merge($pro_ids, $pro_ids2));
                }
            } else {
                $pro_ids = array();
            }
            set_transient(WCPA_PRODUCTS_TRANSIENT_KEY, $pro_ids, 12 * HOUR_IN_SECONDS);
        }

//dividing with 60 will give the execution time in minutes otherwise seconds

        return $pro_ids;
    }

    public function get_form_ids($product_id) {
        $key_1_value = get_post_meta($product_id, 'wcpa_exclude_global_forms', true);
        $post_ids = array();
        $ml = new WCPA_Ml();
        if (empty($key_1_value)) {

            $post_ids = get_posts(
                    array(
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'ids',
                                'include_children' => false,
                                'terms' => wp_get_object_terms($product_id, 'product_cat', array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids'))
                            )
                        ),
                        'fields' => 'ids',
                        'post_type' => WCPA_POST_TYPE,
                        'posts_per_page' => -1
                    )
            );
        }
        $form_ids_set2 = get_post_meta($product_id, WCPA_PRODUCT_META_KEY, true);

        if ($form_ids_set2) {
            $post_ids = array_unique(array_merge($post_ids, $form_ids_set2));
        }

        if ($ml->is_active()) {
            $post_ids = $ml->lang_object_ids($post_ids, 'post', false);
        }

        return $post_ids;
    }

    public function get_forms_by_product($product_id = false) {
        $this->data = array();

        if ($product_id) {

            $this->cart_error = WCPA_Front_End::get_cart_error($product_id);

            $post_ids = $this->get_form_ids($product_id);

            if (wcpa_get_option('form_loading_order_by_date') === true) {
                if (is_array($post_ids) && count($post_ids)) {
                    $post_ids = get_posts(array(
                        'posts_per_page' => -1,
                        'include' => $post_ids,
                        'fields' => 'ids',
                        'post_type' => WCPA_POST_TYPE,
                        'posts_per_page' => -1
                    ));
                }
            }
            foreach ($post_ids as $id) {
                if (get_post_status($id) == 'publish') {
                    $json_string = get_post_meta($id, WCPA_FORM_META_KEY, true);
                    $json_encoded = json_decode($json_string);
                    if ($json_encoded && is_array($json_encoded)) {
                        $this->data = array_merge($this->data, $json_encoded);
                    }
                }
            }
        }

        $this->data = apply_filters('wcpa_product_form_fields', $this->data, $product_id);
    }

    public function validate_form_data($product_id = false) {

        $this->get_forms_by_product($product_id);

        $status = true;

        foreach ($this->data as $v) {
            if ($v->type != 'file' && isset($v->required) && $v->required && (!isset($_POST[$v->name]) || empty($_POST[$v->name]))) {
                $status = FALSE;
                $this->add_cart_error(sprintf(__('Field %s is required', 'woo-custom-product-addons'), $v->label));
            }
        }

        WCPA_Front_End::set_cart_error($product_id, !$status);

        return $status;
    }

    private function add_cart_error($message) {
        wc_add_notice($message, 'error');
    }

    public function find_meta_by_name($name, $meta_data) {

        $arr = array_filter($meta_data, function($v) use($name) {
            return $v['name'] === $name;
        });

        if ($arr !== false && !empty($arr)) {
            return reset($arr);
        } else {
            return false;
        }
    }

    public function order_again_item_data($item) {
        $product_id = $item->get_product_id();
        $this->get_forms_by_product($product_id);
        $this->set_product($product_id);
        $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);

        $submited_data = array();
        if ($meta_data) {
            foreach ($this->data as $k => $v) {

                $form_data = clone $v;
                unset($form_data->values); //avoid saving large number of data
                unset($form_data->className); //avoid saving no use data
                if (!in_array($v->type, array('header', 'paragraph'))) {

                    $meta = $this->find_meta_by_name($v->name, $meta_data);

                    if (is_array($meta['value']) && in_array($v->type, ['select', 'checkbox-group', 'radio-group'])) {
                        //check the options are selectable
                        $value_data = array(); // $meta['value'];

                        foreach ($meta['value'] as $j => $_v) {
                            $flag = false;
                            if (is_array($_v)) {
                                foreach ($v->values as $newkey => $_v2) {
                                    if ((isset($_v2->value) && $_v2->value === $_v['value'])) {
                                        $flag = true;
                                        break;
                                    }
                                }
                            } else {
                                foreach ($v->values as $newkey => $_v2) {
                                    if ((isset($_v2->value) && $_v2->value === $_v)) {
                                        $flag = true;
                                        break;
                                    }
                                }
                            }

                            if ($flag !== false) {
                                $value_data[$newkey] = $_v;
                            }
                        }
                        if ($v->type !== 'checkbox-group' && (!isset($v->multiple) || !$v->multiple)) {//no multi option
                            $value_data = array_slice($value_data, 0, 1, true);
                        }
                    } else {
                        $value_data = $meta['value'];
                    }

                    if (isset($meta['value'])) {
                        $submited_data[] = array(
                            'type' => $v->type,
                            'name' => $v->name,
                            'label' => (isset($v->label)) ? $v->label : '',
                            'value' => $value_data,
                            'price' => false,
                            'form_data' => $form_data
                        );
                    }
                }
            }
        }

        return $submited_data;
    }

    public function submited_data($product_id) {

        $this->get_forms_by_product($product_id);
        $this->set_product($product_id);
        $hide_empty = wcpa_get_option('hide_empty_data', false);
        $submited_data = array();
        foreach ($this->data as $k => $v) {

            $form_data = clone $v;
            unset($form_data->values); //avoid saving large number of data
            unset($form_data->className); //avoid saving no use data
            if (!in_array($v->type, array('header', 'paragraph'))) {
                if (isset($_POST[$v->name]) && !($hide_empty && $_POST[$v->name] === '') ) {
                    $submited_data[] = array(
                        'type' => $v->type,
                        'name' => $v->name,
                        'label' => (isset($v->label)) ? $v->label : '',
                        'value' => $this->sanitize_values($v),
                        'price' => false,
                        'form_data' => $form_data
                    );
                }
            }
        }


        return $submited_data;
    }

    /*
      Sanitize and return user input data based on the type of field
     */

    public function sanitize_values($v) {
        if (!is_object($v)) {
            return sanitize_text_field($v);
        } else if ((isset($v->name))) {
            if (is_array($_REQUEST[$v->name])) {

                $_values = $_REQUEST[$v->name];
                array_walk($_values, function(&$a, $b) {
                    sanitize_text_field($a);
                }); // using this array_wal method to preserve the keys
                return $_values;
            } else if ($v->type == 'textarea') {
                return sanitize_textarea_field(wp_unslash($_REQUEST[$v->name]));
            } else {
                return sanitize_text_field(wp_unslash($_REQUEST[$v->name]));
            }
        }
    }

    private function default_value($v) {

        $default_value = '';
        switch ($v->type) {
            case 'text':
            case 'date':
            case 'number':
            case 'textarea':
            case 'color':

                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value = $this->sanitize_values($v);
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) { // using get if there is any value passed using url/get method
                    $default_value = $this->sanitize_values($v);
                } else if (isset($v->value)) {
                    $default_value = $v->value;
                }

                break;
            case 'select':
            case 'checkbox-group':
            case 'radio-group':

                $default_value = array();
                if ($this->cart_error && (isset($v->name)) && isset($_POST[$v->name])) { // if there is a validation error, it has persist the user entered values,
                    $default_value = $this->sanitize_values($v);
                } elseif ((isset($v->name)) && isset($_GET[$v->name])) {
                    $default_value = $this->sanitize_values($v);
                } else if ($v->values && !(isset($_REQUEST['add-to-cart']) && $this->cart_error)) { // if it is direct product page load, not add-to-cart has set
                    foreach ($v->values as $k => $val) {
                        if (isset($val->selected)) {
                            $default_value[$k] = $val->selected;
                        }
                    }
                }

                break;
        }
        return $default_value;
    }

    public function render() {

        $has_price = false;
        echo '<div class="wcpa_form_outer">';
        foreach ($this->data as $v) {
            $parent_class = 'wcpa_form_item wcpa_type_' . $v->type . ' ';
            if (isset($v->className)) {
                $parent_class .= ' ' . $v->className . '_parent';
            }
            if ($v->type == 'hidden') {
                $this->render_hidden($v);
            } else {

                echo '<div class="' . $parent_class . '">';
                switch ($v->type) {
                    case 'text':
                        $this->render_text($v);
                        break;
                    case 'color':
                        $this->render_color($v);
                        break;
                    case 'number':
                        $this->render_number($v);
                        break;
                    case 'date':
                        $this->render_date($v);
                        break;

                    case 'checkbox-group':
                        $this->render_checkbox($v);

                        break;
                    case 'radio-group':
                        $this->render_radio($v);
                        break;
                    case 'header':
                        $this->render_header($v);
                        break;

                    case 'paragraph':
                        $this->render_paragraph($v);
                        break;
                    case 'select':
                        $this->render_select($v);
                        break;
                    case 'textarea':
                        $this->render_textarea($v);
                        break;
                }

                echo '</div>';
            }
        }

        echo '</div>';
    }

    public function render_text($v) {
        $data = '';
        $maxlength = '';
        $txt_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';
        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $name = (isset($v->name)) ? $v->name : '';
        if ($v->label) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }
        if (isset($v->maxlength)) {
            $maxlength = 'maxlength="' . $v->maxlength . '"';
        }
        echo '<input type="' . $v->subtype . '"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $maxlength . ' ' . $txt_required . '   />';
    }

    public function render_color($v) {
        $data = '';
        $maxlength = '';
        $txt_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';
        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $name = (isset($v->name)) ? $v->name : '';
        if ($v->label) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }
        if (isset($v->maxlength)) {
            $maxlength = 'maxlength="' . $v->maxlength . '"';
        }
        echo '<input type="color"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $maxlength . ' ' . $txt_required . ' />';
    }

    public function render_date($v) {
        $required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';


        $name = (isset($v->name)) ? $v->name : '';
        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        if (isset($v->label)) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }
        echo '<input type="date"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $required . '/>';
    }

    public function render_datetime($v) {
        $required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';


        $name = (isset($v->name)) ? $v->name : '';
        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        if (isset($v->label)) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }
        echo '<input type="datetime-local"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $required . '/>';
    }

    public function render_number($v) {
        $num_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $max = (isset($v->max)) ? 'max="' . $v->max . '"' : '';
        $min = (isset($v->min)) ? 'min="' . $v->min . '"' : '';
        $name = (isset($v->name)) ? $v->name : '';

        $step = (isset($v->step)) ? 'step="' . $v->step . '"' : 'step="any"';

        if ($v->label) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }

        echo '<input type="' . $v->type . '"  id="' . $name . '" ' . $placeholder . ' ' . $className . ' name="' . $name . '" value="' . $this->default_value($v) . '" ' . $max . ' ' . $min . ' ' . $step . ' ' . $num_required . '  />';
    }

    public function render_textarea($v) {
        $maxlength = '';
        $maxlength = (isset($v->maxlength)) ? 'maxlength="' . $v->maxlength . '"' : '';
        $title = (isset($v->title)) ? 'title="' . $v->title . '"' : '';
        $txtarea_required = (isset($v->required)) ? 'required="required"' : '';
        $placeholder = (isset($v->placeholder)) ? 'placeholder="' . $v->placeholder . '"' : '';

        $name = (isset($v->name)) ? $v->name : '';
        $rows = (isset($v->rows)) ? $v->rows : '';
        $rows = (isset($v->rows)) ? 'rows="' . $v->rows . '"' : '';

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }


        if (isset($v->label)) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }



        echo '<textarea id="' . $name . '"  ' . $rows . ' ' . $placeholder . ' ' . $className . ' name="' . $name . '" ' . $maxlength . ' ' . $title . ' ' . $txtarea_required . '  >' . $this->default_value($v) . '</textarea>';
    }

    public function render_checkbox($v) {
        $name = (isset($v->name)) ? $v->name : '';
        if (isset($v->label)) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }
        $chk_required = (isset($v->required)) ? 'required="required"' : '';


        $className = 'checkbox-group ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }


        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        $default_value = $this->default_value($v);
        if ($v->values && !empty($v->values)) {
            echo '<div ' . $className . '>';
            foreach ($v->values as $k => $val) {
                $chkd = (isset($val->selected)) ? 'checked="checked"' : '';
                $price = '';

                $option_class = '';
                if (isset($v->enablePrice) && $v->enablePrice) {
                    $option_class .= 'has_price ';
                }
                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }
                $label = $val->label;


                echo '<div class="wcpa_checkbox">
                    <input name="' . $name . '[' . $k . ']" ' . $option_class . '" id="' . $name . '_' . $k . '" value="' . $val->value . '" type="checkbox" ' . $chkd . ' >
                          
<label for="' . $name . '_' . $k . '"> <span class="wcpa_check"></span>' . $label . '</label>
                    </div>';
            }
            echo '</div>';
        }
    }

    public function render_radio($v) {
        $name = (isset($v->name)) ? $v->name : '';
        if (isset($v->label)) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }
        $default_value = $this->default_value($v);
        $className = 'radio-group ';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        if ($v->values && !empty($v->values)) {
            echo '<div ' . $className . ' >';
            foreach ($v->values as $k => $val) {
                if (is_array($default_value)) {
                    $is_selected = isset($default_value[$k]) ? $default_value[$k] : false;
                } else {
                    $is_selected = ($default_value == $val->value) ? true : false;
                }

                $price = '';
                $option_class = '';

                if (!empty($option_class)) {
                    $option_class = 'class="' . $option_class . '"';
                }


                $label = $val->label;

                echo ' <div class="wcpa_radio">
                    <input name="' . $name . '" ' . $option_class . ' id="' . $name . '_' . $k . '" value="' . $val->value . '"  value="' . $val->value . '" type="radio" ' . (($is_selected !== false) ? 'checked="checked"' : '') . '>
                       
                        <label for="' . $name . '_' . $k . '"><span class="wcpa_check"></span>' . $label . '</label>
                    </div>';
            }
            echo '</div>';
        }
    }

    public function render_select($v) {
        $name = (isset($v->name)) ? $v->name : '';

        $data = '';
        $default_value = $this->default_value($v);
        $sel_required = (isset($v->required)) ? 'required="required"' : '';
        $multiple = (isset($v->multiple)) ? 'multiple="true"' : '';

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }

        if (isset($v->label)) {
            echo '<label for="' . $name . '">' . $v->label;
            if (isset($v->required) && $v->required) {
                echo '<span class="required_ast">*</span>';
            }
            echo '</label>';
        }

        if ($v->values && !empty($v->values)) {


            echo '<div class="select" ><select  name="' . $name . '" ' . $className . ' ' . $multiple . ' ' . $sel_required . '>';
            if (isset($v->placeholder) && $v->placeholder != '') {

                echo '<option value="" >' . $v->placeholder . '</option>';
            }
            foreach ($v->values as $val) {


                $label = $val->label;


                $selectedchk = (isset($val->selected)) ? 'selected="selected"' : '';
                echo '<option  value="' . $val->value . '" ' . $selectedchk . '>' . $label . '</option>';
            }
            echo '</select><div class="select_arrow"></div></div>';
        }
    }

    public function render_hidden($v) {
        $name = (isset($v->name)) ? $v->name : '';
        $value = (isset($v->value)) ? $v->value : '';

        echo '<input type="hidden"  id="' . $name . '"  name="' . $name . '" value="' . $value . '"/>';
    }

    public function render_header($v) {

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }


        if ($v->subtype == 'h1') {
            echo '<h1 ' . $className . '>' . $v->label . '</h1>';
        } else if ($v->subtype == 'h2') {
            echo '<h2 ' . $className . '>' . $v->label . '</h2>';
        } else if ($v->subtype == 'h3') {
            echo '<h3 ' . $className . '>' . $v->label . '</h3>';
        }
    }

    public function render_paragraph($v) {

        $className = '';
        if (isset($v->className)) {
            $className .= $v->className . ' ';
        }

        if (!empty($className)) {
            $className = 'class="' . $className . '"';
        }
        if ($v->subtype == 'address') {
            echo '<address ' . $className . '>' . $v->label . '</address>';
        } else if ($v->subtype == 'blockquote') {
            echo '<blockquote ' . $className . '>' . $v->label . '</blockquote>';
        } else if ($v->subtype == 'output') {
            echo '<output ' . $className . '>' . $v->label . '</output>';
        } else if ($v->subtype == 'canvas') {
            echo '<canvas ' . $className . '>' . $v->label . '</canvas>';
        } else {
            echo '<p ' . $className . '>' . $v->label . '</p>';
        }
    }

    public function __call($name, $arguments) {
        return null;
    }

}
