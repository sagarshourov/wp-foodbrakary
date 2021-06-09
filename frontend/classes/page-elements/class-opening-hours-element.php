<?php
/**
 * File Type: Opening Hours Page Element
 */
if (!class_exists('foodbakery_opening_hours_element')) {

    class foodbakery_opening_hours_element {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('foodbakery_opening_hours_element_html', array($this, 'foodbakery_opening_hours_element_html_callback'), 11, 1);
        }
        
        /*
         * Output features html for frontend on restaurant detail page.
         */
        public function foodbakery_opening_hours_element_html_callback( $post_id ){
            $restaurant_type_slug      = get_post_meta( $post_id, 'foodbakery_restaurant_type', true );
            $restaurant_type_post      = get_posts(array( 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish' ));
            $restaurant_type_id        = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
            $foodbakery_full_data    = get_post_meta( $restaurant_type_id, 'foodbakery_full_data', true );
            
            if ( !isset( $foodbakery_full_data['foodbakery_opening_hours_element'] ) || $foodbakery_full_data['foodbakery_opening_hours_element'] != 'on' ){
                $html = '';
            } else {
                $html = '';
				$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
				$opening_hours_list = array();
				foreach ( $days as $key => $day ) {
					$opening_time = get_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true );
					$opening_time = ( $opening_time != '' ? date('h:i a', $opening_time ) : '' );
					$closing_time = get_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true );
					$closing_time = ( $opening_time != '' ? date('h:i a', $closing_time ) : '' );
					$opening_hours_list[ $day ] = array(
						'day_status' => get_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_day_status', true ),
						'opening_time' => $opening_time,
						'closing_time' => $closing_time,
					);
				}
                if ( isset ( $opening_hours_list ) && !empty( $opening_hours_list ) ){
                    $html   = '<div class="widget widget-timing">'
                            . '<h5>' . __( 'Opening Hours', 'foodbakery' ) . '</h5>';
                    $html   .= '<ul>';
                        $monday     = ( isset( $opening_hours_list['monday']['day_status'] ) && $opening_hours_list['monday']['day_status'] == 'on' )?$opening_hours_list['monday']['opening_time'] . ' - ' . $opening_hours_list['monday']['closing_time']:'Off';
                        $html       .= '<li><span>'.__( 'Monday', 'foodbakery' ).'</span>' . $monday . '</li>';

                        $tuesday     = ( isset( $opening_hours_list['tuesday']['day_status'] ) && $opening_hours_list['tuesday']['day_status'] == 'on' )?$opening_hours_list['tuesday']['opening_time'] . ' - ' . $opening_hours_list['tuesday']['closing_time']:'Off';
                        $html       .= '<li><span>'.__( 'Tuesday', 'foodbakery' ).'</span>' . $tuesday . '</li>';

                        $wednesday    = ( isset( $opening_hours_list['wednesday']['day_status'] ) && $opening_hours_list['wednesday']['day_status'] == 'on' )?$opening_hours_list['wednesday']['opening_time'] . ' - ' . $opening_hours_list['wednesday']['closing_time']:'Off';
                        $html       .= '<li><span>'.__( 'Wednesday', 'foodbakery' ).'</span>' . $wednesday . '</li>';

                        $thursday    = ( isset( $opening_hours_list['thursday']['day_status'] ) && $opening_hours_list['thursday']['day_status'] == 'on' )?$opening_hours_list['thursday']['opening_time'] . ' - ' . $opening_hours_list['thursday']['closing_time']:'Off';
                        $html       .= '<li><span>'.__( 'Thursday', 'foodbakery' ).'</span>' . $thursday . '</li>';

                        $friday      = ( isset( $opening_hours_list['friday']['day_status'] ) && $opening_hours_list['friday']['day_status'] == 'on' )?$opening_hours_list['friday']['opening_time'] . ' - ' . $opening_hours_list['friday']['closing_time']:'Off';
                        $html       .= '<li><span>'.__( 'Friday', 'foodbakery' ).'</span>' . $friday . '</li>';

                        $saturday    = ( isset( $opening_hours_list['saturday']['day_status'] ) && $opening_hours_list['saturday']['day_status'] == 'on' )?$opening_hours_list['saturday']['opening_time'] . ' - ' . $opening_hours_list['saturday']['closing_time']:'Off';
                        $html       .= '<li><span>'.__( 'Saturday', 'foodbakery' ).'</span>' . $saturday . '</li>';

                        $sunday      = ( isset( $opening_hours_list['sunday']['day_status'] ) && $opening_hours_list['sunday']['day_status'] == 'on' )?$opening_hours_list['sunday']['opening_time'] . ' - ' . $opening_hours_list['sunday']['closing_time']:'Off';
                        $html       .= '<li><span>'.__( 'Sunday', 'foodbakery' ).'</span>' . $sunday . '</li>';
                    $html   .= '</ul></div>';
                }
            }
            
            echo force_balance_tags( $html );
            
        }
    }
    global $foodbakery_opening_hours_element;
    $foodbakery_opening_hours_element    = new foodbakery_opening_hours_element();
}