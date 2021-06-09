<?php
/**
 * Start Function how to
 * Add User Image for Avatar
 */
if (!function_exists('foodbakery_get_user_avatar')) {

    function foodbakery_get_user_avatar($size = 0, $foodbakery_user_id = '') {

        if ($foodbakery_user_id != '') {

            $foodbakery_user_avatars = get_the_author_meta('user_avatar_display', $foodbakery_user_id);
            if (is_array($foodbakery_user_avatars) && isset($foodbakery_user_avatars[$size])) {
                return $foodbakery_user_avatars[$size];
            } else if (!is_array($foodbakery_user_avatars) && $foodbakery_user_avatars <> '') {
                return $foodbakery_user_avatars;
            }
        }
    }

}
if (!function_exists('cs_widget_register')) {

    function cs_widget_register($name) {

        add_action('widgets_init', function () use ($name) {
            return register_widget($name);
        });
    }

}
if (!function_exists('cs_get_server_data')) {

    function cs_get_server_data($server_data) {
        if (isset($server_data)) {

            return $_SERVER[$server_data];
        }
    }

}
if (!function_exists('foodbakery_open_close_status')) {

    function foodbakery_open_close_status($id = '') {
        $current_time = strtotime('2016-01-01 ' . current_time('h:i a'));
        $restaurant_open = false;
        $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
        if (isset($restaurants_type_post[0]->ID)) {
            $restaurants_type_open_hours = get_post_meta($restaurants_type_post[0]->ID, 'foodbakery_opening_hours_element', true);
            if ($restaurants_type_open_hours != 'on') {
                $restaurant_open = true;
            }
        }
        $today_var = date('l');
        $today_var = strtolower($today_var);
        $restaurant_today_status = get_post_meta($id, 'foodbakery_opening_hours_' . $today_var . '_day_status', true);
        $restaurant_open_time_today = get_post_meta($id, 'foodbakery_opening_hours_' . $today_var . '_opening_time', true);
        $restaurant_close_time_today = get_post_meta($id, 'foodbakery_opening_hours_' . $today_var . '_closing_time', true);

        if ($restaurant_today_status == 'on' && $restaurant_open_time_today != '' && $restaurant_close_time_today != '') {
            if ($restaurant_close_time_today > $restaurant_open_time_today && $current_time >= $restaurant_open_time_today && $current_time <= $restaurant_close_time_today) {
                $restaurant_open = true;
            }
        }
        if ($restaurant_open === true) {
            $restaurant_status = esc_html__('Open', 'foodbakery');
            $restaurant_class = 'open';
        } else {
            $restaurant_status = esc_html__('Closed', 'foodbakery');
            $restaurant_class = 'close';
        }
        return array($restaurant_status, $restaurant_class);
    }

}

if (!function_exists('foodbakery_related_restaurants')) {

    function foodbakery_related_restaurants($number_post = '-1') {

        global $post, $foodbakery_var_static_text, $foodbakery_post_restaurant_types;
        // check related posts on/off.
        $post_cats = get_the_terms($post->ID, 'restaurant-category');
        $cat_id = array();
        if (!empty($post_cats)) {
            foreach ($post_cats as $post_cat) {
                $cat_id[] = $post_cat->term_id;
            }
        }
        $tags_id = array();
        $tag_ids = get_the_terms($post->ID, 'restaurant-tag');
        if (!empty($tag_ids)) {
            foreach ($tag_ids as $tag_id) {
                $tags_id[] = $tag_id->term_id;
            }
        }
        $args = array(
            'post_type' => 'restaurants',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'restaurant-category',
                    'terms' => $cat_id,
                ),
                array(
                    'taxonomy' => 'restaurant-tag',
                    'terms' => $tags_id,
                ),
            ),
            'posts_per_page' => $number_post,
            'post__not_in' => array($post->ID),
        );
        $rel_qry = new WP_Query($args);
        if ($rel_qry->have_posts()) {
            $flag = 1;
            ?>
            <div class="swiper-container restaurant-slider foodbakery-restaurant foodbakery-restaurant-detail">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="element-title">
                        <h3><?php esc_html_e('Related Restaurant', 'foodbakery'); ?> </h3>
                    </div>
                </div>
                <div class="swiper-wrapper">
                    <?php
                    $list_count = 1;
                    while ($rel_qry->have_posts()) : $rel_qry->the_post();
                        global $post, $foodbakery_publisher_profile;
                        $restaurant_id = $post->ID;

                        $Foodbakery_Locations = new Foodbakery_Locations();
                        $restaurant_location = $Foodbakery_Locations->get_location_by_restaurant_id($restaurant_id);
                        $foodbakery_restaurant_username = get_post_meta($restaurant_id, 'foodbakery_restaurant_username', true);
                        $foodbakery_restaurant_is_featured = get_post_meta($restaurant_id, 'foodbakery_restaurant_is_featured', true);
                        $foodbakery_profile_image = $foodbakery_publisher_profile->publisher_get_profile_image($foodbakery_restaurant_username);
                        $foodbakery_restaurant_price_options = get_post_meta($restaurant_id, 'foodbakery_restaurant_price_options', true);
                        $foodbakery_restaurant_type = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
                        $foodbakery_transaction_restaurant_reviews = get_post_meta($restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);
                        // checking review in on in restaurant type
                        $foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
                        if ($restaurant_type_post = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type'))
                            $restaurant_type_id = $restaurant_type_post->ID;
                        $restaurant_type_id = isset($restaurant_type_id) ? $restaurant_type_id : '';
                        $foodbakery_user_reviews = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);
                        $foodbakery_restaurant_type_price_switch = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_price', true);
                        // end checking review on in restaurant type
                        $foodbakery_restaurant_price = '';
                        if ($foodbakery_restaurant_price_options == 'price') {
                            $foodbakery_restaurant_price = get_post_meta($restaurant_id, 'foodbakery_restaurant_price', true);
                        } else if ($foodbakery_restaurant_price_options == 'on-call') {
                            $foodbakery_restaurant_price = 'Price On Request';
                        }
                        // get all categories
                        $foodbakery_cate = '';
                        $foodbakery_cate_str = '';
                        $foodbakery_restaurant_category = get_post_meta($restaurant_id, 'foodbakery_restaurant_category', true);
                        if (!empty($foodbakery_restaurant_category) && is_array($foodbakery_restaurant_category)) {
                            $comma_flag = 0;
                            foreach ($foodbakery_restaurant_category as $cate_slug => $cat_val) {
                                $foodbakery_cate = get_term_by('slug', $cat_val, 'restaurant-category');

                                if (!empty($foodbakery_cate)) {
                                    if ($comma_flag != 0) {
                                        $foodbakery_cate_str .= ', ';
                                    }
                                    $foodbakery_cate_str .= $foodbakery_cate->name;
                                    $comma_flag++;
                                }
                            }
                        }
                        ?>
                        <div class="swiper-slide col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="restaurant-grid">
                                <div class="img-holder">
                                    <figure>
                                        <?php
                                        if (has_post_thumbnail()) {
                                            $img_atr = array('class' => 'img-grid');
                                            the_post_thumbnail('foodbakery_media_4', $img_atr);
                                            $img_atr = array('class' => 'img-list');
                                            the_post_thumbnail('foodbakery_media_5', $img_atr);
                                        } else {
                                            $no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image9x6.jpg');
                                            $no_image = '<img class="img-grid" src="' . $no_image_url . '" />';
                                            echo force_balance_tags($no_image);
                                            // restaurant image
                                            $no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image4x3.jpg');
                                            $no_image = '<img class="img-list" src="' . $no_image_url . '" />';
                                            echo force_balance_tags($no_image);
                                        }
                                        $cur_user_details = wp_get_current_user();
                                        $user_company_id = get_user_meta($cur_user_details->ID, 'foodbakery_company', true);
                                        $publisher_profile_type = get_post_meta($user_company_id, 'foodbakery_publisher_profile_type', true);
                                        if ($publisher_profile_type != 'restaurant') {
                                            ?>
                                            <figcaption>
                                                <?php
                                                $shortlist_label = '';
                                                $shortlisted_label = '';
                                                $figcaption_div = true;
                                                $book_mark_args = array(
                                                    'before_label' => $shortlist_label,
                                                    'after_label' => $shortlisted_label,
                                                    'before_icon' => '<i class="icon-heart5"></i>',
                                                    'after_icon' => '<i class="icon-heart6"></i>',
                                                );
                                                do_action('foodbakery_shortlists_frontend_button', $restaurant_id, $book_mark_args);
                                                ?>
                                            </figcaption>
                                            <?php
                                        }
                                        ?>
                                    </figure>
                                </div>
                                <div class="text-holder">
                                    <?php if ($foodbakery_restaurant_type_price_switch == 'on' && $foodbakery_restaurant_price != '') { ?>
                                        <span class="restaurant-price">
                                            <span class="new-price text-color">
                                                <?php
                                                if ($foodbakery_restaurant_price_options == 'on-call') {
                                                    echo force_balance_tags($foodbakery_restaurant_price);
                                                } else {
                                                    echo force_balance_tags(foodbakery_get_currency($foodbakery_restaurant_price, true));
                                                }
                                                ?>
                                            </span>
                                        </span>
                                        <?php
                                    }
                                    if ($foodbakery_cate_str != '') {
                                        ?>
                                        <div class="post-category-options">
                                            <ul>
                                                <?php if ($foodbakery_restaurant_is_featured == 'on') { ?>
                                                    <li class="featured-restaurant">
                                                        <span class="bgcolor">featured</span>
                                                    </li>
                                                <?php } ?>
                                                <li><a href="javascript:void(0);"><?php echo esc_html($foodbakery_cate_str); ?></a></li>
                                            </ul>
                                        </div>
                                    <?php } ?>

                                    <div class="post-title">
                                        <h4><a href="<?php echo esc_url(get_permalink($restaurant_id)); ?>"><?php echo esc_html(get_the_title($restaurant_id)); ?></a></h4>
                                        <?php if ($foodbakery_restaurant_is_featured == 'on') { ?><div class="feature-check"><i class="icon-check2"></i></div><?php } ?>
                                    </div>
                                    <?php
                                    $ratings_data = array(
                                        'overall_rating' => 0.0,
                                        'count' => 0,
                                    );
                                    $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $restaurant_id);
                                    ?>
                                    <?php if ($ratings_data['count'] > 0) { ?>
                                        <div class="post-rating">
                                            <div class="rating-holder">
                                                <div class="rating-star">
                                                    <span class="rating-box" style="width: <?php echo intval($ratings_data['overall_rating']); ?>%;"></span>
                                                </div>
                                                <span class="ratings"><span class="rating-text">(<?php echo esc_html($ratings_data['count']); ?>) <?php echo esc_html__('Reviews', 'foodbakery'); ?></span></span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if ($restaurant_location != '') {
                                        ?>
                                        <ul class="restaurant-location">
                                            <li><i class="icon-location-pin2"></i><span><?php echo esc_html($restaurant_location); ?></span></li>
                                        </ul>
                                        <?php
                                    }
                                    // first 4 custom fields with value
                                    $foodbakery_restaurant_type_cus_fields = $foodbakery_post_restaurant_types->foodbakery_types_custom_fields_array($foodbakery_restaurant_type);
                                    $foodbakery_fields_output = '';
                                    if (is_array($foodbakery_restaurant_type_cus_fields) && sizeof($foodbakery_restaurant_type_cus_fields) > 0) {
                                        ?>
                                        <div class="post-category-list">
                                            <ul>
                                                <?php
                                                $custom_field_flag = 1;
                                                foreach ($foodbakery_restaurant_type_cus_fields as $cus_fieldvar => $cus_field) {
                                                    $custom_meta_key = $cus_field['meta_key'];
                                                    if ($custom_meta_key != '') {
                                                        $value = get_post_meta($restaurant_id, $custom_meta_key, true);
                                                        if ($value != '') {
                                                            $icon_str = '';
                                                            $icon = $cus_field['fontawsome_icon'];
                                                            if ($icon != '') {
                                                                $icon_str = '<i class="' . $icon . '"></i>';
                                                            }

                                                            if (!empty($value)) {
                                                                ?>
                                                                <li><?php
                                                                    echo force_balance_tags($icon_str) . '';

                                                                    if (!is_array($value)) {
                                                                        echo esc_html(ucwords(str_replace("-", " ", $value)));
                                                                    } else {
                                                                        foreach ($value as $val) {
                                                                            echo esc_html(ucwords(str_replace("-", " ", $val))), ', ';
                                                                        }
                                                                    }
                                                                    ?></li>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    $custom_field_flag++;
                                                    if ($custom_field_flag > 4) {
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $list_count++;
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
            <?php
        }
    }

}
/**
 * Start Function how to get Custom Loaction for search element

 */
if (!function_exists('foodbakery_get_custom_locations_listing_filter')) {


    // field_type value = filter or header
    function foodbakery_get_custom_locations_listing_filter($dropdown_start_html = '', $dropdown_end_html = '', $foodbakery_text_ret = false, $listing_short_counter = '', $field_type = 'filter', $dropdown_type = '', $onchange_function = '', $restaurant_search_view = '') {
        global $foodbakery_plugin_options, $foodbakery_form_fields_frontend;
        // getting from plugin options
        $gmap_api_key = isset($foodbakery_plugin_options['foodbakery_google_api_key']) ? $foodbakery_plugin_options['foodbakery_google_api_key'] : '';
        $foodbakery_default_locations_list = isset($foodbakery_plugin_options['foodbakery_default_locations_list']) ? $foodbakery_plugin_options['foodbakery_default_locations_list'] : array();
        $foodbakery_search_result_page = isset($foodbakery_plugin_options['foodbakery_search_result_page']) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
        $redirecturl = isset($foodbakery_search_result_page) && $foodbakery_search_result_page != '' ? get_permalink($foodbakery_search_result_page) . '' : '';
        $default_radius = isset($foodbakery_plugin_options['foodbakery_default_radius_circle']) ? $foodbakery_plugin_options['foodbakery_default_radius_circle'] : 0;
        $geo_location_status = isset($foodbakery_plugin_options['foodbakery_map_geo_location']) ? $foodbakery_plugin_options['foodbakery_map_geo_location'] : '';
        $auto_country_detection = isset($foodbakery_plugin_options['foodbakery_map_auto_country_detection']) ? $foodbakery_plugin_options['foodbakery_map_auto_country_detection'] : '';
        $auto_complete = isset($foodbakery_plugin_options['foodbakery_location_autocomplete']) ? $foodbakery_plugin_options['foodbakery_location_autocomplete'] : '';
        $output = '';
        $selected_item = '';
        if ($dropdown_type == 'list') {
            $output = '';
        } else {
            $selected_item .= '<option value="">' . esc_html__('All Locations', 'foodbakery') . '</option>';
        }
        $selected_location = '';
        if (is_array($foodbakery_default_locations_list) && sizeof($foodbakery_default_locations_list) > 0) {
            foreach ($foodbakery_default_locations_list as $tag_r) {
                $tag_obj = get_term_by('slug', $tag_r, 'foodbakery_locations');
                if (is_object($tag_obj)) {
                    if ($dropdown_type == 'list') {
                        $activ_class = '';
                        if (isset($_REQUEST['location']) && $_REQUEST['location'] == $tag_obj->slug) {
                            $activ_class = ' class="active"';
                        }
                        $selected_item .= '<li' . $activ_class . '><a href="' . add_query_arg(array('location' => $tag_obj->slug), $redirecturl) . '">' . $tag_obj->name . '</a></li>';
                    } else {
                        $selected_item .= '<option value="' . $tag_obj->slug . '" >' . $tag_obj->name . '</option>';
                    }
                }
            }
        }

        $onchange_str = 'foodbakery_empty_loc_polygon(\'' . esc_html($listing_short_counter) . '\');foodbakery_restaurant_content(\'' . esc_html($listing_short_counter) . '\')';
        if ($field_type != 'filter') {

            if ($redirecturl != '') {
                $onchange_str = 'foodbakery_page_load(this, \'' . esc_html($redirecturl) . '\')';
            } else {
                $onchange_str = '';
            }
        }

        wp_enqueue_script('chosen-ajaxify');
        $location_slug = '';
        if (isset($_REQUEST['loc_polygon']) && $_REQUEST['loc_polygon'] != '') {
            if ($dropdown_type != 'list') {
                $selected_item .= '<option selected value="">' . esc_html__("Drawn Area", "foodbakery") . '</option>';
            }
        } else
        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
            $location_slug = $_REQUEST['location'];
            if ($dropdown_type != 'list') {
                $selected_item .= '<option selected value="' . $location_slug . '">' . ucwords(str_replace("-", " ", $location_slug)) . '</option>';
            }
        }
        if ($dropdown_type == 'list') {
            $output .= $selected_item;
            $output .= '';
        } else {
            $location_value = ( isset($_REQUEST['location']) ) ? $_REQUEST['location'] : '';
            if (isset($_REQUEST['loc_polygon']) && $_REQUEST['loc_polygon'] != '') {
                $location_value .= esc_html__("Drawn Area", "foodbakery");
            }
            $focus_class = '';
            $location_field_text = '';

            $output .= '<li class="select-location">';
            if ($field_type == 'header') {
                
            } else {
                $focus_class = 'foodbakery-focus-out';
                $location_field_text = 'location-field-text';
            }
            $output .= '<div class="foodbakery-locations-fields-group ' . $focus_class . '">';

            $location_cross_display = ( isset($_REQUEST['location']) ) ? 'block' : 'none';
            if (is_home() || is_front_page()) {
                if (isset($restaurant_search_view) && $restaurant_search_view == 'classic') {
                    $output .= '<span class="foodbakery-search-location-icon" data-id="' . $listing_short_counter . '"></span>';
                } else {
                    $output .= '<span class="foodbakery-search-location-icon" data-id="' . $listing_short_counter . '"><i class="icon-location"></i></span>';
                }
            }
            $output .= '<span class="foodbakery-input-cross foodbakery-input-cross' . $listing_short_counter . '" data-id="' . $listing_short_counter . '" style="display:' . $location_cross_display . ';"><i class="icon-cross"></i></span>';
            $output .= '<span id="foodbakery-radius-location' . $listing_short_counter . '" class="foodbakery-radius-location foodbakery-radius-location' . $listing_short_counter . '" data-id="' . $listing_short_counter . '"><i class="icon-target5"></i></span>';
            if ($auto_complete == 'on' && $field_type != 'header') {
                $output .= '<input type="text" class="' . $location_field_text . ' foodbakery-locations-field-geo' . $listing_short_counter . ' ' . $field_type . '" data-id="' . $listing_short_counter . '" value="' . $location_value . '" id="foodbakery-locations-field" name="location" placeholder="' . esc_html__('All Locations', 'foodbakery') . '" autocomplete="off">';
            } else {
                $output .= '<input type="text" class="' . $location_field_text . ' foodbakery-locations-field' . $listing_short_counter . ' ' . $field_type . '" data-id="' . $listing_short_counter . '" value="' . $location_value . '" id="foodbakery-locations-field" name="location" placeholder="' . esc_html__('All Locations', 'foodbakery') . '" autocomplete="off">';
            }
            $output .= '<input type="hidden" class="foodbakery-locations-position' . $listing_short_counter . '" value="' . $field_type . '" id="foodbakery-locations-position' . $listing_short_counter . '" name="foodbakery_locations_position">';
            $search_type = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : 'autocomplete';
            $output .= '<input type="hidden" name="search_type" class="search_type" value="' . $search_type . '">';
            $output .= '</div>';
            $radius = isset($_REQUEST['foodbakery_radius']) ? $_REQUEST['foodbakery_radius'] : $default_radius;

            if ($geo_location_status == 'on') {

                $radius_display = 'none';
                $output .= '<div class="select-location foodbakery-radius-range' . $listing_short_counter . '" style="display:' . $radius_display . '"><div class="select-popup popup-open" id="popup' . $listing_short_counter . '"> <a href="javascript:;" id="close' . $listing_short_counter . '" class="location-close-popup location-close-popup' . $listing_short_counter . '"><i class="icon-times"></i></a>';
                $output .= $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                        array(
                            'simple' => true,
                            'cust_id' => "range-hidden-foodbakery-radius" . $listing_short_counter,
                            'cust_name' => "foodbakery_radius",
                            'std' => $radius,
                            'classes' => "foodbakery-radius",
                            'return' => true,
                            'extra_atr' => 'data-id="' . $listing_short_counter . '"',
                        )
                );
                $output .= '<p>' . esc_html__('Show with in', 'foodbakery') . '</p>
                                <input id="ex16b' . $listing_short_counter . '" type="text" />
								<span id="ex16b' . $listing_short_counter . 'CurrentSliderValLabel">' . esc_html__('Miles', 'foodbakery') . ': <span id="ex16b' . $listing_short_counter . 'SliderVal">' . $radius . '</span></span>';
                $output .= '<br><p class="my-location">' . esc_html__('of', 'foodbakery') . ' <i class="cs-color icon-location-arrow"></i><a id="foodbakery-geo-location' . $listing_short_counter . '" class="cs-color foodbakery-geo-location' . $listing_short_counter . '" href="javascript:void(0)">' . esc_html__('My location', 'foodbakery') . '</a></p>
                    </div></div>';

                $foodbakery_restaurant_content_call = '';
                if (!( is_home() || is_front_page() )) {
                    $foodbakery_restaurant_content_call = 'foodbakery_restaurant_content("' . esc_html($listing_short_counter) . '");';
                }

                $output .= '<script>
                        jQuery(document).ready(function() {
		        var elem = jQuery("#ex16b' . $listing_short_counter . '");
                        if (elem.length != "") {
                            elem.slider({
                                step : 1,
                                min: 0,
                                max: 500,
                                value: ' . $radius . ',
                            });
                            elem.on("slideStop", function () {
								var val = elem.val();
								jQuery("#range-hidden-foodbakery-radius' . $listing_short_counter . '").val( val );
								jQuery("#ex16b' . $listing_short_counter . 'SliderVal").html( val );
								
								' . $foodbakery_restaurant_content_call . '
                            });
                            elem.on("slide", function (e, ui) {
								jQuery("#ex16b' . $listing_short_counter . 'SliderVal").html( elem.val() );
                            });   

                            }
							$(".location-close-popup' . $listing_short_counter . '").click(function() {
								$(".foodbakery-radius-range' . $listing_short_counter . '").hide();
							});
                        });
                    </script>';
            }
            //}
        }
        if ($dropdown_type != 'list') {
            if (false === ( $foodbakery_location_data = get_transient('foodbakery_location_data') )) {
                $output .= '<script>
				jQuery(document).ready(function () {
					jQuery(".chosen-select-location").chosen();
					chosen_ajaxify("filter-location-box' . $listing_short_counter . '", "' . esc_url(admin_url('admin-ajax.php')) . '", "dropdown_options_for_search_location_data");
				});
				</script>';
            } else {
                $output .= '<script>
				jQuery(document).ready(function () {
					$("#filter-location-box' . $listing_short_counter . '").after(\'<span class="chosen-ajaxify-loader"><img src="' . wp_foodbakery::plugin_url() . 'assets/frontend/images/ajax-loader.gif" alt=""></span>\');                
					var location_data_json = \'' . str_replace("'", "", $foodbakery_location_data) . '\';
					var location_data_json_obj = JSON.parse(location_data_json);
					jQuery.each(location_data_json_obj, function() {
						var location_selected = \'\';
						if(this.value == \'' . $location_slug . '\'){
							location_selected = \'selected\';
						}
						jQuery("#filter-location-box' . $listing_short_counter . '").append(
                            jQuery("<option" + location_selected + "></option>").text(this.caption).val(this.value)
						);
					});
					$("#filter-location-box' . $listing_short_counter . '").next("span.chosen-ajaxify-loader").remove();
				});
				</script>';
            }
        }

        $output .= '<script>
                jQuery(document).ready(function () {
                    jQuery(".chosen-select-location").chosen();
                    /*
                    * Locations search
                    */
                    $(document).on("keyup", ".foodbakery-locations-field' . $listing_short_counter . '", function () {
					   if ( $(this).hasClass("filter") ) { return; }
                       var this_value  = jQuery(this).val();
                       var this_position  = jQuery("#foodbakery-locations-position' . $listing_short_counter . '").val();
                       if( this_value.length > 2 ){
                           jQuery.ajax({
                               type: "POST",
                               url: foodbakery_globals.ajax_url,
                               data: "this_position="+this_position+"&keyword="+this_value+"&action=foodbakery_get_all_locations",
                               success: function (response) {
                                   jQuery(".foodbakery-all-locations' . $listing_short_counter . '").html(response);
                               }
                           });
                       }else{
                           jQuery.ajax({
                               type: "POST",
                               url: foodbakery_globals.ajax_url,
                               data: "this_position="+this_position+"&action=foodbakery_get_all_default_locations",
                               success: function (response) {
                                   jQuery(".foodbakery-all-locations' . $listing_short_counter . '").html(response);
                               }
                           });
                       }
                   });

                   $(document).on("focus", ".foodbakery-locations-field' . $listing_short_counter . '", function () {
                       jQuery("#range-hidden-foodbakery-radius").val(0);
                       jQuery(".foodbakery-radius-range").hide();
                       jQuery(this).keyup();
                   });

                   $(document).on("click", ".foodbakery-all-locations' . $listing_short_counter . ' li", function () {
                       var this_value  = jQuery(this).text();
                       jQuery(".foodbakery-locations-field' . $listing_short_counter . '").val(this_value);
                       var this_position  = jQuery("#foodbakery-locations-position' . $listing_short_counter . '").val();
                       if( this_position != "header" ){
                            var data_counter    = jQuery(".foodbakery-locations-field' . $listing_short_counter . '").data("id");
                            jQuery("#range-hidden-foodbakery-radius' . $listing_short_counter . '").val(0);
                            jQuery(".foodbakery-radius-range").hide();
                            foodbakery_restaurant_content(data_counter);
                       }
                   });
				   $(document).on("keypress", ".foodbakery-locations-fields-group input[name=\'location\']", function (e) {
						var key = e.keyCode || e.which;
						if (key == 13){ 
							$(".location-field-text").val($(this).val());
							$("#range-hidden-foodbakery-radius' . $listing_short_counter . '").val(0);
							$(".location-field-text").parents("form").submit();
						}
				   });
                   $(document).on("click", "body", function () {
                        var data_id     = jQuery(".location-field-text").data("id");
                        jQuery(".foodbakery-all-locations"+data_id).html("");
                   });
                   
                    $(document).on("click", ".foodbakery-input-cross' . $listing_short_counter . '", function () {
                        var data_id = jQuery(this).data("id");
                        jQuery(".foodbakery-locations-field"+data_id).val("");
                        jQuery(".foodbakery-locations-field"+data_id).keyup();
                        jQuery(".foodbakery-locations-field-geo"+data_id).val("");
                        jQuery(".foodbakery-locations-field-geo"+data_id).keyup();
                        jQuery("body").click();
                        //jQuery("#range-hidden-foodbakery-radius' . $listing_short_counter . '").val(0);
                        jQuery(".foodbakery-radius-range").hide();
                        //foodbakery_restaurant_content(data_id);
                        jQuery(this).hide();
                    });
                    
                    $(document).on("change", ".location-field-text", function(){
                        this_text   = jQuery(this).val();
                        if(this_text == ""){
							var data_id = jQuery(this).data("id");
                            jQuery(".foodbakery-input-cross" + data_id).hide();

                        }
							' . $onchange_function . '
                    });
                    
                    $(document).on("click", ".foodbakery-geo-location' . $listing_short_counter . '", function () {
                    var gmap_api_key  = "' . $gmap_api_key . '";
			var radiu_val = jQuery("#range-hidden-foodbakery-radius' . $listing_short_counter . '").val();
						var _this_f = $(this);
                        var data_id = jQuery(this).data("id");
                        jQuery(".foodbakery-locations-field-geo"+data_id).val("");
                        jQuery("#range-hidden-foodbakery-radius' . $listing_short_counter . '").val(radiu_val);
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                
								jQuery.ajax({
									url: "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + position.coords.latitude + "," + position.coords.longitude + "&sensor=true&key="+gmap_api_key,
									type: "POST",
									dataType: "json",
									success: function (response) {
										if ( typeof response.results[0] != "undefined" ) {
											jQuery(".foodbakery-locations-field' . $listing_short_counter . ', .foodbakery-locations-field-geo' . $listing_short_counter . '").val( response.results[0].formatted_address );
											
											jQuery(".foodbakery-input-cross' . $listing_short_counter . '").show();
											_this_f.parents("form").submit();
										}
									}
								});
                            });
                        }
                    });
					
					$(".foodbakery-radius-location' . $listing_short_counter . '").click( function() {
						var data_id = jQuery(this).data("id");
						$(".foodbakery-radius-range"+data_id).toggle();
					});
                });                
               
                function foodbakery_current_lat_long(){
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var pos = {
                              lat: position.coords.latitude,
                              lng: position.coords.longitude
                            };
                            
                            return pos;
                        });
                    }
                }
                    
                </script>';
        //}
        if ($auto_country_detection == 'on' && ( is_home() || is_front_page())) {
            $output .= '<script>
				window.isCountryAlreadyDetected = false;
                $( window ).load(function() {
					if ( isCountryAlreadyDetected ) {
						return true;
					}
					if(jQuery(".location-field-text").length !== 0){
						isCountryAlreadyDetected = true;
						var current_value = jQuery(".location-field-text").val();
						var loading_lat_long = false;
						
							var data_id  = jQuery(".location-list .location-field-text").data("id");
							loading_lat_long = true;
							/*jQuery.getJSON("https://freegeoip.net/json/", function(data) {
								
								if( typeof data.city != "undefined" && data.city.trim() != "" ) {
									current_value = data.city;
								}
								if( typeof data.region_name != "undefined" && data.region_name.trim() != "" ) {
									if ( current_value != "") {
										current_value += " ";
									}
									current_value += data.region_name;
								}
								if( typeof data.zip_code != "undefined" && data.zip_code.trim() != "" ) {
									if ( current_value != "") {
										current_value += " ";
									}
									current_value += data.zip_code;
								}
								if( typeof data.country_name != "undefined" && data.country_name.trim() != "" ) {
									if ( current_value != "") {
										current_value += ", ";
									}
									current_value += data.country_name;
								} 
								
								if ( current_value != "" ) {
									
									jQuery(".foodbakery-locations-fields-group input[name=\'location\'], .foodbakery-locations-fields-group .location-field-text").val( current_value );
									
									jQuery(".foodbakery-input-cross' . $listing_short_counter . '").show();
								}
								
							});*/
						
					}
				});
            </script>';
        }
        if ($field_type == 'header') {
            $output .= '<script>
                    jQuery.ajax({
                       type: "POST",
                       url: foodbakery_globals.ajax_url,
                       data: "this_position=' . $field_type . '&action=foodbakery_get_all_default_locations",
                       success: function (response) {
                           jQuery(".foodbakery-all-locations' . $listing_short_counter . '").html(response);
                       }
                   });
                    $(document).on("click", ".location-has-children > a", function () {
                         jQuery.ajax({
                               type: "POST",
                               url: foodbakery_globals.ajax_url,
                               data: "keyword=&action=foodbakery_get_all_locations",
                               success: function (response) {
                               }
                           });
                    });
                </script>';
        }
        if ($field_type == 'header') {
            
        }
        $output .= '</li>';
        echo force_balance_tags($dropdown_start_html . $output . $dropdown_end_html);
    }

}

