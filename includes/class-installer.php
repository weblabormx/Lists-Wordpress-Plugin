<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Addons & Templates Installer.
 * 
 * @since 2.0.0
 * @package ListForArticles\Installer
 */

class LFA_Installer extends Theme_Upgrader {

    /**
     * Type of installation (addon or template).
     * 
     * @since 2.0.0
     * @access public
     * @type string
     */
    public $type;

    /**
     * Initializing.
     * 
     * @param object $skin Skin object
     * @param string $type Installation type
     * @return void
     */
    public function __construct($skin = null, $type = 'template')
    {
	if ( null == $skin ):
	    $this->skin = new WP_Upgrader_Skin();
	else:
	    $this->skin = $skin;
	endif;

	$this->type = $type;
    }

    /**
     * Installation strings.
     * 
     * @since 2.0.0
     * @return void
     */
    public function install_strings()
    {
	if ( $this->type == 'template' ):
	    $this->strings['no_package'] = __('Install package not available.', LFA_TD);
	    $this->strings['unpack_package'] = __('Unpacking the package&#8230;', LFA_TD);
	    $this->strings['installing_package'] = __('Installing the template&#8230;', LFA_TD);
	    $this->strings['no_files'] = __('The template contains no files.', LFA_TD);
	    $this->strings['process_failed'] = __('Template install failed.', LFA_TD);
	    $this->strings['process_success'] = __('Template installed successfully.', LFA_TD);
	    /* translators: 1: template name, 2: version */
	    $this->strings['process_success_specific'] = __('Successfully installed the template <strong>%1$s %2$s</strong>.', LFA_TD);
	    /* translators: 1: template name, 2: version */
	    $this->strings['parent_template_prepare_install'] = __('Preparing to install <strong>%1$s %2$s</strong>&#8230;', LFA_TD);
	elseif ( $this->type == 'addon' ):
	    $this->strings['no_package'] = __('Install package not available.', LFA_TD);
	    $this->strings['unpack_package'] = __('Unpacking the package&#8230;', LFA_TD);
	    $this->strings['installing_package'] = __('Installing the addon&#8230;', LFA_TD);
	    $this->strings['no_files'] = __('The addon contains no files.', LFA_TD);
	    $this->strings['process_failed'] = __('Addon install failed.', LFA_TD);
	    $this->strings['process_success'] = __('Addon installed successfully.', LFA_TD);
	    /* translators: 1: template name, 2: version */
	    $this->strings['process_success_specific'] = __('Successfully installed the addon <strong>%1$s %2$s</strong>.', LFA_TD);
	    /* translators: 1: template name, 2: version */
	    $this->strings['parent_template_prepare_install'] = __('Preparing to install <strong>%1$s %2$s</strong>&#8230;', LFA_TD);
	endif;
    }

    /**
     * Install a template or an addon.
     * 
     * @global array $wp_theme_directories Themes directories
     * @param object $package Uploaded package object
     * @return \WP_Error|boolean
     */
    public function install($package, $args = array())
    {

	global $wp_theme_directories;
	$wp_theme_directories[] = ($this->type == 'template' ? LFA_TEMPLATES_PATH : LFA_ADDONS_PATH);

	$defaults = array(
	    'clear_update_cache' => true,
	);

	$this->init();
	$this->install_strings();

	add_filter('upgrader_source_selection', array( $this, 'check_package' ));

	$this->run(array(
	    'package' => $package,
	    'destination' => ($this->type == 'template' ? LFA_TEMPLATES_PATH : LFA_ADDONS_PATH),
	    'clear_destination' => false,
	    'clear_working' => true,
	    'hook_extra' => array(
		'type' => $this->type,
		'action' => 'install',
	    ),
	));

	remove_filter('upgrader_source_selection', array( $this, 'check_package' ));

	if ( !$this->result || is_wp_error($this->result) )
	    return $this->result;

	return true;
    }

