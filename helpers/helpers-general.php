<?php

/**
 * Core Helper Functions of Framework
 *
 * @return
 * @package foodbakery-framework
 */
if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}
if ( ! function_exists('foodbakery_heartbeat_frequency') ) {

    function foodbakery_heartbeat_frequency($settings) {
        global $heartbeat_frequency;
        $settings['interval'] = 60;
        return $settings;
    }

    add_filter('heartbeat_settings', 'foodbakery_heartbeat_frequency');
}
if ( ! function_exists('foodbakery_server_protocol') ) {

    /**
     * Return whether request is on SSL or not. Return protocol.
     *
     * @return string
     */
    function foodbakery_server_protocol() {
        if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) {
            return 'https://';
        }
        return 'http://';
    }

}

if ( ! function_exists('foodbakery_get_input') ) {

    /**
     * Return an input variable from $_REQUEST if exists else default.
     *
     * @param	string $name name of the variable.
     * @param string $default default value.
     * @param string $filter
     * @return string
     */
    function foodbakery_get_input($name, $default = null, $filter = 'cmd') {
        if ( isset($_REQUEST[$name]) ) {
            return foodbakery_input_clean($_REQUEST[$name], $filter);
        }
        return $default;
    }

}


if ( ! function_exists('foodbakery_get_server') ) {

    /**
     * Return an input variable from $_SERVER if exists else default.
     *
     * @param	string $name name of the variable.
     * @param string $default default value.
     * @return string
     */
    function foodbakery_get_server($name, $default = null) {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

}

if ( ! function_exists('foodbakery_get_all_server') ) {

    /**
     * Return an input variable from $_SERVER
     *
     * @return string
     */
    function foodbakery_get_all_server() {
        return $_SERVER;
    }

}

if ( ! function_exists('foodbakery_get_cookie') ) {

    /**
     * Return an input variable from $_COOKIE if exists else default.
     *
     * @param	string $name name of the variable.
     * @param string $default default value.
     * @return string
     */
    function foodbakery_get_cookie($name, $default = null) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

}

if ( ! function_exists('foodbakery_get_all_request') ) {

    /**
     * Return an input variable from $_REQUEST
     *
     * @return string
     */
    function foodbakery_get_all_request() {
        return $_REQUEST;
    }

}

if ( ! function_exists('foodbakery_get_all_cookie') ) {

    /**
     * Return an input variable from $_COOKIE
     *
     * @return string
     */
    function foodbakery_get_all_cookie() {
        return $_COOKIE;
    }

}

if ( ! function_exists('foodbakery_input_clean') ) {

    /**
     * Clean given string by applying requested filter.
     *
     * @param   mixed   $source  Input string/array-of-string to be 'cleaned'
     * @param   string  $type    Return type for the variable (INT, UINT, FLOAT, BOOLEAN, WORD, ALNUM, CMD, BASE64, STRING, ARRAY, PATH, NONE)
     *
     * @return  mixed  'Cleaned' version of input parameter
     */
    function foodbakery_input_clean($source, $type = 'string') {
        // Handle the type constraint
        switch ( strtoupper($type) ) {
            case 'INT':
            case 'INTEGER':
                // Only use the first integer value.
                preg_match('/-?[0-9]+/', (string) $source, $matches);
                $result = @ (int) $matches[0];
                break;

            case 'UINT':
                // Only use the first integer value.
                preg_match('/-?[0-9]+/', (string) $source, $matches);
                $result = @ abs((int) $matches[0]);
                break;

            case 'FLOAT':
            case 'DOUBLE':
                // Only use the first floating point value.
                preg_match('/-?[0-9]+(\.[0-9]+)?/', (string) $source, $matches);
                $result = @ (float) $matches[0];
                break;

            case 'BOOL':
            case 'BOOLEAN':
                $result = (bool) $source;
                break;

            case 'WORD':
                $result = (string) preg_replace_callback('/[^A-Z_]/i', function($m) {
                            return '';
                        }, $source);
                break;

            case 'ALNUM':
                $result = (string) preg_replace_callback('/[^A-Z0-9]/i', function($m) {
                            return '';
                        }, $source);
                break;

            case 'CMD':
                $result = (string) preg_replace_callback('/[^A-Z0-9_\.-]/i', function($m) {
                            return '';
                        }, $source);
                $result = ltrim($result, '.');
                break;

            case 'BASE64':
                $result = (string) preg_replace_callback('/[^A-Z0-9\/+=]/i', function($m) {
                            return '';
                        }, $source);
                break;

            case 'STRING':
                $result = (string) esc_html(foodbakery_decode_str((string) $source));
                break;

            case 'HTML':
                $result = (string) $source;
                break;

            case 'ARRAY':
                $result = (array) $source;
                break;

            case 'PATH':
                $pattern = '/^[A-Za-z0-9_-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/';
                preg_match($pattern, (string) $source, $matches);
                $result = @ (string) $matches[0];
                break;

            case 'USERNAME':
                $result = (string) preg_replace_callback('/[\x00-\x1F\x7F<>"\'%&]/', function($m) {
                            return '';
                        }, $source);
                break;

            default:
                // Are we dealing with an array?
                if ( is_array($source) ) {
                    foreach ( $source as $key => $value ) {
                        // filter element for XSS and other 'bad' code etc.
                        if ( is_string($value) ) {
                            $source[$key] = esc_html(foodbakery_decode_str($value));
                        }
                    }
                    $result = $source;
                } else {
                    // Or a string?
                    if ( is_string($source) && ! empty($source) ) {
                        // filter source for XSS and other 'bad' code etc.
                        $result = esc_html(foodbakery_decode_str($source));
                    } else {
                        // Not an array or string.. return the passed parameter.
                        $result = $source;
                    }
                }
                break;
        }

        return $result;
    }

}

if ( ! function_exists('foodbakery_decode_str') ) {

    /**
     * Try to convert to plaintext
     *
     * @param   string  $source  The source string.
     * @return  string  Plaintext string
     */
    function foodbakery_decode_str($source) {
        static $ttr;

        if ( ! is_array($ttr) ) {
            // Entity decode.
            $trans_tbl = get_html_translation_table(HTML_ENTITIES);
            foreach ( $trans_tbl as $k => $v ) {
                $ttr[$v] = utf8_encode($k);
            }
        }
        $source = strtr($source, $ttr);
        // Convert decimal.
        $source = preg_replace_callback('/&#x(\d+);/mi', function($m) {
            return utf8_encode(chr('0x' . $m[1]));
        }, $source); // Decimal notation.
        // Convert hex.
        $source = preg_replace_callback('/&#x([a-f0-9]+);/mi', function($m) {
            return utf8_encode(chr('0x' . $m[1]));
        }, $source); // Hex notation.
        return $source;
    }

}

if ( ! function_exists('foodbakery_dbg') ) {

    /**
     * Used for debugging, output given data to browser console.
     *
     * @param  mixed  $data		The data to be debugged.
     * @param  string $label	The label to shown with debugged data.
     */
    function foodbakery_dbg($data, $label = '') {
        if ( '' === $label ) {
            $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'foodbakery_dbg'));
            $label = 'Debuged from \'' . basename(debug_backtrace()[$key]['file']) . '\'';
        }
        $data = var_export($data, true);
        $data = explode("\n", $data); // Plz don't remove double quotes arround newline character.
        $output = '';
        foreach ( $data as $line ) {
            if ( trim($line) ) {
                $line = addslashes($line);
                $output .= 'console.log( " ' . $line . '" );';
            }
        }
        echo '<script>console.log( "' . $label . ': "); ' . $output . ' </script>';
    }

}

