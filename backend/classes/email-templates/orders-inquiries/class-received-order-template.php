<?php

/**
 * Received Order Email Template
 *
 * @since 1.0
 * @package    Foodbakery
 */
if (!class_exists('Foodbakery_received_order_email_template')) {

    class Foodbakery_received_order_email_template
    {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $template_type;
        public $email_template_index;
        public $order_id;
        public $is_email_sent;
        public static $is_email_sent1;
        public $template_group;

        public function __construct()
        {

            $this->email_template_type = 'Order Received';

            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"> <table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"> <tbody> <tr> <td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"> <h1 style="color: #fff;">Order Received</h1> </td></tr><tr> <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tbody> <tr> <td width="260" valign="top"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tbody> <tr> <td style="padding-bottom:8px;">Hi, [RESTAURANT_NAME]</td></tr><tr> <td style="padding-bottom:8px;">[ORDER_USER_NAME] has submitted an order on your restaurant ( <a href="[RESTAURANT_LINK]">[RESTAURANT_NAME]</a> ).</td></tr><tr> <td>User Name: [ORDER_USER_NAME]</td></tr><tr> <td>Phone Number: [ORDER_PHONE_NUMBER]</td></tr><tr> <td>Email: [ORDER_USER_EMAIL]</td></tr><tr> <td>Address: [ORDER_USER_ADDRESS]</td></tr><tr> <td>You can see order on following link:</td></tr><tr> <td>[ORDER_LINK]</td></tr><tr> <td>Order Detail:</td></tr><tr> <td>[ORDER_DETAIL]</td></tr></tbody> </table> </td></tr></tbody> </table> </td></tr><tr> <td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"> <table border="0" width="100%" cellspacing="0" cellpadding="0"> <tbody> <tr> <td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2019</td></tr></tbody> </table> </td></tr></tbody> </table></div></body></html>';

            $this->email_template_variables = array(
                array(
                    'tag' => 'ORDER_DETAIL',
                    'display_text' => 'Order Detail',
                    'value_callback' => array($this, 'get_order_detail'),
                ),
                array(
                    'tag' => 'RESTAURANT_NAME',
                    'display_text' => 'Restaurant Name',
                    'value_callback' => array($this, 'get_restaurant_name'),
                ),
                array(
                    'tag' => 'RESTAURANT_PHONE',
                    'display_text' => 'Restaurant Phone',
                    'value_callback' => array($this, 'get_restaurant_phone'),
                ),
                array(
                    'tag' => 'RESTAURANT_EMAIL',
                    'display_text' => 'Restaurant Email',
                    'value_callback' => array($this, 'get_restaurant_email'),
                ),
                array(
                    'tag' => 'RESTAURANT_MANAGER_NAME',
                    'display_text' => 'Restaurant Manager Name',
                    'value_callback' => array($this, 'get_restaurant_manager_name'),
                ),
                array(
                    'tag' => 'RESTAURANT_MANAGER_PHONE',
                    'display_text' => 'Restaurant Manager Phone',
                    'value_callback' => array($this, 'get_restaurant_manager_phone'),
                ),
                array(
                    'tag' => 'RESTAURANT_LINK',
                    'display_text' => 'Restaurant Link',
                    'value_callback' => array($this, 'get_restaurant_link'),
                ),
                array(
                    'tag' => 'ORDER_USER_NAME',
                    'display_text' => 'Order User Name',
                    'value_callback' => array($this, 'get_order_user_name'),
                ),
                array(
                    'tag' => 'ORDER_USER_EMAIL',
                    'display_text' => 'Order User Email',
                    'value_callback' => array($this, 'get_order_user_email'),
                ),
                array(
                    'tag' => 'ORDER_PHONE_NUMBER',
                    'display_text' => 'Order User Phoner number',
                    'value_callback' => array($this, 'get_order_user_phone'),
                ),
                array(
                    'tag' => 'ORDER_USER_ADDRESS',
                    'display_text' => 'Order User Address',
                    'value_callback' => array($this, 'get_order_user_address'),
                ),
                array(
                    'tag' => 'ORDER_NUMBER',
                    'display_text' => 'Order Number',
                    'value_callback' => array($this, 'get_order_number'),
                ),
                array(
                    'tag' => 'ORDER_LINK',
                    'display_text' => 'Order LINK',
                    'value_callback' => array($this, 'get_order_link'),
                ),
                array(
                    'tag' => 'ORDER_STATUS',
                    'display_text' => 'Order Status',
                    'value_callback' => array($this, 'get_order_status'),
                ),
                array(
                    'tag' => 'ORDER_DELIVERY_DATE',
                    'display_text' => 'Order Delivery Date',
                    'value_callback' => array($this, 'get_order_delivery_date'),
                ),
            );
            $this->template_group = 'Orders';

            $this->email_template_index = 'received-order-template';
            add_action('init', array($this, 'add_email_template'), 5);
            add_filter('foodbakery_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('foodbakery_received_order_email', array($this, 'foodbakery_received_order_email_callback'), 12, 1);
        }

        public function foodbakery_received_order_email_callback($order_id = '')
        {

            $this->order_id = $order_id;
            $template = $this->get_template();
            // checking email notification is enable/disable
            if (isset($template['email_notification']) && $template['email_notification'] == 1) {

                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');
                // getting template fields

                $subject = (isset($template['subject']) && $template['subject'] != '') ? $template['subject'] : esc_html__('Received Order', 'foodbakery');
                $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($this->get_order_user_name()) . ' <' . $this->get_order_user_email() . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_restaurant_email();
                $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    'message' => $template['email_template'],
                    'email_type' => $email_type,
                    'class_obj' => $this,
                );
                do_action('foodbakery_send_mail', $args);
                Foodbakery_received_order_email_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public function add_email_template()
        {
            $email_templates = array();
            $email_templates[$this->template_group] = array();
            $email_templates[$this->template_group][$this->email_template_index] = array(
                'title' => $this->email_template_type,
                'template' => $this->email_default_template,
                'email_template_type' => $this->email_template_type,
                'is_recipients_enabled' => TRUE,
                'description' => esc_html__('This template is used to send email when restaurant user receive order.', 'foodbakery'),
                'jh_email_type' => 'html',
            );
            do_action('foodbakery_load_email_templates', $email_templates);
        }

        public function template_settings_callback($email_template_options)
        {

            $email_template_options["types"][] = $this->email_template_type;

            $email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

            $email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

            return $email_template_options;
        }

        public function get_template()
        {
            return wp_foodbakery::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
        }

        function get_order_user_name()
        {
            $order_user = get_post_meta($this->order_id, 'foodbakery_order_user', true);
            $order_user_id = foodbakery_user_id_form_company_id($order_user);
            $order_user_info = get_userdata($order_user_id);
            return isset($order_user_info->display_name) ? $order_user_info->display_name : '';
        }

        function get_order_user_email()
        {
            $order_user = get_post_meta($this->order_id, 'foodbakery_order_user', true);
            $order_user_id = foodbakery_user_id_form_company_id($order_user);
            $order_user_info = get_userdata($order_user_id);
            return isset($order_user_info->user_email) ? $order_user_info->user_email : '';
        }

        function get_order_user_address()
        {
            $trans_id = $this->get_trans_id($this->order_id);
            $address = get_post_meta($trans_id, 'foodbakery_trans_address', true);
            return ($address != '') ? $address : '';
        }

        function get_order_user_phone()
        {
            $trans_id = $this->get_trans_id($this->order_id);
            $phone = get_post_meta($trans_id, 'foodbakery_trans_phone_number', true);
            return ($phone != '') ? $phone : '';
        }

        function get_restaurant_user_name()
        {
            $restaurant_user_id = get_post_meta($this->order_id, 'foodbakery_restaurant_user', true);
            $restaurant_user_info = get_userdata($restaurant_user_id);
            return $restaurant_user_info->display_name;
        }

        function get_restaurant_user_email()
        {
            $restaurant_user_id = get_post_meta($this->order_id, 'foodbakery_restaurant_user', true);
            $restaurant_user_info = get_userdata($restaurant_user_id);
            return $restaurant_user_info->user_email;
        }

        function get_restaurant_name()
        {
            $restaurant_id = get_post_meta($this->order_id, 'foodbakery_restaurant_id', true);
            return esc_html(get_the_title($restaurant_id));
        }

        function get_restaurant_phone()
        {
            $restaurant_id = get_post_meta($this->order_id, 'foodbakery_restaurant_id', true);
            return esc_html(get_post_meta($restaurant_id, 'foodbakery_restaurant_contact_phone', true));
        }

        function get_restaurant_email()
        {
            $restaurant_id = get_post_meta($this->order_id, 'foodbakery_restaurant_id', true);
            $restaurant_contact_email = get_post_meta($restaurant_id, 'foodbakery_restaurant_contact_email', true);
            if ($restaurant_contact_email == '') {
                $restaurant_publisher_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_publisher', true);
                $restaurant_contact_email = get_post_meta($restaurant_publisher_id, 'foodbakery_email_address', true);
            }
            if ($restaurant_contact_email == '') {
                $restaurant_user_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_username', true);
                $restaurant_user_info = get_userdata($restaurant_user_id);
                $restaurant_contact_email = isset($restaurant_user_info->user_email) ? $restaurant_user_info->user_email : '';
            }
            return esc_html($restaurant_contact_email);
        }

        function get_trans_id($order_id = '')
        {
            if ($order_id != '') {
                $order_trans_id = '';
                $args = array(
                    'post_type' => 'foodbakery-trans',
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                    'orderby' => 'ID',
                    'order' => 'ASC',
                    'meta_query' => array(
                        array(
                            'key' => 'foodbakery_transaction_order_id',
                            'value' => $order_id,
                            'compare' => '=',
                        )
                    ),
                );
                $order_trans = new WP_Query($args);
                if ($order_trans->have_posts()) {
                    while ($order_trans->have_posts()): $order_trans->the_post();
                        $order_trans_id = get_the_ID();
                    endwhile;
                }
                wp_reset_postdata();
                return $order_trans_id;
            }
            return '';
        }

        function get_restaurant_manager_name()
        {
            $restaurant_id = get_post_meta($this->order_id, 'foodbakery_restaurant_id', true);
            return esc_html(get_post_meta($restaurant_id, 'foodbakery_restaurant_manager_name', true));
        }

        function get_restaurant_manager_phone()
        {
            $restaurant_id = get_post_meta($this->order_id, 'foodbakery_restaurant_id', true);
            return esc_html(get_post_meta($restaurant_id, 'foodbakery_restaurant_manager_phone', true));
        }

        function get_restaurant_link()
        {
            $restaurant_id = get_post_meta($this->order_id, 'foodbakery_restaurant_id', true);
            return esc_url(get_permalink($restaurant_id));
        }

        function get_order_number()
        {
            return $this->order_id;
        }

        function get_order_link()
        {
            global $foodbakery_plugin_options;
            $publisher_dashboard = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
            if ($publisher_dashboard != '') {
                return esc_url(get_permalink($publisher_dashboard)) . '?dashboard=orders';
            } else {
                return esc_url(site_url('/dashboard/?dashboard=orders'));
            }
        }

        function get_order_status()
        {
            $order_status = get_post_meta($this->order_id, 'foodbakery_order_status', true);
            return esc_html($order_status);
        }

        function get_order_detail()
        {
            global $foodbakery_plugin_options;
            $order_id = $this->order_id;
            $order_type = get_post_meta($order_id, 'foodbakery_order_type', true);
            $order_menu_list = get_post_meta($order_id, 'menu_items_list', true);

            $menu_order_fee = get_post_meta($order_id, 'menu_order_fee', true);
            $menu_order_fee_type = get_post_meta($order_id, 'menu_order_fee_type', true);

            $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
            $foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';

            $html = '';

            if ($order_type == 'order' && is_array($order_menu_list)) {
                $order_m_total = 0;

                $html .= '<div class="user-order-holder 2">';
                $html .= apply_filters('restaurant_order_extra_details', '', $order_id);
                $html .= '<div class="user-order">
                            <ul class="categories-order">';
                foreach ($order_menu_list as $_menu_list) {
                    $title_item = isset($_menu_list['title']) ? $_menu_list['title'] : '';
                    $price_item = isset($_menu_list['price']) ? $_menu_list['price'] : '';
                    $extras_item = isset($_menu_list['extras']) ? $_menu_list['extras'] : '';

                   // $order_m_total += floatval($price_item);
                    $temp_html ='';
                    $sa_category_price =0;
                    $html .= '<li>
                                <a>' . $title_item . '</a>';
                               
                    if (is_array($extras_item) && sizeof($extras_item) > 0) {
                        $temp_html .= '<ul>';
                        foreach ($extras_item as $extra_item) {
                            $heading_extra_item = isset($extra_item['heading']) ? $extra_item['heading'] : '';
                            $title_extra_item = isset($extra_item['title']) ? $extra_item['title'] : '';
                            $price_extra_item = isset($extra_item['price']) ? $extra_item['price'] : '';
                            if ($title_extra_item != '') {
                                $temp_html .= '<li>' . $heading_extra_item . ' - ' . $title_extra_item . ' : <span class="category-price">' . foodbakery_get_currency($price_extra_item, true, '', '', true) . '</span></li>';
                            }
                            $order_m_total += floatval($price_extra_item);
                            $sa_category_price += floatval($price_extra_item);
                        }


                        $html .='<span class="category-price">' . foodbakery_get_currency($sa_category_price, true, '', '', true) . '</span>';
                        
                        $html .= $temp_html;
                        $html .= '</ul>';
                    }
                    $html .= '
                                    </li>';
                }
                $html .= '
                            </ul>';

                if ($order_m_total > 0) {
                    $html .= '<div class="price-area">';
                    $html .= '<ul>
                                    <li>' . esc_html__('Subtotal', 'foodbakery') . ' <span class="price">' . foodbakery_get_currency($order_m_total, true, '', '', true) . '</span></li>';

                    $html .= apply_filters('restaurant_order_send_to_buyer', '', $order_id);
                    $check_addon = apply_filters('foodbakery_check_delivery_tax', false);
                    if (!$check_addon) {
                        $order_m_total = foodbakery_get_currency($order_m_total, false, '', '', true);
                        if ($menu_order_fee_type == 'delivery') {
                            $html .= '<li>' . esc_html__('Delivery fee', 'foodbakery') . ' <span class="price">' . foodbakery_get_currency($menu_order_fee, true, '', '', false) . '</span></li>';
                        } else if ($menu_order_fee_type == 'pickup') {
                            $html .= '<li>' . esc_html__('Pickup fee', 'foodbakery') . ' <span class="price">' . foodbakery_get_currency($menu_order_fee, true, '', '', false) . '</span></li>';
                        }
                        if ($foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                            $html .= '<li>' . sprintf(esc_html__('VAT (%s&#37;)', 'foodbakery'), $foodbakery_payment_vat) . ' <span class="price">' . restaurant_menu_price_calc('defined', $order_m_total, $menu_order_fee, true, true, false, '', true) . '</span></li>';
                        }
                    }

                    $html .= '</ul>';
                    if ($check_addon) {
                        $html .= apply_filters('restaurant_order_calculation_for_buyer', '', $order_id, $order_m_total);
                    } else {
                        $html .= '<p class="total-price">' . esc_html__('Total', 'foodbakery') . ' <span class="price">' . restaurant_menu_price_calc('defined', $order_m_total, $menu_order_fee, true, false, false, '', true) . '</span></p>';
                    }
                    $html .= '</div>';
                }
                $html .= '
                            </div> 
                            </div>';
            }

            return $html;
        }

        public function get_order_delivery_date()
        {
            $order_delivery_date = get_post_meta($this->order_id, 'foodbakery_delivery_date', true);
            if (isset($order_delivery_date) && $order_delivery_date != '') {
                return date('M j, Y H:i A', $order_delivery_date);
            } else {
                return '&nbsp;';
            }
        }

    }

    new Foodbakery_received_order_email_template();
}
