<?php
if (!defined('ABSPATH')) exit;

class KAEM_Shortcodes
{
  public function init()
  {
    add_shortcode('kaem_calendar', [$this, 'render_calendar']);
  }

  public function render_calendar()
  {
    ob_start();

    $calendar = new KAEM_Calendar_Generator();
    $data = $calendar->generate();

    include KAEM_PLUGIN_DIR . 'templates/calendar/calendar-month.php';

    return ob_get_clean();
  }
}
