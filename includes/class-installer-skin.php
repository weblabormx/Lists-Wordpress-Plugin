<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * Installer Skin.
 * 
 * @since 2.0.0
 * @package ListForArticles\Installer\Skin
 */

class LFA_Installer_Skin extends Theme_Installer_Skin {

    /**
     * Return URL.
     * @since 2.0.0
     * @return void
     */
    public function after()
    {
	if ( $this->upgrader->type == 'template' ):
	    $this->feedback('<a href="' . self_admin_url('edit.php?post_type=list&page=lfa-templates-manager') . '" title="' . esc_attr__('Return to Templates Installer', LFA_TD) . '" target="_parent">' . __('Return to Templates', LFA_TD) . '</a>');
	else:
	    $this->feedback('<a href="' . self_admin_url('edit.php?post_type=list&page=lfa-addons-manager') . '" title="' . esc_attr__('Return to Addons Installer', LFA_TD) . '" target="_parent">' . __('Return to Addons', LFA_TD) . '</a>');
	endif;
    }

}
