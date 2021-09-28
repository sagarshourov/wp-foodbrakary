<?php

/**
 * File Type: Restaurant Menus
 */
if (!class_exists('Foodbakery_Restaurant_Menus')) {

    class Foodbakery_Restaurant_Menus {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_filter('foodbakery_restaurant_menu_items', array($this, 'foodbakery_restaurant_menu_items_callback'), 10, 3);
            add_action('wp_ajax_foodbakery_add_menu_item_to_list', array($this, 'foodbakery_add_menu_item_to_list_callback'));
            add_action('wp_ajax_foodbakery_restaurant_menu_items_extra_fields', array($this, 'foodbakery_restaurant_menu_items_extra_fields_callback'));
            add_action('wp_ajax_restaurant_menu_add_icon_img', array($this, 'restaurant_menu_add_icon_img_callback'));
            add_action('wp_ajax_nopriv_restaurant_menu_add_icon_img', array($this, 'restaurant_menu_add_icon_img_callback'));
        }

        public function restaurant_menu_add_icon_img_callback() {

            $image_file = isset($_FILES['menu_item_icon_file']) ? $_FILES['menu_item_icon_file'] : '';

            if (isset($image_file['name'])) {
                require_once ABSPATH . 'wp-admin/includes/image.php';
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                $allowed_image_types = array(
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                );

                $status = wp_handle_upload($image_file, array('test_form' => false, 'mimes' => $allowed_image_types));

                if (empty($status['error'])) {

                    $image = wp_get_image_editor($status['file']);
                    $img_resized_name = $status['file'];

                    if (is_wp_error($image)) {

                        echo json_encode(array('type' => 'error', 'msg' => $image->get_error_message()));
                        die;
                    } else {
                        $wp_upload_dir = wp_upload_dir();
                        $filename = isset($status['url']) ? $status['url'] : '';
                        $filetype = wp_check_filetype(basename($filename), null);

                        if ($filename != '') {
                            // Prepare an array of post data for the attachment.

                            $attachment = array(
                                'guid' => ($filename),
                                'post_mime_type' => $filetype['type'],
                                'post_title' => preg_replace('/\.[^.]+$/', '', ($image_file['name'])),
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );
                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                            // Insert the attachment.
                            $attach_id = wp_insert_attachment($attachment, $status['file']);

                            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                            $attach_data = wp_generate_attachment_metadata($attach_id, $status['file']);
                            wp_update_attachment_metadata($attach_id, $attach_data);

                            $attach_img_arr = wp_get_attachment_image_src($attach_id, 'thumbnail');
                            $attach_img_src = isset($attach_img_arr[0]) ? $attach_img_arr[0] : '';

                            echo json_encode(array('type' => 'success', 'msg' => esc_html__('Icon Uploaded.', 'foodbakery'), 'attach_id' => $attach_id, 'attach_src' => $attach_img_src));
                            die;
                        }
                        echo json_encode(array('type' => 'error', 'msg' => esc_html__('Problem in uploading File.', 'foodbakery')));
                        die;
                    }
                } else {
                    echo json_encode(array('type' => 'error', 'msg' => esc_html__('Upload image file only.', 'foodbakery')));
                    die;
                }
            }
            die;
        }

        public function foodbakery_restaurant_menu_items_callback($restaurant_add_counter = '', $type_id = '', $foodbakery_id = '') {
            global $foodbakery_plugin_options, $foodbakery_form_fields, $restaurant_add_counter;
            $currency_sign = foodbakery_get_currency_sign();
            $html = '';
            $foodbakery_restaurant_services = get_post_meta($type_id, 'foodbakery_services_options_element', true);

            $menu_item_counter = rand(123456789, 987654321);
            $menu_items_list = '';

            // In case of changing foodbakery type ajax
            // it will load the pre filled data
            $get_restaurant_form_select_type = foodbakery_get_input('select_type', '', 'STRING');
            if ($get_restaurant_form_select_type != '') {
                $get_restaurant_form_restaurant_menu = foodbakery_get_input('restaurant_menu', '', 'ARRAY');
                $get_restaurant_form_menu_item_title = foodbakery_get_input('menu_item_title', '', 'ARRAY');
                $get_restaurant_form_menu_item_price = foodbakery_get_input('menu_item_price', '', 'ARRAY');
                $get_restaurant_form_menu_item_icon = foodbakery_get_input('menu_item_icon', '', 'ARRAY');
                $get_restaurant_form_menu_item_nutri = foodbakery_get_input('menu_item_nutri', '', 'ARRAY');
                $get_restaurant_form_menu_item_desc = foodbakery_get_input('menu_item_desc', '', 'ARRAY');
                $get_restaurant_form_menu_item_extra = foodbakery_get_input('menu_item_extra', '', 'ARRAY');

                $form_menu_items_array = array();
                if (is_array($get_restaurant_form_menu_item_title) && sizeof($get_restaurant_form_menu_item_title) > 0) {
                    foreach ($get_restaurant_form_menu_item_title as $menu_item_key => $menu_item) {

                        if (count($menu_item) > 0) {
                            $form_menu_items_array[] = array(
                                'restaurant_menu' => isset($get_restaurant_form_restaurant_menu[$menu_item_key]) ? $get_restaurant_form_restaurant_menu[$menu_item_key] : '',
                                'menu_item_title' => $menu_item,
                                'menu_item_description' => isset($get_restaurant_form_menu_item_desc[$menu_item_key]) ? $get_restaurant_form_menu_item_desc[$menu_item_key] : '',
                                'menu_item_icon' => isset($get_restaurant_form_menu_item_icon[$menu_item_key]) ? $get_restaurant_form_menu_item_icon[$menu_item_key] : '',
                                'menu_item_nutri' => isset($get_restaurant_form_menu_item_nutri[$menu_item_key]) ? $get_restaurant_form_menu_item_nutri[$menu_item_key] : '',
                                'menu_item_price' => isset($get_restaurant_form_menu_item_price[$menu_item_key]) ? $get_restaurant_form_menu_item_price[$menu_item_key] : '',
                                'menu_item_extra' => isset($get_restaurant_form_menu_item_extra[$menu_item_key]) ? $get_restaurant_form_menu_item_extra[$menu_item_key] : '',
                                'menu_item_counter' => $menu_item_counter,
                                'restaurant_ad_counter' => $restaurant_add_counter,
                            );
                        }
                    }
                }

                if (sizeof($form_menu_items_array) > 0) {
                    $menu_items_list .= $this->group_restaurant_menu_items($form_menu_items_array, $restaurant_add_counter);
                }
            }
            // end ajax load

            $get_restaurant_id = $foodbakery_id;

            if ($get_restaurant_id != '' && $get_restaurant_id != 0 && $this->is_publisher_restaurant($get_restaurant_id)) {
                $get_restaurant_menu_items = get_post_meta($get_restaurant_id, 'foodbakery_menu_items', true);
                $menu_items_list .= $this->group_restaurant_menu_items($get_restaurant_menu_items, $restaurant_add_counter);
            }
            if ($menu_items_list == '') {
                $menu_items_list = '<li id="no-menu-items-' . $restaurant_add_counter . '" class="no-result-msg">' . esc_html__('No Menu Items added.', 'foodbakery') . '</li>';
            }

            $html .= '
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<h5>' . esc_html__('Food Items', 'foodbakery') . '</h5>
						<div id="menu-item-loader-' . $restaurant_add_counter . '" class="restaurant-loader"></div>
						<a id="restaurant-menu-items-btn-' . $restaurant_add_counter . '" class="add-menu-item" href="javascript:void(0);" onClick="javascript:foodbakery_add_menu_item(\'' . $restaurant_add_counter . '\');">' . esc_html__('Add Menu Item', 'foodbakery') . '</a>
					</div>
				</div>
				<form  id="sagar_add_form_'. $restaurant_add_counter .'" method="post" enctype="multipart/form-data"> <div class="sagar1" id="add-menu-item-from-' . $restaurant_add_counter . '" style="display:none;">';
            $html .= $this->foodbakery_restaurant_menu_items_form_ui($restaurant_add_counter, $menu_item_counter, '', 'add');
            $html .= '</div> </form>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<div class="service-list ">
							<ul id="restaurant_menu_items-list-' . $restaurant_add_counter . '" class="restaurant_menu_items_cat_list panel-group">
								' . $menu_items_list . '
							</ul>
						</div>
					</div>
				</div>
			</div>';

            return apply_filters('foodbakery_front_restaurant_add_menu_items2', $html, $type_id, $foodbakery_id);
        }

        public function foodbakery_restaurant_menu_items_form_ui($restaurant_add_counter = '', $menu_item_counter = '', $get_menu_items = array(), $form_action = 'edit') {
            global $foodbakery_plugin_options, $foodbakery_form_fields, $foodbakery_html_fields, $restaurant_add_counter;

            //print_r($get_menu_items);

            $current_user = wp_get_current_user();
            $publisher_id = foodbakery_company_id_form_user_id($current_user->ID);

            $restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
            $restaurants_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : '';

            $args = array(
                'posts_per_page' => "1",
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                'fields' => 'ids',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_restaurant_publisher',
                        'value' => $publisher_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_restaurant_username',
                        'value' => $current_user->ID,
                        'compare' => '=',
                    ),
                ),
            );
            $custom_query = new WP_Query($args);
            $pub_restaurant = $custom_query->posts;

            if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
                $restaurant_id = $pub_restaurant[0];
            } else {
                $restaurant_id = 0;
            }

            wp_enqueue_style('jquery-te');
            wp_enqueue_script('jquery-te');

            wp_enqueue_style('fonticonpicker');
            wp_enqueue_script('fonticonpicker');
            $currency_sign = foodbakery_get_currency_sign();

            $restaurant_menu = isset($get_menu_items['restaurant_menu']) ? stripslashes($get_menu_items['restaurant_menu']) : '';
            $menu_item_title = isset($get_menu_items['menu_item_title']) ? stripslashes($get_menu_items['menu_item_title']) : '';
            $menu_item_post_status_get = isset($get_menu_items['menu_item_post_status']) ? stripslashes($get_menu_items['menu_item_post_status']) : '';
            $menu_item_comment = isset($get_menu_items['menu_item_comment']) ? stripslashes($get_menu_items['menu_item_comment']) : '';
            $menu_item_price = isset($get_menu_items['menu_item_price']) ? $get_menu_items['menu_item_price'] : '';
            $menu_item_icon = isset($get_menu_items['menu_item_icon']) ? $get_menu_items['menu_item_icon'] : '';
            $menu_item_nutri = isset($get_menu_items['menu_item_nutri']) ? $get_menu_items['menu_item_nutri'] : '';
            $menu_item_desc = isset($get_menu_items['menu_item_desc']) ? stripslashes($get_menu_items['menu_item_desc']) : '';
            $menu_item_counter = isset($get_menu_items['menu_item_counter']) ? $get_menu_items['menu_item_counter'] : $menu_item_counter;

            if (!is_array($menu_item_nutri) && $menu_item_nutri != '') {
                $menu_item_nutri = explode(',', $menu_item_nutri);
            }

            $menu_item_extra = isset($get_menu_items['menu_item_extra']) ? $get_menu_items['menu_item_extra'] : '';

            $form_html = '';
            $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
            if ($form_action == 'edit') {
                $form_html .= '<a href="javascript:void(0);" onClick="foodbakery_close_menu_item(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\');" class="close-menu-item"><i class="icon-close2"></i></a>';
            } else {
                $form_html .= '<a href="javascript:void(0);" onClick="foodbakery_close_menu_lists(\'' . $restaurant_add_counter . '\');" class="close-menu-item"><i class="icon-close2"></i></a>';
            }
            $form_html .= '<div class="row">';
            $form_html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Restaurant Menu *', 'foodbakery') . '</label>';
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
            $foodbakery_opt_array['std'] = $restaurant_menu;
            $foodbakery_opt_array['cust_id'] = 'restaurant_menu_' . $menu_item_counter;
            if ($form_action == 'add') {
                $foodbakery_opt_array['cust_name'] = 'restaurant_menu_ading';
            } else {
                $foodbakery_opt_array['cust_name'] = 'restaurant_menu[' . $menu_item_counter . ']';
            }
            $foodbakery_opt_array['options'] = $restaurants_menus_options;
            $foodbakery_opt_array['classes'] = 'chosen-select';
            $foodbakery_opt_array['return'] = true;
            $form_html .= '<div class="restaurants-menu">';
            $form_html .= $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
            $form_html .= '</div>';
            $form_html .= '<script> jQuery(document).ready(function () { jQuery(".chosen-select").chosen(); }); </script>';

            $inselected = '';

            if ($menu_item_post_status_get == 'inherit') { //edit sagar
                $inselected = 'selected';
            }



            $form_html .= '
				</div>
			</div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
                <div class="field-holder">
					<label>' . esc_html__('Published*', 'foodbakery') . '</label>
                    <select name="menu_item_post_status[' . $menu_item_counter . ']">
                        <option value="publish">Published</option>
                        <option value="inherit" ' . $inselected . '>Un published</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
                <div class="field-holder">
					<label>' . esc_html__('Comment', 'foodbakery') . '</label>
                    <textarea class="menu-item-desc foodbakery_editor" placeholder="Comment"  name="menu_item_comment[' . $menu_item_counter . ']">
                    ' . $menu_item_comment . '
                    </textarea>
                </div>
            </div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Title *', 'foodbakery') . '</label>
					<input id="current_restaurant_menu_' . $menu_item_counter . '" type="hidden" value="' . esc_attr($restaurant_menu) . '">';
            if ($form_action != 'add') {
                $form_html .= '<input name="menu_item_action[' . $menu_item_counter . ']" value="' . esc_attr($form_action) . '" type="hidden">';
            }
            if ($form_action == 'add') {
                $form_html .= '<input class="menu-item-title" id="menu_item_title_' . $menu_item_counter . '" type="text" placeholder="' . esc_html__('Menu Item Title', 'foodbakery') . '">';
            } else {
                $form_html .= '<input class="menu-item-title " id="menu_item_title_' . $menu_item_counter . '" name="menu_item_title[' . $menu_item_counter . ']" value="' . esc_attr($menu_item_title) . '" type="text" placeholder="' . esc_html__('Menu Item Title', 'foodbakery') . '">';
            }
            $form_html .= '
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Price *', 'foodbakery') . ' (' . $currency_sign . ')</label>';
            if ($form_action == 'add') {
                $form_html .= '<input class="menu-item-price" id="menu_item_price_' . $menu_item_counter . '" type="text" placeholder="' . esc_html__('Menu Item Price', 'foodbakery') . '">';
            } else {
                $form_html .= '<input class="menu-item-price" id="menu_item_price_' . $menu_item_counter . '" name="menu_item_price[' . $menu_item_counter . ']" type="text" value="' . esc_attr($menu_item_price) . '" placeholder="' . esc_html__('Menu Item Price', 'foodbakery') . '">';
            }
            $form_html .= '
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Food Image', 'foodbakery') . '</label>';

            $browse_btn_dis = 'bolck';
            $browse_img_dis = 'none';
            $menu_item_icon_img_src = '';
            if ($menu_item_icon != '') {
                $browse_btn_dis = 'none';
                $browse_img_dis = 'bolck';
                $menu_item_icon_img_arr = wp_get_attachment_image_src($menu_item_icon, 'thumbnail');
                $menu_item_icon_img_src = isset($menu_item_icon_img_arr[0]) ? $menu_item_icon_img_arr[0] : '';
            }
            $form_html .= '
			<div id="browse-btn-sec-' . $menu_item_counter . '" class="browse-btn-sec" style="display: ' . $browse_btn_dis . ' !important;">
				<input type="file" id="image-icon-' . $menu_item_counter . '" data-id="' . $menu_item_counter . '" name="image_icon_' . $menu_item_counter . '" class="browse-menu-icon-file" style="display: none;">
				<a id="browse-menu-icon-img-' . $menu_item_counter . '"  href="javascript:void(0)" class="browse-menu-icon-img btn bgcolor" data-id="' . $menu_item_counter . '">' . esc_html__('Browse', 'foodbakery') . '</a>
			</div>';
            $form_html .= '
			<div id="browse-img-sec-' . $menu_item_counter . '" class="browse-image-sec" style="display: ' . $browse_img_dis . ' !important;">
				<div class="icon-img-holder">
					<a href="javascript:void(0)" data-id="' . $menu_item_counter . '" class="remove-icon"><i class="icon-close2"></i></a>
					<img id="img-val-base-' . $menu_item_counter . '" src="' . $menu_item_icon_img_src . '" alt="" />
				</div>
				<input id="hiden-img-val-' . $menu_item_counter . '" type="hidden"' . ( $form_action == 'add' ? '' : ' name="menu_item_icon[' . $menu_item_counter . ']" value="' . $menu_item_icon . '"' ) . '>
			</div>';
            $form_html .= '
				</div>
			</div>';
            $get_foodbakeri_nutri_icons = get_post_meta($restaurants_type_id, 'nutri_icon_imgs', true);
            $get_foodbakeri_nutri_titles = get_post_meta($restaurants_type_id, 'nutri_icon_titles', true);
            if (is_array($get_foodbakeri_nutri_icons) && sizeof($get_foodbakeri_nutri_icons) > 0) {
                $form_html .= '
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
                    $nutri_count++;
                }
                $form_html .= '</ul></div>';
                $form_html .= '
					</div>
				</div>';
            }
            $form_html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Description', 'foodbakery') . '</label>';

            if ($form_action == 'add') {
                $form_html .= '<textarea class="menu-item-desc foodbakery_editor" id="menu_item_desc_' . $menu_item_counter . '" placeholder="' . esc_html__('Menu Item Description test', 'foodbakery') . '"></textarea>';
            } else {
                $form_html .= '<textarea class="menu-item-desc foodbakery_editor" id="menu_item_desc_' . $menu_item_counter . '" name="menu_item_desc[' . $menu_item_counter . ']" placeholder="' . esc_html__('Menu Item Description test', 'foodbakery') . '">' . esc_attr($menu_item_desc) . '</textarea>';
            }
            $form_html .= '
				</div>
			</div>';

            $form_html .= '<ul id="menu-item-extra-list-' . $menu_item_counter . '" class="menu-item-extra-list">';
            $form_html .= $this->foodbakery_restaurant_menu_items_extra_saved_fields($restaurant_add_counter, $menu_item_counter, $menu_item_extra);
            $form_html .= '</ul>';
            if ($form_action == 'edit') {
                $button_label = esc_html__('Save', 'foodbakery');
                $action = 'edit';
            } else {
                $button_label = esc_html__('Add to Menu Item list', 'foodbakery');
                $action = 'add';
            }
            $form_html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<a class="add-menu-item-extra add-menu-item-extra-' . $menu_item_counter . '" href="javascript:void(0);" onClick="foodbakery_add_menu_item_extra(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\');">' . esc_html__('Add Menu Item Extra', 'foodbakery') . '</a>
					<a class="add-menu-item add-menu-item-list add-menu-item-list-' . $menu_item_counter . '" href="javascript:void(0);" onClick="foodbakery_add_menu_item_to_list(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . $action . '\');">' . $button_label . '</a>
				</div>
			</div>';

            $form_html .= '</div>';
            $form_html .= '</div>';
            return $form_html;
        }

        public function foodbakery_restaurant_menu_items_extra_saved_fields($restaurant_add_counter = '', $menu_item_counter = '', $menu_item_extra = '') {
            global $foodbakery_plugin_options, $restaurant_add_counter, $foodbakery_html_fields;
            $currency_sign = foodbakery_get_currency_sign();



			foreach ($menu_item_extra['heading'] as $key => $value) {
				 $menu_item_extra_titles = isset($menu_item_extra[$key]['title']) ? $menu_item_extra[$key]['title'] : array();
				 foreach ($menu_item_extra_titles as $key1 => $menu_item_extra_title) {
					 $condition_array[$value][] = $menu_item_extra_title;
				 }
				
			}
            if (isset($menu_item_extra['heading']) && is_array($menu_item_extra['heading']) && sizeof($menu_item_extra['heading']) > 0) {

                $form_extra_html = '';
                $count_extra_li = 0;
                $count_extra_inner_li = 0;
				$main = 0;
                foreach ($menu_item_extra['heading'] as $key => $value) {
                    $value_type = $menu_item_extra['type'][$key];
                    $required_num = $menu_item_extra['required'][$key];
                    $condition_num = $menu_item_extra['condition'][$key];
                    $menu_item_extra_titles = isset($menu_item_extra[$key]['title']) ? $menu_item_extra[$key]['title'] : array();
                    $menu_item_extra_subtitles = isset($menu_item_extra[$key]['subtitle']) ? $menu_item_extra[$key]['subtitle'] : array();
                    $menu_item_extra_prices = isset($menu_item_extra[$key]['price']) ? $menu_item_extra[$key]['price'] : array();
                    $menu_item_extra_precheck = isset($menu_item_extra[$key]['precheck']) ? $menu_item_extra[$key]['precheck'] : array();

                    $menu_item_extra_quantity = isset($menu_item_extra[$key]['quantity']) ? $menu_item_extra[$key]['quantity'] : array();

                    $form_extra_html .= '<li class="menu-item-extra-' . $menu_item_counter . '" id="extra_li_' . $menu_item_counter . $key . '">';
                    $form_extra_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                    $form_extra_html .= '<div class="row"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
										<div class="field-holder">
											<label>' . esc_html__('Heading', 'foodbakery') . '</label>
											<input class="menu-item-extra-heading" name="menu_item_extra[' . $menu_item_counter . '][heading][]" value="' . esc_attr($value) . '" type="text" placeholder="' . esc_html__('Heading', 'foodbakery') . '">
										</div>
									</div>
                                                                          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                        <label>' . esc_html__('Extra Type', 'foodbakery') . '</label>';
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
                    $form_extra_html .= '</div>              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 testing1133">
												<div class="field-holder">
													<label>' . __('Required', 'foodbakery') . '</label>
													<input class="menu-item-extra-required" id="menu_item_extra_required_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][required][]" type="text" value="' . $required_num . '" placeholder="' . __('Required options', 'foodbakery') . '">
												</div></div>';
												
												
										$form_extra_html .= '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
												<div class="field-holder">
													<label>' . __('Condition', 'foodbakery') . '</label>
													
													<select  class="menu-item-extra-condition" id="menu_item_extra_condition_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][condition][]" >
													<option value="select">----Select----</option>';
													$inner = 0;
													
													foreach($condition_array as $key3 => $conditionvalue){
														
														if($main > $inner ){
														$form_extra_html .= '<optgroup label="'.$key3.'">';
														foreach($conditionvalue as $k => $v){
															$option_value = $inner.'-'.$v;
															
															$selected = '';
															if($condition_num == $option_value){
																$selected = 'selected';
															}
															$form_extra_html .= '<option value="'.$option_value.'" '.$selected.'>'.$v.'</option>';
														}
														$form_extra_html .= '</optgroup>';
														}
														$inner++;
													}
													$form_extra_html .= '</select>
													
												</div></div>';
                    $form_extra_html = apply_filters('foodbakery_extras_main_fields_backend', $form_extra_html, $menu_item_counter, $menu_item_extra, $key);

                    $form_extra_html .= '<a class="cross-icon" href="javascript:void(0);" onClick="remove_more_extra_option_heading(\'' . $key . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\');"><i class="icon-cross-out"></i></a>';
                    if (is_array($menu_item_extra_titles) && sizeof($menu_item_extra_titles) > 0) {
                        $count_extra_inner_li_next = 0;
                        foreach ($menu_item_extra_titles as $key => $menu_item_extra_title) {
                            $menu_item_extra_price = isset($menu_item_extra_prices[$key]) ? $menu_item_extra_prices[$key] : '';


                            $menu_item_subtitle = isset($menu_item_extra_subtitles[$key]) ? $menu_item_extra_subtitles[$key] : '';

                              

                            $menu_item_extra_prechecked = isset($menu_item_extra_precheck[$key]) ? $menu_item_extra_precheck[$key] : '';

                            $precheck = '';

                            if ($menu_item_extra_prechecked == 'on') {
                                $precheck = 'checked';
                            }


                            $form_extra_html .= '<ul id="menu-item-extra-fields-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li_next . '" class="menu-item-extra-fields">
												<li id="menu-item-extra-field-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li_next . '" class="menu-item-extra-field">
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
														<div class="row">
															<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
																<div class="field-holder">
																	<label>' . esc_html__('Title', 'foodbakery') . '</label>
																	<input class="menu-item-extra-title" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_inner_li . '][title][]" type="text" value="' . esc_attr($menu_item_extra_title) . '" placeholder="' . esc_html__('Title', 'foodbakery') . '">
																</div>
															</div>
                                                             <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                                <div class="field-holder">
                                                                    <label>' . esc_html__('Subtitle', 'foodbakery') . '</label>
                                                                    <input class="menu-item-extra-title" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][subtitle][]" type="text" value="'.$menu_item_subtitle.'" placeholder="' . esc_html__('Subtitle', 'foodbakery') . '">
                                                                </div>
                                                            </div>
															<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
																<div class="field-holder">
																	<label>' . esc_html__('Price', 'foodbakery') . ' (' . $currency_sign . ')</label>
																	<input class="menu-item-extra-price" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_inner_li . '][price][]" type="text" value="' . esc_attr($menu_item_extra_price) . '" placeholder="' . esc_html__('Price', 'foodbakery') . '">
																</div>
															</div>
                                                                                                                        <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12">
																<div class="field-holder">
																	<label>' . esc_html__('Quantity', 'foodbakery') . '</label>
																	<input class="menu-item-extra-price" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_inner_li . '][quantity][]" type="text" value="' . esc_attr($menu_item_extra_quantity[$key]) . '" placeholder="' . esc_html__('Unlimited', 'foodbakery') . '">
																</div>
															</div>
															<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
																<div class="menu-item-extra-options">
																	<label>&nbsp;</label>
																	<a href="javascript:void(0);" onClick="add_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . esc_html__('Title', 'foodbakery') . '\',\'' . esc_html__('Price', 'foodbakery') . '\',\'' . $currency_sign . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li_next . '\');">+</a>
																	<a href="javascript:void(0);" onClick="remove_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li_next . '\');">-</a>
																</div>
															</div>
                                                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
																<div class="menu-item-extra-options">
																	<label>&nbsp;</label>
                                                                    <input type="checkbox" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_inner_li . '][precheck][]" ' . $precheck . ' />
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
                    $count_extra_li++;
                    $count_extra_inner_li++;
					$main++;
                }
                return $form_extra_html;
            }
        }

        public function foodbakery_restaurant_menu_items_extra_fields_callback() {
            global $foodbakery_plugin_options, $restaurant_add_counter, $foodbakery_html_fields;
            $currency_sign = foodbakery_get_currency_sign();

            $restaurant_add_counter = foodbakery_get_input('restaurant_ad_counter', '', 'STRING');
            $menu_item_counter = foodbakery_get_input('menu_item_counter', '', 'STRING');
            $count_extra_li = foodbakery_get_input('count_extra_li', '', 'STRING');
            $count_extra_inner_li = foodbakery_get_input('count_extra_inner_li', '', 'STRING');
            $menu_extra_counter = rand(123456789, 987654329);
            $form_extra_html = '';
            $form_extra_html .= '<li class="menu-item-extra-' . $menu_item_counter . '">';
            $form_extra_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
            $form_extra_html .= '<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
				<div class="field-holder">
					<label>' . esc_html__('Heading', 'foodbakery') . '</label>
					<input class="menu-item-extra-heading" name="menu_item_extra[' . $menu_item_counter . '][heading][]" value="" type="text" placeholder="' . esc_html__('Heading', 'foodbakery') . '">
				</div>
			</div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                            <label>' . esc_html__('Extra Type', 'foodbakery') . '</label>';
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
            $form_extra_html .= '</div><div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 testing1122">
												<div class="field-holder">
													<label>' . __('Required', 'foodbakery') . '</label>
													<input class="menu-item-extra-required" id="menu_item_extra_required_' . $menu_item_counter . '" name="menu_item_extra[' . $menu_item_counter . '][required][]" type="text" value="1" placeholder="' . __('Required options', 'foodbakery') . '">
												</div>
											</div>';

            $form_extra_html = apply_filters('foodbakery_extras_main_fields_backend', $form_extra_html, $menu_item_counter);
            $form_extra_html .= '<a class="cross-icon remove_extra_li_' . $menu_extra_counter . '" href="javascript:void(0);" onClick="remove_more_extra_option_heading_extra(\'' . $menu_extra_counter . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\');"><i class="icon-cross-out"></i></a>
			<ul id="menu-item-extra-fields-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li . '" class="menu-item-extra-fields">
				<li id="menu-item-extra-field-' . $menu_item_counter . $count_extra_li . $count_extra_inner_li . '" class="menu-item-extra-field">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
								<div class="field-holder">
									<label>' . esc_html__('Title', 'foodbakery') . '</label>
									<input class="menu-item-extra-title" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][title][]" type="text" value="" placeholder="' . esc_html__('Title', 'foodbakery') . '">
								</div>
							</div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="field-holder">
                                    <label>' . esc_html__('Subtitle', 'foodbakery') . '</label>
                                    <input class="menu-item-extra-title" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][subtitle][]" type="text" value="" placeholder="' . esc_html__('Subtitle', 'foodbakery') . '">
                                </div>
                            </div>
							<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
								<div class="field-holder">
									<label>' . esc_html__('Price', 'foodbakery') . ' (' . $currency_sign . ')</label>
									<input class="menu-item-extra-price" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][price][]" type="text" value="" placeholder="' . esc_html__('Price', 'foodbakery') . '">
								</div>
							</div>
                                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
								<div class="field-holder">
									<label>' . esc_html__('Quantity', 'foodbakery') . ' </label>
									<input class="menu-item-extra-price" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][quantity][]" type="text" value="" placeholder="' . esc_html__('Unlimited', 'foodbakery') . '">
								</div>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
								<div class="menu-item-extra-options">
									<label>&nbsp;</label>
									<a href="javascript:void(0);" onClick="add_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . esc_html__('Title', 'foodbakery') . '\',\'' . esc_html__('Price', 'foodbakery') . '\',\'' . $currency_sign . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li . '\');">+</a>
									<a href="javascript:void(0);" onClick="remove_more_extra_option(\'' . $restaurant_add_counter . '\',\'' . $menu_item_counter . '\',\'' . $count_extra_li . '\',\'' . $count_extra_inner_li . '\');">-</a>
								</div>
							</div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
								<div class="menu-item-extra-options">
									<label>&nbsp;</label>
                                    <input type="checkbox" name="menu_item_extra[' . $menu_item_counter . '][' . $count_extra_li . '][precheck][]" />
								</div>
							</div>
                            
						</div>
					</div>
				</li>
			</ul>';
            $form_extra_html .= '</div>';
            $form_extra_html .= '</div>';
            $form_extra_html .= '</li>';
            echo json_encode(array('html' => $form_extra_html, 'type' => 'success', 'msg' => esc_html__('Menu item extra added successfully', 'foodbakery')));
            die;
        }

        /**
         * Appending menu items to list via Ajax
         * @return markup
         */
        public function foodbakery_add_menu_item_to_list_callback($get_menu_items = '', $form_action = 'edit') {
          // return $this->sa_add_item($get_menu_items, $form_action);
            global $foodbakery_plugin_options, $foodbakery_form_fields, $restaurant_add_counter;
            $currency_sign = foodbakery_get_currency_sign();
            if (is_array($get_menu_items) && sizeof($get_menu_items) > 0) {

                $restaurant_menu = isset($get_menu_items['restaurant_menu']) ? $get_menu_items['restaurant_menu'] : '';
                $menu_item_title = isset($get_menu_items['menu_item_title']) ? $get_menu_items['menu_item_title'] : '';

                $menu_item_post_status = isset($get_menu_items['menu_item_post_status']) ? $get_menu_items['menu_item_post_status'] : '';
                $menu_item_comment = isset($get_menu_items['menu_item_comment']) ? $get_menu_items['menu_item_comment'] : '';

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
                if (isset($_POST['menu_item_title']) && isset($_POST['menu_item_price']) && !is_array($_POST['menu_item_title'])) {
                    $restaurant_menu = isset($_POST['restaurant_menu']) ? $_POST['restaurant_menu'] : '';
                    $menu_item_title = isset($_POST['menu_item_title']) ? $_POST['menu_item_title'] : '';
                    $menu_item_price = isset($_POST['menu_item_price']) ? $_POST['menu_item_price'] : '';
                    $menu_item_icon = isset($_POST['menu_item_icon']) ? $_POST['menu_item_icon'] : '';
                    $menu_item_nutri = isset($_POST['menu_item_nutri']) ? $_POST['menu_item_nutri'] : '';
                    $menu_item_desc = isset($_POST['menu_item_desc']) ? $_POST['menu_item_desc'] : '';
                } else {
                    $restaurant_menu = isset($_POST['restaurant_menu'][$menu_item_counter]) ? $_POST['restaurant_menu'][$menu_item_counter] : '';
                    $menu_item_title = isset($_POST['menu_item_title'][$menu_item_counter]) ? $_POST['menu_item_title'][$menu_item_counter] : '';
                    $menu_item_price = isset($_POST['menu_item_price'][$menu_item_counter]) ? $_POST['menu_item_price'][$menu_item_counter] : '';
                    $menu_item_icon = isset($_POST['menu_item_icon'][$menu_item_counter]) ? $_POST['menu_item_icon'][$menu_item_counter] : '';
                    $menu_item_nutri = isset($_POST['menu_item_nutri'][$menu_item_counter]) ? $_POST['menu_item_nutri'][$menu_item_counter] : '';
                    $menu_item_desc = isset($_POST['menu_item_desc'][$menu_item_counter]) ? $_POST['menu_item_desc'][$menu_item_counter] : '';
                }
                if ($menu_item_add_action == 'add') {
                    $menu_item_extra = isset($_POST['menu_item_extra'][$menu_item_counter]) ? $_POST['menu_item_extra'][$menu_item_counter] : '';
                } else {
                    $menu_item_extra = isset($_POST['menu_item_extra'][$menu_item_counter]) ? $_POST['menu_item_extra'][$menu_item_counter] : '';
                }
            }


            // $menu_item_post_status = isset($get_menu_items['menu_item_post_status']) ? $get_menu_items['menu_item_post_status'] : 'publish';


            if (isset($menu_item_icon) && is_array($menu_item_icon) && !empty($menu_item_icon)) {
                $menu_item_icon = $menu_item_icon[0];
            }
            $_icon_html = '';
            if ($menu_item_icon != '') {
                $menu_item_icon_img_arr = wp_get_attachment_image_src($menu_item_icon, 'thumbnail');
                $menu_item_icon_img_src = isset($menu_item_icon_img_arr[0]) ? $menu_item_icon_img_arr[0] : '';
                $_icon_html = '<div class="icon-holder"><img src="' . $menu_item_icon_img_src . '" alt="" /></div>';
            }

            $menu_item_fields = array(
                'restaurant_menu' => $restaurant_menu,
                'menu_item_title' => $menu_item_title,
                'menu_item_post_status' => $menu_item_post_status,
                'menu_item_comment' => $menu_item_comment,
                'menu_item_price' => $menu_item_price,
                'menu_item_icon' => $menu_item_icon,
                'menu_item_nutri' => $menu_item_nutri,
                'menu_item_desc' => $menu_item_desc,
                'menu_item_extra' => $menu_item_extra,
            );

            $menu_item_counter = rand(123456789, 987654321);
            $html = '
			<li class="menu-item-' . $menu_item_counter . '">
				<div class="drag-list">
					<span class="drag-option"><i class="icon-bars"></i></span>
					' . $_icon_html . '
					<div class="list-title">
						<h6>' . stripslashes($menu_item_title) . '</h6>
						' . ($menu_item_desc != '' ? '<p>' . esc_html($menu_item_desc) . '</p>' : '') . '
					</div>
					<div class="list-price">
						<span>' . currency_symbol_possitions_html("<b>" . $currency_sign . "</b>", "<b>" . $menu_item_price . "</b>") . '</span>
					</div>
					<div class="list-option" style="width:10%">
                    <a href="javascript:void(0);" class="edit-menu-item" onClick="foodbakery_copy_menu_item(\'' . $menu_item_counter . '\');"><i class="icon-copy"></i></a>
						<a href="javascript:void(0);" class="edit-menu-item" onClick="foodbakery_add_menu_item(\'' . $menu_item_counter . '\');"><i class="icon-mode_edit"></i></a>
						<a href="javascript:void(0);" class="remove-menu-item" onClick="foodbakery_remove_menu_item(\'' . $menu_item_counter . '\');"><i class="icon-close2"></i></a>
					</div>
				</div>';
            $html .= '<form  id="sagar_add_form_'. $restaurant_add_counter .'" method="post" enctype="multipart/form-data"><div class="sagar2" id="add-menu-item-from-' . $menu_item_counter . '" style="display:none;">';
            $html .= $this->foodbakery_restaurant_menu_items_form_ui($restaurant_ad_counter, $menu_item_counter, $menu_item_fields, $form_action);
            $html .= '</div> </form>';
            $html .= '</li>';







            if (is_array($get_menu_items) && sizeof($get_menu_items) > 0) {
                return apply_filters('foodbakery_front_restaurant_add_single_service', $html, $get_menu_items);
            } else {
                if ($form_action == 'edit') {
                    $message = esc_html__('Menu item updated successfully', 'foodbakery');
                } else {
                    $message = esc_html__('Menu item added successfully', 'foodbakery');
                }
                echo json_encode(array('html' => $html, 'type' => 'success', 'msg' => $message));
                die;
            }



        }


        public function sa_add_item($get_menu_items = '', $form_action = 'edit'){
            $current_user = wp_get_current_user();
            $publisher_id = foodbakery_company_id_form_user_id($current_user->ID);
        
            $args = array(
                'posts_per_page' => "1",
                'post_type' => 'restaurants',
                'post_status' => 'publish',
                'fields' => 'ids',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'foodbakery_restaurant_publisher',
                        'value' => $publisher_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'foodbakery_restaurant_username',
                        'value' => $current_user->ID,
                        'compare' => '=',
                    ),
                ),
            );
            $custom_query = new WP_Query($args);
            $pub_restaurant = $custom_query->posts;
            if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
               // print_r('_____pass 1____');
                $restaurant_id = $pub_restaurant[0];
        
                // saving restaurant services
                $foodbakery_restaurant_menu_item_title = foodbakery_get_input('menu_item_title', '', 'ARRAY');
                $foodbakery_restaurants_menu = foodbakery_get_input('restaurant_menu', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_price = foodbakery_get_input('menu_item_price', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_icon = foodbakery_get_input('menu_item_icon', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_nutri = foodbakery_get_input('menu_item_nutri', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_desc = foodbakery_get_input('menu_item_desc', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_extra = foodbakery_get_input('menu_item_extra', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_action = foodbakery_get_input('menu_item_action', '', 'ARRAY');
        
                $foodbakery_restaurant_menu_item_post_status = foodbakery_get_input('menu_item_post_status', '', 'ARRAY');
                $foodbakery_restaurant_menu_item_comment = foodbakery_get_input('menu_item_comment', '', 'ARRAY');
        
        
               
        
                $menu_items_array = array();
                if (isset($_POST['menu_item_title']) && is_array($foodbakery_restaurant_menu_item_title) && sizeof($foodbakery_restaurant_menu_item_title) > 0) {
                    $menu_items_array = array();
                    foreach ($foodbakery_restaurant_menu_item_title as $key => $menu_item) {
                        $menu_item_action = isset($foodbakery_restaurant_menu_item_action[$key]) ? $foodbakery_restaurant_menu_item_action[$key] : '';
                        $menu_count = 0;
                        if (isset($menu_item) && is_array($menu_item) && $menu_item != '') {
                            $menu_count = count($menu_item);
                        }
                        if ($menu_item != '' && $menu_item_action != 'add') {
                            $menu_items_array[] = array(
                                'menu_item_title' => $menu_item,
                                'menu_item_post_status' => isset($foodbakery_restaurant_menu_item_post_status[$key]) ? $foodbakery_restaurant_menu_item_post_status[$key] : '',
                                'menu_item_comment' => isset($foodbakery_restaurant_menu_item_comment[$key]) ? $foodbakery_restaurant_menu_item_comment[$key] : '',
                                'restaurant_menu' => isset($foodbakery_restaurants_menu[$key]) ? $foodbakery_restaurants_menu[$key] : '',
                                'menu_item_description' => isset($foodbakery_restaurant_menu_item_desc[$key]) ? $foodbakery_restaurant_menu_item_desc[$key] : '',
                                'menu_item_icon' => isset($foodbakery_restaurant_menu_item_icon[$key]) ? $foodbakery_restaurant_menu_item_icon[$key] : '',
                                'menu_item_nutri' => isset($foodbakery_restaurant_menu_item_nutri[$key]) ? $foodbakery_restaurant_menu_item_nutri[$key] : '',
                                'menu_item_price' => isset($foodbakery_restaurant_menu_item_price[$key]) ? $foodbakery_restaurant_menu_item_price[$key] : '',
                                'menu_item_extra' => isset($foodbakery_restaurant_menu_item_extra[$key]) ? $foodbakery_restaurant_menu_item_extra[$key] : '',
                            );
                        }
                    }
                }

               // print_r($foodbakery_restaurant_menu_item_extra);


               $posts = get_post_meta($restaurant_id, 'foodbakery_menu_items');

         

              

               $new_arr[] = $posts[0][0];
              // $new_arr[] = $posts[0][0];
               print_r($new_arr);
                // update_post_meta($restaurant_id, 'foodbakery_menu_items', $new_arr);
        
                // // saving restaurant menu categories
                // //if ( isset($_POST['menu_cat_title']) ) {
                // $restaurant_menu_cat_titles = isset($_POST['menu_cat_title']) ? $_POST['menu_cat_title'] : '';
                // update_post_meta($restaurant_id, 'menu_cat_titles', $restaurant_menu_cat_titles);
        
                // $restaurant_menu_cat_descs = isset($_POST['menu_cat_desc']) ? $_POST['menu_cat_desc'] : '';
                // update_post_meta($restaurant_id, 'menu_cat_descs', $restaurant_menu_cat_descs);
                // //}
            }
        }

        /**
         * Menu Items Grouping With Menus
         */
        public function group_restaurant_menu_items($restaurant_menu_list = '', $restaurant_add_counter = '') {
            global $restaurant_add_counter;
            if (is_array($restaurant_menu_list) && sizeof($restaurant_menu_list) > 0) {
                $total_items = count($restaurant_menu_list);
                $total_menu = array();
                $menu_items_list = '';
                for ($menu_count = 0; $menu_count < $total_items; $menu_count++) {
                    $menu_exists = in_array($restaurant_menu_list[$menu_count]['restaurant_menu'], $total_menu);
                    if (!$menu_exists) {
                        $total_menu[] = $restaurant_menu_list[$menu_count]['restaurant_menu'];
                    }
                }

                $total_menu_count = count($total_menu);
                $menu_items_list .= '';
                for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop++) {
                    $restaurant_menu = esc_html($total_menu[$menu_loop]);
                    $restaurant_menu_slug = str_replace(' ', '-', strtolower($restaurant_menu));

                    $colapse_class = 'panel-collapse collapse';
                    $colapse_head_class = ' class="collapsed"';
                    if ($menu_loop == 0) {
                        $colapse_class = 'panel-collapse collapse in';
                        $colapse_head_class = '';
                    }

                    $menu_items_list .= '<li id="menu-' . $restaurant_menu_slug . '" class="panel panel-default">';
                    $menu_items_list .= '<div class="element-title panel-heading">';
                    $menu_items_list .= '<span class="drag-option ui-sortable-handle"><i class="icon-bars"></i></span> <a data-toggle="collapse"' . $colapse_head_class . ' data-parent="#restaurant_menu_items-list-' . $restaurant_add_counter . '" href="#collapse-' . $menu_loop . '">' . $restaurant_menu . '</a>';
                    $menu_items_list .= '</div>';
                    $menu_items_list .= '
					<div id="collapse-' . $menu_loop . '" class="' . $colapse_class . '">';
                    $menu_items_list .= '<ul class="menu-items-list">';
                    for ($menu_items_loop = 0; $menu_items_loop < $total_items; $menu_items_loop++) {
                        $menu_item_counter = rand(123456789, 987654321);
                        if ($total_menu[$menu_loop] == $restaurant_menu_list[$menu_items_loop]['restaurant_menu']) {
                            $menu_item_title = isset($restaurant_menu_list[$menu_items_loop]['menu_item_title']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_title'] : '';
                            $menu_item_post_status = isset($restaurant_menu_list[$menu_items_loop]['menu_item_post_status']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_post_status'] : '';

                            $menu_item_comment = isset($restaurant_menu_list[$menu_items_loop]['menu_item_comment']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_comment'] : '';

                            $menu_item_description = isset($restaurant_menu_list[$menu_items_loop]['menu_item_description']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_description'] : '';
                            $menu_item_icon = isset($restaurant_menu_list[$menu_items_loop]['menu_item_icon']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_icon'] : '';
                            $menu_item_nutri = isset($restaurant_menu_list[$menu_items_loop]['menu_item_nutri']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_nutri'] : '';
                            $menu_item_price = isset($restaurant_menu_list[$menu_items_loop]['menu_item_price']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_price'] : '';
                            $menu_item_extra = isset($restaurant_menu_list[$menu_items_loop]['menu_item_extra']) ? $restaurant_menu_list[$menu_items_loop]['menu_item_extra'] : '';
                            $get_menu_items = array(
                                'restaurant_menu' => $restaurant_menu,
                                'menu_item_title' => $menu_item_title,
                                'menu_item_post_status' => $menu_item_post_status,
                                'menu_item_comment' => $menu_item_comment,
                                'menu_item_description' => $menu_item_description,
                                'menu_item_icon' => $menu_item_icon,
                                'menu_item_nutri' => $menu_item_nutri,
                                'menu_item_price' => $menu_item_price,
                                'menu_item_extra' => $menu_item_extra,
                                'menu_item_counter' => $menu_item_counter,
                                'restaurant_ad_counter' => $restaurant_add_counter,
                            );

                            $menu_items_list .= $this->foodbakery_add_menu_item_to_list_callback($get_menu_items, 'edit');
                        }
                    }
                    $menu_items_list .= '</ul>';
                    $menu_items_list .= '</div>';
                    $menu_items_list .= '</li>';
                }
                $menu_items_list .= '';

                return $menu_items_list;
            }
        }

        /**
         * checking publisher own post
         * @return boolean
         */
        public function is_publisher_restaurant($restaurant_id = '') {
            global $current_user;
            $company_id = foodbakery_company_id_form_user_id($current_user->ID);
            $foodbakery_publisher_id = get_post_meta($restaurant_id, 'foodbakery_restaurant_publisher', true);
            if (is_user_logged_in() && $company_id == $foodbakery_publisher_id) {
                return true;
            }
            return false;
        }

    }

    // end class
    // Initialize Object
    $foodbakery_restaurant_menus = new Foodbakery_Restaurant_Menus();
}