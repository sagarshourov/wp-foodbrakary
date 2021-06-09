<?php
/**
 * File Type: Booking Page Element
 */
if (!class_exists('foodbakery_booking_element')) {

    class foodbakery_booking_element {

	/**
	 * Start construct Functions
	 */
	public function __construct() {
	    add_action('wp_enqueue_scripts', array($this, 'foodbakery_booking_element_scripts'), 11);
	    add_action('foodbakery_booking_element_html', array($this, 'foodbakery_booking_element_html_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_booking_submit', array($this, 'foodbakery_booking_submit_callback'));
	    add_action('wp_ajax_nopriv_foodbakery_booking_submit', array($this, 'foodbakery_booking_submit_callback'));
	    add_action('wp_ajax_foodbakery_available_restaurant_time', array($this, 'foodbakery_available_restaurant_time_callback'));
	    add_action('wp_ajax_nopriv_foodbakery_available_restaurant_time', array($this, 'foodbakery_available_restaurant_time_callback'));
	}

	public function foodbakery_booking_element_scripts() {
	    wp_enqueue_style('bootstrap-datepicker');
	    wp_enqueue_script('bootstrap-datepicker');
	    wp_enqueue_script('foodbakery-booking-functions');
	}

	public function foodbakery_booking_element_html_callback($restaurant_id) {
	    global $foodbakery_form_fields, $foodbakery_plugin_options;

	    $restaurant_type_id = foodbakery_restaurant_type_id();

	    // Restaurant & Booking users ID's
	    $restaurant_publisher = get_post_meta($restaurant_id, 'foodbakery_restaurant_publisher', true);
	    $restaurant_user = foodbakery_user_id_form_company_id($restaurant_publisher);
	    $booking_user = $booking_publisher = 0;
	    $booking_user = get_current_user_id();
	    if ($booking_user != 0) {
		$booking_publisher = get_user_meta($booking_user, 'foodbakery_company', true);
	    }

	    $form_button_label = get_post_meta($restaurant_type_id, "foodbakery_form_button_label", true);
	    $form_button_label = isset($form_button_label) && $form_button_label != '' ? $form_button_label : esc_html__('Book a table', 'foodbakery');
	    $booking_fields = get_post_meta($restaurant_type_id, "foodbakery_restaurant_type_reservation_fields", true);

	    if (!empty($booking_fields) && sizeof($booking_fields) > 0) {
		?>
		<div class="booking-info-sec">
		    <form name="booking-form" id="booking-form" class="booking-form" method="post">
			<div class="row">
			    <div class="booking-info">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				    <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h5><?php echo esc_html__('Book This Restaurant','foodbakery');?></h5>
                    <p class="booking-desc"><?php echo esc_html__('All kinds of dining experiences are waiting to be discovered. Check out the best restaurants and Book Using following Form.','foodbakery');?></p>
                    </div>
					<?php
					foreach ($booking_fields as $booking_field) {
					    $booking_field_type = isset($booking_field['type']) ? $booking_field['type'] : '';
					    if ($booking_field_type == 'section') {
						echo force_balance_tags($this->foodbakery_section_field($booking_field));
					    } else {
						echo force_balance_tags($this->foodbakery_common_field($booking_field, $restaurant_id));
					    }
					}
					?>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					    <div class="field-holder">
						<div class="submit-btn">
						    <?php
						    // Restaurant ID
						    $foodbakery_opt_array = array();
						    $foodbakery_opt_array['std'] = intval($restaurant_id);
						    $foodbakery_opt_array['id'] = 'restaurant_id';
						    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

						    // Restaurant Type ID
						    $foodbakery_opt_array = array();
						    $foodbakery_opt_array['std'] = intval($restaurant_type_id);
						    $foodbakery_opt_array['id'] = 'restaurant_type_id';
						    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

						    // Restaurant Publisher ID
						    $foodbakery_opt_array = array();
						    $foodbakery_opt_array['std'] = intval($restaurant_publisher);
						    $foodbakery_opt_array['id'] = 'restaurant_publisher';
						    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

						    // Restaurant User ID
						    $foodbakery_opt_array = array();
						    $foodbakery_opt_array['std'] = intval($restaurant_user);
						    $foodbakery_opt_array['id'] = 'restaurant_user';
						    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

						    // Booking Publisher ID
						    $foodbakery_opt_array = array();
						    $foodbakery_opt_array['std'] = intval($booking_publisher);
						    $foodbakery_opt_array['id'] = 'booking_publisher';
						    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

						    // Booking User ID
						    $foodbakery_opt_array = array();
						    $foodbakery_opt_array['std'] = intval($booking_user);
						    $foodbakery_opt_array['id'] = 'booking_user';
						    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

						    // Submit Button
						    $foodbakery_opt_array = array();
						    $foodbakery_opt_array['std'] = $form_button_label;
						    $foodbakery_opt_array['cust_id'] = 'submit_booking';
						    $foodbakery_opt_array['cust_name'] = 'submit_booking';
						    $foodbakery_opt_array['cust_type'] = 'submit';
						    $foodbakery_opt_array['classes'] = 'input-field';
						    $foodbakery_opt_array['extra_atr'] = ' onclick="javascript:foodbakery_booking_submit()"';
						    
						    ?>
						    <button type="button" class="field-btn bgcolor booking-submit-btn input-button-loader" onclick="javascript:foodbakery_booking_submit();"><?php echo esc_html($form_button_label); ?></button>
						    <span class="booking-loader"></span>
						</div>
					    </div>
					</div>
				    </div>
				</div>
			    </div>
			</div>
		    </form>
		</div>
		<?php
	    }
	}

	public function foodbakery_section_field($booking_field = '') {
	    $field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
	    $field_dec = isset($booking_field['description']) ? $booking_field['description'] : '';
	    $output = '';
	    if ($field_label != '' || $field_dec != '') {
		$output .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		if ($field_label != '') {
		    $output .= '<div class="element-title">';
		    $output .= '<h5>' . $field_label . '</h5>';
		    $output .= '</div>';
		}
		if ($field_dec != '') {
		    $output .= apply_filters('the_content', $field_dec);
		}
		$output .= '</div>';
	    }
	    return $output;
	}

	public function foodbakery_common_field($booking_field = '', $restaurant_id = '') {   // $restaurant_id only required for date available check
	    global $foodbakery_form_fields;
	    $field_type = isset($booking_field['type']) ? $booking_field['type'] : '';
	    $field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
	    $enable_label = isset($booking_field['enable_label']) ? $booking_field['enable_label'] : '';
	    $field_meta_key = isset($booking_field['meta_key']) ? $booking_field['meta_key'] : '';
	    $field_placeholder = isset($booking_field['placeholder']) ? $booking_field['placeholder'] : '';
	    $field_default_value = isset($booking_field['default_value']) ? $booking_field['default_value'] : '';
	    $field_size = isset($booking_field['field_size']) ? $booking_field['field_size'] : '';
	    $field_fontawsome_icon = isset($booking_field['fontawsome_icon']) ? $booking_field['fontawsome_icon'] : '';
	    $field_required = isset($booking_field['required']) ? $booking_field['required'] : '';

	    $output = '';

	    if ($field_meta_key != '') {

		// Field Options
		$foodbakery_opt_array = array();
		$foodbakery_opt_array['std'] = esc_attr($field_default_value);
		$foodbakery_opt_array['label'] = $field_label;
		$foodbakery_opt_array['cust_id'] = $field_meta_key;
		$foodbakery_opt_array['cust_name'] = $field_meta_key;
		$foodbakery_opt_array['extra_atr'] = $this->foodbakery_field_placeholder($field_placeholder);
		$foodbakery_opt_array['classes'] = 'input-field';
		$foodbakery_opt_array['return'] = true;
		// End Field Options

		$field_size = $this->foodbakery_field_size($field_size);

		if ($field_type == 'availability') {
		    $output .= $this->foodbakery_availability_field_date($booking_field, $foodbakery_opt_array, $restaurant_id);
		}

		if ($field_type != 'availability') {
		    $output .= '<div class="col-lg-' . $field_size . ' col-md-' . $field_size . ' col-sm-12 col-xs-12">';
		}

		$field_icon = $this->foodbakery_field_icon($field_fontawsome_icon);
		$textarea_class = $has_icon_class = '';
		if ($field_icon != '') {
		    $has_icon_class = ' has-icon';
		}
		if ($field_type == 'textarea') {
		    $textarea_class = ' field-textarea';
		}

		if ($field_type != 'availability') {
		    $output .= '<div class="field-holder' . $has_icon_class . $textarea_class . '">';
		    if ($enable_label == 'on') {
			// Field Label 
			$output .= $this->foodbakery_field_label($field_label);
		    }
		    // Field Icon
		    $output .= $field_icon;
		}

		// Making Field with defined options
		if ($field_type == 'text' || $field_type == 'url' || $field_type == 'number') {
		    if ($field_required == 'on') {
			$foodbakery_opt_array['classes'] = 'input-field foodbakery-dev-req-field';
		    }
		    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
		} elseif ($field_type == 'email') {
		    if ($field_required == 'on') {
			$foodbakery_opt_array['classes'] = 'input-field foodbakery-email-field foodbakery-dev-req-field';
		    }
		    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
		} elseif ($field_type == 'textarea') {
		    $output .= $foodbakery_form_fields->foodbakery_form_textarea_render($foodbakery_opt_array);
		} elseif ($field_type == 'dropdown') {
		    $output .= $this->foodbakery_dropdown_field($booking_field, $foodbakery_opt_array);
		} elseif ($field_type == 'range') {
		    $output .= $this->foodbakery_range_field($booking_field, $foodbakery_opt_array);
		} elseif ($field_type == 'time') {
		    
		} elseif ($field_type == 'availability') {

		    $output .= $this->foodbakery_availability_field_time($booking_field, $foodbakery_opt_array, $restaurant_id);
		}

		if ($field_type != 'availability') {
		    $output .= '</div>';
		    $output .= '</div>';
		}
	    }
	    return $output;
	}

	public function foodbakery_dropdown_field($booking_field = '', $foodbakery_opt_array = '') {
	    global $foodbakery_form_fields;
	    $output = '';
	    if (!empty($foodbakery_opt_array)) {
		$drop_down_options = array();
		if (isset($booking_field['options']) && !empty($booking_field['options'])) {
		    $first_value = isset($booking_field['first_value']) ? $booking_field['first_value'] : '';
		    if ($first_value != '') {
			$drop_down_options[''] = esc_html($first_value);
		    }
		    foreach ($booking_field['options']['label'] as $key => $value) {
			$drop_down_options[esc_html($booking_field['options']['value'][$key])] = esc_html($value);
		    }
		}
		$foodbakery_opt_array['options'] = $drop_down_options;
		$required_value = isset($booking_field['required']) ? $booking_field['required'] : '';
		$foodbakery_opt_array['classes'] = 'chosen-select';
		if ($required_value != '') {
		    $foodbakery_opt_array['classes'] = 'chosen-select foodbakery-dev-req-field';
		}
		$output .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
	    }
	    return $output;
	}

	public function foodbakery_range_field($booking_field = '', $foodbakery_opt_array = '') {
	    global $foodbakery_form_fields;
	    $output = '';
	    if (!empty($foodbakery_opt_array)) {
		$drop_down_options = array();
		$min_val = isset($booking_field['min']) ? $booking_field['min'] : '1';
		$max_val = isset($booking_field['max']) ? $booking_field['max'] : '10';
		$increment = isset($booking_field['increment']) ? $booking_field['increment'] : '1';
		while ($min_val <= $max_val) {
		    $drop_down_options[intval($min_val)] = intval($min_val);
		    $min_val = $min_val + $increment;
		}
		$foodbakery_opt_array['options'] = $drop_down_options;
		$foodbakery_opt_array['classes'] = 'chosen-select';
		$output .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
	    }
	    return $output;
	}

	public function foodbakery_time_field($booking_field = '', $foodbakery_opt_array = '') {
	    global $foodbakery_form_fields;
	    $output = '';
	    if (!empty($foodbakery_opt_array)) {
		$drop_down_options = array();
		$time_lapse = isset($booking_field['time_lapse']) ? $booking_field['time_lapse'] : '15';
		$time_list = $this->restaurant_time_list($time_lapse);
		if (is_array($time_list) && sizeof($time_list) > 0) {
		    foreach ($time_list as $time_key => $time_val) {
			$drop_down_options[$time_key] = esc_html($time_val);
		    }
		}
		$foodbakery_opt_array['options'] = $drop_down_options;
		$foodbakery_opt_array['classes'] = 'chosen-select';
		$output .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
	    }
	    return $output;
	}

	public function foodbakery_availability_field_date($booking_field = '', $foodbakery_opt_array = '', $restaurant_id = '') {
	    global $post, $foodbakery_form_fields;
	    $rand_num = rand(12345, 54321);
	    $off_days = $this->foodbakery_off_opening_days_callback($restaurant_id);
	    $field_meta_key = isset($booking_field['meta_key']) ? $booking_field['meta_key'] : '';
	    $enable_label = isset($booking_field['enable_label']) ? $booking_field['enable_label'] : '';
	    $field_fontawsome_icon = isset($booking_field['fontawsome_icon']) ? $booking_field['fontawsome_icon'] : '';
	    $field_meta_key = str_replace('-', '_', $field_meta_key);
	    $output = '';
	    $has_icon = '';
	    if ($field_fontawsome_icon != '') {
		$has_icon = 'has-icon';
	    }

	    if (!empty($foodbakery_opt_array)) {

		$foodbakery_opt_array['classes'] = 'form-control booking-date foodbakery-required-field';
		$output .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
		$output .= '<div class="field-holder ' . $has_icon . '">';
		if ($enable_label == 'on') {
		    $output .= $this->foodbakery_field_label($foodbakery_opt_array['label']);
		}
		$output .= '<div class="date-sec">';
		$output .= '<i class="' . $field_fontawsome_icon . '"> </i>';

		wp_enqueue_style('directory_datepicker_css');
		$output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
		$output .= '<div id="datepicker_' . $restaurant_id . '" class="reservaion-calendar"></div>
                                                
					<script type="text/javascript">
						jQuery( document ).ready(function() {
							var disabledDays = [""];
							
							jQuery("#datepicker_' . $restaurant_id . '").datepicker({
								showOtherMonths: true,
								firstDay: 1,
								minDate: 0,
								dateFormat: "dd-mm-yy",
								prevText: "",
								nextText: "",
								monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
								beforeShowDay: function (date) {
									var day = date.getDay();
									var string = jQuery.datepicker.formatDate("dd-mm-yy", date);
									var isDisabled = (jQuery.inArray(string, disabledDays) != -1);
									//day != 0 disables all Sundays
									return [' . $off_days . ' !isDisabled];
								},
								onSelect: function (date) { 
									jQuery("#' . $foodbakery_opt_array['cust_id'] . '").val(date);
									load_available_time(date, \'' . $restaurant_id . '\');
								}
							});
						});
					</script>
					<ul class="calendar-options">
						<li class="avilable">' . esc_html__('Available', 'foodbakery') . '</li>
						<li class="unavailable">' . esc_html__('Unavailable', 'foodbakery') . '</li> 
					</ul>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
	    }
	    return $output;
	}

	public function foodbakery_availability_field_time($booking_field = '', $foodbakery_opt_array = '', $restaurant_id = '') {
	    global $post, $foodbakery_form_fields;
	    $rand_num = rand(12345, 54321);
	    $off_days = $this->foodbakery_off_opening_days_callback($restaurant_id);
	    $field_meta_key = isset($booking_field['meta_key']) ? $booking_field['meta_key'] : '';
	    $enable_label = isset($booking_field['enable_label']) ? $booking_field['enable_label'] : '';
	    $field_meta_key = str_replace('-', '_', $field_meta_key);
	    $output = '';
	    if (!empty($foodbakery_opt_array)) {

		$foodbakery_opt_array['classes'] = 'form-control';
		// time field

		$foodbakery_opt_array['classes'] = 'chosen-select foodbakery-required-field';
		$foodbakery_time_field_id = 'time-' . $foodbakery_opt_array['cust_id'];
		$foodbakery_time_field_name = 'time-' . $foodbakery_opt_array['cust_name'];
		$foodbakery_opt_array['cust_id'] = $foodbakery_time_field_id;
		$foodbakery_opt_array['cust_name'] = $foodbakery_time_field_name;

		$output .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
		$output .= '<div class="field-holder has-icon">';
		if ($enable_label == 'on') {
		    $output .= '<label>' . esc_html__('Booking Time', 'foodbakery') . '</label>';
		}
		$output .= '<div class="booking_time_wrapper">';
		$output .= '<div id="time-div-' . $foodbakery_time_field_id . '">';
		$output .= '<i class="icon-clock-o"></i>';
		$drop_down_options = array();
		$time_lapse = isset($booking_field['time_lapse']) ? $booking_field['time_lapse'] : '15';
		$time_list = $this->restaurant_time_list($time_lapse);
		if (is_array($time_list) && sizeof($time_list) > 0) {
		    foreach ($time_list as $time_key => $time_val) {
			$drop_down_options[$time_key] = esc_html($time_val);
		    }
		}
		$foodbakery_opt_array['options'] = $drop_down_options;
		$output .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<script type="text/javascript">                     
					function load_available_time(date_field, restaurant_id){
						var data = "date_field=" + date_field + "&restaurant_id=" + restaurant_id+ "&action=foodbakery_available_restaurant_time&field_id=' . $foodbakery_time_field_id . '&field_name=' . $foodbakery_time_field_name . '" ;
						jQuery("#time-div-' . $foodbakery_time_field_id . '").html("<i class=\"icon-spinner\"></i>");
						jQuery("#time-div-' . $foodbakery_time_field_id . '").addClass("time-loading");
						jQuery.ajax({
							type: "POST",
							url: foodbakery_globals.ajax_url,
							dataType: "json",
							data: data ,
							success: function (response) {
							jQuery("#time-div-' . $foodbakery_time_field_id . '").removeClass("time-loading");
//                                                    if(response.status == "false"){ 
//                                                        alert("' . esc_html__('Restaurant not opened this day, Please choose another day', 'foodbakery') . '");
//                                                    }
							jQuery("#time-div-' . $foodbakery_time_field_id . '").html(response.html);
							   if (jQuery(".chosen-select, .chosen-select-deselect, .chosen-select-no-single, .chosen-select-no-results, .chosen-select-width").length != "") {
									var config = {
										".chosen-select": {width: "100%"},
										".chosen-select-deselect": {allow_single_deselect: true},
										".chosen-select-no-single": {disable_search_threshold: 4, width: "100%"},
										".chosen-select-no-results": {no_results_text: "Oops, nothing found!"},
										".chosen-select-width": {width: "95%"}
									}
									for (var selector in config) {
										jQuery(selector).chosen(config[selector]);
									}
								}
							}
						});
					} 
				</script>';
	    }
	    return $output;
	}

	public function foodbakery_available_restaurant_time_callback() {
	    global $foodbakery_form_fields;
	    $json = array();

	    $restaurant_id = $_REQUEST['restaurant_id'];
	    $date = $_REQUEST['date_field'];
	    $field_id = $_REQUEST['field_id'];
	    $field_name = $_REQUEST['field_name'];
	    $timestamp = strtotime($date);
		$selected_day = strtolower(date('l', $timestamp));
	    $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
	    $opening_hours_list = array();
	    foreach ($days as $key => $day) {
		$opening_time = get_post_meta($restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true);
		$opening_time = ( $opening_time != '' ? date('h:i a', $opening_time) : '' );
		$closing_time = get_post_meta($restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true);
		$closing_time = ( $opening_time != '' ? date('h:i a', $closing_time) : '' );
		$opening_hours_list[$day] = array(
		    'day_status' => get_post_meta($restaurant_id, 'foodbakery_opening_hours_' . $day . '_day_status', true),
		    'opening_time' => $opening_time,
		    'closing_time' => $closing_time,
		);
	    }

	    $foodbakery_opt_array = array(
		'std' => '',
		'cust_id' => $field_id,
		'cust_name' => $field_name,
		'classes' => 'chosen-select input-field',
		'options' => array('' => esc_html__('Not found', 'foodbakery')),
		'return' => true,
	    );
	    $flag = 0;
		if (isset($opening_hours_list) && is_array($opening_hours_list) && !empty($opening_hours_list)) {
		if (isset($opening_hours_list[$selected_day]) && is_array($opening_hours_list[$selected_day]) && !empty($opening_hours_list[$selected_day])) {

		    if ($opening_hours_list[$selected_day]['day_status'] == 'on') {
			$options = $this->restaurant_time_list('15', $opening_hours_list[$selected_day]['opening_time'], $opening_hours_list[$selected_day]['closing_time']);
			// Field Options
			$foodbakery_opt_array['options'] = $options;
			

			$json['status'] = 'true';
			$json['html'] = '<i class="icon-clock-o"></i>' . $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
			$flag = 1;
		    }
		}
	    }
	    if ($flag == 0) {
		$json['status'] = 'false';
		$json['html'] = $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
	    }
	    echo json_encode($json);
	    wp_die();
	}

	public function foodbakery_off_opening_days_callback($post_id) {
	    $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
	    $opening_hours_list = array();
	    foreach ($days as $key => $day) {
		$opening_time = get_post_meta($post_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true);
		$opening_time = ( $opening_time != '' ? date('h:i a', $opening_time) : '' );
		$closing_time = get_post_meta($post_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true);
		$closing_time = ( $opening_time != '' ? date('h:i a', $closing_time) : '' );
		$opening_hours_list[$day] = array(
		    'day_status' => get_post_meta($post_id, 'foodbakery_opening_hours_' . $day . '_day_status', true),
		    'opening_time' => $opening_time,
		    'closing_time' => $closing_time,
		);
	    }

	    $off_days = '';
	    if (is_array($opening_hours_list) && !empty($opening_hours_list))
		foreach ($opening_hours_list as $key => $value) {
		    if (isset($value['day_status']) && $value['day_status'] != 'on') {
			if (isset($days[$key])) {
			    $off_days .= ' day != ' . $days[$key] . ' && ';
			}
		    }
		}
	    return $off_days;
	}

	public function foodbakery_field_size($field_size) {
	    switch ($field_size) {
		case "small":
		    $col_size = '4';
		    break;
		case "medium":
		    $col_size = '6';
		    break;
		case "large":
		    $col_size = '12';
		    break;
		default :
		    $col_size = '12';
		    break;
	    }
	    return $col_size;
	}

	public function foodbakery_field_label($field_label) {
	    $output = '';
	    if ($field_label != '') {
		$output .= '<label>' . $field_label . '</label>';
	    }
	    return $output;
	}

	public function foodbakery_field_icon($field_fontawsome_icon) {
	    $output = '';
	    if ($field_fontawsome_icon != '') {
		$output .= '<i class="icon ' . $field_fontawsome_icon . '"></i>';
	    }
	    return $output;
	}

	public function foodbakery_field_placeholder($field_placeholder) {
	    $placeholder = '';
	    if ($field_placeholder != '') {
		$placeholder .= 'placeholder="' . $field_placeholder . '"';
	    }
	    return $placeholder;
	}

	public function restaurant_time_list($lapse = 15, $start = '12:00AM', $end = '11:59PM') {
	    $hours = array();
	    $interval = '+' . $lapse . ' minutes';
		$start_str = strtotime(date('d-m-Y').' '.$start);
		$end_str = strtotime(date('d-m-Y').' '.$end);
		if( $start_str > $end_str){
			$end_str = strtotime('+1 day', $end_str);
		}
		$now_str = $start_str;
		while ($now_str <= $end_str) {
			$hours[strtotime(date('h:i A', $now_str))] = date('h:i A', $now_str);
			$now_str = strtotime($interval, $now_str);
		}
		return $hours;
	}
	
        public function foodbakery_booking_submit_callback() {

        if (!is_user_logged_in()) {
            $json['type'] = "error";
            $json['msg'] = esc_html__("Please Login as Buyer to Place an Order.", "foodbakery");
            echo json_encode($json);
            wp_die();
        }

	    $restaurant_id = foodbakery_get_input('foodbakery_restaurant_id', 0);
	    $restaurant_type_id = foodbakery_get_input('foodbakery_restaurant_type_id', 0);
	    $booking_fields = get_post_meta($restaurant_type_id, "foodbakery_restaurant_type_reservation_fields", true);

	    $restaurant_publisher = foodbakery_get_input('foodbakery_restaurant_publisher', 0);
	    $restaurant_user = foodbakery_get_input('foodbakery_restaurant_user', 0);
	    $booking_publisher = foodbakery_get_input('foodbakery_booking_publisher', 0);
	    $booking_user = foodbakery_get_input('foodbakery_booking_user', 0);

		if ($restaurant_publisher == $booking_publisher) {
			$json['type'] = "error";
			$json['msg'] = esc_html__(" Sorry! You can't send booking on your own restaurant.", "direcory");
			echo json_encode($json);
			exit();
	    }

	    $this->booking_form_field_validation($booking_fields);
	    // Insert Booking
	    $restaurant_title = get_the_title($restaurant_id);
	    $date = strtotime(date('d-m-y'));
	    $booking_post = array(
		'post_title' => wp_strip_all_tags($restaurant_title),
		'post_content' => '',
		'post_status' => 'publish',
		'post_type' => 'orders_inquiries',
		'post_date' => current_time('Y-m-d H:i:s')
	    );
	    $booking_id = wp_insert_post($booking_post);
	    $my_post = array(
		'ID' => $booking_id,
		'post_title' => 'booking-' . $booking_id,
		'post_name' => 'booking-' . $booking_id,
	    );
	    wp_update_post($my_post);

	    // insert Order/inquiry meta keys
	    foreach ($_POST as $key => $value) {
		update_post_meta($booking_id, $key, $value);
	    }

	    update_post_meta($booking_id, 'foodbakery_publisher_id', $restaurant_publisher);
	    update_post_meta($booking_id, 'foodbakery_order_user', $booking_publisher);

	    update_post_meta($booking_id, 'foodbakery_order_form_fields', $booking_fields);
	    update_post_meta($booking_id, 'foodbakery_order_status', 'Processing');
	    update_post_meta($booking_id, 'foodbakery_order_type', 'inquiry');
	    update_post_meta($booking_id, 'buyer_read_status', '0');
	    update_post_meta($booking_id, 'seller_read_status', '0');
	    //
	    if ($booking_id) {

		$user_name = get_the_title($booking_publisher);
		/*
		 * Adding Notification
		 */
		$notification_array = array(
		    'type' => 'reservation',
		    'element_id' => $restaurant_id,
		    'message' => __($user_name . ' submitted a booking form on your restaurant <a href="' . get_the_permalink($restaurant_id) . '">' . wp_trim_words(get_the_title($restaurant_id), 5) . '</a> .', 'foodbakery'),
		);
		do_action('foodbakery_add_notification', $notification_array);

		do_action('foodbakery_sent_booking_email', $booking_id);
		do_action('foodbakery_received_booking_email', $booking_id);

		$json['type'] = "success";
		$json['msg'] = esc_html__("Your booking has been sent successfully.", "foodbakery");
	    } else {
		$json['type'] = "error";
		$json['msg'] = esc_html__("Something went wrong, booking could not be processed.", "foodbakery");
	    }
	    echo json_encode($json);
	    wp_die();
	}

	public function booking_form_field_validation($booking_fields) {
	    if (!empty($booking_fields)) {
		foreach ($booking_fields as $booking_field) {

		    $field_type = isset($booking_field['type']) ? $booking_field['type'] : '';
		    $required = isset($booking_field['required']) ? $booking_field['required'] : '';
		    $meta_key = isset($booking_field['meta_key']) ? $booking_field['meta_key'] : '';

		    if ($required == 'on' && $_POST[$meta_key] == '') {
			$json['type'] = "error";
			$json['msg'] = esc_html__(" Please fill all required fields.", "direcory");
			echo json_encode($json);
			exit();
		    } else if ($required == 'on' && $field_type == 'email' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $_POST[$meta_key])) {
			$json['type'] = "error";
			$json['msg'] = esc_html__(" Please enter a valid email address.", "direcory");
			echo json_encode($json);
			exit();
		    }
		}
	    }
	}

    }

    global $foodbakery_booking_element;
    $foodbakery_booking_element = new foodbakery_booking_element();
}