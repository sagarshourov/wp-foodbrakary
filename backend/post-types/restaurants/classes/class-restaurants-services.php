<?php
/**
 * File Type: Opening Hours
 */
if (!class_exists('foodbakery_services')) {

    class foodbakery_services {

        /**
         * Start construct Functions
         */
        public function __construct() {
			add_filter( 'foodbakery_menu_items_admin_fields', array( $this, 'foodbakery_menu_items_admin_fields_callback' ), 11, 2 );
			add_action( 'wp_ajax_foodbakery_menu_items_repeating_fields', array( $this, 'foodbakery_menu_items_repeating_fields_callback' ), 11 );
			add_action( 'save_post', array( $this, 'foodbakery_insert_menu_items' ), 17 );
		}
		
		public function foodbakery_menu_items_admin_fields_callback( $post_id, $restaurant_type_slug ) {
			global $foodbakery_html_fields, $post;
			
			$post_id = ( isset( $post_id ) && $post_id != '' ) ? $post_id : $post->ID;
			$restaurant_type_post = get_posts( array( 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish' ) );
			$restaurant_type_id = isset( $restaurant_type_post[0]->ID ) ? $restaurant_type_post[0]->ID : 0;
			$foodbakery_full_data = get_post_meta( $restaurant_type_id, 'foodbakery_full_data', true );
			$html = '';

			$foodbakery_menu_items = get_post_meta( $post_id, 'foodbakery_menu_items', true );
			
			if ( ! isset( $foodbakery_full_data['foodbakery_services_options_element'] ) || $foodbakery_full_data['foodbakery_services_options_element'] != 'on' ) {
				return $html = '';
			}

			$html .= $foodbakery_html_fields->foodbakery_heading_render(
					array(
						'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_menu_items' ),
						'cust_name' => 'menu_items',
						'classes' => '',
						'std' => '',
						'description' => '',
						'hint' => '',
						'echo' => false,
					)
			);

			$html .= '<div id="form-elements" class="form-elements">';

			$html .= '<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$html .= '<div id="menu_items_repeater_fields">';

			if ( isset( $foodbakery_menu_items ) && is_array( $foodbakery_menu_items ) ) {

				foreach ( $foodbakery_menu_items as $foodbakery_menu_item ) {
					$html .= $this->foodbakery_menu_items_repeating_fields_callback( $foodbakery_menu_item );
				}
			}

			$html .= '</div>';

			$html .= '<div id="menu_items_repeater_loader">';
			$html .= '</div>';

			$html .= '<div class="menu_item_repeater services_repeater_btn button button-primary button-large" data-id="menu_items_repeater">' . __( 'Add More', 'foodbakery' ) . '</div>';
			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		public function foodbakery_menu_items_repeating_fields_callback( $data = array( '' ) ) {
			global $foodbakery_html_fields, $foodbakery_plugin_options, $pagenow;
			if ( isset( $data ) && count( $data ) > 0 ) {
				extract( $data );
			}
			
			if ( $pagenow == 'post.php' ) {
				$restaurant_id = $post->ID;
			} else {
				$restaurant_id = foodbakery_get_input('restaurant_id', 0);
			}
			
			$html = '';
			$rand = mt_rand( 10, 200 );

			$html .= '<div id="menu_items_repeater" style="display:block;">';
			
			
			
			$restaurants_menus = get_post_meta($restaurant_id, 'menu_cat_titles', true);
			
			if ( is_array( $restaurants_menus ) && sizeof( $restaurants_menus ) > 0 ) {
				foreach ( $restaurants_menus as $key => $lable ) {
					if ( $lable != '' ) {
						$restaurants_menus_options[$lable] = $lable;
					}
				}
			}
			$foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_menus' ),
                'desc' => '',
                'hint_text' => '',
                'field_params' => array(
                    'std' => ( isset( $restaurant_menu ) ) ? $restaurant_menu : '',
					'id' => 'restaurant_menu' . $rand,
                    'cust_name' => 'foodbakery_menu_items[restaurant_menu][]',
                    'classes' => 'dropdown chosen-select',
                    'options' => $restaurants_menus_options,
                    'return' => true,
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );
			
			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_menu_item_title' ),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'usermeta' => true,
					'std' => ( isset( $menu_item_title ) ) ? $menu_item_title : '',
					'id' => 'menu_item_title' . $rand,
					'cust_name' => 'foodbakery_menu_items[title][]',
					'classes' => 'repeating_field',
					'return' => true,
				),
			);

			$html .= $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_menu_item_desc' ),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'usermeta' => true,
					'std' => ( isset( $menu_item_description ) ) ? $menu_item_description : '',
					'id' => 'menu_item_description' . $rand,
					'cust_name' => 'foodbakery_menu_items[description][]',
					'extra_atr' => 'class="repeating_field"',
					'classes' => 'foodbakery-dev-req-field foodbakery_editor',
					'foodbakery_editor' => true,
					'return' => true,
				),
			);

			$html .= $foodbakery_html_fields->foodbakery_textarea_field( $foodbakery_opt_array );

			$html .= '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_menu_item_icon' ) . '</label></div>';
			$html .= '<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$html .= foodbakery_iconlist_plugin_options( ( isset( $menu_item_icon ) ) ? $menu_item_icon : '', 'menu_item_icon' . $rand, 'foodbakery_menu_items[icon]' );
			$html .= '</div></div>';

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_menu_item_price' ),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'usermeta' => true,
					'std' => ( isset( $menu_item_price ) ) ? $menu_item_price : '',
					'id' => 'menu_item_price' . $rand,
					'cust_name' => 'foodbakery_menu_items[price][]',
					'classes' => 'repeating_field',
					'return' => true,
				),
			);

			$html .= $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

			$html .= '<div class="remove_field" data-id="menu_items_repeater">Remove</div>';
			$html .= '</div>';
			if ( NULL != foodbakery_get_input( 'ajax', NULL ) && foodbakery_get_input( 'ajax' ) == 'true' ) {
				echo force_balance_tags($html);
			} else {
				return $html;
			}

			if ( NULL != foodbakery_get_input( 'die', NULL ) && foodbakery_get_input( 'die' ) == 'true' ) {
				die();
			}
		}

		public function foodbakery_insert_menu_items( $post_id ) {
			if ( get_post_type( $post_id ) == 'restaurants' ) {
				if ( ! isset( $_POST['foodbakery_menu_items']['title'] ) || count( $_POST['foodbakery_menu_items']['title'] ) < 1 ) {
					delete_post_meta( $post_id, 'foodbakery_menu_items' );
				}
			}
			if ( isset( $_POST['foodbakery_menu_items']['title'] ) && count( $_POST['foodbakery_menu_items']['title'] ) > 0 ) {

				foreach ( $_POST['foodbakery_menu_items']['title'] as $key => $service ) {

					if ( count( $service ) > 0 ) {
						$services_array[] = array(
							'restaurant_menu' => $_POST['foodbakery_menu_items']['restaurant_menu'][$key],
							'menu_item_title' => $service,
							'menu_item_description' => $_POST['foodbakery_menu_items']['description'][$key],
							'menu_item_icon' => $_POST['foodbakery_menu_items']['icon'][$key],
							'menu_item_price' => $_POST['foodbakery_menu_items']['price'][$key],
						);
					}
				}
				update_post_meta( $post_id, 'foodbakery_menu_items', $services_array );
			}
		}
        
        public function foodbakery_services_admin_fields_callback( $post_id, $restaurant_type_slug ){
            global $foodbakery_html_fields, $post;
            
            $post_id                = ( isset( $post_id ) && $post_id != '' )? $post_id : $post->ID;
            $restaurant_type_post      = get_posts(array( 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish' ));
            $restaurant_type_id        = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
            $foodbakery_full_data    = get_post_meta( $restaurant_type_id, 'foodbakery_full_data', true );
            $html                   = '';
            
            $foodbakery_services_data = get_post_meta( $post_id, 'foodbakery_services', true );
            
            if ( !isset( $foodbakery_full_data['foodbakery_services_options_element'] ) || $foodbakery_full_data['foodbakery_services_options_element'] != 'on' ){
                return $html = '';
            }
            
            $html   .= $foodbakery_html_fields->foodbakery_heading_render(
                    array(
                        'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_services' ),
                        'cust_name' => 'services',
                        'classes' => '',
                        'std' => '',
                        'description' => '',
                        'hint' => '',
                        'echo' => false,
                    )
            );
            
			$html   .= '<div id="form-elements" class="form-elements">';
			
			$html   .= '<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
            $html   .= '<div id="services_repeater_fields">';
            
            if ( isset ( $foodbakery_services_data ) && is_array( $foodbakery_services_data ) ){
                
                foreach( $foodbakery_services_data as $service_data ){
                        $html       .= $this->foodbakery_services_repeating_fields_callback( $service_data );
                }
            }
            
            $html   .= '</div>';
            
            $html   .= '<div id="services_repeater_loader">';
            $html   .= '</div>';
            
            $html   .= '<div class="repeater services_repeater_btn button button-primary button-large" data-id="services_repeater">' . __( 'Add More', 'foodbakery') . '</div>';
			$html   .= '</div>';
			$html   .= '</div>';
            
            return $html;
        }
        
        public function foodbakery_services_repeating_fields_callback( $data = array('') ){
             global $foodbakery_html_fields;
             if ( isset ( $data ) && count( $data ) > 0 ){
                 extract( $data );
             }
             $html   = '';
             $rand  = mt_rand(10,200);
             
             $html   .= '<div id="services_repeater" style="display:block;">';
			 $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_services_title' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'usermeta' => true,
                    'std' => ( isset( $service_title ) )? $service_title : '',
                    'id' => 'service_title'.$rand,
                    'cust_name' => 'foodbakery_services[title][]',
                    'classes' => 'repeating_field',
                    'return' => true,
                ),
            );
            
            $html   .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
            
             $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_services_description' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'usermeta' => true,
                    'std' => ( isset( $service_description ) )? $service_description : '',
                    'id' => 'service_description'.$rand,
                    'cust_name' => 'foodbakery_services[description][]',
                    'extra_atr' => 'class="repeating_field"',
					'classes' => 'foodbakery-dev-req-field foodbakery_editor',
					'foodbakery_editor' => true,
                    'return' => true,
                ),
            );

            $html   .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
            
            $html   .=  '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_services_icon' ) . '</label></div>';
            $html   .=  '<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$html   .=  foodbakery_iconlist_plugin_options(( isset( $service_icon ) )? $service_icon : '', 'services_icon'.$rand, 'foodbakery_services[icon]');
            $html   .=  '</div></div>';
            
             $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_services_price' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'usermeta' => true,
                    'std' => ( isset( $service_price ) )? $service_price : '',
                    'id' => 'service_price'.$rand,
                    'cust_name' => 'foodbakery_services[price][]',
                    'classes' => 'repeating_field',
                    'return' => true,
                ),
            );

            $html   .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
            
            $html   .=  '<div class="remove_field" data-id="services_repeater">Remove</div>';
            $html   .= '</div>';
            if ( NULL != foodbakery_get_input( 'ajax', NULL ) && foodbakery_get_input( 'ajax' ) == 'true' ){
               echo force_balance_tags($html);
            } else {
                return $html;
            }
            
            if ( NULL != foodbakery_get_input( 'die', NULL ) && foodbakery_get_input( 'die' ) == 'true' ){
                die();
            }
        }
        
        public function foodbakery_insert_services( $post_id ){
            if ( get_post_type( $post_id ) == 'restaurants' ){
                if ( !isset( $_POST['foodbakery_services']['title'] ) || count( $_POST['foodbakery_services']['title'] ) < 1 ){
                    delete_post_meta ( $post_id, 'foodbakery_services' );
                }
            }
            if ( isset( $_POST['foodbakery_services']['title'] ) && count( $_POST['foodbakery_services']['title'] ) > 0 ){
                
                foreach( $_POST['foodbakery_services']['title'] as $key => $service ){
                    
                    if ( count( $service ) > 0 ){
                        $services_array[] = array(
                            'service_title' => $service,
                            'service_description' => $_POST['foodbakery_services']['description'][$key],
                            'service_icon' => $_POST['foodbakery_services']['icon'][$key],
                            'service_price' => $_POST['foodbakery_services']['price'][$key],
                        );
                    }
                }
                update_post_meta( $post_id, 'foodbakery_services', $services_array );
            }
        }
    }
    global $foodbakery_services;
    $foodbakery_services    = new foodbakery_services();
}