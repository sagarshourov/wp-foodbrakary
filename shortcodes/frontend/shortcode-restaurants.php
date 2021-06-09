<?php
/**
 * File Type: Restaurants Shortcode Frontend
 */
if (!class_exists('Foodbakery_Shortcode_Restaurants_Frontend')) {

    class Foodbakery_Shortcode_Restaurants_Frontend {

        /**
         * Constant variables
         */
        var $PREFIX = 'foodbakery_restaurants';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_restaurants_shortcode_callback'));
            add_action('wp_ajax_foodbakery_restaurants_content', array($this, 'foodbakery_restaurants_content'));
            add_action('wp_ajax_nopriv_foodbakery_restaurants_content', array($this, 'foodbakery_restaurants_content'));
            add_action('foodbakery_restaurant_pagination', array($this, 'foodbakery_restaurant_pagination_callback'), 11, 1);
        }

        /*
         * Shortcode View on Frontend
         */

        public function foodbakery_restaurants_shortcode_callback($atts, $content = "") {
            $restaurant_short_counter = isset($atts['restaurant_counter']) && $atts['restaurant_counter'] != '' ? ( $atts['restaurant_counter'] ) : rand(123, 9999); // for shortcode counter
            wp_enqueue_script('foodbakery-restaurant-functions');
            $restaurant_view = isset($atts['restaurant_view']) ? $atts['restaurant_view'] : '';
            foodbakery_set_transient_obj('foodbakery_restaurant_view' . $restaurant_short_counter, $restaurant_view);
            $restaurant_map_counter = rand(10000000, 99999999);
            $page_element_size = isset($atts['foodbakery_restaurants_element_size']) ? $atts['foodbakery_restaurants_element_size'] : 100;
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
            }
            ob_start();
            ?>
            <div class="row">
                <div class="foodbakery-restaurant-content" id="foodbakery-restaurant-content-<?php echo esc_html($restaurant_short_counter); ?>">
                    <?php
                    $page_url = get_permalink(get_the_ID());
                    ?>
                    <div class="detail-map-restaurant">
                        <div id="Restaurant-content-<?php echo esc_html($restaurant_short_counter); ?>">
                            <?php
                            $restaurant_arg = array(
                                'restaurant_short_counter' => $restaurant_short_counter,
                                'atts' => $atts,
                                'content' => $content,
                                'restaurant_map_counter' => $restaurant_map_counter,
                                'page_url' => $page_url,
                            );

                            $this->foodbakery_restaurants_content($restaurant_arg);
                            ?>
                        </div>
                    </div> 
                </div>   
            </div>
            <?php
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '</div>';
            }
            return ob_get_clean();
        }

        public function foodbakery_restaurants_content($restaurant_arg = '') {
            global $wpdb, $foodbakery_form_fields_frontend, $foodbakery_plugin_options;


            $atts = isset( $restaurant_arg['atts'] )? $restaurant_arg['atts'] : array();
            $restaurant_short_counter = isset($atts['restaurant_counter']) && $atts['restaurant_counter'] != '' ? ( $atts['restaurant_counter'] ) : rand(123, 9999); // for shortcode counter
            $sort_by_options = array(
                'best_match' => esc_html__('Best Match', 'foodbakery'),
                'alphabetical' => esc_html__('Alphabetical', 'foodbakery'),
                'ratings' => esc_html__('Ratings', 'foodbakery'),
                'minimum_order_value' => esc_html__('Minimum order value', 'foodbakery'),
                'delivery_fee' => esc_html__('Delivery fee', 'foodbakery'),
                'fastest_delivery' => esc_html__('Fastest delivery', 'foodbakery'),
            );

            // getting arg array from ajax
            if (isset($_REQUEST['restaurant_arg']) && $_REQUEST['restaurant_arg']) {
                $restaurant_arg = $_REQUEST['restaurant_arg'];
                $restaurant_arg = json_decode(str_replace('\"', '"', $restaurant_arg));
                $restaurant_arg = $this->toArray($restaurant_arg);
            }
            if (isset($restaurant_arg) && $restaurant_arg != '' && !empty($restaurant_arg)) {
                extract($restaurant_arg);
            }

            $default_date_time_formate = 'd-m-Y H:i:s';
            // getting if user set it with his choice
            if (false === ( $restaurant_view = foodbakery_get_transient_obj('foodbakery_restaurant_view' . $restaurant_short_counter) )) {
                $restaurant_view = isset($atts['restaurant_view']) ? $atts['restaurant_view'] : '';
            }

            $open_close_show_labels = isset($atts['open_close_show_labels']) ? $atts['open_close_show_labels'] : 'no';
            $element_restaurant_sort_by = isset($atts['restaurant_sort_by']) ? $atts['restaurant_sort_by'] : 'no';
            $element_restaurant_search_keyword = isset($atts['restaurant_search_keyword']) ? $atts['restaurant_search_keyword'] : 'no';
            $restaurant_restaurant_featured = isset($atts['restaurant_featured']) ? $atts['restaurant_featured'] : 'all';
            // only fix this with db value
            $foodbakery_post_restaurant_types = new Foodbakery_Post_Restaurant_Types();
            $restaurant_type = $foodbakery_post_restaurant_types->foodbakery_single_types_slug_callback();
            $search_box = isset($atts['search_box']) ? $atts['search_box'] : 'no';
            $posts_per_page = '-1';
            $pagination = 'no';
            $posts_per_page = isset($atts['posts_per_page']) ? $atts['posts_per_page'] : '-1';
            $pagination = isset($atts['pagination']) ? $atts['pagination'] : 'no';
            $filter_arr = '';
            $element_filter_arr = array();
            $content_columns = 'page-content col-lg-12 col-md-12 col-sm-12 col-xs-12'; // if filteration not true
            $paging_var = 'restaurant_page';

            // extra location filters
            $lat_long = array();
            $google_api_key = isset($foodbakery_plugin_options['foodbakery_google_api_key']) ? $foodbakery_plugin_options['foodbakery_google_api_key'] : '';
            if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
                $address = sanitize_text_field($_REQUEST['location']);
                $prepAddr = str_replace(' ', '+', $address);
                $response = wp_remote_get('https://google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false&key= ' . $google_api_key . '  ');
                if (is_array($response)) {
                    $geocode = $response['body']; // use the content
                    $output = json_decode($geocode);
                    if (isset($output->status) && ($output->status == 'OK' || $output->status == 'Ok' || $output->status == 'ok')) {
                        $lat_long['lat'] = $output->results[0]->geometry->location->lat;
                        $lat_long['lng'] = $output->results[0]->geometry->location->lng;
                    } else {
                        $lat_long['lat'] = 0;
                        $lat_long['lng'] = 0;
                    }
                }
            }

