<?php
namespace UCWP;
defined('ABSPATH') || exit();

class Api
{
  protected $api_url = 'https://uniqcont.com/api/check';

  public function request(array $params)
  {
    $params = wp_parse_args($params, [
      'source' => 'wp_plugin',
    ]);

    $http_response = wp_remote_post($this->api_url, [
      'timeout' => 30,
      'body' => $params,
      'headers' => [
        'x-api-key' => Plugin::component('settings')->get_option('api_key', ''),
      ],
    ]);

    if (is_wp_error($http_response)) {
      return false;
    }

    if (isset($http_response['response']) && isset($http_response['body'])) {
      $response = json_decode(trim($http_response['body']), true);
      return $response;
    }

    return false;
  }
}
