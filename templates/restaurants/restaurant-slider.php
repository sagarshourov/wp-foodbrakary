<?php
/**
 * Jobs Restaurant search box
 *
 */
?>
<!--Element Section Start-->
<!--Foodbakery Element Start-->
<?php
wp_enqueue_style('swiper');
wp_enqueue_script('swiper');
global $foodbakery_post_restaurant_types;
$restaurant_location_options = isset($atts['restaurant_location']) ? $atts['restaurant_location'] : '';
$restaurants_title = isset($atts['restaurants_title']) ? $atts['restaurants_title'] : '';
$restaurants_subtitle = isset($atts['restaurants_subtitle']) ? $atts['restaurants_subtitle'] : '';
$restaurant_slider_style = isset($atts['restaurant_slider_style']) ? $atts['restaurant_slider_style'] : '';
$foodbakery_var_rest_slider_align = isset($atts['foodbakery_var_rest_slider_align']) ? $atts['foodbakery_var_rest_slider_align'] : '';
if ($restaurant_location_options != '') {
    $restaurant_location_options = explode(',', $restaurant_location_options);
}
$restaurants_top = '';
if ($restaurants_title == '') {
    $restaurants_top = 'swiper-padding-top';
}

if ($restaurant_loop_obj->have_posts()) {
    $flag = 1;
    ?>
    <?php
    if ($restaurants_title != '' || $restaurants_subtitle != '') {
	?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	    <div class="section-title align-left">
		<?php if ($restaurants_title != '') { ?>
	    	<h3><?php echo esc_html($restaurants_title); ?></h3>
		    <?php
		}
		if ($restaurants_subtitle != '') {
		    ?>
	    	<p><?php echo esc_html($restaurants_subtitle); ?></p>
		<?php } ?>
	    </div>
	</div> 
	<?php
    }
    ?>
<?php if(isset($restaurant_slider_style) && $restaurant_slider_style != 'fancy') { ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<?php } ?>
        <div class="company-holder <?php echo $restaurant_slider_style; ?>">
            
            
           <?php if(isset($restaurant_slider_style) && $restaurant_slider_style != 'simple') { ?> 
            
    	<div class="swiper-container">
    	    <div class="swiper-wrapper">
           <?php  } ?>
		    <?php
		    while ($restaurant_loop_obj->have_posts()) : $restaurant_loop_obj->the_post();
			global $post;
			$restaurant_id = $post;
			$foodbakery_transaction_restaurant_reviews = get_post_meta($restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);
			$foodbakery_restaurant_type = get_post_meta($restaurant_id, 'foodbakery_restaurant_type', true);
			$foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
			if ($restaurant_type_post = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type'))
			    $restaurant_type_id = $restaurant_type_post->ID;
			$restaurant_type_id = isset($restaurant_type_id) ? $restaurant_type_id : '';
			$foodbakery_user_reviews = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);
			?>
                  <?php if(isset($restaurant_slider_style) && $restaurant_slider_style != 'simple') { ?> 
			<div class="swiper-slide ">
                            
                  <?php  } ?>
			    <div class="company-logo">
				<figure>
				    <a href="<?php the_permalink(); ?>">
					<?php
					if (has_post_thumbnail()) {
					    $img_atr = array('class' => 'img-grid');
					    the_post_thumbnail('foodbakery_media_4', $img_atr);
					} else {
					    $no_image_url = esc_url(wp_foodbakery::plugin_url() . 'assets/frontend/images/no-image9x6.jpg');
					    $no_image = '<img src="' . $no_image_url . '" alt="" />';
					    echo force_balance_tags($no_image);
					}
					?>
				    </a>
				</figure>
			   

			    <?php
                            $ratings_data = array();
			    if ((isset($restaurant_slider_style) && $restaurant_slider_style == 'fancy') || $restaurant_slider_style == 'simple') {
				$ratings_data = array(
				    'overall_rating' => 0.0,
				    'count' => 0,
				);
				$ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $restaurant_id);
				?>
                             <?php } ?>
                              <?php if(isset($restaurant_slider_style) && $restaurant_slider_style != 'simple') { ?> 
			</div>
                              <?php } ?>
	    		    <div class="text-holder">
	    			<div class="post-title">
	    			    <h6><a href="<?php echo esc_url(get_permalink($restaurant_id)); ?>"><?php echo wp_trim_words(get_the_title($restaurant_id), 10, '...'); ?></a></h6>
	    			</div>
				    <?php if ($foodbakery_transaction_restaurant_reviews == 'on' && $foodbakery_user_reviews == 'on' && isset($ratings_data['count']) && $ratings_data['count'] > 0) { ?>
					<div class="company-rating">
					    <div class="rating-star">
						<span class="rating-box" style="width: <?php echo intval($ratings_data['overall_rating']); ?>%;"></span>
					    </div>
					    <span class="reviews">(<?php echo esc_html($ratings_data['count']); ?>)</span>
<!--                                            //<span class="like"><i class="icon-heart-outlined"></i></span>-->
					     <div class="list-option">
						<?php
						$shortlist_label = '';
						$shortlisted_label = '';
						$figcaption_div = true;
						$book_mark_args = array(
						    'before_label' => $shortlist_label,
						    'after_label' => $shortlisted_label,
						    'before_icon' => '<i class="icon-heart-o"></i>',
						    'after_icon' => '<i class="icon-heart4"></i>',
						);
						do_action('foodbakery_shortlists_frontend_button', $restaurant_id, $book_mark_args, $figcaption_div);
						?>
					    </div>
					</div>
				    <?php } else {
					?>
					<div class="company-rating">
					    <?php
					    echo esc_html_e('Reviews Coming Soon', 'foodbakery');
					    ?>
					    <div class="list-option">
						<?php
						$shortlist_label = '';
						$shortlisted_label = '';
						$figcaption_div = true;
						$book_mark_args = array(
						    'before_label' => $shortlist_label,
						    'after_label' => $shortlisted_label,
						    'before_icon' => '<i class="icon-heart-o"></i>',
						    'after_icon' => '<i class="icon-heart4"></i>',
						);
						do_action('foodbakery_shortlists_frontend_button', $restaurant_id, $book_mark_args, $figcaption_div);
						?>
					    </div>
					</div>
				    <?php }
				    ?>
	    		    </div>
			   
                             </div>
		
    <?php endwhile; ?>
                
                
                  <?php if(isset($restaurant_slider_style) && $restaurant_slider_style != 'simple') { ?> 
                
    	    </div>
    	</div>
                  <?php } ?>
    	<!-- Add Arrows -->
	    <?php if (isset($restaurant_slider_style) && $restaurant_slider_style == 'fancy') {
		?>
		<div class="fancy-button-prev"> <i class="icon-arrow_back"></i></div>
		<div class="fancy-button-next"><i class="icon-arrow_forward"></i></div>
	    <?php } else { ?>
<!--		<div class="swiper-button-prev"> <i class="icon-chevron-thin-left"></i></div>
		<div class="swiper-button-next"><i class="icon-chevron-thin-right"></i></div>-->

	    <?php } ?>
        </div>
	<?php if(isset($restaurant_slider_style) && $restaurant_slider_style != 'fancy') { ?>
    </div>
	<?php  } ?>
    <?php
} else {
    echo '<div class="no-restaurant-match-error"><h6><i class="icon-warning"></i><strong> ' . esc_html__('Sorry !', 'foodbakery') . '</strong>&nbsp; ' . esc_html__("There are no restaurants matching your search.", 'foodbakery') . ' </h6></div>';
}
?>
<!--Foodbakery Element End-->