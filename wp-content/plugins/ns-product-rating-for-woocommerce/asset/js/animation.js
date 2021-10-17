jQuery(document).ready(function($){

$('.ns-rating-woocom-fieldset').mouseenter(function() {
        $("[id^='starhalf']").prop("checked", false);
        $("[id^='star']").prop("checked", false);
    });

$('.ns-rating-woocom-fieldset').mouseleave(function() {
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
});

});

