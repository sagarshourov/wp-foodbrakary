<?php

/**
 * File Type: Restaurant Menus
 */
if (!class_exists('Foodbakery_Admin_Restaurant_Menus')) {

    class Foodbakery_Admin_Restaurant_Menus {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_filter('foodbakery_admin_restaurant_menu_items', array($this, 'foodbakery_admin_restaurant_menu_items_callback'), 10, 2);
            add_action('wp_ajax_foodbakery_admin_add_menu_item_to_list', array($this, 'foodbakery_admin_add_menu_item_to_list_callback'));
            add_action('wp_ajax_foodbakery_admin_restaurant_menu_items_extra_fields', array($this, 'foodbakery_admin_restaurant_menu_items_extra_fields_callback'));
            add_action('save_post', array($this, 'foodbakery_insert_menu_items'), 17);
        }

        public function foodbakery_admin_restaurant_menu_items_callback($restaurant_id = '') {
            global $foodbakery_plugin_options, $foodbakery_form_fields, $restaurant_add_counter, $post;
            $restaurant_add_counter = rand(123456789, 987654321);
            $currency_sign = foodbakery_get_currency_sign();
            $html = '';
            $restaurant_id = $post->ID;
            $restaurant_type_id = foodbakery_restaurant_type_id();
            $menu_item_counter = rand(123456789, 987654321);
            $menu_items_list = '';
            $get_restaurant_menu_items = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
            $menu_items_list .= $this->group_restaurant_menu_items($get_restaurant_menu_items, $restaurant_add_counter);
            if ($menu_items_list == '') {
                $menu_items_list = '<li id="no-menu-items-' . $restaurant_add_counter . '" class="no-result-msg">' . __('No Menu Items added.', 'foodbakery') . '</li>';
            }
            $html .= '<div class="theme-help" id="">
				<h4 style="padding-bottom:0px;">' . __('Menu Builder', 'foodbakery') . '</h4>
				<div class="clear"></div>
			</div>
			<div id="form-elements" class="form-elements">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<div id="menu-item-loader-' . $restaurant_add_counter . '" class="restaurant-loader"></div>
						<a class="add-menu-item" href="javascript:void(0);" onClick="javascript:foodbakery_add_menu_item(\'' . $restaurant_add_counter . '\');">' . __('Add Menu Item', 'foodbakery') . '</a>
					</div>
				</div>
				<div id="add-menu-item-from-' . $restaurant_add_counter . '" style="display:none;">';
            $html .= $this->foodbakery_restaurant_menu_items_form_ui($restaurant_add_counter, $menu_item_counter, '', 'add');
            $html .= '</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<div class="service-list">
						<div class="menu-items-list-holder">
							<ul id="restaurant_menu_items-list-' . $restaurant_add_counter . '" data-id="' . $restaurant_id . '" class="ui-sortable">
								' . $menu_items_list . '
							</ul>
						</div>
						</div>
					</div>
				</div>
			</div>';

            return apply_filters('foodbakery_admin_restaurant_add_menu_items', $html, $restaurant_type_id, $restaurant_id);
        }

        public function foodbakery_restaurant_menu_items_form_ui($restaurant_add_counter = '', $menu_item_counter = '', $get_menu_items = array(), $form_action = 'edit') {
            global $foodbakery_plugin_options, $foodbakery_form_fields, $foodbakery_html_fields, $post;
            $currency_sign = foodbakery_get_currency_sign();
            $restaurant_id = isset($post->ID) ? $post->ID : '';
            if ($restaurant_id == '') {
                $restaurant_id = isset($_POST['restaurant_id']) ? $_POST['restaurant_id'] : '';
            }
            $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
            $restaurants_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : '';
            $restaurant_menu = isset($get_menu_items['restaurant_menu']) ? $get_menu_items['restaurant_menu'] : '';
            $menu_item_title = isset($get_menu_items['menu_item_title']) ? $get_menu_items['menu_item_title'] : '';
            $menu_item_price = isset($get_menu_items['menu_item_price']) ? $get_menu_items['menu_item_price'] : '';
            $menu_item_icon = isset($get_menu_items['menu_item_icon']) ? $get_menu_items['menu_item_icon'] : '';
            $menu_item_nutri = isset($get_menu_items['menu_item_nutri']) ? $get_menu_items['menu_item_nutri'] : '';
            $menu_item_desc = isset($get_menu_items['menu_item_desc']) ? $get_menu_items['menu_item_desc'] : '';
            $menu_item_extra = isset($get_menu_items['menu_item_extra']) ? $get_menu_items['menu_item_extra'] : '';

            $menu_item_counter = isset($get_menu_items['menu_item_counter']) ? $get_menu_items['menu_item_counter'] : $menu_item_counter;
            if (!is_array($menu_item_nutri) && $menu_item_nutri != '') {
                $menu_item_nutri = explode(',', $menu_item_nutri);
            }
            $form_html = '';
            $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
            $form_html .= '<a href="javascript:void(0);" onClick="foodbakery_close_menu_item(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\');" class="close-menu-item"><i class="icon-close2"></i></a>';
            $form_html .= '<div class="row">';
            $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="field-holder">
								<label>' . __('Restaurant Menu *', 'foodbakery') . '</label>';
            $restaurants_menus_options = array();
            $restaurants_menus = get_post_meta($restaurant_id, 'menu_cat_titles', true);
            if (is_array($restaurants_menus) && sizeof($restaurants_menus) > 0) {
                foreach ($restaurants_menus as $key => $lable) {
                    if ($lable != '') {
                        $restaurants_menus_options[$lable] = $lable;
                    }
                }
            }
            $foodbakery_opt_array = array();
            $foodbakery_opt_array['std'] = esc_attr($restaurant_menu);
            $foodbakery_opt_array['cust_id'] = 'restaurant_menu_' . $menu_item_counter;
            $foodbakery_opt_array['cust_name'] = 'restaurant_menu[' . $menu_item_counter . ']';
            $foodbakery_opt_array['options'] = $restaurants_menus_options;
            $foodbakery_opt_array['classes'] = 'chosen-select restaurants-menu';
            $foodbakery_opt_array['return'] = true;
            $form_html .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
            $form_html .= '</div></div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . __('Title *', 'foodbakery') . '</label>
					<input id="current_restaurant_menu_' . $menu_item_counter . '" type="hidden" value="' . esc_attr($restaurant_menu) . '">
					<input name="menu_item_action[' . $menu_item_counter . ']" value="' . esc_attr($form_action) . '" type="hidden">
					<input class="menu-item-title" id="menu_item_title_' . $menu_item_counter . '" name="menu_item_title[' . $menu_item_counter . ']" value="' . esc_attr($menu_item_title) . '" type="text" placeholder="' . __('Menu Item Title', 'foodbakery') . '">
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . __('Price *', 'foodbakery') . ' (' . $currency_sign . ')</label>
					<input class="menu-item-price" id="menu_item_price_' . $menu_item_counter . '" name="menu_item_price[' . $menu_item_counter . ']" type="text" value="' . esc_attr($menu_item_price) . '" placeholder="' . __('Menu Item Price', 'foodbakery') . '">
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . __('Food Image', 'foodbakery') . '</label>
					<div class="icon-image-browse-field">';
            $foodbakery_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'without_html' => true,
                'force_std' => true,
                'std' => $menu_item_icon,
                'echo' => false,
                'id' => 'menu_item_icon_' . $menu_item_counter,
                'field_params' => array(
                    'id' => 'menu_item_icon_' . $menu_item_counter,
                    'cust_name' => 'menu_item_icon[' . $menu_item_counter . ']',
                    'force_std' => true,
                    'std' => $menu_item_icon,
                    'return' => true,
                ),
            );
            $form_html .= $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
            $form_html .= '</div></div></div>';
            $get_foodbakeri_nutri_icons = get_post_meta($restaurants_type_id, 'nutri_icon_imgs', true);
            $get_foodbakeri_nutri_titles = get_post_meta($restaurants_type_id, 'nutri_icon_titles', true);
            if (is_array($get_foodbakeri_nutri_icons) && sizeof($get_foodbakeri_nutri_icons) > 0) {
                $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<label>' . esc_html__('Nutritional Information icons', 'foodbakery') . '</label>';
                $form_html .= '<div class="nutri-info-icons"><ul>';
                $nutri_count = 0;
                foreach ($get_foodbakeri_nutri_icons as $nutri_icon) {
                    $nutri_rand = rand(100000, 9000000);
                    if ($nutri_icon != '') {
                        $menu_nutri_title = isset($get_foodbakeri_nutri_titles[$nutri_count]) ? $get_foodbakeri_nutri_titles[$nutri_count] : '';
                        $nutri_icon_img_arr = wp_get_attachment_image_src($nutri_icon, 'thumbnail');
                        $nutri_icon_img_src = isset($nutri_icon_img_arr[0]) ? $nutri_icon_img_arr[0] : '';
                        $form_html .= '<li' . (is_array($menu_item_nutri) && in_array($nutri_icon, $menu_item_nutri) ? ' class="active"' : '') . '><input id="nutri-icon-' . $nutri_rand . '"' . (is_array($menu_item_nutri) && in_array($nutri_icon, $menu_item_nutri) ? ' checked="checked"' : '') . ' type="checkbox"' . ( $form_action == 'add' ? ' name="menu_item_nutri_info[]"' : ' name="menu_item_nutri[' . $menu_item_counter . '][]"') . ' value="' . $nutri_icon . '"><label for="nutri-icon-' . $nutri_rand . '"><a data-toggle="tooltip" title="' . $menu_nutri_title . '"><img src="' . $nutri_icon_img_src . '" alt="" /></a></label></li>';
                    }
                    $nutri_count ++;
                }
                $form_html .= '</ul></div>';
                $form_html .= '</div></div>';
            }
            $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . __('Description', 'foodbakery') . '</label>
					<textarea class="menu-item-desc foodbakery_editor" id="menu_item_desc_' . $menu_item_counter . '" name="menu_item_desc[' . $menu_item_counter . ']" placeholder="' . __('Menu Item Description test', 'foodbakery') . '">' . esc_attr($menu_item_desc) . '</textarea>
				</div>
			</div>';
            $form_html .= '<ul id="menu-item-extra-list-' . $menu_item_counter . '" class="menu-item-extra-list">';
            $form_html .= $this->foodbakery_restaurant_menu_items_extra_saved_fields($restaurant_add_counter, $menu_item_counter, $menu_item_extra);
            $form_html .= '</ul>';
            if ($form_action == 'edit') {
                $action = 'edit';
            } else {
                $action = 'add';
            }
            $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="field-holder">
								<a class="add-menu-item-extra" href="javascript:void(0);" onClick="foodbakery_add_menu_item_extra(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\');">' . __('Add Menu Item Extra', '') . '</a>
								<a class="add-menu-item add-menu-item-list" href="javascript:void(0);" onClick="foodbakery_admin_add_menu_item_to_list(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . $action . '\');">' . __('Save', 'foodbakery') . '</a>
							</div>
						</div>';
            $form_html .= '</div>';
            $form_html .= '</div>';
            return $form_html;
        }

        public function foodbakery_restaurant_menu_items_extra_saved_fields($restaurant_add_counter = '', $menu_item_counter = '', $menu_item_extra = array()) {
            global $foodbakery_plugin_options, $foodbakery_html_fields;
            $currency_sign = foodbakery_get_currency_sign();

            if (isset($menu_item_extra[0]) && is_array($menu_item_extra[0]) && sizeof($menu_item_extra['heading']) > 0) {
                $form_extra_html = '';
                $count_extra_li = 0;
                $count_extra_inner_li = 0;

                foreach ($menu_item_extra['heading'] as $key => $value) {
                    if( $value != ''){
                    $value_type = isset($menu_item_extra['type'][$key]) ? $menu_item_extra['type'][$key] : '';
                    $required_num = isset($menu_item_extra['required'][$key]) ? $menu_item_extra['required'][$key] : '';
                    $menu_item_extra_titles = isset($menu_item_extra[$key]['title']) ? $menu_item_extra[$key]['title'] : array();
                    $count_extra_next_li = count($menu_item_extra_titles);
                    $menu_item_extra_prices = isset($menu_item_extra[$key]['price']) ? $menu_item_extra[$key]['price'] : array();
                    $form_extra_html .= '<li class="menu-item-extra-' . $menu_item_counter . '" >';
                    $form_extra_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="extra_li_'.$key.'">';
                    $form_extra_html .= '<div class="row"><div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<div class="field-holder">
											<label>' . __('Heading', 'foodbakery') . '</label>
											<input class="menu-item-extra-heading" id="menu_item_extra_heading_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][heading][]" value="' . esc_attr($value) . '" type="text" placeholder="' . __('Heading', 'foodbakery') . '">       
										</div></div>
                                                                        <div class="col-lg-3 col-md-6 col-sm-3 col-xs-3">
                                                                        <label>'.esc_html__('Extra Type', 'foodbakery').'</label>';
                    $foodbakery_opt_array = array('name' => esc_html__('Extra Type', 'foodbakery'),
                        'desc' => '',
                        'id' => 'menu_item_extra' . $menu_item_counter . '',
                        'cust_name' => 'menu_item_extra[' . $menu_item_counter . '][type][]',
                        'std' => $value_type,
                        'force_std' => true,
                        'classes' => 'chosen-select',
                        'options' => array(
                            'single' => esc_html__('Single (Radio button)', 'foodbakery'),
                            'multiple' => esc_html__('Multiple (CheckBoxes)', 'foodbakery'),
                        ),
                        'return' => true,
                    );
                    $form_extra_html .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
                    $form_extra_html .= '</div>              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
												<div class="field-holder">
													<label>' . __('Required', 'foodbakery') . '</label>
													<input class="menu-item-extra-required" id="menu_item_extra_required_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][required][]" type="text" value="' . $required_num . '" placeholder="' . __('Required options', 'foodbakery') . '">
												</div></div>';
                    
                    $form_extra_html    = apply_filters('foodbakery_extras_main_fields_backend', $form_extra_html, $menu_item_counter, $menu_item_extra, $key);
                    
                    $form_extra_html    .= '<a class= "cross-icon" href="javascript:void(0);" onClick="remove_more_extra_option_heading(\'' . $key . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\');"><i class="icon-cross-out"></i></a>';
                    
                    if (is_array($menu_item_extra_titles) && sizeof($menu_item_extra_titles) > 0) {
                        
                        $count_extra_inner_li_next = 0;
                        foreach ($menu_item_extra_titles as $key => $menu_item_extra_title) {
                            $menu_item_extra_price = isset($menu_item_extra_prices[$key]) ? $menu_item_extra_prices[$key] : '';
                            
                            $form_extra_html .= '<ul id="menu-item-extra-fields-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li_next.'" class="menu-item-extra-fields">
												<li id="menu-item-extra-field-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li_next. '" class="menu-item-extra-field">
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
														<div class="row">
															<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																<div class="field-holder">
																	<label>' . __('Title', 'foodbakery') . '</label>
																	<input class="menu-item-extra-title" id="menu_item_extra_title_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][title][]" type="text" value="' . esc_attr($menu_item_extra_title) . '" placeholder="' . __('Title', 'foodbakery') . '">
																</div>
															</div>
															<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																<div class="field-holder">
																	<label>' . __('Price', 'foodbakery') . ' (' . $currency_sign . ')</label>
																	<input class="menu-item-extra-price" id="menu_item_extra_price_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][price][]" type="text" value="' . esc_attr($menu_item_extra_price) . '" placeholder="' . __('Price', 'foodbakery') . '">
																</div>
															</div>
															<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																<div class="menu-item-extra-options">
																	<label>&nbsp;</label>
																	<a href="javascript:void(0);" onClick="add_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . __('Title', 'foodbakery') . '\',\'' . __('Price', 'foodbakery') . '\',\'' . $currency_sign . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li_next . '\');">+</a>
																	<a href="javascript:void(0);" onClick="remove_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li_next . '\');">-</a>
																</div>
															</div>
														</div>
													</div>
												</li>
											</ul>';
                            
                            $count_extra_inner_li_next++;
                            
                        }
                    }
                    $form_extra_html .= '</div>';
                    $form_extra_html .= '</div>';
                    $form_extra_html .= '</li>';
                    $count_extra_li ++;
                    $count_extra_inner_li ++;
                    }
                }
                return $form_extra_html;
            }
        }

        public function foodbakery_admin_restaurant_menu_items_extra_fields_callback($restaurant_add_counter = '', $menu_item_counter = '', $menu_item_extra = '') {
            global $foodbakery_plugin_options, $foodbakery_html_fields;
            $currency_sign = foodbakery_get_currency_sign();
            $restaurant_add_counter = foodbakery_get_input('restaurant_ad_counter', '', 'STRING');
            $menu_item_counter = foodbakery_get_input('menu_item_counter', '', 'STRING');
            $count_extra_li = foodbakery_get_input('count_extra_li', '', 'STRING');
            $count_extra_inner_li = foodbakery_get_input('count_extra_inner_li', '', 'STRING');
            
            $menu_extra_counter = rand(123456789, 987654329);
            
            $form_extra_html = '';
            $form_extra_html .= '<li class="menu-item-extra-' . $menu_item_counter . '">';
            $form_extra_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
            $form_extra_html .= '<div class="row">  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
								<div class="field-holder">
									<label>' . __('Heading', 'foodbakery') . '</label>
									<input class="menu-item-extra-heading" id="menu_item_extra_heading_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][heading][]" value="" type="text" placeholder="' . __('Heading', 'foodbakery') . '">
                                                                       							
                                                                </div>
							</div>
                                                           <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                            <label>'.esc_html__('Extra Type', 'foodbakery').'</label>';
            $foodbakery_opt_array = array('name' => esc_html__('Extra Type', 'foodbakery'),
                'desc' => '',
                'id' => 'menu_item_extra' . $menu_item_counter . '',
                'cust_name' => 'menu_item_extra[' . $menu_item_counter . '][type][]',
                'std' => '',
                'classes' => 'chosen-select',
                'options' => array(
                    'single' => esc_html__('Single (Radio button)', 'foodbakery'),
                    'multiple' => esc_html__('Multiple (CheckBoxes)', 'foodbakery'),
                ),
                'return' => true,
            );
            $form_extra_html .= $foodbakery_html_fields->foodbakery_form_select_render($foodbakery_opt_array);
            $form_extra_html .= '</div><div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
												<div class="field-holder">
													<label>' . __('Required', 'foodbakery') . '</label>
													<input class="menu-item-extra-required" id="menu_item_extra_required_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][required][]" type="text" value="1" placeholder="' . __('Required options', 'foodbakery') . '">
												</div>
											</div>';
            $form_extra_html    = apply_filters('foodbakery_extras_main_fields_backend', $form_extra_html, $menu_item_counter);
            
            $form_extra_html    .= '<a class="cross-icon remove_extra_li_'.$menu_extra_counter.'" href="javascript:void(0);" onClick="remove_more_extra_option_heading_extra(\'' . $menu_extra_counter . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\');"><i class="icon-cross-out"></i></a>	
                                    <ul id="menu-item-extra-fields-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li.'" class="menu-item-extra-fields">
								<li id="menu-item-extra-field-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li.'" class="menu-item-extra-field">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="row">
											<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
												<div class="field-holder">
													<label>' . __('Title', 'foodbakery') . '</label>
													<input class="menu-item-extra-title" id="menu_item_extra_title_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][title][]" type="text" value="" placeholder="' . __('Title', 'foodbakery') . '">
												</div>
											</div>
											<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
												<div class="field-holder">
													<label>' . __('Price', 'foodbakery') . ' (' . $currency_sign . ')</label>
													<input class="menu-item-extra-price" id="menu_item_extra_price_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][price][]" type="text" value="" placeholder="' . __('Price', 'foodbakery') . '">
												</div>
											</div>
											<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
												<div class="menu-item-extra-options">
													<label>&nbsp;</label>
													<a href="javascript:void(0);" onClick="add_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . __('Title', 'foodbakery') . '\',\'' . __('Price', 'foodbakery') . '\',\'' . $currency_sign . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li . '\');">+</a>
													<a href="javascript:void(0);" onClick="remove_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li . '\');">-</a>
												</div>
											</div>
										</div>
									</div>
								</li>
							</ul>';
            $form_extra_html .= '</div>';
            $form_extra_html .= '</div>';
            $form_extra_html .= '</li>';
            echo json_encode(array('html' => $form_extra_html, 'type' => 'success', 'msg' => __('Menu item extra added successfully', 'foodbakery')));
            die;
        }

        /**
         * Appending menu items to list via Ajax
         * @return markup
         */
        public function foodbakery_admin_add_menu_item_to_list_callback($get_menu_items = array(), $form_action = 'edit') {

            global $foodbakery_plugin_options, $foodbakery_form_fields;
            $currency_sign = foodbakery_get_currency_sign();
            if (is_array($get_menu_items) && sizeof($get_menu_items) > 0) {

                $restaurant_menu = isset($get_menu_items['restaurant_menu']) ? $get_menu_items['restaurant_menu'] : '';
                $menu_item_title = isset($get_menu_items['menu_item_title']) ? $get_menu_items['menu_item_title'] : '';
                $menu_item_price = isset($get_menu_items['menu_item_price']) ? $get_menu_items['menu_item_price'] : '';
                $menu_item_icon = isset($get_menu_items['menu_item_icon']) ? $get_menu_items['menu_item_icon'] : '';
                $menu_item_nutri = isset($get_menu_items['menu_item_nutri']) ? $get_menu_items['menu_item_nutri'] : '';
                $menu_item_desc = isset($get_menu_items['menu_item_description']) ? $get_menu_items['menu_item_description'] : '';
                $menu_item_extra = isset($get_menu_items['menu_item_extra']) ? $get_menu_items['menu_item_extra'] : '';
                $menu_item_counter = isset($get_menu_items['menu_item_counter']) ? $get_menu_items['menu_item_counter'] : '';
                $restaurant_ad_counter = isset($get_menu_items['restaurant_ad_counter']) ? $get_menu_items['restaurant_ad_counter'] : '';
            } else {
                $menu_item_counter = foodbakery_get_input('menu_item_counter', '', 'STRING');
                $restaurant_ad_counter = foodbakery_get_input('restaurant_ad_counter', '', 'STRING');
                $menu_item_add_action = foodbakery_get_input('menu_item_add_action', '', 'STRING');
                $restaurant_menu = isset($_POST['restaurant_menu'][$menu_item_counter]) ? $_POST['restaurant_menu'][$menu_item_counter] : '';
                $menu_item_title = isset($_POST['menu_item_title'][$menu_item_counter]) ? $_POST['menu_item_title'][$menu_item_counter] : '';
                $menu_item_price = isset($_POST['menu_item_price'][$menu_item_counter]) ? $_POST['menu_item_price'][$menu_item_counter] : '';
                $menu_item_icon = isset($_POST['menu_item_icon'][$menu_item_counter]) ? $_POST['menu_item_icon'][$menu_item_counter] : '';
                $menu_item_nutri = isset($_POST['menu_item_nutri']) ? $_POST['menu_item_nutri'] : '';
                $menu_item_desc = isset($_POST['menu_item_description'][$menu_item_counter]) ? $_POST['menu_item_description'][$menu_item_counter] : '';
                if (empty($menu_item_desc)) {
                    $menu_item_desc = isset($_POST['menu_item_desc'][$menu_item_counter]) ? $_POST['menu_item_desc'][$menu_item_counter] : '';
                }
                $menu_item_extra = isset($_POST['menu_item_extra'][$menu_item_counter]) ? $_POST['menu_item_extra'][$menu_item_counter] : '';
            }

            if (isset($menu_item_icon) && is_array($menu_item_icon) && !empty($menu_item_icon)) {
                $menu_item_icon = $menu_item_icon[0];
            }
            $_icon_html = '&nbsp';
            if ($menu_item_icon != '') {
                $_icon_html = '<i class="' . $menu_item_icon . '"></i>';
            }

            $menu_item_fields = array(
                'restaurant_menu' => $restaurant_menu,
                'menu_item_title' => $menu_item_title,
                'menu_item_price' => $menu_item_price,
                'menu_item_icon' => $menu_item_icon,
                'menu_item_nutri' => $menu_item_nutri,
                'menu_item_desc' => $menu_item_desc,
                'menu_item_extra' => $menu_item_extra,
            );
            $menu_item_counter = rand(123456789, 987654321);
            $html = ' <li class="menu-item-' . $menu_item_counter . '">
				<div class="drag-list">
					<span class="drag-option"><i class="icon-bars"></i></span>
					<div class="icon-holder">
						' . $_icon_html . '
					</div>
					<div class="list-title">
						<h6>' . $menu_item_title . '</h6>
					</div>
					<div class="list-price">
						<span>'.currency_symbol_possitions_html('<b>' . $currency_sign . '</b>', '<b>' . $menu_item_price . '</b>').'</span>
					</div>
					<div class="list-option">
						<a href="javascript:void(0);" class="edit-menu-item" onClick="foodbakery_add_menu_item(\'' . $menu_item_counter . '\');">' . __('Edit', 'foodbakery') . '</a>
						<a href="javascript:void(0);" class="remove-menu-item" onClick="foodbakery_remove_menu_item(\'' . $menu_item_counter . '\');"><i class="icon-cross-out"></i></a>
					</div>
				</div>';
            $html .= '<div id="add-menu-item-from-' . $menu_item_counter . '" style="display:none;">';
            $html .= $this->foodbakery_restaurant_menu_items_form_ui($restaurant_ad_counter, $menu_item_counter, $menu_item_fields, $form_action);
            $html .= '</div>';
            $html .= '</li>';
            if (is_array($get_menu_items) && sizeof($get_menu_items) > 0) {
                return apply_filters('foodbakery_front_restaurant_add_single_service', $html, $get_menu_items);
            } else {
                if ($menu_item_add_action == 'edit') {
                    $message = __('Menu item updated successfully', 'foodbakery');
                } else {
                    $message = __('Menu item added successfully', 'foodbakery');
                }
                echo json_encode(array('html' => $html, 'type' => 'success', 'msg' => $message));
                die;
            }
        }

        /**
         * Menu Items Grouping With Menus
         */
        public function group_restaurant_menu_items($restaurant_menu_list = '', $restaurant_add_counter = '') {

            if (is_array($restaurant_menu_list) && sizeof($restaurant_menu_list) > 0) {
                $total_items = count($restaurant_menu_list);
                $total_menu = array();
                $menu_items_list = '';
                for ($menu_count = 0; $menu_count < $total_items; $menu_count ++) {
                    if (isset($restaurant_menu_list[$menu_count]['restaurant_menu'])) {
                        $menu_exists = in_array($restaurant_menu_list[$menu_count]['restaurant_menu'], $total_menu);
                        if (!$menu_exists) {
                            $total_menu[] = $restaurant_menu_list[$menu_count]['restaurant_menu'];
                        }
                    }
                }
                $total_menu_count = count($total_menu);
                $rand_menu_id = rand(1000, 99999);
                for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop ++) {
                    $restaurant_menu = esc_html($total_menu[$menu_loop]);
                    $restaurant_menu_slug = str_replace(' ', '-', strtolower($restaurant_menu));
                    $menu_items_list .= '<li id="menu-' . $restaurant_menu_slug . '">';
                    $menu_items_list .= '';
                    $menu_items_list .= '<div class="element-title">';
                    $menu_items_list .= '<span class="drag-option ui-sortable-handle"><i class="icon-bars"></i></span> ' . $restaurant_menu;
                    $menu_items_list .= '</div>';
                    $menu_items_list .= '<ul class="menu-items-list">';
                    for ($menu_items_loop = 0; $menu_items_loop < $total_items; $menu_items_loop ++) {
                        $menu_item_counter = rand(123456789, 987654321);
                        if (isset($restaurant_menu_list[$menu_items_loop]['restaurant_menu']) && $total_menu[$menu_loop] == $restaurant_menu_list[$menu_items_loop]['restaurant_menu']) {
                            $menu_item_title = isset($restaurant_menu_list[$menu_items_loop]['menu_item_title']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_title'] : '';
                            $menu_item_description = isset($restaurant_menu_list[$menu_items_loop]['menu_item_description']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_description'] : '';
                            $menu_item_icon = isset($restaurant_menu_list[$menu_items_loop]['menu_item_icon']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_icon'] : '';
                            $menu_item_nutri = isset($restaurant_menu_list[$menu_items_loop]['menu_item_nutri']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_nutri'] : '';
                            $menu_item_price = isset($restaurant_menu_list[$menu_items_loop]['menu_item_price']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_price'] : '';
                            $menu_item_extra = isset($restaurant_menu_list[$menu_items_loop]['menu_item_extra']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_extra'] : '';
                            $get_menu_items = array(
                                'menu_item_id' => $rand_menu_id,
                                'restaurant_menu' => $restaurant_menu,
                                'menu_item_title' => $menu_item_title,
                                'menu_item_description' => $menu_item_description,
                                'menu_item_icon' => $menu_item_icon,
                                'menu_item_nutri' => $menu_item_nutri,
                                'menu_item_price' => $menu_item_price,
                                'menu_item_extra' => $menu_item_extra,
                                'menu_item_counter' => $menu_item_counter,
                                'restaurant_ad_counter' => $restaurant_add_counter,
                            );
                            $menu_items_list .= $this->foodbakery_admin_add_menu_item_to_list_callback($get_menu_items, 'edit');
                        }
                    }
                    $menu_items_list .= '</ul>';
                    $menu_items_list .= '</li>';
                }

                return $menu_items_list;
            }
        }

        public function foodbakery_insert_menu_items($restaurant_id) {
            if (get_post_type($restaurant_id) == 'restaurants') {
                // saving restaurant menu items
                $foodbakery_restaurant_menu_item_title = foodbakery_get_input('menu_item_title', '', 'ARRAY');
                $foodbakery_restaurants_menu = foodbakery_get_input('restaurant_menu', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_price = foodbakery_get_input('menu_item_price', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_icon = foodbakery_get_input('menu_item_icon', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_nutri = foodbakery_get_input('menu_item_nutri', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_desc = foodbakery_get_input('menu_item_desc', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_extra = foodbakery_get_input('menu_item_extra', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_action = foodbakery_get_input('menu_item_action', '', 'ARRAY');
                $rand_menu_id = rand(1000, 99999);

                $extras_list = array();
            if($foodbakery_restaurant_menu_item_extra){
                foreach ($foodbakery_restaurant_menu_item_extra as $key => $extras) {
                    $extra_list = array();
                    foreach ($extras as $child_key => $extra) {
                        if ($child_key == 'heading' || $child_key == 'type' || $child_key == 'required') {
                            $extra_list[$child_key] = $extra;
                        } else {
                            $extra_list[] = $extra;
                        }
                    }
                    $extras_list[$key] = $extra_list;
                }
            }
                $extras_list    = apply_filters('foodbakery_insert_menu_items_backend', $extras_list, $foodbakery_restaurant_menu_item_extra);
                
                if (isset($_POST['menu_item_title'])) {
                    if (is_array($foodbakery_restaurant_menu_item_title) && sizeof($foodbakery_restaurant_menu_item_title) > 0) {
                        $menu_items_array = array();
                        foreach ($foodbakery_restaurant_menu_item_title as $key => $menu_item) {
                            $menu_item_action = isset($foodbakery_restaurant_menu_item_action[$key]) ? $foodbakery_restaurant_menu_item_action[$key] : '';
                            if ($menu_item != '' && $menu_item_action != 'add') {
                                $menu_items_array[] = array(
                                    'menu_item_id' => $rand_menu_id,
                                    'menu_item_counter' => $rand_menu_id,
                                    'menu_item_title' => $menu_item,
                                    'restaurant_menu' => isset($foodbakery_restaurants_menu[$key]) ? $foodbakery_restaurants_menu[$key] : '',
                                    'menu_item_description' => isset($foodbakery_restaurant_menu_item_desc[$key]) ? $foodbakery_restaurant_menu_item_desc[$key] : '',
                                    'menu_item_icon' => isset($foodbakery_restaurant_menu_item_icon[$key]) ? $foodbakery_restaurant_menu_item_icon[$key] : '',
                                    'menu_item_nutri' => isset($foodbakery_restaurant_menu_item_nutri[$key]) ? $foodbakery_restaurant_menu_item_nutri[$key] : '',
                                    'menu_item_price' => isset($foodbakery_restaurant_menu_item_price[$key]) ? $foodbakery_restaurant_menu_item_price[$key] : '',
                                    'menu_item_extra' => isset($extras_list[$key]) ? $extras_list[$key] : '',
                                );
                            }
                        }
                        //pre($menu_items_array);
                       // exit();
                        update_post_meta($restaurant_id, 'foodbakery_menu_items', $menu_items_array);
                    } else {
                        delete_post_meta($restaurant_id, 'foodbakery_menu_items');
                    }
                }
            }
        }

    }

    // end class
    // Initialize Object
    $foodbakery_admin_restaurant_menus = new Foodbakery_Admin_Restaurant_Menus();
}