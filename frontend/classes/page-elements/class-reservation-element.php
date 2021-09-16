<?php
/**
 * File Type: Services Page Element
 */
if (!class_exists('foodbakery_reservation_element')) {

    class foodbakery_reservation_element {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('foodbakery_reservation_element_html', array($this, 'foodbakery_reservation_element_html_callback'), 11, 1);
            add_action('wp_ajax_foodbakery_reservation_submit', array($this, 'foodbakery_reservation_submit_callback'));
            add_action('wp_ajax_nopriv_foodbakery_reservation_submit', array($this, 'foodbakery_reservation_submit_callback'));

            add_action('wp_ajax_foodbakery_restaurant_add_menu_item', array($this, 'foodbakery_restaurant_add_menu_item'));
            add_action('wp_ajax_nopriv_foodbakery_restaurant_add_menu_item', array($this, 'foodbakery_restaurant_add_menu_item'));

            add_action('wp_ajax_foodbakery_restaurant_set_fee_type', array($this, 'foodbakery_restaurant_set_fee_type'));
            add_action('wp_ajax_nopriv_foodbakery_restaurant_set_fee_type', array($this, 'foodbakery_restaurant_set_fee_type'));

            add_action('wp_ajax_foodbakery_restaurant_remove_menu_item', array($this, 'foodbakery_restaurant_remove_menu_item'));
            add_action('wp_ajax_nopriv_foodbakery_restaurant_remove_menu_item', array($this, 'foodbakery_restaurant_remove_menu_item'));

            add_action('wp_ajax_foodbakery_restaurant_order_confirm', array($this, 'foodbakery_restaurant_order_confirm'));
            add_action('wp_ajax_nopriv_foodbakery_restaurant_order_confirm', array($this, 'foodbakery_restaurant_order_confirm'));

            add_action('wp_ajax_convert_date', array($this, 'convert_date_callback'), 11, 1);
            add_action('wp_ajax_nopriv_convert_date', array($this, 'convert_date_callback'), 11, 1);
            add_action('wp_ajax_calculate_service_price', array($this, 'calculate_service_price_callback'), 11, 1);
            add_action('wp_ajax_nopriv_calculate_service_price', array($this, 'calculate_service_price_callback'), 11, 1);

              add_action('wp_ajax_ansu_repeat_order', array($this, 'ansu_repeat_order'));
        }

        public function ansu_repeat_order(){

            $post_id = $_POST['order_id'];
            $title   = get_the_title($post_id);
            $oldpost = get_post($post_id);
            $post    = array(
              'post_title' => $title,
              'post_status' => 'publish',
              'post_type' => $oldpost->post_type,
              'post_author' => get_current_user_id(),
            );
            $new_post_id = wp_insert_post($post);
            // Copy post metadata
            $data = get_post_custom($post_id);
            foreach ( $data as $key => $values) {
              foreach ($values as $value) {
                if($key == 'menu_items_list'){
                    $value = unserialize($value);
                }
                add_post_meta( $new_post_id, $key, $value );
              }
            }
            $time = time();
            update_post_meta($new_post_id,'foodbakery_order_date',$time);
            update_post_meta($new_post_id,'foodbakery_delivery_date',$time);
            update_post_meta($new_post_id,'foodbakery_order_status','process');
            update_post_meta($new_post_id,'foodbakery_order_payment_status','pending');

            $rest_id = get_post_meta($new_post_id,'foodbakery_restaurant_id',true);

            $return = array(
                'order_id'  => $new_post_id,
                'rest_id'   => $rest_id,
            );
            wp_send_json($return);
            die(0);
        }

        /*
         * Output features html for frontend on restaurant detail page.
         */

        public function foodbakery_reservation_element_html_callback($post_id) {
            global $foodbakery_form_fields, $foodbakery_plugin_options;

            $foodbakery_currency_sign = foodbakery_get_currency_sign();
            $restaurant_type_slug = get_post_meta($post_id, 'foodbakery_restaurant_type', true);
            $restaurant_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => $restaurant_type_slug, 'post_status' => 'publish'));
            $restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;

            $restaurant_publisher_company = get_post_meta($post_id, 'foodbakery_restaurant_username', true);
            $restaurant_publisher = foodbakery_user_id_form_company_id($restaurant_publisher_company);

            $user_id = $company_id = 0;
            $user_id = get_current_user_id();
            if ($user_id != 0) {
                $company_id = get_user_meta($user_id, 'foodbakery_company', true);
            }

            $form_title = get_post_meta($restaurant_type_id, "foodbakery_form_title", true);
            $inquiry_paid = get_post_meta($restaurant_type_id, "foodbakery_inquiry_paid_form", true);
            $reservation_fields = get_post_meta($restaurant_type_id, "foodbakery_restaurant_type_reservation_fields", true);
            $form_button_label = get_post_meta($restaurant_type_id, "foodbakery_form_button_label", true);
            $form_terms_link = get_post_meta($restaurant_type_id, "foodbakery_form_terms_link", true);
            $form_button_label = isset($form_button_label) && $form_button_label != '' ? $form_button_label : esc_html__('Reserve My Spot', 'foodbakery');
            $file_field = false;
            if (!empty($reservation_fields)) {
                ?>
                <div class="widget widget-reservation">
                    <div class="request-form">
                        <form id="reservation-form" name="reservation-form" method="post" enctype="multipart/form-data">
                            <?php if ($form_title != '') { ?>
                                <div class="widget-title">
                                    <h3><?php echo esc_html($form_title); ?></h3>
                                </div>
                            <?php } ?>
                            <div class="select-holder row">
                                <?php
                                foreach ($reservation_fields as $reservation_field) {
                                    $field_type = isset($reservation_field['type']) ? $reservation_field['type'] : '';
                                    $meta_key = isset($reservation_field['meta_key']) ? $reservation_field['meta_key'] : '';
                                    if ($meta_key != '' || $field_type == 'section' || ($field_type == 'services' && $inquiry_paid == 'on')) {
                                        $field_label = isset($reservation_field['label']) ? $reservation_field['label'] : '';
                                        $field_placeholder = isset($reservation_field['placeholder']) ? $reservation_field['placeholder'] : '';
                                        $default_value = isset($reservation_field['default_value']) ? $reservation_field['default_value'] : '';
                                        $field_size = isset($reservation_field['field_size']) ? $reservation_field['field_size'] : '';
                                        $field_required = isset($reservation_field['required']) ? $reservation_field['required'] : '';
                                        $field_placeholder = $field_required == 'on' ? $field_placeholder . '*' : $field_placeholder;

                                        // Field Size
                                        switch ($field_size) {
                                            case "small":
                                                $col_size = '4';
                                                break;
                                            case "medium":
                                                $col_size = '8';
                                                break;
                                            case "large":
                                                $col_size = '12';
                                                break;
                                            default :
                                                $col_size = '12';
                                                break;
                                        }

                                        $rand_id = rand(123, 987);
                                        $field_name = esc_html($meta_key);
                                        ?>
                                        <div class="input-field col-lg-<?php echo absint($col_size); ?> col-md-<?php echo absint($col_size); ?> col-sm-12 col-xs-12">
                                            <?php
                                            if ($field_type == 'time') {
                                                echo '<div class="row"><div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
                                            }
                                            // Field Label
                                            if ($field_label != '' && $field_type != 'availability' && $field_type != 'file' && $field_type != 'section') {

                                                echo '<label>' . esc_html($field_label) . ':</label>';
                                            }
                                            $fields_types = array('text', 'number', 'email', 'url', 'email', 'textarea');

                                            if ($field_type == 'section') {
                                                echo '<section>' . $field_label . '</sction>';
                                            } else if (in_array($field_type, $fields_types)) {

                                                $foodbakery_opt_array = array();

                                                $foodbakery_opt_array['std'] = $default_value;
                                                $foodbakery_opt_array['cust_id'] = $meta_key;
                                                $foodbakery_opt_array['cust_name'] = $field_name;
                                                $foodbakery_opt_array['extra_atr'] = ' placeholder="' . esc_html($field_placeholder) . '"';
                                                $foodbakery_opt_array['return'] = false;

                                                if ($field_required == 'on') {
                                                    if ($field_type == 'email') {
                                                        $foodbakery_opt_array['classes'] = 'foodbakery-email-required-field';
                                                    } else {
                                                        $foodbakery_opt_array['classes'] = 'foodbakery-required-field';
                                                    }
                                                }

                                                if ($field_type == 'textarea') {
                                                    $foodbakery_opt_array['classes'] = 'text-note';
                                                    if ($field_required == 'on') {
                                                        $foodbakery_opt_array['classes'] = 'foodbakery-required-field text-note';
                                                    }
                                                    $foodbakery_form_fields->foodbakery_form_textarea_render($foodbakery_opt_array);
                                                } else {
                                                    $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
                                                }
                                            } elseif ($field_type == 'services' || $field_type == 'range' || $field_type == 'dropdown' || $field_type == 'time') {

                                                $cust_id = '';

                                                if ($field_type == 'services') {
                                                    $cust_id = 'services';
                                                } else {
                                                    $cust_id = $field_name;
                                                }
                                                if ($field_type == 'time') {
                                                    $search_option = 'disable_search: true';
                                                } else {
                                                    $search_option = 'disable_search_threshold: 5';
                                                }
                                                echo '<script>
							jQuery(document).ready(function(){
								jQuery("#' . $cust_id . '").chosen({
									' . $search_option . '
								});
							});
						</script>';

                                                $drop_down_options = array();
                                                if ($field_type == 'services') {
                                                    $services_list = get_post_meta($post_id, 'foodbakery_services', true);
                                                    if (!empty($services_list)) {
                                                        if (isset($reservation_field['services_dropdown']) && $reservation_field['services_dropdown'] == 'yes') {

                                                            echo '<div class="services-selection-holder">';
                                                            echo '<select id="services" class="chosen-select-no-single">';
                                                            echo '<option data-number="" data-price="" value="">' . esc_html__('- Select Services -', 'foodbakery') . '</option>';
                                                            foreach ($services_list as $key => $service_list) {
                                                                $service_title = isset($service_list['service_title']) ? $service_list['service_title'] : '';
                                                                $service_price = isset($service_list['service_price']) ? $service_list['service_price'] : '0';
                                                                $drop_down_options[$service_price] = esc_html($service_title) . ' (' . $service_price . ')';
                                                                echo '<option data-number="' . $key . $service_price . '" data-price="' . $service_price . '" value="' . $service_title . '">' . esc_html($service_title) . ' (' . foodbakery_get_currency($service_price, true) . ')</option>';
                                                            }
                                                            echo '</select>';
                                                            echo '</div>';
                                                        }
                                                        echo '<div id="services-holder" class="services-holder" style="display: none">';
                                                        echo '<input type="hidden" id="currency_sign" value="' . $foodbakery_currency_sign . '">';
                                                        echo '<input type="hidden" id="services_total_quantity" name="services_total_quantity" value="0">';
                                                        echo '<ul>';
                                                        echo '</ul>';
                                                        echo '</div>';
                                                    }
                                                } else if ($field_type == 'range') {

                                                    $min_val = isset($reservation_field['min']) ? $reservation_field['min'] : '1';
                                                    $max_val = isset($reservation_field['max']) ? $reservation_field['max'] : '10';
                                                    $increment = isset($reservation_field['increment']) ? $reservation_field['increment'] : '1';
                                                    while ($min_val <= $max_val) {
                                                        $drop_down_options[intval($min_val)] = intval($min_val);
                                                        $min_val = $min_val + $increment;
                                                    }
                                                } else if ($field_type == 'dropdown') {
                                                    if (isset($reservation_field['options']) && !empty($reservation_field['options'])) {
                                                        $first_value = isset($reservation_field['first_value']) ? $reservation_field['first_value'] : '';
                                                        if ($first_value != '') {
                                                            $drop_down_options[''] = esc_html($first_value);
                                                        }
                                                        foreach ($reservation_field['options']['label'] as $key => $value) {
                                                            $drop_down_options[esc_html($reservation_field['options']['value'][$key])] = esc_html($value);
                                                        }
                                                    }
                                                } else if ($field_type == 'time') {

                                                    $time_lapse = isset($reservation_field['time_lapse']) ? $reservation_field['time_lapse'] : '15';
                                                    $time_list = $this->restaurant_time_list($time_lapse);
                                                    if (is_array($time_list) && sizeof($time_list) > 0) {
                                                        foreach ($time_list as $time_key => $time_val) {
                                                            $drop_down_options[$time_key] = esc_html($time_val);
                                                        }
                                                    }
                                                }

                                                if (!empty($drop_down_options) && $field_type != 'services') {

                                                    $foodbakery_opt_array = array();
                                                    $foodbakery_opt_array['std'] = '';
                                                    $foodbakery_opt_array['cust_id'] = $cust_id;
                                                    $foodbakery_opt_array['cust_name'] = $field_name;
                                                    $foodbakery_opt_array['options'] = $drop_down_options;
                                                    $foodbakery_opt_array['classes'] = 'chosen-select-no-single';
                                                    $foodbakery_opt_array['extra_atr'] = ' placeholder="' . esc_html($field_placeholder) . '"';
                                                    $foodbakery_opt_array['return'] = false;

                                                    $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
                                                }
                                            } else if ($field_type == 'file') {
                                                ?>
                                                <div class="upload-file">
                                                    <input type="file" value="" id="file" name="<?php echo esc_html($field_name); ?>" onchange="checkName(this, '<?php echo absint($rand_id); ?>')">
                                                    <label for="file"><span><?php echo isset($field_label) ? esc_html($field_label) : esc_html__('Choose file', 'foodbakery'); ?></span></label>
                                                </div>
                                                <div id="user-selected-file-<?php echo absint($rand_id); ?>"></div>
                                                <span class="status-msg-<?php echo absint($rand_id); ?>"><?php esc_html_e('Suitable files are .doc, docx, rft, pdf & .pdf', 'foodbakery'); ?></span>
                                                <?php $file_field = true; ?>
                                                <?php
                                            } else if ($field_type == 'availability') {
                                                $off_days = $this->foodbakery_off_opening_days_callback($post_id);
                                                $foodbakery_restaurant_off_days = get_post_meta($post_id, 'foodbakery_restaurant_off_days', true);

                                                $comma = $restaurant_off_days = '';
                                                if (is_array($foodbakery_restaurant_off_days) && !empty($foodbakery_restaurant_off_days)) {
                                                    foreach ($foodbakery_restaurant_off_days as $foodbakery_restaurant_off_day) {
                                                        $restaurant_off_days .= $comma . '"' . date('d-m-Y', strtotime($foodbakery_restaurant_off_day)) . '"';
                                                        $comma = ',';
                                                    }
                                                }
                                                ?>
                                                <div id="datepicker_<?php echo esc_html($post_id); ?>" class="reservaion-calendar"><label class="availability"><?php echo esc_html($field_label); ?>:</label></div>
                                                <?php wp_enqueue_style('foodbakery_datepicker_css'); ?>
                                                <script>
                                                    jQuery(function (jQuery) {
                                                        var disabledDays = [<?php echo esc_html($restaurant_off_days); ?>];
                                                        //replace these with the id's of your datepickers
                                                        jQuery("#datepicker_<?php echo esc_html($post_id); ?>").datepicker({
                                                            showOtherMonths: true,
                                                            firstDay: 1,
                                                            minDate: 0,
                                                            dateFormat: 'dd-mm-yy',
                                                            prevText: '',
                                                            nextText: '',
                                                            monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                                                            beforeShowDay: function (date) {
                                                                var day = date.getDay();
                                                                var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                                                                var isDisabled = (jQuery.inArray(string, disabledDays) != -1);
                                                                //day != 0 disables all Sundays
                                                                return [<?php echo esc_html($off_days); ?> !isDisabled];
                                                            },
                                                            onSelect: function (date) {
                                                                jQuery("#order_date").val(date);
                                                            }
                                                        });
                                                    });
                                                </script>
                                                <ul class="calendar-options">
                                                    <li class="avilable"><?php esc_html_e('Available', 'foodbakery'); ?></li>
                                                    <li class="unavailable"><?php esc_html_e('Unavailable', 'foodbakery'); ?></li>
                                                    <li class="booking"><?php esc_html_e('Booked', 'foodbakery'); ?></li>
                                                </ul>
                                                <?php
                                            }
                                            if ($field_type == 'time') {
                                                echo '</div>';
                                                ?>
                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                    <?php
                                                    if ($field_label != '') {
                                                        echo '<label>&nbsp</label>';
                                                    }
                                                    ?>
                                                    <div class="chickbox-holder">
                                                        <div class="checkbox">
                                                            <input type="radio" value="AM" id="am" name="time_format" checked="checked">
                                                            <label for="am"><?php esc_html_e('AM', 'foodbakery'); ?></label>
                                                        </div>
                                                        <div class="checkbox">
                                                            <input type="radio" value="PM" id="pm" name="time_format">
                                                            <label for="pm"><?php esc_html_e('PM', 'foodbakery'); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            if ($field_type == 'time') {
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>

                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div id="result"></div>
                            <?php if (is_user_logged_in()) { ?>
                                <button type="button" class="reserve-btn bgcolor" onclick="javascript:foodbakery_reservation_submit('<?php echo admin_url('admin-ajax.php'); ?>', '<?php echo esc_html($file_field); ?>')"><?php echo esc_html($form_button_label); ?></button> 
                            <?php } else { ?>
                                <button type="button" data-target="#sign-in" data-toggle="modal" class="reserve-btn bgcolor"><?php echo esc_html($form_button_label); ?></button> 
                            <?php } ?>
                            <input type="hidden" id="order_date" name="foodbakery_order_date" value="">
                            <input type="hidden" id="services_total_price" name="services_total_price" value="0">
                            <input type="hidden" name="foodbakery_publisher" value="<?php echo intval($restaurant_publisher); ?>">
                            <input type="hidden" name="foodbakery_publisher_company" value="<?php echo intval($restaurant_publisher_company); ?>">
                            <input type="hidden" name="foodbakery_restaurant_id" value="<?php echo intval($post_id); ?>">
                            <input type="hidden" name="foodbakery_restaurant_type_id" value="<?php echo intval($restaurant_type_id); ?>">
                            <input type="hidden" name="foodbakery_order_user" value="<?php echo intval($user_id); ?>">
                            <input type="hidden" name="foodbakery_order_user_company" value="<?php echo intval($company_id); ?>">
                            <input type="hidden" name="action" value="foodbakery_reservation_submit" >
                            <div id="total_price_holder" style="display:none;"><span></span><?php esc_html_e(' / Per Night', 'foodbakery'); ?></div>
                                    <?php if ($form_terms_link) { ?>
                                <p><?php echo force_balance_tags($form_terms_link); ?></p>
                            <?php } ?>
                        </form>
                    </div>
                </div>
                <?php
            }
        }

        public function restaurant_time_list($lapse = 15) {
            $hours = array();
            $start = '12:00AM';
            $end = '11:59AM';
            $interval = '+' . $lapse . ' minutes';

            $start_str = strtotime($start);
            $end_str = strtotime($end);
            $now_str = $start_str;
            while ($now_str <= $end_str) {
                $hours[date('h:i', $now_str)] = date('h:i', $now_str);
                $now_str = strtotime($interval, $now_str);
            }
            return $hours;
        }

        public function foodbakery_off_opening_days_callback($post_id) {
            $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
            $opening_hours_list = array();
            foreach ($days as $key => $day) {
                $opening_time = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true);
                $opening_time = ( $opening_time != '' ? date('h:i a', $opening_time) : '' );
                $closing_time = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true);
                $closing_time = ( $opening_time != '' ? date('h:i a', $closing_time) : '' );
                $opening_hours_list[$day] = array(
                    'day_status' => get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_day_status', true),
                    'opening_time' => $opening_time,
                    'closing_time' => $closing_time,
                );
            }
            $days = array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
            $off_days = '';
            if (is_array($opening_hours_list) && !empty($opening_hours_list))
                foreach ($opening_hours_list as $key => $value) {
                    if (isset($value['day_status']) && $value['day_status'] != 'on') {
                        $off_days .= ' day != ' . $days[$key] . ' && ';
                    }
                }
            return $off_days;
        }

        private function sa_quantuty_reverse($resturent_id, $qtys, $menu_id, $extras, $position) {

            $restaurant_menu_list = get_post_meta($resturent_id, 'foodbakery_menu_items', true);

            $arr_extra = explode(',', $extras);

            $arr_position = explode(',', $position);


            $menu_item_arr = explode(',', $menu_id);
            $qty = (int) $qtys;

            foreach ($arr_extra as $key => $extra_id) {
                if ($extra_id !== '') {
                    $extra_id = (int) $extra_id;
                    $position_id = (int) $arr_position[$key];
                    
                    $menu_item_id = (int) $menu_item_arr[$key];
                    
                    $old_quantity = $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id];
                    if ($old_quantity !== 0 && empty($old_quantity)) {
                        $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id] = '';
                    } else {
                        $old_quantity = (int) $old_quantity;
                        $new_quentity = $old_quantity + $qty;
                        $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id] = $new_quentity;
                    }
                }
            }
            update_post_meta($resturent_id, 'foodbakery_menu_items', $restaurant_menu_list);


            return $menu_id;
        }

        public function foodbakery_restaurant_remove_menu_item() {

            global $current_user;

            $restaurant_id = foodbakery_get_input('_rid', 0);
            $menu_item_id = foodbakery_get_input('_id', 0);

            $qty = foodbakery_get_input('qty', 1);



            $sa_extra = isset($_POST['extra']) ? $_POST['extra'] : '';

            $sa_position = isset($_POST['position']) ? $_POST['position'] : '';

            $sa_menu_id = isset($_POST['menu_id']) ? $_POST['menu_id'] : '';



            $user_id = $current_user->ID;
            $publisher_id = foodbakery_company_id_form_user_id($user_id);
            $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
            $get_added_menus = array();

            if ($publisher_id != '' && $publisher_type != '' && $publisher_type != 'restaurant') {
                $get_added_menus = get_transient('add_menu_items_' . $publisher_id);

                if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }

                if (isset($get_added_menus[$restaurant_id][$menu_item_id])) {
                    unset($get_added_menus[$restaurant_id][$menu_item_id]);
                    if (is_array($get_added_menus[$restaurant_id]) && sizeof($get_added_menus[$restaurant_id]) > 0) {
                        $get_added_menus_new = array();
                        $get_added_menus_new[$restaurant_id] = array();
                        foreach ($get_added_menus[$restaurant_id] as $get_added_menu) {
                            $get_added_menus_new[$restaurant_id][] = $get_added_menu;
                        }
                    } else {
                        $get_added_menus_new = '';
                    }
                    delete_transient('add_menu_items_' . $publisher_id);
                    set_transient('add_menu_items_' . $publisher_id, $get_added_menus_new, 60 * 60 * 24 * 30);

                    setcookie('add_menu_items_temp', serialize($get_added_menus_new), time() + (10 * 365 * 24 * 60 * 60), '/');
                }
            } else {
                if (isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }

                if (isset($get_added_menus[$restaurant_id][$menu_item_id])) {
                    unset($get_added_menus[$restaurant_id][$menu_item_id]);
                    if (is_array($get_added_menus[$restaurant_id]) && sizeof($get_added_menus[$restaurant_id]) > 0) {
                        $get_added_menus_new = array();
                        $get_added_menus_new[$restaurant_id] = array();
                        foreach ($get_added_menus[$restaurant_id] as $get_added_menu) {
                            $get_added_menus_new[$restaurant_id][] = $get_added_menu;
                        }
                    } else {
                        $get_added_menus_new = '';
                    }

                    setcookie('add_menu_items_temp', '', time() + (10 * 365 * 24 * 60 * 60), '/');
                    setcookie('add_menu_items_temp', serialize($get_added_menus_new), time() + (10 * 365 * 24 * 60 * 60), '/');
                }
            }

           //$this->sa_quantuty_reverse($restaurant_id, $qty, $sa_menu_id, $sa_extra, $sa_position);
          
       
            die();
        }

        public function foodbakery_restaurant_set_fee_type() {
            global $current_user;

            $restaurant_id = foodbakery_get_input('_rid', 0);
            $fee_type = foodbakery_get_input('this_type', '');
            $jus_pl = false;
            if (!is_user_logged_in()) {
                $jus_pl = true;
            } else {
                $user_id = $current_user->ID;
                $publisher_id = foodbakery_company_id_form_user_id($user_id);
                if ($publisher_id != '') {
                    $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
                    if ($publisher_type != 'restaurant' && $publisher_type != '') {
                        $get_added_menus = get_transient('add_menu_items_' . $publisher_id);
                        if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp'])) {
                            $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                        }

                        if (is_array($get_added_menus) && sizeof($get_added_menus) > 0) {
                            $get_added_menus[$restaurant_id . '_fee_type'] = $fee_type;
                        } else {
                            $get_added_menus = array();
                            $get_added_menus[$restaurant_id . '_fee_type'] = $fee_type;
                        }
                        set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);
                    } else {
                        $jus_pl = true;
                    }
                } else {
                    $jus_pl = true;
                }
            }
            if ($jus_pl) {
                $get_added_menus = '';

                if (isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }

                if (is_array($get_added_menus) && sizeof($get_added_menus) > 0) {

                    $get_added_menus[$restaurant_id . '_fee_type'] = $fee_type;
                } else {
                    $get_added_menus = array();
                    $get_added_menus[$restaurant_id . '_fee_type'] = $fee_type;
                }

                setcookie('add_menu_items_temp', serialize($get_added_menus), time() + (10 * 365 * 24 * 60 * 60), '/');
            }
            die;
        }

        private function sa_quantity_process($data, $restaurant_id) {

            $restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);

            foreach ($data as $key2 => $value2) {


                $menu_item_id = (int) $value2['menu_item_id'];
                $extra_id = (int) $value2['extra_id'];
                $extra_quantity = (int) $value2['quantity'];

                $position_id = (int) $value2['position_id'];

                $old_quantity = $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id];

                if ($old_quantity == '') {
                    $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id] = '';
                } else {
                    $new_quentity = $old_quantity - $extra_quantity;

                    if ($new_quentity > -1) {
                        $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id] = $new_quentity;
                    }
                }
            }



            update_post_meta($restaurant_id, 'foodbakery_menu_items', $restaurant_menu_list);


            //return $restaurant_menu_list;
            // $restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
        }

        public function foodbakery_restaurant_add_menu_item() {
            global $foodbakery_plugin_options, $current_user;

            $foodbakery_currency_sign = foodbakery_get_currency_sign();
            $rand_numb = rand(10000000, 99999999);
            $rand_numb_class = isset($_POST['rand_number']) ? $_POST['rand_number'] : $rand_numb;
            $extra_notes = isset($_POST['extra_notes']) ? $_POST['extra_notes'] : '';
            $restaurant_id = foodbakery_get_input('_rid', 0);
            $menu_cat_id = foodbakery_get_input('menu_cat_id', 0);
            $menu_item_id = foodbakery_get_input('menu_id', 0);
            $menu_updating = isset($_POST['act_updating']) ? $_POST['act_updating'] : '';
            $menu_extra_atts = isset($_POST['extra_atts']) ? $_POST['extra_atts'] : '';
            $extra_name = isset($_POST['extra_name']) ? $_POST['extra_name'] : '';
            $unique_menu_id = isset($_POST['menu_unique_id_']) ? $_POST['menu_unique_id_'] : '';
            $restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);

            $menu_sa_quantity = isset($_POST['sa_quantity']) ? $_POST['sa_quantity'] : '';
