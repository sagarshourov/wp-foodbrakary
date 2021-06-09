<?php
/**
 * Booking Status Update Email Template
 *
 * @since 1.0
 * @package	Foodbakery
 */

if ( ! class_exists( 'Foodbakery_booking_status_updated_email_template' ) ) {

	class Foodbakery_booking_status_updated_email_template {

		public $email_template_type;
		public $email_default_template;
		public $email_template_variables;
		public $template_type;
		public $email_template_index;
		public $booking_id;
		public $is_email_sent;
		public static $is_email_sent1;
		public $template_group;

		public function __construct() {
			
			$this->email_template_type = 'Booking Status Updated';

			$this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" bbooking="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Booking Status Updated</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table bbooking="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table bbooking="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding-bottom:8px;">Hi, [BOOKING_USER_NAME]</td></tr><tr><td style="padding-bottom:8px;">Your booking is [BOOKING_STATUS] on "[RESTAURANT_NAME]" restaurant.</td></tr><tr><td>You can see booking on following link:</td></tr><tr><td>[BOOKING_LINK]</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table bbooking="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2019</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

			$this->email_template_variables = array(
				array(
					'tag' => 'RESTAURANT_NAME',
					'display_text' => 'Restaurant Name',
					'value_callback' => array( $this, 'get_restaurant_name' ),
				),
				array(
					'tag' => 'RESTAURANT_PHONE',
					'display_text' => 'Restaurant Phone',
					'value_callback' => array( $this, 'get_restaurant_phone' ),
				),
				array(
					'tag' => 'RESTAURANT_EMAIL',
					'display_text' => 'Restaurant Email',
					'value_callback' => array( $this, 'get_restaurant_email' ),
				),
				array(
					'tag' => 'RESTAURANT_MANAGER_NAME',
					'display_text' => 'Restaurant Manager Name',
					'value_callback' => array( $this, 'get_restaurant_manager_name' ),
				),
				array(
					'tag' => 'RESTAURANT_MANAGER_PHONE',
					'display_text' => 'Restaurant Manager Phone',
					'value_callback' => array( $this, 'get_restaurant_manager_phone' ),
				),
				array(
					'tag' => 'RESTAURANT_LINK',
					'display_text' => 'Restaurant Link',
					'value_callback' => array( $this, 'get_restaurant_link' ),
				),
				array(
					'tag' => 'BOOKING_USER_NAME',
					'display_text' => 'Booking User Name',
					'value_callback' => array( $this, 'get_booking_user_name' ),
				),
				array(
					'tag' => 'BOOKING_USER_EMAIL',
					'display_text' => 'Booking User Email',
					'value_callback' => array( $this, 'get_booking_user_email' ),
				),
				array(
					'tag' => 'BOOKING_NUMBER',
					'display_text' => 'Booking Number',
					'value_callback' => array( $this, 'get_booking_number' ),
				),
				array(
					'tag' => 'BOOKING_LINK',
					'display_text' => 'Booking LINK',
					'value_callback' => array( $this, 'get_booking_link' ),
				),
				array(
					'tag' => 'BOOKING_STATUS',
					'display_text' => 'Booking Status',
					'value_callback' => array( $this, 'get_booking_status' ),
				),
			);
			$this->template_group = 'Bookings';

			$this->email_template_index = 'booking-status-updated-template';
			add_action( 'init', array( $this, 'add_email_template' ), 5 );
			add_filter( 'foodbakery_email_template_settings', array( $this, 'template_settings_callback' ), 12, 1 );
			add_action( 'foodbakery_booking_status_updated_email', array( $this, 'foodbakery_booking_status_updated_email_callback' ), 10, 4 );
		}

		public function foodbakery_booking_status_updated_email_callback( $booking_id = '' ) {
			
			$this->booking_id = $booking_id;
			$template = $this->get_template();
			// checking email notification is enable/disable
			if ( isset( $template['email_notification'] ) && $template['email_notification'] == 1 ) {

				$blogname = get_option( 'blogname' );
				$admin_email = get_option( 'admin_email' );
				// getting template fields
				$subject = (isset( $template['subject'] ) && $template['subject'] != '' ) ? $template['subject'] : esc_html__( 'Update Booking Status', 'foodbakery' );
				$from = (isset( $template['from'] ) && $template['from'] != '') ? $template['from'] : esc_attr( $this->get_restaurant_name() ) . ' <' . $this->get_restaurant_email() . '>';
				$recipients = (isset( $template['recipients'] ) && $template['recipients'] != '') ? $template['recipients'] : $this->get_booking_user_email();
				$email_type = (isset( $template['email_type'] ) && $template['email_type'] != '') ? $template['email_type'] : 'html';
				
				$args = array(
					'to' => $recipients,
					'subject' => $subject,
					
					'message' => $template['email_template'],
					'email_type' => $email_type,
					'class_obj' => $this,
				);
				do_action( 'foodbakery_send_mail', $args );
				Foodbakery_booking_status_updated_email_template::$is_email_sent1 = $this->is_email_sent;
			}
		}

		public function add_email_template() {
			$email_templates = array();
			$email_templates[$this->template_group] = array();
			$email_templates[$this->template_group][$this->email_template_index] = array(
				'title' => $this->email_template_type,
				'template' => $this->email_default_template,
				'email_template_type' => $this->email_template_type,
				'is_recipients_enabled' => TRUE,
				'description' => esc_html__( 'This template is used to send email when restaurant seller update booking status.', 'foodbakery' ),
				'jh_email_type' => 'html',
			);
			do_action( 'foodbakery_load_email_templates', $email_templates );
		}

		public function template_settings_callback( $email_template_options ) {

			$email_template_options["types"][] = $this->email_template_type;

			$email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

			$email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

			return $email_template_options;
		}

		public function get_template() {
			return wp_foodbakery::get_template( $this->email_template_index, $this->email_template_variables, $this->email_default_template );
		}

		function get_booking_user_name() {
			$booking_user_id   = get_post_meta( $this->booking_id, 'foodbakery_booking_user', true );
			$booking_user_info = get_userdata( $booking_user_id );
			return $booking_user_info->display_name;
		}
		function get_booking_user_email() {
			$booking_user_id   = get_post_meta( $this->booking_id, 'foodbakery_booking_user', true );
			$booking_user_info = get_userdata( $booking_user_id );
			return $booking_user_info->user_email;
		}
		function get_restaurant_user_name() {
			$restaurant_user_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_user', true );
			$restaurant_user_info = get_userdata( $restaurant_user_id );
			return $restaurant_user_info->display_name;
		}
		function get_restaurant_user_email() {
			$restaurant_user_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_user', true );
			$restaurant_user_info = get_userdata( $restaurant_user_id );
			return $restaurant_user_info->user_email;
		}
		function get_restaurant_name() {
			$restaurant_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_id', true );
			return esc_html( get_the_title( $restaurant_id ) );
		}
		function get_restaurant_phone() {
			$restaurant_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_id', true );
			return esc_html( get_post_meta( $restaurant_id, 'foodbakery_restaurant_contact_phone', true ) );
		}
		function get_restaurant_email() {
			$restaurant_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_id', true );
			$restaurant_contact_email = get_post_meta( $restaurant_id, 'foodbakery_restaurant_contact_email', true );
			if( $restaurant_contact_email == '' ){
				$restaurant_publisher_id = get_post_meta( $restaurant_id, 'foodbakery_restaurant_publisher', true );
				$restaurant_contact_email = get_post_meta( $restaurant_publisher_id, 'foodbakery_email_address', true );
			}
			if( $restaurant_contact_email == '' ){
				$restaurant_user_id = get_post_meta( $restaurant_id, 'foodbakery_restaurant_username', true );
				$restaurant_user_info = get_userdata( $restaurant_user_id );
				$restaurant_contact_email = $restaurant_user_info->user_email;
			}
			return esc_html( $restaurant_contact_email );
		}
		function get_restaurant_manager_name() {
			$restaurant_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_id', true );
			return esc_html( get_post_meta( $restaurant_id, 'foodbakery_restaurant_manager_name', true ) );
		}
		function get_restaurant_manager_phone() {
			$restaurant_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_id', true );
			return esc_html( get_post_meta( $restaurant_id, 'foodbakery_restaurant_manager_phone', true ) );
		}
		function get_restaurant_link() {
			$restaurant_id   = get_post_meta( $this->booking_id, 'foodbakery_restaurant_id', true );
			return esc_url( get_permalink( $restaurant_id ) );
		}
		function get_booking_number() {
			return $this->booking_id;
		}
		function get_booking_link() {
			global $foodbakery_plugin_options;
			$publisher_dashboard = isset( $foodbakery_plugin_options['foodbakery_publisher_dashboard'] ) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
			if( $publisher_dashboard != '' ){
				return esc_url( get_permalink( $publisher_dashboard )).'?dashboard=bookings';
			}else{
				return esc_url( site_url( '/dashboard/?dashboard=bookings' ) );
			}
		}
		function get_booking_status() {
			$booking_status = get_post_meta( $this->booking_id, 'foodbakery_order_status', true );
			return esc_html( $booking_status );
		}
		
		
	}

	new Foodbakery_booking_status_updated_email_template();
}
