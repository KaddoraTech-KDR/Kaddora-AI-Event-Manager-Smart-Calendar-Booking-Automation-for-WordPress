<?php
if (!defined('ABSPATH')) exit;

class KAEM_Helpers
{
  public static function is_pro()
  {
    return get_option('kaem_pro_mode') == 1;
  }
}
