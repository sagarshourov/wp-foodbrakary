<?php
/**
 * File Type: Restaurant Posted By
 */
if (!class_exists('foodbakery_images_gallery')) {

    class foodbakery_images_gallery {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_filter('foodbakery_images_gallery_admin_fields', array($this, 'foodbakery_images_gallery_admin_fields_callback'), 11, 2);
            add_action('save_post', array($this, 'foodbakery_images_gallery_on_submission'), 14);
        }
        
        public function foodbakery_images_gallery_admin_fields_callback( $post_id, $restaurant_type_slug ){
            global $foodbakery_html_fields, $post;
            $post_id                = ( isset( $post_id ) && $post_id != '' )? $post_id : $post->ID;
            $restaurant_type_post      = get_posts(array( 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish' ));
            $restaurant_type_id        = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
            $foodbakery_full_data    = get_post_meta( $restaurant_type_id, 'foodbakery_full_data', true );
            $html                   = '';
            if ( !isset( $foodbakery_full_data['foodbakery_image_gallery_element'] ) || $foodbakery_full_data['foodbakery_image_gallery_element'] != 'on' ){
                return $html = '';
            }
            
            
            $html   .= $foodbakery_html_fields->foodbakery_heading_render(
                    array(
                        'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_image_gallery' ),
                        'cust_name' => 'images_gallery',
                        'classes' => '',
                        'std' => '',
                        'echo' => false,
                        'description' => '',
                        'hint' => ''
                    )
            );
            
            $html   .= '<div id="post_detail_gallery">';
                $foodbakery_opt_array = array(
                    'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_gallery_image' ),
                    'id' => 'detail_page_gallery',
                    'post_id' => $post_id,
                    'classes' => '',
                    'echo' => false,
                    'std' => '',
                );

                $html   .= $foodbakery_html_fields->foodbakery_gallery_render( $foodbakery_opt_array );
            $html   .= '</div>';
            return $html;
        }
        
        public function foodbakery_images_gallery_on_submission( $post_id ){
            if ( get_post_type( $post_id ) == 'restaurants' ){
                
				if(isset($_POST['foodbakery_detail_page_gallery_ids']) && $_POST['foodbakery_detail_page_gallery_ids'] == ''){
					delete_post_meta ( $post_id, 'foodbakery_detail_page_gallery_ids' );
				}
            }
        }
        
    }
    global $foodbakery_images_gallery;
    $foodbakery_images_gallery    = new foodbakery_images_gallery();
}