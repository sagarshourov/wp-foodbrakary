<?php
/**
 * Shortcode Name : statics
 *
 * @package	foodbakery 
 */
if ( ! function_exists( 'foodbakery_var_page_builder_statics' ) ) {

    function foodbakery_var_page_builder_statics( $die = 0 ) {
        global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if ( function_exists( 'foodbakery_shortcode_names' ) ) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'statics';

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
                'statics_title' => '',
                'statics_subtitle' => '',
                'foodbakery_types' => '',
                'foodbakery_var_statics_align' => '',
		'foodbakery_statics_text_color'=>'',
            );
            if ( isset( $foodbakery_output['0']['atts'] ) ) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if ( isset( $foodbakery_output['0']['content'] ) ) {
                $statics_column_text = $foodbakery_output['0']['content'];
            } else {
                $statics_column_text = '';
            }
            $statics_element_size = '100';
            foreach ( $defaults as $key => $values ) {
                if ( isset( $atts[$key] ) ) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_statics';
            $coloumn_class = 'column_' . $statics_element_size;
            if ( isset( $_POST['shortcode_element'] ) && $_POST['shortcode_element'] == 'shortcode' ) {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            foodbakery_var_date_picker();
            ?>

            <div id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>_del" class="column  parentdelete <?php echo esc_attr( $coloumn_class ); ?>
                 <?php echo esc_attr( $shortcode_view ); ?>" item="statics" data="<?php echo foodbakery_element_size_data_array_index( $statics_element_size ) ?>" >
                     <?php foodbakery_element_setting( $name, $foodbakery_counter, $statics_element_size ) ?>
                <div class="cs-wrapp-class-<?php echo intval( $foodbakery_counter ) ?>
                     <?php echo esc_attr( $shortcode_element ); ?>" id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>" data-shortcode-template="[statics {{attributes}}]{{content}}[/statics]" style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr( $foodbakery_counter ) ?>">
                        <h5><?php echo foodbakery_var_frame_text_srt( 'foodbakery_var_edit_statics_page' ) ?></h5>
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
                            
                            $location_obj = get_terms( 'restaurant-category', array(
                                'hide_empty' => false,
                                    ) );
                            $foodbakery_cat_list = array();
                            if ( is_array( $location_obj ) && sizeof( $location_obj ) > 0 ) {
                                foreach ( $location_obj as $dir_cat ) {
                                    $foodbakery_cat_list[$dir_cat->slug] = $dir_cat->name;
                                }
                            }
                            $foodbakery_opt_array = array(
                                'name' => esc_html__( 'Element Title', 'foodbakery' ),
                                'desc' => '',
                                'hint_text' => __( "Enter element title here.", "foodbakery" ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $statics_title,
                                    'id' => 'statics_title',
                                    'cust_name' => 'statics_title[]',
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
                                    'std' => $statics_subtitle,
                                    'id' => 'statics_subtitle',
                                    'cust_name' => 'statics_subtitle[]',
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
                                    'std' => $foodbakery_var_statics_align,
                                    'id' => '',
                                    'cust_id' => 'foodbakery_var_statics_align',
                                    'cust_name' => 'foodbakery_var_statics_align[]',
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
				    'name' => esc_html('Text Color'),
				    'desc' => '',
				    'hint_text' => esc_html('Enter text color'),
				    'echo' => true,
				    'field_params' => array(
					'std' => esc_attr($foodbakery_statics_text_color),
					'cust_id' => 'foodbakery_statics_text_color',
					'classes' => 'bg_color',
					'cust_name' => 'foodbakery_statics_text_color[]',
					'return' => true,
				    ),
				);
				$foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
			    


                            if ( function_exists( 'foodbakery_shortcode_custom_classes_test' ) ) {
                                foodbakery_shortcode_custom_dynamic_classes( $statics_custom_class, $statics_custom_animation, '', 'statics' );
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
                                'std' => 'statics',
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
                                    'cust_id' => 'statics_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn',
                                    'cust_name' => 'statics_save',
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

    add_action( 'wp_ajax_foodbakery_var_page_builder_statics', 'foodbakery_var_page_builder_statics' );
}

if ( ! function_exists( 'foodbakery_save_page_builder_data_statics_callback' ) ) {

    /**
     * Save data for statics shortcode.
     *
     * @param	array $args
     * @return	array
     */
    function foodbakery_save_page_builder_data_statics_callback( $args ) {

        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        $shortcode_data ='';
        if ( $widget_type == "statics" || $widget_type == "cs_statics" ) {
            $restaurant_types = isset( $data['foodbakery_types'] ) ? $data['foodbakery_types'] : '';
            $types_lists = '';
            if ( is_array( $restaurant_types ) ) {
                foreach ( $restaurant_types as $restaurant_typ ) {
                    $types_lists .=$restaurant_typ . ',';
                }
            }

            $foodbakery_bareber_statics = '';
            $page_element_size     =  $data['statics_element_size'][$counters['foodbakery_global_counter_statics']];
            $current_element_size  =  $data['statics_element_size'][$counters['foodbakery_global_counter_statics']];
            
            if ( isset( $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] ) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode' ) {
                $shortcode_str = stripslashes( ( $data['shortcode']['statics'][$counters['foodbakery_shortcode_counter_statics']] ));
                
                $element_settings   = 'statics_element_size="'.$current_element_size.'"';
                $reg = '/statics_element_size="(\d+)"/s';
                $shortcode_str  = preg_replace( $reg, $element_settings, $shortcode_str );
                $shortcode_data .= $shortcode_str;
                $foodbakery_bareber_statics ++;
            } else {
                $element_settings   = 'statics_element_size="'.htmlspecialchars( $data['statics_element_size'][$counters['foodbakery_global_counter_statics']] ).'"';
                $foodbakery_bareber_statics = '[statics '.$element_settings.' ';
                if ( isset( $data['statics_title'][$counters['foodbakery_counter_statics']] ) && $data['statics_title'][$counters['foodbakery_counter_statics']] != '' ) {
                    $foodbakery_bareber_statics .= 'statics_title="' . htmlspecialchars( $data['statics_title'][$counters['foodbakery_counter_statics']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['foodbakery_var_statics_align'][$counters['foodbakery_counter_statics']] ) && $data['foodbakery_var_statics_align'][$counters['foodbakery_counter_statics']] != '' ) {
                    $foodbakery_bareber_statics .= 'foodbakery_var_statics_align="' . htmlspecialchars( $data['foodbakery_var_statics_align'][$counters['foodbakery_counter_statics']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['statics_subtitle'][$counters['foodbakery_counter_statics']] ) && $data['statics_subtitle'][$counters['foodbakery_counter_statics']] != '' ) {
                    $foodbakery_bareber_statics .= 'statics_subtitle="' . htmlspecialchars( $data['statics_subtitle'][$counters['foodbakery_counter_statics']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['foodbakery_statics_text_color'][$counters['foodbakery_counter_statics']] ) && $data['foodbakery_statics_text_color'][$counters['foodbakery_counter_statics']] != '' ) {
                    $foodbakery_bareber_statics .= 'foodbakery_statics_text_color="' . htmlspecialchars( $data['foodbakery_statics_text_color'][$counters['foodbakery_counter_statics']], ENT_QUOTES ) . '" ';
                }
		
                $foodbakery_bareber_statics .= ']';

                if ( isset( $data['statics_column_text'][$counters['foodbakery_counter_statics']] ) && $data['statics_column_text'][$counters['foodbakery_counter_statics']] != '' ) {
                    $foodbakery_bareber_statics .= htmlspecialchars( $data['statics_column_text'][$counters['foodbakery_counter_statics']], ENT_QUOTES ) . ' ';
                }
                $foodbakery_bareber_statics .= '[/statics]';
                
                $shortcode_data .= $foodbakery_bareber_statics;
                $counters['foodbakery_counter_statics'] ++;
            }
            $counters['foodbakery_global_counter_statics'] ++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter( 'foodbakery_save_page_builder_data_statics', 'foodbakery_save_page_builder_data_statics_callback' );
}

if ( ! function_exists( 'foodbakery_load_shortcode_counters_statics_callback' ) ) {

    /**
     * Populate statics shortcode counter variables.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_load_shortcode_counters_statics_callback( $counters ) {
        $counters['foodbakery_global_counter_statics'] = 0;
        $counters['foodbakery_shortcode_counter_statics'] = 0;
        $counters['foodbakery_counter_statics'] = 0;
        return $counters;
    }

    add_filter( 'foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_statics_callback' );
}



if ( ! function_exists( 'foodbakery_element_list_populate_statics_callback' ) ) {

    /**
     * Populate statics shortcode strings list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_element_list_populate_statics_callback( $element_list ) {
        $element_list['statics'] = 'Statics';
        return $element_list;
    }

    add_filter( 'foodbakery_element_list_populate', 'foodbakery_element_list_populate_statics_callback' );
}

if ( ! function_exists( 'foodbakery_shortcode_names_list_populate_statics_callback' ) ) {

    /**
     * Populate statics shortcode names list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_shortcode_names_list_populate_statics_callback( $shortcode_array ) {
        $shortcode_array['statics'] = array(
            'title' => 'FB: Statics',
            'name' => 'statics',
            'icon' => 'icon-gears',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    add_filter( 'foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_statics_callback' );
}
