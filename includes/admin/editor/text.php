<?php if ( !defined('ABSPATH') ) exit; // Shhh ?>
<!-- .question -->
<div class="question section <?php echo !isset($last_opened_tabs['question']) ? 'collapsed' : '' ?>" style="display:none">
        <input type="checkbox" name="lfa_opened_tabs[question]" class="tab-state" <?php checked(isset($last_opened_tabs['question']), true); ?>>
	<h3 class="title"><?php _e('Text', LFA_TD); ?></h3>
	<div class="content">
            <?php do_lfa_action('lfa_admin_editor_before_question_content', $options); ?>
            <div id="wp-content-editor-container" class="wp-editor-container">
            	<?php $settings = array( 'textarea_name' => 'lfa_options[question]' );
            	wp_editor( isset($options->question) ? esc_attr($options->question) : '', 'content', $settings );  ?>
			</div>
            <?php do_lfa_action('lfa_admin_editor_after_question_content', $options); ?>
	</div>
</div>
<!-- /.question -->