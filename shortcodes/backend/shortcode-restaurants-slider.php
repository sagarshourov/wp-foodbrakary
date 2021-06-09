<?php
/**
 * Shortcode Name : foodbakery_restaurants_slider
 *
 * @package    foodbakery
 */
if (!function_exists('foodbakery_var_page_builder_foodbakery_restaurants_slider')) {

    function foodbakery_var_page_builder_foodbakery_restaurants_slider($die = 0)
    {
        global $post, $foodbakery_html_fields, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if (function_exists('foodbakery_shortcode_names')) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'foodbakery_restaurants_slider';
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
                'restaurants_title' => '',
                'restaurants_subtitle' => '',
                'restaurant_type' => '',
                'restaurant_sort_by' => 'recent',
                'restaurant_slider_style' => '',
                'restaurant_featured' => 'all',
                'foodbakery_var_rest_slider_align' => '',
                'restaurant_location' => '',
            );
            $defaults = apply_filters('foodbakery_restaurants_slider_shortcode_admin_default_attributes', $defaults);
            if (isset($foodbakery_output['0']['atts'])) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if (isset($foodbakery_output['0']['content'])) {
                $foodbakery_restaurants_slider_column_text = $foodbakery_output['0']['content'];
            } else {
                $foodbakery_restaurants_slider_column_text = '';
            }
            $foodbakery_restaurants_slider_element_size = '100';
            foreach ($defaults as $key => $values) {
                if (isset($atts[$key])) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_foodbakery_restaurants_slider';
            $coloumn_class = 'column_' . $foodbakery_restaurants_slider_element_size;
            if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            $restaurant_rand_id = rand(4444, 99999);
            foodbakery_var_date_picker();
            $restaurant_views = array(
                'grid' => 'Grid',
                'list' => 'List',
                'fancy' => 'Fancy',
                'map' => 'Map',
            );
            ?>

            <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del"
                 class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
                 <?php echo esc_attr($shortcode_view); ?>" item="foodbakery_restaurants_slider"
                 data="<?php echo foodbakery_element_size_data_array_index($foodbakery_restaurants_slider_element_size) ?>">
                <?php foodbakery_element_setting($name, $foodbakery_counter, $foodbakery_restaurants_slider_element_size) ?>
                <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
                     <?php echo esc_attr($shortcode_element); ?>"
                     id="<?php echo esc_attr($name . $foodbakery_counter) ?>"
                     data-shortcode-template="[foodbakery_restaurants_slider {{attributes}}]{{content}}[/foodbakery_restaurants_slider]"
                     style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr($foodbakery_counter) ?>">
                        <h5><?php echo foodbakery_var_frame_text_srt('foodbakery_var_edit_foodbakery_restaurants_page') ?></h5>
                        <a href="javascript:foodbakery_frame_removeoverlay('<?php echo esc_js($name . $foodbakery_counter) ?>','<?php echo esc_js($filter_element); ?>')"
                           class="cs-btnclose">
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
                                'name' => esc_html__('Restaurants Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurants_title),
                                    'id' => 'restaurants_title',
                                    'cust_name' => 'restaurants_title[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Restaurants Sub Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurants_subtitle),
                                    'id' => 'restaurants_subtitle',
                                    'cust_name' => 'restaurants_subtitle[]',
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
                                    'std' => $foodbakery_var_rest_slider_align,
                                    'id' => '',
                                    'cust_id' => 'foodbakery_var_rest_slider_align',
                                    'cust_name' => 'foodbakery_var_rest_slider_align[]',
                                    'classes' => 'service_postion chosen-select-no-single select-medium',
                                    'options' => array(
                                        'align-left' => __('Align Left', 'foodbakery'),
                                        'align-right' => __('Align Right', 'foodbakery'),
                                        'align-center' => __('Align Center', 'foodbakery'),
                                    ),
                                    'return' => true,
                                ),
                            );

                            /*$foodbakery_post_restaurant_types = new Foodbakery_Post_Restaurant_Types();
                            $restaurant_types_array = $foodbakery_post_restaurant_types->foodbakery_types_array_callback();
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Restaurant Types', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_type),
                                    'id' => 'restaurant_type[]',
                                    'classes' => 'chosen-select',
                                    'cust_name' => 'restaurant_type[]',
                                    'return' => true,
                                    'options' => $restaurant_types_array
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);*/

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Sort By', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_sort_by),
                                    'id' => 'restaurant_sort_by[]',
                                    'cust_name' => 'restaurant_sort_by[]',
                                    'classes' => 'chosen-select-no-single',
                                    'return' => true,
                                    'options' => array(
                                        'recent' => 'Recent',
                                        'alphabetical' => 'Alphabetical',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Choose Style', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_slider_style),
                                    'id' => 'restaurant_slider_style[]',
                                    'cust_name' => 'restaurant_slider_style[]',
                                    'classes' => 'chosen-select-no-single',
                                    'return' => true,
                                    'options' => array(
                                        'default' => 'Default',
                                        'fancy' => 'Fancy',
                                        'simple' => 'Simple',

                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);


                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Featured', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_featured),
                                    'id' => 'restaurant_featured[]',
                                    'cust_name' => 'restaurant_featured[]',
                                    'return' => true,
                                    'classes' => 'chosen-select-no-single',
                                    'options' => array(
                                        'all' => 'All',
                                        'only-featured' => 'Only Featured',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            ?>
                            <script>
                                jQuery(document).ready(function () {
                                    jQuery(".save_restaurant_locations_<?php echo intval($restaurant_rand_id); ?>").click(function () {
                                        var MY_SELECT = jQuery('#foodbakery_restaurant_locations_<?php echo intval($restaurant_rand_id); ?>').get(0);
                                        var selection = ChosenOrder.getSelectionOrder(MY_SELECT);
                                        var restaurant_location_value = '';
                                        var comma = '';
                                        jQuery(selection).each(function (i) {
                                            restaurant_location_value = restaurant_location_value + comma + selection[i];
                                            comma = ',';
                                        });
                                        jQuery('#restaurant_location_<?php echo intval($restaurant_rand_id); ?>').val(restaurant_location_value);
                                    });

                                });
                            </script>
                            <?php
                            $saved_restaurant_location = $restaurant_location;
                            $restaurant_location_options = array(
                                'country' => __('Country', 'foodbakery'),
                                'state' => __('State', 'foodbakery'),
                                'city' => __('City', 'foodbakery'),
                                'town' => __('Town', 'foodbakery'),
                                'address' => __('Complete Address', 'foodbakery'),
                            );

                            if ($restaurant_location != '') {
                                $restaurant_locations = explode(',', $restaurant_location);
                                foreach ($restaurant_locations as $restaurant_location) {
                                    $get_restaurant_locations[$restaurant_location] = $restaurant_location_options[$restaurant_location];
                                }
                            }
                            if (isset($get_restaurant_locations) && $get_restaurant_locations) {
                                $restaurant_location_options = array_unique(array_merge($get_restaurant_locations, $restaurant_location_options));
                            } else {
                                $restaurant_location_options = $restaurant_location_options;
                            }

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Location', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'multi' => true,
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $saved_restaurant_location,
                                    'id' => 'restaurant_locations_' . $restaurant_rand_id . '',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'restaurant_locations[]',
                                    'return' => true,
                                    'options' => $restaurant_location_options,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'std' => $restaurant_location,
                                'cust_id' => 'restaurant_location_' . $restaurant_rand_id . '',
                                'cust_name' => "restaurant_location[]",
                                'required' => false
                            );
                            $foodbakery_var_form_fields->foodbakery_var_form_hidden_render($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'std' => absint($restaurant_rand_id),
                                'id' => '',
                                'cust_id' => 'restaurant_counter',
                                'cust_name' => 'restaurant_counter[]',
                                'required' => false
                            );
                            $foodbakery_var_form_fields->foodbakery_var_form_hidden_render($foodbakery_opt_array);
                            if (function_exists('foodbakery_shortcode_custom_classes_test')) {
                                foodbakery_shortcode_custom_dynamic_classes($foodbakery_restaurants_slider_custom_class, $foodbakery_restaurants_slider_custom_animation, '', 'restaurants');
                            }
                            ?>
                        </div>
                        <?php if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') { ?>
                            <ul class="form-elements insert-bg">
                                <li class="to-field">
                                    <a class="insert-btn cs-main-btn"
                                       onclick="javascript:foodbakery_shortcode_insert_editor('<?php echo str_replace('foodbakery_var_page_builder_', '', $name); ?>', '<?php echo esc_js($name . $foodbakery_counter) ?>', '<?php echo esc_js($filter_element); ?>')"><?php echo foodbakery_var_frame_text_srt('foodbakery_var_insert'); ?></a>
                                </li>
                            </ul>
                            <div id="results-shortocde"></div>
                        <?php } else { ?>

                            <?php
                            $foodbakery_opt_array = array(
                                'std' => 'foodbakery_restaurants_slider',
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
                                    'cust_id' => 'foodbakery_restaurants_slider_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn save_restaurant_locations_' . $restaurant_rand_id . '',
                                    'cust_name' => 'foodbakery_restaurants_slider_save',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field($foodbakery_opt_array);
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }
        if ($die <> 1) {
            die();
        }
    }

    add_action('wp_ajax_foodbakery_var_page_builder_foodbakery_restaurants_slider', 'foodbakery_var_page_builder_foodbakery_restaurants_slider');
}

