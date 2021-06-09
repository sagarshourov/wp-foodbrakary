<?php
/**
 * Ads html form for page builder
 */
if (!function_exists('foodbakery_banner_ads_shortcode')) {
    function foodbakery_banner_ads_shortcode($atts, $content = "") {
        global $foodbakery_plugin_options;
        $defaults = array('id' => '0');
        extract(shortcode_atts($defaults, $atts));
		
        $html = '';
        if (isset($foodbakery_plugin_options['foodbakery_banner_field_code_no']) && is_array($foodbakery_plugin_options['foodbakery_banner_field_code_no'])) {
            $i = 0;
            foreach ($foodbakery_plugin_options['foodbakery_banner_field_code_no'] as $banner) :
                if ($foodbakery_plugin_options['foodbakery_banner_field_code_no'][$i] == $id) {
                    break;
                }
                $i++;
            endforeach;
            $foodbakery_banner_title = isset($foodbakery_plugin_options['foodbakery_banner_title'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_title'][$i] : '';
            $foodbakery_banner_style = isset($foodbakery_plugin_options['foodbakery_banner_style'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_style'][$i] : '';
            $foodbakery_banner_type = isset($foodbakery_plugin_options['foodbakery_banner_type'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_type'][$i] : '';
            $foodbakery_banner_image = isset($foodbakery_plugin_options['foodbakery_banner_image_array'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_image_array'][$i] : '';
            $foodbakery_banner_url = isset($foodbakery_plugin_options['foodbakery_banner_field_url'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_field_url'][$i] : '';
            $foodbakery_banner_url_target = isset($foodbakery_plugin_options['foodbakery_banner_target'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_target'][$i] : '';
            $foodbakery_banner_adsense_code = isset($foodbakery_plugin_options['foodbakery_banner_adsense_code'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_adsense_code'][$i] : '';
            $foodbakery_banner_code_no = isset($foodbakery_plugin_options['foodbakery_banner_field_code_no'][$i]) ? $foodbakery_plugin_options['foodbakery_banner_field_code_no'][$i] : '';
	    $image_url='';
	    $image_url = wp_get_attachment_url($foodbakery_banner_image);
	    $html .= '<div class="foodbakery_banner_section">';
            if ($foodbakery_banner_type == 'image') {
                if (!isset($_COOKIE["banner_clicks_" . $foodbakery_banner_code_no])) {
                    $html .= '<a onclick="foodbakery_banner_click_count_plus(\'' . admin_url('admin-ajax.php') . '\', \'' . $foodbakery_banner_code_no . '\')" id="banner_clicks' . $foodbakery_banner_code_no . '" href="' . esc_url($foodbakery_banner_url) . '" target="_blank"><img src="' . esc_url($image_url) . '" alt="' . $foodbakery_banner_title . '" /></a>';
                } else {
                    $html .= '<a href="' . esc_url($foodbakery_banner_url) . '" target="' . $foodbakery_banner_url_target . '"><img src="' . esc_url($image_url) . '" alt="' . $foodbakery_banner_title . '" /></a>';
                }
            } else {
                $html .= htmlspecialchars_decode(stripslashes($foodbakery_banner_adsense_code));
            }
            $html .= '</div>';
        }
        $html .= '<script type="text/javascript">
			function foodbakery_banner_click_count_plus(ajax_url, id) {
				"use strict";
				var dataString = "code_id=" + id + "&action=foodbakery_banner_click_count_plus";
				jQuery.ajax({
					type: "POST",
					url: ajax_url,
					data: dataString,
					success: function (response) {
						if (response != "error") {
							jQuery("#banner_clicks" + id).removeAttr("onclick");
						}
					}
				});
				return false;
			}
		</script>';
       // return $html;



        echo do_shortcode($html);
    }

    add_shortcode('foodbakery_banner_ads', 'foodbakery_banner_ads_shortcode');
}