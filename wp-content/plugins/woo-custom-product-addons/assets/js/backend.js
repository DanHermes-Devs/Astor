var wcpa_functions = {};

jQuery(function ($) {

    var $fb = $(document.getElementById("wcpa_editor"));
    var formData = $("#wcpa_fb-editor-json").text();

    if($fb.length){
        var formBuilder = $fb.formBuilder({
            "formData": formData,
            'disableFields': ['button', 'autocomplete'],
            showActionButtons: false,
            controlOrder: [
                'text',
                'textarea',
                'select',
                'checkbox-group',
                'radio-group',
                'date',
                'number',
                'color'
            ],
            disabledAttrs: ["access", "style", "toggle", "other", "inline", "description", "multiple"],

        });
    }

    $("#publish,#save-post").click(function () {
        if ($("#wcpa_fb-editor-json").length) {
            $("#wcpa_editor input").attr('disabled', 'disabled');
            $("#wcpa_editor select").attr('disabled', 'disabled');
            $("#wcpa_fb-editor-json").text(formBuilder.formData);
        }
    });
    document.addEventListener('fieldAdded', function () {
        $("#wcpa_fb-editor-json").text(formBuilder.formData);
    });
    document.addEventListener('fileRemoved', function () {
        $("#wcpa_fb-editor-json").text(formBuilder.formData);
    });

    /* order meta js */
    var wcpa_meta_boxes_order_items = {

        init: function () {
            $("#woocommerce-order-items").on("click", 'a.wcpa_delete-order-item', this.remove_item);
            $("#woocommerce-order-items").on("change blur", '.wcpa_has_price', this.update_total_price);

        },
        remove_item: function (e) {
            var answer = window.confirm('It will remove this item and will recalculate the price');

            if (answer) {
                wcpa_meta_boxes_order_items.update_total_price.call(this);
                var el_price = parseFloat($(this).parents('.item_wcpa').find('.wcpa_has_price').val());
                if (!isNaN(el_price)) {
                    wcpa_meta_boxes_order_items.set_total_price.call(this, -el_price);
                    wcpa_meta_boxes_order_items.set_subtotal_price.call(this, -el_price);
                }

                $(this).parents('.item_wcpa').remove();
            }

            return false;
        },
        update_total_price: function () {

            var $row = $(this).parents('tr.item');
            var original_price = 0;
            var updated_price = 0;
            $(".wcpa_has_price", $row).each(function () {

                updated_price += (!isNaN(parseFloat($(this).val()))) ? parseFloat($(this).val()) : 0;
                original_price += (!isNaN(parseFloat($(this).data('price')))) ? parseFloat($(this).data('price')) : 0;
                $(this).data('price', parseFloat($(this).val()));
            });
            if ((original_price - updated_price) != 0) {
                wcpa_meta_boxes_order_items.set_total_price.call(this, parseFloat((updated_price - original_price)))
            }

        },
        set_total_price: function (value) {
            var $row = $(this).parents('tr.item');
            var line_total = $('input.line_total', $row);
            line_total.attr('data-total', parseFloat(line_total.attr('data-total')) + value);
            $("input.quantity", $row).trigger("change");
        },
        set_subtotal_price: function (value) {
            var $row = $(this).parents('tr.item');
            var line_subtotal = $('input.line_subtotal', $row);
            line_subtotal.attr('data-subtotal', parseFloat(line_subtotal.attr('data-subtotal')) + value);
            $("input.quantity", $row).trigger("change");
        },

        block: function () {
            $('#woocommerce-order-items').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        },

        unblock: function () {
            $('#woocommerce-order-items').unblock();
        }

    };
    wcpa_meta_boxes_order_items.init();


    $(".wcpa_g_set_tabs").on('click', 'a', function (e) {
        e.preventDefault();
        $(".wcpa_tabcontent").not($(this).attr('href')).hide();
        $(".wcpa_g_set_tabs .active").removeClass('active');
        $(this).addClass('active');
        $($(this).attr('href')).show();
    });

    $('#the-list').on('click', 'a.wcpa_duplicate_form', function (e) {
        e.preventDefault();
        // Create the data to pass
        var data = {
            action: 'wcpa_duplicate_form',
            original_id: $(this).data('postid'),
            wcpa_nonce: $(this).data('nonce')
        };

        $.post(ajaxurl, data, function (response) {
            var location = window.location.href;
            if (location.split('?').length > 1) {
                location = location + '&wcpa_duplicated=' + response;
            } else {
                location = location + '?wcpa_duplicated=' + response;
            }
            window.location.href = location;
        });
    });

});
