<?php
/**
 * File Type: Searchs Shortcode Frontend
 */
if (!class_exists('Foodbakery_Shortcode_Single_Restaurant_front')) {

    class Foodbakery_Shortcode_Single_Restaurant_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'single_restaurant';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_single_restaurant_shortcode_callback'));
        }

        /*
         * Shortcode View on Frontend
         */

        public function foodbakery_single_restaurant_shortcode_callback($atts, $content = "") {
            global $column_container, $foodbakery_form_fields_frontend, $foodbakery_plugin_options, $current_user;

            $restaurant_data = '';
            $this_item_heading='';
            $page_element_size = isset($atts['restaurant_categories_element_size']) ? $atts['restaurant_categories_element_size'] : 100;
            $single_restaurant_title = isset($atts['single_restaurant_title']) ? $atts['single_restaurant_title'] : '';
            $single_restaurant_subtitle = isset($atts['single_restaurant_subtitle']) ? $atts['single_restaurant_subtitle'] : '';
            $selected_restaurant = isset($atts['selected_restaurant']) ? $atts['selected_restaurant'] : '';
            if ($post = get_page_by_path($selected_restaurant, OBJECT, 'restaurants')) {
                $foodbakery_restaurant_id = $post->ID;
            } else {
                $foodbakery_restaurant_id = 0;
            }
            do_action('call_class_obj_sticky_cart', $foodbakery_restaurant_id);
            $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
            $foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';
            $restaurant_table_booking = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_table_booking', true);
            $restaurant_pickup_delivery = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);
            $foodbakery_delivery_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_delivery_fee', true);
            $foodbakery_pickup_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_pickup_fee', true);
            $restaurant_menu_list = get_post_meta($foodbakery_restaurant_id, 'foodbakery_menu_items', true);
            $total_items = is_array($restaurant_menu_list) ? count($restaurant_menu_list) : array();
            $total_menu = array();
            if (isset($restaurant_menu_list) && $restaurant_menu_list != '') {
                for ($menu_count = 0; $menu_count < $total_items; $menu_count ++) {
                    if (isset($restaurant_menu_list[$menu_count]['restaurant_menu'])) {
                        $menu_exists = in_array($restaurant_menu_list[$menu_count]['restaurant_menu'], $total_menu);
                        if (!$menu_exists) {
                            $total_menu[] = $restaurant_menu_list[$menu_count]['restaurant_menu'];
                        }
                    }
                }
            }
            $extras_modal_boxes = '';
            $total_menu_count = count($total_menu);
            wp_enqueue_script('foodbakery-restaurant-single');
            wp_enqueue_script('prettyPhoto');
            wp_enqueue_style('foodbakery-pretty-photo-css');
            wp_deregister_style('bootstrap-datepicker');
            wp_deregister_script('bootstrap-datepicker');

            $foodbakery_restaurant_type = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_type', true);
            $foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
            if ($restaurant_type_post = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type')) {
                $restaurant_type_id = $restaurant_type_post->ID;
            }
            $restaurant_type_id = isset($restaurant_type_id) ? $restaurant_type_id : '';
            $foodbakery_user_reviews = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);
            $transaction_restaurant_reviews = get_post_meta($foodbakery_restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);


            $foodbakery_max_distance = get_post_meta($foodbakery_restaurant_id, 'foodbakery_maximum_delivary_area', true);

            if (empty($foodbakery_max_distance)) { 
            $foodbakery_max_distance = 0;

            }

            $foodbakery_post_loc_latitude_restaurant = get_post_meta($foodbakery_restaurant_id, 'foodbakery_post_loc_latitude_restaurant', true);
            $foodbakery_post_loc_longitude_restaurant = get_post_meta($foodbakery_restaurant_id, 'foodbakery_post_loc_longitude_restaurant', true);


        $foodbakery_post_loc_latitude_user = array();
        $foodbakery_post_loc_longitude_user = array();

        if(is_user_logged_in()){
            $current_user = wp_get_current_user();

        $user_id = $current_user->ID;

        $publisher_id = foodbakery_company_id_form_user_id($user_id);

        $company_id = get_user_meta( $user_id, 'foodbakery_company', true );




        if ($company_id != '' ) {
            $foodbakery_post_loc_latitude_user = get_post_meta($publisher_id,'foodbakery_post_loc_latitude_publisher' ); 
            $foodbakery_post_loc_longitude_user = get_post_meta($publisher_id ,'foodbakery_post_loc_longitude_publisher'); 
        }else{
            $foodbakery_post_loc_latitude_user = get_user_meta($user_id,'foodbakery_post_loc_latitude_publisher' ); 
            $foodbakery_post_loc_longitude_user = get_user_meta($user_id ,'foodbakery_post_loc_longitude_publisher'); 
        }


  


  //  echo 'user logged in';
  //  print_r($foodbakery_post_loc_longitude_user);

            }else{
                $foodbakery_post_loc_latitude_user[0] =0;
                $foodbakery_post_loc_longitude_user[0]=0;
            }


            ob_start();
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
            }
            ?>
            <div class="row">
                <?php if ($single_restaurant_title || $single_restaurant_subtitle) { ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                        <div class="element-title">
                            <?php if ($single_restaurant_title) { ?>
                                <h2><?php echo esc_html($single_restaurant_title); ?></h2>
                            <?php } ?>
                            <?php if ($single_restaurant_subtitle) { ?>
                                <p><?php echo esc_html($single_restaurant_subtitle); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                   <?php
                     $s_bg_color = get_theme_mod('m_bg_color');
                     $s_font_color = get_theme_mod('m_font_color');
                     $s_bottom_color = get_theme_mod('m_bottom_color',"#c33332");
                ?>
                <style type="text/css">
                    .swiper-container.swiper-container-horizontal.fixed-header { background:<?php echo $s_bg_color; ?>; }
                    .fixed-header .swiper-slide{ background:<?php echo $s_bg_color; ?>; padding: 10px;text-align: center;}
                    .swiper-slide > a {color:<?php echo $s_font_color; ?>; font-weight: 600;}
                    .swiper-container.swiper-container-horizontal.fixed-header {
                        border-bottom: 3px solid <?php echo $s_bottom_color; ?>;}
                </style>
                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 sticky-sidebar">
                     <div class="swiper-container">
                             <div class="swiper-wrapper">
                             
                                    <?php
                                    if ($total_items > 0) {
                                        $active_class = 'active';
                                        for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop ++) {
                                            for ($menu_items_loop = 0; $menu_items_loop < $total_items; $menu_items_loop ++) {
                                                if (isset($restaurant_menu_list[$menu_items_loop]['restaurant_menu']) && $total_menu[$menu_loop] == $restaurant_menu_list[$menu_items_loop]['restaurant_menu']) {
                                                    
                                                }
                                            }
                                            if (isset($total_menu[$menu_loop])) {
                                                ?>
                                                 <div class="swiper-slide <?php echo ($active_class); ?>"><a href="javascript:void(0)" class="menu-category-link" data-id="<?php echo absint($menu_loop) ?>"> <?php echo esc_html($total_menu[$menu_loop]); ?> </a></div>
                                                <?php
                                                $active_class = '';
                                            }
                                        }
                                    }
                                    ?>

                            </div>
                        </div>
                    <div class="filter-toggle"><span class="filter-toggle-text"><?php echo esc_html__('Categories By', 'foodbakery'); ?></span><i class="icon-chevron-down"></i></div>
                    <div class='filter-wrapper'>
                        <div class="categories-menu">
                            <h6><i class="icon-restaurant_menu"></i><?php echo esc_html__('Categories', 'foodbakery') ?></h6>
                            <ul class="menu-list">
                                <?php
                                if ($total_items > 0) {
                                    $active_class = 'active';
                                    for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop ++) {
                                        for ($menu_items_loop = 0; $menu_items_loop < $total_items; $menu_items_loop ++) {
                                            if (isset($restaurant_menu_list[$menu_items_loop]['restaurant_menu']) && $total_menu[$menu_loop] == $restaurant_menu_list[$menu_items_loop]['restaurant_menu']) {
                                                
                                            }
                                        }
                                        if (isset($total_menu[$menu_loop])) {
                                            ?>
                                            <li class="<?php echo ($active_class); ?>"><a href="javascript:void(0)" class="menu-category-link" data-id="<?php echo absint($menu_loop) ?>"> <?php echo esc_html($total_menu[$menu_loop]); ?> </a></li>
                                            <?php
                                            $active_class = '';
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-9 col-xs-12">
                    <!--Tabs Start-->
                    <div class="back-to-t"></div>
                    <div class="tabs-holder horizontal">
                        <ul class="stickynav-tabs nav nav-tabs">
                            <?php
                            $menu_active = 'class="active"';
                            $menu_active_tab = 'in active';
                            $review_active = '';
                            $review_active_tab = '';
                            if (isset($_GET['review_id']) && $_GET['review_id'] != '') {
                                $review_active = 'class="active"';
                                $menu_active = '';
                                $menu_active_tab = '';
                                $review_active_tab = 'in active';
                            }
                            ?>
                            <li <?php echo ($menu_active); ?>><a data-toggle="tab" href="#home"><i class="icon- icon-room_service"></i><?php esc_html_e('Menu', 'foodbakery') ?></a></li>
                            <?php
                            if ($foodbakery_user_reviews == 'on' && $transaction_restaurant_reviews == 'on') {
                                $ratings_data = array(
                                    'overall_rating' => 0.0,
                                    'count' => 0,
                                );
                                $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $foodbakery_restaurant_id);
                                ?>
                                <li <?php echo ($review_active); ?>>
                                    <a data-toggle="tab" href="#menu1"><i class="icon- icon-textsms"></i>
                                        <?php
                                        esc_html_e('Reviews', 'foodbakery');
                                        if ($ratings_data['count'] > 0) {
                                            ?> (<?php echo esc_html($ratings_data['count']); ?>)
                                        <?php } ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (isset($restaurant_table_booking) && $restaurant_table_booking == 'yes') { ?>
                                <li><a data-toggle="tab" href="#menu2"><i class="icon- icon-food"></i><?php esc_html_e('Book a Table', 'foodbakery') ?> </a></li>
                            <?php } ?>
                            <li><a data-toggle="tab" href="#menu3"><i class="icon- icon-info3"></i><?php esc_html_e('Restaurant Info', 'foodbakery') ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade <?php echo ($menu_active_tab); ?>">
                                <div class="menu-itam-holder">
                                    <div class="field-holder sticky-search">
                                        <input id="menu-srch-<?php echo absint($foodbakery_restaurant_id) ?>" data-id="<?php echo absint($foodbakery_restaurant_id) ?>" class="input-field dev-menu-search-field" type="text" placeholder="<?php esc_html_e('Search food item', 'foodbakery') ?>">
                                    </div>
                                    <div id="menu-item-list-<?php echo absint($foodbakery_restaurant_id) ?>" class="menu-itam-list">
                                        <?php
                                        $menu_items_bu = restaurant_detail_menu_list($foodbakery_restaurant_id);
                                        $menu_items_b_list = isset($menu_items_bu['items']) ? $menu_items_bu['items'] : '';
                                        $menu_items_b_bs = isset($menu_items_bu['bs_boxes']) ? $menu_items_bu['bs_boxes'] : '';
                                        echo force_balance_tags($menu_items_b_list);
                                        $extras_modal_boxes .= $menu_items_b_bs;
                                        ?>
                                    </div>

                                </div>
                            </div>
                            <?php if ($foodbakery_user_reviews == 'on' && $transaction_restaurant_reviews == 'on') { ?>
                                <div id="menu1" class="tab-pane fade <?php echo ($review_active_tab); ?>">
                                    <?php
                                    do_action('foodbakery_reviews_ui', $foodbakery_restaurant_id, 'yes', 'no');
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($restaurant_table_booking) && $restaurant_table_booking == 'yes') { ?>
                                <div id="menu2" class="tab-pane fade">
                                    <?php do_action('foodbakery_booking_element_html', $foodbakery_restaurant_id); ?>
                                </div>
                            <?php } ?>
                            <div id="menu3" class="tab-pane fade">
                                <?php
                                do_action('foodbakery_contact_element_html', $foodbakery_restaurant_id);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $get_added_menus = array();
                $user_id = $current_user->ID;
                $publisher_id = foodbakery_company_id_form_user_id($user_id);
                $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
                if ($publisher_id != '' && $publisher_type != '' && $publisher_type != 'restaurant') {
                    $get_added_menus = get_transient('add_menu_items_' . $publisher_id);
                    if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp'])) {
                        $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                    }
                } else {
                    if (isset($_COOKIE['add_menu_items_temp'])) {
                        $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                    }
                }

                $have_menu_orders = false;
                if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                    $have_menu_orders = true;
                }
                update_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cashes', 'no');
                $foodbakery_cash_payments = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cashes', true);
                ?>
                <div class="sticky-sidebar col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="user-order-holder 9">
                        <div class="user-order">
                            <h6><i class="icon-shopping-basket"></i><?php esc_html_e('Your Order ', 'foodbakery') ?></h6>
                            <?php
                            $restaurant_allow_pre_order = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_pre_order', true);
                            if ($restaurant_allow_pre_order == 'yes') {
                                echo '<span class="error-message pre-order-msg" style="display: ' . ($have_menu_orders === false ? 'block' : 'none') . ';">' . esc_html__('This restaurant allows Pre orders.', 'foodbakery') . '</span>';
                            }
                            $selected_fee_type = isset($get_added_menus[$foodbakery_restaurant_id . '_fee_type']) ? $get_added_menus[$foodbakery_restaurant_id . '_fee_type'] : '';
                            ?>
                            <span class="discount-info" style="display: <?php echo ($have_menu_orders === false ? 'block' : 'none') ?>;"><?php _e('If you have a discount code,<br> you will be able to input it<br> at the payments stage.', 'foodbakery') ?></span>
                            <?php
                           // if ($foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0 && $restaurant_pickup_delivery == 'delivery_and_pickup') {
                                ?>
                                <div class="select-option dev-select-fee-option" data-rid="<?php echo esc_html($foodbakery_restaurant_id) ?>">
                                    <ul>
                                    <li>
                                            <input id="order-delivery-fee" <?php echo ($selected_fee_type != 'pickup' ? 'checked="checked"' : '') ?> type="radio" name="order_fee_type" data-fee="<?php echo foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false); ?>" data-label="<?php esc_html_e('Delivery', 'foodbakery') ?>" data-type="delivery" />
                                            <label for="order-delivery-fee"><?php esc_html_e('Delivery', 'foodbakery') ?></label>
                                            <span><?php echo foodbakery_get_currency($foodbakery_delivery_fee, true); ?></span>
                                        </li>
                                        <li>
                                            <input id="order-pick-up-fee" type="radio" <?php echo ($selected_fee_type == 'pickup' ? 'checked="checked"' : '') ?> name="order_fee_type" data-fee="<?php echo foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false); ?>" data-label="<?php esc_html_e('Pick-Up', 'foodbakery') ?>" data-type="pickup" />
                                            <label for="order-pick-up-fee"><?php esc_html_e('Pick-Up', 'foodbakery') ?></label>
                                            <span><?php echo foodbakery_get_currency($foodbakery_pickup_fee, true); ?></span>
                                        </li>
                                        
                                    </ul>
                                </div>
                                <?php
                           // }
                            ?>
                            <div class="dev-menu-orders-list" style="display: <?php echo ($have_menu_orders === true ? 'block' : 'none') ?>;">

                                <ul class="categories-order" data-rid="<?php echo absint($foodbakery_restaurant_id) ?>">
                                    <?php
                                    if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                                        $item_count = 1;
                                        $rand_numb_class = 10000001;
                                        foreach ($get_added_menus[$foodbakery_restaurant_id] as $menu_key => $menu_ord_item) {

                                            if (isset($menu_ord_item['menu_id']) && isset($menu_ord_item['price'])) {
                                                $rand_numb = rand(10000000, 99999999);
                                                $menu_t_price = 0;
                                                $this_menu_cat_id = isset($menu_ord_item['menu_cat_id']) ? $menu_ord_item['menu_cat_id'] : '';
                                                $this_item_id = $menu_ord_item['menu_id'];
                                                $this_item_price = $menu_ord_item['price'];
                                                $this_item_extras = isset($menu_ord_item['extras']) ? $menu_ord_item['extras'] : '';

                                               // $menu_t_price += floatval($this_item_price);
                                                $this_item_title = isset($restaurant_menu_list[$this_item_id]['menu_item_title']) ? $restaurant_menu_list[$this_item_id]['menu_item_title'] : '';

                                                $menu_extra_li = '';
                                                $sa_category_price=0;
                                                if (is_array($this_item_extras) && sizeof($this_item_extras) > 0) {
                                                    $extra_m_counter = 0;
                                                    $menu_extra_li .= '<ul>';
                                                    foreach ($this_item_extras as $this_item_extra_at) {
                                                        //$this_item_heading = isset($restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$extra_m_counter]) ? $restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$extra_m_counter] : '';
                                                        $item_extra_at_title = isset($this_item_extra_at['title']) ? $this_item_extra_at['title'] : '';
                                                        $item_extra_at_price = isset($this_item_extra_at['price']) ? $this_item_extra_at['price'] : '';
                                                        $item_extra_quantitye = isset($this_item_extra_at['quantity']) ? (int)$this_item_extra_at['quantity'] : '';
                                                        if ($item_extra_at_title != '' || $item_extra_at_price > 0) {
                                                            $menu_extra_li .= '<li>' . $this_item_heading . ' - ' . $item_extra_at_title . ' X '.$item_extra_quantitye.' : <span class="category-price">' . foodbakery_get_currency($item_extra_at_price* $item_extra_quantitye, true) . ' </span></li>';
                                                        }
                                                        //$menu_extra_li .= '<li>some detsils</li>';
                                                        $menu_t_price += floatval($item_extra_at_price*$item_extra_quantitye);
                                                        $extra_m_counter ++;
                                                        $sa_category_price +=floatval($item_extra_at_price*$item_extra_quantitye);
                                                    }
                                                    $menu_extra_li .= '</ul>';
                                                    $popup_id = 'edit_extras-' . $this_menu_cat_id . '-' . $this_item_id;
                                                    $data_id = $this_item_id;
                                                    $data_cat_id = $this_menu_cat_id;
                                                    $ajax_url = admin_url('admin-ajax.php');
                                                    $unique_id = $get_added_menus[$foodbakery_restaurant_id][$menu_key]['unique_id'];
                                                    $extra_child_menu_id = isset($get_added_menus[$foodbakery_restaurant_id][$menu_key]['extra_child_menu_id']) ? $get_added_menus[$foodbakery_restaurant_id][$menu_key]['extra_child_menu_id'] : '';
                                                    //$menu_extra_li .= '<a href="javascript:void(0);" class="edit-menu-item 123 update_menu_'.$rand_numb_class.'" onClick="foodbakery_edit_extra_menu_item(\'' . $popup_id . '\',\'' . $data_id . '\',\'' . $data_cat_id . '\',\'' . $rand_numb_class . '\',\'' . $ajax_url . '\',\'' . $foodbakery_restaurant_id . '\',\'' . $unique_id . '\',\'' . $extra_child_menu_id . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>';
                                                }
                                                ?>
                                                <li class="menu-added-<?php echo $rand_numb_class; ?>" id="menu-added-<?php echo absint($rand_numb) ?>" class="item_count_<?php echo $item_count;?>" data-pr="<?php echo foodbakery_get_currency($menu_t_price, false, '', '', false); ?>" data-conpr="<?php echo foodbakery_get_currency($menu_t_price, false, '', '', true); ?>">
                                                    <a href="javascript:void(0)" class="btn-cross dev-remove-menu-item"><i class=" icon-cross3"></i></a>
                                                    <a><?php echo esc_html($this_item_title) ?></a>
                                                    <span class=" "><?php echo foodbakery_get_currency($menu_t_price, true, '', '', true); ?></span>
                                                    <?php echo force_balance_tags($menu_extra_li) ?>
                                                </li>
                                                <?php
                                            }
                                            $item_count++;
                                            $rand_numb_class++;
                                        }
                                    }
                                    ?>
                                </ul>
                                <div class="price-area dev-menu-price-con" data-vatsw="<?php echo esc_html($foodbakery_vat_switch) ?>" data-vat="<?php echo floatval($foodbakery_payment_vat) ?>">
                                    <ul>
                                        <input type="hidden" id="order_subtotal_price" name="order_subtotal_price" value="<?php echo restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, '', '', '', false) ?>">
                                        <li><?php esc_html_e('Subtotal', 'foodbakery') ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-subtotal">'.restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, false, false, false, true).'</em>', foodbakery_get_currency_sign()); ?>
                                                </span></li>

                                        <?php
                                        $show_fee_type = '';
                                        if ($selected_fee_type == 'delivery' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                                            $show_fee_type = 'delivery';
                                        } else if ($selected_fee_type == 'pickup' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                                            $show_fee_type = 'pickup';
                                        } else {
                                            if ($foodbakery_delivery_fee > 0 && $restaurant_pickup_delivery != 'pickup') {
                                                $show_fee_type = 'delivery';
                                            } else if ($foodbakery_pickup_fee > 0 && $restaurant_pickup_delivery != 'delivery') {
                                                $show_fee_type = 'pickup';
                                            }
                                        }

                                        if ($show_fee_type == 'delivery') {
                                            ?>
                                            <li class="restaurant-fee-con"><span class="fee-title"><?php esc_html_e('Delivery fee', 'foodbakery') ?></span> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges" data-confee="'.foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', true).'" data-fee="'.foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false).'">'.foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', true).'</em>',  foodbakery_get_currency_sign()); ?>
                                                    </span></li>
                                            <?php
                                        } else if ($show_fee_type == 'pickup') {
                                            ?>
                                            <li class="restaurant-fee-con"><span class="fee-title"><?php esc_html_e('Pickup fee', 'foodbakery') ?></span> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges" data-confee="'.foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', true).'" data-fee="'. foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false).'">'.foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', true).'</em>',foodbakery_get_currency_sign());  ?></span></li>
                                            <?php
                                        }

                                        if ($foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                                            ?>
                                            <input type="hidden" id="order_vat_percent" name="order_vat_percent" value="<?php echo ($foodbakery_payment_vat); ?>">
                                            <input type="hidden" id="order_vat_cal_price" name="order_vat_cal_price" value="<?php echo restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, false, true); ?>">
                                            <li><?php printf(esc_html__('VAT (%s&#37;)', 'foodbakery'), $foodbakery_payment_vat) ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-vtax">'.restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, false, true, true).'</em>', foodbakery_get_currency_sign()); ?></span></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                    <p class="total-price"><?php esc_html_e('Total', 'foodbakery') ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-grtotal">'.restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, true, false, true).'</em>', foodbakery_get_currency_sign());  ?></span></p>
                                </div>
                            </div>
                            <div id="dev-no-menu-orders-list" style="display: <?php echo ($have_menu_orders === false ? 'block' : 'none') ?>;">
                                <?php echo '<span class="success-message">' . esc_html__('There are no items in your basket.', 'foodbakery') . '</span>' ?>
                            </div>
                            <?php
                            if ($foodbakery_cash_payments != 'yes') {
                                ?>
                                <div class="pay-option dev-order-pay-options">
                                    <ul>
                                        <?php
                                         $cash_checked ="";
                                        $foodbakery_restaurant_disable_cash = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cash', true);

                                        if (empty($foodbakery_restaurant_disable_cash) || $foodbakery_restaurant_disable_cash == '') {
                                            $foodbakery_restaurant_disable_cash = 'no';
                                        }

                                        if ($foodbakery_restaurant_disable_cash == 'no') {

                                               $is_card_payment = get_theme_mod('card_payment_status',true);
                                               if($is_card_payment == 'no'){
                                                    $cash_checked = "checked";
                                               }
                                            ?>

                                            <li>
                                                <input id="order-cash-payment"  checked="checked" <?php //echo $cash_checked; ?> type="radio" name="order_payment_method" data-type="cash" />
                                                <label for="order-cash-payment">
                                                    <i class="icon-coins"></i>
                                                    <?php esc_html_e('Cash', 'foodbakery') ?>
                                                </label>
                                            </li>
                                        <?php } ?>

                                        <?php
                                            $is_card_payment = get_theme_mod('card_payment_status',true);
                                            
                                            if($is_card_payment == 'yes'){


                                        ?>
                                        <li>
                                            <input id="order-card-payment" type="radio"  name="order_payment_method" data-type="card" />
                                            <label for="order-card-payment">
                                                <i class="icon-credit-card4"></i>
                                                <?php esc_html_e('Card', 'foodbakery') ?>
                                            </label>
                                        </li>

                                    <?php } ?>
                                    </ul>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="input-group date" >
                                            <input type="text" name="delivery_date" id="datetimepicker1" class="form-control" value="<?php echo date('d-m-Y H:i'); ?>" placeholder="Select Date and Time"/>
                                            <span class="input-group-addon">
                                                <span class="icon-event_available"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    jQuery(function () {
                                        jQuery('#datetimepicker1').datetimepicker({
                                            format: 'd-m-Y H:i',
                                            timepicker: true,
                                            minDate: '<?php echo date('d-m-Y H:i'); ?>',
                                            step: 15
                                        });
                                    });
                                </script>
                            </div>
                            <div style="display:none;" id="sa_data"
                                                        distance="<?php echo $foodbakery_max_distance; ?>"
                                                        user_lat="<?php echo $foodbakery_post_loc_latitude_user[0] ?>"
                                                        user_lag="<?php echo $foodbakery_post_loc_longitude_user[0];  ?>"
                                                        resturent_lag="<?php echo $foodbakery_post_loc_longitude_restaurant; ?>"
                                                        resturent_lat="<?php echo $foodbakery_post_loc_latitude_restaurant; ?>">
                                                        data
                                                    </div>
                            <a href="javascript:void(0)" class="menu-order-confirm " id="menu-order-confirm" data-rid="<?php echo absint($foodbakery_restaurant_id) ?>"><?php esc_html_e('Confirm Order', 'foodbakery') ?></a>
                            <span class="menu-loader"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start Edit extra modal -->
            <div id="edit_extra_modal"></div>
             <script type="text/javascript">
                jQuery(document).ready(function () {

                    // alert('ddddddddddddddss');
                    //jQuery('#extras-0-9').css("display", "none");
                    //jQuery('.add_extrass').hide();
                    jQuery(window).load(function () {
                        //var modal_id = jQuery('[id^="edit_extras-"]').attr('id');
                        //var id_ = jQuery('[id^="extras-"]').length
                        //jQuery('#' + modal_id).remove();
                        //jQuery('#' + id_).remove();
                        //jQuery('#edit_extras-0-1').remove();

                    });
                });
                jQuery(window).scroll(function(){
                    if (jQuery(window).scrollTop() >= 600) {
                       jQuery('.swiper-container.swiper-container-horizontal').addClass('fixed-header');
                    }
                    else {
                        jQuery('.swiper-container.swiper-container-horizontal').removeClass('fixed-header');
                    }
                });
                 jQuery(document).ready(function(){
                    var swiper = new Swiper('.swiper-container', {
                      slidesPerView: 1,
                      spaceBetween: 1,
                      // init: false,
                      breakpoints: {
                        640: {
                          slidesPerView: 2,
                          spaceBetween: 1,
                        },
                        768: {
                          slidesPerView: 2,
                          spaceBetween: 1,
                        },
                        1024: {
                          slidesPerView: 2,
                          spaceBetween: 1,
                        },
                      }
                    });
                });
            </script>
            <!-- End Edit extra modal -->
            <?php
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '</div>';
            }
            $restaurant_data = ob_get_clean();
            return $restaurant_data;
        }

    }

    global $foodbakery_shortcode_restaurant_search_front;
    $foodbakery_shortcode_restaurant_search_front = new Foodbakery_Shortcode_Single_Restaurant_front();
}