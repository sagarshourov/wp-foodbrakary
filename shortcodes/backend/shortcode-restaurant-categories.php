<?php
/**
 * Shortcode Name : restaurant_categories
 *
 * @package	foodbakery 
 */
if ( ! function_exists('foodbakery_var_page_builder_restaurant_categories') ) {

    function foodbakery_var_page_builder_restaurant_categories($die = 0) {
        global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if ( function_exists('foodbakery_shortcode_names') ) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'restaurant_categories';

            $foodbakery_counter = isset($_POST['counter']) ? $_POST['counter'] : '';
            if ( isset($_POST['action']) && ! isset($_POST['shortcode_element_id']) ) {
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
                'restaurant_categories_title' => '',
                'restaurant_categories_subtitle' => '',
                'foodbakery_var_categories_align' => '',
		'foodbakery_var_categories_style' => '',
		'foodbakery_title_color' => '',
                'restaurant_categories' => '',
                'foodbakery_types' => '',
            );
            if ( isset($foodbakery_output['0']['atts']) ) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if ( isset($foodbakery_output['0']['content']) ) {
                $restaurant_categories_column_text = $foodbakery_output['0']['content'];
            } else {
                $restaurant_categories_column_text = '';
            }
            $restaurant_categories_element_size = '100';
            foreach ( $defaults as $key => $values ) {
                if ( isset($atts[$key]) ) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
	    $foodbakery_title_color = isset($foodbakery_title_color) ? $foodbakery_title_color : '';
            $name = 'foodbakery_var_page_builder_restaurant_categories';
            $coloumn_class = 'column_' . $restaurant_categories_element_size;
            if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            $rand_id = rand(4444, 99999);
            wp_enqueue_script('foodbakery-admin-upload');
            ?>

            <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
                 <?php echo esc_attr($shortcode_view); ?>" item="restaurant_categories" data="<?php echo foodbakery_element_size_data_array_index($restaurant_categories_element_size) ?>" >
                     <?php foodbakery_element_setting($name, $foodbakery_counter, $restaurant_categories_element_size) ?>
                <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
                     <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>" data-shortcode-template="[restaurant_categories {{attributes}}]{{content}}[/restaurant_categories]" style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr($foodbakery_counter) ?>">
                        <h5><?php echo __("restaurant_categories Options", "foodbakery"); ?></h5>
                        <a href="javascript:foodbakery_frame_removeoverlay('<?php echo esc_js($name . $foodbakery_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose">
                            <i class="icon-times"></i>
                        </a>
                    </div>
                    <div class="cs-pbwp-content">
                        <div class="cs-wrapp-clone cs-shortcode-wrapp">
                            <?php
                            if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) {
                                foodbakery_shortcode_element_size();
                            }
                            $location_obj = get_terms('restaurant-category', array(
                                'hide_empty' => false,
                            ));
                            $foodbakery_cat_list = array();
                            if ( is_array($location_obj) && sizeof($location_obj) > 0 ) {
                                foreach ( $location_obj as $dir_cat ) {
                                    $foodbakery_cat_list[$dir_cat->slug] = $dir_cat->name;
                                }
                            }

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Element Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => __("Enter element title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $restaurant_categories_title,
                                    'id' => 'restaurant_categories_title',
                                    'cust_name' => 'restaurant_categories_title[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Element Sub  Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => __("Enter element sub title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $restaurant_categories_subtitle,
                                    'id' => 'restaurant_categories_subtitle',
                                    'cust_name' => 'restaurant_categories_subtitle[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => __('Title Alignment', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => __('Set element title alignment here', 'foodbakery'),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $foodbakery_var_categories_align,
                                    'id' => '',
                                    'cust_id' => 'foodbakery_var_categories_align',
                                    'cust_name' => 'foodbakery_var_categories_align[]',
                                    'classes' => 'service_postion chosen-select-no-single select-medium',
                                    'options' => array(
                                        'align-left' => __('Align Left', 'foodbakery'),
                                        'align-right' => __('Align Right', 'foodbakery'),
                                        'align-center' => __('Align Center', 'foodbakery'),
                                    ),
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			                                $foodbakery_opt_array = array(
                                'name' => __('Style', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => __('Set element Style here', 'foodbakery'),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $foodbakery_var_categories_style,
                                    'id' => '',
                                    'cust_id' => 'foodbakery_var_categories_style',
                                    'cust_name' => 'foodbakery_var_categories_style[]',
                                    'classes' => 'service_postion chosen-select-no-single select-medium',
                                    'options' => array(
                                        'default' => __('Default', 'foodbakery'),
                                        'fancy' => __('Fancy', 'foodbakery'),
					'modern' => __('Modern', 'foodbakery'),
                                    ),
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			    
			    $foodbakery_opt_array = array(
				    'name' => __('Category Colour', 'foodbakery'),
				    'desc' => '',
				    'hint_text' => __('Set element Category Colour', 'foodbakery'),
				    'echo' => true,
				    'field_params' => array(
					'std' => esc_attr($foodbakery_title_color),
					'cust_id' => 'foodbakery_title_color',
					'classes' => 'bg_color',
					'cust_name' => 'foodbakery_title_color[]',
					'return' => true,
				    ),
				);
				$foodbakery_var_html_fields->foodbakery_var_text_field($foodbakery_opt_array);
			    
                            ?>
                            <script>
                                jQuery(document).ready(function () {

                                    jQuery(".save_restaurant_categories_<?php echo absint($rand_id); ?>").click(function () {
                                        var MY_SELECT = jQuery('#foodbakery_types_array_<?php echo absint($rand_id); ?>').get(0);
                                        var selection = ChosenOrder.getSelectionOrder(MY_SELECT);
                                        var foodbakery_types_value = '';
                                        var comma = '';

                                        jQuery(selection).each(function (i) {
                                            foodbakery_types_value = foodbakery_types_value + comma + selection[i];
                                            comma = ',';
                                        });
                                        jQuery('#foodbakery_types_value_<?php echo absint($rand_id); ?>').val(foodbakery_types_value);
                                    });

                                });
                            </script>
                            <?php
                            $foodbakery_types_array = explode(',', $foodbakery_types);
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Restaurant Categories', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => __("Select Restaurant categories to show.", "foodbakery"),
                                'echo' => true,
                                'multi' => true,
                                'classes' => 'chosen-select',
                                'field_params' => array(
                                    'std' => $foodbakery_types_array,
                                    //'id' => '',
                                    'id' => 'types_array_' . $rand_id,
                                    'cust_name' => 'foodbakery_types_array[]',
                                    'return' => true,
                                    'classes' => 'chosen-select',
                                    'options' => $foodbakery_cat_list
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'std' => $foodbakery_types,
                                'cust_id' => 'foodbakery_types_value_' . $rand_id . '',
                                'cust_name' => "foodbakery_types[]",
                                'required' => false
                            );
                            $foodbakery_html_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

                            if ( function_exists('foodbakery_shortcode_custom_classes_test') ) {
                                foodbakery_shortcode_custom_dynamic_classes($restaurant_categories_custom_class, $restaurant_categories_custom_animation, '', 'restaurant_categories');
                            }
                            ?>


                        </div>
                        <?php if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) { ?>
                            <ul class="form-elements insert-bg">
                                <li class="to-field">
                                    <a class="insert-btn cs-main-btn" onclick="javascript:foodbakery_shortcode_insert_editor('<?php echo str_replace('foodbakery_var_page_builder_', '', $name); ?>', '<?php echo esc_js($name . $foodbakery_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php echo foodbakery_var_frame_text_srt('foodbakery_var_insert'); ?></a>
                                </li>
                            </ul>
                            <div id="results-shortocde"></div>
                        <?php } else { ?>

                            <?php
                            $foodbakery_opt_array = array(
                                'std' => 'restaurant_categories',
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
                                    'cust_id' => 'restaurant_categories_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn save_restaurant_categories_' . $rand_id,
                                    'cust_name' => 'restaurant_categories_save',
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
                    chosen_selectionbox();

                </script>
            </div>

            <?php
        }
        if ( $die <> 1 ) {
            die();
        }
    }

    add_action('wp_ajax_foodbakery_var_page_builder_restaurant_categories', 'foodbakery_var_page_builder_restaurant_categories');
}

if ( ! function_exists('foodbakery_save_page_builder_data_restaurant_categories_callback') ) {

    /**
     * Save data for restaurant_categories shortcode.
     *
     * @param	array $args
     * @return	array
     */
    function foodbakery_save_page_builder_data_restaurant_categories_callback($args) {
        global $type_names;
        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        $shortcode_data = '';
        if ( $widget_type == "restaurant_categories" || $widget_type == "cs_restaurant_categories" ) {
            $foodbakery_bareber_restaurant_categories = '';
            $page_element_size = $data['restaurant_categories_element_size'][$counters['foodbakery_global_counter_restaurant_categories']];
            $current_element_size = $data['restaurant_categories_element_size'][$counters['foodbakery_global_counter_restaurant_categories']];

            if ( isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode' ) {
                $shortcode_str = stripslashes(( $data['shortcode']['restaurant_categories'][$counters['foodbakery_shortcode_counter_restaurant_categories']]));

                $element_settings = 'restaurant_categories_element_size="' . $current_element_size . '"';
                $reg = '/restaurant_categories_element_size="(\d+)"/s';
                $shortcode_str = preg_replace($reg, $element_settings, $shortcode_str);
                $shortcode_data .= $shortcode_str;
                $counters['foodbakery_shortcode_counter_restaurant_categories'] ++;
                $foodbakery_bareber_restaurant_categories ++;
            } else {
                $element_settings = 'restaurant_categories_element_size="' . htmlspecialchars($data['restaurant_categories_element_size'][$counters['foodbakery_global_counter_restaurant_categories']]) . '"';
                $foodbakery_bareber_restaurant_categories = '[restaurant_categories ' . $element_settings . ' ';
                if ( isset($data['restaurant_categories_title'][$counters['foodbakery_counter_restaurant_categories']]) && $data['restaurant_categories_title'][$counters['foodbakery_counter_restaurant_categories']] != '' ) {
                    $foodbakery_bareber_restaurant_categories .= 'restaurant_categories_title="' . htmlspecialchars($data['restaurant_categories_title'][$counters['foodbakery_counter_restaurant_categories']], ENT_QUOTES) . '" ';
                }
                if ( isset($data['foodbakery_var_categories_align'][$counters['foodbakery_counter_restaurant_categories']]) && $data['foodbakery_var_categories_align'][$counters['foodbakery_counter_restaurant_categories']] != '' ) {
                    $foodbakery_bareber_restaurant_categories .= 'foodbakery_var_categories_align="' . htmlspecialchars($data['foodbakery_var_categories_align'][$counters['foodbakery_counter_restaurant_categories']], ENT_QUOTES) . '" ';
                }
		  if ( isset($data['foodbakery_var_categories_style'][$counters['foodbakery_counter_restaurant_categories']]) && $data['foodbakery_var_categories_style'][$counters['foodbakery_counter_restaurant_categories']] != '' ) {
                    $foodbakery_bareber_restaurant_categories .= 'foodbakery_var_categories_style="' . htmlspecialchars($data['foodbakery_var_categories_style'][$counters['foodbakery_counter_restaurant_categories']], ENT_QUOTES) . '" ';
                }
		if (isset($data['foodbakery_title_color'][$counters['foodbakery_counter_restaurant_categories']]) && $data['foodbakery_title_color'][$counters['foodbakery_counter_restaurant_categories']] != '') {
		    $foodbakery_bareber_restaurant_categories .= 'foodbakery_title_color="' . htmlspecialchars($data['foodbakery_title_color'][$counters['foodbakery_counter_restaurant_categories']], ENT_QUOTES) . '" ';
		}
                if ( isset($data['restaurant_categories_subtitle'][$counters['foodbakery_counter_restaurant_categories']]) && $data['restaurant_categories_subtitle'][$counters['foodbakery_counter_restaurant_categories']] != '' ) {
                    $foodbakery_bareber_restaurant_categories .= 'restaurant_categories_subtitle="' . htmlspecialchars($data['restaurant_categories_subtitle'][$counters['foodbakery_counter_restaurant_categories']], ENT_QUOTES) . '" ';
                }
                if ( isset($data['foodbakery_types'][$counters['foodbakery_counter_restaurant_categories']]) && $data['foodbakery_types'][$counters['foodbakery_counter_restaurant_categories']] != '' ) {
                    $foodbakery_bareber_restaurant_categories .= 'foodbakery_types="' . htmlspecialchars($data['foodbakery_types'][$counters['foodbakery_counter_restaurant_categories']], ENT_QUOTES) . '" ';
                }
                $foodbakery_bareber_restaurant_categories .= ']';
                if ( isset($data['restaurant_categories_column_text'][$counters['foodbakery_counter_restaurant_categories']]) && $data['restaurant_categories_column_text'][$counters['foodbakery_counter_restaurant_categories']] != '' ) {
                    $foodbakery_bareber_restaurant_categories .= htmlspecialchars($data['restaurant_categories_column_text'][$counters['foodbakery_counter_restaurant_categories']], ENT_QUOTES) . ' ';
                }
                $foodbakery_bareber_restaurant_categories .= '[/restaurant_categories]';

                $shortcode_data .= $foodbakery_bareber_restaurant_categories;
                $counters['foodbakery_counter_restaurant_categories'] ++;
            }
            $counters['foodbakery_global_counter_restaurant_categories'] ++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter('foodbakery_save_page_builder_data_restaurant_categories', 'foodbakery_save_page_builder_data_restaurant_categories_callback');
}

if ( ! function_exists('foodbakery_load_shortcode_counters_restaurant_categories_callback') ) {

    /**
     * Populate restaurant_categories shortcode counter variables.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_load_shortcode_counters_restaurant_categories_callback($counters) {
        $counters['foodbakery_global_counter_restaurant_categories'] = 0;
        $counters['foodbakery_shortcode_counter_restaurant_categories'] = 0;
        $counters['foodbakery_counter_restaurant_categories'] = 0;
        return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_restaurant_categories_callback');
}



if ( ! function_exists('foodbakery_element_list_populate_restaurant_categories_callback') ) {

    /**
     * Populate restaurant_categories shortcode strings list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_element_list_populate_restaurant_categories_callback($element_list) {
        $element_list['restaurant_categories'] = 'Restaurant Categories';
        return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_restaurant_categories_callback');
}

if ( ! function_exists('foodbakery_shortcode_names_list_populate_restaurant_categories_callback') ) {

    /**
     * Populate restaurant_categories shortcode names list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_shortcode_names_list_populate_restaurant_categories_callback($shortcode_array) {
        $shortcode_array['restaurant_categories'] = array(
            'title' => 'FB: Restaurant Categories',
            'name' => 'restaurant_categories',
            'icon' => 'icon-grid_on',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_restaurant_categories_callback');
}
