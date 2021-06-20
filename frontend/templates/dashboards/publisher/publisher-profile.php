<?php
/**
 * Publisher Restaurants
 *
 */
if ( ! class_exists( 'Foodbakery_Publisher_Profile' ) ) {

    class Foodbakery_Publisher_Profile {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action( 'wp_ajax_foodbakery_publisher_accounts', array( $this, 'foodbakery_publisher_accounts_callback' ), 11, 1 );
            add_action( 'wp_ajax_nopriv_foodbakery_publisher_accounts', array( $this, 'foodbakery_publisher_accounts_callback' ), 11, 1 );
            add_action( 'wp_ajax_foodbakery_publisher_accounts_save', array( $this, 'foodbakery_publisher_accounts_save' ), 11, 1 );
            add_action( 'wp_ajax_nopriv_foodbakery_publisher_accounts_save', array( $this, 'foodbakery_publisher_accounts_save' ), 11, 1 );
            add_action( 'wp_ajax_publisher_change_address', array( $this, 'publisher_change_address_call_back' ), 11, 1 );
            /*
             * Change Pasword
             */
            add_action( 'wp_ajax_foodbakery_publisher_change_password', array( $this, 'publisher_change_password_callback' ), 11, 1 );
            add_action( 'wp_ajax_publisher_change_pass', array( $this, 'publisher_change_pass_callback' ) );
            /*
             *  Change Location
             */
            add_action( 'wp_ajax_foodbakery_publisher_change_locations', array( $this, 'foodbakery_publisher_change_location_callback' ), 11, 1 );
            /*
             * Team Members 
             */
        }

        /*
         * i croppic scripts
         */

        public function foodbakery_icroppic_scripts() {
            wp_enqueue_style( 'foodbakery-cropic-main-css' );
            wp_enqueue_style( 'foodbakery-cropic-css' );
            wp_enqueue_script( 'foodbakery-cripic-min-js' );
            wp_enqueue_script( 'foodbakery-cropic-js' );
        }

        /*
         * change location
         */

        public function publisher_change_address_call_back() {

            $error_string = '';
            $publisher_id = get_current_user_id();
            $company_id = get_user_meta( $publisher_id, 'foodbakery_company', true );
            $user_info = get_userdata( $publisher_id );

            $foodbakery_post_loc_country_publisher = $_POST['foodbakery_post_loc_country_publisher'];
            $foodbakery_post_loc_state_publisher = $_POST['foodbakery_post_loc_state_publisher'];
            $foodbakery_post_loc_city_publisher = $_POST['foodbakery_post_loc_city_publisher'];
            $foodbakery_post_loc_town_publisher = $_POST['foodbakery_post_loc_town_publisher'];
            $foodbakery_post_comp_address = $_POST['foodbakery_post_loc_address_publisher'];
            $foodbakery_post_loc_address = $_POST['foodbakery_post_loc_address_publisher'];
            $foodbakery_post_loc_latitude = $_POST['foodbakery_post_loc_latitude_publisher'];
            $foodbakery_post_loc_longitude = $_POST['foodbakery_post_loc_longitude_publisher'];
            $foodbakery_post_loc_radius = $_POST['foodbakery_loc_radius_publisher'];
            $foodbakery_post_loc_zoom = $_POST['foodbakery_post_loc_zoom_publisher'];
            $foodbakery_post_add_new_loc = $_POST['foodbakery_add_new_loc_publisher'];
            if ( $company_id != '' ) {
                if ( $foodbakery_post_loc_country_publisher != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_country_publisher', $foodbakery_post_loc_country_publisher );
                }
                if ( $foodbakery_post_loc_state_publisher != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_state_publisher', $foodbakery_post_loc_state_publisher );
                }
                if ( $foodbakery_post_loc_city_publisher != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_city_publisher', $foodbakery_post_loc_city_publisher );
                }
                if ( $foodbakery_post_loc_town_publisher != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_town_publisher', $foodbakery_post_loc_town_publisher );
                }

                if ( $foodbakery_post_comp_address != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_comp_address_publisher', $foodbakery_post_comp_address );
                }
                if ( $foodbakery_post_loc_address != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_address_publisher', $foodbakery_post_loc_address );
                }
                if ( $foodbakery_post_loc_latitude != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_latitude_publisher', $foodbakery_post_loc_latitude );
                }
                if ( $foodbakery_post_loc_longitude != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_longitude_publisher', $foodbakery_post_loc_longitude );
                }
                if ( $foodbakery_post_loc_zoom != '' ) {
                    update_post_meta( $company_id, 'foodbakery_post_loc_zoom_publisher', $foodbakery_post_loc_zoom );
                }
                if ( $foodbakery_post_loc_radius != '' ) {
                    update_post_meta( $company_id, 'foodbakery_loc_radius_publisher', $foodbakery_post_loc_radius );
                }
                if ( $foodbakery_post_add_new_loc != '' ) {
                    update_post_meta( $company_id, 'foodbakery_add_new_loc_publisher', $foodbakery_post_add_new_loc );
                }
            } else {
                if ( $foodbakery_post_loc_country_publisher != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_country_publisher', $foodbakery_post_loc_country_publisher );
                }
                if ( $foodbakery_post_loc_state_publisher != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_state_publisher', $foodbakery_post_loc_state_publisher );
                }
                if ( $foodbakery_post_loc_city_publisher != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_city_publisher', $foodbakery_post_loc_city_publisher );
                }
                if ( $foodbakery_post_loc_town_publisher != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_town_publisher', $foodbakery_post_loc_town_publisher );
                }

                if ( $foodbakery_post_comp_address != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_comp_address_publisher', $foodbakery_post_comp_address );
                }
                if ( $foodbakery_post_loc_address != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_address_publisher', $foodbakery_post_loc_address );
                }
                if ( $foodbakery_post_loc_latitude != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_latitude_publisher', $foodbakery_post_loc_latitude );
                }
                if ( $foodbakery_post_loc_longitude != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_longitude_publisher', $foodbakery_post_loc_longitude );
                }
                if ( $foodbakery_post_loc_zoom != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_post_loc_zoom_publisher', $foodbakery_post_loc_zoom );
                }
                if ( $foodbakery_post_loc_radius != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_loc_radius_publisher', $foodbakery_post_loc_radius );
                }
                if ( $foodbakery_post_add_new_loc != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_add_new_loc_publisher', $foodbakery_post_add_new_loc );
                }
            }
            $response_array = array(
                'type' => 'success',
                'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_updated_success_mesage' ),
            );
            echo json_encode( $response_array );
            wp_die();
        }

        public function publisher_change_pass_callback() {
            $error_string = '';
            $publisher_id = get_current_user_id();
            $user_info = get_userdata( $publisher_id );
            $publisher_current_password = $_POST['publisher_current_password'];
			$publisher_new_password = $_POST['publisher_new_password'];
            $publisher_confirm_new_password = $_POST['publisher_confirm_new_password'];
			
            if ( ! wp_check_password( $publisher_current_password, $user_info->user_pass, $publisher_id ) && $publisher_current_password != '' ) {

                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_invalid_current_pass' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }
            if ( $publisher_new_password != $publisher_confirm_new_password ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_pass_and_confirmpass_not_mached' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }
            if ( wp_check_password( $publisher_current_password, $user_info->user_pass, $publisher_id ) ) {
                if ( $publisher_new_password == $publisher_confirm_new_password ) {
                    wp_set_password( $publisher_confirm_new_password, $publisher_id );
                }
            }

            $response_array = array(
                'type' => 'success',
                'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_updated_success_mesage' ),
            );
            echo json_encode( $response_array );
            wp_die();
        }

        /*
         * Location Fields
         */

        public function foodbakery_publisher_change_location_callback() {
            $publisher_id = get_current_user_id();
            $company_id = get_user_meta( $publisher_id, 'foodbakery_company', true );
            $publisher_profile_type = '';
            if ( $company_id != '' ) {
                $publisher_profile_type = get_post_meta( $company_id, 'foodbakery_publisher_profile_type', true );
            }
            ?>
<div class="row">
    <div class="response-holder-change-address"></div>
    <?php
                    $publisher_id = get_current_user_id();
                    $company_id = get_user_meta( $publisher_id, 'foodbakery_company', true );
                    $current_user = wp_get_current_user();
                    if ( $company_id != '' ) {
                        $currrent_company = get_post( $company_id );
                        FOODBAKERY_FUNCTIONS()->frontend_location_fields_custom( $company_id, 'publisher' );
                    } else {
                        FOODBAKERY_FUNCTIONS()->frontend_location_fields_custom( '', 'publisher', $current_user );
                    }
                    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        chosen_selectionbox();
    });
    </script>
