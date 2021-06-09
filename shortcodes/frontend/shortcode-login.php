<?php
/**
 * File Type: Login Shortcode Frontend
 */
if (!class_exists('Foodbakery_Shortcode_Login_Frontend')) {

    class Foodbakery_Shortcode_Login_Frontend {

        /**
         * Constant variables
         */
        var $PREFIX = 'foodbakery_login';
        var $LOGIN_OUTPUT = '';
        var $REGISTER_OUTPUT = '';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_login_shortcode_callback'));
            add_action($this->PREFIX, array($this, 'foodbakery_login_callback'));
        }

        function wp_nav_menu_items_callback($items, $args) {
            global $post, $foodbakery_plugin_options, $foodbakery_theme_options;

            $foodbakery_html = '';
            $foodbakery_user_dashboard_switchs = '';
            if (isset($foodbakery_plugin_options) && $foodbakery_plugin_options != '') {
                if (isset($foodbakery_plugin_options['foodbakery_user_dashboard_switchs'])) {
                    $foodbakery_user_dashboard_switchs = $foodbakery_plugin_options['foodbakery_user_dashboard_switchs'];
                }
            }
            if ($args->theme_location == 'primary') {
                ob_start();
                ?>
                <ul class="login-option">
                    <?php do_action('foodbakery_login'); ?>
                </ul>
                <?php
                $foodbakery_html .= ob_get_clean();
                $items .= $foodbakery_html;
            }
            return $items;
        }

        /*
         * Login hook calling shortcode
         */

        public function foodbakery_login_callback() {
            echo do_shortcode('[' . $this->PREFIX . ']');
        }

        /*
         * Shortcode View on Frontend
         */

        public function foodbakery_login_shortcode_callback($atts, $content = "") {
            global $wpdb, $foodbakery_plugin_options, $foodbakery_form_fields_frontend, $foodbakery_form_fields_frontend, $foodbakery_html_fields;

            foodbakery_socialconnect_scripts(); // social login script
            $defaults = array('column_size' => '1/1', 'title' => '', 'register_text' => '', 'register_role' => 'contributor', 'foodbakery_type' => '', 'foodbakery_login_txt' => '', 'login_btn_class' => '');
            extract(shortcode_atts($defaults, $atts));

            $user_disable_text = esc_html__('User Registration is disabled', 'foodbakery');
            $foodbakery_sitekey = isset($foodbakery_plugin_options['foodbakery_sitekey']) ? $foodbakery_plugin_options['foodbakery_sitekey'] : '';
            $foodbakery_secretkey = isset($foodbakery_plugin_options['foodbakery_secretkey']) ? $foodbakery_plugin_options['foodbakery_secretkey'] : '';
            $foodbakery_captcha_switch = isset($foodbakery_plugin_options['foodbakery_captcha_switch']) ? $foodbakery_plugin_options['foodbakery_captcha_switch'] : '';

            $foodbakery_demo_user_login_switch = isset($foodbakery_plugin_options['foodbakery_demo_user_login_switch']) ? $foodbakery_plugin_options['foodbakery_demo_user_login_switch'] : '';
            if ($foodbakery_demo_user_login_switch == 'on') {
                $foodbakery_foodbakery_demo_user_publisher = isset($foodbakery_plugin_options['foodbakery_job_demo_user_publisher']) ? $foodbakery_plugin_options['foodbakery_job_demo_user_publisher'] : '';
                $foodbakery_demo_user_buyer = isset($foodbakery_plugin_options['foodbakery_demo_user_buyer']) ? $foodbakery_plugin_options['foodbakery_demo_user_buyer'] : '';
            }
            $rand_id = rand(13243, 99999);

            if ($foodbakery_sitekey <> '' and $foodbakery_secretkey <> '' and ! is_user_logged_in()) {
                foodbakery_google_recaptcha_scripts();
                ?>
                <script>


                    jQuery(document).ready(function ($) {
                        var recaptcha6;
                        var recaptcha5;
                        var foodbakery_multicap = function () {
                            //Render the recaptcha1 on the element with ID "recaptcha1"
                            recaptcha6 = grecaptcha.render('recaptcha6', {
                                'sitekey': '<?php echo ($foodbakery_sitekey); ?>', //Replace this with your Site key
                                'theme': 'light'
                            });
                            //Render the recaptcha2 on the element with ID "recaptcha2"
                            recaptcha5 = grecaptcha.render('recaptcha5', {
                                'sitekey': '<?php echo ($foodbakery_sitekey); ?>', //Replace this with your Site key
                                'theme': 'light'
                            });
                        };

                    });


                </script>
                <?php
            }
			$output = "<span id='translator_widget'>".do_shortcode('[gtranslate]')."</span>";
            $output .= '';
            if (is_user_logged_in()) {
                $output .= $this->foodbakery_profiletop_menu();
            } else {
                $role = $register_role;
                $foodbakery_type = isset($foodbakery_type) ? $foodbakery_type : '';
                $foodbakery_login_class = 'login';

                $isRegistrationOn = get_option('users_can_register');
                $output .= '<a class="cs-color cs-popup-joinus-btn login-popup" data-target="#sign-in" data-toggle="modal" href="#user-register">' . esc_html__('Login / Register', 'foodbakery') . '</a>';

                ob_start();
                do_action('foodbakery_get_started');
                $output .= ob_get_clean();

                $login_btn_class_str = '';
                if ($login_btn_class != '') {
                    $login_btn_class_str = 'class="' . $login_btn_class . '"';
                }



                /*
                 * Signin Popup Rendering
                 */
                $output_html = '';

                $output_html .= '<div class="modal fade" id="sign-in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
						<div class="login-form">
						<div class="modal-content">';

                $output_html .= '<div class="tab-content">';


                // Signin Tab
                $output_html .= '<div id="user-login-tab" class="tab-pane fade in active">';

                $output_html .= '
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
					<h5 class="modal-title foodbakery-dev-login-main-title">' . esc_html__('Login To Your Account', 'foodbakery') . '</h5>
				</div>';
                $output_html .= '<div class="modal-body">';
                $output_html .= '<p class="foodbakery-dev-login-top-msg" style="display: none;"></p>';

                $output_html .='<div class="cs-login-pbox login-form-id-' . $rand_id . '">';
                $output_html .= '<div class="status status-message"></div>';

                ob_start();
                $isRegistrationOn = get_option('users_can_register');
                // Social login switch options

                if (is_user_logged_in()) {
                    $output_html .=
                            '<script>'
                            . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
						if (e.which == "13") {
							show_alert_msg("' . esc_html__("Please logout first then try to login again", "foodbakery") . '");
							return false;
						}
					});'
                            . '</script>';
                } else {
                    $output_html .= ''
                            . '<script>'
                            . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
							if (e.which == "13") {
								foodbakery_user_authentication("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_id) . '", \'.ajax-login-button\');
								return false;
							}
						});'
                            . '</script>';
                }

                $output_html .= '<form method="post" class="wp-user-form webkit" id="ControlForm_' . $rand_id . '">';
                if ($foodbakery_demo_user_login_switch == 'on' && ($foodbakery_foodbakery_demo_user_publisher != '' || $foodbakery_demo_user_buyer != '')) {

                    require_once( ABSPATH . 'wp-includes/class-phpass.php');
                    $wp_hasher = new PasswordHash(8, TRUE);

                    if ($foodbakery_foodbakery_demo_user_publisher != '' || $foodbakery_demo_user_buyer != '') {
                        $output_html .= '<div class="cs-demo-login">';
                        $output_html .= '<div class="cs-demo-login-lable text-color">' . esc_html__('Click to login with Demo User', 'foodbakery') . '</div>';
                        $output_html .= '<div class="clearfix"></div>';
                        $output_html .= '<ul class="login-switches">';
                    }

                    if ($foodbakery_foodbakery_demo_user_publisher != '') {

                        $demo_user_password = esc_html('demo123');
                        $foodbakery_foodbakery_demo_publisher_detail = get_user_by('id', $foodbakery_foodbakery_demo_user_publisher);
                        if (!(isset($foodbakery_foodbakery_demo_publisher_detail->user_pass) && $wp_hasher->CheckPassword($demo_user_password, $foodbakery_foodbakery_demo_publisher_detail->user_pass))) {
                            wp_set_password($demo_user_password, $foodbakery_foodbakery_demo_user_publisher);
                        }
                        $foodbakery_foodbakery_demo_publisher_detail_user = isset($foodbakery_foodbakery_demo_publisher_detail->user_login) ? $foodbakery_foodbakery_demo_publisher_detail->user_login : '';

                        $output_html .= '<li>';
                        $output_html .= '<a href="javascript:void(0)" class="btn-red demo-publisher-user" onclick="javascript:foodbakery_demo_user_login(\'' . $foodbakery_foodbakery_demo_publisher_detail_user . '\',\'.demo-publisher-user\')"><i class="icon-food"></i> ' . esc_html__('Restaurant', 'foodbakery') . '</a>';
                        $output_html .= '
				<script>
					function foodbakery_demo_user_login(user,thisObjClass){
						jQuery("#user_login' . $rand_id . '" ).val(user);
						jQuery("#user_pass' . $rand_id . '" ).val("' . $demo_user_password . '");
						foodbakery_user_authentication(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\', thisObjClass);
					}
				</script>';
                        $output_html .= '</li>';
                    }
                    if ($foodbakery_demo_user_buyer != '') {

                        $foodbakery_demo_user_buyer_detail = get_user_by('id', $foodbakery_demo_user_buyer);
                        $demo_user_password = esc_html('demo123');
                        if (!(isset($foodbakery_demo_user_buyer_detail->user_pass) && $wp_hasher->CheckPassword($demo_user_password, $foodbakery_demo_user_buyer_detail->user_pass))) {
                            wp_set_password($demo_user_password, $foodbakery_demo_user_buyer);
                        }
                        $foodbakery_demo_user_buyer_detail_user = isset($foodbakery_demo_user_buyer_detail->user_login) ? $foodbakery_demo_user_buyer_detail->user_login : '';

                        $output_html .= '<li>';
                        $output_html .= '<a href="javascript:void(0)" class="btn-red btn-green demo-buyer-user" onclick="javascript:foodbakery_demo_buyer_login(\'' . $foodbakery_demo_user_buyer_detail_user . '\',\'.demo-buyer-user\')"><i class="icon-user4"></i> ' . esc_html__('Buyer', 'foodbakery') . '</a>';
                        $output_html .= '
				<script>
					function foodbakery_demo_buyer_login(user,thisObjClass){
						jQuery("#user_login' . $rand_id . '" ).val(user);
						jQuery("#user_pass' . $rand_id . '" ).val("' . $demo_user_password . '");
						foodbakery_user_authentication(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\', thisObjClass);
					}
				</script>';
                        $output_html .= '</li>';
                    }
                    if ($foodbakery_foodbakery_demo_user_publisher != '' || $foodbakery_demo_user_buyer != '') {
                        $output_html .= '</ul>';
                        $output_html .= '</div>';
                    }
                }
                $isRegistrationOn = get_option('users_can_register');
                $output_html .= '<div class="input-filed"><i class="icon-user4"></i>';
                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => '',
                    'cust_id' => 'user_login' . $rand_id,
                    'cust_name' => 'user_login',
                    'classes' => '',
                    'extra_atr' => ' tabindex="11" placeholder="' . esc_html__('Username', 'foodbakery') . '"',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                $output_html .= '</div>';

                $output_html .= '<div class="input-filed"><i class="icon-unlock-alt"></i>';

                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => esc_html__('Password', 'foodbakery'),
                    'cust_id' => 'user_pass' . $rand_id,
                    'cust_name' => 'user_pass',
                    'cust_type' => 'password',
                    'classes' => '',
                    'extra_atr' => ' tabindex="12" size="20" onfocus="if(this.value ==\'' . esc_html__('Password', 'foodbakery') . '\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'' . esc_html__('Password', 'foodbakery') . '\'; }"',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                $output_html .='</div>';
                $output_html .='<div class="forget-password"><a class="cs-forgot-switch forgot-switch">' . esc_html__('Forgot Password?', 'foodbakery') . '</a></div>';
                if (is_user_logged_in()) {
                    $output_html .='<div class="input-filed">';
                    $foodbakery_opt_array = array(
                        'std' => esc_html__('Log in', 'foodbakery'),
                        'cust_name' => 'user-submit',
                        'cust_type' => 'button',
                        'classes' => 'cs-bgcolor',
                        'extra_atr' => ' onclick="javascript:show_alert_msg(\'' . esc_html__("Please logout first then try to login again", "foodbakery") . '\')"',
                        'return' => true,
                    );
                    $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                    $output_html .= '</div>';
                } else {
                    $output_html .='<div class="input-filed input-field-btn">';
                    $output_html .= '<div class="ajax-login-button input-button-loader">';
                    $foodbakery_opt_array = array(
                        'std' => esc_html__('Log in', 'foodbakery'),
                        'cust_name' => 'user-submit',
                        'cust_type' => 'button',
                        'classes' => 'cs-bgcolor',
                        'extra_atr' => ' onclick="javascript:foodbakery_user_authentication(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\', \'.ajax-login-button\')"',
                        'return' => true,
                    );
                    $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                    $output_html .= '</div>';

                    $foodbakery_opt_array = array(
                        'id' => '',
                        'std' => get_permalink(),
                        'cust_id' => 'redirect_to',
                        'cust_name' => 'redirect_to',
                        'cust_type' => 'hidden',
                        'return' => true,
                    );
                    $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                    $foodbakery_opt_array = array(
                        'id' => '',
                        'std' => '1',
                        'cust_id' => 'user-cookie',
                        'cust_name' => 'user-cookie',
                        'cust_type' => 'hidden',
                        'return' => true,
                    );
                    $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                    $foodbakery_opt_array = array(
                        'id' => '',
                        'std' => 'ajax_login',
                        'cust_name' => 'action',
                        'cust_type' => 'hidden',
                        'return' => true,
                    );
                    $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                    $foodbakery_opt_array = array(
                        'id' => '',
                        'std' => 'login',
                        'cust_id' => 'login',
                        'cust_name' => 'login',
                        'cust_type' => 'hidden',
                        'return' => true,
                    );
                    $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                    $output_html .= '
					</div>';
                }

                $output_html .='</form>';
                if ($isRegistrationOn) {
                    $output_html .= '<div class="signin-tab-link forget-password">';
                    $output_html .= esc_html__("New Here? ", 'foodbakery') . '<a href="javascript:void(0);" class="foodbakery-dev-login-box-btn forgot-switch">' . esc_html__('Signup', 'foodbakery') . '</a>';
                    $output_html .= '</div>';
                }

                $rand_idd = $rand_id;
                $twitter_login = isset($foodbakery_plugin_options['foodbakery_twitter_api_switch']) ? $foodbakery_plugin_options['foodbakery_twitter_api_switch'] : '';
                $facebook_login = isset($foodbakery_plugin_options['foodbakery_facebook_login_switch']) ? $foodbakery_plugin_options['foodbakery_facebook_login_switch'] : '';
                $google_login = isset($foodbakery_plugin_options['foodbakery_google_login_switch']) ? $foodbakery_plugin_options['foodbakery_google_login_switch'] : '';

                $output_html .= do_action('login_form');
                $output_html .= ob_get_clean();

                if ($isRegistrationOn && ($twitter_login == 'on' || $facebook_login == 'on' || $google_login == 'on')) {

                }


                $output_html .= '</div>';
                $output_html .= '</div>';
                $output_html .= '<div class="content-style-form cs-forgot-pbox content-style-form-2" style="display:none;">';
                ob_start();
                $output_html .= do_shortcode('[foodbakery_forgot_password foodbakery_type="popup"]');
                $output_html .= ob_get_clean();
                $output_html .= '</div>';


                $output_html .= '</div>';
                // End signin tabs
                // Signup Tab
                $output_html .= '<div id="user-register" class="tab-pane fade">';

                $output_html .= $this->foodbakery_registration_tab();

                $output_html .= '
                </div>';
                // End signup tabs
                //Forgot Password Tab
                $output_html .= '<div id="user-password" class="tab-pane fade">';


                $output_html .= '
                </div>';
                //End Password Tab


                $output_html .= '</div>
                   </div>';

                $output_html .='
                </div>';

                $output_html .='
                </div>';

                $output_html .='
                </div>';

                $output_html .='
				</div></div>';
                $data = get_transient('social_data');
                delete_transient('social_data');
                if ($data != false) {
                    ob_start();
                    ?>
                    <script type="text/javascript">
                        (function ($) {
                            $(function () {
                                var rand_id = window.rand_id_registration;
                                $("input[name='user_login" + rand_id + "']").val('<?php echo esc_html($data['user_login']); ?>');
                                $("input[name='foodbakery_display_name" + rand_id + "']").val('<?php echo esc_html($data['first_name']) . ' ' . esc_html($data['last_name']); ?>');
                                $(".status-message").addClass('text-danger').html('<?php echo esc_html__('Sorry! ' . ucfirst($data['social_login_provider']) . ' does not shared your email, please provide a valid email address.', 'foodbakery'); ?>');
                                $("#signin-role").after('<input type="hidden" name="social_meta_key" value="<?php echo esc_html($data['social_meta_key']); ?>">');
                                $("#signin-role").after('<input type="hidden" name="social_meta_value" value="<?php echo esc_html($data['social_meta_value']); ?>">');
                                $(".foodbakery-dev-login-box-btn").click();
                                $(".cs-popup-joinus-btn").click();
                            });
                        })(jQuery);
                    </script>

                    <?php
                    $output_html .= ob_get_clean();
                }
                $this->LOGIN_OUTPUT = $output_html;
                $this->foodbakery_popup_into_footer();
            }
            return $output;
        }

        public function foodbakery_registration_tab() {
            global $foodbakery_form_fields_frontend, $foodbakery_html_fields, $foodbakery_plugin_options;
            $foodbakery_captcha_switch = '';
            $foodbakery_captcha_switch = isset($foodbakery_plugin_options['foodbakery_captcha_switch']) ? $foodbakery_plugin_options['foodbakery_captcha_switch'] : '';
            $output_html = '';
            $role = '';
            $register_text = '';
            $user_disable_text = esc_html__('User Registration is disabled', 'foodbakery');
            $content = '';
            $output_html .= '<div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                                            <h5 id="myModalLabel" class="modal-title">' . esc_html__('Sign Up', 'foodbakery') . '</h5>
                                            </div>';
            $output_html .='<div class="modal-body">';
            $isRegistrationOn = get_option('users_can_register');
            $popup_register_rand_divids = rand(0, 999999);
            if ($isRegistrationOn) {

                $rand_ids = rand(0, 999999);

                // popup registration forms
                // popup publisher registration form
                $output_html .='<div id="publisher' . $popup_register_rand_divids . '" role="tabpanel" class="tab-pane active">';
                $output_html .= '<div id="result_' . $rand_ids . '" class="status-message"></div>';
                $output_html .='<script>'
                        . 'window.rand_id_registration = \'' . $rand_ids . '\';
						jQuery("body").on("keypress", "input#user_login_3' . absint($rand_ids) . ', input#foodbakery_user_email' . absint($rand_ids) . ', input#foodbakery_organization_name' . absint($rand_ids) . ', input#foodbakery_publisher_specialisms' . absint($rand_ids) . ', input#foodbakery_phone_no' . absint($rand_ids) . '", function (e) {
                                                                            if (e.which == "13") {
                                                                                    foodbakery_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_ids) . '",\'.ajax-signup-button\');
                                                                                    return false;
                                                                            }
                                                                            });'
                        . '</script>';
                $key = foodbakery_get_input('key', NULL, 'STRING');

                $output_html .='<form method="post" class="wp-user-form demo_test" id="wp_signup_form_' . $rand_ids . '" enctype="multipart/form-data">';

                $output_html .='<div class="input-filed">';



                $output_html .= '<input type="hidden" name="foodbakery_profile_type' . $rand_ids . '" value="buyer">';


                $output_html .='</div>';

                if ($key == NULL) {
                    $output_html .='<div class="input-filed foodbakery-company-name" style="display:none;"><i class="icon-v-card"></i>';
                    $output_html .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                            array('name' => esc_html__('Company Name', 'foodbakery'),
                                'id' => 'company_name' . $rand_ids,
                                'extra_atr' => ' placeholder="' . esc_html__('Company Name', 'foodbakery') . '"',
                                'std' => '',
                                'return' => true,
                            )
                    );
                    $output_html .= '</div>';
                }
                $output_html .='<div class="input-filed"><i class="icon-user4"></i>';

                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => '',
                    'cust_id' => 'foodbakery_first_name' . $rand_ids,
                    'cust_name' => 'foodbakery_first_name' . $rand_ids,
                    'extra_atr' => ' placeholder="' . esc_html__('First name', 'foodbakery') . '"',
                    'classes' => '',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                $output_html .='</div><div class="input-filed"><i class="icon-user4"></i>';
                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => '',
                    'cust_id' => 'foodbakery_last_name' . $rand_ids,
                    'cust_name' => 'foodbakery_last_name' . $rand_ids,
                    'extra_atr' => ' placeholder="' . esc_html__('Last name', 'foodbakery') . '"',
                    'classes' => '',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                $output_html .='</div><div class="input-filed"><i class="icon-user4"></i>';
                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => '',
                    'cust_id' => 'user_login_3' . $rand_ids,
                    'cust_name' => 'user_login' . $rand_ids,
                    'extra_atr' => ' placeholder="' . esc_html__('Username', 'foodbakery') . '"',
                    'classes' => '',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                $output_html .= '
                                </div>';
                if ($key == NULL) {
                    $output_html .='<div class="input-filed"><i class="icon-v-card"></i>';
                    $output_html .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                            array('name' => esc_html__('Display Name', 'foodbakery'),
                                'id' => 'display_name' . $rand_ids,
                                'extra_atr' => ' placeholder="' . esc_html__('Display Name', 'foodbakery') . '"',
                                'std' => '',
                                'return' => true,
                            )
                    );
                    $output_html .= '</div>';
                }
                $output_html .='<div class="input-filed"><i class="icon-email"></i>';
                $readonly = ( isset($key_data['email']) ) ? 'readonly' : '';
                $output_html .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                        array('name' => esc_html__('Email', 'foodbakery'),
                            'id' => 'user_email' . $rand_ids,
                            'extra_atr' => ' placeholder="' . esc_html__('Email', 'foodbakery') . '"' . $readonly . '',
                            'std' => ( isset($key_data['email']) ) ? $key_data['email'] : '',
                            'cust_type'=>'email',
                            'return' => true,
                        )
                );
                $output_html .= '</div>';
                $output_html .= '<span class="signup-alert"><b>' . esc_html__('Note :') . '</b> ' . esc_html__('Please enter your correct email and we will send you a password on that email.', 'foodbakery') . '</span>';
                $restaurant_add_counter = rand(123456789, 987654321);
		        $output_html .= wp_foodbakery::get_terms_and_conditions_field('', 'terms-' . $rand_ids);
                $output_html .=$foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                        array('name' => 'user role type',
                            'id' => 'user_role_type' . $rand_ids,
                            'classes' => 'input-holder',
                            'std' => 'publisher',
                            'description' => '',
                            'return' => true,
                            'hint' => '',
                            'icon' => 'icon-user9'
                        )
                );

                $output_html .='<div class="side-by-side select-icon clearfix">';
                $output_html .='<div class="select-holder">';

                $output_html .='</div>';
                $output_html .='</div>';


                if ($foodbakery_captcha_switch == 'on' && (!is_user_logged_in())) {
                    if (class_exists('Foodbakery_Captcha')) {
                        global $Foodbakery_Captcha;
                        $output_html .='<div class="recaptcha-reload" id="recaptcha6_div">';
                        $output_html .= $Foodbakery_Captcha->foodbakery_generate_captcha_form_callback('recaptcha6', 'true');
                        $output_html .='</div>';
                    }
                }
                $output_html .= '<div class="checks-holder">';
                ob_start();
                $output_html .= do_action('register_form');
                $output_html .= ob_get_clean();
                $foodbakery_rand_id = rand(122, 1545464897);

                $output_html .= '<div class="input-filed input-field-btn">';
                $output_html .= '<div class="ajax-signup-button input-button-loader">';
                $foodbakery_opt_array = array(
                    'std' => esc_html__('Sign Up', 'foodbakery'),
                    'cust_id' => 'submitbtn' . $foodbakery_rand_id,
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'classes' => 'user-submit cs-bgcolor acc-submit',
                    'extra_atr' => ' tabindex="103" onclick="javascript:foodbakery_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_ids . '\',\'.ajax-signup-button\')"',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                $output_html .= '</div>';

                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => $role,
                    'cust_id' => 'signin-role',
                    'cust_name' => 'role',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => 'foodbakery_registration_validation',
                    'cust_name' => 'action',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                if ($key != NULL) {
                    $foodbakery_opt_array = array(
                        'id' => '',
                        'std' => $key,
                        'cust_name' => 'key',
                        'cust_type' => 'hidden',
                        'return' => true,
                    );
                    $output_html .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                }

                $output_html .= '
                                        </div>';
                $output_html .= '</div>';

                $output_html .= '</form>
                                        <div class="register_content">' . do_shortcode($content . $register_text) . '</div>';
                $output_html .= '<div class="create- signin-tab-link ">';
                $output_html .= esc_html__('Already have an account? ', 'foodbakery') . '<a href="javascript:void(0);" class="foodbakery-dev-signup-box-btn">' . esc_html__('Login here', 'foodbakery') . '</a>';
                $output_html .= '</div>';

                ob_start();
                if (class_exists('wp_foodbakery')) {

                    $output_html .= do_action('login_form');
                }
                $output_html .= ob_get_clean();

                if ($key != NULL) {
                    $key_data = get_option($key);
                    $output_html .= '<script>jQuery(document).ready(function($){$("#join-us").modal("show")}); </script>';
                }


                $output_html .='</div>';
            } else {
                $output_html .='<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 register-page">
							<div class="cs-user-register">
								<div class="element-title">
									   <h2>' . esc_html__('Register', 'foodbakery') . '</h2>
							   </div>
							   <p>' . $user_disable_text . '</p>
							</div>
						</div>
						</div>';
                $output_html .='</div>';
            }
            $output_html .= '</div>';
            return $output_html;
        }

        public function foodbakery_registration_popup() {
            global $foodbakery_form_fields_frontend, $foodbakery_html_fields;
            $user_disable_text = esc_html__('User Registration is disabled', 'foodbakery');
            $output .= '<div class="modal fade" id="join-us' . $rand_id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                              <div class="modal-dialog" role="document">
                              <div class="login-form">
                                <div class="modal-content">

                                  <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                                    <h3 id="myModalLabel" class="modal-title">' . esc_html__('Sign Up', 'foodbakery') . '</h3>
                                    </div>';
            $output .= '<div class="modal-body">';
            $isRegistrationOn = get_option('users_can_register');
            $popup_register_rand_divids = rand(0, 999999);
            if ($isRegistrationOn) {

                $rand_ids = rand(0, 999999);

                // popup registration forms
                $output .='<div class="tab-content">';
                // popup publisher registration form
                $output .='<div id="publisher' . $popup_register_rand_divids . '" role="tabpanel" class="tab-pane active">';
                $output .= '<div id="result_' . $rand_ids . '" class="status-message"></div>';
                $output .='<script>'
                        . 'jQuery("body").on("keypress", "input#user_login_3' . absint($rand_ids) . ', input#foodbakery_user_email' . absint($rand_ids) . ', input#foodbakery_organization_name' . absint($rand_ids) . ', input#foodbakery_publisher_specialisms' . absint($rand_ids) . ', input#foodbakery_phone_no' . absint($rand_ids) . '", function (e) {
				if (e.which == "13") {
						foodbakery_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_ids) . '",\'.ajax-signup-button\');
						return false;
				}
				});'
                        . '</script>';
                $output .= '<div class="login-with">';
                ob_start();
                if (class_exists('wp_foodbakery')) {
                    $output .='<h3>' . esc_html__('OR', 'foodbakery') . '</h3>';
                    $output .= do_action('login_form');
                }
                $output .= ob_get_clean();
                $output .= '</div>';
                $output .='<form method="post" class="wp-user-form demo_test" id="wp_signup_form_' . $rand_ids . '" enctype="multipart/form-data">';
                $output .='<div class="input-filed">';
                $key = foodbakery_get_input('key', NULL, 'STRING');
                if ($key != NULL) {

                    $key_data = get_option($key);
                }
                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => '',
                    'cust_id' => 'user_login_3' . $rand_ids,
                    'cust_name' => 'user_login' . $rand_ids,
                    'extra_atr' => ' placeholder="' . esc_html__('Username', 'foodbakery') . '"',
                    'classes' => '',
                    'return' => true,
                );
                $output .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                $output .= '
                        </div>';


                $output .='<div class="input-filed">';
                $output .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                        array('name' => esc_html__('Display Name', 'foodbakery'),
                            'id' => 'display_name' . $rand_ids,
                            'extra_atr' => ' placeholder="' . esc_html__('Display Name', 'foodbakery') . '"',
                            'std' => '',
                            'return' => true,
                        )
                );
                $output .= '</div>';
                if ($key == NULL) {
                    $output .='<div class="input-filed">';
                    $output .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                            array('name' => esc_html__('Publisher Name', 'foodbakery'),
                                'id' => 'company_name' . $rand_ids,
                                'extra_atr' => ' placeholder="' . esc_html__('Publisher Name', 'foodbakery') . '"',
                                'std' => '',
                                'return' => true,
                            )
                    );
                    $output .= '</div>';
                }

                $output .='<div class="input-filed">';
                $readonly = ( isset($key_data['email']) ) ? 'readonly' : '';
                $output .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                        array('name' => esc_html__('Email', 'foodbakery'),
                            'id' => 'user_email' . $rand_ids,
                            'extra_atr' => ' placeholder="' . esc_html__('Email', 'foodbakery') . '"' . $readonly . '',
                            'std' => ( isset($key_data['email']) ) ? $key_data['email'] : '',
                            'cust_type'=>'email',
                            'return' => true,
                        )
                );
                $output .= '</div>';

                $output .='<div class="input-filed">';
                $output .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                        array('name' => esc_html__('Password', 'foodbakery'),
                            'id' => 'user_password' . $rand_ids,
                            'extra_atr' => ' placeholder="' . esc_html__('Password', 'foodbakery') . '"',
                            'std' => '',
                            'cust_type' => 'password',
                            'return' => true,
                        )
                );
                $output .= '</div>';


                $output .=$foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                        array('name' => 'user role type',
                            'id' => 'user_role_type' . $rand_ids,
                            'classes' => 'input-holder',
                            'std' => 'publisher',
                            'description' => '',
                            'return' => true,
                            'hint' => '',
                            'icon' => 'icon-user9'
                        )
                );

                $output .='<div class="side-by-side select-icon clearfix">';
                $output .='<div class="select-holder">';

                $output .='</div>';
                $output .='</div>';
                $output .='<div class="input-filed phone">';
                $output .=$foodbakery_form_fields_frontend->foodbakery_form_text_render(
                        array('name' => esc_html__('Phone Number', 'foodbakery'),
                            'id' => 'phone_no' . $rand_ids,
                            'std' => '',
                            'extra_atr' => ' placeholder=" ' . esc_html__('Phone Number', 'foodbakery') . '"',
                            'return' => true,
                        )
                );
                $output .='</div>';
                if ($foodbakery_captcha_switch == 'on' && (!is_user_logged_in())) {
                    if (class_exists('Foodbakery_Captcha')) {
                        global $Foodbakery_Captcha;
                        $output .='<div class="col-md-12 recaptcha-reload" id="recaptcha5_div">';
                        $output .= $Foodbakery_Captcha->foodbakery_generate_captcha_form_callback('recaptcha5', 'true');
                        $output .='</div>';
                    }
                }
                $output .= '<div class="checks-holder">';
                ob_start();
                $output .= do_action('register_form');
                $output .= ob_get_clean();
                $foodbakery_rand_id = rand(122, 1545464897);
                $output .= '<div class="input-filed">';


                $output .= '<div class="ajax-signup-button input-button-loader">';
                $foodbakery_opt_array = array(
                    'std' => esc_html__('Sign Up', 'foodbakery'),
                    'cust_id' => 'submitbtn' . $foodbakery_rand_id,
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'classes' => 'user-submit cs-bgcolor acc-submit',
                    'extra_atr' => ' tabindex="103" onclick="javascript:foodbakery_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_ids . '\' ,\'.ajax-signup-button\')"',
                    'return' => true,
                );
                $output .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                $output .= '</div>';
                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => $role,
                    'cust_id' => 'signin-role',
                    'cust_name' => 'role',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'id' => '',
                    'std' => 'foodbakery_registration_validation',
                    'cust_name' => 'action',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);

                if ($key != NULL) {
                    $foodbakery_opt_array = array(
                        'id' => '',
                        'std' => $key,
                        'cust_name' => 'key',
                        'cust_type' => 'hidden',
                        'return' => true,
                    );
                    $output .= $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
                }

                $output .= '
                                </div>';
                $output .= '</div>';

                $output .= '</form>
                                <div class="register_content">' . do_shortcode($content . $register_text) . '</div>';
                $output .='</div>';

                $output .='</div>';
            } else {
                $output .='<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 register-page">
                                <div class="cs-user-register">
                                    <div class="element-title">
                                           <h2>' . esc_html__('Register', 'foodbakery') . '</h2>
                                   </div>
                                   <p>' . $user_disable_text . '</p>
                                </div>
                            </div>
                        </div>';
                $output .='</div>';
            }
            $output .= '</div>';
            $output .= '</div>';

            $output .= '
                      </div></div>
                                </div>
                          ';
        }

        /*
         * Calling Footer Hook
         */

        public function foodbakery_popup_into_footer() {
            add_action('wp_footer', array($this, 'foodbakery_footer_callback'));
        }

        /*
         * Outputting Signin and Registration Popups into footer
         */

        public function foodbakery_footer_callback() {
            echo force_balance_tags($this->LOGIN_OUTPUT);
            echo force_balance_tags($this->REGISTER_OUTPUT);
        }

        public function foodbakery_dashboar_top_menu_url($url_param = '') {
            global $post;
            $pageid = isset($post->ID) ? $post->ID : '';

            $final_url = '';
            $dashboard_page_link = foodbakery_user_dashboard_page_url('id');
            $dashboard_url_off = 0;
            if ($dashboard_page_link == $pageid) {
                $dashboard_url_off = 1;
            }
            if ($url_param != '') {
                $url_param = '?' . $url_param;
            }
            if ($dashboard_url_off == 1) {
                $final_url = 'javascript:void(0);';
            } else {
                $dashboard_page_link = foodbakery_user_dashboard_page_url('url');
                $final_url = ( $dashboard_page_link . $url_param );
            }

            return $final_url;
        }

        /**
         * Start Function how to add candidate profile menu in top position
         */
        public function foodbakery_profiletop_menu($uid = '') {
            global $post, $cs_plugin_options, $foodbakery_plugin_options, $current_user, $wp_roles, $userdata, $foodbakery_publisher_profile;
            if (is_user_logged_in()) {

                wp_enqueue_script('jquery-mCustomScrollbar');
                wp_enqueue_style('jquery-mCustomScrollbar');

                $menu_cls = '';
                $uid = (isset($uid) and $uid <> '') ? $uid : $current_user->ID;
                $user_display_name = get_the_author_meta('display_name', $uid);
                $cs_page_id = isset($cs_theme_options['cs_dashboard']) ? $cs_theme_options['cs_dashboard'] : '';
                $cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';
                $user_company = get_user_meta($uid, 'foodbakery_company', true);
                $fullName = isset($user_company) && $user_company != '' ? get_the_title($user_company) : '';
                if (strlen($fullName) > 10) {
                    $stringCut = substr($fullName, 0, 10) . "...";
                }

                $user_company_id = get_user_meta($uid, 'foodbakery_company', true);

                $publisher_profile_type = get_post_meta($user_company_id, 'foodbakery_publisher_profile_type', true);
                $foodbakery_profile_image = get_post_meta($user_company_id, 'foodbakery_profile_image', true);
                if ($foodbakery_profile_image != '') {
                    $foodbakery_profile_image = wp_get_attachment_url($foodbakery_profile_image);
                }
                $foodbakery_default_profile_image = isset($foodbakery_plugin_options['foodbakery_default_placeholder_image']) ? $foodbakery_plugin_options['foodbakery_default_placeholder_image'] : '';
                $user_roles = isset($current_user->roles) ? $current_user->roles : '';
                $dashboard_page_link = foodbakery_user_dashboard_page_url();
                $foodbakery_restaurant_add_url = $dashboard_page_link != '' ? add_query_arg(array('tab' => 'add-restaurant'), $dashboard_page_link) : '#';

                if ($foodbakery_profile_image == '') {
                    $foodbakery_profile_image = $foodbakery_default_profile_image;
                }

                if ($foodbakery_profile_image == '') {
                    $foodbakery_profile_image = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-profile-image.jpg';
                }

                if (true === Foodbakery_Member_Permissions::check_permissions('restaurants')) {
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
                    if ($custom_query->have_posts()): $custom_query->the_post();
                        $foodbakery_profile_image = get_post_meta(get_the_ID(), 'foodbakery_cover_image', true);
                        if ($foodbakery_profile_image != '' && is_numeric($foodbakery_profile_image)) {
                            $foodbakery_profile_image = wp_get_attachment_url($foodbakery_profile_image);
                        }
                        if ($foodbakery_profile_image == '') {
                            $foodbakery_profile_image = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-image4x3.jpg';
                        }
                        $fullName = get_the_title();
                    endif;
                    wp_reset_postdata();
                }

                if (is_numeric($foodbakery_profile_image) && $foodbakery_profile_image != '') {
                    $foodbakery_profile_image = wp_get_attachment_url($foodbakery_profile_image);
                }
                ?>
                <div class="user-dashboard-menu">
                    <ul>
                        <li class="user-dashboard-menu-children">
                            <a href="javascript:void(0);">
                                <figure class="profile-image">
                                   <img src="/wp-content/uploads/2021/05/material-design-user-icon-29-300x300-1.png" alt="profile Image" />
                                </figure>
                                <?php echo esc_html($fullName) ?>
                            </a>
                            <?php if (($user_roles != '' && in_array("foodbakery_publisher", $user_roles))) {
                                ?>
                                <ul>
                                    <?php
                                    if ($publisher_profile_type != 'restaurant') {
                                        ?>
                                        <li class="user_dashboard_ajax active" id="foodbakery_publisher_suggested" data-queryvar="dashboard=suggested"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url()); ?>"><i class="icon-dashboard3"></i><?php echo esc_html__("Dashboard", "foodbakery") ?></a></li>

                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_bookings" data-queryvar="dashboard=bookings"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=bookings')); ?>">
                                                <i class="icon-file-text2"></i><?php echo esc_html__("My Bookings", "foodbakery") ?></a></li>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_reviews" data-queryvar="dashboard=reviews"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=reviews')); ?>"><i class="icon-comment2"></i><?php echo esc_html__("My Reviews", "foodbakery") ?></a></li>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_orders" data-queryvar="dashboard=orders"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=orders')); ?>"><i class="icon-add_shopping_cart"></i><?php echo esc_html__("My Orders", "foodbakery") ?></a></li>

                                        <?php
                                        $search_alerts_url = '';
                                        $shortlist_url = '';
                                        // search & alerts link for login shortcode.
                                        if (true === Foodbakery_Member_Permissions::check_permissions('alerts')) {
                                            $search_alerts_url = $this->foodbakery_dashboar_top_menu_url('dashboard=alerts');
                                            echo do_action('foodbakery_top_menu_publisher_dashboard', esc_html__('Searches & Alerts', 'foodbakery'), '<i class="icon-save2"></i>', $search_alerts_url);
                                        }

                                        // Shortlists link for login shortcode.
                                        if (true === Foodbakery_Member_Permissions::check_permissions('shortlists')) {
                                            $shortlist_url = $this->foodbakery_dashboar_top_menu_url('dashboard=shortlists');
                                            echo do_action('foodbakery_top_menu_shortlists_dashboard', esc_html__('Shortlists', 'foodbakery'), '<i class="icon-heart"></i>', $shortlist_url);
                                        }
                                        ?>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_statements" data-queryvar="dashboard=statements"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=statements')); ?>"><i class="icon-file-text22"></i><?php echo esc_html__("Statement", "foodbakery") ?></a></li>
                                        <?php
                                        if (true === Foodbakery_Member_Permissions::check_permissions('company_profile')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_accounts" data-queryvar="dashboard=account"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=account')); ?>"><i class="icon-build"></i><?php echo esc_html__("Account Settings", "foodbakery") ?></a></li>
                                        <?php } ?>
                                        <li>
                                            <?php
                                            if (is_user_logged_in()) {
                                                ?>
                                                <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-log-out"></i><?php echo esc_html__('Signout', 'foodbakery') ?></a>
                                                <?php
                                            }
                                            ?>
                                        </li>

                                        <?php
                                    } else {
                                        $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';

                                        $args = array(
                                            'posts_per_page' => "1",
                                            'post_type' => 'restaurants',
                                            'post_status' => 'publish',
                                            'fields' => 'ids',
                                            'meta_query' => array(
                                                'relation' => 'AND',
                                                array(
                                                    'key' => 'foodbakery_restaurant_publisher',
                                                    'value' => $user_company_id,
                                                    'compare' => '=',
                                                ),
                                                array(
                                                    'key' => 'foodbakery_restaurant_username',
                                                    'value' => $uid,
                                                    'compare' => '=',
                                                ),
                                            ),
                                        );
                                        $custom_query = new WP_Query($args);
                                        $total_restaurants = $custom_query->found_posts;

                                        $pub_restaurant = $custom_query->posts;

                                        $foodbakery_user_type = get_user_meta($uid, 'foodbakery_user_type', true);
                                        ?>

                                        <li class="user_dashboard_ajax <?php echo ($get_tab != 'add-restaurant' ? ' active' : '') ?>" id="foodbakery_publisher_suggested" data-queryvar=""><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url()); ?>"><i class="icon-dashboard3"></i><?php echo esc_html__("Dashboard", "foodbakery") ?></a></li>
                                        <?php
                                        if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
                                            $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                                            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                                            if (isset($_GET['lang'])) {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0], 'lang' => $_GET['lang']), $foodbakery_dashboard_link) : '#';
                                            } else if (cs_wpml_lang_url() != '') {
                                                $cs_lang_string = cs_wpml_lang_url();
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0]), cs_wpml_parse_url($cs_lang_string, $foodbakery_dashboard_link)) : '#';
                                            } else {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0]), $foodbakery_dashboard_link) : '#';
                                            }
                                            ?>
                                            <li class="user_dashboard_url<?php echo (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'add-restaurant' ? ' active' : '') ?>"><a href="<?php echo esc_url_raw($foodbakery_restaurant_add_url) ?>"><i class="icon-building"></i><?php echo esc_html__("My Restaurant", "foodbakery") ?></a></li>
                                            <?php do_action('foodbakery_add_coupon_manu_inrestaurant'); ?>
                                            <?php
                                        } else {
                                            $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                                            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                                            if (isset($_GET['lang'])) {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'lang' => $_GET['lang']), $foodbakery_dashboard_link) : '#';
                                            } else if (cs_wpml_lang_url() != '') {
                                                $cs_lang_string = cs_wpml_lang_url();
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant'), cs_wpml_parse_url($cs_lang_string, $foodbakery_dashboard_link)) : '#';
                                            } else {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant'), $foodbakery_dashboard_link) : '#';
                                            }
                                            ?>
                                            <li class="user_dashboard_url<?php echo (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'add-restaurant' ? ' active' : '') ?>"><a href="<?php echo esc_url_raw($foodbakery_restaurant_add_url) ?>"><i class="icon-building"></i><?php echo esc_html__("My Restaurant", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        ?>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_food_menu" data-queryvar="dashboard=food_menu"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=food_menu')); ?>"><i class="icon-menu5"></i><?php echo esc_html__("Menu Builder", "foodbakery") ?></a></li>
                                        <?php
                                        if (true === Foodbakery_Member_Permissions::check_permissions('orders')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_received_orders" data-queryvar="dashboard=received_orders"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=received_orders')); ?>"><i class="icon-add_shopping_cart"></i><?php echo esc_html__("Orders", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('bookings')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_received_bookings" data-queryvar="dashboard=received_bookings"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=received_bookings')); ?>"><i class="icon-file-text2 "></i><?php echo esc_html__("Bookings", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('reviews')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_my_reviews" data-queryvar="dashboard=my_reviews"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=my_reviews')); ?>"><i class="icon-comment2"></i><?php echo esc_html__("Reviews", "foodbakery") ?></a></li>

                                            <?php
                                        }

                                        if ($foodbakery_user_type != 'team-member' && true === Foodbakery_Member_Permissions::check_permissions('packages')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_packages" data-queryvar="dashboard=packages"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=packages')); ?>"><i class="icon-card_membership"></i><?php echo esc_html__("Memberships", "foodbakery") ?></a></li>
                                            <?php
                                        }

                                        if (true === Foodbakery_Member_Permissions::check_permissions('withdrawals')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_withdrawals" data-queryvar="dashboard=withdrawals"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=withdrawals')); ?>"><i class="icon-bill"></i><?php echo esc_html__("Withdrawals", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('earnings')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_earnings" data-queryvar="dashboard=earnings"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=earnings')); ?>"><i class="icon-money"></i><?php echo esc_html__("Earnings", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('statements')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_statements" data-queryvar="dashboard=statements"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=statements')); ?>"><i class="icon-file-text22"></i><?php echo esc_html__("Statement", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        ?>
                                        <?php if ($foodbakery_user_type != 'team-member') { ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_company" data-queryvar="dashboard=team"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=team')); ?>"><i class="icon-flow-tree"></i><?php echo esc_html__("Team Management", "foodbakery") ?></a></li>
                                        <?php } ?>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_change_password" data-queryvar="dashboard=change_pass"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=change_pass')); ?>"><i class="icon-unlock-alt"></i><?php echo esc_html__("Change Password", "foodbakery") ?></a></li>
                                        <li>
                                            <?php
                                            if (is_user_logged_in()) {
                                                ?>
                                                <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-log-out"></i><?php echo esc_html__('Signout', 'foodbakery') ?></a>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul><?php
                            } else {
                                ?>
                                <ul>
                                    <li>
                                        <h6><?php echo esc_html($user_display_name) ?></h6>
                                        <?php
                                        if (is_user_logged_in()) {
                                            ?>
                                            <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-logout"></i><?php echo esc_html__('Signout', 'foodbakery') ?></a>
                                                <?php
                                            }
                                            ?>
                                    </li>
                                </ul>
                            <?php }
                            ?>
                        </li>
                    </ul>
                </div>
                <div id= "mobile-menu-sidebar-parent">
                    <input type="checkbox" id="check-mobile-menu" />
                    <label for="check-mobile-menu" id="check-mobile-menu-label">
                    <i class="icon icon-bars" id="btn"></i>
                    <i class="icon icon-times" id="cancel"></i>
                    </label>
                    <div class="mobile-menu-sidebar">
                        <header>
                        <a href="javascript:void(0);">
                                <figure class="profile-image">
                                   <img src="/wp-content/uploads/2021/05/material-design-user-icon-29-300x300-1.png" alt="profile Image" />
                                </figure>
                                <?php echo esc_html($fullName) ?>
                            </a>
                        </header>
                        <ul class="mobile-menu-conatiner">
                        <li class="user-dashboard-menu-children">
                            <?php if (($user_roles != '' && in_array("foodbakery_publisher", $user_roles))) {
                                ?>
                                <ul>
                                    <?php
                                    if ($publisher_profile_type != 'restaurant') {
                                        ?>
                                        <li class="user_dashboard_ajax active" id="foodbakery_publisher_suggested" data-queryvar="dashboard=suggested"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url()); ?>"><i class="icon-dashboard3"></i><?php echo esc_html__("Dashboard", "foodbakery") ?></a></li>

                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_bookings" data-queryvar="dashboard=bookings"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=bookings')); ?>">
                                                <i class="icon-file-text2"></i><?php echo esc_html__("My Bookings", "foodbakery") ?></a></li>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_reviews" data-queryvar="dashboard=reviews"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=reviews')); ?>"><i class="icon-comment2"></i><?php echo esc_html__("My Reviews", "foodbakery") ?></a></li>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_orders" data-queryvar="dashboard=orders"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=orders')); ?>"><i class="icon-add_shopping_cart"></i><?php echo esc_html__("My Orders", "foodbakery") ?></a></li>

                                        <?php
                                        $search_alerts_url = '';
                                        $shortlist_url = '';
                                        // search & alerts link for login shortcode.
                                        if (true === Foodbakery_Member_Permissions::check_permissions('alerts')) {
                                            $search_alerts_url = $this->foodbakery_dashboar_top_menu_url('dashboard=alerts');
                                            echo do_action('foodbakery_top_menu_publisher_dashboard', esc_html__('Searches & Alerts', 'foodbakery'), '<i class="icon-save2"></i>', $search_alerts_url);
                                        }

                                        // Shortlists link for login shortcode.
                                        if (true === Foodbakery_Member_Permissions::check_permissions('shortlists')) {
                                            $shortlist_url = $this->foodbakery_dashboar_top_menu_url('dashboard=shortlists');
                                            echo do_action('foodbakery_top_menu_shortlists_dashboard', esc_html__('Shortlists', 'foodbakery'), '<i class="icon-heart"></i>', $shortlist_url);
                                        }
                                        ?>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_statements" data-queryvar="dashboard=statements"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=statements')); ?>"><i class="icon-file-text22"></i><?php echo esc_html__("Statement", "foodbakery") ?></a></li>
                                        <?php
                                        if (true === Foodbakery_Member_Permissions::check_permissions('company_profile')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_accounts" data-queryvar="dashboard=account"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=account')); ?>"><i class="icon-build"></i><?php echo esc_html__("Account Settings", "foodbakery") ?></a></li>
                                        <?php } ?>
                                        <li>
                                            <?php
                                            if (is_user_logged_in()) {
                                                ?>
                                                <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-log-out"></i><?php echo esc_html__('Signout', 'foodbakery') ?></a>
                                                <?php
                                            }
                                            ?>
                                        </li>

                                        <?php
                                    } else {
                                        $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';

                                        $args = array(
                                            'posts_per_page' => "1",
                                            'post_type' => 'restaurants',
                                            'post_status' => 'publish',
                                            'fields' => 'ids',
                                            'meta_query' => array(
                                                'relation' => 'AND',
                                                array(
                                                    'key' => 'foodbakery_restaurant_publisher',
                                                    'value' => $user_company_id,
                                                    'compare' => '=',
                                                ),
                                                array(
                                                    'key' => 'foodbakery_restaurant_username',
                                                    'value' => $uid,
                                                    'compare' => '=',
                                                ),
                                            ),
                                        );
                                        $custom_query = new WP_Query($args);
                                        $total_restaurants = $custom_query->found_posts;

                                        $pub_restaurant = $custom_query->posts;

                                        $foodbakery_user_type = get_user_meta($uid, 'foodbakery_user_type', true);
                                        ?>

                                        <li class="user_dashboard_ajax <?php echo ($get_tab != 'add-restaurant' ? ' active' : '') ?>" id="foodbakery_publisher_suggested" data-queryvar=""><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url()); ?>"><i class="icon-dashboard3"></i><?php echo esc_html__("Dashboard", "foodbakery") ?></a></li>
                                        <?php
                                        if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
                                            $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                                            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                                            if (isset($_GET['lang'])) {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0], 'lang' => $_GET['lang']), $foodbakery_dashboard_link) : '#';
                                            } else if (cs_wpml_lang_url() != '') {
                                                $cs_lang_string = cs_wpml_lang_url();
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0]), cs_wpml_parse_url($cs_lang_string, $foodbakery_dashboard_link)) : '#';
                                            } else {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0]), $foodbakery_dashboard_link) : '#';
                                            }
                                            ?>
                                            <li class="user_dashboard_url<?php echo (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'add-restaurant' ? ' active' : '') ?>"><a href="<?php echo esc_url_raw($foodbakery_restaurant_add_url) ?>"><i class="icon-building"></i><?php echo esc_html__("My Restaurant", "foodbakery") ?></a></li>
                                            <?php do_action('foodbakery_add_coupon_manu_inrestaurant'); ?>
                                            <?php
                                        } else {
                                            $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                                            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                                            if (isset($_GET['lang'])) {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'lang' => $_GET['lang']), $foodbakery_dashboard_link) : '#';
                                            } else if (cs_wpml_lang_url() != '') {
                                                $cs_lang_string = cs_wpml_lang_url();
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant'), cs_wpml_parse_url($cs_lang_string, $foodbakery_dashboard_link)) : '#';
                                            } else {
                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant'), $foodbakery_dashboard_link) : '#';
                                            }
                                            ?>
                                            <li class="user_dashboard_url<?php echo (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'add-restaurant' ? ' active' : '') ?>"><a href="<?php echo esc_url_raw($foodbakery_restaurant_add_url) ?>"><i class="icon-building"></i><?php echo esc_html__("My Restaurant", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        ?>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_food_menu" data-queryvar="dashboard=food_menu"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=food_menu')); ?>"><i class="icon-menu5"></i><?php echo esc_html__("Menu Builder", "foodbakery") ?></a></li>
                                        <?php
                                        if (true === Foodbakery_Member_Permissions::check_permissions('orders')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_received_orders" data-queryvar="dashboard=received_orders"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=received_orders')); ?>"><i class="icon-add_shopping_cart"></i><?php echo esc_html__("Orders", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('bookings')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_received_bookings" data-queryvar="dashboard=received_bookings"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=received_bookings')); ?>"><i class="icon-file-text2 "></i><?php echo esc_html__("Bookings", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('reviews')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_my_reviews" data-queryvar="dashboard=my_reviews"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=my_reviews')); ?>"><i class="icon-comment2"></i><?php echo esc_html__("Reviews", "foodbakery") ?></a></li>

                                            <?php
                                        }

                                        if ($foodbakery_user_type != 'team-member' && true === Foodbakery_Member_Permissions::check_permissions('packages')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_packages" data-queryvar="dashboard=packages"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=packages')); ?>"><i class="icon-card_membership"></i><?php echo esc_html__("Memberships", "foodbakery") ?></a></li>
                                            <?php
                                        }

                                        if (true === Foodbakery_Member_Permissions::check_permissions('withdrawals')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_withdrawals" data-queryvar="dashboard=withdrawals"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=withdrawals')); ?>"><i class="icon-bill"></i><?php echo esc_html__("Withdrawals", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('earnings')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_earnings" data-queryvar="dashboard=earnings"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=earnings')); ?>"><i class="icon-money"></i><?php echo esc_html__("Earnings", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        if (true === Foodbakery_Member_Permissions::check_permissions('statements')) {
                                            ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_statements" data-queryvar="dashboard=statements"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=statements')); ?>"><i class="icon-file-text22"></i><?php echo esc_html__("Statement", "foodbakery") ?></a></li>
                                            <?php
                                        }
                                        ?>
                                        <?php if ($foodbakery_user_type != 'team-member') { ?>
                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_company" data-queryvar="dashboard=team"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=team')); ?>"><i class="icon-flow-tree"></i><?php echo esc_html__("Team Management", "foodbakery") ?></a></li>
                                        <?php } ?>
                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_change_password" data-queryvar="dashboard=change_pass"><a href="<?php echo ($this->foodbakery_dashboar_top_menu_url('dashboard=change_pass')); ?>"><i class="icon-unlock-alt"></i><?php echo esc_html__("Change Password", "foodbakery") ?></a></li>
                                        <li>
                                            <?php
                                            if (is_user_logged_in()) {
                                                ?>
                                                <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-log-out"></i><?php echo esc_html__('Signout', 'foodbakery') ?></a>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul><?php
                            } else {
                                ?>
                                <ul>
                                    <li>
                                        <h6><?php echo esc_html($user_display_name) ?></h6>
                                        <?php
                                        if (is_user_logged_in()) {
                                            ?>
                                            <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-logout"></i><?php echo esc_html__('Signout', 'foodbakery') ?></a>
                                                <?php
                                            }
                                            ?>
                                    </li>
                                </ul>
                            <?php }
                            ?>
                        </li>
                        </ul>
                        </div>
                </div>
                <?php
            }
        }

    }

    global $foodbakery_shortcode_login_frontend;
    $foodbakery_shortcode_login_frontend = new Foodbakery_Shortcode_Login_Frontend();
}
