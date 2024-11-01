<span class="ucwp-column-content" data-progress-text="<?php _e(
  'Checking in progress',
  'uniqcont'
); ?>">
    <?php if ($status === 'checked') {
      $result_class =
        $percent >= 25
          ? 'ucwp-result-red'
          : ($percent < 15
            ? 'ucwp-result-green'
            : 'ucwp-result-orange');
      echo esc_html__('Uniqueness', 'uniqcont') .
        ': <span class="' .
        $result_class .
        ' ucwp-result-value">' .
        (100 - $percent) .
        '%</span><br/>' .
        esc_html__('last checked', 'uniqcont') .
        ' <abbr title="' .
        date("d.m.Y H:i:s", $date) .
        '">' .
        date("d.m.Y", $date) .
        '</abbr>';
    } elseif ($status === 'processing') {
      echo esc_html__('Checking in progress', 'uniqcont');
    } elseif ($status === 'error') {
      echo esc_html__('Error', 'uniqcont') . ':</br>';
      echo esc_attr($error_msg);
    } else {
      echo esc_html__('Not checked yet', 'uniqcont');
    } ?>
	<br/><a href="#" class="ucwp-check-btn" data-check="1" data-nonce="<?php echo esc_attr(
   $nonce
 ); ?>" data-id="<?php echo intval($post_id); ?>"><?php esc_html_e(
  'Check now',
  'uniqcont'
); ?></a>
</span>