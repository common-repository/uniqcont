<?php
/**
 * Plugin Name: UniqCont
 * Plugin URI: https://www.uniqcont.com/
 * Description: Plagiarism checker for WordPress. Check your post before publishing. Find duplicate content on other websites.
 * Version: 1.1
 * Author: uniqcont
 * Author URI: https://www.uniqcont.com/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
  exit();
}

if (!is_admin() && !(defined('DOING_CRON') && DOING_CRON)) {
  return;
}

if (!defined('UCWP_PLUGIN_VERSION')) {
  define('UCWP_PLUGIN_VERSION', '1.1.0');
}

if (!defined('UCWP_PLUGIN_PATH')) {
  define('UCWP_PLUGIN_PATH', dirname(__FILE__));
}

if (!defined('UCWP_PLUGIN_FILE')) {
  define('UCWP_PLUGIN_FILE', __FILE__);
}

if (!defined('UCWP_PLUGIN_URL')) {
  define('UCWP_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!class_exists('\UCWP\Plugin')) {
  include_once UCWP_PLUGIN_PATH . '/includes/autoload.php';
  \UCWP\Plugin::instance();
}

function ucwp_activate() {
  if (!get_option('ucwp_settings')) {
    add_option('ucwp_settings', array(
      'post_types' => array(
        'post' => 'on',
        'page' => 'on'
        )
    ));
  }
}

register_activation_hook(__FILE__, 'ucwp_activate' );
