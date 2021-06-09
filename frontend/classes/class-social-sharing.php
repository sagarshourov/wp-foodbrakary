<?php

/**
 * File Type: Header Element
 */
if (!class_exists('Foodbakery_Social_Sharing')) {

    class Foodbakery_Social_Sharing {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('foodbakery_social_sharing', array($this, 'foodbakery_social_sharing_function'));
        }

        /* ----------------------------------------------------------------
          // @Social Sharing Function
          /---------------------------------------------------------------- */

        public function foodbakery_social_sharing_function() {


            global $post, $foodbakery_plugin_options, $foodbakery_theme_options;
            $html = '';
            $foodbakery_social_share = isset($foodbakery_plugin_options['foodbakery_social_share']) ? $foodbakery_plugin_options['foodbakery_social_share'] : '';
            $foodbakery_var_twitter = isset($foodbakery_plugin_options['foodbakery_twitter_share']) ? $foodbakery_plugin_options['foodbakery_twitter_share'] : '';
            $foodbakery_var_facebook = isset($foodbakery_plugin_options['foodbakery_facebook_share']) ? $foodbakery_plugin_options['foodbakery_facebook_share'] : '';
            $foodbakery_var_google_plus = isset($foodbakery_plugin_options['foodbakery_google_plus_share']) ? $foodbakery_plugin_options['foodbakery_google_plus_share'] : '';
            $foodbakery_var_tumblr = isset($foodbakery_plugin_options['foodbakery_tumblr_share']) ? $foodbakery_plugin_options['foodbakery_tumblr_share'] : '';
            $foodbakery_var_dribbble = isset($foodbakery_plugin_options['foodbakery_dribbble_share']) ? $foodbakery_plugin_options['foodbakery_dribbble_share'] : '';
            $foodbakery_var_share = isset($foodbakery_plugin_options['foodbakery_var_stumbleupon_share']) ? $foodbakery_plugin_options['foodbakery_var_stumbleupon_share'] : '';
            $foodbakery_var_stumbleupon = isset($foodbakery_plugin_options['foodbakery_stumbleupon_share']) ? $foodbakery_plugin_options['foodbakery_stumbleupon_share'] : '';
            $foodbakery_var_sharemore = isset($foodbakery_plugin_options['foodbakery_share_share']) ? $foodbakery_plugin_options['foodbakery_share_share'] : '';
            $foodbakery_pintrest_share = isset($foodbakery_plugin_options['foodbakery_pintrest_share']) ? $foodbakery_plugin_options['foodbakery_pintrest_share'] : '';
            $foodbakery_instagram_share = isset($foodbakery_plugin_options['foodbakery_instagram_share']) ? $foodbakery_plugin_options['foodbakery_instagram_share'] : '';
            if (isset($foodbakery_social_share) && 'on' === $foodbakery_social_share) {
                foodbakery_addthis_script_init_method();
                $html = '';

                $single = false;
                if (is_single()) {
                    $single = true;
                }

                $path = trailingslashit(get_template_directory_uri()) . "include/assets/images/";
                if ($foodbakery_var_twitter == 'on' or $foodbakery_var_sharemore == 'on' or $foodbakery_var_facebook == 'on' or $foodbakery_var_google_plus == 'on' or $foodbakery_var_tumblr == 'on' or $foodbakery_var_dribbble == 'on' or $foodbakery_var_share == 'on' or $foodbakery_var_stumbleupon == 'on') {

                    if (isset($foodbakery_var_facebook) && $foodbakery_var_facebook == 'on') {
                        if ($single == true) {
                            $html .='<li><a class="addthis_button_facebook" data-original-title="facebook"><i class="icon-facebook3"></i></a></li>';
                        } else {
                            $html .='<li><a class="addthis_button_facebook" data-original-title="facebook"><i class="icon-facebook3"></i></a></li>';
                        }
                    }
                    if (isset($foodbakery_var_twitter) && $foodbakery_var_twitter == 'on') {

                        if ($single == true) {
                            $html .='<li><a class="addthis_button_twitter"  data-original-title="twitter"><i class="icon-twitter3"></i></a></li>';
                        } else {
                            $html .='<li><a class="addthis_button_twitter"  data-original-title="twitter"><i class="icon-twitter3"></i></a></li>';
                        }
                    }
                    if (isset($foodbakery_var_google_plus) && $foodbakery_var_google_plus == 'on') {

                        if ($single == true) {
                            $html .='<li><a class="addthis_button_google" data-original-title="google+"><i class="icon-google"></i></a></li>';
                        } else {
                            $html .='<li><a class="addthis_button_google" data-original-title="google+"><i class="icon-google"></i></a></li>';
                        }
                    }
                    if (isset($foodbakery_var_tumblr) && $foodbakery_var_tumblr == 'on') {

                        if ($single == true) {
                            $html .='<li><a class="addthis_button_tumblr" data-original-title="tumbler"><i class="icon-tumblr3"></i></a></li>';
                        } else {
                            $html .='<li><a class="addthis_button_tumblr" data-original-title="tumbler"><i class="icon-tumblr3""></i></a></li>';
                        }
                    }

                    if (isset($foodbakery_var_dribbble) && $foodbakery_var_dribbble == 'on') {
                        if ($single == true) {
                            $html .='<li><a class="addthis_button_dribbble" data-original-title="dribble"><i class="icon-dribbble3"></i></a></li>';
                        } else {
                            $html .='<li><a class="addthis_button_dribbble" data-original-title="dribble"><i class="icon-dribbble3"></i></a></li>';
                        }
                    }
                    if (isset($foodbakery_var_stumbleupon) && $foodbakery_var_stumbleupon == 'on') {
                        if ($single == true) {
                            $html .='<li><a class="addthis_button_stumbleupon" data-original-title="stumbleupon"><i class="icon-stumbleupon"></i></a></li>';
                        } else {
                            $html .='<li><a class="addthis_button_stumbleupon" data-original-title="stumbleupon"><i class="icon-stumbleupon"></i></a></li>';
                        }
                    }
                    if (isset($foodbakery_var_sharemore) && $foodbakery_var_sharemore == 'on') {
                        $html .='<li><a class="cs-more addthis_button_compact"><i class="icon-share"></a></li>';
                    }
                }
                echo balanceTags($html, true);
            }
        }

    }

    global $Foodbakery_Social_Sharing;
    $Foodbakery_Social_Sharing = new Foodbakery_Social_Sharing();
}