<?php

/**
 * File Type: Searchs Shortcode Frontend
 */
if ( ! class_exists( 'Foodbakery_Shortcode_Restaurant_Categories_front' ) ) {

    class Foodbakery_Shortcode_Restaurant_Categories_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'restaurant_categories';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode( $this->PREFIX, array( $this, 'foodbakery_restaurant_categories_shortcode_callback' ) );
        }

        /*
         * Shortcode View on Frontend
         */

        function combine_pt_section( $keys, $values ) {
            $result = array();
            foreach ( $keys as $i => $k ) {
                $result[$k][] = $values[$i];
            }
            array_walk( $result, create_function( '&$v', '$v = (count($v) == 1)? array_pop($v): $v;' ) );
            return $result;
        }

        public function foodbakery_restaurant_categories_shortcode_callback( $atts, $content = "" ) {
            global $current_user, $foodbakery_plugin_options;

            $restaurant_categories_title = isset( $atts['restaurant_categories_title'] ) ? $atts['restaurant_categories_title'] : '';
            $page_element_size = isset( $atts['restaurant_categories_element_size'] ) ? $atts['restaurant_categories_element_size'] : 100;
            ob_start();
            if ( function_exists( 'foodbakery_var_page_builder_element_sizes' ) ) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes( $page_element_size ) . ' ">';
            }
            $args = array( 'post_type' => 'restaurant-type', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', );
            $loop_query = new Wp_Query( $args );
            if ( $loop_query->have_posts() ):
                while ( $loop_query->have_posts() ):

                    $loop_query->the_post();
                    global $post;
                    $restaurant_type_id = $post;

                    $foodbakery_search_result_page = get_post_meta( $restaurant_type_id, 'foodbakery_search_result_page', true );
                    $foodbakery_search_result_page = isset( $foodbakery_search_result_page ) && $foodbakery_search_result_page != '' ? get_permalink( $foodbakery_search_result_page ) : '';
                endwhile;
            endif;
            $pricing_tabl_subtitle = isset( $atts['restaurant_categories_subtitle'] ) ? $atts['restaurant_categories_subtitle'] : '';
            $foodbakery_var_categories_align = isset( $atts['foodbakery_var_categories_align'] ) ? $atts['foodbakery_var_categories_align'] : '';
	    $foodbakery_var_categories_style = isset( $atts['foodbakery_var_categories_style'] ) ? $atts['foodbakery_var_categories_style'] : '';
	    $foodbakery_title_color = isset( $atts['foodbakery_title_color'] ) ? $atts['foodbakery_title_color'] : '';
            $restaurant_categories_show = isset( $atts['restaurant_categories_show'] ) ? $atts['restaurant_categories_show'] : '';
            $restaurant_categories_view = isset( $atts['restaurant_categories_view'] ) ? $atts['restaurant_categories_view'] : '';
            $restaurant_categories_moreless = isset( $atts['restaurant_categories_more_less'] ) ? $atts['restaurant_categories_more_less'] : 'no';
            $foodbakery_typess = isset( $atts['foodbakery_types'] ) ? $atts['foodbakery_types'] : '';
            $class = "class=class-" . $restaurant_categories_moreless . "";
            $restaurant_cate_main_class = '';
            if ( $restaurant_categories_view == 'view-2' ) {
                $restaurant_cate_main_class = ' big-categories';
            }
            $all_types = explode( ",", $foodbakery_typess );
            echo '<div class="element-title '.$foodbakery_var_categories_align.'">';
            if ( $restaurant_categories_title != '' ) {
                echo '<h2>' . $restaurant_categories_title . '</h2>';
            }
            if ( $pricing_tabl_subtitle != '' ) {
                echo '<p>' . $pricing_tabl_subtitle . '</p>';
            }
            echo '</div>';
	    $count = count($all_types);
            if ( is_array( $all_types ) && $count > 0 ) {

                echo '<div class="categories-holder '.$foodbakery_var_categories_style.'">';
                foreach ( $all_types as $type ) {
                    $term_icon = '';
                    $cate_link = '';
                    if ( $type != '' ) {
                        $term_data = get_term_by( 'slug', $type, 'restaurant-category' );
                        if ( isset( $term_data->term_id ) ) {
                            $term_icon = get_term_meta( $term_data->term_id, 'foodbakery_restaurant_taxonomy_icon', true );
                        }
                        if ( isset( $term_data->term_id ) ) {
                            $cate_link = isset( $foodbakery_search_result_page ) && $foodbakery_search_result_page != '' ? $foodbakery_search_result_page . '?foodbakery_restaurant_category=' . $term_data->slug.'' : '';
                        }
                        echo '<div class="col-lg-2 col-sm-4 col-xs-6">
                                    <div class="categories-list">';
                        echo '<a href="' . $cate_link . '">';
                        if ( $term_icon != '' ) {
                            echo '<i class="' . $term_icon . ' text-color"></i>';
                        } else {
                            if ( isset( $term_data->term_id ) ) {


                                $term_image = get_term_meta( $term_data->term_id, 'foodbakery_listing_term_image', true );
                                $term_image_src = wp_get_attachment_image_src( $term_image , 'thumbnail', false, false );
                                $image_src = isset( $term_image_src[0] )? $term_image_src[0] : '';
                                $term_image = '<img alt="" src="' . $image_src . '"/>';

                                echo force_balance_tags( $term_image );
                            }
                        }

                        if ( isset( $term_data->name ) ) { 
                            echo '<h6 style="color:'.$foodbakery_title_color.' !important;"><span>' . $term_data->name . '</span></h6>';
                        }
                        echo '</a>';
                        echo '</div>
                                </div>';
                    }
                }
                echo '</div>';
            }

            if ( function_exists( 'foodbakery_var_page_builder_element_sizes' ) ) {
                echo '</div>';
            }
            $post_data = ob_get_clean();
            return $post_data;
        }

    }

    global $foodbakery_shortcode_restaurant_categories_front;
    $foodbakery_shortcode_restaurant_categories_front = new Foodbakery_Shortcode_Restaurant_Categories_front();
}