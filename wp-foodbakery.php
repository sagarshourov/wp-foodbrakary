<?php
/*
  Plugin Name: WP Foodbakery
  Plugin URI: http://themeforest.net/user/Chimpstudio/
  Description: Foodbakery
  Version: 5.7
  Author: ChimpStudio
  Text Domain: foodbakery
  Author URI: http://themeforest.net/user/Chimpstudio/
  License: GPL2
  Copyright 2015  chimpgroup  (email : info@chimpstudio.co.uk)
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, United Kingdom
 */
if (!class_exists('wp_foodbakery')) {

    class wp_foodbakery {

        public $plugin_url;
        public $plugin_dir;
        public $version = '1.6';
        public static $foodbakery_version;
        public static $foodbakery_data_update_flag;

        /**
         * Start Function of Construct
         */
        public function __construct() {
            self::$foodbakery_version = '1.6';
            self::$foodbakery_data_update_flag = 'foodbakery_old_data_update_flag_' . str_replace(".", "_", self::$foodbakery_version);
            add_action('init', array($this, 'load_plugin_textdomain'), 0);
            remove_filter('pre_user_description', 'wp_filter_kses');
            add_filter('pre_user_description', 'wp_filter_post_kses');
            // Add optinos in Email Template Settings
            add_filter('foodbakery_email_template_settings', array($this, 'email_template_settings_callback'), 0, 1);
            add_filter('foodbakery_get_plugin_options', array($this, 'foodbakery_get_plugin_options_callback'), 0, 1);
            add_action('admin_menu', array($this, 'admin_menu_position'));
            add_action('wp_footer', array($this, 'foodbakery_loader'));
            add_action('admin_notices', array($this, 'check_db_update_db_foodbakery'), 5);
            add_action('admin_footer', array($this, 'foodbakery_admin_footer_modal'));
            add_action('wp_ajax_foodbakery_db_update', array($this, 'foodbakery_db_update_callback'));
            add_action('wp_ajax_nopriv_foodbakery_db_update', array($this, 'foodbakery_db_update_callback'));
            $this->define_constants();
            $this->includes();
            add_action('init', array($this, 'create_restaurant_settings'));
            add_action('admin_head', array($this, 'hide_update_notice_for_foodbakery_pages'), 11);
            $this->change_profile_types();
            //add_action('admin_menu', array($this, 'foodbakery_menu'), 0);
           // add_action('admin_menu', array($this, 'foodbakery_submenu'), 24);
        }
        //foodbakery_menu_title
        public function foodbakery_menu() {
            add_menu_page('FB Help Desk', 'FB Help Desk', 'manage_options', 'foodbakery', array($this, 'foodbakery_menu_callback'), plugins_url('/assets/backend/images/help.png', __FILE__), 2);
            add_submenu_page('foodbakery', '', '', 'manage_options', 'foodbakery', array($this, 'foodbakery_menu_callback'));
            remove_submenu_page('foodbakery', 'foodbakery');
        }
        public function foodbakery_menu_callback() {
            
        }
        public function foodbakery_submenu() {
            global $submenu;
            add_submenu_page('foodbakery', 'Knowledge base', 'Knowledge base', 'manage_options', 'foodbakery_knowledge_base ', array($this, 'foodbakery_knowledge_base_callback'));
            add_submenu_page('foodbakery', 'Documentation', 'Documentation', 'manage_options', 'foodbakery_documentation', array($this, 'documentation_callback'));
            add_submenu_page('foodbakery', 'Support', 'Support', 'manage_options', 'foodbakery_support', array($this, 'foodbakery_support_callback'));
            add_submenu_page('foodbakery', 'Customization', 'Customization', 'manage_options', 'foodbakery_customization', array($this, 'foodbakery_customization_callback'));
        }

        public function foodbakery_knowledge_base_callback() {
            global $submenu;

            echo '<pre>';
            print_r($submenu['foodbakery'][1]);
        }

        public function recommended_plugins_callback() {
            global $submenu;
            echo '<pre>';
            print_r($submenu['foodbakery'][2]);
        }

        public function documentation_callback() {
            global $submenu;
            echo '<pre>';
            print_r($submenu['foodbakery'][3]);
        }

        public function foodbakery_support_callback() {
            global $submenu;
            echo '<pre>';
            print_r($submenu['foodbakery'][4]);
        }

        public function foodbakery_customization_callback() {
            global $submenu;
            echo '<pre>';
            print_r($submenu['foodbakery'][5]);
        }

        public function change_profile_types() {
            global $wpdb;
            $wpdb->update(
                    $wpdb->prefix . 'postmeta', array(
                'meta_value' => 'restaurant', // string
                    ), array(
                'meta_key' => 'foodbakery_publisher_profile_type',
                'meta_value' => 'company'
                    )
            );
            $wpdb->update(
                    $wpdb->prefix . 'postmeta', array(
                'meta_value' => 'restaurant', // string
                    ), array(
                'meta_key' => 'foodbakery_publisher_profile_type',
                'meta_value' => 'company'
                    )
            );
        }

        /**
         * Start Function how to Create WC Constants
         */
        private function define_constants() {

            global $post, $wp_query, $foodbakery_plugin_options, $current_user, $foodbakery_jh_scodes, $plugin_user_images_foodbakery;

            $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
            $this->plugin_url = plugin_dir_url(__FILE__);
            $this->plugin_dir = plugin_dir_path(__FILE__);
            $plugin_user_images_foodbakery = 'wp-foodbakery-users';
        }

        /**
         * What type of request is this?
         * string $type ajax, frontend or admin
         * @return bool
         */
        /*
         * remove admin notices
         */
        public function hide_update_notice_for_foodbakery_pages() {
            $screen = get_current_screen();
            $post_type_screen = isset($screen->post_type) ? $screen->post_type : '';
            $argss = array(
                'public' => true,
                '_builtin' => false
            );
            $output = 'names'; // names or objects, note names is the default
            $operator = 'and';
            $all_custom_post_types = get_post_types($argss, $output, $operator);

            if ($post_type_screen != '' && in_array($post_type_screen, $all_custom_post_types)) {
                global $wp_filter;
                remove_action('admin_notices', 'update_nag', 3);
                unset($wp_filter['user_admin_notices']);
                unset($wp_filter['admin_notices']);
            }
        }

        public function is_request($type) {
            switch ($type) {
                case 'admin' :
                    return is_admin();
                    break;
                case 'ajax' :
                    return defined('DOING_AJAX');
                case 'cron' :
                    return defined('DOING_CRON');
                case 'frontend' :
                    return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
            }
        }

        /*
         * Foodbakery Error Messages Popup in Footer for admin
         * 
         */

        public function foodbakery_admin_footer_modal() {
            echo '<div class="foodbakery-error-messages" style="display:none;"></div>';
        }

        /*
         * update all data for property visibility
         */

        public function foodbakery_db_update_callback() {
            do_action('foodbakery_plugin_db_structure_updater');
            add_option(self::$foodbakery_data_update_flag, 'yes');
            $json['type'] = 'success';
            $json['msg'] = foodbakery_plugin_text_srt('foodbakery_property_visibility_updated_msg');
            echo json_encode($json);
            die();
        }

        /*
         * Update db hook
         */

        public static function check_db_update_db_foodbakery() {
            global $foodbakery_Class;
            $purchase_code_data = get_option('item_purchase_code_verification');
            $envato_email = isset($purchase_code_data['envato_email_address']) ? $purchase_code_data['envato_email_address'] : '';
            $selected_demo = isset($purchase_code_data['selected_demo']) ? $purchase_code_data['selected_demo'] : '';
            $demos_array = array();
            $options = "<option value=''>Pleae select a demo you are using right now</option>";
            if (function_exists('get_demo_data_structure')) {
                $demos = get_demo_data_structure();
            }

            if (!empty($demos)) {
                foreach ($demos as $demo_key => $demo_value) {
                    $demos_array[$demo_key] = $demo_key;
                    $demo_slug = isset($demo_value['slug']) ? $demo_value['slug'] : '';
                    $demo_name = isset($demo_value['name']) ? $demo_value['name'] : '';
                    $selected = ( $demo_slug == $selected_demo ) ? ' selected' : '';
                    $options .= "<option value='" . $demo_slug . "'" . $selected . ">" . $demo_name . "</option>";
                }
            }
            //$item_purchase_code    = isset( $purchase_code_data['item_puchase_code'] )? $purchase_code_data['item_puchase_code'] : '';
            if (get_option(self::$foodbakery_data_update_flag) !== 'yes') {

                $class = 'notice notice-warning is-dismissible';
                $popup_fields = '';
                //$affected_packages = '<ul><li>' . implode('</li><li>', $affected_plugins) . '</li></ul>';
                $popup_message = '<h1 style=\'color: #ff2e2e; margin-top: 0; float: none;\'>Warning!!!</h1> By upgrading it will take some time. So please wait after move next:<br>';

                if (class_exists('wp_foodbakery_framework')) {

                    $popup_fields = "<div id=\'confirmText\' style=\'padding-left: 20px; padding-right: 20px;\'><div class='row'>\
                        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\
                            <div class='field-holder'>\
                                    <input type='text' placeholder='Envato Provided Email *' id='envato_email' name='envato_email' value='" . $envato_email . "'>\
                            </div>\
                        </div>\
                        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\
                            <div class='field-holder'>\
                                    <select name='theme_demo' class='chosen-select' id='theme_demo'>" . $options . "</select>\
                            </div>\
                        </div>\
                    </div></div>";
                }

                $popup = '
						<script type="text/javascript">
                                                        
							var html_popup1 = "<div id=\'confirmOverlay\' style=\'display:block\'><div id=\'confirmBox\' class=\'update-popup-box\'>";
							html_popup1 += "<div id=\'confirmText\' style=\'padding-left: 20px; padding-right: 20px;\'>' . $popup_message . '</div>";
							html_popup1 += "' . $popup_fields . '";
							html_popup1 += "<div id=\'confirmButtons\'><div class=\'button confirm-yes\'>Upgrade</div><div class=\'button confirm-no\'>Cancel</div><br class=\'clear\'></div><div id=\'property-visibility-update-msg\'></div></div></div>";
							
							(function($){
								$(function() {
                                                                    $(".btnConfirmVisiblePropertyUpgrade").click(function() {
                                                                        $(this).parent().append(html_popup1);
                                                                        $(".confirm-yes").click(function() {
                                                                                
                                                                                //start ajax request
                                                                                var old_html =  $(".confirm-yes").html();
                                                                                var theme_demo = $("#theme_demo").val();
                                                                                
                                                                                var envato_email = $("#envato_email").val();
                                                                                
                                                                                var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,50}\b$/i;
                                                                                var result = pattern.test(envato_email);
                                                                                if( envato_email != "" && result == false){
                                                                                    alert("Please provide valid email address.");
                                                                                    return false;
                                                                                }
                                                                                $(".confirm-yes").html("<i class=\'icon-spinner\' style=\'margin:13px 0 0 -5px;\'></i>");
                                                                                $.ajax({
                                                                                    type: "POST",
                                                                                    dataType: "json",
                                                                                    url: foodbakery_globals.ajax_url,
                                                                                    data: "envato_email="+envato_email+"&theme_demo="+theme_demo+"&action=foodbakery_db_update",
                                                                                    success: function (response) {
                                                                                      $(".confirm-yes").html(old_html);
                                                                                      $("#property-visibility-update-msg").html("<p style=\'color: #008000;padding-left: 20px; padding-right: 20px;\'>" + response.msg + "</p>");
                                                                                    }
                                                                                });

                                                                                // end ajax request

                                                                        });
                                                                        $(".confirm-no").click(function() {
                                                                                $("#confirmOverlay").remove();
                                                                                window.location = window.location;
                                                                        });
                                                                        return false;
                                                                });
								});
							})(jQuery);
						</script>';
                $message = '<h2>Foodbakery Alert!</h2>';
                $message .= 'DB Structure Need to update for latest plugin compatibility. <br/><br/> <a href="#" class="btnConfirmVisiblePropertyUpgrade button button-primary button-hero load-customize hide-if-no-customize">Click here to run update</a> ' . $popup;
                printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
            }
        }

        /**
         * Foodbakery Loader in Footer
         */
        public function foodbakery_loader() {
            ?>
            <div class="foodbakery_loader" style="display: none;">
                <div class="loader-img"><i class="icon-spinner"></i></div>
            </div>
            <div class="foodbakery-button-loader">
                <div class="spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
            </div>

            <?php if (!wp_foodbakery::is_demo_user_modification_allowed()) : ?>
                <script type="text/javascript">
                    (function ($) {
                        $(document).ready(function () {
                            bind_rest_auth_event();
                            jQuery.growl.error({
                                message: "<?php //echo __('Demo users are not allowed to modify information.', 'foodbakery'); ?>"
                            });
                            $("body").on("DOMNodeInserted DOMNodeRemoved", bind_rest_auth_event);
                        });

                        function bind_rest_auth_event() {
                            $("input[type='submit'], .btn-submit, .btn-send, .delete-this-user-review, .delete-shortlist, .remove_member, #team_update_form").off("click");
                            $(document).off("click", "input[type='submit'], .btn-submit, .btn-send, .delete-this-user-review, .delete-shortlist, .remove_member, #team_update_form");
                            $("body").off("click", "input[type='submit'], .btn-submit, .btn-send, .delete-this-user-review, .delete-shortlist, .remove_member, #team_update_form");
                            $("body").on("click", "input[type='submit'], .btn-submit, .btn-send, .delete-this-user-review, .delete-shortlist, .remove_member, #team_update_form", function (e) {
                                e.stopPropagation();
                                jQuery.growl.error({
                                    message: '<?php echo __('Demo users are not allowed to modify information.', 'foodbakery'); ?>'
                                });
                                return false;
                            });
                        }
                    })(jQuery);
                </script>
            <?php endif; ?>
            <?php
        }

        /**
         * Start Function how to add core files used in admin and theme
         */
        public function includes() {

            /*
             * Email Templates.
             */
            require_once 'backend/classes/email-templates/class-register-template.php';
            require_once 'backend/classes/email-templates/class-reset-password-template.php';
            require_once 'backend/classes/email-templates/class-restaurant-add-template.php';
            require_once 'backend/classes/email-templates/class-new-publisher-notification-site-owner-template.php';
            require_once 'backend/classes/email-templates/class-restaurant-update-email-template.php';
            require_once 'backend/classes/email-templates/class-restaurant-approved-email-template.php';
            require_once 'backend/classes/email-templates/class-restaurant-not-approved-email-template.php';
            require_once 'backend/classes/email-templates/class-restaurant-pending-email-template.php';
            require_once 'backend/classes/email-templates/class-approved-publisher-profile-template.php';
            require_once 'backend/classes/email-templates/class-not-approved-publisher-profile-template.php';
            require_once 'backend/classes/email-templates/class-approved-buyer-profile-template.php';
            require_once 'backend/classes/email-templates/class-not-approved-buyer-profile-template.php';
            require_once 'backend/classes/email-templates/class-review-added-template.php';
            require_once 'backend/classes/email-templates/class-review-reply-added-template.php';
            require_once 'backend/classes/email-templates/class-restaurant-expired-template.php';
            //Sent Invitation Email Template
            require_once 'backend/classes/email-templates/class-invitation-sent-template.php';
            // Orders Email Template
            require_once 'backend/classes/email-templates/orders-inquiries/class-sent-order-template.php';

            if(class_exists('Foodbakery_customization')){
                require_once 'backend/classes/email-templates/orders-inquiries/class-buyer-invoice-template.php';
                require_once 'backend/classes/email-templates/orders-inquiries/class-restaurant_invoice-template.php';
                require_once 'backend/classes/email-templates/orders-inquiries/class-admin_invoice-template.php';
            }

            require_once 'backend/classes/email-templates/orders-inquiries/class-received-order-template.php';
            require_once 'backend/classes/email-templates/orders-inquiries/class-update-order-status-template.php';
            // Bookings Email Template
            require_once 'backend/classes/email-templates/orders-inquiries/class-sent-booking-template.php';
            require_once 'backend/classes/email-templates/orders-inquiries/class-received-booking-template.php';
            require_once 'backend/classes/email-templates/orders-inquiries/class-update-booking-status-template.php';
            /*
             * croppic
             */
            require_once 'frontend/classes/class-image-cropper.php';
            /*
             * Include admin files
             */

            /*
             * Form Fields Class
             */
            require_once 'backend/classes/form-fields/class-form-fields.php';
            require_once 'backend/classes/form-fields/class-html-fields.php';
            /*
             * Form Fields Classes Frontend
             */
            require_once 'frontend/classes/form-fields/class-form-fields.php';
            require_once 'frontend/classes/form-fields/class-html-fields.php';

            /*
             * Payment Gateways Files
             */
            require_once 'payments/class-payments.php';
            require_once 'payments/custom-wooc-hooks.php';
            require_once 'payments/config.php';

            /*
             * Email Class
             */
            require_once 'backend/classes/class-email.php';

            require_once 'backend/post-types/restaurants/restaurants.php';
            require_once 'backend/post-types/comments/comments.php';
            /*
             * Strings Class
             */
            require_once 'assets/common/translate/class-strings.php';

            // importer hooks
            require_once 'backend/include/importer-hooks.php';

            /*
             * Helpers Classes
             */
            require_once 'helpers/helpers-notification.php';
            require_once 'helpers/helpers-general.php';

            /*
             * Shortcode File
             * Other files are being added into this file.
             */
            // for login
            require_once 'elements/login/login-functions.php';
            require_once 'elements/login/login-forms.php';
            require_once 'elements/login/cs-social-login/cs-social-login.php';
            require_once 'elements/login/cs-social-login/google/cs_google_connect.php';
            // linkedin login
            // recaptchas
            require_once 'elements/login/recaptcha/autoload.php';

            require_once 'shortcodes/backend/class-parent-shortcode.php';
            require_once 'shortcodes/class-shortcodes.php';

            // restaurant add shortcde files

            require_once 'shortcodes/frontend/foodbakery-add-restaurant.php';
            /*
             * shortcodes
             */
            // banners shortcode
            require_once 'shortcodes/frontend/shortcode-banner-ads.php';
            // map search
            require_once 'shortcodes/backend/shortcode-restaurants-search.php';
            require_once 'shortcodes/frontend/shortcode-restaurants-search.php';
            /*
             * social sharing Class
             */
            require_once 'frontend/classes/class-social-sharing.php';
            /*
             * social sharing Class
             */

            /*
             * Order/Inquiry Detail Class
             */
            require_once 'frontend/classes/class-order-detail.php';

            /*
             * pagination sharing Class
             */
            require_once 'frontend/classes/class-pagination.php';

            /*
             * Publisher Account Pages
             */
            require_once 'frontend/templates/dashboards/class-dashboards.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-add-restaurant.php';
            require_once 'frontend/templates/dashboards/class-restaurant-menus.php';

            require_once 'frontend/templates/payment-process-center.php';

            /*
             * Publisher Account Pages
             */
            //require_once 'frontend/templates/dashboards/publisher/publisher-dashboard.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-restaurants.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-profile.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-company.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-packages.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-orders-inquires.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-bookings.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-suggested.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-withdrawals.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-statement.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-food-menu.php';
            require_once 'frontend/templates/dashboards/publisher/publisher-earnings.php';
            /*
             * restaurants Post type classes for fields
             */
            require_once 'backend/post-types/class-save-post-options.php';
            require_once 'backend/post-types/restaurants/classes/class-restaurants-opening-hours.php';
            require_once 'backend/post-types/restaurants/classes/class-restaurants-posted-by.php';
            require_once 'backend/post-types/restaurants/classes/class-restaurants-images-gallery.php';
            require_once 'backend/post-types/restaurants/classes/class-restaurant-menus.php';
            require_once 'backend/post-types/restaurants/classes/class-restaurants-page-elements.php';
            require_once 'backend/post-types/restaurants/restaurant-custom-fields.php';
            require_once 'backend/post-types/restaurants/restaurants-meta.php';
            require_once 'backend/post-types/restaurants/restaurant-taxonomy-mata.php';
            /*
             * restaurants-setting Post type classes for fields
             */
            require_once 'backend/post-types/restaurants-setting/restaurants-setting.php';
            require_once 'backend/post-types/restaurants-setting/restaurants-setting-fields.php';
            require_once 'backend/post-types/restaurants-setting/restaurants-setting-form-builder-fields.php';
            require_once 'backend/post-types/restaurants-setting/restaurants-setting-meta.php';
            require_once 'backend/post-types/restaurants-setting/classes/class-restaurants-setting-categories.php';
            /*
             * publishers Post type classes for fields
             */
            require_once 'backend/post-types/publishers/publishers.php';
            require_once 'backend/post-types/publishers/publishers-meta.php';
            /*
             * Memberships Post type classes for fields
             * @Used as hooks
             */
            require_once 'backend/post-types/packages/packages.php';
            require_once 'backend/post-types/packages/packages-meta.php';
            require_once 'backend/post-types/transactions/transactions.php';
            require_once 'backend/post-types/transactions/transactions-meta.php';
            require_once 'backend/post-types/currencies/currencies.php';
            /*
             * Orders & Inquires Post type classes for fields
             * @Used as hooks
             */
            require_once 'backend/post-types/orders-inquiries/orders-inquiries.php';
            require_once 'backend/post-types/orders-inquiries/orders-inquires-meta.php';
            /*
             * Price Table Post type classes for fields
             * @Files
             */
            require_once 'backend/post-types/price-tables/price-table.php';
            require_once 'backend/post-types/price-tables/price-table-meta.php';
            /*
             * Form Fields Classes
             */
            require_once 'backend/classes/form-fields/class-form-fields.php';
            require_once 'backend/classes/form-fields/class-html-fields.php';
            require_once 'frontend/templates/functions.php';
            /*
             * User Meta
             */
            require_once 'backend/include/user-meta/meta.php';
            /*
             * Plugin Settings Classes
             */
            require_once 'backend/settings/plugin-settings.php';
            require_once 'backend/settings/includes/plugin-options.php';
            require_once 'backend/settings/includes/plugin-options-fields.php';
            require_once 'backend/settings/includes/plugin-options-functions.php';
            require_once 'backend/settings/includes/plugin-options-array.php';
            require_once 'backend/settings/user-import/import.php';
            /*
             * Transactions Files
             */
            require_once 'backend/post-types/package-orders/package-orders.php';
            require_once 'backend/post-types/package-orders/package-orders-meta.php';
            /*
             * Withdrawals Files
             */
            require_once 'backend/post-types/withdrawals/withdrawals.php';
            require_once 'backend/post-types/withdrawals/withdrawals-meta.php';
            /*
             * Include frontend files
             */
            /*
             * Restaurant Page Elements Classes
             */
            require_once 'frontend/classes/page-elements/class-features-element.php';
            require_once 'frontend/classes/page-elements/class-opening-hours-element.php';
            require_once 'frontend/classes/page-elements/class-services-element.php';
            require_once 'frontend/classes/page-elements/class-booking-element.php';
            require_once 'frontend/classes/page-elements/class-reservation-element.php';
            require_once 'frontend/classes/page-elements/class-contact-element.php';
            require_once 'frontend/classes/page-elements/class-discussion-element.php';
            /*
             * Member Permissions
             */
            require_once 'frontend/classes/class-member-permissions.php';
            /*
             * Location Manager
             */
            require_once 'frontend/classes/class-locations-manager.php';

            /*
             * Reviews Manager
             */
            require_once 'frontend/classes/class-reviews-manager.php';

            /*
             * widgets
             */
            require_once 'backend/widgets/widgets.php';
            require_once 'backend/widgets/foodbakery_locations.php';
            require_once 'backend/widgets/foodbakery_popular_cuisines.php';
            require_once 'backend/widgets/foodbakery-restaurent-message.php';
            require_once 'backend/widgets/foodbakery-banners.php';
            /*
             * Publisher Account Pages
             */
            /*
             * search element classes
             */
            require_once 'frontend/classes/search-elements/class-search-box.php';
            /*
             * google cpathca
             */
            require_once 'frontend/classes/class-google-captcha.php';

            do_action('foodbakery_include_required_files');

            add_filter('template_include', array($this, 'foodbakery_single_template'));
            add_action('admin_enqueue_scripts', array($this, 'foodbakery_defaultfiles_plugin_enqueue'), 2);
            add_action('admin_enqueue_scripts', array($this, 'foodbakery_enqueue_admin_style_sheet'), 90);
            add_action('wp_enqueue_scripts', array($this, 'foodbakery_defaultfiles_plugin_enqueue'), 2);
            add_action('wp_enqueue_scripts', array($this, 'foodbakery_enqueue_responsive_front_scripts'), 90);
            add_action('admin_init', array($this, 'foodbakery_all_scodes'));
            add_filter('body_class', array($this, 'foodbakery_boby_class_names'));
        }

        /**
         * Start Function how to add Specific CSS Classes by filter
         */
        function foodbakery_boby_class_names($classes) {
            $classes[] = 'wp-foodbakery';
            return $classes;
        }
        /**
         * Start Function how position admin menu
         */
        public function admin_menu_position() {
            global $menu, $submenu;
            foreach ($menu as $key => $menu_item) {
                if (isset($menu_item[2]) && $menu_item[2] == 'edit.php?post_type=restaurants') {
                    $menu[$key][0] = esc_html__('Foodbakery', 'foodbakery');
                }
            }
        }

        /**
         * Start Function how to access admin panel
         */
        public function prevent_admin_access() {
            if (is_user_logged_in()) {

                if (strpos(strtolower($_SERVER['REQUEST_URI']), '/wp-admin') !== false && (current_user_can('foodbakery_publisher'))) {
                    wp_redirect(get_option('siteurl'));
                    add_filter('show_admin_bar', '__return_false');
                }
            }
        }

        /**
         * Start Function how to Add textdomain for translation
         */
        public function load_plugin_textdomain() {
            global $foodbakery_plugin_options;
            add_action('pre_get_posts', array($this, 'pre_get_posts_callback'));

            if (function_exists('icl_object_id')) {

                global $sitepress, $wp_filesystem;

                require_once ABSPATH . '/wp-admin/includes/file.php';

                $backup_url = '';

                if (false === ($creds = request_filesystem_credentials($backup_url, '', false, false, array()) )) {

                    return true;
                }
                if (!WP_Filesystem($creds)) {
                    request_filesystem_credentials($backup_url, '', true, false, array());
                    return true;
                }
                $foodbakery_languages_dir = plugin_dir_path(__FILE__) . 'languages/';
                $foodbakery_all_langs = $wp_filesystem->dirlist($foodbakery_languages_dir);
                $foodbakery_mo_files = array();
                if (is_array($foodbakery_all_langs) && sizeof($foodbakery_all_langs) > 0) {

                    foreach ($foodbakery_all_langs as $file_key => $file_val) {

                        if (isset($file_val['name'])) {

                            $foodbakery_file_name = $file_val['name'];

                            $foodbakery_ext = pathinfo($foodbakery_file_name, PATHINFO_EXTENSION);

                            if ($foodbakery_ext == 'mo') {
                                $foodbakery_mo_files[] = $foodbakery_file_name;
                            }
                        }
                    }
                }

                $foodbakery_active_langs = $sitepress->get_current_language();

                foreach ($foodbakery_mo_files as $mo_file) {
                    if (strpos($mo_file, $foodbakery_active_langs) !== false) {
                        $foodbakery_lang_mo_file = $mo_file;
                    }
                }
            }

            $locale = apply_filters('plugin_locale', get_locale(), 'foodbakery');
            $dir = trailingslashit(WP_LANG_DIR);
            if (isset($foodbakery_lang_mo_file) && $foodbakery_lang_mo_file != '') {
                load_textdomain('foodbakery', plugin_dir_path(__FILE__) . "languages/" . $foodbakery_lang_mo_file);
            } else {
                load_textdomain('foodbakery', plugin_dir_path(__FILE__) . "languages/foodbakery-" . $locale . '.mo');
            }
        }

        /**
         * Start Function to Hide Commission transactions from admin
         */
        public function pre_get_posts_callback($query) {
            global $post_type, $pagenow;
            //if we are currently on the edit screen of the post type listings
            if ($pagenow == 'edit.php' && $post_type == 'foodbakery-trans') {
                $query->query_vars['meta_query'] = array(
                    array(
                        'key' => 'foodbakery_transaction_order_charge_type',
                        'compare' => 'NOT EXISTS'
                    )
                );
            }
        }

        /**
         * Fetch and return version of the current plugin
         *
         * @return	string	version of this plugin
         */
        public static function get_plugin_version() {
            $plugin_data = get_plugin_data(__FILE__);
            return $plugin_data['Version'];
        }

        /**
         * Start Function how to Add User and custom Roles
         */
        public function foodbakery_add_custom_role() {
            add_role('guest', 'Guest', array(
                'read' => true, // True allows that capability
                'edit_posts' => true,
                'delete_posts' => false, // Use false to explicitly deny
            ));
        }

        /**
         * Start Function how to Add plugin urls
         */
        public static function plugin_url() {
            return plugin_dir_url(__FILE__);
        }

        /**
         * Start Function how to Add image url for plugin foodbakery
         */
        public static function plugin_img_url() {
            return plugin_dir_url(__FILE__);
        }

        /**
         * Start Function how to Create plugin Foodbakery
         */
        public static function plugin_dir() {
            return plugin_dir_path(__FILE__);
        }

        /**
         * Start Function how to Activate the plugin
         */
        public static function activate() {
            global $plugin_user_images_foodbakery;
            add_option('foodbakery__plugin_activation', 'installed');
            add_option('foodbakery_', '1');
            // create user role for foodbakery publisher
            $result = add_role(
                    'foodbakery_publisher', esc_html__('Foodbakery Publisher', 'foodbakery'), array(
                'read' => false,
                'edit_posts' => false,
                'delete_posts' => false,
                    )
            );
            // create users images foodbakery 
            $upload = wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $upload_dir = $upload_dir . '/' . $plugin_user_images_foodbakery;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777);
            }
        }

        /**
         * Start Function how to DeActivate the plugin
         */
        static function deactivate() {
            delete_option('foodbakery__plugin_activation');
            delete_option('foodbakery_', false);
        }

        /**
         * Start Function how to Add Theme Templates
         */
        public function foodbakery_single_template($single_template) {
            global $post;
            if (get_post_type() == 'restaurants') {
                if (is_single()) {
                    $single_template = plugin_dir_path(__FILE__) . 'frontend/templates/single_pages/single-restaurant.php';
                }
            }
            if (get_post_type() == 'orders_inquiries') {
                $single_template = plugin_dir_path(__FILE__) . 'frontend/templates/single_pages/single-orders-inquiries.php';
            }

            return $single_template;
        }

        /**
         * Custom Css 
         */
        public function foodbakery_custom_inline_styles_method() {

            $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
            wp_enqueue_style('custom-style-inline', plugins_url('/assets/frontend/css/custom_script.css', __FILE__));
            $foodbakery_custom_css = isset($foodbakery_plugin_options['foodbakery_style-custom-css']) ? $foodbakery_plugin_options['foodbakery_style-custom-css'] : 'sdfdsa';
            $custom_css = $foodbakery_custom_css;
            wp_add_inline_style('custom-style-inline', $custom_css);
        }

        /**
         * Start Function how to Includes Default Scripts and Styles
         */
        public function foodbakery_defaultfiles_plugin_enqueue() {
            global $foodbakery_plugin_options;
            // admin styles
            if (is_admin()) {
                wp_enqueue_media();
            }
            if (!is_admin()) {
                wp_register_style('foodbakery-pretty-photo-css', plugins_url('/assets/frontend/css/prettyPhoto.css', __FILE__));

                // map height 100%

                wp_register_style('leaflet', plugins_url('/assets/frontend/css/leaflet.css', __FILE__));
                wp_register_style('jquery-mCustomScrollbar', plugins_url('/assets/frontend/css/jquery.mCustomScrollbar.css', __FILE__));


                wp_register_script('leaflet', plugins_url('/assets/frontend/scripts/leaflet.js', __FILE__), array('jquery'));
                /*
                 * register i croppic styles
                 */


                /* swipper */
                wp_register_style('swiper', plugins_url('/assets/frontend/css/swiper.css', __FILE__));
                wp_register_script('swiper', plugins_url('/assets/frontend/scripts/swiper.min.js', __FILE__), array('jquery'), '', true);
                wp_register_script('jquery-mCustomScrollbar', plugins_url('/assets/frontend/scripts/jquery.mCustomScrollbar.concat.min.js', __FILE__), array('jquery'), '', true);
                /*
                 * register i croppic scripts
                 */
                wp_register_script('foodbakery-cripic-min_js', plugins_url('/assets/frontend/scripts/croppic.min.js', __FILE__), array('jquery'));
            }

            // common file for restaurant category
            wp_register_script('foodbakery-restaurant-categories', plugins_url('/assets/common/js/restaurant-categories.js', __FILE__), array('jquery'));
            wp_register_script('chosen-ajaxify', plugins_url('/assets/backend/scripts/chosen-ajaxify.js', __FILE__));
            $foodbakery_pt_array = array(
                'plugin_url' => wp_foodbakery::plugin_url(),
            );
            wp_localize_script('chosen-ajaxify', 'foodbakery_chosen_vars', $foodbakery_pt_array);

            if (!is_admin()) {
                wp_register_style('fonticonpicker', plugins_url('/assets/icomoon/css/jquery.fonticonpicker.min.css', __FILE__));
            }

            wp_enqueue_style('iconmoon', plugins_url('/assets/icomoon/css/iconmoon.css', __FILE__));
            wp_enqueue_style('foodbakery_fonticonpicker_bootstrap_css', plugins_url('/assets/icomoon/theme/bootstrap-theme/jquery.fonticonpicker.bootstrap.css', __FILE__));
            wp_enqueue_script('bootstrap-min', plugins_url('/assets/common/js/bootstrap.min.js', __FILE__), array('jquery'), '', true);

            if (!is_admin()) {

                wp_enqueue_style('bootstrap', plugins_url('/assets/frontend/css/bootstrap.css', __FILE__));
                 wp_enqueue_style('jquery-confirm', plugins_url('/assets/frontend/css/jquery-confirm.min.css', __FILE__));
                wp_enqueue_style('bootstrap_slider', plugins_url('/assets/frontend/css/bootstrap-slider.css', __FILE__));
                wp_enqueue_style('foodbakery_plugin_css', plugins_url('/assets/frontend/css/cs-foodbakery-plugin.css', __FILE__));
                wp_register_style('jqueru_ui', plugins_url('/assets/frontend/css/jquery-ui.css', __FILE__));
                $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
                wp_enqueue_script('jquery-latlon-picker', plugins_url('/assets/frontend/scripts/jquery_latlon_picker.js', __FILE__), '', '', false);
                wp_enqueue_script('foodbakery-map-styles', plugins_url('/assets/frontend/scripts/foodbakery-map-styles.js', __FILE__), '', '', true);

                  wp_enqueue_script('jquery-confirm', plugins_url('/assets/frontend/scripts/jquery-confirm.min.js', __FILE__), '', '', true);
            }

            // All JS files
            $google_api_key = '';
            if (isset($foodbakery_plugin_options['foodbakery_google_api_key']) && $foodbakery_plugin_options['foodbakery_google_api_key'] != '') {
                $google_api_key = '?key=' . $foodbakery_plugin_options['foodbakery_google_api_key'] . '&libraries=geometry,places,drawing&sensor=false';
            } else {
                $google_api_key = '?libraries=geometry,places,drawing&sensor=false';
            }

            wp_enqueue_script('google-autocomplete', 'https://maps.googleapis.com/maps/api/js' . $google_api_key);



            wp_register_script('foodbakery-icons-loader', plugins_url('/assets/common/js/icons-loader.js', __FILE__), array('jquery'));

            $foodbakery_icons_array = array(
                'plugin_url' => wp_foodbakery::plugin_url(),
            );
            wp_localize_script('foodbakery-icons-loader', 'icons_vars', $foodbakery_icons_array);
            wp_enqueue_script('foodbakery-icons-loader');

            // temprary off
            if (!is_admin()) {
                wp_enqueue_script('foodbakery_functions_frontend', plugins_url('/assets/frontend/scripts/functions.js', __FILE__));
                wp_enqueue_script('foodbakery-theia-sticky-sidebar', plugins_url('/assets/frontend/scripts/theia-sticky-sidebar.js', __FILE__));
                wp_localize_script(
                        'foodbakery_functions_frontend', 'foodbakery_globals', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'plugin_dir_url' => plugin_dir_url(__FILE__),
                    'security' => wp_create_nonce('foodbakery-security'),
                        )
                );
                wp_register_script('prettyPhoto', plugins_url('/assets/frontend/scripts/jquery.prettyPhoto.js', __FILE__), array('jquery'));

                wp_register_script('foodbakery-tags-it', plugins_url('/assets/frontend/scripts/tag-it.js', __FILE__));

                wp_register_script('foodbakery-restaurant-functions', plugins_url('/assets/frontend/scripts/restaurant-functions.js', __FILE__), array('jquery'));
                wp_enqueue_script('responsive-menu', plugins_url('/assets/frontend/scripts/responsive.menu.js', __FILE__), '', '', true);
                wp_enqueue_script('foodbakery-growls', plugins_url('/assets/frontend/scripts/jquery.growl.js', __FILE__), '', '', true);
                wp_register_script('foodbakery-restaurant-add', plugins_url('/assets/frontend/scripts/restaurant-add-functions.js', __FILE__), '', '', true);
                wp_register_script('foodbakery-restaurant-menus', plugins_url('/assets/frontend/scripts/restaurant-add-menus-functions.js', __FILE__), '', '', true);
                wp_register_script('foodbakery-booking-functions', plugins_url('/assets/frontend/scripts/booking-functions.js', __FILE__));
                wp_register_script('foodbakery-orders-functions', plugins_url('/assets/frontend/scripts/orders-functions.js', __FILE__));
                wp_register_script('foodbakery-restaurant-single', plugins_url('/assets/frontend/scripts/restaurant-single.js', __FILE__), '', '', true);
                wp_localize_script(
                        'foodbakery-restaurant-single', 'foodbakery_singles_gl', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'plugin_dir_url' => plugin_dir_url(__FILE__),
                    'select_menu_items' => esc_html__('Please select Required menu items.', 'foodbakery'),
                    'add_to_menu' => esc_html__('Add to Menu', 'foodbakery'),
                    'update' => esc_html__('Update', 'foodbakery'),
                        )
                );
                /*
                 * Icons style and script
                 */
                wp_register_script('fonticonpicker', plugins_url('/assets/icomoon/js/jquery.fonticonpicker.min.js', __FILE__));

                $foodbakery_restaurant_strings = array(
                    'service_added' => esc_html__('Service added to List.', 'foodbakery'),
                    'off_day_added' => esc_html__('Off day added to List.', 'foodbakery'),
                    'buy_exist_packg' => esc_html__('Use Existing Membership', 'foodbakery'),
                    'buy_new_packg' => esc_html__('Buy New Membership', 'foodbakery'),
                    'off_day_added' => esc_html__('This date is already added in off days list.', 'foodbakery'),
                    'upload_images_only' => esc_html__('Please upload images only.', 'foodbakery'),
                    'upload_images_size' => esc_html__('Image size should not exceed more than 1mb.', 'foodbakery'),
                    'action_error' => esc_html__('Error! There is some Problem.', 'foodbakery'),
                    'compulsory_fields' => esc_html__('Please fill the compulsory fields first.', 'foodbakery'),
                    'valid_price_error' => esc_html__('Please enter valid price.', 'foodbakery'),
                    'valid_amount_error' => esc_html__('Please enter valid amount.', 'foodbakery'),
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'plugin_dir_url' => plugin_dir_url(__FILE__),
                );
                wp_localize_script('foodbakery-restaurant-add', 'foodbakery_restaurant_strings', $foodbakery_restaurant_strings);

                wp_enqueue_script('jquery-ui', plugins_url('/assets/frontend/scripts/jquery-ui.js', __FILE__), '', '', false);

                //wp_enqueue_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js');
                // Dashboard Common
                wp_register_script('foodbakery-dashboard-common', plugins_url('/assets/frontend/scripts/dashboard-common.js', __FILE__), array(), '', true);

                // restaurant map js
                wp_register_script('map-infobox', plugins_url('/assets/frontend/scripts/map-infobox.js', __FILE__), '', '', true);
                wp_register_script('map-clusterer', plugins_url('/assets/frontend/scripts/markerclusterer.js', __FILE__), '', '', true);
                wp_register_script('foodbakery-restaurant-map', plugins_url('/assets/frontend/scripts/restaurant-map.js', __FILE__), '', '', true);

                wp_register_script('foodbakery-restaurant-top-map', plugins_url('/assets/frontend/scripts/restaurant-top-map.js', __FILE__), '', '', true);

                wp_register_script('scrollbar', plugins_url('/assets/frontend/scripts/jquery.scrollbar.js', __FILE__), '', '', true);
                wp_enqueue_script('bootstrap-slider', plugins_url('/assets/frontend/scripts/bootstrap-slider.js', __FILE__), '', '', true);

                wp_register_script('jquery-print', plugins_url('/assets/frontend/scripts/jQuery.print.js', __FILE__), '', '', true);

                do_action('foodbakery_enqueue_files_frontend');
            }

            wp_register_script('responsive-calendar', plugins_url('/assets/common/js/responsive-calendar.min.js', __FILE__), '', '', true);

            /**
             *
             * @login popup script files
             */
            /**
             *
             * @login popup script files
             */
            if (!function_exists('foodbakery_google_recaptcha_scripts')) {

                function foodbakery_google_recaptcha_scripts() {
                    wp_enqueue_script('foodbakery_google_recaptcha_scripts', foodbakery_server_protocol() . 'www.google.com/recaptcha/api.js?onload=foodbakery_multicap_all_functions&amp;render=explicit', '', '');
                }

            }

            //jquery text editor files
            if (is_admin()) {
                wp_enqueue_style('jquery-te', plugins_url('/assets/common/css/jquery-te-1.4.0.css', __FILE__));
                wp_enqueue_script('jquery-te', plugins_url('/assets/common/js/jquery-te-1.4.0.min.js', __FILE__), '', '', true);
            }

            if (!is_admin()) {
                wp_register_style('jquery-te', plugins_url('/assets/common/css/jquery-te-1.4.0.css', __FILE__));
                wp_enqueue_script('jquery-te', plugins_url('/assets/common/js/jquery-te-1.4.0.min.js', __FILE__));
            }

            //jquery text editor files end

            if (is_admin()) {
                // admin css files
                global $price_tables_meta_object;
                wp_enqueue_style('datatable', plugins_url('/assets/backend/css/datatable.css', __FILE__));
                wp_enqueue_style('fonticonpicker', plugins_url('/assets/icomoon/css/jquery.fonticonpicker.min.css', __FILE__));
                wp_enqueue_style('iconmoon', plugins_url('/assets/icomoon/css/iconmoon.css', __FILE__));
                wp_enqueue_style('foodbakery_fonticonpicker_bootstrap_css', plugins_url('/assets/icomoon/theme/bootstrap-theme/jquery.fonticonpicker.bootstrap.css', __FILE__));
                wp_enqueue_style('bootstrap', plugins_url('/assets/backend/css/bootstrap.css', __FILE__));
                wp_enqueue_style('chosen', plugins_url('/assets/backend/css/chosen.css', __FILE__));
                wp_enqueue_style('foodbakery_bootstrap_calendar_css', plugins_url('/assets/backend/css/bootstrap-year-calendar.css', __FILE__));
                wp_enqueue_style('foodbakery_price_tables', plugins_url('/assets/backend/css/price-tables.css', __FILE__));
                wp_enqueue_style('wp-color-picker');
                // admin js files
                wp_enqueue_script('foodbakery_datatable_js', plugins_url('/assets/backend/scripts/datatable.js', __FILE__), '', '', true);
                wp_enqueue_script('chosen', plugins_url('/assets/common/js/chosen.jquery.js', __FILE__));

                wp_enqueue_script('chosen-order-jquery', plugins_url('/assets/common/js/chosen.order.jquery.js', __FILE__));

                wp_enqueue_script('chosen-ajaxify', plugins_url('/assets/backend/scripts/chosen-ajaxify.js', __FILE__));
                $foodbakery_pt_array = array(
                    'plugin_url' => wp_foodbakery::plugin_url(),
                );
                wp_localize_script('chosen-ajaxify', 'foodbakery_chosen_vars', $foodbakery_pt_array);

                wp_enqueue_script('foodbakery_bootstrap_calendar_js', plugins_url('/assets/backend/scripts/bootstrap-year-calendar.js', __FILE__));

                wp_enqueue_script('foodbakery_custom_wp_admin_script_js', plugins_url('/assets/backend/scripts/functions.js', __FILE__), array('wp-color-picker'), '', true);
                wp_localize_script(
                        'foodbakery_custom_wp_admin_script_js', 'foodbakery_globals', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'plugin_url' => wp_foodbakery::plugin_url(),
                    'security' => wp_create_nonce('foodbakery-security'),
                        )
                );

                wp_enqueue_script('foodbakery__shortcodes_js', plugins_url('/assets/backend/scripts/shortcode-functions.js', __FILE__), '', '', true);
                wp_enqueue_script('foodbakery-restaurant-add-menus', plugins_url('/assets/backend/scripts/restaurant-add-menus-functions.js', __FILE__), '', '', true);
                $foodbakery_restaurant_strings = array(
                    'service_added' => esc_html__('Service added to List.', 'foodbakery'),
                    'off_day_added' => esc_html__('Off day added to List.', 'foodbakery'),
                    'buy_exist_packg' => esc_html__('Use Existing Membership', 'foodbakery'),
                    'buy_new_packg' => esc_html__('Buy New Membership', 'foodbakery'),
                    'off_day_added' => esc_html__('This date is already added in off days list.', 'foodbakery'),
                    'upload_images_only' => esc_html__('Please upload images only.', 'foodbakery'),
                    'upload_images_size' => esc_html__('Image size should not exceed more than 1mb.', 'foodbakery'),
                    'action_error' => esc_html__('Error! There is some Problem.', 'foodbakery'),
                    'compulsory_fields' => esc_html__('Please fill the compulsory fields first.', 'foodbakery'),
                    'valid_price_error' => esc_html__('Please enter valid price.', 'foodbakery'),
                );
                wp_localize_script('foodbakery-restaurant-add-menus', 'foodbakery_restaurant_strings', $foodbakery_restaurant_strings);
                wp_localize_script(
                        'restaurant-add-menus-functions', 'foodbakery_globals', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'plugin_dir_url' => plugin_dir_url(__FILE__),
                    'security' => wp_create_nonce('foodbakery-security'),
                        )
                );
                wp_enqueue_script('fonticonpicker', plugins_url('/assets/icomoon/js/jquery.fonticonpicker.min.js', __FILE__));

                wp_register_script('foodbakery-price-tables', plugins_url('/assets/backend/scripts/price-tables.js', __FILE__), '', '', true);
                $foodbakery_pt_array = array(
                    'plugin_url' => wp_foodbakery::plugin_url(),
                    'ajax_url' => esc_url(admin_url('admin-ajax.php')),
                    'packages_dropdown' => $price_tables_meta_object->foodbakery_pkgs(),
                );
                wp_localize_script('foodbakery-price-tables', 'foodbakery_pt_vars', $foodbakery_pt_array);
                wp_enqueue_script('foodbakery-price-tables');


                wp_enqueue_style('datetimepicker', plugins_url('/assets/common/css/jquery_datetimepicker.css', __FILE__));
                wp_enqueue_script('datetimepicker', plugins_url('/assets/common/js/jquery.datetimepicker.js', __FILE__), '', '', true);

            }

            wp_enqueue_style('datetimepicker', plugins_url('/assets/common/css/jquery_datetimepicker.css', __FILE__));
            wp_enqueue_script('datetimepicker', plugins_url('/assets/common/js/jquery_datetimepicker.js', __FILE__), '', '', true);
            wp_register_script('modernizr-custom-js', plugins_url('/assets/frontend/scripts/modernizr-custom.js', __FILE__), '', '', true);
            wp_register_style('bootstrap-datepicker', plugins_url('/assets/frontend/css/bootstrap-datepicker.css', __FILE__));
            wp_register_script('bootstrap-datepicker', plugins_url('/assets/frontend/scripts/bootstrap-datepicker.js', __FILE__), '', '', true);

            wp_register_style('daterangepicker', plugins_url('/assets/frontend/css/daterangepicker.css', __FILE__));
            wp_register_script('daterangepicker-moment', plugins_url('/assets/frontend/scripts/moment.js', __FILE__), '', '', true);
            wp_register_script('daterangepicker', plugins_url('/assets/frontend/scripts/daterangepicker.js', __FILE__), '', '', true);

            /**
             *
             * @social login script
             */
            if (!function_exists('foodbakery_socialconnect_scripts')) {

                function foodbakery_socialconnect_scripts() {
                    wp_enqueue_script('foodbakery_socialconnect_js', plugins_url('/elements/login/cs-social-login/media/js/cs-connect.js', __FILE__), '', '', true);
                }

            }

            // Register Location Autocomplete for late use.
            wp_register_script('foodbakery_location_autocomplete_js', plugins_url('/assets/common/js/jquery.location-autocomplete.js', __FILE__), '', '', true);
            wp_enqueue_script('foodbakery_location_autocomplete_js');
            /**
             *
             * @google auto complete script
             */
            if (!function_exists('foodbakery_google_autocomplete_scripts')) {

                function foodbakery_google_autocomplete_scripts() {
                    wp_enqueue_script('foodbakery_location_autocomplete_js', plugins_url('/assets/common/js/jquery.location-autocomplete.js', __FILE__), '', '');
                }

            }

            // get user inline style
        }

        public function foodbakery_enqueue_admin_style_sheet() {
            wp_enqueue_style('foodbakery_admin_header', 'https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600&subset=latin,cyrillic-ext', __FILE__); //admin style head import
            wp_enqueue_style('foodbakery_social-network', plugins_url('/assets/backend/css/social-network.css', __FILE__)); //admin style head import
            wp_enqueue_style('foodbakery-admin-style', plugins_url('/assets/backend/css/admin-style.css', __FILE__));
        }

        /**
         *
         * @Responsive Tabs Styles and Scripts
         */
        public static function foodbakery_enqueue_responsive_front_scripts() {


            $my_theme = wp_get_theme('foodbakery');
            if (!$my_theme->exists()) {
                if (is_rtl()) {
                    wp_enqueue_style('foodbakery_rtl_css', plugins_url('/assets/frontend/css/rtl.css', __FILE__));
                }
                wp_enqueue_style('foodbakery_responsive_css', plugins_url('/assets/frontend/css/responsive.css', __FILE__));
                wp_enqueue_style('foodbakery_front_responsive_css', plugins_url('/assets/frontend/css/front_resonsive_ansu.css', __FILE__));
 

            }
        }

        /**
         *
         * @Data Table Style Scripts
         */

        /**
         * Start Function how to Add table Style Script
         */
        public static function foodbakery_data_table_style_script() {
            wp_enqueue_style('foodbakery_data_table_css', plugins_url('/assets/frontend/css/jquery.data_tables.css', __FILE__));
        }

        /**
         * End Function how to Add Tablit Style Script
         */
        public static function foodbakery_jquery_ui_scripts() {
            
        }

        /**
         * Start Function how to Add Location Picker Scripts
         */
        public function foodbakery_location_gmap_script() {
            wp_enqueue_script('jquery-latlon-picker', plugins_url('/assets/frontend/scripts/jquery_latlon_picker.js', __FILE__), '', '', true);
        }

        /**
         * Start Function how to Add Google Place Scripts
         */
        public function foodbakery_google_place_scripts() {
            global $foodbakery_plugin_options;
            $google_api_key = '';
            if (isset($foodbakery_plugin_options['foodbakery_google_api_key']) && $foodbakery_plugin_options['foodbakery_google_api_key'] != '') {
                $google_api_key = '?key=' . $foodbakery_plugin_options['foodbakery_google_api_key'] . '&libraries=geometry,places,drawing';
            } else {
                $google_api_key = '?libraries=geometry,places,drawing';
            }
            // wp_enqueue_script('cs_google_autocomplete_script', 'https://maps.googleapis.com/maps/api/js' . $google_api_key);
        }

        // start function for google map files 

        /**
         * Start Function how to Add Google Autocomplete Scripts
         */
        public function foodbakery_autocomplete_scripts() {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script('jquery-ui-slider');
        }

        // Start function for global code
        public function foodbakery_all_scodes() {
            global $foodbakery_jh_scodes;
        }

        // Start function for auto login user
        public function foodbakery_auto_login_user() {
            
        }

        public static $email_template_type = 'general';
        public static $email_default_template = 'Hello! I am general email template by [COMPANY_NAME].';
        public static $email_template_variables = array(
            array(
                'tag' => 'SITE_NAME',
                'display_text' => 'Site Name',
                'value_callback' => array('wp_foodbakery', 'foodbakery_get_site_name'),
            ),
            array(
                'tag' => 'ADMIN_EMAIL',
                'display_text' => 'Admin Email',
                'value_callback' => array('wp_foodbakery', 'foodbakery_get_admin_email'),
            ),
            array(
                'tag' => 'SITE_URL',
                'display_text' => 'SITE URL',
                'value_callback' => array('wp_foodbakery', 'foodbakery_get_site_url'),
            ),
        );

        public function email_template_settings_callback($email_template_options) {
            $email_template_options['types'][] = self::$email_template_type;
            $email_template_options['templates']['general'] = self::$email_default_template;
            $email_template_options['variables']['General'] = self::$email_template_variables;

            return $email_template_options;
        }

        /*
         * Fetching Plugin Option for specific option ID
         * @ @option_id is the option you want to get status for
         */

        public function foodbakery_get_plugin_options_callback($option_id = '') {
            if (isset($option_id) && $option_id != '') {
                $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
                if (isset($foodbakery_plugin_options[$option_id])) {
                    return $foodbakery_plugin_options[$option_id];
                }
            }
            return false;
        }

        public static function foodbakery_get_site_name() {
            return get_bloginfo('name');
        }

        public static function foodbakery_get_admin_email() {
            return get_bloginfo('admin_email');
        }

        public static function foodbakery_get_site_url() {
            return get_bloginfo('url');
        }

        public static function foodbakery_replace_tags($template, $variables) {
            // Add general variables to the list
            $variables = array_merge(self::$email_template_variables, $variables);


            foreach ($variables as $key => $variable) {
                $callback_exists = false;

                // Check if function/method exists.
                if (is_array($variable['value_callback'])) { // If it is a method of a class.
                    $callback_exists = method_exists($variable['value_callback'][0], $variable['value_callback'][1]);
                } else { // If it is a function.
                    $callback_exists = function_exists($variable['value_callback']);
                }

                // Substitute values in place of tags if callback exists.
                if (true == $callback_exists) {
                    // Make a call to callback to get value.
                    $value = call_user_func($variable['value_callback']);

                    // If we have some value to substitute then use that.
                    if (false != $value) {
                        $template = str_replace('[' . $variable['tag'] . ']', $value, $template);
                    }
                }
            }
            return $template;
        }

        public static function get_template($email_template_index, $email_template_variables, $email_default_template) {
            $email_template = '';
            $template_data = array('subject' => '', 'from' => '', 'recipients' => '', 'email_notification' => '', 'email_type' => '', 'email_template' => '');
            // Check if there is a template select else go with default template.
            $selected_template_id = foodbakery_check_if_template_exists($email_template_index, 'jh-templates');
            if (false != $selected_template_id) {

                // Check if a temlate selected else default template is used.
                if ($selected_template_id != 0) {
                    $templateObj = get_post($selected_template_id);
                    if ($templateObj != null) {
                        $email_template = $templateObj->post_content;
                        $template_id = $templateObj->ID;
                        $template_data['subject'] = wp_foodbakery::foodbakery_replace_tags(get_post_meta($template_id, 'jh_subject', true), $email_template_variables);
                        $template_data['from'] = wp_foodbakery::foodbakery_replace_tags(get_post_meta($template_id, 'jh_from', true), $email_template_variables);
                        $template_data['recipients'] = wp_foodbakery::foodbakery_replace_tags(get_post_meta($template_id, 'jh_recipients', true), $email_template_variables);
                        $template_data['email_notification'] = get_post_meta($template_id, 'jh_email_notification', true);
                        $template_data['email_type'] = get_post_meta($template_id, 'jh_email_type', true);
                    }
                } else {
                    // Get default template.
                    $email_template = $email_default_template;
                    $template_data['email_notification'] = 1;
                }
            } else {
                $email_template = $email_default_template;
                $template_data['email_notification'] = 1;
            }

            $email_template = wp_foodbakery::foodbakery_replace_tags($email_template, $email_template_variables);
            $template_data['email_template'] = $email_template;
            return $template_data;
        }

        public static function plugin_path() {
            return untrailingslashit(plugin_dir_path(__FILE__));
        }

        public static function template_path() {
            return apply_filters('foodbakery_plugin_template_path', 'wp-foodbakery/');
        }

        function create_restaurant_settings() {
            if (isset($_GET["post_type"]) && $_GET["post_type"] == "restaurant-type") {
                $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
                if (empty($restaurants_type_post)) {
                    $reg_post = array(
                        'post_title' => 'Restaurant Settings',
                        'post_type' => 'restaurant-type',
                        'post_status' => 'publish',
                    );
                    wp_insert_post($reg_post);
                }
            }
        }

        public static function get_terms_and_conditions_field($label = '', $field_name = '', $show_accept = true) {
            global $foodbakery_plugin_options;
            $label_privacy = '';
            $label = ( $label == '' ? 'Terms & Conditions' : $label );
            $label_privacy = ( $label_privacy == '' ? 'Privacy Policy' : $label_privacy );
            $field_name = ( $field_name == '' ? 'terms_and_conditions' : $field_name );

            $terms_condition_check = isset($foodbakery_plugin_options['foodbakery_cs_terms_condition_check']) ? $foodbakery_plugin_options['foodbakery_cs_terms_condition_check'] : '';
            ob_start();
            if ($terms_condition_check == 'on') {
                $terms_condition_page = isset($foodbakery_plugin_options['cs_terms_condition']) ? $foodbakery_plugin_options['cs_terms_condition'] : '';
                $privacy_policy_page = isset($foodbakery_plugin_options['cs_privacy_policy']) ? $foodbakery_plugin_options['cs_privacy_policy'] : '';
                ?>
                <div class="checkbox-area">
                    <input type="checkbox" id="<?php echo ($field_name); ?>" name="<?php echo ($field_name); ?>" class="foodbakery-dev-req-field">
                    <label for="<?php echo ($field_name); ?>">
                        <?php
                        if ($show_accept) {
                            _e('By Registering You Confirm That You Accept The', 'foodbakery');
                        }
                        ?>
                        <a target="_blank" href="<?php echo esc_url(get_permalink($terms_condition_page)); ?>">
                            <?php echo esc_html__($label, 'foodbakery'); ?>
                        </a>
                        <?php echo esc_html__('And', 'foodbakery'); ?>
                        <a target="_blank" href="<?php echo esc_url(get_permalink($privacy_policy_page)); ?>">
                            <?php echo esc_html__($label_privacy, 'foodbakery'); ?>
                        </a>
                    </label>
                </div>
                <?php
            }
            return ob_get_clean();
        }

        public static function is_demo_user_modification_allowed($post_name = '') {
            global $foodbakery_plugin_options, $post;

            if ('publisher-dashboard.php' === foodbakery_get_current_template() || 'publisher-dashboard.php' === $post_name) {
                $foodbakery_demo_user_login_switch = isset($foodbakery_plugin_options['foodbakery_demo_user_login_switch']) ? $foodbakery_plugin_options['foodbakery_demo_user_login_switch'] : '';
                if ($foodbakery_demo_user_login_switch == 'on') {
                    $foodbakery_foodbakery_demo_user_publisher = isset($foodbakery_plugin_options['foodbakery_job_demo_user_publisher']) ? $foodbakery_plugin_options['foodbakery_job_demo_user_publisher'] : '';
                    $foodbakery_demo_user_buyer = isset($foodbakery_plugin_options['foodbakery_demo_user_buyer']) ? $foodbakery_plugin_options['foodbakery_demo_user_buyer'] : '';
                    $current_user_id = get_current_user_id();
                    if ($foodbakery_foodbakery_demo_user_publisher == $current_user_id || $foodbakery_demo_user_buyer == $current_user_id) {
                        if (isset($foodbakery_plugin_options['foodbakery_demo_user_modification_allowed_switch']) && $foodbakery_plugin_options['foodbakery_demo_user_modification_allowed_switch'] == 'off') {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

    }

}

function foodbakery_get_current_template($echo = false) {
    if (!isset($GLOBALS['current_theme_template']))
        return false;
    if ($echo)
        echo $GLOBALS['current_theme_template'];
    else
        return $GLOBALS['current_theme_template'];
}

add_filter('template_include', 'foodbakery_template_include', 1000);

function foodbakery_template_include($t) {
    $GLOBALS['current_theme_template'] = basename($t);
    return $t;
}

/*
 * Check if an email template exists
 */
if (!function_exists('foodbakery_check_if_template_exists')) {

    function foodbakery_check_if_template_exists($slug, $type) {
        global $wpdb;
        $post = $wpdb->get_row("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "' && post_type = '" . $type . "'", 'ARRAY_A');
        if (isset($post) && isset($post['ID'])) {
            return $post['ID'];
        } else {
            return false;
        }
    }

}

/**
 *
 * @Create Object of class To Activate Plugin
 */
if (class_exists('wp_foodbakery')) {
    global $foodbakery_Class;
    $foodbakery_Class = new wp_foodbakery();
    register_activation_hook(__FILE__, array('wp_foodbakery', 'activate'));
    register_deactivation_hook(__FILE__, array('wp_foodbakery', 'deactivate'));
}

//Remove Sub Menu add new job
function modify_menu() {
    global $submenu;

    if (isset($submenu['edit.php?post_type=restaurants'][10])) {
        unset($submenu['edit.php?post_type=restaurants'][10]);
    }
    if (isset($submenu['edit.php?post_type=publishers'][10])) {
        unset($submenu['edit.php?post_type=publishers'][10]);
    }
    if (isset($submenu['edit.php?post_type=packages'][10])) {
        unset($submenu['edit.php?post_type=packages'][10]);
    }
    if (isset($submenu['edit.php?post_type=orders_inquiries'][10])) {
        unset($submenu['edit.php?post_type=orders_inquiries'][10]);
    }
    if (isset($submenu['edit.php?post_type=foodbakery_reviews'][10])) {
        unset($submenu['edit.php?post_type=foodbakery_reviews'][10]);
    }
    if (isset($submenu['foodbakery'][1][2])) {
        $submenu['foodbakery'][1][2] = 'http://chimpgroup.com/foodbakery-theme';
    }
    if (isset($submenu['foodbakery'][2][2])) {
        $submenu['foodbakery'][2][2] = 'http://chimpgroup.com/wp-demo/documentation/documentation/food-bakery-theme-guide';
    }
    if (isset($submenu['foodbakery'][3][2])) {
        $submenu['foodbakery'][3][2] = 'http://chimpgroup.com/support';
    }
    if (isset($submenu['foodbakery'][4][2])) {
        $submenu['foodbakery'][4][2] = 'http://chimpgroup.com/crm/index.php/quotation';
    }
}

add_action('admin_menu', 'modify_menu', 25);


    // Add theme option
function add_custom_option($wp_customize){
 // header option add 
 
    // add button option
        $wp_customize->add_section('mobile_sticky_option', array(
            'title'    =>'Mobile Sticky Menu'       
        ));
        // m_bg_color
         $wp_customize->add_setting(
              'm_bg_color', //give it an ID
              array(
                  'default' => '#ffffff', // Give it a default
              )
          );
          $wp_customize->add_control(
             new WP_Customize_Color_Control(
                 $wp_customize,
                 'm_bg_color', //give it an ID
                 array(
                     'label'      => __( 'Sticky background color', 'mythemename' ), //set the label to appear in the Customizer
                     'section'    => 'mobile_sticky_option', //select the section for it to appear under  
                     'settings'   => 'm_bg_color' //pick the setting it applies to
                 )
             )
          );

          // m_font_color
         $wp_customize->add_setting(
              'm_font_color', //give it an ID
              array(
                  'default' => '#c33332', // Give it a default
              )
          );
          $wp_customize->add_control(
             new WP_Customize_Color_Control(
                 $wp_customize,
                 'm_font_color', //give it an ID
                 array(
                     'label'      => __( 'Sticky font color', 'mythemename' ), //set the label to appear in the Customizer
                     'section'    => 'mobile_sticky_option', //select the section for it to appear under  
                     'settings'   => 'm_font_color' //pick the setting it applies to
                 )
             )
          );

              // m_bottom_color
         $wp_customize->add_setting(
              'm_bottom_color', //give it an ID
              array(
                  'default' => '#c33332', // Give it a default
              )
          );
          $wp_customize->add_control(
             new WP_Customize_Color_Control(
                 $wp_customize,
                 'm_bottom_color', //give it an ID
                 array(
                     'label'      => __( 'Sticky divider color', 'mythemename' ), //set the label to appear in the Customizer
                     'section'    => 'mobile_sticky_option', //select the section for it to appear under  
                     'settings'   => 'm_bottom_color' //pick the setting it applies to
                 )
             )
          );
 }
 add_action('customize_register', 'add_custom_option');


add_action('customize_register', 'add_custom_option2');

 // Add theme option
function add_custom_option2($wp_customize){
   
       $wp_customize->add_section('card_payment_option_ansu', array(
            'title'    =>'Card Payment Option',
            'description' => __('Card Payment Enable/Disable.', 'parsmizban'), //Descriptive tooltip       
        ));
        // m_bg_color
         $wp_customize->add_setting(
              'card_payment_status', //give it an ID
              array(
                  'default' => 'yes', // Give it a default
                  'transport'  => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
              )
          );
         $wp_customize->add_control( 'card_payment_status', array(
        'type' => 'radio',
        'section' => 'card_payment_option_ansu',
        'label' => __( 'Card Payment Enable/Disable' ),
        'description' => __( 'Card Payment option' ),
        'choices' => array(
            'yes' => __( 'Enable' ),
            'no' => __( 'Disable' ),
        ),
    ) );
  
}

add_action('admin_head', 'my_custom_fonts',9999);

function my_test() {
    wp_enqueue_style( 'front_resonsive_ansu', plugin_dir_url( __FILE__ ) . '/assets/frontend/css/front_resonsive_ansu.css' );
}

add_action( 'wp_enqueue_scripts', 'my_test',9999);

function my_custom_fonts() {
  echo '<style>
    .customize-control-content .wp-picker-holder {
        position: relative !important;
    }
  </style>';
}

function create_daily_restaurants_check() {
    // Use wp_next_scheduled to check if the event is already scheduled.
    $timestamp = wp_next_scheduled('create_daily_restaurants_check');

    // If $timestamp == false schedule daily alerts since it hasn't been done previously.
    if ($timestamp == false) {
        // Schedule the event for right now, then to repeat daily using the hook 'create_daily_restaurants_check'.
        wp_schedule_event(time(), 'daily', 'create_daily_restaurants_check');
    }
}

function remove_daily_restaurants_check() {
    wp_clear_scheduled_hook('remove_daily_restaurants_check');
}

// On plugin activation register daily cron job.
register_activation_hook(__FILE__, 'create_daily_restaurants_check');

// On plugin deactivation unregister daily cron job.
register_deactivation_hook(__FILE__, 'remove_daily_restaurants_check');
