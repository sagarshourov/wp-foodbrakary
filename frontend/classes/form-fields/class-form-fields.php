<?php

/**
 * File Type: Form Fields
 */
if (!class_exists('foodbakery_form_fields_frontend')) {

    class foodbakery_form_fields_frontend {

        private $counter = 0;

        public function __construct() {
            // Do something...
        }

        /* ----------------------------------------------------------------------
         * @ render label
         * --------------------------------------------------------------------- */

        public function foodbakery_form_label($name = 'Label Not defined') {
            global $post, $pagenow;
            $foodbakery_output = '<li class="to-label">';
            $foodbakery_output .= '<label>' . $name . '</label>';
            $foodbakery_output .= '</li>';
            return $foodbakery_output;
        }

        /* ----------------------------------------------------------------------
         * @ render description
         * --------------------------------------------------------------------- */

        public function foodbakery_form_description($description = '') {
            global $post, $pagenow;
            if ($description == '') {
                return;
            }
            $foodbakery_output = '<div class="left-info">';
            $foodbakery_output .= '<p>' . $description . '</p>';
            $foodbakery_output .= '</div>';
            return $foodbakery_output;
        }

        /* ----------------------------------------------------------------------
         * @ render Headings
         * --------------------------------------------------------------------- */

        public function foodbakery_heading_render($params = '') {
            global $post;
            extract($params);
            $foodbakery_output = '<div class="theme-help" id="' . sanitize_html_class($id) . '">
                            <h4 style="padding-bottom:0px;">' . esc_attr($name) . '</h4>
                            <div class="clear"></div>
                          </div>';
            echo force_balance_tags($foodbakery_output);
        }

       
        public function foodbakery_form_text_render($params = '') {

            global $post, $pagenow, $user;

            if (isset($params) && is_array($params)) {
                extract($params);
            }
           
            $foodbakery_output = '';
            $prefix_enable = 'true'; // default value of prefix add in name and id
            if (!isset($id)) {
                $id = '';
            }
            if (!isset($std)) {
                $std = '';
            }

            if (isset($prefix_on)) {
                $prefix_enable = $prefix_on;
            }
            
            $prefix = 'foodbakery_'; // default prefix
            if (isset($field_prefix) && $field_prefix != '') {
                $prefix = $field_prefix;
            }
            if ($prefix_enable != true) {
                $prefix = '';
            }
            if ($pagenow == 'post.php') {
                if (isset($cus_field) && $cus_field == true) {
                    $foodbakery_value = get_post_meta($post->ID, $id, true);
                } else {
                    $foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
                }
            } elseif (isset($usermeta) && $usermeta == true) {
                if (isset($cus_field) && $cus_field == true) {
                    $foodbakery_value = get_the_author_meta($id, $user->ID);
                } else {
                    if (isset($id) && $id != '') {
                        $foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
                    }
                }
            } else {
                $foodbakery_value = isset($std) ? $std : '';
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }

            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }

            $foodbakery_rand_id = time();

            if (isset($rand_id) && $rand_id != '') {
                $foodbakery_rand_id = $rand_id;
            }

            $html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';
            
            if (isset($cus_field) && $cus_field == true) {
                $html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
            } else {
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
            }

            if (isset($array) && $array == true) {
                $html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
            }

            if (isset($cust_id) && $cust_id != '') {
                $html_id = ' id="' . $cust_id . '"';
            }

            if (isset($cust_name) && $cust_name != '') {
                $html_name = ' name="' . $cust_name . '"';
            }

            // Disabled Field
            $foodbakery_visibilty = '';
            if (isset($active) && $active == 'in-active') {
                $foodbakery_visibilty = 'readonly="readonly"';
            }

            $foodbakery_required = '';
            if (isset($required) && $required == 'yes') {
                $foodbakery_required = ' required';
            }

            $foodbakery_classes = '';
            if (isset($classes) && $classes != '') {
                $foodbakery_classes = ' class="' . $classes . '"';
            }
            $extra_atributes = '';
            if (isset($extra_atr) && $extra_atr != '') {
                $extra_atributes = $extra_atr;
            }

            $foodbakery_input_type = 'text';
            if (isset($cust_type) && $cust_type != '') {
                $foodbakery_input_type = $cust_type;
            }

            $foodbakery_before = '';
            if (isset($before) && $before != '') {
                $foodbakery_before = '<div class="' . $before . '">';
            }

            $foodbakery_after = '';
            if (isset($after) && $after != '') {
                $foodbakery_after = $after;
            }

            if ($html_id == ' id=""' || $html_id == ' id="foodbakery_"') {
                $html_id = '';
            }
           
            if (isset($rang) && $rang == true && isset($min) && isset($max)) {
               
                if(isset($both_rang) && $both_rang==true){
                    $data_min_max='yes';
                }
                else{
                      $data_min_max='no';
                }
                $foodbakery_output .= '<div class="cs-drag-slider" data-slider-min="' . $min . '" data-slider-max="' . $max . '" data-slider-step="1" data-min-max="'.$data_min_max.'" data-slider-value="' . $value . '">';
            }
            $foodbakery_output .= $foodbakery_before;
            if ($value != '') {
                $foodbakery_output .= '<input type="' . $foodbakery_input_type . '" ' . $foodbakery_visibilty . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . $html_id . $html_name . ' value="' . $value . '" />';
            } else {
                $foodbakery_output .= '<input type="' . $foodbakery_input_type . '" ' . $foodbakery_visibilty . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . $html_id . $html_name . ' />';
            }

            $foodbakery_output .= $foodbakery_after;

            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        public function foodbakery_form_radio_render($params = '') {
            global $post, $pagenow;
            extract($params);

            $foodbakery_output = '';

            if (!isset($id)) {
                $id = '';
            }

            $prefix_enable = 'true';    // default value of prefix add in name and id

            if (isset($prefix_on)) {
                $prefix_enable = $prefix_on;
            }

            $prefix = 'foodbakery_';    // default prefix
            if (isset($field_prefix) && $field_prefix != '') {
                $prefix = $field_prefix;
            }
            if ($prefix_enable != true) {
                $prefix = '';
            }

            $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }

            if (isset($cus_field) && $cus_field == true) {
                $html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
            } else {
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
            }

            if (isset($array) && $array == true) {
                $html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
            }

            if (isset($cust_id) && $cust_id != '') {
                $html_id = ' id="' . $cust_id . '"';
            }

            if (isset($cust_name)) {
                $html_name = ' name="' . $cust_name . '"';
            }

            $html_id = isset($html_id) ? $html_id : '';

            // Disbaled Field
            $foodbakery_visibilty = '';
            if (isset($active) && $active == 'in-active') {
                $foodbakery_visibilty = 'readonly="readonly"';
            }
            $foodbakery_required = '';
            if (isset($required) && $required == 'yes') {
                $foodbakery_required = ' required';
            }
            $foodbakery_classes = '';
            if (isset($classes) && $classes != '') {
                $foodbakery_classes = ' class="' . $classes . '"';
            }

            $extra_atributes = '';
            if (isset($extra_atr) && $extra_atr != '') {
                $extra_atributes = $extra_atr;
            }

            if ($html_id == ' id=""' || $html_id == ' id="foodbakery_"') {
                $html_id = '';
            }

            $foodbakery_output .= '<input type="radio" ' . $foodbakery_visibilty . $foodbakery_required . ' ' . $foodbakery_classes . ' ' . $extra_atributes . ' ' . $html_id . $html_name . ' value="' . sanitize_text_field($value) . '" />';

            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /**
         * @ render Radio field
         */

        
         public function foodbakery_form_hidden_render($params = '') {
            global $post, $pagenow;
            extract($params);

            $foodbakery_rand_id = time();

            if (!isset($id)) {
                $id = '';
            }
            $html_id = '';
            $html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
            $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';

            if (isset($array) && $array == true) {
                $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
            }

            if (isset($cust_id) && $cust_id != '') {
                $html_id = ' id="' . $cust_id . '"';
            }

            if (isset($cust_name)) {
                $html_name = ' name="' . $cust_name . '"';
            }

            $foodbakery_classes = '';
            if (isset($classes) && $classes != '') {
                $foodbakery_classes = ' class="' . $classes . '"';
            }

            $extra_atributes = '';
            if (isset($extra_atr) && $extra_atr != '') {
                $extra_atributes = $extra_atr;
            }

            if ($html_id == ' id=""' || $html_id == ' id="foodbakery_"') {
                $html_id = '';
            }

            $foodbakery_output = '<input type="hidden" ' . $html_id . ' ' . $foodbakery_classes . ' ' . $extra_atributes . ' ' . $html_name . ' value="' . sanitize_text_field($std) . '" />';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Date field
         * --------------------------------------------------------------------- */

        public function foodbakery_form_date_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $foodbakery_format = 'd-m-Y';
            if (isset($format) && $format != '') {
                $foodbakery_format = $format;
            }
            $foodbakery_required = '';
            if (isset($required) && $required == 'yes') {
                $foodbakery_required = ' required="required"';
            }
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }
            $foodbakery_rand_id = time();
            $html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
            $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
            $foodbakery_piker_id = $id;
            if (isset($array) && $array == true) {
                $html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
                $foodbakery_piker_id = $id . $foodbakery_rand_id;
            }
            if (isset($force_empty) && $force_empty == true) {
                $value = '';
            }
            $foodbakery_output = '<div  class="' . $classes . '">';
            $foodbakery_output .= '<script>
                                jQuery(function(){
                                    jQuery("#foodbakery_' . $foodbakery_piker_id . '").datetimepicker({
                                        format:"' . $foodbakery_format . '",
                                        timepicker:false
                                    });
                                });
                          </script>';
            $foodbakery_output .= '<input type="text"' . $foodbakery_required . ' class="cs-form-text cs-input form-control" ' . $html_id . $html_name . '  value="' . sanitize_text_field($value) . '" placeholder="' . $name . '" />';
            $foodbakery_output .= $this->foodbakery_form_description($description);
            $foodbakery_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Textarea field
         * --------------------------------------------------------------------- */

        public function foodbakery_form_textarea_render($params = '') {
            global $post, $pagenow;
            $name = '';
            extract($params);
            if ($pagenow == 'post.php') {
                $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            } else {
                $foodbakery_value = $std;
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $foodbakery_rand_id = time();
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }
            $html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
            $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
            }
            $foodbakery_required = '';
            if (isset($required) && $required == 'yes') {
                $foodbakery_required = ' required="required"';
            }
            $foodbakery_output = '<div class="' . $classes . '">';
            $foodbakery_output .= ' <textarea' . $foodbakery_required . ' rows="5" cols="30"' . $html_id . $html_name . ' placeholder="' . $name . '">' . sanitize_text_field($value) . '</textarea>';
            $foodbakery_output .= $this->foodbakery_form_description($description);
            $foodbakery_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Rich edito field
         * --------------------------------------------------------------------- */

        public function foodbakery_form_editor_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if ($pagenow == 'post.php') {
                $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            } else {
                $foodbakery_value = $std;
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $foodbakery_output = '<div class="input-info">';
            $foodbakery_output .= '<div class="row">';
            $foodbakery_output .= '<div class="col-md-12">';
            ob_start();
            wp_editor($value, 'foodbakery_' . sanitize_html_class($id), $settings = array('textarea_name' => 'foodbakery_' . sanitize_html_class($id), 'editor_class' => 'text-input', 'teeny' => true, 'media_buttons' => false, 'textarea_rows' => 8, 'quicktags' => false));
            $foodbakery_editor_contents = ob_get_clean();
            $foodbakery_output .= $foodbakery_editor_contents;
            $foodbakery_output .= '</div>';
            $foodbakery_output .= $this->foodbakery_form_description($description);
            $foodbakery_output .= '</div>';
            $foodbakery_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }
		
		/**
         * @ render select field
         */
        public function foodbakery_form_select_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $prefix_enable = 'true';    // default value of prefix add in name and id
            if (!isset($id)) {
                $id = '';
            }
            $foodbakery_output = '';

            if (isset($prefix_on)) {
                $prefix_enable = $prefix_on;
            }

            $prefix = 'foodbakery_';    // default prefix
            if (isset($field_prefix) && $field_prefix != '') {
                $prefix = $field_prefix;
            }
            if ($prefix_enable != true) {
                $prefix = '';
            }

            $foodbakery_onchange = '';

            if ($pagenow == 'post.php') {
                if (isset($cus_field) && $cus_field == true) {
                    $foodbakery_value = get_post_meta($post->ID, $id, true);
                } else {
                    $foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
                }
            } elseif (isset($usermeta) && $usermeta == true) {
                if (isset($cus_field) && $cus_field == true) {
                    $foodbakery_value = get_the_author_meta($id, $user->ID);
                } else {
                    if (isset($id) && $id != '') {
                        $foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
                    }
                }
            } else {
                $foodbakery_value = $std;
            }

            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }

            $foodbakery_rand_id = time();
            if (isset($rand_id) && $rand_id != '') {
                $foodbakery_rand_id = $rand_id;
            }

            $html_wraper = ' id="wrapper_' . sanitize_html_class($id) . '"';
            $html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';
            if (isset($cus_field) && $cus_field == true) {
                $html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
            } else {
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
            }

            if (isset($array) && $array == true) {
                $html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
                $html_wraper = ' id="wrapper_' . sanitize_html_class($id) . $foodbakery_rand_id . '"';
            }

            if (isset($cust_id) && $cust_id != '') {
                $html_id = ' id="' . $cust_id . '"';
            }

            if (isset($cust_name)) {
                $html_name = ' name="' . $cust_name . '"';
            }

            $foodbakery_display = '';
            if (isset($status) && $status == 'hide') {
                $foodbakery_display = 'style=display:none';
            }

            if (isset($onclick) && $onclick != '') {
                $foodbakery_onchange = 'onchange="' . $onclick . '"';
            }

            $foodbakery_visibilty = '';
            if (isset($active) && $active == 'in-active') {
                $foodbakery_visibilty = 'readonly="readonly"';
            }
            $foodbakery_required = '';
            if (isset($required) && $required == 'yes') {
                $foodbakery_required = ' required';
            }
            $foodbakery_classes = '';
            if (isset($classes) && $classes != '') {
                $foodbakery_classes = ' class="' . $classes . '"';
            }
            $extra_atributes = '';
            if (isset($extra_atr) && $extra_atr != '') {
                $extra_atributes = $extra_atr;
            }

            if (isset($markup) && $markup != '') {
                $foodbakery_output .= $markup;
            }

            if (isset($div_classes) && $div_classes <> "") {
                $foodbakery_output .= '<div class="' . esc_attr($div_classes) . '">';
            }

            if ($html_id == ' id=""' || $html_id == ' id="foodbakery_"') {
                $html_id = '';
            }

            $foodbakery_output .= '<select ' . $foodbakery_visibilty . ' ' . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . $html_id . $html_name . ' ' . $foodbakery_onchange . ' >';
            if (isset($options_markup) && $options_markup == true) {
                $foodbakery_output .= $options;
            } else {
                if (is_array($options)) {
                    foreach ($options as $key => $option) {
                        if (!is_array($option)) {
                            $foodbakery_output .= '<option ' . selected($key, $value, false) . ' value="' . $key . '">' . $option . '</option>';
                        }
                    }
                }
            }
            $foodbakery_output .= '</select>';

            if (isset($div_classes) && $div_classes <> "") {
                $foodbakery_output .= '</div>';
            }

            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Multi Select field
         * --------------------------------------------------------------------- */

        public function foodbakery_form_multiselect_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $foodbakery_onchange = '';
            if ($pagenow == 'post.php') {
                $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            } else {
                $foodbakery_value = $std;
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $foodbakery_rand_id = time();
            $html_wraper = ' id="wrapper_' . sanitize_html_class($id) . '"';
            $html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
            $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '[]"';
            $foodbakery_display = '';
            if (isset($status) && $status == 'hide') {
                $foodbakery_display = 'style=display:none';
            }
            if (isset($onclick) && $onclick != '') {
                $foodbakery_onchange = 'onchange="javascript:' . $onclick . '(this.value, \'' . esc_js(admin_url('admin-ajax.php')) . '\')"';
            }
            if (!is_array($value)) {
                $value = array();
            }
            $foodbakery_required = '';
            if (isset($required) && $required == 'yes') {
                $foodbakery_required = ' required="required"';
            }
            $foodbakery_output = '<ul class="form-elements"' . $html_wraper . ' ' . $foodbakery_display . '>';
            $foodbakery_output .= $this->foodbakery_form_label($name);
            $foodbakery_output .= '<li class="to-field multiple">';
            $foodbakery_output .= '<select' . $foodbakery_required . ' class="multiple" multiple="multiple" ' . $html_id . $html_name . ' ' . $foodbakery_onchange . ' style="height:110px !important;"data-placeholder="' . esc_html__("Please Select", "foodbakery") . '" class="chosen-select">';
            foreach ($options as $key => $option) {
                $selected = '';
                if (in_array($key, $value)) {
                    $selected = 'selected="selected"';
                }
                $foodbakery_output .= '<option ' . $selected . 'value="' . $key . '">' . $option . '</option>';
            }
            $foodbakery_output .= '</select>';
            $foodbakery_output .= $this->foodbakery_form_description($description);
            $foodbakery_output .= '</li>';
            $foodbakery_output .= '</ul>';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Checkbox field
         * --------------------------------------------------------------------- */

        public function foodbakery_form_checkbox_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $prefix_enable = 'true';    // default value of prefix add in name and id

            $foodbakery_output = '';

            if (isset($prefix_on)) {
                $prefix_enable = $prefix_on;
            }

            if (!isset($id)) {
                $id = '';
            }
            $prefix = 'foodbakery_';    // default prefix
            if (isset($field_prefix) && $field_prefix != '') {
                $prefix = $field_prefix;
            }
            if ($prefix_enable != true) {
                $prefix = '';
            }
            if ($pagenow == 'post.php') {
                $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            } elseif (isset($usermeta) && $usermeta == true) {
                if (isset($cus_field) && $cus_field == true) {
                    $foodbakery_value = get_the_author_meta($id, $user->ID);
                } else {
                    if (isset($id) && $id != '') {
                        $foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
                    }
                }
            } else {
                $foodbakery_value = $std;
            }

            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }

            $foodbakery_rand_id = time();

            $html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';
            $btn_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
            
            $html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';

            if (isset($array) && $array == true) {
                $html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $btn_name = ' name="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
            }

            if (isset($cust_id) && $cust_id != '') {
                $html_id = ' id="' . $cust_id . '"';
            }
			
			if ( isset($cust_name)) {
				$html_name = ' name="' . $cust_name . '"';
			}

            if (isset($cust_name) && $cust_name == '' ) {
                $html_name = '';
            }
			
            $checked = isset($value) && $value == 'on' ? ' checked="checked"' : '';
            // Disbaled Field
            $foodbakery_visibilty = '';
            if (isset($active) && $active == 'in-active') {
                $foodbakery_visibilty = 'readonly="readonly"';
            }
            $foodbakery_required = '';
            if (isset($required) && $required == 'yes') {
                $foodbakery_required = ' required';
            }
            $foodbakery_classes = '';
            if (isset($classes) && $classes != '') {
                $foodbakery_classes = ' class="' . $classes . '"';
            }
            $extra_atributes = '';
            if (isset($extra_atr) && $extra_atr != '') {
                $extra_atributes = $extra_atr;
            }

            if ($html_id == ' id=""' || $html_id == ' id="foodbakery_"') {
                $html_id = '';
            }
            
            if (isset($simple) && $simple == true) {
                if ($value == '') {
                    $foodbakery_output .= '<input type="checkbox" ' . $html_id . $html_name . ' ' . $foodbakery_classes . ' ' . $checked . ' ' . $extra_atributes . ' />';
                } else {
                    $foodbakery_output .= '<input type="checkbox" ' . $html_id . $html_name . ' ' . $foodbakery_classes . ' ' . $checked . ' value="' . $value . '"' . $extra_atributes . ' />';
                }
            } else {
                $foodbakery_output .= '<label class="pbwp-checkbox cs-chekbox">';
                $foodbakery_output .= '<input type="hidden"' . $html_id . $html_name . ' value="' . sanitize_text_field($std) . '" />';
                $foodbakery_output .= '<input type="checkbox" ' . $foodbakery_classes . ' ' . $btn_name . $checked . ' ' . $extra_atributes . ' />';
                $foodbakery_output .= '<span class="pbwp-box"></span>';
                $foodbakery_output .= '</label>';
            }

            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render File Upload field
         * --------------------------------------------------------------------- */

        public function foodbakery_media_url($params = '') {
            global $post, $pagenow;
            extract($params);
            $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $foodbakery_rand_id = time();
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }
            $html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
            $html_id_btn = ' id="foodbakery_' . sanitize_html_class($id) . '_btn"';
            $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_rand_id . '"';
                $html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_rand_id . '_btn"';
                $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
            }
            $foodbakery_output = '<ul class="form-elements">';
            $foodbakery_output .= $this->foodbakery_form_label($name);
            $foodbakery_output .= '<li class="to-field">';
            $foodbakery_output .= '<div class="input-sec">';
            $foodbakery_output .= '<input type="text" class="cs-form-text cs-input" ' . $html_id . $html_name . ' value="' . sanitize_text_field($value) . '" />';
            $foodbakery_output .= '<label class="cs-browse">';
            $foodbakery_output .= '<input type="button" ' . $html_id_btn . $html_name . ' class="uploadfile left" value="' . esc_html__('Browse', 'foodbakery') . '"/>';
            $foodbakery_output .= '</label>';
            $foodbakery_output .= '</div>';
            $foodbakery_output .= $this->foodbakery_form_description($description);
            $foodbakery_output .= '</li>';
            $foodbakery_output .= '</ul>';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render File Upload field
         * --------------------------------------------------------------------- */

        public function foodbakery_form_fileupload_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if ($pagenow == 'post.php') {
                $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            } else {
                $foodbakery_value = $std;
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            if (isset($value) && $value != '') {
                $display = 'style=display:block';
            } else {
                $display = 'style=display:none';
            }
            $class = '';
            if (isset($value) && $classes != '') {
                $class = " " . $classes;
            }
            $foodbakery_random_id = FOODBAKERY_FUNCTIONS()->rand_id();
            $btn_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
            $html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
            $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $btn_name = ' name="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
                $html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
                $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
            }
            $foodbakery_output = '<ul class="form-elements">';
            $foodbakery_output .= $this->foodbakery_form_label($name);
            $foodbakery_output .= '<li class="to-field">';
            $foodbakery_output .= '<div class="page-wrap" ' . $display . ' id="foodbakery_' . sanitize_html_class($id) . '_box">';
            $foodbakery_output .= '<div class="gal-active">';
            $foodbakery_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
            $foodbakery_output .= '<ul id="gal-sortable">';
            $foodbakery_output .= '<li class="ui-state-default" id="">';
            $foodbakery_output .= '<div class="thumb-secs"> <img src="' . esc_url($value) . '" id="foodbakery_' . sanitize_html_class($id) . '_img" width="100" alt="" />';
            $foodbakery_output .= '<div class="gal-edit-opts"><a href="javascript:del_media(\'foodbakery_' . sanitize_html_class($id) . '\')" class="delete"></a> </div>';
            $foodbakery_output .= '</div>';
            $foodbakery_output .= '</li>';
            $foodbakery_output .= '</ul>';
            $foodbakery_output .= '</div>';
            $foodbakery_output .= '</div>';
            $foodbakery_output .= '</div>';
            $foodbakery_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $value . '"/>';
            $foodbakery_output .= '<label class="browse-icon"><input' . $btn_name . 'type="button" class="cs-uploadMedia left ' . $class . '" value="' . esc_html__('Browse', 'foodbakery') . '" /></label>';
            $foodbakery_output .= '</li>';
            $foodbakery_output .= '</ul>';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render File Upload field
         * --------------------------------------------------------------------- */

        public function foodbakery_form_cvupload_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if ($pagenow == 'post.php') {
                $foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
            } else {
                $foodbakery_value = $std;
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            if (isset($value) && $value != '') {
                $display = 'style=display:block';
            } else {
                $display = 'style=display:none';
            }
            $foodbakery_random_id = FOODBAKERY_FUNCTIONS()->rand_id();
            $btn_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
            $html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
            $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $btn_name = ' name="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
                $html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
                $html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
            }
            $foodbakery_output = '<div class="cs-img-detail resume-upload">';
            $foodbakery_output = '<div class="upload-btn-div">';
            $foodbakery_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
            $foodbakery_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $value . '"/>';
            $foodbakery_output .= '<input' . $btn_name . 'type="button" class="cs-uploadMedia uplaod-btn" value="' . esc_html__('Browse', 'foodbakery') . '"/>';
            $foodbakery_output .= '<div class="alert alert-dismissible user-resume" id="foodbakery_' . sanitize_html_class($id) . '_img">';
            if (isset($value) and $value <> '') {
                $foodbakery_output .= '<div>' . basename($value);
                $foodbakery_output .= '<button aria-label="Close" data-dismiss="alert" class="close" type="button">';
                $foodbakery_output .= '<span aria-hidden="true" class="cs-color">×</span>';
                $foodbakery_output .= '</button>';
                $foodbakery_output .= '<a href="javascript:foodbakery_del_media(\'foodbakery_' . sanitize_html_class($id) . '\')" class="delete"></a></div>';
            }
            $foodbakery_output .= '</div>';
            $foodbakery_output .= '</div>';
            $foodbakery_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($foodbakery_output);
            } else {
                echo force_balance_tags($foodbakery_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Random String
         * --------------------------------------------------------------------- */

        public function foodbakery_generate_random_string($length = 3) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

    }

    global $foodbakery_form_fields_frontend;
    $foodbakery_form_fields_frontend = new foodbakery_form_fields_frontend();
}