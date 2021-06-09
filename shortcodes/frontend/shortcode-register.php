<?php
/**
 * File Type: Register Shortcode Frontend
 */
if (!class_exists('Foodbakery_Shortcode_Register_Frontend')) {

    class Foodbakery_Shortcode_Register_Frontend {

        /**
         * Constant variables
         */
        var $PREFIX = 'foodbakery_register';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_register_shortcode_callback'));
        }

        /*
         * Shortcode View on Frontend
         */

        public function foodbakery_register_shortcode_callback($atts, $content = "") {
            global $wpdb, $foodbakery_plugin_options, $foodbakery_form_fields_frontend, $foodbakery_form_fields_frontend, $foodbakery_html_fields;

            function_exists('foodbakery_socialconnect_scripts') ? foodbakery_socialconnect_scripts() :  ''; // social login script

            $defaults = array('column_size' => '1/1', 'publisher_register_element_title' => '', 'register_title' => '', 'register_type' => 'buyer', 'reg_type' => '', 'register_text' => '', 'register_role' => 'contributor', 'foodbakery_register_class' => '', 'foodbakery_register_animation' => '');
            extract(shortcode_atts($defaults, $atts));
            $column_size = isset($column_size) ? $column_size : '';

            $user_disable_text = __('User Registration is disabled', 'foodbakery');
            $foodbakery_sitekey = isset($foodbakery_plugin_options['foodbakery_sitekey']) ? $foodbakery_plugin_options['foodbakery_sitekey'] : '';
            $foodbakery_secretkey = isset($foodbakery_plugin_options['foodbakery_secretkey']) ? $foodbakery_plugin_options['foodbakery_secretkey'] : '';

            $foodbakery_captcha_switch = isset($foodbakery_plugin_options['foodbakery_captcha_switch']) ? $foodbakery_plugin_options['foodbakery_captcha_switch'] : '';
            ob_start();
            if ($foodbakery_sitekey <> '' and $foodbakery_secretkey <> '' and ! is_user_logged_in()) {
                foodbakery_google_recaptcha_scripts();
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        var recaptcha1;
                        var recaptcha2;
                        var recaptcha3;
                        var recaptcha4;
                        var foodbakery_multicap = function () {
                            //Render the recaptcha1 on the element with ID "recaptcha1"
                            recaptcha1 = grecaptcha.render('recaptcha1', {
                                'sitekey': '<?php echo ($foodbakery_sitekey); ?>', //Replace this with your Site key
                                'theme': 'light'
                            });
                            //Render the recaptcha2 on the element with ID "recaptcha2"
                            recaptcha2 = grecaptcha.render('recaptcha2', {
                                'sitekey': '<?php echo ($foodbakery_sitekey); ?>', //Replace this with your Site key
                                'theme': 'light'
                            });
                            recaptcha3 = grecaptcha.render('recaptcha3', {
                                'sitekey': '<?php echo ($foodbakery_sitekey); ?>', //Replace this with your Site key
                                'theme': 'light'
                            });
                            //Render the recaptcha2 on the element with ID "recaptcha2"
                            recaptcha4 = grecaptcha.render('recaptcha4', {
                                'sitekey': '<?php echo ($foodbakery_sitekey); ?>', //Replace this with your Site key
                                'theme': 'light'
                            });
                        };
                    });

                </script>
                <?php
            }

            // 
            $output = '';
            $title = isset($atts['title']) ? $atts['title'] : '';
            $registraion_div_rand_id = rand(5, 99999);
            $rand_id = rand(5, 99999);
            $rand_value = rand(0, 9999999);
            $role = $register_role;
            ?>
            <div class="row">
                <?php
                $page_element_size = isset($atts['foodbakery_register_element_size']) ? $atts['foodbakery_register_element_size'] : 100;
                ?>
                <div class="<?php echo foodbakery_var_page_builder_element_sizes($page_element_size); ?> ">
                    <?php if ($title != '') : ?>
                        <div class="element-title">
                            <h2><?php echo $title; ?></h2>
                        </div>
                    <?php endif; ?>
                    <?php
                    $temp_class = '';
                    $foodbakery_restaurant_id = foodbakery_get_input('trans_id', 0);
                    $get_added_menus = '';
                    if ($foodbakery_restaurant_id > 0 && isset($_COOKIE['add_menu_items_temp'])) {
                        $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                        if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                            $temp_class = 'signup-with-orders';
                        }
                    }
                    ?>
                    <div class="signup-form <?php echo $temp_class; ?>">
                        <?php if (is_user_logged_in()) : ?>
                            <div class="alert alert-warning">
                                <?php echo __('You have already logged in, Please logout to try again.', 'foodbakery'); ?>
                                <a data-dismiss="alert" class="close" href="javascript:void(0);">&times;</a>
                            </div>
                        <?php endif; ?>

                        <?php if (is_user_logged_in()) : ?>
                            <script>
                                jQuery("body").on("keypress", "input#user_login<?php echo absint($rand_id); ?>', input#user_pass<?php echo absint($rand_id); ?>", function (e) {
                                    if (e.which == "13") {
                                        show_alert_msg("<?php echo __("Please logout first then try to login again", "foodbakery"); ?>");
                                        return false;
                                    }
                                });
                            </script>
                        <?php else : ?>
                            <script>
                                jQuery("body").on("keypress", "input#user_login_<?php echo absint($rand_id); ?>, input#user_pass<?php echo absint($rand_id); ?>", function (e) {
                                    if (e.which == "13") {
                                        foodbakery_user_authentication("<?php echo esc_url(admin_url("admin-ajax.php")); ?>", "<?php echo absint($rand_id); ?>", '.shortcode-ajax-login-button');
                                        return false;
                                    }
                                });
                            </script>
                        <?php endif; ?>
                        <div class="input-info login-box login-from triggered-box login-form-id-<?php echo $rand_id; ?>">
                            <div class="scetion-title">
                                <h2><?php echo __('User Login', 'foodbakery'); ?></h2>
                            </div>
                            <form method="post" class="wp-user-form webkit" id="ControlForm_<?php echo $rand_id; ?>">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <div class="field-holder">
                                            <?php
                                            $foodbakery_opt_array = array(
                                                'id' => '',
                                                'std' => __('Username', 'foodbakery'),
                                                'cust_id' => 'user_login_' . $rand_id,
                                                'cust_name' => 'user_login',
                                                'classes' => '',
                                                'extra_atr' => ' size="20" tabindex="11" onfocus="if(this.value ==\'Username\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'Username\'; }"',
                                                'return' => true,
                                            );
                                            echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <div class="field-holder">
                                            <?php
                                            $foodbakery_opt_array = array(
                                                'id' => '',
                                                'std' => __('Password', 'foodbakery'),
                                                'cust_id' => 'user_pass' . $rand_id,
                                                'cust_name' => 'user_pass',
                                                'cust_type' => 'password',
                                                'classes' => '',
                                                'extra_atr' => ' size="20" tabindex="12" onfocus="if(this.value ==\'password\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'password\'; }"',
                                                'return' => true,
                                            );
                                            echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <div class="row">
                                            <?php if (is_user_logged_in()) : ?>
                                                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                    <?php
                                                    $foodbakery_opt_array = array(
                                                        'id' => '',
                                                        'std' => __('Log in', 'foodbakery'),
                                                        'cust_id' => 'user-submit',
                                                        'cust_name' => 'user-submit',
                                                        'cust_type' => 'button',
                                                        'extra_atr' => ' onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to login again", "foodbakery") . '\')"',
                                                        'classes' => 'user-submit backcolr cs-bgcolor acc-submit',
                                                        'return' => true,
                                                    );
                                                    echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                    <div class="shortcode-ajax-login-button input-button-loader">
                                                        <?php
                                                        $foodbakery_opt_array = array(
                                                            'id' => '',
                                                            'std' => __('Log in', 'foodbakery'),
                                                            'cust_id' => 'user-submit',
                                                            'cust_name' => 'user-submit',
                                                            'cust_type' => 'button',
                                                            'extra_atr' => ' onclick="javascript:foodbakery_user_authentication(\'' . admin_url("admin-ajax.php") . '\', \'' . $rand_id . '\', \'.shortcode-ajax-login-button\')"',
                                                            'classes' => 'cs-bgcolor user-submit backcolr  acc-submit',
                                                            'return' => true,
                                                        );
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                                        $foodbakery_opt_array = array(
                                                            'std' => $actual_link,
                                                            'id' => 'redirect_to',
                                                            'cust_name' => 'redirect_to',
                                                            'cust_type' => 'hidden',
                                                            'return' => true,
                                                        );
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                        if (isset($_REQUEST['trans_id'])) {

                                                            $foodbakery_opt_array = array(
                                                                'std' => 'checkout_login_yes',
                                                                'id' => 'checkout_login',
                                                                'cust_name' => 'checkout_login',
                                                                'cust_type' => 'hidden',
                                                                'return' => true,
                                                            );
                                                            echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                            $restaurant_id = foodbakery_get_input('_rid', 0);
                                                             echo '<input type="hidden" name="menu_id" value="' . $_REQUEST['menu_id'] . '">';
															 echo '<input type="hidden" name="trans_id" value="' . $_REQUEST['trans_id'] . '">';
                                                        }
                                                        $foodbakery_opt_array = array(
                                                            'std' => '1',
                                                            'id' => 'user_cookie',
                                                            'cust_name' => 'user-cookie',
                                                            'cust_type' => 'hidden',
                                                            'return' => true,
                                                        );
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                                                        $foodbakery_opt_array = array(
                                                            'id' => '',
                                                            'std' => 'ajax_login',
                                                            'cust_name' => 'action',
                                                            'cust_type' => 'hidden',
                                                            'return' => true,
                                                        );
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                                                        $foodbakery_opt_array = array(
                                                            'std' => __('login', 'foodbakery'),
                                                            'id' => 'login',
                                                            'cust_name' => 'login',
                                                            'cust_type' => 'hidden',
                                                            'return' => true,
                                                        );
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                        ?>
                                                    </div>
                                                    <!--<span class="status status-message" style="display:none"></span>-->
                                                    <a class="user-forgot-password-page triggered-click" href="javascript:void(0);"><?php echo __(' Forgot Password?', 'foodbakery'); ?></a>
                                                </div>
                                            <?php endif; ?>
                                            <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 ">
                                                <div class="login-section">
                                                    <i class="icon-user-add"></i><?php echo __('New Here? ', 'foodbakery'); ?>
                                                    <a class="register-link-page triggered-click" href="#"><?php echo __('Signup', 'foodbakery'); ?></a>
                                                </div>
                                            </div>
                                            <div class="status-message">
                                                <p class="status status-message" style="display:none"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="form-bg">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                <?php
                                                // Social login switche options.
                                                $twitter_login = isset($foodbakery_plugin_options['foodbakery_twitter_api_switch']) ? $foodbakery_plugin_options['foodbakery_twitter_api_switch'] : '';
                                                $facebook_login = isset($foodbakery_plugin_options['foodbakery_facebook_login_switch']) ? $foodbakery_plugin_options['foodbakery_facebook_login_switch'] : '';
                                                $linkedin_login = isset($foodbakery_plugin_options['foodbakery_linkedin_login_switch']) ? $foodbakery_plugin_options['foodbakery_linkedin_login_switch'] : '';
                                                $google_login = isset($foodbakery_plugin_options['foodbakery_google_login_switch']) ? $foodbakery_plugin_options['foodbakery_google_login_switch'] : '';
                                                if ($twitter_login == 'on' || $facebook_login == 'on' || $linkedin_login == 'on' || $google_login == 'on') {
                                                    ob_start();
                                                    do_action('login_form');
                                                    echo ob_get_clean();
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="input-info forgot-box triggered-box login-from login-form-id-<?php echo $rand_value; ?>">
                            <?php
                            echo do_shortcode('[foodbakery_forgot_password]');
                            ?>
                        </div>
                        <div class="tab-content signup-box activate triggered-box tab-content-page">
                            <div class="scetion-title">
                                <h2><?php echo __('Sign Up', 'foodbakery'); ?></h2>
                            </div>
                            <?php
                            $isRegistrationOn = get_option('users_can_register');
                            if ($isRegistrationOn) {
                                $tab_pan_class = ' class="tab-pane active"';
                                if (isset($reg_type) && $reg_type == 'content') {
                                    $tab_pan_class = '';
                                }
                                // registration page element
                                ?>
                                <div id="publisher<?php echo $registraion_div_rand_id; ?>" role="tabpanel" <?php echo $tab_pan_class; ?>>
                                    <div class="input-info">
                                        <div class="row">
                                            <script>
                                                jQuery("body").on("keypress", "input#user_login_<?php echo absint($rand_value); ?>, input#foodbakery_user_email<?php echo absint($rand_value); ?>, input#foodbakery_organization_name<?php echo absint($rand_value); ?>, input#foodbakery_publisher_specialisms<?php echo absint($rand_value); ?>, input#foodbakery_phone_no<?php echo absint($rand_value); ?>", function (e) {
                                                    if (e.which == "13") {
                                                        foodbakery_registration_validation("<?php echo esc_url(admin_url("admin-ajax.php")); ?>", "<?php echo absint($rand_value); ?>", '.shortcode-ajax-signup-button');
                                                        return false;
                                                    }
                                                });
                                            </script>
                                            <?php
                                            $key = foodbakery_get_input('key', NULL, 'STRING');
                                            if ($key != NULL) {
                                                $key_data = get_option($key);
                                            }
                                            ?>
                                            <form method="post" class="wp-user-form " id="wp_signup_form_<?php echo $rand_value; ?>" enctype="multipart/form-data">
                                                <?php if (isset($_GET['key']) && $_GET['key'] != '') : ?>
                                                    <input type="hidden" name="foodbakery_profile_type<?php echo $rand_value; ?>" value="restaurant">
                                                <?php elseif (isset($register_type) && $register_type == 'buyer') : ?>
                                                    <input type="hidden" name="foodbakery_profile_type<?php echo $rand_value; ?>" value="buyer">
                                                <?php elseif (isset($register_type) && $register_type == 'restaurant') : ?>
                                                    <input type="hidden" name="foodbakery_profile_type<?php echo $rand_value; ?>" value="restaurant">
                                                <?php else : ?>
                                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                        <div class="input-filed">
                                                            <?php
                                                            echo $foodbakery_html_fields->foodbakery_radio_field(
                                                                    array(
                                                                        'description' => '<label for="publisher_profile_type_individual">' . foodbakery_plugin_text_srt('foodbakery_publisher_profile_individual') . '</label>',
                                                                        'echo' => false,
                                                                        'field_params' => array(
                                                                            'std' => 'buyer',
                                                                            'cust_id' => 'publisher_profile_type_individual',
                                                                            'cust_name' => 'foodbakery_profile_type' . $rand_value . '',
                                                                            'extra_atr' => ' class="foodbakery_profile_type" checked',
                                                                            'return' => true
                                                                        ),
                                                                    )
                                                            );

                                                            echo $foodbakery_html_fields->foodbakery_radio_field(
                                                                    array(
                                                                        'description' => ' <label for="publisher_profile_type_company">' . foodbakery_plugin_text_srt('foodbakery_publisher_profile_company') . '</label>',
                                                                        'echo' => false,
                                                                        'field_params' => array(
                                                                            'std' => 'company',
                                                                            'cust_id' => 'publisher_profile_type_company',
                                                                            'cust_name' => 'foodbakery_profile_type' . $rand_value . '',
                                                                            'extra_atr' => 'class="foodbakery_profile_type"',
                                                                            'return' => true
                                                                        ),
                                                                    )
                                                            );
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($key == NULL) : ?>
                                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 foodbakery-company-name" style="display:none;">
                                                        <div class="field-holder">
                                                            <?php
                                                            echo $foodbakery_form_fields_frontend->foodbakery_form_text_render(
                                                                    array('name' => __('Company Name', 'foodbakery'),
                                                                        'id' => 'company_name' . $rand_value . '',
                                                                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                                                                        'std' => '',
                                                                        'description' => '',
                                                                        'extra_atr' => ' placeholder="' . __('Company Name', 'foodbakery') . '"',
                                                                        'return' => true,
                                                                        'hint' => ''
                                                                    )
                                                            );
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                    <div class="field-holder">
                                                        <?php
                                                        $foodbakery_opt_array = array(
                                                            'id' => '',
                                                            'std' => '',
                                                            'cust_id' => 'user_login_' . $rand_value,
                                                            'cust_name' => 'user_login' . $rand_value,
                                                            'extra_atr' => ' size="20" tabindex="101" placeholder="' . __('Username', 'foodbakery') . '"',
                                                            'classes' => '',
                                                            'return' => true,
                                                        );
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                    <div class="field-holder">
                                                        <?php
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render(
                                                                array('name' => __('First Name', 'foodbakery'),
                                                                    'id' => 'first_name' . $rand_value,
                                                                    'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                                                                    'extra_atr' => ' placeholder="' . __('First Name', 'foodbakery') . '"',
                                                                    'std' => '',
                                                                    'return' => true,
                                                                )
                                                        );
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                    <div class="field-holder">
                                                        <?php
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render(
                                                                array('name' => __('Last Name', 'foodbakery'),
                                                                    'id' => 'last_name' . $rand_value,
                                                                    'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                                                                    'extra_atr' => ' placeholder="' . __('Last Name', 'foodbakery') . '"',
                                                                    'std' => '',
                                                                    'return' => true,
                                                                )
                                                        );
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                    <div class="field-holder">
                                                        <?php
                                                        $readonly = ( isset($key_data['email']) ) ? 'readonly' : '';
                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render(
                                                                array('name' => __('Email', 'foodbakery'),
                                                                    'id' => 'user_email' . $rand_value . '',
                                                                    'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                                                                    'extra_atr' => ' size="20" tabindex="101" placeholder="' . __('Email', 'foodbakery') . '" ' . $readonly . ' ',
                                                                    'std' => ( isset($key_data['email']) ) ? $key_data['email'] : '',
                                                                    'description' => '',
                                                                    'cust_type' => 'email',
                                                                    'return' => true,
                                                                    'hint' => ''
                                                                )
                                                        );
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                                echo $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                                                        array('name' => __('User Type', 'foodbakery'),
                                                            'id' => 'user_role_type' . $rand_value . '',
                                                            'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                                                            'std' => 'publisher',
                                                            'description' => '',
                                                            'return' => true,
                                                            'hint' => ''
                                                        )
                                                );

                                                $foodbakery_rand_value = rand(54654, 99999965);
                                                if ($foodbakery_captcha_switch == 'on' && (!is_user_logged_in())) {
                                                    if (class_exists('Foodbakery_Captcha')) {
                                                        global $Foodbakery_Captcha;
                                                        ?>
                                                        <div class="col-md-12 recaptcha-reload" id="recaptcha1_div">
                                                            <?php
                                                            echo $Foodbakery_Captcha->foodbakery_generate_captcha_form_callback('recaptcha1', 'true');
                                                            ?>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                    <?php
                                                    echo wp_foodbakery::get_terms_and_conditions_field('', 'terms-' . $rand_value);
                                                    ?>
                                                </div>
                                                <div class="upload-file">
                                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                        <div class="row">
                                                            <?php
                                                            ob_start();
                                                            do_action('register_form');
                                                            echo ob_get_clean();
                                                            ?>
                                                            <?php if (is_user_logged_in()) { ?>
                                                                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                                    <?php
                                                                    $foodbakery_opt_array = array(
                                                                        'id' => '',
                                                                        'std' => __('Create Account', 'foodbakery'),
                                                                        'cust_id' => 'submitbtn' . $rand_value,
                                                                        'cust_name' => 'user-submit',
                                                                        'cust_type' => 'button',
                                                                        'classes' => 'user-submit cs-bgcolor acc-submit',
                                                                        'extra_atr' => ' tabindex="103" onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to registration again", "foodbakery") . '\')"',
                                                                        'return' => true,
                                                                    );
                                                                    echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                                    ?>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                                    <div class="shortcode-ajax-signup-button input-button-loader">
                                                                        <?php
                                                                        $foodbakery_opt_array = array(
                                                                            'id' => '',
                                                                            'std' => __('Create Account', 'foodbakery'),
                                                                            'cust_id' => 'submitbtn' . $rand_value,
                                                                            'cust_name' => 'user-submit',
                                                                            'cust_type' => 'button',
                                                                            'classes' => 'cs-bgcolor user-submit acc-submit',
                                                                            'extra_atr' => ' tabindex="103" onclick="javascript:foodbakery_registration_validation(\'' . admin_url("admin-ajax.php") . '\', \'' . $rand_value . '\', \'.shortcode-ajax-signup-button\')"',
                                                                            'return' => true,
                                                                        );
                                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                                                                        $foodbakery_opt_array = array(
                                                                            'id' => '',
                                                                            'std' => $role,
                                                                            'cust_id' => 'register-role',
                                                                            'cust_name' => 'role',
                                                                            'cust_type' => 'hidden',
                                                                            'return' => true,
                                                                        );
                                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
																		 if (isset($_REQUEST['trans_id'])) {

                                                            $foodbakery_opt_array = array(
                                                                'std' => 'checkout_login_yes',
                                                                'id' => 'checkout_login',
                                                                'cust_name' => 'checkout_login',
                                                                'cust_type' => 'hidden',
                                                                'return' => true,
                                                            );
                                                            echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                            echo '<input type="hidden" name="menu_id" value="' . $_REQUEST['menu_id'] . '">';
															 echo '<input type="hidden" name="trans_id" value="' . $_REQUEST['trans_id'] . '">';
                                                        }
                                                                        $foodbakery_opt_array = array(
                                                                            'id' => '',
                                                                            'std' => $key,
                                                                            'cust_id' => 'key',
                                                                            'cust_name' => 'key',
                                                                            'cust_type' => 'hidden',
                                                                            'return' => true,
                                                                        );
                                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                                        $foodbakery_opt_array = array(
                                                                            'id' => '',
                                                                            'std' => 'foodbakery_registration_validation',
                                                                            'cust_name' => 'action',
                                                                            'cust_type' => 'hidden',
                                                                            'return' => true,
                                                                        );
                                                                        echo $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                                <div class="login-section">
                                                                    <i class="icon-user-add"></i><?php echo __(' Already have an account?', 'foodbakery'); ?> 
                                                                    <a href="#" class="login-link-page triggered-click"><?php echo __('Login here', 'foodbakery'); ?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="result_<?php echo $rand_value; ?>" class="status-message"><p class="status status-messages"></p></div>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="register_content"><?php do_shortcode($content . $register_text); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                /// Social login switche options
                                $twitter_login = isset($foodbakery_plugin_options['foodbakery_twitter_api_switch']) ? $foodbakery_plugin_options['foodbakery_twitter_api_switch'] : '';
                                $facebook_login = isset($foodbakery_plugin_options['foodbakery_facebook_login_switch']) ? $foodbakery_plugin_options['foodbakery_facebook_login_switch'] : '';
                                $linkedin_login = isset($foodbakery_plugin_options['foodbakery_linkedin_login_switch']) ? $foodbakery_plugin_options['foodbakery_linkedin_login_switch'] : '';
                                $google_login = isset($foodbakery_plugin_options['foodbakery_google_login_switch']) ? $foodbakery_plugin_options['foodbakery_google_login_switch'] : '';

                                if ($twitter_login == 'on' || $facebook_login == 'on' || $linkedin_login == 'on' || $google_login == 'on') {
                                    ?>
                                    <div class="row">
                                        <div class="form-bg">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                <?php
                                                ob_start();
                                                if (class_exists('wp_foodbakery')) {
                                                    echo do_action('login_form');
                                                }
                                                echo ob_get_clean();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="register_content"><?php do_shortcode($content . $register_text); ?></div>
                                <?php
                            } else {
                                ?>
                                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 register-page">
                                    <div class="cs-user-register">
                                        <div class="element-title">
                                            <h2><?php echo __('Register', 'foodbakery'); ?></h2>
                                        </div>
                                        <p><?php echo $user_disable_text; ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

    }

    global $foodbakery_shortcode_register_frontend;
    $foodbakery_shortcode_register_frontend = new Foodbakery_Shortcode_Register_Frontend();
}
