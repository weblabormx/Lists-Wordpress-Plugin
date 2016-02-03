<?php
if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * List Widget.
 * 
 * @since 2.0.0
 * @package ListForArticles\Widget
 */

Class LFA_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     * 
     * @since 2.0.0
     * @return void
     */
    public function __construct()
    {
        parent::__construct(
                'listforarticles', // Base ID
                __('List - ListForArticles', LFA_TD), // Name
                array( 'description' => __('List widget', LFA_TD), ) // Args
        );
    }

    /**
     * Override _register to invoke template's functions.php file.
     * 
     * @since 2.0.0
     * @global string $pagenow
     * @return void
     */
    function _register()
    {
        global $pagenow;
        
        parent::_register();
        
        if ( in_array($pagenow , array('widgets.php', 'admin-ajax.php')) || !is_admin() ):
            
            $settings = parent::get_settings();
        
            if ( isset($settings[$this->number]['list_id']) ):
                
                if ( ListForArticles()->list->load($settings[$this->number]['list_id']) ):
                    ListForArticles()->list->unload();
                endif;
                
            endif;
            
        endif;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     * @since 2.0.0
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     * @return void
     */
    public function widget($args, $instance)
    {
        if ( ListForArticles()->list->load($instance['list_id']) ):
            /**
             * Filter widget title
             * 
             * @since 2.0.0
             * @filter widget_title
             * @param Widget title
             */
            $title = apply_lfa_filters('widget_title', $instance['title']);

            $args = apply_lfa_filters('lfa_widget_args', $args, $instance);

            echo $args['before_widget'];
            /**
             * Before widget content
             * 
             * @since 2.0.0
             * @action lfa_widget_before_content
             * @param Arguments
             * @param Instance
             */
            do_lfa_action('lfa_widget_before_content', $args, $instance);

            if ( !empty($title) ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            ListForArticles()->list->render();
            /**
             * After widget content
             * 
             * @since 2.0.0
             * @action lfa_widget_after_content
             * @param Arguments
             * @param Instance
             */
            do_lfa_action('lfa_widget_after_content', $args, $instance);
            echo $args['after_widget'];
        endif;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return void
     */
    public function form($instance)
    {
        $defaults = array( 'title' => __('List', LFA_TD), 'list_id' => 0 );
        $instance = wp_parse_args($instance, $defaults);
        
        ListForArticles()->list->load($instance['list_id']);
        /**
         * Before widget form.
         * 
         * @since 2.0.0
         * @action lfa_widget_before_form
         * @param Instance
         * @param Widget instance
         */
        do_lfa_action('lfa_widget_before_form', $instance, $this);
        ?>
        <p>
            <label><?php _e('Title:', LFA_TD); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
            </label>
        </p>
        <p>
            <label for="list_id"><?php _e('List', LFA_TD); ?></label>
            <br>
            <select name="<?php echo $this->get_field_name('list_id'); ?>" class="widefat">
                <?php foreach ( (array) get_posts('post_type=list&posts_per_page=-1') as $index => $list ): ?>
                    <option value="<?php echo $list->ID; ?>" <?php selected($instance['list_id'], $list->ID); ?>>
                        <?php echo $list->post_title; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
        /**
         * After widget form.
         * 
         * @since 2.0.0
         * @action lfa_widget_after_form
         * @param Instance
         * @param Widget instance
         */
        do_lfa_action('lfa_widget_after_form', $instance, $this);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        
        ListForArticles()->list->load($instance['list_id']);
        
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['list_id'] = (int) strip_tags($new_instance['list_id']);
        /**
         * Update widget options
         * 
         * @since 2.0.0
         * @filter lfa_widget_update
         * @param Instance
         * @param New instance
         * @param Old instance
         */
        return apply_lfa_filters('lfa_widget_update', $instance, $new_instance, $old_instance);
    }

}
