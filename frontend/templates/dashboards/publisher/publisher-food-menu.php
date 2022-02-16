<?php

/**
 * Publisher FoodMenus
 *
 */
if (!class_exists('Foodbakery_Publisher_FoodMenus')) {

	class Foodbakery_Publisher_FoodMenus
	{

		/**
		 * Start construct Functions
		 */
		public function __construct()
		{
			add_action('wp_ajax_foodbakery_publisher_food_menu', array($this, 'foodbakery_publisher_foodmenus_callback'));
		}

		public function foodbakery_publisher_foodmenus_callback()
		{
			global $restaurant_add_counter;

			$restaurant_add_counter = rand(1000000, 99999999);
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

?>
			<!-- <form method="post" enctype="multipart/form-data"> -->
			<ul class="restaurant-menu-nav nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#menu-cats-items"><?php esc_html_e('Menu Categories', 'foodbakery') ?></a></li>
				<li><a data-toggle="tab" href="#menu-list-items"><?php esc_html_e('Food Items', 'foodbakery') ?></a></li>
			</ul>

			<div class="tab-content">
				<script>
					if ($(".menu-items-list").length != '') {

						$('.menu-items-list').sortable({
							handle: '.drag-option',
							cursor: 'move',
							start: function(event, ui) {
								ui.item.startPos = ui.item.index();
							},
							change: function(event, ui) {
								ui.item.movement = ui.position.top - ui.originalPosition.top > 0 ? false : true;
							},
							stop: function(event, ui) {
								console.log("Start position: " + ui.item.startPos);
								console.log("New position: " + ui.item.index());

								var replacePosition = 0;

								// if (ui.item.index() > 0) {
								// 	replacePosition = ui.item.index() + 1;
								// }

								if (ui.item.movement) {
									replacePosition = ui.item.index() + 1;
									console.log('Dragged Up');
								} else {
									replacePosition = ui.item.index() - 1;
									console.log('Dragged Down');
								}



								var replaceItem = $(ui.item).parent().children().eq(replacePosition);

	
								// console.log('Replace key  :' + replaceItem.attr('sa_key'));

								// console.log('start key  :' +  ui.item.attr('sa_key'));




								// var change_position = ui.item.startPos - ui.item.index();

								// console.log('Position : ' + change_position);

								// var total_new_position = ui.item.attr('sa_key');

								// console.log('Key position : ' + total_new_position);


								 var menu_item_counter = ui.item.attr('menu_item_counter');
								 var restaurant_id = ui.item.attr('restaurant_id');
								 var restaurant_ad_counter = ui.item.attr('restaurant_ad_counter');

								// var thisObj = jQuery('.menu-item-' + menu_item_counter);
								//foodbakery_show_loader('.menu-item-' + menu_item_counter, '', 'foodbakery_loader', thisObj);

								jQuery.ajax({
									type: "POST",
									url: foodbakery_globals.ajax_url,
									dataType: 'json',
									data: 'restaurant_ad_counter=' + restaurant_ad_counter + '&menu_item_counter=' + menu_item_counter + '&restaurant_id=' + restaurant_id + '&replace_key=' + replaceItem.attr('sa_key') + '&old_key=' + ui.item.attr('sa_key') + '&action=sa_restaurant_move_menu_cat_item',
									success: function(response) {
										console.log(response);

										if (response === 'Success') {


											$('.menu-item-' + menu_item_counter).after(response.html);
											foodbakery_hide_loader();
											var response = {
												type: 'success',
												msg: 'Moved Successfully!'
											};
											foodbakery_show_response(response);

											return true;
										}

									}
								});


							},

						});
					}
					if ($(".restaurant_menu_items_cat_list").length != '') {
						$('.restaurant_menu_items_cat_list').sortable({
							handle: '.drag-option',
							cursor: 'move'
							
						});
					}
					if ($(".restaurant-menu-cats-list").length != '') { //cat move

						$('.restaurant-menu-cats-list').sortable({
							handle: '.drag-option',
							cursor: 'move',
							start: function(event, ui) {
								ui.item.startPos = ui.item.index();
							},
							change: function(event, ui) {
								ui.item.movement = ui.position.top - ui.originalPosition.top > 0 ? false : true;
							},
							stop: function(event, ui) { 
								var replacePosition = 0;
								if (ui.item.movement) {
									replacePosition = ui.item.index() + 1;
									console.log('Dragged Up');
								} else {
									replacePosition = ui.item.index() - 1;
									console.log('Dragged Down');
								}

								var replaceItem = $(ui.item).parent().children().eq(replacePosition);

								 var menu_item_counter = ui.item.attr('menu_item_counter');
								 var restaurant_id = ui.item.attr('restaurant_id');
								// var restaurant_ad_counter = ui.item.attr('restaurant_ad_counter');
								jQuery.ajax({
									type: "POST",
									url: foodbakery_globals.ajax_url,
									dataType: 'json',
									data: 'menu_item_counter=' + menu_item_counter + '&restaurant_id=' + restaurant_id + '&replace_key=' + replaceItem.attr('sa_key') + '&old_key=' + ui.item.attr('sa_key') + '&action=sa_restaurant_move_cat_item',
									success: function(response) {
										if (response === 'Success') {
											$('.menu-item-' + menu_item_counter).after(response.html);
											foodbakery_hide_loader();
											var response = {
												type: 'success',
												msg: 'Moved Successfully!'
											};
											foodbakery_show_response(response);

											return true;
										}

									}
								});

							}
						});
					}
					if ($('[data-toggle="tooltip"]').length != '') {
						$('[data-toggle="tooltip"]').tooltip();
					}
				</script>
				<div id="menu-cats-items" class="tab-pane fade in active">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php do_action('foodbakery_restaurant_menu_cats') ?>
						</div>
					</div>
				</div>
				<div id="menu-list-items" class="tab-pane fade">
					<?php
					$restaurants_type_post = get_posts('post_type=restaurant-type&posts_per_page=1&post_status=publish');
					$selected_type = isset($restaurants_type_post[0]->post_name) ? $restaurants_type_post[0]->post_name : '';
					$restaurant_type_id = isset($restaurants_type_post[0]->ID) ? $restaurants_type_post[0]->ID : 0;

					if (isset($pub_restaurant[0]) && $pub_restaurant[0] != '') {
						$get_restaurant_id = $pub_restaurant[0];
						echo  $menu_html = apply_filters('foodbakery_restaurant_menu_items', $restaurant_add_counter, $restaurant_type_id, $get_restaurant_id);
					} else {

						echo  $menu_html = '<div class="not-found">
									<i class="icon-error"></i>
									<p>' . esc_html__('Sorry! No Menu Found.', 'foodbakery') . '</p>
								</div>';
					}

					// echo force_balance_tags($menu_html);
					?>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<!-- <div id="update-menu-items-holder" class="payment-holder input-button-loader">
								<input class="redirect-button-click update-menu-items" value="<?php esc_html_e('Update Menu', 'foodbakery') ?>" type="submit">
								<input name="food_menu_updating" value="1" type="hidden">
							</div> -->
					</div>
				</div>
			</div>
			<!-- </form> -->
<?php
			die;
		}
	}

	global $publisher_foodmenus;
	$publisher_foodmenus = new Foodbakery_Publisher_FoodMenus();
}
