<?php
/**
 * Jobs Restaurant search box
 *
 */
?> 
<!--Foodbakery Element Start-->
<?php
global $foodbakery_post_restaurant_types;
// start ads script
$open_close_show_labels = isset($atts['open_close_show_labels']) ? $atts['open_close_show_labels'] : 'no';
$restaurant_ads_switch = isset($atts['restaurant_ads_switch']) ? $atts['restaurant_ads_switch'] : 'no';
$restaurant_search_keyword = isset($atts['restaurant_search_keyword']) ? $atts['restaurant_search_keyword'] : '';
$restaurant_sort_by = isset($atts['restaurant_sort_by']) ? $atts['restaurant_sort_by'] : '';

$listing_col_class = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
if ($restaurant_search_keyword == 'yes' || $restaurant_sort_by == 'yes') {
    $listing_col_class = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
}


if ($restaurant_ads_switch == 'yes') {
    $restaurant_ads_after_list_series = isset($atts['restaurant_ads_after_list_count']) ? $atts['restaurant_ads_after_list_count'] : '5';
    if ($restaurant_ads_after_list_series != '') {
        $restaurant_ads_list_array = explode(",", $restaurant_ads_after_list_series);
    }
    $restaurant_ads_after_list_array_count = sizeof($restaurant_ads_list_array);
    $restaurant_ads_after_list_flag = 0;

    $i = 0;
    $array_i = 0;
    $restaurant_ads_after_list_array_final = '';
    while ($restaurant_ads_after_list_array_count > $array_i) {
        if (isset($restaurant_ads_list_array[$array_i]) && $restaurant_ads_list_array[$array_i] != '') {
            $restaurant_ads_after_list_array[$i] = $restaurant_ads_list_array[$array_i];
            $i ++;
        }
        $array_i ++;
    }
    // new count 
    $restaurant_ads_after_list_array_count = sizeof($restaurant_ads_after_list_array);
}