if ( ! function_exists('foodbakery_shortcode_files') ) {

    /**
     * Include Backend shortcodes pages function 
     */
    function foodbakery_shortcode_files($path) {

        $shortcode_foodbakery = wp_foodbakery::plugin_dir() . 'shortcodes/' . $path . '/';
        $aAdmin = array();
        $aFront = array();
        $aResult = array();
        $file_counter = 0;
        if ( is_dir($shortcode_foodbakery) ) {
            if ( $dh = opendir($shortcode_foodbakery) ) {
                while ( ($file = readdir($dh)) !== false ) {
                    $aAdmin[] = $file;
                    $file_counter ++;
                }

                $aResult['admin'] = $aAdmin;
                closedir($dh);
            }
        }
        if ( is_array($aResult) && count($aResult) > 0 ) {
            return $aResult;
        }
    }

}

if ( ! function_exists('foodbakery_include_shortcode_files') ) {

    /**
     * Include Backend shortcodes pages function 
     */
    function foodbakery_include_shortcode_files() {

        $aFiles = foodbakery_shortcode_files('backend');

        $admin = '/';
        $shortcode_foodbakery = wp_foodbakery::plugin_dir() . 'shortcodes/backend/';
        foreach ( $aFiles as $file ) {
            for ( $i = 0; $i < sizeof($file); $i ++ ) {
                if ( $file[$i] != '' && $file[$i] != "." && $file[$i] != "..." && $file[$i] != ".." ) {
                    require_once $shortcode_foodbakery . $admin . $file[$i];
                }
            }
        }
    }

}


if ( ! function_exists('foodbakery_include_frontend_shortcode_files') ) {

    /**
     * Include Backend shortcodes pages function 
     */
    function foodbakery_include_frontend_shortcode_files() {

        $aFiles = foodbakery_shortcode_files('frontend');

        $admin = '/';
        $shortcode_foodbakery = wp_foodbakery::plugin_dir() . 'shortcodes/frontend/';
        foreach ( $aFiles as $file ) {
            for ( $i = 0; $i < sizeof($file); $i ++ ) {
                if ( $file[$i] != '' && $file[$i] != "." && $file[$i] != "..." && $file[$i] != ".." ) {
                    require_once $shortcode_foodbakery . $admin . $file[$i];
                }
            }
        }
    }

}

/**
 * Start Function how to find Index
 */
