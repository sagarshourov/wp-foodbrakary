<?php
/**
 * @Add Meta Box For Publishers Post
 * @return
 *
 */
if ( ! class_exists( 'Foodbakery_Publishers_Meta' ) ) {

    class Foodbakery_Publishers_Meta {

        var $html_data = '';
        var $post_id = '';

        public function __construct() {
            add_action( 'add_meta_boxes', array( $this, 'foodbakery_meta_publishers_add' ) );
            add_action( 'add_meta_boxes', array( $this, 'foodbakery_meta_publishers_add' ) );
            add_action( 'wp_ajax_foodbakery_update_team_member', array( $this, 'foodbakery_update_team_member' ), 11 );
            add_action( 'wp_ajax_foodbakery_removed_shortlist_backend', array( $this, 'foodbakery_removed_shortlist' ), 11 );
            // Handle AJAX to delete a restaurant alert.
            add_action( 'wp_ajax_foodbakery_remove_restaurant_alert', array( $this, 'remove_restaurant_alert' ) );
            add_action( 'wp_ajax_nopriv_foodbakery_remove_restaurant_alert', array( $this, 'remove_restaurant_alert' ) );
            add_action( 'wp_ajax_foodbakery_remove_team_member', array( $this, 'foodbakery_remove_team_member' ), 11 );
        }

        function foodbakery_meta_publishers_add() {
            add_meta_box( 'foodbakery_meta_publishers', foodbakery_plugin_text_srt( 'foodbakery_company_details' ), array( $this, 'foodbakery_meta_publishers' ), 'publishers', 'normal', 'high' );
        }

        /**
         * Start Function How to Attach mata box with publishers post type
         */
        function foodbakery_meta_publishers( $post ) {
            global $post, $foodbakery_post_type_publishers, $post_id;
            
            $post_id = $post->ID;
            $this->post_id = $post_id;
            $publisher_profile_type = get_post_meta( $post_id, 'foodbakery_publisher_profile_type', true );
            $display_tab = '';
            if ( $publisher_profile_type == 'restaurant' ) {
                $display_tab = 'block';
            } else {
                $display_tab = 'none';
            }
            ?>
            <div class="page-wrap page-opts left">
                <div class="option-sec" style="margin-bottom:0;">
                    <div class="opt-conts" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>"> 
                        <div class="elementhidden">
                          
                            <nav class="admin-navigtion">
                                <ul id="cs-options-tab">
                                    <li><a href="javascript:void(0);" name="#tab-general" href="javascript:;"><i class="icon-settings"></i><?php _e( 'Account Settings', 'foodbakery' ) ?> </a></li>
                                    <li><a href="javascript:void(0);" name="#tab-user-restaurants" href="javascript:;"><i class="icon-th-list"></i><?php _e( 'User Restaurants', 'foodbakery' ) ?> </a></li>
                                    <li><a href="javascript:void(0);" name="#tab-orders-inquiries" href="javascript:;"><i class="icon-cart2"></i> <?php _e( 'Orders/Inquiries', 'foodbakery' ) ?></a></li>
                                  
                                    <li><a href="javascript:void(0);" name="#tab-Searches22" href="javascript:;"><i class="icon-add_alert "></i> <?php _e( 'Searches & Alerts', 'foodbakery' ) ?></a></li>
									<?php
									if ( $publisher_profile_type == 'buyer' ) {
										?>
										<li><a href="javascript:void(0);" name="#tab-shortlists22" href="javascript:;"><i class="icon-favorite"></i> <?php _e( 'Shortlists', 'foodbakery' ) ?></a></li>
										<?php
									}
									?>
                                    <li><a href="javascript:void(0);" name="#tab-packages" href="javascript:;"><i class="icon-box"></i> <?php _e( 'Memberships', 'foodbakery' ) ?></a></li>
                                    <li style=" display: <?php echo esc_html( $display_tab ); ?>;" class="publisher_company_name"><a href="javascript:void(0);" name="#tab-team-members" href="javascript:;"><i class="icon-users2"></i> <?php _e( 'Team Members', 'foodbakery' ) ?></a></li>
                                </ul>
                            </nav>
                            <div id="tabbed-content" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
                                <div id="tab-general">
                                    <?php $this->foodbakery_publishers_options(); ?>
                                </div>
                                <div id="tab-user-restaurants">
                                    <?php $this->foodbakery_user_restaurant_options(); ?>
                                </div>
                                <div id="tab-orders-inquiries">
                                    <?php $this->foodbakery_order_inquiries(); ?>
                                </div>
                                <div id="tab-Searches22">
                                    <?php $this->foodbakery_restaurantalerts(); ?>
                                </div>
                                <div id="tab-shortlists22">
                                    <?php $this->foodbakery_shortlists(); ?>
                                </div>
                                <div id="tab-packages">
                                    <?php $this->foodbakery_tab_packages(); ?>
                                </div>
                                <div id="tab-team-members">
                                    <?php $this->foodbakery_publisher_members(); ?>
                                </div>
                              
                            </div>
                            <?php $foodbakery_post_type_publishers->foodbakery_submit_meta_box( 'publishers', $args = array() ); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <?php
        }

        /**
         * Start Function for user restaurants
         */
        public function foodbakery_user_restaurant_options() {
            global $post;
            $publisher_id = $post->ID;
            $args = array(
                'posts_per_page' => "-1",
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_restaurant_publisher',
                        'value' => $publisher_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_restaurant_expired',
                        'value' => strtotime( date( "d-m-Y" ) ),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'foodbakery_restaurant_status',
                        'value' => 'delete',
                        'compare' => '!=',
                    ),
                ),
            );
            $custom_query = new WP_Query( $args );

            $all_restaurants = $custom_query->posts;
            ?>
            <div id="foodbakery-dev-user-restaurant" class="user-list" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"> 
                <div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class = "element-title">
                        <h5><?php echo __( 'User restaurants', 'foodbakery' ); ?></h5>
                    </div>
                </div>
                <ul class="panel-group">
                    <?php
                    if ( isset( $all_restaurants ) && ! empty( $all_restaurants ) ) {
                        ?>
                        <li> <span><?php echo __( 'Restaurants', 'foodbakery' ); ?></span>
                            <span><?php echo __( 'Posted', 'foodbakery' ); ?></span>
                            <span><?php echo __( 'Expires' ); ?></span> </li><?php
                        foreach ( $all_restaurants as $restaurant_data ) {
                            global $post, $foodbakery_plugin_options;
                            $post = $restaurant_data;
                            setup_postdata( $restaurant_data );
                            $category = get_term_by( 'name', get_post_meta( get_the_ID(), 'foodbakery_restaurant_category', true )[0], 'restaurant-category' );
                            $restaurant_post_on = get_post_meta( get_the_ID(), 'foodbakery_restaurant_posted', true );
                            $restaurant_post_expiry = get_post_meta( get_the_ID(), 'foodbakery_restaurant_expired', true );
                            $restaurant_status = get_post_meta( get_the_ID(), 'foodbakery_restaurant_status', true );
                            $foodbakery_restaurant_update_url = get_edit_post_link( get_the_ID() );
                            ?>
                            <li id="user-restaurant-<?php echo absint( get_the_ID() ); ?>" class="alert" data-id="<?php echo esc_attr( get_the_ID() ); ?>">
                                <div class="panel panel-default">
                                    <a href="javascript:void(0);" data-id="<?php echo absint( get_the_ID() ); ?>" class="close-member foodbakery-dev-restaurant-delete"><i class="icon-close2"></i></a>
                                    <div class="panel-heading">
                                        <div class="img-holder">
                                            <?php if ( has_post_thumbnail() ) { ?>
                                                <figure>
                                                    <?php the_post_thumbnail( 'thumbnail' ); ?>
                                                </figure>
                                            <?php } ?>
                                            <strong><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></strong>
                                            <?php if ( isset( $category->name ) && $category->name != '' ) { ?>
                                                <span><?php echo esc_html( $category->name ); ?></span>
                                            <?php } ?>
                                        </div>
                                        <span class="post-date"><?php echo esc_html( $restaurant_post_on != '' ? date_i18n( get_option( 'date_format' ), $restaurant_post_on ) : '-'  ) ?></span>
                                        <?php
                                        if ( $restaurant_status == 'active' || $restaurant_status == 'awaiting-activation' ) {
                                            ?>
                                            <span class="expire-date"><?php echo esc_html( $restaurant_post_expiry != '' ? date_i18n( get_option( 'date_format' ), $restaurant_post_expiry ) : '-'  ) ?></span>
                                            <?php
                                        } else {
                                            ?>
                                            <span class="expire-date">-</span>
                                            <?php
                                        }
                                        ?>
                                        <span class="edit"><a href="<?php echo esc_url_raw( $foodbakery_restaurant_update_url ) ?>"><?php esc_html_e( 'Edit', 'foodbakery' ) ?></a></span>
                                    </div>
                                </div>
                            </li>
                            <?php
                        }
                        wp_reset_postdata();
                    } else {
                        ?>
                        <li class="no-restaurant-found">
                            <i class="icon-caution"></i>
                            <?php _e( 'No restaurant Found.', 'foodbakery' ) ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        }

        /**
         * Start Function Orders/Inquiries
         */
        public function foodbakery_order_inquiries() {
            global $post, $post_id;
           
            $args = array(
                'post_type' => 'orders_inquiries',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'foodbakery_publisher_id',
                        'value' => $post_id,
                        'compare' => '=',
                    )
                ),
            );

            $order_query = new WP_Query( $args );
            ?>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="user-orders-list">
                        <div class="element-title">
                            <h4><?php _e( 'Recent Orders/Inquiries', 'foodbakery' ); ?></h4>
                        </div>
                        <ul class="orders-list" id="portfolio">
                            <?php if ( $order_query->have_posts() ) : ?>
                                <li class="all pending complete">
                                    <div class="orders-number"><strong><?php _e( 'Order', 'foodbakery' ); ?></strong></div>
                                    <div class="orders-title"><strong><?php _e( 'Title', 'foodbakery' ); ?></strong></div>
                                    <div class="orders-date"><strong><?php _e( 'Date', 'foodbakery' ); ?></strong></div>
                                    <div class="orders-status"><strong><?php _e( 'Type', 'foodbakery' ); ?></strong></div>
                                    <div class="orders-price"><strong><?php _e( 'Total', 'foodbakery' ); ?></strong></div>
                                    <div class="orders-detail"><strong><?php _e( 'Action', 'foodbakery' ); ?></strong></div>
                                </li>
                                <?php
                                while ( $order_query->have_posts() ) : $order_query->the_post();

                                    $order_restaurant_id = get_post_meta( get_the_ID(), 'foodbakery_restaurant_id', true );
                                    $order_number = get_post_meta( get_the_ID(), 'foodbakery_order_number', true );
                                    $order_type = get_post_meta( get_the_ID(), 'foodbakery_order_type', true );
                                    $order_date = get_post_meta( get_the_ID(), 'foodbakery_order_date', true );
                                    $order_price = get_post_meta( get_the_ID(), 'foodbakery_order_price', true );
                                    $order_form_fields = get_post_meta( get_the_ID(), 'foodbakery_order_form_fields', true );
                                    $quantity = '';
                                    if ( ! empty( $order_form_fields ) ) {
                                        foreach ( $order_form_fields as $order_form_field ) {
                                            $field_type = isset( $order_form_field['type'] ) ? $order_form_field['type'] : '';
                                            if ( $field_type == 'quantity' ) {
                                                $meta_key = isset( $order_form_field['meta_key'] ) ? $order_form_field['meta_key'] : '';
                                                $quantity = get_post_meta( get_the_ID(), $meta_key, true );
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <li class="all pending">
                                        <div class="orders-number">
                                            <span class="pending">#<?php echo get_the_ID(); ?></span>
                                        </div>
                                        <div class="orders-title">
                                            <h6 class="order-title"><?php echo wp_trim_words( get_the_title(), 4, '...' );
                                    ?></h6>
                                        </div>
                                        <div class="orders-date">
                                            <span><?php echo get_the_date( 'M, d Y' ); ?></span>
                                        </div>
                                        <div class="orders-status">
                                            <span class="pending"><?php echo esc_html( $order_type ); ?></span>
                                        </div>
                                        <div class="orders-price">
                                            <?php
                                            if ( $quantity > 1 ) {
                                                $items = sprintf( ' for %s items', $quantity );
                                            } else {
                                                $items = sprintf( ' for %s item', $quantity );
                                            }
                                            ?>
                                            <span><?php echo esc_html( $order_price ); ?><?php echo esc_html( $items ); ?></span>
                                        </div>
                                        <div class="orders-detail">
                                            <a href="<?php echo get_edit_post_link(get_the_ID()); ?>"><?php _e( 'View', 'foodbakery' ); ?></a>
                                        </div>
                                    </li>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                            else:
                                _e( 'You don\'t have any order/inquiry', 'foodbakery' );
                            endif;
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <?php
        }

        /**
         * Start Function packages
         */
        public function purchase_package_info_field_show( $value = '', $label = '', $value_plus = '' ) {

            if ( $value != '' && $value != 'on' ) {
                $html = '<li><label>' . $label . '</label><span>' . $value . ' ' . $value_plus . '</span></li>';
            } else if ( $value != '' && $value == 'on' ) {
                $html = '<li><label>' . $label . '</label><span><i class="icon-check"></i></span></li>';
            } else {
                $html = '<li><label>' . $label . '</label><span><i class="icon-minus"></i></span></li>';
            }

            return $html;
        }

        public function foodbakery_tab_packages() {
            global $post, $post_id, $foodbakery_plugin_options;
            $foodbakery_current_date = strtotime( date( 'd-m-Y' ) );
            $foodbakery_currency_sign = foodbakery_get_currency_sign();
            $args = array(
                'posts_per_page' => "-1",
                'post_type' => 'package-orders',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_transaction_user',
                        'value' => $post_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_transaction_expiry_date',
                        'value' => $foodbakery_current_date,
                        'compare' => '>',
                    ),
                    array(
                        'key' => 'foodbakery_transaction_status',
                        'value' => 'approved',
                        'compare' => '=',
                    ),
                ),
            );

            $pkg_query = new WP_Query( $args );
            ?>
            <div class="user-packages">
                <div class="element-title">
                    <h4><?php echo _e( 'Memberships', 'foodbakery' ); ?></h4>
                </div>
            </div>
            <div class="user-packages-list">
                <?php if ( isset( $pkg_query ) && $pkg_query != '' && $pkg_query->have_posts() ) : ?>
                    <div class="all-pckgs-sec">
                        <?php
                        while ( $pkg_query->have_posts() ) : $pkg_query->the_post();
                            $transaction_package = get_post_meta( get_the_ID(), 'foodbakery_transaction_package', true );
                            $transaction_expiry_date = get_post_meta( get_the_ID(), 'foodbakery_transaction_expiry_date', true );
                            $transaction_restaurants = get_post_meta( get_the_ID(), 'foodbakery_transaction_restaurants', true );
                            $transaction_feature_list = get_post_meta( get_the_ID(), 'foodbakery_transaction_restaurant_feature_list', true );
                            $transaction_top_cat_list = get_post_meta( get_the_ID(), 'foodbakery_transaction_restaurant_top_cat_list', true );

                            $package_id = get_the_ID();
                            $transaction_restaurants = isset( $transaction_restaurants ) ? $transaction_restaurants : 0;
                            $transaction_feature_list = isset( $transaction_feature_list ) ? $transaction_feature_list : 0;
                            $transaction_top_cat_list = isset( $transaction_top_cat_list ) ? $transaction_top_cat_list : 0;

                            $package_price = get_post_meta( $package_id, 'foodbakery_transaction_amount', true );

                            $html = '';
                            ?>
                            <div class="foodbakery-pkg-holder">
                                <div class="foodbakery-pkg-header">
                                    <div class="pkg-title-price pull-left">
                                        <label class="pkg-title"><?php echo get_the_title( $transaction_package ); ?></label>
                                        <span class="pkg-price"><?php printf( __( 'Price: %s', 'foodbakery' ), $foodbakery_currency_sign . FOODBAKERY_FUNCTIONS()->num_format( $package_price ) ) ?></span>
                                    </div>
                                    <div class="pkg-detail-btn pull-right">
                                        <a data-id="<?php echo absint( $package_id ) ?>" class="foodbakery-dev-dash-detail-pkg" href="javascript:void(0);"><?php _e( 'Detail', 'foodbakery' ) ?></a>
                                    </div>
                                </div>
                                <div class="package-info-sec restaurant-info-sec" style="display:none;" id="package-detail-<?php echo absint( $package_id ) ?>">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <ul class="restaurant-pkg-points">
                                                <?php
                                                $trans_packg_expiry = get_post_meta( $package_id, 'foodbakery_transaction_expiry_date', true );
                                                $trans_packg_list_num = get_post_meta( $package_id, 'foodbakery_transaction_restaurants', true );
                                                $trans_packg_list_expire = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_expiry', true );
                                                $foodbakery_restaurant_ids = get_post_meta( $package_id, 'foodbakery_restaurant_ids', true );

                                                if ( empty( $foodbakery_restaurant_ids ) ) {
                                                    $foodbakery_restaurant_used = 0;
                                                } else {
                                                    $foodbakery_restaurant_used = absint( sizeof( $foodbakery_restaurant_ids ) );
                                                }

                                                $foodbakery_restaurant_remain = '0';
                                                if ( (int) $trans_packg_list_num > (int) $foodbakery_restaurant_used ) {
                                                    $foodbakery_restaurant_remain = (int) $trans_packg_list_num - (int) $foodbakery_restaurant_used;
                                                }

                                                $trans_featured_num = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_feature_list', true );
                                                $foodbakery_featured_ids = get_post_meta( $package_id, 'foodbakery_featured_ids', true );
                                                if ( empty( $foodbakery_featured_ids ) ) {
                                                    $foodbakery_featured_used = 0;
                                                } else {
                                                    $foodbakery_featured_used = absint( sizeof( $foodbakery_featured_ids ) );
                                                }
                                                $foodbakery_featured_remain = '0';
                                                if ( (int) $trans_featured_num > (int) $foodbakery_featured_used ) {
                                                    $foodbakery_featured_remain = (int) $trans_featured_num - (int) $foodbakery_featured_used;
                                                }

                                                $trans_top_cat_num = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_top_cat_list', true );
                                                $foodbakery_top_cat_ids = get_post_meta( $package_id, 'foodbakery_top_cat_ids', true );

                                                if ( empty( $foodbakery_top_cat_ids ) ) {
                                                    $foodbakery_top_cat_used = 0;
                                                } else {
                                                    $foodbakery_top_cat_used = absint( sizeof( $foodbakery_top_cat_ids ) );
                                                }

                                                $foodbakery_top_cat_remain = '0';
                                                if ( (int) $trans_top_cat_num > (int) $foodbakery_top_cat_used ) {
                                                    $foodbakery_top_cat_remain = (int) $trans_top_cat_num - (int) $foodbakery_top_cat_used;
                                                }

                                               
                                                $trans_pics_num = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_pic_num', true );
                                               
                                                $trans_tags_num = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_tags_num', true );
                                                $trans_reviews = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_reviews', true );

                                                $trans_phone = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_phone', true );
                                                $trans_website = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_website', true );
                                                $trans_social = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_social', true );
                                                $trans_ror = get_post_meta( $package_id, 'foodbakery_transaction_restaurant_ror', true );
                                                $trans_dynamic_f = get_post_meta( $package_id, 'foodbakery_transaction_dynamic', true );

                                                $pkg_expire_date = date_i18n( get_option( 'date_format' ), $trans_packg_expiry );

                                                $html .= $this->purchase_package_info_field_show( $pkg_expire_date, __( 'Expiry Date', 'foodbakery' ) );
                                                $html .= $this->purchase_package_info_field_show( $trans_packg_list_num, __( 'Membership Duration', 'foodbakery' ), __( 'Days', 'foodbakery' ) );
                                                $html .= '<li><label>' . __( 'Restaurants', 'foodbakery' ) . '</label><span>' . absint( $foodbakery_restaurant_used ) . '/' . absint( $trans_packg_list_num ) . '</span></li>';
                                                $html .= $this->purchase_package_info_field_show( $trans_packg_list_expire, __( 'Restaurants Duration', 'foodbakery' ), __( 'Days', 'foodbakery' ) );
                                                if ( absint( $trans_featured_num ) > 0 ) {
                                                    $html .= '<li><label>' . __( 'Featured Restaurants', 'foodbakery' ) . '</label><span>' . absint( $foodbakery_featured_used ) . '/' . absint( $trans_featured_num ) . '</span></li>';
                                                } else {
                                                    $html .= '<li><label>' . __( 'Featured Restaurants', 'foodbakery' ) . '</label><span>0</span></li>';
                                                }
                                                if ( absint( $trans_top_cat_num ) > 0 ) {
                                                    $html .= '<li><label>' . __( 'Top Category Restaurants', 'foodbakery' ) . '</label><span>' . absint( $foodbakery_top_cat_used ) . '/' . absint( $trans_top_cat_num ) . '</span></li>';
                                                } else {
                                                    $html .= '<li><label>' . __( 'Top Category Restaurants', 'foodbakery' ) . '</label><span>0</span></li>';
                                                }

                                               
                                                $html .= $this->purchase_package_info_field_show( $trans_tags_num, __( 'Number of Tags', 'foodbakery' ) );
                                                $html .= $this->purchase_package_info_field_show( $trans_reviews, __( 'Reviews', 'foodbakery' ) );
                                               
                                                $html .= $this->purchase_package_info_field_show( $trans_phone, __( 'Phone Number', 'foodbakery' ) );
                                                $html .= $this->purchase_package_info_field_show( $trans_website, __( 'Website Link', 'foodbakery' ) );
                                                $html .= $this->purchase_package_info_field_show( $trans_social, __( 'Social Impressions Reach', 'foodbakery' ) );
                                                $html .= $this->purchase_package_info_field_show( $trans_ror, __( 'Respond to Reviews', 'foodbakery' ) );

                                                $dyn_fields_html = '';
                                                if ( is_array( $trans_dynamic_f ) && sizeof( $trans_dynamic_f ) > 0 ) {
                                                    foreach ( $trans_dynamic_f as $trans_dynamic ) {
                                                        if ( isset( $trans_dynamic['field_type'] ) && isset( $trans_dynamic['field_label'] ) && isset( $trans_dynamic['field_value'] ) ) {
                                                            $d_type = $trans_dynamic['field_type'];
                                                            $d_label = $trans_dynamic['field_label'];
                                                            $d_value = $trans_dynamic['field_value'];

                                                            if ( $d_value == 'on' && $d_type == 'single-choice' ) {
                                                                $html .= '<li><label>' . $d_label . '</label><span><i class="icon-check"></i></span></li>';
                                                            } else if ( $d_value != '' && $d_type != 'single-choice' ) {
                                                                $html .= '<li><label>' . $d_label . '</label><span>' . $d_value . '</span></li>';
                                                            } else {
                                                                $html .= '<li><label>' . $d_label . '</label><span><i class="icon-minus"></i></span></li>';
                                                            }
                                                        }
                                                    }
                                                    // end foreach
                                                }
                                                // emd of Dynamic fields
                                                // other Features
                                                echo force_balance_tags( $html );
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        ?>

                    </div>
                    <?php
                else:
                    _e( 'Sorry! there is no package in your list..', 'foodbakery' );
                endif;
                ?>
            </div>
            <?php
        }

        /**
         * Publisher Removed Shortlist
         * @ removed publisher shortlists based on restaurant id
         */
        public function foodbakery_removed_shortlist() {
            global $post_id;
            $restaurant_id = foodbakery_get_input( 'restaurant_id' );
            $post_id = foodbakery_get_input( 'post_id' );
            $current_user = wp_get_current_user();
            //$publisher_id = get_current_user_id();
            $response = array();
            $response['status'] = false;

            if ( '' != $restaurant_id ) {
            
                $publisher_shortlists = get_post_meta( $post_id, 'foodbakery_shortlists', true );
                foreach ( $publisher_shortlists as $key => $sub_array ) {
                    if ( $sub_array['restaurant_id'] == $restaurant_id ) {
                        unset( $publisher_shortlists[$key] );
                        $response['status'] = true;
                    }
                }
                if ( ! empty( $publisher_shortlists ) ) {
                    $publisher_shortlists = array_values( $publisher_shortlists );
                }
                update_post_meta( $post_id, 'foodbakery_shortlists', $publisher_shortlists );
                $notification_array = array(
                    'type' => 'restaurant',
                    'element_id' => $restaurant_id,
                    'msg' => __( $current_user->user_login . ' one of your restaurant removed from shortlists.', 'foodbakery' ),
                );
                do_action( 'foodbakery_add_notification', $notification_array );
            }
            echo json_encode( $response );
            wp_die();
        }

        /**
         * Start Function Shortlist
         */
        public function foodbakery_shortlists( $post_type = '' ) {
            global $post, $post_id;
            $shortlist_query = '';
            // Post Type.
            if ( ! isset( $post_type ) || $post_type == '' ) {
                $post_type = 'restaurants';
            }
          

            $shortlists = get_post_meta( $post_id, 'foodbakery_shortlists', true );
            
            $all_restaurants = array();
            if ( isset( $shortlists ) && ! empty( $shortlists ) ) {
                $restaurant_ids = array();
				
                foreach ( $shortlists as $shortlist_data ) {
                    $restaurant_ids[] = isset($shortlist_data['listing_id']) ? $shortlist_data['listing_id'] : '';
                }
                $args = array(
                    'post_type' => $post_type,
                    'post__in' => $restaurant_ids,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'foodbakery_restaurant_expired',
                            'value' => strtotime( date( "d-m-Y" ) ),
                            'compare' => '>=',
                        ),
                        array(
                            'key' => 'foodbakery_restaurant_status',
                            'value' => 'active',
                            'compare' => '=',
                        ),
                    ),
                );
                $shortlist_query = new WP_Query( $args );
                ?>
                <div class="user-shortlist-list">
                    <div class="element-title">
                        <h4><?php _e( 'Shortlists', 'foodbakery' ); ?></h4>
                    </div>
                    <ul class="shortlists-list">
                        <script>
                            /*
                             * 
                             * Foodbakery Publisher Removed Shortlist function
                             */
                            jQuery(document).on("click", ".delete-shortlist", function () {
                                var thisObj = jQuery(this);
                                var restaurant_id = thisObj.data('id');
                                var post_id = thisObj.data('post');
                                var delete_icon_class = thisObj.find("i").attr('class');
                                var loader_class = 'icon-spinner icon-spin';
                                var dataString = 'post_id=' + post_id + '&restaurant_id=' + restaurant_id + '&action=foodbakery_removed_shortlist_backend';
                                jQuery('#id_confrmdiv').show();
                                jQuery.ajax({
                                    type: "POST",
                                    url: foodbakery_globals.ajax_url,
                                    data: dataString,
                                    dataType: "json",
                                    success: function (response) {
                                        thisObj.find('i').removeClass(loader_class).addClass(delete_icon_class);
                                        if (response.status == true) {
                                                            
                                            thisObj.closest('li').hide('slow', function () {
                                                thisObj.closest('li').remove();
                                            });
                                                            
                                            var msg_obj = {msg: foodbakery_shortlists.deleted_shortlist, type: 'success'};
                                                            
                                            foodbakery_show_response(msg_obj);
                                        }
                                    }
                                });
                                jQuery('#id_falsebtn').click(function () {
                                    jQuery('#id_confrmdiv').hide();
                                    return false;
                                });
                                return false;
                            });
                        </script>
                        <?php
                        if ( $shortlist_query != '' && $shortlist_query->have_posts() ) :
                            while ( $shortlist_query->have_posts() ) : $shortlist_query->the_post();
                                $publisher_category = get_post_meta( get_the_ID(), 'foodbakery_restaurant_category', true );
                                ?>
                                <li>

                                    <div class="suggest-list-holder">
                                        <?php if ( has_post_thumbnail() ) { ?>
                                            <div class="img-holder">
                                                <figure>
                                                    <?php the_post_thumbnail( 'thumbnail' ); ?>
                                                </figure>
                                            </div>
                                        <?php } ?>
                                        <div class="text-holder">
                                            <h6><a href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>"><?php echo get_the_title(); ?></a></h6>
                                            <?php
                                            if ( is_array( $publisher_category ) ) {
                                                foreach ( $publisher_category as $cate_slug => $cat_val ) {
                                                    $category = get_term_by( 'slug', $cat_val, 'restaurant-category' );
                                                }
                                            }
                                            if ( isset( $category->name ) && $category->name != '' ) {
                                                ?>
                                                <span><?php echo esc_html( $category->name ); ?></span>
                                            <?php }
                                            ?>
                                            <a href="javascript:void(0);" class="short-icon delete-shortlist" data-id="<?php echo intval( get_the_ID() ); ?>" data-post="<?php echo esc_attr( $post_id ); ?>"><i class="icon-close2"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            endwhile;
                        else:
                            ?><li class="no-shortlists-found"><i class="icon-shortlist"></i><?php
                            _e( 'You don\'t have any shortlists', 'foodbakery' );
                            ?></li><?php
                        endif;
                        ?>
                    </ul>
                </div>
                <?php
                wp_reset_postdata();
            } else {
                ?>
                <div class="user-shortlist-list">
                    <div class="element-title">
                        <h4><?php _e( 'Shortlists', 'foodbakery' ); ?></h4>
                    </div>
                    <ul class="shortlists-list">
                        <?php
                        if ( $shortlist_query != '' && $shortlist_query->have_posts() ) :

                            echo force_balance_tags( $this->render_list_item_view( $shortlist_query ) );
                        else:
                            ?><li class="no-shortlists-found"><i class="icon-shortlist"></i><?php
                            _e( 'You don\'t have any shortlists', 'foodbakery' );
                            ?></li><?php
                        endif;
                        ?>
                    </ul>
                </div>
                <?php
            }
        }

        public function remove_restaurant_alert() {
            $status = 0;
            $msg = '';
            if ( isset( $_POST['post_id'] ) ) {
                wp_delete_post( $_POST['post_id'] );
                $status = 1;
                $msg = __( "Restaurant Alert Successfully deleted", FOODBAKERY_NOTIFICATIONS_PLUGIN_DOMAIN );
            } else {
                $msg = __( "Provided data incomplete", FOODBAKERY_NOTIFICATIONS_PLUGIN_DOMAIN );
                $status = 0;
            }
            echo json_encode( array( "msg" => $msg, 'status' => $status ) );
            wp_die();
        }

        /**
         * Start Function Search Alerts
         */
        public function foodbakery_restaurantalerts() {
            global $post, $foodbakery_form_fields2;
            $foodbakery_blog_num_post = 10;

            $uid = empty( $_POST['foodbakery_uid'] ) ? '' : sanitize_text_field( $_POST['foodbakery_uid'] );
            $uid = '111';
            if ( $uid <> '' ) {
                $user_id = foodbakery_get_user_id();
                if ( ! empty( $user_id ) ) {
                    // Get count of total posts
                    $args = array(
                        'author' => $user_id, // I could also use $user_ID, right?
                        'post_type' => 'restaurant-alert',
                        'posts_per_page' => -1,
                        'orderby' => 'post_date',
                        'order' => 'DESC',
                    );
                    $restaurant_alerts = new WP_Query( $args );
                    $alerts_count = $restaurant_alerts->post_count;

                    $page_num = empty( $_POST['page_id_all'] ) ? 1 : sanitize_text_field( $_POST['page_id_all'] );
                    // Get alerts with respect to pagination.
                    $args = array(
                        'author' => $user_id, // I could also use $user_ID, right?
                        'post_type' => 'restaurant-alert',
                        'posts_per_page' => $foodbakery_blog_num_post,
                        'paged' => $page_num,
                        'orderby' => 'post_date',
                        'order' => 'DESC',
                    );
                    $restaurant_alerts = new WP_Query( $args );
                }
                ?>
                <div class="cs-loader"></div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <section class="cs-favorite-restaurants">
                            <div class="element-title">
                                <h4><?php _e( 'Restaurant Alerts', 'foodbakery' ); ?></h4>
                            </div>
                            <?php
                            $foodbakery_plugin_options = get_option( 'foodbakery_plugin_options' );
                            $search_list_page = '';
                            if ( ! empty( $foodbakery_plugin_options ) && $foodbakery_plugin_options['foodbakery_search_result_page'] ) {
                                $search_list_page = $foodbakery_plugin_options['foodbakery_search_result_page'];
                            }
                            if ( ! empty( $restaurant_alerts ) && $restaurant_alerts->have_posts() ) {
                                ?>
                                <ul class="top-heading-list">
                                    <li><span><?php _e( 'Alert Details', 'foodbakery' ); ?></span></li>
                                    <li><span><?php _e( 'Email Frequency', 'foodbakery' ); ?></span></li>
                                </ul>
                                <ul class="feature-restaurants">
                                    <?php
                                    while ( $restaurant_alerts->have_posts() ) :
                                        $restaurant_alerts->the_post();

                                        $foodbakery_restaurant_expired = get_post_meta( $post->ID, 'foodbakery_restaurant_expired', true ) . '<br>';
                                        $foodbakery_org_name = get_post_meta( $post->ID, 'foodbakery_org_name', true );
                                        // Get restaurant's Meta Data.
                                      
                                        $foodbakery_name = get_post_meta( $post->ID, 'foodbakery_name', true );
                                        $foodbakery_query = get_post_meta( $post->ID, 'foodbakery_query', true );
                                        // Get selected frequencies.
                                        $frequencies = array(
                                            'annually',
                                            'biannually',
                                            'monthly',
                                            'fortnightly',
                                            'weekly',
                                            'daily',
                                            'never',
                                        );
                                        $selected_frequencies = array();
                                        foreach ( $frequencies as $key => $frequency ) {
                                            $frequency_val = get_post_meta( $post->ID, 'foodbakery_frequency_' . $frequency, true );
                                            if ( ! empty( $frequency_val ) && $frequency_val == 'on' ) {
                                                $selected_frequencies[] = $frequency;
                                            }
                                        }
                                        ?>
                                        <script>
                                                                   
                                            (function ($) {
                                                $(function () {
                                                    $(".delete-restaurant-alert a").click(function () {
                                                        var post_id = $(this).data("post-id");
                                                        $('#id_confrmdiv').show();
                                                        var dataString = 'post_id=' + post_id + '&action=foodbakery_remove_restaurant_alert';
                                                        jQuery('.holder-' + post_id).find('#remove_resume_link' + post_id).html('<i class="icon-spinner icon-spin"></i>');
                                                        jQuery.ajax({
                                                            type: "POST",
                                                            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                                                            data: dataString,
                                                            dataType: "JSON",
                                                            success: function (response) {
                                                                if (response.status == 0) {
                                                                    show_alert_msg(response.msg);
                                                                } else {
                                                                    jQuery('.holder-' + post_id).remove();
                                                                    var msg_obj = {msg: 'Deleted Successfully.', type: 'success'};
                                                                    foodbakery_show_response(msg_obj);
                                                                }
                                                            }
                                                        });
                                                        $('#id_confrmdiv').hide();
                                                        return false;
                                                        $('#id_falsebtn').click(function () {
                                                            $('#id_confrmdiv').hide();
                                                            return false;
                                                        });
                                                        return false;
                                                    });
                                                });
                                            })(jQuery);
                                        </script>
                                        <li class="holder-<?php echo intval( $post->ID ); ?>">
                                            <div class="company-detail-inner">
                                                <h6><a href="<?php echo esc_url( get_permalink( $search_list_page ) ) . '?' . http_build_query( $search_keywords ); ?>"><?php echo esc_html( $foodbakery_name ); ?></a></h6><br>
                                                <b><?php _e( 'Search Keywords:', 'foodbakery' ); ?> </b><?php echo implode( ', ', array_values( $search_keywords ) ); ?><br>
                                            </div>

                                            <div class="company-date-option">
                                                <?php echo implode( ', ', array_map( 'ucfirst', $selected_frequencies ) ); ?>
                                                <div class="control delete-restaurant-alert">
                                                    <a data-toggle="tooltip" data-placement="top" title="<?php _e( 'Remove', 'foodbakery' ); ?>" id="remove_resume_link<?php echo absint( $post->ID ); ?>" href="#"  class="delete-restaurant delete" data-post-id="<?php echo absint( $post->ID ); ?>">
                                                        <i class="icon-trash-o"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    endwhile;
                                    wp_reset_postdata();
                                    ?>
                                </ul>
                                <?php
                                //==Pagination Start
                                if ( $alerts_count > $foodbakery_blog_num_post && $foodbakery_blog_num_post > 0 ) {
                                    echo '<nav>';
                                    echo foodbakery_ajax_pagination( $alerts_count, $foodbakery_blog_num_post, 'restaurant-alerts', 'publisher', $uid, '' );
                                    echo '</nav>';
                                }//==Pagination End 
                                ?>
                                <?php
                            } else {
                                echo '<div class="cs-no-record">' . foodbakery_info_messages_restaurant( __( "You did not have any restaurant alerts.", 'foodbakery' ) ) . '</div>';
                            }
                            ?>
                        </section>
                    </div>
                </div>
                <?php
            } else {
                echo '<div class="no-result"><h1>' . __( 'Please create user profile.', 'foodbakery' ) . '</h1></div>';
            }
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('[data-toggle="tooltip"]').tooltip();
                });
            <?php
            if ( class_exists( 'WP_Restaurant_Hunt_Alert_Helpers' ) ) {
               echo WP_Restaurant_Hunt_Alert_Helpers::get_script_str();
            }
            ?>
            </script>
            <?php
        }

        /**
         * Publisher Publisher Form
         */
        /*
         * Updating Team Member
         */

        public function foodbakery_update_team_member() {
            $user_ID = foodbakery_get_input( 'foodbakery_user_id', NULL, 'INT' );

            $foodbakery_user_type = foodbakery_get_input( 'foodbakery_user_type', NULL, 'STRING' );
            $foodbakery_old_user_type = foodbakery_get_input( 'foodbakery_old_user_type', NULL, 'STRING' );
            $count_supper_admin = foodbakery_get_input( 'count_supper_admin', NULL, 'STRING' );
            $update_allow = 1;
            if ( $foodbakery_old_user_type == $foodbakery_user_type ) {
               
                $update_allow = 1;
            } elseif ( 'supper-admin' == $foodbakery_user_type ) {
                
                $update_allow = 1;
            } elseif ( $count_supper_admin > 1 ) {
               
                $update_allow = 1;
            } else {
                
                $update_allow = 0;
            }
            
            if ( $update_allow == 1 ) {
                $permissions = foodbakery_get_input( 'permissions', '', 'ARRAY' );

                update_user_meta( $user_ID, 'foodbakery_user_type', $foodbakery_user_type );
                update_user_meta( $user_ID, 'foodbakery_permissions', $permissions );

                $response_array = array(
                    'type' => 'success',
                    'msg' => __( 'Team member successfully updated!', 'foodbakery' ),
                );
            } else {
                $response_array = array(
                    'type' => 'error',
                    'msg' => __( 'Atleast one supper admin required for a restaurant', 'foodbakery' ),
                );
            }
            echo json_encode( $response_array );
        }

        public function foodbakery_remove_team_member() {

            $count_supper_admin = foodbakery_get_input( 'count_supper_admin', NULL, 'STRING' );
           
            echo esc_html( $count_supper_admin );
            if ( $count_supper_admin > 1 ) {
                
                $response_array = array(
                    'type' => 'success',
                    'msg' => __( 'Team Member Successfully Removed', 'foodbakery' ),
                );
            } else {
                $response_array = array(
                    'type' => 'error',
                    'msg' => __( 'Atleast one supper admin required for a restaurant', 'foodbakery' ),
                );
            }
            echo json_encode( $response_array );
            wp_die();
        }

        public function foodbakery_publisher_members() {
            global $foodbakery_html_fields_frontend, $post, $foodbakery_form_fields_frontend, $post_id;
            $company_data = get_post( $post_id );
      
            setup_postdata( $company_data );
            ?>
            <div class="team-list-holder">
                <div class = "element-title">
                    <h4><?php echo __( 'Team Members', 'foodbakery' ); ?></h4>
                </div>
		<div class="responsive-table">
                <div class="team-list">
                    <?php
                    $team_args = array(
                        'role' => 'foodbakery_publisher',
                        'meta_query' => array(
                            array(
                                'key' => 'foodbakery_company',
                                'value' => $company_data->ID,
                                'compare' => '='
                            ),
                            array(
                                'key' => 'foodbakery_user_status',
                                'value' => 'deleted',
                                'compare' => '!='
                            )
                        ),
                           
                    );
                    $team_members = get_users( $team_args );
                    if ( isset( $team_members ) && ! empty( $team_members ) ) {
                        ?>
                        <ul class = "panel-group" id = "accordion">
                            <li> 
                                <span><?php echo __( 'Username', 'foodbakery' ); ?></span>
                                <span><?php echo __( 'Email Address', 'foodbakery' ); ?></span> 
                            </li>
                            <?php
                            // count the supper admin in complete team
                            $supper_admin_count = 0;
                            foreach ( $team_members as $member_data ) {
                                $selected_user_type = get_user_meta( $member_data->ID, 'foodbakery_user_type', true );
                                if ( $selected_user_type == 'supper-admin' ) {
                                    $supper_admin_count ++;
                                }
                            }
                            foreach ( $team_members as $member_data ) {
                                $selected_user_type = get_user_meta( $member_data->ID, 'foodbakery_user_type', true );
                                $selected_user_type = isset( $selected_user_type ) && $selected_user_type != '' ? $selected_user_type : 'team-member';
                                $member_permissions = get_user_meta( $member_data->ID, 'foodbakery_permissions', true );
                                ?>
                                <li>
                                    <form name="foodbakery_update_team_member" id="foodbakery_update_team_member<?php echo esc_attr( $member_data->ID ); ?>" data-id="<?php echo esc_attr( $member_data->ID ); ?>" method="POST">
                                        <?php
                                        // TOTAL SUPPER ADMIN COUNT
                                        $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                                                array(
                                                    'cust_name' => 'count_supper_admin',
                                                    'std' => $supper_admin_count,
                                                )
                                        );
                                        $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                                                array(
                                                    'cust_name' => 'foodbakery_old_user_type',
                                                    'std' => $selected_user_type,
                                                )
                                        );
                                        ?>
                                        <script>
                                            jQuery(document).on('click', '.remove_member', function () {
                                                var user_id = jQuery(this).closest('form').data('id');
                                                foodbakery_show_loader();
                                                var serializedValues = jQuery("#foodbakery_update_team_member" + user_id).serialize();
                                                jQuery.ajax({
                                                type: 'POST',
                                                        dataType: 'json',
                                                        url: foodbakery_globals.ajax_url,
                                                        data: serializedValues + '&foodbakery_user_id=' + user_id + '&action=foodbakery_remove_team_member',
                                                        success: function (response) {
                                                            if (response.type == 'success') {
                                                               
                                                                thisObj.closest('form').fadeOut('slow');
                                                                                    
                                                            }
                                                            foodbakery_show_response(response);
                                                                                
                                                                                
                                                        });
                                            });
                                                                
                                            
                                        </script>
                                        <div class = "panel panel-default"> <a href="javascript:;" class="close-member"><i class="icon-close2 remove_member"></i></a>
                                            <div class = "panel-heading"> 
                                                <a data-toggle = "collapse" data-parent = "#accordion" href = "#collapse<?php echo esc_attr( $member_data->ID ); ?>" class = "collapsed">
                                                    <div class = "img-holder">
                                                        <strong><?php echo esc_html( $member_data->user_login ); ?> </strong> 
                                                    </div>
                                                    <span class="email"><?php echo esc_html( $member_data->user_email ); ?> </span> 
                    <?php if ( $selected_user_type == 'supper-admin' ) { ?><span class="supper-admin"><?php echo esc_html__( 'Supper Admin', 'foodbakery' ); ?></span>
                    <?php } ?>
                                                </a>
                                            </div>
                                        </div>

                                        <div id = "collapse<?php echo esc_attr( $member_data->ID ); ?>" class = "panel-collapse collapse">
                                            <div class = "panel-body">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class = "field-holder">
                                                        <label><?php echo __( 'Email Address', 'foodbakery' ); ?></label>
                                                        <?php
                                                        $foodbakery_opt_array = array(
                                                            'name' => __( 'Email Address', 'foodbakery' ),
                                                            'desc' => '',
                                                            'echo' => true,
                                                            'field_params' => array(
                                                                'std' => esc_html( $member_data->user_email ),
                                                                'id' => 'email_address',
                                                            ),
                                                        );
                                                        $foodbakery_html_fields_frontend->foodbakery_form_text_render( $foodbakery_opt_array );
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class = "field-holder">
                                                        <label><?php echo esc_html__( 'User Type', 'foodbakery' ); ?></label>

                                                        <?php
                                                        $user_type = array(
                                                            'supper-admin' => esc_html__('Supper Admin','foodbakery'),
                                                            'team-member' => esc_html__('Team Member','foodbakery'),
                                                        );
                                                        
                                                        $foodbakery_opt_array = array(
                                                            'name' => __( 'User Type', 'foodbakery' ),
                                                            'desc' => '',
                                                            'echo' => true,
                                                            'field_params' => array(
                                                                'std' => $selected_user_type,
                                                                'id' => 'user_type',
                                                                'classes' => 'chosen-select-no-single',
                                                                'options' => $user_type,
                                                                'extra_atr' => 'onchange="foodbakery_user_permission(this, \'add_member_permission' . esc_attr( $member_data->ID ) . '\', \'supper-admin\');"'
                                                            ),
                                                        );
                                                        $foodbakery_html_fields_frontend->foodbakery_form_select_render( $foodbakery_opt_array );
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $permission_display = '';
                                                if ( $selected_user_type == 'supper-admin' ) {
                                                    $permission_display = 'display:none';
                                                }
                                                ?>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 add_member_permission<?php echo esc_attr( $member_data->ID ); ?>" style="<?php echo esc_html( $permission_display ); ?>">
                                                    <h6 ><?php echo __( 'Roles & Permission', 'foodbakery' ); ?></h6>
                                                    <?php
                                                    global $permissions;
                                                    $permissions_array = $permissions->member_permissions();
                                                    ?>
                                                    <ul class = "checkbox-list">
                                                        <?php
                                                        foreach ( $permissions_array as $permission_key => $permission_value ) {
                                                            $value = '';
                                                            if ( isset( $member_permissions[$permission_key] ) && $member_permissions[$permission_key] == 'on' ) {
                                                                $value = $member_permissions[$permission_key];
                                                            } else if ( $selected_user_type == 'supper-admin' ) {  // if user supper admin then show all permission
                                                                $value = 'on';
                                                            }
                                                            $rand = rand( 23445, 99 );
                                                            ?>
                                                            <li class = "col-lg-6 col-md-6 col-sm-12 col-xs-12" draggable = "true" style = "display: inline-block;">
                                                                <?php
                                                                $foodbakery_opt_array = array(
                                                                    'name' => $permission_value,
                                                                    'desc' => '',
                                                                    'echo' => true,
                                                                    'simple' => true,
                                                                    'field_params' => array(
                                                                        'std' => $value,
                                                                        'simple' => true,
                                                                        'id' => $permission_key . $rand,
                                                                        'cust_name' => 'permissions[' . $permission_key . ']',
                                                                    ),
                                                                );
                                                                $foodbakery_html_fields_frontend->foodbakery_form_checkbox_render( $foodbakery_opt_array );
                                                                ?>
                                                            </li>
                    <?php } ?>
                                                    </ul>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <button name="button" class="btn-submit" type="button" id="team_update_form"><?php echo __( 'Update212', 'foodbakery' ); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            jQuery(document).ready(function () {
                                                'use strict'
                                                jQuery(".chosen-select-no-single").chosen();
                                            });
                                        </script>
                                    </form>
                                </li>
                        <?php } ?>
                        </ul>
                        <?php
                    } else {
                        echo '<div class="cs-no-record">' . foodbakery_info_messages_restaurant( __( "Sorry! there is no team member in your list.", 'foodbakery' ) ) . '</div>';
                    }
                    ?>
                </div>
		</div>
            </div>
            <?php
        }

        /**
         * Start Function How to add form options in  html
         */
        function foodbakery_publishers_options() {
            global $post, $foodbakery_form_fields, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_options, $display_field;
            $post_id = $post->ID;
            $foodbakery_profile_image = get_post_meta( $post_id, 'foodbakery_profile_image', true );
            $publisher_company_name = get_post_meta( $post_id, 'foodbakery_publisher_company_name', true );
			$publisher_company_name = isset($publisher_company_name) ? $publisher_company_name : get_the_title($post->ID);
            $foodbakery_opt_array = array(
                'name' => __( 'Profile Image', 'foodbakery' ),
                'desc' => __( '', 'foodbakery' ),
                'hint_text' => '',
                'echo' => true,
                'id' => 'profile_image',
                'std' => '',
                'field_params' => array(
                    'id' => 'profile_image',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_upload_file_field( $foodbakery_opt_array );

            $publisher_profile_type = get_post_meta( $post_id, 'foodbakery_publisher_profile_type', true );
            echo '<div class="form-elements">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label>' . __( 'Profile Type', 'foodbakery' ) . '</label>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
            $individual_checked = $company_checked = '';
            if ( $publisher_profile_type == 'restaurant' ) {
                $display_field = 'block';
                $company_checked = ' checked';
            } else {
                $individual_checked = ' checked';
                $display_field = 'none';
            }
            $foodbakery_opt_array = array(
                'description' => ' <label for="publisher_profile_type_individual">' . foodbakery_plugin_text_srt( 'foodbakery_publisher_profile_individual' ) . '</label>',
                'echo' => true,
                'field_params' => array(
                    'std' => 'buyer',
                    'cust_id' => 'publisher_profile_type_individual',
                    'cust_name' => 'foodbakery_publisher_profile_type',
                    'extra_atr' => $individual_checked . ' class="publisher_profile_type"',
                    'usermeta' => true,
                    'return' => true
                ),
            );
            $foodbakery_html_fields->foodbakery_radio_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'description' => ' <label for="publisher_profile_type_company">' . foodbakery_plugin_text_srt( 'foodbakery_publisher_profile_company' ) . '</label>',
                'echo' => true,
                'field_params' => array(
                    'std' => 'restaurant',
                    'cust_id' => 'publisher_profile_type_company',
                    'cust_name' => 'foodbakery_publisher_profile_type',
                    'extra_atr' => $company_checked . ' class="publisher_profile_type"',
                    'usermeta' => true,
                    'return' => true
                ),
            );
            $foodbakery_html_fields->foodbakery_radio_field( $foodbakery_opt_array );
            echo '</div>
            </div>';

            echo '<div class="publisher_company_name" style=" display: ' . $display_field . ';">';
            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_publisher_company_name' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => $publisher_company_name,
                    'id' => 'publisher_company_name',
                    'cust_name' => 'foodbakery_publisher_company_name',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

            echo '</div>';

            $user_status = array(
                'pending' => __( 'Pending', 'foodbakery' ),
                'active' => __( 'Active', 'foodbakery' ),
                'inactive' => __( 'Inactive', 'foodbakery' ),
            );

            $selected_user_status = get_post_meta( 'foodbakery_user_status', $post_id );
            $selected_user_status = ( $selected_user_status == '' ? 'pending' : $selected_user_status );

            $foodbakery_opt_array = array(
                'name' => __( 'Profile Status', 'foodbakery' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => $selected_user_status,
                    'id' => 'user_status',
                    'classes' => 'chosen-select-no-single',
                    'options' => $user_status,
                    'return' => true,
                ),
            );
            $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_phone' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'phone_number',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_email_address' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'email_address',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_website' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'website',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'name' => __( 'Biography', 'foodbakery' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'biography',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_textarea_field( $foodbakery_opt_array );

            $foodbakery_html_fields->foodbakery_heading_render(
                    array(
                        'name' => foodbakery_plugin_text_srt( 'foodbakery_user_meta_social_networks' ),
                        'id' => 'social_network',
                        'classes' => '',
                        'std' => '',
                        'description' => '',
                        'hint' => '',
                    )
            );
            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_user_meta_facebook' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'user_facebook',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_user_meta_twitter' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'user_twitter',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

            $foodbakery_opt_array = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_user_meta_google_plus' ),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => '',
                    'id' => 'user_google_plus',
                    'return' => true,
                ),
            );

            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );
            $foodbakery_html_fields->foodbakery_heading_render(
                    array(
                        'name' => foodbakery_plugin_text_srt( 'foodbakery_user_meta_mailing_information' ),
                        'id' => 'mailing_information',
                        'classes' => '',
                        'std' => '',
                        'description' => '',
                        'hint' => ''
                    )
            );

            FOODBAKERY_FUNCTIONS()->foodbakery_location_fields( '', 'publisher' );
        }

    }

    global $foodbakery_publishers_meta;
    $foodbakery_publishers_meta = new Foodbakery_Publishers_Meta();
    return $foodbakery_publishers_meta;
}