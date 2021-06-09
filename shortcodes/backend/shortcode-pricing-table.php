<?php
/**
 * Shortcode Name : pricing_table
 *
 * @package	foodbakery 
 */
if ( ! function_exists( 'foodbakery_var_page_builder_pricing_table' ) ) {

    function foodbakery_var_page_builder_pricing_table( $die = 0 ) {
        global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if ( function_exists( 'foodbakery_shortcode_names' ) ) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'pricing_table';

            $foodbakery_counter = isset( $_POST['counter'] ) ? $_POST['counter'] : '';
            if ( isset( $_POST['action'] ) && ! isset( $_POST['shortcode_element_id'] ) ) {
                $foodbakery_POSTID = '';
                $shortcode_element_id = '';
            } else {
                $foodbakery_POSTID = isset( $_POST['POSTID'] ) ? $_POST['POSTID'] : '';
                $shortcode_element_id = isset( $_POST['shortcode_element_id'] ) ? $_POST['shortcode_element_id'] : '';
                $shortcode_str = stripslashes( $shortcode_element_id );
                $parseObject = new ShortcodeParse();
                $foodbakery_output = $parseObject->foodbakery_shortcodes( $foodbakery_output, $shortcode_str, true, $foodbakery_PREFIX );
            }
            $defaults = array(
                'pricing_table_title' => '',
                'pricing_table_subtitle' => '',
                'foodbakery_pricing_tables' => '',
                'pricing_table_view' => 'simple',
                'foodbakery_var_pricing_table_align' => '',
            );
            if ( isset( $foodbakery_output['0']['atts'] ) ) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if ( isset( $foodbakery_output['0']['content'] ) ) {
                $pricing_table_column_text = $foodbakery_output['0']['content'];
            } else {
                $pricing_table_column_text = '';
            }
            $pricing_table_element_size = '100';
            foreach ( $defaults as $key => $values ) {
                if ( isset( $atts[$key] ) ) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_pricing_table';
            $coloumn_class = 'column_' . $pricing_table_element_size;
            if ( isset( $_POST['shortcode_element'] ) && $_POST['shortcode_element'] == 'shortcode' ) {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            foodbakery_var_date_picker();
            ?>

            <div id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>_del" class="column  parentdelete <?php echo esc_attr( $coloumn_class ); ?>
                 <?php echo esc_attr( $shortcode_view ); ?>" item="pricing_table" data="<?php echo foodbakery_element_size_data_array_index( $pricing_table_element_size ) ?>" >
                     <?php foodbakery_element_setting( $name, $foodbakery_counter, $pricing_table_element_size ) ?>
                <div class="cs-wrapp-class-<?php echo intval( $foodbakery_counter ) ?>
                     <?php echo esc_attr( $shortcode_element ); ?>" id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>" data-shortcode-template="[pricing_table {{attributes}}]{{content}}[/pricing_table]" style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr( $foodbakery_counter ) ?>">
                        <h5><?php echo foodbakery_var_frame_text_srt( 'foodbakery_var_edit_pricing_table_page' ) ?></h5>
                        <a href="javascript:foodbakery_frame_removeoverlay('<?php echo esc_js( $name . $foodbakery_counter ) ?>','<?php echo esc_js( $filter_element ); ?>')" class="cs-btnclose">
                            <i class="icon-times"></i>
                        </a>
                    </div>
                    <div class="cs-pbwp-content">
                        <div class="cs-wrapp-clone cs-shortcode-wrapp">
                            <?php
                            if ( isset( $_POST['shortcode_element'] ) && $_POST['shortcode_element'] == 'shortcode' ) {
                                foodbakery_shortcode_element_size();
                            }

                            $pricing_table = array();
                            $args = array( 'post_type' => 'foodbakery-pt', 'posts_per_page' => '-1', 'post_status' => 'publish' );
                            $query = new wp_query( $args );
                            while ( $query->have_posts() ):
                                $query->the_post();
                                $pricing_table[get_the_id()] = get_the_title();
                            endwhile;

                            wp_reset_postdata();

                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'Element Title', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Enter element title here.", "foodbakery" ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $pricing_table_title,
                                    'id' => 'pricing_table_title',
                                    'cust_name' => 'pricing_table_title[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );
                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'Element Sub  Title', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Enter element sub title here.", "foodbakery" ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $pricing_table_subtitle,
                                    'id' => 'pricing_table_subtitle',
                                    'cust_name' => 'pricing_table_subtitle[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => __( 'Title Alignment', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( 'Set element title alignment here', 'foodbakery' ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $foodbakery_var_pricing_table_align,
                                    'id' => '',
                                    'cust_id' => 'foodbakery_var_pricing_table_align',
                                    'cust_name' => 'foodbakery_var_pricing_table_align[]',
                                    'classes' => 'service_postion chosen-select-no-single select-medium',
                                    'options' => array(
                                        'align-left' => __( 'Align Left', 'foodbakery' ),
                                        'align-right' => __( 'Align Right', 'foodbakery' ),
                                        'align-center' => __( 'Align Center', 'foodbakery' ),
                                    ),
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'View', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Please Select element view.", "foodbakery" ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $pricing_table_view,
                                    'id' => 'pricing_table_view',
                                    'cust_name' => 'pricing_table_view[]',
                                    'return' => true,
                                    'options' => array( 'simple' => 'Simple', 'classic' => 'Classic', 'advance' => 'Advance' ),
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'Element Pricing Tables', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Enter element sub title here.", "foodbakery" ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $foodbakery_pricing_tables,
                                    'id' => 'pricing_tables',
                                    'cust_name' => 'foodbakery_pricing_tables[]',
                                    'return' => true,
                                    'options' => $pricing_table
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );

                            if ( function_exists( 'foodbakery_shortcode_custom_classes_test' ) ) {
                                foodbakery_shortcode_custom_dynamic_classes( $pricing_table_custom_class, $pricing_table_custom_animation, '', 'pricing_table' );
                            }
                            ?>


                        </div>
                        <?php if ( isset( $_POST['shortcode_element'] ) && $_POST['shortcode_element'] == 'shortcode' ) { ?>
                            <ul class="form-elements insert-bg">
                                <li class="to-field">
                                    <a class="insert-btn cs-main-btn" onclick="javascript:foodbakery_shortcode_insert_editor('<?php echo str_replace( 'foodbakery_var_page_builder_', '', $name ); ?>', '<?php echo esc_js( $name . $foodbakery_counter ) ?>', '<?php echo esc_js( $filter_element ); ?>')" ><?php echo foodbakery_var_frame_text_srt( 'foodbakery_var_insert' ); ?></a>
                                </li>
                            </ul>
                            <div id="results-shortocde"></div>
                        <?php } else { ?>

                            <?php
                            $foodbakery_opt_array = array(
                                'std' => 'pricing_table',
                                'id' => '',
                                'before' => '',
                                'after' => '',
                                'classes' => '',
                                'extra_atr' => '',
                                'cust_id' => 'foodbakery_orderby' . $foodbakery_counter,
                                'cust_name' => 'foodbakery_orderby[]',
                                'required' => false
                            );
                            $foodbakery_var_form_fields->foodbakery_var_form_hidden_render( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => '',
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => 'Save',
                                    'cust_id' => 'pricing_table_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn',
                                    'cust_name' => 'pricing_table_save',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
                        }
                        ?>
                    </div>
                </div>
                <script type="text/javascript">
                    
                    popup_over();

                </script>
            </div>

            <?php
        }
        if ( $die <> 1 ) {
            die();
        }
    }

    add_action( 'wp_ajax_foodbakery_var_page_builder_pricing_table', 'foodbakery_var_page_builder_pricing_table' );
}

if ( ! function_exists( 'foodbakery_save_page_builder_data_pricing_table_callback' ) ) {

    /**
     * Save data for pricing_table shortcode.
     *
     * @param	array $args
     * @return	array
     */
    function foodbakery_save_page_builder_data_pricing_table_callback( $args ) {

        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        if ( $widget_type == "pricing_table" || $widget_type == "cs_pricing_table" ) {
            $foodbakery_bareber_pricing_table = '';

            $page_element_size = $data['pricing_table_element_size'][$counters['foodbakery_global_counter_pricing_table']];
            $current_element_size = $data['pricing_table_element_size'][$counters['foodbakery_global_counter_pricing_table']];

            if ( isset( $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] ) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode' ) {
                $shortcode_str = stripslashes( ( $data['shortcode']['pricing_table'][$counters['foodbakery_shortcode_counter_pricing_table']] ) );

                $element_settings = 'pricing_table_element_size="' . $current_element_size . '"';
                $reg = '/pricing_table_element_size="(\d+)"/s';
                $shortcode_str = preg_replace( $reg, $element_settings, $shortcode_str );
                $shortcode_data .= $shortcode_str;
                $counters['foodbakery_shortcode_counter_pricing_table'] ++;
            } else {
                $element_settings = 'pricing_table_element_size="' . htmlspecialchars( $data['pricing_table_element_size'][$counters['foodbakery_global_counter_pricing_table']] ) . '"';
                $foodbakery_bareber_pricing_table = '[pricing_table ' . $element_settings . ' ';
                if ( isset( $data['pricing_table_title'][$counters['foodbakery_counter_pricing_table']] ) && $data['pricing_table_title'][$counters['foodbakery_counter_pricing_table']] != '' ) {
                    $foodbakery_bareber_pricing_table .= 'pricing_table_title="' . htmlspecialchars( $data['pricing_table_title'][$counters['foodbakery_counter_pricing_table']], ENT_QUOTES ) . '" ';
                }
                 if ( isset( $data['foodbakery_var_pricing_table_align'][$counters['foodbakery_counter_pricing_table']] ) && $data['foodbakery_var_pricing_table_align'][$counters['foodbakery_counter_pricing_table']] != '' ) {
                    $foodbakery_bareber_pricing_table .= 'foodbakery_var_pricing_table_align="' . htmlspecialchars( $data['foodbakery_var_pricing_table_align'][$counters['foodbakery_counter_pricing_table']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['pricing_table_subtitle'][$counters['foodbakery_counter_pricing_table']] ) && $data['pricing_table_subtitle'][$counters['foodbakery_counter_pricing_table']] != '' ) {
                    $foodbakery_bareber_pricing_table .= 'pricing_table_subtitle="' . htmlspecialchars( $data['pricing_table_subtitle'][$counters['foodbakery_counter_pricing_table']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['foodbakery_pricing_tables'][$counters['foodbakery_counter_pricing_table']] ) && $data['foodbakery_pricing_tables'][$counters['foodbakery_counter_pricing_table']] != '' ) {
                    $foodbakery_bareber_pricing_table .= 'foodbakery_pricing_tables="' . htmlspecialchars( $data['foodbakery_pricing_tables'][$counters['foodbakery_counter_pricing_table']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['pricing_table_view'][$counters['foodbakery_counter_pricing_table']] ) && $data['pricing_table_view'][$counters['foodbakery_counter_pricing_table']] != '' ) {
                    $foodbakery_bareber_pricing_table .= 'pricing_table_view="' . htmlspecialchars( $data['pricing_table_view'][$counters['foodbakery_counter_pricing_table']], ENT_QUOTES ) . '" ';
                }

                $foodbakery_bareber_pricing_table .= ']';
                if ( isset( $data['pricing_table_column_text'][$counters['foodbakery_counter_pricing_table']] ) && $data['pricing_table_column_text'][$counters['foodbakery_counter_pricing_table']] != '' ) {
                    $foodbakery_bareber_pricing_table .= htmlspecialchars( $data['pricing_table_column_text'][$counters['foodbakery_counter_pricing_table']], ENT_QUOTES ) . ' ';
                }
                $foodbakery_bareber_pricing_table .= '[/pricing_table]';

                $shortcode_data .= $foodbakery_bareber_pricing_table;
                $counters['foodbakery_counter_pricing_table'] ++;
            }
            $counters['foodbakery_global_counter_pricing_table'] ++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter( 'foodbakery_save_page_builder_data_pricing_table', 'foodbakery_save_page_builder_data_pricing_table_callback' );
}

if ( ! function_exists( 'foodbakery_load_shortcode_counters_pricing_table_callback' ) ) {

    /**
     * Populate pricing_table shortcode counter variables.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_load_shortcode_counters_pricing_table_callback( $counters ) {
        $counters['foodbakery_global_counter_pricing_table'] = 0;
        $counters['foodbakery_shortcode_counter_pricing_table'] = 0;
        $counters['foodbakery_counter_pricing_table'] = 0;
        return $counters;
    }

    add_filter( 'foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_pricing_table_callback' );
}



if ( ! function_exists( 'foodbakery_element_list_populate_pricing_table_callback' ) ) {

    /**
     * Populate pricing_table shortcode strings list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_element_list_populate_pricing_table_callback( $element_list ) {
        $element_list['pricing_table'] = 'Pricing Plan';
        return $element_list;
    }

    add_filter( 'foodbakery_element_list_populate', 'foodbakery_element_list_populate_pricing_table_callback' );
}

if ( ! function_exists( 'foodbakery_shortcode_names_list_populate_pricing_table_callback' ) ) {

    /**
     * Populate pricing_table shortcode names list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_shortcode_names_list_populate_pricing_table_callback( $shortcode_array ) {
        $shortcode_array['pricing_table'] = array(
            'title' => 'FB: Price Plan',
            'name' => 'pricing_table',
            'icon' => 'icon-price-tags',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter( 'foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_pricing_table_callback' );
}
