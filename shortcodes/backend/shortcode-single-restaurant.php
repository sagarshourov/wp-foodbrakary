<?php
/**
 * Shortcode Name : Single Restaurant
 *
 * @package    foodbakery
 */
if (!function_exists('foodbakery_var_page_builder_single_restaurant')) {

    function foodbakery_var_page_builder_single_restaurant($die = 0)
    {
        global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if (function_exists('foodbakery_shortcode_names')) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'single_restaurant';

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
                'single_restaurant_title' => '',
                'single_restaurant_subtitle' => '',
                'selected_restaurant' => '',
            );
            if (isset($foodbakery_output['0']['atts'])) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if (isset($foodbakery_output['0']['content'])) {
                $single_restaurant_column_text = $foodbakery_output['0']['content'];
            } else {
                $single_restaurant_column_text = '';
            }
            $single_restaurant_element_size = '100';
            foreach ($defaults as $key => $values) {
                if (isset($atts[$key])) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_single_restaurant';
            $coloumn_class = 'column_' . $single_restaurant_element_size;
            if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            ?>

            <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del"
                 class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
		 <?php echo esc_attr($shortcode_view); ?>" item="single_restaurant"
                 data="<?php echo foodbakery_element_size_data_array_index($single_restaurant_element_size) ?>">
                <?php foodbakery_element_setting($name, $foodbakery_counter, $single_restaurant_element_size) ?>
                <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
		     <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>"
                     data-shortcode-template="[single_restaurant {{attributes}}]{{content}}[/single_restaurant]"
                     style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr($foodbakery_counter) ?>">
                        <h5><?php echo esc_html__("Single Restaurant Options", "foodbakery"); ?></h5>
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
                                    'std' => $single_restaurant_title,
                                    'id' => 'single_restaurant_title',
                                    'cust_name' => 'single_restaurant_title[]',
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
                                    'std' => $single_restaurant_subtitle,
                                    'id' => 'single_restaurant_subtitle',
                                    'cust_name' => 'single_restaurant_subtitle[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            $restaurants_args = array(
                                'post_type' => 'restaurants',
                                'posts_per_page' => -1,
                                'fields' => 'ids',
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => 'foodbakery_restaurant_status',
                                        'value' => 'active',
                                        'compare' => '=',
                                    )
                                )
                            );
                            $restaurants_query = new WP_Query($restaurants_args);

                            $restaurants_list = array('' => esc_html__('--Select Restaurant--', 'foodbakery'));
                            if ($restaurants_query->have_posts()):
                                while ($restaurants_query->have_posts()): $restaurants_query->the_post();
                                    $restaurants_list[get_post_field('post_name', get_the_ID())] = esc_html(get_the_title(get_the_ID()));
                                endwhile;
                            endif;
                            wp_reset_postdata();

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Restaurant', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => __("Select Single Restaurant.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $selected_restaurant,
                                    'id' => 'selected_restaurant',
                                    'cust_name' => 'selected_restaurant[]',
                                    'return' => true,
                                    'classes' => 'chosen-select',
                                    'options' => $restaurants_list
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            if (function_exists('foodbakery_shortcode_custom_classes_test')) {
                                foodbakery_shortcode_custom_dynamic_classes($single_restaurant_custom_class, $single_restaurant_custom_animation, '', 'single_restaurant');
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
                                'std' => 'single_restaurant',
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
                                    'cust_id' => 'single_restaurant_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn',
                                    'cust_name' => 'single_restaurant_save',
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
        if ($die <> 1) {
            die();
        }
    }

    add_action('wp_ajax_foodbakery_var_page_builder_single_restaurant', 'foodbakery_var_page_builder_single_restaurant');
}