if ( ! function_exists('foodbakery_find_in_multiarray') ) {



    function foodbakery_find_in_multiarray($elem, $array, $field) {

        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ( $i = 0; $i <= $top; $i ++ ) {
            if ( isset($array[$i]) ) {
                $new_array[$k] = $array[$i];
                $k ++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;

        $finded_index = '';
        if ( is_array($array) ) {
            while ( $bottom <= $top ) {
                if ( $array[$bottom][$field] == $elem )
                    $finded_index[] = $bottom;
                else
                if ( is_array($array[$bottom][$field]) )
                    if ( foodbakery_find_in_multiarray($elem, ($array[$bottom][$field])) )
                        $finded_index[] = $bottom;
                $bottom ++;
            }
        }

        return $finded_index;
    }

}

/**
 * Start dashboard page link if user login
 */
if ( ! function_exists('foodbakery_user_dashboard_page_url') ) {

    function foodbakery_user_dashboard_page_url($page = 'url') {
        global $foodbakery_plugin_options, $current_user;
        $foodbakery_page_id = '';
        $foodbakery_user_dashboard_page_url = '';
        if ( is_user_logged_in() ) {

            $user_roles = isset($current_user->roles) ? $current_user->roles : '';
            if ( ($user_roles != '' && in_array("foodbakery_publisher", $user_roles) ) ) {
                $foodbakery_page_id = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                if ( $page == 'url' ) {
                    if ( $foodbakery_page_id != '' ) {
                        $foodbakery_user_dashboard_page_url = get_permalink($foodbakery_page_id);
                    }
                } else if ( $page == 'id' ) {
                    $foodbakery_user_dashboard_page_url = ( $foodbakery_page_id );
                }
            }
        }
        return $foodbakery_user_dashboard_page_url;
    }

}



/*
 * @Shortcode Name: Start function for Map shortcode/element front end view
 * @retrun
 *
 */
if ( ! function_exists('foodbakery_map_content') ) {

    function foodbakery_map_content($atts) {

        global $foodbakery_plugin_options;
        $defaults = array(
            'map_height' => '',
            'map_lat' => '51.507351',
            'map_lon' => '-0.127758',
            'map_zoom' => '10',
            'map_type' => '',
            'map_info' => '',
            'map_info_width' => '200',
            'map_info_height' => '200',
            'map_marker_icon' => '',
            'map_show_marker' => 'true',
            'map_controls' => 'true',
            'map_draggable' => 'true',
            'map_scrollwheel' => 'false',
            'map_border' => '',
            'map_border_color' => '',
            'foodbakery_map_style' => '',
            'foodbakery_map_class' => '',
            'foodbakery_map_directions' => 'off',
            'foodbakery_map_circle' => 'off'
        );
        extract(shortcode_atts($defaults, $atts));
        if ( $map_info_width == '' || $map_info_height == '' ) {
            $map_info_width = '300';
            $map_info_height = '150';
        }
        if ( isset($map_height) && $map_height == '' ) {
            $map_height = '500';
        }

        $map_dynmaic_no = rand(6548, 9999999);

        $border = '';
        if ( isset($map_border) && $map_border == 'yes' && $map_border_color != '' ) {
            $border = 'border:1px solid ' . $map_border_color . '; ';
        }

        $map_type = isset($map_type) ? $map_type : '';
        $radius_circle = isset($foodbakery_plugin_options['foodbakery_default_radius_circle']) ? $foodbakery_plugin_options['foodbakery_default_radius_circle'] : '10';
        $radius_circle = ($radius_circle * 1000);
        $map_dynmaic_no = foodbakery_generate_random_string('10');

        $html = '';
        $html .= '<div ' . $foodbakery_map_class . ' style="animation-duration:">';
        $html .= '<div class="clear"></div>';
        $html .= '<div class="cs-map-section" style="' . $border . ';">';
        $html .= '<div class="cs-map">';
        $html .= '<div class="cs-map-content">';

        $html .= '<div class="mapcode iframe mapsection gmapwrapp" id="map_canvas' . $map_dynmaic_no . '" style="height:' . $map_height . 'px;"> </div>';

        if ( $foodbakery_map_directions == 'off' ) {
            $html .= '<div id="cs-directions-panel"></div>';
        }
        $html .= '</div>';
        $html .= '</div>';

        $html .= "<script type='text/javascript'>
                    jQuery(document).ready(function() {
						var center = new google.maps.LatLng(" . $map_lat . ", " . $map_lon . ");  
						var panorama;
						function initialize() {
							var defaultIconsStyle =[
								{
									featureType: \"poi\",
									elementType: \"labels\",
									stylers: [
										  { visibility: \"off\" }
									]
								}
							];
							var myLatlng = new google.maps.LatLng(" . $map_lat . ", " . $map_lon . ");
							var mapOptions = {
								zoom: " . $map_zoom . ",
								scrollwheel: " . $map_scrollwheel . ",
								draggable: " . $map_draggable . ",
								streetViewControl: false,
								center: center,
								disableDefaultUI: true,
								zoomControl: true,
								mapTypeControl: " . $map_controls . ",
								styles: defaultIconsStyle
							};";

        if ( $foodbakery_map_directions == 'on' ) {
            $html .= "var directionsDisplay;
								var directionsService = new google.maps.DirectionsService();
								directionsDisplay = new google.maps.DirectionsRenderer();";
        }

        $html .= "map = new google.maps.Map(document.getElementById('map_canvas" . $map_dynmaic_no . "'), mapOptions);";

        if ( $foodbakery_map_circle == 'on' ) {

            $html .= "var circle = new google.maps.Circle({
									center: center,
									map: map,
									radius: " . $radius_circle . ",          // IN METERS.
									fillColor: '#FF6600',
									fillOpacity: 0.3,
									strokeColor: '#FF6600',
									strokeWeight: 1         // CIRCLE BORDER.     
								});";
        }
        if ( $foodbakery_map_directions == 'on' ) {
            $html .= "directionsDisplay.setMap(map);
									directionsDisplay.setPanel(document.getElementById('cs-directions-panel'));
									function foodbakery_calc_route() {
											var myLatlng = new google.maps.LatLng(" . $map_lat . ", " . $map_lon . ");
											var start = myLatlng;
											var end = document.getElementById('foodbakery_end_direction').value;
											var mode = document.getElementById('foodbakery_chng_dir_mode').value;
											var request = {
													origin:start,
													destination:end,
													travelMode: google.maps.TravelMode[mode]
											};
											directionsService.route(request, function(response, status) {
													if (status == google.maps.DirectionsStatus.OK) {
															directionsDisplay.setDirections(response);
													}
											});
									}
									document.getElementById('foodbakery_search_direction').addEventListener('click', function() {
											foodbakery_calc_route();
									});";
        }
        $html .= "
							var style = '" . $foodbakery_map_style . "';
							if (style != '') {
								var styles = foodbakery_map_select_style(style);
								if (styles != '') {
									var styledMap = new google.maps.StyledMapType(styles,
											{name: 'Styled Map'});
									map.mapTypes.set('map_style', styledMap);
									map.setMapTypeId('map_style');
								}
							}";
        //if ( $foodbakery_map_circle != 'on' ) {
        $html .= "var infowindow = new google.maps.InfoWindow({
								content: '" . $map_info . "',
								maxWidth: " . $map_info_width . ",
								maxHeight: " . $map_info_height . ",
							});
							var marker = new google.maps.Marker({
								position: myLatlng,
								map: map,
								title: '',
								icon: '" . $map_marker_icon . "',
								shadow: ''
							});
							if (infowindow.content != ''){
							  infowindow.open(map, marker);
							   map.panBy(1,-60);
							   google.maps.event.addListener(marker, 'click', function(event) {
								infowindow.open(map, marker);
							   });
							}";
        //  }
        $html .= "panorama = map.getStreetView();
							panorama.setPosition(myLatlng);
							panorama.setPov(({
							  heading: 265,
							  pitch: 0
							}));
					}			
					function foodbakery_toggle_street_view(btn) {
					  var toggle = panorama.getVisible();
					  if (toggle == false) {
							if(btn == 'streetview'){
							  panorama.setVisible(true);
							}
					  } else {
							if(btn == 'mapview'){
							  panorama.setVisible(false);
							}
					  }
					}
					google.maps.event.addDomListener(window, 'load', initialize);
					});
                </script>";
        $html .= '</div>';
        $html .= '</div>';
        echo force_balance_tags($html);
    }

}

