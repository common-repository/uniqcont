<form method="post" action="<?php echo admin_url('options.php'); ?>">
    <?php echo settings_fields('ucwp_settings'); ?>
    <?php echo do_settings_sections('uniqcont'); ?>
	<?php submit_button(); ?>
</form>

