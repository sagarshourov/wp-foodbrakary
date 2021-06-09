<?php
//=====================================================================
// Sign In With Social Media
//=====================================================================

if (!function_exists('foodbakery_pb_register')) {

    function foodbakery_pb_register($die = 0) {

        global $foodbakery_form_fields_frontend, $foodbakery_html_fields_frontend;
        $shortcode_element = '';
        $filter_element = 'filterdrag';
        $shortcode_view = '';
        $output = array();
        $PREFIX = 'foodbakery_register';
        $counter = $_POST['counter'];

        $foodbakery_counter = $_POST['counter'];
        if (isset($_POST['action']) && !isset($_POST['shortcode_element_id'])) {
            $POSTID = '';
            $shortcode_element_id = '';
        } else {
            $parseObject = new ShortcodeParse();
            $POSTID = $_POST['POSTID'];
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->foodbakery_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array('publisher_register_element_title' => '');
        if (isset($output['0']['atts'])) {
            $atts = $output['0']['atts'];
        } else {
            $atts = array();
        }
        if (isset($output['0']['content'])) {
            $atts_content = $output['0']['content'];
        } else {
            $atts_content = array();
        }
        $button_element_size = '100';
        foreach ($defaults as $key => $values) {
            if (isset($atts[$key])) {
                $$key = $atts[$key];
            } else {
                $$key = $values;
            }
        }
        $name = 'foodbakery_pb_register';

        $coloumn_class = 'column_' . $button_element_size;

        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }

        $rand_id = rand(45, 897009);
        ?>

        <div id="<?php echo esc_attr($name . $foodbakery_counter); ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="register" data="<?php echo foodbakery_element_size_data_array_index($button_element_size) ?>" >
            <?php foodbakery_element_setting($name, $foodbakery_counter, $button_element_size, '', 'heart'); ?>
            <div class="cs-wrapp-class-<?php echo esc_attr($foodbakery_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>" data-shortcode-template="[foodbakery_register {{attributes}}]" style="display: none;">
                <div class="cs-heading-area">

                    <h5><?php esc_html_e('WPD: Register Options', 'foodbakery'); ?></h5>
                    <a href="javascript:removeoverlay('<?php echo esc_attr($name . $foodbakery_counter) ?>','<?php echo esc_attr($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a>
                </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp cs-pbwp-content">

                    </div>
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">
                        <?php
                        $foodbakery_opt_array = array(
                            'name' => esc_html__('Element Title', 'foodbakery'),
                            'desc' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $publisher_register_element_title,
                                'id' => 'publisher_register_element_title',
                                'cust_name' => 'publisher_register_element_title[]',
                                'return' => true,
                            ),
                        );

                        $foodbakery_html_fields_frontend->foodbakery_text_field($foodbakery_opt_array);
                        ?>
                    </div>
                    <?php if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                        ?>
                        <ul class="form-elements insert-bg">
                            <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('foodbakery_pb_', '', $name)); ?>', '<?php echo esc_js($name . $foodbakery_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php esc_html_e('Insert', 'foodbakery'); ?></a> </li>
                        </ul>
                        <div id="results-shortocde"></div>
                        <?php
                    } else {

                        $foodbakery_opt_array = array(
                            'std' => esc_html__('register', 'foodbakery'),
                            'id' => '',
                            'before' => '',
                            'after' => '',
                            'classes' => '',
                            'extra_atr' => '',
                            'cust_id' => '',
                            'cust_name' => 'foodbakery_orderby[]',
                            'return' => false,
                            'required' => false
                        );
                        $foodbakery_form_fields_frontend->foodbakery_form_hidden_render($foodbakery_opt_array);


                        $foodbakery_opt_array = array(
                            'name' => '',
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => esc_html__('Save', 'foodbakery'),
                                'cust_id' => '',
                                'cust_type' => 'button',
                                'classes' => 'cs-admin-btn',
                                'cust_name' => '',
                                'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                'return' => true,
                            ),
                        );

                        $foodbakery_html_fields_frontend->foodbakery_text_field($foodbakery_opt_array);
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        if ($die <> 1) {
            die();
        }
    }

    add_action('wp_ajax_foodbakery_pb_register', 'foodbakery_pb_register');
}

/*
 *
 * Start Function  how to login from social site(facebook, linkedin,twitter,etc)
 *
 */
