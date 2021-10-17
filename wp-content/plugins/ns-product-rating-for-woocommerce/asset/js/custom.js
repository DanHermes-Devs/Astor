jQuery(document).ready(function($){
    var id_product;
    var vote_hidden;


    $("[id^='post-id-']").each(function(){
        id_product = $(this).val();
        vote_hidden = $("#rate-id-place-" + id_product).val()

        if(vote_hidden == '0,00'){
        }else if(vote_hidden > '0,00' && vote_hidden <= '0,50'){
            $("#starhalf-" + id_product).prop("checked", true);
        }else if(vote_hidden > '0,50' && vote_hidden <= '1,20'){
            $("#star1-" + id_product).prop("checked", true);
        }else if(vote_hidden > '1,20' && vote_hidden <= '1,70'){
            $("#star1-" + id_product).prop("checked", true);
            $("#star1half-" + id_product).prop("checked", true);
        }else if(vote_hidden > '1,70' && vote_hidden <= '2,20'){
            $("#star2-" + id_product).prop("checked", true);
        }else if(vote_hidden > '2,20' && vote_hidden <= '2,70'){
            $("#star2-" + id_product).prop("checked", true);
            $("#star2half-" + id_product).prop("checked", true);
        }else if(vote_hidden > '2,70' && vote_hidden <= '3,20'){
            $("#star3-" + id_product).prop("checked", true);
        }else if(vote_hidden > '3,20' && vote_hidden <= '3,70'){
            $("#star3-" + id_product).prop("checked", true);
            $("#star3half-" + id_product).prop("checked", true);
        }else if(vote_hidden > '3,70' && vote_hidden <= '4,20'){
            $("#star4-" + id_product).prop("checked", true);
        }else if(vote_hidden > '4,20' && vote_hidden <= '4,70') {
            $("#star4-" + id_product).prop("checked", true);
            $("#star4half-" + id_product).prop("checked", true);
        }else if(vote_hidden > '4,70'){
            $("#star5-" + id_product).prop("checked", true);
        }
    });



    //Configuro il button radio in modo che faccia il submit del form al click
    $('input[name^=rate]').click(function() {
        //Recupero l'id number dall'elemento che comincia con rate e poi lo passo all'id del form per eseguirne il submit
        $('#ns-rating-woocom-post-rate-'+ $(this).attr('name').replace('rate', '')).submit(); //TRIGGER FORM SUBMIT EVENT
    });

    $("[id^='ns-rating-woocom-post-rate-']").on( 'submit', function(e){

        e.preventDefault();

        var id_prod = $(this).serializeArray()[1]['value'];
        var form = $('#ns-rating-woocom-post-rate-' + id_prod),
            loading = form.find('img.ns-rating-woocom-loading'),
            rate = form.find('input[name=rate' + id_prod + ']:checked').val();

        // messaggio di errore se la votazione non è stata espressa
        if ( typeof rate == 'undefined' ) {
            if ( form.parent().find('p.ns-rating-woocom-feedback').length == 0 ) {
                $('#ns-rating-woocom-rating-container-' + id_prod)
                    .addClass('ns-rating-woocom-feedback')
                    .css('font-weight','bold')
                    .css('color','#aa0000')
                    .text('You have not given your feedback!')
                    .insertBefore( form.parent() );
            }
            return;
        }

            // visualizza l'iconcina di caricamento
            loading.removeClass("ns-rating-woocom-loading");
            loading.addClass("ns-rating-woocom-loading-show");

            $.post(

                // l'indirizzo a cui fare la richiesta, wp-admin/admin-ajax.php
                ns_product_rating_woocommerce_vars.ajaxurl,
                {
                    // il nome dell'action che utilizzeremo con il gancio wp_ajax_
                    action : 'add_post_rating',

                    // il voto che ha scelto l'utente
                    rate : form.find('input[name=rate' + id_prod + ']:checked').val(),

                    // il post ID per memorizzare il voto nel post corrente
                    post_id : form.find('#post-id-' + id_prod).val(),

                    // controllo di sicurezza tramite il nonce
                    ns_product_rating_woocommerce_nonce : ns_product_rating_woocommerce_vars.nonce,

                    ns_call_failed : false
                },

                function( response ) {

                    loading.hide();
                    if(response == 0){
                        $('#ns-rating-woocom-post-rate-div-' + id_prod).find('form#ns-rating-woocom-post-rate-' + id_prod).remove();
                        $(' #ns-rating-woocom-rate-info-' + id_prod)
                            .addClass('ns-rating-woocom-feedback')
                            .css('font-weight','bold')
                            .css('color','#FF0000')
                            .text( 'Sólo se permite una valoración cada 24 horas' );
                    }else{
                        $('#ns-rating-woocom-post-rate-div-' + id_prod).find('p.ns-rating-woocom-feedback, p.ns-rating-woocom-form, form#ns-rating-woocom-post-rate-' + id_prod).remove();

                        // mostro il messaggio di avvenuto salvataggio della votazione
                            $('#ns-rating-woocom-rating-container-' + id_prod)
                                .addClass('ns-rating-woocom-feedback')
                                .css('font-weight','bold')
                                .css('color','#00aa00')
                                .text( response.feedback )
                                .insertAfter( $('#ns-rating-woocom-post-rate-div-' + id_prod + ' #ns-rating-woocom-rate-info-' + id_prod) );

                        // aggiorno i valori rispettivamente per la votazione complessiva e il numero di voti
                        $('#ns-rating-woocom-rate-info-' + id_prod).find('b.ns-rating-woocom-rate' + id_prod).text( response.rate );
                        $('#ns-rating-woocom-rate-info-' + id_prod).find('b.ns-rating-woocom-rate-count' + id_prod).text( response.count );
                    }

                },

                // definisco che il formato dell'output è JSON
                'json'
            );



    });

    // Add Color Picker to all inputs that have 'color-field' class

});