<?php

/**
 * Foodbakery_restaurent_message Class
 *
 * @package Foodbakery
 */
if ( ! class_exists( 'Foodbakery_restaurent_message' ) ) {

    /**
      Foodbakery_contact class used to implement the custom contact widget.
     */
    class Foodbakery_restaurent_message extends WP_Widget {

        /**
         * Sets up a new foodbakery restaurent message widget instance.
         */
        public function __construct() {
            global $foodbakery_static_text;
			parent::__construct(
                    'foodbakery_restaurent_message', // Base ID
                    __( 'Foodbakery: Restaurent Message', 'foodbakery' ), // Name
                    array( 'classname' => '', 'description' => __( 'Foodbakery Restaurent Message', 'foodbakery' ), ) // Args
            );
        }

        /**
         * Outputs the foodbakery contact widget settings form.
         *
         * @param array $instance current settings.
         */
        function form( $instance ) {
            global $foodbakery_var_form_fields, $foodbakery_var_html_fields, $foodbakery_var_static_text;

            $cs_rand_id = rand( 23789, 934578930 );
            $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'contact_code' => '' ) );
            $title = $instance['title'];
            $description = isset( $instance['description'] ) ? esc_attr( $instance['description'] ) : '';
			$button_label = isset( $instance['button_label'] ) ? esc_attr( $instance['button_label'] ) : '';
			$button_url = isset( $instance['button_url'] ) ? esc_attr( $instance['button_url'] ) : '';
			$widget_bg_color = isset( $instance['widget_bg_color'] ) ? esc_attr( $instance['widget_bg_color'] ) : '#00a474';

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_widget_title' ),
                'desc' => '',
                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_widget_title_desc' ),
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr( $title ),
                    'classes' => '',
                    'cust_id' => foodbakery_allow_special_char( $this->get_field_name( 'title' ) ),
                    'cust_name' => foodbakery_allow_special_char( $this->get_field_name( 'title' ) ),
                    'return' => true,
                    'required' => false,
                ),
            );
            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_widget_desc' ),
                'desc' => '',
                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_widget_desc_hint' ),
                'echo' => true,
                'field_params' => array(
                    'std' => esc_textarea( $description ),
                    'classes' => 'textarea-field',
                    'cust_id' => foodbakery_allow_special_char( $this->get_field_name( 'description' ) ),
                    'cust_name' => foodbakery_allow_special_char( $this->get_field_name( 'description' ) ),
                    'return' => true,
                ),
            );

            $foodbakery_var_html_fields->foodbakery_var_textarea_field( $foodbakery_opt_array );
			
			$foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_widget_button_label' ),
                'desc' => '',
                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_widget_button_label_hint' ),
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr( $button_label ),
                    'classes' => '',
                    'cust_id' => foodbakery_allow_special_char( $this->get_field_name( 'button_label' ) ),
                    'cust_name' => foodbakery_allow_special_char( $this->get_field_name( 'button_label' ) ),
                    'return' => true,
                    'required' => false,
                ),
            );
            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
			
			$foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_widget_button_url' ),
                'desc' => '',
                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_widget_button_url_hint' ),
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr( $button_url ),
                    'classes' => '',
                    'cust_id' => foodbakery_allow_special_char( $this->get_field_name( 'button_url' ) ),
                    'cust_name' => foodbakery_allow_special_char( $this->get_field_name( 'button_url' ) ),
                    'return' => true,
                    'required' => false,
                ),
            );
            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				wp_enqueue_style('wp-color-picker');
				wp_enqueue_script('wp-color-picker');
				?>
				<script type="text/javascript">
					jQuery(document).ready(function () {
						jQuery('.bg_color').wpColorPicker();
					});

				</script>
				<?php
			}
			$foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_widget_bg_color' ),
                'desc' => '',
                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_widget_bg_color_hint' ),
                'echo' => true,
                'field_params' => array(
                    'std' => $widget_bg_color,
                    'classes' => 'bg_color',
                    'cust_id' => foodbakery_allow_special_char( $this->get_field_name( 'widget_bg_color' ) ),
                    'cust_name' => foodbakery_allow_special_char( $this->get_field_name( 'widget_bg_color' ) ),
                    'return' => true,
                    'required' => false,
                ),
            );
            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
			
		}

        /**
         * Handles updating settings for the current foodbakery contact widget instance.
         *
         * @param array $new_instance New settings for this instance as input by the user.
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['title'] = sanitize_text_field($new_instance['title']);
            $instance['description'] = wp_kses_post($new_instance['description']);
			$instance['button_label'] = $new_instance['button_label'];
			$instance['button_url'] =  $new_instance['button_url'] ;
			$instance['widget_bg_color'] =  $new_instance['widget_bg_color'] ;
            return $instance;
        }

        /**
         * Outputs the content for the current foodbakery contact widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current contact widget instance.
         */
        function widget( $args, $instance ) {

            extract( $args, EXTR_SKIP );
            global $wpdb, $post, $foodbakery_var_options;
            $title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
			$title = htmlspecialchars_decode( stripslashes( $title ) );
            $description = empty( $instance['description'] ) ? '' : $instance['description'];
			$button_label = empty( $instance['button_label'] ) ? '' : $instance['button_label'];
			$button_url = empty( $instance['button_url'] ) ? '#' : $instance['button_url'];
			$widget_bg_color = empty( $instance['widget_bg_color'] ) ? '' : $instance['widget_bg_color'];
			$bg_color = $output = '';
			if( $widget_bg_color != '' ){
				$bg_color = 'style="background-color: '. $widget_bg_color .';"';
			}
			if( $title != '' || $description != '' || $button_label != '' ){
				$output .= '<div class="message-box" '. $bg_color .'>'; 
					if( $title != '' ){
						$output .= '<strong>'. $title .'</strong>';
					}
					if( $description != '' ){
						$output .= '<span>'. $description .'</span>';
					}
					if( $button_label != '' ){
						$output .= '<a href="'. esc_url( $button_url ) .'" class="request-btn">'. $button_label .'</a>';
					}
				$output .= '</div>';
			}
			echo force_balance_tags($output);
        }

    }

}
add_action('widgets_init', function() {
    return register_widget("Foodbakery_restaurent_message");
});