if (!function_exists('foodbakery_social_login_form')) {

    function foodbakery_social_login_form($args = NULL) {


        global $foodbakery_plugin_options, $foodbakery_form_fields_frontend;
        $display_label = false;
        // check for admin login form
        $admin_page = '0';
        if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
            $admin_page = '1';
        }
        if (get_option('users_can_register') && $admin_page == 0) {
            if ($args == NULL)
                $display_label = true;
            elseif (is_array($args))
                extract($args);
            if (!isset($images_url))
                $images_url = wp_foodbakery::plugin_url() . 'foodbakery-login/cs-social-login/media/img/';
            $facebook_app_id = '';
            $facebook_secret = '';
            if (isset($foodbakery_plugin_options['foodbakery_dashboard'])) {
                $foodbakery_dashboard_link = get_permalink($foodbakery_plugin_options['foodbakery_dashboard']);
            }
            $twitter_enabled = isset($foodbakery_plugin_options['foodbakery_twitter_api_switch']) ? $foodbakery_plugin_options['foodbakery_twitter_api_switch'] : '';
            $facebook_enabled = isset($foodbakery_plugin_options['foodbakery_facebook_login_switch']) ? $foodbakery_plugin_options['foodbakery_facebook_login_switch'] : '';
            $google_enabled = isset($foodbakery_plugin_options['foodbakery_google_login_switch']) ? $foodbakery_plugin_options['foodbakery_google_login_switch'] : '';

            if (isset($foodbakery_plugin_options['foodbakery_facebook_app_id']))
                $facebook_app_id = $foodbakery_plugin_options['foodbakery_facebook_app_id'];
            if (isset($foodbakery_plugin_options['foodbakery_facebook_secret']))
                $facebook_secret = $foodbakery_plugin_options['foodbakery_facebook_secret'];
            if (isset($foodbakery_plugin_options['foodbakery_consumer_key']))
                $twitter_app_id = $foodbakery_plugin_options['foodbakery_consumer_key'];
            if (isset($foodbakery_plugin_options['foodbakery_google_client_id']))
                $google_app_id = $foodbakery_plugin_options['foodbakery_google_client_id'];
            if ($twitter_enabled == 'on' || $facebook_enabled == 'on' || $google_enabled == 'on') :
                $rand_id = rand(0, 98989899);
                $isRegistrationOn = get_option('users_can_register');
                if ($isRegistrationOn) {
                    ?>
                    <div class="footer-element comment-form-social-connect social_login_ui <?php if (strpos($_SERVER['REQUEST_URI'], 'wp-signup.php')) echo 'mu_signup'; ?>">
                        <div class="social_login_facebook_auth">
                            <?php
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => esc_attr($facebook_app_id),
                                'cust_id' => "",
                                'cust_name' => "client_id",
                                'classes' => '',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_hidden_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => home_url('index.php?social-login=facebook-callback'),
                                'cust_id' => "",
                                'cust_name' => "redirect_uri",
                                'classes' => '',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_hidden_render($foodbakery_opt_array);
                            ?>
                        </div>
                        <div class="social_login_twitter_auth">
                            <?php
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => esc_attr($twitter_app_id),
                                'cust_id' => "",
                                'cust_name' => "client_id",
                                'classes' => '',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_hidden_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => home_url('index.php?social-login=twitter'),
                                'cust_id' => "",
                                'cust_name' => "redirect_uri",
                                'classes' => '',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_hidden_render($foodbakery_opt_array);
                            ?>
                        </div>
                        <div class="social_login_google_auth">
                            <?php
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => esc_attr($google_app_id),
                                'cust_id' => "",
                                'cust_name' => "client_id",
                                'classes' => '',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_hidden_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => foodbakery_google_login_url() . (isset($_GET['redirect_to']) ? '&redirect=' . $_GET['redirect_to'] : ''),
                                'cust_id' => "",
                                'cust_name' => "redirect_uri",
                                'classes' => '',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_hidden_render($foodbakery_opt_array);
                            ?>
                        </div>

                        <div class="social-media">
                            <h6><span><?php echo esc_html__('Login with', 'foodbakery'); ?></span></h6>
                            <ul>
                                <?php
                                if (is_user_logged_in()) {

                                    // remove id from all links
                                    if ($facebook_enabled == 'on') :
                                        echo apply_filters('social_login_login_facebook', '<li><a onclick="javascript:show_alert_msg(\'' . esc_html__("Please logout first then try to login again", "foodbakery") . '\')" href="javascript:void(0);" title="Facebook" data-original-title="Facebook" class=" facebook"><span class="social-mess-top fb-social-login" style="display:none">' . esc_html__('Please set API key', 'foodbakery') . '</span><i class="icon-facebook"></i>'.esc_html__('Sign In With Facebook', 'foodbakery') .'</a></li>');
                                    endif;
                                    if ($twitter_enabled == 'on') :
                                        echo apply_filters('social_login_login_twitter', '<li><a onclick="javascript:show_alert_msg(\'' . esc_html__("Please logout first then try to login again", "foodbakery") . '\')" href="javascript:void(0);" title="Twitter" data-original-title="twitter" class="twitter"><span class="social-mess-top tw-social-login" style="display:none">' . esc_html__('Please set API key', 'foodbakery') . '</span><i class="icon-twitter3"></i>'.esc_html__('Sign In With Twitter', 'foodbakery') .'</a></li>');
                                    endif;
                                    if ($google_enabled == 'on') :
                                        echo apply_filters('social_login_login_google', '<li><a onclick="javascript:show_alert_msg(\'' . esc_html__("Please logout first then try to login again", "foodbakery") . '\')" href="javascript:void(0);" rel="nofollow" title="google" data-original-title="google+" class="gplus"><span class="social-mess-top gplus-social-login" style="display:none">' . esc_html__('Please set API key', 'foodbakery') . '</span><i class="icon-google2"></i>'.esc_html__('Sign In With Google', 'foodbakery') .'</a></li>');
                                    endif;
                                } else {

                                    // remove id from all links
                                    if ($facebook_enabled == 'on') :
                                        echo apply_filters('social_login_login_facebook', '<li><a href="javascript:void(0);" title="Facebook" data-original-title="Facebook" class="social_login_login_facebook facebook"><span class="social-mess-top fb-social-login" style="display:none">' . esc_html__('Please set API key', 'foodbakery') . '</span><i class="icon-facebook"></i>'.esc_html__('Sign In With Facebook', 'foodbakery') .'</a></li>');
                                    endif;
                                    if ($twitter_enabled == 'on') :
                                        echo apply_filters('social_login_login_twitter', '<li><a href="javascript:void(0);" title="Twitter" data-original-title="twitter" class="social_login_login_twitter twitter"><span class="social-mess-top tw-social-login" style="display:none">' . esc_html__('Please set API key', 'foodbakery') . '</span><i class="icon-twitter3"></i>'.esc_html__('Sign In With Twitter', 'foodbakery') .'</a></li>');
                                    endif;
                                    if ($google_enabled == 'on') :
                                        echo apply_filters('social_login_login_google', '<li><a  href="javascript:void(0);" rel="nofollow" title="google-plus" data-original-title="google+" class="social_login_login_google gplus"><span class="social-mess-top gplus-social-login" style="display:none">' . esc_html__('Please set API key', 'foodbakery') . '</span><i class="icon-google2"></i>'.esc_html__('Sign In With Google', 'foodbakery') .'</a></li>');
                                    endif;
                                }
                                $social_login_provider = isset($_COOKIE['social_login_current_provider']) ? $_COOKIE['social_login_current_provider'] : '';
                                do_action('social_login_auth');
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>

                <?php
            endif;
        }
    }

}
/*
 *
 * End Function  how to login from social site;
 *
 */

