<?php
namespace UCWP;
defined('ABSPATH') || exit();

class Checking
{
  private $column_name = 'ucwp_column';
  private $column_nonce = '';

  public function hooks()
  {
    $settings = Plugin::component('settings');
    if ($settings->get_option('auto_check', 'off') === 'on') {
      add_filter('save_post', [$this, 'save_post'], 10, 3);
      add_action('ucwp_scheduled_check', [$this, 'check_post'], 10, 1);
    }
    $post_types = $settings->get_option('post_types', []);
    $post_types = array_keys($post_types);
    foreach ($post_types as $post_type) {
      add_filter("manage_edit-{$post_type}_columns", [$this, 'add_column']);
      add_filter(
        "manage_{$post_type}_posts_custom_column",
        [$this, 'show_column_content'],
        5,
        2
      );
    }

    add_action('add_meta_boxes', [$this, 'add_metabox']);
  }

  public function save_post($post_ID, $post, $update)
  {
    if (wp_is_post_revision($post_ID) || wp_is_post_autosave($post_ID)) {
      return;
    }
    $post_types = Plugin::component('settings')->get_option('post_types', []);
    if (
      !isset($post_types[$post->post_type]) ||
      $post_types[$post->post_type] !== 'on'
    ) {
      return;
    }
    delete_post_meta($post_ID, '_ucwp_unique_percent');
    delete_post_meta($post_ID, '_ucwp_matches');
    delete_post_meta($post_ID, '_ucwp_error_msg');
    update_post_meta($post_ID, '_ucwp_status', 'processing');
    wp_schedule_single_event(date('U') + 2, 'ucwp_scheduled_check', [$post_ID]);
  }

  public function add_column($columns)
  {
    $columns[$this->column_name] = esc_html__('UniqCont', 'uniqcont');
    return $columns;
  }

  public function get_column_name()
  {
    return $this->column_name;
  }

  public function show_column_content($column_name, $post_id)
  {
    if ($this->column_name != $column_name) {
      return;
    }
    $status = get_post_meta($post_id, '_ucwp_status', true);
    $percent = 0;
    $date = 0;
    $error_msg = '';
    if ($status === 'checked') {
      $percent = get_post_meta($post_id, '_ucwp_unique_percent', true);
      $date = get_post_meta($post_id, '_ucwp_last_check_date', true);
    } elseif ($status === 'error') {
      $error_msg = get_post_meta($post_id, '_ucwp_error_msg', true);
    }
    if (!$this->column_nonce) {
      $this->column_nonce = wp_create_nonce('ucwp_check_post');
    }
    Views::render('column', [
      'post_id' => $post_id,
      'status' => $status,
      'percent' => $percent,
      'date' => $date,
      'error_msg' => $error_msg,
      'nonce' => $this->column_nonce,
    ]);
  }

  public function add_metabox()
  {
    $post_types = Plugin::component('settings')->get_option('post_types', []);
    $screens = array_keys($post_types);
    foreach ($screens as $screen) {
      add_meta_box(
        'uniqcont',
        esc_html__('UniqCont', 'uniqcont'),
        [$this, 'metabox_content'],
        $screen,
        'normal',
        'high'
      );
    }
  }

  public function metabox_content($post_id = 0)
  {
    if (!$post_id) {
      global $post;
    } else {
      $post = get_post($post_id);
    }
    if (!$post || !isset($post->ID)) {
      return false;
    }

    $status = get_post_meta($post->ID, '_ucwp_status', true);
    $percent = 0;
    $matches = false;
    $highlight = [];
    $text = '';
    $error_msg = '';
    if ($status === 'checked') {
      $percent = get_post_meta($post->ID, '_ucwp_unique_percent', true);
      $matches = get_post_meta($post->ID, '_ucwp_matches', true);
      $highlight = get_post_meta($post->ID, '_ucwp_highlight', true);
      $text = get_post_meta($post->ID, '_ucwp_text', true);
      if (!$matches) {
        return '';
      }
      $matches = json_decode($matches, true);
    } elseif ($status === 'error') {
      $error_msg = get_post_meta($post->ID, '_ucwp_error_msg', true);
    }

    Views::render('metabox', [
      'post_id' => $post->ID,
      'status' => $status,
      'percent' => $percent,
      'matches' => $matches,
      'highlight' => $highlight,
      'text' => $text,
      'error_msg' => $error_msg,
      'nonce' => wp_create_nonce('ucwp_check_post'),
    ]);
  }

  public function check_post($post_id, $text = null)
  {
    $post_id = intval($post_id);
    if (is_null($text)) {
      $post = get_post($post_id);
      if (!$post || is_wp_error($post)) {
        return [
          'status' => 'error',
          'percent' => 0,
          'error_msg' => esc_html__('Post not found', 'uniqcont'),
        ];
      }
      $text = $post->post_content;
    }

    $responce = Plugin::component('api')->request([
      'text' => $text,
      'ignore' => get_permalink($post_id),
    ]);
    $matches = json_encode([]);
    $status = 'error';
    $error_msg = false;
    $unique_percent = 0;

    if (!isset($responce['error'])) {
      $error_msg = esc_html__('Uniqueness Check Request Error', 'uniqcont');
    } elseif (!empty($responce['error'])) {
      $error_msg = $responce['error'];
    } else {
      $matches = wp_slash(json_encode($responce['matches']));
      $status = 'checked';
      $unique_percent = $responce['percent'];
      update_post_meta($post_id, '_ucwp_unique_percent', $unique_percent);
      update_post_meta($post_id, '_ucwp_highlight', $responce['highlight']);
      update_post_meta($post_id, '_ucwp_text', $responce['text']);
      delete_post_meta($post_id, '_ucwp_error_msg');
    }

    if ($error_msg) {
      update_post_meta($post_id, '_ucwp_error_msg', $error_msg);
      delete_post_meta($post_id, '_ucwp_text');
    }
    update_post_meta($post_id, '_ucwp_status', $status);
    update_post_meta($post_id, '_ucwp_matches', $matches);
    update_post_meta($post_id, '_ucwp_last_check_date', date('U'));

    return [
      'status' => $status,
      'percent' => esc_html($unique_percent),
      'error_msg' => esc_html($error_msg),
    ];
  }
}
