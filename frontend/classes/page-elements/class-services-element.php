<?php
/**
 * File Type: Services Page Element
 */
if (!class_exists('foodbakery_services_element')) {

    class foodbakery_services_element {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('foodbakery_services_element_html', array($this, 'foodbakery_services_element_html_callback'), 11, 1);
        }
        
        /*
         * Output features html for frontend on restaurant detail page.
         */
        public function foodbakery_services_element_html_callback( $post_id ){
			
			$services_limit = foodbakery_cred_limit_check($post_id, 'foodbakery_transaction_restaurant_serv_num');
			
            $restaurant_type_slug      = get_post_meta( $post_id, 'foodbakery_restaurant_type', true );
            $restaurant_type_post      = get_posts(array( 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish' ));
            $restaurant_type_id        = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
            $foodbakery_full_data    = get_post_meta( $restaurant_type_id, 'foodbakery_full_data', true );
			$inquiry_paid = get_post_meta( $restaurant_type_id, "foodbakery_inquiry_paid_form", true );
            
            if ( !isset( $foodbakery_full_data['foodbakery_services_options_element'] ) || $foodbakery_full_data['foodbakery_services_options_element'] != 'on' ){
                $html = '';
            } else {
                $html = '';
                $services_list = get_post_meta( $post_id, 'foodbakery_services', true );
                
                if ( isset ( $services_list ) && !empty( $services_list ) ){
					$services_counter = 1;
					
					$html   = ' <div class="service-rates-holder">
                                    <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="section-title">
                                                            <h2>' . __( 'Service & Rates', 'foodbakery' ) . '</h2>
                                                    </div>
                                            </div>';
                        foreach ( $services_list as $key => $service_data ){
							
							$service_title = isset( $service_data['service_title'] ) && $service_data['service_title'] != '' ? $service_data['service_title'] : '';
							$service_price = isset( $service_data['service_price'] ) && $service_data['service_price'] != '' ? $service_data['service_price'] : '0';
							$random_num = $key . $service_price;
							
                            $html   .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="icon-boxes">';
                                
                                if( isset( $service_data['service_icon'] ) && $service_data['service_icon'] != '' ){
									if( isset( $inquiry_paid ) && $inquiry_paid == 'on' ){
										$html   .= '<div class="img-holder1">
                                                    <span><a href="javascript:void(0);" onClick="foodbakery_add_services(\''. $random_num .'\', \''. $service_title .'\', \''. $service_price .'\')"><i class="' . $service_data['service_icon'] . '"></i></a></span>
                                                </div>';
									}else{
										$html   .= '<div class="img-holder2">
														<span><i class="' . $service_data['service_icon'] . '"></i></span>
													</div>';
									}
                                }
                                
                                $html       .= ' <div class="text-holder">';
                                $html       .= '<div class="title-holder">';
                                if( $service_title != '' ){
                                    $html   .= '<h4>' . $service_title . '</h4>';
                                }
                                
                                if( $service_price != '' ){
                                    $html   .= '<div class="price"><strong>' . foodbakery_get_currency( $service_price, true ) . '</strong></div>';
                                }
                                $html       .= '</div>';
                                if( isset( $service_data['service_description'] ) && $service_data['service_description'] != '' ){
                                    $html   .= '<ul class="icon-liststyle">' . $service_data['service_description'] . '</ul>';
                                }
                                
                                $html   .= '</div>';
                            
                            $html   .= '</div></div>';
							
							if($services_limit == $services_counter){
								break;
							}
							
							$services_counter ++;
                        }
                        
                    $html   .= '</div></div>';
                }
            }
            
            echo force_balance_tags( $html );
            
        }
    }
    global $foodbakery_services_element;
    $foodbakery_services_element    = new foodbakery_services_element();
}