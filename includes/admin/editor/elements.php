<?php if ( !defined('ABSPATH') ) exit; // Shhh     ?>
<!-- .choices -->
<div class="choices section <?php echo!isset($last_opened_tabs['choices']) ? 'collapsed' : '' ?>">
    <input type="checkbox" name="lfa_opened_tabs[choices]" class="tab-state" <?php checked(isset($last_opened_tabs['choices']), true); ?>>
    <h3 class="title"><?php _e('Elements', LFA_TD); ?></h3>
    <div class="content clearfix">

	<?php do_lfa_action('lfa_admin_editor_before_choices_content', $options); ?>
        <label>
            <input type="checkbox" name="lfa_options[override_votes]" class="override-votes"><?php _e('Override votes', LFA_TD); ?>
        </label>
        <hr>
        <ul class="choice-types">
            <li><button type="button" class="button" data-template="choice-text">Add</button></li>
	    <?php do_lfa_action('lfa_admin_editor_choice_types_buttons'); ?>
        </ul>
        <div class="choices-container show-votes-field">
	    <?php if ( isset($options->choices) ): ?>

		<?php foreach ( $options->choices as $index => $choice ): ?>
		    <?php if ( $choice->type == 'text' ): ?>
	    	    <div class="choice choice-text">
	    		<input type="hidden" name="lfa_options[choices][<?php echo $index; ?>][type]" value="text">
	    		<input type="text" placeholder="<?php _e('Votes', LFA_TD); ?>" name="lfa_options[choices][<?php echo $index; ?>][votes]" value="<?php echo esc_attr($choice->votes) ?>" class="votes-counter widefat">
	    		<input type="text" placeholder="<?php _e('Title', LFA_TD); ?>" name="lfa_options[choices][<?php echo $index; ?>][title]" value="<?php echo esc_attr($choice->title) ?>" class="widefat">
	    		<textarea name="lfa_options[choices][<?php echo $index; ?>][text]" class="widefat" style="height:200px;"><?php echo esc_attr($choice->text) ?></textarea>
	    		<input type="hidden" placeholder="<?php _e('Image', LFA_TD); ?>" name="lfa_options[choices][<?php echo $index; ?>][image]" value="<?php echo esc_attr($choice->image) ?>" class="widefat upload-holder">
	    		<input type="hidden" placeholder="<?php _e('Full Size URL', LFA_TD); ?>" name="lfa_options[choices][<?php echo $index; ?>][full]" value="<?php echo esc_attr($choice->full) ?>" class="widefat">
	    		<input type="hidden" placeholder="<?php _e('Label', LFA_TD); ?>" name="lfa_options[choices][<?php echo $index; ?>][label]" value="<?php echo esc_attr($choice->label) ?>" class="widefat">
			    <?php do_lfa_action("lfa_admin_editor_text_choice_fields", $choice, $index); ?>
	    		<input type="hidden" name="lfa_options[choices][<?php echo $index; ?>][last_index]" value="<?php echo $index; ?>">
	    		<ul class="choice-controllers">
	    			<li><button type="button" class="upload"><?php _e('upload', LFA_TD); ?></button></li>
	    		    <li><button type="button" class="move">&equiv;</button></li>
	    		    <li><button type="button" class="delete">&#10006;</button></li>
				<?php do_lfa_action("lfa_admin_editor_text_choice_buttons", $choice, $index); ?>
	    		</ul>
	    	    </div>
		    <?php else: ?>
			<?php $registered = has_filter("lfa_admin_editor_{$choice->type}_choice_fields") || has_filter("lfa_admin_editor_{$choice->type}_choice_buttons"); ?>
	    	    <div class="choice choice-<?php echo $choice->type; ?> <?php echo $registered ? '' : 'hide'; ?>">
	    		<input type="hidden" name="lfa_options[choices][<?php echo $index; ?>][type]" value="<?php echo $choice->type; ?>">
	    		<input type="text" placeholder="<?php _e('Votes', LFA_TD); ?>" name="lfa_options[choices][<?php echo $index; ?>][votes]" value="<?php echo esc_attr($choice->votes) ?>" class="votes-counter widefat">
			    <?php
			    if ( $registered ):
				do_lfa_action("lfa_admin_editor_{$choice->type}_choice_fields", $choice, $index);
			    else:
				// Recovery mode
				foreach ( $choice as $key => $value ):
				    if ( in_array($key, array( 'id', 'type', 'votes', 'votes_percentage' )) )
					continue;
				    // Array
				    if ( is_array($value) ):
					foreach ( $value as $subkey => $subvalue ):
					    ?>
			    		<textarea name="lfa_options[choices][<?php echo $index; ?>][<?php echo $key; ?>][<?php echo $subkey; ?>]"><?php echo esc_attr($subvalue); ?></textarea>
					    <?php
					endforeach;
				    // Single value
				    else:
					?>
					<textarea name="lfa_options[choices][<?php echo $index; ?>][<?php echo $key; ?>]"><?php echo esc_attr($value); ?></textarea>
				    <?php
				    endif;
				endforeach;
			    endif;
			    ?>
	    		<input type="hidden" name="lfa_options[choices][<?php echo $index; ?>][last_index]" value="<?php echo $index; ?>">
	    		<ul class="choice-controllers">
	    		    <li><button type="button" class="move">&equiv;</button></li>
	    		    <li><button type="button" class="delete">&#10006;</button></li>
			<?php do_lfa_action("lfa_admin_editor_{$choice->type}_choice_buttons", $choice, $index); ?>
	    		</ul>
	    	    </div>
		<?php endif; ?>
	    <?php endforeach; ?>
<?php endif; ?>
        </div>
<?php do_lfa_action('lfa_admin_editor_after_choices_content', $options); ?>
    </div>
</div>