add_action('login_form', 'foodbakery_social_login_form', 10);
add_action('social_form', 'foodbakery_social_login_form', 10);
add_action('after_signup_form', 'foodbakery_social_login_form', 10);
add_action('social_login_form', 'foodbakery_social_login_form', 10);

/*
 *
 * Start Function  how to user  recover his  password
 *
 */
if (!function_exists('foodbakery_recover_pass')) {

    function foodbakery_recover_pass() {
        global $wpdb, $foodbakery_plugin_options;

        $foodbakery_danger_html = '<div class="alert alert-danger"><p><i class="icon-warning4"></i>';

        $foodbakery_success_html = '<div class="alert alert-success"><p><i class="icon-checkmark6"></i>';

        $foodbakery_msg_html = '</p></div>';

        $foodbakery_msg = '';
        $json = array();
        // check if we're in reset form
        if (isset($_POST['action']) && 'foodbakery_recover_pass' == $_POST['action']) {
            $email = esc_sql(trim($_POST['user_input']));
            if (empty($email)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("Enter e-mail address..", "foodbakery");
                echo json_encode($json);
                wp_die();
            } else if (!is_email($email)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("Invalid e-mail address.", "foodbakery");
                echo json_encode($json);
                wp_die();
            } else if (!email_exists($email)) {

                $json['type'] = "error";
                $json['msg'] = esc_html__("There is no user registered with that email address.", "foodbakery");
                echo json_encode($json);
                wp_die();
            } else {
                $random_password = wp_generate_password(12, false);
                $user = get_user_by('email', $email);
                $username = $user->user_login;
                $update_user = wp_set_password($random_password, $user->ID);

                $template_data = array(
                    'user' => $username,
                    'email' => $email,
                    'password' => $random_password,
                );

                do_action('foodbakery_reset_password_email', $template_data);
                if (class_exists('Foodbakery_reset_password_email_template') && isset(Foodbakery_reset_password_email_template::$is_email_sent1)) {

                    $json['type'] = "success";
                    $json['msg'] = esc_html__("Check your email address for you new password.", "foodbakery");
                    echo json_encode($json);
                    wp_die();
                } else {

                    $json['type'] = "error";
                    $json['msg'] = esc_html__("Oops something went wrong updating your account.", "foodbakery");
                    echo json_encode($json);
                    wp_die();
                }
            }
            //end else
        }
        // end if
        echo ($foodbakery_msg);

        die;
    }

    add_action('wp_ajax_foodbakery_recover_pass', 'foodbakery_recover_pass');
    add_action('wp_ajax_nopriv_foodbakery_recover_pass', 'foodbakery_recover_pass');
}

