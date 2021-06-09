<?php
/**
 * Publisher Publisher Data
 *
 */
if (!class_exists('Foodbakery_Publisher_Publisher')) {

    class Foodbakery_Publisher_Publisher {

	/**
	 * Start construct Functions
	 */
	public function __construct() {
	    add_action('wp_ajax_foodbakery_publisher_company', array($this, 'foodbakery_publisher_company_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_send_invitation', array($this, 'foodbakery_send_invitation_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_add_team_member', array($this, 'foodbakery_add_team_member_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_update_team_member', array($this, 'foodbakery_update_team_member_callback'), 11);
	    add_action('wp_ajax_foodbakery_remove_team_member', array($this, 'foodbakery_remove_team_member_callback'), 11);
	}

	/**
	 * Publisher Publisher Form
	 */
	public function foodbakery_publisher_company_callback($publisher_id = '') {
	    global $foodbakery_html_fields_frontend, $post, $foodbakery_form_fields_frontend, $foodbakery_plugin_options;

	    $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

	    if (!isset($publisher_id) || $publisher_id == '') {
		$publisher_id = get_current_user_id();
	    }
	    $company_id = get_user_meta($publisher_id, 'foodbakery_company', true);
	    $company_data = get_post($company_id);

	    $post = $company_data;

	    setup_postdata($post);

	    $website_url = get_post_meta($post->ID, 'foodbakery_website', true);
	    $phone_number = get_post_meta($post->ID, 'foodbakery_phone_number', true);
	    ?>
	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	        <div class="row">
	    	<div class="user-profile">
	    	    <div class="row">

			    <?php
			    wp_reset_postdata();

			    if (true === Foodbakery_Member_Permissions::check_permissions('company_profile')) {
				?>
				<div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12">
				    <div class = "element-title">
					<h5><?php echo esc_html__('Team Members', 'foodbakery'); ?></h5>
					<ul class="dashboard-nav sub-nav">
					    <li><a href="javascript:void(0)" class="send-invitation"><?php echo esc_html__('Send Invitation', 'foodbakery'); ?></a></li>
					    <li><a href="javascript:void(0)" class="add-more add_team_member"><?php echo esc_html__('Add Members', 'foodbakery'); ?></a></li>
					</ul>
				    </div>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				    <div class="invite-member invited_team_member">
					<form id="team_invitation_form" method="POST">
					    <div class ="element-title has-border">
						<a href="javascript:void(0);" class="close-btn cancel">&times;</a>
						<h5><?php esc_html_e('Send invitation', 'foodbakery'); ?></h5>
					    </div>
					    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class = "field-holder">
							<label><?php echo esc_html__('Email Address', 'foodbakery'); ?></label>
							<?php
							$foodbakery_opt_array = array(
							    'name' => esc_html__('Email Address', 'foodbakery'),
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => '',
								'id' => 'email_address',
							    ),
							);
							$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
						    </div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class="field-holder">
							<label><?php echo esc_html__('User Type', 'foodbakery'); ?></label>
							<?php
							$user_type = array(
							    'team-member' => esc_html__('Team Member', 'foodbakery'),
							    'supper-admin' => esc_html__('Supper Admin', 'foodbakery'),
							);
							$foodbakery_opt_array = array(
							    'name' => esc_html__('User Type', 'foodbakery'),
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => '',
								'id' => 'user_type',
								'classes' => 'chosen-select-no-single',
								'options' => $user_type,
								'extra_atr' => 'onchange="foodbakery_user_permission(this, \'invite_member_permission\', \'supper-admin\');"'
							    ),
							);
							$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
							?>
						    </div>
						</div>
						<div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class = "invitation-permission invite_member_permission">
							<span class="most-used"> <?php echo esc_html__('Roles & Permission', 'foodbakery'); ?></span>
							<?php
							global $permissions;
							$permissions_array = $permissions->member_permissions();
							?>
							<ul class = "checkbox-list">
							    <?php foreach ($permissions_array as $permission_key => $permission_value) { ?>
		    					    <li class = "col-lg-6 col-md-6 col-sm-12 col-xs-12" draggable = "true" style = "display: inline-block;">
								    <?php
								    $foodbakery_opt_array = array(
									'name' => $permission_value,
									'desc' => '',
									'echo' => true,
									'simple' => true,
									'field_params' => array(
									    'std' => '',
									    'simple' => true,
									    'id' => $permission_key,
									    'cust_name' => 'permissions[' . $permission_key . ']',
									),
								    );
								    $foodbakery_html_fields_frontend->foodbakery_form_checkbox_render($foodbakery_opt_array);
								    ?>
		    					    </li>
							    <?php } ?>
							</ul>
						    </div>
						</div>
						<div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class = "field-holder">
							<a href="javascript:;" id="send_invitation" class="btn-send"><?php echo esc_html__('Send', 'foodbakery'); ?></a>
							<a href = "#" class="cancel"><?php echo esc_html__('Cancel', 'foodbakery'); ?></a>
						    </div>
						</div>
					    </div>
					</form>
				    </div>
				    <div class="invite-member add-member">
					<form id="team_add_form" method="POST">
					    <div class ="element-title has-border">
						<a href="javascript:void(0);" class="close-btn cancel">&times;</a>
						<h5><?php echo esc_html__('Add Team Member', 'foodbakery'); ?></h5>
					    </div>
					    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class = "field-holder">
							<label><?php echo esc_html__('Email Address', 'foodbakery'); ?></label>
							<?php
							$foodbakery_opt_array = array(
							    'name' => esc_html__('Email Address', 'foodbakery'),
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => '',
								'id' => 'email_address',
							    ),
							);
							$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
						    </div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class="field-holder">
							<label><?php echo esc_html__('User Type', 'foodbakery'); ?></label>
							<?php
							$user_type = array(
							    'team-member' => esc_html__('Team Member', 'foodbakery'),
							    'supper-admin' => esc_html__('Supper Admin', 'foodbakery'),
							);
							$foodbakery_opt_array = array(
							    'name' => esc_html__('User Type', 'foodbakery'),
							    'desc' => '',
							    'echo' => true,
							    'field_params' => array(
								'std' => '',
								'id' => 'user_type',
								'classes' => 'chosen-select-no-single',
								'options' => $user_type,
								'extra_atr' => 'onchange="foodbakery_user_permission(this, \'add_member_permission\', \'supper-admin\');"'
							    ),
							);
							$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
							?>
						    </div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class = "invitation-permission add_member_permission">
							<span class="most-used"> <?php echo esc_html__('Roles & Permission', 'foodbakery'); ?></span>
							<?php
							global $permissions;
							$permissions_array = $permissions->member_permissions();
							?>
							<ul class = "checkbox-list">
							    <?php
							    foreach ($permissions_array as $permission_key => $permission_value) {
								$rand = rand(0, 99);
								?>
		    					    <li class = "col-lg-6 col-md-6 col-sm-12 col-xs-12" draggable = "true" style = "display: inline-block;">
								    <?php
								    $foodbakery_opt_array = array(
									'name' => $permission_value,
									'desc' => '',
									'echo' => true,
									'simple' => true,
									'field_params' => array(
									    'std' => '',
									    'simple' => true,
									    'id' => $permission_key . $rand,
									    'cust_name' => 'permissions[' . $permission_key . ']',
									),
								    );
								    $foodbakery_html_fields_frontend->foodbakery_form_checkbox_render($foodbakery_opt_array);
								    ?>
		    					    </li>
							    <?php } ?>
							</ul>
						    </div>
						</div>
						<div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <div class = "field-holder">
							<a href="javascript:;" id="add_member" class="btn-send"><?php echo esc_html__('Send', 'foodbakery'); ?></a>
							<a href = "#" class = "cancel"><?php echo esc_html__('Cancel', 'foodbakery'); ?></a>
						    </div>
						</div>
					    </div>
					</form>
				    </div>
				    <div class="responsive-table">
					<div class="team-list" id="team-list-table">
					    <?php
					    $company_ID = get_user_meta(get_current_user_id(), 'foodbakery_company', true);
					    $team_args = array(
						'role' => 'foodbakery_publisher',
						'meta_query' => array(
						    array(
							'key' => 'foodbakery_company',
							'value' => $company_ID,
							'compare' => '='
						    ),
						    array(
							'key' => 'foodbakery_user_status',
							'value' => 'deleted',
							'compare' => '!='
						    )
						),
					    );
					    $team_members = get_users($team_args);
					    ?>
					    <ul class="panel-group table-generic">
						<li> 
						    <div>
							<span><?php echo esc_html__('Username', 'foodbakery'); ?></span>
							<span><?php echo esc_html__('Email Address', 'foodbakery'); ?></span> 
						    </div>
						</li>
						<?php
						// count the supper admin in complete team

						if (is_array($team_members) && sizeof($team_members) > 0) {

						    $total_posts = sizeof($team_members);

						    $supper_admin_count = 0;
						    foreach ($team_members as $member_data) {
							$selected_user_type = get_user_meta($member_data->ID, 'foodbakery_user_type', true);
							if ($selected_user_type == 'supper-admin') {
							    $supper_admin_count ++;
							}
						    }

						    $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
						    $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';

						    if ($posts_per_page > 0 && $total_posts > $posts_per_page) {
							$limit_start = $posts_per_page * ($posts_paged - 1);
							$limit_end = $limit_start + $posts_per_page;
							if ($limit_end > $total_posts) {
							    $limit_end = $total_posts;
							}
						    } else {
							$limit_start = 0;
							$limit_end = $total_posts;
						    }
						    for ($i = $limit_start; $i < $limit_end; $i ++) {
							$member_data = isset($team_members[$i]) ? $team_members[$i] : '';
							$selected_user_type = get_user_meta($member_data->ID, 'foodbakery_user_type', true);
							$selected_user_type = isset($selected_user_type) && $selected_user_type != '' ? $selected_user_type : 'team-member';
							$member_permissions = get_user_meta($member_data->ID, 'foodbakery_permissions', true);
							?>
							<li>
							    <div>
								<div class="panel panel-default" >
								    <a href="javascript:void(0);" class="close-member" data-id="<?php echo esc_attr($member_data->ID); ?>"><i class="icon-close2 remove_member"></i></a>
								    <div class="panel-heading"> 
									<a href="javascript:void(0);" class="restaurant-team-member-det" data-id="<?php echo esc_attr($member_data->ID); ?>">
									    <div class="img-holder">
										<strong><?php echo esc_html($member_data->user_login); ?> </strong>
									    </div>
									    <span class="email"><?php echo esc_html($member_data->user_email); ?> </span> 
									    <?php /*if ($is_user_restaurant == 'restaurant') { */?><!--
			    						    <span class="supper-admin"><?php /*echo esc_html__('Administrator', 'foodbakery'); */?></span>
								--><?php /*} else */?>	    
                                <?php  if ($selected_user_type == 'supper-admin') { ?>
			    						    <span class="supper-admin"><?php echo esc_html__('Supper Admin', 'foodbakery'); ?></span>
									    <?php } else { ?>
			    						    <span class="supper-admin"><?php echo esc_html__('Team Memeber', 'foodbakery'); ?></span>
									    <?php } ?>
									</a>
								    </div>

								    <div id="team-member-det-<?php echo esc_attr($member_data->ID); ?>" class="invite-member team-member-det-box">
									<form name="foodbakery_update_team_member" id="foodbakery_update_team_member<?php echo esc_attr($member_data->ID); ?>" data-selected_user_type="<?php echo esc_attr($selected_user_type); ?>" data-count_supper_admin="<?php echo esc_attr($supper_admin_count); ?>" data-id="<?php echo esc_attr($member_data->ID); ?>" method="POST">
									    <?php
									    // TOTAL SUPPER ADMIN COUNT
									    $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
										    array(
											'cust_name' => 'count_supper_admin',
											'classes' => 'count_supper_admin',
											'std' => $supper_admin_count,
										    )
									    );
									    $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
										    array(
											'cust_name' => 'foodbakery_old_user_type',
											'std' => $selected_user_type,
										    )
									    );
									    ?>
									    <div class="element-title has-border">
										<a href="javascript:void(0);" class="close-btn cancel">&times;</a>
										<h5><?php esc_html_e('Update Team Member', 'foodbakery'); ?></h5>
									    </div>
									    <div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										    <div class = "field-holder">
											<label><?php echo esc_html__('Email Address', 'foodbakery'); ?></label>
											<?php
											$foodbakery_opt_array = array(
											    'name' => esc_html__('Email Address', 'foodbakery'),
											    'desc' => '',
											    'echo' => true,
											    'field_params' => array(
												'std' => esc_html($member_data->user_email),
												'id' => 'email_address',
											    ),
											);
											$foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
											?>
										    </div>
										</div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										    <div class = "field-holder">
											<label><?php echo esc_html__('User Type', 'foodbakery'); ?></label>

											<?php
											$user_type = array(
											    'team-member' => esc_html__('Team Member', 'foodbakery'),
											    'supper-admin' => esc_html__('Supper Admin', 'foodbakery'),
											);
											$foodbakery_opt_array = array(
											    'name' => esc_html__('User Type', 'foodbakery'),
											    'desc' => '',
											    'echo' => true,
											    'field_params' => array(
												'std' => $selected_user_type,
												'id' => 'user_type',
												'classes' => 'chosen-select-no-single',
												'options' => $user_type,
												'extra_atr' => 'onchange="foodbakery_user_permission(this, \'add_member_permission' . esc_attr($member_data->ID) . '\', \'supper-admin\');"'
											    ),
											);
											$foodbakery_html_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
											?>
										    </div>
										</div>
										<?php
										$permission_display = '';
										if ($selected_user_type == 'supper-admin') {
										    $permission_display = 'display:none';
										}
										?>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 add_member_permission<?php echo esc_attr($member_data->ID); ?>" style="<?php echo esc_html($permission_display); ?>">
										    <div class="invitation-permission">
											<span class="most-used"><?php echo esc_html__('Roles & Permission', 'foodbakery'); ?></span>
											<?php
											global $permissions;
											$permissions_array = $permissions->member_permissions();
											?>
											<ul class = "checkbox-list">

											    <?php
											    foreach ($permissions_array as $permission_key => $permission_value) {
												$value = '';
												if (isset($member_permissions[$permission_key]) && $member_permissions[$permission_key] == 'on') {
												    $value = $member_permissions[$permission_key];
												} else if ($selected_user_type == 'supper-admin') {  // if user supper admin then show all permission
												    $value = 'on';
												}
												$rand = rand(0, 99);
												?>
			    								    <li class = "col-lg-6 col-md-6 col-sm-12 col-xs-12" draggable = "true" style = "display: inline-block;">
												    <?php
												    $foodbakery_opt_array = array(
													'name' => $permission_value,
													'desc' => '',
													'echo' => true,
													'simple' => true,
													'field_params' => array(
													    'std' => $value,
													    'simple' => true,
													    'id' => $permission_key . $rand,
													    'cust_name' => 'permissions[' . $permission_key . ']',
													),
												    );
												    $foodbakery_html_fields_frontend->foodbakery_form_checkbox_render($foodbakery_opt_array);
												    ?>
			    								    </li>
											    <?php } ?>
											</ul>
										    </div>
										</div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										    <button name="button" class="btn-submit team_update_form" type="button" id="team_update_form<?php echo esc_attr($member_data->ID); ?>"><?php echo esc_html__('Update', 'foodbakery'); ?></button>
										</div>
									    </div>
									</form>
								    </div>
								</div>
								<script>
			                                            jQuery(document).ready(function () {
			                                                'use strict'
			                                                jQuery(".chosen-select-no-single").chosen();
			                                            });
								</script>
							    </div>
							</li>
							<?php
						    }

						    $total_pages = 1;
						    if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
							$total_pages = ceil($total_posts / $posts_per_page);

							$foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
							$foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
							$this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'company'), $foodbakery_dashboard_link) : '';
							foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'company');
						    }
						}
						?>
					    </ul>
					</div>
				    </div>
				</div>
			    <?php } ?>
	    	    </div>
	    	</div>
	        </div>
	    </div>
	    <?php
	    wp_die();
	}

	/*
	 * Sending Invitation Email
	 */

	public function foodbakery_send_invitation_callback() {
	    global $foodbakery_plugin_options;
	    $email = foodbakery_get_input('foodbakery_email_address', NULL, 'STRING');
	    $user_type = foodbakery_get_input('foodbakery_user_type', NULL, 'STRING');
	    $user_type = isset($user_type) && $user_type != '' ? $user_type : 'team-member';

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
		'user_type' => $user_type,
		'invited_by' => get_current_user_id(),
	    );
	    update_option($randkey, $user_details);
	    $login_page = ( isset($foodbakery_plugin_options['foodbakery_login_page']) && $foodbakery_plugin_options['foodbakery_login_page'] != '' ) ? get_permalink($foodbakery_plugin_options['foodbakery_login_page']) : site_url();
	    $email_array = array(
		'to' => $email,
		'login_url' => '<a href="' . $login_page . '?key=' . $randkey . '">' . esc_html__('Invitation Link', 'foodbakery') . '</a>',
	    );

	    do_action('foodbakery_invitation_sent_email', $email_array);

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
	    global $wpdb;
	    $permissions = foodbakery_get_input('permissions', NULL, 'ARRAY');
	    $email = foodbakery_get_input('foodbakery_email_address', NULL, 'STRING');
	    $user_type = foodbakery_get_input('foodbakery_user_type', NULL, 'STRING');
	    $user_type = isset($user_type) && $user_type != '' ? $user_type : 'team-member';
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

		update_user_meta($user_ID, 'foodbakery_user_type', $user_type);

		// active user
		$wpdb->update(
			$wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($user_ID))
		);
		update_user_meta($user_ID, 'foodbakery_user_status', 'active');
		$company_ID = get_user_meta(get_current_user_id(), 'foodbakery_company', true);
		update_user_meta($user_ID, 'foodbakery_company', $company_ID);
		update_user_meta($user_ID, 'foodbakery_is_admin', 0);
		$message = esc_html__('Hi, ', 'foodbakery');
		$message .= esc_html__('Your account was created on foodbakery, you can login with following details  ', 'foodbakery');
		$message .= esc_html__('Username: ', 'foodbakery') . $email . ' | ';
		$message .= esc_html__('Password: ', 'foodbakery') . $random_password . '';

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

	    $foodbakery_user_type = foodbakery_get_input('foodbakery_user_type', NULL, 'STRING');
	    $foodbakery_old_user_type = foodbakery_get_input('foodbakery_old_user_type', NULL, 'STRING');
	    $count_supper_admin = foodbakery_get_input('count_supper_admin', NULL, 'STRING');
	    $update_allow = 1;
	    if ($foodbakery_old_user_type == $foodbakery_user_type) {

		$update_allow = 1;
	    } elseif ('supper-admin' == $foodbakery_user_type) {

		$update_allow = 1;
	    } elseif ($count_supper_admin > 1) {

		$update_allow = 1;
	    } else {

		$update_allow = 0;
	    }

	    if ($update_allow == 1) {
		$permissions = foodbakery_get_input('permissions', '', 'ARRAY');

		update_user_meta($user_ID, 'foodbakery_user_type', $foodbakery_user_type);
		update_user_meta($user_ID, 'foodbakery_permissions', $permissions);

		$response_array = array(
		    'type' => 'success',
		    'msg' => esc_html__('Team member successfully updated!', 'foodbakery'),
		);
	    } else {
		$response_array = array(
		    'type' => 'error',
		    'msg' => esc_html__('Atleast one supper admin required for a company', 'foodbakery'),
		);
	    }
	    echo json_encode($response_array);
	    wp_die();
	}

	/*
	 * Removing Team Member
	 * @ User ID
	 */

	public function foodbakery_remove_team_member_callback() {

	    $user_ID = foodbakery_get_input('foodbakery_user_id', NULL, 'INT');
	    $foodbakery_user_type = get_user_meta($user_ID, 'foodbakery_user_type', true);
	    $count_supper_admin = foodbakery_get_input('count_supper_admin', NULL, 'INT');

	    if ($foodbakery_user_type == 'supper-admin') {
		if ($count_supper_admin > 1) {
		    update_user_meta($user_ID, 'foodbakery_user_status', 'deleted');
		    $response_array = array(
			'type' => 'success',
			'msg' => esc_html__('Super Admin Successfully Removed', 'foodbakery'),
		    );
		} else {
		    $response_array = array(
			'type' => 'error',
			'msg' => esc_html__('Atleast one supper admin required for a company', 'foodbakery'),
		    );
		}
	    }
	    if ($foodbakery_user_type == 'team-member') {
		update_user_meta($user_ID, 'foodbakery_user_status', 'deleted');
		$response_array = array(
		    'type' => 'success',
		    'msg' => esc_html__('Team Member Successfully Removed', 'foodbakery'),
		);
	    }
	    echo json_encode($response_array);
	    wp_die();
	}

    }

    global $foodbakery_publisher_company;
    $foodbakery_publisher_company = new Foodbakery_Publisher_Publisher();
}