//posted date check//

            $element_filter_arr[] = array(
                'key' => 'foodbakery_restaurant_posted',
                'value' => strtotime(date($default_date_time_formate)),
                'compare' => '<=',
            );

            $element_filter_arr[] = array(
                'key' => 'foodbakery_restaurant_expired',
                'value' => strtotime(date($default_date_time_formate)),
                'compare' => '>=',
            );

            $element_filter_arr[] = array(
                'key' => 'foodbakery_restaurant_status',
                'value' => 'active',
                'compare' => '=',
            );

            if ($restaurant_type != '') {
                $element_filter_arr[] = array(
                    'key' => 'foodbakery_restaurant_type',
                    'value' => $restaurant_type,
                    'compare' => '=',
                );
            }
            // if featured restaurant
            if ($restaurant_restaurant_featured == 'only-featured') {
                $element_filter_arr[] = array(
                    'key' => 'foodbakery_restaurant_is_featured',
                    'value' => 'on',
                    'compare' => '=',
                );
            } elseif ($restaurant_restaurant_featured == 'top-category') {
                $element_filter_arr[] = array(
                    'key' => 'foodbakery_restaurant_is_top_cat',
                    'value' => 'on',
                    'compare' => '=',
                );
            }

//            $open_close_filter_switch = isset($atts['open_close_filter_switch']) ? $atts['open_close_filter_switch'] : 'yes';
//            $open_close_default_filter = isset($atts['open_close_default_filter']) ? $atts['open_close_default_filter'] : 'all';
//            $element_restaurant_timings = isset($_REQUEST['restaurant_timings']) ? $_REQUEST['restaurant_timings'] : $open_close_default_filter;
//            if (in_array($element_restaurant_timings, array('open', 'close', 'all')) && $open_close_filter_switch == 'yes') {
//                $today_var = date('l');
//                $today_var = strtolower($today_var);
//                $ststus_str = 'foodbakery_opening_hours_' . $today_var . '_day_status';
//                $opening_time_str = 'foodbakery_opening_hours_' . $today_var . '_opening_time';
//                $closing_time_str = 'foodbakery_opening_hours_' . $today_var . '_closing_time';
//                $current_time = strtotime('2016-01-01 ' . current_time('h:i a'));   // fix date added for time convertion in timestamp
//                if ($element_restaurant_timings == 'open') {
//                    $element_filter_arr[] = array(
//                        'key' => $ststus_str,
//                        'value' => 'on',
//                        'compare' => '=',
//                    );
//                    $element_filter_arr[] = array(
//                        'key' => $opening_time_str,
//                        'value' => $current_time,
//                        'compare' => '<=',
//                    );
//                    $element_filter_arr[] = array(
//                        'key' => $closing_time_str,
//                        'value' => $current_time,
//                        'compare' => '>=',
//                    );
//                } else if ($element_restaurant_timings == 'close') {
//                    $element_filter_arr[] = array(
//                        'relation' => 'OR',
//                        array(
//                            'key' => $ststus_str,
//                            'value' => 'off',
//                            'compare' => '=',
//                        ),
//                        array(
//                            'key' => $opening_time_str,
//                            'value' => $current_time,
//                            'compare' => '>',
//                        ),
//                        array(
//                            'key' => $closing_time_str,
//                            'value' => $current_time,
//                            'compare' => '<',
//                        ),
//                    );
//                }
//            }


            if (!isset($_REQUEST[$paging_var])) {
                $_REQUEST[$paging_var] = '';
            }
            // get all arguments from getting flters
            $left_filter_arr = $this->get_filter_arg($restaurant_type, $restaurant_short_counter);

            $post_ids = '';
            if (!empty($left_filter_arr)) {
                // apply all filters and get ids
                $post_ids = $this->get_listing_id_by_filter($left_filter_arr);
            }
            // print_r( $post_ids);
            // extra location filters
            if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
                $post_ids = $this->restaurant_location_filter($_REQUEST['location'], $post_ids, $lat_long);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
            }

            $post_ids = $this->restaurant_open_close_filter($atts, $post_ids);
            $post_ids = $this->restaurant_pre_order_filter($atts, $post_ids);

            $all_post_ids = '';
            if (!empty($post_ids)) {
                $all_post_ids = $post_ids;
            }

            $search_title = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';
            $restaurant_sort_by = 'best_match'; // default value
            $restaurant_sort_order = 'desc';   // default value
            $qryvar_restaurant_sort_type = 'DESC';
            $qryvar_sort_by_column = 'post_date';
            $meta_key_sort_by = '';
            if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
                $restaurant_sort_by = $_REQUEST['sort-by'];
            }
            if ($restaurant_sort_by == 'best_match') {
                $qryvar_restaurant_sort_type = 'DESC';
                $qryvar_sort_by_column = 'post_date';
            } elseif ($restaurant_sort_by == 'alphabetical') {
                $qryvar_restaurant_sort_type = 'ASC';
                $qryvar_sort_by_column = 'post_title';
            } elseif ($restaurant_sort_by == 'ratings' || $restaurant_sort_by == 'minimum_order_value' || $restaurant_sort_by == 'delivery_fee' || $restaurant_sort_by == 'fastest_delivery') {
                $qryvar_restaurant_sort_type = '';
                $qryvar_sort_by_column = '';
                $meta_value_data_type = 'string';
                $meta_key_sort_order = 'DESC';
                if ($restaurant_sort_by == 'ratings') {
                    $meta_value_data_type = 'int';
                    $meta_key_sort_by = 'overall_ratings';
                } elseif ($restaurant_sort_by == 'minimum_order_value') {
                    $meta_key_sort_order = 'ASC';
                    $meta_value_data_type = 'int';
                    $meta_key_sort_by = 'foodbakery_minimum_order_value';
                } elseif ($restaurant_sort_by == 'delivery_fee') {
                    $meta_key_sort_order = 'ASC';
                    $meta_value_data_type = 'int';
                    $meta_key_sort_by = 'foodbakery_delivery_fee';
                } elseif ($restaurant_sort_by == 'foodbakery_delivery_time') {
                    $meta_key_sort_order = 'ASC';
                    $meta_value_data_type = 'int';
                    $meta_key_sort_by = 'foodbakery_delivery_time';
                }
            }

            $args = array(
                'posts_per_page' => $posts_per_page,
                'paged' => $_REQUEST[$paging_var],
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                's' => $search_title,
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    $element_filter_arr,
                ),
            );
            // for count args
            $args_count = array(
                'posts_per_page' => $posts_per_page,
                'paged' => $_REQUEST[$paging_var],
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                's' => $search_title,
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    $element_filter_arr,
                ),
            );
            if (!empty($qryvar_restaurant_sort_type) && !empty($qryvar_sort_by_column)) {
                $args['order'] = $qryvar_restaurant_sort_type;
                $args['orderby'] = $qryvar_sort_by_column;
            } else {
                $key = 'meta_value';
                if ($meta_value_data_type == 'int') {
                    $key = 'meta_value_num';
                }
                $args['order'] = $meta_key_sort_order;
                $args['meta_key'] = $meta_key_sort_by;
                $args['orderby'] = $key;
            }

            if (!empty($all_post_ids)) {
                $args['post__in'] = $all_post_ids; //array(15888, 15777, 1702,1705,1560,1544,1538,1538,1492);
                $args_count['post__in'] = $all_post_ids;
            }

            $restaurant_loop_obj = foodbakery_get_cached_obj('restaurant_result_cached_loop_obj', $args, 12, false, 'wp_query');

            $restaurant_totnum = $restaurant_loop_obj->found_posts;
            $restaurant_found_count = $restaurant_loop_obj->found_posts;
            ?>
            <form id="frm_restaurant_arg<?php echo intval($restaurant_short_counter); ?>">
                <div style="display:none" id='restaurant_arg<?php echo intval($restaurant_short_counter); ?>'><?php

                    echo json_encode($restaurant_arg);
                    ?>
                </div>
                <input type="hidden" name="search_type" value="<?php echo isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : ''; ?>">
                <?php
                if ($search_box == 'yes') {  // if sidebar on from element
                    set_query_var('restaurant_type', $restaurant_type);
                    set_query_var('restaurant_short_counter', $restaurant_short_counter);
                    set_query_var('restaurant_arg', $restaurant_arg);
                    set_query_var('args_count', $args_count);
                    set_query_var('atts', $atts);
                    set_query_var('lat_long', $lat_long);
                    foodbakery_get_template_part('restaurant', 'leftfilters', 'restaurants');
                    $content_columns = 'col-lg-10 col-md-10 col-sm-12 col-xs-12';
                    if ((isset($atts['restaurant_sort_by']) && $atts['restaurant_sort_by'] == 'yes') || (isset($atts['restaurant_ads_switch']) && $atts['restaurant_ads_switch'] == 'yes') || (isset($atts['right_sidebar']) && $atts['right_sidebar'] == 'yes' && is_active_sidebar('restaurents-element-right-sidebar'))) {
                        $content_columns = 'col-lg-7 col-md-7 col-sm-12 col-xs-12';
                    }
                } else {
                    if ((isset($atts['restaurant_sort_by']) && $atts['restaurant_sort_by'] == 'yes') || (isset($atts['restaurant_ads_switch']) && $atts['restaurant_ads_switch'] == 'yes') || (isset($atts['right_sidebar']) && $atts['right_sidebar'] == 'yes' && is_active_sidebar('restaurents-element-right-sidebar'))) {
                        $content_columns = 'col-lg-9 col-md-9 col-sm-12 col-xs-12';
                    }
                }
                ?>
                <div class="<?php echo esc_html($content_columns); ?>">
                    <?php
                    $restaurants_title = isset($atts['restaurants_title']) ? $atts['restaurants_title'] : '';
                    $restaurants_subtitle = isset($atts['restaurants_subtitle']) ? $atts['restaurants_subtitle'] : '';
                    $foodbakery_var_restaurants_align = isset($atts['foodbakery_var_restaurants_align']) ? $atts['foodbakery_var_restaurants_align'] : '';
                    //echo $foodbakery_var_restaurants_align . '====';
                    if ($restaurants_title != '' || $restaurants_subtitle != '') {
                        ?>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="element-title <?php echo esc_html($foodbakery_var_restaurants_align); ?>">
                                    <?php if ($restaurants_title != '') { ?>
                                        <h2><?php echo esc_html($restaurants_title); ?></h2>
                                        <?php
                                    }
                                    if ($restaurants_subtitle != '') {
                                        ?>
                                        <p><?php echo esc_html($restaurants_subtitle); ?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    // search keywords 
                    $page_url = isset( $page_url )? $page_url : '';
                    $this->restaurant_search_keywords($restaurant_totnum, $element_restaurant_search_keyword, $_REQUEST, $atts, $page_url);
                    if ($restaurant_view == 'list') {
                        ?>
                        <div class="listing simple">
                            <?php
                        }
                        set_query_var('restaurant_loop_obj', $restaurant_loop_obj);
                        set_query_var('restaurant_view', $restaurant_view);
                        set_query_var('restaurant_short_counter', $restaurant_short_counter);
                        set_query_var('atts', $atts);
                        set_query_var('lat_long', $lat_long);
                        
                        
                        if (isset($restaurant_view) && $restaurant_view == 'fancy') {
                            foodbakery_get_template_part('restaurant', 'fancy', 'restaurants');
                        } else if (isset($restaurant_view) && $restaurant_view == 'list') {
                            foodbakery_get_template_part('restaurant', 'list', 'restaurants');
                        } else if (isset($restaurant_view) && $restaurant_view == 'simple') {
                            foodbakery_get_template_part('restaurant', 'simple', 'restaurants');
                        } else if (isset($restaurant_view) && $restaurant_view == 'fancy-grid') {
                            foodbakery_get_template_part('restaurant', 'fancy-grid', 'restaurants');
                        } else if (isset($restaurant_view) && $restaurant_view == 'classic-grid') {
                            foodbakery_get_template_part('restaurant', 'classic-grid', 'restaurants');
                        } else if (isset($restaurant_view) && $restaurant_view == 'grid-slider') {
                            foodbakery_get_template_part('restaurant', 'grid-slider', 'restaurants');
                        }
                        else { // for grid and view 2
                            foodbakery_get_template_part('restaurant', 'grid', 'restaurants');
                        }
                        if ($restaurant_view == 'list') {
                            
                            
                            
                            ?>
                        </div>
                        <?php
                    }
                    // apply paging
                    $paging_args = array(
                        'total_posts' => $restaurant_totnum,
                        'posts_per_page' => $posts_per_page,
                        'paging_var' => $paging_var,
                        'show_pagination' => $pagination,
                        'restaurant_short_counter' => $restaurant_short_counter,
                    );
                    wp_reset_postdata();
                    if ($pagination != 'no') {
                        ?>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <?php
                                $this->foodbakery_restaurant_pagination_callback($paging_args);
                                ?>
                            </div>
                        </div> 
                    <?php } ?>
                </div>
                <!-- Column Start -->
                <?php
                if ((isset($atts['restaurant_sort_by']) && $atts['restaurant_sort_by'] == 'yes') || (isset($atts['restaurant_ads_switch']) && $atts['restaurant_ads_switch'] == 'yes') || (isset($atts['right_sidebar']) && $atts['right_sidebar'] == 'yes' && is_active_sidebar('restaurents-element-right-sidebar'))) {
                    ?>
                    <div class="section-sidebar col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?php if (isset($atts['restaurant_sort_by']) && $atts['restaurant_sort_by'] == 'yes') { ?>
                            <div class="order-sort-results">
                                <h5><?php echo esc_html__('Sort By', 'foodbakery'); ?></h5>
                                <ul>
                                    <?php foreach ($sort_by_options as $key => $val) : ?>
                                        <?php
                                        $class = '';
                                        if (isset($restaurant_sort_by) && $restaurant_sort_by == $key) {
                                            $class = 'active';
                                        }
                                        ?>
                                        <li class="<?php echo esc_html($class); ?>">
                                            <a href="javascript:void(0);" class="sort-by-<?php echo esc_html($key); ?>" data-key="<?php echo esc_html($key); ?>" <?php echo 'onclick="restaurants_sort_by(\'sort-by-' . $key . '\', ' . $restaurant_short_counter . ')"'; ?>>
                                                <?php
                                                if ($key == 'best_match') {
                                                    ?>
                                                    <i class="icon-thumbs-up2"></i>
                                                    <?php
                                                }if ($key == 'alphabetical') {
                                                    ?>
                                                    <i class="icon-sort-alpha-asc"></i>
                                                    <?php
                                                } if ($key == 'ratings') {
                                                    ?>
                                                    <i class="icon-star-o"></i>
                                                    <?php
                                                } if ($key == 'minimum_order_value') {
                                                    ?>
                                                    <i class="icon-user-minus"></i>
                                                    <?php
                                                } if ($key == 'delivery_fee') {
                                                    ?>
                                                    <i class="icon-dollar"></i>
                                                    <?php
                                                } if ($key == 'fastest_delivery') {
                                                    ?>
                                                    <i class="icon-fast-forward"></i>
                                                    <?php
                                                }
                                                ?>
                                                <?php echo esc_html($val); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php } ?>
                        <?php
                        if (isset($atts['restaurant_ads_switch']) && $atts['restaurant_ads_switch'] == 'yes') {
                            do_action('foodbakery_random_ads', 'restaurant_banner_leftfilter');
                        }
                        if (isset($atts['right_sidebar']) && $atts['right_sidebar'] == 'yes' && is_active_sidebar('restaurents-element-right-sidebar')) {
                            dynamic_sidebar('restaurents-element-right-sidebar');
                        }
                        ?> 
                    </div>
                    <!-- Column End -->
                    <?php
                }
                ?>
            </form>


            <script>

                jQuery(window).ready(function () {
                    // function Listing_Filter_li() {
                    /*Main Categories List Show Hide*/
                    if (jQuery(".listing-filter ul.filter-list").length != '') {
                        jQuery('.listing-filter ul.filter-list').each(function () {
                            var $ul = $(this),
                                    $lis = $ul.find('li:gt(7)'),
                                    isExpanded = $ul.hasClass('expanded');


                            $lis[isExpanded ? 'show' : 'hide']();
                            if ($lis.length > 0) {
                                $ul
                                        .append($('<li class="expand">' + (isExpanded ? '<?php echo esc_html__('Less cuisines', 'foodbakery'); ?>' : '<?php echo esc_html__('See more cuisines', 'foodbakery'); ?>') + '</li>')
                                                .click(function (event) {
                                                    var isExpanded = $ul.hasClass('expanded');
                                                    event.preventDefault();
                                                    $(this).text(isExpanded ? '<?php echo esc_html__('See more cuisines', 'foodbakery'); ?>' : '<?php echo esc_html__('Less cuisines', 'foodbakery'); ?>');
                                                    $ul.toggleClass('expanded');
                                                    $lis.toggle(350);
                                                }));
                            }
                        });
                    }
                    /*Main Categories List Show Hide End*/

                });
                if (jQuery('.chosen-select, .chosen-select-deselect, .chosen-select-no-single, .chosen-select-no-results, .chosen-select-width').length != '') {
                    var config = {
                        '.chosen-select': {width: "100%"},
                        '.chosen-select-deselect': {allow_single_deselect: true},
                        '.chosen-select-no-single': {disable_search_threshold: 4, width: "100%"},
                        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                        '.chosen-select-width': {width: "95%"}
                    };
                    for (var selector in config) {
                        jQuery(selector).chosen(config[selector]);
                    }
                }
            </script>

            <?php
            // only for ajax request
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'foodbakery_restaurants_content') {
                die();
            }
        }

        public function get_filter_arg($restaurant_type, $restaurant_short_counter = '', $exclude_meta_key = '') {
            global $foodbakery_post_restaurant_types;
            $filter_arr = array();

            $restaurant_type_category_name = 'foodbakery_restaurant_category';   // category_fieldname in db and request
            if ($exclude_meta_key != $restaurant_type_category_name) {
                if (isset($_REQUEST[$restaurant_type_category_name]) && $_REQUEST[$restaurant_type_category_name] != '') {
                    $dropdown_query_str_var_name = explode(",", $_REQUEST[$restaurant_type_category_name]);
                    $cate_filter_multi_arr ['relation'] = 'OR';
                    foreach ($dropdown_query_str_var_name as $query_str_var_name_key) {
                        $cate_filter_multi_arr[] = array(
                            'key' => $restaurant_type_category_name,
                            'value' => serialize($query_str_var_name_key),
                            'compare' => 'LIKE',
                        );
                    }
                    if (isset($cate_filter_multi_arr) && !empty($cate_filter_multi_arr)) {
                        $filter_arr[] = array(
                            $cate_filter_multi_arr
                        );
                    }
                }
            }

            if (isset($restaurant_type) && $restaurant_type != '') {
                $foodbakery_restaurant_type_cus_fields = $foodbakery_post_restaurant_types->foodbakery_types_custom_fields_array($restaurant_type);
                $foodbakery_fields_output = '';
                if (is_array($foodbakery_restaurant_type_cus_fields) && sizeof($foodbakery_restaurant_type_cus_fields) > 0) {
                    $custom_field_flag = 1;
                    foreach ($foodbakery_restaurant_type_cus_fields as $cus_fieldvar => $cus_field) {
                        if (isset($cus_field['enable_srch']) && $cus_field['enable_srch'] == 'on') {
                            $query_str_var_name = $cus_field['meta_key'];
                            // only for date type field need to change field name
                            if ($exclude_meta_key != $query_str_var_name) {
                                if ($cus_field['type'] == 'date') {
                                    if ($cus_field['type'] == 'date') {
                                        $from_date = 'from' . $query_str_var_name;
                                        $to_date = 'to' . $query_str_var_name;
                                        if (isset($_REQUEST[$from_date]) && $_REQUEST[$from_date] != '') {
                                            $filter_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => strtotime($_REQUEST[$from_date]),
                                                'compare' => '>=',
                                            );
                                        }
                                        if (isset($_REQUEST[$to_date]) && $_REQUEST[$to_date] != '') {
                                            $filter_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => strtotime($_REQUEST[$to_date]),
                                                'compare' => '<=',
                                            );
                                        }
                                    }
                                } else if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] != '') {

                                    if ($cus_field['type'] == 'dropdown') {
                                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                            $filter_multi_arr ['relation'] = 'OR';
                                            $dropdown_query_str_var_name = explode(",", $_REQUEST[$query_str_var_name]);
                                            foreach ($dropdown_query_str_var_name as $query_str_var_name_key) {
                                                if ($cus_field['post_multi'] == 'on') {
                                                    $filter_multi_arr[] = array(
                                                        'key' => $query_str_var_name,
                                                        'value' => serialize($query_str_var_name_key),
                                                        'compare' => 'Like',
                                                    );
                                                } else {
                                                    $filter_multi_arr[] = array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $query_str_var_name_key,
                                                        'compare' => '=',
                                                    );
                                                }
                                            }
                                            $filter_arr[] = array(
                                                $filter_multi_arr
                                            );
                                        } else {
                                            if ($cus_field['post_multi'] == 'on') {

                                                $filter_arr[] = array(
                                                    'key' => $query_str_var_name,
                                                    'value' => serialize($_REQUEST[$query_str_var_name]),
                                                    'compare' => 'Like',
                                                );
                                            } else {
                                                $filter_arr[] = array(
                                                    'key' => $query_str_var_name,
                                                    'value' => $_REQUEST[$query_str_var_name],
                                                    'compare' => '=',
                                                );
                                            }
                                        }
                                    } elseif ($cus_field['type'] == 'text' || $cus_field['type'] == 'email' || $cus_field['type'] == 'url' || $cus_field['type'] == 'number') {
                                        $filter_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => $_REQUEST[$query_str_var_name],
                                            'compare' => 'LIKE',
                                        );
                                    } elseif ($cus_field['type'] == 'range') {
                                        $ranges_str_arr = explode(",", $_REQUEST[$query_str_var_name]);
                                        if (!isset($ranges_str_arr[1])) {
                                            $ranges_str_arr = explode(",", $ranges_str_arr[0]);
                                        }
                                        $range_first = $ranges_str_arr[0];
                                        $range_seond = $ranges_str_arr[1];
                                        $filter_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => $range_first,
                                            'compare' => '>=',
                                            'type' => 'numeric'
                                        );
                                        $filter_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => $range_seond,
                                            'compare' => '<=',
                                            'type' => 'numeric'
                                        );
                                    }
                                }
                            }
                        }
                        $custom_field_flag ++;
                    }
                }
            }

            return $filter_arr;
        }

        public function get_listing_id_by_filter($left_filter_arr) {
            global $wpdb;
            $meta_post_ids_arr = '';
            $restaurant_id_condition = '';

            if (isset($left_filter_arr) && !empty($left_filter_arr)) {
                $meta_post_ids_arr = foodbakery_get_query_whereclase_by_array($left_filter_arr);
                // if no result found in filtration 
                if (empty($meta_post_ids_arr)) {
                    $meta_post_ids_arr = array(0);
                }
                $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
                $restaurant_id_condition = " ID in (" . $ids . ") AND ";
            }
            $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $restaurant_id_condition . " post_type='restaurants' AND post_status='publish'");

            if (empty($post_ids)) {
                $post_ids = array(0);
            }
            return $post_ids;
        }

        public function restaurant_search_keywords($restaurant_totnum = '', $element_restaurant_search_keyword = '', $qrystr = '', $atts = array(), $page_url = '') {

            if ((isset($atts['restaurant_search_keyword']) && $atts['restaurant_search_keyword'] == 'yes') || (isset($atts['foodbakery_var_restaurants_total_num']) && $atts['foodbakery_var_restaurants_total_num'] == 'yes')) {

                if ((isset($qrystr) && !empty($qrystr))) { //&& $restaurant_totnum != ''
                    ?>

                    <div class="listing-sorting-holder">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                <?php
                                $total_reataurant_count = isset($atts['foodbakery_var_restaurants_total_num']) ? $atts['foodbakery_var_restaurants_total_num'] : 'no';
                                if (isset($total_reataurant_count) && $total_reataurant_count == 'yes') {
                                    ?>
                                    <h4>
                                        <?php
                                        echo esc_html($restaurant_totnum) . ' ';
                                        if ($restaurant_totnum > 1) {
                                            echo esc_html__("Restaurant's found", 'foodbakery');
                                        } else {
                                            echo esc_html__("Restaurant found", 'foodbakery');
                                        }
                                        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
                                            echo esc_html__(' in', 'foodbakery') . ' ' . esc_html($_REQUEST['location']);
                                        }
                                        ?>
                                    </h4>
                                    <?php
                                }
                                if (($element_restaurant_search_keyword != 'no')) { //&& $restaurant_totnum != ''
                                    if ($element_restaurant_search_keyword != 'no') {
                                        if (isset($qrystr)) {

                                            echo '<ul class="search-results">';

                                            // get all query string
                                            $reset_var = 0;
                                            $flag = 1;

                                            foreach (array_unique($qrystr) as $qry_var => $qry_val) {

                                                if ('restaurant_page' == $qry_var || 'search_type' == $qry_var || 'foodbakery_locations_position' == $qry_var || 'restaurant_type' == $qry_var || 'ajax_filter' == $qry_var || 'restaurant_arg' == $qry_var || 'action' == $qry_var || 'alert-frequency' == $qry_var || 'alerts-name' == $qry_var || 'loc_polygon' == $qry_var || 'alerts-email' == $qry_var)
                                                    continue;

                                                if ($qry_val != '') {
                                                    $flag ++;
                                                    echo '<li>';
                                                    echo '"' . esc_html(ucwords(str_replace("-", " ", str_replace("+", " ", $qry_val)))) . '"';
                                                    if (count($qrystr) > $flag) {
                                                        echo ', ';
                                                    }
                                                    echo '</li>';
                                                    $reset_var ++;
                                                }
                                            }

                                            echo '</ul>';
                                        }
                                        if (isset($reset_var) && $reset_var > 0) {
                                            ?>
                                            <a class="clear-tags" href="<?php echo esc_url($page_url); ?>"><?php esc_html_e('Reset', 'foodbakery') ?></a>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            }
        }

           public function restaurant_location_filter($location_slug, $all_post_ids, $lat_long = array()) {

            $foodbakery_radius = isset($_REQUEST['foodbakery_radius']) ? $_REQUEST['foodbakery_radius'] : 0;
             $search_type = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : 'custom';

            $location_condition_arr = array();
            if ($search_type == 'custom') {
                if (isset($location_slug) && $location_slug != '') {
                    $location_condition_arr[] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'foodbakery_post_loc_country_restaurant',
                            'value' => $location_slug,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'foodbakery_post_loc_state_restaurant',
                            'value' => $location_slug,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'foodbakery_post_loc_city_restaurant',
                            'value' => $location_slug,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'foodbakery_post_loc_town_restaurant',
                            'value' => $location_slug,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'foodbakery_post_loc_address_restaurant',
                            'value' => $location_slug,
                            'compare' => 'LIKE',
                        ),
                    );
                }
            } else {
                $foodbakery_radius = isset($_REQUEST['foodbakery_radius']) ? $_REQUEST['foodbakery_radius'] : 10;
                $foodbakery_radius = preg_replace("/[^0-9,.]/", "", $foodbakery_radius);
                
                $Latitude = '';
                $Longitude = '';
                $prepAddr = '';
                $minLat = '';
                $maxLat = '';
                $minLong = '';
                $maxLong = '';
                if (isset($lat_long) && !empty($lat_long)) {
                    $Latitude = $lat_long['lat'];
                    $Longitude = $lat_long['lng'];
                    $zcdRadius = new RadiusCheck($Latitude, $Longitude, $foodbakery_radius);
                    $minLat = $zcdRadius->MinLatitude();
                    $maxLat = $zcdRadius->MaxLatitude();
                    $minLong = $zcdRadius->MinLongitude();
                    $maxLong = $zcdRadius->MaxLongitude();
                }
                $foodbakery_compare_type = 'CHAR';
                if ($foodbakery_radius > 0) {
                    $foodbakery_compare_type = 'DECIMAL(10,6)';
                }

                if ($minLat != '' && $maxLat != '' && $minLong != '' && $maxLong != '') {

                    $location_condition_arr = array(
                        'relation' => 'AND',
                        array(
                            'key' => 'foodbakery_post_loc_latitude_restaurant',
                            'value' => array($minLat, $maxLat),
                            'compare' => 'BETWEEN',
                            'type' => $foodbakery_compare_type
                        ),
                        array(
                            'key' => 'foodbakery_post_loc_longitude_restaurant',
                            'value' => array($minLong, $maxLong),
                            'compare' => 'BETWEEN',
                            'type' => $foodbakery_compare_type
                        ),
                    );
                }
            }
            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    $location_condition_arr,
                ),
            );


            if (!empty($all_post_ids)) {
                $args_count['post__in'] = $all_post_ids;
            }

            $location_rslt = get_posts($args_count);
            return $location_rslt;
        }

        public function restaurant_open_close_filter($atts, $all_post_ids, $exclude_meta_key = '', $direct_count_value_for = '') {

            //if ($exclude_meta_key != 'restaurant_timings') {
            $open_close_filter_switch = isset($atts['open_close_filter_switch']) ? $atts['open_close_filter_switch'] : 'yes';
            $open_close_default_filter = isset($atts['open_close_default_filter']) ? $atts['open_close_default_filter'] : 'all';
            // check request come from count query or main query and for open close box or other filter boxes
            if ($direct_count_value_for == '') {
                $element_restaurant_timings = isset($_REQUEST['restaurant_timings']) ? $_REQUEST['restaurant_timings'] : $open_close_default_filter;
            } else {
                $element_restaurant_timings = $direct_count_value_for;
            }
            if (in_array($element_restaurant_timings, array('open', 'close', 'all')) && $open_close_filter_switch == 'yes') {
                $today_var = date('l');
                $today_var = strtolower($today_var);
                $ststus_str = 'foodbakery_opening_hours_' . $today_var . '_day_status';
                $opening_time_str = 'foodbakery_opening_hours_' . $today_var . '_opening_time';
                $closing_time_str = 'foodbakery_opening_hours_' . $today_var . '_closing_time';
                $current_time = strtotime('2016-01-01 ' . current_time('h:i a'));   // fix date added for time convertion in timestamp
                $element_filter_arr = array();
                if ($element_restaurant_timings == 'open') {
                    $element_filter_arr[] = array(
                        'key' => $ststus_str,
                        'value' => 'on',
                        'compare' => '=',
                    );
                    $element_filter_arr[] = array(
                        'key' => $opening_time_str,
                        'value' => $current_time,
                        'compare' => '<=',
                    );
                    $element_filter_arr[] = array(
                        'key' => $closing_time_str,
                        'value' => $current_time,
                        'compare' => '>=',
                    );
                } else if ($element_restaurant_timings == 'close') {
                    $element_filter_arr[] = array(
                        'relation' => 'OR',
                        array(
                            'key' => $ststus_str,
                            'value' => 'off',
                            'compare' => '=',
                        ),
                        array(
                            'key' => $opening_time_str,
                            'value' => $current_time,
                            'compare' => '>',
                        ),
                        array(
                            'key' => $closing_time_str,
                            'value' => $current_time,
                            'compare' => '<',
                        ),
                    );
                }

                $args_count = array(
                    'posts_per_page' => "-1",
                    'post_type' => 'restaurants',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => array(
                        $element_filter_arr,
                    ),
                );
                if (!empty($all_post_ids)) {
                    $args_count['post__in'] = $all_post_ids;
                }
                $post_ids = get_posts($args_count);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
                return $post_ids;
            }
            //}
            return $all_post_ids;
        }

        public function restaurant_pre_order_filter($atts, $all_post_ids, $exclude_meta_key = '', $direct_count_value_for = '') {

            $pre_order_filter_switch = isset($atts['pre_order_filter_switch']) ? $atts['pre_order_filter_switch'] : 'yes';  // yes
            //$pre_order_default_filter = isset($atts['open_close_default_filter']) ? $atts['open_close_default_filter'] : 'all';
            $pre_order_default_filter = 'all';
            //echo 'ids===$direct_count_value_for'.$direct_count_value_for;print_r($all_post_ids);
            // check request come from count query or main query and for pre order box or other filter boxes
            if ($direct_count_value_for == '') {
                $element_restaurant_pre_order = isset($_REQUEST['restaurant_pre_order']) ? $_REQUEST['restaurant_pre_order'] : $pre_order_default_filter;
            } else {
                $element_restaurant_pre_order = $direct_count_value_for;
            }
            if (in_array($element_restaurant_pre_order, array('yes', 'no', 'all')) && $pre_order_filter_switch == 'yes') {
                $element_filter_arr = array();
                if ($element_restaurant_pre_order == 'yes' || $element_restaurant_pre_order == 'no') {
                    $element_filter_arr[] = array(
                        'key' => 'foodbakery_restaurant_pre_order',
                        'value' => $element_restaurant_pre_order,
                        'compare' => '=',
                    );
                }

                $args_count = array(
                    'posts_per_page' => "-1",
                    'post_type' => 'restaurants',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => array(
                        $element_filter_arr,
                    ),
                );
                if (!empty($all_post_ids)) {
                    $args_count['post__in'] = $all_post_ids;
                }
                $post_ids = get_posts($args_count);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
                return $post_ids;
            }
            return $all_post_ids;
        }

        public function toArray($obj) {
            if (is_object($obj)) {
                $obj = (array) $obj;
            }
            if (is_array($obj)) {
                $new = array();
                foreach ($obj as $key => $val) {
                    $new[$key] = $this->toArray($val);
                }
            } else {
                $new = $obj;
            }

            return $new;
        }

        public function foodbakery_restaurant_pagination_callback($args) {
            global $foodbakery_form_fields_frontend;
            $total_posts = '';
            $posts_per_page = '5';
            $paging_var = 'paged_id';
            $show_pagination = 'yes';
            $restaurant_short_counter = '';

            extract($args);
            if ($show_pagination <> 'yes') {
                return;
            } else if ($total_posts <= $posts_per_page) {
                return;
            } else {
                if (!isset($_REQUEST['page_id'])) {
                    $_REQUEST['page_id'] = '';
                }
                $html = '';
                $dot_pre = '';
                $dot_more = '';
                $total_page = 0;
                if ($total_posts <> 0)
                    $total_page = ceil($total_posts / $posts_per_page);
                $paged_id = 1;
                if (isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '') {
                    $paged_id = $_REQUEST[$paging_var];
                }
                $loop_start = $paged_id - 2;

                $loop_end = $paged_id + 2;

                if ($paged_id < 3) {

                    $loop_start = 1;

                    if ($total_page < 5)
                        $loop_end = $total_page;
                    else
                        $loop_end = 5;
                }
                else if ($paged_id >= $total_page - 1) {

                    if ($total_page < 5)
                        $loop_start = 1;
                    else
                        $loop_start = $total_page - 4;

                    $loop_end = $total_page;
                }
                $current_url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $html .= $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                        array(
                            'simple' => true,
                            'cust_id' => $paging_var . '-' . $restaurant_short_counter,
                            'cust_name' => $paging_var,
                            'std' => '',
                            'extra_atr' => 'onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
                        )
                );
                $html .= '<div class="page-nation"><ul class="pagination pagination-large">';
                if ($paged_id > 1) {
                    $html .= '<li><a onclick="foodbakery_restaurant_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id - 1) . '\', \'' . ($restaurant_short_counter) . '\');" href="javascript:void(0);">';
                    $html .= esc_html__('Prev', 'foodbakery') . ' </a></li>';
                } else {
                    $html .= '<li class="disabled"><span>' . esc_html__('Prev', 'foodbakery') . '</span></li>';
                }

                if ($paged_id > 3 and $total_page > 5) {
                    $html .= '<li><a onclick="foodbakery_restaurant_pagenation_ajax(\'' . $paging_var . '\', \'' . (1) . '\', \'' . ($restaurant_short_counter) . '\');" href="javascript:void(0);">';
                    $html .= '1</a></li>';
                }
                if ($paged_id > 4 and $total_page > 6) {
                    $html .= '<li class="disabled"><span><a>. . .</a></span><li>';
                }

                if ($total_page > 1) {

                    for ($i = $loop_start; $i <= $loop_end; $i ++) {

                        if ($i <> $paged_id) {
                            $html .= '<li><a onclick="foodbakery_restaurant_pagenation_ajax(\'' . $paging_var . '\', \'' . ($i) . '\', \'' . ($restaurant_short_counter) . '\');" href="javascript:void(0);">';
                            $html .= $i . '</a></li>';
                        } else {
                            $html .= '<li class="active"><span><a class="page-numbers active">' . $i . '</a></span></li>';
                        }
                    }
                }
                if ($loop_end <> $total_page and $loop_end <> $total_page - 1) {
                    $html .= '<li><a>. . .</a></li>';
                }
                if ($loop_end <> $total_page) {
                    $html .= '<li><a onclick="foodbakery_restaurant_pagenation_ajax(\'' . $paging_var . '\', \'' . ($total_page) . '\', \'' . ($restaurant_short_counter) . '\');" href="javascript:void(0);">';
                    $html .= $total_page . '</a></li>';
                }
                if ($total_posts > 0 and $paged_id < ($total_posts / $posts_per_page)) {
                    $html .= '<li><a onclick="foodbakery_restaurant_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id + 1) . '\', \'' . ($restaurant_short_counter) . '\');" href="javascript:void(0);">';
                    $html .= esc_html__('Next', 'foodbakery') . '</a></li>';
                } else {
                    $html .= '<li class="disabled"><span>' . esc_html__('Next', 'foodbakery') . '</span></li> ';
                }
                $html .= "</ul></div>";
                echo force_balance_tags($html);
            }
        }

        public function foodbakery_restaurant_filter_categories($restaurant_type, $category_request_val) {
            $foodbakery_restaurant_type_category_array = '';
            $parent_cate_array = '';
            if ($category_request_val != '') {
                $category_request_val_arr = explode(",", $category_request_val);
                $category_request_val = isset($category_request_val_arr[0]) && $category_request_val_arr[0] != '' ? $category_request_val_arr[0] : '';
                $single_term = get_term_by('slug', $category_request_val, 'restaurant-category');
                $single_term_id = isset($single_term->term_id) && $single_term->term_id != '' ? $single_term->term_id : '0';
                $parent_cate_array = $this->foodbakery_restaurant_parent_categories($single_term_id);
            }
            $foodbakery_restaurant_type_category_array = $this->foodbakery_restaurant_categories_list($restaurant_type, $parent_cate_array);
            return $foodbakery_restaurant_type_category_array;
        }

        public function foodbakery_restaurant_parent_categories($category_id) {
// get category parent id 
            $parent_cate_array = '';
            $category_obj = get_term_by('id', $category_id, 'restaurant-category');
            if (isset($category_obj->parent) && $category_obj->parent != '0') {
                $parent_cate_array .= $this->foodbakery_restaurant_parent_categories($category_obj->parent);
            }
            $parent_cate_array .= isset($category_obj->slug) ? $category_obj->slug . ',' : '';
            return $parent_cate_array;
        }

        public function foodbakery_restaurant_categories_list($restaurant_type, $parent_cate_string) {
            $cate_list_found = 0;
            $foodbakery_restaurant_type_category_array = array();
            if ($parent_cate_string != '') {
                $category_request_val_arr = explode(",", $parent_cate_string);
                $count_arr = sizeof($category_request_val_arr);
                while ($count_arr >= 0) {
                    if (isset($category_request_val_arr[$count_arr]) && $category_request_val_arr[$count_arr] != '') {
                        if ($cate_list_found == 0) {
                            $single_term = get_term_by('slug', $category_request_val_arr[$count_arr], 'restaurant-category');
                            $single_term_id = isset($single_term->term_id) && $single_term->term_id != '' ? $single_term->term_id : '0';
                            $foodbakery_category_array = get_terms('restaurant-category', array(
                                'hide_empty' => false,
                                'parent' => $single_term_id,
                                    )
                            );
                            if (is_array($foodbakery_category_array) && sizeof($foodbakery_category_array) > 0) {
                                foreach ($foodbakery_category_array as $dir_tag) {
                                    $foodbakery_restaurant_type_category_array['cate_list'][] = $dir_tag->slug;
                                }
                                $cate_list_found ++;
                            }
                        }if ($cate_list_found > 0) {
                            $foodbakery_restaurant_type_category_array['parent_list'][] = $category_request_val_arr[$count_arr];
                        }
                    }
                    $count_arr --;
                }
            }

            if ($cate_list_found == 0 && $restaurant_type != '') {
                $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type", 'post_status' => 'publish', 'fields' => 'ids'));
                $restaurant_type_post_id = isset($restaurant_type_post[0]) ? $restaurant_type_post[0] : 0;
                $foodbakery_restaurant_type_category_array['cate_list'] = get_post_meta($restaurant_type_post_id, 'foodbakery_restaurant_type_cats', true);
            }
            return $foodbakery_restaurant_type_category_array;
        }

        public function foodbakery_restaurant_body_classes($classes) {
            $classes[] = 'restaurant-with-full-map';
            return $classes;
        }

        public function foodbakery_restaurant_map_coords_obj($restaurant_ids) {
            $map_cords = array();

            if (is_array($restaurant_ids) && sizeof($restaurant_ids) > 0) {
                foreach ($restaurant_ids as $restaurant_id) {
                    global $foodbakery_publisher_profile;

                    $Foodbakery_Locations = new Foodbakery_Locations();
                    $restaurant_type = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
                    $restaurant_type_obj = get_page_by_path($restaurant_type, OBJECT, 'restaurant-type');
                    $restaurant_type_id = isset($restaurant_type_obj->ID) ? $restaurant_type_obj->ID : '';
                    $restaurant_location = $Foodbakery_Locations->get_location_by_restaurant_id($restaurant_id);
                    $foodbakery_restaurant_username = get_post_meta($restaurant_id, 'foodbakery_restaurant_username', true);
                    $foodbakery_profile_image = $foodbakery_publisher_profile->publisher_get_profile_image($foodbakery_restaurant_username);
                    $restaurant_latitude = get_post_meta($restaurant_id, 'foodbakery_post_loc_latitude_restaurant', true);
                    $restaurant_longitude = get_post_meta($restaurant_id, 'foodbakery_post_loc_longitude_restaurant', true);
                    $restaurant_marker = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_marker_image', true);

                    if ($restaurant_marker != '') {
                        $restaurant_marker = wp_get_attachment_url($restaurant_marker);
                    } else {
                        $restaurant_marker = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/map-marker.png');
                    }

                    $foodbakery_restaurant_is_featured = get_post_meta($restaurant_id, 'foodbakery_restaurant_is_featured', true);

                    $foodbakery_restaurant_price_options = get_post_meta($restaurant_id, 'foodbakery_restaurant_price_options', true);
                    $foodbakery_restaurant_type = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
                    $foodbakery_transaction_restaurant_reviews = get_post_meta($restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);

                    $foodbakery_restaurant_type_price_switch = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_price', true);
                    $foodbakery_user_reviews = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);

                    // end checking review on in restaurant type

                    $foodbakery_restaurant_price = '';
                    if ($foodbakery_restaurant_price_options == 'price') {
                        $foodbakery_restaurant_price = get_post_meta($restaurant_id, 'foodbakery_restaurant_price', true);
                    } else if ($foodbakery_restaurant_price_options == 'on-call') {
                        $foodbakery_restaurant_price = esc_html__('Price On Request', 'foodbakery');
                    }

                    if (has_post_thumbnail()) {
                        $img_atr = array('class' => 'img-map-info');
                        $restaurant_info_img = get_the_post_thumbnail($restaurant_id, 'foodbakery_media_5', $img_atr);
                    } else {
                        $no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image4x3.jpg');
                        $restaurant_info_img = '<img class="img-map-info" src="' . $no_image_url . '" />';
                    }

                    $restaurant_info_price = '';
                    if ($foodbakery_restaurant_type_price_switch == 'on' && $foodbakery_restaurant_price != '') {
                        $restaurant_info_price .= '
						<span class="restaurant-price">
							<span class="new-price text-color">';

                        if ($foodbakery_restaurant_price_options == 'on-call') {
                            $restaurant_info_price .= $foodbakery_restaurant_price;
                        } else {
                            $restaurant_info_price .= foodbakery_get_currency($foodbakery_restaurant_price, true);
                        }
                        $restaurant_info_price .= '	
							</span>
						</span>';
                    }
                    $restaurant_info_address = '';
                    if ($restaurant_location != '') {
                        $restaurant_info_address = '<span class="info-address">' . $restaurant_location . '</span>';
                    }

                    $cur_user_details = wp_get_current_user();
                    $user_company_id = get_user_meta($cur_user_details->ID, 'foodbakery_company', true);
                    $publisher_profile_type = get_post_meta($user_company_id, 'foodbakery_publisher_profile_type', true);

                    if ($publisher_profile_type != 'restaurant') {

                        ob_start();
                        $shortlist_label = '';
                        $shortlisted_label = '';
                        $figcaption_div = true;
                        $book_mark_args = array(
                            'before_label' => $shortlist_label,
                            'after_label' => $shortlisted_label,
                            'before_icon' => '<i class="icon-heart5"></i>',
                            'after_icon' => '<i class="icon-heart6"></i>',
                        );
                        do_action('foodbakery_shortlists_frontend_button', $restaurant_id, $book_mark_args, $figcaption_div);
                        $list_shortlist = ob_get_clean();
                    } else {
                        $list_shortlist = '';
                    }

                    $restaurant_featured = '';
                    if ($foodbakery_restaurant_is_featured == 'on') {
                        $restaurant_featured .= '
						<div class="featured-restaurant">
							<span class="bgcolor">' . esc_html__('Featured', 'foodbakery') . '</span>
						</div>';
                    }

                    $restaurant_publisher = $foodbakery_restaurant_username != '' && get_the_title($foodbakery_restaurant_username) != '' ? '<span class="info-publisher">' . sprintf(esc_html__('Publisher: %s'), get_the_title($foodbakery_restaurant_username)) . '</span>' : '';

                    $ratings_data = array(
                        'overall_rating' => 0.0,
                        'count' => 0,
                    );
                    $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $restaurant_id);

                    $restaurant_reviews = '';
                    if ($foodbakery_transaction_restaurant_reviews == 'on' && $foodbakery_user_reviews == 'on' && $ratings_data['count'] > 0) {
                        $restaurant_reviews .= '
						<div class="post-rating">
							<div class="rating-holder">
								<div class="rating-star">
									<span class="rating-box" style="width: ' . $ratings_data['overall_rating'] . '%;"></span>
								</div>
								<span class="ratings"><span class="rating-text">(' . $ratings_data['count'] . ') ' . esc_html__('Reviews', 'foodbakery') . '</span></span>
							</div>
						</div>';
                    }

                    if ($restaurant_latitude != '' && $restaurant_longitude != '') {
                        $map_cords[] = array(
                            'lat' => $restaurant_latitude,
                            'long' => $restaurant_longitude,
                            'id' => $restaurant_id,
                            'title' => get_the_title($restaurant_id),
                            'link' => get_permalink($restaurant_id),
                            'img' => $restaurant_info_img,
                            'price' => $restaurant_info_price,
                            'address' => $restaurant_info_address,
                            'shortlist' => $list_shortlist,
                            'featured' => $restaurant_featured,
                            'reviews' => $restaurant_reviews,
                            'publisher' => $restaurant_publisher,
                            'marker' => $restaurant_marker,
                        );
                    }
                }
            }
            return $map_cords;
        }

    }

    global $foodbakery_shortcode_restaurants_frontend;
    $foodbakery_shortcode_restaurants_frontend = new Foodbakery_Shortcode_Restaurants_Frontend();
}

