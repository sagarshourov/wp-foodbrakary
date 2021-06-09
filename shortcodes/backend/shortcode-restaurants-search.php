<?php
/**
 * Shortcode Name : restaurant_search
 *
 * @package    foodbakery
 */
if (!function_exists('foodbakery_var_page_builder_restaurant_search')) {

    function foodbakery_var_page_builder_restaurant_search($die = 0)
    {
        global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if (function_exists('foodbakery_shortcode_names')) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'restaurant_search';

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
                'restaurant_search_title' => '',
                'restaurant_search_subtitle' => '',
                'restaurant_search_result_page' => '',
                'restaurant_search_view' => 'fancy',
            );
            if (isset($foodbakery_output['0']['atts'])) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if (isset($foodbakery_output['0']['content'])) {
                $restaurant_search_column_text = $foodbakery_output['0']['content'];
            } else {
                $restaurant_search_column_text = '';
            }
            $restaurant_search_element_size = '100';
            foreach ($defaults as $key => $values) {
                if (isset($atts[$key])) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_restaurant_search';
            $coloumn_class = 'column_' . $restaurant_search_element_size;
            if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            ?>

            <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del"
                 class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
		 <?php echo esc_attr($shortcode_view); ?>" item="restaurant_search"
                 data="<?php echo foodbakery_element_size_data_array_index($restaurant_search_element_size) ?>">
                <?php foodbakery_element_setting($name, $foodbakery_counter, $restaurant_search_element_size) ?>
                <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
		     <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>"
                     data-shortcode-template="[restaurant_search {{attributes}}]{{content}}[/restaurant_search]"
                     style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr($foodbakery_counter) ?>">
                        <h5><?php echo foodbakery_var_frame_text_srt('foodbakery_var_edit_restaurant_search_page') ?></h5>
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
                                'name' => esc_html__('Element Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter element title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $restaurant_search_title,
                                    'id' => 'restaurant_search_title',
                                    'cust_name' => 'restaurant_search_title[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Element Sub  Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter element sub title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $restaurant_search_subtitle,
                                    'id' => 'restaurant_search_subtitle',
                                    'cust_name' => 'restaurant_search_subtitle[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


                            $field_args = array(
                                'depth' => 0,
                                'child_of' => 0,
                                'class' => 'chosen-select',
                                'sort_order' => 'ASC',
                                'sort_column' => 'post_title',
                                'show_option_none' => esc_html__('Please select a page', "foodbakery"),
                                'hierarchical' => '1',
                                'exclude' => '',
                                'include' => '',
                                'meta_key' => '',
                                'meta_value' => '',
                                'authors' => '',
                                'exclude_tree' => '',
                                'selected' => $restaurant_search_result_page,
                                'echo' => 0,
                                'name' => 'restaurant_search_result_page[]',
                                'post_type' => 'page'
                            );
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Result Page', 'foodbakery'),
                                'id' => 'restaurant_search_result_page',
                                'desc' => '',
                                'echo' => true,
                                'hint_text' => esc_html__('Select Result Page', 'foodbakery'),
                                'std' => $restaurant_search_result_page,
                                'args' => $field_args,
                            );
                            $foodbakery_html_fields->foodbakery_select_page_field($foodbakery_opt_array);

                            $restaurant_views = array(
                                'fancy' => 'Fancy',
                                'modern' => 'Modern',
                                'list' => 'Simple',
                                'classic' => 'Classic',
                            );
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Default View', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_search_view),
                                    'id' => 'restaurant_search_view',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'restaurant_search_view[]',

                                    'return' => true,
                                    'options' => $restaurant_views
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            if (function_exists('foodbakery_shortcode_custom_classes_test')) {
                                foodbakery_shortcode_custom_dynamic_classes($restaurant_search_custom_class, $restaurant_search_custom_animation, '', 'restaurant_search');
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
                                'std' => 'restaurant_search',
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
                                    'cust_id' => 'restaurant_search_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn',
                                    'cust_name' => 'restaurant_search_save',
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

    add_action('wp_ajax_foodbakery_var_page_builder_restaurant_search', 'foodbakery_var_page_builder_restaurant_search');
}

if (!function_exists('foodbakery_save_page_builder_data_restaurant_search_callback')) {

    /**
     * Save data for restaurant_search shortcode.
     *
     * @param    array $args
     * @return    array
     */
    function foodbakery_save_page_builder_data_restaurant_search_callback($args)
    {
        $shortcode_data = '';
        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        if ($widget_type == "restaurant_search" || $widget_type == "cs_restaurant_search") {
            $foodbakery_bareber_restaurant_search = '';

            $page_element_size = $data['restaurant_search_element_size'][$counters['foodbakery_global_counter_restaurant_search']];
            $current_element_size = $data['restaurant_search_element_size'][$counters['foodbakery_global_counter_restaurant_search']];

            if (isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode') {
                $shortcode_str = stripslashes(($data['shortcode']['restaurant_search'][$counters['foodbakery_shortcode_counter_restaurant_search']]));
                $shortcode_data = '';
                $element_settings = 'restaurant_search_element_size="' . $current_element_size . '"';
                $reg = '/restaurant_search_element_size="(\d+)"/s';
                $shortcode_str = preg_replace($reg, $element_settings, $shortcode_str);
                $shortcode_data .= $shortcode_str;

                $counters['foodbakery_shortcode_counter_restaurant_search']++;
            } else {
                $element_settings = 'restaurant_search_element_size="' . htmlspecialchars($data['restaurant_search_element_size'][$counters['foodbakery_global_counter_restaurant_search']]) . '"';
                $foodbakery_bareber_restaurant_search = '[restaurant_search ' . $element_settings . ' ';
                if (isset($data['restaurant_search_title'][$counters['foodbakery_counter_restaurant_search']]) && $data['restaurant_search_title'][$counters['foodbakery_counter_restaurant_search']] != '') {
                    $foodbakery_bareber_restaurant_search .= 'restaurant_search_title="' . htmlspecialchars($data['restaurant_search_title'][$counters['foodbakery_counter_restaurant_search']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_search_subtitle'][$counters['foodbakery_counter_restaurant_search']]) && $data['restaurant_search_subtitle'][$counters['foodbakery_counter_restaurant_search']] != '') {
                    $foodbakery_bareber_restaurant_search .= 'restaurant_search_subtitle="' . htmlspecialchars($data['restaurant_search_subtitle'][$counters['foodbakery_counter_restaurant_search']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_search_result_page'][$counters['foodbakery_counter_restaurant_search']]) && $data['restaurant_search_result_page'][$counters['foodbakery_counter_restaurant_search']] != '') {
                    $foodbakery_bareber_restaurant_search .= 'restaurant_search_result_page="' . htmlspecialchars($data['restaurant_search_result_page'][$counters['foodbakery_counter_restaurant_search']], ENT_QUOTES) . '" ';
                }

                if (isset($data['restaurant_search_view'][$counters['foodbakery_counter_restaurant_search']]) && $data['restaurant_search_view'][$counters['foodbakery_counter_restaurant_search']] != '') {
                    $foodbakery_bareber_restaurant_search .= 'restaurant_search_view="' . htmlspecialchars($data['restaurant_search_view'][$counters['foodbakery_counter_restaurant_search']], ENT_QUOTES) . '" ';
                }

                $foodbakery_bareber_restaurant_search .= ']';
                if (isset($data['restaurant_search_column_text'][$counters['foodbakery_counter_restaurant_search']]) && $data['restaurant_search_column_text'][$counters['foodbakery_counter_restaurant_search']] != '') {
                    $foodbakery_bareber_restaurant_search .= htmlspecialchars($data['restaurant_search_column_text'][$counters['foodbakery_counter_restaurant_search']], ENT_QUOTES) . ' ';
                }
                $foodbakery_bareber_restaurant_search .= '[/restaurant_search]';

                $shortcode_data .= $foodbakery_bareber_restaurant_search;
                $counters['foodbakery_counter_restaurant_search']++;
            }
            $counters['foodbakery_global_counter_restaurant_search']++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter('foodbakery_save_page_builder_data_restaurant_search', 'foodbakery_save_page_builder_data_restaurant_search_callback');
}

if (!function_exists('foodbakery_load_shortcode_counters_restaurant_search_callback')) {

    /**
     * Populate restaurant_search shortcode counter variables.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_load_shortcode_counters_restaurant_search_callback($counters)
    {
        $counters['foodbakery_global_counter_restaurant_search'] = 0;
        $counters['foodbakery_shortcode_counter_restaurant_search'] = 0;
        $counters['foodbakery_counter_restaurant_search'] = 0;
        return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_restaurant_search_callback');
}


if (!function_exists('foodbakery_element_list_populate_restaurant_search_callback')) {

    /**
     * Populate restaurant_search shortcode strings list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_element_list_populate_restaurant_search_callback($element_list)
    {
        $element_list['restaurant_search'] = 'Foodbakery Restaurant Search';
        return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_restaurant_search_callback');
}

if (!function_exists('foodbakery_shortcode_names_list_populate_restaurant_search_callback')) {

    /**
     * Populate restaurant_search shortcode names list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_shortcode_names_list_populate_restaurant_search_callback($shortcode_array)
    {
        $shortcode_array['restaurant_search'] = array(
            'title' => 'FB: Restaurant Search',
            'name' => 'restaurant_search',
            'icon' => 'icon-human-resources',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_restaurant_search_callback');
}
