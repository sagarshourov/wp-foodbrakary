<?php
/**
 * Jobs Restaurant search box
 *
 */
?>
<!--Element Section Start-->
<!--Foodbakery Element Start-->
<?php
global $foodbakery_post_restaurant_types;
$rand_numb = isset( $atts['rand_numb'] ) ? $atts['rand_numb'] : '0';
$foodbakery_search_result_page = isset( $atts['foodbakery_search_result_page'] ) ? $atts['foodbakery_search_result_page'] : '0';
?>
<div class="main-search fancy modern">
    <form name="foodbakery-restaurant-form" id="frm_restaurant_arg<?php echo intval( $rand_numb ); ?>" action="<?php echo esc_html( $foodbakery_search_result_page ); ?>" >
        <?php
        $listing_type = isset( $_GET['listing_type'] ) ? $_GET['listing_type'] : '';
        $search_title = isset( $_GET['search_title'] ) ? $_GET['search_title'] : '';
        $search_location = isset( $_GET['location'] ) ? $_GET['location'] : '';
        ?>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-5 col-xs-12">
                <div class="field-holder">
					<i class="icon-search"></i>
                    <input type="text" placeholder="<?php echo esc_html__('Resturant name', 'foodbakery'); ?>" name="search_title" value="<?php echo esc_html( $search_title ) ?>">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
                <div class="field-holder">
                    <ul>
                        <?php
                        foodbakery_get_custom_locations_listing_filter( '', '', false, $rand_numb, 'filter', '', '' );
                        ?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="field-holder">
                    <input class="bgcolor" type="submit" value="<?php _e( 'Search', 'foodbakery' ) ?>">
                </div>
            </div>
        </div>

    </form>
</div>
<!--Foodbakery Element End-->