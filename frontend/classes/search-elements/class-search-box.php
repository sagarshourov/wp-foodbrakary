<?php
if (!class_exists('Search_Box')) {

    Class Search_Box {

	public function __construct() {
	    add_action('foodbakery_search_box', array($this, 'foodbakery_search_box_callback'));
	    add_action('wp_ajax_foodbakery_type_cats', array($this, 'foodbakery_type_cats_callback'));
	    add_action('wp_ajax_nopriv_foodbakery_type_cats', array($this, 'foodbakery_type_cats_callback'));
	}

	public function foodbakery_type_cats_callback() {
	    global $foodbakery_form_fields_frontend, $foodbakery_html_fields_frontend;
	    $foodbakery_type_id = $_POST['foodbakery_types'];


	    if ($foodbakery_type_id != '') {
		$foodbakery_type_id_array = explode(',', $foodbakery_type_id);
		


		foreach ($foodbakery_type_id_array as $type_id) {
		    if ($type_id != '') {
			$all_terms_types = get_the_terms($type_id, 'restaurant-category');
			if (!empty($all_terms_types)) {
			    foreach ($all_terms_types as $type_term) {



				$foodbakery_opt_array = array(
				    'name' => $type_term->name,
				    'desc' => '',
				    'hint_text' => '',
				    'echo' => true,
				    'std' => $type_term->term_id,
				    'simple' => true,
				    'id' => 'search_categories',
				    'cust_id' => 'search_categories' . $type_term->term_id,
				    'field_params' => array(
				    ),
				);
				echo '<li>
                                                                    <div class="checkbox">';
				$foodbakery_form_fields_frontend->foodbakery_form_checkbox_render($foodbakery_opt_array);

				echo '<label for="foodbakery_search_categories' . $type_term->term_id . '">';
				echo '<i class="icon-home"></i>' . $type_term->name . '</label>';
				echo '<span>(32)</span>
                                                                    </div>
                                                                </li>';
			    }
			}
		    }
		}



		wp_die();
	    }
	}

	public function foodbakery_search_box_callback() {
	    ?>
	    <aside class="page-sidebar col-lg-4 col-md-4 col-sm-12 col-xs-12"><div class="foodbakery-filters"><div class="filters-options"><div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			    <?php
			    global $foodbakery_form_fields_frontend, $foodbakery_html_fields_frontend;
			    $search_title = isset($atts['search_title']) ? $atts['search_title'] : 'a';
			    $search_categories = isset($atts['search_categories']) ? $atts['search_categories'] : 'a';
			    $search_location = isset($atts['search_location']) ? $atts['search_location'] : '5';
			    $search_date_range = isset($atts['search_date_range']) ? $atts['search_date_range'] : 'a';
			    $search_min_lodaing = isset($atts['search_min_lodaing']) ? $atts['search_min_lodaing'] : 'a';
			    $search_price_per_person = isset($atts['search_price_per_person']) ? $atts['search_price_per_person'] : 'a';
			    $search_mark = isset($atts['search_mark']) ? $atts['search_mark'] : '5';
			    $search_point_of_interest = isset($atts['search_point_of_interest']) ? $atts['search_point_of_interest'] : 'a';
			    $search_loyalty = isset($atts['search_loyalty']) ? $atts['search_loyalty'] : 'a';


			    if ($search_title != "") {
				
			    }
			    if ($search_categories != "") {
				?>
				<div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="restaurant-types">
					<a role="button" data-toggle="collapse" href="#cuisine_categories" aria-expanded="true" aria-controls="collapseOne">Restaurant Types</a>
				    </div>
				    <div id="restaurant-types" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="restaurant-types">
					<div class="panel-body">
					    <div class="select-categories">

						<ul class="cs-checkbox-list">


						    <?php
						    $args = array('post_type' => 'restaurant-type', 'posts_per_page' => '-1', 'post_status' => 'publish');
						    $loop = new Wp_Query($args);
						    $restaurant_tax_cats = get_terms('restaurant-category');
						    //print_r($restaurant_tax_cats);

						    while ($loop->have_posts()) {
							$loop->the_post();
							?>
		    				    <li>
		    					<div class="checkbox">
								<?php
								$foodbakery_opt_array = array(
								    'name' => get_the_title(),
								    'desc' => '',
								    'hint_text' => '',
								    'echo' => true,
								    'std' => get_the_ID(),
								    'simple' => true,
								    'id' => 'search_types[]',
								    'cust_id' => 'search_types' . get_the_ID(),
								    'extra_atr' => "onclick=foodbakery_type_cats('foodbakery_search_types[]')",
								    'field_params' => array(
								    ),
								);
								$foodbakery_form_fields_frontend->foodbakery_form_checkbox_render($foodbakery_opt_array);
								?>
		    					    <label for="<?php echo 'search_types' . get_the_ID(); ?>"><i class="icon-home"></i><?php echo get_the_title(); ?></label>
		    					    <span>(32)</span>
		    					</div>
		    				    </li>
							<?php
						    }
						    ?>

						</ul>

					    </div>
					</div>
				    </div>
				</div>
		                <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="restaurant-categories">
					<a role="button" data-toggle="collapse" href="#cuisine_categories" aria-expanded="true" aria-controls="collapseOne">Restaurant categories</a>
				    </div>
				    <div id="restaurant-categories" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="cuisine-categories">
					<div class="panel-body">
					    <div class="select-categories">

						<ul class="cs-checkbox-list" id="list_Cats">




						</ul>

					    </div>
					</div>
				    </div>
				</div>
				<?php
			    }

			    if ($search_date_range != "") {
				?>
				<div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="date-range">
					<a class="collapsed" role="button" data-toggle="collapse" href="#collapsethree" aria-expanded="false" aria-controls="collapsethree">
					    Date Range
					</a>
				    </div>
				    <div id="collapsethree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="date-range">
					<div class="panel-body">
					    <div class="select-categories">
						<div class="cs-datepicker">
						    <span class="datepicker-text">From</span>
						    <label id="Deadline" class="cs-calendar-from">
							<?php
							$foodbakery_opt_array = array(
							    'name' => esc_html__('Cuisine categories', 'foodbakery'),
							    'desc' => '',
							    'hint_text' => '',
							    'std' => '',
							    'echo' => true,
							    'id' => 'search_date_from',
							    'cust_name' => 'search_date_from',
							    'extra_atr' => 'placeholder="________"',
							    'field_params' => array(
								'return' => true,
							    ),
							);

							$foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
						    </label>
						</div>
						<div class="cs-datepicker">
						    <span class="datepicker-text">To</span>
						    <label id="Deadline" class="cs-calendar-to">
							<?php
							$foodbakery_opt_array = array(
							    'name' => esc_html__('Cuisine categories', 'foodbakery'),
							    'desc' => '',
							    'hint_text' => '',
							    'echo' => true,
							    'std' => '',
							    'id' => 'search_date_to',
							    'cust_name' => 'search_date_to',
							    'extra_atr' => 'placeholder="________"',
							);

							$foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
							?>
						    </label>
						</div>
						<div class="datepicker-text-bottom"><i class="icon-time"></i><span>From Date</span></div>
						<div class="datepicker-text-bottom"><i class="icon-time"></i><span>Select a Date</span></div>
					    </div>
					</div>
				    </div>
				</div>
				<?php
			    }
			    if ($search_min_lodaing != "") {
				?>
				<div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="minimum-loading">
					<a class="collapsed" role="button" data-toggle="collapse" href="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
					    Minimum loading
					</a>
				    </div>
				    <div id="collapsefour" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="minimum-loading">
					<div class="panel-body">
					    <div class="select-categories">

						<ul class="minimum-loading-list">
						    <li>
							<div class="spinner-btn input-group spinner">
							    <span><i class="icon-people2"></i></span>
							    <input type="text" class="form-control" value="42">
							    <span class="list-text">Guests</span>
							    <div class="input-group-btn-vertical">
								<button class=" caret-btn btn btn-default " type="button"><i class="icon-minus3"></i></button>
								<button class=" caret-btn btn btn-default" type="button"><i class=" icon-plus4"></i></button>
							    </div>
							</div>
						    </li>
						    <li>
							<div class="spinner-btn input-group spinner2">
							    <span><i class="icon-bed2"></i></span>
							    <input type="text" class="form-control" value="42">
							    <span class="list-text">Bedrooms</span>
							    <div class="input-group-btn-vertical">
								<button class=" caret-btn btn btn-default " type="button"><i class="icon-minus3"></i></button>
								<button class=" caret-btn btn btn-default" type="button"><i class=" icon-plus4"></i></button>
							    </div>
							</div>
						    </li>
						    <li>
							<div class="spinner-btn input-group spinner3">
							    <span><i class="icon-bathroom"></i></span>
							    <input type="text" class="form-control" value="42">
							    <span class="list-text">Bathroom</span>
							    <div class="input-group-btn-vertical">
								<button class=" caret-btn btn btn-default " type="button"><i class="icon-minus3"></i></button>
								<button class=" caret-btn btn btn-default" type="button"><i class=" icon-plus4"></i></button>
							    </div>
							</div>
						    </li>
						</ul>

					    </div>
					</div>
				    </div>
				</div>
				<?php
			    }
			    if ($search_price_per_person != "") {
				?>
				<div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="price-person">
					<a class="collapsed" role="button" data-toggle="collapse" href="#collapsefive" aria-expanded="false" aria-controls="collapsefive">
					    Price per person
		                        </a>
				    </div>
				    <div id="collapsefive" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="price-person">
					<div class="panel-body">
					    <div class="select-categories">
						<div class="price-per-person">

						    <?php
						    $foodbakery_opt_array = array(
							'name' => esc_html__('Price Per Person', 'foodbakery'),
							'desc' => '',
							'hint_text' => '',
							'echo' => true,
							'std' => '',
							'id' => 'search_price_per_person',
							"rang" => true,
							"min" => 0,
							"max" => 500,
							"both_rang" => true,
						    );

						    $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
						    ?>

						    <span class="rang-text">$0 &nbsp; - &nbsp; $500</span>
						</div>
					    </div>
					</div>
				    </div>
				</div>
				<?php
			    }
			    if ($search_point_of_interest != "") {
				?>
				<div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="mark-filter">
					<a class="collapsed" role="button" data-toggle="collapse" href="#collapsesix" aria-expanded="false" aria-controls="collapsesix">
					    Mark
		                        </a>
				    </div>
				    <div id="collapsesix" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="mark-filter">
					<div class="panel-body">
					    <div class="select-categories">
						<div class="price-mark">

						    <span class="rang-text">Rate from 0 to 10/10</span>
						    <?php
						    $foodbakery_opt_array = array(
							'name' => esc_html__('Price Per Person', 'foodbakery'),
							'desc' => '',
							'hint_text' => '',
							'echo' => true,
							'std' => '',
							'id' => 'search_mark',
							'cust_name' => 'search_mark',
							"rang" => true,
							"min" => 0,
							"max" => 10,
						    );

						    $foodbakery_form_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
						    ?>
						</div>
					    </div>
					</div>
				    </div>
				</div>
		<?php
	    }
	    if ($search_loyalty != "") {
		?>
				<div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="point-interest">
					<a class="collapsed" role="button" data-toggle="collapse" href="#collapseseven" aria-expanded="false" aria-controls="collapseseven">
					    Point of interest
		                        </a>
				    </div>
				    <div id="collapseseven" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="point-interest">
					<div class="panel-body">
					    <div class="select-categories">

						<ul class="cs-checkbox-list">

						    <li style="display: list-item;">
							<div class="checkbox">
							    <input id="checkbox14" type="checkbox" value="Speed">
							    <label for="checkbox14">Lyon 6th</label>
							    <span>(32)</span>
							</div>
						    </li>
						</ul>
						<a href="javascript:void(0)" class="btn-view" id="loadMore" style="display: none;">Show more<i class="icon-angle-double-right"></i></a> 
						<a href="javascript:void(0)" class="btn-less" id="showLess" style="display: inline-block;">Show less<i class="icon-angle-double-left"></i></a>

					    </div>
					</div>
				    </div>
				</div>

		<?php
	    }
	    ?>
	    	    </div>
	    	</div>
	        </div>
	    </aside>
	    <?php
	}

    }

    global $Search_Box;
    $Search_Box = new Search_Box();
}



			