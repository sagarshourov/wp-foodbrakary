<?php

/**
 *  File Type: Settings Class
 */
if (!class_exists('foodbakery_plugin_options')) {

    class foodbakery_plugin_options {

        /**
         * Start Contructer Function
         */
        public function __construct() {
            add_action('wp_ajax_foodbakery_add_extra_feature_to_list', array(&$this, 'foodbakery_add_extra_feature_to_list'));
            add_action('wp_ajax_foodbakery_add_feats_to_list', array(&$this, 'foodbakery_add_feats_to_list'));
            add_action('wp_ajax_foodbakery_add_safetytext_to_list', array(&$this, 'foodbakery_add_safetytext_to_list'));
            add_action('wp_ajax_foodbakery_add_package_to_list', array(&$this, 'foodbakery_add_package_to_list'));
            add_action('wp_ajax_foodbakery_add_cv_pkg_to_list', array(&$this, 'foodbakery_add_cv_pkg_to_list'));
        }

        /**
         * End Contructer Function
         */

        /**
         * Start Function how to register setting in admin submenu page
         */
        public function foodbakery_register_jobunt_settings() {
            //add submenu page
            add_menu_page(esc_html__('Foodbakery Settings', 'foodbakery'), esc_html__('Foodbakery Settings', 'foodbakery'), 'manage_options', 'foodbakery_settings', array(&$this, 'foodbakery_settings'), wp_foodbakery::plugin_url() . 'assets/backend/images/settings.png', 32);
		}

        /**
         * End Function how to register setting in admin submenu page
         */

        /**
         * Start Function how to call setting function
         */
        public function foodbakery_settings() {
            // initialize settings array 
            foodbakery_settings_option();

            foodbakery_settings_options_page();
        }

        /**
         * end Function how to call setting function
         */

        /**
         * Start Function how to create package section
         */
        public function foodbakery_packages_section() {
            global $post, $foodbakery_form_fields, $package_id, $counter_package, $package_title, $package_price, $package_duration, $package_no_ads, $package_description, $foodbakery_package_type, $package_restaurants, $package_cvs, $package_submission_limit, $package_duration_period, $package_featured_ads, $foodbakery_list_dur, $package_feature, $foodbakery_html_fields, $foodbakery_plugin_options;
            $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
            $foodbakery_packages_options = $foodbakery_plugin_options['foodbakery_packages_options'];
            $currency_sign = foodbakery_get_currency_sign();
            $foodbakery_free_package_switch = get_option('foodbakery_free_package_switch');
            $cd_checked = '';
            if (isset($foodbakery_free_package_switch) && $foodbakery_free_package_switch == 'on') {
                $cd_checked = 'checked';
            }
            $foodbakery_opt_array = array(
                'id' => '',
                'std' => '1',
                'cust_id' => "",
                'cust_name' => "dynamic_foodbakery_package",
                'return' => true,
            );


            $foodbakery_html = $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                
                <script>
                    jQuery(document).ready(function($) {
                        jQuery("#total_packages").sortable({
                            cancel : \'td div.table-form-elem\'
                        });
                    });
                </script>';
            $foodbakery_html .= '<div class="form-elements" id="safetysafe_switch_add_package">
					<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
						<a href="javascript:foodbakery_createpop(\'add_package_title\',\'filter\')" class="button button_style">' . esc_html__('Add Membership', 'foodbakery') . '</a>
					</div>
				</div>';
            $foodbakery_html .= '<div class="cs-list-table">
              <table class="to-table" border="0" cellspacing="0">
                <thead>
                  <tr>
                    <th style="width:80%;">' . esc_html__('Title', 'foodbakery') . '</th>
                    <th style="width:80%;" class="centr">' . esc_html__('Actions', 'foodbakery') . '</th>
                    <th style="width:0%;" class="centr"></th>
                  </tr>
                </thead>
                <tbody id="total_packages">';
            if (isset($foodbakery_packages_options) && is_array($foodbakery_packages_options) && count($foodbakery_packages_options) > 0) {
                foreach ($foodbakery_packages_options as $package_key => $package) {
                    if (isset($package_key) && $package_key <> '') {
                        $counter_package = $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                        $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                        $package_price = isset($package['package_price']) ? $package['package_price'] : '';
                        $package_duration = isset($package['package_duration']) ? $package['package_duration'] : '';
                        $package_description = isset($package['package_description']) ? $package['package_description'] : '';
                        $foodbakery_package_type = isset($package['package_type']) ? $package['package_type'] : '';
                        $package_restaurants = isset($package['package_restaurants']) ? $package['package_restaurants'] : '';
                        $package_cvs = isset($package['package_cvs']) ? $package['package_cvs'] : '';
                        $package_submission_limit = isset($package['package_submission_limit']) ? $package['package_submission_limit'] : '';
                        $package_duration_period = isset($package['package_duration_period']) ? $package['package_duration_period'] : '';
                        $foodbakery_list_dur = isset($package['foodbakery_list_dur']) ? $package['foodbakery_list_dur'] : '';
                        $package_feature = isset($package['package_feature']) ? $package['package_feature'] : '';
                        $package_featured_ads = isset($package['package_featured_ads']) ? $package['package_featured_ads'] : '';
                        $foodbakery_html .= $this->foodbakery_add_package_to_list();
                    }
                }
            }
            $foodbakery_html .= '</tbody>
              </table>
              </div>
              </form>
              <div id="add_package_title" style="display: none;">
                <div class="cs-heading-area">
                  <h5> <i class="icon-plus-circle"></i> ' . esc_html__('Membership Settings', 'foodbakery') . ' </h5>
                  <span class="cs-btnclose" onClick="javascript:foodbakery_remove_overlay(\'add_package_title\',\'append\')"> <i class="icon-times"></i></span> </div>';

            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Title', 'foodbakery'),
                'desc' => '',
                'hint_text' => esc_html__("Enter title here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'package_title',
                    'cust_name' => 'package_title',
                    'return' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => esc_html__('Price', 'foodbakery') . FOODBAKERY_FUNCTIONS()->special_chars($currency_sign),
                'desc' => '',
                'hint_text' => esc_html__("Enter price here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'package_price',
                    'cust_name' => 'package_price',
                    'return' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);



            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Type', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'id' => 'package_type',
                    'cust_name' => 'package_type',
                    'options' => array(
                        'single' => esc_html__('Single Submission', 'foodbakery'),
                        'subscription' => esc_html__('Subscription', 'foodbakery'),
                    ),
                    'return' => true,
                    'onclick' => 'foodbakery_package_type_toogle(this.value, \'\')',
                    'classes' => 'chosen-select-no-single'
                ),
            );


            $foodbakery_html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('No of Restaurants in Membership', 'foodbakery'),
                'desc' => '',
                'id' => 'package_restaurants_con',
                'hint_text' => '',
                'extra_atr' => 'style="display:none;"',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'id' => '',
                    'cust_id' => 'package_restaurants',
                    'cust_name' => 'package_restaurants',
                    'return' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


            // hide attribute		
            $foodbakery_opt_array = array(
                'name' => esc_html__('No of CV\'s', 'foodbakery'),
                'desc' => '',
                'id' => '',
                'hint_text' => '',
                'styles' => 'display:none',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'id' => '',
                    'cust_id' => 'package_cvs',
                    'cust_name' => 'package_cvs',
                    'return' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Expiry' , 'foodbakery'),
                'id' => '',
                'desc' => '',
                'fields_list' => array(
                    array('type' => 'text', 'field_params' => array(
                            'std' => '',
                            'id' => '',
                            'cust_id' => 'package_duration',
                            'cust_name' => 'package_duration',
                            'cust_type' => '',
                            'classes' => 'input-large',
                            'return' => true,
                        ),
                    ),
                    array('type' => 'select', 'field_params' => array(
                            'std' => '',
                            'id' => '',
                            'cust_type' => '',
                            'cust_id' => 'package_duration_period',
                            'cust_name' => 'package_duration_period',
                            'classes' => 'chosen-select-no-single',
                            'div_classes' => 'select-small',
                            'return' => true,
                            'options' => array(
                                'days' => esc_html__('Days', 'foodbakery'),
                                'months' => esc_html__('Months', 'foodbakery'),
                                'years' => esc_html__('Years', 'foodbakery'),
                            ),
                        ),
                    ),
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);
            $foodbakery_opt_array = array(
                'name' => esc_html('Restaurants Expiry', 'foodbakery'),
                'id' => '',
                'desc' => '',
                'fields_list' => array(
                    array('type' => 'text', 'field_params' => array(
                            'std' => '',
                            'id' => '',
                            'cust_id' => 'package_submission_limit',
                            'cust_name' => 'package_submission_limit',
                            'cust_type' => '',
                            'classes' => 'input-large',
                            'return' => true,
                        ),
                    ),
                    array('type' => 'select', 'field_params' => array(
                            'std' => '',
                            'id' => '',
                            'cust_type' => '',
                            'cust_id' => 'foodbakery_list_dur',
                            'cust_name' => 'foodbakery_list_dur',
                            'classes' => 'chosen-select-no-single',
                            'return' => true,
                            'div_classes' => 'select-small',
                            'options' => array(
                                'days' => esc_html__('Days', 'foodbakery'),
                                'months' => esc_html__('Months', 'foodbakery'),
                                'years' => esc_html__('Years', 'foodbakery'),
                            ),
                        ),
                    ),
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);
            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Featured', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'package_feature',
                    'cust_name' => 'package_feature',
                    'options' => array(
                        'no' => esc_html__('No', 'foodbakery'),
                        'yes' => esc_html__('Yes', 'foodbakery'),
                    ),
                    'return' => true,
                    'classes' => 'chosen-select-no-single'
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
            $foodbakery_opt_array = array(
                'name' => esc_html__('Description', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'package_description',
                    'cust_name' => 'package_description',
                    'return' => true,
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Add Membership to List', 'foodbakery'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'return' => true,
                    'after' => '<div class="package-loader"></div>',
                    'cust_type' => 'button',
                    'extra_atr' => 'onClick="add_package_to_list(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_foodbakery::plugin_url()) . '\')" ',
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
            $foodbakery_html .= '</div>';
            return $foodbakery_html;
        }

        /**
         * end Function how to create package section
         */

        /**
         * Start Function how to add package in list section
         */
        public function foodbakery_add_package_to_list() {
            global $counter_package, $foodbakery_form_fields, $package_id, $package_title, $package_price, $package_duration, $package_description, $foodbakery_package_type, $package_restaurants, $package_cvs, $package_submission_limit, $foodbakery_list_dur, $package_duration_period, $package_featured_ads, $package_feature, $foodbakery_html_fields, $foodbakery_plugin_options;
            foreach ($_POST as $keys => $values) {
                $$keys = $values;
            }
            if (isset($_POST['package_title']) && $_POST['package_title'] <> '') {
                $package_id = time();
            }
            if (empty($package_id)) {
                $package_id = $counter_package;
            }
            $currency_sign = foodbakery_get_currency_sign();

            $foodbakery_opt_array = array(
                'id' => '',
                'std' => absint($package_id),
                'cust_id' => "",
                'cust_name' => "package_id_array[]",
                'return' => true,
            );
            $foodbakery_html = '
            <tr class="parentdelete" id="edit_track' . esc_attr($counter_package) . '">
              <td id="subject-title' . esc_attr($counter_package) . '" style="width:100%;">' . esc_attr($package_title) . '</td>
              <td class="centr" style="width:20%;"><a href="javascript:foodbakery_createpop(\'edit_track_form' . esc_js($counter_package) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a href="#" class="delete-it btndeleteit actions delete">&nbsp;</a></td>
              <td style="width:0"><div id="edit_track_form' . esc_attr($counter_package) . '" style="display: none;" class="table-form-elem">
                  ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                  <div class="cs-heading-area">
                    <h5 style="text-align: left;"> ' . esc_html__('Membership Settings', 'foodbakery') . '</h5>
                    <span onclick="javascript:foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_package) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>
                    <div class="clear"></div>
                  </div>';
            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Title', 'foodbakery'),
                'desc' => '',
                'hint_text' => esc_html__("Enter title here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => htmlspecialchars($package_title),
                    'cust_id' => 'package_title' . esc_attr($counter_package),
                    'cust_name' => 'package_title_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => esc_html__('Price edit', 'foodbakery') . FOODBAKERY_FUNCTIONS()->special_chars($currency_sign),
                'desc' => '',
                'hint_text' => esc_html__("Enter price here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($package_price),
                    'cust_id' => 'package_price' . esc_attr($counter_package),
                    'cust_name' => 'package_price_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);



            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Type', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $foodbakery_package_type,
                    'id' => 'foodbakery_package_type' . esc_attr($counter_package),
                    'cust_name' => 'package_type_array[]',
                    'options' => array(
                        'single' => esc_html__('Single Submission', 'foodbakery'),
                        'subscription' => esc_html__('Subscription', 'foodbakery'),
                    ),
                    'return' => true,
                    'onclick' => 'foodbakery_package_type_toogle(this.value, \'' . esc_attr($counter_package) . '\')',
                    'classes' => 'chosen-select-no-single',
                    'array' => true,
                    'force_std' => true,
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);



            $foodbakery_opt_array = array(
                'name' => esc_html__('No of Restaurants in Membership', 'foodbakery'),
                'desc' => '',
                'id' => 'package_restaurants_con' . esc_attr($counter_package),
                'hint_text' => '',
                'extra_atr' => 'style="display:' . esc_attr($foodbakery_package_type == 'subscription' ? 'block' : 'none') . '"',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($package_restaurants),
                    'id' => '',
                    'cust_id' => 'package_restaurants' . esc_attr($counter_package),
                    'cust_name' => 'package_restaurants_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true,
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('No of CV\'s', 'foodbakery'),
                'desc' => '',
                'id' => '',
                'hint_text' => '',
                'styles' => 'display:none',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($package_cvs),
                    'id' => '',
                    'cust_id' => 'package_cvs' . esc_attr($counter_package),
                    'cust_name' => 'package_cvs_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Expiry', 'foodbakery'),
                'id' => '',
                'desc' => '',
                'fields_list' => array(
                    array('type' => 'text', 'field_params' => array(
                            'std' => esc_attr($package_duration),
                            'id' => '',
                            'cust_id' => 'package_duration' . esc_attr($counter_package),
                            'cust_name' => 'package_duration_array[]',
                            'cust_type' => '',
                            'classes' => 'input-large',
                            'return' => true,
                            'array' => true,
                            'force_std' => true,
                        ),
                    ),
                    array('type' => 'select', 'field_params' => array(
                            'std' => esc_attr($package_duration_period),
                            'id' => '',
                            'cust_type' => '',
                            'cust_id' => 'package_duration_period' . esc_attr($counter_package),
                            'cust_name' => 'package_duration_period_array[]',
                            'classes' => 'chosen-select-no-single',
                            'div_classes' => 'select-small',
                            'options' => array(
                                'days' => esc_html__('Days', 'foodbakery'),
                                'months' => esc_html__('Months', 'foodbakery'),
                                'years' => esc_html__('Years', 'foodbakery'),
                            ),
                            'return' => true,
                            'array' => true,
                            'force_std' => true,
                        ),
                    ),
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Restaurants Expiry', 'foodbakery'),
                'id' => '',
                'desc' => '',
                'fields_list' => array(
                    array('type' => 'text', 'field_params' => array(
                            'std' => esc_attr($package_submission_limit),
                            'id' => '',
                            'cust_id' => 'package_submission_limit' . esc_attr($counter_package),
                            'cust_name' => 'package_submission_limit_array[]',
                            'cust_type' => '',
                            'classes' => 'input-large',
                            'return' => true,
                            'array' => true,
                            'force_std' => true,
                        ),
                    ),
                    array('type' => 'select', 'field_params' => array(
                            'std' => esc_attr($foodbakery_list_dur),
                            'id' => '',
                            'cust_type' => '',
                            'cust_id' => 'foodbakery_list_dur' . esc_attr($counter_package),
                            'cust_name' => 'foodbakery_list_dur_array[]',
                            'classes' => 'chosen-select-no-single',
                            'div_classes' => 'select-small',
                            'options' => array(
                                'days' => esc_html__('Days', 'foodbakery'),
                                'months' => esc_html__('Months', 'foodbakery'),
                                'years' => esc_html__('Years', 'foodbakery'),
                            ),
                            'return' => true,
                            'array' => true,
                            'force_std' => true,
                        ),
                    ),
                ),
            );


            $foodbakery_html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Featured', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $package_feature,
                    'cust_id' => 'package_feature' . esc_attr($counter_package),
                    'cust_name' => 'package_feature_array[]',
                    'options' => array(
                        'no' => esc_html__('No', 'foodbakery'),
                        'yes' => esc_html__('Yes', 'foodbakery'),
                    ),
                    'classes' => 'chosen-select-no-single',
                    'return' => true,
                    'array' => true,
                    'force_std' => true,
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);



            $foodbakery_opt_array = array(
                'name' => esc_html__('Description', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($package_description),
                    'cust_id' => 'package_description' . esc_attr($counter_package),
                    'cust_name' => 'package_description_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true,
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Update Membership', 'foodbakery'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'return' => true,
                    'cust_type' => 'button',
                    'extra_atr' => 'onclick="update_title(' . esc_js($counter_package) . '); foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_package) . '\',\'append\')"',
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
            $foodbakery_html .= '
                </div></td>
            </tr>';
            if (isset($_POST['package_title'])) {
                echo force_balance_tags($foodbakery_html);
                die();
            } else {
                return $foodbakery_html;
            }
        }

        /**
         * end Function how to add package in list section
         */

        /**
         * Start Function how to create cv package section
         */
        public function foodbakery_cv_pkgs_section() {
            global $post, $cv_pkg_id, $foodbakery_form_fields, $foodbakery_html_fields, $counter_cv_pkg, $cv_pkg_title, $cv_pkg_price, $cv_pkg_dur, $cv_pkg_desc, $cv_pkg_cvs, $cv_pkg_dur_period, $foodbakery_plugin_options;
            $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
            $foodbakery_cv_pkgs_options = isset($foodbakery_plugin_options['foodbakery_cv_pkgs_options']) ? $foodbakery_plugin_options['foodbakery_cv_pkgs_options'] : '';
            $currency_sign = foodbakery_get_currency_sign();
            $foodbakery_opt_array = array(
                'id' => '',
                'std' => "1",
                'cust_id' => "",
                'cust_name' => "dynamic_foodbakery_cv_pkg",
                'return' => true,
            );
            $foodbakery_html = $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                <script>
                jQuery(document).ready(function($) {
                    jQuery("#total_cv_pkgs").sortable({
                        cancel : \'td div.table-form-elem\'
                    });
                });
                </script>';
            $foodbakery_html .= '<div class="form-elements" id="safetysafe_switch_add_package_cv">
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					<a href="javascript:foodbakery_createpop(\'add_cv_pkg_title\',\'filter\')" class="button button_style">' . esc_html__('Add Membership', 'foodbakery') . '</a>
				</div>
			</div>';

            $foodbakery_html .= '<div class="cs-list-table">
              <table class="to-table" border="0" cellspacing="0">
                <thead>
                  <tr>
                    <th style="width:80%;">' . esc_html__('Title', 'foodbakery') . '</th>
                    <th style="width:80%;" class="centr">' . esc_html__('Actions', 'foodbakery') . '</th>
                    <th style="width:0%;" class="centr"></th>
                  </tr>
                </thead>
                <tbody id="total_cv_pkgs">';
            if (isset($foodbakery_cv_pkgs_options) && is_array($foodbakery_cv_pkgs_options) && count($foodbakery_cv_pkgs_options) > 0) {
                foreach ($foodbakery_cv_pkgs_options as $cv_pkg_key => $cv_pkg) {
                    if (isset($cv_pkg_key) && $cv_pkg_key <> '') {
                        $counter_cv_pkg = $cv_pkg_id = isset($cv_pkg['cv_pkg_id']) ? $cv_pkg['cv_pkg_id'] : '';
                        $cv_pkg_title = isset($cv_pkg['cv_pkg_title']) ? $cv_pkg['cv_pkg_title'] : '';
                        $cv_pkg_price = isset($cv_pkg['cv_pkg_price']) ? $cv_pkg['cv_pkg_price'] : '';
                        $cv_pkg_desc = isset($cv_pkg['cv_pkg_desc']) ? $cv_pkg['cv_pkg_desc'] : '';
                        $cv_pkg_cvs = isset($cv_pkg['cv_pkg_cvs']) ? $cv_pkg['cv_pkg_cvs'] : '';
                        $cv_pkg_dur = isset($cv_pkg['cv_pkg_dur']) ? $cv_pkg['cv_pkg_dur'] : '';
                        $cv_pkg_dur_period = isset($cv_pkg['cv_pkg_dur_period']) ? $cv_pkg['cv_pkg_dur_period'] : '';
                        $foodbakery_html .= $this->foodbakery_add_cv_pkg_to_list();
                    }
                }
            }
            $foodbakery_html .= '
                </tbody>
              </table>
              </div>
              </form>
              <div id="add_cv_pkg_title" style="display: none;">
                <div class="cs-heading-area">
                  <h5> <i class="icon-plus-circle"></i> ' . esc_html__('Membership Settings', 'foodbakery') . ' </h5>
                  <span class="cs-btnclose" onClick="javascript:foodbakery_remove_overlay(\'add_cv_pkg_title\',\'append\')"> <i class="icon-times"></i></span> </div>';


            $foodbakery_opt_array = array(
                'name' => esc_html__('Title', 'foodbakery'),
                'desc' => '',
                'hint_text' => esc_html__("Enter Title here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'cv_pkg_title',
                    'cust_name' => 'cv_pkg_title',
                    'return' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);




            $foodbakery_opt_array = array(
                'name' => esc_html__('Price', 'foodbakery') . FOODBAKERY_FUNCTIONS()->special_chars($currency_sign),
                'desc' => '',
                'hint_text' => esc_html__("Enter Price here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'cv_pkg_price',
                    'cust_name' => 'cv_pkg_price',
                    'return' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => esc_html__('No of CV\'s', 'foodbakery'),
                'desc' => '',
                'id' => 'cv_pkg_restaurants_con',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'cv_pkg_cvs',
                    'cust_name' => 'cv_pkg_cvs',
                    'return' => true,
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Expiry' , 'foodbakery'),
                'id' => '',
                'desc' => '',
                'fields_list' => array(
                    array('type' => 'text', 'field_params' => array(
                            'std' => '',
                            'id' => '',
                            'cust_id' => 'cv_pkg_dur',
                            'cust_name' => 'cv_pkg_dur',
                            'cust_type' => '',
                            'classes' => 'input-large',
                            'return' => true,
                        ),
                    ),
                    array('type' => 'select', 'field_params' => array(
                            'std' => '',
                            'id' => 'map_search_btn',
                            'cust_type' => '',
                            'cust_id' => 'cv_pkg_dur_period',
                            'cust_name' => 'cv_pkg_dur_period',
                            'classes' => 'chosen-select-no-single',
                            'div_classes' => 'select-small',
                            'return' => true,
                            'options' => array(
                                'days' => esc_html__('Days', 'foodbakery'),
                                'months' => esc_html__('Months', 'foodbakery'),
                                'years' => esc_html__('Years', 'foodbakery'),
                            ),
                        ),
                    ),
                ),
            );


            $foodbakery_html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Description', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'cust_id' => 'cv_pkg_desc',
                    'cust_name' => 'cv_pkg_desc',
                    'return' => true,
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Add Membership to List', 'foodbakery'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'return' => true,
                    'cust_type' => 'button',
                    'after' => '<div class="cv_pkg-loader"></div>',
                    'extra_atr' => 'onClick="add_cv_pkg_to_list(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_foodbakery::plugin_url()) . '\')" ',
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $foodbakery_html .= '
              </div>';
            return $foodbakery_html;
        }

        /**
         * end Function how to create cv package section
         */

        /**
         * Start Function how to add data in package section
         */
        public function foodbakery_add_cv_pkg_to_list() {
            global $post, $cv_pkg_id, $foodbakery_form_fields, $counter_cv_pkg, $foodbakery_html_fields, $cv_pkg_title, $cv_pkg_price, $cv_pkg_dur, $cv_pkg_desc, $cv_pkg_cvs, $cv_pkg_dur_period, $foodbakery_plugin_options;
            foreach ($_POST as $keys => $values) {
                $$keys = $values;
            }
            if (isset($_POST['cv_pkg_title']) && $_POST['cv_pkg_title'] <> '') {
                $cv_pkg_id = time();
            }
            if (empty($cv_pkg_id)) {
                $cv_pkg_id = $counter_cv_pkg;
            }
            $currency_sign = foodbakery_get_currency_sign();
            $foodbakery_opt_array = array(
                'id' => '',
                'std' => absint($cv_pkg_id),
                'cust_id' => '',
                'cust_name' => "cv_pkg_id_array[]",
                'return' => true,
            );

            $foodbakery_html = '
            <tr class="parentdelete" id="edit_track' . esc_attr($counter_cv_pkg) . '">
              <td id="subject-title' . esc_attr($counter_cv_pkg) . '" style="width:100%;">' . esc_attr($cv_pkg_title) . '</td>
              <td class="centr" style="width:20%;"><a href="javascript:foodbakery_createpop(\'edit_track_form' . esc_js($counter_cv_pkg) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a href="#" class="delete-it btndeleteit actions delete">&nbsp;</a></td>
              <td style="width:0"><div id="edit_track_form' . esc_attr($counter_cv_pkg) . '" style="display: none;" class="table-form-elem">
                  ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                  <div class="cs-heading-area">
                    <h5 style="text-align: left;"> ' . esc_html__('Membership Settings', 'foodbakery') . '</h5>
                    <span onclick="javascript:foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_cv_pkg) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>
                    <div class="clear"></div>
                  </div>';


            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Title', 'foodbakery'),
                'desc' => '',
                'hint_text' => esc_html__("Enter Membership Title here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => htmlspecialchars($cv_pkg_title),
                    'cust_id' => 'cv_pkg_title' . esc_attr($counter_cv_pkg),
                    'cust_name' => 'cv_pkg_title_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


            $foodbakery_opt_array = array(
                'name' => esc_html__('Price', 'foodbakery') . FOODBAKERY_FUNCTIONS()->special_chars($currency_sign),
                'desc' => '',
                'hint_text' => esc_html__("Enter Price here.", "foodbakery"),
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($cv_pkg_price),
                    'cust_id' => 'cv_pkg_price' . esc_attr($counter_cv_pkg),
                    'cust_name' => 'cv_pkg_price_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);



            $foodbakery_opt_array = array(
                'name' => esc_html__('No of CV\'s', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($cv_pkg_cvs),
                    'cust_id' => 'cv_pkg_cvs' . esc_attr($counter_cv_pkg),
                    'cust_name' => 'cv_pkg_cvs_array[]',
                    'return' => true,
                    'array' => true,
                    'force_std' => true
                ),
            );

            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);



            $foodbakery_opt_array = array(
                'name' => esc_html__('Membership Expiry' , 'foodbakery'),
                'id' => '',
                'desc' => '',
                'fields_list' => array(
                    array('type' => 'text', 'field_params' => array(
                            'std' => esc_attr($cv_pkg_dur),
                            'id' => '',
                            'cust_id' => 'cv_pkg_dur' . esc_attr($counter_cv_pkg),
                            'cust_name' => 'cv_pkg_dur_array[]',
                            'cust_type' => '',
                            'classes' => 'input-large',
                            'return' => true,
                            'array' => true,
                            'force_std' => true
                        ),
                    ),
                    array('type' => 'select', 'field_params' => array(
                            'std' => $cv_pkg_dur_period,
                            'id' => '',
                            'cust_type' => '',
                            'cust_id' => 'cv_pkg_dur_period' . esc_attr($counter_cv_pkg),
                            'cust_name' => 'cv_pkg_dur_period_array[]',
                            'classes' => 'chosen-select-no-single',
                            'return' => true,
                            'div_classes' => 'select-small',
                            'options' => array(
                                'days' => esc_html__('Days', 'foodbakery'),
                                'months' => esc_html__('Months', 'foodbakery'),
                                'years' => esc_html__('Years', 'foodbakery'),
                            ),
                            'return' => true,
                            'array' => true,
                            'force_std' => true
                        ),
                    ),
                ),
            );


            $foodbakery_html .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Description', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($cv_pkg_desc),
                    'cust_id' => 'cv_pkg_desc' . esc_attr($counter_cv_pkg),
                    'cust_name' => 'cv_pkg_desc_array[]',
                    'return' => true,
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Update Membership', 'foodbakery'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'return' => true,
                    'cust_type' => 'button',
                    'extra_atr' => 'onclick="update_title(' . esc_js($counter_cv_pkg) . '); foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_cv_pkg) . '\',\'append\')"',
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
            $foodbakery_html .= ' 
                </div></td>
            </tr>';
            if (isset($_POST['cv_pkg_title'])) {
                echo force_balance_tags($foodbakery_html);
                die();
            } else {
                return $foodbakery_html;
            }
        }

        /**
         * end Function how to add cata in package section
         */

        /**
         * Start Function how to show extra features in feature list
         */
        public function foodbakery_add_extra_feature_to_list() {
            global $counter_extra_feature, $foodbakery_form_fields, $extra_feature_id, $extra_feature_title, $extra_feature_price, $extra_feature_type, $extra_feature_guests, $extra_feature_fchange, $extra_feature_desc, $foodbakery_form_fields;
            foreach ($_POST as $keys => $values) {
                $$keys = $values;
            }
            $foodbakery_plugin_options = get_option("foodbakery_plugin_options");
            $currency_sign = foodbakery_get_currency_sign();
            $foodbakery_extra_features_options = $foodbakery_plugin_options['foodbakery_extra_features_options'];
            if (isset($_POST['foodbakery_extra_feature_title']) && $_POST['foodbakery_extra_feature_title'] <> '') {
                $extra_feature_id = time();
                $extra_feature_title = $_POST['foodbakery_extra_feature_title'];
            }
            if (isset($_POST['foodbakery_extra_feature_price']) && $_POST['foodbakery_extra_feature_price'] <> '') {
                $extra_feature_price = $_POST['foodbakery_extra_feature_price'];
            }
            if (isset($_POST['foodbakery_extra_feature_type']) && $_POST['foodbakery_extra_feature_type'] <> '') {
                $extra_feature_type = $_POST['foodbakery_extra_feature_type'];
            }
            if (isset($_POST['foodbakery_extra_feature_guests']) && $_POST['foodbakery_extra_feature_guests'] <> '') {
                $extra_feature_guests = $_POST['foodbakery_extra_feature_guests'];
            }
            if (isset($_POST['foodbakery_extra_feature_fchange']) && $_POST['foodbakery_extra_feature_fchange'] <> '') {
                $extra_feature_fchange = $_POST['foodbakery_extra_feature_fchange'];
            }
            if (isset($_POST['foodbakery_extra_feature_desc']) && $_POST['foodbakery_extra_feature_desc'] <> '') {
                $extra_feature_desc = $_POST['foodbakery_extra_feature_desc'];
            }
            if (empty($extra_feature_id)) {
                $extra_feature_id = $counter_extra_feature;
            }
            if (isset($_POST['foodbakery_extra_feature_title']) && is_array($foodbakery_extra_features_options) && ($this->foodbakery_in_array_field($extra_feature_title, 'foodbakery_extra_feature_title', $foodbakery_extra_features_options))) {
                $foodbakery_error_message = sprintf(esc_html__('This feature "%s" is already exist. Please create with another Title', 'foodbakery'), $extra_feature_title);
                $html = '
                <tr class="parentdelete" id="edit_track' . esc_attr($counter_extra_feature) . '">
					<td style="width:100%;">' . $foodbakery_error_message . '</td>
                </tr>';
                echo force_balance_tags($html);
                die();
            } else {
                $extra_feature_price = isset($extra_feature_price) ? esc_attr($extra_feature_price) : '';
                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => absint($extra_feature_id),
                    'cust_id' => "",
                    'cust_name' => "extra_feature_id_array[]",
                    'return' => true,
                );
                $html = '
                <tr class="parentdelete" id="edit_track' . esc_attr($counter_extra_feature) . '">
                  <td id="subject-title' . esc_attr($counter_extra_feature) . '" style="width:80%;">' . esc_attr($extra_feature_title) . '</td>
                  <td class="centr" style="width:20%;"><a href="javascript:foodbakery_createpop(\'edit_track_form' . esc_js($counter_extra_feature) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a href="#" class="delete-it btndeleteit actions delete">&nbsp;</a></td>
                  <td style="width:0"><div id="edit_track_form' . esc_attr($counter_extra_feature) . '" style="display: none;" class="table-form-elem">
                      ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                      <div class="cs-heading-area">
                        <h5 style="text-align: left;">' . esc_html__('Extra Feature Settings', 'foodbakery') . '</h5>
                        <span onclick="javascript:foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_extra_feature) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>
                        <div class="clear"></div>
                      </div>';
                $html .= $foodbakery_form_fields->foodbakery_form_text_render(
                        array('name' => esc_html__('Extra Feature Title', 'foodbakery'),
                            'id' => 'extra_feature_title',
                            'classes' => '',
                            'std' => $extra_feature_title,
                            'description' => '',
                            'return' => true,
                            'array' => true,
                            'hint' => ''
                        )
                );
                $html .= $foodbakery_form_fields->foodbakery_form_text_render(
                        array('name' => esc_html__('Price', 'foodbakery'),
                            'id' => 'extra_feature_price',
                            'classes' => '',
                            'std' => $extra_feature_price,
                            'description' => '',
                            'return' => true,
                            'array' => true,
                            'hint' => ''
                        )
                );
                $html .= $foodbakery_form_fields->foodbakery_form_select_render(
                        array('name' => esc_html__('Type', 'foodbakery'),
                            'id' => 'extra_feature_type',
                            'classes' => '',
                            'std' => $extra_feature_type,
                            'description' => '',
                            'return' => true,
                            'array' => true,
                            'hint' => '',
                            'options' => array('none' => esc_html__('None', 'foodbakery'), 'one-time' => esc_html__('One Time', 'foodbakery'), 'daily' => esc_html__('Daily', 'foodbakery')),
                        )
                );
                $html .= $foodbakery_form_fields->foodbakery_form_select_render(
                        array('name' => esc_html__('Guests', 'foodbakery'),
                            'id' => 'extra_feature_guests',
                            'classes' => '',
                            'std' => $extra_feature_guests,
                            'description' => '',
                            'return' => true,
                            'array' => true,
                            'hint' => '',
                            'options' => array('none' => esc_html__('None', 'foodbakery'), 'per-head' => esc_html__('Per Head', 'foodbakery'), 'group' => esc_html__('Group', 'foodbakery')),
                        )
                );
                $html .= $foodbakery_form_fields->foodbakery_form_checkbox_render(
                        array('name' => esc_html__('Frontend Changeable', 'foodbakery'),
                            'id' => 'extra_feature_fchange',
                            'classes' => '',
                            'std' => $extra_feature_fchange,
                            'description' => '',
                            'return' => true,
                            'array' => true,
                            'hint' => '',
                        )
                );
                $html .= $foodbakery_form_fields->foodbakery_form_textarea_render(
                        array('name' => esc_html__('Description', 'foodbakery'),
                            'id' => 'extra_feature_desc',
                            'classes' => '',
                            'std' => $extra_feature_desc,
                            'description' => '',
                            'return' => true,
                            'array' => true,
                            'hint' => '',
                        )
                );

                $foodbakery_opt_array = array(
                    'name' => '',
                    'desc' => '',
                    'hint_text' => '',
                    'echo' => false,
                    'field_params' => array(
                        'std' => esc_html__('Update Extra Feature', 'foodbakery'),
                        'cust_id' => '',
                        'cust_name' => '',
                        'return' => true,
                        'cust_type' => 'button',
                        'extra_atr' => 'onclick="foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_extra_feature) . '\',\'append\')" ',
                    ),
                );
                $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                $html .= '
                    </div></td>
                </tr>';
                if (isset($_POST['foodbakery_extra_feature_title']) && isset($_POST['foodbakery_extra_feature_price'])) {
                    echo force_balance_tags($html);
                } else {
                    return $html;
                }
            }
            if (isset($_POST['foodbakery_extra_feature_title']) && isset($_POST['foodbakery_extra_feature_price']))
                die();
        }

        /**
         * Start Function how to show extra features in feature list
         */

        /**
         * Start Function how to add data in  feature list
         */
        public function foodbakery_add_feats_to_list() {
            global $counter_feats, $feats_id, $feats_title, $feats_image, $feats_desc, $foodbakery_form_fields, $foodbakery_form_fields;
            foreach ($_POST as $keys => $values) {
                $$keys = $values;
            }
            $foodbakery_plugin_options = get_option("foodbakery_plugin_options");
            $currency_sign = foodbakery_get_currency_sign();
            if (isset($_POST['foodbakery_feats_title']) && $_POST['foodbakery_feats_title'] <> '') {
                $feats_id = time();
                $feats_title = $_POST['foodbakery_feats_title'];
            }
            if (isset($_POST['foodbakery_feats_image']) && $_POST['foodbakery_feats_image'] <> '') {
                $feats_image = $_POST['foodbakery_feats_image'];
            }

            if (isset($_POST['foodbakery_feats_desc']) && $_POST['foodbakery_feats_desc'] <> '') {
                $feats_desc = $_POST['foodbakery_feats_desc'];
            }
            if (empty($feats_id)) {
                $feats_id = $counter_feats;
            }
            $feats_desc = isset($feats_desc) ? esc_attr($feats_desc) : '';
            $foodbakery_opt_array = array(
                'id' => '',
                'std' => absint($feats_id),
                'cust_id' => '',
                'cust_name' => "feats_id_array[]",
                'return' => true,
            );
            $html = '
                <tr class="parentdelete" id="edit_track' . esc_attr($counter_feats) . '">
                  <td id="subject-title' . esc_attr($counter_feats) . '" style="width:80%;">' . esc_attr($feats_title) . '</td>
                  <td class="centr" style="width:20%;"><a href="javascript:foodbakery_createpop(\'edit_track_form' . esc_js($counter_feats) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a href="#" class="delete-it btndeleteit actions delete">&nbsp;</a></td>
                  <td style="width:0"><div id="edit_track_form' . esc_attr($counter_feats) . '" style="display: none;" class="table-form-elem">
                      ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                      <div class="cs-heading-area">
                        <h5 style="text-align: left;">' . esc_html__('Feature Settings', 'foodbakery') . '</h5>
                        <span onclick="javascript:foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_feats) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>
                        <div class="clear"></div>
                      </div>';
            $html .= $foodbakery_form_fields->foodbakery_form_text_render(
                    array('name' => esc_html__('Feature Title', 'foodbakery'),
                        'id' => 'feats_title',
                        'classes' => '',
                        'std' => $feats_title,
                        'description' => '',
                        'return' => true,
                        'array' => true,
                        'hint' => ''
                    )
            );
            $html .= $foodbakery_form_fields->foodbakery_form_fileupload_render(
                    array('name' => esc_html__('Image', 'foodbakery'),
                        'id' => 'feats_image',
                        'classes' => '',
                        'std' => $feats_image,
                        'description' => '',
                        'return' => true,
                        'array' => true,
                        'hint' => ''
                    )
            );
            $html .= $foodbakery_form_fields->foodbakery_form_textarea_render(
                    array('name' => esc_html__('Description', 'foodbakery'),
                        'id' => 'feats_desc',
                        'classes' => '',
                        'std' => $feats_desc,
                        'description' => '',
                        'return' => true,
                        'array' => true,
                        'hint' => ''
                    )
            );

            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Update Feature', 'foodbakery'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'return' => true,
                    'cust_type' => 'button',
                    'extra_atr' => ' onclick="foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_feats) . '\',\'append\')" ',
                ),
            );
            $foodbakery_html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $html .= '</div></td></tr>';
            if (isset($_POST['foodbakery_feats_title']) && isset($_POST['foodbakery_feats_desc'])) {
                echo force_balance_tags($html);
            } else {
                return $html;
            }
            if (isset($_POST['foodbakery_feats_title']) && isset($_POST['foodbakery_feats_desc']))
                die();
        }

        /**
         * end Function how to add data in  feature list
         */

        /**
         * Start Function how create safetytext data section
         */
        public function foodbakery_safetytext_section() {
            global $post, $safety_id, $counter_safety, $foodbakery_safety_title, $foodbakery_safety_desc, $foodbakery_plugin_options, $foodbakery_form_fields, $foodbakery_form_fields, $foodbakery_html_fields;
            $foodbakery_plugin_options = get_option("foodbakery_plugin_options");
            $foodbakery_safetytext_options = isset($foodbakery_plugin_options['foodbakery_safetytext_options']) ? $foodbakery_plugin_options['foodbakery_safetytext_options'] : '';
            $foodbakery_opt_array = array(
                'id' => '',
                'std' => '1',
                'cust_id' => '',
                'cust_name' => "dynamic_safety_text",
                'return' => true,
            );

            $html = '
            <!--<form name="dir-safety" method="post" action="#">-->
            ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                <script>
                jQuery(document).ready(function($) {
                    $("#total_safety").sortable({
                                                cancel : \'td div.table-form-elem\'
                    });
                    });
                </script>';
            $html .= '<div class="form-elements" id="safetysafe_switch_add">
                	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    	<a href="javascript:foodbakery_createpop(\'add_safety_title\',\'filter\')" class="button button_style">' . esc_html__("Add Safety Text", "foodbakery") . '</a>
                    </div>
                </div>';
            $display = '';
            if (isset($foodbakery_safetytext_options) && is_array($foodbakery_safetytext_options) && count($foodbakery_safetytext_options) > 0) {
                $display = 'block';
            }else {
                $display = 'none';
            }
            $html .= '<div class="cs-list-table" style="display:' . $display . '">
              <table class="to-table" border="0" cellspacing="0">
                <thead>
                  <tr>
                    <th style="width:80%;">' . esc_html__("Title", "foodbakery") . '</th>
                    <th style="width:80%;" class="centr">' . esc_html__("Actions", "foodbakery") . '</th>
                    <th style="width:0%;" class="centr"></th>
                  </tr>
                </thead>
                <tbody id="total_safety">';
            if (isset($foodbakery_safetytext_options) && is_array($foodbakery_safetytext_options) && count($foodbakery_safetytext_options) > 0) {
                foreach ($foodbakery_safetytext_options as $safetytext_key => $safetytext) {
                    if (isset($safetytext_key) && $safetytext_key <> '') {
                        $counter_safety = $safety_id = isset($safetytext['safety_id']) ? $safetytext['safety_id'] : '';
                        $foodbakery_safety_title = isset($safetytext['foodbakery_safety_title']) ? $safetytext['foodbakery_safety_title'] : '';
                        $foodbakery_safety_desc = isset($safetytext['foodbakery_safety_desc']) ? $safetytext['foodbakery_safety_desc'] : '';

                        $html .= $this->foodbakery_add_safetytext_to_list();
                    }
                }
            }
            $html .= '
                </tbody>
              </table>
              </div>
              <!--</form>-->
              <div id="add_safety_title" style="display: none;">
                <div class="cs-heading-area">
                  <h5><i class="icon-plus-circle"></i> ' . esc_html__('Safety Text Settings', 'foodbakery') . '</h5>
                  <span class="cs-btnclose" onClick="javascript:foodbakery_remove_overlay(\'add_safety_title\',\'append\')"> <i class="icon-times"></i></span> 	
				</div>';

            $foodbakery_opt_array = array(
                'name' => esc_html__('Title', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Title', 'foodbakery'),
                    'id' => 'safety_title',
                    'return' => true,
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Description', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => '',
                    'id' => 'safety_desc',
                    'return' => true,
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Add Safety Text to List', 'foodbakery'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'return' => true,
                    'cust_type' => 'button',
                    'extra_atr' => '  onClick="add_safety_to_list(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_foodbakery::plugin_url()) . '\')"',
                ),
            );
            $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
            $html .= '</div>';

            echo force_balance_tags($html, true);
        }

        /**
         * end Function how create safetytext data section
         */

        /**
         * Start Function how add data in safetytext  section
         */
        public function foodbakery_add_safetytext_to_list() {
            global $counter_safety, $safety_id, $foodbakery_safety_title, $foodbakery_safety_desc, $foodbakery_form_fields, $foodbakery_form_fields, $foodbakery_html_fields;
            foreach ($_POST as $keys => $values) {
                $$keys = $values;
            }
            $foodbakery_plugin_options = get_option("foodbakery_plugin_options");
            if (isset($_POST['foodbakery_safety_title']) && $_POST['foodbakery_safety_title'] <> '') {
                $safety_id = time();
                $foodbakery_safety_title = $_POST['foodbakery_safety_title'];
            }

            if (isset($_POST['foodbakery_safety_desc']) && $_POST['foodbakery_safety_desc'] <> '') {
                $foodbakery_safety_desc = $_POST['foodbakery_safety_desc'];
            }
            if (empty($safety_id)) {
                $safety_id = $counter_safety;
            }
            $foodbakery_safety_desc = isset($foodbakery_safety_desc) ? esc_attr($foodbakery_safety_desc) : '';
            $foodbakery_opt_array = array(
                'id' => '',
                'std' => absint($safety_id),
                'cust_id' => "",
                'cust_name' => "safety_id_array[]",
                'return' => true,
            );

            $html = '
                <tr class="parentdelete" id="edit_track' . esc_attr($counter_safety) . '">
                  <td id="subject-title' . esc_attr($counter_safety) . '" style="width:80%;">' . esc_attr($foodbakery_safety_title) . '</td>
                  <td class="centr" style="width:20%;"><a href="javascript:foodbakery_createpop(\'edit_track_form' . esc_js($counter_safety) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a href="#" class="delete-it btndeleteit actions delete">&nbsp;</a></td>
                  <td style="width:0"><div id="edit_track_form' . esc_attr($counter_safety) . '" style="display: none;" class="table-form-elem">
                      ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array) . '
                      <div class="cs-heading-area">
                        <h5 style="text-align: left;">' . esc_html__('Safety Settings', 'foodbakery') . '</h5>
                        <span onclick="javascript:foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_safety) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>
                        <div class="clear"></div>
                      </div>';


            $foodbakery_opt_array = array(
                'name' => esc_html__('Title', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $foodbakery_safety_title,
                    'id' => 'safety_title',
                    'return' => true,
                    'array' => true,
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => esc_html__('Description', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $foodbakery_safety_desc,
                    'id' => 'safety_desc',
                    'return' => true,
                    'array' => true,
                ),
            );

            $html .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);

            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_html__('Update', 'foodbakery'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'return' => true,
                    'cust_type' => 'button',
                    'extra_atr' => ' onclick="foodbakery_remove_overlay(\'edit_track_form' . esc_js($counter_safety) . '\',\'append\')"',
                ),
            );
            $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
            $html .= '
                    </div></td>
                </tr>';
            if (isset($_POST['foodbakery_safety_title']) && isset($_POST['foodbakery_safety_desc'])) {
                echo force_balance_tags($html);
            } else {
                return $html;
            }
            if (isset($_POST['foodbakery_safety_title']) && isset($_POST['foodbakery_safety_title']))
                die();
        }

        /**
         *
         * Array Fields
         */
        function foodbakery_in_array_field($array_val, $array_field, $array, $strict = false) {
            if ($strict) {
                foreach ($array as $item)
                    if (isset($item[$array_field]) && $item[$array_field] === $array_val)
                        return true;
            }
            else {
                foreach ($array as $item)
                    if (isset($item[$array_field]) && $item[$array_field] == $array_val)
                        return true;
            }
            return false;
        }

        /**
         * Start Function that how to check duplicate values
         */
        function foodbakery_check_duplicate_value($array_val, $array_field, $array) {
            $foodbakery_val_counter = 0;
            foreach ($array as $item) {
                if (isset($item[$array_field]) && $item[$array_field] == $array_val) {
                    $foodbakery_val_counter++;
                }
            }
            if ($foodbakery_val_counter > 1)
                return true;
            return false;
        }

        /**
         * End Function of how to check duplicate values
         */

        /**
         * Start Function that how to remove  duplicate values
         */
        function foodbakery_remove_duplicate_extra_value() {
            $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
            $foodbakery_extra_features_options = $foodbakery_plugin_options['foodbakery_extra_features_options'];
            $extrasdata = array();
            $extra_feature_array = $extra_features = '';
            if (isset($foodbakery_extra_features_options) && is_array($foodbakery_extra_features_options) && count($foodbakery_extra_features_options) > 0) {
                $extra_feature_array = $extra_features = $extrasdata = array();
                foreach ($foodbakery_extra_features_options as $extra_feature_key => $extra_feature) {
                    if (isset($extra_feature_key) && $extra_feature_key <> '') {
                        $extra_feature_id = isset($extra_feature['extra_feature_id']) ? $extra_feature['extra_feature_id'] : '';
                        $extra_feature_title = isset($extra_feature['foodbakery_extra_feature_title']) ? $extra_feature['foodbakery_extra_feature_title'] : '';
                        $extra_feature_price = isset($extra_feature['foodbakery_extra_feature_price']) ? $extra_feature['foodbakery_extra_feature_price'] : '';
                        $extra_feature_type = isset($extra_feature['foodbakery_extra_feature_type']) ? $extra_feature['foodbakery_extra_feature_type'] : '';
                        $extra_feature_guests = isset($extra_feature['foodbakery_extra_feature_guests']) ? $extra_feature['foodbakery_extra_feature_guests'] : '';
                        $extra_feature_fchange = isset($extra_feature['foodbakery_extra_feature_fchange']) ? $extra_feature['foodbakery_extra_feature_fchange'] : '';
                        $extra_feature_desc = isset($extra_feature['foodbakery_extra_feature_desc']) ? $extra_feature['foodbakery_extra_feature_desc'] : '';
                        if (!$this->foodbakery_check_duplicate_value($extra_feature_title, 'foodbakery_extra_feature_title', $foodbakery_extra_features_options)) {
                            $extra_feature_array['extra_feature_id'] = $extra_feature_id;
                            $extra_feature_array['foodbakery_extra_feature_title'] = $extra_feature_title;
                            $extra_feature_array['foodbakery_extra_feature_price'] = $extra_feature_price;
                            $extra_feature_array['foodbakery_extra_feature_type'] = $extra_feature_type;
                            $extra_feature_array['foodbakery_extra_feature_guests'] = $extra_feature_guests;
                            $extra_feature_array['foodbakery_extra_feature_fchange'] = $extra_feature_fchange;
                            $extra_feature_array['foodbakery_extra_feature_desc'] = $extra_feature_desc;
                            $extra_features[$extra_feature_id] = $extra_feature_array;
                        }
                    }
                }
                $extrasdata['foodbakery_extra_features_options'] = $extra_features;
                $foodbakery_options = array_merge($foodbakery_plugin_options, $extrasdata);
                update_option("foodbakery_plugin_options", $foodbakery_options);
            }
            //End if
        }

        /**
         * end Function of how to remove  duplicate values
         */
    }

    //End Class
}
if (!function_exists('foodbakery_settings_fields')) {

    /**
     * Start Function that set value in setting fields
     */
    function foodbakery_settings_fields($key, $param) {
        global $post, $foodbakery_html_fields;
        $foodbakery_gateway_options = get_option('foodbakery_gateway_options');
        $foodbakery_value = $param['std'];
        $html = '';
        switch ($param['type']) {
            case 'text':
                if (isset($foodbakery_gateway_options)) {
                    if (isset($foodbakery_gateway_options[$param['id']])) {
                        $val = $foodbakery_gateway_options[$param['id']];
                    } else {
                        $val = $param['std'];
                    }
                } else {
                    $val = $param['std'];
                }
                $foodbakery_opt_array = array(
                    'name' => esc_attr($param["name"]),
                    'desc' => '',
                    'hint_text' => esc_attr($param['desc']),
                    'echo' => false,
                    'field_params' => array(
                        'std' => $val,
                        'cust_id' => $param['id'],
                        'cust_name' => $param['id'],
                        'return' => true,
                        'cust_type' => $param['type'],
                        'classes' => 'vsmall',
                    ),
                );
                $output = $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                $html .= $output;
                break;
            case 'textarea':
                $val = $param['std'];
                $std = get_option($param['id']);
                if (isset($foodbakery_gateway_options)) {
                    if (isset($foodbakery_gateway_options[$param['id']])) {
                        $val = $foodbakery_gateway_options[$param['id']];
                    } else {
                        $val = $param['std'];
                    }
                } else {
                    $val = $param['std'];
                }


                $foodbakery_opt_array = array(
                    'name' => esc_attr($param["name"]),
                    'desc' => '',
                    'hint_text' => esc_attr($param['desc']),
                    'echo' => false,
                    'field_params' => array(
                        'std' => $val,
                        'cust_id' => $param['id'],
                        'cust_name' => $param['id'],
                        'return' => true,
                        'extra_atr' => 'rows="10" cols="60"',
                        'classes' => '',
                    ),
                );
                $output = $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);

                $html .= $output;
                break;
            case "checkbox":
                $saved_std = '';
                $std = '';
                if (isset($foodbakery_gateway_options)) {
                    if (isset($foodbakery_gateway_options[$param['id']])) {
                        $saved_std = $foodbakery_gateway_options[$param['id']];
                    }
                } else {
                    $std = $param['std'];
                }
                $checked = '';
                if (!empty($saved_std)) {
                    if ($saved_std == 'on') {
                        $checked = 'checked="checked"';
                    } else {
                        $checked = '';
                    }
                } elseif ($std == 'on') {
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }

                $foodbakery_opt_array = array(
                    'name' => esc_attr($param["name"]),
                    'desc' => '',
                    'hint_text' => esc_attr($param['desc']),
                    'echo' => false,
                    'field_params' => array(
                        'std' => '',
                        'cust_id' => $param['id'],
                        'cust_name' => $param['id'],
                        'return' => true,
                        'classes' => 'myClass',
                    ),
                );
                $output = $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
                $html .= $output;
                break;
            case "logo":
                if (isset($foodbakery_gateway_options) and $foodbakery_gateway_options <> '' && isset($foodbakery_gateway_options[$param['id']])) {
                    $val = $foodbakery_gateway_options[$param['id']];
                } else {
                    $val = $param['std'];
                }
                $output = '';
                $display = ($val <> '' ? 'display' : 'none');
                if (isset($value['tab'])) {
                    $output .='<div class="main_tab"><div class="horizontal_tab" style="display:' . $param['display'] . '" id="' . $param['tab'] . '">';
                }

                $foodbakery_opt_array = array(
                    'name' => esc_attr($param["name"]),
                    'desc' => '',
                    'hint_text' => esc_attr($param['desc']),
                    'echo' => false,
                    'field_params' => array(
                        'std' => $val,
                        'cust_id' => $param['id'],
                        'cust_name' => $param['id'],
                        'return' => true,
                        'classes' => '',
                    ),
                );
                $output = $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
                $html .= $output;
                break;
            case 'select' :

                $options = '';
                if (isset($param['options']) && is_array($param['options'])) {
                    foreach ($param['options'] as $value => $option) {
                        $options[$value] = $option;
                    }
                }

                $foodbakery_opt_array = array(
                    'name' => esc_attr($param["title"]),
                    'desc' => '',
                    'hint_text' => esc_attr($param['description']),
                    'echo' => false,
                    'field_params' => array(
                        'std' => $foodbakery_value,
                        'cust_id' => $param['id'],
                        'cust_name' => $param['id'],
                        'return' => true,
                        'classes' => 'cs-form-select cs-input chosen-select-no-single',
                        'options' => $options,
                    ),
                );
                $output = $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
                // append
                $html .= $output;
                break;
            default :
                break;
        }
        return $html;
    }

    /**
     * end Function of set value in setting fields
     */
}
/**
 * Start Function that how to Checkt load satus
 */
/* ---------------------------------------------------
 * Load States
 * -------------------------------------------------- */
if (!function_exists('foodbakery_load_states')) {

    function foodbakery_load_states() {
        global $foodbakery_theme_options;
        $foodbakery_locations = get_option('foodbakery_location_states');
        $states = '';
        $foodbakery_country = $_POST['country'];
        $foodbakery_country = trim(stripslashes($foodbakery_country));
        if ($foodbakery_country && $foodbakery_country != '') {
            $states_data = isset($foodbakery_locations[$foodbakery_country]) ? $foodbakery_locations[$foodbakery_country] : '';
            $states .= '<option value="">' . esc_html__('Select State', 'foodbakery') . '</option>';
            if (isset($states_data) && $states_data != '') {
                foreach ($states_data as $key => $value) {
                    if ($key != 'no-state') {
                        $states .='<option value="' . $value['name'] . '">' . $value['name'] . '</option>';
                    }
                }
            }
        }
        echo force_balance_tags($states);
        die();
    }

    add_action('wp_ajax_foodbakery_load_states', 'foodbakery_load_states');
}
/**
 * end Function that how to Checkout  load satus
 */
/**
 * Start Function that how add location in location fields
 */
if (!function_exists('add_locations')) {

    function add_locations($original, $items_to_add, $country, $state = '') {
        if (!empty($state)) {
            $target = $original[$country][$state];
        } else {
            $target = $original[$country];
        }
        $new_arr = array_merge($target, $items_to_add);
        if (!empty($state)) {
            $original[$country][$state] = $new_arr;
        } else {
            $original[$country] = $new_arr;
        }
        return $original;
    }

}

/**
 * end Function that how Delete location in location fields
 */
/**
 * Start Function that how Delete location in location fields
 */
if (!function_exists('foodbakery_delete_location')) {

    function foodbakery_delete_location() {
        global $foodbakery_theme_options;
        $type = $_POST['type'];
        $foodbakery_location_countries = get_option('foodbakery_location_countries');
        $foodbakery_location_states = get_option('foodbakery_location_states');
        $foodbakery_location_cities = get_option('foodbakery_location_cities');
        if ($type == 'country') {
            $node = $_POST['node'];
            $foodbakery_location_country = foodbakery_remove_location($foodbakery_location_countries, $foodbakery_location_countries[$node]);
            if (isset($foodbakery_location_states[$node])) {
                $foodbakery_location_states = foodbakery_remove_location($foodbakery_location_states, $foodbakery_location_states[$node]);
            }
            if (isset($foodbakery_location_cities[$node])) {
                $foodbakery_location_cities = foodbakery_remove_location($foodbakery_location_cities, $foodbakery_location_cities[$node]);
            }
            update_option('foodbakery_location_countries', $foodbakery_location_country);
            update_option('foodbakery_location_states', $foodbakery_location_states);
            update_option('foodbakery_location_cities', $foodbakery_location_cities);
        } else if ($type == 'state') {
            $node = $_POST['node'];
            $country_node = $_POST['country_node'];

            unset($foodbakery_location_states[$country_node][$node]);

            if (isset($foodbakery_location_cities[$country_node][$node])) {
                unset($foodbakery_location_cities[$country_node][$node]);
            }
            update_option('foodbakery_location_states', $foodbakery_location_states);
            update_option('foodbakery_location_cities', $foodbakery_location_cities);
        } else if ($type == 'city') {
            $node = $_POST['node'];
            $country_node = $_POST['country_node'];
            $state_node = $_POST['state_node'];
            unset($foodbakery_location_cities[$country_node][$state_node][$node]);
            update_option('foodbakery_location_cities', $foodbakery_location_cities);
        }
        die();
    }

    /**
     * Start Function that how Delete location in location fields
     */
    add_action('wp_ajax_foodbakery_delete_location', 'foodbakery_delete_location');
}
/**
 * Start Function that how remove location 
 */
if (!function_exists('foodbakery_remove_location')) {

    function foodbakery_remove_location($array, $item) {
        $index = array_search($item, $array);
        if ($index !== false) {
            unset($array[$index]);
        }
        return $array;
    }

}
/**
 * end Function of how to remove location 
 */
/**
 * Start Function that how to load country of states 
 */
if (!function_exists('foodbakery_load_country_states')) {

    function foodbakery_load_country_states() {
        global $foodbakery_theme_options;
        $states = '';
        $foodbakery_country = $_POST['country'];
        $json = array();
        $json['cities'] = '<option value="">' . esc_html__('Select City', 'foodbakery') . '</option>';
        $foodbakery_country = trim(stripslashes($foodbakery_country));
        if ($foodbakery_country && $foodbakery_country != '') {
            $states = '';
            $selected_spec = get_term_by('slug', $foodbakery_country, 'foodbakery_locations');
            $state_parent_id = $selected_spec->term_id;
            $states_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
                'parent' => $state_parent_id,
            );
            $cities = get_terms('foodbakery_locations', $states_args);

            if (isset($cities) && $cities != '' && is_array($cities)) {
                foreach ($cities as $key => $city) {
                    $json['cities'] .= "<option value='" . $city->slug . "'>" . $city->name . "</option>";
                }
            }
        }
        echo json_encode($json);
        die();
    }

    add_action("wp_ajax_foodbakery_load_country_states", "foodbakery_load_country_states");
    add_action("wp_ajax_nopriv_foodbakery_load_country_states", "foodbakery_load_country_states");
}
/**
 * end Function that how to load country of states 
 */
/**
 * Start Function that how to crate cities against country 
 */
if (!function_exists('foodbakery_load_country_cities')) {

    function foodbakery_load_country_cities() {
        global $foodbakery_theme_options;
        $foodbakery_country = $_POST['country'];
        $foodbakery_state = $_POST['state'];
        $json = array();
        $json['cities'] = '<option value="">' . esc_html__('Select City', 'foodbakery') . '</option>';
        if ($foodbakery_state && $foodbakery_state != '') {
            // load all cities against state  
            $cities = '';
            $selected_spec = get_term_by('slug', $foodbakery_state, 'foodbakery_locations');
            $state_parent_id = $selected_spec->term_id;
            $states_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
                'parent' => $state_parent_id,
            );
            $cities = get_terms('foodbakery_locations', $states_args);
            if (isset($cities) && $cities != '' && is_array($cities)) {
                foreach ($cities as $key => $city) {
                    $json['cities'] .= "<option value='" . $city->slug . "'>" . $city->name . "</option>";
                }
            }
        }
        echo json_encode($json);
        die();
    }

    add_action('wp_ajax_foodbakery_load_country_cities', 'foodbakery_load_country_cities');
}



