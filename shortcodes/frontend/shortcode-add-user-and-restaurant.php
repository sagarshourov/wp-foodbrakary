<?php
if (!class_exists('Foodbakery_Register_And_Add_Restaurant')) :

    class Foodbakery_Register_And_Add_Restaurant {

	public function __construct() {
	    add_shortcode('foodbakery_register_and_add_restaurant', array($this, 'foodbakery_register_and_add_restaurant_shortcode'));

	    add_action('wp_ajax_user_and_restaurant_meta_save', array($this, 'user_and_restaurant_meta_save_callback'));
	    add_action('wp_ajax_nopriv_user_and_restaurant_meta_save', array($this, 'user_and_restaurant_meta_save_callback'));

	    add_action('wp_ajax_foodbakery_payment_gateways_package_selected', array($this, 'foodbakery_payment_gateways_package_selected_callback'));
	    add_action('wp_ajax_nopriv_foodbakery_payment_gateways_package_selected', array($this, 'foodbakery_payment_gateways_package_selected_callback'));
	}

	/*
	 * Foodbakery Register and Add Restaurant
	 * Shortcode
	 * @retrun markup
	 */

	public function foodbakery_register_and_add_restaurant_shortcode($atts, $content = "") {
	    $defaults = array('restaurant_title' => '');

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
	    if (function_exists('foodbakery_var_page_builder_element_sizes')) {
		echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
	    }
	    echo '<div class="user-dashboard loader-holder">';
	    $restaurant_add_settings = array(
		'return_html' => false,
	    );

	    if (!is_user_logged_in()) {
		$this->register_and_add_restaurant_ui($restaurant_add_settings);
	    } else {
		?>
		<div class="restricted-message">
		    <div class="media-holder">
			<figure>
			    <img src="<?php echo wp_foodbakery::plugin_url() . 'assets/frontend/images/access-restricted-icon-img.png'; ?>" alt="<?php esc_html_e('Access Restricted', 'foodbakery'); ?>">
			</figure>
		    </div>
		    <div class="text-holder">
			<strong><?php esc_html_e('Access Restricted', 'foodbakery'); ?></strong>
			<span><?php esc_html_e('You are not authorized. Please login as restaurant owner to access this page.', 'foodbakery'); ?></span>
		    </div>
		</div>
		<?php
	    }
	    echo '</div>';
	    if (function_exists('foodbakery_var_page_builder_element_sizes')) {
		echo '</div>';
	    }

	    $html .= ob_get_clean();
	    return $html;
	}

	public function register_and_add_restaurant_ui($params = array()) {
	    global $restaurant_add_counter, $foodbakery_plugin_options;
	    extract($params);
	    ob_start();
	    $restaurant_add_counter = rand(10000000, 99999999);

	    $foodbakery_id = foodbakery_get_input('restaurant_id', 0);

	    wp_enqueue_script('foodbakery-restaurant-add');
	    ?>
	    <div id="foodbakery-dev-posting-main-<?php echo absint($restaurant_add_counter); ?>" class="user-holder" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-plugin-url="<?php echo esc_url(wp_foodbakery::plugin_url()); ?>">

		<?php
		$can_post_restaurant = true;
		if (is_user_logged_in()) {
		    $current_user = wp_get_current_user();
		    $publisher_id = foodbakery_company_id_form_user_id($current_user->ID);
		    $args = array(
			'posts_per_page' => "1",
			'post_type' => 'restaurants',
			'post_status' => 'publish',
			'fields' => 'ids',
			'meta_query' => array(
			    'relation' => 'AND',
			    array(
				'key' => 'foodbakery_restaurant_publisher',
				'value' => $publisher_id,
				'compare' => '=',
			    ),
			    array(
				'key' => 'foodbakery_restaurant_username',
				'value' => $current_user->ID,
				'compare' => '=',
			    ),
			),
		    );
		    $custom_query = new WP_Query($args);
		    $pub_restaurant = $custom_query->posts;

		    if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '' && $foodbakery_id != $pub_restaurant[0]) {
			$can_post_restaurant = false;
		    }
		}

		$foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : 'off';

		if ($can_post_restaurant) {
		    $activation_process = ( isset($_GET['tab']) && isset($_GET['tab']) == 'activation' ) ? ' processing' : '';
		    $active_class = ( isset($_GET['tab']) && isset($_GET['tab']) == 'activation' ) ? ' class="active' . $activation_process . '"' : '';
		    $active_processing_class = ( isset($_GET['tab']) && isset($_GET['tab']) == 'activation' ) ? ' class="active"' : '';
		    $processing = ( isset($_GET['tab']) && isset($_GET['tab']) != 'activation' ) ? ' processing' : '';
		    ?>

		    <ul class="restaurant-settings-nav progressbar-nav" data-restaurant="<?php echo absint($foodbakery_id) ?>" data-mcounter="<?php echo absint($restaurant_add_counter) ?>">
			<li class="active <?php echo $processing; ?>" data-act="restaurant-information"><a href="javascript:void(0);" class="cond-restaurant-settings1" data-act="restaurant-information"><?php esc_html_e('Information', 'foodbakery'); ?></a></li>
			<?php if ($foodbakery_free_restaurants_switch != 'on') : ?>
		    	<li<?php echo $active_processing_class; ?> data-act="package"><a href="javascript:void(0);" class="cond-restaurant-settings1" data-act="package"><?php esc_html_e('Select Package', 'foodbakery'); ?></a></li>
		    	<li<?php echo $active_processing_class; ?> data-act="payment-information"><a href="javascript:void(0);" class="cond-restaurant-settings1" data-act="payment-information"><?php esc_html_e('Payment Information', 'foodbakery'); ?></a></li>
			<?php endif; ?>
			<li<?php echo $active_class; ?> data-act="activation"><a href="javascript:void(0);" class="cond-restaurant-settings1" data-act="activation"><?php esc_html_e('Activation', 'foodbakery'); ?></a></li>
		    </ul>

		    <?php
		    $restaurant_tab = isset($_GET['restaurant_tab']) ? $_GET['restaurant_tab'] : '';
		    ?>
		    <div id="restaurant-sets-holder">
			<form id="foodbakery-dev-restaurant-form-<?php echo absint($restaurant_add_counter); ?>" name="foodbakery-dev-restaurant-form" class="form-fields-set foodbakery-dev-restaurant-form" data-id="<?php echo absint($restaurant_add_counter); ?>" method="post" enctype="multipart/form-data">
			    <?php
			    $this->restaurant_show_set_settings(1);
			    ?>
			    <?php if ($foodbakery_free_restaurants_switch != 'on') : ?>
				<?php $this->restaurant_show_set_membership(1); ?> 
			    <?php endif; ?>

			    <input type="hidden" id="action" name="action" value="user_and_restaurant_meta_save">
			</form>
			<?php
			if ($foodbakery_free_restaurants_switch != 'on') {
			    $this->restaurant_show_payment_information();
			}
			?>
			<?php
			$this->restaurant_show_activation_tab();
			?>
		    </div>
		    <script type="text/javascript">
		<?php
		$foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : 'off';
		?>
		        jQuery(document).ready(function ($) {
		            add_event_listners({
		                'package_required_error': '<?php esc_html_e('Please select a package.', 'foodbakery'); ?>',
		                'processing_request': '<?php esc_html_e('Processing...', 'foodbakery'); ?>',
		                'is_restaurant_posting_free': '<?php echo ($foodbakery_free_restaurants_switch); ?>',
		            }, $);
		        });
		    </script>
		    <?php
		} else {
		    esc_html_e('You already have a restaurant.', 'foodbakery');
		}
		?>
	    </div>
	    <?php
	    $html = ob_get_clean();
	    if (isset($return_html) && $return_html == true) {
		return $html;
	    } else {
		echo force_balance_tags($html);
	    }
	}

	public function foodbakery_payment_gateways_package_selected_callback() {
	    $response = array(
		'status' => false,
		'msg' => esc_html__('An error occured while processing payment form.', 'foodbakery'),
	    );
	    $buy_order_action = foodbakery_get_input('foodbakery_buy_order_flag', 0);
	    $get_trans_id = foodbakery_get_input('trans_id', 0);
	    $transaction_return_url = foodbakery_get_input('transaction_return_url', site_url(), 'HTML');

	    $order_type = get_post_meta($get_trans_id, 'foodbakery_order_type', true);
	    $order_menu_list = get_post_meta($get_trans_id, 'menu_items_list', true);

	    if ($buy_order_action == '1') {
		if (foodbakery_is_package_order($get_trans_id)) {

		    $trans_user_id = get_post_meta($get_trans_id, 'foodbakery_transaction_user', true);
		    $foodbakery_trans_pkg = get_post_meta($get_trans_id, 'foodbakery_transaction_package', true);
		    $foodbakery_trans_amount = get_post_meta($get_trans_id, 'foodbakery_transaction_amount', true);

		    $foodbakery_trans_pay_method = foodbakery_get_input('foodbakery_restaurant_gateway', '', 'STRING');

		    $foodbakery_trans_array = array(
			'transaction_id' => $get_trans_id, // order id
			'transaction_user' => $trans_user_id,
			'transaction_package' => $foodbakery_trans_pkg,
			'transaction_amount' => $foodbakery_trans_amount,
			'transaction_order_type' => 'package-order',
			'transaction_pay_method' => $foodbakery_trans_pay_method,
			'transaction_return_url' => $transaction_return_url,
			'exit' => false,
		    );

		    ob_start();
		    $output = foodbakery_payment_process($foodbakery_trans_array);
		    if (!empty($output)) {
			echo force_balance_tags($output);
		    }
		    $transaction_detail = ob_get_clean();

		    if ($transaction_detail) {
			$response = array(
			    'payment_gateway' => $foodbakery_trans_pay_method,
			    'status' => true,
			    'msg' => force_balance_tags($transaction_detail),
			);
		    }
		}
	    }
	    echo json_encode($response);
	    wp_die();
	}

	/**
	 * Get Restaurant Transaction id
	 * @return id
	 */
	public function restaurant_trans_id($restaurant_id = '') {

	    $get_subscripton_data = get_post_meta($restaurant_id, "package_subscripton_data", true);
	    if (is_array($get_subscripton_data)) {
		$last_subs = end($get_subscripton_data);
		$trans_id = isset($last_subs['transaction_id']) ? $last_subs['transaction_id'] : false;
		return $trans_id;
	    }
	}

	/**
	 * Save foodbakery restaurant
	 * @return
	 */
	public function user_and_restaurant_meta_save_callback() {

	    global $current_user, $restaurant_add_counter;
	    $response = array('status' => false, 'msg' => esc_html__('An error occurred. Try again later.', 'foodbakery'));
	    $publisher_id = '';
	    $restaurant_id = 0;
	    $get_username = foodbakery_get_input('foodbakery_restaurant_username', '', 'STRING');
	    $get_useremail = foodbakery_get_input('foodbakery_restaurant_user_email', '', 'STRING');
	    $reg_array = array(
		'username' => $get_username,
		'display_name' => $get_username,
		'email' => $get_useremail,
		'profile_type' => 'restaurant',
		'id' => $restaurant_add_counter,
		'foodbakery_user_role_type' => 'publisher',
		'key' => '',
	    );
	    if ($this->is_form_submit()) {
		$publisher_data = foodbakery_registration_validation('', $reg_array);
		$publisher_id = isset($publisher_data[0]) ? $publisher_data[0] : '';
		$publish_user_id = isset($publisher_data[1]) ? $publisher_data[1] : '';

		if ($publisher_id != '') {
		    $restaurant_id = $this->restaurant_insert($publisher_id);
		} else {
		    $response = $publisher_data;
		}
	    }

	    if ($restaurant_id != 0 && $this->is_form_submit()) {
		// saving Restaurant posted date
		update_post_meta($restaurant_id, 'foodbakery_restaurant_posted', strtotime(current_time('d-m-Y H:i:s')));

		// Saving Restaurant Publisher
		update_post_meta($restaurant_id, 'foodbakery_restaurant_publisher', $publisher_id);
		update_post_meta($restaurant_id, 'foodbakery_restaurant_username', $publish_user_id);

		// updating company id
		$company_id = get_user_meta($publisher_id, 'foodbakery_company', true);
		update_post_meta($restaurant_id, 'foodbakery_restaurant_company', $company_id);


		// adding restaurant categories
		if (isset($_POST['foodbakery_restaurant_category'])) {
		    $restaurant_cats_formate = 'multiple';
		    if ($restaurant_cats_formate == 'multiple') {
			$foodbakery_restaurant_cats = foodbakery_get_input('foodbakery_restaurant_category', '', 'ARRAY');
			$cat_ids = array();
			$cat_slugs = array();

			if ($foodbakery_restaurant_cats) {
			    foreach ($foodbakery_restaurant_cats as $foodbakery_restaurant_cat) {
				$term = get_term_by('slug', $foodbakery_restaurant_cat, 'restaurant-category');
				if (isset($term->term_id)) {
				    $cat_ids[] = $term->term_id;
				    $cat_slugs[] = $term->slug;
				}
			    }
			}
			wp_set_post_terms($restaurant_id, $cat_ids, 'restaurant-category', FALSE);
			update_post_meta($restaurant_id, 'foodbakery_restaurant_category', $cat_slugs);
		    } else {
			$foodbakery_restaurant_cats = foodbakery_get_input('foodbakery_restaurant_category', '', 'STRING');

			if ($foodbakery_restaurant_cats) {
			    $term = get_term_by('slug', $foodbakery_restaurant_cats, 'restaurant-category');

			    if (isset($term->term_id)) {
				$cat_ids = array();
				$cat_ids[] = $term->term_id;
				$cat_slugs = $term->slug;
				wp_set_post_terms($restaurant_id, $cat_ids, 'restaurant-category', FALSE);
				update_post_meta($restaurant_id, 'foodbakery_restaurant_category', $cat_slugs);
			    }
			}
		    }
		}

		if (isset($_POST['foodbakery_post_loc_address_restaurant'])) {
		    // saving location fields
		    $foodbakery_restaurant_country = foodbakery_get_input('foodbakery_post_loc_country_restaurant', '', 'STRING');
		    $foodbakery_restaurant_state = foodbakery_get_input('foodbakery_post_loc_state_restaurant', '', 'STRING');
		    $foodbakery_restaurant_city = foodbakery_get_input('foodbakery_post_loc_city_restaurant', '', 'STRING');
		    $foodbakery_restaurant_town = foodbakery_get_input('foodbakery_post_loc_town_restaurant', '', 'STRING');

		    $foodbakery_restaurant_loc_addr = foodbakery_get_input('foodbakery_post_loc_address_restaurant', '', 'STRING');
		    $foodbakery_restaurant_loc_lat = foodbakery_get_input('foodbakery_post_loc_latitude_restaurant', '', 'STRING');
		    $foodbakery_restaurant_loc_long = foodbakery_get_input('foodbakery_post_loc_longitude_restaurant', '', 'STRING');
		    $foodbakery_restaurant_loc_zoom = foodbakery_get_input('foodbakery_post_loc_zoom_restaurant', '', 'STRING');
		    $foodbakery_restaurant_loc_radius = foodbakery_get_input('foodbakery_loc_radius_restaurant', '', 'STRING');
		    $foodbakery_add_new_loc = foodbakery_get_input('foodbakery_add_new_loc_restaurant', '', 'STRING');
		    $foodbakery_loc_bounds_rest = foodbakery_get_input('foodbakery_loc_bounds_rest_restaurant', '', 'STRING');

		    update_post_meta($restaurant_id, 'foodbakery_post_loc_country_restaurant', $foodbakery_restaurant_country);
		    update_post_meta($restaurant_id, 'foodbakery_post_loc_state_restaurant', $foodbakery_restaurant_state);
		    update_post_meta($restaurant_id, 'foodbakery_post_loc_city_restaurant', $foodbakery_restaurant_city);
		    update_post_meta($restaurant_id, 'foodbakery_post_loc_town_restaurant', $foodbakery_restaurant_town);
		    update_post_meta($restaurant_id, 'foodbakery_post_comp_address_restaurant', $foodbakery_restaurant_loc_addr);
		    update_post_meta($restaurant_id, 'foodbakery_post_loc_address_restaurant', $foodbakery_restaurant_loc_addr);
		    update_post_meta($restaurant_id, 'foodbakery_post_loc_latitude_restaurant', $foodbakery_restaurant_loc_lat);
		    update_post_meta($restaurant_id, 'foodbakery_post_loc_longitude_restaurant', $foodbakery_restaurant_loc_long);
		    update_post_meta($restaurant_id, 'foodbakery_post_loc_zoom_restaurant', $foodbakery_restaurant_loc_zoom);
		    update_post_meta($restaurant_id, 'foodbakery_loc_radius_restaurant', $foodbakery_restaurant_loc_radius);
		    update_post_meta($restaurant_id, 'foodbakery_add_new_loc_restaurant', $foodbakery_add_new_loc);
		    update_post_meta($restaurant_id, 'foodbakery_loc_bounds_rest_restaurant', $foodbakery_loc_bounds_rest);

		    $foodbakery_array_data = array();
            /*add county and county tax*/
            //$foodbakery_array_data = apply_filters('foodbakery_add_county_countytax', $restaurant_id, $foodbakery_array_data);
		    $foodbakery_array_data['foodbakery_post_loc_country_restaurant'] = $foodbakery_restaurant_country;
		    $foodbakery_array_data['foodbakery_post_loc_state_restaurant'] = $foodbakery_restaurant_state;
		    $foodbakery_array_data['foodbakery_post_loc_city_restaurant'] = $foodbakery_restaurant_city;
		    $foodbakery_array_data['foodbakery_post_loc_town_restaurant'] = $foodbakery_restaurant_town;
		    $foodbakery_array_data['foodbakery_post_comp_address_restaurant'] = $foodbakery_restaurant_loc_addr;
		    $foodbakery_array_data['foodbakery_post_loc_address_restaurant'] = $foodbakery_restaurant_loc_addr;
		    $foodbakery_array_data['foodbakery_post_loc_latitude_restaurant'] = $foodbakery_restaurant_loc_lat;
		    $foodbakery_array_data['foodbakery_post_loc_longitude_restaurant'] = $foodbakery_restaurant_loc_long;
		    $foodbakery_array_data['foodbakery_post_loc_zoom_restaurant'] = $foodbakery_restaurant_loc_zoom;
		    $foodbakery_array_data['foodbakery_loc_radius_restaurant'] = $foodbakery_restaurant_loc_radius;
		    $foodbakery_array_data['foodbakery_add_new_loc_restaurant'] = $foodbakery_add_new_loc;

		    update_post_meta($restaurant_id, 'foodbakery_array_data', $foodbakery_array_data);
		}


		$response1 = $this->restaurant_save_assignments($restaurant_id, $publisher_id);

		if ($response1['status'] == true) {
		    $response['status'] = true;
		    $response['msg'] = 'User account and restaurant successfully registered.';
		}
	    }
	    echo json_encode($response);
	    wp_die();
	}

	/**
	 * Check Free or Paid restaurant
	 * Assign Membership in case of paid
	 * Assign Status of restaurant
	 * @return
	 */
	public function restaurant_save_assignments($restaurant_id = '', $publisher_id = '') {
	    global $foodbakery_plugin_options;
	    $response = array('status' => false, 'msg' => esc_html__('An error occurred. Try again later.', 'foodbakery'));
	    $get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    $is_updating = false;
	    if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
		$is_updating = true;
	    }
	    $foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : '';
	    $foodbakery_restaurant_default_expiry = isset($foodbakery_plugin_options['foodbakery_restaurant_default_expiry']) ? $foodbakery_plugin_options['foodbakery_restaurant_default_expiry'] : '';
	    if ($foodbakery_free_restaurants_switch == 'on') {
		// Free Posting without any Membership
		if (!$is_updating) {
		    if ($foodbakery_restaurant_default_expiry == '') {

			$foodbakery_trans_restaurant_expiry = current_time('Y-m-d H:i:s');
		    } else {

			$foodbakery_trans_restaurant_expiry = $this->date_conv($foodbakery_restaurant_default_expiry, 'days');
		    }

		    // Assign expire date
		    update_post_meta($restaurant_id, 'foodbakery_restaurant_expired', strtotime($foodbakery_trans_restaurant_expiry));

		    // Add restaurant type in free posting
		    update_post_meta($restaurant_id, 'foodbakery_restaurant_type', 'restaurant-settings');

		    // Assign without package true
		    update_post_meta($restaurant_id, 'foodbakery_restaurant_without_package', '1');

		    // Assign Status of restaurant
		    $this->restaurant_update_status($restaurant_id);
		    //do_action( 'foodbakery_restaurant_add_assign_status', $restaurant_id );
		}
		$response['status'] = true;
		$response['msg'] = esc_html__('Restaurant added successfully and packages processed', 'foodbakery');
	    } else {
		$new_pkg_check = foodbakery_get_input('foodbakery_restaurant_new_package_used', '');
		if ($new_pkg_check == 'on') {

		    $package_id = foodbakery_get_input('foodbakery_restaurant_package', 0);
		    if ($this->is_package($package_id)) {
			if ($is_updating) {
			    // package subscribe
			    // add transaction
			    $transaction_detail = $this->foodbakery_restaurant_add_transaction('update-restaurant', $restaurant_id, $package_id, $publisher_id);
			    echo force_balance_tags($transaction_detail);
			} else {
			    // package subscribe
			    // add transaction
			    $transaction_detail = $this->foodbakery_restaurant_add_transaction('add-restaurant', $restaurant_id, $package_id, $publisher_id);
			    echo force_balance_tags($transaction_detail);
			}
		    }

		    // end of using new package
		} else {

		    $active_package_key = foodbakery_get_input('foodbakery_restaurant_active_package', 0);
		    $active_package_key = explode('pt_', $active_package_key);
		    $active_pckg_id = isset($active_package_key[0]) ? $active_package_key[0] : '';
		    $active_pckg_trans_id = isset($active_package_key[1]) ? $active_package_key[1] : '';
		    if ($this->is_package($active_pckg_id)) {
			$t_package_feature_list = get_post_meta($active_pckg_trans_id, 'foodbakery_transaction_restaurant_feature_list', true);
			$t_package_top_cat_list = get_post_meta($active_pckg_trans_id, 'foodbakery_transaction_restaurant_top_cat_list', true);

			if ($is_updating) {
			    $foodbakery_package_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_package', true);
			    $foodbakery_trans_id = $this->restaurant_trans_id($restaurant_id);
			    // update-restaurant
			    $is_pkg_subs = $this->foodbakery_is_pkg_subscribed($active_pckg_id, $active_pckg_trans_id);
			    if ($foodbakery_package_id != $active_pckg_id || $active_pckg_trans_id != $foodbakery_trans_id) {
				// if package subscribe
				if ($is_pkg_subs) {

				    // update featured, top category
				    // this change will be temporary
				    update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
				    update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');

				    // Get Transaction Restaurants array
				    // Merge new Restaurant in Array
				    $get_trans_restaurants = get_post_meta($active_pckg_trans_id, "foodbakery_restaurant_ids", true);
				    $updated_trans_restaurants = $this->merge_in_array($get_trans_restaurants, $restaurant_id);
				    update_post_meta($active_pckg_trans_id, "foodbakery_restaurant_ids", $updated_trans_restaurants);

				    $active_pckg_trans_title = $active_pckg_trans_id != '' ? str_replace('#', '', get_the_title($active_pckg_trans_id)) : '';

				    // updating package id in restaurant
				    update_post_meta($restaurant_id, "foodbakery_restaurant_package", $active_pckg_id);

				    // updating transaction title id in restaurant
				    update_post_meta($restaurant_id, "foodbakery_trans_id", $active_pckg_trans_title);

				    // update restaurant subscription renew
				    $get_subscripton_data = get_post_meta($restaurant_id, "package_subscripton_data", true);
				    if (empty($get_subscripton_data)) {
					$package_subscripton_data = array(
					    array(
						'type' => 'update_package',
						'transaction_id' => $active_pckg_trans_id,
						'title_id' => $active_pckg_trans_title,
						'package_id' => $active_pckg_id,
						'subscribe_date' => strtotime(current_time('Y-m-d H:i:s')),
					    )
					);
				    } else {
					$package_subscripton_data = array(
					    'type' => 'update_package',
					    'transaction_id' => $active_pckg_trans_id,
					    'title_id' => $active_pckg_trans_title,
					    'package_id' => $active_pckg_id,
					    'renew_date' => strtotime(current_time('Y-m-d H:i:s')),
					);
				    }
				    $merged_subscripton_data = $this->merge_in_array($get_subscripton_data, $package_subscripton_data, false);
				    update_post_meta($restaurant_id, "package_subscripton_data", $merged_subscripton_data);

				    // updating restaurant meta
				    // as per transaction meta
				    $this->restaurant_assign_meta($restaurant_id, $active_pckg_trans_id);

				    // Assign Status of restaurant
				    $this->restaurant_update_status($restaurant_id);
				}
			    }
			    if ($is_pkg_subs) {
				// update restaurant featured
				if ($t_package_feature_list == 'on') {
				    // featured from form
				    $get_restaurant_featured = foodbakery_get_input('foodbakery_restaurant_featured', '');
				    // featured from meta
				    $db_restaurant_featured = get_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", true);

				    if ($get_restaurant_featured == 'on' && $db_restaurant_featured != 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", 'on');
				    } else if ($get_restaurant_featured != 'on' && $db_restaurant_featured == 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
				    }
				} else {
				    update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
				}

				// update restaurant top category
				if ($t_package_top_cat_list == 'on') {
				    // Top Cat from form
				    $get_restaurant_top_cat = foodbakery_get_input('foodbakery_restaurant_top_cat', '');
				    // Top Cat from meta
				    $db_restaurant_top_cat = get_post_meta($restaurant_id, 'foodbakery_restaurant_is_top_cat', true);

				    if ($get_restaurant_top_cat == 'on' && $db_restaurant_top_cat != 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", 'on');
				    } else if ($get_restaurant_top_cat != 'on' && $db_restaurant_top_cat == 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');
				    }
				} else {
				    update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');
				}
			    }
			} else {
			    // if package subscribe
			    if ($this->foodbakery_is_pkg_subscribed($active_pckg_id, $active_pckg_trans_id)) {

				// Get Transaction Restaurants array
				// Merge new Restaurant in Array
				$get_trans_restaurants = get_post_meta($active_pckg_trans_id, "foodbakery_restaurant_ids", true);
				$updated_trans_restaurants = $this->merge_in_array($get_trans_restaurants, $restaurant_id);
				update_post_meta($active_pckg_trans_id, "foodbakery_restaurant_ids", $updated_trans_restaurants);

				$active_pckg_trans_title = $active_pckg_trans_id != '' ? str_replace('#', '', get_the_title($active_pckg_trans_id)) : '';
				// updating package id in restaurant
				update_post_meta($restaurant_id, "foodbakery_restaurant_package", $active_pckg_id);

				// updating transaction title id in restaurant
				update_post_meta($restaurant_id, "foodbakery_trans_id", $active_pckg_trans_title);

				// update restaurant subscription renew
				$get_subscripton_data = get_post_meta($restaurant_id, "package_subscripton_data", true);

				if (empty($get_subscripton_data)) {
				    $package_subscripton_data = array(
					array(
					    'type' => 'update_package',
					    'transaction_id' => $active_pckg_trans_id,
					    'title_id' => $active_pckg_trans_title,
					    'package_id' => $active_pckg_id,
					    'subscribe_date' => strtotime(current_time('Y-m-d H:i:s')),
					)
				    );
				} else {
				    $package_subscripton_data = array(
					'type' => 'update_package',
					'transaction_id' => $active_pckg_trans_id,
					'title_id' => $active_pckg_trans_title,
					'package_id' => $active_pckg_id,
					'renew_date' => strtotime(current_time('Y-m-d H:i:s')),
				    );
				}
				$merged_subscripton_data = $this->merge_in_array($get_subscripton_data, $package_subscripton_data, false);
				update_post_meta($restaurant_id, "package_subscripton_data", $merged_subscripton_data);

				// update restaurant featured
				if ($t_package_feature_list == 'on') {
				    // featured from form
				    $get_restaurant_featured = foodbakery_get_input('foodbakery_restaurant_featured', '');
				    // featured from meta
				    $db_restaurant_featured = get_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", true);

				    if ($get_restaurant_featured == 'on' && $db_restaurant_featured != 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", 'on');
				    } else if ($get_restaurant_featured != 'on' && $db_restaurant_featured == 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
				    }
				} else {
				    update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
				}

				// update restaurant top category
				if ($t_package_top_cat_list == 'on') {
				    // Top Cat from form
				    $get_restaurant_top_cat = foodbakery_get_input('foodbakery_restaurant_top_cat', '');
				    // Top Cat from meta
				    $db_restaurant_top_cat = get_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", true);

				    if ($get_restaurant_top_cat == 'on' && $db_restaurant_top_cat != 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", 'on');
				    } else if ($get_restaurant_top_cat != 'on' && $db_restaurant_top_cat == 'on') {
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');
				    }
				} else {
				    update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');
				}

				// updating restaurant meta
				// as per transaction meta
				$this->restaurant_assign_meta($restaurant_id, $active_pckg_trans_id);

				// Assign Status of restaurant
				$this->restaurant_update_status($restaurant_id);
			    }
			}
		    }
		    // end of using existing package
		}
		$response['status'] = true;
		$response['msg'] = esc_html__('Restaurant added successfully and packages processed', 'foodbakery');
		// end assigning packages
		// and payment processs
	    }
	    return $response;
	}

	/**
	 * Adding Transaction
	 * @return id
	 */
	public function foodbakery_restaurant_add_transaction($type = '', $restaurant_id = 0, $package_id = 0, $publisher_id = '') {
	    global $foodbakery_plugin_options;
	    $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
	    $foodbakery_pay_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';
	    $woocommerce_enabled = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';
	    $foodbakery_trans_id = rand(10000000, 99999999);
	    $transaction_detail = '';
	    $transaction_post = array(
		'post_title' => '#' . $foodbakery_trans_id,
		'post_status' => 'publish',
		'post_type' => 'package-orders',
		'post_date' => current_time('Y-m-d H:i:s')
	    );
	    //insert the transaction
	    if ($publisher_id != '') {
		$trans_id = wp_insert_post($transaction_post);
	    }

	    if (isset($trans_id) && $type != '' && $trans_id > 0) {

		$pay_process = true;

		$foodbakery_trans_pkg = '';
		$foodbakery_trans_pkg_expiry = '';
		$package_restaurant_allowed = 0;
		$package_restaurant_duration = 0;

		$foodbakery_trans_amount = 0;

		if ($package_id != '' && $package_id != 0) {
		    $foodbakery_trans_pkg = $package_id;

		    $foodbakery_package_data = get_post_meta($package_id, 'foodbakery_package_data', true);

		    $package_duration = isset($foodbakery_package_data['duration']['value']) ? $foodbakery_package_data['duration']['value'] : 0;
		    $package_restaurant_duration = isset($foodbakery_package_data['restaurant_duration']['value']) ? $foodbakery_package_data['restaurant_duration']['value'] : 0;
		    $package_restaurant_allowed = 1;

		    $package_amount = get_post_meta($package_id, 'foodbakery_package_price', true);

		    // calculating package expiry date
		    $foodbakery_trans_pkg_expiry = $this->date_conv($package_duration, 'days');
		    $foodbakery_trans_pkg_expiry = strtotime($foodbakery_trans_pkg_expiry);

		    // calculating_amount
		    $foodbakery_trans_amount += FOODBAKERY_FUNCTIONS()->num_format($package_amount);

		    if ($woocommerce_enabled != 'on') {
			if ($foodbakery_vat_switch == 'on' && $foodbakery_pay_vat > 0 && $foodbakery_trans_amount > 0) {

			    $foodbakery_vat_amount = $foodbakery_trans_amount * ( $foodbakery_pay_vat / 100 );

			    $foodbakery_trans_amount += FOODBAKERY_FUNCTIONS()->num_format($foodbakery_vat_amount);
			}
		    }

		    // transaction offer fields
		    $t_package_serv_num = isset($foodbakery_package_data['number_of_services']['value']) ? $foodbakery_package_data['number_of_services']['value'] : 0;
		    $t_package_pic_num = isset($foodbakery_package_data['number_of_pictures']['value']) ? $foodbakery_package_data['number_of_pictures']['value'] : 0;
		    $t_package_doc_num = isset($foodbakery_package_data['number_of_documents']['value']) ? $foodbakery_package_data['number_of_documents']['value'] : 0;
		    $t_package_tags_num = isset($foodbakery_package_data['number_of_tags']['value']) ? $foodbakery_package_data['number_of_tags']['value'] : 0;
		    $t_package_reviews = isset($foodbakery_package_data['reviews']['value']) ? $foodbakery_package_data['reviews']['value'] : '';
		    $t_package_feature_list = isset($foodbakery_package_data['number_of_featured_restaurants']['value']) ? $foodbakery_package_data['number_of_featured_restaurants']['value'] : 0;
		    $t_package_top_cat_list = isset($foodbakery_package_data['number_of_top_cat_restaurants']['value']) ? $foodbakery_package_data['number_of_top_cat_restaurants']['value'] : 0;
		    $t_package_phone = isset($foodbakery_package_data['phone_number']['value']) ? $foodbakery_package_data['phone_number']['value'] : '';
		    $t_package_website = isset($foodbakery_package_data['website_link']['value']) ? $foodbakery_package_data['website_link']['value'] : '';
		    $t_package_social = isset($foodbakery_package_data['social_impressions_reach']['value']) ? $foodbakery_package_data['social_impressions_reach']['value'] : '';
		    $t_package_ror = isset($foodbakery_package_data['respond_to_reviews']['value']) ? $foodbakery_package_data['respond_to_reviews']['value'] : '';
		    $t_package_dynamic_values = get_post_meta($package_id, 'foodbakery_package_fields', true);
		}

		$foodbakery_trans_array = array(
		    'transaction_id' => $trans_id,
		    'transaction_user' => $publisher_id,
		    'transaction_package' => $foodbakery_trans_pkg,
		    'transaction_amount' => $foodbakery_trans_amount,
		    'transaction_expiry_date' => $foodbakery_trans_pkg_expiry,
		    'transaction_restaurants' => $package_restaurant_allowed,
		    'transaction_restaurant_expiry' => $package_restaurant_duration,
		    'transaction_restaurant_pic_num' => isset($t_package_pic_num) ? $t_package_pic_num : '',
		    'transaction_restaurant_tags_num' => isset($t_package_tags_num) ? $t_package_tags_num : '',
		    'transaction_restaurant_reviews' => isset($t_package_reviews) ? $t_package_reviews : '',
		    'transaction_restaurant_feature_list' => isset($t_package_feature_list) ? $t_package_feature_list : '',
		    'transaction_restaurant_top_cat_list' => isset($t_package_top_cat_list) ? $t_package_top_cat_list : '',
		    'transaction_restaurant_phone' => isset($t_package_phone) ? $t_package_phone : '',
		    'transaction_restaurant_website' => isset($t_package_website) ? $t_package_website : '',
		    'transaction_restaurant_social' => isset($t_package_social) ? $t_package_social : '',
		    'transaction_restaurant_ror' => isset($t_package_ror) ? $t_package_ror : '',
		    'transaction_dynamic' => isset($t_package_dynamic_values) ? $t_package_dynamic_values : '',
		    'transaction_ptype' => $type,
		);

		if ($package_id != '' && $package_id != 0) {
		    if ($foodbakery_trans_amount <= 0) {
			$foodbakery_trans_array['transaction_pay_method'] = '-';
			$foodbakery_trans_array['transaction_status'] = 'approved';
			$pay_process = false;
		    }
		    $package_type = get_post_meta($package_id, 'foodbakery_package_type', true);
		    if ($package_type == 'free') {
			$foodbakery_trans_array['transaction_pay_method'] = '-';
			$foodbakery_trans_array['transaction_status'] = 'approved';
			$pay_process = false;
		    }
		}

		if (($type == 'add-restaurant' || $type == 'update-restaurant') && $restaurant_id != '' && $restaurant_id != 0) {


		    $foodbakery_package_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_package', true);
		    if ($foodbakery_package_id) {
			$foodbakery_package_data = get_post_meta($foodbakery_package_id, 'foodbakery_package_data', true);

			$restaurant_duration = isset($foodbakery_package_data['restaurant_duration']['value']) ? $foodbakery_package_data['restaurant_duration']['value'] : 0;

			// calculating restaurant expiry date
			$foodbakery_trans_restaurant_expiry = $this->date_conv($restaurant_duration, 'days');

			// update restaurant expiry, featured, top category
			// this change will be temporary
			update_post_meta($restaurant_id, "foodbakery_restaurant_expired", strtotime($foodbakery_trans_restaurant_expiry));
			// update restaurant featured
			if ($t_package_feature_list == 'on') {
			    // featured from form
			    $get_restaurant_featured = foodbakery_get_input('foodbakery_restaurant_featured', '');
			    // featured from meta
			    $db_restaurant_featured = get_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", true);

			    if ($get_restaurant_featured == 'on' && $db_restaurant_featured != 'on') {
				update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", 'on');
			    } else if ($get_restaurant_featured != 'on' && $db_restaurant_featured == 'on') {
				update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
			    }
			} else {
			    update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
			}
			// update restaurant top category
			if ($t_package_top_cat_list == 'on') {
			    // Top Cat from form
			    $get_restaurant_top_cat = foodbakery_get_input('foodbakery_restaurant_top_cat', '');
			    // Top Cat from meta
			    $db_restaurant_top_cat = get_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", true);

			    if ($get_restaurant_top_cat == 'on' && $db_restaurant_top_cat != 'on') {
				update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", 'on');
			    } else if ($get_restaurant_top_cat != 'on' && $db_restaurant_top_cat == 'on') {
				update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');
			    }
			} else {
			    update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');
			}
			$package_type = get_post_meta($foodbakery_package_id, 'foodbakery_package_type', true);
			if ($package_type == 'free') {
			    global $foodbakery_plugin_options;
			    $foodbakery_restaurants_review_option = isset($foodbakery_plugin_options['foodbakery_restaurants_review_option']) ? $foodbakery_plugin_options['foodbakery_restaurants_review_option'] : '';
			    $user_data = wp_get_current_user();

			    if ($foodbakery_restaurants_review_option == 'on') {
				update_post_meta($restaurant_id, 'foodbakery_restaurant_status', 'awaiting-activation');
				// Restaurant not approved
				do_action('foodbakery_restaurant_not_approved_email', $user_data, $restaurant_id);
			    } else {
				update_post_meta($restaurant_id, 'foodbakery_restaurant_status', 'active');
				// Restaurant approved
				do_action('foodbakery_restaurant_approved_email', $user_data, $restaurant_id);

				// social sharing
				$get_social_reach = get_post_meta($restaurant_id, 'foodbakery_transaction_restaurant_social', true);
				if ($get_social_reach == 'on') {
				    do_action('foodbakery_restaurant_social_post', $restaurant_id);
				}
			    }
			    // Add restaurant type in free posting
			    update_post_meta($restaurant_id, 'foodbakery_restaurant_type', 'restaurant-settings');
			}

			//if(){}
			// updating restaurant ids in transaction
			$foodbakery_trans_array['restaurant_ids'] = array($restaurant_id);
			// updating transaction id in restaurant
			update_post_meta($restaurant_id, "foodbakery_trans_id", $foodbakery_trans_id);

			// updating package id in restaurant
			update_post_meta($restaurant_id, "foodbakery_restaurant_package", $package_id);

			// update restaurant subscription
			if ($type == 'add-restaurant') {
			    $package_subscripton_data = array(
				array(
				    'type' => ($type == 'add-restaurant' ? 'add_package' : 'update_package'),
				    'transaction_id' => $trans_id,
				    'title_id' => $foodbakery_trans_id,
				    'package_id' => $package_id,
				    'subscribe_date' => strtotime(current_time('Y-m-d H:i:s')),
				)
			    );
			} else {
			    $package_subscripton_data = array(
				'type' => ($type == 'add-restaurant' ? 'add_package' : 'update_package'),
				'transaction_id' => $trans_id,
				'title_id' => $foodbakery_trans_id,
				'package_id' => $package_id,
				'subscribe_date' => strtotime(current_time('Y-m-d H:i:s')),
			    );
			}
			$get_subscripton_data = get_post_meta($restaurant_id, "package_subscripton_data", true);
			$merged_subscripton_data = $this->merge_in_array($get_subscripton_data, $package_subscripton_data, false);
			update_post_meta($restaurant_id, "package_subscripton_data", $merged_subscripton_data);

			// update restaurant featured
			if (isset($foodbakery_package_data) && !empty($foodbakery_package_data)) {
			    // Top Cat from form
			    $get_restaurant_featured = foodbakery_get_input('foodbakery_restaurant_featured', '');
			    if ($t_package_feature_list == 'on' && $get_restaurant_featured == 'on') {
				update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", 'on');
				$foodbakery_trans_array['featured_ids'] = array($restaurant_id);
			    }
			}

			// update restaurant top category
			if (isset($foodbakery_package_data) && !empty($foodbakery_package_data)) {
			    // Top Cat from form
			    $get_restaurant_top_cat = foodbakery_get_input('foodbakery_restaurant_top_cat', '');
			    if ($t_package_top_cat_list == 'on' && $get_restaurant_top_cat == 'on') {
				update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", 'on');
				$foodbakery_trans_array['top_cat_ids'] = array($restaurant_id);
			    }
			}
		    }
		}
		// update package dynamic fields in transaction
		$foodbakery_package_dynamic = get_post_meta($package_id, 'foodbakery_package_fields', true);
		$foodbakery_trans_array['transaction_dynamic'] = $foodbakery_package_dynamic;

		// updating all fields of transaction
		foreach ($foodbakery_trans_array as $trans_key => $trans_val) {
		    update_post_meta($trans_id, "foodbakery_{$trans_key}", $trans_val);
		}

		// Inserting VAT amount in array
		if (isset($foodbakery_vat_amount) && $foodbakery_vat_amount > 0) {
		    $foodbakery_trans_array['vat_amount'] = $foodbakery_vat_amount;
		}

		// Inserting random id in array
		$foodbakery_trans_array['trans_rand_id'] = $foodbakery_trans_id;

		// Inserting item id in array
		if ($restaurant_id != '' && $restaurant_id != 0) {
		    $foodbakery_trans_array['trans_item_id'] = $restaurant_id;
		    update_post_meta($trans_id, "order_item_id", $restaurant_id);
		} else {
		    $foodbakery_trans_array['trans_item_id'] = $foodbakery_trans_id;
		}

		if (($type == 'add-restaurant' || $type == 'update-restaurant') && $restaurant_id != '' && $restaurant_id != 0) {
		    // updating restaurant meta
		    // as per transaction meta
		    $this->restaurant_assign_meta($restaurant_id, $trans_id);
		}

		// Payment Process
		if ($pay_process) {
		    $response = array(
			'status' => true,
			'msg' => $trans_id,
		    );
		    echo json_encode($response);
		    wp_die();
		}
	    }
	    return apply_filters('foodbakery_restaurant_add_transaction', $transaction_detail, $type, $restaurant_id, $package_id, $publisher_id);
	}

	/**
	 * Assigning Status for Restaurant
	 * @return
	 */
	public function restaurant_update_status($restaurant_id = '') {
	    global $foodbakery_plugin_options;
	    $foodbakery_restaurants_review_option = isset($foodbakery_plugin_options['foodbakery_restaurants_review_option']) ? $foodbakery_plugin_options['foodbakery_restaurants_review_option'] : '';

	    $get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    $is_updating = false;
	    if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
		$is_updating = true;
	    }

	    $user_data = wp_get_current_user();

	    if ($foodbakery_restaurants_review_option == 'on') {
		update_post_meta($restaurant_id, 'foodbakery_restaurant_status', 'awaiting-activation');
		// Restaurant not approved
		do_action('foodbakery_restaurant_not_approved_email', $user_data, $restaurant_id);
	    } else {
		update_post_meta($restaurant_id, 'foodbakery_restaurant_status', 'active');
		// Restaurant approved
		do_action('foodbakery_restaurant_approved_email', $user_data, $restaurant_id);

		// social sharing
		$get_social_reach = get_post_meta($restaurant_id, 'foodbakery_transaction_restaurant_social', true);
		if ($get_social_reach == 'on') {
		    do_action('foodbakery_restaurant_social_post', $restaurant_id);
		}
	    }

	    $foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : '';

	    if ($foodbakery_free_restaurants_switch != 'on') {

		$foodbakery_package_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_package', true);
		if ($foodbakery_package_id) {
		    $foodbakery_package_data = get_post_meta($foodbakery_package_id, 'foodbakery_package_data', true);

		    $restaurant_duration = isset($foodbakery_package_data['restaurant_duration']['value']) ? $foodbakery_package_data['restaurant_duration']['value'] : 0;

		    // calculating restaurant expiry date
		    $foodbakery_trans_restaurant_expiry = $this->date_conv($restaurant_duration, 'days');
		    update_post_meta($restaurant_id, 'foodbakery_restaurant_expired', strtotime($foodbakery_trans_restaurant_expiry));
		}
	    }
	}

	/**
	 * Updating transaction meta into restaurant meta
	 * @return
	 */
	public function restaurant_assign_meta($restaurant_id = '', $trans_id = '') {
	    $assign_array = array();

	    $trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_tags_num', true);
	    $assign_array[] = array(
		'key' => 'foodbakery_transaction_restaurant_tags_num',
		'label' => esc_html__('Number of Tags', 'foodbakery'),
		'value' => $trans_get_value,
	    );
	    $trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_reviews', true);
	    $assign_array[] = array(
		'key' => 'foodbakery_transaction_restaurant_reviews',
		'label' => esc_html__('Reviews', 'foodbakery'),
		'value' => $trans_get_value,
	    );
	    $trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_phone', true);
	    $assign_array[] = array(
		'key' => 'foodbakery_transaction_restaurant_phone',
		'label' => esc_html__('Phone Number', 'foodbakery'),
		'value' => $trans_get_value,
	    );
	    $trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_website', true);
	    $assign_array[] = array(
		'key' => 'foodbakery_transaction_restaurant_website',
		'label' => esc_html__('Website Link', 'foodbakery'),
		'value' => $trans_get_value,
	    );
	    $trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_social', true);
	    $assign_array[] = array(
		'key' => 'foodbakery_transaction_restaurant_social',
		'label' => esc_html__('Social Impressions Reach', 'foodbakery'),
		'value' => $trans_get_value,
	    );
	    $trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_ror', true);
	    $assign_array[] = array(
		'key' => 'foodbakery_transaction_restaurant_ror',
		'label' => esc_html__('Respond to Reviews', 'foodbakery'),
		'value' => $trans_get_value,
	    );
	    $trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_dynamic', true);
	    $assign_array[] = array(
		'key' => 'foodbakery_transaction_dynamic',
		'label' => esc_html__('Other Features', 'foodbakery'),
		'value' => $trans_get_value,
	    );

	    if ($restaurant_id != '' && $trans_id != '') {
		foreach ($assign_array as $assign) {
		    update_post_meta($restaurant_id, $assign['key'], $assign['value']);
		}
		update_post_meta($restaurant_id, 'foodbakery_trans_all_meta', $assign_array);
	    }

	    return $assign_array;
	}

	/**
	 * Restaurant Categories
	 * @return markup
	 */
	public function restaurant_categories($type_id = '', $foodbakery_id = '') {
	    global $restaurant_add_counter, $foodbakery_form_fields, $foodbakery_restaurant_meta;

	    $html = '';
	    $restaurant_type_post = get_post($type_id);
	    $restaurant_type_slug = isset($restaurant_type_post->post_name) ? $restaurant_type_post->post_name : 0;

	    $html .= $foodbakery_restaurant_meta->restaurant_categories($restaurant_type_slug, $foodbakery_id, false, true);

	    return apply_filters('foodbakery_front_restaurant_add_categories', $html, $type_id, $foodbakery_id);
	}

	/**
	 * Check user package subscription
	 * @return id
	 */
	public function foodbakery_is_pkg_subscribed($foodbakery_package_id = 0, $trans_id = 0) {
	    global $post, $current_user;

	    $company_id = foodbakery_company_id_form_user_id($current_user->ID);

	    if ($trans_id == '') {
		$trans_id = 0;
	    }
	    $transaction_id = false;
	    $foodbakery_current_date = strtotime(date('d-m-Y'));
	    $args = array(
		'posts_per_page' => "-1",
		'post_type' => 'package-orders',
		'post_status' => 'publish',
		'post__in' => array($trans_id),
		'meta_query' => array(
		    'relation' => 'AND',
		    array(
			'key' => 'foodbakery_transaction_package',
			'value' => $foodbakery_package_id,
			'compare' => '=',
		    ),
		    array(
			'key' => 'foodbakery_transaction_user',
			'value' => $company_id,
			'compare' => '=',
		    ),
		    array(
			'key' => 'foodbakery_transaction_expiry_date',
			'value' => $foodbakery_current_date,
			'compare' => '>',
		    ),
		    array(
			'key' => 'foodbakery_transaction_status',
			'value' => 'approved',
			'compare' => '=',
		    ),
		),
	    );

	    $custom_query = new WP_Query($args);
	    $foodbakery_trans_count = $custom_query->post_count;

	    if ($foodbakery_trans_count > 0) {
		while ($custom_query->have_posts()) : $custom_query->the_post();
		    $foodbakery_pkg_list_num = get_post_meta($post->ID, 'foodbakery_transaction_restaurants', true);
		    $foodbakery_restaurant_ids = get_post_meta($post->ID, 'foodbakery_restaurant_ids', true);

		    if (empty($foodbakery_restaurant_ids)) {
			$foodbakery_restaurant_ids_size = 0;
		    } else {
			$foodbakery_restaurant_ids_size = absint(sizeof($foodbakery_restaurant_ids));
		    }
		    $foodbakery_ids_num = $foodbakery_restaurant_ids_size;
		    if ((int) $foodbakery_ids_num < (int) $foodbakery_pkg_list_num) {
			$foodbakery_trnasaction_id = $post->ID;
		    }
		endwhile;
		wp_reset_postdata();
	    }

	    if (isset($foodbakery_trnasaction_id) && $foodbakery_trnasaction_id > 0) {
		$transaction_id = $foodbakery_trnasaction_id;
	    }
	    return apply_filters('foodbakery_restaurant_is_package_subscribe', $transaction_id, $foodbakery_package_id, $trans_id);
	}

	/**
	 * Creating foodbakery restaurant
	 * @return restaurant id
	 */
	public function restaurant_insert($publisher_id = '') {
	    global $foodbakery_plugin_options, $restaurant_add_counter;

	    $foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : '';

	    $restaurant_id = 0;
	    $restaurant_title = isset($_POST['foodbakery_restaurant_title']) ? $_POST['foodbakery_restaurant_title'] : '';

	    if ($restaurant_title != '' && $publisher_id != '') {

		$form_rand_numb = isset($_POST['form_rand_id']) ? $_POST['form_rand_id'] : '';
		$form_rand_transient = get_transient('restaurant_submission_check');

		if ($form_rand_transient != $form_rand_numb) {
		    $restaurant_post = array(
			'post_title' => wp_strip_all_tags($restaurant_title),
			'post_content' => '',
			'post_status' => 'publish',
			'post_type' => 'restaurants',
			'post_date' => current_time('Y-m-d H:i:s')
		    );

		    //insert job
		    $restaurant_id = wp_insert_post($restaurant_post);

		    set_transient('restaurant_submission_check', $form_rand_numb, 60 * 60 * 24 * 30);

		    $user_data = wp_get_current_user();
		    do_action('foodbakery_restaurant_add_email', $user_data, $restaurant_id);
			do_action('foodbakery_restaurant_add_admin_email', $user_data, $restaurant_id);
		}
	    }

	    return apply_filters('foodbakery_front_restaurant_add_create', $restaurant_id);
	}

	public function restaurant_show_payment_information() {

	    $this->restaurant_add_tag_before('payment-information-tab-container');
	    ?>
	    <li>
		<?php
		ob_start();
		$_REQUEST['trans_id'] = 0;
		$_REQUEST['action'] = 'restaurant-package';
		$_GET['trans_id'] = 0;
		$_GET['action'] = 'restaurant-package';
		$trans_fields = array(
		    'trans_id' => 0,
		    'action' => 'restaurant-package',
		    'back_button' => true,
		);
		do_action('foodbakery_payment_gateways', $trans_fields);
		$output = ob_get_clean();
		echo str_replace('col-lg-8 col-md-8', 'col-lg-12 col-md-12', $output);
		?>
	    </li>
	    <li class="payment-process-form-container"></li>
	    <?php
	    $this->restaurant_add_tag_after();
	}

	public function restaurant_show_activation_tab() {
	    global $foodbakery_plugin_options;
	    $this->restaurant_add_tag_before('activation-tab-container');
	    $img_id = isset($foodbakery_plugin_options['foodbakery_restaurant_success_image']) ? $foodbakery_plugin_options['foodbakery_restaurant_success_image'] : '';
	    $success_message = isset($foodbakery_plugin_options['foodbakery_restaurant_success_message']) ? $foodbakery_plugin_options['foodbakery_restaurant_success_message'] : '';
	    $success_phone = isset($foodbakery_plugin_options['foodbakery_restaurant_success_phone']) ? $foodbakery_plugin_options['foodbakery_restaurant_success_phone'] : '';
	    $success_fax = isset($foodbakery_plugin_options['foodbakery_restaurant_success_fax']) ? $foodbakery_plugin_options['foodbakery_restaurant_success_fax'] : '';
	    $success_email = isset($foodbakery_plugin_options['foodbakery_restaurant_success_email']) ? $foodbakery_plugin_options['foodbakery_restaurant_success_email'] : '';
	    ?>
	    <li>
	        <div class="activation-tab-message">
	    	<div class="media-holder">
	    	    <figure>
			    <?php if ($img_id != '') : ?>
				<img src="<?php echo wp_get_attachment_url($img_id); ?>" alt="<?php esc_html_e('Thank You', 'foodbakery'); ?>">
			    <?php endif; ?>
	    	    </figure>
	    	</div>
	    	<div class="text-holder">
	    	    <strong><?php esc_html_e('Thank You', 'foodbakery'); ?></strong>
			<?php if ($success_message != '') : ?>
			    <span><?php echo esc_html($success_message); ?></span>
			<?php endif; ?>
	    	</div> 

		    <?php if ($success_phone != '' || $success_fax != '' || $success_email != '') : ?>
			<div class="thankyou-contacts">
			    <p><?php esc_html_e('For cancellation or more infomation Please Contact Us', 'foodbakery'); ?></p>
			    <ul class="list-inline clearfix">
				<?php if ($success_phone != '') : ?>
		    		<li><i class="icon-phone4"></i><?php echo esc_html($success_phone); ?></li>
				<?php endif; ?>
				<?php if ($success_fax != '') : ?>
		    		<li><i class="icon-fax"></i><?php echo esc_html($success_fax); ?></li>
				<?php endif; ?>
				<?php if ($success_email != '') : ?>
		    		<li><i class="icon-envelope-o"></i><?php echo esc_html($success_email); ?></li>
				<?php endif; ?>
			    </ul>
			</div>
		    <?php endif; ?>

	        </div>
	    </li>
	    <?php
	    $this->restaurant_add_tag_after();
	}

	public function restaurant_show_set_membership($die_ret = '') {
	    global $restaurant_add_counter, $foodbakery_plugin_options;

	    $restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : '';

	    ob_start();

	    $this->restaurant_add_tag_before('package-tab-container');
	    ?>
	    <li>
	        <ul class="membership-info-main">
		    <?php $this->restaurant_info(); ?>
		    <?php $this->restaurant_packages(); ?>
	        </ul>
	    </li>
	    <li>

		<?php
		$html = '
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="field-holder">
								<div class="payment-holder">
									<input type="submit" value="' . esc_html__('Back', 'foodbakery') . '" id="btn-back-package" class="back-bg-color">
									<input type="submit" value="' . esc_html__('Next', 'foodbakery') . '" id="btn-next-package" class="bgcolor">
								</div>
							</div> 
						</div>
					</div>';
		echo force_balance_tags($html);
		?>
	    </li>
	    <?php
	    $this->restaurant_add_tag_after();

	    $html = ob_get_clean();
	    if ($die_ret == 1) {
		echo force_balance_tags($html);
	    } else {
		echo json_encode(array('html' => $html));
		die;
	    }
	}

	public function restaurant_show_set_settings($die_ret = '') {
	    global $restaurant_add_counter, $foodbakery_plugin_options;

	    $restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : $restaurant_add_counter;

	    ob_start();

	    $this->restaurant_add_tag_before('restaurant-information-tab-container');
	    ?>
	    <li>
		<?php
		$this->title_description();
		$this->user_register_fields();
		?>
	    </li>
	    <li>
		<?php
		$check_box = '';
		$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
		$btn_text = esc_html__('Next', 'foodbakery');

		$check_box = wp_foodbakery::get_terms_and_conditions_field('', 'terms-' . $restaurant_add_counter);

		$html = '
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="field-holder">
								<div class="payment-holder">
									' . $check_box . '
									<input type="submit" value="' . $btn_text . '" id="btn-next-restaurant-information" class="bgcolor">
								</div>
							</div> 
						</div>
					</div>';
		echo force_balance_tags($html);
		?>
		<?php $this->after_restaurant(); ?>
	    </li>
	    <?php
	    $this->restaurant_add_tag_after();

	    $html = ob_get_clean();

	    if ($die_ret == 1) {
		echo force_balance_tags($html);
	    } else {
		echo json_encode(array('html' => $html));
		die;
	    }
	}

	/**
	 * Basic Info
	 * @return markup
	 */
	public function title_description($html = '') {
	    global $foodbakery_form_fields, $restaurant_add_counter, $foodbakery_plugin_options;
	    $foodbakery_restaurant_title = '';
	    $foodbakery_minimum_order_value = '';
	    $foodbakery_delivery_fee = '';
	    $foodbakery_pickup_fee = '';

	    $restaurant_phone = '';
	    $restaurant_manager_name = '';
	    $restaurant_manager_phone = '';
	    $restaurant_email = '';
	    $restaurant_pickup_delivery = '';

	    $get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
		$foodbakery_restaurant_title = get_the_title($get_restaurant_id);
		$restaurant_post = get_post($get_restaurant_id);
		$foodbakery_minimum_order_value = get_post_meta($get_restaurant_id, 'foodbakery_minimum_order_value', true);
		$foodbakery_delivery_fee = get_post_meta($get_restaurant_id, 'foodbakery_delivery_fee', true);
		$foodbakery_pickup_fee = get_post_meta($get_restaurant_id, 'foodbakery_pickup_fee', true);

		$restaurant_phone = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_contact_phone', true);
		$restaurant_manager_name = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_manager_name', true);
		$restaurant_manager_phone = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_manager_phone', true);
		$restaurant_email = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_contact_email', true);
		$restaurant_pickup_delivery = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);
	    }

	    $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
	    $selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';
	    $restaurant_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : 0;

	    $html .= '
			<div class="row">';
	    $foodbakery_restaurant_announce_title = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_title']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_title'] : '';
	    $foodbakery_restaurant_announce_description = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_description']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_description'] : '';
	    ob_start();
	    if ((isset($foodbakery_restaurant_announce_title) && $foodbakery_restaurant_announce_title <> '') || (isset($foodbakery_restaurant_announce_description) && $foodbakery_restaurant_announce_description <> '')) {
		$this->before_restaurant();
	    }
	    $html .= ob_get_clean();
	    $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	    $html .= '<div class="row">';
	    $html .= '<ul class="has-seperator">';
	    $html .= '<li>';
	    $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
	    $html .= '<div class="field-holder">
						<label>' . esc_html__('Restaurant name *', 'foodbakery') . '</label>';
	    $html .= $foodbakery_form_fields->foodbakery_form_text_render(
		    array(
			'id' => 'restaurant_title_' . $restaurant_add_counter,
			'cust_name' => 'foodbakery_restaurant_title',
			'std' => $foodbakery_restaurant_title,
			'desc' => '',
			'classes' => 'foodbakery-dev-req-field',
			'extra_atr' => ' placeholder="' . esc_html__('i.e Pizza Hut', 'foodbakery') . '"',
			'return' => true,
			'force_std' => true,
			'hint_text' => '',
		    )
	    );

	    $html .= '</div></div>';

	    $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
	    $html .= '<div class="field-holder">
						<label>' . esc_html__('Restaurant phone', 'foodbakery') . '</label>';
	    $html .= $foodbakery_form_fields->foodbakery_form_text_render(
		    array(
			'id' => 'restaurant_phone_' . $restaurant_add_counter,
			'cust_name' => 'foodbakery_restaurant_contact_phone',
			'std' => $restaurant_phone,
			'desc' => '',
			'classes' => '',
			'extra_atr' => ' placeholder="' . esc_html__('i.e +1 321 828 6662', 'foodbakery') . '"',
			'return' => true,
			'force_std' => true,
			'hint_text' => '',
		    )
	    );
	    $html .= '</div></div>';

	    $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
	    $html .= '<div class="field-holder">
				<label>' . esc_html__('Manager Name', 'foodbakery') . '</label>';
	    $html .= $foodbakery_form_fields->foodbakery_form_text_render(
		    array(
			'id' => 'restaurant_manager_name_' . $restaurant_add_counter,
			'cust_name' => 'foodbakery_restaurant_manager_name',
			'std' => $restaurant_manager_name,
			'desc' => '',
			'classes' => '',
			'extra_atr' => ' placeholder="' . esc_html__('i.e Alard Willaim', 'foodbakery') . '"',
			'return' => true,
			'force_std' => true,
			'hint_text' => '',
		    )
	    );
	    $html .= '</div></div>';

	    $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
	    $html .= '<div class="field-holder">
				<label>' . esc_html__('Manager Contact phone', 'foodbakery') . '</label>';
	    $html .= $foodbakery_form_fields->foodbakery_form_text_render(
		    array(
			'id' => 'restaurant_manager_phone_' . $restaurant_add_counter,
			'cust_name' => 'foodbakery_restaurant_manager_phone',
			'std' => $restaurant_manager_phone,
			'desc' => '',
			'classes' => '',
			'extra_atr' => ' placeholder="' . esc_html__('i.e +1 321 828 6662', 'foodbakery') . '"',
			'return' => true,
			'force_std' => true,
			'hint_text' => '',
		    )
	    );
	    $html .= '</div></div>';

	    $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
	    $html .= '
				<div class="field-holder">
				<label>' . esc_html__('Contact Email', 'foodbakery') . '</label>';
	    $html .= $foodbakery_form_fields->foodbakery_form_text_render(
		    array(
			'id' => 'restaurant_contact_email_' . $restaurant_add_counter,
			'cust_name' => 'foodbakery_restaurant_contact_email',
			'std' => $restaurant_email,
			'desc' => '',
			'classes' => '',
			'extra_atr' => ' placeholder="' . esc_html__('i.e alard@example.com ', 'foodbakery') . '"',
			'return' => true,
			'force_std' => true,
			'hint_text' => '',
		    )
	    );
	    $html .= '</div></div>';
	    $html .= '</li>';

	    $html .= '<li>';
	    $html1 = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	    $html1 .= $this->restaurant_location($restaurant_type_id, $get_restaurant_id);
	    $html1 .= '</div>';
	    $html .= force_balance_tags($html1);
	    $html .= '</li>';

	    $html .= '<li>';
	    $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	    $html .= '<div class="field-holder">
				<label>' . esc_html__('Delivery/Pickup', 'foodbakery') . '</label>';
	    $html .= $foodbakery_form_fields->foodbakery_form_select_render(
		    array(
			'id' => 'restaurant_pickup_delivery_' . $restaurant_add_counter,
			'cust_name' => 'foodbakery_restaurant_pickup_delivery',
			'std' => $restaurant_pickup_delivery,
			'desc' => '',
			'classes' => 'chosen-select',
			'return' => true,
			'force_std' => true,
			'options' => array(
			    'delivery' => esc_html__('Delivery', 'foodbakery'),
			    'pickup' => esc_html__('Pickup', 'foodbakery'),
			    'delivery_and_pickup' => esc_html__('Delivery &amp; Pickup', 'foodbakery'),
			),
			'hint_text' => '',
		    )
	    );
	    $html .= '</div></div>';

	    $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	    $html .= '<div class="foodbakery-dev-appended-cats12">' . $this->restaurant_categories($restaurant_type_id, $get_restaurant_id) . '</div>';
	    $html .= '</div>';
	    $html .= '</li>';

	    $html .= '<li>';
	    $html .= $this->user_register_fields();
	    $html .= '</li>';
	    $html .= '</ul>';
	    $html .= '
			</div>
			</div>
			</div>';

	    echo force_balance_tags($html);
	}

	/**
	 * Location Map
	 * @return markup
	 */
	public function restaurant_location($type_id = '', $foodbakery_id = '') {
	    global $restaurant_add_counter;
	    $html = '';
	    $foodbakery_restaurant_location = get_post_meta($type_id, 'foodbakery_location_element', true);
	    if ($foodbakery_restaurant_location == 'on') {
		$html .= '
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="element-title">
							<h4>' . esc_html__('Location', 'foodbakery') . '</h4>
						</div>
						<div class="row">';

		ob_start();
		FOODBAKERY_FUNCTIONS()->frontend_location_fields_custom($foodbakery_id, 'restaurant');
		$html .= ob_get_clean();
		$html .= '</div>
					</div>
				</div></div>';
	    }
	    return apply_filters('foodbakery_front_restaurant_add_location', $html, $type_id, $foodbakery_id);
	    // usage :: add_filter('foodbakery_front_restaurant_add_location', 'my_callback_function', 10, 3);
	}

	/**
	 * Steps before
	 * @return markup
	 */
	public function before_restaurant($html = '') {
	    return '';
	    global $foodbakery_plugin_options, $Payment_Processing;
	    $foodbakery_restaurant_announce_title = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_title']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_title'] : '';
	    $foodbakery_restaurant_announce_description = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_description']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_description'] : '';
	    $foodbakery_announce_bg_color = isset($foodbakery_plugin_options['foodbakery_announce_bg_color']) ? $foodbakery_plugin_options['foodbakery_announce_bg_color'] : '#2b8dc4';
	    $restaurant_color = 'style="background-color:' . $foodbakery_announce_bg_color . '"';

	    $foodbakery_order_data = $Payment_Processing->custom_order_status_display();
	    if (isset($foodbakery_order_data) && !empty($foodbakery_order_data)) {
		?>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		    <div class="field-holder">
			<div class="user-message alert" <?php echo esc_html($restaurant_color); ?>>
			    <a href="#" data-dismiss="alert" class="close"><i class="icon-cross-out"></i></a>
				<?php
				global $woocommerce;
				if (class_exists('WooCommerce')) {
				    WC()->payment_gateways();
				    echo '<h2>' . __($foodbakery_order_data['status_message']) . '</h2>';
				    do_action('woocommerce_thankyou_' . $foodbakery_order_data['payment_method'], $foodbakery_order_data['order_id']);


					$temp_order = (int) $foodbakery_order_data['order_id'];
					$temp_order = $temp_order-4;
					update_post_meta($temp_order, 'foodbakery_order_status', 'processing');
				    $Payment_Processing->remove_raw_data($foodbakery_order_data['order_id']);
				}
				?>
			</div>
		    </div>
		</div>
		<?php
		$active = '';
	    }

	    echo force_balance_tags($html);
	}

	/**
	 * User Register Fields
	 * @return markup
	 */
	public function user_register_fields($html = '') {
	    global $restaurant_add_counter;

	    $is_updating = false;
	    $get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
		$is_updating = true;
	    }

	    if (!$is_updating && !is_user_logged_in()) {
		$html .= '
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<h4>' . esc_html__('Signup Fields', 'foodbakery') . '</h4>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="field-holder">
						<label>' . esc_html__('Username', 'foodbakery') . '</label>
						<input type="text" placeholder="' . esc_html__('i.e alardwillaim', 'foodbakery') . '" data-id="' . $restaurant_add_counter . '" data-type="username" name="foodbakery_restaurant_username" class="foodbakery-dev-username foodbakery-dev-req-field">
						<span class="field-info foodbakery-dev-username-check"></span>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="field-holder">
						<label>' . esc_html__('Email', 'foodbakery') . '</label>
						<input type="text" placeholder="' . esc_html__('i.e alard@example.com', 'foodbakery') . '" data-id="' . $restaurant_add_counter . '" data-type="useremail" name="foodbakery_restaurant_user_email" class="foodbakery-dev-user-email foodbakery-dev-req-field">
						<span class="field-info foodbakery-dev-useremail-check"></span>
					</div>
				</div>';
	    }
	    return force_balance_tags($html);
	}

	/**
	 * Basic Info
	 * @return markup
	 */
	public function hidden_restaurant_title() {
	    global $foodbakery_form_fields, $restaurant_add_counter;
	    $foodbakery_restaurant_title = '';

	    $get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
		$foodbakery_restaurant_title = get_the_title($get_restaurant_id);
	    }

	    $html = '<li style="display:none;">';
	    $html .= $foodbakery_form_fields->foodbakery_form_text_render(
		    array(
			'id' => 'restaurant_title_' . $restaurant_add_counter,
			'cust_name' => 'foodbakery_restaurant_title',
			'std' => $foodbakery_restaurant_title,
			'desc' => '',
			'classes' => '',
			'extra_atr' => '',
			'return' => true,
			'force_std' => true,
			'hint_text' => '',
		    )
	    );
	    $html .= '</li>';

	    echo force_balance_tags($html);
	}

	/**
	 * Restaurant Tag Open
	 * @return markup
	 */
	public function restaurant_add_tag_before($class = '') {
	    global $restaurant_add_counter;
	    $restaurant_add_counter = rand(10000000, 99999999);
	    echo '<ul id="foodbakery-dev-main-con-' . $restaurant_add_counter . '" class="register-add-restaurant-tab-container ' . $class . '" style="display: none;">';
	}

	/**
	 * Restaurant Tag Close
	 * @return markup
	 */
	public function restaurant_add_tag_after() {
	    echo '</ul>';
	}

	/**
	 * Select Restaurant Type
	 * @return markup
	 */
	public function restaurant_info($html = '') {
	    global $restaurant_add_counter;
	    $selected_type = '';
	    $get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
		$restaurant_status = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_status', true);
		$restaurant_post_on = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_posted', true);
		$restaurant_post_expiry = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_expired', true);

		$restaurant_post_expiry_date = date('d-m-Y', $restaurant_post_expiry);
		$restaurant_post_on_date = date('d-m-Y', $restaurant_post_on);

		$html .= '
				<li id="restaurant-info-sec-' . $restaurant_add_counter . '" class="restaurant-info-holder">
				<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<h4>' . esc_html__('Restaurant Info', 'foodbakery') . '</h4>
													<div class="buy-new-pakg-actions">
						<label>
							<a data-id="' . $restaurant_add_counter . '" href="javascript:void(0);" class="dev-foodbakery-restaurant-update-package">' . esc_html__('Update Membership', 'foodbakery') . '</a>
						</label>
					</div>
				</div>
					
				</div>';

		// pending post
		if ($restaurant_status == 'pending') {
		    $html .= '
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="field-holder">
							<p>' . esc_html__('Your transaction for this restaurant is at pending mode.', 'foodbakery') . '</p>
						</div>
					</div>';
		}
		// expired post
		else if (strtotime($restaurant_post_expiry_date) > strtotime($restaurant_post_on_date) && strtotime($restaurant_post_expiry_date) <= strtotime(current_time('d-m-Y'))) {
		    $html .= '
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="field-holder">
							<p>' . esc_html__('Your restaurant is expired.', 'foodbakery') . '</p>
						</div>
					</div>';
		}
		// awaiting approval OR active Restaurant
		else if (strtotime($restaurant_post_expiry_date) > strtotime(current_time('d-m-Y')) && $restaurant_status != 'pending') {

		    $restaurant_status_str = FOODBAKERY_FUNCTIONS()->get_restaurant_status($restaurant_status);

		    $restaurant_is_featured = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_is_featured', true);
		    $restaurant_is_featured = $this->restaurant_info_icon_check($restaurant_is_featured);
		    $restaurant_is_top_cat = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_is_top_cat', true);
		    $restaurant_is_top_cat = $this->restaurant_info_icon_check($restaurant_is_top_cat);
		    $trans_all_meta = get_post_meta($get_restaurant_id, 'foodbakery_trans_all_meta', true);

		    $trans_dynamic_meta = get_post_meta($get_restaurant_id, 'foodbakery_transaction_dynamic', true);

		    $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		    $html .= '<div class="restaurant-info-sec">';
		    $html .= '<div class="row">';
		    $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		    $html .= '<ul class="restaurant-pkg-points">';
		    $active_class = '';
		    if ($restaurant_status_str == '') {
			$active_class = ' class="active"';
		    }
		    $html .= '<li><label>' . esc_html__('Expiry') . '</label><span class="info-expiry-date">' . date_i18n(get_option('date_format'), $restaurant_post_expiry) . '</span></li>';
		    $html .= '<li><label>' . esc_html__('Status') . '</label><span ' . $active_class . '>' . $restaurant_status_str . '</span></li>';
		    $html .= '<li><label>' . esc_html__('Featured') . '</label><span>' . $restaurant_is_featured . '</span></li>';
		    $html .= '<li><label>' . esc_html__('Top Category') . '</label><span>' . $restaurant_is_top_cat . '</span></li>';
		    $html .= $this->restaurant_info_field_show($trans_all_meta, 0);
		    $html .= $this->restaurant_info_field_show($trans_all_meta, 1);
		    $html .= $this->restaurant_info_field_show($trans_all_meta, 2);
		    $html .= $this->restaurant_info_field_show($trans_all_meta, 3);
		    $html .= $this->restaurant_info_field_show($trans_all_meta, 4);

		    //
		    $html .= $this->restaurant_info_field_show($trans_all_meta, 5);
		    $html .= $this->restaurant_info_field_show($trans_all_meta, 6);


		    if (is_array($trans_dynamic_meta) && sizeof($trans_dynamic_meta) > 0) {
			foreach ($trans_dynamic_meta as $trans_dynamic) {
			    if (isset($trans_dynamic['field_type']) && isset($trans_dynamic['field_label']) && isset($trans_dynamic['field_value'])) {
				$d_type = $trans_dynamic['field_type'];
				$d_label = $trans_dynamic['field_label'];
				$d_value = $trans_dynamic['field_value'];

				if ($d_value == 'on' && $d_type == 'single-choice') {
				    $html .= '<li><label>' . $d_label . '</label><span><i class="icon-check"></i></span></li>';
				} else if ($d_value != '' && $d_type != 'single-choice') {
				    $html .= '<li><label>' . $d_label . '</label><span>' . $d_value . '</span></li>';
				} else {
				    $html .= '<li><label>' . $d_label . '</label><span><i class="icon-minus"></i></span></li>';
				}
			    }
			}
			// end foreach
		    }
		    // end of Dynamic fields
		    // other Features

		    $html .= '
					</ul>
					</div>
					</div>
					</div>
					</div>';
		}
		$html .= '
				</div>
				</li>';
	    }
	    echo force_balance_tags($html);
	}

	/**
	 * Load Memberships and Payment
	 * @return markup
	 */
	public function restaurant_packages() {
	    global $foodbakery_plugin_options, $restaurant_add_counter;

	    $html = '';

	    $restaurant_up_visi = 'block';
	    $restaurant_hide_btn = 'none';

	    $get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
		$restaurant_up_visi = 'none';
		$restaurant_hide_btn = 'inline-block';
	    }

	    $show_li = false;
	    $show_pgt = false;

	    $foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : '';
	    $foodbakery_currency_sign = isset($foodbakery_plugin_options['foodbakery_currency_sign']) ? $foodbakery_plugin_options['foodbakery_currency_sign'] : '$';

	    if ($foodbakery_free_restaurants_switch != 'on') {

		// subscribed packages list
		//$subscribed_active_pkgs = $this->restaurant_user_subscribed_packages();
		$subscribed_active_pkgs = '';

		if (isset($_GET['package_id']) && $_GET['package_id'] != '') {
		    $subscribed_active_pkgs = '';
		    $buying_pkg_id = $_GET['package_id'];
		}
		$new_pkg_btn_visibility = 'none';
		$new_pkgs_visibility = 'block';
		if ($subscribed_active_pkgs) {
		    $new_pkg_btn_visibility = 'block';
		    $new_pkgs_visibility = 'none';
		}

		// Memberships
		$packages_list = '';
		$args = array('posts_per_page' => '-1', 'post_type' => 'packages', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
		$cust_query = get_posts($args);
		if (is_array($cust_query) && sizeof($cust_query) > 0) {
		    $opts_counter = 1;
		    $packages_list_opts = '<div class="all-pckgs-sec">';
		    foreach ($cust_query as $package_post) {
			if (isset($package_post->ID)) {
			    $show_li = true;
			    $packg_title = $package_post->ID != '' ? get_the_title($package_post->ID) : '';
			    $package_type = get_post_meta($package_post->ID, 'foodbakery_package_type', true);
			    $package_price = get_post_meta($package_post->ID, 'foodbakery_package_price', true);
			    $pckg_color = '';
			    if (isset($buying_pkg_id) && $buying_pkg_id == $package_post->ID) {
				$pckg_color = ' style="background-color: #b7b7b7;"';
			    }
			    $packages_list_opts .= '<div class="foodbakery-pkg-holder">';
			    $packages_list_opts .= '<div class="foodbakery-pkg-header"' . $pckg_color . '>';
			    $packages_list_opts .= '
							<div class="pkg-title-price pull-left">
								<div class="radio-holder">
								  <div class="input-radio">
									<input  type="radio" id="package-' . $package_post->ID . '" name="foodbakery_restaurant_package"' . (isset($buying_pkg_id) && $buying_pkg_id == $package_post->ID ? ' checked="checked"' : '') . ' value="' . $package_post->ID . '">
								        <input  type="hidden" id="package-' . $package_post->ID . '" name="' . $package_post->ID . '" value="' . $package_type . '">
									<label class="pkg-title" for="package-' . $package_post->ID . '">' . $packg_title . '</label>
								  </div>
								</div>
								<span class="pkg-price">' . sprintf(esc_html__('Price: %s', 'foodbakery'), foodbakery_get_currency($package_price, true)) . '</span>
							</div>
							<div class="pkg-detail-btn pull-right">
								<a href="javascript:void(0);" class="foodbakery-dev-detail-pkg" data-id="' . $package_post->ID . '">' . esc_html__('Detail', 'foodbakery') . '</a>
							</div>';
			    $packages_list_opts .= '</div>';
			    $packages_list_opts .= $this->new_package_info($package_post->ID);
			    $packages_list_opts .= '</div>';
			    $opts_counter ++;
			}
		    }
		    $packages_list_opts .= '</div>';

		    $packages_list .= '<div class="packages-main-holder">';

		    if ($subscribed_active_pkgs) {
			$packages_list .= '
						<div id="purchased-package-head-' . $restaurant_add_counter . '" class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
							<div class="element-title">
								<h4>' . esc_html__('Purchased Memberships', 'foodbakery') . '</h4>
							</div>
						</div>';
		    }

		    $packages_list .= '
					<div id="buy-package-head-' . $restaurant_add_counter . '" style="display:' . $new_pkgs_visibility . ';" class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
						<div class="element-title">
							<h4>' . esc_html__('Buy Membership', 'foodbakery') . '</h4>
						</div>
					</div>';
		    if (!is_user_logged_in()) {
			$packages_list .= '<input type="checkbox" checked="checked" style="display:none;" name="foodbakery_restaurant_new_package_used">';
		    }
		    if (true === Foodbakery_Member_Permissions::check_permissions('packages')) {
			if ($subscribed_active_pkgs) {
			    $packages_list .= '
							<div class="buy-new-pakg-actions">
								<input type="checkbox" style="display:none;" id="foodbakery-dev-new-pkg-checkbox-' . $restaurant_add_counter . '" name="foodbakery_restaurant_new_package_used">
								<label for="new-pkg-btn-' . $restaurant_add_counter . '">
									<a id="foodbakery-dev-new-pkg-btn-' . $restaurant_add_counter . '" class="dir-switch-packges-btn" data-id="' . $restaurant_add_counter . '" href="javascript:void(0);">' . esc_html__('Buy New Membership', 'foodbakery') . '</a>
								</label>
								<a data-id="' . $restaurant_add_counter . '" style="display:' . $restaurant_hide_btn . ';" href="javascript:void(0);" class="foodbakery-dev-cancel-pkg"><i class="icon-cross-out"></i></a>
							</div>';
			} else {
			    $packages_list .= '<input type="checkbox" checked="checked" style="display:none;" name="foodbakery_restaurant_new_package_used">';
			    $packages_list .= '
							<div class="buy-new-pakg-actions" style="display:' . $restaurant_hide_btn . ';">
								<a data-id="' . $restaurant_add_counter . '" href="javascript:void(0);" class="foodbakery-dev-cancel-pkg"><i class="icon-cross-out"></i></a>
							</div>';
			}
		    }

		    $packages_list .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		    if ($subscribed_active_pkgs) {
			$packages_list .= '<div id="purchased-packages-' . $restaurant_add_counter . '" class="dir-purchased-packages">' . $subscribed_active_pkgs . '</div>';
		    }
		    $packages_list .= '<div id="new-packages-' . $restaurant_add_counter . '" style="display:' . $new_pkgs_visibility . ';" class="dir-new-packages">' . $packages_list_opts . '</div>';
		    $packages_list .= '</div>';
		    $packages_list .= '</div>';
		}
	    } else {
		$html .= '
				<li>' . esc_html__('Registering restaurant is free. Continue with next.', 'foodbakery') . '</li>';
	    }

	    if ($show_li) {
		$html .= '
				<li id="restaurant-packages-sec-' . $restaurant_add_counter . '" style="display: ' . $restaurant_up_visi . ';">
					<div class="row">
						' . $packages_list . '
					</div>
				</li>';
	    }
	    echo force_balance_tags($html);
	}

	/**
	 * field container size
	 * @return class
	 */
	public function field_size_class($size = '') {
	    switch ($size) {
		case('large'):
		    $class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
		    break;
		case('medium'):
		    $class = 'col-lg-6 col-md-6 col-sm-12 col-xs-12';
		    break;
		default :
		    $class = 'col-lg-4 col-md-4 col-sm-12 col-xs-12';
		    break;
	    }
	    return apply_filters('foodbakery_front_custom_field_class', $class, $size);
	    // usage :: add_filter('foodbakery_front_custom_field_class', 'my_callback_function', 10, 2);
	}

	/**
	 * Steps after
	 * @return markup
	 */
	public function after_restaurant($html = '') {
	    global $restaurant_add_counter;
	    $restaurant_id = foodbakery_get_input('restaurant_id', 0);
	    $html .= '<li style="display: none;">'
		    . '<input type="hidden" name="form_rand_id" value="' . $restaurant_add_counter . '">'
		    . '<input type="hidden" name="restaurant_id" value="' . $restaurant_id . '">'
		    . '</li>';
	    echo force_balance_tags($html);
	}

	/**
	 * Restaurant Submit Msg
	 * @return markup
	 */
	public function restaurant_submit_msg($msg = '') {

	    $html = '';
	    if ($msg != '') {
		$msg_arr = array('msg' => $msg, 'status' => true);
		echo json_encode($msg_arr);
	    }
	    // echo force_balance_tags( $html );
	}

	/**
	 * Membership Info Field Create
	 * @return markup
	 */
	public function package_info_field_show($info_meta = '', $index = '', $label = '', $value_plus = '') {
	    if (isset($info_meta[$index]['value'])) {
		$value = $info_meta[$index]['value'];

		if ($value != '' && $value != 'on') {
		    $html = '<li><label>' . $label . '</label><span>' . $value . ' ' . $value_plus . '</span></li>';
		} else if ($value != '' && $value == 'on') {
		    $html = '<li><label>' . $label . '</label><span><i class="icon-check"></i></span></li>';
		} else {
		    $html = '<li><label>' . $label . '</label><span><i class="icon-minus"></i></span></li>';
		}

		return $html;
	    }
	}

	/**
	 * Get New Membership info
	 * @return html
	 */
	public function new_package_info($package_id = 0) {
	    global $restaurant_add_counter;
	    $html = '';

	    $packg_title = $package_id != '' ? get_the_title($package_id) : '';
	    $trans_all_meta = get_post_meta($package_id, 'foodbakery_package_data', true);

	    $html .= '<div id="package-detail-' . $package_id . '" style="display:none;" class="package-info-sec restaurant-info-sec">';
	    $html .= '<div class="row">';
	    $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	    $html .= '<ul class="restaurant-pkg-points">';

	    $html .= $this->package_info_field_show($trans_all_meta, 'duration', esc_html__('Membership Duration', 'foodbakery'), esc_html__('Days', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'restaurant_duration', esc_html__('Restaurant Duration', 'foodbakery'), esc_html__('Days', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'number_of_featured_restaurants', esc_html__('Featured Restaurants', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'number_of_top_cat_restaurants', esc_html__('Top Categories Restaurants', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'number_of_services', esc_html__('Number of Services', 'foodbakery'));

	    $html .= $this->package_info_field_show($trans_all_meta, 'number_of_documents', esc_html__('Number of Documents', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'number_of_tags', esc_html__('Number of Tags', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'reviews', esc_html__('Reviews', 'foodbakery'));

	    $html .= $this->package_info_field_show($trans_all_meta, 'phone_number', esc_html__('Phone Number', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'website_link', esc_html__('Website Link', 'foodbakery'));

	    $html .= $this->package_info_field_show($trans_all_meta, 'social_impressions_reach', esc_html__('Social Impressions Reach', 'foodbakery'));
	    $html .= $this->package_info_field_show($trans_all_meta, 'respond_to_reviews', esc_html__('Respond to Reviews', 'foodbakery'));

	    $trans_dynamic_f = get_post_meta($package_id, 'foodbakery_package_fields', true);

	    if (is_array($trans_dynamic_f) && sizeof($trans_dynamic_f) > 0) {
		foreach ($trans_dynamic_f as $trans_dynamic) {
		    if (isset($trans_dynamic['field_type']) && isset($trans_dynamic['field_label']) && isset($trans_dynamic['field_value'])) {
			$d_type = $trans_dynamic['field_type'];
			$d_label = $trans_dynamic['field_label'];
			$d_value = $trans_dynamic['field_value'];

			if ($d_value == 'on' && $d_type == 'single-choice') {
			    $html .= '<li><label>' . $d_label . '</label><span><i class="icon-check"></i></span></li>';
			} else if ($d_value != '' && $d_type != 'single-choice') {
			    $html .= '<li><label>' . $d_label . '</label><span>' . $d_value . '</span></li>';
			} else {
			    $html .= '<li><label>' . $d_label . '</label><span><i class="icon-minus"></i></span></li>';
			}
		    }
		}
		// end foreach
	    }
	    // end of Dynamic fields
	    // other Features
	    $html .= '
			</ul>
			</div>';
	    if (( isset($trans_all_meta['number_of_featured_restaurants']['value']) && $trans_all_meta['number_of_featured_restaurants']['value'] == 'on' ) || ( isset($trans_all_meta['number_of_top_cat_restaurants']['value']) && $trans_all_meta['number_of_top_cat_restaurants']['value'] == 'on' )) {
		$html .= '
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		if ($trans_all_meta['number_of_featured_restaurants']['value'] == 'on') {
		    $html .= '
					<div class="package-featured pakg-switch">
						<span>' . esc_html__('Featured', 'foodbakery') . ' :</span>
						<input id="package-featured-' . $package_id . '" type="checkbox" class="cmn-toggle cmn-toggle-round" name="foodbakery_restaurant_featured">
						<label for="package-featured-' . $package_id . '"></label>
					</div>';
		}
		if ($trans_all_meta['number_of_top_cat_restaurants']['value'] == 'on') {
		    $html .= '
					<div class="package-top-cat pakg-switch">
						<span>' . esc_html__('Top Category', 'foodbakery') . ' :</span>
						<input id="package-top-cat-' . $package_id . '" type="checkbox" class="cmn-toggle cmn-toggle-round" name="foodbakery_restaurant_top_cat">
						<label for="package-top-cat-' . $package_id . '"></label>
					</div>';
		}
		$html .= '
				</div>';
	    }

	    $html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="pgk-action-btns">
					<a href="javascript:void(0);" data-id="' . $package_id . '" class="pkg-choose-btn">' . esc_html__('Choose Membership', 'foodbakery') . '</a>
					<a href="javascript:void(0);" data-id="' . $package_id . '" class="pkg-cancel-btn">' . esc_html__('Cancel', 'foodbakery') . '</a>
				</div>
			</div>
			</div>
			</div>';

	    return apply_filters('foodbakery_restaurant_user_new_package_info', $html, $package_id);
	    // usage :: add_filter('foodbakery_restaurant_user_new_package_info', 'my_callback_function', 10, 2);
	}

	/**
	 * Checking is form submit
	 * @return boolean
	 */
	public function is_form_submit() {

	    if (isset($_POST['foodbakery_restaurant_title'])) {
		return true;
	    }
	    return false;
	}

	/**
	 * checking publisher own post
	 * @return boolean
	 */
	public function is_publisher_restaurant($restaurant_id = '') {
	    global $current_user;
	    $company_id = foodbakery_company_id_form_user_id($current_user->ID);
	    $foodbakery_publisher_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_publisher', true);
	    if (is_user_logged_in() && $company_id == $foodbakery_publisher_id) {
		return true;
	    }
	    return false;
	}

	/**
	 * checking package
	 * @return boolean
	 */
	public function is_package($id = '') {
	    $package = get_post($id);
	    if (isset($package->post_type) && $package->post_type == 'packages') {
		return true;
	    }
	    return false;
	}

	/**
	 * Date plus period
	 * @return date
	 */
	public function date_conv($duration, $format = 'days') {
	    if ($format == "months") {
		$adexp = date('Y-m-d H:i:s', strtotime("+" . absint($duration) . " months"));
	    } else if ($format == "years") {
		$adexp = date('Y-m-d H:i:s', strtotime("+" . absint($duration) . " years"));
	    } else {
		$adexp = date('Y-m-d H:i:s', strtotime("+" . absint($duration) . " days"));
	    }
	    return $adexp;
	}

	/**
	 * Array merge
	 * @return Array
	 */
	public function merge_in_array($array, $value = '', $with_array = true) {
	    $ret_array = '';
	    if (is_array($array) && sizeof($array) > 0 && $value != '') {
		$array[] = $value;
		$ret_array = $array;
	    } else if (!is_array($array) && $value != '') {
		$ret_array = $with_array ? array($value) : $value;
	    }
	    return $ret_array;
	}

    }

    endif;

new Foodbakery_Register_And_Add_Restaurant();
