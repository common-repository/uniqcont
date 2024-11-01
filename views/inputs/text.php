<input class="regular-text" type="text" id="<?php echo esc_attr(
  $field_name
); ?>" name="<?php echo esc_attr($group_name); ?>[<?php echo esc_attr(
  $field_name
); ?>]" value="<?php echo esc_attr($field_value); ?>" />
<?php if (
  $description
): ?><br /><span class="description"><?php echo wp_kses_post(
  $description
); ?></span><?php endif;
?>
