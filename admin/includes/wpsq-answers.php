<div class="my_meta_control">

    <?php 

        // get value of the correct answer
        $meta = get_post_meta( get_the_ID(), '_wpsq-answers', true );
        $selected = $meta ? $meta['answer_correct'] : null;


        // alphabetic array for the answers
        $alpha = range('a', 'z');

    ?>
 
    <a style="float:right;" href="#" class="dodelete-docs button"><?php echo __( 'Remove All', $this->plugin_slug ) ?></a>
 
    <p><?php _e( 'Add answers to the question and assign which answer is the right one', $this->plugin_slug ) ?></p>
 
    <?php while($mb->have_fields_and_multi('answers', array('length' => 1, 'limit' => 5) )): ?>
    <?php $mb->the_group_open(); ?>
 
        <?php $mb->the_field('answer'); ?>
        <strong class="answer-range"><?php echo $alpha[ $mb->get_the_index() ] ?>) </strong>
        <p>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="<?php _e( 'Enter your answer', $this->plugin_slug ) ?>"/>
        </p>
        <p>
            <label class="answer-radio" title="<?php _e( 'This is the right answer', $this->plugin_slug ) ?>">
                <!-- <input type="radio" name="answer_correct" value="<?php echo $mb->get_the_index() ?>"<?php echo $mb->is_value( $mb->get_the_index() )?' checked="checked"':''; ?>/> -->
                <input type="radio" name="_wpsq-answers[answer_correct]" value="<?php echo $mb->get_the_index() ?>"<?php echo ( $mb->get_the_index() == $selected )?' checked="checked"':''; ?>/>
                <span class="answer-check button dashicons dashicons-yes"></span>
            </label>

            <a href="#" class="dodelete button dashicons dashicons-no" title="<?php _e( 'Remove answer', $this->plugin_slug ) ?>"></a>
        </p>
 
    <?php $mb->the_group_close(); ?>
    <?php endwhile; ?>
 
    <p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-answers button button-primary"><?php _e( 'Add Answer', $this->plugin_slug ) ?></a></p>
 
</div>