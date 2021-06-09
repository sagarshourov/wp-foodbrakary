<?php

/**
 * File Type: Form Fields
 */
if ( ! class_exists('foodbakery_form_fields') ) {

	class foodbakery_form_fields {

		private $counter = 0;

		public function __construct() {

			// Do something...
		}

		/**
		 * @ render label
		 */
		public function foodbakery_form_text_render($params = '') {

			global $post, $pagenow, $user;

			if ( isset($params) && is_array($params) ) {
				extract($params);
			}
			$foodbakery_output = '';
			$prefix_enable = 'true'; // default value of prefix add in name and id
			if ( ! isset($id) ) {
				$id = '';
			}
			if ( ! isset($std) ) {
				$std = '';
			}

			if ( isset($prefix_on) ) {
				$prefix_enable = $prefix_on;
			}

			$prefix = 'foodbakery_'; // default prefix
			if ( isset($field_prefix) && $field_prefix != '' ) {
				$prefix = $field_prefix;
			}
			if ( $prefix_enable != true ) {
				$prefix = '';
			}
			if ( $pagenow == 'post.php' ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
					}
				}
			} else {
				$foodbakery_value = isset($std) ? $std : '';
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}

			$foodbakery_rand_id = time();

			if ( isset($rand_id) && $rand_id != '' ) {
				$foodbakery_rand_id = $rand_id;
			}

			$html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';

			if ( isset($cus_field) && $cus_field == true ) {
				$html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
			} else {
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
			}

			if ( isset($array) && $array == true ) {
				$html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
			}

			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' id="' . $cust_id . '"';
			}

			if ( isset($cust_name) && $cust_name != '' ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			// Disabled Field
			$foodbakery_visibilty = '';
			if ( isset($active) && $active == 'in-active' ) {
				$foodbakery_visibilty = 'readonly="readonly"';
			}

			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}

			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}
			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			$foodbakery_input_type = 'text';
			if ( isset($cust_type) && $cust_type != '' ) {
				$foodbakery_input_type = $cust_type;
			}

			$foodbakery_before = '';
			if ( isset($before) && $before != '' ) {
				$foodbakery_before = '<div class="' . $before . '">';
			}

			$foodbakery_after = '';
			if ( isset($after) && $after != '' ) {
				$foodbakery_after = $after;
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}

			if ( isset($rang) && $rang == true && isset($min) && isset($max) ) {
				$foodbakery_output .= '<div class="cs-drag-slider" data-slider-min="' . $min . '" data-slider-max="' . $max . '" data-slider-step="1" data-slider-value="' . $value . '">';
			}
			$foodbakery_output .= $foodbakery_before;
			if ( $value != '' ) {
				$foodbakery_output .= '<input type="' . $foodbakery_input_type . '" ' . $foodbakery_visibilty . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . $html_id . $html_name . ' value="' . $value . '" />';
			} else {
				$foodbakery_output .= '<input type="' . $foodbakery_input_type . '" ' . $foodbakery_visibilty . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . $html_id . $html_name . ' />';
			}

			$foodbakery_output .= $foodbakery_after;

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Radio field
		 */
		public function foodbakery_form_radio_render($params = '') {
			global $post, $user, $pagenow;
			extract($params);

			$foodbakery_output = '';

			if ( ! isset($id) ) {
				$id = '';
			}

			$prefix_enable = 'true'; // default value of prefix add in name and id

			if ( isset($prefix_on) ) {
				$prefix_enable = $prefix_on;
			}

			$prefix = 'foodbakery_'; // default prefix
			if ( isset($field_prefix) && $field_prefix != '' ) {
				$prefix = $field_prefix;
			}
			if ( $prefix_enable != true ) {
				$prefix = '';
			}

			if ( $pagenow == 'post.php' ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
					}
				}
			} else {
				$foodbakery_value = isset($std) ? $std : '';
			}

			//$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}

			if ( isset($cus_field) && $cus_field == true ) {
				$html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
			} else {
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
			}

			if ( isset($array) && $array == true ) {
				$html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
			}

			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' id="' . $cust_id . '"';
			}

			if ( isset($cust_name) ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			$html_id = isset($html_id) ? $html_id : '';

			// Disbaled Field
			$foodbakery_visibilty = '';
			if ( isset($active) && $active == 'in-active' ) {
				$foodbakery_visibilty = 'readonly="readonly"';
			}
			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}
			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}

			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}

			$foodbakery_output .= '<input type="radio" ' . $foodbakery_visibilty . $foodbakery_required . ' ' . $foodbakery_classes . ' ' . $extra_atributes . ' ' . $html_id . $html_name . ' value="' . sanitize_text_field($value) . '" />';

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Radio field
		 */
		public function foodbakery_form_hidden_render($params = '') {
			global $post, $pagenow;
			extract($params);

			$foodbakery_rand_id = time();

			if ( ! isset($id) ) {
				$id = '';
			}
			$html_id = '';
			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
			$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';

			if ( isset($array) && $array == true ) {
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
			}

			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' id="' . $cust_id . '"';
			}

			if ( isset($cust_name) ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}

			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}

			$foodbakery_output = '<input type="hidden" ' . $html_id . ' ' . $foodbakery_classes . ' ' . $extra_atributes . ' ' . $html_name . ' value="' . sanitize_text_field($std) . '" />';
			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Date field
		 */
		public function foodbakery_form_date_render($params = '') {
			global $post, $pagenow;
			extract($params);

			$foodbakery_output = '';

			$foodbakery_format = 'd-m-Y';
			$prefix_enable = 'true'; // default value of prefix add in name and id

			if ( isset($prefix_on) ) {
				$prefix_enable = $prefix_on;
			}

			$prefix = 'foodbakery_'; // default prefix
			if ( isset($field_prefix) && $field_prefix != '' ) {
				$prefix = $field_prefix;
			}
			if ( $prefix_enable != true ) {
				$prefix = '';
			}
			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}
			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			if ( isset($format) && $format != '' ) {
				$foodbakery_format = $format;
			}
			$foodbakery_value = '';
			if ( $pagenow == 'post.php' ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
				}
				if ( isset($strtotime) && $strtotime == true ) {
					//$foodbakery_value = date($foodbakery_format, (int) $foodbakery_value);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
					}
				}

				if ( isset($strtotime) && $strtotime == true ) {
					//$foodbakery_value = date($foodbakery_format, (int) $foodbakery_value);
				}
			} else {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					if ( isset($strtotime) && $strtotime == true ) {
						$foodbakery_value = isset($post->ID) ? get_post_meta((int) $post->ID, 'foodbakery_' . $id, true) : '';
						//$foodbakery_value = date($foodbakery_format, (int) $foodbakery_value);
					} else {
						$foodbakery_value = isset($post->ID) ? get_post_meta($post->ID, 'foodbakery_' . $id, true) : '';
					}
				}
			}

			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				if ( isset($strtotime) && $strtotime == true ) {
					$foodbakery_value = date($foodbakery_format, (int) $foodbakery_value);
				}
				$value = $foodbakery_value;
			} elseif ( isset($std) && $std != '' ) {
				$value = $std;
			} else {
				$value = date($foodbakery_format);
			}

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}


			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}
			// disable attribute
			$foodbakery_disabled = '';
			if ( isset($disabled) && $disabled == 'yes' ) {
				$foodbakery_disabled = ' disabled="disabled"';
			}

			$foodbakery_visibilty = '';
			if ( isset($active) && $active == 'in-active' ) {
				$foodbakery_visibilty = 'readonly="readonly"';
			}

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}

			$foodbakery_rand_id = time();
			if ( isset($rand_id) && $rand_id != '' ) {
				$foodbakery_rand_id = $rand_id;
			}

			$html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';
			if ( isset($cus_field) && $cus_field == true ) {
				$html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
			} else {
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
			}

			$foodbakery_piker_id = $id;
			if ( isset($array) && $array == true ) {
				$html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
				$foodbakery_piker_id = $id . $foodbakery_rand_id;
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}

			$foodbakery_output .= '<script>
                                jQuery(function(){
                                    jQuery("#' . $prefix . $foodbakery_piker_id . '").datetimepicker({
                                        format:"' . $foodbakery_format . '",
                                        timepicker:false
                                    });
                                });
                          </script>';
			$foodbakery_output .= '<div class="input-date">';
			$foodbakery_output .= '<input type="text"' . $foodbakery_visibilty . $foodbakery_required . ' ' . $foodbakery_disabled . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . $html_id . $html_name . '  value="' . sanitize_text_field($value) . '" />';
			$foodbakery_output .= '</div>';

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Textarea field
		 */
		public function foodbakery_form_textarea_render($params = array()) {
			global $post, $pagenow;
			if ( isset($params['foodbakery_editor']) ) {
				if ( $params['foodbakery_editor'] == true ) {
					$editor_class = 'foodbakery_editor' . mt_rand();
					if ( isset($params['before']) ) {
						$params['before'] .= ' ' . $editor_class;
					} else {
						$params['before'] = ' ' . $editor_class;
					}
				}
			}
			extract($params);
			$foodbakery_output = '';
			if ( ! isset($id) ) {
				$id = '';
			}
			if ( $pagenow == 'post.php' ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
					}
				}
			} else {
				$foodbakery_value = $std;
			}
			//echo "==(".$foodbakery_value.")";

			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}

			$foodbakery_rand_id = time();

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}

			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
			if ( isset($cus_field) && $cus_field == true ) {
				$html_name = ' name="foodbakery_cus_field[' . sanitize_html_class($id) . ']"';
			} else {
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
			}

			if ( isset($array) && $array == true ) {
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
			}

			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' id="' . $cust_id . '"';
			}
			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}

			if ( isset($cust_name) ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}
			$foodbakery_before = '';
			if ( isset($before) && $before != '' ) {
				$foodbakery_before = '<div class="' . $before . '">';
			}

			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			$foodbakery_after = '';
			if ( isset($after) && $after != '' ) {
				$foodbakery_after = '</div>';
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}

			$foodbakery_output .= $foodbakery_before;
			$foodbakery_output .= ' <textarea' . $foodbakery_required . ' ' . $extra_atributes . ' ' . $html_id . $html_name . $foodbakery_classes . '>' . $value . '</textarea>';
			$foodbakery_output .= $foodbakery_after;
			if ( isset($params['foodbakery_editor']) ) {
				if ( $params['foodbakery_editor'] == true ) {
					$jquery = '<script>
						jQuery( document ).ready(function() {
							jQuery(".' . $editor_class . ' textarea").jqte();
						});
					</script>';
				}
			}
			$foodbakery_jquery = '';
			if ( isset($jquery) && $jquery != '' ) {
				$foodbakery_jquery = $jquery;
			}
			$foodbakery_output .= $foodbakery_jquery;

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render select field
		 */
		public function foodbakery_form_select_render($params = '') {
			global $post, $pagenow;
			extract($params);
			$prefix_enable = 'true'; // default value of prefix add in name and id
			if ( ! isset($id) ) {
				$id = '';
			}
			$foodbakery_output = '';

			if ( isset($prefix_on) ) {
				$prefix_enable = $prefix_on;
			}

			$prefix = 'foodbakery_'; // default prefix
			if ( isset($field_prefix) && $field_prefix != '' ) {
				$prefix = $field_prefix;
			}
			if ( $prefix_enable != true ) {
				$prefix = '';
			}

			$foodbakery_onchange = '';

			if ( $pagenow == 'post.php' ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
					}
				}
			} else {
				$foodbakery_value = $std;
			}
			//echo '<br/>'.$cus_field . '<->'.$foodbakery_value.'<br/>';
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			//echo '<br/>'.$value;
			$foodbakery_rand_id = time();
			if ( isset($rand_id) && $rand_id != '' ) {
				$foodbakery_rand_id = $rand_id;
			}

			$html_wraper = ' id="wrapper_' . sanitize_html_class($id) . '"';
			$html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';
			if ( isset($cus_field) && $cus_field == true ) {
				$html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
			} else {
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
			}

			if ( isset($array) && $array == true ) {
				$html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
				$html_wraper = ' id="wrapper_' . sanitize_html_class($id) . $foodbakery_rand_id . '"';
			}

			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' id="' . $cust_id . '"';
			}

			if ( isset($cust_name) ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			$foodbakery_display = '';
			if ( isset($status) && $status == 'hide' ) {
				$foodbakery_display = 'style=display:none';
			}

			if ( isset($onclick) && $onclick != '' ) {
				$foodbakery_onchange = 'onchange="' . $onclick . '"';
			}

			$foodbakery_visibilty = '';
			if ( isset($active) && $active == 'in-active' ) {
				$foodbakery_visibilty = 'readonly="readonly"';
			}
			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}
			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}
			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			if ( isset($markup) && $markup != '' ) {
				$foodbakery_output .= $markup;
			}

			if ( isset($div_classes) && $div_classes <> "" ) {
				$foodbakery_output .= '<div class="' . esc_attr($div_classes) . '">';
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}

			$foodbakery_output .= '<select ' . $foodbakery_visibilty . ' ' . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . $html_id . $html_name . ' ' . $foodbakery_onchange . ' >';
			if ( isset($options_markup) && $options_markup == true ) {
				$foodbakery_output .= $options;
			} else {
				if ( is_array($options) ) {
					foreach ( $options as $key => $option ) {
						if ( ! is_array($option) ) {
							$selected = '';
							if ( $key == esc_attr($value) ) {
								$selected = 'selected="selected"';
							}
							$foodbakery_output .= '<option ' . $selected . ' value="' . $key . '">' . $option . '</option>';
						}
					}
				}
			}
			$foodbakery_output .= '</select>';

			if ( isset($div_classes) && $div_classes <> "" ) {
				$foodbakery_output .= '</div>';
			}

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Multi Select field
		 */
		public function foodbakery_form_multiselect_render($params = '') {
			global $post, $pagenow;
			extract($params);

			$foodbakery_output = '';

			$prefix_enable = 'true'; // default value of prefix add in name and id
			if ( isset($prefix_on) ) {
				$prefix_enable = $prefix_on;
			}

			$prefix = 'foodbakery_'; // default prefix
			if ( isset($field_prefix) && $field_prefix != '' ) {
				$prefix = $field_prefix;
			}
			if ( $prefix_enable != true ) {
				$prefix = '';
			}
			$foodbakery_onchange = '';

			if ( $pagenow == 'post.php' ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
					}
				}
			} else {
				$foodbakery_value = $std;
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}
			$foodbakery_rand_id = time();
			if ( isset($rand_id) && $rand_id != '' ) {
				$foodbakery_rand_id = $rand_id;
			}
			$html_wraper = '';
			if ( isset($id) && $id != '' ) {
				$html_wraper = ' id="wrapper_' . sanitize_html_class($id) . '"';
			}
			$html_id = '';
			if ( isset($id) && $id != '' ) {
				$html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';
			}
			$html_name = '';
			if ( isset($cus_field) && $cus_field == true ) {
				$html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . '][]"';
			} else {
				if ( isset($id) && $id != '' ) {
					$html_name = ' name="' . $prefix . sanitize_html_class($id) . '[]"';
				}
			}

			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' id="' . $cust_id . '"';
			}

			if ( isset($cust_name) ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			$foodbakery_display = '';
			if ( isset($status) && $status == 'hide' ) {
				$foodbakery_display = 'style=display:none';
			}

			if ( isset($onclick) && $onclick != '' ) {
				$foodbakery_onchange = 'onchange="javascript:' . $onclick . '(this.value, \'' . esc_js(admin_url('admin-ajax.php')) . '\')"';
			}

			if ( ! is_array($value) && $value != '' ) {
				$value = explode(',', $value);
			}

			if ( ! is_array($value) ) {
				$value = array();
			}

			// Disbaled Field
			$foodbakery_visibilty = '';
			if ( isset($active) && $active == 'in-active' ) {
				$foodbakery_visibilty = 'readonly="readonly"';
			}
			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}
			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="multiple ' . $classes . '"';
			} else {
				$foodbakery_classes = ' class="multiple"';
			}
			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}

			$foodbakery_output .= '<select' . $foodbakery_visibilty . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . ' multiple ' . $html_id . $html_name . ' ' . $foodbakery_onchange . ' style="height:110px !important;">';

			if ( isset($options_markup) && $options_markup == true ) {
				$foodbakery_output .= $options;
			} else {
				if ( is_array($options) && sizeof($options) > 0 ) {
					foreach ( $options as $key => $option ) {
						$selected = '';
						if ( in_array($key, $value) ) {
							$selected = 'selected="selected"';
						}

						$foodbakery_output .= '<option ' . $selected . 'value="' . $key . '">' . $option . '</option>';
					}
				}
			}
			$foodbakery_output .= '</select>';

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Checkbox field         
		 */
		public function foodbakery_form_checkbox_render($params = '') {
			global $post, $pagenow;
			extract($params);
			$prefix_enable = 'true'; // default value of prefix add in name and id

			$foodbakery_output = '';

			if ( isset($prefix_on) ) {
				$prefix_enable = $prefix_on;
			}

			if ( ! isset($id) ) {
				$id = '';
			}
			$prefix = 'foodbakery_'; // default prefix
			if ( isset($field_prefix) && $field_prefix != '' ) {
				$prefix = $field_prefix;
			}
			if ( $prefix_enable != true ) {
				$prefix = '';
			}

			if ( $pagenow == 'post.php' && $id != '' ) {
				$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
				$value = $foodbakery_value;
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
					$value = $foodbakery_value;
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
						$value = $foodbakery_value;
					}
				}
			} else {
				$foodbakery_value = $std;
				$value = $foodbakery_value;
			}

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}

			$foodbakery_rand_id = time();

			$html_id = ' id="' . $prefix . sanitize_html_class($id) . '"';
			$btn_name = ' name="' . $prefix . sanitize_html_class($id) . '"';

			$html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';

			if ( isset($array) && $array == true ) {
				$html_id = ' id="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$btn_name = ' name="' . $prefix . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
			}

			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' id="' . $cust_id . '"';
			}

			if ( isset($cust_name) ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			$checked = isset($value) && $value == 'on' ? ' checked="checked"' : '';
			// Disbaled Field
			$foodbakery_visibilty = '';
			if ( isset($active) && $active == 'in-active' ) {
				$foodbakery_visibilty = 'readonly="readonly"';
			}
			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}
			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}
			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			if ( $html_id == ' id=""' || $html_id == ' id="foodbakery_"' ) {
				$html_id = '';
			}
			$html_data_id = str_replace('id=', 'data-id=', $html_id);
			if ( isset($simple) && $simple == true ) {
				if ( $value == '' ) {
					$foodbakery_output .= '<input type="checkbox" ' . $html_id . $html_name . ' ' . $foodbakery_classes . ' ' . $checked . ' ' . $extra_atributes . ' />';
				} else {
					$foodbakery_output .= '<input type="checkbox" ' . $html_id . $html_name . ' ' . $foodbakery_classes . ' ' . $checked . ' value="' . $value . '"' . $extra_atributes . ' />';
				}
			} else {
				if ( $value == '' ) {
					$value = 'off';
				}
				$foodbakery_output .= '<label class="pbwp-checkbox cs-chekbox">';
				$foodbakery_output .= '<input type="hidden"' . $html_id . $html_name . ' value="' . $value . '" />';
				$foodbakery_output .= '<input type="checkbox" ' . $html_data_id . ' ' . $foodbakery_classes . ' ' . $checked . ' ' . $extra_atributes . ' />';
				$foodbakery_output .= '<span class="pbwp-box"></span>';
				$foodbakery_output .= '</label>';
			}

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Checkbox With Input Field
		 */
		public function foodbakery_form_checkbox_with_field_render($params = '') {
			global $post, $pagenow;
			extract($params);
			extract($field);
			$prefix_enable = 'true'; // default value of prefix add in name and id

			if ( isset($prefix_on) ) {
				$prefix_enable = $prefix_on;
			}

			$prefix = 'foodbakery_'; // default prefix
			if ( isset($field_prefix) && $field_prefix != '' ) {
				$prefix = $field_prefix;
			}
			if ( $prefix_enable != true ) {
				$prefix = '';
			}

			$foodbakery_value = get_post_meta($post->ID, $prefix . $id, true);
			if ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($id) && $id != '' ) {
						$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
					}
				}
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}

			$foodbakery_input_value = get_post_meta($post->ID, $prefix . $field_id, true);
			if ( isset($foodbakery_input_value) && $foodbakery_input_value != '' ) {
				$input_value = $foodbakery_input_value;
			} else {
				$input_value = $field_std;
			}

			$foodbakery_visibilty = ''; // Disbaled Field
			if ( isset($active) && $active == 'in-active' ) {
				$foodbakery_visibilty = 'readonly="readonly"';
			}
			$foodbakery_required = '';
			if ( isset($required) && $required == 'yes' ) {
				$foodbakery_required = ' required';
			}
			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}
			$extra_atributes = '';
			if ( isset($extra_atr) && $extra_atr != '' ) {
				$extra_atributes = $extra_atr;
			}

			$foodbakery_output .= '<label class="pbwp-checkbox">';
			$foodbakery_output .= $this->foodbakery_form_hidden_render(array( 'id' => $id, 'std' => '', 'type' => '', 'return' => 'return' ));
			$foodbakery_output .= '<input type="checkbox" ' . $foodbakery_visibilty . $foodbakery_required . ' ' . $extra_atributes . ' ' . $foodbakery_classes . ' ' . ' name="' . $prefix . sanitize_html_class($id) . '" id="' . $prefix . sanitize_html_class($id) . '" value="' . sanitize_text_field('on') . '" ' . checked('on', $value, false) . ' />';
			$foodbakery_output .= '<span class="pbwp-box"></span>';
			$foodbakery_output .= '</label>';
			$foodbakery_output .= '<input type="text" name="' . $prefix . sanitize_html_class($field_id) . '"  value="' . sanitize_text_field($input_value) . '">';
			$foodbakery_output .= $this->foodbakery_form_description($description);

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render File Upload field
		 */
		public function foodbakery_media_url($params = '') {
			global $post, $pagenow;
			extract($params);

			$foodbakery_output = '';

			$foodbakery_value = isset($post->ID) ? get_post_meta($post->ID, 'foodbakery_' . $id, true) : '';
			if ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($dp) && $dp == true ) {
						$foodbakery_value = get_the_author_meta($id, $user->ID);
					} else {
						if ( isset($id) && $id != '' ) {
							$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
						}
					}
				}
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}

			$foodbakery_rand_id = time();

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}

			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
			$html_id_btn = ' id="foodbakery_' . sanitize_html_class($id) . '_btn"';
			$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';

			if ( isset($array) && $array == true ) {
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_rand_id . '"';
				$html_id_btn = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_rand_id . '_btn"';
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
			}

			$foodbakery_output .= '<input type="text" class="cs-form-text cs-input" ' . $html_id . $html_name . ' value="' . sanitize_text_field($value) . '" />';
			$foodbakery_output .= '<label class="cs-browse">';
			$foodbakery_output .= '<input type="button" ' . $html_id_btn . $html_name . ' class="uploadfile left" value="' . esc_html__('Browse', 'foodbakery') . '"/>';
			$foodbakery_output .= '</label>';

			if ( isset($return) && $return == true ) {
				return $foodbakery_output;
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render File Upload field
		 */
		public function foodbakery_form_fileupload_render($params = '') {
			global $post, $pagenow, $image_val, $foodbakery_html_fields;
			extract($params);



			$std = isset($std) ? $std : '';
			$foodbakery_output = '';
			if ( $pagenow == 'post.php' ) {

				if ( isset($dp) && $dp == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($dp) && $dp == true ) {
						$foodbakery_value = get_the_author_meta($id, $user->ID);
					} else {
						if ( isset($id) && $id != '' ) {
							$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
						}
					}
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
					//$value = $foodbakery_html_fields->get_attachment_id($foodbakery_value);
				}
			} else {
				$std = ( isset($std) ) ? $std : '';
				$value = $std;
			}

			if ( isset($force_std) && $force_std == true ) {
				$value = $std;
			}

			$btn_name = ' name="foodbakery_' . sanitize_html_class($id) . '_rand"';
			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '_rand"';
			$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';

			if ( isset($array) && $array == true ) {
				$btn_name = ' name="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
			} else if ( isset($dp) && $dp == true ) {
				$html_name = ' name="' . sanitize_html_class($id) . '"';
			}

			if ( isset($cust_name) ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			if ( isset($value) && $value != '' ) {
				$display_btn = ' style="display:none !important;"';
			} else {
				$display_btn = ' style="display:block !important;"';
			}

			$foodbakery_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $value . '"/>';

			$foodbakery_output .= '<label' . $display_btn . ' class="browse-icon"><input' . $btn_name . 'type="button" class="cs-uploadMedia left" value=' . esc_html__("Browse", "foodbakery") . ' /></label>';

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Custom File Upload field
		 */
		public function foodbakery_form_custom_fileupload_render($params = '') {
			global $post, $pagenow, $image_val;
			extract($params);

			$foodbakery_output = '';
			if ( $pagenow == 'post.php' ) {

				if ( isset($dp) && $dp == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($dp) && $dp == true ) {
						$foodbakery_value = get_the_author_meta($id, $user->ID);
					} else {
						if ( isset($id) && $id != '' ) {
							$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
						}
					}
				}
			} else {
				$foodbakery_value = $std;
			}
			$imagename_only = '';
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
				$imagename_only = $foodbakery_value;
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

			$btn_name = ' name="foodbakery_' . sanitize_html_class($id) . '_media"';
			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
			$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';

			if ( isset($array) && $array == true ) {
				$btn_name = ' name="foodbakery_' . sanitize_html_class($id) . '_media' . $foodbakery_random_id . '"';
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
			} else if ( isset($dp) && $dp == true ) {
				$html_name = ' name="' . sanitize_html_class($id) . '"';
			}

			if ( isset($cust_name) && $cust_name == true ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			if ( isset($value) && $value != '' ) {
				$display_btn = ' style=display:none';
			} else {
				$display_btn = ' style=display:block';
			}

			$foodbakery_classes = '';
			if ( isset($classes) && $classes != '' ) {
				$foodbakery_classes = ' class="' . $classes . '"';
			}

			$foodbakery_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $imagename_only . '"/>';

			$foodbakery_output .= '<label' . $display_btn . ' class="browse-icon"><input' . $btn_name . 'type="file" class="' . $foodbakery_classes . '" value=' . esc_html__("Browse", "foodbakery") . ' /></label>';

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render cvupload Upload field
		 */
		public function foodbakery_form_cvupload_render($params = '') {
			global $post, $pagenow;
			extract($params);
			$foodbakery_output = '';
			if ( $pagenow == 'post.php' ) {
				$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($dp) && $dp == true ) {
						$foodbakery_value = get_the_author_meta($id, $user->ID);
					} else {
						if ( isset($id) && $id != '' ) {
							$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
						}
					}
				}
			} else {
				$foodbakery_value = $std;
			}
			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
			} else {
				$value = $std;
			}

			if ( isset($value) && $value != '' ) {
				$display = 'style=display:block';
			} else {
				$display = 'style=display:none';
			}

			$foodbakery_random_id = FOODBAKERY_FUNCTIONS()->rand_id();

			$btn_name = ' name="' . sanitize_html_class($id) . '"';
			$html_id = ' id="' . sanitize_html_class($id) . '"';
			$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';

			if ( isset($array) && $array == true ) {
				$btn_name = ' name="' . sanitize_html_class($id) . $foodbakery_random_id . '"';
				$html_id = ' id="' . sanitize_html_class($id) . $foodbakery_random_id . '"';
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
			}

			$foodbakery_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $value . '"/>';
			$foodbakery_output .= '<label class="browse-icon"><input' . $btn_name . 'type="button" class="cs-uploadMedia left" value="' . esc_html__("Browse", "foodbakery") . '" /></label>';

			$foodbakery_output .= '<div class="page-wrap" ' . $display . ' id="foodbakery_' . sanitize_html_class($id) . '_box">';
			$foodbakery_output .= '<div class="gal-active">';
			$foodbakery_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
			$foodbakery_output .= '<ul id="gal-sortable">';
			$foodbakery_output .= '<li class="ui-state-default" id="">';
			$foodbakery_output .= '<div class="thumb-secs" id="foodbakery_' . sanitize_html_class($id) . '_img"> ' . basename($value);
			$foodbakery_output .= '<div class="gal-edit-opts"><a href="javascript:del_cv_media(\'foodbakery_' . sanitize_html_class($id) . '\', \'' . sanitize_html_class($id) . '\')" class="delete"></a> </div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</li>';
			$foodbakery_output .= '</ul>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';


			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

		/**
		 * @ render Random String
		 */
		public function foodbakery_generate_random_string($length = 3) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			for ( $i = 0; $i < $length; $i ++ ) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			return $randomString;
		}

		public function foodbakery_img_upload_button($params = '') {
			global $post, $pagenow, $image_val, $foodbakery_plugin_static_text;
			extract($params);

			$foodbakery_output = '';
			if ( $pagenow == 'post.php' ) {

				if ( isset($dp) && $dp == true ) {
					$foodbakery_value = get_post_meta($post->ID, $id, true);
				} else {
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $id, true);
				}
			} elseif ( isset($usermeta) && $usermeta == true ) {
				if ( isset($cus_field) && $cus_field == true ) {
					$foodbakery_value = get_the_author_meta($id, $user->ID);
				} else {
					if ( isset($dp) && $dp == true ) {
						$foodbakery_value = get_the_author_meta($id, $user->ID);
					} else {
						if ( isset($id) && $id != '' ) {
							$foodbakery_value = get_the_author_meta('foodbakery_' . $id, $user->ID);
						}
					}
				}
			} else {
				$foodbakery_value = $std;
			}

			if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
				$value = $foodbakery_value;
				if ( isset($dp) && $dp == true ) {
					$value = foodbakery_get_img_url($foodbakery_value, 'foodbakery_media_6');
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
				$foodbakery_random_id = rand(12345678, 98765432);
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
			}

			$btn_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';
			$html_id = ' id="foodbakery_' . sanitize_html_class($id) . '"';
			$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '"';

			if ( isset($array) && $array == true ) {
				$btn_name = ' name="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
				$html_id = ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '"';
				$html_name = ' name="foodbakery_' . sanitize_html_class($id) . '_array[]"';
			} else if ( isset($dp) && $dp == true ) {
				$html_name = ' name="' . sanitize_html_class($id) . '"';
			}
			if ( isset($cust_id) && $cust_id != '' ) {
				$html_id = ' name="' . $cust_name . '"';
			}

			if ( isset($cust_name) && $cust_name != '' ) {
				$html_name = ' name="' . $cust_name . '"';
			}

			if ( isset($value) && $value != '' ) {
				$display_btn = ' style=display:none';
			} else {
				$display_btn = ' style=display:block';
			}

			$foodbakery_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $value . '"/>';
			$foodbakery_output .= '<label' . $display_btn . ' class="browse-icon"><input' . $btn_name . 'type="button" class="cs-uploadMedia left" value=' . esc_html__('Brows', 'foodbakery') . ' /></label>';
			$foodbakery_output .= '<div class="page-wrap" ' . $display . ' id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '_box">';
			$foodbakery_output .= '<div class="gal-active">';
			$foodbakery_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
			$foodbakery_output .= '<ul id="gal-sortable">';
			$foodbakery_output .= '<li class="ui-state-default" id="">';
			$foodbakery_output .= '<div class="thumb-secs"> <img src="' . esc_url($value) . '" id="foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '_img" width="100" alt="" />';
			$foodbakery_output .= '<div class="gal-edit-opts"><a href="javascript:del_media(\'foodbakery_' . sanitize_html_class($id) . $foodbakery_random_id . '\')" class="delete delImgMedia"></a> </div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</li>';
			$foodbakery_output .= '</ul>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';
			$foodbakery_output .= '</div>';

			if ( isset($return) && $return == true ) {
				return force_balance_tags($foodbakery_output);
			} else {
				echo force_balance_tags($foodbakery_output);
			}
		}

	}

	global $foodbakery_form_fields;
	$foodbakery_form_fields = new foodbakery_form_fields();
}
