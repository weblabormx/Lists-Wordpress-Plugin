<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Admin panel.
 * 
 * @since 2.0.0
 * @package ListForArticles\Admin
 */

Class LFA_Admin {

    /**
     * Initializing (register menu, enqueue required scripts).
     * 
     * @since 2.0.0
     * @return void
     */
    public function __construct()
    {
	// Register menus
	add_action('admin_menu', array( $this, 'register_menus' ));
	
	// Enqueue assets
	if ( isset($_GET['page']) && substr($_GET['page'], 0, 3) == 'lfa-' ):
	    add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
	endif;
	
	// Thickbox JS callback for media uploads
	if ( !empty($_GET['thickbox_callback']) && !empty($_GET['type']) && !empty($_GET['tab']) ):
	    add_filter('media_upload_form_url', array( $this, 'add_jscallback_param' ));
	    add_action('init', array( $this, 'check_head_js_callback' ));
	endif;
    }
    
    /**
     * Add Thickbox callback to form action.
     * 
     * @since 2.2.0
     * @param string $url
     * @return string
     */
    public function add_jscallback_param($url)
    {
	return add_query_arg('thickbox_callback', $_GET['thickbox_callback'], $url);
    }
    
    /**
     * Start output buffer capturing.
     * 
     * @since 2.2.0
     * @return void
     */
    public function check_head_js_callback()
    {
	ob_start(array( $this, 'check_footer_js_callback' ));
    }
    
    /**
     * Change Thickbox JS callback to a user-defined callback.
     * 
     * @since 2.2.0
     * @param string $buffer
     * @return string
     */
    public function check_footer_js_callback($buffer)
    {
	if ( !empty($buffer) ):
	    return str_replace('win.send_to_editor', 'win.' . $_GET['thickbox_callback'], $buffer);
	endif;
    }
    
    /**
     * Enqueue assets.
     * 
     * @since 2.0.0
     * @return void
     */
    public function enqueue_assets()
    {
	wp_enqueue_style('lfa-admin', LFA_CSS_ASSETS . 'admin-style.min.css', array(), LFA_VERSION);
    }

    /**
     * Register menus.
     * 
     * @since 2.0.0
     * @return void
     */
    public function register_menus()
    {

    }
    

    /**
     * Process installation and deletion of templates.
     * 
     * @since 2.0.0
     * @return void
     */
    public function process_templates()
    {
	if ( $_POST ):

	    /**
	     * Install templates
	     */
	    if ( $_FILES && wp_verify_nonce($_POST['upload-template-nonce'], 'upload-template') ):
		ListForArticles()->template->install();
	    endif;

	    /**
	     * Delete templates
	     */
	    if ( isset($_POST['template']) && is_array($templates = $_POST['template']) && wp_verify_nonce($_POST['delete-template-nonce'], 'delete-template') ):

		foreach ( $templates as $template ):
		    ListForArticles()->template->delete($template);
		endforeach;

		// Refresh the templates list
		ListForArticles()->template->fetch();

	    endif;
	endif;
    }

}
