<?php
namespace UCWP;
defined('ABSPATH') || exit();

final class Plugin
{
  private static $_instance = null;

  private $components = null;

  protected function __clone()
  {
    _doing_it_wrong(
      __FUNCTION__,
      __('Something went wrong.', 'uniqcont'),
      '1.0.0'
    );
  }

  protected function __wakeup()
  {
    _doing_it_wrong(
      __FUNCTION__,
      __('Something went wrong.', 'uniqcont'),
      '1.0.0'
    );
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public static function component($component_name)
  {
    $plugin = self::instance();
    $component = $plugin->components()->get($component_name);
    return $component;
  }

  private function __construct()
  {
    $this->include_scripts();
    add_action('plugins_loaded', [$this, 'init']);
  }

  public function init()
  {
    if (!wp_doing_cron() && !current_user_can('manage_options')) {
      return;
    }
    register_deactivation_hook(UCWP_PLUGIN_FILE, function () {
      wp_unschedule_hook('ucwp_scheduled_check');
    });
    $this->register_components();
    Plugin::component('settings')->hooks();
    Plugin::component('checking')->hooks();
  }

  protected function register_components()
  {
    $this->components()->add_components([
      'settings' => [
        'class' => 'UCWP\Settings',
      ],
      'api' => [
        'class' => 'UCWP\Api',
      ],
      'checking' => [
        'class' => 'UCWP\Checking',
      ],
    ]);
  }

  public function components()
  {
    if (is_null($this->components)) {
      $this->components = new Components();
    }
    return $this->components;
  }

  protected function include_scripts()
  {
    if (wp_doing_ajax()) {
      include_once UCWP_PLUGIN_PATH . '/includes/ajax.php';
    }
  }
}
