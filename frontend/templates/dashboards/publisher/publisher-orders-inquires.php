<?php
/**
 * Publisher Restaurants
 *
 */
if (!class_exists('Foodbakery_Publisher_Orders_Inquiries')) {

    class Foodbakery_Publisher_Orders_Inquiries {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('wp_enqueue_scripts', array($this, 'foodbakery_orders_element_scripts'), 11);
            add_action('wp_ajax_foodbakery_publisher_orders', array($this, 'foodbakery_publisher_orders_callback'), 11, 1);
            add_action('wp_ajax_foodbakery_publisher_received_orders', array($this, 'foodbakery_publisher_received_orders_callback'), 11, 3);
        }

        public function foodbakery_orders_element_scripts() {
            wp_enqueue_style('daterangepicker');
            wp_enqueue_script('daterangepicker-moment');
            wp_enqueue_script('daterangepicker');
        }

        public function foodbakery_publisher_orders_callback($publisher_id = '') {
            global $foodbakery_plugin_options;

            // Publisher ID.
            if (!isset($publisher_id) || $publisher_id == '') {
                $publisher_id = get_current_user_id();
            }

            $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

            $publisher_company_id = foodbakery_company_id_form_user_id($publisher_id);

            $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
            $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
            $date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';
            $status = isset($_POST['status']) ? $_POST['status'] : '';

            $args = array(
                'post_type' => 'orders_inquiries',
                'post_status' => 'publish',
                'posts_per_page' => $posts_per_page,
                'paged' => $posts_paged,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_order_user',
                        'value' => $publisher_company_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_order_type',
                        'value' => 'order',
                        'compare' => '=',
                    )
                ),
            );
            // Orders date range filter query
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
            // Orders status filter meta query
            if ($status != '' && $status != 'undefined') {
                $args['meta_query'][] = array(
                    'key' => 'foodbakery_order_status',
                    'value' => $status,
                    'compare' => '=',
                );
            }

            $order_query = new WP_Query($args);
            $total_posts = $order_query->found_posts;

            echo ($this->render_view_orders($order_query, 'my'));
            ?>
            <script>
                if (jQuery('.user-dashboard .order-list').length !== 0) {
                    jQuery('.user-dashboard .order-list').matchHeight();
                }
            </script>
            <?php
            wp_reset_postdata();

            $total_pages = 1;
            if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
                $total_pages = ceil($total_posts / $posts_per_page);

                $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                $this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'orders', 'status' => 'Completed'), $foodbakery_dashboard_link) : '';
                foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'orders');
            }

            wp_die();
        }

        public function foodbakery_publisher_received_orders_callback($publisher_id = '', $numberofpage = '-1', $call_by = 'inside') {
            // Publisher ID.
            global $current_user, $foodbakery_plugin_options;

            $user_id = $current_user->ID;
            $publisher_id = foodbakery_company_id_form_user_id($user_id);

           // echo 'publisher id '. $publisher_id;
            $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

            if ($publisher_type == 'restaurant') {
                $user_meta_key = 'foodbakery_publisher_id';
            } else {
                $user_meta_key = 'foodbakery_order_user';
            }

            $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

            $publisher_company_id = foodbakery_company_id_form_user_id($publisher_id);

            $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
            $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
            $date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';
            $status = isset($_POST['status']) ? $_POST['status'] : '';

            $args = array(
                'post_type' => 'orders_inquiries',
                'post_status' => 'publish',
                'posts_per_page' => $posts_per_page,
                'paged' => $posts_paged,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => $user_meta_key,
                        'value' => $publisher_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_order_type',
                        'value' => 'order',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_order_status',
                        'value' => 'process',
                        'compare' => '!=',
                    )
                ),
            );
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
            // Orders status meta query
            if ($status != '' && $status != 'undefined') {
                $args['meta_query'][] = array(
                    'key' => 'foodbakery_order_status',
                    'value' => $status,
                    'compare' => '=',
                );
            }

            $order_query = new WP_Query($args);
            $total_posts = $order_query->found_posts;

            echo ($this->render_view_orders($order_query, 'received', $call_by));
            wp_reset_postdata();

            $total_pages = 1;
            if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
                $total_pages = ceil($total_posts / $posts_per_page);

                $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                $this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'received_orders'), $foodbakery_dashboard_link) : '';
                foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'received_orders');
            }

            wp_die();
        }

        public function render_view_orders($order_query = '', $type = 'my', $call_by = '') {
            global $foodbakery_plugin_options, $foodbakery_form_fields;
            ?>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="element-title has-border right-filters-row">
                    
                        <?php
                        $order_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
                        $date_range = isset($_REQUEST['date_range']) ? $_REQUEST['date_range'] : '';
                        if ($type == 'my') {
                            ?>
                            <h5><?php esc_html_e('My Orders', 'foodbakery'); ?></h5>
                            <?php
                            $action_id = 'foodbakery_publisher_orders';
                        } else {
                            if ($call_by == 'inside') {
                                $tab_title = esc_html__('Orders', 'foodbakery');
                            } else {
                                $tab_title = esc_html__('Recent Orders', 'foodbakery');
                            }
                            ?>
                            <h5><?php echo esc_html($tab_title); ?></h5>
                            <?php
                            $action_id = 'foodbakery_publisher_received_orders';
                        }
                        ?>
                        <div class="right-filters row pull-right">


                        <?php if ((!$order_query->have_posts() && $order_status != '') || $order_query->have_posts()) { ?>
                                <div class="col-lg-2 col-md-2 col-xs-4">
                                    

                                        <button class="btn" id="sa_btn_delete">Delete</button>


                                    
                                </div>


                            <?php } ?>
                            <?php if ((!$order_query->have_posts() && $order_status != '') || $order_query->have_posts()) { ?>
                                <div class="col-lg-5 col-md-5 col-xs-4">
                                    <div class="input-field">
                                        <?php
                                        $drop_down_options[''] = esc_html__('Select Orders Status', 'foodbakery');
                                        $orders_status = isset($foodbakery_plugin_options['orders_status']) ? $foodbakery_plugin_options['orders_status'] : '';
                                        if (is_array($orders_status) && sizeof($orders_status) > 0) {
                                            foreach ($orders_status as $key => $label) {
                                                $drop_down_options[$label] = esc_html__($label, 'foodbakery');
                                            }
                                        } else {
                                            $drop_down_options = array(
                                                'Processing' => esc_html__('Processing', 'foodbakery'),
                                                'Completed' => esc_html__('Completed', 'foodbakery'),
                                            );
                                        }

                                        $foodbakery_opt_array = array();
                                        $foodbakery_opt_array['std'] = $order_status;
                                        $foodbakery_opt_array['cust_id'] = 'filter_status';
                                        $foodbakery_opt_array['cust_name'] = 'order_status';
                                        $foodbakery_opt_array['options'] = $drop_down_options;
                                        $foodbakery_opt_array['classes'] = 'chosen-select-no-single';
                                        $foodbakery_opt_array['extra_atr'] = ' onchange="foodbakery_orders(this, \'' . $action_id . '\')"';
                                        $foodbakery_opt_array['return'] = false;

                                        $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
                                        ?>
                                        <script type="text/javascript">

                                            chosen_selectionbox();

                                        </script>
                                    </div>
                                </div>
                            <?php } ?>

                            

                            <?php if ((!$order_query->have_posts() && $date_range != '') || $order_query->have_posts()) { ?>
                                <div class="col-lg-5 col-md-5 col-xs-4">
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
                                                        var actionString = "<?php echo esc_html($action_id); ?>";
                                                        var pageNum = 1;
                                                        foodbakery_show_loader('.loader-holder');
                                                        var filter_parameters = get_filter_parameters();
                                                        if (typeof (ajaxRequest) != 'undefined') {
                                                            ajaxRequest.abort();
                                                        }
                                                        ajaxRequest = jQuery.ajax({
                                                            type: "POST",
                                                            url: foodbakery_globals.ajax_url,
                                                            data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters,
                                                            success: function (response) {
                                                                foodbakery_hide_loader();
                                                                jQuery('.user-holder').html(response);

                                                            }
                                                        });
                                                    });
                                            jQuery('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                                                jQuery('#daterange').val('');
                                                jQuery('#date_range').val('');
                                                var actionString = "<?php echo esc_html($action_id); ?>";
                                                var pageNum = 1;
                                                foodbakery_show_loader('.loader-holder');
                                                var filter_parameters = get_filter_parameters();
                                                if (typeof (ajaxRequest) != 'undefined') {
                                                    ajaxRequest.abort();
                                                }
                                                ajaxRequest = jQuery.ajax({
                                                    type: "POST",
                                                    url: foodbakery_globals.ajax_url,
                                                    data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters,
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
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="user-orders-list">
                        <?php if ($order_query->have_posts()) : ?>
                            <?php echo ($this->render_list_item_view($order_query, $type)); ?>
                        <?php else: ?>
                            <div class="not-found">
                                <i class="icon-error"></i>
                                <p>
                                    <?php
                                    if ($type == 'received') {
                                        esc_html_e('You don\'t have any received order.', 'foodbakery');
                                    } else {
                                        esc_html_e('You don\'t have any order.', 'foodbakery');
                                    }
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <?php
        }

        public function render_list_item_view($order_query, $type = 'my') {
            global $foodbakery_plugin_options, $current_user, $foodbakery_order_detail;

            $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
            $foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';
            $user_id = $current_user->ID;
            $publisher_id = foodbakery_company_id_form_user_id($user_id);
            $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
            $status_array = array(
                'processing' => foodbakery_plugin_text_srt('foodbakery_status_processing'),
                'Completed' => foodbakery_plugin_text_srt('foodbakery_status_completed'),
                'Cancelled' => foodbakery_plugin_text_srt('foodbakery_status_cancelled'),
            );
            $orders_status = isset($foodbakery_plugin_options['orders_status']) ? $foodbakery_plugin_options['orders_status'] : array();
            if (!empty($orders_status)) {
                foreach ($orders_status as $orderStatus) {
                    $status_array[$orderStatus] = $orderStatus;
                }
            }
            if ($publisher_type == 'restaurant') {
                ?>

                <script type="text/javascript">
                
               
                (function ($) {
                     $(document).ready(function () {
                        $("#sa_select_all").click(function(){
                                $('input:checkbox').not(this).prop('checked', this.checked);

                        });

                        $("#sa_btn_delete").click(function(){

                            var tbl_ser = $('.table-generic :input').serializeArray();

                       

                           var ajaxRequest = jQuery.ajax({
                                                    type: "POST",
                                                    url: foodbakery_globals.ajax_url,
                                                    data: {action:'saWpDeleteOrder', data:tbl_ser},
                                                    success: function (response) {
                                                       if(response.success){
                                                        window.location.reload();
                                                       }

                                                    }
                                                });


                                             //   console.log(ajaxRequest);



                        });


                    });
               })(jQuery);
                
                
                </script>




                <div class="responsive-table">
                    <ul class="table-generic">
                        <li class="order-heading-titles">
                            <div><input type="checkbox" id="sa_select_all"  /></div>
                            <div><?php esc_html_e('Order id', 'foodbakery') ?></div>
                            <div><?php esc_html_e('Date', 'foodbakery') ?></div>
                            <div><?php esc_html_e('Total Price', 'foodbakery') ?></div>
                            <div><?php esc_html_e('Charges', 'foodbakery') ?></div>
                            <div><?php esc_html_e('Received', 'foodbakery') ?></div>
                            <div><?php esc_html_e('Status', 'foodbakery') ?></div>
                            <div><?php esc_html_e('Detail', 'foodbakery') ?></div>

                        </li>
                        <?php
                    }
                    if ($publisher_type != 'restaurant') {
                        echo '<div class="row">';
                    }
                    $ordr_det_box = '';
                    while ($order_query->have_posts()) : $order_query->the_post();

                        $order_restaurant_id = get_post_meta(get_the_ID(), 'foodbakery_restaurant_id', true);
                        $foodbakery_cover_image = get_post_meta($order_restaurant_id, 'foodbakery_cover_image', true);
                        if (!is_numeric($foodbakery_cover_image) && $foodbakery_cover_image != '') {
                            $foodbakery_cover_image = foodbakery_get_attachment_id_from_url($foodbakery_cover_image);
                        }
                        $restaurant_categories = get_post_meta($order_restaurant_id, 'foodbakery_restaurant_category', true);
                        $restaurant_cats = $this->order_restaurant_categories($restaurant_categories);
                        $restaurant_address = '';
                        if (function_exists('foodbakery_restaurant_address_from_locations')) {
                            $restaurant_address = foodbakery_restaurant_address_from_locations($order_restaurant_id);
                        }
                        $menu_order_fee_type = get_post_meta(get_the_ID(), 'menu_order_fee_type', true);
                        $menu_order_fee = get_post_meta(get_the_ID(), 'menu_order_fee', true);
                        $wp_old_slug = get_post_meta(get_the_ID(), '_wp_old_slug', true);
                        $menu_items_list = get_post_meta(get_the_ID(), 'menu_items_list', true);
                        $order_id = get_post_meta(get_the_ID(), 'foodbakery_order_id', true);
                        $order_type = get_post_meta(get_the_ID(), 'foodbakery_order_type', true);
                        $order_date = get_post_meta(get_the_ID(), 'foodbakery_order_date', true);
                        $foodbakery_delivery_date = get_post_meta(get_the_ID(), 'foodbakery_delivery_date', true);
                        $order_subtotal_price = get_post_meta(get_the_ID(), 'order_subtotal_price', true);
                        $order_price = get_post_meta(get_the_ID(), 'services_total_price', true);
                        $currency_sign = get_post_meta(get_the_ID(), 'foodbakery_currency', true);
                        if ($currency_sign == '') {
                            $currency_sign = foodbakery_get_currency_sign();
                        }
                        $currency_sign .= ' ';
                        $order_quantity = get_post_meta(get_the_ID(), 'services_total_quantity', true);
                        $order_status = get_post_meta(get_the_ID(), 'foodbakery_order_status', true);


                        $order_status_color = $this->order_status_color($order_status);
                        $buyer_read_status = get_post_meta(get_the_ID(), 'buyer_read_status', true);
                        $seller_read_status = get_post_meta(get_the_ID(), 'seller_read_status', true);
                        if ($type == 'my') {
                            if ($buyer_read_status == 1) {
                                $read_unread = 'read';
                            } else {
                                $read_unread = 'unread';
                            }
                        } else {
                            if ($seller_read_status == 1) {
                                $read_unread = 'read';
                            } else {
                                $read_unread = 'unread';
                            }
                        }
                        if ($publisher_type == 'restaurant') {
                            $amount_recieved = foodbakery_get_currency($order_price, true);
                            $amount_charged = '-';
                            $amount_recieved_meta = get_post_meta(get_the_ID(), 'order_amount_credited', true);
                            $amount_charged_meta = get_post_meta(get_the_ID(), 'order_amount_charged', true);
                            if ($amount_recieved_meta && $amount_charged_meta) {
                                $amount_recieved = foodbakery_get_currency($amount_recieved_meta, true, '', '', false);
                                $amount_charged = foodbakery_get_currency($amount_charged_meta, true, '', '', false);
                            }
                            $order_price_total = restaurant_menu_price_calc('defined', $order_subtotal_price, $menu_order_fee, true, false, false);
                            $amount_charged_ = foodbakery_get_currency($amount_charged_meta, false, '', '', false)
                            ?>
                            <li class="order-heading-titles">
                                <div><input type="checkbox" name="<?php echo get_the_ID() ?>" /></div>
                                <div><a href="javascript:void(0)" data-toggle="modal" data-target="#order-det-<?php echo get_the_ID() ?>"><?php echo 'order-' . esc_html($order_id) ?></a></div>
                                <div><?php echo get_the_time(get_option('date_format'), get_the_ID()); ?></div>
                                <div><?php echo restaurant_menu_price_calc('defined', $order_subtotal_price, $menu_order_fee, true, false, false, '', true); ?></div>
                                <div><?php echo esc_html($amount_charged) ?></div>
                                <div><?php $received = foodbakery_get_currency(($order_price_total - $amount_charged_), true, '', '', false);
                                    echo $received; ?></div>
                                <div><span class="order-status" style="background-color: <?php echo ($order_status_color); ?>;"><?php echo __($status_array[$order_status], 'foodbakery'); ?></span></div>
                                <div><a href="javascript:void(0)" data-toggle="modal" data-target="#order-det-<?php echo get_the_ID() ?>"><i class="icon-plus2 text-color"></i></a></div>
                            </li>
                            <?php
                            ob_start();
                            do_action('foodbakery_order_detail', get_the_ID(), $type);
                            $ordr_det_box .= ob_get_clean();
                        } else {
                            $order_id = get_the_ID();
                            $restaurant_user_reviews = $this->foodbakery_restaurant_user_reviews($order_id);
                            $transaction_restaurant_reviews = get_post_meta($order_restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);
                            $overall_rating = $this->foodbakery_get_overall_rating($order_restaurant_id, $order_id, $publisher_id);
                            $order_status = get_post_meta($order_id, 'foodbakery_order_status', true);

                            $foodbakery_cover_image_id = $foodbakery_cover_image;
                            if ($foodbakery_cover_image != '' && is_numeric($foodbakery_cover_image)) {
                                $foodbakery_cover_image = wp_get_attachment_url($foodbakery_cover_image);
                            }
                            if ($foodbakery_cover_image == '') {
                                $foodbakery_cover_image = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-image4x3.jpg';
                            }
                            $order_restaurant_detail_url = get_permalink($order_restaurant_id);
                            ?>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="order-list">
                                    <div class="author-info">
                                        <?php if ($foodbakery_cover_image != '') { ?>
                                            <div class="img-holder">
                                                <figure>
                                                    <a href="<?php echo esc_url($order_restaurant_detail_url); ?>"><?php echo '<img src="' . esc_url($foodbakery_cover_image) . '" alt="' . esc_html__('Restaurant Logo', 'foodbakery') . '"> '; ?></a>
                                                </figure>
                                            </div>
                                        <?php } ?>
                                        <div class="text-holder">
                                            <h6><a href="<?php echo esc_url($order_restaurant_detail_url); ?>"><?php echo get_the_title($order_restaurant_id); ?></a></h6>
                                            <?php if ($restaurant_cats != '') { ?>
                                                <address><?php echo esc_html($restaurant_cats); ?></address>
                                            <?php } ?>
                                            <span class="price"><?php
                                                $check_delivery_fee = apply_filters('foodbakery_check_delivery_tax', false);
                                                if ($check_delivery_fee) {
                                                    $services_total_price = get_post_meta($order_id, 'services_total_price', true);
                                                    echo foodbakery_get_currency($services_total_price, true, '', '', false);
                                                } else {
                                                    echo restaurant_menu_price_calc('defined', $order_subtotal_price, $menu_order_fee, true, false, false, '',true);
                                                }
                                                ?></span>
                                        </div>
                                    </div>
                                    <div class="post-time">
                                        <?php if (!empty($restaurant_address) && is_array($restaurant_address)) { ?>
                                            <span><?php echo implode(', ', $restaurant_address); ?></span>
                                        <?php } ?>
                                            <?php if ($menu_order_fee_type != '') { ?>
                                            <span>
                                                <?php
                                                if ($menu_order_fee_type == 'delivery') {
                                                    $delivery_time = get_post_meta($order_id, 'foodbakery_order_delivery_time', true);
                                                    printf(esc_html__('Deliver in %s Minutes', 'foodbakery'), $delivery_time);
                                                } else {
                                                    $pickup_time = get_post_meta($order_id, 'foodbakery_order_delivery_time', true);
                                                    printf(esc_html__('Pick Up in %s Minutes', 'foodbakery'), $pickup_time);
                                                }
                                                ?>
                                            </span>
                                            <?php
                                        }
                                        if ($overall_rating == '' && $order_status == 'Completed' && $restaurant_user_reviews == 'on' && $transaction_restaurant_reviews == 'on') {
                                            ?>
                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#order-review-<?php echo esc_html($order_id); ?>"><?php esc_html_e('give review', 'foodbakery'); ?></a>
                                            <div class="modal fade menu-order-detail order-review" id="order-review-<?php echo esc_html($order_id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h2><a><?php esc_html_e('Order Review', 'foodbakery') ?></a></h2>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="order-detail-inner">
                                                                <?php
                                                                do_action('foodbakery_review_form_ui', $order_restaurant_id, $order_id);
                                                                ?>
                                                                <script>
                                                                    (function ($) {
                                                                        $(document).ready(function () {
                                                                            $("#order-review-<?php echo esc_html($order_id); ?> .modal-dialog .modal-content").mCustomScrollbar({
                                                                                setHeight: 724,
                                                                                theme: "minimal-dark",
                                                                                mouseWheelPixels: 100
                                                                            });
                                                                        });
                                                                    })(jQuery);
                                                                </script>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php } ?>
                                    </div>
                    <?php if ($overall_rating != '' && is_numeric($overall_rating)) { ?>
                                        <div class="rating-holder">
                                            <div class="rating">
                                                <div class="rating-box" style="width:<?php echo ($overall_rating * 20); ?>%;">
                                                </div>
                                            </div>
                                            <span>(<?php
                                                if ($overall_rating * 20 <= 20) {
                                                    esc_html_e('Very poor', 'foodbakery');
                                                } elseif ($overall_rating * 20 <= 40) {
                                                    esc_html_e('poor', 'foodbakery');
                                                } elseif ($overall_rating * 20 <= 60) {
                                                    esc_html_e('Average', 'foodbakery');
                                                } elseif ($overall_rating * 20 <= 80) {
                                                    esc_html_e('Good', 'foodbakery');
                                                } elseif ($overall_rating * 20 <= 100) {
                                                    esc_html_e('Out standing', 'foodbakery');
                                                }
                                                ?>)</span>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <span class="date-time">
                                        <?php
                                        if ($foodbakery_delivery_date != '') {
                                            echo date_i18n('M j, Y h:i A', $foodbakery_delivery_date);
                                        } else {
                                            echo date_i18n('M j, Y h:i A', $order_date);
                                        }
                                        ?>
                                    </span>
                                    <div class="order-btn">
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#order-det-<?php echo esc_html($order_id); ?>"><?php esc_html_e('Order Detail', 'foodbakery'); ?></a>
                    <?php $order_status_color = $this->order_status_color($order_status); ?>
                                        <span class="order-status" style="background-color: <?php echo ($order_status_color); ?>;"><?php echo __($status_array[$order_status], 'foodbakery'); ?></span>
                                    </div>
                    <?php do_action('foodbakery_order_detail', $order_id, $type); ?>
                                </div>
                            </div>
                            <?php
                        }
                    endwhile;
                    if ($publisher_type != 'restaurant') {
                        echo '</div>';
                    }
                    if ($publisher_type == 'restaurant') {
                        ?>
                    </ul>

                </div>
                <?php
                echo force_balance_tags($ordr_det_box);
            }
        }

        public function order_status_color($order_name = 'processing') {
            global $foodbakery_plugin_options;
            $orders_status = isset($foodbakery_plugin_options['orders_status']) ? $foodbakery_plugin_options['orders_status'] : '';
            $orders_color = isset($foodbakery_plugin_options['orders_color']) ? $foodbakery_plugin_options['orders_color'] : '';
            if (is_array($orders_status) && sizeof($orders_status) > 0) {
                foreach ($orders_status as $key => $lable) {
                    if (strtolower($lable) == strtolower($order_name)) {
                        return $order_color = isset($orders_color[$key]) ? $orders_color[$key] : '';
                        break;
                    }
                }
            }
        }

        public function order_restaurant_categories($restaurant_categories) {
            $restaurant_cats = '';
            if (is_array($restaurant_categories) && !empty($restaurant_categories)) {
                $comma = '';
                foreach ($restaurant_categories as $restaurant_category) {
                    if ($restaurant_category != '') {
                        $term = get_term_by('slug', $restaurant_category, 'restaurant-category');
                        $restaurant_cats .= $comma . $term->name;
                        $comma = ', ';
                    }
                }
            }
            return $restaurant_cats;
        }

        public function foodbakery_get_overall_rating($post_id = '', $order_id = '', $company_id = '') {
            global $foodbakery_order_detail;
            $post = get_post($post_id);
            $slug = '';
            $slug = $post->post_name;
            $overall_rating = '';
            $args = array(
                'post_type' => Foodbakery_Reviews::$post_type_name,
                'post_status' => array('publish', 'pending'),
                'posts_per_page' => 1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'post_id',
                        'value' => $slug,
                    ),
                    array(
                        'key' => 'order_id',
                        'value' => $order_id,
                    ),
                    array(
                        'key' => 'company_id',
                        'value' => $company_id,
                    )
                ),
            );
            $review_query = new WP_Query($args);
            if ($review_query->have_posts()): $review_query->the_post();
                $overall_rating = get_post_meta(get_the_ID(), 'overall_rating', true);
            endif;
            wp_reset_postdata();

            return $overall_rating;
        }

        public function foodbakery_restaurant_user_reviews($order_id = '') {
            $foodbakery_user_reviews = '';
            if ($order_id != '') {
                $restaurant_id = get_post_meta($order_id, 'foodbakery_restaurant_id', true);
                $foodbakery_restaurant_type = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
                $foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
                if ($restaurant_type_post = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type'))
                    $restaurant_type_id = $restaurant_type_post->ID;
                $restaurant_type_id = isset($restaurant_type_id) ? $restaurant_type_id : '';
                $foodbakery_user_reviews = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);
            }
            return $foodbakery_user_reviews;
        }

    }

    global $orders_inquiries;
    $orders_inquiries = new Foodbakery_Publisher_Orders_Inquiries();
}