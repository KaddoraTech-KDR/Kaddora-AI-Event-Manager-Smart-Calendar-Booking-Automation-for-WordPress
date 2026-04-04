<?php

/**
 * Plugin Name: Kaddora AI Event Manager
 * Plugin URI: https://kaddora.com
 * Description: Smart AI Event Manager with Calendar, Automation & Booking for WordPress.
 * Version: 1.0.0
 * Author: KaddoraTech
 * Author URI: https://kaddora.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kaddora-ai-event-manager
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

// ==============================
// CONSTANTS
// ==============================
define('KAEM_VERSION', '1.0.0');
define('KAEM_PRO_VERSION', false);
define('KAEM_DB_VERSION', '1.0');
define('KAEM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KAEM_PLUGIN_URL', plugin_dir_url(__FILE__));

// ==============================
// CORE FILES
// ==============================
require_once KAEM_PLUGIN_DIR . 'includes/class-loader.php';
require_once KAEM_PLUGIN_DIR . 'includes/class-activator.php';
require_once KAEM_PLUGIN_DIR . 'includes/class-deactivator.php';

// ==============================
// MODULE FILES
// ==============================
require_once KAEM_PLUGIN_DIR . 'includes/class-post-types.php';
require_once KAEM_PLUGIN_DIR . 'includes/class-meta-boxes.php';
require_once KAEM_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once KAEM_PLUGIN_DIR . 'includes/events/class-calendar-generator.php';
require_once KAEM_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
require_once KAEM_PLUGIN_DIR . "includes/ai/class-ai-manager.php";
require_once KAEM_PLUGIN_DIR . "includes/automation/class-reminder-scheduler.php";
require_once KAEM_PLUGIN_DIR . "includes/automation/class-automation-manager.php";
require_once KAEM_PLUGIN_DIR . 'includes/admin/class-settings-page.php';
require_once KAEM_PLUGIN_DIR . "includes/class-helpers.php";
require_once KAEM_PLUGIN_DIR . "includes/licensing/class-license.php";
require_once KAEM_PLUGIN_DIR . "includes/events/class-event-filters.php";
require_once KAEM_PLUGIN_DIR . "includes/events/class-event-renderer.php";
require_once KAEM_PLUGIN_DIR . "includes/events/class-event-query.php";
require_once KAEM_PLUGIN_DIR . "includes/events/class-event-schema.php";
require_once KAEM_PLUGIN_DIR . "includes/admin/class-events-page.php";
require_once KAEM_PLUGIN_DIR . "includes/admin/class-organizers-page.php";
require_once KAEM_PLUGIN_DIR . "includes/admin/class-venues-page.php";
require_once KAEM_PLUGIN_DIR . "includes/admin/class-automation-page.php";
require_once KAEM_PLUGIN_DIR . 'includes/integrations/class-elementor.php';
require_once KAEM_PLUGIN_DIR . 'includes/integrations/class-blocks.php';
require_once KAEM_PLUGIN_DIR . 'includes/integrations/class-woocommerce.php';
require_once KAEM_PLUGIN_DIR . "includes/class-rest-api.php";

// ==============================
// ACTIVATE / DEACTIVATE
// ==============================
register_activation_hook(__FILE__, ['KAEM_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['KAEM_Deactivator', 'deactivate']);

// ==============================
// ADMIN ENQUEUE SCRIPTS
// ==============================
add_action('admin_enqueue_scripts', function () {
  wp_enqueue_style(
    'kaem-admin-css',
    KAEM_PLUGIN_URL . 'assets/css/admin.css',
    [],
    KAEM_VERSION
  );
  wp_enqueue_script(
    'kaem-admin-js',
    KAEM_PLUGIN_URL . 'assets/js/admin.js',
    [],
    KAEM_VERSION,
    true
  );
});

// ==============================
// PUBLIC ENQUEUE SCRIPTS
// ==============================
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'kaem-public-css',
    KAEM_PLUGIN_URL . 'assets/css/public.css',
    [],
    KAEM_VERSION
  );
});

// ==============================
// CALENDER ENQUEUE SCRIPTS
// ==============================
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_script(
    'kaem-calendar',
    KAEM_PLUGIN_URL . 'assets/js/calendar.js',
    [],
    KAEM_VERSION,
    true
  );

  wp_localize_script('kaem-calendar', 'kaem_ajax', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('kaem_nonce')
  ]);
});


// ==============================
// RUN PLUGIN
// ==============================
function run_kaem_plugin()
{
  $loader = new KAEM_Loader();

  $post_types = new KAEM_Post_Types();
  $meta_boxes = new KAEM_Meta_Boxes();
  $shortcodes = new KAEM_Shortcodes();
  $calendar = new KAEM_Calendar_Generator();
  $admin_menu = new KAEM_Admin_Menu();
  $ai_manager = new KAEM_AI_Manager();
  $reminder_scheduler = new KAEM_Reminder_Scheduler();
  $automation_manager = new KAEM_Automation_Manager();
  $setting_page = new KAEM_Settings_Page();
  $event_schema = new KAEM_Event_Schema();
  $elementor = new KAEM_Elementor();
  $blocks = new KAEM_Blocks();
  $woo = new KAEM_WooCommerce();
  $rest_api = new KAEM_REST_API();

  $rest_api->init();
  $woo->init();
  $blocks->init();
  $elementor->init();
  $event_schema->init();
  $setting_page->init();
  $automation_manager->init();
  $reminder_scheduler->init();
  $ai_manager->init();
  $admin_menu->init();
  $calendar->init();
  $shortcodes->init();
  $post_types->init($loader);
  $meta_boxes->init($loader);

  $loader->run();
}
run_kaem_plugin();
