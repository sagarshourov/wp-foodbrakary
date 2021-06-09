<?php

/**
 * File Type: Restaurant Posted By
 */
if ( ! class_exists('foodbakery_posted_by') ) {

    class foodbakery_posted_by {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('foodbakery_posted_by_admin_fields', array( $this, 'foodbakery_posted_by_admin_fields_callback' ), 11);
            add_action('save_post', array( $this, 'foodbakery_insert_posted_by' ), 16);
            add_action('wp_ajax_foodbakery_restaurant_back_publishers', array( $this, 'foodbakery_restaurant_back_publishers' ));
            add_action('wp_ajax_foodbakery_load_all_publishers', array( $this, 'foodbakery_load_all_publishers_callback' ));
        }

        public function foodbakery_posted_by_admin_fields_callback() {
            global $foodbakery_html_fields, $foodbakery_form_fields, $post, $foodbakery_publisher_restaurants;

            $this_dir_pub = get_post_meta($post->ID, 'foodbakery_restaurant_publisher', true);
            $this_dir_user = get_post_meta($post->ID, 'foodbakery_restaurant_username', true);

            $foodbakery_users_list = array( '' => __('Select User', 'foodbakery') );
            if ( $this_dir_pub != '' && is_numeric($this_dir_pub) ) {
                $foodbakery_users = get_users(
                        array(
                            'role' => 'foodbakery_publisher',
                            'meta_query' => array(
                                array(
                                    'key' => 'foodbakery_company',
                                    'value' => $this_dir_pub,
                                    'compare' => '=='
                                ),
                            ),
                            'orderby' => 'nicename',
                        ));
                foreach ( $foodbakery_users as $user ) {
                    $foodbakery_users_list[$user->ID] = $user->display_name;
                }
            } elseif ( $this_dir_user != '' && is_numeric($this_dir_user) ) {
                $user_info = get_userdata($this_dir_user);
                $foodbakery_users_list[$this_dir_user] = $user->display_name;
            }

            if ( $this_dir_pub != '' && is_numeric($this_dir_pub) ) {
                $foodbakery_publishers_list = array( $this_dir_pub => get_the_title($this_dir_pub) );
            } else {
                $foodbakery_publishers_list = array( '' => esc_html__('Select Publisher', 'foodbakery') );
            }

            echo $foodbakery_html_fields->foodbakery_opening_field(array(
                'id' => 'restaurant_publisher',
                'name' => __('Select Publisher', 'foodbakery'),
                'hint_text' => '',
                    )
            );
            echo '<div class="dynamic-field select-style restaurant_publisher_holder" onclick="foodbakery_load_all_publishers(\'restaurant_publisher_holder\', \'' . $this_dir_pub . '\');">';
            $foodbakery_opt_array = array(
                'std' => '',
                'id' => 'restaurant_publisher',
                'extra_atr' => 'onchange="foodbakery_show_company_users(this.value, \'' . admin_url('admin-ajax.php') . '\', \'' . wp_foodbakery::plugin_url() . '\');"',
                'classes' => 'chosen-select-no-single',
                'options' => $foodbakery_publishers_list,
                'markup' => '<span class="select-loader"></span>',
            );
            $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
            echo '</div>';
            echo $foodbakery_html_fields->foodbakery_closing_field(array( 'desc' => '' ));

            $foodbakery_opt_array = array(
                'name' => __('Select User', 'foodbakery'),
                'desc' => '',
                'hint_text' => '',
                'col_id' => 'restaurant_user_publisher_col',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'restaurant_username',
                    'extra_atr' => '',
                    'classes' => 'chosen-select-no-single',
                    'options' => $foodbakery_users_list,
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
        }

        public function foodbakery_load_all_publishers_callback() {
            global $foodbakery_form_fields, $foodbakery_publisher_restaurants;

            $selected_publisher = foodbakery_get_input('selected_publisher', '', 'STRING');
            $foodbakery_publishers_list = array( '' => __('Select Publisher', 'foodbakery') );
            $args = array( 'posts_per_page' => '-1', 'post_type' => 'publishers', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC' );
            $cust_query = get_posts($args);
            if ( is_array($cust_query) && sizeof($cust_query) > 0 ) {
                foreach ( $cust_query as $publisher_single ) {
                    if ( isset($publisher_single->ID) ) {

                        $publisher_id = $publisher_single->ID;
                        $publisher_count = $foodbakery_publisher_restaurants->foodbakery_publisher_restaurants_count($publisher_id);
                        if ( ( $publisher_count == '' || $publisher_count <= 0 ) || $selected_publisher == $publisher_id ) {
                            $publisher_title = $publisher_single->post_title;
                            $foodbakery_publishers_list[$publisher_id] = $publisher_title;
                        }
                    }
                }
            }

            $foodbakery_opt_array = array(
                'std' => $selected_publisher,
                'id' => 'restaurant_publisher',
                'extra_atr' => 'onchange="foodbakery_show_company_users(this.value, \'' . admin_url('admin-ajax.php') . '\', \'' . wp_foodbakery::plugin_url() . '\');"',
                'classes' => 'chosen-select-no-single',
                'options' => $foodbakery_publishers_list,
                'return' => true,
                'force_std' => true,
            );
            $html = $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
            $html .= '<script type="text/javascript">
				jQuery(document).ready(function () {
					chosen_selectionbox();
				});
			</script>';
            echo json_encode(array( 'html' => $html ));
            die;
        }

        public function foodbakery_restaurant_back_publishers() {
            global $foodbakery_form_fields;

            $company = isset($_POST['company']) ? $_POST['company'] : '';
            $foodbakery_users_list = array( '' => __('Select Publisher', 'foodbakery') );
            $foodbakery_users = get_users(
                    array(
                        'role' => 'foodbakery_publisher',
                        'meta_query' => array(
                            array(
                                'key' => 'foodbakery_company',
                                'value' => $company,
                                'compare' => '=='
                            ),
                        ),
                        'orderby' => 'nicename',
                    )
            );
            foreach ( $foodbakery_users as $user ) {
                $foodbakery_users_list[$user->ID] = $user->display_name;
            }

            $foodbakery_opt_array = array(
                'std' => '',
                'id' => 'restaurant_username',
                'extra_atr' => '',
                'classes' => 'chosen-select-no-single',
                'options' => $foodbakery_users_list,
                'return' => true,
            );

            $html = $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);

            echo json_encode(array( 'html' => $html ));
            die;
        }

        public function foodbakery_insert_posted_by($post_id) {
            if ( isset($_POST['user_profile_data']) ) {
                update_post_meta($post_id, 'foodbakery_user_profile_data', $_POST['user_profile_data']);
            }
        }

    }

    global $foodbakery_posted_by;
    $foodbakery_posted_by = new foodbakery_posted_by();
}