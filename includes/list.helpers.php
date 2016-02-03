<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Prepare list choices.
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @param object $list List object
 * @return void
 */

function lfa_prepare_list_choices($list)
{
    if ( !isset($list->choices) || empty($list->choices) )
	return;

    if ( !is_admin() ):
	// Check for custom choice types
	$refresh_choices = false;
	$builtin_types = array( 'text', 'link', 'image', 'video', 'html' );
	foreach ( $list->choices as $index => $choice ):
	    if ( !in_array($choice->type, $builtin_types) &&
		    (!has_filter("lfa_render_{$choice->type}_choice_vote") || !has_filter("lfa_render_{$choice->type}_choice_result")) ):
		unset($list->choices[$index]);
		$refresh_choices = true;
	    endif;
	endforeach;

	if ( $refresh_choices ):
	    $list->choices = array_values($list->choices);
	endif;
    endif;

    // Generate an unique ID for each choice
    array_walk($list->choices, 'lfa_generate_choice_id', $list->special_id);

    // Count total votes
    $list->total_votes = 0;
    array_walk($list->choices, 'lfa_count_choices_total_votes', $list);
    array_walk($list->choices, 'lfa_calc_choice_percentages', $list->total_votes);

    /**
     * Prepare list choices
     * 
     * @since 2.0.0
     * @action lfa_list_prepare_choices
     * @param List choices
     * @param List object
     */
    do_lfa_action('lfa_list_prepare_choices', $list->choices, $list);
}

/**
 * Get current list ID.
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @global object $post Post object
 * @return int|bool Current list id, false otherwise
 */
function lfa_get_list_id()
{
    global $post;

    $list_id = false;

    // From request
    if ( isset($_REQUEST['lfa_list_id']) )
	$list_id = $_REQUEST['lfa_list_id'];

    // From post object
    if ( isset($post) && $post->post_type == 'list' )
	$list_id = $post->ID;

    /**
     * Get list ID
     * 
     * @since 2.0.0
     * @filter lfa_list_get_id
     * @param List ID
     */
    return apply_filters('lfa_list_get_id', $list_id);
}

/**
 * Get list options.
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @param int $list_id List ID
 * @return boolean
 */
function lfa_get_list_options($list_id = false)
{
    if ( !$list_id ):
	if ( !($list_id = lfa_get_list_id()) )
	    return false;
    endif;
    // Get stored options
    $options = get_post_meta($list_id, '_lfa_options', true);

    /**
     * Get list options
     * 
     * @since 2.0.0
     * @filter lfa_list_get_options
     * @param Options
     * @param List ID
     */
    $filtered_options = apply_filters('lfa_list_get_options', $options, $list_id);
    if ( empty($filtered_options) ):
	return false;
    endif;
    // Return options object
    return json_decode(json_encode($filtered_options), false);
}

/**
 * Save votes.
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @param $list List object
 * @param array $votes Choices IDs
 * @return boolean
 */
function lfa_save_list_votes($list, $cant, $votes = array())
{
    // Check there are vote
    if ( empty($votes) || !is_object($list) )
	return false;
    // Get a "raw" copy
    $new = lfa_get_list_options($list->id);
    // Add votes to choices
    foreach ( $list->choices as $index => $choice ):
    	if ( in_array($choice->id, $votes) ):
            $votes2 = $new->choices[$index]->votes;
            $votes2 = $votes2+$cant;
    	    $new->choices[$index]->votes = $votes2;
            $votes2 = $list->choices[$index]->votes;
            $votes2 = $votes2+$cant;
    	    $list->choices[$index]->votes = $votes2;
    	endif;
    endforeach;
    /**
     * Filter saved votes.
     * 
     * @since 2.0.0
     * @filter lfa_list_save_votes
     * @param New choices object (with counted votes)
     * @param Votes
     */
    $new = apply_lfa_filters('lfa_list_save_votes', $new, $votes);
    // Update options object
    return update_post_meta($list->id, '_lfa_options', json_decode(json_encode($new), true));
}

/**
 * Generate unique ID for each choice.
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @param object $choice List choice object
 * @param int $index List choice index
 * @param string $special_id List special id
 * @return void
 */
function lfa_generate_choice_id($choice, $index, $special_id)
{
    // Generate a unique ID
    $id = md5(session_id() . $index . $special_id . $choice->type);
    /**
     * Choice ID
     * 
     * @since 2.0.0
     * @filter lfa_list_generate_choice_id
     * @param ID
     * @param Choice object
     * @param Choice index
     */
    $choice->id = apply_lfa_filters('lfa_list_generate_choice_id', $id, $choice, $index);
}

/**
 * Count total votes.
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @param object $choice List choice object
 * @param object $list List object
 * @return void
 */
function lfa_count_choices_total_votes($choice, $index, $list)
{
    // Cumulate votes
    $list->total_votes += (int) $choice->votes;
}

/**
 * Calc percentages.
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @param object $choice List choice object
 * @param int $index List choice index
 * @param int $total_votes List total votes
 * @return void
 */
function lfa_calc_choice_percentages($choice, $index, $total_votes)
{
    /**
     * Filter votes percentage precision
     * 
     * @since 2.0.0
     * @filter lfa_list_percentage_precision
     * @param Precision
     */
    $precision = apply_lfa_filters('lfa_list_percentage_precision', 3);
    $choice->votes_percentage = ($total_votes == 0) ? 0 : round($choice->votes / $total_votes, $precision) * 100;
}

/**
 * Order choices by votes (sort callback).
 * 
 * @package ListForArticles\Helpers\List
 * @since 2.0.0
 * @param $current Current choice
 * @param $next Next Choice
 * @return int
 */
function lfa_order_choices_by_votes($current, $next)
{
    if ( $current->votes == $next->votes ) {
	return 0;
    }
    return ($current->votes < $next->votes) ? -1 : 1;
}