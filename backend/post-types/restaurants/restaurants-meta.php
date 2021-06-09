<?php
/**
 * @Add Meta Box For Restaurants Post
 * @return
 *
 */
if (!class_exists('foodbakery_restaurant_meta')) {

    class foodbakery_restaurant_meta {

	var $html_data = '';

	public function __construct() {
	    add_action('add_meta_boxes', array($this, 'foodbakery_meta_restaurants_add'));
	    add_action('wp_ajax_restaurant_type_dyn_fields', array($this, 'restaurant_type_change_fields'));
	    add_action('admin_footer-edit-tags.php', array($this, 'foodbakery_remove_catmeta'));
	    add_filter('manage_edit-foodbakery_locations_columns', array($this, 'theme_columns'));
	    add_action('wp_ajax_foodbakery_restaurant_off_day_to_list', array($this, 'append_to_book_days_off'));

	    add_action('wp_ajax_foodbakery_meta_restaurant_categories', array($this, 'foodbakery_meta_restaurant_categories'));
	    add_action('wp_ajax_nopriv_foodbakery_meta_restaurant_categories', array($this, 'foodbakery_meta_restaurant_categories'));

	    add_action('wp_ajax_restaurant_add_menu_cat_item', array($this, 'foodbakery_restaurant_menu_cat_item'));
	    add_action('wp_ajax_nopriv_restaurant_add_menu_cat_item', array($this, 'foodbakery_restaurant_menu_cat_item'));
        add_action('save_post', array($this, 'foodbakery_restaurant_save_menu_category'), 11);
        add_action('save_post', array($this, 'foodbakery_restaurant_save_off_days'), 11);
        add_action('save_post', array($this, 'foodbakery_restaurant_categories'), 11);
        add_action('save_post', array($this, 'foodbakery_restaurant_save_opening_hours'), 20);
        add_action('save_post', array($this, 'foodbakery_save_restaurant_custom_fields_dates'), 20);
        add_action('save_post', array($this, 'foodbakery_save_restaurant_features'), 20);
	}

	public function foodbakery_restaurant_save_menu_category($restaurant_id = '') {

	    $restaurant_menu_cat_titles = foodbakery_get_input('menu_cat_title', '', 'ARRAY');
	    $restaurant_menu_cat_descs = foodbakery_get_input('menu_cat_desc', '', 'ARRAY');

	    if (!empty($restaurant_menu_cat_descs)) {
		update_post_meta($restaurant_id, 'menu_cat_descs', $restaurant_menu_cat_descs);
	    }
	    if (!empty($restaurant_menu_cat_titles)) {
		update_post_meta($restaurant_id, 'menu_cat_titles', $restaurant_menu_cat_titles);
	    }
	}

	function foodbakery_meta_restaurants_add() {
	    add_meta_box('foodbakery_meta_restaurants', foodbakery_plugin_text_srt('foodbakery_restaurant_options'), array($this, 'foodbakery_meta_restaurants'), 'restaurants', 'normal', 'high');
	}

	/**
	 * Start Function How to Attach mata box with post
	 */
	function foodbakery_meta_restaurants($post) {
	    ?>
	    <div class="page-wrap page-opts left" style="overflow:hidden; position:relative;">
	        <div class="option-sec" style="margin-bottom:0;">
	    	<div class="opt-conts">
	    	    <div class="elementhidden">
	    		<nav class="admin-navigtion">
	    		    <ul id="cs-options-tab">
	    			<li><a href="javascript:void(0);" name="#tab-general-settings" href="javascript:;"><i class="icon-settings"></i><?php esc_html_e('General Info', 'foodbakery') ?> </a></li>
	    			<li><a href="javascript:void(0);" name="#tab-package-settings" href="javascript:;"><i class="icon-list"></i> <?php esc_html_e('Membership Info', 'foodbakery') ?></a></li>
	    			<li><a href="javascript:void(0);" name="#tab-menu-categories" href="javascript:;"><i class="icon-list"></i> <?php esc_html_e('Menu Categories', 'foodbakery') ?></a></li>
                        <?php echo  do_action('foodbakery_add_menu_coupon_inrestaurant_backend'); ?>
	    		    </ul>
	    		</nav>
	    		<div id="tabbed-content" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
	    		    <div id="tab-general-settings">
				    <?php $this->foodbakery_restaurant_options(); ?>
	    		    </div>
	    		    <div id="tab-package-settings">
				    <?php $this->foodbakery_package_info_options(); ?>
	    		    </div>
	    		    <div id="tab-menu-categories">
				    <?php $this->restaurant_menu_cats(); ?>
	    		    </div>
                    <?php do_action('foodbakery_add_menu_coupon_content_inrestaurant_backend'); ?>
	    		</div>
			    <?php $this->foodbakery_submit_meta_box('restaurants', $args = array()); ?>
	    	    </div>
	    	</div>
	        </div>
	    </div>
	    <div class="clear"></div>
	    <?php
	}

	function foodbakery_restaurant_options() {
	    global $post, $foodbakery_form_fields, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_options;
	    $post_id = $post->ID;
	    $foodbakery_restaurant_types = array();
	    $foodbakery_args = array('posts_per_page' => '-1', 'post_type' => 'restaurants_capacity', 'orderby' => 'ID', 'post_status' => 'publish');
	    $cust_query = get_posts($foodbakery_args);
	    $foodbakery_restaurant_capacity = get_post_meta($post->ID, 'foodbakery_restaurant_capacity', true);
	    $foodbakery_restaurant_featured = get_post_meta($post->ID, 'foodbakery_restaurant_featured', true);
	    $restaurant_type_slug = get_post_meta($post->ID, 'foodbakery_restaurant_type', true);
	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
	    $foodbakery_type_full_data = get_post_meta($restaurant_type_id, 'foodbakery_full_data', true);

	    $foodbakery_users_list = array('' => esc_html__('Select Publisher', 'foodbakery'));
	    $args = array('posts_per_page' => '-1', 'post_type' => 'publishers', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
	    $cust_query = get_posts($args);
	    if (is_array($cust_query) && sizeof($cust_query) > 0) {
		foreach ($cust_query as $package_post) {
		    if (isset($package_post->ID)) {
			$package_id = $package_post->ID;
			$package_title = $package_post->post_title;
			$foodbakery_users_list[$package_id] = $package_title;
		    }
		}
	    }

	    $foodbakery_packages_list = array('' => esc_html__('Select Membership', 'foodbakery'));
	    $args = array('posts_per_page' => '-1', 'post_type' => 'packages', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
	    $cust_query = get_posts($args);
	    if (is_array($cust_query) && sizeof($cust_query) > 0) {
		foreach ($cust_query as $package_post) {
		    if (isset($package_post->ID)) {
			$package_id = $package_post->ID;
			$package_title = $package_post->post_title;
			$foodbakery_packages_list[$package_id] = $package_title;
		    }
		}
	    }


	    $foodbakery_calendar = get_post_meta($post_id, 'foodbakery_calendar', true);
	    $restaurant_types_data = array('' => foodbakery_plugin_text_srt('foodbakery_restaurant_type'));
	    $foodbakery_restaurant_args = array('posts_per_page' => '-1', 'post_type' => 'restaurant-type', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
	    $cust_query = get_posts($foodbakery_restaurant_args);
	    if (is_array($cust_query) && sizeof($cust_query) > 0) {
		foreach ($cust_query as $foodbakery_restaurant_type) {
		    $restaurant_types_data[$foodbakery_restaurant_type->post_name] = get_the_title($foodbakery_restaurant_type->ID);
		    $type_slug = $foodbakery_restaurant_type->post_name;
		}
	    }

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Logo', 'foodbakery'),
		'desc' => esc_html__('', 'foodbakery'),
		'hint_text' => '',
		'echo' => true,
		'feature_img' => true,
		'id' => 'cover_image',
		'std' => '',
		'field_params' => array(
		    'id' => 'cover_image',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);


	    $foodbakery_opt_array = array(
		'name' => esc_html__('Cover Image', 'foodbakery'),
		'desc' => esc_html__('', 'foodbakery'),
		'hint_text' => '',
		'echo' => true,
		'feature_img' => true,
		'id' => 'restaurant_cover_image',
		'std' => '',
		'field_params' => array(
		    'id' => 'restaurant_cover_image',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);


	    echo '<div style="display: none;">';
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_type'),
		'desc' => '',
		'hint_text' => foodbakery_plugin_text_srt('foodbakery_restaurant_type_hint'),
		'echo' => true,
		'field_params' => array(
		    'std' => $type_slug,
		    'id' => 'restaurant_type',
		    'extra_atr' => ' onchange="foodbakery_restaurant_type_change(this.value, \'' . $post_id . '\')"',
		    'classes' => 'chosen-select-no-single',
		    'return' => true,
		    'options' => $restaurant_types_data,
		),
	    );
	    $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
	    echo '</div>';

	    echo '<div id="foodbakery-restaurant-type-field">';
	    $this->restaurant_type_change_fields($restaurant_type_slug, $post_id);
	    echo '</div>';

	    $foodbakery_restaurant_cus_fields = get_option("foodbakery_restaurant_cus_fields");
	    if (is_array($foodbakery_restaurant_cus_fields) && sizeof($foodbakery_restaurant_cus_fields) > 0) {
		foreach ($foodbakery_restaurant_cus_fields as $cus_field) {
		    $foodbakery_type = isset($cus_field['type']) ? $cus_field['type'] : '';
		    switch ($foodbakery_type) {
			case('text'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => true,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'return' => true,
				    ),
				);

				$foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
			case('textarea'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => true,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'return' => true,
				    ),
				);

				$foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
			    }
			    break;
			case('dropdown'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_options = array();
				if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value']) && sizeof($cus_field['options']['value']) > 0) {
				    if (isset($cus_field['first_value']) && $cus_field['first_value'] != '') {
					$foodbakery_options[''] = $cus_field['first_value'];
				    }
				    $foodbakery_opt_counter = 0;
				    foreach ($cus_field['options']['value'] as $foodbakery_option) {

					$foodbakery_opt_label = $cus_field['options']['label'][$foodbakery_opt_counter];
					$foodbakery_options[$foodbakery_option] = $foodbakery_opt_label;
					$foodbakery_opt_counter ++;
				    }
				}

				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => true,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'options' => $foodbakery_options,
					'classes' => 'chosen-select-no-single',
					'cus_field' => true,
					'return' => true,
				    ),
				);

				if (isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'on') {
				    $foodbakery_opt_array['multi'] = true;
				}

				$foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			    }
			    break;
			case('date'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_format = isset($cus_field['date_format']) && $cus_field['date_format'] != '' ? $cus_field['date_format'] : 'd-m-Y';

				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => true,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'format' => $foodbakery_format,
					'cus_field' => true,
					'return' => true,
				    ),
				);

				$foodbakery_html_fields->foodbakery_date_field($foodbakery_opt_array);
			    }
			    break;
			case('email'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => true,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'return' => true,
				    ),
				);

				$foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
			case('url'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {

				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => true,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'return' => true,
				    ),
				);

				$foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
			case('range'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => true,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'classes' => 'foodbakery-range-field',
					'extra_atr' => 'data-min="' . $cus_field['min'] . '" data-max="' . $cus_field['max'] . '"',
					'return' => true,
				    ),
				);

				$foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
		    }
		}
	    }

	    do_action('foodbakery_indeed_restaurant_admin_fields');

	    $foodbakery_form_fields->foodbakery_form_hidden_render(
		    array(
			'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_organization'),
			'id' => 'org_name',
			'classes' => '',
			'std' => '',
			'description' => '',
			'hint' => ''
		    )
	    );
	    $foodbakery_html_fields->foodbakery_heading_render(
		    array(
			'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_mailing_information'),
			'id' => 'mailing_information',
			'classes' => '',
			'std' => '',
			'description' => '',
			'hint' => ''
		    )
	    );

	    FOODBAKERY_FUNCTIONS()->foodbakery_location_fields('', 'restaurant');
	    $foodbakery_html_fields->foodbakery_heading_render(
		    array(
			'name' => foodbakery_plugin_text_srt('restaurant_contact_heading'),
			'id' => 'contact_information',
			'classes' => '',
			'std' => '',
			'description' => '',
			'hint' => ''
		    )
	    );
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('restaurant_contact_email'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'restaurant_contact_email',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('restaurant_contact_phone'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'restaurant_contact_phone',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('restaurant_contact_web'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'restaurant_contact_web',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	}

	/**
	 * Start Function How to add form options in  html
	 */
	function foodbakery_package_info_options() {
	    global $post, $foodbakery_form_fields, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_options;
	    $post_id = $post->ID;
	    $foodbakery_restaurant_types = array();
	    $foodbakery_args = array('posts_per_page' => '-1', 'post_type' => 'restaurants_capacity', 'orderby' => 'ID', 'post_status' => 'publish');
	    $cust_query = get_posts($foodbakery_args);
	    $foodbakery_restaurant_capacity = get_post_meta($post->ID, 'foodbakery_restaurant_capacity', true);
	    $foodbakery_restaurant_featured = get_post_meta($post->ID, 'foodbakery_restaurant_featured', true);
	    $restaurant_type_slug = get_post_meta($post->ID, 'foodbakery_restaurant_type', true);
	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
	    $foodbakery_type_full_data = get_post_meta($restaurant_type_id, 'foodbakery_full_data', true);

	    $foodbakery_users_list = array('' => esc_html__('Select Publisher', 'foodbakery'));
	    $args = array('posts_per_page' => '-1', 'post_type' => 'publishers', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
	    $cust_query = get_posts($args);
	    if (is_array($cust_query) && sizeof($cust_query) > 0) {
		foreach ($cust_query as $package_post) {
		    if (isset($package_post->ID)) {
			$package_id = $package_post->ID;
			$package_title = $package_post->post_title;
			$foodbakery_users_list[$package_id] = $package_title;
		    }
		}
	    }

	    $foodbakery_packages_list = array('' => esc_html__('Select Membership', 'foodbakery'));
	    $args = array('posts_per_page' => '-1', 'post_type' => 'packages', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
	    $cust_query = get_posts($args);
	    if (is_array($cust_query) && sizeof($cust_query) > 0) {
		foreach ($cust_query as $package_post) {
		    if (isset($package_post->ID)) {
			$package_id = $package_post->ID;
			$package_title = $package_post->post_title;
			$foodbakery_packages_list[$package_id] = $package_title;
		    }
		}
	    }

	    $foodbakery_calendar = get_post_meta($post_id, 'foodbakery_calendar', true);

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('transaction_id'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'trans_id',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_posted_on'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'restaurant_posted',
		    'classes' => '',
		    'strtotime' => true,
		    'std' => '', //date('d-m-Y H:i:s'),
		    'description' => '',
		    'hint' => '',
		    'format' => 'd-m-Y H:i:s',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_date_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_expired_on'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '', //date('d-m-Y'),
		    'id' => 'restaurant_expired',
		    'format' => 'd-m-Y',
		    'strtotime' => true,
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_date_field($foodbakery_opt_array);

	    apply_filters('restaurant_hunt_application_deadline_field', '');

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_package'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'restaurant_package',
		    'classes' => 'chosen-select-no-single',
		    'options' => $foodbakery_packages_list,
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_status'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => '',
		    'id' => 'restaurant_status',
		    'classes' => 'chosen-select-no-single',
		    'options' => array('awaiting-activation' => foodbakery_plugin_text_srt('foodbakery_restaurant_awaiting_activation'), 'active' => foodbakery_plugin_text_srt('foodbakery_restaurant_active'), 'inactive' => foodbakery_plugin_text_srt('foodbakery_restaurant_inactive'), 'delete' => foodbakery_plugin_text_srt('foodbakery_restaurant_delete')),
		    'return' => true,
		),
	    );

	    $foodbakery_restaurant_status = get_post_meta($post->ID, 'foodbakery_restaurant_status', true);
	    $foodbakery_form_fields->foodbakery_form_hidden_render(
		    array(
			'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_old_status'),
			'id' => 'restaurant_old_status',
			'classes' => '',
			'std' => $foodbakery_restaurant_status,
			'description' => '',
			'hint' => ''
		    )
	    );

	    $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

	    // package assign data
	    $foodbakery_opt_array = array(
		'name' => esc_html__('Featured', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'restaurant_is_featured',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Top Category', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'restaurant_is_top_cat',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Number of Tags', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'transaction_restaurant_tags_num',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Reviews', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'transaction_restaurant_reviews',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Phone Number', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'transaction_restaurant_phone',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Website Link', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'transaction_restaurant_website',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Social Impressions Reach', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'transaction_restaurant_social',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Respond to Reviews', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'id' => 'transaction_restaurant_ror',
		    'classes' => '',
		    'std' => '',
		    'description' => '',
		    'hint' => '',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

	    $trans_dynamic_values = get_post_meta($post_id, 'foodbakery_transaction_dynamic', true);
	    if (is_array($trans_dynamic_values) && sizeof($trans_dynamic_values) > 0) {
		foreach ($trans_dynamic_values as $trans_dynamic) {
		    if (isset($trans_dynamic['field_type']) && isset($trans_dynamic['field_label']) && isset($trans_dynamic['field_value'])) {
			$d_type = $trans_dynamic['field_type'];
			$d_label = $trans_dynamic['field_label'];
			$d_value = $trans_dynamic['field_value'];
			if ($d_type == 'single-choice') {
			    $d_value = $d_value == 'on' ? esc_html__('Yes', 'foodbakery') : esc_html__('No', 'foodbakery');
			}

			echo '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . $d_label . '</label></div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">' . $d_value . '</div></div>' . "\n";
		    }
		}
		// end foreach
	    }
	    // package assign data

	    /*
	     * Fields for restaurant Posted by
	     */
	    do_action('foodbakery_posted_by_admin_fields');
	}

	public function restaurant_type_change_fields($restaurant_type_slug = 0, $post_id = 0) {
	    if (isset($_POST['restaurant_type_slug'])) {
		$restaurant_type_slug = $_POST['restaurant_type_slug'];
	    }
	    if (isset($_POST['post_id'])) {
		$post_id = $_POST['post_id'];
	    }
	    $html = '';

	    $html .= $this->restaurant_categories($restaurant_type_slug, $post_id);
	    $html .= $this->restaurant_tags($restaurant_type_slug, $post_id);
	    $html .= $this->restaurant_fields($restaurant_type_slug, $post_id);
	    $html .= $this->restaurant_type_dyn_fields($restaurant_type_slug);

	    $html .= $this->restaurant_opening_hours($restaurant_type_slug, $post_id);

	    $html .= apply_filters('foodbakery_admin_restaurant_menu_items', $post_id);

	    if (isset($_POST['restaurant_type_slug'])) {
		echo json_encode(array('restaurant_fields' => $html));
		die;
	    } else {
		echo force_balance_tags($html);
	    }
	}

	public function restaurant_menu_cats() {
	    global $post;

	    $restaurant_id = $post->ID;

	    $menu_item_counter = rand(111456789, 987654321);
	    $html = '';
	    $menu_items_list = '';

	    $menu_items_list .= $this->group_restaurant_menu_cats($restaurant_id);

	    if ($menu_items_list == '') {
		$menu_items_list = '<li id="no-menu-cats-' . $menu_item_counter . '" class="no-result-msg">' . esc_html__('No Menu Category added.', 'foodbakery') . '</li>';
	    }

	    $html .= '
			<div class="theme-help">
				<h4 style="padding-bottom:0px;">' . esc_html__('Menu Categories', 'foodbakery') . '</h4>
				<div class="clear"></div>
			</div>

			<div class="form-elements">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<div id="menu-cats-loader-' . $menu_item_counter . '" class="restaurant-loader"></div>
						<a class="add-menu-item" href="javascript:void(0);" onClick="javascript:foodbakery_add_menu_cat(\'' . $menu_item_counter . '\');">' . esc_html__('Add Menu Category', 'foodbakery') . '</a>
					</div>
				</div>
				<div id="add-menu-cat-from-' . $menu_item_counter . '" style="display:none;">';
	    $html .= $this->foodbakery_restaurant_cat_form('', $menu_item_counter, 'add');
	    $html .= '</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<div class="service-list">
						<div class="menu-items-list-holder">
							<ul id="restaurant-cats-list-' . $menu_item_counter . '" class="restaurant-menu-cats-list">
								' . $menu_items_list . '
							</ul>
						</div>
						</div>
					</div>
				</div>
			</div>';
	    echo force_balance_tags($html);
	}

	public function foodbakery_restaurant_cat_form($get_menu_cat_vals, $menu_item_counter, $doin_action = 'add') {
	    $form_html = '';
	    if ($doin_action == 'edit') {
		$add_btn_txt = esc_html__('Save', 'foodbakery');
		$title_name_value = ' name="menu_cat_title[]" value="' . (isset($get_menu_cat_vals['menu_cat_title']) ? $get_menu_cat_vals['menu_cat_title'] : '') . '"';
		$desc_name = ' name="menu_cat_desc[]"';
		$desc_val = isset($get_menu_cat_vals['menu_cat_desc']) ? $get_menu_cat_vals['menu_cat_desc'] : '';
		$add_btn_func = ' onClick="foodbakery_close_menu_cat(\'' . $menu_item_counter . '\');"';
	    } else {
		$add_btn_txt = esc_html__('Add Category', 'foodbakery');
		$title_name_value = '';
		$desc_name = '';
		$desc_val = '';
		$add_btn_func = ' onClick="foodbakery_admin_add_menu_cat_to_list(\'' . $menu_item_counter . '\');"';
	    }
	    $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	    $form_html .= '<a href="javascript:void(0);" onClick="foodbakery_close_menu_cat(\'' . $menu_item_counter . '\');" class="close-menu-item"><i class="icon-close"></i></a>';
	    $form_html .= '<div class="row">';
	    $form_html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Menu Name *', 'foodbakery') . '</label>
					<input class="menu-item-title" id="menu_item_title_' . $menu_item_counter . '"' . $title_name_value . ' type="text" placeholder="' . esc_html__('Menu Category Title', 'foodbakery') . '">	
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Description', 'foodbakery') . '</label>
					<textarea class="menu-item-desc" id="menu_item_desc_' . $menu_item_counter . '"' . $desc_name . ' placeholder="' . esc_html__('Category Description', 'foodbakery') . '">' . $desc_val . '</textarea>
				</div>
			</div>';
	    $form_html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<a class="add-menu-item add-menu-item-list" href="javascript:void(0);"' . $add_btn_func . '>' . $add_btn_txt . '</a>
				</div>
			</div>';
	    $form_html .= '</div>';
	    $form_html .= '</div>';

	    return $form_html;
	}

	public function foodbakery_restaurant_menu_cat_item($menu_item_counter = '', $menu_cat_vals = array()) {
	    $item_html = '';
	    if (isset($_POST['_menu_cat_title'])) {
		$menu_cat_title = $_POST['_menu_cat_title'];
		$menu_cat_desc = isset($_POST['_menu_cat_desc']) ? $_POST['_menu_cat_desc'] : '';
		$menu_item_counter = rand(1100000, 99999999);
	    } else {
		extract($menu_cat_vals);
	    }

	    $get_menu_cat_vals = array(
		'menu_cat_title' => $menu_cat_title,
		'menu_cat_desc' => $menu_cat_desc,
	    );

	    $item_html .= '
			<li class="menu-item-' . $menu_item_counter . '">
				<div class="drag-list">
					<span class="drag-option"><i class="icon-bars"></i></span>
					<div class="list-title">
						<h6>' . $menu_cat_title . '</h6>
					</div>
					<div class="list-option">
						<a href="javascript:void(0);" class="edit-menu-item" onclick="foodbakery_add_menu_cat(\'' . $menu_item_counter . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>
						<a href="javascript:void(0);" class="remove-menu-item" onclick="foodbakery_remove_menu_item(\'' . $menu_item_counter . '\');"><i class="icon-cross-out"></i></a>
					</div>
				</div>
				<div id="add-menu-cat-from-' . $menu_item_counter . '" style="display: none;">
					' . $this->foodbakery_restaurant_cat_form($get_menu_cat_vals, $menu_item_counter, 'edit') . '
				</div>
			</li>';

	    if (isset($_POST['_menu_cat_title'])) {
		echo json_encode(array('html' => $item_html));
		die;
	    } else {
		return $item_html;
	    }
	}

	public function group_restaurant_menu_cats($restaurant_id) {
	    $restaurant_menu_cat_titles = get_post_meta($restaurant_id, 'menu_cat_titles', true);
	    $restaurant_menu_cat_descs = get_post_meta($restaurant_id, 'menu_cat_descs', true);

	    $html = '';
	    if (is_array($restaurant_menu_cat_titles) && sizeof($restaurant_menu_cat_titles) > 0) {
		$cat_counter = 0;
		foreach ($restaurant_menu_cat_titles as $cat_title) {
		    $menu_item_counter = rand(1100000, 99999999);
		    $cat_desc = isset($restaurant_menu_cat_descs[$cat_counter]) ? $restaurant_menu_cat_descs[$cat_counter] : '';

		    $get_menu_cat_vals = array(
			'menu_cat_title' => $cat_title,
			'menu_cat_desc' => $cat_desc,
		    );
		    $html .= $this->foodbakery_restaurant_menu_cat_item($menu_item_counter, $get_menu_cat_vals);

		    $cat_counter ++;
		}
	    }
	    return $html;
	}

	public function foodbakery_save_restaurant_features($post_id) {

	    $foodbakery_restaurant_feature_list = isset($_POST['foodbakery_restaurant_feature_list']) ? $_POST['foodbakery_restaurant_feature_list'] : '';
	    update_post_meta($post_id, 'foodbakery_restaurant_feature_list', $foodbakery_restaurant_feature_list);
	}

	function restaurant_price($restaurant_type_slug = 0, $post_id = 0) {
	    global $post, $foodbakery_html_fields;
	    $restaurant_type_post = get_posts(array('fields' => 'ids', 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]) && $restaurant_type_post[0] != '' ? $restaurant_type_post[0] : 0;
	    $foodbakery_restaurant_type_price = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_price', true);
	    $foodbakery_restaurant_type_price = isset($foodbakery_restaurant_type_price) && $foodbakery_restaurant_type_price != '' ? $foodbakery_restaurant_type_price : 'off';
	    $html = '';
	    if ($foodbakery_restaurant_type_price == 'on') {
		$foodbakery_opt_array = array(
		    'name' => esc_html__('Restaurant Price Option'),
		    'desc' => '',
		    'hint_text' => '',
		    'echo' => false,
		    'field_params' => array(
			'std' => '',
			'extra_atr' => 'onchange="foodbakery_restaurant_price_change(this.value)"',
			'id' => 'restaurant_price_options',
			'classes' => 'chosen-select-no-single ',
			'options' => array('none' => 'None', 'on-call' => 'On Call', 'price' => 'Price',),
			'return' => true,
		    ),
		);
		$html .= "
				<script>
					function foodbakery_restaurant_price_change(price_selection) {
						if (price_selection == 'none' || price_selection == 'on-call') {
							jQuery('.dynamic_price_field').hide();
						} else {
							jQuery('.dynamic_price_field').show();
						}
					}
				</script>";

		$foodbakery_restaurant_price_options = get_post_meta($post->ID, 'foodbakery_restaurant_price_options', true);
		$foodbakery_restaurant_price_options = isset($foodbakery_restaurant_price_options) ? $foodbakery_restaurant_price_options : '';
		$hide_div = '';
		if ($foodbakery_restaurant_price_options == '' || $foodbakery_restaurant_price_options == 'none' || $foodbakery_restaurant_price_options == 'on-call') {
		    $hide_div = 'style="display:none;"';
		}
		$foodbakery_opt_array = array(
		    'name' => esc_html__('Restaurant Price', 'foodbakery'),
		    'desc' => '',
		    'hint_text' => '',
		    'echo' => false,
		    'field_params' => array(
			'std' => '',
			'classes' => 'foodbakery-number-field ',
			'id' => 'restaurant_price',
			'return' => true,
		    ),
		);
		$html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	    }

	    return $html;
	}

	function restaurant_categories($restaurant_type_slug = 0, $post_id = 0, $backend = true, $custom = false) {
	    global $post, $foodbakery_html_fields, $foodbakery_plugin_static_text, $foodbakery_form_fields;
	    $html = '';
	    wp_enqueue_script('foodbakery-restaurant-categories');
	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_slug = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

	    $foodbakery_restaurant_type_category_array = get_the_terms($restaurant_type_slug, 'restaurant-category');
	    $foodbakery_restaurant_type_categories = array();
	    $foodbakery_restaurant_type_categories[''] = foodbakery_plugin_text_srt('foodbakery_restaurant_select_categories');
	    if (is_array($foodbakery_restaurant_type_category_array) && sizeof($foodbakery_restaurant_type_category_array) > 0) {
		foreach ($foodbakery_restaurant_type_category_array as $in_category) {
		    $foodbakery_restaurant_type_categories[$in_category->term_id] = $in_category->name;
		}
	    }
	    if (!isset($foodbakery_restaurant_type_categories) || !is_array($foodbakery_restaurant_type_categories) || !count($foodbakery_restaurant_type_categories) > 0) {
		$foodbakery_restaurant_type_categories = array();
	    }

	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

	    $foodbakery_restaurant_type_category = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_categories', true);

	    if (!isset($foodbakery_restaurant_type_category) || !is_array($foodbakery_restaurant_type_category) || !count($foodbakery_restaurant_type_category) > 0) {
		$foodbakery_restaurant_type_category = array();
	    }
	    $foodbakery_multi_cat_option = 'on';

	    $args = array(
		'type' => 'post',
		'child_of' => 0,
		'parent' => '',
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0,
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'number' => '',
		'taxonomy' => 'restaurant-category',
		'pad_counts' => false
	    );

	    $categories = get_categories($args);

	    $multiple = false;
	    if ($foodbakery_multi_cat_option == 'on') {
		$multiple = true;
	    }
	    $restaurant_type_cats = array();
	    $tax_slug_array = get_post_meta($post_id, 'foodbakery_restaurant_category', true);
	    if (!$custom) {
		$restaurant_type_cats = array('' => esc_html__('ALL Categories', 'foodbakery'));
	    }
	    $foodbakery_restaurant_type_cats = get_post_meta($restaurant_type_slug, 'foodbakery_restaurant_type_cats', true);
	    if (isset($foodbakery_restaurant_type_cats) && !empty($foodbakery_restaurant_type_cats)) {
		foreach ($foodbakery_restaurant_type_cats as $foodbakery_restaurant_type_cat) {
		    $term = get_term_by('slug', $foodbakery_restaurant_type_cat, 'restaurant-category');
		    if (!empty($term)) {
			$restaurant_type_cats[$term->slug] = $term->name;
		    }
		}
	    }
	    $foodbakery_restaurant_category_val = get_post_meta($post_id, 'foodbakery_restaurant_category', true);

	    $foodbakery_restaurant_selected_value = isset($foodbakery_restaurant_category_val['parent']) && $foodbakery_restaurant_category_val['parent'] != '' ? $foodbakery_restaurant_category_val['parent'] : '';
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_categories'),
		'desc' => '',
		'hint_text' => '',
		'multi' => $multiple,
		'echo' => false,
		'field_params' => array(
		    'id' => 'foodbakery_restaurant_category',
		    'force_std' => true,
		    'std' => $tax_slug_array,
		    'cust_name' => 'foodbakery_restaurant_category[]',
		    'classes' => 'chosen-select foodbakery-dev-req-field',
		    'extra_atr' => '',
		    'options' => $restaurant_type_cats,
		    'return' => true,
		),
	    );

	    $foodbakery_opt_array_frontend = array(
		'id' => 'foodbakery_restaurant_category',
		'force_std' => true,
		'std' => $tax_slug_array,
		'multi' => $multiple,
		'cust_name' => 'foodbakery_restaurant_category[]',
		'classes' => 'chosen-select foodbakery-dev-req-field',
		'extra_atr' => '',
		'options' => $restaurant_type_cats,
		'return' => true,
	    );
	    if ($custom) {
		$foodbakery_opt_array_frontend['extra_atr'] = ' data-placeholder="' . esc_html__('Choose some Cuisines', 'foodbakery') . '"';
	    }
	    if ($backend == true) {
		$html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
	    } else {
		if ($custom) {
		    $html .= '<div class="field-holder"><label>' . esc_html__('Cuisines *', 'foodbakery') . '</label>';
		} else {
		    $html .= '<div class="field-holder"><label>' . esc_html__('Categories *', 'foodbakery') . '</label>';
		}
		$html .= $foodbakery_form_fields->foodbakery_form_multiselect_render($foodbakery_opt_array_frontend);
		$html .= '</div>';
	    }


	    $html .= '<div class = "foodbakery_restaurant_category_field">';
	    $html .= '</div>';

	    return $html;
	}

	public function foodbakery_meta_restaurant_categories($restaurant_arg = '') {

	    global $foodbakery_html_fields;
	    $html = '';
	    $selected_val = foodbakery_get_input('selected_val', '', 'STRING');
	    $load_saved_value = foodbakery_get_input('load_saved_value', '', 'STRING');

	    $foodbakery_restaurant_category = foodbakery_get_input('foodbakery_restaurant_category', '', 'STRING');
	    $foodbakery_restaurant_category_hidden = isset($foodbakery_restaurant_category) && $foodbakery_restaurant_category != '' ? unserialize(( $foodbakery_restaurant_category)) : '';

	    $post_id = foodbakery_get_input('post_id', '', 'STRING');
	    $foodbakery_restaurant_category_val = get_post_meta($post_id, 'foodbakery_restaurant_category', true);
	    if ($selected_val != '') { // if selected value is empty
		$foodbakery_restaurant_selected_value = isset($foodbakery_restaurant_category_val[$selected_val]) && $foodbakery_restaurant_category_val[$selected_val] != '' ? $foodbakery_restaurant_category_val[$selected_val] : '';

		$single_term = get_term_by('slug', $selected_val, 'restaurant-category');
		$single_term_id = isset($single_term->term_id) && $single_term->term_id != '' ? $single_term->term_id : '';
		$single_term_name = isset($single_term->name) && $single_term->name != '' ? $single_term->name : '';
		if ($single_term_id != '' || $single_term_id != 0) { //if geiven value not correct or not return id
		    $cate_arg = array(
			'hide_empty' => false,
			'parent' => $single_term_id,
		    );

		    $foodbakery_category_array = get_terms('restaurant-category', $cate_arg);

		    $restaurant_type_cats = array('test' => 'ALL ' . $single_term_name);
		    if (is_array($foodbakery_category_array) && sizeof($foodbakery_category_array) > 0) {
			foreach ($foodbakery_category_array as $dir_tag) {
			    $restaurant_type_cats[$dir_tag->slug] = $dir_tag->name;
			}
			$foodbakery_opt_array = array(
			    'name' => $single_term_name . ' ' . foodbakery_plugin_text_srt('foodbakery_restaurant_categories'),
			    'desc' => '',
			    'hint_text' => '',
			    'echo' => false,
			    'field_params' => array(
				'std' => $foodbakery_restaurant_selected_value,
				'cust_name' => 'foodbakery_restaurant_category[' . $selected_val . ']',
				'classes' => 'chosen-select',
				'extra_atr' => ' onchange="foodbakery_load_category_models(this.value,\'' . $post_id . '\', \'foodbakery_restaurant_category_field' . $selected_val . '\', \'0\')"',
				'options' => $restaurant_type_cats,
				'return' => true,
			    ),
			);

			$html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			$html .= '<div class="foodbakery_restaurant_category_field' . $selected_val . '">';
			$html .= '</div>';

			if ((isset($load_saved_value) && $load_saved_value == '1' ) && $foodbakery_restaurant_category_val != '') {

			    $html .= '<script>';

			    $html .= 'foodbakery_load_category_models(\'' . $foodbakery_restaurant_selected_value . '\',\'' . $post_id . '\', \'foodbakery_restaurant_category_field' . $selected_val . '\', ' . $load_saved_value . ');';

			    $html .= '</script>';
			}
		    }
		}
	    }// selected value is empty check

	    $output = array('html' => $html,);
	    echo json_encode($output);

	    wp_die();
	}

	function restaurant_tags($restaurant_type_slug = 0, $post_id = 0) {
	    global $post, $foodbakery_html_fields, $foodbakery_plugin_static_text;
	    $html = '';

	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_slug = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

	    $foodbakery_restaurant_type_tags = get_post_meta($restaurant_type_slug, 'foodbakery_restaurant_type_tags', true);


	    $foodbakery_tags_array = get_terms('restaurant-tag', array(
		'hide_empty' => false,
	    ));
	    $foodbakery_tags_list = array();
	    if (is_array($foodbakery_tags_array) && sizeof($foodbakery_tags_array) > 0) {
		foreach ($foodbakery_tags_array as $dir_tag) {
		    $foodbakery_tags_list[$dir_tag->slug] = $dir_tag->name;
		}
	    }

	    $foodbakery_restaurant_type_tags = get_post_meta($post_id, 'foodbakery_restaurant_type_tags', true);

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_select_suggested_tags'),
		'desc' => '',
		'hint_text' => foodbakery_plugin_text_srt('foodbakery_select_suggested_tags_hint'),
		'multi' => true,
		'desc' => sprintf(foodbakery_plugin_text_srt('foodbakery_add_new_tag_link'), admin_url('edit-tags.php?taxonomy=restaurant-tag&post_type=restaurants', foodbakery_server_protocol())),
		'field_params' => array(
		    'std' => '', $foodbakery_restaurant_type_tags,
		    'id' => 'tags',
		    'classes' => 'chosen-select-no-single chosen-select',
		    'options' => $foodbakery_tags_list,
		    'return' => true,
		),
	    );

	    $html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

	    return $html;
	}

	function restaurant_fields($restaurant_type_slug = 0, $post_id = 0) {
	    global $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;

	    $html = '';
	    $foodbakery_time_options = array();
	    $foodbakery_opt_array = array(
		'name' => esc_html__('Table Booking', 'foodbakery'),
		'desc' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'chosen-select',
		    'id' => 'restaurant_table_booking',
		    'options' => array(
			'yes' => esc_html__('Yes', 'foodbakery'),
			'no' => esc_html__('No', 'foodbakery'),
		    ),
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Delivery/Pickup', 'foodbakery'),
		'desc' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'chosen-select',
		    'id' => 'restaurant_pickup_delivery',
		    'options' => array(
			'delivery' => esc_html__('Delivery', 'foodbakery'),
			'pickup' => esc_html__('Pickup', 'foodbakery'),
			'delivery_and_pickup' => esc_html__('Delivery &amp; Pickup', 'foodbakery'),
		    ),
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Minimum Delivery Order', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'foodbakery-order-value-field ',
		    'id' => 'minimum_order_value',
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);



	    $foodbakery_opt_array = array(
		'name' => esc_html__('Maximum Delivery Order', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'foodbakery-order-value-field ',
		    'id' => 'maximum_order_value',
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Minimum Pickup Order', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'foodbakery-order-value-field ',
		    'id' => 'minimum_pickup_order_value',
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


	    $foodbakery_opt_array = array(
		'name' => esc_html__('Maximum Pickup Order', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'foodbakery-order-value-field ',
		    'id' => 'maximum_pickup_order_value',
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);



	    $foodbakery_opt_array = array(
		'name' => esc_html__('Delivery Fee', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'foodbakery-delivery-fee-field ',
		    'id' => 'delivery_fee',
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('PickUp Fee', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'foodbakery-pickup-fee-field ',
		    'id' => 'pickup_fee',
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Delivery Time', 'foodbakery'),
		'desc' => '',
		'hint_text' => esc_html__('Please provide Delivery Time in minutes.', 'foodbakery'),
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'foodbakery-delivery-fee-field ',
		    'id' => 'delivery_time',
		    'return' => true,
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('PickUp Time', 'foodbakery'),
		'desc' => '',
		'hint_text' => esc_html__('Please provide PickUp Time in minutes.', 'foodbakery'),
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'id' => 'restaurant_pickup_time',
		    'return' => true,
		),
	    );

	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Pre-Order', 'foodbakery'),
		'desc' => '',
		'hint_text' => esc_html__('Select yes if you allow users pre orders.', 'foodbakery'),
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'classes' => 'chosen-select',
		    'id' => 'restaurant_pre_order',
		    'options' => array(
			'no' => esc_html__('No', 'foodbakery'),
			'yes' => esc_html__('Yes', 'foodbakery'),
		    ),
		    'return' => true,
		),
	    );

	    $html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
            $html = apply_filters('foodbakery_imran_sound_field_backend',$html);
	    return $html;
	}

	function restaurant_off_days($restaurant_type_slug = 0, $post_id = 0) {
	    global $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;

	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

	    $foodbakery_off_days = get_post_meta($restaurant_type_id, 'foodbakery_off_days', true);
	    if ($foodbakery_off_days == 'on') {
		$html = $foodbakery_html_fields->foodbakery_heading_render(
			array(
			    'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_off_days'),
			    'id' => 'off_days',
			    'classes' => '',
			    'std' => '',
			    'description' => '',
			    'hint' => '',
			    'echo' => false
			)
		);
		$date_js = '';
		if (isset($foodbakery_calendar) && !empty($foodbakery_calendar)) {
		    foreach ($foodbakery_calendar as $calender_date) {
			$calender_date = strtotime($calender_date);
			$dateVal = date("Y, m, d", strtotime('-1 month', $calender_date));
			$date_js .= '{
							startDate: new Date(' . $dateVal . '),
							endDate: new Date(' . $dateVal . ')
						},';
		    }
		}
		$html .= $this->restaurant_book_days_off();
		return $html;
	    }
	}

	function restaurant_opening_hours($restaurant_type_slug = 0, $post_id = 0) {
	    global $restaurant_add_counter, $foodbakery_html_fields;

	    $restaurant_add_counter = rand(10000000, 99999999);
	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

	    $html = '';
	    $foodbakery_restaurant_opening_hours = get_post_meta($restaurant_type_id, 'foodbakery_opening_hours_element', true);
	    if ($foodbakery_restaurant_opening_hours == 'on') {

		$time_list = $this->restaurant_time_list($restaurant_type_id);
		$week_days = $this->restaurant_week_days();

		$time_from_html = '';
		$time_to_html = '';

		$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
		$get_opening_hours = array();
		foreach ($days as $key => $day) {
		    $opening_time = get_post_meta($post_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true);
		    $opening_time = ( $opening_time != '' ? date('h:i a', $opening_time) : '' );
		    $closing_time = get_post_meta($post_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true);
		    $closing_time = ( $opening_time != '' ? date('h:i a', $closing_time) : '' );
		    $get_opening_hours[$day] = array(
			'day_status' => get_post_meta($post_id, 'foodbakery_opening_hours_' . $day . '_day_status', true),
			'opening_time' => $opening_time,
			'closing_time' => $closing_time,
		    );
		}

		if ($get_opening_hours == '') {
		    if (is_array($time_list) && sizeof($time_list) > 0) {
			foreach ($time_list as $time_key => $time_val) {
			    $time_from_html .= '<option value="' . $time_key . '">' . $time_val . '</option>' . "\n";
			    $time_to_html .= '<option value="' . $time_key . '">' . $time_val . '</option>' . "\n";
			}
		    }
		}

		$days_html = '';
		if (is_array($week_days) && sizeof($week_days) > 0) {
		    foreach ($week_days as $day_key => $week_day) {

			$day_status = get_post_meta($post_id, 'foodbakery_opening_hours_' . $day_key . '_day_status', true);
			if (isset($get_opening_hours) && is_array($get_opening_hours) && sizeof($get_opening_hours) > 0) {
			    $opening_time = get_post_meta($post_id, 'foodbakery_opening_hours_' . $day_key . '_opening_time', true);
			    $opening_time = ( $opening_time != '' ? date('h:i a', $opening_time) : '' );
			    $closing_time = get_post_meta($post_id, 'foodbakery_opening_hours_' . $day_key . '_closing_time', true);
			    $closing_time = ( $opening_time != '' ? date('h:i a', $closing_time) : '' );

			    if (is_array($time_list) && sizeof($time_list) > 0) {
				$time_from_html = '';
				$time_to_html = '';
				foreach ($time_list as $time_key => $time_val) {
				    $time_from_html .= '<option value="' . $time_key . '"' . ($opening_time == $time_key ? ' selected="selected"' : '') . '>' . $time_val . '</option>' . "\n";
				    $time_to_html .= '<option value="' . $time_key . '"' . ($closing_time == $time_key ? ' selected="selected"' : '') . '>' . $time_val . '</option>' . "\n";
				}
			    }
			}
			$days_html .= '
						<li>
							<div id="open-close-con-' . $day_key . '-' . $restaurant_add_counter . '" class="open-close-time' . (isset($day_status) && $day_status == 'on' ? ' opening-time' : '') . '">
								<div class="day-sec">
									<span>' . $week_day . '</span>
								</div>
								<div class="time-sec">
									<select class="chosen-select " name="foodbakery_opening_hour[' . $day_key . '][opening_time]">
										' . $time_from_html . '
									</select>
									<span class="option-label">' . esc_html__('to', 'foodbakery') . '</span>
									<select class="chosen-select " name="foodbakery_opening_hour[' . $day_key . '][closing_time]">
										' . $time_to_html . '
									</select>
									<a id="foodbakery-dev-close-time-' . $day_key . '-' . $restaurant_add_counter . '" href="javascript:void(0);" data-id="' . $restaurant_add_counter . '" data-day="' . $day_key . '" title="' . esc_html__('Close', 'foodbakery') . '"><i class="icon-cross-out"></i></a>
								</div>
								<div class="close-time">
									<a id="foodbakery-dev-open-time-' . $day_key . '-' . $restaurant_add_counter . '" href="javascript:void(0);" data-id="' . $restaurant_add_counter . '" data-day="' . $day_key . '">' . esc_html__('Closed', 'foodbakery') . ' <span>(' . esc_html__('Click to add opening  Hours', 'foodbakery') . ')</span></a>
									<input id="foodbakery-dev-open-day-' . $day_key . '-' . $restaurant_add_counter . '" type="hidden" name="foodbakery_opening_hour[' . $day_key . '][day_status]"' . (isset($day_status) && $day_status == 'on' ? ' value="on"' : '') . '>
								</div>
							</div>
						</li>';
		    }
		}
		$html .= $foodbakery_html_fields->foodbakery_heading_render(
			array(
			    'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_opening_hours'),
			    'id' => 'opening_hours',
			    'classes' => '',
			    'std' => '',
			    'description' => '',
			    'hint' => '',
			    'echo' => false
			)
		);
		$html .= '
				<div class="form-elements">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<label>' . esc_html__('Opening Hours', 'foodbakery') . '</label>
					</div>
					<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
						<div class="time-list">
							<ul>
								' . $days_html . '
							</ul>
						</div>
					</div>
				</div>';

		return $html;
	    }
	}

	function restaurant_type_dyn_fields($restaurant_type_slug = 0) {
	    global $foodbakery_html_fields, $foodbakery_plugin_static_text;
	    $foodbakery_fields_output = '';
	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
	    $foodbakery_restaurant_type_cus_fields = get_post_meta($restaurant_type_id, "foodbakery_restaurant_type_cus_fields", true);
	    $foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_heading_render(
		    array(
			'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_custom_fields'),
			'id' => 'foodbakery_fields_section',
			'classes' => '',
			'std' => '',
			'description' => '',
			'hint' => '',
			'echo' => false
		    )
	    );
	    if (is_array($foodbakery_restaurant_type_cus_fields) && sizeof($foodbakery_restaurant_type_cus_fields) > 0) {
		foreach ($foodbakery_restaurant_type_cus_fields as $cus_field) {
		    $foodbakery_type = isset($cus_field['type']) ? $cus_field['type'] : '';
		    $required_class = '';
		    if (isset($cus_field['required']) && $cus_field['required'] == 'on') {
			$required_class = 'foodbakery-dev-req-field-admin';
		    }
		    switch ($foodbakery_type) {
			case('section'):


			    break;
			case('text'):

			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'classes' => $required_class,
					'return' => true,
				    ),
				);

				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;

			case('number'):

			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'classes' => 'foodbakery-number-field ' . $required_class,
					'cust_type' => 'number',
					'return' => true,
				    ),
				);

				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
			case('textarea'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'classes' => $required_class,
					'cus_field' => true,
					'return' => true,
				    ),
				);
				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
			    }
			    break;
			case('dropdown'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_options = array();
				if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value']) && sizeof($cus_field['options']['value']) > 0) {
				    if (isset($cus_field['first_value']) && $cus_field['first_value'] != '') {
					$foodbakery_options[''] = $cus_field['first_value'];
				    }
				    $foodbakery_opt_counter = 0;
				    foreach ($cus_field['options']['value'] as $foodbakery_option) {

					$foodbakery_opt_label = $cus_field['options']['label'][$foodbakery_opt_counter];
					$foodbakery_options[$foodbakery_option] = $foodbakery_opt_label;
					$foodbakery_opt_counter ++;
				    }
				}

				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'options' => $foodbakery_options,
					'classes' => 'chosen-select-no-single ' . $required_class,
					'cus_field' => true,
					'return' => true,
				    ),
				);
				if (isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'on') {
				    $foodbakery_opt_array['multi'] = true;
				}
				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			    }
			    break;
			case('date'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_format = isset($cus_field['date_format']) && $cus_field['date_format'] != '' ? $cus_field['date_format'] : 'd-m-Y';
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'format' => $foodbakery_format,
					'classes' => 'foodbakery-date-field ' . $required_class,
					'cus_field' => true,
					'strtotime' => true,
					'return' => true,
				    ),
				);
				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_date_field($foodbakery_opt_array);
			    }
			    break;
			case('email'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'classes' => 'foodbakery-email-field ' . $required_class,
					'cus_field' => true,
					'return' => true,
				    ),
				);
				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
			case('url'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {

				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'classes' => 'foodbakery-url-field ' . $required_class,
					'cus_field' => true,
					'return' => true,
				    ),
				);
				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
			case('range'):
			    if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
				$foodbakery_opt_array = array(
				    'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
				    'desc' => '',
				    'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
				    'echo' => false,
				    'field_params' => array(
					'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
					'id' => $cus_field['meta_key'],
					'cus_field' => true,
					'classes' => 'foodbakery-range-field ' . $required_class,
					'extra_atr' => 'data-min="' . $cus_field['min'] . '" data-max="' . $cus_field['max'] . '"',
					'return' => true,
				    ),
				);
				$foodbakery_fields_output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    }
			    break;
		    }
		}
		$foodbakery_fields_output .= '
                    <script>
                    jQuery(document).ready(function () {
                        chosen_selectionbox();
                    });
                    </script>';
	    } else {
		$foodbakery_fields_output .= '<div class="custom-field-error">';
		$foodbakery_fields_output .= foodbakery_plugin_text_srt('foodbakery_restaurant_no_custom_field_found');
		$foodbakery_fields_output .= '</div>';
	    }

	    return $foodbakery_fields_output;
	}

	function feature_fields($restaurant_type_slug = 0, $post_id = 0) {
	    global $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;

	    $foodbakery_restaurant_features = get_post_meta($post_id, 'foodbakery_restaurant_feature_list', true);

	    $foodbakery_restaurant_features_array = array();
	    if (!empty($foodbakery_restaurant_features)) {
		foreach ($foodbakery_restaurant_features as $feature) {
		    if ($feature != '') {
			$explode_data = explode("_icon", $feature);
			$feature_name = $explode_data[0];
			$foodbakery_restaurant_features_array[] = $feature_name;
		    }
		}
	    }
	    $html = $foodbakery_html_fields->foodbakery_heading_render(
		    array(
			'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_features'),
			'id' => 'features_information',
			'classes' => '',
			'std' => '',
			'description' => '',
			'hint' => '',
			'echo' => false
		    )
	    );

	    $html .= $foodbakery_html_fields->foodbakery_opening_field(array('name' => foodbakery_plugin_text_srt('foodbakery_restaurant_features')));

	    $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
	    $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
	    $foodbakery_get_features = get_post_meta($restaurant_type_id, 'feature_lables', true);
	    $foodbakery_feature_icon = get_post_meta($restaurant_type_id, 'foodbakery_feature_icon', true);


	    if (is_array($foodbakery_get_features) && sizeof($foodbakery_get_features) > 0) {

		$html .= '<ul class="checkbox-list">';
		foreach ($foodbakery_get_features as $feat_key => $features) {
		    $feat_rand = rand(1000000, 99999999);
		    if (isset($features) && $features <> '') {
			$foodbakery_feature_name = isset($features) ? $features : '';
			$icon = isset($foodbakery_feature_icon[$feat_key]) ? $foodbakery_feature_icon[$feat_key] : '';
			$html .= '
						<li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<input id="feat-' . $feat_rand . '" ' . (is_array($foodbakery_restaurant_features) && in_array($foodbakery_feature_name, $foodbakery_restaurant_features_array) ? ' checked="checked"' : '') . ' type="checkbox" value="' . $foodbakery_feature_name . "_icon" . $icon . '" name="foodbakery_restaurant_feature_list[]"><label for="feat-' . $feat_rand . '">  ' . $foodbakery_feature_name . '  <i class="' . $icon . '"></i></label>
						</li>';
		    }
		}
		$html .= '</ul>';
	    }

	    $html .= $foodbakery_html_fields->foodbakery_closing_field(array());

	    return $html;
	}

	function foodbakery_remove_catmeta() {
	    global $current_screen;
	    switch ($current_screen->id) {
		case 'edit-restaurant_type':
		    ?>
		    <script type="text/javascript">
		        jQuery(window).load(function ($) {
		            jQuery('#parent').parent().remove();
		        });
		    </script>
		    <?php
		    break;
		case 'edit-restaurant-tag':
		    break;
	    }
	}

	/**
	 * Start Function How to create coloumes of post and theme
	 */
	//foodbakery_restaurant_name
	function theme_columns($theme_columns) {
	    $new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_name'),
		'header_icon' => '',
		'slug' => foodbakery_plugin_text_srt('foodbakery_restaurant_slug'),
		'posts' => foodbakery_plugin_text_srt('foodbakery_restaurant_posts')
	    );
	    return $new_columns;
	}

	public function restaurant_time_list($type_id = '') {
                 
	    $lapse = 15;
	    $hours = array();
	    $foodbakery_opening_hours_gap = get_post_meta($type_id, 'foodbakery_opening_hours_time_gap', true);
	    if (isset($foodbakery_opening_hours_gap) && $foodbakery_opening_hours_gap != '') {
		$lapse = $foodbakery_opening_hours_gap;
	    }

	    $date = date("Y-m-d 12:00");
	    $time = strtotime('12:00 am');
	    $start_time = strtotime($date . ' am');
	    $endtime = strtotime(date("Y-m-d h:i a", strtotime('1440 minutes', $start_time)));

	    while ($start_time < $endtime) {
		$time = date("h:i a", strtotime('+' . $lapse . ' minutes', $time));
		$hours[$time] = $time;
		$time = strtotime($time);
		$start_time = strtotime(date("Y-m-d h:i a", strtotime('+' . $lapse . ' minutes', $start_time)));
	    }

	    return $hours;
	}

	public function restaurant_week_days() {

	    $week_days = array(
		'monday' => esc_html__('Monday', 'foodbakery'),
		'tuesday' => esc_html__('Tuesday', 'foodbakery'),
		'wednesday' => esc_html__('Wednesday', 'foodbakery'),
		'thursday' => esc_html__('Thursday', 'foodbakery'),
		'friday' => esc_html__('Friday', 'foodbakery'),
		'saturday' => esc_html__('Saturday', 'foodbakery'),
		'sunday' => esc_html__('Sunday', 'foodbakery')
	    );
	    return $week_days;
	}

	public function restaurant_book_days_off() {
	    global $post;

	    $restaurant_add_counter = rand(10000000, 99999999);
	    $html = '';
	    $off_days_list = '';

	    $get_restaurant_off_days = get_post_meta($post->ID, 'foodbakery_calendar', true);
	    if (is_array($get_restaurant_off_days) && sizeof($get_restaurant_off_days)) {
		foreach ($get_restaurant_off_days as $get_off_day) {
		    $off_days_list .= $this->append_to_book_days_off($get_off_day);
		}
	    } else {
		$off_days_list = '<li id="no-book-day-' . $restaurant_add_counter . '" class="no-result-msg">' . esc_html__('No off days added.', 'foodbakery') . '</li>';
	    }

	    wp_enqueue_script('responsive-calendar');

	    $html .= '
			<div class="form-elements">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . esc_html__('Book Day Off', 'foodbakery') . '</label>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="book-list">
						<ul id="foodbakery-dev-add-off-day-app-' . $restaurant_add_counter . '">
							' . $off_days_list . '
						</ul>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div id="foodbakery-dev-loader-' . absint($restaurant_add_counter) . '" class="foodbakery-loader"></div>
					<a class="book-btn" href="javascript:void(0);">' . esc_html__('Book off day', 'foodbakery') . '</a>
					<div id="foodbakery-dev-cal-holder-' . $restaurant_add_counter . '" class="calendar-holder">
						<div data-id="' . $restaurant_add_counter . '" class="foodbakery-dev-insert-off-days responsive-calendar" data-ajax-url="' . esc_url(admin_url('admin-ajax.php')) . '" data-plugin-url="' . esc_url(wp_foodbakery::plugin_url()) . '">
							<span class="availability">' . esc_html__('Availability', 'foodbakery') . '</span>
							<div class="controls">
								<a data-go="prev"><div class="btn btn-primary"><i class="icon-angle-left"></i></div></a>
								<h4><span data-head-month></span> <span data-head-year></span></h4>
								<a data-go="next"><div class="btn btn-primary"><i class="icon-angle-right"></i></div></a>
							</div>
							<div class="day-headers">
								<div class="day header">' . esc_html__('Sun', 'foodbakery') . '</div>
								<div class="day header">' . esc_html__('Mon', 'foodbakery') . '</div>
								<div class="day header">' . esc_html__('Tue', 'foodbakery') . '</div>
								<div class="day header">' . esc_html__('Wed', 'foodbakery') . '</div>
								<div class="day header">' . esc_html__('Thu', 'foodbakery') . '</div>
								<div class="day header">' . esc_html__('Fri', 'foodbakery') . '</div>
								<div class="day header">' . esc_html__('Sat', 'foodbakery') . '</div>
							</div>
							<div class="days foodbakery-dev-calendar-days" data-group="days"></div>
						</div>
					</div>
					
				</div>
				<script>
					jQuery(document).ready(function () {
						jQuery(".responsive-calendar").responsiveCalendar({
							monthChangeAnimation: false,
						});
					});
				</script>
			</div>';
	    return force_balance_tags($html);
	}

	/**
	 * Appending off days to list via Ajax
	 * @return markup
	 */
	public function append_to_book_days_off($get_off_day = '') {

	    if ($get_off_day != '') {
		$book_off_date = $get_off_day;
	    } else {
		$day = foodbakery_get_input('off_day_day', date('d'), 'STRING');
		$month = foodbakery_get_input('off_day_month', date('m'), 'STRING');
		$year = foodbakery_get_input('off_day_year', date('Y'), 'STRING');
		$book_off_date = $year . '-' . $month . '-' . $day;
	    }

	    $formated_off_date = date_i18n(get_option('date_format'), strtotime($book_off_date));

	    $rand_numb = rand(100000000, 999999999);

	    $html = '
			<li id="day-remove-' . $rand_numb . '">
				<div class="open-close-time opening-time">
					<div class="date-sec">
						<span>' . $formated_off_date . '</span>
						<input type="hidden" value="' . $book_off_date . '" name="foodbakery_restaurant_off_days[]">
					</div>
					<div class="time-sec">
						<a id="foodbakery-dev-day-off-rem-' . $rand_numb . '" data-id="' . $rand_numb . '" href="javascript:void(0);"><i class="icon-cross-out"></i></a>
					</div>
				</div>
			</li>';

	    if ($get_off_day != '') {
		return force_balance_tags($html);
	    } else {
		echo json_encode(array('html' => $html));
		die;
	    }
	}

	public function foodbakery_restaurant_save_off_days($restaurant_id = '') {
	    $foodbakery_off_days = foodbakery_get_input('foodbakery_restaurant_off_days', '', 'ARRAY');
	    if (isset($_POST['foodbakery_restaurant_off_days'])) {
		update_post_meta($restaurant_id, 'foodbakery_calendar', $foodbakery_off_days);
	    }
	}

	public function foodbakery_restaurant_save_opening_hours($restaurant_id = '') {
	    if (isset($_POST['foodbakery_opening_hour'])) {
		$opening_hours_list = foodbakery_get_input('foodbakery_opening_hour', '', 'ARRAY');
		$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
		foreach ($days as $key => $day) {
		    if (isset($opening_hours_list[$day])) {
			$day_status = ( $opening_hours_list[$day]['day_status'] != '' ? $opening_hours_list[$day]['day_status'] : 'Off' );
			$opening_time = ( $opening_hours_list[$day]['opening_time'] != '' ? $opening_hours_list[$day]['opening_time'] : '' );
			if ($opening_time != '') {
			    $opening_time = strtotime('2016-01-01 ' . $opening_time);
			}
			$closing_time = ( $opening_hours_list[$day]['closing_time'] != '' ? $opening_hours_list[$day]['closing_time'] : '' );
			if ($closing_time != '') {
			    $closing_time = strtotime('2016-01-01 ' . $closing_time);
			}

			if ($opening_time != '' && $closing_time != '' && $opening_time > $closing_time) {
			    $closing_time = strtotime('+1 day', $closing_time);
			}

			update_post_meta($restaurant_id, 'foodbakery_opening_hours_' . $day . '_day_status', $day_status);
			update_post_meta($restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time', $opening_time);
			update_post_meta($restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time', $closing_time);
		    }
		}
	    }
	}

	public function foodbakery_save_restaurant_custom_fields_dates($restaurant_id = '') {
	    if ($restaurant_id != '') {

		$restaurant_type_slug = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
		$restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish'));
		$restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
		$restaurant_type_fields = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_cus_fields', true);
		if (!empty($restaurant_type_fields)) {
		    foreach ($restaurant_type_fields as $restaurant_type_field) {
			$field_type = isset($restaurant_type_field['type']) ? $restaurant_type_field['type'] : '';
			$meta_key = isset($restaurant_type_field['meta_key']) ? $restaurant_type_field['meta_key'] : '';
			if ($field_type == 'date') {
			    if ($meta_key != '') {
				$cus_field_values = '';
				$cus_field_values = isset($_POST['foodbakery_cus_field']) ? $_POST['foodbakery_cus_field'] : '';
				if ($cus_field_values) {
				    foreach ($cus_field_values as $c_key => $c_val) {
					if ($c_key == $meta_key) {
					    update_post_meta($restaurant_id, $c_key, strtotime($c_val));
					}
				    }
				}
			    }
			}
		    }
		}
	    }
	}

	public function foodbakery_restaurant_categories($restaurant_id = '') {
	    $foodbakery_restaurant_cats = foodbakery_get_input('foodbakery_restaurant_category', '', 'ARRAY');
	    $cat_ids = array();
	    wp_set_post_terms($restaurant_id, '', 'restaurant-category');
	    if ($foodbakery_restaurant_cats) {
		foreach ($foodbakery_restaurant_cats as $foodbakery_restaurant_cat) {
		    $term = get_term_by('slug', $foodbakery_restaurant_cat, 'restaurant-category');
		    $cat_ids[] = $term->term_id;
		}
	    }
	    wp_set_post_terms($restaurant_id, $cat_ids, 'restaurant-category');
	}

	function foodbakery_submit_meta_box($post, $args = array()) {
	    global $action, $post, $foodbakery_plugin_static_text;


	    $post_type = $post->post_type;
	    $post_type_object = get_post_type_object($post_type);
        $cap_publish_posts = isset($post_type_object->cap->publish_posts) ? $post_type_object->cap->publish_posts : '';
	    $can_publish = current_user_can($cap_publish_posts);
	    ?>
	    <div class="submitbox foodbakery-submit" id="submitpost">
	        <div id="minor-publishing">
	    	<div style="display:none;">
			<?php submit_button(foodbakery_plugin_text_srt('foodbakery_submit'), 'button', 'save'); ?>
	    	</div>
		    <?php
		    if (isset($post_type_object->public) && $post_type_object->public && !empty($post)) :
			if ('publish' == $post->post_status) {
			    $preview_link = esc_url(get_permalink($post->ID));
			    $preview_button = foodbakery_plugin_text_srt('foodbakery_preview');
			} else {
			    $preview_link = set_url_scheme(get_permalink($post->ID));

			    /**
			     * Filter the URI of a post preview in the post submit box.
			     *
			     * @since 2.0.5
			     * @since 4.0.0 $post parameter was added.
			     *
			     * @param string  $preview_link URI the user will be directed to for a post preview.
			     * @param WP_Post $post         Post object.
			     */
			    $preview_link = esc_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', esc_url($preview_link)), $post));
			    $preview_button = foodbakery_plugin_text_srt('foodbakery_preview');
			}
		    endif; // public post type                      
		    ?>


	        </div>
	        <div id="major-publishing-actions" style="border-top:0px">
		    <?php
		    /**
		     * Fires at the beginning of the publishing actions section of the Publish meta box.
		     *
		     * @since 2.7.0
		     */
		    do_action('post_submitbox_start');
		    ?>
	    	<div id="delete-action">
			<?php
			if (current_user_can("delete_post", $post->ID)) {
			    if (!EMPTY_TRASH_DAYS) {
				$delete_text = foodbakery_plugin_text_srt('foodbakery_delete_permanently');
			    } else {
				$delete_text = foodbakery_plugin_text_srt('foodbakery_move_to_trash');
			    }
			    if (isset($_GET['action']) && $_GET['action'] == 'edit') {
				?>
		    	    <a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo foodbakery_allow_special_char($delete_text) ?></a>
				<?php
			    }
			}
			?>
	    	</div>
	    	<div id="publishing-action">
	    	    <span class="spinner"></span>
			<?php
			if (!in_array($post->post_status, array('publish', 'future', 'private')) || 0 == $post->ID) {
			    if ($can_publish) :
				if (!empty($post->post_date_gmt) && time() < strtotime($post->post_date_gmt . ' +0000')) :
				    ?>
				    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo esc_html('foodbakery_schedule'); ?>" />
				    <?php submit_button(esc_html('foodbakery_schedule'), 'primary button-large', 'publish', false, array('accesskey' => 'p')); ?>
				<?php else : ?>
				    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt('foodbakery_publish'); ?>" />
				    <?php submit_button(foodbakery_plugin_text_srt('foodbakery_publish'), 'primary button-large', 'publish', false, array('accesskey' => 'p')); ?>
				<?php
				endif;
			    else :
				?>
		    	    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt('foodbakery_submit_for_review'); ?>" />
				<?php submit_button(foodbakery_plugin_text_srt('foodbakery_submit_for_review'), 'primary button-large', 'publish', false, array('accesskey' => 'p')); ?>
			    <?php
			    endif;
			} else {

			    if (isset($_GET['action']) && $_GET['action'] == 'edit') {
				?>
		    	    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt('foodbakery_update'); ?>" />
		    	    <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php echo foodbakery_plugin_text_srt('foodbakery_update'); ?>" />
				<?php
			    } else {
				?>
		    	    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt('foodbakery_publish'); ?>">
		    	    <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php echo foodbakery_plugin_text_srt('foodbakery_publish'); ?>" accesskey="p">
				<?php
			    }
			}
			?>
	    	</div>
	    	<div class="clear"></div>
	        </div>
	    </div>

	    <?php
	}

    }

    global $foodbakery_restaurant_meta;
    $foodbakery_restaurant_meta = new foodbakery_restaurant_meta();
    return $foodbakery_restaurant_meta;
}