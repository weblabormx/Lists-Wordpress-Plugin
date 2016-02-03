<div class="updated fade">
    <p>
	<?php _e('There is an new update for ListForArticles plugin.', LFA_TD); ?>
	<a href="<?php echo LFA_WEBSITE; ?>?changelog&current=<?php echo LFA_VERSION; ?>&new=<?php echo $last_version; ?>" target="_blank"><?php _e('Check it out!', LFA_TD); ?></a> | 
	<a href="#" onclick="jQuery(this).parents('.updated').slideUp();jQuery.get('<?php echo admin_url('?lfa_dismiss_update=' . $last_version) ?>')">
	    <?php _e('Dismiss', LFA_TD); ?>
	</a>
    </p>
</div>