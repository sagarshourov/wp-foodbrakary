<?php
/**
 * Shortcode Name : foodbakery_register
 *
 * @package	foodbakery 
 */
if (!function_exists('foodbakery_var_page_builder_foodbakery_register')) {

    function foodbakery_var_page_builder_foodbakery_register($die = 0) {
	global $post,$foodbakery_html_fields,$foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
	if (function_exists('foodbakery_shortcode_names')) {
	    $shortcode_element = '';
	    $filter_element = 'filterdrag';
	    $shortcode_view = '';
	    $foodbakery_output = array();
	    $foodbakery_PREFIX = 'foodbakery_register';

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
		'foodbakery_var_column' => '1',
		'foodbakery_var_foodbakery_register_logo_url_array' => '',
		'foodbakery_var_foodbakery_register_image_url_array' => '',
		'title' => '',
		'foodbakery_var_lunch_date' => '',
		'foodbakery_var_foodbakery_register_estimated_time' => '',
		'reg_type' => '',
	    );
	    if (isset($foodbakery_output['0']['atts'])) {
		$atts = $foodbakery_output['0']['atts'];
	    } else {
		$atts = array();
	    }
	    if (isset($foodbakery_output['0']['content'])) {
		$foodbakery_register_column_text = $foodbakery_output['0']['content'];
	    } else {
		$foodbakery_register_column_text = '';
	    }
	    $foodbakery_register_element_size = '100';
	    foreach ($defaults as $key => $values) {
		if (isset($atts[$key])) {
		    $$key = $atts[$key];
		} else {
		    $$key = $values;
		}
	    }
	    $name = 'foodbakery_var_page_builder_foodbakery_register';
            
	    $coloumn_class = 'column_' . $foodbakery_register_element_size;
	    if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
		$shortcode_element = 'shortcode_element_class';
		$shortcode_view = 'cs-pbwp-shortcode';
		$filter_element = 'ajax-drag';
		$coloumn_class = '';
	    }
	    foodbakery_var_date_picker();
	    ?>

	    <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
		 <?php echo esc_attr($shortcode_view); ?>" item="foodbakery_register" data="<?php echo foodbakery_element_size_data_array_index($foodbakery_register_element_size) ?>" >
		     <?php foodbakery_element_setting($name, $foodbakery_counter, $foodbakery_register_element_size) ?>
	        <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
		     <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>" data-shortcode-template="[foodbakery_register {{attributes}}]{{content}}[/foodbakery_register]" style="display: none;">
	    	<div class="cs-heading-area" data-counter="<?php echo esc_attr($foodbakery_counter) ?>">
	    	    <h5><?php esc_html_e('FOODBAKERY REGISTER OPTIONS', 'foodbakery') ?></h5>
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
                                'name' => esc_html__('Element Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter element title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $title,
                                    'id' => 'title',
                                    'cust_name' => 'title[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
							
							$foodbakery_opt_array = array(
                                'name' => esc_html__('View', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'id' => 'reg_type',
                                    'cust_name' => 'reg_type[]',
                                    'return' => true,
									'options' => array('content' => esc_html__('In Content', 'foodbakery')),
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

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
				'std' => 'foodbakery_register',
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
				    'cust_id' => 'foodbakery_register_save',
				    'cust_type' => 'button',
				    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
				    'classes' => 'cs-foodbakery-admin-btn',
				    'cust_name' => 'foodbakery_register_save',
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

    add_action('wp_ajax_foodbakery_var_page_builder_foodbakery_register', 'foodbakery_var_page_builder_foodbakery_register');
}

if (!function_exists('foodbakery_save_page_builder_data_foodbakery_register_callback')) {

    /**
     * Save data for foodbakery_register shortcode.
     *
     * @param	array $args
     * @return	array
     */
    function foodbakery_save_page_builder_data_foodbakery_register_callback($args) {

	$data = $args['data'];
	$counters = $args['counters'];
	$widget_type = $args['widget_type'];
	$column = $args['column'];
	if ($widget_type == "foodbakery_register" || $widget_type == "cs_foodbakery_register") {
	    $foodbakery_bareber_foodbakery_register = '';
            
            $page_element_size     =  $data['foodbakery_register_element_size'][$counters['foodbakery_global_counter_foodbakery_register']];
            $current_element_size  =  $data['foodbakery_register_element_size'][$counters['foodbakery_global_counter_foodbakery_register']];
            if (isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode') {
		$shortcode_str = stripslashes(( $data['shortcode']['foodbakery_register'][$counters['foodbakery_shortcode_counter_foodbakery_register']]));
		
                $element_settings   = 'foodbakery_register_element_size="'.$current_element_size.'"';
                $reg = '/foodbakery_register_element_size="(\d+)"/s';
                $shortcode_str  = preg_replace( $reg, $element_settings, $shortcode_str );
                $shortcode_data .= $shortcode_str;
                                
                $foodbakery_bareber_foodbakery_register++;
	    } else {
                $element_settings   = 'foodbakery_register_element_size="'.htmlspecialchars( $data['foodbakery_register_element_size'][$counters['foodbakery_global_counter_foodbakery_register']] ).'"';
		$foodbakery_bareber_foodbakery_register = '[foodbakery_register '.$element_settings.' ';
		if (isset($data['title'][$counters['foodbakery_counter_foodbakery_register']]) && $data['title'][$counters['foodbakery_counter_foodbakery_register']] != '') {
		    $foodbakery_bareber_foodbakery_register .= 'title="' . htmlspecialchars($data['title'][$counters['foodbakery_counter_foodbakery_register']], ENT_QUOTES) . '" ';
		}
		if (isset($data['reg_type'][$counters['foodbakery_counter_foodbakery_register']]) && $data['reg_type'][$counters['foodbakery_counter_foodbakery_register']] != '') {
		    $foodbakery_bareber_foodbakery_register .= 'reg_type="' . htmlspecialchars($data['reg_type'][$counters['foodbakery_counter_foodbakery_register']], ENT_QUOTES) . '" ';
		}
		

		$foodbakery_bareber_foodbakery_register .= ']';
		if (isset($data['foodbakery_register_column_text'][$counters['foodbakery_counter_foodbakery_register']]) && $data['foodbakery_register_column_text'][$counters['foodbakery_counter_foodbakery_register']] != '') {
		    $foodbakery_bareber_foodbakery_register .= htmlspecialchars($data['foodbakery_register_column_text'][$counters['foodbakery_counter_foodbakery_register']], ENT_QUOTES) . ' ';
		}
		$foodbakery_bareber_foodbakery_register .= '[/foodbakery_register]';

		$shortcode_data .= $foodbakery_bareber_foodbakery_register;
		$counters['foodbakery_counter_foodbakery_register']++;
	    }
	    $counters['foodbakery_global_counter_foodbakery_register']++;
	}
	return array(
	    'data' => $data,
	    'counters' => $counters,
	    'widget_type' => $widget_type,
	    'column' => $shortcode_data,
	);
    }

    add_filter('foodbakery_save_page_builder_data_foodbakery_register', 'foodbakery_save_page_builder_data_foodbakery_register_callback');
}

if (!function_exists('foodbakery_load_shortcode_counters_foodbakery_register_callback')) {

    /**
     * Populate foodbakery_register shortcode counter variables.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_load_shortcode_counters_foodbakery_register_callback($counters) {
	$counters['foodbakery_global_counter_foodbakery_register'] = 0;
	$counters['foodbakery_shortcode_counter_foodbakery_register'] = 0;
	$counters['foodbakery_counter_foodbakery_register'] = 0;
	return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_foodbakery_register_callback');
}



if (!function_exists('foodbakery_element_list_populate_foodbakery_register_callback')) {

    /**
     * Populate foodbakery_register shortcode strings list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_element_list_populate_foodbakery_register_callback($element_list) {
	$element_list['foodbakery_register'] = 'Foodbakery Register';
	return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_foodbakery_register_callback');
}

if (!function_exists('foodbakery_shortcode_names_list_populate_foodbakery_register_callback')) {

    /**
     * Populate foodbakery_register shortcode names list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_shortcode_names_list_populate_foodbakery_register_callback($shortcode_array) {
	$shortcode_array['foodbakery_register'] = array(
	    'title' => 'FB: Register',
	    'name' => 'foodbakery_register',
	    'icon' => 'icon-bill',
	    'categories' => 'typography',
	);
	
	return $shortcode_array;
    }

    add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_foodbakery_register_callback');
}
