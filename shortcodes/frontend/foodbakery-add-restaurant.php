<?php

/*
 * Foodbakery Add Restaurant
 * Shortcode
 * @retrun markup
 */

if ( ! function_exists('foodbakery_add_restaurant_shortcode') ) {

	function foodbakery_add_restaurant_shortcode($atts, $content = "") {
		$defaults = array( 'restaurant_title' => '' );

		extract(shortcode_atts($defaults, $atts));
		$html = '';
		wp_enqueue_style('jquery-te');
		wp_enqueue_script('jquery-te');

		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('responsive-calendar');
		wp_enqueue_script('foodbakery-tags-it');

		//iconpicker
		wp_enqueue_style('fonticonpicker');
		wp_enqueue_script('fonticonpicker');
		wp_enqueue_script('foodbakery-reservation-functions');
		
		ob_start();
		$page_element_size = isset($atts['foodbakery_add_restaurant_element_size']) ? $atts['foodbakery_add_restaurant_element_size'] : 100;
		if ( function_exists('foodbakery_var_page_builder_element_sizes') ) {
			echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
		}
		echo '<div class="user-dashboard loader-holder">';
		$restaurant_add_settings = array(
			'return_html' => false,
		);

		if ( ! is_user_logged_in() ) {
			do_action('foodbakery_restaurant_add', $restaurant_add_settings);
		} else {
			esc_html_e('You are not authorized.');
		}
		echo '</div>';
		if ( function_exists('foodbakery_var_page_builder_element_sizes') ) {
			echo '</div>';
		}

		$html .= ob_get_clean();
		return $html;
	}

	add_shortcode('foodbakery_add_restaurant', 'foodbakery_add_restaurant_shortcode');
}