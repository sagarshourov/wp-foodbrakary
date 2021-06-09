<?php

/**
 * File Type: Buyer Approved Email Template
 */
if (!class_exists('foodbakery_approved_buyer_profile_template')) {

    class foodbakery_approved_buyer_profile_template {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $template_type;
        public $email_template_index;
        public $template_group;
        public $buyer_id;
        public $is_email_sent;

        public function __construct() {

            $this->email_template_type = 'Approved Buyer Profile';
            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Approved Buyer Profile</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><p>Hello [BUYER_NAME]! Your profile has been approved successfully.</p></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2019</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

            $this->email_template_variables = array(
                array(
                    'tag' => 'BUYER_NAME',
                    'display_text' => 'Buyer Name',
                    'value_callback' => array($this, 'get_approved_buyer_user_name'),
                ),
            );
            $this->template_group = 'Buyer';
            $this->email_template_index = 'approved-buyer-profile-template';

            add_filter('foodbakery_email_template_settings', array($this, 'template_settings_callback'), 12, 1);

            // Add options in Email Templates Addon
            // add_filter('foodbakery_foodbakery_email_templates_options', array($this, 'email_templates_options_callback'), 10, 1);
            // Add action job status callback
            add_action('foodbakery_user_profile_status_changed', array($this, 'buyer_profile_status_changed'), 10, 2);

            add_action('init', array($this, 'add_email_template'), 5);
        }

        public function template_settings_callback($email_template_options) {

            $email_template_options["types"][] = $this->email_template_type;

            $email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

            $email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

            return $email_template_options;
        }

        public function get_template() {
            return wp_foodbakery::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
        }

        function get_approved_buyer_user_name() {
            $buyer_info = get_user_by('id', $this->buyer_id);
            return $buyer_info->display_name;
        }

        function get_approved_buyer_user_email() {
            $buyer_info = get_user_by('id', $this->buyer_id);
            return $buyer_info->user_email;
        }

        public function buyer_profile_status_changed($buyer_id, $buyer_old_status) {

            if ($buyer_id != '') {

                $this->buyer_id = $buyer_id;
                $user = new WP_User($buyer_id);
                $role = array_shift($user->roles);
                // checking user role
                if ($role == 'foodbakery_buyer') {
                    // getting buyer status
                    $buyer_status = get_user_meta($buyer_id, 'foodbakery_user_status', true);
                    $buyer_old_status;

                    // checking job status
                    if ($buyer_status == 'active' && $buyer_status != $buyer_old_status) {

                        $template = $this->get_template();

                        // checking email notification is enable/disable
                        if (isset($template['email_notification']) && $template['email_notification'] == 1) {

                            $blogname = get_option('blogname');
                            $admin_email = get_option('admin_email');
                            // getting template fields
                            $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Approved Buyer Profile', 'foodbakery');
                            $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                            $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_approved_buyer_user_email();
                            $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

                            $args = array(
                                'to' => $recipients,
                                'subject' => $subject,
                                'from' => $from,
                                'message' => $template['email_template'],
                                'email_type' => $email_type,
                            );
                            do_action('foodbakery_send_mail', $args);
                        }
                    }
                }
            }
        }

        public function add_email_template() {
            $email_templates = array();
            $email_templates[$this->template_group] = array();
            $email_templates[$this->template_group][$this->email_template_index] = array(
                'title' => $this->email_template_type,
                'template' => $this->email_default_template,
                'email_template_type' => $this->email_template_type,
                'is_recipients_enabled' => false,
                'description' => __('This template is used to sending email when buyer Status approved', 'foodbakery'),
                'jh_email_type' => 'html',
            );
            do_action('foodbakery_load_email_templates', $email_templates);
        }

    }

    new foodbakery_approved_buyer_profile_template();
}