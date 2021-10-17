<?php
if (is_array($meta_data) && count($meta_data)) {
    ?>
    <table>
        <tr>
            <th></th>
            <th><?php _e('Value','woo-custom-product-addons'); ?></th>
            <th></th>
        </tr>
        <?php
        foreach ($meta_data as $k => $data) {

            if (in_array($data['type'], array('checkbox-group')) && is_array($data['value'])) {
                $label_printed = false;
                foreach ($data['value'] as $l => $v) {
                    ?>
                    <tr class="item_wcpa">
                        <td class="name">
                            <?php
                            echo $label_printed ? '' : $data['label'];
                            $label_printed = true;
                            ?>
                        </td>
                        <td class="value" >
                            <div class="view">
                                <?php echo $v ?>
                            </div>
                            <div class="edit" style="display: none;">
                                <input type="text" name="wcpa_meta[value][<?php echo $item_id; ?>][<?php echo $k; ?>][<?php echo $l; ?>]" value="<?php echo $v ?>">
                            </div>
                        </td>

                 

                        <td class="wc-order-edit-line-item" width="1%">
                            <div class = "wc-order-edit-line-item-actions edit" style="display: none;">
                                <a class="wcpa_delete-order-item tips" href="#" data-tip="<?php esc_attr_e('Delete item', 'woocommerce'); ?>"></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr class="item_wcpa">

                    <td class="name">

                        <?php
                        if ($data['type'] == 'hidden' && empty($data['label'])) {
                            echo $data['label'].'[hidden]';
                        } else {
                            echo $data['label'];
                        }
                        ?>
                    </td>
                    <td class="value" >
                        <div class="view">
                            <?php echo $data['value'] ?>
                        </div>
                        <div class="edit" style="display: none;">
                            <input type="text" 
                                   name="wcpa_meta[value][<?php echo $item_id; ?>][<?php echo $k; ?>]" 
                                   value="<?php echo $data['value'] ?>">
                        </div>
                    </td>


                    <td class = "wc-order-edit-line-item" width = "1%">
                        <div class = "wc-order-edit-line-item-actions edit" style="display: none;">
                            <a class="wcpa_delete-order-item tips" href="#" data-tip="<?php esc_attr_e('Delete item', 'woocommerce'); ?>"></a>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>


            <?php
        }
        ?>
        <tr>
            <!--   /* dummy field , it will help to iterate through all data for removing last item*/-->
        <input type="hidden" name="wcpa_meta[value][<?php echo $item_id; ?>][<?php echo $k + 99; ?>]" value="">

        </tr>
    </table>

    <?php
}



