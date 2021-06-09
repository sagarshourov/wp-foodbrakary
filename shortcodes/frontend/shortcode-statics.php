<?php

/**
 * File Type: Searchs Shortcode Frontend
 */
if (!class_exists('Foodbakery_Statics_Categories_front')) {

    class Foodbakery_Statics_Categories_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'statics';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_statics_shortcode_callback'));
        }

        /*
         * Shortcode View on Frontend
         */

        function combine_pt_section($keys, $values) {
            $result = array();
            foreach ($keys as $i => $k) {
                $result[$k][] = $values[$i];
            }
            array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
            return $result;
        }

        public function foodbakery_statics_shortcode_callback($atts, $content = "") {
            global $current_user, $foodbakery_plugin_options;
            $page_element_size = isset($atts['statics_element_size']) ? $atts['statics_element_size'] : 100;
            ob_start();
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
            }
            $statics_title = isset($atts['statics_title']) ? $atts['statics_title'] : '';
            $foodbakery_statics_text_color = isset($atts['foodbakery_statics_text_color']) ? $atts['foodbakery_statics_text_color'] : '';
            $statics_text_color = '';
            if ($foodbakery_statics_text_color != '') {
                $statics_text_color = 'style="color:' . $foodbakery_statics_text_color . ' !important"';
            }

            $pricing_tabl_subtitle = isset($atts['statics_subtitle']) ? $atts['statics_subtitle'] : '';
            $foodbakery_var_statics_align = isset($atts['foodbakery_var_statics_align']) ? $atts['foodbakery_var_statics_align'] : '';

            if ((isset($pricing_tabl_subtitle) && $pricing_tabl_subtitle != '') || (isset($statics_title) && $statics_title != '')) {

                echo '<div class="element-title ' . $foodbakery_var_statics_align . '">';
                if (isset($statics_title) && $statics_title != '') {
                    echo '<h2>' . $statics_title . '</h2>';
                }
                if ($pricing_tabl_subtitle != '') {
                    echo '<p>' . $pricing_tabl_subtitle . '</p>';
                }
                echo '</div>';
            }


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
            $count_rest = $total_query->found_posts;
            wp_reset_postdata();
            $order_args = array(
                'posts_per_page' => "1",
                'post_type' => 'orders_inquiries',
                'post_status' => 'publish',
            );
            $query = new WP_Query($order_args);
            $count_orders = $query->found_posts;
            wp_reset_postdata();
            $result = count_users();


            echo '<div class="counter-sec">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-xs-4 ">
                                    <div class="counter-holder counter-one">
                                        <div class="text-holder">
                                            <i class="icon- icon-check-circle" ' . $statics_text_color . '></i>
                                            <strong class="count" ' . $statics_text_color . '>' . $count_rest . '</strong>
                                            <span ' . $statics_text_color . '>' . __('Restaurant', 'foodbakery') . '</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-xs-4 ">
                                    <div class="counter-holder counter-two">
                                        <div class="text-holder">
                                            <i class="icon- icon-check-circle" ' . $statics_text_color . '></i>
                                            <strong class="count" ' . $statics_text_color . '>' . $count_orders . '</strong>
                                            <span ' . $statics_text_color . '>' . __('People Served', 'foodbakery') . '</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-xs-4 ">
                                    <div class="counter-holder counter-three">
                                        <div class="text-holder">
                                            <i class="icon- icon-check-circle" ' . $statics_text_color . '></i>
                                            <strong class="count" ' . $statics_text_color . '>' . $result['total_users'] . '</strong>
                                            <span ' . $statics_text_color . '>' . __('Registered Users', 'foodbakery') . '</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';

            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '</div>';
            }

            $post_data = ob_get_clean();
            return $post_data;
        }

    }

    global $foodbakery_shortcode_statics_front;
    $foodbakery_shortcode_statics_front = new Foodbakery_Statics_Categories_front();
}