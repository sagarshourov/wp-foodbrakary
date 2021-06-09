<?php

add_action('load-edit-comments.php', 'wpfoodbakery_load');

function wpfoodbakery_load() {
    $screen = get_current_screen();
    add_filter("manage_{$screen->id}_columns", 'wpfoodbakery_add_columns');
}

function wpfoodbakery_add_columns($cols) {
    $new_cols = array();
    foreach ($cols as $key => $title) {
        if ($key == 'author') {
            $new_cols[$key] = $title;
            $new_cols['publisher'] = esc_html__('Publisher', 'foodbakery');
        } else {
            $new_cols[$key] = $title;
        }
    }
    return $new_cols;
}

add_action('manage_comments_custom_column', 'wpfoodbakery_column_cb', 10, 2);

function wpfoodbakery_column_cb($col, $comment_id) {
    // you could expand the switch to take care of other custom columns
    switch ($col) {
        case 'publisher':
            $author_email = get_comment_author_email($comment_id);
            if ($author_email) {
                $user = get_user_by('email', $author_email);
                if ($user) {
                    $comment_user_id = $user->ID;
                    $publisher_id = foodbakery_company_id_form_user_id($comment_user_id);
                    echo get_the_title($publisher_id);
                }
            } else {
                esc_html_e('No Publisher', 'foodbakery');
            }
            break;
    }
}
