<?php

/**
 * Server-side rendering of the `coblocks/social-profiles` block.
 *
 * @package CoBlocks
 */

/**
 * Renders the block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the block content.
 */
function coblocks_render_social_profiles_block( $attributes ) {

	global $post;

	// Get the featured image.
	if ( has_post_thumbnail() ) {
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
		$thumbnail    = $thumbnail_id ? current( wp_get_attachment_image_src( $thumbnail_id, 'large', true ) ) : '';
	} else {
		$thumbnail = null;
	}

	// Attributes.
	$text_align    = is_array( $attributes ) && isset( $attributes['textAlign'] ) ? "style=text-align:{$attributes['textAlign']}" : '';
	$border_radius = is_array( $attributes ) && isset( $attributes['borderRadius'] ) ? "border-radius: {$attributes['borderRadius']}px;" : '';
	$has_padding   = is_array( $attributes ) && isset( $attributes['padding'] ) ? 'has-padding' : '';

	$has_backround           = '';
	$background_color_class  = '';
	$custom_background_color = '';
	$has_color               = '';
	$text_color_class        = '';
	$custom_text_color       = '';
	$icon_size               = '';
	$padding                 = '';

	if ( isset( $attributes['className'] ) && strpos( $attributes['className'], 'is-style-mask' ) !== false ) {
		$has_backround           = is_array( $attributes ) && isset( $attributes['hasColors'] ) && ( isset( $attributes['backgroundColor'] ) || isset( $attributes['customBackgroundColor'] ) ) && ( $attributes['hasColors'] || ( $attributes['backgroundColor'] || $attributes['customBackgroundColor'] ) ) ? 'has-text-color' : '';
		$background_color_class  = is_array( $attributes ) && isset( $attributes['backgroundColor'] ) ? "has-{$attributes['backgroundColor']}-color" : false;
		$custom_background_color = is_array( $attributes ) && isset( $attributes['customBackgroundColor'] ) && isset( $attributes['hasColors'] ) && ( ! $attributes['hasColors'] && ! isset( $attributes['backgroundColor'] ) ) ? "color: {$attributes['customBackgroundColor']};" : '';
	} else {
		$has_backround           = is_array( $attributes ) && isset( $attributes['hasColors'] ) && ( isset( $attributes['backgroundColor'] ) || isset( $attributes['customBackgroundColor'] ) ) && ( $attributes['hasColors'] || ( isset( $attributes['backgroundColor'] ) || $attributes['customBackgroundColor'] ) ) ? 'has-background' : '';
		$background_color_class  = is_array( $attributes ) && isset( $attributes['backgroundColor'] ) ? "has-{$attributes['backgroundColor']}-background-color" : false;
		$custom_background_color = is_array( $attributes ) && isset( $attributes['customBackgroundColor'] ) && isset( $attributes['hasColors'] ) && ( ! $attributes['hasColors'] && ! isset( $attributes['backgroundColor'] ) ) ? "background-color: {$attributes['customBackgroundColor']};" : '';

		$has_color         = is_array( $attributes ) && isset( $attributes['hasColors'] ) && ( isset( $attributes['textColor'] ) || isset( $attributes['customTextColor'] ) ) && ( $attributes['hasColors'] || ( isset( $attributes['textColor'] ) || $attributes['customTextColor'] ) ) ? 'has-text-color' : '';
		$text_color_class  = is_array( $attributes ) && isset( $attributes['textColor'] ) ? "has-{$attributes['textColor']}-color" : false;
		$custom_text_color = is_array( $attributes ) && isset( $attributes['customTextColor'] ) && isset( $attributes['hasColors'] ) && ( ! $attributes['hasColors'] && ! isset( $attributes['textColor'] ) ) ? "color: {$attributes['customTextColor']};" : '';
	}

	if ( isset( $attributes['className'] ) && ( strpos( $attributes['className'], 'is-style-mask' ) !== false || strpos( $attributes['className'], 'is-style-circular' ) !== false ) ) {
		$icon_size = is_array( $attributes ) && isset( $attributes['iconSize'] ) ? "height:{$attributes['iconSize']}px;width: {$attributes['iconSize']}px;" : '';
	}

	if ( isset( $attributes['className'] ) && strpos( $attributes['className'], 'is-style-circular' ) !== false ) {
		$padding = is_array( $attributes ) && isset( $attributes['padding'] ) ? "padding:{$attributes['padding']}px;" : '';
	}

	// Supported social media platforms.
	$platforms = array(

		'facebook'  => array(
			'text' => esc_html__( 'Facebook', 'coblocks' ),
			'url'  => $attributes['facebook'],
		),
		'twitter'   => array(
			'text' => esc_html__( 'Twitter', 'coblocks' ),
			'url'  => $attributes['twitter'],
		),
		'instagram' => array(
			'text' => esc_html__( 'Instagram', 'coblocks' ),
			'url'  => $attributes['instagram'],
		),
		'pinterest' => array(
			'text' => esc_html__( 'Pinterest', 'coblocks' ),
			'url'  => $attributes['pinterest'],
		),
		'linkedin'  => array(
			'text' => esc_html__( 'Linkedin', 'coblocks' ),
			'url'  => $attributes['linkedin'],
		),

		'youtube'   => array(
			'text' => esc_html__( 'YouTube', 'coblocks' ),
			'url'  => $attributes['youtube'],
		),
		'yelp'      => array(
			'text' => esc_html__( 'Yelp', 'coblocks' ),
			'url'  => $attributes['yelp'],
		),
		'houzz'     => array(
			'text' => esc_html__( 'Houzz', 'coblocks' ),
			'url'  => $attributes['houzz'],
		),
	);

	// Start markup.
	$markup = '';

	foreach ( $platforms as $id => $platform ) {

		if ( isset( $attributes[ $id ] ) && $attributes[ $id ] ) {
			$markup .= sprintf(
				'<li>
					<a href="%1$s" class="wp-block-button__link wp-block-coblocks-social__button wp-block-coblocks-social__button--%8$s %3$s %7$s %9$s %10$s %13$s" title="%2$s" style="%4$s%6$s%11$s%12$s">
						<span class="wp-block-coblocks-social__icon" style="%5$s"></span>
						<span class="wp-block-coblocks-social__text">%2$s</span>
					</a>
				</li>',
				esc_url( $platform['url'] ),
				esc_html( $platform['text'] ),
				esc_attr( $has_backround ),
				esc_attr( $border_radius ),
				esc_attr( $icon_size ),
				esc_attr( $custom_background_color ),
				esc_attr( $background_color_class ),
				esc_attr( $id ),
				esc_attr( $has_color ),
				esc_attr( $text_color_class ),
				esc_attr( $custom_text_color ),
				esc_attr( $padding ),
				esc_attr( $has_padding )
			);
		}
	}

	// Build classes.
	$class = 'wp-block-coblocks-social wp-block-coblocks-social-profiles';

	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}

	if ( isset( $attributes['hasColors'] ) && $attributes['hasColors'] ) {
		$class .= ' has-colors';
	}

	if ( isset( $attributes['size'] ) && 'med' !== $attributes['size'] && ( isset( $attributes['className'] ) && strpos( $attributes['className'], 'is-style-mask' ) === false ) ) {
		$class .= ' has-button-size-' . $attributes['size'];
	}

	// Render block content.
	$block_content = sprintf(
		'<div class="%1$s" %2$s><ul>%3$s</ul></div>',
		esc_attr( $class ),
		esc_attr( $text_align ),
		$markup
	);

	return $block_content;
}

