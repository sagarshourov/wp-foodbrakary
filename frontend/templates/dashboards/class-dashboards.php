<?php

/**
 * File Type: Dashboard Templates
 */
if ( ! class_exists( 'Foodbakery_Dashboard_Templates' ) ) {

    class Foodbakery_Dashboard_Templates {

        /**
         * Start construct Functions
         */
        public function __construct() {
            global $foodbakery_plugin_static_text;
            $this->templates = array();
            
            add_filter( 'theme_page_templates', array( $this, 'theme_page_templates_callback' ) );
            add_filter( 'template_include', array( $this, 'dashboard_page_templates' ) );
            add_filter( 'template_include', array( $this, 'packag_page_templates' ) );
            $this->templates = array(
                'publisher-dashboard.php' => __( 'Publisher Dashboard', 'foodbakery' ),
                'packages-template.php' => __( 'Membership  Detail', 'foodbakery' ),
            );
            add_action( 'wp_ajax_foodbakery_save_suggestions_settings_dashboard', array( $this, 'foodbakery_save_suggestions_settings_dashboard_callback' ) );
        }
        
        public function theme_page_templates_callback( $post_templates ) {
            $post_templates = array_merge($this->templates, $post_templates);   
            return $post_templates;
        }

        /**
         * end construct Functions
         */
        /**
         * Adds our template to the pages cache in order to trick WordPress
         * into thinking the template file exists where it doens't really exist.
         */

        /**
         * Start Function how to register template in dashboard
         */
        public function dashboard_register_templates( $atts ) {
            $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
            $templates = wp_cache_get( $cache_key, 'themes' );
            if ( empty( $templates ) ) {
                $templates = array();
            } // end if
            wp_cache_delete( $cache_key, 'themes' );

            $templates = array_merge( $templates, $this->templates );
            wp_cache_add( $cache_key, $templates, 'themes', 1800 );

            return $atts;
        }

        /**
         * End Function for to register template in dashboard
         */
        // end dashboard_register_templates

        /**
         * Start Function if the templae page is assigned to the page funciton
         */
        public function dashboard_page_templates( $template ) {
            global $post;
            if ( ! isset( $post ) )
                return $template;
            if ( ! isset( $this->templates[get_post_meta( $post->ID, '_wp_page_template', true )] ) ) {
                return $template;
            }
            if ( 'publisher-dashboard.php' === get_post_meta( $post->ID, '_wp_page_template', true ) ) {
                $current_user = wp_get_current_user();
                $roles = $current_user->roles;
                if ( in_array( 'foodbakery_publisher', $roles ) ) {
                    $file = plugin_dir_path( __FILE__ ) . 'publisher/' . get_post_meta( $post->ID, '_wp_page_template', true );
                    if ( file_exists( $file ) ) {
                        return $file;
                    }
                } else {
                    wp_redirect( site_url() );
                }
            }
            return $template;
        }

        public function packag_page_templates( $template ) {
            global $post;
            if ( ! isset( $post ) )
                return $template;
            if ( ! isset( $this->templates[get_post_meta( $post->ID, '_wp_page_template', true )] ) ) {
                return $template;
            }
            if ( 'packages-template.php' === get_post_meta( $post->ID, '_wp_page_template', true ) ) {

                $file = plugin_dir_path( __FILE__ ) . '' . get_post_meta( $post->ID, '_wp_page_template', true );
                if ( file_exists( $file ) ) {
                    return $file;
                }
            }
            return $template;
        }

        /**
         * Save suggestions settings for user's dashaboard.
         */
        public function foodbakery_save_suggestions_settings_dashboard_callback() {
            $msg = __( 'Please put number of suggestions.', 'foodbakery' );
            $type   = 'error';
            $success = false;
            $suggested_restaurants_categories = isset( $_POST['foodbakery_suggested_restaurants_categories'] ) ? $_POST['foodbakery_suggested_restaurants_categories'] : '';
            $suggested_restaurants_max_restaurants = isset( $_POST['suggested_restaurants_max_restaurants'] ) ? $_POST['suggested_restaurants_max_restaurants'] : '';
            if ( $suggested_restaurants_categories != '' && $suggested_restaurants_max_restaurants != '' ) {
                $user = wp_get_current_user();

                if ( $user->ID > 0 ) {
                    update_user_meta( $user->ID, 'suggested_restaurants_categories', $suggested_restaurants_categories );
                    update_user_meta( $user->ID, 'suggested_restaurants_max_restaurants', $suggested_restaurants_max_restaurants );
                    $msg = __( 'Your settings successfully saved.', 'foodbakery' );
                    $type   = 'success';
                    $success = true;
                }
            }
            
            $response_array = array(
                'type' => $type,
                'msg' => $msg,
            );
            echo json_encode( $response_array );
            wp_die();
        }

    }

    // end class
    // Initialize Object
    $foodbakery_dashboard_templates = new Foodbakery_Dashboard_Templates();
}