<?php
if (!defined('ABSPATH')) exit;

class KAEM_Settings_Page
{
  public function init()
  {
    add_action('admin_init', [$this, 'register_settings']);
  }

  // register_settings
  public function register_settings()
  {
    register_setting('kaem_settings_group', 'kaem_enable_ai');
    register_setting('kaem_settings_group', 'kaem_enable_booking');
    register_setting('kaem_settings_group', 'kaem_pro_mode');
    register_setting('kaem_settings_group', 'kaem_api_token');
  }
}
