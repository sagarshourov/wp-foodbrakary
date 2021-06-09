<?php

/**
 * Widget API: WP_nav_menu_Widget class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement the Custom Menu widget.
 *
 * @since 3.0.0
 *
 * @see WP_Widget
 */
class Foodbakery_Popular_Cuisines_Widget extends WP_Widget {

    /**
     * Sets up a new Custom Menu widget instance.
     *
     * @since 3.0.0
     * @access public
     */
    public function __construct() {

        $widget_ops = array(
            'classname' => 'cuisines-widget',
            'description' => 'Foodbakery Popular Cuisines widget',
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'foodbakery_cuisines_widget', 'Cs: Popular Cuisines', $widget_ops );
    }

    /**
     * Outputs the content for the current Custom Menu widget instance.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Custom Menu widget instance.
     */
    public function widget( $args, $instance ) {
        // Get menu.

        $selected_cuisines = isset( $instance['cuisines'] ) ? $instance['cuisines'] : '';
      


        $instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

         $post_args = array( 'post_type' => 'restaurant-type', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', );
            $loop_query = new Wp_Query( $post_args );
            if ( $loop_query->have_posts() ):
                while ( $loop_query->have_posts() ):

                    $loop_query->the_post();
                    global $post;
                    $restaurant_type_id = $post;

                    $foodbakery_search_result_page = get_post_meta( $restaurant_type_id, 'foodbakery_search_result_page', true );
                    $foodbakery_search_result_page = isset( $foodbakery_search_result_page ) && $foodbakery_search_result_page != '' ? get_permalink( $foodbakery_search_result_page ) : '';
                endwhile;
            endif;
        echo '<div class="widget widget-top-cities">';

        if ( '' !== $instance['title'] ) {
            echo '<div class="widget-title"><h5>' . esc_html( $instance['title'] ) . '</h5></div>';
        }
        if ( $selected_cuisines != '' && sizeof( $selected_cuisines ) > 0 ) {
            echo '<ul>';
            foreach ( $selected_cuisines as $single_loc ) {

                if ( term_exists( (int) $single_loc, 'restaurant-category' ) ) {
                    $term_info = get_term_by( 'id', $single_loc, 'restaurant-category' );
                    if ( isset( $term_info->term_id ) ) {
                        $cate_link = isset( $foodbakery_search_result_page ) && $foodbakery_search_result_page != '' ? $foodbakery_search_result_page . '?foodbakery_restaurant_category=' . $term_info->slug.'' : '';
                    }

                   

                    echo '<li><a href="' . $cate_link . '">' . $term_info->name . '</a></li>';
                }
            }
            echo '</ul>';
        }

        echo '</div>';
    }

    /**
     * Handles updating settings for the current Custom Menu widget instance.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        if ( ! empty( $new_instance['title'] ) ) {
            $instance['title'] = sanitize_text_field( $new_instance['title'] );
        }
        if ( ! empty( $new_instance['cuisines'] ) ) {
            $instance['cuisines'] = $new_instance['cuisines'];
        }

        return $instance;
    }

    /**
     * Outputs the settings form for the Custom Menu widget.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {

        global $foodbakery_var_form_fields, $foodbakery_var_html_fields, $foodbakery_plugin_options, $foodbakery_html_fields, $foodbakery_form_fields;
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $cuisines = isset( $instance['cuisines'] ) ? $instance['cuisines'] : '';


        $title = isset( $instance['title'] ) ? $title = $instance['title'] : '';

        $foodbakery_opt_array = array(
            'name' => foodbakery_plugin_text_srt( 'foodbakery_widget_title' ),
            'desc' => '',
            'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_widget_title_desc' ),
            'echo' => true,
            'field_params' => array(
                'std' => esc_attr( $title ),
                'id' => foodbakery_allow_special_char( $this->get_field_name( 'title' ) ),
                'classes' => '',
                'cust_id' => foodbakery_allow_special_char( $this->get_field_name( 'title' ) ),
                'cust_name' => foodbakery_allow_special_char( $this->get_field_name( 'title' ) ),
                'return' => true,
                'required' => false
            ),
        );
        $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );


        $taxonomies = array(
            'restaurant-category',
        );

        $args = array(
            'hide_empty' => false,
            'hierarchical' => true,
        );

        $options_all = get_terms( $taxonomies, $args );

     


        $selected_cuisines = array();
        if ( $options_all != '' ) {
            foreach ( $options_all as $option ) {
                $selected_cuisines[$option->term_id] = $option->name;
            }
        }
        $foodbakery_opt_array = array(
            'name' => foodbakery_plugin_text_srt( 'choose_cuisines_fields' ),
            'hint_text' => foodbakery_plugin_text_srt( 'choose_cuisines_fields_desc' ),
            'echo' => true,
            'multi' => true,
            'field_params' => array(
                'cust_name' => foodbakery_allow_special_char( $this->get_field_name( 'cuisines[]' ) ),
                'cust_id' => foodbakery_allow_special_char( $this->get_field_id( 'cuisines' ) ),
                'options' => $selected_cuisines,
                'return' => true,
                'classes' => 'chosen-select',
                'std' => $cuisines,
            ),
        );

        $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );
        ?>
        <script>
            /* modern selection box and help hover text function */
            jQuery(document).ready(function ($) {
                chosen_selectionbox();
                popup_over();
            });
            /* end modern selection box and help hover text function */
        </script>
        <?php

    }

}
add_action('widgets_init', function() {
    return register_widget("Foodbakery_Popular_Cuisines_Widget");
});
