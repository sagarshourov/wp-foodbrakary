<?php
/**
 * Publisher Restaurants
 *
 */
if ( ! class_exists( 'Foodbakery_Publisher_Restaurants' ) ) {

    class Foodbakery_Publisher_Restaurants {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action( 'wp_ajax_foodbakery_publisher_restaurants', array( $this, 'foodbakery_publisher_restaurants_callback' ), 11, 1 );
            add_action( 'wp_ajax_nopriv_foodbakery_publisher_restaurants', array( $this, 'foodbakery_publisher_restaurants_callback' ), 11, 1 );
            add_action( 'wp_ajax_foodbakery_publisher_restaurant_delete', array( $this, 'delete_user_restaurant' ) );
        }

        /**
         * Publisher Restaurants
         * @ filter the restaurants based on publisher id
         */
        public function foodbakery_publisher_restaurants_callback( $publisher_id = '' ) {
            global $current_user;
            $publisher_id = foodbakery_company_id_form_user_id( $current_user->ID );
            $args = array(
                'posts_per_page' => "-1",
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_restaurant_username',
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

            echo force_balance_tags( $this->render_view( $all_restaurants ) );
            wp_reset_postdata();
            wp_die();
        }

        /**
         * Publisher Restaurants HTML render
         * @ HTML before and after the restaurant items
         */
        public function render_view( $all_restaurants ) {
            global $foodbakery_plugin_options;
            $foodbakery_dashboard_page = isset( $foodbakery_plugin_options['foodbakery_publisher_dashboard'] ) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink( $foodbakery_dashboard_page ) : '';
            if ( isset( $_GET['lang'] ) ) {
                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg( array( 'tab' => 'add-restaurant', 'lang' => $_GET['lang'] ), $foodbakery_dashboard_link ) : '#';
            } else if ( cs_wpml_lang_url() != '' ) {
                $cs_lang_string = cs_wpml_lang_url();
                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg( array( 'tab' => 'add-restaurant' ), cs_wpml_parse_url( $cs_lang_string, $foodbakery_dashboard_link ) ) : '#';
            } else {
                $foodbakery_restaurant_add_url = $foodbakery_dashboard_link != '' ? add_query_arg( array( 'tab' => 'add-restaurant' ), $foodbakery_dashboard_link ) : '#';
            }
            ?>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
                    <div class="user-restaurant">
                        <div class="element-title">
                            <h4><?php echo __( 'My Restaurants', 'foodbakery' ); ?></h4>
                            <div class="team-option">
                                <a href="<?php echo esc_url_raw( $foodbakery_restaurant_add_url ) ?>" class="add-more"><?php echo __( 'Add new restaurant', 'foodbakery' ); ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div id="foodbakery-dev-user-restaurant" class="user-list" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"> 
                                    <ul class="panel-group">
                                        <?php
                                        if ( isset( $all_restaurants ) && ! empty( $all_restaurants ) ) {
                                            ?>
                                            <li> <span><?php echo __( 'Restaurants', 'foodbakery' ); ?></span>
                                                <span><?php echo __( 'Posted', 'foodbakery' ); ?></span>
                                                <span><?php echo __( 'Expires' ); ?></span> </li><?php
                                            foreach ( $all_restaurants as $restaurant_data ) {
                                                echo force_balance_tags( $this->render_list_item_view( $restaurant_data ) );
                                            }
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Publisher Restaurants Items HTML render
         * @ HTML for restaurant items
         */
        public function render_list_item_view( $restaurant_data ) {
            global $post, $foodbakery_plugin_options;
            $post = $restaurant_data;
            setup_postdata( $post );

            $category = get_the_terms( get_the_ID(), 'restaurant-category' );
            $restaurant_post_on = get_post_meta( get_the_ID(), 'foodbakery_restaurant_posted', true );
            $restaurant_post_expiry = get_post_meta( get_the_ID(), 'foodbakery_restaurant_expired', true );
            $restaurant_status = get_post_meta( get_the_ID(), 'foodbakery_restaurant_status', true );
            $foodbakery_dashboard_page = isset( $foodbakery_plugin_options['foodbakery_publisher_dashboard'] ) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
            $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink( $foodbakery_dashboard_page ) : '';
            $foodbakery_restaurant_update_url = $foodbakery_dashboard_link != '' ? add_query_arg( array( 'tab' => 'add-restaurant', 'restaurant_id' => get_the_ID() ), $foodbakery_dashboard_link ) : '#';
            
            ?>
            <li id="user-restaurant-<?php echo absint( get_the_ID() ); ?>" class="alert" data-id="<?php echo esc_attr( get_the_ID() ); ?>">
                <div class="panel panel-default">
                    <a href="javascript:void(0);" data-id="<?php echo absint( get_the_ID() ); ?>" class="close-member foodbakery-dev-restaurant-delete"><i class="icon-close"></i></a>
                    <div class="panel-heading"> 
                        <div class="img-holder">
                            <?php if ( has_post_thumbnail() ) { ?>
                                <figure>
                                    <?php the_post_thumbnail( 'thumbnail' ); ?>
                                </figure>
                            <?php } ?>
                            <strong><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></strong>
                            <?php if ( isset( $category[0]->name ) && $category[0]->name != '' ) { ?>
                                <span><?php echo esc_html( $category[0]->name ); ?></span>
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
            wp_reset_postdata();
        }

        /**
         * Deleting user restaurant from dashboard
         * @Delete Restaurant
         */
        public function delete_user_restaurant() {
            global $current_user;
            $restaurant_id = isset( $_POST['restaurant_id'] ) ? $_POST['restaurant_id'] : '';
            $foodbakery_publisher_id = get_post_meta( $restaurant_id, 'foodbakery_restaurant_username', true );
            $publisher_id = foodbakery_company_id_form_user_id( $current_user->ID );
            if ( is_user_logged_in() && $publisher_id == $foodbakery_publisher_id ) {
                update_post_meta( $restaurant_id, 'foodbakery_restaurant_status', 'delete' );
                echo json_encode( array( 'delete' => 'true' ) );
            } else {
                echo json_encode( array( 'delete' => 'false' ) );
            }
            die;
        }

        /**
         * Publisher Restaurants count
         * @ filter the restaurants based on publisher id
         */
        public function foodbakery_publisher_restaurants_count( $publisher_id = '' ) {
            global $current_user;
            if ( $publisher_id == '' ) {
                $publisher_id = foodbakery_company_id_form_user_id( $current_user->ID );
            }
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
                        'key' => 'foodbakery_restaurant_status',
                        'value' => 'delete',
                        'compare' => '!=',
                    ),
                ),
            );
            $custom_query = new WP_Query( $args );
            $all_restaurants = $custom_query->found_posts;
            return $all_restaurants;
        }

    }

    global $foodbakery_publisher_restaurants;
    $foodbakery_publisher_restaurants = new Foodbakery_Publisher_Restaurants();
}
