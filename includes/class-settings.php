<?php
namespace UCWP;
defined('ABSPATH') || exit();

class Settings
{
  protected $settings_group = 'ucwp_settings';

  protected $settings_page_slug = 'uniqcont';

  public function hooks()
  {
    add_action('admin_menu', [$this, 'admin_menu']);
    add_action('admin_init', [$this, 'register']);

    add_filter("plugin_action_links_uniqcont/{$this->settings_page_slug}.php", [
      $this,
      'plugin_action_links',
    ]);
    add_filter(
      "pre_update_option_{$this->settings_group}",
      function ($value, $old_value) {
        if (isset($value['api_key'])) {
          $pos = strpos($value['api_key'], '***');
          if ($pos !== false) {
            $value['api_key'] = isset($old_value['api_key'])
              ? $old_value['api_key']
              : $value['api_key'];
          }
        }
        return $value;
      },
      10,
      2
    );
  }

  public function get_settings_group()
  {
    return $this->settings_group;
  }

  public function get_settings_page_slug()
  {
    return $this->settings_page_slug;
  }

  public function plugin_action_links($actions)
  {
    $settings_page_url = admin_url(
      'options-general.php?page=' . esc_attr($this->settings_page_slug)
    );
    $link_html =
      '<a href="' .
      $settings_page_url .
      '">' .
      esc_html__('Settings', 'uniqcont') .
      '</a>';
    return array_merge(
      [
        'settings' => $link_html,
      ],
      $actions
    );
  }

  public function admin_menu()
  {
    $page = add_submenu_page(
      'options-general.php',
      __('UniqCont', 'uniqcont'),
      __('UniqCont', 'uniqcont'),
      'manage_options',
      $this->get_settings_page_slug(),
      [$this, 'page']
    );
    wp_register_script(
      'ucwp-admin-js',
      UCWP_PLUGIN_URL . 'assets/js/uniqcont.js',
      ['jquery'],
      UCWP_PLUGIN_VERSION
    );
    wp_register_style(
      'ucwp-admin-css',
      UCWP_PLUGIN_URL . 'assets/css/uniqcont.css',
      [],
      UCWP_PLUGIN_VERSION
    );

    add_action('admin_print_scripts', function () {
      $screen = get_current_screen();
      $post_types = $this->get_option('post_types', []);
      $post_types = array_keys($post_types);
      if (is_object($screen) && in_array($screen->post_type, $post_types)) {
        wp_enqueue_script('ucwp-admin-js');
        wp_enqueue_style('ucwp-admin-css');
      }
    });
  }

  public function page()
  {
    $is_api_key_exists = false;
    if ($this->get_option('api_key', '')) {
      $is_api_key_exists = true;
    }
    Views::render('settings', [
      'is_api_key_exists' => $is_api_key_exists,
    ]);
  }

  public function register()
  {
    register_setting($this->settings_group, $this->settings_group);

    add_settings_section(
      'ucwp_section',
      esc_html__('Settings', 'uniqcont'),
      '',
      $this->settings_page_slug
    );

    add_settings_field(
      'api_key',
      __('API Key', 'uniqcont'),
      [$this, 'display_input_field'],
      $this->settings_page_slug,
      'ucwp_section',
      [
        'type' => 'text',
        'id' => 'api_key',
        'required' => false,
        'desc' =>
          esc_html__('copy it from your', 'uniqcont') .
          ' <a href="https://uniqcont.com" target="_blank">uniqcont.com</a> ' .
          esc_html__('account', 'uniqcont'),
        'label_for' => 'api_key',
      ]
    );

    add_settings_field(
      'auto_check',
      esc_html__('Auto checking:', 'uniqcont'),
      [$this, 'display_input_field'],
      $this->settings_page_slug,
      'ucwp_section',
      [
        'type' => 'radio',
        'id' => 'auto_check',
        'vals' => [
          'on' => esc_html__('Check posts when adding and editing', 'uniqcont'),
          'off' => esc_html__('Do not perform auto checking', 'uniqcont'),
        ],
      ]
    );

    $post_types = get_post_types(
      [
        'public' => true,
        '_builtin' => true,
      ],
      'objects'
    );
    $supported_post_types = [];
    if (is_array($post_types) && $post_types) {
      foreach ($post_types as $post_type_obj) {
        if ($post_type_obj->name === 'attachment') {
          continue;
        }
        $supported_post_types[$post_type_obj->name] = $post_type_obj->label;
      }
    }

    add_settings_field(
      'post_types',
      esc_html__('Post types to check', 'uniqcont'),
      [$this, 'display_input_field'],
      $this->settings_page_slug,
      'ucwp_section',
      [
        'type' => 'checkbox',
        'id' => 'post_types',
        'vals' => $supported_post_types,
      ]
    );
  }

  public function get_option($name, $default = '')
  {
    $options = get_option($this->settings_group);

    if (empty($options) || !is_array($options)) {
      return $default;
    }

    return isset($options[$name]) ? $options[$name] : $default;
  }

  public function display_input_field($args)
  {
    $option = $this->get_option($args['id']);
    switch ($args['type']) {
      case 'text':
        $text_value = $option;
        if ($args['id'] === 'api_key') {
          if ($text_value) {
            $text_value = '*********************';
          }
        }
        Views::render('inputs/text', [
          'field_name' => $args['id'],
          'field_value' => $text_value,
          'group_name' => $this->settings_group,
          'description' => $args['desc'],
        ]);
        break;
      case 'checkbox':
        Views::render('inputs/checkbox', [
          'field_name' => $args['id'],
          'values' => $args['vals'],
          'option' => $option,
          'group_name' => $this->settings_group,
        ]);
        break;
      case 'radio':
        Views::render('inputs/radio', [
          'field_name' => $args['id'],
          'values' => $args['vals'],
          'option' => $option,
          'group_name' => $this->settings_group,
        ]);
        break;
    }
  }
}
