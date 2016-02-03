<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Minify CSS.
 * 
 * @package ListForArticles\Helpers\Minify
 * @since 2.0.0
 * @param string $css
 * @return string
 */
function lfa_minify_css($css)
{
    if ( defined('WP_DEBUG') && WP_DEBUG === true )
	return $css;

    $minify_patterns = array(
	'#/\*.*?\*/#s' => '', // Remove comments
	'/\s*([{}|:;,])\s+/' => '$1', // Remove whitespace
	'/\s\s+(.*)/' => '$1', // Remove trailing whitespace at the start
	'/\;\}/' => '}', // Remove unnecesairy ;
    );

    return preg_replace(array_keys($minify_patterns), array_values($minify_patterns), $css);
}

/**
 * Minify HTML.
 * 
 * @package ListForArticles\Helpers\Minify
 * @since 2.0.0
 * @param string $html
 * @return string
 */
function lfa_minify_html($html)
{
    if ( defined('WP_DEBUG') && WP_DEBUG === true )
	return $html;

    return preg_replace("/\n\r|\r\n|\n|\r|\t| {2}/", '', $html);
}

/**
 * Same as add_filter but for current template only.
 * 
 * @package ListForArticles\Helpers\Filters
 * @since 2.0.0
 * @return mixed
 */
function lfa_add_filter_to_current_template($tag, $function_to_add, $priority = 10, $accepted_args = 1)
{
    global $list;
    return add_filter('__' . $list->template->name . '__' . $tag, $function_to_add, $priority, $accepted_args);
}

/**
 * Same as add_filter but for current list only.
 * 
 * @package ListForArticles\Helpers\Filters
 * @since 2.0.0
 * @return mixed
 */
function lfa_add_filter_to_current_list($tag, $function_to_add, $priority = 10, $accepted_args = 1)
{
    global $list;
    if ( isset($list->id) ):
	return add_filter('__list_' . $list->id . '__' . $tag, $function_to_add, $priority, $accepted_args);
    endif;
    return false;
}

/**
 * Same as apply_filters but for current template & list filters only.
 * 
 * @package ListForArticles\Helpers\Filters
 * @since 2.0.0
 * @global object $list List object
 * @param string $tag
 * @param mixed $value
 * @return mixed
 */
function apply_lfa_filters($tag, $value)
{
    global $list;
    $args = (array) func_get_args();
    array_shift($args);
    if ( isset($list->template->name) ):
	$args[0] = apply_filters_ref_array('__list_' . $list->id . '__' . $tag, $args);
	$args[0] = apply_filters_ref_array('__' . $list->template->name . '__' . $tag, $args);
    endif;
    return apply_filters_ref_array($tag, $args);
}

/**
 * Same as has_filter but for current template & list filters only.
 * 
 * @package ListForArticles\Helpers\Filters
 * @since 2.0.0
 * @param string $tag
 * @param callback $function_to_check
 * @return mixed
 */
function has_lfa_filter($tag, $function_to_check = false)
{
    global $list;
    if ( isset($list->template->name) ):
	return has_filter('__' . $list->template->name . '__' . $tag, $function_to_check) ||
		has_filter('__list_' . $list->id . '__' . $tag, $function_to_check);
    endif;
    return false;
}

/**
 * Same as remove_filter but for current template & list filters only.
 * 
 * @package ListForArticles\Helpers\Filters
 * @since 2.0.0
 * @global object $list
 * @param string $tag
 * @param callback $function_to_remove
 * @param int $priority
 * @return bool
 */
function remove_lfa_filter($tag, $function_to_remove, $priority = 10)
{
    global $list;
    return remove_filter('__list_' . $list->id . '__' . $tag, $function_to_remove, $priority) ||
	    remove_filter('__' . $list->template->name . '__' . $tag, $function_to_remove, $priority);
}

/**
 * Same as remove_all_filters but for current template & list filters only.
 * 
 * @package ListForArticles\Helpers\Filters
 * @since 2.0.0
 * @global object $list
 * @param string $tag
 * @param int $priority
 * @return bool
 */