/**
 * Registers the block on server.
 */
function coblocks_register_social_profiles_block() {
	// Return early if this function does not exist.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type(
		'coblocks/social-profiles',
		array(
			'editor_script'   => 'coblocks-editor',
			'editor_style'    => 'coblocks-editor',
			'style'           => 'coblocks-frontend',
			'attributes'      => array(
				'className'             => array(
					'type' => 'string',
				),
				'hasColors'             => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'borderRadius'          => array(
					'type'    => 'number',
					'default' => 40,
				),
				'size'                  => array(
					'type'    => 'string',
					'default' => 'med',
				),
				'iconSize'              => array(
					'type'    => 'number',
					'default' => 22,
				),
				'padding'               => array(
					'type'    => 'number',
					'default' => 16,
				),
				'textAlign'             => array(
					'type' => 'string',
				),
				'backgroundColor'       => array(
					'type' => 'string',
				),
				'customBackgroundColor' => array(
					'type' => 'string',
				),
				'textColor'             => array(
					'type' => 'string',
				),
				'customTextColor'       => array(
					'type' => 'string',
				),
				'facebook'              => array(
					'type'    => 'string',
					'default' => '',
				),
				'twitter'               => array(
					'type'    => 'string',
					'default' => '',
				),
				'instagram'             => array(
					'type'    => 'string',
					'default' => '',
				),
				'pinterest'             => array(
					'type'    => 'string',
					'default' => '',
				),
				'linkedin'              => array(
					'type'    => 'string',
					'default' => '',
				),
				'youtube'               => array(
					'type'    => 'string',
					'default' => '',
				),
				'yelp'                  => array(
					'type'    => 'string',
					'default' => '',
				),
				'houzz'                 => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'render_callback' => 'coblocks_render_social_profiles_block',
		)
	);
}
add_action( 'init', 'coblocks_register_social_profiles_block' );