/**

 * End Function how to get Custom Loaction

 */
if (!function_exists('foodbakery_frontend_icomoon_selector')) {

    function foodbakery_frontend_icomoon_selector($icon_value = '', $id = '', $name = '', $classes = '') {

        global $foodbakery_form_fields;
        $foodbakery_var_icomoon = '
        <script>
            jQuery(document).ready(function ($) {
                var this_icons;
                var e9_element = $(\'#e9_element_' . esc_html($id) . '\').fontIconPicker({
                    theme: \'fip-bootstrap\'
                });
                icons_load_call.always(function () {
                    this_icons = loaded_icons;
                    // Get the class prefix
                    var classPrefix = this_icons.preferences.fontPref.prefix,
                            icomoon_json_icons = [],
                            icomoon_json_search = [];
                    $.each(this_icons.icons, function (i, v) {
                            icomoon_json_icons.push(classPrefix + v.properties.name);
                            if (v.icon && v.icon.tags && v.icon.tags.length) {
                                    icomoon_json_search.push(v.properties.name + \' \' + v.icon.tags.join(\' \'));
                            } else {
                                    icomoon_json_search.push(v.properties.name);
                            }
                    });
                    // Set new fonts on fontIconPicker
                    e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                    // Show success message and disable
                    $(\'#e9_buttons_' . esc_html($id) . ' button\').removeClass(\'btn-primary\').addClass(\'btn-success\').text(\'' . esc_html__('Successfully loaded icons', 'foodbakery') . '\').prop(\'disabled\', true);
                })
                .fail(function () {
                    // Show error message and enable
                    $(\'#e9_buttons_' . esc_html($id) . ' button\').removeClass(\'btn-primary\').addClass(\'btn-danger\').text(\'' . esc_html__('Error: Try Again?', 'foodbakery') . '\').prop(\'disabled\', false);
                });
            });
        </script>';
        $foodbakery_opt_array = array(
            'std' => esc_html($icon_value),
            'classes' => $classes,
            'cust_id' => 'e9_element_' . esc_html($id),
            'cust_name' => esc_html($name) . '[]',
            'return' => true,
        );
        $foodbakery_var_icomoon .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
        $foodbakery_var_icomoon .= '
        <span id="e9_buttons_' . esc_html($id) . '" style="display:none">
            <button autocomplete="off" type="button" class="btn btn-primary">' . esc_html__('Load from IcoMoon selection.json', 'foodbakery') . '</button>
        </span>';

        return $foodbakery_var_icomoon;
    }

}

if (!function_exists('foodbakery_front_change_password')) {

    function foodbakery_front_change_password() {
        global $current_user;
        $user = get_user_by('login', $current_user->user_login);
        $old_pass = isset($_POST['old_pass']) ? $_POST['old_pass'] : '';
        $new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : '';
        $confirm_pass = isset($_POST['confirm_pass']) ? $_POST['confirm_pass'] : '';

        if (!is_user_logged_in()) {
            esc_html_e('Login again to change password.', 'foodbakery');
            die;
        }

        if ($old_pass == '' || $new_pass == '' || $confirm_pass == '') {
            esc_html_e('Password field is empty.', 'foodbakery');
            die;
        }
        if ($user && wp_check_password($old_pass, $user->data->user_pass, $user->ID)) {

            if ($new_pass !== $confirm_pass) {
                esc_html_e('Mismatch Password fields.', 'foodbakery');
                die;
            } else {
                wp_set_password($new_pass, $user->ID);
                esc_html_e('Password Changed.', 'foodbakery');
                die;
            }
        } else {
            esc_html_e('Old Password is incorrect.', 'foodbakery');
            die;
        }
        esc_html_e('Password is incorrect.', 'foodbakery');
        die;
    }

    add_action('wp_ajax_foodbakery_front_change_password', 'foodbakery_front_change_password');
    add_action('wp_ajax_nopriv_foodbakery_front_change_password', 'foodbakery_front_change_password');
}

if (!function_exists('foodbakery_header_cover_style')) {

    function foodbakery_header_cover_style($foodbakery_user_page = '', $meta_cover_image = '', $default_size = '') {

        $foodbakery__theme_options = get_option('foodbakery_theme_options');
        $foodbakery_sh_paddingtop = ( isset($foodbakery__theme_options['foodbakery_sh_paddingtop']) ) ? ' padding-top:' . $foodbakery__theme_options['foodbakery_sh_paddingtop'] . 'px;' : '';
        $foodbakery_sh_paddingbottom = ( isset($foodbakery__theme_options['foodbakery_sh_paddingbottom']) ) ? ' padding-bottom:' . $foodbakery__theme_options['foodbakery_sh_paddingbottom'] . 'px;' : '';
        $page_subheader_color = ( isset($foodbakery__theme_options['foodbakery_sub_header_bg_color'])) ? $foodbakery__theme_options['foodbakery_sub_header_bg_color'] : '';
        $page_subheader_text_color = ( isset($foodbakery__theme_options['foodbakery_sub_header_text_color']) ) ? ' color:' . $foodbakery__theme_options['foodbakery_sub_header_text_color'] . ' !important;' : '';

        $foodbakery_sub_header_default_h = isset($foodbakery__theme_options['foodbakery_sub_header_default_h']) ? $foodbakery__theme_options['foodbakery_sub_header_default_h'] : '';

        if ($foodbakery_user_page == 'candidate') {
            $header_banner_image = ( isset($foodbakery__theme_options['foodbakery_candidate_default_cover']) ) ? $foodbakery__theme_options['foodbakery_candidate_default_cover'] : '';
        } else {
            $header_banner_image = ( isset($foodbakery__theme_options['foodbakery_publisher_default_cover']) ) ? $foodbakery__theme_options['foodbakery_publisher_default_cover'] : '';
        }

        $page_subheader_parallax = ( isset($foodbakery__theme_options['foodbakery_parallax_bg_switch']) ) ? $foodbakery__theme_options['foodbakery_parallax_bg_switch'] : '';

        if ($page_subheader_color) {
            $subheader_style_elements = 'background: ' . $page_subheader_color . ';';
        } else {
            $subheader_style_elements = '';
        }

        $parallax_class = '';

        if (isset($page_subheader_parallax) && (string) $page_subheader_parallax == 'on') {
            $parallax_class = 'parallex-bg';
        }

        if ($meta_cover_image != '') {
            $header_banner_image = $meta_cover_image;
        }
        $foodbakery__header_image_height = '';
        if ($header_banner_image != '') {
            $foodbakery_upload_dir = wp_upload_dir();
            $foodbakery_upload_baseurl = isset($foodbakery_upload_dir['baseurl']) ? $foodbakery_upload_dir['baseurl'] . '/' : '';

            $foodbakery_upload_dir = isset($foodbakery_upload_dir['basedir']) ? $foodbakery_upload_dir['basedir'] . '/' : '';

            if (false !== strpos($header_banner_image, $foodbakery_upload_baseurl)) {
                $foodbakery_upload_subdir_file = str_replace($foodbakery_upload_baseurl, '', $header_banner_image);
            }

            $foodbakery_images_dir = trailingslashit(wp_foodbakery::plugin_url()) . 'assets/images/';

            $foodbakery_img_name = preg_replace('/^.+[\\\\\\/]/', '', $header_banner_image);

            if (is_file($foodbakery_upload_dir . $foodbakery_img_name) || is_file($foodbakery_images_dir . $foodbakery_img_name)) {
                if (ini_get('allow_url_fopen')) {
                    if ($header_banner_image <> '') {
                        $foodbakery__header_image_height = getimagesize($header_banner_image);
                    }
                }
            } else if (isset($foodbakery_upload_subdir_file) && is_file($foodbakery_upload_dir . $foodbakery_upload_subdir_file)) {
                if (ini_get('allow_url_fopen')) {
                    if ($header_banner_image <> '') {
                        $foodbakery__header_image_height = getimagesize($header_banner_image);
                    }
                }
            }
            if (isset($foodbakery__header_image_height) && $foodbakery__header_image_height != '' && isset($foodbakery__header_image_height[1])) {
                $foodbakery__header_image_height = $foodbakery__header_image_height[1] . 'px';
                $foodbakery__header_image_height = ' min-height: ' . $foodbakery__header_image_height . ' !important;';
            }
        } else {
            $foodbakery__header_image_height = ' min-height: ' . $default_size . 'px !important;';
        }
        if ($foodbakery_sub_header_default_h != '' && $foodbakery_sub_header_default_h >= 0) {
            $foodbakery__header_image_height = ' min-height: ' . $foodbakery_sub_header_default_h . 'px !important;';
        }
        if ($header_banner_image != '') {
            if ($page_subheader_parallax == 'on') {
                $parallaxStatus = 'no-repeat fixed';
            } else {
                $parallaxStatus = '';
            }
            if ($page_subheader_parallax == 'on') {
                $header_banner_image = 'url(' . $header_banner_image . ') center top ' . $parallaxStatus . '';
                $subheader_style_elements = 'background: ' . $header_banner_image . ' ' . $page_subheader_color . ';' . ' background-size:cover;';
            } else {
                $header_banner_image = 'url(' . $header_banner_image . ') center top ' . $parallaxStatus . '';
                $subheader_style_elements = 'background: ' . $header_banner_image . ' ' . $page_subheader_color . ';';
            }
        }

        if ($subheader_style_elements <> '' && $foodbakery__header_image_height <> '') {
            $subheader_style_elements = $subheader_style_elements . $foodbakery__header_image_height . $page_subheader_text_color . $foodbakery_sh_paddingtop . $foodbakery_sh_paddingbottom;
        } else {
            if ($foodbakery__header_image_height <> '') {
                $subheader_style_elements = $foodbakery__header_image_height . $page_subheader_text_color . $foodbakery_sh_paddingtop . $foodbakery_sh_paddingbottom;
            } else {
                $subheader_style_elements = $page_subheader_text_color . $foodbakery_sh_paddingtop . $foodbakery_sh_paddingbottom;
            }
        }

        return array($subheader_style_elements, $parallax_class);
    }

}

if (!function_exists('foodbakery_author_role_template')) {

    function foodbakery_author_role_template($author_template = '') {

        $author = get_queried_object();

        $role = $author->roles[0];

        if ($role == 'foodbakery_publisher') {
            $author_template = plugin_dir_path(__FILE__) . 'single_pages/single-employer.php';
        } else if ($role == 'foodbakery_candidate') {
            $author_template = plugin_dir_path(__FILE__) . 'single_pages/single-candidate.php';
        }
        return $author_template;
    }

    add_filter('author_template', 'foodbakery_author_role_template');
}

if (!function_exists('foodbakery_user_pagination')) {

    function foodbakery_user_pagination($total_pages = 1, $page = 1) {

        $query_string = $_SERVER['QUERY_STRING'];

        $base = get_permalink() . '?' . remove_query_arg('page_id_all', $query_string) . '%_%';

        $foodbakery_pagination = paginate_links(array(
            'base' => $base, // the base URL, including query arg
            'format' => '&page_id_all=%#%', // this defines the query parameter that will be used, in this case "p"
            'prev_text' => '<i class="icon-angle-left"></i> ' . esc_html__('Previous', 'foodbakery'), // text for previous page
            'next_text' => esc_html__('Next', 'foodbakery') . ' <i class="icon-angle-right"></i>', // text for next page
            'total' => $total_pages, // the total number of pages we have
            'current' => $page, // the current page
            'end_size' => 1,
            'mid_size' => 2,
            'type' => 'array',
        ));

        $foodbakery_pages = '';

        if (is_array($foodbakery_pagination) && sizeof($foodbakery_pagination) > 0) {

            $foodbakery_pages .= '<ul class="pagination">';

            foreach ($foodbakery_pagination as $foodbakery_link) {

                if (strpos($foodbakery_link, 'current') !== false) {

                    $foodbakery_pages .= '<li><a class="active">' . preg_replace("/[^0-9]/", "", $foodbakery_link) . '</a></li>';
                } else {

                    $foodbakery_pages .= '<li>' . $foodbakery_link . '</li>';
                }
            }

            $foodbakery_pages .= '</ul>';
        }

        echo force_balance_tags($foodbakery_pages);
    }

}

if (!function_exists('foodbakery_dashboard_pagination')) {

    function foodbakery_dashboard_pagination($total_pages = 1, $page = 1, $url = '', $to_action = '') {

        $query_string = $_SERVER['QUERY_STRING'];

        if ($url != '') {
            $base = $url . '' . remove_query_arg('page_id_all', $query_string) . '%_%';
        } else {
            $base = get_permalink() . '?' . remove_query_arg('page_id_all', $query_string) . '%_%';
        }
        $foodbakery_pagination = paginate_links(array(
            'base' => $base, // the base URL, including query arg
            'format' => '&page_id_all=%#%', // this defines the query parameter that will be used, in this case "p"
            'prev_text' => '<i class="icon-angle-left"></i> ' . esc_html__('Previous', 'foodbakery'), // text for previous page
            'next_text' => esc_html__('Next', 'foodbakery') . ' <i class="icon-angle-right"></i>', // text for next page
            'total' => $total_pages, // the total number of pages we have
            'current' => $page, // the current page
            'end_size' => 1,
            'mid_size' => 2,
            'type' => 'array',
        ));

        $foodbakery_pages = '';

        if (is_array($foodbakery_pagination) && sizeof($foodbakery_pagination) > 0) {

            $foodbakery_pages .= '<ul class="pagination">';

            foreach ($foodbakery_pagination as $foodbakery_link) {

                if (strpos($foodbakery_link, 'current') !== false) {

                    $foodbakery_pages .= '<li class="active"><a>' . preg_replace("/[^0-9]/", "", $foodbakery_link) . '</a></li>';
                } else {

                    $page_a_val = '';
                    $page_a_href = '';
                    $query_page_num = '';
                    $pagination_dom = new DOMDocument;
                    $pagination_dom->loadHTML($foodbakery_link);
                    foreach ($pagination_dom->getElementsByTagName('a') as $pagination_node) {
                        $page_a_href = $pagination_node->getAttribute('href');
                        $page_a_val = $pagination_node->nodeValue;

                        $parse_href = parse_url($page_a_href);
                        $href_query = isset($parse_href['query']) ? $parse_href['query'] : '';
                        $query_page_num = preg_replace("/[^0-9]/", "", $href_query);
                    }
                    if (!isset($query_page_num) || $query_page_num == '') {
                        $query_page_num = 1;
                    }
                    if ($page_a_val != '' && $page_a_href != '') {
                        $foodbakery_pages .= '<li><a href="javascript:void(0);" data-id="foodbakery_publisher_' . $to_action . '" data-pagenum="' . $query_page_num . '" class="user_dashboard_ajax" data-queryvar="dashboard=' . $to_action . '&page_id_all=' . $query_page_num . '">' . $page_a_val . '</a></li>';
                    } else {
                        $foodbakery_pages .= '<li>' . $foodbakery_link . '</li>';
                    }
                }
            }

            $foodbakery_pages .= '</ul>';
        }

        echo force_balance_tags($foodbakery_pages);
    }

}

if (!function_exists('foodbakery_show_all_cats')) {

    function foodbakery_show_all_cats($parent = '', $separator = '', $selected = "", $taxonomy = '', $optional = '') {

        if ($parent == "") {

            global $wpdb;

            $parent = 0;
        } else {
            $separator .= " &ndash; ";
        }
        $args = array(
            'parent' => $parent,
            'hide_empty' => 0,
            'taxonomy' => $taxonomy
        );

        $categories = get_categories($args);

        if ($optional) {
            $a_options = array();
            $a_options[''] = esc_html__("Please select..", 'foodbakery');
            foreach ($categories as $category) {
                $a_options[$category->slug] = $category->cat_name;
            }
            return $a_options;
        } else {

            foreach ($categories as $category) {
                ?>
                <option <?php
                if ($selected == $category->slug) {
                    echo "selected";
                }
                ?> value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_attr($separator . $category->cat_name); ?></option>
                    <?php
                    foodbakery_show_all_cats($category->term_id, $separator, $selected, $taxonomy);
                }
            }
        }

    }
    /**
     * End Function how to Add User Image for Avatar
     */
    /**
     * Start Function how to Set Post Views
     */
    if (!function_exists('foodbakery_set_post_views')) {

        function foodbakery_set_post_views($postID) {
            if (!isset($_COOKIE["foodbakery_count_views" . $postID])) {
                setcookie("foodbakery_count_views" . $postID, 'post_view_count', time() + 86400);
                $count_key = 'foodbakery_count_views';
                $count = get_post_meta($postID, $count_key, true);
                if ($count == '') {
                    $count = 0;
                    delete_post_meta($postID, $count_key);
                    add_post_meta($postID, $count_key, '0');
                } else {
                    $count++;
                    update_post_meta($postID, $count_key, $count);
                }
            }
        }

    }

    /**

     * End Function how to Set Post Views

     */
    /**

     * Start Function how to Share Posts

     */
    if (!function_exists('foodbakery_addthis_script_init_method')) {

        function foodbakery_addthis_script_init_method() {

            wp_enqueue_script('foodbakery_addthis', foodbakery_server_protocol() . 's7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e4412d954dccc64', '', '', true);
        }

    }

    /**
     * End Function how to Share Posts
     */
    /**
     * check whether file exsit or not
     */
    if (!function_exists('foodbakery_check_coverletter_exist')) {



        function foodbakery_check_coverletter_exist($file) {

            $is_exist = false;

            if (isset($file) && $file <> "") {

                $file_headers = @get_headers($file);

                if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {

                    $is_exist = false;
                } else {

                    $is_exist = true;
                }
            }

            return $is_exist;
        }

    }

    /**

     * End check whether file exsit or not

     */
    /**

     * Start Function how to Get Current User ID

     */
    if (!function_exists('foodbakery_get_user_id')) {

        function foodbakery_get_user_id() {

            global $current_user;

            wp_get_current_user();

            return $current_user->ID;
        }

    }
    /**

     * End Function how to Get Current User ID

     */
    /**

     * Start Function how to Add your Favourite Dirpost

     */
    if (!function_exists('foodbakery_add_dirpost_favourite')) {

        function foodbakery_add_dirpost_favourite($foodbakery_post_id = '') {
            global $post;
            $foodbakery_emp_funs = new foodbakery_publisher_functions();
            $foodbakery_post_id = isset($foodbakery_post_id) ? $foodbakery_post_id : '';
            if (!is_user_logged_in() || !$foodbakery_emp_funs->is_employer()) {

                if (is_user_logged_in()) {

                    $user = foodbakery_get_user_id();

                    $finded_result_list = foodbakery_find_index_user_meta_list($foodbakery_post_id, 'cs-user-jobs-wishlist', 'post_id', foodbakery_get_user_id());

                    if (isset($user) and $user <> '' and is_user_logged_in()) {

                        if (is_array($finded_result_list) && !empty($finded_result_list)) {
                            ?>

                        <a class="cs-add-wishlist tolbtn" data-toggle="tooltip" data-placement="top" data-original-title="<?php esc_html_e('Shortlist', 'foodbakery') ?>" onclick="foodbakery_delete_from_favourite('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($foodbakery_post_id); ?>', 'post')" >

                            <i class="icon-heart6"></i>

                        </a>

                        <?php
                    } else {
                        ?>

                        <a class="cs-add-wishlist tolbtn" onclick="foodbakery_addto_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($foodbakery_post_id); ?>', 'post')" data-placement="top" data-toggle="tooltip" data-original-title="<?php esc_html_e('Shortlisted', 'foodbakery') ?>">
                            <i class="icon-heart-o"></i>
                        </a>
                        <?php
                    }
                } else {
                    ?>
                    <a class="cs-add-wishlist tolbtn" onclick="foodbakery_addto_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($foodbakery_post_id); ?>', 'post')" data-placement="top" data-toggle="tooltip" data-original-title="<?php esc_html_e('Shortlisted', 'foodbakery') ?>">
                        <i class="icon-heart-o"></i>
                    </a>
                    <?php
                }
            } else {
                ?>
                <a href="javascript:void(0);" class="cs-add-wishlist" onclick="trigger_func('#btn-header-main-login');"><i class="icon-heart-o"></i> </a>

                <?php
            }
        }
    }

}

/**

 * End Function how to Add your Favourite Dirpost

 */
/**

 * Start Function how to Add User Meta

 */
if (!function_exists('foodbakery_addto_usermeta')) {

    function foodbakery_addto_usermeta() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                foodbakery_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                ?>

                <i class="icon-heart6"></i>

                <?php
            }
        } else {

            esc_html_e('You have to login first.', 'foodbakery');
        }

        die();
    }

    add_action("wp_ajax_foodbakery_addto_usermeta", "foodbakery_addto_usermeta");

    add_action("wp_ajax_nopriv_foodbakery_addto_usermeta", "foodbakery_addto_usermeta");
}

/**

 * End Function how to Add User Meta

 */
/**

 * Start Function how to Add User Apply Meta For Job

 */
if (!function_exists('foodbakery_get_user_jobapply_meta')) {



    function foodbakery_get_user_jobapply_meta($user = "") {

        if (!empty($user)) {

            $userdata = get_user_by('login', $user);

            $user_id = $userdata->ID;

            return get_user_meta($user_id, 'cs-jobs-applied', true);
        } else {

            return get_user_meta(foodbakery_get_user_id(), 'cs-jobs-applied', true);
        }
    }

}

/**

 * End Function how to Add User Apply Meta For Job

 */
/**

 * Start Function how to Update User Apply Meta For Job

 */
if (!function_exists('foodbakery_update_user_jobapply_meta')) {

    function foodbakery_update_user_jobapply_meta($arr) {

        return update_user_meta(foodbakery_get_user_id(), 'cs-jobs-applied', $arr);
    }

}

/**

 * End Function how to Update User Apply Meta For Job

 */
/**

 * Start Function how to Delete Favourites User

 */
if (!function_exists('foodbakery_delete_from_favourite')) {



    function foodbakery_delete_from_favourite() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                foodbakery_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);

                echo '<i class="icon-heart-o"></i>';
            } else {

                esc_html_e('You are not authorised', 'foodbakery');
            }
        }

        die();
    }

    add_action("wp_ajax_foodbakery_delete_from_favourite", "foodbakery_delete_from_favourite");

    add_action("wp_ajax_nopriv_foodbakery_delete_from_favourite", "foodbakery_delete_from_favourite");
}

/**

 * End Function how to Delete Favourites User

 */
/**

 * Start Function how to Delete User From Wishlist

 */
if (!function_exists('foodbakery_delete_wishlist')) {

    function foodbakery_delete_wishlist() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            // check this record is in his list

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                foodbakery_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);

                esc_html_e('Removed From Favourite', 'foodbakery');
            } else {

                esc_html_e('You are not authorised', 'foodbakery');
            }
        }

        die();
    }

    add_action("wp_ajax_foodbakery_delete_wishlist", "foodbakery_delete_wishlist");

    add_action("wp_ajax_nopriv_foodbakery_delete_wishlist", "foodbakery_delete_wishlist");
}

/**

 * End Function how to Delete User From Wishlist

 */
/*

  eandidate contact form

 */



if (!function_exists('ajaxcontact_send_mail_cand')) {

    function ajaxcontact_send_mail_cand() {

        $results = '';

        $error = 0;

        $error_result = 0;

        $message = "";

        $name = '';

        $email = '';

        $phone = '';

        $contents = '';

        $candidateid = '';

        if (isset($_POST['ajaxcontactname'])) {
            $name = $_POST['ajaxcontactname'];
        }

        if (isset($_POST['ajaxcontactemail'])) {

            $email = $_POST['ajaxcontactemail'];
        }

        if (isset($_POST['ajaxcontactphone'])) {

            $phone = $_POST['ajaxcontactphone'];
        }

        if (isset($_POST['ajaxcontactcontents'])) {

            $contents = $_POST['ajaxcontactcontents'];
        }

        if (isset($_POST['candidateid'])) {

            $candidateid = $_POST['candidateid'];   // user id for candidate
        }

        if (isset($_POST['foodbakery_terms_page'])) {

            $foodbakery_terms_page = 'on';

            $foodbakery_contact_terms = isset($_POST['foodbakery_contact_terms']) ? $_POST['foodbakery_contact_terms'] : '';
        } else {

            $foodbakery_terms_page = 'off';

            $foodbakery_contact_terms = '';
        }

        $subject = esc_html__("Employer Contact from job hunt", "foodbakery");

        $admin_email_from = get_option('admin_email');

        // getting candidate email address
        // getting email address from user table

        $foodbakery_user_id = $candidateid;

        $user_info = get_userdata($foodbakery_user_id);

        $admin_email = '';

        if (isset($user_info->user_email) && $user_info->user_email <> '') {

            $admin_email = $user_info->user_email;
        }

        if ($admin_email != '' && filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {

            if (strlen($name) == 0) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter name.', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            } else if (strlen($email) == 0) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter email.', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $results = " '" . $email . "' " . esc_html__('email address is not valid.', 'foodbakery');

                $error = 1;

                $error_result = 1;
            } else if (strlen($contents) == 0 || strlen($contents) < 5) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Message should have more than 50 characters', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            } else if ($foodbakery_terms_page == 'on' && $foodbakery_contact_terms != 'on') {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('You should accept Terms and Conditions.', 'foodbakery') . "</span>";

                $error = 1;

                $error_result = 1;
            } else if (isset($_POST['captcha_id']) && $_POST['captcha_id'] != '' && $_POST['captcha_id'] != 'undefined') {

                if (foodbakery_captcha_verify(true)) {

                    $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Captcha should must be validate', 'foodbakery') . "</span>";

                    $error = 1;

                    $error_result = 1;
                }
            }



            if ($error == 0) {

                $form_array = array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'message' => $contents,
                    'candidate_email' => $admin_email
                );
                do_action('foodbakery_publisher_contact_candidate', $form_array);
                if (class_exists('foodbakery_publisher_contact_candidate_email_template') && isset(foodbakery_publisher_contact_candidate_email_template::$is_email_sent1)) {
                    $error = 0;

                    $error_result = 0;

                    $results = esc_html__("&nbsp; <span style=\"color: #060;\">Your inquiry has been sent User will contact you shortly|" . $error_result . "|</span>", "foodbakery");
                } else {

                    $error = 1;

                    $error_result = 1;

                    $results = esc_html__("&nbsp; <span style=\"color: #ff0000;\">*The mail could not be sent due to some resons, Please try again</span>", "foodbakery");
                }

                $args = array(
                    'to' => $admin_email,
                    'subject' => $subject,
                    'message' => $template,
                    'class_obj' => $obj_template,
                );
            }
        } else {

            $results = "&nbsp; <span style=\"color: #ff0000;\">*" . esc_html__('The profile email does not exist, Please try later', 'foodbakery') . "</span>";

            $error = 1;

            $error_result = 1;
        }


        if ($error_result == 1) {

            $data = 1;

            $message = $results;

            die($message);
        } else {

            $data = 0;

            $message = $results;

            die($message);
        }
    }

    add_action('wp_ajax_nopriv_ajaxcontact_send_mail_cand', 'ajaxcontact_send_mail_cand');

    add_action('wp_ajax_ajaxcontact_send_mail_cand', 'ajaxcontact_send_mail_cand');
}