    /**
     * Check package validity.
     * 
     * @global object $wp_filesystem WP Filesystem object
     * @param object $source Path to uploaded package
     * @return \WP_Error
     */
    public function check_package($source)
    {
	global $wp_filesystem;

	if ( is_wp_error($source) )
	    return $source;

	// Check the folder contains a valid package
	$working_directory = str_replace($wp_filesystem->wp_content_dir(), trailingslashit(WP_CONTENT_DIR), $source);

	if ( !is_dir($working_directory) ): // Sanity check, if the above fails, lets not prevent installation.
	    return $source;
	endif;

	if ( $this->type == 'template' ):

	    // A proper archive should have a style.css file in the single subdirectory
	    if ( !file_exists($working_directory . 'style.css') ):
		return new WP_Error('incompatible_archive_template_no_style', $this->strings['incompatible_archive'], __('The template is missing the <code>style.css</code> stylesheet.', LFA_TD));
	    endif;

	    $info = get_file_data($working_directory . 'style.css', array( 'name' => 'Template Name' ));

	    if ( empty($info['name']) ):
		return new WP_Error('incompatible_archive_template_no_name', $this->strings['incompatible_archive'], __("The <code>style.css</code> stylesheet doesn't contain a valid template header.", LFA_TD));
	    endif;

	    // If it's not a child template, it must have at least an vote.php and results.php to be legit.
	    if ( empty($info['Template']) && (!file_exists($working_directory . 'vote.php') || !file_exists($working_directory . 'results.php')) ):
		return new WP_Error('incompatible_archive_template_no_vote', $this->strings['incompatible_archive'], __('The template is missing the <code>vote.php</code> or <code>results.php</code> file.', LFA_TD));
	    endif;

	elseif ( $this->type == 'addon' ):

	    // A proper archive should have a addon.php file in the single subdirectory
	    if ( !file_exists($working_directory . 'addon.php') ):
		return new WP_Error('incompatible_archive_no_addon_file', $this->strings['incompatible_archive'], __('The addon is missing the <code>addon.php</code> essential file.', LFA_TD));
	    endif;

	    $info = get_file_data($working_directory . 'addon.php', array( 'name' => 'Addon Name' ));

	    if ( empty($info['name']) ):
		return new WP_Error('incompatible_archive_addon_no_name', $this->strings['incompatible_archive'], __("The <code>addon.php</code> file doesn't contain a valid addon header.", LFA_TD));
	    endif;

	    if ( empty($info['name']) ):
		return new WP_Error('incompatible_archive_addon_no_required_version', $this->strings['incompatible_archive'], __("The <code>addon.php</code> file doesn't contain a minimum required version.", LFA_TD));
	    endif;

	    if ( !empty($info['name']) && version_compare($info['required'], LFA_VERSION, '>') ):
		return new WP_Error('incompatible_archive_addon_incompatible_version', $this->strings['incompatible_archive'], sprintf(__("This addon require ListForArticles version %s or higher", LFA_TD), $info['required']));
	    endif;

	endif;

	return $source;
    }

