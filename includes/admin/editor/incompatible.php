<?php if ( !defined('ABSPATH') ) exit; // Shhh ?>
<div class="update-nag"><?php printf( __('This list isn\'t compatible with your current version of ListForArticles. Please <a href="%s">upgrade your lists</a>.', LFA_TD), admin_url('edit.php?post_type=list&page=lfa-tools')); ?></div>