//             $menu_extra_atts_exp = explode(',', $menu_extra_atts);
//            if(){
//                
//            }


            $unique_id = $rand_numb;

            if (isset($_POST['unique_id']) && $_POST['unique_id'] != 'undefined') {
                $unique_id = $_POST['unique_id'];
            }



            $this_item_heading = '';

            $jus_pl = false;
            if (!is_user_logged_in()) {
                $jus_pl = true;
            } else {
                $user_id = $current_user->ID;
                $publisher_id = foodbakery_company_id_form_user_id($user_id);

                if ($publisher_id != '') {
                    $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
                    if ($publisher_type != 'restaurant' && $publisher_type != '') {

                        $menu_t_price = 0;

                        // menu title
                        $this_item_title = isset($restaurant_menu_list[$menu_item_id]['menu_item_title']) ? $restaurant_menu_list[$menu_item_id]['menu_item_title'] : '';

                        // menu price
                        $this_item_price = isset($restaurant_menu_list[$menu_item_id]['menu_item_price']) ? $restaurant_menu_list[$menu_item_id]['menu_item_price'] : '';

                        // $menu_t_price += floatval($this_item_price);
                        $extras_arra = array();
                        $sa_category_price = 0;
                        $extras_html = '';
                        if ($menu_extra_atts != '') {

                            $menu_extra_atts = explode(',', $menu_extra_atts);
                            $extra_name = explode(',', $extra_name);
                            $menu_sa_quantity_arr = (int) $menu_sa_quantity;
                            if (is_array($menu_extra_atts)) {
                                //array_reverse($menu_extra_atts);
                                // menu extras
                                $this_item_extras = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra'] : '';

                                $menu_ext_counter = 0;
                                foreach ($menu_extra_atts as $key => $menu_extra_att) {
                                    //foreach ($menu_extra_atts as $menu_extra_att) {
                                    //$this_item_heading = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]]) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]] : '';
                                    $menu_extra_at_label = isset($this_item_extras[$extra_name[$key]]['title'][$menu_extra_att]) ? $this_item_extras[$extra_name[$key]]['title'][$menu_extra_att] : '';
                                    $menu_extra_at_price = isset($this_item_extras[$extra_name[$key]]['price'][$menu_extra_att]) ? $this_item_extras[$extra_name[$key]]['price'][$menu_extra_att] : '';

                                    //  $old_qty = $restaurant_menu_list[$menu_item_id]['menu_item_extra'][0]['quantity'][$menu_extra_att];


                                    $old_qty = isset($this_item_extras[$extra_name[$key]]['quantity'][$menu_extra_att]) ? $this_item_extras[$extra_name[$key]]['quantity'][$menu_extra_att] : '';

                                    if ($old_qty > 0 || $old_qty == '') {



                                        $extras_arra[] = array(
                                            'title' => $menu_extra_at_label,
                                            'price' => $menu_extra_at_price,
                                            'title_id' => $extra_name[$key],
                                            'menu_item_id' => $menu_item_id,
                                            'quantity' => $menu_sa_quantity_arr,
                                            'restaurant_id' => $restaurant_id,
                                            'position_id' => $extra_name[$key],
                                            'extra_id' => $menu_extra_att
                                        );

                                        $extras_html .= '<li class="sa_quantity_info" old_qty="'.$old_qty.'" menu_id="'.$menu_item_id.'" extra="' . $menu_extra_att . '" position="' . $extra_name[$key] . '" qty="' . $menu_sa_quantity_arr . '" dat="extra-' . $menu_extra_att . '-' . $extra_name[$key] . '-' . $menu_item_id . '">' . $menu_extra_at_label . ' x ' . $menu_sa_quantity_arr . ' : <span class="category-price">' . foodbakery_get_currency($menu_extra_at_price * $menu_sa_quantity_arr, true) . '</span></li>';

                                        $menu_t_price += floatval($menu_extra_at_price * $menu_sa_quantity_arr);

                                        $sa_category_price += floatval($menu_extra_at_price * $menu_sa_quantity_arr);
                                    } else {
                                        $extras_html .= '<li><span>out of stock </span></li>';
                                    }

                                    $menu_ext_counter++;
                                }
                            }
                            //$this->sa_quantity_process($extras_arra, $restaurant_id);
                        }

                        $get_added_menus = get_transient('add_menu_items_' . $publisher_id);

                        if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp']) && !is_user_logged_in()) {
                            $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                        }

                        if ($menu_updating == 'true') {
                            $menu_index = isset($_POST['menu_index']) ? $_POST['menu_index'] : '';
                            if (isset($get_added_menus[$restaurant_id][$menu_index])) {
                                $updated_menu = array(
                                    'menu_cat_id' => $menu_cat_id,
                                    'menu_id' => $menu_item_id,
                                    'price' => $this_item_price,
                                    'unique_id' => $unique_id,
                                    'unique_menu_id' => $unique_menu_id,
                                    'extras' => $extras_arra,
                                    'notes' => $extra_notes,
                                );
                                $get_added_menus[$restaurant_id][$menu_index] = $updated_menu;
                            }
                        } else {
                            if (is_array($get_added_menus) && sizeof($get_added_menus) > 0) {

                                $get_added_menus[$restaurant_id][] = array(
                                    'menu_cat_id' => $menu_cat_id,
                                    'menu_id' => $menu_item_id,
                                    'price' => $this_item_price,
                                    'unique_id' => $rand_numb,
                                    'unique_menu_id' => $unique_menu_id,
                                    'extras' => $extras_arra,
                                    'notes' => $extra_notes,
                                );
                            } else {
                                $get_added_menus = array();
                                $get_added_menus[$restaurant_id][] = array(
                                    'menu_cat_id' => $menu_cat_id,
                                    'menu_id' => $menu_item_id,
                                    'price' => $this_item_price,
                                    'unique_id' => $rand_numb,
                                    'unique_menu_id' => $unique_menu_id,
                                    'extras' => $extras_arra,
                                    'notes' => $extra_notes,
                                );
                            }
                        }

                        $li_html = '
				<li    class="menu-added-' . $rand_numb_class . '" id="menu-added-' . $rand_numb . '" data-pr="' . foodbakery_get_currency($menu_t_price, false, '', '', false) . '" data-conpr="' . foodbakery_get_currency($menu_t_price, false, '', '', true) . '">
					<a class="btn-cross dev-remove-menu-item" old_qty="'.$old_qty.'" qty="' . $menu_sa_quantity_arr . '" href="javascript:void(0)" class="btn-cross dev-remove-menu-item"><i class=" icon-cross3"></i></a>
					<a>' . $this_item_title . ' X ' . $menu_sa_quantity_arr . '</a>
					<span class="category-price">' . foodbakery_get_currency($menu_t_price, true) . '</span>';
                        if ($extras_html != '') {
                            $array_latest_added_menu = count($get_added_menus[$restaurant_id]) - 1;
                            $li_html .= '<ul>';
                            $li_html .= $extras_html;
                            $li_html .= $get_added_menus[$restaurant_id][$array_latest_added_menu]['notes'] !== '' ? '<li>' . $get_added_menus[$restaurant_id][$array_latest_added_menu]['notes'] . '</li>' : '';
                            $li_html .= '</ul>';

                            $popup_id = 'edit_extras-' . $menu_cat_id . '-' . $menu_item_id;
                            $data_id = $menu_item_id;
                            $ajax_url = admin_url('admin-ajax.php');

                            // $unique_id = $get_added_menus[$restaurant_id][$array_latest_added_menu]['unique_id'];
                            $extra_child_menu_id = isset($get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id']) ? $get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id'] : '';
                            // $li_html .= '<a href="javascript:void(0);" class="edit-menu-item dd update_menu_'.$rand_numb_class.'" onClick="foodbakery_edit_extra_menu_item(\'' . $popup_id . '\',\'' . $data_id . '\',\'' . $menu_cat_id . '\',\'' . $rand_numb_class . '\',\'' . $ajax_url . '\',\'' . $restaurant_id . '\',\'' . $unique_id . '\',\'' . $unique_menu_id . '\',\'' . $extra_child_menu_id . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>';
                            //$li_html .= '<a href="javascript:void(0)" data-toggle="modal" data-target="#extras-' . $menu_cat_id . '-' . $menu_item_id . '" data-id="' . $menu_item_id . '" data-cid="' . $menu_cat_id . '" data-rand="' . $rand_numb . '" class="update-menu dev-update-menu-btn">' . esc_html__('Edit', 'foodbakery') . '</a>';
                        }
                        $li_html .= '
						</li>';
                        set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);

                        if ($menu_updating == 'true') {
                            //$json = array('msg' => esc_html__('Menu item have been updated in your basket.', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html);
                            $json = array('li_html' => $li_html);
                        } else {
                            //$json = array('msg' => esc_html__('Menu item have been added in your basket', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html);
                            $json = array('li_html' => $li_html);
                        }
                    } else {
                        $jus_pl = true;
                    }
                } else {
                    $jus_pl = true;
                }
            }
            if ($jus_pl) {
                $menu_t_price = 0;
                $sa_category_price = 0;
                // menu title
                $this_item_title = isset($restaurant_menu_list[$menu_item_id]['menu_item_title']) ? $restaurant_menu_list[$menu_item_id]['menu_item_title'] : '';

                // menu price
                $this_item_price = isset($restaurant_menu_list[$menu_item_id]['menu_item_price']) ? $restaurant_menu_list[$menu_item_id]['menu_item_price'] : '';

                // $menu_t_price += floatval($this_item_price);
                $extras_arra = array();
                $extras_html = '';
                if ($menu_extra_atts != '') {

                    $menu_extra_atts = explode(',', $menu_extra_atts);
                    $extra_name = explode(',', $extra_name);
                    $menu_sa_quantity_arr = (int) $menu_sa_quantity;
                    if (is_array($menu_extra_atts)) {
                        // menu extras
                        $this_item_extras = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra'] : '';

                        //  $menu_ext_counter = 0;

                        foreach ($menu_extra_atts as $key => $menu_extra_att) {


                            //$this_item_heading = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]]) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]] : '';
                            $menu_extra_at_label = isset($this_item_extras[$extra_name[$key]]['title'][$menu_extra_att]) ? $this_item_extras[$extra_name[$key]]['title'][$menu_extra_att] : '';
                            $menu_extra_at_price = isset($this_item_extras[$extra_name[$key]]['price'][$menu_extra_att]) ? $this_item_extras[$extra_name[$key]]['price'][$menu_extra_att] : '';
                            //$old_qty = $restaurant_menu_list[$menu_item_id]['menu_item_extra'][0]['quantity'][$menu_extra_att];

                            $old_qty = isset($this_item_extras[$extra_name[$key]]['quantity'][$menu_extra_att]) ? $this_item_extras[$extra_name[$key]]['quantity'][$menu_extra_att] : '';

                            if ($old_qty > 0 || $old_qty == '') {


                                $extras_arra[] = array(
                                    'title' => $menu_extra_at_label,
                                    'price' => $menu_extra_at_price,
                                    'title_id' => $extra_name[$key],
                                    'menu_item_id' => $menu_item_id,
                                    'quantity' => $menu_sa_quantity_arr,
                                    'restaurant_id' => $restaurant_id,
                                    'position_id' => $extra_name[$key],
                                    'extra_id' => $menu_extra_att
                                );

                                // $extras_html .= '<li>' . $this_item_heading . ' - ' . $menu_extra_at_label . ' : <span class="category-price">' . foodbakery_get_currency($menu_extra_at_price, true) . '</span></li>';
                                $extras_html .= '<li menu_id="'.$menu_item_id.'" extra="' . $menu_extra_att . '" position="' . $extra_name[$key] . '" qty="' . $menu_sa_quantity_arr . '" dat="extra-' . $menu_extra_att . '-' . $extra_name[$key] . '-' . $menu_item_id . '">' . $menu_extra_at_label . ' x ' . $menu_sa_quantity_arr . ' : <span class="category-price">' . foodbakery_get_currency($menu_extra_at_price * $menu_sa_quantity_arr, true) . '</span></li>';
                                $menu_t_price += floatval($menu_extra_at_price * $menu_sa_quantity_arr);

                                $sa_category_price += floatval($menu_extra_at_price * $menu_sa_quantity_arr);
                            } else {
                                $extras_html .= '<li><span>out of stock </span></li>';
                            }

                            // $menu_ext_counter ++;
                        }
                        //$this->sa_quantity_process($extras_arra, $restaurant_id);
                    }
                }

                $get_added_menus = '';

                if (isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }

                if ($menu_updating == 'true') {
                    $menu_index = isset($_POST['menu_index']) ? $_POST['menu_index'] : '';
                    if (isset($get_added_menus[$restaurant_id][$menu_index])) {
                        $updated_menu = array(
                            'menu_cat_id' => $menu_cat_id,
                            'menu_id' => $menu_item_id,
                            'price' => $this_item_price,
                            'unique_id' => $unique_id,
                            'unique_menu_id' => $unique_menu_id,
                            'extra_child_menu_id' => rand(10000000, 99999999),
                            'extras' => $extras_arra,
                            'notes' => $extra_notes,
                        );
                        $get_added_menus[$restaurant_id][$menu_index] = $updated_menu;
                    }
                } else {
                    if (is_array($get_added_menus) && sizeof($get_added_menus) > 0) {

                        $get_added_menus[$restaurant_id][] = array(
                            'menu_cat_id' => $menu_cat_id,
                            'menu_id' => $menu_item_id,
                            'price' => $this_item_price,
                            'unique_id' => $rand_numb,
                            'unique_menu_id' => $unique_menu_id,
                            'extra_child_menu_id' => rand(10000000, 99999999),
                            'extras' => $extras_arra,
                            'notes' => $extra_notes,
                        );
                    } else {
                        $get_added_menus = array();
                        $get_added_menus[$restaurant_id][] = array(
                            'menu_cat_id' => $menu_cat_id,
                            'menu_id' => $menu_item_id,
                            'price' => $this_item_price,
                            'unique_id' => $rand_numb,
                            'unique_menu_id' => $unique_menu_id,
                            'extras' => $extras_arra,
                            'notes' => $extra_notes,
                        );
                    }
                }
                $li_html = '
				<li class="menu-added-' . $rand_numb_class . '" id="menu-added-' . $rand_numb . '" data-pr="' . foodbakery_get_currency($menu_t_price, false, '', '', false) . '" data-conpr="' . foodbakery_get_currency($menu_t_price, false, '', '', true) . '">
			    <a qty="' . $menu_sa_quantity . '" href="javascript:void(0)" class="btn-cross dev-remove-menu-item"><i class=" icon-cross3"></i></a>
			    <a>' . $this_item_title . ' X ' . $menu_sa_quantity_arr . ' </a>
			    <span class="category-price">' . foodbakery_get_currency($menu_t_price, true) . '</span>';
                if ($extras_html != '') {
                    $array_latest_added_menu = count($get_added_menus[$restaurant_id]) - 1;
                    $li_html .= '<ul>';
                    $li_html .= $extras_html;
                    $li_html .= $extra_notes != '' ? '<li>' . $extra_notes . '</li>' : '';
                    $li_html .= '</ul>';
                    $popup_id = 'edit_extras-' . $menu_cat_id . '-' . $menu_item_id;
                    $data_id = $menu_item_id;
                    $ajax_url = admin_url('admin-ajax.php');

                    // $unique_id = isset($get_added_menus[$restaurant_id][$array_latest_added_menu]['unique_id']) ? $get_added_menus[$restaurant_id][$array_latest_added_menu]['unique_id'] : '';
                    $extra_child_menu_id = isset($get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id']) ? $get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id'] : '';
                    //$li_html .= '<a href="javascript:void(0);" class="edit-menu-item update_menu_'.$rand_numb_class.'" onClick="foodbakery_edit_extra_menu_item(\'' . $popup_id . '\',\'' . $data_id . '\',\'' . $menu_cat_id . '\',\'' . $rand_numb_class . '\',\'' . $ajax_url . '\',\'' . $restaurant_id . '\',\'' . $unique_id . '\',\'' . $unique_menu_id . '\',\'' . $extra_child_menu_id . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>';
                }
                $li_html .= '</li>';

                setcookie('add_menu_items_temp', serialize($get_added_menus), time() + (10 * 365 * 24 * 60 * 60), '/');
                if ($menu_updating == 'true') {
                    //$json = array( 'msg' => esc_html__('Menu item have been updated in your basket.', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html );
                    $json = array('li_html' => $li_html);
                } else {
                    //$json = array( 'msg' => esc_html__('Menu item have been added in your basket', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html );
                    $json = array('li_html' => $li_html);
                }
            }
            //$this->sa_quantity_process($extras_arra);
            echo json_encode($json);

            wp_die();
        }

        public function foodbakery_restaurant_order_confirm() {
            global $foodbakery_plugin_options, $current_user;

            if (!is_user_logged_in()) {
                $json['type'] = "error";
                $json['is_user_login'] = false;
                $json['msg'] = esc_html__("Please Login as Buyer to Place an Order.", "foodbakery");
                echo json_encode($json);
                wp_die();
            }

            $restaurant_id = foodbakery_get_input('_rid', 0);

            do_action('foodbakery_delivery_address_validation', isset($_REQUEST['user_delivery_address']) ? $_REQUEST['user_delivery_address'] : '', $restaurant_id);

            $woocommerce_enabled = isset($foodbakery_plugin_options['foodbakery_use_woocommerce_gateway']) ? $foodbakery_plugin_options['foodbakery_use_woocommerce_gateway'] : '';
            $json = array();

            $order_vat_percent = foodbakery_get_input('order_vat_percent', '');
            $order_vat_cal_price = foodbakery_get_input('order_vat_cal_price', '');
            $order_subtotal_price = foodbakery_get_input('order_subtotal_price', 0);
            $delivery_date = isset($_POST['delivery_date']) ? $_POST['delivery_date'] : '';

            $foodbakery_cash_payments = get_post_meta($restaurant_id, 'foodbakery_restaurant_disable_cash', true);

            $minimum_delivery_order_value = get_post_meta($restaurant_id, 'foodbakery_minimum_order_value', true);
            $minimum_delivery_order_value = ( $minimum_delivery_order_value != '' ) ? $minimum_delivery_order_value : 0;
            $minimum_pickup_order_value = get_post_meta($restaurant_id, 'foodbakery_minimum_pickup_order_value', true);
            $minimum_pickup_order_value = ( $minimum_pickup_order_value != '' ) ? $minimum_pickup_order_value : 0;

            $maximum_delivery_order_value = get_post_meta($restaurant_id, 'foodbakery_maximum_order_value', true);
            $maximum_delivery_order_value = ( $maximum_delivery_order_value != '' ) ? $maximum_delivery_order_value : 0;
            $maximum_pickup_order_value = get_post_meta($restaurant_id, 'foodbakery_maximum_pickup_order_value', true);
            $maximum_pickup_order_value = ( $maximum_pickup_order_value != '' ) ? $maximum_pickup_order_value : 0;

            $user_id = $current_user->ID;
            $publisher_id = foodbakery_company_id_form_user_id($user_id);

            $get_added_menus = get_transient('add_menu_items_' . $publisher_id);

            if (isset($_COOKIE['add_menu_items_temp']) && is_user_logged_in()) {
                $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
            }

            $restaurant_pickup_delivery = get_post_meta($restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);

            $restaurant_pickup_delivery = ( isset($get_added_menus[$restaurant_id . '_fee_type']) && $get_added_menus[$restaurant_id . '_fee_type'] != '' ) ? $get_added_menus[$restaurant_id . '_fee_type'] : $restaurant_pickup_delivery;
            if (isset($restaurant_pickup_delivery) && $restaurant_pickup_delivery != '' && $restaurant_pickup_delivery != 'delivery_and_pickup') {
                $restaurant_pickup_delivery = $restaurant_pickup_delivery;
            } else {
                if (isset($_POST['order_fee_type']) && $_POST['order_fee_type'] != '') {
                    $restaurant_pickup_delivery = $_POST['order_fee_type'];
                } else {
                    $restaurant_pickup_delivery = 'delivery';
                }
            }

            $minimum_restrict_flag = false;
            $maximum_restrict_flag = false;

            if ($restaurant_pickup_delivery == 'delivery') {
                if ($minimum_delivery_order_value > 0 && $order_subtotal_price < $minimum_delivery_order_value) {
                    $minimum_restrict_flag = true;
                }
                if ($maximum_delivery_order_value > 0 && $order_subtotal_price > $maximum_delivery_order_value) {
                    $maximum_restrict_flag = true;
                }
            }
            if ($restaurant_pickup_delivery == 'pickup') {
                if ($minimum_pickup_order_value > 0 && $order_subtotal_price < $minimum_pickup_order_value) {
                    $minimum_restrict_flag = true;
                }
                if ($maximum_pickup_order_value > 0 && $order_subtotal_price > $maximum_pickup_order_value) {
                    $maximum_restrict_flag = true;
                }
            }

            if ($minimum_restrict_flag == true) {
                $json['type'] = "error";
                $json['msg'] = esc_html__("Your order does not meet the minimum order amount.", "foodbakery");
                echo json_encode($json);
                wp_die();
            }

            if ($maximum_restrict_flag == true) {
                $json['type'] = "error";
                $json['msg'] = esc_html__("Your order is exceeding the maximum order amount.", "foodbakery");
                echo json_encode($json);
                wp_die();
            }
            //
            // restaurant open close time check
            //
            $current_time = strtotime(current_time('h:i a'));
            $restaurant_open = false;
            $restaurant_open_close = get_post_meta($restaurant_id, 'foodbakery_opening_hour', true);

            $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
            if (isset($restaurants_type_post[0]->ID)) {
                $restaurants_type_open_hours = get_post_meta($restaurants_type_post[0]->ID, 'foodbakery_opening_hours_element', true);
                if ($restaurants_type_open_hours != 'on') {
                    $restaurant_open = true;
                }
            }
            $today_var = strtolower(current_time('l'));

            $restaurant_open_close_today = isset($restaurant_open_close[$today_var]) ? $restaurant_open_close[$today_var] : '';
            $restaurant_today_status = isset($restaurant_open_close_today['day_status']) ? $restaurant_open_close_today['day_status'] : '';
            $restaurant_open_time_today = isset($restaurant_open_close_today['opening_time']) ? $restaurant_open_close_today['opening_time'] : '';
            $restaurant_close_time_today = isset($restaurant_open_close_today['closing_time']) ? $restaurant_open_close_today['closing_time'] : '';
            if ($restaurant_today_status == 'on' && $restaurant_open_time_today != '' && $restaurant_close_time_today != '') {
                $restaurant_open_time_today = strtotime($restaurant_open_time_today);
                $restaurant_close_time_today = strtotime($restaurant_close_time_today);

                if ($restaurant_close_time_today > $restaurant_open_time_today && $current_time >= $restaurant_open_time_today && $current_time <= $restaurant_close_time_today) {
                    $restaurant_open = true;
                }
            }
            $restaurant_pre_order = get_post_meta($restaurant_id, 'foodbakery_restaurant_pre_order', true);
            if (false === $restaurant_open && $restaurant_pre_order != 'yes') {
                $json['type'] = "error";
                $json['msg'] = esc_html__("Restaurant is closed at this time.", "foodbakery");
                echo json_encode($json);
                wp_die();
            }
            //
            // restaurant open close time check end
            //

            /* set cookies for tip */
            do_action('foodbakery_set_tip_cookies', foodbakery_get_input('user_order_tip', ''));

            if ($foodbakery_cash_payments != 'yes') {
                $pay_method = isset($_POST['_pay_method']) ? $_POST['_pay_method'] : '';
            } else {
                $pay_method = 'card';
            }

            $restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
            $restaurant_publisher_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_publisher', true);
            $restaurant_user = foodbakery_user_id_form_company_id($restaurant_publisher_id);
            // restaurant publisher not buyer
            $user_id = $current_user->ID;
            $publisher_id = foodbakery_company_id_form_user_id($user_id);
            $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
            if ($publisher_id != '' && $publisher_type != '' && $publisher_type != 'restaurant' || $user_id == '') {

                $get_added_menus = get_transient('add_menu_items_' . $publisher_id);
                if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }
                if (!isset($get_added_menus[$restaurant_id])) {
                    $json['type'] = "error";
                    $json['msg'] = esc_html__("Your basket is empty.", "direcory");
                    echo json_encode($json);
                    wp_die();
                } else if (isset($get_added_menus[$restaurant_id]) && empty($get_added_menus[$restaurant_id])) {
                    $json['type'] = "error";
                    $json['msg'] = esc_html__("Your basket is empty.", "direcory");
                    echo json_encode($json);
                    wp_die();
                }


                $total_price = restaurant_menu_price_calc($get_added_menus, $restaurant_id, true, true, false);

                $check_delivery_tax = apply_filters('foodbakery_check_delivery_tax', false);
                if (!$check_delivery_tax) {
                    if ($woocommerce_enabled == 'on') {
                        $total_price = restaurant_menu_price_calc($get_added_menus, $restaurant_id, false, false, false);
                        restaurant_menu_price_calc($get_added_menus, $restaurant_id, true, true, false);
                    }
                }

                $restaurant_title = get_the_title($restaurant_id);

                $date = strtotime(date('d-m-y'));

                $order_inquiry_post = array(
                    'post_title' => wp_strip_all_tags($restaurant_title),
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_type' => 'orders_inquiries',
                    'post_date' => current_time('Y-m-d H:i:s')
                );

                //insert Order/inquiry
                $order_id = wp_insert_post($order_inquiry_post);

                update_post_meta($order_id, 'foodbakery_currency', foodbakery_base_currency_sign());
                update_post_meta($order_id, 'foodbakery_currency_obj', foodbakery_get_base_currency());

                $my_post = array(
                    'ID' => $order_id,
                    'post_title' => 'order-' . $order_id,
                    'post_name' => 'order-' . $order_id,
                );

                // Update the post into the database
                wp_update_post($my_post);

                if ($order_id) {

                    // menu list
                    if (isset($get_added_menus[$restaurant_id]) && is_array($get_added_menus[$restaurant_id])) {

                        $menu_make_arr = array();
                        foreach ($get_added_menus[$restaurant_id] as $key1 => $menu_item_do) {
                            $menu_single_ar = array();
                            $menu_item_id = isset($menu_item_do['menu_id']) ? $menu_item_do['menu_id'] : '';
                            $menu_item_extr = isset($menu_item_do['extras']) ? $menu_item_do['extras'] : '';

                            // menu category
                            $this_item_category = isset($restaurant_menu_list[$menu_item_id]['restaurant_menu']) ? $restaurant_menu_list[$menu_item_id]['restaurant_menu'] : '';

                            // menu title
                            $this_item_title = isset($restaurant_menu_list[$menu_item_id]['menu_item_title']) ? $restaurant_menu_list[$menu_item_id]['menu_item_title'] : '';

                            // menu price
                            $this_item_price = isset($restaurant_menu_list[$menu_item_id]['menu_item_price']) ? $restaurant_menu_list[$menu_item_id]['menu_item_price'] : '';

                            $this_item_notes = isset($menu_item_do['notes']) ? $menu_item_do['notes'] : 'no notes';

                            $extras_arra = array();

                            // menu extras
                            $this_item_extras = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra'] : '';

                            if (isset($this_item_extras['heading']) && is_array($this_item_extras['heading']) && sizeof($this_item_extras['heading']) > 0) {
                                $menu_ext_counter = 0;
                                foreach ($menu_item_extr as $key => $this_item_extra_at) {
                                    $this_item_heading = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$this_item_extra_at['title_id']]) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$this_item_extra_at['title_id']] : '';
                                    $menu_extra_at_label = isset($this_item_extra_at['title']) ? $this_item_extra_at['title'] : '';
                                    $menu_extra_at_price = isset($this_item_extra_at['price']) ? $this_item_extra_at['price'] : '';

                                    $old_qty = isset($this_item_extra_at['quantity']) ? $this_item_extra_at['quantity'] : '';
                                    if ($old_qty > 0 || $old_qty == '') {


                                        $extras_arra[] = array(
                                            'heading' => $this_item_heading,
                                            'title' => $menu_extra_at_label,
                                            'price' => $menu_extra_at_price,
                                            'quantity' => $this_item_extra_at['quantity'],
                                            'restaurant_id' => $restaurant_id,
                                            'menu_item_id' => $menu_item_id,
                                            'position_id' => $this_item_extra_at['position_id'],
                                            'extra_id' => $this_item_extra_at['extra_id']
                                        );
                                    } else {


                                        $json['type'] = "error";
                                        $json['msg'] = esc_html__("All products stock not avaiable . Try again !", "foodbakery");
                                        echo json_encode($json);
                                        wp_die();
                                    }




                                    $menu_ext_counter++;
                                }
                            }

//                            if (isset($this_item_extras['heading']) && is_array($this_item_extras['heading']) && sizeof($this_item_extras['heading']) > 0) {
//
//                                $menu_ext_counter = 0;
//
//                                foreach ($this_item_extras['heading'] as $menu_extra_att_heading) {
//                                    $this_item_heading = $menu_extra_att_heading;
//                                    $menu_extra_at_label = isset($menu_item_extr[$menu_ext_counter]['title']) ? $menu_item_extr[$menu_ext_counter]['title'] : '';
//                                    $menu_extra_at_price = isset($menu_item_extr[$menu_ext_counter]['price']) ? $menu_item_extr[$menu_ext_counter]['price'] : '';
//
//                                    $extras_arra[] = array(
//                                        'heading' => $this_item_heading,
//                                        'title' => $menu_extra_at_label,
//                                        'price' => $menu_extra_at_price,
//                                    );
//
//                                    $menu_ext_counter ++;
//                                }
//                            }

                            $menu_single_ar['category'] = $this_item_category;
                            $menu_single_ar['title'] = $this_item_title;
                            $menu_single_ar['price'] = $this_item_price;
                            $menu_single_ar['extras'] = $extras_arra;
                            $menu_single_ar['notes'] = $this_item_notes;

                            $menu_make_arr[] = $menu_single_ar;
                        }
                        update_post_meta($order_id, 'menu_items_list', $menu_make_arr);
                    }
                    //

                    update_post_meta($order_id, 'order_vat_percent', $order_vat_percent);
                    update_post_meta($order_id, 'order_vat_cal_price', $order_vat_cal_price);
                    update_post_meta($order_id, 'order_subtotal_price', $order_subtotal_price);
                    update_post_meta($order_id, 'services_total_price', $total_price);
                    update_post_meta($order_id, 'foodbakery_restaurant_id', $restaurant_id);
                    update_post_meta($order_id, 'foodbakery_publisher_id', $restaurant_publisher_id);
                    update_post_meta($order_id, 'foodbakery_order_user', $publisher_id);
                    update_post_meta($order_id, 'foodbakery_restaurant_user', $restaurant_user);

                    update_post_meta($order_id, 'foodbakery_order_date', strtotime(current_time('Y-m-d H:i:s')));
                    update_post_meta($order_id, 'foodbakery_delivery_date', strtotime($delivery_date));
                    update_post_meta($order_id, 'foodbakery_order_form_fields', '');
                    $status_order = "process"; //edit sagar processing
                    if ($check_delivery_tax) {
                        $status_order = "Completed";
                    }

                    //  echo $order_id;

                    update_post_meta($order_id, 'foodbakery_order_status', $status_order);
                    update_post_meta($order_id, 'foodbakery_order_payment_status', 'pending');
                    update_post_meta($order_id, 'foodbakery_order_type', 'order');
                    update_post_meta($order_id, 'foodbakery_order_id', $order_id);
                    update_post_meta($order_id, 'read_status', '0');
                    update_post_meta($order_id, 'buyer_read_status', '0');
                    update_post_meta($order_id, 'seller_read_status', '0');

                    do_action('foodbakery_add_delivery_taxes_details', $order_id, $order_subtotal_price);

                    do_action('foodbakery_imran_order_checked', $order_id);
                    if ($pay_method != 'card') {
                        update_post_meta($order_id, 'foodbakery_order_paytype', 'cash');
                    } else {
                        update_post_meta($order_id, 'foodbakery_order_paytype', 'card');
                    }

                    // saving fee and fee type in meta

                    $check_delivery_tax = apply_filters('foodbakery_check_delivery_tax', false);
                    if (!$check_delivery_tax) {
                        $menu_save_fee = '';
                        $menu_save_fee_type = '';
                        $foodbakery_delivery_fee = get_post_meta($restaurant_id, 'foodbakery_delivery_fee', true);
                        $foodbakery_pickup_fee = get_post_meta($restaurant_id, 'foodbakery_pickup_fee', true);
                        // $restaurant_pickup_delivery = get_post_meta($restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);

                        $selected_fee_type = isset($get_added_menus[$restaurant_id . '_fee_type']) ? $get_added_menus[$restaurant_id . '_fee_type'] : '';
                        if ($selected_fee_type == 'delivery' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                            $menu_save_fee = foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false);
                            $menu_save_fee_type = 'delivery';
                        } else if ($selected_fee_type == 'pickup' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee >= 0) {
                            $menu_save_fee = foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false);
                            $menu_save_fee_type = 'pickup';
                        } else {
                            if ($foodbakery_delivery_fee >= 0 && $restaurant_pickup_delivery != 'pickup') {
                                $menu_save_fee = foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false);
                                $menu_save_fee_type = 'delivery';
                            } else if ($foodbakery_pickup_fee >= 0 && $restaurant_pickup_delivery != 'delivery') {
                                $menu_save_fee = foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false);
                                $menu_save_fee_type = 'pickup';
                            }
                        }

                        update_post_meta($order_id, 'menu_order_fee', $menu_save_fee);
                        update_post_meta($order_id, 'menu_order_fee_type', $menu_save_fee_type);
                    }
                    //
                    // check commission amount and update
                    $restaurant_membership = get_post_meta($restaurant_id, 'foodbakery_restaurant_package', true);
                    $restaurant_comision = get_post_meta($restaurant_membership, 'foodbakery_package_commision_based', true);
                    $restaurant_comision_percentage = get_post_meta($restaurant_membership, 'foodbakery_package_commision', true);

                    if ($restaurant_comision == 'on' && $restaurant_comision_percentage > 0 && $total_price > 0) {
                        $commision_charged = ($total_price * $restaurant_comision_percentage) / 100;
                        $payable_amount = $total_price - $commision_charged;
                        update_post_meta($order_id, 'order_amount_charged', foodbakery_get_currency($commision_charged, false, '', '', false));
                        update_post_meta($order_id, 'order_amount_credited', foodbakery_get_currency($payable_amount, false, '', '', false));
                    }


                    $user_name = get_the_title($publisher_id);
                    do_action('foodbakery_send_invoices', $order_id);
                    $json['pay_method'] = "";
                    if ($pay_method != 'card') {
                        if (isset($get_added_menus[$restaurant_id])) {
                            unset($get_added_menus[$restaurant_id]);
                        }
                        if (isset($get_added_menus[$restaurant_id . '_fee_type'])) {
                            unset($get_added_menus[$restaurant_id . '_fee_type']);
                        }
                        set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);
                        $json['pay_method'] = "cash";
                    }

                    /*
                     * Adding Notification
                     */
                    $notification_array = array(
                        'type' => 'reservation',
                        'element_id' => $restaurant_id,
                        'message' => __($user_name . ' submitted a order form on your restaurant <a href="' . get_the_permalink($restaurant_id) . '">' . wp_trim_words(get_the_title($restaurant_id), 5) . '</a> .', 'foodbakery'),
                    );
                    do_action('foodbakery_add_notification', $notification_array);

                    if ($total_price > 0 && $pay_method == 'card') {

                        $foodbakery_payment_page = isset($foodbakery_plugin_options['foodbakery_package_page']) ? $foodbakery_plugin_options['foodbakery_package_page'] : '';
                        $foodbakery_payment_page_link = $foodbakery_payment_page != '' ? get_permalink($foodbakery_payment_page) : '';

                        // Redirecting to Payment process on next page
                        if ($foodbakery_payment_page_link != '') {
                            $redirect_form_id = rand(1000000, 9999999);
                            $redirect_html = '<form id="form-' . $redirect_form_id . '" method="get" action="' . $foodbakery_payment_page_link . '">
								<input type="hidden" name="action" value="reservation-order">
								<input type="hidden" name="trans_id" value="' . $order_id . '">
								<input type="hidden" name="menu_id" value="' . $restaurant_id . '">';
                            if (isset($_GET['lang'])) {
                                $redirect_html .= '<input type="hidden" name="lang" value="' . $_GET['lang'] . '">';
                            }
                            $redirect_html .= '</form>
								<script>document.getElementById("form-' . $redirect_form_id . '").submit();</script>';
                            $json['message'] = $redirect_html;
                            $json['type'] = "redirect";
                            //do_action('foodbakery_sent_order_email', $order_id);
                            //do_action('foodbakery_received_order_email', $order_id);
                        }
                    } else {

                        $foodbakery_payment_page = isset($foodbakery_plugin_options['foodbakery_package_page']) ? $foodbakery_plugin_options['foodbakery_package_page'] : '';
                        $foodbakery_payment_page_link = $foodbakery_payment_page != '' ? get_permalink($foodbakery_payment_page) : '';

                        // Redirecting to Payment process on next page
                        if ($foodbakery_payment_page_link != '') {
                            $redirect_form_id = rand(1000000, 9999999);
                            $redirect_html = '<form id="form-' . $redirect_form_id . '" method="get" action="' . $foodbakery_payment_page_link . '">
							<input type="hidden" name="action" value="reservation-order">
							<input type="hidden" name="trans_id" value="' . $order_id . '">
							<input type="hidden" name="menu_id" value="' . $restaurant_id . '">
							<input type="hidden" name="payment_mode" value="cash">';
                            if (isset($_GET['lang'])) {
                                $redirect_html .= '<input type="hidden" name="lang" value="' . $_GET['lang'] . '">';
                            }
                            $redirect_html .= '</form>
							<script>document.getElementById("form-' . $redirect_form_id . '").submit();</script>';
                            $json['message'] = $redirect_html;
                            $json['type'] = "redirect";
                        }

                        //do_action('foodbakery_sent_order_email', $order_id);
                        //do_action('foodbakery_received_order_email', $order_id);
                        //$json['type'] = "success";
                        //$json['msg'] = esc_html__("Your order has been sent successfully.", "direcory");
                    }
                } else {
                    $json['type'] = "error";
                    $json['msg'] = esc_html__("Something went wrong, order could not be processed.", "direcory");
                }

                echo json_encode($json);
                wp_die();
            } else {
                $get_added_menus = '';
                if (isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }

                if (!empty($get_added_menus)) {

                    $total_price = restaurant_menu_price_calc($get_added_menus, $restaurant_id, true, true, false);
                    if ($total_price > 0) {

                        $foodbakery_payment_page = isset($foodbakery_plugin_options['foodbakery_package_page']) ? $foodbakery_plugin_options['foodbakery_package_page'] : '';
                        $foodbakery_payment_page_link = $foodbakery_payment_page != '' ? get_permalink($foodbakery_payment_page) : '';
                        $order_id = isset($order_id) ? $order_id : '';
                        // Redirecting to Payment process on next page
                        if ($foodbakery_payment_page_link != '') {
                            $redirect_form_id = rand(1000000, 9999999);
                            $redirect_html = '<form id="form-' . $redirect_form_id . '" method="get" action="' . $foodbakery_payment_page_link . '">
							<input type="hidden" name="action" value="reservation-order">
							<input type="hidden" name="trans_id" value="' . $order_id . '">
							<input type="hidden" name="menu_id" value="' . $restaurant_id . '">';
                            if (isset($_GET['lang'])) {
                                $redirect_html .= '<input type="hidden" name="lang" value="' . $_GET['lang'] . '">';
                            }
                            $redirect_html .= '</form>
							<script>document.getElementById("form-' . $redirect_form_id . '").submit();</script>';
                            $json['message'] = $redirect_html;
                            $json['type'] = "redirect";
                            echo json_encode($json);
                            wp_die();
                        }
                    }
                } else {
                    $json['msg'] = esc_html__("Your cart is empty.", "direcory");
                    $json['type'] = "error";
                    echo json_encode($json);
                    wp_die();
                }
            }

            wp_die();
        }

        public function foodbakery_reservation_submit_callback() {
            global $foodbakery_plugin_options;

            $json = array();
            $cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';
            $cs_success_html = '<div class="alert alert-success"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button><p><i class="icon-checkmark6"></i>';
            $cs_msg_html = '</p></div>';

            $foodbakery_publisher = foodbakery_get_input('foodbakery_publisher', 0);
            $foodbakery_order_user = foodbakery_get_input('foodbakery_order_user', 0);
            $publisher_company = foodbakery_get_input('foodbakery_publisher_company', 0);
            $order_user_company = foodbakery_get_input('foodbakery_order_user_company', 0);

            $order_price = foodbakery_get_input('foodbakery_order_price', 0);
            $restaurant_id = foodbakery_get_input('foodbakery_restaurant_id', 0);
            $restaurant_type_id = foodbakery_get_input('foodbakery_restaurant_type_id', 0);
            $reservation_fields = get_post_meta($restaurant_type_id, "foodbakery_restaurant_type_reservation_fields", true);
            $paid_order = get_post_meta($restaurant_type_id, "foodbakery_inquiry_paid_form", true);

            $order_type = isset($paid_order) && $paid_order == 'on' ? 'order' : 'inquiry';
            if ($order_type == 'order') {
                $order_type_string = esc_html__('order', 'foodbakery');
            } else {
                $order_type_string = esc_html__('inquiry', 'foodbakery');
            }

            // services full data
            $services_total_price = foodbakery_get_input('services_total_price', 0);

            // Fields validation
            $quantity = 0;
            $time_meta_key = '';
            if (!empty($reservation_fields)) {
                foreach ($reservation_fields as $reservation_field) {

                    $field_type = isset($reservation_field['type']) ? $reservation_field['type'] : '';
                    $required = isset($reservation_field['required']) ? $reservation_field['required'] : '';
                    $meta_key = isset($reservation_field['meta_key']) ? $reservation_field['meta_key'] : '';

                    if ($field_type == 'quantity') {
                        $quantity = $_POST[$meta_key];
                    }

                    if ($field_type == 'time') {
                        $time_meta_key = $meta_key;
                    }

                    if ($required == 'yes' && $_POST[$meta_key] == '') {
                        $json['type'] = "error";
                        $json['message'] = $cs_danger_html . esc_html__(" Please fill all required fields.", "direcory") . $cs_msg_html;
                        echo json_encode($json);
                        exit();
                    } else if ($required == 'yes' && $field_type == 'email' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $_POST[$meta_key])) {
                        $json['type'] = "error";
                        $json['message'] = $cs_danger_html . esc_html__(" Please enter a valid email address.", "direcory") . $cs_msg_html;
                        echo json_encode($json);
                        exit();
                    }
                }
            }

            if ($publisher_company == $order_user_company) {
                $json['type'] = "error";
                $json['message'] = $cs_danger_html . esc_html__(" Sorry! You can't send " . $order_type_string . " on your own restaurant.", "direcory") . $cs_msg_html;
                echo json_encode($json);
                exit();
            }

            $restaurant_title = get_the_title($restaurant_id);
            $publisher_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_username', true);
            $date = strtotime(date('d-m-y'));

            $order_inquiry_post = array(
                'post_title' => wp_strip_all_tags($restaurant_title),
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'orders_inquiries',
                'post_date' => current_time('Y-m-d H:i:s')
            );

            //insert Order/inquiry
            $order_id = wp_insert_post($order_inquiry_post);

            $my_post = array(
                'ID' => $order_id,
                'post_title' => 'order-' . $order_id,
                'post_name' => 'order-' . $order_id,
            );

            // Update the post into the database
            wp_update_post($my_post);

            // insert Order/inquiry meta keys
            foreach ($_POST as $key => $value) {
                if ($key == 'foodbakery_order_date') {
                    $value = strtotime($value);
                }
                if ($key == $time_meta_key) {
                    $time_format = foodbakery_get_input('time_format', '');
                    $value = strtotime($value . $time_format);
                }
                update_post_meta($order_id, $key, $value);
            }
            update_post_meta($order_id, 'foodbakery_order_form_fields', $reservation_fields);
            update_post_meta($order_id, 'foodbakery_order_status', 'processing');
            update_post_meta($order_id, 'foodbakery_order_type', $order_type);
            update_post_meta($order_id, 'foodbakery_order_id', $order_id);
            update_post_meta($order_id, 'read_status', '0');
            update_post_meta($order_id, 'buyer_read_status', '0');
            update_post_meta($order_id, 'seller_read_status', '0');

            // update file with attachment id

            foreach ($_FILES as $key => $_FILE) {
                $order_file = $this->foodbakery_upload_order_file($_FILE, $order_id);
                update_post_meta($order_id, $key, $order_file);
            }

            if ($order_id) {
                $user_name = get_the_title($order_user_company);
                /*
                 * Adding Notification
                 */
                $notification_array = array(
                    'type' => 'reservation',
                    'element_id' => $restaurant_id,
                    'message' => __($user_name . ' submitted a reservation form on your restaurant <a href="' . get_the_permalink($restaurant_id) . '">' . wp_trim_words(get_the_title($restaurant_id), 5) . '</a> .', 'foodbakery'),
                );
                do_action('foodbakery_add_notification', $notification_array);
                if ($services_total_price > 0 && $paid_order == 'on') {
                    // Redirecting parameters
                    $foodbakery_payment_params = array(
                        'action' => 'reservation-order',
                        'trans_id' => $order_id,
                    );
                    $foodbakery_payment_page = isset($foodbakery_plugin_options['foodbakery_package_page']) ? $foodbakery_plugin_options['foodbakery_package_page'] : '';
                    $foodbakery_payment_page_link = $foodbakery_payment_page != '' ? get_permalink($foodbakery_payment_page) : '';

                    // Redirecting to Payment process on next page
                    if ($foodbakery_payment_page_link != '') {
                        $redirect_form_id = rand(1000000, 9999999);
                        $redirect_html = '<form id="form-' . $redirect_form_id . '" method="get" action="' . $foodbakery_payment_page_link . '">
						<input type="hidden" name="action" value="reservation-order">
						<input type="hidden" name="trans_id" value="' . $order_id . '">
						<input type="hidden" name="menu_id" value="' . $restaurant_id . '">';
                        if (isset($_GET['lang'])) {
                            $redirect_html .= '<input type="hidden" name="lang" value="' . $_GET['lang'] . '">';
                        }
                        $redirect_html .= '</form>
						<script>document.getElementById("form-' . $redirect_form_id . '").submit();</script>';
                        $json['message'] = $redirect_html;
                        $json['type'] = "ridirect";
                    }
                } else {
                    $json['type'] = "success";
                    $json['message'] = $cs_success_html . esc_html__("Your " . $order_type_string . " has been sent successfully.", "direcory") . $cs_msg_html;
                    if ($order_type == 'order') {
                        do_action('foodbakery_sent_order_email', $order_id);
                        do_action('foodbakery_received_order_email', $order_id);
                    } else {
                        do_action('foodbakery_sent_inquiry_email', $order_id);
                        do_action('foodbakery_received_inquiry_email', $order_id);
                    }
                }
            } else {
                $json['type'] = "error";
                $json['message'] = $cs_danger_html . esc_html__("Something went wrong, " . $order_type_string . " could not be processed.", "direcory") . $cs_msg_html;
            }
            echo json_encode($json);
            wp_die();
        }

        public function foodbakery_upload_order_file($file, $order_id = '') {
            $attach_id = '';
            if (isset($file)) {
                $json = array();
                require_once ABSPATH . 'wp-admin/includes/file.php';
                $current_user_id = get_current_user_id();
                $status = wp_handle_upload($file, array('test_form' => false));
                if (isset($status) && !isset($status['error'])) {
                    $uploads = wp_upload_dir();
                    $filename = isset($status['url']) ? $status['url'] : '';
                    $filetype = wp_check_filetype(basename($filename), null);

                    if ($filename != '') {
                        // Prepare an array of post data for the attachment.

                        $attachment = array(
                            'guid' => $status['url'],
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
                        // Insert the attachment.
                        $attach_id = wp_insert_attachment($attachment, $status['file']);
                        if ($order_id != '') {
                            wp_update_post(
                                    array(
                                        'ID' => $attach_id,
                                        'post_parent' => $order_id
                                    )
                            );
                        }
                        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                        $attach_data = wp_generate_attachment_metadata($attach_id, $status['file']);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $attach_id = $attach_id;
                    }
                }
            }
            return $attach_id;
        }

    }

    global $foodbakery_reservation_element;
    $foodbakery_reservation_element = new foodbakery_reservation_element();
}