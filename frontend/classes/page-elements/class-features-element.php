<?php

/**
 * File Type: Features Element
 */
if (!class_exists('foodbakery_features_element')) {

    class foodbakery_features_element {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('foodbakery_features_element_html', array($this, 'foodbakery_features_element_html_callback'), 11, 1);
        }

        /*
         * Output features html for frontend on restaurant detail page.
         */

        public function foodbakery_features_element_html_callback($post_id) {
            $features_list = get_post_meta($post_id, 'foodbakery_restaurant_feature_list', true);
          
            $restaurant_type_slug = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
            $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));

            $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
         $type_features_not_selected = get_post_meta($restaurant_type_id, 'foodbakery_enable_not_selected', true);
            $type_features = get_post_meta($restaurant_type_id, 'feature_lables', true);
            
            if( !empty($features_list) || $type_features_not_selected=='on'){
                ?>
                <div class="features-holder">
                <div class="section-title">
                    <h2><?php echo esc_html( 'Features', 'foodbakery' ) ?></h2>
                </div>
            <?php 
            
            }
          
            $foodbakery_feature_icon = get_post_meta($restaurant_type_id, 'foodbakery_feature_icon', true);
            $type_features_not_selected = get_post_meta($restaurant_type_id, 'foodbakery_enable_not_selected', true);
            if ($type_features_not_selected != 'on') {
                if (isset($features_list) && !empty($features_list)) {
                    $html = '';
                    $html .= '<ul class="category-list">';
                    foreach ($features_list as $feature_data) {
                        $icon = '';

                        $feature_exploded = explode("_icon", $feature_data);


                        $features_data_name = isset($feature_exploded[0]) ? $feature_exploded[0] : '';
                        $feature_icon = isset($feature_exploded[1]) ? $feature_exploded[1] : '';
                           
                
                  if($feature_icon !='' && $feature_icon !=' '){
                  $feature_icon=' <i class="' . $feature_icon . '"></i>';
                 
                  }
                        $html .= '<li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">'.$feature_icon . $features_data_name . '</li>';
                    }
                    $html .= '</ul>';
                    echo force_balance_tags($html);
                }
            } else {
                $html = '';
                $html .= '<ul class="category-list">';
                foreach ($type_features as $key => $label) {
                  
                  $feature_icon = isset($foodbakery_feature_icon[$key]) ? $foodbakery_feature_icon[$key] : '';
                
                  if($feature_icon !='' && $feature_icon !=' '){
                  $feature_icon=' <i class="' . $feature_icon . '"></i>';
                 
                  }
                    foreach ($features_list as $feature_data) {
                        $feature_exploded = explode("_icon", $feature_data);
                         $icon='';
                        $features_data_name = isset($feature_exploded[0]) ? $feature_exploded[0] : '';
                     
                        if ($features_data_name == $label) {
                            $icon = 'icon-check';
                            break;
                        } else {
                            $icon = 'icon-cross';
                            
                        }
                    }
                    $html .= '<li class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><i class="'.$icon.'"></i></i>' . $label . $feature_icon.'</li>';
                }
                $html .= '</ul>';
                echo force_balance_tags($html);
            }
             if(!empty($features_list)){
                ?>
                    </div>
            <?php
             }
        }
        

    }

    global $foodbakery_features_element;
    $foodbakery_features_element = new foodbakery_features_element();
}