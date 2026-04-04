<?php
if (!defined('ABSPATH')) exit;

class KAEM_Activator
{
  public static function activate()
  {
    flush_rewrite_rules();
  }
}