/**
 * Include any template file 
 * with wordpress standards
 */
if ( ! function_exists('foodbakery_get_template_part') ) {

    function foodbakery_get_template_part($slug, $name = '', $ext_template = '') {
        $template = '';

        if ( $ext_template != '' ) {
            $ext_template = trailingslashit($ext_template);
        }
        if ( $name ) {
            $template = locate_template(array( "{$slug}-{$name}.php", wp_foodbakery::template_path() . "{$ext_template}{$slug}-{$name}.php" ));
        }
        if ( ! $template && $name && file_exists(wp_foodbakery::plugin_path() . "/templates/{$ext_template}{$slug}-{$name}.php") ) {

            $template = wp_foodbakery::plugin_path() . "/templates/{$ext_template}{$slug}-{$name}.php";
        }
        if ( ! $template ) {

            $template = locate_template(array( "{$slug}.php", wp_foodbakery::template_path() . "{$ext_template}{$slug}.php" ));
        }
        if ( $template ) {
            load_template($template, false);
        }
    }

}


if ( ! function_exists('foodbakery_tooltip_text') ) {

    /**
     * Tool tip text for backend usage.
     *
     * @param type $popover_text
     * @param type $return_html
     * @return type
     */
    function foodbakery_tooltip_text($popover_text = '', $return_html = true) {
        $popover_link = '';
        if ( isset($popover_text) && $popover_text != '' ) {
            $popover_link = '<a class="cs-help" data-toggle="popover" data-placement="right" data-trigger="hover" data-content="' . $popover_text . '"><i class="icon-help"></i></a>';
        }
        if ( $return_html == true ) {
            return $popover_link;
        } else {
            echo force_balance_tags($popover_link);
        }
    }

}


if ( ! function_exists('foodbakery_get_currency') ) {

    /**
     * Return an input variable from $_SERVER if exists else default.
     *
     * @param	string $name name of the variable.
     * @param string $default default value.
     * @return string
     */
    function foodbakery_get_currency($price = '', $currency_symbol = false, $before_currency = '', $after_currency = '', $currency_converter = true) {
        global $foodbakery_plugin_options;
        $price_str = '';
        $default_currency = isset($foodbakery_plugin_options['foodbakery_currency_sign']) ? $foodbakery_plugin_options['foodbakery_currency_sign'] : '$';
        $plugin_currency_id = isset($foodbakery_plugin_options['foodbakery_currency_id']) ? $foodbakery_plugin_options['foodbakery_currency_id'] : '';
        $current_currency_id = foodbakery_get_transient_obj('foodbakery_user_currency');
        $current_currency_id = ( $current_currency_id == '' ) ? $plugin_currency_id : $current_currency_id;
        if ( $current_currency_id != '' ) {
            $conversion_rate = get_post_meta($current_currency_id, 'foodbakery_conversion_rate', true);
            $default_currency = get_post_meta($current_currency_id, 'foodbakery_currency_symbol', true);
            if ( $currency_converter === true ) {
                $price = $price * $conversion_rate;
            }
        }

        if ( $current_currency_id == '' ) {
            $base_currency = foodbakery_get_base_currency();
            $base_currency = foodbakery_base_currency_data($base_currency);
            $default_currency = $base_currency['symbol'];
        }

        if ( class_exists('WooCommerce') ) {
            $woocommerce_enabled = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';
            if ( $woocommerce_enabled == 'on' ) {
                $default_currency = get_woocommerce_currency_symbol();
            }
        }
        $currency_alignment = isset($foodbakery_plugin_options['foodbakery_currency_alignment']) ? $foodbakery_plugin_options['foodbakery_currency_alignment'] : 'Left';

        $price = FOODBAKERY_FUNCTIONS()->num_format($price);
        if ( $currency_symbol == true && is_numeric($price) ) {
            $price_str = $before_currency . $default_currency . $after_currency . $price;
            if($currency_alignment == 'Right'){
                $price_str = $price .' '. $before_currency . $default_currency . $after_currency;
            }
        } else {
            $price_str = $price;
        }
        return $price_str;
    }

}

