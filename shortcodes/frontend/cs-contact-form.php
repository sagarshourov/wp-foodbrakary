<?php

/*
 * Frontend file for Contact Us short code
 */
if ( ! function_exists( 'foodbakery_var_contact_us_data' ) ) {

    function foodbakery_var_contact_us_data( $atts, $content = "" ) {
        global $post, $abc;
        $html = '';
        $page_element_size = isset( $atts['contact_form_element_size'] ) ? $atts['contact_form_element_size'] : 100;
        if ( function_exists( 'foodbakery_var_page_builder_element_sizes' ) ) {
            $html .= '<div class="' . foodbakery_var_page_builder_element_sizes( $page_element_size ) . ' ">';
        }
        $defaults = shortcode_atts( array(
            'foodbakery_var_column_size' => '',
            'foodbakery_var_contact_us_element_title' => '',
            'foodbakery_var_contact_us_element_send' => '',
            'foodbakery_var_contact_us_element_success' => '',
            'foodbakery_var_contact_us_element_error' => '',
            'foodbakery_var_text_us' => '',
            'foodbakery_var_call_us' => '',
            'foodbakery_var_address' => '',
            'foodbakery_var_form_title' => '',
            'foodbakery_var_contact_align' => '',
                ), $atts );


        extract( shortcode_atts( $defaults, $atts ) );

        wp_enqueue_script( 'foodbakery-growls' );

        $strings = new foodbakery_theme_all_strings;
        $strings->foodbakery_short_code_strings();

        if ( isset( $foodbakery_var_column_size ) && $foodbakery_var_column_size != '' ) {
            if ( function_exists( 'foodbakery_var_custom_column_class' ) ) {
                $column_class = foodbakery_var_custom_column_class( $foodbakery_var_column_size );
            }
        }

        $foodbakery_email_counter = rand( 56, 5565 );
        // Set All variables 
        $section_title = '';
        $column_class = isset( $column_class ) ? $column_class : '';
        $foodbakery_contactus_section_title = isset( $foodbakery_var_contact_us_element_title ) ? $foodbakery_var_contact_us_element_title : '';
        $foodbakery_contactus_send = isset( $foodbakery_var_contact_us_element_send ) ? $foodbakery_var_contact_us_element_send : '';
        $foodbakery_success = isset( $foodbakery_var_contact_us_element_success ) ? $foodbakery_var_contact_us_element_success : '';
        $foodbakery_error = isset( $foodbakery_var_contact_us_element_error ) ? $foodbakery_var_contact_us_element_error : '';
        $foodbakery_var_text_us = isset( $foodbakery_var_text_us ) ? $foodbakery_var_text_us : '';
        $foodbakery_var_call_us = isset( $foodbakery_var_call_us ) ? $foodbakery_var_call_us : '';
        $foodbakery_var_address = isset( $foodbakery_var_address ) ? $foodbakery_var_address : '';
        $foodbakery_var_form_title = isset( $foodbakery_var_form_title ) ? $foodbakery_var_form_title : '';
        $foodbakery_var_contact_align = isset($foodbakery_var_contact_align) ? $foodbakery_var_contact_align : '';

        // End All variables
        if ( isset( $column_class ) && $column_class <> '' ) {
            $html .= '<div class="' . esc_html( $column_class ) . '">';
        }
        $html .= '<div class="contact-info">';
        $html .= '<div class="element-title '.$foodbakery_var_contact_align.'">';
        if ( $foodbakery_contactus_section_title <> '' ) {
            $html .= '<h3>' . esc_html( $foodbakery_contactus_section_title ) . '</h3>';
        }
        $html .= '</div>';
        $html .= '<ul>';
        $html .= '<li>';
        if ( $foodbakery_var_text_us != '' ) {
            $html .= '<div class="text-holder">';
            $html .= '<span class="title">Text us:</span>';
            $html .= '<strong>' . $foodbakery_var_text_us . '</strong>';
            $html .= '</div>';
        }
        if ( $foodbakery_var_call_us != '' ) {
            $html .= '<div class="text-holder">';
            $html .= '<span class="title">Call Food Bakery</span>';
            $html .= '<strong>' . $foodbakery_var_call_us . '</strong>';
            $html .= '</div>';
        }
        $html .= '</li>';
        if ( $foodbakery_var_address != '' ) {
            $html .= '<li>';
            $html .= '<div class="text-holder">';
            $html .= '<span class="title">Address</span>';
            $html .= '<strong>' . $foodbakery_var_address . '</strong>';
            $html .= '</div>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        if ( trim( $foodbakery_success ) && trim( $foodbakery_success ) != '' ) {
            $success = $foodbakery_success;
        } else {
            $success = foodbakery_plugin_text_srt( 'foodbakery_var_contact_default_success_msg' );
        }

        if ( trim( $foodbakery_error ) && trim( $foodbakery_error ) != '' ) {
            $error = $foodbakery_error;
        } else {
            $error = foodbakery_plugin_text_srt( 'foodbakery_var_contact_default_error_msg' );
        }

        $foodbakery_inline_script = '
		function foodbakery_var_contact_frm_submit(form_id) {
			var foodbakery_mail_id = \'' . esc_js( $foodbakery_email_counter ) . '\';
			if (form_id == foodbakery_mail_id) {
				var $ = jQuery;
                                var thisObj = jQuery(".contact-ajax-button");
                                foodbakery_show_loader(".contact-ajax-button", "", "button_loader",thisObj);
				//$(\'input[type="submit"]\').attr(\'disabled\', true);
				//$("#message22").addClass(\'cs-spinner\');
                                //$("#message22").html(\'<i class="icon-spinner8 icon-spin"></i>\');
				var datastring = $("#frm' . esc_js( $foodbakery_email_counter ) . '").serialize() + "&foodbakery_contact_email=' . esc_js( $foodbakery_contactus_send ) . '&foodbakery_contact_succ_msg=' . esc_js( $success ) . '&foodbakery_contact_error_msg=' . esc_js( $error ) . '&action=foodbakery_var_contact_submit";
                                    $.ajax({
					type: \'POST\',
					url: \'' . esc_js( esc_url( admin_url( 'admin-ajax.php' ) ) ) . '\',
					data: datastring,
					dataType: "json",
					success: function (response) {
                                                console.log(response);
                                                foodbakery_show_response(response,"",thisObj);
						//foodbakery_show_response_theme(response);
                                                $("#message22").removeClass(\'cs-spinner\');

					}
				}
				);
			}
		}';
        foodbakery_inline_enqueue_script( $foodbakery_inline_script, 'foodbakery-custom-inline' );

        $html .= '<div class="contact-form">';
        if ( $foodbakery_var_form_title != '' ) {
            $html .= '<div class="element-title"><h4>' . $foodbakery_var_form_title . '</h4></div>';
        }
        $html .= '<div class="form-holder row" id="ul_frm' . absint( $foodbakery_email_counter ) . '">';
        $html .= '<form  name="frm' . absint( $foodbakery_email_counter ) . '" id="frm' . absint( $foodbakery_email_counter ) . '" action="javascript:foodbakery_var_contact_frm_submit(' . absint( $foodbakery_email_counter ) . ')" >';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
        $html .= '<div class="field-holder">';
        $html .= '<strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_first_name' ) . ' *</strong>';
        $html .= '<input name="contact_name" type="text" placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_first_name' ) . '" required>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
        $html .= '<div class="field-holder">';
        $html .= '<strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_last_name' ) . ' *</strong>';
        $html .= '<input name="contact_name_last" type="text" placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_last_name' ) . '" required>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
        $html .= '<div class="field-holder">';
        $html .= '<strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_email_address' ) . '</strong>';
        $html .= '<input name="contact_email" type="text" placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_email' ) . '" required>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
        $html .= '<div class="field-holder">';
        $html .= '<strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_phone_number' ) . '</strong>';
        $html .= '<input name="contact_number" type="text" placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_phone' ) . '">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $html .= '<div class="field-holder">';
        $html .= '<strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_text_here_message' ) . '</strong>';
        $html .= '<textarea name="contact_msg" placeholder="' . foodbakery_plugin_text_srt( 'foodbakery_var_text_here' ) . '"></textarea>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $html .= '<div class="input-btn">';
        $html .= '<div class="contact-ajax-button input-button-loader">';
        $html .= '<input type="submit" value="' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_button_text' ) . '" class="bgcolor">';
        $html .= '</div>';
        $html .= '<div id="loading_div' . absint( $foodbakery_email_counter ) . '"></div>';
        $html .= '<div id="message22" class="status status-message"></div>';
        $html .= '</div></div>';
        $html .= '</form>';
        $html .= '</div>';

        $html .= '</div>';

        if ( isset( $column_class ) && $column_class <> '' ) {
            $html .= '</div>';
        }
        if ( function_exists( 'foodbakery_var_page_builder_element_sizes' ) ) {
            $html .= '</div>';
        }
        return $html;
    }

}
if ( function_exists( 'foodbakery_var_short_code' ) ) {
    foodbakery_var_short_code( 'foodbakery_contact_form', 'foodbakery_var_contact_us_data' );
}


