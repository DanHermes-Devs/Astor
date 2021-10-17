<?php

if (!defined('ABSPATH'))
    exit;

class WCPA_Order_Meta {

    private function wcpa_meta_by_meta_id($item, $meta_id) {
        $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);

        if (is_array($meta_data) && count($meta_data)) {

            foreach ($meta_data as $v) {

                if ($meta_id == $v['meta_id']) {
                    return $v;
                }
            }
        } else {
            return false;
        }
        return false;
    }

    public function order_meta_plain($v) {
        if (is_array($v['value'])) {

            return implode(', ', $v['value']);
        } else {

            return $v['value'];
        }
    }

    public function display_meta_value($display_value, $meta = null, $item = null) {

        if ($item != null && $meta !== null) {
            $wcpa_data = $this->wcpa_meta_by_meta_id($item, $meta->id);
        } else {
            $wcpa_data = false;
        }
        if ($wcpa_data) {

            switch ($wcpa_data['type']) {
                case 'text':
                case 'date':
                case 'number':
                case 'time':
                    return $display_value;
                case 'textarea':
                    return nl2br($meta->value);
                case 'color':
                    return '<span style="color:' . $meta->value . ';font-size: 20px;
            padding: 0;
    line-height: 0;">&#9632;</span>' . $meta->value;
                case 'select':
                case 'checkbox-group':
                case 'radio-group':
                    return str_replace(', ', '<br>', $meta->value);

                default:
                    return $display_value;
            }
        } else {
            return $display_value;
        }
    }
    public function checkout_subscription_created($subscription) {
        $items = $subscription->get_items();
        $order_id =  $subscription->get_id();
        if (is_array($items)) {
            foreach ($items as $item_id => $item) {
                $this->update_order_item($item, $order_id);
            }
        }
    }
    public function checkout_order_processed($order_id, $posted_data, $order=false) {
        if($order===false){
            $order = wc_get_order($order_id);
        }
        $items = $order->get_items();
        if (is_array($items)) {
            foreach ($items as $item_id => $item) {
                $this->update_order_item($item, $order_id);
            }
        }
    }

    public function update_order_item($item, $order_id) {
        $meta_data = $item->get_meta_data();
        $wcpa_meta_data = $item->get_meta(WCPA_ORDER_META_KEY);
        foreach ($meta_data as $meta) {
            $data = (object) $meta->get_data();

            if (($matches = $this->check_wcpa_meta($data)) !== false) {

                if (isset($wcpa_meta_data[$matches[1]])) {

                    $wcpa_meta_data_item = $wcpa_meta_data[$matches[1]];


                    $item->update_meta_data($wcpa_meta_data_item['label'], $data->value, $data->id);

                    $wcpa_meta_data[$matches[1]]['meta_id'] = $data->id;
                }
            }
        }

        $wcpa_meta_data = apply_filters('wcpa_order_meta_data', $wcpa_meta_data, $item, $order_id);

        $item->update_meta_data(WCPA_ORDER_META_KEY, $wcpa_meta_data);
        $item->save_meta_data();
    }

    public function checkout_create_order_line_item($item, $cart_item_key, $values) {

        if (empty($values[WCPA_CART_ITEM_KEY])) {
            return;
        }
        $meta_data = array();
        $i = 0;

        foreach ($values[WCPA_CART_ITEM_KEY] as $v) {
            $meta_data[$i] = $v;

            if (!in_array($v['type'], array('header', 'paragraph'))) {
                $item->add_meta_data('WCPA_id_' . $i, $this->order_meta_plain($v));
            }
            $i++;
        }
        $item->add_meta_data(WCPA_ORDER_META_KEY, $meta_data);
    }

    private function check_wcpa_meta($meta) {

        preg_match("/WCPA_id_(.*)/", $meta->key, $matches);


        if ($matches && count($matches)) {
            return $matches;
        } else {
            return false;
        }
    }

    // admin side */

    public function order_item_line_item_html($item_id, $item, $order) {
        $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);

        WCPA_Backend::view('order-meta-line-item', ['meta_data' => $meta_data, 'order' => $order, 'item_id' => $item_id]);
    }

    public function order_item_get_formatted_meta_data($formatted_meta, $item) {

        if (did_action('woocommerce_before_order_itemmeta') > 0) {
            $meta_data = $item->get_meta('_wcpa_meta_key_info');
            foreach ($formatted_meta as $meta_id => $v) {
                if ($this->wcpa_meta_by_meta_id($item, $meta_id)) {
                    unset($formatted_meta[$meta_id]);
                }
            }
        }

        return $formatted_meta;
    }

    public function sanitize_values($value, $type) {
        if (is_array($value)) {
            array_walk($value, function(&$a, $b) {
                sanitize_text_field($a);
            }); // using this array_wal method to preserve the keys
            return $value;
        } else if ($type == 'textarea') {
            return sanitize_textarea_field($value);
        } else {
            return sanitize_text_field($value);
        }
    }

    public function before_save_order_items($order_id, $items) {

        if (is_array($items) && isset($items['wcpa_meta'])) {
            $wcpa_meta = $items['wcpa_meta'];
            if (isset($wcpa_meta['value']) && is_array($wcpa_meta['value'])) {
                foreach ($wcpa_meta['value'] as $item_id => $data) {
                    if (!$item = WC_Order_Factory::get_order_item(absint($item_id))) {
                        continue;
                    }

                    $meta_data = $item->get_meta(WCPA_ORDER_META_KEY);

                    foreach ($meta_data as $k => $v) {
                        $meta_id = $meta_data[$k]['meta_id'];
                        if (isset($data[$k])) {
                            $meta_value_temp = array('type' => false, 'value' => false, 'price' => FALSE);


                            $meta_data[$k]['value'] = $this->sanitize_values($data[$k], $v['type']);
                            $meta_value_temp['value'] = $meta_data[$k]['value'];
                            $meta_value_temp['type'] = $v['type'];
                            $meta_value = $this->order_meta_plain($meta_value_temp);
                            $item->update_meta_data($v['label'], $meta_value, $meta_id);
                        } else {
                            $item->delete_meta_data_by_mid($meta_id);
                            unset($meta_data[$k]);
                        }
                    }
                    $item->update_meta_data(WCPA_ORDER_META_KEY, $meta_data);
                    $item->save();
                }
            }
        }
    }

}
