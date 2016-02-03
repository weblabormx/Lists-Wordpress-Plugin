<?php

/*
  Plugin Name: List for Articles
  Description: A plugin to put a list in an article
  Plugin URI: http://www.weblabor.mx
  Author: Carlos Escobar
  Version: 1.0
  Text Domain: listforarticles
  Domain Path: languages
 */

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * ListForArticles Singleton Bootstraper.
 * 
 * @since 2.0.0
 * @pakcage ListForArticles
 */

class ListForArticles {

    /**
     * Instance container.
     * @since 2.0.0
     * @access private
     * @type instance Instance.
     */
    private static $instance;

    /**
     * Get ListForArticles instance.
     * @since 2.0.0
     * @return instance Current instance.
     */
    public static function getInstance()
    {
        if ( !isset(self::$instance) && !( self::$instance instanceof ListForArticles ) ):

            session_start();

            self::$instance = new ListForArticles;
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->textdomain();
            self::$instance->hooks();

            self::$instance->request = new LFA_Request();
            self::$instance->list = new LFA_List();
            self::$instance->template = new LFA_Template();
            self::$instance->security = new LFA_Security();
            
            if ( is_admin() ):
                self::$instance->admin = new LFA_Admin();
                self::$instance->customizer = new LFA_List_Customizer();
                self::$instance->editor = new LFA_List_Editor();
            endif;

	    // Init Cache Compatibility
	    new LFA_Cache_Compatibility();
	    
            /**
             * Init
             * Init hook for ListForArticles
             * 
             * @since 2.0.0
             * @action lfa_init
             * @param Instance
             */
            do_action('lfa_init', self::$instance);

        endif;
        return self::$instance;
    }

    /**
     * Define useful constants.
     * 
     * @since 2.0.0
     * @return void
     */
    private function constants()
    {

        /**
         * Directory separator.
         * 
         * @since 2.0.0
         * @type string
         */
        if ( !defined('DS') )
            define('DS', DIRECTORY_SEPARATOR);

        /**
         * ListForArticles Lite
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_LITE', true);
	
        /**
         * ListForArticles text doamin
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_TD', 'listforarticles');

        /**
         * ListForArticles base directory path.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_PATH', plugin_dir_path(__FILE__));

        /**
         * ListForArticles base directory URL.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_URL', plugin_dir_url(__FILE__));

        /**
         * ListForArticles templates directory path.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_TEMPLATES_PATH', LFA_PATH . 'templates' . DS);

        /**
         * ListForArticles templates directory URL.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_TEMPLATES_URL', LFA_URL . 'templates/');

        /**
         * ListForArticles root file (this file).
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_ROOT_FILE', __FILE__);

        /**
         * ListForArticles assets directory path.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_ASSETS_PATH', LFA_PATH . 'assets' . DS);

        /**
         * ListForArticles assets directory URL.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_ASSETS_URL', LFA_URL . 'assets/');

        /**
         * ListForArticles JS assets directory URL.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_JS_ASSETS', LFA_ASSETS_URL . 'js/');

        /**
         * ListForArticles CSS assets directory URL.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_CSS_ASSETS', LFA_ASSETS_URL . 'css/');

        /**
         * ListForArticles images assets directory URL.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_IMAGES_ASSETS', LFA_ASSETS_URL . 'images/');

        /**
         * ListForArticles current version
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_VERSION', '2.2');

        /**
         * ListForArticles store URL
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_WEBSITE', 'http://listforarticles.com');
	
        /**
         * ListForArticles store URL
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_STORE', 'http://wpsto.re/plugins/total-list?store');

        /**
         * ListForArticles directory name.
         * 
         * @since 2.0.0
         * @type string
         */
        define('LFA_DIRNAME', dirname(plugin_basename(__FILE__)));
    }

    /**
     * Load ListForArticles textdomain.
     * 
     * @since 2.0.0
     * @return bool
     */
    public function textdomain()
    {
        return load_plugin_textdomain(LFA_TD, false, LFA_DIRNAME . '/languages/');
    }

