<?php
/**
 * Plugin Name:     Open All Post Links In New Tab
 * Plugin URI:      https://github.com/alordiel/wp-open-all-links-in-new-tab
 * Description:     Simple plugin to open selected post's links in new tab
 * Author:          Alexander Vasilev
 * Author URI:      https://timelinedev.com
 * Text Domain:     open-all-post-links-in-new-tab
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Open_All_Post_Links_In_New_Tab
 */

// Your code starts here.

// Hook to add meta box
add_action('add_meta_boxes', 'olnt_add_meta_box');

function olnt_add_meta_box() {
    add_meta_box(
        'olnt_meta_box', // Unique ID
        'Open Links in New Tab', // Box title
        'olnt_meta_box_html', // Content callback, must be of type callable
        'post', // Post type
        'side'
    );
}

// Meta box HTML
function olnt_meta_box_html($post) {
    $value = get_post_meta($post->ID, 'all_links_in_new_tab', true);
    ?>
    <label for="olnt_checkbox">Open all links in new window</label>
    <input type="checkbox" id="olnt_checkbox" name="olnt_checkbox" value="1" <?php checked($value, 1); ?>>
    <?php
}


// Hook to save the meta box data
add_action('save_post', 'olnt_save_meta_box_data');

function olnt_save_meta_box_data($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['olnt_checkbox'])) {
        update_post_meta($post_id, 'all_links_in_new_tab', 0);
        return;
    }

    // Update the meta field in the database.
    update_post_meta($post_id, 'all_links_in_new_tab', 1);
}



// Hook to filter post content
add_filter('the_content', 'olnt_modify_post_content');

function olnt_modify_post_content($content) {
    global $post;

    // Get the meta value
    $open_links = get_post_meta($post->ID, 'all_links_in_new_tab', true);

    // If checkbox is not checked, return content as is
    if ($open_links != 1) {
        return $content;
    }

    // Add target="_blank" to all <a> tags
    $content = preg_replace('/<a(.*?)>/', '<a$1 target="_blank">', $content);

    return $content;
}