if ( ! function_exists('foodbakery_get_base_currency') ) {

    function foodbakery_get_base_currency() {
        global $foodbakery_plugin_options;
        $base_currency = isset($foodbakery_plugin_options['foodbakery_base_currency']) ? $foodbakery_plugin_options['foodbakery_base_currency'] : 'USD';
        if ( class_exists('WooCommerce') ) {
            $woocommerce_enabled = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';
            if ( $woocommerce_enabled == 'on' ) {
                $base_currency = get_woocommerce_currency();
            }
        }
        return $base_currency;
    }

}

if ( ! function_exists('foodbakery_base_currency_sign') ) {

    function foodbakery_base_currency_sign() {
        global $foodbakery_plugin_options;
        $base_currency = foodbakery_get_base_currency();
        $base_currency = foodbakery_base_currency_data($base_currency);
        $default_currency = $base_currency['symbol'];
        if ( class_exists('WooCommerce') ) {
            $woocommerce_enabled = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';
            if ( $woocommerce_enabled == 'on' ) {
                $default_currency = get_woocommerce_currency_symbol();
            }
        }
        return $default_currency;
    }

}

if ( ! function_exists('foodbakery_base_currency_data') ) {

    function foodbakery_base_currency_data($base_currency = 'USD') {
        global $foodbakery_plugin_options;
        $currencies = foodbakery_get_currencies();
        if ( isset($currencies[$base_currency]['symbol']) ) {
            $base_currency = $currencies[$base_currency];
        }
        return $base_currency;
    }

}

if ( ! function_exists('foodbakery_get_currency_sign') ) {

    /**
     *
     * @return string for currency sign
     */
    function foodbakery_get_currency_sign() {
        global $foodbakery_plugin_options;
        $price_str = '';
        $default_currency = isset($foodbakery_plugin_options['foodbakery_currency_sign']) ? $foodbakery_plugin_options['foodbakery_currency_sign'] : '$';
        $plugin_currency_id = isset($foodbakery_plugin_options['foodbakery_currency_id']) ? $foodbakery_plugin_options['foodbakery_currency_id'] : '';
        $current_currency_id = foodbakery_get_transient_obj('foodbakery_user_currency');

        $current_currency_id = ( $current_currency_id == '' ) ? $plugin_currency_id : $current_currency_id;



        if ( $current_currency_id != '' ) {
            $default_currency = get_post_meta($current_currency_id, 'foodbakery_currency_symbol', true);
        }

        if ( $current_currency_id == '' ) {
            $base_currency = foodbakery_get_base_currency();
            $base_currency = foodbakery_base_currency_data($base_currency);
            $default_currency = $base_currency['symbol'];
        }

        if ( class_exists('WooCommerce') ) {
            $woocommerce_enabled = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';
            if ( $woocommerce_enabled == 'on' ) {
                $default_currency = get_woocommerce_currency_symbol();
            }
        }

        return $default_currency;
    }

}

if ( ! function_exists('foodbakery_all_currencies') ) {

    function foodbakery_all_currencies($currency = '') {
        global $foodbakery_html_fields_frontend, $foodbakery_plugin_options, $foodbakery_form_fields_frontend;

        $currencies_array = array();
        $args = array(
            'post_type' => 'foodbakery-currenc',
            'post_status' => 'publish',
        );

        if ( $currency != '' ) {
            $args['meta_query'][] = array(
                'key' => 'foodbakery_base_currency',
                'value' => $currency,
                'compare' => '=',
            );
        }

        $currencies_obj = new WP_Query($args);

        $all_currencies = $currencies_obj->posts;

        if ( ! empty($all_currencies) ) {
            foreach ( $all_currencies as $currencyObj ) {
                $currencies_array[$currencyObj->ID] = $currencyObj->post_title;
            }
        }

        $current_currency_id = foodbakery_get_transient_obj('foodbakery_user_currency');

        $foodbakery_opt_array = array(
            'name' => __('Currency', 'foodbakery'),
            'desc' => '',
            'echo' => true,
            'field_params' => array(
                'std' => $current_currency_id,
                'id' => 'currency-id',
                'classes' => 'chosen-select-no-single',
                'options' => $currencies_array,
                'extra_atr' => ''
            ),
        );
        $foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
    }

}


if ( ! function_exists('foodbakery_all_currencies_array') ) {

    function foodbakery_all_currencies_array($currency = '') {
        global $foodbakery_html_fields_frontend, $foodbakery_plugin_options, $foodbakery_form_fields_frontend;
        $currencies_array = array( '' => __('Select Currency', 'foodbakery') );
        $args = array(
            'post_type' => 'foodbakery-currenc',
            'post_status' => 'publish',
        );

        if ( $currency != '' ) {
            $args['meta_query'][] = array(
                'key' => 'foodbakery_base_currency',
                'value' => $currency,
                'compare' => '=',
            );
        }

        $currencies_obj = new WP_Query($args);

        $all_currencies = $currencies_obj->posts;

        if ( ! empty($all_currencies) ) {
            foreach ( $all_currencies as $currencyObj ) {
                $currencies_array[$currencyObj->ID] = $currencyObj->post_title;
            }
        }

        return $currencies_array;
    }

}

