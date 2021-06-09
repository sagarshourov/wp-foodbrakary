<?php
/**
 * File Type: Plugin Functions
 */
if (!class_exists('Foodbakery_Plugin_Functions')) {

    class Foodbakery_Plugin_Functions {

	// The single instance of the class
	protected static $_instance = null;

	/**
	 * Start construct Functions
	 */
	public function __construct() {
        add_action('save_post', array($this, 'foodbakery_save_post_option'), 11);
        add_action('save_post', array($this, 'foodbakery_withdraw_status_field'), 12);
	    add_action('admin_init', array($this, 'foodbakery_wpml_strings_translate'));
	    add_action('create_specialisms', array($this, 'foodbakery_save_jobs_spec_fields'));
	    add_action('edited_specialisms', array($this, 'foodbakery_save_jobs_spec_fields'));
	    add_action('specialisms_edit_form_fields', array($this, 'foodbakery_edit_jobs_spec_fields'));
	    add_action('specialisms_add_form_fields', array($this, 'foodbakery_jobs_spec_fields'));

	    add_action('create_job_type', array($this, 'foodbakery_save_jobs_jobtype_fields'));
	    add_action('edited_job_type', array($this, 'foodbakery_save_jobs_jobtype_fields'));
	    add_action('job_type_edit_form_fields', array($this, 'foodbakery_edit_jobs_job_type_fields'));
	    add_action('job_type_add_form_fields', array($this, 'foodbakery_jobs_job_type_fields'));

	    add_filter('manage_users_columns', array($this, 'foodbakery_new_modify_user_table'));
	    add_filter('manage_users_custom_column', array($this, 'foodbakery_new_modify_user_table_row'), 10, 3);
	}

	/**
	 * End construct Functions
	 * Start Creating  Instance of the Class Function
	 */
	public static function instance() {
	    if (is_null(self::$_instance)) {
		self::$_instance = new self();
	    }
	    return self::$_instance;
	}

	public function foodbakery_new_modify_user_table($column) {
	    $column['display_name'] = 'Display Name';
            $column['publisher_name'] = esc_html__( 'Publisher', 'foodbakery' );
            unset( $column['name'] );
	    return $column;
	}

	public function foodbakery_wpml_strings_translate() {
	    global $foodbakery_plugin_options;
	    if (function_exists('icl_register_string')) {
		$d_announcemrnt_title = isset($foodbakery_plugin_options['foodbakery_dashboard_announce_title']) ? $foodbakery_plugin_options['foodbakery_dashboard_announce_title'] : '';
		$d_announcemrnt_desc = isset($foodbakery_plugin_options['foodbakery_dashboard_announce_description']) ? $foodbakery_plugin_options['foodbakery_dashboard_announce_description'] : '';
		$l_announcemrnt_title = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_title']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_title'] : '';
		$l_announcemrnt_desc = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_description']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_description'] : '';

		do_action('wpml_register_single_string', 'restaurant_notices', 'Announcement "' . $l_announcemrnt_title . '" - Title field', $l_announcemrnt_title);
		do_action('wpml_register_single_string', 'restaurant_notices', 'Announcement "' . $l_announcemrnt_desc . '" - Description field', $l_announcemrnt_desc);
		do_action('wpml_register_single_string', 'dashboard_notices', 'Announcement "' . $d_announcemrnt_title . '" - Title field', $d_announcemrnt_title);
		do_action('wpml_register_single_string', 'dashboard_notices', 'Announcement "' . $d_announcemrnt_desc . '" - Description field', $d_announcemrnt_desc);
	    }
	}

	public function foodbakery_withdraw_status_field($post_id = '') {

	    if (get_post_type($post_id) == 'withdrawals' && isset($_POST['foodbakery_withdrawal_status']) && $_POST['foodbakery_withdrawal_status'] == 'approved') {

		$withdraw_added = get_post_meta($post_id, 'withdraw_added_to_publisher', true);

		if ($withdraw_added != 'yes') {

		    $withdraw_publisher_id = get_post_meta($post_id, 'foodbakery_withdrawal_user', true);
		    $withdraw_amount = get_post_meta($post_id, 'withdrawal_amount', true);
		    $publisher_withdrawals = get_post_meta($withdraw_publisher_id, 'total_withdrawals', true);

            $publisher_withdrawals = is_numeric($publisher_withdrawals) ? $publisher_withdrawals : 0;
            $withdraw_amount = is_numeric($withdraw_amount) ? $withdraw_amount : 0;

		    $total_withdrawals = $publisher_withdrawals + $withdraw_amount;
		    update_post_meta($withdraw_publisher_id, 'total_withdrawals', $total_withdrawals);

		    update_post_meta($post_id, 'withdraw_added_to_publisher', 'yes');
		    update_post_meta($post_id, 'foodbakery_withdrawal_status', 'approved');
		}
	    }
	}

	/**
	 * End Creating  Instance Main Fuunctions
	 * Start Saving Post  options Function
	 */
	public function foodbakery_save_post_option($post_id = '') {
	    global $post, $foodbakery_restaurant_type_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_meta, $foodbakery_restaurant_type_form_builder_fields;
		// Stop WP from clearing custom fields on autosave.
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		// Prevent quick edit from clearing custom fields
		if (defined('DOING_AJAX') && DOING_AJAX)
			return;

		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) )
			return;


        /* coupon status pending to publish */
        //$coupon_ids = isset($_REQUEST['coupon_id']) ? $_REQUEST['coupon_id'] : '';
        //do_action('restaurant_update_coupon_admin', $coupon_ids);

	    $foodbakery_data = array();
	    foreach ($_POST as $key => $value) {
		if (strstr($key, 'foodbakery_')) {
		    if ($key == 'foodbakery_transaction_expiry_date' || $key == 'foodbakery_restaurant_expired' || $key == 'foodbakery_restaurant_posted' || $key == 'foodbakery_user_last_activity_date' || $key == 'foodbakery_user_last_activity_date') {
			if (($key == 'foodbakery_user_last_activity_date' && $value == '') || $key == 'foodbakery_user_last_activity_date') {
			    $value = date('d-m-Y H:i:s');
			}
			$foodbakery_data[$key] = strtotime($value);
			update_post_meta($post_id, $key, strtotime($value));
		    } else {
			$foodbakery_data[$key] = $value;
			if ($key == 'foodbakery_restaurant_new_price') {
			    $value = preg_replace('/\D/', '', $value);
			}

			update_post_meta($post_id, $key, $value);
			if ($key == 'foodbakery_cus_field' && get_post_type($post_id) != 'restaurant-type') {
			    if (is_array($value) && sizeof($value) > 0) {
				foreach ($value as $c_key => $c_val) {
				    update_post_meta($post_id, $c_key, $c_val);
				}
			    }
			}
		    }
		    if (strstr($key, 'foodbakery_transaction_') && get_post_type($post_id) == 'restaurants') {
			$foodbakery_restaurant_add_obj = new foodbakery_publisher_restaurant_actions();
			$foodbakery_restaurant_trans_array = $foodbakery_restaurant_add_obj->restaurant_assign_meta();

			$foodbakery_restaurant_trans_update_arr = array();
			foreach ($foodbakery_restaurant_trans_array as $restaurant_trans_key => $restaurant_trans_val) {
			    if (isset($restaurant_trans_val['label']) && isset($restaurant_trans_val['key']) && isset($_POST[$restaurant_trans_val['key']])) {
				$foodbakery_restaurant_trans_update_arr[] = array(
				    'key' => $restaurant_trans_val['key'],
				    'label' => $restaurant_trans_val['label'],
				    'value' => $_POST[$restaurant_trans_val['key']],
				);
			    }
			}
			update_post_meta($post_id, 'foodbakery_trans_all_meta', $foodbakery_restaurant_trans_update_arr);
		    }
		    if ($key == 'foodbakery_tags' && get_post_type($post_id) == 'restaurants') {
			$foodbakery_restaurant_tags = $_POST['foodbakery_tags'];
			if (!empty($foodbakery_restaurant_tags) && is_array($foodbakery_restaurant_tags)) {
			    wp_set_post_terms($post_id, $foodbakery_restaurant_tags, 'restaurant-tag', FALSE);
			    update_post_meta($post_id, 'foodbakery_restaurant_tags', $foodbakery_restaurant_tags);
			}
		    }
		    if ($key == 'foodbakery_cover_image' && get_post_type($post_id) == 'restaurants') {
			$foodbakery_restaurant_cover_img = $_POST['foodbakery_cover_image'];
			if (!empty($foodbakery_restaurant_cover_img)) {
			    $get_attachment_id = $foodbakery_restaurant_cover_img;
			    set_post_thumbnail($post_id, $get_attachment_id);
			    update_post_meta($post_id, 'foodbakery_cover_image', $foodbakery_restaurant_cover_img);
			} else {
			    update_post_meta($post_id, 'foodbakery_cover_image', '');
			    delete_post_thumbnail($post_id);
			}
		    }
		}
		if (get_post_type($post_id) == 'restaurant-type') {
		    if (array_key_exists('foodbakery_reviews_labels', $_POST)) {
			delete_post_meta($post_id, 'foodbakery_reviews_labels');
			update_post_meta($post_id, 'foodbakery_reviews_labels', $_POST['foodbakery_reviews_labels']);
		    }
		    if (!array_key_exists('foodbakery_restaurant_type_makes', $_POST)) {
			update_post_meta($post_id, 'foodbakery_restaurant_type_makes', '');
		    }
		}
	    }
	    if (!empty($foodbakery_data)) {
		update_post_meta($post_id, 'foodbakery_full_data', $foodbakery_data);
		update_post_meta($post_id, 'foodbakery_array_data', $foodbakery_data);
	    }
	    if (get_post_type($post_id) == 'restaurant-type') {
		$foodbakery_restaurant_type_fields->foodbakery_update_custom_fields($post_id);
		$foodbakery_restaurant_type_form_builder_fields->foodbakery_update_form_builder_custom_fields($post_id);
		$foodbakery_restaurant_type_meta->features_save($post_id);
		$foodbakery_restaurant_type_meta->tags_save($post_id);
		$foodbakery_restaurant_type_meta->categories_save($post_id);
	    }
	    //}
	}

	public function foodbakery_new_modify_user_table_row($val, $column_name, $user_id) {
	    $user = get_userdata($user_id);
            $return = '';
	    switch ($column_name) {
		case 'display_name' :
		    $foodbakery_user = get_userdata($user_id);
		    $return = $foodbakery_user->display_name;
		    break;
                case 'publisher_name' :
                    $publisher_name = get_the_title( get_user_meta( $user_id, 'foodbakery_company', true ) );
                    return ( $publisher_name != '' )? $publisher_name : '-';
                    break;
		case 'jobs' :
		    $foodbakery_user = get_userdata($user_id);
		    $args = array(
			'post_type' => 'restaurants',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
			    array(
				'key' => 'foodbakery_job_username',
				'value' => $user_id,
				'compare' => '=',
			    ),
			),
		    );

		    $query = new WP_Query($args);

		    $author_posts_link = admin_url('edit.php?author=' . $user_id . '&post_type=jobs');

		    if ($query->found_posts > 0) {
			$return = '<a href="' . $author_posts_link . '">' . $query->found_posts . '</a>';
		    } else {
			$return = $query->found_posts;
		    }
		    break;
		default:
	    }
	    return $return;
	}

	/**
	 * Start Special Characters Function
	 */
	public function special_chars($input = '') {
	    $output = $input; // output line
	    return $output;
	}

	/**
	 * Get Restaurant Status
	 */
	public function get_restaurant_status($restaurant_status = '') {

	    if ($restaurant_status == 'awaiting-activation') {
		$restaurant_status_str = esc_html__('Awaiting Activation', 'foodbakery');
	    } else if ($restaurant_status == 'inactive') {
		$restaurant_status_str = esc_html__('Inactive', 'foodbakery');
	    } else if ($restaurant_status == 'delete') {
		$restaurant_status_str = esc_html__('Delete', 'foodbakery');
	    } else if ($restaurant_status == 'pending') {
		$restaurant_status_str = esc_html__('Pending', 'foodbakery');
	    } else {
		$restaurant_status_str = esc_html__('Active', 'foodbakery');
	    }
	    return $restaurant_status_str;
	}

	/**
	 * End Special Characters Function
	 * Start Regular Expression  Text Function
	 */
	public function slugy_text($str) {
	    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
	    $clean = strtolower(trim($clean, '_'));
	    $clean = preg_replace("/[\/_|+ -]+/", '_', $clean);
	    return $clean;
	}

	/**
	 * End Regular Expression  Text Function
	 * Start  Creating  Random Id Function
	 */
	public function rand_id() {
	    $output = rand(12345678, 98765432);
	    return $output;
	}

	/**
	 * End  Creating  Random Id Function
	 * Start Advance Deposit Function
	 */
	public function percent_return($num) {
	    if (is_numeric($num) && $num > 0 && $num <= 100) {
		$num = $num;
	    } else if (is_numeric($num) && $num > 0 && $num > 100) {
		$num = 100;
	    } else {
		$num = 0;
	    }

	    return $num;
	}

	/**
	 * Number Format Function
	 * Function how to get  attachment image src
	 */
	public function num_format($num) {
	    $foodbakery_number = number_format((float) $num, 2, '.', '');
	    return $foodbakery_number;
	}

	public function foodbakery_attach_image_src($attachment_id, $width, $height) {
	    $image_url = wp_get_attachment_image_src($attachment_id, array($width, $height), true);
	    if ($image_url[1] == $width and $image_url[2] == $height)
		;
	    else
		$image_url = wp_get_attachment_image_src($attachment_id, "full", true);
	    $parts = explode('/uploads/', $image_url[0]);
	    if (count($parts) > 1)
		return $image_url[0];
	}

	/**
	 *  End How to get first image from gallery and its image src Function
	 * Get post Id Through meta key Fundtion
	 */
	public function foodbakery_get_post_id_by_meta_key($key, $value) {
	    global $wpdb;
	    $meta = $wpdb->get_results("SELECT * FROM `" . $wpdb->postmeta . "` WHERE meta_key='" . $key . "' AND meta_value='" . $value . "'");

	    if (is_array($meta) && !empty($meta) && isset($meta[0])) {
		$meta = $meta[0];
	    }
	    if (is_object($meta)) {
		return $meta->post_id;
	    } else {
		return false;
	    }
	}

	/**
	 *  end Get post Id Through meta key Fundtion
	 * Start Show All Taxonomy(categories) Function
	 */
	public function foodbakery_show_all_cats($parent = '', $separator = '', $selected = "", $taxonomy = '') {

	    if ($parent == "") {
		global $wpdb;
		$parent = 0;
	    } else
		$separator .= " &ndash; ";
	    $args = array(
		'parent' => $parent,
		'hide_empty' => 0,
		'taxonomy' => $taxonomy
	    );
	    $categories = get_categories($args);

	    foreach ($categories as $category) {
		?>
		<option <?php if ($selected == $category->slug) echo "selected"; ?> value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_attr($separator . $category->cat_name); ?></option>
		<?php
		foodbakery_show_all_cats($category->term_id, $separator, $selected, $taxonomy);
	    }
	}

	/**
	 *  End Show All Taxonomy(categories) Function
	 *  Start how to icomoon get
	 */
	public function foodbakery_icomoons($icon_value = '', $id = '', $name = '') {
	    global $foodbakery_form_fields;
	    ob_start();
	    ?>
	    <script>
	        jQuery(document).ready(function ($) {

	    	var e9_element = $('#e9_element_<?php echo foodbakery_allow_special_char($id); ?>').fontIconPicker({
	    	    theme: 'fip-bootstrap'
	    	});
	    	// Add the event on the button
	    	$('#e9_buttons_<?php echo foodbakery_allow_special_char($id); ?> button').on('click', function (e) {
	    	    e.preventDefault();
	    	    // Show processing message//
	    	    $(this).prop('disabled', true).html('<i class="icon-cog demo-animate-spin"></i><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_please_wait'); ?>');
	    	    $.ajax({
	    		url: '<?php echo wp_foodbakery::plugin_url(); ?>/assets/icomoon/js/selection.json',
	    		type: 'GET',
	    		dataType: 'json'
	    	    })
	    		    .done(function (response) {
	    			// Get the class prefix
	    			var classPrefix = response.preferences.fontPref.prefix,
	    				icomoon_json_icons = [],
	    				icomoon_json_search = [];
	    			$.each(response.icons, function (i, v) {
	    			    icomoon_json_icons.push(classPrefix + v.properties.name);
	    			    if (v.icon && v.icon.tags && v.icon.tags.length) {
	    				icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
	    			    } else {
	    				icomoon_json_search.push(v.properties.name);
	    			    }
	    			});
	    			// Set new fonts on fontIconPicker
	    			e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
	    			// Show success message and disable
	    			$('#e9_buttons_<?php echo foodbakery_allow_special_char($id); ?> button').removeClass('btn-primary').addClass('btn-success').text(<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_loaded_icons'); ?>).prop('disabled', true);
	    		    })
	    		    .fail(function () {
	    			// Show error message and enable
	    			$('#e9_buttons_<?php echo foodbakery_allow_special_char($id); ?> button').removeClass('btn-primary').addClass('btn-danger').text(<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_error_try_again'); ?>).prop('disabled', false);
	    		    });
	    	    e.stopPropagation();
	    	});

	    	jQuery("#e9_buttons_<?php echo foodbakery_allow_special_char($id); ?> button").click();
	        });


	    </script>
	    <?php
	    $foodbakery_opt_array = array(
		'id' => '',
		'std' => foodbakery_allow_special_char($icon_value),
		'cust_id' => "e9_element_" . foodbakery_allow_special_char($id),
		'cust_name' => foodbakery_allow_special_char($name) . "[]",
		'return' => false,
	    );

	    $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
	    ?>
	    <span id="e9_buttons_<?php echo foodbakery_allow_special_char($id); ?>" style="display:none">
	        <button autocomplete="off" type="button" class="btn btn-primary"><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_load_icomoon'); ?></button>
	    </span>
	    <?php
	    $fontawesome = ob_get_clean();
	    return $fontawesome;
	}

	/**
	 * @ render Random ID
	 * Start Get Current  user ID Function
	 *
	 */
	public static function foodbakery_generate_random_string($length = 3) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i ++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}

	public function foodbakery_get_user_id() {
	    global $current_user;
	    wp_get_current_user();
	    return $current_user->ID;
	}

	/**
	 * End Current get user ID Function
	 * How to create location Fields(fields) Function
	 */

	/**
	 * How to create location Fields(fields) Function
	 */
	public function foodbakery_location_fields($user = '', $field_postfix = '', $output = true) {

	    global $foodbakery_plugin_options, $post, $foodbakery_html_fields, $foodbakery_form_fields;
	    $foodbakery_map_latitude = isset($foodbakery_plugin_options['foodbakery_default_map_latitude']) ? $foodbakery_plugin_options['foodbakery_default_map_latitude'] : '';
	    $foodbakery_map_longitude = isset($foodbakery_plugin_options['foodbakery_default_map_longitude']) ? $foodbakery_plugin_options['foodbakery_default_map_longitude'] : '';
	    $foodbakery_map_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '6';
	    $foodbakery_map_marker_icon = isset($foodbakery_plugin_options['foodbakery_map_marker_icon']) ? $foodbakery_plugin_options['foodbakery_map_marker_icon'] : wp_foodbakery::plugin_url() . '/assets/images/map-marker.png';
	    $foodbakery_array_data = '';
	    $foodbakery_post_loc_zoom = $foodbakery_map_zoom;

        $flag = apply_filters('foodbakery_add_county_in_location_level', false);

		$foodbakery_post_loc_address ='';
	    //start_ob();
	    if (isset($user) && !empty($user)) { // get values from usermeta
		$foodbakery_array_data = get_the_author_meta('foodbakery_array_data', $user->ID);

		if (isset($foodbakery_array_data) && !empty($foodbakery_array_data)) {
		    $foodbakery_post_loc_country = get_the_author_meta('foodbakery_post_loc_country_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_state = get_the_author_meta('foodbakery_post_loc_state_' . $field_postfix, $user->ID);
            if($flag) {
                $foodbakery_post_loc_county = get_the_author_meta('foodbakery_post_loc_county_' . $field_postfix, $user->ID);
            }
		    $foodbakery_post_loc_city = get_the_author_meta('foodbakery_post_loc_city_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_town = get_the_author_meta('foodbakery_post_loc_town_' . $field_postfix, $user->ID);
		    $foodbakery_post_comp_address = get_the_author_meta('foodbakery_post_loc_address_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_address = get_the_author_meta('foodbakery_post_loc_address_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_latitude = get_the_author_meta('foodbakery_post_loc_latitude_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_longitude = get_the_author_meta('foodbakery_post_loc_longitude_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_zoom = get_the_author_meta('foodbakery_post_loc_zoom_' . $field_postfix, $user->ID);
		    $foodbakery_add_new_loc = get_the_author_meta('foodbakery_add_new_loc_' . $field_postfix, $user->ID);
		    $foodbakery_loc_radius = get_the_author_meta('foodbakery_loc_radius_' . $field_postfix, $user->ID);
		} else {
		    $foodbakery_post_loc_country = '';
		    $foodbakery_post_loc_region = '';
		    $foodbakery_post_loc_town = '';
            if($flag) {
                $foodbakery_post_loc_county = '';
            }
		    $foodbakery_post_loc_city = '';
		    $foodbakery_post_loc_state = '';
		    $foodbakery_post_loc_address = '';
		    $foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
		    $foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
		    $foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_post_loc_zoom']) ? $foodbakery_plugin_options['foodbakery_post_loc_zoom'] : '';
		    $loc_city = '';
		    $loc_postcode = '';
		    $loc_region = '';
		    $loc_country = '';
		    $event_map_switch = '';
		    $event_map_heading = '';
		    $foodbakery_add_new_loc = '';
		    $foodbakery_post_comp_address = '';
		    $foodbakery_loc_radius = '';
		}
	    } else {  // get values from postmeta
		$foodbakery_array_data = get_post_meta($post->ID, 'foodbakery_array_data', true);

		if (isset($foodbakery_array_data) && !empty($foodbakery_array_data)) {
		    $foodbakery_post_loc_town = get_post_meta($post->ID, 'foodbakery_post_loc_town_' . $field_postfix, true);
            if($flag) {
                $foodbakery_post_loc_county = get_post_meta($post->ID, 'foodbakery_post_loc_county_' . $field_postfix, true);
            }
		    $foodbakery_post_loc_city = get_post_meta($post->ID, 'foodbakery_post_loc_city_' . $field_postfix, true);
		    $foodbakery_post_loc_state = get_post_meta($post->ID, 'foodbakery_post_loc_state_' . $field_postfix, true);
		    $foodbakery_post_loc_country = get_post_meta($post->ID, 'foodbakery_post_loc_country_' . $field_postfix, true);
		    $foodbakery_post_loc_latitude = get_post_meta($post->ID, 'foodbakery_post_loc_latitude_' . $field_postfix, true);
		    $foodbakery_post_loc_longitude = get_post_meta($post->ID, 'foodbakery_post_loc_longitude_' . $field_postfix, true);
		    $foodbakery_post_loc_zoom = get_post_meta($post->ID, 'foodbakery_post_loc_zoom_' . $field_postfix, true);
		    $foodbakery_post_loc_address = get_post_meta($post->ID, 'foodbakery_post_loc_address_' . $field_postfix, true);
		    $foodbakery_post_comp_address = get_post_meta($post->ID, 'foodbakery_post_loc_address_' . $field_postfix, true);
		    $foodbakery_add_new_loc = get_post_meta($post->ID, 'foodbakery_add_new_loc_' . $field_postfix, true);
		    $foodbakery_loc_radius = get_post_meta($post->ID, 'foodbakery_loc_radius_' . $field_postfix, true);
		} else {
		    $foodbakery_post_loc_country = '';
		    $foodbakery_post_loc_region = '';
		    $foodbakery_post_loc_state = '';
            if($flag) {
                $foodbakery_post_loc_county = '';
            }
            $foodbakery_post_loc_city = '';
		    $foodbakery_post_loc_town = '';
		    $foodbakery_post_loc_address = '';
		    $foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
		    $foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
		    $foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_post_loc_zoom']) ? $foodbakery_plugin_options['foodbakery_post_loc_zoom'] : '';
		    $loc_city = '';
		    $loc_postcode = '';
		    $loc_region = '';
		    $loc_country = '';
		    $event_map_switch = '';
		    $event_map_heading = '';
		    $foodbakery_add_new_loc = '';
		    $foodbakery_post_comp_address = '';
		    $foodbakery_loc_radius = '';
		}
	    }
	    if ($foodbakery_post_loc_latitude == '') {
		$foodbakery_post_loc_latitude = $foodbakery_map_latitude;
	    }
	    if ($foodbakery_post_loc_longitude == '') {
		$foodbakery_post_loc_longitude = $foodbakery_map_longitude;
	    }
	    if ($foodbakery_post_loc_zoom == '') {
		$foodbakery_post_loc_zoom = $foodbakery_map_zoom;
	    }

	    $foodbakery_obj = new wp_foodbakery();

	    $foodbakery_obj->foodbakery_location_gmap_script();
	    $foodbakery_obj->foodbakery_google_place_scripts();
	    $foodbakery_obj->foodbakery_autocomplete_scripts();
        if($flag) {
            $locations_data = array(
                'data' => array(
                    'country' => array(),
                    'state' => array(),
                    'county' => array(),
                    'city' => array(),
                    'town' => array(),
                ),
                'selected' => array(
                    'country' => $foodbakery_post_loc_country,
                    'state' => $foodbakery_post_loc_state,
                    'county' => $foodbakery_post_loc_county,
                    'city' => $foodbakery_post_loc_city,
                    'town' => $foodbakery_post_loc_town,
                ),
                'location_levels' => array(
                    'country' => -1,
                    'state' => -1,
                    'city' => -1,
                    'town' => -1,
                ),
            );
        }else{
            $locations_data = array(
                'data' => array(
                    'country' => array(),
                    'state' => array(),
                    'city' => array(),
                    'town' => array(),
                ),
                'selected' => array(
                    'country' => $foodbakery_post_loc_country,
                    'state' => $foodbakery_post_loc_state,
                    'city' => $foodbakery_post_loc_city,
                    'town' => $foodbakery_post_loc_town,
                ),
                'location_levels' => array(
                    'country' => -1,
                    'state' => -1,
                    'city' => -1,
                    'town' => -1,
                ),
            );
        }
	    $locations_data = apply_filters('get_locations_fields_data', $locations_data, 'locations_fields_selector');

	    /*
	     * How to get countries against location Function Start
	     */
	    $_locations_parent_id = 0;
	    $foodbakery_location_countries = isset($locations_data['data']['country']) ? $locations_data['data']['country'] : array();
	    $location_countries_list = '';
	    $location_states_list = '';
	    $location_cities_list = '';
	    $location_towns_list = '';
	    $iso_code_list_admin = '';

	    if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {
		$selected_iso_code = '';
		foreach ($foodbakery_location_countries as $key => $country) {
		    $selected = '';
		    $iso_code_list_admin = $country['iso_code'];
		    if (isset($foodbakery_post_loc_country) && $foodbakery_post_loc_country == $country['slug']) {
			$selected_iso_code = $iso_code_list_admin;
			$selected = 'selected';
		    }
		    $location_countries_list .= "<option " . $selected . "  value='" . $country['slug'] . "' data-name='" . $iso_code_list_admin . "'>" . $country['name'] . "</option>";
		}
	    }

	    $selected_country = $foodbakery_post_loc_country;
	    $selected_state = $foodbakery_post_loc_state;
	    $selected_city = $foodbakery_post_loc_city;
	    $selected_town = $foodbakery_post_loc_town;

	    $states = isset($locations_data['data']['state']) ? $locations_data['data']['state'] : array();
	    if (isset($states) && !empty($states)) {
		foreach ($states as $key => $state) {
		    $selected = ( $selected_state == $state['slug'] ) ? 'selected' : '';
		    $location_states_list .= "<option " . $selected . " value='" . $state['slug'] . "'>" . $state['name'] . "</option>";
		}
	    }
		 $location_county_list = '';
        if($flag){

            $selected_county = $foodbakery_post_loc_county;
        $counties = isset($locations_data['data']['county']) ? $locations_data['data']['county'] : array();
        if (isset($counties) && !empty($counties)) {
            foreach ($counties as $key => $county) {
                $selected = ( $selected_county == $county['slug'] ) ? 'selected' : '';
                $location_county_list .= "<option " . $selected . " value='" . $county['slug'] . "'>" . $county['name'] . "</option>";
            }
        }
        }

	    $cities = isset($locations_data['data']['city']) ? $locations_data['data']['city'] : array();
	    if (isset($cities) && !empty($cities)) {
		foreach ($cities as $key => $city) {
		    $selected = ( $selected_city == $city['slug'] ) ? 'selected' : '';
		    $location_cities_list .= "<option " . $selected . " value='" . $city['slug'] . "'>" . $city['name'] . "</option>";
		}
	    }

	    $towns = isset($locations_data['data']['town']) ? $locations_data['data']['town'] : array();
	    if (isset($towns) && !empty($towns)) {
		foreach ($towns as $key => $town) {
		    $selected = ( $selected_town == $town['slug'] ) ? 'selected' : '';
		    $location_towns_list .= "<option " . $selected . " value='" . $town['slug'] . "'>" . $town['name'] . "</option>";
		}
	    }
	    ?>

	    <fieldset class="gllpLatlonPicker" style="width:100%; float:left;" id="locations-wraper-<?php echo absint($field_postfix) ?>" >
	        <div class="page-wrap page-opts left" style="overflow:hidden; position:relative;" id="locations_wrap" data-themeurl="<?php echo wp_foodbakery::plugin_url(); ?>" data-plugin_url="<?php echo wp_foodbakery::plugin_url(); ?>" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php'), 'foodbakery'); ?>" data-map_marker="<?php echo esc_html($foodbakery_map_marker_icon); ?>">
	    	<div class="option-sec" style="margin-bottom:0;">
	    	    <div class="opt-conts">
			    <?php
			    $output = '';
			    if (isset($locations_data['data']['country'])) {
				$foodbakery_opt_array = array(
				    'name' => esc_html__('Country', 'foodbakery'),
				    'desc' => '',
				    'field_params' => array(
					'std' => '',
					'cust_id' => 'loc_country_' . $field_postfix,
					'cust_name' => 'foodbakery_post_loc_country_' . $field_postfix,
					'classes' => 'chosen-select form-select-country dir-map-search  ',
					'options_markup' => true,
					'return' => true,
				    ),
				);

				if (isset($location_countries_list) && $location_countries_list != '') {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Country', 'foodbakery') . '</option>' . $location_countries_list;
				} else {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Country', 'foodbakery') . '</option>';
				}

				$output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			    }

			    if (isset($locations_data['data']['state'])) {
				$foodbakery_opt_array = array(
				    'name' => esc_html__('State', 'foodbakery'),
				    'id' => 'loc_state_' . $field_postfix . '_container',
				    'desc' => '',
				    'field_params' => array(
					'std' => '',
					'id' => 'loc_state_' . $field_postfix,
					'cust_id' => 'loc_state_' . $field_postfix,
					'cust_name' => 'foodbakery_post_loc_state_' . $field_postfix,
					'classes' => 'chosen-select form-select-state dir-map-search ',
					'markup' => '<span class="loader-state-' . $field_postfix . '"></span>',
					'options_markup' => true,
					'return' => true,
				    ),
				);
				if (isset($location_states_list) && $location_states_list != '') {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select State', 'foodbakery') . '</option>' . $location_states_list;
				} else {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select State', 'foodbakery') . '</option>';
				}

				$output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			    }

                $output .= apply_filters('foodbakery_admin_county_countytax_fields', isset( $locations_data['data']['county'] )? $locations_data['data']['county'] : '', $field_postfix, $location_county_list, $post->ID);

			    if (isset($locations_data['data']['city'])) {
				$foodbakery_opt_array = array(
				    'name' => esc_html__('City', 'foodbakery'),
				    'id' => 'loc_city_' . $field_postfix . '_container',
				    'desc' => '',
				    'field_params' => array(
					'std' => '',
					'id' => 'loc_city_' . $field_postfix,
					'cust_id' => 'loc_city_' . $field_postfix,
					'cust_name' => 'foodbakery_post_loc_city_' . $field_postfix,
					'classes' => 'chosen-select form-select-city dir-map-search ',
					'markup' => '<span class="loader-city-' . $field_postfix . '"></span>',
					'options_markup' => true,
					'return' => true,
				    ),
				);
				if (isset($location_cities_list) && $location_cities_list != '') {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select City', 'foodbakery') . '</option>' . $location_cities_list;
				} else {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select City', 'foodbakery') . '</option>';
				}

				$output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			    }

			    if (isset($locations_data['data']['town'])) {
				$foodbakery_opt_array = array(
				    'name' => esc_html__('Town', 'foodbakery'),
				    'id' => 'loc_town_' . $field_postfix . '_container',
				    'desc' => '',
				    'field_params' => array(
					'std' => '',
					'id' => 'loc_town_' . $field_postfix,
					'cust_id' => 'loc_town_' . $field_postfix,
					'cust_name' => 'foodbakery_post_loc_town_' . $field_postfix,
					'classes' => 'chosen-select form-select-town dir-map-search ',
					'markup' => '<span class="loader-town-' . $field_postfix . '"></span>',
					'options_markup' => true,
					'return' => true,
				    ),
				);
				if (isset($location_towns_list) && $location_towns_list != '') {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Town', 'foodbakery') . '</option>' . $location_towns_list;
				} else {
				    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Town', 'foodbakery') . '</option>';
				}

				$output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
			    }

			    $output .= '
							<div class="theme-help" id="mailing_information">
								<h4 style="padding-bottom:0px;">' . esc_html__('Find on Map', 'foodbakery') . '</h4>
								<div class="clear"></div>
							</div>';

			    $foodbakery_opt_array = array(
				'name' => esc_html__('Address', 'foodbakery'),
				'desc' => '',
				'field_params' => array(
				    'std' => $foodbakery_post_loc_address,
				    'id' => 'loc_address',
				    'classes' => 'foodbakery-search-location',
				    'extra_atr' => 'onkeypress="foodbakery_gl_search_map(this.value)"',
				    'cust_id' => 'loc_address',
				    'cust_name' => 'foodbakery_post_loc_address_' . $field_postfix,
				    'return' => true,
				    'force_std' => true,
				),
			    );

			    $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    $foodbakery_opt_array = array(
				'name' => esc_html__('Latitude', 'foodbakery'),
				'id' => 'post_loc_latitude',
				'desc' => '',
				'field_params' => array(
				    'std' => $foodbakery_post_loc_latitude,
				    'id' => 'post_loc_latitude',
				    'cust_name' => 'foodbakery_post_loc_latitude_' . $field_postfix,
				    'classes' => 'gllpLatitude',
				    'return' => true,
				    'force_std' => true,
				),
			    );

			    if (isset($value['split']) && $value['split'] <> '') {
				$foodbakery_opt_array['split'] = $value['split'];
			    }

			    $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    $foodbakery_opt_array = array(
				'name' => esc_html__('Longitude', 'foodbakery'),
				'id' => 'post_loc_longitude',
				'desc' => '',
				'field_params' => array(
				    'std' => $foodbakery_post_loc_longitude,
				    'id' => 'post_loc_longitude',
				    'cust_name' => 'foodbakery_post_loc_longitude_' . $field_postfix,
				    'classes' => 'gllpLongitude',
				    'return' => true,
				    'force_std' => true,
				),
			    );

			    if (isset($value['split']) && $value['split'] <> '') {
				$foodbakery_opt_array['split'] = $value['split'];
			    }
			    $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    if ($user == '') {
				$foodbakery_opt_array = array(
				    'name' => esc_html__('Exact Location/Radius', 'foodbakery'),
				    'desc' => '',
				    'field_params' => array(
					'std' => $foodbakery_loc_radius,
					'id' => 'loc_radius_' . $field_postfix,
					'return' => true,
				    ),
				);
				$output .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
			    }

			    $foodbakery_opt_array = array(
				'name' => '',
				'id' => 'map_search_btn',
				'desc' => '',
				'field_params' => array(
				    'std' => esc_html__('Search This Location on Map', 'foodbakery'),
				    'id' => 'map_search_btn',
				    'cust_type' => 'button',
				    'classes' => 'gllpSearchButton cs-bgcolor',
				    'return' => true,
				),
			    );

			    if (isset($value['split']) && $value['split'] <> '') {
				$foodbakery_opt_array['split'] = $value['split'];
			    }

			    $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    $output .= $foodbakery_html_fields->foodbakery_full_opening_field(array());
			    $output .= '<div class="clear"></div>';

			    $foodbakery_opt_array = array(
				'id' => 'add_new_loc',
				'cust_name' => 'foodbakery_add_new_loc_' . $field_postfix,
				'std' => $foodbakery_add_new_loc,
				'cust_type' => 'hidden',
				'classes' => 'gllpSearchField',
				'return' => true,
				'force_std' => true,
			    );

			    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
			    $foodbakery_opt_array = array(
				'id' => 'post_loc_zoom',
				'cust_name' => 'foodbakery_post_loc_zoom_' . $field_postfix,
				'std' => $foodbakery_post_loc_zoom,
				'cust_type' => 'hidden',
				'classes' => 'gllpZoom',
				'return' => true,
				'force_std' => true,
			    );

			    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
			    $output .= '<div class="clear"></div><div class="cs-map-section" style="float:left; width:100%; height:100%;"><div class="gllpMap" id="cs-map-location-fe-id"></div></div>';
			    $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
				'desc' => '',
				    )
			    );
			    echo balanceTags($output);
			    ?>
	    	    </div>
	    	</div>
	        </div>
	    </fieldset>
	    <script type="text/javascript">
	        "use strict";
	        var autocomplete;
	        jQuery(document).ready(function () {
	    	foodbakery_load_location_ajax('<?php echo esc_html($field_postfix); ?>', <?php echo json_encode(array_keys($locations_data['data'])); ?>, <?php echo json_encode($locations_data['location_levels']); ?>, '<?php echo wp_create_nonce('get_locations_list'); ?>');
	        });


	        function foodbakery_gl_search_map() {

				console.log('class-save post');

	    	var vals;
	    	vals = jQuery('#loc_address').val();
	    	if (jQuery('#loc_town').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_town').val();
	    	}
	    	if (jQuery('#loc_city').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_city').val();
	    	}
	    	if (jQuery('#loc_state').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_state').val();
	    	}
	    	if (jQuery('#loc_country').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_country').val();
	    	}
	    	jQuery('.gllpSearchField').val(vals);

	        }
	        (function ($) {
	    	$(function () {
	    <?php $foodbakery_obj->foodbakery_google_place_scripts() ?>
	    	    autocomplete = new google.maps.places.Autocomplete(document.getElementById('loc_address'));
	    <?php if (isset($selected_iso_code) && !empty($selected_iso_code)) : ?>
			    autocomplete.setComponentRestrictions({'country': '<?php echo esc_html($selected_iso_code); ?>'});
	    <?php endif; ?>
	    	});
	        })(jQuery);


	    </script>
	    <?php
	}

	/**
	 * How to show location fields in front end
	 */
	public function frontend_location_fields_custom($post_id = '', $field_postfix = '', $user = '') {
		global $foodbakery_plugin_options, $post, $foodbakery_html_fields, $foodbakery_html_fields2, $foodbakery_html_fields_frontend, $foodbakery_form_fields;
		$foodbakery_map_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
		$foodbakery_map_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
		$foodbakery_map_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '10';
		$foodbakery_map_marker_icon = isset($foodbakery_plugin_options['foodbakery_map_marker_icon']) ? $foodbakery_plugin_options['foodbakery_map_marker_icon'] : wp_foodbakery::plugin_url() . '/assets/images/map-marker.png';
		$foodbakery_post_loc_zoom = $foodbakery_map_zoom;
		$foodbakery_array_data = '';

        $flag = apply_filters('foodbakery_add_county_in_location_level', false);

		if (isset($user) && !empty($user)) { // get values from usermeta

		$foodbakery_post_loc_town = get_the_author_meta('foodbakery_post_loc_town_' . $field_postfix, $user->ID);



        if($flag) {
            $foodbakery_post_loc_county = get_the_author_meta('foodbakery_post_loc_county_' . $field_postfix, $user->ID);
            if(isset($foodbakery_post_loc_county) && !empty($foodbakery_post_loc_county) ){
                $foodbakery_post_loc_county = get_the_author_meta('foodbakery_post_loc_county_' . $field_postfix, $user->ID);
            }
        }else{
            $foodbakery_post_loc_county = '';
        }

		$foodbakery_post_loc_city = get_the_author_meta('foodbakery_post_loc_city_' . $field_postfix, $user->ID);
		$foodbakery_post_loc_state = get_the_author_meta('foodbakery_post_loc_state_' . $field_postfix, $user->ID);
		$foodbakery_post_loc_country = get_the_author_meta('foodbakery_post_loc_country_' . $field_postfix, $user->ID);

			if (
				( isset($foodbakery_post_loc_town) && !empty($foodbakery_post_loc_town) ) ||
				( isset($foodbakery_post_loc_city) && !empty($foodbakery_post_loc_city) ) ||
				( isset($foodbakery_post_loc_state) && !empty($foodbakery_post_loc_state) ) ||
				( isset($foodbakery_post_loc_country) && !empty($foodbakery_post_loc_country) )
			) {
				$foodbakery_post_loc_latitude = get_the_author_meta('foodbakery_post_loc_latitude_' . $field_postfix, $user->ID);
				$foodbakery_post_loc_longitude = get_the_author_meta('foodbakery_post_loc_longitude_' . $field_postfix, $user->ID);
				$foodbakery_post_loc_zoom = get_the_author_meta('foodbakery_post_loc_zoom_' . $field_postfix, $user->ID);
				$foodbakery_post_loc_address = get_the_author_meta('foodbakery_post_loc_address_' . $field_postfix, $user->ID);
				$foodbakery_post_comp_address = get_the_author_meta('foodbakery_post_comp_address_' . $field_postfix, $user->ID);
				$foodbakery_add_new_loc = get_the_author_meta('foodbakery_add_new_loc_' . $field_postfix, $user->ID);
				$foodbakery_loc_radius = get_the_author_meta('foodbakery_loc_radius_' . $field_postfix, $user->ID);
			} else {
				$foodbakery_post_loc_country = '';
				$foodbakery_post_loc_region = '';
				$foodbakery_post_loc_town = '';
				$foodbakery_post_loc_city = '';
				$foodbakery_post_loc_state = '';
				$foodbakery_post_loc_address = '';
				$foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
				$foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
				$foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_post_loc_zoom']) ? $foodbakery_plugin_options['foodbakery_post_loc_zoom'] : '';
				$loc_city = '';
				$loc_postcode = '';
				$loc_region = '';
				$loc_country = '';
				$event_map_switch = '';
				$event_map_heading = '';
				$foodbakery_add_new_loc = '';
				$foodbakery_post_comp_address = '';
				$foodbakery_loc_radius = '';
			}
		} else {
			$foodbakery_array_data = get_post_meta($post_id, 'foodbakery_array_data', true);
            if($flag) {
                $foodbakery_post_loc_county = get_post_meta($post_id, 'foodbakery_post_loc_county_' . $field_postfix, true);
            }else{
                $foodbakery_post_loc_county = '';
            }
				if (isset($foodbakery_array_data) && !empty($foodbakery_array_data)) {
					$foodbakery_post_loc_town = get_post_meta($post_id, 'foodbakery_post_loc_town_' . $field_postfix, true);

					$foodbakery_post_loc_city = get_post_meta($post_id, 'foodbakery_post_loc_city_' . $field_postfix, true);
					$foodbakery_post_loc_state = get_post_meta($post_id, 'foodbakery_post_loc_state_' . $field_postfix, true);
					$foodbakery_post_loc_country = get_post_meta($post_id, 'foodbakery_post_loc_country_' . $field_postfix, true);
					$foodbakery_post_loc_latitude = get_post_meta($post_id, 'foodbakery_post_loc_latitude_' . $field_postfix, true);
					$foodbakery_post_loc_longitude = get_post_meta($post_id, 'foodbakery_post_loc_longitude_' . $field_postfix, true);
					$foodbakery_post_loc_zoom = get_post_meta($post_id, 'foodbakery_post_loc_zoom_' . $field_postfix, true);
					$foodbakery_post_loc_address = get_post_meta($post_id, 'foodbakery_post_loc_address_' . $field_postfix, true);
					$foodbakery_post_comp_address = get_post_meta($post_id, 'foodbakery_post_comp_address_' . $field_postfix, true);
					$foodbakery_add_new_loc = get_post_meta($post_id, 'foodbakery_add_new_loc_' . $field_postfix, true);
					$foodbakery_loc_bounds_rest = get_post_meta($post_id, 'foodbakery_loc_bounds_rest_' . $field_postfix, true);
					$foodbakery_loc_radius = get_post_meta($post_id, 'foodbakery_loc_radius_' . $field_postfix, true);
				} else {
					$foodbakery_post_loc_country = '';
					$foodbakery_post_loc_region = '';
					$foodbakery_post_loc_state = '';

					$foodbakery_post_loc_city = '';
					$foodbakery_post_loc_town = '';
					$foodbakery_post_loc_address = get_post_meta($post_id, 'foodbakery_post_loc_address_' . $field_postfix, true); //edit sagar
					$foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
					$foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
					$foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_post_loc_zoom']) ? $foodbakery_plugin_options['foodbakery_post_loc_zoom'] : '';
					$loc_city = '';
					$loc_postcode = '';
					$loc_region = '';
					$loc_country = '';
					$event_map_switch = '';
					$event_map_heading = '';
					$foodbakery_add_new_loc = '';
					$foodbakery_loc_bounds_rest = '';
					$foodbakery_post_comp_address = '';
					$foodbakery_loc_radius = '';
				}
		}
		if ($foodbakery_post_loc_latitude == '')
		$foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
		if ($foodbakery_post_loc_longitude == '')
		$foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
		if ($foodbakery_post_loc_zoom == '')
		$foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '';

		$foodbakery_obj = new wp_foodbakery();
		$foodbakery_obj->foodbakery_location_gmap_script();
		$foodbakery_obj->foodbakery_google_place_scripts();
		$foodbakery_obj->foodbakery_autocomplete_scripts();

        if($flag) {
            $locations_data = array(
                'data' => array(
                    'country' => array(),
                    'state' => array(),
                    'county' => array(),
                    'city' => array(),
                    'town' => array(),
                ),
                'selected' => array(
                    'country' => $foodbakery_post_loc_country,
                    'state' => $foodbakery_post_loc_state,
                    'county' => $foodbakery_post_loc_county,
                    'city' => $foodbakery_post_loc_city,
                    'town' => $foodbakery_post_loc_town,
                ),
                'location_levels' => array(
                    'country' => -1,
                    'state' => -1,
                    'city' => -1,
                    'town' => -1,
                ),
            );
        }else{
            $locations_data = array(
                'data' => array(
                    'country' => array(),
                    'state' => array(),
                    'city' => array(),
                    'town' => array(),
                ),
                'selected' => array(
                    'country' => $foodbakery_post_loc_country,
                    'state' => $foodbakery_post_loc_state,
                    'city' => $foodbakery_post_loc_city,
                    'town' => $foodbakery_post_loc_town,
                ),
                'location_levels' => array(
                    'country' => -1,
                    'state' => -1,
                    'city' => -1,
                    'town' => -1,
                ),
            );
        }

		$locations_data = apply_filters('get_locations_fields_data', $locations_data, 'locations_fields_selector');
		$locations_parent_id = 0;
		$foodbakery_location_countries = isset($locations_data['data']['country']) ? $locations_data['data']['country'] : array();
		$location_countries_list = '';
		$location_states_list = '';
                $location_counties_list = '';

		$location_cities_list = '';
		$location_towns_list = '';
		$iso_code_list = '';
		$iso_code_list_main = '';
		$iso_code = '';
		if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {
		$selected_iso_code = '';
		foreach ($foodbakery_location_countries as $key => $country) {
			$selected = '';
			$iso_code_list_main = $country['iso_code'];

			if (isset($foodbakery_post_loc_country) && $foodbakery_post_loc_country == $country['slug']) {
			$selected = 'selected';
			$selected_iso_code = $iso_code_list_main;
			}
			$location_countries_list .= "<option " . $selected . "  value='" . $country['slug'] . "' data-name='" . $iso_code_list_main . "'>" . $country['name'] . "</option>";
		}
		}

		$selected_country = $foodbakery_post_loc_country;
		$selected_state = $foodbakery_post_loc_state;
        if($flag) {
            $selected_county = $foodbakery_post_loc_county;
        }
		$selected_city = $foodbakery_post_loc_city;
		$selected_town = $foodbakery_post_loc_town;

		$states = isset($locations_data['data']['state']) ? $locations_data['data']['state'] : array();
		if (isset($states) && !empty($states)) {
		foreach ($states as $key => $state) {
			$selected = ( $selected_state == $state['slug'] ) ? 'selected' : '';
			$location_states_list .= "<option " . $selected . " value='" . $state['slug'] . "'>" . $state['name'] . "</option>";
		}
		}
        if($flag) {
            $counties = isset($locations_data['data']['county']) ? $locations_data['data']['county'] : array();
            if (isset($counties) && !empty($counties)) {
            foreach ($counties as $key => $county) {
                $selected = ( $selected_county == $county['slug'] ) ? 'selected' : '';
                $location_counties_list .= "<option " . $selected . " value='" . $county['slug'] . "'>" . $county['name'] . "</option>";
            }
            }
		}

		$cities = isset($locations_data['data']['city']) ? $locations_data['data']['city'] : array();
		if (isset($cities) && !empty($cities)) {
		foreach ($cities as $key => $city) {
			$selected = ( $selected_city == $city['slug'] ) ? 'selected' : '';
			$location_cities_list .= "<option " . $selected . " value='" . $city['slug'] . "'>" . $city['name'] . "</option>";
		}
		}

		$towns = isset($locations_data['data']['town']) ? $locations_data['data']['town'] : array();
		if (isset($towns) && !empty($towns)) {
		foreach ($towns as $key => $town) {
			$selected = ( $selected_town == $town['slug'] ) ? 'selected' : '';
			$location_towns_list .= "<option " . $selected . " value='" . $town['slug'] . "'>" . $town['name'] . "</option>";
		}
		}
		?>
		<?php
		$radius_circle = isset($foodbakery_plugin_options['foodbakery_default_radius_circle']) ? $foodbakery_plugin_options['foodbakery_default_radius_circle'] : '10';
		$radius_circle = ($radius_circle * 1000);
		?>
		<?php
		if ($field_postfix == 'publisher') {
			$foodbakery_loc_radius = 'off';
		}
		?>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<fieldset class="gllpLatlonPicker" style="width:100%; float:left;" id="fe_map<?php echo absint($field_postfix) ?>" data-radius="<?php echo esc_html($radius_circle); ?>" data-radiusshow="<?php echo esc_html($foodbakery_loc_radius); ?>">
			<div class="page-wrap page-opts left" style=" position:relative;" id="locations_wrap" data-default-country="<?php echo ($foodbakery_post_loc_country) ?>" data-themeurl="<?php echo wp_foodbakery::plugin_url(); ?>" data-plugin_url="<?php echo wp_foodbakery::plugin_url(); ?>" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php'), 'foodbakery'); ?>" data-map_marker="<?php echo esc_html($foodbakery_map_marker_icon); ?>">
				<div class="option-sec" style="margin-bottom:0;">
				<div class="opt-conts">
					<?php if (isset($locations_data['data']['country'])) : ?>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="field-holder">
								<label><?php esc_html_e('Country *', 'foodbakery'); ?></label>
								<div class="select-holder">
									<?php
									$output = '';
									if (isset($locations_data['data']['country'])) {
									$foodbakery_opt_array = array(
										'name' => esc_html__('Country', 'foodbakery'),
										'desc' => '',
										'echo' => true,
										'field_params' => array(
										'std' => $foodbakery_post_loc_country,
										'cust_id' => 'loc_country_' . $field_postfix,
										'force_std' => true,
										'cust_name' => 'foodbakery_post_loc_country_' . $field_postfix,
										'classes' => 'form-control chosen-select form-select-country dir-map-search foodbakery-dev-req-field ',
										'markup' => '<span class="loader-country-' . $field_postfix . '"></span>',
										'extra_atr' => 'data-placeholder="' . esc_html__("Select Country", 'foodbakery') . '"',
										'options_markup' => true,
										'return' => false,
										),
									);

									if (isset($location_countries_list) && $location_countries_list != '') {
										$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Country', 'foodbakery') . '</option>' . $location_countries_list;
									} else {
										$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Country', 'foodbakery') . '</option>';
									}

									$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
									}
									?>

								</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<div class="row">
						<?php if (isset($locations_data['data']['state'])) : ?>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="field-holder">
									<label><?php esc_html_e('State', 'foodbakery'); ?></label>
									<div class="select-holder">
										<?php
										if (isset($locations_data['data']['state'])) {
										$foodbakery_opt_array = array(
											'name' => esc_html__('State', 'foodbakery'),
											'id' => 'loc_state_' . $field_postfix . '_container',
											'desc' => '',
											'echo' => true,
											'field_params' => array(
											'std' => $foodbakery_post_loc_state,
											'id' => 'loc_state_' . $field_postfix,
											'cust_id' => 'loc_state_' . $field_postfix,
											'cust_name' => 'foodbakery_post_loc_state_' . $field_postfix,
											'classes' => 'form-control chosen-select form-select-state dir-map-search ',
											'markup' => '<span class="loader-state-' . $field_postfix . '"></span>',
											'extra_atr' => 'data-placeholder="' . esc_html__("Select State", 'foodbakery') . '"',
											'options_markup' => true,
											'return' => false,
											),
										);
										if (isset($location_states_list) && $location_states_list != '') {
											$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select State', 'foodbakery') . '</option>' . $location_states_list;
										} else {
											$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select State', 'foodbakery') . '</option>';
										}

										$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
										}
										?>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php do_action('foodbakery_county_countytax_fields', isset($locations_data['data']['county']), $field_postfix, $foodbakery_post_loc_city, $location_counties_list, $post_id); ?>

                        <?php  if (isset($locations_data['data']['city'])) : ?>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="field-holder">
									<label><?php esc_html_e('City', 'foodbakery'); ?></label>
									<div class="select-holder">
										<?php
										if (isset($locations_data['data']['city'])) {
										$foodbakery_opt_array = array(
											'name' => esc_html__('City', 'foodbakery'),
											'id' => 'loc_city_' . $field_postfix . '_container',
											'desc' => '',
											'echo' => true,
											'field_params' => array(
											'std' => $foodbakery_post_loc_city,
											'id' => 'loc_city_' . $field_postfix,
											'cust_id' => 'loc_city_' . $field_postfix,
											'cust_name' => 'foodbakery_post_loc_city_' . $field_postfix,
											'classes' => 'form-control chosen-select form-select-city dir-map-search ',
											'markup' => '<span class="loader-city-' . $field_postfix . '"></span>',
											'extra_atr' => 'data-placeholder="' . esc_html__("Select City", 'foodbakery') . '"',
											'options_markup' => true,
											'return' => false,
											),
										);
                                            if (isset($location_cities_list) && $location_cities_list != '') {
                                                $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select County', 'foodbakery') . '</option>' . $location_cities_list;
                                            } else {
                                                $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select County', 'foodbakery') . '</option>';
                                            }


										$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
										}
										?>
									</div>
								</div>
							</div>
						<?php endif;  ?>


						<?php if (isset($locations_data['data']['town'])) : ?>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="field-holder">
									<label><?php esc_html_e('Town', 'foodbakery'); ?></label>
									<div class="select-holder">
										<?php
										$foodbakery_opt_array = array(
											'name' => esc_html__('Town', 'foodbakery'),
											'id' => 'loc_town_' . $field_postfix . '_container',
											'desc' => '',
											'echo' => true,
											'field_params' => array(
												'std' => $foodbakery_post_loc_town,
												'force_std' => true,
												'id' => 'loc_town_' . $field_postfix,
												'cust_id' => 'loc_town_' . $field_postfix,
												'cust_name' => 'foodbakery_post_loc_town_' . $field_postfix,
												'classes' => 'form-control chosen-select form-select-town dir-map-search ',
												'markup' => '<span class="loader-town-' . $field_postfix . '"></span>',
												'extra_atr' => 'data-placeholder="' . esc_html__("Select Town", 'foodbakery') . '"',
												'options_markup' => true,
												'return' => false,
											),
										);
										if (isset($location_towns_list) && $location_towns_list != '') {
											$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Town', 'foodbakery') . '</option>' . $location_towns_list;
										} else {
											$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Town', 'foodbakery') . '</option>';
										}

										$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
										?>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="display:none">
							<div class="field-holder">
							<label><?php esc_html_e('Latitude', 'foodbakery'); ?></label>
							<?php
							$foodbakery_opt_array = array(
								'name' => esc_html__('Latitude', 'foodbakery'),
								'id' => 'post_loc_latitude',
								'desc' => '',
								'styles' => 'display:none;',
								'echo' => true,
								'field_params' => array(
								'std' => $foodbakery_post_loc_latitude,
								'id' => 'post_loc_latitude',
								'cust_name' => 'foodbakery_post_loc_latitude_' . $field_postfix,
								'extra_atr' => 'placeholder="' . esc_html__('Latitude', 'foodbakery') . '"',
								'classes' => 'form-control gllpLatitude',
								'return' => false,
								'force_std' => true,
								),
							);

							if (isset($value['split']) && $value['split'] <> '') {
								$foodbakery_opt_array['split'] = $value['split'];
							}

							$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="display:none">
							<div class="field-holder">
							<label><?php esc_html_e('Longitude', 'foodbakery'); ?></label>
							<?php
							$foodbakery_opt_array = array(
								'name' => esc_html__('Longitude', 'foodbakery'),
								'id' => 'post_loc_longitude',
								'desc' => '',
								'echo' => true,
								'field_params' => array(
								'std' => $foodbakery_post_loc_longitude,
								'id' => 'post_loc_longitude',
								'cust_name' => 'foodbakery_post_loc_longitude_' . $field_postfix,
								'extra_atr' => 'placeholder="' . esc_html__('Longitude', 'foodbakery') . '"',
								'classes' => 'form-control gllpLongitude',
								'return' => false,
								'force_std' => true,
								),
							);

							if (isset($value['split']) && $value['split'] <> '') {
								$foodbakery_opt_array['split'] = $value['split'];
							}
							$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
							</div>
						</div>

						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<?php
							$foodbakery_opt_array = array(
								'id' => '_loc_bounds_rest',
								'cust_name' => 'foodbakery_loc_bounds_rest_' . $field_postfix,
								'std' => $foodbakery_loc_bounds_rest,
								'classes' => '',
								'force_std' => true,
							);

							$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

							$foodbakery_opt_array = array(
								'id' => 'add_new_loc',
								'cust_name' => 'foodbakery_add_new_loc_' . $field_postfix,
								'std' => $foodbakery_add_new_loc,
								'classes' => 'gllpSearchField',
								'extra_atr' => 'style="margin-bottom:10px;"',
								'return' => false,
								'force_std' => true,
							);

							$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

							$foodbakery_opt_array = array(
								'id' => '',
								'std' => esc_attr($foodbakery_post_loc_zoom),
								'cust_id' => 'foodbakery_post_loc_zoom',
								'cust_name' => "foodbakery_post_loc_zoom_" . $field_postfix,
								'classes' => 'gllpZoom',
								'return' => false,
								'force_std' => true,
							);

							$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
							?>

							<div class="field-holder">
								<label><?php esc_html_e('Find On Map', 'foodbakery'); ?></label>
								<?php
								$foodbakery_opt_array = array(
									'name' => '',
									'desc' => '',
									'echo' => true,
									'field_params' => array(
									'std' => $foodbakery_post_loc_address, //edit sagar
									'cust_id' => 'loc_address',
									'classes' => 'foodbakery-search-location',
									'extra_atr' => 'onkeypress="foodbakery_gl_search_map(this.value)" placeholder="' . esc_html__('Type Your Address', 'foodbakery') . '"',
									'cust_name' => 'foodbakery_post_loc_address_' . $field_postfix,
									'return' => false,
									'force_std' => true,
									),
								);
								if (isset($value['address_hint']) && $value['address_hint'] != '') {
									$foodbakery_opt_array['hint_text'] = $value['address_hint'];
								}
								if (isset($value['split']) && $value['split'] <> '') {
									$foodbakery_opt_array['split'] = $value['split'];
								}
								$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
								?>
							</div>
						</div>

						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="field-holder"></div>
							<div class="field-holder search-location-map input-button-loader">
								<?php
								$foodbakery_opt_array = array(
									'name' => '',
									'id' => 'map_search_btn',
									'desc' => '',
									'echo' => true,
									'field_params' => array(
									'std' => esc_html__('Search Location', 'foodbakery'),
									'id' => 'map_search_btn',
									'cust_type' => 'button',
									'classes' => 'acc-submit cs-section-update cs-color csborder-color gllpSearchButton',
									'return' => false,
									),
								);

								if (isset($value['split']) && $value['split'] <> '') {
									$foodbakery_opt_array['split'] = $value['split'];
								}

								$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
								?>
							</div>
							<input  class="btn btn-primary sa_location" type="button"  style="padding: 9px;margin-left: 10px;" value="My location" />
						</div>
					</div>
					<div class="row">
						<!-- map start -->
						<div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="cs-map-section" style="float:left; width:100%; height:284px;">
							<div class="gllpMap" id="cs-map-location-fe-id"></div>
							</div>
							<!--<p> <?php esc_html_e('For the precise location, you can drag and drop the pin.', 'foodbakery'); ?></p>-->
						</div>
					</div>
				</div>
				</div>
			</div>
			</fieldset>
		</div>
		<script type="text/javascript">
			// Call Map gMapsLatLonPicker Class

			jQuery(document).on("change", "#myonoffswitch2", function () {

                           // alert('fsdfsd');
			$check = jQuery(this).is(':checked');
			if ($check) {
				jQuery(".gllpLatlonPicker").each(function () {
				var radius = $(this).data('radius');
				$obj = jQuery(document).gMapsLatLonPicker(radius);
				$obj.init(jQuery(this));
				});
			} else {
				jQuery(".gllpLatlonPicker").each(function () {

				$obj = jQuery(document).gMapsLatLonPicker();
				$obj.init(jQuery(this));
				});
			}
			});
			jQuery(document).ready(function () {
			chosen_selectionbox();

			jQuery(".gllpLatlonPicker").each(function () {
				var radius = $(this).data('radius');
				var show_rad = $(this).data('radiusshow');
				if (show_rad == 'on') {
				$obj = jQuery(document).gMapsLatLonPicker(radius);
				} else {
				$obj = jQuery(document).gMapsLatLonPicker();
				}
				$obj.init(jQuery(this));
			});
			});

			jQuery(document).ready(function () {
			foodbakery_load_location_ajax('<?php echo esc_html($field_postfix); ?>', <?php echo json_encode(array_keys($locations_data['data'])); ?>, <?php echo json_encode($locations_data['location_levels']); ?>, '<?php echo wp_create_nonce('get_locations_list'); ?>');
			});
			function foodbakery_gl_search_map() {
			var vals;


			vals = jQuery('#loc_address').val();
			if (jQuery('#loc_town').length > 0) {
				vals = vals + ", " + jQuery('#loc_town').val();
			}
			if (jQuery('#loc_city').length > 0) {
				vals = vals + ", " + jQuery('#loc_city').val();
			}
			if (jQuery('#loc_state').length > 0) {
				vals = vals + ", " + jQuery('#loc_state').val();
			}
			if (jQuery('#loc_country').length > 0) {
				vals = vals + ", " + jQuery('#loc_country').val();
			}
			jQuery('.gllpSearchField').val(vals);
			jQuery('#profile_form').attr('dis', 1);
			}
			function foodbakery_fe_search_map() {
			var vals;
			vals = jQuery('#fe_map<?php echo absint($field_postfix) ?> #loc_address').val();
			jQuery('#fe_map<?php echo absint($field_postfix); ?> .gllpSearchField_fe').val(vals);
			}

			(function ($) {
			$(function () {
		<?php $foodbakery_obj->foodbakery_google_place_scripts(); ?> //var autocomplete;
				autocomplete = new google.maps.places.Autocomplete(document.getElementById('loc_address'));
		<?php if (isset($selected_iso_code) && !empty($selected_iso_code)) { ?>
				autocomplete.setComponentRestrictions({'country': '<?php echo esc_js($selected_iso_code) ?>'});
		<?php } ?>
			});
			})(jQuery);
			jQuery(document).ready(function () {
			var $ = jQuery;
			jQuery("[id^=map_canvas]").css("pointer-events", "none");
			jQuery("[id^=cs-map-location]").css("pointer-events", "none");
			// on leave handle
			var onMapMouseleaveHandler = function (event) {
				var that = jQuery(this);
				that.on('click', onMapClickHandler);
				that.off('mouseleave', onMapMouseleaveHandler);
				jQuery("[id^=map_canvas]").css("pointer-events", "none");
				jQuery("[id^=cs-map-location]").css("pointer-events", "none");
			}
			// on click handle
			var onMapClickHandler = function (event) {
				var that = jQuery(this);
				// Disable the click handler until the user leaves the map area
				that.off('click', onMapClickHandler);
				// Enable scrolling zoom
				that.find('[id^=map_canvas]').css("pointer-events", "auto");
				that.find('[id^=cs-map-location]').css("pointer-events", "auto");
				// Handle the mouse leave event
				that.on('mouseleave', onMapMouseleaveHandler);
			}
			// Enable map zooming with mouse scroll when the user clicks the map
			jQuery('.cs-map-section').on('click', onMapClickHandler);
			// new addition
			});
		</script>
		<?php
	}

	/**
	 * How to show location fields in front end
	 */
	public function foodbakery_frontend_location_fields($post_id = '', $field_postfix = '', $user = '') {

	    global $foodbakery_plugin_options, $post, $foodbakery_html_fields, $foodbakery_html_fields2, $foodbakery_html_fields_frontend, $foodbakery_form_fields;
	    $foodbakery_map_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
	    $foodbakery_map_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
	    $foodbakery_map_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '10';
	    $foodbakery_map_marker_icon = isset($foodbakery_plugin_options['foodbakery_map_marker_icon']) ? $foodbakery_plugin_options['foodbakery_map_marker_icon'] : wp_foodbakery::plugin_url() . '/assets/images/map-marker.png';
	    $foodbakery_post_loc_zoom = $foodbakery_map_zoom;
	    $foodbakery_array_data = '';
	    if (isset($user) && !empty($user)) { // get values from usermeta

		$foodbakery_post_loc_town = get_the_author_meta('foodbakery_post_loc_town_' . $field_postfix, $user->ID);
		$foodbakery_post_loc_city = get_the_author_meta('foodbakery_post_loc_city_' . $field_postfix, $user->ID);
		$foodbakery_post_loc_state = get_the_author_meta('foodbakery_post_loc_state_' . $field_postfix, $user->ID);
		$foodbakery_post_loc_country = get_the_author_meta('foodbakery_post_loc_country_' . $field_postfix, $user->ID);
		if (
			( isset($foodbakery_post_loc_town) && !empty($foodbakery_post_loc_town) ) ||
			( isset($foodbakery_post_loc_city) && !empty($foodbakery_post_loc_city) ) ||
			( isset($foodbakery_post_loc_state) && !empty($foodbakery_post_loc_state) ) ||
			( isset($foodbakery_post_loc_country) && !empty($foodbakery_post_loc_country) )
		) {
		    $foodbakery_post_loc_latitude = get_the_author_meta('foodbakery_post_loc_latitude_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_longitude = get_the_author_meta('foodbakery_post_loc_longitude_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_zoom = get_the_author_meta('foodbakery_post_loc_zoom_' . $field_postfix, $user->ID);
		    $foodbakery_post_loc_address = get_the_author_meta('foodbakery_post_loc_address_' . $field_postfix, $user->ID);
		    $foodbakery_post_comp_address = get_the_author_meta('foodbakery_post_comp_address_' . $field_postfix, $user->ID);
		    $foodbakery_add_new_loc = get_the_author_meta('foodbakery_add_new_loc_' . $field_postfix, $user->ID);
		    $foodbakery_loc_radius = get_the_author_meta('foodbakery_loc_radius_' . $field_postfix, $user->ID);
		} else {
		    $foodbakery_post_loc_country = '';
		    $foodbakery_post_loc_region = '';
		    $foodbakery_post_loc_town = '';
		    $foodbakery_post_loc_city = '';
		    $foodbakery_post_loc_state = '';
		    $foodbakery_post_loc_address = '';
		    $foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
		    $foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
		    $foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_post_loc_zoom']) ? $foodbakery_plugin_options['foodbakery_post_loc_zoom'] : '';
		    $loc_city = '';
		    $loc_postcode = '';
		    $loc_region = '';
		    $loc_country = '';
		    $event_map_switch = '';
		    $event_map_heading = '';
		    $foodbakery_add_new_loc = '';
		    $foodbakery_post_comp_address = '';
		    $foodbakery_loc_radius = '';
		}
	    } else {
		$foodbakery_array_data = get_post_meta($post_id, 'foodbakery_array_data', true);

		if (isset($foodbakery_array_data) && !empty($foodbakery_array_data)) {
		    $foodbakery_post_loc_town = get_post_meta($post_id, 'foodbakery_post_loc_town_' . $field_postfix, true);
		    $foodbakery_post_loc_city = get_post_meta($post_id, 'foodbakery_post_loc_city_' . $field_postfix, true);
		    $foodbakery_post_loc_state = get_post_meta($post_id, 'foodbakery_post_loc_state_' . $field_postfix, true);
		    $foodbakery_post_loc_country = get_post_meta($post_id, 'foodbakery_post_loc_country_' . $field_postfix, true);
		    $foodbakery_post_loc_latitude = get_post_meta($post_id, 'foodbakery_post_loc_latitude_' . $field_postfix, true);
		    $foodbakery_post_loc_longitude = get_post_meta($post_id, 'foodbakery_post_loc_longitude_' . $field_postfix, true);
		    $foodbakery_post_loc_zoom = get_post_meta($post_id, 'foodbakery_post_loc_zoom_' . $field_postfix, true);
		    $foodbakery_post_loc_address = get_post_meta($post_id, 'foodbakery_post_loc_address_' . $field_postfix, true);
		    $foodbakery_post_comp_address = get_post_meta($post_id, 'foodbakery_post_comp_address_' . $field_postfix, true);
		    $foodbakery_add_new_loc = get_post_meta($post_id, 'foodbakery_add_new_loc_' . $field_postfix, true);
		    $foodbakery_loc_bounds_rest = get_post_meta($post_id, 'foodbakery_loc_bounds_rest_' . $field_postfix, true);
		    $foodbakery_loc_radius = get_post_meta($post_id, 'foodbakery_loc_radius_' . $field_postfix, true);
		} else {
		    $foodbakery_post_loc_country = '';
		    $foodbakery_post_loc_region = '';
		    $foodbakery_post_loc_state = '';
		    $foodbakery_post_loc_city = '';
		    $foodbakery_post_loc_town = '';
		    $foodbakery_post_loc_address = '';
		    $foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
		    $foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
		    $foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_post_loc_zoom']) ? $foodbakery_plugin_options['foodbakery_post_loc_zoom'] : '';
		    $loc_city = '';
		    $loc_postcode = '';
		    $loc_region = '';
		    $loc_country = '';
		    $event_map_switch = '';
		    $event_map_heading = '';
		    $foodbakery_add_new_loc = '';
		    $foodbakery_loc_bounds_rest = '';
		    $foodbakery_post_comp_address = '';
		    $foodbakery_loc_radius = '';
		}
	    }
	    if ($foodbakery_post_loc_latitude == '')
		$foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
	    if ($foodbakery_post_loc_longitude == '')
		$foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
	    if ($foodbakery_post_loc_zoom == '')
		$foodbakery_post_loc_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '';

	    $foodbakery_obj = new wp_foodbakery();
	    $foodbakery_obj->foodbakery_location_gmap_script();
	    $foodbakery_obj->foodbakery_google_place_scripts();
	    $foodbakery_obj->foodbakery_autocomplete_scripts();

	    $locations_data = array(
		'data' => array(
		    'country' => array(),
		    'state' => array(),
		    'city' => array(),
		    'town' => array(),
		),
		'selected' => array(
		    'country' => $foodbakery_post_loc_country,
		    'state' => $foodbakery_post_loc_state,
		    'city' => $foodbakery_post_loc_city,
		    'town' => $foodbakery_post_loc_town,
		),
		'location_levels' => array(
		    'country' => -1,
		    'state' => -1,
		    'city' => -1,
		    'town' => -1,
		),
	    );
	    $locations_data = apply_filters('get_locations_fields_data', $locations_data, 'locations_fields_selector');
	    $locations_parent_id = 0;
	    $foodbakery_location_countries = isset($locations_data['data']['country']) ? $locations_data['data']['country'] : array();
	    $location_countries_list = '';
	    $location_states_list = '';
	    $location_cities_list = '';
	    $location_towns_list = '';
	    $iso_code_list = '';
	    $iso_code_list_main = '';
	    $iso_code = '';
	    if (isset($foodbakery_location_countries) && !empty($foodbakery_location_countries)) {
		$selected_iso_code = '';
		foreach ($foodbakery_location_countries as $key => $country) {
		    $selected = '';
		    $iso_code_list_main = $country['iso_code'];

		    if (isset($foodbakery_post_loc_country) && $foodbakery_post_loc_country == $country['slug']) {
			$selected = 'selected';
			$selected_iso_code = $iso_code_list_main;
		    }
		    $location_countries_list .= "<option " . $selected . "  value='" . $country['slug'] . "' data-name='" . $iso_code_list_main . "'>" . $country['name'] . "</option>";
		}
	    }

	    $selected_country = $foodbakery_post_loc_country;
	    $selected_state = $foodbakery_post_loc_state;
	    $selected_city = $foodbakery_post_loc_city;
	    $selected_town = $foodbakery_post_loc_town;

	    $states = isset($locations_data['data']['state']) ? $locations_data['data']['state'] : array();
	    if (isset($states) && !empty($states)) {
		foreach ($states as $key => $state) {
		    $selected = ( $selected_state == $state['slug'] ) ? 'selected' : '';
		    $location_states_list .= "<option " . $selected . " value='" . $state['slug'] . "'>" . $state['name'] . "</option>";
		}
	    }

	    $cities = isset($locations_data['data']['city']) ? $locations_data['data']['city'] : array();
	    if (isset($cities) && !empty($cities)) {
		foreach ($cities as $key => $city) {
		    $selected = ( $selected_city == $city['slug'] ) ? 'selected' : '';
		    $location_cities_list .= "<option " . $selected . " value='" . $city['slug'] . "'>" . $city['name'] . "</option>";
		}
	    }

	    $towns = isset($locations_data['data']['town']) ? $locations_data['data']['town'] : array();
	    if (isset($towns) && !empty($towns)) {
		foreach ($towns as $key => $town) {
		    $selected = ( $selected_town == $town['slug'] ) ? 'selected' : '';
		    $location_towns_list .= "<option " . $selected . " value='" . $town['slug'] . "'>" . $town['name'] . "</option>";
		}
	    }
	    ?>
	    <?php
	    $radius_circle = isset($foodbakery_plugin_options['foodbakery_default_radius_circle']) ? $foodbakery_plugin_options['foodbakery_default_radius_circle'] : '10';
	    $radius_circle = ($radius_circle * 1000);
	    ?>
	    <?php
	    if ($field_postfix == 'publisher') {
		$foodbakery_loc_radius = 'off';
	    }
	    ?>


	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	        <fieldset class="gllpLatlonPicker" style="width:100%; float:left;" id="fe_map<?php echo absint($field_postfix) ?>" data-radius="<?php echo esc_html($radius_circle); ?>" data-radiusshow="<?php echo esc_html($foodbakery_loc_radius); ?>">
	    	<div class="page-wrap page-opts left" style=" position:relative;" id="locations_wrap" data-default-country="<?php echo ($foodbakery_post_loc_country) ?>" data-themeurl="<?php echo wp_foodbakery::plugin_url(); ?>" data-plugin_url="<?php echo wp_foodbakery::plugin_url(); ?>" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php'), 'foodbakery'); ?>" data-map_marker="<?php echo esc_html($foodbakery_map_marker_icon); ?>">
	    	    <div class="option-sec" style="margin-bottom:0;">
	    		<div class="opt-conts">
	    		    <div class="row">
	    			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<?php if (isset($locations_data['data']['country'])) : ?>
					    <div class="field-holder">
						<label><?php esc_html_e('Country', 'foodbakery'); ?></label>
						<div class="select-holder">
						    <?php
						    $output = '';
						    if (isset($locations_data['data']['country'])) {
							$foodbakery_opt_array = array(
							    'name' => esc_html__('Country', 'foodbakery'),
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => $foodbakery_post_loc_country,
								'cust_id' => 'loc_country_' . $field_postfix,
								'force_std' => true,
								'cust_name' => 'foodbakery_post_loc_country_' . $field_postfix,
								'classes' => 'form-control chosen-select form-select-country dir-map-search  ',
								'markup' => '<span class="loader-country-' . $field_postfix . '"></span>',
								'extra_atr' => 'data-placeholder="' . esc_html__("Select Country", 'foodbakery') . '"',
								'options_markup' => true,
								'return' => false,
							    ),
							);

							if (isset($location_countries_list) && $location_countries_list != '') {
							    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Country', 'foodbakery') . '</option>' . $location_countries_list;
							} else {
							    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Country', 'foodbakery') . '</option>';
							}

							$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
						    }
						    ?>

						</div>
					    </div>
					<?php endif; ?>
					<?php if (isset($locations_data['data']['state'])) : ?>
					    <div class="field-holder">
						<label><?php esc_html_e('State', 'foodbakery'); ?></label>
						<div class="select-holder">
						    <?php
						    if (isset($locations_data['data']['state'])) {
							$foodbakery_opt_array = array(
							    'name' => esc_html__('State', 'foodbakery'),
							    'id' => 'loc_state_' . $field_postfix . '_container',
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => $foodbakery_post_loc_state,
								'id' => 'loc_state_' . $field_postfix,
								'cust_id' => 'loc_state_' . $field_postfix,
								'cust_name' => 'foodbakery_post_loc_state_' . $field_postfix,
								'classes' => 'form-control chosen-select form-select-state dir-map-search ',
								'markup' => '<span class="loader-state-' . $field_postfix . '"></span>',
								'extra_atr' => 'data-placeholder="' . esc_html__("Select State", 'foodbakery') . '"',
								'options_markup' => true,
								'return' => false,
							    ),
							);
							if (isset($location_states_list) && $location_states_list != '') {
							    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select State', 'foodbakery') . '</option>' . $location_states_list;
							} else {
							    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select State', 'foodbakery') . '</option>';
							}

							$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
						    }
						    ?>
						</div>
					    </div>
					<?php endif; ?>
					<?php if (isset($locations_data['data']['city'])) : ?>
					    <div class="field-holder">
						<label><?php esc_html_e('City', 'foodbakery'); ?></label>
						<div class="select-holder">
						    <?php
						    if (isset($locations_data['data']['city'])) {
							$foodbakery_opt_array = array(
							    'name' => esc_html__('City', 'foodbakery'),
							    'id' => 'loc_city_' . $field_postfix . '_container',
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => $foodbakery_post_loc_city,
								'id' => 'loc_city_' . $field_postfix,
								'cust_id' => 'loc_city_' . $field_postfix,
								'cust_name' => 'foodbakery_post_loc_city_' . $field_postfix,
								'classes' => 'form-control chosen-select form-select-city dir-map-search ',
								'markup' => '<span class="loader-city-' . $field_postfix . '"></span>',
								'extra_atr' => 'data-placeholder="' . esc_html__("Select City", 'foodbakery') . '"',
								'options_markup' => true,
								'return' => false,
							    ),
							);
							if (isset($location_cities_list) && $location_cities_list != '') {
							    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select City', 'foodbakery') . '</option>' . $location_cities_list;
							} else {
							    $foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select City', 'foodbakery') . '</option>';
							}

							$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
						    }
						    ?>
						</div>
					    </div>
					<?php endif; ?>
					<?php if (isset($locations_data['data']['town'])) : ?>
					    <div class="field-holder">
						<label><?php esc_html_e('Town', 'foodbakery'); ?></label>
						<div class="select-holder">
						    <?php
						    $foodbakery_opt_array = array(
							'name' => esc_html__('Town', 'foodbakery'),
							'id' => 'loc_town_' . $field_postfix . '_container',
							'desc' => '',
							'echo' => true,
							'field_params' => array(
							    'std' => $foodbakery_post_loc_town,
							    'force_std' => true,
							    'id' => 'loc_town_' . $field_postfix,
							    'cust_id' => 'loc_town_' . $field_postfix,
							    'cust_name' => 'foodbakery_post_loc_town_' . $field_postfix,
							    'classes' => 'form-control chosen-select form-select-town dir-map-search ',
							    'markup' => '<span class="loader-town-' . $field_postfix . '"></span>',
							    'extra_atr' => 'data-placeholder="' . esc_html__("Select Town", 'foodbakery') . '"',
							    'options_markup' => true,
							    'return' => false,
							),
						    );
						    if (isset($location_towns_list) && $location_towns_list != '') {
							$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Town', 'foodbakery') . '</option>' . $location_towns_list;
						    } else {
							$foodbakery_opt_array['field_params']['options'] = '<option value="">' . esc_html__('Select Town', 'foodbakery') . '</option>';
						    }

						    $foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
						    ?>
						</div>
					    </div>
					<?php endif; ?>

					<?php
					$foodbakery_opt_array = array(
					    'id' => '_loc_bounds_rest',
					    'cust_name' => 'foodbakery_loc_bounds_rest_' . $field_postfix,
					    'std' => $foodbakery_loc_bounds_rest,
					    'classes' => '',
					    'force_std' => true,
					);

					$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

					$foodbakery_opt_array = array(
					    'id' => 'add_new_loc',
					    'cust_name' => 'foodbakery_add_new_loc_' . $field_postfix,
					    'std' => $foodbakery_add_new_loc,
					    'classes' => 'gllpSearchField',
					    'extra_atr' => 'style="margin-bottom:10px;"',
					    'return' => false,
					    'force_std' => true,
					);

					$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

					$foodbakery_opt_array = array(
					    'id' => '',
					    'std' => esc_attr($foodbakery_post_loc_zoom),
					    'cust_id' => 'foodbakery_post_loc_zoom',
					    'cust_name' => "foodbakery_post_loc_zoom_" . $field_postfix,
					    'classes' => 'gllpZoom',
					    'return' => false,
					    'force_std' => true,
					);

					$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
					?>

	    			    <div class="switchs-holder2">
	    				<div class="field-holder">
	    				    <label><?php esc_html_e('Find On Map', 'foodbakery'); ?></label>
						<?php
						$foodbakery_opt_array = array(
						    'name' => '',
						    'desc' => '',
						    'echo' => true,
						    'field_params' => array(
							'std' => $foodbakery_post_loc_address,
							'cust_id' => 'loc_address',
							'classes' => 'foodbakery-search-location',
							'extra_atr' => 'onkeypress="foodbakery_gl_search_map(this.value)" placeholder="' . esc_html__('Type Your Address', 'foodbakery') . '"',
							'cust_name' => 'foodbakery_post_loc_address_' . $field_postfix,
							'return' => false,
							'force_std' => true,
						    ),
						);
						if (isset($value['address_hint']) && $value['address_hint'] != '') {
						    $foodbakery_opt_array['hint_text'] = $value['address_hint'];
						}
						if (isset($value['split']) && $value['split'] <> '') {
						    $foodbakery_opt_array['split'] = $value['split'];
						}
						$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
						?>
	    				</div>

	    				<div class="row">
	    				    <div style="display:none;" class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	    					<div class="field-holder">

							<?php
							$foodbakery_opt_array = array(
							    'name' => esc_html__('Latitude', 'foodbakery'),
							    'id' => 'post_loc_latitude',
							    'desc' => '',
							    'styles' => 'display:none;',
							    'echo' => true,
							    'field_params' => array(
								'std' => $foodbakery_post_loc_latitude,
								'id' => 'post_loc_latitude',
								'cust_name' => 'foodbakery_post_loc_latitude_' . $field_postfix,
								'extra_atr' => 'placeholder="' . esc_html__('Latitude', 'foodbakery') . '"',
								'classes' => 'form-control gllpLatitude',
								'return' => false,
								'force_std' => true,
							    ),
							);

							if (isset($value['split']) && $value['split'] <> '') {
							    $foodbakery_opt_array['split'] = $value['split'];
							}

							$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
	    					</div>
	    				    </div>
	    				    <div style="display:none;" class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	    					<div class="field-holder">

							<?php
							$foodbakery_opt_array = array(
							    'name' => esc_html__('Longitude', 'foodbakery'),
							    'id' => 'post_loc_longitude',
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => $foodbakery_post_loc_longitude,
								'id' => 'post_loc_longitude',
								'cust_name' => 'foodbakery_post_loc_longitude_' . $field_postfix,
								'extra_atr' => 'placeholder="' . esc_html__('Longitude', 'foodbakery') . '"',
								'classes' => 'form-control gllpLongitude',
								'return' => false,
								'force_std' => true,
							    ),
							);

							if (isset($value['split']) && $value['split'] <> '') {
							    $foodbakery_opt_array['split'] = $value['split'];
							}
							$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
	    					</div>
	    				    </div></div>
	    				<div class="search-location-map input-button-loader">
						<?php
						$foodbakery_opt_array = array(
						    'name' => '',
						    'id' => 'map_search_btn',
						    'desc' => '',
						    'echo' => true,
						    'field_params' => array(
							'std' => esc_html__('Search Location', 'foodbakery'),
							'id' => 'map_search_btn',
							'cust_type' => 'button',
							'classes' => 'acc-submit cs-section-update cs-color csborder-color gllpSearchButton',
							'return' => false,
						    ),
						);

						if (isset($value['split']) && $value['split'] <> '') {
						    $foodbakery_opt_array['split'] = $value['split'];
						}

						$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
						?>
	    				</div>
	    			    </div>

	    			</div>
	    			<!-- map start -->
	    			<div class = "col-lg-6 col-md-6 col-sm-6 col-xs-12">



	    			    <div class="cs-map-section" style="float:left; width:100%; height:402px;">
	    				<div class="gllpMap" id="cs-map-location-fe-id"></div>
	    			    </div>
	    			    <p> <?php esc_html_e('For the precise location, you can drag and drop the pin.', 'foodbakery'); ?></p>
	    			</div>
	    		    </div>
	    		</div>
	    	    </div>
	    	</div>
	        </fieldset>
	    </div>
	    <script type="text/javascript">
	        // Call Map gMapsLatLonPicker Class

	        jQuery(document).on("change", "#myonoffswitch2", function () {
	    	$check = jQuery(this).is(':checked');
	    	if ($check) {
	    	    jQuery(".gllpLatlonPicker").each(function () {
	    		var radius = $(this).data('radius');
	    		$obj = jQuery(document).gMapsLatLonPicker(radius);
	    		$obj.init(jQuery(this));
	    	    });
	    	} else {
	    	    jQuery(".gllpLatlonPicker").each(function () {

	    		$obj = jQuery(document).gMapsLatLonPicker();
	    		$obj.init(jQuery(this));
	    	    });
	    	}
	        });
	        jQuery(document).ready(function () {
	    	chosen_selectionbox();

	    	jQuery(".gllpLatlonPicker").each(function () {
	    	    var radius = $(this).data('radius');
	    	    var show_rad = $(this).data('radiusshow');
	    	    if (show_rad == 'on') {
	    		$obj = jQuery(document).gMapsLatLonPicker(radius);
	    	    } else {
	    		$obj = jQuery(document).gMapsLatLonPicker();
	    	    }
	    	    $obj.init(jQuery(this));
	    	});
	        });

	        jQuery(document).ready(function () {
	    	foodbakery_load_location_ajax('<?php echo esc_html($field_postfix); ?>', <?php echo json_encode(array_keys($locations_data['data'])); ?>, <?php echo json_encode($locations_data['location_levels']); ?>, '<?php echo wp_create_nonce('get_locations_list'); ?>');
	        });
	        function foodbakery_gl_search_map() {
				console.log('sssssssssssssss');
	    	var vals;
	    	vals = jQuery('#loc_address').val();
	    	if (jQuery('#loc_town').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_town').val();
	    	}
	    	if (jQuery('#loc_city').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_city').val();
	    	}
	    	if (jQuery('#loc_state').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_state').val();
	    	}
	    	if (jQuery('#loc_country').length > 0) {
	    	    vals = vals + ", " + jQuery('#loc_country').val();
	    	}
	    	jQuery('.gllpSearchField').val(vals);
	        }
	        function foodbakery_fe_search_map() {
	    	var vals;
	    	vals = jQuery('#fe_map<?php echo absint($field_postfix) ?> #loc_address').val();
	    	jQuery('#fe_map<?php echo absint($field_postfix); ?> .gllpSearchField_fe').val(vals);
	        }

	        (function ($) {
	    	$(function () {
	    <?php $foodbakery_obj->foodbakery_google_place_scripts(); ?> //var autocomplete;
	    	    autocomplete = new google.maps.places.Autocomplete(document.getElementById('loc_address'));
	    <?php if (isset($selected_iso_code) && !empty($selected_iso_code)) { ?>
			    autocomplete.setComponentRestrictions({'country': '<?php echo esc_js($selected_iso_code) ?>'});
	    <?php } ?>
	    	});
	        })(jQuery);
	        jQuery(document).ready(function () {
	    	var $ = jQuery;
	    	jQuery("[id^=map_canvas]").css("pointer-events", "none");
	    	jQuery("[id^=cs-map-location]").css("pointer-events", "none");
	    	// on leave handle
	    	var onMapMouseleaveHandler = function (event) {
	    	    var that = jQuery(this);
	    	    that.on('click', onMapClickHandler);
	    	    that.off('mouseleave', onMapMouseleaveHandler);
	    	    jQuery("[id^=map_canvas]").css("pointer-events", "none");
	    	    jQuery("[id^=cs-map-location]").css("pointer-events", "none");
	    	}
	    	// on click handle
	    	var onMapClickHandler = function (event) {
	    	    var that = jQuery(this);
	    	    // Disable the click handler until the user leaves the map area
	    	    that.off('click', onMapClickHandler);
	    	    // Enable scrolling zoom
	    	    that.find('[id^=map_canvas]').css("pointer-events", "auto");
	    	    that.find('[id^=cs-map-location]').css("pointer-events", "auto");
	    	    // Handle the mouse leave event
	    	    that.on('mouseleave', onMapMouseleaveHandler);
	    	}
	    	// Enable map zooming with mouse scroll when the user clicks the map
	    	jQuery('.cs-map-section').on('click', onMapClickHandler);
	    	// new addition
	        });
	    </script>
	    <?php
	}

	/**
	 * Start How to add  Categories(Taxonomy) fields  Function
	 *
	 */
	public function foodbakery_jobs_spec_fields($tag) { //check for existing featured ID
	    global $foodbakery_form_fields;
	    if (isset($tag->term_id)) {
		$t_id = $tag->term_id;
	    } else {
		$t_id = "";
	    }
	    $spec_image = '';
	    ?>

	    <div class="form-field">
	        <label for="tag-image"><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_image'); ?></label>
	        <ul class="form-elements col-lg-8 col-md-8 col-sm-12 col-xs-12" style="float:left; width:95%; margin:0 0 50px 0; padding:0;">
	    	<li class="to-field" style="width:100%;">
	    	    <div class="page-wrap" style="overflow:hidden; background: none !important; float: left !important; clear: both !important; display:<?php echo esc_attr($spec_image) && trim($spec_image) != '' ? 'inline' : 'none'; ?>" id="spec_image<?php echo esc_attr($t_id) ?>_box" >
	    		<div class="gal-active" style="padding-left:0 !important;">
	    		    <div class="dragareamain" style="padding-bottom:0px;">
	    			<ul id="gal-sortable" style="width:200px;">
	    			    <li class="ui-state-default">
	    				<div class="thumb-secs"> <img src="<?php echo esc_url($spec_image); ?>"  id="spec_image<?php echo esc_attr($t_id); ?>_img" width="200" />
	    				    <div class="gal-edit-opts"> <a   href="javascript:del_media('spec_image<?php echo esc_attr($t_id); ?>')" class="delete"></a> </div>
	    				</div>
	    			    </li>
	    			</ul>
	    		    </div>
	    		</div>
	    	    </div>
			<?php
			$foodbakery_opt_array = array(
			    'id' => '',
			    'std' => esc_url($spec_image),
			    'cust_id' => "spec_image" . esc_attr($t_id),
			    'cust_name' => "spec_image",
			    'classes' => '',
			    'return' => false,
			);
			$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
			?>
	    	    <label class="browse-icon" style="float: left !important; clear: both !important;">
			    <?php
			    $foodbakery_opt_array = array(
				'id' => '',
				'std' => foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_browse'),
				'cust_id' => '',
				'cust_name' => "spec_image" . esc_attr($t_id),
				'classes' => 'uploadMedia left',
				'return' => false,
				'extra_atr' => ' style="background:#ff6363 !important;"',
				'cust_type' => 'button',
			    );
			    $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
			    ?>
	    	    </label>
	    	</li>
	        </ul>
	    </div>
	    <?php
	    $foodbakery_opt_array = array(
		'id' => '',
		'std' => "1",
		'cust_id' => "",
		'cust_name' => "spec_image_meta",
		'return' => false,
	    );
	    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
	}

	/*	 *
	 * End How to add Categories fields Function
	 * Start How to Edit Categories Fields Function
	 * */

	public function foodbakery_edit_jobs_spec_fields($tag) { //check for existing featured ID
	    global $foodbakery_form_fields;
	    if (isset($tag->term_id)) {
		$t_id = $tag->term_id;
	    } else {
		$t_id = "";
	    }
	    $foodbakery_counter = $tag->term_id;
	    $cat_meta = get_term_meta($t_id, 'spec_meta_data', true);
	    $spec_image = isset($cat_meta['img']) ? $cat_meta['img'] : '';
	    ?>
	    <tr>
	        <th><label for="cat_f_img_url"> <?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_choose_icon'); ?></label></th>
	        <td>
	    	<ul class="form-elements col-lg-8 col-md-8 col-sm-12 col-xs-12" style="margin:0; padding:0;">
	    	    <li class="to-field" style="width:100%;">
	    		<div class="page-wrap" style="overflow:hidden; background: none !important; float: left !important; clear: both !important; display:<?php echo esc_attr($spec_image) && trim($spec_image) != '' ? 'inline' : 'none'; ?>" id="spec_image<?php echo esc_attr($foodbakery_counter) ?>_box" >
	    		    <div class="gal-active" style="padding-left:0 !important;">
	    			<div class="dragareamain" style="padding-bottom:0px;">
	    			    <ul id="gal-sortable" style="width:200px;">
	    				<li class="ui-state-default">
	    				    <div class="thumb-secs"> <img src="<?php echo esc_url($spec_image); ?>"  id="spec_image<?php echo esc_attr($foodbakery_counter); ?>_img" width="200" />
	    					<div class="gal-edit-opts"> <a href="javascript:del_media('spec_image<?php echo esc_attr($foodbakery_counter); ?>')" class="delete"></a> </div>
	    				    </div>
	    				</li>
	    			    </ul>
	    			</div>
	    		    </div>
	    		</div>
			    <?php
			    $foodbakery_opt_array = array(
				'id' => '',
				'std' => esc_url($spec_image),
				'cust_id' => "spec_image" . esc_attr($foodbakery_counter),
				'cust_name' => "spec_image",
			    );
			    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
			    ?>
	    		<label class="browse-icon" style="float: left !important; clear: both !important;">
				<?php
				$foodbakery_opt_array = array(
				    'id' => '',
				    'std' => foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_browse'),
				    'cust_id' => '',
				    'cust_name' => "spec_image" . esc_attr($foodbakery_counter),
				    'classes' => 'uploadMedia left',
				    'cust_type' => 'button',
				);
				$foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
				?>
	    		</label>
	    	    </li>
	    	</ul>
	        </td>
	    </tr>
	    <?php
	    $foodbakery_opt_array = array(
		'id' => '',
		'std' => "1",
		'cust_id' => "",
		'cust_name' => "spec_image_meta",
		'return' => false,
	    );
	    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
	}

	/**
	 * Start Function save extra category extra fields callback function
	 *
	 */
	public function foodbakery_save_jobs_spec_fields($term_id) {
	    if (isset($_POST['spec_image_meta']) and $_POST['spec_image_meta'] == '1') {
		$t_id = $term_id;
		$spec_image_img = '';
		if (isset($_POST['spec_image'])) {
		    $spec_image_img = $_POST['spec_image'];
		}
		$cat_meta = array(
		    'img' => $spec_image_img,
		);
		//save the option array
		update_term_meta($t_id, 'spec_meta_data', $cat_meta);
	    }
	}

	// Add Category Fields
	public function foodbakery_jobs_locations_fields($tag) { //check for existing featured ID
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

	        <label><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_ISO_code'); ?></label>
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
	    <?php
	    $foodbakery_opt_array = array(
		'id' => '',
		'std' => "1",
		'cust_id' => "",
		'cust_name' => "locations_image_meta",
		'return' => false,
	    );
	    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
	}

	public function foodbakery_edit_jobs_locations_fields($tag) { //check for existing featured ID
	    global $foodbakery_form_fields;
	    if (isset($tag->term_id)) {
		$t_id = $tag->term_id;
	    } else {
		$t_id = "";
	    }
	    $cat_meta = get_option("iso_code_$t_id");
	    $iso_code = $cat_meta['text'];

	    $foodbakery_opt_array = array(
		'id' => '',
		'std' => "1",
		'cust_id' => "",
		'cust_name' => "locations_image_meta",
		'return' => false,
	    );
	    $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
	    ?>
	    <tr>
	        <th><label for="cat_f_img_url"> <?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_save_post_ISO_code'); ?></label></th>
	        <td>
	    	<ul class="form-elements" style="margin:0; padding:0;">
	    	    <li class="to-field" style="width:100%;">
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
	    	    </li>
	    	</ul>
	        </td>
	    </tr>
	    <?php
	}



	public function foodbakery_jobs_job_type_fields($tag) {
	    global $foodbakery_form_fields;
	    if (isset($tag->term_id)) {
		$t_id = $tag->term_id;
	    } else {
		$t_id = "";
	    }
	    $locations_image = '';
	    $job_type_color = '';
	    wp_enqueue_style('wp-color-picker');
	    wp_enqueue_script('wp-color-picker');
	    ?>
	    <script type="text/javascript">
	        jQuery(document).ready(function ($) {
	    	$('.bg_color').wpColorPicker();
	        });
	    </script>
	    <div class="form-field">

	        <label><?php esc_html_e("Job Type Color", "foodbakery"); ?></label>
	        <ul class="form-elements" style="margin:0; padding:0;">
	    	<li class="to-field" style="width:100%;">
			<?php
			$foodbakery_opt_array = array(
			    'id' => '',
			    'std' => "",
			    'cust_id' => "job_type_color",
			    'cust_name' => "job_type_color",
			    'classes' => 'bg_color',
			    'return' => false,
			);
			$foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
			?>
	    	</li>
	        </ul>
	        </br> </br>

	        <br />
	    </div>
	    <?php
	}

	public function foodbakery_edit_jobs_job_type_fields($tag) { //check for existing featured ID
	    global $foodbakery_form_fields;
	    wp_enqueue_style('wp-color-picker');
	    wp_enqueue_script('wp-color-picker');
	    if (isset($tag->term_id)) {
		$t_id = $tag->term_id;
	    } else {
		$t_id = "";
	    }
	    ?>
	    <script type="text/javascript">
	        jQuery(document).ready(function ($) {
	    	$('.bg_color').wpColorPicker();
	        });
	    </script>
	    <?php
	    $cat_meta = get_option("job_type_color_$t_id");
	    $job_type_color = $cat_meta['text'];
	    ?>

	    <tr>
	        <th><label for="cat_f_img_url"> <?php esc_html_e("Job Type Color", "foodbakery"); ?></label></th>
	        <td>
	    	<ul class="form-elements" style="margin:0; padding:0;">
	    	    <li class="to-field" style="width:100%;">
			    <?php
			    $foodbakery_opt_array = array(
				'id' => '',
				'std' => esc_attr($job_type_color),
				'cust_id' => "job_type_color",
				'cust_name' => "job_type_color",
				'classes' => 'bg_color',
				'return' => false,
			    );
			    $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
			    ?>
	    	    </li>
	    	</ul>
	        </td>
	    </tr>
	    <?php
	}

	/**
	 * Start Function how to save location in jobs fields
	 */
	public function foodbakery_save_jobs_jobtype_fields($term_id) {
	    if (isset($_POST['job_type_color'])) {
		$t_id = $term_id;

		if (isset($_POST['job_type_color'])) {
		    $job_type_color = $_POST['job_type_color'];
		}

		$cat_meta = array(
		    'text' => $job_type_color,
		);

		update_option("job_type_color_$t_id", $cat_meta);
	    }
	}

	/**
	 * End Function how to save location in jobs fields
	 * How to know about working  current Theme Function Start
	 */
	public function foodbakery_get_current_theme() {
	    $foodbakery_theme = wp_get_theme();
	    $theme_name = $foodbakery_theme->get('Name');
	    return $theme_name;
	}

    }

    /**
     * End Function How to know about working  current Theme Function
     * Design Pattern for Object initilization
     */
    function FOODBAKERY_FUNCTIONS() {
	return Foodbakery_Plugin_Functions::instance();
    }

    $GLOBALS['Foodbakery_Plugin_Functions'] = FOODBAKERY_FUNCTIONS();
}