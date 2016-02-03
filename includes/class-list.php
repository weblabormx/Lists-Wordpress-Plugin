<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * List Loader.
 * 
 * @since 2.0.0
 * @package ListForArticles\List
 */

Class LFA_List {

    /**
     * Register hooks to prepare and render list.
     * 
     * @since 2.0.0
     * @return void
     */
    public function __construct()
    {
	// A simple cache layer
	global $cached_lists, $cached_rendered_lists;
	$cached_lists = array();
	$cached_rendered_lists = array();

	// Setup list object
	add_action('wp', array( $this, 'setup' ), 10);
	// Register shortcode
	add_shortcode('list-for-articles', array( $this, 'shortcode' ));
    }

    /**
     * Load list.
     * 
     * @since 2.0.0
     * @global object $list
     * @global array $cached_lists
     * @param int|bool $list_id List ID
     * @return bool
     */
    public function load($list_id = false)
    {
	global $list, $cached_lists;

	// Prepare ID
	$list_id = $list_id ? $list_id : lfa_get_list_id();

	// Check if there is a cached copy
	if ( $list_id && isset($cached_lists[$list_id]) ):
	    // Then set current object from the cached copy
	    $list = $cached_lists[$list_id];
	    return true;
	endif;

	// Get stored options
	$list = lfa_get_list_options($list_id);

	// Check validity
	if ( !is_object($list) )
	    return false;

	// Load template
	ListForArticles()->template->load();

	// Assign id and special id (for security reasons)
	$list->id = $list_id;

	/**
	 * Filter the special ID.
	 * 
	 * @since 2.0.0
	 * @filter lfa_list_generate_special_id
	 * @param Current special ID
	 * @param List object
	 */
	$list->special_id = apply_lfa_filters('lfa_list_generate_special_id', "tpvb-{$list->id}", $list);

	// Prepare choices
	lfa_prepare_list_choices($list);

	// Cache list
	$cached_lists[$list_id] = $list;

	// Enqueue assets
	$minimized = defined('WP_DEBUG') && WP_DEBUG === true ? '' : '.min';
	wp_enqueue_script('fastclick', LFA_JS_ASSETS . "fastclick$minimized.js", LFA_VERSION);
	wp_enqueue_script('listforarticles', LFA_JS_ASSETS . "listforarticles.js", array( 'jquery', 'fastclick' ), LFA_VERSION);
	/**
	 * Enqueue assets.
	 * 
	 * @since 2.0.0
	 * @action lfa_list_enqueue_assets
	 * @param Minimized
	 */
	do_lfa_action('lfa_list_enqueue_assets', $minimized);

	return true;
    }

    /**
     * Unload current list.
     * 
     * @since 2.0.0
     * @global object $list
     * @global array $cached_lists
     * @return void
     */
    public function unload()
    {
	global $list, $cached_lists, $cached_rendered_lists;
	unset($cached_lists[$list->id], $cached_rendered_lists[$list->id]);
	$list = false;
    }

    /**
     * Setup list when ID is present.
     * 
     * @since 2.0.0
     * @global object $post
     * @global array $shortcode_tags
     * @return void
     */
    public function setup()
    {
	global $post, $shortcode_tags;

	if ( is_object($post) && $post->post_type == 'list' ):

	    // Excute shortcode earlier (before sending headers)
	    $tagnames = array_keys($shortcode_tags);
	    $tagregexp = join('|', array_map('preg_quote', $tagnames));

	    // ListForArticles shortcode regex
	    $shortcode_regex = str_replace($tagregexp, 'list-for-articles', get_shortcode_regex());
	    $post->post_content = preg_replace_callback("/$shortcode_regex/s", array( $this, 'do_shortcode' ), $post->post_content);

	endif;

	/**
	 * Load list when it's a post
	 */
	if ( lfa_get_list_id() && is_single() ):
	    $this->load();
	    add_filter('the_content', array( $this, 'single_post' ));
	endif;
    }

    /**
     * Special loading for shortcodes.
     * Load shortcode earlier (before sending headers).
     * 
     * @since 2.0.0
     * @param array $match preg_match regular match array
     * @return string Shortcode
     */
    public function do_shortcode($match)
    {
	// Parse attributes (id)
	$attr = shortcode_parse_atts($match[3]);
	// Load list
	$this->load($attr['id']);
	// Return shortcode again to WordPress to excute it later in 'the_content' filter
	return $match[0];
    }

    /**
     * Render list.
     * 
     * @global object $list List object
     * @return string Rendered list
     */
    public function get_render($skip_css = false)
    {
		global $list, $cached_rendered_lists;

		// Check list
		if ( !is_object($list) )
		    return;

		// Check if there is a cached copy
		if ( isset($cached_rendered_lists[$list->id]) ):
		    // Then set current object from the cached copy
		    return $cached_rendered_lists[$list->id];
		endif;

		/**
		 * Before list render
		 * 
		 * @since 2.0.0
		 * @action lfa_list_before_render
		 * @param List object
		 */
		do_lfa_action('lfa_list_before_render', $list);
		
		// Start capture of content
		ob_start();

		$style = '';

		// Omit css if rendered before
		if ( !$skip_css && !isset(ListForArticles()->template->presets->{$list->template->name}->{$list->template->preset->name}->rendered) ):
		    $style = sprintf('<style type="text/css">%s</style>', ListForArticles()->template->get_css());
		    if ( is_object(ListForArticles()->template->presets->{$list->template->name}->{$list->template->preset->name}) ):
			ListForArticles()->template->presets->{$list->template->name}->{$list->template->preset->name}->rendered = true;
		    endif;
		endif;

		$content_file = '';
		$list->showing_vote = true;
		$content_file = 'vote.php';
		/*if ( ( !isset($list->skip_to_results) || $list->skip_to_results === false ) && ListForArticles()->security->has_ability_to_vote() ):
		    $list->showing_vote = true;
		    $content_file = 'vote.php';
		else:
		    $list->showing_results = true;
		    $content_file = 'results.php';
		endif;*/
		/**
		 * File to render (vote.php or results.php).
		 * 
		 * @since 2.0.0
		 * @filter lfa_list_render_file
		 * @param File name
		 */
		$content_file = apply_lfa_filters('lfa_list_render_file', $content_file);

		echo ListForArticles()->template->get_part('header.php');
		echo ListForArticles()->template->get_part($content_file);
		echo ListForArticles()->template->get_part('footer.php');

		// Preset class
		$id = "lfa-{$list->template->name}-{$list->template->preset->name}-preset";
		// Minify and wrap content
		$classes = apply_lfa_filters('lfa_list_container_classes', array( 'lfa-list-container' ));
		$attributes = apply_lfa_filters('lfa_list_container_attributes', '');
		$content = sprintf('<div id="%s" class="%s" %s>%s</div>', $id, implode(' ', $classes), $attributes, ob_get_clean());
		/**
		 * Render list.
		 * 
		 * @since 2.0.0
		 * @filter lfa_list_render
		 * @param Content
		 */
		$cached_rendered_lists[$list->id] = apply_lfa_filters('lfa_list_render_with_style', lfa_minify_html($style . apply_lfa_filters('lfa_list_render', $content)));
		
		/**
		 * After list render
		 * 
		 * @since 2.0.0
		 * @action lfa_list_after_render
		 * @param Rendered content
		 */
		do_lfa_action('lfa_list_after_render', $cached_rendered_lists[$list->id]);
		
		/**
		 * Rendered list
		 * 
		 * @since 2.0.0
		 * @filter lfa_list_rendered
		 * @param Rendered list
		 */
		return apply_lfa_filters('lfa_list_rendered', $cached_rendered_lists[$list->id]);
	 }

    /**
     * Render list to buffer.
     * 
     * @since 2.0.0
     * @return void
     */
    public function render()
    {
	echo $this->get_render();
    }

    /**
     * Shortcode.
     * @since 2.0.0
     * @param array $attrs
     * @return string
     */
    function shortcode($attrs)
    {
	$this->load($attrs['id']);
	/**
	 * Render list by shortcode.
	 * 
	 * @since 2.0.0
	 * @filter lfa_render_shortcode
	 * @param Content
	 * @param Shortcode attributes
	 */
	return apply_lfa_filters('lfa_list_render_shortcode', $this->get_render(), $attrs);
    }

    /**
     * Post.
     * 
     * @since 2.0.0
     * @return string
     */
    public function single_post()
    {
	$this->load(get_the_ID());
	/**
	 * Render list.
	 * 
	 * @since 2.0.0
	 * @filter lfa_list_render_post
	 * @param Content
	 * @param Shortcode attributes
	 */
	return apply_lfa_filters('lfa_list_render_post', $this->get_render());
    }

}
