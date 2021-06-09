<?php

/**
 * Currencies Post Type
 */
if (!class_exists('post_type_currencies')) {

    class post_type_currencies {

	// The Constructor
	public function __construct() {
	    add_action('init', array(&$this, 'currencies_init'));
	    add_action('admin_init', array(&$this, 'currencies_admin_init'));
	    add_filter('manage_foodbakery-currenc_posts_columns', array($this, 'manage_columns_callback'));
	    add_action('manage_foodbakery-currenc_posts_custom_column', array($this, 'manage_custom_columns_callback'), 10, 2);
	    add_filter('foodbakery_plugin_text_strings', array(&$this, 'foodbakery_plugin_text_strings_callback'));

	    add_action('wp_ajax_curriencies_list_based_currency', array(&$this, 'curriencies_list_based_currency_callback'));
	    add_action('wp_ajax_nopriv_curriencies_list_based_currency', array(&$this, 'curriencies_list_based_currency_callback'));
	}

	/*
	 * add custom column to to row
	 */

	public function manage_columns_callback($columns) {
	    unset($columns['date']);
	    $new_columns = array(
		'currency_date' => __('Date', 'ThemeName'),
		'currency_symbol' => __('Currency Symbol', 'ThemeName'),
		'conversion_rate' => __('Conversion Rate', 'ThemeName'),
	    );
	    return array_merge($columns, $new_columns);
	}

	/*
	 * add column values for each row
	 */

	public function manage_custom_columns_callback($column) {
	    global $post;
	    switch ($column) {
		case 'currency_date':
		    echo get_the_time(get_option('date_format'), $post->ID);
		    break;
		case 'currency_symbol' :
		    echo get_post_meta(get_the_ID(), 'foodbakery_currency_symbol', true);
		    break;
		case 'conversion_rate' :
		    echo get_post_meta(get_the_ID(), 'foodbakery_conversion_rate', true);
		    break;
	    }
	}

	public function currencies_init() {
	    // Initialize Post Type
	    $this->currencies_register();
	}

	public function currencies_register() {
	    $labels = array(
		'name' => __('Currencies', 'foodbakery'),
		'menu_name' => __('Currencies', 'foodbakery'),
		'add_new_item' => __('Add New Currency', 'foodbakery'),
		'edit_item' => __('Edit Currency', 'foodbakery'),
		'new_item' => __('New Currency Item', 'foodbakery'),
		'add_new' => __('Add New Currency', 'foodbakery'),
		'view_item' => __('View Currency Item', 'foodbakery'),
		'search_items' => __('Search', 'foodbakery'),
		'not_found' => __('Nothing found', 'foodbakery'),
		'not_found_in_trash' => __('Nothing found in Trash', 'foodbakery'),
		'parent_item_colon' => ''
	    );
	    $args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'query_var' => false,
		'menu_icon' => 'dashicons-admin-post',
		'show_in_menu' => 'edit.php?post_type=restaurants',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title')
	    );
	    register_post_type('foodbakery-currenc', $args);
	}

	/**
	 * End Function  how create post type of currencies
	 */

	/**
	 * Start Function  how create add meta boxes of currencies
	 */
	public function currencies_admin_init() {
	    // Add metaboxes
	    add_action('add_meta_boxes', array(&$this, 'foodbakery_meta_currencies_add'));
	}

	public function foodbakery_meta_currencies_add() {
	    add_meta_box('foodbakery_meta_currencies', __('Currency Options', 'foodbakery'), array(&$this, 'foodbakery_meta_currencies'), 'foodbakery-currenc', 'normal', 'high');
	}

	public function foodbakery_meta_currencies($post) {
	    global $gateways, $foodbakery_html_fields, $foodbakery_form_fields, $foodbakery_plugin_options;

	    $currencies = array();
	    $foodbakery_currencuies = foodbakery_get_currencies();
	    if (is_array($foodbakery_currencuies)) {
		foreach ($foodbakery_currencuies as $key => $value) {
		    $currencies[$key] = $value['name'] . '-' . $value['code'];
		}
	    }

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_currency_symbol'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'currency_symbol',
		    'name' => 'currency_symbol',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_conversion_rate'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'conversion_rate',
		    'name' => 'conversion_rate',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_base_currency'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'base_currency',
		    'classes' => 'dropdown chosen-select-no-single',
		    'name' => 'base_currency',
		    'return' => true,
		    'options' => $currencies
		),
	    );

	    $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
	}

	public function foodbakery_plugin_text_strings_callback($foodbakery_static_text) {
	    $foodbakery_static_text['foodbakery_conversion_rate'] = __('Conversion Rate', 'foodbakery');
	    $foodbakery_static_text['foodbakery_currency_symbol'] = __('Currency Symbol', 'foodbakery');
	    $foodbakery_static_text['foodbakery_base_currency'] = __('Base Currency', 'foodbakery');

	    return $foodbakery_static_text;
	}

	public function curriencies_list_based_currency_callback() {
	    $base_currency = $_POST['base_currency'];
	    $currencies_array = foodbakery_all_currencies_array($base_currency);
	    $html = '';
	    if (!empty($currencies_array)) {
		foreach ($currencies_array as $currency_key => $currency_value) {
		    $html .= '<option value="' . $currency_key . '">' . $currency_value . '</option>';
		}
	    }
	    echo $html;
	    wp_die();
	}

    }

    return new post_type_currencies();
}