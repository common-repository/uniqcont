<?php
namespace UCWP;
defined('ABSPATH') || exit();

class Views
{
  public static function render($template_name, $params = [])
  {
    $template_name = str_replace('..', '.', $template_name);
    $template_file = UCWP_PLUGIN_PATH . '/views/' . $template_name . '.php';
    if (!file_exists($template_file)) {
      wp_die('template file not found');
    }
    if ($params && is_array($params)) {
      extract($params);
    }
    include $template_file;
  }
}
