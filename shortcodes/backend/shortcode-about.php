<?php
/**
 * Shortcode Name : About Info
 *
 * @package	foodbakery 
 */
if (!function_exists('foodbakery_var_page_builder_about_info')) {

    function foodbakery_var_page_builder_about_info($die = 0) {
	global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
	if (function_exists('foodbakery_shortcode_names')) {
	    $shortcode_element = '';
	    $filter_element = 'filterdrag';
	    $shortcode_view = '';
	    $foodbakery_output = array();
	    $foodbakery_PREFIX = 'about_info';

	    $foodbakery_counter = isset($_POST['counter']) ? $_POST['counter'] : '';
	    if (isset($_POST['action']) && !isset($_POST['shortcode_element_id'])) {
		$foodbakery_POSTID = '';
		$shortcode_element_id = '';
	    } else {
		$foodbakery_POSTID = isset($_POST['POSTID']) ? $_POST['POSTID'] : '';
		$shortcode_element_id = isset($_POST['shortcode_element_id']) ? $_POST['shortcode_element_id'] : '';
		$shortcode_str = stripslashes($shortcode_element_id);
		$parseObject = new ShortcodeParse();
		$foodbakery_output = $parseObject->foodbakery_shortcodes($foodbakery_output, $shortcode_str, true, $foodbakery_PREFIX);
	    }
	    $defaults = array(
		'about_info_time' => '',
		'about_info_phone' => '',
		'about_info_location' => '',
	    );
	    if (isset($foodbakery_output['0']['atts'])) {
		$atts = $foodbakery_output['0']['atts'];
	    } else {
		$atts = array();
	    }
	    if (isset($foodbakery_output['0']['content'])) {
		$about_info_column_text = $foodbakery_output['0']['content'];
	    } else {
		$about_info_column_text = '';
	    }
	    $about_info_element_size = '100';
	    foreach ($defaults as $key => $values) {
		if (isset($atts[$key])) {
		    $$key = $atts[$key];
		} else {
		    $$key = $values;
		}
	    }
	    $name = 'foodbakery_var_page_builder_about_info';
	    $coloumn_class = 'column_' . $about_info_element_size;
	    if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
		$shortcode_element = 'shortcode_element_class';
		$shortcode_view = 'cs-pbwp-shortcode';
		$filter_element = 'ajax-drag';
		$coloumn_class = '';
	    }
	    ?>

	    <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
		 <?php echo esc_attr($shortcode_view); ?>" item="about_info" data="<?php echo foodbakery_element_size_data_array_index($about_info_element_size) ?>" >
		     <?php foodbakery_element_setting($name, $foodbakery_counter, $about_info_element_size) ?>
	        <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
		     <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>" data-shortcode-template="[about_info {{attributes}}]{{content}}[/about_info]" style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr($foodbakery_counter) ?>">
                        <h5><?php echo esc_html__("About Info Options", "foodbakery"); ?></h5>
                        <a href="javascript:foodbakery_frame_removeoverlay('<?php echo esc_js($name . $foodbakery_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose">
                            <i class="icon-times"></i>
                        </a>
                    </div>
                    <div class="cs-pbwp-content">
                        <div class="cs-wrapp-clone cs-shortcode-wrapp">
                            <?php
                            if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                                foodbakery_shortcode_element_size();
                            }

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Time', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter time here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $about_info_time,
                                    'id' => 'about_info_time',
                                    'cust_name' => 'about_info_time[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Phone Number', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter phone number here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $about_info_phone,
                                    'id' => 'about_info_phone',
                                    'cust_name' => 'about_info_phone[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Location', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter location here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $about_info_location,
                                    'id' => 'about_info_location',
                                    'cust_name' => 'about_info_location[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            if (function_exists('foodbakery_shortcode_custom_classes_test')) {
                                foodbakery_shortcode_custom_dynamic_classes($about_info_custom_class, $about_info_custom_animation, '', 'about_info');
                            }
                            ?>

                        </div>
                            <?php if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') { ?>
                                <ul class="form-elements insert-bg">
                                    <li class="to-field">
                                        <a class="insert-btn cs-main-btn" onclick="javascript:foodbakery_shortcode_insert_editor('<?php echo str_replace('foodbakery_var_page_builder_', '', $name); ?>', '<?php echo esc_js($name . $foodbakery_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php echo foodbakery_var_frame_text_srt('foodbakery_var_insert'); ?></a>
                                    </li>
                                </ul>
                                <div id="results-shortocde"></div>
                            <?php } else { ?>

                                <?php
                                $foodbakery_opt_array = array(
                                    'std' => 'about_info',
                                    'id' => '',
                                    'before' => '',
                                    'after' => '',
                                    'classes' => '',
                                    'extra_atr' => '',
                                    'cust_id' => 'foodbakery_orderby' . $foodbakery_counter,
                                    'cust_name' => 'foodbakery_orderby[]',
                                    'required' => false
                                );
                                $foodbakery_var_form_fields->foodbakery_var_form_hidden_render($foodbakery_opt_array);

                                $foodbakery_opt_array = array(
                                    'name' => '',
                                    'desc' => '',
                                    'hint_text' => '',
                                    'echo' => true,
                                    'field_params' => array(
                                        'std' => 'Save',
                                        'cust_id' => 'about_info_save',
                                        'cust_type' => 'button',
                                        'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                        'classes' => 'cs-foodbakery-admin-btn',
                                        'cust_name' => 'about_info_save',
                                        'return' => true,
                                    ),
                                );

                                $foodbakery_var_html_fields->foodbakery_var_text_field($foodbakery_opt_array);
                            }
                            ?>
                    </div>
	        </div>
                <script type="text/javascript">
                    popup_over();
                </script>
	    </div>

	    <?php
	}
	if ($die <> 1) {
	    die();
	}
    }

    add_action('wp_ajax_foodbakery_var_page_builder_about_info', 'foodbakery_var_page_builder_about_info');
}

