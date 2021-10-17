<?php

if (!defined('ABSPATH'))
    exit;

class WCPA_Form_Editor {

    /**
     * The single instance of Custom product options.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var 	object
     * @access  public
     * @since 	1.0.0
     */
    public $parent = null;

    /**
     * Constructor function
     */
    public function __construct() {

        add_action("add_meta_boxes", array($this, 'add_custom_meta_box'));
        add_action("save_post", array($this, 'cd_meta_box_save'));
    }

    public function cd_meta_box_save($post_id) {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        // if our nonce isn't there, or we can't verify it, bail
        if (!isset($_POST['wcpa_box_nonce']) || !wp_verify_nonce($_POST['wcpa_box_nonce'], 'wcpa_meta_box_nonce'))
            return;

        // if our current user can't edit this post, bail
        if (!current_user_can('edit_post', $post_id))
            return;
        // now we can actually save the data
        $allowed = array(
            'a' => array(// on allow a tags
                'href' => true, // and those anchors can only have href attribute
                'target' => true,
                'class' => true// and those anchors can only have href attribute
            ),
            'b' => array('style' => true),
            'strong' => array('style' => true),
            'i' => array('style' => true),
            'img' => array('style' => true, 'class' => true, 'src' => true),
            'span' => array('style' => true, 'class' => true)
        );

        $ml = new WCPA_Ml();

        // Make sure your data is set before trying to save it
        if (isset($_POST['wcpa_fb-editor-json'])) {

            $fb_data = json_decode(wp_unslash($_POST['wcpa_fb-editor-json']));

            if ($fb_data && is_array($fb_data)) {
                foreach ($fb_data as $d) {
                    if (isset($d->label) && ($d->type == 'paragraph' || $d->type == 'header')) {
                        $d->label = wp_kses($d->label, $allowed);
                    } else if (isset($d->label)) {
                        $d->label = wp_kses($d->label, array());
                    }

                    if (isset($d->description)) {
                        $d->description = wp_kses($d->description, $allowed);
                    }
                    if (isset($d->values)) {
                        foreach ($d->values as $v) {
                            if (isset($v->label)) {
                                $v->label = wp_kses($v->label, array());
                            }
                        }
                    }
                }
            }

            $fb_data_json = wp_slash(json_encode($fb_data));

            update_post_meta($post_id, WCPA_FORM_META_KEY, $fb_data_json);
        }

        if ($ml->is_active()) {
            $ml->sync_data($post_id);
        }


        delete_transient(WCPA_PRODUCTS_TRANSIENT_KEY);
    }

    public function add_custom_meta_box() {
        add_meta_box("wcpa_form_builder_box", "Build your form", array($this, 'wcpa_meta_box_markup'), WCPA_POST_TYPE, "normal", "high", null);
        add_meta_box("wcpa_form_banner", "Banner", array($this, 'wcpa_form_banner'), WCPA_POST_TYPE, "normal", "high", null);

    }



    function wcpa_meta_box_markup($object) {
        WCPA_Backend::view('form-edit', ['object' => $object]);
    }
    function wcpa_form_banner($object) {
        WCPA_Backend::view('form-meta', []);
    }


    function wcpa_meta_box_categories($object) {
        WCPA_Backend::view('form-edit_categories', ['object' => $object]);
    }

    public static function instance($file = '', $version = '1.0.0') {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

}
