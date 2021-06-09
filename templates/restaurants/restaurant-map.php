<?php
/**
 * Restaurant Map View
 *
 */
global $foodbakery_plugin_options;
?>
<!--Element Section Start-->
<!--Foodbakery Element Start-->
<?php
$rand_numb = rand(10000000, 99999999);

if (isset($restaurant_ids) && is_array($restaurant_ids) && sizeof($restaurant_ids) > 0) {
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="foodbakery-restaurant-map">
	    <?php
	    $del_btn_class = ' is-disabled';
	    $draw_btn_class = '';

	    if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
		$get_loc_term = get_term_by('slug', $_REQUEST['location'], 'foodbakery_locations');
		if (isset($get_loc_term->term_id)) {
		    $location_coordinates = get_term_meta($get_loc_term->term_id, 'location_coordinates', true);
		}
	    }

	    if (isset($_REQUEST['loc_polygon']) && $_REQUEST['loc_polygon'] != '') {
		$loc_poly_cords = foodbakery_decode_url_string($_REQUEST['loc_polygon']);
		$loc_poly_cords = stripslashes($loc_poly_cords);
	    }

	    $foodbakery_map_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) && $foodbakery_plugin_options['foodbakery_map_zoom_level'] != '' ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '9';
	    $foodbakery_map_style = isset($foodbakery_plugin_options['foodbakery_def_map_style']) && $foodbakery_plugin_options['foodbakery_def_map_style'] != '' ? $foodbakery_plugin_options['foodbakery_def_map_style'] : '';
	    $foodbakery_map_lat = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) && $foodbakery_plugin_options['foodbakery_post_loc_latitude'] != '' ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '51.5';
	    $foodbakery_map_long = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) && $foodbakery_plugin_options['foodbakery_post_loc_longitude'] != '' ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '-0.2';
	    $foodbakery_map_marker_icon = isset($foodbakery_plugin_options['foodbakery_map_marker_icon']) && $foodbakery_plugin_options['foodbakery_map_marker_icon'] != '' ? $foodbakery_plugin_options['foodbakery_map_marker_icon'] : wp_foodbakery::plugin_url() . '/assets/frontend/images/map-marker.png';
	    $foodbakery_map_cluster_icon = isset($foodbakery_plugin_options['foodbakery_map_cluster_icon']) && $foodbakery_plugin_options['foodbakery_map_cluster_icon'] != '' ? $foodbakery_plugin_options['foodbakery_map_cluster_icon'] : wp_foodbakery::plugin_url() . '/assets/frontend/images/map-cluster.png';

	    if (isset($location_coordinates) && !empty($location_coordinates)) {
		$location_coordinates_arr = json_decode($location_coordinates, true);

		if (isset($location_coordinates_arr[0]['lat']) && isset($location_coordinates_arr[0]['lng'])) {
		    $foodbakery_map_lat = $location_coordinates_arr[0]['lat'];
		    $foodbakery_map_long = $location_coordinates_arr[0]['lng'];
		}
	    }

	    if (isset($loc_poly_cords) && !empty($loc_poly_cords)) {
		$loc_poly_cords_arr = json_decode($loc_poly_cords, true);

		$loc_poly_cords_bounds = isset($loc_poly_cords_arr['cords']) ? $loc_poly_cords_arr['cords'] : '';

		$loc_poly_cords_bounds_arr = json_decode($loc_poly_cords_bounds, true);
		if (isset($loc_poly_cords_bounds_arr[0]['lat']) && isset($loc_poly_cords_bounds_arr[0]['lng'])) {
		    $foodbakery_map_lat = $loc_poly_cords_bounds_arr[0]['lat'];
		    $foodbakery_map_long = $loc_poly_cords_bounds_arr[0]['lng'];
		}
	    }

	    $map_height = '400px';
	    $map_zoom = $foodbakery_map_zoom;
	    $map_latitude = $foodbakery_map_lat;
	    $map_longitude = $foodbakery_map_long;

	    $map_cords = array();

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

	    $map_params = array(
		'map_id' => $rand_numb,
		'map_zoom' => $map_zoom,
		'latitude' => $map_latitude,
		'longitude' => $map_longitude,
		'restaurant_cords' => $map_cords,
		'map_style' => $foodbakery_map_style,
		'marker_icon' => $foodbakery_map_marker_icon,
		'cluster_icon' => $foodbakery_map_cluster_icon,
	    );

	    if (isset($location_coordinates) && !empty($location_coordinates)) {
		$map_params['location_cords'] = $location_coordinates;
		$del_btn_class = '';
		$draw_btn_class = ' is-disabled';
	    }
	    if (isset($loc_poly_cords_bounds) && !empty($loc_poly_cords_bounds)) {
		$map_params['location_cords'] = $loc_poly_cords_bounds;
		$del_btn_class = '';
		$draw_btn_class = ' is-disabled';
	    }

	    $map_json = json_encode($map_params);
	    ?>
    	<ul class="map-actions">
    	    <li><a id="draw-map-<?php echo absint($rand_numb) ?>" class="act-btn<?php echo esc_html($draw_btn_class) ?>"><?php esc_html_e('Draw on Map', 'foodbakery') ?></a></li>
    	    <li><a id="cancel-draw-map-<?php echo absint($rand_numb) ?>" class="act-btn is-disabled"><?php esc_html_e('Cancel Draw', 'foodbakery') ?></a></li>
    	    <li><a id="delete-button-<?php echo absint($rand_numb) ?>" class="act-btn<?php echo esc_html($del_btn_class) ?>"><?php esc_html_e('Delete Area', 'foodbakery') ?></a></li>
    	</ul>
    	<div id="restaurant-records-<?php echo absint($rand_numb) ?>" class="restaurant-records-sec" style="display: none;">
    	    <p><span id="total-records-<?php echo absint($rand_numb) ?>">0</span>&nbsp;<?php esc_html_e('Records found', 'foodbakery') ?>,&nbsp;<?php esc_html_e('Showing', 'foodbakery') ?>&nbsp;<span id="showing-records-<?php echo absint($rand_numb) ?>">0</span>&nbsp;<?php esc_html_e('results', 'foodbakery') ?></p>
    	</div>
    	<div id="map-loader-<?php echo absint($rand_numb) ?>" class="map-loader"><div class="loader-holder"><img src="<?php echo wp_foodbakery::plugin_url() ?>assets/frontend/images/ajax-loader.gif" alt=""></div></div>
    	<div id="foodbakery-restaurant-map-<?php echo absint($rand_numb) ?>" style="width: 100%; height: <?php echo esc_html($map_height) ?>"></div>
    	<script type="text/javascript">
    	    var dataobj = jQuery.parseJSON('<?php echo addslashes($map_json) ?>');
		    <?php
		    if (isset($_POST['action'])) {
			?>
		    foodbakery_restaurant_map(dataobj, 'ajax');
		    jQuery('#map-restaurant-map-<?php echo absint($rand_numb) ?>').resize();
		    jQuery('#map-loader-<?php echo absint($rand_numb) ?>').html('');
			<?php
		    } else {
			?>
		    jQuery(window).load(function () {
			foodbakery_restaurant_map(dataobj, 'no-ajax');
		    });
			<?php
		    }
		    ?>
    	    jQuery(window).load(function () {
    		jQuery('#map-loader-<?php echo absint($rand_numb) ?>').html('');
    	    });
    	</script>
        </div>
    </div>
    <?php
}
?>
<!--Foodbakery Element End-->