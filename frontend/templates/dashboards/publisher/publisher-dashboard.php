<?php

/**
 * The template for displaying publisher dashboard
 *
 */

function cs_candidate_popup_style() {
    wp_enqueue_style('custom-publisher-style-inline', plugins_url('../../../../assets/frontend/css/custom_script.css', __FILE__));
    $cs_plugin_options = get_option('cs_plugin_options');
    $cs_custom_css = '#id_confrmdiv
    {
        display: none;
        background-color: #eee;
        border-radius: 5px;
        border: 1px solid #aaa;
        position: fixed;
        width: 300px;
        left: 50%;
        margin-left: -150px;
        padding: 6px 8px 8px;
        box-sizing: border-box;
        text-align: center;
    }
    #id_confrmdiv .button {
        background-color: #ccc;
        display: inline-block;
        border-radius: 3px;
        border: 1px solid #aaa;
        padding: 2px;
        text-align: center;
        width: 80px;
        cursor: pointer;
    }
    #id_confrmdiv .button:hover
    {
        background-color: #ddd;
    }
    #confirmBox .message {
        text-align: left;
        margin-bottom: 8px;
    }';
    wp_add_inline_style('custom-publisher-style-inline', $cs_custom_css);
}
add_action('wp_enqueue_scripts', 'cs_candidate_popup_style', 5);

get_header();

//editor
wp_enqueue_style('jquery-te');
wp_enqueue_script('jquery-te');

wp_enqueue_script('jquery-ui');
wp_enqueue_script('responsive-calendar');
wp_enqueue_script('foodbakery-tags-it');

//iconpicker
wp_enqueue_style('fonticonpicker');
wp_enqueue_script('fonticonpicker');
wp_enqueue_script('foodbakery-reservation-functions');
wp_enqueue_script('foodbakery-dashboard-common');

wp_enqueue_script('foodbakery-restaurant-add');
wp_enqueue_script('foodbakery-restaurant-menus');

/*Thankyou message*/
do_action('foodbakery_thankyou_message_order', foodbakery_get_input('tab', ''), isset( $_GET['response']) ?  $_GET['response'] : '');

$foodbakery_dashboard_strings = array(
    'valid_amount_error' => esc_html__('Please enter valid amount.', 'foodbakery'),
    'ajax_url' => admin_url('admin-ajax.php'),
    'plugin_url' => wp_foodbakery::plugin_url(),
);
wp_localize_script('foodbakery-dashboard-common', 'foodbakery_dashboard_strings', $foodbakery_dashboard_strings);

$post_id = get_the_ID();
$user_details = wp_get_current_user();
global $foodbakery_plugin_options, $Payment_Processing;
//$fullName = get_user_meta( $user_details->ID, 'first_name', true ) . ' ' . get_user_meta( $user_details->ID, 'last_name', true );
$user_company_id = get_user_meta($user_details->ID, 'foodbakery_company', true);
$fullName = isset($user_company_id) && $user_company_id != '' ? get_the_title($user_company_id) : '';
$profile_image_id = $foodbakery_publisher_profile->publisher_get_profile_image($user_details->ID);
$publisher_profile_type = get_post_meta($user_company_id, 'foodbakery_publisher_profile_type', true);

$profile_description = $user_details->description;

$cover_padding_top = isset($foodbakery_plugin_options['foodbakery_restaurant_cover_pading_top']) ? $foodbakery_plugin_options['foodbakery_restaurant_cover_pading_top'] : '';
$cover_padding_bottom = isset($foodbakery_plugin_options['foodbakery_restaurant_cover_pading_botom']) ? $foodbakery_plugin_options['foodbakery_restaurant_cover_pading_botom'] : '';

