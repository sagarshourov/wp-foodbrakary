<?php
/**
 * This file handles locations functionality which register locations by registring
 * new taxonomies.
 *
 * @since		1.0
 * @package		ProCoupler
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * This class register taxonomy for locations(Country, State, City and Town).
 * Also register theme options and fontend UI.
 *
 * @package		Foodbakery
 * @since		1.0
 */
if (!class_exists('Foodbakery_Locations')) {
    class Foodbakery_Locations {

	public static $taxonomy_name = 'foodbakery_locations';

	public function __construct() {
	    add_action('init', array($this, 'admin_init_callback'));
	    add_action('admin_menu', array($this, 'remove_my_taxanomy_meta_callback'));
	    add_action('admin_footer', array($this, 'full_width_location_callback'));
	    add_filter('manage_edit-foodbakery_locations_columns', array($this, 'locations_theme_columns_callback'));
	    add_filter('manage_foodbakery_locations_custom_column', array($this, 'locations_theme_columns_content_callback'), 10, 3);
	    add_action('wp_ajax_dropdown_options_for_search_location_data', array($this, 'dropdown_options_for_search_location_data'));
	    add_action('wp_ajax_nopriv_dropdown_options_for_search_location_data', array($this, 'dropdown_options_for_search_location_data'));
	    add_action('wp_ajax_foodbakery_get_all_locations', array($this, 'foodbakery_get_all_locations_callback'));
	    add_action('wp_ajax_nopriv_foodbakery_get_all_locations', array($this, 'foodbakery_get_all_locations_callback'));
	    add_action('wp_ajax_foodbakery_get_all_default_locations', array($this, 'foodbakery_get_all_default_locations_callback'));
	    add_action('wp_ajax_nopriv_foodbakery_get_all_default_locations', array($this, 'foodbakery_get_all_default_locations_callback'));
	    add_action('create_foodbakery_locations', array($this, 'save_locations_fields_added_callback'));
	    add_action('edited_foodbakery_locations', array($this, 'save_locations_fields_updated_callback'));
	    add_action('foodbakery_locations_edit_form_fields', array($this, 'edit_locations_fields_callback'));
	    add_action('foodbakery_locations_add_form_fields', array($this, 'locations_fields_callback'));
	    add_filter('get_locations_fields_data', array($this, 'get_locations_fields_data_callback'), 10, 2);
	    // AJAX handlers for Locations Search Field Frontend.
	    add_action('wp_ajax_get_locations_for_search', array($this, 'get_locations_for_search_callback'));
	    add_action('wp_ajax_nopriv_get_locations_for_search', array($this, 'get_locations_for_search_callback'));
	    // Backend AJAX handlers.
	    add_action('wp_ajax_get_locations_list', array($this, 'get_locations_list_callback'));
	    add_action('wp_ajax_nopriv_get_locations_list', array($this, 'get_locations_list_callback'));
	    add_action('wp_ajax_generate_locations_backup', array($this, 'generate_locations_backup_callback'));
	    add_action('wp_ajax_delete_locations_backup_file', array($this, 'delete_locations_backup_file_callback'));
	    add_action('wp_ajax_restore_locations_backup', array($this, 'restore_locations_backup_callback'));
	    add_action('wp_ajax_foodbakery_uploading_import_file', array($this, 'foodbakery_uploading_import_file_callback'));
	}
	

	
	

	public function google_map_script_callback() {
	    global $foodbakery_plugin_options;
	    $google_api_key = isset($foodbakery_plugin_options['foodbakery_google_api_key']) ? $foodbakery_plugin_options['foodbakery_google_api_key'] : '';
	    if (isset($foodbakery_plugin_options['foodbakery_google_api_key']) && $foodbakery_plugin_options['foodbakery_google_api_key'] != '') {
		$google_api_key = '?key=' . $google_api_key . '&libraries=drawing,places&sensor=false';
	    }
	    wp_enqueue_script('foodbakery_google_autocomplete_script', 'https://maps.googleapis.com/maps/api/js' . $google_api_key, '', '', true);
	}
		

	/**
	 * Init.
	 */
	public function admin_init_callback() {
	    $this->register_locations_taxonomy_callback();
	    
	}


	
	/**
	 * Register Locations taxonomy.
	 */
	public function register_locations_taxonomy_callback() {
	    $post_type = 'restaurants';
	    $args = array(
		'hierarchical' => true,
		'label' => __('Locations', 'foodbakery'),
		'show_ui' => true,
		'show_in_menu' => true,
		'rewrite' => false,
		'not_found' => __('No locations found.', 'foodbakery'),
		'public' => false,
		'labels' => array(
		    'search_items' => __('Search Locations', 'foodbakery'),
		    'popular_items' => __('Popular Locations', 'foodbakery'),
		    'all_items' => __('All Locations', 'foodbakery'),
		    'parent_item' => __('Parent Location', 'foodbakery'),
		    'parent_item_colon' => __('Parent Location:', 'foodbakery'),
		    'edit_item' => __('Edit Location', 'foodbakery'),
		    'update_item' => __('Update Location', 'foodbakery'),
		    'add_new_item' => __('Add New Location', 'foodbakery'),
		    'new_item_name' => __('New Location Name', 'foodbakery'),
		    'separate_items_with_commas' => __('Separate Locations with commas', 'foodbakery'),
		    'add_or_remove_items' => __('Add or Remove Locations', 'foodbakery'),
		    'choose_from_most_used' => __('Choose from the most used Locations', 'foodbakery'),
		)
	    );
	    register_taxonomy(Foodbakery_Locations::$taxonomy_name, $post_type, $args);
		
	}

	/**
	 * Start Function remove taxonomies meta
	 */
	public function remove_my_taxanomy_meta_callback() {
	    remove_meta_box('foodbakery_locationsdiv', 'post', 'side');
		
	}

	/**
	 * Make locations restaurant full width.
	 */
	public function full_width_location_callback() {

	    global $max_allowed_location_levels, $area_border_color, $area_fill_color;
	    $a_get_current_requset_uri = $_SERVER["REQUEST_URI"];
	    $a_get_current_requset_uri = explode("?", $a_get_current_requset_uri);
	    $a_get_current_taxonomy = isset($a_get_current_requset_uri[1]) ? explode("&", $a_get_current_requset_uri[1]) : '';
	    if (is_array($a_get_current_taxonomy) && count($a_get_current_taxonomy) > 0) {
		if (isset($a_get_current_taxonomy[0]) && $a_get_current_taxonomy['0'] == 'taxonomy=' . Foodbakery_Locations::$taxonomy_name) {
		    echo '<style type="text/css">';
		    echo '#col-right {width:100%;}
                        #popup_div {
                                background-color: #fff;
                                border: 2px solid #ccc;
                                box-shadow: 10px 10px 5px #888888;
                                display: none;
                                padding: 12px;
                                position: fixed;
                                left: 497px;
                                top: 90px; 
                                z-index: 10000;
                        } 

                        #close_div{float:right;} ';
		    echo '</style>';
		    ?>
		    <script type="text/javascript">
		        (function ($) {
		    <?php
		    $foodbakery_plugin_options = get_option('foodbakery_plugin_options');

		    $selected_levels = array('country', 'state', 'city', 'town');

            $flag = apply_filters('foodbakery_add_county_in_location_level', false);
            if($flag){
                $selected_levels = array('country', 'state', 'county', 'city', 'town');
                $foodbakery_plugin_options['foodbakery_locations_levels'] = unserialize(get_option('foodbakery_locations_levels'));
            }

		    if (isset($foodbakery_plugin_options['foodbakery_locations_levels'])) {
			$selected_levels = $foodbakery_plugin_options['foodbakery_locations_levels'];
		    }

		    $map_zoom_level = 8;
		    if (isset($foodbakery_plugin_options['foodbakery_map_zoom_level'])) {
			$map_zoom_level = $foodbakery_plugin_options['foodbakery_map_zoom_level'];
		    }

		    $drawing_tools = true;
		    if (isset($foodbakery_plugin_options['foodbakery_drawing-tools'])) {
			$drawing_tools = $foodbakery_plugin_options['foodbakery_drawing-tools'];
		    }

		    $drawing_tools_line_color = '#000';
		    if (isset($foodbakery_plugin_options['foodbakery_drawing_tools_line_color'])) {
			$drawing_tools_line_color = $foodbakery_plugin_options['foodbakery_drawing_tools_line_color'];
		    }

		    $drawing_tools_fill_color = '#000';
		    if (isset($foodbakery_plugin_options['foodbakery_drawing_tools_fill_color'])) {
			$drawing_tools_fill_color = $foodbakery_plugin_options['foodbakery_drawing_tools_fill_color'];
		    }

		    $default_map_latitude = '51.555210';
		    if (isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) && !empty($foodbakery_plugin_options['foodbakery_post_loc_latitude'])) {
			$default_map_latitude = $foodbakery_plugin_options['foodbakery_post_loc_latitude'];
		    }

		    $default_map_longitude = '-0.165680';
		    if (isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) && !empty($foodbakery_plugin_options['foodbakery_post_loc_longitude'])) {
			$default_map_longitude = $foodbakery_plugin_options['foodbakery_post_loc_longitude'];
		    }
			
		    ?>
		    	var location_levels = <?php echo json_encode($selected_levels); ?>;
		    	var allowed_location_levels = location_levels.length - 1;
		    	var map_zoom_level = <?php echo intval($map_zoom_level); ?>;
		    	var drawing_tools = <?php echo esc_html($drawing_tools) ? 'true' : 'false'; ?>;
		    	var border_color = "<?php echo esc_html($drawing_tools_line_color); ?>";
		    	var fill_color = "<?php echo esc_html($drawing_tools_fill_color); ?>";
		    	var default_map_latitude = <?php echo esc_html($default_map_latitude); ?>;
		    	var default_map_longitude = <?php echo esc_html($default_map_longitude); ?>;
		    	var controlCSS = {
		    	    "background-color": "#fff",
		    	    "color": "#555",
		    	    "bax-shadow": "0px 1px 4px -1px rgba(0, 0, 0, 0.3)",
		    	    "cursor": "pointer",
		    	    "margin-bottom": "10px",
		    	    "margin-top": "10px",
		    	    "text-align": "center",
		    	    "font-family": "Roboto,Arial,sans-serif",
		    	    "font-size": "11px",
		    	    "padding": "8px",
		    	    "display": "inline-block",
		    	    "border-right": "1px solid #d0d0d0",
		    	};
		    	var markersArray = [];
		    	var map = null;
		    	var geocoder = null;
		    	$(document).ready(function () {
		    	    $(".top .bulkactions").prepend('<a href="javascript:void(0);" id="btn_add" class="button"><?php _e('Add Location', 'foodbakery') ?> </a>'); // add link						
                            
                             //jQuery('<div class="btn-action"></div>').prependTo(jQuery("#posts-filter"));
                             //$(".btn-action").prepend('<a href="javascript:void(0);" id="btn_add" class="button"><?php _e('Add Location', 'foodbakery') ?> </a>'); // add link		
                           $("#col-left").hide();
		    	    var popupDiv = '<div id="popup_div"><span id="close_div" class="icon icon-close icon-close2"></span></div>'; // popup container html
		    	    $("#wpfooter").prepend(popupDiv); // send to footer div
		    	    $("#tag-name").attr("required", "true");
		    	    $(".term-description-wrap").hide();
		    	    $("#popup_div").hide();
		    	    $(document).on('click', '#btn_add', function () {
		    		$("#popup_div").show();
		    		$("#col-left").show();
		    		$("#col-left").css({"width": "100%"});
		    		var texonomy_form = $("#col-left").clone(true, true);
		    		$("#popup_div").append(texonomy_form);
		    		init_map();
		    		$("#col-container #col-left").hide();
		    		$("#col-container #col-left").html('');
		    		remove_unwanted_parents("#popup_div select#parent");
		    		$("#popup_div select#parent").change(function () {
		    		    geocode_address($(this).val(), map);
		    		});
		    		return false;
		    	    });

		    	    $("#submit").click(function () {
		    		  var location_name = $("#tag-name").val();
				  if(location_name != ''){
				    setTimeout(function(){ $("#popup_div").slideUp('slow'); }, 2000);
				  }
				 
		    	    });
		    	    $(document).on('click', '#close_div', function () {
		    		$("#popup_div").slideUp();
		    	    });
		    	    var map_selector = "#popup_div #map";
		    	    if ($("#map-for-coordinates").length > 0) {
		    		map_selector = "#map-for-coordinates";
		    		init_map();
		    	    }
		    	    if ($("#foodbakery_locations_image_meta").length > 0) {
		    		remove_unwanted_parents("select#parent");
		    	    }
		    	    function remove_unwanted_parents(selector) {
		    		if (allowed_location_levels == 0) {
		    		    $(selector).prop('disabled', true);
		    		}
		    		$(selector + " option").each(function (key, elem) {
		    		    var elem_class = $(elem).attr('class');
		    		    if (typeof elem_class != "undefined") {
		    			var parts = elem_class.split("-");
		    			var selected_level = parseInt(parts[1]);
		    			if (selected_level >= allowed_location_levels) {
		    			    $(elem).remove();
		    			}
		    		    }
		    		});
		    	    }

		    	    // Map logic begins here.
		    	    function init_map() {
		    		geocoder = new google.maps.Geocoder();
		    		map = new google.maps.Map($(map_selector)[0], {
		    		    center: {lat: default_map_latitude, lng: default_map_longitude},
		    		    zoom: map_zoom_level,
		    		    streetViewControl: false,
		    		    fullscreenControl: true,
		    		});

		    		if (drawing_tools == true) {
		    		    var centerControlDiv = document.createElement('div');
		    		    var controlBar1 = new ControlBar(centerControlDiv, map);
		    		    controlBar1.index = 1;
		    		    map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);
		    		}

		    		if (map_selector == "#map-for-coordinates") {
		    		    var jsonCoords = $("#foodbakery_location_coordinates").val();
		    		    if (jsonCoords == "") {
		    			return;
		    		    }
		    		    // Define the LatLng coordinates for the polygon's path.
		    		    var polygonCoords = $.parseJSON(jsonCoords);

		    		    // Construct the polygon.
		    		    var polygon = new google.maps.Polygon({
		    			paths: polygonCoords,
		    			strokeColor: border_color,
		    			strokeOpacity: 0.8,
		    			strokeWeight: 2,
		    			fillColor: fill_color,
		    			fillOpacity: 0.35,
		    			editable: true,
		    		    });
		    		    polygon.setMap(map);
		    		    var poly_center = get_polygon_center(polygonCoords);
		    		    map.setCenter(poly_center);
		    		    var zoom = $("#foodbakery_location_zoom_level").val();

		    		    if (zoom != "") {
		    			map.setZoom(parseInt(zoom));
		    		    }
		    		    polygon.getPaths().forEach(function (path, index) {
		    			google.maps.event.addListener(path, 'set_at', function () {
		    			    // Point was moved.
		    			    var polygon = markersArray[0];
		    			    save_coordinates(polygon);
		    			});
		    		    });
		    		    markersArray.push(polygon);
		    		}

		    		// On zoom level change.
		    		google.maps.event.addListener(map, 'zoom_changed', function () {
		    		    $("#foodbakery_location_zoom_level").val(map.getZoom());
		    		});

		    		if ($("#foodbakery_locations_image_meta").length > 0) {
		    		    $("select#parent").change(function () {
		    			geocode_address($(this).val(), map);
		    		    });

		    		}
		    	    }



		    	    function save_coordinates(polygon, map) {
		    		var vertices = polygon.getPath();
		    		var contentString = '';
		    		var coordinates = [];
		    		// Iterate over the vertices.
		    		for (var i = 0; i < vertices.getLength(); i++) {
		    		    var xy = vertices.getAt(i);
		    		    coordinates.push({"lat": xy.lat(), "lng": xy.lng()});

		    		}
		    		$("#foodbakery_location_coordinates").val(JSON.stringify(coordinates));
		    		$("#foodbakery_location_zoom_level").val(map.getZoom());
		    	    }

		    	    function ControlBar(controlDiv, map) {
		    		add_search_box(controlDiv, map);

		    		//                                    add_draw_freehand_button(controlDiv, map);

		    		add_draw_polygon_button(controlDiv, map);

		    		add_reset_button(controlDiv, map);
		    	    }

		    	    function add_reset_button(controlDiv, map) {
		    		// Reset Button
		    		var controlUI1 = $("<span>");
		    		controlCSS["title"] = "Clear map.";
		    		controlUI1.css(controlCSS).html("Reset");
		    		$(controlDiv).append(controlUI1);
		    		controlUI1.click(function (e) {
		    		    e.preventDefault();

		    		    clearOverlays();
		    		});
		    	    }



		    	    function add_draw_polygon_button(controlDiv, map) {
		    		// Draw freehand button
		    		var controlUI = $("<span>");
		    		controlCSS["title"] = "Use me to select an area for a location.";
		    		controlUI.css(controlCSS).html("Draw Polygon");
		    		$(controlDiv).append(controlUI);
		    		// Setup the click event listeners: simply set the map to Chicago.
		    		controlUI.click(function (e) {

		    		    clearOverlays();
		    		    e.preventDefault();

		    		    var drawingManager = new google.maps.drawing.DrawingManager({
		    			drawingMode: google.maps.drawing.OverlayType.POLYGON,
		    			drawingControl: false,
		    			drawingControlOptions: {
		    			    position: google.maps.ControlPosition.TOP_RIGHT,
		    			    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
		    			},
		    			polygonOptions: {
		    			    fillColor: fill_color,
		    			    fillOpacity: 0.8,
		    			    strokeColor: border_color,
		    			    strokeWeight: 2,
		    			    clickable: true,
		    			    zIndex: 1,
		    			    editable: true,
		    			}
		    		    });

		    		    drawingManager.setMap(map);

		    		    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
		    			if (e.type != google.maps.drawing.OverlayType.MARKER) {
		    			    drawingManager.setDrawingMode(null);
		    			}

		    			newShape = e.overlay;
		    			save_coordinates(newShape, map);
		    			markersArray.push(newShape);

		    		    });
		    		});
		    	    }

		    	    function add_search_box(controlDiv, map) {
		    		var controlCSS = {
		    		    "background-color": "#fff",
		    		    "color": "#555",
		    		    "bax-shadow": "0px 1px 4px -1px rgba(0, 0, 0, 0.3)",
		    		    "cursor": "pointer",
		    		    "margin-bottom": "10px",
		    		    "margin-top": "10px",
		    		    "margin-right": "10px",
		    		    "text-align": "left",
		    		    "font-family": "Roboto,Arial,sans-serif",
		    		    "font-size": "11px",
		    		    "padding": "8px",
		    		    "display": "inline-block",
		    		};
		    		var searchUI = $('<input id="search-location" class="controls" type="text" placeholder="Search..." onkeypress="return event.keyCode != 13;">');
		    		searchUI.css(controlCSS);
		    		$(controlDiv).append(searchUI);
		    		var searchBox = new google.maps.places.SearchBox(searchUI[0]);
		    		map.controls[ google.maps.ControlPosition.TOP_LEFT ].push(searchUI[0]);

		    		// Bias the SearchBox results towards current map's viewport.
		    		map.addListener('bounds_changed', function () {
		    		    searchBox.setBounds(map.getBounds());
		    		});

		    		var markers = [];
		    		// Listen for the event fired when the user selects a prediction and retrieve
		    		// more details for that place.
		    		searchBox.addListener('places_changed', function () {
		    		    var places = searchBox.getPlaces();

		    		    if (places.length == 0) {
		    			return;
		    		    }

		    		    // Clear out the old markers.
		    		    markers.forEach(function (marker) {
		    			marker.setMap(null);
		    		    });
		    		    markers = [];

		    		    // For each place, get the icon, name and location.
		    		    var bounds = new google.maps.LatLngBounds();
		    		    places.forEach(function (place) {
		    			if (!place.geometry) {
		    			    console.log("Returned place contains no geometry");
		    			    return;
		    			}

		    			if (place.geometry.viewport) {
		    			    // Only geocodes have viewport.
		    			    bounds.union(place.geometry.viewport);
		    			} else {
		    			    bounds.extend(place.geometry.location);
		    			}
		    		    });
		    		    map.fitBounds(bounds);
		    		});
		    	    }

		    	    function clearOverlays() {
		    		for (var i = 0; i < markersArray.length; i++) {
		    		    markersArray[i].setMap(null);
		    		}
		    		markersArray.length = 0;
		    	    }

		    	    function disable(map) {
		    		map.setOptions({
		    		    draggable: false,
		    		    zoomControl: false,
		    		    scrollwheel: false,
		    		    disableDoubleClickZoom: false
		    		});
		    	    }

		    	    function enable(map) {
		    		map.setOptions({
		    		    draggable: true,
		    		    zoomControl: true,
		    		    scrollwheel: true,
		    		    disableDoubleClickZoom: true
		    		});
		    	    }

		    	    function get_polygon_center(polyCoords) {
		    		var bounds = new google.maps.LatLngBounds();
		    		var i;
		    		for (i = 0; i < polyCoords.length; i++) {
		    		    bounds.extend(new google.maps.LatLng(polyCoords[i]["lat"], polyCoords[i]["lng"]));
		    		}

		    		return bounds.getCenter();
		    	    }

		    	    function geocode_address(address, map) {
		    		geocoder.geocode({address: address}, function (results, status) {
		    		    if (status == google.maps.GeocoderStatus.OK) {
		    			map.setCenter(results[0].geometry.location);//center the map over the result
		    		    } else {
		    			alert('Geocode was not successful for the following reason: ' + status);
		    		    }
		    		});
		    	    }
		    	});
		        })(jQuery);
		    </script>
		    <?php
		}
	    }
	}

	/**
	 * Start Function How to create coloumes of post and theme
	 */
	public function locations_theme_columns_callback($theme_columns) {
	    $new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __('Name', 'foodbakery'),
		'type' => __('Type', 'foodbakery'),
		'iso_code' => __('ISO', 'foodbakery'),
		'slug' => __('Slug', 'foodbakery'),
		'posts' => __('Restaurant Counts', 'foodbakery'),
		'count' => __('Restaurant Counts', 'foodbakery'),
	    );
	    unset($new_columns['posts']);
	    return $new_columns;
	}

	public function locations_theme_columns_content_callback($content, $column_name, $term_id) {
	    if ('type' == $column_name) {
		$ancestors = get_ancestors($term_id, Foodbakery_Locations::$taxonomy_name);

            $selected_levels = "country,state,city,town";
            $flag = apply_filters('foodbakery_add_county_in_location_level', false);
            if($flag){
                $selected_levels = "country,state,county,city,town";
            }

		$selected_levels = explode(',', get_option('location_selected_levels', $selected_levels));

		$content = ucfirst($selected_levels[count($ancestors)]);
	    }
	    if ('iso_code' == $column_name) {

                $content = get_term_meta( $term_id, 'iso_code', true );

	    }
	    
	    if ('count' == $column_name) {
				
		$term = get_term( $term_id, Foodbakery_Locations::$taxonomy_name);
		$slug = $term->slug;
		$name = $term->name;
		
		$args = array(
                        'posts_per_page' => "-1",
                        'post_type' => 'restaurants',
                        'post_status' => 'publish',
                        'meta_query' => array(
                            'relation' => 'OR',
                            array(
                                'key' => 'foodbakery_post_loc_country_restaurant',
                                'value' => $slug,
                                'compare' => '=',
                            ),
			                array(
                                'key' => 'foodbakery_post_loc_state_restaurant',
                                'value' => $slug,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_post_loc_city_restaurant',
                                'value' => $slug,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_post_loc_town_restaurant',
                                'value' => $slug,
                                'compare' => '=',
                            ),
                        ),
                    );
				    
		   $my_query = new WP_Query( $args );
					
                   $content =  $count_rest = count( $my_query->posts );
                    wp_reset_postdata();
		
	    
	    }
		 return $content;

	}
	/**
	 * how to save location in restaurant fields
	 */
	public function save_locations_fields_added_callback($term_id) {
	    if (isset($_POST['locations_image_meta']) and $_POST['locations_image_meta'] == '1') {
		if (isset($_POST['iso_code'])) {
		    $iso_code = $_POST['iso_code'];
		    add_term_meta($term_id, 'iso_code', $iso_code, true);
		}

		if (isset($_POST['location-coordinates'])) {
		    $location_coordinates = $_POST['location-coordinates'];
		    add_term_meta($term_id, 'location_coordinates', $location_coordinates, true);
		}

		if (isset($_POST['location-zoom-level'])) {
		    $location_zoom_level = $_POST['location-zoom-level'];
		    add_term_meta($term_id, 'location_zoom_level', $location_zoom_level, true);
		}
	    }
	}

	/**
	 * how to update location in restaurant fields
	 */
	public function save_locations_fields_updated_callback($term_id) {
	    if (isset($_POST['locations_image_meta']) and $_POST['locations_image_meta'] == '1') {
		if (isset($_POST['iso_code'])) {
		    $iso_code = $_POST['iso_code'];
		    update_term_meta($term_id, 'iso_code', $iso_code);
		}

		if (isset($_POST['location-coordinates'])) {
		    $location_coordinates = $_POST['location-coordinates'];
		    update_term_meta($term_id, 'location_coordinates', $location_coordinates);
		}

		if (isset($_POST['location-zoom-level'])) {
		    $location_zoom_level = $_POST['location-zoom-level'];
		    update_term_meta($term_id, 'location_zoom_level', $location_zoom_level);
		}
	    }
	}

	/**
	 * Add ISO Code field.
	 *
	 * @global type $foodbakery_form_fields
	 * @param type $tag
	 */
	public function edit_locations_fields_callback($tag) { //check for existing featured ID
	    global $foodbakery_form_fields;
	    $iso_code = "";
	    $location_coordinates = "";
	    if (isset($tag->term_id)) {
		$term_id = $tag->term_id;

		$iso_code = get_term_meta($term_id, 'iso_code', true);
		$location_coordinates = get_term_meta($term_id, 'location_coordinates', true);
		$location_zoom_level = get_term_meta($term_id, 'location_zoom_level', true);
	    }

	    $foodbakery_opt_array = array(
		'id' => 'locations_image_meta',
		'std' => "1",
		'cust_id' => "",
		'cust_name' => "locations_image_meta",
		'return' => false,
	    );
	    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
	    ?>
	    <tr>
	        <th><label for="cat_f_img_url"> <?php _e("ISO Code", 'foodbakery'); ?></label></th>
	        <td>
		    <?php
		    $foodbakery_opt_array = array(
			'id' => '',
			'std' => esc_attr($iso_code),
			'cust_id' => "iso_code",
			'cust_name' => "iso_code",
			'return' => false,
		    );
		    $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
		    ?>
	        </td>
	    </tr>
	    <tr>
	        <th><label for="cat_f_img_url"> <?php _e("Location Coordinates", 'foodbakery'); ?></label></th>
	        <td>
		    <?php
		    $foodbakery_opt_array = array(
			'id' => 'location_coordinates',
			'std' => esc_attr($location_coordinates),
			'cust_id' => "",
			'cust_name' => "location-coordinates",
			'return' => false,
		    );
		    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

		    $foodbakery_opt_array = array(
			'id' => 'location_zoom_level',
			'std' => esc_attr($location_zoom_level),
			'cust_id' => "",
			'cust_name' => "location-zoom-level",
			'return' => false,
		    );
		    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
		    ?>
	    	<div id="map-for-coordinates" style="height: 350px;"></div>
	        </td>
	    </tr>
	    <?php
	}

	/**
	 * Add Category Fields.
	 *
	 * @global type $foodbakery_form_fields
	 * @param type $tag
	 */
	public function locations_fields_callback($tag) { //check for existing featured ID
	    global $foodbakery_form_fields;
	    if (isset($tag->term_id)) {
		$t_id = $tag->term_id;
	    } else {
		$t_id = '';
	    }
	    $locations_image = '';
	    $iso_code = '';
	    ?>
	    <div class="form-field">

	        <label><?php _e("ISO Code", 'foodbakery'); ?></label>
	        <ul class="form-elements" style="margin:0; padding:0;">
	    	<li class="to-field" style="width:100%;">
			<?php
			$foodbakery_opt_array = array(
			    'id' => '',
			    'std' => "",
			    'cust_id' => "iso_code",
			    'cust_name' => "iso_code",
			    'return' => false,
			);
			$foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
			?>
	    	</li>
	        </ul>
	        <br> <br>
	    </div>
	    <input type="hidden" value="" name="location-coordinates" id="foodbakery_location_coordinates">
	    <input type="hidden" value="" name="location-zoom-level" id="foodbakery_location_zoom_level">
	    <div id="map" style="height: 150px;"></div>

	    <?php
	    $foodbakery_opt_array = array(
		'id' => 'locations_image_meta',
		'std' => "1",
		'cust_id' => "",
		'cust_name' => "locations_image_meta",
		'return' => false,
	    );
	    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
	}

	/**
	 * A filter which returns locations fields data for general usage at 
	 * backend as well as front end.
	 *
	 * @param array $data
	 * @param string $control_id
	 * @return array
	 */
	public function get_locations_fields_data_callback($data, $control_id) {

	    $foodbakery_plugin_options = get_option('foodbakery_plugin_options');

	    $location_levels = array();

        $flag = apply_filters('foodbakery_add_county_in_location_level', false);
        if($flag){
            $foodbakery_plugin_options['foodbakery_locations_levels'] = unserialize(get_option('foodbakery_locations_levels'));
        }

        if (isset($foodbakery_plugin_options['foodbakery_locations_levels'])) {
		$location_levels = $foodbakery_plugin_options['foodbakery_locations_levels'];
	    }

	    $selected_location_fields = array();
	    if (isset($foodbakery_plugin_options[$control_id])) {
		$selected_location_fields = $foodbakery_plugin_options[$control_id];
	    }

	    $country_id = 'all';
	    if (isset($data['selected']['country'])) {
		$country_id = $data['selected']['country'];
	    }

	    $state_id = 'all';
	    if (isset($data['selected']['state'])) {
		$state_id = $data['selected']['state'];
	    }

	    /*for county*/
        $flag = apply_filters('foodbakery_add_county_in_location_level', false);
	    if($flag){
            $county_id = 'all';
            if (isset($data['selected']['county'])) {
                $county_id = $data['selected']['county'];
            }
	    }

	    $city_id = 'all';
	    if (isset($data['selected']['city'])) {
		$city_id = $data['selected']['city'];
	    }

	    $town_id = 'all';
	    if (isset($data['selected']['town'])) {
		$town_id = $data['selected']['town'];
	    }

	    // Remove extra fields.
	    foreach ($data['data'] as $key => $val) {
		if (array_search($key, $selected_location_fields) === false) {
		    unset($data['data'][$key]);
		}
	    }

	    // Get data for each field.
	    foreach ($selected_location_fields as $key => $type) {
		$location_level = array_search($type, $location_levels);
		if ($location_level === false) {
		    continue;
		}
            if($flag){
		if ('country' == $type) {
		    $terms = $this->get_terms($location_level, $country_id);
		    $data['data']['country'] = $terms;
		} else if ('state' == $type) {
		    $var_name = $this->get_previous_location_field('state', $selected_location_fields);
		    $var_name .= "_id";
		    $terms = $this->get_terms($location_level, $$var_name);
		    $data['data']['state'] = $terms;
		} else if ('county' == $type) {
		    $var_name = $this->get_previous_location_field('county', $selected_location_fields);
		    $var_name .= "_id";
		    $terms = $this->get_terms($location_level, $$var_name);
		    $data['data']['county'] = $terms;
		} else if ('city' == $type) {
		    $var_name = $this->get_previous_location_field('city', $selected_location_fields);
		    $var_name .= "_id";
		    $terms = $this->get_terms($location_level, $$var_name);
		    $data['data']['city'] = $terms;
		} else if ('town' == $type) {
		    $var_name = $this->get_previous_location_field('town', $selected_location_fields);
		    $var_name .= "_id";
		    $terms = $this->get_terms($location_level, $$var_name);
		    $data['data']['town'] = $terms;
		}
		}else{
                if ('country' == $type) {
                    $terms = $this->get_terms($location_level, $country_id);
                    $data['data']['country'] = $terms;
                } else if ('state' == $type) {
                    $var_name = $this->get_previous_location_field('state', $selected_location_fields);
                    $var_name .= "_id";
                    $terms = $this->get_terms($location_level, $$var_name);
                    $data['data']['state'] = $terms;
                }else if ('city' == $type) {
                    $var_name = $this->get_previous_location_field('city', $selected_location_fields);
                    $var_name .= "_id";
                    $terms = $this->get_terms($location_level, $$var_name);
                    $data['data']['city'] = $terms;
                } else if ('town' == $type) {
                    $var_name = $this->get_previous_location_field('town', $selected_location_fields);
                    $var_name .= "_id";
                    $terms = $this->get_terms($location_level, $$var_name);
                    $data['data']['town'] = $terms;
                }
            }

	    }

	    foreach ($location_levels as $key => $level) {
		$data['location_levels'][$level] = $key;
	    }

	    return $data;
	}

	/**
	 * Get previous location field from available location fields.
	 *
	 * @param string $this_field
	 * @param array $available_locations
	 * @return string
	 */
	public function get_previous_location_field($this_field, $available_locations) {
	    $index = array_search($this_field, $available_locations);
	    if ($index != false) {
		if ($index - 1 > -1) {
		    return $available_locations[$index - 1];
		}
	    }
	    return current($available_locations);
	}

	/**
	 * Get terms by term level.
	 *
	 * @param string $term_level
	 * @param string $term_selector
	 * @return array
	 */
	public function get_terms($term_level, $term_selector, $is_selector_parent = true, $other_attributes = true) {
	    // If term selector is string then make it id of the term.
	    if (is_string($term_selector) && $term_selector != 'all') {
		$term = get_term_by('slug', $term_selector, Foodbakery_Locations::$taxonomy_name);
		if ($term !== false) {
		    $term_selector = $term->term_id;
		}
	    }

	    $args = array('taxonomy' => Foodbakery_Locations::$taxonomy_name, 'hide_empty' => 0);
	    // If only first level locations required(they don't have parents).
	    if ($term_level == 0) {
		if ($is_selector_parent === false) {
		    $args['include'] = array($term_selector);
		} else {
		    $args['parent'] = 0;
		}
		$terms = get_terms($args);
	    } else { // Else get all terms and then filter out with respect to requested level.
		if ($is_selector_parent === false) {
		    $args['include'] = array($term_selector);
		}
		$terms = get_terms($args);
		// To get all childrens of that level.
		$filter_callback = function ( $term ) {
		    return $term->parent != 0 && count(get_ancestors($term->term_id, $term->taxonomy)) == 1;
		};
		// To get childrens of a specific parent for level 2.
		if ($term_level == 2) {
		    $filter_callback = function ( $term ) {
			return $term->parent != 0 && count(get_ancestors($term->term_id, $term->taxonomy)) == 2;
		    };
		} else if ($term_level == 3) { // To get childrens of a specific parent for level 3.
		    $filter_callback = function ( $term ) {
			return $term->parent != 0 && count(get_ancestors($term->term_id, $term->taxonomy)) == 3;
		    };
		}
		// Filter out items according to filter.
		$terms = array_filter($terms, $filter_callback);
	    }

	    $data = array();
	    foreach ($terms as $key => $term) {
		$term_data = array();
		$keep_this_term = true;
		if ($term_level != 0) {
		    if ($is_selector_parent == false) {
			$keep_this_term = true;
		    } else if ($term_selector == 'all') {
			$keep_this_term = true;
		    } else if (in_array($term_selector, get_ancestors($term->term_id, $term->taxonomy))) {
			$keep_this_term = true;
		    } else {
			$keep_this_term = false;
		    }
		}
		if ($keep_this_term === true) {
		    $term_data['id'] = $term->term_id;
		    $term_data['slug'] = $term->slug;
		    $term_data['name'] = $term->name;

		    if ($other_attributes === true) {
			$term_data['iso_code'] = get_term_meta($term->term_id, 'iso_code', true);
			$term_data['location_coordinates'] = get_term_meta($term->term_id, 'location_coordinates', true);
			$term_data['location_zoom_level'] = get_term_meta($term->term_id, 'location_zoom_level', true);
		    }
		    $data[] = $term_data;
		}
	    }

	    return $data;
	}

	/**
	 * Get Locations for Location field in search form at front-end.
	 */
	public function get_locations_for_search_callback() {
	    global $foodbakery_plugin_options;
	    $error = false;
	    $control_prefix = 'foodbakery_locations_fields_selector_for_search_form_';

        $flag = apply_filters('foodbakery_add_county_in_location_level', false);
        if($flag){
            $foodbakery_plugin_options['foodbakery_locations_levels'] = unserialize(get_option('foodbakery_locations_levels'));
        }

	    $selected_levels = isset($foodbakery_plugin_options['foodbakery_locations_levels']) ? $foodbakery_plugin_options['foodbakery_locations_levels'] : array();
	    $selected_location_parts = isset($foodbakery_plugin_options['locations_fields_selector_for_search_form']) ? $foodbakery_plugin_options['locations_fields_selector_for_search_form'] : array();
	    // Calculate possible number of filters.
	    $possible_filters_count = array_search($selected_location_parts[count($selected_location_parts) - 1], $selected_levels);
	    if ($possible_filters_count === false) {
		$error = true;
	    }
	    $possible_filters_count += 1;

	    $location_list = array();
	    $location_titles = array();
	    $location_levels_to_show = array(false, false, false, false);

	    // Load level 1.
	    if ($possible_filters_count > 0 && $error === false) {
		$level_title = $selected_levels[0];
		$selector_index = $control_prefix . $level_title;
		$term_selector = ( isset($foodbakery_plugin_options[$selector_index]) && $foodbakery_plugin_options[$selector_index] != '-' ) ? $foodbakery_plugin_options[$selector_index] : 'all';
		$term_level = array_search($level_title, $selected_levels);
		if ($term_level !== false) {
		    if ($term_selector === 'all') {
			$is_selector_parent = true;
		    } else {
			$is_selector_parent = false;
		    }
		    $level_1_terms = $this->get_terms($term_level, $term_selector, $is_selector_parent, false);
		    $location_list[] = $level_1_terms;
		    if (array_search($level_title, $selected_location_parts) !== false) {
			$location_titles[] = ucfirst($level_title);
			$location_levels_to_show[0] = true;
		    }
		} else {
		    $error = true;
		}
	    }
	    // Load level 2.
	    $term_selector_temp = 'all';
	    if ($possible_filters_count > 1 && $error === false) {
		$level_title = $selected_levels[1];
		$selector_index = $control_prefix . $level_title;
		$term_selector = ( isset($foodbakery_plugin_options[$selector_index]) && $foodbakery_plugin_options[$selector_index] != '-' ) ? $foodbakery_plugin_options[$selector_index] : 'all';
		$term_level = array_search($level_title, $selected_levels);
		if ($term_level !== false) {
		    $level_2_terms_temp = array();
		    foreach ($location_list[0] as $key => $parent_location) {
			// If term selector for second level is 'all' then use parent's id as term selector.
			if ($term_selector === 'all') {
			    $term_selector_temp = $parent_location['id'];
			    $is_selector_parent = true;
			} else {
			    $term_selector_temp = $term_selector;
			    $is_selector_parent = false;
			}

			$level_2_terms_temp[] = $this->get_terms($term_level, $term_selector_temp, $is_selector_parent, false);
		    }
		    $location_list[] = $level_2_terms_temp;
		    if (array_search($level_title, $selected_location_parts) !== false) {
			$location_titles[] = ucfirst($level_title);
			$location_levels_to_show[1] = true;
		    }
		} else {
		    $error = true;
		}
	    }

	    // Load level 3.
	    $term_selector_temp = 'all';
	    if ($possible_filters_count > 2 && $error === false) {
		$level_title = $selected_levels[2];
		$selector_index = $control_prefix . $level_title;
		$term_selector = ( isset($foodbakery_plugin_options[$selector_index]) && $foodbakery_plugin_options[$selector_index] != '-' ) ? $foodbakery_plugin_options[$selector_index] : 'all';
		$term_level = array_search($level_title, $selected_levels);
		if ($term_level !== false) {
		    $level_3_terms_temp = array();
		    foreach ($location_list[1] as $key => $parent_location) {
			$level_3_nested_terms_temp = array();
			foreach ($parent_location as $key => $nested_parent_location) {
			    // If term selector for second level is 'all' then use parent's id as term selector.
			    if ($term_selector === 'all') {
				$term_selector_temp = $nested_parent_location['id'];
				$is_selector_parent = true;
			    } else {
				$term_selector_temp = $term_selector;
				$is_selector_parent = false;
			    }
			    $level_3_nested_terms_temp[] = $this->get_terms($term_level, $term_selector_temp, $is_selector_parent, false);
			}
			$level_3_terms_temp[] = $level_3_nested_terms_temp;
		    }
		    $location_list[] = $level_3_terms_temp;
		    if (array_search($level_title, $selected_location_parts) !== false) {
			$location_titles[] = ucfirst($level_title);
			$location_levels_to_show[2] = true;
		    }
		} else {
		    $error = true;
		}
	    }

	    // Load level 4.
	    $term_selector_temp = 'all';
	    if ($possible_filters_count > 3 && $error === false) {
		$level_title = $selected_levels[3];
		$selector_index = $control_prefix . $level_title;
		$term_selector = ( isset($foodbakery_plugin_options[$selector_index]) && $foodbakery_plugin_options[$selector_index] != '-' ) ? $foodbakery_plugin_options[$selector_index] : 'all';
		$term_level = array_search($level_title, $selected_levels);
		if ($term_level !== false) {
		    $level_4_terms_temp = array();
		    foreach ($location_list[2] as $key => $parent_location) {
			$level_4_nested_terms_temp = array();
			foreach ($parent_location as $key => $nested_parent_location) {
			    $level_4_nested_nested_terms_temp = array();
			    foreach ($nested_parent_location as $key => $nested_nested_parent_location) {
				// If term selector for second level is 'all' then use parent's id as term selector.
				if ($term_selector === 'all') {
				    $term_selector_temp = $nested_nested_parent_location['id'];
				    $is_selector_parent = true;
				} else {
				    $term_selector_temp = $term_selector;
				    $is_selector_parent = false;
				}
				$level_4_nested_nested_terms_temp[] = $this->get_terms($term_level, $term_selector_temp, $is_selector_parent, false);
			    }
			    $level_4_nested_terms_temp[] = $level_4_nested_nested_terms_temp;
			}
			$level_4_terms_temp[] = $level_4_nested_terms_temp;
		    }
		    $location_list[] = $level_4_terms_temp;
		    if (array_search($level_title, $selected_location_parts) !== false) {
			$location_titles[] = ucfirst($level_title);
			$location_levels_to_show[3] = true;
		    }
		} else {
		    $error = true;
		}
	    }
	    $keyword = '';
	    if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
		$keyword = strtolower($_REQUEST['location']);
	    }

	    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
		$keyword = strtolower($_REQUEST['keyword']);
	    }

	    $locations_for_display = array();
	    $levels = count($location_list);
	    foreach ($location_list[0] as $key => $val1) {

		if ($this->startsWith(strtolower($val1['name']), $keyword) === true) {
		    $locations_for_display[$key] = array('item' => $val1, 'children' => array());
		}
	    }

	    if (count($locations_for_display) > 0 && $levels > 1) {
		foreach ($location_list[1] as $key1 => $val1) {
		    $locations = array();
		    foreach ($val1 as $key2 => $val2) {
			if ($this->startsWith(strtolower($val2['name']), $keyword) === true) {
			    $locations[] = array('item' => $val2, 'children' => array());
			}
		    }
		    $locations_for_display[$key1]['children'] = $locations;
		}
	    }

	    if ($levels > 2) {
		foreach ($location_list[2] as $key1 => $val1) {
		    foreach ($val1 as $key2 => $val2) {
			$locations = array();
			foreach ($val2 as $key3 => $val3) {
			    if ($this->startsWith(strtolower($val3['name']), $keyword) === true) {
				$locations[] = array('item' => $val3, 'children' => array());
			    }
			}
			$locations_for_display[$key1]['children'][$key2]['children'] = $locations;
		    }
		}
	    }

	    if ($levels > 3) {
		foreach ($location_list[3] as $key1 => $val1) {
		    foreach ($val1 as $key2 => $val2) {
			foreach ($val2 as $key3 => $val3) {
			    $locations = array();
			    foreach ($val3 as $key4 => $val4) {
				if ($this->startsWith(strtolower($val4['name']), $keyword) === true) {
				    $locations[] = array('item' => $val4, 'children' => array());
				}
			    }
			    $locations_for_display[$key1]['children'][$key2]['children'][$key3]['children'] = $locations;
			}
		    }
		}
	    }
	    echo json_encode(array(
		'title' => $location_titles,
		'location_levels_to_show' => $location_levels_to_show,
		'location_list' => $location_list,
		'locations_for_display' => $locations_for_display,
	    ));
	    wp_die();
	}

	public function startsWith($haystack, $needle) {
	    // search backwards starting from haystack length characters from the end
	    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}

	/**
	 * AJAX handler for get locations on frontend.
	 */
	public function get_locations_list_callback() {
	    $term_name = Foodbakery_Locations::$taxonomy_name;
	    $result = array();
	    $error = true;
	    $msg = '';
	    $data = array();

	    if (isset($_POST['security']) && !wp_verify_nonce($_POST['security'], 'get_locations_list')) {
		$msg = __('Authentication Failed.', 'foodbakery');
	    } else {
		if (!( isset($_POST['location_type']) && isset($_POST['location_level']) && isset($_POST['selector']) )) {
		    $msg = __('Invalid Data.', 'foodbakery');
		} else {
		    $location_type = $_POST['location_type'];
		    $location_level = $_POST['location_level'];
		    $selector = $_POST['selector'];
		    // If term selector is string then make it id of the term.
		    if (is_string($selector)) {
			$term = get_term_by('slug', $selector, $term_name);
			if ($term !== false) {
			    $selector = $term->term_id;
			    $location_coordinates = get_term_meta($selector, 'location_coordinates', true);
			}
		    }
		    $data = $this->get_terms1($term_name, $location_level, $selector);
		    $error = false;
		}
	    }
	    $result['error'] = $error;
	    $result['msg'] = $msg;
	    $result['data'] = $data;
	    if (isset($location_coordinates) && !empty($location_coordinates)) {
		$result['loc_coords'] = $location_coordinates;
	    } else {
		$result['loc_coords'] = '';
	    }
	    echo json_encode($result);
	    wp_die();
	}

	/**
	 * Get terms by term level.
	 */
	public function get_terms1($term_name, $term_level, $term_selector) {
	    $args = array('taxonomy' => $term_name, 'hide_empty' => 0);
	    // If only first level locations required(they don't have parents).
	    if ($term_level == 0) {
		$args['parent'] = 0;
		$terms = get_terms($args);
	    } else { // Else get all terms and then filter out with respect to requested level.
		$terms = get_terms($args);
		// To get all childrens of that level.
		$filter_callback = function ( $term ) {
		    return $term->parent != 0 && count(get_ancestors($term->term_id, $term->taxonomy)) == 1;
		};
		// To get childrens of a specific parent for level 2.
		if ($term_level == 2) {
		    $filter_callback = function ( $term ) {
			return $term->parent != 0 && count(get_ancestors($term->term_id, $term->taxonomy)) == 2;
		    };
		} else if ($term_level == 3) { // To get childrens of a specific parent for level 3.
		    $filter_callback = function ( $term ) {
			return $term->parent != 0 && count(get_ancestors($term->term_id, $term->taxonomy)) == 3;
		    };
		}
		// Filter out items according to filter.
		$terms = array_filter($terms, $filter_callback);
	    }

	    $data = array(
	    );
	    foreach ($terms as $key => $term) {
		if ($term_level != 0) {
		    if (in_array($term_selector, get_ancestors($term->term_id, $term->taxonomy))) {
			$data[$term->term_id] = array('slug' => $term->slug, 'name' => $term->name);
		    }
		} else {
		    $data[$term->term_id] = array('slug' => $term->slug, 'name' => $term->name);
		}
	    }

	    return $data;
	}

	/**
	 * Generate locations backup.
	 */
	public function generate_locations_backup_callback() {
	    global $wp_filesystem;

	    $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
	    if (false === ( $creds = request_filesystem_credentials($backup_url, '', false, false, array()) )) {
		return true;
	    }
	    if (!WP_Filesystem($creds)) {
		request_filesystem_credentials($backup_url, '', true, false, array());
		return true;
	    }

	    $terms = get_terms(Foodbakery_Locations::$taxonomy_name, array('hide_empty' => 0));

	    $terms_arr = array();
	    $terms_str = 'Name,Parent,ISO Code,Area Coordinates,Zoom Level' . PHP_EOL;
	    foreach ($terms as $key => $term) {
		$term_arr = array();
		$term_arr[] = $term->name;
		$parent_term = get_term($term->parent, Foodbakery_Locations::$taxonomy_name);
		if ($parent_term != null) {
		    $term_arr[] = $parent_term->name;
		} else {
		    $term_arr[] = "";
		}
		$term_arr[] = get_term_meta($term->term_id, 'iso_code', true);
		$location_coordinates = get_term_meta($term->term_id, 'location_coordinates', true);
		$term_arr[] = str_replace('"', '\'', $location_coordinates);
		$term_arr[] = get_term_meta($term->term_id, 'location_zoom_level', true);
		$terms_str .= '"' . implode('","', $term_arr) . '"' . PHP_EOL;
	    }
	    $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/locations/';
	    $foodbakery_filename = trailingslashit($foodbakery_upload_dir) . ( current_time('d-M-Y_H.i.s') ) . '.csv';

	    if (!$wp_filesystem->put_contents($foodbakery_filename, $terms_str, FS_CHMOD_FILE)) {
		echo __("Error saving file!", 'foodbakery');
	    } else {
		echo __("Backup Generated.", 'foodbakery');
	    }
	    wp_die();
	}

	/**
	 * Delete selected locations back file using AJAX.
	 */
	public function delete_locations_backup_file_callback() {
	    global $wp_filesystem;
	    $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
	    if (false === ( $creds = request_filesystem_credentials($backup_url, '', false, false, array()) )) {
		return true;
	    }
	    if (!WP_Filesystem($creds)) {
		request_filesystem_credentials($backup_url, '', true, false, array());
		return true;
	    }
	    $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/locations/';

	    $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';
	    $foodbakery_filename = trailingslashit($foodbakery_upload_dir) . $file_name;
	    if (is_file($foodbakery_filename)) {
		unlink($foodbakery_filename);
		printf(__("File '%s' Deleted Successfully", 'foodbakery'), $file_name);
	    } else {
		echo __("Error Deleting file!", 'foodbakery');
	    }
	    die();
	}

	public function foodbakery_location_upload_foodbakery($dir) {
	    return array(
		'path' => $dir['basedir'] . '/location',
		'url' => $dir['baseurl'] . '/location',
		'subdir' => '/location',
		    ) + $dir;
	}

	/**
	 * Uploading File
	 */
	public function foodbakery_uploading_import_file_callback() {
	    global $wp_filesystem;
	    add_filter('upload_dir', array($this, 'foodbakery_location_upload_foodbakery'));
	    $uploadedfile = $_FILES['foodbakery_btn_browse_locations_file'];
	    $upload_overrides = array('test_form' => false);
	    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

	    if ($movefile && !isset($movefile['error'])) {
		echo esc_url($movefile['url']);
	    }
	    remove_filter('upload_dir', array($this, 'foodbakery_location_upload_foodbakery'));
	    wp_die();
	}

	/**
	 * Restore location from backup file or URL.
	 */
	public function restore_locations_backup_callback() {
	    global $wp_filesystem;

	    $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
	    if (false === ( $creds = request_filesystem_credentials($backup_url, '', false, false, array()) )) {
		return true;
	    }
	    if (!WP_Filesystem($creds)) {
		request_filesystem_credentials($backup_url, '', true, false, array());
		return true;
	    }
	    $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/locations/';
	    $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';
	    $file_path = isset($_POST['file_path']) ? $_POST['file_path'] : '';
	    if ($file_path == 'yes') {
		$foodbakery_file_body = '';
		$foodbakery_file_response = wp_remote_get($file_name);
		if (is_array($foodbakery_file_response)) {
		    $foodbakery_file_body = isset($foodbakery_file_response['body']) ? $foodbakery_file_response['body'] : '';

		    if ($foodbakery_file_body != '') {
			$this->import_locations($foodbakery_file_body);
			_e("File Imported Successfully", 'foodbakery');
		    }
		} else {
		    _e("Error Restoring file!", 'foodbakery');
		}
	    } else {
		$foodbakery_filename = trailingslashit($foodbakery_upload_dir) . $file_name;
		if (is_file($foodbakery_filename)) {
		    $locations_file = $wp_filesystem->get_contents($foodbakery_filename);
		    $this->import_locations($locations_file);
		    printf(__("File '%s' Restored Successfully", 'foodbakery'), $file_name);
		} else {
		    _e("Error Restoring file!", 'foodbakery');
		}
	    }
	    wp_die();
	}

	public function import_locations($csv_str) {
	    wp_defer_term_counting(true);
	    wp_defer_comment_counting(true);
	    wp_suspend_cache_invalidation(true);
	    do_action('import_start');

	    $term_new_ids = array();
	    $lines = preg_split('/\r*\n+|\r+/', $csv_str);
	    $not_found = array();
	    foreach ($lines as $key => $line) {
		if (0 == $key) {
		    continue;
		}

		$parts = str_getcsv($line);
		if (count($parts) < 2) {
		    continue;
		}
		$args = array(
		    'parent' => 0,
		    'slug' => sanitize_title($parts[0]),
		    'description' => '',
		);
		if (!empty($parts[1])) {
		    if (isset($term_new_ids[$parts[1]])) {
			$args['parent'] = $term_new_ids[$parts[1]];
		    } else {
			$not_found[] = $line;
		    }
		}
		$return = wp_insert_term(
			$parts[0], // The term.
			Foodbakery_Locations::$taxonomy_name, // The taxonomy.
			$args
		);

		// Keep new ids and also import term metadata.
		if (is_array($return) && 3 < count($parts)) {
		    $term_new_ids[$parts[0]] = $return['term_id'];
		    add_term_meta($return['term_id'], 'iso_code', $parts[2], true);
		    add_term_meta($return['term_id'], 'location_coordinates', str_replace('\'', '"', $parts[3]), true);
		    add_term_meta($return['term_id'], 'location_zoom_level', $parts[4], true);
		}
	    }
	    wp_suspend_cache_invalidation(false);
	    wp_defer_term_counting(false);
	    wp_defer_comment_counting(false);
	    do_action('import_end');
	}

	public function dropdown_options_for_search_location_callback($output) {

	    // do stuff here
	}

	public function dropdown_options_for_search_location_data() {

	    global $foodbakery_plugin_options;
	    $output = '';

	    $default_locations_saved = isset($foodbakery_plugin_options['foodbakery_default_locations_list']) ? $foodbakery_plugin_options['foodbakery_default_locations_list'] : '';

	    $error = false;
	    $control_prefix = 'foodbakery_locations_fields_selector_for_search_form_';

	    $args = array(
		'hide_empty' => false,
		'fields' => 'all');

	    $terms = foodbakery_get_cached_obj('restaurant_location_cached_loop', $args, 12, true, 'get_term', Foodbakery_Locations::$taxonomy_name);
	    $terms = get_terms(Foodbakery_Locations::$taxonomy_name, array(
		'hide_empty' => false,
		'fields' => 'all'
	    ));
	    $foodbakery_tags_list = array();
	    if (is_array($terms) && sizeof($terms) > 0) {
		foreach ($terms as $dir_tag) {
		    if ($dir_tag->slug != '' && $dir_tag->name != '') {
			if (is_array($default_locations_saved) && in_array($dir_tag->slug, $default_locations_saved)) {
			    continue;
			}
			$foodbakery_tags_list[] = array('value' => $dir_tag->slug, 'caption' => $dir_tag->name);
		    }
		}
	    }
	    $data_string = json_encode($foodbakery_tags_list);
	    foodbakery_set_transient_obj('foodbakery_location_data', $data_string);
	    echo ($data_string);
	    die();
	}

	public function get_location_by_restaurant_id($restaurant_id, $formate = 'restaurant') {
	    $location_str = '';
	    $foodbakery_post_loc_address_restaurant = get_post_meta($restaurant_id, 'foodbakery_post_loc_address_restaurant', true);
	    return $foodbakery_post_loc_address_restaurant;
	}

	public function get_country_by_restaurant_id($restaurant_id, $formate = 'restaurant') {
	    $location_str = '';

	    $foodbakery_post_loc_country_restaurant = get_post_meta($restaurant_id, 'foodbakery_post_loc_country_restaurant', true);
	    if ($foodbakery_post_loc_country_restaurant) {
		$term = get_term_by('slug', $foodbakery_post_loc_country_restaurant, 'foodbakery_locations');
		if ($term) {
		    return $term->name;
		}
	    }
	}

	public function get_state_by_restaurant_id($restaurant_id, $formate = 'restaurant') {
	    $location_str = '';
	    $foodbakery_post_loc_state_restaurant = get_post_meta($restaurant_id, 'foodbakery_post_loc_state_restaurant', true);
	    if ($foodbakery_post_loc_state_restaurant) {
		$term = get_term_by('slug', $foodbakery_post_loc_state_restaurant, 'foodbakery_locations');
		if ($term) {
		    return $term->name;
		}
	    }
	}

	public function get_city_by_restaurant_id($restaurant_id, $formate = 'restaurant') {
	    $location_str = '';
	    $foodbakery_post_loc_city_restaurant = get_post_meta($restaurant_id, 'foodbakery_post_loc_city_restaurant', true);
	    if ($foodbakery_post_loc_city_restaurant) {
		$term = get_term_by('slug', $foodbakery_post_loc_city_restaurant, 'foodbakery_locations');
		if ($term) {
		    return $term->name;
		}
	    }
	}

	public function get_town_by_restaurant_id($restaurant_id, $formate = 'restaurant') {
	    $location_str = '';
	    $foodbakery_post_loc_town_restaurant = get_post_meta($restaurant_id, 'foodbakery_post_loc_town_restaurant', true);
	    if ($foodbakery_post_loc_town_restaurant) {
		$term = get_term_by('slug', $foodbakery_post_loc_town_restaurant, 'foodbakery_locations');
		if ($term) {
		    return $term->name;
		}
	    }
	}

	public function get_element_restaurant_location($restaurant_id = '', $restaurant_location_options = '') {
	    $get_restaurant_location = array();
	    if ($restaurant_id != '' && $restaurant_location_options != '') {
		$restaurant_country = $this->get_country_by_restaurant_id($restaurant_id);
		$restaurant_state = $this->get_state_by_restaurant_id($restaurant_id);
		$restaurant_city = $this->get_city_by_restaurant_id($restaurant_id);
		$restaurant_town = $this->get_town_by_restaurant_id($restaurant_id);
		$restaurant_location = $this->get_location_by_restaurant_id($restaurant_id);
		if (is_array($restaurant_location_options) && !empty($restaurant_location_options)) {
		    foreach ($restaurant_location_options as $value) {
			if ($value == 'country' && $restaurant_country != '') {
			    $get_restaurant_location[] = $restaurant_country;
			}if ($value == 'state' && $restaurant_state != '') {
			    $get_restaurant_location[] = $restaurant_state;
			}if ($value == 'city' && $restaurant_city != '') {
			    $get_restaurant_location[] = $restaurant_city;
			}if ($value == 'town' && $restaurant_town != '') {
			    $get_restaurant_location[] = $restaurant_town;
			}if ($value == 'address' && $restaurant_location != '') {
			    $get_restaurant_location[] = $restaurant_location;
			}
		    }
		}
	    }
	    return $get_restaurant_location;
	}

	public function foodbakery_get_all_locations_callback() {
	    global $foodbakery_plugin_options;
	    $response_data = '';
	    $args = array(
		'hide_empty' => false,
		'fields' => 'all',
	    );
	    $foodbakery_search_result_page = isset($foodbakery_plugin_options['foodbakery_search_result_page']) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
	    $redirecturl = isset($foodbakery_search_result_page) && $foodbakery_search_result_page != '' ? get_permalink($foodbakery_search_result_page) . '' : '';
	    $terms = foodbakery_get_cached_obj('restaurant_location_cached_loop', $args, 12, true, 'get_term', Foodbakery_Locations::$taxonomy_name);

	    $terms = array_search_partial($terms, $_POST['keyword']);


	    $position = isset($_POST['this_position']) ? $_POST['this_position'] : '';
	    if ($position != 'header') {
		$response_data .= '<ul class="foodbakery-locations-ajax-list">';
	    }
	    if (sizeof($terms) > 0) {
		foreach ($terms as $term_data) {
		    if ($position == 'header') {
			$response_data .= '<li><a href="' . $redirecturl . '?location=' . $term_data . '">' . $term_data . '</a></li>';
		    } else {
			$response_data .= '<li>' . $term_data . '</li>';
		    }
		}
	    } else {
		if ($position == 'header') {
		    $response_data .= '<li class="no-location-found"><a>' . __('No location found', 'foodbakery') . '</a></li>';
		} else {
		    $response_data .= '<li class="no-location-found">' . __('No location found', 'foodbakery') . '</li>';
		}
	    }
	    if ($position != 'header') {
		$response_data .= '</ul>';
	    }
	    echo force_balance_tags($response_data);
	    wp_die();
	}

	public function foodbakery_get_all_default_locations_callback() {
	    global $foodbakery_plugin_options;

	    $foodbakery_search_result_page = isset($foodbakery_plugin_options['foodbakery_search_result_page']) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
	    $redirecturl = isset($foodbakery_search_result_page) && $foodbakery_search_result_page != '' ? get_permalink($foodbakery_search_result_page) . '' : '';

	    // getting from plugin options
	    $foodbakery_default_locations_list = isset($foodbakery_plugin_options['foodbakery_default_locations_list']) ? $foodbakery_plugin_options['foodbakery_default_locations_list'] : array();
	    $position = ( $_POST['this_position'] ) ? $_POST['this_position'] : '';
	    $response_data = '';
	    if ($position != 'header') {
		$response_data .= '<ul class="foodbakery-locations-ajax-list">';
	    }
	    if (is_array($foodbakery_default_locations_list) && sizeof($foodbakery_default_locations_list) > 0) {
		foreach ($foodbakery_default_locations_list as $tag_r) {
		    $tag_obj = get_term_by('slug', $tag_r, 'foodbakery_locations');
		    if (is_object($tag_obj)) {
			if ($position == 'header') {
			    $response_data .= '<li><a href="' . $redirecturl . '?location=' . $tag_obj->name . '">' . $tag_obj->name . '</a></li>';
			} else {
			    $response_data .= '<li>' . $tag_obj->name . '</li>';
			}
		    }
		}
	    } else {
		if ($position == 'header') {
		    $response_data .= '<li class="no-location-found"><a>' . __('No location found', 'foodbakery') . '</a></li>';
		} else {
		    $response_data .= '<li class="no-location-found">' . __('No location found', 'foodbakery') . '</li>';
		}
	    }
	    if ($position != 'header') {
		$response_data .= '</ul>';
	    }
	    echo force_balance_tags($response_data);
	    wp_die();
	}

	/*
	 * Get Geolocation address by latitude & longitude
	 */

	public function foodbakery_get_geolocation_callback() {
	    $lat = ( isset($_POST['lat']) ) ? $_POST['lat'] : '';
	    $lng = ( isset($_POST['lng']) ) ? $_POST['lng'] : '';
	    $data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&sensor=true'));
	    $location_data = $data->results[0];
	    $response_array = array();
	    foreach ($data->results[0]->address_components as $element) {
		$data_array[implode(' ', $element->types)] = $element->long_name;
	    }
	    $response_array = array(
		'address' => $location_data->formatted_address,
		'city' => $data_array['locality political'],
		'country' => $data_array['country political'],
	    );
	    echo json_encode($response_array);
	    wp_die();
	}

	/*
	 * Get Geolocation lat & lng by address
	 */

	public function foodbakery_get_geolocation_latlng_callback($address = '') {
	    global $foodbakery_plugin_options;
	    $google_api = isset($foodbakery_plugin_options['foodbakery_google_api_key']) ? $foodbakery_plugin_options['foodbakery_google_api_key'] : '';
	    $address = ( isset($_POST['address']) ) ? $_POST['address'] : $address;
	    $address = urlencode($address);
	    $data = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $google_api));
	    $response = '';
	    if (isset($data->results[0])) {
		$response = $data->results[0]->geometry->location;
	    } else {
		print_r($data);
	    }
	    return $response;
	}

    }

    new Foodbakery_Locations();

}

    