/**
 * Start Function how to send mail using Ajax
 */
if (!function_exists('ajaxcontact_send_mail')) {

    function ajaxcontact_send_mail() {

        $results = '';
        $error = 0;
        $error_result = 0;
        $message = "";
        $name = '';
        $email = '';
        $phone = '';
        $contents = '';
        $candidateid = '';

        if (isset($_POST['foodbakery_ajaxcontactname'])) {

            $name = $_POST['foodbakery_ajaxcontactname'];
        }

        if (isset($_POST['foodbakery_ajaxcontactemail'])) {

            $email = $_POST['foodbakery_ajaxcontactemail'];
        }

        if (isset($_POST['foodbakery_ajaxcontactphone'])) {

            $phone = $_POST['foodbakery_ajaxcontactphone'];
        }

        if (isset($_POST['foodbakery_ajaxcontactcontents'])) {

            $contents = $_POST['foodbakery_ajaxcontactcontents'];
        }

        if ($name == '') {

            if (isset($_POST['ajaxcontactname'])) {

                $name = $_POST['ajaxcontactname'];
            }
        }

        if ($email == '') {

            if (isset($_POST['ajaxcontactemail'])) {

                $email = $_POST['ajaxcontactemail'];
            }
        }

        if ($phone == '') {

            if (isset($_POST['ajaxcontactphone'])) {

                $phone = $_POST['ajaxcontactphone'];
            }
        }

        if ($contents == '') {

            if (isset($_POST['ajaxcontactcontents'])) {

                $contents = $_POST['ajaxcontactcontents'];
            }
        }

        if (isset($_POST['candidateid'])) {

            $candidateid = $_POST['candidateid'];   // user id for candidate
        }

        if (isset($_POST['foodbakery_terms_page'])) {

            $foodbakery_terms_page = 'on';

            $foodbakery_contact_terms = isset($_POST['foodbakery_contact_terms']) ? $_POST['foodbakery_contact_terms'] : '';
        } else {

            $foodbakery_terms_page = 'off';

            $foodbakery_contact_terms = '';
        }

        $subject = esc_html__("Employer Contact from job hunt", "foodbakery");

        $admin_email_from = get_option('admin_email');

        // getting candidate email address
        // getting email address from user table

        $foodbakery_user_id = $candidateid;

        $user_info = get_userdata($foodbakery_user_id);

        $admin_email = '';

        if (isset($user_info->user_email) && $user_info->user_email <> '') {

            $admin_email = $user_info->user_email;
        }

        if ($admin_email != '' && filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {

            if (strlen($name) == 0) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter name.', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            } else if (strlen($email) == 0) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter email.', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $results = " '" . $email . "' " . esc_html__('email address is not valid.', 'foodbakery');

                $error = 1;

                $error_result = 1;
            } else if (strlen($contents) == 0 || strlen($contents) < 50) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Message should have more than 50 characters', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            } else if ($foodbakery_terms_page == 'on' && $foodbakery_contact_terms != 'on') {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('You should accept Terms and Conditions.', 'foodbakery') . "</span>";

                $error = 1;

                $error_result = 1;
            } else if (isset($_POST['captcha_id']) && $_POST['captcha_id'] != '' && $_POST['captcha_id'] != 'undefined') {

                if (foodbakery_captcha_verify(true)) {

                    $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Captcha should must be validate', 'foodbakery') . "</span>";

                    $error = 1;

                    $error_result = 1;
                }
            }

            if ($error == 0) {

                $form_args = array('name' => $name, 'email' => $email, 'phone' => $phone, 'message' => $contents, 'candidate_email' => $admin_email);

                do_action('foodbakery_publisher_contact_candidate', $form_args);
                if (class_exists('foodbakery_publisher_contact_candidate_email_template') && isset(foodbakery_publisher_contact_candidate_email_template::$is_email_sent1)) {

                    $error = 0;

                    $error_result = 0;

                    $results = esc_html__("&nbsp; <span style=\"color: #060;\">Your inquiry has been sent User will contact you shortly</span>", "foodbakery");
                } else {

                    $error = 1;

                    $error_result = 1;

                    $results = esc_html__("&nbsp; <span style=\"color: #ff0000;\">*The mail could not be sent due to some resons, Please try again</span>", "foodbakery");
                }
            }
        } else {

            $results = "&nbsp; <span style=\"color: #ff0000;\">*" . esc_html__('The profile email does not exist, Please try later', 'foodbakery') . "</span>";

            $error = 1;

            $error_result = 1;
        }



        if ($error_result == 1) {

            $data = 1;

            $message = $results;

            die($message);
        } else {

            $data = 0;

            $message = $results;

            die($message);
        }
    }

    // creating Ajax call for WordPress

    add_action('wp_ajax_nopriv_ajaxcontact_send_mail', 'ajaxcontact_send_mail');

    add_action('wp_ajax_ajaxcontact_send_mail', 'ajaxcontact_send_mail');
}

/**
 * End Function how to send mail using Ajax
 */
/**
 * Start Function how to send Employeer Contact mail using Ajax
 */
if (!function_exists('ajaxcontact_employer_send_mail')) {

    function ajaxcontact_employer_send_mail() {

        global $foodbakery_plugin_options;

        $results = '';

        $message = "";

        $error = 0;

        $name = '';

        $email = '';

        $phone = '';

        $employerid_contactuscheckbox = '';

        $phone = '';

        $messgae = '';

        $error_result = 0;

        $contents = '';

        $employerid = '';

        $foodbakery_captcha_switch = isset($foodbakery_plugin_options['foodbakery_captcha_switch']) ? $foodbakery_plugin_options['foodbakery_captcha_switch'] : '';

        if (isset($_POST['ajaxcontactname'])) {

            $name = $_POST['ajaxcontactname'];
        }

        if (isset($_POST['employerid_contactuscheckbox'])) {

            $employerid_contactuscheckbox = $_POST['employerid_contactuscheckbox'];
        }

        if (isset($_POST['ajaxcontactemail'])) {

            $email = $_POST['ajaxcontactemail'];
        }if (isset($_POST['ajaxcontactphone'])) {

            $phone = $_POST['ajaxcontactphone'];
        }if (isset($_POST['ajaxcontactcontents'])) {

            $contents = $_POST['ajaxcontactcontents'];
            $messgae = $_POST['ajaxcontactcontents'];
        }if (isset($_POST['employerid'])) {

            $employerid = $_POST['employerid'];
        }

        if (isset($_POST['foodbakery_terms_page'])) {

            $foodbakery_terms_page = 'on';

            $foodbakery_contact_terms = isset($_POST['foodbakery_contact_terms']) ? $_POST['foodbakery_contact_terms'] : '';
        } else {

            $foodbakery_terms_page = 'off';

            $foodbakery_contact_terms = '';
        }

        // user id for candidate

        $subject = esc_html__("Candidate Contact from job hunt", "foodbakery");

        $admin_email_from = get_option('admin_email');

        // getting employer email address

        $foodbakery_user_id = $employerid;

        $user_info = get_userdata($foodbakery_user_id);

        $admin_email = '';

        if (isset($user_info->user_email)) {

            $admin_email = $user_info->user_email;
        }



        if ($admin_email != '' && filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {



            if (strlen($name) == 0) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter name.</span>', 'foodbakery') . "<br/>";

                $error = 1;

                $error_result = 1;
            } else if (strlen($email) == 0) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter email.</span><br/>', 'foodbakery') . "";

                $error = 1;

                $error_result = 1;
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $results = "&nbsp; '<span style=\"color: #ff0000;\">" . $email . "' " . esc_html__('email address is not valid.</span><br/>', 'foodbakery') . "";

                $error = 1;

                $error_result = 1;
            } else if (strlen($contents) == 0 || strlen($contents) < 50) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Message should have more than 50 characters', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            } else if ($foodbakery_terms_page == 'on' && $foodbakery_contact_terms != 'on') {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('You should accept Terms and Conditions.', 'foodbakery') . "</span>";

                $error = 1;

                $error_result = 1;
            } else if (foodbakery_captcha_verify(true)) {

                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Captcha should must be validate.', 'foodbakery') . "</span><br/>";

                $error = 1;

                $error_result = 1;
            }

            if ($error == 0) {

                $email_template_atts = array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'message' => $messgae,
                    'employer_email' => $admin_email,
                );
                do_action('foodbakery_candidate_contact_email', $email_template_atts);
                if (class_exists('foodbakery_candidate_contact_email_template') && isset(foodbakery_candidate_contact_email_template::$is_email_sent1)) {

                    $error = 0;

                    $error_result = 0;

                    $results = "&nbsp; <span style=\"color: #060;\">" . esc_html__('Your inquiry has been sent User will contact you shortly', 'foodbakery') . "</span>";
                } else {

                    $error = 1;

                    $error_result = 1;

                    $results = "&nbsp; <span style=\"color: #ff0000;\">**" . esc_html__('Something Wrong, Please try later.', 'foodbakery') . "</span> ";
                }
            }
        } else {

            $results = "&nbsp; <span style=\"color: #ff0000;\">**" . esc_html__('The profile email does not exist, Please try later.', 'foodbakery') . "</span> ";

            $error = 1;

            $error_result = 1;
        }

        if ($error_result == 1) {

            $data = 1;

            $message = $results . '|' . $data;

            die($message);
        } else {

            $data = 0;

            $message = $results . '|' . $data;

            die($message);
        }
    }

    // creating Ajax call for WordPress

    add_action('wp_ajax_nopriv_ajaxcontact_employer_send_mail', 'ajaxcontact_employer_send_mail');

    add_action('wp_ajax_ajaxcontact_employer_send_mail', 'ajaxcontact_employer_send_mail');
}

/**
 * End Function how to send Employeer Contact mail using Ajax
 */
/**
 *
 * @time elapsed string
 *
 */
if (!function_exists('foodbakery_time_elapsed_string')) {



    function foodbakery_time_elapsed_string($ptime) {

        return human_time_diff($ptime, current_time('timestamp')) . " " . esc_html__('ago', 'foodbakery');
    }

}


/**

 * Start Function how to create Custom Pagination using Ajax

 */
if (!function_exists('foodbakery_ajax_pagination')) {



    function foodbakery_ajax_pagination($total_records, $per_page, $tab, $type, $uid, $pack_array) {

        $admin_url = esc_url(admin_url('admin-ajax.php'));

        if ($total_records < $per_page) {

            return;
        } else {

            $html = '';

            $dot_pre = '';

            $dot_more = '';

            $total_page = 0;

            if ($per_page <> 0)
                $total_page = ceil($total_records / $per_page);

            $page_id_all = 0;

            if (isset($_REQUEST['page_id_all']) && $_REQUEST['page_id_all'] != '') {

                $page_id_all = $_REQUEST['page_id_all'];
            }

            $loop_start = $page_id_all - 2;

            $loop_end = $page_id_all + 2;

            if ($page_id_all < 3) {

                $loop_start = 1;

                if ($total_page < 5)
                    $loop_end = $total_page;
                else
                    $loop_end = 5;
            } else if ($page_id_all >= $total_page - 1) {

                if ($total_page < 5)
                    $loop_start = 1;
                else
                    $loop_start = $total_page - 4;

                $loop_end = $total_page;
            }

            $html .= "<ul class='pagination'>";

            if ($page_id_all > 1) {

                $html .= "<li><a onclick=\"foodbakery_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '" . ($page_id_all - 1) . "')\" href='javascript:void(0);' aria-label='Previous' ><span aria-hidden='true'><i class='icon-angle-left'></i> " . esc_html__('Previous', 'foodbakery') . " </span></a></li>";
            } else {

                $html .= "<li><a aria-label='Previous'><span aria-hidden='true'><i class='icon-angle-left'></i> " . esc_html__('Previous', 'foodbakery') . "</span></a></li>";
            }

            if ($page_id_all > 3 and $total_page > 5)
                $html .= "<li><a href='javascript:void(0);' onclick=\"foodbakery_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '1')\">1</a></li>";

            if ($page_id_all > 4 and $total_page > 6)
                $html .= "<li> <a>. . .</a> </li>";

            if ($total_page > 1) {

                for ($i = $loop_start; $i <= $loop_end; $i++) {

                    if ($i <> $page_id_all)
                        $html .= "<li><a href='javascript:void(0);' onclick=\"foodbakery_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '" . ($i) . "')\" >" . $i . "</a></li>";
                    else
                        $html .= "<li><a class='active'>" . $i . "</a></li>";
                }
            }

            if ($loop_end <> $total_page and $loop_end <> $total_page - 1)
                $html .= "<li> <a>. . .</a> </li>";

            if ($loop_end <> $total_page)
                $html .= "<li><a href='javascript:void(0);' onclick=\"foodbakery_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '" . ($total_page) . "')\">$total_page</a></li>";

            if ($per_page > 0 and $page_id_all < $total_records / $per_page) {

                $html .= "<li><a href='javascript:void(0);' aria-label='Next' onclick=\"foodbakery_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "','" . ($page_id_all + 1) . "')\" ><span aria-hidden='true'>" . esc_html__('Next', 'foodbakery') . " <i class='icon-angle-right'></i></span></a></li>";
            } else {

                $html .= "<li><a href='javascript:void(0);' aria-label='Next'><span aria-hidden='true'>" . esc_html__('Next', 'foodbakery') . " <i class='icon-angle-right'></i></span></a></li>";
            }

            $html .= "</ul>";

            return $html;
        }
    }

}

/**

 * End Function how to create Custom Pagination using Ajax

 */
/**

 * Start Function how to Add Job User Meta

 */
if (!function_exists('foodbakery_addjob_to_usermeta')) {



    function foodbakery_addjob_to_usermeta() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                foodbakery_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                ?>

                <i class="icon-heart6"></i>

                <?php
            }
        } else {

            esc_html_e('You have to login first.', 'foodbakery');
        }

        die();
    }

    add_action("wp_ajax_foodbakery_addjob_to_usermeta", "foodbakery_addjob_to_usermeta");

    add_action("wp_ajax_nopriv_foodbakery_addjob_to_usermeta", "foodbakery_addjob_to_usermeta");
}





if (!function_exists('foodbakery_addjob_to_user')) {



    function foodbakery_addjob_to_user() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                foodbakery_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                ?>

                <i class="icon-heart6"></i>

                <?php
            }
        } else {

            esc_html_e('You have to login first.', 'foodbakery');
        }

        die();
    }

    add_action("wp_ajax_foodbakery_addjob_to_user", "foodbakery_addjob_to_user");

    add_action("wp_ajax_nopriv_foodbakery_addjob_to_user", "foodbakery_addjob_to_user");
}

/**

 * End Function how to Add Job User Meta

 */
/**

 * Start Function how to Remove Job from User Meta

 */
if (!function_exists('foodbakery_removejob_to_usermeta')) {



    function foodbakery_removejob_to_usermeta() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                foodbakery_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);

                echo '<i class="icon-heart7"></i>';
            } else {

                esc_html_e('You are not authorised', 'foodbakery');
            }
        } else {

            esc_html_e('You have to login first.', 'foodbakery');
        }



        die();
    }

    add_action("wp_ajax_foodbakery_removejob_to_usermeta", "foodbakery_removejob_to_usermeta");

    add_action("wp_ajax_nopriv_foodbakery_removejob_to_usermeta", "foodbakery_removejob_to_usermeta");
}



if (!function_exists('foodbakery_removejob_to_user')) {



    function foodbakery_removejob_to_user() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                foodbakery_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);

                echo '<i class="icon-heart7"></i>';
            } else {

                esc_html_e('You are not authorised', 'foodbakery');
            }
        } else {

            esc_html_e('You have to login first.', 'foodbakery');
        }



        die();
    }

    add_action("wp_ajax_foodbakery_removejob_to_user", "foodbakery_removejob_to_user");

    add_action("wp_ajax_nopriv_foodbakery_removejob_to_user", "foodbakery_removejob_to_user");
}



/**

 * End Function how to Remove Job from User Meta

 */
/**

 * Start Function how to Apply for job

 */
if (!function_exists('foodbakery_add_jobs_applied')) {



    function foodbakery_add_jobs_applied($foodbakery_post_id = '') {

        global $post;

        $foodbakery_post_id = isset($foodbakery_post_id) ? $foodbakery_post_id : '';

        if (is_user_logged_in()) {

            $user = foodbakery_get_user_id();

            if (foodbakery_candidate_post_id($user)) {

                $foodbakery_applied_list = array();

                if (isset($user) and $user <> '' and is_user_logged_in()) {

                    $finded_result_list = foodbakery_find_index_user_meta_list($foodbakery_post_id, 'cs-user-jobs-applied-list', 'post_id', foodbakery_get_user_id());

                    if (is_array($finded_result_list) && !empty($finded_result_list)) {
                        ?>

                        <a class="applied_icon" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Applied", "foodbakery"); ?>">

                            <i class="icon-thumbsup"></i><?php echo esc_html__('Applied', 'foodbakery') ?>

                        </a>

                        <?php
                    } else {
                        ?>

                        <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Apply Now", "foodbakery"); ?>" class="applied_icon" onclick="foodbakery_addjobs_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($foodbakery_post_id); ?>', this)" >

                            <i class="icon-thumbsup"></i><?php esc_html_e('Apply Now', 'foodbakery') ?>

                        </a>

                        <?php
                    }
                } else {
                    ?>

                    <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Apply Now", "foodbakery"); ?>" class="applied_icon" onclick="foodbakery_addjobs_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($foodbakery_post_id); ?>', this)" >

                        <i class="icon-thumbsup"></i><?php echo esc_html__('Apply Now', 'foodbakery') ?>

                    </a>

                    <?php
                }
            }
        } else {
            ?>

            <button type="button" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Apply Now", "foodbakery"); ?>" class="apply-btn" onclick="trigger_func('#btn-header-main-login');"><?php echo esc_html__("Apply Now", "foodbakery"); ?></button>

            <?php
        }
    }

}

/**
 * End Function how to Apply for job
 */
/**

 * Start Function how to Remove Extra Variables using Query String

 */
if (!function_exists('foodbakery_remove_qrystr_extra_var')) {



    function foodbakery_remove_qrystr_extra_var($qStr, $key, $withqury_start = 'yes') {

        $qr_str = preg_replace('/[?&]' . $key . '=[^&]+$|([?&])' . $key . '=[^&]+&/', '$1', $qStr);

        if (!(strpos($qr_str, '?') !== false)) {

            $qr_str = "?" . $qr_str;
        }

        $qr_str = str_replace("?&", "?", $qr_str);

        $qr_str = remove_dupplicate_var_val($qr_str);
        $qr_str = remove_dupplicate_var_val($qr_str);

        if ($withqury_start == 'no') {

            $qr_str = str_replace("?", "", $qr_str);
        }

        return $qr_str;

        die();
    }

}

/**

 * End Function how to Remove Extra Variables using Query String

 */
/**

 * Start Function how to Remove Extra Variables using Query String

 */
if (!function_exists('_string_first_part_match')) {



    function foodbakery_string_first_part_match($str, $find) {

        $str_len = strlen($find); // 6

        $temp_str = substr($str, 0, $str_len);

        if ($temp_str == $find) {

            return true;
        }

        return false;
    }

}

/**

 * End Function how to Remove Extra Variables using Query String

 */
/**

 * Start Function how to get all Countries and Cities Function

 */
if (!function_exists('foodbakery_get_all_countries_cities')) {



    function foodbakery_get_all_countries_cities() {

        global $foodbakery_plugin_options;

        $foodbakery_location_type = isset($foodbakery_plugin_options['foodbakery_search_by_location']) ? $foodbakery_plugin_options['foodbakery_search_by_location'] : '';

        $location_name = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';

        $locations_parent_id = 0;

        $country_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
            'parent' => $locations_parent_id,
        );

        $foodbakery_location_countries = get_terms('foodbakery_locations', $country_args);

        $location_list = array();

        $selectedkey = '';

        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {

            $selectedkey = $_REQUEST['location'];
        }



        if ($foodbakery_location_type == 'countries_only') {

            if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {

                foreach ($foodbakery_location_countries as $key => $country) {

                    $selected = '';

                    if (isset($selectedkey) && $selectedkey == $country->slug) {

                        $selected = 'selected';
                    }

                    if (preg_match("/^$location_name/i", $country->name)) {

                        $location_list[] = array('slug' => $country->slug, 'value' => $country->name);
                    }
                }
            }
        } else if ($foodbakery_location_type == 'countries_and_cities') {

            if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {

                foreach ($foodbakery_location_countries as $key => $country) {

                    $country_added = 0;  // check for country added in array or not

                    $selected = '';

                    if (isset($selectedkey) && $selectedkey == $country->slug) {

                        $selected = 'selected';
                    }

                    if (preg_match("/^$location_name/i", $country->name)) {

                        $location_list[] = array('slug' => $country->slug, 'value' => $country->name);

                        $country_added = 1;
                    }

                    $selected_spec = get_term_by('slug', $country->slug, 'foodbakery_locations');

                    $state_parent_id = $selected_spec->term_id;

                    $cities = '';

                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );

                    $cities = get_terms('foodbakery_locations', $states_args);

                    if (isset($cities) && $cities != '' && is_array($cities)) {

                        $flag_i = 0;

                        foreach ($cities as $key => $city) {

                            if (preg_match("/^$location_name/i", $city->name)) {

                                if ($country_added == 0) { // means if country not added in array then add one time in array for this city
                                    if ($flag_i == 0) {

                                        $location_list[] = array('slug' => $country->slug, 'value' => $country->name);
                                    }
                                }

                                $location_list[]['child'] = array('slug' => $city->slug, 'value' => $city->name);

                                $flag_i++;
                            }
                        }
                    }
                }
            }
        } else if ($foodbakery_location_type == 'cities_only') {

            if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {

                foreach ($foodbakery_location_countries as $key => $country) {

                    $selected = '';

                    $selected_spec = get_term_by('slug', $country->slug, 'foodbakery_locations');

                    $city_parent_id = $selected_spec->term_id;

                    $cities_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $city_parent_id,
                    );

                    $cities = get_terms('foodbakery_locations', $cities_args);

                    if (isset($cities) && $cities != '' && is_array($cities)) {

                        foreach ($cities as $key => $city) {

                            if (preg_match("/^$location_name/i", $city->name)) {

                                $location_list[] = array('slug' => $city->slug, 'value' => $city->name);
                            }
                        }
                    }
                }
            }
        } else {
            $country_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
            );

            $foodbakery_location_countries = get_terms('foodbakery_locations', $country_args);

            if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {

                foreach ($foodbakery_location_countries as $key => $country) {

                    $selected = '';

                    if (isset($selectedkey) && $selectedkey == $country->slug) {

                        $selected = 'selected';
                    }

                    if (preg_match("/^$location_name/i", $country->name)) {

                        $location_list[] = array('slug' => $country->slug, 'value' => $country->name);
                    }
                }
            }
        }

        echo json_encode($location_list);

        die();
    }

    add_action("wp_ajax_foodbakery_get_all_countries_cities", "foodbakery_get_all_countries_cities");

    add_action("wp_ajax_nopriv_foodbakery_get_all_countries_cities", "foodbakery_get_all_countries_cities");
}

/**

 * End Function how to get all Countries and Cities Function

 */
/**

 * Start Function how to get Custom Loaction Using Google Info

 */
