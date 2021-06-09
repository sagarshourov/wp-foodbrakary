<?php
/**
 * Publisher Statements
 *
 */
if (!class_exists('Foodbakery_Publisher_Statements')) {

    class Foodbakery_Publisher_Statements {

	/**
	 * Start construct Functions
	 */
	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'foodbakery_orders_element_scripts'), 11);
	    add_action('wp_ajax_foodbakery_publisher_statements', array($this, 'foodbakery_publisher_statements_callback'));
	}
	
	public function foodbakery_orders_element_scripts(){
		wp_enqueue_style('daterangepicker');
		wp_enqueue_script('daterangepicker-moment');
		wp_enqueue_script('daterangepicker');
	}

	public function foodbakery_publisher_statements_callback() {
	    global $current_user, $foodbakery_plugin_options;

	    $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

	    if (true === Foodbakery_Member_Permissions::check_permissions('statements')) {

		wp_enqueue_script('foodbakery-restaurant-add');

		$user_id = $current_user->ID;
		$publisher_id = foodbakery_company_id_form_user_id($user_id);

		$publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
		$date_range = isset($_REQUEST['date_range']) ? $_REQUEST['date_range'] : '';
		
		$posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
		$posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
		$date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';

		$args = array(
		    'post_type' => 'foodbakery-trans',
		    'post_status' => 'publish',
		    'posts_per_page' => $posts_per_page,
		    'paged' => $posts_paged,
		    'meta_query' => array(
			'relation' => 'AND',
			array(
			    'relation' => 'OR',
			    array(
				'key' => 'foodbakery_transaction_user',
				'value' => $publisher_id,
				'compare' => '=',
			    ),
			    array(
				'key' => 'restaurant_publisher_id',
				'value' => $publisher_id,
				'compare' => '=',
			    ),
			),
			array(
			    'key' => 'foodbakery_transaction_status',
			    'value' => 'approved',
			    'compare' => '=',
			),
		    ),
		);
		// statements date range filter query
		if( $date_range != '' && $date_range != 'undefined' ){
			$new_date_range = explode(',', $date_range);
			$start_date = isset($new_date_range[0]) ? str_replace('/', '-', $new_date_range[0]) : '';
			$end_date = isset($new_date_range[1]) ? str_replace('/', '-', $new_date_range[1]) : '';
			$args['date_query'] = array(
				'after'     => date( 'Y-m-d', strtotime($start_date)),
				'before'    => date( 'Y-m-d', strtotime($end_date)),
				'inclusive' => true,
			);
		}
		$withdraw_query = new WP_Query($args);
		$total_posts = $withdraw_query->found_posts;
		?>
		<div class="element-title has-border right-filters-row">
		    <h5><?php esc_html_e('Statements', 'foodbakery') ?></h5>
			<?php if ( (!$withdraw_query->have_posts() && $date_range != '') || $withdraw_query->have_posts() ){ ?>
			<div class="right-filters row">
				<div class="col-lg-6 col-md-6 col-xs-6 pull-right">
					<div class="input-field">
					<i class="icon-angle-down"></i>
					<input type="text" id="daterange" value="<?php echo ($date_range != 'undefined' ) ? str_replace(',',' - ', $date_range) : ''; ?>" placeholder="<?php echo esc_html__('Select Date Range', 'foodbakery' ); ?>"/>
					<input type="hidden" name="date_range" id="date_range" value="<?php echo ($date_range != 'undefined' ) ? $date_range : ''; ?>" />
					<script type="text/javascript">
						jQuery('#daterange').daterangepicker({
							autoUpdateInput: false,
							opens: 'left',
							locale: {
								format: 'DD/MM/YYYY'
							}
						},
						function(start, end) {
							var date_range = start.format('DD/MM/YYYY')+ ',' +end.format('DD/MM/YYYY');
							jQuery('#date_range').val(date_range);
							var actionString = "foodbakery_publisher_statements";
							var pageNum = 1;
							foodbakery_show_loader('.loader-holder');
							var filter_parameters = get_filter_parameters();
							if (typeof (ajaxRequest) != 'undefined') {
								ajaxRequest.abort();
							}
							ajaxRequest = jQuery.ajax({
								type: "POST",
								url: foodbakery_globals.ajax_url,
								data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters,
								success: function (response) {
									foodbakery_hide_loader();
									jQuery('.user-holder').html(response);

								}
							});
						});
						jQuery('#daterange').on('cancel.daterangepicker', function(ev, picker) {
							jQuery('#daterange').val('');
							jQuery('#date_range').val('');
							var actionString = "foodbakery_publisher_statements";
							var pageNum = 1;
							foodbakery_show_loader('.loader-holder');
							var filter_parameters = get_filter_parameters();
							if (typeof (ajaxRequest) != 'undefined') {
								ajaxRequest.abort();
							}
							ajaxRequest = jQuery.ajax({
								type: "POST",
								url: foodbakery_globals.ajax_url,
								data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters,
								success: function (response) {
									foodbakery_hide_loader();
									jQuery('.user-holder').html(response);

								}
							});
						});
					</script>
				</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php
		if ($withdraw_query->have_posts()) {
		    ?>
		    <div class="responsive-table">
		        <ul class="table-generic">
		    	<li class="order-heading-titles">
		    	    <div><?php esc_html_e('Transaction ID', 'foodbakery') ?></div>	
		    	    <div><?php esc_html_e('Order ID', 'foodbakery') ?></div>	
		    	    <div><?php esc_html_e('Date', 'foodbakery') ?></div>
		    	    <div><?php esc_html_e('Detail', 'foodbakery') ?></div>
		    	    <div><?php esc_html_e('Amount', 'foodbakery') ?></div>
		    	</li>
			    <?php
			    while ($withdraw_query->have_posts()) : $withdraw_query->the_post();
				$trans_id = get_post_meta(get_the_ID(), 'foodbakery_transaction_id', true);
				$trans_order_id = get_post_meta(get_the_ID(), 'foodbakery_transaction_order_id', true);
				$trans_type = get_post_meta(get_the_ID(), 'foodbakery_transaction_order_type', true);
				$currency_sign = get_post_meta($trans_order_id, 'foodbakery_currency', true);
				$trans_long_id = get_the_title(get_the_ID());
				if ($trans_type == 'package-order') {
				    $trans_order_id = get_post_meta(get_the_ID(), 'foodbakery_transaction_order_id', true);
				    $order_pkg = get_post_meta($trans_order_id, 'foodbakery_transaction_package', true);
				    $trans_detail = sprintf(esc_html__('Package - %s', 'foodbakery'), get_the_title($order_pkg));
				    $trans_amount = get_post_meta(get_the_ID(), 'foodbakery_transaction_amount', true);

				    $trans_amount = get_post_meta(get_the_ID(), 'foodbakery_transaction_amount', true);
				} else {

				    $trans_order_id = get_post_meta(get_the_ID(), 'foodbakery_transaction_order_id', true);

				    $restaurant_id = get_post_meta($trans_order_id, 'foodbakery_restaurant_id', true);

				    if ($publisher_type != 'restaurant' && $publisher_type != '') {
					$trans_detail = sprintf(esc_html__('Order - %s', 'foodbakery'), get_the_title($restaurant_id));
				    } else {
					$trans_detail = sprintf(esc_html__('Sale - %s', 'foodbakery'), get_the_title($restaurant_id));
				    }

				    $trans_amount = get_post_meta($trans_order_id, 'order_amount_credited', true);
				    if ($trans_amount == '') {
					$trans_amount = get_post_meta($trans_order_id, 'services_total_price', true);
				    }

				    if ($publisher_type != 'restaurant' && $publisher_type != '') {
					$trans_amount = get_post_meta($trans_order_id, 'services_total_price', true);
				    }

				    $transac_type = get_post_meta(get_the_ID(), 'foodbakery_transaction_order_charge_type', true);
				    if ($publisher_type == 'restaurant' && $transac_type == 'order-charges') {
					$trans_parent_id = get_post_meta(get_the_ID(), 'foodbakery_transaction_parent_id', true);
					$trans_long_id = get_the_title($trans_parent_id);
					$trans_detail = esc_html__('Commission - for sale', 'foodbakery');
					$trans_amount = get_post_meta(get_the_ID(), 'foodbakery_order_amount_charged', true);
				    }
				}
				?>
				<li class="order-heading-titles">
				    <div><?php echo esc_html($trans_long_id); ?></div>
				    <div><a href="javascript:void(0);" class="orders-tab-link"><?php echo esc_html($trans_order_id); ?></a></div>
				    <div><?php echo get_the_date(get_option('date_format')); ?></div>
				    <div><?php echo esc_html($trans_detail) ?></div>
				    <div><?php echo foodbakery_get_currency($trans_amount, true, '', '', false); ?></div>
				</li>
				<?php
			    endwhile;
			    wp_reset_postdata();
			    ?>
		        </ul>
		    </div>
			<script type="text/javascript">
				jQuery( function() {
					jQuery("a.orders-tab-link").click( function() {
						if ( $("#foodbakery_publisher_received_orders").length > 0 ) {
							$("#foodbakery_publisher_received_orders").trigger('click');
						} else if ( $("#foodbakery_publisher_orders").length > 0 ) {
							$("#foodbakery_publisher_orders").trigger('click');
						}
						// var query_var = jQuery("#foodbakery_publisher_received_orders").data('queryvar');
						// if (query_var != undefined && query_var != '') {
								// var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + query_var;
						// } else {
							// var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
						// }
						// window.location = newurl;
					} );					
				} );
			</script>
		    <?php
		    $total_pages = 1;
		    if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
			$total_pages = ceil($total_posts / $posts_per_page);

			$foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
			$foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
			$this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'statements'), $foodbakery_dashboard_link) : '';
			foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'statements');
		    }
		} else {
		    ?>
		    <div class="not-found">
		        <i class="icon-error"></i>
		        <p>
			    <?php
                            $type = '';
			    if ($type == 'received') {
				esc_html_e('No statement found.', 'foodbakery');
			    } else {
				esc_html_e('No statement found.', 'foodbakery');
			    }
			    ?>
		        </p>
		    </div>
		    <?php
		}
	    }
	    wp_die();
	}

    }

    global $publisher_statements;
    $publisher_statements = new Foodbakery_Publisher_Statements();
}
