<?php

/**
 * File Type: Searchs Shortcode Frontend
 */
if ( ! class_exists( 'Foodbakery_Shortcode_Restaurant_Search_front' ) ) {

    class Foodbakery_Shortcode_Restaurant_Search_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'restaurant_search';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode( $this->PREFIX, array( $this, 'foodbakery_restaurant_search_shortcode_callback' ) );
        }

        /*
         * Shortcode View on Frontend
         */

        public function foodbakery_restaurant_search_shortcode_callback( $atts, $content = "" ) {
            global $column_container, $foodbakery_form_fields_frontend, $foodbakery_plugin_options;
            $html = '';
            $main_sections_columns = json_encode( $column_container );
            $main_sections_columns = json_decode( $main_sections_columns, true );
            $main_sections_column = isset( $main_sections_columns['@attributes']['foodbakery_section_view'] ) ? $main_sections_columns['@attributes']['foodbakery_section_view'] : 'wide';
            $restaurant_search_title = isset( $atts['restaurant_search_title'] ) ? $atts['restaurant_search_title'] : '';
            $restaurant_search_subtitle = isset( $atts['restaurant_search_subtitle'] ) ? $atts['restaurant_search_subtitle'] : '';
            $restaurant_search_result_page = isset( $atts['restaurant_search_result_page'] ) ? $atts['restaurant_search_result_page'] : '';
            $restaurant_search_view = isset( $atts['restaurant_search_view'] ) ? $atts['restaurant_search_view'] : '';

            $to_result_page = $restaurant_search_result_page != '' ? get_permalink( $restaurant_search_result_page ) : '';
            ob_start();
            $rand_numb = rand( 999, 999999 );
            $atts['rand_numb'] = $rand_numb;
            $atts['restaurant_search_title'] = $restaurant_search_title;
            $atts['restaurant_search_subtitle'] = $restaurant_search_subtitle;
            $foodbakery_search_result_page = isset( $foodbakery_plugin_options['foodbakery_search_result_page'] ) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
            $foodbakery_search_result_page = ( $foodbakery_search_result_page != '' ) ? get_permalink( $foodbakery_search_result_page ) : '';

            $atts['foodbakery_search_result_page'] = ( $to_result_page != '' ? $to_result_page : $foodbakery_search_result_page );
            $page_element_size = isset( $atts['restaurant_search_element_size'] ) ? $atts['restaurant_search_element_size'] : 100;
            if ( function_exists( 'foodbakery_var_page_builder_element_sizes' ) ) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes( $page_element_size ) . ' ">';
            }
            
            set_query_var( 'atts', $atts );

            if ( isset( $restaurant_search_view ) && $restaurant_search_view == 'fancy' ) {
                foodbakery_get_template_part( 'restaurant', 'search-fancy', 'restaurants' );
			}else if ( isset( $restaurant_search_view ) && $restaurant_search_view == 'modern' ) {
                foodbakery_get_template_part( 'restaurant', 'search-modern', 'restaurants' );
            } else { // else render list view.
                foodbakery_get_template_part( 'restaurant', 'search-list', 'restaurants' );
            }
            if ( function_exists( 'foodbakery_var_page_builder_element_sizes' ) ) {
                echo '</div>';
            }
            $html .= ob_get_clean();
            return $html;
        }

        function get_custom_locations() {
            global $foodbakery_plugin_options;
            // getting from plugin options
            $foodbakery_default_locations_list = isset( $foodbakery_plugin_options['foodbakery_default_locations_list'] ) ? $foodbakery_plugin_options['foodbakery_default_locations_list'] : array();

            $output = '<ul class="top-search-locations" style="display: none;">';
            $selected_location = '';
            $selected_item = '';
            if ( is_array( $foodbakery_default_locations_list ) && sizeof( $foodbakery_default_locations_list ) > 0 ) {
                foreach ( $foodbakery_default_locations_list as $tag_r ) {
                    $tag_obj = get_term_by( 'slug', $tag_r, 'foodbakery_locations' );
                    if ( is_object( $tag_obj ) ) {
                        $selected_item .= '<li data-val="' . $tag_obj->slug . '">' . $tag_obj->name . '</a></li>';
                    }
                }
            }
            $output .= $selected_item;
            $output .= '</ul>';
            if ( false === ( $foodbakery_location_data = foodbakery_get_transient_obj( 'foodbakery_location_data' ) ) ) {
                
            } else {
                if ( ! empty( $foodbakery_location_data ) ) {
                    $output .= '
					<script>
					jQuery(document).ready(function () {
						var location_data_json = \'' . str_replace( "'", "", $foodbakery_location_data ) . '\';
						var location_data_json_obj = JSON.parse(location_data_json);
						jQuery(".top-search-locations").html(\'\');
						jQuery.each(location_data_json_obj, function() {
							jQuery(".top-search-locations").append("<li data-val=\'"+this.value+"\'>"+this.caption+"</li>");
						});
					});
					</script>';
                }
            }
            echo force_balance_tags( $output );
        }

    }

    global $foodbakery_shortcode_restaurant_search_front;
    $foodbakery_shortcode_restaurant_search_front = new Foodbakery_Shortcode_Restaurant_Search_front();
}
