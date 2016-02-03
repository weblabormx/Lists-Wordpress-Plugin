<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Get list id.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @global object $list
 * @return int
 */

function get_list_id()
{
    global $list;
    return $list->id;
}

/**
 * Get list question.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @global object $list
 * @return string
 */
function get_list_question()
{
    global $list;
    return $list->question;
}

/**
 * Echo the list question.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @return void
 */
function the_list_question()
{
    echo get_list_question();
}

/**
 * Get list choices.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @global object $list
 * @return array
 */
function get_list_choices()
{
    global $list;

    $choices = $list->choices;
    /*if ( isset($list->showing_results) ):
	if ( isset($list->misc->orderby_votes) ):
	    usort($choices, 'lfa_order_choices_by_votes');
	    if ( $list->misc->orderby_votes_direction == 'desc' ):
		$choices = array_reverse($choices);
	    endif;
	endif;
    else:
	if ( isset($list->misc->shuffle) ):
	    shuffle($choices);
	endif;
    endif;*/
    usort($choices, 'lfa_order_choices_by_votes');
    $choices = array_reverse($choices);
    return $choices;
}

/**
 * Check if results are locked by vote.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @global object $list
 * @return bool
 */
function is_list_results_locked()
{
    global $list;
    return isset($list->limitations->vote_for_results);
}
/**
 * Check vote ability.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @return bool
 */
function lfa_user_can_vote()
{
    return ListForArticles()->security->has_ability_to_vote();
}

/**
 * List multianswer checker.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @global object $list
 * @return bool
 */
function is_list_multianswer()
{
    global $list;
    return isset($list->limitations->multiselection);
}

/**
 * Display list results as number or other options.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @global object $list
 * @return bool
 */
function diplay_list_results_as($option)
{
    global $list;
    return ($list->misc->show_results == $option);
}

/**
 * Display buttons.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @return bool
 */
function display_list_buttons()
{
    return apply_lfa_filters('lfa_list_display_buttons', true);
}

/**
 * Display other buttons (useful for addons).
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @return void
 */
function other_list_buttons()
{
    do_lfa_action('lfa_list_other_buttons');
}

/**
 * Used by addons to add extra content to choice vote content (eg. A hidden field).
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @param object $choice
 * @return void
 */
function list_choice_vote_rendered($choice)
{
    do_lfa_action("lfa_render_{$choice->type}_choice_vote", $choice);
}

/**
 * Used by addons to add extra content to choice result content.
 * 
 * @package ListForArticles\TemplateTags
 * @since 2.0.0
 * @param object $choice
 * @return void
 */
function list_choice_result_rendered($choice)
{
    do_lfa_action("lfa_render_{$choice->type}_choice_result", $choice);
}