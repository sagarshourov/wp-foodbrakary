<?php

/**
 * Start Function  how to Create Theme Options in Backend 
 */
if (!function_exists('foodbakery_settings_options_page')) {

    function foodbakery_settings_options_page() {

        global $foodbakery_setting_options, $foodbakery_form_fields, $gateways;
        $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
        $obj = new foodbakery_options_fields();
        $return = $obj->foodbakery_fields($foodbakery_setting_options);
        $foodbakery_opt_btn_array = array(
            'id' => '',
            'std' => esc_html__('Save All Settings', 'foodbakery'),
            'cust_id' => "submit_btn",
            'cust_name' => "submit_btn",
            'cust_type' => 'button',
            'classes' => 'bottom_btn_save',
            'extra_atr' => 'onclick="javascript:plugin_option_save(\'' . esc_js(admin_url('admin-ajax.php')) . '\');" ',
            'return' => true,
        );


        $foodbakery_opt_hidden1_array = array(
            'id' => '',
            'std' => 'plugin_option_save',
            'cust_id' => "",
            'cust_name' => "action",
            'return' => true,
        );


        $foodbakery_opt_hidden2_array = array(
            'id' => '',
            'std' => wp_foodbakery::plugin_url(),
            'cust_id' => "foodbakery_plugin_url",
            'cust_name' => "foodbakery_plugin_url",
            'return' => true,
        );

        $foodbakery_opt_btn_cancel_array = array(
            'id' => '',
            'std' => esc_html__('Reset All Options', 'foodbakery'),
            'cust_id' => "submit_btn",
            'cust_name' => "reset",
            'cust_type' => 'button',
            'classes' => 'bottom_btn_reset',
            'extra_atr' => 'onclick="javascript:foodbakery_rest_plugin_options(\'' . esc_js(admin_url('admin-ajax.php')) . '\');"',
            'return' => true,
        );

        $html = '
        <div class="theme-wrap fullwidth">
            <div class="inner">
                <div class="outerwrapp-layer">
                    <div class="loading_div" id="foodbakery_loading_msg_div"> <i class="icon-circle-o-notch icon-spin"></i> <br>
                        ' . esc_html__('Please Wait...', 'foodbakery') . '
                    </div>
                    <div class="form-msg"> <i class="icon-check-circle-o"></i>
                        <div class="innermsg"></div>
                    </div>
                </div>
                <div class="row">
                    <form id="plugin-options" method="post" enctype="multipart/form-data">
			<div class="col1">
                            <nav class="admin-navigtion">
                                <div class="logo"> <a href="javascript;;" class="logo1"><img src="' . esc_url(wp_foodbakery::plugin_url()) . 'assets/backend/images/logo.png" /></a> <a href="#" class="nav-button"><i class="icon-align-justify"></i></a> </div>
                                <ul>
                                    ' . force_balance_tags($return[1], true) . '
                                </ul>
                            </nav>
                        </div>
                        <div class="col2">
                        ' . force_balance_tags($return[0], true) . '
                        </div>

                        <div class="clear"></div>
                        <div class="footer">
                        ' . $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_btn_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden1_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden2_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_btn_cancel_array) . '
                                
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="clear"></div>';
        $html .= '<script type="text/javascript">
			// Sub Menus Show/hide
			jQuery(document).ready(function($) {
                jQuery(".sub-menu").parent("li").addClass("parentIcon");
                $("a.nav-button").click(function() {
                    $(".admin-navigtion").toggleClass("navigation-small");
                });                
                $("a.nav-button").click(function() {
                    $(".inner").toggleClass("shortnav");
                });                
                $(".admin-navigtion > ul > li > a").click(function() {
                    var a = $(this).next(\'ul\')
                    $(".admin-navigtion > ul > li > a").not($(this)).removeClass("changeicon")
                    $(".admin-navigtion > ul > li ul").not(a) .slideUp();
                    $(this).next(\'.sub-menu\').slideToggle();
                    $(this).toggleClass(\'changeicon\');
                });
                $(\'[data-toggle="popover"]\').popover(\'destroy\');
            });            
            function show_hide(id){
				var link = id.replace("#", "");
                jQuery(\'.horizontal_tab\').fadeOut(0);
                jQuery("#"+link).fadeIn(400);
            }            
            function toggleDiv(id) { 
                jQuery(\'.col2\').children().hide();
                jQuery(id).show();
                location.hash = id+"-show";
                var link = id.replace("#", "");
                jQuery(\'.categoryitems li\').removeClass(\'active\');
                jQuery(".menuheader.expandable") .removeClass(\'openheader\');
                jQuery(".categoryitems").hide();
		jQuery("."+link).addClass(\'active\');
		jQuery("."+link) .parent("ul").show().prev().addClass("openheader");
                google.maps.event.trigger(document.getElementById("cs-map-location-id"), "resize");
            }
            jQuery(document).ready(function() {
                jQuery(".categoryitems").hide();
                jQuery(".categoryitems:first").show();
                jQuery(".menuheader:first").addClass("openheader");
                jQuery(".menuheader").on(\'click\', function(event) {
                    if (jQuery(this).hasClass(\'openheader\')){
                        jQuery(".menuheader").removeClass("openheader");
                        jQuery(this).next().slideUp(200);
                        return false;
                    }
                    jQuery(".menuheader").removeClass("openheader");
                    jQuery(this).addClass("openheader");
                    jQuery(".categoryitems").slideUp(200);
                    jQuery(this).next().slideDown(200); 
                    return false;
                });                
                var hash = window.location.hash.substring(1);
                var id = hash.split("-show")[0];
                if (id){
                    jQuery(\'.col2\').children().hide();
                    jQuery("#"+id).show();
                    jQuery(\'.categoryitems li\').removeClass(\'active\');
                    jQuery(".menuheader.expandable") .removeClass(\'openheader\');
                    jQuery(".categoryitems").hide();
                    jQuery("."+id).addClass(\'active\');
                    jQuery("."+id) .parent("ul").slideDown(300).prev().addClass("openheader");
                } 
            });
            
        </script>';
        echo force_balance_tags($html, true);
    }

    /**
     * end Function  how to Create Theme Options in Backend 
     */
}
/**
 * Start Function  how to Create Theme Options setting in Backend 
 */
if (!function_exists('foodbakery_settings_option')) {

    function foodbakery_settings_option() {
        global $foodbakery_setting_options, $gateways;
        $foodbakery_theme_menus = get_registered_nav_menus();
        $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
        $on_off_option = array("show" => "on", "hide" => "off");

        $foodbakery_min_days = array();
        for ($days = 1; $days < 11; $days ++) {
            $foodbakery_min_days[$days] = "$days day";
        }


        $foodbakery_setting_options[] = array(
            "name" => esc_html__("General Options", "foodbakery"),
            "fontawesome" => 'icon-build',
            "id" => "tab-general-page-settings",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );


        // publisher settings
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Publisher Settings", "foodbakery"),
            "fontawesome" => 'icon-book',
            "id" => "tab-publisher-settings",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Gateways", "foodbakery"),
            "fontawesome" => 'icon-wallet2',
            "id" => "tab-gateways-settings",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Api Settings", "foodbakery"),
            "fontawesome" => 'icon-ioxhost',
            "id" => "tab-api-setting",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__('Map Settings', 'foodbakery'),
            "fontawesome" => 'icon-map-marker',
            "id" => "tab-general-default-location",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__('Locations Settings', 'foodbakery'),
            "fontawesome" => 'icon-location-arrow',
            "id" => "tab-backend-settings",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );


        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Social Auto Post", "foodbakery"),
            "fontawesome" => 'icon-comments-o',
            "id" => "tab-autopost-setting",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );


        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Social Icon", "foodbakery"),
            "fontawesome" => 'icon-device_hub',
            "id" => "tab-social-icons",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Social Login", "foodbakery"),
            "fontawesome" => 'icon-user',
            "id" => "tab-social-login-setting",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Ads Management", "foodbakery"),
            "fontawesome" => 'icon-list2',
            "id" => "tab-ads-management-setting",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );
        // Foodbakery Plugin Option Smtp Tab.
        $foodbakery_setting_options = apply_filters('foodbakery_plugin_option_smtp_tab', $foodbakery_setting_options);


        // General Settings
        $foodbakery_setting_options[] = array("name" => esc_html__("General Options", "foodbakery"),
            "id" => "tab-general-page-settings",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("General Options", "foodbakery") . '"',
            "type" => "sub-heading",
            "help_text" => "",
        );
        $foodbakery_setting_options[] = array("name" => esc_html__('General Options', 'foodbakery'),
            "id" => "tab-user-settings",
            "std" => esc_html__('General Options', 'foodbakery'),
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("User Header Login", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Dashboard and Front-End login/register option can be hide by turning off this switch.", "foodbakery"),
            "id" => "user_dashboard_switchs",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Publisher Dashboard", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Select page for publisher dashboard here. This page is set in page template drop down. To create publisher dashboard page, go to Pages > Add new page, set the page template to 'publisher' in the right menu.", "foodbakery"),
            "id" => "foodbakery_publisher_dashboard",
            "std" => "",
            "type" => "select_dashboard",
            "custom" => true,
            "options" => '',
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Dashboard Pagination", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "publisher_dashboard_pagination",
            "std" => "20",
            "type" => "text",
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Memberships Detail Page", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Please select a page for package details. This page is set in page template drop down. To create publisher dashboard page, go to Pages > Add new page, set the page template to 'publisher' in the right menu.", "foodbakery"),
            "id" => "foodbakery_package_page",
            "std" => "",
            "type" => "select_dashboard",
            "custom" => true,
            "options" => '',
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Login Page", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "foodbakery_login_page",
            "std" => "",
            "type" => "select_dashboard",
            "custom" => true,
            "options" => '',
        );

        /*
         * Deafault Locations
         */
        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Default Locations', 'foodbakery'),
            'id' => 'default-locations',
            'std' => 'Default Locations',
            'type' => 'section',
            'options' => ''
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Header Location", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Enable/Disable header location", "foodbakery"),
            "id" => "hedaer_location_switch",
            "std" => "off",
            "onchange" => "foodbakery_default_location_check()",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array(
            "type" => "division",
            'enable_id' => 'foodbakery_hedaer_location_switch',
            'enable_val' => 'on',
            'extra_atts' => 'id="foodbakery_head_location"',
            'auto_enable' => true,
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__('Default Locations', 'foodbakery'),
            "desc" => "",
            "hint_text" => '',
            'id' => 'default_locations_list',
            'classes' => 'chosen-select',
            "std" => "",
            "type" => "default_locations_list"
        );

        $foodbakery_setting_options[] = array(
            "type" => "division_close",
        );

        /*
         * End Deafault Locations
         */



        /*
         * Header Restaurent Type
         */
        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Header Restaurent Type', 'foodbakery'),
            'id' => 'header-restaurent-type',
            'std' => 'Header Restaurent Type',
            'type' => 'section',
            'options' => ''
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Header Restaurant Type", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Enable/Disable header restaurant type", "foodbakery"),
            "id" => "hedaer_restaurant_switch",
            "std" => "off",
            "type" => "checkbox",
            "onchange" => "foodbakery_header_restaurent_type()",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array(
            "type" => "division",
            'enable_id' => 'foodbakery_hedaer_restaurant_switch',
            'enable_val' => 'on',
            'extra_atts' => 'id="foodbakery_head_restaurent_type"',
            'auto_enable' => true,
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__('Popular  Cousines', 'foodbakery'),
            'desc' => '',
            'hint_text' => '',
            'id' => 'default_cousins_list',
            'cust_name' => 'default_cousins_list[]',
            'std' => '',
            'type' => 'default_cousine_list',
        );
        $foodbakery_setting_options[] = array(
            "type" => "division_close",
        );

        /*
         * End Header Restaurent Type
         */




        /*
         * header Button options
         */
        $foodbakery_setting_options[] = array("name" => esc_html__("Header Button", "foodbakery"),
            "id" => "tab-getting-started-options",
            "std" => esc_html__("Header Button", "foodbakery"),
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Getting Started Button", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("", "foodbakery"),
            "id" => "header_buton_switch",
            "std" => "off",
            "type" => "checkbox",
            "onchange" => "foodbakery_getting_startrd()",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array(
            "type" => "division",
            'enable_id' => 'foodbakery_header_buton_switch',
            'enable_val' => 'on',
            'extra_atts' => 'id="foodbakery_head_btn"',
            'auto_enable' => true,
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Button Title", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__('', "foodbakery"),
            "id" => "header_button_title",
            "std" => '',
            "type" => "text",
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Button URL", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__('', "foodbakery"),
            "id" => "header_button_url",
            "std" => '',
            "type" => "text",
        );
        $foodbakery_setting_options[] = array(
            "type" => "division_close",
        );


        $foodbakery_setting_options[] = array("name" => esc_html__("Sent Emails", "foodbakery"),
            "id" => "tab-sent-emails",
            "std" => esc_html__("Sent Emails", "foodbakery"),
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Sent Email Logs", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Enable/Disable sent email logs", "foodbakery"),
            "id" => "sent_email_logs",
            "std" => "off",
            "type" => "checkbox",
            "options" => $on_off_option
        );


        $foodbakery_setting_options[] = array("name" => esc_html__("Job Settings", "foodbakery"),
            "id" => "tab-job-options",
            "std" => esc_html__("Restaurants Settings", "foodbakery"),
            "type" => "section",
            "options" => ""
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Free Restaurants Posting", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "free_restaurants_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Restaurant Expiry Duration ( Days )", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__('Set free restaurant posting expiry duration in days', "foodbakery"),
            "id" => "restaurant_default_expiry",
            "std" => '',
            "type" => "text",
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__('Restaurant Cover Image', "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_cover_image",
            "std" => "",
            "type" => "upload"
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Cover Padding Top", 'foodbakery'),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_cover_pading_top",
            "std" => '',
            "type" => "text",
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Cover Padding Bottom", 'foodbakery'),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_cover_pading_botom",
            "std" => '',
            "type" => "text",
        );

        $foodbakery_setting_options[] = array("name" => esc_html__('Submissions', 'foodbakery'),
            "id" => "tab-settings-submissions",
            "std" => esc_html__('Submissions', 'foodbakery'),
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Search Result Page", 'foodbakery'),
            "desc" => '',
            "hint_text" => esc_html__("Set the specific page where you want to show search results. The slected page must have restaurants page element on it. (Add restaurants page element while creating the restaurant search result page).", 'foodbakery'),
            "id" => "foodbakery_search_result_page",
            "std" => '',
            "type" => "select_dashboard",
            "custom" => true,
            "options" => ''
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Restaurants Publish/Pending On/Off", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Turn this switcher OFF to allow direct publishing of submitted restaurants by publisher without review / moderation. If this switch is ON, restaurants will be published after admin review / moderation.", "foodbakery"),
            "id" => "restaurants_review_option",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array("name" => esc_html__('Terms & Conditions', 'foodbakery'),
            "id" => "tab-settings-submissions",
            "std" => esc_html__('Terms & Conditions', 'foodbakery'),
            "type" => "section",
            "options" => ""
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Terms & Conditions On/Off", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Turn this switcher ON/OFF to show/hide terms and conditions check in forms.", "foodbakery"),
            "id" => "cs_terms_condition_check",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Terms and Conditions", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Select page for Terms and Conditions here. This page is set in page template drop down.", "foodbakery"),
            "id" => "cs_terms_condition",
            "std" => "",
            "type" => "select_dashboard",
            "custom" => true,
            "options" => '',
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Privacy Policy", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Select page for Privacy Policy here. This page is set in page template drop down.", "foodbakery"),
            "id" => "cs_privacy_policy",
            "std" => "",
            "type" => "select_dashboard",
            "custom" => true,
            "options" => '',
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Default Sidebars", "foodbakery"),
            "id" => "tab-announcements-options",
            "std" => esc_html__("Buyer Dashboard Announcements", "foodbakery"),
            "type" => "section",
            "options" => ""
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Announcement Background Color", 'foodbakery'),
            "desc" => "",
            "hint_text" => '',
            "id" => "announce_bg_color",
            "std" => "",
            "type" => "color"
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Announcement Title For Buyer Dashboard", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__('Please add text for announcement title that shows at buyer dashboard .', "foodbakery"),
            "id" => "dashboard_announce_title",
            "std" => '',
            "type" => "text",
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Announcement Description For Buyer Dashboard", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Please add text for announcement description that shows at buyer dashboard .", "foodbakery"),
            "id" => "dashboard_announce_description",
            "std" => "",
            "type" => "textarea",
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Default Sidebars", "foodbakery"),
            "id" => "tab-restaurant-announcements-options",
            "std" => esc_html__("Restaurant Dashboard Announcements", "foodbakery"),
            "type" => "section",
            "options" => ""
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Announcement Background Color", 'foodbakery'),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_announce_bg_color",
            "std" => "",
            "type" => "color"
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Announcement Title For Restaurant Dashboard", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__('Please add text for announcement title that shows at restaurant dashboard .', "foodbakery"),
            "id" => "restaurant_dashboard_announce_title",
            "std" => '',
            "type" => "text",
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Announcement Description For Restaurant Dashboard", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Please add text for announcement description that shows at restaurant dashboard .", "foodbakery"),
            "id" => "restaurant_dashboard_announce_description",
            "std" => "",
            "type" => "textarea",
        );

        $foodbakery_setting_options = apply_filters('foodbakery_general_plugin_options', $foodbakery_setting_options);

        $foodbakery_setting_options[] = array(
            "type" => "col-right-text",
            "extra" => "div",
        );

        // End General Options Announcements
        // general default location 
        // Smtp Email plugin fields filter.
        /**
         * Apply the filters by calling the 'foodbakery_smtp_plugin_options' function we
         * "hooked" to 'foodbakery_smtp_plugin_options' using the add_filter() function above.
         */
        $foodbakery_setting_options = apply_filters('foodbakery_smtp_plugin_options', $foodbakery_setting_options);
        // End Smtp Email plugin fields filter.

        $foodbakery_setting_options = apply_filters('foodbakery_notification_plugin_settings', $foodbakery_setting_options);


        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Publisher Settings", "foodbakery"),
            "id" => "tab-publisher-settings",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Publisher Settings", "foodbakery") . '"',
            "type" => "sub-heading"
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__("Publisher Settings", "foodbakery"),
            'id' => 'publisher-settings',
            'std' => esc_html__("Publisher Settings", "foodbakery"),
            'type' => 'section',
            'options' => ''
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Publisher Auto Approval", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("If this switch set to ON new user will be auto approved. If switch is set to OFF admin will have to approve the new user.", "foodbakery"),
            "id" => "publisher_review_option",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Profile Images", "foodbakery"),
            "desc" => "Add Profile Images",
            "hint_text" => '',
            "echo" => false,
            "id" => "profile_images",
            "type" => "gallery_upload",
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__('Default Profile Placeholder', "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "default_placeholder_image",
            "std" => "",
            "type" => "upload logo"
        );




        $foodbakery_setting_options[] = array("col_heading" => esc_html__("", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // end publisher settings
        // Payments Gateways
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Gateways Settings", "foodbakery"),
            "id" => "tab-gateways-settings",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Gateways Settings", "foodbakery") . '"',
            "type" => "sub-heading"
        );

        $foodbakery_setting_options[] = array("name" => esc_html__('Gateways Settings', 'foodbakery'),
            "id" => "tab-gateways-settings",
            "std" => esc_html__('Gateways Settings', 'foodbakery'),
            "type" => "section",
            "options" => ""
        );


        $foodbakery_gateways_id = FOODBAKERY_FUNCTIONS()->rand_id();


        $payments_settings = new FOODBAKERY_PAYMENTS();

        $gen_settings = $payments_settings->foodbakery_general_settings();

        foreach ($gen_settings as $key => $params) {
            $foodbakery_setting_options[] = $params;
        }

        $foodbakery_setting_options[] = array("name" => esc_html__("VAT On/Off", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("This switch will control VAT calculation and its payment along with package price. If this switch will be ON, user must have to pay VAT percentage separately. Turn OFF the switch to exclude VAT from payment.", "foodbakery"),
            "id" => "vat_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("VAT in %", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Here you can add VAT percentage according to your country laws & regulations.", "foodbakery"),
            "id" => "payment_vat",
            "std" => "",
            "type" => "text",
        );

        if (class_exists('WooCommerce')) {

            $use_wooC_gateways = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';
            $is_gateways_display = ( $use_wooC_gateways == 'on' ) ? 'style="display:none;"' : 'style="display:block;"';
            $foodbakery_setting_options[] = array("name" => esc_html__("Woocommerce Payment Gateways", 'foodbakery'),
                "desc" => "",
                "hint_text" => esc_html__("Make it on to use the woocommerce payment gateways instead of builtin ones."),
                "id" => "use_woocommerce_gateway",
                "std" => "off",
                "type" => "checkbox",
                "onchange" => "use_wooC_gateways(this)",
                "options" => $on_off_option
            );

            $foodbakery_setting_options[] = array(
                "type" => "division",
                "enable_id" => "foodbakery_use_woocommerce_gateway_style",
                "enable_val" => "",
                "auto_enable" => false,
                "extra_atts" => 'id="foodbakery-no-wooC-gateway-div" ' . $is_gateways_display,
            );
        }




        $gtws_settings = new FOODBAKERY_PAYMENTS();
        if (is_array($gateways) && sizeof($gateways) > 0) {
            foreach ($gateways as $key => $value) {
                if (class_exists($key)) {
                    $settings = new $key();
                    $gtw_settings = $settings->settings($foodbakery_gateways_id);
                    foreach ($gtw_settings as $key => $params) {
                        $foodbakery_setting_options[] = $params;
                    }
                }
            }
        }

        if (class_exists('WooCommerce')) {
            $foodbakery_setting_options[] = array(
                "type" => "division_close",
            );
        }

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("Payment Text", "foodbakery"),
            "type" => "col-right-text",
            "hint_text" => esc_html__("", "foodbakery"),
            "help_text" => ""
        );
        /*
         * defaul locations
         */
        // Default location

        $foodbakery_setting_options[] = array("name" => esc_html__("Default Location", "foodbakery"),
            "id" => "tab-general-default-location",
            "type" => "sub-heading",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Default Location", "foodbakery") . '"',
            //"extra" => "div",
            "help_text" => esc_html__('Default Location Set default location for your site. This location can be set from Restaurants > Locations in back end admin area. This will show location of admin only. It is not linked with Geo-location or Candidate.', 'foodbakery'),
        );

        $foodbakery_setting_options[] = array("name" => esc_html__('Default Location', 'foodbakery'),
            "id" => "tab-settings-default-location",
            "std" => esc_html__('Default Location', 'foodbakery'),
            "type" => "section",
            "options" => "",
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Map Marker Icon", "foodbakery"),
            "desc" => "",
            "hint_text" => "",
            "id" => "map_marker_icon",
            "std" => wp_foodbakery::plugin_url() . 'assets/images/map-marker.png',
            "display" => "block",
            "type" => "upload logo"
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Map Cluster Icon", "foodbakery"),
            "desc" => "",
            "hint_text" => "",
            "id" => "map_cluster_icon",
            "std" => wp_foodbakery::plugin_url() . 'assets/frontend/images/map-cluster.png',
            "display" => "block",
            "type" => "upload logo"
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Zoom Level", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Set zoom level 1 to 15 only.", "foodbakery"),
            "id" => "map_zoom_level",
            "std" => "9",
            "classes" => "foodbakery-dev-req-field-admin foodbakery-number-field foodbakery-range-field",
            "extra_attr" => 'data-min="1" data-max="15"',
            "type" => "text"
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Map Style", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Set Map Style.", 'foodbakery'),
            "id" => "def_map_style",
            "std" => "",
            "type" => "select",
            'classes' => 'chosen-select-no-single',
            "options" => array(
                'map-box' => esc_html__("Map Box", 'foodbakery'),
                'blue-water' => esc_html__("Blue Water", 'foodbakery'),
                'icy-blue' => esc_html__("Icy Blue", 'foodbakery'),
                'bluish' => esc_html__("Bluish", 'foodbakery'),
                'light-blue-water' => esc_html__("Light Blue Water", 'foodbakery'),
                'clad-me' => esc_html__("Clad Me", 'foodbakery'),
                'chilled' => esc_html__("Chilled", 'foodbakery'),
                'two-tone' => esc_html__("Two Tone", 'foodbakery'),
                'light-and-dark' => esc_html__("Light and Dark", 'foodbakery'),
                'ilustracao' => esc_html__("Ilustracao", 'foodbakery'),
                'flat-pale' => esc_html__("Flat Pale", 'foodbakery'),
                'title' => esc_html__("Title", 'foodbakery'),
                'moret' => esc_html__("Moret", 'foodbakery'),
                'samisel' => esc_html__("Samisel", 'foodbakery'),
                'herbert-map' => esc_html__("Herbert Map", 'foodbakery'),
                'light-dream' => esc_html__("Light Dream", 'foodbakery'),
                'blue-essence' => esc_html__("Blue Essence", 'foodbakery'),
                'rpn-map' => esc_html__("RPN Map", 'foodbakery'),
            )
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Address", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "default_locations",
            "std" => "",
            "type" => "default_location_fields",
            "contry_hint" => '',
            "city_hint" => '',
            "address_hint" => esc_html__("Set default street address here.", "foodbakery"),
        );
        $foodbakery_setting_options[] = array("col_heading" => esc_html__("Default Location", "foodbakery"),
            "type" => "col-right-text",
            "extra" => "div",
            "help_text" => esc_html__('Set default location for your site (Country, City & Address). This location can be set from Restaurants > Locations in back end admin area. This will show location of admin only and willl fetch results from the given location first. It is not linked with Geo-location or Candidate.', 'foodbakery'),
        );
        //End default location 
        /*
         * Backend Locations and Map settings.
         */
        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Backend Locations & Maps Settings', 'foodbakery'),
            'id' => 'tab-backend-settings',
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Backend Locations & Maps Settings", "foodbakery") . '"',
            'type' => 'sub-heading'
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Locations', 'foodbakery'),
            'id' => 'locations',
            'std' => 'Locations',
            'type' => 'section',
            'options' => ''
        );

        $foodbakery_setting_options[] = array('name' => esc_html__('Location\'s Levels', 'foodbakery'),
            'desc' => '',
            'hint_text' => '',
            'id' => 'locations_level_selector',
            'std' => '',
            'type' => 'locations_level_selector'
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Geo Location', 'foodbakery'),
            'desc' => '',
            'hint_text' => esc_html__('Ask user to share his location.', 'foodbakery'),
            'id' => 'map_geo_location',
            'main_id' => 'foodbakery_map_geo_location',
            'std' => '',
            'type' => 'checkbox'
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Auto Country Detection', 'foodbakery'),
            'desc' => '',
            'hint_text' => esc_html__('Do you want to detect country automatically using user\'s IP?', 'foodbakery'),
            'id' => 'map_auto_country_detection',
            'main_id' => 'foodbakery_map_auto_country_detection',
            'std' => '',
            'type' => 'checkbox'
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Address Maximum Text Limit', 'foodbakery'),
            'desc' => '',
            'hint_text' => esc_html__('Allowed address maximum text limit.', 'foodbakery'),
            'id' => 'map_address_maximum_text_limit',
            'main_id' => 'foodbakery_map_address_maximum_text_limit',
            'std' => '',
            'classes' => 'foodbakery-number-field',
            'type' => 'text'
        );

//        $foodbakery_setting_options[] = array(
//            'name' => esc_html__('Drawing Tools', 'foodbakery'),
//            'desc' => '',
//            'hint_text' => esc_html__('Do you want drawing tools on map?', 'foodbakery'),
//            'id' => 'drawing_tools',
//            'main_id' => 'foodbakery_map_drawing_tools',
//            'std' => '',
//            'type' => 'checkbox'
//        );
//
//        $foodbakery_setting_options[] = array(
//            'name' => esc_html__('Drawing Tools Line Color', 'foodbakery'),
//            'desc' => '',
//            'hint_text' => esc_html__('Color used while drawing line or polygon on map.', 'foodbakery'),
//            'id' => 'drawing_tools_line_color',
//            'main_id' => 'foodbakery_map_drawing_tools_line_color',
//            'std' => '#000000',
//            'type' => 'color',
//        );
//
//        $foodbakery_setting_options[] = array(
//            'name' => esc_html__('Drawing Tools Fill Color', 'foodbakery'),
//            'desc' => '',
//            'hint_text' => esc_html__('Color used to fill polygon on map.', 'foodbakery'),
//            'id' => 'drawing_tools_fill_color',
//            'main_id' => 'foodbakery_map_drawing_tools_fill_color',
//            'std' => '#000000',
//            'type' => 'color',
//        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Auto Complete', 'foodbakery'),
            'desc' => '',
            'hint_text' => esc_html__('Do you want google to give suggestions to auto complete?', 'foodbakery'),
            'id' => 'location_autocomplete',
            'main_id' => 'foodbakery_map_location_autocomplete',
            'std' => '',
            'type' => 'checkbox'
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Circle Radius', 'foodbakery'),
            'desc' => '',
            'hint_text' => esc_html__('Default Radius Circle.', 'foodbakery'),
            'id' => 'default_radius_circle',
            'main_id' => 'foodbakery_default_radius_circle',
            'std' => '10',
            'type' => 'text'
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Location Form Fields', 'foodbakery'),
            'id' => 'locations',
            'std' => 'Location Form Fields',
            'type' => 'section',
            'options' => ''
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Location\'s Fields', 'foodbakery'),
            'desc' => '',
            'hint_text' => '',
            'id' => 'locations_fields_selector',
            'cust_name' => 'locations_fields_selector[]',
            'std' => '',
            'type' => 'locations_fields_selector',
        );



        $foodbakery_setting_options[] = array('col_heading' => esc_html__('', 'foodbakery'),
            'type' => 'col-right-text',
            'help_text' => ''
        );
        /*
         * Social auto post start
         */

        $twitter_format = array(
            'twitter_restaurant_title' => '[Restaurant Title]',
            'twitter_permalink' => '[Permalink]',
            'twitter_restaurant_content' => '[Restaurant Content]',
        );
        $facebook_format = array(
            'facebook_restaurant_title' => '[Restaurant Title]',
            'facebook_permalink' => '[Permalink]',
            'facebook_restaurant_content' => '[Restaurant Content]',
        );
        $linkedin_format = array(
            'linkedin_restaurant_title' => '[Restaurant Title]',
            'linkedin_permalink' => '[Permalink]',
            'linkedin_restaurant_content' => '[Restaurant Content]',
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Social Auto Post Settings", "foodbakery"),
            "id" => "tab-autopost-setting",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Social Auto Post Settings", "foodbakery") . '"',
            "type" => "sub-heading"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Twitter", 'foodbakery'),
            "desc" => "",
            "id" => "twitter_autopost_switch",
            "std" => "off",
            "type" => "checkbox",
            "onchange" => "foodbakery_autopost_twitter_hide_show(this.name)",
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Facebook ", 'foodbakery'),
            "desc" => "",
            "id" => "facebook_autopost_switch",
            "std" => "off",
            "type" => "checkbox",
            "onchange" => "foodbakery_autopost_facebook_hide_show(this.name)",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("LinkedIn", 'foodbakery'),
            "desc" => "",
            "id" => "linkedin_autopost_switch",
            "std" => "off",
            "onchange" => "foodbakery_autopost_linkedin_hide_show(this.name)",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        /*
         * End auto post settings
         */
        // social login 
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Social Login", "foodbakery"),
            "id" => "tab-social-login-setting",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Social Login", "foodbakery") . '"',
            "type" => "sub-heading"
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__("Social Login", "foodbakery"),
            'id' => 'social-login-settings',
            'std' => esc_html__("Social Login", "foodbakery"),
            'type' => 'section',
            'options' => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Show Twitter", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Manage user registration via Twitter here. If this switch is set ON, users will be able to sign up / sign in with Twitter. If it will be OFF, users will not be able to register / sign in through Twitter.", 'foodbakery'),
            "id" => "twitter_api_switch",
            "std" => "on",
            "type" => "checkbox"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Facebook Login On/Off", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Manage user registration via Facebook here. If this switch is set ON, users will be able to sign up / sign in with Facebook. If it will be OFF, users will not be able to register / sign in through Facebook.", 'foodbakery'),
            "id" => "facebook_login_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Google Login On/Off", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Manage user registration via Google here. If this switch is set ON, users will be able to sign up / sign in with Google. If it will be OFF, users will not be able to register / sign in through Google.", 'foodbakery'),
            "id" => "google_login_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array("col_heading" => esc_html__("", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // end social login
        // API settings.
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Api Settings", "foodbakery"),
            "id" => "tab-api-setting",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("API Settings", "foodbakery") . '"',
            "type" => "sub-heading"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Twitter", 'foodbakery'),
            "id" => "Twitter",
            "std" => "Twitter",
            "type" => "section",
            "options" => ""
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Consumer Key", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Insert Twitter Consumer Key here. When you create your Twitter App, you will get this key.", "foodbakery"),
            "id" => "consumer_key",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Consumer Secret", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Insert Twitter Consumer secret here. When you create your Twitter App, you will get this key.", "foodbakery"),
            "id" => "consumer_secret",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Access Token", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Insert Twitter Access Token for permissions. When you create your Twitter App, you will get this Token", 'foodbakery'),
            "id" => "access_token",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Access Token Secret", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Insert Twitter Access Token Secret here. When you create your Twitter App, you will get this Token", 'foodbakery'),
            "id" => "access_token_secret",
            "std" => "",
            "type" => "text"
        );
        //end Twitter Api		
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Facebook", 'foodbakery'),
            "id" => "Facebook",
            "std" => "Facebook",
            "type" => "section",
            "options" => ""
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Facebook Application ID", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Here you have to add your Facebook application ID. You will get this ID when you create Facebook App.", 'foodbakery'),
            "id" => "facebook_app_id",
            "std" => "",
            "classes" => "foodbakery-number-field",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Facebook Secret", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Put your Facebook Secret here. You can find it in your Facebook Application Dashboard", 'foodbakery'),
            "id" => "facebook_secret",
            "std" => "",
            "type" => "text"
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Facebook Access Token", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Click on the button bellow to get access token.", 'foodbakery'),
            "id" => "facebook_access_token",
            "std" => "",
            "type" => "fb_access_token"
        );
        //end facebook api
        //start google api
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Google", 'foodbakery'),
            "id" => "Google",
            "std" => "Google",
            "type" => "section",
            "options" => ""
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Google Client ID", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Put your Google client ID here.  To get this ID, go to your Google account Dashboard", 'foodbakery'),
            "id" => "google_client_id",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Google Client Secret", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Put your google client secret here.  To get client secret, go to your Google account", 'foodbakery'),
            "id" => "google_client_secret",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Google API key", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__('Put your Google API key here.  To get API, go to your Google account', 'foodbakery'),
            "id" => "google_api_key",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Fixed redirect url for login", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__('Put your google+ redirect url here.', 'foodbakery'),
            "id" => "google_login_redirect_url",
            "std" => "",
            "type" => "text"
        );
        //end google api
        // captcha settings
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Captcha", 'foodbakery'),
            "id" => "Captcha",
            "std" => "Captcha",
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Captcha", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Manage your captcha code for secured Signup here. If this switch will be ON, user can register after entering Captcha code. It helps to avoid robotic / spam sign-up", 'foodbakery'),
            "id" => "captcha_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Site Key", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Put your site key for captcha. You can get this site key after registering your site on Google.", "foodbakery"),
            "id" => "sitekey",
            "std" => "",
            "type" => "text",
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Secret Key", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("Put your site Secret key for captcha. You can get this Secret Key after registering your site on Google.", "foodbakery"),
            "id" => "secretkey",
            "std" => "",
            "type" => "text",
        );
        // end captcha settings

        /*
         * Start Linkedin API Settings
         */

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("LinkedIn", 'foodbakery'),
            "id" => "LinkedIn",
            "std" => "LinkedIn",
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("LinkedIn Application Id", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Add LinkedIn application ID. To get your Linked-in Application ID, go to your LinkedIn Dashboard", "foodbakery"),
            "id" => "linkedin_app_id",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Linkedin Secret", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Put your LinkedIn Secret here. You can find it in your LinkedIn Application Dashboard", 'foodbakery'),
            "id" => "linkedin_secret",
            "std" => "",
            "type" => "text"
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Linkedin Access Token", 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Click on the button bellow to get access token.", 'foodbakery'),
            "id" => "linkedin_access_token",
            "std" => "",
            "type" => "linkedin_access_token"
        );

        /*
         * End Linkedin API setting
         */

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("API Settings", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        // Ads Management settings.
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Ads Management", "foodbakery"),
            "id" => "tab-ads-management-setting",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Ads Management", "foodbakery") . '"',
            "type" => "sub-heading"
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__("Ads Management", "foodbakery"),
            'id' => 'ads-management-settings',
            'std' => esc_html__("Ads Management", "foodbakery"),
            'type' => 'section',
            'options' => ''
        );

        ///Ads Unit 
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Ads Management Settings", "foodbakery"),
            "desc" => "",
            "hint_text" => "",
            "id" => "cs_banner_fields",
            "std" => "",
            "type" => "banner_fields",
            "options" => array()
        );

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("Ads Management", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        /* social Network setting */
        $foodbakery_setting_options[] = array("name" => esc_html__("Social Sharing", 'foodbakery'),
            "id" => "tab-social-icons",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Social Sharing", "foodbakery") . '"',
            "type" => "sub-heading"
        );
        $foodbakery_setting_options[] = array(
            'name' => esc_html__("Social Sharing", 'foodbakery'),
            'id' => 'social-sharing-settings',
            'std' => esc_html__("Social Sharing", 'foodbakery'),
            'type' => 'section',
            'options' => ''
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("social sharing", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "social_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("Facebook", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "facebook_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("Twitter", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "twitter_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("Google Plus", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "google_plus_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("Pinterest", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "pintrest_share",
            "std" => "on",
            "type" => "checkbox"
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Tumblr", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "tumblr_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("Dribbble", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "dribbble_share",
            "std" => "off",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("Instagram", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "instagram_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("StumbleUpon", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "stumbleupon_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("youtube", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "youtube_share",
            "std" => "on",
            "type" => "checkbox");
        $foodbakery_setting_options[] = array("name" => esc_html__("share more", 'foodbakery'),
            "desc" => "",
            "hint_text" => "",
            "id" => "share_share",
            "std" => "off",
            "type" => "checkbox");
        /* social network end */

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("Social Icon", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        /**
         * Apply the filters by calling the 'foodbakery__plugin_addons_options' function we
         * "hooked" to 'foodbakery__plugin_addons_options' using the add_filter() function above.
         */
        $foodbakery_setting_options = apply_filters('foodbakery__plugin_addons_options', $foodbakery_setting_options);
        // End foodbakery Add-ons.

        $foodbakery_setting_options[] = array("name" => esc_html__("import & export", 'foodbakery'),
            "fontawesome" => 'icon-refresh3',
            "id" => "tab-import-export-options",
            "std" => "",
            "type" => "main-heading",
            "options" => ""
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("import & export", 'foodbakery'),
            "id" => "tab-import-export-options",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("import & export", "foodbakery") . '"',
            "type" => "sub-heading"
        );


        $foodbakery_setting_options[] = array("name" => esc_html__("Backup", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "backup_options",
            "std" => "",
            "type" => "generate_backup"
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Users Import / Export", 'foodbakery'),
            "id" => "user-import-export",
            "std" => esc_html__("Users Import / Export", 'foodbakery'),
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Import Users Data", 'foodbakery'),
            "desc" => "",
            "hint_text" => '',
            "id" => "backup_options",
            "std" => "",
            "type" => "user_import_export",
        );

        $foodbakery_setting_options[] = array('name' => esc_html__('Backup Locations', 'procoupler'),
            'desc' => '',
            'hint_text' => '',
            'id' => 'backup_locations',
            'std' => '',
            'type' => 'backup_locations'
        );

        $foodbakery_setting_options[] = array('name' => esc_html__('Backup Restaurant Type Categories', 'procoupler'),
            'desc' => '',
            'hint_text' => '',
            'id' => 'backup_restaurant_type_categories',
            'std' => '',
            'type' => 'backup_restaurant_type_categories'
        );

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("import & export", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        // advance settings
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Advance Settings", "foodbakery"),
            "fontawesome" => 'icon-cog',
            "id" => "tab-advance-settings",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Advance Settings", "foodbakery"),
            "id" => "tab-advance-settings",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Advance Settings", "foodbakery") . '"',
            "type" => "sub-heading"
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__("Restaurant Success Settings", "foodbakery"),
            'id' => 'restaurant-success-settings',
            'std' => esc_html__("Restaurant Success Settings", "foodbakery"),
            'type' => 'section',
            'options' => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__('Success Image', "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_success_image",
            "std" => "",
            "type" => "upload"
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Success Message", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_success_message",
            "std" => esc_html__("You have successfully created your restaurant, to add more details, go to your email inbox for login details.", "foodbakery"),
            "type" => "text",
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Success Phone", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_success_phone",
            "std" => "",
            "type" => "text",
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Success FAX", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_success_fax",
            "std" => "",
            "type" => "text",
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Success Email", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "restaurant_success_email",
            "std" => "",
            "type" => "text",
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__("Demo User Login", "foodbakery"),
            'id' => 'advance-settings',
            'std' => esc_html__("Demo User Login", "foodbakery"),
            'type' => 'section',
            'options' => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Demo User Login", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "demo_user_login_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Demo User Modification Allowed", "foodbakery"),
            "desc" => "",
            "hint_text" => '',
            "id" => "demo_user_modification_allowed_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Restaurant', 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Please select a user for restaurant login", "foodbakery"),
            'id' => 'job_demo_user_publisher',
            "std" => "",
            "classes" => "chosen-select",
            "type" => "custom_user_select",
            'main_wraper' => true,
            'main_wraper_class' => 'dynamic-field select-style demo_user_publisher_holder',
            'main_wraper_extra' => ' onclick="foodbakery_load_dropdown_values(\'demo_user_publisher_holder\', \'job_demo_user_publisher\', \'foodbakery_load_all_publishers_options\');"',
            'markup' => '<span class="select-loader"></span>',
            "options" => array('' => esc_html__("Please Select Restaurant", "foodbakery")),
        );

        $foodbakery_setting_options[] = array(
            'name' => esc_html__('Buyer', 'foodbakery'),
            "desc" => "",
            "hint_text" => esc_html__("Please select a user for buyer login", "foodbakery"),
            'id' => 'demo_user_buyer',
            "std" => "",
            "classes" => "chosen-select",
            "type" => "custom_user_select",
            'main_wraper' => true,
            'main_wraper_class' => 'dynamic-field select-style demo_user_buyer_holder',
            'main_wraper_extra' => ' onclick="foodbakery_load_dropdown_values(\'demo_user_buyer_holder\', \'demo_user_buyer\', \'foodbakery_load_all_buyers_options\');"',
            'markup' => '<span class="select-loader"></span>',
            "options" => array('' => esc_html__("Please Select Buyer", "foodbakery")),
        );


        // custom css
        $foodbakery_setting_options[] = array("name" => esc_html__("Custom Css", "foodbakery"),
            "id" => "tab-job-options",
            "std" => esc_html__("Custom Css", "foodbakery"),
            "type" => "section",
            "options" => ""
        );
        $foodbakery_setting_options[] = array("name" => esc_html__("Custom Css", "foodbakery"),
            "desc" => "",
            "hint_text" => esc_html__("This is custom css area", "foodbakery"),
            "id" => "style-custom-css",
            "std" => "",
            "type" => "textarea",
        );

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // end advance settings
        // Orders/Bookings settings
        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Orders/Bookings", "foodbakery"),
            "fontawesome" => 'icon-payment',
            "id" => "tab-orders-bookings",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );

        $foodbakery_setting_options[] = array(
            "name" => esc_html__("Orders/Bookings", "foodbakery"),
            "id" => "tab-orders-bookings",
            "extra" => 'class="foodbakery_tab_block" data-title="' . esc_html__("Orders/Bookings", "foodbakery") . '"',
            "type" => "sub-heading"
        );

        $foodbakery_setting_options[] = array("name" => esc_html__("Orders/Bookings Status", "foodbakery"),
            "desc" => "Add Orders/Bookings Status",
            "hint_text" => '',
            "echo" => true,
            "id" => "orders_bookings_status",
            "type" => "orders_bookings_status",
        );

        $foodbakery_setting_options[] = array("col_heading" => esc_html__("", "foodbakery"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        update_option('foodbakery_plugin_data', $foodbakery_setting_options);
    }

}
$output = '';
$output .= '</div>';
