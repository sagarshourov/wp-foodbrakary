<?php
/**
 * File Type: Opening Hours
 */
if (!class_exists('foodbakery_opening_hours')) {

    class foodbakery_opening_hours {
        
        /**
         * Start construct Functions
         */
        public function __construct() {
            
            add_filter('foodbakery_opening_hours_admin_fields', array($this, 'foodbakery_opening_hours_admin_fields_callback'), 11, 2);
            add_action('save_post', array($this, 'foodbakery_insert_opening_hours'), 15);
        }
        
        public function foodbakery_opening_hours_admin_fields_callback( $post_id, $restaurant_type_slug ){
            global $foodbakery_html_fields, $post;
            $post_id                = ( isset( $post_id ) && $post_id != '' )? $post_id : $post->ID;
            $restaurant_type_post      = get_posts(array( 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish' ));
            $restaurant_type_id        = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
            $foodbakery_full_data    = get_post_meta( $restaurant_type_id, 'foodbakery_full_data', true );
            $lapse                  = 15;
            $foodbakery_opening_hours_gap        = get_post_meta( $restaurant_type_id, 'foodbakery_opening_hours_time_gap', true );
            if ( isset( $foodbakery_opening_hours_gap ) && $foodbakery_opening_hours_gap != '' ){
                $lapse              = $foodbakery_opening_hours_gap;
            }
            
            $html                   = '';
            if ( !isset( $foodbakery_full_data['foodbakery_opening_hours_element'] ) || $foodbakery_full_data['foodbakery_opening_hours_element'] != 'on' ){
                return $html = '';
            }
            
			$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
			$opening_hours_data = array();
			foreach ( $days as $key => $day ) {
				$opening_time = get_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true );
				$opening_time = ( $opening_time != '' ? date( 'h:i a', $opening_time ) : '' );
				$closing_time = get_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true );
				$closing_time = ( $closing_time != '' ? date( 'h:i a', $closing_time ) : '' );
				$opening_hours_data[ $day ] = array(
					'day_status' => get_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_day_status', true ),
					'opening_time' => $opening_time,
					'closing_time' => $closing_time,
				);
			}
			
			
            $date       = date("Y-m-d 12:00");
            $time       = strtotime('12:00 am');
            $start_time = strtotime( $date. ' am' );
            $endtime   = strtotime( date("Y-m-d h:i a", strtotime('1440 minutes', $start_time)) );
            
            while( $start_time < $endtime ){
                $time   = date("h:i a", strtotime('+' . $lapse . ' minutes', $time));
                $hours[$time]   = $time;
                $time   = strtotime( $time );
                $start_time   = strtotime( date("Y-m-d h:i a", strtotime('+' . $lapse . ' minutes', $start_time)));
            }
            
            $html .= $foodbakery_html_fields->foodbakery_heading_render(
                array(
                    'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_schedule_with_time' ),
                    'cust_name' => 'opening_hours',
                    'classes' => '',
                    'std' => '',
                    'description' => '',
                    'hint' => '',
                    'echo' => false,
                )
            );
				

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_monday' ),
                'id' => 'radius_fields',
                'desc' => '',
                'hint_text' => '',
                'echo' => false, 
                'fields_list' => array(
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['monday']['opening_time'] ) )? $opening_hours_data['monday']['opening_time']:'',
                            'cust_name' => 'opening_hours[monday][opening_time]',
                            'id' => 'opening_hours[monday][opening_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_opening_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['monday']['closing_time'] ) )? $opening_hours_data['monday']['closing_time']:'',
                            'cust_name' => 'opening_hours[monday][closing_time]',
                            'id' => 'opening_hours[monday][closing_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_closing_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),

                    array(
                        'type' => 'checkbox', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['monday']['day_status'] ) )? $opening_hours_data['monday']['day_status']:'on',
                            'cust_name' => 'opening_hours[monday][day_status]',
                            'id' => 'opening_hours[monday][day_status]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_monday_on' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                        ),
                    ),
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_tuesday' ),
                'id' => 'radius_fields',
                'desc' => '',
                'hint_text' => '',
                'echo' => false, 
                'fields_list' => array(
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['tuesday']['opening_time'] ) )? $opening_hours_data['tuesday']['opening_time']:'',
                            'cust_name' => 'opening_hours[tuesday][opening_time]',
                            'id' => 'opening_hours[tuesday][opening_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_opening_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['tuesday']['closing_time'] ) )? $opening_hours_data['tuesday']['closing_time']:'',
                            'cust_name' => 'opening_hours[tuesday][closing_time]',
                            'id' => 'opening_hours[tuesday][closing_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_closing_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),

                    array(
                        'type' => 'checkbox', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['tuesday']['day_status'] ) )? $opening_hours_data['tuesday']['day_status']:'on',
                            'cust_name' => 'opening_hours[tuesday][day_status]',
                            'id' => 'opening_hours[tuesday][day_status]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_tuesday_on' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                        ),
                    ),
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_wednesday' ),
                'id' => 'radius_fields',
                'desc' => '',
                'hint_text' => '',
                'echo' => false, 
                'fields_list' => array(
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['wednesday']['opening_time'] ) )? $opening_hours_data['wednesday']['opening_time']:'',
                            'cust_name' => 'opening_hours[wednesday][opening_time]',
                            'id' => 'opening_hours[wednesday][opening_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_opening_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['wednesday']['closing_time'] ) )? $opening_hours_data['wednesday']['closing_time']:'',
                            'cust_name' => 'opening_hours[wednesday][closing_time]',
                            'id' => 'opening_hours[wednesday][closing_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_closing_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),

                    array(
                        'type' => 'checkbox', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['wednesday']['day_status'] ) )? $opening_hours_data['wednesday']['day_status']:'on',
                            'cust_name' => 'opening_hours[wednesday][day_status]',
                            'id' => 'opening_hours[wednesday][day_status]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_wednesday_on' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                        ),
                    ),
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_thursday' ),
                'id' => 'radius_fields',
                'desc' => '',
                'hint_text' => '',
                'echo' => false, 
                'fields_list' => array(
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['thursday']['opening_time'] ) )? $opening_hours_data['thursday']['opening_time']:'',
                            'cust_name' => 'opening_hours[thursday][opening_time]',
                            'id' => 'opening_hours[thursday][opening_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_opening_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['thursday']['closing_time'] ) )? $opening_hours_data['thursday']['closing_time']:'',
                            'cust_name' => 'opening_hours[thursday][closing_time]',
                            'id' => 'opening_hours[thursday][closing_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_closing_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),

                    array(
                        'type' => 'checkbox', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['thursday']['day_status'] ) )? $opening_hours_data['thursday']['day_status']:'on',
                            'cust_name' => 'opening_hours[thursday][day_status]',
                            'id' => 'opening_hours[thursday][day_status]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_thursday_on' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                        ),
                    ),
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_friday' ),
                'id' => 'radius_fields',
                'desc' => '',
                'hint_text' => '',
                'echo' => false, 
                'fields_list' => array(
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['friday']['opening_time'] ) )? $opening_hours_data['friday']['opening_time']:'',
                            'cust_name' => 'opening_hours[friday][opening_time]',
                            'id' => 'opening_hours[friday][opening_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_opening_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['friday']['closing_time'] ) )? $opening_hours_data['friday']['closing_time']:'',
                            'cust_name' => 'opening_hours[friday][closing_time]',
                            'id' => 'opening_hours[friday][closing_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_closing_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),

                    array(
                        'type' => 'checkbox', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['friday']['day_status'] ) )? $opening_hours_data['friday']['day_status']:'on',
                            'cust_name' => 'opening_hours[friday][day_status]',
                            'id' => 'opening_hours[friday][day_status]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_friday_on' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                        ),
                    ),
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_saturday' ),
                'id' => 'radius_fields',
                'desc' => '',
                'hint_text' => '',
                'echo' => false, 
                'fields_list' => array(
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['saturday']['opening_time'] ) )? $opening_hours_data['saturday']['opening_time']:'',
                            'cust_name' => 'opening_hours[saturday][opening_time]',
                            'id' => 'opening_hours[saturday][opening_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_opening_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['saturday']['closing_time'] ) )? $opening_hours_data['saturday']['closing_time']:'',
                            'cust_name' => 'opening_hours[saturday][closing_time]',
                            'id' => 'opening_hours[saturday][closing_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_closing_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),

                    array(
                        'type' => 'checkbox', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['saturday']['day_status'] ) )? $opening_hours_data['saturday']['day_status']:'on',
                            'cust_name' => 'opening_hours[saturday][day_status]',
                            'id' => 'opening_hours[saturday][day_status]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_saturday_on' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                        ),
                    ),
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_sunday' ),
                'id' => 'radius_fields',
                'desc' => '',
                'hint_text' => '',
                'echo' => false, 
                'fields_list' => array(
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['sunday']['opening_time'] ) )? $opening_hours_data['sunday']['opening_time']:'',
                            'cust_name' => 'opening_hours[sunday][opening_time]',
                            'id' => 'opening_hours[sunday][opening_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_opening_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),
                    array(
                        'type' => 'select', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['sunday']['closing_time'] ) )? $opening_hours_data['sunday']['closing_time']:'',
                            'cust_name' => 'opening_hours[sunday][closing_time]',
                            'id' => 'opening_hours[sunday][closing_time]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_closing_time' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                            'options' => $hours,
                        ),
                    ),

                    array(
                        'type' => 'checkbox', 'field_params' => array(
                            'std' => ( isset( $opening_hours_data['sunday']['day_status'] ) )? $opening_hours_data['sunday']['day_status']:'on',
                            'cust_name' => 'opening_hours[sunday][day_status]',
                            'id' => 'opening_hours[sunday][day_status]',
                            'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_sunday_on' ) . '"',
                            'return' => true,
                            'classes' => 'input-small',
                        ),
                    ),
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);
            
            return $html;
        }
        
        public function foodbakery_insert_opening_hours( $post_id ){
			if( isset( $_POST['opening_hours'] ) ){
				$opening_hours_list = $_POST['opening_hours'];
				$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
				foreach ( $days as $key => $day ) {
					if ( isset( $opening_hours_list[ $day ] ) ) {
						$day_status = ( $opening_hours_list[ $day ]['day_status'] != '' ? $opening_hours_list[ $day ]['day_status'] : 'off' );
						$opening_time = ( $opening_hours_list[ $day ]['opening_time'] != '' ? $opening_hours_list[ $day ]['opening_time'] : '' );
						if ( $opening_time != '' ) {
							$opening_time = strtotime( '2016-01-01 ' . $opening_time );
						}
						$closing_time = ( $opening_hours_list[ $day ]['closing_time'] != '' ? $opening_hours_list[ $day ]['closing_time'] : '' );
						if ( $closing_time != '' ) {
							$closing_time = strtotime( '2016-01-01 ' . $closing_time );
						}
						
						if( $opening_time != '' && $closing_time != '' && $opening_time > $closing_time){
							$closing_time = strtotime('+1 day', $closing_time);
						}
						
						update_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_day_status', $day_status );
						update_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_opening_time', $opening_time );
						update_post_meta( $post_id, 'foodbakery_opening_hours_' . $day . '_closing_time', $closing_time );
					}
					
				}
            }
        }
    }
    global $foodbakery_opening_hours;
    $foodbakery_opening_hours    = new foodbakery_opening_hours();
}