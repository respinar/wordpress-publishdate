<?php
/*
Plugin Name: Auto Update Publish Date
Plugin URI:  https://github.com/respinar/wp-publishdate
Description: Automatically updates the publish date of a post to the current date and time when it is edited.
Version:     1.0
Author:      Hamid Peywasti
Author URI:  https://respinar.com
License:     MIT
*/

function update_post_publish_date_on_save($post_id) {
    // Check if this is an autosave or a revision
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verify if this is a revision
    if (wp_is_post_revision($post_id)) {
        return;
    }

    // Verify if this is an update of an existing post (not a new post)
    if (get_post_status($post_id) === 'publish') {
        // Update the post date to the current time
        remove_action('save_post', 'update_post_publish_date_on_save'); // Prevent infinite loop
        $current_time = current_time('mysql');
        wp_update_post(array(
            'ID' => $post_id,
            'post_date' => $current_time,
            'post_date_gmt' => get_gmt_from_date($current_time)
        ));
        add_action('save_post', 'update_post_publish_date_on_save');
    }
}
add_action('save_post', 'update_post_publish_date_on_save');
