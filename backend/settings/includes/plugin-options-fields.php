<?php
/**
 * File Type: Job hunt option fields file
 */
if (!class_exists('foodbakery_options_fields')) {

    class foodbakery_options_fields {

        public function __construct() {

        }

        /**
         * Start Function  how to create Fields Settings
         */
        public function foodbakery_fields($foodbakery_setting_options) {
            global $foodbakery_plugin_options, $foodbakery_form_fields, $foodbakery_html_fields, $help_text, $col_heading;

            $counter = 0;
            $foodbakery_counter = 0;
            $menu = '';
            $output = '';
            $parent_heading = '';
            $style = '';
            $foodbakery_countries_list = '';
            foreach ($foodbakery_setting_options as $value) {
                $counter++;
                $val = '';

                $select_value = '';
                if (isset($value['help_text']) && $value['help_text'] <> '') {
                    $help_text = $value['help_text'];
                } else {
                    $help_text = '';
                }
                if (isset($value['col_heading']) && $value['col_heading'] <> '') {
                    $col_heading = $value['col_heading'];
                } else {
                    $col_heading = '';
                }
                $foodbakery_classes = '';
                if (isset($value['classes']) && $value['classes'] != "") {
                    $foodbakery_classes = $value['classes'];
                }

                $return_fields = apply_filters('foodbakery_plugin_options_fields', $value);
                if (!is_array($return_fields)) {
                    $output .= $return_fields;
                }
                if (isset($value['type'])){
                    switch ($value['type']) {
                        case "heading":
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'fontawesome' => $value['fontawesome'],
                                'options' => $value['options'],
                            );

                            $menu .= $foodbakery_html_fields->foodbakery_set_heading($foodbakery_opt_array);
                            break;

                        case "main-heading":
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'fontawesome' => $value['fontawesome'],
                                'id' => $value['id'],
                            );
                            $menu .= $foodbakery_html_fields->foodbakery_set_main_heading($foodbakery_opt_array);
                            break;

                        case "sub-heading":
                            $foodbakery_counter++;
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'counter' => $foodbakery_counter,
                                'id' => $value['id'],
                                'extra' => isset($value['extra']) ? $value['extra'] : '',
                            );
                            $output .= $foodbakery_html_fields->foodbakery_set_sub_heading($foodbakery_opt_array);
                            break;
                        case "col-right-text":
                            $foodbakery_opt_array = array(
                                'col_heading' => $col_heading,
                                'help_text' => $help_text,
                                'extra' => isset($value['extra']) ? $value['extra'] : '',
                            );
                            $output .= $foodbakery_html_fields->foodbakery_set_col_right($foodbakery_opt_array);
                            break;
                        case "announcement":
                            $foodbakery_counter++;
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'std' => $value['std'],
                                'id' => $value['id'],
                            );
                            $output .= $foodbakery_html_fields->foodbakery_set_announcement($foodbakery_opt_array);
                            break;
                        case "division":
                            $extra_atts = isset($value['extra_atts']) ? $value['extra_atts'] : '';
                            $auto_enable = isset($value['auto_enable']) ? $value['auto_enable'] : true;
                            $d_enable = '';
                            if (isset($value['enable_val'])) {
                                $enable_id = isset($value['enable_id']) ? $value['enable_id'] : '';
                                $enable_val = isset($value['enable_val']) ? $value['enable_val'] : '';
                                $d_val = '';
                                if (isset($foodbakery_plugin_options)) {
                                    if (isset($foodbakery_plugin_options[$enable_id])) {
                                        $d_val = $foodbakery_plugin_options[$enable_id];
                                    }
                                }
                                if ($auto_enable != false) {
                                    $d_enable = ' style="display:none;"';
                                    $d_enable = $d_val == $enable_val ? ' style="display:block;"' : ' style="display:none;"';
                                }
                            }
                            $output .= '<div' . $d_enable . ' ' . $extra_atts . '>';
                            break;

                        case "custom_div":
                            $attss = '';
                            if (isset($value['class']) && $value['class'] != '') {
                                $attss .= ' class="' . $value['class'] . '"';
                            }
                            if (isset($value['id']) && $value['id'] != '') {
                                $attss .= ' id="' . $value['id'] . '"';
                            }
                            $output .= '<div' . $attss . '>';
                            break;
                        case "division_close":
                            $output .= '</div>';
                            break;
                        case "section":

                            $foodbakery_opt_array = array(
                                'id' => $value['id'],
                                'std' => $value['std'],
                            );

                            if (isset($value['accordion']) && $value['accordion'] <> '') {
                                $foodbakery_opt_array['accordion'] = $value['accordion'];
                            }

                            if (isset($value['active']) && $value['active'] <> '') {
                                $foodbakery_opt_array['active'] = $value['active'];
                            }

                            if (isset($value['parrent_id']) && $value['parrent_id'] <> '') {
                                $foodbakery_opt_array['parrent_id'] = $value['parrent_id'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_set_section($foodbakery_opt_array);
                            break;
                        case 'password' :
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $val = $value['std'];
                                }
                            } else {
                                $val = $value['std'];
                            }
                            $cust_type = 'password';
                            $extra_atr = '';
                            $value['cust_type'] = isset($value['cust_type']) ? $value['cust_type'] : '';
                            if ($value['cust_type'] != '') {
                                $cust_type = $value['cust_type'];
                                $extra_atr = 'onClick="send_test_mail(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_foodbakery::plugin_url()) . '\')" value = "' . $value["std"] . '"';
                            }

                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $val,
                                    'cust_type' => $cust_type,
                                    'extra_att' => $extra_atr,
                                    'id' => $value['id'],
                                    'return' => true,
                                ),
                            );

                            if (isset($value['classes']) && $value['classes'] <> '') {
                                $foodbakery_opt_array['field_params']['classes'] = $value['classes'];
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            break;
                        case 'text' :
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $val = $value['std'];
                                }
                            } else {
                                $val = $value['std'];
                            }
                            $active = '';
                            if (isset($value['active']) && $value['active'] !== '') {
                                $active = $value['active'];
                            }
                            $cust_type = '';
                            $extra_atr = '';
                            $value['cust_type'] = isset($value['cust_type']) ? $value['cust_type'] : '';
                            if ($value['cust_type'] != '') {
                                $cust_type = $value['cust_type'];
                                $extra_atr = 'onclick="javascript:send_smtp_mail(\'' . esc_js(admin_url('admin-ajax.php')) . '\');" ';
                            }

                            $extra_attr_html = '';
                            if (isset($value['extra_attr'])) {
                                $extra_attr_html = $value['extra_attr'];
                            }

                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $val,
                                    'cust_type' => $cust_type,
                                    'extra_atr' => $extra_atr . ' ' . $extra_attr_html,
                                    'id' => $value['id'],
                                    'active' => $active,
                                    'return' => true,
                                ),
                            );

                            if (isset($value['classes']) && $value['classes'] <> '') {
                                $foodbakery_opt_array['field_params']['classes'] = $value['classes'];
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }


                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            break;

                        case 'linkedin_access_token' :

                            $lk_client_id = isset($foodbakery_plugin_options['foodbakery_linkedin_app_id']) ? $foodbakery_plugin_options['foodbakery_linkedin_app_id'] : '';
                            $lk_secret_id = isset($foodbakery_plugin_options['foodbakery_linkedin_secret']) ? $foodbakery_plugin_options['foodbakery_linkedin_secret'] : '';
                            $lk_access_token = isset($foodbakery_plugin_options['foodbakery_linkedin_access_token']) ? $foodbakery_plugin_options['foodbakery_linkedin_access_token'] : '';

                            $state = md5(get_home_url());
                            $redirecturl = urlencode(admin_url('admin.php?page=foodbakery_settings'));
                            if ($lk_client_id != '' && $lk_secret_id != '' && $lk_access_token == '') {
                                $linked_url = 'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . $lk_client_id . '&redirect_uri=' . $redirecturl . '&auth%2Flinkedin&state=' . $state . '&scope=w_share+rw_company_admin';
                            }

                            $val = '';
                            if (isset($_GET['code']) && isset($_GET['state']) && $_GET['state'] == $state && $lk_client_id != '' && $lk_secret_id != '' && $lk_access_token == '') {
                                $fields = 'grant_type=authorization_code&code=' . $_GET['code'] . '&redirect_uri=' . $redirecturl . '&client_id=' . $lk_client_id . '&client_secret=' . $lk_secret_id;
                                $ln_acc_tok_json = foodbakery_get_lk_page('https://www.linkedin.com/uas/oauth2/accessToken', '', false, $fields);
                                $ln_acc_tok_json = $ln_acc_tok_json['content'];

                                if ($ln_acc_tok_json) {
                                    $ln_acc_tok = json_decode($ln_acc_tok_json, true);

                                    if (isset($ln_acc_tok['access_token'])) {
                                        $val = $ln_acc_tok['access_token'];
                                    }
                                }
                            } else if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $val = $value['std'];
                                }
                            } else {
                                $val = $value['std'];
                            }

                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'hint_text' => '',
                            );
                            $output .= $foodbakery_html_fields->foodbakery_opening_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'std' => $val,
                                'extra_atr' => '',
                                'id' => $value['id'],
                                'return' => true,
                            );

                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                            if ($lk_client_id != '' && $lk_secret_id != '' && $lk_access_token == '') {
                                $output .= '<a href="' . $linked_url . '">' . esc_html__('Get Access Token', 'foodbakery') . '</a>';
                            }

                            $foodbakery_opt_array = array(
                                'desc' => '',
                            );
                            $output .= $foodbakery_html_fields->foodbakery_closing_field($foodbakery_opt_array);

                            break;

                        case 'fb_access_token' :

                            $fb_app_id = isset($foodbakery_plugin_options['foodbakery_facebook_app_id']) ? $foodbakery_plugin_options['foodbakery_facebook_app_id'] : '';
                            $fb_secret = isset($foodbakery_plugin_options['foodbakery_facebook_secret']) ? $foodbakery_plugin_options['foodbakery_facebook_secret'] : '';
                            $fb_access_token = isset($foodbakery_plugin_options['foodbakery_facebook_access_token']) ? $foodbakery_plugin_options['foodbakery_facebook_access_token'] : '';

                            $state = md5(get_home_url());
                            $redirecturl = urlencode(admin_url('admin.php?page=foodbakery_settings'));
                            if ($fb_app_id != '' && $fb_secret != '' && $fb_access_token == '') {

                                $fb_session_state = md5(uniqid(rand(), TRUE));
                                if (!get_transient('foodbakery_fb_session_state')) {
                                    set_transient('foodbakery_fb_session_state', $fb_session_state, 60 * 60 * 24 * 30);
                                } else {
                                    $fb_session_state = get_transient('foodbakery_fb_session_state');
                                }

                                $fb_access_url = "https://www.facebook.com/v2.6/dialog/oauth?client_id=" . $fb_app_id . "&redirect_uri=" . $redirecturl . "&state=" . $fb_session_state . "&scope=email,public_profile";

                                if (isset($_REQUEST['state']) && ($fb_session_state === $_REQUEST['state'])) {

                                    $code = "";
                                    if (isset($_REQUEST['code'])) {
                                        $code = $_REQUEST["code"];
                                    }
                                    $token_url = "https://graph.facebook.com/v2.6/oauth/access_token?client_id=" . $fb_app_id . "&redirect_uri=" . $redirecturl . "&client_secret=" . $fb_secret . "&code=" . $code;

                                    $params = null;
                                    $get_fb_access_token = "";
                                    $response = wp_remote_get($token_url);

                                    if (is_array($response)) {
                                        if (isset($response['body'])) {
                                            $decode_body = json_decode($response['body'], true);
                                            $params = $decode_body;
                                            if (isset($params['access_token'])) {
                                                $get_fb_access_token = $params['access_token'];
                                            }
                                        }
                                    }


                                    if ($get_fb_access_token != "") {

                                        $offset = 0;
                                        $limit = 100;
                                        $data = array();

                                        do {
                                            $result1 = "";
                                            $pagearray1 = "";
                                            $pp = wp_remote_get("https://graph.facebook.com/v2.6/me/accounts?access_token=$get_fb_access_token&limit=$limit&offset=$offset");
                                            if (is_array($pp)) {
                                                $result1 = $pp['body'];
                                                $pagearray1 = json_decode($result1);
                                                if (is_array($pagearray1->data))
                                                    $data = array_merge($data, $pagearray1->data);
                                            } else
                                                break;
                                            $offset += $limit;
                                        } while (isset($pagearray1->paging->next));

                                        $newpgs = '';

                                        $count = count($data);

                                        $all_pages_names = array();
                                        if ($count > 0) {
                                            for ($i = 0; $i < $count; $i++) {
                                                if (isset($data[$i]->id)) {
                                                    $newpgs .= $data[$i]->id . "-" . $data[$i]->access_token . ",";
                                                    $all_pages_names[$data[$i]->id] = $data[$i]->name;
                                                }
                                            }
                                            $newpgs = rtrim($newpgs, ",");
                                            if ($newpgs != "") {
                                                $newpgs = $newpgs . ",-1";
                                            } else {
                                                $newpgs = -1;
                                            }
                                        }

                                        update_option('foodbakery_fb_pages_ids', $newpgs);
                                        update_option('foodbakery_fb_pages_names', $all_pages_names);
                                    }
                                }
                            }

                            $val = '';
                            if (isset($get_fb_access_token) && $get_fb_access_token != '') {
                                $val = $get_fb_access_token;
                            } else if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $val = $value['std'];
                                }
                            } else {
                                $val = $value['std'];
                            }

                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'hint_text' => '',
                            );
                            $output .= $foodbakery_html_fields->foodbakery_opening_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'std' => $val,
                                'extra_atr' => '',
                                'id' => $value['id'],
                                'return' => true,
                            );

                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);

                            if ($fb_app_id != '' && $fb_secret != '' && $fb_access_token == '') {
                                $output .= '<a href="' . $fb_access_url . '">' . esc_html__('Get Access Token', 'foodbakery') . '</a>';
                            }

                            $foodbakery_opt_array = array(
                                'desc' => '',
                            );
                            $output .= $foodbakery_html_fields->foodbakery_closing_field($foodbakery_opt_array);

                            $all_fb_pages = get_option('foodbakery_fb_pages_names');
                            if (is_array($all_fb_pages)) {
                                $fb_sharing_page = isset($foodbakery_plugin_options['foodbakery_fb_sharing_page']) ? $foodbakery_plugin_options['foodbakery_fb_sharing_page'] : '';

                                $foodbakery_opt_array = array(
                                    'name' => esc_html__('Select Page for Sharing', 'foodbakery'),
                                    'hint_text' => '',
                                );
                                $output .= $foodbakery_html_fields->foodbakery_opening_field($foodbakery_opt_array);

                                $foodbakery_opt_array = array(
                                    'std' => $fb_sharing_page,
                                    'extra_atr' => '',
                                    'id' => 'fb_sharing_page',
                                    'return' => true,
                                    'options' => $all_fb_pages
                                );
                                $output .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);

                                $foodbakery_opt_array = array(
                                    'desc' => '',
                                );
                                $output .= $foodbakery_html_fields->foodbakery_closing_field($foodbakery_opt_array);
                            }

                            break;

                        case 'text3' :
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                    $val2 = $foodbakery_plugin_options['foodbakery_' . $value['id2']];
                                    $val3 = $foodbakery_plugin_options['foodbakery_' . $value['id3']];
                                } else {
                                    $val = $value['std'];
                                    $val2 = $value['std2'];
                                    $val3 = $value['std3'];
                                }
                            } else {
                                $val = $value['std'];
                                $val2 = $value['std2'];
                                $val3 = $value['std3'];
                            }

                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => 'radius_fields',
                                'desc' => '',
                                'hint_text' => $value['hint_text'],
                                'fields_list' => array(
                                    array(
                                        'type' => 'text', 'field_params' => array(
                                        'std' => $val,
                                        'id' => $value['id'],
                                        'extra_atr' => ' placeholder="' . $value['placeholder'] . '"',
                                        'return' => true,
                                        'classes' => 'input-small',
                                    ),
                                    ),
                                    array(
                                        'type' => 'text', 'field_params' => array(
                                        'std' => $val2,
                                        'id' => $value['id2'],
                                        'extra_atr' => ' placeholder="' . $value['placeholder2'] . '"',
                                        'return' => true,
                                        'classes' => 'input-small',
                                    ),
                                    ),
                                    array(
                                        'type' => 'text', 'field_params' => array(
                                        'std' => $val3,
                                        'id' => $value['id3'],
                                        'extra_atr' => ' placeholder="' . $value['placeholder3'] . '"',
                                        'return' => true,
                                        'classes' => 'input-small',
                                    ),
                                    )
                                ),
                            );

                            $output .= $foodbakery_html_fields->foodbakery_multi_fields($foodbakery_opt_array);

                            break;
                        case 'range' :
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $val = $value['std'];
                                }
                            } else {
                                $val = $value['std'];
                            }

                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $val,
                                    'id' => $value['id'],
                                    'range' => true,
                                    'min' => $value['min'],
                                    'max' => $value['max'],
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            break;
                        case 'textarea':
                            $val = $value['std'];
                            $std = get_option($value['id']);
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $val = $value['std'];
                                }
                            } else {
                                $val = $value['std'];
                            }
                            if (!isset($value['foodbakery_editor'])) {
                                $value['foodbakery_editor'] = false;
                            }
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $val,
                                    'id' => $value['id'],
                                    'return' => true,
                                    'foodbakery_editor' => $value['foodbakery_editor'],
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
                            break;
                        case "radio":
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $select_value = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                }
                            } else {

                            }
                            $output .= '<div id="mail_from_name" class="form-elements">';
                            $output .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . $value['name'] . '</label></div>';
                            $output .= '<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
                            foreach ($value['options'] as $key => $option) {
                                $checked = '';
                                if ($select_value != '') {
                                    if ($select_value == $option) {
                                        $checked = ' checked';
                                    }
                                } else {
                                    if ($value['std'] == $option) {
                                        $checked = ' checked';
                                    }
                                }

                                $output .= $foodbakery_html_fields->foodbakery_radio_field(
                                    array(
                                        'name' => $value['name'],
                                        'id' => $value['id'],
                                        'classes' => '',
                                        'std' => '',
                                        'description' => $option,
                                        'hint' => '',
                                        'prefix_on' => false,
                                        'extra_atr' => $checked,
                                        'field_params' => array(
                                            'std' => $option,
                                            'id' => $value['id'],
                                            'return' => true,
                                        ),
                                    )
                                );
                            }
                            $output .= '</div></div>';
                            break;
                        case 'select':
                            if (isset($foodbakery_plugin_options) and $foodbakery_plugin_options <> '') {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']]) and $foodbakery_plugin_options['foodbakery_' . $value['id']] <> '') {
                                    $select_value = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $select_value = $value['std'];
                                }
                            } else {
                                $select_value = $value['std'];
                            }
                            if ($select_value == 'absolute') {
                                if ($foodbakery_plugin_options['foodbakery_headerbg_options'] == 'foodbakery_rev_slider') {
                                    $output .= '<style>
                                                    #foodbakery_headerbg_image_upload,#foodbakery_headerbg_color_color,#foodbakery_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#foodbakery_headerbg_slider_1,#tab-header-options ul#foodbakery_headerbg_options_header{ display:block;}
                                            </style>';
                                } else if ($foodbakery_plugin_options['foodbakery_headerbg_options'] == 'foodbakery_bg_image_color') {
                                    $output .= '<style>
                                                    #foodbakery_headerbg_image_upload,#foodbakery_headerbg_color_color,#foodbakery_headerbg_image_box{ display:block;}
                                                    #tab-header-options ul#foodbakery_headerbg_slider_1{ display:none; }
                                            </style>';
                                } else {
                                    $output .= '<style>
                                                    #foodbakery_headerbg_options_header{display:block;}
                                                    #foodbakery_headerbg_image_upload,#foodbakery_headerbg_color_color,#foodbakery_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#foodbakery_headerbg_slider_1{ display:none; }
                                            </style>';
                                }
                            } elseif ($select_value == 'relative') {
                                $output .= '<style>
                                                    #tab-header-options ul#foodbakery_headerbg_slider_1,#tab-header-options ul#foodbakery_headerbg_options_header,#tab-header-options ul#foodbakery_headerbg_image_upload,#tab-header-options ul#foodbakery_headerbg_color_color,#tab-header-options #foodbakery_headerbg_image_box{ display:none;}
                                      </style>';
                            }
                            $output .= ($value['id'] == 'foodbakery_bgimage_position') ? '<div class="main_tab">' : '';
                            $select_header_bg = ($value['id'] == 'foodbakery_header_position') ? 'onchange=javascript:foodbakery_set_headerbg(this.value)' : '';
                            $value_multiple = isset($value['multiple']) ? $value['multiple'] : false;
                            $value_hint_text = isset($value['hint_text']) ? $value['hint_text'] : false;

                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'multi' => $value_multiple,
                                'hint_text' => $value_hint_text,
                                'field_params' => array(
                                    'std' => $select_value,
                                    'id' => $value['id'],
                                    'options' => $value['options'],
                                    'classes' => $foodbakery_classes,
                                    'return' => true,
                                ),
                            );

                            if (isset($value['change']) && $value['change'] == 'yes') {
                                $foodbakery_opt_array['field_params']['onclick'] = $value['id'] . '_change(this.value)';
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            $output .= ($value['id'] == 'foodbakery_bgimage_position') ? '</div>' : '';
                            break;
                        case 'custom_user_select':
                            if (isset($foodbakery_plugin_options) and $foodbakery_plugin_options <> '') {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']]) and $foodbakery_plugin_options['foodbakery_' . $value['id']] <> '') {
                                    $select_value = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                    $user_info = get_userdata($select_value);
                                    if (!empty($user_info)) {
                                        $value['options'] = array($select_value => $user_info->display_name);
                                    }
                                } else {
                                    $select_value = $value['std'];
                                }
                            } else {
                                $select_value = $value['std'];
                            }
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                    'id' => isset($value['id']) ? $value['id'] : '',
                                    'name' => isset($value['name']) ? $value['name'] : '',
                                    'hint_text' => isset($value['hint_text']) ? $value['hint_text'] : false
                                )
                            );


                            $main_wraper = isset($value['main_wraper']) ? $value['main_wraper'] : false;
                            $main_wraper_class = isset($value['main_wraper_class']) ? $value['main_wraper_class'] : '';
                            $main_wraper_extra = isset($value['main_wraper_extra']) ? $value['main_wraper_extra'] : '';

                            if (isset($main_wraper) && $main_wraper == true) {
                                $main_wraper_class_str = '';
                                if (isset($main_wraper_class) && $main_wraper_class != '') {
                                    $main_wraper_class_str = $main_wraper_class;
                                }
                                $main_wraper_extra_str = '';
                                if (isset($main_wraper_extra) && $main_wraper_extra != '') {
                                    $main_wraper_extra_str = $main_wraper_extra;
                                }
                                $main_wraper_start = '<div class="' . $main_wraper_class_str . '" ' . $main_wraper_extra_str . '>';
                                $main_wraper_end = '</div>';
                            }
                            $output .= $main_wraper_start;
                            $foodbakery_opt_array = array(
                                'std' => $select_value,
                                'id' => $value['id'],
                                'options' => $value['options'],
                                'classes' => $foodbakery_classes,
                                'markup' => isset($value['markup']) ? $value['markup'] : '',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $main_wraper_end;

                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));

                            break;
                        case 'select_values' :
                            if (isset($foodbakery_plugin_options) and $foodbakery_plugin_options <> '') {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']]) and $foodbakery_plugin_options['foodbakery_' . $value['id']] <> '') {
                                    $select_value = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $select_value = $value['std'];
                                }
                            } else {
                                $select_value = $value['std'];
                            }
                            $output .= ($value['id'] == 'foodbakery_bgimage_position') ? '<div class="main_tab">' : '';
                            $select_header_bg = ($value['id'] == 'foodbakery_header_position') ? 'onchange=javascript:foodbakery_set_headerbg(this.value)' : '';
                            $foodbakery_search_display = '';
                            if ($value['id'] == 'foodbakery_search_by_location') {
                                $foodbakery_job_loc_sugg = isset($foodbakery_plugin_options['foodbakery_job_loc_sugg']) ? $foodbakery_plugin_options['foodbakery_job_loc_sugg'] : '';
                                $foodbakery_search_display = $foodbakery_job_loc_sugg == 'Website' ? 'block' : 'none';
                            }
                            if ($value['id'] == 'foodbakery_search_by_location_city') {
                                $foodbakery_search_by_location = isset($foodbakery_plugin_options['foodbakery_search_by_location']) ? $foodbakery_plugin_options['foodbakery_search_by_location'] : '';
                                $foodbakery_search_display = $foodbakery_search_by_location == 'single_city' ? 'block' : 'none';
                            }
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $select_value,
                                    'id' => $value['id'],
                                    'options' => $value['options'],
                                    'classes' => $foodbakery_classes,
                                    'return' => true,
                                ),
                            );

                            if (isset($value['change']) && $value['change'] == 'yes') {
                                $foodbakery_opt_array['field_params']['onclick'] = $value['id'] . '_change(this.value)';
                            }

                            if (isset($value['extra_atts']) && $value['extra_atts'] != '') {
                                $foodbakery_opt_array['field_params']['extra_atr'] = $value['extra_atts'];
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            break;
                        case 'ad_select':
                            if (isset($foodbakery_plugin_options) and $foodbakery_plugin_options <> '') {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']]) and $foodbakery_plugin_options['foodbakery_' . $value['id']] <> '') {
                                    $select_value = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $select_value = $value['std'];
                                }
                            } else {
                                $select_value = $value['std'];
                            }
                            if ($select_value == 'absolute') {
                                if ($foodbakery_plugin_options['foodbakery_headerbg_options'] == 'foodbakery_rev_slider') {
                                    $output .= '<style>
                                                    #foodbakery_headerbg_image_upload,#foodbakery_headerbg_color_color,#foodbakery_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#foodbakery_headerbg_slider_1,#tab-header-options ul#foodbakery_headerbg_options_header{ display:block;}
                                            </style>';
                                } else if ($foodbakery_plugin_options['foodbakery_headerbg_options'] == 'foodbakery_bg_image_color') {
                                    $output .= '<style>
                                                    #foodbakery_headerbg_image_upload,#foodbakery_headerbg_color_color,#foodbakery_headerbg_image_box{ display:block;}
                                                    #tab-header-options ul#foodbakery_headerbg_slider_1{ display:none; }
                                            </style>';
                                } else {
                                    $output .= '<style>
                                                    #foodbakery_headerbg_options_header{display:block;}
                                                    #foodbakery_headerbg_image_upload,#foodbakery_headerbg_color_color,#foodbakery_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#foodbakery_headerbg_slider_1{ display:none; }
                                            </style>';
                                }
                            } elseif ($select_value == 'relative') {
                                $output .= '<style>
                                            #tab-header-options ul#foodbakery_headerbg_slider_1,#tab-header-options ul#foodbakery_headerbg_options_header,#tab-header-options ul#foodbakery_headerbg_image_upload,#tab-header-options ul#foodbakery_headerbg_color_color,#tab-header-options #foodbakery_headerbg_image_box{ display:none;}
                                     </style>';
                            }
                            $output .= ($value['id'] == 'foodbakery_bgimage_position') ? '<div class="main_tab">' : '';
                            $select_header_bg = ($value['id'] == 'foodbakery_header_position') ? 'onchange=javascript:foodbakery_set_headerbg(this.value)' : '';
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $select_value,
                                    'id' => $value['id'],
                                    'options' => $value['options'],
                                    'return' => true,
                                ),
                            );

                            if (isset($value['change']) && $value['change'] == 'yes') {
                                $foodbakery_opt_array['field_params']['onclick'] = $value['id'] . '_change(this.value)';
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

                            break;

                        case "checkbox":
                            $std = '';
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $std = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                }
                            } else {
                                $std = $value['std'];
                            }
                            $simple = false;
                            if (isset($value['simple'])) {
                                $simple = $value['simple'];
                            }
                            $field_hint_text = false;
                            if (isset($value['hint_text'])) {
                                $field_hint_text = $value['hint_text'];
                            }
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $field_hint_text,
                                'field_params' => array(
                                    'std' => $std,
                                    'id' => $value['id'],
                                    'extra_atr' => isset($value['onchange']) ? 'onchange=' . $value['onchange'] : '',
                                    'return' => true,
                                    'simple' => $simple,
                                ),
                            );

                            if (isset($value['onchange']) && $value['onchange'] <> '') {
                                $foodbakery_opt_array['field_params']['extra_atr'] = ' onchange=' . $value['onchange'];
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

                            break;
                        case "color":
                            $val = $value['std'];
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                }
                            } else {
                                $std = $value['std'];
                                if ($std != '') {
                                    $val = $std;
                                }
                            }
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $val,
                                    'classes' => 'bg_color',
                                    'id' => $value['id'],
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            break;
                        case "packages":
                            $obj = new foodbakery_plugin_options();
                            $output .= $obj->foodbakery_packages_section();
                            break;
                        case "cv_pkgs":
                            $obj = new foodbakery_plugin_options();
                            $output .= $obj->foodbakery_cv_pkgs_section();
                            break;
                        case "safetytext":
                            ob_start();
                            $obj = new foodbakery_plugin_options();
                            $obj->foodbakery_safetytext_section();
                            $post_data = ob_get_clean();
                            $output .= $post_data;
                            break;
                        case "gateways":
                            global $gateways;
                            $general_settings = new FOODBAKERY_PAYMENTS();
                            $foodbakery_counter = '';
                            foreach ($gateways as $key => $value) {
                                $output .= '<div class="theme-help">';
                                $output .= '<h4>' . $value . '</h4>';
                                $output .= '<div class="clear"></div>';
                                $output .= '</div>';
                                if (class_exists($key)) {
                                    $settings = new $key();
                                    $foodbakery_settings = $settings->settings();
                                    $html = '';
                                    foreach ($foodbakery_settings as $key => $params) {
                                        ob_start();
                                        foodbakery_settings_fields($key, $params);
                                        $post_data = ob_get_clean();
                                        $output .= $post_data;
                                    }
                                }
                            }
                            break;

                        case "upload":
                            $foodbakery_counter++;
                            if (isset($foodbakery_plugin_options) and $foodbakery_plugin_options <> '' && isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                            } else {
                                $val = $value['std'];
                            }
                            $display = ($val <> '' ? 'display' : 'none');
                            if (isset($value['tab'])) {
                                $output .= '<div class="main_tab"><div class="horizontal_tab" style="display:' . $value['display'] . '" id="' . $value['tab'] . '">';
                            }
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'std' => $val,
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $val,
                                    'id' => $value['id'],
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);

                            if (isset($value['tab'])) {
                                $output .= '</div></div>';
                            }
                            break;
                        case "upload logo":
                            $foodbakery_counter++;

                            if (isset($foodbakery_plugin_options) and $foodbakery_plugin_options <> '' && isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                            } else {
                                $val = $value['std'];
                            }

                            $display = ($val <> '' ? 'display' : 'none');
                            if (isset($value['tab'])) {
                                $output .= '<div class="main_tab"><div class="horizontal_tab" id="' . $value['tab'] . '">';
                            }
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'std' => $val,
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'field_params' => array(
                                    'std' => $val,
                                    'id' => $value['id'],
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);

                            if (isset($value['tab'])) {
                                $output .= '</div></div>';
                            }
                            break;
                        case "custom_fields":
                            $foodbakery_counter++;
                            global $foodbakery_job_cus_fields;
                            $foodbakery_job_cus_fields = get_option("foodbakery_job_cus_fields");
                            $foodbakery_fields_obj = new foodbakery_custom_fields_options();
                            $output .= '<div class="inside-tab-content">
                                        <div class="dragitem">
                                            <div class="pb-form-buttons">
                                            <span class="foodbakery_cus_fields_text">' . esc_html__("Click to Add", "foodbakery") . '</span>
                                                    <ul>
                                                     
                                                    <li><a ' . foodbakery_tooltip_helptext_string(esc_html__('Text', 'foodbakery'), true) . ' href="javascript:foodbakery_add_custom_field(\'foodbakery_pb_text\')" data-type="text" data-name="custom_text"><i class="icon-file-text-o"></i></a></li>
                                                    <li><a ' . foodbakery_tooltip_helptext_string(esc_html__('Textarea', 'foodbakery'), true) . ' href="javascript:foodbakery_add_custom_field(\'foodbakery_pb_textarea\')" data-type="textarea" data-name="custom_textarea"><i class="icon-text"></i></a></li>
                                                    <li><a ' . foodbakery_tooltip_helptext_string(esc_html__('Dropdown', 'foodbakery'), true) . ' href="javascript:foodbakery_add_custom_field(\'foodbakery_pb_dropdown\')" data-type="select" data-name="custom_select"><i class="icon-download10"></i></a></li>
                                                    <li><a ' . foodbakery_tooltip_helptext_string(esc_html__('Date', 'foodbakery'), true) . ' href="javascript:foodbakery_add_custom_field(\'foodbakery_pb_date\')" data-type="date" data-name="custom_date"><i class="icon-calendar-o"></i></a></li>
                                                    <li><a ' . foodbakery_tooltip_helptext_string(esc_html__('Email', 'foodbakery'), true) . ' href="javascript:foodbakery_add_custom_field(\'foodbakery_pb_email\')" data-type="email" data-name="custom_email"><i class="icon-envelope4"></i></a></li>
                                                    <li><a ' . foodbakery_tooltip_helptext_string(esc_html__('Url', 'foodbakery'), true) . ' href="javascript:foodbakery_add_custom_field(\'foodbakery_pb_url\')" data-type="url" data-name="custom_url"><i class="icon-link4"></i></a></li>
                                                    <li><a ' . foodbakery_tooltip_helptext_string(esc_html__('Range', 'foodbakery'), true) . ' href="javascript:foodbakery_add_custom_field(\'foodbakery_pb_range\')" data-type="url" data-name="custom_range"><i class=" icon-target5"></i></a></li>
                                                    </ul>
                                            </div>
                                        </div>
                                    <div id="foodbakery_field_elements" class="cs-custom-fields">';
                            $foodbakery_count_node = time();
                            if (is_array($foodbakery_job_cus_fields) && sizeof($foodbakery_job_cus_fields) > 0) {
                                foreach ($foodbakery_job_cus_fields as $f_key => $foodbakery_field) {
                                    global $foodbakery_f_counter;
                                    $foodbakery_f_counter = $f_key;
                                    if (isset($foodbakery_field['type']) && $foodbakery_field['type'] == "text") {
                                        $foodbakery_count_node++;
                                        $output .= $foodbakery_fields_obj->foodbakery_pb_text(1, true);
                                    } else if (isset($foodbakery_field['type']) && $foodbakery_field['type'] == "textarea") {
                                        $foodbakery_count_node++;
                                        $output .= $foodbakery_fields_obj->foodbakery_pb_textarea(1, true);
                                    } else if (isset($foodbakery_field['type']) && $foodbakery_field['type'] == "dropdown") {
                                        $foodbakery_count_node++;
                                        $output .= $foodbakery_fields_obj->foodbakery_pb_dropdown(1, true);
                                    } else if (isset($foodbakery_field['type']) && $foodbakery_field['type'] == "date") {
                                        $foodbakery_count_node++;
                                        $output .= $foodbakery_fields_obj->foodbakery_pb_date(1, true);
                                    } else if (isset($foodbakery_field['type']) && $foodbakery_field['type'] == "email") {
                                        $foodbakery_count_node++;
                                        $output .= $foodbakery_fields_obj->foodbakery_pb_email(1, true);
                                    } else if (isset($foodbakery_field['type']) && $foodbakery_field['type'] == "url") {
                                        $foodbakery_count_node++;
                                        $output .= $foodbakery_fields_obj->foodbakery_pb_url(1, true);
                                    } else if (isset($foodbakery_field['type']) && $foodbakery_field['type'] == "range") {
                                        $foodbakery_count_node++;
                                        $output .= $foodbakery_fields_obj->foodbakery_pb_range(1, true);
                                    }
                                }
                            }

                            $output .= '</div>
                                    <script type="text/javascript">
                                        jQuery(function() {
                                                foodbakery_custom_fields_script(\'foodbakery_field_elements\');
                                        });
                                        jQuery(document).ready(function($) {
                                                foodbakery_check_fields_avail();
                                        });
                                        var counter = ' . esc_js($foodbakery_count_node) . ';
                                        function foodbakery_add_custom_field(action){
                                            counter++;
                                            var fields_data = "action=" + action + \'&counter=\' + counter;
                                            jQuery.ajax({
                                                type:"POST",
                                                url: "' . esc_js(admin_url('admin-ajax.php')) . '",
                                                data: fields_data,
                                                success:function(data){
                                                    jQuery("#foodbakery_field_elements").append(data);
                                                }
                                            });
                                        }
                                    </script>
                                </div>';
                            break;

                        case 'select_dashboard':
                            if (isset($foodbakery_plugin_options) and $foodbakery_plugin_options <> '') {
                                if (isset($foodbakery_plugin_options[$value['id']])) {
                                    $select_value = $foodbakery_plugin_options[$value['id']];
                                }
                            } else {
                                $select_value = $value['std'];
                            }
                            $field_args = array(
                                'depth' => 0,
                                'child_of' => 0,
                                'class' => 'chosen-select',
                                'sort_order' => 'ASC',
                                'sort_column' => 'post_title',
                                'show_option_none' => esc_html__('Please select a page', "foodbakery"),
                                'hierarchical' => '1',
                                'exclude' => '',
                                'include' => '',
                                'meta_key' => '',
                                'meta_value' => '',
                                'authors' => '',
                                'exclude_tree' => '',
                                'selected' => $select_value,
                                'echo' => 0,
                                'name' => $value['id'],
                                'post_type' => 'page'
                            );
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'id' => $value['id'],
                                'desc' => $value['desc'],
                                'hint_text' => isset($value['hint_text']) ? $value['hint_text'] : '',
                                'std' => $select_value,
                                'args' => $field_args,
                                'return' => true,
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            if (isset($value['custom']) && $value['custom'] == true) {
                                $output .= $foodbakery_html_fields->foodbakery_custom_select_page_field($foodbakery_opt_array);
                            } else {
                                $output .= $foodbakery_html_fields->foodbakery_select_page_field($foodbakery_opt_array);
                            }

                            break;
                        case 'default_locations_list':
                            if (isset($foodbakery_plugin_options)) {
                                if (isset($foodbakery_plugin_options['foodbakery_' . $value['id']])) {
                                    $val = $foodbakery_plugin_options['foodbakery_' . $value['id']];
                                } else {
                                    $val = $value['std'];
                                }
                            } else {
                                $val = $value['std'];
                            }
                            $tag_obj_array = array();
                            $selected_location = '';
                            if (is_array($val) && sizeof($val) > 0) {
                                foreach ($val as $tag_r) {
                                    $tag_obj = get_term_by('slug', $tag_r, 'foodbakery_locations');
                                    if (is_object($tag_obj)) {
                                        $tag_obj_array[$tag_obj->slug] = $tag_obj->name;
                                        $selected_location .= $tag_obj->slug . ',';
                                    }
                                }
                            }
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'desc' => '',
                                'hint_text' => $value['hint_text'],
                                'echo' => false,
                                'multi' => true,
                                'desc' => '',
                                'field_params' => array(
                                    'std' => $selected_location,
                                    'id' => $value['id'],
                                    'classes' => 'chosen-select-no-single',
                                    'options' => $tag_obj_array,
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            $output .= '
			<script>
				jQuery(window).load(function(){
						chosen_ajaxify("foodbakery_default_locations_list", "' . esc_url(admin_url('admin-ajax.php')) . '", "dropdown_options_for_search_location_data");
				});
			</script>';
                            break;
                        case 'default_cousine_list':
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Popular Cousines", 'foodbakery'),
                                'hint_text' => esc_html__('Select Popular Cousines', 'foodbakery'),
                            ));
                            $terms_restaurant_category = get_terms('restaurant-category', array(
                                'hide_empty' => false,
                            ));

                            $tempOptions = array();
                            if (count($terms_restaurant_category) > 0) {
                                foreach ($terms_restaurant_category as $term_res) {
                                    $tempOptions[$term_res->slug] = $term_res->name;
                                }
                            }

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('', 'foodbakery'),
                                'desc' => '',
                                'id' => $value['id'],
                                'cust_name' => $value['cust_name'],
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'extra_atr' => ' multiple',
                                'options' => $tempOptions,
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            /*
                             * Get data from plugin options to populate on frontend.
                             */
                            $frontend_location_parts = array();
                            if (isset($foodbakery_plugin_options[$value['id']])) {
                                $frontend_location_parts = $foodbakery_plugin_options[$value['id']];
                            }
                            ob_start();
                            ?>
                            <script type="text/javascript">
                                "use strict";
                                (function ($) {
                                    $(function () {
                                        var <?php echo esc_html($value['id']); ?> = <?php echo json_encode($frontend_location_parts); ?>;
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>").change(function () {
                                            // For sorting items in an order.
                                            $("#foodbakery_<?php echo esc_html($value['id']); ?>").trigger("chosen:updated");
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?> option").each(function (key, elem) {
                                            var val = $(this).val();
                                            if ($.inArray(val, <?php echo esc_html($value['id']); ?>) > -1) {
                                                $(this).prop('selected', true);
                                            } else {
                                                $(this).prop('selected', false);
                                            }
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>").trigger("chosen:updated");
                                    });
                                })(jQuery);
                            </script>
                            <?php
                            $output .= ob_get_clean();
                            break;
                        case 'default_dynamic_location_fields':
                            $output .= $GLOBALS['Foodbakery_Plugin_Functions']->foodbakery_location_fields('', 'default', false);
                            break;
                        case 'default_location_fields':
                            global $foodbakery_plugin_options, $post;
                            $foodbakery_map_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
                            $foodbakery_map_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
                            $foodbakery_map_zoom = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '';
                            if ($foodbakery_map_latitude == '') {
                                $foodbakery_map_latitude = '51.5';
                            }
                            if ($foodbakery_map_longitude == '') {
                                $foodbakery_map_longitude = '-0.2';
                            }
                            if ($foodbakery_map_zoom == '') {
                                $foodbakery_map_zoom = '9';
                            }

                            $foodbakery_map_address = isset($foodbakery_plugin_options['foodbakery_post_loc_address']) ? $foodbakery_plugin_options['foodbakery_post_loc_address'] : '';

                            $foodbakery_obj = new wp_foodbakery();
                            $foodbakery_obj->foodbakery_location_gmap_script();
                            $foodbakery_obj->foodbakery_google_place_scripts();
                            $foodbakery_obj->foodbakery_autocomplete_scripts();

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Address', 'foodbakery'),
                                'id' => 'post_loc_address',
                                'desc' => '',
                                'field_params' => array(
                                    'std' => $foodbakery_map_address,
                                    'id' => 'post_loc_address',
                                    'classes' => 'foodbakery-search-location',
                                    'extra_atr' => ' onkeypress="foodbakery_gl_search_map(this.value)" placeholder="Enter a location" autocomplete="off"',
                                    'cust_id' => 'loc_address',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['address_hint']) && $value['address_hint'] != '') {
                                $foodbakery_opt_array['hint_text'] = $value['address_hint'];
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Latitude', 'foodbakery'),
                                'id' => 'post_loc_latitude',
                                'desc' => '',
                                'field_params' => array(
                                    'std' => $foodbakery_map_latitude,
                                    'id' => 'post_loc_latitude',
                                    'classes' => 'gllpLatitude',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }
                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Longitude', 'foodbakery'),
                                'id' => 'post_loc_longitude',
                                'desc' => '',
                                'field_params' => array(
                                    'std' => $foodbakery_map_longitude,
                                    'id' => 'post_loc_longitude',
                                    'classes' => 'gllpLongitude',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }

                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => '',
                                'id' => 'map_search_btn',
                                'desc' => '',
                                'field_params' => array(
                                    'std' => esc_html__('Search This Location on Map', 'foodbakery'),
                                    'id' => 'map_t_op_search',
                                    'cust_type' => 'button',
                                    'classes' => 'gllpSearchButton',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $foodbakery_opt_array['split'] = $value['split'];
                            }
                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_full_opening_field(array());
                            $output .= '
                        <div class="clear"></div>';

                            $output .= '
						<div class="clear"></div>
						<div class="cs-map-section" style="float:left; width:100%; height:300px;">
							<div class="gllpMap" id="cs-map-location-id"></div>
						</div>';

                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                    'desc' => '',
                                )
                            );

                            $output .= '
								</div>
							</div>
						</div>';
                            $output .= '
						<script type="text/javascript">
							jQuery(document).ready(function() {
								function markerDragHandleEvent(event) {
									document.getElementById(\'foodbakery_post_loc_latitude\').value = event.latLng.lat();
									document.getElementById(\'foodbakery_post_loc_longitude\').value = event.latLng.lng();
								}
								function new_map_cred(newLat, newLng, map){
									newLat = parseFloat(newLat);
									newLng = parseFloat(newLng);
									var latlng = new google.maps.LatLng(newLat, newLng);
									var mapOptions = {
										zoom: 9,
										center: latlng,
										mapTypeId: google.maps.MapTypeId.ROADMAP
									}
									map = new google.maps.Map(document.getElementById(\'cs-map-location-id\'), mapOptions);
									var marker = new google.maps.Marker({
										position: new google.maps.LatLng(newLat, newLng),
										map: map,
										draggable: true
									});
									marker.addListener(\'drag\', markerDragHandleEvent);
									marker.addListener(\'dragend\', markerDragHandleEvent);
								}
								function map_initialize() {
									var mapCanvas = document.getElementById("cs-map-location-id");
									var mapOptions = {
										center: new google.maps.LatLng(' . $foodbakery_map_latitude . ', ' . $foodbakery_map_longitude . '), 
										zoom: ' . $foodbakery_map_zoom . '
									}
									var map = new google.maps.Map(mapCanvas, mapOptions);
									var marker = new google.maps.Marker({
										position: new google.maps.LatLng(' . $foodbakery_map_latitude . ', ' . $foodbakery_map_longitude . '),
										map: map,
										draggable: true,
										title: \'' . $foodbakery_map_address . '\'
									});
									marker.addListener(\'drag\', markerDragHandleEvent);
									marker.addListener(\'dragend\', markerDragHandleEvent);
									
									google.maps.event.addDomListener(document.getElementById(\'foodbakery_map_t_op_search\'), \'click\', function () {
										geocoder = new google.maps.Geocoder();
										var address = document.getElementById(\'loc_address\').value;
										if(address != ""){
											geocoder.geocode( { \'address\': address}, function(results, status) {
												if (status == google.maps.GeocoderStatus.OK) {
													var newLat = results[0].geometry.location.lat();
													var newLng = results[0].geometry.location.lng();
													document.getElementById(\'foodbakery_post_loc_latitude\').value = newLat;
													document.getElementById(\'foodbakery_post_loc_longitude\').value = newLng;
													new_map_cred(newLat, newLng, map);
												} else {
													alert("Address is not correct.");
													return false;
												}
											});
										} else {
											var newLat = document.getElementById(\'foodbakery_post_loc_latitude\').value;
											var newLng = document.getElementById(\'foodbakery_post_loc_longitude\').value;
											new_map_cred(newLat, newLng, map);
										}
										
									});
								}
								google.maps.event.addDomListener(window, \'load\', map_initialize);
								
								var autocomplete;
								 (function ($) {
									$(function () {
										autocomplete = new google.maps.places.Autocomplete(document.getElementById(\'loc_address\'));
									});
								})(jQuery);
							});	
                        </script>';
                            break;

                        case "banner_fields":
                            $foodbakery_banner_rand_id = rand(23789, 534578930);
                            if (isset($foodbakery_plugin_options) && $foodbakery_plugin_options <> '') {
                                if (!isset($foodbakery_plugin_options['foodbakery_banner_title'])) {
                                    $network_list = '';
                                    $display = 'none';
                                } else {
                                    $network_list = isset($foodbakery_plugin_options['foodbakery_banner_title']) ? $foodbakery_plugin_options['foodbakery_banner_title'] : '';
                                    $banner_style = isset($foodbakery_plugin_options['foodbakery_banner_style']) ? $foodbakery_plugin_options['foodbakery_banner_style'] : '';
                                    $banner_type = isset($foodbakery_plugin_options['foodbakery_banner_type']) ? $foodbakery_plugin_options['foodbakery_banner_type'] : '';
                                    $banner_image = isset($foodbakery_plugin_options['foodbakery_banner_image_array']) ? $foodbakery_plugin_options['foodbakery_banner_image_array'] : '';
                                    $banner_field_url = isset($foodbakery_plugin_options['foodbakery_banner_field_url']) ? $foodbakery_plugin_options['foodbakery_banner_field_url'] : '';
                                    $banner_target = isset($foodbakery_plugin_options['foodbakery_banner_target']) ? $foodbakery_plugin_options['foodbakery_banner_target'] : '';
                                    $adsense_code = isset($foodbakery_plugin_options['foodbakery_banner_adsense_code']) ? $foodbakery_plugin_options['foodbakery_banner_adsense_code'] : '';
                                    $code_no = isset($foodbakery_plugin_options['foodbakery_banner_field_code_no']) ? $foodbakery_plugin_options['foodbakery_banner_field_code_no'] : '';
                                    $display = 'block';
                                }
                            } else {
                                $val = isset($foodbakery_plugin_options['options']) ? $value['options'] : '';
                                $std = isset($foodbakery_plugin_options['id']) ? $value['id'] : '';
                                $display = 'block';
                                $network_list = isset($foodbakery_plugin_options['foodbakery_banner_title']) ? $foodbakery_plugin_options['foodbakery_banner_title'] : '';
                                $banner_style = isset($foodbakery_plugin_options['foodbakery_banner_style']) ? $foodbakery_plugin_options['foodbakery_banner_style'] : '';
                                $banner_type = isset($foodbakery_plugin_options['foodbakery_banner_type']) ? $foodbakery_plugin_options['foodbakery_banner_type'] : '';
                                $banner_image = isset($foodbakery_plugin_options['foodbakery_banner_image_array']) ? $foodbakery_plugin_options['foodbakery_banner_image_array'] : '';
                                $banner_field_url = isset($foodbakery_plugin_options['foodbakery_banner_field_url']) ? $foodbakery_plugin_options['foodbakery_banner_field_url'] : '';
                                $banner_target = isset($foodbakery_plugin_options['foodbakery_banner_target']) ? $foodbakery_plugin_options['foodbakery_banner_target'] : '';
                                $adsense_code = isset($foodbakery_plugin_options['foodbakery_banner_adsense_code']) ? $foodbakery_plugin_options['foodbakery_banner_adsense_code'] : '';
                                $code_no = isset($foodbakery_plugin_options['foodbakery_banner_field_code_no']) ? $foodbakery_plugin_options['foodbakery_banner_field_code_no'] : '';
                            }
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt('foodbakery_banner_title_field'),
                                'desc' => '',
                                'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_title_field_hint'),
                                'field_params' => array(
                                    'std' => '',
                                    'cust_id' => 'banner_title_input',
                                    'cust_name' => 'banner_title_input',
                                    'classes' => '',
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt('foodbakery_banner_style'),
                                'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_style_hint'),
                                'field_params' => array(
                                    'std' => '',
                                    'desc' => '',
                                    'cust_id' => "banner_style_input",
                                    'cust_name' => 'banner_style_input',
                                    'classes' => 'input-small chosen-select',
                                    'options' =>
                                        array(
                                            'top_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_top'),
                                            'bottom_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_bottom'),
                                            'sidebar_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_sidebar'),
                                            'vertical_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_vertical'),
                                            'restaurant_detail_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_restaurant_detail'),
                                            'restaurant_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_restaurant'),
                                            'restaurant_banner_leftfilter' => foodbakery_plugin_text_srt('foodbakery_banner_type_restaurant_leftfilter'),
                                        ),
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt('foodbakery_banner_type'),
                                'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_type_hint'),
                                'field_params' => array(
                                    'std' => '',
                                    'desc' => '',
                                    'cust_id' => "banner_type_input",
                                    'cust_name' => 'banner_type_input',
                                    'classes' => 'input-small chosen-select',
                                    'extra_atr' => 'onchange="javascript:foodbakery_banner_type_toggle(this.value , \'' . $foodbakery_banner_rand_id . '\')"',
                                    'options' =>
                                        array(
                                            'image' => foodbakery_plugin_text_srt('foodbakery_banner_image'),
                                            'code' => foodbakery_plugin_text_srt('foodbakery_banner_code'),
                                        ),
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            $output .= '<div id="ads_image' . absint($foodbakery_banner_rand_id) . '">';
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt('foodbakery_banner_image'),
                                'id' => 'banner_field_image',
                                'std' => '',
                                'desc' => '',
                                'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_image_hint'),
                                'prefix' => '',
                                'field_params' => array(
                                    'std' => '',
                                    'id' => 'banner_field_image',
                                    'prefix' => '',
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
                            $output .= '</div>';
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt('foodbakery_banner_url_field'),
                                'desc' => '',
                                'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_url_hint'),
                                'field_params' => array(
                                    'std' => '',
                                    'cust_id' => 'banner_field_url_input',
                                    'cust_name' => 'banner_field_url_input',
                                    'classes' => '',
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt('foodbakery_banner_target'),
                                'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_target_hint'),
                                'field_params' => array(
                                    'std' => '',
                                    'desc' => '',
                                    'cust_id' => "banner_target_input",
                                    'cust_name' => 'banner_target_input',
                                    'classes' => 'input-small chosen-select',
                                    'options' =>
                                        array(
                                            '_self' => foodbakery_plugin_text_srt('foodbakery_banner_target_self'),
                                            '_blank' => foodbakery_plugin_text_srt('foodbakery_banner_target_blank'),
                                        ),
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            $output .= '<div id="ads_code' . absint($foodbakery_banner_rand_id) . '" style="display:none">';
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt('foodbakery_banner_ad_sense_code'),
                                'desc' => '',
                                'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_ad_sense_code_hint'),
                                'field_params' => array(
                                    'std' => '',
                                    'cust_id' => 'adsense_code_input',
                                    'cust_name' => 'adsense_code_input[]',
                                    'classes' => '',
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
                            $output .= '</div>';
                            $foodbakery_opt_array = array(
                                'name' => '&nbsp;',
                                'desc' => '',
                                'hint_text' => '',
                                'field_params' => array(
                                    'std' => esc_html__('Add Banner', 'foodbakery'),
                                    'id' => 'foodbakery_banner_add_banner',
                                    'classes' => '',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:foodbakery_banner_add_banner(\'' . admin_url("admin-ajax.php") . '\')"',
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            $output .= '
				    <div class="social-area" style="display:' . $display . '">
					<div class="theme-help">
					    <h4 style="padding-bottom:0px;">' . foodbakery_plugin_text_srt('foodbakery_banner_already_added') . '</h4>
						<div class="clear"></div>
						</div>
					    <div class="boxes">
					<table class="to-table" border="0" cellspacing="0">
				    <thead>
				    <tr>                          
                                    <th>' . foodbakery_plugin_text_srt('foodbakery_banner_table_title') . '</th>
					    <th>' . foodbakery_plugin_text_srt('foodbakery_banner_table_style') . '</th>
					    <th>' . foodbakery_plugin_text_srt('foodbakery_banner_table_image') . '</th>
					    <th>' . foodbakery_plugin_text_srt('foodbakery_banner_table_clicks') . '</th>
					    <th>' . foodbakery_plugin_text_srt('foodbakery_banner_table_shortcode') . '</th>
					    <th class="centr">' . foodbakery_plugin_text_srt('foodbakery_banner_actions') . '</th>
						</tr>
						</thead>
						<tbody id="banner_area">';
                            $i = 0;
                            if (is_array($network_list)) {
                                foreach ($network_list as $network) {
                                    if (isset($network_list[$i]) || isset($network_list[$i])) {
                                        $foodbakery_rand_num = rand(123456, 987654);
                                        $output .= '<tr id="del_' . $foodbakery_rand_num . '">';
                                        $output .= '<td>' . esc_html($network_list[$i]) . '</td>';
                                        $output .= '<td>' . esc_html($banner_style[$i]) . '</td>';
                                        if (isset($banner_image[$i]) && !empty($banner_image[$i]) && $banner_type[$i] == 'image') {
                                            $img_url = wp_get_attachment_image_src($banner_image[$i]);
                                            $output .= '<td><img src="' . esc_url($img_url[0]) . '" alt="" height="70" /></td>';
                                        } else {
                                            $output .= '<td>' . foodbakery_plugin_text_srt('foodbakery_banner_custom_code') . '</td>';
                                        }
                                        if ($banner_type[$i] == 'image') {
                                            $banner_click_count = get_option("banner_clicks_" . $code_no[$i]);
                                            $banner_click_count = $banner_click_count <> '' ? $banner_click_count : '0';
                                            $output .= '<td>' . $banner_click_count . '</td>';
                                        } else {
                                            $output .= '<td>&nbsp;</td>';
                                        }
                                        $output .= '<td>[foodbakery_banner_ads id="' . $code_no[$i] . '"]</td>';
                                        $output .= '
                                          <td class="centr">
                                          <a class="remove-btn" onclick="javascript:return confirm(\'' . foodbakery_plugin_text_srt('foodbakery_banner_alert_msg') . '\')" href="javascript:ads_del(\'' . $foodbakery_rand_num . '\')" data-toggle="tooltip" data-placement="top" title="' . foodbakery_plugin_text_srt('foodbakery_banner_remove') . '">
                                          <i class="icon-times"></i></a>
                                          <a href="javascript:foodbakery_banner_toggle(\'' . absint($foodbakery_rand_num) . '\')" data-toggle="tooltip" data-placement="top" title="' . foodbakery_plugin_text_srt('foodbakery_banner_edit') . '">
                                          <i class="icon-edit3"></i>
                                          </a>
                                          </td>
                                          </tr>';
                                        $output .= '
                                          <tr id="' . absint($foodbakery_rand_num) . '" style="display:none">
                                          <td colspan="3">
                                          <div class="form-elements">
                                          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"></div>
                                          <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                          <a class="cs-remove-btn" onclick="foodbakery_banner_toggle(\'' . $foodbakery_rand_num . '\')"><i class="icon-times"></i></a>
                                          </div>
                                          </div>';
                                        $foodbakery_opt_array = array(
                                            'name' => foodbakery_plugin_text_srt('foodbakery_banner_title_field'),
                                            'desc' => '',
                                            'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_title_field_hint'),
                                            'field_params' => array(
                                                'std' => isset($network_list[$i]) ? $network_list[$i] : '',
                                                'cust_id' => 'banner_title',
                                                'cust_name' => 'foodbakery_banner_title[]',
                                                'classes' => '',
                                                'return' => true,
                                            ),
                                        );
                                        $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                                        $foodbakery_opt_array = array(
                                            'name' => foodbakery_plugin_text_srt('foodbakery_banner_style'),
                                            'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_style_hint'),
                                            'field_params' => array(
                                                'std' => isset($banner_style[$i]) ? $banner_style[$i] : '',
                                                'cust_id' => 'banner_style',
                                                'cust_name' => 'foodbakery_banner_style[]',
                                                'desc' => '',
                                                'classes' => 'input-small chosen-select',
                                                'options' =>
                                                    array(
                                                        'top_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_top'),
                                                        'bottom_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_bottom'),
                                                        'sidebar_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_sidebar'),
                                                        'vertical_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_vertical'),
                                                        'restaurant_detail_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_restaurant_detail'),
                                                        'restaurant_banner' => foodbakery_plugin_text_srt('foodbakery_banner_type_restaurant'),
                                                        'restaurant_banner_leftfilter' => foodbakery_plugin_text_srt('foodbakery_banner_type_restaurant_leftfilter'),
                                                    ),
                                                'return' => true,
                                            ),
                                        );
                                        $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                                        $foodbakery_opt_array = array(
                                            'name' => foodbakery_plugin_text_srt('foodbakery_banner_type'),
                                            'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_type_hint'),
                                            'field_params' => array(
                                                'std' => isset($banner_type[$i]) ? $banner_type[$i] : '',
                                                'cust_id' => 'banner_type',
                                                'cust_name' => 'foodbakery_banner_type[]',
                                                'desc' => '',
                                                'extra_atr' => 'onchange="javascript:foodbakery_banner_type_toggle(this.value , \'' . $foodbakery_rand_num . '\')"',
                                                'classes' => 'input-small chosen-select',
                                                'options' =>
                                                    array(
                                                        'image' => foodbakery_plugin_text_srt('foodbakery_banner_image'),
                                                        'code' => foodbakery_plugin_text_srt('foodbakery_banner_code'),
                                                    ),
                                                'return' => true,
                                            ),
                                        );
                                        $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                                        $display_ads = 'none';
                                        if ($banner_type[$i] == 'image') {
                                            $display_ads = 'block';
                                        } elseif ($banner_type[$i] == 'code') {
                                            $display_ads = 'none';
                                        }
                                        $output .= '<div id="ads_image' . absint($foodbakery_rand_num) . '" style="display:' . esc_html($display_ads) . '">';
                                        $foodbakery_opt_array = array(
                                            'name' => foodbakery_plugin_text_srt('foodbakery_banner_image'),
                                            'id' => 'banner_image',
                                            'std' => isset($banner_image[$i]) ? $banner_image[$i] : '',
                                            'desc' => '',
                                            'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_image_hint'),
                                            'prefix' => '',
                                            'array' => true,
                                            'field_params' => array(
                                                'std' => isset($banner_image[$i]) ? $banner_image[$i] : '',
                                                'id' => 'banner_image',
                                                'prefix' => '',
                                                'array' => true,
                                                'return' => true,
                                            ),
                                        );

                                        $output .= $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
                                        $output .= '</div>';
                                        $foodbakery_opt_array = array(
                                            'name' => foodbakery_plugin_text_srt('foodbakery_banner_url_field'),
                                            'desc' => '',
                                            'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_url_hint'),
                                            'field_params' => array(
                                                'std' => isset($banner_field_url[$i]) ? $banner_field_url[$i] : '',
                                                'cust_id' => 'banner_field_url',
                                                'cust_name' => 'foodbakery_banner_field_url[]',
                                                'classes' => '',
                                                'return' => true,
                                            ),
                                        );
                                        $output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                                        $foodbakery_opt_array = array(
                                            'name' => foodbakery_plugin_text_srt('foodbakery_banner_target'),
                                            'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_target_hint'),
                                            'field_params' => array(
                                                'desc' => '',
                                                'std' => isset($banner_target[$i]) ? $banner_target[$i] : '',
                                                'cust_id' => 'banner_target',
                                                'cust_name' => 'foodbakery_banner_target[]',
                                                'classes' => 'input-small chosen-select',
                                                'options' =>
                                                    array(
                                                        '_self' => foodbakery_plugin_text_srt('foodbakery_banner_target_self'),
                                                        '_blank' => foodbakery_plugin_text_srt('foodbakery_banner_target_blank'),
                                                    ),
                                                'return' => true,
                                            ),
                                        );
                                        $output .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                                        $display_ads = 'none';
                                        if ($banner_type[$i] == 'image') {
                                            $display_ads = 'none';
                                        } elseif ($banner_type[$i] == 'code') {
                                            $display_ads = 'block';
                                        }
                                        $output .= '<div id="ads_code' . absint($foodbakery_rand_num) . '" style="display:' . esc_html($display_ads) . '">';
                                        $foodbakery_opt_array = array(
                                            'name' => foodbakery_plugin_text_srt('foodbakery_banner_ad_sense_code'),
                                            'desc' => '',
                                            'hint_text' => foodbakery_plugin_text_srt('foodbakery_banner_ad_sense_code_hint'),
                                            'field_params' => array(
                                                'std' => isset($adsense_code[$i]) ? $adsense_code[$i] : '',
                                                'cust_id' => 'adsense_code',
                                                'cust_name' => 'foodbakery_banner_adsense_code[]',
                                                'classes' => '',
                                                'return' => true,
                                            ),
                                        );
                                        $output .= $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
                                        $output .= '</div>';

                                        $foodbakery_opt_array = array(
                                            'std' => isset($code_no[$i]) ? $code_no[$i] : '',
                                            'id' => 'banner_field_code_no',
                                            'cust_name' => 'foodbakery_banner_field_code_no[]',
                                            'return' => true,
                                        );
                                        $output .= $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);

                                        $output .= '
					    </td>
					</tr>';
                                    }
                                    $i++;
                                }
                            }

                            $output .= '</tbody></table></div></div>';
                            break;
                        case 'generate_backup':
                            global $wp_filesystem;
                            $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
                            if (false === ($creds = request_filesystem_credentials($backup_url, '', false, false, array()))) {
                                return true;
                            }
                            if (!WP_Filesystem($creds)) {
                                request_filesystem_credentials($backup_url, '', true, false, array());
                                return true;
                            }
                            $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/';
                            $foodbakery_upload_dir_path = wp_foodbakery::plugin_url() . 'backend/settings/backups/';
                            $foodbakery_all_list = $wp_filesystem->dirlist($foodbakery_upload_dir);
                            $output .= '<div class="backup_generates_area" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';
                            $output .= '
						<div class="theme-help">
								<h4>' . esc_html__('Import Options', "foodbakery") . '</h4>
						</div>';

                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                    'name' => esc_html__("File URL", 'foodbakery'),
                                    'hint_text' => esc_html__('Input the Url from another location and hit Import Button to apply settings.', "foodbakery"),
                                )
                            );
                            $output .= '<div  class="external_backup_areas">';
                            $foodbakery_opt_array = array(
                                'std' => '',
                                'cust_id' => "bkup_import_url",
                                'cust_name' => '',
                                'classes' => 'input-medium',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Import', 'jobs'),
                                'cust_id' => "cs-p-backup-url-restore",
                                'cust_name' => '',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '</div>';
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                    'desc' => '',
                                )
                            );
                            $output .= '<div class="theme-help">
                                            <h4>' . esc_html__('Export Options', "foodbakery") . '</h4>
                                    </div>';
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                    'name' => esc_html__("Generated Files", 'foodbakery'),
                                    'hint_text' => esc_html__('Here you can Generate/Download Backups. Also you can use these Backups to Restore settings.', "foodbakery"),
                                )
                            );
                            if (is_array($foodbakery_all_list) && sizeof($foodbakery_all_list) > 0) {
                                $foodbakery_list_count = 1;
                                $bk_options = '';
                                foreach ($foodbakery_all_list as $file_key => $file_val) {
                                    if (isset($file_val['name'])) {
                                        $foodbakery_slected = sizeof($foodbakery_all_list) == $foodbakery_list_count ? ' selected="selected"' : '';
                                        if (isset($file_val['name']) && strpos($file_val['name'], '.json')) {
                                            $bk_options .= '<option' . $foodbakery_slected . '>' . $file_val['name'] . '</option>';
                                        }
                                    }
                                    $foodbakery_list_count++;
                                }
                                $foodbakery_opt_array = array(
                                    'std' => esc_html__('Import', 'foodbakery'),
                                    'cust_id' => "",
                                    'cust_name' => '',
                                    'classes' => 'input-medium chosen-select-no-single',
                                    'extra_atr' => ' onchange="foodbakery_set_p_filename(this.value, \'' . esc_url($foodbakery_upload_dir_path) . '\')"',
                                    'options_markup' => true,
                                    'options' => $bk_options,
                                    'return' => true,
                                );
                                $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                                $output .= '<div class="backup_action_btns">';
                                if (isset($file_val['name'])) {
                                    $foodbakery_opt_array = array(
                                        'std' => esc_html__('Restore', 'foodbakery'),
                                        'cust_id' => "cs-p-backup-restore",
                                        'cust_name' => '',
                                        'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                        'cust_type' => 'button',
                                        'return' => true,
                                    );
                                    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                                    $output .= '<a download="' . $file_val['name'] . '" href="' . esc_url($foodbakery_upload_dir_path . $file_val['name']) . '">' . esc_html__('Download', "foodbakery") . '</a>';
                                    $foodbakery_opt_array = array(
                                        'std' => esc_html__('Delete', 'jobs'),
                                        'cust_id' => "cs-p-backup-delte",
                                        'cust_name' => '',
                                        'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                        'cust_type' => 'button',
                                        'return' => true,
                                    );
                                    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                                }
                                $output .= '</div>';
                                $output .= '<div>&nbsp;</div>';
                            }
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Generate Backup', 'foodbakery'),
                                'cust_id' => "cs-p-bkp",
                                'cust_name' => '',
                                'extra_atr' => ' onclick="javascript:foodbakery_pl_opt_backup_generate(\'' . esc_js(admin_url('admin-ajax.php')) . '\');"',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                    'desc' => '',
                                )
                            );
                            $output .= '</div>';
                            break;
                        case 'backup_locations':
                            global $wp_filesystem;
                            $backup_url = wp_nonce_url('admin.php?page=foodbakery_settings');
                            if (false === ($creds = request_filesystem_credentials($backup_url, '', false, false, array()))) {
                                return true;
                            }
                            if (!WP_Filesystem($creds)) {
                                request_filesystem_credentials($backup_url, '', true, false, array());
                                return true;
                            }
                            $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/locations/';
                            $foodbakery_upload_dir_path = wp_foodbakery::plugin_url() . 'backend/settings/backups/locations/';
                            $foodbakery_all_list = $wp_filesystem->dirlist($foodbakery_upload_dir);
                            $output .= '<div class="backup_locations_generates_area" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';
                            $output .= '
                                    <div class="theme-help">
                                            <h4>' . esc_html__('Import/Export Locations', 'foodbakery') . '</h4>
                                    </div>';

                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("File URL", 'foodbakery'),
                                'hint_text' => esc_html__('Input the Url from another location and hit Import Button to import locations.', 'foodbakery'),
                            ));

                            $output .= '<div  class="external_backup_areas">';
                            $foodbakery_opt_array = array(
                                'std' => '',
                                'cust_id' => "bkup_locations_import_url",
                                'cust_name' => '',
                                'classes' => 'input-medium',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Import Locations', 'foodbakery'),
                                'cust_id' => "btn_import_locations_from_url",
                                'cust_name' => '',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '</div>';
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Generated Files", 'foodbakery'),
                                'hint_text' => esc_html__('Here you can Generate/Download Backups. Also you can use these Backups to Restore Locations.', 'foodbakery'),
                            ));
                            if (is_array($foodbakery_all_list) && count($foodbakery_all_list) > 0) {
                                $foodbakery_list_count = 1;
                                $bk_options = '';
                                foreach ($foodbakery_all_list as $file_key => $file_val) {
                                    if (isset($file_val['name'])) {
                                        $foodbakery_slected = sizeof($foodbakery_all_list) == $foodbakery_list_count ? ' selected="selected"' : '';
                                        $bk_options .= '<option' . $foodbakery_slected . '>' . $file_val['name'] . '</option>';
                                    }
                                    $foodbakery_list_count++;
                                }
                                $foodbakery_opt_array = array(
                                    'std' => esc_html__('Import', 'foodbakery'),
                                    'cust_id' => "slct_locations_backups",
                                    'cust_name' => '',
                                    'name' => '',
                                    'classes' => 'input-medium chosen-select-no-single',
                                    'extra_atr' => ' onchange="set_locations_backup_filename(this.value, \'' . esc_url($foodbakery_upload_dir_path) . '\')"',
                                    'options_markup' => true,
                                    'options' => $bk_options,
                                    'return' => true,
                                );
                                $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                                $output .= '<div class="backup_action_btns">';
                                if (isset($file_val['name'])) {
                                    $foodbakery_opt_array = array(
                                        'std' => esc_html__('Restore', 'foodbakery'),
                                        'cust_id' => "btn_restore_locations_backup",
                                        'cust_name' => '',
                                        'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                        'cust_type' => 'button',
                                        'return' => true,
                                    );
                                    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                                    $output .= '<a download="' . $file_val['name'] . '" href="' . esc_url($foodbakery_upload_dir_path . $file_val['name']) . '">' . esc_html__('Download', 'foodbakery') . '</a>';
                                    $foodbakery_opt_array = array(
                                        'std' => esc_html__('Delete', 'foodbakery'),
                                        'cust_id' => "btn_delete_locations_backup",
                                        'cust_name' => '',
                                        'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                        'cust_type' => 'button',
                                        'return' => true,
                                    );
                                    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                                }
                                $output .= '</div>';
                                $output .= '<div>&nbsp;</div>';
                            }
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Generate Backup', 'foodbakery'),
                                'cust_id' => "btn_generate_locations_backup",
                                'cust_name' => '',
                                'extra_atr' => ' onclick="javascript:generate_locations_backup(\'' . esc_js(admin_url('admin-ajax.php')) . '\');"',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '<form method="post" id="foodbakery_import_form" action="" enctype="multipart/form-data">';
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Browse file', 'foodbakery'),
                                'cust_id' => "btn_browse_locations_file",
                                'cust_name' => '',
                                'id' => "btn_browse_locations_file",
                                'cust_type' => 'file',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Import', 'foodbakery'),
                                'cust_id' => "btn_import_file",
                                'cust_name' => 'btn_import_file',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '</form>';
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= '</div>';

                            break;
                        case 'backup_restaurant_type_categories':
                            global $wp_filesystem;
                            $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
                            if (false === ($creds = request_filesystem_credentials($backup_url, '', false, false, array()))) {
                                return true;
                            }
                            if (!WP_Filesystem($creds)) {
                                request_filesystem_credentials($backup_url, '', true, false, array());
                                return true;
                            }
                            $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/restaurant-type-categories/';
                            $foodbakery_upload_dir_path = wp_foodbakery::plugin_url() . 'backend/settings/backups/restaurant-type-categories/';
                            $foodbakery_all_list = $wp_filesystem->dirlist($foodbakery_upload_dir);
                            $output .= '<div class="backup_restaurant_type_categories_generates_area" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';
                            $output .= '
                                    <div class="theme-help">
                                            <h4>' . esc_html__('Import/Export Restaurant Type Categories', 'foodbakery') . '</h4>
                                    </div>';

                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("File URL", 'foodbakery'),
                                'hint_text' => esc_html__('Input the URL from another location and hit Import Button to import restaurant type categories.', 'foodbakery'),
                            ));
                            $output .= '<div  class="external_backup_areas">';
                            $foodbakery_opt_array = array(
                                'std' => '',
                                'cust_id' => "bkup_restaurant_type_categories_import_url",
                                'cust_name' => '',
                                'classes' => 'input-medium',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Import Restaurant Type Categories', 'foodbakery'),
                                'cust_id' => "btn_import_restaurant_type_categories_from_url",
                                'cust_name' => '',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '</div>';
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Generated Files", 'foodbakery'),
                                'hint_text' => esc_html__('Here you can Generate/Download Backups. Also you can use these Backups to Restore Restaurant type categories.', 'foodbakery'),
                            ));
                            if (is_array($foodbakery_all_list) && count($foodbakery_all_list) > 0) {
                                $foodbakery_list_count = 1;
                                $bk_options = '';
                                foreach ($foodbakery_all_list as $file_key => $file_val) {
                                    if (isset($file_val['name'])) {
                                        $foodbakery_slected = sizeof($foodbakery_all_list) == $foodbakery_list_count ? ' selected="selected"' : '';
                                        $bk_options .= '<option' . $foodbakery_slected . '>' . $file_val['name'] . '</option>';
                                    }
                                    $foodbakery_list_count++;
                                }
                                $foodbakery_opt_array = array(
                                    'std' => esc_html__('Import', 'foodbakery'),
                                    'cust_id' => "slct_restaurant_type_categories_backups",
                                    'cust_name' => '',
                                    'name' => '',
                                    'classes' => 'input-medium chosen-select-no-single',
                                    'extra_atr' => ' onchange="set_restaurant_type_categories_backup_filename(this.value, \'' . esc_url($foodbakery_upload_dir_path) . '\')"',
                                    'options_markup' => true,
                                    'options' => $bk_options,
                                    'return' => true,
                                );
                                $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                                $output .= '<div class="backup_action_btns">';
                                if (isset($file_val['name'])) {
                                    $foodbakery_opt_array = array(
                                        'std' => esc_html__('Restore', 'foodbakery'),
                                        'cust_id' => "btn_restore_restaurant_type_categories_backup",
                                        'cust_name' => '',
                                        'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                        'cust_type' => 'button',
                                        'return' => true,
                                    );
                                    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                                    $output .= '<a download="' . $file_val['name'] . '" href="' . esc_url($foodbakery_upload_dir_path . $file_val['name']) . '">' . esc_html__('Download', 'foodbakery') . '</a>';
                                    $foodbakery_opt_array = array(
                                        'std' => esc_html__('Delete', 'foodbakery'),
                                        'cust_id' => "btn_delete_restaurant_type_categories_backup",
                                        'cust_name' => '',
                                        'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                        'cust_type' => 'button',
                                        'return' => true,
                                    );
                                    $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                                }
                                $output .= '</div>';
                                $output .= '<div>&nbsp;</div>';
                            }
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Generate Backup', 'foodbakery'),
                                'cust_id' => "btn_generate_restaurant_type_categories_backup",
                                'cust_name' => '',
                                'extra_atr' => ' onclick="javascript:generate_restaurant_type_categories_backup(\'' . esc_js(admin_url('admin-ajax.php')) . '\');"',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '<form method="post" id="foodbakery_import_categort_form" action="" enctype="multipart/form-data">';
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Browse file', 'foodbakery'),
                                'cust_id' => "btn_browse_category_file",
                                'cust_name' => '',
                                'id' => "btn_browse_category_file",
                                'cust_type' => 'file',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Import', 'foodbakery'),
                                'cust_id' => "btn_import_cat_file",
                                'cust_name' => 'btn_import_cat_file',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '</form>';
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= '</div>';
                            break;
                        case 'locations_level_selector':
                            $foodbakery_opt_array = array(
                                'name' => 'Warning!!!',
                                'std' => 'By modifying location levels your existing locations data may get useless as you change levels. So, it is recommended to backup and delete existing locations.',
                                'id' => 'foodbakery_locations_levels_warning',
                            );
                            $output .= $foodbakery_html_fields->foodbakery_set_announcement($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Locations Levels", 'foodbakery'),
                                'hint_text' => esc_html__('Select the levels you want to use for locations.', 'foodbakery'),
                            ));

                            $location_levels_array = array(
                                'country' => esc_html__('Country', 'foodbakery'),
                                'state' => esc_html__('State', 'foodbakery'),
                                'city' => esc_html__('City', 'foodbakery'),
                                'town' => esc_html__('Town', 'foodbakery'),
                            );
                            $flag = apply_filters('foodbakery_add_county_in_location_level', false);
                            if ($flag) {
                                $location_levels_array = array(
                                    'country' => esc_html__('Country', 'foodbakery'),
                                    'state' => esc_html__('State', 'foodbakery'),
                                    'county' => esc_html__('County', 'foodbakery'),
                                    'city' => esc_html__('City', 'foodbakery'),
                                    'town' => esc_html__('Town', 'foodbakery'),
                                );
                            }
                            $foodbakery_opt_array = array('name' => esc_html__('Locations Levels', 'foodbakery'),
                                'desc' => '',
                                'id' => 'locations_levels',
                                'cust_name' => 'foodbakery_locations_levels[]',
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'extra_atr' => ' multiple disabled',
                                'options' => $location_levels_array,
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Edit Levels', 'jobs'),
                                'cust_id' => "foodbakery_edit_locations_levels",
                                'cust_name' => '',
                                'cust_type' => 'button',
                                'extra_atr' => ' style="margin-top: 10px;"',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $locations_levels = array();
                            if (isset($foodbakery_plugin_options['foodbakery_locations_levels'])) {
                                $locations_levels = $foodbakery_plugin_options['foodbakery_locations_levels'];
                            }
                            ob_start();
                            ?>
                            <script type="text/javascript">
                                "use strict";
                                (function ($) {
                                    $(function () {
                                        var locations_levels = <?php echo json_encode($locations_levels); ?>;
                                        var selecter_id = "#foodbakery_locations_levels";
                                        // Select locations levels.
                                        $(selecter_id + " option").each(function (key, elem) {
                                            var val = $(this).val();
                                            if ($.inArray(val, locations_levels) > -1) {
                                                $(this).prop('selected', true);
                                            } else {
                                                $(this).prop('selected', false);
                                            }
                                        });
                                        $(selecter_id).trigger("chosen:updated");

                                        $(selecter_id).prop('disabled', true).change(function () {
                                            // This is done for sorting of items in an order.
                                            $(selecter_id).trigger("chosen:updated");
                                        });
                                        $("#foodbakery_edit_locations_levels").click(function () {
                                            if (window.confirm("Warning!!!\nBy modifying location levels your existing locations data may get useless as you change levels. So, it is recommended to backup and delete existing locations. Do you still want to edit levels?")) {
                                                $(selecter_id).prop('disabled', false).trigger("chosen:updated");
                                            }
                                        });

                                        $("#plugin-options").on("submit", function () {
                                            $(selecter_id).prop('disabled', false);
                                            return true;
                                        });
                                    });
                                })(jQuery);
                            </script>
                            <?php
                            $output .= ob_get_clean();
                            break;
                        case 'locations_fields_selector':
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Location's Fields Selector", 'foodbakery'),
                                'hint_text' => esc_html__('Select which location parts(Country, State, City, Town) you want to use on frontend. You can select only from location parts those you have selected on backend.', 'foodbakery'),
                            ));
                            $locations_levels = array();
                            $options = $tempOptions = array(
                                'country' => esc_html__('Country', 'foodbakery'),
                                'state' => esc_html__('State', 'foodbakery'),
                                'city' => esc_html__('City', 'foodbakery'),
                                'town' => esc_html__('Town', 'foodbakery'),
                            );
                            $flag = apply_filters('foodbakery_add_county_in_location_level', false);
                            if ($flag) {
                                $options = $tempOptions = array(
                                    'country' => esc_html__('Country', 'foodbakery'),
                                    'state' => esc_html__('State', 'foodbakery'),
                                    'county' => esc_html__('County', 'foodbakery'),
                                    'city' => esc_html__('City', 'foodbakery'),
                                    'town' => esc_html__('Town', 'foodbakery'),
                                );
                                $foodbakery_plugin_options['foodbakery_locations_levels'] = unserialize(get_option('foodbakery_locations_levels'));
                            }
                            $locations_levels = array();

                            if (isset($foodbakery_plugin_options['foodbakery_locations_levels'])) {
                                $options = array();
                                $locations_levels = $foodbakery_plugin_options['foodbakery_locations_levels'];
                                foreach ($locations_levels as $key => $val) {
                                    if (isset($tempOptions[$val])) {
                                        $options[$val] = ucfirst($tempOptions[$val]);
                                    }
                                }
                            }

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('', 'foodbakery'),
                                'desc' => '',
                                'id' => $value['id'],
                                'cust_name' => $value['cust_name'],
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'extra_atr' => ' multiple',
                                'options' => $options,
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            /*
                             * Get data from plugin options to populate on frontend.
                             */
                            $frontend_location_parts = array();
                            if (isset($foodbakery_plugin_options[$value['id']])) {
                                $frontend_location_parts = $foodbakery_plugin_options[$value['id']];
                            }
                            ob_start();
                            ?>
                            <script type="text/javascript">
                                "use strict";
                                (function ($) {
                                    $(function () {
                                        var <?php echo esc_html($value['id']); ?> = <?php echo json_encode($frontend_location_parts); ?>;
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>").change(function () {
                                            // For sorting items in an order.
                                            $("#foodbakery_<?php echo esc_html($value['id']); ?>").trigger("chosen:updated");
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?> option").each(function (key, elem) {
                                            var val = $(this).val();
                                            if ($.inArray(val, <?php echo esc_html($value['id']); ?>) > -1) {
                                                $(this).prop('selected', true);
                                            } else {
                                                $(this).prop('selected', false);
                                            }
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>").trigger("chosen:updated");
                                    });
                                })(jQuery);
                            </script>
                            <?php
                            $output .= ob_get_clean();
                            break;
                        case 'locations_fields_for_search':
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Frontend Location Parts", 'foodbakery'),
                                'hint_text' => esc_html__('Select which location parts(Country, State, City, Town) you want to use on frontend. You can select only from location parts those you have selected on backend.', 'foodbakery'),
                            ));
                            $locations_levels = array();
                            $options = $tempOptions = array(
                                'country' => esc_html__('Country', 'foodbakery'),
                                'state' => esc_html__('State', 'foodbakery'),
                                'city' => esc_html__('City', 'foodbakery'),
                                'town' => esc_html__('Town', 'foodbakery'),
                            );
                            $flag = apply_filters('foodbakery_add_county_in_location_level', false);
                            if ($flag) {
                                $options = $tempOptions = array(
                                    'country' => esc_html__('Country', 'foodbakery'),
                                    'state' => esc_html__('State', 'foodbakery'),
                                    'county' => esc_html__('County', 'foodbakery'),
                                    'city' => esc_html__('City', 'foodbakery'),
                                    'town' => esc_html__('Town', 'foodbakery'),
                                );
                                $foodbakery_plugin_options['foodbakery_locations_levels'] = unserialize(get_option('foodbakery_locations_levels'));
                            }
                            $locations_levels = array();
                            if (isset($foodbakery_plugin_options['foodbakery_locations_levels'])) {
                                $options = array();
                                $locations_levels = $foodbakery_plugin_options['foodbakery_locations_levels'];
                                foreach ($locations_levels as $key => $val) {
                                    if (isset($tempOptions[$val])) {
                                        $options[$val] = ucfirst($tempOptions[$val]);
                                    }
                                }
                            }
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('', 'foodbakery'),
                                'desc' => '',
                                'id' => $value['id'],
                                'cust_name' => $value['cust_name'],
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'extra_atr' => ' multiple',
                                'options' => $options,
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Country", 'foodbakery'),
                                'hint_text' => esc_html__('Select a Country which you want to use in locations or select "All".', 'foodbakery'),
                                'id' => $value['id'] . '_country_container',
                            ));
                            $output .= '<span style="display: none;" class="ajax-loader"><img src="' . wp_foodbakery::plugin_url() . '/assets/images/ajax-loader.gif" /></span>';
                            $foodbakery_opt_array = array('name' => esc_html__('Country', 'foodbakery'),
                                'desc' => '',
                                'id' => $value['id'] . '_country',
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'options' => array(),
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("State", 'foodbakery'),
                                'hint_text' => esc_html__('Select a State which you want to use in locations or select "All".', 'foodbakery'),
                                'id' => $value['id'] . '_state_container',
                            ));
                            $output .= '<span style="display: none;" class="ajax-loader"><img src="' . wp_foodbakery::plugin_url() . '/assets/images/ajax-loader.gif" /></span>';
                            $foodbakery_opt_array = array('name' => esc_html__('State', 'foodbakery'),
                                'desc' => '',
                                'id' => $value['id'] . '_state',
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'options' => array(),
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("City", 'foodbakery'),
                                'hint_text' => esc_html__('Select a City which you want to use in locations or select "All".', 'foodbakery'),
                                'id' => $value['id'] . '_city_container',
                            ));
                            $output .= '<span style="display: none;" class="ajax-loader"><img src="' . wp_foodbakery::plugin_url() . '/assets/images/ajax-loader.gif" /></span>';
                            $foodbakery_opt_array = array('name' => esc_html__('City', 'foodbakery'),
                                'desc' => '',
                                'id' => $value['id'] . '_city',
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'options' => array(),
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));
                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                'name' => esc_html__("Town", 'foodbakery'),
                                'hint_text' => esc_html__('Select a Town which you want to use in locations or select "All".', 'foodbakery'),
                                'id' => $value['id'] . '_town_container',
                            ));
                            $output .= '<span style="display: none;" class="ajax-loader"><img src="' . wp_foodbakery::plugin_url() . '/assets/images/ajax-loader.gif" /></span>';
                            $foodbakery_opt_array = array('name' => esc_html__('Town', 'foodbakery'),
                                'desc' => '',
                                'id' => $value['id'] . '_town',
                                'std' => '',
                                'type' => 'select_values',
                                'classes' => 'chosen-select-no-single',
                                'options' => array(),
                                'return' => true,
                            );
                            $output .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                'desc' => '',
                            ));

                            /*
                             * Get data from plugin options to populate on frontend.
                             */
                            $frontend_location_parts = array();
                            if (isset($foodbakery_plugin_options[$value['id']])) {
                                $frontend_location_parts = $foodbakery_plugin_options[$value['id']];
                            }
                            $var_name_country = 'foodbakery_' . $value['id'] . '_country';
                            $$var_name_country = '';
                            if (isset($foodbakery_plugin_options[$var_name_country])) {
                                $$var_name_country = $foodbakery_plugin_options[$var_name_country];
                            }
                            $var_name_state = 'foodbakery_' . $value['id'] . '_state';
                            $$var_name_state = '';
                            if (isset($foodbakery_plugin_options[$var_name_state])) {
                                $$var_name_state = $foodbakery_plugin_options[$var_name_state];
                            }

                            $var_name_city = 'foodbakery_' . $value['id'] . '_city';
                            $$var_name_city = '';
                            if (isset($foodbakery_plugin_options[$var_name_city])) {
                                $$var_name_city = $foodbakery_plugin_options[$var_name_city];
                            }
                            $var_name_town = 'foodbakery_' . $value['id'] . '_town';
                            $$var_name_town = '';
                            if (isset($foodbakery_plugin_options[$var_name_town])) {
                                $$var_name_town = $foodbakery_plugin_options[$var_name_town];
                            }
                            ob_start();
                            ?>
                            <script type="text/javascript">
                                "use strict";
                                (function ($) {
                                    $(function () {
                                        var <?php echo esc_html($value['id']); ?> = <?php echo json_encode($frontend_location_parts); ?>;
                                        var locations_levels = <?php echo json_encode($locations_levels); ?>;
                                        var pre_data = {
                                            <?php echo esc_html($var_name_country); ?>_id: '<?php echo esc_html($$var_name_country); ?>',
                                            <?php echo esc_html($var_name_state); ?>_id: '<?php echo esc_html($$var_name_state); ?>',
                                            <?php echo esc_html($var_name_city); ?>_id: '<?php echo esc_html($$var_name_city); ?>',
                                            <?php echo esc_html($var_name_town); ?>_id: '<?php echo esc_html($$var_name_town); ?>',
                                        };
                                        var loading_countries = false;
                                        var all_ids = "#<?php echo esc_html($value['id']); ?>_country_container, #<?php echo esc_html($value['id']); ?>_state_container, #<?php echo esc_html($value['id']); ?>_city_container, #<?php echo esc_html($value['id']); ?>_town_container";
                                        var select_ids = "#foodbakery_<?php echo esc_html($value['id']); ?>_country option[value='-'], #foodbakery_<?php echo esc_html($value['id']); ?>_state option[value='-'], #foodbakery_<?php echo esc_html($value['id']); ?>_city option[value='-'], #foodbakery_<?php echo esc_html($value['id']); ?>_town option[value='-']";
                                        /*
                                         * Following ugly logic show and hide (country, state, city, town
                                         * containers) when '#foodbakery_frontend_location_parts changes.
                                         *
                                         * If Town is selected then all four will be shown
                                         * If City is selected then City, State and Country will be shown
                                         * If State is selected then State and Country will be shown
                                         * If Country is selected then Country will be shown
                                         */
                                        var ids = {
                                            "country": {"country": "#<?php echo esc_html($value['id']); ?>_country_container"},
                                            "state": {
                                                "country": "#<?php echo esc_html($value['id']); ?>_country_container",
                                                "state": "#<?php echo esc_html($value['id']); ?>_state_container"
                                            },
                                            "city": {
                                                "country": "#<?php echo esc_html($value['id']); ?>_country_container",
                                                "state": "#<?php echo esc_html($value['id']); ?>_state_container",
                                                "city": "#<?php echo esc_html($value['id']); ?>_city_container"
                                            },
                                            "town": {
                                                "country": "#<?php echo esc_html($value['id']); ?>_country_container",
                                                "state": "#<?php echo esc_html($value['id']); ?>_state_container",
                                                "city": "#<?php echo esc_html($value['id']); ?>_city_container",
                                                "town": "#<?php echo esc_html($value['id']); ?>_town_container"
                                            },
                                        };
                                        // Hide all location parts selectors.
                                        $(join_obj(ids["town"])).hide();
                                        /*
                                         * Remove all those options which are not in location levels so that they does not shown.
                                         */
                                        if ($.inArray("country", locations_levels) < 0) {
                                            delete ids["country"]["country"];
                                            delete ids["state"]["country"];
                                            delete ids["city"]["country"];
                                            delete ids["town"]["country"];
                                        }
                                        if ($.inArray("state", locations_levels) < 0) {
                                            delete ids["state"]["state"];
                                            delete ids["city"]["state"];
                                            delete ids["town"]["state"];
                                        }
                                        if ($.inArray("city", locations_levels) < 0) {
                                            delete ids["city"]["city"];
                                            delete ids["town"]["city"];
                                        }
                                        if ($.inArray("town", locations_levels) < 0) {
                                            delete ids["town"]["town"];
                                        }

                                        /**
                                         * Make a call to load_locations_list() with respective data.
                                         *
                                         * @param string type
                                         * @param [string|int] selector
                                         */
                                        function load_locations_ <?php echo esc_html($value['id']); ?>(type, selector) {
                                            "use strict";
                                            var item = <?php echo esc_html($value['id']); ?>[ <?php echo esc_html($value['id']); ?>.indexOf(type) + 1
                                        ]
                                            ;
                                            if (locations_levels_indexes_<?php echo esc_html($value['id']); ?>[item] > -1 && selector != '-') {
                                                load_locations_list_<?php echo esc_html($value['id']); ?>(item, locations_levels_indexes_<?php echo esc_html($value['id']); ?>[item], selector);
                                            }
                                        }

                                        /*
                                         * Get a list of location parts from server and pass data to provided callback.
                                         *
                                         * @param string location_type
                                         * @param integer location_level
                                         * @param {string|integer} selector
                                         * @param function callback
                                         */
                                        function load_locations_list_ <?php echo esc_html($value['id']); ?>(location_type, location_level, selector) {
                                            "use strict";
                                            $("#<?php echo esc_html($value['id']); ?>_" + location_type + "_container .ajax-loader").show();
                                            $.ajax({
                                                "url": "<?php echo admin_url('admin-ajax.php'); ?>",
                                                "data": {
                                                    "action": "get_locations_list",
                                                    "security": "<?php echo wp_create_nonce('get_locations_list'); ?>",
                                                    "location_type": location_type,
                                                    "location_level": location_level,
                                                    "selector": selector,
                                                },
                                                "dataType": "json",
                                                "method": "post",
                                                "success": function (data) {
                                                    populate_select_data_<?php echo esc_html($value['id']); ?>(data, {
                                                        "location_type": location_type,
                                                        "location_level": location_level,
                                                        "selector": selector
                                                    });
                                                    $("#<?php echo esc_html($value['id']); ?>_" + location_type + "_container .ajax-loader").hide();
                                                }
                                            });
                                        }

                                        function populate_select_data_ <?php echo esc_html($value['id']); ?>(data, params) {
                                            "use strict";
                                            if (data.error == true) {
                                                return;
                                            }
                                            var control_selector = "#foodbakery_<?php echo esc_html($value['id']); ?>_" + params.location_type;
                                            data = data.data;
                                            $(control_selector + ' option').remove();
                                            $(control_selector).append($("<option></option>").attr("value", '-').text('<?php echo esc_html__('Choose...', 'foodbakery'); ?>'));
                                            $(control_selector).append($("<option></option>").attr("value", 'all').text('<?php echo esc_html__('All', 'foodbakery'); ?>'));
                                            $.each(data, function (key, term) {
                                                $(control_selector).append($("<option></option>").attr("value", term.slug).text(term.name));
                                            });
                                            var selected_option_value = '-';
                                            if (params.selector != 'all') {
                                                selected_option_value = pre_data["<?php echo 'foodbakery_' . $value['id'] . '_'; ?>" + params.location_type + "_id"];
                                            }
                                            $(control_selector + " option[value='" + selected_option_value + "']").prop("selected", true);
                                            $(control_selector).trigger("chosen:updated");
                                        }

                                        /*
                                         * Show/Hide locations parts selectors.
                                         *
                                         * @param {type} that
                                         */
                                        function handle_show_location_parts_selectors(that) {
                                            "use strict";
                                            $(join_obj(ids["town"])).hide();
                                            var idss = '';
                                            if ($("option[value='town']", that).is(":selected")) {
                                                idss = ids["town"];
                                            } else if ($("option[value='city']", that).is(":selected")) {
                                                idss = ids["city"];
                                            } else if ($("option[value='state']", that).is(":selected")) {
                                                idss = ids["state"];
                                            } else if ($("option[value='country']", that).is(":selected")) {
                                                idss = ids["country"];
                                            }
                                            console.log(join_obj(idss));
                                            if (idss != '') {
                                                $(join_obj(idss)).show();
                                                // Keep track of selected items and also show location parts selectors.
                                                <?php echo esc_html($value['id']); ?> = $.map(idss, function (v, i) {
                                                    return i;
                                                });
                                            }
                                        }

                                        function join_obj(obj) {
                                            "use strict";
                                            var arr = [];
                                            $.each(obj, function (key, val) {
                                                arr.push(val);
                                            });
                                            return arr.join(", ");
                                        }

                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>").change(function () {
                                            // For sorting items in an order.
                                            $("#foodbakery_<?php echo esc_html($value['id']); ?>").trigger("chosen:updated");
                                            handle_show_location_parts_selectors($(this));
                                        });
                                        /*
                                         * Calculate locations levels indexes for backend.
                                         */
                                        var locations_levels_indexes_<?php echo esc_html($value['id']); ?> = {
                                            "country": -1,
                                            "state": -1,
                                            "city": -1,
                                            "town": -1
                                        };
                                        var locations_levels_index_counter = 0;
                                        $.each(locations_levels_indexes_<?php echo esc_html($value['id']); ?>, function (key, val) {
                                            if ($("#foodbakery_<?php echo esc_html($value['id']); ?> option[value='" + key + "']").length > 0) {
                                                locations_levels_indexes_<?php echo esc_html($value['id']); ?>[key] = locations_levels_index_counter;
                                                locations_levels_index_counter++;
                                            }
                                        });
                                        /*
                                         * Make already selected locations parts selected and update them in choosen.
                                         */
                                        $(all_ids).hide();
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?> option").each(function (key, elem) {
                                            var val = $(this).val();
                                            if ($.inArray(val, <?php echo esc_html($value['id']); ?>) > -1) {
                                                $("#<?php echo esc_html($value['id']); ?>_" + val + "_container").show();
                                                if ("country" == val) {
                                                    loading_countries = true;
                                                }

                                                var type = 'country';
                                                if (<?php echo esc_html($value['id']); ?>.
                                                indexOf(val) - 1 > -1
                                            )
                                                {
                                                    type = <?php echo esc_html($value['id']); ?>[ <?php echo esc_html($value['id']); ?>.indexOf(val) - 1
                                                ]
                                                    ;
                                                }
                                                var variable_selector = "<?php echo 'foodbakery_' . $value['id'] . '_'; ?>" + type + "_id";
                                                var id = pre_data[variable_selector];
                                                load_locations_list_<?php echo esc_html($value['id']); ?>(val, locations_levels_indexes_<?php echo esc_html($value['id']); ?>[$(elem).val()], id);
                                                $(this).prop('selected', true);
                                            } else {
                                                $(this).prop('selected', false);
                                            }
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>").trigger("chosen:updated");
                                        handle_show_location_parts_selectors($("#foodbakery_<?php echo esc_html($value['id']); ?>"));
                                        /*
                                         * Triggers for country, state, city or town change
                                         */
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>_country").change(function () {
                                            load_locations_<?php echo esc_html($value['id']); ?>('country', $(this).val());
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>_state").change(function () {
                                            load_locations_<?php echo esc_html($value['id']); ?>('state', $(this).val());
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>_city").change(function () {
                                            load_locations_<?php echo esc_html($value['id']); ?>('city', $(this).val());
                                        });
                                        $("#foodbakery_<?php echo esc_html($value['id']); ?>_town").change(function () {
                                            load_locations_<?php echo esc_html($value['id']); ?>('town', $(this).val());
                                        });
                                        // Preload data for countries.
                                        if (!loading_countries) {
                                            load_locations_list_<?php echo esc_html($value['id']); ?>('country', locations_levels_indexes_<?php echo esc_html($value['id']); ?>['country'], 'all');
                                        }
                                    });
                                })(jQuery);</script>
                            <?php
                            $output .= ob_get_clean();
                            break;
                        case 'user_import_export':
                            global $wp_filesystem;
                            if (class_exists('foodbakery_user_import')) {
                                $user_imp_exp = new foodbakery_user_import();
                                ob_start();
                                $user_imp_exp->foodbakery_import_user_form();
                                $output .= ob_get_clean();
                            }

                            $output .= '';

                            $output .= $foodbakery_html_fields->foodbakery_opening_field(array(
                                    'name' => esc_html__("File URL", 'foodbakery'),
                                    'hint_text' => esc_html__('', "foodbakery"),
                                )
                            );

                            $output .= '<div class="external_backup_areas">';
                            $foodbakery_opt_array = array(
                                'std' => '',
                                'cust_id' => "user_import_url",
                                'cust_name' => '',
                                'classes' => 'input-medium',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $foodbakery_opt_array = array(
                                'std' => esc_html__('Import Users', 'foodbakery'),
                                'cust_id' => "cs-p-backup-url-restore",
                                'cust_name' => '',
                                'cust_type' => 'button',
                                'return' => true,
                            );
                            $output .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                            $output .= '</div>';

                            $output .= $foodbakery_html_fields->foodbakery_closing_field(array(
                                    'desc' => '',
                                )
                            );
                            break;
                        case 'gallery_upload':
                            $foodbakery_opt_array = array(
                                'name' => $value['name'],
                                'desc' => $value['desc'],
                                'hint_text' => $value['hint_text'],
                                'echo' => $value['echo'],
                                'id' => $value['id'],
                                'std' => '',
                                'field_params' => array(
                                    'id' => $value['id'],
                                    'return' => true,
                                ),
                            );
                            $output .= $foodbakery_html_fields->foodbakery_gallery_render_plugin_option($foodbakery_opt_array);
                            break;
                        case 'orders_bookings_status':
                            global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text, $foodbakery_plugin_options;
                            ob_start();
                            $orders_status = isset($foodbakery_plugin_options['orders_status']) ? $foodbakery_plugin_options['orders_status'] : '';
                            $orders_color = isset($foodbakery_plugin_options['orders_color']) ? $foodbakery_plugin_options['orders_color'] : '';
                            ?>
                            <div class="form-elements">
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <label><?php echo foodbakery_plugin_text_srt('foodbakery_orders_inquiries_status'); ?></label>
                                </div>
                                <?php
                                $rand_id = rand(10000000, 99999999);
                                ?>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <table id="orders_status_srtable_table" class="features-templates-wrapper">
                                        <thead>
                                        <tr>

                                            <th style="width: 45px;">&nbsp;</th>
                                            <th style="width: 45px;">&nbsp;</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody class="ui-sortable">
                                        <?php
                                        $counter = 0;
                                        if (is_array($orders_status) && sizeof($orders_status) > 0) {
                                            foreach ($orders_status as $key => $lable) {
                                                if ($lable == 'Processing' || $lable == 'Cancelled' || $lable == 'Completed') {
                                                    $readonly = 'readonly';
                                                } else {
                                                    $readonly = '';
                                                }
                                                $order_color = isset($orders_color[$key]) ? $orders_color[$key] : '';
                                                 $only_for_loco = esc_html__($lable, 'foodbakery');
                                                ?>
                                                <tr id="repeat_element<?php echo intval($rand_id) . intval($counter); ?>"
                                                    class="tr_clone">
                                                    <td><span class="cntrl-drag-and-drop"><i
                                                                    class="icon-menu2"></i></span></td>
                                                    <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                           value="<?php echo esc_html($order_color); ?>"
                                                                                           name="orders_color[]"></td>
                                                    <td class="feature-label-input"><input type="text"
                                                                                           value="<?php echo $lable; ?>"
                                                                                           name="orders_status[]"
                                                                                           class="review_label" <?php echo esc_html($readonly); ?>
                                                                                           placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_orders_inquiries_enter_status'); ?>">
                                                    </td>
                                                    <?php if ($lable != 'Processing' && $lable != 'Cancelled' && $lable != 'Completed') { ?>
                                                        <td style="text-align: center;"><a href="#"
                                                                                           class="cntrl-delete-rows order-cntrl-delete-rows"
                                                                                           title="Delate Row"><i
                                                                        class="icon-cancel2"></i></a></td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                                $counter++;
                                            }
                                        } else {

                                            $rand_id = rand(1000000, 99999999);
                                            ?>
                                            <tr id="repeat_element<?php echo intval($rand_id); ?>" class="tr_clone">
                                                <td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                                </td>
                                                <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                       value="#f87979"
                                                                                       name="orders_color[]"></td>
                                                <td class="feature-label-input"><input type="text"
                                                                                       value="Processing"
                                                                                       name="orders_status[]"
                                                                                       class="review_label" readonly>
                                                </td>
                                            </tr>
                                            <tr id="repeat_element<?php echo intval($rand_id); ?>" class="tr_clone">
                                                <td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                                </td>
                                                <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                       value="#dd3333"
                                                                                       name="orders_color[]"></td>
                                                <td class="feature-label-input"><input type="text"
                                                                                       value="Cancelled"
                                                                                       name="orders_status[]"
                                                                                       class="review_label" readonly>
                                                </td>
                                            </tr>
                                            <?php $rand_id = rand(1000000, 99999999); ?>
                                            <tr id="repeat_element<?php echo intval($rand_id); ?>" class="tr_clone">
                                                <td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                                </td>
                                                <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                       value="#7ece65"
                                                                                       name="orders_color[]"></td>
                                                <td class="feature-label-input"><input type="text"
                                                                                       value="Completed"
                                                                                       name="orders_status[]"
                                                                                       class="review_label" readonly>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <a href="javascript:void(0);" id="click-more" class="cntrl-add-new-row"
                                       onclick="order_status_duplicate()"><?php echo foodbakery_plugin_text_srt('foodbakery_orders_inquiries_add_status'); ?></a>
                                </div>
                            </div>

                            <script type="text/javascript">
                                jQuery(document).ready(function () {
                                    var table_class = "#orders_status_srtable_table.features-templates-wrapper";
                                    jQuery(table_class + " tbody").sortable({
                                        cancel: "input"
                                    });
                                });
                                var counter_val = 1;
                                function order_status_duplicate() {
                                    counter_val;
                                    jQuery("#orders_status_srtable_table.features-templates-wrapper tbody").append(
                                        '<tr id="repeat_element49748535' + counter_val + '"><td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td class="feature-color-input"><input type="text" class="bg_color" value="#000000" name="orders_color[]"></td><td class="feature-label-input"><input type="text" value="" name="orders_status[]" class="review_label" placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_orders_inquiries_enter_status'); ?>"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows order-cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td></tr>'
                                    );
                                    jQuery('.bg_color').wpColorPicker();
                                    counter_val++;
                                }
                                jQuery(document).on('click', '.order-cntrl-delete-rows', function () {
                                    delete_status_row_top(this);
                                    return false;
                                });
                                function delete_status_row_top(delete_link) {
                                    jQuery(delete_link).parent().parent().remove();
                                }
                            </script>

                            <?php
                            $booking_status = isset($foodbakery_plugin_options['booking_status']) ? $foodbakery_plugin_options['booking_status'] : '';
                            $booking_status_colors = isset($foodbakery_plugin_options['booking_status_color']) ? $foodbakery_plugin_options['booking_status_color'] : '';
                            ?>
                            <div class="form-elements">
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <label><?php echo foodbakery_plugin_text_srt('foodbakery_booking_status'); ?></label>
                                </div>
                                <?php
                                $rand_id = rand(10000000, 99999999);
                                ?>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <table id="booking_status_srtable_table" class="features-templates-wrapper">
                                        <thead>
                                        <tr>

                                            <th style="width: 45px;">&nbsp;</th>
                                            <th style="width: 45px;">&nbsp;</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody class="ui-sortable">
                                        <?php
                                        $counter = 0;
                                        if (is_array($booking_status) && sizeof($booking_status) > 0) {
                                            foreach ($booking_status as $key => $lable) {
                                                if ($lable == 'Processing' || $lable == 'Cancelled' || $lable == 'Completed') {
                                                    $readonly = 'readonly';
                                                } else {
                                                    $readonly = '';
                                                }
                                                $booking_status_color = isset($booking_status_colors[$key]) ? $booking_status_colors[$key] : '';
                                                ?>
                                                <tr id="repeat_element<?php echo intval($rand_id) . intval($counter); ?>"
                                                    class="tr_clone">
                                                    <td><span class="cntrl-drag-and-drop"><i
                                                                    class="icon-menu2"></i></span></td>
                                                    <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                           value="<?php echo esc_html($booking_status_color); ?>"
                                                                                           name="booking_status_color[]">
                                                    </td>
                                                    <td class="feature-label-input"><input type="text"
                                                                                           value="<?php echo esc_html($lable); ?>"
                                                                                           name="booking_status[]"
                                                                                           class="review_label" <?php echo esc_html($readonly); ?>
                                                                                           placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_booking_enter_status'); ?>">
                                                    </td>
                                                    <?php if ($lable != 'Processing' && $lable != 'Cancelled' && $lable != 'Completed') { ?>
                                                        <td style="text-align: center;"><a href="#"
                                                                                           class="cntrl-delete-rows booking-cntrl-delete-rows"
                                                                                           title="Delate Row"><i
                                                                        class="icon-cancel2"></i></a></td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                                $counter++;
                                            }
                                        } else {
                                            $rand_id = rand(1000000, 99999999);
                                            ?>
                                            <tr id="repeat_element<?php echo intval($rand_id); ?>" class="tr_clone">
                                                <td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                                </td>
                                                <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                       value="#f87979"
                                                                                       name="booking_status_color[]">
                                                </td>
                                                <td class="feature-label-input"><input type="text"
                                                                                       value="Processing"
                                                                                       name="booking_status[]"
                                                                                       class="review_label" readonly>
                                                </td>
                                            </tr>
                                            <?php $rand_id = rand(1000000, 99999999); ?>
                                            <tr id="repeat_element<?php echo intval($rand_id); ?>" class="tr_clone">
                                                <td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                                </td>
                                                <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                       value="#dd3333"
                                                                                       name="booking_status_color[]">
                                                </td>
                                                <td class="feature-label-input"><input type="text"
                                                                                       value="Cancelled"
                                                                                       name="booking_status[]"
                                                                                       class="review_label" readonly>
                                                </td>
                                            </tr>
                                            <?php $rand_id = rand(1000000, 99999999); ?>
                                            <tr id="repeat_element<?php echo intval($rand_id); ?>" class="tr_clone">
                                                <td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                                </td>
                                                <td class="feature-color-input"><input type="text" class="bg_color"
                                                                                       value="#7ece65"
                                                                                       name="booking_status_color[]">
                                                </td>
                                                <td class="feature-label-input"><input type="text"
                                                                                       value="Completed"
                                                                                       name="booking_status[]"
                                                                                       class="review_label" readonly>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <a href="javascript:void(0);" id="click-more" class="cntrl-add-new-row"
                                       onclick="booking_status_duplicate()"><?php echo foodbakery_plugin_text_srt('foodbakery_booking_add_status'); ?></a>
                                </div>
                            </div>

                            <script type="text/javascript">
                                jQuery(document).ready(function () {
                                    var table_class = "#booking_status_srtable_table.features-templates-wrapper";
                                    jQuery(table_class + " tbody").sortable({
                                        cancel: "input"
                                    });
                                });
                                var counter_val = 1;
                                function booking_status_duplicate() {
                                    counter_val;
                                    jQuery("#booking_status_srtable_table.features-templates-wrapper tbody").append(
                                        '<tr id="repeat_element49748535' + counter_val + '"><td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td class="feature-color-input"><input type="text" class="bg_color" value="#000000" name="booking_status_color[]"></td><td class="feature-label-input"><input type="text" value="" name="booking_status[]" class="review_label" placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_booking_enter_status'); ?>"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows booking-cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td></tr>'
                                    );
                                    jQuery('.bg_color').wpColorPicker();
                                    counter_val++;
                                }
                                jQuery(document).on('click', '.booking-cntrl-delete-rows', function () {
                                    delete_booking_row_top(this);
                                    return false;
                                });
                                function delete_booking_row_top(delete_link) {
                                    jQuery(delete_link).parent().parent().remove();
                                }
                            </script>
                            <?php
                            $content = ob_get_clean();
                            $output .= $content;
                            break;
                        case 'restaurants_menus':
                            global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text, $foodbakery_plugin_options;
                            ob_start();
                            $restaurants_menus = isset($foodbakery_plugin_options['restaurants_menus']) ? $foodbakery_plugin_options['restaurants_menus'] : '';
                            ?>
                            <div class="form-elements">
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <label><?php echo foodbakery_plugin_text_srt('foodbakery_restaurants_menus'); ?></label>
                                </div>
                                <?php
                                $rand_id = rand(10000000, 99999999);
                                ?>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <table id="restaurants_menus_srtable_table" class="restaurants_menus">
                                        <thead>
                                        <tr>
                                            <th style="width: 45px;">&nbsp;</th>
                                            <th style="width: 45px;">&nbsp;</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody class="ui-sortable">
                                        <?php
                                        $counter = 0;
                                        if (is_array($restaurants_menus) && sizeof($restaurants_menus) > 0) {
                                            foreach ($restaurants_menus as $key => $lable) {
                                                if ($lable != '') {
                                                    ?>
                                                    <tr id="repeat_element<?php echo intval($rand_id) . intval($counter); ?>"
                                                        class="tr_clone">
                                                        <td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                                        </td>
                                                        <td class="feature-label-input"><input type="text"
                                                                                               value="<?php echo esc_html($lable); ?>"
                                                                                               name="restaurants_menus[]"
                                                                                               class="review_label"
                                                                                               placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurants_menus_enter'); ?>">
                                                        </td>
                                                        <td style="text-align: center;"><a href="#"
                                                                                           class="cntrl-delete-rows"
                                                                                           title="Delate Row"><i
                                                                        class="icon-cancel2"></i></a></td>
                                                    </tr>
                                                    <?php
                                                }
                                                $counter++;
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <a href="javascript:void(0);" id="click-more" class="cntrl-add-new-row"
                                       onclick="duplicate()"><?php echo foodbakery_plugin_text_srt('foodbakery_restaurants_menus_add'); ?></a>
                                </div>
                            </div>

                            <script type="text/javascript">
                                jQuery(document).ready(function () {
                                    var table_class = ".restaurants_menus";
                                    jQuery(table_class + " tbody").sortable({
                                        cancel: "input"
                                    });
                                });
                                var counter_val = 1;
                                function duplicate() {
                                    counter_val;
                                    jQuery(".restaurants_menus tbody").append(
                                        '<tr id="repeat_element49748535' + counter_val + '"><td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td class="feature-label-input"><input type="text" value="" name="restaurants_menus[]" class="review_label" placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurants_menus_enter'); ?>"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td></tr>'
                                    );
                                    counter_val++;
                                }
                                jQuery(document).on('click', '.cntrl-delete-rows', function () {
                                    delete_row_top(this);
                                    return false;
                                });
                                function delete_row_top(delete_link) {
                                    jQuery(delete_link).parent().parent().remove();
                                }
                            </script>
                            <?php
                            $content = ob_get_clean();
                            $output .= $content;
                            break;
                            $output .= '</div>';
                            $output .= '</tbody>
							</table></div></div>';
                    }
                }
            }
            $output .= '</div>';
            return array($output, $menu);
        }

        /**
         * End Function  how to create Fields Settings
         */
    }

}
