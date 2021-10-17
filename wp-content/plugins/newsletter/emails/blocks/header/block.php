<?php
/*
 * Name: Header
 * Section: header
 * Description: Default header with company info
 */

$default_options = array(
    'font_family' => $font_family,
    'font_size' => 14,
    'font_color' => '#444444',
    'font_weight' => 'normal',
    'block_background' => '#ffffff',
    'block_padding_top' => 15,
    'block_padding_bottom' => 15,
    'block_padding_left' => 15,
    'block_padding_right' => 15
);
$options = array_merge($default_options, $options);

if (empty($info['header_logo']['id'])) {
    $image = false;
} else {
    $image = tnp_media_resize($info['header_logo']['id'], array(200, 80));
    if (is_wp_error($image)) {
        $image = false;
    }
}

$empty = empty($info['header_logo']['id']) && empty($info['header_sub']) && empty($info['header_title']);
?>

<?php if ($empty) { ?>
    <p>Please, set your company info.</p>
<?php } else { ?>
    <style>
        .header-text {
            padding: 10px; 
            font-size: <?php echo $options['font_size'] ?>px; 
            font-family: <?php echo $options['font_family'] ?>; 
            font-weight: <?php echo $options['font_weight'] ?>; 
            color: <?php echo $options['font_color'] ?>;
            text-decoration: none;
            line-height: normal;
        }
        .header-title {
            font-size: <?php echo $options['font_size'] * 1.2 ?>px; 
            font-family: <?php echo $options['font_family'] ?>; 
            font-weight: <?php echo $options['font_weight'] ?>; 
            color: <?php echo $options['font_color'] ?>;
            text-decoration: none;
            line-height: normal;
        }
        .header-logo {
            font-family: <?php echo $options['font_family'] ?>; 
            line-height: normal;
            font-weight: <?php echo $options['font_weight'] ?>;
            color: <?php echo $options['font_color'] ?>;
        }
        .header-logo-img {
            display: block; 
            max-width: 100%
        }
    </style>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <?php if ($image) { ?>
                <td align="left" width="50%" inline-class="header-logo">
                    <a href="<?php echo home_url() ?>" target="_blank">
                        <img alt="<?php echo esc_attr($info['header_title']) ?>" src="<?php echo $image ?>" class="header-logo-img" border="0">
                    </a>
                </td>             
            <?php } else { ?>
                <td align="left" width="50%" inline-class="header-logo">
                    <a href="<?php echo home_url() ?>" target="_blank" class="header-title">
                        <?php echo esc_attr($info['header_title']) ?>
                    </a>
                </td>
            <?php } ?>


            <td width="50%" align="right" class="mobile-hide" inline-class="header-text">
                <?php echo $info['header_sub'] ?>
            </td>
        </tr>
    </table>

<?php } ?>