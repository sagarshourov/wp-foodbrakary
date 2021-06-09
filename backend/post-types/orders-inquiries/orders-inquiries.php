<?php

/**
 * File Type: Reservations Post Type
 */
if (!class_exists('post_type_orders_inquiries')) {

    class post_type_orders_inquiries {

        /**
         * Start Contructer Function
         */
        public function __construct() {
            add_action('init', array(&$this, 'foodbakery_orders_inquiries_register'), 12);
            add_filter('manage_orders_inquiries_posts_columns', array(&$this, 'orders_inquiries_columns_add'), 10, 1);
            add_action('manage_orders_inquiries_posts_custom_column', array(&$this, 'orders_inquiries_columns'), 10, 2);
            add_filter('post_row_actions', array(&$this, 'orders_inquiries_remove_row_actions'), 11, 2);
            add_action('restrict_manage_posts', array(&$this, 'modify_orders_inquiries_filters'), 11);
            add_filter('parse_query', array(&$this, 'orders_inquiries_order_type_filter'), 11, 1);
        }

        public function orders_inquiries_remove_row_actions($actions, $post) {
            if ($post->post_type == 'orders_inquiries') {
                unset($actions['view']);
            }
            return $actions;
        }

        public function orders_inquiries_columns_add($columns) {
            unset($columns['title']);
            unset($columns['date']);
            unset($columns['comments']);
            unset($columns['author']);
            unset($columns['validated_is_valid']);
            unset($columns['validated_check']);
            $columns['p_title'] = esc_html__('Order Id', 'foodbakery');
            $columns['p_date'] = esc_html__('Date', 'foodbakery');
            $columns['restaurant'] = esc_html__('Restaurant', 'foodbakery');
            $columns['restaurant_user'] = esc_html__('Restaurant Owner', 'foodbakery');
            $columns['booking_order_user'] = esc_html__('Order/Inquiry User', 'foodbakery');
            $columns['price'] = esc_html__('Total Price', 'foodbakery');
            $columns['type'] = esc_html__('Type', 'foodbakery');
            $columns['status'] = esc_html__('Status', 'foodbakery');

            return $columns;
        }

        public function orders_inquiries_columns($name) {
            global $post, $orders_inquiries, $foodbakery_plugin_options;
            $foodbakery_order_type = get_post_meta($post->ID, 'foodbakery_order_type', true);
            if ($foodbakery_order_type != 'order') {
                $foodbakery_restaurant_publisher = get_post_meta($post->ID, 'foodbakery_restaurant_publisher', true);
                $foodbakery_booking_publisher = get_post_meta($post->ID, 'foodbakery_booking_publisher', true);
            } else {
                $foodbakery_restaurant_publisher = get_post_meta($post->ID, 'foodbakery_publisher_id', true);
                $foodbakery_booking_publisher = get_post_meta($post->ID, 'foodbakery_order_user', true);
            }

            $foodbakery_restaurant_id = get_post_meta($post->ID, 'foodbakery_restaurant_id', true);

            switch ($name) {
                case 'p_title':
                    echo '#' . $post->ID;
                    break;
                case 'p_date':
                    echo get_the_date();
                    break;
                case 'restaurant_user':
                    echo get_the_title($foodbakery_restaurant_publisher);
                    break;
                case 'restaurant':
                    echo get_the_title($foodbakery_restaurant_id);
                    break;
                case 'service':
                    $services = get_post_meta($post->ID, 'service_title', true);
                    $service_quantity = get_post_meta($post->ID, 'service_quantity', true);
                    $service_price = get_post_meta($post->ID, 'service_price', true);
                    $currency_sign = foodbakery_get_currency_sign();
                    if (is_array($services) && !empty($services)) {
                        foreach ($services as $key => $service) {
                            $quantity = $service_quantity[$key];
                            $price = $service_price[$key];
                            if ($quantity > 1) {
                                $items = sprintf(' for %s items', $quantity);
                            } else {
                                $items = sprintf(' for %s item', $quantity);
                            }
                            $total_price = $price * $quantity;
                            $total_price = currency_symbol_possitions($total_price, $currency_sign);
                            echo esc_html($service) . ' (' . esc_html($total_price) . esc_html($items) . ')' . "<br>";
                        }
                    } else {
                        echo '-';
                    }
                    break;
                case 'booking_order_user':
                    echo get_the_title($foodbakery_booking_publisher);
                    break;
                case 'price':
                    $foodbakery_order_type = get_post_meta($post->ID, 'foodbakery_order_type', true);
                   // $services_total_price = get_post_meta(get_the_id(), "foodbakery_transaction_amount", true);
                    $services_total_price = get_post_meta($post->ID, 'services_total_price', true);
                    $menu_order_fee = get_post_meta(get_the_ID(), 'menu_order_fee', true);
                    $currency_sign = get_post_meta($post->ID, 'foodbakery_currency', true);
                    if ($currency_sign == '') {
                        $currency_sign = foodbakery_get_currency_sign();
                    }
                    if ($foodbakery_order_type == 'order') {
                        echo currency_symbol_possitions(number_format(floatval($services_total_price), 2), $currency_sign);
                        //echo esc_html($currency_sign) . restaurant_menu_price_calc('defined', $services_total_price, $menu_order_fee, true, false, false);
                    } else {
                        echo '-';
                    }
                    break;
                case 'type':
                    echo get_post_meta($post->ID, 'foodbakery_order_type', true);
                    break;
                case 'status':
                    echo get_post_meta($post->ID, 'foodbakery_order_status', true);
                    break;
            }
        }

        /**
         * Start Wp's Initilize action hook Function
         */
        public function foodbakery_orders_inquiries_init() {
            // Initialize Post Type
            $this->foodbakery_orders_inquiries_register();
        }

        /**
         * Start Function How to Register post type
         */
        public function foodbakery_orders_inquiries_register() {
            $labels = array(
                'name' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_name'),
                'singular_name' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_singular_name'),
                'menu_name' => esc_html__('Orders / Inquiries', 'foodbakery'),
                'name_admin_bar' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_name_admin_bar'),
                'add_new' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_add_new'),
                'add_new_item' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_add_new_item'),
                'new_item' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_new_item'),
                'edit_item' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_edit_item'),
                'view_item' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_view_item'),
                'all_items' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_all_items'),
                'search_items' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_search_items'),
                'not_found' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_not_found'),
                'not_found_in_trash' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries_not_found_in_trash'),
            );

            $args = array(
                'labels' => $labels,
                'description' => foodbakery_plugin_text_srt('foodbakery_orders_inquiries'),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'menu_position' => 29,
                'menu_icon' => wp_foodbakery::plugin_url() . 'assets/backend/images/payment.png',
                'query_var' => false,
                'rewrite' => array('slug' => 'order_inquires'),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'exclude_from_search' => true,
                'supports' => array('title', 'author', 'comments'),
            );

            register_post_type('orders_inquiries', $args);
        }

        public function modify_orders_inquiries_filters() {
            // Only apply the filter to our specific post type
            global $typenow;
            if ($typenow == 'orders_inquiries') {
                $order_types = array('inquiry' => esc_html__('Inquiry', 'foodbakery'), 'order' => esc_html__('Order', 'foodbakery'));
                echo '<select name="order_type">';
                echo '<option value="">' . esc_html__('Select Order Type', 'foodbakery') . '</option>';
                foreach ($order_types as $key => $order_type) {
                    $selected = (isset($_GET['order_type']) && $key == $_GET['order_type']) ? ' selected ' : '';
                    echo '<option ' . $selected . ' value="' . $key . '">' . $order_type . '</option>';
                }

                echo "</select>";
            }
        }

        public function orders_inquiries_order_type_filter($query) {
            global $pagenow;
            if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'orders_inquiries' && isset($_GET['order_type']) && $_GET['order_type'] != '') {
                $query->query_vars['meta_key'] = 'foodbakery_order_type';
                $query->query_vars['meta_value'] = $_GET['order_type'];
                $query->query_vars['compare'] = '=';
            }
        }

        // End of class	
    }

    // Initialize Object
    $orders_inquiries_object = new post_type_orders_inquiries();
}




