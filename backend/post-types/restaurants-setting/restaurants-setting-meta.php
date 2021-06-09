<?php
/**
 * @Add Meta Box For Restaurant Types
 * @return
 *
 */
if (!class_exists('Foodbakery_Restaurant_Type_Meta')) {

    class Foodbakery_Restaurant_Type_Meta {

	public function __construct() {
	    add_action('wp_ajax_add_feature_to_list', array($this, 'add_feature_to_list'));
	    add_action('wp_ajax_add_category_to_list', array($this, 'add_category_to_list'));
	    add_action('add_meta_boxes', array($this, 'foodbakery_meta_restaurant_type_add'));
        add_action('save_post', array($this, 'foodbakery_save_post_categories'), 12);
        add_action('save_post', array($this, 'foodbakery_restaurant_save_nutri_icons'), 13);
	    add_action('wp_ajax_foodbakery_ft_iconpicker', array($this, 'foodbakery_ft_icon'));
	    add_action('wp_ajax_foodbakery_get_tags_list', array($this, 'foodbakery_get_tags_list'));
	    add_action('wp_ajax_foodbakery_get_cats_list', array($this, 'foodbakery_get_cats_list'));
	    add_action('wp_ajax_restaurant_add_nutri_icon_item', array($this, 'foodbakery_restaurant_nutri_icon_item'),10,2);
	    add_action('wp_ajax_nopriv_restaurant_add_nutri_icon_item', array($this, 'foodbakery_restaurant_nutri_icon_item'),10,2);
	    add_filter("get_user_option_screen_layout_restaurant-type", array($this, 'restaurant_type_screen_layout'));
	}

	public function restaurant_type_screen_layout($selected) {
	    return 1; // Use 1 column if user hasn't selected anything in Screen Options
	}

	function foodbakery_meta_restaurant_type_add() {
	    add_meta_box('foodbakery_meta_restaurant_type', esc_html(foodbakery_plugin_text_srt('foodbakery_restaurant_type_options')), array($this, 'foodbakery_meta_restaurant_type'), 'restaurant-type', 'normal', 'high');
	}

	function foodbakery_meta_restaurant_type($post) {
	    global $post, $foodbakery_html_fields, $foodbakery_post_restaurant_types, $foodbakery_plugin_static_text;
	    ?>		
	    <div class="page-wrap page-opts left" style="overflow:hidden; position:relative;">
	        <div class="option-sec" style="margin-bottom:0;">
	    	<div class="opt-conts">
	    	    <div class="elementhidden">
	    		<nav class="admin-navigtion">
	    		    <ul id="cs-options-tab">
	    			<li><a href="javascript:void(0);" name="#tab-restaurant_settings"><i class="icon-build"></i><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_general_settings'); ?></a></li>
	    			<li><a href="javascript:void(0);" name="#tab-restaurant_types-settings-makes"><i class="icon-layers3"></i><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_categories'); ?></a></li>
	    			<li><a href="javascript:void(0);" name="#tab-restaurant_types-settings-custom-fields"><i class="icon-support"></i><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_custom_fields'); ?></a></li>
	    			<li><a href="javascript:void(0);" name="#tab-restaurant_types-form-builder"><i class="icon-dns"></i><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_form_builders'); ?></a></li>

	    			<li><a href="javascript:void(0);" name="#tab-restaurant_types-settings-page-elements"><i class="icon-cogs"></i><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_required_elements'); ?></a></li>
	    			<li><a href="javascript:void(0);" name="#tab-restaurant_types-settings-suggested-tags"><i class="icon-tags"></i><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_suggested_tags'); ?></a></li>
	    			<li><a href="javascript:void(0);" name="#tab-restaurant_types-settings-nutri-icons"><i class="icon-image"></i><?php _e('Nutritional Information icons', 'foodbakery') ?></a></li>
				    <?php do_action('restaurant_type_options_sidebar_tab'); ?>
	    		    </ul>
	    		</nav>
	    		<div id="tabbed-content" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>">
	    		    <div id="tab-restaurant_settings" class="foodbakery_tab_block" data-title="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_general_settings'); ?>">
				    <?php $this->restaurant_type_settings_tab(); ?>
	    		    </div>
	    		    <div id="tab-restaurant_types-settings-makes">
				    <?php $this->foodbakery_post_restaurant_type_categories(); ?>
	    		    </div>
	    		    <div id="tab-restaurant_types-settings-custom-fields" class="foodbakery_tab_block" data-title="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_custom_fields'); ?>">
				    <?php $this->foodbakery_post_restaurant_type_fields(); ?>
	    		    </div>
	    		    <div id="tab-restaurant_types-form-builder" class="foodbakery_tab_block" data-title="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_form_builders'); ?>">
				    <?php $this->foodbakery_post_restaurant_type_fields_form_builder(); ?>
	    		    </div>

	    		    <div id="tab-restaurant_types-settings-features" class="foodbakery_tab_block" data-title="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_features'); ?>">
				    <?php $this->foodbakery_post_restaurant_type_features(); ?>
	    		    </div>

	    		    <div id="tab-restaurant_types-settings-page-elements" class="foodbakery_tab_block" data-title="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_required_elements'); ?>">
				    <?php $this->foodbakery_post_page_elements_setting(); ?>
	    		    </div>
	    		    <div id="tab-restaurant_types-settings-suggested-tags" class="foodbakery_tab_block" data-title="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_suggested_tags'); ?>">
				    <?php $this->foodbakery_post_restaurant_type_tags(); ?>
	    		    </div>
	    		    <div id="tab-restaurant_types-settings-nutri-icons" class="foodbakery_tab_block" data-title="<?php _e('Nutritional Information icons', 'foodbakery') ?>">
				    <?php $this->restaurant_nutri_icons(); ?>
	    		    </div>
				<?php do_action('restaurant_type_options_tab_container'); ?>
	    		</div> 
			    <?php $foodbakery_post_restaurant_types->foodbakery_submit_meta_box('restaurant-type', $args = array()); ?>
	    	    </div>
	    	</div>
	        </div>
	    </div>
	    <div class="clear"></div>
	    <?php
	}

	public function foodbakery_restaurant_save_nutri_icons($restaurant_id = '') {

	    $restaurant_nutri_icon_titles = foodbakery_get_input('nutri_icon_title', '', 'ARRAY');
	    $restaurant_nutri_icon_imgs = foodbakery_get_input('nutri_icon_img', '', 'ARRAY');

	    if (!empty($restaurant_nutri_icon_imgs)) {
		update_post_meta($restaurant_id, 'nutri_icon_imgs', $restaurant_nutri_icon_imgs);
	    }
	    if (!empty($restaurant_nutri_icon_titles)) {
		update_post_meta($restaurant_id, 'nutri_icon_titles', $restaurant_nutri_icon_titles);
	    }
	}

	function get_attached_cats($type = '', $meta_key = '') {
	    global $post;

	    $foodbakery_category_array = array();
	    $args = array(
		'posts_per_page' => "-1",
		'post_type' => "$type",
		'post_status' => array('publish', 'pending', 'draft'),
		'post__not_in' => array($post->ID)
	    );

	    $custom_query = new WP_Query($args);
	    if ($custom_query->have_posts() <> "") {

		while ($custom_query->have_posts()): $custom_query->the_post();
		    $foodbakery_aut_categories = get_post_meta(get_the_ID(), "$meta_key", true);
		    if (is_array($foodbakery_aut_categories)) {
			$foodbakery_category_array = array_merge($foodbakery_category_array, $foodbakery_aut_categories);
		    }
		endwhile;
	    }
	    wp_reset_postdata();

	    return is_array($foodbakery_category_array) ? array_unique($foodbakery_category_array) : $foodbakery_category_array;
	}

	/**
	 * @Inventory Type Custom Fileds Function
	 * @return
	 */
	function foodbakery_post_restaurant_type_fields() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_fields;

	    $foodbakery_restaurant_type_fields->custom_fields();
	}

	function foodbakery_post_restaurant_type_fields_form_builder() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_restaurant_type_form_builder_fields;

	    $foodbakery_html_fields->foodbakery_heading_render(array('name' => esc_html__('Form Builder Fields', 'foodbakery')));
	    /// Form Fields
	    $foodbakery_restaurant_type_form_builder_fields->custom_fields();

	    /// Form Button Label
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_form_button_label'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => 'Submit',
		    'id' => 'form_button_label',
		    'return' => true,
		),
	    );
	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	}

	/**
	 * @Inventory Type Features Function
	 * @return
	 */
	function foodbakery_post_restaurant_type_features() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields;

	    $this->foodbakery_features_items($post);
	}

	function foodbakery_post_restaurant_type_tags() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields;

	    $this->foodbakery_tags_items();
	}

	function foodbakery_post_restaurant_type_categories() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields;

	    $foodbakery_restaurant_type_tags = get_post_meta($post->ID, 'foodbakery_restaurant_type_cats', true);

	    $tag_obj_array = $foodbakery_tags_array = array();
	    if (is_array($foodbakery_restaurant_type_tags) && sizeof($foodbakery_restaurant_type_tags) > 0) {
		foreach ($foodbakery_restaurant_type_tags as $tag_r) {
		    $tag_obj = get_term_by('slug', $tag_r, 'restaurant-category');
		    if (is_object($tag_obj)) {
			$tag_obj_array[$tag_obj->slug] = $tag_obj->name;
		    }
		}
	    } else {
		$foodbakery_tags_array[''] = esc_html__('Select Cuisine', 'foodbakery');
	    }
	    $restaurant_cats = get_terms('restaurant-category', array(
		'hide_empty' => false,
		'parent' => 0,
	    ));
	    if (is_array($restaurant_cats) && !empty($restaurant_cats)) {
		foreach ($restaurant_cats as $restaurant_cat) {
		    $foodbakery_tags_array[$restaurant_cat->slug] = $restaurant_cat->name;
		}
	    }

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_select_cats'),
		'desc' => '',
		'hint_text' => foodbakery_plugin_text_srt('foodbakery_select_cats_hint'),
		'echo' => true,
		'multi' => true,
		'desc' => sprintf(foodbakery_plugin_text_srt('foodbakery_add_new_cats_link'), admin_url('edit-tags.php?taxonomy=restaurant-category&post_type=restaurants', foodbakery_server_protocol())),
		'field_params' => array(
		    'std' => $tag_obj_array,
		    'id' => 'restaurant_type_cats',
		    'classes' => 'chosen-select-no-single chosen-select',
		    'options' => $foodbakery_tags_array,
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
	}

	public function foodbakery_get_cats_list() {
	    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
	    $foodbakery_tags_array = get_terms('restaurant-category', array(
		'hide_empty' => false,
		'parent' => 0,
	    ));
	    $foodbakery_restaurant_type_tags = get_post_meta($post->ID, 'foodbakery_restaurant_type_cats', true);

	    $foodbakery_tags_list = array();
	    if (is_array($foodbakery_tags_array) && sizeof($foodbakery_tags_array) > 0) {
		foreach ($foodbakery_tags_array as $dir_tag) {
		    if (in_array($dir_tag->slug, $foodbakery_restaurant_type_tags)) {

			$foodbakery_tags_list[] = array('value' => 'asif', 'caption' => 'david');
		    }
		}
	    }

	    echo json_encode($foodbakery_tags_list);
	    die;
	}

	public function features_save($post_id) {
	    if (isset($_POST['foodbakery_features_array']) && is_array($_POST['foodbakery_features_array'])) {
		$feat_array = array();
		$feat_counter = 0;
		foreach ($_POST['foodbakery_features_array'] as $feat) {
		    $feat_name = isset($_POST['foodbakery_feature_name_array'][$feat_counter]) ? $_POST['foodbakery_feature_name_array'][$feat_counter] : '';
		    $feat_array[$feat] = array('key' => 'feature_' . $feat, 'name' => $feat_name, 'icon' => $_POST['foodbakery_feature_icon_array'][$feat_counter]);
		    $feat_counter ++;
		}
		update_post_meta($post_id, 'foodbakery_restaurant_type_features', $feat_array);
	    }
	}

	public function tags_save($post_id) {
	    if (isset($_POST['foodbakery_restaurant_type_tags'])) {
		update_post_meta($post_id, 'foodbakery_restaurant_type_tags', $_POST['foodbakery_restaurant_type_tags']);
	    } else {
		update_post_meta($post_id, 'foodbakery_restaurant_type_tags', '');
	    }
	}

	public function categories_save($post_id) {
	    if (isset($_POST['foodbakery_restaurant_type_cats'])) {
		update_post_meta($post_id, 'foodbakery_restaurant_type_cats', $_POST['foodbakery_restaurant_type_cats']);
	    } else {
		update_post_meta($post_id, 'foodbakery_restaurant_type_cats', '');
	    }
	}

	public function foodbakery_features_items($post) {
	    global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;
	    $foodbakery_get_features = get_post_meta($post->ID, 'foodbakery_restaurant_type_features', true);
	    $ratings = array();
	    $post_id = $post->ID;
	    $featured_lables = get_post_meta($post_id, 'feature_lables', true);
	    $foodbakery_feature_icon = get_post_meta($post_id, 'foodbakery_feature_icon', true);
	    $foodbakery_enable_not_selected = get_post_meta($post_id, 'foodbakery_enable_not_selected', true);
	    ?>
	    <div id="tab-features_settings">
		<?php
		$post_meta = get_post_meta(get_the_id());
		$features_data = array();
		if (isset($post_meta['foodbakery_restaurant_type_features']) && isset($post_meta['foodbakery_restaurant_type_features'][0])) {
		    $features_data = json_decode($post_meta['foodbakery_restaurant_type_features'][0], true);
		}
		if (count($featured_lables) > 0) {
		    $foodbakery_opt_array = array(
			'name' => 'Single Page Unchecked show',
			'desc' => '',
			'hint_text' => '',
			'echo' => true,
			'field_params' => array(
			    'std' => $foodbakery_enable_not_selected,
			    'id' => 'enable_not_selected',
			    'return' => true,
			),
		    );
		    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
		}
		?>
	        <div class="form-elements">

		    <?php
		    $icon_rand_id = rand(10000000, 99999999);
		    ?>
	    	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
	    	    <table id="feats_srtable_table" class="features-templates-wrapper">
	    		<thead>
	    		    <tr>
	    			<th style="width: 45px;">&nbsp;</th>
	    			<th style="width: 45px;">&nbsp;</th>
	    			<th>&nbsp;</th>
	    		    </tr>
	    		</thead>

	    		<tbody class="ui-sortable">
				<?php
				$counter = 0;
				if (is_array($featured_lables) && sizeof($featured_lables) > 0) {

				    foreach ($featured_lables as $key => $lable) {
					$icon = isset($foodbakery_feature_icon[$key]) ? $foodbakery_feature_icon[$key] : '';
					?>
		    		    <tr id="repeat_element<?php echo esc_html($icon_rand_id) . esc_html($counter); ?>" class="tr_clone" ><td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td><?php echo foodbakery_iconlist_plugin_options($icon, 'feature_icon' . $icon_rand_id . $counter, 'foodbakery_feature_icon'); ?></td><td><input type="text" value="<?php echo esc_html($lable); ?>" name="feature_label[]" class="review_label" placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_features_label'); ?>"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td>
		    		    </tr>
					<?php
					$counter ++;
				    }
				} else {
				    $icon_rand_id = rand(1000000, 99999999);
				    ?>
				    <tr id="repeat_element<?php echo esc_html($icon_rand_id); ?>" class="tr_clone" >
					<td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td><?php echo foodbakery_iconlist_plugin_options('icon-search', 'feature_icon' . $icon_rand_id, 'foodbakery_feature_icon'); ?></td><td><input type="text" value="Feature # 1" name="feature_label[]" class="review_label"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td>
				    </tr>
				    <?php $icon_rand_id = rand(1000000, 99999999); ?>
				    <tr id="repeat_element<?php echo esc_html($icon_rand_id); ?>" class="tr_clone" >
					<td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td><?php echo foodbakery_iconlist_plugin_options('icon-tablet', 'feature_icon' . $icon_rand_id, 'foodbakery_feature_icon'); ?></td><td><input type="text" value="Feature # 2" name="feature_label[]" class="review_label"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td>
				    </tr>
				    <?php $icon_rand_id = rand(1000000, 99999999); ?>
				    <tr id="repeat_element<?php echo esc_html($icon_rand_id); ?>" class="tr_clone" >
					<td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td><?php echo foodbakery_iconlist_plugin_options('icon-mobile', 'feature_icon' . $icon_rand_id, 'foodbakery_feature_icon'); ?></td><td><input type="text" value="Feature # 3" name="feature_label[]" class="review_label"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td>
				    </tr>
				    <?php
				}
				?>
	    		</tbody>
	    	    </table>
	    	    <a href="javascript:void(0);" id="click-more" class="cntrl-add-new-row" onclick="duplicate()"><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_meta_feature_add_row'); ?></a>
	    	</div>
	        </div>
	    </div>

	    <script type="text/javascript">
	        jQuery(document).ready(function () {
	    	var table_class = ".features-templates-wrapper";

	    	jQuery(table_class + " tbody").sortable({
	    	    //items: "> tr:not(:last)",
	    	    cancel: "input"
	    	});
	        });	// Function for duplicate <tr> for add features.
	        var counter_val = 1;
	        function duplicate() {

	    	counter_val;
	    	$(".features-templates-wrapper tbody").append(
	    		'<tr id="repeat_element49748535' + counter_val + '"><td><span class="cntrl-drag-and-drop"><i class="icon-menu2"></i></span></td><td id="icon-' + counter_val + '<?php echo esc_html($icon_rand_id); ?>"></td><td><input type="text" value="" name="feature_label[]" class="review_label" placeholder="<?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_type_features_label'); ?>"></td><td style="text-align: center;"><a href="#" class="cntrl-delete-rows" title="Delate Row"><i class="icon-cancel2"></i></a></td></tr>'
	    		);
	    	foodbakery_ft_icon_feature(counter_val + '<?php echo esc_html($icon_rand_id); ?>');

	    	counter_val++;

	        }


	        jQuery(document).on('click', '.cntrl-delete-rows', function () {

	    	delete_row_top(this);
	    	return false;
	        });

	        function delete_row_top(delete_link) {
	    	$(delete_link).parent().parent().remove();

	        }
	    </script>
	    <?php
	}

	public function foodbakery_tags_items() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;
	    $foodbakery_restaurant_type_tags = get_post_meta($post->ID, 'foodbakery_restaurant_type_tags', true);
	    $tag_obj_array = array();
	    if (is_array($foodbakery_restaurant_type_tags) && sizeof($foodbakery_restaurant_type_tags) > 0) {
		foreach ($foodbakery_restaurant_type_tags as $tag_r) {
		    $tag_obj = get_term_by('slug', $tag_r, 'restaurant-tag');
		    if (is_object($tag_obj)) {
			$tag_obj_array[$tag_obj->slug] = $tag_obj->name;
		    }
		}
	    }
	    $foodbakery_tags_array = get_terms('restaurant-tag', array(
		'hide_empty' => false,
		'parent' => 0,
	    ));
	    $foodbakery_tags_list = array();
	    if (is_array($foodbakery_tags_array) && sizeof($foodbakery_tags_array) > 0) {
		foreach ($foodbakery_tags_array as $dir_tag) {
		    $foodbakery_tags_list[$dir_tag->slug] = $dir_tag->name;
		}
	    }

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_select_suggested_tags'),
		'desc' => '',
		'hint_text' => foodbakery_plugin_text_srt('foodbakery_select_suggested_tags_hint'),
		'echo' => true,
		'multi' => true,
		'desc' => sprintf(foodbakery_plugin_text_srt('foodbakery_add_new_tag_link'), admin_url('edit-tags.php?taxonomy=restaurant-tag&post_type=restaurants', foodbakery_server_protocol())),
		'field_params' => array(
		    'std' => $tag_obj_array,
		    'id' => 'restaurant_type_tags',
		    'classes' => 'chosen-select-no-single chosen-select',
		    'options' => $foodbakery_tags_list,
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
	}

	public function foodbakery_get_tags_list() {
	    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
	    $foodbakery_tags_array = get_terms('restaurant-tag', array(
		'hide_empty' => false,
	    ));
	    $foodbakery_tags_list = array();
	    if (is_array($foodbakery_tags_array) && sizeof($foodbakery_tags_array) > 0) {
		foreach ($foodbakery_tags_array as $dir_tag) {
		    $foodbakery_tags_list[] = array('value' => $dir_tag->slug, 'caption' => $dir_tag->name);
		}
	    }
	    echo json_encode($foodbakery_tags_list);
	    die;
	}

	public function foodbakery_categories_items() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;
	    $post_meta = get_post_meta(get_the_id());

	    $foodbakery_get_categories = get_the_terms(get_the_id(), 'restaurant-category');


	    $html = '
            <script>
                jQuery(document).ready(function($) {
                    $("#total_categories").sortable({
                        cancel : \'td div.table-form-elem
                    });
                });
            </script>
              <ul class="form-elements">
                  <li class="to-button"><a href="javascript:foodbakery_createpop(\'add_category_title\',\'filter\')" class="button">' . foodbakery_plugin_text_srt('foodbakery_add_category') . '</a> </li>
               </ul>
              <div class="cs-service-list-table">
              <table class="to-table" border="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th style="width:60%;">' . foodbakery_plugin_text_srt('foodbakery_title') . '</th>
                        <th style="width:100%;">' . foodbakery_plugin_text_srt('foodbakery_icon') . '</th>
                        <th style="width:20%;" class="right">' . foodbakery_plugin_text_srt('foodbakery_actions') . '</th>
                      </tr>
                    </thead>
                    <tbody id="total_categories">';
	    if (is_array($foodbakery_get_categories) && sizeof($foodbakery_get_categories) > 0) {

		foreach ($foodbakery_get_categories as $categories) {
		    $category_icon = get_term_meta($categories->term_id, 'foodbakery_restaurant_taxonomy_icon', true);
		    $foodbakery_categories_array = array(
			'counter_category' => $categories->term_id,
			'category_id' => $categories->term_id,
			'foodbakery_category_name' => $categories->name,
			'foodbakery_restaurant_taxonomy_icons' => $category_icon
		    );

		    $html .= $this->add_category_to_list($foodbakery_categories_array);
		    $category_icon = '';
		}
	    }

	    $html .= '
                </tbody>
            </table>

            </div>
            <div id="add_category_title" style="display: none;">
                  <div class="cs-heading-area">
                    <h5><i class="icon-plus-circle"></i> ' . foodbakery_plugin_text_srt('foodbakery_restaurant_categories') . '</h5>
                    <span class="cs-btnclose" onClick="javascript:foodbakery_removeoverlay(\'add_category_title\',\'append\')"> <i class="icon-times"></i></span> 	
                  </div>';



	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_name'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => '',
		    'cust_id' => 'foodbakery_category_name',
		    'cust_name' => 'foodbakery_category_name[]',
		    'return' => true,
		),
	    );

	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	    $terms = get_terms(array(
		'taxonomy' => 'restaurant-category',
		'hide_empty' => false,
	    ));

	    $cats_parents = array();
	    $cats_parents[''] = foodbakery_plugin_text_srt('foodbakery_restaurant_type_no_parent');
	    foreach ($terms as $term) {

		$cats_parents[$term->term_id] = $term->name;
	    }

	    $foodbakery_opt_array = array(
		'name' => esc_html__('Parent', ''),
		'desc' => '',
		'hint_text' => '',
		'field_params' => array(
		    'std' => '',
		    'cust_id' => 'foodbakery_category_parent',
		    'cust_name' => 'foodbakery_category_parent[]',
		    'classes' => 'dropdown chosen-select',
		    'options' => $cats_parents,
		    'return' => true,
		),
	    );


	    $html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

	    $html .= '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<label>' . foodbakery_plugin_text_srt('foodbakery_icon') . '</label></div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
	    $html .= foodbakery_iconlist_plugin_options("", "restaurant_type_icons", "foodbakery_restaurant_taxonomy_icons");

	    $html .= '</div></div>';


	    $html .= '
                <ul class="form-elements noborder">
                  <li class="to-label"></li>
                  <li class="to-field">
                        <input type="button" value="' . foodbakery_plugin_text_srt('foodbakery_add_category') . '" onclick="add_restaurant_category(\'' . esc_js(admin_url('admin-ajax.php')) . '\')" />
                        <div class="category-loader"></div>
                  </li>
                </ul>
          </div>';

	    echo force_balance_tags($html, true);
	}

	public function add_feature_to_list($foodbakery_atts = array()) {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;
	    $foodbakery_defaults = array(
		'counter_feature' => '',
		'feature_id' => '',
		'foodbakery_feature_name' => '',
		'foodbakery_feature_icon' => '',
	    );
	    extract(shortcode_atts($foodbakery_defaults, $foodbakery_atts));

	    foreach ($_POST as $keys => $values) {
		$$keys = $values;
	    }

	    if (isset($_POST['foodbakery_feature_name']) && $_POST['foodbakery_feature_name'] <> '') {
		$foodbakery_feature_name = $_POST['foodbakery_feature_name'];
	    }

	    if (isset($_POST['foodbakery_feature_icon']) && $_POST['foodbakery_feature_icon'] <> '') {
		$foodbakery_feature_icon = $_POST['foodbakery_feature_icon'];
	    }


	    if ($feature_id == '' && $counter_feature == '') {
		$counter_feature = $feature_id = rand(1000000000, 9999999999);
	    }

	    $html = '
            <tr class="parentdelete" id="edit_track' . absint($counter_feature) . '">
              <td id="subject-title' . absint($counter_feature) . '" style="width:100%;">' . esc_attr($foodbakery_feature_name) . '</td>
              <td id="subject-title' . absint($counter_feature) . '" style="width:100%;"><i class="' . esc_attr($foodbakery_feature_icon) . '"></i></td>

              <td class="centr" style="width:20%;"><a href="javascript:foodbakery_createpop(\'edit_track_form' . absint($counter_feature) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a href="#" class="delete-it btndeleteit actions delete">&nbsp;</a></td>
              <td style="width:0"><div id="edit_track_form' . esc_attr($counter_feature) . '" style="display: none;" class="table-form-elem">
                <input type="hidden" name="foodbakery_features_array[]" value="' . absint($feature_id) . '" />
                  <div class="cs-heading-area">
                        <h5 style="text-align: left;">' . foodbakery_plugin_text_srt('foodbakery_restaurant_features') . '</h5>
                        <span onclick="javascript:foodbakery_removeoverlay(\'edit_track_form' . esc_js($counter_feature) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>
                        <div class="clear"></div>
                  </div>';

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_title'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => $foodbakery_feature_name,
		    'id' => 'feature_name',
		    'return' => true,
		    'array' => true,
		    'extra_atr' => 'onchange="change_feature_value(\'subject-title' . $counter_feature . '\',this.value);"',
		    'force_std' => true,
		),
	    );

	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);

	    $html .= '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<label>' . foodbakery_plugin_text_srt('foodbakery_icon') . '</label></div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
	    $html .= foodbakery_iconlist_plugin_options($foodbakery_feature_icon, 'feature_icon' . $counter_feature, 'foodbakery_feature_icon_array');

	    $html .= '</div></div>';

	    $html .= '
                    <ul class="form-elements noborder">
                        <li class="to-label">
                          <label></label>
                        </li>
                        <li class="to-field">
                          <input type="button" value="' . foodbakery_plugin_text_srt('foodbakery_update_feature') . '" onclick="foodbakery_removeoverlay(\'edit_track_form' . esc_js($counter_feature) . '\',\'append\')" />
                        </li>
                    </ul>
                  </div>
                </td>
            </tr>';

	    if (isset($_POST['foodbakery_feature_name'])) {
		echo force_balance_tags($html);
	    } else {
		return $html;
	    }

	    if (isset($_POST['foodbakery_feature_name'])) {
		die();
	    }
	}

	public function foodbakery_ft_icon($value = '', $id = '', $name = '') {//begin function
	    if ($value == '' && $id == '' && $name == '') {
		$id = rand(10000000, 99999999);
		$name = 'foodbakery_feature_icon';
	    }
	    $html = "
			<script>
			jQuery(document).ready(function ($) {
				var this_icons;
				var rand_num = " . $id . ";
				var e9_element = $('#e9_element_' + rand_num).fontIconPicker({
					theme: 'fip-bootstrap'
				});
				icons_load_call.always(function () {
					this_icons = loaded_icons;
					// Get the class prefix
					var classPrefix = this_icons.preferences.fontPref.prefix,
							icomoon_json_icons = [],
							icomoon_json_search = [];
					$.each(this_icons.icons, function (i, v) {
						icomoon_json_icons.push(classPrefix + v.properties.name);
						if (v.icon && v.icon.tags && v.icon.tags.length) {
							icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
						} else {
							icomoon_json_search.push(v.properties.name);
						}
					});
					// Set new fonts on fontIconPicker
					e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
					// Show success message and disable
					$('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-success').text('Successfully loaded icons').prop('disabled', true);
				})
				.fail(function () {
					// Show error message and enable
					$('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-danger').text('Error: Try Again?').prop('disabled', false);
				});
			});
			</script>";

	    $html .= '
			<input type="text" id="e9_element_' . $id . '" name="' . $name . '[]" value="' . $value . '">
			<span id="e9_buttons_' . $id . '" style="display:none">\
				<button autocomplete="off" type="button" class="btn btn-primary">Load from IcoMoon selection.json</button>
			</span>';

	    if (isset($_POST['field']) && $_POST['field'] == 'icon') {
		echo json_encode(array('icon' => $html));
		die;
	    } else {
		return $html;
	    }
	}

	public function add_category_to_list($foodbakery_atts = array()) {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_static_text;
	    $foodbakery_defaults = array(
		'counter_category' => '',
		'category_id' => '',
		'foodbakery_category_name' => '',
		'foodbakery_category_parent' => '',
		'foodbakery_restaurant_taxonomy_icons' => '',
	    );
	    extract(shortcode_atts($foodbakery_defaults, $foodbakery_atts));

	    foreach ($_POST as $keys => $values) {
		$$keys = $values;
	    }

	    if (isset($_POST['foodbakery_category_name']) && $_POST['foodbakery_category_name'] <> '') {
		$foodbakery_featu_name = $_POST['foodbakery_category_name'];
	    }

	    if (isset($_POST['foodbakery_category_parent']) && $_POST['foodbakery_category_parent'] <> '') {
		$foodbakery_category_parent = $_POST['foodbakery_category_parent'];
	    }
	    if (isset($_POST['foodbakery_restaurant_taxonomy_icons']) && $_POST['foodbakery_restaurant_taxonomy_icons'] <> '') {
		$foodbakery_restaurant_taxonomy_icons = $_POST['foodbakery_restaurant_taxonomy_icons'];
	    }


	    if ($category_id == '' && $counter_category == '') {
		$counter_category = $category_id = rand(1000000000, 9999999999);
	    }

	    $html = '
            <tr class="parentdelete" id="edit_track' . absint($counter_category) . '">
              <td id="subject-title' . absint($counter_category) . '" style="width:100%;">' . esc_attr($foodbakery_category_name) . '</td>
              <td id="subject-title' . absint($counter_category) . '" style="width:100%;"><i class="' . esc_attr($foodbakery_category_parent) . '"></i></td>

              <td class="centr" style="width:20%;"><a href="javascript:foodbakery_createpop(\'edit_track_form' . absint($counter_category) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a  href="#"  data-catid=' . $counter_category . ' class="delete-it btndeleteit actions delete">&nbsp;</a></td>
              <td style="width:0"><div id="edit_track_form' . esc_attr($counter_category) . '" style="display: none;" class="table-form-elem">
                <input type="hidden" name="foodbakery_categorys_array[]" value="' . absint($category_id) . '" />
                  <div class="cs-heading-area">
                        <h5 style="text-align: left;">' . foodbakery_plugin_text_srt('foodbakery_restaurant_categorys') . '</h5>
                        <span onclick="javascript:foodbakery_removeoverlay(\'edit_track_form' . esc_js($counter_category) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>
                        <div class="clear"></div>
                  </div>';

	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_title'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'field_params' => array(
		    'std' => $foodbakery_category_name,
		    'id' => 'category_name',
		    'return' => true,
		    'array' => true,
		    'extra_atr' => 'onchange="change_category_value(\'subject-title' . $counter_category . '\',this.value);"',
		    'force_std' => true,
		),
	    );

	    $terms = get_terms(array(
		'taxonomy' => 'restaurant-category',
		'hide_empty' => false,
	    ));
	    $cats_parents = array();
	    $cats_parents[''] = foodbakery_plugin_text_srt('foodbakery_restaurant_type_no_parent');

	    foreach ($terms as $term) {

		$cats_parents[$term->term_id] = $term->name;
	    }
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);




	    $foodbakery_opt_array = array(
		'name' => esc_html__('Parent', ''),
		'desc' => '',
		'hint_text' => '',
		'field_params' => array(
		    'std' => $foodbakery_category_parent,
		    'cust_name' => 'foodbakery_category_parent[]',
		    'classes' => 'dropdown chosen-select',
		    'options' => $cats_parents,
		    'return' => true,
		),
	    );



	    $html .= $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
	    $html .= '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<label>' . foodbakery_plugin_text_srt('foodbakery_icon') . '</label></div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
	    $html .= foodbakery_iconlist_plugin_options($foodbakery_restaurant_taxonomy_icons, "restaurant_type_icon" . $counter_category, "foodbakery_restaurant_taxonomy_icon_array");

	    $html .= '</div></div>';
	    $foodbakery_opt_array = array(
		'name' => '',
		'desc' => '',
		'hint_text' => '',
		'field_params' => array(
		    'std' => '',
		    'return' => true,
		    'cust_name' => 'deleted_categories',
		    'array' => true,
		    'cust_type' => 'hidden',
		),
	    );
	    $html .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	    $html .= '
                    <ul class="form-elements noborder">
                        <li class="to-label">
                          <label></label>
                        </li>
                        <li class="to-field">
                          <input type="button" value="' . foodbakery_plugin_text_srt('foodbakery_update_category') . '" onclick="foodbakery_removeoverlay(\'edit_track_form' . esc_js($counter_category) . '\',\'append\')" />
                        </li>
                    </ul>
                  </div>
                </td>
            </tr>';

	    if (isset($_POST['foodbakery_category_name'])) {
		echo force_balance_tags($html);
	    } else {
		return $html;
	    }

	    if (isset($_POST['foodbakery_category_name'])) {
		die();
	    }
	}

	function foodbakery_post_page_elements_setting() {

	    global $post, $foodbakery_form_fields, $foodbakery_html_fields;



	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_opening_hours'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => 'on',
		    'id' => 'opening_hours_element',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_location_map'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'field_params' => array(
		    'std' => 'on',
		    'id' => 'location_element',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
	}

	public function foodbakery_save_post_categories($post_id) {
	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	    }


	    if (get_post_type() == 'restaurant-type') {
		$restaurant_type_categories = array();
		$del_Cats = isset($_POST['deleted_categories']) ? $_POST['deleted_categories'] : '';
		$feature_label = isset($_POST['feature_label']) ? $_POST['feature_label'] : '';
		$enable_not_selected = isset($_POST['foodbakery_enable_not_selected']) ? $_POST['foodbakery_enable_not_selected'] : '';
		$foodbakery_feature_icon = isset($_POST['foodbakery_feature_icon']) ? $_POST['foodbakery_feature_icon'] : '';
		$feature_array = array();
		if (!empty($feature_label)) {
		    foreach ($feature_label as $key => $lablel) {
			if ($lablel != '') {
			    $feature_array[] = $lablel;
			}
		    }
		}
		$feature_icons = array();
		if (!empty($foodbakery_feature_icon)) {
		    foreach ($foodbakery_feature_icon as $icon) {

			$feature_icons[] = $icon;
		    }
		}

		update_post_meta($post_id, 'foodbakery_enable_not_selected', $enable_not_selected);
		update_post_meta($post_id, 'feature_lables', $feature_array);
		update_post_meta($post_id, 'foodbakery_feature_icon', $feature_icons);

		$foodbakery_categorys_array = isset($_POST['foodbakery_categorys_array']) ? $_POST['foodbakery_categorys_array'] : '';
		$foodbakery_restaurant_taxonomy_icon_array = isset($_POST['foodbakery_restaurant_taxonomy_icon_array']) ? $_POST['foodbakery_restaurant_taxonomy_icon_array'] : '';

		$delete_categories = explode(',', $del_Cats);
		if (!empty($delete_categories)) {
		    foreach ($delete_categories as $cat) {
			if ($cat != '') {
			    wp_delete_term($cat, 'restaurant-category');
			}
		    }
		}
		$foodbakery_category_parent = isset($_POST['foodbakery_category_parent']) ? $_POST['foodbakery_category_parent'] : '';
		$foodbakery_category_name_array = isset($_POST['foodbakery_category_name_array']) ? $_POST['foodbakery_category_name_array'] : '';
		$cats_array = array();
		if (!empty($foodbakery_category_name_array)) {
		    foreach ($foodbakery_category_name_array as $cat_key => $cat_val) {

			$cat_parent = isset($foodbakery_category_parent[$cat_key]) ? $foodbakery_category_parent[$cat_key] : '';
			$cat_name = sanitize_title($cat_val, 'no-title');
			$cat_display_name = $cat_val;

			if (term_exists(intval($foodbakery_categorys_array[$cat_key]), 'restaurant-category')) {
			    $args = array(
				'name' => $cat_display_name,
				'parent' => $cat_parent
			    );
			    wp_update_term($foodbakery_categorys_array[$cat_key], 'restaurant-category', $args);
			    if (isset($foodbakery_restaurant_taxonomy_icon_array[$cat_key])) {
				update_term_meta($foodbakery_categorys_array[$cat_key], 'foodbakery_restaurant_taxonomy_icon', $foodbakery_restaurant_taxonomy_icon_array[$cat_key]);
			    }
			} else {

			    if (!term_exists($cat_name, 'restaurant-category')) {
				$foodbakery_cat_args = array('cat_name' => $cat_display_name, 'category_description' => foodbakery_plugin_text_srt('foodbakery_category_description'), 'category_nicename' => $cat_display_name, 'category_parent' => $cat_parent, 'taxonomy' => 'restaurant-category');

				$inserted_post_id = wp_insert_category($foodbakery_cat_args);
				$cats_array[] = $inserted_post_id;
				if (isset($foodbakery_restaurant_taxonomy_icon_array[$cat_key])) {
				    update_term_meta($inserted_post_id, 'foodbakery_restaurant_taxonomy_icon', $foodbakery_restaurant_taxonomy_icon_array[$cat_key]);
				}
			    }
			}
		    }
		}
		if ($cats_array != '') {
		    update_post_meta(get_the_id(), 'foodbakery_restaurant_type_categories', $cats_array);
		}
		wp_set_post_terms(get_the_ID(), $cats_array, 'restaurant-category', true);
	    }
	}

	public function restaurant_nutri_icons() {
	    global $post;

	    $restaurant_id = $post->ID;

	    $nutri_item_counter = rand(111456789, 987654321);
	    $html = '';
	    $nutri_items_list = '';

	    $nutri_items_list .= $this->group_restaurant_nutri_icons($restaurant_id);

	    if ($nutri_items_list == '') {
		$nutri_items_list = '<li id="no-nutri-icons-' . $nutri_item_counter . '" class="no-result-msg">' . esc_html__('No Icon added.', 'foodbakery') . '</li>';
	    }

	    $html .= '
			<div class="theme-help">
				<h4 style="padding-bottom:0px;">' . esc_html__('Nutritional Information icons', 'foodbakery') . '</h4>
				<div class="clear"></div>
			</div>

			<div class="form-elements">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="element-title">
						<div id="nutri-icons-loader-' . $nutri_item_counter . '" class="restaurant-loader"></div>
						<a class="add-nutri-item" href="javascript:void(0);" onClick="javascript:foodbakery_add_nutri_icon(\'' . $nutri_item_counter . '\');">' . esc_html__('Add Icon', 'foodbakery') . '</a>
					</div>
				</div>
				<div id="add-nutri-icon-from-' . $nutri_item_counter . '" style="display:none;">';
	    $html .= $this->foodbakery_restaurant_cat_form('', $nutri_item_counter, 'add');
	    $html .= '</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
						<div class="service-list">
						<div class="nutri-items-list-holder">
							<ul id="restaurant-cats-list-' . $nutri_item_counter . '" class="restaurant-nutri-icons-list">
								' . $nutri_items_list . '
							</ul>
						</div>
						</div>
					</div>
				</div>
			</div>';
	    echo force_balance_tags($html);
	}

	public function foodbakery_restaurant_cat_form($get_nutri_icon_vals, $nutri_item_counter, $doin_action = 'add') {
	    global $foodbakery_html_fields;
	    $form_html = '';
	    if ($doin_action == 'edit') {
		$add_btn_txt = esc_html__('Save', 'foodbakery');
		$title_name_value = ' name="nutri_icon_title[]" value="' . (isset($get_nutri_icon_vals['nutri_icon_title']) ? $get_nutri_icon_vals['nutri_icon_title'] : '') . '"';
		$img_name = 'nutri_icon_img[]';
		$img_val = isset($get_nutri_icon_vals['nutri_icon_img']) ? $get_nutri_icon_vals['nutri_icon_img'] : '';
		$add_btn_func = ' onClick="foodbakery_close_nutri_icon(\'' . $nutri_item_counter . '\');"';
	    } else {
		$add_btn_txt = esc_html__('Add Icon', 'foodbakery');
		$title_name_value = '';
		$img_name = '';
		$img_val = '';
		$add_btn_func = ' onClick="foodbakery_admin_add_nutri_icon_to_list(\'' . $nutri_item_counter . '\');"';
	    }
	    $form_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	    $form_html .= '<a href="javascript:void(0);" onClick="foodbakery_close_nutri_icon(\'' . $nutri_item_counter . '\');" class="close-nutri-item"><i class="icon-close"></i></a>';
	    $form_html .= '<div class="row">';
	    $form_html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<label>' . esc_html__('Title *', 'foodbakery') . '</label>
					<input class="nutri-item-title" id="nutri_item_title_' . $nutri_item_counter . '"' . $title_name_value . ' type="text" placeholder="' . esc_html__('Title', 'foodbakery') . '">	
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<div class="row">';

	    $foodbakery_opt_array = array(
		'name' => __('Icon', 'foodbakery'),
		'desc' => '',
		'hint_text' => '',
		'echo' => false,
		'force_std' => true,
		'std' => $img_val,
		'echo' => false,
		'id' => 'nutri_item_img_' . $nutri_item_counter,
		'field_params' => array(
		    'id' => 'nutri_item_img_' . $nutri_item_counter,
		    'cust_name' => $img_name,
		    'force_std' => true,
		    'std' => $img_val,
		    'return' => true,
		),
	    );
	    $form_html .= $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);

	    $form_html .= '
					</div>
				</div>
			</div>';
	    $form_html .= '
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="field-holder">
					<a class="add-nutri-item add-nutri-item-list" href="javascript:void(0);"' . $add_btn_func . '>' . $add_btn_txt . '</a>
				</div>
			</div>';
	    $form_html .= '</div>';
	    $form_html .= '</div>';

	    return $form_html;
	}

	public function foodbakery_restaurant_nutri_icon_item($nutri_item_counter = '', $nutri_icon_vals = '') {
	    $item_html = '';

	    if (isset($_POST['_nutri_icon_title'])) {
		$nutri_icon_title = $_POST['_nutri_icon_title'];
		$nutri_icon_img = isset($_POST['_nutri_icon_img']) ? $_POST['_nutri_icon_img'] : '';
		$nutri_item_counter = rand(1100000, 99999999);
	    } else {
		extract($nutri_icon_vals);
	    }

	    $get_nutri_icon_vals = array(
		'nutri_icon_title' => $nutri_icon_title,
		'nutri_icon_img' => $nutri_icon_img,
	    );

	    $item_html .= '
			<li class="nutri-item-' . $nutri_item_counter . '">
				<div class="drag-list">
					<span class="drag-option"><i class="icon-bars"></i></span>
					<div class="list-title">
						<h6>' . $nutri_icon_title . '</h6>
					</div>
					<div class="list-option">
						<a href="javascript:void(0);" class="edit-nutri-item" onclick="foodbakery_add_nutri_icon(\'' . $nutri_item_counter . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>
						<a href="javascript:void(0);" class="remove-nutri-item" onclick="foodbakery_remove_nutri_item(\'' . $nutri_item_counter . '\');"><i class="icon-cross-out"></i></a>
					</div>
				</div>
				<div id="add-nutri-icon-from-' . $nutri_item_counter . '" style="display: none;">
					' . $this->foodbakery_restaurant_cat_form($get_nutri_icon_vals, $nutri_item_counter, 'edit') . '
				</div>
			</li>';

	    if (isset($_POST['_nutri_icon_title'])) {
		'<script>
				$(".restaurant-nutri-icons-list").sortable({
					handle: ".drag-option",
					cursor: "move"
				});
				</script>';
		echo json_encode(array('html' => $item_html));
		die;
	    } else {
		return $item_html;
	    }
	}

	public function group_restaurant_nutri_icons($restaurant_id) {
	    $restaurant_nutri_icon_titles = get_post_meta($restaurant_id, 'nutri_icon_titles', true);
	    $restaurant_nutri_icon_imgs = get_post_meta($restaurant_id, 'nutri_icon_imgs', true);

	    $html = '';
	    if (is_array($restaurant_nutri_icon_titles) && sizeof($restaurant_nutri_icon_titles) > 0) {
		$cat_counter = 0;
		foreach ($restaurant_nutri_icon_titles as $cat_title) {
		    $nutri_item_counter = rand(1100000, 99999999);
		    $cat_img = isset($restaurant_nutri_icon_imgs[$cat_counter]) ? $restaurant_nutri_icon_imgs[$cat_counter] : '';

		    $get_nutri_icon_vals = array(
			'nutri_icon_title' => $cat_title,
			'nutri_icon_img' => $cat_img,
		    );
		    $html .= $this->foodbakery_restaurant_nutri_icon_item($nutri_item_counter, $get_nutri_icon_vals);

		    $cat_counter ++;
		}
	    }
	    return $html;
	}

	/**
	 * Settins tab contents.
	 */
	public function restaurant_type_settings_tab() {
	    global $foodbakery_html_fields, $post;

	    if (false) { // Following options are removed in foodbakery.
		$restaurant_type_icon_image = get_post_meta($post->ID, 'foodbakery_restaurant_type_icon_image', true);

		$foodbakery_opt_array = array(
		    'name' => foodbakery_plugin_text_srt('foodbakery_list_type_icon_image'),
		    'desc' => '',
		    'hint_text' => '',
		    'echo' => true,
		    'field_params' => array(
			'std' => $restaurant_type_icon_image,
			'id' => 'restaurant_type_icon_image',
			'classes' => 'small dropdown chosen-select',
			'options' => array('icon' => foodbakery_plugin_text_srt('foodbakery_icon'), 'image' => foodbakery_plugin_text_srt('foodbakery_image')),
			'return' => true,
		    ),
		);
		$foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);

		$icon_display = $image_display = 'none';
		if ($restaurant_type_icon_image == 'image') {
		    $image_display = 'block';
		} else {
		    $icon_display = 'block';
		}

		echo '<div id="restaurant-type-icon-holder" class="form-elements" style="display:' . $icon_display . '">';
		$type_icon = get_post_meta($post->ID, 'foodbakery_restaurant_type_icon', true);
		$type_icon = ( isset($type_icon[0]) ) ? $type_icon[0] : '';
		?>

		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
		    <label><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_icon'); ?></label>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		    <?php echo foodbakery_iconlist_plugin_options($type_icon, 'restaurant_type_icon', 'foodbakery_restaurant_type_icon'); ?>
		</div>

		<?php
		echo '</div>';

		echo '<div id="restaurant-type-image-holder" style="display:' . $image_display . '">';
		$foodbakery_opt_array = array(
		    'name' => esc_html__('Small Image', 'foodbakery'),
		    'desc' => '',
		    'hint_text' => '',
		    'echo' => true,
		    'id' => 'restaurant_type_image',
		    'field_params' => array(
			'id' => 'restaurant_type_image',
			'std' => ( isset($foodbakery_restaurant_type_image) ) ? $foodbakery_restaurant_type_image : '',
			'return' => true,
		    ),
		);
		$foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
		$foodbakery_opt_array = array(
		    'name' => esc_html__('Big Image', 'foodbakery'),
		    'desc' => '',
		    'hint_text' => '',
		    'echo' => true,
		    'id' => 'restaurant_type_big_image',
		    'field_params' => array(
			'id' => 'restaurant_type_big_image',
			'std' => ( isset($foodbakery_restaurant_type_big_image) ) ? $foodbakery_restaurant_type_big_image : '',
			'return' => true,
		    ),
		);
		$foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
		echo '</div>';


		/////// start for top menu
		$restaurant_menu_type_icon_image = get_post_meta($post->ID, 'foodbakery_restaurant_menu_type_icon_image', true);
		$foodbakery_opt_array = array(
		    'name' => foodbakery_plugin_text_srt('foodbakery_list_menu_type_icon_image'),
		    'desc' => '',
		    'hint_text' => '',
		    'echo' => true,
		    'field_params' => array(
			'std' => $restaurant_menu_type_icon_image,
			'id' => 'restaurant_menu_type_icon_image',
			'classes' => 'small dropdown chosen-select',
			'options' => array('icon' => foodbakery_plugin_text_srt('foodbakery_icon'), 'image' => foodbakery_plugin_text_srt('foodbakery_image')),
			'return' => true,
		    ),
		);
		$foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
		$menu_icon_display = $menu_image_display = 'none';
		if ($restaurant_menu_type_icon_image == 'image') {
		    $menu_image_display = 'block';
		} else {
		    $menu_icon_display = 'block';
		}

		echo '<div id="restaurant-menu-type-icon-holder1" class="form-elements" style="display:' . $menu_icon_display . '">';
		$menu_type_icon = get_post_meta($post->ID, 'foodbakery_menu_restaurant_type_icon', true);
		$menu_type_icon = ( isset($menu_type_icon[0]) ) ? $menu_type_icon[0] : '';
		?>

		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
		    <label><?php echo foodbakery_plugin_text_srt('foodbakery_restaurant_menu_icon'); ?></label>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		    <?php echo foodbakery_iconlist_plugin_options($menu_type_icon, 'menu_restaurant_type_icon', 'foodbakery_menu_restaurant_type_icon'); ?>
		</div>

		<?php
		echo '</div>';

		echo '<div id="restaurant-menu-type-image-holder1" class="form-elements" style="display:' . $menu_image_display . '">';
		$foodbakery_opt_array = array(
		    'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_menu_image'),
		    'desc' => '',
		    'hint_text' => '',
		    'echo' => true,
		    'id' => 'restaurant_menu_type_image',
		    'field_params' => array(
			'id' => 'restaurant_menu_type_image',
			'std' => ( isset($foodbakery_restaurant_menu_type_image) ) ? $foodbakery_restaurant_menu_type_image : '',
			'return' => true,
		    ),
		);
		$foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
		echo '</div>';
	    }

	    /////// end for top menu
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_map_marker_image'),
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'id' => 'restaurant_type_marker_image',
		'field_params' => array(
		    'id' => 'restaurant_type_marker_image',
		    'std' => ( isset($foodbakery_restaurant_type_marker_image) ) ? $foodbakery_restaurant_type_marker_image : '',
		    'return' => true,
		),
	    );
	    $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);

	    $foodbakery_search_result_page = get_post_meta($post->ID, 'foodbakery_search_result_page', true);
	    $field_args = array(
		'depth' => 0,
		'child_of' => 0,
		'class' => 'chosen-select',
		'sort_order' => 'ASC',
		'sort_column' => 'post_title',
		'show_option_none' => foodbakery_plugin_text_srt('foodbakery_select_a_page'),
		'hierarchical' => '1',
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'exclude_tree' => '',
		'selected' => $foodbakery_search_result_page,
		'echo' => 0,
		'name' => 'foodbakery_search_result_page',
		'post_type' => 'page'
	    );
	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_search_result_page'),
		'id' => 'foodbakery_search_result_page',
		'desc' => '',
		'hint_text' => '',
		'echo' => true,
		'std' => $foodbakery_search_result_page,
		'args' => $field_args,
		'return' => false,
	    );
	    $foodbakery_html_fields->foodbakery_custom_select_page_field($foodbakery_opt_array);




	    $foodbakery_opt_array = array(
		'name' => foodbakery_plugin_text_srt('foodbakery_opening_hour_time_lapse'),
		'desc' => '',
		'hint_text' => esc_html__('Only numbers are allowed ', 'foodbakery'),
		'echo' => true,
		'field_params' => array(
		    'std' => '15',
		    'id' => 'opening_hours_time_gap',
		    'classes' => 'foodbakery-number-field',
		    'return' => true,
		),
	    );

	    $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
	}

    }

    global $foodbakery_restaurant_type_meta;
    $foodbakery_restaurant_type_meta = new Foodbakery_Restaurant_Type_Meta();
    return $foodbakery_restaurant_type_meta;
}