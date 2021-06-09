<?php

/**
 * File Type: Member Permissions
 */
if (!class_exists('Foodbakery_Member_Permissions')) {

    class Foodbakery_Member_Permissions {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('init', array($this, 'member_permissions'));
            add_filter('member_permissions', array($this, 'member_permissions'), 10);
            add_filter('check_permissions', array($this, 'check_permissions'), 10, 2);
        }

        public function member_permissions() {

            $permissions['restaurants'] = foodbakery_plugin_text_srt('restaurants_manage');
            $permissions['orders'] = foodbakery_plugin_text_srt('orders_manage');
            $permissions['bookings'] = foodbakery_plugin_text_srt('bookings_manage');
            $permissions['reviews'] = foodbakery_plugin_text_srt('reviews_manage');
            $permissions['withdrawals'] = esc_html__('Withdrawals', 'foodbakery');
            $permissions['earnings'] = esc_html__('Earnings', 'foodbakery');
            $permissions['statements'] = esc_html__('Statements', 'foodbakery');
            return $permissions;
            
        }

        static function check_permissions($module = 'profile', $user_ID = '') {

            if (!isset($user_ID) || $user_ID == '') {
                $user_ID = get_current_user_id();
            }
            $permissions = get_user_meta($user_ID, 'foodbakery_permissions', true);
            $user_status = get_user_meta($user_ID, 'foodbakery_user_type', true);
            if (isset($user_status) && $user_status == 'supper-admin') {
                return true;
            }
            if (isset($permissions[$module]) && $permissions[$module] == 'on') {
                return true;
            }
            return false;
        }

        static function package_buy_permission($user_id, $package_id) {
            $current_user_obj = wp_get_current_user($user_id);
            $current_user_role = $current_user_obj->roles[0];
            $user_role = false;
            $package = false;
            if ($current_user_role == 'foodbakery_publisher') {
                $user_role = true;
            }

            $package_status = get_post_status($package_id);
            if ($package_status == 'publish') {
                $package = true;
            }
            if ($user_role && $package) {
                return true;
            } else {
                return false;
            }
        }

    }

    global $permissions;
    $permissions = new Foodbakery_Member_Permissions();
}