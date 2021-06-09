<?php
/**
 * Jobs Restaurant search box
 *
 */
?>
<!--Element Section Start-->
<!--Foodbakery Element Start-->
<?php
global $foodbakery_post_restaurant_types;
if (false === ( $restaurant_view = foodbakery_get_transient_obj('foodbakery_restaurant_view' . $restaurant_short_counter) )) {
    $restaurant_view = isset($atts['restaurant_view']) ? $atts['restaurant_view'] : '';
}

$restaurant_search_keyword = isset($atts['restaurant_search_keyword']) ? $atts['restaurant_search_keyword'] : '';
$restaurant_sort_by = isset($atts['restaurant_sort_by']) ? $atts['restaurant_sort_by'] : '';

$listing_grid_sidebar = '';
if ($restaurant_search_keyword == 'yes' || $restaurant_sort_by == 'yes') {
    $listing_grid_sidebar = 'listing-grid-sidebar';
}

$restaurants_title = isset($atts['restaurants_title']) ? $atts['restaurants_title'] : '';
$restaurants_subtitle = isset($atts['restaurants_subtitle']) ? $atts['restaurants_subtitle'] : '';
$search_box = isset($atts['search_box']) ? $atts['search_box'] : '';
$main_class = 'restaurant-medium';
// start ads script
$restaurant_ads_switch = isset($atts['restaurant_ads_switch']) ? $atts['restaurant_ads_switch'] : 'no';
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



$columns_class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
if ($restaurant_view == 'grid') {
    $columns_class = 'col-lg-4 col-md-4 col-sm-12 col-xs-12';
    if ($search_box == 'yes' && $restaurant_view != 'map') {
	$columns_class = 'col-lg-6 col-md-6 col-sm-12 col-xs-12';
    }
    $main_class = 'restaurant-grid';
}

$restaurant_location_options = isset($atts['restaurant_location']) ? $atts['restaurant_location'] : '';
if ($restaurant_location_options != '') {
    $restaurant_location_options = explode(',', $restaurant_location_options);
}


if ($restaurant_loop_obj->have_posts()) {
    $flag = 1;
    ?>
    <div class="company-logo <?php echo esc_html($listing_grid_sidebar);?>">
	
        <ul>
	    <?php
	    if ($restaurant_ads_switch == 'yes') {
		if ($restaurant_ads_after_list_array_count > 0 && ( $restaurant_page == 1 || $restaurant_page == '')) {
		    if ($counter == $restaurant_ads_after_list_array[$restaurant_ads_flag_counter] && $restaurant_ads_after_list_array[$restaurant_ads_flag_counter] == 0) {
			?>
			<li class="listing-simple-banner col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
		$foodbakery_profile_image = $foodbakery_publisher_profile->publisher_get_profile_image($foodbakery_restaurant_username);
		$foodbakery_restaurant_price_options = get_post_meta($restaurant_id, 'foodbakery_restaurant_price_options', true);
		$foodbakery_restaurant_type = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
		$foodbakery_transaction_restaurant_reviews = get_post_meta($restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);
		$foodbakery_restaurant_posted = get_post_meta($restaurant_id, 'foodbakery_restaurant_posted', true);
		$foodbakery_restaurant_posted = foodbakery_time_elapsed_string($foodbakery_restaurant_posted);

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
			    $comma_flag ++;
			}
		    }
		}
		?>
		<li class="has-border">
		    <figure>
			<a href="<?php the_permalink(); ?>">
			    <?php
			    if (has_post_thumbnail()) {
					the_post_thumbnail('full');
			    } else {
					if ($restaurant_view == 'grid') {
						$no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image-130x130.jpg');
						$no_image = '<img class="img-grid" src="' . $no_image_url . '" />';
						echo force_balance_tags($no_image);
					} else {
						// restaurant image
						$no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image4x3.jpg');
						$no_image = '<img class="img-list" src="' . $no_image_url . '" />';
						echo force_balance_tags($no_image);
					}
			    }
			    ?>
			</a>
		    </figure>
		</li>

		<?php
		if ($restaurant_ads_switch == 'yes') {
		    if ($restaurant_ads_after_list_array_count > 0) {
			$new_counter = $counter + 1;
			$restaurant_ads_value = isset($restaurant_ads_after_list_array[$restaurant_ads_flag_counter]) ? $restaurant_ads_after_list_array[$restaurant_ads_flag_counter] : 0;
			if ($new_counter == $restaurant_ads_value) {
			    ?><li class="listing-simple-banner col-lg-12 col-md-12 col-sm-12 col-xs-12">
			    <?php do_action('foodbakery_random_ads', 'restaurant_banner'); ?>
		    	</li>
			    <?php
			    if ($restaurant_ads_flag_counter < ($restaurant_ads_after_list_array_count - 1)) {
				$restaurant_ads_flag_counter ++;
			    }
			} elseif ($new_counter % $restaurant_ads_value == 0 && $new_counter > $restaurant_ads_last_number && $new_counter != 1) {
			    ?><li class="listing-simple-banner col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php do_action('foodbakery_random_ads', 'restaurant_banner'); ?>
		    	</li>
			    <?php
			}
		    }
		}
		$counter ++;
		?>
		<?php
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