if ( ! function_exists('foodbakery_change_user_currency_callback') ) {

    function foodbakery_change_user_currency_callback() {
        global $foodbakery_plugin_options;
        $currency_id = foodbakery_get_input('currency_id');
        foodbakery_set_transient_obj('foodbakery_user_currency', $currency_id);
        wp_die();
    }

}
add_action('wp_ajax_foodbakery_change_user_currency', 'foodbakery_change_user_currency_callback', 1);
add_action('wp_ajax_nopriv_foodbakery_change_user_currency', 'foodbakery_change_user_currency_callback', 1);

if ( ! function_exists('foodbakery_current_page_url') ) {

    /**
     * Return an input variable from $_SERVER if exists else default.
     *
     * @param	string $name name of the variable.
     * @param string $default default value.
     * @return string
     */
    function foodbakery_current_page_url($request_var = true) {
        $pageURL = 'http';
        if ( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ) {
            $pageURL .= "s";
        }
        $request_str = '';
        if ( $request_var == true ) {
            if ( isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"] != '' ) {
                $request_str = $_SERVER["REQUEST_URI"];
            }
        }
        $pageURL .= "://";
        if ( isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" ) {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $request_str;
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $request_str;
        }
        echo esc_url($pageURL);
        return esc_url($pageURL);
    }

}

if ( ! function_exists('foodbakery_company_id_form_user_id') ) {

    function foodbakery_company_id_form_user_id($user_id = '') {
        $company_id = '';
        if ( $user_id == '' ) {
            $user_id = get_current_user_id();
        }
        if ( $user_id != '' ) {
            $company_id = get_user_meta($user_id, 'foodbakery_company', true);
        }
        return $company_id;
    }

}

if ( ! function_exists('foodbakery_user_id_form_company_id') ) {

    function foodbakery_user_id_form_company_id($company_id = '') {
        $user_id = '';

        if ( $company_id != '' ) {
            $args = array(
                'meta_query' =>
                array(
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'foodbakery_company',
                            'value' => $company_id,
                            'compare' => '=',
                            'type' => 'numeric'
                        ),
                    )
                )
            );

            $users = get_users($args);
            if ( ! empty($users) && is_array($users) )
                foreach ( $users as $user ) {
                    foreach ( $user as $user_data ) {
                        $user_id = isset($user_data->ID) ? $user_data->ID : '';
                        break;
                    }
                }
        }
        return $user_id;
    }

}


if ( ! function_exists('foodbakery_get_item_count') ) {

    function foodbakery_get_item_count($left_filter_count_switch, $args, $count_arr, $restaurant_type, $restaurant_short_counter, $atts, $field_meta_key, $lat_long = array(), $direct_count_value_for = '', $direct_count_pre_order_value_for = '') {
        if ( $left_filter_count_switch == 'yes' ) {
            global $foodbakery_shortcode_restaurants_frontend;


            // get all arguments from getting flters
            $left_filter_arr = $foodbakery_shortcode_restaurants_frontend->get_filter_arg($restaurant_type, $restaurant_short_counter, $field_meta_key);

            if ( isset($count_arr) && ! empty($count_arr) ) {
                $left_filter_arr[] = $count_arr;
            }

            $post_ids = array();
            if ( ! empty($left_filter_arr) ) {
                // apply all filters and get ids
                $post_ids = $foodbakery_shortcode_restaurants_frontend->get_listing_id_by_filter($left_filter_arr);
            }
            $all_post_ids = array();
            if ( ! empty($post_ids) ) {
                $all_post_ids[] = $post_ids;
            }

            // extra location filters   
            if ( isset($_REQUEST['location']) && $_REQUEST['location'] != '' ) {
                $post_ids = $foodbakery_shortcode_restaurants_frontend->restaurant_location_filter($_REQUEST['location'], $post_ids, $lat_long);
                if ( empty($post_ids) ) {
                    $post_ids = array( 0 );
                }
            }
            $post_ids = $foodbakery_shortcode_restaurants_frontend->restaurant_open_close_filter($atts, $post_ids, $field_meta_key, $direct_count_value_for);

            $post_ids = $foodbakery_shortcode_restaurants_frontend->restaurant_pre_order_filter($atts, $post_ids, $field_meta_key, $direct_count_pre_order_value_for);

            $all_post_ids = $post_ids;

            if ( ! empty($all_post_ids) ) {
                $args['post__in'] = $all_post_ids;
            }
//			echo '<pre>';
//			print_r($args);
//			echo '<pre>';
            $restaurant_loop_obj = foodbakery_get_cached_obj('restaurant_result_cached_loop_count_obj', $args, 12, false, 'wp_query');
            $restaurant_totnum = $restaurant_loop_obj->found_posts;
            return $restaurant_totnum;
        }
    }

}

