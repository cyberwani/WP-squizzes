<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   WP_Squizzes
 * @author    Diego de Oliveira <diego@favolla.com.br>
 * @link      https://github.com/diegoliv/WP-squizzes
 * @copyright 2013 Diego de Oliveira
 */

	// get options
	$options = get_option( 'wpsq-general-options' );

	$min = $options['min_questions'] ? $options['min_questions'] : 1;
	$max = $options['max_questions'] ? $options['max_questions'] : 30;

?>

<?php do_action('wpsq-template-before-quizzes'); ?>

<div id="wpsq-quizzes">
	<?php do_action('wpsq-template-before-title'); ?>

	<?php if( $options['quiz-system-title'] ): ?>
		<h1 id="wpsq-quizzes-title"><?php echo $options['quiz-system-title'] ?></h1>
	<?php endif; ?>

	<?php do_action('wpsq-template-after-title'); ?>

	<div id="wpsq-quizzes-ui">

		<?php do_action('wpsq-template-before-types'); ?>

		<?php 

			// list types of questions (use 'wpsq-ui-types-args' to filter the arguments for this list)
			$args = apply_filters( 'wpsq-ui-types-args', array(
				'hide_empty' => 1,
				'hierarchical' => 0
			) );

			$types = get_terms( 'wpsq-types', $args );

		 ?>

		<?php if( $types ): //is there at least one type of question? ?>

		<form action="" id="wpsq-types-select">

			<p><?php _e( 'Select which type of questions you want to answer:', $this->plugin_slug ) ?></p>

			<ul id="wpsq-types-list">
			<?php foreach( $types as $type ): ?>
				<li>
					<input type="checkbox" class="wpsq-type-checkbox" name="wpsq-types[]" id="wpsq-course-<?php echo $type->slug ?>" value="<?php echo $type->term_id ?>">
					<label for="wpsq-course-<?php echo $type->slug ?>">
						<?php echo $type->name ?> <span class="wpsq-type-count"><?php echo $type->count ?></span>
					</label>
				</li>
			<?php endforeach; ?>
			</ul>

			<div id="wpsq-questions-number-container">
				<label for="wpsq-questions-number"><?php _e( 'Number of questions', $this->plugin_slug ) ?></label>
				<input type="text" id="wpsq-questions-number" name="wpsq-questions-number" data-max-questions="<?php echo $max ?>" data-min-questions="<?php echo $min ?>" value="<?php echo $min ?>">
			</div>

			<button type="button" id="wpsq-start-quiz" class="wpsq-button"><?php _e( 'Start quiz', $this->plugin_slug ) ?></button>

		</form>
			<div id="wpsq-quiz-questions">
				<div id="wpsq-loading">
					<div class="spinner">
					  <div class="bounce1"></div>
					  <div class="bounce2"></div>
					  <div class="bounce3"></div>
					</div>
					<p><?php _e( 'Loading questions...', $this->plugin_slug ) ?></p>
				</div>				
			</div>

		<div id="wpsq-quiz-controls">
			<button id="wpsq-quiz-prev" class="wpsq-button"><?php _e( 'Previous', $this->plugin_slug ) ?></button>
			<button id="wpsq-quiz-next" class="wpsq-button"><?php _e( 'Next', $this->plugin_slug ) ?></button>
			<button id="wpsq-quiz-finish" class="wpsq-button"><?php _e( 'Finish Quiz', $this->plugin_slug ) ?></button>
		</div>

		<?php else: ?>
			<p class="wpsq-info"><?php _e( 'Sorry, there are no questions assigned in any type.', $this->plugin_slug ) ?></p>
		<?php endif; ?>

		<?php do_action('wpsq-template-after-types'); ?>

	</div>
</div>

<?php do_action('wpsq-template-after-quizzes'); ?>