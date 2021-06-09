<?php
/**
 * Restaurant Map View
 *
 */
global $foodbakery_plugin_options, $foodbakery_shortcode_restaurants_frontend;
?>
<!--Element Section Start-->
<!--Foodbakery Element Start-->
<?php

$rand_numb = isset($restaurant_map_counter) ? $restaurant_map_counter : '';

$flag = 1;

$map_position = isset($atts['restaurant_map_position']) ? $atts['restaurant_map_position'] : '';
if ( false === ( $restaurant_view = foodbakery_get_transient_obj('foodbakery_restaurant_view' . $restaurant_short_counter) ) ) {
	$restaurant_view = isset($atts['restaurant_view']) ? $atts['restaurant_view'] : '';
}
$map_elem_height = isset($atts['restaurant_map_height']) && $atts['restaurant_map_height'] > 0 ? absint($atts['restaurant_map_height']) : '400';
if ( $map_position == 'full' ) {
	$map_height = $map_elem_height . 'px';
} else {
	$map_height = '100%';
}

$map_display = ' style="display: none;"';
if ( $restaurant_view == 'map' ) {
	$map_display = ' style="display: block;"';
}
?>
<div class="dev-restaurant-map-holder"<?php echo esc_html($map_display); ?>>
<div class="detail-map col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="foodbakery-restaurant-map">
		<?php
		$foodbakery_shortcode_restaurants_frontend->restaurant_layout_switcher_fields($atts, $restaurant_short_counter, 'map', true);
		$del_btn_class = ' is-disabled';
		$draw_btn_class = '';

		if ( isset($_REQUEST['location']) && $_REQUEST['location'] != '' ) {
			$get_loc_term = get_term_by('slug', $_REQUEST['location'], 'foodbakery_locations');
			if ( isset($get_loc_term->term_id) ) {
				$location_coordinates = get_term_meta($get_loc_term->term_id, 'location_coordinates', true);
			}
		}
		if ( isset($_REQUEST['loc_polygon']) && $_REQUEST['loc_polygon'] != '' ) {
			$loc_poly_cords = foodbakery_decode_url_string($_REQUEST['loc_polygon']);
			$loc_poly_cords = stripslashes($loc_poly_cords);
		}

		$foodbakery_map_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) && $foodbakery_plugin_options['foodbakery_map_zoom_level'] != '' ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '9';
		$foodbakery_map_style = isset($foodbakery_plugin_options['foodbakery_def_map_style']) && $foodbakery_plugin_options['foodbakery_def_map_style'] != '' ? $foodbakery_plugin_options['foodbakery_def_map_style'] : '';
		$foodbakery_map_lat = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) && $foodbakery_plugin_options['foodbakery_post_loc_latitude'] != '' ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '51.5';
		$foodbakery_map_long = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) && $foodbakery_plugin_options['foodbakery_post_loc_longitude'] != '' ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '-0.2';
		$foodbakery_map_marker_icon = isset($foodbakery_plugin_options['foodbakery_map_marker_icon']) && $foodbakery_plugin_options['foodbakery_map_marker_icon'] != '' ? $foodbakery_plugin_options['foodbakery_map_marker_icon'] : wp_foodbakery::plugin_url() . '/assets/frontend/images/map-marker.png';
		$foodbakery_map_cluster_icon = isset($foodbakery_plugin_options['foodbakery_map_cluster_icon']) && $foodbakery_plugin_options['foodbakery_map_cluster_icon'] != '' ? $foodbakery_plugin_options['foodbakery_map_cluster_icon'] : wp_foodbakery::plugin_url() . '/assets/frontend/images/map-cluster.png';

		if ( isset($location_coordinates) && ! empty($location_coordinates) ) {
			$location_coordinates_arr = json_decode($location_coordinates, true);

			if ( isset($location_coordinates_arr[0]['lat']) && isset($location_coordinates_arr[0]['lng']) ) {
				$foodbakery_map_lat = $location_coordinates_arr[0]['lat'];
				$foodbakery_map_long = $location_coordinates_arr[0]['lng'];
			}
		}

		if ( isset($loc_poly_cords) && ! empty($loc_poly_cords) ) {
			$loc_poly_cords_arr = json_decode($loc_poly_cords, true);

			$loc_poly_cords_bounds = isset($loc_poly_cords_arr['cords']) ? $loc_poly_cords_arr['cords'] : '';

			$loc_poly_cords_bounds_arr = json_decode($loc_poly_cords_bounds, true);
			if ( isset($loc_poly_cords_bounds_arr[0]['lat']) && isset($loc_poly_cords_bounds_arr[0]['lng']) ) {
				$foodbakery_map_lat = $loc_poly_cords_bounds_arr[0]['lat'];
				$foodbakery_map_long = $loc_poly_cords_bounds_arr[0]['lng'];
			}
		}

		$map_zoom = $foodbakery_map_zoom;
		$map_latitude = $foodbakery_map_lat;
		$map_longitude = $foodbakery_map_long;

		//

		$map_params = array(
			'map_id' => $rand_numb,
			'map_zoom' => $map_zoom,
			'latitude' => $map_latitude,
			'longitude' => $map_longitude,
			'map_style' => $foodbakery_map_style,
			'marker_icon' => $foodbakery_map_marker_icon,
			'cluster_icon' => $foodbakery_map_cluster_icon,
		);
		
		$map_init_params = array(
			'map_id' => $rand_numb,
			'map_zoom' => $map_zoom,
			'latitude' => $map_latitude,
			'longitude' => $map_longitude,
			'map_style' => $foodbakery_map_style,
			'marker_icon' => $foodbakery_map_marker_icon,
		);

		if ( isset($location_coordinates) && ! empty($location_coordinates) ) {
			$map_params['location_cords'] = $location_coordinates;
			$del_btn_class = '';
			$draw_btn_class = ' is-disabled';
		}
		if ( isset($loc_poly_cords_bounds) && ! empty($loc_poly_cords_bounds) ) {
			$map_params['location_cords'] = $loc_poly_cords_bounds;
			$del_btn_class = '';
			$draw_btn_class = ' is-disabled';
		}

		$map_json = json_encode($map_params);
		
		$map_init_json = json_encode($map_init_params);
		?>
		<ul class="map-actions">
			<li><a id="draw-map-<?php echo absint($rand_numb) ?>" class="act-btn<?php echo esc_html($draw_btn_class) ?>"><?php esc_html_e('Draw on Map', 'foodbakery') ?></a></li>
			<li><a id="cancel-draw-map-<?php echo absint($rand_numb) ?>" class="act-btn is-disabled"><?php esc_html_e('Cancel Draw', 'foodbakery') ?></a></li>
			<li><a id="delete-button-<?php echo absint($rand_numb) ?>" class="act-btn<?php echo esc_html($del_btn_class) ?>"><?php esc_html_e('Delete Area', 'foodbakery') ?></a></li>
			<li><a id="map-lock-<?php echo absint($rand_numb) ?>" class="map-unloked"><i class="icon-unlock"></i></a></li>
		</ul>
		<div id="restaurant-records-<?php echo absint($rand_numb) ?>" class="restaurant-records-sec" style="display: none;">
			<p><span id="total-records-<?php echo absint($rand_numb) ?>">0</span>&nbsp;<?php esc_html_e('Records found', 'foodbakery') ?>,&nbsp;<?php esc_html_e('Showing', 'foodbakery') ?>&nbsp;<span id="showing-records-<?php echo absint($rand_numb) ?>">0</span>&nbsp;<?php esc_html_e('results', 'foodbakery') ?></p>
		</div>
		<div id="map-loader-<?php echo absint($rand_numb) ?>" class="map-loader"><div class="loader-holder"><img src="<?php echo wp_foodbakery::plugin_url() ?>assets/frontend/images/ajax-loader.gif" alt=""></div></div>
		<div id="foodbakery-restaurant-map-<?php echo absint($rand_numb) ?>" style="height: <?php echo esc_html($map_height) ?>;"></div>
	</div>
</div>
</div>

<!--Foodbakery Element End-->