</div>
<?php
        }
        
        /*
         * Change password Form
         */

        public function publisher_change_password_callback() {
            global $wpdb, $foodbakery_plugin_options, $foodbakery_form_fields_frontend, $foodbakery_html_fields_frontend, $foodbakery_html_fields;
            $publisher_id = get_current_user_id();
            $company_id = get_user_meta( $publisher_id, 'foodbakery_company', true );
            $publisher_profile_type = '';
            if ( $company_id != '' ) {
                $publisher_profile_type = get_post_meta( $company_id, 'foodbakery_publisher_profile_type', true );
            }
			?>
<div class="row">
    <div class="response-holder-change-pass"></div>
    <form id="change_password_form" method="POST" name="change_password">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="element-title has-border">
                <h5><?php echo __( 'Change Password', 'foodbakery' ); ?></h5>

            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="field-holder">
                <label> <?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_current_password' ); ?>*</label>
                <?php
                            $foodbakery_opt_array = array(
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'id' => 'publisher_current_password',
                                    'cust_type' => 'password',
                                    'cust_name' => 'publisher_current_password',
									'classes' => 'foodbakery-dev-req-field',
                                    'force_std' => true
                                ),
                            );

                            $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                            ?>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="field-holder">
                <label> <?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_new_password' ); ?>*</label>
                <?php
                            $foodbakery_opt_array = array(
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'cust_type' => 'password',
                                    'id' => 'publisher_new_password',
                                    'cust_name' => 'publisher_new_password',
				    'classes' => 'foodbakery-dev-req-field',
                                    'force_std' => true
                                ),
                            );

                            $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                            ?>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="field-holder">
                <label> <?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_confirm_new_password' ); ?>*
                </label>
                <?php
                            $foodbakery_opt_array = array(
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'cust_type' => 'password',
                                    'id' => 'publisher_confirm_new_password',
                                    'cust_name' => 'publisher_confirm_new_password',
				    'classes' => 'foodbakery-dev-req-field',
                                    'force_std' => true
                                ),
                            );

                            $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                            ?>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="field-holder">
                <button name="button" type="button" class="btn-submit"
                    id="publisher_change_password"><?php echo __( 'Save', 'foodbakery' ); ?></button>
            </div>
        </div>
    </form>
