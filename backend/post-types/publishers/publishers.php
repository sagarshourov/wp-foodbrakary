<?php
/**
 * Register Post Type Publishers
 * @return
 *
 */
if ( ! class_exists( 'Foodbakery_Post_Type_Publishers' ) ) {

    class Foodbakery_Post_Type_Publishers {

        // The Constructor
        public function __construct() {
            add_action( 'init', array( $this, 'publishers_register' ), 12 );
            add_filter( "get_user_option_screen_layout_publishers", array( $this, 'foodbakery_screen_layout' ) );
            add_action( 'admin_menu', array( $this, 'foodbakery_remove_post_boxes' ) );
           
        }

        /**
         * @Register Post Type
         * @return
         *
         */
        public function publishers_register() {

            global $foodbakery_plugin_static_text;

            $labels = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_publishers' ),
                'singular_name' => foodbakery_plugin_text_srt( 'foodbakery_company' ),
                'menu_name' => __( 'Publishers', 'foodbakery' ),
                'name_admin_bar' => foodbakery_plugin_text_srt( 'foodbakery_publishers' ),
                'add_new' => foodbakery_plugin_text_srt( 'foodbakery_add_company' ),
                'add_new_item' => foodbakery_plugin_text_srt( 'foodbakery_add_company' ),
                'new_item' => foodbakery_plugin_text_srt( 'foodbakery_add_company' ),
                'edit_item' => foodbakery_plugin_text_srt( 'foodbakery_edit_company' ),
                'view_item' => foodbakery_plugin_text_srt( 'foodbakery_company' ),
                'all_items' => foodbakery_plugin_text_srt( 'foodbakery_publishers' ),
                'search_items' => foodbakery_plugin_text_srt( 'foodbakery_company' ),
                'not_found' => foodbakery_plugin_text_srt( 'foodbakery_publishers' ),
                'not_found_in_trash' => foodbakery_plugin_text_srt( 'foodbakery_publishers' ),
            );

            $args = array(
                'labels' => $labels,
                'description' => foodbakery_plugin_text_srt( 'foodbakery_publishers' ),
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'menu_position' => 28,
                'menu_icon' => wp_foodbakery::plugin_url() . 'assets/backend/images/publishers.png',
              
                'query_var' => false,
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => true,
                'supports' => array( 'title' ),
                'exclude_from_search' => true
            );

            register_post_type( 'publishers', $args );
        }

        // add submit button at bottom of post 
        public function foodbakery_submit_meta_box( $post, $args = array() ) {
            global $action, $post, $foodbakery_plugin_static_text;


            $post_type = $post->post_type;
            $post_type_object = get_post_type_object( $post_type );
            $can_publish = current_user_can( $post_type_object->cap->publish_posts );
            ?>
            <div class="submitbox foodbakery-submit" id="submitpost">
                <div id="minor-publishing">
                    <div style="display:none;">
                        <?php submit_button( foodbakery_plugin_text_srt( 'foodbakery_submit' ), 'button', 'save' ); ?>
                    </div>
                    <?php
                    if ( $post_type_object->public && ! empty( $post ) ) :
                        if ( 'publish' == $post->post_status ) {
                            $preview_link = esc_url( get_permalink( $post->ID ) );
                            $preview_button = foodbakery_plugin_text_srt( 'foodbakery_preview' );
                        } else {
                            $preview_link = set_url_scheme( get_permalink( $post->ID ) );

                            /**
                             * Filter the URI of a post preview in the post submit box.
                             *
                             * @since 2.0.5
                             * @since 4.0.0 $post parameter was added.
                             *
                             * @param string  $preview_link URI the user will be directed to for a post preview.
                             * @param WP_Post $post         Post object.
                             */
                            $preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', esc_url( $preview_link ) ), $post ) );
                            $preview_button = foodbakery_plugin_text_srt( 'foodbakery_preview' );
                        }

                    endif; // public post type        
                    ?>
                </div>
                <div id="major-publishing-actions" style="border-top:0px">
                    <?php
                    /**
                     * Fires at the beginning of the publishing actions section of the Publish meta box.
                     *
                     * @since 2.7.0
                     */
                    do_action( 'post_submitbox_start' );
                    ?>
                    <div id="delete-action">
                        <?php
                        if ( current_user_can( "delete_post", $post->ID ) ) {
                            if ( ! EMPTY_TRASH_DAYS ) {
                                $delete_text = foodbakery_plugin_text_srt( 'foodbakery_delete_permanently' );
                            } else {
                                $delete_text = foodbakery_plugin_text_srt( 'foodbakery_move_to_trash' );
                            }
                            if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
                                ?>
                                <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo foodbakery_allow_special_char( $delete_text ) ?></a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <?php
                        if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ) ) || 0 == $post->ID ) {
                            if ( $can_publish ) :
                                if ( ! empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) :
                                    ?>
                                    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo esc_html( 'foodbakery_schedule' ); ?>" />
                                    <?php submit_button( esc_html( 'foodbakery_schedule' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
                                <?php else : ?>
                                    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt( 'foodbakery_publish' ); ?>" />
                                    <?php submit_button( foodbakery_plugin_text_srt( 'foodbakery_publish' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
                                <?php
                                endif;
                            else :
                                ?>
                                <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt( 'foodbakery_submit_for_review' ); ?>" />
                                <?php submit_button( foodbakery_plugin_text_srt( 'foodbakery_submit_for_review' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
                            <?php
                            endif;
                        } else {

                            if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
                                ?>
                                <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt( 'foodbakery_update' ); ?>" />
                                <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php echo foodbakery_plugin_text_srt( 'foodbakery_update' ); ?>" />
                                <?php
                            } else {
                                ?>
                                <input name="original_publish" type="hidden" id="original_publish" value="<?php echo foodbakery_plugin_text_srt( 'foodbakery_publish' ); ?>">
                                <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php echo foodbakery_plugin_text_srt( 'foodbakery_publish' ); ?>" accesskey="p">
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <?php
        }

        //remove extra boxes
        public function foodbakery_remove_post_boxes() {

            remove_meta_box( 'submitdiv', 'publishers', 'side' );
           
            remove_meta_box( 'mymetabox_revslider_0', 'publishers', 'normal' );
            remove_meta_box( 'mymetabox_revslider_0', 'publishers', 'normal' );
        }

        // remove submit button
        public function foodbakery_remove_help_tabs() {
            $screen = get_current_screen();
            if ( $screen->post_type == 'publishers' ) {
                add_filter( 'screen_options_show_screen', '__return_false' );
                add_filter( 'bulk_actions-edit-publishers', '__return_empty_array' );
                echo '<style type="text/css">
				.post-type-publishers .tablenav.bottom,
				.post-type-publishers #titlediv .inside,
				.post-type-publishers #postdivrich{
					display: none;
				}
			</style>';
                echo '
		<script>
			jQuery(document).ready(function($){
				$(\'form#post\').submit(function() {
					var errorr = 0;
					$(\'.dir-res-meta-key-field\').each(function(){
						if($(this).val() == \'\'){
							errorr = 1;
							$(this).parents(\'.pb-item-container\').find(\'.pbwp-legend\').addClass(\'item-field-error\');
						}
						if($(this).parents(\'.pb-item-container\').find(\'.pbwp-legend\').hasClass(\'item-field-error\')){
							errorr = 1;
						}
					});
					
					$(\'.dir-meta-key-field\').each(function(){
						if($(this).val() == \'\') {
							errorr = 1;
							$(this).parents(\'.pb-item-container\').find(\'.pbwp-legend\').addClass(\'item-field-error\');
						}
						if($(this).parents(\'.pb-item-container\').find(\'.pbwp-legend\').hasClass(\'item-field-error\')){
							errorr = 1;
						}
					});
					
					$(\'.field-dropdown-opt-values\').each(function(){
						var field_this = $(this);
						var val_field = $(this).find(\'input[id^="cus_field_dropdown_options_values_"]\');
						if(val_field.length === 0){
							errorr = 1;
							$(this).parents(\'.pb-item-container\').find(\'.pbwp-legend\').addClass(\'item-field-error\');
							alert(\'Please Put atleat 1 or 2 values for dropdown options.\');
						} else {
							val_field.each(function(){
								if($(this).val() == \'\'){
									errorr = 1;
									field_this.parents(\'.pb-item-container\').find(\'.pbwp-legend\').addClass(\'item-field-error\');
									alert(\'Options Values cannot be blank.\');
								}
							});
						}
					});

					if(errorr == 0){
						return true;
					}
					return false;
				});
			});
		</script>';
            }
        }

        // set one column layout
        public function foodbakery_screen_layout( $selected ) {
            return 1; // Use 1 column if user hasn't selected anything in Screen Options
        }

    }

    global $foodbakery_post_type_publishers;

    $foodbakery_post_type_publishers = new Foodbakery_Post_Type_Publishers();
}

add_filter( 'manage_publishers_posts_columns', 'publishers_custom_columns' );
add_action( 'manage_publishers_posts_custom_column', 'manage_publisher_custom_column_callback' );

function publishers_custom_columns( $columns ) {
    unset( $columns['date'] );
    $columns['display_name'] = 'Members Name';
    $columns['email'] = 'Email';
    $columns['publisher-type'] = 'Type';
    $columns['team_members'] = 'Team Members';
    $columns['restaurants'] = 'Restaurants';
    $columns['profile_status'] = 'Status';

    $columns['deatail'] = 'Detail';



    return $columns;
}

function manage_publisher_custom_column_callback( $column_name ) {
    global $post;
    switch ( $column_name ) {
        case 'display_name':
            $company_data = get_post( $post->ID );
            setup_postdata( $company_data );
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
                echo '<ul>';
                foreach ( $team_members as $member_data ) {
                    echo '<li><a href=" ' . get_edit_user_link( $member_data->ID ) . ' "> ' . esc_html( $member_data->display_name ) . ' </a></li>';
                }
                echo '</ul>';
            } else {
                echo '-';
            }
            break;
        case 'email':
            $foodbakery_email_address = get_post_meta( $post->ID, 'foodbakery_email_address', true );
            if ( isset( $foodbakery_email_address ) && ! empty( $foodbakery_email_address ) ) {
                echo esc_html( $foodbakery_email_address );
            } else {
                echo '-';
            }
            break;
        case 'publisher-type':
            $foodbakery_publisher_profile_type = get_post_meta( $post->ID, 'foodbakery_publisher_profile_type', true );
            if ( isset( $foodbakery_publisher_profile_type ) && ! empty( $foodbakery_publisher_profile_type ) ) {
                echo esc_html( ucfirst( $foodbakery_publisher_profile_type ) );
            } else {
                echo '-';
            }
            break;
        case 'team_members':
            $company_data = get_post( $post->ID );
            setup_postdata( $company_data );
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
            if ( isset( $team_members ) && $team_members != '' ) {
                $totalmember = count( $team_members );
            }
            if ( isset( $totalmember ) && $totalmember != 0 ) {
                echo esc_html( $totalmember );
            } else {
                echo '-';
            }
            break;
        case 'restaurants':
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
            if ( isset( $all_restaurants ) && $all_restaurants != '' ) {
                $total_list = count( $all_restaurants );
            }
            if ( isset( $total_list ) && $total_list != 0 ) {
                echo esc_html( $total_list );
            } else {
                echo '-';
            }
            break;
        case 'profile_status':
            $foodbakery_user_status = get_post_meta( $post->ID, 'foodbakery_user_status', true );
            $foodbakery_user_status = ( $foodbakery_user_status != '' )? $foodbakery_user_status : 'pending';
            echo esc_html( $foodbakery_user_status );
            break;
        case 'deatail':

            // query for active add
            $args = array(
                'post_type' => 'restaurants',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_restaurant_publisher',
                        'value' => $post->ID,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_restaurant_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                ),
            );
            $query = new WP_Query( $args );
            $count_lisings = $query->post_count;

            // query for expired add
            $args_expire = array(
                'post_type' => 'restaurants',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_restaurant_username',
                        'value' => $post->ID,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_restaurant_expired',
                        'value' => current_time( 'timestamp' ),
                        'compare' => '<',
                    ),
                ),
            );
            $query_expire = new WP_Query( $args_expire );
            $expire_adds = $query_expire->post_count;
            $last_login = get_post_meta( $post->ID, 'last_login', true );
            $output = '<ul>';

            if ( $last_login && $last_login != '' ) {
                $output .= '<li>Last Login : <span>' . human_time_diff( $last_login, current_time( 'timestamp' ) ) . ' ago' . '</span></li>';
            } else {
                $output .= '<li>Last Login : <span> never </span> </li>';
            }
            $output .= ' <li>Active Ads : <span>' . $count_lisings . '</span></li>
		    <li>Expire Ads : <span>' . $expire_adds . '</span> </li>
		</ul>';
		echo force_balance_tags($output);
		break;
    }
}

// add analytic 

add_filter( 'views_edit-publishers', function( $views ) {
    $args = array(
        'post_type' => 'publishers',
        'posts_per_page' => "-1",
    );
    $custom_query = new WP_Query( $args );
    $total_publisher = 0;
    $total_company = 0;
    $total_individual = 0;
    $total_active = 0;
    $total_pending = 0;
    while ( $custom_query->have_posts() ) : $custom_query->the_post();
        global $post;
        $foodbakery_publisher_profile_type = get_post_meta( $post->ID, 'foodbakery_publisher_profile_type', true );
        $foodbakery_user_status = get_post_meta( $post->ID, 'foodbakery_user_status', true );
        if ( isset( $foodbakery_publisher_profile_type ) && ! empty( $foodbakery_publisher_profile_type ) ) {
            if ( $foodbakery_publisher_profile_type == 'restaurant' ) {
                $total_company ++;
            } else if ( $foodbakery_publisher_profile_type == 'buyer' ) {
                $total_individual ++;
            }
        }
        if ( isset( $foodbakery_user_status ) && ! empty( $foodbakery_user_status ) ) {
            if ( $foodbakery_user_status == 'active' ) {
                $total_active ++;
            } else if ( $foodbakery_user_status == 'pending' ) {
                $total_pending ++;
            }
        }
        $total_publisher ++;
    endwhile;
    wp_reset_postdata();
    echo '
    <ul class="total-foodbakery-restaurant row">
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Total Publishers </strong><em>' . $total_publisher . '</em><i class="icon-users"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Active Publishers </strong><em>' . $total_active . '</em><i class="icon-check_circle"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Pending Publishers </strong><em>' . $total_pending . '</em><i class="icon-back-in-time"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Buyer </strong><em>' . $total_individual . '</em><i class="icon-user"></i></div></li>
	<li class="col-lg-3 col-md-3 col-sm-6 col-xs-12"><div class="foodbakery-text-holder"><strong>Restaurants </strong><em>' . $total_company . '</em><i class="icon-building"></i></div></li>
    </ul>';
    return $views;
} );

// End  analytic 







