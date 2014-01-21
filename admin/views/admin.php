<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   WP_Squizzes
 * @author    Diego de Oliveira <diego@favolla.com.br>
 * @link      https://github.com/diegoliv/WP-squizzes
 * @copyright 2013 Diego de Oliveira
 */
?>

<?php
	global $settings; // we'll need this below
?>

<div class="wrap">

	<h2><span class="dashicons dashicons-editor-help" style="width: 32px; font-size: 32px;"></span> <?php echo esc_html( get_admin_page_title() ); ?></h2>

    <?php $settings->make_tabs(); ?>

	<form action="options.php" method="POST">

        <?php $settings->make_pages(); ?>

	    <?php submit_button(null, 'primary', null, true, array( 'id' => 'submit')); ?>

    </form>

</div>