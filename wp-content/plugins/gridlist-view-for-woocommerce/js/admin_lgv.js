(function ($){
    $(document).ready( function () {

        /*BUTTONS*/

        $(document).on( 'click', '.berocket_lgv_button_test', function ( event ) {
            event.preventDefault();
        });
        $(document).on('click', '.br_grid_list_page_add', function(event) {
            event.preventDefault();
            var page_id = $('.br_grid_list_page_select').val();
            var list_style = $('.br_grid_list_style_select').val();
            var device = $('.br_grid_list_device_select').val();
            var page_name = '<span class="br_grid_list_style">'+$('.br_grid_list_style_select').find('option:selected').text()+'</span>';
            page_name += '<span class="br_grid_list_device">'+$('.br_grid_list_device_select').find('option:selected').text()+'</span>';
            page_name += $('.br_grid_list_page_select').find('option:selected').text();
            if ( ( device == 'pages' && $('.id_'+page_id).length == 0 ) 
              || ( device != 'pages' && $('.id_pages_'+page_id).length == 0 && $('.id_'+device+'_'+page_id).length == 0)
            ) {
                var html = '<li class="br_grid_list_page_id id_'+page_id+' id_'+device+'_'+page_id+'">';
                html += '<input type="hidden" name="br-list_grid-options[buttons_page]['+device+']['+page_id+']" value="'+list_style+'">';
                html += '<button type="button" class="button br_grid_list_page_remove">'
                html += page_name;
                html += '</button>';
                html += '</li>';
                $('.br_grid_list_pages').append($(html));
            }
        });
        $(document).on('click', '.br_grid_list_page_remove', function(event) {
            $(this).parents('.br_grid_list_page_id').remove();
        });

        /*PRODUCT COUNT*/

        $(document).on( 'change', '.br_lgv_product_count_spliter', function ( event ) {
            $('.br_lgv_product_count.splitpc').text( $(this).val() );
        });
        $(document).on( 'change', '.berocket_framework_menu_product-count .text_before, .berocket_framework_menu_product-count .text_after', function ( event ) {
            if ( $(this).hasClass( 'text_before' ) ) {
                $('.berocket_framework_menu_product-count span.text_before').text( $(this).val() );
            } else if ( $(this).hasClass( 'text_after' ) ) {
                $('.berocket_framework_menu_product-count span.text_after').text( $(this).val() );
            }
        });

        /*LIST STYLE*/

        $(document).on( 'click', '.br_lgv_selector label', function ( event ) {
            $(this).parent().parent().find($( '.br_lgv_selector_block' ) ).hide();
            $(this).parent().parent().find($( '.br_lgv_selector_block.'+$(this).prev().val() ) ).show();
            set_correct_height();
        });
        $(document).scroll( function ( event ) {
            if( $('.berocket_lgv_additional_data:visible').length > 0 ) {
                $block = $('.berocket_lgv_additional_data:visible');
                pos_top = $(document).scrollTop() - $block.parents('.br_lgv_liststyle').offset().top;
                pos_top += 40;
                if ( pos_top + $block.height() > $block.parents('.br_lgv_liststyle').height() ) {
                    pos_top = $block.parents('.br_lgv_liststyle').height() - $block.height();
                }
                if ( pos_top < 0 ) {
                    pos_top = 0;
                }
                $block.css( 'top', pos_top );
            }
        });
        
        $('.br_lgv_liststyle').on( 'click', '.berocket_lgv_additional_data a', function ( event ) {
            event.preventDefault();
        });
        $('.br_lgv_liststyle').on( 'mouseenter', '.lgv_highlight', function ( event ){
            event.stopPropagation();
            $('.lgv_highlight').removeClass( 'lgv_yellow' );
            $(this).addClass( 'lgv_yellow' );
        });
        $('.br_lgv_liststyle').on( 'mouseleave', '.lgv_highlight', function ( event ){
            event.stopPropagation();
            $(this).removeClass( 'lgv_yellow' );
            to_element = event.toElement || event.relatedTarget;
            $(to_element).trigger( 'mouseenter' );
        });
        $('.br_lgv_liststyle').on( 'click', '.lgv_highlight', function ( event ){
            event.stopPropagation();
            $(this).parents('.brlgv_style_block').find('.lgv_editor').hide(300);
            $(this).parents('.brlgv_style_block').find('.'+$(this).data('editor')).show(300);
        });
        function set_correct_height() {
            $('.br_lgv_liststyle_preview').each( function ( i, o ) {
                $(o).css( 'height', $(o).find('.berocket_lgv_additional_data').height() );
                $(o).parent().css( 'min-height', $(o).find('.berocket_lgv_additional_data').height() );
            });
        }
        $(document).on('click', '[data-block="berocket_framework_menu_list-style"]', set_correct_height);
        setTimeout(function() { 
            set_correct_height();
            $('.br_lgv_liststyle_preview').each( function ( i, o ) {
                $('.lgv_display_none').hide();
            });
        }, 500);
        $(window).resize( set_correct_height );
        $('.br_lgv_liststyle_preview').on( 'click', '.lgv_toggle_next', function ( event ) {
            setTimeout(set_correct_height, 300);
        });
        $('.color-changer').each(function (i,o){
            $(o).css('backgroundColor', $(o).data('color'));
            $(o).colpick({
                layout: 'hex',
                submit: 0,
                color: $(o).data('color'),
                onChange: function(hsb,hex,rgb,el,bySetColor) {
                    $(el).css('backgroundColor', '#'+hex).parent().css('backgroundColor', '#'+hex);
                }
            })
        });
        $('.color-changer').click(function(event) {
            event.preventDefault();
        });

        /*STYLER*/

        $(document).on( 'change', '.lgv_admin_settings input, .lgv_admin_settings select', function ( event ) {
            event.preventDefault();
            set_buttons( $(this), $(this).parents('.lgv_editor').data('button_class') );
            set_correct_height();
        });
        $(document).on( 'click', '.set_default', function ( event ) {
            $(this).parents( 'div' ).first().find('select, input, textarea').filter(':not(.br_colorpicker_value)').each( function ( i, o ) {
                if( $(o).is('.br_colorpicker_default') ) {
                    $(o).trigger('click');
                } else if ( ! $(o).hasClass( 'set_default' ) ) {
                    
                    if( $(o).attr('type') == 'checkbox' ) {
                        if( $(o).data('default') == 1 ) {
                            $(o).prop( 'checked', true );
                        } else {
                            $(o).prop( 'checked', false );
                        }
                    } else {
                        $(o).val( $(o).data('default') ).trigger( 'change' );
                    }
                }
            });
        });
        $(document).on( 'click', '.set_all_default', function ( event ) {
            $(this).parent().find( '.set_default, .br_colorpicker_default' ).trigger( 'click' );
        });
        $(document).on( 'mousemove', '.box-shadow .x, .box-shadow .y, .box-shadow .radius, .box-shadow .size', function ( event ) {
            $(this).next().find( 'span.value_container' ).text( $(this).val() );
        });
        $('.colorpicker_field_lgv').each(function (i,o){
            $(o).css('backgroundColor', $(o).data('color'));
            $(o).colpick({
                layout: 'hex',
                submit: 0,
                color: $(o).data('color'),
                onChange: function(hsb,hex,rgb,el,bySetColor) {
                    $(el).css('backgroundColor', '#'+hex).next().val('#'+hex).trigger('change');
                }
            })
        });
        $('.list_grid_submit_form').on( 'submit', function( event ) {
            $('.berocket_lgv_button_test_normal_styles').val($( '.berocket_lgv_button_test.normal' ).attr('style'));
            $('.berocket_lgv_button_test_hover_styles').val($( '.berocket_lgv_button_test.hover' ).attr('style'));
            $('.berocket_lgv_button_test_selected_styles').val($( '.berocket_lgv_button_test.selected' ).attr('style'));
            
            $('.berocket_lgv_product_count_normal_styles').val($( '.br_lgv_product_count.normalpc' ).attr('style'));
            $('.berocket_lgv_product_count_hover_styles').val($( '.br_lgv_product_count.hoverpc' ).attr('style'));
            $('.berocket_lgv_product_count_selected_styles').val($( '.br_lgv_product_count.selectedpc' ).attr('style'));
            $('.berocket_lgv_product_count_split_styles').val($( '.br_lgv_product_count.splitpc' ).attr('style'));
            $('.berocket_lgv_product_count_text_styles').val($( '.br_lgv_product_count.textpc' ).attr('style'));
            $('.br_lgv_temp_styles_input').remove();
            if ( $(this).find( '.br_lgv_liststyle' ).length > 0 ) {
                $('.lgv_highlight, .lgv_no_highlight').each( function ( i, o ) {
                    $button = $('<input class="br_lgv_temp_styles_input" type="hidden" name="br-list_grid-options[liststyle][button_style]['+i+'][style]" value="'+$(o).attr('style')+'">');
                    $(this).append( $button );
                    $button = $('<input class="br_lgv_temp_styles_input" type="hidden" name="br-list_grid-options[liststyle][button_style]['+i+'][button]" value="'+$(o).data('button')+'">');
                    $(this).append( $button );
                    if ( $(o).data('modifier') != 'none' ) {
                        modifier = $(o).data('modifier');
                    } else {
                        modifier = '';
                    }
                    $button = $('<input class="br_lgv_temp_styles_input" type="hidden" name="br-list_grid-options[liststyle][button_style]['+i+'][modifier]" value="'+modifier+'">');
                    $(this).append( $button );
                });
            }
        });
        $(document).on( 'click', '.lgv_toggle_next', function ( event ) {
            event.preventDefault();
            if( $(this).data('select') == 'parent' ) {
                $block = $(this).parent();
            } else {
                $block = $(this);
            }
            if( $(this).data('find') == 'nextchild' ) {
                $block = $block.next().child();
            } else {
                $block = $block.next();
            }
            if( ! $(this).parents('.berocket_lgv_additional_data').length ) {
                $("html, body").animate({ scrollTop: ( $(this).offset().top - 100 )+"px" });
            }
            $block.toggle(200);
        });
        $(document).on( 'change', '.lgv_class_set', function ( event ) {
            default_class = $(this).data( 'default_class' );
            $( '.'+default_class ).removeClass().addClass( default_class ).addClass( $(this).val() );
        });
        $(document).on( 'change', '.lgv_img_advanced_float_value', function ( event ) {
            setTimeout(function(){ set_margin_text_block( '.lgv_img_advanced', '.lgv_img_advanced + div', $('.lgv_img_advanced_width_value').val(), $('.lgv_img_advanced_width_ex').val() ); }, 20);
        });
        $(document).on( 'change', '.lgv_img_advanced_width_value, .lgv_img_advanced_width_ex', function ( event ) {
            setTimeout(function(){ set_margin_text_block( '.lgv_img_advanced', '.lgv_img_advanced + div', $('.lgv_img_advanced_width_value').val(), $('.lgv_img_advanced_width_ex').val() ); }, 20);
        });
        setTimeout(function(){ set_margin_text_block( '.lgv_img_advanced', '.lgv_img_advanced + div', $('.lgv_img_advanced_width_value').val(), $('.lgv_img_advanced_width_ex').val() ); }, 20);
        jQuery('.lgv_products_count_preview').click(function(event){event.preventDefault()});
    });
})(jQuery);
function set_margin_text_block( $block_float, $block_text, value, ex ) {
    if( ex != 'initial' && ex != 'inherit' ) {
        width = value+ex;
        $block_float = jQuery($block_float);
        $block_text  = jQuery($block_text);
        $block_text.css('margin', 0);
        if ( $block_float.css('float') == 'left' ) {
            $block_text.css('margin-left', width);
        } else if ( $block_float.css('float') == 'right' ) {
            $block_text.css('margin-right', width);
        }
        $block_text.parent().hide().show(0);
    }
}
function set_buttons($button, button_class) {
    value = $button.val();
    if ( $button.data('type') == 'int' ) {
        value = parseInt(value);
    } else if ( $button.data('type') == 'float' ) {
        value = parseFloat(value);
    }
    if ( value || value === 0 ) {
        $button.val( value );
        selector = button_class;
        if ( $button.parents('.lgv_editor_info').data('button_type') != 'all' ) {
            selector += '.'+$button.parents('.lgv_editor_info').data('button_type');
        }
        if ( $button.parents( 'div.berocket_lgv_style_data_block' ).first().data( 'type' ) == 'box-shadow' ) {
            seted_value = '';
            jQuery( 'div.box-shadow.'+$button.parents('.lgv_editor_info').data('button_type') ).each( function ( i, o ) {
                if ( i != 0 ) {
                    seted_value += ',';
                }
                if ( jQuery(o).find('.inset').prop('checked') ) {
                    seted_value += 'inset ';
                }
                seted_value += jQuery(o).find('.x').val()+'px ';
                seted_value += jQuery(o).find('.y').val()+'px ';
                seted_value += jQuery(o).find('.radius').val()+'px ';
                seted_value += jQuery(o).find('.size').val()+'px ';
                seted_value += jQuery(o).find('.color').val();
            });
        } else if ( $button.data('type') == 'color' ) {
            if ( jQuery( '.'+$button.parents('.lgv_editor_info').data('button_type')+'_'+$button.data('option')+'_value').length > 1 ) {
                seted_value = jQuery( '.'+$button.parents('.lgv_editor_info').data('button_type')+'_'+$button.data('option')+'_value').val()+' linear-gradient('
                jQuery( '.'+$button.parents('.lgv_editor_info').data('button_type')+'_'+$button.data('option')+'_value').each( function ( i, o ) {
                    if( i == 0 ) {
                        seted_value += jQuery(o).val();
                    } else {
                        seted_value += ','+jQuery(o).val();
                    }
                });
                seted_value += ')';
            } else {
                seted_value = $button.val();
            }
        } else {
            seted_value = jQuery( '.'+$button.parents('.lgv_editor_info').data('button_type')+'_'+$button.data('option')+'_value').val();
            if ( jQuery( '.'+$button.parents('.lgv_editor_info').data('button_type')+'_'+$button.data('option')+'_ex').hasClass($button.parents('.lgv_editor_info').data('button_type')+'_'+$button.data('option')+'_ex') ) {
                ex_val = jQuery( '.'+$button.parents('.lgv_editor_info').data('button_type')+'_'+$button.data('option')+'_ex').val();
                if ( ex_val == 'initial' || ex_val == 'inherit' ) {
                    seted_value = ex_val;
                } else {
                    seted_value += ex_val;
                }
            }
        }
        jQuery(selector).css( $button.data('option'), seted_value );
        $button.next().find( 'span.value_container' ).text( $button.val() );
    }
}
function add_to_cart_position( $button ) {
    jQuery('.lgv_addtocart_pos').hide();
    jQuery('.lgv_pos_'+$button.val()).show();
}
function out_of_stock_position( $button ) {
    jQuery('.lgv_out_of_stock_button').hide();
    jQuery('.lgv_out_of_stock_'+$button.val()).show();
}
