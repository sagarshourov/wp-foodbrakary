<?php
/**
 * File Type: Services Page Element
 */
if ( ! class_exists('foodbakery_discussion_element') ) {

	class foodbakery_discussion_element {

		/**
		 * Start construct Functions
		 */
		public function __construct() {
			add_action('wp_ajax_foodbakery_discussion_submit', array( $this, 'foodbakery_discussion_submit_callback' ), 10, 2);
			add_action('wp_ajax_nopriv_foodbakery_discussion_submit', array( $this, 'foodbakery_discussion_submit_callback' ), 10, 2);
			add_action('foodbakery_discussion_list', array( $this, 'foodbakery_discussion_list_callback' ));
			add_action('wp_ajax_foodbakery_update_order_status', array( $this, 'foodbakery_update_order_status_callback' ), 10);
			add_action('wp_ajax_foodbakery_update_order_read_status', array( $this, 'foodbakery_update_order_read_status_callback' ), 10);
			add_action('wp_ajax_foodbakery_closed_order', array( $this, 'foodbakery_closed_order_callback' ), 10);
		}

		public function foodbakery_discussion_submit_callback() {
			global $post;
			$current_user = wp_get_current_user();

			$json = array();
			$json['empty'] = false;
			if ( 0 == $current_user->ID ) {
				$json['type'] = "error";
				$json['msg'] = esc_html__("You must be login to post comment.", "direcory");
				echo json_encode($json);
				exit();
			}

			$comment_post_ID = foodbakery_get_input('comment_post_ID', NULL, 'STRING');
			$comment_publisher = foodbakery_get_input('comment_publisher', NULL, 'STRING');
			$publisher_id = foodbakery_company_id_form_user_id($current_user->ID);
			$restaurant_publisher_company = get_post_meta($comment_post_ID, 'foodbakery_publisher_company', true);
			$order_publisher_company = get_post_meta($comment_post_ID, 'foodbakery_order_user_company', true);

			$order_type = get_post_meta($comment_post_ID, 'foodbakery_order_type', true);
			if ( $order_type == 'order' ) {
				$order_type_string = esc_html__('order', 'foodbakery');
			} else {
				$order_type_string = esc_html__('inquiry', 'foodbakery');
			}

			$message = foodbakery_get_input('message', NULL, 'STRING');
			if ( '' == $message ) {
				$json['empty'] = true;
				$json['type'] = "error";
				$json['msg'] = esc_html__("Please enter message.", "direcory");
				echo json_encode($json);
				exit();
			}

			if ( $publisher_id == $restaurant_publisher_company || $publisher_id == $order_publisher_company ) {

				$order_status = get_post_meta($comment_post_ID, 'foodbakery_order_status', true);

				if ( $order_status == 'Closed' ) {
					$json['type'] = "error";
					$json['msg'] = esc_html__("You can't send message because your " . $order_type_string . " has been closed.", "direcory");
					echo json_encode($json);
					exit();
				}

				if ( true !== Foodbakery_Member_Permissions::check_permissions('orders') ) {
					$json['type'] = "error";
					$json['msg'] = esc_html__("You can't send message due to publisher permission.", "direcory");
					echo json_encode($json);
					exit();
				}

				$time = current_time('mysql');
				$data = array(
					'comment_post_ID' => $comment_post_ID,
					'comment_author' => $current_user->display_name,
					'comment_author_email' => $current_user->user_email,
					'comment_author_url' => '',
					'comment_content' => $message,
					'comment_type' => '',
					'comment_parent' => 0,
					'user_id' => $current_user->ID,
					'comment_author_IP' => $this->get_the_user_ip(),
					'comment_agent' => $this->get_the_user_agent(),
					'comment_date' => $time,
					'comment_approved' => 1,
				);

				// check comment already added or not.
				$this->comment_validation($data);
				// insert new comment
				$comment_id = wp_insert_comment($data);
				update_comment_meta($comment_id, 'comment_publisher', $comment_publisher);

				// added last post comment in comments list.
				$args = array(
					'post_id' => $comment_post_ID,
					'comment__in' => array( $comment_id ),
					'status' => 'approve',
				);
				$comments = get_comments($args);
				foreach ( $comments as $comment ) {
					$json['comments_count'] = get_comments_number($comment_post_ID);
					;
					$json['comments_number'] = $this->foodbakery_comments_number($comment_post_ID);
					$json['new_comment'] = $this->foodbakery_discussion_list_items($comments);
				}
				update_post_meta($comment_post_ID, 'read_status', '0');

				$sender_id = $publisher_id;
				$publisher_name = get_the_title($sender_id);
				if ( $publisher_id == $restaurant_publisher_company ) {
					$reciever_id = $order_publisher_company;
					update_post_meta($comment_post_ID, 'buyer_read_status', '0');
				} else {
					$reciever_id = $restaurant_publisher_company;
					update_post_meta($comment_post_ID, 'seller_read_status', '0');
				}


				/*
				 * Adding Notification
				 */
				$notification_type = 'inquiry';
				if ( $order_type == 'order' ) {
					$notification_type = 'order_messages';
				}
				$notification_array = array(
					'type' => $notification_type,
					'element_id' => $comment_post_ID,
					'sender_id' => $sender_id,
					'reciever_id' => $reciever_id,
					'message' => __($publisher_name . ' sent you a message on ' . $order_type_string . ' <a href="javascript:foodbakery_order_detail(\'' . $comment_post_ID . '\',\'my\');">' . wp_trim_words(get_the_title($comment_post_ID), 5) . '</a> .', 'foodbakery'),
				);
				do_action('foodbakery_add_notification', $notification_array);


				$json['type'] = "success";
				$json['msg'] = esc_html__("Your message has been sent successfully.", "direcory");
				echo json_encode($json);
				exit();
			} else {
				$json['type'] = "error";
				$json['msg'] = esc_html__("You can't send message against this " . $order_type_string . ".", "direcory");
				echo json_encode($json);
				exit();
			}

			echo json_encode($json);
			wp_die();
		}

		public function comment_validation($commentdata) {
			global $wpdb;

			$json = array();
			$cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';
			$cs_success_html = '<div class="alert alert-success"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button><p><i class="icon-checkmark6"></i>';
			$cs_msg_html = '</p></div>';

			$dupe = $wpdb->prepare(
					"SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = %s AND comment_approved != 'trash' AND ( comment_author = %s ", wp_unslash($commentdata['comment_post_ID']), wp_unslash($commentdata['comment_parent']), wp_unslash($commentdata['comment_author'])
			);
			if ( $commentdata['comment_author_email'] ) {
				$dupe .= $wpdb->prepare(
						"AND comment_author_email = %s ", wp_unslash($commentdata['comment_author_email'])
				);
			}
			$dupe .= $wpdb->prepare(
					") AND comment_content = %s LIMIT 1", wp_unslash($commentdata['comment_content'])
			);

			$dupe_id = $wpdb->get_var($dupe);
			$dupe_id = apply_filters('duplicate_comment_id', $dupe_id, $commentdata);
			if ( $dupe_id ) {
				$json['type'] = "error";
				$json['message'] = $cs_danger_html . esc_html__("Duplicate message detected; it looks as though you&#8217;ve already said that!", "direcory") . $cs_msg_html;
				echo json_encode($json);
				exit();
			}
		}

		public function get_the_user_ip() {
			if ( ! empty($_SERVER['HTTP_CLIENT_IP']) ) {
				//check ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
				//to check ip is pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return apply_filters('wpb_get_ip', $ip);
		}

		public function get_the_user_agent() {
			return $_SERVER['HTTP_USER_AGENT'];
		}

		public function foodbakery_discussion_list_callback() {
			global $post;

			$args = array(
				'post_id' => $post->ID,
				'status' => 'approve',
				'orderby' => 'ID',
				'order' => 'ASC',
			);
			$comments = get_comments($args);
			?>
			<div class="order-discussions-holder">
				<?php if ( $comments ) { ?>
					<div class="order-discussions">
						<div class="element-title">
							<h3>
								<?php echo esc_html($this->foodbakery_comments_number($post->ID)); ?>
							</h3>
						</div>
						<ul id="discussion-list" class="order-discussion-list">
							<?php echo force_balance_tags($this->foodbakery_discussion_list_items($comments)); ?>
						</ul>

					</div>
				<?php } ?>
			</div>
			<?php
		}

		public function foodbakery_comments_number($comment_post_ID = '') {
			$comments_number = get_comments_number($comment_post_ID);
			if ( 1 >= $comments_number ) {
				$comments = sprintf(esc_html__('%s Message', 'foodbakery'), $comments_number);
			} else {
				$comments = sprintf(esc_html__('%s Messages', 'foodbakery'), $comments_number);
			}
			return $comments;
		}

		public function foodbakery_discussion_list_items($comments) {
			global $post, $foodbakery_publisher_profile;
			$output = '';
			ob_start();
			foreach ( $comments as $comment ) {
				$login_user_id = get_current_user_id();
				$login_user_comapny_id = foodbakery_company_id_form_user_id($comment_user_id);
				$user = get_user_by('email', $comment->comment_author_email);
				$comment_user_id = $user->ID;
				$publisher_id = foodbakery_company_id_form_user_id($comment_user_id);
				$restaurant_publisher_id = get_post_meta($comment->comment_post_ID, 'foodbakery_publisher_company', true);
				$order_user_company_id = get_post_meta($comment->comment_post_ID, 'foodbakery_order_user_company', true);
				$profile_image_id = $foodbakery_publisher_profile->publisher_get_profile_image($user->ID);
				if ( $publisher_id == $restaurant_publisher_id ) {
					$discussion_publisher_type = 'seller';
				} else if ( $publisher_id == $order_user_company_id ) {
					$discussion_publisher_type = 'buyer';
				}
				?>
				<li class="<?php echo esc_html($discussion_publisher_type); ?>">
					<?php
					if ( isset($profile_image_id) && $profile_image_id !== '' ) {
						echo '<div class="img-holder">
                                                            <figure><img src="' . esc_url($profile_image_id) . '" alt=""></figure>
                                                    </div>';
					}
					?>
					<div class="text-holder">
						<div class="heading">
							<h5><?php
								if ( $login_user_comapny_id == $publisher_id ) {
									esc_html_e('Me', 'foodbakery');
								} else {
									echo get_the_title($publisher_id);
								}
								?></h5>
							<span datetime="<?php echo date('Y-m-d', strtotime($comment->comment_date)); ?>" class="post-date">
								<?php echo human_time_diff(strtotime($comment->comment_date), current_time('timestamp')) . ' ' . esc_html__('ago'); ?>
							</span>
						</div>
						<?php echo apply_filters('the_content', $comment->comment_content); ?>
					</div>
				</li>
				<?php
			}
			$output = ob_get_clean();
			return $output;
		}

		public function foodbakery_update_order_read_status_callback() {
			$json = array();

			$order_id = foodbakery_get_input('order_id', NULL, 'STRING');
			$order_type = get_post_meta($order_id, 'foodbakery_order_type', true);
			$order_read_status = foodbakery_get_input('order_read_status', NULL, 'STRING');
			$user_status = foodbakery_get_input('user_status', NULL, 'STRING');

			if ( $order_type == 'order' ) {
				$order_type_string = esc_html__('order', 'foodbakery');
			} else {
				$order_type_string = esc_html__('inquiry', 'foodbakery');
			}

			if ( $order_id ) {
				update_post_meta($order_id, 'read_status', $order_read_status);
				if ( $user_status == 'seller' ) {
					update_post_meta($order_id, 'seller_read_status', $order_read_status);
				} else {
					update_post_meta($order_id, 'buyer_read_status', $order_read_status);
				}
				$json['type'] = "success";
				if ( $order_read_status == 0 ) {
					$json['read_type'] = 'read';
					$json['msg'] = esc_html__("The " . $order_type_string . " has been marked as unread.", "direcory");
				} else {
					$json['read_type'] = 'unread';
					$json['msg'] = esc_html__("The " . $order_type_string . " has been marked as read.", "direcory");
				}
			}
			echo json_encode($json);
			wp_die();
		}

		public function foodbakery_update_order_status_callback() {
			$json = array();

			$order_id = foodbakery_get_input('order_id', NULL, 'STRING');
			$order_type = get_post_meta($order_id, 'foodbakery_order_type', true);
			$order_status = foodbakery_get_input('order_status', NULL, 'STRING');

			if ( $order_type == 'order' ) {
				$order_type_string = esc_html__('Order', 'foodbakery');
			} else {
				$order_type_string = esc_html__('Inquiry', 'foodbakery');
			}

			if ( $order_id && $order_status ) {
				update_post_meta($order_id, 'foodbakery_order_status', $order_status);

				// Update order/inquiry status email hooks.
				$order_type = get_post_meta($order_id, 'foodbakery_order_type', true);
				if ( $order_type == 'order' ) {
					// Update order status email
					do_action('foodbakery_order_status_updated_email', $order_id);
				} else {
					// Update inquiry status email
					do_action('foodbakery_inquiry_status_updated_email', $order_id);
				}

				$json['type'] = "success";
				$json['msg'] = __($order_type_string . " status has been changed.", "direcory");
			} else {
				$json['type'] = "error";
				$json['msg'] = __($order_type_string . " status not changed.", "direcory");
			}

			echo json_encode($json);
			exit();
		}

		public function foodbakery_closed_order_callback() {
			$json = array();

			$order_id = foodbakery_get_input('order_id', NULL, 'STRING');
			$order_type = get_post_meta($order_id, 'foodbakery_order_type', true);
			update_post_meta($order_id, 'foodbakery_order_status', 'Closed');

			if ( $order_type == 'order' ) {
				$order_type_string = esc_html__('order', 'foodbakery');
			} else {
				$order_type_string = esc_html__('inquiry', 'foodbakery');
			}
			$json['type'] = "success";
			$json['msg'] = esc_html__("Your " . $order_type_string . " has been closed.", "direcory");
			echo json_encode($json);
			exit();
		}

	}

	global $foodbakery_discussion_element;
	$foodbakery_discussion_element = new foodbakery_discussion_element();
}