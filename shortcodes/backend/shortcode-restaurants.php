<?php
/**
 * Shortcode Name : foodbakery_restaurants
 *
 * @package    foodbakery
 */
if (!function_exists('foodbakery_var_page_builder_foodbakery_restaurants')) {

    function foodbakery_var_page_builder_foodbakery_restaurants($die = 0)
    {
        global $post, $foodbakery_html_fields, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if (function_exists('foodbakery_shortcode_names')) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'foodbakery_restaurants';
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
                'restaurant_view' => '',
                'restaurant_sort_by' => 'no',
                'restaurant_search_keyword' => 'no',
                'restaurant_featured' => 'no',
                'restaurant_ads_switch' => 'no',
                'restaurant_ads_after_list_count' => '5',
                'restaurant_location' => '',
                'posts_per_page' => '',
                'pagination' => '',
                'search_box' => '',
                'open_close_show_labels' => '',
                'open_close_default_filter' => '',
                'open_close_filter_switch' => '',
                'pre_order_filter_switch' => '',
                'left_filter_count' => '',
                'foodbakery_var_restaurants_total_num' => '',
                'right_sidebar' => 'no',
                'foodbakery_var_restaurants_align' => '',
            );
            $defaults = apply_filters('foodbakery_restaurants_shortcode_admin_default_attributes', $defaults);
            if (isset($foodbakery_output['0']['atts'])) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if (isset($foodbakery_output['0']['content'])) {
                $foodbakery_restaurants_column_text = $foodbakery_output['0']['content'];
            } else {
                $foodbakery_restaurants_column_text = '';
            }
            $foodbakery_restaurants_element_size = '100';
            foreach ($defaults as $key => $values) {
                if (isset($atts[$key])) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_foodbakery_restaurants';
            $coloumn_class = 'column_' . $foodbakery_restaurants_element_size;
            if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            $restaurant_rand_id = rand(4444, 99999);
            foodbakery_var_date_picker();
            $restaurant_views = array(
                'fancy' => 'Fancy',
                'grid' => 'Grid',
                'list' => 'List',
                'simple' => 'Simple',
                'fancy-grid' => 'Fancy Grid',
                'classic-grid' => 'Classic Grid',
                'grid-slider' => 'Grid Slider',
            );
            ?>

            <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del"
                 class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
		 <?php echo esc_attr($shortcode_view); ?>" item="foodbakery_restaurants"
                 data="<?php echo foodbakery_element_size_data_array_index($foodbakery_restaurants_element_size) ?>">
                <?php foodbakery_element_setting($name, $foodbakery_counter, $foodbakery_restaurants_element_size) ?>
                <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
		     <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>"
                     data-shortcode-template="[foodbakery_restaurants {{attributes}}]{{content}}[/foodbakery_restaurants]"
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
                                    'std' => $foodbakery_var_restaurants_align,
                                    'id' => '',
                                    'cust_id' => 'foodbakery_var_restaurants_align',
                                    'cust_name' => 'foodbakery_var_restaurants_align[]',
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
                                'name' => __('Show Restaurant Count', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => __('Display total number of restaurants', 'foodbakery'),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $foodbakery_var_restaurants_total_num,
                                    'id' => '',
                                    'cust_id' => 'foodbakery_var_restaurants_total_num',
                                    'cust_name' => 'foodbakery_var_restaurants_total_num[]',
                                    'classes' => 'chosen-select-no-single',
                                    'options' => array(
                                        'no' => __('No', 'foodbakery'),
                                        'yes' => __('Yes', 'foodbakery'),
                                    ),
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);


                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Default View', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_view),
                                    'id' => 'restaurant_view' . $restaurant_rand_id . '',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'restaurant_view[]',
                                    'return' => true,
                                    'options' => $restaurant_views
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            ?>
                            <script>

                                function restaurant_ads_count<?php echo intval($restaurant_rand_id); ?>($restaurant_ads_switcher) {
                                    if ($restaurant_ads_switcher == 'no') {
                                        jQuery('.restaurant_count_dynamic_fields<?php echo intval($restaurant_rand_id); ?>').hide();
                                    } else {
                                        jQuery('.restaurant_count_dynamic_fields<?php echo intval($restaurant_rand_id); ?>').show();
                                    }
                                }
                            </script>
                            <?php
                            $topmap_position_hide_string = '';
                            $topmap_position_show_string = '';


                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Left Filters', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($search_box),
                                    'id' => 'search_box[]',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'search_box[]',
                                    'extra_atr' => 'onchange="left_filter_count' . $restaurant_rand_id . '(this.value)"',
                                    'return' => true,
                                    'options' => array(
                                        'no' => 'No',
                                        'yes' => 'Yes',
                                    )
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $left_filter_hide_string = '';
                            if ($search_box == 'no') {
                                $left_filter_hide_string = 'style="display:none;"';
                            }
                            ?>
                            <script>
                                function left_filter_count<?php echo intval($restaurant_rand_id); ?>($search_box) {
                                    if ($search_box == 'no') {
                                        jQuery('.left_filter_show_position<?php echo intval($restaurant_rand_id); ?>').hide();
                                    } else {
                                        jQuery('.left_filter_show_position<?php echo intval($restaurant_rand_id); ?>').show();
                                    }
                                }
                            </script><?php
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Left Filters Counts', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'main_wraper' => true,
                                'main_wraper_class' => 'left_filter_show_position' . $restaurant_rand_id . '',
                                'main_wraper_extra' => $left_filter_hide_string,
                                'field_params' => array(
                                    'std' => esc_attr($left_filter_count),
                                    'id' => 'left_filter_count[]',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'left_filter_count[]',
                                    'return' => true,
                                    'options' => array(
                                        'no' => 'No',
                                        'yes' => 'Yes',
                                    )
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Show Open/Close Labels', 'foodbakery'),
                                'hint_text' => esc_html__('You can show restaurant open / close label for every restaurant in restaurant listing', 'foodbakery'),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($open_close_show_labels),
                                    'id' => 'open_close_show_labels[]',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'open_close_show_labels[]',
                                    'return' => true,
                                    'options' => array(
                                        'no' => 'No',
                                        'yes' => 'Yes',
                                    ),
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Open/Close Filter', 'foodbakery'),
                                'hint_text' => esc_html__('You can turn on/off in filters', 'foodbakery'),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($open_close_filter_switch),
                                    'id' => 'open_close_filter_switch[]',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'open_close_filter_switch[]',
                                    'return' => true,
                                    'options' => array(
                                        'yes' => 'Yes',
                                        'no' => 'No',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Default Open/Close Filter', 'foodbakery'),
                                'hint_text' => esc_html__('You can choose default restaurant listing open/close status.', 'foodbakery'),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($open_close_default_filter),
                                    'id' => 'open_close_default_filter[]',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'open_close_default_filter[]',
                                    'return' => true,
                                    'options' => array(
                                        'all' => 'All',
                                        'open' => 'Open',
                                        'close' => 'Close',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);


                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Pre Orders Filter', 'foodbakery'),
                                'hint_text' => esc_html__('Do you want Pre Orders filter in restaurant listings?', 'foodbakery'),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($pre_order_filter_switch),
                                    'id' => 'pre_order_filter_switch[]',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'pre_order_filter_switch[]',
                                    'return' => true,
                                    'options' => array(
                                        'yes' => 'Yes',
                                        'no' => 'No',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

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
                                        'no' => 'No',
                                        'yes' => 'Yes',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);


                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Search Keyword', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_search_keyword),
                                    'id' => 'restaurant_search_keyword[]',
                                    'cust_name' => 'restaurant_search_keyword[]',
                                    'return' => true,
                                    'classes' => 'chosen-select-no-single',
                                    'options' => array(
                                        'no' => 'No',
                                        'yes' => 'Yes',
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
                                        'top-category' => 'Top Category',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);


                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Ads Switch', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_ads_switch),
                                    'id' => 'restaurant_ads_switch[]',
                                    'cust_name' => 'restaurant_ads_switch[]',
                                    'return' => true,
                                    'classes' => 'chosen-select-no-single',
                                    'extra_atr' => 'onchange="restaurant_ads_count' . $restaurant_rand_id . '(this.value)"',
                                    'options' => array(
                                        'no' => 'No',
                                        'yes' => 'Yes',
                                    )
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $restaurant_count_hide_string = '';
                            if ($restaurant_ads_switch == 'no') {
                                $restaurant_count_hide_string = 'style="display:none;"';
                            }

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Restaurant Count', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => 'Number of series for add ad after every number like: 0, 7, 4, 2, 5',
                                'echo' => true,
                                'main_wraper' => true,
                                'main_wraper_class' => 'restaurant_count_dynamic_fields' . $restaurant_rand_id . '',
                                'main_wraper_extra' => $restaurant_count_hide_string,
                                'field_params' => array(
                                    'std' => esc_attr($restaurant_ads_after_list_count),
                                    'id' => 'restaurant_ads_after_list_count',
                                    'cust_name' => 'restaurant_ads_after_list_count[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
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


                            $pagination_options = array('no' => 'No', 'yes' => 'Yes');
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Pagination', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($pagination),
                                    'id' => 'pagination',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'pagination[]',
                                    'return' => true,
                                    'options' => $pagination_options
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Posts Per Page', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($posts_per_page),
                                    'id' => 'posts_per_page',
                                    'cust_name' => 'posts_per_page[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Right Sidebar', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr($right_sidebar),
                                    'id' => 'right_sidebar[]',
                                    'classes' => 'chosen-select-no-single',
                                    'cust_name' => 'right_sidebar[]',
                                    'return' => true,
                                    'options' => array(
                                        'no' => __('No', 'foodbakery'),
                                        'yes' => __('Yes', 'foodbakery'),
                                    )
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'std' => absint($restaurant_rand_id),
                                'id' => '',
                                'cust_id' => 'restaurant_counter',
                                'cust_name' => 'restaurant_counter[]',
                                'required' => false
                            );
                            $foodbakery_var_form_fields->foodbakery_var_form_hidden_render($foodbakery_opt_array);
                            if (function_exists('foodbakery_shortcode_custom_classes_test')) {
                                foodbakery_shortcode_custom_dynamic_classes($foodbakery_restaurants_custom_class, $foodbakery_restaurants_custom_animation, '', 'restaurants');
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
                                'std' => 'foodbakery_restaurants',
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
                                    'cust_id' => 'foodbakery_restaurants_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn save_restaurant_locations_' . $restaurant_rand_id . '',
                                    'cust_name' => 'foodbakery_restaurants_save',
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

    add_action('wp_ajax_foodbakery_var_page_builder_foodbakery_restaurants', 'foodbakery_var_page_builder_foodbakery_restaurants');
}

if (!function_exists('foodbakery_save_page_builder_data_foodbakery_restaurants_callback')) {

    /**
     * Save data for foodbakery_restaurants shortcode.
     *
     * @param    array $args
     * @return    array
     */
    function foodbakery_save_page_builder_data_foodbakery_restaurants_callback($args)
    {

        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        $shortcode_data = '';
        if ($widget_type == "foodbakery_restaurants" || $widget_type == "cs_foodbakery_restaurants") {
            $foodbakery_bareber_foodbakery_restaurants = '';

            $page_element_size = $data['foodbakery_restaurants_element_size'][$counters['foodbakery_global_counter_foodbakery_restaurants']];
            $current_element_size = $data['foodbakery_restaurants_element_size'][$counters['foodbakery_global_counter_foodbakery_restaurants']];

            if (isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode') {
                $shortcode_str = stripslashes(($data['shortcode']['foodbakery_restaurants'][$counters['foodbakery_shortcode_counter_foodbakery_restaurants']]));
                $shortcode_data = '';
                $element_settings = 'foodbakery_restaurants_element_size="' . $current_element_size . '"';
                $reg = '/foodbakery_restaurants_element_size="(\d+)"/s';
                $shortcode_str = preg_replace($reg, $element_settings, $shortcode_str);
                $shortcode_data .= $shortcode_str;
                $counters['foodbakery_shortcode_counter_foodbakery_restaurants']++;
            } else {
                $element_settings = 'foodbakery_restaurants_element_size="' . htmlspecialchars($data['foodbakery_restaurants_element_size'][$counters['foodbakery_global_counter_foodbakery_restaurants']]) . '"';
                $foodbakery_bareber_foodbakery_restaurants = '[foodbakery_restaurants ' . $element_settings . ' ';
                if (isset($data['restaurants_title'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurants_title'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurants_title="' . htmlspecialchars($data['restaurants_title'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }

                if (isset($data['foodbakery_var_restaurants_align'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['foodbakery_var_restaurants_align'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'foodbakery_var_restaurants_align="' . htmlspecialchars($data['foodbakery_var_restaurants_align'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['foodbakery_var_restaurants_total_num'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['foodbakery_var_restaurants_total_num'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'foodbakery_var_restaurants_total_num="' . htmlspecialchars($data['foodbakery_var_restaurants_total_num'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurants_subtitle'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurants_subtitle'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurants_subtitle="' . htmlspecialchars($data['restaurants_subtitle'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }

                if (isset($data['restaurant_view'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_view'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_view="' . htmlspecialchars($data['restaurant_view'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_sort_by'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_sort_by'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_sort_by="' . htmlspecialchars($data['restaurant_sort_by'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }

                // saving job type admin field using filter for add on
                $foodbakery_bareber_foodbakery_restaurants = apply_filters('foodbakery_save_restaurants_shortcode_admin_fields', $foodbakery_bareber_foodbakery_restaurants, $_POST, $counters['foodbakery_counter_foodbakery_restaurants']);
                if (isset($data['restaurant_search_keyword'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_search_keyword'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_search_keyword="' . htmlspecialchars($data['restaurant_search_keyword'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_footer'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_footer'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_footer="' . htmlspecialchars($data['restaurant_footer'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_featured'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_featured'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_featured="' . htmlspecialchars($data['restaurant_featured'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_ads_switch'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_ads_switch'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_ads_switch="' . htmlspecialchars($data['restaurant_ads_switch'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_ads_after_list_count'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_ads_after_list_count'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_ads_after_list_count="' . htmlspecialchars($data['restaurant_ads_after_list_count'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['posts_per_page'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['posts_per_page'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'posts_per_page="' . htmlspecialchars($data['posts_per_page'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['pagination'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['pagination'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'pagination="' . htmlspecialchars($data['pagination'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_counter'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_counter'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_counter="' . htmlspecialchars($data['restaurant_counter'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['search_box'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['search_box'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'search_box="' . htmlspecialchars($data['search_box'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['open_close_show_labels'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['open_close_show_labels'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'open_close_show_labels="' . htmlspecialchars($data['open_close_show_labels'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['open_close_default_filter'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['open_close_default_filter'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'open_close_default_filter="' . htmlspecialchars($data['open_close_default_filter'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['left_filter_count'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['left_filter_count'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'left_filter_count="' . htmlspecialchars($data['left_filter_count'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['open_close_filter_switch'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['open_close_filter_switch'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'open_close_filter_switch="' . htmlspecialchars($data['open_close_filter_switch'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['pre_order_filter_switch'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['pre_order_filter_switch'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'pre_order_filter_switch="' . htmlspecialchars($data['pre_order_filter_switch'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['restaurant_location'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['restaurant_location'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'restaurant_location="' . htmlspecialchars($data['restaurant_location'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                if (isset($data['right_sidebar'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['right_sidebar'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= 'right_sidebar="' . htmlspecialchars($data['right_sidebar'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . '" ';
                }
                $foodbakery_bareber_foodbakery_restaurants .= ']';
                if (isset($data['foodbakery_restaurants_column_text'][$counters['foodbakery_counter_foodbakery_restaurants']]) && $data['foodbakery_restaurants_column_text'][$counters['foodbakery_counter_foodbakery_restaurants']] != '') {
                    $foodbakery_bareber_foodbakery_restaurants .= htmlspecialchars($data['foodbakery_restaurants_column_text'][$counters['foodbakery_counter_foodbakery_restaurants']], ENT_QUOTES) . ' ';
                }
                $foodbakery_bareber_foodbakery_restaurants .= '[/foodbakery_restaurants]';
                $shortcode_data .= $foodbakery_bareber_foodbakery_restaurants;
                $counters['foodbakery_counter_foodbakery_restaurants']++;
            }
            $counters['foodbakery_global_counter_foodbakery_restaurants']++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter('foodbakery_save_page_builder_data_foodbakery_restaurants', 'foodbakery_save_page_builder_data_foodbakery_restaurants_callback');
}

if (!function_exists('foodbakery_load_shortcode_counters_foodbakery_restaurants_callback')) {

    /**
     * Populate foodbakery_restaurants shortcode counter variables.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_load_shortcode_counters_foodbakery_restaurants_callback($counters)
    {
        $counters['foodbakery_global_counter_foodbakery_restaurants'] = 0;
        $counters['foodbakery_shortcode_counter_foodbakery_restaurants'] = 0;
        $counters['foodbakery_counter_foodbakery_restaurants'] = 0;
        return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_foodbakery_restaurants_callback');
}


if (!function_exists('foodbakery_element_list_populate_foodbakery_restaurants_callback')) {

    /**
     * Populate foodbakery_restaurants shortcode strings list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_element_list_populate_foodbakery_restaurants_callback($element_list)
    {
        $element_list['foodbakery_restaurants'] = 'Foodbakery Restaurants';
        return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_foodbakery_restaurants_callback');
}

if (!function_exists('foodbakery_shortcode_names_list_populate_foodbakery_restaurants_callback')) {

    /**
     * Populate foodbakery_restaurants shortcode names list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_shortcode_names_list_populate_foodbakery_restaurants_callback($shortcode_array)
    {
        $shortcode_array['foodbakery_restaurants'] = array(
            'title' => 'FB: Restaurants',
            'name' => 'foodbakery_restaurants',
            'icon' => 'icon-food',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_foodbakery_restaurants_callback');
}
