<?php
/**
 * File Type: About Shortcode
 */
if (!class_exists('Foodbakery_Shortcodes')) {
   
    class Foodbakery_Shortcodes {
        protected $title='title';
       protected $sub_title='Sub Title';
       protected $save_text='save';
        public function __construct() {
            add_action( 'directyory_common_title', array( $this, 'directyory_common_title_call_back' ) );
             add_action( 'directyory_common_save_btn', array( $this, 'directyory_common_save_btn_call_back' ) );
            
        }
        
        protected function directyory_common_title_call_back($title){
             global $post, $foodbakery_html_fields, $foodbakery_form_fields;
            
             $this->title=$title;
             $foodbakery_opt_array = array(
                                'name' => esc_html__('Element Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter element title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'id' => $this->title,
                                    'cust_name' => $this->title.'[]',
                                    'return' => true,
                                ),
                            );
             $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
        }
         protected function directyory_common_subtitle_call_back($sub_title){
             global $post, $foodbakery_html_fields, $foodbakery_form_fields;
             
             $this->sub_title=$sub_title;
             $foodbakery_opt_array = array(
                                'name' => esc_html__('Element Sub  Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter element sub title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'id' => $this->sub_title,
                                    'cust_name' => $this->sub_title.'[]',
                                    'return' => true,
                                ),
                            );
             $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
        }
        protected function directyory_common_save_btn_call_back($ave_text){
            $this->save_text=$ave_text;
            $foodbakery_opt_array = array(
                                'name' => '',
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_html__($this->save_text, 'foodbakery'),
                                    'cust_id' => '',
                                    'cust_type' => 'button',
                                    'classes' => 'cs-admin-btn',
                                    'cust_name' => '',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
        }
    
    }
   
}