if (!function_exists('foodbakery_get_custom_locationswith_google_auto')) {



    function foodbakery_get_custom_locationswith_google_auto($dropdown_start_html = '', $dropdown_end_html = '', $foodbakery_text_ret = false, $foodbakery_top_search = false) {

        global $foodbakery_plugin_options, $foodbakery_form_fields, $foodbakery_form_fields;

        $list_rand = rand(10000, 4999999);
        $foodbakery_location_type = isset($foodbakery_plugin_options['foodbakery_search_by_location']) ? $foodbakery_plugin_options['foodbakery_search_by_location'] : '';

        $location_list = '';

        $selectedkey = '';

        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {

            $selectedkey = $_REQUEST['location'];
        }
        $output = '';

        $output .= '<div class="foodbakery_searchbox_div" data-locationadminurl="' . esc_url(admin_url("admin-ajax.php")) . '">';
        $foodbakery_opt_array = array(
            'std' => $selectedkey,
            'id' => '',
            'before' => '',
            'echo' => false,
            'after' => '',
            'classes' => 'form-control foodbakery_search_location_field',
            'extra_atr' => ' autocomplete="off" placeholder="' . esc_html__('All Locations', 'foodbakery') . '"',
            'cust_name' => '',
            'return' => true,
        );

        $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
        $output .= '<input type="hidden" class="search_keyword" name="location" value="' . $selectedkey . '" />';
        $output .= '</div>';
        echo force_balance_tags($output);
        ?>
        <script type="text/javascript">
            (function ($) {
                jQuery(function () {
                    jQuery('input.foodbakery_search_location_field').cityAutocomplete();

                    jQuery(document).on('click', '.foodbakery_searchbox_div', function () {
                        jQuery('.foodbakery_search_location_field').prop('disabled', false);
                    });

                    jQuery(document).on('click', 'form', function () {
                        var src_loc_val = jQuery(this).find('.foodbakery_search_location_field');
                        src_loc_val.next('.search_keyword').val(src_loc_val.val());
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

}

/**

 * End Function how to get Custom Loaction Using Google Info

 */
/**

 * Start Function how to get Custom Loaction

 */
if (!function_exists('foodbakery_get_custom_locations')) {



    function foodbakery_get_custom_locations($dropdown_start_html = '', $dropdown_end_html = '', $foodbakery_text_ret = false) {
        $output = '';
        $output = '<select class="chosen-select">' . apply_filters('dropdown_options_for_search_location', $output) . '</select>';
        echo force_balance_tags($dropdown_start_html . $output . $dropdown_end_html);
    }

}

/**

 * Start Function how to get Custom Loaction for search element

 */
if (!function_exists('foodbakery_get_custom_locations_restaurant_filter')) {


    // field_type value = filter or header
    function foodbakery_get_custom_locations_restaurant_filter($dropdown_start_html = '', $dropdown_end_html = '', $foodbakery_text_ret = false, $restaurant_short_counter = '', $field_type = 'filter', $dropdown_type = '') {
        global $foodbakery_plugin_options, $foodbakery_form_fields_frontend;

        // getting from plugin options
        $gmap_api_key = isset($foodbakery_plugin_options['foodbakery_google_api_key']) ? $foodbakery_plugin_options['foodbakery_google_api_key'] : '';
        $foodbakery_default_locations_list = isset($foodbakery_plugin_options['foodbakery_default_locations_list']) ? $foodbakery_plugin_options['foodbakery_default_locations_list'] : array();

        $foodbakery_search_result_page = isset($foodbakery_plugin_options['foodbakery_search_result_page']) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
        $redirecturl = isset($foodbakery_search_result_page) && $foodbakery_search_result_page != '' ? get_permalink($foodbakery_search_result_page) . '' : '';

        $default_radius = isset($foodbakery_plugin_options['foodbakery_default_radius_circle']) ? $foodbakery_plugin_options['foodbakery_default_radius_circle'] : 0;
        $geo_location_status = isset($foodbakery_plugin_options['foodbakery_map_geo_location']) ? $foodbakery_plugin_options['foodbakery_map_geo_location'] : '';
        $auto_country_detection = isset($foodbakery_plugin_options['foodbakery_map_auto_country_detection']) ? $foodbakery_plugin_options['foodbakery_map_auto_country_detection'] : '';
        $auto_complete = isset($foodbakery_plugin_options['foodbakery_location_autocomplete']) ? $foodbakery_plugin_options['foodbakery_location_autocomplete'] : '';
        $output = '';
        $selected_item = '';
        if ($dropdown_type == 'list') {
            $output = '';
        } else {
            $selected_item .= '<option value="">' . esc_html__('All Locations', 'foodbakery') . '</option>';
        }

        $selected_location = '';
        if (is_array($foodbakery_default_locations_list) && sizeof($foodbakery_default_locations_list) > 0) {
            foreach ($foodbakery_default_locations_list as $tag_r) {
                $tag_obj = get_term_by('slug', $tag_r, 'foodbakery_locations');
                if (is_object($tag_obj)) {
                    if ($dropdown_type == 'list') {
                        $activ_class = '';
                        if (isset($_REQUEST['location']) && $_REQUEST['location'] == $tag_obj->slug) {
                            $activ_class = ' class="active"';
                        }
                        $selected_item .= '<li' . $activ_class . '><a href="' . add_query_arg(array('location' => $tag_obj->slug), $redirecturl) . '">' . $tag_obj->name . '</a></li>';
                    } else {
                        $selected_item .= '<option value="' . $tag_obj->slug . '" >' . $tag_obj->name . '</option>';
                    }
                }
            }
        }


        $onchange_str = 'foodbakery_empty_loc_polygon(\'' . esc_html($restaurant_short_counter) . '\');foodbakery_restaurant_content(\'' . esc_html($restaurant_short_counter) . '\')';
        if ($field_type != 'filter') {

            if ($redirecturl != '') {
                $onchange_str = 'foodbakery_page_load(this, \'' . esc_html($redirecturl) . '\')';
            } else {
                $onchange_str = '';
            }
        }

        wp_enqueue_script('chosen-ajaxify');
        $location_slug = '';
        if (isset($_REQUEST['loc_polygon']) && $_REQUEST['loc_polygon'] != '') {
            if ($dropdown_type != 'list') {
                $selected_item .= '<option selected value="">' . esc_html__("Drawn Area", "foodbakery") . '</option>';
            }
        } else
        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
            $location_slug = $_REQUEST['location'];
            if ($dropdown_type != 'list') {
                $selected_item .= '<option selected value="' . $location_slug . '">' . ucwords(str_replace("-", " ", $location_slug)) . '</option>';
            }
        }
        if ($dropdown_type == 'list') {
            $output .= $selected_item;
            $output .= '';
        } else {
            $location_value = ( isset($_REQUEST['location']) ) ? $_REQUEST['location'] : '';
            $focus_class = '';
            $location_field_text = '';
            if ($field_type == 'header') {
                $output .= '<li class="select-location">';
            } else {
                $focus_class = 'foodbakery-focus-out';
                $location_field_text = 'location-field-text';
            }
            $foodbakery_search_result_page = isset($foodbakery_plugin_options['foodbakery_search_result_page']) ? get_permalink($foodbakery_plugin_options['foodbakery_search_result_page']) : '';
            $output .= '<div class="foodbakery-locations-fields-group ' . $focus_class . '">';
            $output .= '<form name="foodbakery-restaurant-top-header-form' . $restaurant_short_counter . '" id="foodbakery-restaurant-top-header-form' . $restaurant_short_counter . '" action="' . esc_html($foodbakery_search_result_page) . '" >';
            $output .= '<span id="foodbakery-radius-location' . $restaurant_short_counter . '" class="foodbakery-radius-location foodbakery-radius-location' . $restaurant_short_counter . '" data-id="' . $restaurant_short_counter . '"><i class="icon-target5"></i></span>';
            $location_cross_display = ( isset($_REQUEST['location']) ) ? 'block' : 'none';
            $output .= '<span class="foodbakery-input-cross foodbakery-input-cross' . $restaurant_short_counter . ' foodbakery-input-cross-header" data-id="' . $restaurant_short_counter . '" style="display:' . $location_cross_display . ';"><i class="icon-cross"></i></span>';
            if ($auto_complete == 'on' && $field_type != 'header') {
                $output .= '<input type="text" class="' . $location_field_text . ' foodbakery-locations-field-geo' . $restaurant_short_counter . '" data-id="' . $restaurant_short_counter . '" value="' . $location_value . '" id="foodbakery-locations-field' . $restaurant_short_counter . '" name="location" placeholder="' . esc_html__('All Locations', 'foodbakery') . '" autocomplete="off">';
            } else {
                $output .= '<input type="text" class="' . $location_field_text . ' foodbakery-locations-field' . $restaurant_short_counter . '" data-id="' . $restaurant_short_counter . '" value="' . $location_value . '" id="foodbakery-locations-field' . $restaurant_short_counter . '" name="location" placeholder="' . esc_html__('All Locations', 'foodbakery') . '" autocomplete="off">';
            }
            $output .= '<input type="hidden" class="foodbakery-locations-position' . $restaurant_short_counter . '" value="' . $field_type . '" id="foodbakery-locations-position' . $restaurant_short_counter . '" name="foodbakery_locations_position">';
            $radius = isset($_REQUEST['foodbakery_radius']) ? $_REQUEST['foodbakery_radius'] : $default_radius;

            if ($geo_location_status == 'on') {
                $output .= '<span id="foodbakery-geo-location' . $restaurant_short_counter . '" data-id="' . $restaurant_short_counter . '"><span class="loc-icon-holder"><i class="icon-target3"></i></span></span>';

                $radius_display = 'none';
                $output .= '<div class="select-location foodbakery-radius-range' . $restaurant_short_counter . '" style="display:' . $radius_display . '"><div class="select-popup popup-open" id="popup' . $restaurant_short_counter . '"> <a href="javascript:;" id="close' . $restaurant_short_counter . '" class="location-close-popup location-close-popup' . $restaurant_short_counter . '"><i class="icon-times"></i></a>';
                $output .= $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                        array(
                            'simple' => true,
                            'cust_id' => "range-hidden-foodbakery-radius" . $restaurant_short_counter,
                            'cust_name' => "foodbakery_radius",
                            'std' => $radius,
                            'classes' => "foodbakery-radius",
                            'return' => true,
                            'extra_atr' => 'data-id="' . $restaurant_short_counter . '"',
                        )
                );
                $output .= '<p>' . esc_html__('Show with in', 'foodbakery') . '</p>
                                <input id="ex16b' . $restaurant_short_counter . '" type="text" />
                                <span id="ex16b' . $restaurant_short_counter . 'CurrentSliderValLabel">' . esc_html__('Miles', 'foodbakery') . ': <span id="ex16b' . $restaurant_short_counter . 'SliderVal">' . $radius . '</span></span>';
                $output .= '<br><p class="my-location">' . esc_html__('of', 'foodbakery') . ' <i class="cs-color icon-location-arrow"></i><a id="foodbakery-geo-location-all" class="cs-color foodbakery-geo-location' . $restaurant_short_counter . '" href="javascript:void(0)">' . esc_html__('My location', 'foodbakery') . '</a></p>';
                $output .= '</div></div>';

                $output .= '<script>
                        jQuery(document).ready(function() {
							var elem = jQuery("#ex16b' . $restaurant_short_counter . '");
							if (elem.length != "") {
								elem.slider({
									step : 1,
									min: 0,
									max: 500,
									value: ' . $radius . ',
								});
								elem.on("slideStop", function () {
										var rang_slider_val = elem.val();
										jQuery("#ex16b' . $restaurant_short_counter . 'SliderVal").html(rang_slider_val);
										jQuery("#range-hidden-foodbakery-radius' . $restaurant_short_counter . '").val(rang_slider_val);
										//foodbakery_restaurant_content("' . esc_html($restaurant_short_counter) . '");
									});
								elem.on("slide", function () {
										jQuery("#ex16b' . $restaurant_short_counter . 'SliderVal").html( elem.val() );
								});
                            }
							
							$(".location-close-popup' . $restaurant_short_counter . '").click(function() {
								$(".foodbakery-radius-range' . $restaurant_short_counter . '").hide();
							});
                        });
						
                    </script>';
            }

            $output .= '</form>';
            $output .= '</div>';
            if ($field_type == 'header') {
                $output .= '<li class="popular-location">' . esc_html__('Popular Locations', 'foodbakery') . '</li>';
            }
            $output .= '<li><ul class="foodbakery-all-locations' . $restaurant_short_counter . '">';
            $output .= '</ul></li>';
        }
        if ($dropdown_type != 'list') {
            if (false === ( $foodbakery_location_data = get_transient('foodbakery_location_data') )) {
                $output .= '<script>
				jQuery(document).ready(function () {
					jQuery(".chosen-select-location").chosen();
					chosen_ajaxify("filter-location-box' . $restaurant_short_counter . '", "' . esc_url(admin_url('admin-ajax.php')) . '", "dropdown_options_for_search_location_data");
					$(document).on("click", ".foodbakery-all-locations' . $restaurant_short_counter . ' li", select_item_from_dropdown);
				});
				</script>';
            } else {
                $output .= '<script>
				jQuery(document).ready(function () {
					$("#filter-location-box' . $restaurant_short_counter . '").after(\'<span class="chosen-ajaxify-loader"><img src="' . wp_foodbakery::plugin_url() . 'assets/frontend/images/ajax-loader.gif" alt=""></span>\');                
					var location_data_json = \'' . str_replace("'", "", $foodbakery_location_data) . '\';
					var location_data_json_obj = JSON.parse(location_data_json);
					jQuery.each(location_data_json_obj, function() {
						var location_selected = \'\';
						if(this.value == \'' . $location_slug . '\'){
                                                    location_selected = \'selected\';
						}
						jQuery("#filter-location-box' . $restaurant_short_counter . '").append(
                            jQuery("<option" + location_selected + "></option>").text(this.caption).val(this.value)
						);
					});
					$(document).on("click", ".foodbakery-all-locations' . $restaurant_short_counter . ' li", select_item_from_dropdown);
					$("#filter-location-box' . $restaurant_short_counter . '").next("span.chosen-ajaxify-loader").remove();
				});
				</script>';
            }
        }

        $output .= '<script>
				function select_item_from_dropdown( e ) {
					var this_value  = jQuery(this).text();
					jQuery(".foodbakery-locations-field' . $restaurant_short_counter . '").val(this_value);
					$("#foodbakery-restaurant-top-header-form' . $restaurant_short_counter . '").submit();
					return false;
			    }
				
                jQuery(document).ready(function () {
                    jQuery(".chosen-select-location").chosen();

                    /*
                    * Locations search
                    */
                   $(document).on("keyup", ".foodbakery-locations-field' . $restaurant_short_counter . '", function () {
                       var this_value  = jQuery(this).val();
                       var this_position  = jQuery("#foodbakery-locations-position' . $restaurant_short_counter . '").val();
                       if( this_value.length > 2 ){
                           jQuery.ajax({
                               type: "POST",
                               url: foodbakery_globals.ajax_url,
                               data: "this_position="+this_position+"&keyword="+this_value+"&action=foodbakery_get_all_locations",
                               success: function (response) {
                                   jQuery(".foodbakery-all-locations' . $restaurant_short_counter . '").html(response);
                               }
                           });
                       } else {
                           jQuery.ajax({
                               type: "POST",
                               url: foodbakery_globals.ajax_url,
                               data: "this_position="+this_position+"&action=foodbakery_get_all_default_locations",
                               success: function (response) {
                                   jQuery(".foodbakery-all-locations' . $restaurant_short_counter . '").html(response);
                               }
                           });
                       }
					   
					   $(document).on("click", ".foodbakery-all-locations' . $restaurant_short_counter . ' li", select_item_from_dropdown);
                   });
				   
				   $(document).on("keypress", ".foodbakery-locations-field' . $restaurant_short_counter . '", function (e) {
						var key = e.keyCode || e.which;
						if (key == 13){ 
							$("#foodbakery-restaurant-top-header-form' . $restaurant_short_counter . '").submit();
							return false;
						}
				   });

                   $(document).on("focus", ".foodbakery-locations-field' . $restaurant_short_counter . '", function () {
                       jQuery("#range-hidden-foodbakery-radius").val(0);
                       jQuery(".foodbakery-radius-range' . $restaurant_short_counter . '").hide();
                       jQuery(this).keyup();
                   });

                   $(document).on("click", ".foodbakery-all-locations' . $restaurant_short_counter . ' li", function () {
                       var this_value  = jQuery(this).text();
                       jQuery(".foodbakery-locations-field' . $restaurant_short_counter . '").val(this_value);
                       var this_position  = jQuery("#foodbakery-locations-position' . $restaurant_short_counter . '").val();
                       if( this_position != "header" ){
                            var data_counter    = jQuery(".foodbakery-locations-field' . $restaurant_short_counter . '").data("id");
                            jQuery("#range-hidden-foodbakery-radius' . $restaurant_short_counter . '").val(0);
                            jQuery(".foodbakery-radius-range' . $restaurant_short_counter . '").hide();
                            foodbakery_restaurant_content(data_counter);
                       }
                   });
                   $(document).on("click", "body", function () {
                        var data_id     = jQuery(".location-field-text").data("id");
                        jQuery(".foodbakery-all-locations"+data_id).html("");
                   });
                   
                    $(document).on("click", ".foodbakery-input-cross", function () {
                        var data_id = jQuery(this).data("id");
                        jQuery(".foodbakery-locations-field"+data_id).val("");
                        jQuery(".foodbakery-locations-field"+data_id).keyup();
                        jQuery(".foodbakery-locations-field-geo"+data_id).val("");
                        jQuery(".foodbakery-locations-field-geo"+data_id).keyup();
                        jQuery("body").click();
                        jQuery("#range-hidden-foodbakery-radius' . $restaurant_short_counter . '").val(0);
                        jQuery(".foodbakery-radius-range' . $restaurant_short_counter . '").hide();
                        
                        jQuery(".foodbakery-input-cross' . $restaurant_short_counter . '").hide();
                    });
                    
                    $(document).on("change", ".location-field-text", function(){
                        this_text   = jQuery(this).val();
                        if(this_text == ""){
                            jQuery(".foodbakery-input-cross' . $restaurant_short_counter . '").hide();
                        }
                    });
                    
                    $(document).on("click", ".foodbakery-geo-location' . $restaurant_short_counter . '", function () {
                     var gmap_api_key  = "' . $gmap_api_key . '";
						var _this_f = $(this);
                        var data_id = jQuery(this).data("id");
                        jQuery(".foodbakery-locations-field-geo"+data_id).val("");
                        jQuery("#range-hidden-foodbakery-radius").val(' . $default_radius . ');
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
								jQuery.ajax({
									url: "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + position.coords.latitude + "," + position.coords.longitude + "&sensor=true&key="+gmap_api_key,
									type: "POST",
									dataType: "json",
									success: function (response) {
										if ( typeof response.results[0] != "undefined" ) {
											jQuery("#foodbakery-locations-field' . $restaurant_short_counter . '").val( response.results[0].formatted_address );
											
											jQuery(".foodbakery-input-cross' . $restaurant_short_counter . '").show();
											_this_f.parents("form").submit();
										}
									}
								});
                            });
                        }
                    });
                    
                    $(".foodbakery-radius-location' . $restaurant_short_counter . '").click( function() {
						var data_id = jQuery(this).data("id");
						$(".foodbakery-radius-range"+data_id).show();
					});
                                  
                    
                });
                
                function fillInAddress() {
                  var place = autocomplete.getPlace();
                  var city_name = "";
                  $.each( place.address_components, function( key, value ) {
                        check_city  = $.inArray( "locality", value.types );
                        if( check_city != -1){
                            var city_name = value.long_name;
                            $(".foodbakery-locations-field-geo' . $restaurant_short_counter . '").val(city_name);
                        }
                  });
                  $("#range-hidden-foodbakery-radius").val(0);
                  var data_id   = $(".foodbakery-locations-field-geo' . $restaurant_short_counter . '").data("id");
                  $(".foodbakery-input-cross' . $restaurant_short_counter . '").show();
                  foodbakery_restaurant_content(data_id);
                }
                
                function foodbakery_current_lat_long(){
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var pos = {
                              lat: position.coords.latitude,
                              lng: position.coords.longitude
                            };
                            return pos;
                        });
                    }
                }
                
                </script>';

        if ($auto_country_detection == 'on' && !is_home()) {
            $output .= '
			<script>
			$( window ).load(function() {
				if ( typeof isCountryAlreadyDetected != "undefined" ) {
					return true;
				}
				isCountryAlreadyDetected = true;
				var current_value = jQuery("input[name=\'location\']").val();
				
				if(current_value == "" && ( window.location.search.indexOf("location=") < 0 ) ) {
					
					/*jQuery.getJSON("https://freegeoip.net/json/", function(data) {
						
						if( typeof data.city != "undefined" && data.city.trim() != "" ) {
							current_value = data.city;
						}
						if( typeof data.region_name != "undefined" && data.region_name.trim() != "" ) {
							if ( current_value != "") {
								current_value += " ";
							}
							current_value += data.region_name;
						}
						if( typeof data.zip_code != "undefined" && data.zip_code.trim() != "" ) {
							if ( current_value != "") {
								current_value += " ";
							}
							current_value += data.zip_code;
						}
						if( typeof data.country_name != "undefined" && data.country_name.trim() != "" ) {
							if ( current_value != "") {
								current_value += ", ";
							}
							current_value += data.country_name;
						} 
						
						if ( current_value != "" ) {
							jQuery(".foodbakery-locations-fields-group input[name=\'location\'], .foodbakery-locations-fields-group .location-field-text").val( current_value );
							//jQuery(".location-field-text").val( current_value );
							//jQuery("input[name=\'location\']").val( current_value );
							//foodbakery_restaurant_content(data_id);
							jQuery(".foodbakery-input-cross").show();
						}
					});*/
				}
				
			});
            </script>';
        }
        if ($field_type == 'header') {
            $output .= '<script>
                    jQuery.ajax({
                       type: "POST",
                       url: foodbakery_globals.ajax_url,
                       data: "this_position=' . $field_type . '&action=foodbakery_get_all_default_locations",
                       success: function (response) {
                           jQuery(".foodbakery-all-locations' . $restaurant_short_counter . '").html(response);
                       }
                   });
                    $(document).on("click", ".location-has-children > a", function () {
                         jQuery.ajax({
                               type: "POST",
                               url: foodbakery_globals.ajax_url,
                               data: "keyword=&action=foodbakery_get_all_locations",
                               success: function (response) {
                               }
                           });
                    });
                </script>';
        }
        if ($field_type == 'header') {
            $output .= '</li>';
        }
        $output = force_balance_tags($dropdown_start_html . $output . $dropdown_end_html);
        echo str_replace('</script></script>', '</script>', $output);
    }

}

/**

 * End Function how to get Custom Loaction

 */
/**

 * Start Function how to Convert  Custom Loaction

 */
if (!function_exists('foodbakery_location_convert')) {



    function foodbakery_location_convert() {

        global $foodbakery_plugin_options;

        $foodbakery_location_type = isset($foodbakery_plugin_options['foodbakery_search_by_location']) ? $foodbakery_plugin_options['foodbakery_search_by_location'] : '';

        $foodbakery_field_ret = true;

        $selectedkey = '';

        $locations_parent_id = 0;

        $country_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
            'parent' => $locations_parent_id,
        );

        $foodbakery_location_countries = get_terms('foodbakery_locations', $country_args);

        if (isset($_GET['location']) && $_GET['location'] != '') {

            $selectedkey = $_GET['location'];
        }

        if ($foodbakery_location_type == 'countries_only') {

            if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {

                foreach ($foodbakery_location_countries as $key => $country) {

                    $selected = '';

                    if (isset($selectedkey) && $selectedkey == $country->slug) {

                        $foodbakery_field_ret = false;
                    }
                }
            }
        } else if ($foodbakery_location_type == 'countries_and_cities') {

            if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {

                foreach ($foodbakery_location_countries as $key => $country) {

                    $selected = '';

                    if (isset($selectedkey) && $selectedkey == $country->slug) {

                        $foodbakery_field_ret = false;
                    }

                    $selected_spec = get_term_by('slug', $country->slug, 'foodbakery_locations');

                    $cities = '';

                    $state_parent_id = $selected_spec->term_id;

                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );

                    $cities = get_terms('foodbakery_locations', $states_args);

                    if (isset($cities) && $cities != '' && is_array($cities)) {

                        foreach ($cities as $key => $city) {

                            if ($selectedkey == $city->slug) {

                                $foodbakery_field_ret = false;
                            }
                        }
                    }
                }
            }
        } else if ($foodbakery_location_type == 'cities_only') {



            if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {

                foreach ($foodbakery_location_countries as $key => $country) {

                    $selected = '';

                    // load all cities against state  

                    $cities = '';

                    $selected_spec = get_term_by('slug', $country->slug, 'foodbakery_locations');

                    $state_parent_id = $selected_spec->term_id;

                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );

                    $cities = get_terms('foodbakery_locations', $states_args);

                    if (isset($cities) && $cities != '' && is_array($cities)) {

                        foreach ($cities as $key => $city) {

                            if ($selectedkey == $city->slug) {

                                $foodbakery_field_ret = false;
                            }
                        }
                    }
                }
            }
        }

        if ($foodbakery_field_ret == true && $selectedkey != '') {

            return $selectedkey;
        }

        return '';
    }

}

/**

 * End Function how to Convert  Custom Loaction

 */
/**

 * Start Function how to Count User Meta

 */
if (!function_exists('count_usermeta')) {



    function count_usermeta($key, $value, $opr, $return = false) {

        $arg = array(
            'meta_key' => $key,
            'meta_value' => $value,
            'meta_compare' => $opr,
        );

        $users = get_users($arg);

        if ($return == true) {

            return $users;
        }

        return count($users);
    }

}

/**

 * End Function how to Count User Meta

 */
/**

 * Start Function get to Post Meta

 */
if (!function_exists('foodbakery_get_postmeta_data')) {



    function foodbakery_get_postmeta_data($key, $value, $opr, $post_type, $return = false) {



        $user_post_arr = array('posts_per_page' => "-1", 'post_type' => $post_type, 'order' => "DESC", 'orderby' => 'post_date',
            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $value,
                    'compare' => $opr,
                )
            )
        );

        $user_data = get_posts($user_post_arr);

        if ($return == true) {

            return $user_data;
        }
    }

}

/**

 * End Function get to Post Meta

 */
/**

 * Start Function how to Count Post Meta

 */
if (!function_exists('count_postmeta')) {



    function count_postmeta($key, $value, $opr, $return = false) {

        $mypost = array('posts_per_page' => "-1", 'post_type' => 'employer', 'order' => "DESC", 'orderby' => 'post_date',
            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $value,
                    'compare' => $opr,
                )
            )
        );

        $loop_count = new WP_Query($mypost);

        $count_post = $loop_count->post_count;

        return $count_post;
    }

}

/**

 * End Function how to Count Post Meta

 */
/**

 * Start Function how to Count Candidate Post Meta

 */
if (!function_exists('candidate_count_postmeta')) {



    function candidate_count_postmeta($key, $value, $opr, $return = false) {

        $mypost = array('posts_per_page' => "-1", 'post_type' => 'candidate', 'order' => "DESC", 'orderby' => 'post_date',
            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $value,
                    'compare' => $opr,
                )
            )
        );

        $loop_count = new WP_Query($mypost);

        $count_post = $loop_count->post_count;

        $users = '';

        while ($loop_count->have_posts()): $loop_count->the_post();

            global $post;

            $users = $post;

        endwhile;

        wp_reset_postdata();

        if ($return == true) {

            return $users;
        }

        return $count_post;
    }

}

/**

 * End Function how to Count Candidate Post Meta

 */
/**

 *

 * @check array emptiness

 *

 */
if (!function_exists('is_array_empty')) {



    function is_array_empty($a) {

        foreach ($a as $elm)
            if (!empty($elm))
                return false;

        return true;
    }

}

/**

 *

 * @find heighes date index

 *

 */
if (!function_exists('find_heighest_date_index')) {



    function find_heighest_date_index($foodbakery_dates, $date_format = 'd-m-Y') {

        $max = max(array_map('strtotime', $foodbakery_dates));

        $finded_date = date($date_format, $max);

        $maxs = array_keys($foodbakery_dates, $finded_date);

        if (isset($maxs[0])) {

            return $maxs[0];
        }
    }

}

/**

 * Start Function how to Save last User login Save

 */
if (!function_exists('user_last_login')) {

    add_action('wp_login', 'user_last_login', 0, 2);

    function user_last_login($login, $user) {

        $user = get_user_by('login', $login);

        $now = time();

        update_user_meta($user->ID, 'user_last_login', $now);
    }

}

/**

 * End Function how to Save last User login Save

 */
/**

 * Start Function how to Get last User login Save

 */
if (!function_exists('get_user_last_login')) {



    function get_user_last_login($user_ID = '') {

        if ($user_ID == '') {

            $user_ID = get_current_user_id();
        }

        $key = 'user_last_login';

        $single = true;

        $user_last_login = get_user_meta($user_ID, $key, $single);

        return $user_last_login;
    }

}

/**

 * End Function how to Get last User login Save

 */
/**

 *

 * @get user registeration time

 *

 */
if (!function_exists('get_user_registered_timestamp')) {



    function get_user_registered_timestamp($user_ID = '') {

        if ($user_ID == '') {

            $user_ID = get_current_user_id();
        }

        if (isset(get_userdata($user_ID)->user_registered)) {

            $user_registered_str = strtotime(get_userdata($user_ID)->user_registered);

            return $user_registered_str;
        } else {

            return '';
        }
    }

}

/**

 * Start Function how to Get User Cv Selected in List Meta

 */
if (!function_exists('foodbakery_get_user_cv_selected_list_meta')) {



    function foodbakery_get_user_cv_selected_list_meta($user = "") {

        if (!empty($user)) {

            $userdata = get_user_by('login', $user);

            $user_id = $userdata->ID;

            return get_user_meta($user_id, 'cs-candidate-selected-list', true);
        } else {

            return get_user_meta(foodbakery_get_user_id(), 'cs-candidate-selected-list', true);
        }
    }

}

/**

 * End Function how to Get User Cv Selected in List Meta

 */
/**

 * Start Function how to Update User Cv Selected CV Meta

 */
if (!function_exists('foodbakery_update_user_cv_selected_list_meta')) {



    function foodbakery_update_user_cv_selected_list_meta($arr) {

        return update_user_meta(foodbakery_get_user_id(), 'cs-candidate-selected-list', $arr);
    }

}

/**

 * End Function how to Get User Cv Selected in List Meta

 */
/**

 * Start Function how to Add  User In Selected Cv  Meta

 */
if (!function_exists('foodbakery_add_cv_selected_list_usermeta')) {



    function foodbakery_add_cv_selected_list_usermeta() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                $foodbakery_selected_list = foodbakery_get_user_cv_selected_list_meta();

                $foodbakery_selected_list = (isset($foodbakery_selected_list) and is_array($foodbakery_selected_list)) ? $foodbakery_selected_list : array();

                if (isset($foodbakery_selected_list) && in_array($_POST['post_id'], $foodbakery_selected_list)) {

                    $post_id = array();

                    $post_id[] = $_POST['post_id'];

                    $foodbakery_selected_list = array_diff($post_id, $foodbakery_selected_list);

                    foodbakery_update_user_cv_selected_list_meta($foodbakery_selected_list);

                    esc_html_e('Added to List', 'foodbakery');

                    die();
                }

                $foodbakery_selected_list = array();

                $foodbakery_selected_list = get_user_meta(foodbakery_get_user_id(), 'cs-candidate-selected-list', true);

                $foodbakery_selected_list[] = $_POST['post_id'];

                $foodbakery_selected_list = array_unique($foodbakery_selected_list);

                update_user_meta(foodbakery_get_user_id(), 'cs-candidate-selected-list', $foodbakery_selected_list);

                $user_watchlist = get_user_meta(foodbakery_get_user_id(), 'cs-candidate-selected-list', true);

                esc_html_e('Added to List', 'foodbakery');
                ?>

                <div class="outerwrapp-layer<?php echo esc_html($_POST['post_id']); ?> cs-added-msg">

                    <?php esc_html_e('Added to Selected List', 'foodbakery'); ?>

                </div>

                <?php
            }
        } else {

            esc_html_e('You have to login first.', 'foodbakery');
        }

        die();
    }

    add_action("wp_ajax_foodbakery_add_cv_selected_list_usermeta", "foodbakery_add_cv_selected_list_usermeta");

    add_action("wp_ajax_nopriv_foodbakery_add_cv_selected_list_usermeta", "foodbakery_add_cv_selected_list_usermeta");
}

/**

 * End Function how to Add  User In Selected Cv  Meta

 */
/**

 * Start Function how to Remove  User In Selected Cv

 */
if (!function_exists('foodbakery_remove_cv_selected_list_usermeta')) {



    function foodbakery_remove_cv_selected_list_usermeta() {

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            if (isset($_POST['post_id']) && $_POST['post_id'] <> '') {

                $foodbakery_selected_list = foodbakery_get_user_cv_selected_list_meta();

                $foodbakery_selected_list = (isset($foodbakery_selected_list) and is_array($foodbakery_selected_list)) ? $foodbakery_selected_list : array();

                $post_id = array();

                $post_id[] = $_POST['post_id'];

                $foodbakery_selected_list = array_diff($foodbakery_selected_list, $post_id);

                foodbakery_update_user_cv_selected_list_meta($foodbakery_selected_list);

                echo esc_html__('Add to List', 'foodbakery') . '<div class="outerwrapp-layer' . $_POST['post_id'] . ' cs-remove-msg">';

                esc_html_e('Removed From Selected List', 'foodbakery');

                echo '</div>';
            } else {

                esc_html_e('You are not authorised', 'foodbakery');
            }
        } else {

            esc_html_e('You have to login first.', 'foodbakery');
        }



        die();
    }

    add_action("wp_ajax_foodbakery_remove_cv_selected_list_usermeta", "foodbakery_remove_cv_selected_list_usermeta");

    add_action("wp_ajax_nopriv_foodbakery_remove_cv_selected_list_usermeta", "foodbakery_remove_cv_selected_list_usermeta");
}