/*
 * Class start for location check search 
 */
if(!class_exists('RadiusCheck')){
    class RadiusCheck {

    var $maxLat;
    var $minLat;
    var $maxLong;
    var $minLong;

    // Start function for radius search 
    function __construct($Latitude, $Longitude, $Miles) {
        global $maxLat, $minLat, $maxLong, $minLong;
        $EQUATOR_LAT_MILE = 69.172; // in MIles
        $maxLat = $Latitude + $Miles / $EQUATOR_LAT_MILE;
        $minLat = $Latitude - ($maxLat - $Latitude);
        $maxLong = $Longitude + $Miles / (cos($minLat * M_PI / 180) * $EQUATOR_LAT_MILE);
        $minLong = $Longitude - ($maxLong - $Longitude);
    }

    // Start function for get max latitude 
    function MaxLatitude() {
        return $GLOBALS["maxLat"];
    }

    // Start function for get Min latitude 
    function MinLatitude() {
        return $GLOBALS["minLat"];
    }

    // Start function for get Max Longitude

    function MaxLongitude() {
        return $GLOBALS["maxLong"];
    }

    // Start function for get Min Longitude
    function MinLongitude() {
        return $GLOBALS["minLong"];
    }

    }
}
// Start Class for Distance Check
class DistanceCheck {

    function __construct() {
        
    }

// Start function for calculate distance 
    function Calculate($dblLat1, $dblLong1, $dblLat2, $dblLong2) {
        $EARTH_RADIUS_MILES = 3963;
        $dist = 0;
        //convert degrees to radians
        $dblLat1 = $dblLat1 * M_PI / 180;
        $dblLong1 = $dblLong1 * M_PI / 180;
        $dblLat2 = $dblLat2 * M_PI / 180;
        $dblLong2 = $dblLong2 * M_PI / 180;
        if ($dblLat1 != $dblLat2 || $dblLong1 != $dblLong2) {
            //the two points are not the same
            $dist = sin($dblLat1) * sin($dblLat2) + cos($dblLat1) * cos($dblLat2) * cos($dblLong2 - $dblLong1);
            $dist = $EARTH_RADIUS_MILES * (-1 * atan($dist / sqrt(1 - $dist * $dist)) + M_PI / 2);
        }
        return $dist;
    }

}
