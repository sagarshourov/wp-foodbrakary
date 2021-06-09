<?php
/**
 * File Type: Memberships Post Type Metas
 */
if ( ! class_exists('packages_post_type_meta') ) {

	class packages_post_type_meta {

		/**
		 * Start Contructer Function
		 */
		public function __construct() {
			add_action('add_meta_boxes', array( &$this, 'packages_add_meta_boxes_callback' ));
            add_action('save_post', array( $this, 'foodbakery_insert_package_metas' ), 18);
			add_action('wp_ajax_add_package_field', array( $this, 'add_package_field_callback' ));
		}

		/**
		 * Add meta boxes Callback Function
		 */
		public function packages_add_meta_boxes_callback() {
			add_meta_box('foodbakery_meta_packages', esc_html(foodbakery_plugin_text_srt('foodbakery_restaurant_packages_options')), array( $this, 'foodbakery_meta_packages' ), 'packages', 'normal', 'high');
		}

		/**
		 * Creating an array for meta fields
		 */
		public function foodbakery_meta_packages() {
			global $post, $foodbakery_html_fields;
			$foodbakery_packages_fields = array();

			$package_data = get_post_meta($post->ID, 'foodbakery_package_data', true);
			$package_icon = get_post_meta($post->ID, 'foodbakery_package_icon', true);
			$package_icon = ( isset($package_icon[0]) ) ? $package_icon[0] : '';

			echo '
			<div class="form-elements">

			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<label>' . foodbakery_plugin_text_srt('foodbakery_package_tile') . '</label>
			</div>

			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
			<div id="foodbakery_title_move"></div>
			</div>

			</div>';
			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_package_type'),
				'desc' => '',
				'hint_text' => foodbakery_plugin_text_srt('foodbakery_package_type_hint'),
				'echo' => true,
				'field_params' => array(
					'std' => '',
					'id' => 'package_type',
					'classes' => 'function-class',
					'return' => true,
					'options' => array(
						'free' => foodbakery_plugin_text_srt('foodbakery_package_type_free'),
						'paid' => foodbakery_plugin_text_srt('foodbakery_package_type_paid'),
					),
				),
			);
			$foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			// show/hide package price field.
			$package_value = isset($package_value) ? $package_value : '';
			$package_value = get_post_meta($post->ID, 'foodbakery_package_type', true);
			$display = 'none';
			if ( $package_value == 'paid' ) {
				$display = 'block';
			} else {
				$display = 'none';
			}
			?>

			<div class="package-price-area" id="package-price-area" style="display:<?php echo esc_html($display); ?>;">
				<?php
				$foodbakery_opt_array = array(
					'name' => foodbakery_plugin_text_srt('foodbakery_package_price'),
					'desc' => '',
					'hint_text' => foodbakery_plugin_text_srt('foodbakery_package_price_hint'),
					'echo' => true,
					'field_params' => array(
						'std' => '',
						'id' => 'package_price',
						'classes' => 'foodbakery-dev-req-field-admin',
						'extra_atr' => 'data-visible="package-price-area"',
						'return' => true,
					),
				);

				$foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
				?>
			</div>

			<?php
			
			$foodbakery_opt_array = array(
				'name' => __('Commission Based', 'foodbakery'),
				'desc' => '',
				'hint_text' => __('A commision amount will deduct for each order.', 'foodbakery'),
				'echo' => true,
				'field_params' => array(
					'std' => '',
					'id' => 'package_commision_based',
					'classes' => '',
					'extra_atr' => '',
					'return' => true,
				),
			);

			$foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
			
			$foodbakery_opt_array = array(
				'name' => __('Commission Percentage', 'foodbakery'),
				'desc' => '',
				'hint_text' => __('Define Commission Percentage i.e 10', 'foodbakery'),
				'echo' => true,
				'field_params' => array(
					'std' => '10',
					'id' => 'package_commision',
					'classes' => '',
					'extra_atr' => '',
					'return' => true,
				),
			);

			$foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	

			/*
			 * Membership Features Array
			 */

			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_phone_num'),
				'id' => 'radius_fields',
				'desc' => '',
				'hint_text' => '',
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'checkbox', 
						'field_params' => array(
							'std' => ( isset($package_data['phone_number']['value']) ) ? $package_data['phone_number']['value'] : 'on',
							'force_std' => true,
							'cust_name' => 'package_field[phone_number][value]',
							'id' => 'package_field[phone_number][value]',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);

			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_website_link'),
				'id' => 'radius_fields',
				'desc' => '',
				'hint_text' => '',
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'checkbox', 'field_params' => array(
							'std' => ( isset($package_data['website_link']['value']) ) ? $package_data['website_link']['value'] : 'on',
							'force_std' => true,
							'cust_name' => 'package_field[website_link][value]',
							'id' => 'package_field[website_link][value]',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);
			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_duration'),
				'id' => 'radius_fields',
				'desc' => '',
				
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'text', 'field_params' => array(
							'std' => ( isset($package_data['duration']['value']) ) ? $package_data['duration']['value'] : '15',
							'force_std' => true,
							'cust_name' => 'package_field[duration][value]',
							'id' => 'package_field[duration][value]',
							'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt('foodbakery_restaurant_packages_days') . '"',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);
			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_restaurant_duration'),
				'id' => 'radius_fields',
				'desc' => '',
				
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'text', 'field_params' => array(
							'std' => ( isset($package_data['restaurant_duration']['value']) ) ? $package_data['restaurant_duration']['value'] : '15',
							'force_std' => true,
							'cust_name' => 'package_field[restaurant_duration][value]',
							'id' => 'package_field[restaurant_duration][value]',
							'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt('foodbakery_restaurant_packages_days') . '"',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);

			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_number_of_feature_restaurants'),
				'id' => 'featured_restaurants',
				'desc' => '',
				
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'checkbox', 'field_params' => array(
							'std' => ( isset($package_data['number_of_featured_restaurants']['value']) ) ? $package_data['number_of_featured_restaurants']['value'] : 'on',
							'force_std' => true,
							'cust_name' => 'package_field[number_of_featured_restaurants][value]',
							'id' => 'package_field[number_of_featured_restaurants][value]',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);

			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_number_of_top_cat_restaurants'),
				'id' => 'top_cat_restaurants',
				'desc' => '',
				
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'checkbox', 'field_params' => array(
							'std' => ( isset($package_data['number_of_top_cat_restaurants']['value']) ) ? $package_data['number_of_top_cat_restaurants']['value'] : 'on',
							'force_std' => true,
							'cust_name' => 'package_field[number_of_top_cat_restaurants][value]',
							'id' => 'package_field[number_of_top_cat_restaurants][value]',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);

			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_social_impressions'),
				'id' => 'radius_fields',
				'desc' => '',
				'hint_text' => '',
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'checkbox', 'field_params' => array(
							'std' => ( isset($package_data['social_impressions_reach']['value']) ) ? $package_data['social_impressions_reach']['value'] : 'on',
							'force_std' => true,
							'cust_name' => 'package_field[social_impressions_reach][value]',
							'id' => 'package_field[social_impressions_reach][value]',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);
			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_reviews'),
				'id' => 'radius_fields',
				'desc' => '',
				
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'checkbox', 'field_params' => array(
							'std' => ( isset($package_data['reviews']['value']) ) ? $package_data['reviews']['value'] : 'on',
							'force_std' => true,
							'cust_name' => 'package_field[reviews][value]',
							'id' => 'package_field[reviews][value]',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);

			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_num_tags'),
				'id' => 'radius_fields',
				'desc' => '',
				
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'text', 'field_params' => array(
							'std' => ( isset($package_data['number_of_tags']['value']) ) ? $package_data['number_of_tags']['value'] : '6',
							'force_std' => true,
							'cust_name' => 'package_field[number_of_tags][value]',
							'id' => 'package_field[number_of_tags][value]',
							'extra_atr' => ' placeholder="' . foodbakery_plugin_text_srt('foodbakery_restaurant_packages_num_tags') . '"',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);

			$foodbakery_packages_fields[] = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_packages_respond_reviews'),
				'id' => 'radius_fields',
				'desc' => '',
				'hint_text' => '',
				'type' => 'multi_fields',
				'echo' => true,
				'fields_list' => array(
					array(
						'type' => 'checkbox', 'field_params' => array(
							'std' => ( isset($package_data['respond_to_reviews']['value']) ) ? $package_data['respond_to_reviews']['value'] : 'on',
							'force_std' => true,
							'cust_name' => 'package_field[respond_to_reviews][value]',
							'id' => 'package_field[respond_to_reviews][value]',
							'return' => true,
							'classes' => 'input-medium',
						),
					),

				),
			);

			$foodbakery_packages_fields = apply_filters('package_meta_fields', $foodbakery_packages_fields);
			?>
			<div class="package_options">
				<?php
				foreach ( $foodbakery_packages_fields as $field_array ) {
					$this->foodbakery_meta_packages_fields($field_array);
				}
				$this->foodbakery_add_package_field();
				?>

			</div>
			<div class="clear"></div>
			<script type="text/javascript">
			    jQuery('.function-class').change(function ($) {
			        var value = jQuery(this).val();
			        var parentNode = jQuery(this).parent().parent().parent();
			        if (value == 'paid') {
			            parentNode.find(".package-price-area").show();

			        } else {
			            parentNode.find(".package-price-area").hide();
			        }

			    }
			    );
			</script>
			<?php
		}

		/**
		 * Creating Meta fields from array
		 */
		function foodbakery_meta_packages_fields($field_array = array()) {
			global $foodbakery_html_fields;
			$field_array['type'] = ( isset($field_array['type']) ) ? $field_array['type'] : '';

			switch ( $field_array['type'] ) {

				case "checkbox":
					$foodbakery_html_fields->foodbakery_checkbox_field($field_array);
					break;

				case "text":
					$foodbakery_html_fields->foodbakery_text_field($field_array);
					break;

				case "select":
					$foodbakery_html_fields->foodbakery_select_field($field_array);
					break;

				case "heading":
					$foodbakery_html_fields->foodbakery_heading_render($field_array);
					break;

				case "multi_fields":
					$foodbakery_html_fields->foodbakery_multi_fields($field_array);
					break;
			}
		}

		public function foodbakery_insert_package_metas($post_id) {

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return;
			}
			if ( isset($_POST['package_field']) ) {
				$number_of_allowed_restaurants = isset($_POST['package_field']['number_of_restaurant_allowed']['value']) ? $_POST['package_field']['number_of_restaurant_allowed']['value'] : '';
				$number_of_featured_restaurants = isset($_POST['package_field']['number_of_featured_restaurants']['value']) ? $_POST['package_field']['number_of_featured_restaurants']['value'] : '';
				$number_of_top_cat_restaurants = isset($_POST['package_field']['number_of_top_cat_restaurants']['value']) ? $_POST['package_field']['number_of_top_cat_restaurants']['value'] : '';				

				update_post_meta($post_id, 'foodbakery_package_data', $_POST['package_field']);
			}

			if ( isset($_POST['foodbakery_package_fields']) && is_array($_POST['foodbakery_package_fields']) ) {
				$fields_array = array();
				$field_counter = 0;
				foreach ( $_POST['foodbakery_package_fields'] as $field ) {
					$field_label = isset($_POST['foodbakery_package_field_label'][$field_counter]) ? $_POST['foodbakery_package_field_label'][$field_counter] : '';
					$field_value = isset($_POST['foodbakery_package_field_value'][$field_counter]) ? $_POST['foodbakery_package_field_value'][$field_counter] : '';
					$field_type = isset($_POST['foodbakery_package_field_type'][$field_counter]) ? $_POST['foodbakery_package_field_type'][$field_counter] : '';
					$fields_array[$field] = array( 'key' => 'field_' . $field, 'field_label' => $field_label, 'field_value' => $field_value, 'field_type' => $field_type );
					$field_counter ++;
				}
				update_post_meta($post_id, 'foodbakery_package_fields', $fields_array);
			} else {
				update_post_meta($post_id, 'foodbakery_package_fields', '');
			}
		}

		public function foodbakery_add_package_field() {

			global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;
			$foodbakery_package_fields = get_post_meta($post->ID, 'foodbakery_package_fields', true);

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_package_additional_fields'),
				'id' => 'package_additional_fields',
				'classes' => '',
				'std' => '',
				'description' => '',
				'hint' => '',
			);
			$html = $foodbakery_html_fields->foodbakery_heading_render($foodbakery_opt_array);
			$html .= '<div id="package_fields">';
			if ( is_array($foodbakery_package_fields) && sizeof($foodbakery_package_fields) > 0 ) {
				foreach ( $foodbakery_package_fields as $field_key => $fields ) {
					if ( isset($fields) && $fields != '' ) {
						$counter_feature = $field_id = $field_key;
						$foodbakery_field_label = isset($fields['field_label']) ? $fields['field_label'] : '';
						$foodbakery_field_value = isset($fields['field_value']) ? $fields['field_value'] : '';
						$foodbakery_field_type = isset($fields['field_type']) ? $fields['field_type'] : '';

						$foodbakery_fields_array = array(
							'counter_field' => $counter_feature,
							'field_id' => $field_id,
							'foodbakery_field_label' => $foodbakery_field_label,
							'foodbakery_field_value' => $foodbakery_field_value,
							'foodbakery_field_type' => $foodbakery_field_type,
						);
						$html .= $this->add_package_field_callback($foodbakery_fields_array);
					}
				}
			}
			$html .= '</div>';
			$html .= '
			<div class="form-elements">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label></label></div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					<a href="javascript:foodbakery_createpop(\'add_field_title\',\'filter\')" class="button">' . foodbakery_plugin_text_srt('foodbakery_add_field') . '</a>
				</div>
			</div>
			<div id="add_field_title" style="display: none;">
				<div class="cs-heading-area">
				<h5><i class="icon-plus-circle"></i> ' . foodbakery_plugin_text_srt('foodbakery_package_field') . '</h5>
				<span class="cs-btnclose" onClick="javascript:foodbakery_removeoverlay(\'add_field_title\',\'append\')"> <i class="icon-times"></i></span> 	
			</div>';

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_add_field_label'),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'std' => '',
					'id' => 'field_label',
					'extra_atr' => 'title="' . foodbakery_plugin_text_srt('foodbakery_add_field_label') . '"',
					'return' => true,
				),
			);

			$html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_add_field_type'),
				'desc' => '',
				'hint_text' => foodbakery_plugin_text_srt('foodbakery_add_field_type_hint'),
				'echo' => false,
				'field_params' => array(
					'std' => '',
					'id' => 'field_type',
					'classes' => 'chosen-select select-medium',
					'options' => array(
						'single-line' => foodbakery_plugin_text_srt('foodbakery_add_field_single_line'),
						'single-choice' => foodbakery_plugin_text_srt('foodbakery_add_field_single_choice'),
					),
					'return' => true,
				),
			);

			$html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

			$html .= '
					<ul class="form-elements noborder">
					  <li class="to-label"></li>
					  <li class="to-field">
							<input type="button" value="' . foodbakery_plugin_text_srt('foodbakery_add_field') . '" onclick="add_package_field(\'' . esc_js(admin_url('admin-ajax.php')) . '\')" />
							<div class="package-field-loader"></div>
					  </li>
					</ul>
			</div>';

			echo force_balance_tags($html, true);
		}

		public function add_package_field_callback($foodbakery_atts = array()) {

			global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;
			$foodbakery_defaults = array(
				'counter_field' => '',
				'field_id' => '',
				'foodbakery_field_label' => '',
				'foodbakery_field_type' => '',
				'foodbakery_field_value' => '',
			);
			extract(shortcode_atts($foodbakery_defaults, $foodbakery_atts));

			foreach ( $_POST as $keys => $values ) {
				$$keys = $values;
			}

			if ( isset($_POST['foodbakery_field_label']) && $_POST['foodbakery_field_label'] != '' ) {
				$foodbakery_field_label = $_POST['foodbakery_field_label'];
			}

			if ( isset($_POST['foodbakery_field_type']) && $_POST['foodbakery_field_type'] != '' ) {
				$foodbakery_field_type = $_POST['foodbakery_field_type'];
			}

			if ( $field_id == '' && $counter_field == '' ) {
				$counter_field = $field_id = rand(1000000000, 9999999999);
			}

			$html = '';
			$html .= '<div class="package-field parentdelete">';

			$foodbakery_opt_array = array(
				'std' => absint($field_id),
				'id' => '',
				'cust_name' => 'foodbakery_package_fields[]',
				'return' => true,
				'force_std' => true,
			);
			$html .= $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

			$foodbakery_opt_array = array(
				'std' => esc_html($foodbakery_field_label),
				'id' => '',
				'cust_name' => 'foodbakery_package_field_label[]',
				'return' => true,
				'force_std' => true,
			);
			$html .= $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

			$foodbakery_opt_array = array(
				'std' => esc_html($foodbakery_field_type),
				'id' => '',
				'cust_name' => 'foodbakery_package_field_type[]',
				'return' => true,
				'force_std' => true,
			);
			$html .= $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

			$html .= '<div class="form-elements to-table">';
			$html .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . esc_html($foodbakery_field_label) . '</label></div>';
			$html .= '<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$foodbakery_opt_array = array(
				'std' => esc_html($foodbakery_field_value),
				'id' => 'package_field_value' . absint($counter_field),
				'cust_name' => 'foodbakery_package_field_value[]',
				'return' => true,
				'force_std' => true,
				'classes' => 'input-medium package-field',
			);
			if ( 'single-line' == $foodbakery_field_type ) {
				$html .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
			} else {
				$html .= $foodbakery_form_fields->foodbakery_form_checkbox_render($foodbakery_opt_array);
			}
			$html .= '<a class="package-field-delete delete-it actions delete" href="javascript:void(0);">&nbsp;</a>';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '</div>';

			if ( isset($_POST['foodbakery_field_label']) ) {
				echo force_balance_tags($html);
			} else {
				return $html;
			}

			if ( isset($_POST['foodbakery_field_label']) ) {
				die();
			}
		}

	}

	// Initialize Object
	$packages_meta_object = new packages_post_type_meta();
}