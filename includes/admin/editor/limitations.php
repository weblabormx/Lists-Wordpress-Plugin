<?php if ( !defined('ABSPATH') ) exit; // Shhh  ?>
<!-- .limirations -->
<div class="limitations section <?php echo!isset($last_opened_tabs['limitations']) ? 'collapsed' : '' ?>">
    <input type="checkbox" name="lfa_opened_tabs[limitations]" class="tab-state" <?php checked(isset($last_opened_tabs['limitations']), true); ?>>
    <h3 class="title"><?php _e('Limitations', LFA_TD); ?></h3>
    <div class="content">
        
        <?php do_lfa_action('lfa_admin_editor_before_limitations_content', $options); ?>
        <p><?php _e('Prevent re-vote using', LFA_TD); ?></p>
        <p>
            <label>
                <input type="checkbox" name="lfa_options[limitations][revote][session]" value="1" <?php checked(true, isset($options->limitations->revote->session)); ?>> <?php _e('Sessions', LFA_TD) ?>
            </label>
            <label>
                <input type="checkbox" name="lfa_options[limitations][revote][cookies]" value="1" <?php checked(true, isset($options->limitations->revote->cookies)); ?> data-toggler="cookies-limitation"> <?php _e('Cookies', LFA_TD) ?>
            </label>
        </p>

        <p data-toggle="cookies-limitation" class="<?php echo isset($options->limitations->revote->cookies) ? '' : 'hide'; ?>">
            <label>
                <span><?php _e('Cookies timeout (minutes)', LFA_TD); ?></span>
                <input type="text" name="lfa_options[limitations][cookies][timeout]" value="<?php echo isset($options->limitations->cookies->timeout) ? $options->limitations->cookies->timeout : 43200; ?>">
            </label>
        </p>


        <?php do_lfa_action('lfa_admin_editor_after_limitations_content', $options); ?>
    </div>
</div>
<!-- /.limitations -->