<?php
/**
 * Register Post Type Inventory Type
 * @return
 *
 */
if ( ! class_exists( 'Foodbakery_Post_Restaurant_Types' ) ) {

    class Foodbakery_Post_Restaurant_Types {

        // The Constructor
        public function __construct() {
            add_action( 'init', array( $this, 'restaurant_type_register' ), 12 );
            add_action( 'admin_menu', array( $this, 'foodbakery_remove_post_boxes' ) );
            add_filter( 'post_row_actions', array( $this, 'restaurant_type_remove_row_actions' ), 10, 1 );
            add_action( 'foodbakery_types_dropdown_menu', array( $this, 'foodbakery_types_dropdown_menu_callback' ) );
            add_action( 'foodbakery_location_dropdown', array( $this, 'foodbakery_location_dropdown_callback' ) );
            add_action( 'admin_head', array( $this, 'stop_heartbeat' ), 1 );
            add_action( "init", array( $this, 'block_post' ) );
            add_action( 'admin_head', array( $this, 'remove_post_title' ) );
        }

        function block_post() {
            if ( isset( $_GET["post_type"] ) && $_GET["post_type"] == "restaurant-type" ) {
                $restaurants_type_post = get_posts( 'post_type=restaurant-type&posts_per_page=1&post_status=publish' );
                if ( empty( $restaurants_type_post ) ) {
                    wp_redirect( "edit.php?post_type=restaurants" );
                } else {
                    $restaurants_type_id = isset( $restaurants_type_post[0]->ID ) ? $restaurants_type_post[0]->ID : '';
                    if ( $restaurants_type_id ) {
                        wp_redirect( "post.php?post={$restaurants_type_id}&action=edit" );
                    } else {
                        wp_redirect( "edit.php?post_type=restaurants" );
                    }
                }
            }
        }

        function remove_post_title() {
            if ( get_post_type() == 'restaurant-type' ) {
                ?>
                <style type="text/css">
                    #titlediv {
                        display: none;
                    }
                    #foodbakery_meta_restaurant_type > h2, #foodbakery_meta_restaurant_type > button {
                        display: none;
                    }
                </style>
                <?php
            }
        }

        public function stop_heartbeat() {
            if ( get_post_type() == 'restaurant-type' ) {
                wp_deregister_script( 'heartbeat' );
            }
        }

        /**
         * @Register Post Type
         * @return
         *
         */
        function restaurant_type_register() {

            global $foodbakery_plugin_static_text;

            $labels = array(
                'name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_types' ),
                'singular_name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_type' ),
                'menu_name' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_types' ),
                'name_admin_bar' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_types' ),
                'add_new' => foodbakery_plugin_text_srt( 'foodbakery_add_restaurant_type' ),
                'add_new_item' => foodbakery_plugin_text_srt( 'foodbakery_add_restaurant_type' ),
                'new_item' => foodbakery_plugin_text_srt( 'foodbakery_add_restaurant_type' ),
                'edit_item' => foodbakery_plugin_text_srt( 'foodbakery_edit_restaurant_type' ),
                'view_item' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_type' ),
                'all_items' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_types' ),
                'search_items' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_type' ),
                
                'not_found' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_types' ),
                'not_found_in_trash' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_types' ),
            );

            $args = array(
                'labels' => $labels,
                'description' => foodbakery_plugin_text_srt( 'foodbakery_restaurant_types' ),
                'public' => true,
                'taxonomies' => array( 'restaurant-category' ),
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => 'edit.php?post_type=restaurants',
                'query_var' => false,
                'capability_type' => 'post',
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false,
                'supports' => array( 'title' ),
                'exclude_from_search' => true,
                'can_export' => true,
                'capabilities' => array(
                    'create_posts' => false,
                ),
                'map_meta_cap' => true,
            );

            register_post_type( 'restaurant-type', $args );
        }

        function foodbakery_submit_meta_box( $post, $args = array() ) {
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
                    <?php if ( $post_type_object->public && ! empty( $post ) ) : ?>
                       
                        <?php
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
                        endif; // public post type          ?>


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

        /*
         * location drop down
         */
        /*
         * types drop down menu
         */

        public function foodbakery_location_dropdown_callback() {
            global $foodbakery_plugin_options;
            $locations_switch = isset( $foodbakery_plugin_options['foodbakery_hedaer_location_switch'] ) ? $foodbakery_plugin_options['foodbakery_hedaer_location_switch'] : '';
            $rand_numb = rand( 999, 999999 );
            if ( $locations_switch == 'on' ) {
                ?>
                <li class="location-has-children choose-location">
                    <a href="#"><i class="icon-location-pin2"></i><?php echo esc_html__( 'Choose location', 'foodbakery' ); ?></a>
                    <ul>
                        <?php
                        global $foodbakery_plugin_options;
                        $foodbakery_search_result_page = isset( $foodbakery_plugin_options['foodbakery_search_result_page'] ) ? $foodbakery_plugin_options['foodbakery_search_result_page'] : '';
                        custom_search_location_front();

                        $foodbakery_select_display = 1;
                        if ( ! empty( $foodbakery_search_result_page ) ) {

                            foodbakery_get_custom_locations_restaurant_filter( '', '', false, $rand_numb, 'header', '' );
                           
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }
        }

        public function foodbakery_types_dropdown_menu_callback() {
            $args = array( 'post_type' => 'restaurant-type', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', );
            $loop_query = new Wp_Query( $args );
            if ( $loop_query->have_posts() ):
                while ( $loop_query->have_posts() ):

                    $loop_query->the_post();
                    global $post;
                    $restaurant_type_id = $post;

                    $foodbakery_search_result_page = get_post_meta( $restaurant_type_id, 'foodbakery_search_result_page', true );
                    $foodbakery_search_result_page = isset( $foodbakery_search_result_page ) && $foodbakery_search_result_page != '' ? get_permalink( $foodbakery_search_result_page ) : '';
                endwhile;
            endif;
            wp_reset_postdata();

            global $foodbakery_plugin_options;
            $foodbakery_hedaer_restaurant_switch = isset( $foodbakery_plugin_options['foodbakery_hedaer_restaurant_switch'] ) ? $foodbakery_plugin_options['foodbakery_hedaer_restaurant_switch'] : '';
            $selected_fileds = isset( $foodbakery_plugin_options['default_cousins_list'] ) ? $foodbakery_plugin_options['default_cousins_list'] : '';
            if ( $foodbakery_hedaer_restaurant_switch == 'on' ) {
                ?>
                <li class="location-has-children"><a href="#"><i class="icon-compass-with-white-needles"></i><?php echo esc_html__( 'Feel Like Eating', 'foodbakery' ); ?></a>
                        <?php
                        if ( $selected_fileds != '' ) {
                            echo '<ul>';
                            foreach ( $selected_fileds as $val ) {
                                $restaurant_type_cate = get_term_by( 'slug', $val, 'restaurant-category' );

                                $restaurant_type_cate_id = isset( $restaurant_type_cate->term_id ) && $restaurant_type_cate->term_id != '' ? $restaurant_type_cate->term_id : '';
                                $term_image = '';
                                $post_count = '';

                                $term_icon = get_term_meta( $restaurant_type_cate_id, 'foodbakery_restaurant_taxonomy_icon', true );
                                $term_image = get_term_meta( $restaurant_type_cate_id, 'foodbakery_listing_term_image', true );

                                if ( $term_icon == '' ) {
                                    $term_image = get_term_meta( $restaurant_type_cate_id, 'foodbakery_listing_term_image', true );
                                    $term_image_src = wp_get_attachment_url( $term_image );
                                    $term_image = '<img alt="" src="' . $term_image_src . '"/>';
                                }
                                if ( $term_icon != '' ) {
                                    $term_icon = isset( $term_icon ) && $term_icon != '' ? '<i class="' . $term_icon . '"></i>' : '';
                                } else {
                                    $term_icon = $term_image;
                                }

                                $cate_link = isset( $foodbakery_search_result_page ) && $foodbakery_search_result_page != '' ? $foodbakery_search_result_page . '?foodbakery_restaurant_category=' . $val . '' : '';

                                echo '<li>';
                                echo '<a href="' . esc_url( $cate_link ) . '">' . force_balance_tags( $term_icon ) . '' . esc_html( $restaurant_type_cate->name ) . '</a> <span>' . $post_count . '</span> ';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<ul>';
                            echo '<li class="cousine-notice">' . __( 'Please Select Cousines from plugin options', 'foodbakery' ) . '</li>';
                            echo '</ul>';
                        }
                        ?>

                </li>
                <?php
            }
        }

        public function foodbakery_types_array_callback() {
            $restaurant_types_data = array();
            $restaurant_types_data[''] = __( 'ALL Restaurant Type' );
            $foodbakery_restaurant_args = array( 'posts_per_page' => '-1', 'post_type' => 'restaurant-type', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC' );
            $cust_query = get_posts( $foodbakery_restaurant_args );
            if ( is_array( $cust_query ) && sizeof( $cust_query ) > 0 ) {
                foreach ( $cust_query as $foodbakery_restaurant_type ) {
                    $restaurant_types_data[$foodbakery_restaurant_type->post_name] = get_the_title( $foodbakery_restaurant_type->ID );
                }
            }
            return $restaurant_types_data;
        }

		public function foodbakery_single_types_slug_callback() { 
            $foodbakery_restaurant_args = array( 'posts_per_page' => '-1', 'post_type' => 'restaurant-type', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC' );
            $cust_query = get_posts( $foodbakery_restaurant_args );
			$type_slug = '';
            if ( is_array( $cust_query ) && sizeof( $cust_query ) > 0 ) {
                foreach ( $cust_query as $foodbakery_restaurant_type ) {
                   $type_slug = $foodbakery_restaurant_type->post_name;
                }
            }
            return $type_slug;
        }

        public function foodbakery_types_custom_fields_array_required_fields( $fields ) {
            return 'ID, foodbakery_restaurant_type_cus_fields'; // etc
        }

        public function foodbakery_types_custom_fields_array( $restaurant_type ) {
            $foodbakery_restaurant_type_cus_fields = '';
            if ( $restaurant_type != '' ) {

                $restaurant_type_post = get_posts( array( 'fields' => 'ids', 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type", 'post_status' => 'publish' ) );
                $restaurant_type_id = isset( $restaurant_type_post[0] ) ? $restaurant_type_post[0] : 0;
                $foodbakery_restaurant_type_cus_fields = get_post_meta( $restaurant_type_id, "foodbakery_restaurant_type_cus_fields", true );
            }
            return $foodbakery_restaurant_type_cus_fields;
        }

        public function restaurant_type_remove_row_actions( $actions ) {

            if ( get_post_type() === 'restaurant-type' )
                unset( $actions['view'] );
            unset( $actions['inline hide-if-no-js'] );
            return $actions;
        }

        function foodbakery_remove_post_boxes() {

            remove_meta_box( 'submitdiv', 'restaurant-type', 'side' );
           
            remove_meta_box( 'mymetabox_revslider_0', 'restaurant-type', 'normal' );
            remove_meta_box( 'mymetabox_revslider_0', 'restaurant-type', 'normal' );
        }

    }

    global $foodbakery_post_restaurant_types;

    $foodbakery_post_restaurant_types = new Foodbakery_Post_Restaurant_Types();
}



function foodbakery_remove_help_tabs() {
    $screen = get_current_screen();
    if ( $screen->post_type == 'restaurant-type' ) {
        add_filter( 'screen_options_show_screen', '__return_false' );
        add_filter( 'bulk_actions-edit-restaurant-type', '__return_empty_array' );
        echo '<style type="text/css">
				.post-type-restaurant-type .tablenav.top,
				.post-type-restaurant-type .tablenav.bottom,
				.post-type-restaurant-type #titlediv .inside,
				.post-type-restaurant-type #postdivrich{
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
							//alert(\' Please fill the fields \');
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
					
					$(\'.field-dropdown-opt-values1\').each(function(){
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

function restaurant_type_cpt_columns( $columns ) {
    unset( $columns['date'], $columns['cb'] );
    return $columns;
}

add_filter( 'manage_restaurant-type_posts_columns', 'restaurant_type_cpt_columns' );
