<?php
namespace UCWP;
defined('ABSPATH') || exit();

class Components
{
  private $components = [];

  private $_lazy_load = [];

  public function get($component_name)
  {
    if (!isset($this->components[$component_name])) {
      $params = isset($this->_lazy_load[$component_name])
        ? $this->_lazy_load[$component_name]
        : false;
      if (!$params) {
        return null;
      }
      if (!isset($params['class']) || empty($params['class'])) {
        return null;
      }
      if (!class_exists($params['class'])) {
        return null;
      }
      $this->components[$component_name] = new $params['class']();
    }
    return $this->components[$component_name];
  }

  public function add($component_name, $params = [])
  {
    if (is_array($params)) {
      $this->_lazy_load[$component_name] = $params;
      $this->components[$component_name] = null;
    } elseif (is_object($params)) {
      $this->components[$component_name] = $params;
    }
  }

  public function add_components($components_data)
  {
    foreach ($components_data as $key => $params) {
      $this->add($key, $params);
    }
  }
}
