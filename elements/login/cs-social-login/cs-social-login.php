<?php
if ( ! function_exists('email_exists') )
	require_once ABSPATH . WPINC . '/registration.php';

// set query vars
function foodbakery_query_vars($vars) {
	$vars[] = 'social-login';
	return $vars;
}

add_action('query_vars', 'foodbakery_query_vars');

// set parse request
function foodbakery_parse_request($wp) {

	$plugin_url = plugin_dir_url(__FILE__);
	if ( array_key_exists('social-login', $wp->query_vars) ) {

		$_REQUEST['state'] = (isset($_REQUEST['state'])) ? $_REQUEST['state'] : '';

		$state = base64_decode($_REQUEST['state']);
		$state = json_decode($state);
		if ( isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'twitter' ) {
			foodbakery_twitter_connect();
		} else if ( isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'twitter-callback' ) {
			foodbakery_twitter_callback();
		} else if ( isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'linkedin' || (isset($state->social_login) && $state->social_login == 'linkedin' ) ) {
			require_once "linkedin/linkedin_function.php";
			die();
		} else if ( isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'facebook-callback' ) {
			require_once 'facebook/callback.php';
			die();
		}
		wp_die();
	}
	if ( isset($_REQUEST['likedin-login-request']) ) {

		$user_info = get_userdata($_REQUEST['likedin-login-request']);
		$ID = $_REQUEST['likedin-login-request'];
		$user_login = $user_info->user_login;
		$user_id = $user_info->ID;
		wp_set_current_user($user_id, $user_login);
		wp_set_auth_cookie($user_id);
		do_action('wp_login', $user_login, $user_info);
	}
}

add_action('parse_request', 'foodbakery_parse_request');

