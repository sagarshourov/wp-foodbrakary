<?php

/**
 * File Type: Google Captcha
 */
if (!class_exists('Foodbakery_Captcha')) {

    Class Foodbakery_Captcha {

        public function __construct() {
            add_action('foodbakery_generate_captcha_form', array($this, 'foodbakery_generate_captcha_form_callback'), 10, 2);
            add_action('wp_ajax_foodbakery_reload_captcha_form', array($this, 'foodbakery_reload_captcha_form_callback'), 10, 1);
            add_action('wp_ajax_nopriv_foodbakery_reload_captcha_form', array($this, 'foodbakery_reload_captcha_form_callback'), 10, 1);
            add_action('foodbakery_verify_captcha_form', array($this, 'foodbakery_verify_captcha_form_callback'), 10, 1);
        }

        public function foodbakery_generate_captcha_form_callback($captcha_id = '',$return_output='false') {
            global $foodbakery_plugin_options;

            $foodbakery_captcha_switch = isset($foodbakery_plugin_options['foodbakery_captcha_switch']) ? $foodbakery_plugin_options['foodbakery_captcha_switch'] : '';
            $foodbakery_sitekey = isset($foodbakery_plugin_options['foodbakery_sitekey']) ? $foodbakery_plugin_options['foodbakery_sitekey'] : '';
            $foodbakery_secretkey = isset($foodbakery_plugin_options['foodbakery_secretkey']) ? $foodbakery_plugin_options['foodbakery_secretkey'] : '';
            $output = '';
            if ($foodbakery_captcha_switch == 'on') {
                if ($foodbakery_sitekey <> '' && $foodbakery_secretkey <> '') {

                    $output .= '<div class="g-recaptcha" data-theme="light" id="' . $captcha_id . '" data-sitekey="' . $foodbakery_sitekey . '" style="">'
                            . '</div> <a class="recaptcha-reload-a" href="javascript:void(0);" onclick="captcha_reload(\'' . admin_url('admin-ajax.php') . '\', \'' . $captcha_id . '\');">'
                            . '<i class="icon-refresh2"></i> ' . esc_html__('Reload', 'foodbakery') . '</a>';
                } else {
                    $output .= '<p>' . esc_html__('Please provide google captcha API keys', 'foodbakery') . '</p>';
                }
            }
            if($return_output=='true'){
            return $output;
            }
            else{
                  echo force_balance_tags($output);
            }
        }

        public function foodbakery_reload_captcha_form_callback() {
            global $foodbakery_plugin_options;
            $captcha_id = $_REQUEST['captcha_id'];
            $foodbakery_sitekey = isset($foodbakery_plugin_options['foodbakery_sitekey']) ? $foodbakery_plugin_options['foodbakery_sitekey'] : '';
            $return_str = "<script>
        var " . $captcha_id . ";
            " . $captcha_id . " = grecaptcha.render('" . $captcha_id . "', {
                'sitekey': '" . $foodbakery_sitekey . "', //Replace this with your Site key
                'theme': 'light'
            });"
                    . "</script>";

            if(function_exists('foodbakery_captcha')){
                $return_str .= foodbakery_captcha($captcha_id);
            }

            echo force_balance_tags($return_str);
            wp_die();
        }

        public function foodbakery_verify_captcha_form_callback($page) {
            global $foodbakery_plugin_options;
            $foodbakery_secretkey = isset($foodbakery_plugin_options['foodbakery_secretkey']) ? $foodbakery_plugin_options['foodbakery_secretkey'] : '';
            $foodbakery_captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
            $foodbakery_captcha_switch = isset($foodbakery_plugin_options['foodbakery_captcha_switch']) ? $foodbakery_plugin_options['foodbakery_captcha_switch'] : '';

            if ($foodbakery_captcha_switch == 'on') {
                if ($page == true) {
                    if (empty($foodbakery_captcha)) {
                        return true;
                    }
                } else {

                    if (empty($foodbakery_captcha)) {
                        $response_array = array(
                            'type' => 'error',
                            'msg' => '<p>' . esc_html__('Please Select Captcha Field', 'foodbakery') . '</p>'
                        );
                        echo json_encode($response_array);
                        exit();
                    }
                }
            }
        }

    }

    global $Foodbakery_Captcha;
    $Foodbakery_Captcha = new Foodbakery_Captcha();
}