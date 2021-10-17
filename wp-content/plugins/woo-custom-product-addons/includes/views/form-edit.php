<?php
$value = get_post_meta($object->ID, WCPA_FORM_META_KEY, true);

$ml = new WCPA_Ml();
$fb_class = "";


if ($ml->is_active()) {
    $my_default_lang = $ml->default_language();
    $my_current_lang = $ml->current_language();

    if ($ml->is_new_post($object->ID)) {
        if (!$ml->is_default_lan()) {
            echo '<p class="wcpa_editor_message">' . sprintf(__('You can\'t create new form in current language (%s). Please switch to your default language (%s) and try again'), $my_current_lang, $my_default_lang) . '</p>';
            $fb_class = 'wpml_fb wcpa-disable-new';
        }
    } else {
        if (empty($value) || $value == 'null') {
            if ($ml->is_duplicating($object->ID)) {
                //copy value from base form or from any existing lang
                $value = $ml->default_fb_meta($object->ID);
            }
        }

        if (!$ml->is_default_lan()) {
            echo '<p class="wcpa_editor_message">' . __('You can use this editor only for translating Labels, Values, Help Text, Placeholder and Conditional logic value. All the other configurations and parameters will be populating from the original form.', 'wcpa-text-domain') . '</p>';
            $fb_class = 'wpml_fb lan-';
        }
    }


}


wp_nonce_field('wcpa_meta_box_nonce', 'wcpa_box_nonce');
echo '<div id="wcpa_editor" class="' . $fb_class . '"></div>';
?>

<textarea  style="display:none" name="wcpa_fb-editor-json" id="wcpa_fb-editor-json"><?php echo $value; ?></textarea>