// add analytic for order inquiries

add_filter('views_edit-orders_inquiries', function( $views ) {
    $args = array(
        'post_type' => 'orders_inquiries',
        'posts_per_page' => "-1",
    );
    $custom_query = new WP_Query($args);
    $total_orders = 0;
    $complete_orders = 0;
    $closed_orders = 0;
    $processing_orders = 0;

    while ($custom_query->have_posts()) : $custom_query->the_post();
        global $post;
        $order_status = get_post_meta($post->ID, 'foodbakery_order_status', true);
        if (isset($order_status) && !empty($order_status)) {
            if ($order_status == 'Completed') {
                $complete_orders ++;
            } else if ($order_status == 'Processing') {
                $processing_orders ++;
            } else if ($order_status == 'Closed') {
                $closed_orders ++;
            }
        }
        $total_orders ++;
    endwhile;
    echo '
    <ul class="total-foodbakery-restaurant row">
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Total Orders/Inquiries </strong><em>' . $total_orders . '</em><i class="icon-shopping_cart"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Completed Orders/Inquiries </strong><em>' . $complete_orders . '</em><i class="icon-check_circle"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Processing Orders/Inquiries </strong><em>' . $processing_orders . '</em><i class="icon-refresh3"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Closed Orders/Inquiries </strong><em>' . $closed_orders . '</em><i class="icon-remove_shopping_cart"></i></div></li>    
    </ul>
    ';
    return $views;
});

// End  analytic for order inquiries