/*
 *
 * End Function  how to user  recover his  password
 *
 */
/*
 *
 * Start Function how to user recover his lost password
 *
 */

if (!function_exists('foodbakery_lost_pass')) {

    function foodbakery_lost_pass($atts, $content = "") {
        global $foodbakery_form_fields_frontend;
        $foodbakery_defaults = array(
            'foodbakery_type' => '',
        );
        extract(shortcode_atts($foodbakery_defaults, $atts));
        ob_start();
        $foodbakery_rand = rand(12345678, 98765432);
        if ($foodbakery_type == 'popup') {
            ?>
            <span class="foodbakery-dev-login-forget-txt" style="display: none;"><?php esc_html_e('Create Your Food Bakery Account', 'foodbakery') ?></span>
            <span class="foodbakery-dev-login-box-t-txt" style="display: none;"><?php esc_html_e('Login To Your Account', 'foodbakery') ?></span>
            <div id="cs-result-<?php echo absint($foodbakery_rand) ?>"></div>
            <div class="login-form-id-<?php echo absint($foodbakery_rand) ?>">

                <form class="user_form" id="wp_pass_lost_<?php echo absint($foodbakery_rand) ?>" method="post">
                    <div class="modal-body modal-body-loader">
                        <span class="alert-info"><?php esc_html_e("Enter your email address below and we'll send you an email with instructions on how to change your password", 'foodbakery') ?></span>
                        <div class="input-filed">
                            <i class="icon-email"></i>
                            <?php
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => '',
                                'cust_id' => "",
                                'cust_name' => "user_input",
                                'classes' => '',
                                'extra_atr' => 'placeholder="' . esc_html__('Enter email address...', 'foodbakery') . '"',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                            ?>
                        </div>
                        <div class="input-filed input-field-btn">
                            <div class="ajax-forgot-button input-button-loader">
                                <?php
                                $foodbakery_opt_array = array(
                                    'id' => '',
                                    'std' => esc_html__('Submit', 'foodbakery'),
                                    'cust_id' => "",
                                    'cust_name' => "submit",
                                    'classes' => 'reset_password cs-bgcolor',
                                    'cust_type' => 'submit',
                                );
                                $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                ?>
                            </div>
                        </div>
                        <div class="forget-password">
                            <a class="cs-login-switch forgot-switch triggered-click"><?php esc_html_e('Login Here', 'foodbakery') ?></a>
                        </div>
                    </div>

                </form>
            </div>
            <?php
        } else {
            ?>
            <div class="scetion-title">
                <h4><?php esc_html_e('Forgot Password', 'foodbakery') ?></h4>
            </div>
            <div class="status status-message" id="cs-result-<?php echo absint($foodbakery_rand) ?>"></div>
            <form class="user_form" id="wp_pass_lost_shortcode_<?php echo absint($foodbakery_rand) ?>" method="post">
                <div class="row">
                    <div class="modal-body">
                        <div class="input-filed">
                            <i class="icon-email"></i>
                            <?php
                            $foodbakery_opt_array = array(
                                'id' => '',
                                'std' => '',
                                'cust_id' => "",
                                'cust_name' => "user_input",
                                'classes' => '',
                                'extra_atr' => 'placeholder="' . esc_html__('Enter email address...', 'foodbakery') . '"',
                            );
                            $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <div class="shortcode-ajax-forgot-button input-button-loader">
                                    <?php
                                    $foodbakery_opt_array = array(
                                        'id' => '',
                                        'std' => esc_html__('Submit', 'foodbakery'),
                                        'cust_id' => "",
                                        'cust_name' => "submit",
                                        'classes' => 'reset_password user-submit backcolr cs-bgcolor acc-submit',
                                        'cust_type' => 'submit',
                                    );
                                    $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                    ?>
                                </div>
                            </div>
                            <div class="col-mlg-7 col-md-7 col-sm-12 col-xs-12 login-section">
                                <div class="login-here-seaction">
                                    <a class="login-link-page triggered-click" href="javascript:void(0)"><?php esc_html_e('Login Here', 'foodbakery') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php
        }
        ?>
        <script type="text/javascript">
            var $ = jQuery;
            $("#wp_pass_lost_<?php echo absint($foodbakery_rand) ?>").submit(function () {
                var thisObj = jQuery('.ajax-forgot-button');
                foodbakery_show_loader('.ajax-forgot-button', '', 'button_loader', thisObj);
                var input_data = $('#wp_pass_lost_<?php echo absint($foodbakery_rand) ?>').serialize() + '&action=foodbakery_recover_pass';
                var forget_pass = '.modal-body-loader';
                $.ajax({
                    type: "POST",
                    url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                    data: input_data,
                    dataType: 'json',
                    success: function (msg) {
                        // call response function div.
                        foodbakery_show_response(msg, forget_pass, thisObj);
                    }
                });
                return false;
            });
            $("#wp_pass_lost_shortcode_<?php echo absint($foodbakery_rand) ?>").submit(function () {
                var thisObj = jQuery('.shortcode-ajax-forgot-button');
                foodbakery_show_loader('.shortcode-ajax-forgot-button', '', 'button_loader', thisObj);
                var input_data = $('#wp_pass_lost_<?php echo absint($foodbakery_rand) ?>').serialize() + '&action=foodbakery_recover_pass';
                $.ajax({
                    type: "POST",
                    url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                    data: input_data,
                    dataType: 'json',
                    success: function (msg) {
                        // call response function div.
                        foodbakery_show_response(msg, '', thisObj);
                    }
                });
                return false;
            });
            $(document).on('click', '.cs-forgot-switch', function () {
                var _this_title = $('.foodbakery-dev-login-forget-txt').html();
                var _this_append = $('.foodbakery-dev-login-main-title');

                _this_append.html(_this_title);

                $('.cs-login-pbox').hide();
                $('.cs-forgot-pbox').show();
            });
            $(document).on('click', '.cs-login-switch', function () {
                var _this_title = $('.foodbakery-dev-login-box-t-txt').html();
                var _this_append = $('.foodbakery-dev-login-main-title');

                _this_append.html(_this_title);

                $('.cs-forgot-pbox').hide();
                $('.cs-login-pbox').show();
            });
            $(document).on('click', '.user-registeration a', function () {
                $('#sign-in').modal('hide');
            });
            $(document).on('click', '.user-logging-in a', function () {
                $('#join-us').modal('hide');
            });
            $(document).on('click', '.foodbakery-subscribe-pkg', function () {
                var msg_show = $(this).data('msg');
                $("#sign-in .modal-body .foodbakery-dev-login-top-msg").html(msg_show);
                $("#sign-in .modal-body .foodbakery-dev-login-top-msg").show();
            });
            $(document).on('click', '.cs-popup-login-btn', function () {
                $("#sign-in .modal-body .foodbakery-dev-login-top-msg").html('');
                $("#sign-in .modal-body .foodbakery-dev-login-top-msg").hide();
            });
        </script>
        <?php
        $foodbakery_html = ob_get_clean();
        return do_shortcode($foodbakery_html);
    }

    add_shortcode('foodbakery_forgot_password', 'foodbakery_lost_pass');
}
/*
 *
 * End Function  how to user  recover his  lost password
 *
 */