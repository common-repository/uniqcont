<?php
defined('WP_UNINSTALL_PLUGIN') || exit();

global $wpdb;

$wpdb->query("DELETE FROM $wpdb->options WHERE option_name = 'ucwp_settings';");
$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE '_ucwp_%';");
