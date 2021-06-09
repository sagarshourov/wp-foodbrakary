<?php

/**
 * File Type: Restaurant Post Type
 */
if ( ! class_exists('post_type_restaurant') ) {

    class post_type_restaurant {

        /**
         * Start Contructer Function
         */
        public function __construct() {
            add_action('init', array( $this, 'foodbakery_restaurant_init' ), 12);
            add_filter('manage_restaurants_posts_columns', array( $this, 'foodbakery_restaurant_columns_add' ));

            add_action('manage_restaurants_posts_custom_column', array( $this, 'foodbakery_restaurant_columns' ), 10, 2);

            add_action('create_daily_restaurants_check', array( $this, 'create_daily_restaurants_check_callback' ), 10);

            add_action('admin_menu', array( $this, 'remove_cus_meta_boxes' ));
            add_action('do_meta_boxes', array( $this, 'remove_cus_meta_boxes' ));
            add_filter("get_user_option_screen_layout_restaurants", array( $this, 'restaurant_type_screen_layout' ));
            add_action('admin_head', array( $this, 'check_post_type_and_remove_media_buttons' ));

            // AJAX handlers for import/export restaurant type categories in plugin options.
            add_action('wp_ajax_generate_restaurant_type_categories_backup', array( $this, 'generate_restaurant_type_categories_backup_callback' ));
            add_action('wp_ajax_delete_restaurant_type_categories_backup_file', array( $this, 'delete_restaurant_type_categories_backup_file_callback' ));
            add_action('wp_ajax_restore_restaurant_type_categories_backup', array( $this, 'restore_restaurant_type_categories_backup_callback' ));
            add_action('wp_ajax_foodbakery_uploading_import_cat_file', array( $this, 'foodbakery_uploading_import_cat_file_callback' ));
        }

        public function restaurant_type_screen_layout($selected) {
            return 1; // Use 1 column if user hasn't selected anything in Screen Options
        }

        function check_post_type_and_remove_media_buttons() {
            global $current_screen;
            if ( get_post_type() == 'restaurants' ) {
                remove_action('media_buttons', 'media_buttons');
            }
        }

        function remove_cus_meta_boxes() {
            remove_meta_box('submitdiv', 'restaurants', 'side');
            remove_meta_box('tagsdiv-restaurant-tag', 'restaurants', 'side');
            remove_meta_box('foodbakery_locationsdiv', 'restaurants', 'side');
            remove_meta_box('postimagediv', 'restaurants', 'side');
        }

        /**
         * Start Wp's Initilize action hook Function
         */
        public function foodbakery_restaurant_init() {
            // Initialize Post Type
            $this->foodbakery_restaurant_register();
            $this->create_restaurant_category();
            $this->create_restaurant_tags();
        }

        /**
         * End Wp's Initilize action hook Function
         */
        public function foodbakery_trim_content() {

            global $post;
            $read_more = '....';
            $the_content = get_the_content($post->ID);
            if ( strlen(esc_html__(get_the_content($post->ID))) > 200 ) {
                $the_content = substr(esc_html__(get_the_content($post->ID)), 0, 200) . $read_more;
            }

            return $the_content;
        }

        /**
         * Start Function How to Register post type
         */
        public function foodbakery_restaurant_register() {

            $labels = array(
                'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurants'),
                'menu_name' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurants'),
                'add_new_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_add_new_restaurant'),
                'edit_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_edit_restaurant'),
                'new_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_new_restaurant_item'),
                'add_new' => foodbakery_plugin_text_srt('foodbakery_restaurant_add_new_restaurant'),
                'view_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_view_restaurant_item'),
                'search_items' => foodbakery_plugin_text_srt('foodbakery_restaurant_search'),
                'not_found' => foodbakery_plugin_text_srt('foodbakery_restaurant_nothing_found'),
                'not_found_in_trash' => foodbakery_plugin_text_srt('foodbakery_restaurant_nothing_found_in_trash'),
                'parent_item_colon' => ''
            );
            $args = array(
                'exclude_from_search' => true,
                'labels' => $labels,
                'public' => true,
                'menu_position' => 26,
                'menu_icon' => wp_foodbakery::plugin_url() . 'assets/backend/images/restaurants.png',
                'has_archive' => false,
                'capability_type' => 'post',
                'supports' => array( 'title', 'editor', 'thumbnail' )
            );

            register_post_type('restaurants', $args);
        }

        /**
         * End Function How to Register post type
         */

        /**
         * Start Function How to Add Title Columns
         */
        public function foodbakery_restaurant_columns_add($columns) {

            unset(
                    $columns['date']
            );
            unset(
                    $columns['tags']
            );

            $columns['company'] = foodbakery_plugin_text_srt('foodbakery_restaurant_company');
            $columns['restaurant_type'] = foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_type');
            $columns['posted'] = foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_posted');
            $columns['expired'] = foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_expired');
            $columns['status'] = foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_status');

            return $columns;
        }

        /**
         * End Function How to Add Title Columns
         */

        /**
         * @Register Restaurant Category
         * @return
         */
        function create_restaurant_category() {
            global $foodbakery_var_plugin_static_text;
            $labels = array(
                'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_categories'),
                'singular_name' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_category'),
                'search_items' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_category'),
                'all_items' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_all_categories'),
                'parent_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_parent_category'),
                'parent_item_colon' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_parent_category_clone'),
                'edit_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_edit_category'),
                'update_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_update_category'),
                'add_new_item' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_add_new_category'),
                'new_item_name' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_category'),
                'menu_name' => foodbakery_plugin_text_srt('foodbakery_restaurant_restaurant_categories'),
            );
            $args = array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => false,
                'query_var' => false,
                'meta_box_cb' => false,
                'show_in_quick_edit' => false,
                'rewrite' => array( 'slug' => 'restaurant-cuisine' ),
            );
            register_taxonomy('restaurant-category', array( 'restaurants' ), $args);
        }

        /**
         * @Register Restaurant Tags
         * @return
         */
        function create_restaurant_tags() {
            global $foodbakery_var_plugin_static_text;
            $labels = array(
                'name' => esc_html__('Tags', 'foodbakery'),
                'singular_name' => esc_html__('Tag', 'foodbakery'),
                'search_items' => esc_html__('Tags', 'foodbakery'),
                'all_items' => esc_html__('All Tags', 'foodbakery'),
                'parent_item' => esc_html__('Parent Tag', 'foodbakery'),
                'parent_item_colon' => null,
                'edit_item' => esc_html__('Edit Tag', 'foodbakery'),
                'update_item' => esc_html__('Update Tag', 'foodbakery'),
                'add_new_item' => esc_html__('Add New Tag', 'foodbakery'),
                'new_item_name' => esc_html__('New Tag Name', 'foodbakery'),
                'menu_name' => esc_html__('Tags', 'foodbakery'),
            );
            $args = array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => false,
                'query_var' => true,
                'meta_box_cb' => false,
                'show_in_quick_edit' => false,
                'rewrite' => array( 'slug' => 'restaurant-tag' ),
            );
            register_taxonomy('restaurant-tag', array( 'restaurants' ), $args);
        }

        /**
         * Start Function How to Add  Columns
         */
        public function foodbakery_restaurant_columns($name) {
            global $post, $gateway;

            switch ( $name ) {
                default:
                    //echo "name is " . $name;
                    break;
                case 'company':
                    $foodbakery_restaurant_employer = get_post_meta($post->ID, "foodbakery_restaurant_publisher", true);

                    $publisher_title = '';
                    if ( $foodbakery_restaurant_employer != '' ) {

                        $publisher_title = get_the_title($foodbakery_restaurant_employer);
                    }
                    echo esc_html($publisher_title);
                    break;
                case 'restaurant_type':
                    $restaurant_type = get_post_meta($post->ID, 'foodbakery_restaurant_type', true);
                    echo esc_html($restaurant_type);
                    break;
                case 'posted':
                    $date_format = get_option('date_format');
                    $foodbakery_restaurant_posted = get_post_meta($post->ID, 'foodbakery_restaurant_posted', true);
                    $foodbakery_restaurant_posted_date = isset($foodbakery_restaurant_posted) && $foodbakery_restaurant_posted != '' ? date_i18n($date_format, ($foodbakery_restaurant_posted)) : '';
                    echo esc_html($foodbakery_restaurant_posted_date);
                    break;
                case 'expired':
                    $date_format = get_option('date_format');
                    $foodbakery_restaurant_expired = get_post_meta($post->ID, 'foodbakery_restaurant_expired', true);
                    $foodbakery_restaurant_expiry_date = isset($foodbakery_restaurant_expired) && $foodbakery_restaurant_expired != '' ? date_i18n($date_format, ($foodbakery_restaurant_expired)) : '';
                    echo esc_html($foodbakery_restaurant_expiry_date);
                    break;
                case 'views':
                    $foodbakery_views = get_post_meta($post->ID, "foodbakery_count_views", true);
                    echo absint($foodbakery_views);
                    echo ' / ';
                    $foodbakery_shortlisted = count_usermeta('cs-restaurants-wishlist', serialize(strval($post->ID)), 'LIKE');
                    echo absint($foodbakery_shortlisted);
                    echo ' / ';
                    $applications = count_usermeta('cs-restaurants-applied', serialize(strval($post->ID)), 'LIKE');
                    echo absint($applications);
                    break;
                case 'status':
                    echo get_post_meta($post->ID, 'foodbakery_restaurant_status', true);
                    break;
            }
        }

        /**
         * End Function How to Add  Columns
         */

        /**
         * Invoked when daily cron runs for checking if any restaurant expired.
         */
        public function create_daily_restaurants_check_callback() {
            $args = array(
                'posts_per_page' => '-1',
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'foodbakery_restaurant_status',
                        'value' => 'active',
                    ),
                ),
            );
            $restaurants = new WP_Query($args);
            $restaurants = $restaurants->get_posts();
            foreach ( $restaurants as $key => $restaurant ) {
                $restaurant_post_expiry = get_post_meta($restaurant->ID, 'foodbakery_restaurant_expired', true);

                if ( ! empty($restaurant_post_expiry) ) {

                    $username = get_post_meta($restaurant->ID, 'foodbakery_restaurant_username', true);

                    if ( $restaurant_post_expiry <= time() ) {
                        update_post_meta($restaurant->ID, 'foodbakery_restaurant_status', 'inactive');
                        do_action('foodbakery_restaurant_expired_email', get_user_by('ID', $username), $restaurant->ID);
                    }
                }
            }
        }

        /**
         * Generate restaurant type categories backup.
         */
        public function generate_restaurant_type_categories_backup_callback() {
            global $wp_filesystem;

            $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
            if ( false === ( $creds = request_filesystem_credentials($backup_url, '', false, false, array()) ) ) {
                return true;
            }
            if ( ! WP_Filesystem($creds) ) {
                request_filesystem_credentials($backup_url, '', true, false, array());
                return true;
            }

            $terms = get_terms('restaurant-category', array( 'hide_empty' => 0 ));

            $terms_arr = array();
            $terms_str = 'Name,Parent,Description' . PHP_EOL;
            foreach ( $terms as $key => $term ) {
                $term_arr = array();
                $term_arr[] = $term->name;
                $parent_term = get_term($term->parent, 'restaurant-category');
                if ( $parent_term != null ) {
                    $term_arr[] = $parent_term->name;
                } else {
                    $term_arr[] = "";
                }
                $term_arr[] = $term->description;

                $terms_str .= '"' . implode('","', $term_arr) . '"' . PHP_EOL;
            }
            $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/restaurant-type-categories/';
            $foodbakery_filename = trailingslashit($foodbakery_upload_dir) . ( current_time('d-M-Y_H.i.s') ) . '.csv';

            if ( ! $wp_filesystem->put_contents($foodbakery_filename, $terms_str, FS_CHMOD_FILE) ) {
                echo esc_html__("Error saving file!", 'foodbakery');
            } else {
                echo esc_html__("Backup Generated.", 'foodbakery');
            }
            wp_die();
        }

        /**
         * Delete selected locations back file using AJAX.
         */
        public function delete_restaurant_type_categories_backup_file_callback() {
            global $wp_filesystem;
            $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
            if ( false === ( $creds = request_filesystem_credentials($backup_url, '', false, false, array()) ) ) {
                return true;
            }
            if ( ! WP_Filesystem($creds) ) {
                request_filesystem_credentials($backup_url, '', true, false, array());
                return true;
            }
            $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/restaurant-type-categories/';

            $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';
            $foodbakery_filename = trailingslashit($foodbakery_upload_dir) . $file_name;
            if ( is_file($foodbakery_filename) ) {
                unlink($foodbakery_filename);
                printf(esc_html__("File '%s' Deleted Successfully", 'foodbakery'), $file_name);
            } else {
                echo esc_html__("Error Deleting file!", 'foodbakery');
            }
            die();
        }

        /**
         * Uploading Category File
         */
        public function foodbakery_uploading_import_cat_file_callback() {
            global $wp_filesystem;
            add_filter('upload_dir', array( $this, 'foodbakery_category_upload_foodbakery' ));
            $uploadedfile = $_FILES['foodbakery_btn_browse_category_file'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
            if ( $movefile && ! isset($movefile['error']) ) {
                echo esc_url($movefile['url']);
            }
            remove_filter('upload_dir', array( $this, 'foodbakery_category_upload_foodbakery' ));
            wp_die();
        }

        public function foodbakery_category_upload_foodbakery($dir) {
            return array(
                'path' => $dir['basedir'] . '/category',
                'url' => $dir['baseurl'] . '/category',
                'subdir' => '/category',
                    ) + $dir;
        }

        /**
         * Restore location from backup file or URL.
         */
        public function restore_restaurant_type_categories_backup_callback() {
            global $wp_filesystem;
            $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=foodbakery_settings');
            if ( false === ( $creds = request_filesystem_credentials($backup_url, '', false, false, array()) ) ) {
                return true;
            }
            if ( ! WP_Filesystem($creds) ) {
                request_filesystem_credentials($backup_url, '', true, false, array());
                return true;
            }
            $foodbakery_upload_dir = wp_foodbakery::plugin_dir() . 'backend/settings/backups/restaurant-type-categories/';
            $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';
            $file_path = isset($_POST['file_path']) ? $_POST['file_path'] : '';
            if ( $file_path == 'yes' ) {
                $foodbakery_file_body = '';
                $foodbakery_file_response = wp_remote_get($file_name);
                if ( is_array($foodbakery_file_response) ) {
                    $foodbakery_file_body = isset($foodbakery_file_response['body']) ? $foodbakery_file_response['body'] : '';
                    if ( $foodbakery_file_body != '' ) {
                        $this->import_restaurant_type_categories($foodbakery_file_body);
                        esc_html_e("File Imported Successfully", 'foodbakery');
                    }
                } else {
                    esc_html_e("Error Restoring file!", 'foodbakery');
                }
            } else {
                $foodbakery_filename = trailingslashit($foodbakery_upload_dir) . $file_name;
                if ( is_file($foodbakery_filename) ) {
                    $locations_file = $wp_filesystem->get_contents($foodbakery_filename);
                    $this->import_restaurant_type_categories($locations_file);
                    printf(esc_html__("File '%s' Restored Successfully", 'foodbakery'), $file_name);
                } else {
                    esc_html_e("Error Restoring file!", 'foodbakery');
                }
            }
            wp_die();
        }

        public function import_restaurant_type_categories($csv_str) {
            $term_new_ids = array();
            $lines = preg_split('/\r*\n+|\r+/', $csv_str);
            $not_found = array();
            foreach ( $lines as $key => $line ) {
                if ( 0 == $key ) {
                    continue;
                }

                $parts = str_getcsv($line);
                if ( count($parts) < 3 ) {
                    continue;
                }
                $args = array(
                    'parent' => 0,
                    'slug' => sanitize_title($parts[0]),
                    'description' => $parts[2],
                );
                if ( ! empty($parts[1]) ) {
                    if ( isset($term_new_ids[$parts[0]]) ) {
                        $args['parent'] = $term_new_ids[$parts[0]];
                    } else {
                        $not_found[] = $line;
                    }
                }

                $return = wp_insert_term(
                        $parts[0], // The term.
                        'restaurant-category', // The taxonomy.
                        $args
                );
            }
        }

    }

    // End of class
    // Initialize Object
    $restaurant_object = new post_type_restaurant();
}
add_action('admin_head', 'foodbakery_restaurant_remove_help_tabs');