if (!function_exists('foodbakery_save_page_builder_data_about_info_callback')) {

    /**
     * Save data for about_info shortcode.
     *
     * @param	array $args
     * @return	array
     */
    function foodbakery_save_page_builder_data_about_info_callback($args) {

	$data = $args['data'];
	$counters = $args['counters'];
	$widget_type = $args['widget_type'];
	$column = $args['column'];
	if ($widget_type == "about_info" || $widget_type == "cs_about_info") {
	    $foodbakery_about_info = '';

	    $page_element_size = $data['about_info_element_size'][$counters['foodbakery_global_counter_about_info']];
	    $current_element_size = $data['about_info_element_size'][$counters['foodbakery_global_counter_about_info']];

	    if (isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode') {
		$shortcode_str = stripslashes(( $data['shortcode']['about_info'][$counters['foodbakery_shortcode_counter_about_info']]));

		$element_settings = 'about_info_element_size="' . $current_element_size . '"';
		$reg = '/about_info_element_size="(\d+)"/s';
		$shortcode_str = preg_replace($reg, $element_settings, $shortcode_str);
		$shortcode_data .= $shortcode_str;

		$counters['foodbakery_shortcode_counter_about_info'] ++;
	    } else {
		$element_settings = 'about_info_element_size="' . htmlspecialchars($data['about_info_element_size'][$counters['foodbakery_global_counter_about_info']]) . '"';
		$foodbakery_about_info = '[about_info ' . $element_settings . ' ';
		if (isset($data['about_info_time'][$counters['foodbakery_counter_about_info']]) && $data['about_info_time'][$counters['foodbakery_counter_about_info']] != '') {
		    $foodbakery_about_info .= 'about_info_time="' . htmlspecialchars($data['about_info_time'][$counters['foodbakery_counter_about_info']], ENT_QUOTES) . '" ';
		}
		if (isset($data['about_info_phone'][$counters['foodbakery_counter_about_info']]) && $data['about_info_phone'][$counters['foodbakery_counter_about_info']] != '') {
		    $foodbakery_about_info .= 'about_info_phone="' . htmlspecialchars($data['about_info_phone'][$counters['foodbakery_counter_about_info']], ENT_QUOTES) . '" ';
		}
                if (isset($data['about_info_location'][$counters['foodbakery_counter_about_info']]) && $data['about_info_location'][$counters['foodbakery_counter_about_info']] != '') {
		    $foodbakery_about_info .= 'about_info_location="' . htmlspecialchars($data['about_info_location'][$counters['foodbakery_counter_about_info']], ENT_QUOTES) . '" ';
		}
                
		$foodbakery_about_info .= ']';
		if (isset($data['about_info_column_text'][$counters['foodbakery_counter_about_info']]) && $data['about_info_column_text'][$counters['foodbakery_counter_about_info']] != '') {
		    $foodbakery_about_info .= htmlspecialchars($data['about_info_column_text'][$counters['foodbakery_counter_about_info']], ENT_QUOTES) . ' ';
		}
		$foodbakery_about_info .= '[/about_info]';

		$shortcode_data .= $foodbakery_about_info;
		$counters['foodbakery_counter_about_info'] ++;
	    }
	    $counters['foodbakery_global_counter_about_info'] ++;
	}
	return array(
	    'data' => $data,
	    'counters' => $counters,
	    'widget_type' => $widget_type,
	    'column' => $shortcode_data,
	);
    }

    add_filter('foodbakery_save_page_builder_data_about_info', 'foodbakery_save_page_builder_data_about_info_callback');
}

if (!function_exists('foodbakery_load_shortcode_counters_about_info_callback')) {

    /**
     * Populate about_info shortcode counter variables.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_load_shortcode_counters_about_info_callback($counters) {
	$counters['foodbakery_global_counter_about_info'] = 0;
	$counters['foodbakery_shortcode_counter_about_info'] = 0;
	$counters['foodbakery_counter_about_info'] = 0;
	return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_about_info_callback');
}



if (!function_exists('foodbakery_element_list_populate_about_info_callback')) {

    /**
     * Populate about_info shortcode strings list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_element_list_populate_about_info_callback($element_list) {
	$element_list['about_info'] = 'Foodbakery About Info';
	return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_about_info_callback');
}

if (!function_exists('foodbakery_shortcode_names_list_populate_about_info_callback')) {

    /**
     * Populate about_info shortcode names list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_shortcode_names_list_populate_about_info_callback($shortcode_array) {
	$shortcode_array['about_info'] = array(
	    'title' => 'FB: About Info',
	    'name' => 'about_info',
	    'icon' => 'icon-table',
	    'categories' => 'loops misc',
	);

	return $shortcode_array;
    }

    add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_about_info_callback');
}
