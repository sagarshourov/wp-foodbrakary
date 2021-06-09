<?php
/**
 * Restaurant Type custom 
 * dynamic Fields
 */
if ( ! class_exists('Foodbakery_Restaurant_Type_Form_Builder_Dynamic_Fields') ) {

	class Foodbakery_Restaurant_Type_Form_Builder_Dynamic_Fields {

		public function __construct() {
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_text', array( $this, 'foodbakery_restaurant_type_reservation_pb_text' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_availability', array( $this, 'foodbakery_restaurant_type_reservation_pb_availability' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_number', array( $this, 'foodbakery_restaurant_type_reservation_pb_number' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_textarea', array( $this, 'foodbakery_restaurant_type_reservation_pb_textarea' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_dropdown', array( $this, 'foodbakery_restaurant_type_reservation_pb_dropdown' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_date', array( $this, 'foodbakery_restaurant_type_reservation_pb_date' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_email', array( $this, 'foodbakery_restaurant_type_reservation_pb_email' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_url', array( $this, 'foodbakery_restaurant_type_reservation_pb_url' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_range', array( $this, 'foodbakery_restaurant_type_reservation_pb_range' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_section', array( $this, 'foodbakery_restaurant_type_reservation_pb_section' ));
			add_action('wp_ajax_foodbakery_check_reservation_fields_avail', array( $this, 'foodbakery_check_reservation_fields_avail' ));
			add_action('wp_ajax_foodbakery_restaurant_type_reservation_pb_time', array( $this, 'foodbakery_restaurant_type_reservation_pb_time' ));
		}

		function custom_fields() {
			global $post, $foodbakery_count_node, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;
			$rand_f_counter = rand(1000000, 99999999);
			?>
			<div class="inside-tab-content">
				<div class="dragitem">
					<h4><?php echo foodbakery_plugin_text_srt('foodbakery_click_to_add_item'); ?></h4>
					<div class="pb-form-buttons">
						<ul>
							<li id="field-section-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_section','section', '<?php echo absint($rand_f_counter) ?>')" title="Section" data-type="section" data-name="custom_section"><i class=" icon-section"></i><?php echo foodbakery_plugin_text_srt('foodbakery_section'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-text-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_text','text', '<?php echo absint($rand_f_counter) ?>')" title="Text" data-type="text" data-name="custom_text"><i class="icon-new-message"></i><?php echo foodbakery_plugin_text_srt('foodbakery_text'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-number-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_number','number', '<?php echo absint($rand_f_counter) ?>')" title="Text" data-type="number" data-name="custom_number"><i class="icon-file-text"></i><?php echo foodbakery_plugin_text_srt('foodbakery_number'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-textarea-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_textarea','textarea', '<?php echo absint($rand_f_counter) ?>')" title="Textarea" data-type="textarea" data-name="custom_textarea"><i class="icon-message"></i><?php echo foodbakery_plugin_text_srt('foodbakery_textarea'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-select-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_dropdown','select', '<?php echo absint($rand_f_counter) ?>')" title="Dropdown" data-type="select" data-name="custom_select"><i class="icon-arrow-down"></i><?php echo foodbakery_plugin_text_srt('foodbakery_dropdown'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-email-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_email','email', '<?php echo absint($rand_f_counter) ?>')" title="Email" data-type="email" data-name="custom_email"><i class="icon-mail"></i><?php echo foodbakery_plugin_text_srt('foodbakery_email'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-url-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_url','url', '<?php echo absint($rand_f_counter) ?>')" title="URL" data-type="url" data-name="custom_url"><i class="icon-link2"></i><?php echo foodbakery_plugin_text_srt('foodbakery_url'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-range-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_range','range', '<?php echo absint($rand_f_counter) ?>')" title="Range" data-type="range" data-name="custom_range"><i class=" icon-target2"></i><?php echo foodbakery_plugin_text_srt('foodbakery_range'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-time-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_time','time', '<?php echo absint($rand_f_counter) ?>')" title="Time" data-type="time" data-name="custom_time"><i class="icon-clock3"></i><?php echo foodbakery_plugin_text_srt('foodbakery_time'); ?>&nbsp;&nbsp;<span></span></a></li>
							<li id="field-availability-<?php echo absint($rand_f_counter) ?>"><a href="javascript:foodbakery_restaurant_type_field_reservation_add('foodbakery_restaurant_type_reservation_pb_availability','availability', '<?php echo absint($rand_f_counter) ?>')" title="Availability" data-type="availability" data-name="custom_availability"><i class="icon-calendar4"></i><?php echo foodbakery_plugin_text_srt('foodbakery_availability'); ?>&nbsp;&nbsp;<span></span></a></li>
						</ul>
					</div>
				</div>
				<div class="cs-custom-fields">
					<div id="cs-pb-formelements_form_builder">
						<?php
						$foodbakery_count_node = 0;
						$count_widget = 0;
						$foodbakery_restaurant_type_reservation_fields = get_post_meta($post->ID, "foodbakery_restaurant_type_reservation_fields", true);
						if ( is_array($foodbakery_restaurant_type_reservation_fields) && sizeof($foodbakery_restaurant_type_reservation_fields) > 0 ) {

							foreach ( $foodbakery_restaurant_type_reservation_fields as $f_key => $foodbakery_field ) {
								global $foodbakery_f_counter;
								$foodbakery_f_counter = $f_key;
								if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "text" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_text(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "availability" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_availability(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "number" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_number(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "section" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_section(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "textarea" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_textarea(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "dropdown" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_dropdown(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "date" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_date(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "email" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_email(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "url" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_url(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "range" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_range(1);
								} else if ( isset($foodbakery_field['type']) && $foodbakery_field['type'] == "time" ) {
									$foodbakery_count_node ++;
									$this->foodbakery_restaurant_type_reservation_pb_time(1);
								}
							}
						}
						?>
					</div>
				</div>

				<div class="alert alert-warning" id="cs-pbwp-alert"><?php echo foodbakery_plugin_text_srt('foodbakery_please_insert_item') ?></div>
				<input type="hidden" name="custom_fields_elements" value="1" />

				<script type="text/javascript">
			        jQuery(document).ready(function ($) {
			            foodbakery_custom_fields_form_builder_js();
			            chosen_selectionbox();
			        });
			        var counter = <?php echo esc_js($foodbakery_count_node); ?>;
			        function foodbakery_restaurant_type_field_reservation_add(action, value, f_counter) {
			            var count_field = jQuery(":input[value='" + value + "']").length;
			            if (count_field > 0 && (value == 'services' || value == 'file' || value == 'quantity' || value == 'availability')) {
			                alert('You can add only 1 ' + value + ' field.');
			                return false;
			            } else {
			                counter++;
			                var this_loader = $("#field-" + value + "-" + f_counter);
			                this_loader.find('span').html('<img src="<?php echo wp_foodbakery::plugin_url() ?>assets/backend/images/ajax-loader.gif" alt="">');
			                var newCustomerForm = "action=" + action + '&counter=' + counter;
			                jQuery.ajax({
			                    type: "POST",
			                    url: "<?php echo esc_js(admin_url('admin-ajax.php')); ?>",
			                    data: newCustomerForm,
			                    success: function (data) {
			                        jQuery("#cs-pb-formelements_form_builder").append(data);
			                        chosen_selectionbox();
			                        this_loader.find('span').html('');
			                    }
			                });
			            }
			        }
				</script> 
			</div>
			<?php
		}

		public function foodbakery_restaurant_type_reservation_pb_section($die = 0, $foodbakery_return = false) {

			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_section_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_section_small');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_section[label]',
				'title' => __( 'Section Text', 'foodbakery' ),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
				'id' => '',
				'name' => 'cus_field_reservation_section[description]',
				'title' => __( 'Section Description', 'foodbakery' ),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_section',
				'name' => 'foodbakery_cus_field_reservation_section[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'section', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );

			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);

			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/*
		 * Textarea field
		 */

		public function foodbakery_restaurant_type_reservation_pb_textarea($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_textarea_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_textarea');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[label]',
                'classes' => '',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[meta_key]',
                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => foodbakery_plugin_text_srt('foodbakery_meta_key_hint'),
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[default_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_default_value'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_textarea',
				'name' => 'foodbakery_cus_field_reservation_textarea[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'textarea', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );
			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/**
		 * Start function how to create Text Fields
		 */
		public function foodbakery_restaurant_type_reservation_pb_text($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;


			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_text_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_text_small');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_text[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_text[label]',
                'classes' => '',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_text[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_text[meta_key]',
                                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => foodbakery_plugin_text_srt('foodbakery_meta_key_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_text[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_text[default_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_default_value'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_text[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_text',
				'name' => 'foodbakery_cus_field_reservation_text[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'text', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );

			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);

			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		public function foodbakery_restaurant_type_reservation_pb_time($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;


			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_time_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_time_small');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_time[label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_time[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_time[meta_key]',
                                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => foodbakery_plugin_text_srt('foodbakery_meta_key_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_time[time_lapse]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_time_lapse'),
				'std' => '15',
				'classes' => 'foodbakery-dev-req-field-admin foodbakery-number-field foodbakery-range-field',
				'extra_attr' => 'data-min="1" data-max="60"',
				'hint' => foodbakery_plugin_text_srt('foodbakery_field_time_lapse_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_time',
				'name' => 'cus_field_reservation_time[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_time[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'time', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );

			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);

			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		public function foodbakery_restaurant_type_reservation_pb_availability($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;


			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_availability_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_availability');
			}
			
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_availability[label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_availability[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_availability[meta_key]',
				'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => foodbakery_plugin_text_srt('foodbakery_meta_key_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_availability[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_availability',
				'name' => 'foodbakery_cus_field_reservation_availability[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_availability[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'availability', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );

			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);

			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		public function foodbakery_restaurant_type_reservation_pb_number($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;


			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_number_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_number_small');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_number[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_number[label]',
                'classes' => '',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_number[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_number[meta_key]',
                                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => foodbakery_plugin_text_srt('foodbakery_meta_key_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_number[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_number[default_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_default_value'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_number[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_text',
				'name' => 'foodbakery_cus_field_reservation_number[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'number', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );

			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);

			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/**
		 * Start function how to create Textarea Fields
		 */
		public function foodbakery_enable_upload($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_textarea_small'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_textarea_small');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[featured]',
				'title' => foodbakery_plugin_text_srt('foodbakery_restaurant_featured'),
				'std' => '',
				'options' => array( 'no' => foodbakery_plugin_text_srt('foodbakery_restaurant_no'), 'yes' => foodbakery_plugin_text_srt('foodbakery_restaurant_yes') ),
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[meta_key]',
				'class' => 'dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[help]',
				'title' => foodbakery_plugin_text_srt('foodbakery_help_text'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[rows]',
				'title' => foodbakery_plugin_text_srt('foodbakery_rows'),
				'std' => '5',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[cols]',
				'title' => foodbakery_plugin_text_srt('foodbakery_columns'),
				'std' => '25',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[default_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_default_value'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[collapse_search]',
				'title' => foodbakery_plugin_text_srt('foodbakery_collapse_in_search'),
				'std' => '',
				'options' => array( 'no' => foodbakery_plugin_text_srt('foodbakery_restaurant_no'), 'yes' => foodbakery_plugin_text_srt('foodbakery_restaurant_yes') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_textarea[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_textarea',
				'name' => 'foodbakery_cus_field_reservation_textarea[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'textarea', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );
			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/**
		 * End function how to create Textarea Fields
		 */

		/**
		 * Start function how to create dropdown option fields
		 */
		public function foodbakery_restaurant_type_reservation_pb_dropdown($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text, $foodbakery_Class;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_dropdown_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_dropdown');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_dropdown[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_dropdown[label]',
                                'classes' => '',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_dropdown[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_dropdown[meta_key]',
                                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_dropdown[first_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_first_value'),
				'std' => '- select -',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_dropdown[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_selectbox',
				'name' => 'foodbakery_cus_field_reservation_dropdown[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= '
			<div class="form-elements field-dropdown-opt-values">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . foodbakery_plugin_text_srt('foodbakery_options') . '</label>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';

			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]['options']['value']) ) {
				$foodbakery_opt_counter = 0;
				$foodbakery_radio_counter = 1;
				foreach ( $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]['options']['value'] as $foodbakery_option ) {
					$foodbakery_checked = (int) $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]['options']['select'][0] == (int) $foodbakery_radio_counter ? ' checked="checked"' : '';
					$foodbakery_opt_label = $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]['options']['label'][$foodbakery_opt_counter];
					$foodbakery_opt_img = $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]['options']['img'][$foodbakery_opt_counter];
					$foodbakery_fields_markup .= '
					<div class="pbwp-clone-field clearfix">';
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

					$foodbakery_fields_markup .= '<div class="cs-dr-option-img">';
					$foodbakery_opt_array = array(
						'id' => 'cus_field_dropdown_options_imgs',
						'cust_name' => 'cus_field_dropdown[options_imgs][' . absint($foodbakery_counter) . '][]',
						'std' => $foodbakery_opt_img,
						'array' => true,
						'return' => true,
					);
					$foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_img_upload_button($foodbakery_opt_array);
					$foodbakery_fields_markup .= '</div>';

					$foodbakery_fields_markup .= '
                            <img src="' . esc_url(wp_foodbakery::plugin_url() . 'assets/backend/images/add.png') . '" class="pbwp-clone-field" alt="' . foodbakery_plugin_text_srt('foodbakery_add_another') . '" style="cursor:pointer; margin:0 3px;">
                            <img src="' . esc_url(wp_foodbakery::plugin_url() . 'assets/backend/images/remove.png') . '" alt="' . foodbakery_plugin_text_srt('foodbakery_remove_this') . '" class="pbwp-remove-field" style="cursor:pointer;">
                    </div>';
					$foodbakery_opt_counter ++;
					$foodbakery_radio_counter ++;
				}
			} else {
				$foodbakery_fields_markup .= '
				<div class="pbwp-clone-field clearfix">';

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
					'extra_atr' => ' data-type="option" placeholder="Label"',
					'std' => '',
					'classes' => 'input-small',
					'return' => true,
				);
				$foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

				$foodbakery_opt_array = array(
					'cust_id' => 'cus_field_dropdown_options_values_' . absint($foodbakery_counter),
					'cust_name' => 'cus_field_dropdown[options_values][' . absint($foodbakery_counter) . '][]',
					'extra_atr' => ' placeholder="Value"',
					'std' => '',
					'classes' => 'input-small',
					'return' => true,
				);
				$foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

				$foodbakery_fields_markup .= '<div class="cs-dr-option-img">';
				$foodbakery_opt_array = array(
					'id' => 'cus_field_dropdown_options_imgs',
					'cust_name' => 'cus_field_dropdown[options_imgs][' . absint($foodbakery_counter) . '][]',
					'std' => '',
					'array' => true,
					'return' => true,
				);

				$foodbakery_fields_markup .= $foodbakery_form_fields->foodbakery_img_upload_button($foodbakery_opt_array);
				$foodbakery_fields_markup .= '</div>';
				$foodbakery_fields_markup .= '
                        <img src="' . esc_url($foodbakery_Class->plugin_url() . 'assets/backend/images/add.png') . '" class="pbwp-clone-field" alt="' . foodbakery_plugin_text_srt('foodbakery_add_another') . '" style="cursor:pointer; margin:0 3px;">
                        <img src="' . esc_url($foodbakery_Class->plugin_url() . 'assets/backend/images/remove.png') . '" alt="' . foodbakery_plugin_text_srt('foodbakery_remove_this') . '" class="pbwp-remove-field" style="cursor:pointer;">
                </div>';
			}
			$foodbakery_fields_markup .= '
				</div>
			</div>';


			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'dropdown', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );
			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/**
		 * End function how to create dropdown option fields
		 */

		/**
		 * Start function how to create custom fields
		 */
		public function foodbakery_restaurant_type_reservation_pb_date($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_date_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_date_small');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[featured]',
				'title' => foodbakery_plugin_text_srt('foodbakery_restaurant_featured'),
				'std' => '',
				'options' => array( 'no' => foodbakery_plugin_text_srt('foodbakery_restaurant_no'), 'yes' => foodbakery_plugin_text_srt('foodbakery_restaurant_yes') ),
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[meta_key]',
				'class' => 'dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_textarea(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[help]',
				'title' => foodbakery_plugin_text_srt('foodbakery_help_text'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[enable_srch]',
				'title' => foodbakery_plugin_text_srt('foodbakery_enable_search'),
				'std' => '',
				'options' => array( 'no' => foodbakery_plugin_text_srt('foodbakery_restaurant_no'), 'yes' => foodbakery_plugin_text_srt('foodbakery_restaurant_yes') ),
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[date_format]',
				'title' => foodbakery_plugin_text_srt('foodbakery_date_format'),
				'std' => 'd.m.Y H:i',
				'hint' => foodbakery_plugin_text_srt('foodbakery_date_format') . ': d.m.Y H:i, Y/m/d',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[collapse_search]',
				'title' => foodbakery_plugin_text_srt('foodbakery_collapse_in_search'),
				'std' => '',
				'options' => array( 'no' => foodbakery_plugin_text_srt('foodbakery_restaurant_no'), 'yes' => foodbakery_plugin_text_srt('foodbakery_restaurant_yes') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_date[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_date',
				'name' => 'foodbakery_cus_field_reservation_date[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'date', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );
			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/**
		 * End function how to create custom fields
		 */

		/**
		 * Start function how to create custom email fields
		 */
		public function foodbakery_restaurant_type_reservation_pb_email($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_email_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_user_meta_email');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_email[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_email[label]',
                                'classes' => '',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_email[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_email[meta_key]',
                                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_email[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_email[default_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_default_value'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_email[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_email',
				'name' => 'foodbakery_cus_field_reservation_email[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'email', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );
			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/**
		 * End function how to create custom email fields
		 */

		/**
		 * Start function how to create post custom url fields
		 */
		public function foodbakery_restaurant_type_reservation_pb_url($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_url_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_url');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_url[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_url[label]',
                'classes' => '',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_url[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_url[meta_key]',
                                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_url[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_url[default_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_default_value'),
				'std' => '',
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_url[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_url',
				'name' => 'foodbakery_cus_field_reservation_url[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'url', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );
			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
				die();
		}

		/**
		 * End function how to create post custom url fields
		 */

		/**
		 * Start function how to create post custom range in fields
		 */
		public function foodbakery_restaurant_type_reservation_pb_range($die = 0, $foodbakery_return = false) {
			global $foodbakery_f_counter, $foodbakery_restaurant_type_reservation_fields, $foodbakery_plugin_static_text;

			$foodbakery_fields_markup = '';
			if ( isset($_REQUEST['counter']) ) {
				$foodbakery_counter = $_REQUEST['counter'];
			} else {
				$foodbakery_counter = $foodbakery_f_counter;
			}
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]) ) {
				$foodbakery_title = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) ? sprintf(foodbakery_plugin_text_srt('foodbakery_range_string'), $foodbakery_restaurant_type_reservation_fields[$foodbakery_counter]['label']) : '';
			} else {
				$foodbakery_title = foodbakery_plugin_text_srt('foodbakery_range_small');
			}
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[required]',
				'title' => foodbakery_plugin_text_srt('foodbakery_required'),
				'std' => 'off',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[label]',
                                'classes' => '',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_checkbox(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[enable_label]',
				'title' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable'),
				'std' => 'off',
				'hint' => foodbakery_plugin_text_srt('foodbakery_custom_field_title_enable_hint'),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[meta_key]',
                                'classes' => 'foodbakery-dev-req-field-admin dir_meta_key_field',
				'title' => foodbakery_plugin_text_srt('foodbakery_meta_key'),
				'check' => true,
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[placeholder]',
				'title' => foodbakery_plugin_text_srt('foodbakery_place_holder'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[min]',
                                'classes' => 'foodbakery-dev-req-field-admin foodbakery-number-field',
				'title' => foodbakery_plugin_text_srt('foodbakery_minimum_value'),
				'std' => '',
				'hint' => __( 'Only numbers are allowed ', 'foodbakery' ),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[max]',
                                'classes' => 'foodbakery-dev-req-field-admin foodbakery-number-field',
				'title' => foodbakery_plugin_text_srt('foodbakery_maximum_value'),
				'std' => '',
				'hint' => __( 'Only numbers are allowed ', 'foodbakery' ),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[increment]',
                                'classes' => 'foodbakery-dev-req-field-admin foodbakery-number-field',
				'title' => foodbakery_plugin_text_srt('foodbakery_increment_step'),
				'std' => '',
				'hint' => __( 'Only numbers are allowed ', 'foodbakery' ),
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_input_text(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[default_value]',
				'title' => foodbakery_plugin_text_srt('foodbakery_default_value'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields_markup .= $this->foodbakery_fields_select(array(
				'id' => '',
				'name' => 'cus_field_reservation_range[field_size]',
				'title' => foodbakery_plugin_text_srt('foodbakery_field_size'),
				'std' => '',
				'options' => array(  'large' => foodbakery_plugin_text_srt('foodbakery_large'), 'medium' => foodbakery_plugin_text_srt('foodbakery_medium'), 'small' => foodbakery_plugin_text_srt('foodbakery_small') ),
				'hint' => '',
			));

			$foodbakery_fields_markup .= $this->foodbakery_restaurant_type_fields_icon(array(
				'id' => 'fontawsome_icon_range',
				'name' => 'foodbakery_cus_field_reservation_range[fontawsome_icon]',
				'title' => foodbakery_plugin_text_srt('foodbakery_icon'),
				'std' => '',
				'hint' => '',
			));
			$foodbakery_fields = array( 'foodbakery_counter' => $foodbakery_counter, 'foodbakery_name' => 'range', 'foodbakery_title' => $foodbakery_title, 'foodbakery_markup' => $foodbakery_fields_markup );
			$foodbakery_output = $this->foodbakery_fields_layout($foodbakery_fields);
			if ( $foodbakery_return == true ) {
				return force_balance_tags($foodbakery_output, true);
			} else {
				echo force_balance_tags($foodbakery_output, true);
			}
			if ( $die <> 1 )
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
			$foodbakery_defaults = array( 'foodbakery_counter' => '1', 'foodbakery_name' => '', 'foodbakery_title' => '', 'foodbakery_markup' => '' );
			extract(shortcode_atts($foodbakery_defaults, $foodbakery_fields));

			$foodbakery_html = '<div class="pb-item-container">';
			$foodbakery_section_class = '';
			if ( $foodbakery_name == 'section' ) {
				$foodbakery_section_class = 'cs-cust-field-section';
			}
			$foodbakery_html .= '<div class="pbwp-legend ' . $foodbakery_section_class . '">';
			$foodbakery_html .= '<input type="hidden" name="foodbakery_cus_field_reservation_title[]" value="' . $foodbakery_name . '">';
			$foodbakery_html .= '<input type="hidden" name="foodbakery_cus_field_reservation_id[]" value="' . $foodbakery_counter . '">';

			if ( $foodbakery_name == 'textarea' ) {
				$foodbakery_show_icon = 'icon-message';
			} else if ( $foodbakery_name == 'dropdown' ) {
				$foodbakery_show_icon = 'icon-arrow-down';
			} else if ( $foodbakery_name == 'date' ) {
				$foodbakery_show_icon = 'icon-perm_contact_calendar';
			} else if ( $foodbakery_name == 'email' ) {
				$foodbakery_show_icon = 'icon-mail';
			} else if ( $foodbakery_name == 'url' ) {
				$foodbakery_show_icon = 'icon-link2';
			} else if ( $foodbakery_name == 'range' ) {
				$foodbakery_show_icon = 'icon-target2';
			} else if ( $foodbakery_name == 'quantity' ) {
				$foodbakery_show_icon = 'icon-stackoverflow';
			} else if ( $foodbakery_name == 'section' ) {
				$foodbakery_show_icon = 'icon-section';
			} else if ( $foodbakery_name == 'services' ) {
				$foodbakery_show_icon = 'icon-cog2';
			} else if ( $foodbakery_name == 'availability' ) {
				$foodbakery_show_icon = 'icon-calendar4';
			} else if ( $foodbakery_name == 'time' ) {
				$foodbakery_show_icon = 'icon-clock3';
			} else if ( $foodbakery_name == 'file' ) {
				$foodbakery_show_icon = 'icon-file_upload';
			} else if ( $foodbakery_name == 'number' ) {
				$foodbakery_show_icon = 'icon-file-text';
			} else {
				$foodbakery_show_icon = 'icon-new-message';
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
			global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_reservation_fields;
			$foodbakery_output = '';
			$foodbakery_output .= '<script>jQuery(document).ready(function($) {
                                    //foodbakery_check_reservation_fields_avail();
                            });</script>';
			extract($params);
			
			$set_meta_key_class = '';
			if(isset($class) && $class == 'dir_meta_key_field'){
				$set_meta_key_class = 'dir-res-meta-key-field';
			}
                        
                        $field_classes    = '';
                        if(isset( $classes ) ){
                            $field_classes  = $classes;
                        }
			
			$foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
			$foodbakery_label = str_replace(array( '[', ']' ), array( '', '' ), $foodbakery_label);
			
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]) ) {
				$foodbakery_value = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label] : '';
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			$foodbakery_rand_id = time();
			$html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
			$html_name = 'foodbakery_' . $name . '[]';
			$foodbakery_check_con = '';
			if ( isset($check) && $check == true ) {
				$html_id = 'check_field_name_' . $foodbakery_rand_id;
			}
                        
                        
                        $extra_atributes = '';
			if ( isset($extra_attr) && $extra_attr != '' ) {
				$extra_atributes = $extra_attr;
			}
			$foodbakery_output .= $foodbakery_html_fields->foodbakery_opening_field(array(
				'name' => $title,
				'hint_text' => $hint,
			));
			$foodbakery_opt_array = array(
				'id' => $id,
				'cust_id' => $html_id,
				'classes' => $set_meta_key_class. ' '.$field_classes,
				'cust_name' => $html_name,
                                'extra_atr' => $extra_atributes,
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
		 * Start function how to create post custom fields in input
		 */
		public function foodbakery_fields_checkbox($params = '') {
			global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_reservation_fields;
			$foodbakery_output = '';
			$foodbakery_output .= '<script>jQuery(document).ready(function($) {
                                    //foodbakery_check_reservation_fields_avail();
                            });</script>';
			extract($params);
			
			$set_meta_key_class = '';
			if(isset($class) && $class == 'dir_meta_key_field'){
				$set_meta_key_class = 'dir-res-meta-key-field';
			}
                        
			$field_classes    = '';
			if(isset( $classes ) ){
				$field_classes  = $classes;
			}
			
			$foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
			$foodbakery_label = str_replace(array( '[', ']' ), array( '', '' ), $foodbakery_label);
			
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]) ) {
				$foodbakery_value = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label] : '';
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			$foodbakery_rand_id = time();
			$html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
			$html_name = 'foodbakery_' . $name . '[]';
			$foodbakery_check_con = '';
			if ( isset($check) && $check == true ) {
				$html_id = 'check_field_name_' . $foodbakery_rand_id;
			}
                        
            $extra_atributes = '';
			if ( isset($extra_attr) && $extra_attr != '' ) {
				$extra_atributes = $extra_attr;
			}
			$foodbakery_output .= $foodbakery_html_fields->foodbakery_opening_field(array(
				'name' => $title,
				'hint_text' => $hint,
			));
			$foodbakery_opt_array = array(
				'id' => $id,
				'cust_id' => $html_id,
				'classes' => $set_meta_key_class. ' '.$field_classes,
				'cust_name' => $html_name,
                'extra_atr' => $extra_atributes,
				'std' => $value,
				'return' => true,
			);

			$foodbakery_output .= $foodbakery_form_fields->foodbakery_form_checkbox_render($foodbakery_opt_array);


			$foodbakery_output .= '<span class="name-checking"></span>';

			$foodbakery_output .= $foodbakery_html_fields->foodbakery_closing_field(array(
				'desc' => '',
			));
			return force_balance_tags($foodbakery_output);
		}

		/**
		 * Start function how to create post custom fields in input
		 */
		public function foodbakery_fields_file_upload($params = '') {
			global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_reservation_fields;
			$foodbakery_output = '';
			$foodbakery_output .= '<script>jQuery(document).ready(function($) {
                                    //foodbakery_check_reservation_fields_avail();
                            });</script>';
			extract($params);
			$foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
			$foodbakery_label = str_replace(array( '[', ']' ), array( '', '' ), $foodbakery_label);
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]) ) {
				$foodbakery_value = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label] : '';
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			$foodbakery_rand_id = time();
			$html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
			$html_name = 'foodbakery_' . $name . '[]';
			$foodbakery_check_con = '';
			if ( isset($check) && $check == true ) {
				$html_id = 'check_field_name_' . $foodbakery_rand_id;
			}

			$foodbakery_opt_array = array(
				'name' => $title,
				'desc' => '',
				'hint_text' => $hint,
				'id' => $html_id,
				'custom' => true,
				'field_params' => array(
					'id' => $html_id,
					'cust_name' => $html_name,
					'std' => $value,
					'return' => true,
				),
			);
			$foodbakery_output .= $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);

			$foodbakery_output .= '<span class="name-checking"></span>';


			return force_balance_tags($foodbakery_output);
		}

		/**
		 * end function how to create post custom fields in input
		 */

		/**
		 * Start function how to create post custom fields in input textarea
		 */
		public function foodbakery_fields_input_textarea($params = '') {
			global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_reservation_fields;
			$foodbakery_output = '';
			extract($params);
			$foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
			$foodbakery_label = str_replace(array( '[', ']' ), array( '', '' ), $foodbakery_label);
			$foodbakery_output .= '<script>jQuery(document).ready(function($) {
                                    //foodbakery_check_reservation_fields_avail();
                            });</script>';
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]) ) {
				$foodbakery_value = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label] : '';
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			$html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) : '';
			$html_name = 'foodbakery_' . $name . '[]';

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
		 * end function how to create post custom fields in input textarea
		 */

		/**
		 * Start function how to create post custom select fields
		 */
		public function foodbakery_fields_select($params = '') {
			global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_reservation_fields;
			$foodbakery_output = '';
			extract($params);
			$foodbakery_output .= '<script>jQuery(document).ready(function($) {
                           	  //foodbakery_check_reservation_fields_avail();
                           });
			</script>';

			$foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
			$foodbakery_label = str_replace(array( '[', ']' ), array( '', '' ), $foodbakery_label);
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]) ) {
				$foodbakery_value = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label] : '';
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			$html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
			$html_name = 'foodbakery_' . $name . '[]';
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
		public function foodbakery_restaurant_type_fields_icon($params = '') {
			global $foodbakery_f_counter, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_reservation_fields;
			$foodbakery_output = '';
			extract($params);
			$foodbakery_output .= '';
			$rand_id = rand(0, 999999);
			$foodbakery_label = substr($name, strpos($name, '['), strpos($name, ']'));
			$foodbakery_label = str_replace(array( '[', ']' ), array( '', '' ), $foodbakery_label);
			if ( isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter]) ) {
				$foodbakery_value = isset($foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label]) ? $foodbakery_restaurant_type_reservation_fields[$foodbakery_f_counter][$foodbakery_label] : '';
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			$html_id = $id != '' ? 'foodbakery_' . sanitize_html_class($id) . '' : '';
			$html_name = 'foodbakery_' . $name . '[]';
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
		public function foodbakery_save_array($foodbakery_sec_counter = 0, $foodbakery_type = '', $cus_field_array = array()) {
			$foodbakery_fields = array( 'required', 'featured', 'label', 'enable_label', 'meta_key', 'placeholder', 'enable_srch', 'default_value', 'fontawsome_icon', 'help', 'rows', 'cols', 'multi', 'post_multi', 'first_value', 'collapse_search', 'field_size', 'date_format', 'min', 'max', 'increment', 'enable_inputs', 'srch_style','services_dropdown', 'time_lapse', 'description' );
			$cus_field_array['type'] = $foodbakery_type;
			foreach ( $foodbakery_fields as $field ) {
				if ( isset($_POST["foodbakery_cus_field_reservation_{$foodbakery_type}"][$field][$foodbakery_sec_counter]) ) {
					$cus_field_array[$field] = $_POST["foodbakery_cus_field_reservation_{$foodbakery_type}"][$field][$foodbakery_sec_counter];
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
		public function foodbakery_update_form_builder_custom_fields($post_id) {
			$foodbakery_obj = new foodbakery_restaurant_type_dynamic_fields();
			$text_counter = $services_counter = $availability_counter = $number_counter = $section_counter = $time_counter = $file_counter = $textarea_counter = $dropdown_counter = $date_counter = $email_counter = $url_counter = $range_counter = $quantity_counter = $cus_field_counter = $error = 0;
			$cus_field = array();

			if ( isset($_POST['foodbakery_cus_field_reservation_id']) && sizeof($_POST['foodbakery_cus_field_reservation_id']) > 0 ) {
				foreach ( $_POST['foodbakery_cus_field_reservation_id'] as $keys => $values ) {
					if ( $values != '' ) {
						$foodbakery_rand_numb = rand(1342121, 9974532);
						$cus_field_array = array();
						$foodbakery_type = isset($_POST["foodbakery_cus_field_reservation_title"][$cus_field_counter]) ? $_POST["foodbakery_cus_field_reservation_title"][$cus_field_counter] : '';
						switch ( $foodbakery_type ) {
							case('text'):
								$cus_field_array = $this->foodbakery_save_array($text_counter, $foodbakery_type, $cus_field_array);
								$text_counter ++;
								break;
							case('services'):
								$cus_field_array = $this->foodbakery_save_array($services_counter, $foodbakery_type, $cus_field_array);
								$services_counter ++;
								break;
							case('availability'):
								$cus_field_array = $this->foodbakery_save_array($availability_counter, $foodbakery_type, $cus_field_array);
								$availability_counter ++;
								break;
							case('number'):
								$cus_field_array = $this->foodbakery_save_array($number_counter, $foodbakery_type, $cus_field_array);
								$number_counter ++;
								break;
							case('section'):
								$cus_field_array = $this->foodbakery_save_array($section_counter, $foodbakery_type, $cus_field_array);
								$section_counter ++;
								break;
							case('time'):
								$cus_field_array = $this->foodbakery_save_array($time_counter, $foodbakery_type, $cus_field_array);
								$time_counter ++;
								break;
							case('file'):
								$cus_field_array = $this->foodbakery_save_array($file_counter, $foodbakery_type, $cus_field_array);
								$file_counter ++;
								break;
							case('textarea'):
								$cus_field_array = $this->foodbakery_save_array($textarea_counter, $foodbakery_type, $cus_field_array);
								$textarea_counter ++;
								break;
							case('dropdown'):
								$cus_field_array = $this->foodbakery_save_array($dropdown_counter, $foodbakery_type, $cus_field_array);

								if ( isset($_POST["cus_field_{$foodbakery_type}"]['options_values'][$values]) && (strlen(implode($_POST["cus_field_{$foodbakery_type}"]['options_values'][$values])) != 0) ) {
									$cus_field_array['options'] = array();
									$option_counter = 0;

									foreach ( $_POST["cus_field_{$foodbakery_type}"]['options_values'][$values] as $option ) {
										if ( $option != '' ) {
											$option = ltrim(rtrim($option));
											if ( $_POST["cus_field_{$foodbakery_type}"]['options'][$values][$option_counter] != '' ) {
												$cus_field_array['options']['select'][] = isset($_POST["cus_field_{$foodbakery_type}"]['selected'][$values][$option_counter]) ? $_POST["cus_field_{$foodbakery_type}"]['selected'][$values][$option_counter] : '';
												$cus_field_array['options']['label'][] = isset($_POST["cus_field_{$foodbakery_type}"]['options'][$values][$option_counter]) ? $_POST["cus_field_{$foodbakery_type}"]['options'][$values][$option_counter] : '';
												$cus_field_array['options']['value'][] = isset($option) && $option != '' ? strtolower(str_replace(" ", "-", $option)) : '';
												$cus_field_array['options']['img'][] = isset($_POST["cus_field_{$foodbakery_type}"]['options_imgs'][$values][$option_counter]) ? $_POST["cus_field_{$foodbakery_type}"]['options_imgs'][$values][$option_counter] : '';
											}
										}
										$option_counter ++;
									}
								} else {
									$error = 1;
								}
								$dropdown_counter ++;
								break;
							case('date'):
								$cus_field_array = $this->foodbakery_save_array($date_counter, $foodbakery_type, $cus_field_array);
								$date_counter ++;
								break;
							case('email'):
								$cus_field_array = $this->foodbakery_save_array($email_counter, $foodbakery_type, $cus_field_array);
								$email_counter ++;
								break;
							case('url'):
								$cus_field_array = $this->foodbakery_save_array($url_counter, $foodbakery_type, $cus_field_array);
								$url_counter ++;
								break;
							case('range'):
								$cus_field_array = $this->foodbakery_save_array($range_counter, $foodbakery_type, $cus_field_array);
								$range_counter ++;
								break;
							case('quantity'):
								$cus_field_array = $this->foodbakery_save_array($quantity_counter, $foodbakery_type, $cus_field_array);
								$quantity_counter ++;
								break;
						}
						$cus_field[$foodbakery_rand_numb] = $cus_field_array;
						$cus_field_counter ++;
					}
				}
			}

//			
			if ( $error == 0 ) {
				update_post_meta($post_id, "foodbakery_restaurant_type_reservation_fields", $cus_field);
			}
		}

		public function foodbakery_check_reservation_fields_avail() {
			global $foodbakery_plugin_static_text;


			$foodbakery_json = array();
			$foodbakery_temp_names = array();
			$foodbakery_temp_names_1 = array();
			$foodbakery_temp_names_2 = array();
			$foodbakery_temp_names_3 = array();
			$foodbakery_temp_names_4 = array();
			$foodbakery_temp_names_5 = array();
			$foodbakery_temp_names_6 = array();
			$foodbakery_field_name = $_REQUEST['name'];
			$post_id = isset($_POST['foodbakery_cus_field_reservation_text']['meta_key']) ? $_POST['foodbakery_cus_field_reservation_text']['meta_key'] : '';
			$post_id = isset($_POST['foodbakery_cus_field_reservation_number']['meta_key']) ? $_POST['foodbakery_cus_field_reservation_number']['meta_key'] : '';
			$foodbakery_restaurant_type_reservation_fields = get_post_meta($post_id, "foodbakery_restaurant_type_reservation_fields", true);
			$form_field_names = isset($_REQUEST['foodbakery_cus_field_reservation_text']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_text']['meta_key'] : array();
			$form_field_names_0 = isset($_REQUEST['foodbakery_cus_field_reservation_number']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_number']['meta_key'] : array();
			$form_field_names_1 = isset($_REQUEST['foodbakery_cus_field_reservation_textarea']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_textarea']['meta_key'] : array();
			$form_field_names_2 = isset($_REQUEST['foodbakery_cus_field_reservation_dropdown']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_dropdown']['meta_key'] : array();
			$form_field_names_3 = isset($_REQUEST['foodbakery_cus_field_reservation_date']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_date']['meta_key'] : array();
			$form_field_names_4 = isset($_REQUEST['foodbakery_cus_field_reservation_email']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_email']['meta_key'] : array();
			$form_field_names_5 = isset($_REQUEST['foodbakery_cus_field_reservation_url']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_url']['meta_key'] : array();
			$form_field_names_6 = isset($_REQUEST['foodbakery_cus_field_reservation_range']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_range']['meta_key'] : array();
			$form_field_names_7 = isset($_REQUEST['foodbakery_cus_field_reservation_quantity']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_quantity']['meta_key'] : array();
			$form_field_names_8 = isset($_REQUEST['foodbakery_cus_field_reservation_availability']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_availability']['meta_key'] : array();
			$form_field_names_9 = isset($_REQUEST['foodbakery_cus_field_reservation_file']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_file']['meta_key'] : array();
			$form_field_names_10 = isset($_REQUEST['foodbakery_cus_field_reservation_time']['meta_key']) ? $_REQUEST['foodbakery_cus_field_reservation_time']['meta_key'] : array();
			$form_field_names = array_merge($form_field_names, $form_field_names_0, $form_field_names_1, $form_field_names_2, $form_field_names_3, $form_field_names_4, $form_field_names_5, $form_field_names_6, $form_field_names_7, $form_field_names_8, $form_field_names_9, $form_field_names_10);
			$length = count(array_keys($form_field_names, $foodbakery_field_name));
			if ( $foodbakery_field_name == '' ) {
				$foodbakery_json['type'] = 'error';
				$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_field_name_required');
			} else {
				if ( is_array($foodbakery_restaurant_type_reservation_fields) && sizeof($foodbakery_restaurant_type_reservation_fields) > 0 ) {
					$success = 1;
					foreach ( $foodbakery_restaurant_type_reservation_fields as $field_key => $foodbakery_field ) {
						if ( isset($foodbakery_field['type']) ) {
							if ( preg_match('/\s/', $foodbakery_field_name) ) {
								$foodbakery_json['type'] = 'error';
								$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_whitespaces_not_allowed');
								echo json_encode($foodbakery_json);
								die();
							}
							if ( preg_match('/[\'^�$%&*()}{@#~?><>,|=+�]/', $foodbakery_field_name) ) {
								$foodbakery_json['type'] = 'error';
								$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_special_characters_not_allowed');
								echo json_encode($foodbakery_json);
								die();
							}
							if ( trim($foodbakery_field['type']) == trim($foodbakery_field_name) ) {

								if ( in_array(trim($foodbakery_field_name), $form_field_names) && $length > 1 ) {
									$foodbakery_json['type'] = 'error';
									$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_name_already_exists');
									echo json_encode($foodbakery_json);
									die();
								}
							} else {
								if ( in_array(trim($foodbakery_field_name), $form_field_names) && $length > 1 ) {
									$foodbakery_json['type'] = 'error';
									$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_name_already_exists');
									echo json_encode($foodbakery_json);
									die();
								}
							}
						}
					}
					$foodbakery_json['type'] = 'success';
					$foodbakery_json['message'] = '<i class="icon-checkmark6"></i> ' . foodbakery_plugin_text_srt('foodbakery_restaurant_custom_name_available');
				} else {
					if ( preg_match('/\s/', $foodbakery_field_name) ) {
						$foodbakery_json['type'] = 'error';
						$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_whitespaces_not_allowed');
						echo json_encode($foodbakery_json);
						die();
					}
					if ( preg_match('/[\'^�$%&*()}{@#~?><>,|=+]/', $foodbakery_field_name) ) {
						$foodbakery_json['type'] = 'error';
						$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_special_characters_not_allowed');
						echo json_encode($foodbakery_json);
						die();
					}
					if ( in_array(trim($foodbakery_field_name), $form_field_names) && $length > 1 ) {
						$foodbakery_json['type'] = 'error';
						$foodbakery_json['message'] = '<i class="icon-times"></i> ' . foodbakery_plugin_text_srt('foodbakery_name_already_exists');
					} else {
						$foodbakery_json['type'] = 'success';
						$foodbakery_json['message'] = '<i class="icon-checkmark6"></i> ' . foodbakery_plugin_text_srt('foodbakery_restaurant_custom_name_available');
					}
				}
			}
			echo json_encode($foodbakery_json);
			die();
		}

	}

	global $foodbakery_restaurant_type_form_builder_fields;

	$foodbakery_restaurant_type_form_builder_fields = new Foodbakery_Restaurant_Type_Form_Builder_Dynamic_Fields();
}