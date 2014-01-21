<div class="wpsq-resolution_control">
 
    <p><?php _e( 'Insert here the resolution of the question.', $this->plugin_slug ) ?></p>
 
    <?php $mb->the_field('resolution'); ?>
        <div class="customEditor"><textarea name="<?php $mb->the_name(); ?>"><?php echo wp_richedit_pre($mb->get_the_value()); ?></textarea></div>

</div>