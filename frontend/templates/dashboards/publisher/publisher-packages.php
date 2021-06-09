<?php
/**
 * Publisher Restaurants
 *
 */
if (!class_exists('Foodbakery_Publisher_Memberships')) {

    class Foodbakery_Publisher_Memberships {

	/**
	 * Start construct Functions
	 */
	public function __construct() {
	    add_action('wp_ajax_foodbakery_publisher_packages', array($this, 'foodbakery_publisher_packages_callback'), 11, 1);
	}

	/**
	 * Publisher Restaurants
	 * @ filter the restaurants based on publisher id
	 */
	public function foodbakery_publisher_packages_callback($publisher_id = '') {
	    global $current_user, $foodbakery_plugin_options, $restaurant_add_counter;
	    $restaurant_add_counter = rand(100000, 999999);

	    $publisher_id = foodbakery_company_id_form_user_id($current_user->ID);

	    $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';
	    ?>
	    <form method="post" enctype="multipart/form-data">
	        <ul class="membership-info-main">
		    <?php do_action('foodbakery_restaurant_add_info', ''); ?>
		    <?php do_action('foodbakery_restaurant_add_packages'); ?>
	        </ul>
	        <div class="row">
	    	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	    	    <div class="field-holder">
	    		<div id="update-membership-holder" class="payment-holder input-button-loader" style="display:none;">
	    		    <input class="redirect-button-click update-membership" value="<?php esc_html_e('Update Membership', 'foodbakery') ?>" type="submit">
	    		    <input name="membership_updating" value="1" type="hidden">
	    		</div>
	    	    </div> 
	    	</div>
	        </div>
	    </form>
	    <?php
	    $foodbakery_current_date = strtotime(date('d-m-Y'));
	    $args = array(
		'posts_per_page' => "-1",
		'post_type' => 'package-orders',
		'post_status' => 'publish',
		'meta_query' => array(
		    'relation' => 'AND',
		    array(
			'key' => 'foodbakery_transaction_user',
			'value' => $publisher_id,
			'compare' => '=',
		    ),
		    array(
			'key' => 'foodbakery_transaction_expiry_date',
			'value' => $foodbakery_current_date,
			'compare' => '>',
		    ),
		    array(
			'key' => 'foodbakery_transaction_status',
			'value' => 'approved',
			'compare' => '=',
		    ),
		),
	    );

	    $pkg_query = new WP_Query($args);
	    $total_posts = $pkg_query->post_count;

	    $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
	    $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';

	    $args = array(
		'posts_per_page' => $posts_per_page,
		'paged' => $posts_paged,
		'post_type' => 'package-orders',
		'post_status' => 'publish',
		'meta_query' => array(
		    'relation' => 'AND',
		    array(
			'key' => 'foodbakery_transaction_user',
			'value' => $publisher_id,
			'compare' => '=',
		    ),
		    array(
			'key' => 'foodbakery_transaction_expiry_date',
			'value' => $foodbakery_current_date,
			'compare' => '>',
		    ),
		    array(
			'key' => 'foodbakery_transaction_status',
			'value' => 'approved',
			'compare' => '=',
		    ),
		),
	    );

	    $pkg_query = new WP_Query($args);

	    echo force_balance_tags($this->render_view($pkg_query));
	    wp_reset_postdata();

	    $total_pages = 1;
	    if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
		$total_pages = ceil($total_posts / $posts_per_page);

		$foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
		$foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
		$this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'packages'), $foodbakery_dashboard_link) : '';
		foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'packages');
	    }

	    wp_die();
	}

	public function purchase_package_info_field_show($value = '', $label = '', $value_plus = '') {
	    if ($value != '' && $value != 'on') {
		$html = '<li><label>' .  esc_html__($label, 'foodbakery') . '</label><span>' . esc_html__($value, 'foodbakery') . ' ' . $value_plus . '</span></li>';
	    } else if ($value != '' && $value == 'on') {
		$html = '<li><label>' . esc_html__($label, 'foodbakery') . '</label><span><i class="icon-check"></i></span></li>';
	    } else {
		$html = '<li><label>' . esc_html__($label, 'foodbakery') . '</label><span><i class="icon-minus"></i></span></li>';
	    }

	    return $html;
	}

	public function render_view($pkg_query) {
	    global $foodbakery_plugin_options;
	    $foodbakery_currency_sign = foodbakery_get_currency_sign();
	    ?>
	    <div class="user-packages">
	        <div class="element-title">
	    	<h5><?php echo esc_html_e('Subscribed Memberships', 'foodbakery'); ?></h5>
	        </div>
	    </div>
	    <div class="user-packages-list">
		<?php if (isset($pkg_query) && $pkg_query != '' && $pkg_query->have_posts()) : ?>
		    <div class="all-pckgs-sec">
			<?php
			while ($pkg_query->have_posts()) : $pkg_query->the_post();
			    $transaction_package = get_post_meta(get_the_ID(), 'foodbakery_transaction_package', true);
			    $transaction_expiry_date = get_post_meta(get_the_ID(), 'foodbakery_transaction_expiry_date', true);
			    $transaction_restaurants = get_post_meta(get_the_ID(), 'foodbakery_transaction_restaurants', true);
			    $transaction_feature_list = get_post_meta(get_the_ID(), 'foodbakery_transaction_restaurant_feature_list', true);
			    $transaction_top_cat_list = get_post_meta(get_the_ID(), 'foodbakery_transaction_restaurant_top_cat_list', true);
			    $package_id = get_the_ID();
			    $transaction_restaurants = isset($transaction_restaurants) ? $transaction_restaurants : 0;
			    $transaction_feature_list = isset($transaction_feature_list) ? $transaction_feature_list : 0;
			    $transaction_top_cat_list = isset($transaction_top_cat_list) ? $transaction_top_cat_list : 0;
			    $package_price = get_post_meta($package_id, 'foodbakery_transaction_amount', true);
			    $html = '';
			    ?>
		    	<div class="foodbakery-pkg-holder">
		    	    <div class="foodbakery-pkg-header">
		    		<div class="pkg-title-price pull-left">
		    		    <label class="pkg-title"><?php echo get_the_title($transaction_package); ?></label>
		    		    <span class="pkg-price"><?php printf(esc_html__('Price: %s', 'foodbakery'), $foodbakery_currency_sign . FOODBAKERY_FUNCTIONS()->num_format($package_price)) ?></span>
		    		</div>
		    		<div class="pkg-detail-btn pull-right">
		    		    <a data-id="<?php echo absint($package_id) ?>" class="foodbakery-dev-dash-detail-pkg" href="javascript:void(0);"><?php esc_html_e('Detail', 'foodbakery') ?></a>
		    		</div>
		    	    </div>
		    	    <div class="package-info-sec restaurant-info-sec" style="display:none;" id="package-detail-<?php echo absint($package_id) ?>">
		    		<div class="row">
		    		    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		    			<ul class="restaurant-pkg-points">
						<?php
						$trans_packg_expiry = get_post_meta($package_id, 'foodbakery_transaction_expiry_date', true);
						$trans_packg_list_num = get_post_meta($package_id, 'foodbakery_transaction_restaurants', true);
						$trans_packg_list_expire = get_post_meta($package_id, 'foodbakery_transaction_restaurant_expiry', true);
						$foodbakery_restaurant_ids = get_post_meta($package_id, 'foodbakery_restaurant_ids', true);

						if (empty($foodbakery_restaurant_ids)) {
						    $foodbakery_restaurant_used = 0;
						} else {
						    $foodbakery_restaurant_used = absint(sizeof($foodbakery_restaurant_ids));
						}

						$foodbakery_restaurant_remain = '0';
						if ((int) $trans_packg_list_num > (int) $foodbakery_restaurant_used) {
						    $foodbakery_restaurant_remain = (int) $trans_packg_list_num - (int) $foodbakery_restaurant_used;
						}

						$trans_featured_num = get_post_meta($package_id, 'foodbakery_transaction_restaurant_feature_list', true);
						$trans_top_cat_num = get_post_meta($package_id, 'foodbakery_transaction_restaurant_top_cat_list', true);

						$trans_pics_num = get_post_meta($package_id, 'foodbakery_transaction_restaurant_pic_num', true);

						$trans_tags_num = get_post_meta($package_id, 'foodbakery_transaction_restaurant_tags_num', true);
						$trans_reviews = get_post_meta($package_id, 'foodbakery_transaction_restaurant_reviews', true);
						$trans_phone = get_post_meta($package_id, 'foodbakery_transaction_restaurant_phone', true);
						$trans_website = get_post_meta($package_id, 'foodbakery_transaction_restaurant_website', true);
						$trans_social = get_post_meta($package_id, 'foodbakery_transaction_restaurant_social', true);
						$trans_ror = get_post_meta($package_id, 'foodbakery_transaction_restaurant_ror', true);
						$trans_dynamic_f = get_post_meta($package_id, 'foodbakery_transaction_dynamic', true);
						$pkg_expire_date = date_i18n(get_option('date_format'), $trans_packg_expiry);
						$html .= $this->purchase_package_info_field_show($pkg_expire_date, esc_html__('Expiry Date', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_packg_list_expire, esc_html__('Restaurants Duration', 'foodbakery'), esc_html__('Days', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_featured_num, esc_html__('Feature Restaurant', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_top_cat_num, esc_html__('Top Category Restaurant', 'foodbakery'));

						$html .= $this->purchase_package_info_field_show($trans_tags_num, esc_html__('Number of Tags', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_reviews, esc_html__('Reviews', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_phone, esc_html__('Phone Number', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_website, esc_html__('Website Link', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_social, esc_html__('Social Impressions Reach', 'foodbakery'));
						$html .= $this->purchase_package_info_field_show($trans_ror, esc_html__('Respond to Reviews', 'foodbakery'));
						$dyn_fields_html = '';
						if (is_array($trans_dynamic_f) && sizeof($trans_dynamic_f) > 0) {
						    foreach ($trans_dynamic_f as $trans_dynamic) {
							if (isset($trans_dynamic['field_type']) && isset($trans_dynamic['field_label']) && isset($trans_dynamic['field_value'])) {
							    $d_type = $trans_dynamic['field_type'];
							    $d_label = $trans_dynamic['field_label'];
							    $d_value = $trans_dynamic['field_value'];
							    if ($d_value == 'on' && $d_type == 'single-choice') {
								$html .= '<li><label>' . $d_label . '</label><span><i class="icon-check"></i></span></li>';
							    } else if ($d_value != '' && $d_type != 'single-choice') {
								$html .= '<li><label>' . $d_label . '</label><span>' . $d_value . '</span></li>';
							    } else {
								$html .= '<li><label>' . $d_label . '</label><span><i class="icon-minus"></i></span></li>';
							    }
							}
						    }
						    // end foreach
						}
						// emd of Dynamic fields
						// other Features
						echo force_balance_tags($html);
						?>
		    			</ul>
		    		    </div>
		    		</div>
		    	    </div>
		    	</div>
			    <?php
			endwhile;
			?>

		    </div>
		<?php else:
		    ?>
		    <div class="not-found">
			<i class="icon-error"></i>
			<p>
			    <?php
			    esc_html_e('Sorry! there is no package in your list.', 'foodbakery');
			    ?>
			</p>
		    </div>
		<?php endif;
		?>
	    </div>
	    <?php
	}

	public function render_list_item_view($pkg_query) {
	    
	}

    }

    global $foodbakery_publisher_packages;
    $foodbakery_publisher_packages = new Foodbakery_Publisher_Memberships();
}