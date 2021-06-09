<?php
/**
 * File Type: Restaurants Slider Shortcode Frontend
 */
if ( ! class_exists( 'Foodbakery_Shortcode_Restaurants_Slider_Frontend' ) ) {

	class Foodbakery_Shortcode_Restaurants_Slider_Frontend {

		/**
		 * Constant variables
		 */
		var $PREFIX = 'foodbakery_restaurants_slider';

		public function __construct() {
			add_shortcode( $this->PREFIX, array( $this, 'foodbakery_restaurants_slider_shortcode_callback' ) );
		}

		public function foodbakery_restaurants_slider_shortcode_callback( $atts, $content = "" ) {
                    
//                    
//                    
//                    echo "<pre>";
//                    print_r($atts);
//                    echo "</pre>";
//                   // die('========');
//                    
//                    
//                    
//                    
                    
                    
                    
			$restaurant_short_counter = isset( $atts['restaurant_counter'] ) && $atts['restaurant_counter'] != '' ? ( $atts['restaurant_counter'] ) : rand( 123, 9999 );
                        $page_element_size  = isset( $atts['foodbakery_restaurants_slider_element_size'] )? $atts['foodbakery_restaurants_slider_element_size'] : 100;
                        ob_start();
                        if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                            echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
                        }
			wp_enqueue_script( 'foodbakery-restaurant-functions' );
			?>
			<div class="row3333">
				<div class="foodbakery-restaurant-content" id="foodbakery-restaurant-content-<?php echo esc_html( $restaurant_short_counter ); ?>">
					<div id="Restaurant-content-<?php echo esc_html( $restaurant_short_counter ); ?>">
						<?php
						$restaurant_arg = array(
							'restaurant_short_counter' => $restaurant_short_counter,
							'atts' => $atts,
							'content' => $content,
						);
						$this->foodbakery_restaurants_content( $restaurant_arg );
						?>
					</div>
				</div>   
			</div>
			<?php
                        if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                            echo  '</div>';
                          } 
                          
                        $post_data = ob_get_clean();
                        return $post_data;
		}

		public function foodbakery_restaurants_content( $restaurant_arg = '' ) {

			global $wpdb, $foodbakery_form_fields_frontend;

			if ( isset( $_REQUEST['restaurant_arg'] ) && $_REQUEST['restaurant_arg'] ) {
				$restaurant_arg = $_REQUEST['restaurant_arg'];
				$restaurant_arg = json_decode( str_replace( '\"', '"', $restaurant_arg ) );
				$restaurant_arg = $this->toArray( $restaurant_arg );
			}
			if ( isset( $restaurant_arg ) && $restaurant_arg != '' && ! empty( $restaurant_arg ) ) {
				extract( $restaurant_arg );
			}
                        
                      
			
			$default_date_time_formate = 'd-m-Y H:i:s';
			$restaurant_view = 'slider';
			$restaurant_sort_by = 'recent'; // default value
			$restaurant_sort_order = 'desc';   // default value
			
			$restaurant_type = isset( $atts['restaurant_type'] ) ? $atts['restaurant_type'] : '';
			$restaurant_restaurant_featured = isset( $atts['restaurant_featured'] ) ? $atts['restaurant_featured'] : 'all';
			$restaurant_sort_by = isset( $atts['restaurant_sort_by'] ) ? $atts['restaurant_sort_by'] : 'recent';
			$posts_per_page = '-1';
			$content_columns = 'page-content col-lg-12 col-md-12 col-sm-12 col-xs-12'; // if filteration not true
			
			$element_filter_arr = array();
			$element_filter_arr[] = array(
				'key' => 'foodbakery_restaurant_posted',
				'value' => strtotime( date( $default_date_time_formate ) ),
				'compare' => '<=',
			);
			$element_filter_arr[] = array(
				'key' => 'foodbakery_restaurant_expired',
				'value' => strtotime( date( $default_date_time_formate ) ),
				'compare' => '>=',
			);
			$element_filter_arr[] = array(
				'key' => 'foodbakery_restaurant_status',
				'value' => 'active',
				'compare' => '=',
			);
			// if restaurant type
			if ( $restaurant_type != '' ) {
				$element_filter_arr[] = array(
					'key' => 'foodbakery_restaurant_type',
					'value' => $restaurant_type,
					'compare' => '=',
				);
			}
			// if featured restaurant
			if ( $restaurant_restaurant_featured == 'only-featured' ) {
				$element_filter_arr[] = array(
					'key' => 'foodbakery_restaurant_is_featured',
					'value' => 'on',
					'compare' => '=',
				);
			}
			// if restaurant sort by
                        $qryvar_restaurant_sort_type    = 'DESC';
                        $qryvar_sort_by_column = 'post_date';
			if ( $restaurant_sort_by == 'recent' ) {
				$qryvar_restaurant_sort_type = 'DESC';
				$qryvar_sort_by_column = 'post_date';
			} elseif ( $restaurant_sort_by == 'alphabetical' ) {
				$qryvar_restaurant_sort_type = 'ASC';
				$qryvar_sort_by_column = 'title';
			}
			
			$args = array(
				'posts_per_page' => $posts_per_page,
				'post_type' => 'restaurants',
				'post_status' => 'publish',
				'orderby' => $qryvar_sort_by_column,
				'order' => $qryvar_restaurant_sort_type,
				'fields' => 'ids', // only load ids
				'meta_query' => array(
					$element_filter_arr,
				),
			);
			$restaurant_loop_obj = new WP_Query( $args );
                        
//                              echo "<pre>";
//                    print_r($restaurant_loop_obj);
//                    echo "</pre>";
                        
                        
			?>
				<div class1="<?php echo esc_html( $content_columns ); ?>">
					<?php
					
					
					set_query_var( 'restaurant_loop_obj', $restaurant_loop_obj );
					set_query_var( 'restaurant_view', $restaurant_view );
					set_query_var( 'restaurant_short_counter', $restaurant_short_counter );
					set_query_var( 'atts', $atts );
					foodbakery_get_template_part( 'restaurant', 'slider', 'restaurants' );
					?>
				</div>
			<?php
			wp_reset_postdata(); 
		}
	}

	global $foodbakery_shortcode_restaurants_slider_frontend;
	$foodbakery_shortcode_restaurants_slider_frontend = new Foodbakery_Shortcode_Restaurants_Slider_Frontend();
}