<?php

// Direct access not allowed.
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * File Type: Foodbakery Email
 */
if ( ! class_exists('Foodbakery_Email') ) {

    class Foodbakery_Email {

        public $email_post_type_name;

        /**
         * Start construct Functions
         */
        public function __construct() {
            $this->email_post_type_name = 'emails';
            add_action('init', array( $this, 'register_post_type_callback' ));
            add_action('add_meta_boxes', array( $this, 'add_metabox_callback' ));
            add_action('wp_ajax_process_emails', array( $this, 'process_emails_callback' ));
            add_action('wp_ajax_nopriv_process_emails', array( $this, 'process_emails_callback' ));
            add_filter('manage_emails_posts_columns', array( $this, 'foodbakery_emails_columns_add' ));
            add_action('manage_emails_posts_custom_column', array( $this, 'foodbakery_emails_columns' ), 10, 2);
            add_filter('foodbakery_plugin_option_smtp_tab', array( $this, 'create_plugin_option_smtp_tab' ), 10, 1);
            add_filter('foodbakery_smtp_plugin_options', array( $this, 'create_smtp_plugin_options' ), 10, 1);
            add_action('phpmailer_init', array( $this, 'phpmailer_init_callback' ), 10, 1);
            add_action('wp_ajax_send_smtp_mail', array( $this, 'send_smtp_mail_callback' ));
            add_action('foodbakery_send_mail', array( $this, 'send_mail_callback' ), 20, 1);
            add_filter('wp_mail_from_name', array( $this, 'wp_mail_from_name_callback' ), 10, 1);
        }

        public function register_post_type_callback() {
            $labels = array(
                'name' => _x('Emails', 'post type general name', 'foodbakery'),
                'singular_name' => _x('Email', 'post type singular name', 'foodbakery'),
                'menu_name' => _x('Emails', 'admin menu', 'foodbakery'),
                'name_admin_bar' => _x('Email', 'add new on admin bar', 'foodbakery'),
                'add_new' => _x('Add New', 'email', 'foodbakery'),
                'add_new_item' => esc_html__('Add New Email', 'foodbakery'),
                'new_item' => esc_html__('New Email', 'foodbakery'),
                'edit_item' => esc_html__('Edit Email', 'foodbakery'),
                'view_item' => esc_html__('View Email', 'foodbakery'),
                'all_items' => esc_html__('Sent Emails', 'foodbakery'),
                'search_items' => esc_html__('Search Emails', 'foodbakery'),
                'parent_item_colon' => esc_html__('Parent Emails:', 'foodbakery'),
                'not_found' => esc_html__('No emails found.', 'foodbakery'),
                'not_found_in_trash' => esc_html__('No emails found in Trash.', 'foodbakery')
            );

            $args = array(
                'labels' => $labels,
                'description' => esc_html__('Description.', 'foodbakery'),
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => 'edit.php?post_type=restaurants',
                'query_var' => false,
                'rewrite' => array( 'slug' => 'emails' ),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array( 'title', 'editor' )
            );

            register_post_type($this->email_post_type_name, $args);
        }

        public function foodbakery_emails_columns_add($columns) {
            unset(
                    $columns['date']
            );
            $columns['sent_date'] = esc_html__('Date', 'foodbakery');
            return $columns;
        }

        public function foodbakery_emails_columns($name) {
            global $post;
            switch ( $name ) {
                default:

                    break;
                case 'sent_date':
                    echo get_the_time(get_option('date_format'), $post->ID);
                    break;
            }
        }

        public function add_metabox_callback() {
            add_meta_box(
                    'email-details', esc_html__('Email Details', 'foodbakery'), array( $this, 'render_email_details_metabox' ), $this->email_post_type_name, 'advanced', 'default'
            );
        }

        public function render_email_details_metabox($post) {
            if ( isset($post) ) {
                $post_id = $post->ID;

                $meta = array(
                    'email_send_to' => array( 'title' => esc_html__('Sent To', 'foodbakery'), '' ),
                    'email_status' => array( 'title' => esc_html__('Email Status', 'foodbakery'), '' ),
                    'email_headers' => array( 'title' => esc_html__('Email Headers', 'foodbakery'), '' ),
                    'mailer_response' => array( 'title' => esc_html__('Mailer Response', 'foodbakery'), '' ),
                );
                echo '<table>';
                foreach ( $meta as $key => $val ) {
                    echo '<tr>';
                    echo '<td>' . $val['title'] . '</td>';
                    echo '<td>';
                    $val = get_post_meta($post_id, $key, true);
                    if ( is_array($val) ) {
                        echo implode(', ', $val);
                    } else {
                        echo esc_html($val);
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        }

        public function save_email($args) {
            // Create post object
            $email_post = array(
                'post_title' => $args['subject'],
                'post_content' => $args['message'],
                'post_status' => 'publish',
                'post_type' => $this->email_post_type_name,
            );

            // Insert the post into the database.
            $id = wp_insert_post($email_post);


            if ( ! is_wp_error($id) ) {
                update_post_meta($id, 'email_status', 'new');
                update_post_meta($id, 'email_headers', $args['headers']);
                update_post_meta($id, 'email_send_to', $args['sent_to']);
                update_post_meta($id, 'email_type', $args['email_type']);
                return $id;
            } else {
                return 0;
            }
        }

        public function process_emails_callback() {

            $args = array(
                'post_type' => $this->email_post_type_name
            );
            $post_id = isset($_REQUEST['email_id']) ? $_REQUEST['email_id'] : 0;
            if ( $post_id != 0 ) {
                $args = array(
                    'post__in' => array( intval($post_id) ),
                );
            }
            $args['meta_key'] = 'email_status';
            $args['meta_query'] = array(
                'value' => 'new',
                'compare' => 'LIKE',
            );

            $query = new WP_Query($args);

            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $foodbakery_post_id = get_the_ID();
                    var_dump($foodbakery_post_id);
                    $foodbakery_subject = get_the_title();
                    $foodbakery_message = get_the_content();
                    $foodbakery_send_to = get_post_meta($foodbakery_post_id, 'email_send_to', true);
                    $foodbakery_headers = get_post_meta($foodbakery_post_id, 'email_headers', true);
                    $foodbakery_email_type = get_post_meta($foodbakery_post_id, 'email_type', true);
                    if ( ! empty($foodbakery_email_type) ) {
                        if ( $foodbakery_email_type == 'html' ) {
                            add_filter('wp_mail_content_type', function () {
                                return 'text/html';
                            });
                        } else {
                            add_filter('wp_mail_content_type', function () {
                                return 'text/plain';
                            });
                        }
                    }

                    $foodbakery_confirm = wp_mail($foodbakery_send_to, $foodbakery_subject, $foodbakery_message, $foodbakery_headers);

                    update_post_meta($foodbakery_post_id, 'email_status', 'processed');

                    update_post_meta($foodbakery_post_id, 'mailer_response', $foodbakery_confirm);
                }
                wp_reset_postdata();
            } else {
                echo esc_html__('No Posts found', 'foodbakery');
            }
            wp_die();
        }

        /**
          @return array Smtp plugin option fields.
         */
        public function create_plugin_option_smtp_tab($foodbakery_setting_options) {

            $foodbakery_setting_options[] = array(
                "name" => esc_html__('SMTP Configuration', 'foodbakery'),
                "fontawesome" => 'icon-email',
                "id" => "tab-smtp-configuration",
                "std" => "",
                "type" => "main-heading",
                "options" => ''
            );
            return $foodbakery_setting_options;
        }

        /**
          @return array Smtp plugin option fields.
         */
        public function create_smtp_plugin_options($foodbakery_setting_options) {



            $on_off_option = array( 'yes' => esc_html__('Yes', 'foodbakery'), 'no' => esc_html__('No', 'foodbakery') );

            $foodbakery_setting_options[] = array(
                "name" => esc_html__("SMTP Configuration", "foodbakery"),
                "id" => "tab-smtp-configuration",
                "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("SMTP Configuration", "foodbakery") . '"',
                "type" => "sub-heading",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__('SMTP Configuration', 'foodbakery'),
                "id" => "tab-settings-smtp-configuration",
                "std" => esc_html__('SMTP Configuration', 'foodbakery'),
                "type" => "section",
                "options" => ""
            );

            $foodbakery_setting_options[] = array( "col_heading" => esc_html__("SMTP Configuration", "foodbakery"),
                "type" => "tab-smtp",
                "help_text" => ""
            );
            $foodbakery_setting_options[] = array( "name" => esc_html__("Enable SMTP to Send Emails?", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__("Turn it on If you want to send Email Through SMTP..", "foodbakery"),
                "id" => "use_smtp_mail",
                "std" => "",
                "type" => "checkbox",
                "onchange" => "use_smtp_mail_opt(this)",
                "options" => $on_off_option,
            );

            $foodbakery_setting_options[] = array(
                "type" => "division",
                "enable_id" => "foodbakery_use_smtp_mail",
                "enable_val" => "on",
                "extra_atts" => 'id="foodbakery-no-smtp-div"',
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("Send e-mail via GMail?", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__("Turn it on If you want to send Email Through Google Email Server..", "foodbakery"),
                "id" => "gmail_mail",
                "std" => "",
                "type" => "checkbox",
                "onchange" => "foodbakery_mail_with_gmail(this.name)",
                "options" => $on_off_option
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("Use SMTP Authentication?", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__("Turn it on If you want to use SMTP Authentication.., If checked, you must provide the SMTP username and password below", "foodbakery"),
                "id" => "use_smtp_auth",
                "std" => "",
                "type" => "checkbox",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("SMTP Host Name", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__("Enter your Smtp host here, It is name of your service provider", "foodbakery"),
                "id" => "smtp_host",
                "std" => "",
                "classes" => "foodbakery-dev-req-field-admin",
                'extra_attr' => 'data-visible="foodbakery-no-smtp-div"',
                "type" => "text",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("SMTP Port", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__("Enter your Smtp port here, e.g 25 or 465, This is generally 25.", "foodbakery"),
                "id" => "smtp_port",
                "std" => "",
                "type" => "text",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("Connection prefix", 'foodbakery'),
                "desc" => "",
                "hint_text" => "Sets connection prefix for secure connections (prefix method must be supported by your PHP install and your SMTP host)",
                "id" => "secure_connection_type",
                "cust_name" => "mail_set_return_path",
                "std" => "true",
                "type" => "select",
                "options" => array( 'ssl' => 'ssl', 'tls' => 'tls' ),
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("SMTP username", 'foodbakery'),
                "desc" => "",
                "hint_text" => 'Enter SMTP Username here',
                "id" => "smtp_username",
                "std" => "",
                "type" => "text",
            );
            $foodbakery_setting_options[] = array( "name" => esc_html__("SMTP Password", 'foodbakery'),
                "desc" => "",
                "hint_text" => 'Please Enter SMTP Password here',
                "id" => "smtp_password",
                "std" => "",
                "type" => "password",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("Wordwrap length", 'foodbakery'),
                "desc" => "",
                "hint_text" => 'Enter Wordwrap length here, Sets word wrapping on the body of the message to a given number of characters.',
                "id" => "wordwrap_length",
                "std" => "",
                "type" => "text",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("Enable debugging?", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__("Only check this if you are experiencing problems and would like more error reporting to occur. Uncheck this once you have finished debugging.", "foodbakery"),
                "id" => "smtp_debugging",
                "std" => "",
                "type" => "checkbox",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("Sender e-mail", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__('Sets the From e-mail address for all outgoing messages. Leave blank to use the WordPress default. This value will be used even if you do not enable SMTP. NOTE: This may not take effect depending on your mail server and settings, especially if using SMTPAuth (such as for GMail).', "foodbakery"),
                "id" => "smtp_sender_email",
                "std" => "",
                "type" => "text",
            );

            $foodbakery_setting_options[] = array( "name" => esc_html__("Sender name", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__('Sets the From name for all outgoing messages. Leave blank to use the WordPress default. This value will be used even if you do not enable SMTP.', "foodbakery"),
                "id" => "sender_name",
                "std" => "",
                "type" => "text",
            );

            $foodbakery_setting_options[] = array( "name" => '',
                "desc" => "",
                "hint_text" => '',
                "id" => "submit_test_email",
                "std" => "Send Test",
                "type" => "text",
                "cust_type" => "button",
            );

            $foodbakery_setting_options[] = array(
                "type" => "division_close",
            );

            $foodbakery_setting_options[] = array( "col_heading" => esc_html__("SMTP Settings", "foodbakery"),
                "type" => "col-right-text",
                "help_text" => ""
            );

            return $foodbakery_setting_options;
        }

        /**
         * @param    PHPMailer    $phpmailer    A reference to the current instance of PHP Mailer
         */
        public function phpmailer_init_callback($phpmailer) {
            $options = get_option('foodbakery_plugin_options');
            // Don't configure for SMTP if no host is provided.
            if ( empty($options['foodbakery_use_smtp_mail']) || $options['foodbakery_use_smtp_mail'] != 'on' ) {
                return;
            }
            $phpmailer->IsSMTP();
            $phpmailer->Host = isset($options['foodbakery_smtp_host']) ? $options['foodbakery_smtp_host'] : 'imap.gmail.com';
            $phpmailer->Port = isset($options['foodbakery_smtp_port']) ? $options['foodbakery_smtp_port'] : 25;
            $phpmailer->SMTPAuth = isset($options['foodbakery_use_smtp_auth']) ? $options['foodbakery_use_smtp_auth'] : false;
            if ( $phpmailer->SMTPAuth ) {
                $phpmailer->Username = isset($options['foodbakery_smtp_username']) ? $options['foodbakery_smtp_username'] : 'admin';
                $phpmailer->Password = isset($options['foodbakery_smtp_password']) ? $options['foodbakery_smtp_password'] : 'admin';
            }
            if ( $options['foodbakery_secure_connection_type'] != '' )
                $phpmailer->SMTPSecure = isset($options['foodbakery_secure_connection_type']) ? $options['foodbakery_secure_connection_type'] : 'ssl';
            if ( $options['foodbakery_smtp_sender_email'] != '' )
                $phpmailer->SetFrom($options['foodbakery_smtp_sender_email'], $options['foodbakery_sender_name']);
            if ( $options['foodbakery_wordwrap_length'] > 0 )
                $phpmailer->WordWrap = isset($options['foodbakery_wordwrap_length']) ? $options['foodbakery_wordwrap_length'] : '20';
            if ( $options['foodbakery_smtp_debugging'] == "on" && isset($_POST['action']) && $_POST['action'] == 'send_smtp_mail' )
                $phpmailer->SMTPDebug = true;
        }

        public function send_smtp_mail_callback() {
            $user = wp_get_current_user();
            $options = get_option('foodbakery_plugin_options');
            $email = $user->user_email;
            $subject = esc_html__('This is a test mail', 'foodbakery');
            $timestamp = current_time('mysql');
            $message = sprintf(esc_html__('Hi, this is the %s plugin e-mailing you a test message from your WordPress blog.', 'foodbakery'), 'foodbakery');
            $message .= "\n\n";
            $foodbakery_from_name = isset($options['foodbakery_sender_name']) ? $options['foodbakery_sender_name'] : get_bloginfo( 'name' );
            $foodbakery_from_email = isset($options['foodbakery_smtp_sender_email']) ? $options['foodbakery_smtp_sender_email'] : get_option( 'admin_email' );
			$headers = array();
			if( $foodbakery_from_name != '' && $foodbakery_from_email != ''){
				$headers[] = 'From:' . $foodbakery_from_name . ' <' . $foodbakery_from_email . '>';
			}elseif( $foodbakery_from_name == '' && $foodbakery_from_email != '' ){
				$headers[] = 'From:' . $foodbakery_from_email;
			}
            $array = array( 'to' => $email, 'subject' => $subject, 'message' => $message, 'headers' => $headers );
			
            do_action('foodbakery_send_mail', $array);

            // Check success
            global $phpmailer;
            if ( $phpmailer->ErrorInfo != "" ) {
                $error_msg = '<div class="error"><p>' . esc_html__('An error was encountered while trying to send the test e-mail.', 'foodbakery') . '</p>';
                $error_msg .= '<blockquote style="font-weight:bold;">';
                $error_msg .= '<p>' . $phpmailer->ErrorInfo . '</p>';
                $error_msg .= '</p></blockquote>';
                $error_msg .= '</div>';
            } else {
                $error_msg = '<div class="updated"><p>' . esc_html__('Test e-mail sent.', 'foodbakery') . '</p>';
                $error_msg .= '<p>' . sprintf(esc_html__('The body of the e-mail includes this time-stamp: %s.', 'foodbakery'), $timestamp) . '</p></div>';
            }

            echo FOODBAKERY_FUNCTIONS()->special_chars($error_msg);

            exit;
        }

        /*
         * Send Mail through SMTP if configured.
         * Allowed array parameters: 
         * array('to' => $email, 'subject' => $subject, 'message' => $message, 'headers' => $headers')
         */

        public function send_mail_callback($args) {

            global $foodbakery_plugin_options;
            $foodbakery_sent_email_logs = isset($foodbakery_plugin_options['foodbakery_sent_email_logs']) ? $foodbakery_plugin_options['foodbakery_sent_email_logs'] : '';

            $foodbakery_send_to = (isset($args['to'])) ? $args['to'] : '';
            $foodbakery_subject = (isset($args['subject'])) ? $args['subject'] : '';
            $foodbakery_message = (isset($args['message'])) ? $args['message'] : '';
            $foodbakery_headers = array();
            if ( isset($args['from']) && $args['from'] != '' ) {
                $foodbakery_headers[] = 'From: ' . $args['from'];
            }
            $email_type = 'plain_text';
            if ( isset($args['email_type']) ) {
                $email_type = $args['email_type'];
            }
            $foodbakery_headers = ( isset($args['headers']) ) ? $args['headers'] : $foodbakery_headers;
            $class_obj = ( isset($args['class_obj']) ) ? $args['class_obj'] : '';

            $post_id = $this->save_email(array(
                'sent_to' => $foodbakery_send_to,
                'subject' => $foodbakery_subject,
                'message' => $foodbakery_message,
                'headers' => $foodbakery_headers,
                'email_type' => $email_type,
            ));


            if ( $post_id != 0 ) {
                wp_remote_get(admin_url('admin-ajax.php?action=process_emails&post_id=' . $post_id), array( 'timeout' => 0, 'httpversion' => '1.1' ));
            }
            if ( $class_obj != '' ) {
                $class_obj->is_email_sent = true;
            }
            if ( $foodbakery_sent_email_logs != 'on' && $post_id != '' && is_numeric($post_id) && get_post_type($post_id) == $this->email_post_type_name ) {
                wp_delete_post($post_id);
            }
        }

        /**
          @return string The name from which the email is being sent.
         */
        public function wp_mail_from_name_callback($original_email_from) {
            $options = get_option('foodbakery_plugin_options');
            // Don't configure for SMTP if no host is provided.
            if ( empty($options['foodbakery_use_smtp_mail']) || $options['foodbakery_use_smtp_mail'] != 'on' || $options['foodbakery_sender_name'] == '' ) {
                return get_bloginfo('name');
            } else {
                return $options['foodbakery_sender_name'];
            }
        }

    }

    $foodbakery_email = new Foodbakery_Email();
}
