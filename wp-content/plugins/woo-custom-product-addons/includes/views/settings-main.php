<div class="wrap wcpa_settings">
    <div id="icon-options-general" class="icon32"></div>
    <h1><?php echo WCPA_PLUGIN_NAME; ?></h1>

    <div id="poststuff">
        <div class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable" style="position:relative">
                    <div class="wcpa_settings_outer_left">
                        <div class="postbox">
                            <div class="wcpa_inside">
                                <form method="post" action="">
                                    <?php wp_nonce_field('wcpa_save_settings', 'wcpa_nonce'); ?>
                                        <div class="form_table">
                                            <div class="form-table_row clearfix">
                                                    <div class="form_table_left">
                                                        <label for="add_to_cart_text" class="group_heading">
                                                            <?php _e('Add to cart button text', 'woo-custom-product-addons'); ?> <br>
                                                        </label>
                                                    </div>
                                                    <div class="form_table_right">
                                                        <input type="text" name="add_to_cart_text" id="add_to_cart_text"
                                                        value="<?php echo wcpa_get_option('add_to_cart_text', 'Select options'); ?>" />
                                                        <p class="small-text"><?php _e('Add to cart button text in archive/product listing page in case product has additional fields', 'woo-custom-product-addons'); ?></p>
                                                    </div>
                                            </div>
                                            <div class="form_table_row">
                                                <div class="form_table_left">

                                                        <label class="group_heading">
                                                            <?php _e('Display custom fields data in ', 'woo-custom-product-addons'); ?>
                                                        </label>
                                                </div>
                                                <div class="form_table_right">
                                                    <div class="block_view">
                                                            <input type="checkbox"
                                                                name="wcpa_show_meta_in_cart" id="wcpa_show_meta_in_cart"
                                                                value="1"  <?php checked(wcpa_get_option('show_meta_in_cart')); ?> />

                                                            <label for="wcpa_show_meta_in_cart"> <?php _e('Show in cart','woo-custom-product-addons'); ?>
                                                            </label>
                                                        </div>
                                                        <div class="block_view">
                                                            <input type="checkbox"
                                                                name="wcpa_show_meta_in_checkout" id="wcpa_show_meta_in_checkout"
                                                                value="1"  <?php checked(wcpa_get_option('show_meta_in_checkout')); ?> />

                                                            <label for="wcpa_show_meta_in_checkout"><?php _e('Show in Checkout', 'woo-custom-product-addons'); ?>
                                                            </label>
                                                        </div>
                                                        <div class="block_view">
                                                            <input type="checkbox"
                                                                name="wcpa_show_meta_in_order" id="wcpa_show_meta_in_order"
                                                                value="1"<?php checked(wcpa_get_option('show_meta_in_order')); ?> />

                                                            <label for="wcpa_show_meta_in_order"><?php _e('Show in Order', 'woo-custom-product-addons'); ?>
                                                            </label>
                                                        </div>
                                                </div>
                                            </div>
                                            <div class="form_table_row clearfix">
                                                    <div class="form_table_left">
                                                        <label class="group_heading">
                                                            <?php _e('Load form in recency order', 'woo-custom-product-addons'); ?>

                                                        </label>
                                                    </div>
                                                    <div class="form_table_right">
                                                        <input type="checkbox" name="form_loading_order_by_date" id="form_loading_order_by_date"
                                                                value="1" <?php checked(wcpa_get_option('form_loading_order_by_date', false)); ?>>

                                                        <label for="form_loading_order_by_date">
                                                            <?php _e('If a product has assigned multiple forms, it will be loaded based on form created date', 'woo-custom-product-addons'); ?>
                                                        </label>
                                                    </div>
                                            </div>

                                            <div class="form_table_row clearfix">
                                                <div class="form_table_left">
                                                    <label class="group_heading">
                                                        <?php _e('Hide empty fields in cart', 'woo-custom-product-addons'); ?>

                                                    </label>
                                                </div>
                                                <div class="form_table_right">
                                                    <input type="checkbox" name="hide_empty_data" id="hide_empty_data"
                                                           value="1" <?php checked(wcpa_get_option('hide_empty_data', false)); ?>>

                                                    <label for="hide_empty_data">
                                                        <?php _e('Hide empty fields in cart, checkout and order', 'woo-custom-product-addons'); ?>
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    <div class="form_table_row">
                                        <?php submit_button(null, 'primary', 'wcpa_save_settings'); ?>
                                    </div>
                                </form>
                            </div>
                            <!-- .inside -->

                        </div>
                        <!-- .postbox -->

                        <div class="premium clearfix">
                            <div class="premium_left">
                                <h1>Upgrade to <br>Premium Now!</h1>
