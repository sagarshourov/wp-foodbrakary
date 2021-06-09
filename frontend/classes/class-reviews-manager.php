<?php
/**
 * This file handles Reviews functionality which register post type for that and
 * handle AJAX requests and also handle UI rendering for reviews.
 *
 * @since		1.0
 * @package		Foodbakery
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

if (!class_exists('Foodbakery_Reviews')) {

    /**
     * This class register post type for Reviews. Also register options for reviews 
     * in restaurant type and fontend UI.
     *
     * @package		Foodbakery
     * @since		1.0
     */
    class Foodbakery_Reviews {

        public static $post_type_name = 'foodbakery_reviews';
        public static $posts_per_page = 10;

        public function __construct() {
            add_action('add_meta_boxes', array(&$this, 'reviews_add_meta_boxes_callback'));
            add_action('init', array($this, 'admin_init_callback'), 10);
            add_action('init', array($this, 'register_reviews_post_type_callback'), 15);
            add_action('save_post', array($this, 'foodbakery_review_fields_data_save'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('publish_' . Foodbakery_Reviews::$post_type_name, array($this, 'foodbakery_reviews_publish_callback'), 10, 2);
        }

        static function enable_comments($post_id) {
            $show_ratings = 'on';
            if ($post_id != '') {
                $restaurant_type_id = '';
                $restaurant_type_slug = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
                if ($restaurant_type_slug != '') {
                    $restaurant_type_id = get_page_by_path($restaurant_type_slug, 'OBJECT', 'restaurant-type');
                    $restaurant_type_id = isset($restaurant_type_id->ID) ? $restaurant_type_id->ID : '';
                }
                if ($restaurant_type_id != '') {
                    $reviews_comments = get_post_meta($restaurant_type_id, 'foodbakery_enable_review_comment', true);
                }
                if (isset($reviews_comments) && $reviews_comments == 'on') {
                    $show_ratings = 'off';
                }
            }
            return $show_ratings;
        }

        public function enqueue_scripts() {
            wp_enqueue_script('ajax-pagination', wp_foodbakery::plugin_url() . 'assets/frontend/scripts/jquery.twbsPagination.min.js', array('jquery'), '1.0');
        }

        /*
         * review meta call back
         */

        public function reviews_add_meta_boxes_callback() {
            add_meta_box('foodbakery_meta_reviews', esc_html(foodbakery_plugin_text_srt('foodbakery_reviews_detail')), array($this, 'foodbakery_meta_reviews'), 'foodbakery_reviews', 'normal', 'high');
        }

        /*
         * Update Ratings on reviews publishing
         */

        public function foodbakery_reviews_publish_callback($post_ID, $post) {
            $existing_ratings = get_post_meta($post_ID, 'existing_ratings', true);
            if (!empty($existing_ratings)) {
                $user_name = get_the_title($post_ID);
                $restaurant_slug = get_post_meta($post_ID, 'post_id', true);
                $restaurant_id = get_page_by_path($restaurant_slug, OBJECT, 'restaurants');
                update_post_meta($restaurant_id->ID, 'foodbakery_ratings', $existing_ratings);
                update_post_meta($restaurant_id->ID, 'overall_ratings', $existing_ratings['overall_rating']);

                delete_post_meta($post_ID, 'existing_ratings');

                // update restaurant overall rating
                $ratings_data = array('overall_rating' => 0.0, 'count' => 0);
                $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $restaurant_id->ID);
                $restaurant_overall_rating = isset($ratings_data['overall_rating']) ? $ratings_data['overall_rating'] : '';
                if ($restaurant_overall_rating != '' && is_numeric($restaurant_overall_rating) && $restaurant_overall_rating > 0) {
                    update_post_meta($restaurant_id->ID, 'restaurant_overall_ratings', $restaurant_overall_rating);
                }

                /*
                 * Adding Notification
                 */
                $notification_array = array(
                    'type' => 'review',
                    'element_id' => $restaurant_id->ID,
                    'message' => __($user_name . ' posted a review on your restaurant  <a href="' . get_the_permalink($restaurant_id->ID) . '">' . wp_trim_words(get_the_title($restaurant_id->ID), 5) . '</a> .', 'foodbakery'),
                );
                do_action('foodbakery_add_notification', $notification_array);
            }
        }

        public function foodbakery_meta_reviews() {

            global $post, $foodbakery_html_fields, $foodbakery_form_fields;

            $overall_rating = get_post_meta($post->ID, 'overall_rating', true);
            $rating = get_post_meta($post->ID, 'ratings', true);
            $post_name = get_post_meta($post->ID, 'user_name', true);
            $post_name = isset($post_name) ? $post_name : '';
            $overall_rating = isset($overall_rating) ? $overall_rating : '';
            $post_slugg = get_post_meta($post->ID, 'post_id', true);
            /*
             * using slug get post of specific reviews
             */

            $get_post = array(
                'name' => $post_slugg,
                'post_type' => 'restaurants',
            );
            $required_post = get_posts($get_post);

            /*
             * usiing required post id get that restaurant type
             */
            $required_post_id = '';
            if (isset($required_post[0]->ID) && $required_post[0]->ID != '') {
                $required_post_id = $required_post[0]->ID;
            }
            $restaurant_type = get_post_meta($required_post_id, 'foodbakery_restaurant_type', true);
            $args_restaurants_type = array(
                'name' => $restaurant_type,
                'post_type' => 'restaurant-type',
            );
            $post_restaurants_type = get_posts($args_restaurants_type);
            /*
             * using restaurant type id get the required labels 
             */

            if (isset($post_restaurants_type[0]->ID) && $post_restaurants_type[0]->ID != '') {
                $post_restaurants_type_id = $post_restaurants_type[0]->ID;
            }

            $required_reviews_labels = get_post_meta($post_restaurants_type_id, 'foodbakery_reviews_labels', true);
            $labels_array = json_decode($required_reviews_labels);

            /*
             * using labels getting keys for label name and id 
             */
            $combile_keys = array();
            if (is_array($labels_array) && $labels_array != '') {
                foreach ($labels_array as $value) {
                    $lower_case_label = $value;
                    $final = str_replace(' ', '_', $lower_case_label);
                    $combile_keys[] = $final;
                }
            }


            $combine = array();
            if (isset($combile_keys) && !empty($combile_keys) && is_array($combile_keys)) {
                $combine = array_combine($combile_keys, $labels_array);
            }
            echo '<div class="form-elements order-list">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . esc_html__(foodbakery_plugin_text_srt('foodbakery_reviews_overall_rating')) . '</label>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					<div class="rating-holder">
						<div class="rating-star">
							<span class="rating-box" style="width: ' . ($overall_rating * 20) . '%;"></span>
						</div>
					</div>
				</div>
			</div>
			';

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt('foodbakery_reviews_overall_rating'),
                'desc' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => $overall_rating,
                    'id' => '',
                    'cust_id' => 'overall_rating',
                    'cust_name' => 'overall_rating',
                    'classes' => 'service_postion chosen-select-no-single select-medium',
                    'options' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                    ),
                    'return' => true,
                ),
            );



            /*
             * Display all fields set in reviews settings restaurant type
             */
            if (is_array($combine) && $combine != '') {
                foreach ($combine as $key => $value) {
                    $rating_val = isset($rating[$key]) ? $rating[$key] : '';
                    echo '<div class="form-elements order-list">
						<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
							<label>' . $value . '</label>
						</div>
						<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
							<div class="rating-holder">
								<div class="rating-star">
									<span class="rating-box" style="width: ' . ($rating_val * 20) . '%;"></span>
								</div>
							</div>
						</div>
					</div>
					';

                    $foodbakery_opt_array = array(
                        'name' => $value,
                        'desc' => '',
                        'echo' => true,
                        'field_params' => array(
                            'std' => isset($rating[$key]) ? $rating[$key] : '',
                            'id' => '',
                            'cust_id' => $key,
                            'cust_name' => $key,
                            'classes' => 'service_postion chosen-select-no-single select-medium',
                            'options' => array(
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                            ),
                            'return' => true,
                        ),
                    );
                }
            }
            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt('foodbakery_reviews_username'),
                'desc' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => $post_name,
                    'id' => 'user_name',
                    'cust_name' => 'user_name',
                    'extra_atr' => ' readonly="readonly"',
                    'classes' => '',
                    'return' => true,
                ),
            );
            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);


            $all_reviews = array('' => foodbakery_plugin_text_srt('foodbakery_select_review_reply_for'));

            $args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'foodbakery_parent_review',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key' => 'foodbakery_parent_review',
                        'value' => '',
                        'compare' => '=',
                    ),
                ),
            );
            $query = new WP_Query($args);

            if (!empty($query->posts)) {
                foreach ($query->posts as $post_data) {
                    $all_reviews[$post_data->ID] = $post_data->post_title;
                }
            }

            $foodbakery_parent_review = get_post_meta(get_the_ID(), 'foodbakery_parent_review', true);






            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt('foodbakery_reviews_reply_for'),
                'desc' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => isset($foodbakery_parent_review) ? $foodbakery_parent_review : '',
                    'id' => 'parent_review',
                    'name' => 'parent_review',
                    'classes' => 'chosen-select-no-single select-medium',
                    'options' => $all_reviews,
                    'return' => true,
                ),
            );
            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
        }

        /*
         * save review post
         */

        public function foodbakery_review_fields_data_save() {
            global $post;

            $combile_keys = array();
            $post_idd = isset($post->ID) ? $post->ID : '';

            $post_slugg = get_post_meta($post_idd, 'post_id', true);
            /*
             * usiing slug get post of specific reviews
             */
            $get_post = array(
                'name' => $post_slugg,
                'post_type' => 'restaurants',
            );
            $required_post = get_posts($get_post);
            /*
             * usiing required post id get that restaurant type
             */
            $required_post_id = '';
            if (isset($required_post[0]->ID) && $required_post[0]->ID != '') {
                $required_post_id = $required_post[0]->ID;
            }

            $restaurant_type = get_post_meta($required_post_id, 'foodbakery_restaurant_type', true);
            $args_restaurants_type = array(
                'name' => $restaurant_type,
                'post_type' => 'restaurant-type',
            );

            $post_restaurants_type = get_posts($args_restaurants_type);
            /*
             * using restaurant type id get the required labels 
             */

            $post_restaurants_type_id = '';
            $required_reviews_labels = '';
            if (isset($post_restaurants_type[0]->ID) && $post_restaurants_type[0]->ID != '') {
                $post_restaurants_type_id = $post_restaurants_type[0]->ID;
                $required_reviews_labels = get_post_meta($post_restaurants_type_id, 'foodbakery_reviews_labels', true);
            }
            $labels_array = json_decode($required_reviews_labels);

            /*
             * using labels getting keys for label name and id 
             */
            if (is_array($labels_array) && $labels_array != '') {
                foreach ($labels_array as $value) {
                    $lower_case_label = strtolower($value);
                    $final = str_replace(' ', '_', $lower_case_label);
                    $combile_keys[] = $final;
                }
            }
            if (!is_array($combile_keys)) {
                $combile_keys = array();
            }
            if (!is_array($labels_array)) {
                $labels_array = array();
            }

            $rating = array_combine($combile_keys, $labels_array);
            /*
             * using keys accessing values and generating array that contain key against values..
             */
            if (is_array($rating)) {
                foreach ($rating as $key => $value) {
                    $rating[$key] = isset($_POST[$key]) ? $_POST[$key] : '';
                }
            }
            if (is_admin()) {

                if (is_array($rating)) {
                    foreach ($rating as $key => $value) {
                        
                    }
                }

                update_post_meta($post_idd, 'user_name', isset($_POST['user_name']) ? $_POST['user_name'] : '' );
            }
        }

        /**
         * Init.
         */
        public function admin_init_callback() {
            add_filter('foodbakery_plugin_text_strings', array($this, 'plugin_text_strings_callback'), 1, 1);

            add_action('restaurant_type_options_sidebar_tab', array($this, 'restaurant_type_options_sidebar_tab_callback'), 10, 1);
            add_action('restaurant_type_options_tab_container', array($this, 'restaurant_type_options_tab_container_callback'), 10, 1);

            add_action('foodbakery_reviews_ui', array($this, 'reviews_ui_callback'), 100, 3);
            // action for only review form
            add_action('foodbakery_review_form_ui', array($this, 'review_form_ui_callback'), 100, 2);

            add_filter('have_user_added_review_for_this_post', array($this, 'have_user_added_review_for_this_post_callback'), 10, 3);
            add_filter('is_this_user_owner_of_this_post', array($this, 'is_this_user_owner_of_this_post_callback'), 10, 3);

            add_filter('reviews_ratings_data', array($this, 'reviews_ratings_data_callback'), 10, 2);

            // Remove "Add Review" button from restaurant page and admin menu.
            add_action('admin_head', array($this, 'disable_new_posts_capability_callback'));

            // Custom columns
            add_filter('manage_' . Foodbakery_Reviews::$post_type_name . '_posts_columns', array($this, 'custom_columns_callback'), 10, 1);
            add_action('manage_' . Foodbakery_Reviews::$post_type_name . '_posts_custom_column', array($this, 'manage_posts_custom_column_callback'), 10, 1);

            /*
             * AJAX Handlers.
             */
            // Add user review.
            add_action('wp_ajax_post_user_review', array($this, 'post_user_review_callback'));
            add_action('wp_ajax_nopriv_post_user_review', array($this, 'post_user_review_callback'));
            // Get user reviews for frontend.
            add_action('wp_ajax_get_user_reviews', array($this, 'get_user_reviews_callback'));
            add_action('wp_ajax_nopriv_get_user_reviews', array($this, 'get_user_reviews_callback'));
            // Get user reviews for frontend.
            add_action('wp_ajax_get_user_reviews_for_dashboard', array($this, 'get_user_reviews_for_dashboard_callback'));
            add_action('wp_ajax_nopriv_get_user_reviews_for_dashboard', array($this, 'get_user_reviews_for_dashboard_callback'));
            // Get user given reviews for dashboard.
            add_action('wp_ajax_foodbakery_publisher_reviews', array($this, 'dashboard_reviews_ui_callback'));
            // Get user post's reviews for dashboard.
            add_action('wp_ajax_foodbakery_publisher_my_reviews', array($this, 'dashboard_my_reviews_ui_callback'));
            // Delete user review from user dashboard.
            add_action('wp_ajax_delete_user_review', array($this, 'delete_user_review_callback'));
            // Delete user review from Admin.
            add_action('before_delete_post', array($this, 'delete_user_review_on_trash_callback'));
            // Delete Review Permanently from admin.
            add_action('post_row_actions', array($this, 'post_row_actions_callback'), 10, 2);

            add_filter('bulk_actions-edit-' . Foodbakery_Reviews::$post_type_name . '', array($this, 'bulk_actions_callback'));
        }

        /**
         * Remove Trash option from bulk dropdown
         */
        public function bulk_actions_callback($actions) {
            unset($actions['trash']);
            return $actions;
        }

        /**
         * Delete Review Permanently
         */
        public function post_row_actions_callback($actions, $post) {

            if ($post->post_type == "foodbakery_reviews") {
                unset($actions['trash']);
                unset($actions['view']);
                $post_type_object = get_post_type_object($post->post_type);
                $actions['trash'] = "<a class='submitdelete' title='" . esc_attr(esc_html__('Delete this item permanently')) . "' href='" . get_delete_post_link($post->ID, '', true) . "'>" . esc_html__('Delete') . "</a>";
            }
            return $actions;
        }

        /**
         * Register Reviews Post Type.
         */
        public function register_reviews_post_type_callback() {
            global $foodbakery_static_text;
            $labels = array(
                'name' => foodbakery_plugin_text_srt('foodbakery_reviews_name'),
                'singular_name' => foodbakery_plugin_text_srt('foodbakery_review_singular_name'),
                'menu_name' => foodbakery_plugin_text_srt('foodbakery_reviews_name'),
                'name_admin_bar' => foodbakery_plugin_text_srt('foodbakery_reviews_singular_name'),
                'add_new' => foodbakery_plugin_text_srt('foodbakery_reviews_add_review'),
                'add_new_item' => foodbakery_plugin_text_srt('foodbakery_reviews_add_new_review'),
                'new_item' => foodbakery_plugin_text_srt('foodbakery_reviews_new_review'),
                'edit_item' => foodbakery_plugin_text_srt('foodbakery_reviews_edit_review'),
                'view_item' => foodbakery_plugin_text_srt('foodbakery_reviews_view_review'),
                'all_items' => foodbakery_plugin_text_srt('foodbakery_reviews_name'),
                'search_items' => foodbakery_plugin_text_srt('foodbakery_reviews_search_reviews'),
                'not_found' => foodbakery_plugin_text_srt('foodbakery_reviews_not_found_reviews'),
                'not_found_in_trash' => foodbakery_plugin_text_srt('foodbakery_reviews_not_found_in_trash_reviews'),
            );

            $args = array(
                'labels' => $labels,
                'description' => foodbakery_plugin_text_srt('foodbakery_reviews_description'),
                'public' => true,
                'publicly_queryable' => true,
                'menu_position' => 27,
                'menu_icon' => wp_foodbakery::plugin_url() . 'assets/backend/images/reviews.png',
                'show_ui' => true,
                'query_var' => false,
                'capability_type' => 'post',
                'has_archive' => false,
                'supports' => '',
                'exclude_from_search' => true,
            );

            register_post_type(Foodbakery_Reviews::$post_type_name, $args);
        }

        /**
         * Add new columns to reviews backend restaurant.
         *
         * @param	array	$columns
         * @return	array
         */
        public function custom_columns_callback($columns) {
            unset($columns['date']);
            $columns['post_title'] = esc_html__('Post Name', 'foodbakery');
            $columns['user_name'] = esc_html__('User', 'foodbakery');
            $columns['overall_rating'] = esc_html__('Overall Rating', 'foodbakery');
            $columns['status'] = esc_html__('Status', 'foodbakery');
            $columns['review_date'] = esc_html__('Date', 'foodbakery');
            $columns['ratings_summary'] = esc_html__('Ratings Summary', 'foodbakery');
            return $columns;
        }

        /**
         * Output data for custom columns.
         *
         * @param	string	$column_name
         */
        public function manage_posts_custom_column_callback($column_name) {
            global $post;
            switch ($column_name) {
                case 'status':

                    $pub_selected = '';
                    $pend_selected = '';
                    if(get_post_status($post->ID) == 'pending'){
                        $pend_selected = 'selected';
                    }else if(get_post_status($post->ID) == 'publish'){
                        $pub_selected = 'selected';
                    }

                    $post_slug = get_post_meta($post->ID, 'post_id', true);
                    $args = array(
                        'name' => $post_slug,
                        'post_type' => 'restaurants',
                    );
                    $posts = get_posts($args);
                    if (0 < count($posts)) {
                        $restaurant_id = $posts[0]->ID;
                    } else {
                        $restaurant_id = '';
                    }

                   $html = '<select class="chosen-select-no-single select-medium review_order_change" data-restaurant_id="'.$restaurant_id.'" data-id="'.$post->ID.'" data-placeholder="Select Your Options">
                        <option value="pending" '.$pend_selected.'>Pending</option>
                        <option value="publish" '.$pub_selected.'>Publish</option>
                     </select>
                     <div class="foodbakery_loader" id="foodbakery_loader_'.$post->ID.'" style="display: none;">
                            <div class="loader-img"><i class="icon-spinner"></i></div>
                        </div>
                        <div class="foodbakery-button-loader">
                            <div class="spinner">
                                <div class="double-bounce1"></div>
                                <div class="double-bounce2"></div>
                            </div>
                        </div>';
                    echo $html;
                    break;
                case 'review_date':
                    echo get_the_date();
                    break;
                case 'post_title':
                    $post_slug = get_post_meta($post->ID, 'post_id', true);
                    $args = array(
                        'name' => $post_slug,
                        'post_type' => 'restaurants',
                    );
                    $posts = get_posts($args);
                    if (0 < count($posts)) {
                        echo '<a href="' . get_edit_post_link($posts[0]->ID) . '">  ' . $posts[0]->post_title . ' </a>';
                    } else {
                        echo esc_html__('POST DOES NOT EXIST.', 'foodbakery');
                    }
                    break;

                case 'user_name':
                    $user_name = get_post_meta($post->ID, 'user_name', true);
                    echo esc_html($user_name);
                    break;
                case 'overall_rating':

                    $overall_rting = get_post_meta($post->ID, 'overall_rating', true) . '/5';
                    $total = 0;
                    if(is_numeric($overall_rting)){
                        $total = ($overall_rting / 5) * 100;
                    }

                    echo '<div class="reviews-rating-holder"><div class="rating-star">
		    		<span class="rating-box" style="width:' . $total . '%;"></span>
			  </div></div>';
                    break;
                case 'ratings_summary':
                    $ratings = get_post_meta($post->ID, 'ratings', true);
                    if (is_array($ratings) && $ratings != '') {

                        foreach ($ratings as $key => $rating) {

                            if ($key == '') {
                                $key = '';
                            } else {
                                $key = $key . ' : ';
                            }
                            $rating_summary = '';
                            $rating_summary = ($rating / 5) * 100;
                            echo '<div class="reviews-rating-holder"><em>' . $key . '</em><div class="rating-star">
				    <span class="rating-box" style="width:' . $rating_summary . '%;"></span>
				   </div></div>';
                        }
                    }

                    break;
            }
        }

        /**
         * Disable capibility to create new review.
         */
        public function disable_new_posts_capability_callback() {
            global $post;

            // Hide link on restaurant page.
            if (get_post_type() == Foodbakery_Reviews::$post_type_name) {
                ?>
                <style type="text/css">
                    #edit-slug-box, 
                    .submitbox .preview.button,
                    .submitbox .misc-pub-visibility,
                    .submitbox .edit-timestamp {
                        display:none;
                    }
                </style>
                <?php
            }
        }

        /**
         * Get user reviews for specified post with limit and rating.
         *
         * @param	int		$id			ID of the company or post.
         * @param	int		$start		Start or Offset from which reviews will be selected.
         * @param	int		$count		Number of reviews to be selected.
         * @param	int		$order_by	Order by clause.
         * @param	bool	$is_company	Is this request to search by company or by post
         */
        public function get_user_reviews_for_post($id, $start = 0, $count = 10, $order_by = 'newest', $is_company = false, $my_reviews = false, $is_child = true) {
            global $foodbakery_publisher_profile;
            $args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'offset' => $start,
                'posts_per_page' => $count,
                'post_status' => 'publish',
            );
            /*
             * Set meta query for the query by checking if this request is to
             * select reviews by post or for any company.
             */
            if ($is_company == true) {
                if ($is_child == false) {
                    $child_fetch = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'foodbakery_parent_review',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'foodbakery_parent_review',
                            'value' => '',
                            'compare' => '=',
                        ),
                    );
                }
                $args['meta_query'] = array(
                    array(
                        'key' => 'company_id',
                        'value' => $id,
                    ),
                    $child_fetch,
                );
            } else {

                $post = get_post($id);
                $slug = '';
                if ($post == null) {
                    return array();
                }
                $slug = $post->post_name;

                if ($my_reviews == true) {

                    $restaurants_args = array(
                        'post_type' => 'restaurants',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key' => 'foodbakery_restaurant_publisher',
                                'value' => $id,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_restaurant_status',
                                'value' => 'delete',
                                'compare' => '!=',
                            ),
                        ),
                    );

                    $restaurants_query = new WP_Query($restaurants_args);
                    $slug_array = array(0);
                    $all_restaurants = $restaurants_query->get_posts();
                    if (!empty($all_restaurants)) {
                        foreach ($all_restaurants as $restaurant_key => $restaurant_data) {
                            $slug_array[] = $restaurant_data->post_name;
                        }
                    }
                    $post_meta_query = array(
                        'key' => 'post_id',
                        'value' => $slug_array,
                        'compare' => 'IN'
                    );
                } else {
                    $post_meta_query = array(
                        'key' => 'post_id',
                        'value' => $slug,
                    );
                }
                $args['meta_query'] = array(
                    'relation' => 'AND',
                    $post_meta_query,
                    /*
                     * Check if the review is replied or the parent
                     */
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'foodbakery_parent_review',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'foodbakery_parent_review',
                            'value' => '',
                            'compare' => '=',
                        ),
                    ),
                );
            }
            /*
             * Set ordery by clause for query.
             */
            if ($order_by == 'newest') {
                $args['orderby'] = 'date';
            } elseif ($order_by == 'highest') {
                $args['meta_key'] = 'overall_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
            } elseif ($order_by == 'lowest') {
                $args['meta_key'] = 'overall_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
            }

            $args = $this->foodbakery_filter_query_args($args);

            $query = new WP_Query($args);
            $reviews = $query->get_posts();

            $reviews_data = array();

            foreach ($reviews as $key => $review) {
                $review_parent_data = array();
                $data = array(
                    'id' => $review->ID,
                    'user_name' => $review->post_title,
                    'description' => $review->post_content,
                    'overall_rating' => get_post_meta($review->ID, 'overall_rating', true),
                    'username' => get_post_meta($review->ID, 'user_name', true),
                    'review_title' => $review->post_title,
                    'dated' => $review->post_date,
                );
                $user_id = get_post_meta($review->ID, 'user_id', true);
                $company_id = get_user_meta($review->ID, 'company_id', true);
                $data['img'] = '';
                if ($user_id != '' && $user_id > 0) {
                    $foodbakery_profile_image = $foodbakery_publisher_profile->publisher_get_profile_image($user_id);
                    if ($foodbakery_profile_image > 0) {
                        $data['img'] = $foodbakery_profile_image;
                    }
                }

                if ($data['img'] == '') {
                    $data['img'] = get_avatar_url(0, array('size' => 32));
                }
                $data['is_reply'] = false;
                $review_parent_data = $data;
                $review_parent_data['is_already_replied'] = false;


                /*
                 * Checking child reviews
                 */
                $review_child_data = array();
                $query_args = array(
                    'post_type' => Foodbakery_Reviews::$post_type_name,
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'foodbakery_parent_review',
                            'value' => $review->ID,
                            'compare' => '=',
                        ),
                    ),
                );

                $review_query = new WP_Query($query_args);
                $child_reviews = $review_query->get_posts();
                if (!empty($child_reviews)) {
                    foreach ($child_reviews as $child_key => $child_review) {
                        $data = array(
                            'id' => $child_review->ID,
                            'user_name' => $child_review->post_title,
                            'description' => $child_review->post_content,
                            'overall_rating' => get_post_meta($child_review->ID, 'overall_rating', true),
                            'username' => get_post_meta($child_review->ID, 'user_name', true),
                            'review_title' => $child_review->post_title,
                            'dated' => $child_review->post_date,
                        );
                        $user_id = get_post_meta($child_review->ID, 'user_id', true);
                        $company_id = get_user_meta($child_review->ID, 'company_id', true);
                        $data['img'] = '';
                        if ($user_id != '' && $user_id > 0) {
                            $foodbakery_profile_image = $foodbakery_publisher_profile->publisher_get_profile_image($user_id);
                            if ($foodbakery_profile_image > 0) {

                                $data['img'] = $foodbakery_profile_image;
                            }
                        }

                        if ($data['img'] == '') {
                            $data['img'] = get_avatar_url(0, array('size' => 32));
                        }

                        $data['is_reply'] = true;
                        $data['parent_id'] = $review_parent_data['id'];
                        $review_parent_data['is_already_replied'] = true;
                        $review_child_data = $data;
                    }
                }

                $reviews_data[] = $review_parent_data;
                $reviews_data[] = $review_child_data;
            }

            return $reviews_data;
        }

        /**
         * Get user reviews for specified post with limit and rating.
         *
         * @param	int		$id			ID of the company or post.
         * @param	bool	$is_company	Is this request to search by company or by post
         */
        public function get_user_reviews_count($id, $is_company = false, $is_child = true, $my_reviews = false) {
            $args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'post_status' => 'publish',
            );

            /*
             * Set meta query for the query by checking if this request is to
             * select reviews by post or for any company.
             */
            if ($is_company == true) {
                if ($is_child == false) {
                    $child_fetch = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'foodbakery_parent_review',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'foodbakery_parent_review',
                            'value' => '',
                            'compare' => '=',
                        ),
                    );
                }
                $args['meta_query'] = array(
                    array(
                        'key' => 'company_id',
                        'value' => $id,
                    ),
                    $child_fetch,
                );
            } else {
                $post = get_post($id);
                $slug = '';
                if ($post == null) {
                    return array();
                }
                $slug = $post->post_name;

                if ($my_reviews == true) {

                    $restaurants_args = array(
                        'post_type' => 'restaurants',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key' => 'foodbakery_restaurant_publisher',
                                'value' => $id,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_restaurant_status',
                                'value' => 'delete',
                                'compare' => '!=',
                            ),
                        ),
                    );

                    $restaurants_query = new WP_Query($restaurants_args);

                    $slug_array = array(0);
                    $all_restaurants = $restaurants_query->get_posts();

                    if (!empty($all_restaurants)) {
                        foreach ($all_restaurants as $restaurant_key => $restaurant_data) {
                            $slug_array[] = $restaurant_data->post_name;
                        }
                    }
                    $post_meta_query = array(
                        'key' => 'post_id',
                        'value' => $slug_array,
                        'compare' => 'IN'
                    );
                } else {
                    $post_meta_query = array(
                        'key' => 'post_id',
                        'value' => $slug,
                    );
                }


                $args['meta_query'] = array(
                    $post_meta_query
                );
            }
            $args = $this->foodbakery_filter_query_args($args);
            $query = new WP_Query($args);
            return $query->found_posts;
        }

        public function get_user_my_reviews_count($id, $is_company = false, $is_child = true, $my_reviews = false) {
            $args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'post_status' => 'publish',
            );

            /*
             * Set meta query for the query by checking if this request is to
             * select reviews by post or for any company.
             */
            if ($is_company == true) {
                if ($is_child == false) {
                    $child_fetch = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'foodbakery_parent_review',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'foodbakery_parent_review',
                            'value' => '',
                            'compare' => '=',
                        ),
                    );
                }
                $args['meta_query'] = array(
                    array(
                        'key' => 'company_id',
                        'value' => $id,
                    ),
                    $child_fetch,
                );
            } else {
                $post = get_post($id);
                $slug = '';
                if ($post == null) {
                    return array();
                }
                $slug = $post->post_name;

                if ($my_reviews == true) {

                    $restaurants_args = array(
                        'post_type' => 'restaurants',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key' => 'foodbakery_restaurant_publisher',
                                'value' => $id,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_restaurant_status',
                                'value' => 'delete',
                                'compare' => '!=',
                            ),
                        ),
                    );

                    $restaurants_query = new WP_Query($restaurants_args);

                    $slug_array = array(0);
                    $all_restaurants = $restaurants_query->get_posts();

                    if (!empty($all_restaurants)) {
                        foreach ($all_restaurants as $restaurant_key => $restaurant_data) {
                            $slug_array[] = $restaurant_data->post_name;
                        }
                    }
                    $post_meta_query = array(
                        'key' => 'post_id',
                        'value' => $slug_array,
                        'compare' => 'IN'
                    );
                } else {
                    $post_meta_query = array(
                        'key' => 'post_id',
                        'value' => $slug,
                    );
                }


                $args['meta_query'] = array(
                    $post_meta_query
                );
            }
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => 'foodbakery_parent_review',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => 'foodbakery_parent_review',
                    'value' => '',
                    'compare' => '=',
                ),
            );
            $args = $this->foodbakery_filter_query_args($args);
            $query = new WP_Query($args);
            return $query->found_posts;
        }

        /**
         * Handle AJAX request to fetch user reviews for a post
         */
        public function get_user_reviews_callback() {
            $return = array('success' => true, 'data' => '', 'count' => 0, 'ratings_summary_ui' => '', 'overall_ratings_ui' => '');
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
            $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
            $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 0;
            $all_data = isset($_POST['all_data']) ? $_POST['all_data'] : 0;


            $current_user = wp_get_current_user();
            if (0 < $current_user->ID) {
                $company_id = get_user_meta($current_user->ID, 'foodbakery_company', true);
            }
            $is_user_post_owner = false;

            if (0 < $company_id) {
                $is_user_post_owner = $this->is_this_user_owner_of_this_post_callback(false, $company_id, $post_id);
            }

            $is_review_response_enable = get_post_meta($post_id, 'foodbakery_transaction_restaurant_ror', true);
            $is_review_response_enable = ( isset($is_review_response_enable) && $is_review_response_enable == 'on' ) ? true : false;
            $restaurant_type_slug = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
            $args = array(
                'name' => $restaurant_type_slug,
                'post_type' => 'restaurant-type',
                'post_status' => 'publish',
                'numberposts' => 1,
            );
            $restaurant_types = get_posts($args);
            $restaurant_type_id = $restaurant_types[0]->ID;
            if (0 != $restaurant_type_id) {
                $foodbakery_review_number_of_reviews = get_post_meta($restaurant_type_id, 'foodbakery_review_number_of_reviews', true);
                Foodbakery_Reviews::$posts_per_page = ( $foodbakery_review_number_of_reviews == '' ? Foodbakery_Reviews::$posts_per_page : $foodbakery_review_number_of_reviews );
            }
            $reviews = $this->get_user_reviews_for_post($post_id, $offset, Foodbakery_Reviews::$posts_per_page, $sort_by);

            $reviews_count = count($reviews);
            foreach ($reviews as $review_data) {
                if (!isset($review_data['is_reply']) || $review_data['is_reply'] == '') {
                    $reviews_count_array[] = $review_data;
                }
            }
            //$reviews_count = count( $reviews );

            if ($all_data == 1 && $restaurant_type_id > 0) {
                $ratings_summary = array();
                $overall_ratings = array(
                    5 => 0,
                    4 => 0,
                    3 => 0,
                    2 => 0,
                    1 => 0,
                );

                $foodbakery_reviews_labels = get_post_meta($restaurant_type_id, 'foodbakery_reviews_labels', true);
                $foodbakery_reviews_labels = ( $foodbakery_reviews_labels == '' ? array() : json_decode($foodbakery_reviews_labels, true) );

                // Get existing ratings for this post.
                $existing_ratings_data = get_post_meta($post_id, 'foodbakery_ratings', true);
                if ('' != $existing_ratings_data) {
                    $reviews_count = $existing_ratings_data['reviews_count'];

                    $existing_ratings = $existing_ratings_data['ratings'];
                    foreach ($foodbakery_reviews_labels as $key => $val) {
                        if (isset($existing_ratings[$val])) {
                            $value = $existing_ratings[$val];
                        } else {
                            $value = 0;
                        }
                        $ratings_summary[] = array('label' => $val, 'value' => $value);
                    }
                    $existing_overall_ratings = $existing_ratings_data['overall_rating'];
                    foreach ($existing_overall_ratings as $key => $val) {
                        if (isset($overall_ratings[$key])) {
                            $overall_ratings[$key] = $val;
                        }
                    }
                } else {
                    foreach ($foodbakery_reviews_labels as $key => $val) {
                        $ratings_summary[] = array('label' => $val, 'value' => 0);
                    }
                }

                ob_start();
                $this->get_ratings_summary_ui($ratings_summary, $reviews_count);
                $return['ratings_summary_ui'] = ob_get_clean();

                ob_start();
                $this->get_overall_rating_ui($overall_ratings, $reviews_count); // function change containing 1 parameter post id
                $return['overall_ratings_ui'] = ob_get_clean();
            }

            ob_start();
            ?>
            <?php if (0 < count($reviews)) : ?>
                <?php foreach ($reviews as $key => $review) : ?>
                    <?php if (!empty($review)) { ?>
                        <?php $reply_class = ( isset($review['is_reply']) && $review['is_reply'] == true ) ? 'review_reply' : ''; ?>
                        <?php $review_title = ( isset($review['review_title']) && $review['review_title'] != '' ) ? ' ' . $review['review_title'] : ''; ?>
                        <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo esc_html($reply_class); ?>">
                            <div class="list-holder">
                                <div class="review-text">
                                    <div class="review-title">
                                        <h6>
                                            <?php
                                            $review_id = $review['id'];
                                            if ($reply_class) {
                                                $post_slug = get_post_meta($review_id, 'post_id', true);
                                                $args = array(
                                                    'name' => $post_slug,
                                                    'post_type' => 'restaurants',
                                                );
                                                $posts = get_posts($args);
                                                $post_title = isset($posts[0]->post_title) ? $posts[0]->post_title : '';
                                                esc_html_e("Restaurant Owner: ", 'foodbakery');
                                            } else {
                                                $user_id = get_post_meta($review_id, 'user_id', true);
                                                $user_info = get_userdata($user_id);

                                                $user_name = get_user_info_array($user_id);
                                                echo esc_html($user_name['first_name'] . ' ' . $user_name['last_name']) . ': ';
                                            }
                                            ?>
                                            <?php echo esc_html($review_title); ?>
                                        </h6>
                                        <?php
                                        $show_ratings = $this->enable_comments($post_id);

                                        if ($show_ratings == 'on') {
                                            ?>
                                            <div class="rating-holder">

                                                <?php if (isset($review['is_reply']) && $review['is_reply'] != true) { ?>
                                                    <div class="rating-star">
                                                        <span style="width: <?php echo ( $review['overall_rating'] / 5 ) * 100; ?>%;" class="rating-box"></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <em class="review-date"><?php echo date('j M Y', strtotime($review['dated'])); ?></em>
                                    <p>
                                        <?php echo esc_html($review['description']); ?>
                                    </p>
                                    <?php
                                    if ($is_review_response_enable == true && $is_user_post_owner == true) {
                                        echo force_balance_tags($this->posting_review_reply($review));
                                    }
                                    ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="list-holder"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_no_more_reviews_text'); ?></div>
                </li>
            <?php endif; ?>
            <?php
            $output = ob_get_clean();
            $return['data'] = $output;
            $return['count'] = count($reviews_count_array);
            echo json_encode($return);
            wp_die();
        }

        /*
         * Posting Review Reply by Listing's Owner
         */

        public function posting_review_reply_dashboard($review, $posts) {
            $review_id = $review['id'];
            $restaurant_slug = get_post_meta($review_id, 'post_id', true);
            $restaurant_id = $this->get_post_id_by_slug($restaurant_slug, 'restaurants');
            $restaurant_type_slug = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
            $restaurant_type_id = $this->get_post_id_by_slug($restaurant_type_slug, 'restaurant-type');
            $foodbakery_review_min_length = get_post_meta($restaurant_type_id, 'foodbakery_review_min_length', true);
            $foodbakery_review_min_length = ( $foodbakery_review_min_length == '' ? 10 : $foodbakery_review_min_length );
            $foodbakery_review_max_length = get_post_meta($restaurant_type_id, 'foodbakery_review_max_length', true);
            $foodbakery_review_max_length = ( $foodbakery_review_max_length == '' ? 200 : $foodbakery_review_max_length );
            if ((!isset($review['is_reply']) || $review['is_reply'] == false ) && (!isset($review['is_already_replied']) || $review['is_already_replied'] == false )) {
                ?>
                <a href="javascript:void(0);" data-id="<?php echo esc_html($review['id']); ?>" restaurant-id="<?php echo esc_html($restaurant_id); ?>" restaurant-type-id="<?php echo esc_html($restaurant_type_id); ?>" min-lenght="<?php echo intval($foodbakery_review_min_length); ?>" max-lenght="<?php echo intval($foodbakery_review_max_length); ?>" class="review-reply-btn dashboard-review-reply-btn"><i class="icon-reply"></i><?php echo foodbakery_plugin_text_srt('foodbakery_post_reply'); ?></a>
                <?php
            }
        }

        public function get_post_id_by_slug($slug = '', $post_type_name = '') {
            if ($post = get_page_by_path($slug, OBJECT, $post_type_name)) {
                return $post_id = $post->ID;
            } else {
                return $post_id = 0;
            }
        }

        /*
         * Posting Review Reply by Listing's Owner
         */

        public function posting_review_reply($review) {
            if ((!isset($review['is_reply']) || $review['is_reply'] == false ) && (!isset($review['is_already_replied']) || $review['is_already_replied'] == false )) {
                ?>
                <a href="javascript:void(0);" data-id="<?php echo esc_html($review['id']); ?>" class="review-reply-btn"><i class="icon-reply"></i><?php echo foodbakery_plugin_text_srt('foodbakery_post_reply'); ?></a>
            <?php }
            ?>
            <script>
                jQuery(document).ready(function () {
                    /*
                     * Posting reply for owner's restaurant
                     */
                    jQuery(".review-reply-btn").click(function () {
                        jQuery("#review_title").val('');
                        jQuery("#review_description").val('');
                        var review_ID = jQuery(this).data('id');
                        jQuery("#parent_review_id").val(review_ID);
                        jQuery(".reviwes-restaurant-holder").css("display", "none");
                        jQuery(".add-new-review-holder").css("display", "block");
                        jQuery('html, body').animate({
                            scrollTop: jQuery(".tabs-holder").offset().top - 70
                        }, 'slow');
                        return false;
                    });
                });
            </script>
            <?php
        }

        /**
         * Handle AJAX request to fetch user reviews for a company for dashboard.
         */
        public function get_user_reviews_for_dashboard_callback() {
            global $foodbakery_plugin_options;
            $success = false;
            $msg = foodbakery_plugin_text_srt('foodbakery_reviews_incomplete_data_msg');
            $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '';
            $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
            $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 0;
            $my_review = isset($_POST['my_review']) ? $_POST['my_review'] : false;
            $my_review = ( $my_review == 'yes' ) ? true : false;
            $is_child = isset($_POST['is_child']) ? $_POST['is_child'] : true;
            $is_child = ( $is_child == 'no' ) ? false : true;
            $is_company = isset($_POST['is_company']) ? $_POST['is_company'] : true;
            $is_company = ( $is_company == 'no' ) ? false : true;


            $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';
            $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
            $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
            Foodbakery_Reviews::$posts_per_page = $posts_per_page;
            $offset = 0;
            if ($posts_paged != '') {
                $offset = ( $posts_paged - 1 ) * $posts_per_page;
            }

            $reviews = $this->get_user_reviews_for_post($company_id, $offset, Foodbakery_Reviews::$posts_per_page, $sort_by, $is_company, $my_review, $is_child);

            ob_start();
            ?>
            <script>
                jQuery(document).ready(function () {
                    // Configure/customize these variables.
                    var showChar = 220;  // How many characters are shown by default
                    var ellipsestext = ".";
                    var moretext = "Show more...";
                    var lesstext = "Show less";
                    jQuery('.more').each(function () {
                        var content = jQuery(this).html();
                        if (content.length > showChar) {
                            var c = content.substr(0, showChar);
                            var h = content.substr(showChar, content.length - showChar);
                            var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
                            jQuery(this).html(html);
                            jQuery(this).find('.morecontent span').hide();
                        }

                    });
                    jQuery(".morelink").click(function () {
                        if (jQuery(this).hasClass("less")) {
                            jQuery(this).removeClass("less");
                            jQuery(this).html(moretext);
                            jQuery(this).parent().parent('.more').find('.moreellipses').slideToggle(1000);
                        } else {
                            jQuery(this).addClass("less");
                            jQuery(this).html(lesstext);
                            jQuery(this).parent().parent('.more').find('.moreellipses').slideToggle(1000);
                        }
                        jQuery(this).prev().toggle();
                        return false;
                    });
                });

            </script> 
            <?php
            if (0 < count($reviews)) :


                foreach ($reviews as $key => $review) :
                    if (!empty($review)) {
                        $post_slug = get_post_meta($reviews[$key]['id'], 'post_id', true);
                        $args = array(
                            'name' => $post_slug,
                            'post_type' => 'restaurants',
                        );
                        $posts = get_posts($args);
                        ?>

                        <?php $reply_class = ( isset($review['is_reply']) && $review['is_reply'] == true ) ? 'review_reply' : ''; ?>
                        <?php $review_title = ( isset($review['review_title']) && $review['review_title'] != '' ) ? ' ' . $review['review_title'] : ''; ?>
                        <li class="alert <?php echo esc_html($reply_class); ?>">
                            <div class="list-holder">
                                <div class="review-text">
                                    <div class="review-title">
                                        <h6>
                                            <?php if (0 < count($posts)) { ?>
                                                <a href="<?php echo esc_url($posts[0]->guid) ?>">  
                                                    <?php
                                                    $review_id = $review['id'];
                                                    if ($reply_class) {
                                                        $post_slug = get_post_meta($review_id, 'post_id', true);
                                                        $args = array(
                                                            'name' => $post_slug,
                                                            'post_type' => 'restaurants',
                                                        );
                                                        $posts = get_posts($args);
                                                        $post_title = isset($posts[0]->post_title) ? $posts[0]->post_title : '';
                                                        esc_html_e("Restaurant Owner: ", 'foodbakery');
                                                    } else {
                                                        if ($my_review == 'yes') {
                                                            $review_user_id = get_post_meta($review_id, 'user_id', true);
                                                            $user_info = get_userdata($review_user_id);
                                                            $user_name = get_user_info_array($review_user_id);
                                                            echo esc_html($user_name['first_name'] . ' ' . $user_name['last_name']) . ': ';
                                                            //echo esc_html($user_info->display_name) . ': ';
                                                        } else {
                                                            echo esc_html($posts[0]->post_title) . ': ';
                                                        }
                                                    }
                                                    ?>
                                                    <?php echo esc_html($review_title); ?> 
                                                </a>
                                                <?php
                                            } else {
                                                echo esc_html__('POST DOES NOT EXIST.', 'foodbakery');
                                            }
                                            ?> 
                                        </h6>
                                        <?PHP
                                        $show_ratings = $this->enable_comments($posts[0]->ID);
                                        if ($show_ratings == 'on') {
                                            ?>
                                            <div class="rating-holder">
                                                <?php if (!isset($review['is_reply']) || $review['is_reply'] != true) { ?>
                                                    <div class="rating-star">
                                                        <span class="rating-box" style="width: <?php echo ( $review['overall_rating'] / 5 ) * 100; ?>%;"></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?PHP } ?>
                                    </div>
                                    <em class="review-date">
                                        <?php echo human_time_diff(get_the_time('U', $review['id']), current_time('timestamp')) . ' ' . esc_html__('Ago', 'foodbakery'); ?>
                                    </em>
                                    <p  class="more" >
                                        <?php
                                        echo esc_html($review['description']);
                                        ?>
                                    </p>
                                </div>
                                <a href="#" class="delete-this-user-review close" data-dismiss="alert" data-review-id="<?php echo esc_html($review['id']); ?>"><i class="icon-close2"></i></a>
                            </div>
                        </li>
                    <?php } endforeach; ?>
            <?php else: ?>
                <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="list-holder"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_no_more_reviews_text'); ?></div>
                </li>
            <?php endif; ?>
            <?php
            $output = ob_get_clean();
            echo json_encode(array('success' => true, 'data' => $output, 'count' => count($reviews)));
            wp_die();
        }

        /**
         *  Handle AJAX request to add user review.
         */
        public function post_user_review_callback() {
            $success = false;
            $msg = foodbakery_plugin_text_srt('foodbakery_reviews_incomplete_data_msg');
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
            $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
            $restaurant_type_id = isset($_POST['restaurant_type_id']) ? $_POST['restaurant_type_id'] : 0;
            $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
            $review_title = isset($_POST['review_title']) ? $_POST['review_title'] : 0;
            $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : 0;
            $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $ratings = isset($_POST['ratings']) ? $_POST['ratings'] : '[]';
            $ratings = json_decode(stripslashes($ratings), true);
            $average_of_ratings = array_sum($ratings) / count($ratings);
            $overall_rating = round($average_of_ratings);


            $foodbakery_captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

            $restaurant_type = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
            $the_slug = $restaurant_type;
            $args = array(
                'name' => $the_slug,
                'post_type' => 'restaurant-type',
                'post_status' => 'publish',
                'posts_per_page' => 1,
            );
            $restaurant_types = get_posts($args);

            if (0 == count($restaurant_types)) {
                // Incomplete data msg.
            } else {

                $restaurant_type_id = $restaurant_types[0]->ID; //var_dump($restaurant_type_id);
                $foodbakery_enable_multiple_reviews = get_post_meta($restaurant_type_id, 'foodbakery_enable_multiple_reviews', true);
                $foodbakery_review_captcha_for_reviews = get_post_meta($restaurant_type_id, 'foodbakery_review_captcha_for_reviews', true);

                $foodbakery_review_captcha_for_reviews = ( $foodbakery_review_captcha_for_reviews == '' ? 'off' : $foodbakery_review_captcha_for_reviews );

                if ($foodbakery_review_captcha_for_reviews == 'on' && $foodbakery_captcha == '' && (!is_user_logged_in() )) {
                    $success = false;
                    $msg = foodbakery_plugin_text_srt('foodbakery_reviews_recaptcha_error_msg');
                } else {
                    $have_already_added = false;
                    $is_user_post_owner = false;

                    $child_review = false;

                    if (isset($_POST['parent_review_id']) && $_POST['parent_review_id'] != '') {
                        $child_review = true;
                    }


                    // End Check if review added by this email to this restaurent already.

                    if ($company_id > 0) {
                        $have_already_added = $this->have_user_added_review_for_this_post_callback(false, $company_id, $order_id);
                        $is_user_post_owner = $this->is_this_user_owner_of_this_post_callback(false, $company_id, $post_id);
                    } else {
                        // Check if review added by this email to this post already.
                        $have_already_added = $this->have_user_added_review_for_this_post_callback(false, $user_email, $order_id, true);
                    }

                    if ($have_already_added && $child_review == false) {
                        // Set reponse message to false.
                        $success = false;
                        $msg = foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review0_msg');
                    } else if ($is_user_post_owner && $child_review == false) {
                        // Set reponse message to true.
                        $success = false;
                        $msg = $_POST['parent_review_id'];
                    } else {

                        $post = get_post($post_id);
                        if ($post != null) {
                            $is_auto_approve_reviews = get_post_meta($restaurant_type_id, 'foodbakery_auto_approve_reviews', true);
                            $is_auto_approve_reviews = ( $is_auto_approve_reviews == '' ? 'off' : $is_auto_approve_reviews );
                            $post_status = ( $is_auto_approve_reviews == 'on' ? 'publish' : 'pending' );

                            // Gather post data.
                            $review_post = array(
                                'post_title' => $review_title,
                                'post_content' => $description,
                                'post_status' => $post_status,
                                'post_type' => Foodbakery_Reviews::$post_type_name,
                            );

                            // Insert the post into the database.
                            $review_id = wp_insert_post($review_post);
                            add_post_meta($review_id, 'restaurant_id', $post->ID, true);

                            if ($child_review != true) {
                                // Get existing ratings for this post.
                                $existing_ratings = get_post_meta($post_id, 'foodbakery_ratings', true);

                                $new_ratings = array();
                                if(is_array($existing_ratings) && is_array($ratings)){
                                   $ratings_= isset($existing_ratings['ratings']) ? $existing_ratings['ratings'] : array();
                                    $new_ratings = array_diff_key($ratings, $ratings_);

                                }


                                if ($existing_ratings == '') {
                                    $existing_ratings = array(
                                        'ratings' => array(),
                                        'overall_rating' => array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0),
                                        'reviews_count' => 0,
                                    );

                                    foreach ($ratings as $key => $val) {
                                        $existing_ratings['ratings'][$key] = 0;
                                    }
                                } else {
                                    $new_keys = array_keys($new_ratings);
                                    foreach ($new_keys as $key) {
                                        $existing_ratings['ratings'][$key] = 0;
                                    }
                                }


                                // Add new ratings to existing.
                                foreach ($existing_ratings['ratings'] as $key => $val) {
                                    if (isset($ratings[$key])) {
                                        $existing_ratings['ratings'][$key] += floatval($ratings[$key]);
                                    }
                                }

                                /*Get the all reviews have restaurant id*/
                                $args = array(
                                    'posts_per_page' => -1,
                                    'post_type' => 'foodbakery_reviews',
                                    'post_status' => 'publish',
                                    'meta_query' =>
                                        array(
                                            'relation' => 'AND',
                                            array(
                                                'key' => 'restaurant_id',
                                                'value' => $post_id,
                                                'compare' => '='
                                            ),
                                        ),
                                );

                                $query_rev = new wp_query($args);
                                $total_review = $query_rev->found_posts;
                                /* update reviews count */
                                if ($existing_ratings != '') {
                                    if (isset($existing_ratings['reviews_count'])) {
                                        $existing_ratings['reviews_count'] = $total_review;
                                    }
                                }

                                $existing_ratings['overall_rating'][$overall_rating] ++;

                                if ($post_status == 'pending') {
                                    update_post_meta($review_id, 'existing_ratings', $existing_ratings);
                                } else {

                                    // Do not updated ratings if its a reply from owner.
                                    update_post_meta($post_id, 'foodbakery_ratings', $existing_ratings);
                                    update_post_meta($post_id, 'overall_ratings', $overall_rating);
                                }
                            }

                            // update restaurant overall rating
                            $ratings_data = array('overall_rating' => 0.0, 'count' => 0);
                            $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $post_id);
                            $restaurant_overall_rating = isset($ratings_data['overall_rating']) ? $ratings_data['overall_rating'] : '';
                            if ($restaurant_overall_rating != '' && is_numeric($restaurant_overall_rating) && $restaurant_overall_rating > 0) {
                                update_post_meta($post_id, 'restaurant_overall_ratings', $restaurant_overall_rating);
                            }

                            // Keep slug of the post for which reviews is added keep this in review meta.
                            add_post_meta($review_id, 'post_id', $post->post_name, true);
                            add_post_meta($review_id, 'order_id', $order_id, true);


                            // Add Overall Rating to post meta.
                            add_post_meta($review_id, 'overall_rating', $overall_rating, true);

                            // Add Ratings to post meta.
                            add_post_meta($review_id, 'ratings', $ratings, true);

                            // Add user id to post meta.
                            add_post_meta($review_id, 'user_id', $user_id, true);

                            // Add company id to post meta.
                            add_post_meta($review_id, 'company_id', $company_id, true);

                            // Add user name to post meta.
                            add_post_meta($review_id, 'user_name', $user_name, true);

                            // Add user email to post meta.
                            add_post_meta($review_id, 'user_email', $user_email, true);


                            //Add Review Parent if any
                            if (isset($_POST['parent_review_id']) && $_POST['parent_review_id'] != '') {
                                add_post_meta($review_id, 'foodbakery_parent_review', $_POST['parent_review_id'], true);
                            }

                            if (!is_wp_error($review_id)) {
                                $user_data = wp_get_current_user();
                                if ($user_data->ID < 1) {
                                    $user_data = new stdClass();
                                    $user_data->ID = 0;
                                    $user_data->display_name = $user_name;
                                    $user_data->user_email = $user_email;
                                }
                                if ($child_review == true) {
                                    do_action('foodbakery_review_reply_added_email', $user_data, $review_id);
                                } else {
                                    do_action('foodbakery_review_added_email', $user_data, $review_id);
                                }
                            }

                            // Set reponse message to true.
                            $success = true;
                            $msg = foodbakery_plugin_text_srt('foodbakery_reviews_success_msg');
                            $publisher_name = get_the_title($company_id);

                            if ($post_status != 'pending') {
                                if ($child_review != true) {
                                    /*
                                     * Adding Notification
                                     */
                                    $notification_array = array(
                                        'type' => 'review',
                                        'element_id' => $post_id,
                                        'message' => __($user_name . ' posted a review on your restaurant  <a href="' . get_the_permalink($post_id) . '">' . wp_trim_words(get_the_title($post_id), 5) . '</a> .', 'foodbakery'),
                                    );
                                    do_action('foodbakery_add_notification', $notification_array);
                                }
                            }
                        }
                    }
                }
            }
            echo json_encode(array('success' => $success, 'msg' => $msg));
            wp_die();
        }

        /**
         * Handle AJAX request and render Dashboard My Reviews Tab Container.
         */
        public function dashboard_my_reviews_ui_callback() {
            global $foodbakery_plugin_options;
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
            $user_id = get_current_user_id();
            if ($user_id == 0) {
                echo json_encode(array('success' => false, 'msg' => 'Invalid user.'));
                wp_die();
            }

            $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';
            $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
            $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
            Foodbakery_Reviews::$posts_per_page = $posts_per_page;
            $start = 0;
            if ($posts_paged != '') {
                $start = ( $posts_paged - 1 ) * $posts_per_page;
            }

            $company_id = get_user_meta($user_id, 'foodbakery_company', true);
            $company_id = ( $company_id != '' ? $company_id : 0 );

            $user_email = get_post_meta($company_id, 'foodbakery_email_address', true);
            if (!isset($user_email) || $user_email == '') {
                $user_email = $current_user->user_email;
            }
            $publisher_display_name = get_the_title($company_id);

            $date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';
            $sort_by = (isset($_POST['sort_by']) && $_POST['sort_by'] != 'undefined' ) ? $_POST['sort_by'] : 'newest';

            $reviews_count = $this->get_user_my_reviews_count($company_id, false, true, true);
            $reviews = $this->get_user_reviews_for_post($company_id, $start, Foodbakery_Reviews::$posts_per_page, $sort_by, false, true);
            ob_start();
            ?>
            <div class="dashbard-user-reviews-list">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="element-title has-border reviews-header right-filters-row">
                            <h5>
                                <span><?php esc_html_e('Reviews Given', 'foodbakery'); ?></span>
                                <?php if (0 < count($reviews)) : ?>
                                    <span class="element-slogan"><?php echo sprintf('(' . foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_stats') . ')', $reviews_count); ?></span>
                                <?php endif; ?>
                            </h5>
                            <div class="right-filters row pull-right">
                                <?php if (0 < count($reviews)) : ?>
                                    <?php
                                    $sort_by_options = array(
                                        'newest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_newest_reviews_option'),
                                        'highest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_highest_rating_option'),
                                        'lowest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_lowest_rating_option'),
                                    );
                                    ?>
                                    <div class="col-lg-6 col-md-6 col-xs-6">
                                        <div class="sort-by">
                                            <ul class="reviews-sortby">
                                                <li> 
                                                    <small><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_label'); ?>:</small>
                                                    <span> 
                                                        <strong class="active-sort">
                                                            <?php esc_html_e($sort_by_options[$sort_by]); ?>
                                                        </strong>
                                                    </span>
                                                    <div class="reviews-sort-dropdown">
                                                        <form>
                                                            <div class="input-reviews">
                                                                <?php
                                                                $i = 1;
                                                                foreach ($sort_by_options as $key => $sort_by_option) {
                                                                    ?>
                                                                    <div class="radio-field">
                                                                        <input name="review" id="check-<?php echo intval($i); ?>" type="radio" value="<?php echo esc_html($key); ?>" <?php echo ( $key == $sort_by ) ? 'checked="checked"' : ''; ?>>
                                                                        <label for="check-<?php echo intval($i); ?>"><?php esc_html_e($sort_by_options[$key]); ?></label>
                                                                    </div>
                                                                    <?php
                                                                    $i ++;
                                                                }
                                                                ?>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php $date_range = isset($_REQUEST['date_range']) ? $_REQUEST['date_range'] : ''; ?>
                                <?php if ((0 <= count($reviews) && $date_range != '') || (0 < count($reviews) && $date_range == '' )) { ?>
                                    <div class="col-lg-6 col-md-6 col-xs-6 pull-right">
                                        <div class="input-field">
                                            <i class="icon-angle-down"></i>
                                            <input type="text" id="daterange" value="<?php echo ($date_range != 'undefined' ) ? str_replace(',', ' - ', $date_range) : ''; ?>" placeholder="<?php echo esc_html__('Select Date Range', 'foodbakery'); ?>"/>
                                            <input type="hidden" name="date_range" id="date_range" value="<?php echo ($date_range != 'undefined' ) ? $date_range : ''; ?>" />
                                            <script type="text/javascript">
                                                jQuery('#daterange').daterangepicker({
                                                    autoUpdateInput: false,
                                                    opens: 'left',
                                                    locale: {
                                                        format: 'DD/MM/YYYY'
                                                    }
                                                },
                                                        function (start, end) {
                                                            var date_range = start.format('DD/MM/YYYY') + ',' + end.format('DD/MM/YYYY');
                                                            jQuery('#date_range').val(date_range);
                                                            var actionString = "foodbakery_publisher_my_reviews";
                                                            var pageNum = 1;
                                                            foodbakery_show_loader('.loader-holder');
                                                            var filter_parameters = get_filter_parameters();

                                                            var sort_by = jQuery(".reviews-sort-dropdown .radio-field.active input[name='review']").val();
                                                            var sort_filter_var = '';
                                                            if (typeof sort_by != 'undefined' && sort_by !== '') {
                                                                sort_filter_var = '&sort_by=' + sort_by;
                                                            }

                                                            if (typeof (ajaxRequest) != 'undefined') {
                                                                ajaxRequest.abort();
                                                            }
                                                            ajaxRequest = jQuery.ajax({
                                                                type: "POST",
                                                                url: foodbakery_globals.ajax_url,
                                                                data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters + sort_filter_var,
                                                                success: function (response) {
                                                                    foodbakery_hide_loader();
                                                                    jQuery('.user-holder').html(response);

                                                                }
                                                            });
                                                        });
                                                jQuery('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                                                    jQuery('#daterange').val('');
                                                    jQuery('#date_range').val('');
                                                    var actionString = "foodbakery_publisher_my_reviews";
                                                    var pageNum = 1;
                                                    foodbakery_show_loader('.loader-holder');
                                                    var filter_parameters = get_filter_parameters();

                                                    var sort_by = jQuery(".reviews-sort-dropdown .radio-field.active input[name='review']").val();
                                                    var sort_filter_var = '';
                                                    if (typeof sort_by != 'undefined' && sort_by !== '') {
                                                        sort_filter_var = '&sort_by=' + sort_by;
                                                    }

                                                    if (typeof (ajaxRequest) != 'undefined') {
                                                        ajaxRequest.abort();
                                                    }
                                                    ajaxRequest = jQuery.ajax({
                                                        type: "POST",
                                                        url: foodbakery_globals.ajax_url,
                                                        data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters + sort_filter_var,
                                                        success: function (response) {
                                                            foodbakery_hide_loader();
                                                            jQuery('.user-holder').html(response);

                                                        }
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="user-reviews-list">
                            <?php if (0 < count($reviews)) : ?>
                                <script>
                                    jQuery(document).ready(function () {

                                        jQuery('.reviews-sortby > li').on('click', function () {
                                            jQuery('.reviews-sortby > li').toggleClass('reviews-sortby-active');
                                            jQuery('.reviews-sortby > li').siblings();
                                            jQuery('.reviews-sortby > li').siblings().removeClass('reviews-sortby-active');
                                        });
                                        jQuery('.input-reviews > .radio-field label').on('click', function () {
                                            jQuery(this).parent().toggleClass('active');
                                            jQuery(this).parent().siblings();
                                            jQuery(this).parent().siblings().removeClass('active');
                                            /*replace inner Html*/
                                            var radio_field_active = jQuery(this).html();
                                            jQuery(".active-sort").html(radio_field_active);
                                            jQuery('.reviews-sortby > li').removeClass('reviews-sortby-active');
                                        });
                                        // Configure/customize these variables.
                                        var showChar = 220;  // How many characters are shown by default
                                        var ellipsestext = ".";
                                        var moretext = "Show more...";
                                        var lesstext = "Show less";
                                        jQuery('.more').each(function () {
                                            var content = jQuery(this).html();
                                            if (content.length > showChar) {
                                                var c = content.substr(0, showChar);
                                                var h = content.substr(showChar, content.length - showChar);
                                                var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
                                                jQuery(this).html(html);
                                                jQuery(this).find('.morecontent span').hide();
                                            }

                                        });
                                        jQuery(".morelink").click(function () {
                                            if (jQuery(this).hasClass("less")) {
                                                jQuery(this).removeClass("less");
                                                jQuery(this).html(moretext);
                                                jQuery(this).parent().parent('.more').find('.moreellipses').slideToggle(1000);
                                            } else {
                                                jQuery(this).addClass("less");
                                                jQuery(this).html(lesstext);
                                                jQuery(this).parent().parent().find('.moreellipses').slideToggle(1000);
                                            }

                                            jQuery(this).prev().toggle();
                                            return false;
                                        });
                                    });

                                </script> 
                                <div class="review-listing">
                                    <ul>
                                        <?php foreach ($reviews as $key => $review) : ?>
                                            <?php if (!empty($review)) { ?>
                                                <?php
                                                $post_slug = get_post_meta($reviews[$key]['id'], 'post_id', true);
                                                $args = array(
                                                    'name' => $post_slug,
                                                    'post_type' => 'restaurants',
                                                );
                                                $posts = get_posts($args);
                                                ?>
                                                <?php $review_title = ( isset($review['review_title']) && $review['review_title'] != '' ) ? ' ' . $review['review_title'] : ''; ?>
                                                <?php
                                                $reply_class = ( isset($review['is_reply']) && $review['is_reply'] == true ) ? 'review_reply' : '';

                                                $is_review_response_enable = get_post_meta($posts[0]->ID, 'foodbakery_transaction_restaurant_ror', true);
                                                $is_review_response_enable = ( isset($is_review_response_enable) && $is_review_response_enable == 'on' ) ? true : false;
                                                ?>
                                                <li class="alert <?php echo esc_html($reply_class); ?>">
                                                    <div class="list-holder">
                                                        <div class="review-text">
                                                            <div class="review-title">
                                                                <h6>
                                                                    <?php
                                                                    if (0 < count($posts)) {
                                                                        $review_user_id = get_post_meta($reviews[$key]['id'], 'user_id', true);
                                                                        $user_info = get_userdata($review_user_id);
                                                                        $user_name = '';

                                                                        if (!empty($user_info)) {
                                                                            $user_name = $user_info->display_name . ':1 ';
                                                                        }
                                                                        ?>
                                                                        <a href="<?php echo esc_url($posts[0]->guid) ?>" >
                                                                            <?php
                                                                            $review_id = $reviews[$key]['id'];
                                                                            if ($reply_class) {
                                                                                $post_slug = get_post_meta($review_id, 'post_id', true);
                                                                                $args = array(
                                                                                    'name' => $post_slug,
                                                                                    'post_type' => 'restaurants',
                                                                                );
                                                                                $posts = get_posts($args);
                                                                                $post_title = isset($posts[0]->post_title) ? $posts[0]->post_title : '';
                                                                                esc_html_e("Restaurant Owner: ", 'foodbakery');
                                                                            } else {
                                                                                $review_user_id = get_post_meta($review_id, 'user_id', true);
                                                                                $user_info = get_userdata($review_user_id);
                                                                                $user_name = get_user_info_array($review_user_id);
                                                                                if (!empty($user_name['first_name'])) {
                                                                                    echo esc_html($user_name['first_name'] . ' ' . $user_name['last_name']) . ': ';
                                                                                }
                                                                            }
                                                                            ?>
                                                                            <?php echo esc_html($review_title); ?> 
                                                                        </a>
                                                                        <?php
                                                                    } else {
                                                                        echo esc_html__('POST DOES NOT EXIST.', 'foodbakery');
                                                                    }
                                                                    ?> 
                                                                </h6>

                                                                <?php
                                                                $show_ratings = $this->enable_comments($posts[0]->ID);
                                                                if ($show_ratings == 'on') {
                                                                    ?>
                                                                    <div class="rating-holder">

                                                                        <?php if (!isset($review['is_reply']) || $review['is_reply'] != true) { ?>
                                                                            <div class="rating-star">
                                                                                <span class="rating-box" style="width: <?php echo ( $review['overall_rating'] / 5 ) * 100; ?>%;"></span>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <em class="review-date"><?php echo human_time_diff(get_the_time('U', $review['id']), current_time('timestamp')) . ' ' . esc_html__('Ago', 'foodbakery'); ?></em>
                                                            <p  class="more" >
                                                                <?php
                                                                echo esc_html($review['description']);
                                                                ?>
                                                            </p>
                                                            <?php
                                                            if ($is_review_response_enable == true) {
                                                                echo force_balance_tags($this->posting_review_reply_dashboard($review, $posts));
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php if (isset($review['is_reply']) && $review['is_reply'] == true) { ?>
                                                            <a href="#" class="delete-this-user-review close" data-dismiss="alert" data-review-id="<?php echo esc_html($review['id']); ?>"><i class="icon-close2"></i></a>
                                                        <?php } ?>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <div class="not-found">
                                    <i class="icon-error"></i>
                                    <p><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_no_reviews_found_text'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php
                $total_pages = 1;
                if ($reviews_count > 0 && $posts_per_page > 0 && $reviews_count > $posts_per_page) {
                    $total_pages = ceil($reviews_count / $posts_per_page);
                    $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                    $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                    $this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'reviews'), $foodbakery_dashboard_link) : '';
                    foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'my_reviews');
                }

                $foodbakery_review_max_length = '200';
                $foodbakery_review_min_length = '10';
                ?>
            </div>
            <div class="dashboard-add-new-review-holder add-new-review-holder" style="display:none;">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                        <div class="elements-title">
                            <?php
                            $show_stars = Foodbakery_Reviews::enable_comments($post_id);
                            if ($show_stars == 'on') {
                                ?>
                                <h3><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_rate_and_write_a_review_label'); ?></h3>
                            <?php } else {
                                ?>
                                <h3><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_rate_and_write_a_comment_label'); ?></h3>
                                <?php
                            }
                            ?>
                            <a href="#" class="dashboard-close-post-new-reviews-btn close-post-new-reviews-btn"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_add_new_reviews_close_button'); ?></a>
                        </div>
                    </div>
                    <div class="foodbakery-add-review-data">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-element">
                                <i class="icon-edit2"></i>
                                <input type="text" placeholder="Title of your Comment *" name="review_title" id="review_title" value="">
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-element">
                                <i class="icon-user4"></i>
                                <input type="text" placeholder="Name *" name="review_full_name" id="review_full_name" value="<?php echo esc_html($publisher_display_name); ?>" <?php echo esc_html($publisher_display_name) != '' ? 'disabled="disabled"' : ''; ?>>
                                <input type="hidden" id="restaurant_id" name="restaurant_id" value="">
                                <input type="hidden" id="restaurant_type_id" name="restaurant_type_id" value="">
                                <input type="hidden" name="review_user_id" id="review_user_id" value="<?php echo esc_html($user_id); ?>">
                                <input type="hidden" name="company_id" id="company_id" value="<?php echo esc_html($company_id); ?>">
                                <input type="hidden" id="parent_review_id" name="parent_review_id" value="">
                                <input type="hidden" id="min_lenght" value="">
                                <input type="hidden" id="max_lenght" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-element">
                                <i class="icon-envelope3"></i>
                                <input type="text" placeholder="Email *" name="review_email_address" id="review_email_address" value="<?php echo esc_html($user_email); ?>" <?php echo esc_html($user_email) != '' ? 'disabled="disabled"' : ''; ?>>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-element">
                                <i class="icon-message"></i>
                                <textarea placeholder="Tell about your experience or leave a tip for others" cols="30" rows="10" name="review_description" id="review_description" maxlength="<?php echo intval($foodbakery_review_max_length); ?>"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-element message-length">
                                <span class="min_char"><?php echo esc_html__('Min characters:', 'foodbakery'); ?> <?php echo intval($foodbakery_review_min_length); ?></span>
                                <span class="max_char"><?php echo esc_html__('Max characters:', 'foodbakery'); ?> <?php echo intval($foodbakery_review_max_length); ?></span>
                                <div id="textarea_feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-element">
                                <div class="review-reply-button input-button-loader">
                                    <?php
                                    $show_ratings = $this->enable_comments($post_id);
                                    if ($show_ratings == 'on') {
                                        ?>

                                        <input type="button" name="send_your_review" id="dashboard_send_your_review_reply" value="<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_send_your_review_btn'); ?>">
                                        <?php
                                    } else {
                                        ?>

                                        <input type="button" name="send_your_review" id="dashboard_send_your_review_reply" value="<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_send_your_comment_btn'); ?>">
                                    <?php } ?>
                                </div>    

                                &nbsp;&nbsp;<span class="ajax-message"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <script type="text/javascript">
                (function ($) {
                    jQuery(function () {
                        jQuery(".chosen-select").chosen();

                        bind_delete_review_event();

                        var reviews_count = <?php echo esc_html($reviews_count); ?>;
                        var reviews_shown_count = <?php echo count($reviews); ?>;
                        var start = reviews_shown_count;
                        if (reviews_shown_count < reviews_count) {
                            jQuery(".btn-load-more").click(function () {
                                foodbakery_show_loader();
                                $.ajax({
                                    method: "POST",
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    dataType: "json",
                                    data: {
                                        action: "get_user_reviews_for_dashboard",
                                        company_id: "<?php echo esc_html($company_id); ?>",
                                        offset: start,
                                        my_review: 'yes',
                                        is_child: 'yes',
                                        is_company: 'no',
                                        sorty_by: jQuery(".slct-sort-by-dashboard-reviews").val(),
                                        security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                    },
                                    success: function (data) {
                                        foodbakery_hide_loader();
                                        if (data.success == true) {
                                            jQuery("ul.reviews-list").append(data.data);

                                            // Bind delete event for new reviews.
                                            bind_delete_review_event();

                                            start += data.count;
                                        }
                                        if (data.count == 0) {
                                            jQuery(".btn-more-holder").hide();
                                        }
                                    },
                                });
                                return false;
                            });
                        } else {
                            jQuery(".btn-more-holder").hide();
                        }

                        jQuery(".ajax-loader-sort-by").hide();
                        jQuery("input[name='review']").click(function () {
                            start = 0;
                            jQuery(".ajax-loader-sort-by").show();
                            var date_range = jQuery('.user-holder').find('#date_range').val();
                            foodbakery_show_loader('.loader-holder');
                            $.ajax({
                                method: "POST",
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                dataType: "json",
                                data: {
                                    action: "get_user_reviews_for_dashboard",
                                    company_id: "<?php echo esc_html($company_id); ?>",
                                    offset: start,
                                    my_review: 'yes',
                                    is_child: 'yes',
                                    is_company: 'no',
                                    date_range: date_range,
                                    sort_by: jQuery(this).val(),
                                    security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                },
                                success: function (data) {
                                    foodbakery_hide_loader();
                                    if (data.success == true) {
                                        jQuery(".review-listing ul li").remove();
                                        jQuery(".review-listing ul").append(data.data);

                                        // Bind delete event for new reviews.
                                        bind_delete_review_event();

                                        start += data.count;
                                    }
                                    if (data.count == 0) {
                                        jQuery(".btn-more-holder").hide();
                                    }
                                    jQuery(".ajax-loader-sort-by").hide();
                                },
                            });
                        });

                        function bind_delete_review_event() {
                            jQuery(".delete-this-user-review").click(function (e) {
                                //foodbakery_show_loader('.loader-holder');
                                e.preventDefault();
                                var thisObj = jQuery(this);
                                var loader_class = 'icon-spinner icon-spin';
                                var review_id = thisObj.data("review-id");
                                jQuery('#id_confrmdiv').show();
                                jQuery('#id_truebtn').click(function () {
                                    thisObj.find('i').addClass(loader_class);
                                    $.ajax({
                                        method: "POST",
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        dataType: "json",
                                        data: {
                                            action: "delete_user_review",
                                            review_id: review_id,
                                            security: "<?php echo wp_create_nonce('foodbakery-delete-review'); ?>",
                                        },
                                        success: function (data) {

                                            jQuery('#id_confrmdiv').hide();
                                            if (data.type == 'success') {
                                                thisObj.closest('li').hide('slide', 'left', 100);
                                                jQuery("#parent_review_" + review_id).hide();
                                                reviews_count--;
                                                jQuery(".element-slogan").html("(" + reviews_count + ")");
                                                foodbakery_show_response(data);
                                            }

                                        },
                                    });
                                });

                                jQuery('#id_falsebtn').click(function () {
                                    jQuery('#id_confrmdiv').hide();
                                    return false;
                                });



                            }); // end click event
                        } // function end
                    });
                })(jQuery);
            </script>
            <script>

                var is_review_added = false;
                var is_processing = false;
                jQuery("#dashboard_send_your_review_reply").click(function () {

                    var user_id = jQuery("#review_user_id").val();
                    var company_id = jQuery("#company_id").val();
                    var restaurant_id = jQuery("#restaurant_id").val();
                    var restaurant_type_id = jQuery("#restaurant_type_id").val();
                    var review_min_length = jQuery("#min_lenght").val();
                    var review_max_length = jQuery("#max_lenght").val();

                    if (is_processing == true) {
                        return false;
                    }
                    if (is_review_added == true) {
                        show_msg(<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review1_msg'); ?>, false);
                        return false;
                    }

                    var review_title = jQuery("#review_title").val();

                    if (review_title.length == 0) {
                        show_msg("<?php echo esc_html__('Please provide title of your review.', 'foodbakery') ?>", false);
                        return false;
                    }
                    if (review_title.length < 3) {
                        show_msg("<?php echo esc_html__('Title length must be 3 to long.', 'foodbakery') ?>", false);
                        return false;
                    }

                    var user_email = jQuery("#review_email_address").val();
                    if (is_email_valid(user_email) == false) {
                        show_msg"(<?php echo esc_html__('Please provide valid email address.', 'foodbakery') ?>", false);
                        return false;
                    }
                    var user_full_name = jQuery("#review_full_name").val();
                    if (user_full_name.length < 3) {
                        show_msg("<?php echo esc_html__('Please provide full name.', 'foodbakery') ?>", false);
                        return false;
                    }
                    var parent_review_id = jQuery("#parent_review_id").val();
                    var review_description = jQuery("#review_description").val();
                    if (review_description.length == 0) {
                        show_msg(<?php echo esc_html__('Please provide description of your review.', 'foodbakery') ?>, false);
                        return false;
                    }
                    if (review_description.length < review_min_length || review_description.length > review_max_length) {
                        show_msg('Description length must be ' + review_min_length + ' to ' + review_max_length + ' long.', false);
                        return false;
                    }
                    var overall_rating = jQuery(".overall-rating").data('overall-rating');

                    is_processing = true;

                    var thisObj = jQuery('.dashboard-add-new-review-holder .review-reply-button');
                    foodbakery_show_loader('.dashboard-add-new-review-holder .review-reply-button', '', 'button_loader', thisObj);

                    jQuery.ajax({
                        method: "POST",
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        dataType: "json",
                        data: {
                            action: "post_user_review",
                            ratings: '',
                            post_id: restaurant_id,
                            restaurant_type_id: restaurant_type_id,
                            user_id: user_id,
                            company_id: company_id,
                            user_email: user_email,
                            user_name: user_full_name,
                            review_title: review_title,
                            overall_rating: overall_rating,
                            description: review_description,
                            parent_review_id: parent_review_id,
                            'g-recaptcha-response': jQuery.data(document.body, 'recaptcha'),
                            security: "<?php echo wp_create_nonce('foodbakery-add-reviews'); ?>",
                        },
                        success: function (data) {
                            show_msg(data.msg, data.success);
                            if (data.success == true) {
                                setTimeout(function () {
                                    setTimeout(function () {
                                        jQuery("#foodbakery_publisher_my_reviews").trigger("click");
                                    }, 500);
                                }, 1000);
                            }
                            is_processing = false;
                        },
                    });
                });
                function is_email_valid(email) {
                    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return re.test(email);
                }
                function show_msg(msg, status) {

                    var type = status == true ? "success" : "error";
                    var response = {
                        type: type,
                        msg: msg
                    };
                    foodbakery_show_response(response);
                }

                jQuery(document).ready(function () {
                    var min_char_label = "<?php echo esc_html__('Min characters: ', 'foodbakery'); ?>";
                    var min_char_label = "<?php echo esc_html__('Min characters: ', 'foodbakery'); ?>";
                    jQuery(".dashboard-review-reply-btn").click(function () {
                        var review_ID = jQuery(this).data('id');
                        var restaurant_id = jQuery(this).attr('restaurant-id');
                        var restaurant_type_id = jQuery(this).attr('restaurant-type-id');

                        var min_lenght = jQuery(this).attr('min-lenght');
                        var max_lenght = jQuery(this).attr('max-lenght');
                        jQuery("#min_lenght").val(min_lenght);
                        jQuery("#max_lenght").val(max_lenght);

                        jQuery('#review_description').attr('maxlength', max_lenght);
                        jQuery('.message-length').find('.min_char').text(min_char_label + min_lenght);
                        jQuery('.message-length').find('.max_char').text(min_char_label + max_lenght);

                        jQuery("#review_title").val('');
                        jQuery("#review_description").val('');
                        jQuery("#parent_review_id").val(review_ID);
                        jQuery("#restaurant_id").val(restaurant_id);
                        jQuery("#restaurant_type_id").val(restaurant_type_id);
                        jQuery(".dashbard-user-reviews-list").css("display", "none");
                        jQuery(".dashboard-add-new-review-holder").css("display", "block");
                        jQuery('html, body').animate({
                            scrollTop: jQuery(".user-dashboard").offset().top - 70
                        }, 'slow');
                        return false;
                    });
                    jQuery(".dashboard-close-post-new-reviews-btn").click(function () {
                        jQuery(".dashbard-user-reviews-list").css("display", "block");
                        jQuery(".dashboard-add-new-review-holder").css("display", "none");
                        return false;
                    });

                });
            </script>
            <?php
            $output = ob_get_clean();
            echo force_balance_tags($output);
            wp_die();
        }

        /**
         * Handle AJAX request and render Dashboard Given Reviews Tab Container.
         */
        public function dashboard_reviews_ui_callback() {
            global $foodbakery_plugin_options;
            $user_id = get_current_user_id();
            if ($user_id == 0) {
                echo json_encode(array('success' => false, 'msg' => 'Invalid user.'));
                wp_die();
            }

            $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';
            $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
            $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
            Foodbakery_Reviews::$posts_per_page = $posts_per_page;
            $start = 0;
            if ($posts_paged != '') {
                $start = ( $posts_paged - 1 ) * $posts_per_page;
            }

            $company_id = get_user_meta($user_id, 'foodbakery_company', true);
            $company_id = ( $company_id != '' ? $company_id : 0 );


            $date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';
            $sort_by = (isset($_POST['sort_by']) && $_POST['sort_by'] != 'undefined' ) ? $_POST['sort_by'] : 'newest';

            $reviews_count = $this->get_user_reviews_count($company_id, true, false);
            $reviews = $this->get_user_reviews_for_post($company_id, $start, Foodbakery_Reviews::$posts_per_page, $sort_by, true, false, false);
            ob_start();
            ?>
            <div class="row">
                <div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="element-title has-border reviews-header right-filters-row">
                        <h5>
                            <span><?php esc_html_e('Reviews Given', 'foodbakery'); ?></span>
                            <?php if (0 < count($reviews)) : ?>
                                <span class="element-slogan"><?php echo sprintf('(' . foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_stats') . ')', $reviews_count); ?></span>
                            <?php endif; ?>
                        </h5>
                        <div class="right-filters row pull-right">
                            <?php if (0 < count($reviews)) : ?>
                                <?php
                                $sort_by_options = array(
                                    'newest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_newest_reviews_option'),
                                    'highest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_highest_rating_option'),
                                    'lowest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_lowest_rating_option'),
                                );
                                ?>
                                <div class="col-lg-6 col-md-6 col-xs-6">
                                    <div class="sort-by">
                                        <ul class="reviews-sortby">
                                            <li> 
                                                <small><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_label'); ?>:</small>
                                                <span> 
                                                    <strong class="active-sort">
                                                        <?php esc_html_e($sort_by_options[$sort_by]); ?>
                                                    </strong>
                                                </span>
                                                <div class="reviews-sort-dropdown">
                                                    <form>
                                                        <div class="input-reviews">
                                                            <?php
                                                            $i = 1;
                                                            foreach ($sort_by_options as $key => $sort_by_option) {
                                                                ?>
                                                                <div class="radio-field">
                                                                    <input name="review" id="check-<?php echo intval($i); ?>" type="radio" value="<?php echo esc_html($key); ?>" <?php echo ( $key == $sort_by ) ? 'checked="checked"' : ''; ?>>
                                                                    <label for="check-<?php echo intval($i); ?>"><?php esc_html_e($sort_by_options[$key]); ?></label>
                                                                </div>
                                                                <?php
                                                                $i ++;
                                                            }
                                                            ?>
                                                        </div>
                                                    </form>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php $date_range = isset($_REQUEST['date_range']) ? $_REQUEST['date_range'] : ''; ?>
                            <?php if ((0 <= count($reviews) && $date_range != '') || (0 < count($reviews) && $date_range == '' )) { ?>
                                <div class="col-lg-6 col-md-6 col-xs-6 pull-right">
                                    <div class="input-field">
                                        <i class="icon-angle-down"></i>
                                        <input type="text" id="daterange" value="<?php echo ($date_range != 'undefined' ) ? str_replace(',', ' - ', $date_range) : ''; ?>" placeholder="<?php echo esc_html__('Select Date Range', 'foodbakery'); ?>"/>
                                        <input type="hidden" name="date_range" id="date_range" value="<?php echo ($date_range != 'undefined' ) ? $date_range : ''; ?>" />
                                        <script type="text/javascript">
                                            jQuery('#daterange').daterangepicker({
                                                autoUpdateInput: false,
                                                opens: 'left',
                                                locale: {
                                                    format: 'DD/MM/YYYY'
                                                }
                                            },
                                                    function (start, end) {
                                                        var date_range = start.format('DD/MM/YYYY') + ',' + end.format('DD/MM/YYYY');
                                                        jQuery('#date_range').val(date_range);
                                                        var actionString = "foodbakery_publisher_reviews";
                                                        var pageNum = 1;
                                                        foodbakery_show_loader('.loader-holder');
                                                        var filter_parameters = get_filter_parameters();

                                                        var sort_by = jQuery(".reviews-sort-dropdown .radio-field.active input[name='review']").val();
                                                        var sort_filter_var = '';
                                                        if (typeof sort_by != 'undefined' && sort_by !== '') {
                                                            sort_filter_var = '&sort_by=' + sort_by;
                                                        }

                                                        if (typeof (ajaxRequest) != 'undefined') {
                                                            ajaxRequest.abort();
                                                        }
                                                        ajaxRequest = jQuery.ajax({
                                                            type: "POST",
                                                            url: foodbakery_globals.ajax_url,
                                                            data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters + sort_filter_var,
                                                            success: function (response) {
                                                                foodbakery_hide_loader();
                                                                jQuery('.user-holder').html(response);

                                                            }
                                                        });
                                                    });
                                            jQuery('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                                                jQuery('#daterange').val('');
                                                jQuery('#date_range').val('');
                                                var actionString = "foodbakery_publisher_reviews";
                                                var pageNum = 1;
                                                foodbakery_show_loader('.loader-holder');
                                                var filter_parameters = get_filter_parameters();

                                                var sort_by = jQuery(".reviews-sort-dropdown .radio-field.active input[name='review']").val();
                                                var sort_filter_var = '';
                                                if (typeof sort_by != 'undefined' && sort_by !== '') {
                                                    sort_filter_var = '&sort_by=' + sort_by;
                                                }

                                                if (typeof (ajaxRequest) != 'undefined') {
                                                    ajaxRequest.abort();
                                                }
                                                ajaxRequest = jQuery.ajax({
                                                    type: "POST",
                                                    url: foodbakery_globals.ajax_url,
                                                    data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters + sort_filter_var,
                                                    success: function (response) {
                                                        foodbakery_hide_loader();
                                                        jQuery('.user-holder').html(response);

                                                    }
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="user-reviews-list">
                        <?php if (0 < count($reviews)) : ?>
                            <script>

                                jQuery(document).ready(function () {

                                    jQuery('.reviews-sortby > li').on('click', function () {
                                        jQuery('.reviews-sortby > li').toggleClass('reviews-sortby-active');
                                        jQuery('.reviews-sortby > li').siblings();
                                        jQuery('.reviews-sortby > li').siblings().removeClass('reviews-sortby-active');
                                    });
                                    jQuery('.input-reviews > .radio-field label').on('click', function () {
                                        jQuery(this).parent().toggleClass('active');
                                        jQuery(this).parent().siblings();
                                        jQuery(this).parent().siblings().removeClass('active');
                                        /*replace inner Html*/
                                        var radio_field_active = jQuery(this).html();
                                        jQuery(".active-sort").html(radio_field_active);
                                        jQuery('.reviews-sortby > li').removeClass('reviews-sortby-active');
                                    });
                                    // Configure/customize these variables.
                                    var showChar = 220;  // How many characters are shown by default
                                    var ellipsestext = ".";
                                    var moretext = "Show more...";
                                    var lesstext = "Show less";
                                    jQuery('.more').each(function () {
                                        var content = jQuery(this).text();
                                        content = content.replace(/<\/?[^>]+(>|$)/g, "");
                                        if (content.length > showChar) {
                                            var c = content.substr(0, showChar);
                                            var h = content.substr(showChar, content.length - showChar);
                                            var data_check = h.replace(/\s+/, "");
                                            if (data_check != '') {
                                                var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
                                                jQuery(this).html(html);
                                                jQuery(this).find('.morecontent span').hide();

                                            }
                                        }

                                    });
                                    jQuery(".morelink").click(function () {
                                        if (jQuery(this).hasClass("less")) {
                                            jQuery(this).removeClass("less");
                                            jQuery(this).html(moretext);
                                            jQuery(this).parent().parent('.more').find('.moreellipses').slideToggle(1000);
                                        } else {
                                            jQuery(this).addClass("less");
                                            jQuery(this).html(lesstext);
                                            jQuery(this).parent().parent('.more').find('.moreellipses').slideToggle(1000);
                                        }

                                        jQuery(this).prev().toggle();
                                        return false;
                                    });
                                });

                            </script>   
                            <div class="review-listing">
                                <ul>
                                    <?php foreach ($reviews as $key => $review) : ?>
                                        <?php if (!empty($review)) { ?>
                                            <?php
                                            $post_slug = get_post_meta($reviews[$key]['id'], 'post_id', true);
                                            $args = array(
                                                'name' => $post_slug,
                                                'post_type' => 'restaurants',
                                            );
                                            $posts = get_posts($args);
                                            ?>
                                            <?php $reply_class = ( isset($review['is_reply']) && $review['is_reply'] == true ) ? 'review_reply' : ''; ?>
                                            <?php $parent_review = ( isset($review['parent_id']) && $review['parent_id'] != '' ) ? 'id="parent_review_' . $review['parent_id'] . '"' : ''; ?>
                                            <?php $review_title = ( isset($review['review_title']) && $review['review_title'] != '' ) ? ' ' . $review['review_title'] : ''; ?>
                                            <li class="alert <?php echo esc_html($reply_class); ?>" <?php echo esc_html($parent_review); ?>>
                                                <div class="list-holder">
                                                    <div class="review-text">
                                                        <div class="review-title">
                                                            <h6>
                                                                <?php
                                                                if (0 < count($posts)) {
                                                                    ?>
                                                                    <a href="<?php echo esc_url($posts[0]->guid); ?> " >  <?php echo esc_html($posts[0]->post_title); ?>: <?php echo esc_html($review_title); ?> </a>
                                                                    <?php
                                                                } else {
                                                                    echo esc_html__('POST DOES NOT EXIST.', 'foodbakery');
                                                                }
                                                                ?> 
                                                            </h6>


                                                            <?php
                                                            $show_ratings = $this->enable_comments($posts[0]->ID);
                                                            if ($show_ratings == 'on') {
                                                                ?>
                                                                <div class="rating-holder">

                                                                    <?php if (!isset($review['is_reply']) || $review['is_reply'] != true) { ?>
                                                                        <div class="rating-star">
                                                                            <span class="rating-box" style="width: <?php echo ( $review['overall_rating'] / 5 ) * 100; ?>%;"></span>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php }
                                                            ?>
                                                        </div>
                                                        <em class="review-date"><?php
                                                            echo human_time_diff(get_the_time('U', $review['id']), current_time('timestamp')) . ' ' . esc_html__('Ago', 'foodbakery');
                                                            ?>
                                                        </em>
                                                        <p class="more">
                                                            <?php
                                                            echo esc_html($review['description']);
                                                            ?>
                                                        </p>
                                                    </div>
                                                    <?php if (!isset($review['is_reply']) || $review['is_reply'] != true) { ?>
                                                        <a asif href="#" class="delete-this-user-review close" data-review-id="<?php echo esc_html($review['id']); ?>"><i class="icon-close2"></i></a>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php if (count($reviews) < $reviews_count) { ?>

                            <?php } ?>
                        <?php else: ?>
                            <div class="not-found">
                                <i class="icon-error"></i>
                                <p><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_no_reviews_text'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
            $total_pages = 1;
            if ($reviews_count > 0 && $posts_per_page > 0 && $reviews_count > $posts_per_page) {
                $total_pages = ceil($reviews_count / $posts_per_page);
                $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                $this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'reviews'), $foodbakery_dashboard_link) : '';
                foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'reviews');
            }
            ?>
            <script type="text/javascript">
                (function ($) {
                    jQuery(function () {
                        jQuery(".chosen-select").chosen();

                        bind_delete_review_event();

                        var reviews_count = <?php echo esc_html($reviews_count); ?>;
                        var reviews_shown_count = <?php echo count($reviews); ?>;
                        var start = reviews_shown_count;
                        if (reviews_shown_count < reviews_count) {
                            jQuery(".btn-load-more").click(function () {
                                foodbakery_show_loader();
                                $.ajax({
                                    method: "POST",
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    dataType: "json",
                                    data: {
                                        action: "get_user_reviews_for_dashboard",
                                        company_id: "<?php echo esc_html($company_id); ?>",
                                        offset: start,
                                        my_review: 'no',
                                        is_child: 'no',
                                        is_company: 'yes',
                                        sorty_by: jQuery(".slct-sort-by-dashboard-reviews").val(),
                                        security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                    },
                                    success: function (data) {
                                        foodbakery_hide_loader();
                                        if (data.success == true) {
                                            jQuery("ul.reviews-list").append(data.data);

                                            // Bind delete event for new reviews.
                                            bind_delete_review_event();

                                            start += data.count;
                                        }
                                        if (data.count == 0) {
                                            jQuery(".btn-more-holder").hide();
                                        }
                                    },
                                });
                                return false;
                            });
                        } else {
                            jQuery(".btn-more-holder").hide();
                        }

                        jQuery(".ajax-loader-sort-by").hide();
                        jQuery("input[name='review']").click(function () {
                            start = 0;
                            jQuery(".ajax-loader-sort-by").show();
                            var date_range = jQuery('.user-holder').find('#date_range').val();
                            foodbakery_show_loader('.loader-holder');
                            $.ajax({
                                method: "POST",
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                dataType: "json",
                                data: {
                                    action: "get_user_reviews_for_dashboard",
                                    company_id: "<?php echo esc_html($company_id); ?>",
                                    offset: start,
                                    my_review: 'no',
                                    is_child: 'no',
                                    is_company: 'yes',
                                    date_range: date_range,
                                    sort_by: jQuery(this).val(),
                                    security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                },
                                success: function (data) {
                                    foodbakery_hide_loader();
                                    if (data.success == true) {
                                        jQuery(".review-listing ul li").remove();
                                        jQuery(".review-listing ul").append(data.data);

                                        // Bind delete event for new reviews.
                                        bind_delete_review_event();

                                        start += data.count;
                                    }
                                    if (data.count == 0) {
                                        jQuery(".btn-more-holder").hide();
                                    }
                                    jQuery(".ajax-loader-sort-by").hide();
                                },
                            });
                        });

                        function bind_delete_review_event() {
                            jQuery(".delete-this-user-review").click(function (e) {
                                //foodbakery_show_loader('.loader-holder');
                                e.preventDefault();//icon-close2
                                var thisObj = jQuery(this);
                                var loader_class = 'icon-spinner icon-spin';
                                var review_id = thisObj.data("review-id");
                                jQuery('#id_confrmdiv').show();
                                jQuery('#id_truebtn').click(function () {
                                    thisObj.find('i').addClass(loader_class);
                                    $.ajax({
                                        method: "POST",
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        dataType: "json",
                                        data: {
                                            action: "delete_user_review",
                                            review_id: review_id,
                                            security: "<?php echo wp_create_nonce('foodbakery-delete-review'); ?>",
                                        },
                                        success: function (data) {
                                            jQuery('#id_confrmdiv').hide();
                                            if (data.type == 'success') {
                                                thisObj.closest('li').hide('slide', 'left', 400);
                                                jQuery("#parent_review_" + review_id).hide();
                                                reviews_count--;
                                                jQuery(".element-slogan").html("(" + reviews_count + ")");
                                            }
                                            foodbakery_show_response(data);
                                        },
                                    });
                                });

                                jQuery('#id_falsebtn').click(function () {
                                    jQuery('#id_confrmdiv').hide();
                                    return false;
                                });



                            }); // end click event
                        } // function end
                    });
                })(jQuery);
            </script>
            <?php
            $output = ob_get_clean();
            echo force_balance_tags($output);
            wp_die();
        }

        /**
         * Register strings in Plugin strings for easy language translation.
         *
         * @param	array	$foodbakery_static_text
         * @return	array
         */
        public function plugin_text_strings_callback($foodbakery_static_text) {
            /*
             * Strings for Custom Post Type.
             */
            //global $wpdb;

            $foodbakery_static_text['foodbakery_reviews_name'] = esc_html__('Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_singular_name'] = esc_html__('Review', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_add_review'] = esc_html__('Add Review', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_add_new_review'] = esc_html__('Add New Review', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_new_review'] = esc_html__('New Review', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_edit_review'] = esc_html__('Edit Review', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_view_review'] = esc_html__('View Review', 'foodbakery');

            $foodbakery_static_text['foodbakery_reviews_search_reviews'] = esc_html__('Search Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_not_found_reviews'] = esc_html__('No reviews found.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_not_found_in_trash_reviews'] = esc_html__('No reviews found in Trash.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_description'] = esc_html__('No reviews found in Trash.', 'foodbakery');

            $foodbakery_static_text['review_title'] = esc_html__('Review Title', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_overall_rating'] = esc_html__('Overall Rating', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_username'] = esc_html__('User Name', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_detail'] = esc_html__('Reviews Detail', 'foodbakery');

            /*
             * Strings for settings.
             */
            $foodbakery_static_text['foodbakery_reviews_settings_tab_text'] = esc_html__('Reviews Settings', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_enable_user_reviews'] = esc_html__('Review & Ratings', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_enable_user_reviews_hint'] = esc_html__('Turn on/off user reviews system.', 'foodbakery');

            $foodbakery_static_text['foodbakery_reviews_auto_approve_reviews'] = esc_html__('Auto Approve Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_auto_approve_reviews_hint'] = esc_html__('Do you want to Reviews get approved automatically?', 'foodbakery');

            $foodbakery_static_text['foodbakery_reviews_without_login_user_reviews'] = esc_html__('Reviews Without Login', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_without_login_user_reviews_hint'] = esc_html__('Allow user to add review without login.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_enable_review_comment'] = esc_html__('Enable Comments', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_enable_multiple_reviews'] = esc_html__('Enable Multiple Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_min_length'] = esc_html__('Review Min Length', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_max_length'] = esc_html__('Review Max Length', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_number_of_reviews'] = esc_html__('Number of reviews to list', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_load_more_reviews'] = esc_html__('Load More Reviews Option', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_captcha_for_reviews'] = esc_html__('Captcha', 'foodbakery');

            $foodbakery_static_text['foodbakery_reviews_settings_labels'] = esc_html__('Define a set of score values.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_settings_labels_label'] = esc_html__('Score Value', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_settings_labels_rating'] = esc_html__('Rating Value [1-100]', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_settings_labels_cntrl_delete_row'] = esc_html__('Delate Row', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_settings_labels_cntrl_add_row'] = esc_html__('+Add Score Value', 'foodbakery');
            //features
            $foodbakery_static_text['foodbakery_feature_setting_labels_add'] = esc_html__('Add Feature', 'foodbakery');
            $foodbakery_static_text['foodbakery_feature_setting_labels_add_label'] = esc_html__('Label', 'foodbakery');

            /*
             * Strings for frontend UI.
             */
            $foodbakery_static_text['foodbakery_reviews_total_reviews_label'] = esc_html__('%d Verified Reviews', 'foodbakery');

            $foodbakery_static_text['foodbakery_reviews_total_comments_label'] = esc_html__('%d  Comments', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_post_new_reviews_button'] = esc_html__('Post new reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_post_new_reviews_comments'] = esc_html__('Post new comments', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_rating_summary_heading'] = esc_html__('Rating summary', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_overall_rating_heading'] = esc_html__('Customer Reviews For Food Bakery', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_all_reviews_heading'] = esc_html__('Customer Reviews For', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_all_comments_heading'] = esc_html__('Comments', 'foodbakery');

            $foodbakery_static_text['foodbakery_reviews_rate_and_write_a_review_label'] = esc_html__('Rate and Write a Review', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_rate_and_write_a_comment_label'] = esc_html__('Write a Comment', 'foodbakery');


            $foodbakery_static_text['foodbakery_reviews_add_new_reviews_close_button'] = esc_html__('Close', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_overall_rating_label'] = esc_html__('Overall rating', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_your_overall_rating_label'] = esc_html__('Your overall rating of this restaurant', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_send_your_review_btn'] = esc_html__('Submit your Review', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_send_your_comment_btn'] = esc_html__('Submit your Comment', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_textarea_word_length'] = esc_html__(' Words typed: ', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_read_more_text'] = esc_html__('Read More...', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_read_more_reviews_text'] = esc_html__('Read More Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_read_more_comments_text'] = esc_html__('Read More Comments', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_no_reviews_text'] = esc_html__('Only customers can write reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_no_reviews_text_strong'] = esc_html__('Write your own reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_sort_by_highest_rating_option'] = esc_html__('Highest Rating', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_sort_by_lowest_rating_option'] = esc_html__('Lowest Rating', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_sort_by_newest_reviews_option'] = esc_html__('Newest Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_request_processing_text'] = esc_html__('Processing...', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_incomplete_data_msg'] = esc_html__('Incomplete data.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_success_msg'] = esc_html__('Your review successfully added.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_no_more_reviews_text'] = esc_html__('Sorry, no more reviews.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_multiple_review_msg'] = esc_html__('Sorry, You have already added review for this restaurent.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_already_added_review_msg'] = esc_html__('Sorry, You have already added review for this order.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_already_added_review0_msg'] = esc_html__('You have already added review for this order.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_already_added_review1_msg'] = __("'You have already added review for this order.'", 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_post_owner_review_msg'] = esc_html__('Owner of the restaurent is not allowed to add review.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_withtout_login_msg'] = esc_html__('Please login in order to post review.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_recaptcha_error_msg'] = esc_html__('Please select captcha field.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_post_notallowed_review_msg'] = esc_html__('You are not allowed to post any review.', 'foodbakery');
            /*
             * Strings for Dashboard
             */
            $foodbakery_static_text['foodbakery_reviews_dashboard_heading'] = esc_html__('My Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_given_reviews_dashboard_heading'] = esc_html__('Given Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_stats'] = esc_html__('%d', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_sort_by_label'] = esc_html__('Sort by', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_sort_by_highest_rating_option'] = esc_html__('Highest Rating', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_sort_by_lowest_rating_option'] = esc_html__('Lowest Rating', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_sort_by_newest_reviews_option'] = esc_html__('Newest Reviews', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_no_reviews_text'] = esc_html__('You haven\'t written any reviews.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_no_reviews_found_text'] = esc_html__('Sorry! reviews not found.', 'foodbakery');
            $foodbakery_static_text['foodbakery_reviews_dashboard_delete_success_msg'] = esc_html__('Your review successfully got deleted.', 'foodbakery');

            $foodbakery_static_text['foodbakery_reviews_reply_for'] = esc_html__('Reply for', 'foodbakery');
            $foodbakery_static_text['foodbakery_select_review_reply_for'] = esc_html__('Select review reply for', 'foodbakery');
            $foodbakery_static_text['foodbakery_post_reply'] = esc_html__('Post a reply', 'foodbakery');



            return $foodbakery_static_text;
        }

        /**
         * Render reviews settings tab for reviews on restaurant type add/edit page.
         *
         * @param WP_Post $post
         */
        public function restaurant_type_options_sidebar_tab_callback($post) {
            ?>
            <li>
                <a href="javascript:;" name="#tab-reviews_settings">
                    <i class="icon-star"></i>
                    <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_settings_tab_text'); ?>
                </a>
            </li>
            <?php
        }

        /**
         * Render reviwes settings container for reviews on restaurant type add/edit page.
         *
         * @param WP_Post $post
         */
        public function restaurant_type_options_tab_container_callback($post) {
            global $foodbakery_html_fields;
            $post_meta = get_post_meta(get_the_id());


            $ratings = array();
            ?>
            <div id="tab-reviews_settings" class="foodbakery_tab_block" data-title="<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_settings_tab_text'); ?>">
                <?php
                $foodbakery_opt_array = array(
                    'name' => foodbakery_plugin_text_srt('foodbakery_reviews_enable_user_reviews'),
                    'desc' => '',
                    'hint_text' => foodbakery_plugin_text_srt('foodbakery_reviews_enable_user_reviews_hint'),
                    'echo' => true,
                    'field_params' => array(
                        'std' => '',
                        'id' => 'user_reviews',
                        'return' => true,
                    ),
                );
                $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'name' => foodbakery_plugin_text_srt('foodbakery_reviews_auto_approve_reviews'),
                    'desc' => '',
                    'hint_text' => foodbakery_plugin_text_srt('foodbakery_reviews_auto_approve_reviews_hint'),
                    'echo' => true,
                    'field_params' => array(
                        'std' => '',
                        'id' => 'auto_approve_reviews',
                        'return' => true,
                    ),
                );
                $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);


                $foodbakery_opt_array = array(
                    'name' => foodbakery_plugin_text_srt('foodbakery_reviews_load_more_reviews'),
                    'desc' => '',
                    'echo' => true,
                    'field_params' => array(
                        'std' => '',
                        'id' => 'review_load_more_option',
                        'return' => true,
                    ),
                );
                $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'name' => foodbakery_plugin_text_srt('foodbakery_reviews_min_length'),
                    'desc' => '',
                    'hint_text' => '',
                    'echo' => true,
                    'field_params' => array(
                        'std' => '200',
                        'id' => 'review_min_length',
                        'classes' => 'foodbakery-dev-req-field-admin foodbakery-number-field',
                        'return' => true,
                    ),
                );
                $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'name' => foodbakery_plugin_text_srt('foodbakery_reviews_max_length'),
                    'desc' => '',
                    'hint_text' => '',
                    'echo' => true,
                    'field_params' => array(
                        'std' => '500',
                        'id' => 'review_max_length',
                        'classes' => 'foodbakery-dev-req-field-admin foodbakery-number-field',
                        'return' => true,
                    ),
                );
                $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                $foodbakery_opt_array = array(
                    'name' => foodbakery_plugin_text_srt('foodbakery_reviews_number_of_reviews'),
                    'desc' => '',
                    'hint_text' => '',
                    'echo' => true,
                    'field_params' => array(
                        'std' => '10',
                        'id' => 'review_number_of_reviews',
                        'classes' => 'foodbakery-dev-req-field-admin foodbakery-number-field',
                        'return' => true,
                    ),
                );
                $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

                $foodbakery_html_fields->foodbakery_heading_render(array('name' => esc_html__('Score Values', 'foodbakery')));

                $reviews_data = array();

                if (isset($post_meta['foodbakery_reviews_labels']) && isset($post_meta['foodbakery_reviews_labels'][0])) {
                    $reviews_data = json_decode($post_meta['foodbakery_reviews_labels'][0], true);
                }
                ?>
                <div class="form-elements">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="txt-rating-top-heading"><b><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_settings_labels'); ?></b></label>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <table class="rating-templates-wrapper">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">&nbsp;</th>
                                    <th><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_settings_labels_label'); ?></th>

                                    <th style="width: 45px;">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($reviews_data) > 0) : ?>
                                    <?php foreach ($reviews_data as $key => $value) :
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                            </td>
                                            <td>
                                                <input type="text" name="review_label[]" value="<?php echo esc_html($value); ?>" class="review_label">
                                            </td>

                                            <td style="text-align: center;">
                                                <a href="#" class="cntrl-delete-row" title="<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_settings_labels_cntrl_delete_row'); ?>"><i class="icon-cancel2"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td>
                                            <span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                        </td>
                                        <td>
                                            <input type="text" value="<?php echo esc_html__('Service', 'foodbakery'); ?>" name="review_label[]" class="review_label">
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0);" class="cntrl-delete-row" title=""><i class="icon-cancel2"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                        </td>
                                        <td>
                                            <input type="text" value="<?php echo esc_html__('Quality', 'foodbakery'); ?>" name="review_label[]" class="review_label">
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0);" class="cntrl-delete-row" title=""><i class="icon-cancel2"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span>
                                        </td>
                                        <td>
                                            <input type="text" value="<?php echo esc_html__('Value', 'foodbakery'); ?>" name="review_label[]" class="review_label">
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0);" class="cntrl-delete-row" title=""><i class="icon-cancel2"></i></a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <a href="javascript:void(0);" class="cntrl-add-new-row adding_review_scores"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_settings_labels_cntrl_add_row'); ?></a>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                (function ($) {
                    jQuery(function () {
                        var table_class = ".rating-templates-wrapper";

                        jQuery(table_class + " tbody").sortable({
                            //items: "> tr:not(:last)",
                            cancel: "input"
                        });

                        jQuery(".adding_review_scores").click(function () {
                            jQuery(table_class + " tbody tr:last").after(jQuery(
                                    '<tr><td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td><input type="text" value="" name="review_label[]" class="review_label"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-row" title="Delate Row"><i class="icon-cancel2"></i></a></td></tr>'
                                    ));
                            jQuery(".cntrl-delete-row").click(function () {
                                delete_row(this);
                                return false;
                            });
                            return false;
                        });

                        jQuery(".cntrl-delete-row").click(function () {
                            delete_row(this);
                            return false;
                        });

                        function delete_row(delete_link) {
                            jQuery(delete_link).parent().parent().remove();
                        }

                        var reviews_data = <?php echo json_encode($reviews_data); ?>;
                        var reviews_count = <?php echo count($reviews_data); ?>;
                        if (reviews_count > 0) {

                            jQuery(".cntrl-delete-row").click(function () {
                                delete_row(this);
                                return false;
                            });
                        }
                        jQuery("form#post").submit(function () {
                            var labels = [];
                            jQuery(table_class + " tbody input.review_label").each(function () {
                                labels.push(jQuery(this).val());
                            });
                            var asJSON = JSON.stringify(labels);
                            var hdnField = jQuery('<input type="hidden" value="" name="foodbakery_reviews_labels">');
                            hdnField.val(asJSON);
                            jQuery(this).append(hdnField);
                        });
                    });
                })(jQuery);
            </script>
            <?php
        }

        /**
         * Output UI for add new review form for order details / restuarant detail page of a post.
         *
         * @param type $post_id
         */
        public function review_form_ui_callback($post_id = '', $order_id = '') {
            global $foodbakery_plugin_options;
            $rand_id = rand(100, 9000);
            $order_rand_id = $order_id . $rand_id;
            $show_ratings = $this->enable_comments($post_id);
            $is_reviews_enabled = 'off';
            $is_reviews_without_login = 'off';
            $restaurant_type = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
            $the_slug = $restaurant_type;
            $args = array(
                'name' => $the_slug,
                'post_type' => 'restaurant-type',
                'post_status' => 'publish',
                'numberposts' => 1
            );
            $restaurant_types = get_posts($args);
            // If no restaurant type found then skip reviews section.
            if (1 > count($restaurant_types)) {
                return;
            }
            $reviews_count = 0;
            $ratings_summary = array();
            $overall_ratings = array(
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0,
            );
            $restaurant_type_id = $restaurant_types[0]->ID;
            $is_reviews_enabled = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);
            $is_reviews_enabled = ( $is_reviews_enabled == '' ? 'off' : $is_reviews_enabled );
            if ($is_reviews_enabled == 'off') {
                return;
            }
            $is_reviews_without_login = get_post_meta($restaurant_type_id, 'foodbakery_review_without_login', true);
            $is_reviews_without_login = ( $is_reviews_without_login == '' ? 'off' : $is_reviews_without_login );

            $is_review_response_enable = get_post_meta($post_id, 'foodbakery_transaction_restaurant_ror', true);
            $is_review_response_enable = ( isset($is_review_response_enable) && $is_review_response_enable == 'on' ) ? true : false;

            $foodbakery_reviews_labels = get_post_meta($restaurant_type_id, 'foodbakery_reviews_labels', true);
            $foodbakery_reviews_labels = ( $foodbakery_reviews_labels == '' ? array() : json_decode($foodbakery_reviews_labels, true) );
            $foodbakery_review_min_length = get_post_meta($restaurant_type_id, 'foodbakery_review_min_length', true);
            $foodbakery_review_min_length = ( $foodbakery_review_min_length == '' ? 10 : $foodbakery_review_min_length );
            $foodbakery_review_max_length = get_post_meta($restaurant_type_id, 'foodbakery_review_max_length', true);
            $foodbakery_review_max_length = ( $foodbakery_review_max_length == '' ? 200 : $foodbakery_review_max_length );
            $foodbakery_review_number_of_reviews = get_post_meta($restaurant_type_id, 'foodbakery_review_number_of_reviews', true);
            $foodbakery_review_number_of_reviews = ( $foodbakery_review_number_of_reviews == '' ? 10 : $foodbakery_review_number_of_reviews );
            Foodbakery_Reviews::$posts_per_page = $foodbakery_review_number_of_reviews;
            $foodbakery_review_load_more_option = get_post_meta($restaurant_type_id, 'foodbakery_review_load_more_option', true);
            $foodbakery_review_load_more_option = ( $foodbakery_review_load_more_option == '' ? 'off' : $foodbakery_review_load_more_option );
            $foodbakery_review_captcha_for_reviews = get_post_meta($restaurant_type_id, 'foodbakery_review_captcha_for_reviews', true);
            $foodbakery_review_captcha_for_reviews = $foodbakery_review_captcha_for_reviews == '' ? 'off' : $foodbakery_review_captcha_for_reviews;
            // Get all reviews for this post.
            $reviews = $this->get_user_reviews_for_post($post_id, 0, Foodbakery_Reviews::$posts_per_page);
            $reviews = array_filter($reviews);

            // Get existing ratings for this post.
            $existing_ratings_data = get_post_meta($post_id, 'foodbakery_ratings', true);
            if ('' != $existing_ratings_data && 0 < count($reviews)) {
                $reviews_count = $existing_ratings_data['reviews_count'];
                $existing_ratings = $existing_ratings_data['ratings'];
                foreach ($foodbakery_reviews_labels as $key => $val) {
                    if (isset($existing_ratings[$val])) {
                        $value = $existing_ratings[$val];
                    } else {
                        $value = 0;
                    }
                    $ratings_summary[] = array('label' => $val, 'value' => $value);
                }
                $existing_overall_ratings = $existing_ratings_data['overall_rating'];
                foreach ($existing_overall_ratings as $key => $val) {
                    if (isset($overall_ratings[$key])) {
                        $overall_ratings[$key] = $val;
                    }
                }
            } else {
                foreach ($foodbakery_reviews_labels as $key => $val) {
                    $ratings_summary[] = array('label' => $val, 'value' => 0);
                }
                $reviews = array();
            }
            $user_id = 0;
            $company_id = 0;
            $user_email = '';
            $user_full_name = '';
            $current_user = wp_get_current_user();
            if (0 < $current_user->ID) {
                $user_id = $current_user->ID;
                $user_full_name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
                $company_id = get_user_meta($user_id, 'foodbakery_company', true);
                $user_email = get_post_meta($company_id, 'foodbakery_email_address', true);
                if (!isset($user_email) || $user_email == '') {
                    $user_email = $current_user->user_email;
                }
            }

            $publisher_display_name = '';

            // If company id is 0 it means this review is without login requirement.
            $have_review_added = false;
            $is_user_post_owner = false;
            if (0 < $company_id) {
                $have_review_added = apply_filters('have_user_added_review_for_this_post', $have_review_added, $company_id, $post_id);
                $is_user_post_owner = $this->is_this_user_owner_of_this_post_callback(false, $company_id, $post_id);
                $publisher_display_name = get_the_title($company_id);
            } else if ('' != $user_email) {
                $have_review_added = $this->have_user_added_review_for_this_post_callback(false, $user_email, $post_id, true);
            }

            if ($is_user_post_owner == true) {
                $have_review_added = false;
            }

            $existing_ratings = get_post_meta($post_id, 'foodbakery_ratings', true);
            ?>
            <div class="reviews-holder">

                <div class="review-form-<?php echo esc_html($rand_id); ?>">
                    <div class="add-new-review-holder add-new-review-<?php echo esc_html($rand_id); ?>">
                        <div class="row">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <?php
                                $show_stars = Foodbakery_Reviews::enable_comments($post_id);
                                if ($show_stars == 'on') {
                                    ?>
                                    <h3><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_rate_and_write_a_review_label'); ?></h3>
                                <?php } else {
                                    ?>
                                    <h3><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_rate_and_write_a_comment_label'); ?></h3>
                                    <?php
                                }
                                ?>


                            </div>
                            <?php if ($have_review_added == true) : ?>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                    <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review_msg'); ?>
                                </div>


                            <?php elseif (( is_user_logged_in() ) && true !== Foodbakery_Member_Permissions::check_permissions('reviews')) : ?>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                    <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_post_notallowed_review_msg'); ?>
                                </div>
                            <?php elseif ((!is_user_logged_in() ) && $is_reviews_without_login == 'off') : ?>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                    <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_withtout_login_msg'); ?>
                                </div>
                            <?php else : ?>
                                <div class="foodbakery-add-review-data">
                                    <div class="foodbakery-added-review-string" style="display:none;">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                            <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review_msg'); ?>
                                        </div>
                                    </div>
                                    <?php ?>
                                    <?php
                                    if ($show_ratings == 'on') {
                                        if ($is_user_post_owner != true) {
                                            ?>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="rating-restaurant">
                                                    <ul class="star-rating-list">
                                                        <?php foreach ($ratings_summary as $key => $rating): ?>
                                                            <li class="rating_summary_item" id="<?php echo str_replace(' ', '-', strtolower(esc_html($rating['label']))); ?>" data-selected-rating="1" data-label="<?php echo esc_html($rating['label']); ?>">
                                                                <span><?php echo esc_html($rating['label']); ?></span>
                                                                <div class="stars">
                                                                    <input type="radio" name="star<?php echo esc_html($key); ?>" class="star-1" checked="checked">
                                                                    <label class="star-1" for="star-1">1</label>
                                                                    <input type="radio" name="star<?php echo esc_html($key); ?>" class="star-2">
                                                                    <label class="star-2" for="star-2">2</label>
                                                                    <input type="radio" name="star<?php echo esc_html($key); ?>" class="star-3">
                                                                    <label class="star-3" for="star-3">3</label>
                                                                    <input type="radio" name="star<?php echo esc_html($key); ?>" class="star-4">
                                                                    <label class="star-4" for="star-4">4</label>
                                                                    <input type="radio" name="star<?php echo esc_html($key); ?>" class="star-5">
                                                                    <label class="star-5" for="star-5">5</label>
                                                                    <span style="width: 20%;"></span>
                                                                </div>

                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>


                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="total-rating user-rating-container overall-rating" data-overall-rating="1">
                                                    <h6><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_overall_rating_label'); ?></h6>
                                                    <div class="rating-star">
                                                        <input type="radio" name="star" class="star-1" checked="checked">
                                                        <label class="star-1" for="star-1">1</label>
                                                        <input type="radio" name="star" class="star-2">
                                                        <label class="star-2" for="star-2">2</label>
                                                        <input type="radio" name="star" class="star-3">
                                                        <label class="star-3" for="star-3">3</label>
                                                        <input type="radio" name="star" class="star-4">
                                                        <label class="star-4" for="star-4">4</label>
                                                        <input type="radio" name="star" class="star-5">
                                                        <label class="star-5" for="star-5">5</label>
                                                        <span style="width: 20%;"></span>
                                                    </div>

                                                </div>
                                            </div> 


                                            <?php
                                        }
                                    }
                                    ?>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-element">
                                            <i class="icon-edit2"></i>
                                            <?php
                                            if ($show_ratings == 'on') {
                                                ?>

                                                <input type="text" placeholder="<?php echo esc_html__('Title of your review *', 'foodbakery'); ?>" name="review_title" id="review_title" value="">
                                                <?php
                                            } else {
                                                ?>
                                                <input type="text" placeholder="<?php echo esc_html__('Title of your Comment *', 'foodbakery'); ?>" name="review_title" id="review_title" value="">
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-element">
                                            <i class="icon-user4"></i>
                                            <input type="text" placeholder="<?php echo esc_html__('Name *', 'foodbakery'); ?>" name="review_full_name" id="review_full_name" value="<?php echo esc_html($publisher_display_name); ?>" <?php echo esc_html($publisher_display_name) != '' ? 'disabled="disabled"' : ''; ?>>
                                            <input type="hidden" name="review_user_id" id="review_user_id" value="<?php echo esc_html($user_id); ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-element">
                                            <i class="icon-envelope3"></i>
                                            <input type="text" placeholder="<?php echo esc_html__('Email *', 'foodbakery'); ?>" name="review_email_address" id="review_email_address" value="<?php echo esc_html($user_email); ?>" <?php echo esc_html($user_email) != '' ? 'disabled="disabled"' : ''; ?>>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-element">
                                            <i class="icon-message"></i>
                                            <textarea placeholder="<?php echo esc_html__('Tell about your experience or leave a tip for others', 'foodbakery'); ?>" cols="30" rows="10" name="review_description" id="review_description" maxlength="<?php echo intval($foodbakery_review_max_length); ?>"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-element message-length">
                                            <span><?php echo esc_html__('Min characters:', 'foodbakery'); ?> <?php echo intval($foodbakery_review_min_length); ?></span>
                                            <span><?php echo esc_html__('Max characters:', 'foodbakery'); ?> <?php echo intval($foodbakery_review_max_length); ?></span>
                                            <div id="textarea_feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-element send_review_holder_<?php echo esc_html($rand_id); ?>">
                                            <input type="hidden" id="parent_review_id" name="parent_review_id" value="">
                                            <?php
                                            if ($show_ratings == 'on') {
                                                ?>

                                                <input type="button" name="send_your_review" id="send_your_review<?php echo esc_html($rand_id); ?>" value="<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_send_your_review_btn'); ?>">
                                                <?php
                                            } else {
                                                ?>

                                                <input type="button" name="send_your_review" id="send_your_review<?php echo esc_html($rand_id); ?>" value="<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_send_your_comment_btn'); ?>">
                                            <?php } ?>
                                            <span class="ajax-message"></span>
                                        </div>
                                    </div>

                                </div>    
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">

                var $ = jQuery;
                jQuery(document).on("click", ".review-form-<?php echo esc_html($rand_id); ?> .stars label", function () {
                    var $ = jQuery;
                    var array = new Array();
                    var checked = 0;
                    var starValue = jQuery(this).text();
                    var css_class = jQuery(this).attr('class');
                    var selected_id = jQuery(this).parent().parent().attr('id');
                    set_star_width_of_span(css_class, this);
                    jQuery(this).parent().parent().attr('data-selected-rating', starValue);
                    jQuery(".review-form-<?php echo esc_html($rand_id); ?> .star-rating-list > li").each(function (index, element) {
                        var starValue = jQuery(this).attr('data-selected-rating');
                        checked += parseInt(starValue);
                    });

                    var selected_radio_name = jQuery(this).prev('input').attr('name');

                    jQuery('input:radio[name=' + selected_radio_name + ']').each(function () {
                        if (jQuery(this).attr('class') === css_class) {
                            jQuery(this).attr('checked', true);
                        } else {
                            jQuery(this).attr('checked', false);
                        }
                    });


                    var number_of_items = jQuery('.review-form-<?php echo esc_html($rand_id); ?> .star-rating-list li').length;
                    var over_all_value = checked / number_of_items;
                    over_all_value_rounded = Math.round(over_all_value);

                    var span_width = "20%";
                    if (over_all_value_rounded == 1) {
                        span_width = "20%";
                    } else if (over_all_value_rounded == 2) {
                        span_width = "40%";
                    } else if (over_all_value_rounded == 3) {
                        span_width = "60%";
                    } else if (over_all_value_rounded == 4) {
                        span_width = "80%";
                    } else if (over_all_value_rounded == 5) {
                        span_width = "100%";
                    }
                    jQuery(".review-form-<?php echo esc_html($rand_id); ?> .rating-star span").css("width", span_width);
                    jQuery(".review-form-<?php echo esc_html($rand_id); ?> .total-rating").attr('data-overall-rating', over_all_value_rounded);
                });

                jQuery(".review-form-<?php echo esc_html($rand_id); ?> .stars label").hover(
                        function () {
                            var css_class = jQuery(this).attr('class');
                            set_star_width_of_span(css_class, this);
                        },
                        function () {
                            var css_class = jQuery("input[name^='star']:checked", jQuery(this).parent()).attr('class');
                            var selected_rating = jQuery(this).parent().parent().attr('data-selected-rating');
                            set_star_width_of_span('star-' + selected_rating + '', this);
                        }
                );

                var reviews_count = "<?php echo intval($reviews_count) ?>";
                var is_review_added = false;
                var res_order_id = "<?php echo esc_html($order_id); ?>";
                var page_num = "<?php echo isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '1'; ?>";
                var is_processing = false;
                var review_min_length = "<?php echo intval($foodbakery_review_min_length); ?>";
                var review_max_length = "<?php echo intval($foodbakery_review_max_length); ?>";
                var posts_per_page = "<?php echo intval($foodbakery_review_number_of_reviews); ?>";
                var load_more_option = "<?php echo intval($foodbakery_review_load_more_option); ?>";

                jQuery(document).on("click", ".review-form-<?php echo esc_html($rand_id); ?> #send_your_review<?php echo esc_html($rand_id); ?>", function () {


                    var thisObj = jQuery('.review-form-<?php echo esc_html($rand_id); ?> .send_review_holder_<?php echo esc_html($rand_id); ?>');

                    if (is_processing == true) {
                        return false;
                    }
                    if (is_review_added == true) {
                        show_msg("<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review1_msg'); ?>", false, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');
                        return false;
                    }
                    var ratings = {};
                    jQuery(".review-form-<?php echo esc_html($rand_id); ?> .add-new-review-holder .rating_summary_item").each(function (key, elem) {
                        rating = jQuery(elem).data('selected-rating');
                        label = jQuery(elem).data('label');
                        ratings[label] = rating;
                    });
                    var user_id = jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_user_id").val();

                    var review_title = jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_title").val();

                 if (review_title.length == 0) {
                        show_msg("<?php echo esc_html__('Please provide title of your review.', 'foodbakery'); ?>", false, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');
                        return false;
                    }
                    if (review_title.length < 3) {
                        show_msg("<?php echo esc_html__('Title length must be 3 to long.', 'foodbakery'); ?>", false, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');
                        return false;
                    }
                    var user_email = jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_email_address").val();
                    if (is_email_valid(user_email) == false) {
                        show_msg("<?php echo esc_html__('Please provide valid email address.', 'foodbakery'); ?>", false, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');
                        return false;
                    }
                    var user_full_name = jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_full_name").val();
                    if (user_full_name.length < 3) {
                        show_msg("<?php echo esc_html__('Please provide full name.', 'foodbakery') ?>", false, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');
                        return false;
                    }
                    var parent_review_id = jQuery(".review-form-<?php echo esc_html($rand_id); ?> #parent_review_id").val();
                    var review_description = jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_description").val();
                    if (review_description.length == 0) {
                        show_msg("<?php echo esc_html__('Please provide description of your review.', 'foodbakery') ?>", false, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');
                        return false;
                    }
                    if (review_description.length < review_min_length || review_description.length > review_max_length) {
                        show_msg('Description length must be ' + review_min_length + ' to ' + review_max_length + ' long.', false, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');
                        return false;
                    }


                    var overall_rating = jQuery(".review-form-<?php echo esc_html($rand_id); ?> .overall-rating").data('overall-rating');


                    foodbakery_show_loader('.review-form-<?php echo esc_html($rand_id); ?> .send_review_holder_<?php echo esc_html($rand_id); ?>', '', 'button_loader', thisObj);

                    is_processing = true;
                    $.ajax({
                        method: "POST",
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        dataType: "json",
                        data: {
                            action: "post_user_review",
                            ratings: JSON.stringify(ratings),
                            post_id: "<?php echo esc_html($post_id); ?>",
                            user_id: user_id,
                            company_id: "<?php echo esc_html($company_id); ?>",
                            order_id: "<?php echo esc_html($order_id); ?>",
                            restaurant_type_id: "<?php echo esc_html($restaurant_type_id); ?>",
                            user_email: user_email,
                            user_name: user_full_name,
                            review_title: review_title,
                            overall_rating: overall_rating,
                            description: review_description,
                            parent_review_id: parent_review_id,
                            'g-recaptcha-response': jQuery.data(document.body, 'recaptcha'),
                            security: "<?php echo wp_create_nonce('foodbakery-add-reviews'); ?>",
                        },
                        success: function (data) {
                            reviews_count = parseInt(reviews_count) + 1;
                            show_msg(data.msg, data.success, '<?php echo esc_html($order_id); ?>', '<?php echo esc_html($rand_id); ?>');

                            // Reset form.
                            if (data.success == true) {
                                is_processing = false;
                                if (jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_full_name").is(":enabled")) {
                                    jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_full_name").val('');
                                }
                                if (jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_email_address").is(":enabled")) {
                                    jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_email_address").val('');
                                }

                                jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_title").val('');
                                jQuery(".review-form-<?php echo esc_html($rand_id); ?> #review_description").val('');
                                jQuery(".review-form-<?php echo esc_html($rand_id); ?> .star-rating-list .stars").each(function (key, elem) {
                                    var css_class = jQuery(".review-form-<?php echo esc_html($rand_id); ?> input[name='star']:checked", jQuery(elem)).attr('class');
                                    if (css_class == undefined) {
                                        jQuery(".review-form-<?php echo esc_html($rand_id); ?> input[name='star']:eq(0)", jQuery(elem)).prop("checked", true);
                                        css_class = "star-1";
                                    }
                                    set_star_width_of_span(css_class, jQuery(elem).find("span"));
                                });
                                var elem = jQuery(".review-form-<?php echo esc_html($rand_id); ?> .rating-star input[name='star']:eq(0)").prop("checked", true);
                                set_star_width_of_span('star-1', elem);
                                is_review_added = true;
                                jQuery(".review-form-<?php echo esc_html($rand_id); ?> .btn-more-holder").show();
                                jQuery(".review-form-<?php echo esc_html($rand_id); ?> .reviwes-restaurant-holder").css("display", "block");
                                setTimeout(function () {
                                    jQuery('#order-review-<?php echo esc_html($order_id); ?> .close').click();
                                    setTimeout(function () {
                                        reload_order_tab(page_num);
                                    }, 700);
                                }, 800);

                                return false;
                            }
                            is_processing = false;
                        },
                    });
                });
                function reload_order_tab(page_num) {
                    foodbakery_show_loader('.loader-holder');
                    if (typeof (ajaxRequest) != 'undefined') {
                        ajaxRequest.abort();
                    }
                    ajaxRequest = jQuery.ajax({
                        type: "POST",
                        url: foodbakery_globals.ajax_url,
                        data: 'page_id_all=' + page_num + '&action=foodbakery_publisher_orders',
                        success: function (response) {
                            foodbakery_hide_loader();
                            jQuery('.user-holder').html(response);

                        }
                    });
                }
                function is_email_valid(email) {
                    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return re.test(email);
                }
                function show_msg(msg, status, order_id, rand_id) {


                    var type = status == true ? "success" : "error";
                    var response = {
                        type: type,
                        msg: msg
                    };
                    jQuery('#order-review-' + order_id + ' .modal-dialog .modal-content').mCustomScrollbar('scrollTo', 'top');
                    var thisObj = jQuery('.review-form-' + rand_id + ' .send_review_holder_' + rand_id + '');
                    foodbakery_show_response(response, '.add-new-review-' + rand_id + '', thisObj);
                }

                function set_star_width_of_span(css_class, elem) {
                    var span_width = "20%";
                    if (css_class == "star-1") {
                        span_width = "20%";
                    } else if (css_class == "star-2") {
                        span_width = "40%";
                    } else if (css_class == "star-3") {
                        span_width = "60%";
                    } else if (css_class == "star-4") {
                        span_width = "80%";
                    } else if (css_class == "star-5") {
                        span_width = "100%";
                    }
                    jQuery(elem).parent().find("span").css("width", span_width);
                }


            </script>
            <script type="text/javascript">
                (function ($) {
                    jQuery(function () {

                        /*
                         * Posting a Reply
                         */
                        var review_id = getParameterByName("review_id");
                        removeParam("review_id");

                        if (review_id != null) {
                            jQuery("#parent_review_id").val(review_id);
                            jQuery(".reviwes-restaurant-holder").css("display", "none");
                            jQuery(".add-new-review-holder").css("display", "block");
                            jQuery('html,body').animate({
                                scrollTop: jQuery(".add-new-review-holder").offset().top},
                                    'slow');

                        }


                        jQuery(".post-reviews-btn, .post-reviews-btn-detail").click(function () {
                            jQuery("#parent_review_id").val('');
                            jQuery(".reviwes-restaurant-holder").css("display", "none");
                            jQuery(".add-new-review-holder").css("display", "block");
                            jQuery(".post-reviews-btn-detail").css("display", "none");
                            return false;
                        });


                        jQuery(".close-post-new-reviews-btn").click(function () {
                            jQuery(".add-new-review-holder").css("display", "none");
                            jQuery(".reviwes-restaurant-holder").css("display", "block");
                            jQuery(".post-reviews-btn-detail").css("display", "block");
                            return false;
                        });
                        var reviews_count = <?php echo intval($reviews_count); ?>;
                        var reviews_shown_count = <?php echo count($reviews); ?>;
                        var start = posts_per_page;
                        if (reviews_shown_count < reviews_count && load_more_option == 'on') {

                            jQuery(".btn-load-more").click(function () {
                                $.ajax({
                                    method: "POST",
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    dataType: "json",
                                    data: {
                                        action: "get_user_reviews",
                                        post_id: "<?php echo esc_html($post_id); ?>",
                                        offset: start,
                                        sorty_by: jQuery(".slct-sort-by").val(),
                                        security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                    },
                                    success: function (data) {
                                        if (data.success == true) {
                                            jQuery("ul.review-restaurant").append(data.data);
                                            start = parseInt(start) + parseInt(posts_per_page);
                                        }
                                        if (start >= reviews_count) {
                                            jQuery(".btn-more-holder").hide();
                                        }
                                    },
                                });
                                return false;
                            });
                        } else {
                            jQuery(".btn-more-holder").hide();
                            if (reviews_shown_count < reviews_count) {
                                jQuery('#reviews-pagination').twbsPagination({
                                    totalPages: Math.ceil(reviews_count / posts_per_page),
                                    visiblePages: 3,
                                    first: "",
                                    last: "",
                                    onPageClick: function (event, page) {
                                        page--;
                                        reload_reviews(0, page * posts_per_page);
                                    }
                                });
                            }
                        }

                        jQuery(".ajax-loader-sorty-by").hide();
                        jQuery("input[name='review']").click(function () {
                            jQuery(".btn-more-holder").show();
                            reload_reviews();
                        });

                        function reload_reviews(reload_all_data, new_start) {
                            var reload_all_data = (typeof reload_all_data !== 'undefined') ? reload_all_data : 0;
                            start = (typeof new_start !== 'undefined') ? new_start : 0;

                            jQuery(".ajax-loader-sorty-by").show();
                            $.ajax({
                                method: "POST",
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                dataType: "json",
                                data: {
                                    action: "get_user_reviews",
                                    post_id: "<?php echo esc_html($post_id); ?>",
                                    offset: start,
                                    sort_by: jQuery("input[name='review']:checked").val(),
                                    all_data: reload_all_data,
                                    security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                },
                                success: function (data) {
                                    if (data.success == true) {
                                        jQuery("ul.review-restaurant li").remove();
                                        jQuery("ul.review-restaurant").append(data.data);
                                        if (data.ratings_summary_ui.length > 0) {
                                            jQuery(".ratings-summary-container").html(data.ratings_summary_ui);
                                        }
                                        if (data.overall_ratings_ui.length > 0) {
                                            jQuery(".overall-ratings-container").html(data.overall_ratings_ui);
                                        }
                                        start = parseInt(start) + parseInt(posts_per_page);
                                        //start += data.count;
                                        jQuery("#button").click(function () {
                                            jQuery('html, body').animate({
                                                scrollTop: jQuery(".review-restaurant").offset().top
                                            }, 1000);
                                        });
                                    }

                                    if (start >= reviews_count) {
                                        jQuery(".btn-more-holder").hide();
                                    }
                                    jQuery(".ajax-loader-sorty-by").hide();
                                },
                            });
                        }


                        jQuery(".review-form-<?php echo esc_html($rand_id); ?> .star-rating-list .stars").each(function (key, elem) {

                            var css_class = jQuery(".review-form-<?php echo esc_html($rand_id); ?> input[name='star']:checked", jQuery(elem)).attr('class');
                            if (css_class == undefined) {
                                jQuery(".review-form-<?php echo esc_html($rand_id); ?> input[name='star']:eq(0)", jQuery(elem)).prop("checked", true);
                                css_class = "star-1";
                            }
                            set_width_of_span(css_class, jQuery(elem).find("span"));

                            jQuery(".review-form-<?php echo esc_html($rand_id); ?> label", jQuery(elem)).click(function (e) {
                                e.preventDefault();
                                var css_class = jQuery(this).attr("class");
                                jQuery(".review-form-<?php echo esc_html($rand_id); ?> input." + css_class, jQuery(this).parent()).prop("checked", true);
                                var parts = css_class.split("-");
                                jQuery(this).parent().parent().data("selected-rating", parts[1]);
                            });
                        });



                        function set_width_of_span(css_class, elem) {
                            var span_width = "20%";
                            if (css_class == "star-1") {
                                span_width = "20%";
                            } else if (css_class == "star-2") {
                                span_width = "40%";
                            } else if (css_class == "star-3") {
                                span_width = "60%";
                            } else if (css_class == "star-4") {
                                span_width = "80%";
                            } else if (css_class == "star-5") {
                                span_width = "100%";
                            }

                            jQuery(elem).parent().find("span").css("width", span_width);
                        }

                        function getParameterByName(name) {
                            var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
                            return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
                        }
                        function removeParam(parameter) {
                            var url = document.location.href;
                            var urlparts = url.split('?');

                            if (urlparts.length >= 2)
                            {
                                var urlBase = urlparts.shift();
                                var queryString = urlparts.join("?");

                                var prefix = encodeURIComponent(parameter) + '=';
                                var pars = queryString.split(/[&;]/g);
                                for (var i = pars.length; i-- > 0; )
                                    if (pars[i].lastIndexOf(prefix, 0) !== -1)
                                        pars.splice(i, 1);
                                url = urlBase + '?' + pars.join('&');
                                window.history.pushState('', document.title, url); // added this line to push the new url directly to url bar .

                            }
                            return url;
                        }
                    });
                })(jQuery);


                // Characters Counter with limit
                jQuery(document).ready(function () {
                    var text_max = <?php echo intval($foodbakery_review_max_length); ?>;
                    jQuery('.review-form-<?php echo esc_html($rand_id); ?> #textarea_feedback').html(text_max + ' characters remaining');

                    jQuery('.review-form-<?php echo esc_html($rand_id); ?> #review_description').keyup(function () {
                        var text_length = jQuery('.review-form-<?php echo esc_html($rand_id); ?> #review_description').val().length;
                        var text_remaining = text_max - text_length;

                        jQuery('.review-form-<?php echo esc_html($rand_id); ?> #textarea_feedback').html(text_remaining + ' characters remaining');
                    });
                });


            </script>
            <?php
        }

        /**
         * Output UI for reviews restaurant and add new review for details page of a post.
         *
         * @param type $post_id
         */
        public function reviews_ui_callback($post_id, $show_ratings_div = 'yes', $show_review_form = 'yes') {
            global $foodbakery_plugin_options;
            $rand_id = rand(100, 9000);
            $show_ratings = $this->enable_comments($post_id);
            $is_reviews_enabled = 'off';
            $is_reviews_without_login = 'off';
            $restaurant_type = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
            $the_slug = $restaurant_type;
            $args = array(
                'name' => $the_slug,
                'post_type' => 'restaurant-type',
                'post_status' => 'publish',
                'numberposts' => 1
            );
            $restaurant_types = get_posts($args);
            // If no restaurant type found then skip reviews section.
            if (1 > count($restaurant_types)) {
                return;
            }
            $reviews_count = 0;
            $ratings_summary = array();
            $overall_ratings = array(
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0,
            );
            $restaurant_type_id = $restaurant_types[0]->ID;
            $is_reviews_enabled = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);
            $is_reviews_enabled = ( $is_reviews_enabled == '' ? 'off' : $is_reviews_enabled );
            if ($is_reviews_enabled == 'off') {
                return;
            }
            $is_reviews_without_login = get_post_meta($restaurant_type_id, 'foodbakery_review_without_login', true);
            $is_reviews_without_login = ( $is_reviews_without_login == '' ? 'off' : $is_reviews_without_login );

            $is_review_response_enable = get_post_meta($post_id, 'foodbakery_transaction_restaurant_ror', true);
            $is_review_response_enable = ( isset($is_review_response_enable) && $is_review_response_enable == 'on' ) ? true : false;

            $foodbakery_reviews_labels = get_post_meta($restaurant_type_id, 'foodbakery_reviews_labels', true);
            $foodbakery_reviews_labels = ( $foodbakery_reviews_labels == '' ? array() : json_decode($foodbakery_reviews_labels, true) );
            $foodbakery_review_min_length = get_post_meta($restaurant_type_id, 'foodbakery_review_min_length', true);
            $foodbakery_review_min_length = ( $foodbakery_review_min_length == '' ? 10 : $foodbakery_review_min_length );
            $foodbakery_review_max_length = get_post_meta($restaurant_type_id, 'foodbakery_review_max_length', true);
            $foodbakery_review_max_length = ( $foodbakery_review_max_length == '' ? 200 : $foodbakery_review_max_length );
            $foodbakery_review_number_of_reviews = get_post_meta($restaurant_type_id, 'foodbakery_review_number_of_reviews', true);
            $foodbakery_review_number_of_reviews = ( $foodbakery_review_number_of_reviews == '' ? 10 : $foodbakery_review_number_of_reviews );
            Foodbakery_Reviews::$posts_per_page = $foodbakery_review_number_of_reviews;
            $foodbakery_review_load_more_option = get_post_meta($restaurant_type_id, 'foodbakery_review_load_more_option', true);
            $foodbakery_review_load_more_option = ( $foodbakery_review_load_more_option == '' ? 'off' : $foodbakery_review_load_more_option );
            $foodbakery_review_captcha_for_reviews = get_post_meta($restaurant_type_id, 'foodbakery_review_captcha_for_reviews', true);
            $foodbakery_review_captcha_for_reviews = ( $foodbakery_review_captcha_for_reviews == '' ? 'off' : $foodbakery_review_captcha_for_reviews );

            // Get all reviews for this post.

            $reviews = $this->get_user_reviews_for_post($post_id, 0, Foodbakery_Reviews::$posts_per_page);
            $reviews = array_filter($reviews);

            // Get existing ratings for this post.
            $existing_ratings_data = get_post_meta($post_id, 'foodbakery_ratings', true);

            if ('' != $existing_ratings_data && 0 < count($reviews)) {
                $reviews_count = $existing_ratings_data['reviews_count'];
                $existing_ratings = $existing_ratings_data['ratings'];
                foreach ($foodbakery_reviews_labels as $key => $val) {
                    if (isset($existing_ratings[$val])) {
                        $value = $existing_ratings[$val];
                    } else {
                        $value = 0;
                    }
                    $ratings_summary[] = array('label' => $val, 'value' => $value);
                }
                $existing_overall_ratings = $existing_ratings_data['overall_rating'];
                foreach ($existing_overall_ratings as $key => $val) {
                    if (isset($overall_ratings[$key])) {
                        $overall_ratings[$key] = $val;
                    }
                }
            } else {
                foreach ($foodbakery_reviews_labels as $key => $val) {
                    $ratings_summary[] = array('label' => $val, 'value' => 0);
                }
                $reviews = array();
            }
            $user_id = 0;
            $company_id = 0;
            $user_email = '';
            $user_full_name = '';
            $current_user = wp_get_current_user();
            if (0 < $current_user->ID) {
                $user_id = $current_user->ID;
                $user_full_name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
                $company_id = get_user_meta($user_id, 'foodbakery_company', true);
                $user_email = get_post_meta($company_id, 'foodbakery_email_address', true);
                if (!isset($user_email) || $user_email == '') {
                    $user_email = $current_user->user_email;
                }
            }

            $publisher_display_name = '';

            // If company id is 0 it means this review is without login requirement.
            $have_review_added = false;
            $is_user_post_owner = false;
            if (0 < $company_id) {
                $have_review_added = apply_filters('have_user_added_review_for_this_post', $have_review_added, $company_id, $post_id);
                $is_user_post_owner = $this->is_this_user_owner_of_this_post_callback(false, $company_id, $post_id);
                $publisher_display_name = get_the_title($company_id);
            } else if ('' != $user_email) {
                $have_review_added = $this->have_user_added_review_for_this_post_callback(false, $user_email, $post_id, true);
            }

            if ($is_user_post_owner == true) {
                $have_review_added = false;
            }

            $existing_ratings = get_post_meta($post_id, 'foodbakery_ratings', true);
            ?>
            <div class="reviews-holder">
                <?php
                if ($show_ratings_div == 'no') {
                    echo '<button type="button" class="bgcolor post-reviews-btn-detail  discussion-submit">' . esc_html__('Post new reviews', 'foodbakery') . '</button>';
                }
                ?>
                <div class="add-new-review-holder">
                    <div class="row">

                        <!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                            <div class="elements-title">
                                <?php
                        /*                                $show_stars = Foodbakery_Reviews::enable_comments($post_id);
                                                        if ($show_stars == 'on') {
                                                            */?>
                                    <h3><?php /*echo foodbakery_plugin_text_srt('foodbakery_reviews_rate_and_write_a_review_label'); */?></h3>
                                <?php /*} else {
                                    */?>
                                    <h3><?php /*echo foodbakery_plugin_text_srt('foodbakery_reviews_rate_and_write_a_comment_label'); */?></h3>
                                    <?php
                        /*                                }
                                                        */?>
                                <a href="#" class="close-post-new-reviews-btn"><?php /*echo foodbakery_plugin_text_srt('foodbakery_reviews_add_new_reviews_close_button'); */?></a>
                            </div>
                        </div>-->

                        <?php if ($have_review_added == true) : ?>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review_msg'); ?>
                            </div>

                        <?php elseif (( is_user_logged_in() ) && true !== Foodbakery_Member_Permissions::check_permissions('reviews')) : ?>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_post_notallowed_review_msg'); ?>
                            </div>
                        <?php elseif ((!is_user_logged_in() ) && $is_reviews_without_login == 'off') : ?>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                <?php echo foodbakery_plugin_text_srt('foodbakery_reviews_withtout_login_msg'); ?>
                            </div>
                            <!--<?php /*else : */?>
                            <div class="foodbakery-add-review-data">
                                <div class="foodbakery-added-review-string" style="display:none;">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                        <?php /*echo foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review_msg'); */?>
                                    </div>
                                </div>
                                <?php /**/?>
                                <?php
/*                                if ($show_ratings == 'on') {
                                    if ($is_user_post_owner != true) {
                                        if ($show_ratings_div == 'yes') {
                                            */?>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="rating-restaurant">
                                                    <ul class="star-rating-list">
                                                        <?php /*foreach ($ratings_summary as $key => $rating): */?>
                                                            <li class="rating_summary_item" id="<?php /*echo str_replace(' ', '-', strtolower(esc_html($rating['label']))); */?>" data-selected-rating="1" data-label="<?php /*echo esc_html($rating['label']); */?>">
                                                                <span><?php /*echo esc_html($rating['label']); */?></span>
                                                                <div class="stars">
                                                                    <input type="radio" name="star<?php /*echo esc_html($key); */?>" class="star-1" checked="checked">
                                                                    <label class="star-1" for="star-1">1</label>
                                                                    <input type="radio" name="star<?php /*echo esc_html($key); */?>" class="star-2">
                                                                    <label class="star-2" for="star-2">2</label>
                                                                    <input type="radio" name="star<?php /*echo esc_html($key); */?>" class="star-3">
                                                                    <label class="star-3" for="star-3">3</label>
                                                                    <input type="radio" name="star<?php /*echo esc_html($key); */?>" class="star-4">
                                                                    <label class="star-4" for="star-4">4</label>
                                                                    <input type="radio" name="star<?php /*echo esc_html($key); */?>" class="star-5">
                                                                    <label class="star-5" for="star-5">5</label>
                                                                    <span style="width: 20%;"></span>
                                                                </div>

                                                            </li>
                                                        <?php /*endforeach; */?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php /*} */?>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="total-rating user-rating-container overall-rating" data-overall-rating="1">
                                                <h4><?php /*echo foodbakery_plugin_text_srt('foodbakery_reviews_overall_rating_label'); */?></h4>
                                                <div class="rating-star">
                                                    <input type="radio" name="star" class="star-1" checked="checked">
                                                    <label class="star-1" for="star-1">1</label>
                                                    <input type="radio" name="star" class="star-2">
                                                    <label class="star-2" for="star-2">2</label>
                                                    <input type="radio" name="star" class="star-3">
                                                    <label class="star-3" for="star-3">3</label>
                                                    <input type="radio" name="star" class="star-4">
                                                    <label class="star-4" for="star-4">4</label>
                                                    <input type="radio" name="star" class="star-5">
                                                    <label class="star-5" for="star-5">5</label>
                                                    <span style="width: 20%;"></span>
                                                </div>

                                            </div>
                                        </div> 


                                        <?php
/*                                    }
                                }
                                */?>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-element">
                                        <i class="icon-edit2"></i>
                                        <?php
/*                                        if ($show_ratings == 'on') {
                                            */?>

                                            <input type="text" placeholder="<?php /*echo esc_html__('Title of your review *', 'foodbakery'); */?>" name="review_title" id="review_title" value="">
                                            <?php
/*                                        } else {
                                            */?>
                                            <input type="text" placeholder="<?php /*echo esc_html__('Title of your Comment *', 'foodbakery'); */?>" name="review_title" id="review_title" value="">
                                            <?php
/*                                        }
                                        */?>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-element">
                                        <i class="icon-user4"></i>
                                        <input type="text" placeholder="<?php /*echo esc_html__('Name *', 'foodbakery'); */?>" name="review_full_name" id="review_full_name" value="<?php /*echo esc_html($publisher_display_name); */?>" <?php /*echo esc_html($publisher_display_name) != '' ? 'disabled="disabled"' : ''; */?>>
                                        <input type="hidden" name="review_user_id" id="review_user_id" value="<?php /*echo esc_html($user_id); */?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-element">
                                        <i class="icon-envelope3"></i>
                                        <input type="text" placeholder="<?php /*echo esc_html__('Email *', 'foodbakery'); */?>" name="review_email_address" id="review_email_address" value="<?php /*echo esc_html($user_email); */?>" <?php /*echo esc_html($user_email) != '' ? 'disabled="disabled"' : ''; */?>>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-element">
                                        <i class="icon-message"></i>
                                        <textarea placeholder="<?php /*echo esc_html__('Tell about your experience or leave a tip for others', 'foodbakery'); */?>" cols="30" rows="10" name="review_description" id="review_description" maxlength="<?php /*echo intval($foodbakery_review_max_length); */?>"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-element message-length">
                                        <span><?php /*echo esc_html__('Min characters:', 'foodbakery'); */?> <?php /*echo intval($foodbakery_review_min_length); */?></span>
                                        <span><?php /*echo esc_html__('Max characters:', 'foodbakery'); */?> <?php /*echo intval($foodbakery_review_max_length); */?></span>
                                        <div id="textarea_feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-element">
                                        <div class="review-reply-button send_review_holder_<?php /*echo esc_html($rand_id); */?> input-button-loader">
                                            <input type="hidden" id="parent_review_id" name="parent_review_id" value="">
                                            <?php
/*                                            if ($show_ratings == 'on') {
                                                */?>

                                                <input type="button" name="send_your_review" id="send_your_review_reply<?php /*echo esc_html($rand_id); */?>" value="<?php /*echo foodbakery_plugin_text_srt('foodbakery_reviews_send_your_review_btn'); */?>">
                                                <?php
/*                                            } else {
                                                */?>

                                                <input type="button" name="send_your_review" id="send_your_review_reply<?php /*echo esc_html($rand_id); */?>" value="<?php /*echo foodbakery_plugin_text_srt('foodbakery_reviews_send_your_comment_btn'); */?>">
                                            <?php /*} */?>
                                        </div>
                                        &nbsp;&nbsp;<span class="ajax-message"></span>

                                    </div>
                                </div>

                            </div>-->
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($show_ratings_div == 'yes') {
                    ?>

                    <div class="reviwes-restaurant-holder">

                        <?php
                        if ($show_review_form == 'yes') {
                            if ($is_user_post_owner != true) {
                                ?>
                                <div class="section-title">
                                    <?php
                                    if ($show_ratings == 'on') {
                                        ?>
                                        <h2><?php echo sprintf(foodbakery_plugin_text_srt('foodbakery_reviews_total_reviews_label'), $reviews_count); ?></h2>

                                        <a href="#" class="post-reviews-btn"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_post_new_reviews_button'); ?></a>
                                        <?php
                                    } else {
                                        ?>
                                        <h2><?php echo sprintf(foodbakery_plugin_text_srt('foodbakery_reviews_total_comments_label'), $reviews_count); ?></h2>
                                        <a href="#" class="post-reviews-btn"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_post_new_reviews_comments'); ?></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>

                        <?php
                        if ($show_ratings == 'on' && 0 < count($reviews)) {
                            ?>
                            <div class="over-all-rating-holder">
                                <div class="overall-ratings-container">
                                    <?php $this->get_overall_rating_ui($post_id); ?>
                                </div>
                                <div class="ratings-summary-container">
                                    <?php $this->get_ratings_summary_ui($ratings_summary, $reviews_count); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="review-listing">
                            <div class="elements-title">
                                <?php
                                if ($show_ratings == 'on') {
                                    $ratings_data = array(
                                        'overall_rating' => 0.0,
                                        'count' => 0,
                                    );
                                    $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, get_the_id());
                                    ?>

                                    <h5><?php
                                        echo foodbakery_plugin_text_srt('foodbakery_reviews_all_reviews_heading') . ' ' . get_the_title($post_id);
                                        if ($ratings_data['count'] > 0) {
                                            ?> <span><?php echo esc_html($ratings_data['count']); ?></span><?php } ?></h5>
                                    <?php
                                } else {
                                    ?>

                                    <h5><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_all_comments_heading'); ?></h5>
                                    <?php
                                }


                                if ($show_ratings == 'on') {
                                    if ($ratings_data['count'] > 0) {
                                        $sort_by = (isset($_POST['sort_by']) && $_POST['sort_by'] != 'undefined' ) ? $_POST['sort_by'] : 'newest';
                                        $sort_by_options = array(
                                            'newest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_newest_reviews_option'),
                                            'highest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_highest_rating_option'),
                                            'lowest' => foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_sort_by_lowest_rating_option'),
                                        );
                                        ?>
                                        <div class="sort-by">
                                            <span class="ajax-loader-sorty-by"><img src="<?php echo wp_foodbakery::plugin_url(); ?>assets/frontend/images/ajax-loader.gif" alt="" /></span>
                                            <ul class="reviews-sortby">
                                                <li> 
                                                    <span class="active-sort"><?php esc_html_e($sort_by_options[$sort_by]); ?></span>
                                                    <div class="reviews-sort-dropdown">
                                                        <form>
                                                            <div class="input-reviews">
                                                                <?php
                                                                $i = 1;
                                                                foreach ($sort_by_options as $key => $sort_by_option) {
                                                                    ?>
                                                                    <div class="radio-field">
                                                                        <input name="review" id="check-<?php echo intval($i); ?>" type="radio" value="<?php echo esc_html($key); ?>" <?php echo ( $key == $sort_by ) ? 'checked="checked"' : ''; ?>>
                                                                        <label for="check-<?php echo intval($i); ?>"><?php esc_html_e($sort_by_options[$key]); ?></label>
                                                                    </div>
                                                                    <?php
                                                                    $i ++;
                                                                }
                                                                ?>			
                                                            </div>
                                                        </form>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>

                            <ul class="review-restaurant">
                                <?php if (0 < count($reviews)) : ?>
                                    <?php foreach ($reviews as $key => $review) : ?>
                                        <?php if (!empty($review)) { ?>
                                            <?php $reply_class = ( isset($review['is_reply']) && $review['is_reply'] == true ) ? 'review_reply' : ''; ?>
                                            <?php $review_title = ( isset($review['review_title']) && $review['review_title'] != '' ) ? ' ' . $review['review_title'] : ''; ?>
                                            <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo esc_html($reply_class); ?>">
                                                <div class="list-holder ">

                                                    <div class="review-text">
                                                        <div class="review-title">
                                                            <h6>
                                                                <?php
                                                                $review_id = $review['id'];
                                                                if ($reply_class) {
                                                                    $post_slug = get_post_meta($review_id, 'post_id', true);
                                                                    $args = array(
                                                                        'name' => $post_slug,
                                                                        'post_type' => 'restaurants',
                                                                    );
                                                                    $posts = get_posts($args);
                                                                    $post_title = isset($posts[0]->post_title) ? $posts[0]->post_title : '';
                                                                    esc_html_e("Restaurant Owner: ", 'foodbakery');
                                                                } else {
                                                                    $user_id = get_post_meta($review_id, 'user_id', true);
                                                                    $user_info = get_userdata($user_id);
                                                                    $user_name = get_user_info_array($user_id);
                                                                    echo esc_html($user_name['first_name'] . ' ' . $user_name['last_name']) . ': ';
                                                                }
                                                                ?>
                                                                <?php echo esc_html($review_title); ?>
                                                            </h6>
                                                            <?php
                                                            if ($show_ratings == 'on') {
                                                                ?>
                                                                <div class="rating-holder">

                                                                    <?php if (isset($review['is_reply']) && $review['is_reply'] != true) { ?>
                                                                        <div class="rating-star">
                                                                            <span style="width: <?php echo ( $review['overall_rating'] / 5 ) * 100; ?>%;" class="rating-box"></span>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <em class="review-date"><?php echo date('j M Y', strtotime($review['dated'])); ?></em>
                                                        <p>
                                                            <?php echo esc_html($review['description']); ?>
                                                        </p>
                                                    </div>
                                                    <?php
                                                    if ($is_review_response_enable == true && $is_user_post_owner == true) {
                                                        echo force_balance_tags($this->posting_review_reply($review));
                                                    }
                                                    ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="reviews-alert">
                                            <div class="media-holder">
                                                <img src="<?php echo wp_foodbakery::plugin_url(); ?>assets/frontend/images/icon-review.png" alt="<?php echo esc_html__('review not found', 'foodbakery'); ?>" />
                                            </div>
                                            <div class="text-holder">
                                                <strong ><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_no_reviews_text_strong'); ?></strong>
                                                <span class="text-color" ><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_no_reviews_text'); ?></span>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>


                        </div>
                        <?php if ($foodbakery_review_load_more_option == 'on' && $reviews_count > Foodbakery_Reviews::$posts_per_page) : ?>

                            <div class="btn-more-holder">
                                <?php
                                if ($show_ratings == 'on') {
                                    ?>
                                    <a href="javascript:void(0);" class="btn-load-more"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_read_more_reviews_text'); ?></a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="javascript:void(0);" class="btn-load-more"><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_read_more_comments_text'); ?></a>
                                    <?php
                                }
                                ?>
                            </div>

                        <?php else: ?>
                            <ul id="reviews-pagination" class="pagination-sm"></ul>
                        <?php endif; ?>
                    </div>
                <?php } ?>
            </div>
            <script type="text/javascript">
                function getParameterByName(name) {
                    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
                    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
                }
                jQuery(document).on("click", ".stars label", function () {
                    var $ = jQuery;
                    var array = new Array();
                    var checked = 0;
                    var unchecked = 0;
                    jQuery('.stars input:radio:checked').each(function (index) {
                        var className = jQuery(this).attr('class');


                        var starValue = jQuery(this).next(' label').text();
                        checked += parseInt(starValue);

                    });

                    var number_of_items = jQuery('.star-rating-list li').length;
                    var over_all_value = checked / number_of_items;
                    var over_all_value_rounded = Math.round(over_all_value);




                    var span_width = "20%";
                    if (over_all_value_rounded == 1) {
                        span_width = "20%";
                    } else if (over_all_value_rounded == 2) {
                        span_width = "40%";
                    } else if (over_all_value_rounded == 3) {
                        span_width = "60%";
                    } else if (over_all_value_rounded == 4) {
                        span_width = "80%";
                    } else if (over_all_value_rounded == 5) {
                        span_width = "100%";
                    }
                    jQuery(".rating-star span").css("width", span_width);


                });
                function removeParam(parameter) {
                    var url = document.location.href;
                    var urlparts = url.split('?');

                    if (urlparts.length >= 2)
                    {
                        var urlBase = urlparts.shift();
                        var queryString = urlparts.join("?");

                        var prefix = encodeURIComponent(parameter) + '=';
                        var pars = queryString.split(/[&;]/g);
                        for (var i = pars.length; i-- > 0; )
                            if (pars[i].lastIndexOf(prefix, 0) !== -1)
                                pars.splice(i, 1);
                        url = urlBase + '?' + pars.join('&');
                        window.history.pushState('', document.title, url); // added this line to push the new url directly to url bar .

                    }
                    return url;
                }

                (function ($) {

                    jQuery(function () {
                        var is_review_added = false;
                        var is_processing = false;
                        var review_min_length = "<?php echo intval($foodbakery_review_min_length); ?>";
                        var review_max_length = "<?php echo intval($foodbakery_review_max_length); ?>";
                        var posts_per_page = "<?php echo intval($foodbakery_review_number_of_reviews); ?>";
                        var load_more_option = "<?php echo ($foodbakery_review_load_more_option); ?>";
                        jQuery("#send_your_review_reply<?php echo esc_html($rand_id); ?>").click(function () {

                            var thisObj = jQuery('.send_review_holder_<?php echo esc_html($rand_id); ?>');
                            foodbakery_show_loader('.send_review_holder_<?php echo esc_html($rand_id); ?>', '', 'button_loader', thisObj);

                            if (is_processing == true) {
                                return false;
                            }
                            if (is_review_added == true) {
                                show_msg(<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_already_added_review1_msg'); ?>, false);
                                return false;
                            }
                            var ratings = {};
                            jQuery(".add-new-review-holder .rating_summary_item").each(function (key, elem) {
                                rating = jQuery(elem).data('selected-rating');
                                label = jQuery(elem).data('label');
                                ratings[label] = rating;
                            });
                            var user_id = jQuery("#review_user_id").val();

                            var review_title = jQuery("#review_title").val();
                            if (review_title.length == 0) {
                                show_msg("<?php echo esc_html__('Please provide title of your review.', 'foodbakery') ?>", false);
                                return false;
                            }
                            if (review_title.length < 3) {
                                show_msg("<?php echo esc_html__('Title length must be 3 to long.', 'foodbakery') ?>", false);
                                return false;
                            }

                            var user_email = jQuery("#review_email_address").val();
                            if (is_email_valid(user_email) == false) {
                                show_msg('Please provide valid email address.', false);
                                return false;
                            }
                            var user_full_name = jQuery("#review_full_name").val();
                            if (user_full_name.length < 3) {
                                show_msg("<?php echo esc_html__('Please provide full name.', 'foodbakery') ?>", false);
                                return false;
                            }
                            var parent_review_id = jQuery("#parent_review_id").val();
                            var review_description = jQuery("#review_description").val();
                            if (review_description.length == 0) {
                                show_msg("<?php echo esc_html__('Please provide description of your review.', 'foodbakery') ?>", false);
                                return false;
                            }
                            if (review_description.length < review_min_length || review_description.length > review_max_length) {
                                show_msg('Description length must be ' + review_min_length + ' to ' + review_max_length + ' long.', false);
                                return false;
                            }
                            var overall_rating = jQuery(".overall-rating").data('overall-rating');
                            //jQuery(".ajax-message").text("<?php echo foodbakery_plugin_text_srt('foodbakery_reviews_request_processing_text'); ?>").css("color", "#555555");
                            is_processing = true;

                            jQuery.ajax({
                                method: "POST",
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                dataType: "json",
                                data: {
                                    action: "post_user_review",
                                    ratings: JSON.stringify(ratings),
                                    post_id: "<?php echo esc_html($post_id); ?>",
                                    user_id: user_id,
                                    company_id: "<?php echo esc_html($company_id); ?>",
                                    restaurant_type_id: "<?php echo esc_html($restaurant_type_id); ?>",
                                    user_email: user_email,
                                    user_name: user_full_name,
                                    review_title: review_title,
                                    overall_rating: overall_rating,
                                    description: review_description,
                                    parent_review_id: parent_review_id,
                                    'g-recaptcha-response': jQuery.data(document.body, 'recaptcha'),
                                    security: "<?php echo wp_create_nonce('foodbakery-add-reviews'); ?>",
                                },
                                success: function (data) {
                                    reviews_count = parseInt(reviews_count) + 1;
                                    show_msg(data.msg, data.success);
                                    jQuery(".post-reviews-btn-detail").css("display", "block");
                                    // Reset form.
                                    if (data.success == true) {
                                        if (jQuery("#review_full_name").is(":enabled")) {
                                            jQuery("#review_full_name").val('');
                                        }
                                        if (jQuery("#review_email_address").is(":enabled")) {
                                            jQuery("#review_email_address").val('');
                                        }
                                        jQuery("#review_description").val('');
                                        jQuery(".star-rating-list .stars").each(function (key, elem) {
                                            var css_class = jQuery("input[name='star']:checked", jQuery(elem)).attr('class');
                                            if (css_class == undefined) {
                                                jQuery("input[name='star']:eq(0)", jQuery(elem)).prop("checked", true);
                                                css_class = "star-1";
                                            }
                                            set_width_of_span(css_class, jQuery(elem).find("span"));
                                        });
                                        var elem = jQuery(".rating-star input[name='star']:eq(0)").prop("checked", true);
                                        set_width_of_span('star-1', elem);
                                        is_review_added = true;
                                        jQuery(".btn-more-holder").show();
                                        reload_reviews(1);
                                        jQuery(".add-new-review-holder").css("display", "none");
                                        jQuery(".reviwes-restaurant-holder").css("display", "block");
                                        var review_added_string = jQuery(".foodbakery-added-review-string").html();
                                        jQuery(".foodbakery-add-review-data").html(review_added_string);
                                        return false;
                                    }
                                    jQuery(".post-reviews-btn-detail").css("display", "block");
                                    is_processing = false;
                                },
                            });
                        });
                        function is_email_valid(email) {
                            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                            return re.test(email);
                        }
                        function show_msg(msg, status) {

                            var type = status == true ? "success" : "error";
                            var response = {
                                type: type,
                                msg: msg
                            };

                            var thisObj = jQuery('.send_review_holder_<?php echo esc_html($rand_id); ?>');
                            foodbakery_show_response(response, '', thisObj);
                            return false;
                        }

                        jQuery(".add-new-review-holder").css("display", "none");

                        /*
                         * Posting a Reply
                         */
                        var review_id = getParameterByName("review_id");
                        removeParam("review_id");

                        if (review_id != null) {
                            jQuery("#parent_review_id").val(review_id);
                            jQuery(".reviwes-restaurant-holder").css("display", "none");
                            jQuery(".add-new-review-holder").css("display", "block");
                            jQuery('html,body').animate({
                                scrollTop: jQuery(".add-new-review-holder").offset().top},
                                    'slow');

                        }


                        jQuery(".post-reviews-btn, .post-reviews-btn-detail").click(function () {
                            jQuery("#parent_review_id").val('');
                            jQuery(".reviwes-restaurant-holder").css("display", "none");
                            jQuery(".add-new-review-holder").css("display", "block");
                            jQuery(".post-reviews-btn-detail").css("display", "none");
                            return false;
                        });


                        jQuery(".close-post-new-reviews-btn").click(function () {
                            jQuery(".add-new-review-holder").css("display", "none");
                            jQuery(".reviwes-restaurant-holder").css("display", "block");
                            jQuery(".post-reviews-btn-detail").css("display", "block");
                            return false;
                        });
                        var reviews_count = <?php echo intval($reviews_count); ?>;
                        var reviews_shown_count = <?php echo Foodbakery_Reviews::$posts_per_page; ?>;
                        var start = posts_per_page;
                        if (reviews_shown_count < reviews_count && load_more_option == 'on') {

                            jQuery(".btn-load-more").click(function () {
                                $.ajax({
                                    method: "POST",
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    dataType: "json",
                                    data: {
                                        action: "get_user_reviews",
                                        post_id: "<?php echo esc_html($post_id); ?>",
                                        offset: start,
                                        sorty_by: jQuery("input[name='review']:checked").val(),
                                        security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                    },
                                    success: function (data) {
                                        if (data.success == true) {
                                            jQuery("ul.review-restaurant").append(data.data);
                                            start = parseInt(start) + parseInt(posts_per_page);
                                        }
                                        if (start >= reviews_count) {
                                            jQuery(".btn-more-holder").hide();
                                        }
                                    },
                                });
                                return false;
                            });
                        } else {
                            jQuery(".btn-more-holder").hide();
                            if (reviews_shown_count < reviews_count) {
                                jQuery('#reviews-pagination').twbsPagination({
                                    totalPages: Math.ceil(reviews_count / posts_per_page),
                                    visiblePages: 3,
                                    first: "",
                                    last: "",
                                    onPageClick: function (event, page) {
                                        page--;
                                        reload_reviews(0, page * posts_per_page);
                                    }
                                });
                            }
                        }

                        jQuery(".ajax-loader-sorty-by").hide();
                        jQuery("input[name='review']").click(function () {
                            jQuery(".btn-more-holder").show();
                            reload_reviews();
                        });

                        function reload_reviews(reload_all_data, new_start) {
                            var reload_all_data = (typeof reload_all_data !== 'undefined') ? reload_all_data : 0;
                            start = (typeof new_start !== 'undefined') ? new_start : 0;

                            jQuery(".ajax-loader-sorty-by").show();
                            $.ajax({
                                method: "POST",
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                dataType: "json",
                                data: {
                                    action: "get_user_reviews",
                                    post_id: "<?php echo esc_html($post_id); ?>",
                                    offset: start,
                                    sort_by: jQuery("input[name='review']:checked").val(),
                                    all_data: reload_all_data,
                                    security: "<?php echo wp_create_nonce('foodbakery-get-reviews'); ?>",
                                },
                                success: function (data) {
                                    if (data.success == true) {
                                        jQuery("ul.review-restaurant li").remove();
                                        jQuery("ul.review-restaurant").append(data.data);
                                        if (data.ratings_summary_ui.length > 0) {
                                            jQuery(".ratings-summary-container").html(data.ratings_summary_ui);
                                        }
                                        if (data.overall_ratings_ui.length > 0) {
                                            jQuery(".overall-ratings-container").html(data.overall_ratings_ui);
                                        }
                                        start = parseInt(start) + parseInt(posts_per_page);
                                        //start += data.count;
                                        jQuery("#button").click(function () {
                                            jQuery('html, body').animate({
                                                scrollTop: jQuery(".review-restaurant").offset().top
                                            }, 1000);
                                        });
                                    }

                                    if (start >= reviews_count) {
                                        jQuery(".btn-more-holder").hide();
                                    }
                                    jQuery(".ajax-loader-sorty-by").hide();
                                },
                            });
                        }


                        jQuery(".star-rating-list .stars").each(function (key, elem) {
                            var css_class = jQuery("input[name='star']:checked", jQuery(elem)).attr('class');
                            if (css_class == undefined) {
                                jQuery("input[name='star']:eq(0)", jQuery(elem)).prop("checked", true);
                                css_class = "star-1";
                            }
                            set_width_of_span(css_class, jQuery(elem).find("span"));

                            jQuery("label", jQuery(elem)).click(function (e) {
                                e.preventDefault();
                                var css_class = jQuery(this).attr("class");
                                jQuery("input." + css_class, jQuery(this).parent()).prop("checked", true);
                                var parts = css_class.split("-");
                                jQuery(this).parent().parent().data("selected-rating", parts[1]);
                            });
                        });

                        // For overall ratings.
                        var elem = jQuery(".rating-star input[name='star']:eq(0)").prop("checked", true);
                        set_width_of_span('star-1', elem);
                        jQuery(".rating-star label").click(function (e) {
                            e.preventDefault();
                            var css_class = jQuery(this).attr("class");
                            jQuery("input." + css_class, jQuery(this).parent()).prop("checked", true);
                            var parts = css_class.split("-");
                            jQuery(this).parent().parent().data("overall-rating", parts[1]);
                        });

                        jQuery(".star-rating-list .stars label").hover(
                                function () {
                                    var css_class = jQuery(this).attr('class');

                                    set_width_of_span(css_class, this);
                                },
                                function () {
                                    var css_class = jQuery("input[name^='star']:checked", jQuery(this).parent()).attr('class');
                                    set_width_of_span(css_class, this);
                                }
                        );

                        function set_width_of_span(css_class, elem) {
                            var span_width = "20%";
                            if (css_class == "star-1") {
                                span_width = "20%";
                            } else if (css_class == "star-2") {
                                span_width = "40%";
                            } else if (css_class == "star-3") {
                                span_width = "60%";
                            } else if (css_class == "star-4") {
                                span_width = "80%";
                            } else if (css_class == "star-5") {
                                span_width = "100%";
                            }

                            jQuery(elem).parent().find("span").css("width", span_width);
                        }
                    });
                })(jQuery);


                // Characters Counter with limit
                jQuery(document).ready(function () {
                    var text_max = <?php echo intval($foodbakery_review_max_length); ?>;
                    jQuery('#textarea_feedback').html(text_max + ' characters remaining');

                    jQuery('#review_description').keyup(function () {
                        var text_length = jQuery('#review_description').val().length;
                        var text_remaining = text_max - text_length;

                        jQuery('#textarea_feedback').html(text_remaining + ' characters remaining');
                    });
                });

            </script>
            <?php
        }

        /**
         * 
         * @param type $ratings_summary
         * @param type $reviews_count
         */
        public function get_ratings_summary_ui($ratings_summary, $reviews_count) {
            ?>
            <div class="rating-summary">
                <h5><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_rating_summary_heading'); ?></h5>
                <ul>
                    <?php foreach ($ratings_summary as $key => $rating): ?>
                        <li>
                            <span class="review-category"><?php echo esc_html($rating['label']); ?></span>
                            <div class="rating-star">
                                <span class="rating-box" style="width:<?php echo ( $rating['value'] <= 0 ? 0 : ( round(( $rating['value'] / ( $reviews_count * 5) ) * 100, 1) ) ); ?>%"></span>
                            </div>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        }

        public function get_overall_rating_ui($post_idd) {
            ?>
            <div class="overall-rating">
                <h6><?php echo foodbakery_plugin_text_srt('foodbakery_reviews_overall_rating_heading'); ?></h6>
                <ul class="reviews-box">
                    <?php
                    $ratings_data = array(
                        'overall_rating' => 0.0,
                        'count' => 0,
                    );
                    $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $post_idd);
                    ?>
                    <li>
                        <em>
                            <?php
                            $icon_class = '';
                            $total = ( $ratings_data['overall_rating'] / 20);
                            echo number_format($total, 1, '.', '');
                            ?>
                        </em>
                        <div class="rating-star">
                            <span class="rating-box" style="width: <?php echo intval($ratings_data['overall_rating']); ?>%;"></span>
                        </div>
                        <span class="reviews-count">(<?php printf(esc_html__('based on %s reviews', 'foodbakery'), esc_html($ratings_data['count'])); ?>)</span>

                    </li>
                    <li>
                        <?php
                        if ($ratings_data['overall_rating'] <= 30) {
                            $icon_class = '<i class="icon-sad"></i>';
                        } else if ($ratings_data['overall_rating'] <= 50) {
                            $icon_class = '<i class="icon-neutral"></i>';
                        } else {
                            $icon_class = '<i class="icon-smile"></i>';
                        }
                        ?>
                        <div class="icon-holder">
                            <?php echo force_balance_tags($icon_class); ?>
                        </div>
                        <p><span><?php echo intval($ratings_data['overall_rating']); ?>%</span> <?php echo esc_html__('of diners recommend this restaurant', 'foodbakery'); ?></p>
                    </li>
                </ul>

            </div>
            <?php
        }

        /*
         * Get all child reviews and Delete them by Review ID
         */

        public function delete_child_reviews($review_id) {

            $query_args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'foodbakery_parent_review',
                        'value' => $review_id,
                        'compare' => '=',
                    ),
                ),
            );

            $review_query = new WP_Query($query_args);
            $child_reviews = $review_query->get_posts();
            if (!empty($child_reviews) && count($child_reviews) > 0) {
                foreach ($child_reviews as $child_key => $child_review) {
                    $this->delete_user_review_on_trash_callback($child_review->ID, true);
                }
            }
        }

        /**
         * Delete user review by provided review id from admin.
         */
        public function delete_user_review_on_trash_callback($review_id = '', $is_child_review = false) {
            if (isset($review_id) && $review_id != '') {
                $post_type = get_post_type($review_id);
                if ($post_type == 'foodbakery_reviews') {
                    $post_slug = get_post_meta($review_id, 'post_id', true);
                    $ratings = get_post_meta($review_id, 'ratings', true);
                    $is_child = get_post_meta($review_id, 'foodbakery_parent_review', true);
                    $is_child = ( isset($is_child) && $is_child != '' ) ? true : false;
                    $overall_rating = get_post_meta($review_id, 'overall_rating', true);

                    $args = array(
                        'name' => $post_slug,
                        'post_type' => 'restaurants',
                        'post_status' => 'publish',
                        'numberposts' => 1
                    );
                    $restaurants = get_posts($args);
                    // If restaurant found.
                    if (0 < count($restaurants)) {
                        if ($is_child == false) {

                            $post_id = $restaurants[0]->ID;

                            // Get existing ratings for this post and minus ratings stats from parent post.
                            $existing_ratings = get_post_meta($post_id, 'foodbakery_ratings', true);
                            if ($existing_ratings != '') {
                                if ($existing_ratings['reviews_count'] > 0) {
                                    $existing_ratings['reviews_count'] --;
                                }
                                if ($existing_ratings['overall_rating'][$overall_rating] > 0) {
                                    $existing_ratings['overall_rating'][$overall_rating] --;
                                }
                                foreach ($existing_ratings['ratings'] as $key => $val) {
                                    if (isset($ratings[$key]) && $ratings[$key] > 0 && isset($existing_ratings['ratings'][$key]) && $existing_ratings['ratings'][$key] > 0) {
                                        $existing_ratings['ratings'][$key] -= floatval($ratings[$key]);
                                    }
                                }
                                update_post_meta($post_id, 'foodbakery_ratings', $existing_ratings);
                            }

                            // Finally delete reviews post meta and post.
                            $all_meta = get_post_meta($review_id);
                            foreach ($all_meta as $key => $val) {
                                delete_post_meta($review_id, $key);
                            }
                        }
                        if ($is_child_review == true) {
                            wp_delete_post($review_id, true);
                        }
                    }
                }
            }
        }

        /**
         * Delete user review by provided review id.
         */
        public function delete_user_review_callback() {
            if (!wp_foodbakery::is_demo_user_modification_allowed()) {
                $response_array = array(
                    'type' => 'error',
                    'msg' => __('Demo users are not allowed to modify information.', 'foodbakery'),
                );
                echo json_encode($response_array);
                wp_die();
            }
            $success = false;
            $type = 'error';
            $msg = foodbakery_plugin_text_srt('foodbakery_reviews_incomplete_data_msg');
            $review_id = isset($_POST['review_id']) ? $_POST['review_id'] : 0;
            if ($review_id != 0) {

                $review_user_id = get_post_meta($_POST['review_id'], 'user_id', true);
                $review_company_id = get_user_meta($review_user_id, 'foodbakery_company', true);
                $user_data = get_currentuserinfo();
                $user_company = get_user_meta($user_data->ID, 'foodbakery_company', true);

                if ($review_company_id != $user_company) {
                    $response_array = array(
                        'type' => 'error',
                        'msg' => esc_html__('You dont have permissions to delete this review', 'foodbakery'),
                    );
                    echo json_encode($response_array);
                    wp_die();
                }

                $post_slug = get_post_meta($review_id, 'post_id', true);
                $ratings = get_post_meta($review_id, 'ratings', true);
                $overall_rating = get_post_meta($review_id, 'overall_rating', true);
                $is_child = get_post_meta($review_id, 'foodbakery_parent_review', true);
                $is_child = ( isset($is_child) && $is_child != '' ) ? true : false;
                $args = array(
                    'name' => $post_slug,
                    'post_type' => 'restaurants',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
                $restaurants = get_posts($args);
                // If restaurant found.
                if (0 < count($restaurants)) {
                    if ($is_child == false) {

                        $this->delete_child_reviews($review_id);

                        $post_id = $restaurants[0]->ID;

                        // Get existing ratings for this post and minus ratings stats from parent post.
                        $existing_ratings = get_post_meta($post_id, 'foodbakery_ratings', true);
                        if ($existing_ratings != '') {
                            if ($existing_ratings['reviews_count'] > 0) {
                                $existing_ratings['reviews_count'] --;
                            }
                            if ($existing_ratings['overall_rating'][$overall_rating] > 0) {
                                $existing_ratings['overall_rating'][$overall_rating] --;
                            }
                            foreach ($existing_ratings['ratings'] as $key => $val) {
                                if (isset($ratings[$key]) && $ratings[$key] > 0 && isset($existing_ratings['ratings'][$key]) && $existing_ratings['ratings'][$key] > 0) {
                                    $existing_ratings['ratings'][$key] -= floatval($ratings[$key]);
                                }
                            }
                            update_post_meta($post_id, 'foodbakery_ratings', $existing_ratings);
                        }

                        // Finally delete reviews post meta and post.
                        $all_meta = get_post_meta($review_id);
                        foreach ($all_meta as $key => $val) {
                            delete_post_meta($review_id, $key);
                        }
                    }
                    wp_delete_post($review_id, true);

                    $success = true;
                    $type = 'success';
                    $msg = foodbakery_plugin_text_srt('foodbakery_reviews_dashboard_delete_success_msg');
                }
            }

            $response_array = array(
                'type' => $type,
                'msg' => $msg,
            );
            echo json_encode($response_array);
            wp_die();
        }

        public function have_user_added_review_for_this_restaurent_callback($have_added, $filter, $post_id, $is_email = false) {

            $post = get_post($post_id);
            $slug = '';
            if ($post == null) {
                return $have_added;
            }
            $slug = $post->post_name;

            $args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'post_status' => array('publish', 'pending'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'post_id',
                        'value' => $slug,
                    ),
                ),
            );
            if ($is_email == false) {
                $args['meta_query'][] = array(
                    'key' => 'company_id',
                    'value' => $filter,
                );
            } else {
                $args['meta_query'][] = array(
                    'key' => 'user_email',
                    'value' => $filter,
                );
            }
            $query = new WP_Query($args);
            // Return True if there is already an restaurant with by this user.
            return ( 0 < $query->found_posts );
        }

        /**
         * A filter which is used whether user have added review for a post or not.
         */
        public function have_user_added_review_for_this_post_callback($have_added, $filter, $post_id, $is_email = false) {

            $post = get_post($post_id);
            $slug = '';
            if ($post == null) {
                return $have_added;
            }
            $slug = $post->post_name;

            $args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'post_status' => array('publish', 'pending'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'order_id',
                        'value' => $post_id,
                    ),
                ),
            );
            if ($is_email == false) {
                $args['meta_query'][] = array(
                    'key' => 'company_id',
                    'value' => $filter,
                );
            } else {
                $args['meta_query'][] = array(
                    'key' => 'user_email',
                    'value' => $filter,
                );
            }

            $query = new WP_Query($args);

            // Return True if there is already an restaurant with by this user.

            return ( 0 < $query->found_posts );
        }

        /**
         * Check if this user is owner then also consider that this user
         * have already added review in short he/she will not be allowed to add
         * review he/she to own restaurant.
         * 
         * @param	boolean	$have_added
         * @param	int		$company_id
         * @param	int		$post_id
         * @return	boolean
         */
        public function is_this_user_owner_of_this_post_callback($have_added, $company_id, $post_id) {
            $post_company_id = get_post_meta($post_id, 'foodbakery_restaurant_publisher', true);
            return ( $post_company_id == $company_id );
        }

        /**
         * Get Reviews Ratings Data for specified post.
         *
         * @param	array		$data
         * @param	int		$post_id
         */
        public function reviews_ratings_data_callback($data, $post_id) {
            $reviews_count = 0;
            $overall_ratings_sum = 0;
            $overall_ratings = array(
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0,
            );
            // Get existing ratings for this post.
            $existing_ratings_data = get_post_meta($post_id, 'foodbakery_ratings', true);

            if ('' != $existing_ratings_data) {
                $existing_overall_ratings = isset($existing_ratings_data['overall_rating']) ? $existing_ratings_data['overall_rating'] : array();

                foreach ($existing_overall_ratings as $key => $val) {
                    $overall_ratings_sum += ( $key * $val );
                }
                if (isset($existing_ratings_data['reviews_count']) && $existing_ratings_data['reviews_count'] > 0) {
                    $overall_rating_percentage = ( $overall_ratings_sum / ( $existing_ratings_data['reviews_count'] * 5 ) ) * 100;
                    $data['overall_rating'] = round($overall_rating_percentage, 2);
                }
                $data['count'] = $existing_ratings_data['reviews_count'];
            }

            return $data;
        }

        public function foodbakery_filter_query_args($args = array()) {

            $date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';
            $status = isset($_POST['status']) ? $_POST['status'] : '';

            // Date range filter query
            if ($date_range != '' && $date_range != 'undefined') {
                $new_date_range = explode(',', $date_range);
                $start_date = isset($new_date_range[0]) ? str_replace('/', '-', $new_date_range[0]) : '';
                $end_date = isset($new_date_range[1]) ? str_replace('/', '-', $new_date_range[1]) : '';
                $args['date_query'] = array(
                    'after' => date('Y-m-d', strtotime($start_date)),
                    'before' => date('Y-m-d', strtotime($end_date)),
                    'inclusive' => true,
                );
            }

            // Status filter meta query
            if ($status != '' && $status != 'undefined') {
                $args['meta_query'][] = array(
                    'key' => 'foodbakery_order_status',
                    'value' => $status,
                    'compare' => '=',
                );
            }

            return $args;
        }

    }

    global $Foodbakery_Reviews_Obj;
    $Foodbakery_Reviews_Obj = new Foodbakery_Reviews();
}






// add analytic for reviews

add_filter('views_edit-foodbakery_reviews', function( $views ) {
    $args = array(
        'post_type' => 'foodbakery_reviews',
        'posts_per_page' => "-1",
    );
    $custom_query = new WP_Query($args);
    $total_reviews = 0;
    $overall_rating = 0;
    $total_active = 0;
    $total_pending = 0;
    $rating_sum = 0;
    while ($custom_query->have_posts()) : $custom_query->the_post();
        global $post;
        $review_status = get_post_status($post->ID);
        if (isset($review_status) && !empty($review_status)) {
            if ($review_status == 'publish') {
                $total_active ++;
            } else if ($review_status == 'pending') {
                $total_pending ++;
            }
        }
        $overall_rting = get_post_meta($post->ID, 'overall_rating', true);
        $rating_sum = $rating_sum + $overall_rting;
        $total_reviews ++;
    endwhile;
    wp_reset_postdata();
    if ($total_active != 0) {
        $overall_rating = $rating_sum / $total_active;
    }



    $total = ($overall_rating / 5) * 100;
    $tating_stars = '<div class="reviews-rating-holder"><div class="rating-star">
	    <span class="rating-box" style="width:' . $total . '%;"></span>
		</div></div>';

    echo '
    <ul class="total-foodbakery-restaurant row">
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>' . esc_html__('Total Reviews ', 'foodbakery') . '</strong><em>' . $total_reviews . '</em><i class="icon-comments-o"></i><i class="icon-plus4 custom-plus"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>' . esc_html__('Active Reviews ', 'foodbakery') . '</strong><em>' . $total_active . '</em><i class="icon-check_circle"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>' . esc_html__('Pending Reviews ', 'foodbakery') . '</strong><em>' . $total_pending . '</em><i class="icon-back-in-time"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>' . esc_html__('Overall Rating ', 'foodbakery') . '</strong><em>' . $tating_stars . '</em><i class="icon-favorite2"></i></div></li>
	
    </ul>
    ';
    return $views;
});
