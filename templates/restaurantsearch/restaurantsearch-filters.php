<?php
/**
 * Jobs Restaurant search box
 * default variable which is getting from ajax request or shotcode
 * $restaurant_short_counter, $restaurant_arg
 */
global $foodbakery_plugin_options, $foodbakery_form_fields_frontend, $foodbakery_post_restaurant_types, $foodbakery_shortcode_restaurants_frontend;
?>
<div class="user-filters">
    <div style="display:none" id='restaurant_arg<?php echo esc_html($restaurant_short_counter); ?>'><?php
	echo json_encode($restaurant_arg);
	?>
    </div>
    <div class="location-box">
        <ul>
            <li>
                <input placeholder="<?php echo esc_html__('What are you looking for?', 'foodbakery'); ?>" type="text">
            </li>
        </ul>
    </div>
    <div class="years-select-box">
        <ul>
            <li>
		<?php
		$restaurant_type = '';
		$foodbakery_post_restaurant_types = new Foodbakery_Post_Restaurant_Types();
		$restaurant_types_array = $foodbakery_post_restaurant_types->foodbakery_types_array_callback();
		$foodbakery_opt_array = array(
		    'std' => esc_attr($restaurant_type),
		    'id' => 'restaurant_type',
		    'classes' => 'chosen-select',
		    'cust_name' => 'restaurant_type',
		    'options' => $restaurant_types_array,
		    'extra_atr' => 'onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
		);

		$foodbakery_form_fields_frontend->foodbakery_form_select_render($foodbakery_opt_array);
		?>
                <i class="icon-chevron-small-down"></i>
            </li>
        </ul>
    </div>
    <div class="checked-box">
        <ul>
            <li>
                <i class="icon-location-pin2"></i>
                <input placeholder="<?php echo esc_html__('All Religons', 'foodbakery'); ?>" type="text">
		<?php
		// temprary off for presentation
		$foodbakery_select_display = 1;
		foodbakery_get_custom_locations_restaurant_filter('<div id="foodbakery-top-select-holder" class="search-country" style="display:' . foodbakery_allow_special_char($foodbakery_select_display) . '"><div class="select-holder">', '</div><span>' . esc_html__('Please select your desired location', 'foodbakery') . '</span> </div>', false, $restaurant_short_counter);
		?>
            </li>
        </ul>
    </div>

    <div class = "marital-status-box">
        <ul>
            <li>
                <input id = "single" name = "tow" type = "radio">
                <label for = "single"><?php echo esc_html__('All Restaurants', 'foodbakery'); ?></label>
            </li>
            <li>
                <input id = "divorced" name = "tow" type = "radio">
                <label for = "divorced"><?php echo esc_html__('Feacherd only', 'foodbakery'); ?></label>
            </li>
        </ul>
    </div>
    <div class = "more-filters-box">
        <div id = "accordion" role = "tablist" aria-multiselectable = "true">
            <div class = "panel panel-default">
                <div class = "panel-heading" role = "tab" id = "headingOne">
                    <h4 class = "panel-title">
                        <a data-toggle = "collapse" data-parent = "#accordion" href = "#collapseOne" aria-expanded = "false" aria-controls = "collapseOne" class = "collapsed">
			    <?php echo esc_html__('More Advance Filter', 'foodbakery'); ?>
                            <i class = "icon-chevron-small-down"></i>
                        </a>
                    </h4>
                </div>
                <div id = "collapseOne" class = "panel-collapse collapse" role = "tabpanel" aria-labelledby = "headingOne" aria-expanded = "false" style = "height: 0px;">
		    <?php
		    $restaurant_type_category_name = 'foodbakery_restaurant_category';
		    $category_request_val = isset($_REQUEST[$restaurant_type_category_name]) ? $_REQUEST[$restaurant_type_category_name] : '';
		    $foodbakery_restaurant_type_category_array = '';
		    $foodbakery_restaurant_type_category_array = $foodbakery_shortcode_restaurants_frontend->foodbakery_restaurant_filter_categories($restaurant_type, $category_request_val);
		    $foodbakery_restaurant_type_categories = array();
		    if (is_array($foodbakery_restaurant_type_category_array['cate_list']) && sizeof($foodbakery_restaurant_type_category_array['cate_list']) > 0) {

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
    		    <div class="select-categories">
    			<script>
    			    jQuery(function () {
    				'use strict'
    				var $checkboxes = jQuery("input[type=checkbox].<?php echo esc_html($restaurant_type_category_name); ?>");
    				$checkboxes.on('change', function () {
    				    var ids = $checkboxes.filter(':checked').map(function () {
    					return this.value;
    				    }).get().join(',');
    				    jQuery('#hidden_input-<?php echo esc_html($restaurant_type_category_name); ?>').val(ids);
    				    foodbakery_restaurant_content('<?php echo esc_html($restaurant_short_counter); ?>');
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
					    jQuery('#hidden_input-<?php echo esc_html($restaurant_type_category_name); ?>').val(ids);
					    foodbakery_restaurant_content('<?php echo esc_html($restaurant_short_counter); ?>');
					});

				    });
				</script>
				<ul class="cs-parent-checkbox-list">
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
					    <label for="<?php echo force_balance_tags('parent_all_categories' . $category_list_flag) ?>"><?php echo esc_html__('ALL Categories', 'foodbakery'); ?></label>
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
    			<ul class="cs-checkbox-list"><?php
				if (isset($foodbakery_restaurant_type_category_array['cate_list']) && !empty($foodbakery_restaurant_type_category_array['cate_list']) && is_array($foodbakery_restaurant_type_category_array['cate_list'])) {
				    foreach ($foodbakery_restaurant_type_category_array['cate_list'] as $in_category) {
					$term = get_term_by('slug', $in_category, 'restaurant-category');
					$restaurant_type_category_slug = $term->slug;
					$restaurant_type_category_lable = $term->name;
					// get count of each item
					// extra condidation
					$cate_count_arr = array(
					    'key' => $restaurant_type_category_name,
					    'value' => serialize($restaurant_type_category_slug),
					    'compare' => 'LIKE',
					);
					// main query array $args_count
					$cate_totnum = foodbakery_get_item_count($args_count, $cate_count_arr, 'meta_query');
					?>
	    			    <li>
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
	    				    <span>(<?php echo esc_html($cate_totnum); ?>)</span>
	    				</div>
	    			    </li>
					<?php
					$category_list_flag ++;
				    }
				}
				?>
    			</ul>

    		    </div>
			<?php
		    }
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
				    ?> <div class="panel panel-default">
					<div id="loyalty-program" role="tab" class="panel-heading">
					    <a aria-controls="collapseeight<?php echo esc_html($custom_field_flag); ?>" aria-expanded="false" href="#collapseeight<?php echo esc_html($custom_field_flag); ?>" data-toggle="collapse" role="button" class="collapsed">
						<?php echo esc_html($cus_field['label']); ?>
					    </a>
					</div>
					<div aria-labelledby="loyalty-program" role="tabpanel" class="panel-collapse collapse in" id="collapseeight<?php echo esc_html($custom_field_flag); ?>">
					    <div class="panel-body">
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
							?> <ul class="cs-checkbox-list"><?php
							foreach ($cus_field['options']['value'] as $cus_field_options_value) {
							    if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
								$cut_field_flag ++;
								continue;
							    }

							    // get count of each item
							    // extra condidation
							    $dropdown_count_arr = array(
								'key' => $query_str_var_name,
								'value' => $cus_field_options_value,
								'compare' => '=',
							    );
							    // main query array $args_count
							    $dropdown_totnum = foodbakery_get_item_count($args_count, $dropdown_count_arr, 'meta_query');
							    if ($cus_field_options_value != '') {
								if ($cus_field['multi'] == 'on') {
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
											    'extra_atr' => $checked . 'onchange=""',
											)
										);
										?>

										<label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>"><?php echo force_balance_tags($cus_field['options']['label'][$cut_field_flag]); ?></label>
										<span>(<?php echo esc_html($dropdown_totnum); ?>)</span>
									    </div>
									</li>

									<?php
								    } else {
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
										<span>(<?php echo esc_html($dropdown_totnum); ?>)</span>
									    </div>
									</li>
									<?php
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
									'extra_atr' => 'onchange="foodbakery_restaurant_content(\'' . $restaurant_short_counter . '\');"',
								    )
							    );
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
					</div>
				    </div>
				    <?php
				}
				$custom_field_flag ++;
			    }
			    echo esc_html($foodbakery_fields_output);
			}
		    }
		    ?></div>
            </div>
        </div>
    </div>
</div>