<!--                                <div class="price">-->
<!--                                    <span>For only <b>$29.00</b> per site</span>-->
<!--                                </div>-->
                                <div class="tagline">
                                    Supercharge Your WooCommerce Stores <br> with our
                                    light, fast and feature-rich plugins.
                                </div>
                                <div>
                                    <a href="https://acowebs.com/woo-custom-product-addons/?ref=free-wcpa" target="_blank">Upgrade Now</a>
                                </div>


                            </div>
                            <div class="premium_right">
                                <div class="outer">
                                    <h4>Premium Features</h4>
                                    <ul>
                                        <li>
                                            <b>22+ types of custom product fields.</b>
                                        </li>
                                        <li>
                                            <b>Conditional logic:</b> Show or hide some fields based on the value selected for other fields.
                                        </li>
                                        <li>
                                            <b>Fields based on variations:</b> Show or hide some fields based on the product variation selected.
                                        </li>
                                        <li>
                                            <b>Set price for fields:</b> Price can be set for all the fields available. The price can be a fixed value, percentage value of the product base price.
                                        </li>
                                        <li>
                                            <b>Custom price formula:</b> To calculate price using mathematical formula based on user input value, product quantity, product base price, and based on the prices of other fields as well.
                                        </li>
                                        <li>
                                            <b>WPML</b> and <b>Polylang</b> support.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar">
                        <div class="sidebar_top">
                            <h1>Upgrade to<br> Premium Now!</h1>
<!--                            <div class="price_side">-->
<!--                                For only<b> $29 </b>per site-->
<!--                            </div>-->
                            <div class="tagline_side">Supercharge Your WooCommerce <br>Stores  with our
                                light, fast <br>and feature-rich plugins
                            </div>
                            <div>
                                <a href="https://acowebs.com/woo-custom-product-addons/?ref=free-wcpa" target="_blank">Upgrade Now</a>
                            </div>

                        </div>

                        <div class="sidebar_bottom">
                            <ul>
                                <li>
                                    <b>22+ types of custom product fields.</b>
                                </li>
                                <li>
                                    <b>Conditional logic:</b> Show or hide some fields based on the value selected for other fields.
                                </li>
                                <li>
                                    <b>Fields based on variations:</b> Show or hide some fields based on the product variation selected.
                                </li>
                                <li>
                                    <b>Set price for fields:</b> Price can be set for all the fields available. The price can be a fixed value, percentage value of the product base price.
                                </li>
                                <li>
                                    <b>Custom price formula:</b> To calculate price using mathematical formula based on user input value, product quantity, product base price, and based on the prices of other fields as well.
                                </li>
                                <li>
                                    <b>WPML</b> and <b>Polylang</b> support.
                                </li>
                            </ul>
                        </div>
                        <div class="support">
                            <h3>Dedicated Support Team</h3>
                            <p>Our support is what makes us No.1. We are available round the clock for any support.</p>
                            <p><a href="https://wordpress.org/support/plugin/woo-custom-product-addons/" target="_blank">Submit a ticket</a></p>
                        </div>

                    </div>

                </div>
                <!-- .meta-box-sortables .ui-sortable -->

            </div>
            <!-- post-body-content -->


            <!-- #postbox-container-1 .postbox-container -->

        </div>
        <!-- #post-body .metabox-holder .columns-2 -->
        <div id="post-body" class="metabox-holder columns-2">

        </div>

        <br class="clear">
    </div>
    <!-- #poststuff -->

</div> <!-- .wrap -->
