<?php

/**
 * Cache Compatiblity (W3TC, WP Super Cache, Quick Cache)
 * 
 * @since 2.0.0
 * @package ListForArticles\CacheCompatibility
 */
Class LFA_Cache_Compatibility {

    /**
     * Register some hooks.
     * 
     * @since 1.0.0
     * @return void
     */
    public function __construct()
    {
	add_filter('lfa_list_rendered', array( $this, 'render' ));

	// WP Super Cache
	if ( function_exists('add_cacheaction') ):
	    add_cacheaction('add_cacheaction', array( $this, '_wpsc_dynamic_output_buffer_init' )); // First generation
	    add_cacheaction('wpsc_cachedata', array( $this, '_wpsc_output_buffer' )); // After generation
	endif;
    }

    /**
     * Compatibility per-plugin.
     * 
     * @since 1.0.0
     * @global object $list
     * @param string $content
     * @return string
     */
    public function render($content)
    {
	global $list;
	// W3TC
	if ( defined('W3TC') ):
	    define('DONOTCACHEPAGE', true);
	endif;

	// Quick cache plugin
	if ( class_exists('\\quick_cache\\plugin') ):
	    define('QUICK_CACHE_ALLOWED', FALSE);
	endif;

	// WP Super Cache
	if ( function_exists('add_cacheaction') ):
	    add_cacheaction('wpsc_cachedata_safety', array( $this, '_wpsc_dynamic_output_buffer_safety' )); // Safety first :)
	    $content = "<!-- ListForArticles({$list->id}) -->";
	endif;

	return $content;
    }

    /**
     * WP Super Cache buffer output initializing
     * 
     * @since 1.0.0
     */
    public function _wpsc_dynamic_output_buffer_init()
    {
	add_action('wp_footer', array( $this, '_wpsc_output_buffer' ));
    }

    /**
     * Make dynamic content tags safe.
     * 
     * @since 1.0.0
     * @param int $safety
     * @return int
     */
    public function _wpsc_dynamic_output_buffer_safety($safety)
    {
	return 1;
    }

    /**
     * WP Super Cache output callback.
     * 
     * @since 1.0.0
     * @param string $output
     * @return string
     */
    public function _wpsc_output_buffer(&$output = 0)
    {
	return preg_replace_callback('/\<\!\-\-\s*ListForArticles\(([0-9]+)\)\s*\-\-\>/sim', array( $this, '_wpsc_replace_lists' ), $output);
    }

    /**
     * preg_replace callback.
     * 
     * @global array $cached_rendered_lists
     * @param array $matches
     * @return string
     */
    public function _wpsc_replace_lists($matches)
    {
	global $cached_rendered_lists;
	return isset($cached_rendered_lists[$matches[1]]) ? $cached_rendered_lists[$matches[1]] : '';
    }

}