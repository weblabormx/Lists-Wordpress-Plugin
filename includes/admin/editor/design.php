<?php if ( !defined('ABSPATH') ) exit; // Shhh   ?>
<!-- .design -->
<div class="design section <?php echo!isset($last_opened_tabs['design']) ? 'collapsed' : '' ?>" style="display:none;">
    <input type="checkbox" name="lfa_opened_tabs[design]" class="tab-state" <?php checked(isset($last_opened_tabs['design']), true); ?>>
    <h3 class="title"><?php _e('Design', LFA_TD); ?></h3>
    <div class="content">

	<?php do_lfa_action('lfa_admin_editor_before_design_content', $options); ?>

	<?php
	$templates = ListForArticles()->template->available;
	$presets = ListForArticles()->template->presets;
	$current_preset = isset($options->template->name) ? $options->template->name : 'default';
	$current_preset .= '-preset-';
	$current_preset .= isset($options->template->preset->name) ? $options->template->preset->name : 'default';
	?>

        <div class="customizer">
            <div class="settings-sections">
                <div class="settings-section toggled" data-toggle="section-current-settings">
                    <p>
                        <label class="widefat">
                            <span><?php _e('Presets', LFA_TD); ?></span>
                            <select name="lfa_options[template][preset][name]" class="widefat">
				<?php foreach ( $templates as $slug => $template ): ?>
                                    <optgroup label="<?php echo $template->name; ?>">
					<?php if ( isset($presets->{$slug}) ): ?>
					    <?php foreach ( $presets->{$slug} as $name => $preset ): $preset_id = $slug . '-preset-' . $name; ?>
	    				    <option value="<?php echo $preset_id; ?>" <?php selected($preset_id, $current_preset); ?>><?php echo $name; ?></option>
					    <?php endforeach; ?>
					<?php endif; ?>
                                    </optgroup>
				<?php endforeach; ?>
                            </select>
                        </label>
                        <input name="lfa_options[template][preset][load]" value="" type="hidden">
                    </p>
                    <hr>
                    <p>
                        <button type="submit" class="button button-primary" name="lfa_options[template][preset][new]"><?php _e('Save as', LFA_TD); ?></button>
                        <button type="submit" class="button" name="lfa_options[template][preset][delete]">
			    <?php
			    if ( isset($options->template->preset->name) &&
				    isset($list->template->presets[$options->template->preset->name]) ):
				_e('Reset', LFA_TD);
			    else:
				_e('Delete', LFA_TD);
			    endif;
			    ?>
                        </button>
                    </p>

                </div>
		<?php if ( isset($list->template->settings['sections']) ):
			foreach ( $list->template->settings['sections'] as $section_id => $section ): ?>
			<a href="#" class="" data-toggler="section-<?php echo $section_id; ?>-fields"><?php echo $section['label']; ?></a>
			<div class="hide settings-section need-refresh" data-toggle="section-<?php echo $section_id; ?>-fields">
			    <?php
			    unset($section['label']);
			    foreach ( $section['fields'] as $field_id => $field ):
			    ?>
	    		    <div class="settings-field-container">
				    <?php
				    if ( isset($options->template->preset->settings->{$section_id}->{$field_id}) ):
					$field['value'] = $options->template->preset->settings->{$section_id}->{$field_id};
					if ( isset($field['states']) && is_array($field['states']) ):
					    foreach ( $field['states'] as $id => $state ):
						if ( isset($options->template->preset->settings->{$section_id}->{$field_id . ':' . $id}) ):
						    $field['states'][$id]['value'] = $options->template->preset->settings->{$section_id}->{$field_id . ':' . $id};
						endif;
					    endforeach;
					endif;
				    endif;
				    ?>
				    <?php echo ListForArticles()->customizer->field($section_id, $field_id, (object) $field); ?>
	    		    </div>
			    <?php endforeach; ?>
			</div>
		<?php
			endforeach;
		    endif;
		?>
            </div>
            <div class="preview-pane">
                <iframe height="100%" width="100%" src="<?php echo home_url('?lfa_action=preview&lfa_list_id=' . get_the_ID()); ?>"></iframe>
            </div>
        </div>

	<?php do_lfa_action('lfa_admin_editor_after_design_content', $options); ?>
    </div>
</div>
<!-- /.design -->