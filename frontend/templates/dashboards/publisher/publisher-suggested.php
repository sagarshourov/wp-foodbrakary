<?php
/**
 * Publisher Suggested Data
 *
 */
if (!class_exists('Foodbakery_Publisher_Suggested')) {

    class Foodbakery_Publisher_Suggested {

	/**
	 * Start construct Functions
	 */
	public function __construct() {
	    add_action('wp_ajax_foodbakery_publisher_suggested', array($this, 'foodbakery_publisher_suggested_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_save_suggested_data', array($this, 'foodbakery_save_suggested_data_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_send_invitation', array($this, 'foodbakery_send_invitation_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_add_team_member', array($this, 'foodbakery_add_team_member_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_update_team_member', array($this, 'foodbakery_update_team_member_callback'), 11);
	    add_action('wp_ajax_foodbakery_remove_team_member', array($this, 'foodbakery_remove_team_member_callback'), 11);
	    add_action('wp_ajax_transient_call_back', array($this, 'transient_call_back'), 11);
	    add_action('wp_ajax_nopriv_transient_call_back', array($this, 'transient_call_back'), 11);
	    add_action('clear_auth_cookie', array($this, 'clear_transient_on_logout'), 11);

	}

	/**
	 * Publisher Suggested Form
	 */
	public function foodbakery_publisher_suggested_callback($publisher_id = '') {

	    global $foodbakery_html_fields_frontend, $post, $foodbakery_form_fields_frontend, $foodbakery_plugin_options, $orders_inquiries;

	    $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

	    $user = wp_get_current_user();
	    $suggested_default_restaurants_categories = '';
	    $this->foodbakery_default_suggestions_settings_dashboard_callback();
            $company_id     = get_user_meta( $user->ID, 'foodbakery_company', true );
            $user_type      = get_post_meta( $company_id, 'foodbakery_publisher_profile_type', true );
            $foodbakery_dashboard_announce_title = isset($foodbakery_plugin_options['foodbakery_dashboard_announce_title']) ? $foodbakery_plugin_options['foodbakery_dashboard_announce_title'] : '';
            $foodbakery_dashboard_announce_description = isset($foodbakery_plugin_options['foodbakery_dashboard_announce_description']) ? $foodbakery_plugin_options['foodbakery_dashboard_announce_description'] : '';
            $foodbakery_announce_bg_color = isset($foodbakery_plugin_options['foodbakery_announce_bg_color']) ? $foodbakery_plugin_options['foodbakery_announce_bg_color'] : '#2b8dc4';
            
            if( $user_type == 'restaurant' ){
                $foodbakery_dashboard_announce_title = isset($foodbakery_plugin_options['foodbakery_restaurant_dashboard_announce_title']) ? $foodbakery_plugin_options['foodbakery_restaurant_dashboard_announce_title'] : '';
                $foodbakery_dashboard_announce_description = isset($foodbakery_plugin_options['foodbakery_restaurant_dashboard_announce_description']) ? $foodbakery_plugin_options['foodbakery_restaurant_dashboard_announce_description'] : '';
                $foodbakery_announce_bg_color = isset($foodbakery_plugin_options['foodbakery_restaurant_announce_bg_color']) ? $foodbakery_plugin_options['foodbakery_restaurant_announce_bg_color'] : '#2b8dc4';
            }
	    ?>
	    <?php
	    if ((isset($foodbakery_dashboard_announce_title) && $foodbakery_dashboard_announce_title <> '') || (isset($foodbakery_dashboard_announce_description) && $foodbakery_dashboard_announce_description <> '')) {
		if ('true' !== get_transient('cookie_close' . $user->ID)) {
		    ?>
		    <div id="close-me" class="user-message" style="background-color:<?php echo ($foodbakery_announce_bg_color); ?>;" > 
		        <a onclick="transient_call_back('<?php echo esc_html($user->ID) ?>')" class="close close-div" href="javascript:void(0);"><i class="icon-cross-out"></i></a>
		        <h2><?php echo '</pre>';echo esc_html($foodbakery_dashboard_announce_title); ?></h2>
		        <p><?php echo htmlspecialchars_decode($foodbakery_dashboard_announce_description); ?></p>
		    </div>
		    <?php
		}
	    }
	    ?>
	    <script>
	        function transient_call_back(id) {
	    	"use strict";
	    	var dataString = 'user_id=' + id + '&action=transient_call_back';
	    	jQuery.ajax({
	    	    type: "POST",
	    	    url: foodbakery_globals.ajax_url,
	    	    data: dataString,
	    	    success: function (response) {
	    		if (response != 'error') {
	    		    jQuery("#close-me").remove();
	    		}
	    	    }
	    	});
	    	return false;
	        }

	    </script>
	    <?php
	    $user_details = wp_get_current_user();
	    $user_company_id = get_user_meta($user_details->ID, 'foodbakery_company', true);
	    $publisher_profile_type = get_post_meta($user_company_id, 'foodbakery_publisher_profile_type', true);
	    if ($publisher_profile_type != 'restaurant') {
		?>
		<div class="row">

		    <?php do_action('foodbakery_new_notifications'); ?>

		    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="user-suggest-list listing simple">
			    <div class="element-title">
				<h5><?php echo esc_html__('Suggested Restaurants', 'foodbakery') ?></h5>
				<span><?php echo force_balance_tags(__('Define <em data-target="#suggestions-box" data-toggle="modal">Search criteria</em> to search for specific', 'foodbakery')); ?></span>
			    </div>
			    <?php
			    $suggested_restaurants_categories = array();
			    $suggested_restaurants_max_restaurants = 20;
			    if ($user->ID > 0) {
				$suggested_restaurants_categories = get_user_meta($user->ID, 'suggested_restaurants_categories', true);
				$suggested_restaurants_max_restaurants = get_user_meta($user->ID, 'suggested_restaurants_max_restaurants', true);
			    }

                $all_category_in_array = false;
                if(is_array($suggested_restaurants_categories)){
                    $all_category_in_array = in_array('all_categories', $suggested_restaurants_categories);
                }

			    $cate_filter_multi_arr = array();
			    if (isset($suggested_restaurants_categories) && empty($all_category_in_array)) {
				if (is_array($suggested_restaurants_categories) && count($suggested_restaurants_categories) > 0) {
				    $cate_filter_multi_arr ['relation'] = 'OR';
				    foreach ($suggested_restaurants_categories as $suggested_restaurants_categories_single) {
					$cate_filter_multi_arr = array(
					    'key' => 'foodbakery_restaurant_category',
					    'value' => $suggested_restaurants_categories_single,
					    'compare' => 'LIKE',
					);
				    }
				}
			    }
			    $args = array(
				'posts_per_page' => -1,
				'post_type' => 'restaurants',
				'post_status' => 'publish',
				'meta_query' => array(
				    'relation' => 'AND',
				    array(
					'key' => 'foodbakery_restaurant_expired',
					'value' => strtotime(date("d-m-Y")),
					'compare' => '>=',
				    ),
				    array(
					'key' => 'foodbakery_restaurant_status',
					'value' => 'delete',
					'compare' => '!=',
				    ),
				    $cate_filter_multi_arr,
				),
			    );

			    $custom_queryy = new WP_Query($args);

			    $total_posts = $custom_queryy->post_count;
			    

			    $sugg_total = false;
			    if ($suggested_restaurants_max_restaurants > 0 && $suggested_restaurants_max_restaurants < $total_posts) {
				$total_posts = $suggested_restaurants_max_restaurants;
				$sugg_total = true;
			    }

			    $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
			    $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';

			    if ($suggested_restaurants_max_restaurants < $posts_per_page) {
				$posts_per_page = $suggested_restaurants_max_restaurants;
			    }

			    $args = array(
				'posts_per_page' => $posts_per_page,
				'paged' => $posts_paged,
				'nopaging' => false,
				'post_type' => 'restaurants',
				'post_status' => 'publish',
				'meta_query' => array(
				    'relation' => 'AND',
				    array(
					'key' => 'foodbakery_restaurant_expired',
					'value' => strtotime(date("d-m-Y")),
					'compare' => '>=',
				    ),
				    array(
					'key' => 'foodbakery_restaurant_status',
					'value' => 'delete',
					'compare' => '!=',
				    ),
					array(
					'key' => 'foodbakery_restaurant_status',
					'value' => 'active',
					'compare' => '=',
				    ),
				    $cate_filter_multi_arr,
				),
			    );

			    $custom_query = new WP_Query($args);

			    $total_pages = 1;
			    if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
				$total_pages = ceil($total_posts / $posts_per_page);
				$remain_sugg_posts = fmod($total_posts, $posts_per_page);
			    }

			    $all_restaurants = $custom_query->posts;
			    if (isset($all_restaurants) && !empty($all_restaurants)) {
				?>
		    	    <ul class="user-suggest-list-holder">
				    <?php
				    $suggested_counter = 1;
				    while ($custom_query->have_posts()) : $custom_query->the_post();
					global $post;
					
					$category = get_the_terms(get_the_ID(), 'restaurant-category');
					$restaurant_post_on = get_post_meta(get_the_ID(), 'foodbakery_restaurant_posted', true);
					$restaurant_post_expiry = get_post_meta(get_the_ID(), 'foodbakery_restaurant_expired', true);
					$restaurant_status = get_post_meta(get_the_ID(), 'foodbakery_restaurant_status', true);
					$restaurant_pickup_delivery = get_post_meta(get_the_ID(), 'foodbakery_restaurant_pickup_delivery', true);
					$restaurant_delivery_time = get_post_meta(get_the_ID(), 'foodbakery_delivery_time', true);
					$foodbakery_restaurant_type = get_post_meta(get_the_ID(), 'foodbakery_restaurant_type', true);
					$foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
					$restaurant_pickup_time = get_post_meta(get_the_ID(), 'foodbakery_restaurant_pickup_time', true);
					$foodbakery_post_loc_address_restaurant = get_post_meta(get_the_ID(), 'foodbakery_post_loc_address_restaurant', true);
					?>
					<li>
					    <div class="suggest-list-holder">
						<div class="img-holder">
						    <figure>
							<a href="<?php echo esc_url(get_the_permalink()); ?>">
							    <?php
							    if (has_post_thumbnail()) {
								the_post_thumbnail('full');
							    } else {
								$no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image4x3.jpg');
								$no_image = '<img alt="" src="' . $no_image_url . '" />';
								echo force_balance_tags($no_image);
							    }
							    ?>
							</a>
						    </figure>
						</div>
						<div class="text-holder">
						    <div class="post-title"> <h5><a href="<?php echo esc_url(get_the_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h5></div>

						    <?php
						    $counter = 1;
						    if (is_array($category) && count($category) > 0) {
							echo '<span class="post-categories">';
							foreach ($category as $cat) {

							    if (isset($cat->name) && $cat->name != '') {
								?>
								<?php echo esc_html($cat->name); ?> <?php
								if ($counter < count($category)) {
								    echo ',';
								}
								?>
								<?php
								$counter ++;
							    }
							}
							echo '</span>';
						    }
						    $post_id = get_the_ID();
						    $shortlist_label = 'Shortlist';
						    $shortlisted_label = 'Shortlisted';
						   
						    $book_mark_args = array(
							'before_icon' => '<i class="icon-heart-o"></i>',
							'after_icon' => '<i class="icon-heart4"></i>',
						    );
						    echo '<div class="list-option">';
						    do_action('foodbakery_shortlists_frontend_button', $post_id, $book_mark_args);
						    echo '<a href="' . esc_url(get_the_permalink()) . '" class="viewmenu-btn">' . esc_html__('View Menu', 'foodbakery') . '</a></div>';
						    ?>
						    <div class="delivery-potions">
							<?php
							if ( $restaurant_pickup_delivery == 'delivery' || $restaurant_pickup_delivery == 'delivery_and_pickup' ) {
							    ?>
			    				<div class="post-time">
			    				    <i class="icon-motorcycle"></i>
			    				    <div class="time-tooltip">
			    					<div class="time-tooltip-holder"> <b class="tooltip-label"><?php esc_html_e('Delivery time', 'foodbakery') ?></b> <b class="tooltip-info"><?php echo esc_html($restaurant_delivery_time) ?></b> </div>
			    				    </div>
			    				</div>
							    <?php
							}
							if ( $restaurant_pickup_delivery == 'pickup' || $restaurant_pickup_delivery == 'delivery_and_pickup' ) {
							    ?>
			    				<div class="post-time">
			    				    <i class="icon-clock4"></i>
			    				    <div class="time-tooltip">
			    					<div class="time-tooltip-holder"> <b class="tooltip-label"><?php esc_html_e('Pickup time', 'foodbakery') ?></b> <b class="tooltip-info"><?php echo esc_html($restaurant_pickup_time) ?></b> </div>
			    				    </div>
			    				</div>
							    <?php
							}

							if ($foodbakery_post_loc_address_restaurant) {
							    echo '<span>';
							    echo esc_html($foodbakery_post_loc_address_restaurant);
							    echo '</span>';
							}
							?>
						    </div>
						</div>
					    </div>
					</li>
					<?php
					if (isset($remain_sugg_posts) && $sugg_total && $total_pages == $posts_paged && $remain_sugg_posts == $suggested_counter) {
					    break;
					}
					$suggested_counter ++;
				    endwhile;
				    wp_reset_postdata();
				    ?>
		    	    </ul>
				<?php
			    }
			    ?>
			</div>
			<?php
			$total_pages = 1;
			if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
			    $total_pages = ceil($total_posts / $posts_per_page);

			    $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
			    $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
			    $this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'suggested'), $foodbakery_dashboard_link) : '';
			    foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'suggested');
			}
			?>
		    </div>
		</div>

		<div class="modal fade" id="suggestions-box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
		    <div class="modal-dialog" role="document">
			<div class="login-form">
			    <div class="modal-content">
				<div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><?php echo esc_html('&times;', 'foodbakery'); ?></span>
				    </button>
				    <h3 class="modal-title"><?php echo esc_html__('Suggested Restaurants Settings', 'foodbakery'); ?></h3>
				</div>
				<div class="modal-body">
				    <div class="status status-message"></div>
				    <form method="post" class="wp-user-form webkit" id="ControlForm_suggestions">
					<div class="input-filed">
					    <?php
					    $categories_all_args = array(
						'taxonomy' => 'restaurant-category',
						'orderby' => 'name',
						'order' => 'ASC',
						'fields' => 'all',
						'slug' => '',
						'hide_empty' => false,
					    );

					    $all_categories = get_terms($categories_all_args);
					    $select_options = '';
					    $select_options = array('all_categories' => esc_html__('All Categories', 'foodbakery'));


					    if (isset($all_categories) && is_array($all_categories)) {

						foreach ($all_categories as $category) {

						    $select_options[$category->slug] = $category->name;
						}
					    }


					    $foodbakery_opt_array = array(
						'id' => 'suggested_restaurants_categories',
						'cust_id' => 'suggested_restaurants_categories',
						'cust_name' => 'suggested_restaurants_categories[]',
						'std' => $suggested_restaurants_categories,
						'desc' => '',
						'extra_atr' => 'data-placeholder="' . esc_html__("Please Select categories", "foodbakery") . '"',
						
						'options' => $select_options,
						'hint_text' => '',
						'required' => 'yes',
						'return' => false,
						'description' => '',
						'name' => esc_html__('Categories for suggestions', 'foodbakery'),
					    );

					    $foodbakery_form_fields_frontend->foodbakery_form_multiselect_render($foodbakery_opt_array);
					    ?>
					</div>
					<div class="input-filed">
					    <label><?php echo esc_html__('Number of suggestions to show', 'foodbakery'); ?></label>
					    <?php
					    $foodbakery_opt_array = array(
						'id' => '',
						'std' => $suggested_restaurants_max_restaurants,
						'cust_id' => 'suggested_restaurants_max_restaurants',
						'cust_name' => 'suggested_restaurants_max_restaurants',
						'classes' => 'form-control',
						'extra_atr' => ' tabindex="11" placeholder="' . esc_html__('example 20', 'foodbakery') . '"',
						'return' => false,
					    );
					    $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
					    ?>
					</div>
					<div class="input-filed">
					    <div class="save-search-criteria input-button-loader">
						<input type="button" class="btn-suggestions-settings cs-bgcolor" name="submit-suggestions-settings" value="<?php echo esc_html__('Save Settings', 'foodbakery'); ?>">
					    </div>
					</div>
				    </form>
				</div>
			    </div>          

			    <script type="text/javascript">
				(function ($) {
				    $(function () {
					$(".btn-suggestions-settings").click(function () {
					    var thisObj = jQuery('.save-search-criteria');
					    foodbakery_show_loader('.save-search-criteria', '', 'button_loader', thisObj);
					    var input_data = $('#ControlForm_suggestions').serialize() + '&action=foodbakery_save_suggestions_settings_dashboard';
					    $.ajax({
						type: "POST",
						url: "<?php echo esc_js(admin_url('admin-ajax.php')); ?>",
						data: input_data,
						dataType: "json",
						success: function (data) {
						    foodbakery_show_response(data, '#ControlForm_suggestions', thisObj);
						    jQuery("#suggestions-box").modal('toggle');
						    jQuery('#foodbakery_publisher_suggested').trigger('click');
						},
					    });
					    return false;
					});
					$('#foodbakery_suggested_restaurants_categories').chosen();
				    });
				})(jQuery);
			    </script>
			</div>
		    </div>
		</div>
		<?php
	    } else {
		$orders_inquiries->foodbakery_publisher_received_orders_callback($publisher_id, 5, 'outside');
	    }

	    wp_die();
	}

	public function transient_call_back() {
	    set_transient("cookie_close" . $_POST['user_id'], 'true', (3600 * 60) * 24);
	    wp_die();
	}

	public function clear_transient_on_logout() {
	    $user_data = wp_get_current_user();
	    delete_transient('cookie_close' . $user_data->ID);
	}

	/**
	 * Publisher Suggested Saving Data
	 */
	public function foodbakery_save_suggested_data_callback() {

	    $suggested_id = foodbakery_get_input('publisher_suggested_id', NULL, 'INT');
	    $suggested_name = foodbakery_get_input('publisher_suggested_name', NULL, 'STRING');
	    $website_url = foodbakery_get_input('publisher_suggested_website', NULL, 'STRING');
	    $suggested_phone = foodbakery_get_input('publisher_suggested_phone', NULL, 'STRING');
	    $suggested_content = foodbakery_get_input('foodbakery_publisher_suggested_description', NULL, 'STRING');
	    $post_data = array(
		'ID' => $suggested_id,
		'post_title' => $suggested_name,
		'post_content' => $suggested_content,
	    );

	    wp_update_post($post_data);

	    update_post_meta($suggested_id, 'foodbakery_website', $website_url);
	    update_post_meta($suggested_id, 'foodbakery_phone_number', $suggested_phone);

	    $response_array = array(
		'type' => 'success',
		'msg' => esc_html__('Successfully Updated!', 'foodbakery'),
	    );
	    echo json_encode($response_array);
	    wp_die();
	}

	/*
	 * Sending Invitation Email
	 */

	public function foodbakery_send_invitation_callback() {

	    $email = foodbakery_get_input('foodbakery_email_address', NULL, 'STRING');
	    if ($email == NULL) {
		$response_array = array(
		    'type' => 'error',
		    'msg' => esc_html__('Please provide email address', 'foodbakery'),
		);
		echo json_encode($response_array);
		wp_die();
	    }
	    if (email_exists($email)) {
		$response_array = array(
		    'type' => 'error',
		    'msg' => esc_html__('Email address already exists', 'foodbakery'),
		);
		echo json_encode($response_array);
		wp_die();
	    }
	    $randkey = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);
	    $user_details = array(
		'email' => $email,
		'permissions' => foodbakery_get_input('permissions', '', 'ARRAY'),
		'invited_by' => get_current_user_id(),
	    );
	    update_option($randkey, $user_details);

	    $email_array = array(
		'to' => $email,
		'subject' => 'Invitation',
		'message' => '<a href="' . site_url() . '/?key=' . $randkey . '">' . esc_html__('Login', 'foodbakery') . '</a>',
	    );

	    do_action('foodbakery_send_mail', $email_array);

	    $response_array = array(
		'type' => 'success',
		'msg' => esc_html__('Invitation successfully sent!', 'foodbakery'),
	    );
	    echo json_encode($response_array);
	    wp_die();
	}

	/*
	 * Adding Team Member
	 */

	public function foodbakery_add_team_member_callback() {
	    $first_name = foodbakery_get_input('foodbakery_first_name', NULL, 'STRING');
	    $last_name = foodbakery_get_input('foodbakery_last_name', NULL, 'STRING');
	    $permissions = foodbakery_get_input('permissions', NULL, 'ARRAY');
	    $email = foodbakery_get_input('foodbakery_email_address', NULL, 'STRING');
	    if ($email == NULL) {
		$response_array = array(
		    'type' => 'error',
		    'msg' => esc_html__('Please provide email address', 'foodbakery'),
		);
		echo json_encode($response_array);
		wp_die();
	    }
	    if (email_exists($email)) {
		$response_array = array(
		    'type' => 'error',
		    'msg' => esc_html__('Email address already exists', 'foodbakery'),
		);
		echo json_encode($response_array);
		wp_die();
	    }

	    $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);

	    $user_ID = wp_create_user($email, $random_password, $email);

	    if (!is_wp_error($user_ID)) {

		wp_update_user(array(
		    'ID' => $user_ID,
		    'role' => 'foodbakery_publisher'
		));

		update_user_meta($user_ID, 'show_admin_bar_front', false);

		if ($permissions != NULL) {
		    update_user_meta($user_ID, 'foodbakery_permissions', $permissions);
		}



		if ($first_name != NULL) {
		    update_user_meta($user_ID, 'first_name', $first_name);
		}

		if ($last_name != NULL) {
		    update_user_meta($user_ID, 'last_name', $last_name);
		}


		update_user_meta($user_ID, 'foodbakery_user_type', 'team-member');
		update_user_meta($user_ID, 'foodbakery_user_status', 'active');

		$suggested_ID = get_user_meta(get_current_user_id(), 'foodbakery_suggested', true);
		update_user_meta($user_ID, 'foodbakery_suggested', $suggested_ID);
		update_user_meta($user_ID, 'foodbakery_is_admin', 0);

		$message = 'Hi, ' . $first_name . ' ' . $last_name . ' ';
		$message .= esc_html__('Your account was created on foodbakery, you can login with following details  ','foodbakery');
		$message .= esc_html__('Username: ','foodbakery') . $email . ' | ';
		$message .= esc_html__('Password: ','foodbakery') . $random_password . '';

		/*
		 * Sending Email with login details.
		 */
		$email_array = array(
		    'to' => $email,
		    'subject' => 'Login Details',
		    'message' => $message,
		);

		do_action('foodbakery_send_mail', $email_array);

		$response_array = array(
		    'type' => 'success',
		    'msg' => esc_html__('Team member successfully added!', 'foodbakery'),
		);
		echo json_encode($response_array);

		wp_die();
	    }
	}

	/*
	 * Updating Team Member
	 */

	public function foodbakery_update_team_member_callback() {
	    $user_ID = foodbakery_get_input('foodbakery_user_id', NULL, 'INT');

	    $permissions = foodbakery_get_input('permissions', '', 'ARRAY');
	    update_user_meta($user_ID, 'foodbakery_permissions', $permissions);

	    $response_array = array(
		'type' => 'success',
		'msg' => esc_html__('Team member successfully updated!', 'foodbakery'),
	    );
	    echo json_encode($response_array);
	    wp_die();
	}

	/*
	 * Removing Team Member
	 * @ User ID
	 */

	public function foodbakery_remove_team_member_callback() {
	    $user_ID = foodbakery_get_input('foodbakery_user_id', NULL, 'INT');
	    update_user_meta($user_ID, 'foodbakery_user_status', 'deleted');
	    $response_array = array(
		'type' => 'success',
		'msg' => esc_html__('Team Member Successfully Removed', 'foodbakery'),
	    );
	    echo json_encode($response_array);
	    wp_die();
	}

	/**
	 * Suggestions default settings for user's dashaboard.
	 */
	public function foodbakery_default_suggestions_settings_dashboard_callback() {
	    $suggested_default_restaurants_categories = array();
	    $suggested_default_restaurants_categories[] = 'all_categories';
	    $suggested_restaurants_max_restaurants = 20;
	    if (!empty($suggested_default_restaurants_categories) && $suggested_restaurants_max_restaurants != '') {
		$user = wp_get_current_user();
		if ($user->ID > 0) {
		    $user_selected_cats = get_user_meta($user->ID, 'suggested_restaurants_categories', true);
		    if (empty($user_selected_cats) || $user_selected_cats == '') {
			update_user_meta($user->ID, 'suggested_restaurants_categories', $suggested_default_restaurants_categories);
			update_user_meta($user->ID, 'suggested_restaurants_max_restaurants', $suggested_restaurants_max_restaurants);
		    }
		}
	    }
	}

    }

    global $foodbakery_publisher_suggested;
    $foodbakery_publisher_suggested = new Foodbakery_Publisher_Suggested();
}
