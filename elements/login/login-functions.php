<?php

/**
 * @Generate Random String
 *
 *
 */
if (!function_exists('foodbakery_generate_random_string')) {

    function foodbakery_generate_random_string($length = 3) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i ++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

}

function foodbakery_user_last_login($user_login, $user) {
    $company_id = get_user_meta($user->ID, 'foodbakery_company', true);
    update_post_meta($company_id, 'last_login', time());
}

add_action('wp_login', 'foodbakery_user_last_login', 10, 2);
/*
 *
 * Start Function  for if user exist using Ajax
 *
 */
if (!function_exists('ajax_login')) :

    function ajax_login() {
        global $foodbakery_plugin_options, $wpdb;
        $credentials = array();

        $foodbakery_danger_html = '<div class="alert alert-danger"><p><i class="icon-warning4"></i>';

        $foodbakery_success_html = '<div class="alert alert-success"><p><i class="icon-checkmark6"></i>';

        $foodbakery_msg_html = '</p></div>';

        $credentials['user_login'] = esc_sql($_POST['user_login']);
        $credentials['user_password'] = esc_sql($_POST['user_pass']);

        if (isset($_POST['rememberme'])) {
            $remember = esc_sql($_POST['rememberme']);
        } else {
            $remember = '';
        }
        if ($remember) {
            $credentials['remember'] = true;
        } else {
            $credentials['remember'] = false;
        }
        if ($credentials['user_login'] == '') {

            $json['type'] = "error";
            $json['msg'] = esc_html__("Username should not be empty.", "foodbakery");
            echo json_encode($json);
            exit();
        } elseif ($credentials['user_password'] == '') {

            $json['type'] = "error";
            $json['msg'] = esc_html__("Password should not be empty.", "foodbakery");
            echo json_encode($json);
            exit();
        } else {
            $user_status = '0';
            $user = get_user_by('login', $credentials['user_login']);
            if (is_object($user) && isset($user->ID)) {
                $user_id = $user->ID;
                $user_status = $user->user_status;
                $user_status_profile = get_user_meta($user_id, 'foodbakery_user_status', true);
                if ($user && wp_check_password($credentials['user_password'], $user->data->user_pass, $user_id)) {
                    if ($user_status == '0') {

                        $json['type'] = "error";
                        $json['msg'] = esc_html__("Your account is not activated yet.", "foodbakery");
                        echo json_encode($json);
                        die;
                    } elseif ($user_status_profile == 'deleted') {

                        $json['type'] = "error";
                        $json['msg'] = esc_html__("Your Profile has been removed from company", "foodbakery");
                        echo json_encode($json);
                        die;
                    }
                } else {

                    $json['type'] = "error";
                    $json['msg'] = esc_html__("Invalid password.", "foodbakery");
                    echo json_encode($json);
                    die;
                }
            }

            $status = wp_signon($credentials, false);
            if (is_wp_error($status)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("Invalid username or password.", "foodbakery");
                echo json_encode($json);
            } else {
                $user_roles = isset($status->roles) ? $status->roles : '';

                $uid = $status->ID;
                $foodbakery_user_name = $_POST['user_login'];
                $foodbakery_login_user = get_user_by('login', $foodbakery_user_name);
                $foodbakery_page_id = '';
                $default_url = $_POST['redirect_to'];
                if (($user_roles != '' && in_array("foodbakery_publisher", $user_roles))) {
                    $foodbakery_page_id = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : $default_url;
                }
                // update user last activity
                update_user_meta($uid, 'foodbakery_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
                $foodbakery_redirect_url = '';
                if (isset($_POST['checkout_login'])) {
                    $order_id = get_post_meta($_POST['trans_id'], 'foodbakery_order_id', true);
                    $publisher_id = foodbakery_company_id_form_user_id($uid);

                    update_post_meta($order_id, 'foodbakery_order_user', $publisher_id);
                    $foodbakery_redirect_url = $default_url;
                } else {
                    if ($foodbakery_page_id != '') {
                        /* redirect to the current restaurant URL */
                        $user_company_id = get_user_meta($uid, 'foodbakery_company', true);
                        $publisher_profile_type = get_post_meta($user_company_id, 'foodbakery_publisher_profile_type', true);
                        if (strpos($default_url, '/restaurants') !== false && $publisher_profile_type != 'restaurant') {
                            $foodbakery_redirect_url = $default_url;
                        }else{
                            //$foodbakery_redirect_url = get_permalink($foodbakery_page_id);
                             $foodbakery_redirect_url = $default_url;
                        }
                    } else {
                        $foodbakery_redirect_url = $default_url;  // home URL if page not set
                    }
                }
                $json['type'] = "success";
                $json['msg'] = esc_html__("Login Successfully...", "foodbakery");
                $json['redirecturl'] = $foodbakery_redirect_url;
                echo json_encode($json);
            }
        }
        die();
    }

endif;
add_action('wp_ajax_ajax_login', 'ajax_login');
add_action('wp_ajax_nopriv_ajax_login', 'ajax_login');
/*
 *
 * Start Function  for  user registration validation 
 *
 */
if (!function_exists('foodbakery_registration_validation')) {

    function foodbakery_registration_validation($atts = '', $given_params = '') {

        global $wpdb, $foodbakery_plugin_options, $foodbakery_form_fields_frontend;

        $foodbakery_danger_html = '<div class="alert alert-danger"><p><i class="icon-warning4"></i>';
        $foodbakery_success_html = '<div class="alert alert-success"><p><i class="icon-checkmark6"></i>';
        $foodbakery_msg_html = '</p></div>';

        if ($given_params != '' && is_array($given_params)) {
            extract($given_params);
        } else {
            $id = isset($_POST['id']) ? $_POST['id'] : ''; //rand id 
            $username = isset($_POST['user_login' . $id]) ? $_POST['user_login' . $id] : '';

            $profile_type = isset($_POST['foodbakery_profile_type' . $id]) ? $_POST['foodbakery_profile_type' . $id] : '';
            $email = isset($_POST['foodbakery_user_email' . $id]) ? $_POST['foodbakery_user_email' . $id] : '';
            $password = isset($_POST['foodbakery_user_password' . $id]) ? $_POST['foodbakery_user_password' . $id] : '';
            $foodbakery_user_role_type = (isset($_POST['foodbakery_user_role_type' . $id]) and $_POST['foodbakery_user_role_type' . $id] <> '') ? $_POST['foodbakery_user_role_type' . $id] : '';
            $key = isset($_POST['key']) ? $_POST['key'] : '';
            $display_name = foodbakery_get_input('foodbakery_display_name' . $id, NULL, 'STRING');
        }

        $first_name = foodbakery_get_input('foodbakery_first_name' . $id, NULL, 'STRING');
        $last_name = foodbakery_get_input('foodbakery_last_name' . $id, NULL, 'STRING');
       if($display_name == ''){
            /*$first_name = foodbakery_get_input('foodbakery_first_name' . $id, NULL, 'STRING');
            $last_name = foodbakery_get_input('foodbakery_last_name' . $id, NULL, 'STRING');*/
            $display_name = $first_name.$last_name;
       }
        $company_name = foodbakery_get_input('foodbakery_company_name' . $id, NULL, 'STRING');
        $company_field = foodbakery_get_input('foodbakery_company_name' . $id, NULL, 'STRING');
        if ($company_name == NULL) {
            $company_name = $display_name;
        }

        $password = wp_generate_password($length = 12, $include_standard_special_chars = false);

        $key_data = get_option($key);

        $json = array();
        $foodbakery_captcha_switch = isset($foodbakery_plugin_options['foodbakery_captcha_switch']) ? $foodbakery_plugin_options['foodbakery_captcha_switch'] : '';
        $foodbakery_cs_terms_condition_check = isset($foodbakery_plugin_options['foodbakery_cs_terms_condition_check']) ? $foodbakery_plugin_options['foodbakery_cs_terms_condition_check'] : '';

        if ($given_params == '') {
            if($first_name == ''){
                $json['type'] = "error";
                $json['msg'] = esc_html__("User First name should not be empty.", "foodbakery");
                echo json_encode($json);
                exit();
            }
            if($last_name == ''){
                $json['type'] = "error";
                $json['msg'] = esc_html__("User last name should not be empty.", "foodbakery");
                echo json_encode($json);
                exit();
            }
            if (empty($username)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("Username should not be empty.", "foodbakery");
                echo json_encode($json);
                exit();
            } elseif (!preg_match('/^[a-zA-Z0-9_]{5,}$/', $username)) { // for english chars + numbers only
                $json['type'] = "error";
                $json['msg'] = esc_html__("Please enter a valid username. You can only enter alphanumeric value and only ( _ ) longer than or equals 5 chars", "foodbakery");
                echo json_encode($json);
                exit();
            }

            if (empty($email)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("Email Field should not be empty.", "foodbakery");
                echo json_encode($json);
                exit();
            }
            if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("Please enter a valid email.", "foodbakery");
                echo json_encode($json);
                exit();
            }

            if ($foodbakery_captcha_switch == 'on') {
                do_action('foodbakery_verify_captcha_form');
            }
            if (isset($foodbakery_cs_terms_condition_check) && $foodbakery_cs_terms_condition_check == 'on') {
                do_action('foodbakery_verify_terms_policy', $_POST);
            }
        }

        if (empty($profile_type)) {

            $json['type'] = "error";
            $json['msg'] = esc_html__("Profile Type should not be empty.", "foodbakery");
            echo json_encode($json);
            exit();
        }



        if ($password == '') {
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        } else {
            $random_password = $password;
        }

        if (!email_exists($email)) {

            /*input fields*/
            $first_name = foodbakery_get_input('foodbakery_first_name' . $id, NULL, 'STRING');
            $last_name = foodbakery_get_input('foodbakery_last_name' . $id, NULL, 'STRING');
            /*insert user*/
            $status = wp_create_user($username, $random_password, $email);
            $reg_user = get_user_by('ID', $status);
            $user_id = $reg_user->data->ID;
            /*User meta*/
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);

        } else {
            $json['type'] = "error";
            $json['msg'] = esc_html__("Sorry! Email already exists.", "foodbakery");
            echo json_encode($json);

            die;
        }

        if ($status) {
            update_user_meta($status, 'display_name', wp_strip_all_tags($display_name));
            $foodbakery_userdata = array('display_name' => $display_name);
            wp_update_user(array('ID' => $status, 'display_name' => $display_name));
        }
        if (is_wp_error($status)) {
            if ($given_params != '' && is_array($given_params)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("User already exists. Please try another one.", "foodbakery");
                echo json_encode($json);

                die;
            } else {
                $json['type'] = "error";
                $json['msg'] = esc_html__("Sorry! Username already exists.", "foodbakery");
                echo json_encode($json);

                die;
            }
        } else {
            global $wpdb;
            $signup_user_role = '';
            if ($foodbakery_user_role_type == 'publisher') {
                $signup_user_role = 'foodbakery_publisher';
            }
            wp_update_user(array('ID' => esc_sql($status), 'role' => esc_sql($signup_user_role), 'user_status' => 1));
            $wpdb->update(
                    $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($status))
            );
            update_user_meta($status, 'show_admin_bar_front', false);
            // set extra status only for delete user maintain
            update_user_meta($status, 'foodbakery_user_status', 'active');

            if (isset($_POST['social_meta_key']) && isset($_POST['social_meta_value'])) {
                update_user_meta($status, $_POST['social_meta_key'], $_POST['social_meta_value']);
            }

            /*
             * Setting up permissions
             */
            $key = foodbakery_get_input('key', NULL, 'STRING');
            if ($key != NULL) {
                $key_data = get_option($key);
            }

            if (isset($key_data['permissions']) && !empty($key_data['permissions'])) {
                update_user_meta($status, 'foodbakery_permissions', $key_data['permissions']);
            }

            if (isset($_POST['key']) && $_POST['key'] != '') {
                $invited_by = $key_data['invited_by'];
                $company_ID = get_user_meta($invited_by, 'foodbakery_company', true);
                update_user_meta($status, 'foodbakery_company', $company_ID);
                update_user_meta($status, 'foodbakery_is_admin', 0);
            } else {
                update_user_meta($status, 'foodbakery_is_admin', 1);

                /*
                 * Inserting Publisher while registering user
                 */
                $company_phone = foodbakery_get_input('foodbakery_phone_no' . $id, NULL, 'STRING');
                $company_data = array(
                    'post_title' => wp_strip_all_tags($display_name),
                    'post_type' => 'publishers',
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => 1,
                );
                $company_ID = wp_insert_post($company_data);
                if ($company_ID) {
                    update_user_meta($status, 'foodbakery_user_type', 'supper-admin');
                    update_post_meta($company_ID, 'foodbakery_publisher_company_name', $company_name);
                    update_post_meta($company_ID, 'foodbakery_email_address', $email);

                    if (isset($_POST['foodbakery_profile_type' . $id])) {
                        update_post_meta($company_ID, 'foodbakery_publisher_profile_type', $_POST['foodbakery_profile_type' . $id]);
                    } else {
                        update_post_meta($company_ID, 'foodbakery_publisher_profile_type', 'restaurant');
                    }
                    if (isset($foodbakery_plugin_options['foodbakery_publisher_review_option']) && $foodbakery_plugin_options['foodbakery_publisher_review_option'] == 'on') {
                        update_post_meta($company_ID, 'foodbakery_user_status', 'active');
                    }
                }
                update_user_meta($status, 'foodbakery_company', $company_ID);

                $user_type = get_post_meta($company_ID, 'foodbakery_publisher_profile_type', true);

                if ($user_type == 'restaurant') {
                    // insert Restaurant for Member

                    if ($given_params != '' && is_array($given_params)) {
                        
                    } else {
                        $res_data = array(
                            'post_title' => wp_strip_all_tags($display_name),
                            'post_type' => 'restaurants',
                            'post_content' => '',
                            'post_status' => 'publish',
                            'post_author' => 1,
                        );
                        $restaurant_ID = wp_insert_post($res_data);
                        if ($restaurant_ID) {
                            update_post_meta($restaurant_ID, 'foodbakery_restaurant_publisher', $company_ID);
                            update_post_meta($restaurant_ID, 'foodbakery_restaurant_username', $status);
                            update_post_meta($restaurant_ID, 'foodbakery_restaurant_posted', strtotime(date('d-m-Y H:i:s')));
                            update_post_meta($restaurant_ID, 'foodbakery_restaurant_expired', strtotime(date('d-m-Y H:i:s')));

                            $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
                            if (isset($restaurants_type_post[0]->post_name) && $restaurants_type_post[0]->post_name != '') {
                                update_post_meta($restaurant_ID, 'foodbakery_restaurant_type', $restaurants_type_post[0]->post_name);
                            }
                        }
                    }
                }
            }

            // send email to user

            $reg_user = get_user_by('ID', $status);
            if (isset($reg_user->roles) && in_array('foodbakery_publisher', $reg_user->roles)) {
                // Site owner email hook
                do_action('foodbakery_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
                do_action('foodbakery_user_register', $reg_user, $random_password);

                if (class_exists('foodbakery_register_email_template') && isset(Foodbakery_register_email_template::$is_email_sent1)) {
                    
                    $json['type'] = "success";
                    $json['msg'] = esc_html__("Please check your email for login details.", "foodbakery");
                } else {

                    $json['type'] = "error";
                    $json['msg'] = esc_html__("Something went wrong, Email could not be processed..", "foodbakery");
                }
            } else {
                // Site owner email hook
                do_action('foodbakery_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
                do_action('foodbakery_user_register', $reg_user, $random_password);

                if (class_exists('foodbakery_register_email_template') && isset(Foodbakery_register_email_template::$is_email_sent1)) {
                    $json['type'] = "success";
                    $json['msg'] = esc_html__("Please check your email for login details.", "foodbakery");
                } else {
                    $json['type'] = "error";
                    $json['msg'] = esc_html__("Something went wrong, Email could not be processed..", "foodbakery");
                }
            }
            $foodbakery_comp_name = '';
            $foodbakery_specialisms = '';
            $foodbakery_phone_no = '';
            // update user meta by role
            if ($foodbakery_user_role_type == 'publisher') {
                if ($given_params != '' && is_array($given_params)) {
                    $foodbakery_comp_name = isset($_POST['foodbakery_organization_name' . $id]) ? $_POST['foodbakery_organization_name' . $id] : '';
                    $foodbakery_specialisms = isset($_POST['foodbakery_publisher_specialisms' . $id]) ? $_POST['foodbakery_organization_name' . $id] : '';
                }
                if (isset($foodbakery_plugin_options['foodbakery_publisher_review_option']) && $foodbakery_plugin_options['foodbakery_publisher_review_option'] == 'on') {
                    $wpdb->update(
                            $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($status))
                    );
                    update_user_meta($status, 'profile_approved', 1);
                } else {
                    $wpdb->update(
                            $wpdb->prefix . 'users', array('user_status' => 0), array('ID' => esc_sql($status))
                    );
                    update_user_meta($status, 'profile_approved', 0);
                }
            }
            update_user_meta($status, 'foodbakery_user_last_activity_date', strtotime(date('d-m-Y')));
            update_user_meta($status, 'foodbakery_allow_search', 'yes');
            if ($given_params != '' && is_array($given_params)) {
                return array($company_ID, $status);
            }
            echo json_encode($json);
            die;
        }
        die();
    }
    add_action('wp_ajax_foodbakery_registration_validation', 'foodbakery_registration_validation');
    add_action('wp_ajax_nopriv_foodbakery_registration_validation', 'foodbakery_registration_validation');
}
add_action('foodbakery_verify_terms_policy', 'foodbakery_verify_terms_policy_callback', 10, 1);
if (!function_exists('foodbakery_verify_terms_policy_callback')) {
    function foodbakery_verify_terms_policy_callback($array_data = array()) {
        global $foodbakery_plugin_options;
        $cs_terms_policy_switch = isset($foodbakery_plugin_options['foodbakery_cs_terms_condition_check']) && !empty($foodbakery_plugin_options['foodbakery_cs_terms_condition_check']) ? $foodbakery_plugin_options['foodbakery_cs_terms_condition_check'] : '';
        $cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';
        $cs_msg_html = '</p></div>';
        $id = $array_data['id']; //rand id 
        $terms_check = $array_data['terms-' . $id];
        if (empty($terms_check) && $cs_terms_policy_switch == 'on') {
            $json['type'] = "error";
            $json['msg'] = esc_html__("Please check and accept Terms and Conditions to Register Successfully.", "foodbakery");
            echo json_encode($json);
            exit();
        } else {
            
        }
    }
}
if (!function_exists('foodbakery_contact_validation')) {
    function foodbakery_contact_validation($atts = '') {
        global $wpdb, $foodbakery_plugin_options, $foodbakery_form_fields_frontend;
        $id = rand(10000000, 91564689); //rand id 
        $username = $_POST['user_login' . $id];
        $json = array();
        if ($foodbakery_captcha_switch == 'on') {
            foodbakery_captcha_verify();
        }
        if (is_wp_error($status)) {
            $json['type'] = "error";
            $json['message'] = esc_html__("Currently there are and issue", "foodbakery");
            echo json_encode($json);
            die;
        } else {
            $json['type'] = "error";
            $json['message'] = esc_html__("Your account has been registered successfully, Please contact to site admin for password.", "foodbakery");
        }
        echo json_encode($json);
        die;
    }
    add_action('wp_ajax_foodbakery_registration_validation', 'foodbakery_registration_validation');
    add_action('wp_ajax_nopriv_foodbakery_registration_validation', 'foodbakery_registration_validation');
}

add_action('user_register', 'foodbakery_registration_save', 10, 1);
if (!function_exists('foodbakery_registration_save')) {

    function foodbakery_registration_save($user_id) {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'register') {
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
            wp_set_password($random_password, $user_id);
            $reg_user = get_user_by('ID', $user_id);
            if (isset($reg_user->roles) && (in_array('subscriber', $reg_user->roles) || in_array('editor', $reg_user->roles) || in_array('author', $reg_user->roles))) {
                // Site owner email hook
                do_action('foodbakery_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
                // normal user email hook
                do_action('foodbakery_user_register', $reg_user, $random_password);
            }
        }
    }

}