/**

 * End Function how to Remove  User In Selected Cv

 */
/**

 * Start Function how to Add Enqueue Scripts

 */
if (!function_exists('my_enqueue_scripts')) {

    add_action('wp_print_scripts', 'my_enqueue_scripts');

    function my_enqueue_scripts() {

        wp_enqueue_script('tiny_mce');
    }

}

/**

 * End Function how to Add Enqueue Scripts

 */
/**

 * Start Function how to Get Job Type Restaurants in Dropdown

 */
if (!function_exists('get_job_type_dropdown')) {



    function get_job_type_dropdown($name, $id, $selected_post_id = '', $class = '', $required_status = 'false') {

        global $foodbakery_form_fields;

        $selected_slug = '';

        $required = '';

        if ($required_status == 'true') {

            $required = ' required';
        }

        if ($selected_post_id != '') {

            // get all job types

            $all_job_type = get_the_terms($selected_post_id, 'job_type');

            $job_type_values = '';

            $job_type_class = '';

            $specialism_flag = 1;

            if ($all_job_type != '') {

                foreach ($all_job_type as $job_typeitem) {

                    $selected_slug = $job_typeitem->term_id;
                }
            }
        }

        $job_types_all_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
        );

        $all_job_types = get_terms('job_type', $job_types_all_args);

        $select_options = '';

        if (isset($all_job_types) && is_array($all_job_types)) {

            foreach ($all_job_types as $job_typesitem) {

                $select_options[$job_typesitem->term_id] = $job_typesitem->name;
            }
        }

        $foodbakery_opt_array = array(
            'cust_id' => $id,
            'cust_name' => $name,
            'std' => $selected_slug,
            'desc' => '',
            'extra_atr' => 'data-placeholder="' . esc_html__("Please Select", "foodbakery") . '"',
            'classes' => $class,
            'options' => $select_options,
            'hint_text' => '',
            'required' => 'yes',
        );

        if (isset($required_status) && $required_status == 'true') {

            $foodbakery_opt_array['required'] = 'yes';
        }

        $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
    }

}

/**

 * End Function how to Get Job Type Restaurants in Dropdown

 */
/**

 * Start Function how to Get specialisms Restaurants in Dropdown

 */
if (!function_exists('get_job_specialisms_dropdown')) {



    function get_job_specialisms_dropdown($name, $id, $selected_post_id = '', $class = '', $required_status = 'false') {

        global $foodbakery_form_fields;

        $selected_slug = array();

        $required = '';

        if ($required_status == 'true') {

            $required = ' required';
        }

        if ($selected_post_id != '') {

            // get all job types			

            $all_specialisms = get_the_terms($selected_post_id, 'specialisms');

            $specialisms_values = '';

            $specialisms_class = '';

            $specialism_flag = 1;

            if ($all_specialisms != '') {

                foreach ($all_specialisms as $specialismsitem) {

                    $selected_slug[] = $specialismsitem->term_id;
                }
            }
        }

        //var_dump($selected_slug);

        $specialisms_all_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
        );

        $all_specialisms = get_terms('specialisms', $specialisms_all_args);
        $select_options = '';

        if (isset($all_specialisms) && is_array($all_specialisms)) {

            foreach ($all_specialisms as $specialismsitem) {

                $select_options[$specialismsitem->term_id] = $specialismsitem->name;
            }
        }

        $foodbakery_opt_array = array(
            'id' => $id,
            'cust_id' => $id,
            'cust_name' => $name . '[]',
            'std' => $selected_slug,
            'desc' => '',
            'extra_atr' => 'data-placeholder="' . esc_html__("Please Select specialism", "foodbakery") . '"',
            'classes' => $class,
            'options' => $select_options,
            'hint_text' => '',
            'required' => 'yes',
        );

        if (isset($required_status) && $required_status == 'true') {

            $foodbakery_opt_array['required'] = 'yes';
        }

        $foodbakery_form_fields->foodbakery_form_multiselect_render($foodbakery_opt_array);
    }

}

/**

 * End Function how to Get specialisms Restaurants in Dropdown

 */
/**

 * Start Function how to Add specialisms  in Dropdown

 */
if (!function_exists('get_specialisms_dropdown')) {



    function get_specialisms_dropdown($name, $id, $user_id = '', $class = '', $required_status = 'false') {

        global $foodbakery_form_fields, $post;

        $output = '';

        $foodbakery_spec_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
        );

        $terms = get_terms('specialisms', $foodbakery_spec_args);

        if (!empty($terms)) {



            $foodbakery_selected_specs = get_user_meta($user_id, $name, true);

            $specialisms_option = '';

            foreach ($terms as $term) {

                $foodbakery_selected = '';

                if (is_array($foodbakery_selected_specs) && in_array($term->slug, $foodbakery_selected_specs)) {

                    $foodbakery_selected = ' selected="selected"';
                }

                $specialisms_option .= '<option' . $foodbakery_selected . ' value="' . esc_attr($term->slug) . '">' . $term->name . '</option>';
            }

            $foodbakery_opt_array = array(
                'cust_id' => $id,
                'cust_name' => $name . '[]',
                'std' => '',
                'desc' => '',
                'return' => true,
                'extra_atr' => 'data-placeholder="' . esc_html__("Please Select Specialism", "foodbakery") . '"',
                'classes' => $class,
                'options' => $specialisms_option,
                'options_markup' => true,
                'hint_text' => '',
            );

            if (isset($required_status) && $required_status == true) {

                $foodbakery_opt_array['required'] = 'yes';
            }

            $output .= $foodbakery_form_fields->foodbakery_form_multiselect_render($foodbakery_opt_array);
        } else {

            $output .= esc_html__('There are no specialisms available.', 'foodbakery');
        }

        return $output;
    }

}

/**

 * End Function how to Add specialisms  in Dropdown

 */
/**

 * Start Function how to Add images sizes and their URL's

 */
if (!function_exists('foodbakery_get_img_url')) {



    function foodbakery_get_img_url($img_name = '', $size = 'foodbakery_media_2', $return_sizes = false, $dir_filter = true) {

        $ret_name = '';

        $foodbakery_img_sizes = array(
            'foodbakery_media_1' => '-870x489',
            'foodbakery_media_2' => '-270x203',
            'foodbakery_media_3' => '-236x168',
            'foodbakery_media_4' => '-200x200',
            'foodbakery_media_5' => '-180x135',
            'foodbakery_media_6' => '-150x113',
        );

        if ($return_sizes == true) {

            return $foodbakery_img_sizes;
        }

        // Register our new path for user images.

        if ($dir_filter == true) {

            add_filter('upload_dir', 'foodbakery_user_images_custom_foodbakery');
        }

        $foodbakery_upload_dir = wp_upload_dir();

        $foodbakery_upload_sub_dir = '';

        if ((strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) !== false)) {

            if (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) + strlen($foodbakery_img_sizes['foodbakery_media_1'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) + strlen($foodbakery_img_sizes['foodbakery_media_2'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) + strlen($foodbakery_img_sizes['foodbakery_media_3'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) + strlen($foodbakery_img_sizes['foodbakery_media_4'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) + strlen($foodbakery_img_sizes['foodbakery_media_5'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) + strlen($foodbakery_img_sizes['foodbakery_media_6'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']));
            }



            $foodbakery_upload_dir = isset($foodbakery_upload_dir['url']) ? $foodbakery_upload_dir['url'] . '/' : '';

            $foodbakery_upload_dir = $foodbakery_upload_dir . $foodbakery_upload_sub_dir;

            if ($ret_name != '') {

                if (isset($foodbakery_img_sizes[$size])) {

                    $ret_name = $foodbakery_upload_dir . $ret_name . $foodbakery_img_sizes[$size] . $img_ext;
                } else {

                    $ret_name = $foodbakery_upload_dir . $ret_name . $img_ext;
                }
            }
        } else {

            if ($img_name != '') {



                $ret_name = '';
            }
        }

        // Set everything back to normal.

        if ($dir_filter == true) {

            remove_filter('upload_dir', 'foodbakery_user_images_custom_foodbakery');
        }

        return $ret_name;
    }

}

/**

 * End Function how to Add images sizes and their URL's

 */
/**

 * Start Function how to  get image

 */
if (!function_exists('foodbakery_get_orignal_image_nam')) {



    function foodbakery_get_orignal_image_nam($img_name = '', $size = 'foodbakery_media_2') {

        $ret_name = '';

        $foodbakery_img_sizes = array(
            'foodbakery_media_1' => '-870x489',
            'foodbakery_media_2' => '-270x203',
            'foodbakery_media_3' => '-236x168',
            'foodbakery_media_4' => '-200x200',
            'foodbakery_media_5' => '-180x135',
            'foodbakery_media_6' => '-150x113',
        );

        if ((strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) !== false)) {

            if (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) + strlen($foodbakery_img_sizes['foodbakery_media_1'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) + strlen($foodbakery_img_sizes['foodbakery_media_2'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) + strlen($foodbakery_img_sizes['foodbakery_media_3'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) + strlen($foodbakery_img_sizes['foodbakery_media_4'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) + strlen($foodbakery_img_sizes['foodbakery_media_5'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) + strlen($foodbakery_img_sizes['foodbakery_media_6'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']));
            }

            $foodbakery_upload_dir = isset($foodbakery_upload_dir['url']) ? $foodbakery_upload_dir['url'] . '/' : '';

            if ($ret_name != '') {

                if (isset($foodbakery_img_sizes[$size])) {

                    $ret_name = $foodbakery_upload_dir . $ret_name . $foodbakery_img_sizes[$size] . $img_ext;
                } else {

                    $ret_name = $foodbakery_upload_dir . $ret_name . $img_ext;
                }
            }
        } else {

            if ($img_name != '') {



                $ret_name = '';
            }
        }



        return $ret_name;
    }

}

/**

 * Start Function how to  get image

 */
if (!function_exists('foodbakery_get_image_url')) {



    function foodbakery_get_image_url($img_name = '', $size = 'foodbakery_media_2', $return_sizes = false) {

        $ret_name = '';

        $foodbakery_img_sizes = array(
            'foodbakery_media_1' => '-870x489',
            'foodbakery_media_2' => '-270x203',
            'foodbakery_media_3' => '-236x168',
            'foodbakery_media_4' => '-200x200',
            'foodbakery_media_5' => '-180x135',
            'foodbakery_media_6' => '-150x113',
        );

        if ($return_sizes == true) {

            return $foodbakery_img_sizes;
        }

        add_filter('upload_dir', 'foodbakery_user_images_custom_foodbakery');

        $foodbakery_upload_dir = wp_upload_dir();

        $foodbakery_upload_sub_dir = '';

        if ((strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) !== false) || (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) !== false)) {

            if (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']) + strlen($foodbakery_img_sizes['foodbakery_media_1'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_1']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']) + strlen($foodbakery_img_sizes['foodbakery_media_2'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_2']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']) + strlen($foodbakery_img_sizes['foodbakery_media_3'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_3']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']) + strlen($foodbakery_img_sizes['foodbakery_media_4'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_4']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) + strlen($foodbakery_img_sizes['foodbakery_media_5'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']));
            } elseif (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) !== false) {

                $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']) + strlen($foodbakery_img_sizes['foodbakery_media_6'])), strlen($img_name));

                $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_6']));
            }

            $foodbakery_upload_dir = isset($foodbakery_upload_dir['url']) ? $foodbakery_upload_dir['url'] . '/' : '';

            $foodbakery_upload_dir = $foodbakery_upload_dir . $foodbakery_upload_sub_dir;

            if ($ret_name != '') {

                if (isset($foodbakery_img_sizes[$size])) {

                    $ret_name = $foodbakery_upload_dir . $ret_name . $foodbakery_img_sizes[$size] . $img_ext;
                } else {

                    $ret_name = $foodbakery_upload_dir . $ret_name . $img_ext;
                }
            }
        } else {

            if ($img_name != '') {



                $ret_name = '';
            }
        }

        // Set everything back to normal.

        remove_filter('upload_dir', 'foodbakery_user_images_custom_foodbakery');

        return $ret_name;
    }

}

/**

 * End Function how to Add images sizes and their URL's

 */
/**

 * Start Function how to Add get portfolio images  URL's

 */
if (!function_exists('foodbakery_get_portfolio_img_url')) {



    function foodbakery_get_portfolio_img_url($img_name = '', $size = 'foodbakery_media_5', $return_sizes = false) {

        $foodbakery_img_sizes = array(
            'foodbakery_media_5' => '-180x135',
        );

        if ($return_sizes == true) {

            return $foodbakery_img_sizes;
        }

        $foodbakery_upload_dir = wp_upload_dir();

        $foodbakery_upload_dir = isset($foodbakery_upload_dir['url']) ? $foodbakery_upload_dir['url'] . '/' : '';

        if (strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) !== false) {

            $img_ext = substr($img_name, ( strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']) + strlen($foodbakery_img_sizes['foodbakery_media_5'])), strlen($img_name));

            $ret_name = substr($img_name, 0, strpos($img_name, $foodbakery_img_sizes['foodbakery_media_5']));

            if (isset($foodbakery_img_sizes[$size])) {

                $ret_name = $foodbakery_upload_dir . $ret_name . $foodbakery_img_sizes[$size] . $img_ext;
            } else {

                $ret_name = $foodbakery_upload_dir . $ret_name . $img_ext;
            }
        } else {

            $ret_name = $foodbakery_upload_dir . $img_name;
        }

        return $ret_name;
    }

}

/**

 * End Function how to Add get portfolio images  URL's

 */
/**

 * Start Function how to Save  images  URL's

 */
if (!function_exists('foodbakery_save_img_url')) {

    function foodbakery_save_img_url($img_url = '') {

        if ($img_url != '') {

            $img_id = foodbakery_get_attachment_id_from_url($img_url);

            $img_url = wp_get_attachment_image_src($img_id, 'foodbakery_media_2');

            if (isset($img_url[0])) {

                $img_url = $img_url[0];

                if (strpos($img_url, 'uploads/') !== false) {

                    $img_url = substr($img_url, ( strpos($img_url, 'uploads/') + strlen('uploads/')), strlen($img_url));
                }
            }
        }

        return $img_url;
    }

}

/**

 * End Function how to Save  images  URL's

 */
/**

 * Start Function how to get attachment id from url

 */
if (!function_exists('foodbakery_get_attachment_id_from_url')) {



    function foodbakery_get_attachment_id_from_url($attachment_url = '') {

        global $wpdb;

        $attachment_id = false;

        // If there is no url, return.

        if ('' == $attachment_url)
            return;

        // Get the upload foodbakery paths

        $upload_dir_paths = wp_upload_dir();

        if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {

            // If this is the URL of an auto-generated thumbnail, get the URL of the original image

            $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);

            // Remove the upload path base foodbakery from the attachment URL

            $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);

            $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
        }

        return $attachment_id;
    }

}



/**

 * Start Function how to get attachment id from url

 */
if (!function_exists('foodbakery_get_attachment_id_from_filename')) {



    function foodbakery_get_attachment_id_from_filename($attachment_name = '') {

        global $wpdb;

        $attachment_id = false;

        // If there is no url, return.

        if ('' == $attachment_name)
            return;

        // Get the upload foodbakery paths

        $upload_dir_paths = wp_upload_dir();

        $attachment_id = $wpdb->get_results("SELECT * FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wposts.post_name like '%" . $attachment_name . "%' AND wposts.post_type = 'attachment'", OBJECT);

        return $attachment_id;
    }

}

/**

 * Start Function how to Remove Image URL's

 */
if (!function_exists('foodbakery_remove_img_url')) {



    function foodbakery_remove_img_url($img_url = '') {

        $foodbakery_upload_dir = wp_upload_dir();

        $foodbakery_upload_dir = isset($foodbakery_upload_dir['basedir']) ? $foodbakery_upload_dir['basedir'] . '/' : '';

        if ($img_url != '') {

            $foodbakery_img_sizes = foodbakery_get_img_url('', '', true);

            if (isset($foodbakery_img_sizes['foodbakery_media_2']) && strpos($img_url, $foodbakery_img_sizes['foodbakery_media_2']) !== false) {

                $img_ext = substr($img_url, ( strpos($img_url, $foodbakery_img_sizes['foodbakery_media_2']) + strlen($foodbakery_img_sizes['foodbakery_media_2'])), strlen($img_url));

                $img_name = substr($img_url, 0, strpos($img_url, $foodbakery_img_sizes['foodbakery_media_2']));

                if (is_file($foodbakery_upload_dir . $img_name . $img_ext)) {



                    unlink($foodbakery_upload_dir . $img_name . $img_ext);
                }

                if (is_array($foodbakery_img_sizes)) {

                    foreach ($foodbakery_img_sizes as $foodbakery_key => $foodbakery_size) {

                        if (is_file($foodbakery_upload_dir . $img_name . $foodbakery_size . $img_ext)) {



                            unlink($foodbakery_upload_dir . $img_name . $foodbakery_size . $img_ext);
                        }
                    }
                }
            } else {

                if (is_file($foodbakery_upload_dir . $img_url)) {



                    unlink($foodbakery_upload_dir . $img_url);
                }
            }
        }
    }

}

/**

 * End Function how to Remove Image URL's

 */
/**

 * Start Function how to Add Wishlist in Candidate

 */
if (!function_exists('candidate_header_wishlist')) {



    function candidate_header_wishlist($return = 'no') {

        global $post, $foodbakery_plugin_options;

        $top_wishlist_menu_html = '';

        $foodbakery_publisher_functions = new foodbakery_publisher_functions();

        $user = foodbakery_get_user_id();

        if (isset($user) && $user <> '') {

            $foodbakery_shortlist_array = get_user_meta($user, 'cs-user-jobs-wishlist', true);

            if (!empty($foodbakery_shortlist_array))
                $foodbakery_shortlist = array_column_by_two_dimensional($foodbakery_shortlist_array, 'post_id');
            else
                $foodbakery_shortlist = array();
        }

        if (!empty($foodbakery_shortlist) && count($foodbakery_shortlist) > 0) {

            $args = array('posts_per_page' => "-1", 'post__in' => $foodbakery_shortlist, 'post_type' => 'restaurants');

            $custom_query = new WP_Query($args);

            $wishlist_count = $custom_query->post_count;

            if ($custom_query->have_posts()):



                $top_wishlist_menu_html .= '<div class="wish-list" id="top-wishlist-content"><a><i class="icon-heart6"></i></a> <em class="cs-bgcolor" id="cs-fav-counts">' . absint($wishlist_count) . '</em>

                <div class="recruiter-widget wish-list-dropdown">

                    <ul class="recruiter-list">';

                $top_wishlist_menu_html .= '<li><span class="foodbakery_shortlisted_count">' . esc_html__("My Shortlisted Restaurants", 'foodbakery') . ' (<span id="cs-heading-counts">' . absint($wishlist_count) . '</span>)</span></li>';

                $wishlist_count = 1;

                while ($custom_query->have_posts()): $custom_query->the_post();

                    $foodbakery_jobs_thumb_url = '';

                    $employer_img = '';

                    // get employer images at run time

                    $foodbakery_job_employer = get_post_meta($post->ID, "foodbakery_job_username", true); //

                    $foodbakery_job_employer_data = foodbakery_get_postmeta_data('foodbakery_user', $foodbakery_job_employer, '=', 'employer', true);

                    $employer_img = get_the_author_meta('user_img', $foodbakery_job_employer);

                    if ($employer_img == '') {



                        $foodbakery_jobs_thumb_url = esc_url(wp_foodbakery::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                    } else {

                        $foodbakery_jobs_thumb_url = foodbakery_get_img_url($employer_img, 'foodbakery_media_5');
                    }

                    $top_wishlist_menu_html .= '<li class="alert alert-dismissible">

                                <a class="cs-remove-top-shortlist" id="cs-rem-' . esc_html($post->ID) . '" onclick="foodbakery_unset_user_job_fav(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_html($post->ID) . '\')"><span>&times;</span></a>';

                    if ($foodbakery_jobs_thumb_url != '') {

                        $top_wishlist_menu_html .= '<a href="' . esc_url(get_the_permalink($post->ID)) . '"><img src="' . esc_url($foodbakery_jobs_thumb_url) . '" alt="" /></a>';
                    }

                    $top_wishlist_menu_html .= '<div class="cs-info">

                                    <h6><a href="' . esc_url(get_the_permalink($post->ID)) . '">' . $post->post_title . '</a></h6>

                                    ' . esc_html__('Added ', 'foodbakery') . '<span>';

                    // getting added in wishlist date

                    $finded = in_multiarray($post->ID, $foodbakery_shortlist_array, 'post_id');

                    if ($finded != '')
                        if ($foodbakery_shortlist_array[$finded[0]]['date_time'] != '') {

                            $top_wishlist_menu_html .= date_i18n(get_option('date_format'), $foodbakery_shortlist_array[$finded[0]]['date_time']);
                        }

                    $top_wishlist_menu_html .= '</span>

                                </div>

                            </li>';

                    $wishlist_count++;

                    if ($wishlist_count > 5) {

                        break;
                    }

                endwhile;

                $foodbakery_page_id = isset($foodbakery_plugin_options['foodbakery_js_dashboard']) ? $foodbakery_plugin_options['foodbakery_js_dashboard'] : '';

                $top_wishlist_menu_html .= '<li class="alert alert-dismissible"><a href="' . esc_url(foodbakery_users_profile_link($foodbakery_page_id, 'shortlisted_jobs', $user)) . '" >' . esc_html__('View All', 'foodbakery') . '</a></li>

                    </ul>

                </div></div>';

                wp_reset_postdata();

            endif;
        }

        if ($return == 'no')
            echo force_balance_tags($top_wishlist_menu_html);
        else
            return $top_wishlist_menu_html;
    }

}

/**

 * End Function how to Add Wishlist in Candidate

 */
/**

 * Start Function how to Find Other Fields User Meta List

 */
if (!function_exists('foodbakery_find_other_field_user_meta_list')) {



    function foodbakery_find_other_field_user_meta_list($post_id, $post_column, $list_name, $need_find, $user_id) {

        $finded = foodbakery_find_index_user_meta_list($post_id, $list_name, $post_column, $user_id);

        $index = '';

        $need_find_value = '';

        if (isset($finded[0])) {
            $index = $finded[0];

            $existing_list_data = get_user_meta($user_id, $list_name, true);

            $need_find_value = $existing_list_data[$index][$need_find];
        }
        return $need_find_value;
    }

}

/**

 * End Function how to Find Other Fields User Meta List

 */
/**

 * Start Function how to find Index

 */
if (!function_exists('find_in_multiarray')) {



    function find_in_multiarray($elem, $array, $field) {


        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ($i = 0; $i <= $top; $i++) {
            if (isset($array[$i])) {
                $new_array[$k] = $array[$i];
                $k++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;

        $finded_index = '';

        if (is_array($array)) {

            while ($bottom <= $top) {

                if ($array[$bottom][$field] == $elem)
                    $finded_index[] = $bottom;

                else

                if (is_array($array[$bottom][$field]))
                    if (find_in_multiarray($elem, ($array[$bottom][$field])))
                        $finded_index[] = $bottom;

                $bottom++;
            }
        }

        return $finded_index;
    }

}

/**

 * Start Function how to Find Index User Meta List

 */
if (!function_exists('foodbakery_find_index_user_meta_list')) {



    function foodbakery_find_index_user_meta_list($post_id, $list_name, $need_find, $user_id) {

        $existing_list_data = get_user_meta($user_id, $list_name, true);

        $finded = find_in_multiarray($post_id, $existing_list_data, $need_find);

        return $finded;
    }

}

/**

 * End Function how to Find Index User Meta List

 */
/**

 * Start Function how to Remove List From User Meta List

 */
if (!function_exists('foodbakery_remove_from_user_meta_list')) {



    function foodbakery_remove_from_user_meta_list($post_id, $list_name, $user_id) {

        $existing_list_data = '';

        $existing_list_data = get_user_meta($user_id, $list_name, true);

        $finded = in_multiarray($post_id, $existing_list_data, 'post_id');

        $existing_list_data = remove_index_from_array($existing_list_data, $finded);

        update_user_meta($user_id, $list_name, $existing_list_data);
    }

}

/**

 * End Function how to Remove List From User Meta List

 */
/**

 * Start Function how to Create  User Meta List

 */
if (!function_exists('foodbakery_create_user_meta_list')) {



    function foodbakery_create_user_meta_list($post_id, $list_name, $user_id) {
        $current_timestamp = strtotime(date('d-m-Y H:i:s'));
        $existing_list_data = '';

        $existing_list_data = get_user_meta($user_id, $list_name, true);

        // search duplicat and remove it then arrange new ordering

        $finded = in_multiarray($post_id, $existing_list_data, 'post_id');

        $existing_list_data = remove_index_from_array($existing_list_data, $finded);

        // adding one more entry

        $existing_list_data[] = array('post_id' => $post_id, 'date_time' => $current_timestamp);

        update_user_meta($user_id, $list_name, $existing_list_data);
    }

}

/**

 * End Function how to Create  User Meta List

 */
/**

 * Start Function how to find Index

 */
if (!function_exists('in_multiarray')) {



    function in_multiarray($elem, $array, $field) {

        $top = sizeof($array) - 1;
        $bottom = 0;

        $finded_index = '';

        if (is_array($array)) {

            while ($bottom <= $top) {

                if ($array[$bottom][$field] == $elem)
                    $finded_index[] = $bottom;

                else

                if (is_array($array[$bottom][$field]))
                    if (in_multiarray($elem, ($array[$bottom][$field])))
                        $finded_index[] = $bottom;

                $bottom++;
            }
        }

        return $finded_index;
    }

}

/**

 * End Function how to find Index

 */
/**

 * Start Function how to remove given Indexes

 */
if (!function_exists('remove_index_from_array')) {



    function remove_index_from_array($array, $index_array) {

        $top = sizeof($index_array) - 1;

        $bottom = 0;

        if (is_array($index_array)) {

            while ($bottom <= $top) {

                unset($array[$index_array[$bottom]]);

                $bottom++;
            }
        }

        if (!empty($array))
            return array_values($array);
        else
            return $array;
    }

}

/**

 * End Function how to remove given Indexes

 */
/**

 * Start Function how to get only one Index from two dimenssion array

 */
if (!function_exists("array_column_by_two_dimensional")) {



    function array_column_by_two_dimensional($array, $column_name) {

        if (isset($array) && is_array($array)) {

            return array_map(function ($element) use ($column_name) {

                return $element[$column_name];
            }, $array);
        }
    }

}

/**

 * End Function how to get only one Index from two dimenssion array

 */
/**

 * Start Function how prevent guest not access admin panel

 */
if (!function_exists('redirect_user')) {

    add_action('admin_init', 'redirect_user');

    function redirect_user() {

        $user = wp_get_current_user();

        if ((!defined('DOING_AJAX') || !DOING_AJAX ) && ( empty($user) || in_array("foodbakery_publisher", (array) $user->roles) || in_array("foodbakery_candidate", (array) $user->roles))) {

            wp_safe_redirect(home_url());

            exit;
        }
    }

}

/**

 * End Function how prevent guest not access admin panel

 */
/**

 * Start Function how to get login user information

 */
if (!function_exists('getlogin_user_info')) {



    function getlogin_user_info() {

        global $current_user;

        $foodbakery_emp_funs = new foodbakery_publisher_functions();

        if (is_user_logged_in()) {

            if ($foodbakery_emp_funs->is_employer()) {   // for employer
                $login_user_args = array(
                    'posts_per_page' => "1",
                    'post_type' => 'employer',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'foodbakery_user',
                            'value' => $current_user->ID,
                            'compare' => '=',
                        ),
                    ),
                );

                $login_user_query = new WP_Query($login_user_args);

                $user_info = '';

                if ($login_user_query->have_posts()):

                    while ($login_user_query->have_posts()) : $login_user_query->the_post();

                        global $post;

                        $login_employer_post = $post;

                        $user_info['post_id'] = $login_employer_post->ID;

                        $user_info['name'] = get_post_meta($login_employer_post->ID, 'foodbakery_first_name', true) . " " . get_post_meta($login_employer_post->ID, 'foodbakery_last_name', true);

                        $user_info['email'] = get_post_meta($login_employer_post->ID, 'foodbakery_email', true);

                        $user_info['phone'] = get_post_meta($login_employer_post->ID, 'foodbakery_phone_number', true);

                        $user_info['user_type'] = 'employer';

                    endwhile;

                    wp_reset_postdata();

                endif;
            } else {

                $login_user_args = array(
                    'posts_per_page' => "1",
                    'post_type' => 'candidate',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'foodbakery_user',
                            'value' => $current_user->ID,
                            'compare' => '=',
                        ),
                    ),
                );

                $login_user_query = new WP_Query($login_user_args);

                $user_info = '';

                if ($login_user_query->have_posts()):

                    while ($login_user_query->have_posts()) : $login_user_query->the_post();

                        global $post;

                        $login_candidate_post = $post;

                        $user_info['post_id'] = $login_candidate_post->ID;

                        $user_info['name'] = get_post_meta($login_candidate_post->ID, 'foodbakery_first_name', true) . " " . get_post_meta($login_candidate_post->ID, 'foodbakery_last_name', true);

                        $user_info['email'] = get_post_meta($login_candidate_post->ID, 'foodbakery_email', true);

                        $user_info['phone'] = get_post_meta($login_candidate_post->ID, 'foodbakery_phone_number', true);

                        $user_info['user_type'] = 'candidate';

                    endwhile;

                    wp_reset_postdata();

                endif;
            }
        }

        return $user_info;
    }

}

/**

 * End Function how to get login user information

 */
/**

 * Start Function how to get Job Detail

 */
if (!function_exists('get_job_detail')) {



    function get_job_detail($job_id) {

        $post = get_post($job_id);

        return $post;
    }

}

/**

 * End Function how to get Job Detail

 */
/**

 * Start Function how to Check Candidate Applications

 */
if (!function_exists('check_candidate_applications')) {



    function check_candidate_applications($candidate_meta_id) {

        global $current_user;

        $result_count = 0;

        $foodbakery_emp_funs = new foodbakery_publisher_functions();

        if (is_user_logged_in() && $foodbakery_emp_funs->is_employer()) {

            $employer_id = $current_user->ID;   // employer id

            $foodbakery_jobapplied_array = get_user_meta($candidate_meta_id, 'cs-user-jobs-applied-list', true);

            if (!empty($foodbakery_jobapplied_array))
                $foodbakery_jobapplied = array_column_by_two_dimensional($foodbakery_jobapplied_array, 'post_id');
            else
                $foodbakery_jobapplied = array();



            if (is_array($foodbakery_jobapplied) && sizeof($foodbakery_jobapplied) > 0) {

                $args = array('posts_per_page' => "-1", 'post__in' => $foodbakery_jobapplied, 'post_type' => 'restaurants', 'order' => "ASC", 'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'foodbakery_job_expired',
                            'value' => strtotime(date('d-m-Y')),
                            'compare' => '>=',
                            'type' => 'numeric',
                        ),
                        array(
                            'key' => 'foodbakery_job_username',
                            'value' => $employer_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'foodbakery_job_status',
                            'value' => 'delete',
                            'compare' => '!=',
                        ),
                    ),
                );

                $custom_query = new WP_Query($args);

                $result_count = $custom_query->post_count;
            }
        }

        return $result_count;
    }

}