if ( ! function_exists('foodbakery_get_cached_obj') ) {

    function foodbakery_get_cached_obj($cache_variable, $args, $time = 12, $cache = true, $type = 'wp_query', $taxanomy_name = '') {
        $restaurant_loop_obj = '';
        if ( $cache == true ) {
            $time_string = $time * HOUR_IN_SECONDS;
            if ( $cache_variable != '' ) {
                if ( false === ( $restaurant_loop_obj = wp_cache_get($cache_variable) ) ) {
                    if ( $type == 'wp_query' ) {
                        $restaurant_loop_obj = new WP_Query($args);
                    } else if ( $type == 'get_term' ) {
                        $restaurant_loop_obj = array();
                        $terms = get_terms($taxanomy_name, $args);
                        if ( sizeof($terms) > 0 ) {
                            foreach ( $terms as $term_data ) {
                                $restaurant_loop_obj[] = $term_data->name;
                            }
                        }
                    }
                    wp_cache_set($cache_variable, $restaurant_loop_obj, $time_string);
                }
            }
        } else {
            if ( $type == 'wp_query' ) {
                $restaurant_loop_obj = new WP_Query($args);
            } else if ( $type == 'get_term' ) {
                $restaurant_loop_obj = array();
                $terms = get_terms($taxanomy_name, $args);
                if ( sizeof($terms) > 0 ) {
                    foreach ( $terms as $term_data ) {
                        $restaurant_loop_obj[] = $term_data->name;
                    }
                }
            }
        }



        return $restaurant_loop_obj;
    }

}
if ( ! function_exists('foodbakery_set_transient_obj') ) {

    function foodbakery_set_transient_obj($transient_variable, $data_string, $time = 12) {
        if ( !isset($_COOKIE['identifier']) || $_COOKIE['identifier'] == '' ) {
            setcookie('identifier', uniqid(), time() + (86400 * 30), "/"); // 86400 = 1 day
        }
        $result = '';
        $identifier = '';
        $identifier = isset($_COOKIE['identifier']) ? $_COOKIE['identifier'] : '';
        $time_string = $time * HOUR_IN_SECONDS;
        if ( $data_string != '' ) {
            $result = set_transient($identifier . $transient_variable, $data_string, $time_string);
        }
        return $result;
    }

}

if ( ! function_exists('foodbakery_get_transient_obj') ) {

    function foodbakery_get_transient_obj($transient_variable) {
        //$data_string = get_transient( $transient_variable );
        $identifier = uniqid();
        if ( isset($_COOKIE['identifier']) ) {
            $identifier = $_COOKIE['identifier'];
        }
        if ( false === ( $data_string = get_transient($identifier . $transient_variable) ) ) {
            return false;
        } else {
            return $data_string;
        }
    }

}

/*
 * action for header get started
 */

add_action('foodbakery_get_started', 'foodbakery_get_started_callback');

if ( ! function_exists('foodbakery_get_started_callback') ) {

    function foodbakery_get_started_callback() {
        global $foodbakery_plugin_options;
        $foodbakery_header_button_title = isset($foodbakery_plugin_options['foodbakery_header_button_title']) ? $foodbakery_plugin_options['foodbakery_header_button_title'] : '';
        $foodbakery_header_button_url = isset($foodbakery_plugin_options['foodbakery_header_button_url']) ? $foodbakery_plugin_options['foodbakery_header_button_url'] : '';
        $foodbakery_header_buton_switch = isset($foodbakery_plugin_options['foodbakery_header_buton_switch']) ? $foodbakery_plugin_options['foodbakery_header_buton_switch'] : '';
        $button_url = 'javascript:void()';
        if ( $foodbakery_header_button_url != '' ) {
            $button_url = esc_url($foodbakery_header_button_url);
        }
        if ( $foodbakery_header_buton_switch == 'on' && ! is_user_logged_in() ) {
            echo '<a class="get-start-btn" href=" ' . $button_url . ' " > ' . esc_html($foodbakery_header_button_title) . '  </a> ';
        }
    }

}

if ( ! function_exists('foodbakery_random_ads_callback') ) {

    function foodbakery_random_ads_callback($banner_style) {
        global $wpdb, $post, $foodbakery_plugin_options;

        $cs_total_banners = 1;
        if ( isset($foodbakery_plugin_options['foodbakery_banner_title']) ) {
            $i = 0;
            $d = 0;
            $cs_banner_array = array();
            foreach ( $foodbakery_plugin_options['foodbakery_banner_title'] as $banner ) :

                if ( $foodbakery_plugin_options['foodbakery_banner_style'][$i] == $banner_style ) {
                    $cs_banner_array[] = $i;
                    $d ++;
                }
                if ( $cs_total_banners == $d ) {
                    break;
                }
                $i ++;
            endforeach;
            if ( sizeof($cs_banner_array) > 0 ) {
                if ( sizeof($cs_banner_array) > 1 ) {
                    $cs_act_size = sizeof($cs_banner_array) - 1;
                    $cs_rand_banner = rand(0, $cs_act_size);
                } else {
                    $cs_rand_banner = 0;
                }

                $rand_banner = $cs_banner_array[$cs_rand_banner];
                echo do_shortcode('[foodbakery_banner_ads id="' . $foodbakery_plugin_options['foodbakery_banner_field_code_no'][$rand_banner] . '"]');
            }
        }
    }

    add_action('foodbakery_random_ads', 'foodbakery_random_ads_callback', 1);
}

if ( ! function_exists('foodbakery_restaurant_type_id') ) {

    function foodbakery_restaurant_type_id() {
        $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
        return $restaurants_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : '';
    }

}

