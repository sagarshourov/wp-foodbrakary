<?php
/**
 * File Type: Searchs Shortcode Frontend
 */
if (!class_exists('Foodbakery_Shortcode_About_Info_front')) {

    class Foodbakery_Shortcode_About_Info_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'about_info';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_about_info_shortcode_callback'));
        }

        /*
         * Shortcode View on Frontend
         */

        public function foodbakery_about_info_shortcode_callback($atts, $content = "") {
            global $column_container, $foodbakery_form_fields_frontend, $foodbakery_plugin_options, $current_user;

            $html = '';
            $page_element_size = isset($atts['about_info_element_size']) ? $atts['about_info_element_size'] : 100;
            $about_info_time = isset($atts['about_info_time']) ? $atts['about_info_time'] : '';
            $about_info_phone = isset($atts['about_info_phone']) ? $atts['about_info_phone'] : '';
            $about_info_location = isset($atts['about_info_location']) ? $atts['about_info_location'] : '';


            ob_start();
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
            }
            if ($about_info_time || $about_info_phone || $about_info_location) {
                ?>
                <div class="contact-area">
                    <?php if ($about_info_time) { ?>
                        <span class="time">
                            <a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html($about_info_time); ?>"><i class="icon-clock-o"></i></a>
                        </span>
                    <?php } ?>
                    <?php if ($about_info_phone) { ?> 
                        <span class="phone">
                            <a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html($about_info_phone); ?>"><i class="icon-phone5"></i></a>
                        </span>
                    <?php } ?>
                    <?php if ($about_info_location) { ?>   
                        <span class="location">
                            <a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html($about_info_location); ?>"><i class="icon-location-pin"></i></a>
                        </span>
                    <?php } ?>
                </div>
                <?php
            }
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '</div>';
            }
            $html = ob_get_clean();
            return $html;
        }

    }

    global $foodbakery_shortcode_restaurant_search_front;
    $foodbakery_shortcode_restaurant_search_front = new Foodbakery_Shortcode_About_Info_front();
}
