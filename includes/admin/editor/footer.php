<?php if ( !defined('ABSPATH') ) exit; // Shhh ?>
<?php do_lfa_action( 'lfa_admin_editor_footer', $options); ?>

<script type="text/template" class="choice-template" id="choice-text">
    <div class="choice choice-text">
        <input type="hidden" name="lfa_options[choices][{{index}}][type]" value="text">
        <input type="text" placeholder="<?php _e( 'Votes', LFA_TD ); ?>" name="lfa_options[choices][{{index}}][votes]" class="votes-counter widefat">
        <input type="text" placeholder="<?php _e('Titulo', LFA_TD); ?>" name="lfa_options[choices][{{index}}][title]" class="widefat">
        <textarea name="lfa_options[choices][{{index}}][text]" class="widefat"  style="height:200px;"></textarea>
        <input type="hidden" placeholder="<?php _e( 'Image', LFA_TD ); ?>" name="lfa_options[choices][{{index}}][image]" class="widefat upload-holder">
        <input type="hidden" placeholder="<?php _e( 'Full Size URL', LFA_TD ); ?>" name="lfa_options[choices][{{index}}][full]" class="widefat">
        <input type="hidden" placeholder="<?php _e( 'Label', LFA_TD ); ?>" name="lfa_options[choices][{{index}}][label]" class="widefat">
	<?php do_lfa_action("lfa_admin_editor_text_choice_fields_template"); ?>
        <ul class="choice-controllers">
                <li><button type="button" class="upload"><?php _e( 'upload', LFA_TD ); ?></button></li>
                <li><button type="button" class="move">&equiv;</button></li>
                <li><button type="button" class="delete">&#10006;</button></li>
		<?php do_lfa_action("lfa_admin_editor_text_choice_buttons_template"); ?>
        </ul>
    </div>
</script>
<?php do_lfa_action('lfa_admin_editor_choice_types_templates', $options); ?>

</div>
<!-- /.lfa-wrapper -->