if ( ! function_exists('foodbakery_get_term_name_by_slug') ) {

    function foodbakery_get_term_name_by_slug($term_slug = '') {
        $term_name = '';
        if ( $term_slug ) {
            $term = get_term_by('slug', $term_slug, 'foodbakery_locations');
            if ( $term ) {
                $term_name = $term->name;
            }
        }
        return $term_name;
    }

}
if ( ! function_exists('foodbakery_restaurant_address_from_locations') ) {

    function foodbakery_restaurant_address_from_locations($restaurant_id = '') {
        $restaurant_location_address = array();
        if ( $restaurant_id != '' ) {

            $restaurant_country = get_post_meta($restaurant_id, 'foodbakery_post_loc_country_restaurant', true);
            $restaurant_state = get_post_meta($restaurant_id, 'foodbakery_post_loc_state_restaurant', true);
            $restaurant_city = get_post_meta($restaurant_id, 'foodbakery_post_loc_city_restaurant', true);
            $restaurant_town = get_post_meta($restaurant_id, 'foodbakery_post_loc_town_restaurant', true);

            if ( function_exists('foodbakery_get_term_name_by_slug') ) {
                $restaurant_country = foodbakery_get_term_name_by_slug($restaurant_country);
                $restaurant_state = foodbakery_get_term_name_by_slug($restaurant_state);
                $restaurant_city = foodbakery_get_term_name_by_slug($restaurant_city);
                $restaurant_town = foodbakery_get_term_name_by_slug($restaurant_town);
            }

            if ( $restaurant_town ) {
                $restaurant_location_address[] = $restaurant_town;
            }if ( $restaurant_city ) {
                $restaurant_location_address[] = $restaurant_city;
            }if ( $restaurant_state ) {
                $restaurant_location_address[] = $restaurant_state;
            }if ( $restaurant_country ) {
                $restaurant_location_address[] = $restaurant_country;
            }
        }
        return $restaurant_location_address;
    }

}

if ( ! function_exists('get_user_info_array') ) {

    function get_user_info_array($user_id = '') {
        $first_name = '';
        $last_name = '';
        $email = '';
        $phone_number = '';
        $address = '';
        if ( $user_id == '' ) {
            $user_data = wp_get_current_user();
            $user_id = $user_data->ID;
        }
        if ( is_user_logged_in() ) {
            $publisher_id = get_user_meta($user_id, 'foodbakery_company', true);

            /*$display_name = get_the_title($publisher_id);
            $user_names = explode(" ", $display_name);
            $first_name = isset($user_names[0]) ? $user_names[0] : '';
            $last_name = isset($user_names[1]) ? $user_names[1] : '';*/

            $first_name = get_user_meta($user_id, 'first_name', true);
            $last_name = get_user_meta($user_id, 'last_name', true);

            $first_name = ($first_name != '') ? $first_name : '';
            $last_name = ($last_name != '') ? $last_name : '';

            $phone_number = get_post_meta($publisher_id, 'foodbakery_phone_number', true);
            $email = get_post_meta($publisher_id, 'foodbakery_email_address', true);
            $address = get_post_meta($publisher_id, 'foodbakery_post_loc_address_publisher', true);
        }

        $user_info = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone_number' => $phone_number,
            'email' => $email,
            'address' => $address,
        );

        return $user_info;
    }

}

/*
 * Filter to include the required files
 */
if ( ! function_exists('foodbakery_include_required_files_callback') ) {

    function foodbakery_include_required_files_callback() {
        /*
         * Add Files to include along with the path
         */
        $files_array = array();

        /*
         * Automatically adding the required files from "files_array"
         */
        if ( ! empty($files_array) ) {
            foreach ( $files_array as $file_path ) {
                if ( $file_path != '' && file_exists($file_path) ) {
                    require_once $file_path;
                }
            }
        }
    }

    add_action('foodbakery_include_required_files', 'foodbakery_include_required_files_callback', 1);
}

/*
 * On Update Plugin / Theme calling web service
 */

if ( class_exists('wp_foodbakery_framework') ) {

    if ( ! function_exists('foodbakery_plugin_db_structure_updater_demo_callback') ) {

        function foodbakery_plugin_db_structure_updater_demo_callback() {
            $remote_api_url = REMOTE_API_URL;
            $envato_purchase_code_verification = get_option('item_purchase_code_verification');
            $selected_demo = isset($_POST['theme_demo']) ? $_POST['theme_demo'] : '';
            $envato_email = isset($_POST['envato_email']) ? $_POST['envato_email'] : '';
            $envato_purchase_code_verification['selected_demo'] = $selected_demo;
            $envato_purchase_code_verification['envato_email_address'] = $envato_email;
            update_option('item_purchase_code_verification', $envato_purchase_code_verification);
            $theme_obj = wp_get_theme();
            $demo_data = array(
                'theme_puchase_code' => isset($envato_purchase_code_verification['item_puchase_code']) ? $envato_purchase_code_verification['item_puchase_code'] : '',
                'theme_name' => $theme_obj->get('Name'),
                'theme_id' => isset($envato_purchase_code_verification['item_id']) ? $envato_purchase_code_verification['item_id'] : '',
                'user_email' => $envato_email,
                'theme_demo' => $selected_demo,
                'theme_version' => $theme_obj->get('Version'),
                'site_url' => site_url(),
                'supported_until' => isset($envato_purchase_code_verification['supported_until']) ? $envato_purchase_code_verification['supported_until'] : '',
                'action' => 'add_to_active_themes',
            );
            $url = $remote_api_url;
            $response = wp_remote_post($url, array( 'body' => $demo_data ));
            check_theme_is_active();
        }

        add_action('foodbakery_plugin_db_structure_updater', 'foodbakery_plugin_db_structure_updater_demo_callback', 10);
    }
}