if (!function_exists('foodbakery_save_page_builder_data_foodbakery_restaurants_slider_callback')) {

    /**
     * Save data for foodbakery_restaurants_slider shortcode.
     *
     * @param    array $args
     * @return    array
     */
    function foodbakery_save_page_builder_data_foodbakery_restaurants_slider_callback($args)
    {

        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $shortcode_data ='';
        $column = $args['column'];
        if ($widget_type == "foodbakery_restaurants_slider" || $widget_type == "cs_foodbakery_restaurants_slider") {
            $foodbakery_bareber_foodbakery_restaurants_slider = '';

            $page_element_size = $data['foodbakery_restaurants_slider_element_size'][$counters['foodbakery_global_counter_foodbakery_restaurants_slider']];
            $current_element_size = $data['foodbakery_restaurants_slider_element_size'][$counters['foodbakery_global_counter_foodbakery_restaurants_slider']];

            if (isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode') {
                $shortcode_str = stripslashes(($data['shortcode']['foodbakery_restaurants_slider'][$counters['foodbakery_shortcode_counter_foodbakery_restaurants_slider']]));
                $shortcode_data = '';
                $element_settings = 'foodbakery_restaurants_slider_element_size="' . $current_element_size . '"';
                $reg = '/foodbakery_restaurants_slider_element_size="(\d+)"/s';
                $shortcode_str = preg_replace($reg, $element_settings, $shortcode_str);
                $shortcode_data .= $shortcode_str;
                $counters['foodbakery_shortcode_counter_foodbakery_restaurants_slider']++;
            } else {
                $element_settings = 'foodbakery_restaurants_slider_element_size="' . htmlspecialchars($data['foodbakery_restaurants_slider_element_size'][$counters['foodbakery_global_counter_foodbakery_restaurants_slider']]) . '"';
                $foodbakery_bareber_foodbakery_restaurants_slider = '[foodbakery_restaurants_slider ' . $element_settings . ' ';
                if (isset($data['restaurants_title'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurants_title'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurants_title="' . htmlspecialchars($data['restaurants_title'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                if (isset($data['foodbakery_var_rest_slider_align'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['foodbakery_var_rest_slider_align'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'foodbakery_var_rest_slider_align="' . htmlspecialchars($data['foodbakery_var_rest_slider_align'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurants_subtitle'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurants_subtitle'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurants_subtitle="' . htmlspecialchars($data['restaurants_subtitle'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_type'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurant_type'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurant_type="' . htmlspecialchars($data['restaurant_type'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_sort_by'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurant_sort_by'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurant_sort_by="' . htmlspecialchars($data['restaurant_sort_by'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_slider_style'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurant_slider_style'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurant_slider_style="' . htmlspecialchars($data['restaurant_slider_style'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                // saving job type admin field using filter for add on
                $foodbakery_bareber_foodbakery_restaurants_slider = apply_filters('foodbakery_save_restaurants_shortcode_admin_fields', $foodbakery_bareber_foodbakery_restaurants_slider, $_POST, $counters['foodbakery_counter_foodbakery_restaurants_slider']);
                if (isset($data['restaurant_featured'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurant_featured'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurant_featured="' . htmlspecialchars($data['restaurant_featured'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_counter'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurant_counter'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurant_counter="' . htmlspecialchars($data['restaurant_counter'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_location'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['restaurant_location'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= 'restaurant_location="' . htmlspecialchars($data['restaurant_location'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . '" ';
                }
                $foodbakery_bareber_foodbakery_restaurants_slider .= ']';
                if (isset($data['foodbakery_restaurants_slider_column_text'][$counters['foodbakery_counter_foodbakery_restaurants_slider']]) && $data['foodbakery_restaurants_slider_column_text'][$counters['foodbakery_counter_foodbakery_restaurants_slider']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants_slider .= htmlspecialchars($data['foodbakery_restaurants_slider_column_text'][$counters['foodbakery_counter_foodbakery_restaurants_slider']], ENT_QUOTES) . ' ';
                }
                $foodbakery_bareber_foodbakery_restaurants_slider .= '[/foodbakery_restaurants_slider]';
                $shortcode_data .= $foodbakery_bareber_foodbakery_restaurants_slider;
                $counters['foodbakery_counter_foodbakery_restaurants_slider']++;
            }
            $counters['foodbakery_global_counter_foodbakery_restaurants_slider']++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter('foodbakery_save_page_builder_data_foodbakery_restaurants_slider', 'foodbakery_save_page_builder_data_foodbakery_restaurants_slider_callback');
}

if (!function_exists('foodbakery_load_shortcode_counters_foodbakery_restaurants_slider_callback')) {

    /**
     * Populate foodbakery_restaurants_slider shortcode counter variables.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_load_shortcode_counters_foodbakery_restaurants_slider_callback($counters)
    {
        $counters['foodbakery_global_counter_foodbakery_restaurants_slider'] = 0;
        $counters['foodbakery_shortcode_counter_foodbakery_restaurants_slider'] = 0;
        $counters['foodbakery_counter_foodbakery_restaurants_slider'] = 0;
        return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_foodbakery_restaurants_slider_callback');
}


if (!function_exists('foodbakery_element_list_populate_foodbakery_restaurants_slider_callback')) {

    /**
     * Populate foodbakery_restaurants_slider shortcode strings list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_element_list_populate_foodbakery_restaurants_slider_callback($element_list)
    {
        $element_list['foodbakery_restaurants_slider'] = 'Foodbakery Restaurants Slider';
        return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_foodbakery_restaurants_slider_callback');
}

if (!function_exists('foodbakery_shortcode_names_list_populate_foodbakery_restaurants_slider_callback')) {

    /**
     * Populate foodbakery_restaurants_slider shortcode names list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_shortcode_names_list_populate_foodbakery_restaurants_slider_callback($shortcode_array)
    {
        $shortcode_array['foodbakery_restaurants_slider'] = array(
            'title' => 'FB: Restaurants Slider',
            'name' => 'foodbakery_restaurants_slider',
            'icon' => 'icon-food',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_foodbakery_restaurants_slider_callback');
}
