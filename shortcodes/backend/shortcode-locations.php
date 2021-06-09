<?php
/**
 * Shortcode Name : locations
 *
 * @package	foodbakery 
 */
if ( ! function_exists( 'foodbakery_var_page_builder_locations' ) ) {

    function foodbakery_var_page_builder_locations( $die = 0 ) {
        global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if ( function_exists( 'foodbakery_shortcode_names' ) ) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'locations';

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
                'locations_title' => '',
                'locations_subtitle' => '',
                'locations_button_url' => '',
                'locations_show' => '',
                'foodbakery_location' => '',
                'locations_more_less' => '',
                'foodbakery_var_location_align' => '',
		'foodbakery_var_location_style' => '',
            );
            if ( isset( $foodbakery_output['0']['atts'] ) ) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if ( isset( $foodbakery_output['0']['content'] ) ) {
                $locations_column_text = $foodbakery_output['0']['content'];
            } else {
                $locations_column_text = '';
            }
            $locations_element_size = '100';
            foreach ( $defaults as $key => $values ) {
                if ( isset( $atts[$key] ) ) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_locations';
            $coloumn_class = 'column_' . $locations_element_size;
            if ( isset( $_POST['shortcode_element'] ) && $_POST['shortcode_element'] == 'shortcode' ) {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            foodbakery_var_date_picker();
            ?>

            <div id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>_del" class="column  parentdelete <?php echo esc_attr( $coloumn_class ); ?>
                 <?php echo esc_attr( $shortcode_view ); ?>" item="locations" data="<?php echo foodbakery_element_size_data_array_index( $locations_element_size ) ?>" >
                     <?php foodbakery_element_setting( $name, $foodbakery_counter, $locations_element_size ) ?>
                <div class="cs-wrapp-class-<?php echo intval( $foodbakery_counter ) ?>
                     <?php echo esc_attr( $shortcode_element ); ?>" id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>" data-shortcode-template="[locations {{attributes}}]{{content}}[/locations]" style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr( $foodbakery_counter ) ?>">
                        <h5><?php echo foodbakery_var_frame_text_srt( 'foodbakery_var_edit_locations_page' ) ?></h5>
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

                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'Element Title', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Enter element title here.", "foodbakery" ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $locations_title,
                                    'id' => 'locations_title',
                                    'cust_name' => 'locations_title[]',
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
                                    'std' => $locations_subtitle,
                                    'id' => 'locations_subtitle',
                                    'cust_name' => 'locations_subtitle[]',
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
                                        'std' => $foodbakery_var_location_align,
                                        'id' => '',
                                        'cust_id' => 'foodbakery_var_location_align',
                                        'cust_name' => 'foodbakery_var_location_align[]',
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
                                    'name' => __( 'Style', 'foodbakery' ),
                                    'desc' => '',
                                    'hint_text' => __( 'Set element Style here', 'foodbakery' ),
                                    'echo' => true,
                                    'field_params' => array(
                                        'std' => $foodbakery_var_location_style,
                                        'id' => '',
                                        'cust_id' => 'foodbakery_var_location_style',
                                        'cust_name' => 'foodbakery_var_location_style[]',
                                        'classes' => 'service_postion chosen-select-no-single select-medium',
                                        'options' => array(
                                            'default' => __( 'Default', 'foodbakery' ),
                                            'fancy' => __( 'Fancy', 'foodbakery' ),
                                             'modern' => __( 'Modern', 'foodbakery' ),
                                        ),
                                        'return' => true,
                                    ),
                                );
                                $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );

                            $location_obj = get_terms( 'foodbakery_locations', array(
                                'hide_empty' => false,
                                    ) );
                            $foodbakery_loc_list = array();
                            if ( is_array( $location_obj ) && sizeof( $location_obj ) > 0 ) {
                                foreach ( $location_obj as $dir_loc ) {
                                    $foodbakery_loc_list[$dir_loc->slug] = $dir_loc->name;
                                }
                            }
                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'Restaurant Locations', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Select Restaurant locations to show.", "foodbakery" ),
                                'echo' => true,
                                'multi' => true,
                                'desc' => '',
                                'field_params' => array(
                                    'std' => $foodbakery_location,
                                    'id' => 'foodbakery_location',
                                    'cust_name' => 'foodbakery_location[]',
                                    'classes' => 'chosen-select-no-single',
                                    'options' => $foodbakery_loc_list,
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_select_field( $foodbakery_opt_array );
                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'Buton Link', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Enter button url here.", "foodbakery" ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $locations_button_url,
                                    'id' => 'locations_button_url',
                                    'cust_name' => 'locations_button_url[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field( $foodbakery_opt_array );



                            if ( function_exists( 'foodbakery_shortcode_custom_classes_test' ) ) {
                                foodbakery_shortcode_custom_dynamic_classes( $locations_custom_class, $locations_custom_animation, '', 'locations' );
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
                                'std' => 'locations',
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
                                    'cust_id' => 'locations_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn',
                                    'cust_name' => 'locations_save',
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

    add_action( 'wp_ajax_foodbakery_var_page_builder_locations', 'foodbakery_var_page_builder_locations' );
}

if ( ! function_exists( 'foodbakery_save_page_builder_data_locations_callback' ) ) {

    /**
     * Save data for locations shortcode.
     *
     * @param	array $args
     * @return	array
     */
    function foodbakery_save_page_builder_data_locations_callback( $args ) {

        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $shortcode_data ='';
        $column = $args['column'];
        if ( $widget_type == "locations" || $widget_type == "cs_locations" ) {
            $restaurant_types = isset( $data['foodbakery_location'] ) ? $data['foodbakery_location'] : '';
            $types_lists = '';
            if ( is_array( $restaurant_types ) ) {
                foreach ( $restaurant_types as $restaurant_typ ) {
                    $types_lists .=$restaurant_typ . ',';
                }
            }


            $foodbakery_bareber_locations = '';
            
            $page_element_size     =  $data['locations_element_size'][$counters['foodbakery_global_counter_locations']];
            $current_element_size  =  $data['locations_element_size'][$counters['foodbakery_global_counter_locations']];
            
            if ( isset( $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] ) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode' ) {
                $shortcode_str = stripslashes( ( $data['shortcode']['locations'][$counters['foodbakery_shortcode_counter_locations']] ));
                
                $element_settings   = 'locations_element_size="'.$current_element_size.'"';
                $reg = '/locations_element_size="(\d+)"/s';
                $shortcode_str  = preg_replace( $reg, $element_settings, $shortcode_str );
                $shortcode_data .= $shortcode_str;
                $foodbakery_bareber_locations ++;
            } else {
                $element_settings   = 'locations_element_size="'.htmlspecialchars( $data['locations_element_size'][$counters['foodbakery_global_counter_locations']] ).'"';
                $foodbakery_bareber_locations = '[locations '.$element_settings.' ';
                if ( isset( $data['locations_title'][$counters['foodbakery_counter_locations']] ) && $data['locations_title'][$counters['foodbakery_counter_locations']] != '' ) {
                    $foodbakery_bareber_locations .= 'locations_title="' . htmlspecialchars( $data['locations_title'][$counters['foodbakery_counter_locations']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['foodbakery_var_location_align'][$counters['foodbakery_counter_locations']] ) && $data['foodbakery_var_location_align'][$counters['foodbakery_counter_locations']] != '' ) {
                    $foodbakery_bareber_locations .= 'foodbakery_var_location_align="' . htmlspecialchars( $data['foodbakery_var_location_align'][$counters['foodbakery_counter_locations']], ENT_QUOTES ) . '" ';
                }
		 if ( isset( $data['foodbakery_var_location_style'][$counters['foodbakery_counter_locations']] ) && $data['foodbakery_var_location_style'][$counters['foodbakery_counter_locations']] != '' ) {
                    $foodbakery_bareber_locations .= 'foodbakery_var_location_style="' . htmlspecialchars( $data['foodbakery_var_location_style'][$counters['foodbakery_counter_locations']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['locations_subtitle'][$counters['foodbakery_counter_locations']] ) && $data['locations_subtitle'][$counters['foodbakery_counter_locations']] != '' ) {
                    $foodbakery_bareber_locations .= 'locations_subtitle="' . htmlspecialchars( $data['locations_subtitle'][$counters['foodbakery_counter_locations']], ENT_QUOTES ) . '" ';
                }

                if ( isset( $data['locations_button_url'][$counters['foodbakery_counter_locations']] ) && $data['locations_button_url'][$counters['foodbakery_counter_locations']] != '' ) {
                    $foodbakery_bareber_locations .= 'locations_button_url="' . htmlspecialchars( $data['locations_button_url'][$counters['foodbakery_counter_locations']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['foodbakery_location'][$counters['foodbakery_counter_locations']] ) && $data['foodbakery_location'][$counters['foodbakery_counter_locations']] != '' ) {
                    $foodbakery_bareber_locations .= 'foodbakery_location="' . $types_lists . '" ';
                }



                $foodbakery_bareber_locations .= ']';

                if ( isset( $data['locations_column_text'][$counters['foodbakery_counter_locations']] ) && $data['locations_column_text'][$counters['foodbakery_counter_locations']] != '' ) {
                    $foodbakery_bareber_locations .= htmlspecialchars( $data['locations_column_text'][$counters['foodbakery_counter_locations']], ENT_QUOTES ) . ' ';
                }
                $foodbakery_bareber_locations .= '[/locations]';

                $shortcode_data .= $foodbakery_bareber_locations;
                $counters['foodbakery_counter_locations'] ++;
            }
            $counters['foodbakery_global_counter_locations'] ++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter( 'foodbakery_save_page_builder_data_locations', 'foodbakery_save_page_builder_data_locations_callback' );
}

if ( ! function_exists( 'foodbakery_load_shortcode_counters_locations_callback' ) ) {

    /**
     * Populate locations shortcode counter variables.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_load_shortcode_counters_locations_callback( $counters ) {
        $counters['foodbakery_global_counter_locations'] = 0;
        $counters['foodbakery_shortcode_counter_locations'] = 0;
        $counters['foodbakery_counter_locations'] = 0;
        return $counters;
    }

    add_filter( 'foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_locations_callback' );
}



if ( ! function_exists( 'foodbakery_element_list_populate_locations_callback' ) ) {

    /**
     * Populate locations shortcode strings list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_element_list_populate_locations_callback( $element_list ) {
        $element_list['locations'] = 'Locations';
        return $element_list;
    }

    add_filter( 'foodbakery_element_list_populate', 'foodbakery_element_list_populate_locations_callback' );
}

if ( ! function_exists( 'foodbakery_shortcode_names_list_populate_locations_callback' ) ) {

    /**
     * Populate locations shortcode names list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_shortcode_names_list_populate_locations_callback( $shortcode_array ) {
        $shortcode_array['locations'] = array(
            'title' => 'FB: Locations',
            'name' => 'locations',
            'icon' => 'icon-location',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter( 'foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_locations_callback' );
}