// Contact form submit ajax
if ( ! function_exists( 'foodbakery_var_contact_submit' ) ) {

    function foodbakery_var_contact_submit() {

        define( 'WP_USE_THEMES', false );
        global $abc;
        $strings = new foodbakery_theme_all_strings;
        $strings->foodbakery_short_code_strings();
        $strings->foodbakery_theme_strings();
        $check_box = '';
        $json = array();
        $foodbakery_contact_error_msg = '';
        $subject_name = '';
        foreach ( $_REQUEST as $keys => $values ) {
            $$keys = $values;
        }

        $foodbakery_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i><span>';
        $foodbakery_success_html = '<div class="alert alert-success"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button><p><i class="icon-warning4"></i><span>';
        
        $foodbakery_danger_html = '';
        $foodbakery_success_html    = '';
        
        $foodbakery_msg_html = '</span></p></div>';
        
        $foodbakery_msg_html = '';

        $bloginfo = get_bloginfo();
        $foodbakery_contactus_send = '';
        $subjecteEmail = "(" . $bloginfo . ") " . foodbakery_plugin_text_srt( 'foodbakery_var_contact_received' );
        if ( '' == $foodbakery_contact_email || ! preg_match( '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $foodbakery_contact_email ) ) {
            $json['type'] = "error";
            $json['msg'] = $foodbakery_danger_html . esc_html( $foodbakery_contact_error_msg ) . $foodbakery_msg_html;
        } else {
            if ( ! preg_match( '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $contact_email ) ) {
                $json['type'] = 'error';
                $json['msg'] = $foodbakery_danger_html . esc_html( foodbakery_plugin_text_srt( 'foodbakery_var_contact_valid_email' ) ) . $foodbakery_msg_html;
            } else if ( $contact_email == '' ) {
                $json['type'] = "error";
                $json['msg'] = $foodbakery_danger_html . esc_html( foodbakery_plugin_text_srt( 'foodbakery_var_contact_email_should_not_be_empty' ) ) . $foodbakery_msg_html;
            } else {
                $message = '
				<table width="100%" border="1">
				  <tr>
					<td width="100"><strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_full_name' ) . '</strong></td>
					<td>' . esc_html( $contact_name ) . '</td>
				  </tr>
				  <tr>
					<td><strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_email' ) . '</strong></td>
					<td>' . esc_html( $contact_email ) . '</td>
				  </tr>';
                if ( $contact_number != '' ) {
                    $message .= '<tr>
					<td><strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_subject' ) . '</strong></td>
					<td>' . esc_html( $contact_number ) . '</td>
				  </tr>';
                }
                if ( $contact_msg != '' ) {
                    $message .= '<tr>
					<td><strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_text_here' ) . '</strong></td>
					<td>' . esc_html( $contact_msg ) . '</td>
				  </tr>';
                }
                if ( $check_box != '' ) {
                    $message .= '
				  <tr>
					<td><strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_check_field' ) . '</strong></td>
					<td>' . esc_html( $check_box ) . '</td>
				  </tr>';
                }
                $message .= '
				  <tr>
					<td><strong>' . foodbakery_plugin_text_srt( 'foodbakery_var_contact_ip_address' ) . '</strong></td>
					<td>' . $_SERVER["REMOTE_ADDR"] . '</td>
				  </tr>
				</table>';
                $headers = "From: " . $contact_name . "\r\n";
                $headers .= "Reply-To: " . $contact_email . "\r\n";
                $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $attachments = '';
				$respose = mail( $foodbakery_contact_email, $subjecteEmail, $message, $headers );
		if ( $respose ) {
                    $json['type'] = "success";
                    $json['msg'] = $foodbakery_success_html . esc_html( $foodbakery_contact_succ_msg ) . $foodbakery_msg_html;
                } else {
                    $json['type'] = "error";
                    $json['msg'] = $foodbakery_danger_html . esc_html( $foodbakery_contact_error_msg ) . $foodbakery_msg_html;
                };
            }
        }
        echo json_encode( $json );
        die();
    }

}
//Submit Contact Us Form Hooks
add_action( 'wp_ajax_nopriv_foodbakery_var_contact_submit', 'foodbakery_var_contact_submit' );
add_action( 'wp_ajax_foodbakery_var_contact_submit', 'foodbakery_var_contact_submit' );