/**

 * End Function how to Check Candidate Applications

 */
/**

 *

 * @get specialism headings

 *

 */
if (!function_exists('get_specialism_headings')) {



    function get_specialism_headings($specialisms) {

        $return_str = '';

        if (count($specialisms) > 0) {

            if (isset($specialisms[0]))
                $specialisms_str = $specialisms[0];

            if (strpos($specialisms_str, ',') !== FALSE) {

                $specialisms = explode(",", $specialisms_str);
            }

            $i = 1;

            foreach ($specialisms as $single_specialism_title) {

                $selected_spec_data = get_term_by('slug', $single_specialism_title, 'specialisms');

                if (isset($selected_spec_data))
                    $return_str .= isset($selected_spec_data->name) ? ($selected_spec_data->name) : '';

                if ($i != count($specialisms))
                    $return_str .= ", ";
                else
                    $return_str .= " ";

                $i++;
            }
        }

        $return_str .= esc_html__("Job(s)", "foodbakery");

        return $return_str;
    }

}

/**

 * Start Function how to get using servers and servers protocols

 */
if (!function_exists('foodbakery_server_protocol')) {

    function foodbakery_server_protocol() {

        if (is_ssl()) {
            return 'https://';
        }

        return 'http://';
    }

}

/**
 * End Function how to get using servers and servers protocols
 */
if (!function_exists('getMultipleParameters')) {

    function getMultipleParameters($query_string = '') {

        if ($query_string == '')
            $query_string = $_SERVER['QUERY_STRING'];

        $params = explode('&', $query_string);
        foreach ($params as $param) {

            $k = $param;
            $v = '';

            if (strpos($param, '=')) {

                list($name, $value) = explode('=', $param);

                $k = rawurldecode($name);

                $v = rawurldecode($value);
            }

            if (isset($query[$k])) {

                if (is_array($query[$k])) {

                    $query[$k][] = $v;
                } else {

                    $query[$k][] = array($query[$k], $v);
                }
            } else {

                $query[$k][] = $v;
            }
        }

        return $query;
    }

}

/**

 * End Function how to get using servers and servers protocols

 */
/**

 * Start Function how to arrang jobs in shorlist

 */
if (!function_exists('foodbakery_job_shortlist_load')) {



    function foodbakery_job_shortlist_load() {

        candidate_header_wishlist();

        die();
    }

    add_action("wp_ajax_foodbakery_job_shortlist_load", "foodbakery_job_shortlist_load");

    add_action("wp_ajax_nopriv_foodbakery_job_shortlist_load", "foodbakery_job_shortlist_load");
}

/**

 * end Function how to arrang jobs in shorlist

 */
/**

 * Start Function how to Set Geo Location

 */
if (!function_exists('foodbakery_set_geo_loc')) {



    function foodbakery_set_geo_loc() {

        $foodbakery_geo_loc = isset($_POST['geo_loc']) ? $_POST['geo_loc'] : '';

        if (isset($_COOKIE['foodbakery_geo_loc'])) {

            unset($_COOKIE['foodbakery_geo_loc']);

            setcookie('foodbakery_geo_loc', null, -1, '/');
        }

        if (isset($_COOKIE['foodbakery_geo_switch'])) {

            unset($_COOKIE['foodbakery_geo_switch']);

            setcookie('foodbakery_geo_switch', null, -1, '/');
        }

        setcookie('foodbakery_geo_loc', $foodbakery_geo_loc, time() + 86400, '/');

        setcookie('foodbakery_geo_switch', 'on', time() + 86400, '/');
    }

    add_action("wp_ajax_foodbakery_set_geo_loc", "foodbakery_set_geo_loc");

    add_action("wp_ajax_nopriv_foodbakery_set_geo_loc", "foodbakery_set_geo_loc");
}

/**

 * End Function how to Set Geo Location

 */
/**

 * Start Function how to UnSet Geo Location

 */
if (!function_exists('foodbakery_unset_geo_loc')) {



    function foodbakery_unset_geo_loc() {

        if (isset($_COOKIE['foodbakery_geo_loc'])) {

            unset($_COOKIE['foodbakery_geo_loc']);

            setcookie('foodbakery_geo_loc', null, -1, '/');
        }

        if (isset($_COOKIE['foodbakery_geo_switch'])) {

            unset($_COOKIE['foodbakery_geo_switch']);

            setcookie('foodbakery_geo_switch', null, -1, '/');
        }

        setcookie('foodbakery_geo_loc', '', time() + 86400, '/');

        setcookie('foodbakery_geo_switch', 'off', time() + 86400, '/');

        die;
    }

    add_action("wp_ajax_foodbakery_unset_geo_loc", "foodbakery_unset_geo_loc");

    add_action("wp_ajax_nopriv_foodbakery_unset_geo_loc", "foodbakery_unset_geo_loc");
}

/**

 *

 * @set sort filter

 *

 */
if (!function_exists('foodbakery_set_sort_filter')) {

    function foodbakery_set_sort_filter() {
        $json = array();
        if (session_id() == '') {
            session_start();
        }
        $field_name = $_REQUEST['field_name'];
        $field_name_value = $_REQUEST['field_name_value'];
        $_SESSION[$field_name] = $field_name_value;
        $json['type'] = esc_html__('success', 'foodbakery');
        echo json_encode($json);
        die();
    }

    add_action("wp_ajax_foodbakery_set_sort_filter", "foodbakery_set_sort_filter");

    add_action("wp_ajax_nopriv_foodbakery_set_sort_filter", "foodbakery_set_sort_filter");
}

/**

 * Start Function how to check if Image Exists

 */
if (!function_exists('foodbakery_image_exist')) {



    function foodbakery_image_exist($sFilePath) {



        $img_formats = array("png", "jpg", "jpeg", "gif", "tiff"); //Etc. . . 

        $path_info = pathinfo($sFilePath);

        if (isset($path_info['extension']) && in_array(strtolower($path_info['extension']), $img_formats)) {

            if (!filter_var($sFilePath, FILTER_VALIDATE_URL) === false) {

                $foodbakery_file_response = wp_remote_get($sFilePath);

                if (is_array($foodbakery_file_response) && isset($foodbakery_file_response['headers']['content-type']) && strpos($foodbakery_file_response['headers']['content-type'], 'image') !== false) {

                    return true;
                }
            }
        }

        return false;
    }

}

/**

 *

 * @get query whereclase by array

 *

 */
if (!function_exists('foodbakery_get_query_whereclase_by_array')) {



    function foodbakery_get_query_whereclase_by_array($array, $user_meta = false) {

        $id = '';

        $flag_id = 0;

        if (isset($array) && is_array($array)) {

            foreach ($array as $var => $val) {
                if (!empty($val)) {
                    $string = ' ';

                    $string .= ' AND (';

                    if (isset($val['key']) || isset($val['value'])) {

                        $string .= get_meta_condition($val);
                    } else {  // if inner array 
                        if (isset($val) && is_array($val)) {

                            foreach ($val as $inner_var => $inner_val) {

                                $inner_relation = isset($inner_val['relation']) ? $inner_val['relation'] : 'and';

                                $second_string = '';

                                if (isset($inner_val) && is_array($inner_val)) {

                                    $string .= "( ";

                                    $inner_arr_count = is_array($inner_val) ? count($inner_val) : '';

                                    $inner_flag = 1;

                                    foreach ($inner_val as $inner_val_var => $inner_val_value) {

                                        if (is_array($inner_val_value)) {

                                            $string .= "( ";

                                            $string .= get_meta_condition($inner_val_value);

                                            $string .= ' )';

                                            if ($inner_flag != $inner_arr_count)
                                                $string .= ' ' . $inner_relation . ' ';
                                        }

                                        $inner_flag++;
                                    }

                                    $string .= ' )';
                                }
                            }
                        }
                    }

                    $string .= " ) ";

                    $id_condtion = '';

                    if (isset($id) && $flag_id != 0) {

                        $id = implode(",", $id);

                        if (empty($id)) {

                            $id = 0;
                        }

                        if ($user_meta == true) {

                            $id_condtion = ' AND user_id IN (' . $id . ')';
                        } else {

                            $id_condtion = ' AND post_id IN (' . $id . ')';
                        }
                    }

                    if ($user_meta == true) {

                        $id = foodbakery_get_user_id_by_whereclase($string . $id_condtion);
                    } else {

                        $id = foodbakery_get_post_id_by_whereclase($string . $id_condtion);
                    }

                    $flag_id = 1;
                }
            }
        }

        return $id;
    }

}

/**

 * Start Function how to get Meta using Conditions

 */
if (!function_exists('get_meta_condition')) {



    function get_meta_condition($val) {

        $string = '';

        $meta_key = isset($val['key']) ? $val['key'] : '';

        $compare = isset($val['compare']) ? $val['compare'] : '=';

        $meta_value = isset($val['value']) ? $val['value'] : '';

        $string .= " meta_key='" . $meta_key . "' AND ";

        $type = isset($val['type']) ? $val['type'] : '';

        if ($compare == 'BETWEEN' || $compare == 'between' || $compare == 'Between') {

            $meta_val1 = '';

            $meta_val2 = '';

            if (isset($meta_value) && is_array($meta_value)) {

                $meta_val1 = isset($meta_value[0]) ? $meta_value[0] : '';

                $meta_val2 = isset($meta_value[1]) ? $meta_value[1] : '';
            }

            if ($type != '' && strtolower($type) == 'numeric') {

                $string .= " meta_value BETWEEN '" . $meta_val1 . "' AND " . $meta_val2 . " ";
            } else {

                $string .= " meta_value BETWEEN '" . $meta_val1 . "' AND '" . $meta_val2 . "' ";
            }
        } elseif ($compare == 'like' || $compare == 'LIKE' || $compare == 'Like') {

            $string .= " meta_value LIKE '%" . $meta_value . "%' ";
        } else {

            if ($type != '' && strtolower($type) == 'numeric' && $meta_value != '') {

                $string .= " meta_value" . $compare . " " . $meta_value . " ";
            } else {

                $string .= " meta_value" . $compare . "'" . $meta_value . "' ";
            }
        }

        return $string;
    }

}

/**

 * end Function how to get Meta using Conditions

 */
/**

 * Start Function how to get post id using whereclase Query

 */
if (!function_exists('foodbakery_get_post_id_by_whereclase')) {



    function foodbakery_get_post_id_by_whereclase($whereclase) {

        global $wpdb;

        $qry = "SELECT post_id FROM $wpdb->postmeta WHERE 1=1 " . $whereclase;
        $posts = $wpdb->get_col($qry);
        return $posts;
    }

}



if (!function_exists('foodbakery_get_user_id_by_whereclase')) {



    function foodbakery_get_user_id_by_whereclase($whereclase) {

        global $wpdb;
        $qry = "SELECT user_id FROM $wpdb->usermeta WHERE 1=1 " . $whereclase;

        return $posts = $wpdb->get_col($qry);
    }

}



/**

 * end Function how to get post id using whereclase Query

 */
/**

 * Start Function how to get post id using whereclase Query

 */
if (!function_exists('foodbakery_get_post_id_whereclause_post')) {



    function foodbakery_get_post_id_whereclause_post($whereclase) {

        global $wpdb;

        $qry = "SELECT ID FROM $wpdb->posts WHERE 1=1 " . $whereclase;

        return $posts = $wpdb->get_col($qry);
    }

}

/**

 * End Function how to get post id using whereclase Query

 */
/**

 *

 * @array_flatten

 *

 */
if (!function_exists('array_flatten')) {



    function array_flatten($array) {

        $return = array();

        foreach ($array as $key => $value) {

            if (is_array($value)) {

                $return = array_merge($return, array_flatten($value));
            } else {

                $return[$key] = $value;
            }
        }

        return $return;
    }

}

/**

 * Start Function how to remove Dupplicate variable value

 */
if (!function_exists('remove_dupplicate_var_val')) {



    function remove_dupplicate_var_val($qry_str) {

        $old_string = $qry_str;

        $qStr = str_replace("?", "", $qry_str);

        $query = explode('&', $qStr);

        $params = array();

        if (isset($query) && !empty($query)) {

            foreach ($query as $param) {

                if (!empty($param)) {

                    $param_array = explode('=', $param);

                    $name = isset($param_array[0]) ? $param_array[0] : '';

                    $value = isset($param_array[1]) ? $param_array[1] : '';

                    $new_str = $name . "=" . $value;

                    // count matches

                    $count_str = substr_count($old_string, $new_str);

                    $count_str = $count_str - 1;

                    if ($count_str > 0) {

                        $old_string = foodbakery_str_replace_limit($new_str, "", $old_string, $count_str);
                    }

                    $old_string = str_replace("&&", "&", $old_string);
                }
            }
        }

        $old_string = str_replace("?&", "?", $old_string);

        return $old_string;
    }

}

/**

 *

 * @str replace limit

 *

 */
if (!function_exists('foodbakery_str_replace_limit')) {



    function foodbakery_str_replace_limit($search, $replace, $string, $limit = 1) {

        if (is_bool($pos = (strpos($string, $search))))
            return $string;

        $search_len = strlen($search);

        for ($i = 0; $i < $limit; $i++) {

            $string = substr_replace($string, $replace, $pos, $search_len);

            if (is_bool($pos = (strpos($string, $search))))
                break;
        }

        return $string;
    }

}

/**

 * Start Function how to allow the user for adding special characters

 */
if (!function_exists('foodbakery_allow_special_char')) {



    function foodbakery_allow_special_char($input = '') {

        $output = $input;

        return $output;
    }

}

/**

 * End Function how to allow the user for adding special characters

 */
if (!function_exists('foodbakery_plugin_image_sizes')) {

    function foodbakery_plugin_image_sizes() {



        /* Thumb size On Candidate ,Candidate , Restaurant 2, Employer Detail,Related Restaurants */

        add_image_size('foodbakery_media_6', 150, 113, true);

        add_image_size('foodbakery_media_7', 120, 90, true);
        add_image_size('foodbakery_media_8', 359, 212, true);
    }

}

/**

 *

 * @site header login plugin

 *

 */
if (!function_exists('foodbakery_site_header_login_plugin')) {


    function foodbakery_site_header_login_plugin($items, $args) {

        global $foodbakery_plugin_options;

        if (isset($foodbakery_plugin_options['foodbakery_user_dashboard_switchs']) && $foodbakery_plugin_options['foodbakery_user_dashboard_switchs'] == 'on') {

            if ($args->theme_location == 'primary') {

                echo do_shortcode('[foodbakery_user_login register_role="contributor"] [/foodbakery_user_login]');
            }
        }



        return $items;
    }

}

/**

 * Start Function how to share the posts

 */
if (!function_exists('foodbakery_social_share')) {



    function foodbakery_social_share($echo = true) {

        global $foodbakery_plugin_options;

        $foodbakery_plugin_options = get_option('foodbakery_plugin_options');

        $twitter = '';

        $facebook = '';

        $google_plus = '';

        $tumblr = '';

        $dribbble = '';

        $instagram = '';

        $share = '';

        $stumbleupon = '';

        $youtube = '';

        $pinterst = '';

        if (isset($foodbakery_plugin_options['foodbakery_twitter_share'])) {

            $twitter = $foodbakery_plugin_options['foodbakery_twitter_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_facebook_share'])) {

            $facebook = $foodbakery_plugin_options['foodbakery_facebook_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_google_plus_share'])) {

            $google_plus = $foodbakery_plugin_options['foodbakery_google_plus_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_tumblr_share'])) {

            $tumblr = $foodbakery_plugin_options['foodbakery_tumblr_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_dribbble_share'])) {

            $dribbble = $foodbakery_plugin_options['foodbakery_dribbble_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_instagram_share'])) {

            $instagram = $foodbakery_plugin_options['foodbakery_instagram_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_share_share'])) {

            $share = $foodbakery_plugin_options['foodbakery_share_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_stumbleupon_share'])) {

            $stumbleupon = $foodbakery_plugin_options['foodbakery_stumbleupon_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_youtube_share'])) {

            $youtube = $foodbakery_plugin_options['foodbakery_youtube_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_pintrest_share'])) {

            $pinterst = $foodbakery_plugin_options['foodbakery_pintrest_share'];
        }

        foodbakery_addthis_script_init_method();

        $html = '';

        if ($twitter == 'on' or $facebook == 'on' or $google_plus == 'on' or $pinterst == 'on' or $tumblr == 'on' or $dribbble == 'on' or $instagram == 'on' or $share == 'on' or $stumbleupon == 'on' or $youtube == 'on') {

            if (isset($facebook) && $facebook == 'on') {

                $html .= '<li><a class="addthis_button_facebook" data-original-title="Facebook"><i class="icon-facebook2"></i></a></li>';
            }

            if (isset($twitter) && $twitter == 'on') {

                $html .= '<li><a class="addthis_button_twitter" data-original-title="twitter"><i class="icon-twitter2"></i></a></li>';
            }

            if (isset($google_plus) && $google_plus == 'on') {

                $html .= '<li><a class="addthis_button_google" data-original-title="google-plus"><i class="icon-googleplus7"></i></a></li>';
            }

            if (isset($tumblr) && $tumblr == 'on') {

                $html .= '<li><a class="addthis_button_tumblr" data-original-title="Tumblr"><i class="icon-tumblr5"></i></a></li>';
            }

            if (isset($dribbble) && $dribbble == 'on') {

                $html .= '<li><a class="addthis_button_dribbble" data-original-title="Dribbble"><i class="icon-dribbble7"></i></a></li>';
            }

            if (isset($instagram) && $instagram == 'on') {

                $html .= '<li><a class="addthis_button_instagram" data-original-title="Instagram"><i class="icon-instagram4"></i></a></li>';
            }

            if (isset($stumbleupon) && $stumbleupon == 'on') {

                $html .= '<li><a class="addthis_button_stumbleupon" data-original-title="stumbleupon"><i class="icon-stumbleupon4"></i></a></li>';
            }

            if (isset($youtube) && $youtube == 'on') {

                $html .= '<li><a class="addthis_button_youtube" data-original-title="Youtube"><i class="icon-youtube"></i></a></li>';
            }

            if (isset($pinterst) && $pinterst == 'on') {

                $html .= '<li><a class="addthis_button_youtube" data-original-title="Youtube"><i class="icon-pinterest"></i></a></li>';
            }

            if (isset($share) && $share == 'on') {

                $html .= '<li><a class="cs-more addthis_button_compact at300m"></a></li>';
            }

            $html .= '</ul>';
        }
        if ($echo) {
            echo balanceTags($html, true);
        } else {
            return balanceTags($html, true);
        }
    }

}







/**

 * Start Function how to share the posts

 */
if (!function_exists('foodbakery_social_more')) {



    function foodbakery_social_more() {

        global $foodbakery_plugin_options;

        $foodbakery_plugin_options = get_option('foodbakery_plugin_options');

        $twitter = '';

        $facebook = '';

        $google_plus = '';

        $tumblr = '';

        $dribbble = '';

        $instagram = '';

        $share = '';

        $stumbleupon = '';

        $youtube = '';

        $pinterst = '';

        if (isset($foodbakery_plugin_options['foodbakery_twitter_share'])) {

            $twitter = $foodbakery_plugin_options['foodbakery_twitter_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_facebook_share'])) {

            $facebook = $foodbakery_plugin_options['foodbakery_facebook_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_google_plus_share'])) {

            $google_plus = $foodbakery_plugin_options['foodbakery_google_plus_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_tumblr_share'])) {

            $tumblr = $foodbakery_plugin_options['foodbakery_tumblr_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_dribbble_share'])) {

            $dribbble = $foodbakery_plugin_options['foodbakery_dribbble_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_instagram_share'])) {

            $instagram = $foodbakery_plugin_options['foodbakery_instagram_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_share_share'])) {

            $share = $foodbakery_plugin_options['foodbakery_share_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_stumbleupon_share'])) {

            $stumbleupon = $foodbakery_plugin_options['foodbakery_stumbleupon_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_youtube_share'])) {

            $youtube = $foodbakery_plugin_options['foodbakery_youtube_share'];
        }

        if (isset($foodbakery_plugin_options['foodbakery_pintrest_share'])) {

            $pinterst = $foodbakery_plugin_options['foodbakery_pintrest_share'];
        }

        foodbakery_addthis_script_init_method();

        $html = '';

        if (isset($share) && $share == 'on') {

            $html .= '<a class="addthis_button_compact share-btn">' . esc_html__('Share Job', 'foodbakery') . '</a>';
        }





        echo balanceTags($html, true);
    }

}

/**

 * End Function how to share the posts

 */
/**

 * Start Function how to add tool tip text

 */
if (!function_exists('foodbakery_tooltip_helptext')) {



    function foodbakery_tooltip_helptext($popover_text = '', $return_html = true) {

        $popover_link = '';

        if (isset($popover_text) && $popover_text != '') {

            $popover_link = '<br><em><strong>' . $popover_text . '</strong></em>';
        }

        if ($return_html == true) {

            return $popover_link;
        } else {

            echo force_balance_tags($popover_link);
        }
    }

}

/*

 *  End tool tip text asaign function

 */



/**

 * Start Function how to add tool tip text without icon only tooltip string

 */
if (!function_exists('foodbakery_tooltip_helptext_string')) {



    function foodbakery_tooltip_helptext_string($popover_text = '', $return_html = true, $class = '') {

        $popover_link = '';

        if (isset($popover_text) && $popover_text != '') {

            $popover_link = '<br><em><strong>' . $popover_text . '</strong></em>';
        }

        if ($return_html == true) {

            return $popover_link;
        } else {

            echo force_balance_tags($popover_link);
        }
    }

}

/*

 *  End tool tip text asaign function

 */





// Fontawsome icon box for Theme Options

if (!function_exists('foodbakery_iconlist_plugin_options')) {



    function foodbakery_iconlist_plugin_options($icon_value = '', $id = '', $name = '', $class = '') {

        global $foodbakery_form_fields;

        ob_start();
        ?>

        <script>



            jQuery(document).ready(function ($) {

                var this_icons;
                var rand_num = '<?php echo foodbakery_allow_special_char($id); ?>';
                var e9_element = $('#e9_element_' + rand_num).fontIconPicker({
                    theme: 'fip-bootstrap'
                });
                icons_load_call.always(function () {
                    this_icons = loaded_icons;
                    // Get the class prefix
                    var classPrefix = this_icons.preferences.fontPref.prefix,
                            icomoon_json_icons = [],
                            icomoon_json_search = [];
                    $.each(this_icons.icons, function (i, v) {
                        icomoon_json_icons.push(classPrefix + v.properties.name);
                        if (v.icon && v.icon.tags && v.icon.tags.length) {
                            icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
                        } else {
                            icomoon_json_search.push(v.properties.name);
                        }
                    });
                    // Set new fonts on fontIconPicker
                    e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                    // Show success message and disable
                    $('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-success').text('Successfully loaded icons').prop('disabled', true);
                })
                        .fail(function () {
                            // Show error message and enable
                            $('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-danger').text('Error: Try Again?').prop('disabled', false);
                        });

            });
        </script>

        <?php
        $foodbakery_opt_array = array(
            'id' => '',
            'std' => foodbakery_allow_special_char($icon_value),
            'cust_id' => "e9_element_" . foodbakery_allow_special_char($id),
            'cust_name' => foodbakery_allow_special_char($name) . "[]",
            'classes' => ( isset($class) ) ? $class : '',
            'extra_atr' => '',
        );

        $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
        ?>

        <span id="e9_buttons_<?php echo foodbakery_allow_special_char($id); ?>" style="display:none">

            <button autocomplete="off" type="button" class="btn btn-primary"><?php esc_html_e('Load from IcoMoon selection.json', 'foodbakery') ?></button>

        </span>

        <?php
        $fontawesome = ob_get_clean();

        return $fontawesome;
    }

}

/*

 * start information messages

 */

if (!function_exists('foodbakery_info_messages_restaurant')) {



    function foodbakery_info_messages_restaurant($message = 'There is no record in list', $return = true, $classes = '', $before = '', $after = '') {

        global $post;

        $output = '';

        $class_str = '';

        if ($classes != '') {

            $class_str .= ' class="' . $classes . '"';
        }

        $before_str = '';

        if ($before != '') {

            $before_str .= $before;
        }

        $after_str = '';

        if ($after != '') {

            $after_str .= $after;
        }

        $output .= $before_str;

        $output .= '<span' . $class_str . '>';

        $output .= $message;

        $output .= '</span>';

        $output .= $after_str;

        if ($return == true) {

            return force_balance_tags($output);
        } else {

            echo force_balance_tags($output);
        }
    }

}

/*

 * end information messages

 */



/* define it global */

$umlaut_chars['in'] = array(chr(196), chr(228), chr(214), chr(246), chr(220), chr(252), chr(223));

$umlaut_chars['ecto'] = array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß');

$umlaut_chars['html'] = array('&Auml;', '&auml;', '&Ouml;', '&ouml;', '&Uuml;', '&uuml;', '&szlig;');

$umlaut_chars['feed'] = array('&#196;', '&#228;', '&#214;', '&#246;', '&#220;', '&#252;', '&#223;');

$umlaut_chars['utf8'] = array(utf8_encode('Ä'), utf8_encode('ä'), utf8_encode('Ö'), utf8_encode('ö'), utf8_encode('Ü'), utf8_encode('ü'), utf8_encode('ß'));

$umlaut_chars['perma'] = array('Ae', 'ae', 'Oe', 'oe', 'Ue', 'ue', 'ss');

/* sanitizes the titles to get qualified german permalinks with  correct transliteration */

