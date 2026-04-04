<?php
if (!defined("ABSPATH")) exit;

class KAEM_Admin_Menu
{
  public function init()
  {
    add_action("admin_menu", array($this, "add_menu"));
    add_action("admin_enqueue_scripts", array($this, "chart_enqueue_script"));
  }

  // chart_enqueue_script
  public function chart_enqueue_script($hook)
  {
    if ($hook !== 'toplevel_page_kaem-dashboard') return;

    wp_enqueue_script(
      'chart-js',
      'https://cdn.jsdelivr.net/npm/chart.js',
      [],
      null,
      true
    );

    wp_enqueue_script(
      'kaem-dashboard',
      KAEM_PLUGIN_URL . 'assets/js/dashboard.js',
      ['chart-js'],
      KAEM_VERSION,
      true
    );
  }

  // add_menu
  public function add_menu()
  {
    // Event Manager
    add_menu_page(
      __("Event Manager", "kaddora-ai-event-manager"),
      __("Event Manager", "kaddora-ai-event-manager"),
      "manage_options",
      'kaem-dashboard',
      array($this, "dashboard_page"),
      'dashicons-calendar',
      6
    );

    // Settings
    add_submenu_page(
      'kaem-dashboard',
      __('Settings', 'kaddora-ai-event-manager'),
      __('Settings', 'kaddora-ai-event-manager'),
      'manage_options',
      'kaem-settings',
      [$this, 'settings_page']
    );

    // Events
    add_submenu_page(
      'kaem-dashboard',
      __('Events', "kaddora-ai-event-manager"),
      __('Events', "kaddora-ai-event-manager"),
      'manage_options',
      'kaem-events',
      [$this, 'event_render']
    );
  }

  // event_render
  public function event_render()
  {
    require_once KAEM_PLUGIN_DIR . "templates/admin/event-page.php";
  }

  // settings_page
  public function settings_page()
  {
    include KAEM_PLUGIN_DIR . "templates/admin/settings.php";
  }

  // dashboard_page
  public function dashboard_page()
  {
    include KAEM_PLUGIN_DIR . 'templates/admin/dashboard.php';
  }
}
