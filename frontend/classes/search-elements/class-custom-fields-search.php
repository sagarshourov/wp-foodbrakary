<?php
if(!class_exists('Custom_Fields_Search')){
    Class Custom_Fields_Search{
        public function __construct(){
            add_action('foodbakery_search_custom_fields',array($this,'foodbakery_search_custom_fields'));
        }
        public function foodbakery_search_custom_fields(){
            $cs_job_cus_fields = get_option("cs_job_cus_fields");
        if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {
            ?>
            <a class="cs-expand-filters"><i class="icon-minus8"></i> <?php esc_html_e('Collapse all Filters', 'foodbakery') ?></a>
            <div class="accordion" id="accordion2">
                <?php
                $custom_field_flag = 11;
                foreach ($cs_job_cus_fields as $cus_fieldvar => $cus_field) {
                    $all_item_empty = 0;
                    if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value'])) {
                        foreach ($cus_field['options']['value'] as $cus_field_options_value) {

                            if ($cus_field_options_value != '') {
                                $all_item_empty = 0;
                                break;
                            } else {
                                $all_item_empty = 1;
                            }
                        }
                    }
                    if (isset($cus_field['enable_srch']) && $cus_field['enable_srch'] == 'on' && ($all_item_empty == 0)) {
                        $query_str_var_name = $cus_field['meta_key'];
                        $collapse_condition = 'off';
                        if (isset($cus_field['collapse_search'])) {
                            $collapse_condition = $cus_field['collapse_search'];
                        }
                        $count_filtration = $cus_fields_count_arr;
                        $filter_new_arr = '';
                        if (isset($count_filtration[$query_str_var_name])) {
                            unset($count_filtration[$query_str_var_name]);
                            $filter_temp_arr = $count_filtration;

                            foreach ($filter_temp_arr as $var => $value) {
                                $filter_new_arr[] = $value;
                            }
                        } else {
                            if (isset($count_filtration) && $count_filtration != '') {
                                foreach ($count_filtration as $var => $value) {
                                    $filter_new_arr[] = $value;
                                }
                            }
                        }
                        $filter_new_arr = isset($filter_new_arr) && !empty($filter_new_arr) ? call_user_func_array('array_merge', $filter_new_arr) : '';
                        $meta_post_ids_cus_fields_arr = '';
                        $meta_post_job_title_id_condition = '';
                        if (!empty($filter_new_arr)) {
                            $meta_post_ids_cus_fields_arr = cs_get_query_whereclase_by_array($filter_new_arr);
                            if (empty($meta_post_ids_cus_fields_arr)) {
                                $meta_post_ids_cus_fields_arr = array(0);
                            }
                            $ids = $meta_post_ids_cus_fields_arr != '' ? implode(",", $meta_post_ids_cus_fields_arr) : '0';
                            $meta_post_job_title_id_condition = " ID in (" . $ids . ") AND ";
                        }
                        ?>
                        <div class="accordion-group">
                            <div class="accordion-heading"> 
                                <a class="accordion-toggle <?php
                                if ($collapse_condition == 'on') {
                                    echo 'collapsed';
                                }
                                ?>" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo esc_html($custom_field_flag); ?>">
                                       <?php echo esc_html($cus_field['label']); ?>
                                </a> 
                            </div>
                            <div id="collapse<?php echo esc_html($custom_field_flag); ?>" class="accordion-body collapse <?php
                            if ($collapse_condition != 'on') {
                                echo 'in';
                            }
                            ?>">
                                <div class="accordion-inner">
                                    <?php
                                    if ($cus_field['type'] == 'dropdown') {
                                        $_query_string_arr = getMultipleParameters();
                                        ?>
                                        <form action="#" method="get" name="frm_<?php echo str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)); ?>" id="frm_<?php echo str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)); ?>">
                                            <ul class="custom-restaurant">
                                                <?php
                                                $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                                $final_query_str = str_replace("?", "", $final_query_str);
                                                $query = explode('&', $final_query_str);
                                                foreach ($query as $param) {
                                                    if (!empty($param)) {
                                                        $param_array = explode('=', $param);
                                                        $name = isset($param_array[0]) ? $param_array[0] : '';
                                                        $value = isset($param_array[1]) ? $param_array[1] : '';
                                                        $new_str = $name . "=" . $value;
                                                        if (is_array($name)) {

                                                            foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                                echo '<li>';
                                                                $cs_form_fields2->cs_form_hidden_render(
                                                                        array(
                                                                            'id' => $name,
                                                                            'cust_name' => $name . '[]',
                                                                            'std' => $value,
                                                                        )
                                                                );
                                                                echo '</li>';
                                                            }
                                                        } else {
                                                            echo '<li>';
                                                            $cs_form_fields2->cs_form_hidden_render(
                                                                    array(
                                                                        'id' => $name,
                                                                        'cust_name' => $name,
                                                                        'std' => $value,
                                                                    )
                                                            );
                                                            echo '</li>';
                                                        }
                                                    }
                                                }
                                                $number_option_flag = 1;
                                                $cut_field_flag = 0;
                                                foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                    if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                        $cut_field_flag++;
                                                        continue;
                                                    }
                                                    if ($cus_field_options_value != '') {
                                                        if ($cus_field['multi'] == 'on') {
                                                            $dropdown_arr = '';
                                                            if ($cus_field['post_multi'] == 'on') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }

                                                            $cus_field_mypost = '';
                                                            if ($job_title != '') {

                                                                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $meta_post_job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                                                                if ($post_ids) {
                                                                    $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                        'post__in' => $post_ids,
                                                                        'tax_query' => array(
                                                                            'relation' => 'AND',
                                                                            $filter_arr2
                                                                        ),
                                                                        'meta_query' => array(
                                                                            array(
                                                                                'key' => 'cs_job_posted',
                                                                                'value' => strtotime(date($cs_job_posted_date_formate)),
                                                                                'compare' => '<=',
                                                                            ),
                                                                            array(
                                                                                'key' => 'cs_job_expired',
                                                                                'value' => strtotime(date($cs_job_expired_date_formate)),
                                                                                'compare' => '>=',
                                                                            ),
                                                                            array(
                                                                                'key' => 'cs_job_status',
                                                                                'value' => 'active',
                                                                                'compare' => '=',
                                                                            ),
                                                                            $dropdown_arr,
                                                                        )
                                                                    );
                                                                }
                                                            } else {
                                                                $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                    'post__in' => $meta_post_ids_cus_fields_arr,
                                                                    'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                    'tax_query' => array(
                                                                        'relation' => 'AND',
                                                                        $filter_arr2
                                                                    ),
                                                                    'meta_query' => array(
                                                                        array(
                                                                            'key' => 'cs_job_posted',
                                                                            'value' => strtotime(date($cs_job_posted_date_formate)),
                                                                            'compare' => '<=',
                                                                        ),
                                                                        array(
                                                                            'key' => 'cs_job_expired',
                                                                            'value' => strtotime(date($cs_job_expired_date_formate)),
                                                                            'compare' => '>=',
                                                                        ),
                                                                        array(
                                                                            'key' => 'cs_job_status',
                                                                            'value' => 'active',
                                                                            'compare' => '=',
                                                                        ),
                                                                        $dropdown_arr,
                                                                    )
                                                                );
                                                            }
                                                            $cus_field_loop_count = new WP_Query($cus_field_mypost);
                                                            $cus_field_count_post = $cus_field_loop_count->post_count;
                                                            if (isset($_query_string_arr[$query_str_var_name]) && isset($cus_field_options_value) && is_array($_query_string_arr[$query_str_var_name]) && in_array($cus_field_options_value, $_query_string_arr[$query_str_var_name])) {
                                                                echo '<li class="checkbox" >';

                                                                $cs_form_fields2->cs_form_checkbox_render(
                                                                        array(
                                                                            'simple' => true,
                                                                            'cust_id' => $query_str_var_name . '_' . $number_option_flag,
                                                                            'cust_name' => $query_str_var_name,
                                                                            'extra_atr' => ' onclick="cs_restaurant_content_load();" onchange="javascript:frm_' . str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)) . '.submit();" checked="checked"',
                                                                            'std' => $cus_field_options_value,
                                                                        )
                                                                );

                                                                echo '<label class="cs-color" for="' . $query_str_var_name . '_' . $number_option_flag . '">' . $cus_field['options']['label'][$cut_field_flag] . '<span>(' . $cus_field_count_post . ')</span></label></li>';
                                                            } else {
                                                                echo '<li class="checkbox" >';

                                                                $cs_form_fields2->cs_form_checkbox_render(
                                                                        array(
                                                                            'simple' => true,
                                                                            'cust_id' => $query_str_var_name . '_' . $number_option_flag,
                                                                            'cust_name' => $query_str_var_name,
                                                                            'extra_atr' => ' onclick="cs_restaurant_content_load();" onchange="javascript:frm_' . str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)) . '.submit();"',
                                                                            'std' => $cus_field_options_value,
                                                                        )
                                                                );

                                                                echo '<label class="cs-color" for="' . $query_str_var_name . '_' . $number_option_flag . '">' . $cus_field['options']['label'][$cut_field_flag] . '<span>(' . $cus_field_count_post . ')</span></label></li>';
                                                            }
                                                        } else {
                                                            //get count for this itration
                                                            $dropdown_arr = '';
                                                            if ($cus_field['post_multi'] == 'on') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }
                                                            $cus_field_mypost = '';
                                                            if ($job_title != '') {

                                                                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $meta_post_job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'  AND post_type='jobs' AND post_status='publish'");
                                                                if ($post_ids) {
                                                                    $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                        'post__in' => $post_ids,
                                                                        'tax_query' => array(
                                                                            'relation' => 'AND',
                                                                            $filter_arr2
                                                                        ),
                                                                        'meta_query' => array(
                                                                            array(
                                                                                'key' => 'cs_job_posted',
                                                                                'value' => strtotime(date($cs_job_posted_date_formate)),
                                                                                'compare' => '<=',
                                                                            ),
                                                                            array(
                                                                                'key' => 'cs_job_expired',
                                                                                'value' => strtotime(date($cs_job_expired_date_formate)),
                                                                                'compare' => '>=',
                                                                            ),
                                                                            array(
                                                                                'key' => 'cs_job_status',
                                                                                'value' => 'active',
                                                                                'compare' => '=',
                                                                            ),
                                                                            $dropdown_arr,
                                                                        )
                                                                    );
                                                                }
                                                            } else {
                                                                $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                    'post__in' => $meta_post_ids_cus_fields_arr,
                                                                    'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                    'tax_query' => array(
                                                                        'relation' => 'AND',
                                                                        $filter_arr2
                                                                    ),
                                                                    'meta_query' => array(
                                                                        array(
                                                                            'key' => 'cs_job_posted',
                                                                            'value' => strtotime(date($cs_job_posted_date_formate)),
                                                                            'compare' => '<=',
                                                                        ),
                                                                        array(
                                                                            'key' => 'cs_job_expired',
                                                                            'value' => strtotime(date($cs_job_expired_date_formate)),
                                                                            'compare' => '>=',
                                                                        ),
                                                                        array(
                                                                            'key' => 'cs_job_status',
                                                                            'value' => 'active',
                                                                            'compare' => '=',
                                                                        ),
                                                                        $dropdown_arr,
                                                                    )
                                                                );
                                                            }
                                                            $cus_field_loop_count = new WP_Query($cus_field_mypost);
                                                            $cus_field_count_post = $cus_field_loop_count->post_count;
                                                            $amp_sign = '';
                                                            if (cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) != '?')
                                                                $amp_sign = '&';
                                                            if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == $cus_field_options_value) {

                                                                echo '<li><a onclick="cs_restaurant_content_load();"  class="text-capitalize active" href="' . cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) . '">' . $cus_field['options']['label'][$cut_field_flag] . ' <span>(' . $cus_field_count_post . ')</span></a></li>';
                                                            } else {
                                                                echo '<li><a onclick="cs_restaurant_content_load();"  class="text-capitalize" href="' . cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) . $amp_sign . $query_str_var_name . '=' . $cus_field_options_value . '">' . $cus_field['options']['label'][$cut_field_flag] . ' <span>(' . $cus_field_count_post . ')</span></a></li>';
                                                            }
                                                        }
                                                    }
                                                    $number_option_flag++;
                                                    $cut_field_flag++;
                                                }
                                                ?>
                                            </ul>
                                        </form>
                                        <?php
                                    } else if ($cus_field['type'] == 'text' || $cus_field['type'] == 'email' || $cus_field['type'] == 'url') {
                                        ?>
                                        <form action="#" method="get" name="frm_<?php echo esc_html($query_str_var_name); ?>">
                                            <?php
                                            $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                            $final_query_str = str_replace("?", "", $final_query_str);
                                            parse_str($final_query_str, $_query_str_arr);
                                            foreach ($_query_str_arr as $_query_str_single_var => $_query_str_single_value) {
                                                if (is_array($_query_str_single_value)) {
                                                    foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                        $cs_form_fields2->cs_form_hidden_render(
                                                                array(
                                                                    'id' => $_query_str_single_var,
                                                                    'cust_name' => $_query_str_single_var . '[]',
                                                                    'std' => $_query_str_single_value_arr,
                                                                )
                                                        );
                                                    }
                                                } else {
                                                    $cs_form_fields2->cs_form_hidden_render(
                                                            array(
                                                                'id' => $_query_str_single_var,
                                                                'cust_name' => $_query_str_single_var,
                                                                'std' => $_query_str_single_value,
                                                            )
                                                    );
                                                }
                                            }

                                            $cs_form_fields2->cs_form_text_render(
                                                    array(
                                                        'id' => $query_str_var_name,
                                                        'cust_name' => $query_str_var_name,
                                                        'classes' => 'form-control',
                                                        'extra_atr' => ' onclick="cs_restaurant_content_load();" onchange="javascript: ' . esc_html($query_str_var_name) . '.submit();"',
                                                        'std' => isset($_GET[$query_str_var_name]) ? $_GET[$query_str_var_name] : '',
                                                    )
                                            );
                                            ?>

                                        </form>
                                        <?php
                                    } else if ($cus_field['type'] == 'date') {

                                        $cus_field_date_formate_arr = explode(" ", $cus_field['date_format']);
                                        ?>
                                        <script>
                                            jQuery(function () {
                                                jQuery("#cs_<?php echo esc_html($query_str_var_name); ?>").datetimepicker({
                                                    format: "<?php echo esc_html($cus_field_date_formate_arr[0]); ?>",
                                                    timepicker: false
                                                });
                                            });
                                        </script>
                                        <form action="#" method="get" name="frm_<?php echo esc_html($query_str_var_name); ?>">
                                            <?php
                                            // parse query string and create hidden fileds
                                            $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                            $final_query_str = str_replace("?", "", $final_query_str);
                                            parse_str($final_query_str, $_query_str_arr);
                                            foreach ($_query_str_arr as $_query_str_single_var => $_query_str_single_value) {
                                                if (is_array($_query_str_single_value)) {
                                                    foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                        $cs_form_fields2->cs_form_hidden_render(
                                                                array(
                                                                    'id' => $_query_str_single_var,
                                                                    'cust_name' => $_query_str_single_var . '[]',
                                                                    'std' => $_query_str_single_value_arr,
                                                                )
                                                        );
                                                    }
                                                } else {
                                                    $cs_form_fields2->cs_form_hidden_render(
                                                            array(
                                                                'id' => $_query_str_single_var,
                                                                'cust_name' => $_query_str_single_var,
                                                                'std' => $_query_str_single_value,
                                                            )
                                                    );
                                                }
                                            }

                                            $cs_form_fields2->cs_form_text_render(
                                                    array(
                                                        'id' => $query_str_var_name,
                                                        'cust_name' => $query_str_var_name,
                                                        'classes' => 'form-control',
                                                        'extra_atr' => ' onclick="cs_restaurant_content_load();" onchange="javascript: ' . esc_html($query_str_var_name) . '.submit();"',
                                                        'std' => isset($_GET[$query_str_var_name]) ? $_GET[$query_str_var_name] : '',
                                                    )
                                            );
                                            ?>

                                        </form>
                                        <?php
                                    } elseif ($cus_field['type'] == 'range') {

                                        $range_min = $cus_field['min'];
                                        $range_max = $cus_field['max'];
                                        $range_increment = $cus_field['increment'];
                                        $filed_type = $cus_field['srch_style']; //input, slider, input_slider
                                        if (strpos($filed_type, '-') !== FALSE) {
                                            $filed_type_arr = explode("_", $filed_type);
                                        } else {
                                            $filed_type_arr[0] = $filed_type;
                                        }
                                        $range_flag = 0;
                                        while (count($filed_type_arr) > $range_flag) {
                                            if ($filed_type_arr[$range_flag] == 'input') { // if input style
                                                echo '<ul>';
                                                while ($range_min < $range_max) {
                                                    $cus_field_mypost = '';
                                                    if ($job_title != '') {

                                                        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $meta_post_job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                                                        if ($post_ids) {
                                                            $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                'post__in' => $post_ids,
                                                                'tax_query' => array(
                                                                    'relation' => 'AND',
                                                                    $filter_arr2
                                                                ),
                                                                'meta_query' => array(
                                                                    array(
                                                                        'key' => 'cs_job_posted',
                                                                        'value' => strtotime(date($cs_job_posted_date_formate)),
                                                                        'compare' => '<=',
                                                                    ),
                                                                    array(
                                                                        'key' => 'cs_job_expired',
                                                                        'value' => strtotime(date($cs_job_expired_date_formate)),
                                                                        'compare' => '>=',
                                                                    ),
                                                                    array(
                                                                        'key' => 'cs_job_status',
                                                                        'value' => 'active',
                                                                        'compare' => '=',
                                                                    ),
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => $range_min,
                                                                        'compare' => '>=',
                                                                    ),
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => $range_min + $range_increment,
                                                                        'compare' => '<=',
                                                                    ),
                                                                )
                                                            );
                                                        }
                                                    } else {
                                                        $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                            'post__in' => $meta_post_ids_cus_fields_arr,
                                                            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                            'tax_query' => array(
                                                                'relation' => 'AND',
                                                                $filter_arr2
                                                            ),
                                                            'meta_query' => array(
                                                                array(
                                                                    'key' => 'cs_job_posted',
                                                                    'value' => strtotime(date($cs_job_posted_date_formate)),
                                                                    'compare' => '<=',
                                                                ),
                                                                array(
                                                                    'key' => 'cs_job_expired',
                                                                    'value' => strtotime(date($cs_job_expired_date_formate)),
                                                                    'compare' => '>=',
                                                                ),
                                                                array(
                                                                    'key' => 'cs_job_status',
                                                                    'value' => 'active',
                                                                    'compare' => '=',
                                                                ),
                                                                array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $range_min,
                                                                    'compare' => '>=',
                                                                ),
                                                                array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $range_min + $range_increment,
                                                                    'compare' => '<=',
                                                                ),
                                                            )
                                                        );
                                                    }

                                                    $cus_field_loop_count = new WP_Query($cus_field_mypost);
                                                    $cus_field_count_post = $cus_field_loop_count->post_count;
                                                    ?>
                                                    <li>
                                                        <a onclick="cs_restaurant_content_load();" <?php
                                                        if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == ($range_min . "-" . ($range_min + $range_increment))) {
                                                            echo 'class="active"';
                                                        }
                                                        ?>href="<?php
                                                           if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == ($range_min . "-" . ($range_min + $range_increment))) {
                                                               echo cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                                           } else {
                                                               echo cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) . "&" . $query_str_var_name . '=' . $range_min . "-" . ($range_min + $range_increment);
                                                           }
                                                           ?>"><?php
                                                               echo esc_attr($range_min);
                                                               echo " - ";
                                                               $range_min = $range_min + $range_increment;
                                                               echo esc_attr($range_min);
                                                               ?> <span><?php echo '(' . $cus_field_count_post . ')'; ?></span><?php
                                                            if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == ($range_min . "-" . ($range_min + $range_increment))) {
                                                                echo '';
                                                            }
                                                            ?></a>
                                                    </li><?php
                                                    $range_min = $range_min + $range_increment;
                                                }
                                                echo '</ul>';
                                            } elseif ($filed_type_arr[$range_flag] == 'slider') { // if slider style
                                                ?>
                                                <form action="#" method="get" name="frm_<?php echo esc_html($query_str_var_name); ?>" id="frm_<?php echo esc_html($query_str_var_name); ?>">
                                                    <?php
                                                    $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                                    $final_query_str = str_replace("?", "", $final_query_str);
                                                    parse_str($final_query_str, $_query_str_arr);
                                                    foreach ($_query_str_arr as $_query_str_single_var => $_query_str_single_value) {
                                                        if (is_array($_query_str_single_value)) {
                                                            foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                                $cs_form_fields2->cs_form_hidden_render(
                                                                        array(
                                                                            'name' => '',
                                                                            'id' => $_query_str_single_var . '[]',
                                                                            'classes' => '',
                                                                            'std' => $_query_str_single_value_arr,
                                                                            'description' => '',
                                                                            'hint' => ''
                                                                        )
                                                                );
                                                            }
                                                        } else {
                                                            $cs_form_fields2->cs_form_hidden_render(
                                                                    array(
                                                                        'name' => '',
                                                                        'id' => $_query_str_single_var,
                                                                        'classes' => '',
                                                                        'std' => $_query_str_single_value,
                                                                        'description' => '',
                                                                        'hint' => ''
                                                                    )
                                                            );
                                                        }
                                                    }
                                                    $range_complete_str_first = "";
                                                    $range_complete_str_second = "";
                                                    if (isset($_GET[$query_str_var_name])) {
                                                        $range_complete_str = $_GET[$query_str_var_name];
                                                        $range_complete_str_arr = explode(",", $range_complete_str);
                                                        $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                                        $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                                                    } else {
                                                        $range_complete_str = '';
                                                        if (isset($_GET[$query_str_var_name]))
                                                            $range_complete_str = $_GET[$query_str_var_name];
                                                        $range_complete_str_first = $cus_field['min'];
                                                        $range_complete_str_second = $cus_field['max'];
                                                    }
                                                    echo '<div class="cs-selector-range">
                                                                <input name="' . $query_str_var_name . '" onchange="range_form_submit' . $cus_fieldvar . '();" id="slider-range' . esc_html($query_str_var_name) . '" type="text" class="span2" value="" data-slider-min="' . $cus_field['min'] . '" data-slider-max="' . $cus_field['max'] . '" data-slider-step="5" data-slider-value="[' . $range_complete_str_first . ',' . $range_complete_str_second . ']" />
                                                                       <div class="selector-value">
                                                                        <span>' . $cus_field['min'] . '</span>
                                                                        <span class="pull-right">' . $cus_field['max'] . '</span>
                                                                       </div>
                                                               </div>';
                                                    ?>
                                                </form>
                                                <?php
                                                echo '<script>
                                                    function range_form_submit' . $cus_fieldvar . '(){
                                                        cs_restaurant_content_load();
                                                        jQuery("#frm_' . esc_html($query_str_var_name) . '").submit();
                                                    }
                                                    jQuery(document).ready(function(){
                                                            jQuery("#slider-range' . esc_html($query_str_var_name) . '").slider({
                                                                stop: function(event, ui) {
                                                                    cs_restaurant_content_load();
                                                                    jQuery("#frm_' . esc_html($query_str_var_name) . '").submit();
                                                                }
                                                        });
                                                    });

                                                    </script>';
                                            }
                                            $range_flag++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div><?php
                    }
                    $custom_field_flag++;
                }
                ?>

            </div>
            <?php
        }
        }
    }
}
		?>
     