    /**
     * Load required files (modules, templates ..).
     * 
     * @since 2.0.0
     * @global string $wp_version
     * @return void
     */
    private function includes()
    {
        global $wp_version;
        require_once( LFA_PATH . 'includes/post-type.php' );
        require_once( LFA_PATH . 'includes/list.helpers.php' );
        require_once( LFA_PATH . 'includes/helpers.php' );
        require_once( LFA_PATH . 'includes/template-tags.php' );

        require_once( LFA_PATH . 'includes/class-request.php' );
        require_once( LFA_PATH . 'includes/class-template.php' );
        require_once( LFA_PATH . 'includes/class-security.php' );
        require_once( LFA_PATH . 'includes/class-list.php' );
	   require_once( LFA_PATH . 'includes/class-widget.php' );
	   require_once( LFA_PATH . 'includes/class-cache-compatibility.php' );

        if ( is_admin() ):

            require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

            if ( version_compare($wp_version, '3.7', '>') ):
                require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader-skins.php' );
            endif;

            require_once( LFA_PATH . 'includes/class-installer.php' );
            require_once( LFA_PATH . 'includes/class-installer-skin.php' );
            require_once( LFA_PATH . 'includes/class-list-customizer.php' );
            require_once( LFA_PATH . 'includes/class-list-customizer-fields.php' );
            require_once( LFA_PATH . 'includes/class-list-editor.php' );
            require_once( LFA_PATH . 'includes/class-admin.php' );

        endif;
    }

    /**
     * Register ListForArticles widgets.
     * 
     * @since 2.0.0
     * @return void
     */
    public function widgets()
    {
        register_widget('LFA_Widget');
    }
    
    /**
     * Register hooks (actions & filters).
     * 
     * @since 2.0.0
     * @return void
     */
    private function hooks()
    {
        // Activation
        register_activation_hook(__FILE__, array( $this, 'activate' ));
        add_action('admin_init', array( $this, 'redirect_about_page' ), 1);
        // Widget
        add_action('widgets_init', array( $this, 'widgets' ));
        // Capture actions
        add_action('wp', array( $this, 'capture_action' ), 11);
        // Post-type
        add_action('init', 'lfa_register_post_type');
        add_filter('post_updated_messages', 'lfa_update_messages');
    }

    /**
     * Capture actions from POST, GET and AJAX
     * 
     * @since 2.0.0
     * @return void
     */
    public function capture_action()
    {
        /**
         * AJAX
         */
        if ( !empty($_SERVER['HTLFA_X_REQUESTED_WITH']) && strtolower($_SERVER['HTLFA_X_REQUESTED_WITH']) == 'xmlhttprequest' ):
            if ( isset($_REQUEST['lfa_action']) ):
                /**
                 * ListForArticles Ajax request
                 * 
                 * @since 2.0.0
                 * @type string
                 */
                define('LFA_AJAX', true);
                /**
                 * Capture ajax requests
                 * 
                 * @since 2.0.0
                 * @action lfa_capture_ajax_{$_REQUEST['lfa_action']}
                 */
                do_action("lfa_capture_ajax_{$_REQUEST['lfa_action']}");
            endif;
        endif;

        /**
         * POST & GET
         */
        if ( isset($_POST['lfa_action']) ):
            /**
             * Capture post requests
             * 
             * @since 2.0.0
             * @action lfa_capture_ajax_{$_POST['lfa_action']}
             */
            do_action("lfa_capture_post_{$_POST['lfa_action']}");
        endif;
        if ( isset($_GET['lfa_action']) ):
            /**
             * Capture get requests
             * 
             * @since 2.0.0
             * @action lfa_capture_ajax_{$_GET['lfa_action']}
             */
            do_action("lfa_capture_get_{$_GET['lfa_action']}");
        endif;
    }

    /**
     * Activation.
     * 
     * @since 2.0.0
     * @return void
     */
    public function activate()
    {
        lfa_register_post_type();
        flush_rewrite_rules();
        set_transient('listforarticles_about_page_activated', 1, 30);
    }

    /**
     * Redirect to about page if is a fresh installation
     * 
     * @since 2.0.0
     * @return void
     */
    public function redirect_about_page()
    {
        if ( !current_user_can('manage_options') )
            return;

        if ( !get_transient('listforarticles_about_page_activated') )
            return;

        delete_transient('listforarticles_about_page_activated');
        wp_safe_redirect(admin_url('edit.php?post_type=list'));
        exit;
    }

}

/**
 * Get instance.
 * 
 * @package ListForArticles
 * @since 2.0.0
 * @return instance Current instance of ListForArticles.
 */
function ListForArticles()
{
    return ListForArticles::getInstance();
}

/**
 * Bootstrap, and let the fun begin.
 */
ListForArticles();
