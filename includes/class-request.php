<?php

if ( !defined('ABSPATH') )
    exit; // Shhh


/**
 * Process vote and results requests.
 * 
 * @since 2.0.0
 * @package ListForArticles\Request
 */

Class LFA_Request {

    /**
     * Capture actions.
     * 
     * @since 2.0.0
     * @return void
     */
    public function __construct()
    {
	add_action('lfa_capture_ajax_vote', array( $this, 'ajax_vote' ));
	add_action('lfa_capture_ajax_back', array( $this, 'ajax_back' ));
	add_action('lfa_capture_ajax_results', array( $this, 'ajax_results' ));

	add_action('lfa_capture_post_results', array( $this, 'results' ));
	add_action('lfa_capture_post_vote', array( $this, 'vote' ));
	add_action('lfa_capture_get_preview', array( $this, 'preview' ));

    }

    /**
     * Preview.
     * 
     * @since 2.0.0
     * @return void
     */
    public function preview()
    {
	// Prevent preview from unpreviliged users
	if ( !current_user_can('edit_posts') )
	    wp_die(__('You cannot preview this list. Login first.', LFA_TD));

	ListForArticles()->list->load();
	exit(include_once( LFA_PATH . 'includes/admin/editor/preview.php' ));
    }

    /**
     * Vote.
     * 
     * @since 2.0.0
     * @return void
     */
    public function vote()
    {
    	global $list;
    	if ( ListForArticles()->list->load($_REQUEST['lfa_list_id']) ):
            if ( ListForArticles()->security->has_ability_to_vote($_POST['lfa_choices']) && !empty($_POST['lfa_choices']) && !empty($_POST['lfa_votes']) ):
        		$voted_for = (array) $_POST['lfa_choices'];
        		// Save votes
        		if ( lfa_save_list_votes($list, $_POST['lfa_votes'], $voted_for) ):
        		    // Refresh choices
        		    lfa_prepare_list_choices($list);
        		    // Lock vote ability
        		    ListForArticles()->security->lock_vote_ability($_POST['lfa_choices']);
        		endif;

    	    endif;

    	    $list->skip_to_results = true;

    	endif;
    }

    /**
     * Results.
     * 
     * @since 2.0.0
     * @return void
     */
    public function results()
    {
	global $list;

	if ( ListForArticles()->list->load($_POST['lfa_list_id']) ):

	    if ( !is_list_results_locked() ):
		$list->skip_to_results = true;
	    endif;

	endif;
    }

    /**
     * Ajax vote.
     * 
     * @since 2.0.0
     * @return void
     */
    public function ajax_vote()
    {
    	$this->vote();
    }

    /**
     * Ajax results.
     * 
     * @since 2.0.0
     * @return void
     */
    public function ajax_results()
    {
    	$this->results();
    	//exit(ListForArticles()->list->get_render(true));
    }

    /**
     * Ajax back.
     * 
     * @since 2.0.0
     * @return void
     */
    public function ajax_back()
    {
    	if ( ListForArticles()->list->load($_REQUEST['lfa_list_id']) ):
    	    //exit(ListForArticles()->list->get_render(true));
    	endif;
    }

}
