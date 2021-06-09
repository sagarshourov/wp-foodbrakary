<?php
/*
 * claim restaurant class
 */
if (!class_exists('Foodbakery_Claim_Restaurant')) {

    Class Foodbakery_Claim_Restaurant {
	/*
	 * Constructor
	 */

	public function __construct() {
	    add_action('claim_restaurant_from', array($this, 'claim_restaurant_from_callback'));
	}

	/*
	 * claim restaurant form
	 */

	public function claim_restaurant_from_callback() {
	    global $wpdb, $foodbakery_plugin_options, $foodbakery_html_fields_frontend, $foodbakery_form_fields_frontend;
	    echo '<a class="claim-list" href="#" data-toggle="modal" data-target="#user-claim-restaurant"><i class="icon-edit3"></i>Claim Restaurant</a>';
	    ?>
	    <div class="modal fade" id="user-claim-restaurant" tabindex="-1" role="dialog">
	        <div class="modal-dialog" role="document">
	    	<div class="login-form">
	    	    <div class="modal-content">
	    		<div class="modal-header">
	    		    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
	    		    <h3 class="modal-title" id="myModalLabel"><?php echo esc_html__('Claim Restaurant', 'foodbakery'); ?></h3>
	    		</div>
	    		<div class="modal-body">
	    		    <form id="foodbakery_claim_restaurant">
				    <?php
				    $output = '';

				    $foodbakery_opt_array = array(
					'name' => esc_html__('Name', 'foodbakery'),
					'id' => '',
					'field_params' => array(
					    'description' => '',
					    'id' => 'claim_restaurant_user_name',
					    'name' => 'claim_restaurant_user_name',
					    'classes' => 'form-control',
					    'std' => '',
					    'extra_atr' => 'placeholder="'.esc_html__('Name', 'foodbakery').'"',
					),
				    );
				    $output .= $foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
				    $foodbakery_opt_array = array(
					'name' => esc_html__('Email', 'foodbakery'),
					'id' => '',
					'field_params' => array(
					    'description' => '',
					    'id' => 'claim_restaurant_user_email',
					    'name' => 'claim_restaurant_user_email',
					    'classes' => 'form-control',
					    'std' => '',
					    'extra_atr' => 'placeholder="'.esc_html__('Email', 'foodbakery').'"',
					),
				    );
				    $output .= $foodbakery_html_fields_frontend->foodbakery_form_text_render($foodbakery_opt_array);
				    $foodbakery_opt_array = array(
					'name' => esc_html__('Reason', 'foodbakery'),
					'id' => '',
					'description' => '',
					'field_params' => array(
					    'std' => '',
					    'description' => '',
					    'id' => 'claim_restaurant_reason',
					    'name' => 'Reason',
					    'classes' => '',
					),
				    );
				    $output .= $foodbakery_html_fields_frontend->foodbakery_form_textarea_render($foodbakery_opt_array);
				    echo force_balance_tags($output);
				    ?> 
	    			<div class="input-filed">
	    			    <button type="button"  value="" id="foodbakery_claim_restaurant"><?php echo esc_html__('Claim Restaurant', 'foodbakery'); ?></button>
	    			</div>
	    		    </form>

	    		</div>
	    	    </div>
	    	</div>
	        </div>
	    </div>
	    <?php
	}

    }

    global $Foodbakery_Claim_Restaurant;
    $Foodbakery_Claim_Restaurant = new Foodbakery_Claim_Restaurant();
}