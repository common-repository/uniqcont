<?php

spl_autoload_register(function ($class) {
  $prefix = 'UCWP\\';

  $len = strlen($prefix);
  if (strncmp($prefix, $class, $len) !== 0) {
    return;
  }

  $base_dir = UCWP_PLUGIN_PATH . '/includes/';

  $relative_class = substr($class, $len);
  $relative_class = str_replace('\\', '/', $relative_class);
  $relative_class = explode('/', $relative_class);

  if (is_array($relative_class)) {
    $class_name_index = count($relative_class) - 1;
    $relative_clas_name = $relative_class[$class_name_index];
    $relative_clas_name = strtolower($relative_clas_name);
    $relative_clas_name = str_replace('_', '-', $relative_clas_name);
    $relative_class[$class_name_index] = 'class-' . $relative_clas_name;
    $relative_class = join('/', $relative_class);
  }

  $class_file_path = $base_dir . $relative_class . '.php';

  if (file_exists($class_file_path)) {
    include $class_file_path;
  }
});
