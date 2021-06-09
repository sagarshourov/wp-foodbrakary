<?php

/**
 *  File Type: Custom Fields Class
 */
if (!class_exists('foodbakery_custom_fields_options')) {

    class foodbakery_custom_fields_options {

        /**
         * Start Contructer Function
         */
        public function __construct() {
            add_action('wp_ajax_foodbakery_pb_text', array(&$this, 'foodbakery_pb_text'));
            add_action('wp_ajax_foodbakery_pb_textarea', array(&$this, 'foodbakery_pb_textarea'));
            add_action('wp_ajax_foodbakery_pb_dropdown', array(&$this, 'foodbakery_pb_dropdown'));
            add_action('wp_ajax_foodbakery_pb_date', array(&$this, 'foodbakery_pb_date'));
            add_action('wp_ajax_foodbakery_pb_email', array(&$this, 'foodbakery_pb_email'));
            add_action('wp_ajax_foodbakery_pb_url', array(&$this, 'foodbakery_pb_url'));
            add_action('wp_ajax_foodbakery_pb_range', array(&$this, 'foodbakery_pb_range'));
            add_action('wp_ajax_foodbakery_check_fields_avail', array(&$this, 'foodbakery_check_fields_avail'));
        }

        /**
         * End Contructer Function
         */

        /**
         * Start function how to create Text Fields
         */
        public function foodbakery_pb_text($die = 0, $foodbakery_return = false) {
            global $foodbakery_f_counter, $foodbakery_job_cus_fields;
            $foodbakery_fields_markup = '';
            if (isset($_REQUEST['counter'])) {
                $foodbakery_counter = $_REQUEST['counter'];
            } else {
                $foodbakery_counter = $foodbakery_f_counter;
            }
            if (isset($foodbakery_job_cus_fields[$foodbakery_counter])) {
                $foodbakery_title = isset($foodbakery_job_cus_fields[$foodbakery_counter]['label']) ? sprintf(esc_html__('Text : %s', 'foodbakery'), $foodbakery_job_cus_fields[$foodbakery_counter]['label']) : '';
            } else {
                $foodbakery_title = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_text' );
            }
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_text[required]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_required' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_text[label]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_title' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_text[meta_key]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key' ),
                'check' => true,
                'std' => '',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_text[placeholder]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_place_holder' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_text[enable_srch]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_text[default_value]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_default_value' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_text[collapse_search]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_collapse_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));

            $foodbakery_fields_markup .= $this->foodbakery_fields_fontawsome_icon_jobs(array(
                'id' => 'fontawsome_icon_text',
                'name' => 'foodbakery_cus_field_text[fontawsome_icon]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_icon' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields = array('foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'text', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup);

            $foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);

            if ($foodbakery_return == true) {
                return force_balance_tags($foodbakery_output, true);
            } else {
                echo force_balance_tags($foodbakery_output, true);
            }
            if ($die <> 1)
                die();
        }

        /**
         * End function how to create Text Fields
         */

        /**
         * Start function how to create Textarea Fields
         */
        public function foodbakery_pb_textarea($die = 0, $foodbakery_return = false) {
            global $foodbakery_f_counter, $foodbakery_job_cus_fields;
            $foodbakery_fields_markup = '';
            if (isset($_REQUEST['counter'])) {
                $foodbakery_counter = $_REQUEST['counter'];
            } else {
                $foodbakery_counter = $foodbakery_f_counter;
            }
            if (isset($foodbakery_job_cus_fields[$foodbakery_counter])) {
                $foodbakery_title = isset($foodbakery_job_cus_fields[$foodbakery_counter]['label']) ? sprintf(esc_html__('Text Area : %s', 'foodbakery'), $foodbakery_job_cus_fields[$foodbakery_counter]['label']) : '';
            } else {
                $foodbakery_title = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_text_area' );
            }
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_textarea[required]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_required' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_textarea[label]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_title' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_textarea[meta_key]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key' ),
                'check' => true,
                'std' => '',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
                'id' => '',
                'name' => 'cus_field_textarea[help]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_help_text' ),
                'std' => '',
                'hint' => '',
            ));

            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_textarea[placeholder]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_place_holder' ),
                'std' => '',
                'hint' => '',
            ));

            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_textarea[rows]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_rows' ),
                'std' => '5',
                'hint' => '',
            ));

            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_textarea[cols]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_columns' ),
                'std' => '25',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_textarea[default_value]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_default_value' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_textarea[collapse_search]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_collapse_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_fontawsome_icon_jobs(array(
                'id' => 'fontawsome_icon_textarea',
                'name' => 'foodbakery_cus_field_textarea[fontawsome_icon]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_icon' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields = array('foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'textarea', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup);
            $foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
            if ($foodbakery_return == true) {
                return force_balance_tags($foodbakery_output, true);
            } else {
                echo force_balance_tags($foodbakery_output, true);
            }
            if ($die <> 1)
                die();
        }

        /**
         * Start function how to create Textarea Fields
         */

        /**
         * Start function how to create dropdown option fields
         */
        public function foodbakery_pb_dropdown($die = 0, $foodbakery_return = false) {
            global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_job_cus_fields;
            $foodbakery_fields_markup = '';
            if (isset($_REQUEST['counter'])) {
                $foodbakery_counter = $_REQUEST['counter'];
            } else {
                $foodbakery_counter = $foodbakery_f_counter;
            }
            if (isset($foodbakery_job_cus_fields[$foodbakery_counter])) {
                $foodbakery_title = isset($foodbakery_job_cus_fields[$foodbakery_counter]['label']) ? sprintf(esc_html__('Dropdown : %s', 'foodbakery'), $foodbakery_job_cus_fields[$foodbakery_counter]['label']) : '';
            } else {
                $foodbakery_title = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_dropdown' );
            }
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_dropdown[required]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_required' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_dropdown[label]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_title' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_dropdown[meta_key]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key' ),
                'check' => true,
                'std' => '',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
                'id' => '',
                'name' => 'cus_field_dropdown[help]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_help_text' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_dropdown[enable_srch]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_dropdown[multi]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_multi_select' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_dropdown[post_multi]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_post_multi_select' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_dropdown[first_value]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_first_value' ),
                'std' => '- select -',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_dropdown[collapse_search]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_collapse_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_fontawsome_icon_jobs(array(
                'id' => 'fontawsome_icon_selectbox',
                'name' => 'foodbakery_cus_field_dropdown[fontawsome_icon]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_icon' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= '
			<div class="form-elements">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_options' ) . '</label>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';

            if (isset($foodbakery_job_cus_fields[$foodbakery_f_counter]['options']['value'])) {
                $foodbakery_opt_counter = 0;
                $foodbakery_radio_counter = 1;
                foreach ($foodbakery_job_cus_fields[$foodbakery_f_counter]['options']['value'] as $foodbakery_option) {
                    $foodbakery_checked = (int) $foodbakery_job_cus_fields[$foodbakery_f_counter]['options']['select'][0] == (int) $foodbakery_radio_counter ? ' checked="checked"' : '';
                    $foodbakery_opt_label = $foodbakery_job_cus_fields[$foodbakery_f_counter]['options']['label'][$foodbakery_opt_counter];
                    $foodbakery_fields_markup .= '<div class="pbwp-clone-field">';
                    $foodbakery_opt_array = array(
                        'cust_id' => 'cus_field_dropdown_selected_' . absint($foodbakery_counter),
                        'cust_name' => 'cus_field_dropdown[selected][' . absint($foodbakery_counter) . '][]',
                        'cust_type' => 'radio',
                        'extra_atr' => $foodbakery_checked,
                        'std' => $foodbakery_radio_counter,
                        'return' => true,
                    );
                    $foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                    $foodbakery_opt_array = array(
                        'cust_id' => 'cus_field_dropdown_options_' . absint($foodbakery_counter),
                        'cust_name' => 'cus_field_dropdown[options][' . absint($foodbakery_counter) . '][]',
                        'extra_atr' => ' data-type="option"',
                        'std' => $foodbakery_opt_label,
                        'classes' => 'input-small',
                        'return' => true,
                    );
                    
                    $foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                    $foodbakery_opt_array = array(
                        'cust_id' => 'cus_field_dropdown_options_values_' . absint($foodbakery_counter),
                        'cust_name' => 'cus_field_dropdown[options_values][' . absint($foodbakery_counter) . '][]',
                        'std' => $foodbakery_option,
                        'classes' => 'input-small',
                        'return' => true,
                    );
                    $foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                    $foodbakery_fields_markup .= '<img src="' . esc_url(wp_foodbakery::plugin_url() . '/assets/images/add.png') . '" class="pbwp-clone-field" alt="' . esc_html__('add another choice', 'foodbakery') . '" style="cursor:pointer; margin:0 3px;">
						<img src="' . esc_url(wp_foodbakery::plugin_url() . '/assets/images/remove.png') . '" alt="' . esc_html__('remove this choice', 'foodbakery') . '" class="pbwp-remove-field" style="cursor:pointer;">
					</div>';
                    $foodbakery_opt_counter++;
                    $foodbakery_radio_counter++;
                }
            } else {
                $foodbakery_fields_markup .= '<div class="pbwp-clone-field">';

                $foodbakery_opt_array = array(
                    'cust_id' => 'cus_field_dropdown_selected_' . absint($foodbakery_counter),
                    'cust_name' => 'cus_field_dropdown[selected][' . absint($foodbakery_counter) . '][]',
                    'cust_type' => 'radio',
                    'extra_atr' => ' checked="checked"',
                    'std' => '1',
                    'return' => true,
                );
                $foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'cust_id' => 'cus_field_dropdown_options_' . absint($foodbakery_counter),
                    'cust_name' => 'cus_field_dropdown[options][' . absint($foodbakery_counter) . '][]',
                    'extra_atr' => ' data-type="option"',
                    'std' => '',
                    'classes' => 'input-small',
                    'return' => true,
                );
                $foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'cust_id' => 'cus_field_dropdown_options_values_' . absint($foodbakery_counter),
                    'cust_name' => 'cus_field_dropdown[options_values][' . absint($foodbakery_counter) . '][]',
                    'std' => '',
                    'classes' => 'input-small',
                    'return' => true,
                );
                $foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                $foodbakery_fields_markup .= '<img src="' . esc_url(wp_foodbakery::plugin_url() . '/assets/images/add.png') . '" class="pbwp-clone-field" alt="' . esc_html__('add another choice', 'foodbakery') . '" style="cursor:pointer; margin:0 3px;">
					<img src="' . esc_url(wp_foodbakery::plugin_url() . '/assets/images/remove.png') . '" alt="' . esc_html__('remove this choice', 'foodbakery') . '" class="pbwp-remove-field" style="cursor:pointer;">
				</div>';
            }
            $foodbakery_fields_markup .= '</div>
			</div>';
            $foodbakery_fields = array('foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'dropdown', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup);
            $foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
            if ($foodbakery_return == true) {
                return force_balance_tags($foodbakery_output, true);
            } else {
                echo force_balance_tags($foodbakery_output, true);
            }
            if ($die <> 1)
                die();
        }

        /**
         * End function how to create dropdown option fields
         */

        /**
         * Start function how to create custom fields
         */
        public function foodbakery_pb_date($die = 0, $foodbakery_return = false) {
            global $foodbakery_f_counter, $foodbakery_job_cus_fields;
            $foodbakery_fields_markup = '';
            if (isset($_REQUEST['counter'])) {
                $foodbakery_counter = $_REQUEST['counter'];
            } else {
                $foodbakery_counter = $foodbakery_f_counter;
            }
            if (isset($foodbakery_job_cus_fields[$foodbakery_counter])) {
                $foodbakery_title = isset($foodbakery_job_cus_fields[$foodbakery_counter]['label']) ? sprintf(esc_html__('Date : %s', 'foodbakery'), $foodbakery_job_cus_fields[$foodbakery_counter]['label']) : '';
            } else {
                $foodbakery_title = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_date' );
            }
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_date[required]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_required' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_date[label]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_title' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_date[meta_key]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key' ),
                'check' => true,
                'std' => '',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
                'id' => '',
                'name' => 'cus_field_date[help]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_help_text' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_date[enable_srch]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_date[date_format]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_date_format' ),
                'std' => 'd.m.Y H:i',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_date_format' ) . ': d.m.Y H:i, Y/m/d',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_date[collapse_search]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_collapse_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_fontawsome_icon_jobs(array(
                'id' => 'fontawsome_icon_date',
                'name' => 'foodbakery_cus_field_date[fontawsome_icon]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_icon' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields = array('foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'date', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup);
            $foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
            if ($foodbakery_return == true) {
                return force_balance_tags($foodbakery_output, true);
            } else {
                echo force_balance_tags($foodbakery_output, true);
            }
            if ($die <> 1)
                die();
        }

        /**
         * End function how to create custom fields
         */

        /**
         * Start function how to create custom email fields
         */
        public function foodbakery_pb_email($die = 0, $foodbakery_return = false) {
            global $foodbakery_f_counter, $foodbakery_job_cus_fields;
            $foodbakery_fields_markup = '';
            if (isset($_REQUEST['counter'])) {
                $foodbakery_counter = $_REQUEST['counter'];
            } else {
                $foodbakery_counter = $foodbakery_f_counter;
            }
            if (isset($foodbakery_job_cus_fields[$foodbakery_counter])) {
                $foodbakery_title = isset($foodbakery_job_cus_fields[$foodbakery_counter]['label']) ? sprintf(esc_html__('Email : %s', 'foodbakery'), $foodbakery_job_cus_fields[$foodbakery_counter]['label']) : '';
            } else {
                $foodbakery_title = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_email' );
            }
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_email[required]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_required' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_email[label]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_title' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_email[meta_key]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key' ),
                'check' => true,
                'std' => '',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_email[placeholder]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_place_holder' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
                'id' => '',
                'name' => 'cus_field_email[help]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_help_text' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_email[enable_srch]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_email[default_value]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_default_value' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_email[collapse_search]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_collapse_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_fontawsome_icon_jobs(array(
                'id' => 'fontawsome_icon_email',
                'name' => 'foodbakery_cus_field_email[fontawsome_icon]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_icon' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields = array('foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'email', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup);
            $foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
            if ($foodbakery_return == true) {
                return force_balance_tags($foodbakery_output, true);
            } else {
                echo force_balance_tags($foodbakery_output, true);
            }
            if ($die <> 1)
                die();
        }

        /**
         * End function how to create custom email fields
         */

        /**
         * Start function how to create post custom url fields
         */
        public function foodbakery_pb_url($die = 0, $foodbakery_return = false) {
            global $foodbakery_f_counter, $foodbakery_job_cus_fields;
            $foodbakery_fields_markup = '';
            if (isset($_REQUEST['counter'])) {
                $foodbakery_counter = $_REQUEST['counter'];
            } else {
                $foodbakery_counter = $foodbakery_f_counter;
            }
            if (isset($foodbakery_job_cus_fields[$foodbakery_counter])) {
                $foodbakery_title = isset($foodbakery_job_cus_fields[$foodbakery_counter]['label']) ? sprintf(esc_html__('Url : %s', 'foodbakery'), $foodbakery_job_cus_fields[$foodbakery_counter]['label']) : '';
            } else {
                $foodbakery_title = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_url' );
            }
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_url[required]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_required' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));

            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_url[label]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_title' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_url[meta_key]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key' ),
                'check' => true,
                'std' => '',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_url[placeholder]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_place_holder' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
                'id' => '',
                'name' => 'cus_field_url[help]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_help_text' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_url[enable_srch]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_url[default_value]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_default_value' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_url[collapse_search]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_collapse_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_fontawsome_icon_jobs(array(
                'id' => 'fontawsome_icon_url',
                'name' => 'foodbakery_cus_field_url[fontawsome_icon]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_icon' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields = array('foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'url', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup);
            $foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
            if ($foodbakery_return == true) {
                return force_balance_tags($foodbakery_output, true);
            } else {
                echo force_balance_tags($foodbakery_output, true);
            }
            if ($die <> 1)
                die();
        }

        /**
         * End function how to create post custom url fields
         */

        /**
         * Start function how to create post custom range in fields
         */
        public function foodbakery_pb_range($die = 0, $foodbakery_return = false) {
            global $foodbakery_f_counter, $foodbakery_job_cus_fields;
            $foodbakery_fields_markup = '';
            if (isset($_REQUEST['counter'])) {
                $foodbakery_counter = $_REQUEST['counter'];
            } else {
                $foodbakery_counter = $foodbakery_f_counter;
            }
            if (isset($foodbakery_job_cus_fields[$foodbakery_counter])) {
                $foodbakery_title = isset($foodbakery_job_cus_fields[$foodbakery_counter]['label']) ? sprintf(esc_html__('Range : %s', 'foodbakery'), $foodbakery_job_cus_fields[$foodbakery_counter]['label']) : '';
            } else {
                $foodbakery_title = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_range' );
            }
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_range[required]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_required' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_range[label]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_title' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_range[meta_key]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key' ),
                'check' => true,
                'std' => '',
                'hint' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_meta_key_hint' ),
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_range[placeholder]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_place_holder' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
                'id' => '',
                'name' => 'cus_field_range[help]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_help_text' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_range[min]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_minimum_value' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_range[max]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_maximum_value' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_range[increment]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_increment_step' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_range[enable_srch]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_range[enable_inputs]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_enable_inputs' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_range[srch_style]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_search_style' ),
                'std' => '',
                'options' => array('input' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_input' ), 'slider' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_slider' ), 'input_slider' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_Input_Slider' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
                'id' => '',
                'name' => 'cus_field_range[default_value]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_default_value' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
                'id' => '',
                'name' => 'cus_field_range[collapse_search]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_collapse_search' ),
                'std' => '',
                'options' => array('no' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_no' ), 'yes' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_yes' )),
                'hint' => '',
            ));
            $foodbakery_fields_markup .= $this->foodbakery_fields_fontawsome_icon_jobs(array(
                'id' => 'fontawsome_icon_range',
                'name' => 'foodbakery_cus_field_range[fontawsome_icon]',
                'title' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_icon' ),
                'std' => '',
                'hint' => '',
            ));
            $foodbakery_fields = array('foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'range', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup);
            $foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
            if ($foodbakery_return == true) {
                return force_balance_tags($foodbakery_output, true);
            } else {
                echo force_balance_tags($foodbakery_output, true);
            }
            if ($die <> 1)
                die();
        }
        /**
         * end function how to create post custom range in fields
         */
        /**
         * Start function how to create post fields layout 
         */
        public function foodbakery_fields_layout($foodbakery_fields) {
            global $foodbakery_form_fields;
            $foodbakery_defaults = array('foodbakery_counter' => '1', 'foodbakery_name' => '', 'foodbakery_title' => '', 'foodbakery_markup' => '');
            extract(shortcode_atts($foodbakery_defaults, $foodbakery_fields));
            $foodbakery_html = '<div class="pb-item-container">
				<div class="pbwp-legend">';
            $foodbakery_opt_array = array(
                'std' => $foodbakery_name,
                'id' => 'cus_field_title',
                'cust_name' => 'foodbakery_cus_field_title[]',
                'cust_type' => 'hidden',
                'return' => true,
            );
            $foodbakery_html .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
            $foodbakery_opt_array = array(
                'std' => $foodbakery_counter,
                'id' => 'foodbakery_cus_field_id',
                'cust_name' => 'foodbakery_cus_field_id[]',
                'cust_type' => 'hidden',
                'return' => true,
            );
            $foodbakery_html .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

            if ($foodbakery_name == 'textarea') {
                $foodbakery_show_icon = 'icon-text';
            } else if ($foodbakery_name == 'dropdown') {
                $foodbakery_show_icon = 'icon-download10';
            } else if ($foodbakery_name == 'date') {
                $foodbakery_show_icon = 'icon-calendar-o';
            } else if ($foodbakery_name == 'email') {
                $foodbakery_show_icon = 'icon-envelope4';
            } else if ($foodbakery_name == 'url') {
                $foodbakery_show_icon = 'icon-link4';
            } else if ($foodbakery_name == 'range') {
                $foodbakery_show_icon = 'icon-target5';
            } else {
                $foodbakery_show_icon = 'icon-file-text-o';
            }

            $foodbakery_html .= '
					<div class="pbwp-label"><i class="' . $foodbakery_show_icon . '"></i> ' . esc_attr($foodbakery_title) . ' </div>
					<div class="pbwp-actions">
						<a class="pbwp-remove" href="javascript:void(0);"><i class="icon-times"></i></a>
						<a class="pbwp-toggle" href="javascript:void(0);"><i class="icon-sort-down"></i></a>
					</div>
				</div>
				<div class="pbwp-form-holder" style="display:none;">';
            $foodbakery_html .= $foodbakery_markup;
            $foodbakery_html .= '	
				</div>
			</div>';

            return force_balance_tags($foodbakery_html, true);
        }

        /**
         * End function how to create post fields layout in html
         */

        /**
         * Start function how to create post custom fields in input
         */
        public function foodbakery_fields_input_text($params = '') {
            global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_job_cus_fields;
            $foodbakery_output = '';
            $foodbakery_output .= '<script>jQuery(document).ready(function($) {
                                    foodbakery_check_fields_avail();
                            });</script>';
            extract($params);
            $foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
            $foodbakery_label = str_replace(array('[', ']'), array('', ''), $foodbakery_label);
            if (isset($foodbakery_job_cus_fields[$foodbakery_f_counter])) {
                $foodbakery_value = isset($foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label] : '';
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $foodbakery_rand_id = time();
            $html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
            $html_name = 'foodbakery_' . FOODBAKERY_FUNCTIONS()->special_chars($name) . '[]';
            $foodbakery_check_con = '';
            if (isset($check) && $check == true) {
                $html_id = 'check_field_name_' . $foodbakery_rand_id;
            }

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                'name' => $title,
                'hint_text' => $hint,
            ));

            $foodbakery_opt_array = array(
                'id' => $id,
                'cust_id' => $html_id,
                'cust_name' => $html_name,
                'std' => $value,
                'return' => true,
            );

            $foodbakery_output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

            $foodbakery_output .= '<span class="name-checking"></span>';

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                'desc' => '',
            ));
            return force_balance_tags($foodbakery_output);
        }

        /**
         * end function how to create post custom fields in input
         */

        /**
         * Start function how to create post custom fields in input textarea
         */
        public function foodbakery_fields_input_textarea($params = '') {
            global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_job_cus_fields;
            $foodbakery_output = '';
            extract($params);
            $foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
            $foodbakery_label = str_replace(array('[', ']'), array('', ''), $foodbakery_label);
            $foodbakery_output .= '<script>jQuery(document).ready(function($) {
                                    foodbakery_check_fields_avail();
                            });</script>';
            if (isset($foodbakery_job_cus_fields[$foodbakery_f_counter])) {
                $foodbakery_value = isset($foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label] : '';
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
            $html_name = 'foodbakery_' . FOODBAKERY_FUNCTIONS()->special_chars($name) . '[]';

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                'name' => $title,
                'hint_text' => $hint,
            ));

            $foodbakery_opt_array = array(
                'id' => $id,
                'cust_id' => $html_id,
                'cust_name' => $html_name,
                'std' => $value,
                'return' => true,
            );

            $foodbakery_output .= $foodbakery_form_fields->foodbakery_form_textarea_render($foodbakery_opt_array);

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                'desc' => '',
            ));
            return force_balance_tags($foodbakery_output);
        }

        /**
         * end function how to create post custom fields in input
         */

        /**
         * Start function how to create post custom select fields
         */
        public function foodbakery_fields_select($params = '') {
            global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_job_cus_fields;
            $foodbakery_output = '';
            extract($params);
            $foodbakery_output .= '<script>jQuery(document).ready(function($) {
                           	  foodbakery_check_fields_avail();
                           });
						   </script>';

            $foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
            $foodbakery_label = str_replace(array('[', ']'), array('', ''), $foodbakery_label);
            if (isset($foodbakery_job_cus_fields[$foodbakery_f_counter])) {
                $foodbakery_value = isset($foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label] : '';
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
            $html_name = 'foodbakery_' . FOODBAKERY_FUNCTIONS()->special_chars($name) . '[]';
            $html_class = 'chosen-select-no-single';

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                'name' => $title,
                'hint_text' => $hint,
            ));

            $foodbakery_opt_array = array(
                'id' => $id,
                'cust_id' => $html_id,
                'cust_name' => $html_name,
                'std' => $value,
                'classes' => $html_class,
                'options' => $options,
                'return' => true,
            );

            $foodbakery_output .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                'desc' => '',
            ));

            return force_balance_tags($foodbakery_output);
        }

        /**
         * end function how to create post custom select fields
         */

        /**
         * Start function how to create post custom icon fields
         */
        public function foodbakery_fields_fontawsome_icon_jobs($params = '') {
            global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_job_cus_fields;
            $foodbakery_output = '';
            extract($params);
            $foodbakery_output .= '';
            $rand_id = rand(0, 999999);
            $foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
            $foodbakery_label = str_replace(array('[', ']'), array('', ''), $foodbakery_label);
            if (isset($foodbakery_job_cus_fields[$foodbakery_f_counter])) {
                $foodbakery_value = isset($foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_job_cus_fields[$foodbakery_f_counter][$foodbakery_label] : '';
            }
            if (isset($foodbakery_value) && $foodbakery_value != '') {
                $value = $foodbakery_value;
            } else {
                $value = $std;
            }
            $html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
            $html_name = 'foodbakery_' . FOODBAKERY_FUNCTIONS()->special_chars($name) . '[]';
            $html_class = 'chosen-select-no-single';

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                'name' => $title,
                'hint_text' => $hint,
            ));

            $foodbakery_output .= foodbakery_iconlist_plugin_options($value, $id . $foodbakery_f_counter . $rand_id, $name);

            $foodbakery_output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                'desc' => '',
            ));

            return force_balance_tags($foodbakery_output);
        }

        /**
         * end function how to create post custom icon fields
         */

        /**
         * Start function how to save array of fields
         */
        public function foodbakery_save_array($foodbakery_counter = 0, $foodbakery_type = '', $cus_field_array = array()) {
            $foodbakery_fields = array('required', 'label', 'meta_key', 'placeholder', 'enable_srch', 'default_value', 'fontawsome_icon', 'help', 'rows', 'cols', 'multi', 'post_multi', 'first_value', 'collapse_search', 'date_format', 'min', 'max', 'increment', 'enable_inputs', 'srch_style');
            $cus_field_array['type'] = $foodbakery_type;
            foreach ($foodbakery_fields as $field) {
                if (isset($_POST["foodbakery_cus_field_{$foodbakery_type}"][$field][$foodbakery_counter])) {
                    $cus_field_array[$field] = $_POST["foodbakery_cus_field_{$foodbakery_type}"][$field][$foodbakery_counter];
                }
            }
            return $cus_field_array;
        }

        /**
         * end function how to save array of fields
         */

        /**
         * Start function how to update fields
         */
        public function foodbakery_update_custom_fields() {
            $foodbakery_obj = new foodbakery_custom_fields_options();
            $text_counter = $textarea_counter = $dropdown_counter = $date_counter = $email_counter = $url_counter = $range_counter = $cus_field_counter = $error = 0;
            $error_msg = '';
            $cus_field = array();
            if (isset($_POST['foodbakery_cus_field_id']) && sizeof($_POST['foodbakery_cus_field_id']) > 0) {
                foreach ($_POST['foodbakery_cus_field_id'] as $keys => $values) {
                    $foodbakery_rand_numb = rand(1342121, 9974532);
                    if ($values != '') {
                        $cus_field_array = array();
                        $foodbakery_type = isset($_POST["foodbakery_cus_field_title"][$cus_field_counter]) ? $_POST["foodbakery_cus_field_title"][$cus_field_counter] : '';
                        switch ($foodbakery_type) {
                            case('text'):
                                $cus_field_array = $foodbakery_obj->foodbakery_save_array($text_counter, $foodbakery_type, $cus_field_array);
                                $text_counter++;
                                break;
                            case('textarea'):
                                $cus_field_array = $foodbakery_obj->foodbakery_save_array($textarea_counter, $foodbakery_type, $cus_field_array);
                                $textarea_counter++;
                                break;
                            case('dropdown'):
                                $cus_field_array = $foodbakery_obj->foodbakery_save_array($dropdown_counter, $foodbakery_type, $cus_field_array);
                                if (isset($_POST["cus_field_{$foodbakery_type}"]['options_values'][$values]) && (strlen(implode($_POST["cus_field_{$foodbakery_type}"]['options_values'][$values])) != 0)) {
                                    $cus_field_array['options'] = array();
                                    $option_counter = 0;
                                    foreach ($_POST["cus_field_{$foodbakery_type}"]['options_values'][$values] as $option) {
                                        if ($option != '') {
                                            $option = ltrim(rtrim($option));
                                            if ($_POST["cus_field_{$foodbakery_type}"]['options'][$values][$option_counter] != '') {
                                                $cus_field_array['options']['select'][] = isset($_POST["cus_field_{$foodbakery_type}"]['selected'][$values][$option_counter]) ? $_POST["cus_field_{$foodbakery_type}"]['selected'][$values][$option_counter] : '';
                                                $cus_field_array['options']['label'][] = isset($_POST["cus_field_{$foodbakery_type}"]['options'][$values][$option_counter]) ? $_POST["cus_field_{$foodbakery_type}"]['options'][$values][$option_counter] : '';
                                                $cus_field_array['options']['value'][] = isset($option) && $option != '' ? strtolower(str_replace(" ", "-", $option)) : '';
                                            }
                                        }
                                        $option_counter++;
                                    }
                                } else {
                                    $error = 1;
                                    $error_msg .= foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_please_select_atleast_one_option' ) . "'" . $cus_field_array['label'] . "'" . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_field' ) . "'<br/>";
                                }
                                $dropdown_counter++;
                                break;
                            case('date'):
                                $cus_field_array = $foodbakery_obj->foodbakery_save_array($date_counter, $foodbakery_type, $cus_field_array);
                                $date_counter++;
                                break;
                            case('email'):
                                $cus_field_array = $foodbakery_obj->foodbakery_save_array($email_counter, $foodbakery_type, $cus_field_array);
                                $email_counter++;
                                break;
                            case('url'):
                                $cus_field_array = $foodbakery_obj->foodbakery_save_array($url_counter, $foodbakery_type, $cus_field_array);
                                $url_counter++;
                                break;
                            case('range'):
                                $cus_field_array = $foodbakery_obj->foodbakery_save_array($range_counter, $foodbakery_type, $cus_field_array);
                                $range_counter++;
                                break;
                        }
                        $cus_field[$foodbakery_rand_numb] = $cus_field_array;
                        $cus_field_counter++;
                    }
                }
            }

            if ($error == 0) {
                update_option("foodbakery_job_cus_fields", $cus_field);
                $error = 0;
                $error_msg = foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_all_settings_saved' );
            }
            $return_arr = array('error' => $error, 'error_msg' => $error_msg);
            return $return_arr;
        }

        public function foodbakery_check_fields_avail() {
            $foodbakery_job_cus_fields = get_option("foodbakery_job_cus_fields");
            $foodbakery_json = array();
            $foodbakery_temp_names = array();
            $foodbakery_temp_names_1 = array();
            $foodbakery_temp_names_2 = array();
            $foodbakery_temp_names_3 = array();
            $foodbakery_temp_names_4 = array();
            $foodbakery_temp_names_5 = array();
            $foodbakery_temp_names_6 = array();
            $foodbakery_field_name = $_REQUEST['name'];
            $form_field_names = isset($_REQUEST['foodbakery_cus_field_text']['meta_key']) ? $_REQUEST['foodbakery_cus_field_text']['meta_key'] : array();
            $form_field_names_0 = isset($_REQUEST['foodbakery_cus_field_number']['meta_key']) ? $_REQUEST['foodbakery_cus_field_number']['meta_key'] : array();
            $form_field_names_1 = isset($_REQUEST['foodbakery_cus_field_textarea']['meta_key']) ? $_REQUEST['foodbakery_cus_field_textarea']['meta_key'] : array();
            $form_field_names_2 = isset($_REQUEST['foodbakery_cus_field_dropdown']['meta_key']) ? $_REQUEST['foodbakery_cus_field_dropdown']['meta_key'] : array();
            $form_field_names_3 = isset($_REQUEST['foodbakery_cus_field_date']['meta_key']) ? $_REQUEST['foodbakery_cus_field_date']['meta_key'] : array();
            $form_field_names_4 = isset($_REQUEST['foodbakery_cus_field_email']['meta_key']) ? $_REQUEST['foodbakery_cus_field_email']['meta_key'] : array();
            $form_field_names_5 = isset($_REQUEST['foodbakery_cus_field_url']['meta_key']) ? $_REQUEST['foodbakery_cus_field_url']['meta_key'] : array();
            $form_field_names_6 = isset($_REQUEST['foodbakery_cus_field_range']['meta_key']) ? $_REQUEST['foodbakery_cus_field_range']['meta_key'] : array();
            $form_field_names = array_merge($form_field_names,$form_field_names_0, $form_field_names_1, $form_field_names_2, $form_field_names_3, $form_field_names_4, $form_field_names_5, $form_field_names_6);
            $length = count(array_keys($form_field_names, $foodbakery_field_name));
            if ($foodbakery_field_name == '') {
                $foodbakery_json['type'] = 'error';
                $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_field_name_required' );
            } else {
                if (is_array($foodbakery_job_cus_fields) && sizeof($foodbakery_job_cus_fields) > 0) {
                    $success = 1;
                    foreach ($foodbakery_job_cus_fields as $field_key => $foodbakery_field) {
                        if (isset($foodbakery_field['type'])) {
                            if (preg_match('/\s/', $foodbakery_field_name)) {
                                $foodbakery_json['type'] = 'error';
                                $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_whitespaces_not_allowed' );
                                echo json_encode($foodbakery_json);
                                die();
                            }
                            if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $foodbakery_field_name)) {
                                $foodbakery_json['type'] = 'error';
                                $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_special_characters_not_allowed' );
                                echo json_encode($foodbakery_json);
                                die();
                            }
                            if (trim($foodbakery_field['type']) == trim($foodbakery_field_name)) {

                                if (in_array(trim($foodbakery_field_name), $form_field_names) && $length > 1) {
                                    $foodbakery_json['type'] = 'error';
                                    $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_name_already_exist' );
                                    echo json_encode($foodbakery_json);
                                    die();
                                }
                            } else {
                                if (in_array(trim($foodbakery_field_name), $form_field_names) && $length > 1) {
                                    $foodbakery_json['type'] = 'error';
                                    $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_name_already_exist' );
                                    echo json_encode($foodbakery_json);
                                    die();
                                }
                            }
                        }
                    }
                    $foodbakery_json['type'] = 'success';
                    $foodbakery_json['message'] = '<i class="icon-checkmark6"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_name_available' );
                } else {
                    if (preg_match('/\s/', $foodbakery_field_name)) {
                        $foodbakery_json['type'] = 'error';
                        $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_whitespaces_not_allowed' );
                        echo json_encode($foodbakery_json);
                        die();
                    }
                    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+]/', $foodbakery_field_name)) {
                        $foodbakery_json['type'] = 'error';
                        $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_special_characters_not_allowed' );
                        echo json_encode($foodbakery_json);
                        die();
                    }
                    if (in_array(trim($foodbakery_field_name), $form_field_names) && $length > 1) {
                        $foodbakery_json['type'] = 'error';
                        $foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_name_already_exist' );
                    } else {
                        $foodbakery_json['type'] = 'success';
                        $foodbakery_json['message'] = '<i class="icon-checkmark6"></i> ' . foodbakery_plugin_text_srt( 'foodbakery_restaurant_custom_name_available' );
                    }
                }
            }
            echo json_encode($foodbakery_json);
            die();
        }

    }

    $foodbakery_custom_fields_obj = new foodbakery_custom_fields_options();
}