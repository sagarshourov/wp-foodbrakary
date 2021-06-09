<?php

/**
 * File Type: Price Tables Post Type
 */
if (!class_exists('post_type_price_tables')) {

    class post_type_price_tables {

	/**
	 * Start Contructer Function
	 */
	public function __construct() {
	    add_action('init', array(&$this, 'foodbakery_price_tables_register'), 12);
	    add_filter('manage_price_tables_posts_columns', array($this, 'price_tables_cpt_columns'));
	    add_action('manage_price_tables_posts_custom_column', array($this, 'custom_price_tables_column'), 10, 2);
	    add_shortcode('foodbakery_price_table', array($this, 'foodbakery_price_table_shortcode_function'));
	    add_filter('manage_foodbakery-pt_posts_columns', array($this, 'foodbakery_foodbakery_pt_columns_add'));
	    add_action('manage_foodbakery-pt_posts_custom_column', array($this, 'foodbakery_foodbakery_pt_columns'), 10, 2);
	}

	/**
	 * Start Wp's Initilize action hook Function
	 */
	public function foodbakery_price_tables_init() {
	    // Initialize Post Type
	    $this->foodbakery_price_tables_register();
	}

	public function foodbakery_foodbakery_pt_columns_add($columns) {
	    unset(
		    $columns['date']
	    );
	    $columns['price_table_date'] = esc_html__('Date', 'foodbakery');
	    return $columns;
	}

	public function foodbakery_foodbakery_pt_columns($name) {
	    global $post;
	    switch ($name) {
		default:
		    //echo "name is " . $name;
		    break;
		case 'price_table_date':
		    echo get_the_time(get_option('date_format'), $post->ID);
		    break;
	    }
	}

	/**
	 * Start Function How to Register post type
	 */
	public function foodbakery_price_tables_register() {
	    $labels = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_name'),
		'singular_name' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_singular_name'),
		'menu_name' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_menu_name'),
		'name_admin_bar' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_name_admin_bar'),
		'add_new' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_add_new'),
		'add_new_item' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_add_new_item'),
		'new_item' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_new_item'),
		'edit_item' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_edit_item'),
		'view_item' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_view_item'),
		'all_items' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_all_items'),
		'search_items' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_search_items'),
		'not_found' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_not_found'),
		'not_found_in_trash' => foodbakery_plugin_text_srt('foodbakery_post_type_price_table_not_found_in_trash'),
	    );

	    $args = array(
		'labels' => $labels,
		'description' => foodbakery_plugin_text_srt('foodbakery_price_tables'),
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => 'edit.php?post_type=packages',
		'query_var' => false,
		'rewrite' => array('slug' => 'foodbakery-pt'),
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'exclude_from_search' => true,
		'supports' => array('title')
	    );

	    register_post_type('foodbakery-pt', $args);
	}

	/*
	 * add custom column to to row
	 */

	public function price_tables_cpt_columns($columns) {

	    $new_columns = array();
	    return array_merge($columns, $new_columns);
	}

	/*
	 * add column values for each row
	 */

	public function custom_price_tables_column($column) {
	    switch ($column) {
		
	    }
	}

	// End of class	
    }

    // Initialize Object
    $price_tables_object = new post_type_price_tables();
}
