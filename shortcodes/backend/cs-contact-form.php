<?php
/*
 *
 * @File : Contact Us Short Code
 * @retrun
 *
 */

if ( ! function_exists( 'foodbakery_var_page_builder_contact_form' ) ) {

    function foodbakery_var_page_builder_contact_form( $die = 0 ) {
        global $post, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_static_text;

        if ( function_exists( 'foodbakery_shortcode_names' ) ) {

            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $FOODBAKERY_PREFIX = 'foodbakery_contact_form';
            $counter = isset( $_POST['counter'] ) ? $_POST['counter'] : '';
            $foodbakery_counter = isset( $_POST['counter'] ) ? $_POST['counter'] : '';
            if ( isset( $_POST['action'] ) && ! isset( $_POST['shortcode_element_id'] ) ) {
                $FOODBAKERY_POSTID = '';
                $shortcode_element_id = '';
            } else {
                $FOODBAKERY_POSTID = isset( $_POST['POSTID'] ) ? $_POST['POSTID'] : '';
                $shortcode_element_id = isset( $_POST['shortcode_element_id'] ) ? $_POST['shortcode_element_id'] : '';
                $shortcode_str = stripslashes( $shortcode_element_id );
                $parseObject = new ShortcodeParse();
                $foodbakery_output = $parseObject->foodbakery_shortcodes( $foodbakery_output, $shortcode_str, true, $FOODBAKERY_PREFIX );
            }
            $defaults = array(
                'foodbakery_var_contact_us_element_title' => '',
                'foodbakery_var_contact_us_element_send' => '',
                'foodbakery_var_contact_us_element_success' => '',
                'foodbakery_var_contact_us_element_error' => '',
                'foodbakery_var_text_us' => '',
                'foodbakery_var_call_us' => '',
                'foodbakery_var_address' => '',
                'foodbakery_var_form_title' => '',
                'foodbakery_var_contact_align' => '',
            );
            if ( isset( $foodbakery_output['0']['atts'] ) ) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if ( isset( $foodbakery_output['0']['content'] ) ) {
                $contact_us_text = $foodbakery_output['0']['content'];
            } else {
                $contact_us_text = '';
            }
            $contact_form_element_size = '25';
            foreach ( $defaults as $key => $values ) {
                if ( isset( $atts[$key] ) ) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_contact_form';
            $coloumn_class = 'column_' . $contact_form_element_size;
            $foodbakery_var_contact_us_element_title = isset( $foodbakery_var_contact_us_element_title ) ? $foodbakery_var_contact_us_element_title : '';
            $foodbakery_var_contact_us_element_send = isset( $foodbakery_var_contact_us_element_send ) ? $foodbakery_var_contact_us_element_send : '';
            $foodbakery_var_contact_us_element_success = isset( $foodbakery_var_contact_us_element_success ) ? $foodbakery_var_contact_us_element_success : '';
            $foodbakery_var_contact_us_element_error = isset( $foodbakery_var_contact_us_element_error ) ? $foodbakery_var_contact_us_element_error : '';
            $foodbakery_var_contact_align = isset($foodbakery_var_contact_align) ? $foodbakery_var_contact_align : '';

            if ( isset( $_POST['shortcode_element'] ) && $_POST['shortcode_element'] == 'shortcode' ) {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            $strings = new foodbakery_theme_all_strings;
            $strings->foodbakery_short_code_strings();
            ?>
            <div id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>_del" class="column  parentdelete <?php echo esc_attr( $coloumn_class ); ?>
                 <?php echo esc_attr( $shortcode_view ); ?>" item="contact_form" data="<?php echo foodbakery_element_size_data_array_index( $contact_form_element_size ) ?>" >
                     <?php foodbakery_element_setting( $name, $foodbakery_counter, $contact_form_element_size ) ?>
                <div class="cs-wrapp-class-<?php echo intval( $foodbakery_counter ) ?>
                     <?php echo esc_attr( $shortcode_element ); ?>" id="<?php echo esc_attr( $name . $foodbakery_counter ) ?>" data-shortcode-template="[foodbakery_contact_form {{attributes}}]{{content}}[/foodbakery_contact_form]" style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr( $foodbakery_counter ) ?>">
                        <h5><?php echo esc_html( foodbakery_plugin_text_srt( 'foodbakery_var_edit_form' ) ); ?></h5>
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
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_element_title' ),
                                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_element_title_hint' ),
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_contact_us_element_title ),
                                    'cust_id' => 'foodbakery_var_contact_us_element_title' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_contact_us_element_title[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
                            
                            $foodbakery_opt_array = array(
                                    'name' => foodbakery_plugin_text_srt( 'foodbakery_var_title_alignment' ),
                                    'desc' => '',
                                    'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_title_alignment_hint' ),
                                    'echo' => true,
                                    'field_params' => array(
                                        'std' => $foodbakery_var_contact_align,
                                        'id' => '',
                                        'cust_id' => 'foodbakery_var_contact_align',
                                        'cust_name' => 'foodbakery_var_contact_align[]',
                                        'classes' => 'service_postion chosen-select-no-single select-medium',
                                        'options' => array(
                                            'align-left' => foodbakery_plugin_text_srt( 'foodbakery_var_align_left' ),
                                            'align-right' => foodbakery_plugin_text_srt( 'foodbakery_var_align_right' ),
                                            'align-center' => foodbakery_plugin_text_srt( 'foodbakery_var_align_center' ),
                                        ),
                                        'return' => true,
                                    ),
                                );
                                $foodbakery_var_html_fields->foodbakery_var_select_field( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_text_us' ),
                               // 'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_text_us_hint' ),
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_text_us ),
                                    'cust_id' => 'foodbakery_var_text_us' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_text_us[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
                            
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_call_us' ),
                                //'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_call_us_hint' ),
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_call_us ),
                                    'cust_id' => 'foodbakery_var_call_us' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_call_us[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
                            
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_address_contact' ),
                                //'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_address_contact_hint' ),
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_address ),
                                    'cust_id' => 'foodbakery_var_address' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_address[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
                            
                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_form_title_contact' ),
                                //'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_form_title_contact_hint' ),
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_form_title ),
                                    'cust_id' => 'foodbakery_var_form_title' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_form_title[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_send_to' ),
                                'desc' => '',
                                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_send_to_hint' ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_contact_us_element_send ),
                                    'cust_id' => 'foodbakery_var_contact_us_element_send' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_contact_us_element_send[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_success_message' ),
                                'desc' => '',
                                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_success_message_hint' ),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_contact_us_element_success ),
                                    'cust_id' => 'foodbakery_var_contact_us_element_success' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_contact_us_element_success[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );

                            $foodbakery_opt_array = array(
                                'name' => foodbakery_plugin_text_srt( 'foodbakery_var_error_message' ),
                                'hint_text' => foodbakery_plugin_text_srt( 'foodbakery_var_error_message_hint' ),
                                'desc' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => esc_attr( $foodbakery_var_contact_us_element_error ),
                                    'cust_id' => 'foodbakery_var_contact_us_element_error' . $foodbakery_counter,
                                    'classes' => '',
                                    'cust_name' => 'foodbakery_var_contact_us_element_error[]',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
                            ?>
                        </div>
                        <?php if ( isset( $_POST['shortcode_element'] ) && $_POST['shortcode_element'] == 'shortcode' ) { ?>
                            <ul class="form-elements insert-bg">
                                <li class="to-field">
                                    <a class="insert-btn cs-main-btn" onclick="javascript:foodbakery_shortcode_insert_editor('<?php echo str_replace( 'foodbakery_var_page_builder_', '', $name ); ?>', '<?php echo esc_js( $name . $foodbakery_counter ) ?>', '<?php echo esc_js( $filter_element ); ?>')" ><?php echo esc_html( foodbakery_plugin_text_srt( 'foodbakery_var_insert' ) ); ?></a>
                                </li>
                            </ul>
                            <div id="results-shortocde"></div>
                            <?php
                        } else {
                            $foodbakery_opt_array = array(
                                'std' => 'contact_form',
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
                                    'std' => foodbakery_plugin_text_srt( 'foodbakery_var_save' ),
                                    'cust_id' => 'contact_form_save' . $foodbakery_counter,
                                    'cust_type' => 'button',
                                    'classes' => '',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'cust_name' => 'contact_from_save',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_var_html_fields->foodbakery_var_text_field( $foodbakery_opt_array );
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }

        if ( $die <> 1 ) {
            die();
        }
    }

    add_action( 'wp_ajax_foodbakery_var_page_builder_contact_form', 'foodbakery_var_page_builder_contact_form' );
}

if ( ! function_exists( 'foodbakery_save_page_builder_data_contact_form_callback' ) ) {

    /**
     * Save data for contact_form shortcode.
     *
     * @param	array $args
     * @return	array
     */
    function foodbakery_save_page_builder_data_contact_form_callback( $args ) {

        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        if ( $widget_type == "contact_form" || $widget_type == "cs_contact_form" ) {
            $shortcode = '';

            $page_element_size = $data['contact_form_element_size'][$counters['foodbakery_global_counter_contact_us']];
            $contact_element_size = $data['contact_form_element_size'][$counters['foodbakery_global_counter_contact_us']];

            if ( isset( $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] ) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode' ) {
                $shortcode_str = stripslashes( ( $data['shortcode']['contact_form'][$counters['foodbakery_shortcode_counter_contact_us']] ) );

                $element_settings = 'contact_form_element_size="' . $contact_element_size . '"';
                $reg = '/contact_form_element_size="(\d+)"/s';
                $shortcode_str = preg_replace( $reg, $element_settings, $shortcode_str );
                $shortcode_data .= $shortcode_str;
                $counters['foodbakery_shortcode_counter_contact_us'] ++;
            } else {
                $shortcode = '[foodbakery_contact_form contact_form_element_size="' . htmlspecialchars( $data['contact_form_element_size'][$counters['foodbakery_global_counter_contact_us']] ) . '" ';
                if ( isset( $data['foodbakery_var_contact_us_element_title'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_contact_us_element_title'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_contact_us_element_title="' . stripslashes( htmlspecialchars( ($data['foodbakery_var_contact_us_element_title'][$counters['foodbakery_counter_contact_us']] ), ENT_QUOTES ) ) . '" ';
                }
                if ( isset( $data['foodbakery_var_contact_align'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_contact_align'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_contact_align="' . stripslashes( htmlspecialchars( ($data['foodbakery_var_contact_align'][$counters['foodbakery_counter_contact_us']] ), ENT_QUOTES ) ) . '" ';
                }
                if ( isset( $data['foodbakery_var_text_us'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_text_us'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_text_us="' . stripslashes( htmlspecialchars( ($data['foodbakery_var_text_us'][$counters['foodbakery_counter_contact_us']] ), ENT_QUOTES ) ) . '" ';
                }
                if ( isset( $data['foodbakery_var_call_us'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_call_us'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_call_us="' . stripslashes( htmlspecialchars( ($data['foodbakery_var_call_us'][$counters['foodbakery_counter_contact_us']] ), ENT_QUOTES ) ) . '" ';
                }
                if ( isset( $data['foodbakery_var_address'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_address'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_address="' . stripslashes( htmlspecialchars( ($data['foodbakery_var_address'][$counters['foodbakery_counter_contact_us']] ), ENT_QUOTES ) ) . '" ';
                }
                if ( isset( $data['foodbakery_var_form_title'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_form_title'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_form_title="' . stripslashes( htmlspecialchars( ($data['foodbakery_var_form_title'][$counters['foodbakery_counter_contact_us']] ), ENT_QUOTES ) ) . '" ';
                }
                if ( isset( $data['foodbakery_var_contact_us_element_send'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_contact_us_element_send'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_contact_us_element_send="' . htmlspecialchars( $data['foodbakery_var_contact_us_element_send'][$counters['foodbakery_counter_contact_us']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['foodbakery_var_contact_us_element_success'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_contact_us_element_success'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_contact_us_element_success="' . htmlspecialchars( $data['foodbakery_var_contact_us_element_success'][$counters['foodbakery_counter_contact_us']], ENT_QUOTES ) . '" ';
                }
                if ( isset( $data['foodbakery_var_contact_us_element_error'][$counters['foodbakery_counter_contact_us']] ) && $data['foodbakery_var_contact_us_element_error'][$counters['foodbakery_counter_contact_us']] != '' ) {
                    $shortcode .= 'foodbakery_var_contact_us_element_error="' . htmlspecialchars( $data['foodbakery_var_contact_us_element_error'][$counters['foodbakery_counter_contact_us']], ENT_QUOTES ) . '" ';
                }
                $shortcode .= ']';
                $shortcode .= '[/foodbakery_contact_form]';
                $shortcode_data .= $shortcode;
                $counters['foodbakery_counter_contact_us'] ++;
            }
            $counters['foodbakery_global_counter_contact_us'] ++;
        }
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter( 'foodbakery_save_page_builder_data_contact_form', 'foodbakery_save_page_builder_data_contact_form_callback' );
}

if ( ! function_exists( 'foodbakery_load_shortcode_counters_contact_form_callback' ) ) {

    /**
     * Populate contact_form shortcode counter variables.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_load_shortcode_counters_contact_form_callback( $counters ) {
        $counters['foodbakery_global_counter_contact_us'] = 0;
        $counters['foodbakery_shortcode_counter_contact_us'] = 0;
        $counters['foodbakery_counter_contact_us'] = 0;
        return $counters;
    }

    add_filter( 'foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_contact_form_callback' );
}

if ( ! function_exists( 'foodbakery_shortcode_names_list_populate_contact_form_callback' ) ) {

    /**
     * Populate contact form shortcode names list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_shortcode_names_list_populate_contact_form_callback( $shortcode_array ) {
        $shortcode_array['contact_form'] = array(
            'title' => foodbakery_var_frame_text_srt( 'foodbakery_var_contact_form' ),
            'name' => 'contact_form',
            'icon' => 'icon-building-o',
            'categories' => 'typography',
        );
        return $shortcode_array;
    }

    add_filter( 'foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_contact_form_callback' );
}

if ( ! function_exists( 'foodbakery_element_list_populate_contact_form_callback' ) ) {

    /**
     * Populate contact form shortcode strings list.
     *
     * @param	array $counters
     * @return	array
     */
    function foodbakery_element_list_populate_contact_form_callback( $element_list ) {
        $element_list['contact_form'] = foodbakery_var_frame_text_srt( 'foodbakery_var_contact_form' );
        return $element_list;
    }

    add_filter( 'foodbakery_element_list_populate', 'foodbakery_element_list_populate_contact_form_callback' );
}