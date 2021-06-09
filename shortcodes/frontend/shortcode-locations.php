<?php

/**
 * File Type: Searchs Shortcode Frontend
 */
if ( ! class_exists( 'Foodbakery_Shortcode_Locations_front' ) ) {

    class Foodbakery_Shortcode_Locations_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'locations';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode( $this->PREFIX, array( $this, 'foodbakery_locations_shortcode_callback' ) );
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

        public function foodbakery_locations_shortcode_callback( $atts, $content = "" ) {
            global $current_user, $foodbakery_plugin_options;

            $locations_title = isset( $atts['locations_title'] ) ? $atts['locations_title'] : '';
            $foodbakery_var_location_align = isset( $atts['foodbakery_var_location_align'] ) ? $atts['foodbakery_var_location_align'] : '';
	    $foodbakery_var_location_style = isset( $atts['foodbakery_var_location_style'] ) ? $atts['foodbakery_var_location_style'] : '';
            $pricing_tabl_subtitle = isset( $atts['locations_subtitle'] ) ? $atts['locations_subtitle'] : '';
            $foodbakery_location = isset( $atts['foodbakery_location'] ) ? $atts['foodbakery_location'] : '';
            $locations_button_url = isset( $atts['locations_button_url'] ) ? $atts['locations_button_url'] : '';
            $foodbakery_search_result_page = isset( $foodbakery_plugin_options['foodbakery_search_result_page'] ) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
            $redirecturl = isset( $foodbakery_search_result_page ) && $foodbakery_search_result_page != '' ? get_permalink( $foodbakery_search_result_page ) . '' : '';
            ob_start();
            $page_element_size  = isset( $atts['locations_element_size'] )? $atts['locations_element_size'] : 100;
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
            }
            $all_types = explode( ",", $foodbakery_location );
            echo '<div class="element-title '.$foodbakery_var_location_align.'">';
            if ( $locations_title != '' ) {
                echo '<h2>' . $locations_title . '</h2>';
            }
            if ( $pricing_tabl_subtitle != '' ) {
                echo '<p>' . $pricing_tabl_subtitle . '</p>';
            }
            echo '</div>';
            echo '<div class="location-holder '.$foodbakery_var_location_style.'">';
	    echo '<div class="row">';
	    $count = count($all_types);
            if ( is_array( $all_types ) && $count > 0 ) {

                echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><ul class="location-list">';
               
                $fields_array = array();
                $counter = 0;
                foreach ( $all_types as $type ) {
                    $args = array(
                        'posts_per_page' => "-1",
                        'post_type' => 'restaurants',
                        'post_status' => 'publish',
                        'meta_query' => array(
                            'relation' => 'OR',
                            array(
                                'key' => 'foodbakery_post_loc_country_restaurant',
                                'value' => $type,
                                'compare' => '=',
                            ),
							array(
                                'key' => 'foodbakery_post_loc_state_restaurant',
                                'value' => $type,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_post_loc_city_restaurant',
                                'value' => $type,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_post_loc_town_restaurant',
                                'value' => $type,
                                'compare' => '=',
                            ),
                        ),
                    );
					
                    $my_query = new WP_Query( $args );
					
                    $count_rest = count( $my_query->posts );
                    wp_reset_postdata();
                    $term_data = get_term_by( 'slug', $type, 'foodbakery_locations' );

                    if ( isset( $term_data->name ) ) {
                        $fields_array[$counter]['location'] = $term_data->name;
                        $fields_array[$counter]['id'] = $term_data->term_id;
                        $fields_array[$counter]['slug'] = $term_data->slug;
                        $fields_array[$counter]['count_rest'] = $count_rest;
                    }
                    $counter ++;
                }
                $sort_by_count = array();
                foreach ( $fields_array as $key => $row ) {
                    $sort_by_count[$key] = $row['count_rest'];
                }
                array_multisort($sort_by_count, SORT_DESC, $fields_array);
                $loc_count = 1;
                $counter_loc = 0;
                foreach ( $fields_array as $field_data ) {
                    echo '<li><a href="' . $redirecturl . '?location=' . $field_data['slug'] . '">' . $field_data['location'] . '</a><span>(' . $field_data['count_rest'] . ' places)</span></li>';    
                    $loc_count ++;
                    $counter_loc++;
                    if ( $loc_count > 5 ) {
                        $loc_count = 1;
                        if($counter_loc != count($fields_array)){
                        echo '</ul></div>';
                        echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><ul class="location-list">';
                        }
                        
                    }
                   
                }
                echo '</ul></div>';
            }
            echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="more-btn-holder"><a class="more-btn" href="'.esc_url($locations_button_url).'">'.esc_html__('See more locations', 'foodbakery').'</a></div></div>';
            echo '</div>';
	      echo '</div>';
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
             echo  '</div>';
           } 
		   
           $post_data = ob_get_clean();
           return $post_data;
        }

    }

    global $foodbakery_shortcode_locations_front;
    $foodbakery_shortcode_locations_front = new Foodbakery_Shortcode_Locations_front();
}