function de_DE_umlaut_permalinks($title) {

    global $umlaut_chars;

    if (seems_utf8($title)) {

        $invalid_latin_chars = array(chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe', chr(197) . chr(160) => 'S', chr(197) . chr(189) => 'Z', chr(197) . chr(161) => 's', chr(197) . chr(190) => 'z', chr(226) . chr(130) . chr(172) => 'E');

        $title = utf8_decode(strtr($title, $invalid_latin_chars));
    }


    if (isset($umlaut_chars['ecto']) && isset($umlaut_chars['perma'])) {
        $title = str_replace($umlaut_chars['ecto'], $umlaut_chars['perma'], $title);
    }

    if (isset($umlaut_chars['in']) && isset($umlaut_chars['perma'])) {
        $title = str_replace($umlaut_chars['in'], $umlaut_chars['perma'], $title);
    }

    if (isset($umlaut_chars['html']) && isset($umlaut_chars['perma'])) {
        $title = str_replace($umlaut_chars['html'], $umlaut_chars['perma'], $title);
    }

    $title = sanitize_title_with_dashes($title);

    return $title;
}

add_filter('sanitize_title', 'de_DE_umlaut_permalinks');

if (!function_exists('wp_new_user_notification')) :

    function wp_new_user_notification($user_id, $plaintext_pass = ' ') {

        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        if (empty($plaintext_pass)) {
            return;
        }

        do_action('foodbakery_new_user_notification_site_owner', $user_login, $user_email);
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        wp_set_password($random_password, $user_id);

        $reg_user = get_user_by('ID', $user_id);
        do_action('foodbakery_publisher_register', $reg_user, $random_password);
    }

endif;

if (!function_exists('foodbakery_get_loginuser_role')) :

    function foodbakery_get_loginuser_role() {

        global $current_user;

        $foodbakery_user_role = '';

        if (is_user_logged_in()) {

            wp_get_current_user();

            $user_roles = isset($current_user->roles) ? $current_user->roles : '';

            $foodbakery_user_role = 'other';

            if (($user_roles != '' && in_array("foodbakery_publisher", $user_roles))) {

                $foodbakery_user_role = 'foodbakery_publisher';
            } elseif (($user_roles != '' && in_array("foodbakery_candidate", $user_roles))) {

                $foodbakery_user_role = 'foodbakery_candidate';
            }
        }

        return $foodbakery_user_role;
    }

endif;

//change author/username base to users/userID

function change_author_permalinks() {

    global $wp_rewrite, $foodbakery_plugin_options;

    $author_slug = isset($foodbakery_plugin_options['foodbakery_author_page_slug']) ? $foodbakery_plugin_options['foodbakery_author_page_slug'] : 'user';

    // Change the value of the author permalink base to whatever you want here

    $wp_rewrite->author_base = $author_slug;
    $wp_rewrite->flush_rules();
}

add_action('init', 'change_author_permalinks');

add_filter('query_vars', 'users_query_vars');

function users_query_vars($vars) {

    global $foodbakery_plugin_options;

    // add lid to the valid list of variables
    $author_slug = isset($foodbakery_plugin_options['foodbakery_author_page_slug']) ? $foodbakery_plugin_options['foodbakery_author_page_slug'] : 'user';

    $new_vars = array($author_slug);

    $vars = $new_vars + $vars;

    return $vars;
}

function user_rewrite_rules($wp_rewrite) {

    global $foodbakery_plugin_options;
    $author_slug = isset($foodbakery_plugin_options['foodbakery_author_page_slug']) ? $foodbakery_plugin_options['foodbakery_author_page_slug'] : 'user';

    $newrules = array();
    $new_rules[$author_slug . '/(\d*)$'] = 'index.php?author=$matches[1]';
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

add_filter('generate_rewrite_rules', 'user_rewrite_rules');

function location_query_vars($query_vars) {
    $query_vars['location'] = 'location';

    return $query_vars;
}

add_filter('query_vars', 'location_query_vars');

function custom_rewrite_rule() {

    add_rewrite_rule('(employer-simple)/(.+)$', 'index.php?pagename=employer-simple&location=$matches[2]', 'top');
}

add_action('init', 'custom_rewrite_rule', 10, 0);

/*

 * Bootstrap Coloumn Class

 */

if (!function_exists('foodbakery_custom_column_class')) {



    function foodbakery_custom_column_class($column_size) {

        $coloumn_class = '';

        if (isset($column_size) && $column_size <> '') {

            list($top, $bottom) = explode('/', $column_size);

            $width = $top / $bottom * 100;

            $width = (int) $width;

            $coloumn_class = '';

            if (round($width) == '25' || round($width) < 25) {

                $coloumn_class = 'col-md-3';
            } elseif (round($width) == '33' || (round($width) < 33 && round($width) > 25)) {

                $coloumn_class = 'col-md-4';
            } elseif (round($width) == '50' || (round($width) < 50 && round($width) > 33)) {

                $coloumn_class = 'col-md-6';
            } elseif (round($width) == '67' || (round($width) < 67 && round($width) > 50)) {

                $coloumn_class = 'col-md-8';
            } elseif (round($width) == '75' || (round($width) < 75 && round($width) > 67)) {

                $coloumn_class = 'col-md-9';
            } elseif (round($width) == '100') {

                $coloumn_class = 'col-md-12';
            } else {

                $coloumn_class = '';
            }
        }

        return sanitize_html_class($coloumn_class);
    }

}

/*
 * TinyMCE EDITOR "Biographical Info" USER PROFILE
 * */
if (!function_exists('foodbakery_biographical_info_tinymce')) {

    function foodbakery_biographical_info_tinymce() {
        if (basename($_SERVER['PHP_SELF']) == 'profile.php' || basename($_SERVER['PHP_SELF']) == 'user-edit.php' && function_exists('wp_tiny_mce')) {
            wp_admin_css();
            wp_enqueue_script('utils');
            wp_enqueue_script('editor');
            do_action('admin_print_scripts');
            do_action("admin_print_styles-post-php");
            do_action('admin_print_styles');
            remove_all_filters('mce_external_plugins');

            add_filter('teeny_mce_before_init', function ($a) {

                $a["skin"] = "wp_theme";
                $a["height"] = "200";
                $a["width"] = "240";
                $a["onpageload"] = "";
                $a["mode"] = "exact";
                $a["elements"] = "description";
                $a["theme_advanced_buttons1"] = "formatselect, forecolor, bold, italic, pastetext, pasteword, bullist, numlist, link, unlink, outdent, indent, charmap, removeformat, spellchecker, fullscreen, wp_adv";
                $a["theme_advanced_buttons2"] = "underline, justifyleft, justifycenter, justifyright, justifyfull, forecolor, pastetext, undo, redo, charmap, wp_help";
                $a["theme_advanced_blockformats"] = "p,h2,h3,h4,h5,h6";
                $a["theme_advanced_disable"] = "strikethrough";
                return $a;
            }
            );

            //wp_editor(true);
        }
    }

    add_action('admin_head', 'foodbakery_biographical_info_tinymce');
}

function foodbakery_cred_limit_check($restaurant_id = '', $index = '', $print_all = false) {

    $restaurant_limits = get_post_meta($restaurant_id, 'foodbakery_trans_all_meta', true);
    if (is_array($restaurant_limits) && sizeof($restaurant_limits) > 0) {

        foreach ($restaurant_limits as $limit_key => $limit_val) {
            if (isset($limit_val['value']) && isset($limit_val['key']) && $limit_val['key'] == $index) {

                return $limit_val['value'];
            }
        }
    }
    if (empty($restaurant_limits)) {
        return 'on';
    }
    if ($print_all === true) {
        echo '<pre>';
        print_r($restaurant_limits);
        echo '<pre>';
    }
}

function foodbakery__encrypt($data) {

    $encrypt_data = base64_encode(htmlentities($data, ENT_COMPAT, 'ISO-8859-15'));

    return $encrypt_data;
}

function foodbakery__decrypt($data) {

    $decrypt_data = html_entity_decode(base64_decode($data), ENT_COMPAT, 'ISO-8859-15');

    return $decrypt_data;
}

function foodbakery_encode_url_string($stringArray) {
    $s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray), 9))), '+/=', '-_,');
    return $s;
}

function foodbakery_decode_url_string($stringArray) {
    $s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '-_,', '+/=')))));
    return $s;
}

if (!function_exists('foodbakery_restaurants_map_cords_to_url')) {

    function foodbakery_restaurants_map_cords_to_url() {
        $cords = isset($_POST['pathstr']) ? $_POST['pathstr'] : '';
        $restaurant_ins = isset($_POST['poly_in_restaurants']) ? $_POST['poly_in_restaurants'] : '';

        $final_array = array(
            'cords' => $cords,
            'ids' => $restaurant_ins,
        );

        $final_json = json_encode($final_array);

        $encode_string = foodbakery_encode_url_string($final_json);

        echo json_encode(array('string' => $encode_string));
        die;
    }

    add_action('wp_ajax_foodbakery_restaurants_map_cords_to_url', 'foodbakery_restaurants_map_cords_to_url');
    add_action('wp_ajax_nopriv_foodbakery_restaurants_map_cords_to_url', 'foodbakery_restaurants_map_cords_to_url');
}

if (!function_exists('array_column')) {

    function array_column($input = null, $columnKey = null, $indexKey = null) {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                    'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }

}

if (!function_exists("foodbakery_linkedin_attachment_metas")) {

    function foodbakery_linkedin_attachment_metas($contentln, $url) {
        $content_title = '';
        $content_desc = '';
        $utf = "UTF-8";
        $content_img = '';
        $aprv_me_data = wp_remote_get($url);
        if (is_array($aprv_me_data)) {
            $aprv_me_data = $aprv_me_data['body']; // use the content
        } else {
            $aprv_me_data = '';
        }

        $og_datas = new DOMDocument();
        @$og_datas->loadHTML($aprv_me_data);
        $xpath = new DOMXPath($og_datas);
        if (isset($contentln['content']['title'])) {
            $ogmetaContentAttributeNodes_tit = $xpath->query("/html/head/meta[@property='og:title']/@content");
            foreach ($ogmetaContentAttributeNodes_tit as $ogmetaContentAttributeNode_tit) {
                $content_title = $ogmetaContentAttributeNode_tit->nodeValue;
            }

            if ($content_title != '')
                $contentln['content']['title'] = $content_title;
        }
        if (isset($contentln['content']['description'])) {
            $ogmetaContentAttributeNodes_desc = $xpath->query("/html/head/meta[@property='og:description']/@content");
            foreach ($ogmetaContentAttributeNodes_desc as $ogmetaContentAttributeNode_desc) {
                $content_desc = $ogmetaContentAttributeNode_desc->nodeValue;
            }

            if ($content_desc != '')
                $contentln['content']['description'] = $content_desc;
        }

        if (isset($contentln['content']['submitted-url']))
            $contentln['content']['submitted-url'] = $url;

        return $contentln;
    }

}

if (!function_exists('foodbakery_restaurant_string_limit')) {

    function foodbakery_restaurant_string_limit($string, $limit) {

        $space = " ";
        $appendstr = " ...";
        if (mb_strlen($string) <= $limit)
            return $string;
        if (mb_strlen($appendstr) >= $limit)
            return '';
        $string = mb_substr($string, 0, $limit - mb_strlen($appendstr));
        $rpos = mb_strripos($string, $space);
        if ($rpos === false)
            return $string . $appendstr;
        else
            return mb_substr($string, 0, $rpos) . $appendstr;
    }

}

if (!function_exists("foodbakery_fbapp_attachment_metas")) {

    function foodbakery_fbapp_attachment_metas($attachment, $url) {
        $name = '';
        $description_li = '';
        $content_img = '';
        $utf = "UTF-8";
        $aprv_me_data = wp_remote_get($url);
        if (is_array($aprv_me_data)) {
            $aprv_me_data = $aprv_me_data['body']; // use the content
        } else {
            $aprv_me_data = '';
        }

        $og_datas = new DOMDocument();
        @$og_datas->loadHTML($aprv_me_data);
        $xpath = new DOMXPath($og_datas);
        if (isset($attachment['name'])) {
            $ogmetaContentAttributeNodes_tit = $xpath->query("/html/head/meta[@property='og:title']/@content");

            foreach ($ogmetaContentAttributeNodes_tit as $ogmetaContentAttributeNode_tit) {
                $name = $ogmetaContentAttributeNode_tit->nodeValue;
            }
            $name = utf8_decode($name);
            if ($name != '')
                $attachment['name'] = $name;
        }
        if (isset($attachment['actions'])) {
            if (isset($attachment['actions']['name'])) {
                $ogmetaContentAttributeNodes_tit = $xpath->query("/html/head/meta[@property='og:title']/@content");

                foreach ($ogmetaContentAttributeNodes_tit as $ogmetaContentAttributeNode_tit) {
                    $name = $ogmetaContentAttributeNode_tit->nodeValue;
                }
                $name = utf8_decode($name);
                if ($name != '')
                    $attachment['actions']['name'] = $name;
            }
            if (isset($attachment['actions']['link'])) {
                $attachment['actions']['link'] = $url;
            }
        }
        if (isset($attachment['description'])) {
            $ogmetaContentAttributeNodes_desc = $xpath->query("/html/head/meta[@property='og:description']/@content");
            foreach ($ogmetaContentAttributeNodes_desc as $ogmetaContentAttributeNode_desc) {
                $description_li = $ogmetaContentAttributeNode_desc->nodeValue;
            }
            if (get_option('xyz_smap_utf_decode_enable') == 1)
                $description_li = utf8_decode($description_li);
            if ($description_li != '')
                $attachment['description'] = $description_li;
        }

        if (isset($attachment['link']))
            $attachment['link'] = $url;

        return $attachment;
    }

}

/**
 * @count Banner Clicks
 *
 */
if (!function_exists('foodbakery_banner_click_count_plus')) {

    function foodbakery_banner_click_count_plus() {
        $code_id = isset($_POST['code_id']) ? $_POST['code_id'] : '';
        $banner_click_count = get_option("banner_clicks_" . $code_id);
        $banner_click_count = $banner_click_count <> '' ? $banner_click_count : 0;
        if (!isset($_COOKIE["banner_clicks_" . $code_id])) {
            setcookie("banner_clicks_" . $code_id, 'true', time() + 86400, '/');
            update_option("banner_clicks_" . $code_id, $banner_click_count + 1);
        }
        die(0);
    }

    add_action('wp_ajax_foodbakery_banner_click_count_plus', 'foodbakery_banner_click_count_plus');
    add_action('wp_ajax_nopriv_foodbakery_banner_click_count_plus', 'foodbakery_banner_click_count_plus');
}

if (!function_exists('cs_wpml_lang_url')) {

    function cs_wpml_lang_url() {

        if (function_exists('icl_object_id')) {

            global $sitepress;

            $cs_server_uri = $_SERVER['REQUEST_URI'];
            $cs_server_uri = explode('/', $cs_server_uri);

            $cs_active_langs = $sitepress->get_active_languages();

            if (is_array($cs_active_langs) && sizeof($cs_active_langs) > 0) {
                foreach ($cs_server_uri as $uri) {

                    if (array_key_exists($uri, $cs_active_langs)) {
                        return $uri;
                    }
                }
            }
        }
        return false;
    }

}

if (!function_exists('cs_wpml_parse_url')) {

    function cs_wpml_parse_url($lang = 'en', $url = '') {

        $cs_fir_url = home_url('/');
        if (strpos($cs_fir_url, '/' . $lang . '/') !== false) {
            
        }
        $cs_tail_url = substr($url, strlen($cs_fir_url), strlen($url));

        $cs_trans_url = $cs_fir_url . $lang . '/' . $cs_tail_url;

        return $cs_trans_url;
    }

}

add_filter('icl_ls_languages', 'wpml_ls_filter');

function custom_search_location_front() {
    global $foodbakery_plugin_options;
    $foodbakery_location_data = array();
    $foodbakery_location_data = json_decode(get_transient('foodbakery_location_data'));
    $foodbakery_search_result_page = isset($foodbakery_plugin_options['foodbakery_search_result_page']) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
    $redirecturl = isset($foodbakery_search_result_page) && $foodbakery_search_result_page != '' ? get_permalink($foodbakery_search_result_page) . '' : '';
    if (is_array($foodbakery_location_data) && count($foodbakery_location_data) > 0) {
        echo '<div class="field-holder"><input id="searchfiled" type="text" class="field-input" placeholder="Search" /></div>';

        echo '<ul class="list-group">';
        foreach ($foodbakery_location_data as $foodbakery_location_data_val) {
            echo '<li style="display:none"><a href="' . add_query_arg(array('location' => $foodbakery_location_data_val->value), $redirecturl) . '">' . $foodbakery_location_data_val->caption . '</a></li>';
        }
        echo '</ul>';
    }
}

if (!function_exists('wpml_ls_filter')) {

    function wpml_ls_filter($languages) {
        global $sitepress;
        if (strpos(basename($_SERVER['REQUEST_URI']), 'dashboard') !== false || strpos(basename($_SERVER['REQUEST_URI']), 'tab') !== false) {

            $cs_request_query = str_replace('?', '', basename($_SERVER['REQUEST_URI']));

            $cs_request_query = explode('&', $cs_request_query);

            $cs_request_quer = '';

            $query_count = 1;

            if (is_array($cs_request_query)) {
                foreach ($cs_request_query as $quer) {
                    if (strpos($quer, 'page_id') !== false || strpos($quer, 'lang') !== false) {
                        continue;
                    }
                    if ($query_count == 1) {
                        $cs_request_quer .= $quer;
                    } else {
                        $cs_request_quer .= '&' . $quer;
                    }
                    $query_count++;
                }
            }

            if (is_array($languages) && sizeof($languages) > 0) {
                foreach ($languages as $lang_code => $language) {
                    if (strpos($languages[$lang_code]['url'], '?') !== false) {
                        $languages[$lang_code]['url'] = $languages[$lang_code]['url'] . '&' . $cs_request_quer;
                    } else {
                        $languages[$lang_code]['url'] = $languages[$lang_code]['url'] . '?' . $cs_request_quer;
                    }
                }
            }
        }
        return $languages;
    }

}

if (!function_exists('restaurant_menu_price_calc')) {

    function restaurant_menu_price_calc($get_added_menus = '', $foodbakery_restaurant_id = '', $fee = false, $vat = false, $only_vat = false, $converter = false, $order_id = '', $currency_symbol = false) {
        global $foodbakery_plugin_options;

        $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
        $foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';
        $woocommerce_enabled = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';

        $menu_t_price = 0;

        if (!is_string($get_added_menus) && isset($get_added_menus[$foodbakery_restaurant_id]) && !empty($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id])) {

            foreach ($get_added_menus[$foodbakery_restaurant_id] as $menu_ord_item) {

                if (isset($menu_ord_item['menu_id']) && isset($menu_ord_item['price'])) {

                    $this_item_price = $menu_ord_item['price'];
                    $this_item_extras = isset($menu_ord_item['extras']) ? $menu_ord_item['extras'] : '';

                    //$menu_t_price += floatval($this_item_price);

                    if (is_array($this_item_extras) && sizeof($this_item_extras) > 0) {
                        foreach ($this_item_extras as $this_item_extra_at) {
                            $item_extra_at_price = isset($this_item_extra_at['price']) ? $this_item_extra_at['price'] : '';
                            $menu_t_price += floatval($item_extra_at_price);
                        }
                    }
                }
            }
        }
        //
        $woocommerce_fee_data = array();

        $restaurant_pickup_delivery = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);
        $foodbakery_delivery_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_delivery_fee', true);
        $foodbakery_pickup_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_pickup_fee', true);

        if (true === $fee) {
            $selected_fee_type = isset($get_added_menus[$foodbakery_restaurant_id . '_fee_type']) ? $get_added_menus[$foodbakery_restaurant_id . '_fee_type'] : '';

            /* if ($foodbakery_delivery_fee > 0 && $restaurant_pickup_delivery == 'delivery') {
              if ($woocommerce_enabled == 'on') {
              $woocommerce_fee_data['deliver']['label'] = esc_html__('Delivery', 'foodbakery');
              $woocommerce_fee_data['deliver']['value'] = floatval($foodbakery_delivery_fee);
              }
              $menu_t_price += floatval($foodbakery_delivery_fee);
              } else if ($foodbakery_pickup_fee > 0 && $restaurant_pickup_delivery == 'pickup' || $restaurant_pickup_delivery == 'delivery_and_pickup') {
              if ($woocommerce_enabled == 'on') {
              $woocommerce_fee_data['pickup']['label'] = esc_html__('Pickup', 'foodbakery');
              $woocommerce_fee_data['pickup']['value'] = floatval($foodbakery_pickup_fee);
              }
              $menu_t_price += floatval($foodbakery_pickup_fee);
              }else */

            if ($selected_fee_type == 'delivery' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                if ($woocommerce_enabled == 'on') {
                    $woocommerce_fee_data['deliver']['label'] = esc_html__('Delivery', 'foodbakery');
                    $woocommerce_fee_data['deliver']['value'] = floatval($foodbakery_delivery_fee);
                }
                $menu_t_price += floatval($foodbakery_delivery_fee);
            } else if ($selected_fee_type == 'pickup' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                if ($woocommerce_enabled == 'on') {
                    $woocommerce_fee_data['pickup']['label'] = esc_html__('Pickup', 'foodbakery');
                    $woocommerce_fee_data['pickup']['value'] = floatval($foodbakery_pickup_fee);
                }
                $menu_t_price += floatval($foodbakery_pickup_fee);
            } else {
                if ($foodbakery_delivery_fee > 0 && $restaurant_pickup_delivery == 'delivery') {
                    if ($woocommerce_enabled == 'on') {
                        $woocommerce_fee_data['deliver']['label'] = esc_html__('Delivery', 'foodbakery');
                        $woocommerce_fee_data['deliver']['value'] = floatval($foodbakery_delivery_fee);
                    }
                    $menu_t_price += floatval($foodbakery_delivery_fee);
                } else if ($foodbakery_pickup_fee > 0 && $restaurant_pickup_delivery == 'pickup' || $restaurant_pickup_delivery == 'delivery_and_pickup') {
                    if ($woocommerce_enabled == 'on') {
                        $woocommerce_fee_data['pickup']['label'] = esc_html__('Pickup', 'foodbakery');
                        $woocommerce_fee_data['pickup']['value'] = floatval($foodbakery_pickup_fee);
                    }
                    $menu_t_price += floatval($foodbakery_pickup_fee);
                }
            }
        }

        if ($get_added_menus == 'defined') {
            $menu_t_price = 0;
            $menu_t_price += floatval($foodbakery_restaurant_id);
        }

        if ($fee > 0 && true !== $fee) {
            $menu_t_price += floatval($fee);
        }


        if (true === $only_vat) {
            if ($foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                if ($menu_t_price > 0) {
                    $menu_t_price = ($menu_t_price / 100) * $foodbakery_payment_vat;
                }
            }
        } else {
            if (true === $vat && $foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                if ($menu_t_price > 0) {
                    $vat_price = ($menu_t_price / 100) * $foodbakery_payment_vat;
                    if ($woocommerce_enabled == 'on') {
                        $woocommerce_fee_data['vat']['label'] = esc_html__('VAT (' . $foodbakery_payment_vat . '%)', 'foodbakery');
                        $woocommerce_fee_data['vat']['value'] = $vat_price;
                    }
                    $menu_t_price += $vat_price;
                }
            }
        }

        update_post_meta($foodbakery_restaurant_id, 'woocommerce_fee_data', $woocommerce_fee_data);
        return foodbakery_get_currency($menu_t_price, $currency_symbol, '', '', $converter);
    }

}

function array_search_partial($arr, $keyword) {
    $response = array();
    foreach ($arr as $index => $string) {
        if (stripos($string, $keyword) !== FALSE)
            if (stripos($string, $keyword) == 0) {
                $response[] = $string;
            }
    }
    return $response;
}

add_action('wp_ajax_restaurant_detail_menu_search', 'restaurant_detail_menu_list');
add_action('wp_ajax_nopriv_restaurant_detail_menu_search', 'restaurant_detail_menu_list');

