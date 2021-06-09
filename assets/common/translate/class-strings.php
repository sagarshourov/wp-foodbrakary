<?php

/**
 * Static string Return
 */
global $foodbakery_static_text;

if (!function_exists('foodbakery_plugin_text_srt')) {

    function foodbakery_plugin_text_srt($str = '') {
        global $foodbakery_static_text;
        if (isset($foodbakery_static_text[$str])) {
            return $foodbakery_static_text[$str];
        }
        return '';
    }

}
if (!class_exists('foodbakery_plugin_all_strings')) {

    class foodbakery_plugin_all_strings {

        public function __construct() {
            /*
             * Triggering function for strings.
             */
            add_action('init', array($this, 'foodbakery_plugin_strings'), 11);
           
        }

        public function foodbakery_plugin_strings() {
            global $foodbakery_static_text;

            /*
             * Restaurants Post Type Strings
             */
            $foodbakery_static_text['id_number'] = esc_html__('ID Number', 'foodbakery');
            $foodbakery_static_text['transaction_id'] = esc_html__('Transaction Id', 'foodbakery');
            $foodbakery_static_text['restaurant_contact_email'] = esc_html__('Email', 'foodbakery');
            $foodbakery_static_text['restaurant_contact_phone'] = esc_html__('Phone Number', 'foodbakery');
            $foodbakery_static_text['restaurant_contact_web'] = esc_html__('Web', 'foodbakery');
            $foodbakery_static_text['restaurant_contact_heading'] = esc_html__('Contact Information', 'foodbakery');
            
            $foodbakery_static_text['foodbakery_save_settings'] = esc_html__('Save All Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_reset_options'] = esc_html__('Reset All Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_please_wait'] = esc_html__('Please Wait...', 'foodbakery');
            $foodbakery_static_text['foodbakery_general_options'] = esc_html__('General Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_page_settings'] = esc_html__('Page Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_default_location'] = esc_html__('Default Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_candidate_skills_sets'] = esc_html__('Candidate Skills Sets', 'foodbakery');
            $foodbakery_static_text['foodbakery_others'] = esc_html__('Others', 'foodbakery');
            $foodbakery_static_text['foodbakery_smtp_settings'] = esc_html__('SMTP Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_gateways'] = esc_html__('Gateways', 'foodbakery');
            $foodbakery_static_text['foodbakery_packages'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['foodbakery_job_credit'] = esc_html__('Job Credit', 'foodbakery');
            $foodbakery_static_text['foodbakery_cv_search'] = esc_html__('CV Search', 'foodbakery');
            $foodbakery_static_text['foodbakery_featured_restaurants'] = esc_html__('Featured Restaurants', 'foodbakery');
            $foodbakery_static_text['foodbakery_custom_fields'] = esc_html__('Custom Fields', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurants_fields'] = esc_html__('Restaurants Fields', 'foodbakery');
            $foodbakery_static_text['foodbakery_candidates_fields'] = esc_html__('Candidates Fields', 'foodbakery');
            $foodbakery_static_text['foodbakery_recruiters'] = esc_html__('Recruiters', 'foodbakery');
            $foodbakery_static_text['foodbakery_api_settings'] = esc_html__('Api Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_search_options'] = esc_html__('Search Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_social_icon'] = esc_html__('Social Icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_settings'] = esc_html__('User Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_header_login'] = esc_html__('User Header Login', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_header_login_hint'] = esc_html__('Dashboard and Front-End login/register option can be hide by turning off this switch.', 'foodbakery');
            $foodbakery_static_text['foodbakery_menu_location'] = esc_html__('Menu Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_menu_location_hint'] = esc_html__('Show login section in Menu', 'foodbakery');
            $foodbakery_static_text['foodbakery_employer_dashboard'] = esc_html__('Employer Dashboard', 'foodbakery');
            $foodbakery_static_text['foodbakery_employer_dashboard_hint'] = esc_html__("Select page for employer dashboard here. This page is set in page template drop down. To create employer dashboard page, go to Pages > Add new page, set the page template to 'employer' in the right menu.", 'foodbakery');
            $foodbakery_static_text['foodbakery_candidates_dashboard'] = esc_html__('Candidates Dashboard', 'foodbakery');
            $foodbakery_static_text['foodbakery_candidates_dashboard_hint'] = esc_html__("Select page for Candidates dashboard here. This page is set in page template drop down. To create Candidate dashboard page, go to Pages > Add new page, set the page template to 'Candidate' in the right menu.", 'foodbakery');

            // Order & Inquires Strings
            $foodbakery_static_text['foodbakery_orders_inquiries'] = esc_html__('Orders/Inquiries', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_name'] = esc_html__('Orders/Inquiries', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_singular_name'] = esc_html__('Order/Inquiry', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_menu_name'] = esc_html__('Orders/Inquiries', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_name_admin_bar'] = esc_html__('Orders/Inquiries', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_add_new'] = esc_html__('Add Order/Inquiry', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_add_new_item'] = esc_html__('Add Order/Inquiry', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_new_item'] = esc_html__('Add Order/Inquiry', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_edit_item'] = esc_html__('Edit Order/Inquiry', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_view_item'] = esc_html__('Order/Inquiry', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_all_items'] = esc_html__('Orders/Inquiries', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_search_items'] = esc_html__('Order/Inquiry', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_not_found'] = esc_html__('Orders/Inquiries', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_not_found_in_trash'] = esc_html__('Orders/Inquiries', 'foodbakery');
            $foodbakery_static_text['foodbakery_orders_inquiries_description'] = esc_html__('Edit Order/Inquiry', 'foodbakery');
            // Restaurant Settings
            $foodbakery_static_text['foodbakery_restaurant_types'] = esc_html__('Restaurant Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_category_description'] = esc_html__('Category Description', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_restaurant_type'] = esc_html__('Add Restaurant Setting', 'foodbakery');
            $foodbakery_static_text['foodbakery_edit_restaurant_type'] = esc_html__('Restaurant Setting', 'foodbakery');
            $foodbakery_static_text['foodbakery_submit'] = esc_html__('Submit', 'foodbakery');
            $foodbakery_static_text['foodbakery_preview'] = esc_html__('Preview', 'foodbakery');
            $foodbakery_static_text['foodbakery_delete_permanently'] = esc_html__('Delete permanently', 'foodbakery');
            $foodbakery_static_text['foodbakery_move_to_trash'] = esc_html__('Move to trash', 'foodbakery');
            $foodbakery_static_text['foodbakery_publish'] = esc_html__('Publish', 'foodbakery');
            $foodbakery_static_text['foodbakery_submit_for_review'] = esc_html__('Submit for review', 'foodbakery');
            $foodbakery_static_text['foodbakery_update'] = esc_html__('Update', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_category'] = esc_html__('Add Category', 'foodbakery');
            $foodbakery_static_text['foodbakery_name'] = esc_html__('Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_actions'] = esc_html__('Actions', 'foodbakery');
            $foodbakery_static_text['foodbakery_update_category'] = esc_html__('Update Category', 'foodbakery');
            $foodbakery_static_text['foodbakery_click_to_add_item'] = esc_html__('Click to Add Item', 'foodbakery');
            $foodbakery_static_text['foodbakery_text'] = esc_html__('TEXT', 'foodbakery');
            $foodbakery_static_text['foodbakery_services'] = esc_html__('Services', 'foodbakery');
            $foodbakery_static_text['foodbakery_availability'] = esc_html__('Availability', 'foodbakery');
            $foodbakery_static_text['foodbakery_availability_string'] = esc_html__('Availability: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_number'] = esc_html__('NUMBER', 'foodbakery');
            $foodbakery_static_text['foodbakery_textarea'] = esc_html__('TEXTAREA', 'foodbakery');
            $foodbakery_static_text['foodbakery_dropdown'] = esc_html__('DROPDOWN', 'foodbakery');
            $foodbakery_static_text['foodbakery_date'] = esc_html__('DATE', 'foodbakery');
            $foodbakery_static_text['foodbakery_email'] = esc_html__('EMAIL', 'foodbakery');
            $foodbakery_static_text['foodbakery_url'] = esc_html__('URL', 'foodbakery');
            $foodbakery_static_text['foodbakery_url_string'] = esc_html__('URL: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_range'] = esc_html__('RANGE', 'foodbakery');
            $foodbakery_static_text['foodbakery_quantity'] = esc_html__('Quantity', 'foodbakery');
            $foodbakery_static_text['foodbakery_section'] = esc_html__('SECTION', 'foodbakery');
            $foodbakery_static_text['foodbakery_time'] = esc_html__('Time', 'foodbakery');
            $foodbakery_static_text['foodbakery_form_title'] = esc_html__('Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_reservation_form_title'] = esc_html__('Form Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_paid_inquiry_form'] = esc_html__('Reservation Paid', 'foodbakery');
            $foodbakery_static_text['foodbakery_form_button_label'] = esc_html__('Form Button Label', 'foodbakery');
            $foodbakery_static_text['foodbakery_form_terms_label'] = esc_html__('Form Terms Label', 'foodbakery');
            $foodbakery_static_text['foodbakery_form_terms_link'] = esc_html__('Form Terms Link', 'foodbakery');
            $foodbakery_static_text['foodbakery_time_small'] = esc_html__('Time', 'foodbakery');
            $foodbakery_static_text['foodbakery_time_string'] = esc_html__('Time: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_file_upload'] = esc_html__('File Upload', 'foodbakery');
            $foodbakery_static_text['foodbakery_file'] = esc_html__('File', 'foodbakery');
            $foodbakery_static_text['foodbakery_file_hint'] = esc_html__('Upload Image / File here.', 'foodbakery');
            $foodbakery_static_text['foodbakery_file_upload_string'] = esc_html__('File Upload: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_please_insert_item'] = esc_html__('Please Insert Item', 'foodbakery');
            $foodbakery_static_text['foodbakery_section_small'] = esc_html__('Section', 'foodbakery');
            $foodbakery_static_text['foodbakery_section_string'] = esc_html__('Section: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_text_string'] = esc_html__('Text: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_text_small'] = esc_html__('Text', 'foodbakery');
            $foodbakery_static_text['foodbakery_custom_field_title'] = esc_html__('Field Title', 'foodbakery');
			$foodbakery_static_text['foodbakery_custom_field_title_enable'] = esc_html__('Enable Field Title', 'foodbakery');
			$foodbakery_static_text['foodbakery_custom_field_title_enable_hint'] = esc_html__('Enable/Disable field title in booking form.', 'foodbakery');
            $foodbakery_static_text['foodbakery_services_small'] = esc_html__('Services', 'foodbakery');
            $foodbakery_static_text['foodbakery_services_string'] = esc_html__('Services: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_field_title'] = esc_html__('Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_field_time_lapse'] = esc_html__('Time Lapse', 'foodbakery');
            $foodbakery_static_text['foodbakery_field_time_lapse_hint'] = esc_html__('Add time lapse here in minutes ( 1 to 60 )', 'foodbakery');
            $foodbakery_static_text['foodbakery_list_type_icon_image'] = esc_html__('Restaurant Setting Icon / Image', 'foodbakery');
			$foodbakery_static_text['foodbakery_list_menu_type_icon_image'] = esc_html__('Restaurant Setting Menu Icon / Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_icon'] = esc_html__('Icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_image'] = esc_html__('Image', 'foodbakery');
             $foodbakery_static_text['foodbakery_image_icon'] = esc_html__('small icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_icon'] = esc_html__('Restaurant Icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_image'] = esc_html__('Restaurant Image', 'foodbakery');
			$foodbakery_static_text['foodbakery_restaurant_menu_icon'] = esc_html__('Restaurant Menu Icon', 'foodbakery');
			$foodbakery_static_text['foodbakery_restaurant_menu_image'] = esc_html__('Restaurant Menu Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_map_marker_image'] = esc_html__('Map Marker Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_select_a_page'] = esc_html__('Please select a page', 'foodbakery');
            $foodbakery_static_text['foodbakery_search_result_page'] = esc_html__('Search Result Page', 'foodbakery');
            $foodbakery_static_text['foodbakery_single_page_layout'] = esc_html__('Single Page Layout', 'foodbakery');
            $foodbakery_static_text['foodbakery_view1'] = esc_html__('View 1', 'foodbakery');
            $foodbakery_static_text['foodbakery_view2'] = esc_html__('View 2', 'foodbakery');
            $foodbakery_static_text['foodbakery_view3'] = esc_html__('View 3', 'foodbakery');
            $foodbakery_static_text['foodbakery_required'] = esc_html__('Required', 'foodbakery');
			$foodbakery_static_text['foodbakery_required_fields'] = esc_html__('Required Fields', 'foodbakery');
            $foodbakery_static_text['foodbakery_meta_key'] = esc_html__('Meta Key', 'foodbakery');
            $foodbakery_static_text['foodbakery_meta_key_hint'] = esc_html__('Please enter Meta Key without special characters and spaces', 'foodbakery');
            $foodbakery_static_text['foodbakery_place_holder'] = esc_html__('Place Holder', 'foodbakery');
            $foodbakery_static_text['foodbakery_enable_search'] = esc_html__('Enable In Search', 'foodbakery');
            $foodbakery_static_text['foodbakery_enable_search_hint'] = esc_html__('If Set to "Yes" user can filter the restaurants from search based on this field.', 'foodbakery');            
            $foodbakery_static_text['foodbakery_default_value'] = esc_html__('Default Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_collapse_in_search'] = esc_html__('Collapse in Search', 'foodbakery');
            $foodbakery_static_text['foodbakery_field_size'] = esc_html__('Field Size', 'foodbakery');
            $foodbakery_static_text['foodbakery_services_dropdown'] = esc_html__('Services Dropdown', 'foodbakery');
            $foodbakery_static_text['foodbakery_services_dropdown_hint'] = esc_html__('Do you want to show services dropdown in form.', 'foodbakery');
            $foodbakery_static_text['foodbakery_small'] = esc_html__('Small', 'foodbakery');
            $foodbakery_static_text['foodbakery_medium'] = esc_html__('Medium', 'foodbakery');
            $foodbakery_static_text['foodbakery_large'] = esc_html__('Large', 'foodbakery');
            $foodbakery_static_text['foodbakery_number_small'] = esc_html__('Number', 'foodbakery');
            $foodbakery_static_text['foodbakery_number_string'] = esc_html__('Number: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_textarea_small'] = esc_html__('TextArea', 'foodbakery');
            $foodbakery_static_text['foodbakery_textarea_string'] = esc_html__('TextArea: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_title'] = esc_html__('Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_help_text'] = esc_html__('Help Text', 'foodbakery');
            $foodbakery_static_text['foodbakery_rows'] = esc_html__('Rows', 'foodbakery');
            $foodbakery_static_text['foodbakery_columns'] = esc_html__('Columns', 'foodbakery');
            $foodbakery_static_text['foodbakery_search_style'] = esc_html__('Search Style', 'foodbakery');
            $foodbakery_static_text['foodbakery_view_style'] = esc_html__('View Style', 'foodbakery');
            $foodbakery_static_text['foodbakery_simple'] = esc_html__('Simple', 'foodbakery');
            $foodbakery_static_text['foodbakery_with_background_image'] = esc_html__('With Background Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_multi_select'] = esc_html__('Multi Select', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_multi_select'] = esc_html__('Post Multi Select', 'foodbakery');
            $foodbakery_static_text['foodbakery_first_value'] = esc_html__('First Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_options'] = esc_html__('Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_another'] = esc_html__('Add Another', 'foodbakery');
            $foodbakery_static_text['foodbakery_remove_this'] = esc_html__('Remove This', 'foodbakery');
            $foodbakery_static_text['foodbakery_date_small'] = esc_html__('Date', 'foodbakery');
            $foodbakery_static_text['foodbakery_date_string'] = esc_html__('Date: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_date_format'] = esc_html__('Date Format', 'foodbakery');
            $foodbakery_static_text['foodbakery_range_small'] = esc_html__('Range', 'foodbakery');
            $foodbakery_static_text['foodbakery_range_string'] = esc_html__('Range: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_quantity_small'] = esc_html__('Quantity', 'foodbakery');
            $foodbakery_static_text['foodbakery_quantity_string'] = esc_html__('Quantity: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_minimum_value'] = esc_html__('Minimum Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_maximum_value'] = esc_html__('Maximum Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_increment_step'] = esc_html__('Increment Step', 'foodbakery');
            $foodbakery_static_text['foodbakery_slider'] = esc_html__('Slider', 'foodbakery');
            $foodbakery_static_text['foodbakery_dropdown_small'] = esc_html__('Dropdown', 'foodbakery');
            $foodbakery_static_text['foodbakery_dropdown_string'] = esc_html__('Dropdown: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_field_name_required'] = esc_html__('Field Name is Required', 'foodbakery');
            $foodbakery_static_text['foodbakery_whitespaces_not_allowed'] = esc_html__('Whitespaces not allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_special_characters_not_allowed'] = esc_html__('Special Characters are not allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_name_already_exists'] = esc_html__('Name already exists', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_options'] = esc_html__('Restaurant Setting Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_feature'] = esc_html__('Add Feature', 'foodbakery');
            $foodbakery_static_text['foodbakery_update_feature'] = esc_html__('Update Feature', 'foodbakery');
            $foodbakery_static_text['foodbakery_image_gallery'] = esc_html__('Image Gallery', 'foodbakery');
            $foodbakery_static_text['foodbakery_opening_hours'] = esc_html__('Opening Hours', 'foodbakery');
            $foodbakery_static_text['foodbakery_off_days'] = esc_html__('Off Days', 'foodbakery');
            $foodbakery_static_text['foodbakery_inquiry_form_choice'] = esc_html__('Inquiry Form', 'foodbakery');
            $foodbakery_static_text['foodbakery_similar_posts'] = esc_html__('Similar Posts', 'foodbakery');
            $foodbakery_static_text['foodbakery_featured_restaurant_image'] = esc_html__('Featured Restaurant Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_claim_restaurant'] = esc_html__('Claim Restaurant', 'foodbakery');
            $foodbakery_static_text['foodbakery_social_share'] = esc_html__('Social Share', 'foodbakery');
            $foodbakery_static_text['foodbakery_location_map'] = esc_html__('Location / Map', 'foodbakery');
            $foodbakery_static_text['foodbakery_review_ratings'] = esc_html__('Review & Ratings', 'foodbakery');
            $foodbakery_static_text['foodbakery_services_options'] = esc_html__('Services Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_financing_calculator_choice'] = esc_html__('Financing Calculator', 'foodbakery');
            $foodbakery_static_text['foodbakery_uncheck_features'] = esc_html__('Uncheck Features', 'foodbakery');
            $foodbakery_static_text['foodbakery_update'] = esc_html__('Update', 'foodbakery');
            $foodbakery_static_text['foodbakery_review_options'] = esc_html__('Reviews Detail', 'foodbakery');

            /////// Restaurant Setting Suggested Tags
            $foodbakery_static_text['foodbakery_select_cats'] = esc_html__('Select Cuisines', 'foodbakery');
            $foodbakery_static_text['foodbakery_select_cats_hint'] = esc_html__('Select restaurant type Cuisines from this dropdown.', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_new_cats_link'] = __('<a href="%s">Add new Cuisines</a>', 'foodbakery');

            /////// Restaurant Setting Suggested Tags
            $foodbakery_static_text['foodbakery_select_suggested_tags'] = esc_html__('Select Tags', 'foodbakery');
            $foodbakery_static_text['foodbakery_select_suggested_tags_hint'] = esc_html__('Select restaurant type suggested tags from this dropdown.', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_tag'] = esc_html__('Add Tag', 'foodbakery');
			$foodbakery_static_text['foodbakery_add_tag_name'] = esc_html__('Tag Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_tags'] = esc_html__('Suggested Tags', 'foodbakery');
            $foodbakery_static_text['foodbakery_update_tag'] = esc_html__('Update Tag', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_new_tag_link'] = __('<a href="%s">Add new tags</a>', 'foodbakery');

            /////// Restaurant Setting Features
            $foodbakery_static_text['foodbakery_restaurant_type_features_label'] = esc_html__('Enter Label', 'foodbakery');

            ///////
            $foodbakery_static_text['foodbakery_restaurant_options'] = esc_html__('Restaurants Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_select'] = esc_html__('Select', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_on'] = esc_html__('Posted on:', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_expired_on'] = esc_html__('Expired on:', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_yes'] = esc_html__('Yes', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_no'] = esc_html__('No', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_package'] = esc_html__('Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_status'] = esc_html__('Status', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_awaiting_activation'] = esc_html__('Awaiting Activation', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_active'] = esc_html__('Active', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_inactive'] = esc_html__('Inactive', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_delete'] = esc_html__('Delete', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_old_status'] = esc_html__('Restaurant Old Status', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_style'] = esc_html__('Style', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_default'] = esc_html__('Default - Selected From Plugin Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_2_columns'] = esc_html__('2 Columns', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_3_columns'] = esc_html__('3 Columns', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_classic'] = esc_html__('Classic', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_fancy'] = esc_html__('Fancy', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_map_view'] = esc_html__('Map View', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type'] = esc_html__('Restaurant Setting', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_calendar_demo'] = esc_html__('Calendar Demo', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_hint'] = esc_html__('Select Restaurant Setting', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_fields'] = esc_html__('Custom Fields', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_organization'] = esc_html__('Organization', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_mailing_information'] = esc_html__('Mailing Information', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_select_categories'] = esc_html__('Select Cuisines', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_categories'] = esc_html__('Cuisines', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_no_custom_field_found'] = esc_html__('No Custom Field Found', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_off_days'] = esc_html__('Off Days', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_opening_hours'] = esc_html__('Opening Hours', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_features'] = esc_html__('Features', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_featured'] = esc_html__('Featured', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_locations'] = esc_html__('Locations', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_add_location'] = esc_html__('Add Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_new_location'] = esc_html__('New Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_add_new_location'] = esc_html__('Add New Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_edit_location'] = esc_html__('Edit Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_no_locations_found.'] = esc_html__('No locations found.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_name'] = esc_html__('Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_slug'] = esc_html__('Slug', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posts'] = esc_html__('Posts', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurants'] = esc_html__('Restaurants', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_add_new_restaurant'] = esc_html__('Add New Restaurant', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_edit_restaurant'] = esc_html__('Edit Restaurant', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_new_restaurant_item'] = esc_html__('New Restaurant Item', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_view_restaurant_item'] = esc_html__('View Restaurant Item', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_search'] = esc_html__('Search', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_nothing_found'] = esc_html__('Nothing found', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_nothing_found_in_trash'] = esc_html__('Nothing found in Trash', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_company'] = esc_html__('Publisher', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_type'] = esc_html__('Restaurant Setting', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_specialisms'] = esc_html__('Specialisms', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_posted'] = esc_html__('Posted', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_expired'] = esc_html__('Expired', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_status'] = esc_html__('Status', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_categories'] = esc_html__('Cuisines', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_category'] = esc_html__('Cuisine', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_all_categories'] = esc_html__('All Cuisines', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_parent_category'] = esc_html__('Parent Cuisine', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_parent_category_clone'] = esc_html__('Parent Cuisine Clone', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_edit_category'] = esc_html__('Edit Cuisine', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_update_category'] = esc_html__('Update Cuisine', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_restaurant_add_new_category'] = esc_html__('Add New Cuisine', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_new_location'] = esc_html__('New Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_locations'] = esc_html__('Locations', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_add_new_location'] = esc_html__('Add New Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_edit_location'] = esc_html__('Edit Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_no_locations_found'] = esc_html__('No locations found.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_column_name'] = esc_html__('Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_column_slug'] = esc_html__('Slug', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_column_posts'] = esc_html__('Posts', 'foodbakery');
            // Restaurant Custom Fields
            $foodbakery_static_text['foodbakery_restaurant_custom_text'] = esc_html__('Text', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_required'] = esc_html__('Required', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_title'] = esc_html__('Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_meta_key'] = esc_html__('Meta Key', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_meta_key_hint'] = esc_html__('Please enter Meta Key without special character and space.', 'foodbakery');
            $foodbakery_static_text['dfoodbakery_restaurant_custom_place_holder'] = esc_html__('Place Holder', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_enable_search'] = esc_html__('Enable Search', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_default_value'] = esc_html__('Default Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_collapse_search'] = esc_html__('Collapse in Search', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_icon'] = esc_html__('Icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_text_area'] = esc_html__('Text Area', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_help_text'] = esc_html__('Help Text', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_rows'] = esc_html__('Rows', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_columns'] = esc_html__('Columns', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_dropdown'] = esc_html__('Dropdown', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_enable_multi_select'] = esc_html__('Enable Multi Select', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_post_multi_select'] = esc_html__('Post Multi Select', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_first_value'] = esc_html__('First Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_options'] = esc_html__('Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_date'] = esc_html__('Date', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_date_format'] = esc_html__('Date Format', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_email'] = esc_html__('Email', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_url'] = esc_html__('Url', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_range'] = esc_html__('Range', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_minimum_value'] = esc_html__('Minimum Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_maximum_value'] = esc_html__('Maximum Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_increment_step'] = esc_html__('Increment Step', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_enable_inputs'] = esc_html__('Enable Inputs', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_search_style'] = esc_html__('Search Style', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_input'] = esc_html__('Input', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_slider'] = esc_html__('Slider', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_Input_Slider'] = esc_html__('Input + Slider', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_please_select_atleast_one_option'] = esc_html__('Please select atleast one option for', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_field'] = esc_html__('field', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_all_settings_saved'] = esc_html__('All Settings Saved', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_field_name_required'] = esc_html__('Field name is required.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_whitespaces_not_allowed'] = esc_html__('Whitespaces not allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_special_characters_not_allowed'] = esc_html__('Special character not allowed but only (_,-).', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_name_already_exist'] = esc_html__('Name already exist.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_custom_name_available'] = esc_html__('Name Available.', 'foodbakery');
            // Restaurant Images Gallery/opening hours.
            $foodbakery_static_text['foodbakery_restaurant_image_gallery'] = esc_html__('Images Gallery', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_gallery_image'] = esc_html__('Gallery Images', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_schedule_with_time'] = esc_html__('Schedule With Time', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_opening_time'] = esc_html__('Opening Time', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_closing_time'] = esc_html__('Closing Time', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_monday_on'] = esc_html__('Monday On?', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_monday'] = esc_html__('Monday', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_tuesday'] = esc_html__('Tuesday', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_tuesday_on'] = esc_html__('Tuesday On?', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_wednesday'] = esc_html__('Wednesday', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_wednesday_on'] = esc_html__('Wednesday On?', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_thursday'] = esc_html__('Thursday', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_thursday_on'] = esc_html__('Thursday On?', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_friday'] = esc_html__('Friday', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_friday_on'] = esc_html__('Friday On?', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_saturday'] = esc_html__('Saturday', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_saturday_on'] = esc_html__('Saturday On?', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_sunday'] = esc_html__('Sunday', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_sunday_on'] = esc_html__('Sunday On?', 'foodbakery');
            //Restaurant Page element
            $foodbakery_static_text['foodbakery_restaurant_page_elements'] = esc_html__('Page Elements', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_page_inquire_form'] = esc_html__('Inquire Form ON / OFF', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_page_financing_calculator'] = esc_html__('Financing calculator ON / OFF', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_page_similar_posts'] = esc_html__('Similar Posts', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_page_featured_restaurant_image'] = esc_html__('Featured Restaurant Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_page_claim_restaurant'] = esc_html__('Claim Restaurant', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_page_social_share'] = esc_html__('Social Share', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_page_review_ratings'] = esc_html__('Review & Ratings', 'foodbakery');
            //Restaurant Posted by
            $foodbakery_static_text['foodbakery_restaurant_posted_by'] = esc_html__('Posted by', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_user_profile_data'] = esc_html__('User Profile Data', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_logo'] = esc_html__('Logo', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_full_name_business_name'] = esc_html__('Full Name / Business Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_email'] = esc_html__('Email', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_website'] = esc_html__('Website', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_facebook'] = esc_html__('Facebook', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_twitter'] = esc_html__('Twitter', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_linkedIn'] = esc_html__('LinkedIn', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_google_plus'] = esc_html__('Google Plus', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_phone_no'] = esc_html__('Phone No', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_posted_select_a_user'] = esc_html__('Select a user', 'foodbakery');
            //Restaurant Services
            $foodbakery_static_text['foodbakery_restaurant_services'] = esc_html__('Services', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_services_title'] = esc_html__('Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_services_description'] = esc_html__('Description', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_services_icon'] = esc_html__('Icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_services_price'] = esc_html__('Price', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_services_capacity'] = esc_html__('Capacity', 'foodbakery');
			
			//Resaurents Menu Items
            $foodbakery_static_text['foodbakery_restaurant_menu_items'] = esc_html__('Menu Items', 'foodbakery');
			$foodbakery_static_text['foodbakery_restaurant_menus'] = esc_html__('Restaurant Menus', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_menu_item_title'] = esc_html__('Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_menu_item_desc'] = esc_html__('Description', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_menu_item_icon'] = esc_html__('Icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_menu_item_price'] = esc_html__('Price', 'foodbakery');
            
            //Restaurant save post options
            $foodbakery_static_text['foodbakery_restaurant_save_post_browse'] = esc_html__('Browse', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_load_icomoon'] = esc_html__('Load from IcoMoon selection.json', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_country'] = esc_html__('Country', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_select_country'] = esc_html__('Select Country', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_city'] = esc_html__('City', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_select_city'] = esc_html__('Select City', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_complete_address'] = esc_html__('Complete Address', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_complete_address_hint'] = esc_html__('Enter you complete address with city, state or country.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_find_on_map'] = esc_html__('Find on Map', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_address'] = esc_html__('Address', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_latitude'] = esc_html__('Latitude', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_longitude'] = esc_html__('Longitude', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_search_location_on_map'] = esc_html__('Search This Location on Map', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_search_location'] = esc_html__('Search Location', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_update_map'] = esc_html__('update map', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_please_wait'] = esc_html__('Please wait...', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_loaded_icons'] = esc_html__('Successfully loaded icons', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_error_try_again'] = esc_html__('Error: Try Again?', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_image'] = esc_html__('Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_choose_icon'] = esc_html__('Choose Icon', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_ISO_code'] = esc_html__('ISO Code', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_publisher'] = esc_html__('Publisher', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_restaurants'] = esc_html__('Restaurants', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_save_post_register'] = esc_html__('Register', 'foodbakery');


            // post type price tables
            $foodbakery_static_text['foodbakery_post_type_price_table_name'] = esc_html__('Price Tables', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_singular_name'] = esc_html__('Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_menu_name'] = esc_html__('Price Tables', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_name_admin_bar'] = esc_html__('Price Tables', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_add_new'] = esc_html__('Add Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_add_new_item'] = esc_html__('Add Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_new_item'] = esc_html__('Add Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_edit_item'] = esc_html__('Edit Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_view_item'] = esc_html__('Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_all_items'] = esc_html__('Price Tables', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_search_items'] = esc_html__('Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_not_found'] = esc_html__('Price Tables', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_not_found_in_trash'] = esc_html__('Price Tables', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_description'] = esc_html__('Edit Price Table', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_price_table_meta_number_of_services'] = esc_html__('Price Table', 'foodbakery');
            // price tables meta
            $foodbakery_static_text['foodbakery_restaurant_price_tables_options'] = esc_html__('Price Table Options', 'foodbakery');

            // post type packages
            $foodbakery_static_text['foodbakery_post_type_package_name'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_singular_name'] = esc_html__('Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_menu_name'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_name_admin_bar'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_add_new'] = esc_html__('Add Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_add_new_item'] = esc_html__('Add Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_new_item'] = esc_html__('Add Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_edit_item'] = esc_html__('Edit Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_view_item'] = esc_html__('Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_all_items'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_search_items'] = esc_html__('Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_not_found'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_not_found_in_trash'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_description'] = esc_html__('Edit Membership', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_type_package_meta_number_of_services'] = esc_html__('Membership', 'foodbakery');
            // Memberships meta
            $foodbakery_static_text['foodbakery_restaurant_orders_inquiries_options'] = esc_html__('Orders/Inquiries Options', 'foodbakery');
            // Memberships meta
            $foodbakery_static_text['foodbakery_restaurant_packages_options'] = esc_html__('Membership Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_restaurant_allowed'] = esc_html__('Number of Restaurant ', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_restaurant_allowed_hint'] = esc_html__('Add no of restaurant allowed in this package.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_duration'] = esc_html__('Membership Duration ( Days )', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_duration_hint'] = esc_html__('Add duration of package.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_days'] = esc_html__('Days', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_restaurant_duration'] = esc_html__('Restaurant Duration ( Days )', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_restaurant_duration_hint'] = esc_html__('Add duration of restaurant.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_month'] = esc_html__('Month', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_services'] = esc_html__('Service Area Size', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_services_hint'] = esc_html__('Add no of services allowed in this package.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_pictures'] = esc_html__('No of Pictures Allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_pictures_hint'] = esc_html__('Add no of pictures allowed in this package.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_documents'] = esc_html__('Number of Documents', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_documents_hint'] = esc_html__('Add no of documents allowed in this package.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_tags'] = esc_html__('Search Tags', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_num_tags_hint'] = esc_html__('Add no of tags allowed in this package.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_reviews'] = esc_html__('Reviews Allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_reviews_hint'] = esc_html__('Reviews On/Off', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_home_featured_restaurant'] = esc_html__('Home Featured Restaurant', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_greater_val_error'] = esc_html__('Please enter a value less than number of restaurant Allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_number_of_top_cat_restaurants'] = esc_html__('Top Categories Restaurants', 'foodbakery');
            $foodbakery_static_text['foodbakery_number_of_top_cat_restaurants_hint'] = esc_html__('Add no of top categories restaurants.', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_tile'] = esc_html__('Membership name', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_type'] = esc_html__('Type', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_type_hint'] = esc_html__('Select package type from this dropdown.', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_type_free'] = esc_html__('Free', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_type_paid'] = esc_html__('Paid', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_top_categories'] = esc_html__('Top of Categories', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_phone_num'] = esc_html__('Listed Phone Number', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_website_link'] = esc_html__('Website Link', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_cover_image'] = esc_html__('Cover Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_social_impressions'] = esc_html__('Social Impressions Reach', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_respond_reviews'] = esc_html__('Can respond to reviews', 'foodbakery');
            
            $foodbakery_static_text['foodbakery_restaurant_packages_24support'] = esc_html__('24 Support', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_analytics_tracking'] = esc_html__('Analytics and Tracking', 'foodbakery');
            $foodbakery_static_text['foodbakery_number_of_feature_restaurants'] = esc_html__('Featured Restaurants', 'foodbakery');
            $foodbakery_static_text['foodbakery_number_of_feature_restaurants_hint'] = esc_html__('Add no of featured restaurants.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_packages_seo'] = esc_html__('Search Engine Optimization', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_price'] = esc_html__('Price', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_price_hint'] = esc_html__('Add package price in this field.', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_icon'] = esc_html__('Icon', 'foodbakery');


            // Import/Export users
            $foodbakery_static_text['foodbakery_restaurant_users_zip_file'] = esc_html__('Zip file', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_zip_file_desc'] = __('You may want to see <a href="%s">the demo file</a>.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_zip_notification'] = esc_html__('Notification', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_zip_send_new_users'] = esc_html__('Send to new users', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_password_nag'] = esc_html__('Password nag', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_password_nag_hint'] = esc_html__('Show password nag on new users signon', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_update'] = esc_html__('Users update', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_update_hint'] = esc_html__('Update user when a username or email exists', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_import_users'] = esc_html__('Import Users', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_export_all_users'] = esc_html__('Export All Users', 'foodbakery');
            // Import/Export users errors/Notices
            $foodbakery_static_text['foodbakery_restaurant_users_update'] = esc_html__('Import / Export Users', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_export'] = esc_html__('Export Users', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_data_import_error'] = esc_html__('There is an error in your users data import, please try later', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_import_notice'] = esc_html__('Notice: please make the foodbakery %s writable so that you can see the error log.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_error_file_upload'] = esc_html__('Error during file upload.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_cannot_extract_data'] = esc_html__('Cannot extract data from uploaded file or no file was uploaded.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_not_imported'] = esc_html__('No user was successfully imported%s.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_imported_some_success'] = esc_html__('Some users were successfully imported but some were not%s.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_import_successful'] = esc_html__('Users import was successful.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_invalid_file_type'] = esc_html__('You have selected invalid file type, Please try again.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_export_successful'] = esc_html__('Users has been done export successful.', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_import_user_data'] = esc_html__('Import User Data', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_users_sufficient_permissions'] = esc_html__('You do not have sufficient permissions to access this page.', 'foodbakery');

            // user meta 
            $foodbakery_static_text['foodbakery_user_meta_profile_approved'] = esc_html__('Profile Approved', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_extra_profile_information'] = esc_html__('Extra profile information', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_my_profile'] = esc_html__('My Profile', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_profile_settings'] = esc_html__('Profile Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_logo'] = esc_html__('Logo', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_gallery'] = esc_html__('Gallery', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_full_name_or_business_nme'] = esc_html__('Full Name / Business Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_email'] = esc_html__('Email', 'foodbakery');
            $foodbakery_static_text['foodbakery_email_string'] = esc_html__('Email: %s', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_website'] = esc_html__('Website', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_social_networks'] = esc_html__('Social Networks', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_facebook'] = esc_html__('Facebook', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_twitter'] = esc_html__('Twitter', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_linkedIn'] = esc_html__('LinkedIn', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_google_plus'] = esc_html__('Google Plus', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_phone_no'] = esc_html__('Phone No', 'foodbakery');
            $foodbakery_static_text['foodbakery_user_meta_mailing_information'] = esc_html__('Mailing Information', 'foodbakery');

            // restaurant type meta
            $foodbakery_static_text['foodbakery_restaurant_type_meta_custom_fields'] = esc_html__('Extra Fields', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_form_builders'] = esc_html__('Booking Form', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_features'] = esc_html__('Restaurants Features', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_suggested_tags'] = esc_html__('Suggested Tags', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_categories'] = esc_html__('Cuisines', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_page_elements'] = esc_html__('Page Elements', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_required_elements'] = esc_html__('Detail Page Options', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_settings'] = esc_html__('Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_general_settings'] = esc_html__('General Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_enable_upload'] = esc_html__('Enable Upload', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_image_per_ad'] = esc_html__('Image per Ad', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_price'] = esc_html__('Price', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_price_switch'] = esc_html__('Price', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_price_field_label'] = esc_html__('Price Field Label', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_enable_price_search'] = esc_html__('Enable Price Search', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_min_range'] = esc_html__('Min Range', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_max_range'] = esc_html__('Max Range', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_increament'] = esc_html__('Increament', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_price_search_style'] = esc_html__('Price Search Style', 'foodbakery');

            $foodbakery_static_text['foodbakery_no_of_pictures_allowed'] = esc_html__('Number of Pictures Allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_no_of_tags_allowed'] = esc_html__('Number of Tags Allowed', 'foodbakery');
            $foodbakery_static_text['foodbakery_auto_reviews_approval'] = esc_html__('Auto Reviews Approval', 'foodbakery');
            $foodbakery_static_text['foodbakery_ads_images_videos_limit'] = esc_html__('Ads Images / Videos Limit', 'foodbakery');
            $foodbakery_static_text['foodbakery_opening_hour_time_lapse'] = esc_html__('Opening Hour Time Laps ( In Minutes )', 'foodbakery');
            $foodbakery_static_text['foodbakery_restaurant_type_meta_feature_add_row'] = esc_html__('Add New Feature', 'foodbakery');
			
			$foodbakery_static_text['``'] = esc_html__('Orders Statuses', 'foodbakery');
			$foodbakery_static_text['foodbakery_orders_inquiries_add_status'] = esc_html__('Add New order Status', 'foodbakery');
			$foodbakery_static_text['foodbakery_orders_inquiries_enter_status'] = esc_html__('Enter order Status', 'foodbakery');
			
			$foodbakery_static_text['foodbakery_booking_status'] = esc_html__('Booking Statuses', 'foodbakery');
			$foodbakery_static_text['foodbakery_booking_add_status'] = esc_html__('Add New Booking Status', 'foodbakery');
			$foodbakery_static_text['foodbakery_booking_enter_status'] = esc_html__('Enter Booking Status', 'foodbakery');
			
			// Restaurants Menus Categories
			$foodbakery_static_text['foodbakery_restaurants_menus'] = esc_html__('Restaurants Menus', 'foodbakery');
			$foodbakery_static_text['foodbakery_restaurants_menus_add'] = esc_html__('Add New Restaurant Menu', 'foodbakery');
			$foodbakery_static_text['foodbakery_restaurants_menus_enter'] = esc_html__('Enter Restaurant Menu', 'foodbakery');

            // publisher profile tab
            $foodbakery_static_text['foodbakery_publisher_profile_settings'] = esc_html__('Profile Setting', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_first_name'] = esc_html__('First name', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_last_name'] = esc_html__('Last Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_display_name'] = esc_html__('Display Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_name'] = esc_html__('Publisher Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_email_address'] = esc_html__('Email Address', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_profile_type'] = esc_html__('Profile Type', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_profile_individual'] = esc_html__('Buyer', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_profile_company'] = esc_html__('Restaurant', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_change_password'] = esc_html__('Password Change', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_current_password'] = esc_html__('Current Password', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_new_password'] = esc_html__('New Password', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_new_password_em'] = esc_html__('leave blank to leave unchanged', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_confirm_new_password'] = esc_html__('Confirm New Password', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_address'] = esc_html__('Address', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_country'] = esc_html__('Country', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_city_town'] = esc_html__('Town / City', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_upload_profile_picture'] = esc_html__('Upload a profile picture or choose one of the following', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_upload_featured_image'] = esc_html__('Upload a featured image', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_upload_profile_picture_button'] = esc_html__('Upload Picture', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_city_town'] = esc_html__('Town / City', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_first_name_empty_error'] = esc_html__('first name should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_last_name_empty_error'] = esc_html__('last name should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_display_name_empty_error'] = esc_html__('display name should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_name_empty_error'] = esc_html__('Profile Url should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_name_exist_error'] = esc_html__('Profile Url already taken', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_biography_empty_error'] = esc_html__('Biography should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_phone_empty_error'] = esc_html__('Phone number should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_email_empty_error'] = esc_html__('email should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_name_empty_error'] = esc_html__('Restaurant name should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_email_valid_error'] = esc_html__('email address is not valid', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_email_exists_error'] = esc_html__('email already exists!', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_invalid_current_pass'] = esc_html__('Invalid current password', 'foodbakery');
	    $foodbakery_static_text['foodbakery_publisher_empty_current_pass'] = esc_html__('Current password should not be empty', 'foodbakery');
	    $foodbakery_static_text['foodbakery_publisher_empty_new_pass'] = esc_html__('New password should not be empty', 'foodbakery');
	    $foodbakery_static_text['foodbakery_publisher_empty_conform_new_pass'] = esc_html__('Confirm new password should not be empty', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_pass_and_confirmpass_not_mached'] = esc_html__('Password and confirm password did not matched', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_updated_success_mesage'] = esc_html__('Updated successfully', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_no_permissions_to_upload'] = esc_html__('No permissions to upload file', 'foodbakery');
            $foodbakery_static_text['foodbakery_cropping_file_error'] = esc_html__('something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini', 'foodbakery');

            /*
             * Publishers Post Type
             */
            $foodbakery_static_text['foodbakery_publishers'] = esc_html__('Publishers', 'foodbakery');
            $foodbakery_static_text['foodbakery_company'] = esc_html__('Publisher', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_company'] = esc_html__('Add New Publisher', 'foodbakery');
            $foodbakery_static_text['foodbakery_edit_company'] = esc_html__('Edit Publisher', 'foodbakery');

            /*
             * Member Permissions
             */
            $foodbakery_static_text['profile_manage'] = esc_html__('Profile', 'foodbakery');
            $foodbakery_static_text['restaurants_manage'] = esc_html__('Restaurant', 'foodbakery');
            $foodbakery_static_text['orders_manage'] = esc_html__('Orders', 'foodbakery');
			$foodbakery_static_text['bookings_manage'] = esc_html__('Bookings', 'foodbakery');
            $foodbakery_static_text['reviews_manage'] = esc_html__('Reviews', 'foodbakery');
            $foodbakery_static_text['packages_manage'] = esc_html__('Memberships', 'foodbakery');
            $foodbakery_static_text['shortlists_manage'] = esc_html__('Shortlists', 'foodbakery');

            // Memberships add fields
            $foodbakery_static_text['foodbakery_add_field'] = esc_html__('Add Membership Field', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_field'] = esc_html__('Membership Field', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_field_label'] = esc_html__('Label', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_field_type'] = esc_html__('Field Type', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_field_type_hint'] = esc_html__('Select field type from this dropdown.', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_field_single_choice'] = esc_html__('Single Choice', 'foodbakery');
            $foodbakery_static_text['foodbakery_add_field_single_line'] = esc_html__('Single Line', 'foodbakery');
            $foodbakery_static_text['foodbakery_package_additional_fields'] = esc_html__('Membership Additional Fields', 'foodbakery');
            $foodbakery_static_text['foodbakery_company_details'] = esc_html__('Publisher Data', 'foodbakery');
            $foodbakery_static_text['foodbakery_phone'] = esc_html__('Phone Number', 'foodbakery');
            $foodbakery_static_text['foodbakery_email_address'] = esc_html__('Email', 'foodbakery');
            $foodbakery_static_text['foodbakery_website'] = esc_html__('Website', 'foodbakery');


            $foodbakery_static_text['foodbakery_publisher_company_settings'] = esc_html__('Publisher Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_name'] = esc_html__('Display Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_slug'] = esc_html__('Profile Url', 'foodbakery');
            $foodbakery_static_text['foodbakery_add'] = esc_html__('Add', 'foodbakery');
            $foodbakery_static_text['foodbakery_update'] = esc_html__('Update', 'foodbakery');


            $foodbakery_static_text['foodbakery_publisher_company_website'] = esc_html__('Publisher Website', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_phone'] = esc_html__('Publisher Phone', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_description'] = esc_html__('Publisher Description', 'foodbakery');
            $foodbakery_static_text['company_profile_manage'] = esc_html__('Publisher Profile Manage', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_account_display_name'] = esc_html__('Account Display Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_publisher_company_name'] = esc_html__('Restaurant Name', 'foodbakery');

            /*
             * widgets
             */
            $foodbakery_static_text['foodbakery_var_locations'] = esc_html__('Cs:Locations', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_locations_description'] = esc_html__('Foodbakery Locations widget');
            $foodbakery_static_text['foodbakery_widget_title'] = esc_html__('Title');
            $foodbakery_static_text['foodbakery_widget_title_desc'] = esc_html__('Enter title here.');
			$foodbakery_static_text['foodbakery_widget_desc'] = esc_html__('Description');
            $foodbakery_static_text['foodbakery_widget_desc_hint'] = esc_html__('Enter description here.');
			$foodbakery_static_text['foodbakery_widget_button_label'] = esc_html__('Button Label');
			$foodbakery_static_text['foodbakery_widget_button_label_hint'] = esc_html__('Button label here.');
			$foodbakery_static_text['foodbakery_widget_button_url'] = esc_html__('Button Url');
			$foodbakery_static_text['foodbakery_widget_button_url_hint'] = esc_html__('Button url here.');
			$foodbakery_static_text['foodbakery_widget_bg_color'] = esc_html__('Bg Color');
			$foodbakery_static_text['foodbakery_widget_bg_color_hint'] = esc_html__('Choose widget background color here.');
            $foodbakery_static_text['choose_location_fields'] = esc_html__('Locations');
            $foodbakery_static_text['choose_location_fields_desc'] = esc_html__('Select Locations');
            
            $foodbakery_static_text['choose_cuisines_fields'] = esc_html__('Cuisines');
            $foodbakery_static_text['choose_cuisines_fields_desc'] = esc_html__('Select Cuisines');


            // Banners
            $foodbakery_static_text['foodbakery_banner_single_banner'] = esc_html__(' Single Banner', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_random_banner'] = esc_html__('Random Banners', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_view'] = esc_html__('Banner View ', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_view_hint'] = esc_html__('Select Banner View ', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_search_pagination'] = esc_html__('Show Pagination', 'foodbakery');

            $foodbakery_static_text['foodbakery_banner_no_of_banner'] = esc_html__('Number of Banners', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_no_of_banner_hint'] = esc_html__('Please Number of Banners here', 'foodbakery');

            $foodbakery_static_text['foodbakery_banner_code'] = esc_html__('Banner Code', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_code_hint'] = esc_html__('Please Banner Code here', 'foodbakery');

            $foodbakery_static_text['foodbakery_banner_title_field'] = esc_html__('Banner Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_title_field_hint'] = esc_html__('Please enter Banner Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_style'] = esc_html__('Banner Style', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_style_hint'] = esc_html__('Please Select  Banner Style', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_type'] = esc_html__('Banner Type', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_type_hint'] = esc_html__('Please enter  Banner Type', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_type_top'] = esc_html__('Top Banner', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_type_bottom'] = esc_html__('Bottom Banner', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_type_restaurant_detail'] = esc_html__('Restaurant Detail Banner', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_type_restaurant'] = esc_html__('Restaurant Banner', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_type_restaurant_leftfilter'] = esc_html__('Restaurant Left Filter Banner', 'foodbakery');            
            $foodbakery_static_text['foodbakery_banner_type_sidebar'] = esc_html__('Sidebar Banner', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_url_field'] = esc_html__('Banner Url', 'foodbakery');


            $foodbakery_static_text['foodbakery_banner_type_vertical'] = esc_html__('Vertical Banner', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_image'] = esc_html__('Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_code'] = esc_html__('Code', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_ad_sense_code'] = esc_html__('Ad sense Code', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_ad_sense_code_hint'] = esc_html__('Please enter Banner Ad sense Code', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_image_hint'] = esc_html__('Please Select Banner Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_target'] = esc_html__('Target', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_target_hint'] = esc_html__('Please select Banner Link Target', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_target_self'] = esc_html__('Self', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_target_blank'] = esc_html__('Blank', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_already_added'] = esc_html__('Already Added Banners', 'foodbakery');

            $foodbakery_static_text['foodbakery_banner_table_title'] = esc_html__('Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_table_style'] = esc_html__('Style', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_table_image'] = esc_html__('Image', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_table_clicks'] = esc_html__('Clicks', 'foodbakery');
            $foodbakery_static_text['foodbakery_banner_table_shortcode'] = esc_html__('Shortcode', 'foodbakery');

	    /*
	     * Contact form
	     */
	    
	    $foodbakery_static_text['foodbakery_var_edit_form'] = esc_html__('Contact Form Options', 'foodbakery');
	     $foodbakery_static_text['foodbakery_var_element_title'] = esc_html__('Element Title', 'foodbakery');
	     $foodbakery_static_text['foodbakery_var_element_title_hint'] = esc_html__('Enter element title here.', 'foodbakery');
	     $foodbakery_static_text['foodbakery_var_title_alignment'] = esc_html__('Title Alignment', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_title_alignment_hint'] = esc_html__('Set element title alignment here', 'foodbakery');
	     $foodbakery_static_text['foodbakery_var_align_left'] = esc_html__('Align Left', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_align_right'] = esc_html__('Align Right', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_align_center'] = esc_html__('Align Center', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_text_us'] = esc_html__('Text Us', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_text_us_hint'] = esc_html__('Element Title', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_call_us'] = esc_html__('Call Us', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_call_us_hint'] = esc_html__('Element Title', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_address_contact'] = esc_html__('Address', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_address_contact_hint'] = esc_html__('Element Title', 'foodbakery');
	     $foodbakery_static_text['foodbakery_var_form_title_contact'] = esc_html__('Form Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_form_title_contact_hint'] = esc_html__('Address', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_send_to'] = esc_html__('Receiver Email', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_send_to_hint'] = esc_html__('Receiver, or receivers of the mail.', 'foodbakery');
	     $foodbakery_static_text['foodbakery_var_success_message'] = esc_html__('Success Message', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_success_message_hint'] = esc_html__('Enter Mail Successfully Send Message.', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_error_message'] = esc_html__('Error Message', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_error_message_hint'] = esc_html__('Enter Error Message In any case Mail Not Submited.', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_save'] = esc_html__('Save', 'foodbakery');
	     $foodbakery_static_text['foodbakery_var_insert'] = esc_html__('Insert', 'foodbakery');
	     // frontend strings
	    $foodbakery_static_text['foodbakery_var_contact_default_success_msg'] = esc_html__('Email has been sent Successfully', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_default_error_msg'] = esc_html__('An error Occured, please try again later', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_first_name'] = esc_html__('First Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_contact_last_name'] = esc_html__('Last Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_contact_phone'] = esc_html__('Phone Number', 'foodbakery');
            $foodbakery_static_text['foodbakery_var_contact_email'] = esc_html__('Email', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_text_here'] = esc_html__('Text here..', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_button_text'] = esc_html__('Submit message', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_received'] = esc_html__('Contact Form Received', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_valid_email'] = esc_html__('Please enter a valid email.', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_email_should_not_be_empty'] = esc_html__('Email should not be empty.', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_full_name'] = esc_html__('Name', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_email'] = esc_html__('Email', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_subject'] = esc_html__('Subject', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_check_field'] = esc_html__('Subscribe and Get latest updates and offers by Email', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_ip_address'] = esc_html__('IP Address:', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_email_address'] = esc_html__('Email Address *', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_contact_phone_number'] = esc_html__('Phone number', 'foodbakery');
	    $foodbakery_static_text['foodbakery_var_text_here_message'] = esc_html__('Message', 'foodbakery');
            
	    $foodbakery_static_text['foodbakery_publisher_new_password_empty_error'] = esc_html__('New password should not be empty', 'foodbakery');
	    $foodbakery_static_text['foodbakery_publisher_password_mismatch_error'] = esc_html__('New password and confirm password does not match', 'foodbakery');
	     
	     
	     
	    /*
	     * End contact form
	     */

            
            
            $foodbakery_static_text['foodbakery_google_service_error'] = esc_html__('A service error occurred:', 'foodbakery');
            $foodbakery_static_text['foodbakery_google_client_error'] = esc_html__('A client error occurred:', 'foodbakery');
            $foodbakery_static_text['foodbakery_google_error_code'] = esc_html__('Error Code', 'foodbakery');
            $foodbakery_static_text['foodbakery_google_auth_failed'] = esc_html__('Authentication failed due to Invalid Credentials', 'foodbakery');
            $foodbakery_static_text['foodbakery_google_already_linked'] = esc_html__('This Google profile is already linked with other account. Linking process failed!', 'foodbakery');
            
            $foodbakery_static_text['foodbakery_property_visibility_updated_msg'] = esc_html__('your DB structure updated successfully.', 'foodbakery');
	    $foodbakery_static_text['foodbakery_status_processing'] = esc_html__('Processing', 'foodbakery');
	    $foodbakery_static_text['foodbakery_status_completed'] = esc_html__('Completed', 'foodbakery');
	    $foodbakery_static_text['foodbakery_status_cancelled'] = esc_html__('Cancelled', 'foodbakery');
	    
	    $foodbakery_static_text['foodbakery_transaction_status_pending'] = esc_html__('Pending', 'foodbakery');
	    $foodbakery_static_text['foodbakery_transaction_status_approved'] = esc_html__('Approved', 'foodbakery');
	    $foodbakery_static_text['foodbakery_transaction_status_cancelled'] = esc_html__('Cancelled', 'foodbakery');
	    
	    
	    
	    
                    
            /*
             * Use this filter to add more strings from Add on.
             */
            $foodbakery_static_text = apply_filters('foodbakery_plugin_text_strings', $foodbakery_static_text);
            
//			
            return $foodbakery_static_text;
        }

    }

    new foodbakery_plugin_all_strings;
}
