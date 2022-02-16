<?php

/**
 * Publisher add/edit Restaurant
 *
 */
if (!class_exists('foodbakery_publisher_restaurant_actions')) {

	class foodbakery_publisher_restaurant_actions
	{

		/**
		 * Start construct Functions
		 */
		public function __construct()
		{
			$this->restaurant_action_hooks();
		}

		/**
		 * Restaurant Hooks
		 * @return
		 */
		public function restaurant_action_hooks()
		{
			add_action('foodbakery_restaurant_add', array($this, 'add_edit_restaurant'), 10, 1);
			add_action('foodbakery_restaurant_basic_info', array($this, 'title_description'), 10, 1);
			add_action('foodbakery_contact_info', array($this, 'restaurant_contact_information'), 10, 3);
			add_action('foodbakery_restaurant_user_signup', array($this, 'user_register_fields'), 10, 1);
			add_action('foodbakery_restaurant_type_selection', array($this, 'select_restaurant_type'), 10, 1);
			add_action('foodbakery_restaurant_add_info', array($this, 'restaurant_info'), 10, 1);
			add_filter('foodbakery_restaurant_add_loader', array($this, 'ajax_loader'), 10, 1);
			add_action('foodbakery_restaurant_add_tag_before', array($this, 'restaurant_add_tag_before'), 10);
			add_action('foodbakery_restaurant_add_tag_after', array($this, 'restaurant_add_tag_after'), 10);
			add_action('foodbakery_restaurant_custom_fields', array($this, 'restaurant_custom_fields'), 10);
			add_action('foodbakery_restaurant_add_meta_data', array($this, 'restaurant_meta_data'), 10);
			add_action('foodbakery_restaurant_add_packages', array($this, 'restaurant_packages'), 10);
			add_action('foodbakery_restaurant_add_submit_button', array($this, 'restaurant_submit_button'), 10);
			add_action('foodbakery_restaurant_add_meta_save', array($this, 'restaurant_meta_save'), 11);
			add_action('foodbakery_restaurant_add_save_assignments', array($this, 'restaurant_save_assignments'), 10, 2);
			add_action('foodbakery_restaurant_add_assign_status', array($this, 'restaurant_update_status'), 10, 1);
			add_action('foodbakery_restaurant_assign_trans_meta', array($this, 'restaurant_assign_meta'), 10, 2);
			add_action('foodbakery_restaurant_social_post', array($this, 'social_post_after_activation'), 10, 1);
			add_action('foodbakery_restaurant_menu_cats', array($this, 'restaurant_menu_cats'), 10);
			add_action('wp_ajax_foodbakery_restaurant_load_cf', array($this, 'custom_fields_features'));
			add_action('wp_ajax_nopriv_foodbakery_restaurant_load_cf', array($this, 'custom_fields_features'));
			add_action('wp_ajax_foodbakery_restaurant_off_day_to_list', array($this, 'append_to_book_days_off'));
			add_action('wp_ajax_nopriv_foodbakery_restaurant_off_day_to_list', array($this, 'append_to_book_days_off'));
			add_action('wp_ajax_foodbakery_new_package_info', array($this, 'new_package_info'));
			add_action('wp_ajax_nopriv_foodbakery_new_package_info', array($this, 'new_package_info'));
			add_action('wp_ajax_foodbakery_subs_package_info', array($this, 'subs_package_info'));
			add_action('wp_ajax_nopriv_foodbakery_subs_package_info', array($this, 'subs_package_info'));
			add_action('wp_ajax_foodbakery_restaurant_user_authentication', array($this, 'user_authentication'));
			add_action('wp_ajax_nopriv_foodbakery_restaurant_user_authentication', array($this, 'user_authentication'));
			add_action('wp_ajax_restaurant_add_menu_cat_item', array($this, 'foodbakery_restaurant_menu_cat_item'));
			add_action('wp_ajax_nopriv_restaurant_add_menu_cat_item', array($this, 'foodbakery_restaurant_menu_cat_item'));
			//
			add_action('wp_ajax_restaurant_show_set_settings', array($this, 'restaurant_show_set_settings'));
			add_action('wp_ajax_nopriv_restaurant_show_set_settings', array($this, 'restaurant_show_set_settings'));
			add_action('wp_ajax_restaurant_show_set_location', array($this, 'restaurant_show_set_location'));
			add_action('wp_ajax_nopriv_restaurant_show_set_location', array($this, 'restaurant_show_set_location'));

			add_action('wp_ajax_restaurant_show_set_openclose', array($this, 'restaurant_show_set_openclose'));
			add_action('wp_ajax_nopriv_restaurant_show_set_openclose', array($this, 'restaurant_show_set_openclose'));
			add_action('wp_ajax_restaurant_show_set_menu', array($this, 'restaurant_show_set_menu'));
			add_action('wp_ajax_nopriv_restaurant_show_set_menu', array($this, 'restaurant_show_set_menu'));
			add_action('wp_ajax_restaurant_show_set_membership', array($this, 'restaurant_show_set_membership'));
			add_action('wp_ajax_nopriv_restaurant_show_set_membership', array($this, 'restaurant_show_set_membership'));
			//
			add_action('before_foodbakery_restaurant_add', array($this, 'before_restaurant'), 10, 1);
			add_action('after_foodbakery_restaurant_add', array($this, 'after_restaurant'), 10, 1);


			add_action('wp_ajax_restaurant_remove_menu_cat_item', array($this, 'sa_restaurant_remove_menu_cat_item'));
			add_action('wp_ajax_nopriv_restaurant_remove_menu_cat_item', array($this, 'sa_restaurant_remove_menu_cat_item'));
		}

		/**
		 * add/edit Restaurant
		 * @return markup
		 */
		public function restaurant_contact_information($type_id = '', $foodbakery_id = '')
		{
			global $foodbakery_form_fields, $restaurant_add_counter, $foodbakery_plugin_options;
			if ($type_id == '') {
				$foodbakery_id = foodbakery_get_input('restaurant_id', 0);
			}

			$restaurant_email = get_post_meta($foodbakery_id, 'foodbakery_restaurant_contact_email', true);
			$restaurant_phone = get_post_meta($foodbakery_id, 'foodbakery_restaurant_contact_phone', true);
			$restaurant_web = get_post_meta($foodbakery_id, 'foodbakery_restaurant_contact_web', true);
			$html = '<div class="field-holder">';
			$html .= '<div class="element-title">
						<h4>' . esc_html__('Contact Information', 'foodbakery') . '</h4>
					</div>
					<label>' . esc_html__('Email', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'restaurant_title_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_contact_email',
					'std' => $restaurant_email,
					'desc' => '',
					'extra_atr' => ' placeholder="' . esc_html__('Email', 'foodbakery') . '"',
					'classes' => 'foodbakery-email-field',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
				)
			);
			$html .= '
			</div>
				<div class="field-holder">
				<label>' . esc_html__('Phone', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'restaurant_title_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_contact_phone',
					'std' => $restaurant_phone,
					'desc' => '',
					'classes' => 'foodbakery-number-field',
					'extra_atr' => ' placeholder="' . esc_html__('phone', 'foodbakery') . '"',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
				)
			);
			$html .= '
				</div>
                <div class="field-holder">
					<label>' . esc_html__('Web', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'restaurant_title_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_contact_web',
					'std' => $restaurant_web,
					'desc' => '',
					'classes' => 'foodbakery-url-field',
					'extra_atr' => ' placeholder="' . esc_html__('Web', 'foodbakery') . '"',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
				)
			);

			$html .= '
				</div>';


			return apply_filters('foodbakery_front_restaurant_add_contact_information', $html, $type_id, $foodbakery_id);
		}

		public function restaurant_show_set_settings($die_ret = '')
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;

			$restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : $restaurant_add_counter;

			ob_start();

			do_action('foodbakery_restaurant_add_tag_before');
