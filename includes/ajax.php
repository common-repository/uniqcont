<?php
use UCWP\Plugin;
defined('ABSPATH') || exit();

add_action('wp_ajax_ucwp_check_post_by_id', function () {
  check_ajax_referer('ucwp_check_post');
  if (!current_user_can('manage_options')) {
    wp_die();
  }
  $post_id = intval($_POST['post_id']);
  $from = isset($_POST['from'])
    ? sanitize_text_field($_POST['from'])
    : 'metabox';
  $check = isset($_POST['check']) && $_POST['check'] ? true : false;
  if ('metabox' != $from) {
    $from = 'column';
  }
  $checking = Plugin::component('checking');
  if ($check) {
    $checking->check_post($post_id);
  }

  if ($from === 'column') {
    $column_name = $checking->get_column_name();
    $checking->show_column_content($column_name, $post_id);
  } elseif ($from === 'metabox') {
    $checking->metabox_content($post_id);
  }
  wp_die();
});
