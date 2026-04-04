<?php
if (!defined('ABSPATH')) exit;

class KAEM_Deactivator
{
  public static function deactivate()
  {
    wp_clear_scheduled_hook('kaem_daily_event_check');

    wp_clear_scheduled_hook('kaem_daily_recurring_check');

    flush_rewrite_rules();
  }
}