    /**
     * Install package.
     * 
     * @global object $wp_filesystem WP Filesystem object
     * @global array $wp_theme_directories Themes directories
     * @param array $args Other arguments
     * @return \WP_Error|string
     */
    public function install_package($args = array())
    {
	global $wp_filesystem, $wp_theme_directories;

	$defaults = array(
	    'source' => '', // Please always pass this
	    'destination' => '', // and this
	    'clear_destination' => false,
	    'clear_working' => false,
	    'abort_if_destination_exists' => true,
	    'hook_extra' => array()
	);

	$args = wp_parse_args($args, $defaults);
	extract($args);

	@set_time_limit(300);

	if ( empty($source) || empty($destination) )
	    return new WP_Error('bad_request', $this->strings['bad_request']);

	$this->skin->feedback('installing_package');

	/**
	 * Filter the install response before the installation has started.
	 *
	 * Returning a truthy value, or one that could be evaluated as a WP_Error
	 * will effectively short-circuit the installation, returning that value
	 * instead.
	 *
	 * @since 2.8.0
	 *
	 * @param bool|WP_Error $response   Response.
	 * @param array         $hook_extra Extra arguments passed to hooked filters.
	 */
	$res = apply_filters('upgrader_pre_install', true, $hook_extra);
	if ( is_wp_error($res) )
	    return $res;

	//Retain the Original source and destinations
	$remote_source = $source;
	$local_destination = $destination;

	$source_files = array_keys($wp_filesystem->dirlist($remote_source));
	$remote_destination = $wp_filesystem->find_folder($local_destination);

	//Locate which directory to copy to the new folder, This is based on the actual folder holding the files.
	if ( 1 == count($source_files) && $wp_filesystem->is_dir(trailingslashit($source) . $source_files[0] . '/') ) //Only one folder? Then we want its contents.
	    $source = trailingslashit($source) . trailingslashit($source_files[0]);
	elseif ( count($source_files) == 0 )
	    return new WP_Error('incompatible_archive_empty', $this->strings['incompatible_archive'], $this->strings['no_files']); // There are no files?
	else //It's only a single file, the upgrader will use the foldername of this file as the destination folder. foldername is based on zip filename.
	    $source = trailingslashit($source);

	/**
	 * Filter the source file location for the upgrade package.
	 *
	 * @since 2.8.0
	 *
	 * @param string      $source        File source location.
	 * @param string      $remote_source Remove file source location.
	 * @param WP_Upgrader $this          WP_Upgrader instance.
	 */
	$source = apply_filters('upgrader_source_selection', $source, $remote_source, $this);
	if ( is_wp_error($source) )
	    return $source;

	//Has the source location changed? If so, we need a new source_files list.
	if ( $source !== $remote_source )
	    $source_files = array_keys($wp_filesystem->dirlist($source));

	// Protection against deleting files in any important base directories.
	// Theme_Upgrader & Plugin_Upgrader also trigger this, as they pass the destination directory (WP_PLUGIN_DIR / wp-content/themes)
	// intending to copy the directory into the directory, whilst they pass the source as the actual files to copy.
	$protected_directories = array( ABSPATH, WP_CONTENT_DIR, WP_PLUGIN_DIR, WP_CONTENT_DIR . '/themes' );
	if ( is_array($wp_theme_directories) )
	    $protected_directories = array_merge($protected_directories, $wp_theme_directories);
	if ( in_array($destination, $protected_directories) ) {
	    $remote_destination = trailingslashit($remote_destination) . trailingslashit(basename($source));
	    $destination = trailingslashit($destination) . trailingslashit(basename($source));
	}

	if ( $clear_destination ) {
	    //We're going to clear the destination if there's something there
	    $this->skin->feedback('remove_old');
	    $removed = true;
	    if ( $wp_filesystem->exists($remote_destination) )
		$removed = $wp_filesystem->delete($remote_destination, true);

	    /**
	     * Filter whether the upgrader cleared the destination.
	     *
	     * @since 2.8.0
	     *
	     * @param bool   $removed            Whether the destination was cleared.
	     * @param string $local_destination  The local package destination.
	     * @param string $remote_destination The remote package destination.
	     * @param array  $hook_extra         Extra arguments passed to hooked filters.
	     */
	    $removed = apply_filters('upgrader_clear_destination', $removed, $local_destination, $remote_destination, $hook_extra);

	    if ( is_wp_error($removed) )
		return $removed;
	    else if ( !$removed )
		return new WP_Error('remove_old_failed', $this->strings['remove_old_failed']);
	} elseif ( $abort_if_destination_exists && $wp_filesystem->exists($remote_destination) ) {
	    //If we're not clearing the destination folder and something exists there already, Bail.
	    //But first check to see if there are actually any files in the folder.
	    $_files = $wp_filesystem->dirlist($remote_destination);
	    if ( !empty($_files) ) {
		$wp_filesystem->delete($remote_source, true); //Clear out the source files.
		return new WP_Error('folder_exists', $this->strings['folder_exists'], $remote_destination);
	    }
	}

	//Create destination if needed
	if ( !$wp_filesystem->exists($remote_destination) )
	    if ( !$wp_filesystem->mkdir($remote_destination, FS_CHMOD_DIR) )
		return new WP_Error('mkdir_failed_destination', $this->strings['mkdir_failed'], $remote_destination);

	// Copy new version of item into place.
	$result = copy_dir($source, $remote_destination);
	if ( is_wp_error($result) ) {
	    if ( $clear_working )
		$wp_filesystem->delete($remote_source, true);
	    return $result;
	}

	//Clear the Working folder?
	if ( $clear_working )
	    $wp_filesystem->delete($remote_source, true);

	$destination_name = basename(str_replace($local_destination, '', $destination));
	if ( '.' == $destination_name )
	    $destination_name = '';

	$this->result = compact('local_source', 'source', 'source_name', 'source_files', 'destination', 'destination_name', 'local_destination', 'remote_destination', 'clear_destination', 'delete_source_dir');

	/**
	 * Filter the install response after the installation has finished.
	 *
	 * @since 2.8.0
	 *
	 * @param bool  $response   Install response.
	 * @param array $hook_extra Extra arguments passed to hooked filters.
	 * @param array $result     Installation result data.
	 */
	$res = apply_filters('upgrader_post_install', true, $hook_extra, $this->result);

	if ( is_wp_error($res) ) {
	    $this->result = $res;
	    return $res;
	}

	//Bombard the calling function will all the info which we've just used.
	return $this->result;
    }

    public function current_after($return, $theme)
    {
	
    }

    public function bulk_upgrade($language_updates = array(), $args = array())
    {
	
    }

    public function upgrade($update = false, $args = array())
    {
	
    }

}
