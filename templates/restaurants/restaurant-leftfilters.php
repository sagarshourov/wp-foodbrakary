<?php


/**
 * Jobs Listing search box
 * default variable which is getting from ajax request or shotcode
 * $restaurant_short_counter, $restaurant_arg
 */


global $foodbakery_plugin_options, $foodbakery_form_fields_frontend, $foodbakery_post_restaurant_types, $foodbakery_shortcode_restaurants_frontend;
wp_enqueue_style('foodbakery_bootstrap_slider_css');
wp_enqueue_script('foodbakery-bootstrap-slider');
wp_enqueue_script('bootstrap-datepicker');
wp_enqueue_style('datetimepicker');
wp_enqueue_script('datetimepicker');
$save_search_box = isset($atts['save_search_box']) ? $atts['save_search_box'] : '';
$left_filter_count_switch = isset($atts['left_filter_count']) ? $atts['left_filter_count'] : '';
$open_close_filter_switch = isset($atts['open_close_filter_switch']) ? $atts['open_close_filter_switch'] : 'yes';
$pre_order_filter_switch = isset($atts['pre_order_filter_switch']) ? $atts['pre_order_filter_switch'] : 'yes';
$open_close_default_filter = isset($atts['open_close_default_filter']) ? $atts['open_close_default_filter'] : 'all';
$lat_long = isset($lat_long) ? $lat_long : array();
$restaurant_type = 'restaurant-settings';
?>




<aside class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
<script>
    jQuery('.filter-toggle').click(function () {
	jQuery(this).toggleClass("active").next().slideToggle();
    });
