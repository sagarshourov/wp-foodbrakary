<?php
/**
 * Publisher Bookings
 *
 */
if (!class_exists('Foodbakery_Publisher_Bookings')) {

    class Foodbakery_Publisher_Bookings {

	/**
	 * Start construct Functions
	 */
	public function __construct() {
	    add_action('wp_enqueue_scripts', array($this, 'foodbakery_booking_element_scripts'), 5);
	    add_action('wp_ajax_foodbakery_publisher_bookings', array($this, 'foodbakery_publisher_bookings_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_publisher_received_bookings', array($this, 'foodbakery_publisher_received_bookings_callback'), 11, 1);
	    add_action('wp_ajax_foodbakery_update_booking_status', array($this, 'foodbakery_update_booking_status_callback'), 10);
	}

	public function foodbakery_booking_element_scripts() {
	    wp_enqueue_script('foodbakery-booking-functions');
	}

	public function foodbakery_publisher_bookings_callback($publisher_id = '') {
	    global $foodbakery_plugin_options;

	    $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

	    // Publisher ID.
	    if (!isset($publisher_id) || $publisher_id == '') {
		$publisher_id = get_current_user_id();
	    }

	    $publisher_company_id = foodbakery_company_id_form_user_id($publisher_id);

	    $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
	    $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
	    $date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';
	    $status = isset($_POST['status']) ? $_POST['status'] : '';

	    $args = array(
		'post_type' => 'orders_inquiries',
		'post_status' => 'publish',
		'posts_per_page' => $posts_per_page,
		'paged' => $posts_paged,
		'meta_query' => array(
		    'relation' => 'AND',
		    array(
			'key' => 'foodbakery_booking_publisher',
			'value' => $publisher_company_id,
			'compare' => '=',
		    ),
		    array(
			'key' => 'foodbakery_order_type',
			'value' => 'inquiry',
			'compare' => '=',
		    )
		),
	    );
	    // booking date range filter query
	    if ($date_range != '' && $date_range != 'undefined') {
		$new_date_range = explode(',', $date_range);
		$start_date = isset($new_date_range[0]) ? str_replace('/', '-', $new_date_range[0]) : '';
		$end_date = isset($new_date_range[1]) ? str_replace('/', '-', $new_date_range[1]) : '';
		$args['date_query'] = array(
		    'after' => date('Y-m-d', strtotime($start_date)),
		    'before' => date('Y-m-d', strtotime($end_date)),
		    'inclusive' => true,
		);
	    }
	    // booking status filter meta query
	    if ($status != '' && $status != 'undefined') {
		$args['meta_query'][] = array(
		    'key' => 'foodbakery_order_status',
		    'value' => $status,
		    'compare' => '=',
		);
	    }

	    $booking_query = new WP_Query($args);
	    $total_posts = $booking_query->found_posts;

	    echo force_balance_tags($this->render_view_bookings($booking_query, 'my'));

	    wp_reset_postdata();

	    $total_pages = 1;
	    if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
		$total_pages = ceil($total_posts / $posts_per_page);

		$foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
		$foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
		$this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'bookings'), $foodbakery_dashboard_link) : '';
		foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'bookings');
	    }

	    wp_die();
	}

	public function foodbakery_publisher_received_bookings_callback($publisher_id = '') {
	    global $foodbakery_plugin_options;

	    $pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

	    // Publisher ID.
	    if (!isset($publisher_id) || $publisher_id == '') {
		$publisher_id = get_current_user_id();
	    }
	    $publisher_company_id = foodbakery_company_id_form_user_id($publisher_id);

	    $posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
	    $posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';
	    $date_range = isset($_POST['date_range']) ? $_POST['date_range'] : '';
	    $status = isset($_POST['status']) ? $_POST['status'] : '';

	    $args = array(
		'post_type' => 'orders_inquiries',
		'post_status' => 'publish',
		'posts_per_page' => $posts_per_page,
		'paged' => $posts_paged,
		'meta_query' => array(
		    'relation' => 'AND',
		    array(
			'key' => 'foodbakery_restaurant_publisher',
			'value' => $publisher_company_id,
			'compare' => '=',
		    ),
		    array(
			'key' => 'foodbakery_order_type',
			'value' => 'inquiry',
			'compare' => '=',
		    )
		),
	    );
	    // Orders date range filter query
	    if ($date_range != '' && $date_range != 'undefined') {
		$new_date_range = explode(',', $date_range);
		$start_date = isset($new_date_range[0]) ? str_replace('/', '-', $new_date_range[0]) : '';
		$end_date = isset($new_date_range[1]) ? str_replace('/', '-', $new_date_range[1]) : '';
		$args['date_query'] = array(
		    'after' => date('Y-m-d', strtotime($start_date)),
		    'before' => date('Y-m-d', strtotime($end_date)),
		    'inclusive' => true,
		);
	    }
	    // Orders status filter meta query
	    if ($status != '' && $status != 'undefined') {
		$args['meta_query'][] = array(
		    'key' => 'foodbakery_order_status',
		    'value' => $status,
		    'compare' => '=',
		);
	    }

	    $booking_query = new WP_Query($args);
	    $total_posts = $booking_query->found_posts;

	    echo force_balance_tags($this->render_view_bookings($booking_query, 'received'));
	    wp_reset_postdata();

	    $total_pages = 1;
	    if ($total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page) {
		$total_pages = ceil($total_posts / $posts_per_page);

		$foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
		$foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
		$this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array('dashboard' => 'received_bookings'), $foodbakery_dashboard_link) : '';
		foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'received_bookings');
	    }

	    wp_die();
	}

	public function render_view_bookings($booking_query = '', $type = 'my') {
	    global $foodbakery_plugin_options, $foodbakery_form_fields;
	    ?>
	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	        <div class="row">
	    	<div class="element-title has-border right-filters-row">
			<?php
			$booking_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
			$date_range = isset($_REQUEST['date_range']) ? $_REQUEST['date_range'] : '';
			if ($type == 'my') {
			    ?>
			    <h5><?php esc_html_e('My Bookings', 'foodbakery'); ?></h5>
			    <?php
			    $action_id = 'foodbakery_publisher_bookings';
			} else {
			    ?>
			    <h5><?php esc_html_e('Recent Bookings', 'foodbakery'); ?></h5>
			    <?php
			    $action_id = 'foodbakery_publisher_received_bookings';
			}
			?>
	    	    <div class="right-filters row pull-right">
			    <?php if ((!$booking_query->have_posts() && $booking_status != '') || $booking_query->have_posts()) { ?>
				<div class="col-lg-6 col-md-6 col-xs-6">
				    <div class="input-field">
					<?php
					$drop_down_options[''] = esc_html__('Select Booking Status', 'foodbakery');
					$booking_statuses = isset($foodbakery_plugin_options['booking_status']) ? $foodbakery_plugin_options['booking_status'] : '';
					if (is_array($booking_statuses) && sizeof($booking_statuses) > 0) {
					    foreach ($booking_statuses as $key => $label) {
						$drop_down_options[$label] = $label;
					    }
					} else {
					    $drop_down_options = array(
						'Processing' => esc_html__('Processing', 'foodbakery'),
						'Completed' => esc_html__('Completed', 'foodbakery'),
						'Cancelled' => esc_html__('Cancelled', 'foodbakery'),
					    );
					}
					$foodbakery_opt_array = array();
					$foodbakery_opt_array['std'] = $booking_status;
					$foodbakery_opt_array['cust_id'] = 'filter_status';
					$foodbakery_opt_array['cust_name'] = 'booking-status';
					$foodbakery_opt_array['options'] = $drop_down_options;
					$foodbakery_opt_array['classes'] = 'chosen-select-no-single';
					$foodbakery_opt_array['extra_atr'] = ' onchange="foodbakery_bookings(this, \'' . $action_id . '\')"';
					$foodbakery_opt_array['return'] = false;
					$foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
					?>
					<script type="text/javascript">
					    
					    chosen_selectionbox();
					 
					</script>
				    </div>
				</div>
			    <?php } ?>
			    <?php if ((!$booking_query->have_posts() && $date_range != '') || $booking_query->have_posts()) { ?>
				<div class="col-lg-6 col-md-6 col-xs-6">
				    <div class="input-field">
					<i class="icon-angle-down"></i>
					<input type="text" id="daterange" value="<?php echo ($date_range != 'undefined' ) ? str_replace(',', ' - ', $date_range) : ''; ?>" placeholder="<?php echo esc_html__('Select Date Range', 'foodbakery'); ?>"/>
					<input type="hidden" name="date_range" id="date_range" value="<?php echo ($date_range != 'undefined' ) ? $date_range : ''; ?>" />
					<script type="text/javascript">
					    jQuery('#daterange').daterangepicker({
						autoUpdateInput: false,
						opens: 'left',
						locale: {
						    format: 'DD/MM/YYYY'
						}
					    },
						    function (start, end) {
							var date_range = start.format('DD/MM/YYYY') + ',' + end.format('DD/MM/YYYY');
							jQuery('#date_range').val(date_range);
							var actionString = "<?php echo esc_html($action_id); ?>";
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
					    jQuery('#daterange').on('cancel.daterangepicker', function (ev, picker) {
						jQuery('#daterange').val('');
						jQuery('#date_range').val('');
						var actionString = "<?php echo esc_html($action_id); ?>";
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
			    <?php } ?>
	    	    </div>
	    	</div>
	        </div>
	    </div>
	    <div class="row">
	        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	    	<div class="user-orders-list responsive-table">
			<?php if ($booking_query->have_posts()) : ?>
			    <ul class="table-generic" id="portfolio">
				<li>

				    <?php if ($type == 'my') {
					?>
		    		    <div class="orders-title"><?php esc_html_e('Restaurant Name', 'foodbakery'); ?></div>
					<?php
				    } else {
					?>
		    		    <div class="orders-title"><?php esc_html_e('Customer Name', 'foodbakery'); ?></div>
				    <?php }
				    ?>
				    <div class="orders-date"><?php esc_html_e('Date', 'foodbakery'); ?></div>
			  
				    <div class="orders-type"><?php esc_html_e('Status', 'foodbakery'); ?></div>
				    <div class="orders-price"><?php esc_html_e('Detail', 'foodbakery'); ?></div>
				</li>
				<?php echo force_balance_tags($this->render_booking_list_item_view($booking_query, $type)); ?>
			    </ul>
			<?php else: ?>
			    <div class="not-found">
				<i class="icon-error"></i>
				<p>
				    <?php
				    if ($type == 'received') {
					esc_html_e('You don\'t have any received booking.', 'foodbakery');
				    } else {
					esc_html_e('You don\'t have any booking.', 'foodbakery');
				    }
				    ?>
				</p>
			    </div>
			<?php endif; ?>
	    	</div>
	        </div>
	    </div>
	    <?php
	}

	public function render_booking_list_item_view($booking_query, $type = 'my') {
	    while ($booking_query->have_posts()) : $booking_query->the_post();

		$order_restaurant_id = get_post_meta(get_the_ID(), 'foodbakery_restaurant_id', true);
		$order_type = get_post_meta(get_the_ID(), 'foodbakery_order_type', true);
		$order_date = get_post_meta(get_the_ID(), 'foodbakery_order_date', true);
		$order_status = get_post_meta(get_the_ID(), 'foodbakery_order_status', true);
		$foodbakery_booking_publisher = get_post_meta(get_the_ID(), 'foodbakery_booking_publisher', true);
		?>
		<li>

		    <?php
		    if ($type == 'my') {
			?>
		        <div class="orders-title">
		    	<h6 class="order-title">
		    	    <a href="javascript:void(0);" data-toggle="modal" data-target="#booking-detail-<?php echo get_the_ID() ?>">
				    <?php
				    echo wp_trim_words(get_the_title($order_restaurant_id), 6, '...');
				    ?>
		    	    </a>
		    	    <span>( #<?php echo get_the_ID(); ?> )</span>
		    	</h6>
		        </div>
			<?php
		    } else {
			?>
		        <div class="orders-title">
		    	<h6 class="order-title">
		    	    <a href="javascript:void(0);" data-toggle="modal" data-target="#booking-detail-<?php echo get_the_ID() ?>">
				    <?php
				    echo wp_trim_words(get_the_title($foodbakery_booking_publisher), 6, '...');
				    ?>
		    	    </a>
		    	    <span>( #<?php echo get_the_ID(); ?> )</span>
		    	</h6>
		        </div>
			<?php
		    }
		    ?>
		    <div class="orders-date">
			<span><?php echo get_the_date(); ?></span>
		    </div> 
		    <div class="orders-status">
			<?php $booking_status_color = $this->foodbakery_booking_status_color($order_status); ?>
			<span class="booking-status" style="background-color: <?php echo esc_html($booking_status_color); ?>;"><?php echo esc_html__(ucfirst($order_status), 'foodbakery'); ?></span>
		    </div>
		    <div class="orders-price"> 
			<a href="javascript:void(0);" data-toggle="modal" data-target="#booking-detail-<?php echo get_the_ID() ?>"><i class="icon-plus2 text-color"></i></a> 
		    </div>
		</li>
		<div class="modal fade menu-order-detail menu-order-info" id="booking-detail-<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		    <div class="modal-dialog" role="document">
			<div class="modal-content">
			    <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h2><a><?php esc_html_e('Booking Detail', 'foodbakery') ?></a></h2>
			    </div>
			    <div class="modal-body booking-modal-body">
				<div class="order-detail-inner">
				    <h3>
					<?php echo get_the_title($order_restaurant_id); ?>
				    </h3>
				    <?php
				    $order_form_fields = get_post_meta(get_the_ID(), 'foodbakery_order_form_fields', true);

				    $fields_values = [];
				    $textarea_fields_values = array();
				    if (is_array($order_form_fields) && !empty($order_form_fields)) {
					foreach ($order_form_fields as $order_form_field) {
					    $field_type = isset($order_form_field['type']) ? $order_form_field['type'] : '';
					    $label = isset($order_form_field['label']) ? $order_form_field['label'] : '';
					    $meta_key = isset($order_form_field['meta_key']) ? $order_form_field['meta_key'] : '';
					    if ($meta_key != '') {
						if ($field_type == 'availability') {
						    $value = get_post_meta(get_the_ID(), $meta_key, true);
						    $value = date(get_option('date_format'), strtotime($value));
						    $value = $value . ' ' . date_i18n(get_option('time_format'), get_post_meta(get_the_ID(), 'time-' . $meta_key, true));
						    $fields_values[$meta_key] = array('label' => $label, 'value' => $value);
						} else {
						    $value = get_post_meta(get_the_ID(), $meta_key, true);
						    if ($value != '' && $value != 'undefined') {
							if ($field_type == 'textarea') {
							    $textarea_fields_values[$meta_key] = array('label' => $label, 'value' => $value);
							} else {
							    if ($field_type == 'time') {
								$value = date_i18n('h:i a', $value);
							    }
							    $fields_values[$meta_key] = array('label' => $label, 'value' => $value);
							}
						    }
						}
					    }
					}
				    }
				    if (!empty($fields_values) || !empty($textarea_fields_values)) {
					?>
		    		    <ul class="order-detail-options">
		    			<li>
		    			    <strong><?php esc_html_e('Booking ID', 'foodbakery'); ?> :</strong>
		    			    <span><?php echo intval(get_the_ID()); ?></span>
		    			</li>
		    			<li>
		    			    <strong><?php esc_html_e('Booking Date', 'foodbakery'); ?> :</strong>
		    			    <span>
						    <?php
						    $format = '';
						    $date_format = '';
						    $time_format = '';
						    $date_format = get_option('date_format');
						    $time_format = get_option('time_format');
						    $format = $date_format . ' ' . $time_format;
						    echo get_the_time($format, get_the_ID());
						    ?>
		    			    </span>
		    			</li>
		    		    </ul>

		    		    <h3><?php echo esc_html__('Customer Deatil', 'foodbakery'); ?></h3>

		    		    <ul class="order-detail-options">

					    <?php
					    if (!empty($fields_values)) {
						?>

						<?php
						foreach ($fields_values as $key => $value) {
						    $label = isset($value['label']) ? $value['label'] : '';
						    $value = isset($value['value']) ? $value['value'] : '';
						    if ($label || $value) {
							?>
							<li>
							    <?php if ($label) { ?>
				    			    <strong><?php echo esc_html__($label, 'foodbakery'); ?>:</strong>
							    <?php } ?>
							    <?php if ($value) { ?>
				    			    <span><?php echo $value; ?></span>
							    <?php } ?>
							</li>
							<?php
						    }
						}
					    }

					    if (is_array($textarea_fields_values) && !empty($textarea_fields_values)) {

						foreach ($textarea_fields_values as $key => $value) {
						    $label = isset($value['label']) ? $value['label'] : '';
						    $value = isset($value['value']) ? $value['value'] : '';
						    if ($label || $value) {
							?>
							<li class="order-detail-message">
							    <?php if ($label) { ?>
				    			    <strong><?php echo esc_html__($label,'foodbakery'); ?>:</strong>
							    <?php } ?>
							    <?php if ($value) { ?>
				    			    <span><?php echo esc_html__($value, 'foodbakery'); ?></span>
							    <?php } ?>
							</li>
							<?php
						    }
						}
					    }
					    ?>
		    		    </ul>
				    <?php } ?>
				    <?php
				    // Booking Status.
				    $this->booking_status(get_the_ID());
				    ?>
				</div>
			    </div>
			</div>
		    </div>
		</div>
		<?php
	    endwhile;
	    ?>
	    <script>
	        (function ($) {
	    	$(document).ready(function () {
	    	    $(".menu-order-info .modal-dialog .modal-content").mCustomScrollbar({
	    		setHeight: 467,
	    		theme: "minimal-dark",
	    		mouseWheelPixels: 100
	    	    });
	    	});
	        })(jQuery);
	    </script>
	    <?php
	}

	public function booking_status($booking_id = '') {
	    global $post, $foodbakery_form_fields, $current_user, $foodbakery_plugin_options;

	    if ($booking_id == '') {
		$booking_id = $post->ID;
	    }

	    $user_id = $current_user->ID;
	    $publisher_id = foodbakery_company_id_form_user_id($user_id);
	    $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

	    $booking_status = get_post_meta($booking_id, 'foodbakery_order_status', true);
	    ?>
	    <?php if ($publisher_type == 'restaurant') { ?>
		<h3>
		    <?php esc_html_e('Booking Status', 'foodbakery'); ?>
		</h3>
	    <?php } ?>
	    <div class="booking-status-holder">
		<?php if ($publisher_type == 'restaurant') { ?>
		    <div class="input-field">
			<span class="status-loader booking-status-loader-<?php echo esc_html($booking_id); ?>"></span>
			<?php
			$booking_statuses = isset($foodbakery_plugin_options['booking_status']) ? $foodbakery_plugin_options['booking_status'] : '';
			if (is_array($booking_statuses) && sizeof($booking_statuses) > 0) {
			    foreach ($booking_statuses as $key => $label) {
				$drop_down_options[$label] = $label;
			    }
			} else {
			    $drop_down_options = array(
				'Processing' => esc_html__('Processing', 'foodbakery'),
				'Completed' => esc_html__('Completed', 'foodbakery'),
				'Cancelled' => esc_html__('Cancelled', 'foodbakery'),
			    );
			}

			$foodbakery_opt_array = array();
			$foodbakery_opt_array['std'] = $booking_status;
			$foodbakery_opt_array['cust_id'] = 'booking-status';
			$foodbakery_opt_array['cust_name'] = 'booking-status';
			$foodbakery_opt_array['options'] = $drop_down_options;
			$foodbakery_opt_array['classes'] = 'chosen-select-no-single';
			$foodbakery_opt_array['extra_atr'] = ' onchange="foodbakery_update_booking_status(this, \'' . $booking_id . '\', \'' . admin_url('admin-ajax.php') . '\')"';
			$foodbakery_opt_array['return'] = false;

			$foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
			?>
			<script type="text/javascript">
			    jQuery(document).ready(function () {
				chosen_selectionbox();
			    });
			</script>

		    </div>
		<?php } else { ?>
		    <?php $booking_status_color = $this->foodbakery_booking_status_color($booking_status); ?>
		    <div class="booking-status-process booking-status">
			<p style="background:<?php echo esc_html($booking_status_color); ?>;"><?php
                 printf(__('Your booking is %s', 'foodbakery'), esc_html($booking_status));
			    ?></p>
		    </div>
		<?php } ?>
	    </div>
	    <?php
	}

	public function foodbakery_booking_status_color($booking_status = 'Processing') {
	    global $foodbakery_plugin_options;

	    $booking_statuses = isset($foodbakery_plugin_options['booking_status']) ? $foodbakery_plugin_options['booking_status'] : '';
	    $booking_status_color = isset($foodbakery_plugin_options['booking_status_color']) ? $foodbakery_plugin_options['booking_status_color'] : '';

	    if (is_array($booking_statuses) && sizeof($booking_statuses) > 0) {
		foreach ($booking_statuses as $key => $lable) {
		    if (strtolower($lable) == strtolower($booking_status)) {
			return $booking_color = isset($booking_status_color[$key]) ? $booking_status_color[$key] : '';
			break;
		    }
		}
	    }
	}

	public function foodbakery_update_booking_status_callback() {
	    $json = array();

	    $order_id = foodbakery_get_input('order_id', NULL, 'STRING');
	    $order_status = foodbakery_get_input('order_status', NULL, 'STRING');

	    if ($order_id && $order_status) {
		update_post_meta($order_id, 'foodbakery_order_status', $order_status);
		// Update order status email
		do_action('foodbakery_booking_status_updated_email', $order_id);

		$json['type'] = "success";
		$json['msg'] = esc_html__("Booking status has been changed.", "direcory");
	    } else {
		$json['type'] = "error";
		$json['msg'] = esc_html__("Booking status not changed.", "direcory");
	    }

	    echo json_encode($json);
	    exit();
	}

    }

    global $bookings;
    $bookings = new Foodbakery_Publisher_Bookings();
}