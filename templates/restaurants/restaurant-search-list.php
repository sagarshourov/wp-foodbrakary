<?php
/**
 * Jobs Restaurant search box
 *
 */
?>
<!--Element Section Start-->
<!--Foodbakery Element Start-->
<?php
global $foodbakery_post_restaurant_types, $foodbakery_var_options;
$rand_numb = isset($atts['rand_numb']) ? $atts['rand_numb'] : '0';
$foodbakery_search_result_page = isset($atts['foodbakery_search_result_page']) ? $atts['foodbakery_search_result_page'] : '0';
$restaurant_search_title = isset($atts['restaurant_search_title']) ? $atts['restaurant_search_title'] : '0';
$restaurant_search_subtitle = isset($atts['restaurant_search_subtitle']) ? $atts['restaurant_search_subtitle'] : '0';
?>
<div class="row"> 
    <!-- Column Start -->
    <div class="listing-main-search">
        <div class="text-holder">
	    <?php
	    $args = array(
		'posts_per_page' => "-1",
		'post_type' => 'restaurants',
		'post_status' => 'publish',
	    );
	    $my_query = new WP_Query($args);
	    wp_reset_postdata();

	    $restaurants_count = count($my_query->posts);
	    ?>
            <strong><?php echo esc_html($restaurant_search_title); ?></strong>
	    <span><?php echo esc_html($restaurant_search_subtitle); ?></span>
        </div>
	<?php
	$restaurant_search_view = isset($atts['restaurant_search_view']) ? $atts['restaurant_search_view'] : '';
	if (isset($restaurant_search_view) && $restaurant_search_view == 'classic') {
	    ?>
    	<div class="main-search classic">
		<?php } else {
		?>

    	    <div class="main-search">
		<?php }

		$listing_type = isset($_GET['listing_type']) ? $_GET['listing_type'] : '';
		$search_title = isset($_GET['search_title']) ? $_GET['search_title'] : '';
		$search_location = isset($_GET['location']) ? $_GET['location'] : '';
		?>
		<form id="frm_restaurant_arg<?php echo intval($rand_numb); ?>" action="<?php echo esc_html($foodbakery_search_result_page); ?>">
		    <div class="restaurant-search-element-container row">
			<?php if (isset($restaurant_search_view) && $restaurant_search_view == 'classic') { ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<?php } else { ?>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<?php } ?>
			    <div class="field-holder"> <span class="restaurant-element-search-btn"> <i class="icon-search5"></i> </span>
				
				<?php if (isset($restaurant_search_view) && $restaurant_search_view == 'classic') { ?>
				<?php
				    foodbakery_get_custom_locations_listing_filter('', '', false, $rand_numb, 'header', '', '',$restaurant_search_view);
				    ?>
				 <input value="<?php echo esc_html__('Search', 'foodbakery'); ?>" type="submit">
				<?php } else{?>
				 <input placeholder="Resturant name" name="search_title" value="" type="text">
				<?php } ?>
			    </div>
			</div>
			<?php
			if (isset($restaurant_search_view) && $restaurant_search_view != 'classic') {
			?>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			    <div class="field-holder"> 
                                <?php if($restaurant_search_view != 'list')  {?>
                                <span> <i class="icon-location search-by-location-icon"></i> </span>
                                <?php } ?>
				<ul>
				    <?php
				    foodbakery_get_custom_locations_listing_filter('', '', false, $rand_numb, 'header', '', '');
				    ?>
				</ul>
			    </div>
			</div>
			<?php } ?>
		    </div>
		    <script type="text/javascript">
			jQuery(document).ready(function ($) {
			    $(document).on('restaurant-item-selected', function () {
				$(".restaurant-search-element-container #foodbakery-locations-field").parents("form").submit();
			    });

			    $(".restaurant-search-element-container .restaurant-element-search-btn, .restaurant-search-element-container .search-by-location-icon").click(function () {
				$(this).parents("form").submit();
			    });

			    $('.restaurant-search-element-container input[type="text"]').keypress(function (e) {
				if (e.which == 13) {
				    $(this).parents("form").submit();
				    return false;
				}
			    });
			});
		    </script>
		</form>
	    </div>
	</div>
	<!-- Column End --> 
    </div>
    <!--Foodbakery Element End-->