</script>
    <div class="filter-toggle"><span class="filter-toggle-text"><?php echo esc_html__('Filters By', 'foodbakery'); ?></span><i class="icon-chevron-down"></i></div>
    <div class='filter-wrapper'>

        <div class="foodbakery-filters listing-filter"> 
            <div id="foodbakery-filters-<?php echo esc_html($restaurant_short_counter); ?>">
                <?php
                $search_title = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';
                $location = isset($_REQUEST['location']) ? $_REQUEST['location'] : '';
                $foodbakery_radius = isset($_REQUEST['foodbakery_radius']) ? $_REQUEST['foodbakery_radius'] : '';
                ?>
                <input type="hidden" name="search_title" value="<?php echo esc_html($search_title); ?>">
                <input type="hidden" name="location" value="<?php echo ($location); ?>">
                <input type="hidden" name="foodbakery_radius" value="<?php echo esc_html($foodbakery_radius); ?>">
                <?php
				
                // categories || Cuisines =================================================================
				
                $restaurant_type_category_name = 'foodbakery_restaurant_category';
                $category_request_val = isset($_REQUEST[$restaurant_type_category_name]) ? $_REQUEST[$restaurant_type_category_name] : '';
                $foodbakery_restaurant_type_category_array = '';
                $foodbakery_restaurant_type_category_array = $foodbakery_shortcode_restaurants_frontend->foodbakery_restaurant_filter_categories($restaurant_type, $category_request_val);
                $foodbakery_restaurant_type_categories = array();
                if (isset($foodbakery_restaurant_type_category_array['cate_list']) && is_array($foodbakery_restaurant_type_category_array['cate_list']) && sizeof($foodbakery_restaurant_type_category_array['cate_list']) > 0) {
                    $category_request_val_arr = explode(",", $category_request_val);
                    $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                            array(
                                'simple' => true,
                                'cust_id' => "hidden_input-" . $restaurant_type_category_name,
                                'cust_name' => $restaurant_type_category_name,
                                'std' => $category_request_val,
                                'classes' => $restaurant_type_category_name,
                                'extra_atr' => 'onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
                            )
                    );
                   
                    ?>
                    <div class="filter-holder panel-default">
                        <div class="filter-heading">
                            <h6><i class=" icon-food"></i><?php echo esc_html__('Cuisines', 'foodbakery'); ?></h6>
                        </div>
                        <div class="select-categories">
                            <?php ?>
                            <script>
                                jQuery(function () {
                                    'use strict'
                                    var $checkboxes = jQuery("input[type=checkbox].<?php echo esc_html($restaurant_type_category_name); ?>");
                                    $checkboxes.on('change', function () {
                                        var ids = $checkboxes.filter(':checked').map(function () {
                                            return this.value;
                                        }).get().join(',');																				
										
                                        jQuery('#hidden_input-<?php echo esc_html($restaurant_type_category_name); ?>').val(ids);										
										
                                        foodbakery_restaurant_content('<?php echo esc_html($restaurant_short_counter); ?>','testing');
                                    });

                                });
                            </script>
                            <?php
                            $category_list_flag = 1;
                            if (isset($foodbakery_restaurant_type_category_array['parent_list']) && !empty($foodbakery_restaurant_type_category_array['parent_list']) && is_array($foodbakery_restaurant_type_category_array['cate_list'])) {
                                ?>
                                <script>
                                    jQuery(function () {
                                        'use strict'
                                        var $checkboxes = jQuery("input[type=checkbox].parent_<?php echo esc_html($restaurant_type_category_name); ?>");
                                        $checkboxes.on('change', function () {
                                            var ids = this.value;
											//console.log(ids);
                                            jQuery('#hidden_input-<?php echo esc_html($restaurant_type_category_name); ?>').val(ids);
                                            foodbakery_restaurant_content('<?php echo esc_html($restaurant_short_counter); ?>');
                                        });

                                    });
                                </script>
                                <ul class="filter-list cs-parent-checkbox-list">
                                    <li>
                                        <div class="checkbox">
                                            <?php
                                            $foodbakery_form_fields_frontend->foodbakery_form_checkbox_render(
                                                    array(
                                                        'simple' => true,
                                                        'cust_id' => 'parent_all_categories' . $category_list_flag,
                                                        'cust_name' => '',
                                                        'std' => '',
                                                        'classes' => 'parent_' . $restaurant_type_category_name,
                                                    )
                                            );
                                            ?>
                                            <label for="<?php echo force_balance_tags('parent_all_categories' . $category_list_flag) ?>"><?php echo esc_html__('All Categories', 'foodbakery'); ?></label>
                                        </div>
                                    </li>
                                    <?php
                                    $foodbakery_restaurant_type_category_array['parent_list'] = array_reverse($foodbakery_restaurant_type_category_array['parent_list']);
                                    foreach ($foodbakery_restaurant_type_category_array['parent_list'] as $in_category) {
                                        $term = get_term_by('slug', $in_category, 'restaurant-category');
                                        $restaurant_type_category_slug = $term->slug;
                                        $restaurant_type_category_lable = $term->name;
                                        ?>
                                        <li>
                                            <div class="checkbox">
                                                <?php
                                                $foodbakery_form_fields_frontend->foodbakery_form_checkbox_render(
                                                        array(
                                                            'simple' => true,
                                                            'cust_id' => 'parent_' . $restaurant_type_category_name . '_' . $category_list_flag,
                                                            'cust_name' => '',
                                                            'std' => $restaurant_type_category_slug,
                                                            'classes' => 'parent_' . $restaurant_type_category_name,
                                                        )
                                                );
                                                ?>
                                                <label for="<?php echo force_balance_tags('parent_' . $restaurant_type_category_name . '_' . $category_list_flag) ?>"><?php echo force_balance_tags($restaurant_type_category_lable); ?></label>
                                            </div>
                                        </li>
                                        <?php
                                        $category_list_flag ++;
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>
                            <ul class="filter-list cs-checkbox-list"><?php
                              
                                if (isset($foodbakery_restaurant_type_category_array['cate_list']) && !empty($foodbakery_restaurant_type_category_array['cate_list']) && is_array($foodbakery_restaurant_type_category_array['cate_list'])) {
                                    $list_item_counter = 1;
                                   foreach ($foodbakery_restaurant_type_category_array['cate_list'] as $in_category) {
                                       $term = '';
                                        $term = get_term_by('slug', $in_category, 'restaurant-category');
					if(isset($term) && $term != ''){
                                        $restaurant_type_category_slug = $term->slug;
                                        $restaurant_type_category_lable = $term->name;
					}
                                        // get count of each item
                                        // extra condidation

                                        $cate_count_arr = array(
                                            'key' => $restaurant_type_category_name,
                                            'value' => serialize($restaurant_type_category_slug),
                                            'compare' => 'LIKE',
                                        );
                                        // main query array $args_count
                                        $cate_totnum = foodbakery_get_item_count($left_filter_count_switch, $args_count, $cate_count_arr, $restaurant_type, $restaurant_short_counter, $atts, $restaurant_type_category_name, $lat_long);


                                        $list_itm_visi = 'block';
                                        if ($list_item_counter > 8) {
                                            $list_itm_visi = 'none';
                                        }
                                        if ($restaurant_type_category_lable) {
                                            ?>
                                            <li style="display: <?php echo ($list_itm_visi) ?> !important;">
                                                <div class="checkbox">
                                                    <?php
                                                    $checked = '';
                                                    if (!empty($category_request_val_arr) && in_array($restaurant_type_category_slug, $category_request_val_arr)) {
                                                        $checked = 'checked="checked"';
                                                    }
                                                    $foodbakery_form_fields_frontend->foodbakery_form_checkbox_render(
                                                            array(
                                                                'simple' => true,
                                                                'cust_id' => $restaurant_type_category_name . '_' . $category_list_flag,
                                                                'cust_name' => '',
                                                                'std' => $restaurant_type_category_slug,
                                                                'classes' => $restaurant_type_category_name,
                                                                'extra_atr' => $checked,
                                                            )
                                                    );
                                                    ?>

                                                    <label for="<?php echo force_balance_tags($restaurant_type_category_name . '_' . $category_list_flag) ?>"><?php echo force_balance_tags($restaurant_type_category_lable); ?></label>
                                                    <?php if ($left_filter_count_switch == 'yes') { ?><span>(<?php echo esc_html($cate_totnum); ?>)</span><?php } ?>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                        $category_list_flag ++;
                                        $list_item_counter ++;
                                    }
                                }
                                ?>
                            </ul>

                        </div>

                    </div>
                    <?php
                }
                ?>

                <?php if ($open_close_filter_switch == 'yes') : ?>
                    <div class="filter-holder panel-default">
                        <div class="filter-heading">
                            <h6><i class="icon-clock4"></i><?php echo esc_html__('Opening Status', 'foodbakery'); ?></h6>
                        </div>

                        <div class="select-categories restaurant_timings">
                            <ul class="filter-list cs-parent-checkbox-list">
                                <?php
                                $restaurant_timings = isset($_REQUEST['restaurant_timings']) ? $_REQUEST['restaurant_timings'] : $open_close_default_filter;

                                $types = array(
                                    'open' => esc_html__('Open Now', 'foodbakery'),
                                    'close' => esc_html__('Closed Now', 'foodbakery'),
                                );
                                ?>
                                <?php foreach ($types as $key => $type) :
                                    ?>
                                    <li>
                                        <div class="checkbox">
                                            <?php
                                            $cate_count_arr = '';
                                            // main query array $args_count
                                        $cate_totnum = foodbakery_get_item_count($left_filter_count_switch, $args_count, $cate_count_arr, $restaurant_type, $restaurant_short_counter, $atts, 'restaurant_timings', $lat_long, $key);
										  
                                            $checked = $restaurant_timings != '' && $restaurant_timings == $key ? 'checked="checked"' : '';
                                            $foodbakery_form_fields_frontend->foodbakery_form_checkbox_render(
                                                    array(
                                                        'simple' => true,
                                                        'cust_id' => 'restaurant_timings_' . $key,
                                                        'cust_name' => 'restaurant_timings_checkbox',
                                                        'std' => $key,
                                                        'classes' => 'restaurant_timings_' . $key,
                                                        'extra_atr' => $checked,
                                                    )
                                            );
                                            ?>
                                            <label for="restaurant_timings_<?php echo ($key); ?>"><?php echo ucfirst($type); ?>
                                                <?php if ($left_filter_count_switch == 'yes') { ?><span>(<?php echo esc_html($cate_totnum); ?>)</span><?php } ?> 
                                            </label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <input type="hidden" value="<?php echo ($restaurant_timings); ?>" name="restaurant_timings">
                            <script>
                                jQuery(function () {
                                    'use strict'
                                    jQuery("input[name='restaurant_timings_checkbox']").on('change', function () {
                                        jQuery('.restaurant_timings input[type="checkbox"]').prop('checked', false);
                                        jQuery(this).prop('checked', true);
                                        jQuery("input[name='restaurant_timings']").val(jQuery(this).val());
                                        foodbakery_restaurant_content('<?php echo esc_html($restaurant_short_counter); ?>');
                                    });
                                });
                            </script>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($pre_order_filter_switch == 'yes') : ?>
                    <div class="filter-holder panel-default">
                        <div class="filter-heading">
                            <h6><i class="icon-external-link"></i><?php echo esc_html__('Pre Orders', 'foodbakery'); ?></h6>
                        </div>

                        <div class="select-categories restaurant_pre_order">
                            <ul class="filter-list cs-parent-checkbox-list">
                                <?php
                                $restaurant_pre_order = isset($_REQUEST['restaurant_pre_order']) ? $_REQUEST['restaurant_pre_order'] : 'all';

                                $types = array(
                                    'yes' => esc_html__('Yes', 'foodbakery'),
                                    'no' => esc_html__('No', 'foodbakery'),
                                );
                                ?>
                                <?php foreach ($types as $key => $type) :
                                    ?>
                                    <li>
                                        <div class="checkbox">
                                            <?php
                                            $cate_count_arr = '';
                                            // main query array $args_count
                                            $cate_totnum = foodbakery_get_item_count($left_filter_count_switch, $args_count, $cate_count_arr, $restaurant_type, $restaurant_short_counter, $atts, 'restaurant_pre_order', $lat_long, '',$key);
                                            $checked = $restaurant_pre_order != '' && $restaurant_pre_order == $key ? 'checked="checked"' : '';
                                            $foodbakery_form_fields_frontend->foodbakery_form_checkbox_render(
                                                    array(
                                                        'simple' => true,
                                                        'cust_id' => 'restaurant_pre_order_' . $key,
                                                        'cust_name' => 'restaurant_pre_order_checkbox',
                                                        'std' => $key,
                                                        'classes' => 'restaurant_pre_order_' . $key,
                                                        'extra_atr' => $checked,
                                                    )
                                            );
                                            ?>
                                            <label for="restaurant_pre_order_<?php echo ($key); ?>"><?php echo ucfirst($type); ?>
                                                <?php if ($left_filter_count_switch == 'yes') { ?><span>(<?php echo esc_html($cate_totnum); ?>)</span><?php } ?> 
                                            </label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <input type="hidden" value="<?php echo ($restaurant_pre_order); ?>" name="restaurant_pre_order">
                            <script>
                                jQuery(function () {
                                    'use strict'
                                    jQuery("input[name='restaurant_pre_order_checkbox']").on('change', function () {
                                        jQuery('.restaurant_pre_order input[type="checkbox"]').prop('checked', false);
                                        jQuery(this).prop('checked', true);
                                        jQuery("input[name='restaurant_pre_order']").val(jQuery(this).val());
                                        foodbakery_restaurant_content('<?php echo esc_html($restaurant_short_counter); ?>');
                                    });
                                });
                            </script>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                // $restaurant_type getting from shortcode backend element
                if (isset($restaurant_type) && $restaurant_type != '') {
                    $foodbakery_restaurant_type_cus_fields = $foodbakery_post_restaurant_types->foodbakery_types_custom_fields_array($restaurant_type);
                    $foodbakery_fields_output = '';
                    if (is_array($foodbakery_restaurant_type_cus_fields) && sizeof($foodbakery_restaurant_type_cus_fields) > 0) {
                        $custom_field_flag = 1;
                        foreach ($foodbakery_restaurant_type_cus_fields as $cus_fieldvar => $cus_field) {

                            $all_item_empty = 0;
                            $query_str_var_name = '';
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
                                ?> <div class="filter-holder panel-default">
                                    <div class="filter-heading">
                                        <h6><i class="<?php echo esc_html($cus_field['fontawsome_icon']); ?>"></i><?php echo esc_html($cus_field['label']); ?></h6>
                                    </div>

                                    <div class="select-categories">
                                        <?php
                                        if ($cus_field['type'] == 'dropdown') {
                                            $number_option_flag = 1;
                                            $cut_field_flag = 0;
                                            $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                            $request_val_arr = explode(",", $request_val);
                                            if ($cus_field['multi'] == 'on') { // if multi select then use hidden for submittion
                                                $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                                                        array(
                                                            'simple' => true,
                                                            'cust_id' => "hidden_input-" . $query_str_var_name,
                                                            'cust_name' => $query_str_var_name,
                                                            'std' => $request_val,
                                                            'classes' => $query_str_var_name,
                                                            'extra_atr' => 'onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
                                                        )
                                                );
                                                ?>

                                                <script>
                                                    jQuery(function () {
                                                        'use strict'
                                                        var $checkboxes = jQuery("input[type=checkbox].<?php echo esc_html($query_str_var_name); ?>");
                                                        $checkboxes.on('change', function () {
                                                            var ids = $checkboxes.filter(':checked').map(function () {
                                                                return this.value;
                                                            }).get().join(',');
                                                            jQuery('#hidden_input-<?php echo esc_html($query_str_var_name); ?>').val(ids);
                                                            foodbakery_restaurant_content('<?php echo esc_html($restaurant_short_counter); ?>');
                                                        });

                                                    });
                                                </script>
                                                <?php
                                            }
                                            ?> <ul class="filter-list cs-checkbox-list"><?php
                                            foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                    $cut_field_flag ++;
                                                    continue;
                                                }
                                                // get count of each item
                                                // extra condidation
                                                if ($cus_field['post_multi'] == 'on') {

                                                    $dropdown_count_arr = array(
                                                        'key' => $query_str_var_name,
                                                        'value' => serialize($cus_field_options_value),
                                                        'compare' => 'Like',
                                                    );
                                                } else {
                                                    $dropdown_count_arr = array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $cus_field_options_value,
                                                        'compare' => '=',
                                                    );
                                                }



                                                // main query array $args_count
                                                $dropdown_totnum = foodbakery_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $restaurant_type, $restaurant_short_counter, $atts, $query_str_var_name, $lat_long);
                                                if ($cus_field_options_value != '') {
                                                    if ($cus_field['multi'] == 'on') {
                                                        if (isset($cus_field['options']['label'][$cut_field_flag]) && $cus_field['options']['label'][$cut_field_flag] != '') {
                                                            ?>
                                                                <li style="">
                                                                    <div class="checkbox">
                                                                        <?php
                                                                        $checked = '';
                                                                        if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                                            $checked = 'checked="checked"';
                                                                        }
                                                                        $foodbakery_form_fields_frontend->foodbakery_form_checkbox_render(
                                                                                array(
                                                                                    'simple' => true,
                                                                                    'cust_id' => $query_str_var_name . '_' . $number_option_flag,
                                                                                    'cust_name' => '',
                                                                                    'std' => $cus_field_options_value,
                                                                                    'classes' => $query_str_var_name,
                                                                                    'extra_atr' => $checked . ' onchange=""',
                                                                                )
                                                                        );
                                                                        ?>

                                                                        <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>"><?php echo force_balance_tags($cus_field['options']['label'][$cut_field_flag]); ?></label>
                                                                        <?php if ($left_filter_count_switch == 'yes') { ?><span>(<?php echo esc_html($dropdown_totnum); ?>)</span><?php } ?>
                                                                    </div>
                                                                </li>

                                                                <?php
                                                            }
                                                        } else {
                                                            if (isset($cus_field['options']['label'][$cut_field_flag]) && $cus_field['options']['label'][$cut_field_flag] != '') {
                                                                ?>
                                                                <li style="">
                                                                    <div class="checkbox">
                                                                        <?php
                                                                        $checked = '';
                                                                        if (!empty($request_val) && $cus_field_options_value == $request_val) {
                                                                            $checked = 'checked="checked"';
                                                                        }
                                                                        $foodbakery_form_fields_frontend->foodbakery_form_radio_render(
                                                                                array(
                                                                                    'simple' => true,
                                                                                    'cust_id' => $query_str_var_name . '_' . $number_option_flag,
                                                                                    'cust_name' => $query_str_var_name,
                                                                                    'std' => $cus_field_options_value,
                                                                                    'extra_atr' => $checked . ' onchange="foodbakery_restaurant_content(\'' . esc_html($restaurant_short_counter) . '\');"',
                                                                                )
                                                                        );
                                                                        ?>
                                                                        <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>"><?php echo force_balance_tags($cus_field['options']['label'][$cut_field_flag]); ?></label>
                                                                        <?php if ($left_filter_count_switch == 'yes') { ?><span>(<?php echo esc_html($dropdown_totnum); ?>)</span><?php } ?>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    $number_option_flag ++;
                                                    $cut_field_flag ++;
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        } else if ($cus_field['type'] == 'text' || $cus_field['type'] == 'email' || $cus_field['type'] == 'url' || $cus_field['type'] == 'number') {
                                            ?>
                                            <div class="select-categories">
                                                <?php
                                                $foodbakery_form_fields_frontend->foodbakery_form_text_render(
                                                        array(
                                                            'id' => $query_str_var_name,
                                                            'cust_name' => $query_str_var_name,
                                                            'classes' => 'form-control',
                                                            'std' => isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '',
                                                            'extra_atr' => 'onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');" onkeypress="foodbakery_handleKeyPress_' . $query_str_var_name . '(event)"',
                                                        )
                                                );
                                                echo '<script>function foodbakery_handleKeyPress_' . $query_str_var_name . '(e){
                                                    var key=e.keyCode || e.which;
                                                     if (key==13){ 
                                                        foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');
                                                     }
                                                   }</script>';
                                                ?>
                                            </div>   
                                            <?php
                                        } else if ($cus_field['type'] == 'date') {
                                            ?>
                                            <div class="select-categories">
                                                <div class="cs-datepicker">
                                                    <span class="datepicker-text"><?php echo esc_html__('From', 'foodbakery'); ?></span>
                                                    <label id="Deadline" class="cs-calendar-from">
                                                        <?php
                                                        $foodbakery_form_fields_frontend->foodbakery_form_text_render(
                                                                array(
                                                                    'id' => $query_str_var_name,
                                                                    'cust_name' => 'from' . $query_str_var_name,
                                                                    'classes' => 'form-control',
                                                                    'std' => isset($_REQUEST['from' . $query_str_var_name]) ? $_REQUEST['from' . $query_str_var_name] : '',
                                                                    'extra_atr' => ' placeholder="________" onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
                                                                )
                                                        );
                                                        ?>

                                                    </label>
                                                </div>
                                                <div class="cs-datepicker">
                                                    <span class="datepicker-text"><?php echo esc_html__('To', 'foodbakery'); ?></span>
                                                    <label id="Deadline" class="cs-calendar-to">
                                                        <?php
                                                        $foodbakery_form_fields_frontend->foodbakery_form_text_render(
                                                                array(
                                                                    'id' => $query_str_var_name,
                                                                    'cust_name' => 'to' . $query_str_var_name,
                                                                    'classes' => 'form-control',
                                                                    'std' => isset($_REQUEST['to' . $query_str_var_name]) ? $_REQUEST['to' . $query_str_var_name] : '',
                                                                    'extra_atr' => ' placeholder="________" onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
                                                                )
                                                        );
                                                        ?>

                                                    </label>
                                                </div>
                                                <div class="datepicker-text-bottom"><i class="icon-time"></i><span><?php echo esc_html__('From Date', 'foodbakery'); ?></span></div>
                                                <div class="datepicker-text-bottom"><i class="icon-time"></i><span><?php echo esc_html__('To Date', 'foodbakery'); ?></span></div>
                                            </div>
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
                                                if ($filed_type_arr[$range_flag] == 'input') {
                                                    
                                                } elseif ($filed_type_arr[$range_flag] == 'slider') { // if slider style
                                                    if ((isset($cus_field['min']) && $cus_field['min'] != '') && (isset($cus_field['max']) && $cus_field['max'] != '' )) {
                                                        $range_complete_str_first = "";
                                                        $range_complete_str_second = "";
                                                        $range_complete_str = '';
                                                        if (isset($_REQUEST[$query_str_var_name])) {
                                                            $range_complete_str = $_REQUEST[$query_str_var_name];
                                                            $range_complete_str_arr = explode(",", $range_complete_str);
                                                            $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                                            $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                                                        } else {
                                                            $range_complete_str = $cus_field['min'] . ',' . $cus_field['max'];
                                                            $range_complete_str_first = $cus_field['min'];
                                                            $range_complete_str_second = $cus_field['max'];
                                                        }

                                                        $foodbakery_form_fields_frontend->foodbakery_form_hidden_render(
                                                                array(
                                                                    'simple' => true,
                                                                    'cust_id' => "range-hidden-" . $query_str_var_name,
                                                                    'cust_name' => $query_str_var_name,
                                                                    'std' => esc_html($range_complete_str),
                                                                    'classes' => $query_str_var_name,
                                                                    'extra_atr' => 'onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
                                                                )
                                                        );
                                                        ?>
                                                        <div class="price-per-person">
                                                            <input id="ex16b<?php echo esc_html($query_str_var_name) ?>" type="text" />
                                                            <span class="rang-text"><?php echo esc_html($range_complete_str_first); ?> &nbsp; - &nbsp; <?php echo esc_html($range_complete_str_second); ?></span>
                                                        </div>
                                                        <?php
                                                        $increment_step = isset($cus_field['increment']) ? $cus_field['increment'] : 1;
                                                        echo '<script>
                                                                    jQuery(document).ready(function() {
                                                                    if (jQuery("#ex16b' . $query_str_var_name . '").length != "") {
                                                                        jQuery("#ex16b' . $query_str_var_name . '").slider({
									    step : ' . esc_html($increment_step) . ',
                                                                            min: ' . esc_html($cus_field['min']) . ',
                                                                            max: ' . esc_html($cus_field['max']) . ',
                                                                            value: [ ' . esc_html($range_complete_str) . '],
                                                                          
                                                                             
                                                                        });
                                                                        jQuery("#ex16b' . $query_str_var_name . '").on("slideStop", function () {

                                                                                var rang_slider_val = jQuery("#ex16b' . $query_str_var_name . '").val();
                                                                                jQuery("#range-hidden-' . $query_str_var_name . '").val(rang_slider_val);    
                                                                                foodbakery_restaurant_content("' . esc_html($restaurant_short_counter) . '");
                                                                            });
                                                                        }
                                                                    });
                                                                </script>';
                                                    }
                                                    $range_flag ++;
                                                }
                                            }
                                        } else {
                                            echo esc_html($cus_field['type']);
                                        }
                                        ?>

                                    </div>


                                </div>
                                <?php
                            }
                            $custom_field_flag ++;
                        }
                        echo esc_html($foodbakery_fields_output);
                    }
                }
                ?>


            </div>

        </div><!-- end of filters-->

        <div class="restaurant-filters-ads">
            <?php do_action('foodbakery_random_ads', 'restaurant_banner_leftfilter');
            ?>
        </div>
    </div>
</aside>