</div>
<?php
            wp_die();
        }

        /*
         * Change password Form
         */

        public function publisher_change_password_settings_callback() {
            global $wpdb, $foodbakery_plugin_options, $foodbakery_form_fields_frontend, $foodbakery_html_fields_frontend, $foodbakery_html_fields;
            $publisher_id = get_current_user_id();
            $company_id = get_user_meta( $publisher_id, 'foodbakery_company', true );
            $publisher_profile_type = '';
            if ( $company_id != '' ) {
                $publisher_profile_type = get_post_meta( $company_id, 'foodbakery_publisher_profile_type', true );
            }
			?>
<div class="row">
    <div class="response-holder-change-pass"></div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="element-title has-border">
            <h5><?php echo __( 'Change Password', 'foodbakery' ); ?></h5>

        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="field-holder">
            <label> <?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_current_password' ); ?>*</label>
            <?php
                            $foodbakery_opt_array = array(
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'id' => 'publisher_current_password',
                                    'cust_type' => 'password',
                                    'cust_name' => 'publisher_current_password',
                                    //'classes' => 'foodbakery-dev-req-field',
                                    'force_std' => true
                                ),
                            );

                            $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                            ?>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="field-holder">
            <label> <?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_new_password' ); ?>*</label>
            <?php
                            $foodbakery_opt_array = array(
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'cust_type' => 'password',
                                    'id' => 'publisher_new_password',
                                    'cust_name' => 'publisher_new_password',
				    //'classes' => 'foodbakery-dev-req-field',
                                    'force_std' => true
                                ),
                            );

                            $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                            ?>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="field-holder">
            <label> <?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_confirm_new_password' ); ?>* </label>
            <?php
                            $foodbakery_opt_array = array(
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => '',
                                    'cust_type' => 'password',
                                    'id' => 'publisher_confirm_new_password',
                                    'cust_name' => 'publisher_confirm_new_password',
				    //'classes' => 'foodbakery-dev-req-field',
                                    'force_std' => true
                                ),
                            );

                            $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                            ?>
        </div>
    </div>
