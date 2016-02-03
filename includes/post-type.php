<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Register post type.
 * 
 * @package ListForArticles\PostType
 * @since 2.0.0
 * @return void
 */
function lfa_register_post_type()
{
    global $wp_version;
    
    $labels = array(
        'name' => __('Lists', LFA_TD),
        'singular_name' => __('List', LFA_TD),
        'add_new' => __('Add New', LFA_TD),
        'add_new_item' => __('Add New List', LFA_TD),
        'edit_item' => __('Edit List', LFA_TD),
        'new_item' => __('New List', LFA_TD),
        'all_items' => __('All Lists', LFA_TD),
        'view_item' => __('View List', LFA_TD),
        'search_items' => __('Search Lists', LFA_TD),
        'not_found' => __('No lists found', LFA_TD),
        'not_found_in_trash' => __('No lists found in Trash', LFA_TD),
        'parent_item_colon' => '',
        'menu_name' => __('Lists', LFA_TD)
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'list' ),
        'capability_type' => 'post',
        'has_archive' => true,
        'menu_position' => 2,
        'hierarchical' => false,
        'menu_position' => null,
	'menu_icon' => version_compare($wp_version, '3.8', '>=') ? 'dashicons-list-view' : LFA_ASSETS_URL . 'images/fallback-icon.png',
        'supports' => array( 'title' )
    );
    
    /**
     * Filter post type registration arguments.
     * 
     * @since 2.0.0
     * @filter lfa_post_type_args
     */
    register_post_type('list', apply_filters('lfa_post_type_args', $args));
}

/**
 * Update messages.
 * 
 * @package ListForArticles\PostType
 * @global object $post Post object
 * @global int $post_ID Post ID
 * @param array $messages Messages
 * @return array Array of messages
 */
function lfa_update_messages($messages)
{
    global $post, $post_ID;

    $messages['list'] = array(
        0 => '', // Unused. Messages start at index 1.
        1 => sprintf(__('List updated. <a href="%s">View list</a>', LFA_TD), esc_url(get_permalink($post_ID))),
        2 => __('Custom field updated.', LFA_TD),
        3 => __('Custom field deleted.', LFA_TD),
        4 => __('List updated.', LFA_TD),
        5 => isset($_GET['revision']) ? sprintf(__('List restored to revision from %s', LFA_TD), wp_post_revision_title((int) $_GET['revision'], false)) : false,
        6 => sprintf(__('List published. <a href="%s">View list</a>', LFA_TD), esc_url(get_permalink($post_ID))),
        7 => __('List saved.', LFA_TD),
        8 => sprintf(__('List submitted. <a target="_blank" href="%s">Preview list</a>', LFA_TD), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
        9 => sprintf(__('List scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview list</a>', LFA_TD), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
        10 => sprintf(__('List draft updated. <a target="_blank" href="%s">Preview list</a>', LFA_TD), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
    );

    return $messages;
}