if ($publisher_profile_type == 'restaurant') {
    $current_user = wp_get_current_user();
    $publisher_id = foodbakery_company_id_form_user_id($current_user->ID);

    $args = array(
        'posts_per_page' => "1",
        'post_type' => 'restaurants',
        'post_status' => 'publish',
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'foodbakery_restaurant_publisher',
                'value' => $publisher_id,
                'compare' => '=',
            ),
            array(
                'key' => 'foodbakery_restaurant_username',
                'value' => $current_user->ID,
                'compare' => '=',
            ),
        ),
    );
    $custom_query = new WP_Query($args);
    $pub_restaurant = $custom_query->posts;
    if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '' && isset($_POST['food_menu_updating']) && $_POST['food_menu_updating'] == '1') {
        $restaurant_id = $pub_restaurant[0];

        // saving restaurant services
        $foodbakery_restaurant_menu_item_title = foodbakery_get_input('menu_item_title', '', 'ARRAY');
        $foodbakery_restaurants_menu = foodbakery_get_input('restaurant_menu', '', 'ARRAY');
        $foodbakery_restaurant_menu_item_price = foodbakery_get_input('menu_item_price', '', 'ARRAY');
        $foodbakery_restaurant_menu_item_icon = foodbakery_get_input('menu_item_icon', '', 'ARRAY');
        $foodbakery_restaurant_menu_item_nutri = foodbakery_get_input('menu_item_nutri', '', 'ARRAY');
        $foodbakery_restaurant_menu_item_desc = foodbakery_get_input('menu_item_desc', '', 'ARRAY');
        $foodbakery_restaurant_menu_item_extra = foodbakery_get_input('menu_item_extra', '', 'ARRAY');
        $foodbakery_restaurant_menu_item_action = foodbakery_get_input('menu_item_action', '', 'ARRAY');

        $foodbakery_restaurant_menu_item_post_status = foodbakery_get_input('menu_item_post_status', '', 'ARRAY');
        $foodbakery_restaurant_menu_item_comment = foodbakery_get_input('menu_item_comment', '', 'ARRAY');

        

        $menu_items_array = array();
        if (isset($_POST['menu_item_title']) && is_array($foodbakery_restaurant_menu_item_title) && sizeof($foodbakery_restaurant_menu_item_title) > 0) {
            $menu_items_array = array();
            foreach ($foodbakery_restaurant_menu_item_title as $key => $menu_item) {
                $menu_item_action = isset($foodbakery_restaurant_menu_item_action[$key]) ? $foodbakery_restaurant_menu_item_action[$key] : '';
                $menu_count = 0;
                if (isset($menu_item) && is_array($menu_item) && $menu_item != '') {
                    $menu_count = count($menu_item);
                }
                if ($menu_item != '' && $menu_item_action != 'add') {
                    $menu_items_array[] = array(
                        'menu_item_title' => $menu_item,
                        'menu_item_post_status' => isset($foodbakery_restaurant_menu_item_post_status[$key]) ? $foodbakery_restaurant_menu_item_post_status[$key] : '',
                        'menu_item_comment' => isset($foodbakery_restaurant_menu_item_comment[$key]) ? $foodbakery_restaurant_menu_item_comment[$key] : '',
                        'restaurant_menu' => isset($foodbakery_restaurants_menu[$key]) ? $foodbakery_restaurants_menu[$key] : '',
                        'menu_item_description' => isset($foodbakery_restaurant_menu_item_desc[$key]) ? $foodbakery_restaurant_menu_item_desc[$key] : '',
                        'menu_item_icon' => isset($foodbakery_restaurant_menu_item_icon[$key]) ? $foodbakery_restaurant_menu_item_icon[$key] : '',
                        'menu_item_nutri' => isset($foodbakery_restaurant_menu_item_nutri[$key]) ? $foodbakery_restaurant_menu_item_nutri[$key] : '',
                        'menu_item_price' => isset($foodbakery_restaurant_menu_item_price[$key]) ? $foodbakery_restaurant_menu_item_price[$key] : '',
                        'menu_item_extra' => isset($foodbakery_restaurant_menu_item_extra[$key]) ? $foodbakery_restaurant_menu_item_extra[$key] : '',
                    );
                }
            }
        }
        update_post_meta($restaurant_id, 'foodbakery_menu_items', $menu_items_array);

        // saving restaurant menu categories
        //if ( isset($_POST['menu_cat_title']) ) {
        $restaurant_menu_cat_titles = isset($_POST['menu_cat_title']) ? $_POST['menu_cat_title'] : '';
        update_post_meta($restaurant_id, 'menu_cat_titles', $restaurant_menu_cat_titles);

        $restaurant_menu_cat_descs = isset($_POST['menu_cat_desc']) ? $_POST['menu_cat_desc'] : '';
        update_post_meta($restaurant_id, 'menu_cat_descs', $restaurant_menu_cat_descs);
        //}
    }

    if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '' && isset($_POST['membership_updating']) && $_POST['membership_updating'] == '1') {
        $restaurant_id = $pub_restaurant[0];
        do_action('foodbakery_restaurant_add_save_assignments', $restaurant_id, $publisher_id);
    }

    if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {

        $foodbakery_restaurant_id = $pub_restaurant[0];

        $foodbakery_minimum_order_value = get_post_meta($foodbakery_restaurant_id, 'foodbakery_minimum_order_value', true);
        $foodbakery_restaurant_category = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_category', true);
        $foodbakery_restaurant_category_str = '';
        $cat_flag = 0;
        $count_flag = is_array($foodbakery_restaurant_category) ? sizeof($foodbakery_restaurant_category) : 0;
        if (isset($foodbakery_restaurant_category) && is_array($foodbakery_restaurant_category)) {
            foreach ($foodbakery_restaurant_category as $single_restaurant_category) {
                $term_single = get_term_by('slug', $single_restaurant_category, 'restaurant-category');
                $term_name = $term_single->name;
                if ($cat_flag != 0) {
                    if ($cat_flag != ($count_flag - 1)) {
                        $foodbakery_restaurant_category_str .= ', ';
                    }
                    if ($cat_flag == ($count_flag - 1)) {
                        $foodbakery_restaurant_category_str .= ' &amp; ';
                    }
                }
                $foodbakery_restaurant_category_str .= $term_name;
                $cat_flag ++;
            }
        }

        // get all reviews
        $ratings_data = array(
            'overall_rating' => 0.0,
            'count' => 0,
        );
        $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $foodbakery_restaurant_id);

        $foodbakery_restaurant_featured_image_src = '';
        $foodbakery_restaurant_featured_image_id = get_post_meta($foodbakery_restaurant_id, 'foodbakery_cover_image', true);

        $foodbakery_restaurant_cover_styles = '';
        $foodbakery_restaurant_cover_image_id = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_cover_image', true);
        if ($foodbakery_restaurant_cover_image_id == '') {
            $foodbakery_restaurant_cover_image_id = isset($foodbakery_plugin_options['foodbakery_restaurant_cover_image']) ? $foodbakery_plugin_options['foodbakery_restaurant_cover_image'] : '';
        }
        $foodbakery_default_profile_image = isset($foodbakery_plugin_options['foodbakery_default_placeholder_image']) ? $foodbakery_plugin_options['foodbakery_default_placeholder_image'] : '';
        if ($foodbakery_restaurant_cover_image_id != '') {
            $foodbakery_restaurant_cover_image_src = wp_get_attachment_url($foodbakery_restaurant_cover_image_id);
            if ($foodbakery_restaurant_cover_image_src != '') {
                $foodbakery_restaurant_cover_styles = ' background: url(' . $foodbakery_restaurant_cover_image_src . ') no-repeat scroll 0 0 / cover;';
            }
        }

        if ($cover_padding_top != '') {
            $foodbakery_restaurant_cover_styles .= ' padding-top: ' . $cover_padding_top . 'px !important;';
        }

        if ($cover_padding_bottom != '') {
            $foodbakery_restaurant_cover_styles .= ' padding-bottom: ' . $cover_padding_bottom . 'px !important;';
        }

        if ($foodbakery_restaurant_cover_styles != '') {
            $foodbakery_restaurant_cover_styles = ' style="' . $foodbakery_restaurant_cover_styles . '"';
        }
        ?>
        <div class="main-section ">
            <div class="page-content-fullwidth">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="page-section restaurant-detail-image-section" <?php echo force_balance_tags($foodbakery_restaurant_cover_styles); ?>>
                            <!-- Container Start -->
                            <div class="container">
                                <!-- Row Start -->
                                <div class="row">
                                    <!-- Column Start -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="company-info-detail">
                                            <div class="company-info">
                                                <?php
                                                if ($foodbakery_restaurant_featured_image_id != '' && is_numeric($foodbakery_restaurant_featured_image_id)) {
                                                    $foodbakery_restaurant_featured_image_src = wp_get_attachment_url($foodbakery_restaurant_featured_image_id);
                                                } elseif ($foodbakery_restaurant_featured_image_id != '') {
                                                    $foodbakery_restaurant_featured_image_src = $foodbakery_restaurant_featured_image_id;
                                                }
                                                if ($foodbakery_restaurant_featured_image_src == '') {
                                                    $foodbakery_restaurant_featured_image_src = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-image4x3.jpg';
                                                }
                                                if ($foodbakery_restaurant_featured_image_src != '') {
                                                    ?>
                                                    <div class="img-holder">
                                                        <figure> 
                                                            <img src="<?php echo esc_html($foodbakery_restaurant_featured_image_src); ?>" alt=""> 
                                                        </figure>
                                                    </div>
                                                    <?php
                                                }
                                                ?>

                                                <div class="text-holder">
                                                    <?php if ($ratings_data['count'] != 0) { ?>
                                                        <div class="rating-star">
                                                            <span class="rating-box" style="width: <?php echo intval($ratings_data['overall_rating']); ?>%;"></span> 
                                                        </div>
                                                        <span class="reviews">(<?php echo ($ratings_data['count'] > 0 ? $ratings_data['count'] : 0); ?> <?php echo esc_html__('Reviews', 'foodbakery'); ?>)</span> 
                                                    <?php } ?>
                                                    <span class="restaurant-title"><?php echo esc_html(get_the_title($foodbakery_restaurant_id)) ?></span>
                                                    <?php if ($foodbakery_restaurant_category_str != '') { ?>
                                                        <div class="text">
                                                            <i class="icon-local_pizza"></i>
                                                            <p><?php echo esc_html($foodbakery_restaurant_category_str); ?></p>
                                                        </div>  
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                    <!-- Column End -->
                                </div>
                                <!-- Row End -->
                            </div>
                            <!-- Container End -->
                        </div>


                        <?php
                    }
                } else {

                    $foodbakery_restaurant_cover_styles = '';
                    $foodbakery_restaurant_cover_image_id = get_post_meta(get_the_ID(), 'foodbakery_restaurant_cover_image', true);
                    if ($foodbakery_restaurant_cover_image_id == '') {
                        $foodbakery_restaurant_cover_image_id = isset($foodbakery_plugin_options['foodbakery_restaurant_cover_image']) ? $foodbakery_plugin_options['foodbakery_restaurant_cover_image'] : '';
                    }
                    $foodbakery_default_profile_image = isset($foodbakery_plugin_options['foodbakery_default_placeholder_image']) ? $foodbakery_plugin_options['foodbakery_default_placeholder_image'] : '';
                    if ($foodbakery_restaurant_cover_image_id != '') {
                        $foodbakery_restaurant_cover_image_src = wp_get_attachment_url($foodbakery_restaurant_cover_image_id);
                        if ($foodbakery_restaurant_cover_image_src != '') {
                            $sec_height = restaurant_get_image_height($foodbakery_restaurant_cover_image_src);
                            $foodbakery_restaurant_cover_styles = ' background: url(' . $foodbakery_restaurant_cover_image_src . ') no-repeat scroll 0 0 / cover;';
                        }
                    }

                    if ($cover_padding_top != '') {
                        $foodbakery_restaurant_cover_styles .= ' padding-top: ' . $cover_padding_top . 'px !important;';
                    }

                    if ($cover_padding_bottom != '') {
                        $foodbakery_restaurant_cover_styles .= ' padding-bottom: ' . $cover_padding_bottom . 'px !important;';
                    }

                    if ($foodbakery_restaurant_cover_styles != '') {
                        $foodbakery_restaurant_cover_styles = ' style="' . $foodbakery_restaurant_cover_styles . '"';
                    }

                    $foodbakery_default_profile_image = isset($foodbakery_plugin_options['foodbakery_default_placeholder_image']) ? $foodbakery_plugin_options['foodbakery_default_placeholder_image'] : '';
                    $pub_id = get_current_user_id();
                    $user_company = get_user_meta($pub_id, 'foodbakery_company', true);
                    $foodbakery_profile_image = get_post_meta($user_company, 'foodbakery_profile_image', true);
                    $foodbakery_profile_image = isset($foodbakery_profile_image) ? $foodbakery_profile_image : '';
                    $foodbakery_user_phone = get_post_meta($user_company, 'foodbakery_phone_number', true);
                    $display_user_email = get_post_meta($user_company, 'foodbakery_email_address', true);
                    $foodbakery_user_profile_image_src = '';
                    $display_name = wp_get_current_user()->display_name;
                    $company_id = foodbakery_company_id_form_user_id($pub_id);
                    $display_name = get_the_title($company_id);
                    ?>
                    <div class="main-section ">
                        <div class="page-content-fullwidth">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="page-section restaurant-detail-image-section" <?php echo force_balance_tags($foodbakery_restaurant_cover_styles); ?>>
                                        <!-- Container Start -->
                                        <div class="container">
                                            <!-- Row Start -->
                                            <div class="row">
                                                <!-- Column Start -->
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="company-info-detail">
                                                        <div class="company-info">
                                                            <?php
                                                            if ($foodbakery_profile_image != '') {
                                                                $foodbakery_user_profile_image_src = wp_get_attachment_url($foodbakery_profile_image);
                                                            } else if ($foodbakery_user_profile_image_src == '' && $foodbakery_default_profile_image != '') {
                                                                $foodbakery_user_profile_image_src = wp_get_attachment_url($foodbakery_default_profile_image);
                                                            } else if ($foodbakery_user_profile_image_src == '') {
                                                                $foodbakery_user_profile_image_src = wp_foodbakery::plugin_url() . '/assets/frontend/images/no-profile-image.jpg';
                                                            }
                                                            if ($foodbakery_user_profile_image_src != '') {
                                                                ?>
                                                                <div class="img-holder">
                                                                    <figure> 
                                                                        <img src="<?php echo esc_html($foodbakery_user_profile_image_src); ?>" alt=""> 
                                                                    </figure>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>

                                                            <div class="text-holder">
                                                                <span class="restaurant-title"><?php echo esc_html($display_name) ?></span>
                                                                <?php if ($foodbakery_user_phone != '' || $display_user_email != '') { ?>
                                                                    <ul class="user-info-contact">
                                                                        <?php if ($foodbakery_user_phone != '') { ?>
                                                                            <li class="cell"><i class="icon-phone"></i><a href="tel:<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', $foodbakery_user_phone); ?>"><?php echo esc_html($foodbakery_user_phone); ?></a></li>
                                                                        <?php }if ($display_user_email != '') { ?>
                                                                            <li class="email"><i class="icon-mail5"></i><a href="mailto:<?php echo esc_html($display_user_email); ?>"><?php echo esc_html($display_user_email); ?></a></li>
                                                                        <?php } ?>
                                                                    </ul>  
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                    </div>  
                                                </div>
                                                <!-- Column End -->
                                            </div>
                                            <!-- Row End -->
                                        </div>
                                        <!-- Container End -->
                                    </div>

                                    <?php
                                }

                                $main_section_class = '';
                                if (is_user_logged_in() && $publisher_profile_type == 'buyer') {
                                    $main_section_class = ' buyer-logged-in';
                                }
                                ?>
                                <div id="main">
                                    <div class="page-section account-header<?php echo ($main_section_class) ?>">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
                                                    <div class="user-account-nav user-account-sidebar">
                                                        <?php
                                                        $active_tab = ''; // default tab
                                                        $child_tab = '';
                                                        $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                                                        $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'location') { // for account settings active tab
                                                            $child_tab = 'foodbakery_publisher_change_locations';
                                                            $active_tab = 'foodbakery_publisher_accounts';
                                                        }
                                                        ?>
                                                        <div class="user-nav-list">
                                                            <ul>
                                                                <?php
                                                                if ($publisher_profile_type != 'restaurant') {
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'suggested') {
                                                                        $active_tab = 'foodbakery_publisher_suggested';
                                                                    }
                                                                    ?>
                                                                    <li class="user_dashboard_ajax active" id="foodbakery_publisher_suggested" data-queryvar="dashboard=suggested"><a href="javascript:void(0);"><i class="icon-dashboard3"></i><?php echo esc_html__(" Dashboard", "foodbakery") ?></a></li>

                                                                    <?php
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'bookings') {
                                                                        $active_tab = 'foodbakery_publisher_bookings';
                                                                    }
                                                                    ?>

                                                                    <li class="user_dashboard_ajax" id="foodbakery_publisher_bookings" data-queryvar="dashboard=bookings"><a href="javascript:void(0);" class="btn-edit-profile" ><i class="icon-file-text2"></i><?php echo esc_html__('My Bookings', 'foodbakery'); ?></a></li>
                                                                    <?php
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'reviews') {
                                                                        $active_tab = 'foodbakery_publisher_reviews';
                                                                    }
                                                                    ?>
                                                                    <li class="user_dashboard_ajax" id="foodbakery_publisher_reviews" data-queryvar="dashboard=reviews"><a href="javascript:void(0);" class="btn-edit-profile"><i class="icon-comment2"></i><?php echo esc_html__('My Reviews', 'foodbakery'); ?></a></li>
                                                                    <?php
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'orders') {
                                                                        $active_tab = 'foodbakery_publisher_orders';
                                                                    }
                                                                    ?>
                                                                    <li class="user_dashboard_ajax" id="foodbakery_publisher_orders" data-queryvar="dashboard=orders"><a href="javascript:void(0);" class="btn-edit-profile"><i class="icon-add_shopping_cart"></i><?php echo esc_html__('My Orders', 'foodbakery'); ?></a></li>
                                                                    <?php
                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('alerts')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'alerts') {
                                                                            $active_tab = 'foodbakery_publisher_restaurantalerts';
                                                                        }
                                                                        echo do_action('foodbakery_top_menu_publisher_dashboard', esc_html__('Saved Notifications', 'foodbakery'), '<i class="icon-save2"></i>');
                                                                    }

                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('shortlists')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'shortlists') {
                                                                            $active_tab = 'foodbakery_publisher_shortlists';
                                                                        }
                                                                        echo do_action('foodbakery_top_menu_shortlists_dashboard', esc_html__('Shortlists', 'foodbakery'), '<i class="icon-heart"></i>');
                                                                    }
                                                                    ?>
                                                                    <li class="user_dashboard_ajax" id="foodbakery_publisher_statements" data-queryvar="dashboard=statement"><a href="javascript:void(0);"><i class="icon-file-text22"></i><?php echo esc_html__("Statement", "foodbakery") ?></a></li>
                                                                    <?php
                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('company_profile')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'account') {
                                                                            $active_tab = 'foodbakery_publisher_accounts';
                                                                        }
                                                                        ?>
                                                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_accounts" data-queryvar="dashboard=account"><a href="javascript:void(0);"><i class="icon-build"></i><?php echo esc_html__("Account Settings", "foodbakery") ?></a></li>
                                                                        <?php
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'change_pass') {
                                                                        $active_tab = 'foodbakery_publisher_change_password';
                                                                    }
                                                                    ?>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="cs_remove_profile('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($current_user->ID) ?>', '<?php echo $GLOBALS['current_theme_template']; ?>')"><i class="icon-delete"></i><?php esc_html_e('Delete Profile', 'foodbakery'); ?></a>
                                                                    </li>
                                                                    <li><a href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-log-out"></i><?php echo esc_html__("Sign out", "foodbakery"); ?></a></li>

                                                                    <?php
                                                                }
                                                                if ($publisher_profile_type == 'restaurant') {
                                                                    $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
                                                                    ?>
                                                                    <li class="user_dashboard_ajax<?php echo ($get_tab != 'add-restaurant' ? ' active' : '') ?>" id="foodbakery_publisher_suggested" data-queryvar=""> <a href="javascript:void(0);"><i class="icon-dashboard3"></i><?php echo esc_html__(" Dashboard", "foodbakery") ?></a></li>	
                                                                    <?php
                                                                    $current_user = wp_get_current_user();
                                                                    $publisher_id = foodbakery_company_id_form_user_id($current_user->ID);

                                                                    $args = array(
                                                                        'posts_per_page' => "1",
                                                                        'post_type' => 'restaurants',
                                                                        'post_status' => 'publish',
                                                                        'fields' => 'ids',
                                                                        'meta_query' => array(
                                                                            'relation' => 'AND',
                                                                            array(
                                                                                'key' => 'foodbakery_restaurant_publisher',
                                                                                'value' => $publisher_id,
                                                                                'compare' => '=',
                                                                            ),
                                                                            array(
                                                                                'key' => 'foodbakery_restaurant_username',
                                                                                'value' => $current_user->ID,
                                                                                'compare' => '=',
                                                                            ),
                                                                        ),
                                                                    );
                                                                    $custom_query = new WP_Query($args);
                                                                    $total_restaurants = $custom_query->found_posts;
                                                                    wp_reset_postdata();


                                                                    $foodbakery_user_type = get_user_meta($current_user->ID, 'foodbakery_user_type', true);
                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('restaurants')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'restaurants') {
                                                                            $active_tab = 'foodbakery_publisher_restaurants';
                                                                        }
                                                                        $pub_restaurant = $custom_query->posts;

                                                                        if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
                                                                            $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                                                                            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                                                                            if (isset($_GET['lang'])) {
                                                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0], 'lang' => $_GET['lang']), $foodbakery_dashboard_link) : '#';
                                                                            } else if (cs_wpml_lang_url() != '') {
                                                                                $cs_lang_string = cs_wpml_lang_url();
                                                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0]), cs_wpml_parse_url($cs_lang_string, $foodbakery_dashboard_link)) : '#';
                                                                            } else {
                                                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'restaurant_id' => $pub_restaurant[0]), $foodbakery_dashboard_link) : '#';
                                                                            }
                                                                            ?>
                                                                            <li class="user_dashboard_url<?php echo (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'add-restaurant' ? ' active' : '') ?>"><a href="<?php echo esc_url_raw($foodbakery_restaurant_add_url) ?>"><i class="icon-building"></i><?php echo esc_html__("My Restaurant", "foodbakery") ?></a></li>
                                                                            <?php
                                                                        } else {
                                                                            $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                                                                            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                                                                            if (isset($_GET['lang'])) {
                                                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant', 'lang' => $_GET['lang']), $foodbakery_dashboard_link) : '#';
                                                                            } else if (cs_wpml_lang_url() != '') {
                                                                                $cs_lang_string = cs_wpml_lang_url();
                                                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant'), cs_wpml_parse_url($cs_lang_string, $foodbakery_dashboard_link)) : '#';
                                                                            } else {
                                                                                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('tab' => 'add-restaurant'), $foodbakery_dashboard_link) : '#';
                                                                            }
                                                                            ?>
                                                                            <li class="user_dashboard_url<?php echo (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'add-restaurant' ? ' active' : '') ?>"><a href="<?php echo esc_url_raw($foodbakery_restaurant_add_url) ?>"><i class="icon-building"></i><?php echo esc_html__("My Restaurant", "foodbakery") ?></a></li>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'food_menu') {
                                                                        $active_tab = 'foodbakery_publisher_food_menu';
                                                                    }
                                                                    ?>
                                                                    <?php do_action('foodbakery_add_coupon_manu_inrestaurant'); ?>
                                                                    <li class="user_dashboard_ajax" id="foodbakery_publisher_food_menu" data-queryvar="dashboard=food_menu"><a href="javascript:void(0);"><i class="icon-menu5"></i><?php echo esc_html__("Menu Builder", "foodbakery") ?></a></li>
                                                                    <?php
                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('orders') || true === Foodbakery_Member_Permissions::check_permissions('inquiries')) {

                                                                        $args = array(
                                                                            'post_type' => 'orders_inquiries',
                                                                            'post_status' => 'publish',
                                                                            'posts_per_page' => '-1',
                                                                            'fields' => 'ids',
                                                                            'meta_query' => array(
                                                                                'relation' => 'AND',
                                                                                array(
                                                                                    'key' => 'foodbakery_order_user_company',
                                                                                    'value' => $publisher_id,
                                                                                    'compare' => '=',
                                                                                ),
                                                                                array(
                                                                                    'key' => 'foodbakery_order_type',
                                                                                    'value' => 'order',
                                                                                    'compare' => '=',
                                                                                )
                                                                            ),
                                                                        );

                                                                        $order_query = new WP_Query($args);
                                                                        $total_orders = $order_query->found_posts;
                                                                        wp_reset_postdata();

                                                                        if (true === Foodbakery_Member_Permissions::check_permissions('orders')) {
                                                                            if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'received_orders') {
                                                                                $active_tab = 'foodbakery_publisher_received_orders';
                                                                            }
                                                                            ?>
                                                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_received_orders" data-queryvar="dashboard=received_orders"><a href="javascript:void(0);"><i class="icon-add_shopping_cart"></i><?php echo esc_html__("Orders", "foodbakery") ?></a></li>
                                                                            <?php
                                                                        }

                                                                        //$active_tab = apply_filters('foodbakery_refresh_page_coupon', isset($_REQUEST['dashboard']) ? $_REQUEST['dashboard'] : '');

                                                                        if (true === Foodbakery_Member_Permissions::check_permissions('inquiries')) {
                                                                            $args = array(
                                                                                'post_type' => 'orders_inquiries',
                                                                                'post_status' => 'publish',
                                                                                'posts_per_page' => '-1',
                                                                                'fields' => 'ids',
                                                                                'meta_query' => array(
                                                                                    'relation' => 'AND',
                                                                                    array(
                                                                                        'key' => 'foodbakery_order_user_company',
                                                                                        'value' => $publisher_id,
                                                                                        'compare' => '=',
                                                                                    ),
                                                                                    array(
                                                                                        'key' => 'foodbakery_order_type',
                                                                                        'value' => 'inquiry',
                                                                                        'compare' => '=',
                                                                                    )
                                                                                ),
                                                                            );

                                                                            $order_query = new WP_Query($args);
                                                                            $total_inquiries = $order_query->found_posts;
                                                                            wp_reset_postdata();
                                                                            if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'received_bookings') {
                                                                                $active_tab = 'foodbakery_publisher_received_bookings';
                                                                            }
                                                                            ?>
                                                                            <li class="user_dashboard_ajax" id="foodbakery_publisher_received_bookings" data-queryvar="dashboard=received_bookings"><a href="javascript:void(0);"><i class="icon-file-text2"></i><?php echo esc_html__("Bookings", "foodbakery") ?></a></li>
                                                                            <?php
                                                                        }
                                                                        ?>

                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('reviews')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'my_reviews') {
                                                                            $active_tab = 'foodbakery_publisher_my_reviews';
                                                                        }
                                                                        ?>
                                                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_my_reviews" data-queryvar="dashboard=my_reviews"><a href="javascript:void(0);"><i class="icon-comment2"></i><?php echo esc_html__("Reviews", "foodbakery") ?></a></li>
                                                                        <?php
                                                                    }

                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'packages') {
                                                                        $active_tab = 'foodbakery_publisher_packages';
                                                                    }

                                                                    if ($foodbakery_user_type != 'team-member' && true === Foodbakery_Member_Permissions::check_permissions('packages')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'packages') {
                                                                            $active_tab = 'foodbakery_publisher_packages';
                                                                        }
                                                                        ?>
                                                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_packages" data-queryvar="dashboard=packages"><a href="javascript:void(0);"><i class="icon-card_membership"></i><?php echo esc_html__("Memberships", "foodbakery") ?></a></li>

                                                                        <?php
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'withdrawals') {
                                                                        $active_tab = 'foodbakery_publisher_withdrawals';
                                                                    }

                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('withdrawals')) {
                                                                        ?>
                                                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_withdrawals" data-queryvar="dashboard=withdrawals"><a href="javascript:void(0);"><i class="icon-bill"></i><?php echo esc_html__("Withdrawals", "foodbakery") ?></a></li>
                                                                        <?php
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'change_pass') {

                                                                        $active_tab = 'foodbakery_publisher_change_password';
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'location') {
                                                                        $child_tab = 'foodbakery_publisher_change_locations';
                                                                        $active_tab = 'foodbakery_publisher_accounts';
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'inquiries_received') {
                                                                        $child_tab = 'foodbakery_publisher_received_inquiries';
                                                                        $active_tab = 'foodbakery_publisher_inquiries';
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'my_reviews') {
                                                                        $child_tab = 'foodbakery_publisher_my_reviews';
                                                                        $active_tab = 'foodbakery_publisher_reviews';
                                                                    }

                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('earnings')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'earnings') {

                                                                            $active_tab = 'foodbakery_publisher_earnings';
                                                                        }
                                                                        ?>
                                                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_earnings" data-queryvar="dashboard=earnings"><a href="javascript:void(0);"><i class="icon-money"></i><?php echo esc_html__("Earnings", "foodbakery") ?></a></li>
                                                                        <?php
                                                                    }
                                                                    if (true === Foodbakery_Member_Permissions::check_permissions('statements')) {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'statements') {

                                                                            $active_tab = 'foodbakery_publisher_statements';
                                                                        }
                                                                        ?>
                                                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_statements" data-queryvar="dashboard=statements"><a href="javascript:void(0);"><i class="icon-file-text22"></i><?php echo esc_html__("Statements", "foodbakery") ?></a></li>
                                                                        <?php
                                                                    }
                                                                    if ($foodbakery_user_type != 'team-member') {
                                                                        if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'team') {

                                                                            $active_tab = 'foodbakery_publisher_company';
                                                                        }
                                                                        ?>
                                                                        <li class="user_dashboard_ajax" id="foodbakery_publisher_company" data-queryvar="dashboard=team"><a href="javascript:void(0);"><i class="icon-flow-tree"></i><?php echo esc_html__("Team Management", "foodbakery") ?></a></li>
                                                                        <?php
                                                                    }
                                                                    if (isset($_REQUEST['dashboard']) && $_REQUEST['dashboard'] == 'change_pass') {
                                                                        $active_tab = 'foodbakery_publisher_change_password';
                                                                    }
                                                                    ?>
                                                                    <li class="user_dashboard_ajax" id="foodbakery_publisher_change_password" data-queryvar="dashboard=change_pass"><a href="javascript:void(0);"><i class="icon-unlock-alt"></i><?php echo esc_html__("Change Password", "foodbakery") ?></a></li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="cs_remove_profile('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($current_user->ID) ?>', '<?php echo $GLOBALS['current_theme_template']; ?>')"><i class="icon-delete"></i><?php esc_html_e('Delete Profile', 'foodbakery'); ?></a>
                                                                    </li>

                                                                    <li><a href="<?php echo esc_url(wp_logout_url(foodbakery_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-log-out"></i><?php echo esc_html__("Sign out", "foodbakery"); ?></a></li>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                                    <?php
                                                    if (foodbakery_get_input('tab', '') == 'add-restaurant') {
                                                        $restaurant_add_settings = array(
                                                            'return_html' => false,
                                                        );
                                                        ?>
                                                        <div class="user-dashboard loader-holder">
                                                            <?php do_action('foodbakery_restaurant_add', $restaurant_add_settings) ?>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <?php
                                                        if (isset($_GET['response']) && $_GET['response'] == 'order-completed') {
                                                            $foodbakery_announce_bg_color = isset($foodbakery_plugin_options['foodbakery_announce_bg_color']) ? $foodbakery_plugin_options['foodbakery_announce_bg_color'] : '#2b8dc4';
                                                            $foodbakery_order_data = $Payment_Processing->custom_order_status_display();
                                                            if (isset($foodbakery_order_data['order_id'])) {
                                                                $Payment_Processing->remove_raw_data($foodbakery_order_data['order_id']);
                                                                $temp_order = (int) $foodbakery_order_data['order_id'];
                                                                $temp_order = $temp_order-4;
                                                                update_post_meta($temp_order, 'foodbakery_order_status', 'processing');
                                                              //  print_r($foodbakery_order_data);
                                                            }
                                                            if(isset($foodbakery_order_data['foodbakery_order_id'])){
                                                                update_post_meta($foodbakery_order_data['foodbakery_order_id'], 'foodbakery_order_status', 'processing');
                                                                
                                                            }

                                                            ?>
                                                            <div id="close-me-order" class="user-message" style="background-color:<?php echo ($foodbakery_announce_bg_color); ?>" > 
                                                                <a onclick="remove_order_complete();" class="close close-div" href="javascript:void(0);"><i class="icon-cross-out"></i></a>
                                                                <h2><?php echo esc_html__("Congratulations", "foodbakery"); ?></h2>
                                                                <p><?php echo esc_html__("Your order has been created successfully.", "foodbakery"); ?></p>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>

                                                        <script>
                                                            function remove_order_complete() {
                                                                jQuery("#close-me-order").remove();
                                                            }

                                                        </script>
                                                        <div class="user-dashboard loader-holder 4">
                                                            <div class="user-holder">
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                    <?php
                                                                    if (!isset($_REQUEST['dashboard']) || $_REQUEST['dashboard'] == '') {
                                                                        ?>
                                                                        <script>jQuery(document).ready(function (e) {
                                                                                jQuery('#foodbakery_publisher_suggested>').trigger('click');
                                                                            });
                                                                        </script>
                                                                    <?php } ?>

                                                                </div>
                                                                <?php ?>

                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="page-section">
                                        <div class="container">
                                            <div class="row">
                                                <!-- warning popup -->
                                                <div id="id_confrmdiv">
                                                    <div class="cs-confirm-container">
                                                        <i class="icon-sad"></i>
                                                        <div class="message"><?php esc_html_e("You Want To Delete?", "foodbakery"); ?></div>
                                                        <a href="javascript:void(0);" id="id_truebtn"><?php esc_html_e("Yes, Delete", "foodbakery"); ?></a>
                                                        <a href="javascript:void(0);" id="id_falsebtn"><?php esc_html_e("No, Cancel", "foodbakery"); ?></a>
                                                    </div>
                                                </div>
                                                <!-- end warning popup -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($active_tab != '') {
                    ?>
                    <script type="text/javascript">
                        var page_id_all = <?php echo isset($_REQUEST['page_id_all']) && $_REQUEST['page_id_all'] != '' ? $_REQUEST['page_id_all'] : '1' ?>;
                        jQuery(document).ready(function (e) {
                            jQuery('#<?php echo esc_html($active_tab); ?>').trigger('click');

                        });
                        var count = 0;
                        jQuery(document).ajaxComplete(function (event, request, settings) {
                            if (count == 2) {
                                jQuery('#<?php echo esc_html($child_tab); ?>').trigger('click');
                            }

                            count++;
                        });
                    </script>
                    <?php
                }

                get_footer();
                