</div>
<?php
        }

        /**
         * Publisher Restaurants
         * @ filter the restaurants based on publisher id
         */
        public function foodbakery_publisher_accounts_save() {
            $error_string = '';
            $publisher_id = get_current_user_id();
            $user_info = get_userdata( $publisher_id );
            $company_id = get_user_meta( $publisher_id, 'foodbakery_company', true );
            $display_name = isset($_POST['publisher_display_name']) ? $_POST['publisher_display_name'] : '';
            $company_name = isset($_POST['foodbakery_publisher_company_name']) ? $_POST['foodbakery_publisher_company_name'] : '';
            $company_slug = isset($_POST['publisher_company_slug']) ? $_POST['publisher_company_slug'] : '';
            $publisher_email = isset($_POST['publisher_email']) ? $_POST['publisher_email'] : '';
            $publisher_profile_type = isset($_POST['publisher_profile_type']) ? $_POST['publisher_profile_type'] : '';
            $publisher_current_password = isset($_POST['publisher_current_password']) ? $_POST['publisher_current_password'] : ''; 
            $publisher_new_password =  isset($_POST['publisher_new_password']) ? $_POST['publisher_new_password'] : ''; 
            $publisher_confirm_new_password = isset($_POST['publisher_confirm_new_password']) ? $_POST['publisher_confirm_new_password'] : '';
            $publisher_profile_image =  isset($_POST['publisher_profile_image']) ? $_POST['publisher_profile_image'] : '';
            $foodbakery_user_facebook = isset($_POST['foodbakery_user_facebook']) ? $_POST['foodbakery_user_facebook'] : '';
            $foodbakery_user_twitter =  isset($_POST['foodbakery_user_twitter']) ? $_POST['foodbakery_user_twitter'] : '';
            $foodbakery_user_google_plus = isset($_POST['foodbakery_user_google_plus']) ? $_POST['foodbakery_user_google_plus'] : '';
            $foodbakery_user_phone_number = isset($_POST['foodbakery_user_phone_number']) ? $_POST['foodbakery_user_phone_number'] : '';
            $foodbakery_user_floor = isset($_POST['foodbakery_user_floor']) ? $_POST['foodbakery_user_floor'] : '';
            $foodbakery_user_beel = isset($_POST['foodbakery_user_beel']) ? $_POST['foodbakery_user_beel'] : '';




            
            $foodbakery_biography = isset($_POST['foodbakery_biography']) ? $_POST['foodbakery_biography'] : '';
            $foodbakery_user_website = isset($_POST['foodbakery_user_website']) ? $_POST['foodbakery_user_website'] : '';
            $foodbakery_email_address = $publisher_email;
            $post = get_post( $company_id );
            $company_slug_old = $post->post_name;
            
            if ( $company_slug != $company_slug_old ) {
                $comp_exists = get_page_by_path( $company_slug, '', 'publishers' );


                if ( ($comp_exists != '' ) ) {
                    $response_array = array(
                        'type' => 'error',
                        'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_company_name_exist_error' ),
                    );
                    echo json_encode( $response_array );
                    wp_die();
                }
            }

            
            if ( $display_name == '' ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_display_name_empty_error' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }

        

            if ( $foodbakery_user_phone_number == '' ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_phone_empty_error' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }




            if ( $publisher_email == '' ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_email_empty_error' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }

            if ( ! is_email( $publisher_email ) ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_email_valid_error' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }

            $exists = email_exists( $publisher_email );
            if ( $exists != $publisher_id && $exists != '' ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_email_exists_error' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }
            
            
            if ( $publisher_current_password != '' && $publisher_new_password == '' ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_new_password_empty_error' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }
            if ( $publisher_current_password != '' && $publisher_new_password != '' && $publisher_confirm_new_password != $publisher_new_password ) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_password_mismatch_error' ),
                );
                echo json_encode( $response_array );
                wp_die();
            }

            if ( wp_check_password( $publisher_current_password, $user_info->user_pass, $publisher_id ) ) {
                if ( $publisher_new_password == $publisher_confirm_new_password ) {
                    wp_set_password( $publisher_confirm_new_password, $publisher_id );
                }
            }
            if ( $publisher_profile_image != '' && ! is_numeric( $publisher_profile_image ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
				$filetype = wp_check_filetype( basename( $publisher_profile_image ), null );
				$wp_upload_dir = wp_upload_dir();
                $attachment = array(
					'guid' => $wp_upload_dir['url'] . '/' . basename( $publisher_profile_image ),
                    'post_mime_type' => $filetype['type'],
                    'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $publisher_profile_image ) ),
                    'post_content' => '',
					'post_status' => 'inherit'
                );
                
                $profile_image_absolute_path    =  $wp_upload_dir['path'] . '/' . basename( $publisher_profile_image );
                
                $profile_image_id = wp_insert_attachment( $attachment, $publisher_profile_image );
				if ($attach_data = wp_generate_attachment_metadata( $profile_image_id, $profile_image_absolute_path )) {
					wp_update_attachment_metadata( $profile_image_id, $attach_data );
				}
                $publisher_profile_image_id = $profile_image_id;
            } else {
                $publisher_profile_image_id = $publisher_profile_image;
            }
            if ( $foodbakery_user_beel != '' ) {
                update_user_meta( $publisher_id, 'foodbakery_user_beel', $foodbakery_user_beel );
            }
            if ( $foodbakery_user_floor != '' ) {
                update_user_meta( $publisher_id, 'foodbakery_user_floor', $foodbakery_user_floor );
            }
            if ( $company_id != '' ) {
                if ( $publisher_profile_type != '' ) {
                    update_post_meta( $company_id, 'foodbakery_publisher_profile_type', $publisher_profile_type );
                }
                if ( $display_name != '' ) {
                    $my_post = array(
                        'ID' => $company_id,
                        'post_title' => $display_name,
                    );
                    wp_update_post( $my_post );
                }

                if ( $company_slug != '' ) {
                    $my_post = array(
                        'ID' => $company_id,
                        'post_name' => sanitize_title( $company_slug ),
                    );
                    wp_update_post( $my_post );
                } elseif ( $display_name != '' ) {
                    $my_post = array(
                        'ID' => $company_id,
                        'post_name' => sanitize_title( $display_name ),
                    );
                    wp_update_post( $my_post );
                }
                update_post_meta( $company_id, 'foodbakery_profile_image', $publisher_profile_image_id );
				
                if ( $company_name != '' ) {
                    update_post_meta( $company_id, 'foodbakery_publisher_company_name', $company_name );
                }
                if ( $foodbakery_user_facebook != '' ) {
                    update_post_meta( $company_id, 'foodbakery_user_facebook', $foodbakery_user_facebook );
                }

                if ( $foodbakery_user_twitter != '' ) {
                    update_post_meta( $company_id, 'foodbakery_user_twitter', $foodbakery_user_twitter );
                }

                if ( $foodbakery_user_google_plus != '' ) {
                    update_post_meta( $company_id, 'foodbakery_user_google_plus', $foodbakery_user_google_plus );
                }
                if ( $foodbakery_user_phone_number != '' ) {
                    update_post_meta( $company_id, 'foodbakery_phone_number', $foodbakery_user_phone_number );
                }
                if ( $foodbakery_biography != '' ) {
                    update_post_meta( $company_id, 'foodbakery_biography', $foodbakery_biography );
                }

                if ( $foodbakery_user_website != '' ) {
                    update_post_meta( $company_id, 'foodbakery_user_website', $foodbakery_user_website );
                }

                if ( $foodbakery_email_address != '' ) {
                    update_post_meta( $company_id, 'foodbakery_email_address', $foodbakery_email_address );
                }
            } else {
                if ( $publisher_profile_type != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_publisher_profile_type', $publisher_profile_type );
                }
                update_user_meta( $publisher_id, 'foodbakery_profile_image', $publisher_profile_image_id );


                if ( $foodbakery_user_facebook != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_user_facebook', $foodbakery_user_facebook );
                }

                if ( $foodbakery_user_twitter != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_user_twitter', $foodbakery_user_twitter );
                }
                if ( $foodbakery_user_google_plus != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_user_google_plus', $foodbakery_user_google_plus );
                }
                if ( $foodbakery_user_phone_number != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_user_phone_number', $foodbakery_user_phone_number );
                }
              

                if ( $foodbakery_user_website != '' ) {
                    update_user_meta( $publisher_id, 'foodbakery_user_website', $foodbakery_user_website );
                }
                if ( $foodbakery_email_address != '' ) {
                    wp_update_user( array( 'ID' => $publisher_id, 'user_email' => $foodbakery_email_address ) );
                    update_user_meta( $publisher_id, 'foodbakery_user_email', $foodbakery_email_address );
                }
            }
            $response_array = array(
                'type' => 'success',
                'msg' => foodbakery_plugin_text_srt( 'foodbakery_publisher_updated_success_mesage' ),
            );
            echo json_encode( $response_array );
            wp_die();
        }

        public function foodbakery_publisher_accounts_callback( $publisher_id = '' ) {
            global $wpdb, $foodbakery_plugin_options, $foodbakery_form_fields_frontend, $foodbakery_html_fields_frontend, $foodbakery_form_fields;

            $rand_id = rand( 5, 99999 );
            if ( ! isset( $publisher_id ) || $publisher_id == '' ) {
                $publisher_id = get_current_user_id();
                $company_id = get_user_meta( $publisher_id, 'foodbakery_company', true );
				
                $publisher_complete_data = get_user_meta( $publisher_id );
                $foodbakery_company_email = wp_get_current_user()->user_email;
                $display_name = wp_get_current_user()->display_name;
                if ( $company_id != '' ) {
                    $display_name = get_the_title( $company_id );
                }
                $company_name = get_post_meta( $company_id, 'foodbakery_publisher_company_name', true );
				if( $company_name == '' ){
					$company_name = $display_name;
				}
                $foodbakery_company_email = get_post_meta( $company_id, 'foodbakery_email_address', true );
                $foodbakery_user_beel = get_post_meta( $company_id, 'foodbakery_user_beel', true );
                $foodbakery_user_floor = get_post_meta( $company_id, 'foodbakery_user_floor', true );
                $foodbakery_biography = '';
                $publisher_profile_type = get_user_meta( $publisher_id, 'foodbakery_publisher_profile_type', true );
                $foodbakery_profile_images_ids = isset( $foodbakery_plugin_options['foodbakery_profile_images_ids'] ) ? $foodbakery_plugin_options['foodbakery_profile_images_ids'] : '';
                $foodbakery_user_facebook = '';
                $foodbakery_user_twitter = '';
                $foodbakery_user_linkedin = '';
                $foodbakery_user_google_plus = '';
                $foodbakery_user_phone_number = '';
                $foodbakery_user_website = '';
				
                $foodbakery_profile_image = $this->publisher_get_profile_image( $publisher_id, '1' );
                if ( $company_id != '' ) {
                    $publisher_profile_type = get_post_meta( $company_id, 'foodbakery_publisher_profile_type', true );
                    $foodbakery_profile_images_ids = isset( $foodbakery_plugin_options['foodbakery_profile_images_ids'] ) ? $foodbakery_plugin_options['foodbakery_profile_images_ids'] : '';
                    $foodbakery_user_facebook = get_post_meta( $company_id, 'foodbakery_user_facebook', true );
                    $foodbakery_user_twitter = get_post_meta( $company_id, 'foodbakery_user_twitter', true );
                    $foodbakery_user_linkedin = get_post_meta( $company_id, 'foodbakery_user_linkedin', true );
                    $foodbakery_user_google_plus = get_post_meta( $company_id, 'foodbakery_user_google_plus', true );

                    $foodbakery_user_phone_number = get_post_meta( $company_id, 'foodbakery_phone_number', true );
                    $foodbakery_biography = get_post_meta( $company_id, 'foodbakery_biography', true );
                    $foodbakery_user_website = get_post_meta( $company_id, 'foodbakery_user_website', true );

                    $post = get_post( $company_id );
                    $company_slug = $post->post_name;
                }
            }

            $search_location = '1';
            echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url( 'wp-foodbakery/assets/frontend/css/main.css' ) . '" />';
            echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url( 'wp-foodbakery/assets/frontend/css/croppic.css' ) . '" />';
            echo '<script type="text/javascript" src="' . plugins_url( 'wp-foodbakery/assets/frontend/scripts/croppic.js' ) . '"></script>';
            ?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="user-profile">
            <div class="response-holder"></div>
            <div class="ewrror-holder"></div>
            <div class="element-title has-border">
                <h5><?php echo __( 'Account Settings', 'foodbakery' ); ?></h5>
            </div>
            <?PHP if ( true === Foodbakery_Member_Permissions::check_permissions( 'company_profile' ) ) { ?>

            <div class="row">
                <form id="publisher_profile" method="POST">
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                        <div class="row">
                            <?php
                                            $company_display = 'none';
                                            if ( $publisher_profile_type == 'restaurant' ) {
                                                $company_display = 'block';
                                            }
                                            if ( true === Foodbakery_Member_Permissions::check_permissions( 'company_profile' ) ) {
                                                ?>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 foodbakery-company-name"
                                style="display:<?php echo esc_html( $company_display ); ?>">
                                <div class="field-holder">
                                    <label><?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_company_name' ); ?>
                                        *</label>
                                    <?php
                                                        $foodbakery_opt_array = array(
                                                            'desc' => '',
                                                            'echo' => true,
                                                            'field_params' => array(
                                                                'std' => $company_name,
                                                                'id' => 'foodbakery_publisher_company_name',
                                                                'cust_name' => 'foodbakery_publisher_company_name',
                                                            ),
                                                        );

                                                        $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                                                        ?>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="field-holder">
                                    <label><?php _e( 'Full Name', 'foodbakery' ); ?>*</label>
                                    <?php
                                                        $foodbakery_opt_array = array(
                                                            'desc' => '',
                                                            'echo' => true,
                                                            'field_params' => array(
                                                                'std' => $display_name,
                                                                'id' => 'publisher_display_name',
                                                                'classes' => 'foodbakery-dev-req-field',
                                                                'cust_name' => 'publisher_display_name',
                                                            ),
                                                        );

                                                        $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
														
							$foodbakery_opt_array = array(
								'std' => $company_slug,
								'id' => 'publisher_company_slug',
								'cust_name' => 'publisher_company_slug',
								'return' => false,
							);
							$foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_array);
                                                        ?>
                                </div>
                            </div>


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="field-holder">
                                    <label>
                                        <?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_email_address' ); ?>
                                        *</label>
                                    <?php
                                                        $foodbakery_opt_array = array(
                                                            'desc' => '',
                                                            'echo' => true,
                                                            'field_params' => array(
                                                                'std' => $foodbakery_company_email,
                                                                'id' => 'publisher_email',
                                                                'classes' => 'foodbakery-dev-req-field',
                                                                'cust_name' => 'publisher_email',
                                                            ),
                                                        );

                                                        $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                                                        ?>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="field-holder">
                                    <label><?php echo __( 'Phone Number *', 'foodbakery' ); ?></label>
                                    <?php
                                                        $foodbakery_opt_array = array(
                                                            'desc' => '',
                                                            'echo' => true,
                                                            'field_params' => array(
                                                                'std' => $foodbakery_user_phone_number,
                                                                'classes' => 'foodbakery-dev-req-field',
                                                                'id' => 'foodbakery_user_phone_number',
                                                                'cust_name' => 'foodbakery_user_phone_number',
                                                            ),
                                                        );

                                                        $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                                                        ?>
                                </div>
                            </div>

                            <?php } ?>




                        </div>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <?php
										$default_profile_image = isset( $foodbakery_plugin_options['foodbakery_default_placeholder_image'] ) ? $foodbakery_plugin_options['foodbakery_default_placeholder_image'] : '';
                                        $profile_img = isset($foodbakery_profile_image['img']) ? $foodbakery_profile_image['img'] : '';
										if (is_numeric($profile_img)) {
											$profile_img = wp_get_attachment_url($profile_img);
										}
										if( $profile_img == '' ){
											$profile_img = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-profile-image.jpg';
										}
										if (is_numeric($default_profile_image)) {
											$default_profile_image = wp_get_attachment_url($default_profile_image);
										}
										if( $default_profile_image == '' ){
											$default_profile_image = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-profile-image.jpg';
										}
                                        ?>
                        <!-- <div class="user-profile-images">
                            <div class="current-img">
                                <div class="row mt">
                                    <div id="cropContainerModal"
                                        data-def-img="<?php echo esc_url( $default_profile_image ) ?>"
                                        data-img-type="<?php echo isset($foodbakery_profile_image['type']) && $foodbakery_profile_image['type'] == '1' ? 'default' : 'selective' ?>">
                                        <figure>
                                            <a>
                                                <?php if( $profile_img ){ ?>
                                                <img src="<?php echo esc_url($profile_img); ?>">
                                                <?php } ?>
                                            </a>
                                        </figure>
                                    </div>
                                    <?php
                                                    $hidden_foodbakery_profile_image = isset( $foodbakery_profile_image['img'] ) ? $foodbakery_profile_image['img'] : '';
                                                    if ( isset( $foodbakery_profile_image['type'] ) && $foodbakery_profile_image['type'] == '1' ) {
                                                        $hidden_foodbakery_profile_image = '';
                                                    }
                                                    $foodbakery_opt_array = array(
                                                        'desc' => '',
                                                        'echo' => true,
                                                        'field_params' => array(
                                                            'cust_type' => 'hidden',
                                                            'std' => $hidden_foodbakery_profile_image,
                                                            'id' => 'publisher_profile_image',
                                                            'cust_name' => 'publisher_profile_image',
                                                        ),
                                                    );
													$foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                                                    ?>
                                </div>
                                <span><?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_upload_profile_picture' ); ?></span>
                            </div>
                            <div class="upload-file">
                                <button for="file-1"
                                    type="button"><span><?php echo foodbakery_plugin_text_srt( 'foodbakery_publisher_upload_profile_picture_button' ); ?></span></button>
                            </div>
                            <ul class="uploaded-img">
                                <?php
                                                if (is_array($foodbakery_profile_images_ids) || is_object($foodbakery_profile_images_ids))
                                                {
                                                    foreach ( $foodbakery_profile_images_ids as $image_id ) {
                                                        if ( $image_id != '' ) {
                                                            ?>
                                <li>
                                    <figure>
                                        <img data-attachment_id="<?php echo intval( $image_id ); ?>"
                                            src="<?php echo wp_get_attachment_url( $image_id ); ?>">
                                    </figure>
                                </li>
                                <?php
                                                        }
                                                    }
                                                }
                                                ?>
                            </ul>

                        </div> -->
                    </div>



                    <?php
                        } // END company_profile CHECK
                        $wp_upload_dir = wp_upload_dir();
                        ?>
                    <script>
                    var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
                    var croppicHeaderOptions = {

                        cropData: {
                            "dummyData": 1,
                            "dummyData2": "asdas"
                        },
                        cropUrl: ajax_url,
                        customUploadButtonId: 'cropContainerHeaderButton',
                        modal: false,
                        processInline: true,
                        loaderHtml: '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
                        onBeforeImgUpload: function() {
                            console.log('onBeforeImgUpload')
                        },
                        onAfterImgUpload: function() {
                            console.log('onAfterImgUpload')
                        },
                        onImgDrag: function() {
                            console.log('onImgDrag')
                        },
                        onImgZoom: function() {
                            console.log('onImgZoom')
                        },
                        onBeforeImgCrop: function() {
                            console.log('onBeforeImgCrop')
                        },
                        onAfterImgCrop: function() {
                            console.log('onAfterImgCrop')
                        },
                        onReset: function() {
                            console.log('onReset')
                        },
                        onError: function(errormessage) {
                            console.log('onError:' + errormessage)
                        }
                    }
                    var croppic = new Croppic('croppic', croppicHeaderOptions);


                    var croppicContainerModalOptions = {
                        uploadUrl: ajax_url,
                        cropUrl: ajax_url,
                        modal: true,
                        imgEyecandyOpacity: 0.4,
                        loaderHtml: '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
                        onBeforeImgUpload: function() {
                            console.log('onBeforeImgUpload')
                        },
                        onAfterImgUpload: function() {
                            console.log('onAfterImgUpload')
                        },
                        onImgDrag: function() {
                            console.log('onImgDrag')
                        },
                        onImgZoom: function() {
                            console.log('onImgZoom')
                        },
                        onBeforeImgCrop: function() {
                            console.log('onBeforeImgCrop')
                        },
                        onAfterImgCrop: function() {
                            console.log('onAfterImgCrop')
                        },
                        onReset: function() {
                            console.log('onReset')
                        },
                        onError: function(errormessage) {
                            console.log('onError:' + errormessage)
                        }
                    }
                    var cropContainerModal = new Croppic('cropContainerModal', croppicContainerModalOptions);
                    </script>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php $this->foodbakery_publisher_change_location_callback(); ?>
                    </div>

                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6 col-xs-6">
                                <div class="field-holder">
                                    <label>Κουδούνι</label>

                                    <?php
                                    
                                    $foodbakery_opt_array = array(
                                        'desc' => '',
                                        'echo' => true,
                                        'field_params' => array(
                                            'std' => $foodbakery_user_beel,
                                            'cust_type' => 'text',
                                            'id' => 'foodbakery_user_beel',
                                            'cust_name' => 'foodbakery_user_beel',
                                            'classes' => 'foodbakery_user_beel foodbakery-dev-req-field',
                                            'placeholder' => "Bell",
                                            'force_std' => true
                                        ),
                                    );
        
                                    $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                                    
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xs-6">
                                <div class="field-holder">
                                    <label>Όροφος</label>

                                    <?php
                                    
                                    $foodbakery_opt_array = array(
                                        'desc' => '',
                                        'echo' => true,
                                        'field_params' => array(
                                            'std' => $foodbakery_user_floor,
                                            'cust_type' => 'number',
                                            'id' => 'foodbakery_user_floor',
                                            'cust_name' => 'foodbakery_user_floor',
                                            'classes' => 'foodbakery_user_floor foodbakery-dev-req-field',
                                            'force_std' => true
                                        ),
                                    );
        
                                    $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                                    
                                    ?>


                                    <!-- <input type="text" placeholder="Beel" class="form-control gllpLongitude"
                                        name="foodbakery_user_beel" value=""> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php $this->publisher_change_password_settings_callback(); ?>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <button name="button" type="button" class="btn-submit"
                                id="profile_form"><?php echo __( 'Save', 'foodbakery' ); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
            
            wp_die();
        }

        public function publisher_get_profile_image( $publisher_id, $ret_array = '' ) {
            global $foodbakery_plugin_options;
            $user_company = get_user_meta( $publisher_id, 'foodbakery_company', true );
            $foodbakery_profile_image = '';
            $blank_img = '0';
            if ( $user_company != '' ) {
                $foodbakery_profile_image = get_post_meta( $user_company, 'foodbakery_profile_image', true );
                if ( $foodbakery_profile_image != '' ) {
                    $foodbakery_profile_image = wp_get_attachment_url( $foodbakery_profile_image );
                }
            }
            if ( $foodbakery_profile_image == '' ) {
                $blank_img = '1';
                $foodbakery_profile_image = isset( $foodbakery_plugin_options['foodbakery_default_placeholder_image'] ) ? $foodbakery_plugin_options['foodbakery_default_placeholder_image'] : '';
            }
            if ( $ret_array == '1' ) {
                $img_array = array(
                    'img' => $foodbakery_profile_image,
                    'type' => $blank_img,
                );
                return $img_array;
            }
            return $foodbakery_profile_image;
        }

    }

    global $foodbakery_publisher_profile;
    $foodbakery_publisher_profile = new Foodbakery_Publisher_Profile();
}