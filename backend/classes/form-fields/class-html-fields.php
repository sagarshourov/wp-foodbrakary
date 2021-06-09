<?php

/**
 * File Type: Form Fields
 */
if ( ! class_exists('foodbakery_html_fields') ) {

	class foodbakery_html_fields extends foodbakery_form_fields {

		public function __construct() {

			// Do something...
		}

		/**
		 * opening field markup
		 * 
		 */
		public function foodbakery_opening_field($params = '') {
			extract($params);
			$id = isset($id) ? $id : '';
			$foodbakery_output = '';
			$foodbakery_output .= '
			<div class="form-elements">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="' . $id . '">
					<label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';

			return $foodbakery_output;
		}

		/**
		 * full opening field markup
		 * 
		 */
		public function foodbakery_full_opening_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_output .= '<div class="form-elements"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

			return $foodbakery_output;
		}

		/**
		 * closing field markup
		 * 
		 */
		public function foodbakery_closing_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$desc = ( isset($desc) ) ? $desc : '';
			$foodbakery_output .= '<p>' . esc_html($desc) . '</p>
			</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '</div>';

			return $foodbakery_output;
		}

		/**
		 * heading markup
		 * 
		 */
		public function foodbakery_heading_render($params = '') {
			global $post;
			extract($params);
			$id = ( isset($id) ) ? $id : '';
			$foodbakery_output = '
			<div class="theme-help" id="' . sanitize_html_class($id) . '">
				<h4 style="padding-bottom:0px;">' . esc_attr($name) . '</h4>
				<div class="clear"></div>
			</div>';
			$echo = ( isset($echo) ) ? $echo : '';
			if ( false !== $echo ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * heading markup
		 * 
		 */
		public function foodbakery_set_heading($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_output .= '<li><a title="' . esc_html($name) . '" href="#"><i class="' . sanitize_html_class($fontawesome) . '"></i>
				<span class="cs-title-menu">' . esc_html($name) . '</span></a>';
			if ( is_array($options) && sizeof($options) > 0 ) {
				$active = '';
				$foodbakery_output .= '<ul class="sub-menu">';
				foreach ( $options as $key => $value ) {
					$active = ( $key == "tab-general-page-settings" ) ? 'active' : '';
					$foodbakery_output .= '<li class="' . sanitize_html_class($key) . ' ' . $active . '"><a href="#' . $key . '" onClick="toggleDiv(this.hash);return false;">' . esc_html($value) . '</a></li>';
				}
				$foodbakery_output .= '</ul>';
			}
			$foodbakery_output .= '
			</li>';

			return $foodbakery_output;
		}

		/**
		 * main heading markup
		 * 
		 */
		public function foodbakery_set_main_heading($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_output .= '<li><a title="' . $name . '" href="#' . $id . '" onClick="toggleDiv(this.hash);return false;"><i class="' . sanitize_html_class($fontawesome) . '"></i>
			<span class="cs-title-menu">' . esc_html($name) . '</span>
			</a>
			</li>';

			return $foodbakery_output;
		}

		/**
		 * sub heading markup
		 * 
		 */
		public function foodbakery_set_sub_heading($params = '') {
			extract($params);
			$foodbakery_output = '';
			$style = '';
			if ( $counter > 1 ) {
				$foodbakery_output .= '</div>';
			}
			if ( $id != 'tab-general-page-settings' ) {
				$style = 'style="display:none;"';
			}
			$extra_attr_html = '';
			if ( isset($extra) ) {
				$extra_attr_html = $extra;
			}
			$foodbakery_output .= '<div  id="' . $id . '" ' . $style . ' ' . $extra_attr_html . '>';
			$foodbakery_output .= '<div class="theme-header"><h1>' . esc_html($name) . '</h1>
			</div>';
			//if(isset($extra) && $extra == 'div'){
			$foodbakery_output .= '<div class="col-holder">';
			//}
			//$foodbakery_output .= '<div class="col2-right">';

			return $foodbakery_output;
		}

		/**
		 * announcement markup
		 * 
		 */
		public function foodbakery_set_announcement($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_output .= '<div id="' . $id . '" class="alert alert-info fade in nomargin theme_box"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&#215;</button>
			<h4>' . esc_html($name) . '</h4>
			<p>' . esc_html($std) . '</p></div>';

			return $foodbakery_output;
		}

		/**
		 * settings col right markup
		 * 
		 */
		public function foodbakery_set_col_right($params = '') {
			extract($params);
			$foodbakery_output = '';
			//$foodbakery_output .= '</div><!-- end col2-right-->';
			if ( (isset($col_heading) && $col_heading != '') || (isset($help_text) && $help_text <> '') ) {
				//$foodbakery_output .= '<div class="col3"><h3>' . esc_html($col_heading) . '</h3><p>' . esc_html($help_text) . '</p></div>';
			}
			// if(isset($extra) && $extra == 'div'){
			$foodbakery_output .= '</div>';
			//  }
			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * settings section markup
		 * 
		 */
		public function foodbakery_set_section($params = '') {
			extract($params);
			$foodbakery_output = '';
			if ( isset($accordion) && $accordion == true ) {
				if ( isset($active) && $active == true ) {
					$active = '';
				} else {
					$active = ' class="collapsed"';
				}
				$foodbakery_output .= '<div class="panel-heading"><a' . $active . ' href="#accordion-' . esc_attr($id) . '" data-parent="#accordion-' . esc_attr($parrent_id) . '" data-toggle="collapse"><h4>' . esc_html($std) . '</h4>';
			} else {
				$foodbakery_output .= '<div class="theme-help"><h4>' . esc_html($std) . '</h4><div class="clear"></div></div>';
			}
			if ( isset($accordion) && $accordion == true ) {
				$foodbakery_output .= '</a></div>';
			}

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * text field markup
		 * 
		 */
		public function foodbakery_text_field($params = '') {
			extract($params);
			$foodbakery_output = '';

			$foodbakery_styles = '';
			if ( isset($styles) && $styles != '' ) {
				$foodbakery_styles = ' style="' . $styles . '"';
			}
			$main_wraper_start = '';
			$main_wraper_end = '';
			if ( isset($main_wraper) && $main_wraper == true ) {
				$main_wraper_class_str = '';
				if ( isset($main_wraper_class) && $main_wraper_class != '' ) {
					$main_wraper_class_str = $main_wraper_class;
				}
				$main_wraper_extra_str = '';
				if ( isset($main_wraper_extra) && $main_wraper_extra != '' ) {
					$main_wraper_extra_str = $main_wraper_extra;
				}
				$main_wraper_start = '<div class="' . $main_wraper_class_str . '" ' . $main_wraper_extra_str . '>';
				$main_wraper_end = '</div>';
			}

			$cust_id = isset($id) ? ' id="' . $id . '"' : '';
			$extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
			$name = isset($name) ? $name : '';
			$field_params = isset($field_params) ? $field_params : '';
			$desc = isset($desc) ? $desc : '';
			$foodbakery_output .= $main_wraper_start;
			$foodbakery_output .= '<div' . $cust_id . $extra_attr . ' class="form-elements"' . $foodbakery_styles . '><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '</div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			//echo "<pre>";print_r($field_params);echo "</pre>";
			$foodbakery_output .= parent::foodbakery_form_text_render($field_params);
			if ( $desc ) {
				$foodbakery_output .= '<p>' . esc_html($desc) . '</p>';
			}
			$foodbakery_output .= '</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '</div>';
			$foodbakery_output .= $main_wraper_end;
			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * date field markup
		 * 
		 */
		public function foodbakery_date_field($params = '') {
			extract($params);
			$foodbakery_output = '';

			$foodbakery_styles = '';
			if ( isset($styles) && $styles != '' ) {
				$foodbakery_styles = ' style="' . $styles . '"';
			}

			$cust_id = isset($id) ? ' id="' . $id . '"' : '';
			$extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
			$foodbakery_output .= '
			<div' . $cust_id . $extra_attr . ' class="form-elements"' . $foodbakery_styles . '>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '</div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$foodbakery_output .= parent::foodbakery_form_date_render($field_params);
			$foodbakery_output .= '<p>' . esc_html($desc) . '</p></div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * textarea field markup
		 * 
		 */
		public function foodbakery_textarea_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_styles = '';
			if ( isset($styles) && $styles != '' ) {
				$foodbakery_styles = ' style="' . $styles . '"';
			}

			$cust_id = isset($id) ? ' id="' . $id . '"' : '';
			$extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
			$foodbakery_output .= '<div' . $cust_id . $extra_attr . ' class="form-elements"' . $foodbakery_styles . '><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '</div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$foodbakery_output .= parent::foodbakery_form_textarea_render($field_params);
			if ( $desc != '' ) {
				$foodbakery_output .= '<p>' . esc_html($desc) . '</p>';
			}
			$foodbakery_output .= '</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * radio field markup
		 * 
		 */
		public function foodbakery_radio_field($params = '') {
			extract($params);
			$foodbakery_output = '';

			$foodbakery_output .= '
			<div class="input-sec">';
			$foodbakery_output .= parent::foodbakery_form_radio_render($field_params);
			$foodbakery_output .= $description;
			$foodbakery_output .= '
			</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * select field markup
		 * 
		 */
		public function foodbakery_select_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_styles = '';
			$desc = isset($desc) ? $desc : '';
			if ( isset($styles) && $styles != '' ) {
				$foodbakery_styles = ' style="' . $styles . '"';
			}
			$main_wraper_start = '';
			$main_wraper_end = '';
			if ( isset($main_wraper) && $main_wraper == true ) {
				$main_wraper_class_str = '';
				if ( isset($main_wraper_class) && $main_wraper_class != '' ) {
					$main_wraper_class_str = $main_wraper_class;
				}
				$main_wraper_extra_str = '';
				if ( isset($main_wraper_extra) && $main_wraper_extra != '' ) {
					$main_wraper_extra_str = $main_wraper_extra;
				}
				$main_wraper_start = '<div class="' . $main_wraper_class_str . '" ' . $main_wraper_extra_str . '>';
				$main_wraper_end = '</div>';
			}

			$cust_id = isset($id) ? ' id="' . $id . '"' : '';
			$extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
			$foodbakery_output .= $main_wraper_start;
			$foodbakery_output .= '<div' . $cust_id . $extra_attr . ' class="form-elements"' . $foodbakery_styles . '><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '</div><div' . (isset($col_id) ? ' id="' . $col_id . '"' : '') . ' class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';

			if ( isset($array) && $array == true ) {
				$foodbakery_random_id = FOODBAKERY_FUNCTIONS()->rand_id();
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
			}
			if ( isset($multi) && $multi == true ) {
				$foodbakery_output .= parent::foodbakery_form_multiselect_render($field_params);
			} else {
				$foodbakery_output .= parent::foodbakery_form_select_render($field_params);
			}
			if ( $desc != '' ) {
				$foodbakery_output .= '<p>' . $desc . '</p>';
			}
			$foodbakery_output .= '</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '</div>';
			$foodbakery_output .= $main_wraper_end;
			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * checkbox field markup
		 * 
		 */
		public function foodbakery_checkbox_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_styles = '';
			if ( isset($styles) && $styles != '' ) {
				$foodbakery_styles = ' style="' . $styles . '"';
			}

			$cust_id = isset($id) ? ' id="' . $id . '"' : '';
			$extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
			$foodbakery_output .= '
			<div' . $cust_id . $extra_attr . ' class="form-elements"' . $foodbakery_styles . '>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$foodbakery_output .= parent::foodbakery_form_checkbox_render($field_params);
			if ( $desc ) {
				$foodbakery_output .= '<p>' . esc_html($desc) . '</p>';
			}
			$foodbakery_output .= '</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '
			</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * upload media field markup
		 * 
		 */
		public function foodbakery_media_url_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_output .= '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '</div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$foodbakery_output .= parent::foodbakery_media_url($field_params);
			$foodbakery_output .= '<p>' . esc_html($desc) . '</p>
				</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * upload file field markup
		 * 
		 */
		public function foodbakery_upload_file_field($params = '') {
			global $post, $pagenow, $image_val;

			extract($params);

			$std = isset($std) ? $std : '';

			if ( $pagenow == 'post.php' ) {

				if ( isset($dp) && $dp == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
				}
			} elseif ( isset($user) && ! empty($user) ) {

				if ( isset($dp) && $dp == true ) {

					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
				}
			} else {
				$foodbakery_value = $std;
			}


			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;

				if ( isset($dp) && $dp == true ) {

					$value = wp_get_attachment_id($foodbakery_value);
				} else {
					//$value = wp_get_attachment_url( $foodbakery_value );
					$value = $foodbakery_value;
				}
			} else {

				$value = wp_get_attachment_url($std);
			}

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}

			if ( isset($feature_img) && $feature_img == true ) {
				if ( $pagenow == 'post.php' ) {
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
					$value = $foodbakery_value;
					$get_attachment_id = $this->get_attachment_id($foodbakery_value);
				}
			}

			if ( isset($value) && $value != '' ) {
				$value = wp_get_attachment_url($value);
				$display = ' style="display:block !important;"';
			} else {
				$display = ' style="display:none !important;"';
			}

			$foodbakery_random_id = '_rand';
			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
			if ( isset($array) && $array == true ) {
				$foodbakery_random_id = FOODBAKERY_FUNCTIONS()->rand_id();
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
			}

			$field_params['foodbakery_random_id'] = $foodbakery_random_id;

			$html_render = true;
			if ( isset($without_html) && $without_html === true ) {
				$html_render = false;
			}

			$foodbakery_output = '';
			if ( $html_render === true ) {
				$foodbakery_output .= '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">';

				$foodbakery_output .= '
				<label>' . esc_attr($name) . '</label>';
				if ( isset($hint_text) && $hint_text != '' ) {
					$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
				}
				$foodbakery_output .= '</div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			}
			$foodbakery_output .= parent::foodbakery_form_fileupload_render($field_params);
			$foodbakery_output .= '<div class="page-wrap" ' . $display . ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '_box">';
			$foodbakery_output .= '<div class="gal-active">';
			$foodbakery_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
			$foodbakery_output .= '<ul id="gal-sortable">';
			$foodbakery_output .= '<li class="ui-state-default" id="">';
			$foodbakery_output .= '<div class="thumb-secs"> <img src="' . esc_url($value) . '" id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '_img" width="100" alt="" />';
			$foodbakery_output .= '<div class="gal-edit-opts"><a href="javascript:del_media(\'foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '\')" class="delete"></a> </div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</li>';
			$foodbakery_output .= '</ul>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';

			if ( $html_render === true ) {
				$foodbakery_output .= '<p>' . esc_html($desc) . '</p>
				</div>';
				if ( isset($split) && $split == true ) {
					$foodbakery_output .= '<div class="splitter"></div>';
				}
				$foodbakery_output .= '
				</div>';
			}

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * upload file field markup
		 * 
		 */
		public function foodbakery_custom_upload_file_field($params = '') {
			global $post, $pagenow, $image_val;

			extract($params);
			$std = isset($std) ? $std : '';
			if ( $pagenow == 'post.php' ) {

				if ( isset($dp) && $dp == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
				}
			} elseif ( isset($user) && ! empty($user) ) {

				if ( isset($dp) && $dp == true ) {

					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
				}
			} else {
				$foodbakery_value = $std;
			}

			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;

				if ( isset($dp) && $dp == true ) {

					$value = foodbakery_get_img_url($foodbakery_value, 'foodbakery_media_5');
				} else {
					$value = $foodbakery_value;
				}
			} else {
				$value = $std;
			}

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}
			if ( isset($value) && $value != '' ) {
				$display = 'style=display:block';
			} else {
				$display = 'style=display:none';
			}

			$foodbakery_random_id = '';
			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
			if ( isset($array) && $array == true ) {
				$foodbakery_random_id = FOODBAKERY_FUNCTIONS()->rand_id();
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
			}

			$field_params['foodbakery_random_id'] = $foodbakery_random_id;

			$foodbakery_output = '';
			$foodbakery_output .= '<div class="form-elements"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
	    <label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '</div><div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			$foodbakery_output .= parent::foodbakery_form_custom_fileupload_render($field_params);
			$foodbakery_output .= '<div class="page-wrap" ' . $display . ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '_box">';
			$foodbakery_output .= '<div class="gal-active">';
			$foodbakery_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
			$foodbakery_output .= '<ul id="gal-sortable">';
			$foodbakery_output .= '<li class="ui-state-default" id="">';
			$foodbakery_output .= '<div class="thumb-secs"> <img src="' . esc_url($value) . '" id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '_img" width="100" alt="" />';
			$foodbakery_output .= '<div class="gal-edit-opts"><a href="javascript:del_media(\'foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '\')" class="delete"></a> </div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</li>';
			$foodbakery_output .= '</ul>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';

			$foodbakery_output .= '<p>' . esc_html($desc) . '</p>
				</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '
			</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}
		
		/**
		 * select page field markup
		 * 
		 */
		public function foodbakery_custom_select_page_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_output .= '
			<div class="form-elements">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					<div class="select-style dynamic-field pages-loader-holder">';
						$foodbakery_output .= '<div class="args_'. $id.'" style="display:none;">'. json_encode($args) .'</div>';
						if( $std != '' && is_numeric($std)){
							$pages = array( $std => get_the_title($std) );
						}else{
							$pages = array( '' => esc_html__('Please select a page', "foodbakery") );
						}
						$foodbakery_output .= '<div id="'. $id.'_holder">';
							$foodbakery_output .= '<div id="'. $id.'" onclick="foodbakery_load_all_pages(\''. $id .'\');">';
								$foodbakery_output .= '<span class="select-loader loader-' . $id . '"></span>';
								$foodbakery_opt_array = array(
									'std' => $std,
									'cust_id' => $id,
									'cust_name' => $id,
									'classes' => 'chosen-select-no-single',
									'options' => $pages,
									'return' => true,
								);
								$foodbakery_output .= parent::foodbakery_form_select_render( $foodbakery_opt_array );
							$foodbakery_output .= '</div>';
						$foodbakery_output .= '</div>';
						if ( '' != $desc ) {
							$foodbakery_output .= '<p>' . esc_html($desc) . '</p>';
						}
					$foodbakery_output .= '</div>
				</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '
			</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		/**
		 * select page field markup
		 * 
		 */
		public function foodbakery_select_page_field($params = '') {
			extract($params);
			$foodbakery_output = '';
			$foodbakery_output .= '
			<div class="form-elements">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					<div class="select-style">';
			$foodbakery_output .= wp_dropdown_pages($args);
			if ( '' != $desc ) {
				$foodbakery_output .= '<p>' . esc_html($desc) . '</p>';
			}
			$foodbakery_output .= '</div>
				</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '
			</div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		public function foodbakery_multi_fields($params = '') {//var_dump($params);
			extract($params);
			$foodbakery_output = '';

			$foodbakery_styles = '';
			if ( isset($styles) && $styles != '' ) {
				$foodbakery_styles = ' style="' . $styles . '"';
			}
			$cust_id = isset($id) ? ' id="' . $id . '"' : '';
			$extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
			$foodbakery_output .= '
			<div' . $cust_id . $extra_attr . ' class="form-elements"' . $foodbakery_styles . '>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<label>' . esc_attr($name) . '</label>';
			if ( isset($hint_text) && $hint_text != '' ) {
				$foodbakery_output .= foodbakery_tooltip_helptext(esc_html($hint_text));
			}
			$foodbakery_output .= '
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
			if ( isset($fields_list) && is_array($fields_list) ) {
				foreach ( $fields_list as $field_array ) {
					if ( $field_array['type'] == 'text' ) {
						$foodbakery_output .= parent::foodbakery_form_text_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'hidden' ) {
						$foodbakery_output .= parent::foodbakery_form_hidden_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'select' ) {
						$foodbakery_output .= parent::foodbakery_form_select_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'multiselect' ) {
						$foodbakery_output .= parent::foodbakery_form_multiselect_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'checkbox' ) {
						$foodbakery_output .= parent::foodbakery_form_checkbox_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'radio' ) {
						$foodbakery_output .= parent::foodbakery_form_radio_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'date' ) {
						$foodbakery_output .= parent::foodbakery_form_radio_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'textarea' ) {
						$foodbakery_output .= parent::foodbakery_form_textarea_render($field_array['field_params']);
					} elseif ( $field_array['type'] == 'media' ) {
						$foodbakery_output .= parent::foodbakery_media_url($field_array['field_params']);
					} elseif ( $field_array['type'] == 'fileupload' ) {
						$foodbakery_output .= '<div class="page-wrap" ' . $display . ' id="foodbakery_' . sanitize_html_class($id) . '_box">';
						$foodbakery_output .= '<div class="gal-active">';
						$foodbakery_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
						$foodbakery_output .= '<ul id="gal-sortable">';
						$foodbakery_output .= '<li class="ui-state-default" id="">';
						$foodbakery_output .= '<div class="thumb-secs"> <img src="' . esc_url($value) . '" id="foodbakery_' . sanitize_html_class($id) . '_img" width="100" alt="" />';
						$foodbakery_output .= '<div class="gal-edit-opts"><a href="javascript:del_media(\'foodbakery_' . sanitize_html_class($id) . '\')" class="delete"></a> </div>';
						$foodbakery_output .= '</div>';
						$foodbakery_output .= '</li>';
						$foodbakery_output .= '</ul>';
						$foodbakery_output .= '</div>';
						$foodbakery_output .= '</div>';
						$foodbakery_output .= '</div>';
						$foodbakery_output .= parent::foodbakery_form_fileupload_render($field_params);
					} elseif ( $field_array['type'] == 'dropdown_pages' ) {
						$foodbakery_output .= wp_dropdown_pages($args);
					}
				}
			}

			$foodbakery_output .= '<p>' . esc_html($desc) . '</p>
				</div>';
			if ( isset($split) && $split == true ) {
				$foodbakery_output .= '<div class="splitter"></div>';
			}
			$foodbakery_output .= '
			</div>';
			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		public function foodbakery_gallery_render($params = '') {
			global $post;
			extract($params);
			$post_id = (isset($post_id) && $post_id != '' ) ? $post_id : $post->ID;
			$foodbakery_random_id = rand(156546, 956546);
			$foodbakery_output = '';

			$foodbakery_output .= '<div class="form-elements">';
			$foodbakery_output .= ' <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div id="gallery_container_' . esc_attr($foodbakery_random_id) . '" data-csid="foodbakery_' . esc_attr($id) . '">
                        <script>
                            jQuery(document).ready(function () {
                                jQuery("#gallery_sortable_' . esc_attr($foodbakery_random_id) . '").sortable({
                                    out: function (event, ui) {
                                        foodbakery_gallery_sorting_list("foodbakery_' . sanitize_html_class($id) . '", "' . esc_attr($foodbakery_random_id) . '");
                                    }
                                });

                                gal_num_of_items("' . esc_attr($id) . '", "' . absint($foodbakery_random_id) . '", "");

                                jQuery("#gallery_container_' . esc_attr($foodbakery_random_id) . '").on("click", "a.delete", function () {
                                    gal_num_of_items("' . esc_attr($id) . '", "' . absint($foodbakery_random_id) . '", 1);
                                    jQuery(this).closest("li.image").remove();
                                    foodbakery_gallery_sorting_list("foodbakery_' . sanitize_html_class($id) . '", "' . esc_attr($foodbakery_random_id) . '");
                                });
                            });
                        </script>
                        <ul class="gallery_images" id="gallery_sortable_' . esc_attr($foodbakery_random_id) . '">';
			$gallery = get_post_meta($post_id, 'foodbakery_' . $id . '_ids', true);
			$gallery_titles = get_post_meta($post_id, 'foodbakery_' . $id . '_title', true);
			$gallery_descs = get_post_meta($post_id, 'foodbakery_' . $id . '_desc', true);
			$foodbakery_gal_counter = 0;
			if ( is_array($gallery) && sizeof($gallery) > 0 ) {
				foreach ( $gallery as $foodbakery_attach_id ) {
					$attach_url = wp_get_attachment_url($foodbakery_attach_id);
					if ( $attach_url != '' ) {

						$foodbakery_gal_id = rand(156546, 956546);

						$foodbakery_gallery_title = isset($gallery_titles[$foodbakery_gal_counter]) ? $gallery_titles[$foodbakery_gal_counter] : '';
						$foodbakery_gallery_desc = isset($gallery_descs[$foodbakery_gal_counter]) ? $gallery_descs[$foodbakery_gal_counter] : '';

						$foodbakery_attach_img = $this->foodbakery_get_icon_for_attachment($foodbakery_attach_id);
						$foodbakery_output .= '
                                            <li class="image" data-attachment_id="' . esc_attr($foodbakery_gal_id) . '">
                                                    ' . $foodbakery_attach_img . '
                                                    <input type="hidden" value="' . esc_attr($foodbakery_attach_id) . '" name="foodbakery_' . $id . '_ids[]" />
                                                    <div class="actions">
                                                            <span><a href="javascript:;" class="delete tips" data-tip="' . ('foodbakery_delete_image') . '"><i class="icon-times"></i></a></span>
                                                    </div>
                                                    <tr class="parentdelete" id="edit_track' . absint($foodbakery_gal_id) . '">
                                                      <td style="width:0">
                                                      <div id="edit_track_form' . absint($foodbakery_gal_id) . '" style="display: none;" class="table-form-elem">
                                                              <div class="form-elements">
                                                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                      <label>&nbsp;</label>
                                                                    </div>
                                                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                                                      ' . $foodbakery_attach_img . '
                                                                    </div>
                                                              </div>
                                                            </div>
                                                            </td>
                                                    </tr>
                                            </li>';
					}
					$foodbakery_gal_counter ++;
				}
			}
			$foodbakery_output .= '</ul>
                    </div>
                    <div id="foodbakery_' . esc_attr($id) . '_temp"></div>
                    <input type="hidden" value="" name="foodbakery_' . esc_attr($id) . '_num" />
                    <div style="width:100%; display:inline-block; margin:20px 0;">
                        <label class="browse-icon add_gallery_plugin hide-if-no-js" data-id="foodbakery_' . sanitize_html_class($id) . '" data-rand_id="' . esc_attr($foodbakery_random_id) . '">
                            <input type="button" class="left" data-choose="' . esc_attr($name) . '" data-update="' . esc_attr($name) . '" data-delete="' . ('foodbakery_delete') . '" data-text="' . ('foodbakery_delete') . '"  value="' . esc_attr($name) . '">
                        </label>
                    </div>
                </div>
            </div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		public function foodbakery_gallery_render_user($params = '') {
			extract($params);
			if ( isset($user) ) {
				$user_id = $user->ID;
			}
			$foodbakery_random_id = rand(156546, 956546);
			$foodbakery_output = '';

			$foodbakery_output .= '<div class="form-elements">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label>' . esc_attr($name) . ' </label>
                </div>';
			$foodbakery_output .= ' <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div id="gallery_container_' . esc_attr($foodbakery_random_id) . '" data-csid="foodbakery_' . esc_attr($id) . '">
                        <script>
                            jQuery(document).ready(function () {
                                jQuery("#gallery_sortable_' . esc_attr($foodbakery_random_id) . '").sortable({
                                    out: function (event, ui) {
                                        foodbakery_gallery_sorting_list("foodbakery_' . sanitize_html_class($id) . '", "' . esc_attr($foodbakery_random_id) . '");
                                    }
                                });

                                gal_num_of_items("' . esc_attr($id) . '", "' . absint($foodbakery_random_id) . '", "");

                                jQuery("#gallery_container_' . esc_attr($foodbakery_random_id) . '").on("click", "a.delete", function () {
                                    gal_num_of_items("' . esc_attr($id) . '", "' . absint($foodbakery_random_id) . '", 1);
                                    jQuery(this).closest("li.image").remove();
                                    foodbakery_gallery_sorting_list("foodbakery_' . sanitize_html_class($id) . '", "' . esc_attr($foodbakery_random_id) . '");
                                });
                            });
                        </script>
                        <ul class="gallery_images user_gallery" id="gallery_sortable_' . esc_attr($foodbakery_random_id) . '">';
			$foodbakery_attach_id = get_user_meta($user_id, 'foodbakery_' . $id, true);

			$add_button_text = foodbakery_plugin_text_srt('foodbakery_add') . ' ' . esc_attr($name);


			if ( $foodbakery_attach_id ) {

				$attach_url = wp_get_attachment_url($foodbakery_attach_id);
				if ( $attach_url != '' ) {
					$add_button_text = foodbakery_plugin_text_srt('foodbakery_update') . ' ' . esc_attr($name);
					$foodbakery_gal_id = rand(156546, 956546);
					$foodbakery_attach_img = $this->foodbakery_get_icon_for_attachment($foodbakery_attach_id);
					$foodbakery_output .= '
										<li class="image" data-attachment_id="' . esc_attr($foodbakery_attach_id) . '">
												' . $foodbakery_attach_img . '
												<input type="hidden" value="' . esc_attr($foodbakery_attach_id) . '" name="foodbakery_' . $id . '" />
												<div class="actions">
													<span><a href="javascript:;" class="delete tips" data-tip="' . ('foodbakery_delete_image') . '"><i class="icon-times"></i></a></span>
												</div>
												<tr class="parentdelete" id="edit_track' . absint($foodbakery_gal_id) . '">
												  <td style="width:0">
												  <div id="edit_track_form' . absint($foodbakery_gal_id) . '" style="display: none;" class="table-form-elem">
														  <div class="form-elements">
																<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																  <label>&nbsp;</label>
																</div>
																<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
																  ' . $foodbakery_attach_img . '
																</div>
														  </div>
														</div>
														</td>
												</tr>
										</li>';
				}
			}
			$foodbakery_output .= '</ul>
                    </div>
                    <div id="foodbakery_' . esc_attr($id) . '_temp"></div>
                    <input type="hidden" value="" name="foodbakery_' . esc_attr($id) . '_num" />
                    <div style="width:100%; display:inline-block; margin:20px 0;">
                        <label class="browse-icon add_gallery_plugin hide-if-no-js" data-id="foodbakery_' . sanitize_html_class($id) . '" data-rand_id="' . esc_attr($foodbakery_random_id) . '" data-button_label="' . esc_attr($add_button_text) . '" data-multiple="false">
                            <input type="button" class="left" data-choose="' . esc_attr($add_button_text) . '" data-update="' . esc_attr($add_button_text) . '" data-delete="' . ('foodbakery_delete') . '" data-text="' . ('foodbakery_delete') . '"  value="' . esc_attr($add_button_text) . '">
                        </label>
                    </div>
                </div>
            </div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		public function foodbakery_gallery_render_plugin_option($params = '') {
			global $foodbakery_plugin_options;
			extract($params);

			$foodbakery_random_id = rand(156546, 956546);
			$foodbakery_output = '';

			$foodbakery_output .= '<div class="form-elements">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label>' . esc_attr($name) . ' </label>
                </div>';
			$foodbakery_output .= ' <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div id="gallery_container_' . esc_attr($foodbakery_random_id) . '" data-csid="foodbakery_' . esc_attr($id) . '">
                        <script>
                            jQuery(document).ready(function () {
                                jQuery("#gallery_sortable_' . esc_attr($foodbakery_random_id) . '").sortable({
                                    out: function (event, ui) {
                                        foodbakery_gallery_sorting_list("foodbakery_' . sanitize_html_class($id) . '", "' . esc_attr($foodbakery_random_id) . '");
                                    }
                                });

                                gal_num_of_items("' . esc_attr($id) . '", "' . absint($foodbakery_random_id) . '", "");

                                jQuery("#gallery_container_' . esc_attr($foodbakery_random_id) . '").on("click", "a.delete", function () {
                                    gal_num_of_items("' . esc_attr($id) . '", "' . absint($foodbakery_random_id) . '", 1);
                                    jQuery(this).closest("li.image").remove();
                                    foodbakery_gallery_sorting_list("foodbakery_' . sanitize_html_class($id) . '", "' . esc_attr($foodbakery_random_id) . '");
                                });
                            });
                        </script>
                        <ul class="gallery_images user_gallery" id="gallery_sortable_' . esc_attr($foodbakery_random_id) . '">';
			$gallery = isset($foodbakery_plugin_options['foodbakery_' . $id . '_ids']) ? $foodbakery_plugin_options['foodbakery_' . $id . '_ids'] : '';

			$foodbakery_gal_counter = 0;
			if ( is_array($gallery) && sizeof($gallery) > 0 ) {
				foreach ( $gallery as $foodbakery_attach_id ) {
					$attach_url = wp_get_attachment_url($foodbakery_attach_id);
					if ( $attach_url != '' ) {
						$foodbakery_gal_id = rand(156546, 956546);
						$foodbakery_attach_img = $this->foodbakery_get_icon_for_attachment($foodbakery_attach_id);
						$foodbakery_output .= '
                                            <li class="image" data-attachment_id="' . esc_attr($foodbakery_attach_id) . '">
                                                    ' . $foodbakery_attach_img . '
                                                    <input type="hidden" value="' . esc_attr($foodbakery_attach_id) . '" name="foodbakery_' . $id . '_ids[]" />
                                                    <div class="actions">
                                                            <span><a href="javascript:;" class="delete tips" data-tip="' . ('foodbakery_delete_image') . '"><i class="icon-times"></i></a></span>
                                                    </div>
                                                    <tr class="parentdelete" id="edit_track' . absint($foodbakery_gal_id) . '">
                                                      <td style="width:0">
                                                      <div id="edit_track_form' . absint($foodbakery_gal_id) . '" style="display: none;" class="table-form-elem">
                                                              <div class="form-elements">
                                                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                      <label>&nbsp;</label>
                                                                    </div>
                                                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                                                      ' . $foodbakery_attach_img . '
                                                                    </div>
                                                              </div>
                                                            </div>
                                                            </td>
                                                    </tr>
                                            </li>';
					}
					$foodbakery_gal_counter ++;
				}
			}
			$foodbakery_output .= '</ul>
                    </div>
                    <div id="foodbakery_' . esc_attr($id) . '_temp"></div>
                    <input type="hidden" value="" name="foodbakery_' . esc_attr($id) . '_num" />
                    <div style="width:100%; display:inline-block; margin:20px 0;">
                        <label class="browse-icon add_gallery_plugin hide-if-no-js" data-id="foodbakery_' . sanitize_html_class($id) . '" data-rand_id="' . esc_attr($foodbakery_random_id) . '" data-button_label="' . esc_attr($desc) . '">
                            <input type="button" class="left" data-choose="' . esc_attr($desc) . '" data-update="' . esc_attr($desc) . '" data-delete="' . ('foodbakery_delete') . '" data-text="' . ('foodbakery_delete') . '"  value="' . esc_attr($desc) . '">
                        </label>
                    </div>
                </div>
            </div>';

			if ( isset($echo) && $echo == true ) {
				echo force_balance_tags($foodbakery_output);
			} else {
				return $foodbakery_output;
			}
		}

		public function get_attachment_id($attachment_url) {
			global $wpdb;
			$attachment_id = false;
			//  If there is no url, return. 
			if ( '' == $attachment_url )
				return;
			// Get the upload foodbakery paths 
			$upload_dir_paths = wp_upload_dir();
			if ( false !== strpos($attachment_url, $upload_dir_paths['baseurl']) ) {
				//  If this is the URL of an auto-generated thumbnail, get the URL of the original image 
				$attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);
				// Remove the upload path base foodbakery from the attachment URL 
				$attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);

				$attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
			}
			return $attachment_id;
		}

		public function foodbakery_get_icon_for_attachment($post_id) {
			return wp_get_attachment_image($post_id, 'thumbnail');
		}

	}

	global $foodbakery_html_fields;
	$foodbakery_html_fields = new foodbakery_html_fields();
}
