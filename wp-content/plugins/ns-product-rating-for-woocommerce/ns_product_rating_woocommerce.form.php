
<div class="ns-rating-woocom-post-rate" id="ns-rating-woocom-post-rate-div-<?php echo $post->ID ?>">
    <div id="ns-rating-woocom-rating-container-<?php echo $post->ID ?>" class="ns-rating-woocom-rating">

        <form method="post" id="ns-rating-woocom-post-rate-<?php echo $post->ID ?>" class="ns-rating-woocom-form">
            <p class="ns-rating-woocom-div-loader">
                <img src="<?php echo plugins_url( 'asset/img/loading.gif', __FILE__ ) ?>" class="ns-rating-woocom-loading" />
            </p>
            <span class="ns-rating-woocom-fieldset">
                <input type="radio" id="star5-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="5" /><label class = "ns-rating-woocom-full" for="star5-<?php echo $post->ID ?>" title="Perfect - 5 stars"></label>
                <input type="radio" id="star4half-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="4.5" /><label class="ns-rating-woocom-half" for="star4half-<?php echo $post->ID ?>" title="Excellent - 4.5 stars"></label>
                <input type="radio" id="star4-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="4" /><label class = "ns-rating-woocom-full" for="star4-<?php echo $post->ID ?>" title="Very Good - 4 stars"></label>
                <input type="radio" id="star3half-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="3.5" /><label class="ns-rating-woocom-half" for="star3half-<?php echo $post->ID ?>" title="Good - 3.5 stars"></label>
                <input type="radio" id="star3-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="3" /><label class = "ns-rating-woocom-full" for="star3-<?php echo $post->ID ?>" title="Average - 3 stars"></label>
                <input type="radio" id="star2half-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="2.5" /><label class="ns-rating-woocom-half" for="star2half-<?php echo $post->ID ?>" title="More than enough - 2.5 stars"></label>
                <input type="radio" id="star2-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="2" /><label class = "ns-rating-woocom-full" for="star2-<?php echo $post->ID ?>" title="Poor - 2 stars"></label>
                <input type="radio" id="star1half-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="1.5" /><label class="ns-rating-woocom-half" for="star1half-<?php echo $post->ID ?>" title="Very Poor - 1.5 stars"></label>
                <input type="radio" id="star1-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="1" /><label class = "ns-rating-woocom-full" for="star1-<?php echo $post->ID ?>" title="Awful - 1 star"></label>
                <input type="radio" id="starhalf-<?php echo $post->ID ?>" name="rate<?php echo $post->ID ?>" value="0.5" /><label class="ns-rating-woocom-half" for="starhalf-<?php echo $post->ID ?>" title="Very Awful - 0.5 stars"></label>
            </span>

            <input type="hidden" id="post-id-<?php echo $post->ID ?>" name="post_id" value="<?php echo $post->ID ?>" />
            <?php if ( ! empty( $post_rate ) ) : ?>
            <input type="hidden" id="rate-id-place-<?php echo $post->ID ?>" name="rate_id" value="<?php echo number_format( $post_rate, 2, ',', '.' ); ?>" />
            <?php endif; ?>
        </form>
        <p class="ns-rating-woocom-rate-info" id="ns-rating-woocom-rate-info-<?php echo $post->ID ?>">
        <?php if ( ! empty( $post_rate ) ) : ?>
                <?php _e( 'Valoración', 'ns-product-rating-woocommerce' ) ?>: <b class="ns-rating-woocom-rate ns-rating-woocom-rate<?php echo $post->ID ?>"><?php echo number_format( $post_rate, 2, ',', '.' ) ?></b>
                <?php// _e( 'de', 'ns-product-rating-woocommerce' ) ?> <b class="ns-rating-woocom-rate-count ns-rating-woocom-rate-count<?php echo $post->ID ?>"><?php echo $rating_count ?></b> <?php echo _n( 'voto', 'votos', $rating_count, 'ns-product-rating-woocommerce' ) ?>.
        <?php endif; ?>
        <?php if ( empty( $post_rate ) ) : ?>
                <b class="ns-rating-woocom-rate ns-rating-woocom-rate<?php echo $post->ID ?>"></b>
                <b class="ns-rating-woocom-rate-count ns-rating-woocom-rate-count<?php echo $post->ID ?>"></b>
            <?php endif; ?>
        </p>
        <br/>
    </div>

</div>