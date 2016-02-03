<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Anti-cheating and limitations
 * 
 * @since 2.0.0
 * @package ListForArticles\Security
 */

Class LFA_Security {

    /**
     * Current IP.
     * 
     * @since 2.0.0
     * @access public
     * @type string
     */
    public $ip;

    /**
     * Current IP Range (192.193.194.***).
     * 
     * @since 2.0.0
     * @access public
     * @type string
     */
    public $ip_range;

    /**
     * Type of current IP.
     * 
     * @since 2.0.0
     * @access public
     * @type bool
     */
    public $is_ipv6;

    /**
     * Assign IP.
     * 
     * @since 2.0.0
     * @return void
     */
    public function __construct()
    {
	
    }

    /**
     * Check vote ability.
     * 
     * @since 2.0.0
     * @global object $list List object
     * @return bool
     */
    public function has_ability_to_vote($optionid)
    {

	$session_layer = $this->is_layer_enabled('session') && $this->check_session_layer($optionid);
	$cookies_layer = $this->is_layer_enabled('cookies') && $this->check_cookies_layer($optionid);

	if ( $session_layer || $cookies_layer)
	    return (bool) apply_lfa_filters('lfa_security_vote_ability', false);
	/**
	 * Vote ability.
	 * 
	 * @since 2.0.0
	 * @filter lfa_security_vote_ability
	 * @param Current ability
	 */
	return (bool) apply_lfa_filters('lfa_security_vote_ability', true);
    }

    /**
     * Lock vote ability using enabled layers.
     * 
     * @since 2.0.0
     * @return void
     */
    public function lock_vote_ability($optionid)
    {
	// Session
	if ( $this->is_layer_enabled('session') ):
	    $this->add_session_layer($optionid);
	endif;

	// Cookies
	if ( $this->is_layer_enabled('cookies') ):
	    $this->add_cookies_layer($optionid);
	endif;

	/**
	 * Lock vote ability.
	 * 
	 * @since 2.0.0
	 * @action lfa_security_lock_vote_ability
	 */
	do_lfa_action('lfa_security_lock_vote_ability');
    }

    /**
     * Session layer.
     * 
     * @since 2.0.0
     * @global object $list
     * @return void
     */
    public function add_session_layer($optionid)
    {
	$_SESSION[$optionid] = true;
	/**
	 * Session lock.
	 * 
	 * @since 2.0.0
	 * @action lfa_security_lock_vote_by_session
	 * @param Special ID
	 */
	do_lfa_action('lfa_security_lock_vote_by_session', $optionid);
    }

    /**
     * Cookies layer.
     * 
     * @since 2.0.0
     * @global object $list
     * @return void
     */
    public function add_cookies_layer($optionid)
    {
	global $list;
	setcookie('wp_' . md5($optionid), true, time() + (MINUTE_IN_SECONDS * intval($list->limitations->cookies->timeout)), COOKIEPATH, COOKIE_DOMAIN);
	/**
	 * Cookies lock
	 * 
	 * @since 2.0.0
	 * @action lfa_security_lock_vote_by_cookies
	 * @param Special ID
	 * @param Timeout
	 */
	do_lfa_action('lfa_security_lock_vote_by_cookies', $optionid, intval($list->limitations->cookies->timeout));
    }

    /**
     * Check layer if enabled.
     * 
     * @since 2.0.0
     * @global object $list
     * @param string $layer Layer name
     * @return bool
     */
    public function is_layer_enabled($layer)
    {
	global $list;
	/**
	 * Security layer check.
	 * 
	 * @since 2.0.0
	 * @filter lfa_security_layer_enabled
	 * @param State
	 * @param Layer name
	 */
	return apply_lfa_filters('lfa_security_layer_enabled', isset($list->limitations->revote->{$layer}), $layer);
    }

    /**
     * Check session layer.
     * 
     * @since 2.0.0
     * @global object $list
     * @return bool
     */
    public function check_session_layer($optionid)
    {
	/**
	 * Session layer check.
	 * 
	 * @since 2.0.0
	 * @filter lfa_security_check_session_layer
	 * @param State
	 * @param Special ID
	 */
	return apply_lfa_filters('lfa_security_check_session_layer', isset($_SESSION[$optionid]), $optionid);
    }

    /**
     * Check cookies layer.
     * 
     * @since 2.0.0
     * @global object $list
     * @return boold
     */
    public function check_cookies_layer($optionid)
    {
	/**
	 * Cookies layer check.
	 * 
	 * @since 2.0.0
	 * @filter lfa_security_check_cookies_layer
	 * @param State
	 * @param Special ID
	 */
	return apply_lfa_filters('lfa_security_check_cookies_layer', isset($_COOKIE['wp_' . md5($optionid)]), $optionid);
    }

}