function restaurant_detail_menu_list($foodbakery_restaurant_id = '') {

    $html = '';

    if (isset($_POST['_menu_keyword']) && isset($_POST['_restaurant_id'])) {
        $foodbakery_restaurant_id = $_POST['_restaurant_id'];
    }

    $restaurant_menu_list = get_post_meta($foodbakery_restaurant_id, 'foodbakery_menu_items', true);
    $restaurant_menu_cat_desc = get_post_meta($foodbakery_restaurant_id, 'menu_cat_descs', true);
    $restaurant_menu_cat_title = get_post_meta($foodbakery_restaurant_id, 'menu_cat_titles', true);
    $total_items = (is_array($restaurant_menu_list) || is_object($restaurant_menu_list)) ? count($restaurant_menu_list) : 0;
    $total_menu = array();
    if (isset($restaurant_menu_list) && $restaurant_menu_list != '') {
        for ($menu_count = 0; $menu_count < $total_items; $menu_count++) {
            if (isset($restaurant_menu_list[$menu_count]['restaurant_menu'])) {
                $menu_exists = in_array($restaurant_menu_list[$menu_count]['restaurant_menu'], $total_menu);
                if (!$menu_exists) {
                    $total_menu[] = $restaurant_menu_list[$menu_count]['restaurant_menu'];
                }
            }
        }
    }
    $extras_modal_boxes = '';
    $total_menu_count = count($total_menu);

    $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
    $restaurants_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : '';
    $get_foodbakeri_nutri_icons = get_post_meta($restaurants_type_id, 'nutri_icon_imgs', true);
    $get_foodbakeri_nutri_titles = get_post_meta($restaurants_type_id, 'nutri_icon_titles', true);

    for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop++) {
        ob_start();
        for ($menu_items_loop = 0; $menu_items_loop < $total_items; $menu_items_loop++) {
            if (isset($restaurant_menu_list[$menu_items_loop]['restaurant_menu']) && $total_menu[$menu_loop] == $restaurant_menu_list[$menu_items_loop]['restaurant_menu'] && $restaurant_menu_list[$menu_items_loop]['menu_item_post_status'] != 'inherit') { //edit sagar 
                $menu_item_title = isset($restaurant_menu_list[$menu_items_loop]['menu_item_title']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_title'] : '';
                $menu_item_unique_id = isset($restaurant_menu_list[$menu_items_loop]['menu_item_counter']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_counter'] : '';
                $menu_item_description = isset($restaurant_menu_list[$menu_items_loop]['menu_item_description']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_description'] : '';
                $menu_item_icon = isset($restaurant_menu_list[$menu_items_loop]['menu_item_icon']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_icon'] : '';
                $menu_item_nutri = isset($restaurant_menu_list[$menu_items_loop]['menu_item_nutri']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_nutri'] : '';
                $menu_item_price = isset($restaurant_menu_list[$menu_items_loop]['menu_item_price']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_price'] : 0;
                $menu_item_extra = isset($restaurant_menu_list[$menu_items_loop]['menu_item_extra']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_extra'] : 0;

                if (isset($_POST['_menu_keyword']) && $_POST['_menu_keyword'] != '') {
                    $menu_keyword = $_POST['_menu_keyword'];

                    if (stripos(strtolower($menu_item_title), $menu_keyword) === false) {
                        continue;
                    }
                }
                $menu_item_icon_img_arr = wp_get_attachment_image_src($menu_item_icon, array(120, 90));
                $menu_item_icon_img_src = isset($menu_item_icon_img_arr[0]) ? $menu_item_icon_img_arr[0] : '';
                $menu_item_icon_img_arr_p = wp_get_attachment_image_src($menu_item_icon, 'large');
                $menu_item_icon_img_src_p = isset($menu_item_icon_img_arr_p[0]) ? $menu_item_icon_img_arr_p[0] : '';

                /* When import demo data images that are not availabe is replace with the no-image.png image */
                /* $image_src_array = explode('wp-content', $menu_item_icon_img_src);
                  $image_src_array_p = explode('wp-content', $menu_item_icon_img_src_p);

                  if(!file_exists('wp-content'.end($image_src_array))){
                  $menu_item_icon_img_src = content_url().'/plugins/wp-foodbakery/assets/frontend/images/No-image.png' ;
                  }
                  if(!file_exists('wp-content'.end($image_src_array_p))){
                  $menu_item_icon_img_src = content_url().'/plugins/wp-foodbakery/assets/frontend/images/No-image.png' ;
                  } */

                // print_r($restaurant_menu_list[$menu_items_loop]['menu_item_extra']);
                ?>

                <script> jQuery(document).ready(function ($) {

                        //$("a[rel^='prettyPhoto']").prettyPhoto();

                    });
                </script>
                <?php
                /// print_r($menu_item_extra);
                $js_function = '';
                if (isset($menu_item_extra[0]['title']) && is_array($menu_item_extra[0]['title']) && sizeof($menu_item_extra[0]['title']) > 0) {
                    $ajax_url = admin_url('admin-ajax.php');

                    $js_function = ' onclick="foodbakery_show_extra_menu_item(`extras-' . absint($menu_loop) . '-' . absint($menu_items_loop) . '`, `' . absint($menu_items_loop) . '`, `' . absint($menu_loop) . '`, `' . $ajax_url . '`, `' . $foodbakery_restaurant_id . '`)"';
                    ?>
                                                <!-- <a href="javascript:void(0);" class="dev-adding-menu-btn-<?php echo absint($menu_items_loop) ?>" onclick="foodbakery_show_extra_menu_item('extras-<?php echo absint($menu_loop) ?>-<?php echo absint($menu_items_loop) ?>', '<?php echo absint($menu_items_loop) ?>', '<?php echo absint($menu_loop) ?>', '<?php echo $ajax_url; ?>', '<?php echo $foodbakery_restaurant_id; ?>');"></a>
                    -->
                    <?php
                } else {
                    $js_function = 'class="restaurant-add-menu-btn restaurant-add-menu-btn-' . absint($menu_items_loop) . '" data-rid="' . absint($foodbakery_restaurant_id) . '" data-id="' . absint($menu_items_loop) . '" data-cid="' . absint($menu_loop) . '"';
                    ?>
                    <!-- <a href="javascript:void(0)" class="restaurant-add-menu-btn restaurant-add-menu-btn-<?php echo absint($menu_items_loop) ?>" data-rid="<?php echo absint($foodbakery_restaurant_id) ?>" data-id="<?php echo absint($menu_items_loop) ?>" data-cid="<?php echo absint($menu_loop) ?>"><i class="icon-plus4 text-color"></i></a>
                    -->
                    <?php
                }
                ?>

                <li <?php echo $js_function; ?>>






                    <div class="text-holder"  style="float:left;">

                        <h6><?php echo esc_html($menu_item_title); ?></h6>
                        <span><?php echo esc_html($menu_item_description); ?></span>
                        <?php
                        if (is_array($menu_item_nutri) && sizeof($menu_item_nutri) > 0) {
                            ?>
                            <ul class="nutri-icons">
                                <?php
                                $nutri_count = 0;
                                foreach ($menu_item_nutri as $men_nutri) {
                                    $menu_nutri_index = is_array($get_foodbakeri_nutri_icons) ? array_search($men_nutri, $get_foodbakeri_nutri_icons) : '';
                                    $menu_nutri_title = isset($get_foodbakeri_nutri_titles[$menu_nutri_index]) ? $get_foodbakeri_nutri_titles[$menu_nutri_index] : '';
                                    $men_nutri_icon_img_arr = wp_get_attachment_image_src($men_nutri, 'thumbnail');
                                    $men_nutri_icon_img_src = isset($men_nutri_icon_img_arr[0]) ? $men_nutri_icon_img_arr[0] : '';
                                    ?>
                                    <li><a data-toggle="tooltip" title="<?php echo esc_html($menu_nutri_title) ?>"><img src="<?php echo esc_url($men_nutri_icon_img_src); ?>" alt=""></a></li>
                                    <?php
                                    $nutri_count++;
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                        <span class="price">
                            <?php echo foodbakery_get_currency($menu_item_price, true); ?>
                        </span>
                    </div>
                    <!-- <div class="price-holder">
                    
                        <span id="add-menu-loader-<?php echo absint($menu_items_loop) ?>"></span>
                    </div> -->
                    <?php
                    if ($menu_item_icon_img_src != '') {
                        ?>

                        <div class="image-holder" style="float: right;"> <img style="border-radius: 10px;" src="<?php echo esc_url($menu_item_icon_img_src); ?>" alt=""></div>
                        <?php
                    }
                    ?>
                </li>
                <div id="show_extra_modal"></div>
                <?php
                //ob_start();
                //$extras_modal_boxes .= ob_get_clean();
                // extras
            }
        }
        $menu_it_html = ob_get_clean();
        ob_start();
        $menu_item_style = '';
        $menu_item_style = apply_filters('foodbakery_menu_item_style', $menu_item_style);
        if ($menu_it_html != '') {
            ?>
            <div class="fb-category-data">
                <div class="element-title fb-category-title" id="menu-category-<?php echo absint($menu_loop) ?>">
                    <h5 class="text-color"><?php echo esc_html($total_menu[$menu_loop]); ?> <?php do_action('foodbakery_menu_category_title_after'); ?></h5>
                    <span><?php echo isset($restaurant_menu_cat_desc[$menu_loop]) ? esc_html($restaurant_menu_cat_desc[$menu_loop]) : ''; ?></span>
                </div>
                <ul class="fb-menu-item-ul" style="<?php echo esc_html($menu_item_style); ?>">
                    <?php echo force_balance_tags($menu_it_html) ?>
                </ul>
            </div>
            <?php
        }
        $html .= ob_get_clean();
    }

    if (isset($_POST['_menu_keyword']) && isset($_POST['_restaurant_id'])) {
        echo json_encode(array('html' => $html));
        die;
    } else {
        return array('items' => $html, 'bs_boxes' => $extras_modal_boxes);
    }
}

function restaurant_get_image_height($img_url = '') {
    if ($img_url != '') {
        $foodbakery_upload_dir = wp_upload_dir();
        $foodbakery_upload_baseurl = isset($foodbakery_upload_dir['baseurl']) ? $foodbakery_upload_dir['baseurl'] . '/' : '';

        $foodbakery_upload_dir = isset($foodbakery_upload_dir['basedir']) ? $foodbakery_upload_dir['basedir'] . '/' : '';

        if (false !== strpos($img_url, $foodbakery_upload_baseurl)) {
            $foodbakery_upload_subdir_file = str_replace($foodbakery_upload_baseurl, '', $img_url);
        }

        $foodbakery_images_dir = wp_foodbakery::plugin_url() . 'assets/frontend/images/';

        $foodbakery_img_name = preg_replace('/^.+[\\\\\\/]/', '', $img_url);

        if (is_file($foodbakery_upload_dir . $foodbakery_img_name) || is_file($foodbakery_images_dir . $foodbakery_img_name)) {
            if (ini_get('allow_url_fopen')) {
                $foodbakery_var_header_image_height = getimagesize($img_url);
            }
        } else if (isset($foodbakery_upload_subdir_file) && is_file($foodbakery_upload_dir . $foodbakery_upload_subdir_file)) {
            if (ini_get('allow_url_fopen')) {
                $foodbakery_var_header_image_height = getimagesize($img_url);
            }
        } else {
            $foodbakery_var_header_image_height = '';
        }

        if (isset($foodbakery_var_header_image_height[1]) && $foodbakery_var_header_image_height[1] != '') {
            $foodbakery_var_header_image_height = $foodbakery_var_header_image_height[1] . 'px';
            $foodbakery_height_style = ' min-height: ' . foodbakery_allow_special_char($foodbakery_var_header_image_height) . ' !important;';
            return $foodbakery_height_style;
        }
    }
}

// Profile Deletion!
if (!function_exists('cs_remove_profile_callback')) {

    function cs_remove_profile_callback() {
        if (!wp_foodbakery::is_demo_user_modification_allowed($_POST['template_name'])) {
            $reponse['status'] = 'error';
            $reponse['message'] = esc_html__('Demo users are not allowed to modify information.', 'foodbakery');
            echo json_encode($reponse);
            wp_die();
        }
        $u_id = isset($_POST['u_id']) ? $_POST['u_id'] : '';
        if (isset($u_id) && !empty($u_id)) {
            wp_delete_user($u_id);
            $reponse['status'] = 'success';
            $reponse['message'] = esc_html__('Delete Successfully', 'foodbakery');
            $reponse['redirecturl'] = home_url();
            echo json_encode($reponse);
            wp_die();
        }
        $reponse['status'] = 'error';
        $reponse['message'] = esc_html__('Something went wrong', 'foodbakery');
        echo json_encode($reponse);
        wp_die();
    }

    add_action("wp_ajax_cs_remove_profile", "cs_remove_profile_callback");
    add_action("wp_ajax_nopriv_cs_remove_profile", "cs_remove_profile_callback");
}

//edit extra menu
if (!function_exists('foodbakery_edit_extra_menu_item')) {

    function foodbakery_edit_extra_menu_item() {
        global $current_user;
        ob_start();

        $pop_up_id = isset($_REQUEST['popup_id']) ? $_REQUEST['popup_id'] : '';

        $menu_index = isset($_REQUEST['menu_index']) ? $_REQUEST['menu_index'] : 0;

        $data_id = isset($_REQUEST['data_id']) ? $_REQUEST['data_id'] : '';
        $data_cat_id = isset($_REQUEST['data_cat_id']) ? $_REQUEST['data_cat_id'] : '';
        $data_rand = isset($_REQUEST['data_rand']) ? $_REQUEST['data_rand'] : '';
        $unique_id = isset($_REQUEST['unique_id']) ? $_REQUEST['unique_id'] : '';
        $extra_child_menu_id = isset($_REQUEST['extra_child_menu_id']) ? $_REQUEST['extra_child_menu_id'] : '';
        $foodbakery_restaurant_id = isset($_REQUEST['restuarant_id']) ? $_REQUEST['restuarant_id'] : '';
        $get_added_menus = array();
        $user_id = $current_user->ID;
        $publisher_id = foodbakery_company_id_form_user_id($user_id);
        $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

        if ($publisher_id != '' && $publisher_type != '' && $publisher_type != 'restaurant') {
            $get_added_menus = get_transient('add_menu_items_' . $publisher_id);
            if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp'])) {

                $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
            }
        } else {
            if (isset($_COOKIE['add_menu_items_temp'])) {
                $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
            }
        }

        $old_menu_ids = $get_added_menus;

        $added_extra_ids = array();
        $this_item_notes = '';
        foreach ($get_added_menus[$foodbakery_restaurant_id] as $menu_key => $menu_ord_item) {

            if ($get_added_menus[$foodbakery_restaurant_id][$menu_key]['unique_id'] == $unique_id) {
                $this_menu_cat_id = isset($menu_ord_item['menu_cat_id']) ? $menu_ord_item['menu_cat_id'] : '';

                $this_item_id = $menu_ord_item['menu_id'];
                $this_item_ids = $menu_ord_item['menu_id'];
                $this_item_price = $menu_ord_item['price'];

                $this_item_extras = isset($menu_ord_item['extras']) ? $menu_ord_item['extras'] : '';

                $this_item_notes = isset($menu_ord_item['notes']) ? $menu_ord_item['notes'] : '';

                foreach ($this_item_extras as $this_item_extra_at) {
                    $item_extra_at_title_id = isset($this_item_extra_at['title_id']) ? $this_item_extra_at['title_id'] : '';
                    $item_extra_at_menu_item_id = isset($this_item_extra_at['menu_item_id']) ? $this_item_extra_at['menu_item_id'] : '';
                    $added_extra_ids[] = 'extra-' . $item_extra_at_menu_item_id . '-' . $item_extra_at_title_id . '-' . $this_item_id;
                }
            }
        }




        $restaurant_menu_list = get_post_meta($foodbakery_restaurant_id, 'foodbakery_menu_items', true);

        $total_items = count($restaurant_menu_list);
        $total_menu = array();
        if (isset($restaurant_menu_list) && $restaurant_menu_list != '') {
            for ($menu_count = 0; $menu_count < $total_items; $menu_count++) {
                if (isset($restaurant_menu_list[$menu_count]['restaurant_menu'])) {
                    $menu_exists = in_array($restaurant_menu_list[$menu_count]['restaurant_menu'], $total_menu);
                    if (!$menu_exists) {
                        $total_menu[] = $restaurant_menu_list[$menu_count]['restaurant_menu'];
                    }
                }
            }
        }

        $total_menu_count = ( is_object($this_item_ids) || is_array($this_item_ids) ) ? count($this_item_ids) : 1;

        for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop++) {
            $menu_item_extra = isset($restaurant_menu_list[$data_id]['menu_item_extra']) ? $restaurant_menu_list[$data_id]['menu_item_extra'] : 0;

            if (isset($menu_item_extra[0]['title']) && is_array($menu_item_extra[0]['title']) && sizeof($menu_item_extra[0]['title']) > 0) {
                ?>
                <div class="modal fade menu-extras-modal" id="<?php echo $pop_up_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h2><a><?php esc_html_e('Edit Extras', 'foodbakery') ?></a></h2>
                            </div>
                            <div class="modal-body">
                                <div class="menu-selection-container">
                                    <?php
                                    // print_r($old_menu_ids );
                                    $menu_extra_counter = 0;

                                    // print_r($restaurant_menu_list);
                                    foreach ($menu_item_extra['heading'] as $key => $value) {
                                        $type_value = $menu_item_extra['type'][$key];
                                        $required_num_value = isset($menu_item_extra['required'][$key]) ? $menu_item_extra['required'][$key] : 0;
                                        $menu_item_extra_titles = isset($menu_item_extra[$key]['title']) ? $menu_item_extra[$key]['title'] : array();
                                        $menu_item_extra_prices = isset($menu_item_extra[$key]['price']) ? $menu_item_extra[$key]['price'] : array();

                                        $menu_item_extra_notes = isset($menu_item_extra[$key]['notes']) ? $menu_item_extra[$key]['notes'] : '';
                                        if (is_array($menu_item_extra_titles) && sizeof($menu_item_extra_titles) > 0) {
                                            $menu_extra_att_counter = 0;
                                            ?>
                                            <div class="dw extras-detail-main" id="menu_idd_<?php echo $menu_extra_counter; ?>">
                                                <input type="hidden" name="required_count" value="<?php echo $required_num_value; ?>">
                                                <h3 style="height:20px;"><?php echo esc_html($value); ?>

                                                    <?php if ($required_num_value != '') { ?>
                                                        <span style="float: right;" class="required_extras"><?php echo esc_html__('Required ', 'foodbakery') . $required_num_value; ?></span>
                                                    <?php } ?>
                                                </h3>
                                                <div class="extras-detail-options">
                                                    <?php
                                                    foreach ($menu_item_extra_titles as $key => $menu_item_extra_title) {
                                                        $menu_item_extra_price = isset($menu_item_extra_prices[$key]) ? $menu_item_extra_prices[$key] : 0;
                                                        $all_id = 'extra-' . absint($menu_extra_att_counter) . '-' . absint($menu_extra_counter) . '-' . absint($this_item_id);
                                                        $checked = '';
                                                        if (isset($added_extra_ids) && in_array($all_id, $added_extra_ids)) {
                                                            $checked = 'checked';
                                                        }
                                                        $field_type = '';
                                                        if ($type_value == 'single') {
                                                            $field_type = 'radio';
                                                        } else {
                                                            $field_type = 'checkbox';
                                                        }
                                                        $menu_items_loop = isset($menu_items_loop) ? $menu_items_loop : '';
                                                        ?>
                                                        <div class="extras-detail-att <?php echo $all_id; ?>">
                                                            <input price="<?php echo $menu_item_extra_price; ?>" class="sa_extra_checkbox" type="<?php echo $field_type; ?>" <?php echo $checked; ?> id="extra-<?php echo absint($menu_extra_att_counter) ?>-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>" data-ind="<?php echo absint($menu_extra_att_counter) ?>" data-menucat-id="<?php echo absint($menu_loop) ?>" data-menu-id="<?php echo absint($this_item_id) ?>" name="extra-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($this_item_id) ?>">
                                                            <label for="extra-<?php echo absint($menu_extra_att_counter) ?>-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>">
                                                                <span class="extra-title"><?php echo esc_html($menu_item_extra_title) ?></span>
                                                                <span class="extra-price"><?php echo foodbakery_get_currency($menu_item_extra_price, true); ?></span>
                                                            </label>
                                                        </div>
                                                        <?php
                                                        $menu_extra_att_counter++;
                                                    }
                                                    ?>
                                                </div>
                                                <div class="extras-detail-selected"></div>
                                            </div>
                                            <?php
                                            $menu_extra_counter++;
                                        }
                                    }
                                    ?>
                                    <div class="extras-detail-att">


                                        <input type="name" class="form-control" value=" <?php echo $this_item_notes; ?>" name="extrasnotes"/>

                                                                                                <!-- <textarea   rows="7"  wrap="virtual" style="height:auto;white-space: normal"  name="extras-notes-0" >
                                        <?php // echo $this_item_notes; ?>
                                                                                                </textarea> -->



                                    </div>
                                    <div class="extras-btns-holder" style="margin-top:10px">
                                        <button data-menucat-id-new="<?php echo absint($data_cat_id) ?>"  data-menucat-id="<?php echo absint($menu_loop) ?>" data-menu-id="<?php echo absint($data_id) ?>" data-rid="<?php echo absint($foodbakery_restaurant_id) ?>" data-rand="<?php echo $data_rand; ?>" unique_id="<?php echo $unique_id; ?>" class="add-extra-menu-btn input-button-loader editing-menu"><?php esc_html_e('Update', 'foodbakery') ?></button>
                                        <a href="javascript:void(0)" class="reset-menu-fields btn"><?php esc_html_e('Reset Fields', 'foodbakery') ?></a>
                                        <span class="extra-loader"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        $contents = ob_get_clean();
        //ob_end_clean();
        echo json_encode(array('status' => 'success', 'html' => $contents));
        wp_die();
    }

    add_action("wp_ajax_foodbakery_edit_extra_menu_item", "foodbakery_edit_extra_menu_item");
    add_action("wp_ajax_nopriv_foodbakery_edit_extra_menu_item", "foodbakery_edit_extra_menu_item");
}
//Show extra menu popup
if (!function_exists('foodbakery_show_extra_menu_item')) {

    function foodbakery_show_extra_menu_item() {
        global $current_user;
        $pop_up_id = $_REQUEST['popup_id'];
        $data_id = $_REQUEST['data_id'];
        $data_cat_id = $_REQUEST['data_cat_id'];
        $data_rand = isset($_REQUEST['data_rand']) ? $_REQUEST['data_rand'] : '';
        $unique_id = isset($_REQUEST['unique_id']) ? $_REQUEST['unique_id'] : '';
        $foodbakery_restaurant_id = $_POST['restaurant_id'];
        $restaurant_menu_list = get_post_meta($foodbakery_restaurant_id, 'foodbakery_menu_items', true);
        ob_start();
        $menu_items_loop = $data_id;
        $menu_item_extra = isset($restaurant_menu_list[$menu_items_loop]['menu_item_extra']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_extra'] : 0;

        $sa_total = 0;
        if (isset($menu_item_extra[0]['title']) && is_array($menu_item_extra[0]['title']) && sizeof($menu_item_extra[0]['title']) > 0) {

            $sa_men_nutri_icon_img_arr = wp_get_attachment_image_src($restaurant_menu_list[$menu_items_loop]['menu_item_icon'], 'large');
            $sa_men_nutri_icon_img_src = isset($sa_men_nutri_icon_img_arr[0]) ? $sa_men_nutri_icon_img_arr[0] : '';
            ?>
            <div class="modal fade menu-extras-modal add_extrass sa_model" id="<?php echo $pop_up_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="padding:0px">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 0;margin: 6px;"><span aria-hidden="true">&times;</span></button>
                            <img style="width:100%" src="<?php echo $sa_men_nutri_icon_img_src; ?>" />


                            <?php
                            /// print_r($restaurant_menu_list[$menu_items_loop]);


                            ob_start();

                            $menu_extra_counter = 0;
                            foreach ($menu_item_extra['heading'] as $key => $value) {
                                $type_value = isset($menu_item_extra['type'][$key]) ? $menu_item_extra['type'][$key] : '';
                                $required_num_value = isset($menu_item_extra['required'][$key]) ? $menu_item_extra['required'][$key] : 0;
                                $menu_item_extra_titles = isset($menu_item_extra[$key]['title']) ? $menu_item_extra[$key]['title'] : array();
                                $menu_item_extra_prices = isset($menu_item_extra[$key]['price']) ? $menu_item_extra[$key]['price'] : array();
                                $menu_item_extra_precheck = isset($menu_item_extra[$key]['precheck']) ? $menu_item_extra[$key]['precheck'] : array();
                                $menu_item_extra_quantity = isset($menu_item_extra[$key]['quantity']) ? $menu_item_extra[$key]['quantity'] : array();
                                if (is_array($menu_item_extra_titles) && sizeof($menu_item_extra_titles) > 0) {
                                    $menu_extra_att_counter = 0;
                                    ?>
                                    <div class="up extras-detail-main" id="menu_idd_<?php echo $menu_extra_counter; ?>">
                                        <input type="hidden" name="required_count" value="<?php echo $required_num_value; ?>">
                                        <?php do_action('foodbakery_extra_fields_hidden', $menu_item_extra, $key); ?>
                                        <h3 style="height:20px;"><?php echo esc_html($value); ?>
                                            <?php if ($required_num_value != '') { ?>
                                                <span style="float: right;" class="required_extras"><?php echo esc_html__('Required ', 'foodbakery') . $required_num_value; ?></span>
                                            <?php } ?> </h3>
                                        <div class="extras-detail-options">
                                            <?php
                                            foreach ($menu_item_extra_titles as $key => $menu_item_extra_title) {
                                                $menu_item_extra_price = isset($menu_item_extra_prices[$key]) ? $menu_item_extra_prices[$key] : 0;
                                                $menu_item_extra_prechecked = isset($menu_item_extra_precheck[$key]) ? $menu_item_extra_precheck[$key] : '';
                                                $menu_item_extra_quantitys = isset($menu_item_extra_quantity[$key]) ? $menu_item_extra_quantity[$key] : '';
                                                $field_type = '';
                                                $checked = '';
                                                $disabled = '';

                                                $disablestyle = '';
                                                $defult_qty = 1;

                                                if ($menu_item_extra_quantitys == '0') { //edit sagar
                                                    $disabled = 'disabled';
                                                    $disablestyle = 'style="text-decoration-line: line-through; "';
                                                    //$sa_total -= $menu_item_extra_price;
                                                }

                                                if ($menu_item_extra_prechecked == 'on' && $menu_item_extra_quantitys > 0) { //edit sagar
                                                    $checked = 'checked';

                                                    $sa_total += $menu_item_extra_price;
                                                    $defult_qty = 1;
                                                }

                                                if ($menu_item_extra_price == '') {
                                                    $menu_item_extra_price = 0;
                                                }

                                                if ($type_value == 'single') {
                                                    $field_type = 'radio';
                                                } else {
                                                    $field_type = 'checkbox';
                                                }
                                                ?>
                                                <div class="extras-detail-att">
                                                    <div class="row">


                                                        <div class="col-lg-12 col-xs-12">
                                                            <input <?php echo $disabled ?>   
                                                                price="<?php echo $menu_item_extra_price; ?>" 
                                                                class="sa_extra_checkbox" <?php echo $checked; ?> 
                                                                title="<?php echo esc_html($menu_item_extra_title); ?>"
                                                                type="<?php echo $field_type; ?>" 
                                                                max_qty="<?php echo $menu_item_extra_quantitys; ?>" 
                                                                id="extra-<?php echo absint($menu_extra_att_counter) ?>-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>" 
                                                                data-ind="<?php echo absint($menu_extra_att_counter) ?>" data-menucat-id="<?php echo isset($menu_loop) ? absint($menu_loop) : '' ?>" 
                                                                data-menu-id="<?php echo absint($menu_items_loop) ?>" 
                                                                name="extra-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>">
                                                            <label for="extra-<?php echo absint($menu_extra_att_counter) ?>-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>">
                                                                <span class="extra-title" <?php echo $disablestyle; ?>><?php echo esc_html($menu_item_extra_title) ?>  </span>
                                                                <span class="extra-price" <?php echo $disablestyle; ?>><?php echo foodbakery_get_currency($menu_item_extra_price, true); ?> </span>
                                                            </label>

                                                        </div>
                                                       


                                                    </div>

                                                </div>
                                                <?php
                                                $menu_extra_att_counter++;
                                            }
                                            ?>




                                        </div>
                                        <div class="extras-detail-selected"></div>
                                    </div>
                                    <?php
                                    $menu_extra_counter++;
                                }
                            }


                            $sa_output = ob_get_contents();
                            ob_end_clean();
                            ?>


                            <h2 style="padding: 15px 0px 10px 22px;"><a><?php echo $restaurant_menu_list[$menu_items_loop]['menu_item_title']; ?> x <span class="total_quantity">1</span></a> </h2>
                            <p style="width: 100%;clear: both;padding: 0px 0px 7px 22px;margin-bottom: 0px;font-size: 14px;font-weight: 100;color: #9D9D9D !important;line-height: 1.4;"><?php echo $restaurant_menu_list[$menu_items_loop]['menu_item_description']; ?></p>
                            <h4 style="margin-left:20px;margin-bottom: 8px;float: right;padding-right: 25px;" class="total_count" total_count="<?php echo number_format($sa_total, 2); ?>">  <?php echo number_format($sa_total, 2); ?> €</h4>
                        </div>
                        <div class="modal-body">
                            <div class="menu-selection-container">

                                <?php echo $sa_output; ?>



                                <div class="extras-detail-att">



                                    <input type="name" class="form-control" placeholder="Notes" value=""  name="extrasnotes"/>

                                                                        <!-- <textarea rows="5"  cols="7"  style="height:100px" class="foodbakery-dev-req-field" name="extras-notes-0" placeholder="Notes"></textarea> -->

                                                                                                        <!-- <input id="extra_notes" type="text" placeholder="Notes" class="form control" name="extras-notes-<?php echo $key; ?>" />                   -->

                                </div>

                                <div class="extras-btns-holder"  style="margin-top:10px">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <a href="javascript:void(0)" class="reset-menu-fields btn"><?php esc_html_e('Reset Fields', 'foodbakery') ?></a>
                                          
                                        </div>

                                        <div class="col-lg-3">
                                            <div class=" sa_quantity">
                                                <input   max_qty="<?php echo $menu_item_extra_quantitys; ?>"   type="hidden" class="sa_quantity_in" name="sa_quantity-<?php echo absint($menu_extra_att_counter) ?>-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>" value="<?php echo $defult_qty; ?>" />
                                                <button class=" col-4 sa_decrement"  >-</button>
                                                <span class="col-4 sa_quantity"><?php echo $defult_qty; ?></span>


                                                <button class=" col-4 sa_increment"  >+</button>

                                            </div>
                                        </div>

                                        <div class="col-lg-5">
                                          
                                                 <button data-menucat-id="<?php echo absint($data_cat_id) ?>" data-unique-menu-id="<?php echo isset($menu_item_unique_id) ? absint($menu_item_unique_id) : '' ?>" data-menu-id="<?php echo absint($menu_items_loop) ?>" data-rid="<?php echo absint($foodbakery_restaurant_id) ?>" class="add-extra-menu-btn input-button-loader "><?php esc_html_e('Add to Menu', 'foodbakery') ?></button>

                                             
                                            
                                            </div>
                                        </div>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        $contents = ob_get_clean();
        //ob_end_clean();
        echo json_encode(array('status' => 'success', 'html' => $contents));
        wp_die();
    }

    add_action("wp_ajax_foodbakery_show_extra_menu_item", "foodbakery_show_extra_menu_item");
    add_action("wp_ajax_nopriv_foodbakery_show_extra_menu_item", "foodbakery_show_extra_menu_item");
}

function tags_balnce_func($return = '') {

    return force_balance_tags($return, true);
}

/**
 * @param $amount
 * @param $currency_symbol
 * @return string
 */
function currency_symbol_possitions($amount, $currency_symbol) {
    global $foodbakery_plugin_options;
    $currency_alignment = isset($foodbakery_plugin_options['foodbakery_currency_alignment']) ? $foodbakery_plugin_options['foodbakery_currency_alignment'] : 'Left';
    if ($currency_alignment == 'Right') {
        $amount_with_currency_symbol = $amount . ' ' . $currency_symbol;
    } else {
        $amount_with_currency_symbol = $currency_symbol . $amount;
    }
    return $amount_with_currency_symbol;
}

/**
 * @param $amount_html
 * @param $currency_symbol_html
 * @return string
 */
function currency_symbol_possitions_html($amount_html, $currency_symbol_html) {
    global $foodbakery_plugin_options;
    $currency_alignment = isset($foodbakery_plugin_options['foodbakery_currency_alignment']) ? $foodbakery_plugin_options['foodbakery_currency_alignment'] : 'Left';
    if ($currency_alignment == 'Right') {
        $amount_with_currency_symbol_html = $amount_html . ' ' . $currency_symbol_html;
    } else {
        $amount_with_currency_symbol_html = $currency_symbol_html . $amount_html;
    }
    return $amount_with_currency_symbol_html;
}

add_action('wp_ajax_update_reviews_status', 'update_reviews_status_callback', 10);

/**
 * @param $post_id
 * @param $status
 */
function update_reviews_status_callback() {
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $restaurant_id = isset($_POST['restaurant_id']) ? $_POST['restaurant_id'] : '';

    if ($restaurant_id == '') {
        $res = array('type' => 'error', 'msg' => 'Post does not exist.');
        echo json_encode($res);
        die();
    }

    if ($post_id != '' && $status != '' && $restaurant_id != '') {
        $update_review_status['ID'] = $post_id;
        $update_review_status['post_status'] = $status;
        wp_update_post($update_review_status);

        /* Rating data count update */
        $existing_ratings_data = get_post_meta($restaurant_id, 'foodbakery_ratings', true);

        if (is_array($existing_ratings_data) && !empty($existing_ratings_data)) {
            if (is_numeric($existing_ratings_data['reviews_count'])) {
                if ($status == 'publish') {
                    $existing_ratings_data['reviews_count'] = $existing_ratings_data['reviews_count'] + 1;
                } else if ($status == 'pending' && $existing_ratings_data['reviews_count'] > 0) {
                    $existing_ratings_data['reviews_count'] = $existing_ratings_data['reviews_count'] - 1;
                }
                /* update rating count */
                update_post_meta($restaurant_id, 'foodbakery_ratings', $existing_ratings_data);
            }
        }

        $res = array('type' => 'success', 'msg' => 'You have successfully changed your status');
        echo json_encode($res);
        die();
    }

    $res = array('type' => 'error', 'msg' => 'There is some problem please consern with administrator.');
    echo json_encode($res);
    die();
}
