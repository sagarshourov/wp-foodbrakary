<?php
/**
 * File Type: Services Page Element
 */
if (!class_exists('foodbakery_contact_element')) {

    class foodbakery_contact_element {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('foodbakery_contact_element_html', array($this, 'foodbakery_contact_element_html_callback'), 11, 1);
        }

        /*
         * Output features html for frontend on restaurant detail page.
         */

        public function foodbakery_contact_element_html_callback($post_id) {
            global $foodbakery_plugin_options;

            // restaurant type fields
            $foodbakery_restaurant_type = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
            $list_type = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type');

            $restaurant_type_id = isset($list_type) ? $list_type->ID : '';
            $restaurant_type_marker_image_id = get_post_meta($restaurant_type_id, 'foodbakery_restaurant_type_marker_image', true);
            $restaurant_type_marker_image = empty($restaurant_type_marker_image_id) ? wp_foodbakery::plugin_url() . '/assets/frontend/images/map-marker.png' : wp_get_attachment_url($restaurant_type_marker_image_id);

            $foodbakery_restaurant_type_loc_map_switch = get_post_meta($restaurant_type_id, 'foodbakery_location_element', true);
            $foodbakery_restaurant_type_open_hours_switch = get_post_meta($restaurant_type_id, 'foodbakery_opening_hours_element', true);

            // restaurant fields
            $foodbakery_post_comp_address = get_post_meta($post_id, 'foodbakery_post_loc_address_restaurant', true);
            $foodbakery_post_loc_latitude = get_post_meta($post_id, 'foodbakery_post_loc_latitude_restaurant', true);
            $foodbakery_post_loc_longitude = get_post_meta($post_id, 'foodbakery_post_loc_longitude_restaurant', true);
            $foodbakery_loc_radius_restaurant = get_post_meta($post_id, 'foodbakery_loc_radius_restaurant', true);

            if ($foodbakery_post_loc_latitude == '' && $foodbakery_post_loc_longitude == '') {
                $foodbakery_post_loc_latitude = isset($foodbakery_plugin_options['foodbakery_post_loc_latitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_latitude'] : '';
                $foodbakery_post_loc_longitude = isset($foodbakery_plugin_options['foodbakery_post_loc_longitude']) ? $foodbakery_plugin_options['foodbakery_post_loc_longitude'] : '';
            }
            // user profile fields

            $user_profile_data = get_post_meta($post_id, 'foodbakery_user_profile_data', true);

            $foodbakery_user_phone_number = get_post_meta($post_id, 'foodbakery_restaurant_contact_phone', true);
            $foodbakery_post_loc_address_restaurant = get_post_meta($post_id, 'foodbakery_post_loc_address_restaurant', true);
            $foodbakery_user_website = get_post_meta($post_id, 'foodbakery_restaurant_contact_web', true);
            $foodbakery_user_email = get_post_meta($post_id, 'foodbakery_restaurant_contact_email', true);
            $phone_number_limit = foodbakery_cred_limit_check($post_id, 'foodbakery_transaction_restaurant_phone');
            $website_limit = foodbakery_cred_limit_check($post_id, 'foodbakery_transaction_restaurant_website');
            $map_zoom_level_default = isset($foodbakery_plugin_options['foodbakery_map_zoom_level']) ? $foodbakery_plugin_options['foodbakery_map_zoom_level'] : '10';
            $map_zoom_level_post = get_post_meta($post_id, 'foodbakery_post_loc_zoom_restaurant', true);
            if ($map_zoom_level_post == '' || !isset($map_zoom_level_post)) {
                $map_zoom_level_post = $map_zoom_level_default;
            }

            $foodbakery_post_comp_address = wp_trim_words($foodbakery_post_comp_address, 12);
            if (($foodbakery_restaurant_type_loc_map_switch == 'on' && $foodbakery_post_loc_longitude != '' && $foodbakery_post_loc_latitude != '') || $foodbakery_restaurant_type_open_hours_switch == 'on') {
                ?>
                <div class="contact-info-detail">
                    <h5><?php printf(esc_html__('Overview %s', 'foodbakery'), get_the_title($post_id)) ?></h5>
                    <?php $restaurant_info = get_post( $post_id ); ?>
                    <?php if(isset($restaurant_info) && !empty($restaurant_info)){ ?>
                        <p class="restaurant-desc">
                            <?php echo apply_filters('the_content', $restaurant_info->post_content); ?>
                        </p>
                    <?php } ?>
                    <?php if ($foodbakery_restaurant_type_loc_map_switch == 'on' && $foodbakery_post_loc_longitude != '' && $foodbakery_post_loc_latitude != '') { ?>
                        <div class="map-sec-holder">
                            <script>
                                var map;
                            </script>
                            <?php
                            $map_atts = array(
                                'map_height' => '180',
                                'map_lat' => $foodbakery_post_loc_latitude,
                                'map_lon' => $foodbakery_post_loc_longitude,
                                'map_zoom' => $map_zoom_level_post,
                                'map_type' => '',
                                'map_info' => '', //$foodbakery_post_comp_address,
                                'map_info_width' => '200',
                                'map_info_height' => '200',
                                'map_marker_icon' => $restaurant_type_marker_image,
                                'map_show_marker' => 'true',
                                'map_controls' => 'false',
                                'map_draggable' => 'true',
                                'map_scrollwheel' => 'false',
                                'map_border' => '',
                                'map_border_color' => '',
                                'foodbakery_map_style' => '',
                                'foodbakery_map_class' => '',
                                'foodbakery_map_directions' => 'off',
                                'foodbakery_map_circle' => $foodbakery_loc_radius_restaurant,
                            );
                            foodbakery_map_content($map_atts);
                            ?>
                            <script>
                                jQuery(document).ready(function ($) {
                                    $("a[href='#menu3']").on('shown.bs.tab', function () {
                                        var center = map.getCenter();
                                        google.maps.event.trigger(map, 'resize');
                                        map.setCenter(center);
                                    });
                                });
                            </script>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <?php
                        $contact_flag = false;
                        if (( $phone_number_limit == 'on' && $foodbakery_user_phone_number != '' ) || ( $website_limit == 'on' && $foodbakery_user_website != '' ) || $foodbakery_user_email != '' || $foodbakery_post_comp_address != '') {
                            $contact_flag = true;
                        }
                        if ($foodbakery_restaurant_type_loc_map_switch == 'on') {
                            ?>
                            <?php if ($contact_flag) { ?>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="contact-info">
                                        <h5><?php _e('Contact details', 'foodbakery'); ?></h5>
                                        <p><?php echo esc_html($foodbakery_post_loc_address_restaurant); ?></p>

                                        <ul>
                                            <?php if ($phone_number_limit == 'on' && $foodbakery_user_phone_number != '') { ?>
                                                <li class="cell"><i class="icon-phone"></i><a href="tel:<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', $foodbakery_user_phone_number); ?>"><?php echo esc_html($foodbakery_user_phone_number); ?></a></li>
                                            <?php } ?>	
                                            <?php if ($website_limit == 'on' && $foodbakery_user_website != '') { ?>
                                                <li class="pizzaeast"><i class="icon-globe2"></i><a href="<?php echo esc_url($foodbakery_user_website); ?>"><?php echo esc_url($foodbakery_user_website); ?></a></li>
                                            <?php } ?>	
                                            <?php if ($foodbakery_user_email != '') { ?>
                                                <li class="email"><i class="icon-mail5"></i><a class="text-color" href="mailto:<?php echo esc_html($foodbakery_user_email); ?>"><?php echo esc_html__('Send Enquiry By Email', 'foodbakery'); ?></a></li>
                                            <?php } ?>	
                                        </ul>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php
                        }
                        if ($foodbakery_restaurant_type_open_hours_switch == 'on') {
                            ?>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <?php do_action('foodbakery_opening_hours_element_html', $post_id); ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }

    }

    global $foodbakery_contact_element;
    $foodbakery_contact_element = new foodbakery_contact_element();
}