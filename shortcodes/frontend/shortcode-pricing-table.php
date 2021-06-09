<?php
/**
 * File Type: Searchs Shortcode Frontend
 */
if (!class_exists('Foodbakery_Shortcode_Pricing_Table_front')) {

    class Foodbakery_Shortcode_Pricing_Table_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'pricing_table';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_pricing_table_shortcode_callback'));
        }

        /*
         * Shortcode View on Frontend
         */

        public function foodbakery_pkgs($value = '') {
            $pkgs_options = '';
            $args = array('posts_per_page' => '-1', 'post_type' => 'packages', 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC');
            $cust_query = get_posts($args);
            $pkgs_options .= '<option value="">' . esc_html__('Select Membership', 'foodbakery') . '</option>';
            if (is_array($cust_query) && sizeof($cust_query) > 0) {
                $pkg_counter = 1;
                foreach ($cust_query as $pkg_post) {
                    $option_selected = '';
                    if ($value != '' && $value == $pkg_post->ID) {
                        $option_selected = ' selected="selected"';
                    }
                    $pkgs_options .= '<option' . $option_selected . ' value="' . $pkg_post->ID . '">' . get_the_title($pkg_post->ID) . '</option>' . "\n";
                    $pkg_counter ++;
                }
            }

            $select_field = '<select name="pt_pkg_url[]">' . $pkgs_options . '</select>';

            return $select_field;
        }

        function combine_pt_section($keys, $values) {
            $result = array();
            foreach ($keys as $i => $k) {
                $result[$k][] = $values[$i];
            }
            array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
            return $result;
        }

        public function foodbakery_pricing_table_shortcode_callback($atts, $content = "") {
            global $current_user, $foodbakery_plugin_options;

            $publisher_profile_type = '';
            $user_publisher_id = '';
            if (is_user_logged_in() && current_user_can('foodbakery_publisher')) {
                $user_publisher_id = get_user_meta($current_user->ID, 'foodbakery_company', true);
                $publisher_profile_type = get_post_meta($user_publisher_id, 'foodbakery_publisher_profile_type', true);
            }

            $page_element_size = isset($atts['pricing_table_element_size']) ? $atts['pricing_table_element_size'] : 100;
            ob_start();
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '<div class="' . foodbakery_var_page_builder_element_sizes($page_element_size) . ' ">';
            }
            $pricing_table_id = isset($atts['foodbakery_pricing_tables']) ? $atts['foodbakery_pricing_tables'] : '';
            $pricing_table_title = isset($atts['pricing_table_title']) ? $atts['pricing_table_title'] : '';
            $foodbakery_var_pricing_table_align = isset($atts['foodbakery_var_pricing_table_align']) ? $atts['foodbakery_var_pricing_table_align'] : '';
            $pricing_tabl_subtitle = isset($atts['pricing_table_subtitle']) ? $atts['pricing_table_subtitle'] : '';
            $pricing_table_view = isset($atts['pricing_table_view']) ? $atts['pricing_table_view'] : 'simple';

            $rand_numb = rand(1000000, 99999999);
            if (isset($_POST['foodbakery_package_buy']) && $_POST['foodbakery_package_buy'] == '1') {
                $package_id = isset($_POST['package_id']) ? $_POST['package_id'] : 0;

                $foodbakery_price_table_restaurant_switch = get_post_meta($pricing_table_id, 'foodbakery_subscribe_action', true);
                $pkg_type = get_post_meta($package_id, 'foodbakery_package_type', true);
                $pkg_price = get_post_meta($package_id, 'foodbakery_package_price', true);

                if (is_user_logged_in() && current_user_can('foodbakery_publisher') && $publisher_profile_type == 'restaurant') {
                    $args = array(
                        'posts_per_page' => "1",
                        'post_type' => 'restaurants',
                        'post_status' => 'publish',
                        'fields' => 'ids',
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'foodbakery_restaurant_publisher',
                                'value' => $user_publisher_id,
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
                    wp_reset_postdata();
                    $pub_restaurant = $custom_query->posts;

                    $restaurant_id = isset($pub_restaurant[0]) ? $pub_restaurant[0] : '';

                    if (($pkg_type == 'free') || ($pkg_type == 'paid' && $pkg_price <= 0)) {
                        $foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
                        $foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
                        $redirect_form_id = rand(1000000, 9999999);
                        $redirect_html = '
						<form id="form-' . $redirect_form_id . '" method="get" action="' . $foodbakery_dashboard_link . '">
						<input type="hidden" name="tab" value="add-restaurant">
						<input type="hidden" name="restaurant_id" value="' . $restaurant_id . '">
						<input type="hidden" name="restaurant_tab" value="membership">
						<input type="hidden" name="package_id" value="' . $package_id . '">';
                        if (isset($_GET['lang'])) {
                            $redirect_html .= '<input type="hidden" name="lang" value="' . $_GET['lang'] . '">';
                        }
                        $redirect_html .= '
						</form>
						<script>document.getElementById("form-' . $redirect_form_id . '").submit();</script>';
                        echo force_balance_tags($redirect_html);
                    } else {

                        update_post_meta($restaurant_id, 'foodbakery_restaurant_package', $package_id);

                        $form_rand_numb = isset($_POST['foodbakery_package_random']) ? $_POST['foodbakery_package_random'] : '';
                        $form_rand_transient = get_transient('foodbakery_package_random');

                        if ($form_rand_transient != $form_rand_numb) {

                            $foodbakery_restaurant_obj = new foodbakery_publisher_restaurant_actions();
                            $company_id = foodbakery_company_id_form_user_id($current_user->ID);

                            set_transient('foodbakery_package_random', $form_rand_numb, 60 * 60 * 24 * 30);

                            $foodbakery_restaurant_obj->foodbakery_restaurant_add_transaction('buy_package', $restaurant_id, $package_id, $company_id);
                        }
                    }
                }
            }

            $no_publisher_msg = '';
            if (is_user_logged_in() && !current_user_can('foodbakery_publisher')) {
                $no_publisher_msg = '
				<div id="response-' . $rand_numb . '" class="response-holder" style="display: none;">
					<div class="alert alert-warning fade in">' . esc_html__('Only a restaurant can subscribe a Membership.', 'foodbakery') . '</div>
				</div>';
            } else if (is_user_logged_in() && current_user_can('foodbakery_publisher') && $publisher_profile_type != 'restaurant') {
                $no_publisher_msg = '
				<div id="response-' . $rand_numb . '" class="response-holder" style="display: none;">
					<div class="alert alert-warning fade in">' . esc_html__('Only a restaurant can subscribe a Membership.', 'foodbakery') . '</div>
				</div>';
            }

            if ($pricing_table_title != '' || $pricing_tabl_subtitle != '') {
                ?>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="section-title <?php echo esc_html($foodbakery_var_pricing_table_align); ?>">
                        <?php
                        if ($pricing_table_title != '') {
                            ?>
                            <h2><?php echo esc_html($pricing_table_title); ?></h2>
                            <?php
                        }
                        if ($pricing_tabl_subtitle != '') {
                            ?>
                            <p><?php echo esc_html($pricing_tabl_subtitle); ?></p>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
            if ($pricing_table_id != '') {

                $pt_pkg_name = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_names', true);
                $pt_pkg_price = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_prices', true);
                $pt_pkg_desc = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_descs', true);
                $pt_pkg_btn_txt = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_btn_txts', true);
                $pt_pkg_feat = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_feats', true);
                $pt_pkg_url = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_urls', true);
                $row_num_input = get_post_meta($pricing_table_id, 'foodbakery_pt_row_num', true);
                $pt_col_input = get_post_meta($pricing_table_id, 'foodbakery_pt_col_vals', true);
                $pt_col_sub_input = get_post_meta($pricing_table_id, 'foodbakery_pt_col_subs', true);
                $pt_row_title = get_post_meta($pricing_table_id, 'foodbakery_pt_row_title', true);
                $pt_pkg_dur = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_durs', true);
                $pt_pkg_color = get_post_meta($pricing_table_id, 'foodbakery_pt_pkg_color', true);
                $pt_sec_val = get_post_meta($pricing_table_id, 'foodbakery_pt_sec_vals', true);
                $pt_sec_pos = get_post_meta($pricing_table_id, 'foodbakery_pt_sec_pos', true);
                $counter = 0;
                $internal_counter = 1;
                $num_pack_count = 0;
                $pt_row_title_count = 0;
                $pt_col_input_count = 0;
                if (!empty($pt_pkg_name)) {
                    $num_pack_count = count($pt_pkg_name);
                }
                if (!empty($pt_pkg_name)) {
                    $pt_row_title_count = count($pt_row_title);
                }
                if (!empty($pt_pkg_name)) {
                    $pt_col_input_count = count($pt_col_input);
                }
                $number_of_pack = $num_pack_count;
                $number_of_rows = $pt_row_title_count;
                $totla_features = $pt_col_input_count;
                if ($totla_features && $number_of_pack > 0) {
                    $number_of_cols = $totla_features / $number_of_pack;
                }
                if ($totla_features && $number_of_rows > 0) {
                    $row_numbers = $totla_features / $number_of_rows;
                }
                $new_col = 0;
                if (is_array($pt_col_input)) {
                    $chunked_array = (array_chunk($pt_col_input, $row_numbers));
                }
                if (is_array($pt_pkg_name) && sizeof($pt_pkg_name) > 0) {
                    if ($pricing_table_view == 'simple') {
                        foreach ($pt_pkg_name as $single_package) {
                            $pkg_id = isset($pt_pkg_url[$counter]) ? $pt_pkg_url[$counter] : '';
                            $pkg_btn_txt = isset($pt_pkg_btn_txt[$counter]) && $pt_pkg_btn_txt[$counter] != '' ? $pt_pkg_btn_txt[$counter] : esc_html__('Buy Now', 'foodbakery');
                            $pt_pkg_prices = isset($pt_pkg_price[$counter]) && $pt_pkg_price[$counter] != '' ? $pt_pkg_price[$counter] : '';
                            $row_count = 0;
                            $pt_pkg_feat[$counter];
                            ?>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="pricetable-holder center <?php
                                if ($pt_pkg_feat[$counter] == 'yes') {
                                    echo "active";
                                }
                                ?>">
                                    <div class="price-holder">
                                        <?php
                                        if ($pt_pkg_feat[$counter] == 'yes') {
                                            ?>

                                            <?php
                                        }
                                        ?>
                                        <div class="cs-price">
                                            <h2 class="text-color"><?php echo esc_html($single_package); ?></h2>
                                            <span>
                                                <?php echo foodbakery_get_currency($pt_pkg_prices, true, '<small>', '</small>'); ?>
                                                <em><?php echo esc_html($pt_pkg_dur[$counter]); ?></em>
                                            </span>
                                        </div>
                                        <?php
                                        if ($pt_pkg_desc[$counter] != '') {
                                            ?>
                                            <p> <?php echo esc_html($pt_pkg_desc[$counter]); ?></p>
                                            <?php
                                        }
                                        ?>
                                        <ul>
                                            <?php
                                            if (is_array($chunked_array) || is_object($chunked_array)) {
                                                if(is_array($chunked_array) && count($chunked_array) > 0 ){
                                                    foreach ($chunked_array as $chunk) {
                                                        ?>
                                                        <li>
                                                            <?php
                                                            if (isset($chunk[$counter])) {
                                                                ?>
                                                                <strong> <?php echo esc_html($chunk[$counter]); ?> </strong> 
                                                                <?php
                                                            }
                                                            echo esc_html($pt_row_title[$row_count]);
                                                            ?>
                                                        </li>
                                                        <?php
                                                        $row_count ++;
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if (is_user_logged_in() && current_user_can('foodbakery_publisher') && $publisher_profile_type == 'restaurant') {
                                            if (true === Foodbakery_Member_Permissions::check_permissions('packages')) {
                                                ?>
                                                <form method="post">
                                                    <input type="hidden" name="foodbakery_package_buy" value="1" />
                                                    <input type="hidden" name="foodbakery_package_random" value="<?php echo absint($rand_numb) ?>" />
                                                    <input type="hidden" name="package_id" value="<?php echo absint($pkg_id) ?>" />
                                                    <div class="foodbakery-subscribe-pkg-btn">
                                                        <input type="submit"  value="<?php echo esc_html($pkg_btn_txt) ?>">
                                                        <i class="icon-controller-play"></i>
                                                    </div>
                                                </form>
                                                <?php
                                            }
                                        } else if (is_user_logged_in() && current_user_can('foodbakery_publisher') && $publisher_profile_type != 'restaurant') {
                                            ?>
                                            <a data-id="<?php echo absint($rand_numb) ?>" href="javascript:void(0);" class="foodbakery-subscribe-pkg"><?php echo esc_html($pkg_btn_txt) ?><i class="icon-controller-play"></i></a>
                                            <?php
                                        } else if (is_user_logged_in() && !current_user_can('foodbakery_publisher')) {
                                            ?>
                                            <a data-id="<?php echo absint($rand_numb) ?>" href="javascript:void(0);" class="foodbakery-subscribe-pkg"><?php echo esc_html($pkg_btn_txt) ?><i class="icon-controller-play"></i></a>
                                            <?php
                                        } else if (!is_user_logged_in()) {
                                            ?>
                                            <a href="#" data-target="#sign-in" data-msg="<?php esc_html_e('You have to login for purchase restaurant.', 'foodbakery') ?>" data-toggle="modal" class="foodbakery-subscribe-pkg"><?php echo esc_html($pkg_btn_txt) ?><i class="icon-controller-play"></i></a>
                                            <?php
                                        }
                                        ?>

                                    </div>
                                </div>
                            </div>

                            <?php
                            $internal_counter ++;

                            $counter ++;
                        }
                        echo force_balance_tags($no_publisher_msg);
                    } else if ($pricing_table_view == 'advance') {
                        foreach ($pt_pkg_name as $single_package) {

                            $pkg_id = isset($pt_pkg_url[$counter]) ? $pt_pkg_url[$counter] : '';
                            $pkg_btn_txt = isset($pt_pkg_btn_txt[$counter]) && $pt_pkg_btn_txt[$counter] != '' ? $pt_pkg_btn_txt[$counter] : esc_html__('Buy Now', 'foodbakery');
                            $pt_pkg_prices = isset($pt_pkg_price[$counter]) && $pt_pkg_price[$counter] != '' ? $pt_pkg_price[$counter] : '';
                            $row_count = 0;
                            $pt_pkg_feat[$counter];
                            ?>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 advance ">
                                <div class="pricetable-holder center <?php
                                if ($pt_pkg_feat[$counter] == 'yes') {
                                    echo "active";
                                }
                                ?> ">
                                    <div class="price-holder">
                                        <?php
                                        if ($pt_pkg_feat[$counter] == 'yes') {
                                            ?>

                                            <?php
                                        }
                                        ?>
                                        <div class="cs-price">
                                            <h2 class="text-color"><?php echo esc_html($single_package); ?></h2>
                                            <span>
                                                <?php echo foodbakery_get_currency($pt_pkg_prices, true, '<small>', '</small>'); ?>
                                                <em><?php echo esc_html($pt_pkg_dur[$counter]); ?></em>
                                            </span>
                                        </div>

                                        <ul>
                                            <?php if (is_array($chunked_array) && count($chunked_array > 0)) { ?>
                                                <?php foreach ($chunked_array as $chunk) {
                                                    ?>

                                                    <li><?php if (isset($chunk[$counter])) {
                                                        ?>
                                                            <strong> <?php echo esc_html($chunk[$counter]); ?> </strong> 
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php echo esc_html($pt_row_title[$row_count]); ?></li>
                                                    <?php
                                                    $row_count ++;
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if (is_user_logged_in() && current_user_can('foodbakery_publisher') && $publisher_profile_type == 'restaurant') {
                                            if (true === Foodbakery_Member_Permissions::check_permissions('packages')) {
                                                ?>
                                                <form method="post">
                                                    <input type="hidden" name="foodbakery_package_buy" value="1" />
                                                    <input type="hidden" name="foodbakery_package_random" value="<?php echo absint($rand_numb) ?>" />
                                                    <input type="hidden" name="package_id" value="<?php echo absint($pkg_id) ?>" />
                                                    <div class="foodbakery-subscribe-pkg-btn">

                                                        <input type="submit"  value="<?php echo esc_html($pkg_btn_txt) ?>">
                                                        <i class="icon-controller-play"></i>
                                                    </div>
                                                </form>
                                                <?php
                                            }
                                        } else if (is_user_logged_in() && current_user_can('foodbakery_publisher') && $publisher_profile_type != 'restaurant') {
                                            ?>
                                            <a data-id="<?php echo absint($rand_numb) ?>" href="javascript:void(0);" class="foodbakery-subscribe-pkg"><?php echo esc_html($pkg_btn_txt) ?><i class="icon-controller-play"></i></a>
                                            <?php
                                        } else if (is_user_logged_in() && !current_user_can('foodbakery_publisher')) {
                                            ?>
                                            <a data-id="<?php echo absint($rand_numb) ?>" href="javascript:void(0);" class="foodbakery-subscribe-pkg"><?php echo esc_html($pkg_btn_txt) ?><i class="icon-controller-play"></i></a>
                                            <?php
                                        } else if (!is_user_logged_in()) {
                                            ?>
                                            <a href="#" data-target="#sign-in" data-msg="<?php esc_html_e('You have to login for purchase restaurant.', 'foodbakery') ?>" data-toggle="modal" class="foodbakery-subscribe-pkg"><?php echo esc_html($pkg_btn_txt) ?><i class="icon-controller-play"></i></a>
                                            <?php
                                        }
                                        if ($pt_pkg_desc[$counter] != '') {
                                            
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $internal_counter ++;
                            $counter ++;
                        }
                        echo force_balance_tags($no_publisher_msg);
                    } else {
                        ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-fancy table-responsive">
                                <div class="table-fancy table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <?php
                                                if ($pt_pkg_name != '') {
                                                    echo ' <th></th>';
                                                    foreach ($pt_pkg_name as $key => $val) {
                                                        $price = isset($pt_pkg_price[$key]) ? $pt_pkg_price[$key] : '';
                                                        ?>
                                                        <th><?php echo esc_html($val); ?> <span class="text-color"> <?php echo foodbakery_get_currency($price, true); ?></span></th>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $table_html = '';
                                            $row_num_input = get_post_meta($pricing_table_id, 'foodbakery_pt_row_num', true);
                                            if (
                                                    $row_num_input &&
                                                    is_array($pt_col_input) &&
                                                    (int) $row_num_input > 0 &&
                                                    sizeof($pt_col_input) > 0
                                            ) {
                                                $row_nums = (int) $row_num_input;
                                                $col_nums = sizeof($pt_col_input);
                                                $row_break = 0;
                                                if ($col_nums >= $row_nums) {
                                                    $row_break = $col_nums / $row_nums;
                                                }
                                                $rows_array = array();
                                                if ($row_break > 0) {
                                                    $row_num_field = '<input type="hidden" class="row_num_input" name="dir_pt_row_num[]">';
                                                    $row_markup = '';
                                                    $col_markup = '';
                                                    $pt_counter = 1;
                                                    $pt_index_counter = 0;
                                                    $pt_row_counter = 0;
                                                    foreach ($pt_col_input as $col_val) {

                                                        $rand_id = rand(10000000, 99999999);
                                                        $pt_sub_input_val = isset($pt_col_sub_input[$pt_index_counter]) ? $pt_col_sub_input[$pt_index_counter] : '';
                                                        $item_icon = '';
                                                        if ($pt_sub_input_val != '') {
                                                            $item_icon = '<i class=" ' . $pt_sub_input_val . '"></i>  ';
                                                        }
                                                        $col_markup .= '<td>' . $item_icon . $col_val . ' <br></td>' . "\n";
                                                        if ($row_break == $pt_counter) {
                                                            $pt_row_title_txt = isset($pt_row_title[$pt_row_counter]) ? $pt_row_title[$pt_row_counter] : '';
                                                            $pt_row_counter ++;

                                                            $pt_row_del = '<td class="pt_row_actions">' . $pt_row_title_txt . '</td>';

                                                            $row_markup .= '<tr class="pt_row">' . $pt_row_del . $col_markup . '</tr>' . "\n";
                                                            $rows_array[] = $row_markup;
                                                            $col_markup = '';
                                                            $row_markup = '';
                                                            $pt_counter = 0;
                                                        }
                                                        $pt_counter ++;
                                                        $pt_index_counter ++;
                                                    }
                                                }
                                                $sections_array = array();
                                                if (
                                                        is_array($pt_sec_val) &&
                                                        is_array($pt_sec_pos) &&
                                                        sizeof($pt_sec_val) > 0 &&
                                                        sizeof($pt_sec_pos) > 0
                                                ) {
                                                    $sections_array = $this->combine_pt_section($pt_sec_pos, $pt_sec_val);
                                                }
                                                if (sizeof($sections_array) > 0 && isset($sections_array[0])) {
                                                    $row_break_new = $row_break + 1;
                                                    $table_html .= '
												<tr class="pt_section has-bgcolor">

													<td colspan="' . $row_break_new . '">
														' . $sections_array[0] . '
													</td>
												</tr>';
                                                }
                                                if (sizeof($rows_array) > 0) {
                                                    $row_counter = 1;
                                                    foreach ($rows_array as $row_arr) {
                                                        $table_html .= $row_arr;
                                                        if (sizeof($sections_array) > 0 && array_key_exists($row_counter, $sections_array)) {
                                                            if (is_array($sections_array[$row_counter])) {
                                                                foreach ($sections_array[$row_counter] as $sec_0) {
                                                                    $table_html .= '
										<tr class="pt_section">
											<td colspan="' . $row_break . '">
												<input type="text" name="dir_pt_sec_val[]" value="' . $sec_0 . '">
												<input type="hidden" name="dir_pt_sec_pos[]" value="' . $row_counter . '">
											</td>
										</tr>';
                                                                }
                                                            } else {
                                                                if (sizeof($sections_array) > 0 && isset($sections_array[$row_counter])) {
                                                                    $row_break_new = $row_break + 1;
                                                                    $table_html .= '
										    <tr class="pt_section has-bgcolor">

											    <td colspan="' . $row_break_new . '">
												    ' . $sections_array[$row_counter] . '
											    </td>
										    </tr>';
                                                                }
                                                            }
                                                        }
                                                        $row_counter ++;
                                                    }
                                                }
                                                echo force_balance_tags($table_html);
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <?php
                                                if ($pt_pkg_name != '') {
                                                    echo '<td></td>';
                                                    foreach ($pt_pkg_name as $key => $val) {
                                                        $pkg_id = isset($pt_pkg_url[$key]) ? $pt_pkg_url[$key] : '';
                                                        $pkg_btn_txt = isset($pt_pkg_btn_txt[$key]) && $pt_pkg_btn_txt[$key] != '' ? $pt_pkg_btn_txt[$key] : esc_html__('Buy Now', 'foodbakery');
                                                        ?>
                                                        <td>
                                                            <?php
                                                            if (is_user_logged_in() && current_user_can('foodbakery_publisher')) {
                                                                if (true === Foodbakery_Member_Permissions::check_permissions('packages')) {
                                                                    ?>
                                                                    <form method="post">
                                                                        <input type="hidden" name="foodbakery_package_buy" value="1" />
                                                                        <input type="hidden" name="foodbakery_package_random" value="<?php echo absint($rand_numb) ?>" />
                                                                        <input type="hidden" name="package_id" value="<?php echo absint($pkg_id) ?>" />
                                                                        <input type="submit" class="pkg-buy-btn" value="<?php echo esc_html($pkg_btn_txt) ?>">
                                                                    </form>
                                                                    <?php
                                                                }
                                                            } else if (is_user_logged_in() && !current_user_can('foodbakery_publisher')) {
                                                                ?>
                                                                <a data-id="<?php echo absint($rand_numb) ?>" href="javascript:void(0);" class="foodbakery-subscribe-pkg pkg-buy-btn"><?php echo esc_html($pkg_btn_txt) ?></a>
                                                                <?php
                                                            } else if (!is_user_logged_in()) {
                                                                ?>
                                                                <a href="#" data-target="#sign-in" data-msg="<?php esc_html_e('You have to login for purchase restaurant.', 'foodbakery') ?>" data-toggle="modal" class="foodbakery-subscribe-pkg pkg-buy-btn"><?php echo esc_html($pkg_btn_txt) ?></a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <?php echo force_balance_tags($no_publisher_msg) ?>
                        </div>
                        <?php
                    }
                }
            }
            if (function_exists('foodbakery_var_page_builder_element_sizes')) {
                echo '</div>';
            }
            $post_data = ob_get_clean();
            return $post_data;
        }

    }

    global $foodbakery_shortcode_pricing_table_front;
    $foodbakery_shortcode_pricing_table_front = new Foodbakery_Shortcode_Pricing_Table_front();
}