// login process method
function foodbakery_social_process_login($is_ajax = false) {
	global $foodbakery_plugin_options, $wpdb;
	if ( isset($_REQUEST['redirect_to']) && $_REQUEST['redirect_to'] != '' ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( isset($secure_cookie) && $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}
	$foodbakery_page_id = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : $_POST['redirect_to'];
	$redirect_to = get_permalink((int) $foodbakery_page_id);
	$redirect_to = apply_filters('social_login_redirect_to', $redirect_to);
	$social_login_provider = $_REQUEST['social_login_provider'];
	$foodbakery_provider_identity_key = 'social_login_' . $social_login_provider . '_id';
	$foodbakery_provided_signature = $_REQUEST['social_login_signature'];
	$foodbakery_email = '';
	switch ( $social_login_provider ) {
		case 'facebook':
			$fields = array(
				'id', 'name', 'first_name', 'last_name', 'link', 'website', 'picture',
				'gender', 'locale', 'about', 'email', 'hometown', 'location',
				'birthday'
			);
			
			foodbakery_social_login_verify_signature($_REQUEST['social_login_access_token'], $foodbakery_provided_signature, $redirect_to);
			$fb_json = json_decode(foodbakery_http_get_contents("https://graph.facebook.com/me?access_token=" . $_REQUEST['social_login_access_token'] . "&fields=" . implode(',', $fields)));
			$facebook_publisher_profile_url = $fb_json->picture->data->url;
			
			
			if ( isset($fb_json->error->type) ? $fb_json->error->type : '' == 'OAuthException' ) {
				?>
				<script>
					alert("<?php echo esc_html_e('Please check facebook account developers settings.', 'foodbakery'); ?>");
					window.close();
				</script>
				<?php
				exit();
			} else {
				$foodbakery_provider_identity = $fb_json->{ 'id' };
				$foodbakery_profile_pic = 'https://graph.facebook.com/' . $foodbakery_provider_identity . '/picture';
				$foodbakery_facebook = $fb_json->{ 'link' };
				$foodbakery_gender = $fb_json->{ 'gender' };
				if ( isset( $fb_json->email ) ) {
					$foodbakery_email = $fb_json->{ 'email' };
				}
				$foodbakery_first_name = $fb_json->{ 'first_name' };
				$foodbakery_last_name = $fb_json->{ 'last_name' };
				$foodbakery_profile_url = $fb_json->{ 'link' };
				$foodbakery_gender = $fb_json->{ 'gender' };
				$foodbakery_name = $foodbakery_first_name . ' ' . $foodbakery_last_name;
				$user_login = strtolower($foodbakery_first_name . $foodbakery_last_name);
			}
			break;
		case 'twitter':
			$facebook_publisher_profile_url = $_REQUEST['publisher_profile_image_url'];
			$foodbakery_provider_identity = $_REQUEST['social_login_twitter_identity'];
			foodbakery_social_login_verify_signature($foodbakery_provider_identity, $foodbakery_provided_signature, $redirect_to);
			$foodbakery_name = $_REQUEST['social_login_name'];
			
			$foodbakery_twitter = 'https://twitter.com/' . $_REQUEST['social_login_screen_name'];
			$names = explode(" ", $foodbakery_name);
			$foodbakery_first_name = '';
			if ( isset($names[0]) )
				$foodbakery_first_name = $names[0];
			$foodbakery_last_name = '';
			if ( isset($names[1]) )
				$foodbakery_last_name = $names[1];
			$foodbakery_screen_name = $_REQUEST['social_login_screen_name'];
			$foodbakery_publisher_image_id = $_REQUEST['social_profile_image_id'];
			$foodbakery_profile_url = '';
			$foodbakery_gender = '';
			// Get host name from URL
			$site_url = parse_url(site_url());
			$foodbakery_email = 'tw_' . md5($foodbakery_provider_identity) . '@' . $site_url['host'] . '.com';
			$user_login = $foodbakery_screen_name;
			break;
		default:

			break;
	}

	// Get user by meta
	$user_id = foodbakery_social_get_user_by_meta($foodbakery_provider_identity_key, $foodbakery_provider_identity);
	if ( $user_id ) {
		$current_user = get_userdata($user_id);
		$user_roles = isset($current_user->roles) ? $current_user->roles : '';
		if ( ($user_roles != '' && in_array("foodbakery_publisher", $user_roles) ) ) {
			$user_data = get_userdata($user_id);
			$user_login = $user_data->user_login;
			// update user meta
			update_user_meta($user_id, 'foodbakery_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
			update_user_meta($user_id, 'foodbakery_allow_search', 'yes');
			update_user_meta($user_id, 'foodbakery_user_status', 'active');
			if ( isset($foodbakery_facebook) && $foodbakery_facebook != '' ) {
				update_user_meta($user_id, 'foodbakery_facebook', $foodbakery_facebook);
			}
			if ( isset($foodbakery_twitter) && $foodbakery_twitter != '' ) {
				update_user_meta($user_id, 'foodbakery_twitter', $foodbakery_twitter);
			}
		} else {
			?>
			<script>
				alert("<?php echo esc_html_e('This profile is already linked with other account. Linking process failed!', 'foodbakery'); ?>");
				window.opener.location.reload();
				window.close();
			</script>
			<?php
			$ID = Null;	 // set null bcz this user exist in other Role
		}
	} elseif ( $user_id = email_exists($foodbakery_email) ) { // User not found by provider identity, check by email
		$current_user = get_userdata($user_id);
		$user_roles = isset($current_user->roles) ? $current_user->roles : '';
		if ( ($user_roles != '' && in_array("foodbakery_publisher", $user_roles) ) ) {
			// update user meta
			update_user_meta($user_id, $foodbakery_provider_identity_key, $foodbakery_provider_identity);
			$user_data = get_userdata($user_id);
			$user_login = $user_data->user_login;
			// update user meta
			update_user_meta($user_id, 'foodbakery_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
			update_user_meta($user_id, 'foodbakery_allow_search', 'yes');
			update_user_meta($user_id, 'foodbakery_user_status', 'active');
			if ( isset($foodbakery_facebook) && $foodbakery_facebook != '' ) {
				update_user_meta($user_id, 'foodbakery_facebook', $foodbakery_facebook);
			}
			if ( isset($foodbakery_twitter) && $foodbakery_twitter != '' ) {
				update_user_meta($user_id, 'foodbakery_twitter', $foodbakery_twitter);
			}
		} else {
			?>
			<script>
				alert("<?php echo esc_html_e('This profile is already linked with other account. Linking process failed!', 'foodbakery'); ?>");
				window.opener.location.reload();
				window.close();
			</script>
			<?php
			$ID = Null;	 // set null bcz this user exist in other Role
		}
	} else { // Create new user and associate provider identity
		if ( get_option('users_can_register') ) {
			if ( empty( $foodbakery_email ) )  {
				$data = array(
					'user_login' => $user_login,
					'user_email' => $foodbakery_email,
					'role' => 'foodbakery_publisher',
					'first_name' => $foodbakery_first_name,
					'last_name' => $foodbakery_last_name,
					'user_url' => $foodbakery_profile_url,
					'social_login_provider' => $social_login_provider,
					'social_meta_key' => $foodbakery_provider_identity_key, 
					'social_meta_value' => $foodbakery_provider_identity,
				);
				set_transient( 'social_data', $data, 60 );
				?>
				<script>
					alert("<?php echo esc_html_e('Email Address is required, kindly provide all details to complete registration.', 'foodbakery'); ?>");
					
					location.href = '<?php echo get_home_url(); ?>';
					
				</script>
				<?php
				wp_die();
			}
			$foodbakery_publisher_image_id = upload_publisher_profile_image($facebook_publisher_profile_url);
			$user_login = foodbakery_get_unique_username($user_login);
			$userdata = array( 'user_login' => $user_login, 'user_email' => $foodbakery_email, 'role' => 'foodbakery_publisher', 'first_name' => $foodbakery_first_name, 'last_name' => $foodbakery_last_name, 'user_url' => $foodbakery_profile_url, 'user_pass' => wp_generate_password() );
			// Create a new user
			$user_id = wp_insert_user($userdata);
			$user_id = (int) $user_id; // converting user id into int from object
			$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
			wp_set_password($random_password, $user_id);
			$reg_user = get_user_by('ID', $user_id);
			// Site owner email hook
			$user_login = isset($reg_user->data->user_login) ? $reg_user->data->user_login : '';
			$user_email = isset($reg_user->data->user_email) ? $reg_user->data->user_email : '';
			do_action('foodbakery_new_user_notification_site_owner', $user_login, $user_email);

			// send publisher email template hook
			do_action('foodbakery_publisher_register', $reg_user, $random_password);
			$new_user = new WP_User($user_id);
			// update user meta
			$new_user->set_role('foodbakery_publisher');
			update_user_meta($user_id, 'foodbakery_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
			update_user_meta($user_id, 'foodbakery_allow_search', 'yes');
			update_user_meta($user_id, 'foodbakery_user_status', 'active');

			$apply_job_id = get_transient('apply_job_id');
			if ( $apply_job_id && $apply_job_id != '' ) {
				$redirect_to = get_permalink((int) $apply_job_id);
			}
			$company_name = $foodbakery_first_name . ' ' . $foodbakery_last_name;
			$company_data = array(
				'post_title' => wp_strip_all_tags($company_name),
				'post_type' => 'publishers',
				'post_content' => '',
				'post_status' => 'publish',
				'post_author' => 1,
			);
			$company_ID = wp_insert_post($company_data);
			if ( $company_ID ) {
				update_user_meta($user_id, 'foodbakery_user_type', 'supper-admin');
				update_post_meta($company_ID, 'foodbakery_publisher_profile_type', 'buyer');

				if ( isset($foodbakery_publisher_image_id) && $foodbakery_publisher_image_id != '' ) {
					update_post_meta($company_ID, 'foodbakery_profile_image', $foodbakery_publisher_image_id);
				}
			}

			update_user_meta($user_id, 'foodbakery_is_admin', 1);
			update_user_meta($user_id, 'foodbakery_company', $company_ID);

			if ( isset($foodbakery_facebook) && $foodbakery_facebook != '' ) {
				update_user_meta($user_id, 'foodbakery_facebook', $foodbakery_facebook);
			}
			if ( isset($foodbakery_twitter) && $foodbakery_twitter != '' ) {
				update_user_meta($user_id, 'foodbakery_twitter', $foodbakery_twitter);
			}
			update_user_meta($user_id, 'foodbakery_user_registered', $social_login_provider);
			if ( $user_id && is_integer($user_id) ) {
				update_user_meta($user_id, $foodbakery_provider_identity_key, $foodbakery_provider_identity);
			}
			if ( isset($foodbakery_plugin_options['foodbakery_publisher_review_option']) && $foodbakery_plugin_options['foodbakery_publisher_review_option'] == 'on' ) {
				$wpdb->update(
						$wpdb->prefix . 'users', array( 'user_status' => 1 ), array( 'ID' => esc_sql($user_id) )
				);
				update_user_meta($user_id, 'foodbakery_user_status', 'active');
			} else {
				$wpdb->update(
						$wpdb->prefix . 'users', array( 'user_status' => 0 ), array( 'ID' => esc_sql($user_id) )
				);
				update_user_meta($user_id, 'foodbakery_user_status', 'inactive');
			}
		} else {
			add_filter('wp_login_errors', 'wp_login_errors');

			return;
		}
	}

	wp_set_auth_cookie($user_id);
	do_action('social_connect_login', $user_login);

	if ( $is_ajax ) {
		echo '{"redirect":"' . $redirect_to . '"}';
	} else {
		wp_safe_redirect($redirect_to);
	}

	exit();
}

// login error
function foodbakery_login_errors($errors) {
	$errors->errors = array();
	$errors->add('registration_disabled', '<strong>' . esc_html__('ERROR', 'foodbakery') . '</strong>:', esc_html__('Registration is disabled.', 'foodbakery'));




	return $errors;
}

// get unique username
function foodbakery_get_unique_username($user_login, $c = 1) {
	if ( username_exists($user_login) ) {
		if ( $c > 5 )
			$append = '_' . substr(md5($user_login), 0, 3) . $c;
		else
			$append = $c;

		$user_login = apply_filters('social_login_username_exists', $user_login . $append);
		return foodbakery_get_unique_username($user_login, ++ $c);
	} else {
		return $user_login;
	}
}

add_action('login_form_social_login', 'foodbakery_social_process_login');

// ajax login
function foodbakery_ajax_login() {
	if ( isset($_POST['login_submit']) && $_POST['login_submit'] == 'ajax' && // Plugins will need to pass this param
			isset($_POST['action']) && $_POST['action'] == 'social_login' )
		foodbakery_social_process_login(true);
}

add_action('init', 'foodbakery_ajax_login');

// filter user avatar
function foodbakery_filter_avatar($avatar, $id_or_email, $size, $default, $alt) {
	$custom_avatar = '';
	$social_id = '';
	$provider_id = '';
	$user_id = ( ! is_integer($id_or_email) && ! is_string($id_or_email) && get_class($id_or_email)) ? $id_or_email->user_id : $id_or_email;

	if ( ! empty($user_id) ) {
		$providers = array( 'facebook', 'twitter' );
		$social_login_provider = isset($_COOKIE['social_login_current_provider']) ? $_COOKIE['social_login_current_provider'] : '';
		if ( ! empty($social_login_provider) && $social_login_provider == 'twitter' ) {
			$providers = array( 'twitter', 'facebook' );
		}
		foreach ( $providers as $search_provider ) {
			$social_id = get_user_meta($user_id, 'social_login_' . $search_provider . '_id', true);
			if ( ! empty($social_id) ) {
				$provider_id = $search_provider;
				break;
			}
		}
	}
	if ( ! empty($social_id) ) {
		
	}

	if ( ! empty($custom_avatar) ) {
		update_user_meta($user_id, 'custom_avatar', $custom_avatar);
		$return = '<img class="avatar" src="' . esc_url($custom_avatar) . '" style="width:' . $size . 'px" alt="' . $alt . '" />';
	} else if ( $avatar ) {
		// gravatar
		$return = $avatar;
	} else {
		// default
		$return = '<img class="avatar" src="' . esc_url($default) . '" style="width:' . $size . 'px" alt="' . $alt . '" />';
	}

	return $return;
}

// social add comment meta
function foodbakery_social_add_comment_meta($comment_id) {
	$social_login_comment_via_provider = isset($_POST['social_login_comment_via_provider']) ? $_POST['social_login_comment_via_provider'] : '';
	if ( $social_login_comment_via_provider != '' ) {
		update_comment_meta($comment_id, 'social_login_comment_via_provider', $social_login_comment_via_provider);
	}
}

add_action('comment_post', 'foodbakery_social_add_comment_meta');

// social comment meta
function foodbakery_social_comment_meta($link) {
	global $comment;
	$images_url = get_template_directory_uri() . '/media/img/';
	if ( is_object($comment) ) {
		$social_login_comment_via_provider = get_comment_meta($comment->comment_ID, 'social_login_comment_via_provider', true);
		if ( $social_login_comment_via_provider && current_user_can('manage_options') ) {
			return $link . '&nbsp;<img class="social_login_comment_via_provider" alt="' . $social_login_comment_via_provider . '" src="' . $images_url . $social_login_comment_via_provider . '_16.png"  />';
		} else {
			return $link;
		}
	}
	return $link;
}

add_action('get_comment_author_link', 'foodbakery_social_comment_meta');

// social login form
function foodbakery_comment_form_social_login() {
	if ( comments_open() && ! is_user_logged_in() ) {
		foodbakery_social_login_form();
	}
}

// login page url
function foodbakery_login_page_uri() {
	global $foodbakery_form_fields_frontend;
	$foodbakery_opt_array = array(
		'id' => '',
		'cust_id' => 'social_login_form_uri',
		'std' => esc_url(site_url('wp-login.php', 'login_post')),
		'cust_type' => 'hidden',
		'classes' => '',
	);

	$foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
}

add_action('wp_footer', 'foodbakery_login_page_uri');

// get user by meta key
function foodbakery_social_get_user_by_meta($meta_key, $meta_value) {
	global $wpdb;
	$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
	return $wpdb->get_var($wpdb->prepare($sql, $meta_key, $meta_value));
}

// generate social signature
function foodbakery_social_generate_signature($data) {
	return hash('SHA256', AUTH_KEY . $data);
}

// login verify signature
function foodbakery_social_login_verify_signature($data, $signature, $redirect_to) {
	$generated_signature = foodbakery_social_generate_signature($data);
	if ( $generated_signature != $signature ) {
		wp_safe_redirect($redirect_to);
		exit();
	}
}

// get the contents of url
function foodbakery_http_get_contents($url) {
	$response = wp_remote_get($url);
	if ( is_wp_error($response) ) {
		die(sprintf(esc_html__('Something went wrong: %s', 'foodbakery'), $response->get_error_message()));
	} else {
		return $response['body'];
	}
}

// add custom styling
function foodbakery_add_stylesheets() {
	if ( is_admin() ) {
		if ( ! wp_style_is('social_login', 'registered') ) {

			wp_register_style("social_login_css", plugins_url('media/css/cs-social-style.css', __FILE__));
		}
		if ( did_action('wp_print_styles') ) {
			wp_print_styles('social_login');
			wp_print_styles('wp-jquery-ui-dialog');
		} else {
			wp_enqueue_style("social_login");
			wp_enqueue_style("wp-jquery-ui-dialog");
		}
	}
}

add_action('login_enqueue_scripts', 'foodbakery_add_stylesheets');
add_action('wp_head', 'foodbakery_add_stylesheets');

// add admin side styling
function foodbakery_add_admin_stylesheets() {
	if ( is_admin() ) {
		if ( ! wp_style_is('social_login', 'registered') ) {
			wp_register_style("social_login_css", plugins_url('media/css/cs-social-style.css', __FILE__));
		}

		if ( did_action('wp_print_styles') ) {
			wp_print_styles('social_login');
		} else {
			wp_enqueue_style("social_login");
		}
	}
}

add_action('admin_print_styles', 'foodbakery_add_admin_stylesheets');

// add javascripts files
function foodbakery_add_javascripts() {
	if ( is_admin() ) {
		$deps = array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog' );
		$wordpress_enabled = 0;


		if ( $wordpress_enabled ) {
			$deps[] = 'jquery-ui-dialog';
		}

		if ( ! wp_script_is('social_login_js', 'registered') )
			wp_register_script('social_login_js', plugins_url('media/js/cs-connect.js', __FILE__), $deps);

		wp_enqueue_script('social_login_js');
		wp_localize_script('social_login_js', 'social_login_data', array( 'wordpress_enabled' => $wordpress_enabled ));
	}
}

add_action('login_enqueue_scripts', 'foodbakery_add_javascripts');
add_action('wp_enqueue_scripts', 'foodbakery_add_javascripts');

// Twitter Callback

function foodbakery_twitter_callback() {
	global $foodbakery_plugin_options;
	$consumer_key = isset($foodbakery_plugin_options['foodbakery_consumer_key']) ? $foodbakery_plugin_options['foodbakery_consumer_key'] : '';
	$consumer_secret = isset($foodbakery_plugin_options['foodbakery_consumer_secret']) ? $foodbakery_plugin_options['foodbakery_consumer_secret'] : '';

	if ( ! class_exists('TwitterOAuth') ) {
		require_once wp_foodbakery::plugin_dir() . 'include/cs-twitter/twitteroauth.php';
	}
	$oauth_token = get_transient('oauth_token');
	$oauth_token_secret = get_transient('oauth_token_secret');
	if ( ! empty($oauth_token) && ! empty($oauth_token_secret) ) {
		$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
		$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
		set_transient('access_token', $access_token, (3600 * 60) * 24);
		delete_transient('oauth_token');
		delete_transient('oauth_token_secret');
	}

	if ( 200 == $connection->http_code ) {
		set_transient('status', 'verified', (3600 * 60) * 24);
		$user = $connection->get('account/verify_credentials');
		$publisher_profile_image_url = $user->profile_image_url;
		$publisher_profile_image_id = '';
		if ( isset($publisher_profile_image) && $publisher_profile_image != '' ) {
		
		}
		$name = $user->name;
		$screen_name = $user->screen_name;
		$twitter_id = $user->id;
		$signature = foodbakery_social_generate_signature($twitter_id);
		?>
		<html>
			<head>
				<script>
					function init() {
						window.opener.wp_social_login({'action': 'social_login', 'social_login_provider': 'twitter',
							'social_login_signature': '<?php echo esc_attr($signature) ?>',
							'social_login_twitter_identity': '<?php echo esc_attr($twitter_id) ?>',
							'social_login_screen_name': '<?php echo esc_attr($screen_name) ?>',
							'publisher_profile_image_url': '<?php echo esc_attr($publisher_profile_image_url) ?>',
							'social_login_name': '<?php echo esc_attr($name) ?>'});
						window.close();
					}
				</script>
			</head>
			<body onLoad="init();"></body>
		</html>
		<?php
		die();
	} else {

		echo esc_html__('Login error', 'foodbakery');
	}
}

function upload_publisher_profile_image($publisher_profile_image) {
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	$wp_upload_dir = wp_upload_dir();
	$image_data = file_get_contents($publisher_profile_image);
	$publisher_profile_image = explode('?', $publisher_profile_image);
	$publisher_profile_image = $publisher_profile_image[0];
	$filename = basename($publisher_profile_image);
	$file = $wp_upload_dir['path'] . '/' . $filename;

	file_put_contents($file, $image_data);

	$filetype = wp_check_filetype(basename($publisher_profile_image), null);
	$attachment = array(
		'post_mime_type' => $filetype['type'],
		'post_title' => preg_replace('/\.[^.]+$/', '', basename($publisher_profile_image)),
		'guid' => $wp_upload_dir['url'] . '/' . basename($publisher_profile_image),
	);

	$profile_image_id = wp_insert_attachment($attachment, $file);
	$attach_data = wp_generate_attachment_metadata($profile_image_id, $file);
	wp_update_attachment_metadata($profile_image_id, $attach_data);
	$publisher_profile_image_id = $profile_image_id;
	return $publisher_profile_image_id;
}

// Twitter connect
function foodbakery_twitter_connect() {
	global $foodbakery_plugin_options;
	if ( ! class_exists('TwitterOAuth') ) {
		require_once wp_foodbakery::plugin_dir() . 'include/cs-twitter/twitteroauth.php';
	}
	$consumer_key = $foodbakery_plugin_options['foodbakery_consumer_key'];
	$consumer_secret = $foodbakery_plugin_options['foodbakery_consumer_secret'];
	$twitter_oath_callback = home_url('index.php?social-login=twitter-callback');
	if ( $consumer_key != '' && $consumer_secret != '' ) {
		$connection = new TwitterOAuth($consumer_key, $consumer_secret);
		$request_token = $connection->getRequestToken($twitter_oath_callback);

		if ( ! empty($request_token) ) {
			set_transient('oauth_token', $request_token['oauth_token'], (3600 * 60) * 24);
			set_transient('oauth_token_secret', $request_token['oauth_token_secret'], (3600 * 60) * 24);
			$token = $request_token['oauth_token'];
		}

		switch ( $connection->http_code ) {
			case 200:
				$url = $connection->getAuthorizeURL($token);
				wp_redirect($url);
				break;
			default:
				echo esc_html($connection->http_code);
				esc_html_e('There is problem while connecting to twitter', 'foodbakery');
		}
		exit();
	}
}

// Facebook Callback

function foodbakery_facebook_callback() {
	global $foodbakery_plugin_options;

	require_once plugin_dir_url(__FILE__) . 'facebook/facebook.php';


	$client_id = $foodbakery_plugin_options['foodbakery_facebook_app_id'];
	$secret_key = $foodbakery_plugin_options['foodbakery_facebook_secret'];


	if ( isset($_GET['code']) ) {
		$code = $_GET['code'];
		$access_token = $code;
		parse_str(foodbakery_http_get_contents("https://graph.facebook.com/oauth/access_token?" .
						'client_id=' . $client_id . '&redirect_uri=' . home_url('index.php?social-login=facebook-callback') .
						'&client_secret=' . $secret_key .
						'&code=' . urlencode($code)));
		$signature = foodbakery_social_generate_signature($access_token);
		do_action('social_login_before_register_facebook', $code, $signature, $access_token);
		?>
		<html>
			<head>
				<script>
					function init() {
						window.opener.wp_social_login({'action': 'social_login', 'social_login_provider': 'facebook',
							'social_login_signature': '<?php echo esc_attr($signature) ?>',
							'social_login_access_token': '<?php echo esc_attr($access_token) ?>'});
						window.close();
					}
				</script>
			</head>
			<body onLoad="init();"></body>
		</html>
		<?php
	} else {
		$redirect_uri = urlencode(plugin_dir_url(__FILE__) . 'facebook/callback.php');
		wp_redirect('https://graph.facebook.com/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email');
	}
}