function remove_all_lfa_filters($tag, $priority = false)
{
    global $list;
    return remove_all_filters('__list_' . $list->id . '__' . $tag, $priority) ||
	    remove_all_filters('__' . $list->template->name . '__' . $tag, $priority);
}

/**
 * Same as add_action but for current template only.
 * 
 * @package ListForArticles\Helpers\Actions
 * @since 2.0.0
 * @global object $list
 * @param string $tag
 * @param callback $function_to_add
 * @param int $priority
 * @param int $accepted_args
 * @return bool
 */
function lfa_add_action_to_current_template($tag, $function_to_add, $priority = 10, $accepted_args = 1)
{
    global $list;
    return add_filter('__' . $list->template->name . '__' . $tag, $function_to_add, $priority, $accepted_args);
}

/**
 * Same as add_action but for current list only.
 * 
 * @package ListForArticles\Helpers\Actions
 * @since 2.0.0
 * @global object $list
 * @param string $tag
 * @param callback $function_to_add
 * @param int $priority
 * @param int $accepted_args
 * @return bool
 */
function add_action_to_current_list($tag, $function_to_add, $priority = 10, $accepted_args = 1)
{
    global $list;
    return add_filter('__list_' . $list->id . '__' . $tag, $function_to_add, $priority, $accepted_args);
}

/**
 * Same as do_action but for current template & list actions only.
 * 
 * @package ListForArticles\Helpers\Actions
 * @since 2.0.0
 * @global object $list
 * @param string $tag
 * @param mixed $arg
 * @return mixed
 */
function do_lfa_action($tag, $arg = '')
{
    global $list;
    $args = (array) func_get_args();
    array_shift($args);

    if ( isset($list->template->name) ):
	do_action_ref_array('__list_' . $list->id . '__' . $tag, $args);
	do_action_ref_array('__' . $list->template->name . '__' . $tag, $args);
    endif;

    return do_action_ref_array($tag, $args);
}

/**
 * Same as has_action but for current template & list actions only.
 * 
 * @package ListForArticles\Helpers\Actions
 * @since 2.0.0
 * @param string $tag
 * @param callback $function_to_check
 * @return mixed
 */
function has_lfa_action($tag, $function_to_check = false)
{
    return has_lfa_filter($tag, $function_to_check);
}

/**
 * Same as remove_action but for current template & list actions only.
 * 
 * @package ListForArticles\Helpers\Actions
 * @since 2.0.0
 * @param string $tag
 * @param callback $function_to_remove
 * @param int $priority
 * @return bool
 */
function remove_lfa_action($tag, $function_to_remove, $priority = 10)
{
    return remove_lfa_filter($tag, $function_to_remove, $priority);
}

/**
 * Same as remove_all_actions but for current template & list actions only.
 * 
 * @package ListForArticles\Helpers\Actions
 * @since 2.0.0
 * @param type $tag
 * @param type $priority
 * @return bool
 */
function remove_all_lfa_actions($tag, $priority = false)
{
    return remove_all_lfa_filters($tag, $priority);
}

/**
 * Preset setting getter.
 * 
 * @package ListForArticles\Helpers\Template
 * @since 2.0.0
 * @param string $section
 * @param string|bool $field
 * @param string|bool $states
 * @param string|array|bool $arrays
 * @param string|bool $default
 * @return mixed
 */
function lfa_preset_options($section, $field = false, $states = false, $arrays = false, $default = false)
{
    return ListForArticles()->template->retrieve_options($section, $field, $states, $arrays, $default);
}

/**
 * Get current template url.
 * 
 * @package ListForArticles\Helpers\Template
 * @global object $list Current list
 * @param string $with With additional path
 * @return string Url
 */
function lfa_get_template_url($with = '')
{
    global $list;
    return LFA_TEMPLATES_URL . $list->template->name . '/' . $with;
}