function foodbakery_restaurant_remove_help_tabs() {
     $current_screen = get_current_screen();
    if ( isset($current_screen) && $current_screen->post_type == 'restaurants' ) {
        add_filter('screen_options_show_screen', '__return_false');
        add_filter('bulk_actions-edit-restaurant-type', '__return_empty_array');
        echo '<style type="text/css">
				.post-type-restaurant-type .tablenav.top,
				.post-type-restaurant-type .tablenav.bottom,
				.post-type-restaurant-type #titlediv .inside,
				.post-type-restaurant-type #postdivrich{
					display: none;
				}
			</style>';
    }
}

add_filter('views_edit-restaurants', function( $views ) {

    $args_expire = array(
        'post_type' => 'restaurants',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'foodbakery_restaurant_expired',
                'value' => current_time('timestamp'),
                'compare' => '<',
            ),
        ),
    );
    $query_expire = new WP_Query($args_expire);
    $count_lisings_expire = $query_expire->found_posts;

    // end expired restaurant count

    $total_add = wp_count_posts('restaurants');

    $args = array(
        'post_type' => 'restaurants',
        'posts_per_page' => "1",
        'fields' => 'ids',
    );
    $args['meta_query'] = array(
        array(
            'key' => 'foodbakery_restaurant_status',
            'value' => 'active',
            'compare' => '=',
        ),
    );

    $total_query = new WP_Query($args);
    $total_active = $total_query->found_posts;


    /*
     * Getting Free Packages
     */

    $args = array(
        'post_type' => 'packages',
        'posts_per_page' => -1,
        'fields' => 'ids',
    );
    $args['meta_query'] = array(
        array(
            'key' => 'foodbakery_package_type',
            'value' => 'free',
            'compare' => '=',
        ),
    );
    $free_listings_query = new WP_Query($args);
    $free_package_ids = $free_listings_query->posts;


    /*
     * Getting Paid Packages
     */

    $args = array(
        'post_type' => 'packages',
        'posts_per_page' => -1,
        'fields' => 'ids',
    );
    $args['meta_query'] = array(
        array(
            'key' => 'foodbakery_package_type',
            'value' => 'paid',
            'compare' => '=',
        ),
    );
    $paid_listings_query = new WP_Query($args);
    $paid_package_ids = $paid_listings_query->posts;


    /*
     * Free Ads
     */
    $args = array(
        'post_type' => 'restaurants',
        'posts_per_page' => "1",
        'fields' => 'ids',
    );
    $args['meta_query'] = array(
        array(
            'key' => 'foodbakery_restaurant_package',
            'value' => $free_package_ids,
            'compare' => 'IN',
        ),
    );
    $free_query = new WP_Query($args);
    $free_ads = $free_query->found_posts;


    /*
     * Paid Ads
     */
    $args = array(
        'post_type' => 'restaurants',
        'posts_per_page' => "1",
        'fields' => 'ids',
    );
    $args['meta_query'] = array(
        array(
            'key' => 'foodbakery_restaurant_package',
            'value' => $paid_package_ids,
            'compare' => 'IN',
        ),
    );
    $paid_query = new WP_Query($args);
    $paid_ads = $paid_query->found_posts;


    wp_reset_postdata();
    echo '
    <ul class="total-foodbakery-restaurant row">
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Total Ads </strong><em>' . $total_add->publish . '</em><i class="icon-coins"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Active Ads </strong><em>' . $total_active . '</em><i class="icon-check_circle"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Expire ads </strong><em>' . $count_lisings_expire . '</em><i class="icon-back-in-time"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Free Ads </strong><em>' . $free_ads . '</em><i class="icon-money_off"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Paid Ads </strong><em>' . $paid_ads . '</em><i class="icon-attach_money"></i></div></li>
    </ul>
    ';
    return $views;
});
