<?php
/**
 * List/Grid widget
 */
class BeRocket_LGV_Widget extends WP_Widget 
{
	public function __construct() {
        parent::__construct("berocket_lgv_widget", "WooCommerce Grid/List View",
            array("description" => "Show grid/list view toggle buttons"));
    }
    /**
     * WordPress widget for display List/Grid buttons
     */
    public function widget($args, $instance)
    {
        if ( is_product_category() || is_shop() || @$instance['all_page'] ) {
            $BeRocket_LGV = BeRocket_LGV::getInstance();
            $options = $BeRocket_LGV->get_option();
            $lgv_options = $options['buttons_page'];
            $instance['title'] = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
            set_query_var( 'title', apply_filters( 'lgv_widget_title', @ $instance['title'] ) );
            set_query_var( 'position', @ $instance['position'] );
            set_query_var( 'padding', @ $instance['padding'] );
            set_query_var( 'custom_class', apply_filters( 'lgv_widget_custom_class', @ $lgv_options['custom_class'] ) );
            set_query_var( 'args', $args );
            echo $args['before_widget'];
            $BeRocket_LGV->br_get_template_part( apply_filters( 'lgv_widget_template', 'list-grid' ) );
            echo $args['after_widget'];
        }
	}
    /**
     * Update widget settings
     */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( @ $new_instance['title'] );
        if( @ $new_instance['all_page'] ) {
            $instance['all_page'] = 1;
        } else {
            $instance['all_page'] = 0;
        }
		$instance['position'] = strip_tags( @ $new_instance['position'] );
		$instance['padding']['top'] = strip_tags( @ $new_instance['padding']['top'] );
		$instance['padding']['bottom'] = strip_tags( @ $new_instance['padding']['bottom'] );
		$instance['padding']['left'] = strip_tags( @ $new_instance['padding']['left'] );
		$instance['padding']['right'] = strip_tags( @ $new_instance['padding']['right'] );
		return $instance;
	}
    /**
     * Widget settings form
     */
	public function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'all_page' => '', 'position' => '', 'padding' => array('top' => '', 'bottom' => '', 'left' => '', 'right' => '') ) );
		$title = @ strip_tags($instance['title']);
		?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( @ $title ); ?>" />
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('all_page'); ?>"><?php _e('Show on all pages'); ?></label>
            <input id="<?php echo $this->get_field_id('all_page'); ?>" name="<?php echo $this->get_field_name('all_page'); ?>" type="checkbox" value="1" <?php if( $instance['all_page'] ) echo 'checked'; ?>>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('position'); ?>"><?php _e('Position'); ?></label>
            <select id="<?php echo $this->get_field_id('position'); ?>" name="<?php echo $this->get_field_name('position'); ?>">
                <option value=""<?php if(!$instance['position']) echo ' selected'; ?>><?php _e('NONE'); ?></option>
                <option value="left"<?php if($instance['position'] == 'left') echo ' selected'; ?>><?php _e('Left'); ?></option>
                <option value="right"<?php if($instance['position'] == 'right') echo ' selected'; ?>><?php _e('Right'); ?></option>
            </select>
        </p>
		<p>
            <label><?php _e('Paddings'); ?></label>
            <label><?php _e('Top: '); ?><input class="widefat" name="<?php echo $this->get_field_name('padding'); ?>[top]" type="number" min="0" value="<?php echo @$instance['padding']['top'] ?>"></label>
            <label><?php _e('Bottom: '); ?><input class="widefat" name="<?php echo $this->get_field_name('padding'); ?>[bottom]" type="number" min="0" value="<?php echo @$instance['padding']['bottom'] ?>"></label>
            <label><?php _e('Left: '); ?><input class="widefat" name="<?php echo $this->get_field_name('padding'); ?>[left]" type="number" min="0" value="<?php echo @$instance['padding']['left'] ?>"></label>
            <label><?php _e('Right: '); ?><input class="widefat" name="<?php echo $this->get_field_name('padding'); ?>[right]" type="number" min="0" value="<?php echo @$instance['padding']['right'] ?>"></label>
        </p>
		<?php
	}
}
?>