if (class_exists('foodbakery_plugin_options')) {
    $settings_object = new foodbakery_plugin_options();
    add_action('admin_menu', array(&$settings_object, 'foodbakery_register_jobunt_settings'));
}

if (!function_exists('foodbakery_iconlist_plugin_options')) {

    function foodbakery_iconlist_plugin_options($icon_value = '', $id = '', $name = '') {
        global $foodbakery_form_fields, $foodbakery_plugin_static_text, $foodbakery_;
        $foodbakery_icomoon = '
        <script>
            jQuery(document).ready(function ($) {

                var e9_element = $(\'#e9_element_' . esc_html($id) . '\').fontIconPicker({
                    theme: \'fip-bootstrap\'
                });
                // Add the event on the button
                $(\'#e9_buttons_' . esc_html($id) . ' button\').on(\'click\', function (e) {
                    e.preventDefault();
                    // Show processing message
                    $(this).prop(\'disabled\', true).html(\'<i class="icon-cog demo-animate-spin"></i> ' . esc_html__('Please wait...', 'foodbakery') . '\');
                    $.ajax({
                        url: "' . $foodbakery_Class->plugin_url() . 'assets/icomoon/js/selection.json",
                        type: \'GET\',
                        dataType: \'json\'
                    }).done(function (response) {
                            // Get the class prefix
                            var classPrefix = response.preferences.fontPref.prefix,
                                    icomoon_json_icons = [],
                                    icomoon_json_search = [];
                            $.each(response.icons, function (i, v) {
                                    icomoon_json_icons.push(classPrefix + v.properties.name);
                                    if (v.icon && v.icon.tags && v.icon.tags.length) {
                                            icomoon_json_search.push(v.properties.name + \' \' + v.icon.tags.join(\' \'));
                                    } else {
                                            icomoon_json_search.push(v.properties.name);
                                    }
                            });
                            // Set new fonts on fontIconPicker
                            e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                            // Show success message and disable
                            $(\'#e9_buttons_' . esc_html($id) . ' button\').removeClass(\'btn-primary\').addClass(\'btn-success\').text(\'' . esc_html__('Load Icon','foodbakery') . '\').prop(\'disabled\', true);
                    })
                    .fail(function () {
                            // Show error message and enable
                            $(\'#e9_buttons_' . esc_html($id) . ' button\').removeClass(\'btn-primary\').addClass(\'btn-danger\').text(\'' . esc_html__('Try Again','foodbakery') . '\').prop(\'disabled\', false);
                    });
                    e.stopPropagation();
                });
                jQuery("#e9_buttons_' . esc_html($id) . ' button").click();
            });
        </script>';
        $foodbakery_opt_array = array(
            'std' => esc_html($icon_value),
            'cust_id' => 'e9_element_' . esc_html($id),
            'cust_name' => esc_html($name) . '[]',
            'return' => true,
        );
        $foodbakery_icomoon .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
        $foodbakery_icomoon .= '
        <span id="e9_buttons_' . esc_html($id) . '" style="display:none">
            <button autocomplete="off" type="button" class="btn btn-primary">' . esc_html__('Load Json','foodbakery') . '</button>
        </span>';

        return $foodbakery_icomoon;
    }

}
