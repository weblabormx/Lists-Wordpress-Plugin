<?php if ( !defined('ABSPATH') ) exit; // Shhh  ?>
<!-- .shortcode -->
<div class="shortcode section <?php echo!isset($last_opened_tabs['shortcode']) ? 'collapsed' : '' ?>">
    <input type="checkbox" name="lfa_opened_tabs[shortcode]" class="tab-state" <?php checked(isset($last_opened_tabs['shortcode']), true); ?>>
    <h3 class="title"><?php _e('Shortcode', LFA_TD); ?></h3>
    <div class="content">
        <?php do_lfa_action('lfa_admin_editor_before_shortcode_content', $options); ?>
        <input type="text" class="widefat" value="[list-for-articles id=<?php echo get_the_ID(); ?>]" readonly="readonly">
        <?php do_lfa_action('lfa_admin_editor_after_shortcode_content', $options); ?>
    </div>
</div>
<!-- /.shortcode -->