<?php
/*
 * restaurant taxonomy mata
 */
if (!class_exists('Restaurant_taxonomy_Meta')) {

    Class Restaurant_taxonomy_Meta {

        public function __construct() {
            add_action('restaurant-category_add_form_fields', array($this, 'icon_taxonomy_add_new_meta_field'), 10, 2);
            add_action('restaurant-category_edit_form_fields', array($this, 'icon_taxonomy_edit_meta_field'), 10, 2);
            add_action('edited_restaurant-category', array($this, 'save_taxonomy_custom_meta'), 10, 2);
            add_action('create_restaurant-category', array($this, 'save_taxonomy_custom_meta'), 10, 2);
        }

        function icon_taxonomy_add_new_meta_field($term) {
            // this will add the custom meta field to the add new term page

            global $foodbakery_html_fields;

            $type_icon = ( isset($type_icon[0]) ) ? $type_icon[0] : '';
            ?>
            <div class="form-field term-slug-wrap">
                <div class="form-elements">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label><?php echo foodbakery_plugin_text_srt('foodbakery_icon'); ?></label>
                    </div>

                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <?php echo foodbakery_iconlist_plugin_options('', 'restaurant_type_icon', 'foodbakery_restaurant_taxonomy_icon'); ?>
                    </div>
                </div>
                <?php
                 $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_image_icon'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'id' => 'listing_term_image',
		'field_params' => array(
		    'id' => 'listing_term_image',
		    'std' => ( isset($foodbakery_listing_term_image) ) ? $foodbakery_listing_term_image : '',
		    'return' => true,
		),
	    );
	    $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
            ?>
             
            </div>
            <?php
        }

        public function icon_taxonomy_edit_meta_field($term) {
            $t_id = $term->term_id;
            global $foodbakery_html_fields;

            // retrieve the existing value(s) for this meta field. This returns an array
            $term_meta = get_term_meta($t_id, 'foodbakery_restaurant_taxonomy_icon', true);
          $term_meta_image = get_term_meta($t_id, 'foodbakery_listing_term_image', true);
          $term_meta_image_large = get_term_meta($t_id, 'foodbakery_listing_term_image_large', true);
        
            
            ?>


            <div class="form-field term-slug-wrap">
                <div class="form-elements">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label><?php echo foodbakery_plugin_text_srt('foodbakery_icon'); ?></label>
                    </div>

                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <?php echo foodbakery_iconlist_plugin_options($term_meta, 'restaurant_type_icon', 'foodbakery_restaurant_taxonomy_icon'); ?>
                    </div>
                </div>
                 <?php
                 $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_image_icon'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'id' => 'listing_term_image',
                                    'std' => ( isset($term_meta_image) ) ? $term_meta_image : '',
		'field_params' => array(
		    'id' => 'listing_term_image',
		    'std' => ( isset($term_meta_image) ) ? $term_meta_image : '',
		    'return' => true,
		),
	    );
	    $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
            ?>
                
            </div>
            <?php
        }

        public function save_taxonomy_custom_meta($term_id) {
            if (isset($_POST['foodbakery_restaurant_taxonomy_icon'])) {

                $icon = $_POST['foodbakery_restaurant_taxonomy_icon'][0];

                $t_id = $term_id;

                // Save the option array.
              
                update_term_meta($t_id, 'foodbakery_restaurant_taxonomy_icon', $icon);
            }
             if (isset($_POST['foodbakery_listing_term_image'])) {

                $image = $_POST['foodbakery_listing_term_image'];
              
                $t_id = $term_id;

                // Save the option array.
             
                update_term_meta($t_id, 'foodbakery_listing_term_image', $image);
            }
              
        }

    }

    $Restaurant_taxonomy_Meta = new Restaurant_taxonomy_Meta();
}