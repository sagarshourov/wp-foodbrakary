<?php
/**
 * @Add Meta Box For Publisher Profile
 * @return
 *
 */
add_action('show_user_profile', 'extra_user_profile_fields');
add_action('edit_user_profile', 'extra_user_profile_fields');

add_filter( 'manage_users_columns', 'manage_users_columns_callback' );
add_filter( 'manage_users_custom_column', 'manage_users_custom_column_callback', 10, 3 );

/*
 * Column for User Type
 */
function manage_users_columns_callback( $column ) {
    $column['foodbakery_user_type'] = esc_html__('User Type', 'foodbakery');
    return $column;
}

/*
 * Column for User Type
 */
function manage_users_custom_column_callback( $val, $column_name, $user_id ) {
    switch($column_name) {

        case 'foodbakery_user_type' :
        $company_id = get_user_meta($user_id, 'foodbakery_company', true);
        $user_type = get_post_meta($company_id, 'foodbakery_publisher_profile_type', true);
        $user_type_label = '';
        if( $user_type == 'buyer'){
            $user_type_label = esc_html__('Buyer', 'foodbakery');
        }else {
            $user_type_label = esc_html__('Restaurant', 'foodbakery');
        }
        return $user_type_label;
        break;

       default:
    }
}

function extra_user_profile_fields($user) {
    global $post, $foodbakery_form_fields, $foodbakery_form_fields, $foodbakery_html_fields, $foodbakery_plugin_options;
    $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
    ?>
    <table class="form-table">
        <tr>
            <th><label for="user_status"><?php echo foodbakery_plugin_text_srt('foodbakery_user_meta_profile_approved'); ?></label></th>
            <td><?php
    $user_status = array();
    $user_status = array(
        '1' => 'Approved',
        '0' => 'Pending',
    );
    $foodbakery_opt_array = array(
        'std' => isset($user->user_status) ? $user->user_status : '',
        'id' => '',
        'cust_id' => 'profile_approved',
        'cust_name' => 'profile_approved',
        'classes' => 'chosen-select-no-single small',
        'options' => $user_status,
    );
    $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
    ?></td>
        </tr>
        <tr>
            <th><label for="user_status"><?php echo esc_html__('User Type', 'foodbakery'); ?></label></th>
            <td><?php
            $user_type = array(
                'supper-admin' => 'Supper Admin',
                'team-member' => 'Team Member',
            );
            $selected_user_type = get_the_author_meta('foodbakery_user_type', $user->ID);
            $selected_user_type = ( $selected_user_type == '' ? 'team-member' : $selected_user_type );
            $foodbakery_opt_array = array(
                'std' => $selected_user_type,
                'id' => 'user_type',
                'classes' => 'chosen-select-no-single',
                'options' => $user_type,
            );
            $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
    ?></td>
        </tr>
        <tr>
            <th><label for="user_status"><?php echo esc_html__('User Comapny', 'foodbakery'); ?></label></th>
            <td><?php
            $user_type = array(
                'supper-admin' => 'Supper Admin',
                'team-member' => 'Team Member',
            );
            $post_company_args = array( 'post_type' => 'publishers', 'posts_per_page' => '-1', 'post_status' => 'publish' );
            $loop = new wp_query($post_company_args);
            $options = array();
            while ( $loop->have_posts() ) {
                $loop->the_post();

                $options[get_the_ID()] = get_the_title();
            }
            wp_reset_postdata();

            $selected_user_company = get_user_meta($user->ID, 'foodbakery_company', true);
            $foodbakery_opt_array = array(
                'std' => $selected_user_company,
                'id' => 'company',
                'classes' => 'chosen-select-no-single',
                'options' => $options,
            );
            $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
    ?></td>
        </tr>
    </table> 

    <?php
}

add_action('personal_options_update', 'save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'save_extra_user_profile_fields');

function save_extra_user_profile_fields($user_id) {
    global $wpdb;
    if ( ! current_user_can('edit_user', $user_id) ) {
        return false;
    }
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }
    $data = array();

    $user_old_status = get_user_meta($user_id, 'cs_user_status', true);

    if ( isset($_POST['profile_approved']) ) {
        $wpdb->update(
                $wpdb->prefix . 'users', array( 'user_status' => $_POST['profile_approved'] ), array( 'ID' => esc_sql($user_id) )
        );
    }
    if ( isset($_POST['foodbakery_user_type']) ) {
        update_user_meta($user_id, 'foodbakery_user_type', $_POST['foodbakery_user_type']);
    }
    if ( isset($_POST['foodbakery_company']) ) {
        update_user_meta($user_id, 'foodbakery_company', $_POST['foodbakery_company']);
    }

    do_action('foodbakery_user_profile_status_changed', $user_id, $user_old_status);
}