?>
			<li>
				<?php
				do_action('foodbakery_restaurant_basic_info', '');
				do_action('foodbakery_restaurant_user_signup', '');
				do_action('foodbakery_restaurant_type_selection', '');
				?>
			</li>
			<li>
				<?php do_action('foodbakery_restaurant_add_submit_button'); ?>
				<?php do_action('after_foodbakery_restaurant_add', ''); ?>
			</li>
			<?php
			do_action('foodbakery_restaurant_add_tag_after');

			$html = ob_get_clean();

			if ($die_ret == 1) {
				echo force_balance_tags($html);
			} else {
				echo json_encode(array('html' => $html));
				die;
			}
		}

		public function restaurant_show_set_location($die_ret = '')
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;

			$restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : '';

			ob_start();

			do_action('foodbakery_restaurant_add_tag_before');
			?>
			<li>
				<?php
				$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);

				$restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
				$selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';
				$restaurant_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : 0;

				$location = $this->restaurant_location($restaurant_type_id, $get_restaurant_id);

				echo force_balance_tags($location);
				?>
			</li>
			<li>
				<?php do_action('foodbakery_restaurant_add_submit_button'); ?>
				<?php do_action('after_foodbakery_restaurant_add', ''); ?>
			</li>
			<?php
			$this->hidden_restaurant_title();
			do_action('foodbakery_restaurant_add_tag_after');

			$html = ob_get_clean();
			if ($die_ret == 1) {
				echo force_balance_tags($html);
			} else {
				echo json_encode(array('html' => $html));
				die;
			}
		}

		public function restaurant_show_set_gallery($die_ret = '')
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;

			$restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : '';

			ob_start();

			do_action('foodbakery_restaurant_add_tag_before');
			?>
			<li>
				<?php
				$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);

				$restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
				$selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';
				$restaurant_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : 0;

				$location = $this->restaurant_gallery($restaurant_type_id, $get_restaurant_id);

				echo force_balance_tags($location);
				?>
			</li>
			<li>
				<?php do_action('foodbakery_restaurant_add_submit_button'); ?>
				<?php do_action('after_foodbakery_restaurant_add', ''); ?>
			</li>
			<?php
			$this->hidden_restaurant_title();
			do_action('foodbakery_restaurant_add_tag_after');

			$html = ob_get_clean();
			if ($die_ret == 1) {
				echo force_balance_tags($html);
			} else {
				echo json_encode(array('html' => $html));
				die;
			}
		}

		public function restaurant_show_set_openclose($die_ret = '')
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;

			$restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : '';

			ob_start();

			do_action('foodbakery_restaurant_add_tag_before');
			?>
			<li>
				<?php
				$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);

				$restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
				$selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';
				$restaurant_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : 0;

				$location = $this->restaurant_opening_hours($restaurant_type_id, $get_restaurant_id);


				echo force_balance_tags($location);
				?>
			</li>
			<li>
				<?php do_action('foodbakery_restaurant_add_submit_button'); ?>
				<?php do_action('after_foodbakery_restaurant_add', ''); ?>
			</li>
			<?php
			$this->hidden_restaurant_title();
			do_action('foodbakery_restaurant_add_tag_after');

			$html = ob_get_clean();
			if ($die_ret == 1) {
				echo force_balance_tags($html);
			} else {
				echo json_encode(array('html' => $html));
				die;
			}
		}

		public function restaurant_show_set_menu($die_ret = '')
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;

			$restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : '';

			ob_start();

			do_action('foodbakery_restaurant_add_tag_before');
			?>
			<li>
				<ul class="restaurant-menu-nav nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#menu-cats-items"><?php esc_html_e('Menu Categories', 'foodbakery') ?></a></li>
					<li><a data-toggle="tab" href="#menu-list-items"><?php esc_html_e('Food Items', 'foodbakery') ?></a></li>
				</ul>

				<div class="tab-content">
					<div id="menu-cats-items" class="tab-pane fade in active">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<?php $this->restaurant_menu_cats(); ?>
							</div>
						</div>
					</div>
					<div id="menu-list-items" class="tab-pane fade">
						<?php
						$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);

						$restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
						$selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';
						$restaurant_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : 0;

						$location = $this->restaurant_menu_items($restaurant_type_id, $get_restaurant_id);

						echo force_balance_tags($location);
						?>
					</div>
				</div>
			</li>
			<li>
				<?php do_action('foodbakery_restaurant_add_submit_button'); ?>
				<?php do_action('after_foodbakery_restaurant_add', ''); ?>
			</li>
			<?php
			$this->hidden_restaurant_title();
			do_action('foodbakery_restaurant_add_tag_after');

			$html = ob_get_clean();
			if ($die_ret == 1) {
				echo force_balance_tags($html);
			} else {
				echo json_encode(array('html' => $html));
				die;
			}
		}

		public function restaurant_show_set_membership($die_ret = '')
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;

			$restaurant_add_counter = isset($_POST['_main_counter']) ? $_POST['_main_counter'] : '';

			ob_start();

			do_action('foodbakery_restaurant_add_tag_before');
			?>
			<li>
				<ul class="membership-info-main">
					<?php do_action('foodbakery_restaurant_add_info', ''); ?>
					<?php do_action('foodbakery_restaurant_add_packages'); ?>
				</ul>
			</li>
			<li>
				<?php do_action('foodbakery_restaurant_add_submit_button'); ?>
				<?php do_action('after_foodbakery_restaurant_add', ''); ?>
			</li>
			<?php
			$this->hidden_restaurant_title();
			do_action('foodbakery_restaurant_add_tag_after');

			$html = ob_get_clean();
			if ($die_ret == 1) {
				echo force_balance_tags($html);
			} else {
				echo json_encode(array('html' => $html));
				die;
			}
		}

		public function add_edit_restaurant($params = array())
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;

			extract($params);
			ob_start();
			$restaurant_add_counter = rand(10000000, 99999999);

			$foodbakery_id = foodbakery_get_input('restaurant_id', 0);

			wp_enqueue_script('foodbakery-restaurant-add');
			wp_enqueue_script('foodbakery-restaurant-menus');
			?>
			<div id="foodbakery-dev-posting-main-<?php echo absint($restaurant_add_counter); ?>" class="user-holder" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-plugin-url="<?php echo esc_url(wp_foodbakery::plugin_url()); ?>">
				<?php
				do_action('foodbakery_restaurant_add_meta_save');
				?>
				<form id="foodbakery-dev-restaurant-form-<?php echo absint($restaurant_add_counter); ?>" name="foodbakery-dev-restaurant-form" class="foodbakery-dev-restaurant-form" data-id="<?php echo absint($restaurant_add_counter); ?>" method="post" enctype="multipart/form-data">
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

					if ($can_post_restaurant) {
						$restaurant_tab = isset($_GET['restaurant_tab']) ? $_GET['restaurant_tab'] : '';
						$settings_class = "";
						$location_class = "";
						$openclose_class = "";
						if ($restaurant_tab == 'settings' || $restaurant_tab == '') {
							$settings_class = "active processing";
							$location_class = "";
							$openclose_class = "";
						} else if ($restaurant_tab == 'location') {
							$settings_class = "active";
							$location_class = "active processing";
							$openclose_class = "";
						} else if ($restaurant_tab == 'openclose') {
							$settings_class = "active";
							$location_class = "active";
							$openclose_class = "active processing";
						}
					?>
						<ul class="restaurant-settings-nav progressbar-nav" data-restaurant="<?php echo absint($foodbakery_id) ?>" data-mcounter="<?php echo absint($restaurant_add_counter) ?>">
							<li data-act="settings" class="<?php echo esc_html($settings_class); ?> cond-restaurant-settings"><a href="javascript:void(0);" data-act="settings"><?php esc_html_e('Restaurant Settings', 'foodbakery'); ?></a></li>
							<li data-act="location" class="<?php echo esc_html($location_class); ?> cond-restaurant-settings"><a href="javascript:void(0);" data-act="location"><?php esc_html_e('Location/Map', 'foodbakery'); ?></a></li>

							<li data-act="openclose" class="<?php echo esc_html($openclose_class); ?> cond-restaurant-settings"><a href="javascript:void(0);" data-act="openclose"><?php esc_html_e('Restaurant Open/Close', 'foodbakery'); ?></a></li>


							<?php
							$foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : '';

							if ($foodbakery_free_restaurants_switch != 'on') {
								$args = array('posts_per_page' => '-1', 'post_type' => 'packages', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
								$cust_query = get_posts($args);
								if (is_array($cust_query) && sizeof($cust_query) > 0) {
							?>
									<!--									<li<?php echo ($restaurant_tab == 'membership' ? ' class="active"' : '') ?>><a href="javascript:void(0);" class="cond-restaurant-settings" data-act="membership"><?php esc_html_e('Memberships', 'foodbakery'); ?></a></li>-->
							<?php
								}
							}
							?>
						</ul>

						<div id="restaurant-sets-holder" class="form-fields-set">
							<?php
							if ($restaurant_tab == 'location') {
								$this->restaurant_show_set_location(1);
							} else if ($restaurant_tab == 'openclose') {
								$this->restaurant_show_set_openclose(1);
							} else if ($restaurant_tab == 'menu') {
							} else if ($restaurant_tab == 'membership') {
							} else {
								$this->restaurant_show_set_settings(1);
							}
							?>
						</div>
					<?php
					} else {
						esc_html_e('You already have a restaurant.', 'foodbakery');
					}
					?>
				</form>
			</div>
			<?php
			$html = ob_get_clean();
			if (isset($return_html) && $return_html == true) {
				return $html;
			} else {
				echo force_balance_tags($html);
			}
		}

		/**
		 * Basic Info
		 * @return markup
		 */
		public function hidden_restaurant_title()
		{
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
		 * Basic Info
		 * @return markup
		 */
		public function title_description($html = '')
		{
			global $foodbakery_form_fields, $restaurant_add_counter, $foodbakery_plugin_options;
			$foodbakery_restaurant_title = '';
			$restaurant_slug = '';
			$foodbakery_restaurant_desc = '';
			$foodbakery_minimum_order_value = '';
			$foodbakery_delivery_fee = '';
			$foodbakery_pickup_fee = '';

			$restaurant_phone = '';
			$restaurant_manager_name = '';
			$restaurant_manager_phone = '';
			$restaurant_email = '';
			$restaurant_table_booking = '';
			$restaurant_pickup_delivery = '';
			$foodbakery_maximum_order_value = '';
			$foodbakery_minimum_pickup_order_value = '';
			$foodbakery_maximum_pickup_order_value = '';
			$restaurant_disable_cash = 'no';
			$foodbakery_delivery_time = '';
			$restaurant_pre_order = '';
			$foodbakery_restaurant_pickup_time = '';
			$foodbakery_maximum_delivary_area_value = 1;
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$foodbakery_restaurant_title = get_the_title($get_restaurant_id);
				$restaurant_post = get_post($get_restaurant_id);
				$restaurant_slug = $restaurant_post->post_name;
				$foodbakery_restaurant_desc = $this->restaurant_post_content($get_restaurant_id);
				$foodbakery_minimum_order_value = get_post_meta($get_restaurant_id, 'foodbakery_minimum_order_value', true);
				$foodbakery_delivery_fee = get_post_meta($get_restaurant_id, 'foodbakery_delivery_fee', true);
				$foodbakery_pickup_fee = get_post_meta($get_restaurant_id, 'foodbakery_pickup_fee', true);

				$restaurant_phone = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_contact_phone', true);
				$restaurant_manager_name = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_manager_name', true);
				$restaurant_manager_phone = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_manager_phone', true);
				$restaurant_email = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_contact_email', true);
				$restaurant_table_booking = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_table_booking', true);
				$restaurant_pickup_delivery = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);
				$foodbakery_maximum_order_value = get_post_meta($get_restaurant_id, 'foodbakery_maximum_order_value', true);
				$foodbakery_minimum_pickup_order_value = get_post_meta($get_restaurant_id, 'foodbakery_minimum_pickup_order_value', true);
				$foodbakery_maximum_pickup_order_value = get_post_meta($get_restaurant_id, 'foodbakery_maximum_pickup_order_value', true);
				$restaurant_disable_cash = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_disable_cash', true);
				$foodbakery_delivery_time = get_post_meta($get_restaurant_id, 'foodbakery_delivery_time', true);
				$restaurant_pre_order = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_pre_order', true);
				$foodbakery_restaurant_pickup_time = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_pickup_time', true);
				$foodbakery_maximum_delivary_area_value = get_post_meta($get_restaurant_id, 'foodbakery_maximum_delivary_area', true);;
			}

			$restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
			$selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';
			$restaurant_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : 0;

			$html .= '<div class="row">';
			$foodbakery_restaurant_announce_title = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_title']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_title'] : '';
			$foodbakery_restaurant_announce_description = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_description']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_description'] : '';
			ob_start();
			if ((isset($foodbakery_restaurant_announce_title) && $foodbakery_restaurant_announce_title <> '') || (isset($foodbakery_restaurant_announce_description) && $foodbakery_restaurant_announce_description <> '')) {
				do_action('before_foodbakery_restaurant_add', '');
			}
			$html .= ob_get_clean();
			$html .= '</div>';

			$html .= $this->restaurant_featured_image($restaurant_type_id, $get_restaurant_id);
			$html .= $this->restaurant_cover_image($restaurant_type_id, $get_restaurant_id);

			$html .= '<div class="row">';

			$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="field-holder">
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

			$html .= '</div>'
				. '</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="field-holder">
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
			$html .= '</div>
				</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
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
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
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
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
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
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Information *', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_textarea_render(
				array(
					'name' => '',
					'id' => 'restaurant_desc_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_desc',
					'classes' => 'foodbakery-dev-req-field foodbakery_editor',
					'std' => $foodbakery_restaurant_desc,
					'description' => '',
					'return' => true,
					'foodbakery_editor' => true,
					'force_std' => true,
					'hint' => ''
				)
			);
			$html .= '</div>'
				. '</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Restaurant Pre-Order', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_select_render(
				array(
					'id' => 'restaurant_pre_order_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_pre_order',
					'std' => $restaurant_pre_order,
					'desc' => '',
					'classes' => 'chosen-select',
					'return' => true,
					'force_std' => true,
					'options' => array(
						'no' => esc_html__('No', 'foodbakery'),
						'yes' => esc_html__('Yes', 'foodbakery'),
					),
					'hint_text' => '',
				)
			);
			$html .= '</div> </div>';
			$html = apply_filters('foodbakery_imran_sound_field_frntend', $html, $get_restaurant_id);
			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Table Booking', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_select_render(
				array(
					'id' => 'restaurant_table_booking_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_table_booking',
					'std' => $restaurant_table_booking,
					'desc' => '',
					'classes' => 'chosen-select',
					'return' => true,
					'force_std' => true,
					'options' => array(
						'yes' => esc_html__('Yes', 'foodbakery'),
						'no' => esc_html__('No', 'foodbakery'),
					),
					'hint_text' => '',
				)
			);
			$html .= '</div>
                    </div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Delivery/Pickup', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_select_render(
				array(
					'id' => 'restaurant_table_booking_' . $restaurant_add_counter,
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
			$html .= '</div>
                    </div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Minimum Delivery Order', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'minimum_order_value_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_minimum_order_value',
					'std' => $foodbakery_minimum_order_value,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 15', 'foodbakery') . '"',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Maximum Delivery Order', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'maximum_order_value_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_maximum_order_value',
					'std' => $foodbakery_maximum_order_value,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 200', 'foodbakery') . '"',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Minimum Pickup Order', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'minimum_pickup_order_value_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_minimum_pickup_order_value',
					'std' => $foodbakery_minimum_pickup_order_value,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 15', 'foodbakery') . '"',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Maximum delivary area (KM)', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'maximum_delivary_area_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_maximum_delivary_area',
					'std' => $foodbakery_maximum_delivary_area_value,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 15', 'foodbakery') . '"',
				)
			);

			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Maximum Pickup Order', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'maximum_pickup_order_value_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_maximum_pickup_order_value',
					'std' => $foodbakery_maximum_pickup_order_value,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 200', 'foodbakery') . '"',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Delivery Fee', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'delivery_fee' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_delivery_fee',
					'std' => $foodbakery_delivery_fee,
					'desc' => '',
					'classes' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 15', 'foodbakery') . '"',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('PickUp Fee', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'pickup_fee' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_pickup_fee',
					'std' => $foodbakery_pickup_fee,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 15', 'foodbakery') . '"',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Disable cash on Delivery?', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_select_render(
				array(
					'id' => 'restaurant_disable_cash_' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_disable_cash',
					'std' => $restaurant_disable_cash,
					'desc' => '',
					'classes' => 'chosen-select',
					'return' => true,
					'force_std' => true,
					'options' => array(
						'no' => esc_html__('No', 'foodbakery'),
						'yes' => esc_html__('Yes', 'foodbakery'),
					),
					'hint_text' => '',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('Delivery Time', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'delivery_time' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_delivery_time',
					'std' => $foodbakery_delivery_time,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 10', 'foodbakery') . '"',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="field-holder">
			<label>' . esc_html__('PickUp Time', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'restaurant_pickup_time' . $restaurant_add_counter,
					'cust_name' => 'foodbakery_restaurant_pickup_time',
					'std' => $foodbakery_restaurant_pickup_time,
					'desc' => '',
					'classes' => '',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
					'extra_atr' => ' placeholder="' . esc_html__('i.e 15', 'foodbakery') . '"',
				)
			);
			$html .= '</div>
			</div>';

			$html .= '</div><!-- End Row -->
			</div><!-- End columns -->
			</div>';


			$html .= $this->select_restaurant_type();
			$html .= '<div class="foodbakery-dev-appended-cats">' . $this->restaurant_categories($restaurant_type_id, $get_restaurant_id) . '</div>';
			$html .= $this->user_register_fields();
			$html .= $this->restaurant_tags($restaurant_type_id, $get_restaurant_id);


			echo force_balance_tags($html);
		}

		/**
		 * User Register Fields
		 * @return markup
		 */
		public function user_register_fields($html = '')
		{
			global $restaurant_add_counter;

			$is_updating = false;
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$is_updating = true;
			}

			if (!$is_updating && !is_user_logged_in()) {
				$html .= '
				<li id="foodbakery-dev-user-signup-' . $restaurant_add_counter . '">
				<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<h4>' . esc_html__('Signup Fields', 'foodbakery') . '</h4>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="field-holder">
						<label>' . esc_html__('Username *', 'foodbakery') . '</label>
						<input type="text" placeholder="' . esc_html__('Username', 'foodbakery') . '" data-id="' . $restaurant_add_counter . '" data-type="username" name="foodbakery_restaurant_username" class="foodbakery-dev-username foodbakery-dev-req-field">
						<span class="field-info foodbakery-dev-username-check"></span>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="field-holder">
						<label>' . esc_html__('Email *', 'foodbakery') . '</label>
						<input type="text" placeholder="' . esc_html__('Email address', 'foodbakery') . '" data-id="' . $restaurant_add_counter . '" data-type="useremail" name="foodbakery_restaurant_user_email" class="foodbakery-dev-user-email foodbakery-dev-req-field">
						<span class="field-info foodbakery-dev-useremail-check"></span>
					</div>
				</div>
				</div>
				</li>';
			}
			return force_balance_tags($html);
		}

		/**
		 * Select Restaurant Type
		 * @return markup
		 */
		public function select_restaurant_type($html = '')
		{
			global $foodbakery_form_fields, $restaurant_add_counter;
			$selected_type = '';
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$selected_type = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_type', true);
			} else {
				$types_args = array('posts_per_page' => '-1', 'post_type' => 'restaurant-type', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
				$cust_query = get_posts($types_args);
				$selected_type = isset($cust_query[0]->post_name) ? $cust_query[0]->post_name : '';
			}

			$types_options = '';
			$types_args = array('posts_per_page' => '-1', 'post_type' => 'restaurant-type', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
			$cust_query = get_posts($types_args);
			$types_options .= '<option value="">' . esc_html__('Select Type', 'foodbakery') . '</option>';
			if (is_array($cust_query) && sizeof($cust_query) > 0) {
				$type_counter = 1;
				foreach ($cust_query as $type_post) {
					$option_selected = '';
					if ($selected_type != '' && $selected_type == $type_post->post_name) {
						$option_selected = ' selected="selected"';
					} else if ($type_counter == 1) {
					}
					$types_data[$type_post->post_name] = get_the_title($type_post->ID);
					$types_options .= '<option' . $option_selected . ' value="' . $type_post->post_name . '">' . get_the_title($type_post->ID) . '</option>' . "\n";
					$type_counter++;
				}
			}
			$html .= '
			<div class="row">';

			$restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
			$selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';

			$html .= '
			<input type="hidden" name="foodbakery_restaurant_type" value="' . $selected_type . '">
			<div id="foodbakery-dev-cf-con-' . absint($restaurant_add_counter) . '">';
			ob_start();
			do_action('foodbakery_restaurant_custom_fields');
			$html .= ob_get_clean();
			$html .= '
			</div>
			</div>';
			return force_balance_tags($html);
		}

		/**
		 * Info Icon Check
		 * @return markup
		 */
		public function restaurant_info_icon_check($info_el = '')
		{
			$info_icon = $info_el == 'on' ? '<i class="icon-check"></i>' : '<i class="icon-minus"></i>';
			return $info_icon;
		}

		/**
		 * Info Field Create
		 * @return markup
		 */
		public function restaurant_info_field_show($info_meta = '', $index = '')
		{
			if (isset($info_meta[$index]) && isset($info_meta[$index]['key']) && isset($info_meta[$index]['label']) && isset($info_meta[$index]['value'])) {
				$key = isset($info_meta[$index]['key']) ? $info_meta[$index]['key'] : '';
				$label = isset($info_meta[$index]['label']) ? $info_meta[$index]['label'] : '';
				$value = isset($info_meta[$index]['value']) ? $info_meta[$index]['value'] : '';
				if ($value != '' && $value != 'on') {
					$html = '<li><label>' . esc_html__($label, 'foodbakery') . '</label><span>' . esc_html__($value, 'foodbakery') . '</span></li>';
				} else if ($value != '' && $value == 'on') {
					$html = '<li><label>' . esc_html__($label, 'foodbakery') . '</label><span><i class="icon-check"></i></span></li>';
				} else {
					$html = '<li><label>' . esc_html__($label, 'foodbakery') . '</label><span><i class="icon-minus"></i></span></li>';
				}

				return $html;
			}
		}

		/**
		 * Select Restaurant Type
		 * @return markup
		 */
		public function restaurant_info($html = '')
		{
			global $restaurant_add_counter;
			$selected_type = '';
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			$current_user = wp_get_current_user();
			$publisher_id = foodbakery_company_id_form_user_id($current_user->ID);
			if ($get_restaurant_id <= 0) {
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
				if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
					$get_restaurant_id = $pub_restaurant[0];
				}
			}
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$restaurant_status = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_status', true);
				$restaurant_post_on = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_posted', true);
				$restaurant_post_expiry = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_expired', true);

				$restaurant_post_expiry_date = date('d-m-Y', $restaurant_post_expiry);
				$restaurant_post_on_date = date('d-m-Y', $restaurant_post_on);

				$restaurant_hide_btn = 'block';
				if (isset($_GET['package_id']) && $_GET['package_id'] != '') {
					$restaurant_hide_btn = 'none';
				}

				$html .= '
				<li id="restaurant-info-sec-' . $restaurant_add_counter . '" class="restaurant-info-holder" style="display : ' . $restaurant_hide_btn . ';">
				<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<h4>' . esc_html__('Current Membership', 'foodbakery') . '</h4>
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
					$html .= '<li><label>' . esc_html__('Expiry', 'foodbakery') . '</label><span class="info-expiry-date">' . date_i18n(get_option('date_format'), $restaurant_post_expiry) . '</span></li>';
					$html .= '<li><label>' . esc_html__('Status', 'foodbakery') . '</label><span ' . $active_class . '>' . $restaurant_status_str . '</span></li>';
					$html .= '<li><label>' . esc_html__('Featured', 'foodbakery') . '</label><span>' . $restaurant_is_featured . '</span></li>';
					$html .= '<li><label>' . esc_html__('Top Category', 'foodbakery') . '</label><span>' . $restaurant_is_top_cat . '</span></li>';
					$html .= $this->restaurant_info_field_show($trans_all_meta, 0);
					$html .= $this->restaurant_info_field_show($trans_all_meta, 1);
					$html .= $this->restaurant_info_field_show($trans_all_meta, 2);
					$html .= $this->restaurant_info_field_show($trans_all_meta, 3);
					$html .= $this->restaurant_info_field_show($trans_all_meta, 4);
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
		 * Loading custom fields and features
		 * while selecting type
		 * @return markup
		 */
		public function custom_fields_features()
		{
			global $restaurant_add_counter;
			$cus_fields_html = '';
			$main_append_html = '';
			$restaurant_add_counter = foodbakery_get_input('restaurant_add_counter', '');
			$select_type = foodbakery_get_input('select_type', '');
			if ($select_type != '') {
				$restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$select_type", 'post_status' => 'publish'));
				$restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

				$cus_fields_html = $this->custom_fields($restaurant_type_id);
				$main_append_html = $this->restaurant_categories($restaurant_type_id);

				$main_append_html .= $this->restaurant_featured_image($restaurant_type_id);
				$main_append_html .= $this->restaurant_cover_image($restaurant_type_id);

				$main_append_html .= $this->restaurant_location($restaurant_type_id);

				$main_append_html .= $this->restaurant_tags($restaurant_type_id);

				$main_append_html .= $this->restaurant_menu_items($restaurant_type_id);
				$main_append_html .= $this->restaurant_menu_order($restaurant_type_id);
				$main_append_html .= $this->restaurant_opening_hours($restaurant_type_id);
			}
			echo json_encode(array('cf_html' => $cus_fields_html, 'main_html' => $main_append_html));
			die;
		}

		/**
		 * Ajax Loader
		 * @return markup
		 */
		public function ajax_loader($echo = true)
		{
			global $restaurant_add_counter;
			$html = '
			<div id="foodbakery-dev-loader-' . absint($restaurant_add_counter) . '" class="foodbakery-loader"></div>
			<div id="foodbakery-dev-act-msg-' . absint($restaurant_add_counter) . '" class="foodbakery-loader"></div>';
			if ($echo) {
				echo force_balance_tags($html);
			} else {
				return force_balance_tags($html);
			}
		}

		/**
		 * field container size
		 * @return class
		 */
		public function field_size_class($size = '')
		{
			switch ($size) {
				case ('large'):
					$class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
					break;
				case ('medium'):
					$class = 'col-lg-6 col-md-6 col-sm-12 col-xs-12';
					break;
				default:
					$class = 'col-lg-4 col-md-4 col-sm-12 col-xs-12';
					break;
			}
			return apply_filters('foodbakery_front_custom_field_class', $class, $size);
			// usage :: add_filter('foodbakery_front_custom_field_class', 'my_callback_function', 10, 2);
		}

		/**
		 * Custom Fields
		 * @return markup
		 */
		public function custom_fields($type_id = '', $foodbakery_id = '')
		{
			global $foodbakery_form_fields;
			$html = '';
			$foodbakery_cus_fields = get_post_meta($type_id, "foodbakery_restaurant_type_cus_fields", true);
			if (is_array($foodbakery_cus_fields) && sizeof($foodbakery_cus_fields) > 0) {
				foreach ($foodbakery_cus_fields as $cus_field) {
					$cus_type = isset($cus_field['type']) ? $cus_field['type'] : '';
					switch ($cus_type) {
						case ('text'):
							$cus_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cus_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cus_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cus_required = isset($cus_field['required']) && $cus_field['required'] == 'on' ? ' foodbakery-dev-req-field' : '';
							$cus_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							$cus_size = isset($cus_field['field_size']) ? $cus_field['field_size'] : '';
							if ($foodbakery_id != '') {
								$cus_default_val = get_post_meta((int) $foodbakery_id, "$cus_meta_key", true);
							}

							if ($cus_meta_key != '') {
								$html .= '
								<div class="' . $this->field_size_class($cus_size) . '">
									<div class="field-holder">
									<label>' . esc_attr($cus_label) . '</label>';
								$cus_opt_array = array(
									'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
									'desc' => '',
									'classes' => $cus_required,
									'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
									'std' => $cus_default_val,
									'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
									'cus_field' => true,
									'return' => true,
								);

								if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
									$cus_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
								}

								if (isset($cus_field['required']) && $cus_field['required'] == 'yes') {

									$cus_opt_array['classes'] = 'foodbakery-dev-req-field';
								}
								$html .= $foodbakery_form_fields->foodbakery_form_text_render($cus_opt_array);

								if ($cus_help_txt <> '') {
									$html .= '<span class="cs-caption">' . $cus_help_txt . '</span>';
								}
								$html .= '</div></div>';
							}
							break;
						case ('textarea'):
							$cus_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cus_rows = isset($cus_field['rows']) ? $cus_field['rows'] : '';
							$cus_cols = isset($cus_field['cols']) ? $cus_field['cols'] : '';
							$cus_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cus_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cus_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' foodbakery-dev-req-field' : '';
							$cus_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							$cus_size = isset($cus_field['field_size']) ? $cus_field['field_size'] : '';
							if ($foodbakery_id != '') {
								$cus_default_val = get_post_meta((int) $foodbakery_id, "$cus_meta_key", true);
							}
							if ($cus_meta_key != '') {
								$html .= '
										<div class="' . $this->field_size_class($cus_size) . '">
										<div class="field-holder">
											<label>' . esc_attr($cus_label) . '</label>';

								$cus_opt_array = array(
									'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
									'desc' => '',
									'classes' => $cus_required,
									'extra_atr' => 'rows="' . $cus_rows . '" cols="' . $cus_cols . '"',
									'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
									'std' => $cus_default_val,
									'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
									'cus_field' => true,
									'return' => true,
								);

								if (isset($cus_field['required']) && $cus_field['required'] == 'yes') {

									$cus_opt_array['classes'] = 'foodbakery-dev-req-field';
								}

								$html .= $foodbakery_form_fields->foodbakery_form_textarea_render($cus_opt_array);

								if ($cus_help_txt <> '') {
									$html .= '<span class="cs-caption">' . $cus_help_txt . '</span>';
								}
								$html .= '
									</div>
								</div>';
							}
							break;
						case ('dropdown'):
							$cus_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cus_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cus_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cus_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' foodbakery-dev-req-field' : '';
							$cus_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							$cus_size = isset($cus_field['field_size']) ? $cus_field['field_size'] : '';

							if ($foodbakery_id != '') {
								$cus_default_val = get_post_meta((int) $foodbakery_id, "$cus_meta_key", true);
							}
							$cus_dr_name = ' name="foodbakery_cus_field[' . sanitize_html_class($cus_meta_key) . ']"';
							$cus_dr_mult = '';
							if (isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'on') {
								$cus_dr_name = ' name="foodbakery_cus_field[' . sanitize_html_class($cus_meta_key) . '][]"';
								$cus_dr_mult = ' multiple="multiple"';
							}

							$a_options = array();

							$cus_options_mark = '';

							if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value']) && sizeof($cus_field['options']['value']) > 0) {
								if (isset($cus_field['first_value']) && $cus_field['first_value'] != '') {
									$cus_options_mark .= '<option value="">' . $cus_field['first_value'] . '</option>';
								}
								$cus_opt_counter = 0;
								foreach ($cus_field['options']['value'] as $cus_option) {

									if (isset($cus_field['']) && $cus_field['post_multi'] == 'on') {

										$cus_checkd = '';
										if (is_array($cus_default_val) && in_array($cus_option, $cus_default_val)) {
											$cus_checkd = ' selected="selected"';
										}
									} else {
										$cus_checkd = $cus_option == $cus_default_val ? ' selected="selected"' : '';
									}

									$cus_opt_label = $cus_field['options']['label'][$cus_opt_counter];
									$cus_options_mark .= '<option value="' . $cus_option . '"' . $cus_checkd . '>' . $cus_opt_label . '</option>';
									$cus_opt_counter++;
								}
							}

							if ($cus_meta_key != '') {
								$html .= '
					<div class="' . $this->field_size_class($cus_size) . '">
					<div class="field-holder">
					<label>' . esc_attr($cus_label) . '</label>';

								$cus_opt_array = array(
									'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
									'desc' => '',
									'classes' => 'chosen-select' . $cus_required,
									'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
									'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
									'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
									'options' => $cus_options_mark,
									'options_markup' => true,
									'cus_field' => true,
									'return' => true,
								);

								if (isset($cus_field['first_value']) && $cus_field['first_value'] != '') {
									$cus_opt_array['extra_atr'] = ' data-placeholder="' . $cus_field['first_value'] . '"';
								}

								if (isset($cus_field['required']) && $cus_field['required'] == 'yes') {

									$cus_opt_array['classes'] = 'chosen-select form-control foodbakery-dev-req-field';
								}
								if (isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'on') {
									$html .= $foodbakery_form_fields->foodbakery_form_multiselect_render($cus_opt_array);
								} else {
									$html .= $foodbakery_form_fields->foodbakery_form_select_render($cus_opt_array);
								}
								if ($cus_help_txt <> '') {
									$html .= '<span class="cs-caption">' . $cus_help_txt . '</span>';
								}
								$html .= '
								</div>
								</div>';
							}
							break;
						case ('date'):
							$cus_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cus_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cus_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cus_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' foodbakery-dev-req-field' : '';
							$cus_format = isset($cus_field['date_format']) ? $cus_field['date_format'] : 'd-m-Y';
							$cus_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							$cus_size = isset($cus_field['field_size']) ? $cus_field['field_size'] : '';
							if ($foodbakery_id != '') {
								$cus_default_val = get_post_meta((int) $foodbakery_id, "$cus_meta_key", true);
							}

							if ($cus_meta_key != '') {
								$html .= '
								<div class="' . $this->field_size_class($cus_size) . '">
								<div class="field-holder">
								<label>' . esc_attr($cus_label) . '</label>';

								$cus_opt_array = array(
									'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
									'desc' => '',
									'classes' => $cus_required . ' foodbakery-date-field',
									'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
									'std' => $cus_default_val,
									'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
									'cus_field' => true,
									'format' => $cus_format,
									'return' => true,
								);

								if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
									$cus_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
								}

								$html .= $foodbakery_form_fields->foodbakery_form_date_render($cus_opt_array);

								if ($cus_help_txt <> '') {
									$html .= '<span class="cs-caption">' . $cus_help_txt . '</span>';
								}
								$html .= '
									</div>
								</div>';
							}
							break;
						case ('email'):
							$cus_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cus_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cus_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cus_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' foodbakery-dev-req-field' : '';
							$cus_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							$cus_size = isset($cus_field['field_size']) ? $cus_field['field_size'] : '';
							if ($foodbakery_id != '') {
								$cus_default_val = get_post_meta((int) $foodbakery_id, "$cus_meta_key", true);
							}

							if ($cus_meta_key != '') {
								$html .= '
								<div class="' . $this->field_size_class($cus_size) . '">
								<div class="field-holder">
								<label>' . esc_attr($cus_label) . '</label>';
								$cus_opt_array = array(
									'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
									'desc' => '',
									'classes' => $cus_required . ' foodbakery-email-field',
									'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
									'std' => $cus_default_val,
									'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
									'cus_field' => true,
									'return' => true,
								);

								if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
									$cus_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
								}

								$html .= $foodbakery_form_fields->foodbakery_form_text_render($cus_opt_array);
								if ($cus_help_txt <> '') {
									$html .= '<span class="cs-caption">' . $cus_help_txt . '</span>';
								}
								$html .= '
									</div>
								</div>';
							}
							break;
						case ('url'):
							$cus_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cus_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cus_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cus_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' foodbakery-dev-req-field' : '';
							$cus_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							$cus_size = isset($cus_field['field_size']) ? $cus_field['field_size'] : '';
							if ($foodbakery_id != '') {
								$cus_default_val = get_post_meta((int) $foodbakery_id, "$cus_meta_key", true);
							}

							if ($cus_meta_key != '') {
								$html .= '
								<div class="' . $this->field_size_class($cus_size) . '">
									<div class="field-holder">
									<label>' . esc_attr($cus_label) . '</label>';

								$cus_opt_array = array(
									'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
									'desc' => '',
									'classes' => $cus_required . ' foodbakery-url-field',
									'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
									'std' => $cus_default_val,
									'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
									'cus_field' => true,
									'return' => true,
								);

								if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
									$cus_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
								}

								$html .= $foodbakery_form_fields->foodbakery_form_text_render($cus_opt_array);

								if ($cus_help_txt <> '') {
									$html .= '<span class="cs-caption">' . $cus_help_txt . '</span>';
								}
								$html .= '
									</div>
								</div>';
								break;
							}
						case ('range'):
							$cus_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cus_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cus_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cus_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' foodbakery-dev-req-field' : '';
							$cus_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							$cus_size = isset($cus_field['field_size']) ? $cus_field['field_size'] : '';
							if ($foodbakery_id != '') {
								$cus_default_val = get_post_meta((int) $foodbakery_id, "$cus_meta_key", true);
							}

							if ($cus_meta_key != '') {
								$html .= '
										<div class="' . $this->field_size_class($cus_size) . '">
											<div class="field-holder">
												<label>' . esc_attr($cus_label) . '</label>';

								$cus_opt_array = array(
									'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
									'desc' => '',
									'classes' => $cus_required . ' foodbakery-range-field',
									'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
									'std' => $cus_default_val,
									'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
									'cus_field' => true,
									'extra_atr' => 'data-min="' . $cus_field['min'] . '" data-max="' . $cus_field['max'] . '"',
									'return' => true,
								);

								if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
									$cus_opt_array['extra_atr'] .= ' placeholder="' . $cus_field['placeholder'] . '"';
								}

								$html .= $foodbakery_form_fields->foodbakery_form_text_render($cus_opt_array);

								if ($cus_help_txt <> '') {
									$html .= '<span class="cs-caption">' . $cus_help_txt . '</span>';
								}
								$html .= '
									</div>
								</div>';
							}
							break;
					}
				}
			}
			return apply_filters('foodbakery_front_custom_fields', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_custom_fields', 'my_callback_function', 10, 3);
		}

		public function restaurant_menu_cats()
		{

			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);

			if ($get_restaurant_id <= 0) {
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
				if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
					$get_restaurant_id = $pub_restaurant[0];
				}
			}

			$restaurant_id = $get_restaurant_id;

			$menu_item_counter = rand(111456789, 987654321);
			$html = '';
			$menu_items_list = $this->group_restaurant_menu_cats($restaurant_id);
			$html .= '<div class="element-title">
				<h5>' . esc_html__('Menu Categories', 'foodbakery') . '</h5>
				<div id="menu-cats-loader-' . $menu_item_counter . '" class="restaurant-loader"></div>
				<a id="restaurant-cats-btn-' . $menu_item_counter . '" class="add-menu-item" href="javascript:void(0);" onClick="javascript:foodbakery_add_menu_cat(\'' . $menu_item_counter . '\');">' . esc_html__('Add Menu Category', 'foodbakery') . '</a>
			</div>
		    <div class="form-elements">
			<div id="add-menu-cat-from-' . $menu_item_counter . '" style="display:none;">';
			$html .= $this->foodbakery_restaurant_cat_form($restaurant_id, '', $menu_item_counter, 'add');
			$html .= '   </div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<div class="service-list">
						<div class="menu-items-list-holder">';

			$html .= '<div class="not-found"' . ($menu_items_list == '' ? ' style="display:block;"' : ' style="display:none;"') . '>
					<i class="icon-error"></i>
					<p>' . esc_html__('Sorry! No Menu Category added.', 'foodbakery') . '</p>
				</div>';

			$html .= '       <ul id="restaurant-cats-list-' . $menu_item_counter . '" class="restaurant-menu-cats-list"' . ($menu_items_list != '' ? ' style="display:block;"' : ' style="display:none;"') . '>
								' . $menu_items_list . '
							</ul>';

			$html .= '
					    </div>
					</div>
				    </div>
				</div>
			</div>';
			echo force_balance_tags($html);
		}

		public function foodbakery_restaurant_cat_form($resturent_id = 0,  $get_menu_cat_vals, $menu_item_counter, $doin_action = 'add')
		{
			$form_html = '';
			if ($doin_action == 'edit') {
				$add_btn_txt = esc_html__('Save', 'foodbakery');
				$title_name_value = ' name="menu_cat_title[]" value="' . (isset($get_menu_cat_vals['menu_cat_title']) ? $get_menu_cat_vals['menu_cat_title'] : '') . '"';
				$desc_name = ' name="menu_cat_desc[]"';
				$desc_val = isset($get_menu_cat_vals['menu_cat_desc']) ? $get_menu_cat_vals['menu_cat_desc'] : '';
				$add_btn_func = ' onClick="sa_restaurant_edit_save_cat_item( '. $resturent_id .','. $menu_item_counter .');"';
			} else {
				$add_btn_txt = esc_html__('Add Category', 'foodbakery');
				$title_name_value = '';
				$desc_name = '';
				$desc_val = '';
				$add_btn_func = ' onClick="foodbakery_admin_add_menu_cat_to_list(' . $resturent_id . ',' . $menu_item_counter . ');"';
			}
			$form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
			$form_html .= '<a href="javascript:void(0);" onClick="onClick="sa_restaurant_edit_save_cat_item( '. $resturent_id .','. $menu_item_counter .');" class="close-menu-item"><i class="icon-close2"></i></a>';
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





		public function sa_restaurant_remove_menu_cat_item($restaurant_id = 0, $menu_item_counter = '')
		{
			if (isset($_POST['restaurant_id'])) {
				$restaurant_id = (int) $_POST['restaurant_id'];
				$position  = (int)$_POST['position'];

				$restaurant_menu_cat_titles = get_post_meta($restaurant_id, 'menu_cat_titles', true);
				$restaurant_menu_cat_descs = get_post_meta($restaurant_id, 'menu_cat_descs', true);

				unset($restaurant_menu_cat_titles[$position]);
				unset($restaurant_menu_cat_descs[$position]);
				sort($restaurant_menu_cat_titles);
				update_post_meta($restaurant_id, 'menu_cat_titles', $restaurant_menu_cat_titles);
				sort($restaurant_menu_cat_descs);
				update_post_meta($restaurant_id, 'menu_cat_descs', $restaurant_menu_cat_descs);
				echo wp_json_encode(['success']);
			}

			// 	$restaurant_menu_cat_titles = get_post_meta($restaurant_id, 'menu_cat_titles', true);
			// 	$restaurant_menu_cat_descs = get_post_meta($restaurant_id, 'menu_cat_descs', true);
			// 	if (isset($_POST['position'])) {
			// 		$position = (int) $_POST['position'];
			// 		unset($new_arr[$position]);

			// 		sort($new_arr);
			// 		update_post_meta($restaurant_id, 'foodbakery_menu_items', $new_arr);
			// 		echo wp_json_encode(['success']);
			// 	}
			// }

			exit();
		}


		public function foodbakery_restaurant_menu_cat_item($restaurant_id = 0,  $cat_counter = 0, $menu_item_counter = '', $menu_cat_vals = array())
		{
			$item_html = '';
			$new_position = $cat_counter;
			if (isset($_POST['_menu_cat_title'])) {
				$menu_cat_title = $_POST['_menu_cat_title'];
				$menu_cat_desc = isset($_POST['_menu_cat_desc']) ? $_POST['_menu_cat_desc'] : '';
				$menu_item_counter = rand(1100000, 99999999);
				$restaurant_id = isset($_POST['restaurant_id']) ? (int) $_POST['restaurant_id'] : 0;
				$position = isset($_POST['position']) ? (int) $_POST['position'] : false;
			
				if ($restaurant_id > 0) {
					$restaurant_menu_cat_titles = get_post_meta($restaurant_id, 'menu_cat_titles', true);
					$restaurant_menu_cat_descs = get_post_meta($restaurant_id, 'menu_cat_descs', true);

					if (is_array($restaurant_menu_cat_titles)) {
					
						if($position !==false){
							$restaurant_menu_cat_titles[$position] = $menu_cat_title;
							$new_position = $position;
						}else{
							$restaurant_menu_cat_titles[] = $menu_cat_title;
							
						}
						//$restaurant_menu_cat_descs[] = $menu_cat_desc;
					} else {
						$restaurant_menu_cat_titles = array($menu_cat_title);
						//	$restaurant_menu_cat_descs = array($restaurant_menu_cat_descs, $menu_cat_desc);

					}
					if (is_array($restaurant_menu_cat_descs)) {
						if($position !=false){
							$restaurant_menu_cat_descs[$position] = $menu_cat_title;
						}else{
							$restaurant_menu_cat_descs[] = $menu_cat_desc;
						}
					} else {
						$restaurant_menu_cat_descs = array($menu_cat_desc);
					}
				
					update_post_meta($restaurant_id, 'menu_cat_titles', $restaurant_menu_cat_titles);
					update_post_meta($restaurant_id, 'menu_cat_descs', $restaurant_menu_cat_descs);
				}
			} else {
				extract($menu_cat_vals);
			}

			$get_menu_cat_vals = array(
				'menu_cat_title' => $menu_cat_title,
				'menu_cat_desc' => $menu_cat_desc,
			);
			

			$item_html .= '
			<li class="menu-item-' . $menu_item_counter . '" sa_key="' . $new_position . '" menu_item_counter="' . $menu_item_counter . '" restaurant_id="' . $restaurant_id . '">
				<div class="drag-list">
					<span class="drag-option"><i class="icon-bars"></i></span>
					<div class="list-title">
						<h6>' . $menu_cat_title . '</h6>
					</div>
					<div class="list-option">
						<a href="javascript:void(0);" class="edit-menu-item" onclick="foodbakery_add_menu_cat(\'' . $menu_item_counter . '\');"><i class="icon-mode_edit"></i></a>
						<a href="javascript:void(0);" class="remove-menu-item" onclick="foodbakery_remove_cat( ' . $restaurant_id . ' , ' . $menu_item_counter . ',' . $cat_counter . ');"><i class="icon-close2"></i></a>
					</div>
				</div>
				<div id="add-menu-cat-from-' . $menu_item_counter . '" style="display: none;">
					' . $this->foodbakery_restaurant_cat_form($restaurant_id, $get_menu_cat_vals, $menu_item_counter, 'edit') . '
				</div>
			</li>';

			if (isset($_POST['_menu_cat_title'])) {
				echo json_encode(array('html' => $item_html, 'type' => 'success', 'msg' => esc_html__('Menu category added successfully.', 'foodbakery')));
				die;
			} else {
				return $item_html;
			}
		}

		public function group_restaurant_menu_cats($restaurant_id)
		{
			$restaurant_menu_cat_titles = get_post_meta($restaurant_id, 'menu_cat_titles', true);
			$restaurant_menu_cat_descs = get_post_meta($restaurant_id, 'menu_cat_descs', true);

			//print_r($restaurant_menu_cat_titles);

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
					$html .= $this->foodbakery_restaurant_menu_cat_item($restaurant_id, $cat_counter, $menu_item_counter, $get_menu_cat_vals);

					$cat_counter++;
				}
			}
			return $html;
		}

		/**
		 * Features List
		 * @return markup
		 */
		public function restaurant_features_list($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter;

			$html = '';
			$foodbakery_restaurant_features = get_post_meta($foodbakery_id, 'foodbakery_restaurant_feature_list', true);

			$foodbakery_get_features = get_post_meta($type_id, 'feature_lables', true);
			$foodbakery_feature_icons = get_post_meta($type_id, 'foodbakery_feature_icon', true);

			if (is_array($foodbakery_get_features) && sizeof($foodbakery_get_features) > 0) {
				$html .= '
				<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<h4>' . esc_html__('Feature List', 'foodbakery') . '</h4>
						<a id="choose-all-apply-' . $restaurant_add_counter . '" data-id="' . $restaurant_add_counter . '" class="choose-all-apply" href="javascript:void(0);">' . esc_html__('Select/Unselect all', 'foodbakery') . '</a>
					</div>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
				<div class="field-holder">
				<ul id="features-check-list-' . $restaurant_add_counter . '" class="checkbox-list">';
				$feature_counter = 1;
				foreach ($foodbakery_get_features as $feat_key => $features) {
					if (isset($features) && !empty($features)) {

						$foodbakery_feature_name = isset($features) ? $features : '';
						$foodbakery_feature_icon = isset($foodbakery_feature_icons[$feat_key]) ? $foodbakery_feature_icons[$feat_key] : '';

						$html .= '<li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<input type="checkbox" id="check-' . $foodbakery_id . $feature_counter . '" value="' . $foodbakery_feature_name . "_icon" . $foodbakery_feature_icon . '" name="foodbakery_restaurant_feature[]"' . (is_array($foodbakery_restaurant_features) && in_array($foodbakery_feature_name . "_icon" . $foodbakery_feature_icon, $foodbakery_restaurant_features) ? ' checked="checked"' : '') . '>
							<label for="check-' . $foodbakery_id . $feature_counter . '">';
						if ($foodbakery_feature_icon != '') {
							$html .= '<i class="' . $foodbakery_feature_icon . '"></i>';
						}
						$html .= $foodbakery_feature_name . '</label>
						</li>';
						$feature_counter++;
					}
				}
				$html .= '
				</ul>
				</div>
				</div>
				</div>
				</div>';
			}
			return apply_filters('foodbakery_front_restaurant_add_features_list', $html, $type_id, $foodbakery_id);
		}

		/**
		 * Location Map
		 * @return markup
		 */
		public function restaurant_location($type_id = '', $foodbakery_id = '')
		{
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
				</div>';
			}
			return apply_filters('foodbakery_front_restaurant_add_location', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_location', 'my_callback_function', 10, 3);
		}

		/**
		 * Featured Image
		 * @return markup
		 */
		public function restaurant_featured_image($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter, $foodbakery_html_fields_frontend;
			$html = '';

			$restaurant_featured_image = get_post_thumbnail_id($foodbakery_id);
			$foodbakery_restaurant_title = get_the_title($foodbakery_id);
			$restaurant_title_text = '';
			if ($foodbakery_restaurant_title != '') {
				$restaurant_title_text = '<strong>' . $foodbakery_restaurant_title . '</strong> ';
			}
			$attacment_placeholder = '';
			$restaurant_added_featured_image = '';

			// $placeholder_style = '';
			$restaurant_added_featured_image_html = '';
			$img_url = '';
			$close_btn = '';
			if ($restaurant_featured_image != '') {
				$img_url = wp_get_attachment_url($restaurant_featured_image);
				$close_btn = '<ul class="list-inline pull-right">
                                    <li class="close-btn"><a href="javascript:void(0);"><i class="icon-cross-out"></i></a></li>
                            </ul>';
				$restaurant_added_featured_image .= '
				<li class="gal-img">
					<div class="drag-list">
						<div class="item-thumb"><img class="thumbnail" src="' . $img_url . '" alt=""/></div>
						<div class="item-assts">
							' . $close_btn . '
							<input type="hidden" name="foodbakery_restaurant_featured_image_id" value="' . $restaurant_featured_image . '">
						</div>
					</div>
				</li>';
			}


			$restaurant_added_featured_image_html .= ' 
                        <div class="img-holder">
                        ' . $attacment_placeholder . '
                        <ul id="foodbakery-dev-featured-img-' . $restaurant_add_counter . '" class="foodbakery-gallery-holder">' . $restaurant_added_featured_image . '</ul>
                        </div>
                ';





			$html .= '
			<div class="row featured-image-holder"> 
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                               <div class="restaurant-info">
                               ' . $restaurant_added_featured_image_html . '
					<div class="text-holder">
                                            ' . $restaurant_title_text . '
						<div class="upload-gallery">
							<input id="featured-image-uploader-' . $restaurant_add_counter . '" class="foodbakery-dev-gallery-uploader" style="display:none;" type="file" name="foodbakery_restaurant_featured_image[]" onchange="foodbakery_handle_file_single_select(event, \'' . $restaurant_add_counter . '\')">
							<a href="javascript:void(0);" class="upload-btn foodbakery-dev-featured-upload-btn" data-id="' . $restaurant_add_counter . '">' . esc_html__('Upload Logo', 'foodbakery') . '</a>
						</div>
                                                <span>' . esc_html__('Update your avatar manually, If the not set the default Gravatar will be the same as your login email/user account. Max Upload Size: 1MB,', 'foodbakery') . '</span>
					</div>
                                        </div>
				</div>
			</div>';

			return apply_filters('foodbakery_front_restaurant_add_featured_image', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_featured_image', 'my_callback_function', 10, 3);
		}

		/**
		 * Featured Cover Image
		 * @return markup
		 */
		public function restaurant_cover_image($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter, $foodbakery_html_fields_frontend;
			$html = '';

			//$restaurant_cover_image = get_post_thumbnail_id($foodbakery_id);

			$restaurant_cover_image = get_post_meta($foodbakery_id, 'foodbakery_restaurant_cover_image', true);
			$restaurant_title_text = '';
			$attacment_placeholder = '';
			$restaurant_added_cover_image = '';

			// $placeholder_style = '';
			$restaurant_added_cover_image_html = '';
			$img_url = '';
			$close_btn = '';
			if ($restaurant_cover_image != '') {
				$img_url = wp_get_attachment_url($restaurant_cover_image);
				$close_btn = '<ul class="list-inline pull-right">
                                    <li class="close-btn"><a href="javascript:void(0);"><i class="icon-cross-out"></i></a></li>
                            </ul>';
				$restaurant_added_cover_image .= '
				<li class="gal-img">
					<div class="drag-list">
						<div class="item-thumb"><img class="thumbnail" src="' . $img_url . '" alt=""/></div>
						<div class="item-assts">
							' . $close_btn . '
							<input type="hidden" name="foodbakery_restaurant_cover_image_id" value="' . $restaurant_cover_image . '">
						</div>
					</div>
				</li>';
			}


			$restaurant_added_cover_image_html .= ' 
                        <div class="img-holder">
                        ' . $attacment_placeholder . '
                        <ul id="foodbakery-dev-cover-img-' . $restaurant_add_counter . '" class="foodbakery-gallery-holder">' . $restaurant_added_cover_image . '</ul>
                        </div>
                ';





			$html .= '
			<div class="row cover-image-holder"> 
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                               <div class="restaurant-info">
                               ' . $restaurant_added_cover_image_html . '
					<div class="text-holder">
                                            ' . $restaurant_title_text . '
						<div class="upload-gallery">
							<input id="cover-image-uploader-' . $restaurant_add_counter . '" class="foodbakery-dev-gallery-uploader" style="display:none;" type="file" name="foodbakery_restaurant_cover_image[]" onchange="foodbakery_handle_cover_file_single_select(event, \'' . $restaurant_add_counter . '\')">
							<a href="javascript:void(0);" class="upload-btn foodbakery-dev-cover-upload-btn" data-id="' . $restaurant_add_counter . '">' . esc_html__('Upload Cover Image', 'foodbakery') . '</a>
						</div>
                                                <span>' . esc_html__('Update your cover image manually, If the not set the default cover image will be showing on your restaurant detail page. Max Upload Size: 1MB,', 'foodbakery') . '</span>
					</div>
                                        </div>
				</div>
			</div>';

			return apply_filters('foodbakery_front_restaurant_add_cover_image', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_featured_image', 'my_callback_function', 10, 3);
		}

		/**
		 * Gallery Photos
		 * @return markup
		 */
		public function restaurant_gallery($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter;
			$html = '';
			$foodbakery_restaurant_gallery = get_post_meta($type_id, 'foodbakery_image_gallery_element', true);

			$foodbakery_restaurant_gallery_ids = get_post_meta($foodbakery_id, 'foodbakery_detail_page_gallery_ids', true);

			if ($foodbakery_restaurant_gallery == 'on') {

				$attacment_placeholder = '';
				$attacment_sec_items = '';

				// In case of changing foodbakery type ajax
				// it will load the pre filled data
				$get_restaurant_form_select_type = foodbakery_get_input('select_type', '', 'STRING');
				if ($get_restaurant_form_select_type != '') {
					$get_restaurant_form_gal_items = foodbakery_get_input('foodbakery_restaurant_gallery_item', '', 'ARRAY');
					if (is_array($get_restaurant_form_gal_items) && sizeof($get_restaurant_form_gal_items) > 0) {
						foreach ($get_restaurant_form_gal_items as $img_item) {
							$img_url = wp_get_attachment_url($img_item);
							$attacment_sec_items .= '
							<li class="gal-img">
								<div class="drag-list">
									<div class="item-thumb"><img class="thumbnail" src="' . $img_url . '" alt=""/></div>
									<div class="item-assts">
										<div class="list-inline pull-right">
											<div class="drag-btn"><a href="javascript:void(0);"><i class="icon-bars"></i></a></div>
											<div class="close-btn"><a href="javascript:void(0);"><i class="icon-cross-out"></i></a></div>
										</div>
										<input type="hidden" name="foodbakery_restaurant_gallery_item[]" value="' . $img_item . '">
									</div>
								</div>
							</li>';
						}
					}
				}
				//

				if (is_array($foodbakery_restaurant_gallery_ids) && sizeof($foodbakery_restaurant_gallery_ids) > 0) {
					foreach ($foodbakery_restaurant_gallery_ids as $img_item) {
						$img_url = wp_get_attachment_url($img_item);
						$attacment_sec_items .= '
						<li class="gal-img">
							<div class="drag-list">
								<div class="item-thumb"><img class="thumbnail" src="' . $img_url . '" alt=""/></div>
								<div class="item-assts">
									<div class="list-inline pull-right">
										<div class="drag-btn"><a href="javascript:void(0);"><i class="icon-bars"></i></a></div>
										<div class="close-btn"><a href="javascript:void(0);"><i class="icon-cross-out"></i></a></div>
									</div>
									<input type="hidden" name="foodbakery_restaurant_gallery_item[]" value="' . $img_item . '">
								</div>
							</div>
						</li>';
					}
				}

				$is_ajax = false;
				if (isset($_POST['set_type']) && $_POST['set_type'] == 'gallery') {
					$is_ajax = true;
				}

				$placeholder_style = '';
				if ($attacment_sec_items != '') {
					$placeholder_style = ' style="display: none;"';
				}
				$attacment_placeholder = '
				<div' . $placeholder_style . ' id="attach-placeholder-' . $restaurant_add_counter . '" class="update-attachment">
					<div class="img-holder">
						<figure>
							<img src="' . wp_foodbakery::plugin_url() . 'assets/frontend/images/upload-attach-img.jpg" alt="" />
						</figure>
					</div>
					<div class="text">
						<h3>' . esc_html__('Attachments', 'foodbakery') . '</h3>
						<p>' . esc_html__('You can add only JPG, JPEG, PNG, GIF formates of images.', 'foodbakery') . '</p>
					</div>
				</div>';

				$html .= '
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="element-title">
							<h4>' . esc_html__('Photo Gallery', 'foodbakery') . '</h4>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="field-holder">
							<div class="upload-gallery">
								<input id="image-uploader-' . $restaurant_add_counter . '" class="foodbakery-dev-gallery-uploader" style="display:none;" type="file" multiple="multiple" name="foodbakery_restaurant_gallery_images[]" onchange="foodbakery_handle_file_select(event, \'' . $restaurant_add_counter . '\')">
								<a href="javascript:void(0);" class="upload-btn foodbakery-dev-gallery-upload-btn" data-id="' . $restaurant_add_counter . '"><i class="icon-upload6"></i>' . esc_html__('Upload Image', 'foodbakery') . '</a>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="field-holder">
							' . $attacment_placeholder . '
							<ul id="foodbakery-dev-gal-attach-sec-' . $restaurant_add_counter . '" class="foodbakery-gallery-holder">' . $attacment_sec_items . '</ul>
						</div>
					</div>
					<script>';
				if ($is_ajax === false) {
					$html .= 'jQuery(document).ready(function ($) {';
				}
				$html .= '$("#foodbakery-dev-gal-attach-sec-' . $restaurant_add_counter . '").sortable();';
				if ($is_ajax === false) {
					$html .= '});';
				}
				$html .= '
					</script>
				</div>';
			}
			return apply_filters('foodbakery_front_restaurant_add_gallery_plugin', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_gallery_plugin', 'my_callback_function', 10, 3);
		}

		/**
		 * Restaurant Tags
		 * @return markup
		 */
		public function restaurant_tags($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter;

			$html = '';
			// enqueue required script
			wp_enqueue_script('jquery-ui');
			wp_enqueue_script('foodbakery-tags-it');
			$select_restaurant_type = foodbakery_get_input('select_type', '');
			if ($select_restaurant_type != '') {
				$post = get_page_by_path($select_restaurant_type, OBJECT, 'restaurant-type');
				$type_id = $post->ID;
			} else {
				$type_id = $type_id;
			}
			$foodbakery_restaurant_type_tags = get_post_meta($type_id, 'foodbakery_restaurant_type_tags', true);

			$foodbakery_tags_list = '';

			// In case of changing foodbakery type ajax
			// it will load the pre filled data
			$get_restaurant_form_select_type = foodbakery_get_input('select_type', '', 'STRING');
			if ($get_restaurant_form_select_type != '') {
				$get_restaurant_form_tags = foodbakery_get_input('foodbakery_tags', '', 'ARRAY');
				if (is_array($get_restaurant_form_tags) && sizeof($get_restaurant_form_tags) > 0) {
					$foodbakery_tags_list = '';
					foreach ($get_restaurant_form_tags as $dir_tag) {
						$foodbakery_tags_list .= '<li>' . $dir_tag . '</li>';
					}
				}
			}
			//

			$foodbakery_restaurant_tags = get_post_meta($foodbakery_id, 'foodbakery_restaurant_tags', true);
			if (is_array($foodbakery_restaurant_tags) && !empty($foodbakery_restaurant_tags)) {
				$foodbakery_tags_list = '';
				foreach ($foodbakery_restaurant_tags as $foodbakery_restaurant_tag) {
					$foodbakery_tags_list .= '<li>' . $foodbakery_restaurant_tag . '</li>';
				}
			}

			$html .= '<div class="row">';
			$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
			$html .= '<div class="element-title">';
			$html .= '<h4>' . esc_html__('Tags Clouds', 'foodbakery') . '</h4>';
			$html .= '</div>';
			$html .= '<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(\'#foodbakery-tags\').tagit({
						allowSpaces: true,
						fieldName : \'foodbakery_tags[]\'
					});
				});
			</script>';
			$html .= '<ul id="foodbakery-tags">';
			$html .= $foodbakery_tags_list;
			$html .= '</ul>';
			if (is_array($foodbakery_restaurant_type_tags) && !empty($foodbakery_restaurant_type_tags)) {
				$html .= '<span class="most-used">' . esc_html__('Mostly Used Tags', 'foodbakery') . '</span>';
				$html .= '<ul class="tag-cloud-container" id="tag-cloud">';
				foreach ($foodbakery_restaurant_type_tags as $foodbakery_restaurant_type_tag) {
					$term = get_term_by('slug', $foodbakery_restaurant_type_tag, 'restaurant-tag');
					if (is_object($term)) {
						$html .= '<li class="tag-cloud" onclick="jQuery(\'#foodbakery-tags\').tagit(\'createTag\', \'' . $term->name . '\');return false;">' . $term->name . '</li>';
					}
				}
				$html .= '</ul>';
			}
			$html .= '</div>';
			$html .= '</div>';

			return apply_filters('foodbakery_front_restaurant_add_tags', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_tags', 'my_callback_function', 10, 3);
		}

		/**
		 * Restaurant Price
		 * @return markup
		 */
		public function restaurant_price($restaurant_type_id = 0, $foodbakery_id = 0)
		{
			global $restaurant_add_counter, $foodbakery_form_fields;

			$foodbakery_restaurant_type_price = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_price', true);
			$foodbakery_restaurant_type_price = isset($foodbakery_restaurant_type_price) && $foodbakery_restaurant_type_price != '' ? $foodbakery_restaurant_type_price : 'off';
			$html = '';
			if ($foodbakery_restaurant_type_price == 'on') {
				$html .= '<div class="row">';
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
				$html .= '<div class="element-title">';
				$html .= '<h4>' . esc_html__('Restaurant Price Option', 'foodbakery') . '</h4>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
				$html .= '<div class="field-holder">';
				$foodbakery_restaurant_price_options = get_post_meta($foodbakery_id, 'foodbakery_restaurant_price_options', true);
				$foodbakery_restaurant_price = get_post_meta($foodbakery_id, 'foodbakery_restaurant_price', true);
				$foodbakery_opt_array = array(
					'std' => $foodbakery_restaurant_price_options,
					'id' => 'restaurant_price_options',
					'classes' => 'chosen-select-no-single',
					'extra_atr' => 'onchange="foodbakery_restaurant_price_change_frontend(this.value)"',
					'options' => array('none' => 'None', 'on-call' => 'On Call', 'price' => 'Price',),
					'return' => true,
				);
				$html .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
				$html .= '</div>';
				$html .= "<script>
		    function foodbakery_restaurant_price_change_frontend(price_selection) {
			if (price_selection == 'none' || price_selection == 'on-call') {
			    jQuery('#foodbakery_restaurant_price_toggle').hide();
			} else {
			    jQuery('#foodbakery_restaurant_price_toggle').show();
			}
		    }
		</script>";
				$html .= '</div>';
				$html .= '</div>';
				$hide_div = '';
				if ($foodbakery_restaurant_price_options == '' || $foodbakery_restaurant_price_options == 'none' || $foodbakery_restaurant_price_options == 'on-call') {
					$hide_div = 'style="display:none;"';
				}
				$html .= '<li class="foodbakery-dev-appended" id="foodbakery_restaurant_price_toggle" ' . $hide_div . '>';
				$html .= '<div class="row">';
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
				$html .= '<div class="element-title">';
				$html .= '<h4>' . esc_html__('Restaurant Price', 'foodbakery') . '</h4>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
				$html .= '<div class="field-holder">';
				$foodbakery_opt_array = array(
					'std' => $foodbakery_restaurant_price,
					'id' => 'restaurant_price',
					'return' => true,
				);
				$html .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</li>';
			}

			return $html;
		}

		/**
		 * Restaurant Categories
		 * @return markup
		 */
		public function restaurant_categories($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter, $foodbakery_form_fields, $foodbakery_restaurant_meta;

			$html = '';
			$restaurant_type_post = get_post($type_id);
			$restaurant_type_slug = isset($restaurant_type_post->post_name) ? $restaurant_type_post->post_name : 0;

			$html .= $foodbakery_restaurant_meta->restaurant_categories($restaurant_type_slug, $foodbakery_id, $backend = false);

			return apply_filters('foodbakery_front_restaurant_add_categories', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_categories', 'my_callback_function', 10, 3);
		}

		/**
		 * Restaurant Menu Order
		 * @return markup
		 */
		public function restaurant_menu_order($type_id = '', $foodbakery_id = '')
		{
			global $foodbakery_form_fields, $restaurant_add_counter, $foodbakery_plugin_options;
			if ($type_id == '') {
				$foodbakery_id = foodbakery_get_input('restaurant_id', 0);
			}

			$minimum_order_value = get_post_meta($foodbakery_id, 'foodbakery_minimum_order_value', true);
			$delivery_fee = get_post_meta($foodbakery_id, 'foodbakery_delivery_fee', true);
			$restaurant_web = get_post_meta($foodbakery_id, 'foodbakery_restaurant_contact_web', true);
			$html = '<div class="field-holder">';
			$html .= '<div class="element-title">
						<h4> ' . esc_html__('Menu Order Options', 'foodbakery') . '</h4>
					</div>
					<label>' . esc_html__('Minimum Order Value', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'min_order_value_' . $minimum_order_value,
					'cust_name' => 'foodbakery_minimum_order_value',
					'std' => $restaurant_email,
					'desc' => '',
					'extra_atr' => ' placeholder="' . esc_html__('Minimum Order Value', 'foodbakery') . '"',
					'classes' => 'foodbakery-order-value-field',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
				)
			);
			$html .= '
				</div>
                                <div class="field-holder">
					<label>' . esc_html__('Delivery Fee', 'foodbakery') . '</label>';
			$html .= $foodbakery_form_fields->foodbakery_form_text_render(
				array(
					'id' => 'delivery_fee_' . $delivery_fee,
					'cust_name' => 'foodbakery_delivery_fee',
					'std' => $restaurant_phone,
					'desc' => '',
					'classes' => 'foodbakery-delivery-fee-field',
					'extra_atr' => ' placeholder="' . esc_html__('Delivery Fee', 'foodbakery') . '"',
					'return' => true,
					'force_std' => true,
					'hint_text' => '',
				)
			);
			$html .= '
				</div>';

			return apply_filters('foodbakery_front_restaurant_add_menu_order_fields', $html, $type_id, $foodbakery_id);
		}

		/**
		 * Restaurant Menu Items
		 * @return markup
		 */
		public function restaurant_menu_items($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter;
			return apply_filters('foodbakery_restaurant_menu_items', $restaurant_add_counter, $type_id, $foodbakery_id);
		}

		/**
		 * Set Book Days off
		 * @return markup
		 */
		public function restaurant_book_days_off($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter;
			$html = '';
			$off_days_list = '';

			$foodbakery_off_days = get_post_meta($type_id, 'foodbakery_off_days', true);
			if ($foodbakery_off_days == 'on') {
				// In case of changing foodbakery type ajax
				// it will load the pre filled data
				$get_restaurant_form_select_type = foodbakery_get_input('select_type', '', 'STRING');
				if ($get_restaurant_form_select_type != '') {
					$get_restaurant_form_days_off = foodbakery_get_input('foodbakery_restaurant_off_days', '', 'ARRAY');
					if (is_array($get_restaurant_form_days_off) && sizeof($get_restaurant_form_days_off)) {
						foreach ($get_restaurant_form_days_off as $get_off_day) {
							$off_days_list .= $this->append_to_book_days_off($get_off_day);
						}
					}
				}
				// end ajax loading

				$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
				if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
					$get_restaurant_off_days = get_post_meta($get_restaurant_id, 'foodbakery_calendar', true);
					if (is_array($get_restaurant_off_days) && sizeof($get_restaurant_off_days)) {
						foreach ($get_restaurant_off_days as $get_off_day) {
							$off_days_list .= $this->append_to_book_days_off($get_off_day);
						}
					}
				}
				if ($off_days_list == '') {
					$off_days_list = '<li id="no-book-day-' . $restaurant_add_counter . '" class="no-result-msg">' . esc_html__('No off days added.', 'foodbakery') . '</li>';
				}

				$is_ajax = false;
				if (isset($_POST['set_type']) && $_POST['set_type'] == 'openclose') {
					$is_ajax = true;
				}

				wp_enqueue_script('responsive-calendar');

				$html .= '
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="element-title">
							<h4>' . esc_html__('Book Day Off', 'foodbakery') . '</h4>
							<div id="dev-off-day-loader-' . $restaurant_add_counter . '" class="restaurant-loader"></div>
							<a class="book-btn" href="javascript:void(0);">' . esc_html__('Book off day', 'foodbakery') . '</a>
							<div id="foodbakery-dev-cal-holder-' . $restaurant_add_counter . '" class="calendar-holder">
								<div data-id="' . $restaurant_add_counter . '" class="foodbakery-dev-insert-off-days responsive-calendar">
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
						<script>';
				if ($is_ajax === false) {
					$html .= 'jQuery(document).ready(function () {';
				}
				$html .= '
				jQuery(".responsive-calendar").responsiveCalendar({
					monthChangeAnimation: false,
				});';
				if ($is_ajax === false) {
					$html .= '});';
				}
				$html .= '</script>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="field-holder">
							<div class="book-list">
								<ul id="foodbakery-dev-add-off-day-app-' . $restaurant_add_counter . '">
									' . $off_days_list . '
								</ul>
							</div>
						</div>
					</div>
				</div>';
			}
			return apply_filters('foodbakery_front_restaurant_add_book_off_days', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_book_off_days', 'my_callback_function', 10, 3);
		}

		/**
		 * Appending off days to list via Ajax
		 * @return markup
		 */
		public function append_to_book_days_off($get_off_day = '')
		{

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

			$html = '<li id="day-remove-' . $rand_numb . '">
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
				return apply_filters('foodbakery_front_restaurant_add_single_off_day', $html, $get_off_day);
				// usage :: add_filter('foodbakery_front_restaurant_add_single_off_day', 'my_callback_function', 10, 2);
			} else {
				echo json_encode(array('html' => $html));
				die;
			}
		}

		/**
		 * Opening Hours
		 * @return markup
		 */
		public function restaurant_opening_hours($type_id = '', $foodbakery_id = '')
		{
			global $restaurant_add_counter;
			$html = '';
			$foodbakery_restaurant_opening_hours = get_post_meta($type_id, 'foodbakery_opening_hours_element', true);
			if ($foodbakery_restaurant_opening_hours == 'on') {
				$time_list = $this->restaurant_time_list($type_id);
				$week_days = $this->restaurant_week_days();

				$time_from_html = '';
				$time_to_html = '';

				// In case of changing foodbakery type ajax
				// it will load the pre filled data
				$get_restaurant_form_select_type = foodbakery_get_input('select_type', '', 'STRING');
				if ($get_restaurant_form_select_type != '') {
					$get_opening_hours = foodbakery_get_input('foodbakery_opening_hour', '', 'ARRAY');
				}
				// end ajax loading

				$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
				if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
					$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
					$get_opening_hours = array();
					foreach ($days as $key => $day) {
						$opening_time = get_post_meta($get_restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true);
						$opening_time = ($opening_time != '' ? date('h:i a', $opening_time) : '');
						$closing_time = get_post_meta($get_restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true);
						$closing_time = ($opening_time != '' ? date('h:i a', $closing_time) : '');
						$get_opening_hours[$day] = array(
							'day_status' => get_post_meta($get_restaurant_id, 'foodbakery_opening_hours_' . $day . '_day_status', true),
							'opening_time' => $opening_time,
							'closing_time' => $closing_time,
						);
					}
				}

				$days_html = '';
				if (is_array($week_days) && sizeof($week_days) > 0) {
					foreach ($week_days as $day_key => $week_day) {

						$day_status = isset($get_opening_hours[$day_key]['day_status']) ? $get_opening_hours[$day_key]['day_status'] : '';


						$opening_time = isset($get_opening_hours[$day_key]['opening_time']) ? $get_opening_hours[$day_key]['opening_time'] : '';
						$closing_time = isset($get_opening_hours[$day_key]['closing_time']) ? $get_opening_hours[$day_key]['closing_time'] : '';

						if (is_array($time_list) && sizeof($time_list) > 0) {
							$time_from_html = '';
							$time_to_html = '';
							foreach ($time_list as $time_key => $time_val) {
								$time_from_html .= '<option value="' . $time_key . '"' . ($opening_time == $time_key ? ' selected="selected"' : '') . '>' . $time_val . '</option>' . "\n";
								$time_to_html .= '<option value="' . $time_key . '"' . ($closing_time == $time_key ? ' selected="selected"' : '') . '>' . $time_val . '</option>' . "\n";
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
									<a id="foodbakery-dev-close-time-' . $day_key . '-' . $restaurant_add_counter . '" href="javascript:void(0);" data-id="' . $restaurant_add_counter . '" data-day="' . $day_key . '" title="' . esc_html__('Close', 'foodbakery') . '"><i class="icon-close2"></i></a>
								</div>
								<div class="close-time">
									<a id="foodbakery-dev-open-time-' . $day_key . '-' . $restaurant_add_counter . '" href="javascript:void(0);" data-id="' . $restaurant_add_counter . '" data-day="' . $day_key . '">' . esc_html__('Closed', 'foodbakery') . ' <span>(' . esc_html__('Click to add opening  Hours', 'foodbakery') . ')</span></a>
									<input id="foodbakery-dev-open-day-' . $day_key . '-' . $restaurant_add_counter . '" type="hidden" name="foodbakery_opening_hour[' . $day_key . '][day_status]"' . (isset($day_status) && $day_status == 'on' ? ' value="on"' : '') . '>
								</div>
							</div>
						</li>';
					}
				}

				$html .= '
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="element-title">
							<h4>' . esc_html__('Opening Hours', 'foodbakery') . '</h4>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<div class="time-list">
							<ul>
								' . $days_html . '
							</ul>
						</div>
					</div>
				</div>';
			}
			return apply_filters('foodbakery_front_restaurant_add_open_hours', $html, $type_id, $foodbakery_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_open_hours', 'my_callback_function', 10, 3);
		}

		/**
		 * Load foodbakery Custom Fields
		 * @return markup
		 */
		public function restaurant_custom_fields()
		{
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$restaurant_type = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_type', true);
			} else {
				$types_args = array('posts_per_page' => '-1', 'post_type' => 'restaurant-type', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
				$cust_query = get_posts($types_args);
				$restaurant_type = isset($cust_query[0]->post_name) ? $cust_query[0]->post_name : '';
			}
			if ($restaurant_type != '') {
				$restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type", 'post_status' => 'publish'));
				$restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
				$html = $this->custom_fields($restaurant_type_id, $get_restaurant_id);
				echo force_balance_tags($html);
			}
		}

		/**
		 * Load foodbakery Meta Data
		 * @return markup
		 */
		public function restaurant_meta_data()
		{
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$restaurant_type = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_type', true);
			} else {
				$types_args = array('posts_per_page' => '-1', 'post_type' => 'restaurant-type', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
				$cust_query = get_posts($types_args);
				$restaurant_type = isset($cust_query[0]->post_name) ? $cust_query[0]->post_name : '';
			}

			if ($restaurant_type != '') {

				$restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type", 'post_status' => 'publish'));
				$restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

				$html .= '<li id="cond-restaurant-gallery" class="restaurant-settings-cond" style="display:none;">' . $this->restaurant_gallery($restaurant_type_id, $get_restaurant_id) . '</li>';
				$html .= '<li id="cond-restaurant-openclose" class="restaurant-settings-cond" style="display:none;">';
				$html .= $this->restaurant_opening_hours($restaurant_type_id, $get_restaurant_id);
				$html .= $this->restaurant_book_days_off($restaurant_type_id, $get_restaurant_id);
				$html .= '</li>';
				$html .= '<li id="cond-restaurant-menubuild" class="restaurant-settings-cond" style="display:none;">' . $this->restaurant_menu_items($restaurant_type_id, $get_restaurant_id) . '</li>';
				echo force_balance_tags($html);
			}
		}

		/**
		 * Load Subscribed Memberships
		 * @return markup
		 */
		public function restaurant_user_subscribed_packages()
		{
			global $restaurant_add_counter, $foodbakery_plugin_options;
			$html = '';
			$pkg_options = '';
			$foodbakery_currency_sign = foodbakery_get_currency_sign();

			$atcive_pkgs = $this->user_all_active_pkgs();
			if (is_array($atcive_pkgs) && sizeof($atcive_pkgs) > 0) {
				$pkgs_counter = 1;
				$html .= '<div class="all-pckgs-sec">';
				foreach ($atcive_pkgs as $atcive_pkg) {

					$package_id = get_post_meta($atcive_pkg, 'foodbakery_transaction_package', true);
					$package_price = get_post_meta($atcive_pkg, 'foodbakery_transaction_amount', true);
					$package_title = $package_id != '' ? get_the_title($package_id) : '';
					$pkg_options .= '<div class="foodbakery-pkg-holder">';
					$pkg_options .= '<div class="foodbakery-pkg-header">';
					$pkg_options .= '
					<div class="pkg-title-price pull-left">
						<label class="pkg-title">' . $package_title . '</label>
						<span class="pkg-price">' . sprintf(esc_html__('Price: %s', 'foodbakery'), foodbakery_get_currency($package_price, true)) . '</span>
					</div>
					<div class="pkg-detail-btn pull-right">
						<input type="radio" id="package-' . $package_id . 'pt_' . $atcive_pkg . '" name="foodbakery_restaurant_active_package" value="' . $package_id . 'pt_' . $atcive_pkg . '">
						<a href="javascript:void(0);" class="foodbakery-dev-detail-pkg" data-id="' . $package_id . 'pt_' . $atcive_pkg . '">' . esc_html__('Detail', 'foodbakery') . '</a>
					</div>';
					$pkg_options .= '</div>';
					$pkg_options .= $this->subs_package_info($package_id, $atcive_pkg);
					$pkg_options .= '</div>';
					$pkgs_counter++;
				}

				$html .= $pkg_options;
				$html .= '</div>';
			}

			return apply_filters('foodbakery_restaurant_add_subscribed_packages', $html);
			// usage :: add_filter('foodbakery_restaurant_add_subscribed_packages', 'my_callback_function', 10, 1);
		}

		/**
		 * Load Memberships and Payment
		 * @return markup
		 */
		public function restaurant_packages()
		{
			global $foodbakery_plugin_options, $restaurant_add_counter;
			$html = '';
			$restaurant_up_visi = 'block';
			$restaurant_hide_btn = 'none';

			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			$current_user = wp_get_current_user();
			$publisher_id = foodbakery_company_id_form_user_id($current_user->ID);
			if ($get_restaurant_id <= 0) {
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
				if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
					$get_restaurant_id = $pub_restaurant[0];
				}
			}

			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$restaurant_up_visi = 'none';
				$restaurant_hide_btn = 'inline-block';
			}

			if (isset($_GET['package_id']) && $_GET['package_id'] != '') {
				$restaurant_up_visi = 'block';
				$restaurant_hide_btn = 'none';
			}

			$show_li = false;
			$show_pgt = false;

			$foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : '';
			$foodbakery_currency_sign = foodbakery_get_currency_sign();

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
							$packages_list_opts .= '<div class="pkg-title-price pull-left">
								<label class="pkg-title">' . $packg_title . '</label>
								<span class="pkg-price">' . sprintf(esc_html__('Price: %s', 'foodbakery'), foodbakery_get_currency($package_price, true)) . '</span>
							</div>
							<div class="pkg-detail-btn pull-right">
								<input  type="radio" id="package-' . $package_post->ID . '" name="foodbakery_restaurant_package"' . (isset($buying_pkg_id) && $buying_pkg_id == $package_post->ID ? ' checked="checked"' : '') . ' value="' . $package_post->ID . '">
								<a href="javascript:void(0);" class="foodbakery-dev-detail-pkg" data-id="' . $package_post->ID . '">' . esc_html__('Detail', 'foodbakery') . '</a>
							</div>';
							$packages_list_opts .= '</div>';
							$packages_list_opts .= $this->new_package_info($package_post->ID);
							$packages_list_opts .= '</div>';
							$opts_counter++;
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
					$packages_list .= '<div id="buy-package-head-' . $restaurant_add_counter . '" style="display:' . $new_pkgs_visibility . ';" class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
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
								<a data-id="' . $restaurant_add_counter . '" style="display:' . $restaurant_hide_btn . ';" href="javascript:void(0);" class="foodbakery-dev-cancel-pkg"><i class="icon-cross2"></i></a>
							</div>';
						} else {
							$packages_list .= '<input type="checkbox" checked="checked" style="display:none;" name="foodbakery_restaurant_new_package_used">';
							$packages_list .= '
							<div class="buy-new-pakg-actions" style="display:' . $restaurant_hide_btn . ';">
								<a data-id="' . $restaurant_add_counter . '" href="javascript:void(0);" class="foodbakery-dev-cancel-pkg"><i class="icon-cross2"></i></a>
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
		 * Select Foodbakery Featured
		 * and Top Category
		 * @return markup
		 */
		public function restaurant_featured_top_cat($pckg_id = '', $trans_id = '')
		{
			global $restaurant_add_counter;

			$html = '';
			$restaurant_featured = '';
			$restaurant_top_cat = '';

			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$restaurant_featured = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_is_featured', true);
				$restaurant_top_cat = get_post_meta($get_restaurant_id, 'foodbakery_restaurant_is_top_cat', true);
			}

			$featured_num = 0;
			$top_cat_num = 0;
			if ($pckg_id != '' && $trans_id == '') {
				$packg_data = get_post_meta($pckg_id, 'foodbakery_package_data', true);
				$featured_num = isset($packg_data['number_of_featured_restaurants']['value']) ? $packg_data['number_of_featured_restaurants']['value'] : '';
				$top_cat_num = isset($packg_data['number_of_top_cat_restaurants']['value']) ? $packg_data['number_of_top_cat_restaurants']['value'] : '';
			} else if ($pckg_id != '' && $trans_id != '') {
				if ($user_package = $this->get_user_package_trans($pckg_id, $trans_id)) {

					$trans_featured_num = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_feature_list', true);
					$foodbakery_featured_ids = get_post_meta($trans_id, 'foodbakery_featured_ids', true);

					if (empty($foodbakery_featured_ids)) {
						$foodbakery_featured_ids_size = 0;
					} else {
						$foodbakery_featured_ids_size = absint(sizeof($foodbakery_featured_ids));
					}

					$foodbakery_featured_used = $foodbakery_featured_ids_size;

					if ((int) $trans_featured_num > (int) $foodbakery_featured_used) {
						$featured_num = (int) $trans_featured_num - (int) $foodbakery_featured_used;
					}

					$trans_top_cat_num = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_top_cat_list', true);
					$foodbakery_top_cat_ids = get_post_meta($trans_id, 'foodbakery_top_cat_ids', true);

					if (empty($foodbakery_top_cat_ids)) {
						$foodbakery_top_cat_ids_size = 0;
					} else {
						$foodbakery_top_cat_ids_size = absint(sizeof($foodbakery_top_cat_ids));
					}

					$foodbakery_top_cat_used = $foodbakery_top_cat_ids_size;

					if ((int) $trans_top_cat_num > (int) $foodbakery_top_cat_used) {
						$top_cat_num = (int) $trans_top_cat_num - (int) $foodbakery_top_cat_used;
					}
				}
			}

			if ($featured_num <= 0 && $top_cat_num <= 0) {
				return apply_filters('foodbakery_restaurant_add_featured_top_cat', $html, $pckg_id, $trans_id);
				// usage :: add_filter('foodbakery_restaurant_add_featured_top_cat', 'my_callback_function', 10, 3);
			}

			$html .= '<div class="dev-restaurant-featured-top-cat col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">';
			if ($featured_num > 0) {
				$html .= '<input id="foodbakery_restaurant_featured_' . $restaurant_add_counter . '" type="checkbox" name="foodbakery_restaurant_featured"' . ($restaurant_featured == 'on' ? ' checked="checked"' : '') . '>
				<label for="foodbakery_restaurant_featured_' . $restaurant_add_counter . '">' . esc_html__('Featured', 'foodbakery') . '</label>';
			}
			if ($top_cat_num > 0) {
				$html .= '<input id="foodbakery_restaurant_top_cat_' . $restaurant_add_counter . '" type="checkbox" name="foodbakery_restaurant_top_cat"' . ($restaurant_top_cat == 'on' ? ' checked="checked"' : '') . '>
				<label for="foodbakery_restaurant_top_cat_' . $restaurant_add_counter . '">' . esc_html__('Top Category', 'foodbakery') . '</label>';
			}

			$html .= '</div>
			</div>';

			return apply_filters('foodbakery_restaurant_add_featured_top_cat', $html, $pckg_id, $trans_id);
			// usage :: add_filter('foodbakery_restaurant_add_featured_top_cat', 'my_callback_function', 10, 3);
		}

		/**
		 * Terms and Conditions
		 * and Submit Button
		 * @return markup
		 */
		public function restaurant_submit_button()
		{
			global $restaurant_add_counter;
			$check_box = '';
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			$btn_text = esc_html__('Create Restaurant', 'foodbakery');
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				$btn_text = esc_html__('Update Restaurant', 'foodbakery');
			} else {

				$check_box = wp_foodbakery::get_terms_and_conditions_field('', 'terms-' . $restaurant_add_counter);
			}
			ob_start();
			?>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<?php echo $check_box; ?>
						<div class="payment-holder input-button-loader" id="update-restaurant-holder">
							<input class="update-restaurant" type="submit" value="<?php echo $btn_text; ?>">
						</div>
					</div>
				</div>
			</div>
			<?php
			$html = ob_get_clean();
			echo force_balance_tags($html);
		}

		/**
		 * Time List
		 * @return array
		 */
		public function restaurant_time_list($type_id = '')
		{

			$lapse = 15;

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

			return apply_filters('foodbakery_front_restaurant_add_time_list', $hours, $type_id);
			// usage :: add_filter('foodbakery_front_restaurant_add_time_list', 'my_callback_function', 10, 2);
		}

		/**
		 * Week Days
		 * @return array
		 */
		public function restaurant_week_days()
		{

			$week_days = array(
				'monday' => esc_html__('Monday', 'foodbakery'),
				'tuesday' => esc_html__('Tuesday', 'foodbakery'),
				'wednesday' => esc_html__('Wednesday', 'foodbakery'),
				'thursday' => esc_html__('Thursday', 'foodbakery'),
				'friday' => esc_html__('Friday', 'foodbakery'),
				'saturday' => esc_html__('Saturday', 'foodbakery'),
				'sunday' => esc_html__('Sunday', 'foodbakery')
			);

			return apply_filters('foodbakery_front_restaurant_add_week_days', $week_days);
			// usage :: add_filter('foodbakery_front_restaurant_add_week_days', 'my_callback_function', 10, 1);
		}

		/**
		 * Creating foodbakery restaurant
		 * @return restaurant id
		 */
		public function restaurant_insert($publisher_id = '')
		{
			global $foodbakery_plugin_options, $restaurant_add_counter;

			$foodbakery_free_restaurants_switch = isset($foodbakery_plugin_options['foodbakery_free_restaurants_switch']) ? $foodbakery_plugin_options['foodbakery_free_restaurants_switch'] : '';

			$restaurant_id = 0;
			$restaurant_title = isset($_POST['foodbakery_restaurant_title']) ? $_POST['foodbakery_restaurant_title'] : '';
			$restaurant_desc = isset($_POST['foodbakery_restaurant_desc']) ? $_POST['foodbakery_restaurant_desc'] : '';
			if ($restaurant_title != '' && $restaurant_desc != '' && $publisher_id != '') {

				$form_rand_numb = isset($_POST['form_rand_id']) ? $_POST['form_rand_id'] : '';
				$form_rand_transient = get_transient('restaurant_submission_check');

				if ($form_rand_transient != $form_rand_numb) {
					$restaurant_post = array(
						'post_title' => wp_strip_all_tags($restaurant_title),
						'post_content' => $restaurant_desc,
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
			// usage :: add_filter('foodbakery_front_restaurant_add_create', 'my_callback_function', 10, 1);
		}

		/**
		 * Save foodbakery restaurant
		 * @return
		 */
		public function restaurant_meta_save()
		{
			global $current_user, $restaurant_add_counter;
			$get_restaurant_id = foodbakery_get_input('restaurant_id', 0);
			$is_updating = false;
			if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
				//restaurant is for update
				$restaurant_id = $get_restaurant_id;
				$is_updating = true;
				$publisher_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_publisher', true);
			} else {
				// Inserting Listing
				if (is_user_logged_in()) {
					$company_id = foodbakery_company_id_form_user_id($current_user->ID);
					$publisher_id = $company_id;
					$publish_user_id = $current_user->ID;
					$restaurant_id = $this->restaurant_insert($publisher_id);
				} else {
					$publisher_id = '';
					$restaurant_id = '';
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
						$restaurant_id = $this->restaurant_insert($publisher_id);
					}
				}
			}

			if ($restaurant_id != '' && $restaurant_id != 0 && $this->is_form_submit()) {

				if ($is_updating) {
					// updating post title and content
					$foodbakery_restaurant_title = foodbakery_get_input('foodbakery_restaurant_title', '', 'STRING');
					$foodbakery_restaurant_content = isset($_POST['foodbakery_restaurant_desc']) ? $_POST['foodbakery_restaurant_desc'] : '';

					if (isset($_POST['foodbakery_restaurant_desc']) && $_POST['foodbakery_restaurant_desc'] != '') {
						$restaurant_post = array(
							'ID' => $restaurant_id,
							'post_title' => $foodbakery_restaurant_title,
							'post_content' => $foodbakery_restaurant_content,
						);
					} else {
						$restaurant_post = array(
							'ID' => $restaurant_id,
							'post_title' => $foodbakery_restaurant_title,
						);
					}
					wp_update_post($restaurant_post);
				}

				if (!$is_updating) {
					// saving Restaurant posted date
					update_post_meta($restaurant_id, 'foodbakery_restaurant_posted', strtotime(current_time('d-m-Y H:i:s')));

					// saving Restaurant Publisher
					update_post_meta($restaurant_id, 'foodbakery_listing_publisher', $publisher_id);
					if (isset($publish_user_id)) {
						update_post_meta($restaurant_id, 'foodbakery_listing_username', $publish_user_id);
					}
				}




				// Saving Restaurant Featured Image
				$restaurant_featured_image_id = '';
				$foodbakery_restaurant_featured_image_id = isset($_POST['foodbakery_restaurant_featured_image_id']) ? $_POST['foodbakery_restaurant_featured_image_id'] : '';
				$restaurant_featured_image = isset($_FILES['foodbakery_restaurant_featured_image']) ? $_FILES['foodbakery_restaurant_featured_image'] : '';
				if ($foodbakery_restaurant_featured_image_id != '') {
					$restaurant_featured_image_id = $foodbakery_restaurant_featured_image_id;
				} else if ($restaurant_featured_image != '' && !is_numeric($restaurant_featured_image) && !empty($restaurant_featured_image)) {
					$gallery_media_upload = $this->restaurant_gallery_upload('foodbakery_restaurant_featured_image', $restaurant_featured_image);
					$restaurant_featured_image_id = isset($gallery_media_upload[0]) ? $gallery_media_upload[0] : '';
				}

				if (isset($_FILES['foodbakery_restaurant_featured_image'])) {
					if ($restaurant_featured_image_id != '' && is_numeric($restaurant_featured_image_id)) {
						set_post_thumbnail($restaurant_id, $restaurant_featured_image_id);
						$img_url = wp_get_attachment_url($restaurant_featured_image_id);
						update_post_meta($restaurant_id, 'foodbakery_cover_image', $restaurant_featured_image_id);
					} else {
						delete_post_thumbnail($restaurant_id);
						update_post_meta($restaurant_id, 'foodbakery_cover_image', '');
					}
				}




				// Saving Restaurant Cover Image
				$restaurant_cover_image_id = '';
				$foodbakery_restaurant_cover_image_id = isset($_POST['foodbakery_restaurant_cover_image_id']) ? $_POST['foodbakery_restaurant_cover_image_id'] : '';
				$restaurant_cover_image = isset($_FILES['foodbakery_restaurant_cover_image']) ? $_FILES['foodbakery_restaurant_cover_image'] : '';

				if ($foodbakery_restaurant_cover_image_id != '') {
					$restaurant_cover_image_id = $foodbakery_restaurant_cover_image_id;
				} else if ($restaurant_cover_image != '' && !is_numeric($restaurant_cover_image) && !empty($restaurant_cover_image)) {
					$gallery_media_upload = $this->restaurant_gallery_upload('foodbakery_restaurant_cover_image', $restaurant_cover_image);
					$restaurant_cover_image_id = isset($gallery_media_upload[0]) ? $gallery_media_upload[0] : '';
				}

				if (isset($_FILES['foodbakery_restaurant_cover_image'])) {
					if ($restaurant_cover_image_id != '' && is_numeric($restaurant_cover_image_id)) {
						$img_url = wp_get_attachment_url($restaurant_cover_image_id);
						update_post_meta($restaurant_id, 'foodbakery_restaurant_cover_image', $restaurant_cover_image_id);
					} else {
						update_post_meta($restaurant_id, 'foodbakery_restaurant_cover_image', '');
					}
				}


				// Saving Restaurant Gallery
				$restaurant_gal_array = array();
				if (isset($_FILES['foodbakery_restaurant_gallery_images']) && !empty($_FILES['foodbakery_restaurant_gallery_images'])) {
					$gallery_media_upload = $this->restaurant_gallery_upload('foodbakery_restaurant_gallery_images');
					if (is_array($gallery_media_upload)) {
						$restaurant_gal_array = array_merge($restaurant_gal_array, $gallery_media_upload);
					}
				}
				$foodbakery_restaurant_gallery_items = foodbakery_get_input('foodbakery_restaurant_gallery_item', '', 'ARRAY');
				if (is_array($foodbakery_restaurant_gallery_items) && sizeof($foodbakery_restaurant_gallery_items) > 0) {
					$restaurant_gal_array = array_merge($restaurant_gal_array, $foodbakery_restaurant_gallery_items);
				}

				if (isset($_FILES['foodbakery_restaurant_gallery_images']) || isset($_POST['foodbakery_restaurant_gallery_item'])) {
					update_post_meta($restaurant_id, 'foodbakery_detail_page_gallery_ids', $restaurant_gal_array);
				}

				// updating company id
				$company_id = get_user_meta($publisher_id, 'foodbakery_company', true);
				update_post_meta($restaurant_id, 'foodbakery_restaurant_company', $company_id);

				// saving Restaurant Type
				$foodbakery_restaurant_type = foodbakery_get_input('foodbakery_restaurant_type', '');
				if (isset($_POST['foodbakery_restaurant_type'])) {
					update_post_meta($restaurant_id, 'foodbakery_restaurant_type', $foodbakery_restaurant_type);
				}

				// saving Custom Fields
				// all dynamic fields
				if (isset($_POST['foodbakery_cus_field'])) {
					$foodbakery_cus_fields = foodbakery_get_input('foodbakery_cus_field', '', 'ARRAY');
					if (is_array($foodbakery_cus_fields) && sizeof($foodbakery_cus_fields) > 0) {
						foreach ($foodbakery_cus_fields as $c_key => $c_val) {
							update_post_meta($restaurant_id, $c_key, $c_val);
						}
					}
				}

				// price save

				$restaurant_type_post = get_posts(array('fields' => 'ids', 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$foodbakery_restaurant_type", 'post_status' => 'publish'));
				$restaurant_type_id = isset($restaurant_type_post[0]) && $restaurant_type_post[0] != '' ? $restaurant_type_post[0] : 0;
				$foodbakery_restaurant_type_price = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_price', true);
				$foodbakery_restaurant_type_price = isset($foodbakery_restaurant_type_price) && $foodbakery_restaurant_type_price != '' ? $foodbakery_restaurant_type_price : 'off';
				$html = '';
				if ($foodbakery_restaurant_type_price == 'on') {
					$foodbakery_restaurant_price_options = foodbakery_get_input('foodbakery_restaurant_price_options', 'STRING');
					$foodbakery_restaurant_price = foodbakery_get_input('foodbakery_restaurant_price', 'STRING');

					if (isset($_POST['foodbakery_restaurant_price_options'])) {
						update_post_meta($restaurant_id, 'foodbakery_restaurant_price_options', $foodbakery_restaurant_price_options);
					}
					if (isset($_POST['foodbakery_restaurant_price'])) {
						update_post_meta($restaurant_id, 'foodbakery_restaurant_price', $foodbakery_restaurant_price);
					}
				}
				// end price save
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

				// adding restaurant tags
				$new_pkg_check = foodbakery_get_input('foodbakery_restaurant_new_package_used', '');
				if ($new_pkg_check == 'on') {
					$get_package_id = foodbakery_get_input('foodbakery_restaurant_package', '');
				} else {
					$active_package_key = foodbakery_get_input('foodbakery_restaurant_active_package', '');
					$active_package_key = explode('pt_', $active_package_key);
					$get_package_id = isset($active_package_key[0]) ? $active_package_key[0] : '';
				}

				if ($get_package_id == '') {
					$get_package_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_package', true);
				}

				$trans_id = $this->restaurant_trans_id($restaurant_id);

				if ($trans_id > 0 && $this->foodbakery_is_pkg_subscribed($get_package_id, $trans_id)) {
					$tags_limit = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_tags_num', true);
				} else {
					$foodbakery_pckg_data = get_post_meta($get_package_id, 'foodbakery_package_data', true);
					$tags_limit = isset($foodbakery_pckg_data['number_of_tags']['value']) ? $foodbakery_pckg_data['number_of_tags']['value'] : '';
				}

				if (isset($_POST['foodbakery_tags'])) {
					$foodbakery_restaurant_tags = foodbakery_get_input('foodbakery_tags', '', 'ARRAY');
					if (!empty($foodbakery_restaurant_tags) && is_array($foodbakery_restaurant_tags)) {
						if ($tags_limit && $tags_limit > 0) {
							$foodbakery_restaurant_tags = array_slice($foodbakery_restaurant_tags, 0, $tags_limit, true);
						}
						wp_set_post_terms($restaurant_id, $foodbakery_restaurant_tags, 'restaurant-tag', FALSE);
						update_post_meta($restaurant_id, 'foodbakery_restaurant_tags', $foodbakery_restaurant_tags);
					}
				}

				// saving restaurant features
				if (isset($_POST['foodbakery_restaurant_feature'])) {
					$foodbakery_restaurant_features = foodbakery_get_input('foodbakery_restaurant_feature', '', 'ARRAY');
					update_post_meta($restaurant_id, 'foodbakery_restaurant_feature_list', $foodbakery_restaurant_features);
				}

				// saving location fields
				$foodbakery_restaurant_country = foodbakery_get_input('foodbakery_post_loc_country_restaurant', '', 'STRING');
				$foodbakery_restaurant_state = foodbakery_get_input('foodbakery_post_loc_state_restaurant', '', 'STRING');
				$foodbakery_restaurant_city = foodbakery_get_input('foodbakery_post_loc_city_restaurant', '', 'STRING');
				$foodbakery_restaurant_town = foodbakery_get_input('foodbakery_post_loc_town_restaurant', '', 'STRING');
				$foodbakery_restaurant_comp_addr = foodbakery_get_input('foodbakery_post_loc_address_restaurant', '', 'STRING');
				$foodbakery_restaurant_loc_addr = foodbakery_get_input('foodbakery_post_loc_address_restaurant', '', 'STRING');
				$foodbakery_restaurant_loc_lat = foodbakery_get_input('foodbakery_post_loc_latitude_restaurant', '', 'STRING');
				$foodbakery_restaurant_loc_long = foodbakery_get_input('foodbakery_post_loc_longitude_restaurant', '', 'STRING');
				$foodbakery_restaurant_loc_zoom = foodbakery_get_input('foodbakery_post_loc_zoom_restaurant', '', 'STRING');
				$foodbakery_restaurant_loc_radius = foodbakery_get_input('foodbakery_loc_radius_restaurant', '', 'STRING');
				$foodbakery_add_new_loc = foodbakery_get_input('foodbakery_add_new_loc_restaurant', '', 'STRING');
				$foodbakery_loc_bounds_rest = foodbakery_get_input('foodbakery_loc_bounds_rest_restaurant', '', 'STRING');

				if (isset($_POST['foodbakery_post_loc_address_restaurant'])) {
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

				// saving restaurant menu categories
				if (isset($_POST['menu_cat_title'])) {
					$restaurant_menu_cat_titles = isset($_POST['menu_cat_title']) ? $_POST['menu_cat_title'] : '';
					update_post_meta($restaurant_id, 'menu_cat_titles', $restaurant_menu_cat_titles);

					$restaurant_menu_cat_descs = isset($_POST['menu_cat_desc']) ? $_POST['menu_cat_desc'] : '';
					update_post_meta($restaurant_id, 'menu_cat_descs', $restaurant_menu_cat_descs);
				}

				// saving restaurant services
				$foodbakery_restaurant_menu_item_title = foodbakery_get_input('menu_item_title', '', 'ARRAY');
				$foodbakery_restaurants_menu = foodbakery_get_input('restaurant_menu', '', 'ARRAY');
				$foodbakery_restaurant_menu_item_price = foodbakery_get_input('menu_item_price', '', 'ARRAY');
				$foodbakery_restaurant_menu_item_icon = foodbakery_get_input('menu_item_icon', '', 'ARRAY');
				$foodbakery_restaurant_menu_item_desc = foodbakery_get_input('menu_item_desc', '', 'ARRAY');
				$foodbakery_restaurant_menu_item_extra = foodbakery_get_input('menu_item_extra', '', 'ARRAY');
				$foodbakery_restaurant_menu_item_action = foodbakery_get_input('menu_item_action', '', 'ARRAY');

				if (isset($_POST['menu_item_title']) && is_array($foodbakery_restaurant_menu_item_title) && sizeof($foodbakery_restaurant_menu_item_title) > 0) {
					$menu_items_array = array();
					foreach ($foodbakery_restaurant_menu_item_title as $key => $menu_item) {
						$menu_item_action = isset($foodbakery_restaurant_menu_item_action[$key]) ? $foodbakery_restaurant_menu_item_action[$key] : '';
						if (count($menu_item) > 0 && $menu_item != '' && $menu_item_action != 'add') {
							$menu_items_array[] = array(
								'menu_item_title' => $menu_item,
								'restaurant_menu' => isset($foodbakery_restaurants_menu[$key]) ? $foodbakery_restaurants_menu[$key] : '',
								'menu_item_description' => isset($foodbakery_restaurant_menu_item_desc[$key]) ? $foodbakery_restaurant_menu_item_desc[$key] : '',
								'menu_item_icon' => isset($foodbakery_restaurant_menu_item_icon[$key]) ? $foodbakery_restaurant_menu_item_icon[$key] : '',
								'menu_item_price' => isset($foodbakery_restaurant_menu_item_price[$key]) ? $foodbakery_restaurant_menu_item_price[$key] : '',
								'menu_item_extra' => isset($foodbakery_restaurant_menu_item_extra[$key]) ? $foodbakery_restaurant_menu_item_extra[$key] : '',
							);
						}
					}
					update_post_meta($restaurant_id, 'foodbakery_menu_items', $menu_items_array);
				}

				// saving opening hours
				if (isset($_POST['foodbakery_opening_hour'])) {
					$opening_hours_list = foodbakery_get_input('foodbakery_opening_hour', '', 'ARRAY');
					$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
					foreach ($days as $key => $day) {
						if (isset($opening_hours_list[$day])) {
							$day_status = ($opening_hours_list[$day]['day_status'] != '' ? $opening_hours_list[$day]['day_status'] : 'Off');
							$opening_time = ($opening_hours_list[$day]['opening_time'] != '' ? $opening_hours_list[$day]['opening_time'] : '');
							if ($opening_time != '') {
								$opening_time = strtotime('2016-01-01 ' . $opening_time);
							}
							$closing_time = ($opening_hours_list[$day]['closing_time'] != '' ? $opening_hours_list[$day]['closing_time'] : '');
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

				// saving book off days
				if (isset($_POST['foodbakery_restaurant_off_days'])) {
					$foodbakery_off_days = foodbakery_get_input('foodbakery_restaurant_off_days', '', 'ARRAY');
					update_post_meta($restaurant_id, 'foodbakery_calendar', $foodbakery_off_days);
				}

				// Check Free or Paid restaurant
				// Assign Membership in case of paid
				// Assign Status of restaurant
				do_action('foodbakery_restaurant_add_save_assignments', $restaurant_id, $publisher_id);
			}
		}

		/**
		 * Assigning Status for Restaurant
		 * @return
		 */
		public function restaurant_update_status($restaurant_id = '')
		{
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
		 * checking publisher own post
		 * @return boolean
		 */
		public function is_publisher_restaurant($restaurant_id = '')
		{
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
		public function is_package($id = '')
		{
			$package = get_post($id);
			if (isset($package->post_type) && $package->post_type == 'packages') {
				return true;
			}
			return false;
		}

		/**
		 * Checking is form submit
		 * @return boolean
		 */
		public function is_form_submit()
		{

			if (isset($_POST['foodbakery_restaurant_title'])) {
				return true;
			}
			return false;
		}

		/**
		 * Get Restaurant Content
		 * @return markup
		 */
		public function restaurant_post_content($id = '')
		{

			$content = get_post($id);
			$content = $content->post_content;
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			return apply_filters('foodbakery_front_restaurant_post_content', $content, $id);
			// usage :: add_filter('foodbakery_front_restaurant_post_content', 'my_callback_function', 10, 2);
		}

		/**
		 * Get Restaurant Transaction id
		 * @return id
		 */
		public function restaurant_trans_id($restaurant_id = '')
		{

			$get_subscripton_data = get_post_meta($restaurant_id, "package_subscripton_data", true);
			if (is_array($get_subscripton_data)) {
				$last_subs = end($get_subscripton_data);
				$trans_id = isset($last_subs['transaction_id']) ? $last_subs['transaction_id'] : false;
				return $trans_id;
			}
		}

		/**
		 * Check Free or Paid restaurant
		 * Assign Membership in case of paid
		 * Assign Status of restaurant
		 * @return
		 */
		public function restaurant_save_assignments($restaurant_id = '', $publisher_id = '')
		{
			global $foodbakery_plugin_options;
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

					// Assign expire date
					$foodbakery_ins_exp = current_time('Y-m-d H:i:s');
					if ($foodbakery_restaurant_default_expiry != '' && is_numeric($foodbakery_restaurant_default_expiry) && $foodbakery_restaurant_default_expiry > 0) {
						$foodbakery_ins_exp = $this->date_conv($foodbakery_restaurant_default_expiry, 'days');
					}
					update_post_meta($restaurant_id, 'foodbakery_restaurant_expired', strtotime($foodbakery_ins_exp));

					// Assign without package true
					update_post_meta($restaurant_id, 'foodbakery_restaurant_without_package', '1');

					// Assign Status of restaurant
					do_action('foodbakery_restaurant_add_assign_status', $restaurant_id);
				}
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
									do_action('foodbakery_restaurant_assign_trans_meta', $restaurant_id, $active_pckg_trans_id);

									// Assign Status of restaurant
									do_action('foodbakery_restaurant_add_assign_status', $restaurant_id);
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
								do_action('foodbakery_restaurant_assign_trans_meta', $restaurant_id, $active_pckg_trans_id);

								// Assign Status of restaurant
								do_action('foodbakery_restaurant_add_assign_status', $restaurant_id);
							}
						}
					}
					// end of using existing package
				}
				// end assigning packages
				// and payment processs
			}

			// submit msg
			if ($is_updating) {
				$sumbit_msg = esc_html__('Restaurant Updated.', 'foodbakery');
				$user_data = wp_get_current_user();
				// Restaurant not approved
				do_action('foodbakery_restaurant_updated_on_admin', $user_data, $restaurant_id);
			} else {
				if (isset($_GET['restaurant_id'])) {
					$sumbit_msg = esc_html__('Restaurant Added Successfully.', 'foodbakery');
				} else {
					$sumbit_msg = esc_html__('Membership Updated.', 'foodbakery');
				}
			}
			$this->restaurant_submit_msg($sumbit_msg);
		}

		/**
		 * Adding Transaction
		 * @return id
		 */
		public function foodbakery_restaurant_add_transaction($type = '', $restaurant_id = 0, $package_id = 0, $publisher_id = '')
		{
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
				update_post_meta($trans_id, 'foodbakery_currency', foodbakery_base_currency_sign());
				update_post_meta($trans_id, 'foodbakery_currency_obj', foodbakery_get_base_currency());
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

							$foodbakery_vat_amount = $foodbakery_trans_amount * ($foodbakery_pay_vat / 100);

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

					// update restaurant expiry, featured, top category
					// this change will be temporary
					$foodbakery_package_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_package', true);
					if ($foodbakery_package_id) {

						$foodbakery_package_data = get_post_meta($foodbakery_package_id, 'foodbakery_package_data', true);

						$restaurant_duration = isset($foodbakery_package_data['restaurant_duration']['value']) ? $foodbakery_package_data['restaurant_duration']['value'] : 0;

						// calculating restaurant expiry date
						$foodbakery_trans_restaurant_expiry = $this->date_conv($restaurant_duration, 'days');
						update_post_meta($restaurant_id, 'foodbakery_restaurant_expired', strtotime($foodbakery_trans_restaurant_expiry));
					}
					//update_post_meta($restaurant_id, "foodbakery_restaurant_expired", strtotime(current_time('Y-m-d H:i:s')));
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_featured", '');
					update_post_meta($restaurant_id, "foodbakery_restaurant_is_top_cat", '');

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
					do_action('foodbakery_restaurant_assign_trans_meta', $restaurant_id, $trans_id);
				}

				// Payment Process
				if ($pay_process) {
					// update restaurant status temporarily
					// as pending
					//update_post_meta($restaurant_id, 'foodbakery_restaurant_status', 'pending');
					update_post_meta($restaurant_id, 'foodbakery_restaurant_status', 'active');

					$user_data = wp_get_current_user();
					// Restaurant pending email
					// Redirecting parameters
					$foodbakery_payment_params = array(
						'action' => 'restaurant-package',
						'trans_id' => $trans_id,
					);
					$foodbakery_payment_page = isset($foodbakery_plugin_options['foodbakery_package_page']) ? $foodbakery_plugin_options['foodbakery_package_page'] : '';

					$foodbakery_payment_page_link = $foodbakery_payment_page != '' ? get_permalink($foodbakery_payment_page) : '';

					// Redirecting to Payment process on next page
					if ($foodbakery_payment_page_link != '' && $foodbakery_trans_amount > 0) {

						$redirect_form_id = rand(1000000, 9999999);
						$redirect_html = '
						<form id="form-' . $redirect_form_id . '" method="get" action="' . $foodbakery_payment_page_link . '">
						<input type="hidden" name="action" value="restaurant-package">
						<input type="hidden" name="trans_id" value="' . $trans_id . '">';
						if (isset($_GET['lang'])) {
							$redirect_html .= '<input type="hidden" name="lang" value="' . $_GET['lang'] . '">';
						}
						$redirect_html .= '
						</form>
						<script>document.getElementById("form-' . $redirect_form_id . '").submit();</script>';
						echo force_balance_tags($redirect_html);
						wp_die();
					}
				} else {
					$msg_arr = array('msg' => esc_html__('Membership subscribed successfully.', 'foodbakery'), 'type' => 'success');
					$msg_arr = json_encode($msg_arr);
					echo '
					<script>
					jQuery(document).ready(function () {
						foodbakery_show_response(' . $msg_arr . ');
					});
					</script>';

					// Assign Status of restaurant
					// This will be case of Free Membership
					do_action('foodbakery_restaurant_add_assign_status', $restaurant_id);
				}
			}
			return apply_filters('foodbakery_restaurant_add_transaction', $transaction_detail, $type, $restaurant_id, $package_id, $publisher_id);
			// usage :: add_filter('foodbakery_restaurant_add_transaction', 'my_callback_function', 10, 5);
		}

		/**
		 * Check user package subscription
		 * @return id
		 */
		public function foodbakery_is_pkg_subscribed($foodbakery_package_id = 0, $trans_id = 0)
		{
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
			// usage :: add_filter('foodbakery_restaurant_is_package_subscribe', 'my_callback_function', 10, 3);
		}

		/**
		 * Get all active packages of current user
		 * @return array
		 */
		public function user_all_active_pkgs()
		{
			global $post, $current_user;

			$company_id = foodbakery_company_id_form_user_id($current_user->ID);

			$trans_ids = array();
			$foodbakery_current_date = strtotime(date('d-m-Y'));
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'package-orders',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
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
						$trans_ids[] = $post->ID;
					}
				endwhile;
				wp_reset_postdata();
			}

			return apply_filters('foodbakery_restaurant_user_active_packages', $trans_ids);
			// usage :: add_filter('foodbakery_restaurant_user_active_packages', 'my_callback_function', 10, 1);
		}

		/**
		 * Get User Membership Trans
		 * @return id
		 */
		public function get_user_package_trans($foodbakery_package_id = 0, $trans_id = 0)
		{
			global $post, $current_user;

			$company_id = foodbakery_company_id_form_user_id($current_user->ID);

			if ($trans_id == '') {
				$trans_id = 0;
			}
			$transaction_id = false;
			$args = array(
				'posts_per_page' => "1",
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
					$foodbakery_trnasaction_id = $post->ID;
				endwhile;
				wp_reset_postdata();
			}

			if (isset($foodbakery_trnasaction_id) && $foodbakery_trnasaction_id > 0) {
				$transaction_id = $foodbakery_trnasaction_id;
			}
			return apply_filters('foodbakery_restaurant_user_package_trans', $transaction_id, $foodbakery_package_id, $trans_id);
			// usage :: add_filter('foodbakery_restaurant_user_package_trans', 'my_callback_function', 10, 3);
		}

		/**
		 * Purchased Membership Info Field Create
		 * @return markup
		 */
		public function purchase_package_info_field_show($value = '', $label = '', $value_plus = '')
		{

			if ($value != '' && $value != 'on') {
				$html = '<li><label>' . $label . '</label><span>' . esc_html__($value, 'foodbakery') . ' ' . $value_plus . '</span></li>';
			} else if ($value != '' && $value == 'on') {
				$html = '<li><label>' . $label . '</label><span><i class="icon-check"></i></span></li>';
			} else {
				$html = '<li><label>' . $label . '</label><span><i class="icon-minus"></i></span></li>';
			}

			return $html;
		}

		/**
		 * Get Subscribe Membership info
		 * @return html
		 */
		public function subs_package_info($package_id = 0, $trans_id = 0)
		{
			global $restaurant_add_counter;
			$html = '';
			$inner_html = '';

			if ($user_package = $this->get_user_package_trans($package_id, $trans_id)) {
				$title_id = $user_package != '' ? get_the_title($user_package) : '';
				$trans_packg_id = get_post_meta($trans_id, 'foodbakery_transaction_package', true);
				$packg_title = $trans_packg_id != '' ? get_the_title($trans_packg_id) : '';

				$trans_packg_expiry = get_post_meta($trans_id, 'foodbakery_transaction_expiry_date', true);
				$trans_packg_list_num = get_post_meta($trans_id, 'foodbakery_transaction_restaurants', true);
				$trans_packg_list_expire = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_expiry', true);

				$trans_packg_feature_one = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_feature_list', true);
				$trans_packg_top_cat_one = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_top_cat_list', true);


				$trans_pics_num = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_pic_num', true);

				$trans_tags_num = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_tags_num', true);
				$trans_reviews = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_reviews', true);

				$trans_phone = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_phone', true);
				$trans_website = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_website', true);
				$trans_social = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_social', true);
				$trans_ror = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_ror', true);
				$trans_dynamic_f = get_post_meta($trans_id, 'foodbakery_transaction_dynamic', true);

				$pkg_expire_date = date_i18n(get_option('date_format'), $trans_packg_expiry);

				$html .= '<div id="package-detail-' . $package_id . 'pt_' . $trans_id . '" style="display:none;" class="package-info-sec restaurant-info-sec">';
				$html .= '<div class="row">';
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
				$html .= '<ul class="restaurant-pkg-points">';

				$html .= $this->purchase_package_info_field_show($pkg_expire_date, esc_html__('Expiry Date', 'foodbakery'));
				$html .= $this->purchase_package_info_field_show($trans_packg_list_expire, esc_html__('Restaurant Duration', 'foodbakery'), esc_html__('Days', 'foodbakery'));
				$html .= $this->purchase_package_info_field_show($trans_packg_feature_one, esc_html__('Feature Restaurant', 'foodbakery'));
				$html .= $this->purchase_package_info_field_show($trans_packg_top_cat_one, esc_html__('Top Category Restaurant', 'foodbakery'));



				$html .= $this->purchase_package_info_field_show($trans_pics_num, esc_html__('Number of Pictures', 'foodbakery'));

				$html .= $this->purchase_package_info_field_show($trans_tags_num, esc_html__('Number of Tags', 'foodbakery'));
				$html .= $this->purchase_package_info_field_show($trans_reviews, esc_html__('Reviews', 'foodbakery'));

				$html .= $this->purchase_package_info_field_show($trans_phone, esc_html__('Phone Number', 'foodbakery'));
				$html .= $this->purchase_package_info_field_show($trans_website, esc_html__('Website Link', 'foodbakery'));
				$html .= $this->purchase_package_info_field_show($trans_social, esc_html__('Social Impressions Reach', 'foodbakery'));
				$html .= $this->purchase_package_info_field_show($trans_ror, esc_html__('Respond to Reviews', 'foodbakery'));

				$dyn_fields_html = '';
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
				// emd of Dynamic fields
				// other Features



				$html .= '
		</ul>
		</div>';

				if (absint($foodbakery_featured_remain) > 0 || absint($foodbakery_top_cat_remain) > 0) {
					$html .= '
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

					if (absint($foodbakery_featured_remain) > 0) {
						$html .= '
				<div class="package-featured pakg-switch">
					<span>' . esc_html__('Featured', 'foodbakery') . ' :</span>
					<input id="package-featured-' . $package_id . 'pt_' . $trans_id . '" type="checkbox" class="cmn-toggle cmn-toggle-round" name="foodbakery_restaurant_featured">
					<label for="package-featured-' . $package_id . 'pt_' . $trans_id . '"></label>
				</div>';
					}

					if (absint($foodbakery_top_cat_remain) > 0) {
						$html .= '
				<div class="package-top-cat pakg-switch">
					<span>' . esc_html__('Top Category', 'foodbakery') . ' :</span>
					<input id="package-top-cat-' . $package_id . 'pt_' . $trans_id . '" type="checkbox" class="cmn-toggle cmn-toggle-round" name="foodbakery_restaurant_top_cat">
					<label for="package-top-cat-' . $package_id . 'pt_' . $trans_id . '"></label>
				</div>';
					}

					$html .= '
			</div>';
				}

				$html .= '
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="pgk-action-btns">
						<a href="javascript:void(0);" data-id="' . $package_id . 'pt_' . $trans_id . '" class="pkg-choose-btn">' . esc_html__('Choose Membership', 'foodbakery') . '</a>
						<a href="javascript:void(0);" data-id="' . $package_id . 'pt_' . $trans_id . '" class="pkg-cancel-btn">' . esc_html__('Cancel', 'foodbakery') . '</a>
					</div>
				</div>
				</div>
				</div>';
			}

			return apply_filters('foodbakery_restaurant_user_subs_package_info', $html, $package_id, $trans_id);
			// usage :: add_filter('foodbakery_restaurant_user_subs_package_info', 'my_callback_function', 10, 3);
		}

		/**
		 * Membership Info Field Create
		 * @return markup
		 */
		public function package_info_field_show($info_meta = '', $index = '', $label = '', $value_plus = '')
		{
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
		public function new_package_info($package_id = 0)
		{
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
			$html .= $this->package_info_field_show($trans_all_meta, 'number_of_pictures', esc_html__('Number of Pictures', 'foodbakery'));
			$html .= $this->package_info_field_show($trans_all_meta, 'number_of_documents', esc_html__('Number of Documents', 'foodbakery'));
			$html .= $this->package_info_field_show($trans_all_meta, 'number_of_tags', esc_html__('Number of Tags', 'foodbakery'));
			$html .= $this->package_info_field_show($trans_all_meta, 'reviews', esc_html__('Reviews', 'foodbakery'));
			//
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


			if ((isset($trans_all_meta['number_of_featured_restaurants']['value']) && $trans_all_meta['number_of_featured_restaurants']['value'] == 'on') || (isset($trans_all_meta['number_of_top_cat_restaurants']['value']) && $trans_all_meta['number_of_top_cat_restaurants']['value'] == 'on')) {
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
			</div>';
			$html .= '
			</div>
			</div>';

			return apply_filters('foodbakery_restaurant_user_new_package_info', $html, $package_id);
			// usage :: add_filter('foodbakery_restaurant_user_new_package_info', 'my_callback_function', 10, 2);
		}

		/**
		 * Updating transaction meta into restaurant meta
		 * @return
		 */
		public function restaurant_assign_meta($restaurant_id = '', $trans_id = '')
		{
			$assign_array = array();

			$trans_get_value = get_post_meta($trans_id, 'foodbakery_transaction_restaurant_pic_num', true);
			$assign_array[] = array(
				'key' => 'foodbakery_transaction_restaurant_pic_num',
				'label' => esc_html__('Number of Pictures', 'foodbakery'),
				'value' => $trans_get_value,
			);

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
		 * User authentication
		 * @return Ajax
		 */
		public function user_authentication()
		{

			$field_type = isset($_POST['field_type']) ? $_POST['field_type'] : '';
			$field_val = isset($_POST['field_val']) ? $_POST['field_val'] : '';

			if ($field_type == 'username') {
				if (username_exists($field_val)) {
					$msg = esc_html__('This Username already exists.', 'foodbakery');
					$action = 'false';
				} else if (!validate_username($field_val)) {
					$msg = esc_html__('Please enter a valid Username. You can only enter alphanumeric value and only ( _ ) longer than or equals 5 chars.', 'foodbakery');
					$action = 'false';
				} else {
					$msg = esc_html__('Username available.', 'foodbakery');
					$action = 'true';
				}
			} else if ($field_type == 'useremail') {
				if (email_exists($field_val)) {
					$msg = esc_html__('This email is already exist.', 'foodbakery');
					$action = 'false';
				} else if (!filter_var($field_val, FILTER_VALIDATE_EMAIL)) {
					$msg = esc_html__('Please enter a valid email address.', 'foodbakery');
					$action = 'false';
				} else {
					$msg = esc_html__('Email available.', 'foodbakery');
					$action = 'true';
				}
			} else {
				$msg = esc_html__('Error! There is some Problem.', 'foodbakery');
				$action = 'false';
			}

			echo json_encode(array('msg' => $msg, 'action' => $action));
			die;
		}

		public function restaurant_gallery_upload($Fieldname = 'media_upload', $restaurant_id = '')
		{
			$img_resized_name = '';
			$restaurant_gallery = array();
			$count = 0;

			if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

				$multi_files = isset($_FILES[$Fieldname]) ? $_FILES[$Fieldname] : '';

				if (isset($multi_files['name']) && is_array($multi_files['name'])) {
					$img_name_array = array();
					foreach ($multi_files['name'] as $multi_key => $multi_value) {
						if ($multi_files['name'][$multi_key]) {
							$loop_file = array(
								'name' => $multi_files['name'][$multi_key],
								'type' => $multi_files['type'][$multi_key],
								'tmp_name' => $multi_files['tmp_name'][$multi_key],
								'error' => $multi_files['error'][$multi_key],
								'size' => $multi_files['size'][$multi_key]
							);

							$json = array();
							require_once ABSPATH . 'wp-admin/includes/image.php';
							require_once ABSPATH . 'wp-admin/includes/file.php';
							require_once ABSPATH . 'wp-admin/includes/media.php';
							$allowed_image_types = array(
								'jpg|jpeg|jpe' => 'image/jpeg',
								'png' => 'image/png',
								'gif' => 'image/gif',
							);

							$status = wp_handle_upload($loop_file, array('test_form' => false, 'mimes' => $allowed_image_types));

							if (empty($status['error'])) {

								$image = wp_get_image_editor($status['file']);
								$img_resized_name = $status['file'];

								if (is_wp_error($image)) {

									echo '<span class="error-msg">' . $image->get_error_message() . '</span>';
								} else {
									$wp_upload_dir = wp_upload_dir();
									$img_name_array[] = isset($status['url']) ? $status['url'] : '';
									$filename = $img_name_array[$count];
									$filetype = wp_check_filetype(basename($filename), null);

									if ($filename != '') {
										// Prepare an array of post data for the attachment.

										$attachment = array(
											'guid' => ($filename),
											'post_mime_type' => $filetype['type'],
											'post_title' => preg_replace('/\.[^.]+$/', '', ($loop_file['name'])),
											'post_content' => '',
											'post_status' => 'inherit'
										);
										require_once(ABSPATH . 'wp-admin/includes/image.php');
										// Insert the attachment.
										$attach_id = wp_insert_attachment($attachment, $status['file']);
										if ($restaurant_id != '') {
											wp_update_post(
												array(
													'ID' => $attach_id,
													'post_parent' => $restaurant_id
												)
											);
										}
										// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
										$attach_data = wp_generate_attachment_metadata($attach_id, $status['file']);
										wp_update_attachment_metadata($attach_id, $attach_data);

										$restaurant_gallery[] = $attach_id;
										$count++;
									}
								}
							}
						}
					}

					$img_resized_name = $restaurant_gallery;
				} else {
					$img_resized_name = '';
				}
			}

			return $img_resized_name;
		}

		/**
		 * Date plus period
		 * @return date
		 */
		public function date_conv($duration, $format = 'days')
		{
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
		public function merge_in_array($array, $value = '', $with_array = true)
		{
			$ret_array = '';
			if (is_array($array) && sizeof($array) > 0 && $value != '') {
				$array[] = $value;
				$ret_array = $array;
			} else if (!is_array($array) && $value != '') {
				$ret_array = $with_array ? array($value) : $value;
			}
			return $ret_array;
		}

		/**
		 * Restaurant Tag Open
		 * @return markup
		 */
		public function restaurant_add_tag_before()
		{
			global $restaurant_add_counter;
			echo '<ul id="foodbakery-dev-main-con-' . $restaurant_add_counter . '">';
		}

		/**
		 * Restaurant Tag Close
		 * @return markup
		 */
		public function restaurant_add_tag_after()
		{

			echo '</ul>';
		}

		/**
		 * Steps before
		 * @return markup
		 */
		public function before_restaurant($html = '')
		{
			global $foodbakery_plugin_options, $Payment_Processing;
			$foodbakery_restaurant_announce_title = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_title']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_title'] : '';
			$foodbakery_restaurant_announce_description = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_description']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_description'] : '';
			$foodbakery_announce_bg_color = isset($foodbakery_plugin_options['foodbakery_announce_bg_color']) ? $foodbakery_plugin_options['foodbakery_announce_bg_color'] : '#2b8dc4';
			$restaurant_color = 'style="background-color:' . $foodbakery_announce_bg_color . '"';
			$user = wp_get_current_user();

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
								$temp_order = $temp_order - 4;
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
		 * Restaurant Submit Msg
		 * @return markup
		 */
		public function restaurant_submit_msg($msg = '')
		{

			$html = '';
			if ($msg != '') {
				$foodbakery_restaurant_image = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-image4x3.jpg';
				$msg_arr = array('msg' => $msg, 'type' => 'success');
				$msg_arr = json_encode($msg_arr);
				$html = '';
				if ((isset($_REQUEST['restaurant_tab']) && $_REQUEST['restaurant_tab'] == 'settings') || !isset($_REQUEST['restaurant_tab'])) {
					$html .= '
					<script>
					jQuery(document).ready(function () {
						if( jQuery(".foodbakery-gallery-holder .item-thumb").find("img.thumbnail").length > 0 ){
							var restaurent_image_src = jQuery( ".foodbakery-gallery-holder .item-thumb" ).find("img.thumbnail").attr( "src");
						}else{
							var restaurent_image_src = "' . $foodbakery_restaurant_image . '";
						}
						jQuery( ".user-dashboard-menu .profile-image" ).find("img").attr( "src", restaurent_image_src);
						jQuery( ".company-info-detail .company-info .img-holder" ).find("img").attr( "src", restaurent_image_src);
					});
					</script>';
				}

				$html .= '
				<script>
				jQuery(document).ready(function () {
					foodbakery_show_response(' . $msg_arr . '); 
				});
				</script>';
			}
			echo force_balance_tags($html);
		}

		/**
		 * Steps after
		 * @return markup
		 */
		public function after_restaurant($html = '')
		{
			global $restaurant_add_counter;
			$restaurant_id = foodbakery_get_input('restaurant_id', 0);
			$html .= '<li style="display: none;">'
				. '<input type="hidden" name="form_rand_id" value="' . $restaurant_add_counter . '">'
				. '<input type="hidden" name="restaurant_id" value="' . $restaurant_id . '">'
				. '</li>';
			echo force_balance_tags($html);
		}

		/**
		 * Social Post
		 * @return
		 */
		public function social_post_after_activation($restaurant_id)
		{

			global $foodbakery_plugin_options;

			if ($restaurant_id == '') {
				return;
			}

			$restaurant_post = get_post($restaurant_id);

			if (is_object($restaurant_post)) {
				$name = $restaurant_post->post_title;
				$name = apply_filters('the_title', $name);
				$name = html_entity_decode($name, ENT_QUOTES, get_bloginfo('charset'));
				$name = strip_tags($name);
				$name = strip_shortcodes($name);

				$content = $restaurant_post->post_content;
				$content = apply_filters('the_content', $content);
				$content = wp_kses($content, array());

				$description = $content;

				$excerpt = '';
				$caption = '';
				$user_nicename = '';

				$post_thumbnail_id = get_post_thumbnail_id($restaurant_id);
				$attachmenturl = '';
				if ($post_thumbnail_id) {
					$attachmenturl = wp_get_attachment_url($post_thumbnail_id);
				}
				$link = get_permalink($restaurant_post->ID);
			} else {
				return;
			}

			// Twitter Posting Start
			$foodbakery_twitter_posting_switch = isset($foodbakery_plugin_options['foodbakery_twitter_autopost_switch']) ? $foodbakery_plugin_options['foodbakery_twitter_autopost_switch'] : '';

			if ($foodbakery_twitter_posting_switch == 'on') {

				if (!class_exists('SMAPTwitterOAuth')) {
					require_once(dirname(__FILE__) . '/social-api/twitteroauth.php');
				}

				$tappid = isset($foodbakery_plugin_options['foodbakery_consumer_key']) ? $foodbakery_plugin_options['foodbakery_consumer_key'] : '';
				$tappsecret = isset($foodbakery_plugin_options['foodbakery_consumer_secret']) ? $foodbakery_plugin_options['foodbakery_consumer_secret'] : '';
				$taccess_token = isset($foodbakery_plugin_options['foodbakery_access_token']) ? $foodbakery_plugin_options['foodbakery_access_token'] : '';
				$taccess_token_secret = isset($foodbakery_plugin_options['foodbakery_access_token_secret']) ? $foodbakery_plugin_options['foodbakery_access_token_secret'] : '';

				$post_twitter_image_permission = 1;


				$messagetopost = '{POST_TITLE} - {PERMALINK}';

				$img_status = "";
				if ($post_twitter_image_permission == 1) {

					$img = array();
					if ($attachmenturl != "")
						$img = wp_remote_get($attachmenturl);

					if (is_array($img)) {
						if (isset($img['body']) && trim($img['body']) != '') {
							$image_found = 1;
							if (($img['headers']['content-length']) && trim($img['headers']['content-length']) != '') {
								$img_size = $img['headers']['content-length'] / (1024 * 1024);
								if ($img_size > 3) {
									$image_found = 0;
									$img_status = "Image skipped(greater than 3MB)";
								}
							}

							$img = $img['body'];
						} else
							$image_found = 0;
					}
				}
				///Twitter upload image end/////

				$messagetopost = str_replace("&nbsp;", "", $messagetopost);

				preg_match_all("/{(.+?)}/i", $messagetopost, $matches);
				$matches1 = $matches[1];
				$substring = "";
				$islink = 0;
				$issubstr = 0;
				$len = 118;
				if ($image_found == 1)
					$len = $len - 24;

				foreach ($matches1 as $key => $val) {
					$val = "{" . $val . "}";
					if ($val == "{POST_TITLE}") {
						$replace = $name;
					}
					if ($val == "{POST_CONTENT}") {
						$replace = $description;
					}
					if ($val == "{PERMALINK}") {
						$replace = "{PERMALINK}";
						$islink = 1;
					}
					if ($val == "{POST_EXCERPT}") {
						$replace = $excerpt;
					}
					if ($val == "{BLOG_TITLE}")
						$replace = $caption;

					if ($val == "{USER_NICENAME}")
						$replace = $user_nicename;



					$append = mb_substr($messagetopost, 0, mb_strpos($messagetopost, $val));

					if (mb_strlen($append) < ($len - mb_strlen($substring))) {
						$substring .= $append;
					} else if ($issubstr == 0) {
						$avl = $len - mb_strlen($substring) - 4;
						if ($avl > 0)
							$substring .= mb_substr($append, 0, $avl) . "...";

						$issubstr = 1;
					}



					if ($replace == "{PERMALINK}") {
						$chkstr = mb_substr($substring, 0, -1);
						if ($chkstr != " ") {
							$substring .= " " . $replace;
							$len = $len + 12;
						} else {
							$substring .= $replace;
							$len = $len + 11;
						}
					} else {

						if (mb_strlen($replace) < ($len - mb_strlen($substring))) {
							$substring .= $replace;
						} else if ($issubstr == 0) {

							$avl = $len - mb_strlen($substring) - 4;
							if ($avl > 0)
								$substring .= mb_substr($replace, 0, $avl) . "...";

							$issubstr = 1;
						}
					}
					$messagetopost = mb_substr($messagetopost, mb_strpos($messagetopost, $val) + strlen($val));
				}

				if ($islink == 1)
					$substring = str_replace('{PERMALINK}', $link, $substring);

				$twobj = new SMAPTwitterOAuth(array('consumer_key' => $tappid, 'consumer_secret' => $tappsecret, 'user_token' => $taccess_token, 'user_secret' => $taccess_token_secret, 'curl_ssl_verifypeer' => false));

				if ($image_found == 1 && $post_twitter_image_permission == 1) {
					$resultfrtw = $twobj->request('POST', 'https://api.twitter.com/1.1/statuses/update_with_media.json', array('media[]' => $img, 'status' => $substring), true, true);

					if ($resultfrtw != 200) {
						if ($twobj->response['response'] != "")
							$tw_publish_status["statuses/update_with_media"] = print_r($twobj->response['response'], true);
						else
							$tw_publish_status["statuses/update_with_media"] = $resultfrtw;
					}
				} else {
					$resultfrtw = $twobj->request('POST', $twobj->url('1.1/statuses/update'), array('status' => $substring));

					if ($resultfrtw != 200) {
						if ($twobj->response['response'] != "")
							$tw_publish_status["statuses/update"] = print_r($twobj->response['response'], true);
						else
							$tw_publish_status["statuses/update"] = $resultfrtw;
					} else if ($img_status != "")
						$tw_publish_status["statuses/update_with_media"] = $img_status;
				}
			}

			// Linkedin
			$lk_client_id = isset($foodbakery_plugin_options['foodbakery_linkedin_app_id']) ? $foodbakery_plugin_options['foodbakery_linkedin_app_id'] : '';
			$lk_secret_id = isset($foodbakery_plugin_options['foodbakery_linkedin_secret']) ? $foodbakery_plugin_options['foodbakery_linkedin_secret'] : '';
			$lk_posting_switch = isset($foodbakery_plugin_options['foodbakery_linkedin_autopost_switch']) ? $foodbakery_plugin_options['foodbakery_linkedin_autopost_switch'] : '';

			$lnpost_permission = 1;

			if ($lk_posting_switch == 'on' && $lk_client_id != "" && $lk_secret_id != "" && $lnpost_permission == 1) {
				if (!class_exists('SMAPLinkedInOAuth2')) {
					require_once(dirname(__FILE__) . '/social-api/linkedin.php');
				}

				$authorized_access_token = isset($foodbakery_plugin_options['foodbakery_linkedin_access_token']) ? $foodbakery_plugin_options['foodbakery_linkedin_access_token'] : '';


				$lmessagetopost = '{PERMALINK}';

				$contentln = array();

				$description_li = foodbakery_restaurant_string_limit($description, 362);
				$caption_li = foodbakery_restaurant_string_limit($caption, 200);
				$name_li = foodbakery_restaurant_string_limit($name, 200);

				$message1 = str_replace('{POST_TITLE}', $name, $lmessagetopost);
				$message2 = str_replace('{BLOG_TITLE}', $caption, $message1);
				$message3 = str_replace('{PERMALINK}', $link, $message2);
				$message4 = str_replace('{POST_EXCERPT}', $excerpt, $message3);
				$message5 = str_replace('{POST_CONTENT}', $description, $message4);
				$message5 = str_replace('{USER_NICENAME}', $user_nicename, $message5);

				$message5 = str_replace("&nbsp;", "", $message5);

				$contentln['comment'] = $message5;
				$contentln['content']['title'] = $name_li;
				$contentln['content']['submitted-url'] = $link;
				if ($attachmenturl != "") {
					$contentln['content']['submitted-image-url'] = $attachmenturl;
				}
				$contentln['content']['description'] = $description_li;

				$contentln['visibility']['code'] = 'anyone';

				$ln_publish_status = array();

				$ObjLinkedin = new SMAPLinkedInOAuth2($authorized_access_token);
				$contentln = foodbakery_linkedin_attachment_metas($contentln, $link);

				$arrResponse = $ObjLinkedin->shareStatus($contentln);
			}

			// Facebook
			$fb_posting_switch = isset($foodbakery_plugin_options['foodbakery_facebook_autopost_switch']) ? $foodbakery_plugin_options['foodbakery_facebook_autopost_switch'] : '';

			$fb_app_id = isset($foodbakery_plugin_options['foodbakery_facebook_app_id']) ? $foodbakery_plugin_options['foodbakery_facebook_app_id'] : '';
			$fb_secret = isset($foodbakery_plugin_options['foodbakery_facebook_secret']) ? $foodbakery_plugin_options['foodbakery_facebook_secret'] : '';
			$fb_access_token = isset($foodbakery_plugin_options['foodbakery_facebook_access_token']) ? $foodbakery_plugin_options['foodbakery_facebook_access_token'] : '';

			if ($fb_posting_switch == 'on' && $fb_app_id != "" && $fb_secret != "" && $fb_access_token != "") {
				$descriptionfb_li = foodbakery_restaurant_string_limit($description, 10000);

				if (!class_exists('SMAPFacebook')) {
					require_once(dirname(__FILE__) . '/social-api/facebook.php');
				}
				$disp_type = 'feed';


				$lmessagetopost = '{PERMALINK}';

				$foodbakery_restaurant_pages_ids = get_option('foodbakery_fb_pages_ids');
				if ($foodbakery_restaurant_pages_ids == "") {
					$foodbakery_restaurant_pages_ids = -1;
				}

				$foodbakery_restaurant_pages_ids1 = explode(",", $foodbakery_restaurant_pages_ids);

				foreach ($foodbakery_restaurant_pages_ids1 as $key => $value) {
					if ($value != -1) {
						$value1 = explode("-", $value);
						$acces_token = $value1[1];
						$page_id = $value1[0];

						$fb = new SMAPFacebook(array(
							'appId' => $fb_app_id,
							'secret' => $fb_secret,
							'cookie' => true
						));
						$message1 = str_replace('{POST_TITLE}', $name, $lmessagetopost);
						$message2 = str_replace('{BLOG_TITLE}', $caption, $message1);
						$message3 = str_replace('{PERMALINK}', $link, $message2);
						$message4 = str_replace('{POST_EXCERPT}', $excerpt, $message3);
						$message5 = str_replace('{POST_CONTENT}', $description, $message4);
						$message5 = str_replace('{USER_NICENAME}', $user_nicename, $message5);

						$message5 = str_replace("&nbsp;", "", $message5);

						$attachment = array(
							'message' => $message5,
							'access_token' => $acces_token,
							'link' => $link,
							'name' => $name,
							'caption' => $caption,
							'description' => $descriptionfb_li,
							'actions' => array(
								array(
									'name' => $name,
									'link' => $link
								)
							),
							'picture' => $attachmenturl
						);

						$attachment = foodbakery_fbapp_attachment_metas($attachment, $link);

						$result = $fb->api('/' . $page_id . '/' . $disp_type . '/', 'post', $attachment);
					}
				}
			}
		}
	}

	$foodbakery_publisher_restaurant_actions = new foodbakery_publisher_restaurant_actions();
}