if (!function_exists('foodbakery_save_page_builder_data_single_restaurant_callback')) {

    /**
     * Save data for single_restaurant shortcode.
     *
     * @param    array $args
     * @return    array
     */
    function foodbakery_save_page_builder_data_single_restaurant_callback($args)
    {
        $shortcode_data = '';
        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        if ($widget_type == "single_restaurant" || $widget_type == "cs_single_restaurant") {
            $foodbakery_bareber_single_restaurant = '';

            $page_element_size = $data['single_restaurant_element_size'][$counters['foodbakery_global_counter_single_restaurant']];
            $current_element_size = $data['single_restaurant_element_size'][$counters['foodbakery_global_counter_single_restaurant']];

            if (isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode') {
                $shortcode_str = stripslashes(($data['shortcode']['single_restaurant'][$counters['foodbakery_shortcode_counter_single_restaurant']]));

                $element_settings = 'single_restaurant_element_size="' . $current_element_size . '"';
                $reg = '/single_restaurant_element_size="(\d+)"/s';
                $shortcode_str = preg_replace($reg, $element_settings, $shortcode_str);
                $shortcode_data .= $shortcode_str;

                $counters['foodbakery_shortcode_counter_single_restaurant']++;
            } else {
                $element_settings = 'single_restaurant_element_size="' . htmlspecialchars($data['single_restaurant_element_size'][$counters['foodbakery_global_counter_single_restaurant']]) . '"';
                $foodbakery_bareber_single_restaurant = '[single_restaurant ' . $element_settings . ' ';
                if (isset($data['single_restaurant_title'][$counters['foodbakery_counter_single_restaurant']]) && $data['single_restaurant_title'][$counters['foodbakery_counter_single_restaurant']] != '') {
                    $foodbakery_bareber_single_restaurant .= 'single_restaurant_title="' . htmlspecialchars($data['single_restaurant_title'][$counters['foodbakery_counter_single_restaurant']], ENT_QUOTES) . '" ';
                }
                if (isset($data['single_restaurant_subtitle'][$counters['foodbakery_counter_single_restaurant']]) && $data['single_restaurant_subtitle'][$counters['foodbakery_counter_single_restaurant']] != '') {
                    $foodbakery_bareber_single_restaurant .= 'single_restaurant_subtitle="' . htmlspecialchars($data['single_restaurant_subtitle'][$counters['foodbakery_counter_single_restaurant']], ENT_QUOTES) . '" ';
                }
                if (isset($data['selected_restaurant'][$counters['foodbakery_counter_single_restaurant']]) && $data['selected_restaurant'][$counters['foodbakery_counter_single_restaurant']] != '') {
                    $foodbakery_bareber_single_restaurant .= 'selected_restaurant="' . htmlspecialchars($data['selected_restaurant'][$counters['foodbakery_counter_single_restaurant']], ENT_QUOTES) . '" ';
                }

                $foodbakery_bareber_single_restaurant .= ']';
                if (isset($data['single_restaurant_column_text'][$counters['foodbakery_counter_single_restaurant']]) && $data['single_restaurant_column_text'][$counters['foodbakery_counter_single_restaurant']] != '') {
                    $foodbakery_bareber_single_restaurant .= htmlspecialchars($data['single_restaurant_column_text'][$counters['foodbakery_counter_single_restaurant']], ENT_QUOTES) . ' ';
                }
                $foodbakery_bareber_single_restaurant .= '[/single_restaurant]';

                $shortcode_data .= $foodbakery_bareber_single_restaurant;
                $counters['foodbakery_counter_single_restaurant']++;
            }
            $counters['foodbakery_global_counter_single_restaurant']++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter('foodbakery_save_page_builder_data_single_restaurant', 'foodbakery_save_page_builder_data_single_restaurant_callback');
}

if (!function_exists('foodbakery_load_shortcode_counters_single_restaurant_callback')) {

    /**
     * Populate single_restaurant shortcode counter variables.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_load_shortcode_counters_single_restaurant_callback($counters)
    {
        $counters['foodbakery_global_counter_single_restaurant'] = 0;
        $counters['foodbakery_shortcode_counter_single_restaurant'] = 0;
        $counters['foodbakery_counter_single_restaurant'] = 0;
        return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_single_restaurant_callback');
}


if (!function_exists('foodbakery_element_list_populate_single_restaurant_callback')) {

    /**
     * Populate single_restaurant shortcode strings list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_element_list_populate_single_restaurant_callback($element_list)
    {
        $element_list['single_restaurant'] = 'Foodbakery Single Restaurant';
        return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_single_restaurant_callback');
}

if (!function_exists('foodbakery_shortcode_names_list_populate_single_restaurant_callback')) {

    /**
     * Populate single_restaurant shortcode names list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_shortcode_names_list_populate_single_restaurant_callback($shortcode_array)
    {
        $shortcode_array['single_restaurant'] = array(
            'title' => 'FB: Single Restaurant',
            'name' => 'single_restaurant',
            'icon' => 'icon-food',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_single_restaurant_callback');
}
