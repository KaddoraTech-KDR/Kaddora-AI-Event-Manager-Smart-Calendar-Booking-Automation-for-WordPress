<?php
if (!defined('ABSPATH')) exit;

class KAEM_License
{
  public static function is_valid()
  {
    $key = get_option('kaem_license_key');

    if (!$key) {
      return false;
    }

    return true;
  }
}