$restaurant_page = isset($_REQUEST['restaurant_page']) ? $_REQUEST['restaurant_page'] : '';
$posts_per_page = isset($atts['posts_per_page']) ? $atts['posts_per_page'] : '';
$restaurant_location_options = isset($atts['restaurant_location']) ? $atts['restaurant_location'] : '';
if ($restaurant_location_options != '') {
    $restaurant_location_options = explode(',', $restaurant_location_options);
}
$counter = 0;
if ($restaurant_page >= 2) {
    $counter = ( ($restaurant_page - 1) * $posts_per_page );
}
$restaurant_ads_number_counter = 1;
$restaurant_ads_flag_counter = 0;
$restaurant_ads_last_number = 0;
if (isset($restaurant_ads_after_list_array) && !empty($restaurant_ads_after_list_array)) {
    foreach ($restaurant_ads_after_list_array as $key => $restaurant_ads_number) {
        $restaurant_ads_last_number = $restaurant_ads_number;
    }
    foreach ($restaurant_ads_after_list_array as $key => $restaurant_ads_number) {
        if ($restaurant_page == 1 || $restaurant_page == '') {
            $restaurant_ads_flag_counter = $key;
            break;
        } elseif ($counter < $restaurant_ads_number) {
            $restaurant_ads_flag_counter = $key;
            break;
        } elseif ($restaurant_ads_number_counter == $restaurant_ads_after_list_array_count) {
            $restaurant_ads_flag_counter = $key;
            break;
        }
        $restaurant_ads_number_counter ++;
    }
}
// end ads script
if ($restaurant_loop_obj->have_posts()) {
    $flag = 1;
    ?>
    <div class="listing fancy">
        <ul class="row">
            <?php
            if ($restaurant_ads_switch == 'yes') {
                if ($restaurant_ads_after_list_array_count > 0 && ( $restaurant_page == 1 || $restaurant_page == '')) {
                    if ($counter == $restaurant_ads_after_list_array[$restaurant_ads_flag_counter] && $restaurant_ads_after_list_array[$restaurant_ads_flag_counter] == 0) {
                        ?>
                        <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php do_action('foodbakery_random_ads', 'restaurant_banner'); ?>
                        </li>
                        <?php
                        if ($restaurant_ads_flag_counter < $restaurant_ads_after_list_array_count) {
                            $restaurant_ads_flag_counter ++;
                        }
                    }
                }
            }
            while ($restaurant_loop_obj->have_posts()) : $restaurant_loop_obj->the_post();
                global $post, $foodbakery_publisher_profile;
                $restaurant_id = $post;
                $Foodbakery_Locations = new Foodbakery_Locations();
                $get_restaurant_location = $Foodbakery_Locations->get_element_restaurant_location($restaurant_id, $restaurant_location_options);
                $foodbakery_restaurant_username = get_post_meta($restaurant_id, 'foodbakery_restaurant_username', true);
                $foodbakery_restaurant_is_featured = get_post_meta($restaurant_id, 'foodbakery_restaurant_is_featured', true);
                $foodbakery_restaurant_price_options = get_post_meta($restaurant_id, 'foodbakery_restaurant_price_options', true);
                $foodbakery_restaurant_posted = get_post_meta($restaurant_id, 'foodbakery_restaurant_posted', true);
                $foodbakery_transaction_restaurant_reviews = get_post_meta($restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);
                $foodbakery_restaurant_posted = foodbakery_time_elapsed_string($foodbakery_restaurant_posted);

                $restaurant_pickup_delivery = get_post_meta($restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);
                $restaurant_delivery_time = get_post_meta($restaurant_id, 'foodbakery_delivery_time', true);
                $restaurant_pickup_time = get_post_meta($restaurant_id, 'foodbakery_restaurant_pickup_time', true);

                $foodbakery_restaurant_price = '';
                if ($foodbakery_restaurant_price_options == 'price') {
                    $foodbakery_restaurant_price = get_post_meta($restaurant_id, 'foodbakery_restaurant_price', true);
                } else if ($foodbakery_restaurant_price_options == 'on-call') {
                    $foodbakery_restaurant_price = 'ON CALL';
                }

                $foodbakery_restaurant_type = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
                $foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
                if ($restaurant_type_post = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type'))
                    $restaurant_type_id = $restaurant_type_post->ID;
                $restaurant_type_id = isset($restaurant_type_id) ? $restaurant_type_id : '';
                $foodbakery_user_reviews = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);

                $foodbakery_restaurant_type_price_switch = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_price', true);

                $featured_class = '';
                if ($foodbakery_restaurant_is_featured == 'on') {
                    $featured_class = 'featured';
                } else {
                    $featured_class = '';
                }
                $foodbakery_profile_image = $foodbakery_publisher_profile->publisher_get_profile_image($foodbakery_restaurant_username);
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
                            $comma_flag ++;
                        }
                    }
                }
                ?>
                <li class="<?php echo esc_html($listing_col_class); ?>">
                    <div class="list-post <?php echo esc_html($featured_class); ?>">
                        <div class="img-holder">
                            <figure>
                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        $img_atr = array('class' => 'img-thumb');
                                        the_post_thumbnail('full', $img_atr);
                                    } else {
                                        $no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image4x3.jpg');
                                        $no_image = '<img src="' . $no_image_url . '" />';
                                        echo force_balance_tags($no_image);
                                    }
                                    ?>
                                </a>
                            </figure>
							<?php if ( $open_close_show_labels == 'yes' ) : ?>
								<?php $status = foodbakery_open_close_status($restaurant_id); ?>
								<span class="restaurant-status <?php echo strtolower($status[1]); ?>"><em class="bookmarkRibbon"></em><?php echo esc_html__($status[0], 'foodbakery'); ?></span>
							<?php endif; ?>
                        </div>
                        <?php
                        $ratings_data = array(
                            'overall_rating' => 0.0,
                            'count' => 0,
                        );
                        $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $restaurant_id);
                        ?>
                        <div class="text-holder">
                            <?php if ($foodbakery_transaction_restaurant_reviews == 'on' && $foodbakery_user_reviews == 'on' && $ratings_data['count'] > 0) { ?>
                                <div class="list-rating">
                                    <div class="rating-star">
                                        <span class="rating-box" style="width: <?php echo intval($ratings_data['overall_rating']); ?>%;"></span>
                                    </div>
                                    <span class="reviews">(<?php echo esc_html($ratings_data['count']); ?>)</span>
                                </div>
                            <?php } ?>
                            <div class="post-title">
                                <h5>
                                    <a href="<?php echo esc_url(get_permalink($restaurant_id)); ?>"><?php echo wp_trim_words(get_the_title($restaurant_id), 10, '...'); ?></a>
                                    <?php if ($foodbakery_restaurant_is_featured == 'on') { ?>
                                        <span class="sponsored text-color"><?php echo esc_html__('Sponsored', 'foodbakery'); ?></span>
                                    <?php } ?>
                                </h5>
                            </div>

                            <?php
                            if ($foodbakery_cate_str != '') {
                                ?>
                                <address> <span><?php echo esc_html__('Type of food :', 'foodbakery'); ?> </span> <?php echo esc_html($foodbakery_cate_str); ?></address>
                                <?php
                            }
                            ?>
                            <div class="delivery-potions">
                                <?php
                                if ($restaurant_pickup_delivery == 'delivery' || $restaurant_pickup_delivery == 'delivery_and_pickup') {
                                    ?>
                                    <div class="post-time">
                                        <i class="icon-motorcycle"></i>
                                        <div class="time-tooltip">
                                            <div class="time-tooltip-holder"> <b class="tooltip-label"><?php esc_html_e('Delivery time', 'foodbakery') ?></b> <b class="tooltip-info"><?php printf(esc_html__('Your order will be delivered in %s minutes', 'foodbakery'), esc_html($restaurant_delivery_time)); ?></b> </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                if ($restaurant_pickup_delivery == 'pickup' || $restaurant_pickup_delivery == 'delivery_and_pickup') {
                                    ?>
                                    <div class="post-time">
                                        <i class="icon-clock4"></i>
                                        <div class="time-tooltip">
                                            <div class="time-tooltip-holder"> <b class="tooltip-label"><?php esc_html_e('Pickup time', 'foodbakery') ?></b> <b class="tooltip-info"><?php printf(esc_html__('You can pickup order in  %s minutes', 'foodbakery'), esc_html($restaurant_pickup_time)); ?>
                                        </div>
                                    </div>
                                    <?php
                                }

                                if (!empty($get_restaurant_location)) {
                                    ?>
                                    <span><?php echo esc_html(implode(', ', $get_restaurant_location)); ?></span>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        $cur_user_details = wp_get_current_user();
                        $user_company_id = get_user_meta($cur_user_details->ID, 'foodbakery_company', true);
                        $publisher_profile_type = get_post_meta($user_company_id, 'foodbakery_publisher_profile_type', true);

                        if ($publisher_profile_type != 'restaurant') {
                            ?>
                            <div class="list-option">
                                <?php
                                $shortlist_label = '';
                                $shortlisted_label = '';
                                $figcaption_div = true;
                                $book_mark_args = array(
                                    'before_label' => $shortlist_label,
                                    'after_label' => $shortlisted_label,
                                    'before_icon' => '<i class="icon-heart-o"></i>',
                                    'after_icon' => '<i class="icon-heart4"></i>',
                                );
                                do_action('foodbakery_shortlists_frontend_button', $restaurant_id, $book_mark_args, $figcaption_div);
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </li>
                <?php
                if ($restaurant_ads_switch == 'yes') {
                    if ($restaurant_ads_after_list_array_count > 0) {
                        $new_counter = $counter + 1;
                        $restaurant_ads_value = $restaurant_ads_after_list_array[$restaurant_ads_flag_counter];
                        if ($new_counter == $restaurant_ads_after_list_array[$restaurant_ads_flag_counter]) {
                            ?><li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php do_action('foodbakery_random_ads', 'restaurant_banner'); ?>
                            </li>
                            <?php
                            if ($restaurant_ads_flag_counter < ($restaurant_ads_after_list_array_count - 1)) {
                                $restaurant_ads_flag_counter ++;
                            }
                        } elseif ($new_counter % $restaurant_ads_value == 0 && $new_counter > $restaurant_ads_last_number && $new_counter != 1) {
                            ?><li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <?php do_action('foodbakery_random_ads', 'restaurant_banner'); ?>
                            </li>
                            <?php
                        }
                    }
                }
                $counter ++;
            endwhile;
            ?>
        </ul>
    </div>
    <?php
} else {
    echo '<div class="no-restaurant-match-error"><h6><i class="icon-warning"></i><strong> ' . esc_html__('Sorry !', 'foodbakery') . '</strong>&nbsp; ' . esc_html__("There are no restaurants matching your search.", 'foodbakery') . ' </h6></div>';
}
?>
<!--Foodbakery Element End-->